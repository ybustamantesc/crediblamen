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
                                <h5><?php echo $titulo; ?></h5>
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
                    <div class="row" style="gap:12px;">
                        <?php
                        $groups = array(
                            'docs_generales' => 'Documentos Generales',
                            'docs_legales' => 'Documentos Legales Variados',
                            'fotos_adicionales' => 'Fotos Adicionales',
                            'consentimiento_filtrado' => 'Consentimiento de Filtrado'
                        );
                        $accept = array(
                            'docs_generales' => 'image/*,application/pdf',
                            'docs_legales' => 'image/*,application/pdf',
                            'fotos_adicionales' => 'image/*',
                            'consentimiento_filtrado' => 'image/*,application/pdf'
                        );
                        foreach ($groups as $group => $title):
                            $items = isset($documents[$group]) && is_array($documents[$group]) ? $documents[$group] : array();
                        ?>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1"><?php echo $title; ?></h6>
                                            <small class="text-muted"><?php echo count($items); ?> archivos</small>
                                        </div>
                                        <div>
                                            <label class="btn btn-sm btn-outline-primary mb-0" for="upload_<?php echo $group; ?>">Subir</label>
                                        </div>
                                    </div>
                                    <input type="file" id="upload_<?php echo $group; ?>" name="document_<?php echo $group; ?>[]" accept="<?php echo $accept[$group]; ?>" multiple style="display:none;" data-group="<?php echo $group; ?>">
                                    <div id="group_<?php echo $group; ?>_list">
                                        <?php if (empty($items)): ?>
                                            <div class="text-muted">Sin archivos</div>
                                        <?php else: ?>
                                            <?php foreach ($items as $item):
                                                $filename = isset($item->filename) ? trim($item->filename, '/') : '';
                                                $url = base_url('uploads/' . $filename);
                                                // Determine mime: prefer DB value, else infer from extension
                                                $mime = isset($item->mime) && $item->mime ? $item->mime : null;
                                                if (!$mime && $filename) {
                                                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                    if (in_array($ext, array('jpg','jpeg','png','gif','bmp'))) $mime = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
                                                    elseif ($ext === 'pdf') $mime = 'application/pdf';
                                                    else $mime = 'application/octet-stream';
                                                }
                                                $isImage = $mime && strpos($mime, 'image/') === 0;
                                                $isPdf = $mime === 'application/pdf';
                                            ?>
                                            <div class="document-item mb-3 border rounded p-2" style="background:#fafafa;">
                                                <div class="d-flex justify-content-between align-items-start" style="gap:10px;">
                                                    <div style="flex:1; min-width:0;">
                                                        <?php if ($isImage): ?>
                                                            <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="max-width:100%; max-height:160px; display:block; margin-bottom:8px; border:1px solid #ddd;" /></a>
                                                        <?php elseif ($isPdf): ?>
                                                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                                                <span class="fas fa-file-pdf" style="font-size:28px; color:#d9534f;"></span>
                                                                <span><?php echo htmlspecialchars(basename($filename)); ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <div style="margin-bottom:8px;"><?php echo htmlspecialchars(basename($filename)); ?></div>
                                                        <?php endif; ?>
                                                        <div class="text-muted small">Tipo: <?php echo htmlspecialchars($mime ? $mime : 'N/A'); ?></div>
                                                    </div>
                                                    <div class="text-right" style="white-space:nowrap;">
                                                        <a class="btn btn-sm btn-outline-secondary mb-1" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-document" data-idphoto="<?php echo intval(isset($item->idphoto) ? $item->idphoto : 0); ?>" data-filename="<?php echo htmlspecialchars($filename); ?>">Eliminar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    function uploadFiles(files, group) {
        if (!files || files.length === 0) return;
        var promises = [];
        for (var i = 0; i < files.length; i++) {
            (function(file){
                var fd = new FormData();
                fd.append('idsolicitud', '<?php echo intval($idsolicitud); ?>');
                fd.append('group', group);
                fd.append('photo', file);
                promises.push(fetch('<?php echo base_url('solicitudes/upload_photo_ajax'); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: fd
                }).then(function(response){ 
                    return response.json(); 
                }).catch(function(err){
                    console.error('Upload fetch error:', err);
                    return { status: false, error: err.toString() };
                }));
            })(files[i]);
        }
        Promise.all(promises).then(function(results){
            var failed = 0, succeeded = 0;
            results.forEach(function(r){
                if (r && r.status === true) {
                    succeeded++;
                } else {
                    failed++;
                    console.warn('Upload failed:', r);
                }
            });
            if (failed > 0) {
                console.warn('Some uploads had issues (' + failed + ' failed, ' + succeeded + ' succeeded)');
            }
            // Always reload after a short delay to ensure server has processed all uploads
            setTimeout(function(){
                window.location.reload();
            }, 800);
        }).catch(function(err){
            console.error('Promise.all error:', err);
            alert('Error al procesar las subidas. Recargando la página...');
            setTimeout(function(){
                window.location.reload();
            }, 1500);
        });
    }

    document.querySelectorAll('input[type=file][data-group]').forEach(function(input){
        input.addEventListener('change', function(){
            if (this.files && this.files.length > 0) {
                uploadFiles(this.files, this.getAttribute('data-group'));
            }
        });
    });

    document.addEventListener('click', function(event){
        var target = event.target;
        if (target && target.classList.contains('btn-delete-document')) {
            var idphoto = target.getAttribute('data-idphoto');
            var filename = target.getAttribute('data-filename');
            if (!confirm('¿Confirma eliminar este documento?')) return;
            var fd = new FormData();
            if (idphoto && parseInt(idphoto) > 0) {
                fd.append('idphoto', idphoto);
            } else if (filename) {
                fd.append('filename', filename);
            } else {
                alert('No se encontró el identificador del documento.');
                return;
            }
            fetch('<?php echo base_url('solicitudes/delete_photo_ajax'); ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function(response){ return response.json(); }).then(function(json){
                if (json && json.status) {
                    window.location.reload();
                } else {
                    alert(json && json.message ? json.message : 'No se pudo eliminar el documento.');
                }
            }).catch(function(){
                alert('Error al eliminar el documento.');
            });
        }
    });
})();
</script>
