<?php
/**
 * Script para verificar/crear las columnas necesarias en tb_garantias_verificaciones
 * Acceder a: http://localhost/crediblamen/setup_garantias_verificaciones_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'u987557742_crediblamensis';

echo "<html><head><meta charset='utf-8'><title>Setup BD Garantías Verificaciones</title></head><body>";
echo "<h2>Configuración de Base de Datos - Garantías Verificaciones</h2>";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'><strong>✗ Error de conexión:</strong> " . htmlspecialchars($conn->connect_error) . "</p>";
        die();
    }
    
    echo "<p style='color: green;'><strong>✓ Conectado a la base de datos</strong></p>";
    
    // Verificar la estructura actual de la tabla
    $check_sql = "SHOW COLUMNS FROM tb_garantias_verificaciones";
    $result = $conn->query($check_sql);
    
    if (!$result) {
        echo "<p style='color: red;'><strong>✗ Error al obtener estructura:</strong> " . htmlspecialchars($conn->error) . "</p>";
        die();
    }
    
    $columns = array();
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row;
    }
    
    echo "<h3>Columnas actuales en tb_garantias_verificaciones:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Campo</th><th>Tipo</th><th>Nulo</th><th>Por defecto</th>";
    echo "</tr>";
    
    foreach ($columns as $field => $info) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($field) . "</strong></td>";
        echo "<td>" . htmlspecialchars($info['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($info['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($info['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar si las columnas necesarias existen
    $needs_nombre = !isset($columns['nombre_garantia']);
    $needs_estado = !isset($columns['estado_aprobacion']);
    
    echo "<h3>Estado de las columnas necesarias:</h3>";
    
    if (!$needs_nombre && !$needs_estado) {
        echo "<p style='color: green;'><strong>✓ Todas las columnas están presentes</strong></p>";
    } else {
        echo "<p style='color: orange;'><strong>⚠ Falta agregar columnas</strong></p>";
        
        // Intentar agregar las columnas
        echo "<h3>Intentando agregar columnas faltantes...</h3>";
        
        $alter_queries = array();
        if ($needs_nombre) {
            $alter_queries[] = "ALTER TABLE `tb_garantias_verificaciones` ADD COLUMN `nombre_garantia` VARCHAR(255) DEFAULT NULL AFTER `garantia_id`";
        }
        if ($needs_estado) {
            $alter_queries[] = "ALTER TABLE `tb_garantias_verificaciones` ADD COLUMN `estado_aprobacion` VARCHAR(50) DEFAULT 'No aprobado' AFTER `nombre_garantia`";
        }
        
        foreach ($alter_queries as $query) {
            if ($conn->query($query) === TRUE) {
                echo "<p style='color: green;'><strong>✓</strong> Columna agregada correctamente</p>";
            } else {
                if (strpos($conn->error, 'Duplicate column') !== false) {
                    echo "<p style='color: orange;'><strong>⚠</strong> La columna ya existe (puede ser normal)</p>";
                } else {
                    echo "<p style='color: red;'><strong>✗ Error:</strong> " . htmlspecialchars($conn->error) . "</p>";
                }
            }
        }
        
        // Re-verificar después de agregar
        $result = $conn->query($check_sql);
        $columns = array();
        while ($row = $result->fetch_assoc()) {
            $columns[$row['Field']] = $row;
        }
        
        echo "<h3>Columnas después de la configuración:</h3>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>Campo</th><th>Tipo</th><th>Nulo</th><th>Por defecto</th>";
        echo "</tr>";
        
        foreach ($columns as $field => $info) {
            $highlight = ($field === 'nombre_garantia' || $field === 'estado_aprobacion') ? 'style="background: #fff9e6;"' : '';
            echo "<tr " . $highlight . ">";
            echo "<td><strong>" . htmlspecialchars($field) . "</strong></td>";
            echo "<td>" . htmlspecialchars($info['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($info['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($info['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Mostrar algunos registros existentes
    echo "<h3>Últimos registros en tb_garantias_verificaciones:</h3>";
    $data_sql = "SELECT * FROM tb_garantias_verificaciones ORDER BY id DESC LIMIT 5";
    $data_result = $conn->query($data_sql);
    
    if ($data_result && $data_result->num_rows > 0) {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; font-size: 12px;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Garantía</th><th>Nombre</th><th>Estado</th><th>Comentario</th><th>Verificador</th><th>Fecha</th>";
        echo "</tr>";
        
        while ($row = $data_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['garantia_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre_garantia'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['estado_aprobacion'] ?? '-') . "</td>";
            echo "<td>" . substr(htmlspecialchars($row['comentario']), 0, 30) . "...</td>";
            echo "<td>" . htmlspecialchars($row['verificador_usuario'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: gray;'>No hay registros en la tabla aún.</p>";
    }
    
    echo "<h3>Resumen:</h3>";
    echo "<ul>";
    echo "<li>✓ Base de datos: " . htmlspecialchars($db) . "</li>";
    echo "<li>✓ Tabla: tb_garantias_verificaciones</li>";
    echo "<li>✓ Total de columnas: " . count($columns) . "</li>";
    if (isset($columns['nombre_garantia'])) {
        echo "<li>✓ Columna 'nombre_garantia': Presente</li>";
    }
    if (isset($columns['estado_aprobacion'])) {
        echo "<li>✓ Columna 'estado_aprobacion': Presente</li>";
    }
    echo "</ul>";
    
    echo "<p style='margin-top: 20px; color: #666;'>";
    echo "<em>Los cambios en el código del controlador ya fueron realizados.</em><br>";
    echo "<em>El sistema ahora guardará nombre_garantia y estado_aprobacion para cada verificación.</em>";
    echo "</p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
