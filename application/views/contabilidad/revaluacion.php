<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layout/navbar'); ?>

<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-balanza-header" style="margin-bottom:18px;">
                <div class="d-flex align-items-center">
                    <div style="width:40px;height:40px;background:rgba(0,0,0,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                        <i class="fas fa-exchange-alt" style="color:#1e3c72;font-size:18px;"></i>
                    </div>
                    <div>
                        <h1 style="margin:0;font-size:18px;font-weight:700;">Proceso: Revaluación por Tipo de Cambio</h1>
                        <div style="color:#6b7280;font-size:13px;">Calcular diferencias por cuenta y generar asientos de ajuste</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="revalForm" class="row g-3">
                                <div class="col-md-3">
                                  <label class="form-label">Fecha</label>
                                  <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-3">
                                  <label class="form-label">Tasa nueva</label>
                                  <input type="number" step="0.0001" name="new_rate" class="form-control" placeholder="ej. 36.50" required>
                                </div>
                                <div class="col-md-3">
                                  <label class="form-label">Cuenta Ganancia Cambiaria (ID)</label>
                                  <input type="number" name="fx_gain_account" class="form-control" placeholder="ID cuenta ganancia">
                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Cuenta Pérdida Cambiaria (ID)</label>
                                                                    <input type="number" name="fx_loss_account" class="form-control" placeholder="ID cuenta pérdida">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Cuenta Contra Gastos (ID) - para pasivos</label>
                                                                    <input type="number" name="fx_contra_gastos_account" class="form-control" placeholder="ID cuenta contra gastos">
                                                                </div>
                                <div class="col-12">
                                  <label class="form-label">Notas (opcional)</label>
                                  <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="col-12">
                                  <button type="button" id="previewBtn" class="btn btn-secondary">Vista previa</button>
                                  <button type="button" id="executeBtn" class="btn btn-primary">Ejecutar revaluación</button>
                                  <span id="revalStatus" class="ms-3"></span>
                                </div>
                            </form>

                            <hr>
                            <div id="revalResult"><!-- resultados de preview / ejecución --></div>
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
<script>
    // URL para la ejecución (usa site_url para respetar index.php/rewrite)
    window.REVAL_EXECUTE_URL = '<?php echo site_url("contabilidad/revaluacion_execute"); ?>';
</script>
