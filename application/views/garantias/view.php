<?php defined('BASEPATH') OR exit('No direct script access allowed');
$g = isset($g) ? $g : null;
if (! $g) { echo '<div class="alert alert-warning">No existe el registro.</div>'; return; }
$this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-shield-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Formato de Garantía - Solicitud #<?php echo $g->solicitud_id; ?></h5>
                                <span>Detalle del formato y fotos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a class="btn btn-secondary" href="<?php echo base_url('garantias'); ?>">Volver</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div></div>
                                <div>
                                    <a class="btn btn-info btn-sm" href="<?php echo base_url('garantias/pdf/'.$g->id); ?>" target="_blank">Descargar PDF</a>
                                    <button class="btn btn-outline-dark btn-sm" onclick="window.print();">Imprimir</button>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <tr><th>Nombre Garantía</th><td><?php echo html_escape($g->nombre); ?></td></tr>
                                <tr><th>Cantidad</th><td><?php echo (int)$g->cantidad; ?></td></tr>
                                <tr><th>Marca</th><td><?php echo html_escape($g->marca); ?></td></tr>
                                <tr><th>Modelo</th><td><?php echo html_escape($g->modelo); ?></td></tr>
                                <tr><th>Nº Serie</th><td><?php echo html_escape(isset($g->n_serie) ? $g->n_serie : ''); ?></td></tr>
                                <tr><th>Avaluo</th><td><?php echo html_escape($g->costo); ?></td></tr>
                                <tr><th>Estado</th><td><?php echo html_escape($g->tiempo_vida); ?></td></tr>
                            </table>

                            <div class="row">
                                <?php for ($i=1;$i<=5;$i++): $f = 'foto'.$i; if (! empty($g->$f)): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="<?php echo base_url($g->$f); ?>" class="img-fluid" style="max-height:260px; object-fit:cover;">
                                    </div>
                                </div>
                                <?php endif; endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
