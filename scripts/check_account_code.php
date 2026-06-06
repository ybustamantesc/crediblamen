<?php
// Usage: php scripts/check_account_code.php 14010101301
ini_set('display_errors', 1);
error_reporting(E_ALL);
$code = isset($argv[1]) ? $argv[1] : null;
if (!$code) { echo "Usage: php scripts/check_account_code.php <code>\n"; exit(1); }
$db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'u987557742_crediblamensis';
$db_port = getenv('DB_PORT') !== false ? (int)getenv('DB_PORT') : 3306;
$m = new mysqli($db_host,$db_user,$db_pass,$db_name,$db_port);
if ($m->connect_error) { echo "DB error: " . $m->connect_error . "\n"; exit(1); }
$m->set_charset('utf8mb4');
$stmt = $m->prepare('SELECT id,code,type,report_bs,report_is,agrupador_estado,name FROM tb_account WHERE code = ? LIMIT 1');
$stmt->bind_param('s',$code);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    // Simulate controller mapping: if agrupador_estado exists, map into report_bs or report_is
    $tipo = isset($row['type']) ? strtolower($row['type']) : '';
    if (isset($row['agrupador_estado']) && trim($row['agrupador_estado']) !== '') {
        if (in_array($tipo, ['activo','pasivo','patrimonio'])) {
            $row['mapped_report_bs'] = $row['agrupador_estado'];
            $row['mapped_report_is'] = '';
        } else {
            $row['mapped_report_is'] = $row['agrupador_estado'];
            $row['mapped_report_bs'] = '';
        }
    } else {
        $row['mapped_report_bs'] = $row['report_bs'];
        $row['mapped_report_is'] = $row['report_is'];
    }
    print_r($row);
} else {
    echo "Not found\n";
}
$stmt->close(); $m->close();
