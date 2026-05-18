<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-chart-line bg-blue"></i>
                            <div class="d-inline">
                                <h5> Estado de Resultados Comparativo </h5>
                                <span>Comparativo mensual vs acumulado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->load->view('contabilidad/partial_back'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label>Desde</label>
                                    <input type="date" id="resCompStart" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label>Hasta</label>
                                    <input type="date" id="resCompEnd" class="form-control" />
                                </div>
                                <div class="col-md-2">
                                    <button id="resCompRefresh" class="btn btn-primary btn-block">Actualizar</button>
                                </div>
                                <div class="col-md-2">
                                    <button id="resCompExport" class="btn btn-outline-secondary btn-block">Exportar CSV</button>
                                </div>
                                <div class="col-md-2">
                                    <button id="resCompPdf" class="btn btn-info btn-block">Exportar PDF</button>
                                </div>
                            </div>

                            <div id="resCompContent">
                                <div class="table-responsive">
                                    <table id="resCompTable" class="table table-bordered" style="font-size:16px;min-width:480px;">
                                        <thead>
                                            <tr style="background:#ffe082;border-bottom:2px solid #d32f2f;">
                                                <th style="width:60%;font-weight:bold;font-size:18px;">CONCEPTO</th>
                                                <th style="width:20%;font-weight:bold;font-size:18px;text-align:right;">MES</th>
                                                <th style="width:20%;font-weight:bold;font-size:18px;text-align:right;">ACUMULADO</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resCompBody">
                                            <tr><td colspan="3" class="text-center text-muted">Seleccione fechas y presione "Actualizar"</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
<script>
function renderEstadoResultadosComparativo(data) {
    var tbody = document.getElementById('resCompBody');
    if (!data || !data.estructura_mes || !data.estructura_acumulado) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay datos</td></tr>';
        return;
    }
    var html = '';
    for (var i = 0; i < data.estructura_mes.length; i++) {
        var itemMes = data.estructura_mes[i];
        var itemAcum = data.estructura_acumulado[i] || {};
        var isSubtotal = itemMes.titulo && itemMes.titulo.startsWith('=');
        var isDetalle = itemMes.detalle && itemMes.detalle.length > 0;
        var montoMes = (typeof itemMes.monto !== 'undefined') ? Number(itemMes.monto) : '';
        var montoAcum = (typeof itemAcum.monto !== 'undefined') ? Number(itemAcum.monto) : '';
        var montoFmtMes = (montoMes !== '' && !isNaN(montoMes)) ? 'C$' + montoMes.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
        var montoFmtAcum = (montoAcum !== '' && !isNaN(montoAcum)) ? 'C$' + montoAcum.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
        if (isSubtotal) {
            html += '<tr style="font-weight:bold;background:#fffde7;border-top:2px solid #d32f2f;"><td style="font-size:16px;">' + itemMes.titulo.replace('=','').trim() + '</td><td class="text-right" style="font-size:16px;">' + montoFmtMes + '</td><td class="text-right" style="font-size:16px;">' + montoFmtAcum + '</td></tr>';
        } else if (isDetalle) {
            html += '<tr style="font-weight:bold;text-transform:uppercase;"><td>' + itemMes.titulo + '</td><td class="text-right">' + montoFmtMes + '</td><td class="text-right">' + montoFmtAcum + '</td></tr>';
            for (var j = 0; j < itemMes.detalle.length; j++) {
                var detMes = itemMes.detalle[j];
                var detAcum = (itemAcum.detalle && itemAcum.detalle[j]) ? itemAcum.detalle[j] : {};
                var detMontoMes = (typeof detMes.display !== 'undefined') ? Number(detMes.display) : '';
                var detMontoAcum = (typeof detAcum.display !== 'undefined') ? Number(detAcum.display) : '';
                var detFmtMes = (detMontoMes !== '' && !isNaN(detMontoMes)) ? 'C$' + detMontoMes.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
                var detFmtAcum = (detMontoAcum !== '' && !isNaN(detMontoAcum)) ? 'C$' + detMontoAcum.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
                html += '<tr><td style="padding-left:32px;">' + detMes.name + '</td><td class="text-right">' + detFmtMes + '</td><td class="text-right">' + detFmtAcum + '</td></tr>';
            }
        } else {
            html += '<tr style="font-weight:bold;text-transform:uppercase;"><td>' + itemMes.titulo + '</td><td class="text-right">' + montoFmtMes + '</td><td class="text-right">' + montoFmtAcum + '</td></tr>';
        }
    }
    tbody.innerHTML = html;
}
</script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
        </div>
    </footer>
</div>
