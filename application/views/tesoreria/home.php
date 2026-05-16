<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-wallet bg-blue"></i>
                            <div class="d-inline">
                                <h5>Tesorería</h5>
                                <span>Panel de control y operaciones financieras</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-2 mt-lg-0">
                        <a href="<?php echo site_url('menu'); ?>" class="btn btn-outline-secondary btn-sm" id="btnVolverMenuTeso"><i class="fa fa-arrow-left mr-1"></i> Volver al Menú</a>
                    </div>
                </div>
            </div>

            <style>
                .teso-hero {
                    border: 1px solid #dfe7f3;
                    border-radius: 12px;
                    background: linear-gradient(130deg, #f6fbff 0%, #eef5ff 58%, #f9fbff 100%);
                    box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
                }
                .teso-hero-title {
                    margin: 0 0 6px;
                    color: #0f2f5f;
                    font-weight: 700;
                    letter-spacing: .2px;
                }
                .teso-hero-sub {
                    margin: 0;
                    color: #5b6b82;
                    font-size: 13px;
                }
                .teso-actions .btn {
                    border-radius: 8px;
                    font-weight: 600;
                }
                .teso-actions .btn-primary {
                    background: #1f5bbd;
                    border-color: #1f5bbd;
                }
                .teso-kpi {
                    border: 1px solid #e6edf7;
                    border-radius: 12px;
                    background: #fff;
                    box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
                    height: 100%;
                }
                .teso-kpi .kpi-label {
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: .6px;
                    color: #6b7b93;
                    margin-bottom: 6px;
                    font-weight: 700;
                }
                .teso-kpi .kpi-value {
                    font-size: 22px;
                    line-height: 1;
                    font-weight: 700;
                    color: #143b76;
                }
                .teso-kpi .kpi-note {
                    margin-top: 6px;
                    font-size: 12px;
                    color: #7b8798;
                }
                .teso-nav-card,
                .teso-info-card {
                    border: 1px solid #e3ebf7;
                    border-radius: 12px;
                    background: #fff;
                    box-shadow: 0 6px 16px rgba(2, 48, 71, .05);
                    height: 100%;
                }
                .teso-nav-card .card-header,
                .teso-info-card .card-header {
                    background: #f5f8fe;
                    border-bottom: 1px solid #e4ecf8;
                    border-top-left-radius: 12px;
                    border-top-right-radius: 12px;
                }
                .teso-nav-link {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border: 1px solid #e6edf7;
                    border-radius: 10px;
                    padding: 10px 12px;
                    color: #1b3f75;
                    font-weight: 600;
                    margin-bottom: 10px;
                    text-decoration: none;
                    transition: all .2s ease;
                }
                .teso-nav-link:hover {
                    text-decoration: none;
                    color: #0c2b55;
                    border-color: #bcd1f0;
                    background: #f4f8ff;
                }
                .teso-nav-link i {
                    color: #3b82f6;
                }
                .teso-info-list {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                }
                .teso-info-list li {
                    border-bottom: 1px dashed #d9e3f3;
                    padding: 10px 0;
                    font-size: 13px;
                    color: #334155;
                }
                .teso-info-list li:last-child {
                    border-bottom: 0;
                    padding-bottom: 0;
                }
            </style>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card teso-hero">
                        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                            <div class="mb-3 mb-lg-0">
                                <h4 class="teso-hero-title">Módulo de Tesorería</h4>
                                <p class="teso-hero-sub">Administra cajas y bancos, controla movimientos, programa pagos y ejecuta conciliaciones con trazabilidad completa.</p>
                            </div>
                            <div class="teso-actions d-flex flex-wrap">
                                <button id="btnNuevoMovimiento" class="btn btn-primary mr-2 mb-2"><i class="fas fa-plus-circle mr-1"></i> Nuevo Movimiento</button>
                                <button id="btnProgramarPago" class="btn btn-outline-primary mr-2 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Programar Pago</button>
                                <button id="btnArqueo" class="btn btn-outline-info mr-2 mb-2"><i class="fas fa-cash-register mr-1"></i> Arqueo</button>
                                <a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="btn btn-outline-secondary mr-2 mb-2">Movimientos</a>
                                <a href="<?php echo base_url('tesoreria/pagos'); ?>" class="btn btn-outline-secondary mr-2 mb-2">Pagos</a>
                                <a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="btn btn-outline-secondary mb-2">Conciliación</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card teso-kpi">
                        <div class="card-body">
                            <div class="kpi-label">Movimientos</div>
                            <div class="kpi-value">Control</div>
                            <div class="kpi-note">Registro diario de ingresos y egresos.</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card teso-kpi">
                        <div class="card-body">
                            <div class="kpi-label">Conciliación</div>
                            <div class="kpi-value">Bancos</div>
                            <div class="kpi-note">Valida saldos y operaciones pendientes.</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card teso-kpi">
                        <div class="card-body">
                            <div class="kpi-label">Pagos</div>
                            <div class="kpi-value">Programados</div>
                            <div class="kpi-note">Agenda pagos y controla su ejecución.</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card teso-kpi">
                        <div class="card-body">
                            <div class="kpi-label">Flujo</div>
                            <div class="kpi-value">Efectivo</div>
                            <div class="kpi-note">Visibilidad de entradas y salidas.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card teso-nav-card">
                        <div class="card-header"><h6 class="mb-0">Atajos de Tesorería</h6></div>
                        <div class="card-body">
                            <a class="teso-nav-link" href="<?php echo base_url('tesoreria/cajas_bancos'); ?>">
                                <span><i class="fas fa-university mr-2"></i>Cajas y Bancos</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a class="teso-nav-link" href="<?php echo base_url('tesoreria/movimientos'); ?>">
                                <span><i class="fas fa-exchange-alt mr-2"></i>Movimientos</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a class="teso-nav-link" href="<?php echo base_url('tesoreria/flujo'); ?>">
                                <span><i class="fas fa-chart-line mr-2"></i>Flujo de Efectivo</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a class="teso-nav-link mb-0" href="<?php echo base_url('tesoreria/reportes'); ?>">
                                <span><i class="fas fa-file-invoice-dollar mr-2"></i>Reportes</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card teso-info-card">
                        <div class="card-header"><h6 class="mb-0">Información Operativa</h6></div>
                        <div class="card-body">
                            <ul class="teso-info-list">
                                <li><strong>Movimientos:</strong> registra cada transacción con soporte y trazabilidad.</li>
                                <li><strong>Pagos:</strong> programa y ejecuta pagos con control de estado.</li>
                                <li><strong>Conciliación:</strong> compara operaciones registradas contra cuentas bancarias.</li>
                                <li><strong>Flujo:</strong> consulta comportamiento de efectivo para decisiones diarias.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalContainer"></div>

        </div>
    </div>
</div>
<script src="<?php echo base_url('public/js/tesoreria_home.js'); ?>"></script>
