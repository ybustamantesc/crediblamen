<!-- Modal para registrar movimiento (cheque) - Copiado de tesoreria/movimientos.php -->
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
<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-money-check-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Desembolsos Programados</h5>
                                <span>Lista de desembolsos pendientes por ejecutar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Desembolsos Pendientes</h3>
                        </div>
                        <div class="card-body">
                            <form method="get" class="form-inline mb-3">
                                <label class="mr-2">Fecha desembolso desde:</label>
                                <input type="date" name="start_date" class="form-control form-control-sm mr-3">
                                <label class="mr-2">Hasta:</label>
                                <input type="date" name="end_date" class="form-control form-control-sm mr-3">
                                <label class="mr-2">Buscar:</label>
                                <input type="text" id="desembolsos_search" name="q" class="form-control form-control-sm mr-3" placeholder="Código plan o cliente">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>
                                <a href="<?php echo site_url('desembolsos'); ?>" class="btn btn-sm btn-secondary">Limpiar</a>
                            </form>
                            <div class="table-responsive-sm">
                                <style>
                                  /* Compact table style aligned with solicitudes */
                                  #tabla_desembolsos td,
                                  #tabla_desembolsos th {
                                    padding: .25rem .5rem;
                                    vertical-align: middle !important;
                                    text-align: center;
                                    font-size: .85rem;
                                    line-height: 1.1;
                                    white-space: nowrap;
                                  }
                                  #tabla_desembolsos thead th {
                                    background: #f8f9fa;
                                    font-size: .82rem;
                                    padding: .3rem .5rem;
                                  }
                                  #tabla_desembolsos td.monto {
                                    text-align: right;
                                    font-weight: 600;
                                  }
                                  #tabla_desembolsos th:nth-child(2),
                                  #tabla_desembolsos td:nth-child(2) {
                                    max-width: 190px;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                  }
                                  #tabla_desembolsos th:nth-child(7),
                                  #tabla_desembolsos td:nth-child(7) {
                                    width: 145px;
                                    white-space: nowrap;
                                  }
                                  #tabla_desembolsos .btn {
                                    padding: .25rem .5rem;
                                    font-size: .9rem;
                                    min-width: 100px;
                                  }
                                  .badge-pendiente,
                                  .badge-pendiente-aprobacion,
                                  .badge-desembolsado,
                                  .badge-anulado {
                                    color: #fff;
                                    font-size: .78rem;
                                    font-weight: 600;
                                    padding: .25rem .5rem;
                                    border-radius: 999px;
                                    display: inline-block;
                                  }
                                  .badge-pendiente {
                                    background: #ff9800;
                                  }
                                  .badge-pendiente-aprobacion {
                                    background: #f57c00;
                                  }
                                  .badge-desembolsado {
                                    background: #4caf50;
                                  }
                                  .badge-anulado {
                                    background: #6c757d;
                                  }
                                  .row-anulado td {
                                    background: #e2e3e5 !important;
                                    color: #383d41;
                                  }
                                  .btn-desembolsar {
                                    min-width: 110px;
                                  }

                                  @media (max-width: 767.98px) {
                                    #tabla_desembolsos td,
                                    #tabla_desembolsos th {
                                      white-space: normal !important;
                                      font-size: .82rem;
                                    }
                                    #tabla_desembolsos th:nth-child(2),
                                    #tabla_desembolsos td:nth-child(2) {
                                      max-width: none !important;
                                      overflow: visible !important;
                                    }
                                    #tabla_desembolsos th:nth-child(7),
                                    #tabla_desembolsos td:nth-child(7) {
                                      width: auto !important;
                                    }
                                    #tabla_desembolsos .btn {
                                      width: 100%;
                                      min-width: 0;
                                      margin-bottom: .25rem;
                                    }
                                  }
                                </style>
                                <table class="table table-sm table-striped table-bordered table-hover table-compact" id="tabla_desembolsos">
                                  <thead>
                                    <tr>
                                      <th style="min-width:60px;"># Plan</th>
                                      <th style="min-width:180px;">Cliente</th>
                                      <th style="min-width:90px;">Monto</th>
                                      <th style="min-width:120px;">Fecha desembolso</th>
                                      <th style="min-width:120px;">Fecha primer pago</th>
                                      <th style="min-width:100px;">Estado</th>
                                      <th style="min-width:120px;">Acciones</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      <!-- AJAX: contenido -->
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para ejecutar desembolso -->
<div class="modal fade" id="modalDesembolso" tabindex="-1" role="dialog" aria-labelledby="modalDesembolsoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDesembolsoLabel">Ejecutar Desembolso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formDesembolso">
          <input type="hidden" name="idprestamo" id="modal_idprestamo">
          <div class="form-group">
            <label>Monto del crédito</label>
            <input type="number" class="form-control" name="monto_credito" id="modal_monto_credito" step="0.01" readonly>
          </div>
          <div class="form-group">
            <label>Fecha de desembolso</label>
            <input type="date" class="form-control" name="fecha_desembolso" id="modal_fecha_desembolso" required>
          </div>
          <div class="form-group">
            <label>Fecha primer pago</label>
            <input type="date" class="form-control" name="primer_dia_pago" id="modal_primer_dia_pago" required>
          </div>
          <hr>
          <h6 class="text-muted">Gastos a descontar del monto</h6>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label>Costos Legales</label>
              <div class="input-group">
                <input type="number" class="form-control costos-input" name="costos_legales" id="modal_costos_legales" step="0.01" min="0" value="0" readonly>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-primary btn-sm btn-editar-costo" data-field="costos_legales" id="btn_editar_costos_legales">Editar</button>
                  <button type="button" class="btn btn-primary btn-sm btn-guardar-costo" data-field="costos_legales" id="btn_guardar_costos_legales" style="display:none;">Guardar</button>
                </div>
              </div>
              <input type="hidden" name="confirmado_costos_legales" id="confirmado_costos_legales" value="0">
              <div class="mt-1" id="grupo_comentario_costos_legales" style="display:none;">
                <textarea class="form-control form-control-sm" name="comentario_costos_legales" id="comentario_costos_legales" rows="2" placeholder="Comentario obligatorio para Costos Legales"></textarea>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label>Seguros</label>
              <div class="input-group">
                <input type="number" class="form-control costos-input" name="seguros" id="modal_seguros" step="0.01" min="0" value="0" readonly>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-primary btn-sm btn-editar-costo" data-field="seguros" id="btn_editar_seguros">Editar</button>
                  <button type="button" class="btn btn-primary btn-sm btn-guardar-costo" data-field="seguros" id="btn_guardar_seguros" style="display:none;">Guardar</button>
                </div>
              </div>
              <input type="hidden" name="confirmado_seguros" id="confirmado_seguros" value="0">
              <div class="mt-1" id="grupo_comentario_seguros" style="display:none;">
                <textarea class="form-control form-control-sm" name="comentario_seguros" id="comentario_seguros" rows="2" placeholder="Comentario obligatorio para Seguros"></textarea>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label>Comisiones</label>
              <div class="input-group">
                <input type="number" class="form-control costos-input" name="comisiones" id="modal_comisiones" step="0.01" min="0" value="0" readonly>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-primary btn-sm btn-editar-costo" data-field="comisiones" id="btn_editar_comisiones">Editar</button>
                  <button type="button" class="btn btn-primary btn-sm btn-guardar-costo" data-field="comisiones" id="btn_guardar_comisiones" style="display:none;">Guardar</button>
                </div>
              </div>
              <input type="hidden" name="confirmado_comisiones" id="confirmado_comisiones" value="0">
              <div class="mt-1" id="grupo_comentario_comisiones" style="display:none;">
                <textarea class="form-control form-control-sm" name="comentario_comisiones" id="comentario_comisiones" rows="2" placeholder="Comentario obligatorio para Comisiones"></textarea>
              </div>
            </div>
          </div>
          <hr>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="text-muted mb-0">Renovación</h6>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleRenovacionSeccion">Mostrar renovación</button>
          </div>
          <div id="renovacion_seccion" style="display:none;">
            <div class="form-group">
              <label>Saldo de renovación (deuda actual del cliente)</label>
              <input type="number" class="form-control" name="saldo_renovacion" id="modal_saldo_renovacion" step="0.01" value="0" readonly>
            </div>
            <div class="form-group">
              <label>Monto Renovación</label>
              <div class="input-group">
                <input type="number" class="form-control renov-total-input" name="monto_renovacion" id="modal_monto_renovacion" step="0.01" min="0" value="0">
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleRenovacionDetalle">Mostrar desglose</button>
                </div>
              </div>
            </div>
            <div id="renovacion_detalle_group" style="display:none;">
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label>Principal</label>
                  <input type="number" class="form-control renov-input" name="renov_principal" id="modal_renov_principal" step="0.01" min="0" value="0">
                </div>
                <div class="form-group col-md-4">
                  <label>Interés Corriente</label>
                  <input type="number" class="form-control renov-input" name="renov_interes_corriente" id="modal_renov_interes_corriente" step="0.01" min="0" value="0">
                </div>
                <div class="form-group col-md-4">
                  <label>Interés en Mora</label>
                  <input type="number" class="form-control renov-input" name="renov_interes_mora" id="modal_renov_interes_mora" step="0.01" min="0" value="0">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Comentario global de renovación</label>
              <textarea class="form-control" name="comentario_renovacion" id="modal_comentario_renovacion" rows="2" placeholder="Comentario opcional para la renovación"></textarea>
            </div>
          </div>
          <div class="form-group" style="background:#f0f0f0; padding:10px; border-radius:4px;">
            <label><strong>Total a desembolsar</strong></label>
            <div style="font-size:18px; font-weight:bold; color:#0066cc;">
              $ <span id="total_a_desembolsar">0.00</span>
            </div>
          </div>
          <div class="form-group">
            <label>Seleccionar Cuenta Bancaria <span class="text-danger">*</span></label>
            <select class="form-control" name="cuenta_bancaria" id="modal_cuenta_bancaria" required>
              <option value="">-- Seleccione cuenta --</option>
            </select>
          </div>
          <div class="form-group">
            <label>Observaciones</label>
            <textarea class="form-control" name="observaciones" id="modal_observaciones"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnEjecutarDesembolso"><i class="fas fa-check"></i> Solicitud de Desembolso</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal vista previa desembolso -->
<div class="modal fade" id="modalPreviewDesembolso" tabindex="-1" role="dialog" aria-labelledby="modalPreviewDesembolsoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalPreviewDesembolsoLabel">Vista previa del desembolso</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="small text-muted">Cliente</div>
          <div id="preview_cliente" class="font-weight-bold"></div>
        </div>
        <div class="row">
          <div class="col-6 mb-2">
            <div class="small text-muted">No. plan</div>
            <div id="preview_plan"></div>
          </div>
          <div class="col-6 mb-2">
            <div class="small text-muted">Monto crédito</div>
            <div id="preview_monto_credito"></div>
          </div>
          <div class="col-6 mb-2">
            <div class="small text-muted">Día ejecutado</div>
            <div id="preview_fecha"></div>
          </div>
          <div class="col-6 mb-2">
            <div class="small text-muted">Plazo</div>
            <div id="preview_plazo"></div>
          </div>
          <div class="col-6 mb-2">
            <div class="small text-muted">Tasa</div>
            <div id="preview_tasa"></div>
          </div>
          <div class="col-4 mb-2">
            <div class="small text-muted">Costos legales</div>
            <div id="preview_costos_legales"></div>
          </div>
          <div class="col-4 mb-2">
            <div class="small text-muted">Seguros</div>
            <div id="preview_seguros"></div>
          </div>
          <div class="col-4 mb-2">
            <div class="small text-muted">Comisiones</div>
            <div id="preview_comisiones"></div>
          </div>
        </div>
        <div class="mt-2">
          <div class="small text-muted">Ejecutado por</div>
          <div id="preview_usuario"></div>
        </div>
        <div class="mt-3">
          <div class="small text-muted">Observaciones / distribución</div>
          <div id="preview_obs" style="white-space:pre-wrap; background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:10px;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('tesoreria/modal_movimiento'); ?>
<?php $this->load->view('layout/footer'); ?>
<script>
window.cuentasBanco = <?php echo json_encode(array_values(array_filter($cuentas, function($c){return $c->type==='banco' && $c->estado==1;}))); ?>;
</script>
<script src="<?php echo base_url('public/js/desembolsos_mejorado.js'); ?>"></script>
<script>
// JS del modal de tesorería para guardar cheque
$('#btnGuardarMovimiento').on('click', function(){
    var $form = $('#formMovimiento');
    var isCheque = $('#chequeCustomLayout').is(':visible');
    var payload = {};
    if(isCheque) {
        payload.tipo_movimiento = 'cheque';
        payload.concepto = $('[name="concepto_cheque"]').val();
        payload.forma_pago = 'CHEQUE';
        payload.fecha_registro = $('[name="fecha_registro"]:visible').val();
        payload.fecha_aplicacion = $('[name="fecha_aplicacion"]:visible').val();
        payload.beneficiario = $('[name="cheque_a"]').val();
        payload.referencia1 = $('[name="referencia1"]:visible').val();
        payload.referencia2 = $('[name="referencia2"]:visible').val();
        payload.monto_total = parseFloat($('[name="monto_total"]:visible').val() || 0);
        payload.iva_total = parseFloat($('[name="iva_total"]:visible').val() || 0);
        payload.departamento = null;
        payload.centro_costos = null;
        payload.proyecto = null;
        payload.descripcion = $('[name="descripcion_cheque"]').val();
        payload.cuenta_id = $('[name="cuenta_id"]:visible').val();
        payload.tipo_transferencia = 'cargo';
    } else {
        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }
        payload = $form.serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        payload.monto_total = parseFloat(payload.monto_total || 0);
        payload.iva_total = parseFloat(payload.iva_total || 0);
        payload.departamento = null;
        payload.centro_costos = null;
        payload.proyecto = null;
        payload.tipo_movimiento = 'transferencia';
        payload.tipo_transferencia = $('[name="tipo_transferencia"]').val();
    }
    if(isCheque && (!payload.cuenta_id || payload.cuenta_id === '')){
        alert('Seleccione la cuenta bancaria de origen.');
        $('[name="cuenta_id"]:visible').focus();
        return;
    }
    if(isCheque && (!payload.monto_total || payload.monto_total <= 0)){
        alert('Ingrese el monto total.');
        $('[name="monto_total"]:visible').focus();
        return;
    }
    $.post('/Conta/tesoreria/save_movimiento_ajax', payload)
        .done(function(resp){
            try{ var j = (typeof resp === 'object')? resp : JSON.parse(resp); }catch(e){ j = null; }
            if(j && j.status){
                $('#modalMovimiento').modal('hide');
                alert('Movimiento guardado correctamente.');
            }else{
                alert((j && j.message)? j.message : 'Error al guardar movimiento.');
            }
        })
        .fail(function(){
            alert('Error en la petición AJAX.');
        });
});
</script>
