<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-exchange-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Movimientos</h5>
                                <span>Registro y consulta de movimientos bancarios</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('tesoreria/partial_back'); ?>

            <!-- Filtros y resumen superior -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Fecha de movimientos</label>
                            <input type="date" id="filtro_fecha" class="form-control" />
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Forma de pago</label>
                            <select id="filtro_forma_pago" class="form-control">
                                <option value="">Todas</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Cheque">Cheque</option>
                                <!-- Agrega más opciones según catálogo -->
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Conciliado</label>
                            <select id="filtro_conciliado" class="form-control">
                                <option value="">Todos</option>
                                <option value="1">Conciliado</option>
                                <option value="0">Sin conciliar</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnFiltrarMovs">Filtrar</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-2"><strong>Saldo inicial:</strong> <span id="saldo_inicial">0.00</span></div>
                        <div class="col-md-2"><strong>Saldo con tránsito:</strong> <span id="saldo_transito">0.00</span></div>
                        <div class="col-md-2"><strong>Saldo sin tránsito:</strong> <span id="saldo_sin_transito">0.00</span></div>
                        <div class="col-md-2"><strong>Total abono:</strong> <span id="total_abono">0.00</span></div>
                        <div class="col-md-2"><strong>Total cargo:</strong> <span id="total_cargo">0.00</span></div>
                    </div>
                </div>
            </div>

            <!-- Tabla de movimientos -->
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary mb-3" id="btnNuevoMovimiento">Nuevo Movimiento</button>
                    <div class="table-responsive">
                        <table id="tabla-movimientos" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Fecha de aplica</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Forma de pago</th>
                                    <th>No. cheque</th>
                                    <th>Referencia 1</th>
                                    <th>A nombre de</th>
                                    <th>Abono</th>
                                    <th>Cargo</th>
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

            <div id="modalContainer"></div>

<!-- Modal Agregar Movimiento -->
<div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Movimiento Bancario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="formMovimiento">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Cuenta bancaria</label>
                                                <select name="cuenta_id" id="mov_cuenta_id" class="form-control" required>
                                                    <option value="">Selecciona una cuenta</option>
                                                    <?php if (isset($cuentas) && is_array($cuentas)) foreach($cuentas as $c): ?>
                                                        <option value="<?= htmlspecialchars($c->id) ?>"><?= htmlspecialchars($c->name) ?> (<?= htmlspecialchars($c->account_number) ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Concepto</label>
                            <div class="input-group">
                                <select name="concepto" id="mov_concepto" class="form-control"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnAddConcepto" title="Agregar concepto"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                        <!-- Modal Agregar Concepto -->
                        <div class="modal fade" id="modalConcepto" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Agregar Concepto Bancario</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formConcepto">
                                            <div class="form-group">
                                                <label>Código de Concepto</label>
                                                <input name="clave" id="concepto_clave" class="form-control" required placeholder="Ej: CLI, TRANS-001" />
                                                <small class="form-text text-muted">Si es transferencia, el código será autoincremental (Ej: TRANS-001, TRANS-002...)</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Descripción</label>
                                                <input name="descripcion" id="concepto_descripcion" class="form-control" required placeholder="Descripción del concepto" />
                                            </div>
                                            <div class="form-group">
                                                <label>Tipo</label>
                                                <select name="tipo" id="concepto_tipo" class="form-control">
                                                    <option value="ABONO">ABONO</option>
                                                    <option value="CARGO">CARGO</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Concepto de la transacción (SAE)</label>
                                                <input name="concepto_sae" id="concepto_sae" class="form-control" placeholder="Opcional" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" id="btnGuardarConcepto">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Forma de pago</label>
                            <select name="forma_pago" id="mov_forma_pago" class="form-control">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Fecha de registro</label>
                            <input type="date" name="fecha" id="mov_fecha" class="form-control" />
                        </div>
                        <div class="form-group col-md-2">
                            <label>Fecha de aplicación</label>
                            <input type="date" name="fecha_aplicacion" id="mov_fecha_aplicacion" class="form-control" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Beneficiario</label>
                            <div class="input-group">
                                <select name="beneficiario" id="mov_beneficiario" class="form-control"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnAddBeneficiario" title="Agregar beneficiario"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                        <!-- Modal Agregar Beneficiario -->
                        <div class="modal fade" id="modalBeneficiario" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Agregar Beneficiario</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formBeneficiario">
                                            <div class="form-group">
                                                <label>Código de Beneficiario</label>
                                                <input name="clave" id="beneficiario_clave" class="form-control" required placeholder="Ej: B001" />
                                                <small class="form-text text-muted">Se sugiere consecutivo (Ej: B001, B002...)</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Descripción</label>
                                                <input name="descripcion" id="beneficiario_descripcion" class="form-control" required placeholder="Nombre o razón social" />
                                            </div>
                                            <div class="form-group">
                                                <label>RFC (Ruc o Cédula)</label>
                                                <input name="rfc" id="beneficiario_rfc" class="form-control" placeholder="RFC, RUC o Cédula" />
                                            </div>
                                            <div class="form-group">
                                                <label>Cuenta Contable</label>
                                                <select name="cuenta" id="beneficiario_cuenta" class="form-control"></select>
                                            </div>
                                            <div class="form-group">
                                                <label>Comentario</label>
                                                <input name="comentario" id="beneficiario_comentario" class="form-control" placeholder="Referencia o comentario" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" id="btnGuardarBeneficiario">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Referencia 1</label>
                            <input name="referencia1" id="mov_referencia1" class="form-control" />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Referencia 2</label>
                            <input name="referencia2" id="mov_referencia2" class="form-control" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Monto total</label>
                            <input name="monto_total" id="mov_monto_total" class="form-control" type="number" step="0.01" min="0" />
                        </div>
                        <div class="form-group col-md-3">
                            <label>IVA Total</label>
                            <input name="iva_total" id="mov_iva_total" class="form-control" type="number" step="0.01" min="0" />
                        </div>
                        <!-- Campo Centro de Costos oculto por solicitud -->
                    </div>
                    <div class="form-row">
                        <!-- Campos adicionales para transferencia -->
                        <div class="form-group col-md-3 transferencia-only" style="display:none;">
                            <label>Cuenta destino</label>
                            <input name="cuenta_destino" id="mov_cuenta_destino" class="form-control" />
                        </div>
                        <div class="form-group col-md-3 transferencia-only" style="display:none;">
                            <label>Banco destino</label>
                            <input name="banco_destino" id="mov_banco_destino" class="form-control" />
                        </div>
                        <div class="form-group col-md-3 cheque-only" style="display:none;">
                            <label>No. cheque</label>
                            <input name="numero_cheque" id="mov_numero_cheque" class="form-control" readonly />
                        </div>
                        <div class="form-group col-md-6 cheque-only" style="display:none;">
                            <label>Páguese este cheque a</label>
                            <input name="cheque_a" id="mov_cheque_a" class="form-control" />
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
jQuery(function($){
    // Cargar beneficiarios en el select
    function cargarBeneficiarios() {
        $.getJSON('<?php echo base_url('tesoreria/get_beneficiarios_ajax'); ?>', function(resp){
            var $sel = $('#mov_beneficiario');
            $sel.empty();
            if(resp && resp.status && resp.beneficiarios) {
                resp.beneficiarios.forEach(function(b){
                    $sel.append('<option value="'+b.id+'">'+b.clave+' - '+b.descripcion+' ('+(b.rfc||'')+')</option>');
                });
            }
        });
    }
    cargarBeneficiarios();

    // Mostrar modal para agregar beneficiario
    $('#btnAddBeneficiario').on('click', function(){
        var $form = $('#formBeneficiario');
        if ($form.length) $form[0].reset();
        // Sugerir código autoincremental B001, B002...
        $.getJSON('<?php echo base_url('tesoreria/get_beneficiarios_ajax'); ?>', function(resp){
            var max = 0;
            if(resp && resp.status && resp.beneficiarios) {
                resp.beneficiarios.forEach(function(b){
                    var m = /^B(\d+)$/i.exec(b.clave);
                    if(m && parseInt(m[1]) > max) max = parseInt(m[1]);
                });
            }
            var next = 'B' + String(max+1).padStart(3,'0');
            $('#beneficiario_clave').val(next);
        });
        // Cargar cuentas contables reales desde el catálogo contable
        var $sel = $('#beneficiario_cuenta');
        $sel.empty();
        $.getJSON(base_url+'contabilidad/accounts', function(resp) {
            if (resp && resp.status === 'success' && Array.isArray(resp.data)) {
                resp.data.forEach(function(c) {
                    var code = c.code || c.codigo || '';
                    var name = c.name || c.nombre || '';
                    $sel.append('<option value="'+code+'">'+code+' - '+name+'</option>');
                });
            } else {
                $sel.append('<option value="">No hay cuentas disponibles</option>');
            }
            // Inicializar Select2 después de poblar
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.select2('destroy');
            }
            $sel.select2({
                dropdownParent: $('#modalBeneficiario'),
                width: '100%',
                placeholder: 'Buscar cuenta contable...',
                allowClear: true
            });
            $('#modalBeneficiario').modal('show');
        }).fail(function(){
            $sel.append('<option value="">Error cargando catálogo</option>');
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.select2('destroy');
            }
            $sel.select2({
                dropdownParent: $('#modalBeneficiario'),
                width: '100%',
                placeholder: 'Buscar cuenta contable...',
                allowClear: true
            });
            $('#modalBeneficiario').modal('show');
        });
    });
    // Guardar nuevo beneficiario
    $('#btnGuardarBeneficiario').on('click', function(){
        var payload = {
            clave: $('#beneficiario_clave').val(),
            descripcion: $('#beneficiario_descripcion').val(),
            rfc: $('#beneficiario_rfc').val(),
            cuenta: $('#beneficiario_cuenta').val(),
            clabe: $('#beneficiario_clabe_campo').val()
        };
        $.post('<?php echo base_url('tesoreria/save_beneficiario_ajax'); ?>', payload).done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if(j && j.status){
                $('#modalBeneficiario').modal('hide');
                cargarBeneficiarios();
            } else {
                alert((j && j.message)? j.message : 'Error al guardar');
            }
        }).fail(function(){ alert('Error en la petición'); });
    });
    // Cargar conceptos en el select
    function cargarConceptos() {
        $.getJSON('<?php echo base_url('tesoreria/get_conceptos_ajax'); ?>', function(resp){
            var $sel = $('#mov_concepto');
            $sel.empty();
            if(resp && resp.status && resp.conceptos) {
                resp.conceptos.forEach(function(c){
                    $sel.append('<option value="'+c.clave+'">'+c.clave+' - '+c.descripcion+' ('+c.tipo+')</option>');
                });
            }
        });
    }
    cargarConceptos();

    // Mostrar modal para agregar concepto
    $('#btnAddConcepto').on('click', function(){
        var $form = $('#formConcepto');
        if ($form.length) $form[0].reset();
        // Si el tipo de movimiento seleccionado es Transferencia, sugerir código autoincremental
        if($('#mov_forma_pago').val() === 'Transferencia') {
            // Buscar el último código TRANS-XXX
            $.getJSON('<?php echo base_url('tesoreria/get_conceptos_ajax'); ?>', function(resp){
                var max = 0;
                if(resp && resp.status && resp.conceptos) {
                    resp.conceptos.forEach(function(c){
                        var m = /^TRANS-(\d+)$/.exec(c.clave);
                        if(m && parseInt(m[1]) > max) max = parseInt(m[1]);
                    });
                }
                var next = 'TRANS-' + String(max+1).padStart(3,'0');
                $('#concepto_clave').val(next);
            });
        }
        $('#modalConcepto').modal('show');
    });
    // Guardar nuevo concepto
    $('#btnGuardarConcepto').on('click', function(){
        var payload = {
            clave: $('#concepto_clave').val(),
            descripcion: $('#concepto_descripcion').val(),
            tipo: $('#concepto_tipo').val(),
            concepto_sae: $('#concepto_sae').val()
        };
        $.post('<?php echo base_url('tesoreria/save_concepto_ajax'); ?>', payload).done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if(j && j.status){
                $('#modalConcepto').modal('hide');
                cargarConceptos();
            } else {
                alert((j && j.message)? j.message : 'Error al guardar');
            }
        }).fail(function(){ alert('Error en la petición'); });
    });
        $('#btnNuevoMovimiento').on('click', function(){
            $('#formMovimiento')[0].reset();
            $('.transferencia-only').hide();
            $('#modalMovimiento').modal('show');
            // Disparar el evento para que se llene el número de cheque si corresponde
            setTimeout(function(){
                $('#mov_forma_pago').val('Cheque').trigger('change');
                $('#mov_cuenta_id').trigger('change');
            }, 200);
        });
        $('#mov_forma_pago, #mov_cuenta_id').on('change', function(){
            var forma = $('#mov_forma_pago').val();
            var cuenta_id = $('#mov_cuenta_id').val();
            if(forma === 'Transferencia') {
                $('.transferencia-only').show();
            } else {
                $('.transferencia-only').hide();
            }
            if(forma === 'Cheque') {
                $('.cheque-only').show();
                // Obtener el siguiente número de cheque vía AJAX
                if(cuenta_id) {
                    $.getJSON(base_url+'tesoreria/get_sig_cheque_ajax', {cuenta_id: cuenta_id}, function(resp){
                        if(resp && resp.status && resp.sig_cheque !== undefined) {
                            $('#mov_numero_cheque').val(resp.sig_cheque);
                        } else {
                            $('#mov_numero_cheque').val('');
                        }
                    });
                } else {
                    $('#mov_numero_cheque').val('');
                }
            } else {
                $('.cheque-only').hide();
                $('#mov_numero_cheque').val('');
            }
        });

    // Guardar movimiento
    $('#btnGuardarMovimiento').on('click', function(){
        var concepto_txt = $('#mov_concepto option:selected').text() || '';
        var concepto_val = $('#mov_concepto').val() || '';
        var beneficiario_txt = $('#mov_beneficiario option:selected').text() || '';
        var beneficiario_val = $('#mov_beneficiario').val() || '';
        // Validación de campos obligatorios
        var errores = [];
        if (!concepto_val) errores.push('Selecciona un concepto.');
        if (!$('#mov_forma_pago').val()) errores.push('Selecciona la forma de pago.');
        if (!$('#mov_fecha').val()) errores.push('Ingresa la fecha de registro.');
        if (!$('#mov_fecha_aplicacion').val()) errores.push('Ingresa la fecha de aplicación.');
        if (!beneficiario_val) errores.push('Selecciona un beneficiario.');
        if (!$('#mov_monto_total').val()) errores.push('Ingresa el monto total.');
        if (errores.length) { alert(errores.join('\n')); return; }
            var tipo_concepto = $('#mov_concepto option:selected').text().toUpperCase().includes('CARGO') ? 'CARGO' : 'ABONO';
            var monto = $('#mov_monto_total').val() || '0.00';
            var cuentaId = $('#mov_cuenta_id').val();
            if (!cuentaId || isNaN(parseInt(cuentaId))) {
                alert('Selecciona una cuenta bancaria válida.');
                return;
            }
            var payload = {
                cuenta_id: parseInt(cuentaId),
                clave_concepto: concepto_val || concepto_txt.split(' - ')[0] || '',
                forma_pago: $('#mov_forma_pago').val() || '',
                fecha: $('#mov_fecha').val() || '',
                fecha_aplicacion: $('#mov_fecha_aplicacion').val() || '',
                a_nombre_de: $('#mov_beneficiario option:selected').text() || '',
                referencia1: $('#mov_referencia1').val() || '',
                referencia2: $('#mov_referencia2').val() || '',
                monto: monto,
                iva_total: $('#mov_iva_total').val() || '0.00',
                cuenta_destino: $('#mov_cuenta_destino').val() || '',
                banco_destino: $('#mov_banco_destino').val() || '',
                saldo_resultante: '',
                descripcion: $('#mov_descripcion').val() || '',
                estado: $('#mov_estado').val() || 'pendiente',
                movimiento: '',
                numero_cheque: $('#mov_numero_cheque').val() || '',
                rfc: '',
                abono: tipo_concepto === 'ABONO' ? monto : '0.00',
                cargo: tipo_concepto === 'CARGO' ? monto : '0.00',
                saldo: $('#mov_saldo').val() || '0.00',
                conciliado: $('#mov_conciliado').val() || '0'
            };
        $.post('<?php echo base_url('tesoreria/save_movimiento_ajax'); ?>', payload).done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if(j && j.status){
                $('#modalMovimiento').modal('hide');
                cargarMovimientos();

            // Código para agregar Departamento y Proyecto eliminado
                // --- Select2 para Departamento ---
                $('#mov_departamento').select2({
                    ajax: {
                        url: base_url+'api_departamentos.php',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: (data.departamentos||[]).map(function(d){ return {id:d.id,text:d.descripcion}; })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Buscar departamento...',
                    allowClear: true,
                    width: '100%'
                });
                // --- Select2 para Centro de Costos ---
                $('#mov_centro_costos').select2({
                    ajax: {
                        url: base_url+'api_centros_costo.php',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: (data.centros||[]).map(function(c){ return {id:c.id,text:(c.codigo ? c.codigo+' - ' : '')+c.nombre}; })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Buscar centro de costo...',
                    allowClear: true,
                    width: '100%'
                });
                // --- Select2 para Proyecto ---
                $('#mov_proyecto').select2({
                    ajax: {
                        url: base_url+'api_proyectos.php',
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: (data.proyectos||[]).map(function(p){ return {id:p.id,text:p.descripcion}; })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Buscar proyecto...',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                alert((j && j.message)? j.message : 'Error al guardar');
            }
        }).fail(function(){ alert('Error en la petición'); });
    });
    // Acción: Editar movimiento
    $(document).on('click', '.btn-editar', function(){
        var id = $(this).data('id');
        $.getJSON(base_url+'tesoreria/get_movimiento_ajax', {id: id}, function(resp){
            if(resp && resp.status && resp.movimiento){
                var mov = resp.movimiento;
                $('#mov_cuenta_id').val(mov.cuenta_id).trigger('change');
                // Concepto (Select2): si no existe la opción, agregarla
                if($('#mov_concepto option[value="'+mov.clave_concepto+'"]').length === 0 && mov.clave_concepto) {
                    $('#mov_concepto').append('<option value="'+mov.clave_concepto+'">'+(mov.clave_concepto)+'</option>');
                }
                $('#mov_concepto').val(mov.clave_concepto).trigger('change');
                $('#mov_forma_pago').val(mov.forma_pago).trigger('change');
                $('#mov_fecha').val(mov.fecha);
                $('#mov_fecha_aplicacion').val(mov.fecha_aplicacion);
                // Beneficiario (Select2): si no existe la opción, agregarla
                if($('#mov_beneficiario option[value="'+mov.a_nombre_de+'"]').length === 0 && mov.a_nombre_de) {
                    $('#mov_beneficiario').append('<option value="'+mov.a_nombre_de+'">'+(mov.a_nombre_de)+'</option>');
                }
                $('#mov_beneficiario').val(mov.a_nombre_de).trigger('change');
                $('#mov_referencia1').val(mov.referencia1);
                $('#mov_referencia2').val(mov.referencia2);
                $('#mov_monto_total').val(mov.monto);
                $('#mov_iva_total').val(mov.iva_total);
                $('#mov_numero_cheque').val(mov.numero_cheque);
                $('#mov_cheque_a').val(mov.cheque_a);
                $('#mov_descripcion').val(mov.descripcion);
                $('#mov_estado').val(mov.estado);
                // Mostrar campos cheque/transferencia según forma de pago
                if(mov.forma_pago === 'Cheque') {
                    $('.cheque-only').show();
                } else {
                    $('.cheque-only').hide();
                }
                if(mov.forma_pago === 'Transferencia') {
                    $('.transferencia-only').show();
                } else {
                    $('.transferencia-only').hide();
                }
                $('#modalMovimiento').modal('show');
                // Guardar edición
                $('#btnGuardarMovimiento').off('click').on('click', function(){
                    var payload = {
                        id: id,
                        cuenta_id: $('#mov_cuenta_id').val(),
                        clave_concepto: $('#mov_concepto').val(),
                        forma_pago: $('#mov_forma_pago').val(),
                        fecha: $('#mov_fecha').val(),
                        fecha_aplicacion: $('#mov_fecha_aplicacion').val(),
                        a_nombre_de: $('#mov_beneficiario').val(),
                        referencia1: $('#mov_referencia1').val(),
                        referencia2: $('#mov_referencia2').val(),
                        monto: $('#mov_monto_total').val(),
                        iva_total: $('#mov_iva_total').val(),
                        numero_cheque: $('#mov_numero_cheque').val(),
                        cheque_a: $('#mov_cheque_a').val(),
                        descripcion: $('#mov_descripcion').val(),
                        estado: $('#mov_estado').val()
                    };
                    $.post(base_url+'tesoreria/update_movimiento_ajax', payload).done(function(resp){
                        try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
                        if(j && j.status){ $('#modalMovimiento').modal('hide'); cargarMovimientos(); } else { alert((j && j.message)? j.message : 'Error al guardar'); }
                    }).fail(function(){ alert('Error en la petición'); });
                });
            } else {
                alert('No se pudo cargar el movimiento');
            }
        });
    });
    // Acción: Anular movimiento
    $(document).on('click', '.btn-anular', function(){
        var id = $(this).data('id');
        if(confirm('¿Seguro que deseas anular este movimiento?')){
            $.post(base_url+'tesoreria/anular_movimiento_ajax', {id: id}, function(resp){
                try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
                if(j && j.status){ cargarMovimientos(); } else { alert((j && j.message)? j.message : 'Error al anular'); }
            }).fail(function(){ alert('Error en la petición'); });
        }
    });
});
</script>

        <script>
        function cargarMovimientos() {
            var cuentaId = $('#mov_cuenta_id').val();
            var params = {
                fecha: $('#filtro_fecha').val(),
                forma_pago: $('#filtro_forma_pago').val(),
                conciliado: $('#filtro_conciliado').val()
            };
            if (cuentaId) {
                params.cuenta_id = cuentaId;
            }
            $.getJSON(base_url+'tesoreria/get_movimientos_ajax', params).done(function(resp){
                var $tb = $('#tabla-movimientos tbody');
                $tb.empty();
                if(!resp || !resp.status || !resp.movimientos || resp.movimientos.length === 0) {
                    $tb.append('<tr><td colspan="15" class="text-center text-muted">No hay datos para desplegar</td></tr>');
                    return;
                }
                var total_abono = 0, total_cargo = 0, saldo = 0;
                resp.movimientos.forEach(function(mov){
                    total_abono += parseFloat(mov.abono||0);
                    total_cargo += parseFloat(mov.cargo||0);
                    var tr = '<tr>'
                        +'<td>'+(mov.fecha||'')+'</td>'
                        +'<td>'+(mov.fecha_aplicacion||'')+'</td>'
                        +'<td>'+(mov.descripcion||'')+'</td>'
                        +'<td>'+(mov.estado||'')+'</td>'
                        +'<td>'+(mov.forma_pago||'')+'</td>'
                        +'<td>'+(mov.numero_cheque||'')+'</td>'
                        +'<td>'+(mov.referencia1||'')+'</td>'
                        +'<td>'+(mov.a_nombre_de||'')+'</td>'
                        +'<td class="text-success">'+parseFloat(mov.abono||0).toFixed(2)+'</td>'
                        +'<td class="text-danger">'+parseFloat(mov.cargo||0).toFixed(2)+'</td>'
                        +'<td>'
                        +'<button class="btn btn-sm btn-warning btn-editar" data-id="'+(mov.id||'')+'">Editar</button> '
                        +'<button class="btn btn-sm btn-danger btn-anular" data-id="'+(mov.id||'')+'">Anular</button> '
                        +'<button class="btn btn-sm btn-info btn-conciliar" data-id="'+(mov.id||'')+'">Conciliar</button>'
                        +'</td>'
                        +'</tr>';
                    $tb.append(tr);
                });
                $('#total_abono').text(total_abono.toFixed(2));
                $('#total_cargo').text(total_cargo.toFixed(2));
                $('#saldo_inicial').text('0.00'); // Puedes calcularlo si tienes el dato
                $('#saldo_transito').text('0.00'); // Puedes calcularlo si tienes el dato
                $('#saldo_sin_transito').text('0.00'); // Puedes calcularlo si tienes el dato
            }).fail(function(){
                var $tb = $('#tabla-movimientos tbody');
                $tb.empty();
                $tb.append('<tr><td colspan="15" class="text-center text-danger">Error cargando movimientos</td></tr>');
            });
        }
        jQuery(function($){
            $('#btnFiltrarMovs, #filtro_fecha, #filtro_forma_pago, #filtro_conciliado').on('change click', cargarMovimientos);
            cargarMovimientos();
            // --- Select2 CSS/JS global includes ---
            if (!$('link[href*="select2.min.css"]').length) {
                $('head').append('<link href="'+base_url+'public/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />');
            }
            if (!$('script[src*="select2.min.js"]').length) {
                var s = document.createElement('script');
                s.src = base_url+'public/plugins/select2/dist/js/select2.min.js';
                document.body.appendChild(s);
            }
        });
        </script>
        </div>
    </div>
</div>
