<?php
/**
 * Script para limpiar todos los asientos contables
 * Deja el sistema en 0 para una nueva instalación
 * 
 * ADVERTENCIA: Este script eliminará TODOS los asientos contables
 * Ejecutar solo si está seguro de que desea eliminar todos los registros
 */

// Cargar configuración de base de datos
$db_config_file = __DIR__ . '/application/config/database.php';
if (file_exists($db_config_file)) {
    include($db_config_file);
    $db = $db['default'];
} else {
    die("No se pudo cargar el archivo de configuración de base de datos");
}

$conn = new mysqli(
    $db['hostname'],
    $db['username'],
    $db['password'],
    $db['database']
);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<h2>🗑️ Limpieza de Asientos Contables</h2>";
echo "<p><strong>ADVERTENCIA:</strong> Este proceso eliminará TODOS los asientos contables del sistema.</p>";

// Verificar datos antes de eliminar
$result = $conn->query("SELECT COUNT(*) as total FROM tb_journal");
$row = $result->fetch_assoc();
$total_asientos = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM tb_journal_entry");
$row = $result->fetch_assoc();
$total_lineas = $row['total'];

echo "<h3>📊 Estado actual:</h3>";
echo "<ul>";
echo "<li>Asientos (tb_journal): <strong>" . $total_asientos . "</strong></li>";
echo "<li>Líneas de asientos (tb_journal_entry): <strong>" . $total_lineas . "</strong></li>";
echo "</ul>";

if ($total_asientos == 0 && $total_lineas == 0) {
    echo "<p style='color: green;'>✅ Las tablas ya están vacías. No hay nada que limpiar.</p>";
    $conn->close();
    exit;
}

echo "<hr>";
echo "<h3>🔄 Procediendo con la limpieza...</h3>";

// Deshabilitar revisión de llaves foráneas
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
echo "<p>✓ Llaves foráneas deshabilitadas</p>";

// Eliminar líneas de asientos
if ($conn->query("TRUNCATE TABLE tb_journal_entry")) {
    echo "<p style='color: green;'>✓ Tabla tb_journal_entry limpiada exitosamente</p>";
} else {
    echo "<p style='color: red;'>✗ Error al limpiar tb_journal_entry: " . $conn->error . "</p>";
}

// Eliminar cabeceras de asientos
if ($conn->query("TRUNCATE TABLE tb_journal")) {
    echo "<p style='color: green;'>✓ Tabla tb_journal limpiada exitosamente</p>";
} else {
    echo "<p style='color: red;'>✗ Error al limpiar tb_journal: " . $conn->error . "</p>";
}

// Reiniciar autoincrementos
$conn->query("ALTER TABLE tb_journal_entry AUTO_INCREMENT = 1");
$conn->query("ALTER TABLE tb_journal AUTO_INCREMENT = 1");
echo "<p>✓ Autoincrementos reiniciados</p>";

// Habilitar revisión de llaves foráneas
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
echo "<p>✓ Llaves foráneas habilitadas nuevamente</p>";

echo "<hr>";
echo "<h3>✅ Verificación final:</h3>";

// Verificar que las tablas estén vacías
$result = $conn->query("SELECT COUNT(*) as total FROM tb_journal");
$row = $result->fetch_assoc();
$total_asientos = $row['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM tb_journal_entry");
$row = $result->fetch_assoc();
$total_lineas = $row['total'];

echo "<ul>";
echo "<li>Asientos (tb_journal): <strong>" . $total_asientos . "</strong></li>";
echo "<li>Líneas de asientos (tb_journal_entry): <strong>" . $total_lineas . "</strong></li>";
echo "</ul>";

if ($total_asientos == 0 && $total_lineas == 0) {
    echo "<h2 style='color: green;'>✅ ¡Limpieza completada exitosamente!</h2>";
    echo "<p>El sistema está listo para una nueva instalación. Todos los asientos contables han sido eliminados.</p>";
} else {
    echo "<h2 style='color: red;'>⚠️ Advertencia</h2>";
    echo "<p>Aún hay registros en las tablas. Por favor, revise manualmente.</p>";
}

$conn->close();

echo "<hr>";
echo "<p><a href='contabilidad/diario' style='display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold;'>← Volver al Libro Diario</a></p>";
?>
