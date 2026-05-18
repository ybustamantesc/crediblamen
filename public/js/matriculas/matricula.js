$(document).ready(function() {

    /*Select2*/
    $('.select2').select2();

    /*Ao carregar a página, já inputa o valor vindo do banco*/
    $(".precio_valor_mensualidad").val($(".precios").val().split(' ')[1]);
    $(".diasVencimento").val($(".clientes").val().split(' ')[1]);
    /*Ao carregar a página, já inputa o valor vindo do banco no hidden inputs*/
    $(".matricula_cliente_id").val($(".clientes").val().split(' ')[0]);
    console.log($(".matricula_cliente_id").val());
    $(".matricula_precio_id").val($(".precios").val().split(' ')[0]);


    $("select.precios").change(function() {
        var categoria_selecionada = $(this).children("option:selected").val();
        $(".precio_valor_mensualidad").val(categoria_selecionada.split(' ')[1]);
        $(".matricula_precio_id").val(categoria_selecionada.split(' ')[0]);

    });

    $("select.clientes").change(function() {
        var clienteSeleccionado = $(this).children("option:selected").val();
        $(".diasVencimento").val(clienteSeleccionado.split(' ')[1]);
        $(".matricula_cliente_id").val(clienteSeleccionado.split(' ')[0]);
    });

});