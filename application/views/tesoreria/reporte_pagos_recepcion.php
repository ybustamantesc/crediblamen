<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <?php
                $rows = isset($rows) && is_array($rows) ? $rows : array();
                $fecha = isset($fecha) ? $fecha : date('Y-m-d');
                $modo = isset($modo) ? strtolower((string)$modo) : '';
                $tituloRep = ($modo === 'arqueo') ? 'Reporte Arqueo de Pagos' : 'Reporte Recepcion de Pagos';
                $grupos = array();
                $total = 0;
                $totalesReporte = isset($totales_reporte) && is_array($totales_reporte) ? $totales_reporte : array('aplicado' => 0, 'anulado' => 0, 'general' => 0);
                foreach ($rows as $r) {
                    $ref = isset($r->documento_numero) ? trim((string)$r->documento_numero) : '';
                    $serie = !empty($r->serie_codigo) ? strtoupper(trim((string)$r->serie_codigo)) : 'SIN SERIE';
                    if ($serie === 'SIN SERIE' && $ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $m)) {
                        $serie = strtoupper($m[1]);
                    }
                    if (!isset($grupos[$serie])) $grupos[$serie] = array();
                    $grupos[$serie][] = $r;
                    $total += isset($r->monto) ? floatval($r->monto) : 0;
                }
                ksort($grupos);
            ?>

            <div class="page-header mb-3">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-file-invoice-dollar bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo $tituloRep; ?></h5>
                                <span>Fecha: <?php echo html_escape($fecha); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                        <button onclick="window.print();" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Imprimir</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="border rounded p-2 bg-light"><strong>Total registros:</strong> <?php echo count($rows); ?></div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="border rounded p-2 bg-light"><strong>Total aplicado:</strong> $<?php echo number_format(isset($totalesReporte['aplicado']) ? floatval($totalesReporte['aplicado']) : 0, 2); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-2 bg-light"><strong>Total anulado:</strong> $<?php echo number_format(isset($totalesReporte['anulado']) ? floatval($totalesReporte['anulado']) : 0, 2); ?></div>
                        </div>
                    </div>
                    <div class="mb-2"><strong>Total general:</strong> $<?php echo number_format(isset($totalesReporte['general']) ? floatval($totalesReporte['general']) : $total, 2); ?></div>
                    <?php if (empty($grupos)): ?>
                        <div class="text-muted">No hay registros para esta fecha.</div>
                    <?php else: ?>
                        <?php foreach ($grupos as $serie => $items): ?>
                            <?php $subtotal = 0; foreach ($items as $it) { $subtotal += isset($it->monto) ? floatval($it->monto) : 0; } ?>
                            <h6 class="mt-3">Serie <?php echo html_escape($serie); ?> - Subtotal $<?php echo number_format($subtotal, 2); ?></h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Beneficiario</th>
                                            <th>Concepto</th>
                                            <th>Monto Sistema</th>
                                            <th>Monto Recibido</th>
                                            <th>Fecha Recepcion</th>
                                            <th>Referencia</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $i => $it): ?>
                                            <?php
                                                $estadoRow = isset($it->estado) ? strtolower(trim($it->estado)) : '';
                                                $estadoLabel = ($estadoRow === 'aplicado_pendiente_arqueo') ? 'Revisado' : (($estadoRow === 'anulado') ? 'Anulado' : ucfirst($estadoRow));
                                                $estadoClass = ($estadoRow === 'aplicado_pendiente_arqueo') ? 'badge-success' : (($estadoRow === 'anulado') ? 'badge-danger' : 'badge-secondary');
                                            ?>
                                            <tr>
                                                <td><?php echo $i + 1; ?></td>
                                                <td><?php echo html_escape(isset($it->fecha) ? $it->fecha : '-'); ?></td>
                                                <td><?php echo html_escape(isset($it->beneficiario) ? $it->beneficiario : '-'); ?></td>
                                                <td><?php echo html_escape(isset($it->concepto) ? $it->concepto : '-'); ?></td>
                                                <td>$<?php echo number_format(isset($it->monto) ? floatval($it->monto) : 0, 2); ?></td>
                                                <td>$<?php echo number_format(isset($it->monto_recibido) ? floatval($it->monto_recibido) : 0, 2); ?></td>
                                                <td><?php echo html_escape(isset($it->fecha_recepcion) ? $it->fecha_recepcion : '-'); ?></td>
                                                <td><?php echo html_escape(isset($it->documento_numero) ? $it->documento_numero : '-'); ?></td>
                                                <td><span class="badge <?php echo $estadoClass; ?>"><?php echo html_escape($estadoLabel); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
