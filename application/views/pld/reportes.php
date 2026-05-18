<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-file-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Reportes Regulatorios</h5>
                                <span>ROS y reportes periódicos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('pld/partial_back'); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted">Generar Reporte de Operaciones Sospechosas (ROS) y otros (placeholder).</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
