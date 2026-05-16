<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Estadísticas</h2>
            <p class="text-muted">Indicadores financieros del sistema</p>
        </div>
        <div>
            <a href="<?php echo site_url('menu'); ?>" class="btn btn-sm btn-outline-secondary">&larr; Volver al Menú</a>
        </div>
    </div>

    <div class="row" id="estadisticas-cards">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Ingresos</h6>
                    <h3 id="stat-revenue">--</h3>
                    <small class="text-muted">Total acumulado</small>
                    <div class="mt-2">
                        <button class="btn btn-link p-0" data-metric="revenue" id="btn-detail-revenue">Ver detalle</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Gastos</h6>
                    <h3 id="stat-expenses">--</h3>
                    <small class="text-muted">Total acumulado</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Neto</h6>
                    <h3 id="stat-net">--</h3>
                    <small class="text-muted">Ingresos - Gastos</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Créditos Vivos</h6>
                    <h3 id="stat-loans">--</h3>
                    <small class="text-muted">Total créditos</small>
                    <div class="mt-2">
                        <button class="btn btn-link p-0" data-metric="overdue_loans" id="btn-detail-overdue">Ver morosos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Cobros</h6>
                    <div id="chart-placeholder" style="height:240px; display:flex; align-items:center; justify-content:center; color:#999">[Gráfico - pendiente de implementar]</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Información rápida</h6>
                    <p class="text-muted small">Resumen de las métricas más importantes. Usa "Ver detalle" para abrir el modal con desgloses.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal placeholder -->
    <?php $this->load->view('estadisticas/modal_metric'); ?>
</div>
