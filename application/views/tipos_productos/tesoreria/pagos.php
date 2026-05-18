<div class="container-fluid">
    <?php $this->load->view('tesoreria/partial_back'); ?>
    <h4>Pagos</h4>
    <button class="btn btn-secondary" id="btnProgramarPago">Programar Pago</button>
    <div id="modalContainer"></div>
</div>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-money-check-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Pagos</h5>
                                <span>Programación y ejecución de pagos</span>
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
                            <button class="btn btn-secondary" id="btnProgramarPago">Programar Pago</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalContainer"></div>

        </div>
    </div>
</div>
