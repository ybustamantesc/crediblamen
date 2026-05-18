<?php
$this->load->view('layout/header');
$this->load->view('layout/navbar');
?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-landmark bg-blue"></i>
                            <div class="d-inline">
                                <h5>Conami / PLA</h5>
                                <span>Bienvenido al módulo Conami / PLA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <a href="<?php echo base_url('menu'); ?>" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left mr-1"></i> Regresar al menú
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('layout/footer'); ?>
