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

                            $arqueosSeriesExistentes = (isset($arqueos_series_existentes) && is_array($arqueos_series_existentes)) ? $arqueos_series_existentes : array();
                            $seriesRequeridasArqueo = (isset($series_requeridas_arqueo) && is_array($series_requeridas_arqueo)) ? $series_requeridas_arqueo : array();
                            $seriesPendientesArqueo = (isset($series_pendientes_arqueo) && is_array($series_pendientes_arqueo)) ? $series_pendientes_arqueo : array();
                            $depositosPendientesCierre = (isset($depositos_pendientes_cierre) && is_array($depositos_pendientes_cierre)) ? $depositos_pendientes_cierre : array();
                            $allSeriesArqueadas = empty($seriesPendientesArqueo);
                            $arqueoGeneralGuardado = isset($arqueo_existente) && is_array($arqueo_existente) && !empty($arqueo_existente);
                            $depositosYaEnviados = count($depositosPendientesCierre) > 0;

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

                            $sumArqSeriesUSD = 0.0;
                            $sumArqSeriesNIO = 0.0;
                            foreach ($arqueosSeriesExistentes as $arqSerieTmp) {
                                $sumArqSeriesUSD += isset($arqSerieTmp['total_billetaje_usd']) ? floatval($arqSerieTmp['total_billetaje_usd']) : 0;
                                $sumArqSeriesNIO += isset($arqSerieTmp['total_billetaje_nio']) ? floatval($arqSerieTmp['total_billetaje_nio']) : 0;
                            }
                        ?>

                        <div class="alert alert-light border mb-3">
                            <div class="d-flex flex-wrap" style="gap:10px;">
                                <span class="badge <?php echo $allSeriesArqueadas ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $allSeriesArqueadas ? 'Todas las series arqueadas' : ('Series pendientes: ' . implode(', ', $seriesPendientesArqueo)); ?>
                                </span>
                                <span class="badge badge-light border text-dark">Series arqueadas: <?php echo number_format(count($arqueosSeriesExistentes), 0); ?></span>
                                <span class="badge badge-light border text-dark">Series requeridas: <?php echo number_format(count($seriesRequeridasArqueo), 0); ?></span>
                                <span class="badge badge-light border text-dark">Suma Arqueos Serie USD: $<strong id="sumArqueoSerieUSD"><?php echo number_format($sumArqSeriesUSD, 2); ?></strong></span>
                                <span class="badge badge-light border text-dark">Suma Arqueos Serie NIO: C$<strong id="sumArqueoSerieNIO"><?php echo number_format($sumArqSeriesNIO, 2); ?></strong></span>
                                <span class="badge badge-light border text-dark">Total Cierre USD: $<?php echo number_format($totalGeneralUSD, 2); ?></span>
                                <span class="badge badge-light border text-dark">Total Cierre NIO: C$<?php echo number_format($totalGeneralNIO, 2); ?></span>
                            </div>
                        </div>

                        <?php foreach ($gruposSerie as $serie => $grupo): ?>
                            <?php
                                $refsSerie = array();
                                $lenRefSerie = 10;
                                foreach ($grupo['items'] as $pagoRefTmp) {
                                    $refTmp = isset($pagoRefTmp->documento_numero) ? trim((string)$pagoRefTmp->documento_numero) : '';
                                    if ($refTmp !== '' && preg_match('/^' . preg_quote((string)$serie, '/') . '(\d+)$/i', $refTmp, $mRefTmp)) {
                                        $refsSerie[] = intval($mRefTmp[1]);
                                        $lenRefSerie = max($lenRefSerie, strlen($mRefTmp[1]));
                                    }
                                }
                                $refDesdeSerie = 'N/D';
                                $refHastaSerie = 'N/D';
                                if (!empty($refsSerie)) {
                                    sort($refsSerie, SORT_NUMERIC);
                                    $refDesdeSerie = $serie . str_pad((string)$refsSerie[0], $lenRefSerie, '0', STR_PAD_LEFT);
                                    $refHastaSerie = $serie . str_pad((string)$refsSerie[count($refsSerie)-1], $lenRefSerie, '0', STR_PAD_LEFT);
                                }
                                $arqSerieActual = isset($arqueosSeriesExistentes[$serie]) ? $arqueosSeriesExistentes[$serie] : null;
                                $serieTienePagos = count($grupo['items']) > 0;
                            ?>
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                                <h6 class="mb-0 text-primary">Serie <?php echo htmlspecialchars($serie); ?></h6>
                                <div>
                                    <span class="badge badge-light border text-dark">Registros: <?php echo count($grupo['items']); ?></span>
                                    <span class="badge badge-light border text-dark">Rango: <?php echo htmlspecialchars($refDesdeSerie); ?> a <?php echo htmlspecialchars($refHastaSerie); ?></span>
                                    <span class="badge badge-light border text-dark">USD $<?php echo number_format($grupo['subtotal_usd'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">NIO C$<?php echo number_format($grupo['subtotal_nio'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Transferencias: <?php echo intval($grupo['transf_count']); ?> (USD $<?php echo number_format($grupo['transf_usd'], 2); ?> | NIO C$<?php echo number_format($grupo['transf_nio'], 2); ?>)</span>
                                    <span class="badge badge-light border text-dark">Efectivo: $<?php echo number_format($grupo['metodos']['efectivo'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Cheque: $<?php echo number_format($grupo['metodos']['cheque'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Tarjeta: $<?php echo number_format($grupo['metodos']['tarjeta'], 2); ?></span>
                                    <span class="badge badge-light border text-dark">Otros: $<?php echo number_format($grupo['metodos']['otros'], 2); ?></span>
                                    <?php if ($arqSerieActual): ?>
                                        <span class="badge badge-success">Arqueo Serie Guardado</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pendiente Arqueo Serie</span>
                                    <?php endif; ?>
                                    <?php if ($serieTienePagos): ?>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-success js-btn-arqueo-serie"
                                            data-serie="<?php echo htmlspecialchars($serie); ?>"
                                            data-total-usd="<?php echo number_format($grupo['subtotal_usd'], 2, '.', ''); ?>"
                                            data-total-nio="<?php echo number_format($grupo['subtotal_nio'], 2, '.', ''); ?>">
                                            <i class="fas fa-cash-register"></i> Arqueo Serie <?php echo htmlspecialchars($serie); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Beneficiario</th>
                                            <th>Registrado Por</th>
                                            <th>Número de Préstamo</th>
                                            <th>Cuota</th>
                                            <th>Método</th>
                                            <th>Moneda</th>
                                            <th>Monto</th>
                                            <th>Referencia</th>
                                            <th>Monto a Aplicar</th>
                                            <th>Fecha Recepción</th>
                                            <th>Recibo Revisado</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($grupo['items'])): ?>
                                            <tr><td colspan="14" class="text-center text-muted">Sin pagos para esta serie.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($grupo['items'] as $i => $pago): ?>
                                            <?php
                                                $monto = isset($pago->monto) ? floatval($pago->monto) : 0;
                                                $monto_recibido = isset($pago->monto_recibido) && $pago->monto_recibido !== null ? floatval($pago->monto_recibido) : 0;
                                                $monedaFila = $normalizarMoneda(isset($pago->moneda) ? $pago->moneda : 'USD');
                                                $simboloFila = ($monedaFila === 'NIO') ? 'C$' : '$';
                                                $medioFila = isset($pago->medio_pago) ? ucfirst(strtolower(trim((string)$pago->medio_pago))) : '-';
                                                $fechaFila = isset($pago->fecha) ? $pago->fecha : (isset($pago->fecha_programada) ? $pago->fecha_programada : '-');
                                                $referenciaFila = '';
                                                if (isset($pago->documento_numero) && trim((string)$pago->documento_numero) !== '') {
                                                    $referenciaFila = trim((string)$pago->documento_numero);
                                                } elseif (isset($pago->referencia) && trim((string)$pago->referencia) !== '') {
                                                    $referenciaFila = trim((string)$pago->referencia);
                                                }

                                                $prestamoNum = isset($pago->idprestamo) ? intval($pago->idprestamo) : 0;
                                                $cuotaNum = isset($pago->idcuota) ? intval($pago->idcuota) : 0;
                                                $conceptoParse = isset($pago->concepto) ? (string)$pago->concepto : '';
                                                if (($prestamoNum <= 0 || $cuotaNum <= 0) && preg_match('/prestamo\s*#?\s*(\d+).*?cuota\s*#?\s*(\d+)/i', $conceptoParse, $mc)) {
                                                    if ($prestamoNum <= 0) $prestamoNum = intval($mc[1]);
                                                    if ($cuotaNum <= 0) $cuotaNum = intval($mc[2]);
                                                }

                                                $reciboRevisado = isset($pago->recibo_revisado) && intval($pago->recibo_revisado) === 1;
                                                $estadoRaw = isset($pago->estado) ? strtolower(trim((string)$pago->estado)) : '';
                                                $isPendiente = in_array($estadoRaw, array('registrado','programado','pendiente'));
                                                $isAnulado = ($estadoRaw === 'anulado');
                                                $isAplicado = in_array($estadoRaw, array('aplicado_pendiente_arqueo','aplicado','cerrado'));
                                                $estadoLabel = $isPendiente ? 'Pendiente' : ($isAnulado ? 'Anulado' : ($isAplicado ? 'Aplicado' : ucfirst($estadoRaw)));
                                                $estadoClass = $isPendiente ? 'badge-warning' : ($isAnulado ? 'badge-danger' : 'badge-success');
                                            ?>
                                            <tr>
                                                <td><?php echo isset($i) ? (intval($i) + 1) : '-'; ?></td>
                                                <td><?php echo htmlspecialchars($fechaFila); ?></td>
                                                <td><?php echo isset($pago->beneficiario) ? htmlspecialchars($pago->beneficiario) : '-'; ?></td>
                                                <td><?php echo isset($pago->registrado_por) ? htmlspecialchars($pago->registrado_por) : '-'; ?></td>
                                                <td><?php echo $prestamoNum > 0 ? ('#' . intval($prestamoNum)) : '-'; ?></td>
                                                <td><?php echo $cuotaNum > 0 ? ('#' . intval($cuotaNum)) : '-'; ?></td>
                                                <td><?php echo htmlspecialchars($medioFila); ?></td>
                                                <td><?php echo htmlspecialchars($monedaFila); ?></td>
                                                <td><?php echo $simboloFila; ?><?php echo number_format($monto, 2); ?></td>
                                                <td><?php echo $referenciaFila !== '' ? htmlspecialchars($referenciaFila) : '-'; ?></td>
                                                <td><?php echo $simboloFila; ?><?php echo number_format($monto_recibido, 2); ?></td>
                                                <td>
                                                    <?php
                                                        if (isset($pago->fecha_recepcion) && !empty($pago->fecha_recepcion)) {
                                                            echo htmlspecialchars(date('d/m/Y', strtotime($pago->fecha_recepcion)));
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $reciboRevisado ? 'badge-success' : 'badge-secondary'; ?>">
                                                        <?php echo $reciboRevisado ? 'Revisado' : 'No'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $estadoClass; ?>"><?php echo htmlspecialchars($estadoLabel); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="8" class="text-end"><strong>SUBTOTAL SERIE <?php echo htmlspecialchars($serie); ?>:</strong></td>
                                            <td><strong><?php echo '$' . number_format($grupo['subtotal_monto'], 2); ?></strong></td>
                                            <td></td>
                                            <td><strong><?php echo '$' . number_format($grupo['subtotal_recibido'], 2); ?></strong></td>
                                            <td colspan="3"></td>
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
                        <button id="btnOpenArqueoGeneral" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalArqueoCierre" <?php echo $allSeriesArqueadas ? '' : 'disabled'; ?> title="<?php echo $allSeriesArqueadas ? 'Abrir arqueo general' : 'Complete primero todos los arqueos por serie'; ?>">
                                <i class="fas fa-cash-register"></i> Realizar Arqueo
                        </button>
            <button id="btnEnviarADepositar" class="btn btn-warning" type="button" <?php echo ($arqueoGeneralGuardado && !$depositosYaEnviados) ? '' : 'disabled'; ?> title="<?php echo !$arqueoGeneralGuardado ? 'Guarde primero el arqueo general' : ($depositosYaEnviados ? 'Este cierre ya fue enviado a depositar' : 'Generar depósitos pendientes USD/NIO'); ?>">
                <i class="fas fa-university"></i> Enviar a Depositar
            </button>
            <?php if ($depositosYaEnviados): ?>
                <span class="badge badge-info">Depósitos generados: <?php echo intval(count($depositosPendientesCierre)); ?></span>
            <?php endif; ?>
            <?php if (!$allSeriesArqueadas): ?>
                <span class="badge badge-warning">Primero complete arqueos por serie: <?php echo htmlspecialchars(implode(', ', $seriesPendientesArqueo)); ?></span>
            <?php endif; ?>
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
    $arqueosSeriesExistentes = (isset($arqueos_series_existentes) && is_array($arqueos_series_existentes)) ? $arqueos_series_existentes : array();
        $mapDetUSD = array();
        $mapDetNIO = array();
    $mapArqueosSeries = array();
        if ($arqueoExistente && !empty($arqueoExistente['detalles']) && is_array($arqueoExistente['detalles'])) {
                foreach ($arqueoExistente['detalles'] as $d) {
                        $mon = isset($d['moneda']) ? strtoupper(trim((string)$d['moneda'])) : '';
                        $den = isset($d['denominacion']) ? (string)(floatval($d['denominacion']) + 0) : '';
                        $cant = isset($d['cantidad']) ? intval($d['cantidad']) : 0;
                        if ($mon === 'USD') $mapDetUSD[$den] = $cant;
                        if ($mon === 'NIO') $mapDetNIO[$den] = $cant;
                }
        }
        foreach ($arqueosSeriesExistentes as $serieArqKey => $serieArqData) {
                $detUsdTmp = array();
                $detNioTmp = array();
                if (!empty($serieArqData['detalles']) && is_array($serieArqData['detalles'])) {
                        foreach ($serieArqData['detalles'] as $dtmp) {
                                $mon = isset($dtmp['moneda']) ? strtoupper(trim((string)$dtmp['moneda'])) : '';
                                $den = isset($dtmp['denominacion']) ? (string)(floatval($dtmp['denominacion']) + 0) : '';
                                $cant = isset($dtmp['cantidad']) ? intval($dtmp['cantidad']) : 0;
                                if ($mon === 'USD') $detUsdTmp[$den] = $cant;
                                if ($mon === 'NIO') $detNioTmp[$den] = $cant;
                        }
                }
                $mapArqueosSeries[$serieArqKey] = array(
                        'total_usd' => isset($serieArqData['monto_cierre_usd']) ? floatval($serieArqData['monto_cierre_usd']) : 0,
                        'total_nio' => isset($serieArqData['monto_cierre_nio']) ? floatval($serieArqData['monto_cierre_nio']) : 0,
                        'comentario' => isset($serieArqData['comentario_diferencia']) ? (string)$serieArqData['comentario_diferencia'] : '',
                    'edit_autorizado_por' => isset($serieArqData['edit_autorizado_por']) ? (string)$serieArqData['edit_autorizado_por'] : '',
                    'edit_comentario' => isset($serieArqData['edit_comentario']) ? (string)$serieArqData['edit_comentario'] : '',
                    'edit_count' => isset($serieArqData['edit_count']) ? intval($serieArqData['edit_count']) : 0,
                        'det_usd' => $detUsdTmp,
                        'det_nio' => $detNioTmp,
                        'billetaje_usd' => isset($serieArqData['total_billetaje_usd']) ? floatval($serieArqData['total_billetaje_usd']) : 0,
                        'billetaje_nio' => isset($serieArqData['total_billetaje_nio']) ? floatval($serieArqData['total_billetaje_nio']) : 0
                );
        }
        $montoCierreUSD = isset($totalGeneralUSD) ? floatval($totalGeneralUSD) : 0;
        $montoCierreNIO = isset($totalGeneralNIO) ? floatval($totalGeneralNIO) : 0;
?>

<div class="modal fade" id="modalArqueoSerie" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Arqueo por Serie <span id="arqueoSerieCodigo">-</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="arqueo_serie_codigo" value="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Serie USD</div>
                            <strong id="montoCierreSerieUSD" data-value="0">$0.00</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Serie NIO</div>
                            <strong id="montoCierreSerieNIO" data-value="0">C$0.00</strong>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Billetaje USD</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light"><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($denUSD as $d): ?>
                                <tr>
                                    <td>$<?php echo number_format($d, 0); ?></td>
                                    <td><input type="number" min="0" step="1" class="form-control form-control-sm js-billetaje-serie" data-moneda="USD" data-den="<?php echo number_format($d, 2, '.', ''); ?>" value=""></td>
                                    <td class="text-right js-subtotal-den-serie" data-for="USD-<?php echo number_format($d, 2, '.', ''); ?>">$0.00</td>
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
                                <?php foreach ($denNIO as $d): ?>
                                <tr>
                                    <td>C$<?php echo number_format($d, 0); ?></td>
                                    <td><input type="number" min="0" step="1" class="form-control form-control-sm js-billetaje-serie" data-moneda="NIO" data-den="<?php echo number_format($d, 2, '.', ''); ?>" value=""></td>
                                    <td class="text-right js-subtotal-den-serie" data-for="NIO-<?php echo number_format($d, 2, '.', ''); ?>">C$0.00</td>
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
                            <strong id="subBilletajeSerieUSD">$0.00</strong>
                            <div class="small mt-1">Diferencia USD: <strong id="difSerieUSD">$0.00</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Subtotal Billetaje NIO</div>
                            <strong id="subBilletajeSerieNIO">C$0.00</strong>
                            <div class="small mt-1">Diferencia NIO: <strong id="difSerieNIO">C$0.00</strong></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3 mb-0">
                    <label>Comentario de diferencia (obligatorio si hay faltante/excedente)</label>
                    <textarea id="arqueo_serie_comentario" class="form-control" rows="2"></textarea>
                </div>

                <div id="serieEditGate" class="alert alert-warning mt-3 d-none mb-0">
                    Este arqueo de serie ya fue registrado. Puede visualizarlo, pero para editar debe registrar autorización.
                    <div id="serieEditHistory" class="mt-2 d-none">
                        <div><strong>Autorizó:</strong> <span id="serieEditHistoryAutoriza">-</span></div>
                        <div><strong>Comentario de edición:</strong> <span id="serieEditHistoryComentario">-</span></div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-dark" id="btnHabilitarEdicionSerie">Solicitar Edición</button>
                    </div>
                    <div id="serieEditAuthWrap" class="mt-2 d-none">
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label>Quién autoriza</label>
                                <input type="text" class="form-control" id="arqueo_serie_edit_autorizado_por" placeholder="Nombre de quien autoriza">
                            </div>
                            <div class="form-group col-md-7 mb-0">
                                <label>Comentario de autorización (obligatorio)</label>
                                <input type="text" class="form-control" id="arqueo_serie_edit_comentario" placeholder="Motivo de la edición autorizada">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardarArqueoSerie"><i class="fas fa-save"></i> Guardar Arqueo Serie</button>
            </div>
        </div>
    </div>
</div>

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

                <div class="alert alert-info mt-3 mb-0">
                    Después de guardar el arqueo general, use <strong>Enviar a Depositar</strong>. El sistema generará automáticamente dos documentos pendientes, uno para USD y otro para NIO, en <strong>Integración Bancaria</strong>.
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
        var arqueosSerieMap = <?php echo json_encode($mapArqueosSeries); ?>;
        var serieEditEnabled = false;

        function setSerieReadOnlyMode(readOnly){
            $('.js-billetaje-serie').prop('disabled', readOnly);
            $('#arqueo_serie_comentario').prop('readonly', readOnly);
            if (readOnly) {
                $('#btnGuardarArqueoSerie').addClass('d-none');
                $('#serieEditGate').removeClass('d-none');
            } else {
                $('#btnGuardarArqueoSerie').removeClass('d-none').html('<i class="fas fa-save"></i> Guardar Arqueo Serie');
                $('#serieEditGate').addClass('d-none');
            }
        }

        function setSerieInputCount(mon, den, cantidad){
            var selector = '.js-billetaje-serie[data-moneda="' + mon + '"][data-den="' + den.toFixed(2) + '"]';
            var $el = $(selector);
            if (!$el.length) return;
            $el.val(cantidad > 0 ? cantidad : '');
        }

        function recalcBilletajeSerie(){
            var totalUSD = 0, totalNIO = 0;
            $('.js-billetaje-serie').each(function(){
                var den = num($(this).data('den'));
                var mon = String($(this).data('moneda') || '').toUpperCase();
                var cant = Math.max(0, parseInt($(this).val() || '0', 10) || 0);
                var subtotal = den * cant;
                var key = mon + '-' + den.toFixed(2);
                $('.js-subtotal-den-serie[data-for="'+key+'"]').text((mon === 'NIO' ? 'C$' : '$') + subtotal.toFixed(2));
                if (mon === 'NIO') totalNIO += subtotal; else totalUSD += subtotal;
            });

            var cierreUSD = num($('#montoCierreSerieUSD').data('value'));
            var cierreNIO = num($('#montoCierreSerieNIO').data('value'));
            var difUSD = totalUSD - cierreUSD;
            var difNIO = totalNIO - cierreNIO;

            $('#subBilletajeSerieUSD').text(fmt(totalUSD, '$'));
            $('#subBilletajeSerieNIO').text(fmt(totalNIO, 'C$'));
            $('#difSerieUSD').text(fmt(difUSD, '$'));
            $('#difSerieNIO').text(fmt(difNIO, 'C$'));
            $('#difSerieUSD').toggleClass('text-danger', Math.abs(difUSD) > 0.009).toggleClass('text-success', Math.abs(difUSD) <= 0.009);
            $('#difSerieNIO').toggleClass('text-danger', Math.abs(difNIO) > 0.009).toggleClass('text-success', Math.abs(difNIO) <= 0.009);
        }

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

        $(document).on('click', '.js-btn-arqueo-serie', function(){
            var serie = String($(this).data('serie') || '').toUpperCase();
            var totalUsd = num($(this).data('total-usd'));
            var totalNio = num($(this).data('total-nio'));
            var existe = !!(arqueosSerieMap && arqueosSerieMap[serie]);
            serieEditEnabled = false;

            $('#arqueo_serie_codigo').val(serie);
            $('#arqueoSerieCodigo').text(serie);
            $('#montoCierreSerieUSD').data('value', totalUsd).text(fmt(totalUsd, '$'));
            $('#montoCierreSerieNIO').data('value', totalNio).text(fmt(totalNio, 'C$'));

            $('.js-billetaje-serie').val('');
            $('#arqueo_serie_comentario').val('');
            $('#arqueo_serie_edit_autorizado_por').val('');
            $('#arqueo_serie_edit_comentario').val('');
            $('#serieEditAuthWrap').addClass('d-none');
            $('#serieEditHistory').addClass('d-none');
            $('#serieEditHistoryAutoriza').text('-');
            $('#serieEditHistoryComentario').text('-');

            if (existe) {
                var ex = arqueosSerieMap[serie];
                $('#arqueo_serie_comentario').val(ex.comentario || '');
                $('#arqueo_serie_edit_autorizado_por').val(ex.edit_autorizado_por || '');
                $('#arqueo_serie_edit_comentario').val(ex.edit_comentario || '');
                if ((ex.edit_autorizado_por || '') !== '' || (ex.edit_comentario || '') !== '') {
                    $('#serieEditHistory').removeClass('d-none');
                    $('#serieEditHistoryAutoriza').text(ex.edit_autorizado_por || '-');
                    $('#serieEditHistoryComentario').text(ex.edit_comentario || '-');
                }

                if (ex.det_usd) {
                    Object.keys(ex.det_usd).forEach(function(den){
                        setSerieInputCount('USD', parseFloat(den), parseInt(ex.det_usd[den] || 0, 10));
                    });
                }
                if (ex.det_nio) {
                    Object.keys(ex.det_nio).forEach(function(den){
                        setSerieInputCount('NIO', parseFloat(den), parseInt(ex.det_nio[den] || 0, 10));
                    });
                }
            }

            setSerieReadOnlyMode(existe);
            recalcBilletajeSerie();
            $('#modalArqueoSerie').modal('show');
        });

        $('#btnHabilitarEdicionSerie').on('click', function(){
            serieEditEnabled = true;
            $('#serieEditAuthWrap').removeClass('d-none');
            $('.js-billetaje-serie').prop('disabled', false);
            $('#arqueo_serie_comentario').prop('readonly', false);
            $('#btnGuardarArqueoSerie').removeClass('d-none').html('<i class="fas fa-save"></i> Guardar Edición Arqueo Serie');
        });

        $(document).on('input change', '.js-billetaje-serie', recalcBilletajeSerie);

        $('#btnGuardarArqueoSerie').on('click', function(){
            var cierreId = <?php echo intval($cierre->id); ?>;
            var serieCodigo = String($('#arqueo_serie_codigo').val() || '').toUpperCase();
            var comentario = $.trim($('#arqueo_serie_comentario').val() || '');
            var difUSD = num($('#difSerieUSD').text().replace(/[^0-9\.-]/g,''));
            var difNIO = num($('#difSerieNIO').text().replace(/[^0-9\.-]/g,''));
            var yaExiste = !!(arqueosSerieMap && arqueosSerieMap[serieCodigo]);
            var editAutorizadoPor = $.trim($('#arqueo_serie_edit_autorizado_por').val() || '');
            var editComentario = $.trim($('#arqueo_serie_edit_comentario').val() || '');

            if (!serieCodigo) {
                alert('Serie inválida para arqueo.');
                return;
            }

            if (yaExiste && !serieEditEnabled) {
                alert('Este arqueo de serie está en modo solo lectura. Use "Solicitar Edición" para habilitar cambios.');
                return;
            }

            if ((Math.abs(difUSD) > 0.009 || Math.abs(difNIO) > 0.009) && comentario === '') {
                alert('Debe ingresar comentario porque hay faltante o excedente en arqueo de serie.');
                return;
            }

            if (yaExiste && (editAutorizadoPor === '' || editComentario === '')) {
                alert('Para editar un arqueo ya registrado debe indicar quién autoriza y comentario obligatorio.');
                return;
            }

            var usd = [], nio = [];
            $('.js-billetaje-serie[data-moneda="USD"]').each(function(){
                usd.push({ denominacion: num($(this).data('den')), cantidad: Math.max(0, parseInt($(this).val() || '0', 10) || 0) });
            });
            $('.js-billetaje-serie[data-moneda="NIO"]').each(function(){
                nio.push({ denominacion: num($(this).data('den')), cantidad: Math.max(0, parseInt($(this).val() || '0', 10) || 0) });
            });

            var payload = {
                cierre_id: cierreId,
                serie_codigo: serieCodigo,
                billetaje_usd: JSON.stringify(usd),
                billetaje_nio: JSON.stringify(nio),
                comentario_diferencia: comentario,
                edit_autorizado_por: editAutorizadoPor,
                edit_comentario: editComentario
            };

            var $btn = $(this);
            $btn.prop('disabled', true).text('Guardando...');
            $.post('<?php echo base_url('tesoreria/save_cierre_arqueo_serie_ajax'); ?>', payload, function(resp){
                if (resp && resp.status) {
                    alert(resp.message || 'Arqueo de serie guardado');
                    window.location.reload();
                    return;
                }
                alert(resp && resp.message ? resp.message : 'No se pudo guardar el arqueo por serie');
            }, 'json').fail(function(){
                alert('Error de conexión al guardar arqueo por serie');
            }).always(function(){
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Arqueo Serie');
            });
        });

        $('#btnEnviarADepositar').on('click', function(){
            var cierreId = <?php echo intval($cierre->id); ?>;
            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }
            $btn.prop('disabled', true).text('Enviando...');
            $.post('<?php echo base_url('tesoreria/enviar_cierre_a_depositar_ajax'); ?>', { cierre_id: cierreId }, function(resp){
                if (resp && resp.status) {
                    alert(resp.message || 'Depósitos enviados a Integración Bancaria');
                    window.location.reload();
                    return;
                }
                alert(resp && resp.message ? resp.message : 'No se pudo enviar a depositar');
            }, 'json').fail(function(){
                alert('Error de conexión al generar depósitos pendientes');
            }).always(function(){
                $btn.prop('disabled', false).html('<i class="fas fa-university"></i> Enviar a Depositar');
            });
        });

        $(document).on('input change', '.js-billetaje', recalcBilletaje);
        $('#modalArqueoCierre').on('shown.bs.modal', recalcBilletaje);

        $('#btnGuardarArqueoCierre').on('click', function(){
            var cierreId = <?php echo intval($cierre->id); ?>;
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
                comentario_diferencia: comentario
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
