<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Estado de Cuenta</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 10mm 16mm 10mm; }
        html,body { height:100%; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:10px; color:#222; margin:0; padding:0; background:#f6f6f6; }
        .container { width:100%; max-width:780px; margin:0 auto; padding:0; background:#fff; border-radius:8px; box-shadow:0 2px 8px #eee; }
        header { text-align:center; margin-bottom:10px; }
        .logo { margin-bottom:4px; }
        .company { font-size:18px; font-weight:700; color:#1a237e; letter-spacing:1px; }
        .title { font-size:15px; font-weight:700; margin:8px 0 2px 0; color:#222; }
        .subtitle { font-size:11px; color:#444; margin-bottom:10px; }
        .info-block { width:100%; border:1px solid #e0e0e0; border-radius:6px; background:#f8fafc; margin-bottom:12px; padding:10px 12px; }
        .info-table { width:100%; border-collapse:collapse; }
        .info-table td { padding:3px 8px; vertical-align:top; font-size:10px; }
        .info-table .label { font-weight:700; color:#1a237e; width:140px; background:none; }
        .summary-table { width:100%; border-collapse:collapse; margin-bottom:14px; }
        .summary-table td { padding:3px 8px; font-size:10px; }
        .summary-table .label { font-weight:700; color:#333; width:160px; background:none; }
        .big-table { width:100%; border-collapse:collapse; font-size:9px; table-layout:fixed; margin-bottom:18px; }
        .big-table thead th{ border-bottom:2px solid #1a237e; background:#e3e7f7; padding:7px 6px; text-align:left; color:#1a237e; font-size:10px; }
        .big-table tbody td{ border-bottom:1px solid #e0e0e0; padding:6px 6px; vertical-align:top; color:#333; word-wrap:break-word; }
        .big-table tbody tr:nth-child(even){ background:#f7f9fc; }
        .text-right { text-align:right; }
        .signature-row { width:100%; margin-top:9mm; border-collapse:collapse; table-layout:fixed; }
        .signature-row td { width:33.33%; text-align:center; font-size:10px; padding:0 8px; }
        .footer { margin-top:30px; text-align:center; font-size:10px; color:#888; border-top:1px solid #e0e0e0; padding-top:8px; }
        .highlight { color:#1565c0; font-weight:700; }
        table { page-break-inside: auto; }
        tr    { page-break-inside: avoid; page-break-after: auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }
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
    </style>
</head>
<body>

    <?php
        $es_anulado = false;
        if (isset($prestamo->estado_credito) && strtoupper(trim((string)$prestamo->estado_credito)) === 'ANULADO') {
            $es_anulado = true;
        } elseif (isset($prestamo->estado) && intval($prestamo->estado) === 2) {
            $es_anulado = true;
        } elseif (isset($prestamo->estado_aprobacion) && strtolower(trim((string)$prestamo->estado_aprobacion)) === 'anulado') {
            $es_anulado = true;
        }
    ?>

    <?php if ($es_anulado): ?>
        <div class="watermark-anulado">ANULADO</div>
    <?php endif; ?>

    <div class="container">
        <header>
            <div class="logo">
                <?php
                    // Logo centrado
                    $logo_file = FCPATH . 'public/img/logo.jpg';
                    if (!file_exists($logo_file)) {
                        $logo_file = FCPATH . 'public/img/logo.png';
                    }
                    if (file_exists($logo_file)) {
                        $logo_data = @file_get_contents($logo_file);
                        if ($logo_data !== false) {
                            $ext = strtolower(pathinfo($logo_file, PATHINFO_EXTENSION));
                            $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                            $data_uri = 'data:' . $mime . ';base64,' . base64_encode($logo_data);
                            echo '<img src="' . $data_uri . '" style="max-height:60px; margin-bottom:2px;" />';
                        }
                    }
                ?>
            </div>
            <div class="company">Crediblamen</div>
            <div class="title">ESTADO DE CUENTA DE CRÉDITO</div>
            <div class="subtitle">Plan N° <span class="highlight"><?php echo isset($prestamo->idprestamo) ? $prestamo->idprestamo : ''; ?></span></div>
        </header>

        <div class="info-block">
            <table class="info-table">
                <tr>
                    <td class="label">Nombre del Cliente:</td>
                    <td><?php echo isset($prestamo->cliente_nombre) ? htmlspecialchars($prestamo->cliente_nombre) : ''; ?></td>
                    <td class="label">ID Cliente:</td>
                    <td><?php echo isset($prestamo->idcliente) ? htmlspecialchars($prestamo->idcliente) : ''; ?></td>
                </tr>
                <tr>
                    <td class="label">Doc. Identidad:</td>
                    <td><?php echo isset($prestamo->doc_identidad) ? htmlspecialchars($prestamo->doc_identidad) : ''; ?></td>
                    <td class="label">Solicitud:</td>
                    <td><?php echo isset($prestamo->idsolicitud) ? htmlspecialchars($prestamo->idsolicitud) : ''; ?></td>
                </tr>
                <tr>
                    <td class="label">Nombre de Producto:</td>
                    <td><?php echo isset($prestamo->producto_nombre) ? htmlspecialchars($prestamo->producto_nombre) : ''; ?></td>
                    <td class="label">Cobrador / Ruta:</td>
                    <td><?php echo isset($prestamo->cobrador) ? htmlspecialchars($prestamo->cobrador) : ''; ?></td>
                </tr>
            </table>
        </div>

        <table class="summary-table">
            <tr>
                <td class="label">TCA (Tasa Costo Anual - XIRR):</td>
                <td><?php echo isset($tca) && $tca !== null ? round($tca*100,2).'%' : '-'; ?></td>
                <td class="label">Interés corriente anual:</td>
                <td><?php echo isset($prestamo->interes_credito) ? (floatval($prestamo->interes_credito)*100).'%': '-'; ?></td>
            </tr>
            <tr>
                <td class="label">TCM (Tasa Costo Mensual):</td>
                <td><?php echo isset($tcm) && $tcm !== null ? round($tcm*100,2).'%' : '-'; ?></td>
                <td class="label">Interés moratorio:</td>
                <td><?php echo isset($prestamo->interes_moratorio) ? (floatval($prestamo->interes_moratorio)*100).'%' : '-'; ?></td>
            </tr>
            <tr>
                <td class="label">Fecha de desembolso:</td>
                <td><?php echo isset($prestamo->fecha_credito) ? htmlspecialchars($prestamo->fecha_credito) : '-'; ?></td>
                <td class="label">Fecha 1ra cuota:</td>
                <td><?php echo isset($rows) && count($rows)>0 ? htmlspecialchars($rows[0]['fecha']) : '-'; ?></td>
            </tr>
            <tr>
                <td class="label">Monto crédito:</td>
                <td><?php echo isset($prestamo->monto_credito) ? ('$' . number_format($prestamo->monto_credito,2)) : '-'; ?></td>
                <td class="label">Cantidad de cuotas:</td>
                <td><?php echo isset($prestamo->cuotas) ? htmlspecialchars($prestamo->cuotas) : (isset($rows)?count($rows):'-'); ?></td>
            </tr>
        </table>

        <table class="big-table">
            <thead>
                <tr style="background:#f0f0f0; font-weight:700;">
                    <th style="width:40px;">No Cuota</th>
                    <th style="width:120px;">Fecha</th>
                    <th style="width:80px;">Cuota</th>
                    <th style="width:80px;">Capital</th>
                    <th style="width:80px;">Interés</th>
                    <th style="width:80px;">Pagado</th>
                    <th style="width:80px;">Saldo</th>
                    <th style="width:120px;">Fecha Pagada</th>
                    <th style="width:120px;">No Serie Recibo</th>
                    <th style="width:80px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal_cuota = 0.0; $subtotal_capital = 0.0; $subtotal_interes = 0.0; $subtotal_pagado = 0.0; $subtotal_saldo = 0.0;
                if (!empty($rows) && is_array($rows)):
                    foreach ($rows as $r):
                        $payments = (!empty($r['payments']) && is_array($r['payments'])) ? $r['payments'] : array();
                        // Calcular capital/interés estimado (si existen campos, si no, repartir)
                        $capital = isset($r['capital']) ? floatval($r['capital']) : (isset($r['cuota']) ? floatval($r['cuota']) * 0.7 : 0); // 70% capital estimado
                        $interes = isset($r['interes']) ? floatval($r['interes']) : (isset($r['cuota']) ? floatval($r['cuota']) * 0.3 : 0); // 30% interés estimado
                        $subtotal_cuota += isset($r['cuota']) ? floatval($r['cuota']) : 0;
                        $subtotal_capital += $capital;
                        $subtotal_interes += $interes;
                        $subtotal_pagado += isset($r['pagado']) ? floatval($r['pagado']) : 0;
                        $subtotal_saldo += isset($r['saldo']) ? floatval($r['saldo']) : 0;
                        $estado_cuota = isset($r['saldo']) && floatval($r['saldo']) <= 0.01 ? 'Pagada' : 'Pendiente';
                        if (count($payments) > 0) {
                            $firstPay = $payments[0];
                ?>
                    <tr>
                        <td class="text-right"><?php echo htmlspecialchars($r['numero']); ?></td>
                        <td><?php echo htmlspecialchars($r['fecha']); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['cuota']),2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format($capital,2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format($interes,2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['pagado']),2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['saldo']),2); ?></td>
                        <td><?php echo htmlspecialchars(substr((isset($firstPay['fecha_pago'])?$firstPay['fecha_pago']:''),0,10)); ?></td>
                        <td><?php echo htmlspecialchars(isset($firstPay['serie_codigo'])?$firstPay['serie_codigo']:''); ?></td>
                        <td><?php echo $estado_cuota; ?></td>
                    </tr>
                <?php
                            for ($i=1;$i<count($payments);$i++) {
                                $pay = $payments[$i];
                ?>
                    <tr>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        <td><?php echo htmlspecialchars(substr((isset($pay['fecha_pago'])?$pay['fecha_pago']:''),0,10)); ?></td>
                        <td><?php echo htmlspecialchars(isset($pay['serie_codigo'])?$pay['serie_codigo']:''); ?></td>
                        <td></td>
                    </tr>
                <?php
                            }
                        } else {
                ?>
                    <tr>
                        <td class="text-right"><?php echo htmlspecialchars($r['numero']); ?></td>
                        <td><?php echo htmlspecialchars($r['fecha']); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['cuota']),2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format($capital,2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format($interes,2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['pagado']),2); ?></td>
                        <td class="text-right"><?php echo '$'.number_format(floatval($r['saldo']),2); ?></td>
                        <td>-</td><td>-</td><td><?php echo $estado_cuota; ?></td>
                    </tr>
                <?php
                        }
                    endforeach;
                else:
                ?>
                    <tr><td colspan="10" style="text-align:center;">No hay cuotas para este plan.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background:#dbeafe; font-weight:700; color:#1a237e; font-size:11px;">
                    <td colspan="2" style="text-align:right;">SUBTOTALES</td>
                    <td style="text-align:right;"><span style="font-size:12px;"><b><?php echo '$' . number_format($subtotal_cuota,2); ?></b></span></td>
                    <td style="text-align:right;"><span style="font-size:12px;"><b><?php echo '$' . number_format($subtotal_capital,2); ?></b></span></td>
                    <td style="text-align:right;"><span style="font-size:12px;"><b><?php echo '$' . number_format($subtotal_interes,2); ?></b></span></td>
                    <td style="text-align:right;"><span style="font-size:12px;"><b><?php echo '$' . number_format($subtotal_pagado,2); ?></b></span></td>
                    <td style="text-align:right;"><span style="font-size:12px;"><b><?php echo '$' . number_format($subtotal_saldo,2); ?></b></span></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <table class="signature-row">
            <tr>
                <td><div style="border-bottom:2px solid #000; width:190px; margin:46px auto 0 auto; height:1px;"></div></td>
                <td><div style="border-bottom:2px solid #000; width:190px; margin:46px auto 0 auto; height:1px;"></div></td>
                <td><div style="border-bottom:2px solid #000; width:190px; margin:46px auto 0 auto; height:1px;"></div></td>
            </tr>
            <tr>
                <td><div>Firma</div></td>
                <td><div>Nombre del Cliente</div></td>
                <td><div>Cartera</div></td>
            </tr>
        </table>

        <?php
        // --- RESUMEN DEL COMPORTAMIENTO DEL CRÉDITO ---
        $cuotas_totales = is_array($rows) ? count($rows) : 0;
        $cuotas_pagadas = 0;
        $ultimo_pago = null;
        $total_pagado = 0.0;
        $total_saldo = 0.0;
        if (!empty($rows) && is_array($rows)) {
            foreach ($rows as $r) {
                $pagado = isset($r['pagado']) ? floatval($r['pagado']) : 0;
                $saldo = isset($r['saldo']) ? floatval($r['saldo']) : 0;
                $total_pagado += $pagado;
                $total_saldo += $saldo;
                if ($saldo <= 0 && $pagado > 0) $cuotas_pagadas++;
                // Buscar último pago
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
        // Estado actual: replicar lógica de la vista web
        $estado_actual = 'VIGENTE';
        $dias_mora = 0;
        if (isset($rows) && is_array($rows)) {
            foreach ($rows as $row) {
                if (isset($row['saldo']) && floatval($row['saldo']) > 0 && isset($row['fecha'])) {
                    $fecha_venc = $row['fecha'];
                    $fecha_actual = date('Y-m-d');
                    $dias_mora = (strtotime($fecha_actual) - strtotime($fecha_venc)) / 86400;
                    if ($dias_mora < 0) $dias_mora = 0;
                    break;
                }
            }
        }
        if ($dias_mora === 0) {
            $estado_actual = 'VIGENTE';
        } elseif ($dias_mora >= 1 && $dias_mora <= 15) {
            $estado_actual = 'MORA TEMPRANA';
        } elseif ($dias_mora >= 16 && $dias_mora <= 30) {
            $estado_actual = 'MORA';
        } elseif ($dias_mora >= 31 && $dias_mora <= 60) {
            $estado_actual = 'MORA MEDIA';
        } elseif ($dias_mora >= 61 && $dias_mora <= 90) {
            $estado_actual = 'MORA ALTA';
        } elseif ($dias_mora >= 91 && $dias_mora <= 120) {
            $estado_actual = 'CARTERA EN RIESGO';
        } elseif ($dias_mora >= 121 && $dias_mora <= 180) {
            $estado_actual = 'CARTERA DUDOSA';
        } elseif ($dias_mora >= 181 && $dias_mora <= 240) {
            $estado_actual = 'CARTERA CRÍTICA';
        } elseif ($dias_mora >= 241 && $dias_mora <= 360) {
            $estado_actual = 'CARTERA IRRECUPERABLE';
        } elseif ($dias_mora >= 361) {
            $estado_actual = 'CASTIGADO';
        }
        $comentario = '';
        if ($estado_actual === 'VIGENTE') {
            $comentario = 'El crédito se encuentra vigente y al día.';
        } elseif (strpos($estado_actual, 'MORA') !== false) {
            $comentario = 'El crédito presenta cuotas en mora.';
        } elseif (strpos($estado_actual, 'CARTERA') !== false || $estado_actual === 'CASTIGADO') {
            $comentario = 'El crédito presenta alto riesgo o ha sido castigado.';
        } else {
            $comentario = 'El crédito está en estado: ' . $estado_actual;
        }
        ?>
        <div style="margin-top:36px; padding:18px 24px; background:#f8fafd; border-radius:8px; border:1px solid #e0e0e0; max-width:700px; margin-left:auto; margin-right:auto;">
            <h4 style="margin-top:0; color:#1a237e; font-weight:700;">Resumen del comportamiento del crédito</h4>
            <ul style="font-size:12px; color:#222; margin-bottom:10px;">
                <li><b>Monto original:</b> <?php echo isset($prestamo->monto_credito) ? '$'.number_format($prestamo->monto_credito,2) : '-'; ?></li>
                <li><b>Cuotas totales:</b> <?php echo $cuotas_totales; ?> &nbsp; <b>Pagadas:</b> <?php echo $cuotas_pagadas; ?> &nbsp; <b>Pendientes:</b> <?php echo $cuotas_pendientes; ?></li>
                <li><b>Total pagado:</b> <?php echo '$'.number_format($total_pagado,2); ?> &nbsp; <b>Saldo pendiente:</b> <?php echo '$'.number_format($total_saldo,2); ?></li>
                <li><b>Estado actual:</b> <?php echo $estado_actual; ?></li>
                <li><b>Fecha de último pago:</b> <?php echo $ultimo_pago ? date('Y-m-d', strtotime($ultimo_pago)) : '-'; ?></li>
            </ul>
            <div style="font-size:12px; color:#444; margin-top:10px;"><b>Comentario:</b> <?php echo $comentario; ?></div>
        </div>
    </div>
</body>
</html>