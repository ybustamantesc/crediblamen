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
                            <input id="garantias_search" class="form-control" placeholder="Buscar por código o cliente..." />
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
                            <small class="text-muted">Filtrar por estado o buscar por cliente/código.</small>
                        </div>
                    </div>
                    <style>
                        #garantias-table{ table-layout: auto; width:100%; max-width: 100%; margin: 0; box-sizing: border-box; }
                        #garantias-table{ max-width:100%; box-sizing:border-box; display: table; }
                        #garantias-table th:first-child, #garantias-table td:first-child{
                            width:auto; min-width:40px; max-width:80px; text-align:center;
                            padding-left:.4rem; padding-right:.4rem;
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                        }
                        #garantias-table th:last-child, #garantias-table td:last-child{
                            width: 90px; min-width:70px; white-space: nowrap;
                            vertical-align: middle; text-align:center;
                        }
                        /* Match compact table styles used by solicitudes list so dimensions align */
                        #garantias-table.table-compact td, #garantias-table.table-compact th{
                            padding: .12rem .28rem;
                            vertical-align: middle;
                            font-size: .72rem;
                            line-height: 1.02;
                            white-space: normal;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        #garantias-table.table-compact thead th{ font-size: .7rem; padding: .18rem .28rem; }
                        #garantias-table.table-compact .btn{ padding: .12rem .28rem; font-size: .72rem; min-width: auto; }
                        /* Keep Cliente column from stretching too far while allowing wrapping */
                        #garantias-table th:nth-child(2), #garantias-table td:nth-child(2){
                            max-width: 180px;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        /* Narrow action column */
                        #garantias-table th:last-child, #garantias-table td:last-child{
                            width: 85px;
                            white-space: nowrap;
                        }
                        #garantias-table td:last-child .btn-group { white-space: nowrap; }
                        #garantias-table td:last-child .btn { padding: .16rem .28rem; font-size: .75rem; min-width: auto; }
                        /* Ensure only one of table or cards is visible (avoid conflicts from other CSS) */
                        #garantias-table-wrap { display: block; }
                        #garantias-cards-wrap { display: none; }
                        @media (max-width: 767.98px) {
                            /* Keep table visible on small screens so DataTables pagination works and hide cards */
                            #garantias-table-wrap { display: block !important; }
                            #garantias-cards-wrap { display: none !important; }
                            #garantias-table td:last-child .btn { padding: .16rem .28rem; font-size: .72rem; }
                        }
                        @media (min-width: 768px) {
                            #garantias-cards-wrap { display: none !important; }
                        }
                    </style>
                    <div id="garantias-table-wrap" class="table-responsive d-block">
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
                                        if (isset($g->nombres) || isset($g->apellidos)) {
                                            $cliente_nombre = trim((isset($g->nombres)?$g->nombres:'') . ' ' . (isset($g->apellidos)?$g->apellidos:''));
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
                                                <!-- Inline dropdown for desktop to preserve column width -->
                                                <div class="d-none d-md-flex btn-group" role="group" aria-label="Acciones">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acciones</button>
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
                                if (isset($g->nombres) || isset($g->apellidos)) {
                                    $cliente_nombre = trim((isset($g->nombres)?$g->nombres:'') . ' ' . (isset($g->apellidos)?$g->apellidos:''));
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

            <script>
                (function waitForGarantiasDataTable(){
                    if(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable){
                        (function($){
                            try{
                                var table = $('#garantias-table').DataTable({
                                    "bSort": false,
                                    "responsive": true,
                                    "autoWidth": false,
                                    "pageLength": 10,
                                    "lengthMenu": [[10,25,50,100],[10,25,50,100]]
                                });
                                var wrapper = $(table.table().container());
                                wrapper.find('.dataTables_filter').hide();
                                $('#garantias_search').off('input.dt').on('input', function(){ table.search(this.value).draw(); });
                                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
                                    if (!settings || !settings.nTable || settings.nTable.id !== 'garantias-table') return true;
                                    var status = $('#garantias_filter_status').val();
                                    if (!status || status === 'all') return true;
                                    var row = table.row(dataIndex).node();
                                    return ($(row).data('status') || 'pending') === status;
                                });
                                $('#garantias_filter_status').off('change.dt').on('change', function(){ table.draw(); });
                            }catch(e){ console.error('Garantias DataTable error', e); }
                        })(jQuery);
                    } else { setTimeout(waitForGarantiasDataTable, 100); }
                })();
            </script>

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
                                    error: function(xhr){
                                        console.error('Error al guardar verificación:', xhr);
                                        alert('Error de red al guardar la verificación.\nHTTP ' + xhr.status + ' ' + xhr.statusText + '\n' + xhr.responseText);
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
                                    if (!resp.ok) {
                                        return resp.text().then(function(text){
                                            throw new Error('HTTP ' + resp.status + ' ' + resp.statusText + '\n' + text);
                                        });
                                    }
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
                                }).catch(function(err){
                                    console.error('Error al guardar verificación:', err);
                                    alert('Error de red al guardar la verificación.\n' + (err && err.message ? err.message : 'Revise la consola para más detalles.'));
                                });
                            });
                        }
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
