<?php
// Script para agregar columnas a la tabla tb_garantias_verificaciones
// Ejecutar en: http://localhost/crediblamen/check_alter_garantias_verificaciones.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'u987557742_crediblamensis';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die('Conexión fallida: ' . $conn->connect_error);
    }
    
    // SQL para agregar las columnas
    $sql = "ALTER TABLE `tb_garantias_verificaciones` 
            ADD COLUMN `nombre_garantia` VARCHAR(255) DEFAULT NULL AFTER `garantia_id`,
            ADD COLUMN `estado_aprobacion` VARCHAR(50) DEFAULT 'No aprobado' AFTER `nombre_garantia`";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green; font-weight: bold;'>✓ Columnas agregadas correctamente a tb_garantias_verificaciones</p>";
        
        // Verificar que las columnas fueron creadas
        $check_sql = "DESCRIBE tb_garantias_verificaciones";
        $result = $conn->query($check_sql);
        
        echo "<h3>Estructura actual de la tabla:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        // Podría ser que las columnas ya existan
        if (strpos($conn->error, 'Duplicate column') !== false) {
            echo "<p style='color: orange;'>⚠ Las columnas ya existen en la tabla</p>";
            
            // Mostrar la estructura de todas formas
            $check_sql = "DESCRIBE tb_garantias_verificaciones";
            $result = $conn->query($check_sql);
            
            echo "<h3>Estructura actual de la tabla:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
