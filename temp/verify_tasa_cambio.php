<?php
// Script para verificar y poblar datos de ejemplo en tb_tasa_cambio

$mysqli = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
if ($mysqli->connect_error) die('Error de conexión: ' . $mysqli->connect_error);

echo "=== VERIFICACIÓN DE TABLA tb_tasa_cambio ===\n\n";

// Mostrar estructura
echo "1. Estructura de la tabla:\n";
$result = $mysqli->query('SHOW COLUMNS FROM tb_tasa_cambio');
while ($row = $result->fetch_assoc()) {
    echo "   - {$row['Field']} ({$row['Type']})\n";
}

// Contar registros
echo "\n2. Registros actuales:\n";
$result = $mysqli->query('SELECT COUNT(*) as total FROM tb_tasa_cambio');
$total = $result->fetch_assoc()['total'];
echo "   Total: $total registros\n";

// Si no hay registros, insertar datos de ejemplo
if ($total == 0) {
    echo "\n3. Insertando datos de ejemplo...\n";
    $ejemplos = [
        ['2026-01-01', 36.9000, 37.2000],
        ['2026-01-02', 36.9200, 37.2200],
        ['2026-01-05', 36.9500, 37.2500],
        ['2026-01-08', 36.9800, 37.2800],
        ['2026-01-09', 37.0000, 37.3000],
    ];
    
    foreach ($ejemplos as $ej) {
        $sql = "INSERT INTO tb_tasa_cambio (fecha, tasa_cambio, tasa_venta, created_at) 
                VALUES ('{$ej[0]}', {$ej[1]}, {$ej[2]}, NOW())";
        if ($mysqli->query($sql)) {
            echo "   ✓ Insertada tasa para {$ej[0]}: Compra C\${$ej[1]} / Venta C\${$ej[2]}\n";
        }
    }
}

// Mostrar tasas actuales
echo "\n4. Tasas de cambio registradas:\n";
$result = $mysqli->query('SELECT * FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 10');
echo "   Fecha       | Compra (C$) | Venta (C$)\n";
echo "   " . str_repeat('-', 45) . "\n";
while ($row = $result->fetch_assoc()) {
    printf("   %s | %10.4f  | %10.4f\n", $row['fecha'], $row['tasa_cambio'], $row['tasa_venta']);
}

echo "\n✓ Sistema de Tasa de Cambio actualizado correctamente\n";
echo "  - Ahora puedes gestionar COMPRA y VENTA en la misma tasa\n";
echo "  - Accede a: http://localhost/Servicredit/tasacambio\n";

$mysqli->close();
