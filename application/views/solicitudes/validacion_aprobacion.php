<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <style>
                /* Match compact table styles used by solicitudes list so dimensions align */
                #aprobaciones-table.table-compact td, #aprobaciones-table.table-compact th{
                    padding: .12rem .28rem;
                    vertical-align: middle;
                    font-size: .72rem;
                    line-height: 1.02;
                    white-space: nowrap;
                }
                #aprobaciones-table.table-compact thead th{ font-size: .7rem; padding: .18rem .28rem; }
                #aprobaciones-table.table-compact .btn{ padding: .12rem .28rem; font-size: .72rem; min-width: auto; }
                /* Truncate Cliente/Documento columns to prevent layout stretching */
                #aprobaciones-table th:nth-child(2), #aprobaciones-table td:nth-child(2),
                #aprobaciones-table th:nth-child(3), #aprobaciones-table td:nth-child(3){
                    max-width: 180px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                /* Actions column: allow wrapping and compact button grid */
                #aprobaciones-table th:nth-child(9), #aprobaciones-table td:nth-child(9){ min-width: 420px; white-space: normal; }
                #aprobaciones-table td:nth-child(9) .btn-group{
                    display: flex;
                    flex-wrap: wrap;
                    gap: 4px;
                    align-items: center;
                }
                #aprobaciones-table td:nth-child(9) .btn-group .btn{
                    white-space: nowrap;
                    margin: 0;
                }
                @media (max-width: 1366px){
                    #aprobaciones-table th:nth-child(9), #aprobaciones-table td:nth-child(9){ min-width: 360px; }
                }
            </style>
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
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="aprob_search" class="form-control" placeholder="Buscar por código o cliente..." />
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-5 text-right">
                            <small class="text-muted">Filtrar por estado o buscar por cliente/código.</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="mb-1">Fecha inicio</label>
                            <input type="date" id="rep_fecha_inicio" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-2">
                            <label class="mb-1">Fecha fin</label>
                            <input type="date" id="rep_fecha_fin" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">Estado del reporte</label>
                            <select id="aprob_filter_status" class="form-control form-control-sm">
                                <option value="all">Todos</option>
                                <option value="pending">Pendiente</option>
                                <option value="approved">Aprobado</option>
                                <option value="rejected">Rechazado</option>
                                <option value="annulled">Anulado</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex align-items-end justify-content-end flex-column">
                            <button type="button" id="btn_generar_reporte_aprob" class="btn btn-sm btn-primary">
                                <i class="fa fa-file-pdf"></i> Generar Resumen para Firma
                            </button>
                            <small class="text-muted mt-2">Los filtros de fecha y estado se aplican a la tabla y también se usan al generar el PDF.</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="aprobaciones-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Vía Aprobación</th>
                                    <th>Aprobado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($solicitudes)) : foreach ($solicitudes as $s) : ?>
                                    <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                    <tr data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <td><?php echo $s->idsolicitud; ?></td>
                                        <td><?php echo trim($s->nombres . ' ' . $s->apellidos); ?></td>
                                        <td><?php echo (!empty($s->tipo_documento) ? $s->tipo_documento : ''); ?> <?php echo (!empty($s->numero_doc) ? '<br/><small class="text-muted">' . $s->numero_doc . '</small>' : ''); ?></td>
                                        <td><?php
                                            $fecha = !empty($s->fecha_recepcion) ? $s->fecha_recepcion : (!empty($s->fecha_solicitud) ? $s->fecha_solicitud : '');
                                            if ($fecha) {
                                                $dt = date_create($fecha);
                                                echo $dt ? date_format($dt, 'Y-m-d') : $fecha;
                                            } else {
                                                echo '';
                                            }
                                        ?></td>
                                        <td><?php echo (isset($s->monto_solicitado) ? number_format($s->monto_solicitado, 2) : ''); ?></td>
                                        <td>
                                            <?php
                                            if ($status === 'approved') {
                                                echo '<span class="badge badge-success">Aprobado</span>';
                                            } elseif ($status === 'rejected') {
                                                echo '<span class="badge badge-danger">Rechazado</span>';
                                            } elseif ($status === 'annulled') {
                                                echo '<span class="badge badge-secondary">Anulado</span>';
                                            } else {
                                                echo '<span class="badge badge-warning">Pendiente</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo (!empty($s->aprobado_por) ? $s->aprobado_por : ''); ?></td>
                                        <td><?php echo (!empty($s->aprobado_usuario) ? $s->aprobado_usuario : ''); ?></td>
                                        <td>
                                            <!-- Edit is disabled in this view -->
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-success btn-aprobacion" data-id="<?php echo $s->idsolicitud; ?>" title="Aprobacion">Aprobacion</button>
                                                <button class="btn btn-sm btn-info btn-view-aprob" data-id="<?php echo $s->idsolicitud; ?>" title="Ver Aprobación"><i class="fa fa-eye"></i> Ver</button>
                                                <?php $disabled = ($status === 'rejected' || $status === 'annulled') ? 'disabled aria-disabled="true" class="disabled"' : ''; ?>
                                                <button <?php echo $disabled; ?> class="btn btn-sm btn-outline-primary btn-download-aprob" data-id="<?php echo $s->idsolicitud; ?>" title="Descargar Aprobaciones"><i class="fa fa-download"></i> Descargar</button>
                                                <?php
                                                if (!empty($s->has_plan) && $s->has_plan == 1) {
                                                    echo '<button class="btn btn-sm btn-info btn-reimprimir-plan" data-id="' . $s->idsolicitud . '" title="Reimprimir Plan de Pago" onclick="window.location.href=\'' . site_url('planescredito') . '\';"><i class="fa fa-print"></i> Reimprimir</button>';
                                                } else {
                                                    echo '<button ' . $disabled . ' class="btn btn-sm btn-warning btn-emit-plan" data-id="' . $s->idsolicitud . '" title="Emitir Plan de Pago"><i class="fa fa-file-alt"></i> Emitir plan</button>';
                                                }
                                                if ($status === 'approved') {
                                                    echo '<button class="btn btn-sm btn-outline-danger btn-anular-credito" data-id="' . $s->idsolicitud . '" title="Anular Crédito"><i class="fa fa-ban"></i> Anular</button>';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay registros para mostrar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Anulación -->
            <div class="modal fade" id="anularModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Anular Crédito - Solicitud <span id="anular_ids"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning mb-2">
                                Esta acción solo está permitida si no hay pagos y no existe desembolso ejecutado.
                            </div>
                            <div class="form-group mb-0">
                                <label>Comentario de anulación (obligatorio)</label>
                                <textarea id="anular_comment" class="form-control" rows="3" placeholder="Explique el motivo de la anulación"></textarea>
                            </div>
                            <div id="anular_error" class="text-danger mt-2" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" id="anular_save" class="btn btn-danger">Confirmar anulación</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Aprobacion -->
            <div class="modal fade" id="aprobModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Aprobación - Solicitud <span id="aprob_ids"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div id="aprob_propuestas" style="min-height:60px; margin-bottom:10px"></div>
                            <div id="aprob_list" style="min-height:100px"></div>
                            <hr/>
                            <div class="form-group">
                                <label>Decisión</label>
                                <div>
                                    <label class="mr-3"><input type="radio" name="aprob_decision" value="approve"> Aprobar</label>
                                    <label><input type="radio" name="aprob_decision" value="reject"> Rechazar</label>
                                </div>
                                <small class="form-text text-muted">Elija una opción. No se puede aprobar y rechazar a la vez.</small>
                            </div>
                            <div class="form-group">
                                <label>¿Aprobado por?</label>
                                <select id="aprob_aprobado_por" class="form-control">
                                    <option value="Comite Interno">Comite Interno</option>
                                    <option value="Junta Directiva">Junta Directiva</option>
                                </select>
                                <small class="form-text text-muted">Seleccione la instancia que emite la decisión.</small>
                            </div>
                            <div class="form-group">
                                <label>Comentario (obligatorio)</label>
                                <textarea id="aprob_comment" class="form-control" rows="3" placeholder="Escriba el comentario de aprobación o rechazo"></textarea>
                                <small class="form-text text-muted">Este comentario será el único que se registre para la validación.</small>
                            </div>
                            <div class="form-group">
                                <label>Adjuntar foto (opcional)</label>
                                <input type="file" id="aprob_photo" accept="image/jpeg,image/png" class="form-control-file">
                                <small class="form-text text-muted">Solo JPG/PNG, máximo 5MB.</small>
                            </div>
                            <div id="aprob_error" class="text-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" id="aprob_save" class="btn btn-primary">Registrar Decisión</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function initAprob(){
                    // Set row status class: 'pending'|'approved'|'rejected'

                // Filter and search for approvals table
                function applyAprobFilters(){
                    var q = jQuery('#aprob_search').val().toLowerCase().trim();
                    var status = jQuery('#aprob_filter_status').val();
                    var startDate = jQuery('#rep_fecha_inicio').val();
                    var endDate = jQuery('#rep_fecha_fin').val();

                    jQuery('#aprobaciones-table tbody tr').each(function(){
                        var $tr = jQuery(this);
                        var id = $tr.data('id').toString();
                        var client = $tr.find('td').eq(1).text().toLowerCase();
                        var rowStatus = $tr.data('status') || 'pending';
                        var rowDateText = $tr.find('td').eq(3).text().trim();
                        var rowDateValue = NaN;
                        if(rowDateText){
                            if(/^\d{4}-\d{2}-\d{2}/.test(rowDateText)){
                                rowDateValue = Date.parse(rowDateText);
                            } else if(/^\d{2}\/\d{2}\/\d{4}/.test(rowDateText)){
                                var d = rowDateText.split('/');
                                rowDateValue = Date.UTC(parseInt(d[2],10), parseInt(d[1],10)-1, parseInt(d[0],10));
                            } else {
                                rowDateValue = Date.parse(rowDateText);
                            }
                        }

                        var matchesQuery = q === '' || id.indexOf(q) !== -1 || client.indexOf(q) !== -1;
                        var matchesStatus = (status === 'all') || (status === rowStatus);
                        var matchesDate = true;
                        var startDateValue = !startDate ? NaN : Date.parse(startDate);
                        var endDateValue = !endDate ? NaN : Date.parse(endDate);
                        if(!isNaN(endDateValue)){
                            endDateValue += 24 * 60 * 60 * 1000 - 1; // include the entire end day
                        }

                        if(startDate){
                            matchesDate = matchesDate && !isNaN(rowDateValue) && rowDateValue >= startDateValue;
                        }
                        if(endDate){
                            matchesDate = matchesDate && !isNaN(rowDateValue) && rowDateValue <= endDateValue;
                        }

                        if(matchesQuery && matchesStatus && matchesDate){
                            $tr.show();
                        } else {
                            $tr.hide();
                        }
                    });
                }

                    function markRowStatus(id, status){
                        try{
                            var clsMap = {
                                // pending: no special background (leave default table row color)
                                'pending': '',
                                'approved': 'table-success',
                                'rejected': 'table-danger',
                                'annulled': 'table-secondary'
                            };
                            var remove = ['table-warning','table-success','table-danger','table-secondary'];
                            var $tr = jQuery('#aprobaciones-table tbody tr[data-id="'+id+'"]');
                            $tr.removeClass(remove.join(' '));
                            if(status && clsMap[status]) $tr.addClass(clsMap[status]);
                            if(typeof status !== 'undefined'){ $tr.attr('data-status', status); }

                            try{
                                var $anularBtn = $tr.find('.btn-anular-credito');
                                if(status === 'annulled' || status === 'rejected' || status === 'pending'){
                                    $anularBtn.remove();
                                }
                            }catch(e3){ /* ignore anular button errors */ }

                            try{
                                var $emit = $tr.find('.btn-emit-plan');
                                var $download = $tr.find('.btn-download-aprob');
                                if(status === 'annulled'){
                                    $emit.prop('disabled', true).addClass('disabled').attr('aria-disabled', 'true');
                                    $download.prop('disabled', true).addClass('disabled').attr('aria-disabled', 'true');
                                }
                            }catch(e4){ /* ignore action button errors */ }

                            // Update the approval button color to indicate final state
                            try{
                                var $btn = $tr.find('.btn-aprobacion');
                                if($btn && $btn.length){
                                    // remove previous color classes we might have applied
                                    $btn.removeClass('btn-success btn-dark');
                                    if(status === 'approved' || status === 'rejected' || status === 'annulled'){
                                        $btn.addClass('btn-dark');
                                    } else {
                                        $btn.addClass('btn-success');
                                    }
                                }
                            }catch(e2){ /* ignore button color errors */ }

                        }catch(e){ console && console.warn && console.warn('markRowStatus error', e); }
                    }

                    // Backwards-compatible wrapper: some code called markRowApproved
                    function markRowApproved(id, hasAny){
                        try{
                            if(hasAny){
                                // treat presence of any approval as 'approved' for visual purposes
                                markRowStatus(id, 'approved');
                            } else {
                                markRowStatus(id, 'pending');
                            }
                        }catch(e){ console && console.warn && console.warn('markRowApproved error', e); }
                    }

                    // On page load, check visible rows for approvals (throttle requests)
                    function scanAndMarkRows(){
                        var rows = Array.prototype.slice.call(document.querySelectorAll('#aprobaciones-table tbody tr[data-id]'));
                        rows.forEach(function(r, idx){
                            var id = r.getAttribute('data-id');
                            if(!id) return;
                            // space requests by 120ms to avoid spikes
                            setTimeout(function(){
                                var url = '<?php echo base_url($this->router->fetch_class() . '/get_aprobaciones_ajax/'); ?>' + id;
                                jQuery.getJSON(url).done(function(resp){
                                    try{
                                        if(resp && resp.status && resp.aprobaciones && resp.aprobaciones.length>0){
                                            // inspect latest approval comment to decide approved/rejected
                                            var latest = resp.aprobaciones[0];
                                            var txt = (latest.comment || '').toString().toLowerCase();
                                            if(txt.indexOf('anul') !== -1){
                                                markRowStatus(id, 'annulled');
                                            } else if(txt.indexOf('rechaz') !== -1){
                                                markRowStatus(id, 'rejected');
                                            } else if(txt.indexOf('aprob') !== -1){
                                                markRowStatus(id, 'approved');
                                            } else {
                                                markRowStatus(id, 'pending');
                                            }
                                            // disable approval button for finalized states (approved/rejected)
                                            try{
                                                var $btn = jQuery('#aprobaciones-table tbody tr[data-id="'+id+'"] .btn-aprobacion');
                                                if($btn && $btn.length){
                                                    if(txt.indexOf('anul') !== -1 || txt.indexOf('rechaz') !== -1 || txt.indexOf('aprob') !== -1){
                                                        $btn.prop('disabled', true).addClass('disabled');
                                                    } else {
                                                        $btn.prop('disabled', false).removeClass('disabled');
                                                    }
                                                }
                                            }catch(e2){ }
                                        } else {
                                            // no approvals -> ensure pending state
                                            markRowStatus(id, 'pending');
                                            try{ jQuery('#aprobaciones-table tbody tr[data-id="'+id+'"] .btn-aprobacion').prop('disabled', false).removeClass('disabled'); }catch(e3){}
                                        }
                                    }catch(e){ /* ignore */ }
                                }).fail(function(){ /* ignore errors */ });
                            }, idx * 120);
                        });
                    }

                    function renderAprob(list){
                        var $c = jQuery('#aprob_list');
                        $c.empty();
                        if(!list || !list.length){
                            $c.append('<div class="text-muted">No hay aprobaciones registradas.</div>');
                            return;
                        }
                        list.forEach(function(it){
                            var header = '<div class="d-flex justify-content-between align-items-center"><strong>' + (it.username || 'Usuario') + '</strong><small class="text-muted"> ' + (it.created_at ? it.created_at : '') + '</small></div>';
                            var body = '<div class="pt-1 pb-2">' + (it.comment ? jQuery('<div>').text(it.comment).html().replace(/\n/g,'<br>') : '') + '</div>';
                            $c.append('<div class="border rounded px-2 py-2 mb-2"><div><strong>' + (it.role||'') + '</strong></div>' + header + body + '</div>');
                        });
                        // if there are approvals, disable the approval button for that solicitud row
                        if (list && list.length > 0) {
                            try{
                                var rid = jQuery('#aprob_ids').text();
                                jQuery('#aprobaciones-table tbody tr[data-id="'+rid+'"] .btn-aprobacion').prop('disabled', true).addClass('disabled');
                            }catch(e){ }
                        }
                    }

                    // flag that indicates modal should be read-only (set by openAprobHandler)
                    var modalReadOnly = false;

                    function renderGarantias(list, total){
                        var $c = jQuery('#aprob_garantias');
                        if(!$c.length){
                            // create container if it doesn't exist
                            jQuery('#aprob_propuestas').after('<div id="aprob_garantias" style="min-height:30px; margin-bottom:10px"></div>');
                            $c = jQuery('#aprob_garantias');
                        }
                        $c.empty();
                        if(!list || !list.length){
                            $c.append('<div class="text-muted"><small>No hay garantías registradas.</small></div>');
                            return;
                        }
                        var html = '<div><strong>Monto de la garantía:</strong></div><div class="mt-2">';
                        html += '<div class="border rounded p-2 mb-2" style="background:#f9f9f9;">';
                        html += '<div class="row">';
                        html += '<div class="col-md-8">';
                                // Tasa de cambio: puedes ajustar esto según tu contexto o traerlo dinámicamente
                                var tasa_cambio = window.TASA_DOLAR_COR || 36.5; // Ejemplo: 36.5 córdobas por dólar
                                var html = '<div><strong>Monto de la garantía:</strong></div><div class="mt-2">';
                                html += '<div class="border rounded p-2 mb-2" style="background:#f9f9f9;">';
                                html += '<div class="row">';
                                html += '<div class="col-md-8">';
                                html += '<table class="table table-sm table-bordered mb-0" style="font-size:0.85rem;">';
                                html += '<thead><tr><th>Artículo</th><th>Cantidad</th><th>Marca</th><th>Modelo</th><th class="text-right">Costo Unit.</th><th class="text-right">Subtotal</th></tr></thead>';
                                html += '<tbody>';
                                var totalCordoba = parseFloat(total) || 0;
                                var totalDolar = totalCordoba / tasa_cambio;
                                // Obtener el monto solicitado desde el contexto global o variable
                                var montoSolicitado = window.MONTO_SOLICITADO || 0;
                                // Si no existe, intentar buscar en DOM
                                if(montoSolicitado === 0){
                                    var montoInput = jQuery('.aprob-monto').first();
                                    if(montoInput.length){
                                        montoSolicitado = parseFloat(montoInput.val()) || 0;
                                    }
                                }
                                var coberturaGlobal = (totalDolar > 0 && montoSolicitado > 0) ? (totalDolar / montoSolicitado * 100) : 0;
                                list.forEach(function(g){
                                    var cantidad = g.cantidad || 1;
                                    var costo_cordoba = parseFloat(g.costo) || 0;
                                    var subtotal_cordoba = cantidad * costo_cordoba;
                                    var costo_dolar = costo_cordoba / tasa_cambio;
                                    var subtotal_dolar = subtotal_cordoba / tasa_cambio;
                                    html += '<tr>';
                                    html += '<td>' + jQuery('<div>').text(g.nombre || '').html() + '</td>';
                                    html += '<td class="text-center">' + cantidad + '</td>';
                                    html += '<td>' + jQuery('<div>').text(g.marca || '').html() + '</td>';
                                    html += '<td>' + jQuery('<div>').text(g.modelo || '').html() + '</td>';
                                    html += '<td class="text-right">C$' + costo_cordoba.toFixed(2) + '<br/><span style="color:#007bff;">$' + costo_dolar.toFixed(2) + '</span></td>';
                                    html += '<td class="text-right">C$' + subtotal_cordoba.toFixed(2) + '<br/><span style="color:#007bff;">$' + subtotal_dolar.toFixed(2) + '</span></td>';
                                    html += '</tr>';
                                });
                                html += '</tbody></table>';
                                html += '</div>';
                                html += '<div class="col-md-4">';
                                html += '<div class="p-3 border rounded" style="background:#fff;">';
                                html += '<div class="text-center"><strong>Total Garantía</strong></div>';
                                var totalCordoba = parseFloat(total) || 0;
                                var totalDolar = totalCordoba / tasa_cambio;
                                html += '<div class="text-center mt-2" style="font-size:1.5rem; color:#28a745; font-weight:bold;">C$' + totalCordoba.toFixed(2) + '<br/><span style="color:#007bff;">$' + totalDolar.toFixed(2) + '</span></div>';
                                html += '<div class="text-center mt-2" style="font-size:1.1rem; color:#333;">Cobertura de Garantía: <span style="color:#007bff; font-weight:bold;">' + coberturaGlobal.toFixed(2) + '%</span></div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                $c.append(html);
                    }

                    function renderPropuestas(list){
                        var $c = jQuery('#aprob_propuestas');
                        $c.empty();
                        if(!list || !list.length){
                            $c.append('<div class="text-muted">No hay propuestas seleccionadas en la solicitud.</div>');
                            return;
                        }
                        // render as editable list: allow monto and tasa overrides per propuesta
                        var html = '<div><strong>Productos propuestos:</strong></div><div class="mt-2">';
                        list.forEach(function(p, idx){
                            var title = p.nombre ? p.nombre : (p.descripcion ? p.descripcion : ('ID ' + (p.id||'')));
                            // Prefer explicit solicitud values when present
                            var montoVal = (p.monto_solicitado !== undefined && p.monto_solicitado !== null && p.monto_solicitado !== '') ? p.monto_solicitado : ((p.monto_min && p.monto_min>0) ? p.monto_min : (p.monto_max && p.monto_max>0 ? p.monto_max : ''));
                            var tasaVal = '';
                            var tasaRaw = (p.tasa_mensual !== undefined && p.tasa_mensual !== null && p.tasa_mensual !== '') ? p.tasa_mensual : (p.tasa !== undefined ? p.tasa : '');
                            if (tasaRaw !== '' && !isNaN(parseFloat(tasaRaw))) {
                                var tv = parseFloat(tasaRaw);
                                if (tv > 0 && tv <= 1) tv = tv * 100; // convert decimal (0.06) -> percent (6)
                                tasaVal = tv;
                            }
                            html += '<div class="border rounded p-2 mb-2 propuesta-item" data-id="'+(p.id||'')+'">';
                            html += '<div class="d-flex justify-content-between align-items-center"><div><strong>' + jQuery('<div>').text(title).html() + '</strong></div>';
                            html += '<div><small class="text-muted">' + (p.clasificacion||'') + '</small></div></div>';
                            html += '<div class="row mt-2">';
                            html += '<div class="col-md-4"><label class="mb-0"><small>Monto</small></label><div class="input-group"><input disabled type="number" step="0.01" class="form-control aprob-monto" value="'+montoVal+'"/><div class="input-group-append"><button class="btn btn-outline-secondary btn-edit-field" data-field="monto" data-id="'+(p.id||'')+'" type="button"><i class="fa fa-edit"></i></button></div></div></div>';
                            html += '<div class="col-md-4"><label class="mb-0"><small>Tasa %</small></label><div class="input-group"><input disabled type="number" step="0.0001" class="form-control aprob-tasa" value="'+tasaVal+'"/><div class="input-group-append"><button class="btn btn-outline-secondary btn-edit-field" data-field="tasa" data-id="'+(p.id||'')+'" type="button"><i class="fa fa-edit"></i></button></div></div></div>';
                            var plazoVal = (typeof p.plazo_solicitado !== "undefined" && p.plazo_solicitado !== null && p.plazo_solicitado !== '') ? p.plazo_solicitado : (p.plazo_max || p.plazo || '');
                            html += '<div class="col-md-4"><label class="mb-0"><small>Plazo meses</small></label><div class="input-group"><input disabled type="number" step="1" class="form-control aprob-plazo" value="'+plazoVal+'"/><div class="input-group-append"><button class="btn btn-outline-secondary btn-edit-field" data-field="plazo" data-id="'+(p.id||'')+'" type="button"><i class="fa fa-edit"></i></button></div></div></div>';
                            html += '</div>';
                            // commission display: prefer solicitud value, and convert decimals to percent
                            var comRaw = (p.comision_desembolso !== undefined && p.comision_desembolso !== null && p.comision_desembolso !== '') ? p.comision_desembolso : (p.comision !== undefined ? p.comision : (p.porcentaje_desembolso !== undefined ? p.porcentaje_desembolso : ''));
                            var comDispInput = '';
                            if (comRaw !== '' && !isNaN(parseFloat(comRaw))) {
                                var cv = parseFloat(comRaw);
                                if (cv > 0 && cv <= 1) cv = cv * 100;
                                // If cv is a whole number, show integer (e.g. 7 not 7.00000). Otherwise trim trailing zeros.
                                if (Math.abs(cv - Math.round(cv)) < 1e-9) {
                                    comDispInput = String(Math.round(cv));
                                } else {
                                    comDispInput = String(parseFloat(cv.toFixed(4)).toString().replace(/\.0+$|(?<=\.[0-9]*?)0+$/,''));
                                }
                            } else {
                                comDispInput = (p.comision_desembolso || p.comision || '');
                            }
                            html += '<div class="mt-1"><small class="text-muted">Comisión desembolso (%) <input disabled type="number" step="0.0001" class="form-control form-control-sm d-inline-block aprob-comision" style="width:120px; margin-left:8px;" value="'+comDispInput+'"/><button class="btn btn-sm btn-outline-secondary btn-edit-field ml-2" data-field="comision" data-id="'+(p.id||'')+'" type="button"><i class="fa fa-edit"></i></button></small></div>';
                            html += '</div>';
                        });
                        html += '</div>';
                        $c.append(html);
                        // Enforce read-only mode: hide/disable edit buttons and keep inputs disabled
                        try{
                            if(modalReadOnly){
                                $c.find('.btn-edit-field').hide().prop('disabled', true);
                                $c.find('.aprob-monto, .aprob-tasa, .aprob-plazo, .aprob-comision').prop('disabled', true);
                            } else {
                                $c.find('.btn-edit-field').show().prop('disabled', false);
                                // inputs are rendered disabled by default; keep them disabled until edit is requested
                                $c.find('.aprob-monto, .aprob-tasa, .aprob-plazo, .aprob-comision').prop('disabled', true);
                            }
                        }catch(e){ console && console.warn && console.warn('apply modalReadOnly state error', e); }
                    }

                    function loadAprob(id){
                        var url = '<?php echo base_url($this->router->fetch_class() . '/get_aprobaciones_ajax/'); ?>' + id;
                        jQuery('#aprob_list').html('<div class="text-center text-muted">Cargando...</div>');
                            // Clear any previous fields while loading
                        try{ jQuery('#aprob_aprobado_por').val('Comite Interno'); }catch(e){}
                        jQuery.getJSON(url).done(function(resp){
                            console.log('get_aprobaciones_ajax:', resp);
                            if(resp && resp.status){
                                // render propuestas first (if present)
                                try{ renderPropuestas(resp.propuestas); }catch(e){ console && console.warn && console.warn('renderPropuestas error', e); }
                                // render garantias info after propuestas
                                try{ renderGarantias(resp.garantias, resp.total_garantias); }catch(e){ console && console.warn && console.warn('renderGarantias error', e); }
                                renderAprob(resp.aprobaciones);
                                // Populate modal fields for the latest approval (if present)
                                try{
                                    var latest = (resp.aprobaciones && resp.aprobaciones.length>0) ? resp.aprobaciones[0] : null;
                                    if(latest){
                                        var full = (latest.comment || '').toString();
                                        // decision: look for [Aprobado] or [Rechazado]
                                        if(/\[Aprobado\]/i.test(full)){
                                            jQuery('input[name="aprob_decision"][value="approve"]').prop('checked', true);
                                        } else if(/\[Rechazado\]/i.test(full)){
                                            jQuery('input[name="aprob_decision"][value="reject"]').prop('checked', true);
                                        }
                                        // strip tags [Aprobado] / [Rechazado] and [foto:...] from displayed comment
                                        var cleaned = full.replace(/\[Aprobado\]|\[Rechazado\]/ig, '').replace(/\[foto:([^\]]+)\]/i, '').trim();
                                        jQuery('#aprob_comment').val(cleaned);
                                        // set aprobado_por if provided
                                        try{ if(latest.aprobado_por){ jQuery('#aprob_aprobado_por').val(latest.aprobado_por); } }catch(e){}
                                        // show saved photo preview if present in comment tag
                                        var m = full.match(/\[foto:([^\]]+)\]/i);
                                        if(m && m[1]){
                                            var photoPath = m[1];
                                            var baseUploads = '<?php echo base_url('uploads/'); ?>';
                                            var src = baseUploads + photoPath;
                                            // create or update preview container
                                            var $prev = jQuery('#aprob_saved_photo');
                                            if(!$prev.length){
                                                jQuery('#aprob_photo').after('<div id="aprob_saved_photo" class="mt-2"></div>');
                                                $prev = jQuery('#aprob_saved_photo');
                                            }
                                            $prev.empty();
                                            var thumb = '<a href="'+src+'" target="_blank" rel="noopener"><img src="'+src+'" style="max-width:180px; max-height:140px; border:1px solid #ddd; padding:4px; background:#fff;" alt="Foto guardada"></a>';
                                            $prev.append(thumb);
                                        } else {
                                            // remove any previous preview if not present
                                            jQuery('#aprob_saved_photo').remove();
                                        }
                                    } else {
                                        // clear modal fields when no approvals
                                        jQuery('#aprob_comment').val('');
                                        jQuery('input[name="aprob_decision"]').prop('checked', false);
                                        jQuery('#aprob_saved_photo').remove();
                                    }
                                }catch(e){ console && console.warn && console.warn('populate modal fields error', e); }
                                // Decide row color based on latest approval comment (approve/reject)
                                if(resp.aprobaciones && resp.aprobaciones.length > 0){
                                    var latest = resp.aprobaciones[0];
                                    var txt = (latest.comment || '').toLowerCase();
                                    if(txt.indexOf('anul') !== -1){ markRowStatus(id, 'annulled'); }
                                    else if(txt.indexOf('rechaz') !== -1){ markRowStatus(id, 'rejected'); }
                                    else if(txt.indexOf('aprobad') !== -1){ markRowStatus(id, 'approved'); }
                                    else { markRowStatus(id, 'pending'); }
                                } else {
                                    markRowStatus(id, 'pending');
                                }
                                // Re-apply filters after marking status so visible rows update
                                try{ if(typeof applyAprobFilters === 'function') applyAprobFilters(); }catch(e){}
                            } else {
                                var msg = (resp && resp.message) ? resp.message : 'No se pudieron cargar aprobaciones.';
                                jQuery('#aprob_list').html('<div class="text-danger">'+msg+'</div>');
                            }
                        }).fail(function(){
                            jQuery('#aprob_list').html('<div class="text-danger">Error al cargar aprobaciones.</div>');
                        });
                    }

                    var selId = null;

                    // Robust event binding: prefer jQuery if available
                    function openAprobHandler(elem, readOnly){
                        try{
                            selId = elem.getAttribute('data-id') || elem.dataset.id;
                        } catch(err){
                            selId = null;
                        }
                        if(!selId){ console && console.warn && console.warn('Aprobacion: id no encontrado en el elemento.'); }
                        jQuery('#aprob_ids').text(selId);
                        jQuery('#aprob_comite_interno').val('');
                        jQuery('#aprob_comite_externo').val('');
                        jQuery('#aprob_gerencia').val('');
                        jQuery('#aprob_error').hide().text('');
                        // set modal read-only state
                        modalReadOnly = !!readOnly;
                        if (modalReadOnly) {
                            jQuery('#aprob_save').hide();
                            jQuery('input[name="aprob_decision"]').prop('disabled', true);
                            jQuery('#aprob_aprobado_por').prop('disabled', true);
                            jQuery('#aprob_photo').prop('disabled', true).hide();
                        } else {
                            jQuery('#aprob_save').show();
                            jQuery('input[name="aprob_decision"]').prop('disabled', false);
                            jQuery('#aprob_aprobado_por').prop('disabled', false);
                            jQuery('#aprob_photo').prop('disabled', false).show();
                        }
                        // show modal (bootstrap)
                        jQuery('#aprobModal').modal('show');
                        loadAprob(selId);
                    }

                    // delegated: attach to table to avoid conflicts
                    jQuery(document).on('click', '#aprobaciones-table .btn-aprobacion, .btn-aprobacion', function(e){
                        e.preventDefault();
                        console && console.log && console.log('click .btn-aprobacion (jQuery)', this);
                        // Determine if this solicitud is already approved/rejected and open read-only in that case
                        try{
                            var $tr = jQuery(this).closest('tr[data-id]');
                            var status = $tr.length ? ($tr.data('status') || '').toString().toLowerCase() : '';
                            var readOnly = (status === 'approved' || status === 'rejected' || status === 'annulled');
                            openAprobHandler(this, readOnly);
                        }catch(e){
                            openAprobHandler(this);
                        }
                    });

                    // View-only button: open modal but disable save controls
                    jQuery(document).on('click', '#aprobaciones-table .btn-view-aprob, .btn-view-aprob', function(e){
                        e.preventDefault();
                        openAprobHandler(this, true);
                    });

                    // Field edit: open comment modal then enable field for editing
                    jQuery(document).on('click', '.btn-edit-field', function(e){
                        e.preventDefault();
                        var $btn = jQuery(this);
                        var field = $btn.data('field');
                        var pid = $btn.data('id');
                        if(!field || !pid) return;
                        // show modal to capture mandatory comment
                        jQuery('#fieldEditModal').data('field', field).data('pid', pid).data('btn', $btn).modal('show');
                        jQuery('#fieldEditComment').val('');
                        jQuery('#fieldEditError').hide().text('');
                    });

                    // handle comment save from modal
                    jQuery(document).on('click', '#fieldEditSave', function(e){
                        e.preventDefault();
                        var comment = jQuery('#fieldEditComment').val().trim();
                        if(!comment || comment.length < 3){ jQuery('#fieldEditError').show().text('Ingrese un comentario (mín. 3 caracteres)'); return; }
                        var field = jQuery('#fieldEditModal').data('field');
                        var pid = jQuery('#fieldEditModal').data('pid');
                        var $btn = jQuery('#fieldEditModal').data('btn');
                        // find the propuesta-item with data-id
                        var $item = jQuery('#aprob_propuestas .propuesta-item[data-id="'+pid+'"]');
                        if($item.length){
                            // store comment in a data structure on the item
                            var existing = $item.data('overrideComments') || {};
                            existing[field] = comment;
                            $item.data('overrideComments', existing);
                            // enable the corresponding input for editing
                            if(field === 'monto') $item.find('.aprob-monto').prop('disabled', false).focus();
                            if(field === 'tasa') $item.find('.aprob-tasa').prop('disabled', false).focus();
                            if(field === 'plazo') $item.find('.aprob-plazo').prop('disabled', false).focus();
                            if(field === 'comision') $item.find('.aprob-comision').prop('disabled', false).focus();
                            // visually mark edited
                            $item.find('.propuesta-item').addClass('propuesta-has-edits');
                        }
                        jQuery('#fieldEditModal').modal('hide');
                    });

                    jQuery(document).on('click', '#aprob_save', function(e){
                        e.preventDefault();
                        console && console.log && console.log('click #aprob_save');
                        var decision = jQuery('input[name="aprob_decision"]:checked').val();
                        var comment = jQuery('#aprob_comment').val().trim();
                            var aprobado_por = jQuery('#aprob_aprobado_por').val();
                        if(!decision){ jQuery('#aprob_error').show().text('Seleccione Aprobar o Rechazar.'); return; }
                        if(!comment || comment.length < 3){ jQuery('#aprob_error').show().text('Ingrese un comentario (mín. 3 caracteres).'); return; }
                        var fd = new FormData();
                        fd.append('idsolicitud', selId);
                        fd.append('decision', decision);
                        fd.append('comment', comment);
                            fd.append('aprobado_por', aprobado_por);
                        var file = (document.getElementById('aprob_photo')||{}).files;
                        if(file && file.length){ fd.append('photo', file[0]); }

                        // collect propuesta overrides: {id, monto, tasa, plazo, comision, comments}
                        try{
                            // Habilitar temporalmente los inputs disabled para que se puedan leer los valores
                            jQuery('#aprob_propuestas .aprob-monto, #aprob_propuestas .aprob-tasa, #aprob_propuestas .aprob-plazo, #aprob_propuestas .aprob-comision').prop('disabled', false);
                            
                            var overrides = [];
                            jQuery('#aprob_propuestas .propuesta-item').each(function(){
                                var $it = jQuery(this);
                                var pid = $it.data('id');
                                if(!pid) return;
                                var m = $it.find('.aprob-monto').val();
                                var t = $it.find('.aprob-tasa').val();
                                var plazo = $it.find('.aprob-plazo').val();
                                var com = $it.find('.aprob-comision').val();
                                // collect any stored comments for this field (stored in data on the input group)
                                var comments = $it.data('overrideComments') || {};
                                overrides.push({ id: pid, monto: (m===''?null:m), tasa: (t===''?null:t), plazo: (plazo===''?null:plazo), comision: (com===''?null:com), comments: comments });
                            });
                            fd.append('propuesta_overrides', JSON.stringify(overrides));
                            console && console.log && console.log('propuesta_overrides:', overrides);
                        }catch(e){ console && console.warn && console.warn('collect overrides error', e); }

                        jQuery('#aprob_save').prop('disabled', true).text('Guardando...');
                        jQuery.ajax({
                            url: '<?php echo base_url($this->router->fetch_class() . '/submit_validacion_ajax'); ?>',
                            data: fd,
                            type: 'POST',
                            contentType: false,
                            processData: false
                        }).done(function(resp){
                            var json = (typeof resp === 'object') ? resp : JSON.parse(resp || '{}');
                            if(json && json.status){
                                loadAprob(selId);
                                jQuery('input[name="aprob_decision"]').prop('checked', false);
                                jQuery('#aprob_comment').val('');
                                jQuery('#aprob_photo').val('');
                                jQuery('#aprob_error').hide().text('');
                            } else {
                                jQuery('#aprob_error').show().text((json && json.message) ? json.message : 'No se pudo registrar la decisión.');
                            }
                        }).fail(function(){ jQuery('#aprob_error').show().text('Error al guardar la decisión.'); })
                        .always(function(){ jQuery('#aprob_save').prop('disabled', false).text('Registrar Decisión'); });
                    });

                    // Download approvals button: check there is at least 1 approval, otherwise show message
                    jQuery(document).on('click', '#aprobaciones-table .btn-download-aprob, .btn-download-aprob', function(e){
                        e.preventDefault();
                        var $btn = jQuery(this);
                        var $tr = $btn.closest('tr[data-id]');
                        var statusRow = $tr.length ? ($tr.data('status') || '').toString().toLowerCase() : '';
                        if(statusRow === 'rejected'){
                            alert('No se puede descargar aprobaciones: la solicitud está rechazada.');
                            return;
                        }
                        if(statusRow === 'annulled'){
                            alert('No se puede descargar aprobaciones: la solicitud está anulada.');
                            return;
                        }
                        var id = $btn.data('id');
                        if(!id){ alert('ID de solicitud no encontrado.'); return; }
                        var url = '<?php echo base_url($this->router->fetch_class() . '/get_aprobaciones_ajax/'); ?>' + id;
                        jQuery.getJSON(url).done(function(resp){
                            if(resp && resp.status && resp.aprobaciones && resp.aprobaciones.length > 0){
                                // Open download URL in new tab/window
                                var dl = '<?php echo base_url($this->router->fetch_class() . '/download_aprobaciones/'); ?>' + id;
                                window.open(dl, '_blank');
                            } else {
                                alert('Pendiente de aprobación: mínimo debe haber 1 comentario de aprobación.');
                            }
                        }).fail(function(){
                            alert('Error comprobando aprobaciones. Revisa la consola de red.');
                        });
                    });

                    jQuery(document).on('click', '#btn_generar_reporte_aprob', function(e){
                        e.preventDefault();
                        var fi = jQuery('#rep_fecha_inicio').val() || '';
                        var ff = jQuery('#rep_fecha_fin').val() || '';
                        var est = jQuery('#aprob_filter_status').val() || 'all';

                        var url = '<?php echo base_url($this->router->fetch_class() . '/reporte_resumen_aprobaciones'); ?>';
                        var q = [];
                        if (fi !== '') q.push('fecha_inicio=' + encodeURIComponent(fi));
                        if (ff !== '') q.push('fecha_fin=' + encodeURIComponent(ff));
                        if (est !== '') q.push('estado=' + encodeURIComponent(est));
                        if (q.length) url += '?' + q.join('&');

                        window.open(url, '_blank');
                    });

                    // Emitir plan button: redirect to /prestamo preloaded with id
                    jQuery(document).on('click', '#aprobaciones-table .btn-emit-plan, .btn-emit-plan', function(e){
                        e.preventDefault();
                        var $btn = jQuery(this);
                        var id = $btn.data('id');
                        if(!id){ alert('ID de solicitud no encontrado.'); return; }
                        var $tr = $btn.closest('tr[data-id]');
                        var status = $tr.length ? ($tr.data('status') || '').toString().toLowerCase() : '';
                        // Block only if rejected
                        if(status === 'rejected'){
                            alert('No puede emitir plan de pago: la solicitud está rechazada.');
                            return;
                        }
                        if(status === 'annulled'){
                            alert('No puede emitir plan de pago: la solicitud está anulada.');
                            return;
                        }
                        // navigate to prestamo and pre-load the solicitud id via querystring
                        var url = '<?php echo base_url('prestamo'); ?>' + '?idsolicitud=' + encodeURIComponent(id);
                        window.location = url;
                    });

                    var anularSelId = null;
                    jQuery(document).on('click', '#aprobaciones-table .btn-anular-credito, .btn-anular-credito', function(e){
                        e.preventDefault();
                        anularSelId = jQuery(this).data('id');
                        if(!anularSelId){ alert('ID de solicitud no encontrado.'); return; }
                        jQuery('#anular_ids').text(anularSelId);
                        jQuery('#anular_comment').val('');
                        jQuery('#anular_error').hide().text('');
                        jQuery('#anularModal').modal('show');
                    });

                    jQuery(document).on('click', '#anular_save', function(e){
                        e.preventDefault();
                        var comment = jQuery('#anular_comment').val().trim();
                        if(!comment || comment.length < 3){
                            jQuery('#anular_error').show().text('Ingrese el comentario de anulación (mín. 3 caracteres).');
                            return;
                        }
                        jQuery('#anular_save').prop('disabled', true).text('Anulando...');
                        jQuery.ajax({
                            url: '<?php echo base_url($this->router->fetch_class() . '/anular_credito_ajax'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: { idsolicitud: anularSelId, comment: comment }
                        }).done(function(resp){
                            if(resp && resp.status){
                                jQuery('#anularModal').modal('hide');
                                markRowStatus(anularSelId, 'annulled');
                                var $tr = jQuery('#aprobaciones-table tbody tr[data-id="'+anularSelId+'"]');
                                $tr.find('td').eq(5).html('<span class="badge badge-secondary">Anulado</span>');
                                $tr.find('.btn-anular-credito').remove();
                                $tr.find('.btn-emit-plan, .btn-download-aprob').prop('disabled', true).addClass('disabled').attr('aria-disabled', 'true');
                                try{ if(typeof applyAprobFilters === 'function') applyAprobFilters(); }catch(err){}
                            } else {
                                jQuery('#anular_error').show().text((resp && resp.message) ? resp.message : 'No se pudo anular el crédito.');
                            }
                        }).fail(function(){
                            jQuery('#anular_error').show().text('Error al anular el crédito.');
                        }).always(function(){
                            jQuery('#anular_save').prop('disabled', false).text('Confirmar anulación');
                        });
                    });

                    // init: attach search/filter handlers and scan rows
                    jQuery('#aprob_search').on('input', applyAprobFilters);
                    jQuery('#aprob_filter_status').on('change', applyAprobFilters);
                    jQuery('#rep_fecha_inicio, #rep_fecha_fin').on('change', applyAprobFilters);
                    // keep table sorted newest -> oldest by date column (client-side fallback)
                    function sortTableByDateDesc(){
                        var $tbody = jQuery('#aprobaciones-table tbody');
                        var rows = $tbody.find('tr[data-id]').get();
                        rows.sort(function(a,b){
                            var at = jQuery(a).find('td').eq(3).text().trim();
                            var bt = jQuery(b).find('td').eq(3).text().trim();
                            function p(t){
                                if(!t) return 0;
                                if(/^\d{4}-\d{2}-\d{2}/.test(t)) return Date.parse(t);
                                if(/^\d{2}\/\d{2}\/\d{4}/.test(t)){
                                    var d = t.split('/'); return Date.UTC(parseInt(d[2],10), parseInt(d[1],10)-1, parseInt(d[0],10));
                                }
                                var x = Date.parse(t); return isNaN(x)?0:x;
                            }
                            return p(bt) - p(at);
                        });
                        jQuery.each(rows, function(i,r){ $tbody.append(r); });
                    }

                    sortTableByDateDesc();
                    scanAndMarkRows();
                    // ensure filters are applied on initial load
                    try{ if(typeof applyAprobFilters === 'function') applyAprobFilters(); }catch(e){}
                }

                // wait for jQuery to be available before initializing
                (function waitForjQuery(){
                    if(window.jQuery){
                        initAprob();
                    } else {
                        setTimeout(waitForjQuery, 100);
                    }
                })();
            </script>

            <!-- Modal to capture mandatory comment when editing a field -->
            <div class="modal fade" id="fieldEditModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar valor (comentario requerido)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Antes de editar este campo, ingrese el motivo o comentario (obligatorio).</p>
                            <div class="form-group">
                                <textarea id="fieldEditComment" class="form-control" rows="3"></textarea>
                                <div id="fieldEditError" class="text-danger mt-2" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" id="fieldEditSave" class="btn btn-primary">Guardar comentario y editar</button>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                    <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
                </div>
            </footer>

        </div>
    </div>
</div>
