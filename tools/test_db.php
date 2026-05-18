<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'crediblamen.db';
$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    echo "CONNECT ERROR: " . $mysqli->connect_error . "\n";
    exit(1);
}
echo "Connected to MySQL server OK\n";
// list databases
$res = $mysqli->query('SHOW DATABASES');
if (!$res) {
    echo "SHOW DATABASES FAILED: " . $mysqli->error . "\n";
} else {
    while ($row = $res->fetch_assoc()) {
        echo "DB: " . $row['Database'] . "\n";
    }
}
// try to select database
if (! $mysqli->select_db($db)) {
    echo "Select DB failed: " . $mysqli->error . "\n";
} else {
    echo "Selected DB OK\n";
}
// simple query test
$result = $mysqli->query("SELECT 1 AS test");
if ($result) {
    echo "Query OK: ";
    $r = $result->fetch_assoc();
    print_r($r);
} else {
    echo "Query failed: " . $mysqli->error . "\n";
}
$mysqli->close();
?>