<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-water bg-blue"></i>
                            <div class="d-inline">
                                <h5> Flujo de Efectivo </h5>
                                <span>Entradas y salidas de efectivo</span>
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
                                    <label>Desde</label>
                                    <input type="date" id="flujoStart" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label>Hasta</label>
                                    <input type="date" id="flujoEnd" class="form-control" />
                                </div>
                                <div class="col-md-2">
                                    <button id="flujoRefresh" class="btn btn-primary btn-block">Actualizar</button>
                                </div>
                                <div class="col-md-2">
                                    <button id="flujoExport" class="btn btn-outline-secondary btn-block">Exportar CSV</button>
                                </div>
                                <div class="col-md-2">
                                    <button id="flujoPdf" class="btn btn-info btn-block">Exportar PDF</button>
                                </div>
                            </div>

                            <div id="flujoContent">
                                <div class="table-responsive">
                                    <table id="flujoTable" class="table table-sm table-striped">
                                        <thead>
                                            <tr><th>Fecha</th><th>Asiento</th><th>Descripción</th><th>Categoría</th><th class="text-right">Monto</th></tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr><th colspan="4">Total Colecciones (Créditos)</th><th id="tot_colecciones" class="text-right"></th></tr>
                                            <tr><th colspan="4">Total Intereses / Comisiones</th><th id="tot_intereses" class="text-right"></th></tr>
                                            <tr><th colspan="4">Total Desembolsos</th><th id="tot_desembolsos" class="text-right"></th></tr>
                                            <tr><th colspan="4">Total Pagos Operativos</th><th id="tot_pagos" class="text-right"></th></tr>
                                            <tr><th colspan="4">Total Financiación</th><th id="tot_financiacion" class="text-right"></th></tr>
                                            <tr class="font-weight-bold"><th colspan="4">Flujo Neto</th><th id="tot_neto" class="text-right"></th></tr>
                                        </tfoot>
                                    </table>
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
