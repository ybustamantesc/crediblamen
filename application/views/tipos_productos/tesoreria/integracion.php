<div class="container-fluid">
    <?php $this->load->view('tesoreria/partial_back'); ?>
    <h4>Integración Contable</h4>
    <p>Posteo automático de asientos al diario contable.</p>

    <?php if (!empty($flujo) && is_array($flujo)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cuenta ID</th>
                        <th>Concepto</th>
                        <th>Tipo</th>
                        <th>Proyectado</th>
                        <th>Realizado</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flujo as $f): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($f->fecha); ?></td>
                            <td><?php echo htmlspecialchars($f->cuenta_id); ?></td>
                            <td><?php echo htmlspecialchars($f->concepto); ?></td>
                            <td><?php echo htmlspecialchars($f->tipo); ?></td>
                            <td class="text-right"><?php echo number_format($f->proyectado, 2); ?></td>
                            <td class="text-right"><?php echo is_null($f->realizado) ? '-' : number_format($f->realizado, 2); ?></td>
                            <td><?php echo htmlspecialchars($f->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay movimientos automáticos registrados en el flujo.</div>
    <?php endif; ?>

    <h5 class="mt-4">Pagos</h5>
    <?php if (!empty($pagos) && is_array($pagos)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Beneficiario</th>
                        <th>Cuenta ID</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $p): ?>
                        <?php $link = isset($this->Tesoreria_model) ? $this->Tesoreria_model->get_pago_journal($p->id) : null; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p->fecha); ?></td>
                            <td><?php echo htmlspecialchars($p->beneficiario); ?></td>
                            <td><?php echo htmlspecialchars($p->cuenta_id); ?></td>
                            <td class="text-right"><?php echo number_format($p->monto,2); ?></td>
                            <td><?php echo htmlspecialchars($p->estado); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-create-asiento" data-id="<?php echo $p->id; ?>" data-fecha="<?php echo htmlspecialchars($p->fecha); ?>" data-monto="<?php echo htmlspecialchars($p->monto); ?>" data-beneficiario="<?php echo htmlspecialchars($p->beneficiario); ?>">Crear asiento</button>
                                <button class="btn btn-sm btn-secondary btn-lock-asiento" data-id="<?php echo $p->id; ?>" <?php if (!$link || empty($link->journal_id)) echo 'disabled'; ?> ><?php echo (!empty($link) && $link->locked) ? 'Desbloquear' : 'Bloquear'; ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay pagos registrados.</div>
    <?php endif; ?>

    <script src="<?php echo base_url('public/js/tesoreria_integracion.js'); ?>"></script>
</div>
