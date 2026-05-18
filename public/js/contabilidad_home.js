document.addEventListener('DOMContentLoaded', function(){
    function openModalFrom(url){
        fetch(url, {credentials: 'same-origin'})
        .then(r => r.text())
        .then(html => {
            const container = document.getElementById('modalContainer');
            if (!container) return;
            container.innerHTML = html;
            // if jQuery/Bootstrap available, show modal by id if present
            if (window.jQuery) {
                const $modal = window.jQuery('#contabModal');
                if ($modal && $modal.length) $modal.modal('show');
            }
        })
        .catch(err => console.error('Error loading modal:', err));
    }

    const btn = document.getElementById('btnNewEntryHome');
    if (btn) {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            openModalFrom(base_url + 'contabilidad/modal_add');
        });
    }
});
