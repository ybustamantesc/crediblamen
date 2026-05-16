<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <style>
                .teso-pay-card {
                    border: 1px solid #e3ebf7;
                    border-radius: 12px;
                    box-shadow: 0 6px 16px rgba(2, 48, 71, .05);
                }
                .teso-pay-toolbar {
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    background: #f8fbff;
                    padding: 10px 12px;
                    margin-bottom: 12px;
                }
                .teso-pay-badge {
                    font-size: .72rem;
                    letter-spacing: .3px;
                    padding: .36em .6em;
                    border-radius: .45rem;
                }
                .teso-pay-kpi {
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    background: #f8fbff;
                    padding: 10px 12px;
                    height: 100%;
                }
                .teso-pay-kpi .label {
                    font-size: .78rem;
                    color: #54739a;
                    text-transform: uppercase;
                    letter-spacing: .4px;
                    font-weight: 700;
                    margin-bottom: 3px;
                }
                .teso-pay-kpi .value {
                    font-size: 1.05rem;
                    color: #13315c;
                    font-weight: 700;
                }
                .teso-pay-actions .btn {
                    border-radius: 8px;
                    margin-bottom: 4px;
                    font-weight: 600;
                    font-size: .77rem;
                    padding: .25rem .5rem;
                }
                .teso-ref-flag {
                    display: inline-flex;
                    align-items: center;
                    gap: .25rem;
                    font-size: .7rem;
                    font-weight: 700;
                    color: #7f5200;
                    background: #fff2d8;
                    border: 1px solid #ffd48a;
                    border-radius: 999px;
                    padding: .12rem .45rem;
                    margin-left: .35rem;
                }
                .teso-monto-badge {
                    display: inline-block;
                    min-width: 46px;
                    text-align: center;
                    font-size: .68rem;
                    font-weight: 700;
                    color: #0f3f72;
                    background: #e9f3ff;
                    border: 1px solid #cfe2fb;
                    border-radius: 999px;
                    padding: .14rem .45rem;
                    margin-right: .35rem;
                }
                .teso-row-risk {
                    background: #fffaf2 !important;
                }
                .teso-row-ready {
                    background: #edf9f1 !important;
                }
                .teso-cuadre-badge {
                    font-size: .72rem;
                    border-radius: 999px;
                    padding: .28rem .55rem;
                    font-weight: 700;
                }
                .teso-state-wrap {
                    display: flex;
                    align-items: center;
                    gap: .4rem;
                    flex-wrap: wrap;
                }
                .teso-serie-head {
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:.75rem;
                    margin-bottom: .45rem;
                    padding: 0 .15rem;
                }
                .teso-serie-actions {
                    display:flex;
                    align-items:center;
                    gap:.5rem;
                }
                .teso-review-check {
                    display: inline-flex;
                    align-items: center;
                    gap: .3rem;
                    font-size: .78rem;
                    color: #1f4a75;
                    border: 1px solid #d6e4f5;
                    background: #f4f9ff;
                    border-radius: 8px;
                    padding: .2rem .45rem;
                }
                .btn[disabled], .btn.disabled {
                    cursor:not-allowed;
                    opacity:.6;
                }
                .teso-mobile-card {
                    border: 1px solid #e6edf7;
                    border-radius: 12px;
                    background: #fff;
                    box-shadow: 0 4px 12px rgba(2, 48, 71, .05);
                    padding: 10px;
                    margin-bottom: 10px;
                }
                .teso-mobile-card .head {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 6px;
                }
                .teso-mobile-card .line {
                    font-size: .83rem;
                    margin-bottom: 3px;
                    color: #244d74;
                }
                .teso-mobile-card .line strong {
                    color: #163c61;
                    margin-right: 4px;
                }
                @media (max-width: 767.98px) {
                    .teso-pay-actions {
                        display: flex;
                        flex-direction: column;
                        min-width: 140px;
                    }
                }
            </style>

            <?php
                $pendientes = isset($pagos_pendientes) && is_array($pagos_pendientes) ? $pagos_pendientes : array();
                $totalPendientes = count($pendientes);
                $totalMonto = 0;
                $totalMontoUSD = 0;
                $totalMontoNIO = 0;
                $totalTransferUSD = 0;
                $totalTransferNIO = 0;
                $countTransfer = 0;
                $defaultFechaRecepcion = isset($filtro_fecha_fin) && !empty($filtro_fecha_fin) ? $filtro_fecha_fin : date('Y-m-d');
                $normalizarMoneda = function ($raw) {
                    $m = strtoupper(trim((string)$raw));
                    if ($m === 'NIO' || $m === 'NIO$' || $m === 'CS' || $m === 'C$' || $m === 'CRC' || $m === 'CORDOBA' || $m === 'CORDOBAS') {
                        return 'NIO';
                    }
                    return 'USD';
                };
                $simboloMoneda = function ($moneda) {
                    return ($moneda === 'NIO') ? 'C$' : '$';
                };
                $normalizarConcepto = function ($raw) {
                    $txt = trim((string)$raw);
                    if ($txt === '') {
                        return '-';
                    }
                    if (preg_match('/prestamo\s*#?\s*(\d+).*?cuota\s*#?\s*(\d+)/i', $txt, $m)) {
                        return 'Prestamo #' . $m[1] . ' Cuota #' . $m[2];
                    }
                    return $txt;
                };
                $pendientesPorSerie = array(
                    'A' => array(),
                    'B' => array(),
                    'C' => array(),
                    'D' => array(),
                    'E' => array(),
                    'F' => array(),
                    'G' => array(),
                );
                foreach ($pendientes as $p) {
                    $montoTmp = isset($p->monto) ? floatval($p->monto) : 0;
                    $totalMonto += $montoTmp;
                    $monedaTmpTotal = $normalizarMoneda(isset($p->moneda) ? $p->moneda : 'USD');
                    if ($monedaTmpTotal === 'NIO') {
                        $totalMontoNIO += $montoTmp;
                    } else {
                        $totalMontoUSD += $montoTmp;
                    }

                    $medioTmp = isset($p->medio_pago) ? strtolower(trim((string)$p->medio_pago)) : '';
                    $esTransfer = (strpos($medioTmp, 'transfer') !== false);
                    if ($esTransfer) {
                        $countTransfer++;
                        if ($monedaTmpTotal === 'NIO') {
                            $totalTransferNIO += $montoTmp;
                        } else {
                            $totalTransferUSD += $montoTmp;
                        }
                    }

                    $refTmp = isset($p->documento_numero) ? trim((string)$p->documento_numero) : '';
                    $serieTmp = '';
                    if (!empty($p->serie_codigo)) {
                        $serieTmp = strtoupper(trim((string)$p->serie_codigo));
                    } elseif ($refTmp !== '' && preg_match('/^([A-Za-z]+)/', $refTmp, $mSerie)) {
                        $serieTmp = strtoupper($mSerie[1]);
                    }
                    if ($serieTmp === '' || !array_key_exists($serieTmp, $pendientesPorSerie)) {
                        continue;
                    }
                    if (!isset($pendientesPorSerie[$serieTmp])) $pendientesPorSerie[$serieTmp] = array();
                    $pendientesPorSerie[$serieTmp][] = $p;
                }
            ?>

            <?php $this->load->view('tesoreria/partial_back'); ?>
            <?php
                $tcCompraDefault = isset($tasa_compra) && floatval($tasa_compra) > 0 ? number_format(floatval($tasa_compra), 4, '.', '') : '';
                $tcVentaDefault = isset($tasa_venta) && floatval($tasa_venta) > 0 ? number_format(floatval($tasa_venta), 4, '.', '') : '';
            ?>
            <div class="page-header mb-3">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-money-check-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Pagos Pendientes de Crédito</h5>
                                <span>Pagos provisionales de créditos enviados desde Cobros para revisión del día.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card teso-pay-card mb-3">
                <div class="card-body py-2">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="mb-1 small text-muted">Tipo de Cambio Compra</label>
                            <input type="number" step="0.0001" min="0" class="form-control form-control-sm" id="tc_compra" value="<?php echo html_escape($tcCompraDefault); ?>" placeholder="Ej: 36.4500">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="mb-1 small text-muted">Tipo de Cambio Venta (usa conversión pagos en C$)</label>
                            <input type="number" step="0.0001" min="0" class="form-control form-control-sm" id="tc_venta" value="<?php echo html_escape($tcVentaDefault); ?>" placeholder="Ej: 36.8500">
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Para pagos en NIO, se convertirá a USD con: <strong id="tc_venta_preview"><?php echo html_escape($tcVentaDefault !== '' ? $tcVentaDefault : '0.0000'); ?></strong></small>
                            <small class="text-muted">Fórmula: $ USD = C$ recibido / TC Venta</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-2 col-md-4 col-6 mb-2">
                    <div class="teso-pay-kpi">
                        <div class="label">Pendientes</div>
                        <div class="value"><?php echo number_format($totalPendientes, 0); ?></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-2">
                    <div class="teso-pay-kpi">
                        <div class="label">Total USD</div>
                        <div class="value">$<?php echo number_format($totalMontoUSD, 2); ?></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-2">
                    <div class="teso-pay-kpi">
                        <div class="label">Total NIO</div>
                        <div class="value">C$<?php echo number_format($totalMontoNIO, 2); ?></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-2">
                    <div class="teso-pay-kpi">
                        <div class="label">Transferencias (USD)</div>
                        <div class="value"><?php echo number_format($countTransfer, 0); ?></div>
                    </div>
                </div>
            </div>

            <div class="card teso-pay-card">
                <div class="card-body">
                    <div id="tesoPayAlert" class="alert d-none" role="alert"></div>
                    <form class="teso-pay-toolbar" method="get" action="<?php echo base_url('tesoreria/pagos'); ?>">
                        <div class="row align-items-end">
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="<?php echo html_escape(isset($filtro_fecha_inicio) ? $filtro_fecha_inicio : date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" value="<?php echo html_escape(isset($filtro_fecha_fin) ? $filtro_fecha_fin : date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Serie</label>
                                <select class="form-control" name="idserie">
                                    <option value="">Todas</option>
                                    <?php if (!empty($series_recibos)): ?>
                                        <?php foreach ($series_recibos as $sr): ?>
                                            <option value="<?php echo intval($sr->idserie); ?>" <?php echo (isset($filtro_idserie) && intval($filtro_idserie) === intval($sr->idserie)) ? 'selected' : ''; ?>>
                                                <?php echo html_escape((isset($sr->codigo) ? $sr->codigo : ('Serie ' . intval($sr->idserie))) . ' - ' . (isset($sr->nombre) ? $sr->nombre : '')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="mb-1 small text-muted">Buscar</label>
                                <input type="text" class="form-control" name="q" placeholder="Cliente, concepto o referencia" value="<?php echo html_escape(isset($filtro_q) ? $filtro_q : ''); ?>">
                            </div>
                            <div class="col-md-2 text-md-right">
                                <button type="submit" class="btn bg-blue text-white"><i class="fas fa-filter"></i> Filtrar</button>
                                <a href="<?php echo base_url('tesoreria/pagos'); ?>" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i> Hoy</a>
                            </div>
                        </div>
                    </form>

                    <div class="d-none d-md-block">
                        <?php foreach ($pendientesPorSerie as $serie => $rowsSerie): ?>
                                <?php
                                    $serieAllReviewed = !empty($rowsSerie);
                                    if (!empty($rowsSerie)) {
                                        foreach ($rowsSerie as $tmpSerieRow) {
                                            $revTmp = isset($tmpSerieRow->recibo_revisado) ? intval($tmpSerieRow->recibo_revisado) : 0;
                                            if ($revTmp !== 1) {
                                                $serieAllReviewed = false;
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <div class="table-responsive mb-3">
                                    <div class="teso-serie-head">
                                        <h6 class="mb-0 text-primary">Serie <?php echo html_escape($serie); ?></h6>
                                        <div class="teso-serie-actions">
                                            <?php if (!empty($rowsSerie)): ?>
                                                <label class="teso-review-check mb-0">
                                                    <input type="checkbox" class="js-serie-recibos-check" data-serie="<?php echo html_escape($serie); ?>" <?php echo $serieAllReviewed ? 'checked' : ''; ?>>
                                                    Recibos revisados
                                                </label>
                                            <?php endif; ?>
                                            <small class="text-muted">Registros: <?php echo count($rowsSerie); ?></small>
                                            <button type="button" class="btn btn-sm btn-success btn-aprobar-serie" data-serie="<?php echo html_escape($serie); ?>" <?php echo empty($rowsSerie) ? 'disabled' : ''; ?>><i class="fas fa-check-double"></i> Aprobar Serie</button>
                                        </div>
                                    </div>
                                    <table class="table table-sm table-striped table-bordered mb-0">
                                        <thead>
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
                                            <?php if (empty($rowsSerie)): ?>
                                                        <tr><td colspan="14" class="text-center text-muted">Sin pagos para esta serie.</td></tr>
                                            <?php endif; ?>
                                            <?php foreach ($rowsSerie as $i => $row): ?>
                                                <?php
                                                    $estadoRaw = isset($row->estado) ? strtolower(trim($row->estado)) : '';
                                                    $isPendiente = in_array($estadoRaw, array('registrado','programado','pendiente'));
                                                    $isAnulado = ($estadoRaw === 'anulado');
                                                    $isAplicado = in_array($estadoRaw, array('aplicado_pendiente_arqueo','aplicado','cerrado'));
                                                    $estadoTxt = $isPendiente ? 'Pendiente' : ($isAnulado ? 'Anulado' : ($isAplicado ? 'Aplicado' : ucfirst($estadoRaw)));
                                                    $estadoClass = $isPendiente ? 'badge-warning' : ($isAnulado ? 'badge-danger' : 'badge-success');
                                                    $fecha = isset($row->fecha) ? $row->fecha : (isset($row->fecha_programada) ? $row->fecha_programada : '');
                                                    $ref = isset($row->documento_numero) ? trim((string)$row->documento_numero) : '';
                                                    $patronSerie = '/^' . preg_quote((string)$serie, '/') . '\\d{10}$/i';
                                                    $refValida = ($ref !== '' && preg_match($patronSerie, $ref));
                                                    $refSospechosa = !$refValida;
                                                    $montoSistema = isset($row->monto) ? floatval($row->monto) : 0;
                                                    $monedaRow = $normalizarMoneda(isset($row->moneda) ? $row->moneda : 'USD');
                                                    $simboloRow = $simboloMoneda($monedaRow);
                                                    $montoRecibido = isset($row->monto_recibido) && $row->monto_recibido !== null ? floatval($row->monto_recibido) : $montoSistema;
                                                    $fechaRecepcion = isset($row->fecha_recepcion) && !empty($row->fecha_recepcion) ? $row->fecha_recepcion : $defaultFechaRecepcion;
                                                    $reciboRevisado = isset($row->recibo_revisado) && intval($row->recibo_revisado) === 1;
                                                    $prestamoNum = isset($row->idprestamo) ? intval($row->idprestamo) : 0;
                                                    $cuotaNum = isset($row->idcuota) ? intval($row->idcuota) : 0;
                                                    $conceptoParse = isset($row->concepto) ? (string)$row->concepto : '';
                                                    if (($prestamoNum <= 0 || $cuotaNum <= 0) && preg_match('/prestamo\s*#?\s*(\d+).*?cuota\s*#?\s*(\d+)/i', $conceptoParse, $mc)) {
                                                        if ($prestamoNum <= 0) $prestamoNum = intval($mc[1]);
                                                        if ($cuotaNum <= 0) $cuotaNum = intval($mc[2]);
                                                    }
                                                ?>
                                                        <tr class="<?php echo $refSospechosa ? 'teso-row-risk' : ''; ?> js-pago-row" data-row-id="<?php echo intval(isset($row->id) ? $row->id : 0); ?>" data-row-monto="<?php echo number_format($montoSistema, 2, '.', ''); ?>" data-row-moneda="<?php echo html_escape($monedaRow); ?>" data-serie="<?php echo html_escape($serie); ?>" data-ref-ok="<?php echo $refValida ? '1' : '0'; ?>" data-ready="0">
                                                    <td><?php echo $i + 1; ?></td>
                                                    <td><?php echo html_escape($fecha); ?></td>
                                                    <td><?php echo html_escape(isset($row->beneficiario) ? $row->beneficiario : '-'); ?></td>
                                                    <td><?php echo html_escape(isset($row->registrado_por) ? $row->registrado_por : '-'); ?></td>
                                                    <td><?php echo $prestamoNum > 0 ? ('#' . intval($prestamoNum)) : '-'; ?></td>
                                                    <td><?php echo $cuotaNum > 0 ? ('#' . intval($cuotaNum)) : '-'; ?></td>
                                                    <td><?php echo html_escape(isset($row->medio_pago) ? ucfirst($row->medio_pago) : '-'); ?></td>
                                                    <td><span class="teso-monto-badge"><?php echo html_escape($monedaRow); ?></span></td>
                                                    <td><?php echo $simboloRow; ?><?php echo number_format($montoSistema, 2); ?></td>
                                                    <td>
                                                        <?php echo html_escape(isset($row->documento_numero) ? $row->documento_numero : '-'); ?>
                                                        <?php if ($refSospechosa): ?>
                                                            <span class="teso-ref-flag"><i class="fas fa-exclamation-circle"></i> Serie/Ref inválida</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="min-width:120px;">
                                                        <input type="number" step="0.01" min="0" class="form-control form-control-sm input-monto-recibido" value="<?php echo $montoRecibido !== null ? number_format($montoRecibido, 2, '.', '') : ''; ?>" placeholder="0.00">
                                                    </td>
                                                    <td style="min-width:145px;">
                                                        <input type="date" class="form-control form-control-sm input-fecha-recepcion" value="<?php echo html_escape($fechaRecepcion); ?>">
                                                    </td>
                                                    <td class="text-center" style="min-width:120px;">
                                                        <label class="mb-0 teso-review-check">
                                                            <input type="checkbox" class="input-recibo-revisado" data-serie="<?php echo html_escape($serie); ?>" <?php echo $reciboRevisado ? 'checked' : ''; ?>>
                                                            Revisado
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="teso-state-wrap">
                                                            <span class="badge <?php echo $estadoClass; ?> teso-pay-badge js-row-status"><?php echo $estadoTxt; ?></span>
                                                            <?php if ($isPendiente): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-danger js-btn-anular" data-row-id="<?php echo intval(isset($row->id) ? $row->id : 0); ?>">Anular</button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php endforeach; ?>

                        <div class="d-flex justify-content-end align-items-center mt-2 mb-1">
                            <button type="button" class="btn btn-primary mr-2" id="btnGuardarRecepcion"><i class="fas fa-save"></i> Guardar Todo</button>
                            <a href="<?php echo base_url('tesoreria/arqueos?fecha=' . urlencode(isset($filtro_fecha_fin) && $filtro_fecha_fin ? $filtro_fecha_fin : date('Y-m-d'))); ?>" class="btn btn-info"><i class="fas fa-file-alt"></i> Ir a Arqueos</a>
                        </div>
                    </div>

                    <div class="d-md-none mt-1">
                        <?php if (empty($pendientes)): ?>
                            <div class="teso-mobile-card text-center text-muted">No hay pagos pendientes en tesorería.</div>
                        <?php else: ?>
                            <?php foreach ($pendientes as $i => $row): ?>
                                <?php
                                    $estadoRaw = isset($row->estado) ? strtolower(trim($row->estado)) : '';
                                    $estadoTxt = in_array($estadoRaw, array('registrado','programado','pendiente')) ? 'Pendiente' : ucfirst($estadoRaw);
                                    $estadoClass = in_array($estadoRaw, array('registrado','programado','pendiente')) ? 'badge-warning' : 'badge-secondary';
                                    $fecha = isset($row->fecha) ? $row->fecha : (isset($row->fecha_programada) ? $row->fecha_programada : '');
                                    $isPendiente = in_array($estadoRaw, array('registrado','programado','pendiente'));
                                    $ref = isset($row->documento_numero) ? trim((string)$row->documento_numero) : '';
                                    $serieMobile = !empty($row->serie_codigo) ? strtoupper(trim((string)$row->serie_codigo)) : '';
                                    if ($serieMobile === '' && $ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $mSerieMobile)) {
                                        $serieMobile = strtoupper($mSerieMobile[1]);
                                    }
                                    $patronSerieMobile = '/^' . preg_quote((string)$serieMobile, '/') . '\\d{10}$/i';
                                    $refValidaMobile = ($serieMobile !== '' && $ref !== '' && preg_match($patronSerieMobile, $ref));
                                    $refSospechosa = !$refValidaMobile;
                                    $montoSistemaMobile = isset($row->monto) ? floatval($row->monto) : 0;
                                    $monedaRowMobile = $normalizarMoneda(isset($row->moneda) ? $row->moneda : 'USD');
                                    $simboloRowMobile = $simboloMoneda($monedaRowMobile);
                                    $montoRecMobile = isset($row->monto_recibido) && $row->monto_recibido !== null ? floatval($row->monto_recibido) : $montoSistemaMobile;
                                    $fechaRecMobile = isset($row->fecha_recepcion) && !empty($row->fecha_recepcion) ? $row->fecha_recepcion : $defaultFechaRecepcion;
                                    $reciboRevisadoMobile = isset($row->recibo_revisado) && intval($row->recibo_revisado) === 1;
                                ?>
                                    <div class="teso-mobile-card <?php echo $refSospechosa ? 'teso-row-risk' : ''; ?> js-pago-row" data-row-id="<?php echo intval(isset($row->id) ? $row->id : 0); ?>" data-row-monto="<?php echo number_format($montoSistemaMobile, 2, '.', ''); ?>" data-row-moneda="<?php echo html_escape($monedaRowMobile); ?>" data-serie="<?php echo html_escape($serieMobile); ?>" data-ref-ok="<?php echo $refValidaMobile ? '1' : '0'; ?>" data-ready="0">
                                    <div class="head">
                                        <div>
                                            <strong>#<?php echo $i + 1; ?></strong>
                                            <span class="teso-monto-badge ml-1"><?php echo html_escape($monedaRowMobile); ?></span>
                                        </div>
                                        <span class="badge <?php echo $estadoClass; ?> teso-pay-badge"><?php echo $estadoTxt; ?></span>
                                    </div>
                                    <div class="line"><strong>Fecha:</strong><?php echo html_escape($fecha); ?></div>
                                    <div class="line"><strong>Cliente:</strong><?php echo html_escape(isset($row->beneficiario) ? $row->beneficiario : '-'); ?></div>
                                    <div class="line"><strong>Registrado por:</strong><?php echo html_escape(isset($row->registrado_por) ? $row->registrado_por : '-'); ?></div>
                                    <?php
                                        $prestamoNumMobile = isset($row->idprestamo) ? intval($row->idprestamo) : 0;
                                        $cuotaNumMobile = isset($row->idcuota) ? intval($row->idcuota) : 0;
                                        $conceptoParseMobile = isset($row->concepto) ? (string)$row->concepto : '';
                                        if (($prestamoNumMobile <= 0 || $cuotaNumMobile <= 0) && preg_match('/prestamo\s*#?\s*(\d+).*?cuota\s*#?\s*(\d+)/i', $conceptoParseMobile, $mcMobile)) {
                                            if ($prestamoNumMobile <= 0) $prestamoNumMobile = intval($mcMobile[1]);
                                            if ($cuotaNumMobile <= 0) $cuotaNumMobile = intval($mcMobile[2]);
                                        }
                                    ?>
                                    <div class="line"><strong>Préstamo:</strong><?php echo $prestamoNumMobile > 0 ? ('#' . intval($prestamoNumMobile)) : '-'; ?></div>
                                    <div class="line"><strong>Cuota:</strong><?php echo $cuotaNumMobile > 0 ? ('#' . intval($cuotaNumMobile)) : '-'; ?></div>
                                    <div class="line"><strong>Método:</strong><?php echo html_escape(isset($row->medio_pago) ? ucfirst($row->medio_pago) : '-'); ?></div>
                                    <div class="line"><strong>Monto:</strong><?php echo $simboloRowMobile; ?><?php echo number_format(isset($row->monto) ? floatval($row->monto) : 0, 2); ?></div>
                                    <div class="line">
                                        <strong>Ref:</strong><?php echo html_escape(isset($row->documento_numero) ? $row->documento_numero : '-'); ?>
                                        <?php if ($refSospechosa): ?>
                                            <span class="teso-ref-flag"><i class="fas fa-exclamation-circle"></i> Serie/Ref inválida</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="line"><strong>Monto a Aplicar:</strong><input type="number" step="0.01" min="0" class="form-control form-control-sm input-monto-recibido" placeholder="0.00" value="<?php echo ($montoRecMobile !== null) ? number_format($montoRecMobile, 2, '.', '') : ''; ?>"></div>
                                    <div class="line"><strong>Fecha Recepción:</strong><input type="date" class="form-control form-control-sm input-fecha-recepcion" value="<?php echo html_escape($fechaRecMobile); ?>"></div>
                                    <div class="line">
                                        <strong>Recibo Revisado:</strong>
                                        <label class="mb-0 teso-review-check">
                                            <input type="checkbox" class="input-recibo-revisado" data-serie="<?php echo html_escape($serieMobile); ?>" <?php echo $reciboRevisadoMobile ? 'checked' : ''; ?>>
                                            Revisado
                                        </label>
                                    </div>
                                    <div class="line">
                                        <strong>Estado:</strong>
                                        <span class="badge <?php echo $estadoClass; ?> teso-pay-badge js-row-status"><?php echo $estadoTxt; ?></span>
                                        <?php if ($isPendiente): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger js-btn-anular mt-1" data-row-id="<?php echo intval(isset($row->id) ? $row->id : 0); ?>">Anular</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($pendientes)): ?>
                        <div class="d-md-none mt-2">
                            <button type="button" class="btn btn-primary btn-block mb-2" id="btnGuardarRecepcionMobile"><i class="fas fa-save"></i> Guardar Todo</button>
                            <a href="<?php echo base_url('tesoreria/arqueos?fecha=' . urlencode(isset($filtro_fecha_fin) && $filtro_fecha_fin ? $filtro_fecha_fin : date('Y-m-d'))); ?>" class="btn btn-info btn-block"><i class="fas fa-file-alt"></i> Ir a Arqueos</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="modalContainer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarProvisional" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar Pago Provisional</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_pago_id">
                <div class="form-group">
                    <label>Monto</label>
                    <input type="number" step="0.01" id="edit_monto" class="form-control">
                </div>
                <div class="form-group">
                    <label>Método</label>
                    <select id="edit_metodo" class="form-control">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="cheque">Cheque</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label>Referencia</label>
                    <input type="text" id="edit_referencia" class="form-control" placeholder="Serie o referencia">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarEdicionProvisional">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        function showAlert(type, text) {
            var $a = $('#tesoPayAlert');
            $a.removeClass('d-none alert-success alert-danger alert-info')
              .addClass('alert-' + type)
              .text(text || '');
        }

        function parseAmount(val) {
            var n = parseFloat(val);
            return isNaN(n) ? 0 : n;
        }

        function getSerieRows(serie) {
            return $('.js-pago-row[data-serie="' + serie + '"]:visible');
        }

        function persistReciboRevision(ids, revisado, onDone) {
            if (!Array.isArray(ids) || !ids.length) {
                if (typeof onDone === 'function') onDone(false);
                return;
            }
            $.ajax({
                url: base_url + 'tesoreria/guardar_revision_recibos_ajax',
                method: 'POST',
                dataType: 'json',
                data: {
                    items: JSON.stringify(ids.map(function (id) { return { id: id }; })),
                    revisado: revisado ? 1 : 0
                },
                success: function (resp) {
                    if (!(resp && resp.status)) {
                        showAlert('danger', (resp && resp.message) ? resp.message : 'No se pudo guardar revisión de recibo.');
                    }
                    if (typeof onDone === 'function') onDone(!!(resp && resp.status));
                },
                error: function () {
                    showAlert('danger', 'Error de conexión al guardar revisión de recibo.');
                    if (typeof onDone === 'function') onDone(false);
                }
            });
        }

        function setRowReady($row, ready) {
            if (!$row || !$row.length) return;
            $row.attr('data-ready', ready ? '1' : '0');
            $row.toggleClass('teso-row-ready', !!ready);
        }

        function evaluateRow($row) {
            if (!$row || !$row.length) return false;
            var montoRecibido = parseAmount($row.find('.input-monto-recibido').val());
            var fechaRecepcion = ($row.find('.input-fecha-recepcion').val() || '').trim();
            var refOk = ($row.attr('data-ref-ok') || '0') === '1';
            var revisado = $row.find('.input-recibo-revisado').is(':checked');
            var ready = ($row.attr('data-ready') || '0') === '1';
            if (!refOk) {
                setRowReady($row, false);
            } else if (!montoRecibido || fechaRecepcion === '') {
                setRowReady($row, false);
            } else if (!revisado) {
                setRowReady($row, false);
            } else {
                if (ready) {
                    // listo
                }
            }
            return (refOk && revisado && montoRecibido > 0 && fechaRecepcion !== '');
        }

        function evaluateSerie(serie) {
            var $rows = getSerieRows(serie);
            var total = 0;
            var valid = 0;
            $rows.each(function () {
                total++;
                if (evaluateRow($(this))) valid++;
            });
            var allValid = (total > 0 && valid === total);
            var $btnSerie = $('.btn-aprobar-serie[data-serie="' + serie + '"]');
            $btnSerie.prop('disabled', !(total > 0));
            if (allValid) {
                $rows.each(function () { setRowReady($(this), true); });
                $btnSerie.html('<i class="fas fa-check-double"></i> Serie lista');
            } else {
                $rows.each(function () { setRowReady($(this), false); });
                $btnSerie.html('<i class="fas fa-check-double"></i> Aprobar Serie');
            }
        }

        function evaluateAll() {
            $('.js-pago-row').each(function () { evaluateRow($(this)); });
            $('.btn-aprobar-serie').each(function () { evaluateSerie($(this).data('serie')); });
        }

        function getTasaInputs() {
            var compra = parseFloat($('#tc_compra').val() || '0');
            var venta = parseFloat($('#tc_venta').val() || '0');
            return {
                compra: isNaN(compra) ? 0 : compra,
                venta: isNaN(venta) ? 0 : venta
            };
        }

        $('#btnGuardarRecepcion').on('click', function () {
            var $btnGuardar = $('#btnGuardarRecepcion');
            if ($btnGuardar.prop('disabled')) {
                return;
            }
            var mapItems = {};
            $('.js-pago-row[data-row-id]').each(function () {
                var $row = $(this);
                var id = parseInt($row.attr('data-row-id'), 10) || 0;
                if (!id) return;
                if (($row.attr('data-ready') || '0') !== '1') return;
                var montoRec = parseFloat($row.find('.input-monto-recibido').val()) || 0;
                var fechaRec = $row.find('.input-fecha-recepcion').val() || '';
                if (montoRec > 0 && fechaRec) {
                    mapItems[id] = { id: id, monto_recibido: montoRec, fecha_recepcion: fechaRec };
                }
            });
            var items = Object.keys(mapItems).map(function (k) { return mapItems[k]; });

            if (!items.length) {
                showAlert('info', 'Primero aprueba una serie para dejar filas listas para guardar.');
                return;
            }

            var tasas = getTasaInputs();
            var requiereVenta = false;
            $('.js-pago-row[data-ready="1"]').each(function () {
                var m = ($(this).attr('data-row-moneda') || 'USD').toString().toUpperCase();
                if (m === 'NIO') {
                    requiereVenta = true;
                }
            });
            if (requiereVenta && (!tasas.venta || tasas.venta <= 0)) {
                showAlert('danger', 'Debes ingresar Tipo de Cambio Venta válido para convertir pagos en C$ a USD.');
                return;
            }

            $btnGuardar.prop('disabled', true).addClass('disabled').html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: base_url + 'tesoreria/guardar_recepcion_pagos_ajax',
                method: 'POST',
                dataType: 'json',
                data: {
                    items: JSON.stringify(items),
                    tasa_compra: tasas.compra,
                    tasa_venta: tasas.venta
                },
                success: function (resp) {
                    if (resp && resp.status) {
                        var msg = resp.message || 'Pagos procesados.';
                        if (resp.errors && resp.errors.length) {
                            msg += ' ' + resp.errors.length + ' con observación.';
                        }
                        showAlert('success', msg);
                        setTimeout(function () { window.location.reload(); }, 700);
                    } else {
                        var errMsg = (resp && resp.message) ? resp.message : 'No se pudieron procesar los pagos.';
                        if (resp && resp.errors && resp.errors.length) {
                            errMsg += ' ' + resp.errors[0];
                        }
                        showAlert('danger', errMsg);
                    }
                },
                error: function () {
                    showAlert('danger', 'Error de conexión al guardar todo.');
                },
                complete: function () {
                    $btnGuardar.prop('disabled', false).removeClass('disabled').html('<i class="fas fa-save"></i> Guardar Todo');
                }
            });
        });

        $('#btnGuardarRecepcionMobile').on('click', function () {
            $('#btnGuardarRecepcion').trigger('click');
        });

        $(document).on('input change', '.input-monto-recibido, .input-fecha-recepcion', function () {
            var $row = $(this).closest('.js-pago-row');
            setRowReady($row, false);
            evaluateRow($row);
            evaluateSerie(($row.data('serie') || '').toString());
        });

        $(document).on('change', '.input-recibo-revisado', function () {
            var $row = $(this).closest('.js-pago-row');
            var rowId = parseInt($row.attr('data-row-id'), 10) || 0;
            var checked = $(this).is(':checked');
            setRowReady($row, false);
            evaluateRow($row);
            var serie = ($(this).data('serie') || $row.data('serie') || '').toString();
            evaluateSerie(serie);

            if (rowId > 0) {
                persistReciboRevision([rowId], checked);
            }

            if (serie) {
                var $serieRows = getSerieRows(serie);
                if ($serieRows.length) {
                    var allChecked = true;
                    $serieRows.each(function () {
                        if (!$(this).find('.input-recibo-revisado').is(':checked')) {
                            allChecked = false;
                            return false;
                        }
                    });
                    $('.js-serie-recibos-check[data-serie="' + serie + '"]').prop('checked', allChecked);
                }
            }
        });

        $(document).on('change', '.js-serie-recibos-check', function () {
            var serie = ($(this).data('serie') || '').toString();
            var checked = $(this).is(':checked');
            if (!serie) return;
            var $rows = getSerieRows(serie);
            var ids = [];
            $rows.each(function () {
                $(this).find('.input-recibo-revisado').prop('checked', checked);
                setRowReady($(this), false);
                evaluateRow($(this));
                var id = parseInt($(this).attr('data-row-id'), 10) || 0;
                if (id > 0) ids.push(id);
            });
            evaluateSerie(serie);
            if (ids.length) {
                persistReciboRevision(ids, checked);
            }
        });

        $(document).on('input', '#tc_venta', function () {
            var val = parseFloat($(this).val() || '0');
            if (isNaN(val) || val <= 0) {
                $('#tc_venta_preview').text('0.0000');
                return;
            }
            $('#tc_venta_preview').text(val.toFixed(4));
        });

        $(document).on('click', '.btn-aprobar-serie', function () {
            var $btn = $(this);
            var serie = ($btn.data('serie') || '').toString();
            var $rows = getSerieRows(serie);
            var invalid = false;

            $rows.each(function () {
                var $row = $(this);
                if (!evaluateRow($row)) {
                    invalid = true;
                    return false;
                }
            });

            if (invalid || !$rows.length) {
                showAlert('danger', 'La serie ' + serie + ' requiere: referencia válida, monto a aplicar, fecha y check de recibo revisado en todas las filas.');
                return;
            }

            var ids = [];
            $rows.each(function () {
                var id = parseInt($(this).attr('data-row-id'), 10) || 0;
                if (id > 0) ids.push(id);
            });
            if (!ids.length) {
                showAlert('danger', 'No hay pagos válidos para marcar la serie.');
                return;
            }

            persistReciboRevision(ids, true, function (ok) {
                if (!ok) return;
                $rows.each(function () {
                    var $row = $(this);
                    $row.find('.input-recibo-revisado').prop('checked', true);
                    setRowReady($row, true);
                });
                $('.js-serie-recibos-check[data-serie="' + serie + '"]').prop('checked', true);
                $btn.html('<i class="fas fa-check-double"></i> Serie lista');
                showAlert('success', 'Serie ' + serie + ' marcada y guardada.');
                evaluateSerie(serie);
            });
        });

        $(document).on('click', '.js-btn-anular', function () {
            var $btn = $(this);
            var id = parseInt($btn.data('row-id'), 10) || 0;
            if (!id) return;

            var motivo = window.prompt('Motivo de anulación:', 'Anulado por tesorería');
            if (motivo === null) return;
            motivo = (motivo || '').trim();
            if (!motivo) {
                showAlert('danger', 'Debes indicar un motivo para anular.');
                return;
            }

            $btn.prop('disabled', true);
            $.ajax({
                url: base_url + 'tesoreria/rechazar_pago_provisional_ajax',
                method: 'POST',
                dataType: 'json',
                data: { pago_id: id, motivo: motivo },
                success: function (resp) {
                    if (resp && resp.status) {
                        showAlert('success', resp.message || 'Pago anulado correctamente.');
                        setTimeout(function () { window.location.reload(); }, 500);
                    } else {
                        showAlert('danger', (resp && resp.message) ? resp.message : 'No se pudo anular el pago.');
                        $btn.prop('disabled', false);
                    }
                },
                error: function () {
                    showAlert('danger', 'Error de conexión al anular el pago.');
                    $btn.prop('disabled', false);
                }
            });
        });

        evaluateAll();
    })();
</script>
