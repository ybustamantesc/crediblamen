<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $titulo; ?></title>
    <style>
        @page { size: letter portrait; margin: 10mm; }
        html,body { height:100%; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:10px; color:#222; margin:0; padding:0; }
        .container { width:100%; max-width:780px; margin:0 auto; padding:6px; }
        header { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
        .company { text-align:left; }
        .title { text-align:center; font-weight:700; font-size:13px; }
        .meta { text-align:right; font-size:10px; }
        .info-table, .summary-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .info-table td { padding:3px 6px; vertical-align:top; }
        .info-table .label { font-weight:700; width:160px; background:#f7f7f7; }
        .big-table { width:100%; border-collapse:collapse; font-size:9px; }
        .big-table th, .big-table td { border:1px solid #ddd; padding:5px 4px; }
        .text-right { text-align:right; }
        .small { font-size:10px; color:#444; }
        .logo { max-height:72px; }
        .watermark-anulado {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-28deg);
            font-size: 110px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #b71c1c;
            opacity: 0.14;
            border: 7px solid #b71c1c;
            border-radius: 16px;
            padding: 12px 30px;
            z-index: 999;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <?php
        $es_anulado = !empty($prestamo->es_anulado);
        if (!$es_anulado && isset($prestamo->estado_credito) && strtoupper(trim((string)$prestamo->estado_credito)) === 'ANULADO') {
            $es_anulado = true;
        } elseif (!$es_anulado && isset($prestamo->estado) && intval($prestamo->estado) === 2) {
            $es_anulado = true;
        } elseif (!$es_anulado && isset($prestamo->estado_aprobacion) && strtolower(trim((string)$prestamo->estado_aprobacion)) === 'anulado') {
            $es_anulado = true;
        }
    ?>
    <?php if ($es_anulado): ?>
        <div class="watermark-anulado">ANULADO</div>
    <?php endif; ?>
    <div class="container">
        <?php $generated_at = date('d/m/Y'); $tca = null; $tcm = null; ?>
        <header>
            <div class="company">
                <!-- Company name and address hidden as requested -->
            </div>
            <div class="title">PLAN DE PAGOS</div>
            <div class="meta">
                <?php if (file_exists(FCPATH.'public/img/logo.jpg')): $logo_path = 'file://'.str_replace('\\','/',FCPATH.'public/img/logo.jpg'); ?>
                    <img src="<?php echo $logo_path; ?>" class="logo" />
                <?php endif; ?>
            </div>
        </header>

        <table class="info-table">
            <tr>
                <td class="label">Nombre del Cliente:</td>
                <td><?php echo isset($prestamo->cliente_nombre) ? html_escape($prestamo->cliente_nombre) : (isset($prestamo->cliente) ? html_escape($prestamo->cliente) : ''); ?></td>
                <td class="label">ID Cliente</td>
                <td><?php echo isset($prestamo->idcliente) ? $prestamo->idcliente : (isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : ''); ?></td>
            </tr>
            <tr>
                <td class="label">Doc de identidad:</td>
                <td><?php echo isset($prestamo->doc_identidad) ? html_escape($prestamo->doc_identidad) : ''; ?></td>
                <td class="label">Solicitud</td>
                <td><?php echo isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : ''; ?></td>
            </tr>
            <tr>
                <td class="label">Nombre de Producto:</td>
                <td><?php echo isset($prestamo->producto_nombre) ? html_escape($prestamo->producto_nombre) : ''; ?></td>
                <td class="label">Cobrador:</td>
                <td><?php echo isset($prestamo->cobrador) ? html_escape($prestamo->cobrador) : ''; ?></td>
            </tr>
        </table>

        <!-- TCA/TCM summary will be rendered after XIRR computation below -->

        <?php
            // Compute TCA (annual) via XIRR using desembolso neto and cuotas, then TCM (monthly)
            function xirr_calc($amounts, $dates, $guess = 0.0) {
                $fmt = function($d){
                    if (is_numeric($d)) {
                        return (int) round(($d - 25569) * 86400.0);
                    }
                    if (is_string($d) && preg_match('#^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$#', trim($d))) {
                        $clean = trim($d);
                        $dt = DateTime::createFromFormat('d/m/Y', $clean);
                        if ($dt) return $dt->getTimestamp();
                        $dt = DateTime::createFromFormat('d-m-Y', $clean);
                        if ($dt) return $dt->getTimestamp();
                    }
                    try { return (new DateTime($d))->getTimestamp(); } catch (Exception $e) { return strtotime($d); }
                };

                $ts = array_map($fmt, $dates);
                $days = array_map(function($t) use ($ts){ return ($t - $ts[0]) / 86400.0; }, $ts);

                $f = function($r) use ($amounts, $days) {
                    $res = 0.0; $year = 365.0;
                    foreach ($amounts as $i => $a) {
                        $den = pow(1 + $r, $days[$i] / $year);
                        if (!is_finite($den) || $den == 0) return NAN;
                        $res += $a / $den;
                    }
                    return $res;
                };

                $df = function($r) use ($amounts, $days) {
                    $res = 0.0; $year = 365.0;
                    foreach ($amounts as $i => $a) {
                        $t = $days[$i] / $year;
                        $den = pow(1 + $r, $t + 1);
                        if (!is_finite($den) || $den == 0) return NAN;
                        $res += -$a * $t / $den;
                    }
                    return $res;
                };

                // Try multiple initial guesses (improves convergence)
                $guesses = array($guess, 0.0, 0.01, 0.1, -0.1, 0.5, 1.0);
                foreach ($guesses as $g) {
                    $rate = $g;
                    for ($i=0; $i<200; $i++) {
                        $fv = $f($rate);
                        $dfv = $df($rate);
                        if (!is_finite($fv) || !is_finite($dfv)) break;
                        if (abs($dfv) < 1e-14) break;
                        $new = $rate - $fv / $dfv;
                        if (!is_finite($new)) break;
                        if (abs($new - $rate) < 1e-12) { $rate = $new; break; }
                        $rate = $new;
                    }
                    if (is_finite($rate) && $rate > -0.999999) return $rate;
                }

                // Fallback bisection
                $min = -0.999999; $max = 10.0; $fa = $f($min); $fb = $f($max);
                if (!is_finite($fa) || !is_finite($fb)) return NAN;
                if ($fa * $fb > 0) {
                    $found = false; $step = 0.1;
                    for ($a = -0.9999; $a < 50; $a += $step) {
                        $b = $a + $step; $fa = $f($a); $fb = $f($b);
                        if (!is_finite($fa) || !is_finite($fb)) continue;
                        if ($fa * $fb <= 0) { $min = $a; $max = $b; $found = true; break; }
                    }
                    if (!$found) return NAN;
                }
                $a = $min; $b = $max; $fa = $f($a); $fb = $f($b);
                if (!is_finite($fa) || !is_finite($fb) || $fa * $fb > 0) return NAN;
                for ($i=0; $i<200; $i++) {
                    $m = ($a + $b) / 2.0; $fm = $f($m);
                    if (!is_finite($fm)) break;
                    if (abs($fm) < 1e-12) return $m;
                    if ($fa * $fm < 0) { $b = $m; $fb = $fm; } else { $a = $m; $fa = $fm; }
                    if (abs($b - $a) < 1e-12) return ($a + $b) / 2.0;
                }
                return NAN;
            }

            $tca = null; $tcm = null;
            try {
                $p_id = isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : (isset($prestamo->idprestamo) ? $prestamo->idprestamo : (isset($prestamo->idcliente) ? $prestamo->idcliente : 'unknown'));
                if (isset($prestamo->monto_credito) && isset($cuotas) && is_array($cuotas) && count($cuotas)) {
                    $amounts = array(); $dates = array();
                    // Build cashflows using the Excel convention seen in the workbook:
                    // first flow: disbursement as NEGATIVE (outflow), subsequent flows: positive payments
                    $disb_date = isset($prestamo->fecha_credito) ? $prestamo->fecha_credito : (isset($cuotas[0]->fecha_vencimiento) ? $cuotas[0]->fecha_vencimiento : date('Y-m-d'));
                    $amounts[] = -1.0 * floatval($prestamo->monto_credito);
                    $dates[] = $disb_date;
                    foreach ($cuotas as $c) {
                        $amounts[] = floatval($c->cuota);
                        $dates[] = isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : $disb_date;
                    }
                    // Debug: optionally render flows to the PDF when debug_xirr=1 is present in the URL
                    $p_id = isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : (isset($prestamo->idcliente) ? $prestamo->idcliente : 'unknown');
                    // (debug_xirr output removed from PDF to avoid rendering issues)
                    // Log flows for debugging to app logs as well
                    if (function_exists('log_message')) {
                        log_message('info', "planescredito xirr flows for {$p_id}: amounts=" . json_encode($amounts) . " dates=" . json_encode($dates));
                    }
                    // compute xirr (annual) using initial guess 0 to match Excel's =XIRR(...,0)
                    $x = xirr_calc($amounts, $dates, 0.0);

                    // (always-on debug block removed from PDF output)

                    // accept only finite numeric results
                    if (is_numeric($x) && is_finite($x) && $x > -0.999999) {
                        $tca = $x; // annual
                        $tcm = pow(1 + $tca, 1/12.0) - 1;
                    } else {
                        $tca = null; $tcm = null;
                        if (function_exists('log_message')) {
                            log_message('error', "planescredito xirr failed for {$p_id}: result=" . var_export($x, true) . ' amounts=' . json_encode($amounts) . ' dates=' . json_encode($dates));
                        }
                    }
                }
            } catch (Exception $e) { $tca = null; $tcm = null; }
        ?>

        <!-- Render TCA / TCM summary using computed values -->
        <table class="summary-table" style="margin-top:6px;">
            <tr>
                <td class="label">TCA (Tasa Costo Anual - XIRR):</td>
                <td><?php echo (isset($tca) && $tca !== null) ? number_format($tca * 100, 2) . '%' : 'N/A'; ?></td>
                <td class="label">TCM (Tasa Costo Mensual):</td>
                <td><?php echo (isset($tcm) && $tcm !== null) ? number_format($tcm * 100, 2) . '%' : 'N/A'; ?></td>
            </tr>
        </table>

        <?php
            // Normalize interest values and compute annual percent based on payment frequency.
            $ic_raw = isset($prestamo->interes_credito) ? floatval($prestamo->interes_credito) : 0.0;
            $freq = isset($prestamo->frecuencia) ? strtolower(trim($prestamo->frecuencia)) : 'mensual';
            switch ($freq) {
                case 'semanal': case 'weekly': $mult = 52; break;
                case 'quincenal': case 'quincena': $mult = 24; break;
                case 'bimestral': $mult = 6; break;
                case 'trimestral': case 'trimestralmente': $mult = 4; break;
                case 'anual': case 'anualmente': $mult = 1; break;
                case 'mensual': default: $mult = 12; break;
            }
            // Determine annual percent value (as percentage, e.g., 96.00 means 96%).
            if ($ic_raw > 1.0) {
                // Value stored as percent per period
                $annual_percent = $ic_raw * $mult;
            } else {
                // Value stored as decimal per period (e.g., 0.08 for 8%)
                $annual_percent = $ic_raw * $mult * 100.0;
            }
            $monthly_percent = $annual_percent / 12.0;

            // Interes moratorio: prefer explicit field; otherwise use annual/4 as requested.
            if (isset($prestamo->interes_moratorio) && $prestamo->interes_moratorio !== '') {
                $im_raw = floatval($prestamo->interes_moratorio);
                $interes_moratorio_percent = ($im_raw > 1.0) ? $im_raw : ($im_raw * 100.0);
            } else {
                $interes_moratorio_percent = $annual_percent / 4.0;
            }

            $monthly_display = number_format($monthly_percent, 2) . '%';
            $annual_display = number_format($annual_percent, 2) . '%';
            $interes_moratorio_display = number_format($interes_moratorio_percent, 2) . '%';
        ?>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:6px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><td class="label">Interes corriente mensual:</td><td><?php echo isset($prestamo->interes_credito) ? ((floatval($prestamo->interes_credito) > 1) ? number_format($prestamo->interes_credito,2) .'%' : number_format($prestamo->interes_credito * 100,2) .'%') : ''; ?></td></tr>
                        <tr><td class="label">Interes corriente anual:</td><td><?php echo isset($prestamo->interes_credito) ? number_format((floatval($prestamo->interes_credito) * 12) * 100,2) . '%' : ''; ?></td></tr>
                        <tr><td class="label">Interes moratorio:</td><td><?php echo $interes_moratorio_display; ?></td></tr>
                        <tr><td class="label">Plazo en meses:</td><td><?php echo isset($prestamo->numero_coutas) ? $prestamo->numero_coutas : ''; ?></td></tr>
                        <tr><td class="label">Frecuencia de pago:</td><td><?php echo isset($prestamo->frecuencia) ? html_escape(ucfirst($prestamo->frecuencia)) : 'Mensual'; ?></td></tr>
                        <tr><td class="label">Cuota:</td><td><?php echo isset($cuotas[0]) ? ('$' . number_format($cuotas[0]->cuota,2)) : ''; ?></td></tr>
                        <tr><td class="label">% comisión por desembolso</td><td><?php
                            $cd = isset($prestamo->comision_desembolso) ? floatval($prestamo->comision_desembolso) : 0;
                            $cd_display = ($cd > 1) ? $cd : ($cd * 100);
                            echo number_format($cd_display,2) . '%';
                        ?></td></tr>
                    </table>
                </td>
                <td style="width:50%; vertical-align:top; padding-left:6px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><td class="label">Fecha de desembolso</td><td><?php echo isset($prestamo->fecha_credito) ? $prestamo->fecha_credito : ''; ?></td></tr>
                        <tr><td class="label">1er día de pago</td><td><?php echo isset($cuotas[0]) ? (isset($cuotas[0]->fecha_vencimiento) ? $cuotas[0]->fecha_vencimiento : '') : ''; ?></td></tr>
                        <tr><td class="label">Monto</td><td><?php echo isset($prestamo->monto_credito) ? ('$' . number_format($prestamo->monto_credito,2)) : ''; ?></td></tr>
                        <tr><td class="label">Saldo Inicial</td><td><?php echo isset($prestamo->monto_credito) ? ('$' . number_format($prestamo->monto_credito,2)) : ''; ?></td></tr>
                        <tr><td class="label">Otros cargos</td><td><?php echo isset($prestamo->otros_cargos) ? html_escape($prestamo->otros_cargos) : 'Aplica'; ?></td></tr>
                        <tr><td class="label">Cantidad de Cuotas</td><td><?php echo isset($count_cuotas) ? intval($count_cuotas) : (is_array($cuotas) ? count($cuotas) : 0); ?></td></tr>
                        <tr><td class="label">Suma de Cuotas</td><td><?php echo isset($sum_cuotas) ? ('$' . number_format($sum_cuotas,2)) : (isset($sum_cuota) ? ('$' . number_format($sum_cuota,2)) : ''); ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="big-table">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="width:30px">N°</th>
                    <th style="width:90px">Fecha</th>
                    <th style="width:50px">N° dias</th>
                    <th style="width:90px">Principal</th>
                    <th style="width:90px">Interés</th>
                    <th style="width:110px">Comisión por desembolso</th>
                    <th style="width:90px">Cuota</th>
                    <th style="width:110px">Saldo Capital</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $prev_date = null;
                    if (isset($prestamo->fecha_credito) && $prestamo->fecha_credito) $prev_date = $prestamo->fecha_credito;
                    $sum_principal = 0; $sum_interes = 0; $sum_comision = 0; $sum_cuota = 0;
                ?>
                <?php if (is_array($cuotas) && count($cuotas)): foreach ($cuotas as $c):
                    $fecha = isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : '';
                    $dias = '';
                    try{
                        if ($prev_date && $fecha) {
                            $d1 = new DateTime($prev_date);
                            $d2 = new DateTime($fecha);
                            $interval = $d1->diff($d2);
                            $dias = $interval->days;
                        }
                    }catch(Exception $e){ $dias = ''; }
                    $sum_principal += floatval($c->principal);
                    $sum_interes += floatval($c->interes);
                    $sum_comision += floatval($c->comision);
                    $sum_cuota += floatval($c->cuota);
                ?>
                <tr>
                    <td class="text-right"><?php echo $c->numero; ?></td>
                    <td><?php echo $fecha; ?></td>
                    <td class="text-right"><?php echo $dias; ?></td>
                    <td class="text-right"><?php echo number_format($c->principal,2); ?></td>
                    <td class="text-right"><?php echo number_format($c->interes,2); ?></td>
                    <td class="text-right"><?php echo number_format($c->comision,2); ?></td>
                    <td class="text-right"><?php echo number_format($c->cuota,2); ?></td>
                    <td class="text-right"><?php echo number_format($c->saldo,2); ?></td>
                </tr>
                <?php $prev_date = $fecha; endforeach; else: ?>
                <tr><td colspan="8" class="text-center">No hay cuotas registradas</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background:#f8f8f8; font-weight:700;">
                    <td colspan="3" class="text-right">Totales</td>
                    <td class="text-right"><?php echo '$' . number_format($sum_principal,2); ?></td>
                    <td class="text-right"><?php echo '$' . number_format($sum_interes,2); ?></td>
                    <td class="text-right"><?php echo '$' . number_format($sum_comision,2); ?></td>
                    <td class="text-right"><?php echo '$' . number_format($sum_cuota,2); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Tabla de totales elegante: números arriba, etiquetas abajo en tabla -->
        <div style="margin-top:12px; width:100%;">
            <style>
                .totals-table { width:70%; margin:0 auto 18px auto; border-collapse:collapse; }
                .totals-table td { vertical-align:middle; padding:8px 12px; }
                /* Reduced totals font-size so amounts don't appear too large on the PDF */
                .totals-value { font-size:14px; font-weight:700; color:#111; text-align:center; }
                .totals-label { font-size:11px; color:#555; text-align:center; padding-top:6px; }
                .signature-row { width:100%; margin-top:22px; display:flex; justify-content:space-between; }
                .signature-box { width:23%; text-align:center; }
                .signature-line { border-bottom:1px solid #000; height:1px; margin-top:36px; }
                /* Make signature labels bold for clarity */
                .signature-label { font-size:12px; color:#333; margin-top:6px; font-weight:700; }
            </style>

            <table class="totals-table">
                <tr>
                    <td style="width:100%;">
                        <table style="width:80%; margin:0 auto; border-collapse:collapse;">
                            <tr>
                                <td style="width:25%; text-align:center;">
                                    <div class="totals-value"><?php echo '$' . number_format($sum_principal,2); ?></div>
                                </td>
                                <td style="width:25%; text-align:center;">
                                    <div class="totals-value"><?php echo '$' . number_format($sum_interes,2); ?></div>
                                </td>
                                <td style="width:25%; text-align:center;">
                                    <div class="totals-value"><?php echo '$' . number_format($sum_comision,2); ?></div>
                                </td>
                                <td style="width:25%; text-align:center;">
                                    <div class="totals-value"><?php echo '$' . number_format($sum_cuota,2); ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center;"><div class="totals-label">Total Principal</div></td>
                                <td style="text-align:center;"><div class="totals-label">Total Interés</div></td>
                                <td style="text-align:center;"><div class="totals-label">Total Comisión</div></td>
                                <td style="text-align:center;"><div class="totals-label">Total Cuotas</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Espacio de 2cm antes de las firmas -->
            <div style="height:20mm;"></div>

            <!-- Firmas: 4 casillas en una sola fila (tabla) -->
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:25%; text-align:center; vertical-align:bottom;">
                        <div style="border-bottom:1px solid #000; margin:0 12px 6px 12px; height:1px;"></div>
                        <div class="signature-label">Firma</div>
                    </td>
                    <td style="width:25%; text-align:center; vertical-align:bottom;">
                        <div style="border-bottom:1px solid #000; margin:0 12px 6px 12px; height:1px;"></div>
                        <div class="signature-label">Nombre del cliente</div>
                    </td>
                    <td style="width:25%; text-align:center; vertical-align:bottom;">
                        <div style="border-bottom:1px solid #000; margin:0 12px 6px 12px; height:1px;"></div>
                        <div class="signature-label">Firma</div>
                    </td>
                    <td style="width:25%; text-align:center; vertical-align:bottom;">
                        <div style="border-bottom:1px solid #000; margin:0 12px 6px 12px; height:1px;"></div>
                        <div class="signature-label">Nombre del fiador</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
