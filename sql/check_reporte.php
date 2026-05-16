<?php
// Simple DB check script to run the reporte query outside CodeIgniter
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'minitas';

$fechaInicio = $argv[1] ?? '2026-01-01';
$fechaFin = $argv[2] ?? '2026-01-26';
$idcliente = $argv[3] ?? '3';
$idasesor = $argv[4] ?? '0';

$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_errno) {
    echo json_encode(['error' => true, 'message' => 'Connect error: ' . $mysqli->connect_error]);
    exit(1);
}
$mysqli->set_charset('utf8');

if ($idasesor === '0' || $idasesor === 0) {
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
                 WHERE c.fecha_credito >= ?
                   AND c.fecha_credito <= ?
                   AND c.idcliente = ?

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
                       (pr.fecha_credito >= ? AND pr.fecha_credito <= ?)
                       OR (DATE(pr.created_at) >= ? AND DATE(pr.created_at) <= ?)
                       )
                   AND cl2.idcliente = ?
                 ORDER BY fecha_credito DESC";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => true, 'message' => 'Prepare error: ' . $mysqli->error]);
        exit(1);
    }
    $stmt->bind_param('sssss s s', $fechaInicio, $fechaFin, $idcliente, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $idcliente);
    // bind_param pattern above is incorrect for simplicity; use procedural query replacement instead
    $stmt->close();
    // fallback to simple replacement (safe enough for local debugging)
    // replace placeholders with properly quoted values
    $replacements = [
        "'" . $mysqli->real_escape_string($fechaInicio) . "'",
        "'" . $mysqli->real_escape_string($fechaFin) . "'",
        "'" . $mysqli->real_escape_string($idcliente) . "'",
        "'" . $mysqli->real_escape_string($fechaInicio) . "'",
        "'" . $mysqli->real_escape_string($fechaFin) . "'",
        "'" . $mysqli->real_escape_string($fechaInicio) . "'",
        "'" . $mysqli->real_escape_string($fechaFin) . "'",
        "'" . $mysqli->real_escape_string($idcliente) . "'",
    ];
    $sql2 = $sql;
    foreach ($replacements as $rep) {
        $sql2 = preg_replace('/\?/', $rep, $sql2, 1);
    }
    $res = $mysqli->query($sql2);
    if (!$res) {
        echo json_encode(['error' => true, 'message' => 'Query error: ' . $mysqli->error, 'query' => $sql2]);
        exit(1);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    // additional debug: list solicitudes for client and prestamos linked
    $dbg = [];
    $sres = $mysqli->query("SELECT * FROM tb_solicitudes WHERE idcliente = '" . $mysqli->real_escape_string($idcliente) . "'");
    $sols = [];
    while ($s = $sres->fetch_assoc()) { $sols[] = $s; }
    $dbg['solicitudes'] = $sols;
    $prest = [];
    foreach ($sols as $s) {
        $idsol = $s['idsolicitud'];
        $presRes = $mysqli->query("SELECT * FROM tb_prestamos WHERE idsolicitud = '" . $mysqli->real_escape_string($idsol) . "'");
        while ($p = $presRes->fetch_assoc()) { $prest[] = $p; }
    }
    $dbg['prestamos_by_solicitud'] = $prest;

    echo json_encode(['error' => false, 'count' => count($rows), 'rows' => $rows, 'debug' => $dbg], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit(0);
} else {
    echo json_encode(['error' => true, 'message' => 'This debug script only handles idasesor=0 path']);
    exit(1);
}
