<?php defined('BASEPATH') OR exit('Acción no permitida'); ?>
<div class="container-fluid pagos-page">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 page-title">Registrar Pago de Planes</h4>
                    <small class="subtitle">Registre pagos de cuotas por cliente y crédito</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo base_url('tesoreria'); ?>" class="btn btn-regresar btn-sm">Regresar</a>
                </div>
            </div>
        </div>

        <!-- Solo el formulario, sin sidebar de créditos -->
        <div class="col-md-8 offset-md-2">
            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <form id="formPrestamoPago" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-3">
                                <label for="idcliente" class="solicitud-field-label">Cliente</label>
                                <select id="idcliente" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-3">
                                <label for="idcredito" class="solicitud-field-label">Nro Crédito</label>
                                <select id="idcredito" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                        </div>
                        <!-- Campos de saldo ocultos por solicitud -->
                        <div class="row" style="display:none;">
                            <div class="col-sm-6 col-md-6 mb-3">
                                <label for="saldo_cancelar" class="form-label">Saldo para Cancelar Préstamos</label>
                                <input type="text" id="saldo_cancelar" class="form-control text-end font-weight-bold bg-light saldo-campo" readonly style="font-size:1.2em; color:#007bff;" />
                            </div>
                            <div class="col-sm-6 col-md-6 mb-3">
                                <label for="saldo_aldia" class="form-label">Saldo para ponerlo al día</label>
                                <input type="text" id="saldo_aldia" class="form-control text-end font-weight-bold bg-light saldo-campo" readonly style="font-size:1.2em; color:#28a745;" />
                            </div>
                            <style>
                            .saldo-campo { background: #f8f9fa !important; border: 2px solid #e3e6f0; }
                            .saldo-campo[readonly] { box-shadow: none; }
                            </style>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-3">
                                <label for="idcuota" class="form-label">Cuota</label>
                                <select id="idcuota" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <label for="monto_couta" class="form-label">Monto Cuota</label>
                                <input type="text" id="monto_couta" class="form-control text-end" readonly />
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <label for="monto_pendiente" class="form-label">Monto Pendiente</label>
                                <input type="text" id="monto_pendiente" class="form-control text-end" readonly />
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" id="btnPagarPrestamo" class="btn btn-pagar" data-toggle="modal" data-target="#modalPagarPrestamo">Pagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('pagos/modal_pagar_prestamo'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/css/pagos_theme.css'); ?>">
<script src="<?php echo base_url('public/js/pagos/prestamos.js'); ?>"></script>
