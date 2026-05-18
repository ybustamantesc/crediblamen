<?php
$mysqli = new mysqli('localhost','root','');
$mysqli->select_db('crediblamen.db');
$data = [
    'solicitud_id' => 3,
    'nombre' => 'na',
    'cantidad' => 1,
    'marca' => 'na',
    'modelo' => 'na',
    'n_serie' => 'na',
    'costo' => '3.00',
    'tiempo_vida' => '2'
];
$sql = "INSERT INTO tb_garantias (solicitud_id,nombre,cantidad,marca,modelo,n_serie,costo,tiempo_vida,created_at) VALUES ('".
    $mysqli->real_escape_string($data['solicitud_id'])."','".
    $mysqli->real_escape_string($data['nombre'])."','".
    $mysqli->real_escape_string($data['cantidad'])."','".
    $mysqli->real_escape_string($data['marca'])."','".
    $mysqli->real_escape_string($data['modelo'])."','".
    $mysqli->real_escape_string($data['n_serie'])."','".
    $mysqli->real_escape_string($data['costo'])."','".
    $mysqli->real_escape_string($data['tiempo_vida'])."', NOW())";
if ($mysqli->query($sql)) {
    echo "Insert OK id=".$mysqli->insert_id."\n";
} else {
    echo "Insert failed: " . $mysqli->error . "\n";
}
$mysqli->close();
?>