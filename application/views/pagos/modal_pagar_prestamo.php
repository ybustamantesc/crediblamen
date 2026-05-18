<?php defined('BASEPATH') OR exit('Acción no permitida'); ?>
<style>
    #modalPagarPrestamo .form-control[readonly],
    #modalPagarPrestamo .form-control:disabled,
    #modalPagarPrestamo select.form-control:disabled {
        background-color: #eef1f5;
        border-color: #d6dde7;
        color: #5f6b7a;
        cursor: not-allowed;
    }

    #modalPagarPrestamo .form-control[readonly]:focus,
    #modalPagarPrestamo .form-control:disabled:focus,
    #modalPagarPrestamo select.form-control:disabled:focus {
        box-shadow: none;
    }
</style>
<div class="modal fade" id="modalPagarPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-pago-provisional" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg,var(--logo-dark),var(--logo-teal)); color:#fff; border-bottom:none;">
                <h5 class="modal-title">Confirmar Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formPagarPrestamo">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="solicitud-field-label">Cliente</label>
                            <input type="text" id="modal_cliente" class="form-control" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Plan de Pagos</label>
                            <input type="text" id="modal_credito" class="form-control" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Cuota</label>
                            <input type="text" id="modal_cuota" class="form-control" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Monto Pendiente Cuota</label>
                            <input type="text" id="modal_pendiente_cuota" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Monto del Principal</label>
                            <input type="text" id="modal_principal" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Interés Corriente</label>
                            <input type="text" id="modal_interes_corriente" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Interés Moratorio</label>
                            <input type="text" id="modal_interes_moratorio" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Mora (Calculada)</label>
                            <input type="text" id="modal_mora_calculada" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Cuotas Atrasadas</label>
                            <input type="text" id="modal_cuotas_atrasadas" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">USD (Pago en Dólares)</label>
                            <input type="number" step="0.01" id="modal_monto_usd" class="form-control text-right" min="0" value="" />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">NIO (Pago en Córdobas)</label>
                            <input type="number" step="0.01" id="modal_monto_nio" class="form-control text-right" min="0" value="" />
                            <div class="small text-muted mt-1" id="modal_equivalente" style="display:none;"></div>
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Fecha de Pago</label>
                            <input type="date" id="modal_fecha_pago" name="fecha_pago" class="form-control" />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Método</label>
                            <select id="modal_metodo" name="metodo" class="form-control">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Moneda (Automática)</label>
                            <input type="text" id="modal_moneda_auto" class="form-control" readonly />
                            <small class="text-muted d-block mt-1" id="modalReglaTransferencia">Regla: Transferencia solo se registra en USD.</small>
                            <small class="text-muted d-block" id="modalTcInfo">TC Compra: - | TC Venta: -</small>
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Total a Pagar</label>
                            <input type="text" id="modal_total_pagar" class="form-control text-right" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Estado Cuota</label>
                            <input type="text" id="modal_estado_cuota" class="form-control" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Estado Cliente</label>
                            <input type="text" id="modal_estado_cliente" class="form-control" readonly />
                        </div>

                        <div class="col-md-6 col-sm-12 mb-3">
                            <label class="solicitud-field-label">Serie de Recibo</label>
                            <select id="modal_referencia" name="referencia" class="form-control" required>
                                <option value="">SELECCIONAR</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-sm-12 mb-0">
                            <label class="solicitud-field-label">Referencia</label>
                            <input type="text" id="modal_dato_adicional" name="dato_adicional" class="form-control" />
                        </div>

                        <input type="hidden" id="modal_monto" name="monto" value="0" />
                        <input type="hidden" id="modal_moneda" name="moneda" value="USD" />
                    </div>
                </form>

                <div id="prestamoPagoAlert" class="alert d-none mt-3" role="alert"></div>
            </div>

            <div class="modal-footer" style="border-top:none;">
                <button type="button" class="btn btn-regresar" data-dismiss="modal">Cerrar</button>
                <button type="button" id="submitPagarPrestamo" class="btn btn-pagar">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>
