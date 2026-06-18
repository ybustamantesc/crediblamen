<!DOCTYPE html>
<html>
<head><title>Check Movement</title></head>
<body>
<h2>Verificación del Movimiento Guardado</h2>
<?php
// Include CI config
require_once 'system/database/DB.php';
require_once 'system/core/Config.php';

// Connect directly
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Get last movement
$result = $mysqli->query('SELECT id, monto_total, tasa_cambio, descripcion, forma_pago FROM teso_movimientos ORDER BY id DESC LIMIT 1');
$movement = $result->fetch_assoc();

echo '<h3>Último Movimiento Guardado:</h3>';
echo '<pre>';
echo json_encode($movement, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo '</pre>';

// Check fields in table
$fieldsResult = $mysqli->query('DESCRIBE teso_movimientos');
echo '<h3>Campos en teso_movimientos:</h3>';
echo '<ul>';
while ($field = $fieldsResult->fetch_assoc()) {
    echo '<li>' . $field['Field'] . ' (' . $field['Type'] . ')</li>';
}
echo '</ul>';

// Get asiento entries
echo '<h3>Asientos Contables Creados:</h3>';
$journalResult = $mysqli->query("
    SELECT am.id, am.description, al.debit_amount_lio, al.description as line_desc 
    FROM asiento_movimientos am
    LEFT JOIN asiento_lines al ON am.id = al.asiento_movimientos_id
    WHERE am.source_type = 'teso_movimiento' AND am.source_id = " . intval($movement['id']) . "
");

echo '<table border="1">';
echo '<tr><th>ID Asiento</th><th>Debe NIO</th><th>Descripción</th></tr>';
while ($line = $journalResult->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $line['id'] . '</td>';
    echo '<td>' . $line['debit_amount_lio'] . '</td>';
    echo '<td>' . $line['line_desc'] . '</td>';
    echo '</tr>';
}
echo '</table>';

$mysqli->close();
?>
</body>
</html>
