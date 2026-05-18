<?php
$mysqli = new mysqli('localhost', 'root', '', 'minitas');
if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Connect failed: ' . $mysqli->connect_error]);
    exit(1);
}
$queries = [
    'clients' => "SELECT idcliente, apellidos, nombres FROM tb_clientes WHERE apellidos LIKE '%Vasquez%' OR apellidos LIKE '%Vásquez%' OR apellidos LIKE '%VÃ¡squez%';",
    'solicitudes' => "SELECT idsolicitud, apellidos, nombres FROM tb_solicitudes WHERE apellidos LIKE '%Vasquez%' OR apellidos LIKE '%Vásquez%' OR apellidos LIKE '%VÃ¡squez%';",
    'creditos_by_solicitud' => "SELECT c.* FROM tb_creditos c JOIN tb_solicitudes s ON s.idsolicitud = c.idsolicitud WHERE s.apellidos LIKE '%Vasquez%' OR s.apellidos LIKE '%Vásquez%' OR s.apellidos LIKE '%VÃ¡squez%' ORDER BY c.fecha_credito DESC LIMIT 50;",
    'prestamos' => "SELECT pr.* FROM tb_prestamos pr JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud WHERE s.apellidos LIKE '%Vasquez%' OR s.apellidos LIKE '%Vásquez%' OR s.apellidos LIKE '%VÃ¡squez%';",
    'prestamo_pagos' => "SELECT pp.* FROM tb_prestamo_pagos pp JOIN tb_prestamos pr ON pr.idprestamo = pp.idprestamo JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud WHERE s.apellidos LIKE '%Vasquez%' OR s.apellidos LIKE '%Vásquez%';",
    'pagos_legacy' => "SELECT p.* FROM tb_pagos p JOIN tb_creditos c ON c.id = p.idcredito JOIN tb_solicitudes s ON s.idsolicitud = c.idsolicitud WHERE s.apellidos LIKE '%Vasquez%' OR s.apellidos LIKE '%Vásquez%';",
        'desc_tb_creditos' => "SHOW COLUMNS FROM tb_creditos;",
        'desc_tb_credito_detalle' => "SHOW COLUMNS FROM tb_credito_detalle;",
        'desc_tb_prestamos' => "SHOW COLUMNS FROM tb_prestamos;",
        'desc_tb_prestamo_pagos' => "SHOW COLUMNS FROM tb_prestamo_pagos;",
        'desc_tb_pagos' => "SHOW COLUMNS FROM tb_pagos;",
];
$result = [];
foreach ($queries as $key => $sql) {
    $res = $mysqli->query($sql);
    if ($res === false) {
        $result[$key] = ['error' => $mysqli->error];
        continue;
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    $result[$key] = $rows;
    $res->free();
}
$mysqli->close();
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
