<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-3">Avalúo de Garantías</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Datos de la Solicitud</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID Solicitud:</strong> <?php echo htmlspecialchars($solicitud_id); ?></li>
                        <li class="list-group-item"><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente_nombre); ?></li>
                        <li class="list-group-item"><strong>Fecha:</strong> <?php echo htmlspecialchars($fecha_solicitud); ?></li>
                        <!-- Agrega más datos relevantes aquí -->
                    </ul>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Garantías</h5>
                    <?php if (!empty($garantias)) : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cant.</th>
                                    <th>Descripción</th>
                                    <th>Modelo</th>
                                    <th>Marca/Color</th>
                                    <th>N° Serie</th>
                                    <th>Avalúo C$</th>
                                    <th>Avalúo US$</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($garantias as $g) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($g->cantidad); ?></td>
                                    <td><?php echo htmlspecialchars($g->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($g->modelo); ?></td>
                                    <td><?php echo htmlspecialchars($g->marca); ?></td>
                                    <td><?php echo htmlspecialchars($g->serie); ?></td>
                                    <td><?php echo number_format($g->costo_usd, 2); ?></td>
                                    <td><?php echo number_format($g->costo_usd, 2); ?></td>
                                    <td><?php echo htmlspecialchars($g->tiempo_vida); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No hay garantías registradas para esta solicitud.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-4">
                <a href="<?php echo site_url('garantias/generar_pdf/'.$solicitud_id); ?>" class="btn btn-primary" target="_blank">
                    <i class="fa fa-file-pdf"></i> Generar PDF
                </a>
            </div>
            <?php if (!empty($error_pdf)) : ?>
                <div class="alert alert-danger">
                    <strong>Error al generar PDF:</strong> <?php echo htmlspecialchars($error_pdf); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->load->view('layout/footer'); ?>
