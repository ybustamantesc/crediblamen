<div class="mb-3 d-flex justify-content-end">
    <a href="<?php echo site_url('menu'); ?>" class="btn btn-outline-primary btn-sm mr-2"><i class="fas fa-home"></i> Regresar al menú</a>
    <button id="contabBackBtn" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Regresar</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('contabBackBtn');
    if (!btn) return;
    btn.addEventListener('click', function(e){
        e.preventDefault();
        var fallback = '<?php echo base_url('contabilidad'); ?>';
        // Attempt history.back(); if it doesn't navigate within 300ms, redirect to fallback
        var current = location.href;
        try { history.back(); } catch(err) { location.href = fallback; return; }
        setTimeout(function(){
            if (location.href === current) {
                location.href = fallback;
            }
        }, 300);
    });
});
</script>
