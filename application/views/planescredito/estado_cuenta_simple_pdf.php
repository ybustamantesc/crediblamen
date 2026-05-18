<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Estado de Cuenta</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        html, body { height: 100%; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 0; }

        .page-header { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 12px; }
        .logo-wrap { flex: 0 0 92px; }
        .logo { max-height: 56px; }
        .header-main { flex: 1; }
        .header-title { margin: 0; font-weight: 700; letter-spacing: 1px; color: #222; font-size: 20px; }
        .header-subtitle { font-size: 14px; color: #666; margin-top: 2px; }

        .ec-top-wrap {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 12px;
        }
        .ec-top-wrap > tbody > tr > td {
            vertical-align: top;
            padding: 0 12px;
        }
        .ec-top-wrap > tbody > tr > td:first-child {
            padding-left: 0;
            width: 39%;
        }
        .ec-top-wrap > tbody > tr > td:nth-child(2) {
            width: 30%;
            border-left: 2px solid #d8e1ee;
        }
        .ec-top-wrap > tbody > tr > td:nth-child(3) {
            width: 31%;
            border-left: 2px solid #d8e1ee;
            padding-right: 0;
        }

        .ec-info-mini { width:100%; border-collapse:collapse; font-size:11px; }
        .ec-info-mini td { padding: 3px 4px; vertical-align: top; }
        .ec-info-mini td.label { font-weight:700; color:#1f2937; width:145px; white-space:nowrap; }

        .ec-corte-card {
            border: 1px solid #d8e1ee;
            border-radius: 12px;
            background: #f5f9ff;
            padding: 10px 12px;
        }
        .ec-corte-card h5 {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .ec-corte-table { width:100%; border-collapse:collapse; font-size:10.5px; }
        .ec-corte-table td { padding:4px 0; border-bottom:1px dashed #d4dbe8; }
        .ec-corte-table tr:last-child td { border-bottom:0; }
        .ec-corte-label { color:#334155; font-weight:700; }
        .ec-corte-value { color:#0f172a; font-weight:700; text-align:right; }
        .ec-corte-total td { padding-top:7px; border-top:2px solid #c6d2e6; border-bottom:0; }

        .ec-big-table { width: 100%; border-collapse: collapse; font-size: 8.6px; background: #fff; table-layout: auto; }
        .ec-big-table th {
            border: 1px solid #b0b0b0; background: #f5f7fa; color: #222; font-weight: 700; padding: 5px 3px; text-align: center;
        }
        .ec-big-table td {
            border: 1px solid #e0e0e0; padding: 4px 3px; text-align: right; background: #fff;
        }
        .ec-big-table td.text-left { text-align: left; }
        .ec-big-table td.text-center { text-align: center; }
        .ec-big-table th,
        .ec-big-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ec-big-table tfoot td {
            background: #eef3fb;
            font-weight: 700;
            color: #1a237e;
            border-top: 2px solid #95a7c7;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .ec-big-table tfoot td.total-label {
            text-align: center;
            letter-spacing: .2px;
            color: #1e3a8a;
        }
        .ec-big-table tfoot td.total-money {
            text-align: right;
            color: #1e40af;
        }

        .estado-badge { display: inline-block; min-width: 98px; padding: 2px 6px; border-radius: 999px; font-size: 9px; font-weight: 700; text-align: center; }
        .estado-vigente { background: #e7f6ea; color: #17643a; }
        .estado-al-dia { background: #e6f4ff; color: #0b5cad; }
        .estado-mora-temprana { background: #fff4d6; color: #8a5a00; }
        .estado-mora { background: #ffe6bf; color: #9a4d00; }
        .estado-mora-media { background: #ffd9b3; color: #994400; }
        .estado-mora-alta { background: #ffc9c9; color: #9f1d1d; }
        .estado-riesgo { background: #f7c6d9; color: #8f1655; }
        .estado-dudosa { background: #e4cdfc; color: #5a2f91; }
        .estado-critica { background: #d9d0ff; color: #442f8f; }
        .estado-irrecuperable { background: #d6d6d6; color: #444; }
        .estado-castigado { background: #2f2f2f; color: #fff; }
        .estado-anulado { background: #7b7b7b; color: #fff; }

        .watermark-anulado {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-28deg);
            font-size: 110px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #b71c1c;
            opacity: 0.13;
            border: 7px solid #b71c1c;
            border-radius: 16px;
            padding: 10px 28px;
            z-index: 999;
            pointer-events: none;
            white-space: nowrap;
        }

        .summary-box { margin-top: 12px; padding: 10px 12px; background: #f8fafd; border-radius: 8px; border: 1px solid #e0e0e0; }
        .summary-title { margin-top: 0; color: #1a237e; font-weight: 700; font-size: 13px; }
        .summary-list { font-size: 11px; color: #222; margin: 0; padding-left: 18px; }
        .summary-list li { margin-bottom: 3px; }
        .summary-comment { font-size: 11px; color: #444; margin-top: 8px; }

        .signatures-table { margin-top: 6mm; width: 100%; border-collapse: collapse; table-layout: fixed; }
        .signatures-table td { text-align: center; vertical-align: top; padding: 0 8px; }
        .signature-line { border-top: 2.6px solid #222; width: 190px; margin: 0 auto; }
        .signature-label { margin-top: 6px; font-size: 11px; color: #333; }
    </style>
</head>
<body>
<?php
    $estado_class_map = array(
        'VIGENTE' => 'estado-vigente',
        'AL DÍA' => 'estado-al-dia',
        'MORA TEMPRANA' => 'estado-mora-temprana',
        'MORA' => 'estado-mora',
        'MORA MEDIA' => 'estado-mora-media',
        'MORA ALTA' => 'estado-mora-alta',
        'CARTERA EN RIESGO' => 'estado-riesgo',
        'CARTERA DUDOSA' => 'estado-dudosa',
        'CARTERA CRÍTICA' => 'estado-critica',
        'CARTERA IRRECUPERABLE' => 'estado-irrecuperable',
        'CASTIGADO' => 'estado-castigado',
        'ANULADO' => 'estado-anulado'
    );

    $es_anulado = false;
    if (isset($prestamo->estado_credito) && strtoupper(trim((string)$prestamo->estado_credito)) === 'ANULADO') {
        $es_anulado = true;
    } elseif (isset($prestamo->estado) && intval($prestamo->estado) === 2) {
        $es_anulado = true;
    } elseif (isset($prestamo->estado_aprobacion) && strtolower(trim((string)$prestamo->estado_aprobacion)) === 'anulado') {
        $es_anulado = true;
    }

    $sum_cuota = 0.0;
    $sum_capital = 0.0;
    $sum_interes = 0.0;
    $sum_comision = 0.0;
    $sum_pagado = 0.0;
    $sum_saldo = 0.0;
    if (!empty($rows) && is_array($rows)) {
        foreach ($rows as $rr) {
            $sum_cuota += isset($rr['cuota']) ? floatval($rr['cuota']) : 0;
            $sum_capital += isset($rr['principal']) ? floatval($rr['principal']) : 0;
            $sum_interes += isset($rr['interes']) ? floatval($rr['interes']) : 0;
            $sum_comision += isset($rr['comision']) ? floatval($rr['comision']) : 0;
            $sum_pagado += isset($rr['pagado']) ? floatval($rr['pagado']) : 0;
            $sum_saldo += isset($rr['saldo']) ? floatval($rr['saldo']) : 0;
        }
    }

    $cuotas_totales = is_array($rows) ? count($rows) : 0;
    $cuotas_pagadas = 0;
    $ultimo_pago = null;
    $total_pagado = 0.0;
    $total_saldo = 0.0;
    $cuota_actual = null;

    if (!empty($rows) && is_array($rows)) {
        foreach ($rows as $r) {
            $pagado = isset($r['pagado']) ? floatval($r['pagado']) : 0;
            $saldo = isset($r['saldo']) ? floatval($r['saldo']) : 0;
            $total_pagado += $pagado;
            $total_saldo += $saldo;
            if ($saldo <= 0 && $pagado > 0) {
                $cuotas_pagadas++;
            }
            if ($cuota_actual === null && $saldo > 0) {
                $cuota_actual = $r;
            }
            if (!empty($r['payments']) && is_array($r['payments'])) {
                foreach ($r['payments'] as $p) {
                    if (!empty($p['fecha_pago'])) {
                        if ($ultimo_pago === null || $p['fecha_pago'] > $ultimo_pago) {
                            $ultimo_pago = $p['fecha_pago'];
                        }
                    }
                }
            }
        }
    }

    $cuotas_pendientes = $cuotas_totales - $cuotas_pagadas;
    $estado_actual = isset($prestamo->estado_credito) ? $prestamo->estado_credito : 'VIGENTE';
    $cuota_actual_texto = 'Crédito sin cuotas pendientes.';
    $detalle_cuota_actual = 'No hay mora pendiente ni cuotas por pagar.';

    if ($cuota_actual !== null) {
        $numero_cuota_actual = isset($cuota_actual['numero']) ? $cuota_actual['numero'] : '-';
        $fecha_cuota_actual = isset($cuota_actual['fecha']) ? $cuota_actual['fecha'] : '-';
        $saldo_cuota_actual = isset($cuota_actual['saldo']) ? floatval($cuota_actual['saldo']) : 0;
        $dias_mora_actual = isset($cuota_actual['dias_mora']) ? intval($cuota_actual['dias_mora']) : 0;
        $monto_mora_actual = isset($cuota_actual['monto_mora']) ? floatval($cuota_actual['monto_mora']) : 0;
        $estado_pago_actual = isset($cuota_actual['estado_pago']) ? $cuota_actual['estado_pago'] : 'VIGENTE';

        $cuota_actual_texto = 'Cuota #' . $numero_cuota_actual . ' con vencimiento ' . $fecha_cuota_actual . '.';
        $detalle_cuota_actual = 'Saldo actual $' . number_format($saldo_cuota_actual, 2)
            . ' | Estado de pago: ' . $estado_pago_actual
            . ' | Días de mora: ' . $dias_mora_actual
            . ' | Monto en mora: $' . number_format($monto_mora_actual, 2) . '.';
    }

    $comentario = '';
    if ($cuota_actual === null) {
        $comentario = 'El crédito no tiene cuotas pendientes. Se encuentra al día o totalmente cancelado.';
    } elseif ($estado_actual === 'VIGENTE') {
        $comentario = 'La cuota actual corresponde a ' . $cuota_actual_texto . ' El crédito está vigente y sin mora registrada al momento.';
    } elseif (strpos($estado_actual, 'MORA') !== false) {
        $comentario = 'La cuota actual corresponde a ' . $cuota_actual_texto . ' El crédito presenta atraso y requiere seguimiento inmediato para normalizar el pago.';
    } elseif (strpos($estado_actual, 'CARTERA') !== false || $estado_actual === 'CASTIGADO') {
        $comentario = 'La cuota actual corresponde a ' . $cuota_actual_texto . ' El crédito ya se encuentra en una clasificación de riesgo alta y necesita atención prioritaria.';
    } else {
        $comentario = 'El crédito está en estado: ' . $estado_actual;
    }

    $logo_data_uri = '';
    $logo_candidates = array(
        FCPATH . 'Logo/Logo.png',
        FCPATH . 'public/img/credi_socios_logo.png',
        FCPATH . 'public/img/logo.jpg',
        FCPATH . 'public/img/logo.png',
        FCPATH . 'assets/logo.png'
    );
    foreach ($logo_candidates as $logo_file) {
        if (file_exists($logo_file)) {
            $logo_data = @file_get_contents($logo_file);
            if ($logo_data !== false) {
                $ext = strtolower(pathinfo($logo_file, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                $logo_data_uri = 'data:' . $mime . ';base64,' . base64_encode($logo_data);
                break;
            }
        }
    }
?>

<?php if ($es_anulado): ?>
    <div class="watermark-anulado">ANULADO</div>
<?php endif; ?>

    <div class="page-header">
        <div class="logo-wrap">
            <?php if ($logo_data_uri !== ''): ?>
                <img src="<?php echo $logo_data_uri; ?>" alt="Logo" class="logo">
            <?php endif; ?>
        </div>
        <div class="header-main">
            <h2 class="header-title">Estado de Cuenta</h2>
            <div class="header-subtitle">Plan N° <?php echo isset($prestamo->idprestamo) ? $prestamo->idprestamo : ''; ?></div>
        </div>
    </div>

    <?php
        $saldo_corte_principal = 0.0;
        $interes_corriente_corte = 0.0;
        $interes_moratorio_corte = 0.0;
        $saldo_comision_corte = 0.0;
        if (!empty($rows) && is_array($rows)) {
            foreach ($rows as $fila_corte) {
                $saldo_fila = isset($fila_corte['saldo']) ? floatval($fila_corte['saldo']) : 0.0;
                if ($saldo_fila > 0) {
                    $saldo_corte_principal += isset($fila_corte['principal']) ? floatval($fila_corte['principal']) : 0.0;
                    $interes_corriente_corte += isset($fila_corte['interes']) ? floatval($fila_corte['interes']) : 0.0;
                    $interes_moratorio_corte += isset($fila_corte['monto_mora']) ? floatval($fila_corte['monto_mora']) : 0.0;
                    $saldo_comision_corte += isset($fila_corte['comision']) ? floatval($fila_corte['comision']) : 0.0;
                }
            }
        }
        $saldo_seguro_corte = isset($prestamo->seguros) ? floatval($prestamo->seguros) : 0.0;
        $total_corte = $saldo_corte_principal + $interes_corriente_corte + $interes_moratorio_corte + $saldo_seguro_corte + $saldo_comision_corte;
        $estado_actual_badge = isset($prestamo->estado_credito) ? $prestamo->estado_credito : 'VIGENTE';
    ?>

    <table class="ec-top-wrap">
        <tr>
            <td>
                <div class="ec-corte-card">
                    <h5>Saldos Al Corte</h5>
                    <table class="ec-corte-table">
                        <tr><td class="ec-corte-label">Saldo al corte (Principal)</td><td class="ec-corte-value">$<?php echo number_format($saldo_corte_principal, 2); ?></td></tr>
                        <tr><td class="ec-corte-label">Interés corriente</td><td class="ec-corte-value">$<?php echo number_format($interes_corriente_corte, 2); ?></td></tr>
                        <tr><td class="ec-corte-label">Interés Moratorio</td><td class="ec-corte-value">$<?php echo number_format($interes_moratorio_corte, 2); ?></td></tr>
                        <tr><td class="ec-corte-label">Saldo de Seguro</td><td class="ec-corte-value">$<?php echo number_format($saldo_seguro_corte, 2); ?></td></tr>
                        <tr><td class="ec-corte-label">Saldo de Comisión</td><td class="ec-corte-value">$<?php echo number_format($saldo_comision_corte, 2); ?></td></tr>
                        <tr class="ec-corte-total"><td class="ec-corte-label">Total</td><td class="ec-corte-value">$<?php echo number_format($total_corte, 2); ?></td></tr>
                    </table>
                </div>
            </td>
            <td>
                <table class="ec-info-mini">
                    <tr><td class="label">Codigo de Cliente:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['codigo_cliente']) ? (string)$resumen_tecnico['codigo_cliente'] : '-'); ?></td></tr>
                    <tr><td class="label">Tipo de Producto:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['tipo_producto']) ? $resumen_tecnico['tipo_producto'] : '-'); ?></td></tr>
                    <tr><td class="label">Comisión:</td><td><?php echo isset($resumen_tecnico['comision']) && $resumen_tecnico['comision'] !== null ? number_format($resumen_tecnico['comision'], 2). '%' : '-'; ?></td></tr>
                    <tr><td class="label">Fecha Desembolso:</td><td><?php echo isset($prestamo->fecha_desembolso) ? $prestamo->fecha_desembolso : ''; ?></td></tr>
                    <tr><td class="label">Fecha de Vencimiento:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['fecha_vencimiento']) ? $resumen_tecnico['fecha_vencimiento'] : '-'); ?></td></tr>
                    <tr><td class="label">Tasa interés Moratorio:</td><td><?php echo isset($resumen_tecnico['tasa_moratoria']) && $resumen_tecnico['tasa_moratoria'] !== null ? number_format($resumen_tecnico['tasa_moratoria'], 2). '%' : '-'; ?></td></tr>
                    <tr><td class="label">Tipo de Cuota:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['tipo_cuota']) ? $resumen_tecnico['tipo_cuota'] : 'Nivelada'); ?></td></tr>
                    <tr><td class="label">Tipo (Frecuencia):</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['tipo_frecuencia']) ? $resumen_tecnico['tipo_frecuencia'] : '-'); ?></td></tr>
                    <tr><td class="label">Moneda:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['moneda']) ? $resumen_tecnico['moneda'] : 'USD'); ?></td></tr>
                    <tr><td class="label">Asesor/Ruta:</td><td><?php echo isset($prestamo->cobrador) ? htmlspecialchars($prestamo->cobrador) : ''; ?></td></tr>
                    <tr><td class="label">Estado Actual:</td><td><span class="estado-badge <?php echo isset($estado_class_map[$estado_actual_badge]) ? $estado_class_map[$estado_actual_badge] : 'estado-vigente'; ?>"><?php echo htmlspecialchars($estado_actual_badge); ?></span></td></tr>
                </table>
            </td>
            <td>
                <table class="ec-info-mini">
                    <tr><td class="label">Cliente:</td><td><?php echo isset($prestamo->cliente_nombre) ? htmlspecialchars($prestamo->cliente_nombre) : ''; ?></td></tr>
                    <tr><td class="label">Sector económico:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['sector_economico']) ? $resumen_tecnico['sector_economico'] : '-'); ?></td></tr>
                    <tr><td class="label">Monto Crédito:</td><td><?php echo isset($prestamo->monto_credito) ? '$'.number_format($prestamo->monto_credito,2) : ''; ?></td></tr>
                    <tr><td class="label">Fecha Crédito:</td><td><?php echo isset($prestamo->fecha_credito) ? $prestamo->fecha_credito : ''; ?></td></tr>
                    <tr><td class="label">Fecha 1ra Cuota:</td><td><?php echo (isset($rows) && is_array($rows) && count($rows) > 0) ? htmlspecialchars($rows[0]['fecha']) : '-'; ?></td></tr>
                    <tr><td class="label">Interés:</td><td><?php if (isset($prestamo->interes_credito)) { $tasa = floatval($prestamo->interes_credito); echo ($tasa < 1 ? ($tasa*100) : $tasa) . '%'; } else { echo '-'; } ?></td></tr>
                    <tr><td class="label">TCA:</td><td><?php echo isset($resumen_tecnico['tca']) && $resumen_tecnico['tca'] !== null ? number_format($resumen_tecnico['tca'] * 100, 2) . '%' : '-'; ?></td></tr>
                    <tr><td class="label">Plazo (Tiempo):</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['plazo_tiempo']) ? $resumen_tecnico['plazo_tiempo'] : '-'); ?></td></tr>
                    <tr><td class="label">Cuotas en Mora:</td><td><?php echo htmlspecialchars((string)(isset($resumen_tecnico['cuotas_en_mora']) ? $resumen_tecnico['cuotas_en_mora'] : 0)); ?></td></tr>
                    <tr><td class="label">Metodología:</td><td><?php echo htmlspecialchars(isset($resumen_tecnico['metodologia']) ? $resumen_tecnico['metodologia'] : 'Individual'); ?></td></tr>
                    <tr><td class="label">Creador:</td><td><?php echo isset($creador_nombre) ? htmlspecialchars($creador_nombre) : ''; ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="ec-big-table">
        <thead>
            <tr>
                <th style="width:40px;">No Cuota</th>
                <th style="width:78px;">Fecha</th>
                <th style="width:70px;">Cuota</th>
                <th style="width:70px;">Capital</th>
                <th style="width:70px;">Interés</th>
                <th style="width:58px;">Comisión</th>
                <th style="width:70px;">Pagado</th>
                <th style="width:70px;">Saldo</th>
                <th style="width:68px;">Días Mora</th>
                <th style="width:78px;">Monto Mora</th>
                <th style="width:80px;">Fecha Pagada</th>
                <th style="width:78px;">Serie</th>
                <th style="width:98px;">Estado Pago</th>
                <th style="width:58px;">Días Trans.</th>
                <th style="width:74px;">Asiento</th>
                <th style="width:62px;">Módulo</th>
                <th style="width:60px;">T/C</th>
                <th style="width:60px;">Seguro</th>
                <th style="width:60px;">Dispensa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows) && is_array($rows)): foreach ($rows as $r): ?>
                <?php
                    $payments = (!empty($r['payments']) && is_array($r['payments'])) ? $r['payments'] : array();
                    if (count($payments) > 0) {
                        $firstPay = $payments[0];
                ?>
                <tr>
                    <td class="text-right"><?php echo htmlspecialchars($r['numero']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($r['fecha']); ?></td>
                    <td><?php echo '$' . number_format(floatval($r['cuota']), 2); ?></td>
                    <td><?php echo isset($r['principal']) ? '$' . number_format(floatval($r['principal']), 2) : '-'; ?></td>
                    <td><?php echo isset($r['interes']) ? '$' . number_format(floatval($r['interes']), 2) : '-'; ?></td>
                    <td><?php echo '$' . number_format(floatval(isset($r['comision']) ? $r['comision'] : 0), 2); ?></td>
                    <td><?php echo '$' . number_format(floatval($r['pagado']), 2); ?></td>
                    <td><?php echo '$' . number_format(floatval($r['saldo']), 2); ?></td>
                    <td class="text-center"><?php echo isset($r['dias_mora']) ? intval($r['dias_mora']) : 0; ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval(isset($r['monto_mora']) ? $r['monto_mora'] : 0), 2); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars(substr((isset($firstPay['fecha_pago']) ? $firstPay['fecha_pago'] : ''), 0, 10)); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars(isset($firstPay['serie_codigo']) ? $firstPay['serie_codigo'] : ''); ?></td>
                    <?php $estado_pago = isset($r['estado_pago']) ? $r['estado_pago'] : '-'; ?>
                    <td class="text-left">
                        <span class="estado-badge <?php echo isset($estado_class_map[$estado_pago]) ? $estado_class_map[$estado_pago] : 'estado-vigente'; ?>">
                            <?php echo htmlspecialchars($estado_pago); ?>
                        </span>
                    </td>
                    <td class="text-right"><?php echo intval(isset($r['dias_transcurridos']) ? $r['dias_transcurridos'] : 0); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['asiento_contable']) && $r['asiento_contable'] !== '' ? $r['asiento_contable'] : '-'); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars(isset($r['modulo']) ? $r['modulo'] : '-'); ?></td>
                    <td class="text-right"><?php echo number_format(floatval(isset($r['tipo_cambio']) ? $r['tipo_cambio'] : 36.6243), 4); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['seguro']) ? $r['seguro'] : '0.00'); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['dispensa']) ? $r['dispensa'] : '0.00'); ?></td>
                </tr>
                <?php
                        for ($i = 1; $i < count($payments); $i++) {
                            $pay = $payments[$i];
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-left"><?php echo htmlspecialchars(substr((isset($pay['fecha_pago']) ? $pay['fecha_pago'] : ''), 0, 10)); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars(isset($pay['serie_codigo']) ? $pay['serie_codigo'] : ''); ?></td>
                    <td></td>
                    <td></td>
                    <td>-</td>
                    <td>-</td>
                    <td>36.6243</td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <?php
                        }
                    } else {
                ?>
                <tr>
                    <td class="text-right"><?php echo htmlspecialchars($r['numero']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($r['fecha']); ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval($r['cuota']), 2); ?></td>
                    <td class="text-right"><?php echo isset($r['principal']) ? '$' . number_format(floatval($r['principal']), 2) : '-'; ?></td>
                    <td class="text-right"><?php echo isset($r['interes']) ? '$' . number_format(floatval($r['interes']), 2) : '-'; ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval(isset($r['comision']) ? $r['comision'] : 0), 2); ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval($r['pagado']), 2); ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval($r['saldo']), 2); ?></td>
                    <td class="text-center"><?php echo isset($r['dias_mora']) ? intval($r['dias_mora']) : 0; ?></td>
                    <td class="text-right"><?php echo '$' . number_format(floatval(isset($r['monto_mora']) ? $r['monto_mora'] : 0), 2); ?></td>
                    <td>-</td>
                    <td>-</td>
                    <?php $estado_pago = isset($r['estado_pago']) ? $r['estado_pago'] : '-'; ?>
                    <td>
                        <span class="estado-badge <?php echo isset($estado_class_map[$estado_pago]) ? $estado_class_map[$estado_pago] : 'estado-vigente'; ?>">
                            <?php echo htmlspecialchars($estado_pago); ?>
                        </span>
                    </td>
                    <td class="text-right"><?php echo intval(isset($r['dias_transcurridos']) ? $r['dias_transcurridos'] : 0); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['asiento_contable']) && $r['asiento_contable'] !== '' ? $r['asiento_contable'] : '-'); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars(isset($r['modulo']) ? $r['modulo'] : '-'); ?></td>
                    <td class="text-right"><?php echo number_format(floatval(isset($r['tipo_cambio']) ? $r['tipo_cambio'] : 36.6243), 4); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['seguro']) ? $r['seguro'] : '0.00'); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars(isset($r['dispensa']) ? $r['dispensa'] : '0.00'); ?></td>
                </tr>
                <?php
                    }
                ?>
            <?php endforeach; else: ?>
                <tr><td colspan="18" class="text-center">No hay cuotas para este plan.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="total-label">Totales</td>
                <td></td>
                <td class="total-money"><?php echo '$' . number_format($sum_cuota, 2); ?></td>
                <td class="total-money"><?php echo '$' . number_format($sum_capital, 2); ?></td>
                <td class="total-money"><?php echo '$' . number_format($sum_interes, 2); ?></td>
                <td class="total-money"><?php echo '$' . number_format($sum_comision, 2); ?></td>
                <td class="total-money"><?php echo '$' . number_format($sum_pagado, 2); ?></td>
                <td class="total-money"><?php echo '$' . number_format($sum_saldo, 2); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures-table">
        <tr>
            <td><div class="signature-line"></div></td>
            <td><div class="signature-line"></div></td>
            <td><div class="signature-line"></div></td>
        </tr>
        <tr>
            <td><div class="signature-label">Cobrador / Firma</div></td>
            <td><div class="signature-label">Cartera</div></td>
            <td><div class="signature-label">Cliente</div></td>
        </tr>
    </table>

    <div class="summary-box">
        <h4 class="summary-title">Resumen del comportamiento del crédito</h4>
        <ul class="summary-list">
            <li><b>Monto original:</b> <?php echo isset($prestamo->monto_credito) ? '$' . number_format($prestamo->monto_credito, 2) : '-'; ?></li>
            <li><b>Cuotas totales:</b> <?php echo $cuotas_totales; ?> &nbsp; <b>Pagadas:</b> <?php echo $cuotas_pagadas; ?> &nbsp; <b>Pendientes:</b> <?php echo $cuotas_pendientes; ?></li>
            <li><b>Total pagado:</b> <?php echo '$' . number_format($total_pagado, 2); ?> &nbsp; <b>Saldo pendiente:</b> <?php echo '$' . number_format($total_saldo, 2); ?></li>
            <li><b>Estado actual:</b> <?php echo htmlspecialchars($estado_actual); ?></li>
            <li><b>Cuota actual:</b> <?php echo htmlspecialchars($cuota_actual_texto); ?></li>
            <li><b>Detalle cuota actual:</b> <?php echo htmlspecialchars($detalle_cuota_actual); ?></li>
            <li><b>Último pago:</b> <?php echo $ultimo_pago ? substr($ultimo_pago, 0, 10) : 'N/A'; ?></li>
        </ul>
        <div class="summary-comment"><b>Comentario:</b> <?php echo htmlspecialchars($comentario); ?></div>
    </div>
</body>
</html>
