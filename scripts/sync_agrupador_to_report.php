<?php
// Sync agrupador_estado into report_bs/report_is for catalog consistency
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$sql = "SELECT id, code, type, agrupador_estado FROM tb_account WHERE agrupador_estado IS NOT NULL AND TRIM(agrupador_estado) != ''";
$res = $mysqli->query($sql);
if (!$res) {
    echo "Query error: " . $mysqli->error . "\n";
    exit(1);
}

$updated = 0;
$skipped = 0;
$notFound = 0;

while ($row = $res->fetch_assoc()) {
    $type = strtolower(trim($row['type']));
    $agrupador = trim($row['agrupador_estado']);
    if ($agrupador === '') {
        $skipped++;
        continue;
    }
    if (in_array($type, ['activo','pasivo','patrimonio'])) {
        $stmt = $mysqli->prepare("UPDATE tb_account SET report_bs = ? WHERE id = ?");
    } else {
        $stmt = $mysqli->prepare("UPDATE tb_account SET report_is = ? WHERE id = ?");
    }
    if (!$stmt) {
        echo "Prepare failed for id {$row['id']}: " . $mysqli->error . "\n";
        continue;
    }
    $stmt->bind_param('si', $agrupador, $row['id']);
    $stmt->execute();
    if ($stmt->errno) {
        echo "Execute failed for id {$row['id']}: " . $stmt->error . "\n";
        $stmt->close();
        continue;
    }
    if ($stmt->affected_rows > 0) {
        $updated++;
    } else {
        $notFound++;
    }
    $stmt->close();
}

$res->close();
$mysqli->close();

echo "Sync completed. Updated: {$updated}, Not changed: {$notFound}, Skipped empty: {$skipped}\n";
?>
