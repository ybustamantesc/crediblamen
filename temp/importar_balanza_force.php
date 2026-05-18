<?php
// Force-import balanza CSV: read CSV with columns
// Codigo,Denominacion,Cargos,Abonos,Saldo Actual
// Creates one adjusting journal to force each account's balance to Saldo Actual
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    if (php_sapi_name() === 'cli') {
        throw new Exception('Run via HTTP upload using the importer form.');
    }
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'u987557742_testsystem';

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) throw new Exception('DB connection error: ' . $conn->connect_error);

    if (!isset($_FILES['balanzaFile'])) throw new Exception('No file uploaded (balanzaFile)');
    $f = $_FILES['balanzaFile'];
    if ($f['error'] !== 0) throw new Exception('Upload error code: ' . $f['error']);

    $mes = $_POST['periodoMes'] ?? null;
    $anio = $_POST['periodoAnio'] ?? null;
    $offset_code = trim($_POST['offset_account_code'] ?? '');
    $auto_post = isset($_POST['auto_post']) ? boolval($_POST['auto_post']) : true;
    if (!$mes || !$anio) throw new Exception('Missing period (periodoMes, periodoAnio)');
    if (!$offset_code) throw new Exception('Offset account code required (offset_account_code)');

    $fecha = date('Y-m-t', strtotime("$anio-$mes-01"));

    // helper parse number robustly
    $parse_number = function($s) {
        $s = trim($s);
        if ($s === '') return 0.0;
        // remove surrounding quotes
        $s = trim($s, "\"' ");
        // if contains apostrophe used as thousands separator (e.g. 1'234'567.89) remove it
        $s = str_replace("'", '', $s);
        // If contains both comma and dot, assume dot is decimal and comma thousands -> remove commas
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $s = str_replace(',', '', $s);
        } elseif (strpos($s, ',') !== false && strpos($s, '.') === false) {
            // comma used as decimal separator
            $s = str_replace(',', '.', $s);
        }
        // remove spaces
        $s = str_replace(' ', '', $s);
        // final cleanup remove any leftover non-digit except dot and minus
        $s = preg_replace('/[^0-9\.\-]/', '', $s);
        return floatval($s);
    };

    $h = fopen($f['tmp_name'], 'r');
    if (!$h) throw new Exception('Cannot open uploaded file');
    $hdr = fgetcsv($h);
    // detect columns: accept 5-col format or 6-col older format
    // header expected: Codigo,Denominacion,Cargos,Abonos,Saldo Actual
    $rows = [];
    while (($r = fgetcsv($h)) !== false) {
        if (count($r) < 2) continue;
        // skip empty code
        $cod = trim($r[0]);
        if ($cod === '') continue;
        $name = isset($r[1]) ? trim($r[1]) : '';
        if (count($r) >= 5) {
            // 5-col: 0 code,1 name,2 cargos,3 abonos,4 saldo
            $cargos = $parse_number($r[2] ?? '0');
            $abonos = $parse_number($r[3] ?? '0');
            $saldo  = $parse_number($r[4] ?? '0');
        } else if (count($r) >= 6) {
            // older 6-col format used elsewhere
            $cargos = $parse_number($r[3] ?? '0');
            $abonos = $parse_number($r[4] ?? '0');
            $saldo  = $parse_number($r[5] ?? '0');
        } else {
            // fallback, try to interpret last two as cargos/abonos
            $cargos = $parse_number($r[count($r)-3] ?? '0');
            $abonos = $parse_number($r[count($r)-2] ?? '0');
            $saldo  = $parse_number($r[count($r)-1] ?? '0');
        }
        $rows[] = ['code'=>$cod,'name'=>$name,'cargos'=>$cargos,'abonos'=>$abonos,'saldo'=>$saldo];
    }
    fclose($h);

    if (count($rows) == 0) throw new Exception('No rows parsed from CSV');

    // lookup offset account id
    $stmt = $conn->prepare('SELECT id, code, name, type FROM tb_account WHERE code = ? LIMIT 1');
    $stmt->bind_param('s', $offset_code);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows == 0) throw new Exception('Offset account not found: ' . $offset_code);
    $offset_acc = $res->fetch_assoc();
    $offset_id = intval($offset_acc['id']);

    // prepare adjustments list
    $adjustments = []; // each: account_id, code, name, raw_diff (debit positive -> raw increase)

    foreach ($rows as $rr) {
        // find account
        $code = $rr['code'];
        $stmt = $conn->prepare('SELECT id, code, name, type FROM tb_account WHERE code = ? LIMIT 1');
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $rres = $stmt->get_result();
        if (!$rres || $rres->num_rows == 0) {
            // skip unknown account but include in report
            $adjustments[] = ['code'=>$code,'found'=>false,'note'=>'account not found'];
            continue;
        }
        $acct = $rres->fetch_assoc();
        $aid = intval($acct['id']);
        $atype = strtolower($acct['type']);
        $factor = 1;
        if (in_array($atype, ['pasivo','patrimonio','ingreso'])) $factor = -1;

        // compute current raw up to fecha (inclusive) using posted entries
        $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as raw FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= ?";
        $ps = $conn->prepare($sql);
        $ps->bind_param('is', $aid, $fecha);
        $ps->execute();
        $prs = $ps->get_result();
        $curr_raw = 0.0;
        if ($prs && $prs->num_rows) {
            $curr_raw = floatval($prs->fetch_assoc()['raw']);
        }
        $curr_display = $curr_raw * $factor;
        $desired_display = floatval($rr['saldo']);
        $diff_display = $desired_display - $curr_display;
        // raw diff required
        $raw_diff = $diff_display / ($factor == 0 ? 1 : $factor);
        // treat very small diffs as zero
        if (abs($raw_diff) < 0.005) {
            $adjustments[] = ['code'=>$code,'found'=>true,'account_id'=>$aid,'name'=>$acct['name'],'current_display'=>round($curr_display,2),'desired_display'=>round($desired_display,2),'raw_diff'=>0.0,'note'=>'no adjustment needed'];
            continue;
        }
        $adjustments[] = ['code'=>$code,'found'=>true,'account_id'=>$aid,'name'=>$acct['name'],'current_display'=>round($curr_display,2),'desired_display'=>round($desired_display,2),'raw_diff'=>round($raw_diff,2)];
    }

    // compute totals for debits and credits for adjustments
    $total_debit = 0.0; $total_credit = 0.0;
    foreach ($adjustments as $a) {
        if (!isset($a['found']) || !$a['found']) continue;
        $v = floatval($a['raw_diff']);
        if ($v > 0) $total_debit += $v; else $total_credit += abs($v);
    }
    // difference to balance
    $diff = round($total_debit - $total_credit,2);

    // if diff != 0 we will add balancing line(s) to offset account to make journal balanced
    // create journal header
    $description = 'Import forced balances ' . $mes . '/' . $anio . ' - generated by importar_balanza_force';
    $posted = $auto_post ? 1 : 0;
    $now = date('Y-m-d H:i:s');
    $total_debit = round($total_debit,2);
    $total_credit = round($total_credit,2);

    // if we have no adjustments (all zero) return report
    $has_adj = false;
    foreach ($adjustments as $a) { if (isset($a['raw_diff']) && abs($a['raw_diff']) >= 0.005) { $has_adj = true; break; } }
    if (!$has_adj) {
        echo json_encode(['status'=>'ok','message'=>'No adjustments required','adjustments'=>$adjustments]);
        $conn->close();
        exit;
    }

    // Build journal entries array
    $entries = [];
    foreach ($adjustments as $a) {
        if (!isset($a['found']) || !$a['found']) continue;
        $v = floatval($a['raw_diff']);
        if (abs($v) < 0.005) continue;
        if ($v > 0) {
            // need to debit account
            $entries[] = ['account_id'=>$a['account_id'],'debit'=>round($v,2),'credit'=>0.0,'description'=>'Ajuste import'];
        } else {
            // need to credit account
            $entries[] = ['account_id'=>$a['account_id'],'debit'=>0.0,'credit'=>round(abs($v),2),'description'=>'Ajuste import'];
        }
    }

    // add balancing line to offset account (single line): if total_debit > total_credit, credit offset by diff, else debit offset
    if ($total_debit > $total_credit) {
        $bal_amount = round($total_debit - $total_credit,2);
        $entries[] = ['account_id'=>$offset_id,'debit'=>0.0,'credit'=>$bal_amount,'description'=>'Ajuste import (compensacion)'];
        $total_credit += $bal_amount;
    } elseif ($total_credit > $total_debit) {
        $bal_amount = round($total_credit - $total_debit,2);
        $entries[] = ['account_id'=>$offset_id,'debit'=>$bal_amount,'credit'=>0.0,'description'=>'Ajuste import (compensacion)'];
        $total_debit += $bal_amount;
    }

    // final totals
    $total_debit = round($total_debit,2);
    $total_credit = round($total_credit,2);
    if (abs($total_debit - $total_credit) > 0.01) {
        throw new Exception('Cannot build balanced journal (debits != credits): ' . $total_debit . ' vs ' . $total_credit);
    }

    // Insert journal
    $ins = $conn->prepare('INSERT INTO tb_journal (`date`,`description`,`total_debit`,`total_credit`,`posted`,`created_at`) VALUES (?,?,?,?,?,?)');
    $ins->bind_param('ssddis', $fecha, $description, $total_debit, $total_credit, $posted, $now);
    if (!$ins->execute()) throw new Exception('Failed to insert journal: ' . $conn->error);
    $journal_id = $ins->insert_id;

    // insert entries
    $ent_stmt = $conn->prepare('INSERT INTO tb_journal_entry (`journal_id`,`account_id`,`debit`,`credit`,`description`) VALUES (?,?,?,?,?)');
    foreach ($entries as $e) {
        $d = $e['debit']; $c = $e['credit']; $aid = intval($e['account_id']); $desc = $e['description'];
        $ent_stmt->bind_param('iidds', $journal_id, $aid, $d, $c, $desc);
        if (!$ent_stmt->execute()) throw new Exception('Failed to insert journal entry: ' . $conn->error);
    }

    echo json_encode(['status'=>'ok','message'=>'Adjusting journal created','journal_id'=>$journal_id,'posted'=>$posted,'total_debit'=>$total_debit,'total_credit'=>$total_credit,'entries_count'=>count($entries),'adjustments'=>$adjustments]);
    $conn->close();
    exit;

} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}

?>