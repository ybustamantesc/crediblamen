<html>
<head>
    <meta charset="utf-8">
    <title>Matriz de Evaluacion - <?php echo isset($perfil->solicitud_id)?'SOL-'.$perfil->solicitud_id:''; ?></title>
    <style>
        body{font-family: Arial, sans-serif; color:#222; margin:10px}
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

        /* report card style similar to solicitud */
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
        .footer-line { margin-top:16px; font-size:11px; color:#888; text-align:center }
        /* print-friendly rules */
        @page { margin: 10mm 8mm; }
        table { page-break-inside: avoid }
        tr { page-break-inside: avoid; page-break-after: auto }

        /* specific matrix styles */
        .score-table { width:100%; border-collapse:collapse; margin-top:8px }
        .score-table th { background:#0b87a9; color:#fff; font-weight:700; padding:8px 10px }
        .score-table td, .score-table th { border:1px solid #0bdae0; padding:8px 10px }
        .score-total { background:#0b5b78; color:#fff; font-weight:700 }
        .nivel-box { display:block; width:280px; margin:8px auto 0 auto; padding:10px 12px; color:#fff; font-weight:700; text-align:center; border-radius:4px }

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

?>

<div class="report">
    <h1>Matriz de Evaluación de Riesgo</h1>
    <div class="meta">Perfil ID: <?php echo isset($perfil->id)?$perfil->id:'-'; ?> &nbsp; &nbsp; Solicitud: <?php echo isset($perfil->solicitud_id)?'SOL-'.$perfil->solicitud_id:'-'; ?></div>

    <h2>Datos Generales</h2>
    <table class="qa">
        <tr><td class="label">Nombre/Razón social</td><td class="value"><?php echo _show_field(isset($sol->nombre_completo)?$sol->nombre_completo:(isset($perfil->nombre)?$perfil->nombre:'')); ?></td></tr>
        <tr><td class="label">Número identificación</td><td class="value"><?php echo _show_field(isset($sol->numero_documento)?$sol->numero_documento:(isset($perfil->numero_documento)?$perfil->numero_documento:'')); ?></td></tr>
        <tr><td class="label">Fecha inicio relación</td><td class="value"><?php echo _show_field(isset($sol->fecha_solicitud)?$sol->fecha_solicitud:''); ?></td></tr>
        <tr><td class="label">Fecha de Evaluación</td><td class="value"><?php echo _show_field(isset($fecha_evaluacion)?$fecha_evaluacion:date('d/m/Y H:i')); ?></td></tr>
    </table>

    <h2>Cuadro de Calificaciones</h2>
    <table class="score-table">
        <thead>
            <tr>
                <th style="width:6%;">#</th>
                <th style="width:70%;">Criterio de Valoración</th>
                <th style="width:24%;">Calificación</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $order = [
                    ['label'=>'Tipo de Persona','key'=>'tipo_persona'],
                    ['label'=>'Categoría','key'=>'categoria'],
                    ['label'=>'Actividad Económica','key'=>'actividad_economica'],
                    ['label'=>'Edad','key'=>'edad'],
                    ['label'=>'Condición PEP','key'=>'condicion_pep'],
                    ['label'=>'¿Es Frecuente?','key'=>'es_frecuente'],
                    ['label'=>'Zona geográfica','key'=>'zona_geografica'],
                    ['label'=>'Valor de Transacción','key'=>'valor_transaccion'],
                    ['label'=>'Garantía','key'=>'garantia']
                ];
                $i=1;
                foreach ($order as $row) {
                    $val = '';
                    if (!empty($group_scores) && is_array($group_scores) && array_key_exists($row['key'], $group_scores)){
                        $val = $group_scores[$row['key']];
                    }
                    echo '<tr>';
                    echo '<td style="text-align:center">'.($i++).'</td>';
                    echo '<td>'.html_escape($row['label']).'</td>';
                    echo '<td style="text-align:center">'.($val!==''?html_escape($val):'').'</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right;" class="score-total">TOTAL</td>
                <td style="text-align:center" class="score-total"><?php echo html_escape($matriz_score ?? ''); ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Nivel de Riesgo centered below -->
    <?php
        $bg = '#6c757d';
        if (!empty($nivel_riesgo)){
            $nr = strtolower(trim($nivel_riesgo));
            if ($nr === 'alto') $bg = '#d93025';
            elseif (strpos($nr,'medio') !== false) $bg = '#ffb020';
            elseif ($nr === 'bajo') $bg = '#28a745';
        }
    ?>
    <div class="section">
        <div class="doc-title" style="text-align:center; margin-top:8px;">DDC - Nivel de Riesgo</div>
        <div class="nivel-box" style="background:<?php echo $bg; ?>"><?php echo html_escape($nivel_riesgo ?? '-'); ?></div>
    </div>

    <!-- Resultado del cálculo DDC según nivel (celda B15 -> DDC-S / DDC-I) -->
    <div class="section" style="margin-top:6px;">
        <div style="text-align:center; font-weight:700; margin-bottom:6px;">Resultado DDC (según nivel)</div>
        <div style="display:block; width:180px; margin:0 auto; padding:8px 10px; text-align:center; border-radius:4px; background:#f2f2f2; font-weight:700;">
            <?php echo isset($ddc_result) && $ddc_result ? html_escape($ddc_result) : '-'; ?>
        </div>
    </div>

    <div class="footer-line">Documento generado por Servicredit - Matriz de Evaluación | Impreso: <?php echo isset($fecha_impresion)?html_escape($fecha_impresion):date('d/m/Y H:i'); ?><?php if (!empty($impreso_por)) echo ' | Usuario: '.html_escape($impreso_por); ?></div>

</div>

</body>
</html>
