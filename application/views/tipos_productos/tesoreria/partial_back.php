<div class="mb-3">
    <button class="btn btn-light" id="btnBack">← Regresar</button>
</div>
<script>
(function(){
  var btn = document.getElementById('btnBack');
  if(!btn) return;
  btn.addEventListener('click', function(){
    var done = false;
    try { history.back(); done = true; } catch(e){}
    setTimeout(function(){ if(!done){ window.location.href = '<?php echo base_url('tesoreria'); ?>'; } }, 300);
  });
})();
</script>
