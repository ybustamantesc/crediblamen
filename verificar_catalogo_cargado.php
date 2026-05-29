<?php
$mysqli = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
if ($mysqli->connect_error) {
    die('Error: ' . $mysqli->connect_error);
}

echo "=== Verificación de datos cargados ===\n\n";

// Verificar conteos por tipo
$result = $mysqli->query('SELECT type, naturaleza, COUNT(*) as cantidad FROM tb_account GROUP BY type, naturaleza ORDER BY type');
echo "Resumen por tipo y naturaleza:\n";
while ($row = $result->fetch_assoc()) {
    echo "  {$row['type']} - {$row['naturaleza']}: {$row['cantidad']} registros\n";
}

echo "\n--- Ejemplos de registros cargados ---\n\n";

// Mostrar ejemplos de cada tipo
$types = [1 => 'Activo', 2 => 'Pasivo', 3 => 'Patrimonio', 4 => 'Ingreso', 5 => 'Gasto', 8 => 'Orden'];

foreach ([1, 2, 3, 4, 5, 6, 8] as $firstDigit) {
    $result = $mysqli->query("SELECT code, name, type, naturaleza FROM tb_account WHERE code LIKE '$firstDigit%' LIMIT 3");
    $typeLabel = $types[$firstDigit] ?? 'Desconocido';
    echo "Primer dígito $firstDigit ($typeLabel):\n";
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "  {$row['code']} | {$row['name']} | {$row['type']} | {$row['naturaleza']}\n";
        }
    } else {
        echo "  Sin registros\n";
    }
    echo "\n";
}

// Total de registros
$result = $mysqli->query('SELECT COUNT(*) as total FROM tb_account');
$row = $result->fetch_assoc();
echo "Total de registros en tb_account: {$row['total']}\n";

$mysqli->close();
?>
