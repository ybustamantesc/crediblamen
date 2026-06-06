<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-chart-line bg-blue"></i>
                            <div class="d-inline">
                                <h5> Desagregación de Cuentas </h5>
                                <span>Integración de Cuentas a Estados Financieros</span>
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
                            <div class="form-row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label>Mes</label>
                                    <input type="month" id="resMonth" class="form-control" />
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check" style="margin-top:6px;">
                                        <input class="form-check-input" type="checkbox" id="resAcumulado" />
                                        <label class="form-check-label" for="resAcumulado">Acumulado</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="resRefresh" class="btn btn-primary btn-block">Actualizar</button>
                                </div>
                                <!-- Export CSV hidden by request -->
                                <div class="col-md-2">
                                    <button id="resExcel" class="btn btn-info btn-block">Exportar Excel</button>
                                </div>
                                <div class="col-md-2">
                                    <button id="resPdfReal" class="btn btn-secondary btn-block">Exportar PDF</button>
                                </div>
                            </div>

                            <div id="resContent">
                                <style>
                                    .detail-row td { border: none !important; padding: 0 !important; }
                                    .detail-inner-table th, .detail-inner-table td { padding: 6px 8px; }
                                    .detail-inner-table th { background: #f8f9fa; }
                                    .type-card { border: 1px solid #dee2e6; border-radius: 0.25rem; margin-bottom: 1rem; }
                                    .type-card-header { background: #f1f3f5; padding: 0.75rem 1rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
                                    .type-card-body { padding: 1rem; }
                                    .type-row-card { border: 1px solid #ced4da; border-radius: 0.35rem; margin-bottom: 1rem; }
                                    .type-row-header { background: #e9ecef; padding: 0.75rem 1rem; display: flex; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
                                    .type-row-header input { max-width: 400px; }
                                    .type-row-header .row-total { white-space: nowrap; font-size: 1rem; font-weight: 700; color: #333; }
                                    .type-row-body { padding: 1rem; }
                                    .accounts-search-results { max-width: 100%; overflow: hidden; }
                                    .accounts-search-results .list-group-item { white-space: normal; }
                                    .rows-empty { padding: 1rem; color: #6c757d; }
                                </style>

                                <div class="card mb-0">
                                    <div class="card-body p-0">
                                        <ul class="nav nav-tabs" id="desagregacionTabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="balance-tab" data-toggle="tab" href="#balance_panel" role="tab" aria-controls="balance_panel" aria-selected="true">Balance</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="resultados-tab" data-toggle="tab" href="#resultados_panel" role="tab" aria-controls="resultados_panel" aria-selected="false">Resultados</a>
                                            </li>
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <button id="saveDesagregacion" class="btn btn-success btn-sm">Guardar configuración</button>
                                                <span id="saveStatus" class="ml-3 text-muted"></span>
                                            </div>
                                            <div class="text-right text-muted small">Orden de filas guarda el orden actual</div>
                                        </div>
                                        <div class="tab-content p-3">
                                            <div class="tab-pane fade show active" id="balance_panel" role="tabpanel" aria-labelledby="balance-tab">
                                                <div id="balance_content"></div>
                                            </div>
                                            <div class="tab-pane fade" id="resultados_panel" role="tabpanel" aria-labelledby="resultados-tab">
                                                <div id="resultados_content"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
