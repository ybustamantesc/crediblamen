$(function(){
    var base_url = window.location.origin + '/Crediblamen/';
    var camposCosto = ['costos_legales', 'seguros', 'comisiones'];

    function renovacionDetalleVisible() {
        return $('#renovacion_detalle_group').is(':visible');
    }

    function getTotalRenovacion() {
        if (renovacionDetalleVisible()) {
            var renovPrincipal = parseFloat($('#modal_renov_principal').val()) || 0;
            var renovInteresCorriente = parseFloat($('#modal_renov_interes_corriente').val()) || 0;
            var renovInteresMora = parseFloat($('#modal_renov_interes_mora').val()) || 0;
            return renovPrincipal + renovInteresCorriente + renovInteresMora;
        }
        return parseFloat($('#modal_monto_renovacion').val()) || 0;
    }

    function sincronizarMontoRenovacion() {
        var totalRenovacion = getTotalRenovacion();
        $('#modal_monto_renovacion').val(totalRenovacion.toFixed(2));
    }

    function setRenovacionDetalleVisible(show) {
        $('#renovacion_detalle_group').toggle(show);
        $('#btnToggleRenovacionDetalle').text(show ? 'Ocultar desglose' : 'Mostrar desglose');
        if (show) {
            var totalRenovacion = parseFloat($('#modal_monto_renovacion').val()) || 0;
            var principal = parseFloat($('#modal_renov_principal').val()) || 0;
            var interesCorriente = parseFloat($('#modal_renov_interes_corriente').val()) || 0;
            var interesMora = parseFloat($('#modal_renov_interes_mora').val()) || 0;
            if (principal === 0 && interesCorriente === 0 && interesMora === 0 && totalRenovacion > 0) {
                $('#modal_renov_principal').val(totalRenovacion.toFixed(2));
            }
            calcularTotalDesembolso();
        }
    }

    function setRenovacionSeccionVisible(show) {
        if (show) {
            $('#renovacion_seccion').stop(true, true).slideDown(160);
        } else {
            $('#renovacion_seccion').stop(true, true).slideUp(120);
        }
        $('#btnToggleRenovacionSeccion').text(show ? 'Ocultar renovación' : 'Mostrar renovación');
        if (!show) {
            $('#renovacion_detalle_group').hide();
            $('#btnToggleRenovacionDetalle').text('Mostrar desglose');
        } else {
            setTimeout(function() {
                var seccion = document.getElementById('renovacion_seccion');
                if (seccion && typeof seccion.scrollIntoView === 'function') {
                    seccion.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                $('#modal_monto_renovacion').focus();
            }, 180);
        }
        calcularTotalDesembolso();
    }

    function setEdicionCostoCampo(field, activa) {
        $('#modal_' + field).prop('readonly', !activa);
        $('#grupo_comentario_' + field).toggle(activa);
        $('#btn_editar_' + field).toggle(!activa);
        $('#btn_guardar_' + field).toggle(activa);
        if (activa) {
            $('#confirmado_' + field).val('0');
            $('#comentario_' + field).focus();
        }
    }

    function resetCostosModal() {
        camposCosto.forEach(function(field){
            $('#modal_' + field).val('0').prop('readonly', true);
            $('#comentario_' + field).val('');
            $('#confirmado_' + field).val('0');
            $('#grupo_comentario_' + field).hide();
            $('#btn_editar_' + field).show();
            $('#btn_guardar_' + field).hide();
        });

        $('#modal_saldo_renovacion').val('0');
        $('#modal_monto_renovacion').val('0');
        $('#modal_renov_principal').val('0');
        $('#modal_renov_interes_corriente').val('0');
        $('#modal_renov_interes_mora').val('0');
        $('#modal_comentario_renovacion').val('');
        setRenovacionSeccionVisible(false);
        setRenovacionDetalleVisible(false);

        calcularTotalDesembolso();
    }

    // ========== CARGA INICIAL DE DESEMBOLSOS ==========
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
                    } else if (d.estado === 'pendiente_aprobacion') {
                        estado = '<span class="badge badge-pendiente-aprobacion">Pendiente Aprobación</span>';
                    } else if (d.estado === 'pendiente') {
                        estado = '<span class="badge badge-pendiente">Pendiente</span>';
                    } else {
                        estado = '<span class="badge badge-desembolsado">Procesado</span>';
                    }
                    var btn = '';
                    var resumen = '';
                    if(d.estado === 'anulado') {
                        resumen = '<div class="text-muted small">Crédito anulado<br>'+(d.obs_desembolso ? d.obs_desembolso : '')+'</div>';
                    } else if (d.estado === 'pendiente_aprobacion') {
                        btn = '<button class="btn btn-sm btn-warning" type="button" disabled style="pointer-events:none;opacity:0.85;">Pendiente Aprobación</button>';
                    } else if(d.desembolsado == 0 || d.desembolsado === null) {
                        btn = '<button class="btn btn-sm btn-primary btn-desembolsar" data-id="'+d.idprestamo+'" data-monto="'+d.monto+'" data-fecha="'+d.fecha_desembolso+'" data-cliente="'+d.cliente+'">Desembolsar</button>';
                    } else {
                        btn = '<button class="btn btn-sm btn-success btn-vista-previa-desembolso" data-id="'+d.idprestamo+'" data-cliente="'+encodeURIComponent(d.cliente || '')+'" data-monto="'+d.monto+'" data-fecha="'+encodeURIComponent(d.fecha_desembolso_real ? d.fecha_desembolso_real : d.fecha_desembolso)+'" data-usuario="'+encodeURIComponent(d.usuario_desembolso_nombre ? d.usuario_desembolso_nombre : (d.usuario_desembolso || '-'))+'" data-plazo="'+(d.plazo || 0)+'" data-tasa="'+(d.tasa || 0)+'" data-costos-legales="'+(d.costos_legales || 0)+'" data-seguros="'+(d.seguros || 0)+'" data-comisiones="'+(d.comisiones || 0)+'" data-obs="'+encodeURIComponent(d.obs_desembolso ? d.obs_desembolso : '')+'">Vista previa</button>';
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

    // ========== CARGAR CUENTAS BANCARIAS ==========
    function cargarCuentasBancarias() {
        $.get(base_url + 'tesoreria/get_cuentas_banco_ajax', function(resp) {
            var cuentas = resp || [];
            var $select = $('#modal_cuenta_bancaria');
            $select.empty().append('<option value="">-- Seleccione cuenta --</option>');
            if(cuentas && cuentas.length) {
                cuentas.forEach(function(c) {
                    $select.append('<option value="' + c.id + '" data-code="' + (c.code || c.numero) + '">' + c.name + ' (' + (c.code || c.numero) + ')</option>');
                });
            }
        }, 'json').fail(function(){
            console.log('Error cargando cuentas bancarias');
        });
    }

    // ========== CALCULAR TOTAL A DESEMBOLSAR ==========
    function calcularTotalDesembolso() {
        var montoCreditoStr = $('#modal_monto_credito').val() || '0';
        var costosLegales = parseFloat($('#modal_costos_legales').val()) || 0;
        var seguros = parseFloat($('#modal_seguros').val()) || 0;
        var comisiones = parseFloat($('#modal_comisiones').val()) || 0;
        var totalRenovacion = getTotalRenovacion();
        
        var montoCredito = parseFloat(montoCreditoStr);
        var totalCostos = costosLegales + seguros + comisiones;
        var totalDesembolso = montoCredito - totalCostos - totalRenovacion;
        
        if(totalDesembolso < 0) totalDesembolso = 0;

        if (renovacionDetalleVisible()) {
            sincronizarMontoRenovacion();
        }
        
        $('#total_a_desembolsar').text(totalDesembolso.toFixed(2));
    }

    // ========== LISTENERS PARA COSTOS ==========
    $(document).on('change keyup', '.costos-input', function() {
        var field = $(this).attr('id').replace('modal_', '');
        if ($('#confirmado_' + field).val() === '1') {
            $('#confirmado_' + field).val('0');
        }
        calcularTotalDesembolso();
    });

    $(document).on('click', '.btn-editar-costo', function() {
        var field = $(this).data('field');
        setEdicionCostoCampo(field, true);
    });

    $(document).on('click', '.btn-guardar-costo', function() {
        var field = $(this).data('field');
        var valor = parseFloat($('#modal_' + field).val()) || 0;
        var comentario = ($.trim($('#comentario_' + field).val()) || '');

        if (valor > 0 && !comentario) {
            alert('Debe escribir un comentario obligatorio para guardar este campo.');
            $('#comentario_' + field).focus();
            return;
        }

        if (valor < 0) {
            alert('Los costos no pueden ser negativos.');
            return;
        }

        $('#confirmado_' + field).val('1');
        setEdicionCostoCampo(field, false);
        calcularTotalDesembolso();
    });

    $(document).on('change keyup', '.renov-input', function() {
        calcularTotalDesembolso();
    });

    $(document).on('change keyup', '.renov-total-input', function() {
        if (!renovacionDetalleVisible()) {
            calcularTotalDesembolso();
        }
    });

    $(document).on('click', '#btnToggleRenovacionSeccion', function() {
        setRenovacionSeccionVisible(!$('#renovacion_seccion').is(':visible'));
    });

    $(document).on('click', '#btnToggleRenovacionDetalle', function() {
        setRenovacionDetalleVisible(!renovacionDetalleVisible());
    });

    // ========== ABRIR MODAL DE DESEMBOLSO ==========
    $(document).on('click', '.btn-desembolsar', function(){
        var idPrestamo = $(this).data('id');
        var cliente = $(this).data('cliente');
        var monto = $(this).data('monto');
        var fechaDesembolso = $(this).data('fecha');

        // Obtener datos adicionales por AJAX
        $.get(base_url + 'desembolsos/detalle_ajax', {idprestamo: idPrestamo}, function(resp) {
            // Mostrar modal
            $('#modalDesembolso').modal('show');
            
            setTimeout(function() {
                // Setear campos principales
                $('#modal_idprestamo').val(idPrestamo);
                $('#modal_monto_credito').val(parseFloat(monto).toFixed(2));
                $('#modal_saldo_renovacion').val(parseFloat(resp.saldo_renovacion || 0).toFixed(2));
                
                // Fecha de desembolso (hoy o la del plan)
                var fechaHoy = new Date().toISOString().slice(0,10);
                $('#modal_fecha_desembolso').val(fechaDesembolso || fechaHoy);
                
                // Fecha de primer pago (30 días después por defecto)
                var fechaPrimerPago = new Date();
                fechaPrimerPago.setDate(fechaPrimerPago.getDate() + 30);
                var fechaPrimerPagoStr = fechaPrimerPago.toISOString().slice(0,10);
                $('#modal_primer_dia_pago').val(fechaPrimerPagoStr);
                
                // Limpiar costos
                resetCostosModal();
                
                // Cargar cuentas bancarias
                cargarCuentasBancarias();
                
            }, 300);
        }, 'json');
    });

    $(document).on('click', '.btn-vista-previa-desembolso', function() {
        var idPrestamo = $(this).data('id') || '-';
        var cliente = decodeURIComponent($(this).attr('data-cliente') || '') || '-';
        var monto = parseFloat($(this).data('monto')) || 0;
        var fecha = decodeURIComponent($(this).attr('data-fecha') || '') || '-';
        var usuario = decodeURIComponent($(this).attr('data-usuario') || '') || '-';
        var plazo = parseInt($(this).data('plazo'), 10) || 0;
        var tasaRaw = parseFloat($(this).data('tasa')) || 0;
        var costosLegales = parseFloat($(this).data('costos-legales')) || 0;
        var seguros = parseFloat($(this).data('seguros')) || 0;
        var comisiones = parseFloat($(this).data('comisiones')) || 0;
        var observaciones = decodeURIComponent($(this).attr('data-obs') || '');
        var tasa = tasaRaw > 1 ? tasaRaw : (tasaRaw * 100);
        var diaEjecutado = fecha && fecha.length >= 10 ? fecha.substring(0, 10) : fecha;

        $('#preview_plan').text(idPrestamo);
        $('#preview_cliente').text(cliente);
        $('#preview_monto_credito').text('$ ' + monto.toFixed(2));
        $('#preview_fecha').text(diaEjecutado || '-');
        $('#preview_plazo').text(plazo > 0 ? (plazo + ' cuotas') : '-');
        $('#preview_tasa').text(tasa ? (tasa.toFixed(2) + ' %') : '-');
        $('#preview_costos_legales').text('$ ' + costosLegales.toFixed(2));
        $('#preview_seguros').text('$ ' + seguros.toFixed(2));
        $('#preview_comisiones').text('$ ' + comisiones.toFixed(2));
        $('#preview_usuario').text(usuario);
        $('#preview_obs').text(observaciones || 'Sin observaciones registradas.');

        $('#modalPreviewDesembolso').modal('show');
    });

    // ========== EJECUTAR DESEMBOLSO CON CHEQUE ==========
    $('#btnEjecutarDesembolso').on('click', function(){
        // Validaciones
        if(!$('#modal_cuenta_bancaria').val()) {
            alert('Por favor selecciona una cuenta bancaria');
            return;
        }
        
        var idPrestamo = $('#modal_idprestamo').val();
        var montoCreditoStr = $('#modal_monto_credito').val();
        var costos_legales = parseFloat($('#modal_costos_legales').val()) || 0;
        var seguros = parseFloat($('#modal_seguros').val()) || 0;
        var comisiones = parseFloat($('#modal_comisiones').val()) || 0;
        var renov_principal = parseFloat($('#modal_renov_principal').val()) || 0;
        var renov_interes_corriente = parseFloat($('#modal_renov_interes_corriente').val()) || 0;
        var renov_interes_mora = parseFloat($('#modal_renov_interes_mora').val()) || 0;
        var monto_renovacion = renovacionDetalleVisible()
            ? (renov_principal + renov_interes_corriente + renov_interes_mora)
            : (parseFloat($('#modal_monto_renovacion').val()) || 0);
        var saldo_renovacion = parseFloat($('#modal_saldo_renovacion').val()) || 0;
        var montoCredito = parseFloat(montoCreditoStr);
        var totalCostos = costos_legales + seguros + comisiones;
        var totalRenovacion = monto_renovacion;
        var totalDesembolso = montoCredito - totalCostos - totalRenovacion;

        if (totalDesembolso < 0) {
            alert('El total a desembolsar no puede ser negativo. Revise los costos.');
            return;
        }

        if (totalRenovacion > saldo_renovacion && saldo_renovacion > 0) {
            alert('La suma de Principal + Interés Corriente + Interés en Mora no puede exceder el saldo de renovación.');
            return;
        }

        for (var i = 0; i < camposCosto.length; i++) {
            var field = camposCosto[i];
            var value = parseFloat($('#modal_' + field).val()) || 0;
            var confirmed = $('#confirmado_' + field).val() === '1';
            var comentario = $.trim($('#comentario_' + field).val() || '');
            if (value > 0 && (!confirmed || !comentario)) {
                alert('Debe editar y guardar ' + field.replace('_', ' ') + ' con comentario obligatorio.');
                return;
            }
        }
        
        var data = {
            idprestamo: idPrestamo,
            fecha_desembolso: $('#modal_fecha_desembolso').val(),
            primer_dia_pago: $('#modal_primer_dia_pago').val(),
            cuenta_bancaria_id: $('#modal_cuenta_bancaria').val(),
            monto_credito: montoCreditoStr,
            total_desembolso: totalDesembolso,
            costos_legales: costos_legales,
            seguros: seguros,
            comisiones: comisiones,
            comentario_costos_legales: $('#comentario_costos_legales').val(),
            comentario_seguros: $('#comentario_seguros').val(),
            comentario_comisiones: $('#comentario_comisiones').val(),
            confirmado_costos_legales: $('#confirmado_costos_legales').val(),
            confirmado_seguros: $('#confirmado_seguros').val(),
            confirmado_comisiones: $('#confirmado_comisiones').val(),
            monto_renovacion: monto_renovacion,
            saldo_renovacion: saldo_renovacion,
            renov_principal: renov_principal,
            renov_interes_corriente: renov_interes_corriente,
            renov_interes_mora: renov_interes_mora,
            comentario_renovacion: $('#modal_comentario_renovacion').val(),
            observaciones: $('#modal_observaciones').val()
        };
        
        $.post(base_url + 'desembolsos/ejecutar_desembolso_con_cheque_ajax', data, function(resp){
            if(resp && resp.status === 'success') {
                $('#modalDesembolso').modal('hide');
                alert(resp.message || 'Solicitud de desembolso creada.');
                cargarDesembolsos();
            } else {
                alert('Error: ' + (resp && resp.message ? resp.message : 'Error desconocido'));
            }
        }, 'json').fail(function(xhr){
            alert('Error al procesar: ' + xhr.statusText);
        });
    });

    // ========== INICIALIZAR ==========
    cargarDesembolsos();
    $('form').on('submit', function(e){ 
        e.preventDefault(); 
        cargarDesembolsos(); 
    });
    $('#desembolsos_search').on('keyup', function(e){ 
        if(e.keyCode==13) cargarDesembolsos(); 
    });
});
