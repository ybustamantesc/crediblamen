<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-id-card bg-blue"></i>
                            <div class="d-inline">
                                <h5>KYC - Identificación</h5>
                                <span>Datos y documentos del cliente</span>
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
                            <h6>Formulario KYC (placeholder)</h6>
                            <p class="text-muted">Aquí se cargan datos completos del cliente, documentos e información KYC.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
