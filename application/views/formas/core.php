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
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="<?php echo base_url('/'); ?>" data-toggle="tooltip" data-placement="top" title="Inicio"><i class="ik ik-home"></i></a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="<?php echo $this->router->fetch_class(); ?>" data-toggle="tooltip" data-placement="top" title="Listar <?php echo $this->router->fetch_class(); ?>">Listar <?php echo $this->router->fetch_class(); ?></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $titulo; ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <?php echo (isset($forma) ? '<i class="ik ik-calendar ik-2x"></i> Fecha de la última actualización: ' . formatoFechaHora($forma->actualizado) : ''); ?>
                        </div>
                        <div class="card-body">
                            <form class="forms-sample" name="form_core" method="POST">
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" class="form-control" name="nombre" value="<?php echo (isset($forma) ? $forma->nombre : set_value('nombre')); ?> ">
                                            <?php echo form_error('nombre', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>


                                    <div class="col-md-6" id="">
                                        <div class="form-group">
                                            <label>Estado</label>
                                            <select name="estado" class="form-control">
                                                <?php if (isset($forma)) : ?>
                                                    <option value="0" <?php echo ($forma->estado == 0 ? 'selected' : '') ?>>No</option>
                                                    <option value="1" <?php echo ($forma->estado == 1 ? 'selected' : '') ?>>Sí</option>
                                                <?php else : ?>
                                                    <option value="0">No</option>
                                                    <option value="1">Sí</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if (isset($forma)) : ?>
                                        <div class="col-md-6">
                                            <input type="hidden" name="id" value="<?php echo ($forma->id); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Guardar</button>
                                <a class="btn btn-info" href="<?php echo base_url($this->router->fetch_class()); ?>">Volver</a>
                            </form>
                        </div>
                    </div>
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