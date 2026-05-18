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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="forms-sample" name="form_core" method="POST">
                                <div class="form-group row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Nombre Moneda</label>
                                            <input type="text" class="form-control" name="nombre" required value="<?php echo (isset($moneda) ? $moneda->nombre : set_value('nombre')); ?> ">
                                            <?php echo form_error('nombre', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Símbolo</label>
                                            <input type="text" class="form-control" name="simbolo" required value="<?php echo (isset($moneda) ? $moneda->simbolo : set_value('simbolo')); ?> ">
                                            <?php echo form_error('simbolo', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Activo</label>
                                            <select name="estado" class=" form-control">
                                                <?php if (isset($moneda)) : ?>
                                                    <option value="0" <?php echo ($moneda->estado == 0 ? 'selected' : '') ?>>NO</option>
                                                    <option value="1" <?php echo ($moneda->estado == 1 ? 'selected' : '') ?>>SI</option>
                                                <?php else : ?>
                                                    <option value="1">SI</option>
                                                    <option value="0">NO</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if (isset($moneda)) : ?>
                                        <div class="col-md-6">
                                            <input type="hidden" name="id" value="<?php echo ($moneda->id); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Guardar</button>
                                <a class="btn btn-danger" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
                            </form>
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