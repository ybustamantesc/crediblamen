$(document).ready(function() {
    $('#tblcuotas').DataTable();
    $('#fecha_credito').datetimepicker({
        defaultDate: new Date(),
        format: 'DD/MM/YYYY'
    });
    $('.select2').select2();

    $('#numero_cuotas').change(function() {
        calcular();
    });
    $('#monto_credito').change(function() {
        //calcular();
    });
    $('#interes_credito').change(function() {
        //calcular();
    });

    $("#btnSimular").click(function() {
        let fecha_credito = $("#fecha_credito").val();
        let forma_pago = $("#forma_pago").val();
        let numero_cuotas = $("#numero_cuotas").val();
        let monto_cuota = $("#monto_cuota").val();
        let monto_capital = $("#monto_capital").val();
        let monto_interes = $("#monto_interes").val();
        CargarCuotas(fecha_credito, forma_pago, numero_cuotas, monto_cuota, monto_capital, monto_interes);
    });

});

function CargarCuotas(fecha_credito, forma_pago, numero_cuotas, monto_cuota, monto_capital, monto_interes) {
    $('#tblcuotas').DataTable({
        ajax: {
            url: base_url + "simulador/detalleSimulador",
            data: { fecha_credito: fecha_credito, forma_pago: forma_pago, numero_cuotas: numero_cuotas, monto_cuota: monto_cuota, monto_capital: monto_capital, monto_interes: monto_interes },
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function(e) {
                console.log(e);
            }
        },
        columns: [{
                data: "numero_cuotas"
            },
            {
                data: "fecha_cuota"
            },
            {
                data: "monto_capital"
            },
            {
                data: "monto_interes"
            },
            {
                data: "monto_cuota"
            }
        ],
        "bSort": false,
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
        "lengthMenu": [50]
    });
}

function calcular() {
    let monto_credito = $("#monto_credito").val();
    let interes = $("#interes_credito").val();
    let numero_cuotas = $("#numero_cuotas").val();
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
    $("#monto_cuota").val(monto_cuota.toFixed(2));

    total_interes = parseFloat(monto_interes) * parseFloat(numero_cuotas);
    $("#total_interes").val(total_interes.toFixed(2));

    total_pagar = parseFloat(monto_cuota) * parseFloat(numero_cuotas);
    $("#total_pagar").val(total_pagar.toFixed(2));
}