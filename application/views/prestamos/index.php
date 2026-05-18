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
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo</a>
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
                            <h3>Créditos Registrados</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table data-table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="nosort text-center">Orden</th>
                                        <th>N° Crédito</th>
                                        <th>Cliente</th>
                                        <th>Fecha Crédito</th>
                                        <th>Monto Crédito</th>
                                        <th>Interés</th>
                                        <th>Cuotas</th>
                                        <th>Total Interés</th>
                                        <th>Total Pagar</th>
                                        <th>Total Saldo</th>
                                        <th>Forma Pago</th>
                                        <th>Estado</th>
                                        <th class="text-right pr-25 nosort">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0; ?>
                                    <?php foreach ($prestamos as $prestamo) : ?>
                                        <?php $i++; ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i; ?> </td>
                                            <td class="text-center"><?php echo $prestamo->id; ?> </td>
                                            <td><?php echo $prestamo->apellidos . ', ' . $prestamo->nombres; ?> </td>
                                            <td><?php echo formatoFechaCorta($prestamo->fecha_credito); ?> </td>
                                            <td><?php echo number_format($prestamo->monto_credito, 2); ?> </td>
                                            <td><?php echo number_format($prestamo->interes_credito, 2) . '%'; ?> </td>
                                            <td class="text-center"><?php echo $prestamo->numero_coutas; ?> </td>
                                            <td><?php echo number_format($prestamo->total_interes, 2); ?> </td>
                                            <td><?php echo number_format($prestamo->total_pagar, 2); ?> </td>
                                            <td><?php echo number_format($prestamo->total_saldo, 2); ?> </td>
                                            <td class="text-center">
                                                <?php if ($prestamo->forma_pago == 0) : ?>
                                                    <span class="badge badge-pill badge-success mb-1">DIARIO</span>
                                                <?php elseif ($prestamo->forma_pago == 1) : ?>
                                                    <span class="badge badge-pill badge-primary mb-1">SEMANAL</span>
                                                <?php elseif ($prestamo->forma_pago == 2) : ?>
                                                    <span class="badge badge-pill badge-info mb-1">QUINCENAL</span>
                                                <?php elseif ($prestamo->forma_pago == 3) : ?>
                                                    <span class="badge badge-pill badge-warning mb-1">MENSUAL</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($prestamo->estado == 0) : ?>
                                                    <span class="badge  badge-pill  badge-success mb-1"><i class="fas fa-check-circle"></i> PAGADO</span>
                                                <?php elseif ($prestamo->estado == 1) : ?>
                                                    <span class="badge  badge-pill badge-danger mb-1"><i class="fas fa-lock"></i> POR COBRAR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="table-actions text-center">
                                                    <a target="_blank" href="<?php echo base_url($this->router->fetch_class() . '/pdf/' . $prestamo->id); ?>" data-toggle="tooltip" data-placement="top" title="Estado de Cuenta"><i class="fas fa-file-pdf text-danger"></i></a>
                                                    <?php if ($prestamo->estado == 1) : ?>
                                                        <a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $prestamo->id) ?>" data-toggle="tooltip" data-placement="top" title="Editar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-edit f-16 mr-15 text-success"></i></a>
                                                    <?php endif; ?>
                                                    <?php if ($prestamo->estado == 0) : ?>
                                                        <a href="" data-toggle="modal" data-target="#prestamo-<?php echo $prestamo->id ?>" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-trash-2 f-16 text-danger"></i></a>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="prestamo-<?php echo $prestamo->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="exampleModalCenterLabel"><i class="fas fa-exclamation-triangle"></i> ¿Quieres eliminar el registro?</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Si desea eliminar el registro click en <strong>Sí, eliminar.</strong></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Cancelar" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $prestamo->id) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>

</div>
