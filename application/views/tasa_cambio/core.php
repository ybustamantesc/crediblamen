<?php defined('BASEPATH') OR exit('No direct script access allowed');
$t = isset($tasa) ? $tasa : null;
?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo $titulo; ?></h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a class="btn btn-secondary" href="<?php echo base_url('tasacambio'); ?>">Volver al Listado</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if ($this->session->flashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $this->session->flashdata('message'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $this->session->flashdata('error'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php echo form_open('tasacambio/save'); ?>
                        <?php if ($t): ?>
                            <input type="hidden" name="id" value="<?php echo $t->id; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           value="<?php echo $t ? $t->fecha : date('Y-m-d'); ?>" required>
                                    <small class="form-text text-muted">Fecha para la cual aplica esta tasa.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tasa_cambio">Tipo de Cambio COMPRA <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">C$</span>
                                        </div>
                                        <input type="number" step="0.0001" class="form-control" id="tasa_cambio" name="tasa_cambio" 
                                               value="<?php echo $t ? $t->tasa_cambio : ''; ?>" required min="0.0001" placeholder="36.5000">
                                    </div>
                                    <small class="form-text text-muted">Compra de dólares (menor)</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tasa_venta">Tipo de Cambio VENTA <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">C$</span>
                                        </div>
                                        <input type="number" step="0.0001" class="form-control" id="tasa_venta" name="tasa_venta" 
                                               value="<?php echo $t ? $t->tasa_venta : ''; ?>" required min="0.0001" placeholder="37.0000">
                                    </div>
                                    <small class="form-text text-muted">Venta de dólares (mayor)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $t ? 'Actualizar' : 'Guardar'; ?> Tasa de Cambio
                            </button>
                            <a href="<?php echo base_url('tasacambio'); ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                    <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
                </div>
            </footer>
        </div>
    </div>
</div>
