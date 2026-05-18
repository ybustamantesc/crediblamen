<div class="mb-3 d-flex justify-content-end">
    <button id="pldBackBtn" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Regresar</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('pldBackBtn');
    if (!btn) return;
    btn.addEventListener('click', function(e){
        e.preventDefault();
        var fallback = '<?php echo base_url('pld'); ?>';
        var current = location.href;
        try { history.back(); } catch(err) { location.href = fallback; return; }
        setTimeout(function(){ if (location.href === current) location.href = fallback; }, 300);
    });
});
</script>
