<?php
// Quick check: list accounts with agrupador_estado set
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'u987557742_crediblamensis';
$db_port = getenv('DB_PORT') !== false ? (int)getenv('DB_PORT') : 3306;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($mysqli->connect_error) { echo "DB connect error: " . $mysqli->connect_error . "\n"; exit(1); }
$mysqli->set_charset('utf8mb4');

$sql = "SELECT id, code, type, agrupador_estado, report_bs, report_is FROM tb_account WHERE agrupador_estado IS NOT NULL AND agrupador_estado != '' ORDER BY code LIMIT 100";
$res = $mysqli->query($sql);
if (!$res) { echo "Query error: " . $mysqli->error . "\n"; exit(1); }

echo str_pad('ID',6) . str_pad('CODE',18) . str_pad('TYPE',12) . str_pad('AGRUPADOR',20) . str_pad('REPORT_BS',20) . "REPORT_IS\n";
echo str_repeat('-',90) . "\n";
while ($r = $res->fetch_assoc()) {
    echo str_pad($r['id'],6) . str_pad($r['code'],18) . str_pad($r['type'],12) . str_pad($r['agrupador_estado'],20) . str_pad($r['report_bs'] ?? '',20) . ($r['report_is'] ?? '') . "\n";
}

$res->close();
$mysqli->close();
?>
