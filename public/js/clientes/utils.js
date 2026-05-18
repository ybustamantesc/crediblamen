$(document).ready(function() {
    $('.select2').select2();
    $('#idcliente').change(function() {
        var id = $(this).val();
        $.ajax({
            url: base_url + "prestamos/getLimiteCredito",
            method: "POST",
            data: { cliente_id: id },
            async: true,
            dataType: 'json',
            success: function(data) {
                $("#limite_credito").val(data.limite_credito);
            }
        });
    });
});