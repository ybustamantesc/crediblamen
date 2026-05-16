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

$checkSql = "SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $mysqli->real_escape_string($dbName) . "' AND TABLE_NAME = 'tb_account' AND COLUMN_NAME = 'report_type'";
$r = $mysqli->query($checkSql);
if (!$r) { echo "ERROR: check query failed: " . $mysqli->error . "\n"; exit(3); }
$row = $r->fetch_assoc();
if (intval($row['cnt']) > 0) {
    echo "Column 'report_type' already exists in tb_account.\n";
    exit(0);
}

$alter = "ALTER TABLE `tb_account` ADD COLUMN `report_type` VARCHAR(10) DEFAULT NULL COMMENT 'BS=Balance, IS=Estado de Resultado'";
if ($mysqli->query($alter) === TRUE) {
    echo "Column 'report_type' added successfully.\n";
    exit(0);
} else {
    echo "ERROR: failed to add column: " . $mysqli->error . "\n";
    exit(4);
}
