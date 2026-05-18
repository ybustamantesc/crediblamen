<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <style>
                .arqueo-card {
                    border: 1px solid #e3ebf7;
                    border-radius: 12px;
                    box-shadow: 0 6px 16px rgba(2, 48, 71, .05);
                }
                .arqueo-kpi {
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    background: #f8fbff;
                    padding: 12px 14px;
                    height: 100%;
                }
                .arqueo-kpi .label {
                    font-size: .78rem;
                    color: #54739a;
                    text-transform: uppercase;
                    letter-spacing: .4px;
                    font-weight: 700;
                    margin-bottom: 3px;
                }
                .arqueo-kpi .value {
                    font-size: 1.06rem;
                    color: #13315c;
                    font-weight: 700;
                }
                .arqueo-toolbar {
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    background: #f8fbff;
                    padding: 10px 12px;
                    margin-bottom: 12px;
                }
                .arqueo-serie-head {
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:.75rem;
                    margin-bottom: .45rem;
                    padding: 0 .15rem;
                }
                .arqueo-badge {
                    font-size: .72rem;
                    border-radius: 999px;
                    padding: .28rem .55rem;
                    font-weight: 700;
                }
                .arqueo-table thead th {
                    white-space: nowrap;
                }
            </style>

            <?php
                $rows = isset($rows) && is_array($rows) ? $rows : array();
                $grupos = isset($grupos) && is_array($grupos) ? $grupos : array();
                $fecha = isset($fecha) && !empty($fecha) ? $fecha : date('Y-m-d');
                $modo = isset($modo) ? strtolower((string)$modo) : '';
                $tituloVista = isset($titulo) ? $titulo : 'Arqueos de Pagos';
                $totales = isset($totales_reporte) && is_array($totales_reporte) ? $totales_reporte : array('aplicado' => 0, 'anulado' => 0, 'general' => 0, 'aplicados_count' => 0, 'anulados_count' => 0);
            ?>

            <div class="page-header mb-3">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-cash-register bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo html_escape($tituloVista); ?></h5>
                                <span>Historial diario de pagos guardados, aplicados y anulados.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                        <a href="<?php echo base_url('tesoreria/pagos'); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-left"></i> Volver a Pagos</a>
                        <a href="<?php echo base_url('tesoreria/arqueos_pdf?fecha=' . urlencode($fecha) . (!empty($modo) ? '&modo=' . urlencode($modo) : '') . (!empty($filtro_q) ? '&q=' . urlencode($filtro_q) : '')); ?>" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
                        <button onclick="window.print();" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Imprimir</button>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <div class="arqueo-kpi">
                        <div class="label">Fecha</div>
                        <div class="value"><?php echo html_escape($fecha); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <div class="arqueo-kpi">
                        <div class="label">Aplicados</div>
                        <div class="value"><?php echo number_format(isset($totales['aplicados_count']) ? intval($totales['aplicados_count']) : 0, 0); ?> | $<?php echo number_format(isset($totales['aplicado']) ? floatval($totales['aplicado']) : 0, 2); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <div class="arqueo-kpi">
                        <div class="label">Anulados</div>
                        <div class="value"><?php echo number_format(isset($totales['anulados_count']) ? intval($totales['anulados_count']) : 0, 0); ?> | $<?php echo number_format(isset($totales['anulado']) ? floatval($totales['anulado']) : 0, 2); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <div class="arqueo-kpi">
                        <div class="label">Total General</div>
                        <div class="value">$<?php echo number_format(isset($totales['general']) ? floatval($totales['general']) : 0, 2); ?></div>
                    </div>
                </div>
            </div>

            <div class="card arqueo-card mb-3">
                <div class="card-body">
                    <form class="arqueo-toolbar" method="get" action="<?php echo base_url('tesoreria/arqueos'); ?>">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Fecha</label>
                                <input type="date" class="form-control" name="fecha" value="<?php echo html_escape($fecha); ?>">
                            </div>
                            <div class="col-md-5 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Buscar</label>
                                <input type="text" class="form-control" name="q" placeholder="Cliente, concepto o referencia" value="<?php echo html_escape(isset($filtro_q) ? $filtro_q : ''); ?>">
                            </div>
                            <div class="col-md-4 text-md-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                                <a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i> Hoy</a>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($grupos)): ?>
                        <div class="text-muted">No hay pagos guardados para esta fecha.</div>
                    <?php else: ?>
                        <?php foreach ($grupos as $serie => $items): ?>
                            <?php
                                $subtotal = 0;
                                $aplicadosSerie = 0;
                                $anuladosSerie = 0;
                                foreach ($items as $it) {
                                    $subtotal += isset($it->monto) ? floatval($it->monto) : 0;
                                    $estadoRow = isset($it->estado) ? strtolower(trim($it->estado)) : '';
                                    if ($estadoRow === 'aplicado_pendiente_arqueo') $aplicadosSerie++;
                                    if ($estadoRow === 'anulado') $anuladosSerie++;
                                }
                            ?>
                            <div class="arqueo-serie-head mt-3">
                                <h6 class="mb-0 text-primary">Serie <?php echo html_escape($serie); ?></h6>
                                <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
                                    <span class="badge badge-light border">Registros: <?php echo count($items); ?></span>
                                    <span class="badge badge-success">Aplicados: <?php echo intval($aplicadosSerie); ?></span>
                                    <span class="badge badge-danger">Anulados: <?php echo intval($anuladosSerie); ?></span>
                                    <span class="badge badge-info">Subtotal $<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered table-striped arqueo-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Beneficiario</th>
                                            <th>Concepto</th>
                                            <th>Monto Sistema</th>
                                            <th>Monto Recibido</th>
                                            <th>Fecha Recepción</th>
                                            <th>Referencia</th>
                                            <th>Préstamo/Cuota</th>
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
                                                <td><?php echo html_escape(isset($it->fecha_recepcion) && !empty($it->fecha_recepcion) ? $it->fecha_recepcion : (isset($it->fecha) ? $it->fecha : '-')); ?></td>
                                                <td><?php echo html_escape(isset($it->beneficiario) ? $it->beneficiario : '-'); ?></td>
                                                <td><?php echo html_escape(isset($it->concepto) ? $it->concepto : '-'); ?></td>
                                                <td>$<?php echo number_format(isset($it->monto) ? floatval($it->monto) : 0, 2); ?></td>
                                                <td>$<?php echo number_format(isset($it->monto_recibido) ? floatval($it->monto_recibido) : 0, 2); ?></td>
                                                <td><?php echo html_escape(isset($it->fecha_recepcion) ? $it->fecha_recepcion : '-'); ?></td>
                                                <td><?php echo html_escape(isset($it->documento_numero) ? $it->documento_numero : '-'); ?></td>
                                                <td><?php echo html_escape((isset($it->idprestamo) ? 'P#' . $it->idprestamo : '-') . ' / ' . (isset($it->idcuota) ? 'C#' . $it->idcuota : '-')); ?></td>
                                                <td><span class="badge <?php echo $estadoClass; ?> arqueo-badge"><?php echo html_escape($estadoLabel); ?></span></td>
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
