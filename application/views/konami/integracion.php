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
                            <i class="fas fa-link bg-blue"></i>
                            <div class="d-inline">
                                <h5>Integración Contable</h5>
                                <span>Asientos automáticos y conciliaciones</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card"><div class="card-body"><p class="text-muted">Placeholder para cruces entre contabilidad, tesorería y cartera.</p></div></div>
        </div>
    </div>
    <footer class="footer"><div class="w-100 clearfix"><span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1</span><span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Serviconta</span></div></footer>
</div>
<?php $this->load->view('layout/footer'); ?>