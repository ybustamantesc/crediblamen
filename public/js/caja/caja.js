$(document).ready(function () {
    $('.btnCorte').on('click', function () {
        let cajaId = $(this).attr('idcaja');
        if (cajaId == '') {
            alert('Seleccionar una Caja');
            return false;
        } else {
            window.open(base_url + 'caja/pdfcortefecha/' + cajaId);
        }

    });
});