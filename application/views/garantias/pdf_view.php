<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Expecting $g (garantia) optionally provided by controller
$g = isset($g) ? $g : null;
$solicitud = isset($solicitud) ? $solicitud : null;

$cliente_nombre = '';
if (!empty($solicitud)) {
    $cliente_nombre = trim((string)($solicitud->nombre_completo ?? ''));
    if ($cliente_nombre === '') {
        $cliente_nombre = trim((string)($solicitud->apellidos ?? '') . ' ' . (string)($solicitud->nombres ?? ''));
    }
}

// prepare logo data URI (try several expected filenames including 'crediblamen')
$logo_uri = '';
$logo_names = [
    'crediblamen.png', 'crediblamen.jpg', 'crediblamen_logo.png', 'crediblamen_logo.jpg',
    'public/img/crediblamen.png', 'public/img/crediblamen.jpg',
    'public/img/logo.png', 'public/img/logo.jpg', 'public/img/credi_socios_logo.png', 'public/img/credi_socios_logo.jpg'
];
foreach ($logo_names as $lp) {
    $p = FCPATH . ltrim($lp, '/\\');
    if (file_exists($p)) {
        $m = mime_content_type($p) ?: 'image/png';
        $data = base64_encode(file_get_contents($p));
        $logo_uri = 'data:' . $m . ';base64,' . $data;
        break;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formato de Garantía</title>
    <style>
        @page { size: A4 landscape; margin: 8mm 5mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#222; margin:0; }

        /* Use header style compatible with Solicitud PDF */
        .header-dark { background:#0b3d91; color:#fff; padding:8px 8px; }
        .header-inner { width:100%; }
        .title { font-size:16px; font-weight:700; letter-spacing:1px; margin:4px 0 2px 0; }
        .sub { font-size:10px; color:#ddd; margin-bottom:2px; }
        .logo { float:right; max-height:50px; }
        .clear { clear:both; }

        .content { padding:6px 8px 0 8px; }

        .table-wrap { width:100%; margin-top:6px; text-align:center; }
        table.garantias { width:100%; margin:0 auto; border-collapse:collapse; font-size:9pt; border:1px solid #e6eefc; }
        table.garantias thead th { background:#eef6ff; color:#0b3d91; font-weight:700; padding:6px 4px; border-right:1px solid #e6eefc; font-size:8.5pt; }
        table.garantias thead th:last-child { border-right: none; }
        table.garantias tbody td { padding:5px 3px; border-top:1px solid #f7fbff; vertical-align:middle; font-size:8.5pt; }
        table.garantias tbody td:last-child { text-align:center; }
        table.garantias tbody tr:nth-child(odd) td { background:#ffffff; }

        .text-right { text-align:right; }
        .text-center { text-align:center; }

        /* Photos grid: two rows x two columns per page with modest heights to avoid overflow */
        .photo-page { page-break-before:always; padding-top:6mm; }
        .photo-table td { width:50%; padding:6px; vertical-align:top; }
        .photo-table img { width:100%; height:80mm; object-fit:cover; border:1px solid #ccc; display:block; }

        footer { display:block; clear:both; font-size:8px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:4px; margin:8px 8px 8px 8px; }
    </style>
</head>
<body>
    <div class="header-dark">
        <div class="header-inner">
            <div style="float:left; max-width:70%;">
                <div class="title">FORMATO DE GARANTÍA</div>
                <div class="sub">INFORMACIÓN DE LA GARANTÍA</div>
                <?php if ($cliente_nombre !== ''): ?>
                    <div class="sub">Cliente: <?php echo html_escape($cliente_nombre); ?></div>
                <?php endif; ?>
            </div>
            <div style="float:right;">
                <?php if (! empty($logo_uri)): ?>
                    <img class="logo" src="<?php echo $logo_uri; ?>" alt="logo" />
                <?php else: ?>
                    <?php $lf = FCPATH . 'public/img/logo.png'; if (! file_exists($lf)) $lf = FCPATH . 'public/img/logo.jpg'; if (file_exists($lf)) echo '<img class="logo" src="' . base_url('public/img/' . basename($lf)) . '" alt="logo">'; ?>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="content">
        <?php $garantias = isset($garantias) ? $garantias : []; ?>
        <?php $tasa_cambio = isset($tasa_cambio) ? floatval($tasa_cambio) : 36.50; ?>

        <?php if (! empty($garantias)): ?>
            <div class="table-wrap">
                <table class="garantias">
                    <thead>
                        <tr>
                            <th style="width:4%;" class="text-center">Cant.</th>
                            <th style="width:28%;">Descripción del Artículo</th>
                            <th style="width:12%;">Modelo</th>
                            <th style="width:12%;">Marca / Color</th>
                            <th style="width:11%;">N° Serie</th>
                                <th style="width:9%;" class="text-right">Avaluo C$</th>
                                <th style="width:10%;" class="text-right">Avaluo US$</th>
                            <th style="width:9%;" class="text-right">Total US$</th>
                            <th style="width:5%;" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // compute subtotal as sum(cantidad * avaluo) for each line
                        $subtotal_avaluo_usd = 0.0;
                        $subtotal_avaluo_cordobas = 0.0;

                        foreach ($garantias as $row):
                               $cantidad = isset($row->cantidad) && $row->cantidad !== null && $row->cantidad !== '' ? intval($row->cantidad) : 1;
                               $avaluo_cordobas = isset($row->costo) && $row->costo !== null && $row->costo !== '' ? floatval($row->costo) : 0.0;
                               $avaluo_usd = $tasa_cambio > 0 ? ($avaluo_cordobas / $tasa_cambio) : 0.0;
                               $line_total_usd = $cantidad * $avaluo_usd;
                               $line_total_cordobas = $cantidad * $avaluo_cordobas;
                               $subtotal_avaluo_usd += $line_total_usd;
                               $subtotal_avaluo_cordobas += $line_total_cordobas;
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $cantidad; ?></td>
                                <td><?php echo isset($row->nombre) ? html_escape($row->nombre) : ''; ?></td>
                                <td><?php echo isset($row->modelo) ? html_escape($row->modelo) : ''; ?></td>
                                <td><?php echo isset($row->marca) ? html_escape($row->marca) : ''; ?></td>
                                <td><?php echo isset($row->n_serie) ? html_escape($row->n_serie) : ''; ?></td>
                                <td class="text-right"><?php echo $avaluo_cordobas > 0 ? 'C$' . number_format($avaluo_cordobas,2) : ''; ?></td>
                                <td class="text-right"><?php echo $avaluo_usd > 0 ? '$' . number_format($avaluo_usd,2) : ''; ?></td>
                                <td class="text-right"><?php echo $line_total_usd > 0 ? '$' . number_format($line_total_usd,2) : ''; ?></td>
                                <td class="text-center"><?php echo isset($row->estado) ? html_escape($row->estado) : (isset($row->tiempo_vida) ? html_escape($row->tiempo_vida) : ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8f9fa; font-weight:bold;">
                            <td colspan="5" class="text-right" style="padding:10px 8px;">SUBTOTAL AVALÚO (C$)</td>
                            <td class="text-right" style="padding:10px 8px;">C$<?php echo number_format($subtotal_avaluo_cordobas,2); ?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr style="background:#f8f9fa; font-weight:bold;">
                            <td colspan="5" class="text-right" style="padding:10px 8px;">SUBTOTAL AVALÚO (US$)</td>
                            <td colspan="2" class="text-right" style="padding:10px 8px;">$<?php echo number_format($subtotal_avaluo_usd,2); ?></td>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <!-- Tasa de cambio ocultada -->
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div style="color:#666; font-size:10pt;">No hay garantías registradas para esta solicitud.</div>
        <?php endif; ?>

        <!-- Bloque de firmas elegante al final de la hoja -->
        <div style="margin-top:60px; width:100%;">
            <table style="width:100%; border:none; margin-top:40px;">
                <tr>
                    <td style="width:50%; text-align:center; vertical-align:bottom; padding-top:40px;">
                        <div style="border-top:1px solid #222; width:70%; margin:0 auto 6px auto; height:0;"></div>
                        <span style="font-size:10pt; color:#222;">Firma Promotor</span>
                    </td>
                    <td style="width:50%; text-align:center; vertical-align:bottom; padding-top:40px;">
                        <div style="border-top:1px solid #222; width:70%; margin:0 auto 6px auto; height:0;"></div>
                        <span style="font-size:10pt; color:#222;">Firma Verificador</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Photos: render 4 images per page (2x2) after the table -->
        <?php if (! empty($photos) && is_array($photos)): ?>
            <?php
                $photos_fixed = $photos;
                $total = count($photos_fixed);
                // Rellenar con nulls hasta múltiplo de 4
                $faltan = ($total % 4) ? (4 - ($total % 4)) : 0;
                for ($i = 0; $i < $faltan; $i++) {
                    $photos_fixed[] = null;
                }
                $chunks = array_chunk($photos_fixed, 4);
            ?>
            <?php foreach ($chunks as $page => $group): ?>
                <div class="photo-page" style="page-break-before:always; margin:0; padding:0;">
                    <table class="photo-table" style="width:100%; height:180mm; border-collapse:collapse; table-layout:fixed; margin:0; padding:0;">
                        <?php for ($row = 0; $row < 2; $row++): ?>
                            <tr style="height:90mm;">
                                <?php for ($col = 0; $col < 2; $col++): ?>
                                    <?php $idx = $row * 2 + $col; $p = isset($group[$idx]) ? $group[$idx] : null; ?>
                                    <td style="width:50%; height:90mm; margin:0; padding:0; border:1px solid #bbb; text-align:center; vertical-align:middle;">
                                        <?php if ($p): ?>
                                            <?php $src = preg_match('#^(data:|https?://|/)#', $p) ? $p : base_url($p); ?>
                                            <img src="<?php echo $src; ?>" alt="foto" style="width:100%; height:88mm; object-fit:cover; display:block; margin:0 auto; padding:0; border:1.5px solid #0b3d91;">
                                        <?php else: ?>
                                            <!-- Celda vacía con borde tenue -->
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!isset($es_pagina_fotos) || !$es_pagina_fotos): ?>
    <footer>
        <span style="display:inline-block; margin-right:18px;"><strong>Generado por:</strong> <?php echo htmlspecialchars(isset($this->ion_auth) && method_exists($this->ion_auth,'user') ? ($this->ion_auth->user()->row()->username ?? 'Usuario') : 'Usuario'); ?></span>
        <span style="display:inline-block; margin-right:18px;"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i'); ?></span>
        <span style="display:inline-block;"><strong>Código documento:</strong> <?php echo htmlspecialchars($g ? (isset($g->id) ? 'GAR-'.$g->id : (isset($g->solicitud_id)? 'SOL-'.$g->solicitud_id : '')) : ''); ?></span>
    </footer>
    <?php endif; ?>

</body>
</html>
