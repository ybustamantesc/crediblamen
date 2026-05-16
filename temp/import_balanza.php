<?php
// Import trial balance CSV into tb_journal / tb_journal_entry
// Usage: php import_balanza.php [csv_path] [YYYY-MM-DD]

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'minitas';

$csv_path = __DIR__ . '/ejemplo_balanza_abril_2025.csv';
$date = '2025-04-30';
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

// detect header columns (support both 6-col and 5-col formats)
$cols = array_map(function($h){ return mb_strtolower(trim($h)); }, $header);
$idx = ['code'=>null,'name'=>null,'cargos'=>null,'abonos'=>null,'saldo_actual'=>null,'saldo_anterior'=>null];
foreach ($cols as $i => $c) {
    $c2 = preg_replace('/[^a-z0-9 _]/u','',$c);
    if (strpos($c2,'codigo') !== false) $idx['code'] = $i;
    elseif (strpos($c2,'denomin') !== false) $idx['name'] = $i;
    elseif (strpos($c2,'cargos') !== false || strpos($c2,'debe') !== false) $idx['cargos'] = $i;
    elseif (strpos($c2,'abonos') !== false || strpos($c2,'haber') !== false) $idx['abonos'] = $i;
    elseif (strpos($c2,'saldo actual') !== false || strpos($c2,'saldo_actual') !== false) $idx['saldo_actual'] = $i;
    elseif (strpos($c2,'saldo anterior') !== false) $idx['saldo_anterior'] = $i;
}

while (($r = fgetcsv($fp)) !== false) {
    $lineNo++;
    // require at least code and name
    if ($idx['code'] === null) continue;
    $code = isset($r[$idx['code']]) ? trim($r[$idx['code']]) : '';
    $name = ($idx['name'] !== null && isset($r[$idx['name']])) ? trim($r[$idx['name']]) : '';
    if ($code === '') continue;

    // cargos/abonos may be missing or contain '-' placeholders
    $raw_cargos = ($idx['cargos'] !== null && isset($r[$idx['cargos']])) ? trim($r[$idx['cargos']]) : '';
    $raw_abonos = ($idx['abonos'] !== null && isset($r[$idx['abonos']])) ? trim($r[$idx['abonos']]) : '';
    $raw = '';
    if ($idx['saldo_actual'] !== null && isset($r[$idx['saldo_actual']])) $raw = trim($r[$idx['saldo_actual']]);
    elseif ($idx['saldo_anterior'] !== null && isset($r[$idx['saldo_anterior']])) $raw = trim($r[$idx['saldo_anterior']]);

    // normalize numbers: remove anything except digits, dot, minus
    $numc = preg_replace('/[^0-9\.-]/','',$raw_cargos);
    $cargos = ($numc === '') ? 0.0 : floatval($numc);
    $numa = preg_replace('/[^0-9\.-]/','',$raw_abonos);
    $abonos = ($numa === '') ? 0.0 : floatval($numa);
    $num = preg_replace('/[^0-9\.-]/','',$raw);
    $balance = ($num === '') ? 0.0 : floatval($num);

    if (isset($seen[$code])) {
        fwrite(STDERR, "Duplicate code in CSV at line $lineNo: $code\n");
        fclose($fp);
        exit(5);
    }
    $seen[$code] = true;
    $rows[] = ['code'=>$code,'name'=>$name,'balance'=>$balance,'cargos'=>$cargos,'abonos'=>$abonos,'line'=>$lineNo];
}
fclose($fp);

if (count($rows) === 0) { echo "No rows to import.\n"; exit(0); }

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
    $ins->bind_param('ssiss',$code,$name,$postable,$inferred_type,$inferred_nat);
    if (!$ins->execute()) { fwrite(STDERR,"Failed create account: " . $ins->error . "\n"); exit(6); }
    $newid = $ins->insert_id;
    $ins->close();
    return ['id'=>$newid,'created'=>true,'naturaleza'=>$inferred_nat,'type'=>$inferred_type];
}

function get_or_create_adjust_account($mysqli) {
    $code = '9999'; $name = 'AJUSTE BALANZA';
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

$description = "Balanza import $date";
$chk = $mysqli->prepare('SELECT id FROM tb_journal WHERE description = ? LIMIT 1');
$chk->bind_param('s',$description); $chk->execute(); $chk->bind_result($existing); $exists = $chk->fetch(); $chk->close();
if ($exists) {
    // If a previous import exists with same description, remove it so we can re-import
    echo "Found existing journal id={$existing}, removing it to allow re-import...\n";
    $del = $mysqli->prepare('DELETE FROM tb_journal_entry WHERE journal_id = ?');
    $del->bind_param('i',$existing); $del->execute(); $del->close();
    $delj = $mysqli->prepare('DELETE FROM tb_journal WHERE id = ?');
    $delj->bind_param('i',$existing); $delj->execute(); $delj->close();
}

// determine centro de costo id for code '001'
function get_centro_costo_id($mysqli, $code = '001') {
    $q = $mysqli->prepare('SELECT id FROM tb_centro_costo WHERE codigo = ? LIMIT 1');
    $q->bind_param('s',$code); $q->execute(); $q->bind_result($id); if ($q->fetch()) { $q->close(); return intval($id); } $q->close();
    return 1;
}
$centro_costo_id = get_centro_costo_id($mysqli,'001');

$posted = 1; $posted_at = date('Y-m-d H:i:s');
$insj = $mysqli->prepare('INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`,`centro_costo_id`) VALUES (?,?,?,?,NOW(),?)');
$insj->bind_param('ssisi',$date,$description,$posted,$posted_at,$centro_costo_id);
if (!$insj->execute()) { fwrite(STDERR, "Failed insert journal: " . $insj->error . "\n"); exit(9); }
$journal_id = $insj->insert_id; $insj->close();

$adjust_id = get_or_create_adjust_account($mysqli);

$total_debit = 0.0; $total_credit = 0.0;
foreach ($rows as $r) {
    $code = $r['code']; $name = $r['name']; $bal = $r['balance'];
    // Prefer using Cargos (debe) and Abonos (haber) from CSV when provided
    $debit = isset($r['cargos']) ? floatval($r['cargos']) : 0.0;
    $credit = isset($r['abonos']) ? floatval($r['abonos']) : 0.0;
    // If both cargos and abonos are zero, skip this row entirely (do not use Saldo Actual)
    if (abs($debit) < 0.0001 && abs($credit) < 0.0001) {
        echo "Skipping {$code} (no cargos/abonos)\n";
        continue;
    }

    // Only ensure/create account when we will insert a movement
    $acct = ensure_account($mysqli, $code, $name);
    $aid = intval($acct['id']);
    $naturaleza = isset($acct['naturaleza']) ? $acct['naturaleza'] : 'deudora';
    $total_debit += $debit; $total_credit += $credit;
    $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id) VALUES (?,?,?,?,?,?)');
    $desc = 'Balanza importada';
    $ins->bind_param('iiddsi',$journal_id,$aid,$debit,$credit,$desc,$centro_costo_id);
    if (!$ins->execute()) { fwrite(STDERR, "Failed insert entry for {$code}: " . $ins->error . "\n"); }
    $ins->close();
}

echo "Totals before adjustment: debit={" . number_format($total_debit,2,'.','') . "}, credit={" . number_format($total_credit,2,'.','') . "}\n";

$diff = round($total_debit - $total_credit, 2);
if (abs($diff) > 0.005) {
    if ($diff > 0) {
        $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id) VALUES (?,?,?,?,?,?)');
        $d = 0.0; $c = $diff; $desc = 'Ajuste para balancear balanza';
        $ins->bind_param('iiddsi',$journal_id,$adjust_id,$d,$c,$desc,$centro_costo_id);
        if ($ins->execute()) { echo "Inserted adjustment credit of {$c} to account {$adjust_id}\n"; }
        else { fwrite(STDERR, "Failed insert adjustment: " . $ins->error . "\n"); }
        $ins->close();
    } else {
        $ins = $mysqli->prepare('INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id) VALUES (?,?,?,?,?,?)');
        $d = -$diff; $c = 0.0; $desc = 'Ajuste para balancear balanza';
        $ins->bind_param('iiddsi',$journal_id,$adjust_id,$d,$c,$desc,$centro_costo_id);
        if ($ins->execute()) { echo "Inserted adjustment debit of {" . number_format($d,2,'.','') . "} to account {$adjust_id}\n"; }
        else { fwrite(STDERR, "Failed insert adjustment: " . $ins->error . "\n"); }
        $ins->close();
    }
}

$upd = $mysqli->prepare('UPDATE tb_journal SET total_debit = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = ?), total_credit = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = ?) WHERE id = ?');
$upd->bind_param('iii',$journal_id,$journal_id,$journal_id);
$upd->execute(); $upd->close();

echo "Import completed. Journal id: {$journal_id}\n";
$mysqli->close();

?>