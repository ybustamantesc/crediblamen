<?php
// Debug public endpoint that mimics Reporte::getCreditoFechas() output (no auth)
$hostname='localhost'; $username='root'; $password=''; $database='minitas';
$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_errno) { header('Content-Type: application/json'); echo json_encode(['data'=>[],'error'=>true,'message'=>'DB connect error']); exit; }
$mysqli->set_charset('utf8');
$fechaInicio = $_GET['fechaInicio'] ?? date('Y-m-01');
$fechaFin = $_GET['fechaFin'] ?? date('Y-m-d');
$idcliente = $_GET['idcliente'] ?? 0;
$idasesor = $_GET['idasesor'] ?? 0;

// Run two separate queries (legacy and new) and merge to avoid UNION column alignment issues
$params = [
    'fi' => $mysqli->real_escape_string($fechaInicio),
    'ff' => $mysqli->real_escape_string($fechaFin),
    'idc' => $mysqli->real_escape_string($idcliente)
];

$sql1 = sprintf(
    "SELECT c.id as id,
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
            a.nombres as nombre_asesor,
            ROUND(c.monto_credito,2) as monto_dolares,
            ROUND(c.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc WHERE tc.fecha <= c.fecha_credito ORDER BY tc.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
            ROUND(c.interes_credito * 100,0) as interes_porcent,
            NULL as comision_desembolso,
            COALESCE((SELECT SUM(p.monto_pago) FROM tb_pagos p WHERE p.idcredito = c.id),0) as total_pagado,
            CASE
                WHEN (SELECT COUNT(*) FROM tb_credito_detalle cd WHERE cd.idcredito = c.id AND cd.estado_couta = 0) >= c.numero_coutas THEN 'PAGADO'
                WHEN EXISTS(SELECT 1 FROM tb_credito_detalle cd2 WHERE cd2.idcredito = c.id AND cd2.fecha_couta < CURDATE() AND cd2.estado_couta = 1) THEN 'EN MORA'
                ELSE 'VIGENTE'
            END as estado_calculado
     FROM tb_creditos c
     JOIN tb_clientes cl ON cl.idcliente = c.idcliente
     LEFT JOIN tb_asesores a ON a.idasesor = c.idasesor
     WHERE c.fecha_credito >= '%s' AND c.fecha_credito <= '%s' AND c.idcliente = '%s'",
    $params['fi'], $params['ff'], $params['idc']
);

$sql2 = sprintf(
    "SELECT pr.idprestamo as id,
            pr.monto_credito,
            COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) as fecha_credito,
            pr.estado,
            pr.idasesor,
            pr.numero_coutas,
            pr.interes_credito,
            COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) as total_interes,
            ROUND(pr.monto_credito + COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) + (pr.monto_credito * COALESCE(pr.comision_desembolso,0) / 100),2) as total_pagar,
            cl2.idcliente,
            cl2.apellidos,
            cl2.nombres,
            COALESCE(a2.nombres, s.nombre_promotor, s.promotor) as nombre_asesor,
            ROUND(pr.monto_credito,2) as monto_dolares,
            ROUND(pr.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc2 WHERE tc2.fecha <= COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) ORDER BY tc2.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
            ROUND(pr.interes_credito * 100,0) as interes_porcent,
            pr.comision_desembolso,
            COALESCE((SELECT SUM(pp.monto_pagado) FROM tb_prestamo_pagos pp WHERE pp.idprestamo = pr.idprestamo),0) as total_pagado,
            CASE
                WHEN (SELECT COUNT(*) FROM tb_prestamo_cuotas pc2 WHERE pc2.idprestamo = pr.idprestamo AND (pc2.saldo IS NULL OR pc2.saldo <= 0)) >= pr.numero_coutas THEN 'PAGADO'
                WHEN EXISTS(SELECT 1 FROM tb_prestamo_cuotas pc3 WHERE pc3.idprestamo = pr.idprestamo AND pc3.fecha_vencimiento < CURDATE() AND (pc3.saldo IS NULL OR pc3.saldo > 0)) THEN 'EN MORA'
                ELSE 'VIGENTE'
            END as estado_calculado
     FROM tb_prestamos pr
     JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
     JOIN tb_clientes cl2 ON cl2.idcliente = s.idcliente
     LEFT JOIN tb_asesores a2 ON a2.idasesor = pr.idasesor
     WHERE ((pr.fecha_credito >= '%s' AND pr.fecha_credito <= '%s') OR (DATE(pr.created_at) >= '%s' AND DATE(pr.created_at) <= '%s')) AND cl2.idcliente = '%s'",
    $params['fi'], $params['ff'], $params['fi'], $params['ff'], $params['idc']
);

$all_rows = [];
$r1 = $mysqli->query($sql1);
if ($r1) {
    while ($rr = $r1->fetch_assoc()) $all_rows[] = $rr;
}
$r2 = $mysqli->query($sql2);
if ($r2) {
    while ($rr = $r2->fetch_assoc()) $all_rows[] = $rr;
}

// sort by fecha_credito desc
usort($all_rows, function($a,$b){
    $ta = isset($a['fecha_credito']) ? strtotime($a['fecha_credito']) : 0;
    $tb = isset($b['fecha_credito']) ? strtotime($b['fecha_credito']) : 0;
    return $tb - $ta;
});

$data = []; $i = 0;

// helper to fix encoding
function fix_utf($s){ if ($s===null) return ''; if (mb_detect_encoding($s, 'UTF-8', true)) return $s; return mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1'); }

// iterate over merged rows
foreach ($all_rows as $row) {
    $i++;
    $forma_pago = '';
    $fp = $row['forma_pago'] ?? null;
    if ($fp === '0' || $fp === 0) $forma_pago = 'DIARIO';
    elseif ($fp === '1' || $fp === 1) $forma_pago = 'SEMANAL';
    elseif ($fp === '2' || $fp === 2) $forma_pago = 'QUINCENAL';
    elseif ($fp === '3' || $fp === 3) $forma_pago = 'MENSUAL';

    $estado = '';
    if (isset($row['estado']) && $row['estado'] == '0') $estado = '<span class="badge  badge-success mr-2 mb-1"><i class="fas fa-check-circle"></i> PAGADO</span>';
    if (isset($row['estado']) && $row['estado'] == '1') $estado = '<span class="badge badge-primary mr-2 mb-1"><i class="fas fa-sync-alt"></i> POR COBRAR</span>';

    // normalize interest
    $interes_formatted = '';
    $raw_interes = null;
    if (isset($row['interes_credito']) && is_numeric($row['interes_credito'])) {
        $raw_interes = (float)$row['interes_credito'];
        if ($raw_interes > 0 && $raw_interes <= 1) $interest_percent = $raw_interes * 100;
        elseif ($raw_interes > 1 && $raw_interes <= 100) $interest_percent = $raw_interes;
        else $interest_percent = $raw_interes;
        $interes_formatted = number_format($interest_percent, 2, '.', '') . '%';
    } elseif (isset($row['interes_porcent']) && is_numeric($row['interes_porcent'])) {
        $interest_percent = (float)$row['interes_porcent'];
        $interes_formatted = number_format($interest_percent, 2, '.', '') . '%';
        $raw_interes = $interest_percent;
    }

    // normalize commission
    $comision_formatted = '';
    $raw_comision = null;
    if (isset($row['comision_desembolso']) && $row['comision_desembolso'] !== null && $row['comision_desembolso'] !== '') {
        $raw_comision = (float)$row['comision_desembolso'];
        // Heuristic normalization for unexpectedly large stored values
        if ($raw_comision > 1000) {
            // assume stored as thousandths (e.g. 18310 -> 18.31%)
            $com_percent = $raw_comision / 1000.0;
        } elseif ($raw_comision > 100) {
            // assume stored as hundredths (e.g. 18310/100 -> 183.10%)
            $com_percent = $raw_comision / 100.0;
        } elseif ($raw_comision > 1 && $raw_comision <= 100) {
            $com_percent = $raw_comision;
        } elseif ($raw_comision > 0 && $raw_comision <= 1) {
            $com_percent = $raw_comision * 100;
        } else {
            $com_percent = $raw_comision;
        }
        $comision_formatted = number_format($com_percent, 2, '.', '') . '%';
    }

    $estado_final = isset($row['estado_calculado']) && $row['estado_calculado'] !== '' ? '<span class="badge badge-info mr-2 mb-1">' . $row['estado_calculado'] . '</span>' : $estado;

    // Build client name (try several candidate fields because some rows have mixed columns)
    $candidates = [($row['monto_dolares'] ?? ''), ($row['apellidos'] ?? ''), ($row['nombres'] ?? ''), ($row['nombre_asesor'] ?? '')];
    $client_name = '';
    foreach ($candidates as $cand) {
        if (!$cand) continue;
        $cand_fixed = fix_utf($cand);
        // prefer candidates containing letters (handles names with accents)
        if (preg_match('/\p{L}/u', $cand_fixed) || preg_match('/[^0-9.,\s]/u', $cand_fixed)) { $client_name = trim($cand_fixed); break; }
    }
    if (!$client_name) $client_name = fix_utf(trim((($row['apellidos'] ?? '') . ', ' . ($row['nombres'] ?? '')), " ,"));
    $asesor_field = $client_name ? $client_name : (isset($row['nombre_asesor']) ? fix_utf($row['nombre_asesor']) : '');
    $formatted_total_pagado = isset($row['total_pagado']) ? number_format((float)$row['total_pagado'],2,'.','') : '';

    // Provide both the client name and monto pagado explicitly
    $entry = [
        'id' => $i,
        'cliente' => $client_name,
        'nombreCliente' => $client_name,
        'asesor' => $asesor_field,
        'idCredito' => $row['id'] ?? '',
        'fechaCredito' => isset($row['fecha_credito']) ? date('d/m/Y', strtotime($row['fecha_credito'])) : '',
        'montoCredito' => isset($row['monto_credito']) ? number_format((float)$row['monto_credito'],2,'.','') : '',
        'interes' => $interes_formatted,
        'coutas' => $row['numero_coutas'] ?? '',
        'totalInteres' => isset($row['total_interes']) ? number_format((float)$row['total_interes'],2,'.','') : '',
        'totalPagar' => isset($row['total_pagar']) ? number_format((float)$row['total_pagar'],2,'.','') : '',
        'totalPagado' => $formatted_total_pagado,
        'montoPagado' => $formatted_total_pagado,
        'saldoTotal' => isset($row['total_pagar']) ? number_format(((float)$row['total_pagar'] - (float)$row['total_pagado']),2,'.','') : '',
        'comisionDesembolso' => $comision_formatted,
        'raw_interes_credito' => $raw_interes,
        'raw_comision_desembolso' => $raw_comision,
        'formaPago' => $forma_pago,
        'estado' => $estado_final
    ];
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        $entry['raw_row'] = $row;
    }
    // --- Calculate credit status: diasMora, saldoCapital, estado_credito, estado_actual, peor_estado_historico
    $loanId = $row['id'] ?? null;
    $next_fecha = null;
    $worst_days = 0;
    if ($loanId) {
        // legacy credit cuotas
        $qcd = $mysqli->query("SELECT MIN(fecha_couta) as next_fecha, MAX(DATEDIFF(CURDATE(), fecha_couta)) as worst_days FROM tb_credito_detalle WHERE idcredito = '".$mysqli->real_escape_string($loanId)."' AND estado_couta = 1");
        if ($qcd) {
            $r = $qcd->fetch_assoc();
            if (!empty($r['next_fecha'])) $next_fecha = $r['next_fecha'];
            if (!empty($r['worst_days'])) $worst_days = max($worst_days, (int)$r['worst_days']);
        }
        // prestamos cuotas
        $qpc = $mysqli->query("SELECT MIN(fecha_vencimiento) as next_fecha_p, MAX(DATEDIFF(CURDATE(), fecha_vencimiento)) as worst_days_p FROM tb_prestamo_cuotas WHERE idprestamo = '".$mysqli->real_escape_string($loanId)."' AND (saldo IS NULL OR saldo > 0)");
        if ($qpc) {
            $r2 = $qpc->fetch_assoc();
            if (empty($next_fecha) && !empty($r2['next_fecha_p'])) $next_fecha = $r2['next_fecha_p'];
            if (!empty($r2['worst_days_p'])) $worst_days = max($worst_days, (int)$r2['worst_days_p']);
        }
    }
    // compute diasMora
    $dias_mora = 0;
    if ($next_fecha) {
        $diff = (int)floor((strtotime(date('Y-m-d')) - strtotime(substr($next_fecha,0,10))) / 86400);
        $dias_mora = $diff > 0 ? $diff : 0;
    }
    // compute saldoCapital (fallback: total_pagar - total_pagado)
    $tp = isset($row['total_pagar']) ? (float)$row['total_pagar'] : (isset($row['totalPagar']) ? (float)$row['totalPagar'] : 0);
    $mp = isset($row['total_pagado']) ? (float)$row['total_pagado'] : (isset($row['totalPagado']) ? (float)$row['totalPagado'] : (isset($row['montoPagado']) ? (float)$row['montoPagado'] : 0));
    $saldo_capital = max(0.0, $tp - $mp);

    // map days to state
    $map_state = function($d) {
        if ($d <= 0) return 'VIGENTE';
        if ($d >= 1 && $d <= 30) return 'VIGENTE EN MORA';
        if ($d >= 31 && $d <= 90) return 'EN MORA';
        if ($d >= 91 && $d <= 180) return 'VENCIDO';
        if ($d > 180) return 'INCOBRABLE';
        return 'VIGENTE';
    };

    $peor_estado_historico = $map_state($worst_days);
    // determine final estado_credito
    if ($saldo_capital == 0.0 && $worst_days > 0) {
        $estado_credito = 'SANEADO';
    } else {
        $estado_credito = $map_state($dias_mora);
    }
    $estado_actual = $estado_credito;

    $entry['diasMora'] = $dias_mora;
    $entry['saldoCapital'] = number_format($saldo_capital,2,'.','');
    $entry['estado_credito'] = $estado_credito;
    $entry['estado_actual'] = $estado_actual;
    $entry['peor_estado_historico'] = $peor_estado_historico;
    $data[] = $entry;
}

// If debug=2, include raw client rows for inspection
if (isset($_GET['debug']) && $_GET['debug'] == '2') {
    $clients = [];
    $qc = $mysqli->query("SELECT idcliente, apellidos, nombres FROM tb_clientes WHERE idcliente = '" . $mysqli->real_escape_string($idcliente) . "'");
    if ($qc) while ($r = $qc->fetch_assoc()) $clients[] = $r;
    header('Content-Type: application/json');
    echo json_encode(['data'=>$data, 'clients'=>$clients], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} else {
    header('Content-Type: application/json');
    echo json_encode(['data'=>$data], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}
