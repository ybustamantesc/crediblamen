$(document).ready(function() {
    $("#numero_coutas").trigger("touchspin.updatesettings", { max: 1000 });
    $('#fecha_credito').datetimepicker({
        defaultDate: new Date(),
        format: 'DD/MM/YYYY'
    });
    $('.select2').select2({
        width: "100%",
    });
    $("input[name='interes_credito']").TouchSpin({
        min: 0.00,
        //stepinterval: 1,
        max: 100,
        step: 1,
        decimals: 2
    }).on('touchspin.on.startspin', function() {
        calcular()
    });
    $("input[name='numero_coutas']").TouchSpin({
        min: 1,
        //stepinterval: 1,
        max: 100,
        step: 1
    }).on('touchspin.on.startspin', function() {
        calcular()
    });
    $(document).on("focusout", "#interes_credito", function() {
        calcular();
    })
    $('#numero_coutas').change(function() {
        //calcular();
    });
    $('#numero_coutas').change(function() {
        calcular();
    });
    $('#monto_credito').change(function() {
        //calcular();
    });
    $('#interes_credito').change(function() {
        //calcular();
    });

});

function calcular() {
    // let monto_credito = $("#monto_credito").val();
    // let interes_credito = $("#interes_credito").val();
    // let numero_coutas = $("#numero_coutas").val();
    // let monto_couta = $("#monto_couta").val();
    // let total_interes = $("#total_interes").val();
    // let total_pagar = $("#total_pagar").val();
    // total_interes = parseFloat(monto_credito) * parseFloat(interes_credito / 100);
    // total_pagar = parseFloat(monto_credito) + parseFloat(total_interes);
    // monto_couta = parseFloat(total_pagar) / parseFloat(numero_coutas);
    // $("#total_pagar").val(total_pagar.toFixed(2));
    // $("#monto_couta").val(monto_couta.toFixed(2));
    // $("#total_interes").val(total_interes.toFixed(2));

    let monto_credito = $("#monto_credito").val();
    let interes = $("#interes_credito").val();
    let numero_cuotas = $("#numero_coutas").val();
    let monto_cuota = 0;
    let monto_interes = 0;
    let total_interes = 0;
    let total_pagar = 0;
    let monto_capital = 0;
    let valor_interes = interes / 100;
    monto_capital = (monto_credito / numero_cuotas);
    $("#monto_capital").val(monto_capital.toFixed(2));

    monto_interes = (monto_capital * valor_interes);
    $("#monto_interes").val(monto_interes.toFixed(2));

    monto_cuota = parseFloat(monto_capital) + parseFloat(monto_interes);
    $("#monto_couta").val(monto_cuota.toFixed(2));

    total_interes = parseFloat(monto_interes) * parseFloat(numero_cuotas);
    $("#total_interes").val(total_interes.toFixed(2));

    total_pagar = parseFloat(monto_cuota) * parseFloat(numero_cuotas);
    $("#total_pagar").val(total_pagar.toFixed(2));
}