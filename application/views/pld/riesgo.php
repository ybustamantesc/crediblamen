<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-chart-pie bg-blue"></i>
                            <div class="d-inline">
                                <h5>Evaluación de Riesgos</h5>
                                <span>Matriz y scoring</span>
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
                            <p class="text-muted">Matriz de riesgo y scoring (placeholder).</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
