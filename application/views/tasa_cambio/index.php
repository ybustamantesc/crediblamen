<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .servicont-tasa-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    .servicont-tasa-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-tasa-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-dollar-sign" style="font-size: 24px; color: #ffffff;"></i>
                        </div>
                        <div>
                            <h1 class="servicont-catalogo-title"><?php echo $titulo; ?></h1>
                            <p class="servicont-catalogo-subtitle" style="color: #ffffff !important;"><?php echo $subtitulo; ?></p>
                        </div>
                    </div>
                    <a class="servicont-btn-primary" href="<?php echo base_url('tasacambio/core'); ?>" style="text-decoration: none;">
                        <i class="fas fa-plus mr-2"></i>Nueva Tasa de Cambio
                    </a>
                </div>
            </div>

            <div class="servicont-catalogo-card">
                <div class="card-body" style="padding: 30px;">
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

                    <div class="table-responsive">
                        <table class="table servicont-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Tipo Cambio COMPRA (C$)</th>
                                    <th>Tipo Cambio VENTA (C$)</th>
                                    <th>Registrada</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tasas)): ?>
                                    <?php foreach ($tasas as $t): ?>
                                        <tr>
                                            <td><?php echo $t->id; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($t->fecha)); ?></td>
                                            <td class="text-right"><strong style="color: #10b981;">C$ <?php echo number_format($t->tasa_cambio, 4); ?></strong></td>
                                            <td class="text-right"><strong style="color: #3b82f6;">C$ <?php echo number_format($t->tasa_venta, 4); ?></strong></td>
                                            <td><?php echo $t->created_at ? date('d/m/Y H:i', strtotime($t->created_at)) : ''; ?></td>
                                            <td style="text-align: center;">
                                                <a href="<?php echo base_url('tasacambio/core/' . $t->id); ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; margin-right: 5px;">Editar</a>
                                                <a href="<?php echo base_url('tasacambio/delete/' . $t->id); ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none;" onclick="return confirm('¿Está seguro de eliminar esta tasa de cambio?');">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="padding: 40px; color: #6b7280;">
                                            <i class="fas fa-dollar-sign" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                            <div style="font-size: 16px; font-weight: 600;">No hay tasas de cambio registradas</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    </div>
</div>
