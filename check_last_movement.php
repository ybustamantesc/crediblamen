<?php
// Load CI
require_once 'application/config/database.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

// Get last movement
$result = $db->query("SELECT id, monto_total, tasa_cambio, descripcion, forma_pago, fecha_registro, contabilizado FROM teso_movimientos ORDER BY id DESC LIMIT 1");
$movement = $result->fetch_assoc();

echo "=== ÚLTIMO MOVIMIENTO ===\n";
echo json_encode($movement, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Check if there's a costos_adicionales field
$fieldsResult = $db->query("DESCRIBE teso_movimientos");
$fields = [];
while ($field = $fieldsResult->fetch_assoc()) {
    $fields[] = $field['Field'];
}

echo "=== CAMPOS EN teso_movimientos ===\n";
echo implode(", ", $fields) . "\n\n";

// Try to get full record if costos_adicionales exists
if (in_array('costos_adicionales', $fields)) {
    $fullResult = $db->query("SELECT * FROM teso_movimientos ORDER BY id DESC LIMIT 1");
    $fullMovement = $fullResult->fetch_assoc();
    echo "=== COSTOS ADICIONALES ===\n";
    echo $fullMovement['costos_adicionales'] . "\n\n";
}

// Check journal entries for this movement
$journalResult = $db->query("
    SELECT am.id, am.date, am.description, al.debit_amount_lio as debe_nio, al.description as line_desc 
    FROM asiento_movimientos am
    JOIN asiento_lines al ON am.id = al.asiento_movimientos_id
    WHERE am.source_type = 'teso_movimiento' AND am.source_id = " . intval($movement['id']) . "
    ORDER BY al.id
");

echo "=== ASIENTOS CONTABLES CREADOS ===\n";
while ($line = $journalResult->fetch_assoc()) {
    echo "ID Asiento: " . $line['id'] . " | Debe NIO: " . $line['debe_nio'] . " | Desc: " . $line['line_desc'] . "\n";
}

$db->close();
?>
