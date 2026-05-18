<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Reporte de Cierre'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container {
            width: 100%;
            padding: 18px;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 1px solid #1f2937;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 30px;
            margin-bottom: 2px;
            color: #1f2937;
            letter-spacing: .4px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .pill {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #d5dce8;
            border-radius: 999px;
            font-size: 10px;
            color: #334155;
            background: #f8fafc;
            margin-top: 4px;
        }
        .cierre-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 11px;
        }
        .info-item {
            padding: 8px;
            background-color: #f8fafc;
            border: 1px solid #dbe3ef;
            border-radius: 4px;
        }
        .info-item label {
            font-weight: bold;
            display: block;
            color: #333;
            font-size: 10px;
            text-transform: uppercase;
        }
        .info-item value {
            display: block;
            font-size: 13px;
            color: #000;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
        }
        table thead {
            background-color: #1f2937;
            color: white;
        }
        table th {
            padding: 6px;
            text-align: left;
            border: 1px solid #253247;
            font-weight: bold;
        }
        table td {
            padding: 6px;
            border: 1px solid #d7dee9;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .total-row {
            background-color: #eef4fb;
            font-weight: bold;
        }
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .page-break {
            page-break-after: always;
        }
        .serie-head {
            margin: 8px 0 6px;
            padding: 6px 7px;
            border: 1px solid #d8e1ee;
            background: #f8fbff;
            font-size: 10px;
        }
        .serie-title {
            font-weight: bold;
            color: #113a6b;
            margin-bottom: 2px;
        }
        .summary-box {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #d5deea;
            border-radius: 4px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>REPORTE DE CIERRE DE CAJA</h1>
            <p>Cierre #<?php echo htmlspecialchars($cierre->consecutivo); ?> - <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($cierre->fecha_cierre))); ?></p>
            <span class="pill">Documento de control tesoreria</span>
        </div>

        <!-- INFORMACIÓN DEL CIERRE -->
        <div class="cierre-info">
            <div class="info-item">
                <label>Número de Cierre:</label>
                <value>CIERRE #<?php echo htmlspecialchars($cierre->consecutivo); ?></value>
            </div>
            <div class="info-item">
                <label>Fecha de Cierre:</label>
                <value><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($cierre->fecha_cierre))); ?></value>
            </div>
            <div class="info-item">
                <label>Estado:</label>
                <value><span class="badge badge-success"><?php echo strtoupper(htmlspecialchars($cierre->estado)); ?></span></value>
            </div>
            <div class="info-item">
                <label>Usuario:</label>
                <value><?php echo htmlspecialchars(isset($cierre->usuario_ejecutor) ? $cierre->usuario_ejecutor : ($cierre->idusuario ? ('Usuario #' . $cierre->idusuario) : 'N/A')); ?></value>
            </div>
        </div>

        <?php
            $normalizarMoneda = function ($raw) {
                $m = strtoupper(trim((string)$raw));
                if (in_array($m, array('NIO', 'NIO$', 'CS', 'C$', 'CRC', 'CORDOBA', 'CORDOBAS'))) {
                    return 'NIO';
                }
                return 'USD';
            };
            $detectarSerie = function ($pago) {
                $serie = '';
                if (isset($pago->serie_codigo) && trim((string)$pago->serie_codigo) !== '') {
                    $serie = strtoupper(trim((string)$pago->serie_codigo));
                }
                if ($serie === '' && isset($pago->documento_numero)) {
                    $ref = trim((string)$pago->documento_numero);
                    if ($ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $m)) {
                        $serie = strtoupper($m[1]);
                    }
                }
                return $serie !== '' ? $serie : 'SIN SERIE';
            };

            $gruposSerie = array();
            $total_monto = 0;
            $total_recibido = 0;
            $total_usd = 0;
            $total_nio = 0;
            $total_transf_count = 0;
            $total_transf_usd = 0;
            $total_transf_nio = 0;
            $total_metodo_efectivo = 0;
            $total_metodo_cheque = 0;
            $total_metodo_tarjeta = 0;
            $total_metodo_otros = 0;

            if (!empty($pagos)) {
                foreach ($pagos as $pagoTmp) {
                    $serieTmp = $detectarSerie($pagoTmp);
                    if (!isset($gruposSerie[$serieTmp])) {
                        $gruposSerie[$serieTmp] = array(
                            'items' => array(),
                            'subtotal_monto' => 0,
                            'subtotal_recibido' => 0,
                            'subtotal_usd' => 0,
                            'subtotal_nio' => 0,
                            'transf_count' => 0,
                            'transf_usd' => 0,
                            'transf_nio' => 0,
                        );
                    }

                    $montoTmp = isset($pagoTmp->monto) ? floatval($pagoTmp->monto) : 0;
                    $montoRecTmp = isset($pagoTmp->monto_recibido) && $pagoTmp->monto_recibido !== null ? floatval($pagoTmp->monto_recibido) : 0;
                    $monedaTmp = $normalizarMoneda(isset($pagoTmp->moneda) ? $pagoTmp->moneda : 'USD');
                    $medioTmp = isset($pagoTmp->medio_pago) ? strtolower(trim((string)$pagoTmp->medio_pago)) : '';
                    $esTransferTmp = (strpos($medioTmp, 'transfer') !== false);
                    if ($medioTmp === 'efectivo') $total_metodo_efectivo += $montoRecTmp;
                    elseif ($medioTmp === 'cheque') $total_metodo_cheque += $montoRecTmp;
                    elseif ($medioTmp === 'tarjeta') $total_metodo_tarjeta += $montoRecTmp;
                    elseif (!$esTransferTmp) $total_metodo_otros += $montoRecTmp;

                    $gruposSerie[$serieTmp]['items'][] = $pagoTmp;
                    $gruposSerie[$serieTmp]['subtotal_monto'] += $montoTmp;
                    $gruposSerie[$serieTmp]['subtotal_recibido'] += $montoRecTmp;
                    if ($monedaTmp === 'NIO') {
                        $gruposSerie[$serieTmp]['subtotal_nio'] += $montoRecTmp;
                    } else {
                        $gruposSerie[$serieTmp]['subtotal_usd'] += $montoRecTmp;
                    }
                    if ($esTransferTmp) {
                        $gruposSerie[$serieTmp]['transf_count']++;
                        if ($monedaTmp === 'NIO') {
                            $gruposSerie[$serieTmp]['transf_nio'] += $montoRecTmp;
                        } else {
                            $gruposSerie[$serieTmp]['transf_usd'] += $montoRecTmp;
                        }
                    }

                    $total_monto += $montoTmp;
                    $total_recibido += $montoRecTmp;
                    if ($monedaTmp === 'NIO') {
                        $total_nio += $montoRecTmp;
                    } else {
                        $total_usd += $montoRecTmp;
                    }
                    if ($esTransferTmp) {
                        $total_transf_count++;
                        if ($monedaTmp === 'NIO') {
                            $total_transf_nio += $montoRecTmp;
                        } else {
                            $total_transf_usd += $montoRecTmp;
                        }
                    }
                }
                ksort($gruposSerie);
            }
        ?>

        <?php if (empty($gruposSerie)): ?>
            <table>
                <tbody>
                    <tr>
                        <td style="text-align: center; padding: 15px;">No hay pagos registrados en este cierre</td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <?php foreach ($gruposSerie as $serie => $grupo): ?>
                <div class="serie-head">
                    <div class="serie-title">Serie <?php echo htmlspecialchars($serie); ?></div>
                    <div>
                        Registros: <?php echo count($grupo['items']); ?> |
                        Subtotal USD: $<?php echo number_format($grupo['subtotal_usd'], 2); ?> |
                        Subtotal NIO: C$<?php echo number_format($grupo['subtotal_nio'], 2); ?> |
                        Transferencias: <?php echo intval($grupo['transf_count']); ?> (USD $<?php echo number_format($grupo['transf_usd'], 2); ?> / NIO C$<?php echo number_format($grupo['transf_nio'], 2); ?>)
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 16%;">Cliente / Beneficiario</th>
                            <th style="width: 18%;">Concepto</th>
                            <th style="width: 10%;">No Serie Recibo</th>
                            <th style="width: 6%;">Moneda</th>
                            <th style="width: 7%;" class="amount">Monto</th>
                            <th style="width: 7%;" class="amount">Recibido</th>
                            <th style="width: 6%;" class="amount">NIO</th>
                            <th style="width: 8%;" class="amount">Transferencia</th>
                            <th style="width: 7%;">Método</th>
                            <th style="width: 8%;">Fecha Recep.</th>
                            <th style="width: 8%;">Asiento</th>
                            <th style="width: 7%;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grupo['items'] as $pago): ?>
                            <?php
                                $monto = isset($pago->monto) ? floatval($pago->monto) : 0;
                                $monto_recibido = isset($pago->monto_recibido) && $pago->monto_recibido !== null ? floatval($pago->monto_recibido) : 0;
                                $monedaFila = $normalizarMoneda(isset($pago->moneda) ? $pago->moneda : 'USD');
                                $medioFila = isset($pago->medio_pago) ? strtolower(trim((string)$pago->medio_pago)) : '';
                                $esTransferFila = (strpos($medioFila, 'transfer') !== false);
                                $montoNioFila = ($monedaFila === 'NIO') ? $monto_recibido : 0;
                                $montoTransferFila = $esTransferFila ? $monto_recibido : 0;
                                $referenciaFila = '';
                                if (isset($pago->documento_numero) && trim((string)$pago->documento_numero) !== '') {
                                    $referenciaFila = trim((string)$pago->documento_numero);
                                } elseif (isset($pago->referencia) && trim((string)$pago->referencia) !== '') {
                                    $referenciaFila = trim((string)$pago->referencia);
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pago->id); ?></td>
                                <td><?php echo isset($pago->beneficiario) ? htmlspecialchars($pago->beneficiario) : 'N/A'; ?></td>
                                <td><?php echo isset($pago->concepto) ? htmlspecialchars($pago->concepto) : 'N/A'; ?></td>
                                <td><?php echo $referenciaFila !== '' ? htmlspecialchars($referenciaFila) : ''; ?></td>
                                <td><?php echo isset($pago->moneda) ? htmlspecialchars($pago->moneda) : 'USD'; ?></td>
                                <td class="amount">$<?php echo number_format($monto, 2); ?></td>
                                <td class="amount">$<?php echo number_format($monto_recibido, 2); ?></td>
                                <td class="amount"><?php echo $montoNioFila > 0 ? ('C$' . number_format($montoNioFila, 2)) : ''; ?></td>
                                <td class="amount"><?php echo $montoTransferFila > 0 ? (($monedaFila === 'NIO' ? 'C$' : '$') . number_format($montoTransferFila, 2)) : ''; ?></td>
                                <td><?php echo isset($pago->medio_pago) ? htmlspecialchars($pago->medio_pago) : 'N/A'; ?></td>
                                <td>
                                    <?php
                                        if (isset($pago->fecha_recepcion) && !empty($pago->fecha_recepcion)) {
                                            echo htmlspecialchars(date('d/m/Y', strtotime($pago->fecha_recepcion)));
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </td>
                                <td></td>
                                <td>APLICADO</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" style="text-align: right;"><strong>SUBTOTAL SERIE <?php echo htmlspecialchars($serie); ?>:</strong></td>
                            <td class="amount"><strong>$<?php echo number_format($grupo['subtotal_monto'], 2); ?></strong></td>
                            <td class="amount"><strong>$<?php echo number_format($grupo['subtotal_recibido'], 2); ?></strong></td>
                            <td class="amount"><strong><?php echo $grupo['subtotal_nio'] > 0 ? ('C$' . number_format($grupo['subtotal_nio'], 2)) : ''; ?></strong></td>
                            <td class="amount"><strong><?php
                                $subtotalTransferTexto = '';
                                if ($grupo['transf_usd'] > 0 && $grupo['transf_nio'] > 0) {
                                    $subtotalTransferTexto = '$' . number_format($grupo['transf_usd'], 2) . ' | C$' . number_format($grupo['transf_nio'], 2);
                                } elseif ($grupo['transf_usd'] > 0) {
                                    $subtotalTransferTexto = '$' . number_format($grupo['transf_usd'], 2);
                                } elseif ($grupo['transf_nio'] > 0) {
                                    $subtotalTransferTexto = 'C$' . number_format($grupo['transf_nio'], 2);
                                }
                                echo $subtotalTransferTexto;
                            ?></strong></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- RESUMEN FINAL -->
        <div class="summary-box">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                <div>
                    <label style="font-weight: bold;">Total de Pagos:</label>
                    <div><?php echo count($pagos); ?> pagos</div>
                </div>
                <div>
                    <label style="font-weight: bold;">Monto Total Procesado:</label>
                    <div>$<?php echo number_format($total_monto, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Monto Total Recibido:</label>
                    <div>$<?php echo number_format($total_recibido, 2); ?></div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-weight: bold;">Subtotal USD:</label>
                    <div>$<?php echo number_format($total_usd, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Subtotal NIO:</label>
                    <div>C$<?php echo number_format($total_nio, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Transferencias:</label>
                    <div><?php echo intval($total_transf_count); ?> (USD $<?php echo number_format($total_transf_usd, 2); ?> / NIO C$<?php echo number_format($total_transf_nio, 2); ?>)</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-weight: bold;">Efectivo:</label>
                    <div>$<?php echo number_format($total_metodo_efectivo, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Cheque:</label>
                    <div>$<?php echo number_format($total_metodo_cheque, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Tarjeta:</label>
                    <div>$<?php echo number_format($total_metodo_tarjeta, 2); ?></div>
                </div>
                <div>
                    <label style="font-weight: bold;">Otros métodos:</label>
                    <div>$<?php echo number_format($total_metodo_otros, 2); ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($cierre->observaciones)): ?>
            <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; font-size: 10px;">
                <label style="font-weight: bold;">Observaciones:</label>
                <div><?php echo htmlspecialchars($cierre->observaciones); ?></div>
            </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <div class="footer">
            <p>Documento generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>Para consultas, contacte al departamento de tesorería.</p>
        </div>
    </div>
</body>
</html>
