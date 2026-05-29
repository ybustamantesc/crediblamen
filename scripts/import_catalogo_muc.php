<?php
/**
 * Importador simple para Importador_Catalogo_MUC.xlsx
 * Mapea columna 'Codigo' -> tb_account.code y 'Nombre' -> tb_account.name
 * Uso: php import_catalogo_muc.php --file="scripts/Importador_Catalogo_MUC_copy.xlsx" --dbhost=localhost --dbuser=root --dbpass= --dbname=u987557742_crediblamensis
 */
set_time_limit(0);
ini_set('memory_limit','512M');

$opts = getopt('', ['file:','dbhost:','dbuser:','dbpass:','dbname:']);
$file = isset($opts['file']) ? $opts['file'] : __DIR__ . '/Importador_Catalogo_MUC_copy.xlsx';
$dbhost = isset($opts['dbhost']) ? $opts['dbhost'] : '127.0.0.1';
$dbuser = isset($opts['dbuser']) ? $opts['dbuser'] : 'root';
$dbpass = isset($opts['dbpass']) ? $opts['dbpass'] : '';
$dbname = isset($opts['dbname']) ? $opts['dbname'] : 'u987557742_crediblamensis';

if (!file_exists($file)) {
    fwrite(STDERR, "File not found: $file\n");
    exit(1);
}

// load composer autoload
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "Missing composer dependencies. Run 'composer install' in project root.\n");
    exit(1);
}
require $autoload;

use PhpOffice\PhpSpreadsheet\IOFactory;

// read spreadsheet
try {
    $reader = IOFactory::createReaderForFile($file);
    $spreadsheet = $reader->load($file);
} catch (Exception $e) {
    fwrite(STDERR, "Error reading XLSX: " . $e->getMessage() . "\n");
    exit(1);
}

$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
if (count($rows) < 2) {
    fwrite(STDERR, "No hay datos en el archivo.\n");
    exit(1);
}

// detect headers (first non-empty row)
$headerRow = null;
foreach ($rows as $rIndex => $r) {
    $allEmpty = true;
    foreach ($r as $c) { if (trim((string)$c) !== '') { $allEmpty = false; break; } }
    if (!$allEmpty) { $headerRow = $r; $startIndex = $rIndex + 1; break; }
}
if (!$headerRow) { fwrite(STDERR, "No se encontró fila de encabezado.\n"); exit(1); }

$colIndexByName = [];
foreach ($headerRow as $colLetter => $value) {
    $k = strtolower(trim((string)$value));
    if ($k === '') continue;
    $colIndexByName[$k] = $colLetter;
}

// find best matches for Codigo and Nombre
$find = function($candidates) use ($colIndexByName) {
    foreach ($candidates as $cand) {
        $k = strtolower(trim($cand));
        foreach ($colIndexByName as $name => $letter) {
            if ($name === $k) return $letter;
        }
    }
    // try substring contains
    foreach ($colIndexByName as $name => $letter) {
        foreach ($candidates as $cand) {
            if (strpos($name, strtolower($cand)) !== false) return $letter;
        }
    }
    return null;
};

$codigoCol = $find(['codigo','code','cuenta','cuentamuc','cuentacrediblamen']);
$nombreCol = $find(['nombre','name','descripcion','nombrecuenta','nombredecuentamuc']);

if (!$codigoCol || !$nombreCol) {
    fwrite(STDERR, "No se pudieron detectar columnas 'Codigo' y/o 'Nombre'. Revisa encabezados.\n");
    exit(1);
}

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . "\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$inserted = 0; $updated = 0; $skipped = 0; $processed = 0;

$sql = "INSERT INTO tb_account (`code`,`name`,`created_at`) VALUES (?,?,NOW()) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) { fwrite(STDERR, "Prepare failed: " . $mysqli->error . "\n"); exit(1); }

for ($i = $startIndex; $i <= count($rows); $i++) {
    if (!isset($rows[$i])) continue;
    $r = $rows[$i];
    $code = isset($r[$codigoCol]) ? trim((string)$r[$codigoCol]) : '';
    $name = isset($r[$nombreCol]) ? trim((string)$r[$nombreCol]) : '';
    if ($code === '') { $skipped++; continue; }
    $processed++;
    $code = preg_replace('/\s+/', '', $code);
    if ($name === '') $name = $code;
    $stmt->bind_param('ss', $code, $name);
    if (!$stmt->execute()) { fwrite(STDERR, "DB error on row $i: " . $stmt->error . "\n"); $skipped++; continue; }
    $aff = $stmt->affected_rows;
    if ($aff == 1) $inserted++; elseif ($aff == 2) $updated++;
}

$stmt->close();
$mysqli->close();

echo "Import finished. Processed: $processed, Inserted: $inserted, Updated: $updated, Skipped: $skipped\n";
exit(0);
