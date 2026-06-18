<style>
    .servicont-main-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }
    
    .servicont-header-modern {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .servicont-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .servicont-header-content {
        position: relative;
        z-index: 1;
        padding-left: 6px; /* small offset to match other headers */
    }
    
    .servicont-header-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .servicont-header-icon i {
        font-size: 32px;
        color: #ffffff;
    }
    
    .servicont-header-title {
        color: #ffffff;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 5px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .servicont-header-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        margin: 0;
    }
    
    .servicont-card-modern {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 25px;
    }
    
    .servicont-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(42, 82, 152, 0.15);
    }
    
    .servicont-card-header {
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        padding: 20px 25px;
        border-bottom: none;
    }
    
    .servicont-card-header h4 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    
    .servicont-card-body {
        padding: 30px 25px;
    }
    
    .servicont-welcome-card {
        background: linear-gradient(135deg, #163a70 0%, #0d284f 100%);
        border-radius: 15px;
        padding: 35px;
        color: #ffffff;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(13, 40, 79, 0.28);
    }
    
    .servicont-welcome-title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .servicont-welcome-text {
        font-size: 15px;
        opacity: 0.95;
        margin-bottom: 25px;
    }
    
    .servicont-btn-primary {
        background: #ffffff;
        color: #2a5298;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-right: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    
    .servicont-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        color: #1e3c72;
    }
    
    .servicont-shortcut-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .servicont-shortcut-item {
        padding: 15px 20px;
        margin-bottom: 12px;
        background: #f8fafc;
        border-radius: 10px;
        border-left: 4px solid #2a5298;
        transition: all 0.3s ease;
    }
    
    .servicont-shortcut-item:hover {
        background: #e8f0fe;
        transform: translateX(5px);
        border-left-color: #1e3c72;
    }
    
    .servicont-shortcut-link {
        color: #2a5298;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    
    .servicont-shortcut-link i {
        margin-right: 12px;
        font-size: 18px;
    }
    
    .servicont-shortcut-link:hover {
        color: #1e3c72;
    }
    
    .servicont-info-card {
        background: linear-gradient(135deg, #e8f0fe 0%, #f0f4ff 100%);
        border-left: 4px solid #2a5298;
        padding: 25px;
        border-radius: 10px;
    }
    
    .servicont-info-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .servicont-info-icon i {
        font-size: 24px;
        color: #ffffff;
    }
    
    .servicont-info-text {
        color: #5a6c7d;
        font-size: 15px;
        line-height: 1.6;
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="mb-3 d-flex justify-content-end">
                <a href="<?php echo site_url('menu'); ?>" class="btn btn-outline-primary"><i class="fa fa-home mr-1"></i> Regresar al menú</a>
            </div>

            <div class="servicont-welcome-card">
                <h2 class="servicont-welcome-title">Bienvenido al módulo de Contabilidad</h2>
                <p class="servicont-welcome-text">Accede rápidamente a las funciones más usadas del módulo para gestionar tus registros contables de manera eficiente.</p>
                <div class="d-flex flex-wrap align-items-center" style="gap:12px;">
                    <a href="<?php echo base_url('contabilidad/catalogo'); ?>" class="servicont-btn-primary">
                        <i class="ik ik-layers mr-2"></i>Catálogo de Cuentas
                    </a>
                    <a href="<?php echo base_url('contabilidad/diario'); ?>" class="servicont-btn-primary">
                        <i class="ik ik-book-open mr-2"></i>Libro Diario
                    </a>
                    <a href="<?php echo base_url('contabilidad/mayor'); ?>" class="servicont-btn-primary">
                        <i class="ik ik-file-text mr-2"></i>Libro Mayor
                    </a>
                    <div style="display:flex;gap:8px;align-items:center;margin-left:8px;">
                        <input type="month" id="homeMonth" class="form-control" style="max-width:200px;" value="<?php
                            // prefer start_date if present, else end_date, else current month
                            $sd = $this->input->get('start_date');
                            $ed = $this->input->get('end_date');
                            if ($sd && preg_match('/^(\d{4}-\d{2})-01$/', $sd, $m)) {
                                echo $m[1];
                            } elseif ($ed && preg_match('/^(\d{4}-\d{2})-\d{2}$/', $ed, $m2)) {
                                echo $m2[1];
                            } else {
                                echo date('Y-m');
                            }
                        ?>" />
                        <button id="homeApply" class="servicont-btn-primary" style="background:#2a9d8f;color:#fff;border:none;">Aplicar mes</button>
                    </div>
                </div>
            </div>

            <!-- Título resumen de indicadores -->
            <div class="row mb-2">
                <div class="col-12">
                    <h3 style="margin:0 0 12px 0;color:#1e3c72;font-weight:700;font-size:20px;">Resumen de Asientos Mensual</h3>
                </div>
            </div>
            <!-- Estadísticas mensuales / rango -->
            <div class="row mb-3">
                <?php 
                    $s = isset($conta_stats) ? $conta_stats : ['total'=>0,'posted'=>0,'unposted'=>0,'year'=>date('Y'),'month'=>date('m')]; 
                    $range = isset($conta_stats_range) && $conta_stats_range ? $conta_stats_range : null;
                    $diario_base = base_url('contabilidad/diario');
                    if ($range) {
                        $diario_base .= '?start_date=' . $range['start'] . '&end_date=' . $range['end'];
                        $diario_mayorizados = $diario_base . '&filter=posted';
                        $diario_pendientes = $diario_base . '&filter=unposted';
                    } else {
                        $diario_mayorizados = base_url('contabilidad/diario?filter=posted');
                        $diario_pendientes = base_url('contabilidad/diario?filter=unposted');
                    }
                ?>
                <div class="col-md-4 mb-3">
                    <div class="servicont-card-modern text-center p-3">
                        <div class="servicont-card-header">
                            <h4><i class="ik ik-list mr-2"></i>Asientos este mes</h4>
                        </div>
                        <div class="servicont-card-body">
                            <h2 style="font-size:36px; margin:0;"><?php echo intval($s['total']); ?></h2>
                            <p class="mb-0 text-muted"><?php echo $range ? 'Rango: ' . $range['start'] . ' → ' . $range['end'] : ('Mes: ' . sprintf('%02d', $s['month']) . '/' . $s['year']); ?></p>
                            <div class="mt-3"><a href="<?php echo $diario_base; ?>" class="btn btn-outline-primary">Ver Libro Diario</a></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="servicont-card-modern text-center p-3">
                        <div class="servicont-card-header">
                            <h4><i class="ik ik-check-circle mr-2"></i>Asientos mayorizados</h4>
                        </div>
                        <div class="servicont-card-body">
                            <h2 style="font-size:36px; margin:0; color:#2a9d8f;"><?php echo intval($s['posted']); ?></h2>
                            <p class="mb-0 text-muted">Mayorizados (posted)</p>
                            <div class="mt-3"><a href="<?php echo $diario_mayorizados; ?>" class="btn btn-outline-success">Ver mayorizados</a></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="servicont-card-modern text-center p-3">
                        <div class="servicont-card-header">
                            <h4><i class="ik ik-alert-circle mr-2"></i>Asientos pendientes</h4>
                        </div>
                        <div class="servicont-card-body">
                            <h2 style="font-size:36px; margin:0; color:#e76f51;"><?php echo intval($s['unposted']); ?></h2>
                            <p class="mb-0 text-muted">Pendientes por mayorizar</p>
                            <div class="mt-3"><a href="<?php echo $diario_pendientes; ?>" class="btn btn-outline-danger">Ver pendientes</a></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="servicont-card-modern">
                        <div class="servicont-card-header">
                            <h4><i class="ik ik-zap mr-2"></i>Accesos Rápidos</h4>
                        </div>
                        <div class="servicont-card-body">
                            <ul class="servicont-shortcut-list">
                                <li class="servicont-shortcut-item">
                                    <a href="<?php echo base_url('contabilidad/catalogo'); ?>" class="servicont-shortcut-link">
                                        <i class="ik ik-layers"></i>Ver Catálogo de Cuentas
                                    </a>
                                </li>
                                <li class="servicont-shortcut-item">
                                    <a href="<?php echo base_url('contabilidad/diario'); ?>" class="servicont-shortcut-link">
                                        <i class="ik ik-book-open"></i>Ir al Libro Diario
                                    </a>
                                </li>
                                <li class="servicont-shortcut-item">
                                    <a href="<?php echo base_url('contabilidad/mayor'); ?>" class="servicont-shortcut-link">
                                        <i class="ik ik-file-text"></i>Abrir Libro Mayor
                                    </a>
                                </li>
                                <li class="servicont-shortcut-item">
                                    <a href="<?php echo base_url('contabilidad/balanza'); ?>" class="servicont-shortcut-link">
                                        <i class="ik ik-bar-chart-2"></i>Balanza de Comprobación
                                    </a>
                                </li>
                                <li class="servicont-shortcut-item" style="border-left-color: #764ba2;">
                                    <a href="<?php echo base_url('contabilidad/importar_balanza'); ?>" class="servicont-shortcut-link" style="color: #764ba2;">
                                        <i class="ik ik-upload"></i><strong>Importar Balanza de Comprobación</strong>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="servicont-card-modern">
                        <div class="servicont-card-header">
                            <h4><i class="ik ik-info mr-2"></i>Información del Sistema</h4>
                        </div>
                        <div class="servicont-card-body">
                            <div class="servicont-info-card">
                                <div class="servicont-info-icon">
                                    <i class="ik ik-briefcase"></i>
                                </div>
                                <p class="servicont-info-text mb-3">
                                    <strong>ServiCont</strong> te permite gestionar de manera integral todos los aspectos contables de tu empresa.
                                </p>
                                <p class="servicont-info-text mb-0">
                                    Crea asientos contables, revisa balances, genera reportes y exporta información de manera rápida y sencilla.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalContainer"></div>

            <script>
                document.getElementById('homeApply').addEventListener('click', function(){
                    var m = document.getElementById('homeMonth').value; // format YYYY-MM
                    if (!m) { alert('Seleccione un mes'); return; }
                    var parts = m.split('-');
                    var year = parseInt(parts[0],10);
                    var month = parseInt(parts[1],10);
                    var start = m + '-01';
                    // compute last day of month
                    var lastDay = new Date(year, month, 0).getDate();
                    var end = m + '-' + String(lastDay).padStart(2,'0');
                    var params = new URLSearchParams(window.location.search);
                    params.set('start_date', start);
                    params.set('end_date', end);
                    window.location = window.location.pathname + '?' + params.toString();
                });
            </script>

        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
        </div>
    </footer>
</div>
<script src="<?php echo base_url('public/js/contabilidad_home.js'); ?>"></script>
