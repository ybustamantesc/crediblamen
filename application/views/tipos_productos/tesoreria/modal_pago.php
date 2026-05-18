<div class="modal" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Programar Pago</h5></div>
      <div class="modal-body">
        <form id="formPago">
          <div class="form-group">
            <label>Proveedor</label>
            <input type="text" name="proveedor_id" class="form-control">
          </div>
          <div class="form-group">
            <label>Monto</label>
            <input type="number" name="monto" class="form-control" step="0.01">
          </div>
          <div class="form-group">
            <label>Fecha programada</label>
            <input type="date" name="fecha_programada" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" id="guardarPago">Guardar</button>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  var btn = document.getElementById('guardarPago');
  if(!btn) return;
  btn.addEventListener('click', function(){
    var form = document.getElementById('formPago');
    var fd = new FormData(form);
    fetch('<?php echo base_url('tesoreria/save_pago'); ?>', { method: 'POST', body: fd })
      .then(r=>r.json()).then(function(json){
        alert(json.status==='success'?'Pago programado':'Error');
        location.reload();
      });
  });
})();
</script>
