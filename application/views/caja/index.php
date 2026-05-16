<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-6">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo($datos_caja->estado == 0 ? '<span class="badge badge-pill badge-dark mb-1">CAJA CERRADA</span>' : '<span class="badge badge-pill badge-success mb-1">CAJA ABIERTA</span>'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                    $cajas_abiertas = $datos_caja->cajas_abiertas;
                    ?>
                    <div class="col-lg-6">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <?php if ($cajas_abiertas == 0): ?>
                                    <a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right mr-2"><i class="fas fa-box-open"></i> Abrir Caja</a>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <?php if ($message = $this->session->flashdata('success')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-success alert-success text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($message = $this->session->flashdata('info')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-info alert-info text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($message = $this->session->flashdata('error')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-danger alert-dagner text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-frown"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Cajas Registradas</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table data-table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Fecha Apertura</th>
                                        <th class="text-center">Monto Apertura</th>
                                        <th class="text-center">Fecha Cierre</th>
                                        <th class="text-center">Monto Cierre</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0; ?>
                                    <?php foreach ($cajas as $caja) : ?>
                                        <?php $i++; ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i; ?> </td>
                                            <td class="text-center"><?php echo formatoFechaHora($caja->fecha_apertura); ?> </td>
                                            <td class="text-center"><?php echo number_format($caja->monto_apertura, 2); ?> </td>
                                            <td class="text-center"><?php echo formatoFechaHora($caja->fecha_cierre); ?> </td>
                                            <td class="text-center"><?php echo number_format($caja->monto_cierre, 2); ?> </td>
                                            <td class="text-center"><?php echo($caja->estado == 1 ? '<span class="badge  badge-success mb-1">ABIERTA</span>' : '<span class="badge badge-warning mb-1">CERRADA</span>'); ?> </td>
                                            <td class="text-center">
                                                <?php if ($caja->estado == 1) : ?>
                                                    <a class="btn bg-dark text-white" href="<?php echo base_url($this->router->fetch_class() . '/core/' . $caja->idcaja) ?>" data-toggle="tooltip" data-placement="top" title="Cerrar <?php echo $this->router->fetch_class(); ?>">Cerrar</a>
                                                <?php endif; ?>
                                                <button id="btnCorte" class="btn bg-warning text-white float-right mr-2 btnCorte" idcaja="<?php echo $caja->idcaja; ?>"><i class="fas fa-cut"></i> Corte de Caja</button>
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
    <!-- Modal -->
    <div class="modal fade" id="modalCorte" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Corte de Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Fecha</label>
                        <input type="hidden" name="cajaId" id="cajaId">
                        <input type="date" id="txtFecha" class="form-control" value="<?php echo date("Y-m-d"); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btnConsultarCorteDia" id="btnConsultarCorteDia">Consultar</button>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>
</div>
