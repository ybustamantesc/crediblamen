$(document).ready(function() {
    $.ajax({
        url: base_url + "caja/validarCaja",
        method: "POST",
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mensaje',
                    text: 'Debes realizar la apertura de caja'
                });
                setTimeout(function() {
                    window.location.href = "caja";
                }, 1200);
            }
        }
    });
});