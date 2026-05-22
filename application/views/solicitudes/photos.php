<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a href="<?php echo base_url('solicitudes/core/' . intval($idsolicitud)); ?>" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php // Debug panel: shows raw photos payload when needed ?>
                    <div style="margin-bottom:10px;">
                        <button id="toggle-photos-debug" class="btn btn-sm btn-outline-secondary">Mostrar debug fotos</button>
                        <button id="btn-auto-classify" class="btn btn-sm btn-outline-primary">Auto-clasificar fotos</button>
                        <div id="photos-debug" style="display:none;margin-top:8px;font-size:12px;max-height:220px;overflow:auto;border:1px dashed #ddd;padding:8px;background:#f9f9f9;"></div>
                    </div>
                    <div class="row" id="photos-gallery" style="gap:12px;">
                        <?php
                            // Group photos by known groups so we can display sections
                            $groups = array(
                                'fachada' => array(),
                                'inventario' => array(),
                                'cedula_front' => array(),
                                'cedula_back' => array(),
                                'evidencia' => array(),
                                'otros_ingresos_1' => array(),
                                'otros_ingresos_2' => array(),
                                'otros_ingresos_3' => array(),
                                'otros' => array(),
                                'docs_generales' => array(),
                                'docs_legales' => array(),
                                'consentimiento_filtrado' => array(),
                                'fotos_adicionales' => array()
                            );
                            if (!empty($photos) && is_array($photos)){
                                foreach($photos as $p){
                                    $g = (isset($p->grupo) && trim($p->grupo) !== '') ? $p->grupo : (isset($p->grupo_name) ? $p->grupo_name : 'otros');
                                    $g = strtolower(preg_replace('/[^a-z0-9_]/i','_', $g));
                                    if (!array_key_exists($g, $groups)) $g = 'otros';
                                    $groups[$g][] = $p;
                                }
                            }

                            $any = false;
                            // Desired order: Cédula (frontal+trasera), Fachada, Inventario, Evidencia, Otros ingresos 1,2,3
                            $order = array(
                                'cedula' => array('cedula_front','cedula_back'),
                                'fachada' => array('fachada'),
                                'inventario' => array('inventario'),
                                'evidencia' => array('evidencia'),
                                'otros_ingresos_1' => array('otros_ingresos_1'),
                                'otros_ingresos_2' => array('otros_ingresos_2'),
                                'otros_ingresos_3' => array('otros_ingresos_3')
                            );
                            $titles = array(
                                'cedula' => 'Cédula',
                                'fachada' => 'Fachada',
                                'inventario' => 'Inventario',
                                'evidencia' => 'Evidencia de Estado Financiero',
                                'otros_ingresos_1' => 'Otros Ingresos 1',
                                'otros_ingresos_2' => 'Otros Ingresos 2',
                                'otros_ingresos_3' => 'Otros Ingresos 3'
                            );

                            // Only render the explicit 4-column panels below (avoid duplicate sections)

                                // Render explicit columns so photos are not mixed across sections
                                $cedula = array();
                                if (!empty($groups['cedula_front'])) $cedula = array_merge($cedula, $groups['cedula_front']);
                                if (!empty($groups['cedula_back'])) $cedula = array_merge($cedula, $groups['cedula_back']);
                                $fachada = !empty($groups['fachada']) ? $groups['fachada'] : array();
                                $inventario = !empty($groups['inventario']) ? $groups['inventario'] : array();
                                $evidencia = !empty($groups['evidencia']) ? $groups['evidencia'] : array();
                                $otros = array();
                                if (!empty($groups['otros_ingresos_1'])) $otros = array_merge($otros, $groups['otros_ingresos_1']);
                                if (!empty($groups['otros_ingresos_2'])) $otros = array_merge($otros, $groups['otros_ingresos_2']);
                                if (!empty($groups['otros_ingresos_3'])) $otros = array_merge($otros, $groups['otros_ingresos_3']);
                                if (!empty($groups['otros'])) $otros = array_merge($otros, $groups['otros']);
                                $any = (count($cedula) + count($fachada) + count($inventario) + count($evidencia) + count($otros)) > 0;
                            ?>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6>Cédula <small class="text-muted">(<?php echo count($cedula); ?>)</small></h6>
                                                <?php if (empty($cedula)): ?><div class="text-muted">Sin imágenes</div><?php endif; ?>
                                                <?php foreach($cedula as $p): $url = base_url('uploads/' . (isset($p->filename) ? $p->filename : '')); ?>
                                                    <div class="mb-2">
                                                        <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="width:100%; max-width:160px; max-height:120px; display:block; margin:0 0 6px;" /></a>
                                                        <div class="d-flex" style="gap:6px;">
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                            <button class="btn btn-sm btn-danger btn-delete-photo" data-idphoto="<?php echo intval(isset($p->idphoto)?$p->idphoto:0); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6>Fachada <small class="text-muted">(<?php echo count($fachada); ?>)</small></h6>
                                                <?php if (empty($fachada)): ?><div class="text-muted">Sin imágenes</div><?php endif; ?>
                                                <?php foreach($fachada as $p): $url = base_url('uploads/' . (isset($p->filename) ? $p->filename : '')); ?>
                                                    <div class="mb-2">
                                                        <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="width:100%; max-width:160px; max-height:120px; display:block; margin:0 0 6px;" /></a>
                                                        <div class="d-flex" style="gap:6px;">
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                            <button class="btn btn-sm btn-danger btn-delete-photo" data-idphoto="<?php echo intval(isset($p->idphoto)?$p->idphoto:0); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6>Inventario <small class="text-muted">(<?php echo count($inventario); ?>)</small></h6>
                                                <?php if (empty($inventario)): ?><div class="text-muted">Sin imágenes</div><?php endif; ?>
                                                <?php foreach($inventario as $p): $url = base_url('uploads/' . (isset($p->filename) ? $p->filename : '')); ?>
                                                    <div class="mb-2">
                                                        <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="width:100%; max-width:160px; max-height:120px; display:block; margin:0 0 6px;" /></a>
                                                        <div class="d-flex" style="gap:6px;">
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                            <button class="btn btn-sm btn-danger btn-delete-photo" data-idphoto="<?php echo intval(isset($p->idphoto)?$p->idphoto:0); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6>Evidencia de Estado Financiero <small class="text-muted">(<?php echo count($evidencia); ?>)</small></h6>
                                                <?php if (empty($evidencia)): ?><div class="text-muted">Sin imágenes</div><?php endif; ?>
                                                <?php foreach($evidencia as $p): $url = base_url('uploads/' . (isset($p->filename) ? $p->filename : '')); ?>
                                                    <div class="mb-2">
                                                        <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="width:100%; max-width:160px; max-height:120px; display:block; margin:0 0 6px;" /></a>
                                                        <div class="d-flex" style="gap:6px;">
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                            <button class="btn btn-sm btn-danger btn-delete-photo" data-idphoto="<?php echo intval(isset($p->idphoto)?$p->idphoto:0); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6>Otros Ingresos <small class="text-muted">(<?php echo count($otros); ?>)</small></h6>
                                                <?php if (empty($otros)): ?><div class="text-muted">Sin imágenes</div><?php endif; ?>
                                                <?php foreach($otros as $p): $url = base_url('uploads/' . (isset($p->filename) ? $p->filename : '')); ?>
                                                    <div class="mb-2">
                                                        <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="width:100%; max-width:160px; max-height:120px; display:block; margin:0 0 6px;" /></a>
                                                        <div class="d-flex" style="gap:6px;">
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                            <button class="btn btn-sm btn-danger btn-delete-photo" data-idphoto="<?php echo intval(isset($p->idphoto)?$p->idphoto:0); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
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
        // Click handler: delete buttons and debug toggle
        document.addEventListener('click', function(e){
            var t = e.target;
            // Delete photo button
            if (t && t.classList && t.classList.contains('btn-delete-photo')){
                var idp = t.getAttribute('data-idphoto');
                var filename = t.getAttribute('data-filename');
                if (idp && parseInt(idp) > 0){
                    if(!confirm('Confirma eliminar esta imagen?')) return;
                    var fd = new FormData(); fd.append('idphoto', idp);
                    fetch('<?php echo base_url('solicitudes/delete_photo_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                        .then(function(r){ return r.json(); }).then(function(j){ if(j && j.status){ try{ var node = (t.closest ? (t.closest('.mb-2') || t.closest('.col-md-2') || t.closest('.col-md-3') || t.closest('.card')) : null); if(node) node.remove(); }catch(e){} } else { alert('No se pudo eliminar.'); } }).catch(function(){ alert('Error al eliminar.'); });
                    return;
                } else if (filename){
                    if(!confirm('Confirma eliminar esta imagen?')) return;
                    var fd = new FormData(); fd.append('filename', filename);
                    fetch('<?php echo base_url('solicitudes/delete_photo_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                        .then(function(r){ return r.json(); }).then(function(j){ if(j && j.status){ try{ var node = (t.closest ? (t.closest('.mb-2') || t.closest('.col-md-2') || t.closest('.col-md-3') || t.closest('.card')) : null); if(node) node.remove(); }catch(e){} } else { alert('No se pudo eliminar.'); } }).catch(function(){ alert('Error al eliminar.'); });
                    return;
                } else {
                    alert('No hay identificador de foto disponible');
                }
            }

            // Toggle debug panel
            if (t && t.id === 'toggle-photos-debug'){
                var dbg = document.getElementById('photos-debug');
                if (!dbg) return;
                if (dbg.style.display === 'none'){
                    try {
                        var photos = <?php echo json_encode(isset($photos)?$photos:array()); ?>;
                        dbg.innerText = JSON.stringify(photos, null, 2);
                    } catch(e){ dbg.innerText = 'No se pudo serializar photos'; }
                    dbg.style.display = 'block';
                    t.innerText = 'Ocultar debug fotos';
                } else {
                    dbg.style.display = 'none';
                    t.innerText = 'Mostrar debug fotos';
                }
            }
        }, false);
    })();
    </script>

<script>
(function(){
    // Handle group select change
    document.addEventListener('change', function(e){
        var t = e.target;
        if (t && t.classList && t.classList.contains('photo-group-select')){
            var group = t.value;
            var idp = t.getAttribute('data-idphoto');
            var filename = t.getAttribute('data-filename');
            var fd = new FormData(); fd.append('group', group);
            if (idp && parseInt(idp) > 0) fd.append('idphoto', idp);
            else if (filename) fd.append('filename', filename);
            else { alert('No se puede actualizar: falta id o filename'); return; }
            fetch('<?php echo base_url('solicitudes/update_photo_group_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function(r){ return r.json(); }).then(function(j){ if (j && j.status) { /* success - optionally show feedback */ } else { alert('No se pudo actualizar el tipo de foto'); } }).catch(function(){ alert('Error actualizando tipo'); });
        }
    }, false);

    // Auto-classify button
    var ac = document.getElementById('btn-auto-classify');
    if (ac){ ac.addEventListener('click', function(){
        if (!confirm('Auto-clasificar todas las fotos de esta solicitud?')) return;
        fetch('<?php echo base_url('solicitudes/auto_classify_photos_ajax/' . intval($idsolicitud)); ?>', { method: 'POST', credentials: 'same-origin' })
            .then(function(r){ return r.json(); }).then(function(j){ if (j && j.status){ location.reload(); } else { alert('No se pudo auto-clasificar'); } }).catch(function(){ alert('Error al auto-clasificar'); });
    }); }
})();
</script>
