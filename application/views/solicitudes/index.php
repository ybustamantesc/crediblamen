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
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">Acciones</button>
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
                                                    <?php else: ?>
                                                        <a class="dropdown-item" data-action="garantia" href="<?php echo base_url('garantias/create/' . $s->idsolicitud); ?>">Garantia</a>
                                                        <a class="dropdown-item" data-action="pic" href="<?php echo base_url('perfil_integral/create/' . $s->idsolicitud); ?>">PIC</a>
                                                        <a class="dropdown-item" data-action="fotos" href="<?php echo base_url('solicitudes/photos/' . intval($s->idsolicitud)); ?>">Fotos</a>
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
                .table-compact td, .table-compact th{
                    padding: .25rem .5rem;
                    vertical-align: middle;
                    font-size: .85rem;
                    line-height: 1.1;
                    white-space: nowrap;
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
                    font-size: .95rem; /* slightly larger for clarity */
                    padding: .28rem .6rem;
                    min-width: 100px;
                }
                /* Truncate Cliente column to prevent layout stretching */
                #solicitudes-table th:nth-child(2),
                #solicitudes-table td:nth-child(2){
                    max-width: 180px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                /* Narrow action column (keep reasonable min width to avoid squashing content) */
                #solicitudes-table th:nth-child(5),
                #solicitudes-table td:nth-child(5){
                    width: 140px;
                    white-space: nowrap;
                    vertical-align: middle;
                }

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

            <script>
                // Initialize client-side filtering for the solicitudes list; wait for jQuery
                (function waitForjQuery(){
                    if(window.jQuery){
                        (function($){
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
            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                    <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
                </div>
            </footer>

        </div>
    </div>
</div>
