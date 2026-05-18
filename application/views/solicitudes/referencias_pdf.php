<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Referencias - Solicitud <?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?></title>
    <?php
    // prepare logo data URI: prefer same order as Solicitud Inicial template
    $logo_uri = '';
    $logo_paths = array(
        FCPATH . 'public/img/logo.png',
        FCPATH . 'public/img/logo.jpg',
        FCPATH . 'public/img/credi_socios_logo.png',
        FCPATH . 'public/img/credi_socios_logo.jpg'
    );
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
        @page { margin: 14mm 12mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; margin:0; padding:0; }
        .header-dark { background:#0b3d91; color:#fff; padding:14px 16px; }
        .header-inner { width:100%; }
        .title { font-size:18px; font-weight:700; letter-spacing:1px; margin:6px 0 8px 0; }
        .sub { font-size:11px; color:#ddd; margin-bottom:8px; }
        .logo { float:right; }
        .clear { clear:both; }

        .card { border:1px solid #e6e6e6; padding:10px; margin:12px 6px; background:#fff; }
        .photo { width: 100%; max-height: 180px; object-fit: contain; border: 1px solid #ccc; }
        .meta { margin-bottom: 6px; }
        .label { font-weight: bold; }

        .section-title { font-size:12px; font-weight:700; color:#fff; background:#0b3d91; padding:8px 10px; margin-bottom:8px; }
        table.ref-table { width:100%; border-collapse:collapse; margin-bottom:8px; font-size:11px; }
        table.ref-table td { border:1px solid #e6e6e6; padding:6px; vertical-align:top; }

        footer { display:block; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:6px; margin:18px 6px 12px 6px; }
        img.logo-img { max-height:60px; }
    </style>
</head>
<body>
    <?php
    $cliente_nombre = trim((string)($solicitud->nombre_completo ?? ''));
    if ($cliente_nombre === '') {
        $cliente_nombre = trim((string)($solicitud->apellidos ?? '') . ' ' . (string)($solicitud->nombres ?? ''));
    }
    if ($cliente_nombre === '') {
        $cliente_nombre = trim((string)($solicitud->nombre_cliente ?? ''));
    }
    ?>
    <div class="header-dark">
        <div class="header-inner">
            <div style="float:left; max-width:75%">
                <div class="title">VERIFICACIÓN DE REFERENCIAS</div>
                <div class="sub">Solicitud <?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?> &nbsp;&nbsp; | &nbsp;&nbsp; Generado: <?php echo isset($generated_at) ? $generated_at : date('d/m/Y H:i'); ?></div>
                <div class="sub">Cliente: <?php echo htmlspecialchars($cliente_nombre); ?></div>
            </div>
            <div class="logo">
                <?php if (!empty($logo_uri)): ?>
                    <img class="logo-img" src="<?php echo $logo_uri; ?>" alt="logo">
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <?php
    // force exactly 2 pages: render reference 1 and 2 (or blanks)
    $pages = 2;
    for ($p = 0; $p < $pages; $p++) {
        if (isset($referencias[$p])) {
            $r = $referencias[$p];
        } else {
            $r = new stdClass();
            $r->referencia_num = $p + 1;
            $r->nombre = null;
            $r->cedula = null;
            $r->telefono = null;
            $r->direccion = null;
            $r->tipo_referencia = null;
            $r->desde_conoce_cliente = null;
            $r->relacion_economica = null;
            $r->opinion = null;
            $r->comentarios = null;
            $r->photo_front = null;
            $r->photo_back = null;
        }
    ?>
        <div class="card" style="page-break-after: <?php echo ($p === $pages-1) ? 'auto' : 'always'; ?>;">
            <div class="section-title">Referencia <?php echo isset($r->referencia_num) ? (int)$r->referencia_num : ($p+1); ?> - Solicitud <?php echo isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : ''; ?></div>

            <table class="ref-table">
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;width:25%;"><strong>Nombre</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->nombre) && $r->nombre !== null ? htmlspecialchars($r->nombre) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Cédula / Identificación</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->cedula) && $r->cedula !== null ? htmlspecialchars($r->cedula) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Teléfono</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->telefono) && $r->telefono !== null ? htmlspecialchars($r->telefono) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Dirección</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->direccion) && $r->direccion !== null ? nl2br(htmlspecialchars($r->direccion)) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Tipo de referencia</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->tipo_referencia) && $r->tipo_referencia !== null ? htmlspecialchars($r->tipo_referencia) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Desde cuándo conoce</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->desde_conoce_cliente) && $r->desde_conoce_cliente !== null ? htmlspecialchars($r->desde_conoce_cliente) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Relación económica</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php if (!isset($r->relacion_economica) || $r->relacion_economica === null) { echo '&nbsp;'; } else { echo ($r->relacion_economica ? 'Sí' : 'No'); } ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Opinión</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->opinion) && $r->opinion !== null ? htmlspecialchars($r->opinion) : '&nbsp;'; ?></td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;"><strong>Comentarios</strong></td>
                    <td style="border:1px solid #ddd;padding:6px;"><?php echo isset($r->comentarios) && $r->comentarios !== null ? nl2br(htmlspecialchars($r->comentarios)) : '&nbsp;'; ?></td>
                </tr>
            </table>

            <div style="margin-top:6px;">
                <div style="font-weight:bold;margin-bottom:6px;">Fotos de la Cédula</div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; font-weight:bold; margin-bottom:4px;">Frontal</div>
                    <?php if (!empty($r->photo_front_src) || !empty($r->photo_front)): ?>
                        <img src="<?php echo !empty($r->photo_front_src) ? $r->photo_front_src : $r->photo_front; ?>" style="width:100%; height:auto; max-height:300px; border:1px solid #ccc;" alt="Frontal">
                    <?php else: ?>
                        <div style="width:100%; height:180px; border:1px dashed #ccc; text-align:center; line-height:180px; color:#999;">Sin foto frontal</div>
                    <?php endif; ?>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:bold; margin-bottom:4px;">Trasera</div>
                    <?php if (!empty($r->photo_back_src) || !empty($r->photo_back)): ?>
                        <img src="<?php echo !empty($r->photo_back_src) ? $r->photo_back_src : $r->photo_back; ?>" style="width:100%; height:auto; max-height:300px; border:1px solid #ccc;" alt="Trasera">
                    <?php else: ?>
                        <div style="width:100%; height:180px; border:1px dashed #ccc; text-align:center; line-height:180px; color:#999;">Sin foto trasera</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php } // end pages loop ?>

    <?php
    // Footer (only once, at the end)
    $generated_by = 'Usuario';
    try {
        if (isset($this->ion_auth) && method_exists($this->ion_auth, 'user')) {
            $u = $this->ion_auth->user()->row();
            if (!empty($u->username)) $generated_by = $u->username;
            elseif (!empty($u->first_name) || !empty($u->last_name)) $generated_by = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $generated_by;
        } elseif (isset($this->session)) {
            $uname = $this->session->userdata('username') ?: $this->session->userdata('user_login') ?: $this->session->userdata('email');
            if (!empty($uname)) $generated_by = $uname;
        }
    } catch (Exception $e) { }
    $generated_at = isset($generated_at) ? $generated_at : date('d/m/Y H:i');
    $sol_id = isset($solicitud->idsolicitud) ? intval($solicitud->idsolicitud) : '';
    ?>

    <footer>
        <span style="display:inline-block; margin-right:18px;"><strong>Generado por:</strong> <?php echo htmlspecialchars($generated_by); ?></span>
        <span style="display:inline-block; margin-right:18px;"><strong>Fecha:</strong> <?php echo htmlspecialchars($generated_at); ?></span>
        <span style="display:inline-block;"><strong>Solicitud ID:</strong> <?php echo htmlspecialchars($sol_id); ?></span>
    </footer>

</body>
</html>
