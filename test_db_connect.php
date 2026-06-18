<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'u987557742_crediblamen10062026';
$port = 3306;
$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    echo "ERROR|" . $mysqli->connect_errno . "|" . $mysqli->connect_error;
    exit(1);
}
echo "OK|Connected to MySQL " . $mysqli->server_info . "\n";
$mysqli->close();
