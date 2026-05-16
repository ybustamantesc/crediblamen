<html>
<head>
        <meta charset="utf-8">
        <title>Aprobaciones - Solicitud</title>
        <style>
        @page { margin: 10mm 12mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; margin:0; padding:0 0 18mm 0; }
        .header-dark { padding:14px 16px; color:#fff; }
        .header-inner { width:100%; }
        .title { font-size:18px; font-weight:700; letter-spacing:1px; margin:6px 0 8px 0; }
        .sub { font-size:11px; color:#fff; margin-bottom:8px; }
        .logo { float:right; }
        .clear { clear:both; }
        .card { border: 1px solid #e2e2e2; border-radius: 6px; padding: 12px; margin-bottom: 10px; background:#fff; }
        .role { font-weight: 700; }
        .user { font-size: 12px; color: #444; }
        .date { font-size: 11px; color: #777; }
        .comment { margin-top: 8px; white-space: pre-wrap; }
        footer { display:block; position:static; clear:both; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:6px; margin:8px 12mm 6mm 12mm; }
        .footer-item { display:inline-block; margin-right:18px; }
        img.logo-img { max-height:60px; }
        .status-badge { display:inline-block; padding:6px 10px; border-radius:4px; color:#fff; font-weight:700; margin-top:6px; }
        </style>
        <?php
        // prepare logo data URI for embedding (reuse same logic as print_solicitud_pdf)
        $logo_uri = '';
        $possible_logos = [
            FCPATH . 'public/img/logo.png',
            FCPATH . 'public/img/logo.jpg',
            FCPATH . 'public/img/credi_socios_logo.png',
            FCPATH . 'public/img/credi_socios_logo.jpg'
        ];
        foreach ($possible_logos as $p) {
            if (file_exists($p)) {
                $m = mime_content_type($p);
                $data = base64_encode(file_get_contents($p));
                $logo_uri = 'data:' . $m . ';base64,' . $data;
                break;
            }
        }
        ?>
</head>
<body>
    <?php
    if (!function_exists('sc_fmt_rate')) {
        function sc_fmt_rate($v) {
            if ($v === null || $v === '') return '-';
            // coerce to float (accept comma decimal separators)
            $fv = floatval(str_replace(',', '.', $v));
            if ($fv == 0) return '0';
            // if stored as fraction (0.08) convert to percent
            if (abs($fv) < 1) $pct = $fv * 100.0; else $pct = $fv;
            // format up to 4 decimal places then trim trailing zeros
            $s = sprintf('%.4f', $pct);
            $s = rtrim(rtrim($s, '0'), '.');
            return $s;
        }
    }
    ?>
        <div class="header-dark" style="background:<?php echo isset($status_color) ? $status_color : '#0b3d91'; ?>;">
                <div class="header-inner">
                        <div style="float:left; max-width:65%">
                                <div class="title"><?php echo isset($status_label) ? htmlspecialchars($status_label) : 'Aprobaciones'; ?> - Solicitud #<?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?></div>
                                <div class="sub">Cliente: <?php echo isset($solicitud->apellidos) || isset($solicitud->nombres) ? trim((isset($solicitud->apellidos)?$solicitud->apellidos:'') . ' ' . (isset($solicitud->nombres)?$solicitud->nombres:'')) : ''; ?> &nbsp;|&nbsp; Generado: <?php echo isset($generated_at) ? $generated_at : ''; ?></div>
                                <div><span class="status-badge" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.15);"><?php echo isset($status_label) ? htmlspecialchars($status_label) : ''; ?></span></div>
                        </div>
                        <div class="logo" style="float:right; margin-left:10px;">
                                <?php if (!empty($logo_uri)): ?>
                                    <img class="logo-img" src="<?php echo $logo_uri; ?>" alt="logo" />
                                <?php else: ?>
                                    <?php
                                        $logo_file = '';
                                        foreach (array(FCPATH . 'public/img/logo.png', FCPATH . 'public/img/logo.jpg', FCPATH . 'public/img/credi_socios_logo.png', FCPATH . 'public/img/credi_socios_logo.jpg') as $lf) {
                                            if (file_exists($lf)) { $logo_file = $lf; break; }
                                        }
                                        if (!empty($logo_file)) echo '<img class="logo-img" src="' . htmlspecialchars($logo_file) . '" alt="logo">';
                                    ?>
                                <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                </div>
        </div>

    <div>
        <?php if (!empty($propuestas)) : ?>
            <!-- Solicitado: shows what the client originally requested -->
            <div class="card">
                <div style="font-weight:700; margin-bottom:8px;">Solicitado</div>
                <div style="margin-bottom:6px;">
                    <div><strong>Monto:</strong> <?php echo isset($requested['monto']) ? '$' . number_format($requested['monto'], 2) : '-'; ?></div>
                    <div><strong>Plazo (meses):</strong> <?php echo isset($requested['plazo']) ? intval($requested['plazo']) : '-'; ?></div>
                    <div><strong>Tasa mensual:</strong> <?php echo isset($requested['tasa']) ? sc_fmt_rate($requested['tasa']) : '-'; ?></div>
                    <div><strong>Comisión desembolso (%):</strong> <?php echo isset($requested['comision']) ? sc_fmt_rate($requested['comision']) : '-'; ?></div>
                </div>
            </div>

            <!-- Aprobado: shows what was approved (persisted propuestas) -->
            <div class="card">
                <div style="font-weight:700; margin-bottom:8px;">Aprobado</div>
                <div style="margin-bottom:6px;">
                    <div><strong>Total aprobado (suma propuestas):</strong> <?php echo isset($approved_total) ? '$' . number_format($approved_total, 2) : '$0.00'; ?></div>
                </div>

                <div style="font-weight:700; margin-top:8px; margin-bottom:6px;">Detalle por propuesta (aprobado)</div>
                <?php foreach ($propuestas as $p) : ?>
                    <div style="margin-bottom:6px;">
                        <div><strong>Producto ID:</strong> <?php echo isset($p->idtipo_producto) ? $p->idtipo_producto : (isset($p->id) ? $p->id : ''); ?></div>
                        <div><strong>Monto:</strong> <?php echo isset($p->monto) && $p->monto !== null && $p->monto !== '' ? '$' . number_format($p->monto, 2) : '-'; ?></div>
                        <div><strong>Plazo (meses):</strong> <?php echo isset($p->plazo_max) ? $p->plazo_max : (isset($p->plazo_min)?$p->plazo_min:''); ?></div>
                        <div><strong>Tasa mensual:</strong> <?php echo (isset($p->tasa) && $p->tasa !== null && $p->tasa !== '') ? sc_fmt_rate($p->tasa) : ((isset($p->tasa_mensual) && $p->tasa_mensual !== null && $p->tasa_mensual !== '') ? sc_fmt_rate($p->tasa_mensual) : '-'); ?></div>
                        <div><strong>Comisión desembolso (%):</strong> <?php echo (isset($p->comision_desembolso) && $p->comision_desembolso !== null && $p->comision_desembolso !== '') ? sc_fmt_rate($p->comision_desembolso) : '-'; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <br/>
        <?php endif; ?>

        <?php if (!empty($aprobaciones)) : ?>
            <?php foreach ($aprobaciones as $a) : ?>
                <?php
                // Extract photo path from comment if present
                $comment_text = isset($a->comment) ? $a->comment : '';
                $photo_path = null;
                if (preg_match('/\[foto:([^\]]+)\]/', $comment_text, $matches)) {
                    $photo_path = $matches[1];
                    // Remove the [foto:path] tag from displayed comment
                    $comment_text = trim(preg_replace('/\[foto:[^\]]+\]/', '', $comment_text));
                }
                ?>
                <div class="card">
                    <div><span class="role"><?php echo htmlspecialchars($a->role); ?></span></div>
                    <div class="d-flex justify-content-between">
                        <div class="user"><?php echo htmlspecialchars($a->username ?: 'Sistema'); ?></div>
                        <div class="date"><?php echo isset($a->created_at) ? $a->created_at : ''; ?></div>
                    </div>
                    <?php if (!empty($a->aprobado_por)) : ?>
                        <div style="margin-top:6px;"><strong>Procesado por :</strong> <?php echo htmlspecialchars($a->aprobado_por); ?></div>
                    <?php endif; ?>
                    <div class="comment"><?php echo nl2br(htmlspecialchars($comment_text)); ?></div>
                    
                    <?php if ($photo_path) : ?>
                        <?php
                        // Build full path and convert to data URI for embedding
                        $photo_full_path = FCPATH . 'uploads/' . $photo_path;
                        $photo_uri = '';
                        if (file_exists($photo_full_path)) {
                            try {
                                $mime = mime_content_type($photo_full_path);
                                $data = base64_encode(file_get_contents($photo_full_path));
                                $photo_uri = 'data:' . $mime . ';base64,' . $data;
                            } catch (Exception $e) {
                                // Ignore if cannot read photo
                            }
                        }
                        ?>
                        <?php if ($photo_uri) : ?>
                            <div style="margin-top:12px; border-top:1px solid #e2e2e2; padding-top:12px;">
                                <div style="font-weight:700; margin-bottom:6px;">Evidencia fotográfica:</div>
                                <img src="<?php echo $photo_uri; ?>" alt="Foto de aprobación" style="max-width:100%; max-height:400px; border:1px solid #ddd; padding:4px;" />
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="card">No hay aprobaciones registradas.</div>
        <?php endif; ?>
    </div>

    <div class="footer">Emitido por: <?php echo isset($generated_by) && $generated_by !== '' ? htmlspecialchars($generated_by) : 'Sistema'; ?> &nbsp;|&nbsp; Fecha: <?php echo isset($generated_at) ? htmlspecialchars($generated_at) : ''; ?></div>
</body>
</html>