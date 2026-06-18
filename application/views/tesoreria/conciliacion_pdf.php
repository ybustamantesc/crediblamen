<?php
$cuenta = isset($cuenta) ? $cuenta : null;
$movimientos = isset($movimientos) && is_array($movimientos) ? $movimientos : array();
$periodo = isset($periodo) ? $periodo : date('Y-m');
$saldo_extracto = isset($saldo_extracto) ? $saldo_extracto : 0;
$saldo_libros = isset($saldo_libros) ? $saldo_libros : 0;
$diferencia = isset($diferencia) ? $diferencia : 0;
$hora_impresion = isset($hora_impresion) ? $hora_impresion : date('Y-m-d H:i:s');
$conciliacion = isset($conciliacion) ? $conciliacion : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Estado de Cuenta Bancario</title>
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
            font-size: 28px;
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
        .conciliacion-info {
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
        .status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status.finalizado {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status.borrador {
            background-color: #fef3c7;
            color: #92400e;
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
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table style="width:100%;"><tr>
                <td style="width:20%; vertical-align: middle;">
                    <?php if (isset($logo_data) && $logo_data): ?>
                        <img src="<?php echo $logo_data; ?>" alt="logo" style="max-height:60px; max-width:160px;" />
                    <?php endif; ?>
                </td>
                <td style="width:60%; text-align:center; vertical-align: middle;">
                    <h1 style="margin:0;">Estado de Cuenta Bancario</h1>
                    <div style="font-size:12px; color:#666;"><?php echo html_escape($cuenta ? ($cuenta['name'] . ' (' . $cuenta['code'] . ')') : '-'); ?> | Periodo <?php echo html_escape($periodo); ?></div>
                </td>
                <td style="width:20%; text-align:right; vertical-align: middle;">
                    <span class="pill">Generado: <?php echo html_escape($hora_impresion); ?></span>
                </td>
            </tr></table>
        </div>

        <div class="conciliacion-info">
            <?php
                    // allow controller to send $logo_data; if not present, prefer an explicit company logo file
                    if (!isset($logo_data) || !$logo_data) {
                        $fallback = FCPATH . 'public/img/sistema/crediblamen_logo.png';
                        if (file_exists($fallback)) {
                            $mime = mime_content_type($fallback);
                            $contents = @file_get_contents($fallback);
                            if ($contents !== false) {
                                $logo_data = 'data:' . $mime . ';base64,' . base64_encode($contents);
                            }
                        }
                    }

                    $currencySymbol = '$';
                    $map = array('USD'=>'$', 'EUR'=>'€', 'NIO'=>'C$', 'DOP'=>'RD$', 'HNL'=>'L.', 'CRC'=>'₡', 'GTQ'=>'Q', 'PAB'=>'B/');
                    if (isset($cuenta['currency']) && $cuenta['currency']) {
                        $c = strtoupper($cuenta['currency']);
                        if (isset($map[$c])) $currencySymbol = $map[$c]; else $currencySymbol = $cuenta['currency'];
                    }
                    function fmtMoney($v, $sym) { $n = number_format(abs($v), 2, '.', ','); if ($v < 0) return '-' . $sym . ' ' . $n; return $sym . ' ' . $n; }
                ?>
            <div class="info-item">
                <label>Saldo inicial</label>
                <value><?php echo fmtMoney($saldo_inicial, $currencySymbol); ?></value>
            </div>
            <div class="info-item">
                <label>Total Débito</label>
                <value><?php echo fmtMoney($total_cargos, $currencySymbol); ?></value>
            </div>
            <div class="info-item">
                <label>Total Crédito</label>
                <value><?php echo fmtMoney($total_abonos, $currencySymbol); ?></value>
            </div>
            <div class="info-item">
                <label>Saldo final</label>
                <value><?php echo fmtMoney($saldo_final, $currencySymbol); ?></value>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="18%">Fecha</th>
                    <th width="55%">Descripción</th>
                    <th width="10%" class="text-right">Débito</th>
                    <th width="10%" class="text-right">Crédito</th>
                    <th width="12%" class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr><td colspan="6" class="text-center">No hay movimientos bancarios para este periodo.</td></tr>
                <?php else: ?>
                    <?php $runningSaldo = $saldo_inicial; ?>
                    <?php $total_debito = 0; $total_credito = 0; ?>
                    <?php foreach ($movimientos as $index => $mov): ?>
                        <?php 
                            $monto = isset($mov['monto_total']) ? floatval($mov['monto_total']) : (isset($mov['monto']) ? floatval($mov['monto']) : 0);
                            $tipo = isset($mov['tipo_transferencia']) ? strtolower($mov['tipo_transferencia']) : '';
                            $debito = 0;
                            $credito = 0;
                            if ($tipo === 'cargo') {
                                $debito = abs($monto);
                                $runningSaldo -= abs($monto);
                                $total_debito += abs($monto);
                            } else {
                                $credito = abs($monto);
                                $runningSaldo += abs($monto);
                                $total_credito += abs($monto);
                            }
                            $fecha = isset($mov['fecha_aplicacion']) && !empty($mov['fecha_aplicacion']) ? $mov['fecha_aplicacion'] : (isset($mov['fecha_registro']) ? $mov['fecha_registro'] : '');
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo html_escape($fecha); ?></td>
                            <td><?php echo html_escape(isset($mov['descripcion']) ? $mov['descripcion'] : (isset($mov['beneficiario']) ? $mov['beneficiario'] : '')); ?></td>
                            <td class="text-right"><?php echo $debito > 0 ? fmtMoney($debito, $currencySymbol) : ''; ?></td>
                            <td class="text-right"><?php echo $credito > 0 ? fmtMoney($credito, $currencySymbol) : ''; ?></td>
                            <td class="text-right"><?php echo fmtMoney($runningSaldo, $currencySymbol); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="3">TOTAL</td>
                        <td class="text-right"><?php echo fmtMoney($total_debito, $currencySymbol); ?></td>
                        <td class="text-right"><?php echo fmtMoney($total_credito, $currencySymbol); ?></td>
                        <td class="text-right"><?php echo fmtMoney($runningSaldo, $currencySymbol); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>


        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
            <table style="width: 100%; font-size: 10px;">
                <tr>
                    <td style="text-align: center; width: 50%; padding-right: 10px;">
                        <div style="height: 40px;"></div>
                        <div style="border-top: 1px solid #333; padding-top: 4px;">
                            <strong>Realizado por</strong><br>
                            <span style="font-size: 9px;">Firma / Iniciales</span>
                        </div>
                        <div style="font-size: 9px; margin-top: 4px; color: #666;">
                            Fecha: _______________
                        </div>
                    </td>
                    <td style="text-align: center; width: 50%; padding-left: 10px;">
                        <div style="height: 40px;"></div>
                        <div style="border-top: 1px solid #333; padding-top: 4px;">
                            <strong>Revisado por</strong><br>
                            <span style="font-size: 9px;">Firma / Iniciales</span>
                        </div>
                        <div style="font-size: 9px; margin-top: 4px; color: #666;">
                            Fecha: _______________
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>


