<?php
// Export rows from a balanza CSV where Cargos and Abonos are both zero (or empty/'-')
// Usage: php export_omitted_may_2025.php [input_csv] [output_csv]
$input = __DIR__ . '/ejemplo_balanza_mayol_2025.csv';
$output = __DIR__ . '/omitted_ejemplo_balanza_mayol_2025.csv';
if (isset($argv[1]) && trim($argv[1])!=='') $input = $argv[1];
if (isset($argv[2]) && trim($argv[2])!=='') $output = $argv[2];
if (!file_exists($input)) { fwrite(STDERR,"Input not found: $input\n"); exit(2); }
$fp = fopen($input,'r'); if (!$fp) { fwrite(STDERR,"Cannot open input\n"); exit(3); }
$header = fgetcsv($fp);
if ($header === false) { fwrite(STDERR,"Empty CSV\n"); exit(4); }
$out = fopen($output,'w'); if (!$out) { fwrite(STDERR,"Cannot open output\n"); exit(5); }
// write header to output
fputcsv($out, $header);
// detect indices
$cols = array_map(function($h){ return mb_strtolower(trim($h)); }, $header);
$idx = ['cargos'=>null,'abonos'=>null,'code'=>null];
foreach ($cols as $i=>$c) {
    $c2 = preg_replace('/[^a-z0-9 _]/u','',$c);
    if (strpos($c2,'cargos')!==false || strpos($c2,'debe')!==false) $idx['cargos']=$i;
    if (strpos($c2,'abonos')!==false || strpos($c2,'haber')!==false) $idx['abonos']=$i;
    if (strpos($c2,'codigo')!==false) $idx['code']=$i;
}
$line=1; $skipped=0;
while (($r = fgetcsv($fp)) !== false) {
    $line++;
    $raw_c = ($idx['cargos']!==null && isset($r[$idx['cargos']])) ? trim($r[$idx['cargos']]) : '';
    $raw_a = ($idx['abonos']!==null && isset($r[$idx['abonos']])) ? trim($r[$idx['abonos']]) : '';
    // normalize placeholders like '-' or empty
    $norm = function($s){ $s = trim($s); if ($s === '' || $s === '-' || $s === '\\u2212') return '0'; return preg_replace('/[^0-9\.-]/','',$s); };
    $nc = $norm($raw_c); $na = $norm($raw_a);
    $vc = ($nc==='') ? 0.0 : floatval($nc);
    $va = ($na==='') ? 0.0 : floatval($na);
    if (abs($vc) < 0.0001 && abs($va) < 0.0001) { fputcsv($out, $r); $skipped++; }
}
fclose($fp); fclose($out);
echo "Exported $skipped omitted rows to $output\n";
?>