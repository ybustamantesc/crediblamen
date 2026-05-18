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
                                <h5> Balance General </h5>
                                <span>Reporte de situación financiera</span>
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
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <h5>Balance General</h5>
                                    <p class="text-muted">Reporte de situación financiera (contextualizado para Nicaragua).</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="form-inline justify-content-end">
                                        <label class="mr-2">Al:</label>
                                        <input id="balance_as_of" type="date" class="form-control form-control-sm mr-2" value="<?php echo date('Y-m-d'); ?>">
                                        <button id="btn_balance_refresh" class="btn btn-primary btn-sm mr-1">Actualizar</button>
                                        <button id="btn_balance_print" class="btn btn-secondary btn-sm mr-1">Imprimir</button>
                                        <button id="btn_balance_pdf" class="btn btn-info btn-sm mr-1">Exportar PDF</button>
                                        <button id="btn_balance_csv" class="btn btn-outline-success btn-sm">Exportar CSV</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="tbl_balance" class="table table-sm table-striped table-bordered">
                                  <thead>
                                    <tr>
                                      <th>Grupo</th>
                                      <th>Código</th>
                                      <th>Cuenta</th>
                                      <th class="text-right">Saldo</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr><td colspan="4" class="text-center text-muted">Seleccione una fecha y presione "Actualizar"</td></tr>
                                  </tbody>
                                  <tfoot>
                                    <tr class="font-weight-bold"><td colspan="3">Total Activo</td><td id="total_activo" class="text-right"></td></tr>
                                    <tr class="font-weight-bold"><td colspan="3">Total Pasivo</td><td id="total_pasivo" class="text-right"></td></tr>
                                    <tr class="font-weight-bold"><td colspan="3">Total Patrimonio</td><td id="total_patrimonio" class="text-right"></td></tr>
                                    <tr class="font-weight-bold"><td colspan="3">Total Pasivo + Patrimonio</td><td id="total_pasivo_patrimonio" class="text-right"></td></tr>
                                  </tfoot>
                                </table>
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
