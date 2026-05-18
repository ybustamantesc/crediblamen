<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Comprobante de Diario - <?php echo isset($empresa->razon_social) ? htmlspecialchars($empresa->razon_social) : ''; ?></title>
    <style>
        @page { margin:18mm 12mm; }
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color:#111; font-size:13px; margin:0; }
        .wrap { width:820px; max-width:100%; margin:12px auto; padding:12px; box-sizing:border-box; }
        .header { text-align:center; margin-bottom:6px; }
        .header .company { font-size:16px; font-weight:700; }
        .header .title { margin-top:6px; font-size:15px; font-weight:700; }
        .meta { display:flex; justify-content:space-between; margin-top:8px; gap:12px; }
        .box { border:1px solid #000; padding:8px; }
        /* Elegant table: external border + vertical separators between columns; no horizontal internal lines */
        table { width:100%; border-collapse:separate; border-spacing:0; margin-top:10px; font-size:12px; border:1px solid #000; }
        th, td { padding:8px 10px; border-left:1px solid #000; }
        th:first-child, td:first-child { border-left: none; }
        th { background:#f7f7f7; font-weight:700; text-align:left; }
        tbody tr td { border-top: none; border-bottom: none; }
        /* Totals row: show top border and bolder text */
        .totals-row th, .totals-row td { border-top:1px solid #000; font-weight:700; background:transparent; }
        /* Align numeric columns */
        .right { text-align:right; }
        .center { text-align:center; }
        .right { text-align:right; }
        .center { text-align:center; }
        .no-border { border: none; }
        /* Larger signature area to allow manual signing */
        .sig-row { margin-top:28px; display:flex; justify-content:space-between; align-items:flex-end; min-height:180px; }
        .sig { width:30%; text-align:center; border-top:1px solid #000; padding-top:14px; font-weight:600; }
        @media print {
            .wrap { padding:8mm; }
            .sig-row { min-height:200px; }
        }
    </style>
</head>
<body>
    <?php
        function _mes_es($ts){
            $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            $d = date('d', $ts);
            $m = $meses[intval(date('n', $ts)) - 1];
            $y = date('Y', $ts);
            return $d.' de '.ucfirst($m).' de '.$y;
        }
        $fecha_ts = strtotime($header->date);
        $fecha_es = $fecha_ts ? _mes_es($fecha_ts) : '';
    ?>
    <div class="wrap">
        <div class="header">
              <!-- Title removed as requested -->
            <div style="margin-top:6px;font-size:14px;font-weight:700;">COMPROBANTE DE DIARIO</div>
        </div>

        <?php
            // obtain 'tasa de cambio compra' for the journal date using TasaCambio_model
            $tasa = null;
            if (!empty($header->date)) {
                $CI = &get_instance();
                if (!isset($CI->TasaCambio_model)) {
                    $CI->load->model('TasaCambio_model');
                }
                $tasa = $CI->TasaCambio_model->get_tasa_vigente($header->date, 'compra');
                $tasa = $tasa !== null ? floatval($tasa) : null;
            }
            $display_doc_type = '';
            if (!empty($header->entry_type)) $display_doc_type = $header->entry_type;
            elseif (!empty($header->document_type)) $display_doc_type = $header->document_type;
            else $display_doc_type = $header->description;
        ?>
        <div class="meta">
            <div style="flex:1; margin-right:10px;">
                <div><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($display_doc_type); ?></div>
                <div style="margin-top:6px;"><strong>Comentario:</strong> <?php echo htmlspecialchars($header->description); ?></div>
                <div style="margin-top:6px;"><strong>Tipo de Cambio:</strong> <?php echo $tasa !== null ? number_format($tasa,4,',','.') : 'N/A'; ?></div>
            </div>
            <div style="width:220px;text-align:right;">
                <div><strong>Documento No.:</strong> <?php echo sprintf('%09d', $header->id); ?></div>
                <div style="margin-top:6px;"><strong>Fecha:</strong> <?php echo htmlspecialchars($fecha_es); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:80px">Código</th>
                    <th style="width:90px">Tipo Doc</th>
                    <th style="width:120px">Centro Costo</th>
                    <th>Nombre</th>
                    <th>Comentario</th>
                    <th style="width:100px">Tipo Cambio</th>
                    <th style="width:120px" class="right">Débito</th>
                    <th style="width:120px" class="right">Crédito</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $ln): ?>
                <tr>
                    <td class="center"><?php echo htmlspecialchars($ln->code); ?></td>
                    <td class="center"><?php echo htmlspecialchars(!empty($ln->doc_type) ? $ln->doc_type : (!empty($ln->document_type) ? $ln->document_type : (!empty($ln->documento) ? $ln->documento : (!empty($header->document_type) ? $header->document_type : '')))); ?></td>
                    <td class="center"><?php echo htmlspecialchars(!empty($ln->centro_costo) ? $ln->centro_costo : (!empty($ln->cost_center) ? $ln->cost_center : (!empty($ln->centro) ? $ln->centro : ''))); ?></td>
                    <td><?php echo htmlspecialchars($ln->name); ?></td>
                    <td><?php echo htmlspecialchars($ln->line_description); ?></td>
                    <td class="center"><?php echo $tasa !== null ? number_format($tasa,4,',','.') : '-'; ?></td>
                    <td class="right"><?php echo $ln->debit > 0 ? number_format($ln->debit,2,',','.') : '-'; ?></td>
                    <td class="right"><?php echo $ln->credit > 0 ? number_format($ln->credit,2,',','.') : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <th class="no-border" colspan="6" style="text-align:right;">Totales</th>
                    <th class="right"><?php echo number_format($header->total_debit,2,',','.'); ?></th>
                    <th class="right"><?php echo number_format($header->total_credit,2,',','.'); ?></th>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top:10px; font-weight:600; text-align:center;">
            <?php if (abs($header->total_debit - $header->total_credit) < 0.01): ?>
                ASIENTO CUADRADO
            <?php else: ?>
                DIFERENCIA: <?php echo number_format(abs($header->total_debit - $header->total_credit),2,',','.'); ?>
            <?php endif; ?>
        </div>

        <div class="sig-row">
            <div class="sig">Firma Elaboró</div>
            <div class="sig">Firma Revisó</div>
            <div class="sig">Firma Aprobó</div>
        </div>

    </div>
</body>
</html>
