<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="page-header-title d-flex align-items-center">
                            <i class="fas fa-money-check-alt bg-blue"></i>
                            <div class="d-inline ml-3">
                                <h5>Cobros Adicionales</h5>
                                <span>Registre cobros por servicios o productos adicionales</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                        <a href="<?php echo site_url('tesoreria/cobros_list'); ?>" class="btn btn-primary btn-sm btn-icon-text">
                            <i class="fas fa-list mr-2"></i>Ver listado completo
                        </a>
                    </div>
                </div>
            </div>

            <?php $this->load->view('tesoreria/partial_back'); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Nuevo Cobro</h5>
                        </div>
                        <div class="card-body">
                            <form id="formCobro">
                                <!-- Paso 1: Tipo de Pago (Primero) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroTipoPago">Tipo de Pago <span class="text-danger">*</span></label>
                                            <select id="cobroTipoPago" name="tipo_transferencia" class="form-control" required>
                                                <option value="">-- Seleccionar tipo de pago --</option>
                                                <option value="cargo">Efectivo</option>
                                                <option value="abono">Transferencia o Depósito</option>
                                            </select>
                                            <small class="form-text text-muted">Selecciona el medio de pago</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>

                                <!-- Paso 2: Cuenta Destino (Filtrada según Tipo) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroCuenta">Cuenta Destino <span class="text-danger">*</span></label>
                                            <select id="cobroCuenta" name="cuenta_id" class="form-control" required>
                                                <option value="">-- Seleccionar cuenta --</option>
                                            </select>
                                            <small class="form-text text-muted"><span id="txtTipoCuenta">Caja o Banco</span> donde se registrará el ingreso</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cobroNombrePerson">Nombre de la Persona <span class="text-danger">*</span></label>
                                            <input type="text" id="cobroNombrePerson" name="nombre_persona" class="form-control" placeholder="Nombre de quien realiza el cobro" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cobroDescriptor">Descripción del Pago <span class="text-danger">*</span></label>
                                            <input type="text" id="cobroDescriptor" name="descripcion" class="form-control" placeholder="Ej: Venta de producto X, Servicio de consultoría, etc." required />
                                            <small class="form-text text-muted">Descripción clara del servicio o producto cobrado</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroMoneda">Moneda <span class="text-danger">*</span></label>
                                            <select id="cobroMoneda" name="moneda" class="form-control" required>
                                                <option value="NIO">NIO (Córdoba)</option>
                                                <option value="USD">USD (Dólar)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroMonto">Monto <span class="text-danger">*</span></label>
                                            <input type="number" id="cobroMonto" name="monto_total" class="form-control" step="0.01" placeholder="0.00" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="tasaCambioRow" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroTasaCambio">Tasa de Cambio (USD a NIO) <span class="text-danger">*</span></label>
                                            <input type="number" id="cobroTasaCambio" name="tc_aplicada" class="form-control" step="0.01" placeholder="33.50" required />
                                            <small class="form-text text-muted">Editable - Ingresa la tasa actual si cambió</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cobroMontoNIO">Monto en NIO</label>
                                            <input type="number" id="cobroMontoNIO" name="monto_nio_calculado" class="form-control" step="0.01" readonly />
                                            <small class="form-text text-muted">Se calcula automáticamente</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cobroSerie">Serie del Documento <span class="text-danger">*</span></label>
                                            <select id="cobroSerie" name="idserie" class="form-control" required>
                                                <option value="">-- Seleccionar Serie del Documento --</option>
                                            </select>
                                            <small class="form-text text-muted">Seleccione la serie del documento para el recibo</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cobroObservaciones">Observaciones</label>
                                            <textarea id="cobroObservaciones" name="observaciones" class="form-control" rows="3" placeholder="Notas adicionales del cobro..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" id="btnGuardarCobro" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cobro
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resumen del Cobro</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>Tipo:</strong> <span id="resumenTipo">-</span>
                            </div>
                            <div class="alert alert-light">
                                <p class="mb-2">
                                    <strong>Persona:</strong><br>
                                    <span id="resumenPersona">-</span>
                                </p>
                            </div>
                            <div class="alert alert-light">
                                <p class="mb-2">
                                    <strong>Descripción:</strong><br>
                                    <span id="resumenDescripcion">-</span>
                                </p>
                            </div>
                            <div class="alert alert-success">
                                <p class="mb-0">
                                    <strong>Monto Total:</strong><br>
                                    <span id="resumenMonto" style="font-size: 1.4rem; font-weight: bold;">0.00</span>
                                    <span id="resumenMoneda"> NIO</span>
                                </p>
                            </div>
                            <div class="alert alert-light mt-3">
                                <p class="mb-2">
                                    <strong>Cuenta Destino:</strong><br>
                                    <span id="resumenCuenta">-</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Últimos Cobros</h5>
                        </div>
                        <div class="card-body">
                            <div id="ultimosCobros" style="font-size: 0.85rem;">
                                <p class="text-muted">Cargando...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Últimos 5 Cobros</h5>
                            <a href="<?php echo site_url('tesoreria/cobros_list'); ?>" class="btn btn-sm btn-primary">Ver listado completo</a>
                        </div>
                        <div class="card-body" id="ultimosCobrosList">
                            <p class="text-muted">Cargando últimos cobros...</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Editar Cobro -->
<div class="modal fade" id="modalEditarCobro" tabindex="-1" role="dialog" aria-labelledby="modalEditarCobroLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCobroLabel">Editar Cobro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarCobro">
                    <input type="hidden" id="editarCobroId" />
                    <div class="form-group">
                        <label for="editarCobroBeneficiario">Beneficiario</label>
                        <input type="text" id="editarCobroBeneficiario" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroDescripcion">Descripción</label>
                        <input type="text" id="editarCobroDescripcion" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroMonto">Monto</label>
                        <input type="number" id="editarCobroMonto" class="form-control" step="0.01" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroObservaciones">Observaciones</label>
                        <textarea id="editarCobroObservaciones" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCobroEdicion">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Anular Cobro -->
<div class="modal fade" id="modalAnularCobro" tabindex="-1" role="dialog" aria-labelledby="modalAnularCobroLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAnularCobroLabel">Anular Cobro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="anularCobroId" />
                <div class="form-group">
                    <label for="motivoAnulacionCobro">Motivo de anulación</label>
                    <textarea id="motivoAnulacionCobro" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAnularCobro">Anular Cobro</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var siteUrl = '<?php echo site_url('tesoreria'); ?>';
    var tasaCambioActual = null;
    var allCuentas = [];  // Guardar TODAS las cuentas con su tipo

    function formatCurrency(value) {
        var number = parseFloat(value);
        if (isNaN(number)) return '0.00';
        return number.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Nueva función: Filtrar cuentas según tipo de pago
    function filtrarCuentasSegunTipo() {
        var tipoPago = $('#cobroTipoPago').val();
        var selectCuenta = $('#cobroCuenta');
        var txtTipoCuenta = $('#txtTipoCuenta');
        
        selectCuenta.empty();
        selectCuenta.append($('<option>').val('').text('-- Seleccionar cuenta --'));
        
        if (!tipoPago) {
            selectCuenta.append($('<option disabled>').text('Primero selecciona un tipo de pago'));
            txtTipoCuenta.text('Caja o Banco');
            return;
        }
        
        // Determinar qué tipo de cuenta mostrar según el tipo de pago
        var tiposCuentaFiltrados = [];
        var labelCuenta = '';
        
        if (tipoPago === 'cargo') {
            // EFECTIVO → Solo CAJAS
            tiposCuentaFiltrados = ['caja'];
            labelCuenta = 'Cajas';
        } else if (tipoPago === 'abono') {
            // TRANSFERENCIA O DEPÓSITO → Solo BANCOS
            tiposCuentaFiltrados = ['banco'];
            labelCuenta = 'Bancos';
        }
        
        txtTipoCuenta.text(labelCuenta);
        
        // Filtrar y mostrar las cuentas correspondientes
        var cuentasFiltradas = allCuentas.filter(function(c) {
            return tiposCuentaFiltrados.indexOf(c.type) !== -1;
        });
        
        if (cuentasFiltradas.length === 0) {
            selectCuenta.append($('<option disabled>').text('No hay cuentas disponibles para este tipo'));
        } else {
            cuentasFiltradas.forEach(function(c){
                selectCuenta.append(
                    $('<option>').val(c.id).text(c.name + ' (' + c.code + ') - ' + c.type.toUpperCase())
                );
            });
        }
    }

    function cargarDatosIniciales() {
        $.ajax({
            url: siteUrl + '/get_cobro_datos_ajax',
            method: 'GET',
            dataType: 'json'
        }).done(function(resp){
            if (!resp || !resp.status) {
                alert('Error cargando datos.');
                return;
            }

            allCuentas = resp.cuentas || [];

            var series = resp.series || [];
            var selectSerie = $('#cobroSerie');
            selectSerie.empty();
            if (series.length === 0) {
                selectSerie.append($('<option>').val('').text('No hay series disponibles'));
                selectSerie.prop('disabled', true);
            } else {
                selectSerie.append($('<option>').val('').text('-- Seleccionar Serie del Documento --'));
                series.forEach(function(s){
                    selectSerie.append($('<option>').val(s.idserie).text(s.codigo + ' - ' + (s.nombre || '')));
                });
                selectSerie.prop('disabled', false);
            }

            tasaCambioActual = resp.tasa_cambio || 33.50;
            $('#cobroTasaCambio').val(tasaCambioActual.toFixed(2));

            cargarUltimosCobros();
        }).fail(function(){
            console.error('Error al cargar datos iniciales');
        });
    }

    function cargarUltimosCobros() {
        $.ajax({
            url: siteUrl + '/get_ultimos_cobros_ajax',
            method: 'GET',
            dataType: 'json'
        }).done(function(resp){
            if (!resp || !resp.status) return;
            
            var cobros = resp.cobros || [];
            var html = '';
            if (cobros.length === 0) {
                html = '<p class="text-muted">No hay cobros registrados aún.</p>';
            } else {
                cobros.forEach(function(c, index){
                    html += '<div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">';
                    html += '<div>';
                    html += '<p class="mb-1 font-weight-bold">' + (c.descripcion || '-') + '</p>';
                    html += '<p class="mb-1 text-muted mb-0">' + (c.beneficiario || 'Persona desconocida') + '</p>';
                    html += '<p class="mb-0 text-muted" style="font-size: 0.85rem;">' + (c.fecha_registro ? c.fecha_registro.split(' ')[0] : '') + ' · ' + ((c.serie_codigo || '') + ' ' + (c.referencia1 || '')).trim() + '</p>';
                    html += '</div>';
                    html += '<div class="text-right">';
                    html += '<p class="mb-1 font-weight-bold text-success">' + formatCurrency(c.monto_total) + '</p>';
                    html += '<p class="mb-0 text-muted" style="font-size: 0.85rem;">' + (c.moneda || 'NIO') + '</p>';
                    html += '</div>';
                    html += '</div>';
                });
            }
            $('#ultimosCobrosList').html(html);
        }).fail(function(){
            $('#ultimosCobrosList').html('<p class="text-danger">Error al cargar</p>');
        });
    }

    function actualizarResumen() {
        var tipo = $('#cobroTipoPago').val();
        var persona = $('#cobroNombrePerson').val();
        var descripcion = $('#cobroDescriptor').val();
        var monto = parseFloat($('#cobroMonto').val() || 0);
        var moneda = $('#cobroMoneda').val();
        var cuenta = $('#cobroCuenta').find('option:selected').text();

        $('#resumenTipo').text(tipo === 'abono' ? 'Transferencia Bancaria' : tipo === 'cargo' ? 'Efectivo' : '-');
        $('#resumenPersona').text(persona || '-');
        $('#resumenDescripcion').text(descripcion || '-');
        $('#resumenMonto').text(formatCurrency(monto));
        $('#resumenMoneda').text(' ' + moneda);
        $('#resumenCuenta').text(cuenta || '-');
    }

    function calcularMontoNIO() {
        var moneda = $('#cobroMoneda').val();
        var monto = parseFloat($('#cobroMonto').val() || 0);
        var tc = parseFloat($('#cobroTasaCambio').val() || tasaCambioActual || 0);
        
        if (moneda === 'USD' && tc > 0) {
            var montoNIO = monto * tc;
            $('#cobroMontoNIO').val(montoNIO.toFixed(2));
        } else {
            $('#cobroMontoNIO').val(monto.toFixed(2));
        }
    }

    $(document).ready(function(){
        cargarDatosIniciales();

        // EVENTO: Cuando cambia el Tipo de Pago - filtrar cuentas
        $('#cobroTipoPago').on('change', function(){
            filtrarCuentasSegunTipo();
            actualizarResumen();
        });

        // EVENTO: Cuando cambia la Moneda
        $('#cobroMoneda').on('change', function(){
            var moneda = $(this).val();
            if (moneda === 'USD') {
                $('#tasaCambioRow').show();
                calcularMontoNIO();
            } else {
                $('#tasaCambioRow').hide();
                $('#cobroMontoNIO').val('');
            }
            actualizarResumen();
        });

        // EVENTO: Cuando cambia la Tasa de Cambio - recalcular monto NIO
        $('#cobroTasaCambio').on('change input', function(){
            calcularMontoNIO();
        });

        $('#cobroMonto, #cobroNombrePerson, #cobroDescriptor, #cobroTipoPago, #cobroCuenta').on('change input', function(){
            actualizarResumen();
            if ($(this).attr('id') === 'cobroMonto') {
                calcularMontoNIO();
            }
        });

        $('#formCobro').on('submit', function(e){
            e.preventDefault();
            guardarCobro();
        });
    });

    function guardarCobro() {
        var cuentaId = $('#cobroCuenta').val();
        var tipo = $('#cobroTipoPago').val();
        var nombre = $('#cobroNombrePerson').val();
        var descripcion = $('#cobroDescriptor').val();
        var monto = parseFloat($('#cobroMonto').val() || 0);
        var moneda = $('#cobroMoneda').val();
        var tc = parseFloat($('#cobroTasaCambio').val() || 0);
        var idserie = $('#cobroSerie').val() || null;
        var observaciones = $('#cobroObservaciones').val() || '';

        if (!cuentaId || !tipo || !nombre || !descripcion || monto <= 0 || !idserie) {
            alert('Complete todos los campos obligatorios y seleccione la Serie del Documento');
            return;
        }

        $('#btnGuardarCobro').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: siteUrl + '/save_cobro_ajax',
            method: 'POST',
            dataType: 'json',
            data: {
                cuenta_id: cuentaId,
                tipo_transferencia: tipo,
                nombre_persona: nombre,
                descripcion: descripcion,
                monto_total: monto,
                moneda: moneda,
                tc_aplicada: tc,
                idserie: idserie,
                observaciones: observaciones
            }
        }).done(function(resp){
            $('#btnGuardarCobro').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Cobro');
            if (resp && resp.status) {
                alert('Cobro registrado exitosamente');
                if (resp.recibo_url) {
                    window.open(resp.recibo_url, '_blank');
                }
                $('#formCobro')[0].reset();
                $('#tasaCambioRow').hide();
                actualizarResumen();
                cargarUltimosCobros();
                cargarCobrosListado();
            } else {
                alert('Error: ' + (resp.message || 'No se pudo guardar'));
            }
        }).fail(function(){
            $('#btnGuardarCobro').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Cobro');
            alert('Error de red al guardar cobro');
        });
    }

    function cargarCobrosListado() {
        $.ajax({
            url: siteUrl + '/get_cobros_ajax',
            method: 'GET',
            dataType: 'json'
        }).done(function(resp){
            var tbody = $('#cobrosTabla tbody');
            var searchTerm = ($('#cobrosSearch').val() || '').toLowerCase();
            var estadoFilter = ($('#cobrosFilterEstado').val() || '').toLowerCase();
            tbody.empty();
            if (!resp || !resp.status || !Array.isArray(resp.cobros) || resp.cobros.length === 0) {
                $('#cobrosSinDatos').text('No se encontraron cobros.');
                return;
            }

            var visibleCount = 0;
            resp.cobros.forEach(function(c, index) {
                var almacen = [
                    (c.descripcion || '').toLowerCase(),
                    (c.beneficiario || '').toLowerCase(),
                    (c.cuenta_nombre || '').toLowerCase(),
                    (c.cuenta_codigo || '').toLowerCase(),
                    (c.serie_codigo || '').toLowerCase(),
                    (c.referencia1 || '').toLowerCase(),
                    (c.estado || '').toLowerCase()
                ];
                var matchesSearch = searchTerm === '' || almacen.join(' ').indexOf(searchTerm) !== -1;
                var matchesEstado = estadoFilter === '' || (c.estado || '').toLowerCase() === estadoFilter;
                if (!matchesSearch || !matchesEstado) {
                    return;
                }

                visibleCount++;
                var montoTexto = formatCurrency(c.monto_total) + ' ' + (c.moneda || 'NIO');
                var estadoTexto = c.estado ? c.estado.charAt(0).toUpperCase() + c.estado.slice(1) : 'N/A';
                var acciones = '';
                acciones += '<button type="button" class="btn btn-sm btn-info mr-1 btn-ver-recibo" data-id="' + c.id + '"><i class="fas fa-print"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-success mr-1 btnContabilizarMov" data-id="' + c.id + '"><i class="fas fa-balance-scale"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-warning mr-1 btn-editar-cobro" data-id="' + c.id + '" data-beneficiario="' + escapeHtml(c.beneficiario) + '" data-descripcion="' + escapeHtml(c.descripcion) + '" data-monto="' + parseFloat(c.monto_total) + '" data-observaciones="' + escapeHtml(c.observaciones || '') + '"><i class="fas fa-edit"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-danger btn-eliminar-cobro" data-id="' + c.id + '"><i class="fas fa-trash"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-secondary btn-anular-cobro ml-1" data-id="' + c.id + '"><i class="fas fa-ban"></i></button>';

                tbody.append('<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + (c.fecha_registro ? c.fecha_registro.split(' ')[0] : '') + '</td>' +
                    '<td>' + (c.beneficiario || '') + '</td>' +
                    '<td>' + (c.descripcion || '') + '</td>' +
                    '<td>' + (c.cuenta_nombre ? c.cuenta_nombre + ' (' + (c.cuenta_codigo || '') + ')' : '-') + '</td>' +
                    '<td>' + ((c.serie_codigo || '') + ' ' + (c.referencia1 || '')) + '</td>' +
                    '<td>' + montoTexto + '</td>' +
                    '<td>' + estadoTexto + '</td>' +
                    '<td>' + acciones + '</td>' +
                '</tr>');
            });

            if (visibleCount === 0) {
                $('#cobrosSinDatos').text('No se encontraron cobros con los filtros aplicados.');
            } else {
                $('#cobrosSinDatos').text('');
            }
        }).fail(function(){
            $('#cobrosSinDatos').text('Error al cargar el listado de cobros.');
        });
    }

    function abrirModalEditarCobro(id, beneficiario, descripcion, monto, observaciones) {
        $('#editarCobroId').val(id);
        $('#editarCobroBeneficiario').val(beneficiario);
        $('#editarCobroDescripcion').val(descripcion);
        $('#editarCobroMonto').val(parseFloat(monto || 0).toFixed(2));
        $('#editarCobroObservaciones').val(observaciones || '');
        $('#modalEditarCobro').modal('show');
    }

    function guardarEdicionCobro() {
        var id = parseInt($('#editarCobroId').val(), 10);
        var beneficiario = $('#editarCobroBeneficiario').val();
        var descripcion = $('#editarCobroDescripcion').val();
        var monto = parseFloat($('#editarCobroMonto').val() || 0);
        var observaciones = $('#editarCobroObservaciones').val();

        if (!id || beneficiario.trim() === '' || descripcion.trim() === '' || monto <= 0) {
            alert('Complete los campos obligatorios antes de guardar.');
            return;
        }

        $('#btnGuardarCobroEdicion').prop('disabled', true).text('Guardando...');
        $.ajax({
            url: siteUrl + '/update_cobro_ajax',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                beneficiario: beneficiario,
                descripcion: descripcion,
                monto_total: monto,
                observaciones: observaciones
            }
        }).done(function(resp){
            $('#btnGuardarCobroEdicion').prop('disabled', false).text('Guardar Cambios');
            if (resp && resp.status) {
                $('#modalEditarCobro').modal('hide');
                cargarCobrosListado();
                cargarUltimosCobros();
                alert('Cobro actualizado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo actualizar el cobro.'));
            }
        }).fail(function(){
            $('#btnGuardarCobroEdicion').prop('disabled', false).text('Guardar Cambios');
            alert('Error de red al actualizar el cobro.');
        });
    }

    function abrirModalAnularCobro(id) {
        $('#anularCobroId').val(id);
        $('#motivoAnulacionCobro').val('');
        $('#modalAnularCobro').modal('show');
    }

    function confirmarAnularCobro() {
        var id = parseInt($('#anularCobroId').val(), 10);
        var motivo = $('#motivoAnulacionCobro').val();
        if (!id || motivo.trim() === '') {
            alert('Ingrese el motivo de anulación.');
            return;
        }

        $('#btnConfirmarAnularCobro').prop('disabled', true).text('Anulando...');
        $.ajax({
            url: siteUrl + '/anular_movimiento_ajax',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                motivo: motivo
            }
        }).done(function(resp){
            $('#btnConfirmarAnularCobro').prop('disabled', false).text('Anular Cobro');
            if (resp && resp.status) {
                $('#modalAnularCobro').modal('hide');
                cargarCobrosListado();
                cargarUltimosCobros();
                alert('Cobro anulado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo anular el cobro.'));
            }
        }).fail(function(){
            $('#btnConfirmarAnularCobro').prop('disabled', false).text('Anular Cobro');
            alert('Error de red al anular el cobro.');
        });
    }

    function eliminarCobro(id) {
        if (!confirm('¿Está seguro de eliminar este cobro? Esta acción ajustará el saldo de la cuenta.')) {
            return;
        }
        $.ajax({
            url: siteUrl + '/delete_cobro_ajax',
            method: 'POST',
            dataType: 'json',
            data: { id: id }
        }).done(function(resp){
            if (resp && resp.status) {
                cargarCobrosListado();
                cargarUltimosCobros();
                alert('Cobro eliminado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo eliminar el cobro.'));
            }
        }).fail(function(){
            alert('Error de red al eliminar el cobro.');
        });
    }

    $(document).ready(function(){
        cargarDatosIniciales();
        cargarUltimosCobros();

        $(document).on('click', '.btn-editar-cobro', function(){
            abrirModalEditarCobro(
                $(this).data('id'),
                $(this).data('beneficiario'),
                $(this).data('descripcion'),
                $(this).data('monto'),
                $(this).data('observaciones')
            );
        });

        $(document).on('click', '.btn-anular-cobro', function(){
            abrirModalAnularCobro($(this).data('id'));
        });

        $(document).on('click', '.btn-eliminar-cobro', function(){
            eliminarCobro($(this).data('id'));
        });

        $(document).on('click', '.btn-ver-recibo', function(){
            var id = $(this).data('id');
            if (id) {
                window.open(siteUrl + '/recibo_cobro/' + id, '_blank');
            }
        });

        $('#btnGuardarCobroEdicion').on('click', guardarEdicionCobro);
        $('#btnConfirmarAnularCobro').on('click', confirmarAnularCobro);
    });
})();
</script>
<script src="<?php echo base_url('public/js/contabilidad_modal_enhanced.js'); ?>?v=<?php echo time(); ?>"></script>

