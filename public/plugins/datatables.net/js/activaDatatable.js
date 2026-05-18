$(document).ready(function() {
    var table = $('.data-table').DataTable({
        "bSort": false,
        "responsive": true,
        "autoWidth": false,
        "lengthMenu": [25, 50, 75, 100]
    });

    $('.alert').fadeIn();
    setTimeout(function() {
        $(".alert").fadeOut();
    }, 2000);
});