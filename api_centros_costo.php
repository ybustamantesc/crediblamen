<?php
// API para centros de costo desde la lógica real (tb_centro_costo)
require_once 'check_db.php'; // $db = mysqli
header('Content-Type: application/json');
$result = $db->query('SELECT id, codigo, nombre FROM tb_centro_costo ORDER BY codigo ASC');
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'codigo' => $row['codigo'],
        'nombre' => $row['nombre']
    ];
}
echo json_encode(['status' => true, 'centros' => $data]);
