<?php
if (! defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/');
}
require 'application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
if ($mysqli->connect_errno) {
    echo 'ERROR: ' . $mysqli->connect_error . "\n";
    exit(1);
}
echo 'OK: connected to ' . $cfg['database'] . '@' . $cfg['hostname'] . ':' . $cfg['port'] . "\n";
$mysqli->close();
