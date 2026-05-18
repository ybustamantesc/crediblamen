<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-wallet bg-blue"></i>
                            <div class="d-inline">
                                <h5>Tesorería</h5>
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
                                <h4 class="mb-1">Módulo de Tesorería</h4>
                                <p class="text-muted mb-0">Cajas y bancos, movimientos, conciliación, pagos y flujo.</p>
                            </div>
                            <div class="d-flex">
                                <a href="<?php echo site_url('menu'); ?>" class="btn btn-sm btn-outline-secondary mr-2" id="btnVolverMenuTeso"><i class="fa fa-arrow-left mr-1"></i> Volver al Menú</a>
                                <button id="btnNuevoMovimiento" class="btn btn-primary mr-2">Nuevo Movimiento</button>
                                <a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="btn btn-outline-secondary mr-2">Movimientos</a>
                                <a href="<?php echo base_url('tesoreria/pagos'); ?>" class="btn btn-outline-secondary mr-2">Pagos</a>
                                <a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="btn btn-outline-secondary">Conciliación</a>
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
                                <li><a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>">Cajas y Bancos</a></li>
                                <li><a href="<?php echo base_url('tesoreria/movimientos'); ?>">Movimientos</a></li>
                                <li><a href="<?php echo base_url('tesoreria/flujo'); ?>">Flujo de Efectivo</a></li>
                                <li><a href="<?php echo base_url('tesoreria/reportes'); ?>">Reportes</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h6>Información rápida</h6></div>
                        <div class="card-body">
                            <p class="text-muted">Registra movimientos, programa pagos y realiza conciliaciones desde aquí.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalContainer"></div>

        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
        </div>
    </footer>
</div>
<script src="<?php echo base_url('public/js/tesoreria_home.js'); ?>"></script>
