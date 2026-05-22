<?php
if (! defined('BASEPATH')) define('BASEPATH', __DIR__ . '/');
require 'application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
if ($mysqli->connect_errno) { echo 'ERROR: ' . $mysqli->connect_error . "\n"; exit(1); }
$res = $mysqli->query("SHOW CREATE TABLE tb_garantias_verificaciones");
if (! $res) { echo 'ERROR: ' . $mysqli->error . "\n"; exit(1); }
$row = $res->fetch_assoc();
echo $row['Create Table'] . "\n";
$mysqli->close();
