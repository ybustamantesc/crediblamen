
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de pago</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 30px 40px 30px 40px;
        }
    body { font-family: Arial, sans-serif; color:#000; margin:0; background: #fff; font-size:12px; }
    .main-title { font-size: 22px; font-weight: bold; color: #000; text-align: center; margin-top: 8px; margin-bottom: 2px; letter-spacing: 1px; }
    .sub-title { font-size: 16px; color: #000; text-align: center; margin-bottom: 8px; letter-spacing: 1px; }
        /* make header span most of the page so values sit near page edges */
    .header-table { width: 100%; max-width: 760px; margin: 0 auto 12px auto; table-layout: fixed; }
    .header-table td, .header-table th { border: none; padding: 8px 6px; font-size: 12px; vertical-align: top; }
    .header-table th { background: #fff; color: #000; font-weight: bold; }
    /* labels right, values black; widths tuned so left group hugs left and right group hugs right with extra gap between groups */
    .header-table td.label { background: #fff; color: #000; font-weight: bold; text-align: right; width: 15%; padding-right: 12px; }
    .header-table td.value { background: #fff; color: #000; width: 35%; padding-left: 12px; }
        .header-table td.value.left { text-align: left; }
        .header-table td.value.right { text-align: right; }
        /* Underline style for values outside tables */
    .header-table td.value .underline { display: inline-block; border-bottom: 1px solid #cfcfcf; padding-bottom: 2px; width: 100%; color: #000; }
        .header-table td.value .underline.left { text-align: left; }
        .header-table td.value .underline.right { text-align: right; }
        .header-table tr { line-height: 1.08; }
    .plan-table { width: 95%; max-width: 760px; margin: 8px auto 10px auto; border-collapse: collapse; }
    /* Only headers keep visible borders; body cells are borderless for a clean look */
    .plan-table th { border: 1px solid #b5d3e7; padding: 6px 6px; font-size: 12px; background: #fff; color: #000; font-weight: bold; text-align: center; }
    .plan-table td { border: none; padding: 6px 6px; font-size: 12px; color: #000; text-align: center; }
        .plan-table td.left { text-align: left; }
        .totals-table { width: 70%; max-width: 500px; margin: 12px auto 0 auto; border-collapse: collapse; }
    /* totals: no borders, keep header bold and centered */
    .totals-table th, .totals-table td { border: none; padding: 6px 6px; font-size: 12px; }
        .totals-table th { background: #fff; color: #000; font-weight: bold; text-align: center; }
        .totals-table td { background: #fff; color: #000; text-align: center; }
    /* Thick black separator used between header and table (removed in markup when not needed) */
    </style>
</head>
<body>
    <div class="main-title">CALENDARIO DE PAGO</div>
    <div class="sub-title">Propuesta Plan de Pagos</div>
    <table class="header-table" style="width: 95%; margin: 0 auto;">
            <tr>
                <td class="label">Nombre del Cliente:</td>
                <td class="value left" colspan="3"><span class="underline left"><?php echo $prestamo->apellidos . ' ' . $prestamo->nombres; ?></span></td>
            </tr>
            <tr>
                <td class="label">Doc de identidad:</td>
                <td class="value left"><span class="underline left"><?php echo isset($prestamo->numero_doc) ? $prestamo->numero_doc : ''; ?></span></td>
                <td class="label">ID Cliente:</td>
                <td class="value right"><span class="underline right"><?php echo $prestamo->idcliente; ?></span></td>
            </tr>
            <tr>
                <td class="label">Tipo de Producto:</td>
                <td class="value left"><span class="underline left"><?php echo isset($prestamo->tipo_producto) ? $prestamo->tipo_producto : 'Microcrédito'; ?></span></td>
                <td class="label">Promotor:</td>
                    <td class="value right"><span class="underline right"><?php echo isset($prestamo->nombre_cobrador) ? $prestamo->nombre_cobrador : ''; ?></span></td>
            </tr>
            <tr>
                <td class="label">Interés mensual:</td>
                <td class="value left"><span class="underline left"><?php echo isset($prestamo->interes_mensual) ? $prestamo->interes_mensual . '%' : ''; ?></span></td>
                <td class="label">Interés anual:</td>
                <td class="value right"><span class="underline right"><?php echo isset($prestamo->interes_anual) ? $prestamo->interes_anual . '%' : ''; ?></span></td>
            </tr>
            <tr>
                <td class="label">Interés moratorio:</td>
                <td class="value left"><span class="underline left"><?php echo isset($prestamo->interes_moratorio) ? $prestamo->interes_moratorio . '%' : ''; ?></span></td>
                <td class="label">Plazo (meses):</td>
                <td class="value right"><span class="underline right"><?php echo isset($prestamo->plazo_meses) ? $prestamo->plazo_meses : ''; ?></span></td>
            </tr>
            <tr>
                <td class="label">Fecha desembolso:</td>
                    <td class="value left"><span class="underline left"><?php echo isset($prestamo->fecha_credito) ? formatoFechaCorta($prestamo->fecha_credito) : ''; ?></span></td>
                <td class="label">1er día de pago:</td>
                <td class="value right"><span class="underline right"><?php echo isset($prestamo->fecha_credito) ? formatoFechaCorta($prestamo->fecha_credito) : ''; ?></span></td>
            </tr>
            <tr>
                <td class="label">Saldo Inicial:</td>
                <td class="value left"><span class="underline left">$ <?php echo isset($prestamo->monto_credito) ? number_format($prestamo->monto_credito,2) : ''; ?></span></td>
                <td class="label">Frecuencia de pago:</td>
                <td class="value right"><span class="underline right"><?php echo isset($prestamo->forma_pago) ? $prestamo->forma_pago : ''; ?></span></td>
            </tr>
            <tr>
                <td class="label">Cuota:</td>
                <td class="value left"><span class="underline left">$ <?php echo isset($cuotas[0]) ? number_format($cuotas[0]->monto_couta,2) : '0.00'; ?></span></td>
                <td class="label">Otros cargos:</td>
                <td class="value right"><span class="underline right">Aplica</span></td>
            </tr>
    </table>

    <table class="plan-table">
        <tr>
            <th>N°</th>
            <th>Fecha</th>
            <th>N° días</th>
            <th>Principal</th>
            <th>Interes</th>
            <th>Comisión por desembolso</th>
            <th>Cuota</th>
            <th>Saldo Capital</th>
        </tr>
        <?php 
        $total_principal = 0; $total_interes = 0; $total_comision = 0; $total_pagado = 0;
        foreach ($cuotas as $i => $c): 
            $dias = isset($c->dias) ? $c->dias : 14;
            $comision = isset($c->comision) ? $c->comision : '';
            $total_principal += isset($c->monto_capital) ? $c->monto_capital : 0;
            $total_interes += isset($c->monto_interes) ? $c->monto_interes : 0;
            $total_comision += is_numeric($comision) ? $comision : 0;
            $total_pagado += isset($c->monto_couta) ? $c->monto_couta : 0;
        ?>
        <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo isset($c->fecha_couta) ? formatoFechaCorta($c->fecha_couta) : ''; ?></td>
            <td><?php echo $dias; ?></td>
            <td><?php echo isset($c->monto_capital) ? number_format($c->monto_capital,2) : ''; ?></td>
            <td><?php echo isset($c->monto_interes) ? number_format($c->monto_interes,2) : ''; ?></td>
            <td><?php echo $comision !== '' ? number_format($comision,2) : ''; ?></td>
            <td><?php echo isset($c->monto_couta) ? number_format($c->monto_couta,2) : ''; ?></td>
            <td><?php echo isset($c->saldo_capital) ? number_format($c->saldo_capital,2) : ''; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <table class="totals-table">
        <tr>
            <th>Total principal</th>
            <th>Total intereses</th>
            <th>Total comisión</th>
            <th>Total Pagado</th>
        </tr>
        <tr>
            <td>$ <?php echo number_format($total_principal,2); ?></td>
            <td>$ <?php echo number_format($total_interes,2); ?></td>
            <td>$ <?php echo $total_comision !== 0 ? number_format($total_comision,2) : ''; ?></td>
            <td>$ <?php echo number_format($total_pagado,2); ?></td>
        </tr>
    </table>
    
    <!-- Signatures removed as requested -->
        <div style="position: fixed; bottom: 0; left: 0; width: 100%; text-align: center; font-size: 11px; color: #888;">
            Crediblamen - Todos los derechos reservados <?php echo date('Y'); ?>
        </div>
        </body>
        </html>
