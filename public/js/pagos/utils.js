$(document).ready(function () {
    $('.select2').select2();

    $('#numero_coutas').change(function () {
        calcular();
    });
    $('#numero_coutas').change(function () {
        calcular();
    });
    $('#interes_credito').change(function () {
        calcular();
    });
    $('#descuento').change(function () {
        let descuento = $(this).val();
        let total = $("#total_pagar").val();
        let nuevo_total = total - descuento;
        $("#total_pagar").val(nuevo_total).toFixed(2);

    });
    $('#idcliente').change(function () {
        var id = $(this).val();
        $.ajax({
            url: base_url + "pagos/getCreditosCliente",
            method: "POST",
            data: {cliente_id: id},
            async: true,
            dataType: 'json',
            success: function (data) {
                // Replace options and refresh Select2
                $("#idcredito").empty().append(data);
                try {
                    // If select2 is used, destroy and re-init to refresh options
                    if ($("#idcredito").data('select2')) {
                        $("#idcredito").select2('destroy');
                    }
                } catch (e) {}
                $("#idcredito").select2({ width: '100%' });
                // auto-select first option (if any non-empty) and trigger change
                var firstOpt = $("#idcredito option").filter(function(){ return $(this).val() !== ''; }).first();
                if (firstOpt && firstOpt.val()) {
                    $("#idcredito").val(firstOpt.val()).trigger('change');
                }
            }
        });
    });
    // $('#cliente').change(function() {
    //     var id = $(this).val();      
    //     listarCreditosTabla(id);
    // });
    $('#idcredito').change(function () {
        var id = $(this).val();
        if (typeof id === 'string' && id.indexOf('P-') === 0) {
            var prestamo_id = id.split('-')[1];
            $.ajax({
                url: base_url + "pagos/getPrestamoNextCuota",
                method: "POST",
                data: {idprestamo: prestamo_id},
                async: true,
                dataType: 'json',
                success: function (resp) {
                    if (resp.status) {
                        $("#idcuota").html(resp.html);
                        // fill amounts using cuota object
                        if (resp.cuota) {
                            $("#monto_couta").val(resp.cuota.cuota);
                            $("#monto_pendiente").val(resp.cuota.saldo);
                        }
                        // disable manual change
                        $("#idcuota").prop('disabled', true);
                        $("#idcredito").prop('disabled', true);
                    } else {
                        $("#idcuota").html('<option value="" selected>SELECCIONAR</option>');
                    }
                }
            });
            return;
        }
        $.ajax({
            url: base_url + "pagos/getCreditoId",
            method: "POST",
            data: {credito_id: id},
            async: true,
            dataType: 'json',
            success: function (data) {
                $("#monto_credito").val(data.total_pagar);
                $("#fecha_credito").val(data.fecha_credito);
                listarCoutasTabla(id);
                listarCoutas(id);
                // enable selects in case previously disabled
                $("#idcuota").prop('disabled', false);
                $("#idcredito").prop('disabled', false);
            }
        });
    });
    $('#idcuota').change(function () {
        var id = $(this).val();
        $.ajax({
            url: base_url + "pagos/getCuotaId",
            method: "POST",
            data: {cuota_id: id},
            async: true,
            dataType: 'json',
            success: function (data) {
                $("#monto_couta").val(data.monto_couta);
                $("#fecha_couta").val(data.fecha_couta);
                $("#monto_pendiente").val(data.monto_pendiente);
            }
        });
    });
    $('#monto_pago').change(function () {
        let monto_pagado = parseFloat($(this).val()).toFixed(2);
        let monto_couta = parseFloat($("#monto_couta").val()).toFixed(2);
        let monto_pendiente = parseFloat($("#monto_pendiente").val()).toFixed(2);
        // if (monto_pagado > monto_couta) {
        //     alert("El valor ingresado no puede ser mayor al monto de cuota.");
        //     return
        // }
        //if (Number(monto_pagado) > Number(monto_pendiente)) {
        //    alert("El valor ingresado no puede ser mayor al monto pendiente.");
        //    return false;
        //} else {
        //    monto_pendiente = monto_pendiente - monto_pagado;
        //}
        
        // Actualizar el monto pendiente restando el monto pagado
        monto_pendiente = monto_pendiente - monto_pagado;
        
        // Asegurarse de que el monto pendiente no sea negativo
        if (monto_pendiente < 0) {
            monto_pendiente = 0;  // O puedes ajustar este comportamiento según lo que necesites
        }

        $("#monto_pendiente").val(monto_pendiente.toFixed(2));
    });

});

function calcularPago(monto_pagado) {
    let monto_couta = parseFloat($("#monto_couta").val()).toFixed(2);
    let monto_pendiente = parseFloat($("#monto_pendiente").val()).toFixed(2);
    
    //if (monto_pagado < monto_couta) {
    //    alert("El valor ingresado no puede ser mayor al monto de cuota.");
    //    return
    //}
    //if (monto_pagado < monto_pendiente) {
    //    alert("El valor ingresado no puede ser mayor al monto pendiente.");
    //    return
    //}
    
    monto_pendiente = monto_pendiente - monto_pagado;
    
    // Asegurarse de que el monto pendiente no sea negativo
    if (monto_pendiente < 0) {
        monto_pendiente = 0;  // O puedes ajustar este comportamiento según lo que necesites
    }
    
    //$("#monto_pendiente").val(monto_pendiente.toFixed(2));
}

function calcular() {
    let monto_credito = $("#monto_credito").val();
    let interes_credito = $("#interes_credito").val();
    let numero_coutas = $("#numero_coutas").val();
    let monto_couta = $("#monto_couta").val();
    let total_interes = $("#total_interes").val();
    let total_pagar = $("#total_pagar").val();
    total_interes = parseFloat(monto_credito) * parseFloat(interes_credito / 100);
    total_pagar = parseFloat(monto_credito) + parseFloat(total_interes);
    monto_couta = parseFloat(total_pagar) / parseFloat(numero_coutas);
    $("#total_pagar").val(total_pagar.toFixed(2));
    $("#monto_couta").val(monto_couta.toFixed(2));
    $("#total_interes").val(total_interes.toFixed(2));
}

function listarCoutas(id) {
    $.ajax({
        url: base_url + "pagos/getCreditoCoutas",
        method: "POST",
        data: {credito_id: id},
        async: true,
        dataType: 'json',
        success: function (data) {
            $("#idcuota").html(data);
        }
    });
}

function listarCoutasTabla(id) {
    $.ajax({
        url: base_url + "pagos/getCreditoCoutasTabla",
        method: "POST",
        data: {credito_id: id},
        async: true,
        dataType: 'json',
        success: function (data) {
            $("#tablaCuotas").dataTable().fnDestroy();
            $('#tablaCuotas').DataTable({
                "aaData": data,
                "deferRender": true,
                "processing": true,
                "bPaginate": false,
                "searching": false,
                "scrollY": '50vh',
                "scrollCollapse": true,
                "language": {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
                },
                "responsive": true,
                "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
            });
        }
    });
    $('#select-all').click(function (event) {
        if (this.checked) {
            $(':checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });
    $('input:checkbox').on('change', function () {
        var total = 0;
        $('input:checkbox:enabled:checked').each(function () {
            total += isNaN(Number($(this).attr('data-fee'))) ? 0 : Number($(this).attr('data-fee'));
        });
        $("#total_pagar").val(total.toFixed(2));
    });
    $('.tablaCuotas tbody').on('change', '.check', function () {
        var total = 0;
        $(':input:checkbox:checked').each(function () {
            //total += parseInt($(this).attr('data-fee'), 2);
            total += isNaN(parseFloat($(this).attr('data-fee'))) ? 0 : parseFloat($(this).attr('data-fee'));
            console.log(Math.ceil(total));
        });
        $("#total_pagar").val(redondearDecimales(total, 2));
    });
}


function redondearDecimales(numero, decimales) {
    numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}'); // Expresion regular para numeros con un cierto numero de decimales o mas
    if (numeroRegexp.test(numero)) { // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
        return Number(numero.toFixed(decimales));
    } else {
        return Number(numero.toFixed(decimales)) === 0 ? 0 : numero; // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
    }
}