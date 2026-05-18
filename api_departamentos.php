<?php
require_once 'check_db.php'; // Asegura conexión $db (mysqli)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    if ($desc === '') {
        echo json_encode(['status'=>false,'msg'=>'Descripción requerida']);
        exit;
    }
    $stmt = $db->prepare('INSERT INTO departamentos (descripcion) VALUES (?)');
    $stmt->bind_param('s', $desc);
    if ($stmt->execute()) {
        echo json_encode(['status'=>true,'departamento'=>['id'=>$stmt->insert_id,'descripcion'=>$desc]]);
    } else {
        echo json_encode(['status'=>false,'msg'=>'Error al guardar']);
    }
    exit;
}

$result = $db->query('SELECT id, descripcion FROM departamentos ORDER BY descripcion ASC');
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = ['id' => $row['id'], 'descripcion' => $row['descripcion']];
}
echo json_encode(['status' => true, 'departamentos' => $data]);
