<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-receipt'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo isset($titulo) ? $titulo : 'Series de Recibos'; ?> </h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : 'Control de series y consecutivos'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <button id="btnNuevaSerie" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nueva Serie</button>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <style>
                            .table-compact td, .table-compact th{ padding: .12rem .28rem; vertical-align: middle; font-size: .82rem; }
                            .table-compact thead th{ font-size: .78rem; padding: .12rem .28rem; }
                            .table-compact .btn{ padding: .10rem .24rem; font-size: .78rem; }
                            .table-compact td.serie-nombre{ max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                            .table-compact td:last-child{ width: 140px; text-align: center; }
                        </style>
                        <table id="series-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Consecutivo (próximo)</th>
                                    <th>Último emitido</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($series) && is_array($series)) : foreach ($series as $s) : ?>
                                    <tr data-id="<?php echo $s->idserie; ?>">
                                        <td><?php echo $s->idserie; ?></td>
                                        <td><?php echo htmlspecialchars($s->codigo); ?></td>
                                        <td class="serie-nombre"><?php echo htmlspecialchars($s->nombre); ?></td>
                                        <td><?php echo htmlspecialchars($s->codigo . str_pad((int)$s->consecutivo, 3, '0', STR_PAD_LEFT)); ?></td>
                                        <td><?php echo (!empty($s->ultimo_emitido) ? htmlspecialchars($s->codigo . str_pad((int)$s->ultimo_emitido, 3, '0', STR_PAD_LEFT)) : '-'); ?></td>
                                        <td><?php echo ($s->estado ? '<span class="badge badge-success">ACTIVO</span>' : '<span class="badge badge-warning">INACTIVO</span>'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-view" data-id="<?php echo $s->idserie; ?>">Ver</button>
                                            <button class="btn btn-sm btn-warning btn-edit" data-id="<?php echo $s->idserie; ?>">Editar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php $this->load->view('series_recibos/modal'); ?>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                </div>
            </footer>

        </div>
    </div>
</div>

<script src="<?php echo base_url('public/js/series_recibos.js'); ?>"></script>
