<?php
// Verificar base de datos directamente
$conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión falló: ' . $conn->connect_error]));
}

$tables = [];
$result = $conn->query("SHOW TABLES LIKE 'tb_%'");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Verificar estructura de tb_account
$account_structure = $conn->query("DESCRIBE tb_account");
$account_fields = [];
if ($account_structure) {
    while ($row = $account_structure->fetch_assoc()) {
        $account_fields[] = $row['Field'];
    }
}

echo json_encode([
    'status' => 'success',
    'database' => 'u987557742_testsystem',
    'tables' => $tables,
    'tb_account_fields' => $account_fields
], JSON_PRETTY_PRINT);

$conn->close();
