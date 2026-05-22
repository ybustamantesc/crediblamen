$(document).ready(function() {
    var table = $('.data-table').DataTable({
        "bSort": false,
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    $('.alert').fadeIn();
    setTimeout(function() {
        $(".alert").fadeOut();
    }, 2000);
});