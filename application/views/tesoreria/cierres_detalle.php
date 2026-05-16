<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1><i class="fas fa-receipt"></i> Detalle del Cierre</h1>
            <hr>
        </div>
    </div>

    <!-- Info del Cierre -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4 cierre-hero-card">
                <div class="card-header cierre-hero-head border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                        <h5 class="mb-0">Información General - CIERRE #<?php echo htmlspecialchars($cierre->consecutivo); ?></h5>
                        <span class="badge cierre-hero-badge">Cierre de Caja</span>
                    </div>
                </div>
                <div class="card-body cierre-hero-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="cierre-kpi-strong">
                                <span class="label">Monto Total del Cierre</span>
                                <span class="value">$<?php echo number_format($cierre->monto_total, 2); ?></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="cierre-kpi-box">
                                <span class="label">Fecha de Cierre</span>
                                <span class="value"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($cierre->fecha_cierre))); ?></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="cierre-kpi-box">
                                <span class="label">Ejecutado por</span>
                                <span class="value"><?php echo htmlspecialchars(isset($cierre->usuario_ejecutor) ? $cierre->usuario_ejecutor : (isset($cierre->idusuario) ? ('Usuario #' . intval($cierre->idusuario)) : 'N/A')); ?></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="cierre-kpi-box">
                                <span class="label">Estado</span>
                                <span class="value">
                                <?php
                                    $estado = strtoupper($cierre->estado);
                                    $badge_class = 'badge-light border text-dark';
                                    if ($estado === 'ABIERTO') {
                                        $badge_class = 'badge-light border text-dark';
                                    } elseif ($estado === 'ANULADO') {
                                        $badge_class = 'badge-light border text-dark';
                                    }
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="cierre-kpi-box">
                                <span class="label">Cantidad de Pagos</span>
                                <span class="value"><span class="badge badge-light border text-dark"><?php echo htmlspecialchars($cierre->cantidad_pagos); ?></span></span>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($cierre->observaciones)): ?>
                        <div class="row mt-1">
                            <div class="col-md-12">
                                <div class="cierre-observacion-box">
                                    <span class="label">Observaciones</span>
                                    <p class="mb-0"><?php echo htmlspecialchars($cierre->observaciones); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Pagos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Pagos Incluidos en este Cierre</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pagos)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> No hay pagos en este cierre.
                        </div>
                    <?php else: ?>
                        <?php
                            $normalizarMoneda = function ($raw) {
                                $m = strtoupper(trim((string)$raw));
                                if (in_array($m, array('NIO', 'NIO$', 'CS', 'C$', 'CRC', 'CORDOBA', 'CORDOBAS'))) {
                                    return 'NIO';
                                }
                                return 'USD';
                            };
                            $detectarSerie = function ($pago) {
                                $serie = '';
                                if (isset($pago->serie_codigo) && trim((string)$pago->serie_codigo) !== '') {
                                    $serie = strtoupper(trim((string)$pago->serie_codigo));
                                }
                                if ($serie === '' && isset($pago->documento_numero)) {
                                    $ref = trim((string)$pago->documento_numero);
                                    if ($ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $m)) {
                                        $serie = strtoupper($m[1]);
                                    }
                                }
                                return $serie !== '' ? $serie : 'SIN SERIE';
                            };

                            $gruposSerie = array();
                            $totalGeneralMonto = 0;
                            $totalGeneralRecibido = 0;
                            $totalGeneralUSD = 0;
                            $totalGeneralNIO = 0;
                            $totalGeneralTransf = 0;
                            $metodosCatalogo = array('efectivo', 'transferencia', 'cheque', 'tarjeta');
                            $totalesMetodoGlobal = array(
                                'efectivo' => 0.0,
                                'transferencia' => 0.0,
                                'cheque' => 0.0,
                                'tarjeta' => 0.0,
                                'otros' => 0.0
                            );

                            foreach ($pagos as $pagoTmp) {
                                $serieTmp = $detectarSerie($pagoTmp);
                                if (!isset($gruposSerie[$serieTmp])) {
                                    $gruposSerie[$serieTmp] = array(
                                        'items' => array(),
                                        'subtotal_monto' => 0,
                                        'subtotal_recibido' => 0,
                                        'subtotal_usd' => 0,
                                        'subtotal_nio' => 0,
                                        'transf_count' => 0,
                                        'transf_usd' => 0,
                                        'transf_nio' => 0,
                                        'metodos' => array(
                                            'efectivo' => 0.0,
                                            'transferencia' => 0.0,
                                            'cheque' => 0.0,
                                            'tarjeta' => 0.0,
                                            'otros' => 0.0
                                        )
                                    );
                                }

                                $montoTmp = isset($pagoTmp->monto) ? floatval($pagoTmp->monto) : 0;
                                $montoRecTmp = isset($pagoTmp->monto_recibido) && $pagoTmp->monto_recibido !== null ? floatval($pagoTmp->monto_recibido) : 0;
                                $monedaTmp = $normalizarMoneda(isset($pagoTmp->moneda) ? $pagoTmp->moneda : 'USD');
                                $medioTmp = isset($pagoTmp->medio_pago) ? strtolower(trim((string)$pagoTmp->medio_pago)) : '';
                                $esTransferTmp = (strpos($medioTmp, 'transfer') !== false);
                                $medioKey = in_array($medioTmp, $metodosCatalogo) ? $medioTmp : 'otros';

                                $gruposSerie[$serieTmp]['items'][] = $pagoTmp;
                                $gruposSerie[$serieTmp]['subtotal_monto'] += $montoTmp;
                                $gruposSerie[$serieTmp]['subtotal_recibido'] += $montoRecTmp;
                                if ($monedaTmp === 'NIO') {
                                    $gruposSerie[$serieTmp]['subtotal_nio'] += $montoRecTmp;
                                } else {
                                    $gruposSerie[$serieTmp]['subtotal_usd'] += $montoRecTmp;
                                }
                                if ($esTransferTmp) {
                                    $gruposSerie[$serieTmp]['transf_count']++;
                                    if ($monedaTmp === 'NIO') {
                                        $gruposSerie[$serieTmp]['transf_nio'] += $montoRecTmp;
                                    } else {
                                        $gruposSerie[$serieTmp]['transf_usd'] += $montoRecTmp;
                                    }
                                }
                                $gruposSerie[$serieTmp]['metodos'][$medioKey] += $montoRecTmp;
                                $totalesMetodoGlobal[$medioKey] += $montoRecTmp;

                                $totalGeneralMonto += $montoTmp;
                                $totalGeneralRecibido += $montoRecTmp;
                                if ($monedaTmp === 'NIO') {
                                    $totalGeneralNIO += $montoRecTmp;
                                } else {
                                    $totalGeneralUSD += $montoRecTmp;
                                }
                                if ($esTransferTmp) {
                                    $totalGeneralTransf++;
                                }
                            }
                            ksort($gruposSerie);
                        ?>

                        <?php foreach ($gruposSerie as $serie => $grupo): ?>
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                                <h6 class="mb-0 text-primary">Serie <?php echo htmlspecialchars($serie); ?></h6>
                                <div>
                                    <span class="badge badge-light border text-dark">Registros: <?php echo count($grupo['items']); ?></span>
                                    <span class="badge badge-light border text-dark">USD $<?php echo number_format($grupo['subtotal_usd'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">NIO C$<?php echo number_format($grupo['subtotal_nio'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Transferencias: <?php echo intval($grupo['transf_count']); ?> (USD $<?php echo number_format($grupo['transf_usd'], 2); ?> | NIO C$<?php echo number_format($grupo['transf_nio'], 2); ?>)</span>
                                    <span class="badge badge-light border text-dark">Efectivo: $<?php echo number_format($grupo['metodos']['efectivo'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Cheque: $<?php echo number_format($grupo['metodos']['cheque'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Tarjeta: $<?php echo number_format($grupo['metodos']['tarjeta'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Otros: $<?php echo number_format($grupo['metodos']['otros'], 2); ?></span>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente / Beneficiario</th>
                                            <th>Concepto</th>
                                            <th>No Serie Recibo</th>
                                            <th>Moneda</th>
                                            <th>Monto</th>
                                            <th>Monto Recibido</th>
                                            <th>NIO</th>
                                            <th>Transferencia</th>
                                            <th>Método</th>
                                            <th>Fecha Recepción</th>
                                            <th>Asiento Contable</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($grupo['items'] as $pago): ?>
                                            <?php
                                                $monto = isset($pago->monto) ? floatval($pago->monto) : 0;
                                                $monto_recibido = isset($pago->monto_recibido) && $pago->monto_recibido !== null ? floatval($pago->monto_recibido) : 0;
                                                $monedaFila = $normalizarMoneda(isset($pago->moneda) ? $pago->moneda : 'USD');
                                                $medioFila = isset($pago->medio_pago) ? strtolower(trim((string)$pago->medio_pago)) : '';
                                                $esTransferFila = (strpos($medioFila, 'transfer') !== false);
                                                $montoNioFila = ($monedaFila === 'NIO') ? $monto_recibido : 0;
                                                $montoTransferFila = $esTransferFila ? $monto_recibido : 0;
                                                $referenciaFila = '';
                                                if (isset($pago->documento_numero) && trim((string)$pago->documento_numero) !== '') {
                                                    $referenciaFila = trim((string)$pago->documento_numero);
                                                } elseif (isset($pago->referencia) && trim((string)$pago->referencia) !== '') {
                                                    $referenciaFila = trim((string)$pago->referencia);
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($pago->id); ?></td>
                                                <td>
                                                    <strong><?php echo isset($pago->beneficiario) ? htmlspecialchars($pago->beneficiario) : 'N/A'; ?></strong>
                                                </td>
                                                <td><?php echo isset($pago->concepto) ? htmlspecialchars($pago->concepto) : 'N/A'; ?></td>
                                                <td><?php echo $referenciaFila !== '' ? htmlspecialchars($referenciaFila) : ''; ?></td>
                                                <td><?php echo isset($pago->moneda) ? htmlspecialchars($pago->moneda) : 'USD'; ?></td>
                                                <td class="text-end"><strong>$<?php echo number_format($monto, 2); ?></strong></td>
                                                <td class="text-end"><strong>$<?php echo number_format($monto_recibido, 2); ?></strong></td>
                                                <td class="text-end"><?php echo $montoNioFila > 0 ? ('C$' . number_format($montoNioFila, 2)) : '-'; ?></td>
                                                <td class="text-end"><?php echo $montoTransferFila > 0 ? (($monedaFila === 'NIO' ? 'C$' : '$') . number_format($montoTransferFila, 2)) : '-'; ?></td>
                                                <td><?php echo isset($pago->medio_pago) ? htmlspecialchars($pago->medio_pago) : 'N/A'; ?></td>
                                                <td>
                                                    <?php
                                                        if (isset($pago->fecha_recepcion) && !empty($pago->fecha_recepcion)) {
                                                            echo htmlspecialchars(date('d/m/Y', strtotime($pago->fecha_recepcion)));
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <td>
                                                    <span class="badge badge-light border text-dark">APLICADO</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>SUBTOTAL SERIE <?php echo htmlspecialchars($serie); ?>:</strong></td>
                                            <td class="text-end"><strong class="text-primary">$<?php echo number_format($grupo['subtotal_monto'], 2); ?></strong></td>
                                            <td class="text-end"><strong class="text-success">$<?php echo number_format($grupo['subtotal_recibido'], 2); ?></strong></td>
                                            <td class="text-end"><strong><?php echo $grupo['subtotal_nio'] > 0 ? ('C$' . number_format($grupo['subtotal_nio'], 2)) : ''; ?></strong></td>
                                            <td class="text-end"><strong><?php
                                                $subtotalTransferTexto = '';
                                                if ($grupo['transf_usd'] > 0 && $grupo['transf_nio'] > 0) {
                                                    $subtotalTransferTexto = '$' . number_format($grupo['transf_usd'], 2) . ' | C$' . number_format($grupo['transf_nio'], 2);
                                                } elseif ($grupo['transf_usd'] > 0) {
                                                    $subtotalTransferTexto = '$' . number_format($grupo['transf_usd'], 2);
                                                } elseif ($grupo['transf_nio'] > 0) {
                                                    $subtotalTransferTexto = 'C$' . number_format($grupo['transf_nio'], 2);
                                                }
                                                echo $subtotalTransferTexto;
                                            ?></strong></td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endforeach; ?>

                        <div class="alert alert-light border mb-0">
                            <div class="d-flex flex-wrap" style="gap:10px;">
                                <span class="badge badge-light border text-dark">Total USD: $<?php echo number_format($totalGeneralUSD, 2); ?></span>
                                <span class="badge badge-light border text-dark">Total NIO: C$<?php echo number_format($totalGeneralNIO, 2); ?></span>
                                <span class="badge badge-light border text-dark">Transferencias: <?php echo intval($totalGeneralTransf); ?></span>
                                <span class="badge badge-light border text-dark">Total Sistema: $<?php echo number_format($totalGeneralMonto, 2); ?></span>
                                <span class="badge badge-light border text-dark">Total Recibido: $<?php echo number_format($totalGeneralRecibido, 2); ?></span>
                                <span class="badge badge-light border text-dark">Efectivo: $<?php echo number_format($totalesMetodoGlobal['efectivo'], 2); ?></span>
                                <span class="badge badge-light border text-dark">Transferencia: $<?php echo number_format($totalesMetodoGlobal['transferencia'], 2); ?></span>
                                <span class="badge badge-light border text-dark">Cheque: $<?php echo number_format($totalesMetodoGlobal['cheque'], 2); ?></span>
                                <span class="badge badge-light border text-dark">Tarjeta: $<?php echo number_format($totalesMetodoGlobal['tarjeta'], 2); ?></span>
                                <span class="badge badge-light border text-dark">Otros Métodos: $<?php echo number_format($totalesMetodoGlobal['otros'], 2); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-md-12">
            <a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Cierres
            </a>
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#modalArqueoCierre">
                                <i class="fas fa-cash-register"></i> Realizar Arqueo
                        </button>
            <button class="btn btn-primary" onclick="exportarPDF()">
                <i class="fas fa-print"></i> Imprimir Arqueo
            </button>
        </div>
    </div>
</div>

<?php
        $denUSD = array(1,5,10,20,50,100);
        $denNIO = array(1,5,10,20,50,100,200,500,1000);
        $arqueoExistente = (isset($arqueo_existente) && is_array($arqueo_existente)) ? $arqueo_existente : null;
        $mapDetUSD = array();
        $mapDetNIO = array();
        if ($arqueoExistente && !empty($arqueoExistente['detalles']) && is_array($arqueoExistente['detalles'])) {
                foreach ($arqueoExistente['detalles'] as $d) {
                        $mon = isset($d['moneda']) ? strtoupper(trim((string)$d['moneda'])) : '';
                        $den = isset($d['denominacion']) ? (string)(floatval($d['denominacion']) + 0) : '';
                        $cant = isset($d['cantidad']) ? intval($d['cantidad']) : 0;
                        if ($mon === 'USD') $mapDetUSD[$den] = $cant;
                        if ($mon === 'NIO') $mapDetNIO[$den] = $cant;
                }
        }
        $montoCierreUSD = isset($totalGeneralUSD) ? floatval($totalGeneralUSD) : 0;
        $montoCierreNIO = isset($totalGeneralNIO) ? floatval($totalGeneralNIO) : 0;
?>

<div class="modal fade" id="modalArqueoCierre" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Arqueo y Billetaje del Cierre #<?php echo htmlspecialchars($cierre->consecutivo); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Cierre USD</div>
                            <strong id="montoCierreUSD" data-value="<?php echo number_format($montoCierreUSD, 2, '.', ''); ?>">$<?php echo number_format($montoCierreUSD, 2); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Cierre NIO</div>
                            <strong id="montoCierreNIO" data-value="<?php echo number_format($montoCierreNIO, 2, '.', ''); ?>">C$<?php echo number_format($montoCierreNIO, 2); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Billetaje USD</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light"><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($denUSD as $d): $key = (string)($d+0); $cant = isset($mapDetUSD[$key]) ? intval($mapDetUSD[$key]) : 0; ?>
                                <tr>
                                    <td>$<?php echo number_format($d, 0); ?></td>
                                    <td><input type="number" min="0" step="1" class="form-control form-control-sm js-billetaje" data-moneda="USD" data-den="<?php echo number_format($d, 2, '.', ''); ?>" value="<?php echo $cant > 0 ? $cant : ''; ?>"></td>
                                    <td class="text-right js-subtotal-den" data-for="USD-<?php echo number_format($d, 2, '.', ''); ?>">$0.00</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Billetaje NIO</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light"><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($denNIO as $d): $key = (string)($d+0); $cant = isset($mapDetNIO[$key]) ? intval($mapDetNIO[$key]) : 0; ?>
                                <tr>
                                    <td>C$<?php echo number_format($d, 0); ?></td>
                                    <td><input type="number" min="0" step="1" class="form-control form-control-sm js-billetaje" data-moneda="NIO" data-den="<?php echo number_format($d, 2, '.', ''); ?>" value="<?php echo $cant > 0 ? $cant : ''; ?>"></td>
                                    <td class="text-right js-subtotal-den" data-for="NIO-<?php echo number_format($d, 2, '.', ''); ?>">C$0.00</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Subtotal Billetaje USD</div>
                            <strong id="subBilletajeUSD">$0.00</strong>
                            <div class="small mt-1">Diferencia USD: <strong id="difUSD">$0.00</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Subtotal Billetaje NIO</div>
                            <strong id="subBilletajeNIO">C$0.00</strong>
                            <div class="small mt-1">Diferencia NIO: <strong id="difNIO">C$0.00</strong></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>Comentario de diferencia (obligatorio si hay faltante/excedente)</label>
                    <textarea id="arqueo_comentario" class="form-control" rows="2"><?php echo $arqueoExistente && !empty($arqueoExistente['comentario_diferencia']) ? htmlspecialchars($arqueoExistente['comentario_diferencia']) : ''; ?></textarea>
                </div>

                <hr>
                <h6>Depósito a Banco</h6>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Estado</label>
                        <select id="arqueo_estado_deposito" class="form-control">
                            <option value="pendiente" <?php echo ($arqueoExistente && isset($arqueoExistente['estado_deposito']) && $arqueoExistente['estado_deposito'] === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="depositado" <?php echo ($arqueoExistente && isset($arqueoExistente['estado_deposito']) && $arqueoExistente['estado_deposito'] === 'depositado') ? 'selected' : ''; ?>>Depositado</option>
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label>Banco / Cuenta destino</label>
                        <select id="arqueo_idbanco" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php if (!empty($cuentas_banco)): foreach ($cuentas_banco as $cb): ?>
                                <option value="<?php echo intval($cb->id); ?>" <?php echo ($arqueoExistente && intval(isset($arqueoExistente['idbanco']) ? $arqueoExistente['idbanco'] : 0) === intval($cb->id)) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars((isset($cb->name)?$cb->name:'Cuenta') . ' - ' . (isset($cb->bank_name)?$cb->bank_name:'Banco') . ' (' . (isset($cb->currency)?$cb->currency:'') . ')'); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Monto depositado total</label>
                        <input type="number" min="0" step="0.01" class="form-control" id="arqueo_monto_deposito" value="<?php echo $arqueoExistente && isset($arqueoExistente['monto_depositado_total']) && $arqueoExistente['monto_depositado_total'] !== null ? htmlspecialchars($arqueoExistente['monto_depositado_total']) : ''; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Referencia minuta</label>
                        <input type="text" class="form-control" id="arqueo_referencia_minuta" value="<?php echo $arqueoExistente && !empty($arqueoExistente['referencia_minuta']) ? htmlspecialchars($arqueoExistente['referencia_minuta']) : ''; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Fecha depósito</label>
                        <input type="date" class="form-control" id="arqueo_fecha_deposito" value="<?php echo $arqueoExistente && !empty($arqueoExistente['fecha_deposito']) ? htmlspecialchars($arqueoExistente['fecha_deposito']) : date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardarArqueoCierre"><i class="fas fa-save"></i> Guardar Arqueo</button>
            </div>
        </div>
    </div>
</div>

<script>
    function exportarPDF() {
        const cierre_id = <?php echo htmlspecialchars($cierre->id); ?>;
        window.location.href = '<?php echo base_url('tesoreria/cierres_pdf?cierre_id='); ?>' + cierre_id;
    }

    (function(){
        function num(v){ var n = parseFloat(v); return isNaN(n) ? 0 : n; }
        function fmt(n, pref){ return pref + (num(n)).toLocaleString('en-US',{minimumFractionDigits:2, maximumFractionDigits:2}); }

        function recalcBilletaje(){
            var totalUSD = 0, totalNIO = 0;
            $('.js-billetaje').each(function(){
                var den = num($(this).data('den'));
                var mon = String($(this).data('moneda') || '').toUpperCase();
                var cant = Math.max(0, parseInt($(this).val() || '0', 10) || 0);
                var subtotal = den * cant;
                var key = mon + '-' + den.toFixed(2);
                $('.js-subtotal-den[data-for="'+key+'"]').text((mon === 'NIO' ? 'C$' : '$') + subtotal.toFixed(2));
                if (mon === 'NIO') totalNIO += subtotal; else totalUSD += subtotal;
            });

            var cierreUSD = num($('#montoCierreUSD').data('value'));
            var cierreNIO = num($('#montoCierreNIO').data('value'));
            var difUSD = totalUSD - cierreUSD;
            var difNIO = totalNIO - cierreNIO;

            $('#subBilletajeUSD').text(fmt(totalUSD, '$'));
            $('#subBilletajeNIO').text(fmt(totalNIO, 'C$'));
            $('#difUSD').text(fmt(difUSD, '$'));
            $('#difNIO').text(fmt(difNIO, 'C$'));
            $('#difUSD').toggleClass('text-danger', Math.abs(difUSD) > 0.009).toggleClass('text-success', Math.abs(difUSD) <= 0.009);
            $('#difNIO').toggleClass('text-danger', Math.abs(difNIO) > 0.009).toggleClass('text-success', Math.abs(difNIO) <= 0.009);
        }

        $(document).on('input change', '.js-billetaje', recalcBilletaje);
        $('#modalArqueoCierre').on('shown.bs.modal', recalcBilletaje);

        $('#btnGuardarArqueoCierre').on('click', function(){
            var cierreId = <?php echo intval($cierre->id); ?>;
            var estadoDep = String($('#arqueo_estado_deposito').val() || 'pendiente');
            var comentario = $.trim($('#arqueo_comentario').val() || '');
            var difUSD = num($('#difUSD').text().replace(/[^0-9\.-]/g,''));
            var difNIO = num($('#difNIO').text().replace(/[^0-9\.-]/g,''));

            if ((Math.abs(difUSD) > 0.009 || Math.abs(difNIO) > 0.009) && comentario === '') {
                alert('Debe ingresar comentario porque hay faltante o excedente en arqueo.');
                return;
            }

            var usd = [], nio = [];
            $('.js-billetaje[data-moneda="USD"]').each(function(){
                usd.push({ denominacion: num($(this).data('den')), cantidad: Math.max(0, parseInt($(this).val() || '0', 10) || 0) });
            });
            $('.js-billetaje[data-moneda="NIO"]').each(function(){
                nio.push({ denominacion: num($(this).data('den')), cantidad: Math.max(0, parseInt($(this).val() || '0', 10) || 0) });
            });

            var payload = {
                cierre_id: cierreId,
                billetaje_usd: JSON.stringify(usd),
                billetaje_nio: JSON.stringify(nio),
                comentario_diferencia: comentario,
                estado_deposito: estadoDep,
                idbanco: $('#arqueo_idbanco').val(),
                monto_depositado_total: $('#arqueo_monto_deposito').val(),
                referencia_minuta: $('#arqueo_referencia_minuta').val(),
                fecha_deposito: $('#arqueo_fecha_deposito').val()
            };

            var $btn = $(this);
            $btn.prop('disabled', true).text('Guardando...');
            $.post('<?php echo base_url('tesoreria/save_cierre_arqueo_ajax'); ?>', payload, function(resp){
                if (resp && resp.status) {
                    alert(resp.message || 'Arqueo guardado');
                    window.location.reload();
                    return;
                }
                alert(resp && resp.message ? resp.message : 'No se pudo guardar el arqueo');
            }, 'json').fail(function(){
                alert('Error de conexión al guardar arqueo');
            }).always(function(){
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Arqueo');
            });
        });
    })();
</script>

<style>
    .cierre-hero-card {
        border: 1px solid #e4e8ef;
    }
    .cierre-hero-head {
        background: #f7f9fc;
    }
    .cierre-hero-badge {
        background: #eef2f7;
        color: #405166;
        border: 1px solid #d8dee8;
        font-size: .78rem;
        font-weight: 600;
        padding: .35rem .65rem;
    }
    .cierre-kpi-strong {
        border: 1px solid #d9e2ef;
        background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .cierre-kpi-strong .label {
        color: #5b6b80;
        font-size: .9rem;
        font-weight: 600;
    }
    .cierre-kpi-strong .value {
        color: #1f2d3d;
        font-size: 1.45rem;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .cierre-kpi-box {
        border: 1px solid #e2e7f0;
        border-radius: 10px;
        background: #fff;
        min-height: 94px;
        padding: 12px 14px;
    }
    .cierre-kpi-box .label {
        display: block;
        color: #6b7788;
        font-size: .78rem;
        font-weight: 600;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .cierre-kpi-box .value {
        color: #1f2d3d;
        font-size: 1rem;
        font-weight: 600;
    }
    .cierre-observacion-box {
        border: 1px solid #e2e7f0;
        border-radius: 10px;
        background: #fff;
        padding: 12px 14px;
    }
    .cierre-observacion-box .label {
        display: block;
        color: #6b7788;
        font-size: .78rem;
        font-weight: 600;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
    .text-end {
        text-align: right;
    }
</style>
        </div>
    </div>
</div>
