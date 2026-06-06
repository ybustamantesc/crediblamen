<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-balance-scale bg-blue"></i>
                            <div class="d-inline">
                                <h5>Estado de Situación Financiera</h5>
                                <span>Balance General</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->load->view('contabilidad/partial_back'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="situacionTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="mensual-tab" data-toggle="tab" href="#mensual" role="tab">
                                        <i class="ik ik-calendar"></i> Mensual
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="anual-tab" data-toggle="tab" href="#anual" role="tab">
                                        <i class="ik ik-calendar"></i> Anual Consolidado
                                    </a>
                                </li>
                            </ul>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-inline justify-content-end">
                                        <label class="mr-2">Moneda:</label>
                                        <select id="currency_select" class="form-control form-control-sm mr-2" style="min-width:160px;">
                                            <option value="NIO" selected>Córdobas</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content" id="situacionTabContent">
                                <!-- MENSUAL TAB -->
                                <div class="tab-pane fade show active" id="mensual" role="tabpanel">
                                    <div class="row mt-3 mb-3">
                                        <div class="col-md-8">
                                            <p class="text-muted">Reporte de situación financiera al cierre de un período específico.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <label class="mr-2 mb-2">Mes:</label>
                                                <input id="mes_select" type="month" class="form-control form-control-sm mr-2 mb-2" style="min-width:170px;" value="<?php echo date('Y-m'); ?>">
                                                <button id="btn_refresh_mensual" class="btn btn-primary btn-sm mr-1 mb-2">Actualizar</button>
                                                <button id="btn_export_mensual" class="btn btn-success btn-sm mr-1 mb-2">Excel</button>
                                                <button id="btn_pdf_mensual" class="btn btn-secondary btn-sm mb-2">PDF</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="mensual_content" class="table-responsive">
                                        <div class="text-center text-muted py-5">
                                            Seleccione un mes y presione "Actualizar"
                                        </div>
                                    </div>
                                </div>

                                <!-- ANUAL TAB -->
                                <div class="tab-pane fade" id="anual" role="tabpanel">
                                    <div class="row mt-3 mb-3">
                                        <div class="col-md-8">
                                            <p class="text-muted">Reporte consolidado de todos los meses del año seleccionado.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <label class="mr-2 mb-2">Año:</label>
                                                <select id="anio_select" class="form-control form-control-sm mr-2 mb-2" style="min-width:120px;">
                                                    <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                                                        <option value="<?php echo $y; ?>" <?php echo $y == date('Y') ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <button id="btn_refresh_anual" class="btn btn-primary btn-sm mr-1 mb-2">Actualizar</button>
                                                <button id="btn_export_anual" class="btn btn-success btn-sm mr-1 mb-2">Excel</button>
                                                <button id="btn_pdf_anual" class="btn btn-secondary btn-sm mb-2">PDF</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="anual_content" class="table-responsive">
                                        <div class="text-center text-muted py-5">
                                            Seleccione un año y presione "Actualizar"
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="empresa_razon_social" value="<?php echo htmlspecialchars($empresa ? $empresa->razon_social : ''); ?>" />
                            <input type="hidden" id="empresa_direccion" value="<?php echo htmlspecialchars($empresa ? $empresa->direccion : ''); ?>" />
                            <input type="hidden" id="empresa_telefonos" value="<?php echo htmlspecialchars($empresa ? $empresa->telefonos : ''); ?>" />
                            <input type="hidden" id="empresa_logo" value="<?php echo htmlspecialchars($empresa && !empty($empresa->logotipo) ? base_url('uploads/'.$empresa->logotipo) : ''); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
        </div>
    </footer>
</div>

<script src="<?php echo base_url('public/js/contabilidad_situacion_financiera.js'); ?>"></script>
