<?php
if (! defined('BASEPATH')) define('BASEPATH', __DIR__ . '/');
require 'application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
if ($mysqli->connect_errno) { echo 'ERROR: ' . $mysqli->connect_error . "\n"; exit(1); }
$sql = "INSERT INTO tb_garantias_verificaciones (garantia_id, solicitud_id, verificador_usuario, comentario) VALUES (9999, 9999, 'TEST_USER', 'test insert')";
if ($mysqli->query($sql) === TRUE) {
    echo 'OK: inserted id=' . $mysqli->insert_id . "\n";
} else {
    echo 'ERROR: ' . $mysqli->error . "\n";
}
$mysqli->close();
