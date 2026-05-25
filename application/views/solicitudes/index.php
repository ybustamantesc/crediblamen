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
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <a data-toggle="tooltip" data-placement="right" title="Nueva Solicitud" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nueva Solicitud</a>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <?php if ($message = $this->session->flashdata('success')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-success alert-success text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($pdf = $this->session->flashdata('pdf_download')) : ?>
                <script type="text/javascript">
                    (function(){
                        var url = '<?php echo $pdf; ?>';
                        try {
                            var w = window.open(url, '_blank');
                            if (!w) {
                                // popup blocked -> navigate (will leave the list view)
                                window.location.href = url;
                            }
                        } catch (e) {
                            window.location.href = url;
                        }
                    })();
                </script>
            <?php endif; ?>
            <!-- Listado de solicitudes -->
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="sol_search" class="form-control" placeholder="Buscar por código o cliente..." />
                        </div>
                        <div class="col-md-3">
                            <select id="sol_filter_status" class="form-control">
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
                    <div class="table-responsive">
                        <table id="solicitudes-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Código</th>
                                    <th>Destino Conami</th>
                                    <th>Creado por</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($solicitudes)) : foreach ($solicitudes as $s) : ?>
                                    <?php
                                        $rowClass = '';
                                        if (isset($s->aprob_status)) {
                                            if ($s->aprob_status === 'approved') $rowClass = 'table-success';
                                            elseif ($s->aprob_status === 'rejected') $rowClass = 'table-danger';
                                            elseif ($s->aprob_status === 'annulled') $rowClass = 'table-secondary';
                                            // pending: no special background (leave default table row color)
                                            elseif ($s->aprob_status === 'pending') $rowClass = '';
                                        }
                                    ?>
                                    <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                    <tr class="<?php echo $rowClass; ?>" data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <td><?php echo $s->idsolicitud; ?></td>
                                        <td>
                                            <?php echo trim($s->nombres . ' ' . $s->apellidos); ?>
                                            <?php if ($status === 'annulled'): ?>
                                                <span class="badge badge-secondary ml-1">Anulado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($s->rubro_credito ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($s->nombre_asesor ?? ''); ?></td>
                                        <td><?php echo (!empty($s->fecha_recepcion) ? $s->fecha_recepcion : (!empty($s->fecha_solicitud) ? $s->fecha_solicitud : '')); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" data-boundary="scrollParent" data-display="static" aria-haspopup="true" aria-expanded="false">Acciones</button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <?php if ($status === 'annulled'): ?>
                                                        <span class="dropdown-item text-muted disabled" data-action="editar" style="pointer-events:none;">Editar (bloqueado por anulación)</span>
                                                    <?php else: ?>
                                                        <a class="dropdown-item" data-action="editar" href="<?php echo base_url($this->router->fetch_class() . '/core/' . $s->idsolicitud); ?>">Editar</a>
                                                    <?php endif; ?>
                                                    <a class="dropdown-item" data-action="historial" href="<?php echo base_url($this->router->fetch_class() . '/comments/' . $s->idsolicitud); ?>">Historial</a>
                                                    <a class="dropdown-item btn-notes" data-action="notas" href="#" data-id="<?php echo $s->idsolicitud; ?>">Notas</a>
                                                    <?php if ($status === 'annulled'): ?>
                                                        <span class="dropdown-item text-muted disabled" data-action="nueva-nota" style="pointer-events:none;">Nueva Nota (bloqueado por anulación)</span>
                                                    <?php else: ?>
                                                        <a class="dropdown-item btn-add-note" data-action="nueva-nota" href="#" data-id="<?php echo $s->idsolicitud; ?>">Nueva Nota</a>
                                                    <?php endif; ?>
                                                    <div class="dropdown-divider"></div>
                                                    <?php if ($status === 'annulled'): ?>
                                                        <span class="dropdown-item text-muted disabled" data-action="garantia" style="pointer-events:none;">Garantia (bloqueado por anulación)</span>
                                                        <span class="dropdown-item text-muted disabled" data-action="pic" style="pointer-events:none;">PIC (bloqueado por anulación)</span>
                                                        <span class="dropdown-item text-muted disabled" data-action="fotos" style="pointer-events:none;">Fotos (bloqueado por anulación)</span>
                                                        <span class="dropdown-item text-muted disabled" data-action="documentos" style="pointer-events:none;">Documentos (bloqueado por anulación)</span>
                                                    <?php else: ?>
                                                        <a class="dropdown-item" data-action="garantia" href="<?php echo base_url('garantias/create/' . $s->idsolicitud); ?>">Garantia</a>
                                                        <a class="dropdown-item" data-action="pic" href="<?php echo base_url('perfil_integral/create/' . $s->idsolicitud); ?>">PIC</a>
                                                        <a class="dropdown-item" data-action="fotos" href="<?php echo base_url('solicitudes/photos/' . intval($s->idsolicitud)); ?>">Fotos</a>
                                                        <a class="dropdown-item" data-action="documentos" href="<?php echo base_url('solicitudes/documents/' . intval($s->idsolicitud)); ?>">Documentos</a>
                                                    <?php endif; ?>
                                                    <a class="dropdown-item" data-action="pdf" href="<?php echo base_url('solicitudes/download_solicitud_pdf_force/' . intval($s->idsolicitud)); ?>"><i class="fas fa-file-pdf"></i> PDF</a>
                                                    <div class="dropdown-divider"></div>
                                                    <?php if ($status === 'annulled'): ?>
                                                        <span class="dropdown-item text-muted disabled" data-action="uso" style="pointer-events:none;">Uso Credito (bloqueado por anulación)</span>
                                                        <span class="dropdown-item text-muted disabled" data-action="referencias" style="pointer-events:none;">Referencias (bloqueado por anulación)</span>
                                                    <?php else: ?>
                                                        <a class="dropdown-item btn-uso" data-action="uso" href="#" data-id="<?php echo $s->idsolicitud; ?>">Uso Credito</a>
                                                        <a class="dropdown-item btn-referencias" data-action="referencias" href="<?php echo base_url('solicitudes/referencias?idsolicitud=' . $s->idsolicitud); ?>" data-id="<?php echo $s->idsolicitud; ?>">Referencias</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay registros para mostrar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de notas -->
            <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Notas de la solicitud</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div id="notes-list" style="min-height:120px"></div>
                            <div class="form-group mt-3">
                                <label for="note_text">Agregar nota</label>
                                <textarea id="note_text" class="form-control" rows="3"></textarea>
                                <div id="note_error" class="text-danger mt-1" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" id="note_save" class="btn btn-primary">Guardar Nota</button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* Compact table styles for solicitudes list (adjusted for readability) */
                     /* Use automatic table layout so columns size to content and remain responsive.
                         Make the table fill the available container width without extra lateral margins. */
                     #solicitudes-table{ table-layout: auto; width:100%; max-width: 100%; margin: 0; box-sizing: border-box; }
                .table-compact td{
                    padding: .25rem .5rem;
                    vertical-align: middle;
                    font-size: .85rem;
                    line-height: 1.1;
                    /* allow wrapping in table cells at word boundaries only */
                    white-space: normal;
                    overflow-wrap: break-word;
                    word-break: normal;
                }
                .table-compact th{
                    padding: .25rem .5rem;
                    vertical-align: middle;
                    font-size: .85rem;
                    line-height: 1.1;
                    white-space: normal;
                    word-break: normal;
                    overflow-wrap: break-word;
                }
                .table-compact thead th{ font-size: .82rem; padding: .3rem .5rem; }
                /* Ensure buttons (especially action dropdown) remain readable */
                .table-compact .btn{
                    padding: .25rem .5rem;
                    font-size: .9rem;
                    min-width: 70px;
                }
                /* Specific rule to make the action dropdown more visible */
                #solicitudes-table td:nth-child(5) .btn{
                    font-size: .95rem;
                    padding: .22rem .35rem;
                    min-width: 36px;
                    max-width: 42px;
                }
                /* Constrain Cliente column to avoid extreme stretching but allow wrapping */
                #solicitudes-table td:nth-child(2){
                    max-width: 220px;
                    overflow-wrap: break-word;
                }

                /* Make first column (#) compact but flexible */
                #solicitudes-table th:first-child, #solicitudes-table td:first-child{
                    width:auto; min-width:40px; max-width:80px; text-align:center; padding-left:.4rem; padding-right:.4rem;
                    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                }
                /* Narrow action column (keep reasonable min width to avoid squashing content) */
                #solicitudes-table th:last-child,
                #solicitudes-table td:last-child{
                    width: 90px;
                    min-width: 70px;
                    white-space: nowrap;
                    vertical-align: middle;
                    text-align: center;
                }

                /* Ensure the responsive container and table respect parent margins */
                .table-responsive{ overflow-x:auto; max-width:100%; padding: 0; margin: 0; -webkit-overflow-scrolling: touch; }
                #solicitudes-table{ max-width:100%; box-sizing:border-box; display: table; }

                /* Responsive improvements for small screens (mobile) */
                @media (max-width: 767.98px) {
                    /* Allow table cells to wrap and avoid forced horizontal scroll */
                    .table-compact td, .table-compact th{
                        white-space: normal !important;
                        font-size: .82rem;
                    }
                    /* Make action buttons stack vertically and fill width for easier tapping */
                    #solicitudes-table td:nth-child(5) .btn{
                        display: block !important;
                        width: 100% !important;
                        text-align: left !important;
                        padding-left: .6rem !important;
                        padding-right: .6rem !important;
                        margin-bottom: .4rem !important;
                    }
                    /* Remove fixed narrow width on action column so it can grow vertically */
                    #solicitudes-table th:nth-child(5), #solicitudes-table td:nth-child(5){
                        width: auto !important;
                    }
                    /* Let Cliente column be full width and wrap */
                    #solicitudes-table th:nth-child(2), #solicitudes-table td:nth-child(2){
                        max-width: none !important;
                        overflow: visible !important;
                    }
                    /* Make top action (Nueva Solicitud) full width */
                    .breadcrumb-container .btn{ display: block; width: 100%; }
                    /* Improve modal spacing on small screens */
                    .modal-dialog { max-width: 95%; margin: 1.75rem auto; }
                    /* Avoid horizontal scroll for table container on mobile but allow dropdowns to overflow */
                    .table-responsive { overflow-x: auto; overflow: visible !important; }
                    /* Make sure dropdown menus inside compact tables can expand and are not clipped */
                    .table-compact .dropdown-menu{
                        min-width: 140px;
                        white-space: normal;
                        z-index: 2050;
                        max-height: none;
                        overflow: visible;
                    }
                    /* Ensure dropdown aligns to the right edge of the cell when using dropdown-menu-right */
                    .table-compact td .dropdown-menu.dropdown-menu-right{ right: 0; left: auto; }
                }
            </style>

            <style>
                /* Override: force actions column to fit and reduce button min-width to avoid overflow */
                #solicitudes-table th:last-child, #solicitudes-table td:last-child {
                    width: auto !important;
                    min-width: 42px !important;
                    max-width: 70px !important;
                    text-align: center !important;
                    overflow: hidden !important;
                    white-space: nowrap !important;
                    vertical-align: middle !important;
                    padding-right: .2rem !important;
                }

                .table-compact td .btn, .table-compact td .btn.btn-sm {
                    min-width: 40px !important;
                    padding: .22rem .35rem !important;
                }

                /* Ensure dropdown menu will not push table width (positioned over content) */
                .table-compact .dropdown-menu {
                    position: absolute !important;
                    left: auto !important;
                    right: 0 !important;
                    min-width: 110px !important;
                    z-index: 3000 !important;
                }
            </style>

            <style>
                /* Responsive adjustments: hide ID column and simplify actions on small screens */
                @media (max-width: 767.98px) {
                    /* Hide the first column (ID) to save horizontal space */
                    #solicitudes-table th:nth-child(1),
                    #solicitudes-table td:nth-child(1) {
                        display: none !important;
                    }

                    /* Reduce padding and font-size for compactness */
                    #solicitudes-table td, #solicitudes-table th {
                        padding: .2rem .4rem !important;
                        font-size: .78rem !important;
                    }

                    /* Make the actions button small and circular when collapsed */
                    .sol-acciones-collapsed {
                        width: 34px !important;
                        height: 34px !important;
                        padding: 0 !important;
                        border-radius: 6px !important;
                        text-align: center !important;
                        line-height: 34px !important;
                        font-weight: 700 !important;
                    }

                    /* Hide the dropdown caret for the tiny button to keep it clean */
                    .sol-acciones-collapsed .dropdown-toggle::after { display: none !important; }

                    /* Ensure dropdown menu still appears above content */
                    .table-compact .dropdown-menu{ z-index: 3000; }
                }
            </style>

            <script>
                // Initialize client-side filtering for the solicitudes list; wait for jQuery
                // If DataTables is present we skip the manual row show/hide and let DataTables handle search/pagination.
                (function waitForjQuery(){
                    if(window.jQuery){
                        (function($){
                            if($.fn && $.fn.DataTable){
                                // DataTables will handle filtering/pagination for this table
                                return;
                            }
                            function applySolFilters(){
                                var q = $('#sol_search').val().toLowerCase().trim();
                                var status = $('#sol_filter_status').val();
                                $('#solicitudes-table tbody tr').each(function(){
                                    var $tr = $(this);
                                    var id = ($tr.data('id') || '').toString();
                                    var client = $tr.find('td').eq(1).text().toLowerCase();
                                    var rowStatus = $tr.data('status') || 'pending';
                                    var matchesQuery = q === '' || id.indexOf(q) !== -1 || client.indexOf(q) !== -1;
                                    var matchesStatus = (status === 'all') || (status === rowStatus);
                                    if(matchesQuery && matchesStatus) $tr.show(); else $tr.hide();
                                });
                            }
                            $('#sol_search').on('input', applySolFilters);
                            $('#sol_filter_status').on('change', applySolFilters);
                            // apply initial filter state
                            setTimeout(applySolFilters, 200);
                        })(jQuery);
                    } else {
                        setTimeout(waitForjQuery, 100);
                    }
                })();
            </script>

            <script>
                // Initialize DataTables for solicitudes list when plugin is available.
                (function waitForDataTable(){
                    if(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable){
                        (function($){
                            try{
                                var table = $('#solicitudes-table').DataTable({
                                    "bSort": false,
                                    "responsive": true,
                                    "autoWidth": false,
                                    "pageLength": 10,
                                    "lengthMenu": [[10,25,50,100],[10,25,50,100]]
                                });

                                // Custom status filter: use DataTables ext.search
                                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
                                    if(!settings || !settings.nTable) return true;
                                    if(settings.nTable.id !== 'solicitudes-table') return true;
                                    var status = $('#sol_filter_status').val();
                                    if(!status || status === 'all') return true;
                                    var row = table.row(dataIndex).node();
                                    var rowStatus = $(row).data('status') || 'pending';
                                    return status === rowStatus;
                                });

                                // Bind search input to DataTables search
                                $('#sol_search').off('input.dt').on('input', function(){
                                    table.search(this.value).draw();
                                });

                                // Bind status select to redraw which will apply the ext.search above
                                $('#sol_filter_status').off('change.dt').on('change', function(){
                                    table.draw();
                                });
                            }catch(e){ console.error('DataTable init error', e); }
                        })(jQuery);
                    } else {
                        setTimeout(waitForDataTable, 100);
                    }
                })();
            </script>
            <script>
                (function waitForjQueryResponsive(){
                    if(window.jQuery){
                        (function($){
                            var resizeTimer = null;
                            function applyResponsiveActions(){
                                var w = $(window).width();
                                if (w <= 767) {
                                    // hide id column (already hidden by CSS) and collapse action buttons
                                    $('#solicitudes-table tbody tr').each(function(){
                                        var $btn = $(this).find('td').last().find('.btn-group > .btn').first();
                                        if ($btn.length && !$btn.data('orig-text')) {
                                            $btn.data('orig-text', $btn.text());
                                        }
                                        if ($btn.length) {
                                            $btn.addClass('sol-acciones-collapsed');
                                            $btn.text('+');
                                        }
                                    });
                                } else {
                                    $('#solicitudes-table tbody tr').each(function(){
                                        var $btn = $(this).find('td').last().find('.btn-group > .btn').first();
                                        if ($btn.length) {
                                            var orig = $btn.data('orig-text');
                                            if (orig !== undefined && orig !== null) $btn.text(orig);
                                            $btn.removeClass('sol-acciones-collapsed');
                                        }
                                    });
                                }
                            }
                            $(window).on('resize', function(){
                                clearTimeout(resizeTimer);
                                resizeTimer = setTimeout(applyResponsiveActions, 150);
                            });
                            // run on ready
                            $(function(){
                                // store original texts for buttons if not present
                                $('#solicitudes-table tbody tr').each(function(){
                                    var $btn = $(this).find('td').last().find('.btn-group > .btn').first();
                                    if ($btn.length && !$btn.data('orig-text')) $btn.data('orig-text', $btn.text());
                                });
                                applyResponsiveActions();
                            });
                        })(jQuery);
                    } else {
                        setTimeout(waitForjQueryResponsive, 100);
                    }
                })();
            </script>
            <script>
                // Ensure 'Referencias' dropdown item always navigates to the referencias page
                (function waitForjQueryRef(){
                    if(window.jQuery){
                        (function($){
                            $(document).on('click', 'a.dropdown-item[data-action="referencias"]', function(e){
                                var href = $(this).attr('href');
                                if(!href) return;
                                // allow control keys to open in new tab
                                if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                                e.preventDefault();
                                // Force a full navigation so the referencias view loads (uses its own modal/form)
                                window.location.href = href;
                            });
                        })(jQuery);
                    } else {
                        setTimeout(waitForjQueryRef, 100);
                    }
                })();
            </script>
            <script>
                // Notes: wrap logic in initNotes($) and ensure jQuery is available.
                // Accept jQuery as a parameter to avoid relying on global `$` when noConflict is used.
                function initNotes($){
                    var selId = null;
                    function renderComments(list){
                        var $c = $('#notes-list');
                        $c.empty();
                        if(!list || !list.length){
                            $c.append('<div class="text-muted">No hay comentarios todavía.</div>');
                            return;
                        }
                        list.forEach(function(cm){
                            var header = '<div class="d-flex justify-content-between align-items-center"><strong>' + (cm.username || 'Usuario') + '</strong>' + (cm.action ? ' <small class="text-muted">('+cm.action+')</small>' : '') + '<small class="text-muted"> ' + (cm.created_at ? cm.created_at : '') + '</small></div>';
                            var content = cm.note !== undefined ? cm.note : (cm.comment !== undefined ? cm.comment : '');
                            var body = '<div class="pt-1 pb-3">' + (content ? $('<div>').text(content).html().replace(/\n/g,'<br>') : '') + '</div>';
                            $c.append('<div class="border rounded px-2 py-2 mb-2">' + header + body + '</div>');
                        });
                    }

                    function renderSingle(cm){
                        var header = '<div class="d-flex justify-content-between align-items-center"><strong>' + (cm.username || 'Usuario') + '</strong>' + (cm.action ? ' <small class="text-muted">('+cm.action+')</small>' : '') + '<small class="text-muted"> ' + (cm.created_at ? cm.created_at : '') + '</small></div>';
                        var content = cm.note !== undefined ? cm.note : (cm.comment !== undefined ? cm.comment : '');
                        var body = '<div class="pt-1 pb-3">' + (content ? $('<div>').text(content).html().replace(/\n/g,'<br>') : '') + '</div>';
                        return '<div class="border rounded px-2 py-2 mb-2">' + header + body + '</div>';
                    }

                    function loadComments(id){
                        var url = '<?php echo base_url($this->router->fetch_class() . '/get_notes_ajax/'); ?>' + id;
                        $('#notes-list').html('<div class="text-center text-muted">Cargando comentarios...</div>');
                        $.getJSON(url).done(function(resp){
                            console.log('get_notes_ajax response:', resp);
                            if(resp && resp.status){
                                renderComments(resp.notes);
                            } else {
                                var msg = (resp && resp.message) ? resp.message : 'No se pudieron cargar las notas.';
                                $('#notes-list').html('<div class="text-danger">'+msg+'</div>');
                            }
                        }).fail(function(jqxhr, textStatus, error){
                            console.error('get_notes_ajax fail:', textStatus, error);
                            $('#notes-list').html('<div class="text-danger">Error al cargar las notas.</div>');
                        });
                    }

                    // Delegated handlers to support DataTables and dynamically added rows
                    $(document).on('click', '.btn-notes', function(e){
                        e.preventDefault();
                        var $btn = $(this);
                        selId = $btn.data('id');
                        console.log('Opening notes modal for id', selId);
                        $('#note_text').val('');
                        $('#note_error').hide().text('');
                        $('#notesModal').modal('show');
                        loadComments(selId);
                    });

                    $(document).on('click', '.btn-add-note', function(e){
                        e.preventDefault();
                        var $btn = $(this);
                        var id = $btn.data('id');
                        console.log('Inline add note clicked for id', id);
                        // if an inline form already exists for this id, focus textarea
                        var selector = 'tr.note-inline-row[data-id="' + id + '"]';
                        if($(selector).length){
                            $(selector).find('textarea').focus();
                            return;
                        }
                        // find the table row where the button lives
                        var $tr = $btn.closest('tr');
                        // build inline form row
                        var cols = $tr.children('td').length; // try to span all cols
                        var $row = $('<tr class="note-inline-row" data-id="'+id+'">\n' +
                            '<td colspan="'+cols+'">' +
                            '<div class="border p-2">' +
                            '<div class="form-group mb-2"><textarea class="form-control inline-note-text" rows="3" placeholder="Escribe la nota (min 3 caracteres)"></textarea><div class="text-danger mt-1 inline-note-error" style="display:none;"></div></div>' +
                            '<div><button class="btn btn-sm btn-primary inline-note-save">Guardar</button> <button class="btn btn-sm btn-secondary inline-note-cancel">Cancelar</button></div>' +
                            '</div>' +
                            '</td></tr>');
                        // insert after current row
                        $tr.after($row);
                        $row.find('textarea').focus();

                        // cancel handler
                        $row.on('click', '.inline-note-cancel', function(evt){
                            evt.preventDefault();
                            $row.remove();
                        });

                        // save handler
                        $row.on('click', '.inline-note-save', function(evt){
                            evt.preventDefault();
                            var txt = $row.find('.inline-note-text').val() || '';
                            txt = txt.trim();
                            var $err = $row.find('.inline-note-error');
                            if(txt.length < 3){
                                $err.show().text('Ingrese al menos 3 caracteres en la nota.');
                                return;
                            }
                            // disable buttons
                            $row.find('.inline-note-save').prop('disabled', true).text('Guardando...');
                            $.post('<?php echo base_url($this->router->fetch_class() . '/add_note_ajax'); ?>', {idsolicitud: id, comment: txt})
                                .done(function(resp){
                                    try{ var json = (typeof resp === 'object') ? resp : JSON.parse(resp); } catch(e){ json = {status:false}; }
                                    if(json && json.status){
                                            // show feedback: remove inline form
                                            $row.remove();
                                            // If modal is open for same id, prepend the returned note
                                            if(json.note && $('#notesModal').hasClass('show') && selId == id){
                                                var html = renderSingle(json.note);
                                                $('#notes-list').prepend(html);
                                            }
                                    } else {
                                        $err.show().text((json && json.message) ? json.message : 'No se pudo guardar la nota.');
                                        $row.find('.inline-note-save').prop('disabled', false).text('Guardar');
                                    }
                                }).fail(function(){
                                    $err.show().text('Error al intentar guardar la nota.');
                                    $row.find('.inline-note-save').prop('disabled', false).text('Guardar');
                                });
                        });
                    });

                    // Save note from modal
                    $(document).on('click', '#note_save', function(){
                        var txt = $('#note_text').val() || '';
                        txt = txt.trim();
                        if(txt.length < 3){
                            $('#note_error').show().text('Ingrese al menos 3 caracteres en la nota.');
                            return;
                        }
                        $('#note_save').prop('disabled', true).text('Guardando...');
                        $.post('<?php echo base_url($this->router->fetch_class() . '/add_note_ajax'); ?>', {idsolicitud: selId, comment: txt})
                            .done(function(resp){
                                try{ var json = (typeof resp === 'object') ? resp : JSON.parse(resp); } catch(e){ json = {status:false}; }
                                if(json && json.status){
                                    $('#note_text').val('');
                                    // If server returned the inserted note, prepend it to the list for immediate feedback
                                    if(json.note){
                                        var html = renderSingle(json.note);
                                        $('#notes-list').prepend(html);
                                    } else {
                                        // fallback: reload full list
                                        loadComments(selId);
                                    }
                                } else {
                                    alert((json && json.message) ? json.message : 'No se pudo guardar la nota.');
                                }
                            }).fail(function(){
                                alert('Error al intentar guardar la nota.');
                            }).always(function(){
                                $('#note_save').prop('disabled', false).text('Guardar Nota');
                            });
                    });
                }

                // Ensure we initialise notes handlers after the full page load so
                // that `layout/footer.php` has loaded jQuery and Bootstrap first.
                // This avoids dynamically loading a second jQuery instance and
                // modal plugin mismatches that prevented the modal from opening.
                window.addEventListener('load', function(){
                    function start($) {
                        try{
                            if(!$ || typeof $ !== 'function') { console.warn('notes: invalid jQuery instance, aborting init'); return; }
                            // wait until bootstrap modal plugin available (or timeout)
                            var startTs = Date.now();
                            var interval = setInterval(function(){
                                if($.fn && $.fn.modal){
                                    clearInterval(interval);
                                    console.log('notes: initializing (jQuery + bootstrap detected)');
                                    initNotes($);
                                    return;
                                }
                                if(Date.now() - startTs > 5000){
                                    clearInterval(interval);
                                    console.warn('notes: bootstrap modal plugin not detected after 5s — initializing handlers anyway');
                                    initNotes($);
                                }
                            }, 100);
                        } catch(err){ console.error('notes: start error', err); }
                    }

                    try{
                        if(typeof jQuery === 'undefined'){
                            console.warn('notes: jQuery not found on window after load — aborting init to avoid conflicts');
                            return;
                        } else {
                            // jQuery present — pass it explicitly
                            start(jQuery);
                        }
                    } catch(e){
                        console.error('notes: error initializing notes script after load', e);
                    }
                });
            </script>
            <?php $this->load->view('solicitudes/_uso_credito_modal'); ?>
            <!-- Modal para editar las dos referencias (inline en listado) -->
            <div class="modal fade" id="modalReferencias" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Verificación de Referencias - Solicitud <span id="modal-idsolicitud"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form id="formReferencias">
                                <input type="hidden" name="idsolicitud" id="idsolicitud">

                                <?php for ($i = 1; $i <= 2; $i++): ?>
                                <fieldset style="border:1px solid #ddd;padding:10px;margin-bottom:10px;">
                                    <legend>Referencia #<?php echo $i; ?></legend>
                                    <div class="form-group">
                                        <label>Nombre completo</label>
                                        <input type="text" class="form-control" name="nombre_<?php echo $i; ?>" id="nombre_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Cédula / Identificación</label>
                                        <input type="text" class="form-control" name="cedula_<?php echo $i; ?>" id="cedula_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <textarea class="form-control" name="direccion_<?php echo $i; ?>" id="direccion_<?php echo $i; ?>" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" name="telefono_<?php echo $i; ?>" id="telefono_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo de referencia</label>
                                        <input type="text" class="form-control" name="tipo_<?php echo $i; ?>" id="tipo_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>¿Desde cuándo conoce al cliente?</label>
                                        <input type="text" class="form-control" name="desde_<?php echo $i; ?>" id="desde_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>¿Ha tenido relación económica con el cliente? (Si/No)</label>
                                        <select class="form-control" name="relacion_<?php echo $i; ?>" id="relacion_<?php echo $i; ?>">
                                            <option value="">-- Seleccionar --</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Opinión sobre el cliente</label>
                                        <select class="form-control" name="opinion_<?php echo $i; ?>" id="opinion_<?php echo $i; ?>">
                                            <option value="">-- Seleccionar --</option>
                                            <option value="Excelente">Excelente</option>
                                            <option value="Buena">Buena</option>
                                            <option value="Mala">Mala</option>
                                            <option value="No opina">No opina</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Comentarios adicionales</label>
                                        <textarea class="form-control" name="comentarios_<?php echo $i; ?>" id="comentarios_<?php echo $i; ?>" rows="2"></textarea>
                                    </div>
                                </fieldset>
                                <?php endfor; ?>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="btn-save-referencias">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
    <style>
    /* Action completed styles */
    .sol-row-completed { border-left: 4px solid #28a745; background-color: rgba(40,167,69,0.03); }
    .dropdown-item.action-completed { background-color: #e8f6ea; color: #155724; }
    .action-completed::after { content: " \2713"; color: #1e7e34; font-weight: 700; margin-left: 6px; }
    </style>

    <script>
    ;(function(){
        function key(id, action){ return 'sol:'+id+':'+action; }
        function markActionDone(id, action){
            try{ localStorage.setItem(key(id,action),'1'); }catch(e){ }
            applyMark(id, action);
        }
        function isActionDone(id, action){
            try{ return localStorage.getItem(key(id,action)) === '1'; }catch(e){ return false; }
        }
        function applyMark(id, action){
            // mark dropdown item(s)
            var selector = '[data-id="'+id+'"][data-action="'+action+'"]';
            var $items = $(selector);
            $items.addClass('action-completed');

            // mark parent row (if any)
            var $tr = $items.closest('tr');
            if(!$tr.length){
                // fallback: try any element that has data-id
                $tr = $('[data-id="'+id+'"]').closest('tr');
            }
            if($tr.length){
                $tr.addClass('sol-row-completed');
            }
        }

        // restore marks on load
        $(function(){
            // iterate known action items and apply stored marks
            $('.dropdown-item[data-id][data-action]').each(function(){
                var $it = $(this);
                var id = $it.attr('data-id');
                var action = $it.attr('data-action');
                if(isActionDone(id, action)){
                    applyMark(id, action);
                }
            });

            // clicking an action marks it done (useful for actions that open forms/pages)
            $(document).on('click', '.dropdown-item[data-id][data-action]', function(e){
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action') || 'click';
                // mark on click; for AJAX-backed saves the server flow may also re-mark on success
                markActionDone(id, action);
            });

            // convenience: also mark common buttons that might exist elsewhere
            $(document).on('click', '.btn-add-note, .btn-notes, .btn-uso, .btn-referencias', function(e){
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action') || $(this).hasClass('btn-referencias') ? 'referencias' : 'notas';
                if(id) markActionDone(id, action);
            });
        });

        // expose helper for other scripts (e.g., when AJAX save succeeds you can call this)
        window.SolicitudActions = {
            markDone: markActionDone,
            isDone: isActionDone
        };
    })();
    </script>

            <script>
            (function(){
                // Espera y registra handlers cuando jQuery esté disponible.
                function initRef($){
                    function resetFields(){
                        for(var i=1;i<=2;i++){
                            $('#nombre_'+i).val('');
                            $('#cedula_'+i).val('');
                            $('#direccion_'+i).val('');
                            $('#telefono_'+i).val('');
                            $('#tipo_'+i).val('');
                            $('#desde_'+i).val('');
                            $('#relacion_'+i).val('');
                            $('#opinion_'+i).val('');
                            $('#comentarios_'+i).val('');
                        }
                    }

                    $(document).on('click', '.btn-referencias', function(e){
                        e.preventDefault();
                        var id = $(this).data('id');
                        $('#modal-idsolicitud').text(id);
                        $('#idsolicitud').val(id);
                        resetFields();
                        $.getJSON('<?php echo base_url('solicitudes/get_referencias_ajax/'); ?>' + id, function(json){
                            if(json && json.status && Array.isArray(json.referencias)){
                                json.referencias.forEach(function(r){
                                    var n = r.referencia_num || 1; if(n<1) n=1; if(n>2) n=2;
                                    $('#nombre_'+n).val(r.nombre || '');
                                    $('#cedula_'+n).val(r.cedula || '');
                                    $('#direccion_'+n).val(r.direccion || '');
                                    $('#telefono_'+n).val(r.telefono || '');
                                    $('#tipo_'+n).val(r.tipo_referencia || '');
                                    $('#desde_'+n).val(r.desde_conoce_cliente || '');
                                    $('#relacion_'+n).val((r.relacion_economica === null || r.relacion_economica === undefined) ? '' : (r.relacion_economica ? '1' : '0'));
                                    $('#opinion_'+n).val(r.opinion || '');
                                    $('#comentarios_'+n).val(r.comentarios || '');
                                });
                            }
                            $('#modalReferencias').modal('show');
                        }).fail(function(){ $('#modalReferencias').modal('show'); });
                    });

                    $(document).on('click', '#btn-save-referencias', function(e){
                        e.preventDefault();
                        var data = $('#formReferencias').serialize();
                        $.post('<?php echo base_url('solicitudes/save_referencias_ajax'); ?>', data, function(json){
                            if(json && json.status){
                                $('#modalReferencias').modal('hide');
                                $('.alert').remove();
                                $('.container-fluid').prepend('<div class="alert alert-success">Referencias guardadas.</div>');
                            } else {
                                alert('Error al guardar referencias: ' + (json && json.message ? json.message : ''));
                            }
                        }, 'json').fail(function(){ alert('Error de red al guardar referencias'); });
                    });
                }

                function waitForjQueryRef(tries){
                    if(window.jQuery){ initRef(window.jQuery); return; }
                    if(tries <= 0) return; 
                    setTimeout(function(){ waitForjQueryRef(tries - 1); }, 100);
                }

                waitForjQueryRef(50); // espera hasta ~5s
            })();
            </script>

            <style>
                /* Allow dropdown menus in the solicitudes table to display above its responsive wrapper */
                .card-body > .table-responsive { overflow: visible !important; }
                #solicitudes-table td:last-child,
                #solicitudes-table th:last-child {
                    overflow: visible !important;
                }
                #solicitudes-table .btn-group {
                    position: relative;
                    overflow: visible !important;
                }
                #solicitudes-table .dropdown-menu {
                    z-index: 12000 !important;
                }
            </style>

            <script>
                // Center the dropdown arrow (--dropdown-arrow-left) over the toggle button
                window.addEventListener('load', function(){
                    function initCentering(){
                        if(typeof jQuery === 'undefined' || !jQuery.fn || !jQuery.fn.dropdown) return;
                        var $ = jQuery;

                        $(document).on('shown.bs.dropdown', '#solicitudes-table .btn-group', function(){
                            try{
                                var $group = $(this);
                                var $btn = $group.find('.dropdown-toggle').first();
                                var $menu = $group.find('.dropdown-menu').first();
                                if(!$btn.length || !$menu.length) return;
                                var btnRect = $btn[0].getBoundingClientRect();
                                var menuRect = $menu[0].getBoundingClientRect();
                                // center point of button relative to menu left
                                var desired = (btnRect.left + (btnRect.width/2)) - menuRect.left;
                                // clamp to keep arrow inside menu (12px padding from edges)
                                var clamp = function(v, a, b){ return Math.max(a, Math.min(v, b)); };
                                desired = clamp(desired, 12, Math.max(12, menuRect.width - 12));
                                $menu[0].style.setProperty('--dropdown-arrow-left', desired + 'px');
                                // Small vertical nudge to bring the menu closer to the toggle button.
                                // We adjust the popper-applied transform Y value by a few pixels toward the button.
                                try{
                                    var placement = $menu.attr('x-placement') || $menu[0].getAttribute('x-placement') || '';
                                    var comp = window.getComputedStyle($menu[0]);
                                    var t = comp.transform || $menu[0].style.transform || '';
                                    var parse = function(str){
                                        var out = {x:0,y:0,z:0};
                                        if(!str || str === 'none') return out;
                                        // translate3d(...) case
                                        var m = /translate3d\(([-0-9.]+)px,\s*([-0-9.]+)px,\s*([-0-9.]+)px\)/.exec(str);
                                        if(m){ out.x = parseFloat(m[1]); out.y = parseFloat(m[2]); out.z = parseFloat(m[3]); return out; }
                                        // matrix(a,b,c,d,tx,ty)
                                        m = /matrix\(([^)]+)\)/.exec(str);
                                        if(m){ var parts = m[1].split(',').map(function(s){return parseFloat(s.trim());}); if(parts.length>=6){ out.x = parts[4]; out.y = parts[5]; out.z = 0; } return out; }
                                        // matrix3d(..., tx, ty, tz)
                                        m = /matrix3d\(([^)]+)\)/.exec(str);
                                        if(m){ var parts = m[1].split(',').map(function(s){return parseFloat(s.trim());}); if(parts.length>=14){ out.x = parts[12]; out.y = parts[13]; out.z = parts[14]||0; } return out; }
                                        return out;
                                    };
                                    var vals = parse(t);
                                    var delta = 8; // pixels to move menu closer
                                    var newY = vals.y;
                                    if(placement.indexOf('top') === 0){
                                        // menu above button: y is negative; increase toward zero
                                        newY = vals.y + delta;
                                    } else if(placement.indexOf('bottom') === 0){
                                        // menu below button: y is positive; decrease toward zero
                                        newY = vals.y - delta;
                                    }
                                    // write back transform preserving X/Z
                                    $menu[0].style.transform = 'translate3d(' + (vals.x||0) + 'px, ' + (newY||0) + 'px, ' + (vals.z||0) + 'px)';
                                }catch(e){ console.warn('nudge error', e); }
                            }catch(err){ console.warn('arrow-centering error', err); }
                        });

                        $(document).on('hidden.bs.dropdown', '#solicitudes-table .btn-group', function(){
                            try{
                                var $menu = $(this).find('.dropdown-menu').first();
                                if($menu && $menu.length) $menu[0].style.removeProperty('--dropdown-arrow-left');
                            }catch(e){ }
                        });
                    }

                    // attempt init (in case jQuery already loaded) and also try later
                    var tries = 0;
                    function tryInit(){ if(typeof jQuery !== 'undefined'){ initCentering(); } else if(tries < 50){ tries++; setTimeout(tryInit, 100); } }
                    tryInit();
                });
            </script>

            <style>
                /* Make the dropdown arrow point toward the source button when menu opens above it */
                /* For top-placed menus, flip the arrow to the bottom edge and align it to the button */
                .table-compact .dropdown-menu[x-placement^="top"]::after {
                    top: 100% !important;
                    bottom: auto !important;
                    /* switch arrow direction */
                    border-bottom-color: transparent !important;
                    border-top-color: #ffffff !important;
                    /* position horizontally using a runtime-updated CSS variable so the arrow
                       can be centered exactly on the source button regardless of table layout */
                    left: var(--dropdown-arrow-left, 12px) !important;
                    right: auto !important;
                    transform: translateX(-50%) !important;
                }

                /* If desired, remove the decorative triangle entirely for the solicitudes table */
                .table-compact .dropdown-menu::before,
                .table-compact .dropdown-menu::after {
                    display: none !important;
                    content: none !important;
                    border: 0 !important;
                    box-shadow: none !important;
                }
            </style>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                    <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
                </div>
            </footer>

        </div>
    </div>
</div>
