<?php
// Import monthly balances CSV into tb_journal / tb_journal_entry
// Usage: php import_monthly_balances.php 2025 04 [--force]

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'minitas';

if ($argc < 3) {
    fwrite(STDERR, "Usage: php import_monthly_balances.php YEAR MONTH [--force]\n");
    exit(1);
}
$year = intval($argv[1]);
$month = intval($argv[2]);
$force = in_array('--force', $argv);

$period_start = sprintf('%04d-%02d-01', $year, $month);
$period_end = date('Y-m-t', strtotime($period_start));
$csv_path = __DIR__ . '/saldos_mensuales/' . sprintf('%04d-%02d.csv', $year, $month);

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

// Check existing journal for this period
$desc = "Monthly balances import $year-$month";
$stmt = $mysqli->prepare('SELECT id FROM tb_journal WHERE description = ? LIMIT 1');
$stmt->bind_param('s', $desc);
$stmt->execute();
$stmt->bind_result($existing_journal_id);
$has_existing = $stmt->fetch();
$stmt->close();

if ($has_existing && !$force) {
    echo "Journal already exists (id={$existing_journal_id}). Use --force to create another.\n";
    exit(0);
}

// Create or reuse adjustment account code
function ensure_account($mysqli, $code, $name) {
    $q = $mysqli->prepare('SELECT id FROM tb_account WHERE code = ? LIMIT 1');
    $q->bind_param('s',$code);
    $q->execute();
    $q->bind_result($id);
    if ($q->fetch()) { $q->close(); return $id; }
    $q->close();
    $ins = $mysqli->prepare('INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,created_at) VALUES (?,?,?,?,?,NOW())');
    $t = 'MISC'; $nat = 'acreedora';
    $postable = 1;
    $ins->bind_param('sssss',$code,$name,$postable,$t,$nat);
    if (!$ins->execute()) { fwrite(STDERR,"Failed create account: " . $ins->error . "\n"); exit(4); }
    $newid = $ins->insert_id;
    $ins->close();
    return $newid;
}

$adjust_code = '9999';
$adjust_id = ensure_account($mysqli, $adjust_code, 'AJUSTE IMPORTACION');

// Create journal
$date = $period_end;
$posted = 1;
$posted_at = date('Y-m-d H:i:s');
$description = "Monthly balances import $year-" . str_pad($month,2,'0',STR_PAD_LEFT);

$insj = $mysqli->prepare('INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`) VALUES (?,?,?,?,NOW())');
$insj->bind_param('ssss',$date,$description,$posted,$posted_at);
if (!$insj->execute()) { fwrite(STDERR, "Failed insert journal: " . $insj->error . "\n"); exit(5); }
$journal_id = $insj->insert_id;
$insj->close();

// Read CSV and build entries
$fp = fopen($csv_path,'r');
if (!$fp) { fwrite(STDERR, "Cannot open CSV $csv_path\n"); exit(6); }
$header = fgetcsv($fp);
// Expecting: code,name,balance
$rows = [];
while (($r = fgetcsv($fp)) !== false) {
    if (count($r) < 3) continue;
    $code = trim($r[0]);
    $name = trim($r[1]);
    $balance = floatval(str_replace(',','',$r[2]));
    if ($code === '') continue;
    $rows[] = ['code'=>$code,'name'=>$name,'balance'=>$balance];
}
fclose($fp);

// Insert entries
foreach ($rows as $r) {
    // find account
    $q = $mysqli->prepare('SELECT id,naturaleza FROM tb_account WHERE code = ? LIMIT 1');
    $q->bind_param('s',$r['code']);
    $q->execute();
    $q->bind_result($aid,$naturaleza);
    if ($q->fetch()) { $q->close(); } else {
        $q->close();
        // create minimal account
        $aid = ensure_account($mysqli, $r['code'], $r['name']);
        $naturaleza = 'deudora';
    }

    $debit = 0.00; $credit = 0.00;
    $bal = $r['balance'];
    if ($naturaleza === 'deudora') {
        if ($bal >= 0) $debit = $bal; else $credit = -$bal;
    } else {
        if ($bal >= 0) $credit = $bal; else $debit = -$bal;
    }
    $sql = sprintf("INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit) VALUES (%d,%d,%.2f,%.2f)", $journal_id, $aid, $debit, $credit);
    if (!$mysqli->query($sql)) { fwrite(STDERR, "Failed insert entry: " . $mysqli->error . "\n"); }
}

// Balance check
$res = $mysqli->query("SELECT COALESCE(SUM(debit),0) AS deb, COALESCE(SUM(credit),0) AS cre FROM tb_journal_entry WHERE journal_id = " . intval($journal_id));
$tot = $res->fetch_assoc();
$deb = floatval($tot['deb']); $cre = floatval($tot['cre']);
echo "Journal {$journal_id} totals: debit={$deb}, credit={$cre}\n";
if (abs($deb - $cre) > 0.01) {
    $diff = $deb - $cre;
    if ($diff > 0) {
        $sql = sprintf("INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit) VALUES (%d,%d,0,%.2f)", $journal_id, $adjust_id, $diff);
    } else {
        $sql = sprintf("INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit) VALUES (%d,%d,%.2f,0)", $journal_id, $adjust_id, -$diff);
    }
    if ($mysqli->query($sql)) {
        echo "Inserted adjustment entry to account {$adjust_id} to balance journal.\n";
    } else {
        fwrite(STDERR, "Failed insert adjustment: " . $mysqli->error . "\n");
    }
}

echo "Import finished for $year-$month, journal id: $journal_id\n";
$mysqli->close();

?>
