<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-university bg-blue"></i>
                            <div class="d-inline">
                                <h5>Cajas y Bancos</h5>
                                <span>Gestione cuentas bancarias y cajas internas (crear, editar, eliminar, ver movimientos).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('tesoreria/partial_back'); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div></div>
                                <button id="btnNewCuenta" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Nueva Cuenta</button>
                            </div>
                            <div class="table-responsive">
                                <table id="cuentas-table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Banco</th>
                                            <th>Cuenta</th>
                                            <th>Saldo</th>
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
                </div>
            </div>


            <!-- Modal con Tabs -->
            <div class="modal fade" id="cuentaModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cuenta (Caja / Banco)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="cuenta_id" />
                            <ul class="nav nav-tabs" id="cuentaTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="datos-cuenta-tab" data-toggle="tab" href="#datos-cuenta" role="tab">Datos de la cuenta</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="datos-banco-tab" data-toggle="tab" href="#datos-banco" role="tab">Datos del Banco</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="montos-tab" data-toggle="tab" href="#montos" role="tab">Montos</a>
                                </li>
                            </ul>
                            <div class="tab-content pt-3" id="cuentaTabContent">
                                <!-- Datos de la cuenta -->
                                <div class="tab-pane fade show active" id="datos-cuenta" role="tabpanel">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Clave</label>
                                            <input id="cuenta_code" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Núm. Cuenta</label>
                                            <input id="cuenta_account_number" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>CLABE Interbancaria</label>
                                            <input id="cuenta_clabe" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Formato</label>
                                            <input id="cuenta_formato" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Cuenta contable</label>
                                            <input id="cuenta_contable" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Fecha de apertura</label>
                                            <input id="cuenta_fecha_apertura" type="date" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Siguiente cheque a emitir</label>
                                            <input id="cuenta_sig_cheque" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Día de corte</label>
                                            <input id="cuenta_dia_corte" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Moneda</label>
                                            <input id="cuenta_currency" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Estado</label>
                                            <select id="cuenta_estado" class="form-control"><option value="1">Activo</option><option value="0">Inactivo</option></select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Datos del banco -->
                                <div class="tab-pane fade" id="datos-banco" role="tabpanel">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Banco</label>
                                            <input id="cuenta_bank_name" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Nombre de la cuenta</label>
                                            <input id="cuenta_name" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clave del banco</label>
                                            <input id="cuenta_clave_banco" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Sucursal</label>
                                            <input id="cuenta_sucursal" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Funcionario</label>
                                            <input id="cuenta_funcionario" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Teléfono</label>
                                            <input id="cuenta_telefono" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Plaza</label>
                                            <input id="cuenta_plaza" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Logo del banco</label>
                                            <input id="cuenta_logo_banco" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>RFC</label>
                                            <input id="cuenta_rfc" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3 d-flex align-items-center">
                                            <input type="checkbox" id="cuenta_banco_extranjero" class="mr-2" />
                                            <label for="cuenta_banco_extranjero" class="mb-0">Es banco extranjero</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Montos -->
                                <div class="tab-pane fade" id="montos" role="tabpanel">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Saldo inicial</label>
                                            <input id="cuenta_saldo_inicial" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Total de cargos</label>
                                            <input id="cuenta_total_cargos" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Total de abonos</label>
                                            <input id="cuenta_total_abonos" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Saldo actual</label>
                                            <input id="cuenta_saldo_actual" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Saldo conciliado</label>
                                            <input id="cuenta_saldo_conciliado" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Cargos en tránsito</label>
                                            <input id="cuenta_cargos_transito" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Abonos en tránsito</label>
                                            <input id="cuenta_abonos_transito" class="form-control" />
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Montos en tránsito</label>
                                            <input id="cuenta_montos_transito" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Saldos sin tránsito</label>
                                            <input id="cuenta_saldos_sin_transito" class="form-control" />
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
    function loadCuentas(){
        // request accounts with computed saldo (compatible with older/newer schemas)
        $.getJSON('<?php echo base_url('tesoreria/get_cuentas_with_saldo_ajax'); ?>').done(function(resp){
            if(!resp || !resp.status) return;
            var $tb = $('#cuentas-table tbody'); $tb.empty();
            resp.cuentas.forEach(function(c){
                var estado = (c.estado==1) ? '<span class="badge badge-success">ACTIVO</span>' : '<span class="badge badge-warning">INACTIVO</span>';
                var tr = '<tr data-id="'+c.id+'">'
                    +'<td>'+c.id+'</td>'
                    +'<td>'+ (c.code||'') +'</td>'
                    +'<td>'+ (c.name||'') +'</td>'
                    +'<td>'+ (c.type||'') +'</td>'
                    +'<td>'+ (c.bank_name||'') +'</td>'
                    +'<td>'+ (c.account_number||'') +'</td>'
                    +'<td class="text-right font-weight-bold">'+ (c.saldo !== undefined ? (typeof c.saldo === 'number' ? c.saldo.toFixed(2) : c.saldo) : '0.00') +'</td>'
                    +'<td>'+ (c.currency||'') + (c.currency_symbol? ' ('+c.currency_symbol+')':'') +'</td>'
                    +'<td>'+ estado +'</td>'
                    +'<td><button class="btn btn-sm btn-info btn-edit" data-id="'+c.id+'">Editar</button> '
                    +'<a class="btn btn-sm btn-danger" href="<?php echo base_url('tesoreria/del_cuenta/'); ?>'+c.id+'" onclick="return confirm(\'Confirmar eliminación\')">Eliminar</a> '
                    +'<a class="btn btn-sm btn-secondary" href="<?php echo base_url('tesoreria/movimientos'); ?>?cuenta_id='+c.id+'">Movimientos</a> '
                    +'<a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('tesoreria/conciliacion'); ?>?cuenta_id='+c.id+'">Conciliación</a></td>'
                    +'</tr>';
                $tb.append(tr);
            });
        }).fail(function(){ alert('Error cargando cuentas'); });
    }

    $('#btnNewCuenta').on('click', function(){
        $('#cuenta_id').val(''); $('#cuenta_code').val(''); $('#cuenta_name').val('');
        $('#cuenta_type').val('caja'); $('#cuenta_bank_name').val(''); $('#cuenta_account_number').val('');
        $('#cuenta_currency').val(''); $('#cuenta_currency_symbol').val(''); $('#cuenta_estado').val('1');
        $('#cuentaModal').modal('show');
    });

    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        var $tr = $('tr[data-id="'+id+'"]');
        $('#cuenta_id').val(id);
        $('#cuenta_code').val($tr.children().eq(1).text().trim());
        $('#cuenta_name').val($tr.children().eq(2).text().trim());
        $('#cuenta_type').val($tr.children().eq(3).text().trim());
        $('#cuenta_bank_name').val($tr.children().eq(4).text().trim());
        $('#cuenta_account_number').val($tr.children().eq(5).text().trim());
        var cur = $tr.children().eq(6).text().trim(); $('#cuenta_currency').val(cur.split(' ')[0] || '');
        $('#cuenta_currency_symbol').val((/\((.*)\)/.exec(cur)||[])[1]||'');
        $('#cuenta_estado').val($tr.children().eq(7).text().indexOf('ACTIVO')>-1?1:0);
        // Leer el valor de sig_cheque desde la tabla y ponerlo en el input
        $.getJSON(base_url+'tesoreria/get_sig_cheque_ajax', {cuenta_id: id}, function(resp){
            if(resp && resp.status && resp.sig_cheque !== undefined) {
                $('#cuenta_sig_cheque').val(resp.sig_cheque);
            } else {
                $('#cuenta_sig_cheque').val('');
            }
        });
        $('#cuentaModal').modal('show');
    });

    $('#cuenta_save').on('click', function(){
        var id = $('#cuenta_id').val();
        function safeVal(sel) {
            var v = $(sel).val();
            return (typeof v === 'string') ? v.trim() : '';
        }
        var payload = {
            id: id,
            code: safeVal('#cuenta_code'),
            name: safeVal('#cuenta_name'),
            type: $('#cuenta_type').val(),
            bank_name: safeVal('#cuenta_bank_name'),
            account_number: safeVal('#cuenta_account_number'),
            currency: safeVal('#cuenta_currency'),
            currency_symbol: safeVal('#cuenta_currency_symbol'),
            estado: $('#cuenta_estado').val(),
            sig_cheque: $('#cuenta_sig_cheque').val()
        };
        if(!payload.name){ alert('Nombre requerido'); return; }
        $.post('<?php echo base_url('tesoreria/save_cuenta_ajax'); ?>', payload).done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if(j && j.status){ $('#cuentaModal').modal('hide'); loadCuentas(); } else { alert((j && j.message)? j.message : 'Error al guardar'); }
        }).fail(function(){ alert('Error en la petición'); });
    });

    // initial load
    loadCuentas();
});
</script>
