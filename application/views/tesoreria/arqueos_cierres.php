<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="<?php echo isset($icono) ? $icono : 'fas fa-box'; ?> bg-blue"></i>
                    <div class="d-inline">
                        <h5><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Arqueos'; ?></h5>
                        <span>Consulte los cierres de caja y entre al detalle para arqueo e impresión.</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                <a href="<?php echo base_url('tesoreria/pagos'); ?>" class="btn bg-blue text-white">
                    <i class="fas fa-arrow-left"></i> Volver a Pagos
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom bg-light">
                    <h5 class="mb-0">Historial de Cierres de Caja</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($cierres)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> No hay cierres registrados.
                        </div>
                    <?php else: ?>
                        <div class="row mb-2">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <input id="cierres_search" class="form-control" placeholder="Buscar por cierre, fecha, usuario o estado..." />
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select id="cierres_filter_estado" class="form-control">
                                    <option value="all">Todos los estados</option>
                                    <option value="CERRADO">Cerrado</option>
                                    <option value="ABIERTO">Abierto</option>
                                    <option value="ANULADO">Anulado</option>
                                </select>
                            </div>
                            <div class="col-md-5 text-md-right">
                                <small class="text-muted">Estilo compacto, similar al listado de solicitudes.</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="cierres-table" class="table table-sm table-striped table-bordered table-compact">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Consecutivo</th>
                                        <th>Fecha de Cierre</th>
                                        <th>Usuario</th>
                                        <th>Cantidad de Pagos</th>
                                        <th>Monto Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cierres as $cierre): ?>
                                        <?php $estado = strtoupper($cierre->estado); ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cierre->id); ?></td>
                                            <td><strong>CIERRE <?php echo htmlspecialchars($cierre->consecutivo); ?></strong></td>
                                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($cierre->fecha_cierre))); ?></td>
                                            <td><?php echo htmlspecialchars($cierre->idusuario ? $cierre->idusuario : 'N/A'); ?></td>
                                            <td><span class="badge badge-light border text-dark"><?php echo htmlspecialchars($cierre->cantidad_pagos); ?></span></td>
                                            <td><strong>$<?php echo number_format($cierre->monto_total, 2); ?></strong></td>
                                            <td>
                                                <?php
                                                    $badge_class = 'badge-light border text-dark';
                                                    if ($estado === 'ABIERTO') {
                                                        $badge_class = 'badge-light border text-dark';
                                                    } elseif ($estado === 'ANULADO') {
                                                        $badge_class = 'badge-light border text-dark';
                                                    }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span>
                                            </td>
                                            <td>
                                                <a href="<?php echo base_url('tesoreria/cierres_detalle?cierre_id=' . $cierre->id); ?>" 
                                                   class="btn btn-sm btn-primary" title="Ver detalle">
                                                    <i class="fas fa-eye"></i> Visualizar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-compact td, .table-compact th{
        padding: .32rem .48rem;
        vertical-align: middle;
        font-size: .84rem;
        line-height: 1.1;
        white-space: nowrap;
    }
    .table-compact thead th{
        background: #f5f8fe;
        color: #1f3c73;
        font-weight: 700;
        border-bottom: 2px solid #d9e4f5;
    }
    .table-compact .btn{
        padding: .24rem .52rem;
        font-size: .85rem;
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>

<script>
    (function(){
        function normalize(v){ return (v || '').toString().toLowerCase(); }
        function applyFilter(){
            var q = normalize(document.getElementById('cierres_search') ? document.getElementById('cierres_search').value : '');
            var st = normalize(document.getElementById('cierres_filter_estado') ? document.getElementById('cierres_filter_estado').value : 'all');
            var rows = document.querySelectorAll('#cierres-table tbody tr');
            rows.forEach(function(row){
                var txt = normalize(row.innerText);
                var estadoCell = row.querySelector('td:nth-child(7)');
                var estadoTxt = estadoCell ? normalize(estadoCell.innerText) : '';
                var okQ = (q === '' || txt.indexOf(q) !== -1);
                var okSt = (st === 'all' || estadoTxt.indexOf(st) !== -1);
                row.style.display = (okQ && okSt) ? '' : 'none';
            });
        }

        var input = document.getElementById('cierres_search');
        var sel = document.getElementById('cierres_filter_estado');
        if (input) input.addEventListener('input', applyFilter);
        if (sel) sel.addEventListener('change', applyFilter);
    })();
</script>
        </div>
    </div>
</div>
