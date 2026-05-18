<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'minitas';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    echo "ERROR: Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(2);
}

$cols = [
    'report_is' => "ALTER TABLE `tb_account` ADD COLUMN `report_is` VARCHAR(80) DEFAULT NULL COMMENT 'Key for Estado de Resultado mapping'",
    'report_bs' => "ALTER TABLE `tb_account` ADD COLUMN `report_bs` VARCHAR(80) DEFAULT NULL COMMENT 'Key for Estado de Situación Financiera mapping'",
];

foreach ($cols as $col => $sql) {
    $checkSql = "SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $mysqli->real_escape_string($dbName) . "' AND TABLE_NAME = 'tb_account' AND COLUMN_NAME = '" . $mysqli->real_escape_string($col) . "'";
    $r = $mysqli->query($checkSql);
    if (!$r) { echo "ERROR: check query failed for $col: " . $mysqli->error . "\n"; continue; }
    $row = $r->fetch_assoc();
    if (intval($row['cnt']) > 0) {
        echo "Column '$col' already exists in tb_account.\n";
        continue;
    }
    if ($mysqli->query($sql) === TRUE) {
        echo "Column '$col' added successfully.\n";
    } else {
        echo "ERROR: failed to add column '$col': " . $mysqli->error . "\n";
    }
}
