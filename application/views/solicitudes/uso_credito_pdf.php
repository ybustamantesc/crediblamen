<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Formato Uso de Crédito - Solicitud <?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?></title>
    <style>
        /* Tighter page and element spacing to better use vertical space */
        @page { margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#222; margin:0; padding:0; }
        .header-dark { background:#0b3d91; color:#fff; padding:10px 12px; }
        .header-inner { width:100%; }
        .title { font-size:16px; font-weight:700; letter-spacing:1px; margin:4px 0 6px 0; }
        .sub { font-size:10px; color:#ddd; margin-bottom:6px; }
        .logo { float:right; }
        .clear { clear:both; }

        .section-title { margin-top:8px; font-size:11px; font-weight:700; color:#fff; background:#0b3d91; padding:6px 8px; page-break-after: avoid; break-after: avoid; }

        .section { margin-bottom: 8px; padding:8px 6px; page-break-inside: avoid; break-inside: avoid; }
        .section.section-break-before { page-break-before: always; break-before: page; }
        .label { font-weight:700; display:block; margin-bottom:4px; color:#222; }
        .box { border:1px solid #e6e6e6; padding:8px; background:#fff; line-height:1.35; min-height:26px; word-wrap:break-word; white-space:pre-wrap; box-sizing:border-box; page-break-inside: avoid; break-inside: avoid; }
        .decl-box { font-size:11px; line-height:1.35; word-wrap:break-word; overflow-wrap:break-word; hyphens:auto; max-width:100%; box-sizing:border-box; }
        .decl-box p { margin:0 0 6px 0; }
        .cols { width:100%; border-collapse:collapse; }
        .cols .col { vertical-align:top; padding:0; }
        .cols .col-inner { padding-left:0; padding-right:0; }
        .col-50 { width:50%; }
        .small { font-size:10px; color:#555; }
        p { margin: 0 0 6px 0; }
        .detail { margin-bottom:4px; }
        .meta { font-size:10px; color:#666; }
        footer { display:block; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:4px; margin-top:12px; }
        img.logo-img { max-height:50px; }
    </style>
</head>
<body>
    <div class="header-dark">
        <div class="header-inner">
            <div style="float:left; max-width:75%">
                <div class="title">FORMATO DE USO DEL CRÉDITO</div>
                <div class="sub">Solicitud ID: <?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?> - Cliente: <?php echo isset($solicitud->nombres) ? trim($solicitud->nombres . ' ' . $solicitud->apellidos) : ''; ?></div>
            </div>
                        <div class="logo">
                                <?php
                                    // prefer same logo order as Solicitud Inicial and use data URI for dompdf
                                    $logo_uri = '';
                                    $logo_paths = [
                                        FCPATH . 'public/img/logo.png',
                                        FCPATH . 'public/img/logo.jpg',
                                        FCPATH . 'public/img/credi_socios_logo.png',
                                        FCPATH . 'public/img/credi_socios_logo.jpg'
                                    ];
                                    foreach ($logo_paths as $p) {
                                            if (file_exists($p)) { $m = mime_content_type($p); $data = base64_encode(file_get_contents($p)); $logo_uri = 'data:'.$m.';base64,'.$data; break; }
                                    }
                                    if (!empty($logo_uri)) {
                                            echo '<img class="logo-img" src="'.htmlspecialchars($logo_uri).'" alt="logo">';
                                    }
                                ?>
                        </div>
            <div class="clear"></div>
        </div>
    </div>
    <!-- Applicant general info (from solicitud) -->
    <div class="section">
        <div class="section-title">INFORMACIÓN GENERAL DEL SOLICITANTE</div>
        <div class="box">
            <div class="detail"><strong>Nombre Completo:</strong> <?php echo isset($solicitud->nombres) ? htmlspecialchars(trim($solicitud->nombres . ' ' . $solicitud->apellidos)) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
            <div class="detail"><strong>Número de Identificación:</strong> <?php echo isset($solicitud->cedula) ? htmlspecialchars($solicitud->cedula) : (isset($solicitud->numero_identificacion) ? htmlspecialchars($solicitud->numero_identificacion) : '<span class="small text-muted">(sin datos)</span>'); ?></div>
            <div class="detail"><strong>Teléfono de Contacto:</strong> <?php echo isset($solicitud->telefono) ? htmlspecialchars($solicitud->telefono) : (isset($solicitud->celular) ? htmlspecialchars($solicitud->celular) : '<span class="small text-muted">(sin datos)</span>'); ?></div>
            <div class="detail"><strong>Fecha de Solicitud:</strong> <?php echo isset($solicitud->fecha_solicitud) ? htmlspecialchars($solicitud->fecha_solicitud) : (isset($solicitud->fecha_recepcion) ? htmlspecialchars($solicitud->fecha_recepcion) : '<span class="small text-muted">(sin datos)</span>'); ?></div>
        </div>
    </div>

    <!-- Solicitud specific metadata (Destino only) - other fields hidden per request -->
    <div class="section">
        <div class="section-title">INFORMACIÓN DE LA SOLICITUD</div>
        <div class="box">
            <div class="detail"><strong>Destino (Solicitud):</strong>
                <?php
                    $destino_sol = '';
                    if (!empty($solicitud->destino_credito)) $destino_sol = $solicitud->destino_credito;
                    elseif (!empty($solicitud->destino)) $destino_sol = $solicitud->destino;
                    elseif (!empty($uso->destino_prestamo)) $destino_sol = $uso->destino_prestamo;
                    echo $destino_sol !== '' ? htmlspecialchars($destino_sol) : '<span class="small text-muted">(sin datos)</span>';
                ?>
            </div>
            <!-- Rubro, firma, promotor, DDC and flags intentionally not shown -->
        </div>
    </div>

    <div class="section">
        <div class="section-title">DETALLE DEL USO DEL CRÉDITO SOLICITADO</div>
        <div class="box">
            <div class="detail"><strong>1. Monto Solicitado:</strong> <?php echo (isset($uso->monto_solicitado) && $uso->monto_solicitado !== null && trim((string)$uso->monto_solicitado) !== '') ? '$ ' . number_format($uso->monto_solicitado,2,',','.') : '<span class="small text-muted">(sin datos)</span>'; ?></div>
            <div class="detail" style="margin-top:6px;"><strong>2. Plazo Solicitado (en meses):</strong> <?php echo (isset($uso->plazo_solicitado) && $uso->plazo_solicitado !== null && trim((string)$uso->plazo_solicitado) !== '') ? htmlspecialchars($uso->plazo_solicitado) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">3. Destino del Préstamo</div>
        <div class="box">
            <div class="detail"><strong>Opción seleccionada:</strong>
                <?php
                    $destino_sol = '';
                    if (!empty($solicitud->destino_credito)) $destino_sol = $solicitud->destino_credito;
                    elseif (!empty($solicitud->destino)) $destino_sol = $solicitud->destino;
                    elseif (!empty($uso->destino_prestamo)) $destino_sol = $uso->destino_prestamo;
                    echo $destino_sol !== '' ? htmlspecialchars($destino_sol) : '<span class="small text-muted">(sin datos)</span>';
                    //$map = array('vivienda'=>'Mejoramiento de vivienda','educacion'=>'Educación','apertura_negocio'=>'Apertura o mejora de negocio','salud'=>'Salud','compra_bienes'=>'Compra de bienes o servicios','otros'=>'Otros');
                    //$sel = isset($uso->destino_prestamo) ? $uso->destino_prestamo : '';
                    //echo isset($map[$sel]) ? htmlspecialchars($map[$sel]) : '<span class="small text-muted">(sin datos)</span>';
                ?>
            </div>
            <div class="detail" style="margin-top:6px;"><strong>Detalle del uso:</strong> <?php echo isset($uso->destino_detalle) && trim($uso->destino_detalle) !== '' ? nl2br(htmlspecialchars($uso->destino_detalle)) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DESCRIPCIÓN DETALLADA DEL USO DEL CRÉDITO</div>
        <div class="box">
            <?php echo isset($uso->descripcion) && trim($uso->descripcion) !== '' ? nl2br(htmlspecialchars($uso->descripcion)) : '<span class="small text-muted">(sin datos)</span>'; ?>
        </div>
    </div>

    <div class="section section-break-before">
        <div class="section-title">PLAN DE PAGOS (si aplica)</div>
        <div class="box">
            <div class="detail"><strong>Fuente de ingreso para el pago:</strong> <?php echo isset($uso->fuente_ingreso) && trim($uso->fuente_ingreso) !== '' ? nl2br(htmlspecialchars($uso->fuente_ingreso)) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
            <div class="detail" style="margin-top:6px;"><strong>Monto estimado de ingresos mensuales:</strong> <?php echo (isset($uso->monto_estimado_mes) && $uso->monto_estimado_mes !== null && trim((string)$uso->monto_estimado_mes) !== '') ? '$ ' . number_format($uso->monto_estimado_mes,2,',','.') : '<span class="small text-muted">(sin datos)</span>'; ?></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Declaración y Autorización</div>
        <div class="box">
            <?php
                $fullName = isset($solicitud->nombres) ? trim($solicitud->nombres . ' ' . $solicitud->apellidos) : (isset($uso->declaracion_nombre) ? $uso->declaracion_nombre : '');
                $idnum = isset($solicitud->cedula) ? $solicitud->cedula : (isset($solicitud->identificacion) ? $solicitud->identificacion : '');
                // Build a prefix line (includes applicant name and ID) and wrap the remaining text
                $prefix_raw = 'Yo, ' . ($fullName ? $fullName : '________________________') . ' con Número de Identificación ' . ($idnum ? $idnum : '________________') . ', declaro bajo juramento que la información';
                $rest_raw = ' proporcionada en este formato es verídica y completa. Entiendo que la microfinanciera podrá verificar la veracidad de esta información y utilizarla para la evaluación de mi solicitud de crédito. Asimismo, autorizo a la microfinanciera a utilizar los datos proporcionados para fines de análisis y evaluación crediticia.';
                // Use the number of characters in the prefix as the desired wrap width (measured in characters)
                $wrap_width = mb_strlen($prefix_raw);
                $wrapped = wordwrap($rest_raw, $wrap_width, "\n", true);
                $decl_par = htmlspecialchars($prefix_raw) . '<br/>' . nl2br(htmlspecialchars($wrapped));
                echo '<div class="decl-box"><p>' . $decl_par . '</p></div>';
            ?>
            <div style="margin-top:12px;">
                <div style="display:block; margin-bottom:18px;">Firma del Solicitante: <?php echo isset($solicitud->nombres) ? trim($solicitud->nombres . ' ' . $solicitud->apellidos) : (isset($uso->declaracion_nombre) ? $uso->declaracion_nombre : '') ?> </div>
                <div>Fecha de firma: <?php echo isset($solicitud->fecha_firma) ? htmlspecialchars($solicitud->fecha_firma) : (isset($solicitud->fecha_firma_solicitud) ? htmlspecialchars($solicitud->fecha_firma_solicitud) : '<span class="small text-muted">(sin datos)</span>'); ?></div>
            </div>
        </div>
    </div>

    <div class="section">
        <table class="cols">
            <tr>
                <td class="col col-50">
                                <div class="col-inner">
                                    <div class="section-title">Uso interno de la microfinanciera - Evaluador de Crédito</div>
                                    <div class="box"><?php echo isset($uso->evaluador_credito) && trim($uso->evaluador_credito) !== '' ? htmlspecialchars($uso->evaluador_credito) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
                                </div>
                </td>
                <td class="col col-50">
                    <div class="col-inner">
                        <div class="section-title">Fecha de Evaluación</div>
                        <div class="box"><?php echo isset($uso->fecha_evaluacion) && trim($uso->fecha_evaluacion) !== '' ? htmlspecialchars($uso->fecha_evaluacion) : '<span class="small text-muted">(sin datos)</span>'; ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section small" style="margin-top:18px;">
        Documento generado: <?php echo isset($generated_at) ? $generated_at : date('d/m/Y H:i'); ?>
    </div>
</body>
</html>
