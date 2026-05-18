<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layout/navbar'); ?>

<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-balanza-header" style="margin-bottom:18px;">
                <div class="d-flex align-items-center">
                    <div style="width:40px;height:40px;background:rgba(0,0,0,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                        <i class="fas fa-lock" style="color:#1e3c72;font-size:18px;"></i>
                    </div>
                    <div>
                        <h1 style="margin:0;font-size:18px;font-weight:700;">Proceso: Cierre Mensual</h1>
                        <div style="color:#6b7280;font-size:13px;">Cerrar y desbloquear meses para evitar modificaciones</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                          <form id="cierreForm" class="row g-3">
                            <div class="col-md-2">
                              <label class="form-label">Año</label>
                              <input type="number" id="cierreYear" class="form-control" value="<?php echo date('Y'); ?>" min="2000" />
                            </div>
                            <div class="col-md-2">
                              <label class="form-label">Mes</label>
                              <select id="cierreMonth" class="form-control">
                                <?php for($i=1;$i<=12;$i++): ?>
                                  <option value="<?php echo $i; ?>"><?php echo strftime('%B', mktime(0,0,0,$i,1)); ?></option>
                                <?php endfor; ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Notas (opcional)</label>
                              <input type="text" id="cierreNotes" class="form-control" placeholder="Notas (opcional)" />
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                              <button id="btnClosePeriod" class="btn btn-primary me-2" type="button">Cerrar mes</button>
                              <button id="btnRefreshPeriods" class="btn btn-secondary" type="button">Refrescar</button>
                            </div>
                          </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Meses cerrados</div>
                        <div class="card-body">
                          <table class="table table-striped" id="closedPeriodsTable">
                            <thead><tr><th>Año</th><th>Mes</th><th>Cerrado por</th><th>Fecha</th><th>Notas</th><th>Acciones</th></tr></thead>
                            <tbody></tbody>
                          </table>
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

<script>
window.CIERRE_LIST_URL = '<?php echo site_url('contabilidad/cierre_mensual_list'); ?>';
window.CIERRE_CLOSE_URL = '<?php echo site_url('contabilidad/cierre_mensual_close'); ?>';
window.CIERRE_OPEN_URL = '<?php echo site_url('contabilidad/cierre_mensual_open'); ?>';
</script>
