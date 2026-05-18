<?php
// compare_journal_vs_csv.php
// Usage: php compare_journal_vs_csv.php [journal_id] [csv_path] [output_csv]
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'minitas';
$journal_id = isset($argv[1]) ? intval($argv[1]) : 16;
$csv_path = isset($argv[2]) ? $argv[2] : __DIR__ . '/ejemplo_balanza_junio_2025.csv';
$out_csv = isset($argv[3]) ? $argv[3] : __DIR__ . '/journal_'.$journal_id.'_mismatches.csv';
if (!file_exists($csv_path)) { fwrite(STDERR, "CSV not found: $csv_path\n"); exit(2); }
$mysqli = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if ($mysqli->connect_errno) { fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . "\n"); exit(3); }
$mysqli->set_charset('utf8');

// load CSV mapping by code -> cargos,abonos,saldo_actual,name
$fp = fopen($csv_path,'r');
$hdr = fgetcsv($fp);
$cols = array_map(function($h){ return mb_strtolower(trim($h)); }, $hdr);
$idx = ['code'=>null,'name'=>null,'cargos'=>null,'abonos'=>null,'saldo_actual'=>null,'saldo_anterior'=>null];
foreach ($cols as $i=>$c) {
    $c2 = preg_replace('/[^a-z0-9 _]/u','',$c);
    if (strpos($c2,'codigo') !== false) $idx['code'] = $i;
    elseif (strpos($c2,'denomin') !== false) $idx['name'] = $i;
    elseif (strpos($c2,'cargos') !== false || strpos($c2,'debe') !== false) $idx['cargos'] = $i;
    elseif (strpos($c2,'abonos') !== false || strpos($c2,'haber') !== false) $idx['abonos'] = $i;
    elseif (strpos($c2,'saldo actual') !== false || strpos($c2,'saldo_actual') !== false) $idx['saldo_actual'] = $i;
    elseif (strpos($c2,'saldo anterior') !== false) $idx['saldo_anterior'] = $i;
}
$csv_map = [];
while (($r = fgetcsv($fp)) !== false) {
    $code = isset($r[$idx['code']]) ? trim($r[$idx['code']]) : '';
    if ($code === '') continue;
    $name = ($idx['name']!==null && isset($r[$idx['name']])) ? trim($r[$idx['name']]) : '';
    $raw_c = ($idx['cargos']!==null && isset($r[$idx['cargos']])) ? trim($r[$idx['cargos']]) : '';
    $raw_a = ($idx['abonos']!==null && isset($r[$idx['abonos']])) ? trim($r[$idx['abonos']]) : '';
    $raw_sa = ($idx['saldo_actual']!==null && isset($r[$idx['saldo_actual']])) ? trim($r[$idx['saldo_actual']]) : '';
    $norm = function($s){ $s = trim($s); if ($s === '' || $s === '-' ) return 0.0; $n = preg_replace('/[^0-9\.-]/','',$s); return ($n==='') ? 0.0 : floatval($n); };
    $cargos = $norm($raw_c); $abonos = $norm($raw_a); $saldo_actual = $norm($raw_sa);
    $csv_map[$code] = ['code'=>$code,'name'=>$name,'cargos'=>$cargos,'abonos'=>$abonos,'saldo_actual'=>$saldo_actual];
}
fclose($fp);

// fetch journal entries for journal_id
$sql = "SELECT e.account_id,a.code,a.name,e.debit,e.credit,e.centro_costo_id FROM tb_journal_entry e JOIN tb_account a ON a.id = e.account_id WHERE e.journal_id = ? ORDER BY a.code";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i',$journal_id);
$stmt->execute();
$stmt->bind_result($account_id,$acct_code,$acct_name,$debit,$credit,$cc_id);
$mismatches = [];
while ($stmt->fetch()) {
    $code = $acct_code;
    $csv = isset($csv_map[$code]) ? $csv_map[$code] : null;
    $debit = floatval($debit); $credit = floatval($credit);
    if ($csv === null) {
        $mismatches[] = ['code'=>$code,'name'=>$acct_name,'debit'=>$debit,'credit'=>$credit,'csv_cargos'=>null,'csv_abonos'=>null,'note'=>'No row in CSV'];
        continue;
    }
    // Compare values allowing tiny rounding diff
    $diff_debit = round($debit - $csv['cargos'],2);
    $diff_credit = round($credit - $csv['abonos'],2);
    if (abs($diff_debit) > 0.005 || abs($diff_credit) > 0.005) {
        $mismatches[] = ['code'=>$code,'name'=>$acct_name,'debit'=>$debit,'credit'=>$credit,'csv_cargos'=>$csv['cargos'],'csv_abonos'=>$csv['abonos'],'note'=>"diff_debit={$diff_debit};diff_credit={$diff_credit}"];
    }
}
$stmt->close();

// Write mismatches CSV
$ofp = fopen($out_csv,'w');
fputcsv($ofp, ['code','name','debit','credit','csv_cargos','csv_abonos','note']);
foreach ($mismatches as $m) fputcsv($ofp, [$m['code'],$m['name'],$m['debit'],$m['credit'],$m['csv_cargos'],$m['csv_abonos'],$m['note']]);
fclose($ofp);

echo "Found " . count($mismatches) . " mismatches. Output: $out_csv\n";
$mysqli->close();
?>