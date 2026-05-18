<?php
/**
 * Script para verificar errores recientes en garantías
 */

$log_file = __DIR__ . '/application/logs/garantias_save_debug.log';

echo "<h2>Últimos Errores de Garantías</h2>";

if (!file_exists($log_file)) {
    echo "<p style='color:orange;'>El archivo de log no existe todavía.</p>";
    exit;
}

// Leer últimas 200 líneas
$lines = file($log_file);
$last_lines = array_slice($lines, -200);

echo "<pre style='background:#f4f4f4; padding:15px; max-height:600px; overflow:auto;'>";
echo htmlspecialchars(implode('', $last_lines));
echo "</pre>";

echo "<hr>";
echo "<h3>Información del Sistema:</h3>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Error Reporting: " . error_reporting() . "</li>";
echo "<li>Display Errors: " . ini_get('display_errors') . "</li>";
echo "<li>Log Errors: " . ini_get('log_errors') . "</li>";
echo "<li>Upload Max Size: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>Post Max Size: " . ini_get('post_max_size') . "</li>";
echo "</ul>";

echo "<p><a href='check_uploads_permissions.php'>Ver permisos de uploads</a></p>";
?>
