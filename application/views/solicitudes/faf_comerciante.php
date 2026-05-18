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

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr><th>#</th><th>Cliente</th><th>Fecha</th><th>Acción</th></tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($solicitudes)): foreach($solicitudes as $s): ?>
                                <tr>
                                    <td><?php echo $s->idsolicitud; ?></td>
                                    <td><?php echo htmlspecialchars(trim($s->apellidos . ' ' . $s->nombres)); ?></td>
                                    <td><?php echo isset($s->fecha_solicitud) ? $s->fecha_solicitud : ''; ?></td>
                                    <td>
                                        <?php if (!empty($s->faf_comerciante)): ?>
                                            <button class="btn btn-sm btn-success btn-open-faf" data-id="<?php echo $s->idsolicitud; ?>" data-tipo="comerciante">Ver / Editar FAF</button>
                                        <?php else: ?>
                                            <?php if (!empty($s->faf_asalariado)): ?>
                                                <button class="btn btn-sm btn-secondary" disabled title="Bloqueado: FAF Asalariado ya completado">Bloqueado (Asalariado)</button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-primary btn-open-faf" data-id="<?php echo $s->idsolicitud; ?>" data-tipo="comerciante">Crear FAF</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center">No hay solicitudes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reuse the same modal ID and JS as in faf_asalariado; keep behavior consistent -->
            <?php $this->load->view('solicitudes/faf_partial_modal'); ?>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                </div>
            </footer>
        </div>
    </div>
</div>
