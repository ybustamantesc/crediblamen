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
                                <h5> Estado de Resultados </h5>
                                <span>Ingresos y gastos</span>
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
                                <div class="col-md-2">
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
                                    <label>Moneda</label>
                                    <select id="resCurrency" class="form-control">
                                        <option value="local">Córdobas</option>
                                        <option value="usd">Dólares</option>
                                    </select>
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
                                    /* Minimal print/report styles to resemble the requested layout */
                                    .report-wrap { font-family: Arial, Helvetica, sans-serif; font-size:13px; }
                                    .report-header { display:flex; justify-content:space-between; align-items:flex-end; border-bottom:1px dotted #333; padding-bottom:8px; margin-bottom:10px; }
                                    .report-title { font-weight:700; font-size:16px; }
                                    .report-year { font-weight:700; font-size:16px; }
                                    .report-body { margin-top:8px; border:1px solid #333; border-collapse:collapse; }
                                    .r-row { display:grid; grid-template-columns: 1fr auto; border-bottom:1px solid #333; margin:0; }
                                    .r-row > .desc { padding:6px 12px; border-right:1px solid #333; }
                                    .r-row > .amt { padding:6px 12px; text-align:right; font-variant-numeric: tabular-nums; min-width:120px; }
                                    .r-section { font-weight:700; background-color:#f5f5f5; grid-template-columns: 1fr auto; }
                                    .r-section > .desc { padding:6px 12px; background-color:#f5f5f5; border-right:1px solid #333; }
                                    .r-section > .amt { padding:6px 12px; background-color:#f5f5f5; }
                                    .r-total { font-weight:700; border-top:2px solid #000; background-color:#f9f9f9; }
                                    .r-total > .desc { background-color:#f9f9f9; }
                                    .r-total > .amt { background-color:#f9f9f9; }
                                    .report-totals { display:none; }
                                    .r-sign { margin-top:24px; display:flex; justify-content:space-between; }
                                    .r-sign .sig { width:30%; text-align:center; border-top:1px solid #000; padding-top:8px; }
                                </style>

                                <div id="resReport" class="report-wrap">
                                    <div class="report-header">
                                        <div class="report-title">Estado de Resultados</div>
                                        <div class="report-year" id="reportYear"></div>
                                    </div>

                                    <div class="report-body" id="resReportBody">
                                        <!-- rows injected by JS -->
                                    </div>

                                    <div class="report-totals mt-3" id="resTotals">
                                        <div class="r-row r-total"><div class="desc">Total Ingresos</div><div class="amt" id="tot_ingresos"></div></div>
                                        <div class="r-row r-total"><div class="desc">Total Gastos</div><div class="amt" id="tot_gastos"></div></div>
                                        <div class="r-row r-total"><div class="desc">Resultado Operativo</div><div class="amt" id="res_operativo"></div></div>
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
