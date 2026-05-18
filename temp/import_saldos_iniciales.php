<?php
// Import initial balances CSV into tb_journal / tb_journal_entry
// Usage: php import_saldos_iniciales.php [csv_path] [YYYY-MM-DD]

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'minitas';

$csv_path = __DIR__ . '/saldos_iniciales_importar.csv';
$date = '2025-03-31';
if ($argc >= 2 && trim($argv[1]) !== '') $csv_path = $argv[1];
if ($argc >= 3 && trim($argv[2]) !== '') $date = $argv[2];

if (!file_exists($csv_path)) {
    fwrite(STDERR, "CSV not found: $csv_path\n");
    exit(2);
}

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . "\n");
    exit(3);
}
$mysqli->set_charset('utf8');

// Parse CSV and detect duplicates
$fp = fopen($csv_path,'r');
if (!$fp) { fwrite(STDERR, "Cannot open CSV $csv_path\n"); exit(4); }
$header = fgetcsv($fp);
$rows = [];
$seen = [];
$lineNo = 1;
while (($r = fgetcsv($fp)) !== false) {
    $lineNo++;
    if (count($r) < 3) continue;
    $code = trim($r[0]);
    $name = trim($r[1]);
    $raw = isset($r[2]) ? $r[2] : '';
    // normalize number: remove anything except digits, dot, minus
    $num = preg_replace('/[^0-9\.-]/', '', $raw);
    if ($num === '') $balance = 0.0; else $balance = floatval($num);
    if ($code === '') continue;
    if (isset($seen[$code])) {
        fwrite(STDERR, "Duplicate code in CSV at line $lineNo: $code\n");
        fclose($fp);
        exit(5);
    }
    $seen[$code] = true;
    $rows[] = ['code'=>$code,'name'=>$name,'balance'=>$balance,'line'=>$lineNo];
}
fclose($fp);

if (count($rows) === 0) { echo "No rows to import.\n"; exit(0); }

// Helper: heuristics to determine type and naturaleza based on code
function infer_type_and_naturaleza($code) {
    $c0 = substr(trim($code),0,1);
    $type = 'MISC'; $naturaleza = 'deudora';
    if ($c0 === '1') { $type = 'activo'; $naturaleza = 'deudora'; }
    elseif ($c0 === '2') { $type = 'pasivo'; $naturaleza = 'acreedora'; }
    elseif ($c0 === '3') { $type = 'patrimonio'; $naturaleza = 'acreedora'; }
    elseif ($c0 === '4') { $type = 'ingreso'; $naturaleza = 'acreedora'; }
    elseif (in_array($c0, ['5','6'])) { $type = 'gasto'; $naturaleza = 'deudora'; }
    return [$type, $naturaleza];
}

// ensure account exists; if not create with inferred type/naturaleza
function ensure_account($mysqli, $code, $name) {
    $q = $mysqli->prepare('SELECT id,naturaleza,type FROM tb_account WHERE code = ? LIMIT 1');
    $q->bind_param('s',$code);
    $q->execute();
    $q->bind_result($id,$naturaleza,$type);
    if ($q->fetch()) { $q->close(); return ['id'=>$id,'created'=>false,'naturaleza'=>$naturaleza,'type'=>$type]; }
    $q->close();

    list($inferred_type, $inferred_nat) = infer_type_and_naturaleza($code);
    $ins = $mysqli->prepare('INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,`created_at`) VALUES (?,?,?,?,?,NOW())');
    $postable = 1;
    $ins->bind_param('sssss',$code,$name,$postable,$inferred_type,$inferred_nat);
    if (!$ins->execute()) { fwrite(STDERR,"Failed create account: " . $ins->error . "\n"); exit(6); }
    $newid = $ins->insert_id;
    $ins->close();
    return ['id'=>$newid,'created'=>true,'naturaleza'=>$inferred_nat,'type'=>$inferred_type];
}

// Create adjustment account if missing
function get_or_create_adjust_account($mysqli) {
    $code = '9999'; $name = 'AJUSTE IMPORTACION';
    $q = $mysqli->prepare('SELECT id FROM tb_account WHERE code = ? LIMIT 1');
    $q->bind_param('s',$code); $q->execute(); $q->bind_result($id);
    if ($q->fetch()) { $q->close(); return $id; }
    $q->close();
    $ins = $mysqli->prepare('INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,`created_at`) VALUES (?,?,?,?,?,NOW())');
    $postable = 1; $type = 'MISC'; $nat = 'acreedora';
    $ins->bind_param('ssiss',$code,$name,$postable,$type,$nat);
    if (!$ins->execute()) { fwrite(STDERR,"Failed create adjust account: " . $ins->error . "\n"); exit(7); }
    $newid = $ins->insert_id; $ins->close(); return $newid;
}

// Create journal for opening balances
$description = "Saldos iniciales cierre " . $date;
$chk = $mysqli->prepare('SELECT id FROM tb_journal WHERE description = ? LIMIT 1');
$chk->bind_param('s',$description); $chk->execute(); $chk->bind_result($existing); $exists = $chk->fetch(); $chk->close();
if ($exists) {
    echo "Found existing journal id={$existing}, removing it to allow re-import...\n";
    $del = $mysqli->prepare('DELETE FROM tb_journal_entry WHERE journal_id = ?');
    $del->bind_param('i',$existing); $del->execute(); $del->close();
    $delj = $mysqli->prepare('DELETE FROM tb_journal WHERE id = ?');
    $delj->bind_param('i',$existing); $delj->execute(); $delj->close();
}

$posted = 1; $posted_at = date('Y-m-d H:i:s');
$insj = $mysqli->prepare('INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`) VALUES (?,?,?,?,NOW())');
$insj->bind_param('ssis',$date,$description,$posted,$posted_at);
if (!$insj->execute()) { fwrite(STDERR, "Failed insert journal: " . $insj->error . "\n"); exit(9); }
$journal_id = $insj->insert_id; $insj->close();

$adjust_id = get_or_create_adjust_account($mysqli);

// Insert entries
$total_debit = 0.0; $total_credit = 0.0;
foreach ($rows as $r) {
    $code = $r['code']; $name = $r['name']; $bal = $r['balance'];
    $acct = ensure_account($mysqli, $code, $name);
    $aid = intval($acct['id']);
    $naturaleza = isset($acct['naturaleza']) ? $acct['naturaleza'] : 'deudora';

    $debit = 0.0; $credit = 0.0;
    if ($naturaleza === 'deudora') {
        if ($bal >= 0) $debit = $bal; else $credit = -$bal;
    } else {
        if ($bal >= 0) $credit = $bal; else $debit = -$bal;
    }
    $total_debit += $debit; $total_credit += $credit;
    $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description) VALUES (?,?,?,?,?)');
    $desc = 'Saldo inicial importado';
    $ins->bind_param('iidds',$journal_id,$aid,$debit,$credit,$desc);
    if (!$ins->execute()) { fwrite(STDERR, "Failed insert entry for {$code}: " . $ins->error . "\n"); }
    $ins->close();
}

echo "Totals before adjustment: debit={" . number_format($total_debit,2,'.','') . "}, credit={" . number_format($total_credit,2,'.','') . "}\n";

// Balance journal with adjustment account if needed
$diff = round($total_debit - $total_credit, 2);
if (abs($diff) > 0.005) {
    if ($diff > 0) {
        // insert credit on adjust account
        $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description) VALUES (?,?,?,?,?)');
        $d = 0.0; $c = $diff; $desc = 'Ajuste para balancear importacion';
        $ins->bind_param('iidds',$journal_id,$adjust_id,$d,$c,$desc);
        if ($ins->execute()) { echo "Inserted adjustment credit of {$c} to account {$adjust_id}\n"; }
        else { fwrite(STDERR, "Failed insert adjustment: " . $ins->error . "\n"); }
        $ins->close();
    } else {
        $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description) VALUES (?,?,?,?,?)');
        $d = -$diff; $c = 0.0; $desc = 'Ajuste para balancear importacion';
        $ins->bind_param('iidds',$journal_id,$adjust_id,$d,$c,$desc);
        if ($ins->execute()) { echo "Inserted adjustment debit of {" . number_format($d,2,'.','') . "} to account {$adjust_id}\n"; }
        else { fwrite(STDERR, "Failed insert adjustment: " . $ins->error . "\n"); }
        $ins->close();
    }
}

// Update journal totals
$upd = $mysqli->prepare('UPDATE tb_journal SET total_debit = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = ?), total_credit = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = ?) WHERE id = ?');
$upd->bind_param('iii',$journal_id,$journal_id,$journal_id);
$upd->execute(); $upd->close();

echo "Import completed. Journal id: {$journal_id}\n";
$mysqli->close();

?>
