<?php
/**
 * Script de prueba para simular el guardado de garantías y capturar errores
 */

// Habilitar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Simular ambiente CodeIgniter básico
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('FCPATH', __DIR__ . '/');

echo "<h2>Prueba de Guardado de Garantías</h2>";

// Verificar que los archivos clave existan
$files_to_check = [
    'application/controllers/Garantias.php',
    'application/models/Garantia_model.php',
    'application/models/TasaCambio_model.php'
];

echo "<h3>Verificación de archivos:</h3><ul>";
foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $color = $exists ? 'green' : 'red';
    echo "<li style='color:$color;'>$file: " . ($exists ? 'OK' : 'NO EXISTE') . "</li>";
}
echo "</ul>";

// Cargar CodeIgniter
echo "<h3>Intentando cargar CodeIgniter...</h3>";
try {
    require_once __DIR__ . '/index.php';
    echo "<p style='color:green;'>✓ CodeIgniter cargado correctamente</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Error al cargar CodeIgniter: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
