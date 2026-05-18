<?php
$mysqli = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
if ($mysqli->connect_error) die('Connection failed: ' . $mysqli->connect_error);

$result = $mysqli->query('SHOW COLUMNS FROM tb_tasa_cambio');
if ($result) {
    echo "Columnas actuales de tb_tasa_cambio:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . "\n";
    }
} else {
    echo 'Error: ' . $mysqli->error;
}

$mysqli->close();
