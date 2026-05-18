<?php
// API para proyectos (dummy, reemplaza con tu lógica real)
header('Content-Type: application/json');
$data = [
    ['id' => 1, 'nombre' => 'Proyecto A'],
    ['id' => 2, 'nombre' => 'Proyecto B'],
    ['id' => 3, 'nombre' => 'Proyecto C'],
];
echo json_encode(['status' => true, 'proyectos' => $data]);
