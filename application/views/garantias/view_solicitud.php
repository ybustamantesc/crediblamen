<?php defined('BASEPATH') OR exit('No direct script access allowed');
$g = isset($g) ? $g : null;
$garantias = isset($garantias) ? $garantias : array();
if (! $g) { echo '<div class="alert alert-warning">No existe el registro.</div>'; return; }
$this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-shield-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Formato de Garantía - Solicitud #<?php echo $g->solicitud_id; ?></h5>
                                <span>Detalle del formato y fotos (todas las garantías registradas)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a class="btn btn-secondary" href="<?php echo base_url('garantias'); ?>">Volver</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <a class="btn btn-info btn-sm" href="<?php echo base_url('garantias/pdf_by_solicitud/'.$g->solicitud_id); ?>" target="_blank">Descargar PDF (todas)</a>
                        </div>
                        <div>
                            <button class="btn btn-outline-dark btn-sm" onclick="window.print();">Imprimir</button>
                        </div>
                    </div>

                    <?php foreach ($garantias as $idx => $item): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Garantía <?php echo ($idx+1); ?></strong>
                                <span class="ml-2 text-muted">ID: <?php echo $item->id; ?></span>
                                <span class="float-right">
                                    <a class="btn btn-sm btn-secondary" href="<?php echo base_url('garantias/pdf/'.$item->id); ?>" target="_blank">PDF</a>
                                    <a class="btn btn-sm btn-info" href="<?php echo base_url('garantias/create/'.$item->solicitud_id); ?>">Editar</a>
                                </span>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered mb-2">
                                    <tr><th>Nombre Garantía</th><td><?php echo html_escape($item->nombre); ?></td></tr>
                                    <tr><th>Cantidad</th><td><?php echo (int)$item->cantidad; ?></td></tr>
                                    <tr><th>Marca</th><td><?php echo html_escape($item->marca); ?></td></tr>
                                    <tr><th>Modelo</th><td><?php echo html_escape($item->modelo); ?></td></tr>
                                    <tr><th>Nº Serie</th><td><?php echo html_escape(isset($item->n_serie) ? $item->n_serie : ''); ?></td></tr>
                                    <tr><th>Avaluo</th><td><?php echo html_escape($item->costo); ?></td></tr>
                                    <tr><th>Estado</th><td><?php echo html_escape($item->tiempo_vida); ?></td></tr>
                                </table>

                                <div class="row">
                                    <?php
                                    // Prefer photos from related table mapping passed as $photos_map
                                    $printed = 0;
                                    if (isset($photos_map) && is_array($photos_map) && ! empty($photos_map[$item->id])) {
                                        foreach ($photos_map[$item->id] as $pfile) {
                                            if (empty($pfile)) continue;
                                            $printed++;
                                            ?>
                                            <div class="col-md-4 mb-3 photo-card">
                                                <div class="card">
                                                    <?php
                                                    // Normalize photo path to a usable src URL
                                                    $rel = isset($pfile) ? trim($pfile) : '';
                                                    $src = '';
                                                    if ($rel === '') {
                                                        $src = '';
                                                    } elseif (strpos($rel, 'data:') === 0) {
                                                        $src = $rel; // already data URI
                                                    } elseif (preg_match('#^https?://#i', $rel)) {
                                                        $src = $rel; // external URL
                                                    } else {
                                                        // treat as relative path inside project (uploads/... or similar)
                                                        $candidate = FCPATH . ltrim($rel, '/\\');
                                                        if (file_exists($candidate)) {
                                                            $src = base_url(ltrim($rel, '/\\'));
                                                        } else {
                                                            // try as-is with base_url (in case it is already relative web path)
                                                            $src = base_url(ltrim($rel, '/\\'));
                                                        }
                                                    }
                                                    ?>
                                                    <img src="<?php echo htmlspecialchars($src); ?>" class="img-fluid" style="max-height:260px; object-fit:cover;">
                                                    <div class="card-body p-2 text-center">
                                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($src); ?>" target="_blank">Descargar</a>
                                                        <button class="btn btn-sm btn-danger btn-delete-garantia-photo" data-filename="<?php echo htmlspecialchars($rel); ?>">Eliminar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    // Fallback to foto1..foto5 columns if no related photos
                                    if ($printed === 0) {
                                        for ($i=1;$i<=5;$i++){
                                            $f = 'foto'.$i;
                                            if (! empty($item->$f)) {
                                                ?>
                                                <div class="col-md-4 mb-3">
                                                            <div class="card">
                                                            <?php
                                                            $rel2 = isset($item->$f) ? trim($item->$f) : '';
                                                            $src2 = '';
                                                            if ($rel2 === '') {
                                                                $src2 = '';
                                                            } elseif (strpos($rel2, 'data:') === 0) {
                                                                $src2 = $rel2;
                                                            } elseif (preg_match('#^https?://#i', $rel2)) {
                                                                $src2 = $rel2;
                                                            } else {
                                                                $candidate2 = FCPATH . ltrim($rel2, '/\\');
                                                                if (file_exists($candidate2)) {
                                                                    $src2 = base_url(ltrim($rel2, '/\\'));
                                                                } else {
                                                                    $src2 = base_url(ltrim($rel2, '/\\'));
                                                                }
                                                            }
                                                            ?>
                                                            <img src="<?php echo htmlspecialchars($src2); ?>" class="img-fluid" style="max-height:260px; object-fit:cover;">
                                                            <div class="card-body p-2 text-center">
                                                                <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($src2); ?>" target="_blank">Descargar</a>
                                                                <button class="btn btn-sm btn-danger btn-delete-garantia-photo" data-filename="<?php echo htmlspecialchars($rel2); ?>">Eliminar</button>
                                                            </div>
                                                        </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function(){
    document.addEventListener('click', function(e){
        var t = e.target;
        if (t && t.classList && t.classList.contains('btn-delete-garantia-photo')){
            if (!confirm('Confirma eliminar esta imagen?')) return;
            var filename = t.getAttribute('data-filename');
            if (!filename) { alert('No se pudo identificar la imagen'); return; }
            var fd = new FormData(); fd.append('filename', filename);
            fetch('<?php echo base_url('garantias/delete_garantia_photo_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function(r){ return r.json(); }).then(function(j){ if (j && j.status){ try{ var node = (t.closest ? (t.closest('.photo-card') || t.closest('.col-md-4') || t.closest('.card')) : null); if (node) node.remove(); }catch(e){} } else { alert('No se pudo eliminar la imagen'); } }).catch(function(){ alert('Error al eliminar la imagen'); });
        }
    }, false);
})();
</script>
