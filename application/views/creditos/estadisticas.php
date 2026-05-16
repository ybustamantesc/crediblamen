<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card prod-p-card card-blue">
                        <div class="card-body">
                            <div class="row align-items-center mb-30">
                                <div class="col">
                                    <h6 class="mb-5 text-white">TOTAL ASESORES</h6>
                                    <h3 class="mb-0 fw-700 text-white"><?php echo $total_asesores; ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users text-blue f-18"></i>
                                </div>
                            </div>
                            <p class="mb-0 text-white"><span class="label label-danger mr-10"><a href="<?php echo base_url('bancos') ?>" class="text-white"> Ir al Módulo</a></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card prod-p-card card-red">
                        <div class="card-body">
                            <div class="row align-items-center mb-30">
                                <div class="col">
                                    <h6 class="mb-5 text-white">TOTAL CLIENTES</h6>
                                    <h3 class="mb-0 fw-700 text-white"><?php echo $total_clientes; ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users text-danger f-18"></i>
                                </div>
                            </div>
                            <p class="mb-0 text-white"><span class="label label-danger mr-10"><a href="<?php echo base_url('clientes') ?>" class="text-white"> Ir al Módulo</a></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card prod-p-card card-yellow">
                        <div class="card-body">
                            <div class="row align-items-center mb-30">
                                <div class="col">
                                    <h6 class="mb-5 text-white">TOTAL CRÉDITOS</h6>
                                    <h3 class="mb-0 fw-700 text-white"><?php echo $total_prestamos; ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments-dollar text-yellow f-18"></i>
                                </div>
                            </div>
                            <p class="mb-0 text-white"><span class="label label-danger mr-10"><a href="<?php echo base_url('prestamo') ?>" class="text-white"> Ir al Módulo</a></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card prod-p-card card-green">
                        <div class="card-body">
                            <div class="row align-items-center mb-30">
                                <div class="col">
                                    <h6 class="mb-5 text-white">TOTAL PAGOS</h6>
                                    <h3 class="mb-0 fw-700 text-white"><?php echo $total_pagos; ?> </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comment-dollar text-green f-18"></i>
                                </div>
                            </div>
                            <p class="mb-0 text-white"><span class="label label-danger mr-10"><a href="<?php echo base_url('pagos') ?>" class="text-white"> Ir al Módulo</a></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3 class="text-danger font-weight-bold">Lista de Cuotas Vencidas</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table data-table table-striped table-bordered  table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center">N° Crédito</th>
                                            <th>Cliente</th>
                                            <th>Fecha Cuota</th>
                                            <th class="text-center">N° Cuota</th>
                                            <th>Fecha Pago</th>
                                            <th>Monto Pagado</th>
                                            <th>Monto Cuota</th>
                                            <th>Monto Pendiente</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        <?php foreach ($coutas_vencidas as $cuota) : ?>
                                            <?php $i++; ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td class="text-center"><?php echo $cuota->idcredito; ?></td>
                                                <td><?php echo $cuota->apellidos . ', ' . $cuota->nombres; ?></td>
                                                <td><?php echo $cuota->fecha_couta; ?></td>
                                                <td class="text-center"><?php echo $cuota->numero_couta; ?></td>
                                                <td><?php echo $cuota->fecha_pago; ?></td>
                                                <td><?php echo $cuota->monto_pagado; ?></td>
                                                <td><?php echo $cuota->monto_couta; ?></td>
                                                <td><?php echo $cuota->monto_pendiente; ?></td>
                                                <td class="text-center"><span class="badge  badge-danger mb-1"><i class="fas fa-exclamation-triangle"></i> VENCIÓ</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-blue d-block">
                            <h3 class="text-white">Lista de Cuotas Pagan hoy <span class="badge badge-pill badge-yellow mb-1"><?php echo date('d/m/Y'); ?></span></h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table data-table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>N° Crédito</th>
                                            <th>Cliente</th>
                                            <th>Fecha Cuota</th>
                                            <th>N° Cuota</th>
                                            <th>Fecha Pago</th>
                                            <th>Monto Pagado</th>
                                            <th>Monto Cuota</th>
                                            <th>Monto Pendiente</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $f = 0; ?>
                                        <?php foreach ($coutas_pagan_hoy as $cuota) : ?>
                                            <?php $f++; ?>
                                            <tr>
                                                <td><?php echo $f; ?></td>
                                                <td><?php echo $cuota->idcredito; ?></td>
                                                <td><?php echo $cuota->apellidos . ', ' . $cuota->nombres; ?></td>
                                                <td><?php echo $cuota->fecha_couta; ?></td>
                                                <td><?php echo $cuota->numero_couta; ?></td>
                                                <td><?php echo $cuota->fecha_pago; ?></td>
                                                <td><?php echo $cuota->monto_pagado; ?></td>
                                                <td><?php echo $cuota->monto_couta; ?></td>
                                                <td><?php echo $cuota->monto_pendiente; ?></td>
                                                <td class="text-center"><span class="badge  badge-primary mb-1"><i class="fas fa-info-circle"></i> PAGA HOY</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <footer class=" footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>
</div>