<?php
$csv = 'C:/xampp/htdocs/lasminitas/temp/ejemplo_balanza_junio_2025.csv';
if (!file_exists($csv)) { echo "MISSING\n"; exit(1); }
$h = fopen($csv,'r');
$n = 0;
while (($r = fgetcsv($h)) !== false) {
    $n++;
    echo "LINE $n: count=".count($r)." => ";
    echo json_encode($r, JSON_UNESCAPED_UNICODE);
    echo "\n";
}
fclose($h);
?>