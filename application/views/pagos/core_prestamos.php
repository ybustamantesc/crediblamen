<?php defined('BASEPATH') OR exit('Acción no permitida'); ?>
<div class="container-fluid pagos-page">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="page-header mb-2">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-money-check-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5 class="mb-0 page-title">Pago Provisional de Préstamos</h5>
                                <span class="subtitle">Busque cliente, seleccione préstamo y registre el pago.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                        <a href="<?php echo base_url('pagos?date_from=&date_to=&q=&idserie='); ?>" class="btn btn-regresar btn-sm">Regresar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="pg-search-hint mb-2">
                <i class="fas fa-search"></i>
                <span>Tip: escriba nombre, apellido o documento para encontrar rápido al cliente.</span>
            </div>
            <div class="pg-summary-panel mb-3">
                <div class="pg-summary-title">Resumen del crédito seleccionado</div>
                <div class="row">
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Cliente Seleccionado</div>
                            <div class="value" id="pg_kpi_cliente">-</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Préstamo</div>
                            <div class="value" id="pg_kpi_prestamo">-</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Cuota</div>
                            <div class="value" id="pg_kpi_cuota">-</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Monto Pendiente</div>
                            <div class="value" id="pg_kpi_pendiente">$0.00</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Estado Cliente</div>
                            <div class="value" id="pg_kpi_estado_cliente">-</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="pg-kpi">
                            <div class="label">Estado Cuota</div>
                            <div class="value" id="pg_kpi_estado_cuota">-</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0" style="display: none;">
                        <div class="pg-kpi pg-kpi-soft-warning">
                            <div class="label">Días Mora</div>
                            <div class="value" id="pg_kpi_dias_mora">0</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0" style="display: none;">
                        <div class="pg-kpi pg-kpi-soft-danger">
                            <div class="label">Monto Mora</div>
                            <div class="value" id="pg_kpi_mora">$0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card chart-card shadow-sm">
                <div class="card-body">
                    <form id="formPrestamoPago" class="needs-validation" novalidate>
                        <div class="pg-toolbar mb-2">
                            <div class="row align-items-end">
                            <div class="col-sm-12 col-lg-6 mb-3">
                                <label for="idcliente" class="solicitud-field-label">Cliente</label>
                                <select id="idcliente" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-lg-6 mb-3">
                                <label for="idcredito" class="solicitud-field-label">Nro Crédito</label>
                                <select id="idcredito" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-lg-6 mb-3">
                                <label for="idcuota" class="form-label">Cuota</label>
                                <select id="idcuota" class="form-control select2" required>
                                    <option value="">SELECCIONAR</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <label for="monto_couta" class="form-label">Monto Cuota</label>
                                <input type="text" id="monto_couta" class="form-control text-end" readonly />
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <label for="monto_pendiente" class="form-label">Monto Pendiente</label>
                                <input type="text" id="monto_pendiente" class="form-control text-end" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-lg-3 mb-3" style="display: none;">
                                <label for="monto_dias_mora" class="form-label">Días Mora</label>
                                <input type="text" id="monto_dias_mora" class="form-control text-end" readonly />
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3" style="display: none;">
                                <label for="monto_mora" class="form-label">Monto Mora</label>
                                <input type="text" id="monto_mora" class="form-control text-end" readonly />
                            </div>
                            <div class="col-lg-6 mb-3 d-none d-lg-block"></div>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="button" id="btnPagarPrestamo" class="btn btn-pagar" data-toggle="modal" data-target="#modalPagarPrestamo">Continuar Pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('pagos/modal_pagar_prestamo'); ?>

<link rel="stylesheet" href="<?php echo base_url('public/css/pagos_theme.css'); ?>">
