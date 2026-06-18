<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-file-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Estados de Cuenta Bancarios</h5>
                                <span>Seleccione cuenta y periodo para ver el estado de cuenta bancario.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('tesoreria/partial_back'); ?>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cuenta bancaria</label>
                                        <select id="conciliacionCuenta" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Periodo</label>
                                        <input id="conciliacionPeriodo" type="month" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="btnCargarEstadoCuenta" class="btn btn-primary btn-block">Ver Estado</button>
                                </div>
                                <div class="col-md-3 text-right">
                                    <button id="btnImprimirEstadoCuenta" class="btn btn-secondary btn-block">Imprimir Estado</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="estadoCuentaResumen" style="display:none;">
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>Saldo inicial</h6>
                            <h4 id="estadoSaldoInicial">0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>Total Abonos</h6>
                            <h4 id="estadoTotalAbonos">0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>Total Cargos</h6>
                            <h4 id="estadoTotalCargos">0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>Saldo final</h6>
                            <h4 id="estadoSaldoFinal">0.00</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="estadoCuentaTableRow" style="display:none;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Movimientos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="estadoCuentaTable" class="table table-sm table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha registro</th>
                                            <th>Descripción</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Monto</th>
                                            <th class="text-right">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function(){
    var siteUrl = '<?php echo site_url('tesoreria'); ?>';

    var currencySymbols = {
        'USD': '$', 'US': '$', 'EUR': '€', 'NIO': 'C$', 'DOP': 'RD$', 'HNL': 'L.', 'CRC': '₡', 'GTQ': 'Q', 'PAB': 'B/'
    };

    function formatCurrency(value, symbol) {
        var number = parseFloat(value);
        if (isNaN(number)) return (symbol || '') + '0.00';
        var abs = Math.abs(number).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        var s = symbol || '';
        if (number < 0) return '-' + s + ' ' + abs;
        return s + ' ' + abs;
    }

    function formatDate(value) {
        if (!value) return '';
        var d = new Date(value);
        if (isNaN(d.getTime())) return value;
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear();
    }

    function cargarCuentas() {
        $.ajax({
            url: siteUrl + '/get_conciliacion_data_ajax',
            method: 'GET',
            dataType: 'json',
            data: { cuenta_id: 0, periodo: $('#conciliacionPeriodo').val() || '' }
        }).done(function(resp) {
            if (!resp || !resp.status) {
                return;
            }
            var cuentas = resp.cuentas || [];
            $('#conciliacionCuenta').empty();
            cuentas.forEach(function(c) {
                $('#conciliacionCuenta').append($('<option>').val(c.id).text((c.name || '') + (c.code ? ' (' + c.code + ')' : '')));
            });
        });
    }

    function cargarEstadoCuenta() {
        var cuentaId = $('#conciliacionCuenta').val();
        var periodo = $('#conciliacionPeriodo').val();
        if (!cuentaId) {
            alert('Seleccione una cuenta bancaria.');
            return;
        }
        if (!periodo) {
            alert('Seleccione un periodo.');
            return;
        }

        $.ajax({
            url: siteUrl + '/get_conciliacion_data_ajax',
            method: 'GET',
            dataType: 'json',
            data: { cuenta_id: cuentaId, periodo: periodo }
        }).done(function(resp) {
            if (!resp || !resp.status) {
                alert('No se pudo cargar el estado de cuenta.');
                return;
            }

            var cuentaSymbol = '';
            if (resp.cuenta && resp.cuenta.currency_symbol) cuentaSymbol = resp.cuenta.currency_symbol;
            else if (resp.cuenta && resp.cuenta.currency) cuentaSymbol = currencySymbols[resp.cuenta.currency] || resp.cuenta.currency;

            $('#estadoSaldoInicial').text(formatCurrency(resp.saldo_inicial, cuentaSymbol));
            $('#estadoTotalAbonos').text(formatCurrency(resp.total_abonos, cuentaSymbol));
            $('#estadoTotalCargos').text(formatCurrency(resp.total_cargos, cuentaSymbol));
            $('#estadoSaldoFinal').text(formatCurrency(resp.saldo_final, cuentaSymbol));
            $('#estadoCuentaResumen').show();
            $('#estadoCuentaTableRow').show();

            var saldo = parseFloat(resp.saldo_inicial || 0);
            var rows = '';
            var movs = resp.movimientos || [];
            movs.forEach(function(m, index) {
                var monto = parseFloat(m.monto_total || m.monto || 0);
                var signo = 1;
                if (m.tipo_transferencia === 'cargo') {
                    signo = -1;
                } else if (m.tipo_transferencia === 'abono') {
                    signo = 1;
                } else if (monto < 0) {
                    signo = -1;
                }
                saldo += monto * signo;
                rows += '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + formatDate(m.fecha_registro || '') + '</td>' +
                    '<td>' + (m.descripcion || m.beneficiario || m.concepto || '') + '</td>' +
                    '<td>' + (m.tipo_transferencia || m.tipo_movimiento || m.forma_pago || '') + '</td>' +
                    '<td class="text-right">' + formatCurrency(monto, cuentaSymbol) + '</td>' +
                    '<td class="text-right">' + formatCurrency(saldo, cuentaSymbol) + '</td>' +
                    '</tr>';
            });
            if (rows === '') {
                rows = '<tr><td colspan="7" class="text-center">No hay movimientos para este periodo.</td></tr>';
            }
            $('#estadoCuentaTable tbody').html(rows);
        }).fail(function() {
            alert('Error de red al consultar el estado de cuenta.');
        });
    }

    function imprimirEstadoCuenta() {
        var cuentaId = $('#conciliacionCuenta').val();
        var periodo = $('#conciliacionPeriodo').val();
        if (!cuentaId || !periodo) {
            alert('Seleccione cuenta y periodo antes de imprimir.');
            return;
        }
        var url = siteUrl + '/conciliacion_pdf?cuenta_id=' + encodeURIComponent(cuentaId) + '&periodo=' + encodeURIComponent(periodo);
        window.open(url, '_blank');
    }

    $(document).ready(function() {
        var today = new Date();
        $('#conciliacionPeriodo').val(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2));
        cargarCuentas();

        $('#btnCargarEstadoCuenta').on('click', function() {
            cargarEstadoCuenta();
        });
        $('#btnImprimirEstadoCuenta').on('click', function() {
            imprimirEstadoCuenta();
        });
    });
})();
</script>

