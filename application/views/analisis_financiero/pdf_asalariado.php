<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen Análisis Financiero Asalariado</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #222; }
        .pdf-titulo-principal {
            font-family: 'Courier New', Courier, monospace;
            color: #1760a3;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .pdf-titulo-sub {
            font-family: 'Courier New', Courier, monospace;
            color: #1760a3;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .pdf-titulo-fecha {
            font-family: 'Courier New', Courier, monospace;
            color: #1760a3;
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9.5px; }
        th, td { border: 1px solid #e6e6e6; padding: 3px 4px; text-align: left; }
        th { background: #f5f5f5; }
        .cliente-nombre { font-size: 13px; font-weight: bold; color: #1760a3; margin-bottom: 6px; }
    </style>
</head>
<body>
    <?php
    $decode_array_field = function ($value) {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [$value];
        }
        return [];
    };

    $build_obligaciones = function ($fechas, $cuotas, $instituciones, $saldos) {
        $rows = [];
        $count = max(count($fechas), count($cuotas), count($instituciones), count($saldos));
        for ($i = 0; $i < $count; $i++) {
            $fecha = trim((string)($fechas[$i] ?? ''));
            $cuota = trim((string)($cuotas[$i] ?? ''));
            $institucion = trim((string)($instituciones[$i] ?? ''));
            $saldo = trim((string)($saldos[$i] ?? ''));
            if ($fecha === '' && $cuota === '' && $institucion === '' && $saldo === '') {
                continue;
            }
            $rows[] = [
                'fecha' => $fecha,
                'cuota' => $cuota,
                'institucion' => $institucion,
                'saldo' => $saldo,
            ];
        }
        return $rows;
    };

    $olp_rows = $build_obligaciones(
        $decode_array_field($analisis['olp_fecha'] ?? []),
        $decode_array_field($analisis['olp_cuota'] ?? []),
        $decode_array_field($analisis['olp_instituciones'] ?? []),
        $decode_array_field($analisis['olp_saldo'] ?? [])
    );
    $ocp_rows = $build_obligaciones(
        $decode_array_field($analisis['ocp_fecha'] ?? []),
        $decode_array_field($analisis['ocp_cuota'] ?? []),
        $decode_array_field($analisis['ocp_instituciones'] ?? []),
        $decode_array_field($analisis['ocp_saldo'] ?? [])
    );
    ?>
    <div style="width:100%; text-align:center; margin-bottom: 10px;">
        <div class="cliente-nombre">
            <?= isset($analisis['nombres']) || isset($analisis['apellidos']) ? htmlspecialchars(($analisis['nombres'] ?? '') . ' ' . ($analisis['apellidos'] ?? '')) : '' ?>
        </div>
        <div class="pdf-titulo-principal">ANÁLISIS ESTADOS FINANCIEROS - COMERCIANTE</div>
        <div class="pdf-titulo-sub">Balance del Solicitante</div>
        <div class="pdf-titulo-fecha"><?= date('d/m/Y') ?></div>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr>
            <th colspan="3" style="background:#e9ecef; text-align:center;">ACTIVO CIRCULANTE O CORRIENTE</th>
            <th colspan="3" style="background:#e9ecef; text-align:center;">PASIVO CIRCULANTE O CORRIENTE</th>
        </tr>
        <tr>
            <td colspan="3"><b>(2) DISPONIBLE (A+B)</b></td>
            <td colspan="3"><b>(1) CUENTAS POR PAGAR</b> C$ <?= number_format($analisis['cuentas_pagar_proveedores'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>A, Efectivo o Caja</td>
            <td colspan="2">C$ <?= number_format($analisis['efectivo_caja'] ?? 0, 2) ?></td>
            <td>Cuentas por pagar a proveedores</td>
            <td colspan="2">C$ <?= number_format($analisis['cuentas_pagar_proveedores'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>B, Dinero ahorrado o Banco</td>
            <td colspan="2">C$ <?= number_format($analisis['dinero_ahorrado'] ?? 0, 2) ?></td>
            <td>B, Cuentas por pagar crédito corto plazo</td>
            <td colspan="2">C$ <?= number_format($analisis['cuentas_pagar_credito'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td colspan="3"><b>(3) CUENTAS POR COBRAR</b></td>
            <td colspan="3"><b>(2) PASIVO NO CORRIENTE (mayor de 12 meses)</b></td>
        </tr>
        <tr>
            <td colspan="3">C$ <?= number_format($analisis['cuentas_cobrar'] ?? 0, 2) ?></td>
            <td colspan="3">C$ <?= number_format($analisis['pasivo_no_corriente'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td colspan="3"><b>(4) INVENTARIOS</b></td>
            <td colspan="3"><b>(3) TOTAL PASIVO (1+2)</b> C$ <?= number_format($analisis['total_pasivo'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>A, Inventario de mercadería</td>
            <td colspan="2">C$ <?= number_format($analisis['inventario_mercaderia'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td>B, Productos en proceso</td>
            <td colspan="2">C$ <?= number_format($analisis['productos_proceso'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td>C, Productos terminados</td>
            <td colspan="2">C$ <?= number_format($analisis['productos_terminados'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3"><b>(5) TOTAL ACTIVOS FIJOS O</b> C$ <?= number_format($analisis['total_activos_fijos'] ?? 0, 2) ?></td>
            <td colspan="3"><b>(4) TOTAL PATRIMONIO</b> C$ <?= number_format($analisis['total_patrimonio'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>A, Bienes muebles (equipos)</td>
            <td colspan="2">C$ <?= number_format($analisis['bienes_muebles'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td>B, Propiedades (casa, finca, etc.)</td>
            <td colspan="2">C$ <?= number_format($analisis['propiedades'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td>C, Otros Activos</td>
            <td colspan="2">C$ <?= number_format($analisis['otros_activos'] ?? 0, 2) ?></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3"><b>(6) TOTAL ACTIVOS (1+5)</b> C$ <?= number_format($analisis['total_activos'] ?? 0, 2) ?></td>
            <td colspan="3"><b>TOTAL PASIVO + PATRIMONIO (3+4)</b> C$ <?= number_format($analisis['total_pasivo_patrimonio'] ?? 0, 2) ?></td>
        </tr>
    </table>
    <h3 style="background:#073048; color:#fff; text-align:center; padding:6px; margin-top:30px;">Estado Financiero y Flujo de Caja Mensual</h3>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <tr>
            <th colspan="3" style="background:#e9ecef; text-align:center;">ESTADO DE RESULTADO MENSUAL</th>
            <th colspan="3" style="background:#e9ecef; text-align:center;">FLUJO DE CAJA MENSUAL</th>
        </tr>
        <tr>
            <td colspan="2"><b>(1) VENTAS TOTALES (A+B)</b></td>
            <td>C$ <?= number_format($analisis['ventas_totales'] ?? 0, 2) ?></td>
            <td>1. Ventas al contado</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_ventas_contado'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>A, Ventas al contado</td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['ventas_contado'] ?? 0, 2) ?></td>
            <td>2. Recuperación ventas al crédito</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_recuperacion_credito'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td>B, Ventas al crédito</td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['ventas_credito'] ?? 0, 2) ?></td>
            <td>3. Compras al contado</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_compras_contado'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td><b>(2) COSTOS DE VENTA</b></td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['costos_venta'] ?? 0, 2) ?></td>
            <td>4. Gastos Generales</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_gastos_generales'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td><b>(3) MARGEN BRUTO (1-2)</b></td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['margen_bruto'] ?? 0, 2) ?></td>
            <td><b>Flujo del negocio (1+2-3-4)</b></td>
            <td colspan="2">C$ <?= number_format($analisis['flujo_negocio'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td><b>(4) GASTOS GENERALES</b></td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['gastos_generales'] ?? 0, 2) ?></td>
            <td>5. Otros ingresos de la unidad familiar</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_otros_ingresos'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td><b>(5) UTILIDAD OPERATIVA (3-4)</b></td>
            <td colspan="1"></td>
            <td>C$ <?= number_format($analisis['utilidad_operativa'] ?? 0, 2) ?></td>
            <td>6. Gastos consumo familiar<br><span style="font-size:11px; color:#555;">(costo mínimo de vida en función canasta básica y cantidad de personas que dependen del titular)</span></td>
            <td colspan="2">C$ <?php
                $valor_canasta = isset($analisis['fcm_valor_canasta_basica']) ? floatval($analisis['fcm_valor_canasta_basica']) : 0;
                $cant_personas = isset($analisis['fcm_cant_personas_dep']) ? floatval($analisis['fcm_cant_personas_dep']) : 0;
                $gasto_consumo = ($valor_canasta > 0 && $cant_personas > 0) ? ($valor_canasta / 6 * $cant_personas) : ($analisis['fcm_gastos_consumo'] ?? 0);
                echo number_format($gasto_consumo, 2);
            ?></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td>7. Otros gastos (pagos de cuotas y otras transacciones financieras)</td>
            <td colspan="2">C$ <?= number_format($analisis['fcm_otros_gastos'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td><b>FLUJO NETO DISPONIBLE (1+2-3-4+5-6-7)</b></td>
            <td colspan="2"><b>C$ <?= number_format($analisis['flujo_neto_disponible'] ?? 0, 2) ?></b></td>
        </tr>
    </table>
    <h3 style="background:#073048; color:#fff; text-align:center; padding:6px; margin-top:30px;">Gastos Fijos Mensuales</h3>
    <table style="width:60%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr><th style="background:#e9ecef;">Concepto</th><th style="background:#e9ecef;">Valor C$</th></tr>
        <tr><td>Local o casa propia/Alquiler</td><td>C$ <?= number_format($analisis['gasto_local'] ?? 0, 2) ?></td></tr>
        <tr><td>Servicio de energía eléctrica</td><td>C$ <?= number_format($analisis['gasto_energia'] ?? 0, 2) ?></td></tr>
        <tr><td>Servicio de agua potable</td><td>C$ <?= number_format($analisis['gasto_agua'] ?? 0, 2) ?></td></tr>
        <tr><td>Internet residencial/Plan</td><td>C$ <?= number_format($analisis['gasto_internet'] ?? 0, 2) ?></td></tr>
        <tr><td style="background:yellow;">Seguridad:</td><td style="background:yellow;">C$ <?= number_format($analisis['gasto_seguridad'] ?? 0, 2) ?></td></tr>
        <tr><td style="background:yellow;">Limpieza y mantenimiento:</td><td style="background:yellow;">C$ <?= number_format($analisis['gasto_limpieza'] ?? 0, 2) ?></td></tr>
        <tr><td>Gastos personales básicos:</td><td>C$ <?= number_format($analisis['gasto_personal'] ?? 0, 2) ?></td></tr>
        <tr><th>Total</th><th>C$ <?= number_format(
            ($analisis['gasto_local'] ?? 0) +
            ($analisis['gasto_energia'] ?? 0) +
            ($analisis['gasto_agua'] ?? 0) +
            ($analisis['gasto_internet'] ?? 0) +
            ($analisis['gasto_seguridad'] ?? 0) +
            ($analisis['gasto_limpieza'] ?? 0) +
            ($analisis['gasto_personal'] ?? 0)
        , 2) ?></th></tr>
    </table>
    <h3 style="background:#073048; color:#fff; text-align:center; padding:6px; margin-top:30px;">Costos de Operación Directos</h3>
    <table style="width:60%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr><th style="background:#e9ecef;">Concepto</th><th style="background:#e9ecef;">Valor C$</th></tr>
        <tr><td>Salario ayudante/empleado</td><td>C$ <?= number_format($analisis['salario_ayudante'] ?? 0, 2) ?></td></tr>
        <tr><td>Transporte</td><td>C$ <?= number_format($analisis['costo_transporte'] ?? 0, 2) ?></td></tr>
        <tr><th>Total</th><th>C$ <?= number_format((($analisis['salario_ayudante'] ?? 0) + ($analisis['costo_transporte'] ?? 0)), 2) ?></th></tr>
    </table>



    <table style="width:60%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr>
            <td colspan="2" style="background:#e86c1a; color:#fff; font-family:'Courier New', Courier, monospace; font-size:16px; font-weight:bold;">Valor Canasta básica</td>
            <td style="background:#e86c1a; color:#fff; font-family:'Courier New', Courier, monospace; font-size:16px; font-weight:bold; text-align:right;">C$ <?= number_format($analisis['fcm_valor_canasta_basica'] ?? 0, 2) ?></td>
        </tr>
        <tr>
            <td colspan="2">Cantidad de personas dep</td>
            <td style="text-align:right;"><?= (int)($analisis['fcm_cant_personas_dep'] ?? 0) ?></td>
        </tr>
        <tr>
            <td colspan="2" style="background:#073048; color:#fff; font-weight:bold;">% MARGEN</td>
            <td style="text-align:right; font-weight:bold;"><?= number_format($analisis['porcentaje_margen'] ?? 0, 2) ?>%</td>
        </tr>
    </table>
    <h3 style="background:#073048; color:#fff; text-align:center; padding:6px; margin-top:30px;">Obligaciones a largo plazo</h3>
    <table style="width:80%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr style="background:#073048; color:#fff;">
            <th>Fecha</th><th>Cuota</th><th>Instituciones</th><th>Saldo</th>
        </tr>
        <?php if (!empty($olp_rows)): ?>
            <?php foreach ($olp_rows as $obl): ?>
                <tr>
                    <td><?= htmlspecialchars($obl['fecha'] ?? '') ?></td>
                    <td><?= htmlspecialchars($obl['cuota'] ?? '') ?></td>
                    <td><?= htmlspecialchars($obl['institucion'] ?? '') ?></td>
                    <td><?= number_format($obl['saldo'] ?? 0, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">Sin datos</td></tr>
        <?php endif; ?>
        <tr>
            <td colspan="3" style="background:#e86c1a; color:#fff; text-align:right; font-weight:bold;">Total</td>
            <td style="background:#e86c1a; color:#fff; font-weight:bold;">C$ <?= number_format($analisis['subtotal_olp_saldo'] ?? 0, 2) ?></td>
        </tr>
    </table>

    <h3 style="background:#073048; color:#fff; text-align:center; padding:6px; margin-top:30px;">Obligaciones a corto plazo</h3>
    <table style="width:80%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
        <tr style="background:#073048; color:#fff;">
            <th>Fecha</th><th>Cuota</th><th>Instituciones</th><th>Saldo</th>
        </tr>
        <?php if (!empty($ocp_rows)): ?>
            <?php foreach ($ocp_rows as $obl): ?>
                <tr>
                    <td><?= htmlspecialchars($obl['fecha'] ?? '') ?></td>
                    <td><?= htmlspecialchars($obl['cuota'] ?? '') ?></td>
                    <td><?= htmlspecialchars($obl['institucion'] ?? '') ?></td>
                    <td><?= number_format($obl['saldo'] ?? 0, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">Sin datos</td></tr>
        <?php endif; ?>
        <tr>
            <td colspan="2"></td>
            <td style="background:#e86c1a; color:#fff; text-align:right; font-weight:bold;">Total</td>
            <td style="background:#e86c1a; color:#fff; font-weight:bold;">C$ <?= number_format($analisis['subtotal_ocp_saldo'] ?? 0, 2) ?></td>
        </tr>
    </table>
    <h3 style="background:#073048; color:#fff; text-align:left; padding:6px; margin-top:30px;">Indicadores</h3>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <tr>
            <th style="background:#e9ecef;">Indicadores</th>
            <th style="background:#e9ecef;">Resultado Actual</th>
        </tr>
        <tr>
            <td>Nivel de Endeudamiento = (Total Pasivo + Monto Crédito Solicitado / Total Activos)</td>
            <td style="background:yellow; text-align:center; font-weight:bold;">
                <?php
                $val = isset($analisis['indicador_endeudamiento']) ? floatval($analisis['indicador_endeudamiento']) : 0;
                echo number_format($val * 100, 2) . '%';
                ?>
            </td>
        </tr>
        <tr>
            <td>Capital de trabajo Neto (Activo Corriente - Pasivo Corriente)</td>
            <td style="background:yellow; text-align:center; font-weight:bold;">
                C$ <?= number_format($analisis['capital_trabajo_neto'] ?? 0, 2) ?>
            </td>
        </tr>
        <tr>
            <td>Cobertura de la deuda capacidad de pago = (Cuota / Flujo neto disponible)<br>Máxima porción a comprometer del flujo = 25%</td>
            <td style="background:yellow; text-align:center; font-weight:bold;">
                <?php
                $val = isset($analisis['cobertura_deuda']) ? floatval($analisis['cobertura_deuda']) : 0;
                echo number_format($val * 100, 2) . '%';
                ?>
            </td>
        </tr>
        <tr>
            <td>Cobertura de garantía (150%)</td>
            <td style="background:yellow; text-align:center; font-weight:bold;">
                <?php
                $cg_raw = isset($analisis['cobertura_garantia']) ? $analisis['cobertura_garantia'] : 0;
                if (is_string($cg_raw)) {
                    $cg_raw = str_replace('%', '', $cg_raw);
                    $cg_raw = trim($cg_raw);
                }
                $cg = floatval($cg_raw);
                if ($cg > 0 && $cg <= 1) {
                    $cg = $cg * 100;
                }
                echo number_format($cg, 2) . '%';
                ?>
            </td>
        </tr>
    </table>
    <p><small>Generado el <?= date('d/m/Y H:i') ?></small></p>
</body>
</html>
