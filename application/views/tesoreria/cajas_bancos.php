<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <style>
                .teso-cb-card {
                    border: 1px solid #e3ebf7;
                    border-radius: 12px;
                    box-shadow: 0 6px 16px rgba(2, 48, 71, .05);
                }
                .teso-cb-toolbar {
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    background: #f8fbff;
                    padding: 10px 12px;
                    margin-bottom: 12px;
                }
                .teso-cb-toolbar .form-control,
                .teso-cb-toolbar .custom-select {
                    border-radius: 8px;
                }
                #cuentas-table.table-compact {
                    width: 100%;
                    background: #fff;
                    border-radius: .4rem;
                    margin-bottom: 0;
                    font-size: .88rem;
                }
                #cuentas-table.table-compact th,
                #cuentas-table.table-compact td {
                    padding: .42rem .55rem;
                    vertical-align: middle;
                    white-space: nowrap;
                }
                #cuentas-table thead th {
                    background: #f5f8fe;
                    color: #1f3c73;
                    font-weight: 700;
                    border-bottom: 2px solid #d9e4f5;
                }
                #cuentas-table tbody tr {
                    transition: background .2s ease;
                }
                #cuentas-table tbody tr:hover {
                    background: #f2f7ff;
                }
                #cuentas-table td:last-child .btn {
                    border-radius: 7px;
                    font-weight: 600;
                    padding: .24rem .55rem;
                }
                #cuentas-table .badge {
                    font-size: .72rem;
                    letter-spacing: .3px;
                    padding: .36em .56em;
                    border-radius: .45rem;
                }
                @media (max-width: 767.98px) {
                    #cuentas-table.table-compact th,
                    #cuentas-table.table-compact td {
                        white-space: normal;
                        font-size: .82rem;
                    }
                }
            </style>
            <?php $this->load->view('tesoreria/partial_back'); ?>
            <div class="page-header mb-3">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-university bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo isset($default_tipo) ? ($default_tipo === 'caja' ? 'Cajas' : 'Bancos') : 'Cajas y Bancos'; ?></h5>
                                <span><?php echo isset($default_tipo)
                                    ? ($default_tipo === 'caja'
                                        ? 'Gestione solo las cajas internas (crear, editar, eliminar y consultar saldos).'
                                        : 'Gestione solo las cuentas bancarias (crear, editar, eliminar y consultar saldos).')
                                    : 'Gestione cuentas bancarias y cajas internas (crear, editar, eliminar y consultar saldos).'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                        <?php if (!isset($default_tipo)): ?>
                            <button id="btnNewCuenta" class="btn bg-blue text-white"><i class="fas fa-plus-circle"></i> Nueva Cuenta</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <?php $activeTipo = isset($default_tipo) ? $default_tipo : 'all'; ?>
                <a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="btn btn-sm <?php echo $activeTipo === 'all' ? 'btn-primary text-white' : 'btn-outline-primary'; ?>">Todos</a>
                <a href="<?php echo base_url('tesoreria/cajas'); ?>" class="btn btn-sm <?php echo $activeTipo === 'caja' ? 'btn-primary text-white' : 'btn-outline-primary'; ?>">Solo Cajas</a>
                <a href="<?php echo base_url('tesoreria/bancos'); ?>" class="btn btn-sm <?php echo $activeTipo === 'banco' ? 'btn-primary text-white' : 'btn-outline-primary'; ?>">Solo Bancos</a>
            </div>

            <div class="card teso-cb-card">
                <div class="card-body">
                    <div class="teso-cb-toolbar">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <input id="cb_search" class="form-control" placeholder="Buscar por código, nombre, banco o cuenta..." />
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select id="cb_filter_tipo" class="custom-select">
                                    <option value="all">Todos los tipos</option>
                                    <option value="caja">Caja</option>
                                    <option value="banco">Cuenta Bancaria</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select id="cb_filter_estado" class="custom-select">
                                    <option value="all">Todos los estados</option>
                                    <option value="ACTIVO">Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-md-right">
                                <small class="text-muted">Filtro rápido</small>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="cuentas-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Banco</th>
                                    <th>Cuenta</th>
                                    <th>Moneda</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- llenado por JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal avanzado con pestañas -->
            <div class="modal fade" id="cuentaModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar / Editar Cuenta Bancaria</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="cuenta_id" />
                            <ul class="nav nav-tabs" id="tabCuenta" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="tab-datos-cuenta" data-toggle="tab" href="#datos-cuenta" role="tab">Datos de la cuenta</a></li>
                                <li class="nav-item"><a class="nav-link" id="tab-datos-banco" data-toggle="tab" href="#datos-banco" role="tab">Datos del Banco</a></li>
                                <li class="nav-item"><a class="nav-link" id="tab-montos" data-toggle="tab" href="#montos" role="tab">Montos</a></li>
                            </ul>
                            <div class="tab-content mt-3">
                                <!-- DATOS DE LA CUENTA -->
                                <div class="tab-pane fade show active" id="datos-cuenta" role="tabpanel">
                                    <div class="form-row">
                                                                                                                    <div class="form-group col-md-4">
                                                                                                                        <label>Tipo de cuenta</label>
                                                                                                                        <select id="cuenta_type" class="form-control" required>
                                                                                                                            <option value="">Seleccione...</option>
                                                                                                                            <option value="caja">Caja</option>
                                                                                                                            <option value="banco">Cuenta Bancaria</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                <div class="form-group col-md-4">
                                                                                    <label>Símbolo</label>
                                                                                    <input id="cuenta_currency_symbol" class="form-control" placeholder="$" />
                                                                                </div>
                                        <div class="form-group col-md-3">
                                            <label>Clave</label>
                                            <input id="cuenta_code" class="form-control" readonly disabled placeholder="Automática" />
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label>Núm. Cuenta</label>
                                            <input id="cuenta_account_number_1" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Fecha de apertura</label>
                                            <input id="cuenta_fecha_apertura_tab1" type="date" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-5">
                                            <label>CLABE Interbancaria</label>
                                            <input id="cuenta_clabe" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Siguiente cheque a emitir</label>
                                            <input id="cuenta_sig_cheque" type="number" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Día de corte</label>
                                            <input id="cuenta_dia_corte" type="number" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-2 d-flex align-items-end">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="cuenta_ultimo_dia_mes">
                                                <label class="form-check-label" for="cuenta_ultimo_dia_mes">Usar último día</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Formato</label>
                                            <input id="cuenta_formato" class="form-control" placeholder="(opcional)" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Moneda</label>
                                            <select id="cuenta_currency" class="form-control">
                                                <option value="NIO">NIO - Córdobas</option>
                                                <option value="USD">USD - Dólares</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Estado</label>
                                            <select id="cuenta_estado" class="form-control"><option value="1">Activo</option><option value="0">Inactivo</option></select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Cuenta contable</label>
                                            <input id="cuenta_contable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <!-- DATOS DEL BANCO -->
                                <div class="tab-pane fade" id="datos-banco" role="tabpanel">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Banco</label>
                                            <input id="cuenta_bank_name" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Nombre de la cuenta</label>
                                            <input id="cuenta_name" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Clave del Banco</label>
                                            <input id="cuenta_clave_banco" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Sucursal</label>
                                            <input id="cuenta_sucursal" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Funcionario</label>
                                            <input id="cuenta_funcionario" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Teléfono</label>
                                            <input id="cuenta_telefono" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Clave</label>
                                            <input id="cuenta_code" class="form-control" readonly disabled placeholder="Automática" />
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label>Núm. Cuenta</label>
                                            <input id="cuenta_account_number_2" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Fecha de apertura</label>
                                            <input id="cuenta_fecha_apertura_tab2" type="date" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cuenta_banco_extranjero">
                                        <label class="form-check-label" for="cuenta_banco_extranjero">Es banco extranjero</label>
                                    </div>
                                </div>
                                <!-- MONTOS -->
                                <div class="tab-pane fade" id="montos" role="tabpanel">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Saldo inicial</label>
                                            <input id="cuenta_saldo_inicial" type="number" step="0.01" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Total de cargos</label>
                                            <input id="cuenta_total_cargos" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Total de abonos</label>
                                            <input id="cuenta_total_abonos" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Saldo actual</label>
                                            <input id="cuenta_saldo_actual" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Saldo conciliado</label>
                                            <input id="cuenta_saldo_conciliado" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Cargos en tránsito</label>
                                            <input id="cuenta_cargos_transito" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Abonos en tránsito</label>
                                            <input id="cuenta_abonos_transito" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Montos en tránsito</label>
                                            <input id="cuenta_montos_transito" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Saldos sin tránsito</label>
                                            <input id="cuenta_saldos_sin_transito" type="number" step="0.01" class="form-control" readonly disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button id="cuenta_save" type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

</div>

<script>
jQuery(function($){
    var cuentasData = [];
    var defaultTipo = '<?php echo isset($default_tipo) ? $default_tipo : 'all'; ?>';
    if (defaultTipo !== 'all') {
        $('#cb_filter_tipo').val(defaultTipo).prop('disabled', true);
        $('#cb_filter_tipo').closest('.col-md-3').append('<small class="form-text text-muted">Vista filtrada: ' + (defaultTipo === 'caja' ? 'Cajas' : 'Bancos') + '</small>');
    }

    function applyClientFilters(){
        var q = ($('#cb_search').val() || '').toLowerCase().trim();
        var tipo = ($('#cb_filter_tipo').val() || 'all').toLowerCase();
        var estado = ($('#cb_filter_estado').val() || 'all').toUpperCase();

        $('#cuentas-table tbody tr').each(function(){
            var $tr = $(this);
            var rowText = $tr.text().toLowerCase();
            var rowTipo = ($tr.children().eq(3).text() || '').toLowerCase().trim();
            var rowEstado = $tr.find('td').eq(7).text().toUpperCase().trim();

            var okQ = (q === '' || rowText.indexOf(q) !== -1);
            var okTipo = (tipo === 'all' || rowTipo === tipo);
            var okEstado = (estado === 'ALL' || rowEstado.indexOf(estado) !== -1);

            $tr.toggle(okQ && okTipo && okEstado);
        });
    }

    function loadCuentas(){
        $.getJSON('<?php echo base_url('tesoreria/get_cuentas_with_saldo_ajax'); ?>')
        .done(function(resp){
            console.log('Respuesta AJAX cuentas:', resp);
            if(!resp || !resp.status) {
                console.error('Respuesta inválida o status false:', resp);
                $('#cuentas-table tbody').html('<tr><td colspan="9" class="text-danger">Error: respuesta inválida del servidor</td></tr>');
                return;
            }
            var $tb = $('#cuentas-table tbody'); $tb.empty();
            if(!Array.isArray(resp.cuentas)) {
                console.error('El campo cuentas no es un array:', resp.cuentas);
                    $tb.html('<tr><td colspan="9" class="text-danger">Error: formato de cuentas incorrecto</td></tr>');
                return;
            }
            cuentasData = resp.cuentas;
            resp.cuentas.forEach(function(c){
                var nombre = c.name || c.nombre || '';
                var estado = (c.estado==1) ? '<span class="badge badge-success">ACTIVO</span>' : '<span class="badge badge-warning">INACTIVO</span>';
                var saldoRaw = (typeof c.saldo !== 'undefined' && !isNaN(parseFloat(c.saldo))) ? parseFloat(c.saldo) : 0;
                var saldo = saldoRaw.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                var tasa = 36.5; // Conversión simple para visualizar ambas monedas
                var saldo_nio = '';
                var saldo_usd = '';
                if (c.currency === 'USD') {
                    saldo_usd = saldo;
                    saldo_nio = (saldoRaw * tasa).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else {
                    saldo_nio = saldo;
                    saldo_usd = (saldoRaw / tasa).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                var tipoRaw = (c.type || '').toString().trim().toLowerCase();
                var tipoLabel = tipoRaw === 'banco' ? 'Banco' : tipoRaw === 'caja' ? 'Caja' : (tipoRaw.length > 0 ? tipoRaw.charAt(0).toUpperCase() + tipoRaw.slice(1) : '');

                var saldoBtn = (defaultTipo !== 'all') ? '<button class="btn btn-sm btn-info btn-caja-saldo" data-id="'+c.id+'" data-currency="'+(c.currency||'')+'">Saldo</button> ' : '';
                var tr = '<tr data-id="'+c.id+'">' 
                    +'<td>'+c.id+'</td>'
                    +'<td>'+ (c.code||'') +'</td>'
                    +'<td>'+ nombre +'</td>'
                    +'<td>'+ tipoLabel +'</td>'
                    +'<td>'+ (c.bank_name||'') +'</td>'
                    +'<td>'+ (c.account_number||'') +'</td>'
                    +'<td>'+ (c.currency||'') + (c.currency_symbol? ' ('+c.currency_symbol+')':'') +'</td>'
                    +'<td>'+ estado +'</td>'
                    +'<td>'+ saldoBtn +'<button class="btn btn-sm btn-info btn-edit" data-id="'+c.id+'">Editar</button> '
                    +'<a class="btn btn-sm btn-danger" href="<?php echo base_url('tesoreria/del_cuenta/'); ?>'+c.id+'" onclick="return confirm(\'Confirmar eliminación\')">Eliminar</a></td>'
                    +'</tr>';
                $tb.append(tr);
            });

            applyClientFilters();
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            console.error('Error AJAX:', textStatus, errorThrown, jqXHR.responseText);
            $('#cuentas-table tbody').html('<tr><td colspan="9" class="text-danger">Error cargando cuentas (AJAX)</td></tr>');
        });
    }

    $('#cb_search, #cb_filter_tipo, #cb_filter_estado').on('input change', function(){
        applyClientFilters();
    });

    $(document).on('click', '.btn-caja-saldo', function(){
        var id = $(this).data('id');
        var caja = cuentasData.find(function(c){ return c.id == id; });
        if (!caja) {
            alert('No se encontraron datos de la caja.');
            return;
        }
        var saldo = (typeof caja.saldo !== 'undefined' && !isNaN(parseFloat(caja.saldo))) ? parseFloat(caja.saldo) : 0;
        var currency = caja.currency || 'NIO';
        var saldoFormato = saldo.toLocaleString(currency === 'USD' ? 'en-US' : 'es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        alert('Saldo actual de ' + (caja.name || caja.nombre || 'caja desconocida') + ':\n' + currency + ' ' + saldoFormato);
    });

    $('#btnNewCuenta').on('click', function(){
        $('#cuenta_id').val('');
        $('#cuenta_code').val('').prop('readonly', true).prop('disabled', true); // Clave automática
        $('#cuenta_name').val('');
        $('#cuenta_type').val('caja');
        $('#cuenta_sig_cheque').val('').prop('required', false).prop('readonly', true);
        $('#cuenta_bank_name').val('');
        $('#cuenta_account_number_1, #cuenta_account_number_2').val('');
        $('#cuenta_currency').val('');
        $('#cuenta_currency_symbol').val('');
        $('#cuenta_estado').val('1');
        $('#cuenta_formato').val('');
        // Permitir capturar saldo inicial solo al crear
        $('#cuenta_saldo_inicial').val('').prop('readonly', false).prop('disabled', false);
        $('#cuenta_total_cargos, #cuenta_total_abonos, #cuenta_saldo_actual, #cuenta_saldo_conciliado, #cuenta_cargos_transito, #cuenta_abonos_transito, #cuenta_montos_transito, #cuenta_saldos_sin_transito').val('').prop('readonly', true).prop('disabled', true);
        // Quitar aria-hidden si está presente
        $('#cuentaModal').removeAttr('aria-hidden');
        $('#cuentaModal').modal('show');
    });

    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        // Obtener datos de la cuenta desde la BD
        $.getJSON('<?php echo base_url('tesoreria/get_cuenta_by_id_ajax'); ?>', {cuenta_id: id}, function(resp){
            if(!resp || !resp.status) {
                alert('Error cargando datos de la cuenta');
                return;
            }
            var c = resp.cuenta;
            $('#cuenta_id').val(c.id);
            $('#cuenta_code').val(c.code || '').prop('readonly', true).prop('disabled', true);
            $('#cuenta_name').val(c.name || '');
            $('#cuenta_type').val(c.type || 'caja');
            if((c.type || '').toLowerCase() === 'banco') {
                $('#cuenta_sig_cheque').prop('required', true).prop('readonly', true);
                $.getJSON('<?php echo base_url('tesoreria/get_sig_cheque_ajax'); ?>', {cuenta_id: id}, function(resp) {
                    if(resp && resp.status) {
                        $('#cuenta_sig_cheque').val(resp.sig_cheque);
                    }
                });
            } else {
                $('#cuenta_sig_cheque').val('').prop('required', false).prop('readonly', true);
            }
            $('#cuenta_bank_name').val(c.bank_name || '');
            $('#cuenta_account_number_1, #cuenta_account_number_2').val(c.account_number || '');
            $('#cuenta_currency').val(c.currency || '');
            $('#cuenta_currency_symbol').val(c.currency_symbol || '');
            $('#cuenta_estado').val(c.estado == 1 ? '1' : '0');
            $('#cuenta_formato').val(c.formato || '');
            // Nuevos campos
            $('#cuenta_fecha_apertura_tab1').val(c.fecha_apertura || '');
            $('#cuenta_fecha_apertura_tab2').val(c.fecha_apertura || '');
            $('#cuenta_clabe').val(c.clabe || '');
            $('#cuenta_dia_corte').val(c.dia_corte || '');
            $('#cuenta_ultimo_dia_mes').prop('checked', c.ultimo_dia_mes == 1);

            $('#cuenta_clave_banco').val(c.clave_banco || '');
            $('#cuenta_sucursal').val(c.sucursal || '');
            $('#cuenta_funcionario').val(c.funcionario || '');
            $('#cuenta_telefono').val(c.telefono || '');
            $('#cuenta_contable').val(c.cuenta_contable || '');
            $('#cuenta_banco_extranjero').prop('checked', c.banco_extranjero == 1);
            // Bloquear saldo inicial en edición
            $('#cuenta_saldo_inicial').prop('readonly', true).prop('disabled', true);
            $('#cuentaModal').modal('show');
        });
    });

    // Al cambiar tipo de cuenta, mostrar/ocultar y autollenar sig_cheque
    $('#cuenta_type').on('change', function(){
        var tipo = $(this).val();
        if(tipo === 'banco') {
            $('#cuenta_sig_cheque').prop('required', true).prop('readonly', true);
            var cuentaId = $('#cuenta_id').val();
            if(cuentaId) {
                $.getJSON('<?php echo base_url('tesoreria/get_sig_cheque_ajax'); ?>', {cuenta_id: cuentaId}, function(resp) {
                    if(resp && resp.status) {
                        $('#cuenta_sig_cheque').val(resp.sig_cheque);
                    }
                });
            } else {
                $('#cuenta_sig_cheque').val('1');
            }
        } else {
            $('#cuenta_sig_cheque').val('').prop('required', false).prop('readonly', true);
        }
    });

    // Sincronizar Fecha de apertura entre ambas pestañas
    $(document).on('change', '#cuenta_fecha_apertura_tab1', function(){
        $('#cuenta_fecha_apertura_tab2').val($(this).val());
    });
    
    $(document).on('change', '#cuenta_fecha_apertura_tab2', function(){
        $('#cuenta_fecha_apertura_tab1').val($(this).val());
    });

    $('#cuenta_save').on('click', function(){
        var id = $('#cuenta_id').val();
        // Helper para obtener valor seguro
        function safeVal(sel) {
            var el = $(sel);
            if (el.length === 0) return '';
            var v = el.val();
            return (typeof v === 'string') ? v.trim() : '';
        }
        // Helper para valores numéricos
        function safeNum(sel) {
            var v = safeVal(sel);
            return v === '' ? null : parseInt(v, 10);
        }
        // Helper para valores flotantes
        function safeFloat(sel) {
            var v = safeVal(sel);
            return v === '' ? null : parseFloat(v);
        }
        // Helper para checkbox
        function safeCheckbox(sel) {
            return $(sel).is(':checked') ? 1 : 0;
        }
        
        var payload = {
            id: id,
            // code no se envía, es automático
            name: safeVal('#cuenta_name'),
            type: safeVal('#cuenta_type'),
            bank_name: safeVal('#cuenta_bank_name'),
            account_number: safeVal('#cuenta_account_number_1') || safeVal('#cuenta_account_number_2'),
            currency: safeVal('#cuenta_currency'),
            currency_symbol: safeVal('#cuenta_currency_symbol'),
            estado: safeVal('#cuenta_estado'),
            formato: safeVal('#cuenta_formato') || null,
            // Nuevos campos - usar tab1, pero ambas están sincronizadas
            fecha_apertura: safeVal('#cuenta_fecha_apertura_tab1') || null,
            clabe: safeVal('#cuenta_clabe') || null,
            dia_corte: safeNum('#cuenta_dia_corte'),
            ultimo_dia_mes: safeCheckbox('#cuenta_ultimo_dia_mes'),
            clave_banco: safeVal('#cuenta_clave_banco') || null,
            sucursal: safeVal('#cuenta_sucursal') || null,
            funcionario: safeVal('#cuenta_funcionario') || null,
            telefono: safeVal('#cuenta_telefono') || null,
            cuenta_contable: safeVal('#cuenta_contable') || null,
            banco_extranjero: safeCheckbox('#cuenta_banco_extranjero'),
            sig_cheque: safeNum('#cuenta_sig_cheque')
        };
        // Solo enviar saldo_inicial si es alta (no edición)
        if(!id){
            payload.saldo_inicial = safeFloat('#cuenta_saldo_inicial');
        }
        // No hay campos obligatorios
        $.post('<?php echo base_url('tesoreria/save_cuenta_ajax'); ?>', payload).done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if(j && j.status){ $('#cuentaModal').modal('hide'); loadCuentas(); } else { alert((j && j.message)? j.message : 'Error al guardar'); }
        }).fail(function(){ alert('Error en la petición'); });
    });

    // initial load
    loadCuentas();
});
</script>
