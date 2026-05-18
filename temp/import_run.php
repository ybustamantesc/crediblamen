<?php
set_time_limit(0);
$iniSetOk = @ini_set('mysqli.allow_local_infile', '1');
$baseDir = dirname(__DIR__);
$lockFile = $baseDir . '/application/cache/import_lock';
@file_put_contents($lockFile, date('c'));

$sqlFile = __DIR__ . '/../sql/import_carga_credito.sql';
if (!file_exists($sqlFile)) {
    echo "No existe: {$sqlFile}";
    @unlink($lockFile);
    exit;
}

$errors = [];
$logFile = __DIR__ . '/import_run_output.txt';

// Run import via mysql client to avoid LOCAL INFILE restriction in mysqli
$mysqlExe = 'C:\\xampp\\mysql\\bin\\mysql.exe';
if (!file_exists($mysqlExe)) {
    $errors[] = 'No existe mysql.exe en ' . $mysqlExe;
    $cmdOut = [];
    $cmdCode = 1;
} else {
    $sqlFilePosix = str_replace('\\', '/', $sqlFile);
    $cmd = '"' . $mysqlExe . '" --local-infile=1 -u root conta -e "SOURCE ' . $sqlFilePosix . '"';
    @exec($cmd, $cmdOut, $cmdCode);
    if ($cmdCode !== 0) {
        $errors[] = 'mysql.exe error code: ' . $cmdCode;
    }
}

header('Content-Type: text/plain; charset=utf-8');
$output = "import_ok=" . (count($errors) ? 'NO' : 'SI') . "\n";
if (count($errors)) {
    $output .= "errors=" . implode(' | ', $errors) . "\n";
}
if (!empty($cmdOut)) {
    $output .= implode("\n", $cmdOut) . "\n";
}
@file_put_contents($logFile, "\n" . $output, FILE_APPEND);
echo $output;
@unlink($lockFile);
