<?php
/**
 * Script de prueba para verificar que el guardado de cobros funciona correctamente
 * Ejecutar desde: http://localhost/Crediblamen/test_guardar_cobro.php
 */

// Cargar configuración de CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

// Incluir configuración de base de datos
require_once(APPPATH . 'config/database.php');

// Conectar a la base de datos
$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

echo "<h2>Test de Guardado de Cobros</h2>";
echo "<hr>";

// 1. Verificar que la tabla teso_movimientos existe
echo "<h3>1. Verificando tabla teso_movimientos</h3>";
$result = $mysqli->query("SHOW TABLES LIKE 'teso_movimientos'");
if ($result->num_rows > 0) {
    echo "✓ Tabla existe<br>";
} else {
    echo "✗ Tabla NO existe<br>";
    exit;
}

// 2. Verificar todas las columnas necesarias
echo "<h3>2. Verificando columnas necesarias</h3>";
$columns_needed = [
    'id', 'tipo_movimiento', 'concepto', 'forma_pago', 'fecha_registro',
    'fecha_aplicacion', 'beneficiario', 'monto_total', 'descripcion',
    'cuenta_id', 'tipo_transferencia', 'estado', 'usuario_id', 'conciliado',
    'moneda', 'tc_aplicada', 'monto_nio', 'monto_usd', 'observaciones', 'idserie'
];

$result = $mysqli->query("DESCRIBE `teso_movimientos`");
$columns_found = [];
while ($row = $result->fetch_assoc()) {
    $columns_found[$row['Field']] = $row['Type'];
}

$missing_columns = [];
foreach ($columns_needed as $col) {
    if (isset($columns_found[$col])) {
        echo "✓ " . htmlspecialchars($col) . " (" . htmlspecialchars($columns_found[$col]) . ")<br>";
    } else {
        echo "✗ " . htmlspecialchars($col) . " - FALTA<br>";
        $missing_columns[] = $col;
    }
}

if (!empty($missing_columns)) {
    echo "<div style='color: red; font-weight: bold;'>ERROR: Faltan columnas: " . implode(', ', $missing_columns) . "</div>";
    exit;
}

// 3. Verificar cuentas disponibles
echo "<h3>3. Cuentas disponibles (teso_cuentas)</h3>";
$result = $mysqli->query("SELECT id, nombre, codigo FROM teso_cuentas LIMIT 5");
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Código</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row['id']) . "</td><td>" . htmlspecialchars($row['nombre']) . "</td><td>" . htmlspecialchars($row['codigo']) . "</td></tr>";
    }
    echo "</table>";
    $first_cuenta_id = $result->fetch_assoc()['id'] ?? null;
} else {
    echo "⚠ No hay cuentas disponibles";
    $first_cuenta_id = 1; // Para prueba
}

// 4. Simular un guardado de cobro
echo "<h3>4. Insertando cobro de prueba</h3>";

$test_data = [
    'cuenta_id' => $first_cuenta_id ?? 1,
    'tipo_transferencia' => 'abono',
    'descripcion' => 'TEST: Cobro por prueba de integridad',
    'beneficiario' => 'Cliente Test Cobro',
    'monto_total' => 150.00,
    'moneda' => 'NIO',
    'fecha_registro' => date('Y-m-d H:i:s'),
    'fecha_aplicacion' => date('Y-m-d'),
    'concepto' => 'COBRO_ADICIONAL',
    'usuario_id' => 1,
    'conciliado' => 0,
    'estado' => 'registrado',
    'tipo_movimiento' => 'efectivo',
    'observaciones' => 'Prueba de inserción'
];

// Construir INSERT
$fields = implode(', ', array_keys($test_data));
$values = implode(', ', array_map(function($v) use ($mysqli) {
    return "'" . $mysqli->real_escape_string($v) . "'";
}, array_values($test_data)));

$insert_sql = "INSERT INTO `teso_movimientos` (" . $fields . ") VALUES (" . $values . ")";

echo "<pre style='background: #f0f0f0; padding: 10px; overflow-x: auto;'>" . htmlspecialchars($insert_sql) . "</pre>";

if ($mysqli->query($insert_sql)) {
    $cobro_id = mysqli_insert_id($mysqli);
    echo "✓ Cobro insertado exitosamente<br>";
    echo "ID del cobro: <strong>" . $cobro_id . "</strong><br>";
    
    // 5. Verificar el registro insertado
    echo "<h3>5. Verificando el registro insertado</h3>";
    $result = $mysqli->query("SELECT * FROM `teso_movimientos` WHERE id = " . $cobro_id);
    if ($row = $result->fetch_assoc()) {
        echo "<table border='1' cellpadding='10' style='font-size: 0.9em; width: 100%;'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        foreach ($row as $key => $value) {
            echo "<tr><td><strong>" . htmlspecialchars($key) . "</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
        echo "<br><div style='color: green; font-weight: bold;'>✓ ¡Todos los datos se guardaron correctamente!</div>";
    } else {
        echo "✗ No se pudo recuperar el registro";
    }
} else {
    echo "✗ Error al insertar: " . $mysqli->error . "<br>";
}

// 6. Contar registros con COBRO_ADICIONAL
echo "<h3>6. Resumen de Cobros Adicionales</h3>";
$result = $mysqli->query("SELECT COUNT(*) as total FROM `teso_movimientos` WHERE concepto = 'COBRO_ADICIONAL'");
$row = $result->fetch_assoc();
echo "Total de cobros adicionales registrados: <strong>" . $row['total'] . "</strong>";

$mysqli->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f9f9f9;
}
h2, h3 {
    color: #333;
}
table {
    border-collapse: collapse;
    margin: 10px 0;
}
td, th {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
th {
    background-color: #4CAF50;
    color: white;
}
tr:nth-child(even) {
    background-color: #f2f2f2;
}
pre {
    background: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
}
.success {
    color: green;
    font-weight: bold;
}
.error {
    color: red;
    font-weight: bold;
}
</style>
