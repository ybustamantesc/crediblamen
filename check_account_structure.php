<?php
$mysqli = new mysqli('localhost', 'u987557742_cbm', 'Gec4w7P8', 'u987557742_crediblamensis');
if ($mysqli->connect_error) {
        // Try with default XAMPP credentials
        $mysqli = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
        if ($mysqli->connect_error) {
    echo 'Error: ' . $mysqli->connect_error;
    exit;
        }
}
$result = $mysqli->query('DESCRIBE tb_account');
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}
$mysqli->close();
?>