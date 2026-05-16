$(document).ready(function() {
    $('.select2').select2();
    $('#cliente').change(function() {
        var id = $(this).val();      
        listarCreditosTabla(id);
    });
});
function listarCreditosTabla(id) {
    $.ajax({
        url: base_url + "pagos/getCreditosClienteTabla",
        method: "POST",
        data: { cliente: id },
        async: true,
        dataType: 'json',
        success: function(data) {
            $("#tablaCreditos").dataTable().fnDestroy();
            $('#tablaCreditos').DataTable({
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
    $('#select-all-creditos').click(function(event) {
        if (this.checked) {
            $(':checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
    });
    $('input:checkbox').on('change', function() {
        var total = 0;
        $('input:checkbox:enabled:checked').each(function() {
            total += isNaN(Number($(this).attr('data-fee'))) ? 0 : Number($(this).attr('data-fee'));
        });

        $("#total_pagar_creditos").val(total.toFixed(2));
    });
    $('.tablaCreditos tbody').on('change', '.creditos_check', function() {
        var total = 0;
        $(':input:checkbox:checked').each(function() {            
            total += isNaN(parseFloat($(this).attr('data-fee'))) ? 0 : parseFloat($(this).attr('data-fee'));
            
        });
        $("#total_pagar_creditos").val(redondearDecimales(total, 2));
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