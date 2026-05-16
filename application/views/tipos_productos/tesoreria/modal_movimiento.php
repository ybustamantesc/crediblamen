<div class="modal" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Nuevo Movimiento</h5></div>
      <div class="modal-body">
        <form id="formMovimiento">
          <div class="form-group">
            <label>Tipo</label>
            <select name="tipo" class="form-control"><option>ingreso</option><option>egreso</option><option>transferencia</option></select>
          </div>
          <div class="form-group">
            <label>Monto</label>
            <input type="number" name="monto" class="form-control" step="0.01">
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="guardarMovimiento">Guardar</button>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  var btn = document.getElementById('guardarMovimiento');
  if(!btn) return;
  btn.addEventListener('click', function(){
    var form = document.getElementById('formMovimiento');
    var fd = new FormData(form);
    fetch('<?php echo base_url('tesoreria/save_movimiento'); ?>', { method: 'POST', body: fd })
      .then(r=>r.json()).then(function(json){
        alert(json.status==='success'?'Movimiento guardado':'Error');
        location.reload();
      });
  });
})();
</script>
