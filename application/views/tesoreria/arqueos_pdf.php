<?php
$rows = isset($rows) && is_array($rows) ? $rows : array();
$grupos = isset($grupos) && is_array($grupos) ? $grupos : array();
$fecha = isset($fecha) && !empty($fecha) ? $fecha : date('Y-m-d');
$totales = isset($totales_reporte) && is_array($totales_reporte) ? $totales_reporte : array('aplicado' => 0, 'anulado' => 0, 'general' => 0, 'aplicados_count' => 0, 'anulados_count' => 0);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Arqueos de Pagos <?php echo html_escape($fecha); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .head { margin-bottom: 10px; }
        .head h2 { margin: 0 0 4px 0; font-size: 16px; }
        .meta { margin: 0; font-size: 11px; color: #555; }
        .kpi { margin: 10px 0; }
        .kpi span { display: inline-block; margin-right: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cfcfcf; padding: 4px 6px; vertical-align: top; }
        th { background: #f0f0f0; font-weight: 700; text-align: left; }
        .serie { margin-top: 12px; font-weight: 700; font-size: 12px; }
        .num { text-align: right; }
    </style>
</head>
<body>
    <div class="head">
        <h2>Cierre Diario de Arqueos de Pagos</h2>
        <p class="meta">Fecha: <?php echo html_escape($fecha); ?> | Registros: <?php echo count($rows); ?></p>
    </div>

    <div class="kpi">
        <span><strong>Aplicados:</strong> <?php echo intval(isset($totales['aplicados_count']) ? $totales['aplicados_count'] : 0); ?> ($<?php echo number_format(isset($totales['aplicado']) ? floatval($totales['aplicado']) : 0, 2); ?>)</span>
        <span><strong>Anulados:</strong> <?php echo intval(isset($totales['anulados_count']) ? $totales['anulados_count'] : 0); ?> ($<?php echo number_format(isset($totales['anulado']) ? floatval($totales['anulado']) : 0, 2); ?>)</span>
        <span><strong>Total General:</strong> $<?php echo number_format(isset($totales['general']) ? floatval($totales['general']) : 0, 2); ?></span>
    </div>

    <?php if (empty($grupos)): ?>
        <p>No hay registros para esta fecha.</p>
    <?php else: ?>
        <?php foreach ($grupos as $serie => $items): ?>
            <?php $subtotal = 0; foreach ($items as $it) { $subtotal += isset($it->monto) ? floatval($it->monto) : 0; } ?>
            <div class="serie">Serie <?php echo html_escape($serie); ?> | Subtotal $<?php echo number_format($subtotal, 2); ?></div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Beneficiario</th>
                        <th>Concepto</th>
                        <th class="num">Monto</th>
                        <th class="num">Recibido</th>
                        <th>Referencia</th>
                        <th>Prestamo/Cuota</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $i => $it): ?>
                        <?php
                            $estadoRow = isset($it->estado) ? strtolower(trim($it->estado)) : '';
                            $estadoLabel = ($estadoRow === 'aplicado_pendiente_arqueo') ? 'Revisado' : (($estadoRow === 'anulado') ? 'Anulado' : ucfirst($estadoRow));
                            $prestamoCuota = (isset($it->idprestamo) ? 'P#' . $it->idprestamo : '-') . ' / ' . (isset($it->idcuota) ? 'C#' . $it->idcuota : '-');
                        ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo html_escape(isset($it->fecha_recepcion) && !empty($it->fecha_recepcion) ? $it->fecha_recepcion : (isset($it->fecha) ? $it->fecha : '-')); ?></td>
                            <td><?php echo html_escape(isset($it->beneficiario) ? $it->beneficiario : '-'); ?></td>
                            <td><?php echo html_escape(isset($it->concepto) ? $it->concepto : '-'); ?></td>
                            <td class="num">$<?php echo number_format(isset($it->monto) ? floatval($it->monto) : 0, 2); ?></td>
                            <td class="num">$<?php echo number_format(isset($it->monto_recibido) ? floatval($it->monto_recibido) : 0, 2); ?></td>
                            <td><?php echo html_escape(isset($it->documento_numero) ? $it->documento_numero : '-'); ?></td>
                            <td><?php echo html_escape($prestamoCuota); ?></td>
                            <td><?php echo html_escape($estadoLabel); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
