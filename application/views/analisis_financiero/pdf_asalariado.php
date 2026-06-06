<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen Análisis Financiero Comerciante</title>
    <style>
        @page { margin: 16mm 12mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            background: #ffffff;
        }
        .pdf-titulo-principal {
            font-family: 'Courier New', Courier, monospace;
            color: #175a9e;
            font-size: 44px;
            font-weight: bold;
            letter-spacing: 1.4px;
            margin-bottom: 1px;
            text-transform: uppercase;
        }
        .pdf-titulo-sub {
            font-family: 'Courier New', Courier, monospace;
            color: #175a9e;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.8px;
            margin-top: 0;
        }
        .pdf-titulo-fecha {
            font-family: 'Courier New', Courier, monospace;
            color: #175a9e;
            font-size: 12px;
            font-weight: bold;
            margin-top: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 10px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #cfd6de;
            padding: 5px 8px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background: #e8edf3;
            color: #111827;
            font-weight: bold;
        }
        .section-title {
            margin-top: 14px;
            margin-bottom: 0;
            padding: 6px 8px;
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
            background: #073a5b;
            text-transform: uppercase;
        }
        .value-col {
            width: 210px;
            text-align: right;
            font-weight: bold;
            color: #111827;
        }
        .cliente-nombre {
            font-size: 16px;
            font-weight: bold;
            color: #175a9e;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .total-row td {
            background: #f4f8fc;
            font-weight: bold;
        }
        .subtle-row td {
            background: #fbfdff;
        }
    </style>
</head>
<body>
    <div style="width:100%; text-align:center; margin-top: 8px; margin-bottom: 6px;">
        <div class="cliente-nombre">
            <?= htmlspecialchars(trim(($analisis['nombres'] ?? '') . ' ' . ($analisis['apellidos'] ?? ''))) ?>
        </div>
        <div class="pdf-titulo-principal">ANÁLISIS ESTADOS FINANCIEROS - ASALARIADO</div>
        <div class="pdf-titulo-sub">Balance del Solicitante</div>
        <div class="pdf-titulo-fecha"><?= date('d/m/Y') ?></div>
    </div>

    <?php
    $money = function ($value) {
        return 'C$ ' . number_format((float)$value, 2);
    };
    $money_or_dash = function ($value) use ($money) {
        return ((float)$value) > 0 ? $money($value) : '-';
    };
    $money_or_currency_dash = function ($value) use ($money) {
        return ((float)$value) > 0 ? $money($value) : 'C$ -';
    };
    $percent = function ($value) {
        if (is_string($value)) {
            $value = str_replace('%', '', trim($value));
        }
        $num = (float)$value;
        if ($num > 0 && $num <= 1) {
            $num = $num * 100;
        }
        return number_format($num, 2) . '%';
    };
    ?>

    <div class="section-title">Flujo Mensual</div>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="value-col">Valores C$</th>
            </tr>
        </thead>
        <tbody>
            <tr class="total-row">
                <td><b>(1) TOTAL INGRESOS (A+B+C+D+E)</b></td>
                <td class="value-col"><?= $money($analisis['total_ingresos'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>A. Sueldo Neto (restadas las deducciones INSS e IR)</td>
                <td class="value-col"><?= $money($analisis['ingreso_sueldo_neto'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>B. Comisiones</td>
                <td class="value-col"><?= $money_or_dash($analisis['ingreso_comisiones'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>C. Bonificaciones</td>
                <td class="value-col"><?= $money_or_dash($analisis['ingreso_bonificaciones'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>D. Remesas</td>
                <td class="value-col"><?= $money_or_dash($analisis['ingreso_remesas'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>E. Otros ingresos</td>
                <td class="value-col"><?= $money_or_dash($analisis['ingreso_otros'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr class="total-row">
                <td><b>(2) GASTOS FAMILIARES TOTAL (F+G+H+I+J+K+L)</b></td>
                <td class="value-col"><?= $money($analisis['total_gastos_familiares'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>F. Gastos en alimentación</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_alimentacion'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>G. Servicios básicos (agua, luz, Internet Fijo.)</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_servicios'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>H. Vestuario</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_vestuario'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>I. Gastos educativos</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_educativos'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>J. Gastos en transporte</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_transporte'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>K. Gastos en alquiler o arriendo vivienda</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['gastos_alquiler'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>L. Salud / Medicinas</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['pago_empleado_viatico'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>P. Entretenimiento (incluye gastos derivados del uso celulares e internet)</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['p_entretenimiento'] ?? 0) ?></td>
            </tr>
            <tr class="subtle-row">
                <td>Q. Otros Gastos (Especifique)</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['otros_gastos'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td><b>(3) OTRAS OBLIGACIONES (M+N+O)</b></td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['total_otras_obligaciones'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>M. Abono o cuotas de prestamos o deudas con instituciones financieras, casas comerciales o particulares.</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['cuotas_prestamos'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>N. Pension alimenticia o similares</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['pension_alimenticia'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>O. Otros</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['otras_obligaciones'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td><b>(4) TOTAL EGRESOS (2+3)</b></td>
                <td class="value-col"><?= $money($analisis['total_egresos'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td><b>(5) FLUJO NETO MENSUAL DISPONIBLE (1-4)</b></td>
                <td class="value-col"><?= $money($analisis['flujo_neto_mensual'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td><b>Valor de la cuota periódica de pago estimada C$</b></td>
                <td class="value-col"><?= $money($analisis['cuota_periodica'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Canasta Basica</div>
    <table>
        <tbody>
            <tr>
                <td><b>Canasta basica C$</b></td>
                <td class="value-col"><?= $money($analisis['canasta_basica'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><b>Cantidad promedio</b></td>
                <td class="value-col"><?= number_format((float)($analisis['cantidad_promedio'] ?? 0), 0) ?></td>
            </tr>
            <tr>
                <td><b>Monto por persona</b></td>
                <td class="value-col"><?= $money($analisis['monto_por_persona'] ?? 0) ?></td>
            </tr>
            <tr>
                <td><b>Cantidad de personas dependientes</b></td>
                <td class="value-col"><?= number_format((float)($analisis['personas_dependientes'] ?? 0), 0) ?></td>
            </tr>
            <tr>
                <td><b>Gastos de alimentación</b></td>
                <td class="value-col"><?= $money($analisis['gastos_alimentacion_canasta'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Tipo De Transporte</div>
    <table>
        <tbody>
            <tr>
                <td><b>Tipo de transporte</b></td>
                <td class="value-col"><b>Valores C$</b></td>
            </tr>
            <tr>
                <td>Transporte urbano colectivo</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['transporte_urbano'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>Servicio individual ( taxi,caponera)</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['transporte_individual'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>Transporte interurbano</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['transporte_interurbano'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>Recorrido laboral</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['recorrido_laboral'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>Vehículo particular de uso personal</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['vehiculo_particular'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Gastos De Vivienda</div>
    <table>
        <tbody>
            <tr>
                <td><b>Gastos de vivienda</b></td>
                <td class="value-col"><b>Valores C$</b></td>
            </tr>
            <tr>
                <td>Alquiler</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['alquiler'] ?? 0) ?></td>
            </tr>
            <tr>
                <td>Casa propia</td>
                <td class="value-col"><?= $money_or_currency_dash($analisis['casa_propia'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">7.2. Indicadores</div>
    <table>
        <thead>
            <tr>
                <th>Indicadores</th>
                <th class="value-col">Resultado Actual</th>
            </tr>
        </thead>
        <tbody>
            <tr class="total-row">
                <td>Nivel de endeudamiento = ((3) Otras Obligaciones + Total de Deuda a Creditar) / (1) Total Ingresos</td>
                <td class="value-col">
                    <?php
                    $pdt = isset($analisis['porcentaje_deuda_total']) ? $analisis['porcentaje_deuda_total'] : null;
                    if ($pdt === null || $pdt === '' || (float)$pdt === 0.0) {
                        $otras = (float)($analisis['total_otras_obligaciones'] ?? 0);
                        $deudaAcreditar = (float)($analisis['total_deuda_acreditar'] ?? 0);
                        $ingresos = (float)($analisis['total_ingresos'] ?? 0);
                        $pdt = $ingresos > 0 ? (($otras + $deudaAcreditar) / $ingresos) * 100 : 0;
                    }
                    echo $percent($pdt);
                    ?>
                </td>
            </tr>
            <tr class="total-row">
                <td>Cobertura de la deuda con capacidad de pago = (Flujo neto disponible /cuota). Máxima porción a comprometer del flujo=25%</td>
                <td class="value-col"><?= $percent($analisis['cobertura_deuda'] ?? 0) ?></td>
            </tr>
            <tr class="total-row">
                <td>Cobertura de garantía (150%)</td>
                <td class="value-col"><?= $percent($analisis['cobertura_garantia'] ?? 0) ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

