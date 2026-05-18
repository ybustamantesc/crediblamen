$(document).ready(function() {
    $('.btnFormato1').on('click', function() {
        let idpago = $(this).attr("idpago");
        console.log(idpago);
        window.open(base_url + 'pagos/pdfformato1/' + idpago);
    });
    $('.btnFormato2').on('click', function() {
        let idpago = $(this).attr("idpago");
        console.log(idpago);
        window.open(base_url + 'pagos/pdfformato2/' + idpago);
    });
    $('.btnFormato3').on('click', function() {
        let idpago = $(this).attr("idpago");
        console.log(idpago);
        window.open(base_url + 'pagos/pdfformato3/' + idpago);
    });

    $('.btnFormato4').on('click', function() {
        let idpago = $(this).attr("idpago");
        console.log(idpago);
        window.open(base_url + 'pagos/pdfformato4/' + idpago);
    });
    $('.btnFormato5').on('click', function() {
        let idpago = $(this).attr("idpago");
        console.log(idpago);
        window.open(base_url + 'pagos/pdf/' + idpago);
    });
    $('.btnFormato6').on('click', function() {
        let idpago = $(this).attr("idpago");
        window.open(base_url + 'pagos/pdfformato6/' + idpago);
    });
});
