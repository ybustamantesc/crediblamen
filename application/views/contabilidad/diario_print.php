<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Comprobante de Diario - <?php echo isset($empresa->razon_social) ? htmlspecialchars($empresa->razon_social) : ''; ?></title>
    <style>
        @page { margin:10mm 8mm; }
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color:#111; font-size:11px; margin:0; }
        .wrap { width:100%; max-width:100%; margin:0 auto; padding:0; box-sizing:border-box; }
        .header { text-align:center; margin-bottom:16px; }
        .header .company { font-size:15px; font-weight:700; }
        .header .title { margin-top:6px; font-size:13px; font-weight:700; }
        .header > div:first-child { background-color:#0b3d91; color:#fff; padding:8px; border-radius:3px; }
        .meta { display:flex; justify-content:space-between; margin-top:12px; margin-bottom:16px; gap:16px; font-size:10px; }
        .meta > div { flex:1; }
        .meta-left { flex:2; }
        .meta-right { flex:1; text-align:right; }
        .box { border:1px solid #000; padding:6px; }
        /* Elegant table: external border + vertical separators between columns; no horizontal internal lines */
        table { width:100%; border-collapse:separate; border-spacing:0; margin-top:12px; margin-bottom:12px; font-size:10px; border:1px solid #000; }
        th, td { padding:6px 5px; border-left:1px solid #000; }
        th:first-child, td:first-child { border-left: none; }
        th { background:#0b3d91; color:#fff; font-weight:700; text-align:left; font-size:10px; }
        tbody tr td { border-top: none; border-bottom: none; }
        /* Totals row: show top border and bolder text */
        .totals-row th, .totals-row td { border-top:1px solid #000; font-weight:700; background:#0b3d91; color:#fff; }
        .totals-row td.right { color: #fff; }
        /* Align numeric columns */
        .right { text-align:right; }
        /* Meta section: clean label styling */
        .meta strong { font-weight:700; }
        .center { text-align:center; }
        .no-border { border: none; }
        /* Status area */
        .status-area { margin-top:15px; margin-bottom:30px; font-weight:600; text-align:center; font-size:12px; }
        /* Larger signature area to allow manual signing */
        .sig-table { width:100%; border-collapse:collapse; margin-top:60px; border:none; }
        .sig-cell { width:33.33%; text-align:center; padding:0 10px; border:none; }
        .sig-line { border-top:1px solid #000; height:40px; margin-bottom:-6px; }
        .sig-label { font-weight:600; font-size:13px; padding:0; }
        @media print {
            .wrap { padding:0; }
            .sig-line { height:50px; }
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
            <div style="font-size:14px;font-weight:700;">COMPROBANTE DE DIARIO</div>
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
            <div class="meta-left">
                <div><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($display_doc_type); ?></div>
                <div style="margin-top:8px;"><strong>Comentario:</strong> <?php echo htmlspecialchars($header->description); ?></div>
                <div style="margin-top:8px;"><strong>Tipo de Cambio:</strong> <?php echo $tasa !== null ? number_format($tasa,4,'.',',') : 'N/A'; ?></div>
            </div>
            <div class="meta-right">
                <div><strong>Documento No.:</strong> <?php echo sprintf('%09d', $header->id); ?></div>
                <div style="margin-top:8px;"><strong>Fecha:</strong> <?php echo htmlspecialchars($fecha_es); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:13%">Código</th>
                    <th style="width:17%">Nombre</th>
                    <th style="width:11%">Centro Costo</th>
                    <th style="width:17%">Comentario</th>
                    <th style="width:8%" class="right">Débito NIO</th>
                    <th style="width:8%" class="right">Débito USD</th>
                    <th style="width:8%" class="right">Crédito NIO</th>
                    <th style="width:10%" class="right">Crédito USD</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $ln):
                    $debit_usd = $tasa !== null && $tasa > 0 ? floatval($ln->debit) / $tasa : 0.0;
                    $credit_usd = $tasa !== null && $tasa > 0 ? floatval($ln->credit) / $tasa : 0.0;
                ?>
                <tr>
                    <td class="center"><?php echo htmlspecialchars($ln->code); ?></td>
                    <td><?php echo htmlspecialchars($ln->name); ?></td>
                    <td class="center"><?php
                        $centro_costo = '';
                        if (!empty($ln->centro_costo_codigo) || !empty($ln->centro_costo_nombre)) {
                            $centro_costo = trim((!empty($ln->centro_costo_codigo) ? $ln->centro_costo_codigo : '') .
                                (!empty($ln->centro_costo_codigo) && !empty($ln->centro_costo_nombre) ? ' - ' : '') .
                                (!empty($ln->centro_costo_nombre) ? $ln->centro_costo_nombre : ''));
                        }
                        echo htmlspecialchars($centro_costo);
                    ?></td>
                    <td><?php echo htmlspecialchars($ln->line_description); ?></td>
                    <td class="right"><?php echo $ln->debit > 0 ? 'C$ ' . number_format($ln->debit,2,'.',',') : '-'; ?></td>
                    <td class="right"><?php echo $ln->debit > 0 ? '$ ' . number_format($debit_usd,2,'.',',') : '-'; ?></td>
                    <td class="right"><?php echo $ln->credit > 0 ? 'C$ ' . number_format($ln->credit,2,'.',',') : '-'; ?></td>
                    <td class="right"><?php echo $ln->credit > 0 ? '$ ' . number_format($credit_usd,2,'.',',') : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <th class="no-border" colspan="4" style="text-align:right;">Totales</th>
                    <th class="right"><?php echo 'C$ ' . number_format($header->total_debit,2,'.',','); ?></th>
                    <th class="right"><?php echo '$ ' . number_format($tasa !== null && $tasa > 0 ? floatval($header->total_debit) / $tasa : 0.0,2,'.',','); ?></th>
                    <th class="right"><?php echo 'C$ ' . number_format($header->total_credit,2,'.',','); ?></th>
                    <th class="right"><?php echo '$ ' . number_format($tasa !== null && $tasa > 0 ? floatval($header->total_credit) / $tasa : 0.0,2,'.',','); ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="status-area">
            <?php if (abs($header->total_debit - $header->total_credit) < 0.01): ?>
                ASIENTO CUADRADO
            <?php else: ?>
                DIFERENCIA: <?php echo number_format(abs($header->total_debit - $header->total_credit),2,',','.'); ?>
            <?php endif; ?>
        </div>

        <table class="sig-table">
            <tr>
                <td class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-label">Firma Elaboró</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-label">Firma Revisó</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-label">Firma Aprobó</div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>
