// Cargar bancos y cuentas al abrir el modal
    function cargarBancosYCuentas() {
        // Reemplaza con tu endpoint real si es diferente
        $.get('/Conta/tesoreria/get_bancos_cuentas_ajax', function(resp) {
            var bancos = resp.bancos || [];
            var cuentas = resp.cuentas || [];
            var $banco = $('#cheque_banco');
            var $cuenta = $('#cheque_cuenta');
            $banco.empty().append('<option value="">Seleccione banco...</option>');
            bancos.forEach(function(b){ $banco.append('<option value="'+b.id+'">'+b.nombre+'</option>'); });
            $cuenta.empty().append('<option value="">Seleccione cuenta...</option>');
            cuentas.forEach(function(c){ $cuenta.append('<option value="'+c.id+'">'+c.nombre+' ('+c.numero+')</option>'); });
        },'json');
    }

    $('#modalChequeDesembolso').on('show.bs.modal', function(){
        cargarBancosYCuentas();
    });

    // Validar número de cheque único al cambiar cuenta o número
    $('#cheque_cuenta, #cheque_numero').on('change', function(){
        var cuenta = $('#cheque_cuenta').val();
        var num = $('#cheque_numero').val();
        if(cuenta && num){
            $.get('/Conta/tesoreria/validar_numero_cheque_ajax', {cuenta_id: cuenta, numero: num}, function(resp){
                if(resp && resp.exists){
                    alert('El número de cheque ya existe en esa cuenta.');
                    $('#cheque_numero').val('');
                }
            },'json');
        }
    });

    // Enviar formulario de cheque y desembolso
    $('#formChequeDesembolso').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $('#btnEmitirCheque').prop('disabled', true).text('Procesando...');
        $.post('/Conta/desembolsos/emitir_cheque_ajax', data, function(resp){
            if(resp && resp.status){
                $('#modalChequeDesembolso').modal('hide');
                alert('Cheque emitido y desembolso realizado.');
                cargarDesembolsos();
            }else{
                alert('Error: '+(resp && resp.message ? resp.message : 'No se pudo procesar.'));
            }
            $('#btnEmitirCheque').prop('disabled', false).text('Emitir cheque y desembolsar');
        },'json');
    });
$(function(){
    function cargarDesembolsos(){
        var params = {
            start_date: $('input[name="start_date"]').val(),
            end_date: $('input[name="end_date"]').val(),
            q: $('#desembolsos_search').val()
        };
        $.get(base_url+'desembolsos/list_ajax', params, function(resp){
            var $tbody = $('#tabla_desembolsos tbody');
            $tbody.empty();
            if(resp && resp.length){
                resp.forEach(function(d){
                    var estado = '';
                    if (d.estado === 'anulado') {
                        estado = '<span class="badge badge-anulado">Anulado</span>';
                    } else if (d.estado === 'pendiente') {
                        estado = '<span class="badge badge-pendiente">Pendiente</span>';
                    } else {
                        estado = '<span class="badge badge-desembolsado">Procesado</span>';
                    }
                    var btn = '';
                    var resumen = '';
                    if(d.estado === 'anulado') {
                        resumen = '<div class="text-muted small">Crédito anulado<br>'+(d.obs_desembolso ? d.obs_desembolso : '')+'</div>';
                    } else if(d.desembolsado == 0 || d.desembolsado === null) {
                        btn = '<button class="btn btn-sm btn-primary btn-desembolsar" data-id="'+d.idprestamo+'" data-monto="'+d.monto+'" data-fecha="'+d.fecha_desembolso+'" data-cliente="'+d.cliente+'">Desembolsar</button>';
                    } else {
                        resumen = '<div class="text-success small">Ya desembolsado<br>Fecha: '+(d.fecha_desembolso_real ? d.fecha_desembolso_real : d.fecha_desembolso)+'<br>Por: '+(d.usuario_desembolso ? d.usuario_desembolso : '-')+'</div>';
                    }
                    var monto = d.monto ? parseFloat(d.monto).toLocaleString('es-MX', {minimumFractionDigits:2}) : '';
                    var fechaDesembolso = d.fecha_desembolso && d.fecha_desembolso !== 'null' ? d.fecha_desembolso : '<span class="badge badge-pendiente">Pendiente</span>';
                    var fechaPrimerPago = d.primer_dia_pago && d.primer_dia_pago !== 'null' ? d.primer_dia_pago : '<span class="badge badge-pendiente">Pendiente</span>';
                    var rowClass = d.estado === 'anulado' ? ' class="row-anulado"' : '';
                    $tbody.append('<tr' + rowClass + '>' +
                        '<td>'+d.idprestamo+'</td>' +
                        '<td>'+d.cliente+'</td>' +
                        '<td class="monto">'+monto+'</td>' +
                        '<td>'+fechaDesembolso+'</td>' +
                        '<td>'+fechaPrimerPago+'</td>' +
                        '<td>'+estado+'</td>' +
                        '<td>'+(btn || resumen)+'</td>' +
                    '</tr>');
                });
            }else{
                $tbody.append('<tr><td colspan="7" class="text-center">No hay desembolsos pendientes.</td></tr>');
            }
        },'json');
    }
    cargarDesembolsos();
    $('form').on('submit', function(e){ e.preventDefault(); cargarDesembolsos(); });
    $('#desembolsos_search').on('keyup', function(e){ if(e.keyCode==13) cargarDesembolsos(); });
    $(document).on('click','.btn-desembolsar',function(){
        var idPrestamo = $(this).data('id');
        var cliente = $(this).data('cliente');
        var monto = $(this).data('monto');
        // Obtener datos adicionales por AJAX
        $.get(base_url+'desembolsos/detalle_ajax', {idprestamo: idPrestamo}, function(resp) {
            var plazo = resp.plazo || '';
            var tasa = resp.tasa ? (parseFloat(resp.tasa)*100).toFixed(0) : '';
            var comision = resp.comision ? (parseFloat(resp.comision)*100).toFixed(0) : '';
            var producto = resp.producto || '';
            var concepto = 'Desembolso préstamo #' + idPrestamo + ' - ' + cliente + ' - Monto (' + monto + ')';
            if(plazo) concepto += ' - Plazo en Meses (' + plazo + ')';
            if(tasa) concepto += ' - Tasa Interes (' + tasa + ')';
            if(comision) concepto += ' - Comision (' + comision + ')';
            if(producto) concepto += ' - Nombre del producto (' + producto + ')';
            // Mostrar modal de desembolso y setear campos
            $('#modalDesembolso').modal('show');
            setTimeout(function() {
                // Setear campos existentes
                $('[name="monto"]').val(monto);
                $('[name="idprestamo"]').val(idPrestamo);
                $('[name="cliente"]').val(cliente);
                // Agregar campo de fecha primer pago si no existe
                if ($('#modalDesembolso [name="primer_dia_pago"]').length === 0) {
                    var fechaHoy = new Date().toISOString().slice(0,10);
                    var html = '<div class="form-group"><label>Fecha primer pago <span class="text-danger">*</span></label>'+
                        '<input type="date" class="form-control" name="primer_dia_pago" id="modal_primer_dia_pago" required value="'+fechaHoy+'" /></div>';
                    // Insertar antes del botón de ejecutar
                    $('#modalDesembolso .modal-body').append(html);
                }
                // Setear valor por defecto si está vacío
                var fechaHoy = new Date().toISOString().slice(0,10);
                var $primerPago = $('#modalDesembolso [name="primer_dia_pago"]');
                if ($primerPago.val() === '' || !$primerPago.val()) {
                    $primerPago.val(fechaHoy);
                }
            }, 300);
        },'json');
    });
    $('#btnEjecutarDesembolso').on('click',function(){
        var data = $('#formDesembolso').serialize();
        $.post(base_url+'desembolsos/ejecutar_ajax', data, function(resp){
            if(resp && resp.status){
                $('#modalDesembolso').modal('hide');
                cargarDesembolsos();
            }else{
                alert('Error al ejecutar desembolso');
            }
        },'json');
    });
    // --- COPIA DEL JS DE TESORERIA PARA ARMAR EL FORMULARIO DE CHEQUE ---
    $('#btnMovCheque').on('click', function(e){
        e.preventDefault();
        $('#formMovimiento')[0].reset();
        var hoy = new Date().toISOString().slice(0,10);
        $('[name="fecha_registro"], [name="fecha_aplicacion"]').val(hoy);
        $('#modalMovimientoLabel').text('Registrar Cheque');
        $('[name="forma_pago"]').val('CHEQUE');
        // Mostrar solo cuentas tipo banco
        var cuentasBanco = window.cuentasBanco || [];
        var cuentaSelect = '<select class="form-control" name="cuenta_id" id="chequeCuentaBancoSelect" required><option value="">Seleccione cuenta bancaria...</option>';
        for(var i=0;i<cuentasBanco.length;i++){
          cuentaSelect += '<option value="'+cuentasBanco[i].id+'">'+cuentasBanco[i].name+' ('+cuentasBanco[i].code+')</option>';
        }
        cuentaSelect += '</select>';
        var chequeHtml = `
        <div id="chequeCustomLayout">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Fecha primer pago <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="primer_dia_pago" id="modal_primer_dia_pago" required value="`+hoy+`" />
                </div>
            </div>
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
            <input type="hidden" name="departamento" value="" />
            <input type="hidden" name="centro_costos" value="" />
            <input type="hidden" name="proyecto" value="" />
        </div>
        `;
        $('#chequeCustomLayout').remove();
        $('#formMovimiento').append(chequeHtml);
        // Obtener el siguiente número de cheque por AJAX
        $('#chequeCuentaBancoSelect').off('change').on('change', function() {
            var cuentaId = $(this).val();
            if (cuentaId) {
                $.get('/Conta/tesoreria/get_ultimo_numero_cheque_ajax?cuenta_id=' + cuentaId, function(resp) {
                    var nextNum = 1;
                    if(resp && resp.next_numero) nextNum = resp.next_numero;
                    $('#numero_cheque_auto').val(nextNum);
                });
            } else {
                $('#numero_cheque_auto').val('');
            }
        });
        setTimeout(function(){
            $('#chequeCuentaBancoSelect').trigger('change');
        }, 200);
        $('#modalMovimiento').off('hidden.bs.modal.cheque').on('hidden.bs.modal.cheque', function(){
            $('#chequeCustomLayout').hide();
        });
    });
    // --- FIN COPIA ---
});
