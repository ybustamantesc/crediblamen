<?php
// Simple CSV deduplicator: keeps first occurrence of each code (first column)
// Usage: php dedupe_csv.php --file="uploads/cuentas contables.csv" [--sep=';'] [--out="uploads/cuentas.dedup.csv"]

$opts = getopt('', ['file:', 'sep::', 'out::']);
if (empty($opts['file'])) {
    fwrite(STDERR, "Usage: php dedupe_csv.php --file=FILE [--sep=';'] [--out=OUT]\n");
    exit(2);
}
$file = $opts['file'];
$sep = isset($opts['sep']) ? $opts['sep'] : ';';
$out = isset($opts['out']) ? $opts['out'] : preg_replace('/\.csv$/i', '.dedup.csv', $file);

if (!is_readable($file)) {
    fwrite(STDERR, "File not readable: $file\n");
    exit(3);
}

$in = fopen($file, 'r');
if (!$in) { fwrite(STDERR, "Cannot open file: $file\n"); exit(4); }
$outf = fopen($out, 'w');
if (!$outf) { fwrite(STDERR, "Cannot open output file: $out\n"); exit(5); }

$headers = fgetcsv($in, 0, $sep);
if ($headers === false) { fwrite(STDERR, "Empty file or invalid CSV\n"); exit(6); }
fputcsv($outf, $headers, $sep);

$seen = [];
$line = 1;
while (($row = fgetcsv($in, 0, $sep)) !== false) {
    $line++;
    if (count($row) === 0) continue;
    // use first non-empty column as code
    $code = isset($row[0]) ? trim($row[0]) : '';
    if ($code === '') {
        // no code, write as-is
        fputcsv($outf, $row, $sep);
        continue;
    }
    if (isset($seen[$code])) {
        continue; // skip duplicate code
    }
    $seen[$code] = true;
    fputcsv($outf, $row, $sep);
}

fclose($in);
fclose($outf);

echo "Dedup complete. Input: $file -> Output: $out. Unique rows: " . count($seen) . "\n";

exit(0);
