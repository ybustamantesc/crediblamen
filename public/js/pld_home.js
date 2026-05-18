document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('btnNewKyc');
    if (!btn) return;
    btn.addEventListener('click', function(e){
        e.preventDefault();
        fetch(base_url + 'pld/modal_kyc', {credentials: 'same-origin'})
        .then(r => r.text())
        .then(html => {
            var cont = document.getElementById('modalContainer');
            if (!cont) return;
            cont.innerHTML = html;
            if (window.jQuery && window.jQuery('#pldModal').length) {
                window.jQuery('#pldModal').modal('show');
            }
        });
    });
});
