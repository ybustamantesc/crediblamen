                                                        <!-- Modal Anular Movimiento -->
                                                        <div class="modal fade" id="modalAnularMov" tabindex="-1" role="dialog" aria-labelledby="modalAnularMovLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modalAnularMovLabel">Anular Movimiento</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form id="formAnularMov">
                                                                            <input type="hidden" id="anularMovId" />
                                                                            <div class="form-group">
                                                                                <label for="motivoAnulacion">Motivo de anulación</label>
                                                                                <textarea class="form-control" id="motivoAnulacion" rows="3" required></textarea>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                        <button type="button" class="btn btn-danger" id="btnConfirmarAnular">Anular Movimiento</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Modal Ver Cheque -->
                                                        <div class="modal fade" id="modalVerCheque" tabindex="-1" role="dialog" aria-labelledby="modalVerChequeLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-xl" role="document" style="max-width: 1280px;">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-info text-white">
                                                                        <h5 class="modal-title" id="modalVerChequeLabel">Visualizar Cheque</h5>
                                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" id="verChequeId" value="">
                                                                        <style>
                                                                            .desembolso-preview-grid {
                                                                                display: grid;
                                                                                grid-template-columns: minmax(760px, 1.7fr) minmax(320px, .8fr);
                                                                                gap: 18px;
                                                                                align-items: start;
                                                                            }
                                                                            .desembolso-preview-cheque {
                                                                                background: #fff;
                                                                                border: 1px solid #e5e7eb;
                                                                                border-radius: 8px;
                                                                                padding: 10px;
                                                                                overflow: auto;
                                                                                min-height: 500px;
                                                                            }
                                                                            .desembolso-preview-card {
                                                                                border: 1px solid #e5e7eb;
                                                                                border-radius: 8px;
                                                                                background: #fafafa;
                                                                                padding: 14px;
                                                                                min-height: 500px;
                                                                            }
                                                                            .desembolso-preview-card h6 {
                                                                                margin: 0 0 10px;
                                                                                font-weight: 700;
                                                                                color: #1f2937;
                                                                            }
                                                                            .desembolso-preview-row {
                                                                                display: flex;
                                                                                justify-content: space-between;
                                                                                gap: 12px;
                                                                                font-size: 13px;
                                                                                margin-bottom: 8px;
                                                                            }
                                                                            .desembolso-preview-row .label {
                                                                                color: #6b7280;
                                                                                font-weight: 600;
                                                                            }
                                                                            .desembolso-preview-row .value {
                                                                                color: #111827;
                                                                                font-weight: 600;
                                                                                text-align: right;
                                                                            }
                                                                            .desembolso-preview-divider {
                                                                                border-top: 1px solid #d1d5db;
                                                                                margin: 10px 0;
                                                                            }
                                                                            .desembolso-preview-notes {
                                                                                white-space: pre-wrap;
                                                                                background: #fff;
                                                                                border: 1px solid #e5e7eb;
                                                                                border-radius: 6px;
                                                                                padding: 10px;
                                                                                min-height: 72px;
                                                                                font-size: 13px;
                                                                            }
                                                                            @media (max-width: 991.98px) {
                                                                                .desembolso-preview-grid {
                                                                                    grid-template-columns: 1fr;
                                                                                }
                                                                            }
                                                                        </style>
                                                                        <div class="desembolso-preview-grid">
                                                                            <div class="desembolso-preview-cheque" id="chequePreviewPanel">
                                                                                <iframe id="iframeChequePreview" src="about:blank" style="width:100%;height:520px;border:0;background:#fff;"></iframe>
                                                                            </div>
                                                                            <div class="desembolso-preview-card" id="movimientoPreviewPanel" style="display:none;">
                                                                                <h6>Detalle del movimiento</h6>
                                                                                <div class="desembolso-preview-row"><span class="label">Tipo</span><span class="value" id="previewMovTipo">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Forma de pago</span><span class="value" id="previewMovFormaPago">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Monto</span><span class="value" id="previewMovMonto">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Fecha registro</span><span class="value" id="previewMovFechaRegistro">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Fecha aplicación</span><span class="value" id="previewMovFechaAplicacion">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Beneficiario</span><span class="value" id="previewMovBeneficiario">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Referencia 1</span><span class="value" id="previewMovRef1">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Referencia 2</span><span class="value" id="previewMovRef2">-</span></div>
                                                                                <div class="desembolso-preview-divider"></div>
                                                                                <div class="small text-muted mb-1">Descripción</div>
                                                                                <div class="desembolso-preview-notes" id="previewMovDescripcion">Sin descripción.</div>
                                                                            </div>
                                                                            <div class="desembolso-preview-card" id="desembolsoPreviewPanel" style="display:none;">
                                                                                <h6>Detalle del desembolso</h6>
                                                                                <div class="desembolso-preview-row"><span class="label">Cliente</span><span class="value" id="previewDetalleCliente">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Monto crédito</span><span class="value" id="previewDetalleMontoCredito">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Tasa</span><span class="value" id="previewDetalleTasa">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Plazo</span><span class="value" id="previewDetallePlazo">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Fecha desembolso</span><span class="value" id="previewDetalleFecha">-</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Primer pago</span><span class="value" id="previewDetallePrimerPago">-</span></div>
                                                                                <div class="desembolso-preview-divider"></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Costos legales</span><span class="value" id="previewDetalleCostosLegales">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Seguros</span><span class="value" id="previewDetalleSeguros">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Comisiones</span><span class="value" id="previewDetalleComisiones">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Monto renovación</span><span class="value" id="previewDetalleRenovacion">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Renov. principal</span><span class="value" id="previewDetalleRenovPrincipal">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Renov. int. corriente</span><span class="value" id="previewDetalleRenovInteresCorriente">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Renov. int. mora</span><span class="value" id="previewDetalleRenovInteresMora">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Total deducciones</span><span class="value" id="previewDetalleDeducciones">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-row"><span class="label">Neto desembolso</span><span class="value" id="previewDetalleNeto">$ 0.00</span></div>
                                                                                <div class="desembolso-preview-divider"></div>
                                                                                <div class="small text-muted mb-1">Observaciones / distribución</div>
                                                                                <div class="desembolso-preview-notes" id="previewDetalleObservaciones">Sin observaciones registradas.</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                        <button type="button" class="btn btn-danger" id="btnAnularDesembolsoDesdeModal" style="display:none;">Anular</button>
                                                                        <button type="button" class="btn btn-info" id="btnEjecutarDesembolsoPreview" style="display:none;">Ejecutar Desembolso</button>
                                                                        <button type="button" class="btn btn-primary" id="btnImprimirCheque">Imprimir</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Modal Ver Info (Referencias y Descripción) -->
                                                        <div class="modal fade" id="modalVerInfo" tabindex="-1" role="dialog" aria-labelledby="modalVerInfoLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-dark-custom text-white">
                                                                        <div>
                                                                            <h5 class="modal-title" id="modalVerInfoLabel">Información del Movimiento</h5>
                                                                            <small class="subtitle-info" id="subtituloInfo" style="display:block; margin-top:5px; opacity:0.9;">-</small>
                                                                        </div>
                                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="info-block">
                                                                            <div class="info-row">
                                                                                <label class="info-label">Referencia 1:</label>
                                                                                <div class="info-value" id="infoRef1">-</div>
                                                                            </div>
                                                                            <div class="info-row">
                                                                                <label class="info-label">Referencia 2:</label>
                                                                                <div class="info-value" id="infoRef2">-</div>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="info-row">
                                                                                <label class="info-label">Descripción:</label>
                                                                            </div>
                                                                            <div class="info-description" id="infoDesc">-</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <style>
                                                            .info-block {
                                                                padding: 10px 0;
                                                            }
                                                            .info-row {
                                                                margin-bottom: 15px;
                                                            }
                                                            .info-label {
                                                                font-weight: 700;
                                                                color: #1f2937;
                                                                display: block;
                                                                margin-bottom: 5px;
                                                                font-size: 0.95rem;
                                                            }
                                                            .info-value {
                                                                background: #f9fafb;
                                                                border: 1px solid #e5e7eb;
                                                                border-radius: 6px;
                                                                padding: 10px 12px;
                                                                color: #374151;
                                                                word-break: break-word;
                                                                line-height: 1.5;
                                                            }
                                                            .info-description {
                                                                background: #f9fafb;
                                                                border: 1px solid #e5e7eb;
                                                                border-radius: 6px;
                                                                padding: 12px;
                                                                color: #374151;
                                                                white-space: pre-wrap;
                                                                word-break: break-word;
                                                                line-height: 1.6;
                                                                min-height: 80px;
                                                                max-height: 300px;
                                                                overflow-y: auto;
                                                            }
                                                            .bg-dark-custom {
                                                                background-color: #1e3a5f !important;
                                                            }
                                                            .subtitle-info {
                                                                font-size: 0.85rem;
                                                                color: rgba(255, 255, 255, 0.9);
                                                            }
                                                        </style>
<!-- Script para modal de anulación, debe ir al final para que cargarMovimientos esté definido -->
<?php /* ...existing code... */ ?>
<script>
$(document).ready(function(){
    function formatearMoneda(valor) {
        var numero = parseFloat(valor || 0);
        return '$ ' + numero.toLocaleString('es-NI', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function parsearMetaDesembolso(referencia2) {
        var params = new URLSearchParams(referencia2 || '');
        return {
            fechaDesembolso: params.get('fd') || '-',
            primerPago: params.get('pp') || '-',
            costosLegales: parseFloat(params.get('cl') || 0),
            seguros: parseFloat(params.get('sg') || 0),
            comisiones: parseFloat(params.get('cm') || 0),
            renovacionTotal: parseFloat(params.get('rn') || 0),
            renovacionPrincipal: parseFloat(params.get('rp') || 0),
            renovacionInteresCorriente: parseFloat(params.get('rc') || 0),
            renovacionInteresMora: parseFloat(params.get('rm') || 0)
        };
    }

    function parsearConceptoDesembolso(concepto) {
        var texto = String(concepto || '');
        var resultado = {
            cliente: '-',
            montoCredito: 0,
            tasa: '-',
            plazo: '-'
        };
        var cliente = texto.match(/Cliente:\s*([^|]+)/i);
        var montoCredito = texto.match(/Monto cr[ée]dito:\s*([^|]+)/i);
        var tasa = texto.match(/Tasa:\s*([^|]+)/i);
        var plazo = texto.match(/Plazo:\s*([^|]+)/i);
        if (cliente) resultado.cliente = $.trim(cliente[1]);
        if (montoCredito) resultado.montoCredito = parseFloat(montoCredito[1]) || 0;
        if (tasa) resultado.tasa = $.trim(tasa[1]);
        if (plazo) resultado.plazo = $.trim(plazo[1]);
        return resultado;
    }

    function renderDetalleDesembolso(movimiento) {
        var esDesembolso = String(movimiento.tipo || '') === 'desembolso_preview'
            || String(movimiento.estado || '') === 'previsualizacion'
            || String(movimiento.referencia2 || '').indexOf('p=') === 0;
        var puedeAnularDesembolso = esDesembolso && String(movimiento.estado || '') === 'previsualizacion';
        if (!esDesembolso) {
            $('#desembolsoPreviewPanel').hide();
            $('#btnAnularDesembolsoDesdeModal').hide();
            return;
        }
        var meta = parsearMetaDesembolso(movimiento.referencia2);
        var info = parsearConceptoDesembolso(movimiento.concepto);
        var totalDeducciones = meta.costosLegales + meta.seguros + meta.comisiones + meta.renovacionTotal;
        $('#previewDetalleCliente').text(info.cliente || movimiento.beneficiario || '-');
        $('#previewDetalleMontoCredito').text(formatearMoneda(info.montoCredito));
        $('#previewDetalleTasa').text(info.tasa || '-');
        $('#previewDetallePlazo').text(info.plazo ? info.plazo + ' cuotas' : '-');
        $('#previewDetalleFecha').text(meta.fechaDesembolso || movimiento.fecha_registro_display || movimiento.fecha_registro || '-');
        $('#previewDetallePrimerPago').text(meta.primerPago || '-');
        $('#previewDetalleCostosLegales').text(formatearMoneda(meta.costosLegales));
        $('#previewDetalleSeguros').text(formatearMoneda(meta.seguros));
        $('#previewDetalleComisiones').text(formatearMoneda(meta.comisiones));
        $('#previewDetalleRenovacion').text(formatearMoneda(meta.renovacionTotal));
        $('#previewDetalleRenovPrincipal').text(formatearMoneda(meta.renovacionPrincipal));
        $('#previewDetalleRenovInteresCorriente').text(formatearMoneda(meta.renovacionInteresCorriente));
        $('#previewDetalleRenovInteresMora').text(formatearMoneda(meta.renovacionInteresMora));
        $('#previewDetalleDeducciones').text(formatearMoneda(totalDeducciones));
        $('#previewDetalleNeto').text(formatearMoneda(movimiento.monto_total || 0));
        $('#previewDetalleObservaciones').text(movimiento.descripcion || movimiento.concepto || 'Sin observaciones registradas.');
        $('#desembolsoPreviewPanel').show();
        $('#btnAnularDesembolsoDesdeModal').toggle(puedeAnularDesembolso);
    }

    function capitalizeFirstLetter(text) {
        var str = String(text || '').trim();
        if (!str) return str;
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function renderDetalleMovimientoGeneral(movimiento) {
        var tipo = (movimiento.tipo_transferencia || movimiento.tipo_movimiento || movimiento.tipo || '-');
        $('#previewMovTipo').text(capitalizeFirstLetter(tipo) || '-');
        $('#previewMovFormaPago').text(movimiento.forma_pago || '-');
        $('#previewMovMonto').text(formatearMoneda(movimiento.monto_total || 0));
        $('#previewMovFechaRegistro').text(movimiento.fecha_registro_display || movimiento.fecha_registro || '-');
        $('#previewMovFechaAplicacion').text(movimiento.fecha_aplicacion_display || movimiento.fecha_aplicacion || '-');
        $('#previewMovBeneficiario').text(movimiento.beneficiario || '-');
        $('#previewMovRef1').text(movimiento.referencia1 || '-');
        $('#previewMovRef2').text(movimiento.referencia2 || '-');
        $('#previewMovDescripcion').text(movimiento.descripcion || movimiento.concepto || 'Sin descripción.');
    }

    // Modal anulación

    // Modal anulación
    $(document).on('click', '.btnAnularMov', function(){
        var id = $(this).data('id');
        $('#anularMovId').val(id);
        $('#motivoAnulacion').val('');
        $('#modalAnularMov').modal('show');
    });

    $(document).on('click', '.btnVerCheque', function(){
        var id = $(this).data('id');
        var preview = String($(this).data('preview') || '') === '1';
        $('#verChequeId').val(id || '');
        $.get('<?php echo site_url('tesoreria/get_movimiento_ajax'); ?>', {id: id}, function(resp){
            var j = (typeof resp === 'object') ? resp : JSON.parse(resp);
            if (j && j.status && j.movimiento) {
                var mov = j.movimiento;
                var esCheque = String(mov.forma_pago || '').toUpperCase() === 'CHEQUE'
                    || String(mov.tipo_movimiento || '').toLowerCase() === 'cheque'
                    || preview;
                var esDesembolso = String(mov.tipo || '') === 'desembolso_preview'
                    || String(mov.estado || '') === 'previsualizacion'
                    || String(mov.referencia2 || '').indexOf('p=') === 0;

                $('#movimientoPreviewPanel').hide();
                $('#desembolsoPreviewPanel').hide();
                $('#btnAnularDesembolsoDesdeModal').hide();
                $('#btnEjecutarDesembolsoPreview').hide();
                $('#btnImprimirCheque').hide();
                $('#chequePreviewPanel').hide();

                if (esCheque || esDesembolso) {
                    $('#iframeChequePreview').attr('src', '<?php echo site_url('tesoreria/imprimir_cheque'); ?>/' + id + '?preview=1');
                    $('#chequePreviewPanel').show();
                    $('#btnImprimirCheque').show();
                    $('#btnEjecutarDesembolsoPreview').toggle(preview);
                    $('#modalVerChequeLabel').text(preview ? 'Solicitud de Desembolso' : 'Visualizar Cheque');
                } else {
                    $('#iframeChequePreview').attr('src', 'about:blank');
                    $('#movimientoPreviewPanel').show();
                    $('#modalVerChequeLabel').text('Visualizar Movimiento');
                    renderDetalleMovimientoGeneral(mov);
                }

                if (esDesembolso) {
                    renderDetalleDesembolso(mov);
                }
            } else {
                $('#movimientoPreviewPanel').hide();
                $('#desembolsoPreviewPanel').hide();
                $('#btnAnularDesembolsoDesdeModal').hide();
                $('#btnEjecutarDesembolsoPreview').hide();
                $('#btnImprimirCheque').hide();
                $('#chequePreviewPanel').hide();
            }
        }).fail(function(){
            $('#movimientoPreviewPanel').hide();
            $('#desembolsoPreviewPanel').hide();
            $('#btnAnularDesembolsoDesdeModal').hide();
            $('#btnEjecutarDesembolsoPreview').hide();
            $('#btnImprimirCheque').hide();
            $('#chequePreviewPanel').hide();
        });
        $('#modalVerCheque').modal('show');
    });

    $(document).on('click', '#btnAnularDesembolsoDesdeModal', function(){
        var id = $('#verChequeId').val();
        if(!id){
            alert('No se encontró el movimiento para anular.');
            return;
        }
        $('#modalVerCheque').modal('hide');
        $('#anularMovId').val(id);
        $('#motivoAnulacion').val('');
        $('#modalAnularMov').modal('show');
    });

    $(document).on('click', '.btnVerInfo', function(){
        var id = $(this).data('id');
        $.get('<?php echo site_url('tesoreria/get_movimiento_ajax'); ?>', {id: id}, function(resp){
            var j = (typeof resp === 'object') ? resp : JSON.parse(resp);
            if (j && j.status && j.movimiento) {
                var mov = j.movimiento;
                $('#infoRef1').text(mov.referencia1 || '-');
                $('#infoRef2').text(mov.referencia2 || '-');
                
                var desc = mov.descripcion || mov.concepto || 'Sin descripción';
                if (mov.tipo === 'desembolso_preview' && desc.indexOf('Inicio crédito:') === 0) {
                    desc = 'Solicitud de desembolso';
                    if (mov.referencia1) {
                        desc += ' (' + mov.referencia1 + ')';
                    }
                }
                $('#infoDesc').text(desc);
                $('#subtituloInfo').text('ID: ' + (mov.id || '-') + ' | Beneficiario: ' + (mov.beneficiario || '-'));
                $('#modalVerInfo').modal('show');
            } else {
                alert('No se encontraron los datos del movimiento.');
            }
        }).fail(function(){
            alert('Error al obtener los datos del movimiento.');
        });
    });

    $(document).on('click', '#btnEjecutarDesembolsoPreview', function(){
        var id = $('#verChequeId').val();
        if(!id){
            alert('No se encontró la solicitud de desembolso.');
            return;
        }
        if(!confirm('¿Desea ejecutar el desembolso y entregar el cheque?')){
            return;
        }
        $.post('<?php echo site_url('tesoreria/finalizar_desembolso_preview_ajax'); ?>', {id: id}, function(resp){
            var j = (typeof resp === 'object') ? resp : JSON.parse(resp);
            if(j && j.status){
                $('#modalVerCheque').modal('hide');
                cargarMovimientos();
                if (typeof cargarDesembolsos === 'function') {
                    cargarDesembolsos();
                }
                alert('Desembolso ejecutado correctamente.');
            } else {
                alert('Error: ' + (j && j.message ? j.message : 'No se pudo ejecutar el desembolso'));
            }
        }, 'json').fail(function(){
            alert('Error al procesar la solicitud.');
        });
    });

    $('#btnImprimirCheque').on('click', function(){
        var id = $('#verChequeId').val();
        if(!id){
            alert('No se encontró el cheque para imprimir.');
            return;
        }
        var frame = document.getElementById('iframeChequePreview');
        if(frame && frame.contentWindow){
            frame.contentWindow.focus();
            frame.contentWindow.print();
            return;
        }
        window.open('<?php echo site_url('tesoreria/imprimir_cheque'); ?>/' + id, '_blank');
    });

    // Modal asiento contable (Contabilizar)
    $(document).on('click', '.btnContabilizarMov', function(){
        var id = $(this).data('id');
        window.CONTABILIZAR_MOV_ID = id;
        // Obtener datos del movimiento por AJAX
        $.get('<?php echo site_url('tesoreria/get_movimiento_ajax'); ?>', {id: id}, function(resp){
            var j = (typeof resp === 'object')? resp : JSON.parse(resp);
            if(j && j.status && j.movimiento){
                var modalContainer = document.getElementById('modalContainer');
                if (!modalContainer) {
                    modalContainer = document.createElement('div');
                    modalContainer.id = 'modalContainer';
                    document.body.appendChild(modalContainer);
                }

                if (j.movimiento.journal_id) {
                    // Mostrar asiento existente
                    $.get('<?php echo site_url('contabilidad/modal_view'); ?>', {id: j.movimiento.journal_id}, function(html){
                        modalContainer.innerHTML = html;
                    });
                } else {
                    // Cargar modal de asiento contable para crear uno nuevo
                    $.get('<?php echo site_url('contabilidad/modal_add'); ?>', function(html){
                        var $modal = $(html);
                        // Asegurar que el ID esté disponible para el modal
                        window.CONTABILIZAR_MOV_ID = id;
                        // Prellenar campos principales
                        $modal.find('input[name="date"]').val(j.movimiento.fecha_registro);
                        $modal.find('textarea[name="description"]').val(j.movimiento.descripcion || '');
                        // Mostrar modal
                        modalContainer.innerHTML = '';
                        modalContainer.appendChild($modal[0]);
                        // Inicializar Select2 y AJAX para cuentas contables
                        if (typeof attachModalEvents === 'function') {
                            attachModalEvents();
                        }
                        // Cerrar modal al hacer clic en la X o fondo
                        $modal.find('#btnCancelModal').on('click', function(){ modalContainer.innerHTML = ''; });
                        $modal.on('click', function(e){ if(e.target === this) modalContainer.innerHTML = ''; });
                    });
                }
            }else{
                alert('No se pudo obtener el movimiento para contabilizar.');
            }
        });
    });

    $('#btnConfirmarAnular').on('click', function(){
        var id = $('#anularMovId').val();
        var motivo = $('#motivoAnulacion').val().trim();
        if(!motivo){
            $('#motivoAnulacion').focus();
            return;
        }
        // Llamar backend para anular
        $.post('<?php echo site_url('tesoreria/anular_movimiento_ajax'); ?>', {id: id, motivo: motivo}, function(resp){
            var j = (typeof resp === 'object')? resp : JSON.parse(resp);
            if(j && j.status){
                $('#modalAnularMov').modal('hide');
                cargarMovimientos();
                // Si hay asiento relacionado, guardar en localStorage para resaltar en Diario
                if(j.asiento_id){
                    localStorage.setItem('highlightDiarioAsiento', j.asiento_id);
                }
                alert('Movimiento anulado correctamente.');
            }else{
                alert('Error al anular movimiento: '+(j && j.msg ? j.msg : 'Error desconocido'));
            }
        });
    });
});
</script>
<!-- Script para búsqueda y selección de cuentas contables (igual que diario) -->
<script src="<?php echo base_url('public/js/contabilidad_modal_enhanced.js'); ?>?v=<?php echo time(); ?>"></script>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <?php $this->load->view('tesoreria/partial_back'); ?>

            <div class="row">
                        <div class="card-body col-12">
                            <div class="btn-group mb-3 movimientos-toolbar" role="group" aria-label="Tipos de movimiento">
                                <button class="btn btn-outline-primary" id="btnMovTransferencia"><i class="fas fa-exchange-alt"></i> Transferencia</button>
                                <button class="btn btn-outline-success" id="btnMovEfectivo"><i class="fas fa-money-bill-wave"></i> Efectivo</button>
                                <button class="btn btn-outline-info" id="btnMovCheque"><i class="fas fa-money-check"></i> Cheque</button>
                                <button class="btn btn-outline-warning" id="btnMovTraslado"><i class="fas fa-random"></i> Traslado entre cuentas</button>
                                <button class="btn btn-outline-secondary" id="btnMovOtros"><i class="fas fa-ellipsis-h"></i> Otros</button>
                            </div>
                            <input type="hidden" id="movimientosContext" value="<?php echo isset($doc_context) ? htmlspecialchars($doc_context) : ''; ?>" />
                            <style>
                            .movimientos-toolbar {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 8px;
                            }
                            .movimientos-toolbar .btn {
                                flex: 1 1 180px;
                                min-width: 0;
                                font-weight: 600;
                                border-radius: 10px !important;
                                font-size: .84rem;
                                letter-spacing: .1px;
                            }
                            .movimientos-filtros {
                                margin-bottom: 14px;
                                padding: 14px 16px 10px;
                                border: 1px solid #e5e7eb;
                                border-radius: 14px;
                                background: linear-gradient(180deg, #fbfdff 0%, #f4f7fb 100%);
                                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
                            }
                            .movimientos-filtros label {
                                font-size: 12px;
                                font-weight: 700;
                                color: #475569;
                                margin-bottom: 6px;
                            }
                            .movimientos-filtros .form-control,
                            .movimientos-filtros .btn {
                                height: 38px;
                                border-radius: 10px;
                            }
                            .movimientos-table-wrap {
                                margin-top: 14px;
                                border: 1px solid #e5e7eb;
                                border-radius: 14px;
                                background: #fff;
                                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
                                overflow: hidden;
                            }
                            .movimientos-table-scroll {
                                width: 100%;
                                overflow-x: auto;
                                overflow-y: hidden;
                            }
                            .movimientos-title-soft {
                                color: #163c61;
                                font-weight: 700;
                                letter-spacing: .2px;
                            }
                            @media (max-width: 767px) {
                                .movimientos-toolbar .btn { flex-basis: calc(50% - 8px); font-size: 0.95rem; }
                            }
                            </style>
                            <!-- Filtros -->
                            <div class="row movimientos-filtros">
                                <div class="col-md-3">
                                    <label>Desde</label>
                                    <input type="date" id="filtroDesde" class="form-control form-control-sm" />
                                </div>
                                <div class="col-md-3">
                                    <label>Hasta</label>
                                    <input type="date" id="filtroHasta" class="form-control form-control-sm" />
                                </div>
                                <div class="col-md-3">
                                    <label>Tipo de documento</label>
                                    <select id="filtroTipoDoc" class="form-control form-control-sm">
                                        <option value="">Todos</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="traslado">Traslado</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button id="btnFiltrarMov" class="btn btn-primary btn-block">Filtrar</button>
                                </div>
                            </div>
                            <!-- Tabla de movimientos -->
                            <div class="movimientos-table-wrap">
                                <div class="movimientos-table-scroll">
                                <style>
                                    /* Compact table style aligned with solicitudes/desembolsos */
                                    #tablaMovimientos {
                                        width: 100%;
                                        min-width: 920px;
                                        margin-bottom: 0;
                                        table-layout: auto;
                                    }
                                    #tablaMovimientos td,
                                    #tablaMovimientos th {
                                        padding: .42rem .5rem;
                                        vertical-align: middle;
                                        font-size: .82rem;
                                        line-height: 1.25;
                                        white-space: normal;
                                        word-break: break-word;
                                        border-color: #e6edf5;
                                        min-width: 0;
                                    }
                                    #tablaMovimientos thead th {
                                        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
                                        color: #1f2937;
                                        font-weight: 700;
                                        font-size: .82rem;
                                        padding: .55rem .5rem;
                                        border-bottom: 1px solid #dbe3ef;
                                    }
                                    #tablaMovimientos tbody tr:nth-child(even) td {
                                        background: #fbfdff;
                                    }
                                    #tablaMovimientos tbody tr:hover td {
                                        background: #f4f8ff;
                                    }
                                    #tablaMovimientos tbody tr.row-anulado td {
                                        background: #fff6f7 !important;
                                        color: #8a4750;
                                    }
                                    #tablaMovimientos tbody tr.row-preview td {
                                        background: #fff9ef !important;
                                    }
                                    #tablaMovimientos th:nth-child(1),
                                    #tablaMovimientos td:nth-child(1) {
                                        width: 40px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(2),
                                    #tablaMovimientos td:nth-child(2) {
                                        min-width: 110px;
                                        max-width: 160px;
                                    }
                                    #tablaMovimientos th:nth-child(3),
                                    #tablaMovimientos td:nth-child(3) {
                                        width: 75px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(4),
                                    #tablaMovimientos td:nth-child(4) {
                                        min-width: 100px;
                                        max-width: 130px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(5),
                                    #tablaMovimientos td:nth-child(5),
                                    #tablaMovimientos th:nth-child(6),
                                    #tablaMovimientos td:nth-child(6) {
                                        width: 90px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(7),
                                    #tablaMovimientos td:nth-child(7) {
                                        min-width: 110px;
                                    }
                                    #tablaMovimientos th:nth-child(8),
                                    #tablaMovimientos td:nth-child(8) {
                                        width: 90px;
                                        text-align: right;
                                    }
                                    #tablaMovimientos th:nth-child(9),
                                    #tablaMovimientos td:nth-child(9) {
                                        min-width: 110px;
                                        max-width: 160px;
                                    }
                                    /* Ocultar columnas de Referencias */
                                    #tablaMovimientos th:nth-child(10),
                                    #tablaMovimientos td:nth-child(10),
                                    #tablaMovimientos th:nth-child(11),
                                    #tablaMovimientos td:nth-child(11) {
                                        display: none;
                                    }
                                    #tablaMovimientos th:nth-child(12),
                                    #tablaMovimientos td:nth-child(12) {
                                        display: none;
                                    }
                                    #tablaMovimientos th:nth-child(13),
                                    #tablaMovimientos td:nth-child(13) {
                                        min-width: 120px;
                                        max-width: 150px;
                                    }
                                    #tablaMovimientos .mov-ref-cell {
                                        white-space: normal;
                                        overflow-wrap: break-word;
                                        word-break: break-word;
                                        max-width: none;
                                    }
                                    #tablaMovimientos .mov-desc-cell {
                                        overflow: visible;
                                        text-overflow: unset;
                                        white-space: normal;
                                        display: none !important;
                                        min-width: 0;
                                        word-break: break-word;
                                        line-height: 1.3;
                                    }
                                    #tablaMovimientos .mov-user-cell {
                                        white-space: normal;
                                        overflow: visible;
                                        text-overflow: unset;
                                        word-break: break-word;
                                        line-height: 1.2;
                                    }
                                    #tablaMovimientos .acciones-cell {
                                        display: flex;
                                        flex-direction: column;
                                        gap: 6px;
                                        align-items: stretch;
                                        white-space: normal;
                                        justify-content: flex-start;
                                        min-width: 110px;
                                        max-width: 180px;
                                        overflow: visible;
                                    }
                                    #tablaMovimientos .acciones-cell .btn {
                                        padding: .28rem .36rem;
                                        font-size: .72rem;
                                        margin: 0;
                                        min-width: 0;
                                        width: 100%;
                                        border-radius: 4px;
                                        font-weight: 600;
                                        border-width: 1px;
                                        white-space: normal;
                                        text-align: center;
                                        line-height: 1;
                                    }
                                    #tablaMovimientos .texto-suave {
                                        color: #475569;
                                    }
                                    #tablaMovimientos .badge {
                                        font-size: .72rem;
                                        font-weight: 600;
                                        padding: .22rem .48rem;
                                        border-radius: 999px;
                                    }
                                    #tablaMovimientos .mov-badge {
                                        border: 1px solid #d8e2ef;
                                        background: #f8fbff;
                                        color: #284b74;
                                    }
                                    #tablaMovimientos .mov-badge-abono {
                                        border: 1px solid #cfe8d7;
                                        background: #edf8f0;
                                        color: #1f6a3f;
                                    }
                                    #tablaMovimientos .mov-badge-cargo {
                                        border: 1px solid #f2d3d9;
                                        background: #fdf0f3;
                                        color: #9b3047;
                                    }
                                    #tablaMovimientos .mov-badge-preview {
                                        border: 1px solid #f0dfbe;
                                        background: #fff8ec;
                                        color: #8a651b;
                                    }
                                    #tablaMovimientos .mov-badge-anulado {
                                        border: 1px solid #eac8cf;
                                        background: #fbecef;
                                        color: #9f2741;
                                    }
                                    #tablaMovimientos .mov-forma-pago-cell {
                                        white-space: nowrap;
                                        word-break: normal;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                        min-width: 100px;
                                        max-width: 140px;
                                    }
                                    #tablaMovimientos th:nth-child(4),
                                    #tablaMovimientos td.mov-forma-pago-cell {
                                        width: 120px;
                                    }
                                    #tablaMovimientos th:nth-child(5),
                                    #tablaMovimientos th:nth-child(6),
                                    #tablaMovimientos td:nth-child(5),
                                    #tablaMovimientos td:nth-child(6) {
                                        width: 90px;
                                        white-space: nowrap;
                                    }
                                    #tablaMovimientos .btn-mov-view {
                                        background: #e9f7fb;
                                        color: #17637a;
                                        border-color: #bee8f3;
                                    }
                                    #tablaMovimientos .btn-mov-info {
                                        background: #f0f1ff;
                                        color: #4c5fd5;
                                        border-color: #dfe1f5;
                                    }
                                    #tablaMovimientos .btn-mov-anular {
                                        background: #fff1f3;
                                        color: #a2384f;
                                        border-color: #f5ccd5;
                                    }
                                    #tablaMovimientos .btn-mov-conta {
                                        background: #eef8ef;
                                        color: #256f3a;
                                        border-color: #cce9d1;
                                    }
                                    #tablaMovimientos .btn-mov-muted {
                                        background: #f1f5f9;
                                        color: #64748b;
                                        border-color: #d7e0ea;
                                    }
                                    @media (max-width: 767.98px) {
                                        .movimientos-filtros {
                                            padding-left: 12px;
                                            padding-right: 12px;
                                        }
                                        #tablaMovimientos td,
                                        #tablaMovimientos th {
                                            font-size: .82rem;
                                        }
                                        #tablaMovimientos .acciones-cell {
                                            /* keep column layout on small screens */
                                            flex-direction: column;
                                        }
                                        #tablaMovimientos .acciones-cell .btn {
                                            width: 100%;
                                            min-width: 0;
                                        }
                                    }
                                </style>
                                <table class="table table-sm table-striped table-bordered table-hover table-compact" id="tablaMovimientos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cuenta</th>
                                            <th>Tipo</th>
                                            <th>Forma de pago</th>
                                            <th>Fecha registro</th>
                                            <th>Fecha aplicación</th>
                                            <th>Beneficiario</th>
                                            <th class="text-right">Monto</th>
                                            <th>Ejecutado por</th>
                                            <th style="display:none;">Referencia 1</th>
                                            <th style="display:none;">Referencia 2</th>
                                            <th style="display:none;">Descripción</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top bg-white" id="movimientosPaginationBar">
                                    <div id="movimientosPaginationInfo" class="small text-muted">Mostrando 0 registros</div>
                                    <div class="btn-group" role="group" aria-label="Paginación movimientos">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPagePrev" disabled>Anterior</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPageNext" disabled>Siguiente</button>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para registrar movimiento -->
            <div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="max-width:95vw; width:95vw; margin: 15px auto;">
                    <div class="modal-content" style="border-radius:8px; box-shadow: 0 5px 25px rgba(0,0,0,0.5); max-height:95vh; overflow-y: auto;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border-radius:8px 8px 0 0; padding:20px; position:sticky; top:0; z-index:100;">
                            <div>
                                <h5 class="modal-title text-white" id="modalMovimientoLabel" style="font-size:20px; margin-bottom:4px;"><i class="fas fa-exchange-alt"></i> Registrar Transferencia</h5>
                                <small class="text-white" style="opacity:0.9;">Ingrese los detalles de la transferencia y el desglose contable completo</small>
                            </div>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body" style="padding:24px; background-color:#fafbfc;">
                            <form id="formMovimiento">
                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-sign-out-alt"></i> Cuenta de origen <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg" name="cuenta_id" id="selectCuentaOrigen" required style="border-color:#e9ecef; border-width:2px;">
                                            <option value="">Seleccione...</option>
                                            <?php if(isset($cuentas) && is_array($cuentas)) foreach($cuentas as $c): ?>
                                                <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?> (<?php echo $c->code; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-arrow-right"></i> Tipo de transferencia <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg" name="tipo_transferencia" required style="border-color:#e9ecef; border-width:2px;">
                                            <option value="">Seleccione...</option>
                                            <option value="cargo">Cargo (Egreso)</option>
                                            <option value="abono">Abono (Ingreso)</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-credit-card"></i> Forma de pago</label>
                                        <input type="text" class="form-control form-control-lg" name="forma_pago" value="TRANSFERENCIA" readonly style="border-color:#e9ecef; border-width:2px; background-color:#f8f9fa;">
                                    </div>
                                </div>
                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-3">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-calendar"></i> Fecha de registro</label>
                                        <input type="date" class="form-control form-control-lg" name="fecha_registro" required style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-calendar-check"></i> Fecha de aplicación</label>
                                        <input type="date" class="form-control form-control-lg" name="fecha_aplicacion" required style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-user"></i> Beneficiario</label>
                                        <input type="text" class="form-control form-control-lg" name="beneficiario" style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                </div>
                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-hashtag"></i> Referencia 1</label>
                                        <input type="text" class="form-control form-control-lg" name="referencia1" style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-hashtag"></i> Referencia 2</label>
                                        <input type="text" class="form-control form-control-lg" name="referencia2" style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-money-bill"></i> Monto total</label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="monto_total" required style="border-color:#e9ecef; border-width:2px;">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-info-circle"></i> Previsualización</label>
                                        <div id="preview_movimiento_tipo" class="alert alert-info" style="margin: 0; padding: 8px 12px; border-width:2px;">
                                            Selecciona un concepto y monto
                                        </div>
                                    </div>
                                </div>
                                <!-- Departamento, Centro de costos y Proyecto ocultos y enviados como null -->
                                <input type="hidden" name="departamento" value="" />
                                <input type="hidden" name="centro_costos" value="" />
                                <input type="hidden" name="proyecto" value="" />
                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-9">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-align-left"></i> Descripción</label>
                                        <textarea class="form-control" name="descripcion" rows="3" style="border-color:#e9ecef; border-width:2px; font-size:14px;"></textarea>
                                    </div>
                                    <div class="form-group col-md-3 d-flex align-items-end justify-content-end">
                                        <button type="button" class="btn btn-lg btn-primary" id="btnAddGastoMovimiento" style="border-radius:6px; min-width:180px;"><i class="fas fa-plus"></i> Agregar costo</button>
                                    </div>
                                </div>
                                <div class="card card-warning mt-3 mb-3" id="movimientoTipoCambioSection" style="border-color:#ffc107; box-shadow: 0 2px 4px rgba(0,0,0,0.08); display:none;">
                                    <div class="card-header p-3" style="background-color:#fff3cd; border-bottom:1px solid #ffc107;">
                                        <strong style="font-size:14px; color:#856404;"><i class="fas fa-exchange-alt"></i> Tipo de Cambio y Monto Equivalente</strong>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label style="font-weight:600; font-size:13px; color:#555;">Monto Total</label>
                                                <div style="background-color:#f8f9fa; border:1px solid #e9ecef; padding:10px; border-radius:4px; font-weight:600; font-size:16px; text-align:center; color:#0c5460;">
                                                    <span id="movimientoMontoDisplay">0.00</span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label style="font-weight:600; font-size:13px; color:#555;">Tipo de Cambio (NIO/USD)</label>
                                                <input type="number" step="0.01" min="0" class="form-control" id="movimientoTipoCambio" value="36.50" style="font-weight:600; font-size:16px; text-align:center; border-color:#ffc107; background-color:#fffbeb;" />
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label style="font-weight:600; font-size:13px; color:#555;">Monto Equivalente</label>
                                                <div style="background-color:#f8f9fa; border:1px solid #e9ecef; padding:10px; border-radius:4px; font-weight:600; font-size:16px; text-align:center; color:#0c5460;">
                                                    <span id="movimientoMontoEquivalente">0.00</span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label style="font-weight:600; font-size:13px; color:#555;">Tasa Guardada</label>
                                                <input type="hidden" id="movimientoTasaGuardada" value="1.0000" />
                                                <div style="background-color:#f8f9fa; border:1px solid #e9ecef; padding:10px; border-radius:4px; font-weight:600; font-size:14px; text-align:center; color:#666;">
                                                    <span id="movimientoTasaDisplay">1.0000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-light mt-2" id="movimientoGastosSection" style="border-color:#6c757d; box-shadow: 0 2px 4px rgba(0,0,0,0.08); min-height:150px; display:none;">
                                    <div class="card-header p-3 bg-white" style="border-bottom:1px solid #dee2e6;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong style="font-size:16px;"><i class="fas fa-receipt"></i> Costos Adicionales</strong>
                                                <div class="small text-muted" style="margin-top:4px;">Agrega costos opcionales que afectan el asiento contable.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0" id="movimientoGastosTable" style="min-width:560px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60%;">Descripción del costo</th>
                                                        <th style="width:25%; text-align:right;">Monto</th>
                                                        <th style="width:15%; text-align:center;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th class="text-right">Total costos:</th>
                                                        <th class="text-right"><span id="movimientoTotalCostos">0.00</span></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-info mt-4" id="asientoDesgloseSection" style="display:none; border-color:#17a2b8; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div class="card-header p-3 bg-info text-white">
                                        <strong style="font-size:16px;"><i class="fas fa-list-ul"></i> Desglose Contable</strong>
                                        <div class="small" style="margin-top:4px; opacity:0.95;">Detalle de líneas del asiento contable para esta transferencia</div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                                            <table class="table table-striped table-hover mb-0" id="asientoDesgloseTable" style="font-size:13px; min-width:1200px; white-space:nowrap;">
                                                <thead style="background-color:#f8f9fa; position: sticky; top: 0; z-index:10;">
                                                    <tr>
                                                        <th style="width:250px; min-width:250px; vertical-align:middle;"><i class="fas fa-book"></i> Cuenta contable</th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#fff3cd; color:#856404; font-weight:bold;"><small>Debe<br/>(NIO)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#cfe2ff; color:#084298; font-weight:bold;"><small>Debe<br/>(USD)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#d1e7dd; color:#0f5132; font-weight:bold;"><small>Haber<br/>(NIO)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#d1f2eb; color:#0a5345; font-weight:bold;"><small>Haber<br/>(USD)</small></th>
                                                        <th style="width:300px; min-width:300px;"><i class="fas fa-align-left"></i> Descripción</th>
                                                        <th style="width:50px; min-width:50px; text-align:center;"><i class="fas fa-trash"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot style="background-color:#f8f9fa; font-weight:bold; border-top:3px solid #dee2e6; position:sticky; bottom:0; z-index:9;">
                                                    <tr>
                                                        <td style="vertical-align:middle; padding:12px;"><strong>TOTALES:</strong></td>
                                                        <td style="text-align:center; background-color:#fff3cd; padding:12px;"><span id="asientoTotalDebeNio" style="font-size:14px; color:#d39e00; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#cfe2ff; padding:12px;"><span id="asientoTotalDebeUsd" style="font-size:14px; color:#0c63e4; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#d1e7dd; padding:12px;"><span id="asientoTotalHaberNio" style="font-size:14px; color:#198754; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#d1f2eb; padding:12px;"><span id="asientoTotalHaberUsd" style="font-size:14px; color:#0dcaf0; font-weight:bold;">0.00</span></td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer p-4 bg-white" style="border-top:2px solid #dee2e6;">
                                        <button type="button" class="btn btn-primary" id="btnAddAsientoLinea" style="border-radius:6px; padding:10px 20px; font-weight:600; background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border:none; box-shadow:0 2px 4px rgba(13,110,253,0.3);">
                                            <i class="fas fa-plus-circle"></i> Agregar Línea de Asiento
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer" style="background-color:#f8f9fa; border-top:2px solid #dee2e6; padding:16px; border-radius:0 0 8px 8px; position:sticky; bottom:0; z-index:100;">
                            <button type="button" class="btn btn-light" data-dismiss="modal" style="min-width:100px; border-color:#dee2e6;"><i class="fas fa-times"></i> Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnGuardarMovimiento" style="min-width:150px; background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border:none; color:white;"><i class="fas fa-check-circle"></i> Guardar Transferencia</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Traslado entre Cuentas Bancarias -->
            <div class="modal fade" id="modalTraslado" tabindex="-1" role="dialog" aria-labelledby="modalTrasladoLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="max-width:90vw; width:90vw; margin: 30px auto;">
                    <div class="modal-content" style="border-radius:8px; box-shadow: 0 5px 20px rgba(0,0,0,0.4); max-height:90vh; overflow-y: auto;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border-radius:8px 8px 0 0; padding:20px;">
                            <div>
                                <h5 class="modal-title text-white" id="modalTrasladoLabel" style="font-size:20px; margin-bottom:4px;"><i class="fas fa-exchange-alt"></i> Traslado entre Cuentas Bancarias</h5>
                                <small class="text-white" style="opacity:0.9;">Registre movimientos de dinero entre cuentas con desglose contable</small>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body" style="padding:24px; background-color:#fafbfc;">
                            <form id="formTraslado">
                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-sign-out-alt"></i> Cuenta de Origen <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg" id="trasladoCuentaOrigen" required style="border-color:#e9ecef; border-width:2px;">
                                            <option value="">Seleccione cuenta...</option>
                                        </select>
                                        <small class="text-muted"><strong>Moneda:</strong> <span id="trasladoMonedaOrigen" style="font-weight:600; color:#495057;">-</span></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-sign-in-alt"></i> Cuenta de Destino <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg" id="trasladoCuentaDestino" required style="border-color:#e9ecef; border-width:2px;">
                                            <option value="">Seleccione cuenta...</option>
                                        </select>
                                        <small class="text-muted"><strong>Moneda:</strong> <span id="trasladoMonedaDestino" style="font-weight:600; color:#495057;">-</span></small>
                                    </div>
                                </div>

                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-money-bill"></i> Monto <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" step="0.01" class="form-control" id="trasladoMonto" placeholder="0.00" required style="border-color:#e9ecef; border-width:2px;" />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="trasladoMonedaInput" style="background-color:#fff3cd; border-color:#e9ecef; font-weight:600;">NIO</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-percentage"></i> Comisión Bancaria</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" step="0.01" class="form-control" id="trasladoComision" placeholder="0.00" style="border-color:#e9ecef; border-width:2px;" />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="trasladoMonedaComision" style="background-color:#fff3cd; border-color:#e9ecef; font-weight:600;">NIO</span>
                                            </div>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle"></i> Se añadirá al monto total</small>
                                    </div>
                                </div>

                                <div class="form-row" id="trasladoConversionGroup" style="display:none; margin-bottom:20px;">
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-arrow-up"></i> Tipo de Cambio Venta</label>
                                        <input type="number" step="0.0001" class="form-control form-control-lg" id="trasladoTcVenta" value="36.50" style="border-color:#e9ecef; border-width:2px;" />
                                        <small class="text-muted">NIO/USD</small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-arrow-down"></i> Tipo de Cambio Compra</label>
                                        <input type="number" step="0.0001" class="form-control form-control-lg" id="trasladoTcCompra" value="36.50" style="border-color:#e9ecef; border-width:2px;" />
                                        <small class="text-muted">NIO/USD</small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-exchange-alt"></i> Usar TC</label>
                                        <select class="form-control form-control-lg" id="trasladoTcSelector" style="border-color:#e9ecef; border-width:2px;">
                                            <option value="venta">Venta</option>
                                            <option value="compra">Compra</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6" id="trasladoConversionDestino" style="display:none;">
                                        <label>Monto Equivalente en Destino</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="trasladoMontoEquivalente" placeholder="0.00" readonly />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="trasladoMonedaEquivalente">USD</span>
                                            </div>
                                        </div>
                                        <small class="text-muted"><strong>TC:</strong> <span id="trasladoTasaCambio" style="font-weight:600; color:#495057;">36.50</span></small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-key"></i> ID de Transación</label>
                                        <input type="text" class="form-control form-control-lg" id="trasladoIdTransaccion" placeholder="Ej: TRF20260610001234" style="border-color:#e9ecef; border-width:2px;" />
                                        <small class="text-muted">ID proporcionado por el banco</small>
                                    </div>
                                </div>

                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-calendar"></i> Fecha de Registro</label>
                                        <input type="date" class="form-control form-control-lg" id="trasladoFechaRegistro" required style="border-color:#e9ecef; border-width:2px;" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-calendar-check"></i> Fecha de Aplicación</label>
                                        <input type="date" class="form-control form-control-lg" id="trasladoFechaAplicacion" required style="border-color:#e9ecef; border-width:2px;" />
                                    </div>
                                </div>

                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-12">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-heading"></i> Concepto/Referencia</label>
                                        <input type="text" class="form-control form-control-lg" id="trasladoConcepto" placeholder="Ej: Consolidación de fondos" style="border-color:#e9ecef; border-width:2px;" />
                                    </div>
                                </div>

                                <div class="form-row" style="margin-bottom:20px;">
                                    <div class="form-group col-md-12">
                                        <label style="font-weight:600; color:#333;"><i class="fas fa-align-left"></i> Descripción</label>
                                        <textarea class="form-control" id="trasladoDescripcion" rows="3" placeholder="Detalles del traslado..." style="border-color:#e9ecef; border-width:2px; font-size:14px;"></textarea>
                                    </div>
                                </div>

                                <div class="form-row" style="margin-bottom:15px;">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnAddTrasladoGastoLineaTop" style="border-radius:6px;"><i class="fas fa-plus"></i> Agregar gasto</button>
                                    </div>
                                </div>

                                <div class="card card-light mt-2" id="trasladoGastosSection" style="border-color:#6c757d; box-shadow: 0 2px 4px rgba(0,0,0,0.08); min-height:150px;">
                                    <div class="card-header p-3 bg-white" style="border-bottom:1px solid #dee2e6;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong style="font-size:16px;"><i class="fas fa-receipt"></i> Gastos adicionales</strong>
                                                <div class="small text-muted" style="margin-top:4px;">Agrega costos extra que afectan el origen, pero no aumentan el monto neto transferido.</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddTrasladoGastoLinea" style="border-radius:6px;"><i class="fas fa-plus"></i> Agregar gasto</button>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0" id="trasladoGastosTable" style="min-width:560px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60%;">Categoría del gasto</th>
                                                        <th style="width:25%; text-align:right;">Monto</th>
                                                        <th style="width:15%; text-align:center;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th class="text-right">Total gastos:</th>
                                                        <th class="text-right"><span id="trasladoTotalCostos">0.00</span></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-info mt-4" id="trasladoAsientoDesgloseSection" style="border-color:#17a2b8; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div class="card-header p-3 bg-info text-white">
                                        <strong style="font-size:16px;"><i class="fas fa-list-ul"></i> Desglose Contable</strong>
                                        <div class="small" style="margin-top:4px; opacity:0.95;">Detalle de líneas del asiento contable para este traslado</div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                                            <table class="table table-striped table-hover mb-0" id="trasladoAsientoDesgloseTable" style="font-size:13px; min-width:1200px; white-space:nowrap;">
                                                <thead style="background-color:#f8f9fa; position: sticky; top: 0; z-index:10;">
                                                    <tr>
                                                        <th style="width:250px; min-width:250px; vertical-align:middle;"><i class="fas fa-book"></i> Cuenta Contable</th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#fff3cd; color:#856404; font-weight:bold;"><small>Debe<br/>(NIO)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#cfe2ff; color:#084298; font-weight:bold;"><small>Debe<br/>(USD)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#d1e7dd; color:#0f5132; font-weight:bold;"><small>Haber<br/>(NIO)</small></th>
                                                        <th style="width:110px; min-width:110px; text-align:center; background-color:#d1f2eb; color:#0a5345; font-weight:bold;"><small>Haber<br/>(USD)</small></th>
                                                        <th style="width:300px; min-width:300px;"><i class="fas fa-align-left"></i> Descripción</th>
                                                        <th style="width:50px; min-width:50px; text-align:center;"><i class="fas fa-trash"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot style="background-color:#f8f9fa; font-weight:bold; border-top:3px solid #dee2e6; position:sticky; bottom:0; z-index:9;">
                                                    <tr>
                                                        <td style="vertical-align:middle; padding:12px;"><strong>TOTALES:</strong></td>
                                                        <td style="text-align:center; background-color:#fff3cd; padding:12px;"><span id="trasladoTotalDebeNio" style="font-size:14px; color:#d39e00; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#cfe2ff; padding:12px;"><span id="trasladoTotalDebeUsd" style="font-size:14px; color:#0c63e4; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#d1e7dd; padding:12px;"><span id="trasladoTotalHaberNio" style="font-size:14px; color:#198754; font-weight:bold;">0.00</span></td>
                                                        <td style="text-align:center; background-color:#d1f2eb; padding:12px;"><span id="trasladoTotalHaberUsd" style="font-size:14px; color:#0dcaf0; font-weight:bold;">0.00</span></td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer p-4 bg-white" style="border-top:2px solid #dee2e6;">
                                        <button type="button" class="btn btn-primary" id="btnAddTrasladoAsientoLinea" style="border-radius:6px; padding:10px 20px; font-weight:600; background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border:none; box-shadow:0 2px 4px rgba(13,110,253,0.3);">
                                            <i class="fas fa-plus-circle"></i> Agregar Línea de Asiento
                                        </button>
                                    </div>
                                </div>

                                <!-- Resumen de operación -->
                                <div class="alert alert-info" id="trasladoResumen" style="display:none; margin-top:15px;">
                                    <h6>Resumen de la operación:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Salida de:</strong><br/>
                                            <span id="trasladoResumenOrigen">-</span><br/>
                                            <span id="trasladoResumenMontoOrigen">-</span>
                                            <span id="trasladoResumenComision" style="display:none;"><br/><small class="text-warning">+ Comisión: <span id="trasladoResumenComisionMonto">0.00</span></small></span>
                                            <span id="trasladoResumenTotal" style="display:none;"><br/><strong class="text-danger">Total: <span id="trasladoResumenTotalMonto">0.00</span></strong></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Entrada en:</strong><br/>
                                            <span id="trasladoResumenDestino">-</span><br/>
                                            <span id="trasladoResumenMontoDestino">-</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer" style="background-color:#f8f9fa; border-top:2px solid #dee2e6; padding:16px; border-radius:0 0 8px 8px;">
                            <button type="button" class="btn btn-light" data-dismiss="modal" style="min-width:100px; border-color:#dee2e6;"><i class="fas fa-times"></i> Cancelar</button>
                            <button type="button" class="btn btn-warning" id="btnGuardarTraslado" style="min-width:150px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border:none; color:white;"><i class="fas fa-check-circle"></i> Registrar Traslado</button>
                        </div>
                    </div>
                </div>
            </div>

<script>
$(function(){
        // Al hacer clic en Cheque, mostrar solo cuentas tipo banco y ajustar campos
                $('#btnMovCheque').on('click', function(e){
                        e.preventDefault();
                        // Limpiar y preparar el formulario
                        $('#formMovimiento')[0].reset();
                        var hoy = new Date().toISOString().slice(0,10);
                        $('[name="fecha_registro"], [name="fecha_aplicacion"]').val(hoy);
                        $('#modalMovimientoLabel').text('Registrar Cheque');
                        $('[name="forma_pago"]').val('CHEQUE');
                        // Mostrar solo cuentas tipo banco
                        var $select = $('#selectCuentaOrigen');
                        $select.empty().append('<option value="">Seleccione...</option>');
                        getCuentasPorTipo('banco').forEach(function(c) {
                                $select.append('<option value="'+c.id+'">'+c.name+' ('+c.code+')</option>');
                        });

                        // Ocultar todos los campos del formulario y mostrar solo los del cheque
                        $('#formMovimiento .form-row, #formMovimiento .form-group').hide();
                        $('#asientoDesgloseSection').hide();
                        $('#asientoDesgloseTable tbody').empty();
                        $('#asientoTotalDebe').text('0.00');
                        $('#asientoTotalHaber').text('0.00');

                        // Remover el layout anterior si existe (para evitar valores sucios)
                        if ($('#chequeCustomLayout').length > 0) {
                                $('#chequeCustomLayout').remove();
                        }
                        
                        // Generar select de cuentas tipo banco
                        var cuentasBanco = <?php echo json_encode(array_values(array_filter($cuentas, function($c){return $c->type==='banco' && $c->estado==1;}))); ?>;
                        var cuentaSelect = '<select class="form-control" name="cuenta_id" id="chequeCuentaBancoSelect" required><option value="">Seleccione cuenta bancaria...</option>';
                        for(var i=0;i<cuentasBanco.length;i++){
                          cuentaSelect += '<option value="'+cuentasBanco[i].id+'">'+cuentasBanco[i].name+' ('+cuentasBanco[i].code+')</option>';
                        }
                        cuentaSelect += '</select>';
                        var chequeHtml = `
                        <div id="chequeCustomLayout">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Cuenta bancaria de origen <span class="text-danger">*</span></label>
                                    `+cuentaSelect+`
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Concepto</label>
                                    <input type="text" class="form-control" name="concepto_cheque" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <textarea class="form-control" name="descripcion_cheque" rows="2" placeholder="Descripción"></textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de registro</label>
                                    <input type="date" class="form-control" name="fecha_registro" value="`+hoy+`" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Monto total</label>
                                    <input type="number" step="0.01" class="form-control" name="monto_total" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Páguese este cheque a:</label>
                                    <input type="text" class="form-control" name="cheque_a" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>No. cheque:</label>
                                    <input type="text" class="form-control" name="numero_cheque" id="numero_cheque_auto" value="" readonly />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de aplicación</label>
                                    <input type="date" class="form-control" name="fecha_aplicacion" value="`+hoy+`" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Forma de pago</label>
                                    <input type="text" class="form-control" name="forma_pago" value="CHEQUE" readonly />
                                <div class="form-group col-md-3">
                                    <label>Referencia 1</label>
                                    <input type="text" class="form-control" name="referencia1" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Referencia 2</label>
                                    <input type="text" class="form-control" name="referencia2" />
                                </div>
                            </div>
                            <!-- Campos ocultos para que lleguen como null -->
                            <input type="hidden" name="departamento" value="" />
                            <input type="hidden" name="centro_costos" value="" />
                            <input type="hidden" name="proyecto" value="" />
                        </div>
                        `;
                        $('#formMovimiento').append(chequeHtml);

                        // Obtener el siguiente número de cheque por AJAX
                        $('#modalMovimiento').modal('show');
                        $('#chequeCuentaBancoSelect').off('change').on('change', function() {
                            var cuentaId = $(this).val();
                            if (cuentaId) {
                                $.get('<?php echo site_url('tesoreria/get_ultimo_numero_cheque_ajax'); ?>?cuenta_id=' + cuentaId, function(resp) {
                                    var nextNum = 1;
                                    if(resp && resp.next_numero) nextNum = resp.next_numero;
                                    $('#numero_cheque_auto').val(nextNum);
                                });
                            } else {
                                $('#numero_cheque_auto').val('');
                            }
                        });
                        // Disparar el cambio al abrir si ya hay una cuenta seleccionada
                        setTimeout(function(){
                            $('#chequeCuentaBancoSelect').trigger('change');
                        }, 200);

                        // Al cerrar el modal, limpiar el layout
                        $('#modalMovimiento').off('hidden.bs.modal.cheque').on('hidden.bs.modal.cheque', function(){
                                $('#chequeCustomLayout').remove();
                                $('#formMovimiento .form-row, #formMovimiento .form-group').show();
                        });
                });
    // Filtrar cuentas por tipo
    var contabilidadAccounts = null;
    function getCuentasPorTipo(tipo) {
        var cuentas = <?php echo json_encode($cuentas); ?>;
        return cuentas.filter(function(c) { return c.type === tipo && c.estado == 1; });
    }
    function cargarCuentasContablesAsiento(callback) {
        if (contabilidadAccounts !== null) {
            callback(contabilidadAccounts);
            return;
        }
        $.getJSON('<?php echo site_url('contabilidad/accounts'); ?>', function(resp){
            if (resp && resp.status === 'success' && Array.isArray(resp.data)) {
                contabilidadAccounts = resp.data;
            } else {
                contabilidadAccounts = [];
            }
            callback(contabilidadAccounts);
        }).fail(function(){
            contabilidadAccounts = [];
            callback(contabilidadAccounts);
        });
    }
    function construirSelectCuentaAsiento(selectedId) {
        var opciones = '<option value=""></option>';
        var cuentas = contabilidadAccounts || [];
        cuentas.forEach(function(c) {
            var label = (c.code ? c.code + ' - ' : '') + (c.name || c.nombre || '');
            opciones += '<option value="'+(c.id || '')+'"'+((String(c.id) === String(selectedId)) ? ' selected' : '')+'>'+label+'</option>';
        });
        return '<select class="form-control asientoCuentaSelect account-select">'+opciones+'</select>';
    }
    function initAsientoCuentaSelect($select, selectedId, $dropdownParent) {
        if (!$select || !$select.length) return;
        var cuentas = contabilidadAccounts || [];
        if (cuentas.length === 0) return;
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        if (typeof $.fn.select2 === 'function') {
            $select.empty();
            $select.append('<option value=""></option>');
            cuentas.forEach(function(c) {
                var label = (c.code ? c.code + ' - ' : '') + (c.name || c.nombre || '');
                $select.append('<option value="'+(c.id || '')+'"'+((String(c.id) === String(selectedId)) ? ' selected' : '')+'>'+label+'</option>');
            });
            $select.select2({
                placeholder: 'Buscar cuenta contable...',
                allowClear: true,
                width: '100%',
                dropdownParent: ($dropdownParent && $dropdownParent.length) ? $dropdownParent : $('#modalMovimiento')
            });
            if (selectedId) {
                $select.val(String(selectedId)).trigger('change.select2');
            }
        }
    }
    function actualizarTotalesAsiento() {
        var totalDebeNio = 0;
        var totalDebeUsd = 0;
        var totalHaberNio = 0;
        var totalHaberUsd = 0;
        $('#asientoDesgloseTable tbody tr').each(function(){
            var debeNio = parseFloat($(this).find('.asientoDebeNio').val() || 0) || 0;
            var debeUsd = parseFloat($(this).find('.asientoDebeUsd').val() || 0) || 0;
            var haberNio = parseFloat($(this).find('.asientoHaberNio').val() || 0) || 0;
            var haberUsd = parseFloat($(this).find('.asientoHaberUsd').val() || 0) || 0;
            totalDebeNio += debeNio;
            totalDebeUsd += debeUsd;
            totalHaberNio += haberNio;
            totalHaberUsd += haberUsd;
        });
        $('#asientoTotalDebeNio').text(totalDebeNio.toFixed(2));
        $('#asientoTotalDebeUsd').text(totalDebeUsd.toFixed(2));
        $('#asientoTotalHaberNio').text(totalHaberNio.toFixed(2));
        $('#asientoTotalHaberUsd').text(totalHaberUsd.toFixed(2));
    }
    function ajustarBloqueoLinea($row) {
        if (!$row || !$row.length) return;
        var debeNio = parseFloat($row.find('.asientoDebeNio').val() || 0) || 0;
        var debeUsd = parseFloat($row.find('.asientoDebeUsd').val() || 0) || 0;
        var haberNio = parseFloat($row.find('.asientoHaberNio').val() || 0) || 0;
        var haberUsd = parseFloat($row.find('.asientoHaberUsd').val() || 0) || 0;
        var hasDebe = debeNio > 0 || debeUsd > 0;
        var hasHaber = haberNio > 0 || haberUsd > 0;
        if (hasDebe) {
            $row.find('.asientoHaberNio, .asientoHaberUsd').prop('readonly', true).val('0.00');
        } else {
            $row.find('.asientoHaberNio, .asientoHaberUsd').prop('readonly', false);
        }
        if (hasHaber) {
            $row.find('.asientoDebeNio, .asientoDebeUsd').prop('readonly', true).val('0.00');
        } else {
            $row.find('.asientoDebeNio, .asientoDebeUsd').prop('readonly', false);
        }
    }
    function agregarLineaAsiento(line) {
        var row = $('<tr style="vertical-align:middle; border-bottom:1px solid #dee2e6; height:50px;">');
        var isTransferLine = line && line.transfer === true;
        if (line && line.costoId) {
            row.attr('data-costo-id', line.costoId);
        }
        if (isTransferLine) {
            row.attr('data-transfer-line', '1');
        }
        var cuentaSelectHtml = construirSelectCuentaAsiento(line && line.account_id ? line.account_id : '');
        row.append('<td style="padding:8px; width:250px; min-width:250px;"><div style="width:100%;">'+cuentaSelectHtml+'</div></td>');
        row.append('<td style="text-align:center; background-color:#fffbeb; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm asientoDebeNio" value="'+((line && line.debit)?parseFloat(line.debit).toFixed(2):'0.00')+'" style="text-align:center; border-color:#fbbf24; background-color:#fffbeb; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#e0f2fe; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm asientoDebeUsd" value="'+((line && line.debit_usd)?parseFloat(line.debit_usd).toFixed(2):'0.00')+'" style="text-align:center; border-color:#38bdf8; background-color:#e0f2fe; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#f0fdf4; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm asientoHaberNio" value="'+((line && line.credit)?parseFloat(line.credit).toFixed(2):'0.00')+'" style="text-align:center; border-color:#86efac; background-color:#f0fdf4; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#ecfdf5; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm asientoHaberUsd" value="'+((line && line.credit_usd)?parseFloat(line.credit_usd).toFixed(2):'0.00')+'" style="text-align:center; border-color:#2dd4bf; background-color:#ecfdf5; width:100%; font-weight:600;" /></td>');
        row.append('<td style="padding:8px; width:300px; min-width:300px;"><input type="text" class="form-control form-control-sm asientoDescripcion" value="'+((line && line.description)?String(line.description).replace(/"/g,'&quot;'):'')+'" placeholder="Descripción..." style="font-size:12px; width:100%;" /></td>');
        if (isTransferLine) {
            // Primera línea: transferencia, no permitir eliminar
            row.append('<td style="text-align:center; padding:8px; width:50px; min-width:50px;"></td>');
        } else {
            row.append('<td style="text-align:center; padding:8px; width:50px; min-width:50px;"><button type="button" class="btn btn-sm btn-danger btnRemoveAsientoLinea" title="Eliminar línea" style="padding:6px 10px;"><i class="fas fa-trash"></i></button></td>');
        }
        $('#asientoDesgloseTable tbody').append(row);
        initAsientoCuentaSelect(row.find('.asientoCuentaSelect'), line && line.account_id ? line.account_id : '', $('#modalMovimiento'));
        ajustarBloqueoLinea(row);
        actualizarTotalesAsiento();
    }

    // Actualiza la primera línea del asiento para que siempre represente la transferencia
    // Función para actualizar la previsualización del movimiento en tiempo real
    function actualizarPreviewMovimiento() {
        var tipo = ($('[name="tipo_transferencia"]').length) ? String($('[name="tipo_transferencia"]').val()).toLowerCase() : '';
        var monto = parseFloat($('[name="monto_total"]:visible').val() || '0.00');
        var $preview = $('#preview_movimiento_tipo');
        
        if (!tipo || monto === 0) {
            $preview.html('Selecciona un tipo y monto para ver la previsualización');
            $preview.removeClass('alert-success alert-danger').addClass('alert-info');
            return;
        }
        
        var monto_formateado = monto.toFixed(2);
        var labelTipo = '';
        
        if (tipo === 'cargo' || tipo === 'cargo (egreso)') {
            labelTipo = 'CARGO';
            $preview.html('<strong>📊 Este movimiento será un CARGO de:</strong> <strong style="color: #dc3545;">$' + monto_formateado + '</strong> (Columna: Debe)');
            $preview.removeClass('alert-info alert-success').addClass('alert-danger');
        } else {
            labelTipo = 'ABONO';
            $preview.html('<strong>📊 Este movimiento será un ABONO de:</strong> <strong style="color: #28a745;">$' + monto_formateado + '</strong> (Columna: Haber)');
            $preview.removeClass('alert-info alert-danger').addClass('alert-success');
        }
    }

    function actualizarLineaTransferenciaMovimiento() {
        var $firstRow = $('#asientoDesgloseTable tbody tr[data-transfer-line="1"]');
        var monto = parseFloat($('[name="monto_total"]:visible').val() || 0) || 0;
        var totalCostos = parseFloat($('#movimientoTotalCostos').text() || 0) || 0;
        var montoTotal = monto + totalCostos;
        var tipo = ($('#grupoTipoTransferencia').length && $('[name="tipo_transferencia"]').length) ? String($('[name="tipo_transferencia"]').val()).toLowerCase() : '';
        var descripcion = ($('[name="beneficiario"]').length ? String($('[name="beneficiario"]').val()) : '') || 'Transferencia';
        if ($firstRow.length) {
            if (tipo === 'cargo' || tipo === 'cargo (egreso)') {
                // Cargo: debe ir en DEBE
                $firstRow.find('.asientoDebeNio').val(montoTotal.toFixed(2));
                $firstRow.find('.asientoDebeUsd').val('0.00');
                $firstRow.find('.asientoHaberNio').val('0.00');
                $firstRow.find('.asientoHaberUsd').val('0.00');
            } else {
                // Abono: debe ir en HABER
                $firstRow.find('.asientoDebeNio').val('0.00');
                $firstRow.find('.asientoDebeUsd').val('0.00');
                $firstRow.find('.asientoHaberNio').val(montoTotal.toFixed(2));
                $firstRow.find('.asientoHaberUsd').val('0.00');
            }
            $firstRow.find('.asientoDescripcion').val(descripcion);
            ajustarBloqueoLinea($firstRow);
        }
        actualizarTotalesAsiento();
    }
    function actualizarTotalesAsientoTraslado() {
        var totalDebeNio = 0;
        var totalDebeUsd = 0;
        var totalHaberNio = 0;
        var totalHaberUsd = 0;
        $('#trasladoAsientoDesgloseTable tbody tr').each(function(){
            var debeNio = parseFloat($(this).find('.trasladoDebeNio').val() || 0) || 0;
            var debeUsd = parseFloat($(this).find('.trasladoDebeUsd').val() || 0) || 0;
            var haberNio = parseFloat($(this).find('.trasladoHaberNio').val() || 0) || 0;
            var haberUsd = parseFloat($(this).find('.trasladoHaberUsd').val() || 0) || 0;
            totalDebeNio += debeNio;
            totalDebeUsd += debeUsd;
            totalHaberNio += haberNio;
            totalHaberUsd += haberUsd;
        });
        $('#trasladoTotalDebeNio').text(totalDebeNio.toFixed(2));
        $('#trasladoTotalDebeUsd').text(totalDebeUsd.toFixed(2));
        $('#trasladoTotalHaberNio').text(totalHaberNio.toFixed(2));
        $('#trasladoTotalHaberUsd').text(totalHaberUsd.toFixed(2));
    }
    function ajustarBloqueoLineaTraslado($row) {
        if (!$row || !$row.length) return;
        var debeNio = parseFloat($row.find('.trasladoDebeNio').val() || 0) || 0;
        var debeUsd = parseFloat($row.find('.trasladoDebeUsd').val() || 0) || 0;
        var haberNio = parseFloat($row.find('.trasladoHaberNio').val() || 0) || 0;
        var haberUsd = parseFloat($row.find('.trasladoHaberUsd').val() || 0) || 0;
        var hasDebe = debeNio > 0 || debeUsd > 0;
        var hasHaber = haberNio > 0 || haberUsd > 0;
        if (hasDebe) {
            $row.find('.trasladoHaberNio, .trasladoHaberUsd').prop('readonly', true).val('0.00');
        } else {
            $row.find('.trasladoHaberNio, .trasladoHaberUsd').prop('readonly', false);
        }
        if (hasHaber) {
            $row.find('.trasladoDebeNio, .trasladoDebeUsd').prop('readonly', true).val('0.00');
        } else {
            $row.find('.trasladoDebeNio, .trasladoDebeUsd').prop('readonly', false);
        }
    }
    function agregarLineaAsientoTraslado(line) {
        var row = $('<tr style="vertical-align:middle; border-bottom:1px solid #dee2e6; height:50px;">');
        if (line && line.gastoId) {
            row.attr('data-gasto-id', line.gastoId);
        }
        if (line && line.bancoLine) {
            row.attr('data-banco-line', line.bancoLine);
        }
        var cuentaSelectHtml = construirSelectCuentaAsiento(line && line.account_id ? line.account_id : '');
        row.append('<td style="padding:8px; width:250px; min-width:250px;"><div style="width:100%;">'+cuentaSelectHtml+'</div></td>');
        row.append('<td style="text-align:center; background-color:#fffbeb; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoDebeNio" value="'+((line && line.debit)?parseFloat(line.debit).toFixed(2):'0.00')+'" style="text-align:center; border-color:#fbbf24; background-color:#fffbeb; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#e0f2fe; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoDebeUsd" value="'+((line && line.debit_usd)?parseFloat(line.debit_usd).toFixed(2):'0.00')+'" style="text-align:center; border-color:#38bdf8; background-color:#e0f2fe; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#f0fdf4; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoHaberNio" value="'+((line && line.credit)?parseFloat(line.credit).toFixed(2):'0.00')+'" style="text-align:center; border-color:#86efac; background-color:#f0fdf4; width:100%; font-weight:600;" /></td>');
        row.append('<td style="text-align:center; background-color:#ecfdf5; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoHaberUsd" value="'+((line && line.credit_usd)?parseFloat(line.credit_usd).toFixed(2):'0.00')+'" style="text-align:center; border-color:#2dd4bf; background-color:#ecfdf5; width:100%; font-weight:600;" /></td>');
        row.append('<td style="padding:8px; width:300px; min-width:300px;"><input type="text" class="form-control form-control-sm asientoDescripcion" value="'+((line && line.description)?String(line.description).replace(/"/g,'&quot;'):'')+'" placeholder="Descripción..." style="font-size:12px; width:100%;" /></td>');
        if (line && line.bancoLine) {
            row.append('<td></td>');
        } else {
            row.append('<td style="text-align:center; padding:8px; width:50px; min-width:50px;"><button type="button" class="btn btn-sm btn-danger btnRemoveTrasladoAsientoLinea" title="Eliminar línea" style="padding:6px 10px;"><i class="fas fa-trash"></i></button></td>');
        }
        $('#trasladoAsientoDesgloseTable tbody').append(row);
        initAsientoCuentaSelect(row.find('.asientoCuentaSelect'), line && line.account_id ? line.account_id : '', $('#modalTraslado'));
        ajustarBloqueoLineaTraslado(row);
        actualizarTotalesAsientoTraslado();
    }
    $('#btnAddTrasladoAsientoLinea').on('click', function(){
        agregarLineaAsientoTraslado();
    });
    $('#btnAddGastoMovimiento').on('click', function(){
        $('#movimientoGastosSection').show();
        agregarLineaGastoMovimiento();
    });
    
    // Mostrar sección de tipo de cambio cuando hay costos o monto
    function mostrarTipoCambioMovimiento() {
        var monto = parseFloat($('[name="monto_total"]:visible').val() || 0) || 0;
        var totalCostos = parseFloat($('#movimientoTotalCostos').text() || 0) || 0;
        if (monto > 0 || totalCostos > 0) {
            $('#movimientoTipoCambioSection').show();
            actualizarTipoCambioMovimiento();
        } else {
            $('#movimientoTipoCambioSection').hide();
        }
    }
    
    function actualizarTipoCambioMovimiento() {
        var monto = parseFloat($('[name="monto_total"]:visible').val() || 0) || 0;
        var totalCostos = parseFloat($('#movimientoTotalCostos').text() || 0) || 0;
        var montoTotal = monto + totalCostos;
        var tc = parseFloat($('#movimientoTipoCambio').val() || 36.50) || 36.50;
        var montoEquivalente = montoTotal / tc;  // Si es NIO a USD
        
        $('#movimientoMontoDisplay').text(montoTotal.toFixed(2));
        $('#movimientoMontoEquivalente').text(montoEquivalente.toFixed(2));
        $('#movimientoTasaDisplay').text(tc.toFixed(4));
        $('#movimientoTasaGuardada').val(tc.toFixed(4));
        // Actualizar la primera línea del asiento para reflejar el monto total
        actualizarLineaTransferenciaMovimiento();
    }
    
    // Detectar cambios en monto y mostrar TC
    $(document).on('input', '[name="monto_total"]:visible', function(){
        mostrarTipoCambioMovimiento();
    });
    // Si el tipo de transferencia cambia (Cargo/Abono), actualizar la primera línea del asiento
    $(document).on('change', '[name="tipo_transferencia"]', function(){
        actualizarPreviewMovimiento();
        actualizarLineaTransferenciaMovimiento();
    });

    // Actualizar previsualización cuando cambia el monto
    $(document).on('input change', '[name="monto_total"]', function(){
        actualizarPreviewMovimiento();
    });
    
    // Detectar cambios en el TC
    $(document).on('input', '#movimientoTipoCambio', function(){
        actualizarTipoCambioMovimiento();
    });
    $(document).on('click', '.btnRemoveMovimientoGastoLinea', function(){
        var $costRow = $(this).closest('tr');
        var costoId = $costRow.attr('data-gasto-id');
        $costRow.remove();
        // Eliminar la línea correspondiente del asiento
        if (costoId) {
            $('#asientoDesgloseTable tbody tr[data-costo-id="'+costoId+'"]').remove();
        }
        actualizarTotalesGastosMovimiento();
        actualizarTotalesAsiento();
    });
    
    $(document).on('input', '.movimientoGastoMonto', function(){
        var $costRow = $(this).closest('tr');
        var costoId = $costRow.attr('data-gasto-id');
        var monto = parseFloat($(this).val() || 0) || 0;
        // Sincronizar con la línea del asiento
        if (costoId) {
            var $asientoRow = $('#asientoDesgloseTable tbody tr[data-costo-id="'+costoId+'"]');
            if ($asientoRow.length) {
                $asientoRow.find('.asientoDebeNio').val(monto.toFixed(2));
                $asientoRow.find('.asientoDebeUsd').val('0.00');
                actualizarTotalesAsiento();
            }
        }
        actualizarTotalesGastosMovimiento();
    });
    
    $(document).on('input', '.movimientoGastoDescripcion', function(){
        var $costRow = $(this).closest('tr');
        var costoId = $costRow.attr('data-gasto-id');
        var descripcion = $(this).val() || 'Costo adicional';
        // Sincronizar con la línea del asiento
        if (costoId) {
            var $asientoRow = $('#asientoDesgloseTable tbody tr[data-costo-id="'+costoId+'"]');
            if ($asientoRow.length) {
                $asientoRow.find('.asientoDescripcion').val(descripcion);
            }
        }
    });
    $(document).on('change', '.trasladoDebeNio, .trasladoDebeUsd, .trasladoHaberNio, .trasladoHaberUsd', function(){
        var $row = $(this).closest('tr');
        ajustarBloqueoLineaTraslado($row);
        actualizarTotalesAsientoTraslado();
    });
    $(document).on('click', '.btnRemoveTrasladoAsientoLinea', function(){
        $(this).closest('tr').remove();
        actualizarTotalesAsientoTraslado();
    });
    function resetAsientoDesglose() {
        $('#asientoDesgloseTable tbody').empty();
        $('#asientoTotalDebeNio').text('0.00');
        $('#asientoTotalDebeUsd').text('0.00');
        $('#asientoTotalHaberNio').text('0.00');
        $('#asientoTotalHaberUsd').text('0.00');
        cargarCuentasContablesAsiento(function(){
            // Crear la primera línea que representa la transferencia
            var monto = parseFloat($('[name="monto_total"]:visible').val() || 0) || 0;
            var totalCostos = parseFloat($('#movimientoTotalCostos').text() || 0) || 0;
            var montoTotal = monto + totalCostos;
            var tipo = ($('#grupoTipoTransferencia').length && $('[name="tipo_transferencia"]').length) ? String($('[name="tipo_transferencia"]').val()).toLowerCase() : '';
            var descripcion = ($('[name="beneficiario"]').length ? String($('[name="beneficiario"]').val()) : '') || 'Transferencia';
            var line = { transfer: true, account_id: '', debit: 0, debit_usd:0, credit:0, credit_usd:0, description: descripcion };
            if (tipo === 'cargo' || tipo === 'cargo (egreso)') {
                // Cargo debe ir en DEBE
                line.debit = montoTotal;
            } else {
                // Abono debe ir en HABER
                line.credit = montoTotal;
            }
            agregarLineaAsiento(line);
        });
    }
    $('#btnAddAsientoLinea').on('click', function(){
        agregarLineaAsiento();
    });
    $(document).on('change', '.asientoDebeNio, .asientoDebeUsd, .asientoHaberNio, .asientoHaberUsd', function(){
        var $row = $(this).closest('tr');
        ajustarBloqueoLinea($row);
        actualizarTotalesAsiento();
    });
    $(document).on('change', '.asientoCuentaSelect', function(){
        // Si el select usa Select2, mantener la instancia actualizada
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).trigger('change.select2');
        }
    });
    $(document).on('click', '.btnRemoveAsientoLinea', function(){
        $(this).closest('tr').remove();
        actualizarTotalesAsiento();
    });
    // Cargar movimientos al iniciar
    var movimientosPage = 1;
    var movimientosPageSize = 25;

    function actualizarControlesPaginacion(total, page, pageSize) {
        var totalPages = pageSize > 0 ? Math.ceil(total / pageSize) : 1;
        var start = total === 0 ? 0 : ((page - 1) * pageSize) + 1;
        var end = Math.min(total, page * pageSize);
        $('#movimientosPaginationInfo').text('Mostrando ' + start + ' - ' + end + ' de ' + total + ' registros');
        $('#btnPagePrev').prop('disabled', page <= 1);
        $('#btnPageNext').prop('disabled', page >= totalPages);
    }

    function cargarMovimientos(){
        var desde = $('#filtroDesde').val();
        var hasta = $('#filtroHasta').val();
        var tipoDoc = $('#filtroTipoDoc').val();
        var modo = $('#movimientosContext').val();
        $.get('<?php echo site_url('tesoreria/get_movimientos_ajax'); ?>', {desde: desde, hasta: hasta, tipo: tipoDoc, modo: modo, page: movimientosPage, page_size: movimientosPageSize}, function(resp){
            var j = (typeof resp === 'object')? resp : JSON.parse(resp);
            var $tbody = $('#tablaMovimientos tbody');
            $tbody.empty();
            if(j && j.status && Array.isArray(j.movimientos)){
                j.movimientos.forEach(function(mov){
                    var rowClass = mov.estado === 'anulado' ? 'row-anulado' : (mov.estado === 'previsualizacion' ? 'row-preview' : '');
                    var motivoAnulacion = mov.estado === 'anulado' && mov.motivo_anulacion ? '<span title="'+mov.motivo_anulacion.replace(/"/g,'&quot;')+'" class="badge mov-badge mov-badge-anulado">Anulado</span>' : '';
                    var acciones = '';
                    var esDesembolso = String(mov.tipo || '') === 'desembolso_preview' || String(mov.referencia2 || '').indexOf('p=') === 0;
                    if (mov.estado === 'previsualizacion') {
                        acciones = '<button class="btn btn-sm btn-mov-view btnVerCheque" data-id="'+mov.id+'" data-preview="1">Ver</button> ';
                        acciones += '<button class="btn btn-sm btn-mov-info btnVerInfo" data-id="'+mov.id+'">Info</button> ';
                    } else if(mov.estado !== 'anulado') {
                        acciones = '<button class="btn btn-sm btn-mov-view btnVerCheque" data-id="'+mov.id+'">Ver</button> ';
                        acciones += '<button class="btn btn-sm btn-mov-info btnVerInfo" data-id="'+mov.id+'">Info</button> ';
                        if (esDesembolso) {
                            acciones += '<button class="btn btn-sm btn-mov-muted" disabled style="pointer-events:none;opacity:0.75;">Anular bloqueado</button> ';
                        } else {
                            acciones += '<button class="btn btn-sm btn-mov-anular btnAnularMov" data-id="'+mov.id+'">Anular</button> ';
                        }
                        acciones += '<button class="btn btn-sm btn-mov-conta btnContabilizarMov" data-id="'+mov.id+'">Contabilizar</button>';
                    } else {
                        acciones = '<button class="btn btn-sm btn-mov-view btnVerCheque" data-id="'+mov.id+'">Ver</button> ';
                        acciones += '<button class="btn btn-sm btn-mov-info btnVerInfo" data-id="'+mov.id+'">Info</button> ' + motivoAnulacion;
                    }
                    var badge = '';
                    if (mov.estado === 'previsualizacion') badge = '<span class="badge mov-badge mov-badge-preview">Solicitud</span>';
                    else if(mov.tipo_transferencia === 'abono') badge = '<span class="badge mov-badge mov-badge-abono">Abono</span>';
                    else if(mov.tipo_transferencia === 'cargo') badge = '<span class="badge mov-badge mov-badge-cargo">Cargo</span>';
                    else badge = '<span class="badge mov-badge">'+(mov.tipo_transferencia||'')+'</span>';
                    var monto = (!isNaN(parseFloat(mov.monto_total))) ? Number(mov.monto_total).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : mov.monto_total;
                    var ref1 = mov.referencia1 || '';
                    var ref2 = mov.referencia2 || '';
                    var desc = mov.descripcion || '';
                    if (mov.tipo === 'desembolso_preview' && desc.indexOf('Inicio crédito:') === 0) {
                        desc = 'Solicitud de desembolso';
                        if (ref1) {
                            desc += ' (' + ref1 + ')';
                        }
                    }
                    $tbody.append(
                        '<tr class="'+rowClass+'">'+
                        '<td>'+mov.id+'</td>'+
                        '<td>'+(mov.cuenta_nombre || mov.cuenta_id)+'</td>'+
                        '<td>'+badge+'</td>'+ 
                        '<td class="mov-forma-pago-cell">'+mov.forma_pago+'</td>'+ 
                        '<td>'+(mov.fecha_registro_display || mov.fecha_registro || '')+'</td>'+ 
                        '<td>'+(mov.fecha_aplicacion_display || mov.fecha_aplicacion || '')+'</td>'+ 
                        '<td>'+(mov.beneficiario || '')+'</td>'+
                        '<td class="text-right font-weight-bold">'+monto+'</td>'+
                        '<td class="texto-suave mov-user-cell" title="'+String(mov.ejecutado_por || '-').replace(/"/g,'&quot;')+'">'+(mov.ejecutado_por || '-')+'</td>'+
                        '<td class="mov-ref-cell" title="'+String(ref1).replace(/"/g,'&quot;')+'">'+ref1+'</td>'+
                        '<td class="mov-ref-cell" title="'+String(ref2).replace(/"/g,'&quot;')+'">'+ref2+'</td>'+
                        '<td class="mov-desc-cell" title="'+String(desc).replace(/"/g,'&quot;')+'">'+desc+'</td>'+
                        '<td class="acciones-cell">'+acciones+'</td>'+
                        '</tr>'
                    );
                });
            }else{
                $tbody.append('<tr><td colspan="13" class="text-center">Sin movimientos registrados</td></tr>');
            }
            if (j && j.status) {
                actualizarControlesPaginacion(parseInt(j.total || 0, 10), parseInt(j.page || movimientosPage, 10), parseInt(j.page_size || movimientosPageSize, 10));
            } else {
                actualizarControlesPaginacion(0, 1, movimientosPageSize);
            }
        });
    }
    var movimientosContext = $('#movimientosContext').val();
    function aplicarContextoDocumentos() {
        if (movimientosContext === 'caja') {
            $('#btnMovTransferencia, #btnMovCheque, #btnMovTraslado, #btnMovOtros').hide();
            $('#btnMovEfectivo').show();
            $('#filtroTipoDoc').empty().append('<option value="efectivo">Efectivo</option>').val('efectivo');
        } else if (movimientosContext === 'banco') {
            $('#btnMovTransferencia, #btnMovCheque, #btnMovTraslado').show();
            $('#btnMovEfectivo, #btnMovOtros').hide();
            $('#filtroTipoDoc').empty()
                .append('<option value="">Todos</option>')
                .append('<option value="transferencia">Transferencia</option>')
                .append('<option value="cheque">Cheque</option>')
                .append('<option value="traslado">Traslado</option>')
                .val('');
        } else {
            $('#btnMovTransferencia, #btnMovEfectivo, #btnMovCheque, #btnMovTraslado, #btnMovOtros').show();
            $('#filtroTipoDoc').empty()
                .append('<option value="">Todos</option>')
                .append('<option value="transferencia">Transferencia</option>')
                .append('<option value="efectivo">Efectivo</option>')
                .append('<option value="cheque">Cheque</option>')
                .append('<option value="traslado">Traslado</option>')
                .append('<option value="otros">Otros</option>')
                .val('');
        }
    }
    aplicarContextoDocumentos();
    cargarMovimientos();
    // Filtros
    $('#btnFiltrarMov').on('click', function(){
        movimientosPage = 1;
        cargarMovimientos();
    });
    $('#filtroDesde, #filtroHasta, #filtroTipoDoc').on('change', function(){
        movimientosPage = 1;
        cargarMovimientos();
    });
    $('#btnPagePrev').on('click', function(){
        if (movimientosPage > 1) {
            movimientosPage -= 1;
            cargarMovimientos();
        }
    });
    $('#btnPageNext').on('click', function(){
        movimientosPage += 1;
        cargarMovimientos();
    });
    // Al hacer clic en Transferencia, mostrar modal
    $('#btnMovTransferencia').on('click', function(e){
            $('#rowCheque').hide();
            $('#grupoTipoTransferencia').show();
            $('[name="beneficiario"]').closest('.form-group').show();
            $('[name="forma_pago"]').val('TRANSFERENCIA');
        e.preventDefault();
        // Limpiar formulario
        $('#formMovimiento')[0].reset();
        // Limpiar el layout del cheque si existe (para evitar que aparezca con valores previos)
        if ($('#chequeCustomLayout').length > 0) {
            $('#chequeCustomLayout').remove();
        }
        // Setear fechas por defecto
        var hoy = new Date().toISOString().slice(0,10);
        $('[name="fecha_registro"], [name="fecha_aplicacion"]').val(hoy);
        $('#modalMovimientoLabel').text('Registrar Transferencia');
        $('[name="forma_pago"]').val('TRANSFERENCIA');
        // Mostrar solo cuentas tipo banco
        var $select = $('#selectCuentaOrigen');
        $select.empty().append('<option value="">Seleccione...</option>');
        getCuentasPorTipo('banco').forEach(function(c) {
            $select.append('<option value="'+c.id+'">'+c.name+' ('+c.code+')</option>');
        });
        resetAsientoDesglose();
        resetMovimientoGastos();
        $('#asientoDesgloseSection').show();
        $('#modalMovimiento').modal('show');
    });

    // Al hacer clic en Traslado entre cuentas
    $('#btnMovTraslado').on('click', function(e){
        e.preventDefault();
        $('#formTraslado')[0].reset();
        
        var hoy = new Date().toISOString().slice(0,10);
        $('#trasladoFechaRegistro, #trasladoFechaAplicacion').val(hoy);
        
        // Cargar cuentas bancarias
        var cuentasBanco = getCuentasPorTipo('banco');
        var $selectOrigen = $('#trasladoCuentaOrigen');
        var $selectDestino = $('#trasladoCuentaDestino');
        
        $selectOrigen.empty().append('<option value="">Seleccione cuenta...</option>');
        $selectDestino.empty().append('<option value="">Seleccione cuenta...</option>');
        
        cuentasBanco.forEach(function(c) {
            $selectOrigen.append('<option value="'+c.id+'" data-moneda="'+(c.currency||'NIO')+'">'+c.name+' ('+c.code+')</option>');
            $selectDestino.append('<option value="'+c.id+'" data-moneda="'+(c.currency||'NIO')+'">'+c.name+' ('+c.code+')</option>');
        });
        
        // Ocultar el resumen y conversión al abrir
        $('#trasladoResumen').hide();
        $('#trasladoConversionGroup').hide();
        resetAsientoDesgloseTraslado();
        resetTrasladoGastos();
        $('#trasladoAsientoDesgloseSection').show();
        $('#modalTraslado').modal('show');
    });

    // Manejar cambios en los selects de traslado
    $('#trasladoCuentaOrigen, #trasladoCuentaDestino').on('change', function(){
        var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
        var monedaDestino = $('#trasladoCuentaDestino option:selected').data('moneda') || 'NIO';
        
        $('#trasladoMonedaOrigen').text(monedaOrigen);
        $('#trasladoMonedaDestino').text(monedaDestino);
        $('#trasladoMonedaInput').text(monedaOrigen);
        $('#trasladoMonedaComision').text(monedaOrigen);
        
        if (monedaOrigen !== monedaDestino) {
            $('#trasladoConversionGroup').show();
            $('#trasladoConversionDestino').show();
            $('#trasladoMonedaEquivalente').text(monedaDestino);
        } else {
            $('#trasladoConversionGroup').hide();
            $('#trasladoConversionDestino').hide();
            $('#trasladoMontoEquivalente').val('');
        }
        
        actualizarTotalesGastos();
    });

    function resetAsientoDesgloseTraslado() {
        $('#trasladoAsientoDesgloseTable tbody').empty();
        $('#trasladoTotalDebeNio').text('0.00');
        $('#trasladoTotalDebeUsd').text('0.00');
        $('#trasladoTotalHaberNio').text('0.00');
        $('#trasladoTotalHaberUsd').text('0.00');
        cargarCuentasContablesAsiento(function(){
            agregarLineaAsientoTraslado({bancoLine: 'origen', description: 'Cuenta banco origen'});
            agregarLineaAsientoTraslado({bancoLine: 'destino', description: 'Cuenta banco destino'});
            actualizarLineaBancoTraslado();
        });
    }

    function resetTrasladoGastos() {
        $('#trasladoGastosTable tbody').empty();
        $('#trasladoTotalCostos').text('0.00');
    }

    function resetMovimientoGastos() {
        $('#movimientoGastosTable tbody').empty();
        $('#movimientoTotalCostos').text('0.00');
        $('#movimientoGastosSection').hide();
    }

    function agregarLineaGastoMovimiento(line) {
        var gastoId = 'gasto-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        var descripcion = line && line.description ? String(line.description).replace(/"/g,'&quot;') : '';
        var monto = line && !isNaN(parseFloat(line.amount)) ? parseFloat(line.amount).toFixed(2) : '0.00';
        var row = $('<tr data-gasto-id="'+gastoId+'">');
        row.append('<td><input type="text" class="form-control form-control-sm movimientoGastoDescripcion" placeholder="Descripción del costo" value="'+descripcion+'" style="width:100%;" /></td>');
        row.append('<td style="text-align:right;"><input type="number" step="0.01" min="0" class="form-control form-control-sm movimientoGastoMonto" value="'+monto+'" style="text-align:right;" /></td>');
        row.append('<td class="text-center"><button type="button" class="btn btn-sm btn-danger btnRemoveMovimientoGastoLinea" title="Eliminar costo"><i class="fas fa-trash"></i></button></td>');
        $('#movimientoGastosTable tbody').append(row);
        
        // Agregar línea correspondiente al asiento contable
        agregarLineaAsiento({
            account_id: '',
            debit: parseFloat(monto) || 0,
            debit_usd: 0,
            credit: 0,
            credit_usd: 0,
            description: descripcion || 'Costo adicional',
            costoId: gastoId
        });
        
        actualizarTotalesGastosMovimiento();
    }

    function actualizarTotalesGastosMovimiento() {
        var totalGastos = 0;
        $('#movimientoGastosTable tbody tr').each(function(){
            var monto = parseFloat($(this).find('.movimientoGastoMonto').val() || 0) || 0;
            totalGastos += monto;
        });
        $('#movimientoTotalCostos').text(totalGastos.toFixed(2));
        mostrarTipoCambioMovimiento();
    }

    function actualizarTotalesGastos() {
        var totalGastos = 0;
        $('#trasladoGastosTable tbody tr').each(function(){
            var monto = parseFloat($(this).find('.trasladoGastoMonto').val() || 0) || 0;
            var descripcion = $(this).find('.trasladoGastoDescripcion').val() || '';
            totalGastos += monto;
            var gastoId = $(this).attr('data-gasto-id');
            if (gastoId) {
                var asientoRow = $('#trasladoAsientoDesgloseTable tbody tr[data-gasto-id="'+gastoId+'"]');
                if (asientoRow.length) {
                    var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
                    if (monedaOrigen === 'USD') {
                        asientoRow.find('.trasladoDebeUsd').val(monto.toFixed(2));
                        asientoRow.find('.trasladoDebeNio').val('0.00');
                    } else {
                        asientoRow.find('.trasladoDebeNio').val(monto.toFixed(2));
                        asientoRow.find('.trasladoDebeUsd').val('0.00');
                    }
                    asientoRow.find('.asientoDescripcion').val(descripcion || 'Gasto adicional');
                }
            }
        });
        $('#trasladoTotalCostos').text(totalGastos.toFixed(2));
        actualizarLineaBancoTraslado();
        actualizarTotalesAsientoTraslado();
        actualizarLineaComisionTraslado();
        calcularEquivalente();
    }

    $('#trasladoMonto, #trasladoComision').on('input', function(){
        actualizarLineaBancoTraslado();
        actualizarLineaComisionTraslado();
        calcularEquivalente();
    });

    $('#trasladoTcVenta, #trasladoTcCompra, #trasladoTcSelector').on('input change', function(){
        calcularEquivalente();
        actualizarLineaBancoTraslado();
    });

    $(document).on('input change', '.trasladoGastoMonto, .trasladoGastoDescripcion', function(){
        actualizarTotalesGastos();
    });

    function actualizarLineaComisionTraslado() {
        var comision = parseFloat($('#trasladoComision').val() || 0) || 0;
        var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
        var $comisionRow = $('#trasladoAsientoDesgloseTable tbody tr[data-comision-line="1"]');

        if (comision <= 0) {
            if ($comisionRow.length) {
                $comisionRow.remove();
                actualizarTotalesAsientoTraslado();
            }
            return;
        }

        var descripcion = 'Comisión de traslado';
        if (!$comisionRow.length) {
            $comisionRow = $('<tr data-comision-line="1"></tr>');
            var cuentaSelectHtml = construirSelectCuentaAsiento('');
            $comisionRow.append('<td style="padding:8px; width:250px; min-width:250px;"><div style="width:100%;">' + cuentaSelectHtml + '</div></td>');
            $comisionRow.append('<td style="text-align:center; background-color:#fffbeb; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoDebeNio" value="' + (monedaOrigen === 'USD' ? '0.00' : comision.toFixed(2)) + '" style="text-align:center; border-color:#fbbf24; background-color:#fffbeb; width:100%; font-weight:600;" /></td>');
            $comisionRow.append('<td style="text-align:center; background-color:#e0f2fe; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoDebeUsd" value="' + (monedaOrigen === 'USD' ? comision.toFixed(2) : '0.00') + '" style="text-align:center; border-color:#38bdf8; background-color:#e0f2fe; width:100%; font-weight:600;" /></td>');
            $comisionRow.append('<td style="text-align:center; background-color:#f0fdf4; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoHaberNio" value="0.00" style="text-align:center; border-color:#86efac; background-color:#f0fdf4; width:100%; font-weight:600;" readonly /></td>');
            $comisionRow.append('<td style="text-align:center; background-color:#ecfdf5; padding:8px; width:110px; min-width:110px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm trasladoHaberUsd" value="0.00" style="text-align:center; border-color:#2dd4bf; background-color:#ecfdf5; width:100%; font-weight:600;" readonly /></td>');
            $comisionRow.append('<td style="padding:8px; width:300px; min-width:300px;"><input type="text" class="form-control form-control-sm asientoDescripcion" value="' + descripcion + '" placeholder="Descripción..." style="font-size:12px; width:100%;" readonly /></td>');
            $comisionRow.append('<td style="text-align:center; padding:8px; width:50px; min-width:50px;"><button type="button" class="btn btn-sm btn-danger btnRemoveTrasladoAsientoLinea" title="Eliminar línea" style="padding:6px 10px;"><i class="fas fa-trash"></i></button></td>');
            $('#trasladoAsientoDesgloseTable tbody').append($comisionRow);
            initAsientoCuentaSelect($comisionRow.find('.asientoCuentaSelect'), '', $('#modalTraslado'));
        } else {
            if (monedaOrigen === 'USD') {
                $comisionRow.find('.trasladoDebeUsd').val(comision.toFixed(2));
                $comisionRow.find('.trasladoDebeNio').val('0.00');
            } else {
                $comisionRow.find('.trasladoDebeNio').val(comision.toFixed(2));
                $comisionRow.find('.trasladoDebeUsd').val('0.00');
            }
            $comisionRow.find('.asientoDescripcion').val(descripcion);
        }
        ajustarBloqueoLineaTraslado($comisionRow);
        actualizarTotalesAsientoTraslado();
    }

    $(document).on('click', '.btnRemoveTrasladoGastoLinea', function(){
        var gastoId = $(this).closest('tr').attr('data-gasto-id');
        $(this).closest('tr').remove();
        if (gastoId) {
            $('#trasladoAsientoDesgloseTable tbody tr[data-gasto-id="'+gastoId+'"]').remove();
            actualizarTotalesAsientoTraslado();
        }
        actualizarTotalesGastos();
    });

    $('#btnAddTrasladoGastoLinea').on('click', function(){
        agregarLineaGastoTraslado();
    });
    $('#btnAddTrasladoGastoLineaTop').on('click', function(){
        agregarLineaGastoTraslado();
    });

    function actualizarLineaBancoTraslado() {
        var monto = parseFloat($('#trasladoMonto').val() || 0);
        var comision = parseFloat($('#trasladoComision').val() || 0);
        var gastosTotales = parseFloat($('#trasladoTotalCostos').text() || 0) || 0;
        var cuentaOrigen = $('#trasladoCuentaOrigen').val() || '';
        var cuentaDestino = $('#trasladoCuentaDestino').val() || '';
        var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
        var monedaDestino = $('#trasladoCuentaDestino option:selected').data('moneda') || 'NIO';
        var totalOrigen = monto + comision + gastosTotales;
        var montoDestino = parseFloat($('#trasladoMontoEquivalente').val() || monto);
        var $origen = $('#trasladoAsientoDesgloseTable tbody tr[data-banco-line="origen"]');
        var $destino = $('#trasladoAsientoDesgloseTable tbody tr[data-banco-line="destino"]');

        if ($origen.length) {
            if (monedaOrigen === 'USD') {
                $origen.find('.trasladoHaberUsd').val(totalOrigen.toFixed(2));
                $origen.find('.trasladoHaberNio').val('0.00');
            } else {
                $origen.find('.trasladoHaberNio').val(totalOrigen.toFixed(2));
                $origen.find('.trasladoHaberUsd').val('0.00');
            }
            if (cuentaOrigen) {
                $origen.find('.asientoCuentaSelect').val(cuentaOrigen).trigger('change.select2');
            }
            $origen.find('.asientoDescripcion').val('Cuenta banco origen: ' + ($('#trasladoCuentaOrigen option:selected').text() || '-'));
            ajustarBloqueoLineaTraslado($origen);
        }

        if ($destino.length) {
            if (monedaDestino === 'USD') {
                $destino.find('.trasladoDebeUsd').val(montoDestino.toFixed(2));
                $destino.find('.trasladoDebeNio').val('0.00');
            } else {
                $destino.find('.trasladoDebeNio').val(montoDestino.toFixed(2));
                $destino.find('.trasladoDebeUsd').val('0.00');
            }
            if (cuentaDestino) {
                $destino.find('.asientoCuentaSelect').val(cuentaDestino).trigger('change.select2');
            }
            $destino.find('.asientoDescripcion').val('Cuenta banco destino: ' + ($('#trasladoCuentaDestino option:selected').text() || '-'));
            ajustarBloqueoLineaTraslado($destino);
        }

        actualizarTotalesAsientoTraslado();
    }

    function calcularEquivalente() {
        var monto = parseFloat($('#trasladoMonto').val() || 0);
        var comision = parseFloat($('#trasladoComision').val() || 0);
        var gastosTotales = parseFloat($('#trasladoTotalCostos').text() || 0) || 0;
        var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
        var monedaDestino = $('#trasladoCuentaDestino option:selected').data('moneda') || 'NIO';
        
        // Obtener la tasa de cambio seleccionada
        var tcVenta = parseFloat($('#trasladoTcVenta').val() || 36.50);
        var tcCompra = parseFloat($('#trasladoTcCompra').val() || 36.50);
        var tcSelector = $('#trasladoTcSelector').val() || 'venta';
        var tasaCambio = monedaOrigen === monedaDestino ? 1 : ((tcSelector === 'venta') ? tcVenta : tcCompra);
        
        // Calcular montos
        var montoTotal = monto + comision + gastosTotales;
        var montoEquivalente = monto;
        if (monedaOrigen !== monedaDestino) {
            if (monedaOrigen === 'USD' && monedaDestino === 'NIO') {
                montoEquivalente = monto * tasaCambio;
            } else if (monedaOrigen === 'NIO' && monedaDestino === 'USD') {
                montoEquivalente = monto / tasaCambio;
            }
            $('#trasladoTasaCambio').text(tasaCambio.toFixed(4));
        } else {
            $('#trasladoTasaCambio').text('1.0000');
        }
        $('#trasladoMontoEquivalente').val(montoEquivalente.toFixed(2));
        
        // Actualizar resumen
        var cuentaOrigenText = $('#trasladoCuentaOrigen option:selected').text() || '-';
        var cuentaDestinoText = $('#trasladoCuentaDestino option:selected').text() || '-';
        
        $('#trasladoResumenOrigen').text(cuentaOrigenText);
        $('#trasladoResumenMontoOrigen').text(montoTotal.toFixed(2) + ' ' + monedaOrigen);
        $('#trasladoResumenDestino').text(cuentaDestinoText);
        $('#trasladoResumenMontoDestino').text(montoEquivalente.toFixed(2) + ' ' + monedaDestino);
        
        // Mostrar/ocultar costos en el resumen
        var costoTotal = comision + gastosTotales;
        if (costoTotal > 0) {
            $('#trasladoResumenComision').show();
            $('#trasladoResumenComisionMonto').text(costoTotal.toFixed(2));
            $('#trasladoResumenTotal').show();
            $('#trasladoResumenTotalMonto').text(montoTotal.toFixed(2) + ' ' + monedaOrigen);
        } else {
            $('#trasladoResumenComision').hide();
            $('#trasladoResumenTotal').hide();
        }
        
        if (monto > 0 && $('#trasladoCuentaOrigen').val() && $('#trasladoCuentaDestino').val() && $('#trasladoCuentaOrigen').val() !== $('#trasladoCuentaDestino').val()) {
            $('#trasladoResumen').show();
        }
    }

    // Guardar traslado
    $('#btnGuardarTraslado').on('click', function(){
        var trasladoLines = [];
        $('#trasladoAsientoDesgloseTable tbody tr').each(function(){
            var accountId = $(this).find('.asientoCuentaSelect').val() || '';
            var debit = parseFloat($(this).find('.trasladoDebeNio').val() || 0) || 0;
            var debitUsd = parseFloat($(this).find('.trasladoDebeUsd').val() || 0) || 0;
            var credit = parseFloat($(this).find('.trasladoHaberNio').val() || 0) || 0;
            var creditUsd = parseFloat($(this).find('.trasladoHaberUsd').val() || 0) || 0;
            var description = $(this).find('.asientoDescripcion').val() || '';
            if (accountId || debit > 0 || debitUsd > 0 || credit > 0 || creditUsd > 0 || description) {
                if (!accountId) {
                    alert('Cada línea de asiento debe tener una cuenta contable seleccionada.');
                    trasladoLines = null;
                    return false;
                }
                var hasDebe = debit > 0 || debitUsd > 0;
                var hasHaber = credit > 0 || creditUsd > 0;
                if (!hasDebe && !hasHaber) {
                    alert('Cada línea de asiento debe tener un monto en Debe o Haber.');
                    trasladoLines = null;
                    return false;
                }
                if (hasDebe && hasHaber) {
                    alert('Cada línea de asiento debe ser Debe o Haber, no ambos.');
                    trasladoLines = null;
                    return false;
                }
                trasladoLines.push({
                    account_id: accountId,
                    debit: debit,
                    credit: credit,
                    debit_usd: debitUsd,
                    credit_usd: creditUsd,
                    description: description
                });
            }
        });
        if (trasladoLines === null) {
            return;
        }
        if (trasladoLines.length > 0) {
            var totalDebeNio = 0;
            var totalDebeUsd = 0;
            var totalHaberNio = 0;
            var totalHaberUsd = 0;
            trasladoLines.forEach(function(line){
                totalDebeNio += parseFloat(line.debit || 0);
                totalDebeUsd += parseFloat(line.debit_usd || 0);
                totalHaberNio += parseFloat(line.credit || 0);
                totalHaberUsd += parseFloat(line.credit_usd || 0);
            });
            if (Math.abs(totalDebeNio - totalHaberNio) > 0.009 || Math.abs(totalDebeUsd - totalHaberUsd) > 0.009) {
                alert('El asiento contable debe estar balanceado en cada moneda: totales Debe y Haber deben coincidir.');
                return;
            }
        }
        var cuentaOrigen = $('#trasladoCuentaOrigen').val();
        var cuentaDestino = $('#trasladoCuentaDestino').val();
        var monto = parseFloat($('#trasladoMonto').val() || 0);
        var comision = parseFloat($('#trasladoComision').val() || 0);
        var idTransaccion = $('#trasladoIdTransaccion').val() || '';
        
        if (!cuentaOrigen || !cuentaDestino || monto <= 0) {
            alert('Por favor complete todos los campos correctamente.');
            return;
        }
        
        if (cuentaOrigen === cuentaDestino) {
            alert('La cuenta de origen y destino deben ser diferentes.');
            return;
        }
        
        var monedaOrigen = $('#trasladoCuentaOrigen option:selected').data('moneda') || 'NIO';
        var monedaDestino = $('#trasladoCuentaDestino option:selected').data('moneda') || 'NIO';
        var gastosTotales = parseFloat($('#trasladoTotalCostos').text() || 0) || 0;
        var montoTotal = monto + comision + gastosTotales;
        var montoDestino = parseFloat($('#trasladoMontoEquivalente').val() || monto);
        
        var tcVenta = parseFloat($('#trasladoTcVenta').val() || 36.50);
        var tcCompra = parseFloat($('#trasladoTcCompra').val() || 36.50);
        var tcSelector = $('#trasladoTcSelector').val() || 'venta';
        var tasaCambio = monedaOrigen === monedaDestino ? 1 : ((tcSelector === 'venta') ? tcVenta : tcCompra);
        
        // Crear dos movimientos: salida de origen, entrada en destino
        var payloadOrigen = {
            tipo_movimiento: 'traslado',
            cuenta_id: cuentaOrigen,
            tipo_transferencia: 'cargo',
            forma_pago: 'TRASLADO',
            fecha_registro: $('#trasladoFechaRegistro').val(),
            fecha_aplicacion: $('#trasladoFechaAplicacion').val(),
            beneficiario: $('#trasladoCuentaDestino option:selected').text(),
            referencia1: $('#trasladoConcepto').val() || 'Traslado entre cuentas',
            referencia2: 'Destino: ' + $('#trasladoCuentaDestino option:selected').text(),
            monto_total: montoTotal,
            iva_total: 0,
            departamento: null,
            centro_costos: null,
            proyecto: null,
            descripcion: $('#trasladoDescripcion').val() + (monedaOrigen !== monedaDestino ? ' (TC: '+tasaCambio.toFixed(4)+')' : ''),
            id_transaccion: idTransaccion,
            tasa_cambio: tasaCambio,
            comision: comision
        };
        var payloadDestino = {
            tipo_movimiento: 'traslado',
            cuenta_id: cuentaDestino,
            tipo_transferencia: 'abono',
            forma_pago: 'TRASLADO',
            fecha_registro: $('#trasladoFechaRegistro').val(),
            fecha_aplicacion: $('#trasladoFechaAplicacion').val(),
            beneficiario: $('#trasladoCuentaOrigen option:selected').text(),
            referencia1: $('#trasladoConcepto').val() || 'Traslado entre cuentas',
            referencia2: 'Origen: ' + $('#trasladoCuentaOrigen option:selected').text(),
            monto_total: montoDestino,
            iva_total: 0,
            departamento: null,
            centro_costos: null,
            proyecto: null,
            descripcion: $('#trasladoDescripcion').val() + (monedaOrigen !== monedaDestino ? ' (TC: '+tasaCambio.toFixed(4)+')' : ''),
            id_transaccion: idTransaccion,
            tasa_cambio: tasaCambio,
            comision: 0
        };
        if (trasladoLines.length > 0) {
            payloadOrigen.asiento_lines = JSON.stringify(trasladoLines);
        }
        
        // Guardar ambos movimientos
        $.ajax({
            url: '<?php echo site_url('tesoreria/save_movimiento_ajax'); ?>',
            type: 'POST',
            dataType: 'json',
            data: payloadOrigen,
            success: function(resp1){
                if(resp1 && resp1.status) {
                    $.ajax({
                        url: '<?php echo site_url('tesoreria/save_movimiento_ajax'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: payloadDestino,
                        success: function(resp2){
                            if(resp2 && resp2.status) {
                                alert('Traslado registrado exitosamente.');
                                $('#modalTraslado').modal('hide');
                                cargarMovimientos();
                            } else {
                                alert('Error al guardar el movimiento de destino: ' + (resp2 && resp2.message ? resp2.message : ''));
                            }
                        },
                        error: function(){ alert('Error al guardar el movimiento de destino.'); }
                    });
                } else {
                    alert('Error al guardar el movimiento de origen: ' + (resp1 && resp1.message ? resp1.message : ''));
                }
            },
            error: function(){ alert('Error guardando traslado.'); }
        });
    });

    // Guardar movimiento (AJAX pendiente de implementar)
    $('#btnGuardarMovimiento').on('click', function(){
        var $form = $('#formMovimiento');
        // Detectar si está visible el layout de cheque personalizado
        var isCheque = $('#chequeCustomLayout').is(':visible');
        var payload = {};
        if(isCheque) {
            // Tomar solo los campos relevantes para la tabla y mapear nombres
            payload.tipo_movimiento = 'cheque';
            payload.concepto = $('[name="concepto_cheque"]').val();
            payload.forma_pago = 'CHEQUE';
            payload.fecha_registro = $('[name="fecha_registro"]:visible').val();
            payload.fecha_aplicacion = $('[name="fecha_aplicacion"]:visible').val();
            payload.beneficiario = $('[name="cheque_a"]').val();
            payload.referencia1 = $('[name="referencia1"]:visible').val();
            payload.referencia2 = $('[name="referencia2"]:visible').val();
            payload.monto_total = parseFloat($('[name="monto_total"]:visible').val() || 0);
            payload.departamento = null;
            payload.centro_costos = null;
            payload.proyecto = null;
            payload.descripcion = $('[name="descripcion_cheque"]').val();
            payload.cuenta_id = $('[name="cuenta_id"]:visible').val();
            payload.tipo_transferencia = 'cargo'; // Por defecto para cheque
        } else {
            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }
            payload = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            // Normalizar campos
            payload.monto_total = parseFloat(payload.monto_total || 0);
            payload.departamento = null;
            payload.centro_costos = null;
            payload.proyecto = null;
            payload.tipo_movimiento = 'transferencia';
            // Asegurar que tipo_transferencia sea el valor seleccionado
            payload.tipo_transferencia = $('[name="tipo_transferencia"]').val();
        }
        // Validar cuenta bancaria seleccionada en cheque
        if(isCheque && (!payload.cuenta_id || payload.cuenta_id === '')){
            alert('Seleccione la cuenta bancaria de origen.');
            $('[name="cuenta_id"]:visible').focus();
            return;
        }
        // Cargar líneas de asiento si las hay
        var asientoLines = [];
        $('#asientoDesgloseTable tbody tr').each(function(){
            var accountId = $(this).find('.asientoCuentaSelect').val() || '';
            var debit = parseFloat($(this).find('.asientoDebeNio').val() || 0) || 0;
            var debitUsd = parseFloat($(this).find('.asientoDebeUsd').val() || 0) || 0;
            var credit = parseFloat($(this).find('.asientoHaberNio').val() || 0) || 0;
            var creditUsd = parseFloat($(this).find('.asientoHaberUsd').val() || 0) || 0;
            var description = $(this).find('.asientoDescripcion').val() || '';
            if (accountId || debit > 0 || debitUsd > 0 || credit > 0 || creditUsd > 0 || description) {
                if (!accountId) {
                    alert('Cada línea de asiento debe tener una cuenta contable seleccionada.');
                    return false;
                }
                var hasDebe = debit > 0 || debitUsd > 0;
                var hasHaber = credit > 0 || creditUsd > 0;
                if (!hasDebe && !hasHaber) {
                    alert('Cada línea de asiento debe tener un monto en Debe o Haber.');
                    return false;
                }
                if (hasDebe && hasHaber) {
                    alert('Cada línea de asiento debe ser Debe o Haber, no ambos.');
                    return false;
                }
                asientoLines.push({
                    account_id: accountId,
                    debit: debit,
                    credit: credit,
                    debit_usd: debitUsd,
                    credit_usd: creditUsd,
                    description: description
                });
            }
        });
        if (asientoLines.length > 0) {
            var totalDebeNio = 0;
            var totalDebeUsd = 0;
            var totalHaberNio = 0;
            var totalHaberUsd = 0;
            asientoLines.forEach(function(line){
                totalDebeNio += parseFloat(line.debit || 0);
                totalDebeUsd += parseFloat(line.debit_usd || 0);
                totalHaberNio += parseFloat(line.credit || 0);
                totalHaberUsd += parseFloat(line.credit_usd || 0);
            });
            if (Math.abs(totalDebeNio - totalHaberNio) > 0.009 || Math.abs(totalDebeUsd - totalHaberUsd) > 0.009) {
                alert('El asiento contable debe estar balanceado en cada moneda: totales Debe y Haber deben coincidir.');
                return;
            }
            payload.asiento_lines = JSON.stringify(asientoLines);
        }
        // Validar monto
        if(isCheque && (!payload.monto_total || payload.monto_total <= 0)){
            alert('Ingrese el monto total.');
            $('[name="monto_total"]:visible').focus();
            return;
        }
        // Agregar tipo de cambio y costos adicionales
        payload.tasa_cambio = parseFloat($('#movimientoTasaGuardada').val() || 1.0);
        var costosAdicionales = [];
        $('#movimientoGastosTable tbody tr').each(function(){
            costosAdicionales.push({
                description: $(this).find('.movimientoGastoDescripcion').val() || 'Costo adicional',
                amount: parseFloat($(this).find('.movimientoGastoMonto').val() || 0)
            });
        });
        if (costosAdicionales.length > 0) {
            payload.costos_adicionales = JSON.stringify(costosAdicionales);
        }
        $.post('<?php echo site_url('tesoreria/save_movimiento_ajax'); ?>', payload)
            .done(function(resp){
                try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ j = null; }
                if(j && j.status){
                    $('#modalMovimiento').modal('hide');
                    alert('Movimiento guardado correctamente.');
                    cargarMovimientos(); // Recargar tabla
                }else{
                    alert((j && j.message)? j.message : 'Error al guardar movimiento.');
                }
            })
            .fail(function(){
                alert('Error en la petición AJAX.');
            });
    });
});

</script>
        </div>
    </div>
</div>

        </div>
    </div>
</div>
