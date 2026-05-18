<?php
/**
 * Script para verificar y crear directorios de uploads con permisos correctos
 */

$base_dir = __DIR__ . '/uploads';
$garantias_dir = $base_dir . '/garantias';

echo "<h2>Verificación de Permisos de Uploads</h2>";

// Verificar/crear directorio base uploads
if (!is_dir($base_dir)) {
    if (mkdir($base_dir, 0755, true)) {
        echo "<p style='color:green;'>✓ Directorio 'uploads' creado correctamente</p>";
    } else {
        echo "<p style='color:red;'>✗ Error: No se pudo crear el directorio 'uploads'</p>";
    }
} else {
    echo "<p style='color:green;'>✓ Directorio 'uploads' existe</p>";
}

// Verificar/crear directorio garantias
if (!is_dir($garantias_dir)) {
    if (mkdir($garantias_dir, 0755, true)) {
        echo "<p style='color:green;'>✓ Directorio 'uploads/garantias' creado correctamente</p>";
    } else {
        echo "<p style='color:red;'>✗ Error: No se pudo crear el directorio 'uploads/garantias'</p>";
    }
} else {
    echo "<p style='color:green;'>✓ Directorio 'uploads/garantias' existe</p>";
}

// Verificar permisos
echo "<h3>Permisos actuales:</h3>";
echo "<ul>";
echo "<li>uploads: " . substr(sprintf('%o', fileperms($base_dir)), -4) . " - " . (is_writable($base_dir) ? "<span style='color:green;'>Escribible</span>" : "<span style='color:red;'>No escribible</span>") . "</li>";
if (is_dir($garantias_dir)) {
    echo "<li>uploads/garantias: " . substr(sprintf('%o', fileperms($garantias_dir)), -4) . " - " . (is_writable($garantias_dir) ? "<span style='color:green;'>Escribible</span>" : "<span style='color:red;'>No escribible</span>") . "</li>";
}
echo "</ul>";

// Configuración de PHP
echo "<h3>Configuración PHP:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
echo "</ul>";

// Intentar ajustar permisos si es necesario
if (!is_writable($base_dir)) {
    if (@chmod($base_dir, 0755)) {
        echo "<p style='color:green;'>✓ Permisos de 'uploads' ajustados a 0755</p>";
    } else {
        echo "<p style='color:red;'>✗ No se pudieron ajustar los permisos de 'uploads'. Hazlo manualmente.</p>";
    }
}

if (is_dir($garantias_dir) && !is_writable($garantias_dir)) {
    if (@chmod($garantias_dir, 0755)) {
        echo "<p style='color:green;'>✓ Permisos de 'uploads/garantias' ajustados a 0755</p>";
    } else {
        echo "<p style='color:red;'>✗ No se pudieron ajustar los permisos de 'uploads/garantias'. Hazlo manualmente.</p>";
    }
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Si ves errores en rojo, ejecuta estos comandos en PowerShell (como Administrador):</p>";
echo "<pre style='background:#f4f4f4; padding:10px;'>";
echo "cd C:\\xampp\\htdocs\\Servicredit\n";
echo "icacls uploads /grant Users:F /T\n";
echo "</pre>";
?>
