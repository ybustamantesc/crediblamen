<?php $this->load->view('layout/navbar'); ?>
<style>
    .negative { color: #ef4444; font-weight: 600; }
    .servicont-balanza-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    .servicont-balanza-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
</style>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-balanza-header">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-balance-scale" style="font-size: 24px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h1 class="servicont-catalogo-title">Balanza de Comprobación</h1>
                        <p class="servicont-catalogo-subtitle" style="color: #ffffff !important;">Saldos y verificación de cuentas contables</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="servicont-catalogo-card">
                        <div class="card-body" style="padding: 30px;">
                            <div id="balanzaControls" class="mb-4">
                                <div class="form-row align-items-end mb-3">
                                    <div class="col-md-3">
                                        <label style="font-weight: 600; color: #2a5298; margin-bottom: 8px;">Mes</label>
                                        <input type="month" id="balanzaMonth" class="form-control servicont-input" />
                                        <small class="form-text text-muted">Seleccione el mes para emitir la balanza</small>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="balanzaRefresh" class="servicont-btn-primary btn-block">Actualizar</button>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="balanzaExport" class="servicont-btn-secondary btn-block">Exportar Excel</button>
                                    </div>
                                    <!-- Se muestran siempre las cuentas sin movimiento; casilla removida -->
                                    <div class="col-md-2">
                                        <button id="balanzaPdf" class="servicont-btn-success btn-block" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);">Exportar PDF</button>
                                    </div>
                                    <div class="col-md-3">
                                        <label style="font-weight:600; color:#2a5298; margin-bottom:8px;">Ver por</label>
                                        <div class="d-flex">
                                            <select id="balanzaGroup" class="form-control servicont-input">
                                                <option value="detalle">Detalle</option>
                                                <option value="mayor">Mayor</option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted">Elija "Mayor" para mostrar solo las cuentas de mayor; "Detalle" mostrará todas las cuentas.</small>
                                    </div>
                                    <!-- Botones eliminados: Exportar todo (Excel) y PDF BG - ocultados por solicitud -->
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <label class="small" style="font-weight: 600; color: #2a5298;">Firma autorizada (imagen)</label>
                                        <div class="input-group">
                                            <input type="file" id="firmaFile" accept="image/*" class="form-control form-control-sm servicont-input" />
                                            <div class="input-group-append">
                                                <button id="uploadFirma" class="servicont-btn-secondary btn-sm">Subir</button>
                                            </div>
                                        </div>
                                        <div id="firmaPreview" class="small text-muted mt-1"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="balanzaContent">
                                <div class="table-responsive">
                                    <table id="balanzaTable" class="table servicont-table">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Cuenta</th>
                                                <th class="text-right">Saldo Inicial (Deudor)</th>
                                                <th class="text-right">Saldo Inicial (Acreedor)</th>
                                                <th class="text-right">Debe</th>
                                                <th class="text-right">Haber</th>
                                                <th class="text-right">Saldo Final (Deudor)</th>
                                                <th class="text-right">Saldo Final (Acreedor)</th>
                                                <th class="text-right">Balance Final</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); font-weight: 700;">
                                            <tr>
                                                <th colspan="2">Totales</th>
                                                <th id="tot_open_deudor" class="text-right"></th>
                                                <th id="tot_open_acreedor" class="text-right"></th>
                                                <th id="tot_debits" class="text-right"></th>
                                                <th id="tot_credits" class="text-right"></th>
                                                <th id="tot_close_deudor" class="text-right"></th>
                                                <th id="tot_close_acreedor" class="text-right"></th>
                                                <th id="tot_balance_final" class="text-right"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div id="balanzaFooter" class="mt-2 small text-muted"></div>
                            </div>
                            <div class="row mt-4" style="margin-top:24px;">
                                <div class="col-4 text-center">
                                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                                    <div style="font-weight:700;">Contador General</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                                    <div style="font-weight:700;">Gerente Financiero</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                                    <div style="font-weight:700;">Gerente General</div>
                                </div>
                            </div>
                            <div id="modalContainer"></div>
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
