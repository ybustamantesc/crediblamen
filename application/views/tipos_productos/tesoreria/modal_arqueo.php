<div class="modal" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Nuevo Arqueo</h5></div>
      <div class="modal-body">
        <form id="formArqueo">
          <div class="form-group">
            <label>Caja</label>
            <input type="text" name="caja_id" class="form-control">
          </div>
          <div class="form-group">
            <label>Hora Apertura</label>
            <input type="datetime-local" name="apertura" class="form-control">
          </div>
          <div class="form-group">
            <label>Observaciones</label>
            <input type="text" name="observaciones" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-info" id="guardarArqueo">Guardar</button>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  var btn = document.getElementById('guardarArqueo');
  if(!btn) return;
  btn.addEventListener('click', function(){
    var form = document.getElementById('formArqueo');
    var fd = new FormData(form);
    fetch('<?php echo base_url('tesoreria/save_arqueo'); ?>', { method: 'POST', body: fd })
      .then(r=>r.json()).then(function(json){
        alert(json.status==='success'?'Arqueo guardado':'Error');
        location.reload();
      });
  });
})();
</script>
