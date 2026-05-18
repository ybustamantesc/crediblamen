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
        $('#previewDetalleFecha').text(meta.fechaDesembolso || movimiento.fecha_registro || '-');
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

    function renderDetalleMovimientoGeneral(movimiento) {
        var tipo = (movimiento.tipo_transferencia || movimiento.tipo_movimiento || movimiento.tipo || '-');
        $('#previewMovTipo').text(tipo || '-');
        $('#previewMovFormaPago').text(movimiento.forma_pago || '-');
        $('#previewMovMonto').text(formatearMoneda(movimiento.monto_total || 0));
        $('#previewMovFechaRegistro').text(movimiento.fecha_registro || '-');
        $('#previewMovFechaAplicacion').text(movimiento.fecha_aplicacion || '-');
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
                // Cargar modal de asiento contable
                $.get('<?php echo site_url('contabilidad/modal_add'); ?>', function(html){
                    var $modal = $(html);
                    // Asegurar que el ID esté disponible para el modal
                    window.CONTABILIZAR_MOV_ID = id;
                    // Prellenar campos principales
                    $modal.find('input[name="date"]').val(j.movimiento.fecha_registro);
                    $modal.find('textarea[name="description"]').val(j.movimiento.descripcion || '');
                    // Mostrar modal
                    $('body').append($modal);
                    // Inicializar Select2 y AJAX para cuentas contables
                    if (typeof attachModalEvents === 'function') {
                        attachModalEvents();
                    }
                    // Cerrar modal al hacer clic en la X o fondo
                    $modal.find('#btnCancelModal').on('click', function(){ $modal.remove(); });
                    $modal.on('click', function(e){ if(e.target === this) $modal.remove(); });
                });
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
                        <div class="card-body">
                            <div class="btn-group mb-3 movimientos-toolbar" role="group" aria-label="Tipos de movimiento">
                                <button class="btn btn-outline-primary" id="btnMovTransferencia"><i class="fas fa-exchange-alt"></i> Transferencia</button>
                                <button class="btn btn-outline-success" id="btnMovEfectivo"><i class="fas fa-money-bill-wave"></i> Efectivo</button>
                                <button class="btn btn-outline-info" id="btnMovCheque"><i class="fas fa-money-check"></i> Cheque</button>
                                <button class="btn btn-outline-warning" id="btnMovTraslado"><i class="fas fa-random"></i> Traslado entre cuentas</button>
                                <button class="btn btn-outline-secondary" id="btnMovOtros"><i class="fas fa-ellipsis-h"></i> Otros</button>
                            </div>
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
                                        margin-bottom: 0;
                                        table-layout: fixed;
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
                                        width: 42px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(2),
                                    #tablaMovimientos td:nth-child(2) {
                                        width: 112px;
                                    }
                                    #tablaMovimientos th:nth-child(3),
                                    #tablaMovimientos td:nth-child(3) {
                                        width: 72px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(4),
                                    #tablaMovimientos td:nth-child(4) {
                                        width: 96px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(5),
                                    #tablaMovimientos td:nth-child(5),
                                    #tablaMovimientos th:nth-child(6),
                                    #tablaMovimientos td:nth-child(6) {
                                        width: 92px;
                                        text-align: center;
                                    }
                                    #tablaMovimientos th:nth-child(7),
                                    #tablaMovimientos td:nth-child(7) {
                                        width: 112px;
                                    }
                                    #tablaMovimientos th:nth-child(8),
                                    #tablaMovimientos td:nth-child(8) {
                                        width: 90px;
                                        text-align: right;
                                    }
                                    #tablaMovimientos th:nth-child(9),
                                    #tablaMovimientos td:nth-child(9) {
                                        width: 112px;
                                    }
                                    #tablaMovimientos th:nth-child(10),
                                    #tablaMovimientos td:nth-child(10) {
                                        width: 92px;
                                    }
                                    #tablaMovimientos th:nth-child(11),
                                    #tablaMovimientos td:nth-child(11) {
                                        width: 92px;
                                    }
                                    #tablaMovimientos th:nth-child(12),
                                    #tablaMovimientos td:nth-child(12) {
                                        width: 120px;
                                    }
                                    #tablaMovimientos th:nth-child(13),
                                    #tablaMovimientos td:nth-child(13) {
                                        width: 150px;
                                    }
                                    #tablaMovimientos .mov-ref-cell {
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                    }
                                    #tablaMovimientos .mov-desc-cell {
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                        display: -webkit-box;
                                        -webkit-line-clamp: 1;
                                        -webkit-box-orient: vertical;
                                    }
                                    #tablaMovimientos .mov-user-cell {
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                    }
                                    #tablaMovimientos .acciones-cell {
                                        display: flex;
                                        flex-wrap: nowrap;
                                        gap: 2px;
                                        align-items: center;
                                        white-space: nowrap;
                                        justify-content: flex-start;
                                    }
                                    #tablaMovimientos .acciones-cell .btn {
                                        padding: .14rem .28rem;
                                        font-size: .67rem;
                                        margin: 0;
                                        min-width: 0;
                                        border-radius: 8px;
                                        font-weight: 600;
                                        border-width: 1px;
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
                                    #tablaMovimientos .btn-mov-view {
                                        background: #e9f7fb;
                                        color: #17637a;
                                        border-color: #bee8f3;
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
                                            flex-wrap: wrap;
                                        }
                                        #tablaMovimientos .acciones-cell .btn {
                                            width: auto;
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
                                            <th>Referencia 1</th>
                                            <th>Referencia 2</th>
                                            <th>Descripción</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para registrar movimiento -->
            <div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalMovimientoLabel"><i class="fas fa-exchange-alt"></i> Registrar Transferencia</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form id="formMovimiento">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Cuenta de origen <span class="text-danger">*</span></label>
                                        <select class="form-control" name="cuenta_id" id="selectCuentaOrigen" required>
                                            <option value="">Seleccione...</option>
                                            <?php if(isset($cuentas) && is_array($cuentas)) foreach($cuentas as $c): ?>
                                                <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?> (<?php echo $c->code; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tipo de transferencia <span class="text-danger">*</span></label>
                                        <select class="form-control" name="tipo_transferencia" required>
                                            <option value="">Seleccione...</option>
                                            <option value="cargo">Cargo (Egreso)</option>
                                            <option value="abono">Abono (Ingreso)</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Forma de pago</label>
                                        <input type="text" class="form-control" name="forma_pago" value="TRANSFERENCIA" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Fecha de registro</label>
                                        <input type="date" class="form-control" name="fecha_registro" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Fecha de aplicación</label>
                                        <input type="date" class="form-control" name="fecha_aplicacion" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Beneficiario</label>
                                        <input type="text" class="form-control" name="beneficiario">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Referencia 1</label>
                                        <input type="text" class="form-control" name="referencia1">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Referencia 2</label>
                                        <input type="text" class="form-control" name="referencia2">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Monto total</label>
                                        <input type="number" step="0.01" class="form-control" name="monto_total" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>IVA Total</label>
                                        <input type="number" step="0.01" class="form-control" name="iva_total">
                                    </div>
                                </div>
                                <!-- Departamento, Centro de costos y Proyecto ocultos y enviados como null -->
                                <input type="hidden" name="departamento" value="" />
                                <input type="hidden" name="centro_costos" value="" />
                                <input type="hidden" name="proyecto" value="" />
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Descripción</label>
                                        <textarea class="form-control" name="descripcion" rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnGuardarMovimiento">Guardar</button>
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

                        // Construir el nuevo layout para cheque si no existe
                        if ($('#chequeCustomLayout').length === 0) {
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
                                        <div class="form-group col-md-3">
                                            <label>IVA Total</label>
                                            <input type="number" step="0.01" class="form-control" name="iva_total" value="0.00" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Forma de pago</label>
                                            <input type="text" class="form-control" name="forma_pago" value="CHEQUE" readonly />
                                        </div>
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
                        } else {
                                $('#chequeCustomLayout').show();
                        }

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

                        // Al cerrar el modal, restaurar el formulario original
                        $('#modalMovimiento').off('hidden.bs.modal.cheque').on('hidden.bs.modal.cheque', function(){
                                $('#chequeCustomLayout').hide();
                                $('#formMovimiento .form-row, #formMovimiento .form-group').show();
                        });
                });
    // Filtrar cuentas por tipo
    function getCuentasPorTipo(tipo) {
        var cuentas = <?php echo json_encode($cuentas); ?>;
        return cuentas.filter(function(c) { return c.type === tipo && c.estado == 1; });
    }
    // Cargar movimientos al iniciar
    function cargarMovimientos(){
        var desde = $('#filtroDesde').val();
        var hasta = $('#filtroHasta').val();
        var tipoDoc = $('#filtroTipoDoc').val();
        $.get('<?php echo site_url('tesoreria/get_movimientos_ajax'); ?>', {desde: desde, hasta: hasta, tipo: tipoDoc}, function(resp){
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
                    } else if(mov.estado !== 'anulado') {
                        acciones = '<button class="btn btn-sm btn-mov-view btnVerCheque" data-id="'+mov.id+'">Ver</button> ';
                        if (esDesembolso) {
                            acciones += '<button class="btn btn-sm btn-mov-muted" disabled style="pointer-events:none;opacity:0.75;">Anular bloqueado</button> ';
                        } else {
                            acciones += '<button class="btn btn-sm btn-mov-anular btnAnularMov" data-id="'+mov.id+'">Anular</button> ';
                        }
                        if (mov.tiene_asiento == 1) {
                            acciones += '<button class="btn btn-sm btn-mov-muted btnContabilizarMov" data-id="'+mov.id+'" disabled style="pointer-events:none;opacity:0.75;">Contabilizar</button>';
                        } else {
                            acciones += '<button class="btn btn-sm btn-mov-conta btnContabilizarMov" data-id="'+mov.id+'">Contabilizar</button>';
                        }
                    } else {
                        acciones = '<button class="btn btn-sm btn-mov-view btnVerCheque" data-id="'+mov.id+'">Ver</button> ' + motivoAnulacion;
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
                    $tbody.append(
                        '<tr class="'+rowClass+'">'+
                        '<td>'+mov.id+'</td>'+
                        '<td>'+(mov.cuenta_nombre || mov.cuenta_id)+'</td>'+
                        '<td>'+badge+'</td>'+
                        '<td>'+mov.forma_pago+'</td>'+
                        '<td>'+mov.fecha_registro+'</td>'+
                        '<td>'+(mov.fecha_aplicacion || '')+'</td>'+
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
        });
    }
    cargarMovimientos();
    // Filtros
    $('#btnFiltrarMov').on('click', function(){
        cargarMovimientos();
    });
    $('#filtroDesde, #filtroHasta, #filtroTipoDoc').on('change', function(){
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
        $('#modalMovimiento').modal('show');
    });

    // Al hacer clic en Efectivo, mostrar solo cuentas tipo caja
    $('#btnMovEfectivo').on('click', function(e){
        $('#rowCheque').hide();
        $('#grupoTipoTransferencia').show();
        $('[name="beneficiario"]').closest('.form-group').show();
        $('[name="forma_pago"]').val('EFECTIVO');
        // Cambiar label de tipo_transferencia a 'Tipo de Transacción'
        var $labelTipo = $("#formMovimiento label[for='tipo_transferencia'], #formMovimiento label:contains('Tipo de transferencia')");
        $labelTipo.text('Tipo de Transacción *');
        e.preventDefault();
        $('#formMovimiento')[0].reset();
        var hoy = new Date().toISOString().slice(0,10);
        $('[name="fecha_registro"], [name="fecha_aplicacion"]').val(hoy);
        $('#modalMovimientoLabel').text('Registrar Efectivo');
        $('[name="forma_pago"]').val('EFECTIVO');
        // Mostrar solo cuentas tipo caja
        var $select = $('#selectCuentaOrigen');
        $select.empty().append('<option value="">Seleccione...</option>');
        getCuentasPorTipo('caja').forEach(function(c) {
            $select.append('<option value="'+c.id+'">'+c.name+' ('+c.code+')</option>');
        });
        $('#modalMovimiento').modal('show');
        // Restaurar el label al cerrar el modal
        $('#modalMovimiento').off('hidden.bs.modal.efectivo').on('hidden.bs.modal.efectivo', function(){
            $labelTipo.text('Tipo de transferencia *');
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
            payload.iva_total = parseFloat($('[name="iva_total"]:visible').val() || 0);
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
            payload.iva_total = parseFloat(payload.iva_total || 0);
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
        // Validar monto
        if(isCheque && (!payload.monto_total || payload.monto_total <= 0)){
            alert('Ingrese el monto total.');
            $('[name="monto_total"]:visible').focus();
            return;
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
