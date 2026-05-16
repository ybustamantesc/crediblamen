<html>
<head>
    <meta charset="utf-8">
    <title><?php echo isset($document_title) ? $document_title : 'Solicitud'; ?> <?php echo $solicitud->idsolicitud; ?></title>
    <style>
        body{font-family: Arial, sans-serif; color:#222; margin:20px}
        .header{ text-align:center; border-bottom:3px solid #00a389; padding-bottom:8px; margin-bottom:14px }
        .logo{font-weight:900;color:#073048; font-size:34px; line-height:1}
        .doc-title{font-size:18px; font-weight:900; color:#073048; margin-top:6px; letter-spacing:0.6px}
        .meta-top{position: absolute; top:18px; right:20px; font-size:0.85rem; color:#444; text-align:right}
        .section{margin:12px 0}
        h2{margin:0 0 6px 0;color:#073048; font-size:14px}
        table{width:100%;border-collapse:collapse;margin-top:6px}
        td, th{padding:6px 8px;border:1px solid #e6e6e6;font-size:0.95rem; vertical-align:top}
        .col-label{width:30%;font-weight:700;background:#fbfbfb}
        .content{margin-bottom:80px}
        .footer{position:fixed; bottom:0; left:0; right:0; text-align:center; font-size:0.85rem; color:#666; border-top:1px solid #eee; padding:8px 0;}
    </style>
</head>
<body>

<?php
function _show_field($val) {
    if (!isset($val) || $val === null || $val === '') {
        return '&nbsp;';
    }
    return $val;
}

function _show_bool($val) {
    // Always display Sí or No for checkbox/radio fields.
    // Treat null/empty/false/0 as No, truthy values as Sí.
    return ($val ? 'Sí' : 'No');
}

function _is_filled($v) {
    return isset($v) && $v !== null && $v !== '';
}

function _join_nonempty_values($values, $sep = ' / ') {
    $out = array();
    foreach ($values as $v) {
        if (_is_filled($v)) $out[] = $v;
    }
    if (empty($out)) return '&nbsp;';
    return implode($sep, $out);
}

function _duration_label($years, $months, $y_label = 'años', $m_label = 'meses') {
    $parts = array();
    if (_is_filled($years)) $parts[] = $years . ' ' . $y_label;
    if (_is_filled($months)) $parts[] = $months . ' ' . $m_label;
    if (empty($parts)) return '';
    return implode(' / ', $parts);
}

function _format_date_or_blank($val) {
    if (!_is_filled($val)) return '';
    $ts = strtotime($val);
    if ($ts === false) return $val;
    return date('Y-m-d', $ts);
}
?>

<?php
// Always render the clean, elegant Q&A report only (avoid adding scanned template pages)
?>

<style
    body { font-family: Arial, Helvetica, sans-serif; color:#111; background:#f6f8fa; }
    .report { width:180mm; margin:10mm auto; font-size:12px; background:#ffffff; padding:10mm; border-radius:6px; box-shadow:0 0 0 1px rgba(0,0,0,0.04); }
    .report h1 { font-size:20px; margin:0 0 8px 0; text-align:center; color:#073048 }
    .report .meta { text-align:center; margin-bottom:8px; color:#666; font-size:11px }
    .report h2 { font-size:14px; margin:16px 0 8px 0; color:#073048 }
    .qa { width:100%; border-collapse:collapse; margin-bottom:6px }
    .qa td.label { width:36%; vertical-align:top; padding:8px 10px; font-weight:700; color:#0b4861; background:#fbfdff }
    .qa td.value { width:64%; vertical-align:top; padding:8px 10px; border-bottom:1px dashed #e6e9eb; min-height:18px; color:#222 }
    .qa tr + tr td.value { padding-top:10px }
    .small { font-size:11px; color:#666 }
    .muted { color:#777 }
    .section { margin-top:6px }
    .footer { margin-top:16px; font-size:11px; color:#888; text-align:center }
    /* print-friendly rules */
    @page { margin: 10mm 8mm; }
    table { page-break-inside: avoid }
    tr { page-break-inside: avoid; page-break-after: auto }
</style>

<div class="report">
    <h1>Solicitud Inicial de Crédito</h1>
    <div class="small muted">Registro ID: <?php echo isset($solicitud->id) ? $solicitud->id : '-'; ?> &nbsp; &nbsp; Fecha: <?php echo isset($solicitud->fecha_recepcion) ? date('Y-m-d', strtotime($solicitud->fecha_recepcion)) : '-'; ?></div>

    <h2>Información del Crédito</h2>
    <table class="qa">
        <tr><td class="label">Giro del negocio</td><td class="value"><?php echo _show_field($solicitud->giro_negocio); ?></td></tr>
        <tr><td class="label">Monto solicitado</td><td class="value"><?php echo _show_field($solicitud->monto_solicitado); ?></td></tr>
        <tr><td class="label">Plazo (meses)</td><td class="value"><?php echo _show_field($solicitud->plazo_meses); ?></td></tr>
        <tr><td class="label">Frecuencia</td><td class="value"><?php echo _show_field($solicitud->frecuencia); ?></td></tr>
        <tr><td class="label">Tasa de interés</td><td class="value"><?php echo _show_field($solicitud->tasa_interes); ?></td></tr>
        <tr><td class="label">Cuota estimada</td><td class="value"><?php echo _show_field($solicitud->cuota_estim_estimada); ?></td></tr>
        <tr><td class="label">Cuota estimada quincenal/catorcenal</td><td class="value"><?php echo _show_field($solicitud->cuota_estim_estimada_quincenal); ?></td></tr>
        <tr><td class="label">Garantía</td><td class="value"><?php echo _show_field($solicitud->garantia); ?></td></tr>
        <tr><td class="label">Ruta (Asesor)</td><td class="value"><?php echo _show_field($solicitud->nombre_asesor); ?></td></tr>
    </table>

    <h2>Selecciones / Flags</h2>
    <table class="qa">
        <tr><td class="label">Solicitud nueva</td><td class="value"><?php echo _show_bool($solicitud->es_nuevo); ?></td></tr>
        <tr><td class="label">Renovación</td><td class="value"><?php echo _show_bool($solicitud->es_renovacion); ?></td></tr>
        <tr><td class="label">Matrícula / Permiso</td><td class="value"><?php echo _show_bool($solicitud->matricula_permiso); ?></td></tr>
        <tr><td class="label">Cédula vigente</td><td class="value"><?php echo _show_bool($solicitud->cedula_vigente); ?></td></tr>
        <tr><td class="label">Otros ingresos (flag)</td><td class="value"><?php echo _show_bool($solicitud->otros_ingresos); ?></td></tr>
        <tr><td class="label">Ahorros (flag)</td><td class="value"><?php echo _show_bool($solicitud->ahorros); ?></td></tr>
        <tr><td class="label">Recibo de servicios</td><td class="value"><?php echo _show_bool($solicitud->recibo_servicios); ?></td></tr>
        <tr><td class="label">Investigación a vecinos</td><td class="value"><?php echo _show_bool($solicitud->investigacion_vecinos); ?></td></tr>
    </table>

    <h2>Datos Generales del Cliente</h2>
    <table class="qa">
        <tr><td class="label">Nombre completo</td><td class="value"><?php echo trim(_show_field($solicitud->apellidos . ' ' . $solicitud->nombres)); ?></td></tr>
                <tr><td class="label">Tipo / Nº identificación</td><td class="value"><?php echo _join_nonempty_values(array(isset($solicitud->tipo_documento) && $solicitud->tipo_documento ? $solicitud->tipo_documento : $solicitud->tipo_doc, $solicitud->numero_doc), ' '); ?></td></tr>
                <tr><td class="label">Fecha de nacimiento / Edad</td><td class="value"><?php echo _join_nonempty_values(array(_format_date_or_blank($solicitud->fecha_nacimiento), _is_filled($solicitud->edad) ? $solicitud->edad : ''), ' '); ?></td></tr>
        <tr><td class="label">Estado civil</td><td class="value"><?php echo _show_field($solicitud->estado_civil); ?></td></tr>
                <tr><td class="label">Nombre del cónyuge / Teléfono</td><td class="value"><?php echo _join_nonempty_values(array($solicitud->nombre_conyuge, $solicitud->telefono_conyuge)); ?></td></tr>
                <tr><td class="label">Dirección / Barrio</td><td class="value"><?php echo _join_nonempty_values(array($solicitud->direccion, $solicitud->barrio)); ?></td></tr>
        <tr><td class="label">Teléfono</td><td class="value"><?php echo _show_field($solicitud->telefono); ?></td></tr>
                <tr><td class="label">Tiempo de residencia</td><td class="value"><?php $tmp = _duration_label($solicitud->tiempo_residir_anios, $solicitud->tiempo_residir_meses, 'años', 'meses'); echo _is_filled($tmp) ? $tmp : '&nbsp;'; ?></td></tr>
        <tr><td class="label">Condición de vivienda</td><td class="value"><?php echo _show_field($solicitud->condicion_vivienda); ?></td></tr>
    </table>

    <h2>Información Laboral</h2>
    <table class="qa">
        <tr><td class="label">Nombre empresa</td><td class="value"><?php echo _show_field($solicitud->nombre_empresa); ?></td></tr>
        <tr><td class="label">Dirección empresa</td><td class="value"><?php echo _show_field($solicitud->direccion_empresa); ?></td></tr>
        <tr><td class="label">Teléfono empresa</td><td class="value"><?php echo _show_field($solicitud->telefono_empresa); ?></td></tr>
                <tr><td class="label">Cargo / Tiempo empleo</td><td class="value"><?php $dur = _duration_label($solicitud->tiempo_empleo_anios, $solicitud->tiempo_empleo_meses, 'a', 'm'); echo _join_nonempty_values(array($solicitud->cargo_puesto, $dur)); ?></td></tr>
        <tr><td class="label">Tipo de contrato</td><td class="value"><?php echo _show_field($solicitud->tipo_contrato); ?></td></tr>
        <tr><td class="label">Ingreso mensual neto</td><td class="value"><?php echo _show_field($solicitud->ingreso_mensual_neto); ?></td></tr>
        <tr><td class="label">Deducciones</td><td class="value"><?php echo _show_field($solicitud->deducciones); ?></td></tr>
    </table>

    <h2>Información del Negocio</h2>
    <table class="qa">
        <tr><td class="label">Nombre del negocio</td><td class="value"><?php echo _show_field($solicitud->nombre_negocio); ?></td></tr>
        <tr><td class="label">Actividad económica</td><td class="value"><?php echo _show_field($solicitud->actividad_economica); ?></td></tr>
        <tr><td class="label">Ubicación negocio</td><td class="value"><?php echo _show_field($solicitud->ubicacion_negocio); ?></td></tr>
        <tr><td class="label">Teléfono negocio</td><td class="value"><?php echo _show_field($solicitud->telefono_negocio); ?></td></tr>
                <tr><td class="label">Tiempo operación</td><td class="value"><?php $tmp2 = _duration_label($solicitud->tiempo_operacion_anios, $solicitud->tiempo_operacion_meses, 'años', 'meses'); echo _is_filled($tmp2) ? $tmp2 : '&nbsp;'; ?></td></tr>
        <tr><td class="label">Número empleados</td><td class="value"><?php echo _show_field($solicitud->numero_empleados); ?></td></tr>
        <tr><td class="label">Negocio propio</td><td class="value"><?php echo _show_field($solicitud->negocio_propio); ?></td></tr>
        <tr><td class="label">Antigüedad negocio</td><td class="value"><?php echo _show_field($solicitud->negocio_antiguedad); ?></td></tr>
    </table>

    <h2>Ingresos y Ventas</h2>
    <table class="qa">
        <tr><td class="label">Ventas días buenos / malos</td><td class="value"><?php echo _join_nonempty_values(array($solicitud->ventas_dias_buenos, $solicitud->ventas_dias_malos)); ?></td></tr>
        <tr><td class="label">Ventas promedio diario / mensual</td><td class="value"><?php echo _join_nonempty_values(array($solicitud->ventas_promedio_diarios, $solicitud->ventas_promedio_mensual)); ?></td></tr>
        <tr><td class="label">Otros ingresos (detalle)</td><td class="value"><?php echo nl2br(_show_field($solicitud->otros_ingresos_detalle)); ?></td></tr>
        <tr><td class="label">Ingreso promedio alto / bajo</td><td class="value"><?php echo _join_nonempty_values(array($solicitud->ingreso_promedio_alto, $solicitud->ingreso_promedio_bajo)); ?></td></tr>
        <tr><td class="label">Margen comercial (%) Actividad Principal</td><td class="value"><?php echo _show_field($solicitud->margen_comercial); ?></td></tr>
    </table>

    <h2>Estructura Financiera</h2>
    <table class="qa">
        <tr><td class="label">Cuentas por cobrar</td><td class="value"><?php echo _show_field($solicitud->cuentas_por_cobrar_amount); ?></td></tr>
        <tr><td class="label">Caja (efectivo)</td><td class="value"><?php echo _show_field($solicitud->caja_amount); ?></td></tr>
        <tr><td class="label">Banco</td><td class="value"><?php echo _show_field($solicitud->banco_amount); ?></td></tr>
        <tr><td class="label">Detalle de inventario</td><td class="value"><?php echo nl2br(_show_field($solicitud->detalle_inventario)); ?></td></tr>
    </table>

    <h2>Gastos Fijos y Operativos</h2>
    <table class="qa">
        <tr><td class="label">Pago alquiler</td><td class="value"><?php echo _show_field($solicitud->pago_alquiler); ?></td></tr>
        <tr><td class="label">Pago trabajadores</td><td class="value"><?php echo _show_field($solicitud->pago_trabajadores); ?></td></tr>
        <tr><td class="label">Energía</td><td class="value"><?php echo _show_field($solicitud->energia); ?></td></tr>
        <tr><td class="label">Agua</td><td class="value"><?php echo _show_field($solicitud->agua); ?></td></tr>
        <tr><td class="label">Internet / Teléfono</td><td class="value"><?php echo _show_field($solicitud->internet); ?></td></tr>
        <tr><td class="label">Gastos fijos (total)</td><td class="value"><?php echo _show_field($solicitud->gastos_fijos); ?></td></tr>
        <tr><td class="label">Otros gastos</td><td class="value"><?php echo nl2br(_show_field($solicitud->otros_gastos)); ?></td></tr>
    </table>

    <h2>Declaración y Referencias</h2>
    <table class="qa">
        <tr><td class="label">Observaciones</td><td class="value"><?php echo nl2br(_show_field($solicitud->observaciones)); ?></td></tr>
        <tr><td class="label">Referencias personales</td><td class="value"><?php echo nl2br(_show_field($solicitud->referencias_personales)); ?></td></tr>
        <tr><td class="label">Promotor / Firma</td><td class="value"><?php $fullname = trim($solicitud->apellidos . ' ' . $solicitud->nombres); echo _join_nonempty_values(array($solicitud->promotor, $fullname)); ?></td></tr>
    </table>

    <div class="footer">Generado por CredI - Formato elegante para microfinanciera. Campos vacíos se muestran como espacio en blanco para permitir firma/llenado manual.</div>
</div>

    </body>
</html>