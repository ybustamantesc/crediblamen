<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-shield-alt'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo isset($titulo) ? $titulo : 'Formato de Garantía'; ?></h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : 'Listado de formatos de garantía'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <a data-toggle="tooltip" data-placement="right" title="Nuevo Formato" href="<?php echo base_url('garantias/create/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo Formato</a>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="garantias_search" class="form-control" placeholder="Buscar por código o garantía..." />
                        </div>
                        <div class="col-md-3">
                            <select id="garantias_filter_status" class="form-control">
                                <option value="all">Todos</option>
                                <option value="pending">Pendiente</option>
                                <option value="approved">Aprobado</option>
                                <option value="rejected">Rechazado</option>
                                <option value="annulled">Anulado</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-right">
                            <small class="text-muted">Filtrar por estado o buscar por código/garantía.</small>
                        </div>
                    </div>
                    <style>
                        /* Match compact table styles used by solicitudes list so dimensions align */
                        #garantias-table.table-compact td, #garantias-table.table-compact th{
                            padding: .12rem .28rem !important;
                            vertical-align: middle !important;
                            font-size: .72rem !important;
                            line-height: 1.02 !important;
                            white-space: nowrap !important;
                        }
                        #garantias-table.table-compact thead th{ font-size: .7rem !important; padding: .18rem .28rem !important; }
                        #garantias-table.table-compact .btn{ padding: .12rem .28rem !important; font-size: .72rem !important; min-width: auto !important; }
                        /* Truncate Cliente to prevent layout stretching */
                        #garantias-table th:nth-child(4), #garantias-table td:nth-child(4){
                            max-width: 180px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        /* Actions column: allow wider space and prevent overflow */
                        #garantias-table th:last-child, #garantias-table td:last-child{ width: 220px; white-space: nowrap; }
                    </style>
                    <style>
                        /* Ensure only one of table or cards is visible (avoid conflicts from other CSS) */
                        #garantias-table-wrap { display: block; }
                        #garantias-cards-wrap { display: none; }
                        @media (max-width: 767.98px) {
                            #garantias-table-wrap { display: none !important; }
                            #garantias-cards-wrap { display: block !important; }
                        }
                        @media (min-width: 768px) {
                            #garantias-table-wrap { display: block !important; }
                            #garantias-cards-wrap { display: none !important; }
                        }
                    </style>
                    <div id="garantias-table-wrap" class="table-responsive d-none d-md-block">
                        <table id="garantias-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>Solicitud</th>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Destino Conami</th>
                                    <th>Creado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($garantias)) : foreach ($garantias as $g) : 
                                        $status = isset($g->aprob_status) ? $g->aprob_status : 'pending';
                                        $rowClass = '';
                                        if ($status === 'approved') $rowClass = 'table-success';
                                        elseif ($status === 'rejected') $rowClass = 'table-danger';
                                        elseif ($status === 'annulled') $rowClass = 'table-secondary';
                                        elseif ($status === 'pending') $rowClass = 'table-warning';
                                        $cliente_nombre = '';
                                        if (isset($g->apellidos) || isset($g->nombres)) {
                                            $cliente_nombre = trim((isset($g->apellidos)?$g->apellidos:'') . ' ' . (isset($g->nombres)?$g->nombres:''));
                                        } elseif (isset($g->cliente_nombre)) {
                                            $cliente_nombre = $g->cliente_nombre;
                                        }
                                        $items_count = isset($g->items_count) ? (int)$g->items_count : 1;
                                    ?>
                                        <tr class="<?php echo $rowClass; ?>" data-solicitud-id="<?php echo $g->solicitud_id; ?>" data-status="<?php echo $status; ?>">
                                            <td><?php echo $g->solicitud_id; ?></td>
                                            <td><?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?></td>
                                            <td><?php echo html_escape(isset($g->rubro_credito) ? $g->rubro_credito : ''); ?></td>
                                            <td><?php echo html_escape(isset($g->nombre_asesor) ? $g->nombre_asesor : ''); ?></td>
                                            <td>
                                                <!-- Inline buttons for desktop -->
                                                <div class="d-none d-md-flex btn-group" role="group" aria-label="Acciones">
                                                    <a class="btn btn-sm btn-info" href="<?php echo base_url('garantias/create/'.$g->solicitud_id); ?>">Editar</a>
                                                    <a class="btn btn-sm btn-secondary" href="<?php echo base_url('garantias/view_by_solicitud/'.$g->solicitud_id); ?>">Ver</a>
                                                    <a class="btn btn-sm btn-secondary" href="<?php echo base_url('garantias/pdf_by_solicitud/'.$g->solicitud_id); ?>">PDF</a>
                                                    <?php if (isset($g->verified) && $g->verified): ?>
                                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('garantias/download_verificacion/'.$g->id); ?>">Descargar</a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-warning btn-verify" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>">Verificar</button>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Compact dropdown for small screens -->
                                                <div class="d-md-none btn-group">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/create/'.$g->solicitud_id); ?>">Editar</a>
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/view_by_solicitud/'.$g->solicitud_id); ?>">Ver</a>
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/pdf_by_solicitud/'.$g->solicitud_id); ?>">PDF</a>
                                                        <?php if (isset($g->verified) && $g->verified): ?>
                                                            <a class="dropdown-item" href="<?php echo base_url('garantias/download_verificacion/'.$g->id); ?>">Descargar Verificación</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item btn-verify" href="#" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>">Verificar</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay registros para mostrar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div id="garantias-cards-wrap" class="d-block d-md-none">
                        <div class="row">
                            <?php if (!empty($garantias)) : foreach ($garantias as $g) : 
                                $status = isset($g->aprob_status) ? $g->aprob_status : 'pending';
                                $cardClass = '';
                                if ($status === 'approved') $cardClass = 'border-success';
                                elseif ($status === 'rejected') $cardClass = 'border-danger';
                                elseif ($status === 'annulled') $cardClass = 'border-secondary';
                                $cliente_nombre = '';
                                if (isset($g->apellidos) || isset($g->nombres)) {
                                    $cliente_nombre = trim((isset($g->apellidos)?$g->apellidos:'') . ' ' . (isset($g->nombres)?$g->nombres:''));
                                } elseif (isset($g->cliente_nombre)) {
                                    $cliente_nombre = $g->cliente_nombre;
                                }
                            ?>
                                <div class="col-12">
                                    <div class="card mb-2 garantia-card <?php echo $cardClass; ?>" data-solicitud-id="<?php echo $g->solicitud_id; ?>" data-status="<?php echo $status; ?>">
                                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="font-weight-bold"><?php echo html_escape($cliente_nombre ?: (isset($g->nombre)?$g->nombre:'')); ?></div>
                                                <div class="text-muted small"><?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?></div>
                                            </div>
                                            <div>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">Acciones</button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/create/'.$g->solicitud_id); ?>">Editar</a>
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/view_by_solicitud/'.$g->solicitud_id); ?>">Ver</a>
                                                        <a class="dropdown-item" href="<?php echo base_url('garantias/pdf_by_solicitud/'.$g->solicitud_id); ?>">PDF</a>
                                                        <?php if (isset($g->verified) && $g->verified): ?>
                                                            <a class="dropdown-item" href="<?php echo base_url('garantias/download_verificacion/'.$g->id); ?>">Descargar Verificación</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item btn-verify" href="#" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>">Verificar</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="col-12"><div class="text-center text-muted">No hay registros para mostrar.</div></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- Modal Verificación -->
                    <div class="modal fade" id="modalVerificacion" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Verificador de Garantía</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form id="formVerificacion" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="garantia_id" id="ver_garantia_id">
                                    <input type="hidden" name="solicitud_id" id="ver_solicitud_id">

                                    <!-- Usuario eliminado: se usará el usuario logueado como verificador -->
                                    <div class="form-group">
                                        <label for="comentario">Comentario</label>
                                        <textarea name="comentario" id="comentario" class="form-control" rows="5"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Fotos evidenciales (máx. 5)</label>
                                        <div class="row">
                                            <?php for ($i=1;$i<=5;$i++): ?>
                                            <div class="col-6 col-md-2 mb-2">
                                                <input type="file" name="ver_foto<?php echo $i; ?>" accept="image/*" class="form-control-file">
                                            </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Verificación</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript">
                    (function(){
                        // Helper to show modal using available APIs
                        function showModalByApi() {
                            if (window.jQuery && typeof jQuery('#modalVerificacion').modal === 'function') {
                                jQuery('#modalVerificacion').modal('show');
                                return;
                            }
                            if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                                var m = new bootstrap.Modal(document.getElementById('modalVerificacion'));
                                m.show();
                                return;
                            }
                            var mm = document.getElementById('modalVerificacion');
                            if (mm) { mm.style.display = 'block'; mm.classList.add('show'); }
                        }

                        // jQuery-based handlers only if jQuery is loaded
                        if (window.jQuery) {
                                jQuery(document).on('click', '.btn-verify', function(){
                                console.log('btn-verify clicked (jQuery)', this);
                                var id = jQuery(this).data('id');
                                var solicitud = jQuery(this).data('solicitud');
                                jQuery('#ver_garantia_id').val(id);
                                jQuery('#ver_solicitud_id').val(solicitud);
                                jQuery('#comentario').val('');
                                showModalByApi();
                            });

                            jQuery('#formVerificacion').on('submit', function(e){
                                e.preventDefault();
                                var form = this;
                                var fd = new FormData(form);
                                jQuery.ajax({
                                    url: '<?php echo base_url('garantias/save_verificacion'); ?>',
                                    data: fd,
                                    processData: false,
                                    contentType: false,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(resp){
                                        if (resp && resp.success) {
                                            alert(resp.message || 'Verificación guardada');
                                            if (window.jQuery && typeof jQuery('#modalVerificacion').modal === 'function') {
                                                jQuery('#modalVerificacion').modal('hide');
                                            }
                                        } else {
                                            alert((resp && resp.message) ? resp.message : 'Error en la verificación');
                                        }
                                    },
                                    error: function(){
                                        alert('Error de red al guardar la verificación.');
                                    }
                                });
                            });
                        }

                        // Vanilla JS delegated listener (works even without jQuery)
                        document.addEventListener('click', function(ev){
                            var btn = ev.target.closest && ev.target.closest('.btn-verify');
                            if (!btn) return;
                            console.log('btn-verify clicked (vanilla)', btn);
                            var id = btn.getAttribute('data-id');
                            var solicitud = btn.getAttribute('data-solicitud');
                            var vg = document.getElementById('ver_garantia_id');
                            var vs = document.getElementById('ver_solicitud_id');
                            var cm = document.getElementById('comentario');
                            if (vg) vg.value = id;
                            if (vs) vs.value = solicitud;
                            if (cm) cm.value = '';
                            showModalByApi();
                        });

                        // Vanilla form submit fallback using fetch if jQuery not present
                        var formEl = document.getElementById('formVerificacion');
                        if (formEl && !window.jQuery) {
                            formEl.addEventListener('submit', function(e){
                                e.preventDefault();
                                var fd = new FormData(formEl);
                                fetch('<?php echo base_url('garantias/save_verificacion'); ?>', {
                                    method: 'POST',
                                    body: fd,
                                    credentials: 'same-origin'
                                }).then(function(resp){
                                    return resp.json();
                                }).then(function(data){
                                    if (data && data.success) {
                                        alert(data.message || 'Verificación guardada');
                                        // try to hide modal if possible
                                        if (window.jQuery && typeof jQuery('#modalVerificacion').modal === 'function') {
                                            jQuery('#modalVerificacion').modal('hide');
                                        } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                                            // nothing to do, bootstrap modal was used to show
                                        } else {
                                            var mm = document.getElementById('modalVerificacion'); if (mm) { mm.style.display = 'none'; mm.classList.remove('show'); }
                                        }
                                    } else {
                                        alert((data && data.message) ? data.message : 'Error en la verificación');
                                    }
                                }).catch(function(){
                                    alert('Error de red al guardar la verificación.');
                                });
                            });
                        }
                    })();

                    // Filters for garantias table (search + status)
                    (function(){
                        function applyGarantiasFilters(){
                            var qEl = document.getElementById('garantias_search');
                            var fEl = document.getElementById('garantias_filter_status');
                            if (!qEl || !fEl) return;
                            var q = qEl.value.toLowerCase().trim();
                            var status = fEl.value;
                            var rows = document.querySelectorAll('#garantias-table tbody tr');
                            rows.forEach(function(r){
                                var code = (r.querySelector('td:nth-child(2)') || {textContent:''}).textContent.toLowerCase();
                                var name = (r.querySelector('td:nth-child(3)') || {textContent:''}).textContent.toLowerCase();
                                var rowStatus = r.getAttribute('data-status') || 'pending';
                                var matchQ = q === '' || code.indexOf(q) !== -1 || name.indexOf(q) !== -1;
                                var matchStatus = status === 'all' || status === rowStatus;
                                if (matchQ && matchStatus) r.style.display = ''; else r.style.display = 'none';
                            });
                            // apply to mobile cards
                            var cards = document.querySelectorAll('.garantia-card');
                            cards.forEach(function(c){
                                var code = (c.querySelector('.text-muted small') || {textContent:''}).textContent.toLowerCase();
                                var name = (c.querySelector('.font-weight-bold') || {textContent:''}).textContent.toLowerCase();
                                var cardStatus = c.getAttribute('data-status') || 'pending';
                                var matchQ = q === '' || code.indexOf(q) !== -1 || name.indexOf(q) !== -1;
                                var matchStatus = status === 'all' || status === cardStatus;
                                if (matchQ && matchStatus) c.style.display = ''; else c.style.display = 'none';
                            });
                        }
                        var sq = document.getElementById('garantias_search');
                        var sf = document.getElementById('garantias_filter_status');
                        if (sq) sq.addEventListener('input', applyGarantiasFilters);
                        if (sf) sf.addEventListener('change', applyGarantiasFilters);
                    })();
                    </script>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                    <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
                </div>
            </footer>

        </div>
    </div>
</div>
