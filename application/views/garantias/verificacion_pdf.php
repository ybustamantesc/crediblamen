<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verificación Garantía <?php echo isset($v->garantia_id) ? $v->garantia_id : ''; ?></title>
    <?php
    // prepare logo data URI
    $logo_uri = '';
    $logo_paths = [
        FCPATH . 'public/img/logo.png',
        FCPATH . 'public/img/logo.jpg',
        FCPATH . 'public/img/credi_socios_logo.png',
        FCPATH . 'public/img/credi_socios_logo.jpg'
    ];
    foreach ($logo_paths as $p) {
        if (file_exists($p)) {
            $m = mime_content_type($p);
            $data = base64_encode(file_get_contents($p));
            $logo_uri = 'data:' . $m . ';base64,' . $data;
            break;
        }
    }
    ?>
    <style>
        @page { margin: 10mm 12mm; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size:12px;
            color:#222;
            margin:0;
            padding:0 0 18mm 0;
            background:#fff;
        }
        .header-dark {
            background:#0b3d91;
            color:#fff;
            padding:15px 20px;
            border-radius:0px;
            margin-bottom:12px;
            position:relative;
            min-height:60px;
        }
        .header-left { display:inline-block; width:75%; vertical-align:top; padding-right:10px; color:#fff !important; }
        .header-right { display:inline-block; width:22%; text-align:right; vertical-align:middle; }
        .title { font-size:24px; font-weight:700; margin:0 0 4px 0; letter-spacing:0.5px; line-height:1.1; color:#fff !important; }
        .sub { font-size:12px; color:#fff !important; margin:0 0 2px 0; }
        .meta { font-size:10px; color:#fff !important; line-height:1.3; margin:0; }
        .header-right img { max-height:58px; display:inline-block; width:auto; }
        .header-row { color:#fff !important; display:block; }
        .header-clear { display:none; }
        .section-title {
            background:#0b3d91;
            color:#fff;
            font-size:12px;
            font-weight:700;
            padding:8px 10px;
            margin:16px 0 0 0;
            border-radius:3px;
        }
        .section-box {
            border:1px solid #dfe7f4;
            padding:12px 14px;
            margin-top:6px;
            border-radius:4px;
            background:#fbfdff;
            font-size:11px;
            line-height:1.5;
        }
        .field-row { margin-bottom:8px; }
        .field-row strong { display:inline-block; min-width:160px; color:#0b3d91; }
        .photos { display:block; margin-top:8px; }
        .photos::after { content: ""; display: table; clear: both; }
        .photos .photo-item {
            display: inline-block;
            width: 48%;
            margin-right: 4%;
            margin-bottom: 10px;
            vertical-align: top;
        }
        .photos .photo-item:nth-child(2n) { margin-right: 0; }
        .photos img { width: 100%; max-width: 100%; border: 1px solid #dcdcdc; padding: 2px; background: #fff; display: block; }
        footer {
            display:block;
            position:static;
            clear:both;
            font-size:10px;
            color:#444;
            text-align:left;
            border-top:1px solid #eee;
            padding-top:10px;
            margin:14px 12mm 12mm 12mm;
        }
        .footer-item { display:inline-block; margin-right:18px; }
    </style>
</head>
<body>
    <?php
        $cliente_nombre = 'N/A';
        $codigo_solicitud = '';
        if (! empty($solicitud)) {
            if (! empty($solicitud->nombre_completo)) {
                $cliente_nombre = $solicitud->nombre_completo;
            } else {
                $cliente_nombre = trim((string)($solicitud->nombres ?? '') . ' ' . ($solicitud->apellidos ?? '')) ?: 'N/A';
            }
            if (! empty($solicitud->codigo)) {
                $codigo_solicitud = $solicitud->codigo;
            } elseif (! empty($solicitud->idsolicitud)) {
                $codigo_solicitud = 'SOL-' . str_pad($solicitud->idsolicitud, 4, '0', STR_PAD_LEFT);
            } elseif (! empty($solicitud->id)) {
                $codigo_solicitud = 'SOL-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT);
            }
        }
    ?>

    <div class="header-dark">
        <div class="header-left">
            <div class="title">FORMATO DE VERIFICACIÓN</div>
            <div class="sub">Solicitud ID: <?php echo isset($v->solicitud_id) ? $v->solicitud_id : ''; ?> - Cliente: <?php echo htmlspecialchars($cliente_nombre); ?></div>
            <p class="meta">Garantía ID: <?php echo isset($v->garantia_id) ? $v->garantia_id : ''; ?></p>
        </div><div class="header-right">
            <?php if (!empty($logo_uri)): ?>
                <img src="<?php echo $logo_uri; ?>" alt="logo">
            <?php endif; ?>
        </div>
    </div>

    <div class="section-title">Detalle de verificación</div>
    <div class="section-box">
        <div class="field-row"><strong>Verificador:</strong> <?php echo isset($v->verificador_usuario) ? htmlspecialchars($v->verificador_usuario) : 'N/A'; ?></div>
        <div class="field-row"><strong>Fecha de verificación:</strong> <?php echo isset($v->created_at) ? htmlspecialchars($v->created_at) : 'N/A'; ?></div>
        <div class="field-row"><strong>Estado de la garantía:</strong> <?php echo htmlspecialchars(isset($v->estado) ? $v->estado : 'Pendiente'); ?></div>
        <div class="field-row"><strong>Código de garantía:</strong> <?php echo htmlspecialchars(isset($v->garantia_id) ? $v->garantia_id : ''); ?></div>
        <div class="field-row"><strong>Código de solicitud:</strong> <?php echo htmlspecialchars($codigo_solicitud ?: (isset($v->solicitud_id) ? $v->solicitud_id : '')); ?></div>
    </div>

    <?php if (!empty($verificaciones)): ?>
        <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse; margin-top:16px;">
            <thead>
                <tr style="background:#0b3d91;color:#fff;">
                    <th align="left" style="border:1px solid #dcdcdc;">Garantía</th>
                    <th align="left" style="border:1px solid #dcdcdc;">Comentario</th>
                    <th align="left" style="border:1px solid #dcdcdc;">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($verificaciones as $row): ?>
                    <tr>
                        <td style="border:1px solid #dcdcdc; vertical-align:top;"><?php echo htmlspecialchars(isset($row->nombre_garantia) ? $row->nombre_garantia : ('Garantía ' . (isset($row->garantia_id) ? $row->garantia_id : ''))); ?></td>
                        <td style="border:1px solid #dcdcdc; vertical-align:top;"><?php echo nl2br(htmlspecialchars(isset($row->comentario) ? $row->comentario : '')); ?></td>
                        <td style="border:1px solid #dcdcdc; vertical-align:top;"><?php echo htmlspecialchars(isset($row->estado_aprobacion) ? $row->estado_aprobacion : 'Pendiente'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="section-title">Fotos evidenciales</div>
    <div class="section-box">
        <?php if (!empty($imgs)): ?>
            <div class="photos">
                <?php foreach ($imgs as $img): ?>
                    <div class="photo-item"><img src="<?php echo $img; ?>" alt="Foto evidencia"></div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div>No hay fotos adjuntas.</div>
        <?php endif; ?>
    </div>

    <footer>
        <span style="display:inline-block; margin-right:18px;"><strong>Generado por:</strong> <?php echo htmlspecialchars(isset($this->ion_auth) && method_exists($this->ion_auth,'user') ? ($this->ion_auth->user()->row()->username ?? 'Usuario') : 'Usuario'); ?></span>
        <span style="display:inline-block; margin-right:18px;"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i'); ?></span>
        <span style="display:inline-block;"><strong>Garantía ID:</strong> <?php echo htmlspecialchars(isset($v->garantia_id) ? $v->garantia_id : ''); ?></span>
    </footer>

</body>
</html>