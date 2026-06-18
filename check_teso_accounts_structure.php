<?php
require_once('application/config/database.php');
$db_config = $db['default'];
$mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
$result = $mysqli->query('DESCRIBE teso_accounts');
if (!$result) {
    die('Error: ' . $mysqli->error);
}
echo '<pre>';
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . ' | ' . $row['Default'] . "\n";
}
echo '</pre>';
$mysqli->close();
?>