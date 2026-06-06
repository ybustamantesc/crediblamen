<?php
// CLI script: import agrupador_estado from CSV into tb_account
// Usage: php scripts/import_agrupador_estado.php "C:\\path\\to\\Segregacion_Importacion.csv"

ini_set('display_errors', 1);
error_reporting(E_ALL);

$csvPath = isset($argv[1]) ? $argv[1] : null;
if (!$csvPath) {
    echo "Usage: php scripts/import_agrupador_estado.php \"C:\\full\\path\\to\\Segregacion_Importacion.csv\"\n";
    exit(1);
}

if (!file_exists($csvPath)) {
    echo "CSV file not found: {$csvPath}\n";
    exit(1);
}

// Read DB credentials from environment or fallback to application config defaults
$db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'u987557742_crediblamensis';
$db_port = getenv('DB_PORT') !== false ? (int)getenv('DB_PORT') : 3306;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($mysqli->connect_error) {
    echo "DB connection error: " . $mysqli->connect_error . "\n";
    exit(1);
}
$mysqli->set_charset('utf8mb4');

echo "Importing agrupador_estado from CSV: {$csvPath}\n";

$handle = fopen($csvPath, 'r');
if (!$handle) {
    echo "Unable to open CSV file.\n";
    exit(1);
}

$header = fgetcsv($handle);
$updated = 0;
$skipped = 0;
$notfound = 0;

$mysqli->begin_transaction();
try {
    while (($row = fgetcsv($handle)) !== false) {
        if (!isset($row[0])) continue;
        $code = trim($row[0]);
        // agrupador expected in second column; fallback: third column if empty
        $agrupador = isset($row[1]) ? trim($row[1]) : '';
        if ($agrupador === '' && isset($row[2])) $agrupador = trim($row[2]);

        if ($code === '' || $agrupador === '') {
            $skipped++;
            continue;
        }

        // Update tb_account by code
        $stmt = $mysqli->prepare("UPDATE tb_account SET agrupador_estado = ? WHERE code = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        $stmt->bind_param('ss', $agrupador, $code);
        $stmt->execute();
        if ($stmt->errno) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        if ($stmt->affected_rows > 0) {
            $updated++;
        } else {
            $notfound++;
        }
        $stmt->close();
    }

    $mysqli->commit();
    fclose($handle);
    echo "Import completed. Updated: {$updated}, Not found: {$notfound}, Skipped (missing data): {$skipped}\n";
    echo "You can now open http://localhost:8080/crediblamen/contabilidad/catalogo and verify the agrupador is selected.\n";
    exit(0);

} catch (Exception $e) {
    $mysqli->rollback();
    fclose($handle);
    echo "Error during import: " . $e->getMessage() . "\n";
    exit(1);
}

?>
