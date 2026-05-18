<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balanza de Comprobación</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #222; font-size: 10px; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .company { font-size: 14px; font-weight: 700; margin-bottom: 3px; }
        .title { font-size: 12px; font-weight: 700; margin-bottom: 3px; }
        .period { font-size: 10px; color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        thead th { background: #0070C0; color: #fff; font-weight: 700; text-align: center; }
        tfoot th { background: #0070C0; color: #fff; font-weight: 700; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .negative { color: #c00; }
        .sign-block { margin-top: 18px; }
        .sign-block .sign-box { text-align:center; width:33%; }
        .sign-block .sign-box .line { border-top:1px solid #000; width:70%; margin:0 auto 6px; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="header">
            <div class="title" style="font-size:16px;font-weight:700;">Balanza de comprobacion</div>
        <div class="period">
            Período: <?php echo ($start ? date('d/m/Y', strtotime($start)) : 'Inicio'); ?> 
            al <?php echo ($end ? date('d/m/Y', strtotime($end)) : 'Final'); ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:12%">Código</th>
                <th style="width:40%">Denominación</th>
                <th style="width:12%" class="text-right">Mayor</th>
                <th style="width:12%" class="text-right">Cargos</th>
                <th style="width:12%" class="text-right">Abonos</th>
                <th style="width:12%" class="text-right">Saldo Actual</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['rows'])): foreach ($data['rows'] as $r): 
                $mayor = $r['opening_deudor'] - $r['opening_acreedor'];
                $saldo_actual = $mayor + $r['debits'] - $r['credits'];
            ?>
            <tr>
                <td class="text-left"><?php echo htmlspecialchars($r['code']); ?></td>
                <td class="text-left"><?php echo htmlspecialchars($r['name']); ?></td>
                <td class="text-right"><?php echo number_format($mayor, 2); ?></td>
                <td class="text-right"><?php echo number_format($r['debits'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['credits'], 2); ?></td>
                <td class="text-right <?php echo $saldo_actual < 0 ? 'negative' : ''; ?>"><?php echo number_format($saldo_actual, 2); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center">No hay datos para el rango seleccionado.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <?php 
                $total_mayor = $data['totals']['opening_deudor'] - $data['totals']['opening_acreedor'];
                $total_cargos = $data['totals']['debits'];
                $total_abonos = $data['totals']['credits'];
                $total_saldo = $total_mayor + $total_cargos - $total_abonos;
            ?>
            <tr>
                <th colspan="2" class="text-left">TOTALES</th>
                <th class="text-right"><?php echo number_format($total_mayor, 2); ?></th>
                <th class="text-right"><?php echo number_format($total_cargos, 2); ?></th>
                <th class="text-right"><?php echo number_format($total_abonos, 2); ?></th>
                <th class="text-right"><?php echo number_format($total_saldo, 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="sign-block no-break" style="margin-top:18px;">
        <table style="width:100%; border-collapse:collapse; margin-top:12px; border:0;">
            <tr>
                <td style="width:33%; text-align:center; border:0; vertical-align:bottom; padding-top:14px;">
                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                    <div style="font-weight:700;">Contador General</div>
                </td>
                <td style="width:33%; text-align:center; border:0; vertical-align:bottom; padding-top:14px;">
                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                    <div style="font-weight:700;">Gerente Financiero</div>
                </td>
                <td style="width:33%; text-align:center; border:0; vertical-align:bottom; padding-top:14px;">
                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                    <div style="font-weight:700;">Gerente General</div>
                </td>
            </tr>
        </table>
    </div>

<?php /** Add page numbers and watermark */ ?>
<script type="text/php">
    if (isset(\$pdf)) {
        \$font = \$fontMetrics->get_font("DejaVu Sans", "normal");
        \$size = 9;
        \$y = \$pdf->get_height() - 30;
        \$x_center = \$pdf->get_width() / 2;
        \$x_right = \$pdf->get_width() - 50;
        
        // Page number
        \$pageText = "Página {PAGE_NUM} de {PAGE_COUNT}";
        \$textWidth = \$fontMetrics->get_text_width(\$pageText, \$font, \$size);
        \$pdf->text(\$x_center - (\$textWidth / 2), \$y, \$pageText, \$font, \$size, [0.5, 0.5, 0.5]);
        
        // Watermark in center
        \$watermark = "Página {PAGE_NUM}";
        \$wfont = \$fontMetrics->get_font("DejaVu Sans", "bold");
        \$wsize = 60;
        \$wtext_width = \$fontMetrics->get_text_width(\$watermark, \$wfont, \$wsize);
        \$wx = (\$pdf->get_width() - \$wtext_width) / 2;
        \$wy = \$pdf->get_height() / 2;
        \$pdf->text(\$wx, \$wy, \$watermark, \$wfont, \$wsize, [0.9, 0.9, 0.9]);
    }
</script>
</body>
</html>
