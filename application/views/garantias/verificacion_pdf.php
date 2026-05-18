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
        @page { margin: 12mm; }
        body{font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:12mm}
        .header-dark { background:#0b3d91; color:#fff; padding:10px 12px; display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
        .header-dark .title { font-size:13px; font-weight:700; }
        .header-dark .meta { font-size:10px; opacity:0.95; }
        .section{margin-bottom:12px}
        .photos{display:flex;gap:10px}
        .photos img{max-height:220px;object-fit:contain;border:1px solid #ccc}
        .meta td{padding:6px;border:1px solid #eee}
        footer { display:block; position:static; clear:both; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:6px; margin:18px 12mm 12mm 12mm; page-break-before:always; }
    </style>
</head>
<body>
    <div class="header-dark">
        <div>
            <div class="title">Verificación de Garantía</div>
            <div class="meta">Garantía ID: <?php echo isset($v->garantia_id) ? $v->garantia_id : ''; ?> — Solicitud: <?php echo isset($v->solicitud_id) ? $v->solicitud_id : ''; ?></div>
        </div>
        <div>
            <?php if (!empty($logo_uri)): ?>
                <img src="<?php echo $logo_uri; ?>" style="max-height:56px" alt="logo">
            <?php endif; ?>
        </div>
    </div>

    <div style="padding:6px 0 0 0; margin-bottom:8px; font-size:11px; color:#222;">
        <strong>Verificador:</strong> <?php echo isset($v->verificador_usuario) ? htmlspecialchars($v->verificador_usuario) : 'N/A'; ?> &nbsp; | &nbsp; <strong>Fecha:</strong> <?php echo isset($v->created_at) ? $v->created_at : ''; ?>
    </div>

    <div class="section">
        <h4>Comentario</h4>
        <div><?php echo isset($v->comentario) ? nl2br(htmlspecialchars($v->comentario)) : '&nbsp;'; ?></div>
    </div>

    <div class="section">
        <h4>Fotos evidenciales</h4>
        <?php if (!empty($imgs)): ?>
            <div class="photos">
                <?php foreach ($imgs as $img): ?>
                    <div style="flex:1"><img src="<?php echo $img; ?>" style="width:100%"></div>
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