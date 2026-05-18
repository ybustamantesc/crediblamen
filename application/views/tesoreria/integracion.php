<?php defined('BASEPATH') OR exit('Acción no permitida'); ?>
<?php
    $depositos = isset($depositos_pendientes) && is_array($depositos_pendientes) ? $depositos_pendientes : array();
    $cuentasBanco = isset($cuentas_banco) && is_array($cuentas_banco) ? $cuentas_banco : array();
    $tcCompraDefault = isset($tasa_compra) ? floatval($tasa_compra) : 0;
    $tcVentaDefault = isset($tasa_venta) ? floatval($tasa_venta) : 0;
    $pendientes = array();
    $integrados = array();
    foreach ($depositos as $dep) {
        $estadoTmp = isset($dep->estado) ? strtolower(trim((string)$dep->estado)) : 'pendiente';
        if ($estadoTmp === 'integrado') {
            $integrados[] = $dep;
        } else {
            $pendientes[] = $dep;
        }
    }
?>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid py-3">
    <style>
        .ib-card {
            border: 1px solid #e3ebf7;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(2, 48, 71, .05);
        }
        .ib-kpi {
            border: 1px solid #e6edf7;
            border-radius: 10px;
            background: #f8fbff;
            padding: 12px 14px;
            height: 100%;
        }
        .ib-kpi .label {
            font-size: .78rem;
            color: #54739a;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .ib-kpi .value {
            font-size: 1.06rem;
            color: #13315c;
            font-weight: 700;
        }
        .ib-table thead th {
            white-space: nowrap;
            background: #f5f8fe;
            color: #1f3c73;
        }
        .ib-table td {
            vertical-align: middle;
        }
        .ib-money-usd {
            color: #123f70;
            font-weight: 700;
        }
        .ib-money-nio {
            color: #1f5d3f;
            font-weight: 700;
        }
        .ib-rule {
            font-size: .72rem;
            color: #28557f;
            background: #ecf5ff;
            border: 1px solid #d2e6ff;
            border-radius: 999px;
            padding: .16rem .46rem;
            display: inline-block;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Integración Bancaria</h4>
            <small class="text-muted">Depósitos bancarios pendientes generados desde el arqueo general, separados por moneda.</small>
        </div>
        <div>
            <a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="btn btn-outline-secondary btn-sm">Volver a Cierres</a>
        </div>
    </div>

    <div class="alert alert-light border mb-3">
        <span class="mr-3"><strong>TC Compra:</strong> <?php echo $tcCompraDefault > 0 ? number_format($tcCompraDefault, 4) : 'N/D'; ?></span>
        <span><strong>TC Venta:</strong> <?php echo $tcVentaDefault > 0 ? number_format($tcVentaDefault, 4) : 'N/D'; ?></span>
    </div>

    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="ib-kpi">
                <div class="label">Pendientes</div>
                <div class="value"><?php echo number_format(count($pendientes), 0); ?></div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="ib-kpi">
                <div class="label">Integrados</div>
                <div class="value"><?php echo number_format(count($integrados), 0); ?></div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="ib-kpi">
                <div class="label">Pendiente USD</div>
                <div class="value">
                    $<?php echo number_format(array_sum(array_map(function($d){ return (isset($d->moneda_origen) && strtoupper($d->moneda_origen)==='USD') ? floatval(isset($d->monto_arqueo)?$d->monto_arqueo:0) : 0; }, $pendientes)), 2); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="ib-kpi">
                <div class="label">Pendiente NIO</div>
                <div class="value">
                    C$<?php echo number_format(array_sum(array_map(function($d){ return (isset($d->moneda_origen) && strtoupper($d->moneda_origen)==='NIO') ? floatval(isset($d->monto_arqueo)?$d->monto_arqueo:0) : 0; }, $pendientes)), 2); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card ib-card mb-3">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Depósitos Bancarios Pendientes</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pendientes)): ?>
                <div class="alert alert-light border mb-0">No hay depósitos pendientes por integrar.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped ib-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cierre</th>
                                <th>Moneda Origen</th>
                                <th>Monto Arqueo</th>
                                <th>Cuenta Bancaria</th>
                                <th>Moneda Integración</th>
                                <th>Regla TC</th>
                                <th>Tipo Cambio</th>
                                <th>Monto Depositado</th>
                                <th>Monto Integrado</th>
                                <th>Minuta</th>
                                <th>Fecha Depósito</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $i => $dep): ?>
                                <?php $monOrigen = isset($dep->moneda_origen) ? strtoupper((string)$dep->moneda_origen) : 'USD'; ?>
                                <tr data-deposito-id="<?php echo intval($dep->id); ?>" data-moneda-origen="<?php echo htmlspecialchars($monOrigen); ?>">
                                    <td><?php echo $i + 1; ?></td>
                                    <td>#<?php echo intval(isset($dep->cierre_consecutivo) ? $dep->cierre_consecutivo : $dep->idcierre_caja); ?></td>
                                    <td><span class="badge badge-light border text-dark"><?php echo htmlspecialchars($monOrigen); ?></span></td>
                                    <td class="<?php echo $monOrigen === 'NIO' ? 'ib-money-nio' : 'ib-money-usd'; ?>">
                                        <?php echo $monOrigen === 'NIO' ? 'C$' : '$'; ?><?php echo number_format(isset($dep->monto_arqueo) ? floatval($dep->monto_arqueo) : 0, 2); ?>
                                    </td>
                                    <td style="min-width:220px;">
                                        <select class="form-control form-control-sm js-dep-cuenta">
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($cuentasBanco as $cb): ?>
                                                <option value="<?php echo intval($cb->id); ?>" data-currency="<?php echo htmlspecialchars(isset($cb->currency) ? strtoupper((string)$cb->currency) : 'USD'); ?>">
                                                    <?php echo htmlspecialchars((isset($cb->name) ? $cb->name : 'Cuenta') . ' - ' . (isset($cb->bank_name) ? $cb->bank_name : 'Banco') . ' (' . (isset($cb->currency) ? strtoupper((string)$cb->currency) : 'USD') . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td style="min-width:130px;">
                                        <select class="form-control form-control-sm js-dep-moneda-destino">
                                            <option value="USD" <?php echo $monOrigen === 'USD' ? 'selected' : ''; ?>>USD</option>
                                            <option value="NIO" <?php echo $monOrigen === 'NIO' ? 'selected' : ''; ?>>NIO</option>
                                        </select>
                                    </td>
                                    <td style="min-width:130px;"><span class="ib-rule js-dep-rule">Misma moneda</span></td>
                                    <td style="min-width:120px;"><input type="number" step="0.0001" min="0" class="form-control form-control-sm js-dep-tc" placeholder="0.0000"></td>
                                    <td style="min-width:130px;"><input type="number" step="0.01" min="0" class="form-control form-control-sm js-dep-monto" value="<?php echo number_format(isset($dep->monto_arqueo) ? floatval($dep->monto_arqueo) : 0, 2, '.', ''); ?>"></td>
                                    <td style="min-width:130px;"><input type="text" class="form-control form-control-sm js-dep-monto-int" readonly></td>
                                    <td style="min-width:140px;"><input type="text" class="form-control form-control-sm js-dep-minuta" placeholder="Núm. minuta"></td>
                                    <td style="min-width:140px;"><input type="date" class="form-control form-control-sm js-dep-fecha" value="<?php echo date('Y-m-d'); ?>"></td>
                                    <td style="min-width:120px;">
                                        <button type="button" class="btn btn-sm btn-success js-btn-integrar-deposito">Integrar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card ib-card">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Depósitos Integrados</h5>
        </div>
        <div class="card-body">
            <?php if (empty($integrados)): ?>
                <div class="alert alert-light border mb-0">Aún no hay depósitos integrados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped ib-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cierre</th>
                                <th>Origen</th>
                                <th>Monto Arqueo</th>
                                <th>Cuenta</th>
                                <th>Moneda Destino</th>
                                <th>Monto Depositado</th>
                                <th>Monto Integrado</th>
                                <th>Regla TC</th>
                                <th>Tipo Cambio</th>
                                <th>Minuta</th>
                                <th>Fecha</th>
                                <th>Movimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($integrados as $i => $dep): ?>
                                <?php $monOrigen = isset($dep->moneda_origen) ? strtoupper((string)$dep->moneda_origen) : 'USD'; ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td>#<?php echo intval(isset($dep->cierre_consecutivo) ? $dep->cierre_consecutivo : $dep->idcierre_caja); ?></td>
                                    <td><?php echo htmlspecialchars($monOrigen); ?></td>
                                    <td><?php echo $monOrigen === 'NIO' ? 'C$' : '$'; ?><?php echo number_format(isset($dep->monto_arqueo) ? floatval($dep->monto_arqueo) : 0, 2); ?></td>
                                    <td><?php echo htmlspecialchars(trim((string)(isset($dep->cuenta_nombre) ? $dep->cuenta_nombre : '')) . (isset($dep->banco_nombre) && $dep->banco_nombre ? ' - ' . $dep->banco_nombre : '')); ?></td>
                                    <td><?php echo htmlspecialchars(isset($dep->moneda_destino) ? strtoupper((string)$dep->moneda_destino) : '-'); ?></td>
                                    <td><?php echo $monOrigen === 'NIO' ? 'C$' : '$'; ?><?php echo number_format(isset($dep->monto_depositado) ? floatval($dep->monto_depositado) : 0, 2); ?></td>
                                    <td><?php echo (isset($dep->moneda_destino) && strtoupper((string)$dep->moneda_destino) === 'NIO') ? 'C$' : '$'; ?><?php echo number_format(isset($dep->monto_integrado) ? floatval($dep->monto_integrado) : 0, 2); ?></td>
                                    <td><?php echo htmlspecialchars(isset($dep->tc_tipo_aplicado) ? strtoupper((string)$dep->tc_tipo_aplicado) : '-'); ?></td>
                                    <td><?php echo isset($dep->tasa_cambio) && floatval($dep->tasa_cambio) > 0 ? number_format(floatval($dep->tasa_cambio), 4) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars(isset($dep->referencia_minuta) ? $dep->referencia_minuta : '-'); ?></td>
                                    <td><?php echo htmlspecialchars(isset($dep->fecha_deposito) ? $dep->fecha_deposito : '-'); ?></td>
                                    <td><?php echo isset($dep->movimiento_id) && intval($dep->movimiento_id) > 0 ? ('#' . intval($dep->movimiento_id)) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function(){
        var tcCompraDefault = <?php echo $tcCompraDefault > 0 ? json_encode(round($tcCompraDefault, 4)) : '0'; ?>;
        var tcVentaDefault = <?php echo $tcVentaDefault > 0 ? json_encode(round($tcVentaDefault, 4)) : '0'; ?>;

        function num(v){ var n = parseFloat(v); return isNaN(n) ? 0 : n; }

        function recalcRow($tr){
            var origen = String($tr.data('moneda-origen') || 'USD').toUpperCase();
            var destino = String($tr.find('.js-dep-moneda-destino').val() || 'USD').toUpperCase();
            var montoDep = num($tr.find('.js-dep-monto').val());
            var $tc = $tr.find('.js-dep-tc');
            var tc = num($tc.val());
            var regla = 'Misma moneda';
            var montoInt = montoDep;

            if (origen === destino) {
                regla = 'Sin conversión';
                $tc.val('').prop('disabled', true);
                montoInt = montoDep;
            } else if (origen === 'NIO' && destino === 'USD') {
                regla = 'TC Venta (NIO→USD)';
                if (tc <= 0 && tcVentaDefault > 0) {
                    tc = tcVentaDefault;
                    $tc.val(tc.toFixed(4));
                }
                $tc.prop('disabled', false);
                montoInt = tc > 0 ? (montoDep / tc) : 0;
            } else if (origen === 'USD' && destino === 'NIO') {
                regla = 'TC Compra (USD→NIO)';
                if (tc <= 0 && tcCompraDefault > 0) {
                    tc = tcCompraDefault;
                    $tc.val(tc.toFixed(4));
                }
                $tc.prop('disabled', false);
                montoInt = tc > 0 ? (montoDep * tc) : 0;
            }

            $tr.find('.js-dep-rule').text(regla);
            $tr.find('.js-dep-monto-int').val(montoInt.toFixed(2));
        }

        $(document).on('change', '.js-dep-cuenta', function(){
            var $tr = $(this).closest('tr');
            var currency = String($(this).find('option:selected').data('currency') || '').toUpperCase();
            if (currency === 'USD' || currency === 'NIO') {
                $tr.find('.js-dep-moneda-destino').val(currency).trigger('change');
            }
        });

        $(document).on('change', '.js-dep-moneda-destino', function(){
            var $tr = $(this).closest('tr');
            recalcRow($tr);
        });

        $(document).on('input change', '.js-dep-monto, .js-dep-tc', function(){
            recalcRow($(this).closest('tr'));
        });

        $('tr[data-deposito-id]').each(function(){ recalcRow($(this)); });

        $(document).on('click', '.js-btn-integrar-deposito', function(){
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var payload = {
                deposito_id: $tr.data('deposito-id'),
                idcuenta_banco: $tr.find('.js-dep-cuenta').val(),
                moneda_destino: $tr.find('.js-dep-moneda-destino').val(),
                tasa_cambio: $tr.find('.js-dep-tc').val(),
                monto_depositado: $tr.find('.js-dep-monto').val(),
                referencia_minuta: $tr.find('.js-dep-minuta').val(),
                fecha_deposito: $tr.find('.js-dep-fecha').val()
            };

            $btn.prop('disabled', true).text('Integrando...');
            $.post('<?php echo base_url('tesoreria/integrar_deposito_bancario_ajax'); ?>', payload, function(resp){
                if (resp && resp.status) {
                    alert(resp.message || 'Depósito integrado correctamente');
                    window.location.reload();
                    return;
                }
                alert(resp && resp.message ? resp.message : 'No se pudo integrar el depósito');
            }, 'json').fail(function(){
                alert('Error de conexión al integrar depósito');
            }).always(function(){
                $btn.prop('disabled', false).text('Integrar');
            });
        });
    })();
</script>
        </div>
    </div>
</div>
