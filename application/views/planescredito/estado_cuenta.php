<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header" style="display:flex; align-items:center; gap:24px; border-bottom:2px solid #e0e0e0; padding-bottom:12px; margin-bottom:24px;">
                <div style="flex:0 0 110px;">
                    <img src="<?php echo base_url('Logo/Logo.png'); ?>" alt="Logo" class="ec-logo" style="max-height:64px; width:auto; object-fit:contain;" onerror="this.onerror=null;this.src='<?php echo base_url('public/img/logo.jpg'); ?>';">
                </div>
                <div style="flex:1;">
                    <h2 style="margin:0; font-weight:700; letter-spacing:1px; color:#222;">Estado de Cuenta</h2>
                    <div style="font-size:18px; color:#666;">Plan N° <?php echo isset($prestamo->idprestamo) ? $prestamo->idprestamo : ''; ?></div>
                </div>
            </div>
            <div class="card" style="box-shadow:0 2px 8px #0001;">
                <div class="card-body">


                        <style>
                        .ec-big-table { width:100%; border-collapse:collapse; font-size:13px; background:#fff; }
                        .ec-big-table th {
                            border:1px solid #b0b0b0; background:#f5f7fa; color:#222; font-weight:700; padding:8px 6px; text-align:center;
                        }
                        .ec-big-table td {
                            border:1px solid #e0e0e0; padding:7px 6px; text-align:right; background:#fff;
                        }
                        .ec-big-table td.text-left { text-align:left; }
                        .ec-big-table tfoot td {
                            background:#f0f4f8; font-weight:700; color:#1a237e; border-top:2px solid #b0b0b0;
                        }
                        .ec-logo { max-height:64px; }
                        .ec-info-table { width:100%; margin-bottom:18px; font-size:14px; }
                        .ec-info-table td.label { font-weight:600; color:#333; width:190px; white-space:nowrap; }
                        .ec-info-table td { padding:3px 8px; }
                        .ec-top-grid {
                            display: grid;
                            grid-template-columns: minmax(260px, 1fr) minmax(290px, 360px) minmax(260px, 1fr);
                            gap: 24px;
                            align-items: start;
                            margin-bottom: 18px;
                        }
                        .ec-top-grid > *:not(:first-child) {
                            border-left: 2px solid #d8e1ee;
                            padding-left: 18px;
                        }
                        .ec-info-mini { width:100%; border-collapse:collapse; font-size:14px; }
                        .ec-info-mini td { padding:4px 6px; vertical-align:top; }
                        .ec-info-mini td.label { font-weight:700; color:#1f2937; width:170px; white-space:nowrap; }
                        .ec-corte-card {
                            border: 1px solid #d8e1ee;
                            border-radius: 12px;
                            background: linear-gradient(180deg, #f8fbff 0%, #f2f7ff 100%);
                            box-shadow: 0 8px 20px rgba(15,23,42,.08);
                            padding: 14px 16px;
                        }
                        .ec-corte-card h5 {
                            margin: 0 0 10px;
                            font-size: 14px;
                            font-weight: 700;
                            color: #1e3a8a;
                            letter-spacing: .2px;
                        }
                        .ec-corte-row {
                            display: flex;
                            justify-content: space-between;
                            gap: 10px;
                            padding: 5px 0;
                            border-bottom: 1px dashed #d4dbe8;
                            font-size: 13px;
                        }
                        .ec-corte-row:last-child { border-bottom: 0; }
                        .ec-corte-label { color:#334155; font-weight:700; }
                        .ec-corte-value { color:#0f172a; font-weight:700; text-align:right; }
                        .ec-corte-total {
                            margin-top: 8px;
                            padding-top: 10px;
                            border-top: 2px solid #c6d2e6;
                        }
                        @media (max-width: 1199.98px) {
                            .ec-top-grid { grid-template-columns: 1fr; }
                            .ec-top-grid > *:not(:first-child) {
                                border-left: 0;
                                border-top: 2px solid #d8e1ee;
                                padding-left: 0;
                                padding-top: 12px;
                            }
                        }
                        .ec-header { margin-bottom:18px; }
                        .btn { border-radius:4px; font-weight:600; }
                        .btn-info { background:#00bcd4; border:none; color:#fff; }
                        .btn-info:hover { background:#0097a7; }
                        .btn-secondary { background:#757575; border:none; color:#fff; }
                        .btn-secondary:hover { background:#424242; }
                        .no-print { margin-right:8px; }
                        .estado-badge { display:inline-block; min-width:110px; padding:4px 8px; border-radius:999px; font-size:11px; font-weight:700; text-align:center; letter-spacing:.2px; }
                        .estado-vigente { background:#e7f6ea; color:#17643a; }
                        .estado-al-dia { background:#e6f4ff; color:#0b5cad; }
                        .estado-mora-temprana { background:#fff4d6; color:#8a5a00; }
                        .estado-mora { background:#ffe6bf; color:#9a4d00; }
                        .estado-mora-media { background:#ffd9b3; color:#994400; }
                        .estado-mora-alta { background:#ffc9c9; color:#9f1d1d; }
                        .estado-riesgo { background:#f7c6d9; color:#8f1655; }
                        .estado-dudosa { background:#e4cdfc; color:#5a2f91; }
                        .estado-critica { background:#d9d0ff; color:#442f8f; }
                        .estado-irrecuperable { background:#d6d6d6; color:#444; }
                        .estado-castigado { background:#2f2f2f; color:#fff; }
                        .estado-anulado { background:#7b7b7b; color:#fff; }
                        .watermark-anulado {
                            position: fixed;
                            top: 48%;
                            left: 50%;
                            transform: translate(-50%, -50%) rotate(-26deg);
                            font-size: 112px;
                            font-weight: 800;
                            letter-spacing: 8px;
                            color: #b71c1c;
                            opacity: 0.12;
                            border: 8px solid #b71c1c;
                            border-radius: 16px;
                            padding: 10px 28px;
                            z-index: 9999;
                            pointer-events: none;
                            white-space: nowrap;
                        }
                        @media print {
                            body { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
                            .no-print { display:none !important; }
                        }
                        </style>

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
                    ?>

                    <?php if ($es_anulado): ?>
                        <div class="watermark-anulado">ANULADO</div>
                    <?php endif; ?>

                    <div class="ec-container">


                        <?php
                            $resumen_tecnico = isset($resumen_tecnico) && is_array($resumen_tecnico) ? $resumen_tecnico : array();
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
                        ?>
                        <div class="ec-top-grid">
                            <div class="ec-corte-card">
                                <h5>Saldos Al Corte</h5>
                                <div class="ec-corte-row"><span class="ec-corte-label">Saldo al corte (Principal)</span><span class="ec-corte-value">$<?php echo number_format($saldo_corte_principal, 2); ?></span></div>
                                <div class="ec-corte-row"><span class="ec-corte-label">Interés corriente</span><span class="ec-corte-value">$<?php echo number_format($interes_corriente_corte, 2); ?></span></div>
                                <div class="ec-corte-row"><span class="ec-corte-label">Interés Moratorio</span><span class="ec-corte-value">$<?php echo number_format($interes_moratorio_corte, 2); ?></span></div>
                                <div class="ec-corte-row"><span class="ec-corte-label">Saldo de Seguro</span><span class="ec-corte-value">$<?php echo number_format($saldo_seguro_corte, 2); ?></span></div>
                                <div class="ec-corte-row"><span class="ec-corte-label">Saldo de Comisión</span><span class="ec-corte-value">$<?php echo number_format($saldo_comision_corte, 2); ?></span></div>
                                <div class="ec-corte-row ec-corte-total"><span class="ec-corte-label">Total</span><span class="ec-corte-value">$<?php echo number_format($total_corte, 2); ?></span></div>
                            </div>

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
                                <tr><td class="label">Estado Actual:</td><td><?php $estado_actual_badge = isset($prestamo->estado_credito) ? $prestamo->estado_credito : 'VIGENTE'; ?><span class="estado-badge <?php echo isset($estado_class_map[$estado_actual_badge]) ? $estado_class_map[$estado_actual_badge] : 'estado-vigente'; ?>"><?php echo htmlspecialchars($estado_actual_badge); ?></span></td></tr>
                            </table>

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
                        </div>

                        <div style="margin-bottom:8px;">
                            <!-- Only show the simple PDF download button -->
                            <a href="<?php echo site_url('planescredito/estado_cuenta_simple_pdf/'.(isset($prestamo->idprestamo)?$prestamo->idprestamo:'')); ?>" class="btn btn-info no-print" target="_blank">Descargar PDF</a>
                            <a href="<?php echo site_url('planescredito'); ?>" class="btn btn-light no-print">Volver</a>
                        </div>

                        <!-- Table area will follow -->
                    
                    <!-- end ec-container wrapper will be closed after table -->
                    </div>


                    <?php
                        // compute column totals for display in PDF/print
                        $sum_cuota = 0.0; $sum_capital = 0.0; $sum_interes = 0.0; $sum_pagado = 0.0; $sum_saldo = 0.0;
                        if (!empty($rows) && is_array($rows)) {
                            foreach ($rows as $rr) {
                                $sum_cuota += isset($rr['cuota']) ? floatval($rr['cuota']) : 0;
                                $sum_capital += isset($rr['principal']) ? floatval($rr['principal']) : (isset($rr['capital']) ? floatval($rr['capital']) : 0);
                                $sum_interes += isset($rr['interes']) ? floatval($rr['interes']) : 0;
                                $sum_pagado += isset($rr['pagado']) ? floatval($rr['pagado']) : 0;
                                $sum_saldo += isset($rr['saldo']) ? floatval($rr['saldo']) : 0;
                            }
                        }
                    ?>

                    <table class="ec-big-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">No Cuota</th>
                                <th style="width:90px;">Fecha</th>
                                <th style="width:80px;">Cuota</th>
                                <th style="width:80px;">Capital</th>
                                <th style="width:80px;">Interés</th>
                                <th style="width:80px;">Pagado</th>
                                <th style="width:80px;">Saldo</th>
                                <th style="width:100px;">Días Mora</th>
                                <th style="width:100px;">Monto Mora</th>
                                <th style="width:100px;">Fecha Pagada</th>
                                <th style="width:110px;">No Serie Recibo</th>
                                <th style="width:120px;">Estado de Pago</th>
                                <th style="width:105px;">Días Transcurridos</th>
                                <th style="width:105px;">Asiento Contable</th>
                                <th style="width:90px;">Módulo</th>
                                <th style="width:95px;">Tipo de Cambio</th>
                                <th style="width:80px;">Seguro</th>
                                <th style="width:90px;">Dispensa</th>
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
                                    <td><?php echo '$'.number_format(floatval($r['cuota']),2); ?></td>
                                    <td><?php echo isset($r['principal']) ? '$'.number_format(floatval($r['principal']),2) : '-'; ?></td>
                                    <td><?php echo isset($r['interes']) ? '$'.number_format(floatval($r['interes']),2) : '-'; ?></td>
                                    <td><?php echo '$'.number_format(floatval($r['pagado']),2); ?></td>
                                    <td><?php echo '$'.number_format(floatval($r['saldo']),2); ?></td>
                                    <td class="text-center">
                                        <span id="dias-mora-display-<?php echo htmlspecialchars($r['idcuota']); ?>">
                                            <?php echo isset($r['dias_mora']) ? intval($r['dias_mora']) : 0; ?>
                                        </span>
                                    </td>
                                    <td class="text-right"><span id="monto-mora-display-<?php echo htmlspecialchars($r['idcuota']); ?>"><?php echo '$'.number_format(floatval(isset($r['monto_mora']) ? $r['monto_mora'] : 0),2); ?></span></td>
                                    <td class="text-left"><?php echo htmlspecialchars(substr((isset($firstPay['fecha_pago'])?$firstPay['fecha_pago']:''),0,10)); ?></td>
                                    <td class="text-left"><?php echo htmlspecialchars(!empty($firstPay['referencia']) ? $firstPay['referencia'] : (isset($firstPay['serie_codigo']) ? $firstPay['serie_codigo'] : '')); ?></td>
                                    <?php $estado_pago = isset($r['estado_pago']) ? $r['estado_pago'] : '-'; ?>
                                    <td class="text-left">
                                        <span class="estado-badge <?php echo isset($estado_class_map[$estado_pago]) ? $estado_class_map[$estado_pago] : 'estado-vigente'; ?>">
                                            <?php echo htmlspecialchars($estado_pago); ?>
                                        </span>
                                    </td>
                                    <td class="text-right"><?php echo intval(isset($r['dias_transcurridos']) ? $r['dias_transcurridos'] : 0); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars(isset($r['asiento_contable']) && $r['asiento_contable'] !== '' ? $r['asiento_contable'] : '-'); ?></td>
                                    <td class="text-left"><?php echo htmlspecialchars(isset($r['modulo']) ? $r['modulo'] : '0.00'); ?></td>
                                    <td class="text-right"><?php echo number_format(floatval(isset($r['tipo_cambio']) ? $r['tipo_cambio'] : 36.6243), 4); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars(isset($r['seguro']) ? $r['seguro'] : '0.00'); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars(isset($r['dispensa']) ? $r['dispensa'] : '0.00'); ?></td>
                                </tr>
                                <?php
                                        for ($i=1;$i<count($payments);$i++) {
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
                                    <td class="text-left"><?php echo htmlspecialchars(substr((isset($pay['fecha_pago'])?$pay['fecha_pago']:''),0,10)); ?></td>
                                    <td class="text-left"><?php echo htmlspecialchars(!empty($pay['referencia']) ? $pay['referencia'] : (isset($pay['serie_codigo']) ? $pay['serie_codigo'] : '')); ?></td>
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
                                    <td class="text-right"><?php echo '$'.number_format(floatval($r['cuota']),2); ?></td>
                                    <td class="text-right"><?php echo isset($r['principal']) ? '$'.number_format(floatval($r['principal']),2) : '-'; ?></td>
                                    <td class="text-right"><?php echo isset($r['interes']) ? '$'.number_format(floatval($r['interes']),2) : '-'; ?></td>
                                    <td class="text-right"><?php echo '$'.number_format(floatval($r['pagado']),2); ?></td>
                                    <td class="text-right"><?php echo '$'.number_format(floatval($r['saldo']),2); ?></td>
                                    <td class="text-center">
                                        <span id="dias-mora-display-<?php echo htmlspecialchars($r['idcuota']); ?>">
                                            <?php echo isset($r['dias_mora']) ? intval($r['dias_mora']) : 0; ?>
                                        </span>
                                    </td>
                                    <td class="text-right"><span id="monto-mora-display-<?php echo htmlspecialchars($r['idcuota']); ?>"><?php echo '$'.number_format(floatval(isset($r['monto_mora']) ? $r['monto_mora'] : 0),2); ?></span></td>
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
                                    <td class="text-left"><?php echo htmlspecialchars(isset($r['modulo']) ? $r['modulo'] : '0.00'); ?></td>
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
                            <tr style="background:#f8f8f8; font-weight:700;">
                                <td colspan="2" class="text-right">Totales</td>
                                <td><?php echo '$' . number_format($sum_cuota,2); ?></td>
                                <td><?php echo '$' . number_format($sum_capital,2); ?></td>
                                <td><?php echo '$' . number_format($sum_interes,2); ?></td>
                                <td><?php echo '$' . number_format($sum_pagado,2); ?></td>
                                <td><?php echo '$' . number_format($sum_saldo,2); ?></td>
                                <td colspan="11"></td>
                            </tr>
                        </tfoot>
                    </table>



                    <div style="margin-top:32px; display:flex; gap:24px;">
                        <div style="flex:1; text-align:center;">
                            <div style="border-top:2px solid #222; width:220px; margin:0 auto;"></div>
                            <div style="margin-top:8px; font-size:14px; color:#333;">Cobrador / Firma</div>
                        </div>
                        <div style="flex:1; text-align:center;">
                            <div style="border-top:2px solid #222; width:220px; margin:0 auto;"></div>
                            <div style="margin-top:8px; font-size:14px; color:#333;">Cartera</div>
                        </div>
                        <div style="flex:1; text-align:center;">
                            <div style="border-top:2px solid #222; width:220px; margin:0 auto;"></div>
                            <div style="margin-top:8px; font-size:14px; color:#333;">Cliente</div>
                        </div>
                    </div>

                    <?php
                    // --- RESUMEN DEL COMPORTAMIENTO DEL CRÉDITO ---
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
                    $estado_actual = isset($prestamo->estado_credito) ? $prestamo->estado_credito : '';
                    $cuota_actual_texto = 'Crédito sin cuotas pendientes.';
                    if ($cuota_actual !== null) {
                        $numero_cuota_actual = isset($cuota_actual['numero']) ? $cuota_actual['numero'] : '-';
                        $fecha_cuota_actual = isset($cuota_actual['fecha']) ? $cuota_actual['fecha'] : '-';
                        $cuota_actual_texto = 'Cuota #' . $numero_cuota_actual . ' con vencimiento ' . $fecha_cuota_actual . '.';
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
                    ?>
                    <div style="margin-top:36px; padding:20px 26px; background:linear-gradient(180deg,#f8fbff 0%,#f3f7fd 100%); border-radius:12px; border:1px solid #dbe5f1; max-width:980px; margin-left:auto; margin-right:auto; box-shadow:0 8px 20px rgba(15,23,42,.06);">
                        <h4 style="margin-top:0; color:#1a237e; font-weight:700; letter-spacing:.2px;">Resumen Ejecutivo del Crédito</h4>
                        <ul style="font-size:15px; color:#222; margin-bottom:10px;">
                            <li><b>Monto original:</b> <?php echo isset($prestamo->monto_credito) ? '$'.number_format($prestamo->monto_credito,2) : '-'; ?></li>
                            <li><b>Cuotas totales:</b> <?php echo $cuotas_totales; ?> &nbsp; <b>Pagadas:</b> <?php echo $cuotas_pagadas; ?> &nbsp; <b>Pendientes:</b> <?php echo $cuotas_pendientes; ?></li>
                            <li><b>Total pagado:</b> <?php echo '$'.number_format($total_pagado,2); ?> &nbsp; <b>Saldo pendiente:</b> <?php echo '$'.number_format($total_saldo,2); ?></li>
                            <li><b>Estado actual:</b> <?php echo $estado_actual; ?></li>
                            <li><b>Cuota actual:</b> <?php echo htmlspecialchars($cuota_actual_texto); ?></li>
                            <li><b>Último pago:</b> <?php echo $ultimo_pago ? substr($ultimo_pago,0,10) : 'N/A'; ?></li>
                        </ul>
                        <div style="font-size:15px; color:#444; margin-top:10px;"><b>Comentario:</b> <?php echo $comentario; ?></div>
                    </div>

                    <div style="margin-top:12px;">
                        <a href="<?php echo site_url('planescredito/estado_cuenta_simple_pdf/'.(isset($prestamo->idprestamo)?$prestamo->idprestamo:'')); ?>" class="btn btn-info" target="_blank">Descargar PDF</a>
                        <a href="<?php echo site_url('planescredito'); ?>" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
