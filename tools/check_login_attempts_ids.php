<?php
// tools/check_login_attempts_ids.php
$configPath = __DIR__ . '/../application/config/database.php';
if (!defined('BASEPATH')) define('BASEPATH', true);
require $configPath;
$conf = $db['default'];
$pdo = new PDO("mysql:host={$conf['hostname']};dbname={$conf['database']};charset=utf8mb4", $conf['username'], $conf['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query('SELECT MAX(id) AS max_id, SUM(id=0) AS zeros FROM `login_attempts`');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "max_id=".($row['max_id'] ?? 'NULL')."\n";
echo "zeros=".($row['zeros'] ?? '0')."\n";
if (($row['zeros'] ?? 0) > 0) {
    echo "Rows with id=0 sample:\n";
    $s2 = $pdo->query('SELECT * FROM `login_attempts` WHERE id=0 LIMIT 10');
    foreach ($s2 as $r) {
        print_r($r);
    }
}
