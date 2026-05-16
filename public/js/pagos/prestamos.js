$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        allowClear: true,
        placeholder: 'SELECCIONAR'
    });
    var latestTasa = null;
    var latestTasaCompra = null;
    var latestTasaVenta = null;
    var base_usd_amount = null; // store total a pagar in USD

    function setKpi(selector, value) {
        try {
            $(selector).text(value || '-');
        } catch (e) {}
    }

    function loadLatestTasa(tipo) {
        tipo = tipo || null;
        var url = base_url + 'pagos/get_latest_tasa';
        if (tipo) url += '?tipo=' + encodeURIComponent(tipo);
        return $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json'
        }).done(function (resp) {
            if (resp && resp.status) {
                if (resp.tasa) latestTasa = parseFloat(resp.tasa);
                if (resp.row) {
                    if (resp.row.tasa_cambio && parseFloat(resp.row.tasa_cambio) > 0) {
                        latestTasaCompra = parseFloat(resp.row.tasa_cambio);
                    }
                    if (resp.row.tasa_venta && parseFloat(resp.row.tasa_venta) > 0) {
                        latestTasaVenta = parseFloat(resp.row.tasa_venta);
                    }
                }
                if ((!latestTasaVenta || latestTasaVenta <= 0) && latestTasa && latestTasa > 0) {
                    latestTasaVenta = latestTasa;
                }
                if ((!latestTasaCompra || latestTasaCompra <= 0) && latestTasa && latestTasa > 0) {
                    latestTasaCompra = latestTasa;
                }
                $('#modalTcInfo').text('TC Compra: ' + (latestTasaCompra ? Number(latestTasaCompra).toFixed(4) : '-') + ' | TC Venta: ' + (latestTasaVenta ? Number(latestTasaVenta).toFixed(4) : '-'));
            }
        }).fail(function () {
            latestTasa = null;
            latestTasaCompra = null;
            latestTasaVenta = null;
            $('#modalTcInfo').text('TC Compra: - | TC Venta: -');
        });
    }

    function getTasaVenta() {
        if (latestTasaVenta && latestTasaVenta > 0) return latestTasaVenta;
        if (latestTasa && latestTasa > 0) return latestTasa;
        return null;
    }

    function enforceMetodoMonedaRule() {
        var metodo = ($('#modal_metodo').val() || '').toLowerCase();
        if (metodo === 'transferencia') {
            var nioVal = parseFloat($('#modal_monto_nio').val()) || 0;
            if (nioVal > 0) {
                $('#modal_monto_nio').val('0.00');
            }
            $('#modalReglaTransferencia').text('Transferencia: solo USD. NIO se ajusta a 0 automáticamente.');
        } else {
            $('#modalReglaTransferencia').text('Regla: Transferencia solo se registra en USD.');
        }
        syncPaymentSummary();
    }

    function syncPaymentSummary() {
        try {
            var usd = parseFloat($('#modal_monto_usd').val()) || 0;
            var nio = parseFloat($('#modal_monto_nio').val()) || 0;
            var tasaVenta = getTasaVenta();

            if (usd <= 0 && nio <= 0) {
                $('#modal_monto').val('0.00');
                $('#modal_moneda').val('USD');
                $('#modal_moneda_auto').val('');
                $('#modal_equivalente').hide().text('');
                return;
            }

            if ((($('#modal_metodo').val() || '').toLowerCase() === 'transferencia') && nio > 0) {
                nio = 0;
                $('#modal_monto_nio').val('0.00');
            }

            var nioEnUsd = 0;
            if (nio > 0) {
                if (!tasaVenta || tasaVenta <= 0) {
                    $('#modal_equivalente').show().text('Tasa de cambio venta no disponible para convertir NIO a USD.');
                    $('#modal_monto').val('0.00');
                    $('#modal_moneda').val('USD');
                    $('#modal_moneda_auto').val('USD');
                    return;
                }
                nioEnUsd = nio / tasaVenta;
            }

            var totalUsd = usd + nioEnUsd;
            var monedaAuto = 'USD';
            if (usd > 0 && nio > 0) {
                monedaAuto = (usd >= nioEnUsd) ? 'USD' : 'NIO';
            } else if (usd <= 0 && nio > 0) {
                monedaAuto = 'NIO';
            } else {
                monedaAuto = 'USD';
            }

            $('#modal_monto').val(Number(totalUsd).toFixed(2));
            $('#modal_moneda').val(monedaAuto);
            $('#modal_moneda_auto').val(monedaAuto);

            if (tasaVenta && tasaVenta > 0) {
                $('#modal_equivalente').show().text(
                    'USD equivalente total: $' + Number(totalUsd).toFixed(2) +
                    ' | TC Venta: ' + Number(tasaVenta).toFixed(4)
                );
            } else {
                $('#modal_equivalente').show().text('Tasa no disponible para conversión.');
            }
        } catch (e) { console.error(e); }
    }

    // Load clients that have plans
    $.ajax({
        url: base_url + 'planescredito/clients',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            var clients = [];
            if (Array.isArray(data)) clients = data;
            else if (data && Array.isArray(data.clients)) clients = data.clients;
            var $sel = $('#idcliente');
            $sel.empty().append('<option value="">SELECCIONAR</option>');
            clients.forEach(function (c) {
                var val = c.id || c.idcliente ? (c.id || c.idcliente) : ('DOC:' + (c.numero_doc || ''));
                var label = c.nombre ? c.nombre : (c.apellidos ? (c.apellidos + ', ' + c.nombres) : ('DOC:' + (c.numero_doc || '')));
                $sel.append('<option value="' + val + '">' + label + '</option>');
            });
            try {
                $('#idcliente').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: 'Buscar cliente...'
                });
            } catch (e) {}
        }
    });

    $('#idcliente').on('change', function () {
        var id = $(this).val();
        var clienteLabel = $('#idcliente option:selected').text() || '-';
        setKpi('#pg_kpi_cliente', id ? clienteLabel : '-');
        setKpi('#pg_kpi_prestamo', '-');
        setKpi('#pg_kpi_cuota', '-');
        setKpi('#pg_kpi_pendiente', '$0.00');
        setKpi('#pg_kpi_estado_cliente', '-');
        setKpi('#pg_kpi_estado_cuota', '-');
        setKpi('#pg_kpi_dias_mora', '0');
        setKpi('#pg_kpi_mora', '$0.00');
        try { $('#monto_dias_mora').val('0'); } catch (e) {}
        try { $('#monto_mora').val('$0.00'); } catch (e) {}
        $.ajax({
            url: base_url + 'pagos/getCreditosCliente',
            method: 'POST',
            data: {cliente_id: id},
            dataType: 'json',
            success: function (resp) {
                var html = '';
                if (!resp) html = '';
                else if (typeof resp === 'string') html = resp;
                else if (resp.html) html = resp.html;
                else html = resp;
                // Modificar las opciones para concatenar nombre y número de crédito
                var $tmp = $('<select>' + html + '</select>');
                $tmp.find('option').each(function(){
                    var val = $(this).val();
                    var txt = $(this).text();
                    // Si hay data-nombre o similar, concatenar
                    var nombre = $(this).data('nombre') || '';
                    if (nombre && txt.indexOf(nombre) === -1) {
                        $(this).text(nombre + ' - ' + txt);
                    }
                });
                $('#idcredito').empty().append($tmp.html());
                try {
                    $('#idcredito').select2({
                        width: '100%',
                        allowClear: true,
                        placeholder: 'Seleccione préstamo...'
                    });
                } catch (e) {}
                $('#idcuota').html('<option value="">SELECCIONAR</option>');
                $('#monto_couta').val('');
                $('#monto_pendiente').val('');
                try { $('#idcredito').focus(); } catch (e) {}
            }
        });
    });

    $('#idcredito').on('change', function () {
        var id = $(this).val();
        var creditoLabel = $('#idcredito option:selected').text() || '-';
        setKpi('#pg_kpi_prestamo', id ? creditoLabel : '-');
        setKpi('#pg_kpi_cuota', '-');
        setKpi('#pg_kpi_pendiente', '$0.00');
        setKpi('#pg_kpi_estado_cliente', '-');
        setKpi('#pg_kpi_estado_cuota', '-');
        setKpi('#pg_kpi_dias_mora', '0');
        setKpi('#pg_kpi_mora', '$0.00');
        try { $('#monto_dias_mora').val('0'); } catch (e) {}
        try { $('#monto_mora').val('$0.00'); } catch (e) {}
        if (typeof id === 'string' && id.indexOf('P-') === 0) {
            var prestamo_id = id.split('-')[1];
            $.ajax({
                url: base_url + 'pagos/getPrestamoNextCuota',
                method: 'POST',
                data: {idprestamo: prestamo_id},
                dataType: 'json',
                success: function (resp) {
                    if (resp.status) {
                        $('#idcuota').html(resp.html);
                        try {
                            $('#idcuota').select2({
                                width: '100%',
                                allowClear: true,
                                placeholder: 'Seleccione cuota...'
                            });
                        } catch (e) {}
                        // SIEMPRE llenar los campos de saldo aunque no cambie el select
                        var cuotaObj = resp.cuota || {};
                        $('#monto_couta').val(cuotaObj.cuota !== undefined ? cuotaObj.cuota : '');
                        setKpi('#pg_kpi_cuota', (cuotaObj.idcuota || cuotaObj.numero || $('#idcuota option:selected').text() || '-').toString());
                        var montoPend = parseFloat(cuotaObj.saldo) || 0;
                        var moraCalc = parseFloat(cuotaObj.mora) || 0;
                        var totalPagar = (parseFloat(cuotaObj.total_pagar) || 0);
                        setKpi('#pg_kpi_pendiente', '$' + totalPagar.toFixed(2));
                        setKpi('#pg_kpi_estado_cliente', cuotaObj.estado_cliente || '-');
                        setKpi('#pg_kpi_estado_cuota', cuotaObj.estado || '-');
                        setKpi('#pg_kpi_dias_mora', String(parseInt(cuotaObj.dias_atraso || 0, 10) || 0));
                        setKpi('#pg_kpi_mora', '$' + moraCalc.toFixed(2));
                        // store base USD amount - prefer total_pagar, fallback to saldo + cuota
                        var guessed = null;
                        if (typeof cuotaObj.total_pagar !== 'undefined' && cuotaObj.total_pagar !== null) guessed = cuotaObj.total_pagar;
                        if ((guessed === null || guessed === '') && typeof cuotaObj.saldo !== 'undefined') {
                            guessed = (parseFloat(cuotaObj.saldo) || 0) + (parseFloat(cuotaObj.mora) || 0);
                        }
                        if ((guessed === null || guessed === '') && typeof cuotaObj.cuota !== 'undefined') guessed = cuotaObj.cuota;
                        base_usd_amount = parseFloat(guessed) || 0;
                        try { $('#monto_pendiente').val(totalPagar.toFixed(2)); } catch(e) {}
                        try { $('#monto_dias_mora').val(String(parseInt(cuotaObj.dias_atraso || 0, 10) || 0)); } catch(e) {}
                        try { $('#monto_mora').val('$' + moraCalc.toFixed(2)); } catch(e) {}
                        try { $('#modal_pendiente_cuota').val('$' + montoPend.toFixed(2)); } catch(e) {}
                        try { $('#modal_principal').val('$' + (parseFloat(cuotaObj.principal) || 0).toFixed(2)); } catch(e) {}
                        try { $('#modal_interes_corriente').val('$' + (parseFloat(cuotaObj.interes_corriente) || 0).toFixed(2)); } catch(e) {}
                        try { $('#modal_interes_moratorio').val('$' + (parseFloat(cuotaObj.interes_moratorio) || 0).toFixed(2)); } catch(e) {}
                        try { $('#modal_mora_calculada').val('$' + moraCalc.toFixed(2)); } catch(e) {}
                        try { $('#modal_cuotas_atrasadas').val(String(parseInt(cuotaObj.cuotas_atrasadas || 0, 10) || 0)); } catch(e) {}
                        try { $('#modal_estado_cuota').val(cuotaObj.estado || '-'); } catch(e) {}
                        try { $('#modal_estado_cliente').val(cuotaObj.estado_cliente || '-'); } catch(e) {}
                        try { $('#modal_total_pagar').val('$' + totalPagar.toFixed(2)); } catch(e) {}
                        // Saldo para Cancelar Prestamos = total_pending
                        if (typeof resp.total_pending !== 'undefined') {
                            var val_cancelar = parseFloat(resp.total_pending) || 0;
                            $('#saldo_cancelar').val(val_cancelar.toFixed(2));
                            $('#modal_saldo_credito').val(val_cancelar.toFixed(2));
                        } else {
                            $.ajax({
                                url: base_url + 'pagos/getPrestamoSaldo',
                                method: 'POST',
                                data: {idprestamo: prestamo_id},
                                dataType: 'json',
                                success: function(saldoResp){
                                    var val_cancelar = (saldoResp && saldoResp.status) ? parseFloat(saldoResp.total_saldo) || 0 : 0;
                                    $('#saldo_cancelar').val(val_cancelar.toFixed(2));
                                    $('#modal_saldo_credito').val(val_cancelar.toFixed(2));
                                }
                            });
                        }
                        // Saldo para ponerlo al día = saldo de la cuota seleccionada + mora
                        $('#saldo_aldia').val(totalPagar.toFixed(2));
                        // Forzar actualización visual de los campos de saldo al cambiar el select de cuota
                        $('#idcuota').off('change.saldo').on('change.saldo', function() {
                            var val = $(this).val();
                            // Si hay solo una opción, igual forzar el llenado
                            $('#saldo_cancelar').trigger('input');
                            $('#saldo_aldia').trigger('input');
                        });
                        // load latest tasa then update modal monto according to selected moneda
                        loadLatestTasa('venta').always(function(){ syncPaymentSummary(); });
                        // set default fecha_pago and compute estado relative to that date
                        try {
                            setDefaultFechaPago();
                            var fechaPagoVal = $('#modal_fecha_pago').val();
                            var venc = cuotaObj.fecha_vencimiento ? cuotaObj.fecha_vencimiento : null;
                            if (venc && venc.length === 10) venc = venc + 'T00:00';
                            var estadoObj = computeEstado(venc, fechaPagoVal);
                            var estadoText = estadoObj.estado + (estadoObj.dias > 0 ? ' (' + estadoObj.dias + ' días atraso)' : '');
                            $('#modal_estado_cuota').val(estadoText);
                            try { $('#modal_fecha_pago').data('venc', venc); } catch(e) {}
                        } catch(e) {}
                    } else {
                        $('#idcuota').html('<option value="">SELECCIONAR</option>');
                        $('#saldo_cancelar').val('');
                        $('#saldo_aldia').val('');
                    }
                }
            });
            return;
        }
    });

    // When opening modal, fill summary fields
    // set default fecha_pago when opening modal and recompute estado based on selected fecha
    function setDefaultFechaPago() {
        var now = new Date();
        function pad(n){return n<10?('0'+n):n}
        var local = now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate());
        try { $('#modal_fecha_pago').val(local); } catch(e) {}
    }

    // compute estado locally given fecha_vencimiento and fecha_pago (both ISO-like strings)
    // Clasificación CONAMI para Estado Cuota
    function computeEstado(fecha_vencimiento, fecha_pago) {
        if (!fecha_vencimiento) return {estado: 'Sin fecha', dias: 0};
        var due = new Date(fecha_vencimiento);
        var pay = fecha_pago ? new Date(fecha_pago) : new Date();
        var dueYMD = new Date(due.getFullYear(), due.getMonth(), due.getDate());
        var payYMD = new Date(pay.getFullYear(), pay.getMonth(), pay.getDate());
        var diff = Math.floor((payYMD - dueYMD) / (1000*60*60*24));
        var dias = diff > 0 ? diff : 0;
        var estado = '';
        if (dias <= 0) {
            estado = 'Al Día';
        } else if (dias >= 1 && dias <= 15) {
            estado = 'Mora de 1 a 15 días';
        } else if (dias >= 16 && dias <= 30) {
            estado = 'Mora de 16 a 30 días';
        } else if (dias >= 31 && dias <= 60) {
            estado = 'Mora de 31 a 60 días';
        } else if (dias >= 61 && dias <= 90) {
            estado = 'Mora de 61 a 90 días';
        } else if (dias >= 91 && dias <= 120) {
            estado = 'Mora de 91 a 120 días';
        } else if (dias >= 121 && dias <= 180) {
            estado = 'Mora de 121 a 180 días';
        } else if (dias >= 181 && dias <= 240) {
            estado = 'Mora de 181 a 240 días';
        } else if (dias >= 241 && dias <= 360) {
            estado = 'Mora de 241 a 360 días';
        } else if (dias > 360) {
            estado = 'Mora mayor a 361 días';
        }
        return {estado: estado, dias: dias};
    }

    $('#btnPagarPrestamo').on('click', function () {
        if (!$('#idcliente').val()) {
            alert('Seleccione un cliente.');
            return;
        }
        if (!$('#idcredito').val()) {
            alert('Seleccione un préstamo.');
            return;
        }
        if (!$('#idcuota').val()) {
            alert('Seleccione una cuota.');
            return;
        }

        var clienteText = $('#idcliente option:selected').text();
        var creditoText = $('#idcredito option:selected').text();
        var cuotaText = $('#idcuota option:selected').text();
        $('#modal_cliente').val(clienteText);
        $('#modal_credito').val(creditoText);
        $('#modal_cuota').val(cuotaText);
        // Iniciar vacío para que el usuario decida en qué moneda registrar el pago
        $('#modal_monto_usd').val('');
        $('#modal_monto_nio').val('');
        $('#modal_moneda_auto').val('');
        $('#modal_moneda').val('USD');
        $('#modal_monto').val('0.00');
        $('#modal_equivalente').hide().text('');
        try { setDefaultFechaPago(); } catch(e) {}
        loadLatestTasa('venta').always(function(){ syncPaymentSummary(); enforceMetodoMonedaRule(); });
        $('#prestamoPagoAlert').addClass('d-none').removeClass('alert-success alert-danger').text('');
        setKpi('#pg_kpi_cliente', clienteText || '-');
        setKpi('#pg_kpi_prestamo', creditoText || '-');
        setKpi('#pg_kpi_cuota', cuotaText || '-');
        setKpi('#pg_kpi_pendiente', $('#modal_total_pagar').val() || '$0.00');
        setKpi('#pg_kpi_estado_cliente', $('#modal_estado_cliente').val() || '-');
        setKpi('#pg_kpi_estado_cuota', $('#modal_estado_cuota').val() || '-');
    });

    $('#idcuota').on('change', function () {
        var cuotaText = $('#idcuota option:selected').text() || '-';
        setKpi('#pg_kpi_cuota', ($(this).val() ? cuotaText : '-'));
        var pendiente = parseFloat($('#monto_pendiente').val()) || 0;
        setKpi('#pg_kpi_pendiente', '$' + pendiente.toFixed(2));
    });

    $(document).on('change', '#modal_metodo', function () {
        enforceMetodoMonedaRule();
    });

    $(document).on('input', '#modal_monto_usd, #modal_monto_nio', function () {
        syncPaymentSummary();
    });

    $('#submitPagarPrestamo').on('click', function () {
        if (!$('#modal_monto').val() || parseFloat($('#modal_monto').val()) <= 0) {
            $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Ingrese un monto válido.');
            return;
        }
        if (!$('#modal_referencia').val()) {
            $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Seleccione una serie de recibo.');
            return;
        }

        var metodoSel = ($('#modal_metodo').val() || '').toLowerCase();
        var montoUsdInput = parseFloat($('#modal_monto_usd').val()) || 0;
        var montoNioInput = parseFloat($('#modal_monto_nio').val()) || 0;
        var tasaVenta = getTasaVenta();

        if (metodoSel === 'transferencia' && montoNioInput > 0) {
            $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Las transferencias solo se permiten en USD.');
            return;
        }

        if (montoNioInput > 0 && (!tasaVenta || tasaVenta <= 0)) {
            $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text('No hay TC Venta disponible para convertir NIO.');
            return;
        }

        var usdEquivalenteNio = (montoNioInput > 0 && tasaVenta > 0) ? (montoNioInput / tasaVenta) : 0;
        var totalUsd = montoUsdInput + usdEquivalenteNio;
        var monedaAuto = 'USD';
        if (montoUsdInput > 0 && montoNioInput > 0) {
            monedaAuto = (montoUsdInput >= usdEquivalenteNio) ? 'USD' : 'NIO';
        } else if (montoUsdInput <= 0 && montoNioInput > 0) {
            monedaAuto = 'NIO';
        }

        $('#modal_monto').val(Number(totalUsd).toFixed(2));
        $('#modal_moneda').val(monedaAuto);
        $('#modal_moneda_auto').val(monedaAuto);

        var detalleMonedas = 'USD: ' + Number(montoUsdInput).toFixed(2) +
            ' | NIO: ' + Number(montoNioInput).toFixed(2) +
            ' | TC Venta: ' + (tasaVenta && tasaVenta > 0 ? Number(tasaVenta).toFixed(4) : '-');
        var referenciaAdicional = ($('#modal_dato_adicional').val() || '').trim();
        var referenciaFinal = referenciaAdicional ? (referenciaAdicional + ' | ' + detalleMonedas) : detalleMonedas;

        var data = {
            cliente_id: $('#idcliente').val(),
            idcredito: $('#idcredito').val(),
            idcuota: $('#idcuota').val(),
            monto: Number(totalUsd).toFixed(2),
            metodo: $('#modal_metodo').val(),
            referencia: $('#modal_referencia').val(),
            moneda: monedaAuto,
            fecha_pago: $('#modal_fecha_pago').val(),
            dato_adicional: referenciaFinal,
            tc_compra: (latestTasaCompra && latestTasaCompra > 0) ? latestTasaCompra : '',
            tc_venta: (latestTasaVenta && latestTasaVenta > 0) ? latestTasaVenta : '',
            monto_usd: Number(montoUsdInput).toFixed(2),
            monto_nio: Number(montoNioInput).toFixed(2)
        };
        $.ajax({
            url: base_url + 'pagos/savePrestamoPagoProvisional',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (resp.status) {
                    $('#prestamoPagoAlert').removeClass('d-none alert-danger').addClass('alert-success').text(resp.message || 'Pago provisional registrado');
                    setTimeout(function () {
                        window.location.href = (resp.redirect || (base_url + 'pagos?date_from=&date_to=&q=&idserie='));
                    }, 900);
                } else {
                    $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text(resp.message || 'Error al registrar');
                }
            },
            error: function () {
                $('#prestamoPagoAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Error de conexión');
            }
        });
    });

    // Load series_recibos into modal select
    function loadSeriesRecibos() {
        $.ajax({
            url: base_url + 'series_recibos/list',
            method: 'GET',
            dataType: 'json',
            success: function (rows) {
                var $sel = $('#modal_referencia');
                $sel.empty().append('<option value="">SELECCIONAR</option>');
                if (Array.isArray(rows)) {
                    rows.forEach(function (r) {
                        var label = (r.codigo ? r.codigo + ' - ' : '') + (r.nombre ? r.nombre : '');
                        $sel.append('<option value="' + (r.idserie || r.id || r.idserie) + '">' + label + '</option>');
                    });
                    // Selección automática: por defecto la primera serie disponible
                    if ($sel.find('option').length > 1) {
                        $sel.val($sel.find('option:eq(1)').val());
                    }
                }
            }
        });
    }

    // Load series on page ready so modal has options
    loadSeriesRecibos();

    // when user changes fecha de pago, recompute estado using stored venc date
    $(document).on('change', '#modal_fecha_pago', function () {
        try {
            var venc = $(this).data('venc');
            var val = $(this).val();
            if (venc && venc.length === 10) venc = venc + 'T00:00';
            var estadoObj = computeEstado(venc, val);
            var estadoText = estadoObj.estado + (estadoObj.dias > 0 ? ' (' + estadoObj.dias + ' días atraso)' : '');
            $('#modal_estado_cuota').val(estadoText);
        } catch (e) {}
    });

});
