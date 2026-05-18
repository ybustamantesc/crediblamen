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
                        <a href="<?php echo base_url('garantias/create/' . intval($solicitud_id)); ?>" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div style="margin-bottom:12px;">
                        <form id="garantia-upload-form" class="form-inline" onsubmit="return false;">
                            <div class="form-group mr-2">
                                <label for="garantia_select" class="mr-2">Garantía:</label>
                                <select id="garantia_select" class="form-control">
                                    <option value="">(Sin asignar)</option>
                                    <?php if (!empty($garantias) && is_array($garantias)){
                                        foreach($garantias as $gopt){
                                            $gid = isset($gopt->id) ? intval($gopt->id) : 0;
                                            $gname = isset($gopt->nombre) ? $gopt->nombre : ('Garantía '.$gid);
                                            echo '<option value="'.intval($gid).'">'.htmlspecialchars($gname).'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <input type="file" id="garantia_files" multiple accept="image/*" />
                            </div>
                            <button id="btn-upload-garantia" class="btn btn-primary">Subir fotos</button>
                        </form>
                    </div>
                    <div class="row" id="photos-gallery" style="gap:12px;">
                        <?php
                        // Group photos by garantia_id
                        $map = array();
                        if (!empty($photos) && is_array($photos)){
                            foreach($photos as $p){
                                $gid = isset($p->garantia_id) ? intval($p->garantia_id) : 0;
                                if (!isset($map[$gid])) $map[$gid] = array();
                                $map[$gid][] = $p;
                            }
                        }
                        // ensure sections order follows garantias array if present
                        $sections = array();
                        if (!empty($garantias) && is_array($garantias)){
                            foreach($garantias as $g) {
                                $sections[] = $g;
                            }
                        } else {
                            // fallback: use keys from map
                            foreach($map as $gid => $arr) {
                                $sections[] = (object) array('id' => $gid, 'nombre' => 'Garantía '.$gid, 'cantidad' => '');
                            }
                        }

                        $any = false;
                        foreach($sections as $g) {
                            $gid = isset($g->id) ? intval($g->id) : 0;
                            $items = isset($map[$gid]) ? $map[$gid] : array();
                            if (empty($items)) continue;
                            $any = true;
                            $title = isset($g->nombre) ? $g->nombre : ('Garantía ' . $gid);
                            $meta = '';
                            if (isset($g->cantidad) && $g->cantidad !== null && $g->cantidad !== '') $meta = ' (Cantidad: ' . intval($g->cantidad) . ')';
                        ?>
                        <div class="col-12 mb-2"><h6><?php echo htmlspecialchars($title . $meta); ?> <small class="text-muted">(<?php echo count($items); ?>)</small></h6></div>
                        <?php foreach($items as $p): $url = base_url(trim((isset($p->filename) ? $p->filename : ''))); ?>
                            <div class="col-md-2 mb-3">
                                <div class="border p-2 text-center">
                                    <a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $url; ?>" style="max-width:100%; max-height:120px; display:block; margin:0 auto 8px;" /></a>
                                    <div class="d-flex justify-content-center" style="gap:6px;">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo $url; ?>" target="_blank">Descargar</a>
                                        <button class="btn btn-sm btn-danger btn-delete-photo" data-id="<?php echo intval(isset($p->id)?$p->id:(isset($p->idfoto)?$p->idfoto:0)); ?>" data-filename="<?php echo htmlspecialchars(isset($p->filename)?$p->filename:''); ?>">Eliminar</button>
                                    </div>
                                    <div class="text-muted small mt-2">Garantía ID: <?php echo $gid; ?></div>
                                </div>
                            </div>
                        <?php endforeach; } // end foreach sections
                            if (!$any) : ?>
                            <div class="col-12 text-center text-muted">No hay fotos registradas para estas garantías.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    function ajaxDelete(id, filename, el){
        if(!confirm('Confirma eliminar esta imagen?')) return;
        var fd = new FormData(); if(id) fd.append('id', id); if(filename) fd.append('filename', filename);
        fetch('<?php echo base_url('garantias/delete_garantia_photo_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
            .then(function(r){ return r.json(); }).then(function(j){ if(j && j.status){ try{ var node = (el.closest ? (el.closest('.mb-2') || el.closest('.col-md-2') || el.closest('.col-md-3') || el.closest('.card')) : null); if(node) node.remove(); }catch(e){} } else { alert('No se pudo eliminar.'); } }).catch(function(){ alert('Error al eliminar.'); });
    }
    document.addEventListener('click', function(e){
        var t = e.target;
        if(t && t.classList && t.classList.contains('btn-delete-photo')){
            var id = t.getAttribute('data-id'); var fn = t.getAttribute('data-filename'); ajaxDelete(id, fn, t);
        }
    }, false);

    // Upload handler: uploads files one-by-one and appends thumbnails
    document.getElementById('btn-upload-garantia').addEventListener('click', function(ev){
        ev.preventDefault();
        var input = document.getElementById('garantia_files');
        if (!input || !input.files || input.files.length === 0) { alert('Selecciona al menos una imagen'); return; }
        var garantiaId = document.getElementById('garantia_select').value || '';
        var files = Array.from(input.files);
        var uploading = function(file){
            var fd = new FormData(); fd.append('solicitud_id', '<?php echo intval($solicitud_id); ?>'); if (garantiaId) fd.append('garantia_id', garantiaId); fd.append('photo', file);
            return fetch('<?php echo base_url('garantias/upload_garantia_photo_ajax'); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function(r){ return r.json(); });
        };
        // sequential upload to avoid server overload
        (function next(){
            if (files.length === 0) { document.getElementById('garantia_files').value = ''; return; }
            var f = files.shift();
            uploading(f).then(function(res){
                if (res && res.status){
                    // append thumbnail to its garantia section (if garantiaId provided), else to top
                    var url = res.url || (f.name);
                    var gid = garantiaId || '';
                    // find section header by garantia id; if not found, append to gallery root
                    var gallery = document.getElementById('photos-gallery');
                    var node = document.createElement('div'); node.className = 'col-md-2 mb-3';
                    node.innerHTML = '<div class="border p-2 text-center"><a href="'+url+'" target="_blank"><img src="'+url+'" style="max-width:100%; max-height:120px; display:block; margin:0 auto 8px;"/></a><div class="d-flex justify-content-center" style="gap:6px;"><a class="btn btn-sm btn-outline-secondary" href="'+url+'" target="_blank">Descargar</a><button class="btn btn-sm btn-danger btn-delete-photo" data-id="'+(res.id||0)+'" data-filename="'+(res.file?('uploads/garantias/solicitud_'+<?php echo intval($solicitud_id); ?>+'/'+res.file):'')+'">Eliminar</button></div><div class="text-muted small mt-2">Garantía ID: '+(gid?gid:'-')+'</div></div>';
                    gallery.appendChild(node);
                } else {
                    alert('Error subiendo '+f.name+': ' + (res && res.message ? res.message : 'respuesta inválida'));
                }
                // next
                setTimeout(next, 200);
            }).catch(function(){ alert('Error subiendo '+f.name); setTimeout(next,200); });
        })();
    });
})();
</script>
