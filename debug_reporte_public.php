<?php
// Public debug endpoint: returns reporte rows without authentication (for local debugging only)
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'minitas';
$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_errno) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'DB connect error: ' . $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8');
$fechaInicio = $_GET['fechaInicio'] ?? '2026-01-01';
$fechaFin = $_GET['fechaFin'] ?? '2026-01-26';
$idcliente = $_GET['idcliente'] ?? '3';

$sql = "SELECT c.id as id,
                        c.monto_credito,
                        c.fecha_credito,
                        c.estado,
                        c.idasesor,
                        c.numero_coutas,
                        c.interes_credito,
                        c.total_interes,
                        c.total_pagar,
                        cl.idcliente,
                        cl.apellidos,
                        cl.nombres,
                        a.nombres as nombre_asesor
                 FROM tb_creditos c
                 JOIN tb_clientes cl ON cl.idcliente = c.idcliente
                 LEFT JOIN tb_asesores a ON a.idasesor = c.idasesor
                 WHERE c.fecha_credito >= '%s'
                   AND c.fecha_credito <= '%s'
                   AND c.idcliente = '%s'

                 UNION ALL

                 SELECT pr.idprestamo as id,
                        pr.monto_credito,
                        pr.fecha_credito,
                        pr.estado,
                        pr.idasesor,
                        pr.numero_coutas,
                        pr.interes_credito,
                        NULL as total_interes,
                        NULL as total_pagar,
                        cl2.idcliente,
                        cl2.apellidos,
                        cl2.nombres,
                        a2.nombres as nombre_asesor
                 FROM tb_prestamos pr
                 JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                 JOIN tb_clientes cl2 ON cl2.idcliente = s.idcliente
                 LEFT JOIN tb_asesores a2 ON a2.idasesor = pr.idasesor
                 WHERE (
                       (pr.fecha_credito >= '%s' AND pr.fecha_credito <= '%s')
                       OR (DATE(pr.created_at) >= '%s' AND DATE(pr.created_at) <= '%s')
                       )
                   AND cl2.idcliente = '%s'
                 ORDER BY fecha_credito DESC";

$query = sprintf($sql,
    $mysqli->real_escape_string($fechaInicio),
    $mysqli->real_escape_string($fechaFin),
    $mysqli->real_escape_string($idcliente),
    $mysqli->real_escape_string($fechaInicio),
    $mysqli->real_escape_string($fechaFin),
    $mysqli->real_escape_string($fechaInicio),
    $mysqli->real_escape_string($fechaFin),
    $mysqli->real_escape_string($idcliente)
);

$res = $mysqli->query($query);
if (!$res) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => $mysqli->error, 'query' => $query]);
    exit;
}
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
header('Content-Type: application/json');
echo json_encode(['error' => false, 'count' => count($rows), 'rows' => $rows], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
