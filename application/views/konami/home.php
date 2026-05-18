<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
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
                                <h5>Conami</h5>
                                <span>Panel de control</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">Módulo Conami</h4>
                                <p class="text-muted mb-0">Cheats, historia y contenido temático.</p>
                            </div>
                            <div class="d-flex">
                                <a href="<?php echo site_url('menu'); ?>" class="btn btn-sm btn-outline-secondary mr-2" id="btnVolverMenuKonami"><i class="fa fa-arrow-left mr-1"></i> Volver al Menú</a>
                                <a href="<?php echo base_url('konami/cheats'); ?>" class="btn btn-outline-secondary mr-2">Cheats</a>
                                <a href="<?php echo base_url('konami/history'); ?>" class="btn btn-outline-secondary mr-2">Historia</a>
                                <a href="<?php echo base_url('konami/about'); ?>" class="btn btn-outline-secondary">Acerca de</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h6>Atajos</h6></div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><a href="<?php echo base_url('konami/cheats'); ?>">Cheats</a></li>
                                <li><a href="<?php echo base_url('konami/history'); ?>">Historia</a></li>
                                <li><a href="<?php echo base_url('konami/about'); ?>">Acerca de</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h6>Información rápida</h6></div>
                        <div class="card-body">
                            <p class="text-muted">Explora el contenido del módulo Conami con una UI uniforme.</p>
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
<?php $this->load->view('layout/footer'); ?>
