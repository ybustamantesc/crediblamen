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
        if(!form.checkValidity()){ form.reportValidity(); return; }
        // Usar el endpoint AJAX estándar y serializar con jQuery para compatibilidad
        var payload = $(form).serialize();
        $.post('<?php echo site_url('tesoreria/save_movimiento_ajax'); ?>', payload)
            .done(function(resp){
                try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ j = null; }
                if(j && j.status){
                    $('#modalMovimiento').modal('hide');
                    alert('Movimiento guardado correctamente.');
                    if(typeof cargarMovimientos === 'function') cargarMovimientos();
                    else location.reload();
                }else{
                    alert((j && j.message)? j.message : 'Error al guardar movimiento.');
                }
            })
            .fail(function(xhr){
                var msg = 'Error en la petición AJAX.';
                try{ if(xhr && xhr.responseText) msg = xhr.responseText; }catch(e){}
                alert(msg);
            });
    });
})();
</script>
