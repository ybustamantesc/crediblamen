<?php
if (! defined('BASEPATH')) define('BASEPATH', __DIR__ . '/');
require 'application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
if ($mysqli->connect_errno) { echo 'ERROR: ' . $mysqli->connect_error . "\n"; exit(1); }
// determine current max id
$res = $mysqli->query("SELECT MAX(id) as m FROM tb_garantias_verificaciones");
$max = 0;
if ($res) { $r = $res->fetch_assoc(); $max = isset($r['m']) ? intval($r['m']) : 0; }
$next = $max + 1;
$sql = "ALTER TABLE tb_garantias_verificaciones MODIFY id INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=$next";
if ($mysqli->query($sql) === TRUE) {
    echo "OK: ALTER TABLE applied, set AUTO_INCREMENT to $next\n";
} else {
    echo 'ERROR: ' . $mysqli->error . "\n";
}
// show create table after change
$res2 = $mysqli->query("SHOW CREATE TABLE tb_garantias_verificaciones");
if ($res2) { $row = $res2->fetch_assoc(); echo $row['Create Table'] . "\n"; }
$mysqli->close();
