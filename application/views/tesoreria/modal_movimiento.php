<?php /* Solo el modal y el botón de cheque, sin tabla ni filtros */ ?>
<div class="btn-group mb-2" role="group" aria-label="Tipos de movimiento">
    <button class="btn btn-outline-info" id="btnMovCheque"><i class="fas fa-money-check"></i> Cheque</button>
</div>
<!-- Modal para registrar movimiento (cheque) -->
<div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalMovimientoLabel"><i class="fas fa-money-check"></i> Registrar Cheque</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="formMovimiento">
                    <div id="chequeCustomLayout"><!-- Aquí se genera el layout dinámico por JS --></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarMovimiento">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
  var btn = document.getElementById('btnGuardarMovimiento');
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
