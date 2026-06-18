<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-user-friends'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo isset($titulo) ? $titulo : 'Formato de Verificación de Referencias - Solicitud de Crédito'; ?> </h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : 'Complete las dos referencias personales por cada solicitud.'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="ref_search" class="form-control" placeholder="Buscar por código o cliente..." />
                        </div>
                        <div class="col-md-3">
                            <select id="ref_filter_status" class="form-control">
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
                        @media (max-width: 767.98px) {
                            #referencias-table th:nth-child(1),
                            #referencias-table td:nth-child(1),
                            #referencias-table th:nth-child(4),
                            #referencias-table td:nth-child(4) {
                                display: none;
                            }
                            #referencias-table th:last-child,
                            #referencias-table td:last-child {
                                min-width: 0;
                                white-space: normal;
                            }
                        }
                    </style>
                    <div id="referencias-table-wrap" class="table-responsive d-block">
                        <table id="referencias-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Código</th>
                                    <th>Destino Conami</th>
                                    <th>Creado por</th>
                                    <th class="d-none">Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($solicitudes)): foreach($solicitudes as $s): ?>
                                    <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                    <?php $rowClass = ''; if(isset($s->aprob_status)){ if($s->aprob_status === 'approved') $rowClass = 'table-success'; elseif($s->aprob_status === 'rejected') $rowClass = 'table-danger'; elseif($s->aprob_status === 'annulled') $rowClass = 'table-secondary'; }
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>" data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <td><?php echo $s->idsolicitud; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(trim($s->nombres . ' ' . $s->apellidos)); ?>
                                            <?php if ($status === 'annulled'): ?>
                                                <span class="badge badge-secondary ml-1">Anulado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($s->rubro_credito ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($s->nombre_asesor ?? ''); ?></td>
                                        <td class="d-none"><?php echo isset($s->fecha_solicitud) ? $s->fecha_solicitud : ''; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary btn-referencias" data-id="<?php echo $s->idsolicitud; ?>">
                                                    <span class="d-none d-md-inline">Completar Referencias</span>
                                                    <span class="d-inline d-md-none">Ver</span>
                                                </button>
                                                <a class="btn btn-sm btn-secondary" href="<?php echo base_url('solicitudes/download_referencias/' . $s->idsolicitud); ?>" target="_blank">
                                                    <span class="d-none d-md-inline">Descargar PDF</span>
                                                    <span class="d-inline d-md-none">PDF</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="7" class="text-center">No hay solicitudes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div id="referencias-cards-wrap" class="d-block d-md-none">
                        <div class="row">
                            <?php if(!empty($solicitudes)): foreach($solicitudes as $s): ?>
                                <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                <?php $cardClass = ''; if(isset($s->aprob_status)){ if($s->aprob_status === 'approved') $cardClass = 'border-success'; elseif($s->aprob_status === 'rejected') $cardClass = 'border-danger'; elseif($s->aprob_status === 'annulled') $cardClass = 'border-secondary'; }
                                ?>
                                <div class="col-12">
                                    <div class="card mb-2 <?php echo $cardClass; ?>" data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="font-weight-bold"><?php echo htmlspecialchars(trim($s->nombres . ' ' . $s->apellidos)); ?></div>
                                                    <div class="text-muted small"><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-primary btn-referencias" data-id="<?php echo $s->idsolicitud; ?>">Ver</button>
                                                        <a class="btn btn-sm btn-secondary" href="<?php echo base_url('solicitudes/download_referencias/' . $s->idsolicitud); ?>" target="_blank">PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="col-12"><div class="text-center text-muted">No hay solicitudes.</div></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function waitForReferenciasDataTable(){
                    if(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable){
                        (function($){
                            try{
                                var table = $('#referencias-table').DataTable({
                                    "bSort": false,
                                    "responsive": true,
                                    "autoWidth": false,
                                    "pageLength": 10,
                                    "lengthMenu": [[10,25,50,100],[10,25,50,100]]
                                });
                                var wrapper = $(table.table().container());
                                wrapper.find('.dataTables_filter').hide();
                                $('#ref_search').off('input.dt').on('input', function(){ table.search(this.value).draw(); });
                                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
                                    if (!settings || !settings.nTable || settings.nTable.id !== 'referencias-table') return true;
                                    var status = $('#ref_filter_status').val();
                                    if (!status || status === 'all') return true;
                                    var row = table.row(dataIndex).node();
                                    return ($(row).data('status') || 'pending') === status;
                                });
                                $('#ref_filter_status').off('change.dt').on('change', function(){ table.draw(); });
                            }catch(e){ console.error('Referencias DataTable error', e); }
                        })(jQuery);
                    } else { setTimeout(waitForReferenciasDataTable, 100); }
                })();
            </script>

            <!-- Modal para editar las dos referencias -->
            <div class="modal fade" id="modalReferencias" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Verificación de Referencias - Solicitud <span id="modal-idsolicitud"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form id="formReferencias" enctype="multipart/form-data">
                                <script>
                                // Helper: disables all editable fields and hides save button if readonly
                                function setReferenciasReadonly(isReadonly){
                                    for(var i=1;i<=2;i++){
                                        var ids = [
                                            'nombre_'+i,'cedula_'+i,'direccion_'+i,'telefono_'+i,'tipo_'+i,'tipo_personal_relacion_'+i,'desde_'+i,'relacion_'+i,'opinion_'+i,'comentarios_'+i,'cedula_front_'+i,'cedula_back_'+i
                                        ];
                                        ids.forEach(function(id){
                                            var el = document.getElementById(id);
                                            if(el){
                                                el.readOnly = isReadonly;
                                                el.disabled = isReadonly;
                                            }
                                        });
                                        var pf = document.getElementById('preview_front_'+i);
                                        var pb = document.getElementById('preview_back_'+i);
                                        if(pf) pf.style.pointerEvents = isReadonly ? 'none' : '';
                                        if(pb) pb.style.pointerEvents = isReadonly ? 'none' : '';
                                    }
                                    var btn = document.getElementById('btn-save-referencias');
                                    if(btn){ btn.style.display = isReadonly ? 'none' : ''; }
                                }
                                </script>
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
                                        <input type="tel" inputmode="tel" pattern="^\+?[0-9]*$" title="Solo números y +" class="form-control phone-input" name="telefono_<?php echo $i; ?>" id="telefono_<?php echo $i; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo de referencia</label>
                                        <select class="form-control tipo-ref-select" name="tipo_<?php echo $i; ?>" id="tipo_<?php echo $i; ?>">
                                            <option value="">-- Seleccionar --</option>
                                            <option value="Personal">Personal</option>
                                            <option value="Comercial">Comercial</option>
                                        </select>
                                    </div>
                                    <div class="form-group tipo-personal-rel" id="tipo_personal_group_<?php echo $i; ?>" style="display:none;">
                                        <label>Si es Personal, indicar relación</label>
                                        <select class="form-control" name="tipo_personal_relacion_<?php echo $i; ?>" id="tipo_personal_relacion_<?php echo $i; ?>">
                                            <option value="">-- Seleccionar --</option>
                                            <option value="Vecino">Vecino</option>
                                            <option value="Compañero de Trabajo">Compañero de Trabajo</option>
                                            <option value="Amigo">Amigo</option>
                                        </select>
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
                                    <div class="form-group">
                                        <label>Foto Cédula (frontal)</label>
                                        <input type="file" accept="image/*" class="form-control-file cedula-input" name="cedula_front_<?php echo $i; ?>" id="cedula_front_<?php echo $i; ?>">
                                        <small class="form-text text-muted">Subir foto frontal de la cédula (opcional).</small>
                                        <div class="cedula-preview" id="preview_front_<?php echo $i; ?>" style="margin-top:6px;"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Foto Cédula (trasera)</label>
                                        <input type="file" accept="image/*" class="form-control-file cedula-input" name="cedula_back_<?php echo $i; ?>" id="cedula_back_<?php echo $i; ?>">
                                        <small class="form-text text-muted">Subir foto trasera de la cédula (opcional).</small>
                                        <div class="cedula-preview" id="preview_back_<?php echo $i; ?>" style="margin-top:6px;"></div>
                                    </div>
                                </fieldset>
                                <?php endfor; ?>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <a id="download_referencias_btn" class="btn btn-info" target="_blank" href="#" style="display:none;margin-right:6px;">Descargar PDF</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="btn-save-referencias">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                #referencias-table{ table-layout: auto; width:100%; max-width: 100%; margin: 0; box-sizing: border-box; }
                #referencias-table{ max-width:100%; box-sizing:border-box; display: table; }
                #referencias-table th:first-child, #referencias-table td:first-child{
                    width:auto; min-width:40px; max-width:80px; text-align:center;
                    padding-left:.4rem; padding-right:.4rem;
                    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                }
                #referencias-table th:last-child, #referencias-table td:last-child{
                    width: 90px; min-width:70px; white-space: nowrap;
                    vertical-align: middle; text-align:center;
                }
                /* Match compact table styles used by solicitudes list so dimensions align */
                #referencias-table.table-compact td, #referencias-table.table-compact th{
                    padding: .12rem .28rem;
                    vertical-align: middle;
                    font-size: .72rem;
                    line-height: 1.02;
                    white-space: normal;
                    overflow-wrap: break-word;
                    word-break: normal;
                }
                #referencias-table.table-compact thead th{ font-size: .7rem; padding: .18rem .28rem; }
                #referencias-table.table-compact .btn{ padding: .12rem .28rem; font-size: .72rem; min-width: auto; }
                /* Keep Cliente column from stretching too far while allowing wrapping */
                #referencias-table th:nth-child(2), #referencias-table td:nth-child(2){
                    max-width: 180px;
                    overflow-wrap: break-word;
                    word-break: normal;
                }
                /* Narrow action column */
                #referencias-table th:last-child, #referencias-table td:last-child{
                    width: 120px;
                    white-space: nowrap;
                }
                #referencias-table td:last-child .btn-group { white-space: nowrap; }
                #referencias-table td:last-child .btn { padding: .22rem .35rem; font-size: .78rem; min-width: auto; }
                /* Ensure only one of table or cards is visible (avoid conflicts from other CSS) */
                #referencias-table-wrap { display: block; }
                #referencias-cards-wrap { display: none; }
                @media (max-width: 767.98px) {
                    /* Keep table visible on small screens so DataTables pagination works and hide cards */
                    #referencias-table-wrap { display: block !important; }
                    #referencias-cards-wrap { display: none !important; }
                    #referencias-table td:last-child .btn { padding: .18rem .28rem; font-size: .72rem; }
                }
                @media (min-width: 768px) {
                    #referencias-cards-wrap { display: none !important; }
                }
            </style>

            <script>
                // Initialize client-side filtering for referencias list; wait for jQuery
                (function waitForjQuery(){
                    if(window.jQuery){
                        (function($){
                            function applyRefFilters(){
                                var q = $('#ref_search').val().toLowerCase().trim();
                                var status = $('#ref_filter_status').val();
                                $('#referencias-table tbody tr').each(function(){
                                    var $tr = $(this);
                                    var id = ($tr.data('id') || '').toString();
                                    var client = $tr.find('td').eq(1).text().toLowerCase();
                                    var code = $tr.find('td').eq(2).text().toLowerCase();
                                    var rowStatus = $tr.data('status') || 'pending';
                                    var matchesQuery = q === '' || id.indexOf(q) !== -1 || client.indexOf(q) !== -1 || code.indexOf(q) !== -1;
                                    var matchesStatus = (status === 'all') || (status === rowStatus);
                                    if(matchesQuery && matchesStatus) $tr.show(); else $tr.hide();
                                });
                            }
                            $('#ref_search').on('input', applyRefFilters);
                            $('#ref_filter_status').on('change', applyRefFilters);
                            // apply initial filter state
                            setTimeout(applyRefFilters, 200);
                        })(jQuery);
                    } else {
                        setTimeout(waitForjQuery, 100);
                    }
                })();
            </script>
            <script>
            (function(){
                // Espera y registra handlers cuando jQuery esté disponible.
                function init($){
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
                            var aprob = (json && json.solicitud && json.solicitud.aprob_status) ? json.solicitud.aprob_status : 'pending';
                            var isReadonly = (aprob === 'approved' || aprob === 'rejected' || aprob === 'annulled');
                            setReferenciasReadonly(isReadonly);
                            if(json && json.status && Array.isArray(json.referencias)){
                                json.referencias.forEach(function(r){
                                    var n = r.referencia_num || 1; if(n<1) n=1; if(n>2) n=2;
                                    $('#nombre_'+n).val(r.nombre || '');
                                    $('#cedula_'+n).val(r.cedula || '');
                                    $('#direccion_'+n).val(r.direccion || '');
                                    $('#telefono_'+n).val(r.telefono || '');
                                    $('#tipo_'+n).val(r.tipo_referencia || '');
                                        try { $('#tipo_personal_relacion_'+n).val(r.tipo_personal_relacion || ''); } catch(e) {}
                                    $('#desde_'+n).val(r.desde_conoce_cliente || '');
                                    $('#relacion_'+n).val((r.relacion_economica === null || r.relacion_economica === undefined) ? '' : (r.relacion_economica ? '1' : '0'));
                                    $('#opinion_'+n).val(r.opinion || '');
                                    $('#comentarios_'+n).val(r.comentarios || '');
                                    try {
                                        if (r.photos) {
                                            if (r.photos.front && r.photos.front.url) {
                                                var $pf = $('#preview_front_' + n);
                                                $pf.html('<div style="display:inline-block;margin-right:6px;"><img src="'+r.photos.front.url+'" style="max-width:120px;max-height:90px;border:1px solid #ddd;padding:2px;display:block;"/><a href="#" class="btn btn-sm btn-link btn-delete-foto" data-id="'+r.photos.front.id+'" data-referencia="'+n+'" data-tipo="front">Eliminar</a></div>');
                                            }
                                            if (r.photos.back && r.photos.back.url) {
                                                var $pb = $('#preview_back_' + n);
                                                $pb.html('<div style="display:inline-block;margin-right:6px;"><img src="'+r.photos.back.url+'" style="max-width:120px;max-height:90px;border:1px solid #ddd;padding:2px;display:block;"/><a href="#" class="btn btn-sm btn-link btn-delete-foto" data-id="'+r.photos.back.id+'" data-referencia="'+n+'" data-tipo="back">Eliminar</a></div>');
                                            }
                                        }
                                    } catch(e) { console.warn('preview error', e); }
                                });
                            }
                            $('#download_referencias_btn').attr('href', '<?php echo base_url('solicitudes/download_referencias/'); ?>' + id).show();
                            $('#modalReferencias').modal('show');
                        }).fail(function(){ $('#modalReferencias').modal('show'); });
                    });

                    // Preview selected cedula images and prepare FormData when saving
                    $(document).on('change', '.cedula-input', function(e){
                        var $input = $(this);
                        var id = $input.attr('id');
                        var previewId = 'preview_' + id.replace('cedula_', '');
                        var file = this.files && this.files[0];
                        var $preview = $('#' + previewId.replace('_', '_'));
                        // show small thumbnail or filename
                        if (file) {
                            var reader = new FileReader();
                            reader.onload = function(ev){
                                var img = $('<img/>').attr('src', ev.target.result).css({'max-width':'120px','max-height':'90px','margin-right':'6px','border':'1px solid #ddd','padding':'2px'});
                                $preview.html(img);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            $preview.empty();
                        }
                    });

                    // Permit only digits and + in phone inputs
                    $(document).on('input', '.phone-input', function(){
                        var cleaned = this.value.replace(/[^0-9+]/g, '');
                        if (cleaned !== this.value) {
                            this.value = cleaned;
                        }
                    });

                    // Show/hide personal relation select depending on tipo selection
                    $(document).on('change', '.tipo-ref-select', function(){
                        var id = $(this).attr('id');
                        var idx = id.split('_')[1];
                        var val = $(this).val();
                        if (val === 'Personal') {
                            $('#tipo_personal_group_' + idx).show();
                        } else {
                            $('#tipo_personal_group_' + idx).hide();
                            $('#tipo_personal_relacion_' + idx).val('');
                        }
                    });

                    // Ensure when modal opens the tipo-personal controls are visible when needed
                    $(document).on('show.bs.modal', '#modalReferencias', function(){
                        for(var i=1;i<=2;i++){
                            var v = $('#tipo_' + i).val();
                            if(v === 'Personal') $('#tipo_personal_group_' + i).show(); else $('#tipo_personal_group_' + i).hide();
                        }
                    });

                    // Delete previously uploaded photo
                    $(document).on('click', '.btn-delete-foto', function(e){
                        e.preventDefault();
                        if (!confirm('¿Eliminar foto? Esta acción no se puede deshacer.')) return;
                        var idfoto = $(this).data('id');
                        var tipo = $(this).data('tipo');
                        var referencia = $(this).data('referencia');
                        $.post('<?php echo base_url('solicitudes/delete_referencia_foto_ajax'); ?>', { idfoto: idfoto }, function(json){
                            if (json && json.status) {
                                // remove preview
                                if (tipo === 'front') $('#preview_front_' + referencia).empty();
                                if (tipo === 'back') $('#preview_back_' + referencia).empty();
                                $('.alert').remove();
                                $('.container-fluid').prepend('<div class="alert alert-success">Foto eliminada.</div>');
                            } else {
                                alert('Error al eliminar foto: ' + (json && json.message ? json.message : '')); 
                            }
                        }, 'json').fail(function(){ alert('Error de red al eliminar foto'); });
                    });

                    $(document).on('click', '#btn-save-referencias', function(e){
                        e.preventDefault();
                        var form = document.getElementById('formReferencias');
                        var useFormData = false;
                        // detect files
                        $('.cedula-input').each(function(){ if (this.files && this.files.length) useFormData = true; });

                        if (useFormData && window.FormData) {
                            var fd = new FormData(form);
                            // append files (FormData will include file inputs by name automatically)
                            $.ajax({
                                url: '<?php echo base_url('solicitudes/save_referencias_ajax'); ?>',
                                type: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function(json){
                                    if (json && json.status) {
                                        $('#modalReferencias').modal('hide');
                                        $('.alert').remove();
                                        $('.container-fluid').prepend('<div class="alert alert-success">Referencias guardadas.</div>');
                                    } else {
                                        alert('Error al guardar referencias: ' + (json && json.message ? json.message : ''));
                                    }
                                },
                                error: function(){ alert('Error de red al guardar referencias'); }
                            });
                        } else {
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
                        }
                    });

                    // Auto-open modal if `idsolicitud` is present in query string
                    try {
                        var _params = new URLSearchParams(window.location.search);
                        var _qid = _params.get('idsolicitud') || _params.get('id');
                        if (_qid) {
                                // trigger the same click flow as when user presses the button (if exists)
                                var $btn = $('.btn-referencias[data-id="' + _qid + '"]');
                                if ($btn.length) {
                                    $btn.trigger('click');
                                } else {
                                    // fallback to manual load
                                    $('#modal-idsolicitud').text(_qid);
                                    $('#idsolicitud').val(_qid);
                                    resetFields();
                                    $.getJSON('<?php echo base_url('solicitudes/get_referencias_ajax/'); ?>' + _qid, function(json){
                                        if(json && json.status && Array.isArray(json.referencias)){
                                            json.referencias.forEach(function(r){
                                                var n = r.referencia_num || 1; if(n<1) n=1; if(n>2) n=2;
                                                $('#nombre_'+n).val(r.nombre || '');
                                                $('#cedula_'+n).val(r.cedula || '');
                                                $('#direccion_'+n).val(r.direccion || '');
                                                $('#telefono_'+n).val(r.telefono || '');
                                                $('#tipo_'+n).val(r.tipo_referencia || '');
                                                $('#desde_'+n).val(r.desde_conoce_cliente || '');
                                                try { $('#tipo_personal_relacion_'+n).val(r.tipo_personal_relacion || ''); } catch(e) {}
                                                $('#relacion_'+n).val((r.relacion_economica === null || r.relacion_economica === undefined) ? '' : (r.relacion_economica ? '1' : '0'));
                                                $('#opinion_'+n).val(r.opinion || '');
                                                $('#comentarios_'+n).val(r.comentarios || '');
                                                // also show previews if returned
                                                try {
                                                    if (r.photos) {
                                                        if (r.photos.front && r.photos.front.url) {
                                                            var $pf = $('#preview_front_' + n);
                                                            $pf.html('<div style="display:inline-block;margin-right:6px;"><img src="'+r.photos.front.url+'" style="max-width:120px;max-height:90px;border:1px solid #ddd;padding:2px;display:block;"/><a href="#" class="btn btn-sm btn-link btn-delete-foto" data-id="'+r.photos.front.id+'" data-referencia="'+n+'" data-tipo="front">Eliminar</a></div>');
                                                        }
                                                        if (r.photos.back && r.photos.back.url) {
                                                            var $pb = $('#preview_back_' + n);
                                                            $pb.html('<div style="display:inline-block;margin-right:6px;"><img src="'+r.photos.back.url+'" style="max-width:120px;max-height:90px;border:1px solid #ddd;padding:2px;display:block;"/><a href="#" class="btn btn-sm btn-link btn-delete-foto" data-id="'+r.photos.back.id+'" data-referencia="'+n+'" data-tipo="back">Eliminar</a></div>');
                                                        }
                                                    }
                                                } catch(e) { console.warn('preview error', e); }
                                            });
                                        }
                                        // set download link in modal
                                        $('#download_referencias_btn').attr('href', '<?php echo base_url('solicitudes/download_referencias/'); ?>' + _qid).show();
                                        $('#modalReferencias').modal('show');
                                    }).fail(function(){ $('#modalReferencias').modal('show'); });
                                }
                        }
                    } catch(err) {
                        console.warn('referencias: auto-open error', err);
                    }
                }

                function waitForjQuery(tries){
                    if(window.jQuery){ init(window.jQuery); return; }
                    if(tries <= 0) return; 
                    setTimeout(function(){ waitForjQuery(tries - 1); }, 100);
                }

                waitForjQuery(50); // espera hasta ~5s
            })();
            </script>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                </div>
            </footer>
        </div>
    </div>
</div>
