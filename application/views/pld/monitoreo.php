<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-search-dollar bg-blue"></i>
                            <div class="d-inline">
                                <h5>Monitoreo de Transacciones</h5>
                                <span>Parámetros y alertas</span>
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
                            <p class="text-muted">Panel de monitoreo (placeholder) — configurar reglas, ver operaciones inusuales.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
