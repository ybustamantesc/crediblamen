<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-balance-scale bg-blue"></i>
                            <div class="d-inline">
                                <h5>Conciliación</h5>
                                <span>Conciliaciones bancarias y de caja</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('tesoreria/partial_back'); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-0">Cargue extractos y concilie movimientos.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
