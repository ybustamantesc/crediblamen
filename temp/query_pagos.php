<?php
// Query pagos for debugging
$mysqli = new mysqli('localhost', 'root', '', 'minitas');
if ($mysqli->connect_errno) {
    echo "Connect failed: " . $mysqli->connect_error . "\n";
    exit(1);
}
$idprestamo = isset($argv[1]) ? intval($argv[1]) : 5;
$sql = "SELECT p.*, IFNULL(sr.codigo,'') AS serie_codigo, IFNULL(u.first_name,'') AS first_name, IFNULL(u.last_name,'') AS last_name FROM tb_prestamo_pagos p LEFT JOIN tb_series_recibos sr ON sr.idserie = p.idserie LEFT JOIN users u ON u.id = p.idusuario WHERE p.idprestamo = ? ORDER BY p.idcuota ASC, p.fecha_pago ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $idprestamo);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
echo "Payments for idprestamo={$idprestamo}:\n";
if (count($rows) == 0) { echo "(none)\n"; exit(0); }
foreach ($rows as $r) {
    $user = trim(($r['first_name'] ?: '') . ' ' . ($r['last_name'] ?: ''));
    echo sprintf("id=%s | idcuota=%s | monto_pagado=%s | fecha_pago=%s | referencia=%s | idserie=%s | serie=%s | usuario=%s\n",
        $r['id'] ?? $r['idpago'] ?? '',
        $r['idcuota'] ?? '',
        $r['monto_pagado'] ?? '',
        $r['fecha_pago'] ?? '',
        $r['referencia'] ?? '',
        $r['idserie'] ?? '',
        $r['serie_codigo'] ?? '',
        $user ?: '(none)'
    );
}
$stmt->close();
$mysqli->close();
