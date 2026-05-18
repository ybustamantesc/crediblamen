<?php
$csv = __DIR__ . '/correccion_asientos_cierre2025.csv';
$verif = __DIR__ . '/verificacion_post_fixed_20250123.txt';
$out = __DIR__ . '/comparacion_final_20250123.txt';
if (!file_exists($csv) || !file_exists($verif)) { echo "Missing files\n"; exit(1); }
$map = [];
// read csv
$fh = fopen($csv,'r');
$hdr = fgetcsv($fh);
while (($r = fgetcsv($fh)) !== false) {
    if (count($r) < 4) continue;
    $code = trim($r[0]);
    $desired = trim($r[3]);
    $s = str_replace("'", '', $desired);
    $s = str_replace(' ', '', $s);
    $s = str_replace(',', '.', $s);
    $s = preg_replace('/[^0-9\.\-]/','', $s);
    if ($s === '') $s = '0';
    $map[$code] = (float)$s;
}
fclose($fh);
// read verification
$lines = file($verif, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$rows = [];
foreach ($lines as $line) {
    $parts = preg_split('/\t+/', $line);
    $code = $parts[0];
    $display = isset($parts[count($parts)-1]) ? $parts[count($parts)-1] : null;
    $display = str_replace("\r","",$display);
    $display = trim($display);
    if ($display === 'NULL' || $display === '') $display_f = null; else $display_f = (float)$display;
    $desired = isset($map[$code]) ? $map[$code] : null;
    $diff = null;
    if ($display_f !== null && $desired !== null) { $diff = round($display_f - $desired,2); }
    $rows[] = ['code'=>$code,'desired'=>$desired,'actual'=>$display_f,'diff'=>$diff];
}
// write summary
$fp = fopen($out,'w');
fwrite($fp, "Code,Desired,Actual,Diff\n");
$bad = 0;
foreach ($rows as $r) {
    fwrite($fp, $r['code'] . ',' . ($r['desired']===null?"":number_format($r['desired'],2,'.','')) . ',' . ($r['actual']===null?"":number_format($r['actual'],2,'.','')) . ',' . ($r['diff']===null?"":number_format($r['diff'],2,'.','')) . "\n");
    if ($r['diff'] !== null && abs($r['diff']) > 0.01) $bad++;
}
fwrite($fp, "\nMismatches > 0.01: $bad\n");
fclose($fp);
echo "Comparison written to $out\n";
