<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-clipboard-list bg-blue"></i>
                            <div class="d-inline">
                                <h5>Bitácora de Cumplimiento</h5>
                                <span>Registro de acciones y auditorías</span>
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
                            <p class="text-muted">Registro de actividades del oficial de cumplimiento (placeholder).</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
