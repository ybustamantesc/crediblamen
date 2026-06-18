<?php
// Cargar configuración de CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

require_once(APPPATH . 'config/database.php');

$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

$mysqli->set_charset("utf8mb4");

// Get table structure
$result = $mysqli->query("DESCRIBE `teso_movimientos`");

echo "<h2>Campos en teso_movimientos</h2>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";

$fields = [];
while ($row = $result->fetch_assoc()) {
    $fields[] = $row['Field'];
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . ($row['Key'] ? $row['Key'] : '-') . "</td>";
    echo "<td>" . ($row['Default'] ? $row['Default'] : '-') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Campos buscados en query:</h3>";
echo "<ul>";
echo "<li>id - " . (in_array('id', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>descripcion - " . (in_array('descripcion', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>beneficiario - " . (in_array('beneficiario', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>nombre_persona - " . (in_array('nombre_persona', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>monto_total - " . (in_array('monto_total', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>moneda - " . (in_array('moneda', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "<li>fecha_registro - " . (in_array('fecha_registro', $fields) ? "✓ EXISTE" : "✗ NO EXISTE") . "</li>";
echo "</ul>";

// Try the query
echo "<h3>Intentando query:</h3>";
$query = "SELECT id, descripcion, beneficiario, nombre_persona, monto_total, moneda, fecha_registro FROM teso_movimientos WHERE concepto='COBRO_ADICIONAL' LIMIT 5";
echo "<pre>" . $query . "</pre>";

$result = $mysqli->query($query);
if ($result === false) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $mysqli->error . "</p>";
} else {
    echo "<p style='color: green;'>Query exitosa, registros encontrados: " . $result->num_rows . "</p>";
    while ($row = $result->fetch_assoc()) {
        echo "<pre>" . json_encode($row, JSON_PRETTY_PRINT) . "</pre>";
    }
}

$mysqli->close();
?>
