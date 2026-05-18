<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono_view; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <?php if (isset($caja)) : ?>
                                <div class="text-center">
                                    <h3>Cerrar Caja</h3>
                                </div>
                            <?php else : ?>
                                <div class="text-center">
                                    <h3 class="text-center">Abrir Caja</h3>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <form class="forms-sample" name="form_core" method="POST">
                                <div class="form-group">
                                    <?php if (!isset($caja)) : ?>
                                        <div class="form-group">
                                            <label>Monto de Apertura</label>
                                            <input type="text" class="form-control money" id="money" <?php echo(isset($caja) ? 'disabled' : '') ?> name="monto_apertura" required value="<?php echo(isset($caja) ? $caja->monto_apertura : set_value('monto_apertura')); ?>">
                                            <?php echo form_error('monto_apertura', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($caja)) : ?>
                                        <div class="form-group">
                                            <?php $total = 0; ?>
                                            <?php
                                            foreach ($caja_detalle as $cjd) {
                                                $total = $total + $cjd->monto_movimiento;
                                            }
                                            ?>
                                            <label>Monto de Cierre</label>
                                            <input type="text" class="form-control" name="monto_cierre" <?php echo(isset($caja) ? 'required' : ''); ?> value="<?php echo(isset($caja) ? $total + $caja->monto_apertura : set_value('monto_cierre')); ?>">
                                            <?php echo form_error('monto_cierre', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($caja)) : ?>
                                        <input type="hidden" name="idcaja" value="<?php echo($caja->idcaja); ?>">
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Guardar</button>
                                <a class="btn btn-danger float-right" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
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
