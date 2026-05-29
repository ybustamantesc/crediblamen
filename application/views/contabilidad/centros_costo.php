<style>
    .servicont-centros-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    .servicont-centros-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-centros-header">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-building" style="font-size: 24px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h1 class="servicont-header-title">Centros de Costo</h1>
                        <p class="servicont-header-subtitle">Gestión de centros de costo para distribución contable</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="servicont-catalogo-card">
                        <div class="card-body" style="padding: 30px;">
                            <!-- Barra de herramientas -->
                            <div style="display:flex;gap:12px;margin-bottom:25px;align-items:center;">
                                <button id="btnNewCentroCosto" class="servicont-diario-btn">
                                    <i class="fas fa-plus"></i> Nuevo Centro de Costo
                                </button>
                                
                                <div style="position:relative;flex:1;max-width:400px;">
                                    <input type="text" id="searchCentroCosto" class="form-control servicont-input" placeholder="🔍 Buscar por código o nombre..." />
                                </div>
                            </div>
                            
                            <div id="centrosCostoContent">
                                <div style="text-align:center;padding:40px;color:#6b7280;">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    <div style="margin-top:12px;">Cargando...</div>
                                </div>
                            </div>
                            
                            <div id="modalContainer"></div>
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
