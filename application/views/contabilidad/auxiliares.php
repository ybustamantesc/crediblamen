<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layout/navbar'); ?>

<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-balanza-header" style="margin-bottom:18px; padding: 30px 30px;">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="margin-right:12px; width:40px; height:40px; background: rgba(255, 255, 255, 0.16); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-file-alt" style="color:#ffffff; font-size:18px"></i>
                    </div>
                    <div>
                        <h1 class="servicont-header-title">Reporte: Auxiliares</h1>
                        <div class="servicont-header-subtitle">Lista de movimientos por cuenta</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
                                <button id="btnSelectAccounts" class="btn btn-sm btn-outline-primary">Seleccionar cuentas</button>
                                <label class="mb-0">Inicio: <input type="date" id="auxStart" class="form-control form-control-sm" /></label>
                                <label class="mb-0">Fin: <input type="date" id="auxEnd" class="form-control form-control-sm" /></label>
                                <button id="btnRunAux" class="btn btn-sm btn-primary">Generar</button>
                                <button id="btnClearFilters" class="btn btn-sm btn-outline-secondary">Limpiar</button>
                                <button id="btnExportAux" class="btn btn-sm btn-secondary">Exportar CSV</button>
                                <button id="btnExportXlsx" class="btn btn-sm btn-success">Exportar XLSX</button>
                                <button id="btnExportPdf" class="btn btn-sm btn-danger">Exportar PDF</button>
                                <div style="margin-left:6px;display:inline-block;">
                                    <select id="auxCurrency" class="form-control form-control-sm" style="width:160px;display:inline-block;">
                                        <option value="local">Moneda local</option>
                                        <option value="usd">Dólares (USD)</option>
                                    </select>
                                </div>
                            </div>

                            <div id="auxSelected" style="margin-bottom:10px;color:#444;font-size:13px;">Cuentas seleccionadas: <span id="auxCount">0</span></div>

                            <div id="auxResult"><!-- results will be injected here --></div>

                            <div id="modalContainer"></div>
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

<!-- Script is loaded via the layout footer using $scripts from the controller to avoid duplicates -->
