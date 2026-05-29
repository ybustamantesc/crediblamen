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
                                        $garantias_label = '';
                                        if (isset($g->nombre) && trim($g->nombre) !== '') {
                                            $garantias_label = trim($g->nombre);
                                            if ($items_count > 1) {
                                                $garantias_label .= ' +' . ($items_count - 1) . ' más';
                                            }
                                        }
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
                                                            <?php $vId = isset($g->ver_garantia_id) && $g->ver_garantia_id ? $g->ver_garantia_id : $g->id; ?>
                                                            <a class="dropdown-item btn-edit-verificacion" href="#" data-id="<?php echo $vId; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>" data-verified="1" data-garantias="<?php echo html_escape($garantias_label); ?>">Editar Verificación</a>
                                                            <a class="dropdown-item" href="<?php echo base_url('garantias/download_verificacion/'.$vId); ?>">Descargar Verificación</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item btn-verify" href="#" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>" data-garantias="<?php echo html_escape($garantias_label); ?>">Verificar</a>
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
                                                            <?php $vId = isset($g->ver_garantia_id) && $g->ver_garantia_id ? $g->ver_garantia_id : $g->id; ?>
                                                            <a class="dropdown-item btn-edit-verificacion" href="#" data-id="<?php echo $vId; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>" data-verified="1">Editar Verificación</a>
                                                            <a class="dropdown-item" href="<?php echo base_url('garantias/download_verificacion/'.$vId); ?>">Descargar Verificación</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item btn-verify" href="#" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>">Verificar</a>
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
                                                            <?php $vId = isset($g->ver_garantia_id) && $g->ver_garantia_id ? $g->ver_garantia_id : $g->id; ?>
                                                            <a class="dropdown-item btn-edit-verificacion" href="#" data-id="<?php echo $vId; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>" data-verified="1" data-garantias="<?php echo html_escape($garantias_label); ?>">Editar Verificación</a>
                                                            <a class="dropdown-item" href="<?php echo base_url('garantias/download_verificacion/'.$vId); ?>">Descargar Verificación</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item btn-verify" href="#" data-id="<?php echo $g->id; ?>" data-solicitud="<?php echo $g->solicitud_id; ?>" data-solicitud-codigo="<?php echo 'SOL-' . str_pad($g->solicitud_id, 4, '0', STR_PAD_LEFT); ?>" data-solicitud-cliente="<?php echo html_escape($cliente_nombre ?: (isset($g->nombre) ? $g->nombre : '')); ?>" data-garantias="<?php echo html_escape($garantias_label); ?>">Verificar</a>
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
                    <style>
                        /* Mejorar lectura de botones de carga en el modal de verificación */
                        #modalVerificacion .form-control-file {
                            display: block;
                            width: 100%;
                            max-width: 100%;
                            min-width: 0;
                            margin-bottom: 0.5rem;
                        }
                        #modalVerificacion .form-control-file::-webkit-file-upload-button,
                        #modalVerificacion .form-control-file::file-selector-button {
                            display: block;
                            width: 100%;
                        }
                        #modalVerificacion .row .col-6 {
                            flex: 0 0 50%;
                            max-width: 50%;
                        }
                        #modalVerificacion .existing-verification-photo .btn-delete-verification-photo {
                            width: 100%;
                        }
                        @media (min-width: 768px) {
                            #modalVerificacion .row .col-md-2 {
                                flex: 0 0 16.666667%;
                                max-width: 16.666667%;
                            }
                        }
                    </style>
                    <!-- Modal Verificación -->
                    <div class="modal fade" id="modalVerificacion" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title">Verificador de Garantía <small id="ver_codigo_solicitud" class="text-muted">Cargando...</small></h5>
                                        <div id="ver_nombre_cliente_header" class="mt-1" style="font-size: 1.05rem; font-weight: 600;">-</div>
                                    </div>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form id="formVerificacion" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="garantia_id" id="ver_garantia_id">
                                    <input type="hidden" name="solicitud_id" id="ver_solicitud_id">

                                    <!-- Nombre del cliente -->
                                    <div class="mb-3" id="ver_cliente_info_container" style="display: none;">
                                        <div class="alert alert-info mb-3" style="margin-bottom: 1rem;">
                                            <strong>Cliente:</strong> <span id="ver_nombre_cliente">-</span>
                                        </div>
                                    </div>

                                    <!-- Usuario eliminado: se usará el usuario logueado como verificador -->
                                    <div class="form-group">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-2">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle">Garantías asociadas</th>
                                                        <th class="align-middle">Comentario</th>
                                                        <th class="align-middle">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ver_garantia_rows">
                                                    <tr>
                                                        <td colspan="3" class="align-middle text-wrap text-muted">Cargando garantías...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="existing_verification_info" class="mb-3 d-none">
                                        <div class="alert alert-secondary mb-3">
                                            <div><strong>Verificación existente:</strong></div>
                                            <div id="existing_verificacion_meta" class="small text-muted"></div>
                                        </div>
                                        <div id="existing_verification_photos" class="row"></div>
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
                                        <small class="form-text text-muted">Si no selecciona nuevas fotos, se conservarán las actuales.</small>
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

                        function resetVerificationModal() {
                            var form = document.getElementById('formVerificacion');
                            if (form) {
                                form.reset();
                            }
                            if (window.jQuery) {
                                jQuery('#ver_garantia_id').val('');
                                jQuery('#ver_solicitud_id').val('');
                                jQuery('#ver_codigo_solicitud').text('Cargando...');
                                jQuery('#ver_nombre_cliente').text('-');
                                jQuery('#ver_nombre_cliente_header').text('-');
                                jQuery('#ver_cliente_info_container').hide();
                                setVerificationGarantiaRows([]);
                                jQuery('#existing_verification_info').addClass('d-none');
                                jQuery('#existing_verificacion_meta').text('');
                                jQuery('#existing_verification_photos').empty();
                            } else {
                                var vg = document.getElementById('ver_garantia_id');
                                var vs = document.getElementById('ver_solicitud_id');
                                var info = document.getElementById('existing_verification_info');
                                var meta = document.getElementById('existing_verificacion_meta');
                                var codigoEl = document.getElementById('ver_codigo_solicitud');
                                var clienteEl = document.getElementById('ver_nombre_cliente');
                                var clienteHeaderEl = document.getElementById('ver_nombre_cliente_header');
                                var containerEl = document.getElementById('ver_cliente_info_container');
                                if (vg) vg.value = '';
                                if (vs) vs.value = '';
                                if (codigoEl) codigoEl.textContent = 'Cargando...';
                                if (clienteEl) clienteEl.textContent = '-';
                                if (clienteHeaderEl) clienteHeaderEl.textContent = '-';
                                if (containerEl) containerEl.style.display = 'none';
                                setVerificationGarantiaRows([]);
                                if (info) info.classList.add('d-none');
                                if (meta) meta.textContent = '';
                                var photos = document.getElementById('existing_verification_photos');
                                if (photos) photos.innerHTML = '';
                            }
                        }

                        function loadSolicitudInfo(solicitud_id) {
                            if (!solicitud_id) return;
                            var url = '<?php echo base_url('garantias/get_solicitud_info_ajax/'); ?>' + solicitud_id;
                            fetch(url, { credentials: 'same-origin' })
                                .then(function(resp) { return resp.json(); })
                                .then(function(data) {
                                    if (data && data.success) {
                                        var codigoEl = window.jQuery 
                                            ? jQuery('#ver_codigo_solicitud') 
                                            : document.getElementById('ver_codigo_solicitud');
                                        var clienteEl = window.jQuery 
                                            ? jQuery('#ver_nombre_cliente') 
                                            : document.getElementById('ver_nombre_cliente');
                                        var containerEl = window.jQuery 
                                            ? jQuery('#ver_cliente_info_container') 
                                            : document.getElementById('ver_cliente_info_container');
                                        
                                        if (window.jQuery) {
                                            codigoEl.text(data.codigo_solicitud || '-');
                                            clienteEl.text(data.nombre_cliente || '-');
                                            jQuery('#ver_nombre_cliente_header').text(data.nombre_cliente || '-');
                                            if (data.nombre_cliente) {
                                                containerEl.show();
                                            }
                                        } else {
                                            if (codigoEl) codigoEl.textContent = data.codigo_solicitud || '-';
                                            if (clienteEl) clienteEl.textContent = data.nombre_cliente || '-';
                                            var clienteHeaderEl = document.getElementById('ver_nombre_cliente_header');
                                            if (clienteHeaderEl) clienteHeaderEl.textContent = data.nombre_cliente || '-';
                                            if (containerEl && data.nombre_cliente) {
                                                containerEl.style.display = 'block';
                                            }
                                        }
                                    }
                                })
                                .catch(function() {
                                    // Silently fail - show default values
                                });
                        }

                        function setVerificationGarantiaRows(garantias) {
                            var rows = document.getElementById('ver_garantia_rows');
                            if (!rows) return;
                            rows.innerHTML = '';
                            if (!Array.isArray(garantias) || garantias.length === 0) {
                                var tr = document.createElement('tr');
                                var td = document.createElement('td');
                                td.setAttribute('colspan', '3');
                                td.className = 'align-middle text-wrap text-muted';
                                td.textContent = 'No hay garantías asociadas.';
                                tr.appendChild(td);
                                rows.appendChild(tr);
                                return;
                            }
                            garantias.forEach(function(item) {
                                var garantiaId = item && item.id ? item.id : '';
                                var garantiaNombre = item && item.nombre ? item.nombre : '-';
                                var tr = document.createElement('tr');
                                if (garantiaId) {
                                    tr.setAttribute('data-garantia-id', garantiaId);
                                }
                                var tdName = document.createElement('td');
                                tdName.className = 'align-middle text-wrap';
                                tdName.textContent = garantiaNombre;
                                var tdComment = document.createElement('td');
                                tdComment.className = 'align-middle';
                                var textarea = document.createElement('textarea');
                                textarea.className = 'form-control';
                                textarea.rows = 2;
                                if (garantiaId) {
                                    textarea.name = 'comentario[' + garantiaId + ']';
                                } else {
                                    textarea.name = 'comentario[]';
                                }
                                tdComment.appendChild(textarea);
                                var tdStatus = document.createElement('td');
                                tdStatus.className = 'align-middle';
                                var select = document.createElement('select');
                                select.className = 'form-control form-control-sm';
                                if (garantiaId) {
                                    select.name = 'estado_aprobacion[' + garantiaId + ']';
                                } else {
                                    select.name = 'estado_aprobacion[]';
                                }
                                var optionNotApproved = document.createElement('option');
                                optionNotApproved.value = 'No aprobado';
                                optionNotApproved.textContent = 'No aprobado';
                                var optionApproved = document.createElement('option');
                                optionApproved.value = 'Aprobado';
                                optionApproved.textContent = 'Aprobado';
                                select.appendChild(optionNotApproved);
                                select.appendChild(optionApproved);
                                select.value = 'No aprobado';
                                tdStatus.appendChild(select);
                                
                                // Campo oculto para el nombre de la garantía
                                var hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                if (garantiaId) {
                                    hiddenInput.name = 'nombre_garantia[' + garantiaId + ']';
                                } else {
                                    hiddenInput.name = 'nombre_garantia[]';
                                }
                                hiddenInput.value = garantiaNombre;
                                tdStatus.appendChild(hiddenInput);
                                
                                tr.appendChild(tdName);
                                tr.appendChild(tdComment);
                                tr.appendChild(tdStatus);
                                rows.appendChild(tr);
                            });
                        }

                        function loadVerificationGarantias(solicitud_id, callback) {
                            var url = '<?php echo base_url('garantias/get_garantias_by_solicitud_ajax/'); ?>' + solicitud_id;
                            fetch(url, { credentials: 'same-origin' }).then(function(resp) {
                                return resp.json();
                            }).then(function(data) {
                                if (data && data.success && Array.isArray(data.garantias)) {
                                    setVerificationGarantiaRows(data.garantias);
                                } else {
                                    setVerificationGarantiaRows([]);
                                }
                                if (typeof callback === 'function') callback();
                            }).catch(function() {
                                setVerificationGarantiaRows([]);
                                if (typeof callback === 'function') callback();
                            });
                        }

                        function populateExistingVerification(data) {
                            var info = window.jQuery ? jQuery('#existing_verification_info') : document.getElementById('existing_verification_info');
                            var meta = window.jQuery ? jQuery('#existing_verificacion_meta') : document.getElementById('existing_verificacion_meta');
                            var photos = window.jQuery ? jQuery('#existing_verification_photos') : document.getElementById('existing_verificacion_photos');
                            var text = [];
                            if (data.verification) {
                                if (data.verification.verificador_usuario) {
                                    text.push('Verificador: ' + data.verification.verificador_usuario);
                                }
                                if (data.verification.created_at) {
                                    text.push('Fecha: ' + data.verification.created_at);
                                }
                                if (data.verification.comentario) {
                                    text.push('Comentario actual: ' + data.verification.comentario);
                                }
                            }
                            var photoHtml = '';
                            if (data.photos && data.photos.length) {
                                data.photos.forEach(function(photo){
                                    photoHtml += '<div class="col-6 col-md-2 mb-2 existing-verification-photo" data-field="' + photo.field + '">';
                                    photoHtml += '<a href="' + photo.url + '" target="_blank" class="d-block text-truncate">';
                                    photoHtml += '<img src="' + photo.url + '" class="img-fluid img-thumbnail" alt="Foto">';
                                    photoHtml += '</a>';
                                    photoHtml += '<button type="button" class="btn btn-sm btn-danger btn-delete-verification-photo mt-2" data-field="' + photo.field + '">Eliminar</button>';
                                    photoHtml += '</div>';
                                });
                            }
                            if (window.jQuery) {
                                info.removeClass('d-none');
                                meta.text(text.join(' | '));
                                photos.html(photoHtml);
                            } else {
                                if (info) info.classList.remove('d-none');
                                if (meta) meta.textContent = text.join(' | ');
                                if (photos) photos.innerHTML = photoHtml;
                            }
                            if (data.verification && data.verification.garantia_id && data.verification.comentario) {
                                var textarea = document.querySelector('textarea[name="comentario[' + data.verification.garantia_id + ']"]');
                                if (textarea) {
                                    textarea.value = data.verification.comentario;
                                }
                            }
                        }

                        // Aplica una lista de verificaciones (por garantía) a las filas del modal
                        function applyVerificationsToRows(verifications) {
                            if (!Array.isArray(verifications) || verifications.length === 0) return;
                            verifications.forEach(function(v) {
                                try {
                                    var gid = v.garantia_id || v.garantia_id === 0 ? v.garantia_id : null;
                                    if (!gid) return;
                                    var textarea = document.querySelector('textarea[name="comentario[' + gid + ']"]');
                                    if (textarea && (v.comentario !== undefined && v.comentario !== null)) {
                                        textarea.value = v.comentario;
                                    }
                                    var select = document.querySelector('select[name="estado_aprobacion[' + gid + ']"]');
                                    if (select && v.estado_aprobacion) {
                                        // try to match option ignoring case
                                        var matched = false;
                                        for (var i=0;i<select.options.length;i++){
                                            if (select.options[i].value.toString().toLowerCase() === v.estado_aprobacion.toString().toLowerCase()){
                                                select.selectedIndex = i; matched = true; break;
                                            }
                                        }
                                        if (!matched) {
                                            var opt = document.createElement('option'); opt.value = v.estado_aprobacion; opt.textContent = v.estado_aprobacion; select.appendChild(opt); select.value = v.estado_aprobacion;
                                        }
                                    }
                                    // hidden nombre_garantia
                                    var hidden = document.querySelector('input[name="nombre_garantia[' + gid + ']"]');
                                    if (hidden && v.nombre_garantia) hidden.value = v.nombre_garantia;
                                } catch (e) { console.error('applyVerificationsToRows error', e); }
                            });
                        }

                        function deleteVerificationPhoto(field) {
                            var guaranteeId = window.jQuery ? jQuery('#ver_garantia_id').val() : (document.getElementById('ver_garantia_id') ? document.getElementById('ver_garantia_id').value : null);
                            if (!guaranteeId || !field) {
                                alert('No se pudo determinar la garantía o foto a eliminar.');
                                return;
                            }
                            if (!confirm('¿Eliminar esta foto de la verificación? Esta acción no se puede deshacer.')) {
                                return;
                            }
                            var url = '<?php echo base_url('garantias/delete_verificacion_photo_ajax'); ?>';
                            var payload = new FormData();
                            payload.append('garantia_id', guaranteeId);
                            payload.append('field', field);
                            if (window.jQuery) {
                                jQuery.ajax({
                                    url: url,
                                    data: payload,
                                    processData: false,
                                    contentType: false,
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(resp) {
                                        if (resp && resp.success) {
                                            var photoCol = jQuery('#existing_verification_photos').find('.existing-verification-photo[data-field="' + field + '"]');
                                            if (photoCol.length) {
                                                photoCol.remove();
                                            }
                                            if (jQuery('#existing_verification_photos').children().length === 0) {
                                                jQuery('#existing_verification_info').addClass('d-none');
                                            }
                                        } else {
                                            alert((resp && resp.message) ? resp.message : 'No se pudo eliminar la foto.');
                                        }
                                    },
                                    error: function(xhr) {
                                        console.error('Error al eliminar foto de verificación:', xhr);
                                        alert('Error de red al eliminar la foto.');
                                    }
                                });
                            } else {
                                fetch(url, {
                                    method: 'POST',
                                    body: payload,
                                    credentials: 'same-origin'
                                }).then(function(resp) {
                                    if (!resp.ok) {
                                        return resp.text().then(function(text) {
                                            throw new Error('HTTP ' + resp.status + ' ' + resp.statusText + '\n' + text);
                                        });
                                    }
                                    return resp.json();
                                }).then(function(data) {
                                    if (data && data.success) {
                                        var photos = document.getElementById('existing_verification_photos');
                                        if (photos) {
                                            var photoEl = photos.querySelector('.existing-verification-photo[data-field="' + field + '"]');
                                            if (photoEl) {
                                                photoEl.parentNode.removeChild(photoEl);
                                            }
                                            if (photos.children.length === 0) {
                                                var info = document.getElementById('existing_verification_info');
                                                if (info) { info.classList.add('d-none'); }
                                            }
                                        }
                                    } else {
                                        alert((data && data.message) ? data.message : 'No se pudo eliminar la foto.');
                                    }
                                }).catch(function(err) {
                                    console.error('Error al eliminar foto de verificación:', err);
                                    alert('Error de red al eliminar la foto.');
                                });
                            }
                        }

                        function initVerificationModalCloseBehavior() {
                            var modal = document.getElementById('modalVerificacion');
                            if (!modal) return;
                            if (window.jQuery && typeof jQuery(modal).on === 'function') {
                                jQuery(modal).on('hidden.bs.modal', function() {
                                    resetVerificationModal();
                                });
                            }
                            var closeButtons = modal.querySelectorAll('[data-dismiss="modal"], .close');
                            closeButtons.forEach(function(button) {
                                button.addEventListener('click', function() {
                                    resetVerificationModal();
                                });
                            });
                        }

                        function loadExistingVerification(id) {
                            var url = '<?php echo base_url('garantias/get_verificacion_ajax/'); ?>' + id;
                            if (window.jQuery) {
                                jQuery.getJSON(url).done(function(resp){
                                    if (resp && resp.success) {
                                        jQuery('#ver_garantia_id').val(id);
                                        if (resp.verification && resp.verification.solicitud_id) {
                                            jQuery('#ver_solicitud_id').val(resp.verification.solicitud_id);
                                            loadSolicitudInfo(resp.verification.solicitud_id);
                                        }
                                        populateExistingVerification(resp);
                                        // además cargar todas las verificaciones de la solicitud y aplicar a las filas
                                        if (resp.verification && resp.verification.solicitud_id) {
                                            var url2 = '<?php echo base_url('garantias/get_verificaciones_by_solicitud_ajax/'); ?>' + resp.verification.solicitud_id;
                                            jQuery.getJSON(url2).done(function(r2){ if (r2 && r2.success && Array.isArray(r2.verifications)) { applyVerificationsToRows(r2.verifications); } }).always(function(){ showModalByApi(); });
                                        } else {
                                            showModalByApi();
                                        }
                                    } else {
                                        alert((resp && resp.message) ? resp.message : 'No se encontró la verificación existente.');
                                        showModalByApi();
                                    }
                                }).fail(function(){
                                    alert('No se pudo cargar la verificación existente.');
                                    showModalByApi();
                                });
                            } else {
                                fetch(url, { credentials: 'same-origin' }).then(function(resp){
                                    return resp.json();
                                }).then(function(data){
                                    if (data && data.success) {
                                        var vg = document.getElementById('ver_garantia_id');
                                        var vs = document.getElementById('ver_solicitud_id');
                                        if (vg) vg.value = id;
                                        if (vs && data.verification && data.verification.solicitud_id) {
                                            vs.value = data.verification.solicitud_id;
                                            loadSolicitudInfo(data.verification.solicitud_id);
                                        }
                                        populateExistingVerification(data);
                                        if (data.verification && data.verification.solicitud_id) {
                                            var url2 = '<?php echo base_url('garantias/get_verificaciones_by_solicitud_ajax/'); ?>' + data.verification.solicitud_id;
                                            fetch(url2, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(d2){ if (d2 && d2.success && Array.isArray(d2.verifications)) { applyVerificationsToRows(d2.verifications); } }).finally(function(){ showModalByApi(); });
                                        } else {
                                            showModalByApi();
                                        }
                                    } else {
                                        alert((data && data.message) ? data.message : 'No se encontró la verificación existente.');
                                    }
                                    showModalByApi();
                                }).catch(function(){
                                    alert('No se pudo cargar la verificación existente.');
                                    showModalByApi();
                                });
                            }
                        }

                        // jQuery-based handlers only if jQuery is loaded
                        if (window.jQuery) {
                            initVerificationModalCloseBehavior();
                            jQuery(document).on('click', '.btn-verify, .btn-edit-verificacion', function(){
                                var id = jQuery(this).data('id');
                                var solicitud = jQuery(this).data('solicitud');
                                var solicitudCodigo = jQuery(this).data('solicitud-codigo');
                                var solicitudCliente = jQuery(this).data('solicitud-cliente');
                                var isVerified = jQuery(this).data('verified') === 1 || jQuery(this).data('verified') === '1' || jQuery(this).data('verified') === true;
                                resetVerificationModal();
                                jQuery('#ver_garantia_id').val(id);
                                jQuery('#ver_solicitud_id').val(solicitud);
                                if (solicitudCodigo) {
                                    jQuery('#ver_codigo_solicitud').text(solicitudCodigo);
                                }
                                if (solicitudCliente) {
                                    jQuery('#ver_nombre_cliente').text(solicitudCliente);
                                    jQuery('#ver_nombre_cliente_header').text(solicitudCliente);
                                    jQuery('#ver_cliente_info_container').show();
                                }
                                loadSolicitudInfo(solicitud);
                                loadVerificationGarantias(solicitud, function(){
                                    if (isVerified) {
                                        loadExistingVerification(id);
                                    } else {
                                        showModalByApi();
                                    }
                                });
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

                            jQuery(document).on('click', '.btn-delete-verification-photo', function(e) {
                                e.preventDefault();
                                var field = jQuery(this).data('field');
                                if (field) {
                                    deleteVerificationPhoto(field);
                                }
                            });
                        }

                        if (!window.jQuery) {
                            initVerificationModalCloseBehavior();
                            document.addEventListener('click', function(ev){
                                var deleteBtn = ev.target.closest && ev.target.closest('.btn-delete-verification-photo');
                                if (deleteBtn) {
                                    ev.preventDefault();
                                    var field = deleteBtn.getAttribute('data-field');
                                    if (field) {
                                        deleteVerificationPhoto(field);
                                    }
                                    return;
                                }
                                var btn = ev.target.closest && ev.target.closest('.btn-verify, .btn-edit-verificacion');
                                if (!btn) return;
                                ev.preventDefault();
                                var id = btn.getAttribute('data-id');
                                var solicitud = btn.getAttribute('data-solicitud');
                                var solicitudCodigo = btn.getAttribute('data-solicitud-codigo');
                                var solicitudCliente = btn.getAttribute('data-solicitud-cliente');
                                var garantiaNames = btn.getAttribute('data-garantias');
                                var isVerified = btn.getAttribute('data-verified') === '1';
                                resetVerificationModal();
                                var vg = document.getElementById('ver_garantia_id');
                                var vs = document.getElementById('ver_solicitud_id');
                                if (vg) vg.value = id;
                                if (vs) vs.value = solicitud;
                                var codigoEl = document.getElementById('ver_codigo_solicitud');
                                var clienteEl = document.getElementById('ver_nombre_cliente');
                                var containerEl = document.getElementById('ver_cliente_info_container');
                                if (solicitudCodigo && codigoEl) {
                                    codigoEl.textContent = solicitudCodigo;
                                }
                                if (solicitudCliente && clienteEl) {
                                    clienteEl.textContent = solicitudCliente;
                                    var clienteHeaderEl = document.getElementById('ver_nombre_cliente_header');
                                    if (clienteHeaderEl) {
                                        clienteHeaderEl.textContent = solicitudCliente;
                                    }
                                    if (containerEl) {
                                        containerEl.style.display = 'block';
                                    }
                                }
                                loadSolicitudInfo(solicitud);
                                loadVerificationGarantias(solicitud, function(){
                                    if (isVerified) {
                                        loadExistingVerification(id);
                                    } else {
                                        showModalByApi();
                                    }
                                });
                            });
                        }

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
