<?php
include 'd:/xampp/htdocs/crediblamen/application/config/database.php';
$cfg = $db['default'];
$mysqli = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
if ($mysqli->connect_errno) {
    echo 'ERROR: ' . $mysqli->connect_error;
    exit(1);
}
echo 'OK: connected to ' . $cfg['database'] . '@' . $cfg['hostname'] . ':' . $cfg['port'];
$mysqli->close();
?>
