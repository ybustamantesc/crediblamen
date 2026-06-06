<?php // Menú lateral para módulo de contabilidad (estilo moderno) ?>
<style>
    /* Sidebar content sizing is handled by the global theme CSS to keep behavior consistent */

    .app-sidebar.servicont-sidebar {
        background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
    }
    
    .servicont-sidebar .sidebar-header {
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        /* Match global theme header height to keep calc(100vh - 60px) accurate */
        padding: 13px 15px;
        height: 60px;
        box-sizing: border-box;
    }
    
    .servicont-sidebar .header-brand {
        display: flex;
        align-items: center;
    }
    
    .servicont-logo-container {
        width: 45px;
        height: 45px;
        background: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    .servicont-logo-icon {
        font-size: 24px;
        color: #2a5298;
    }
    
    /* Profile header placed inside the sidebar header to keep header height = 60px
       so that .sidebar-content height = calc(100vh - 60px) remains correct. */
    .servicont-sidebar .sidebar-profile-header {
        color: #ffffff;
        padding-left: 12px;
        padding-right: 12px;
    }

    /* Use the same readable text colors as the main sidebar */
    .servicont-sidebar .sidebar-profile-header .profile-name {
        color: #222 !important;
        font-size: 13px;
    }
    .servicont-sidebar .sidebar-profile-header .profile-role {
        color: #666 !important;
        font-size: 11px;
    }
    
    .servicont-sidebar .profile-img {
        width: 48px;
        height: 48px;
        background: #ffffff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .servicont-sidebar .profile-img i {
        font-size: 24px;
        color: #2a5298;
    }
    
    .servicont-sidebar .profile-name {
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    
    .servicont-sidebar .profile-role {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    .servicont-sidebar .nav-lavel {
        color: rgba(255, 255, 255, 0.6);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1px;
        margin: 20px 20px 10px;
    }
    
    .servicont-sidebar .nav-item a {
        color: rgba(255, 255, 255, 0.9);
        padding: 12px 20px;
        border-radius: 8px;
        margin: 5px 15px;
        transition: all 0.3s ease;
    }
    
    .servicont-sidebar .nav-item a:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        transform: translateX(5px);
    }
    
    .servicont-sidebar .nav-item.active a {
        background: #ffffff;
        color: #2a5298;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    
    .servicont-sidebar .nav-item a i {
        margin-right: 12px;
        font-size: 18px;
    }

    .servicont-sidebar .nav-item.has-sub .submenu-content {
        padding: 0 10px 10px;
    }

    .servicont-sidebar .nav-item.has-sub .submenu-content .menu-item {
        display: block;
        width: auto;
        margin: 6px 15px;
        padding: 12px 16px;
        border-radius: 8px;
        text-align: left;
        white-space: normal;
        overflow-wrap: anywhere;
        line-height: 1.4;
    }
</style>

<div class="app-sidebar colored servicont-sidebar">
    <div class="sidebar-header">
        <a class="header-brand" href="<?php echo base_url('contabilidad'); ?>">
            <div class="servicont-logo-container">
                <i class="ik ik-briefcase servicont-logo-icon"></i>
            </div>
        </a>
        <?php $user = $this->ion_auth->user()->row(); $perfil = isset($user->perfil) ? $user->perfil : NULL; ?>
        <div class="sidebar-profile-header px-3 py-2" style="flex: 1;">
            <div class="profile-name font-weight-bold"><?php echo isset($user) ? ($user->first_name ?: ($user->username ?: $user->email)) : 'Usuario'; ?></div>
            <div class="profile-role small text-muted"><?php echo (isset($perfil) ? ($perfil == 1 ? 'Administrador' : ($perfil == 2 ? 'Supervisor' : 'Asesor')) : 'Usuario'); ?></div>
        </div>
        <!-- nav-toggle removed for full-view layout -->
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="conta-menu-navigation" class="navigation-main">
                <div class="nav-lavel">CONTABILIDAD</div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'index' || $this->router->fetch_method() == 'home' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad'); ?>"><i class="ik ik-home"></i><span>Inicio</span></a>
                </div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'catalogo' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/catalogo'); ?>"><i class="ik ik-layers"></i><span>Catálogo de Cuentas</span></a>
                </div>

                <!-- Enlace 'Importar Balanza' ocultado por solicitud del usuario -->
                <!--
                <div class="nav-item <?php echo ($this->router->fetch_method() == 'importar_balanza' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/importar_balanza'); ?>"><i class="ik ik-upload"></i><span>Importar Balanza</span></a>
                </div>
                -->

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'diario' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/diario'); ?>"><i class="ik ik-book-open"></i><span>Libro Diario</span></a>
                </div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'mayor' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/mayor'); ?>"><i class="ik ik-layers"></i><span>Libro Mayor</span></a>
                </div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'balanza' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/balanza'); ?>"><i class="ik ik-bar-chart-2"></i><span>Balanza de Comprobación</span></a>
                </div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'auxiliares' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/auxiliares'); ?>"><i class="ik ik-file-text"></i><span>Auxiliares</span></a>
                </div>

                <div class="nav-item has-sub <?php echo (in_array($this->router->fetch_method(), ['revaluacion']) ? 'active' : ''); ?>">
                    <a href="#"><i class="ik ik-repeat"></i><span>Procesos</span></a>
                    <div class="submenu-content">
                        <a href="<?php echo base_url('contabilidad/revaluacion'); ?>" class="menu-item">Reevaluación del Dólar</a>
                        <a href="<?php echo base_url('contabilidad/cierre_mensual'); ?>" class="menu-item">Cierre Mensual</a>
                    </div>
                </div>

                <div class="nav-item has-sub <?php echo (in_array($this->router->fetch_method(), ['balance','situacion_financiera','resultados','flujo']) ? 'active' : ''); ?>">
                    <a href="#"><i class="ik ik-pie-chart"></i><span>Estados Financieros</span></a>
                    <div class="submenu-content">
                        <a href="<?php echo base_url('contabilidad/situacion_financiera'); ?>" class="menu-item">Estado de Situación Financiera</a>
                        <a href="<?php echo base_url('contabilidad/resultados'); ?>" class="menu-item">Estado de Resultados</a>
                        <!-- Enlace 'Desagregación de Cuentas' ocultado por solicitud del usuario -->
                        <!-- <a href="<?php echo base_url('contabilidad/desagregacion_cuentas'); ?>" class="menu-item">Desagregación de Cuentas</a> -->
                        <!-- Enlace 'Flujo de Efectivo' ocultado por solicitud del usuario -->
                        <!-- <a href="<?php echo base_url('contabilidad/flujo'); ?>" class="menu-item">Flujo de Efectivo</a> -->
                    </div>
                </div>

                <div class="nav-lavel" style="margin-top:20px;">CONFIGURACIÓN</div>

                <div class="nav-item <?php echo ($this->router->fetch_method() == 'centros_costo' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('contabilidad/centros_costo'); ?>"><i class="fas fa-building"></i><span>Centros de Costo</span></a>
                </div>

                <div class="nav-item <?php echo ($this->router->fetch_class() == 'tasacambio' ? 'active' : ''); ?>">
                    <a href="<?php echo base_url('tasacambio'); ?>"><i class="fas fa-dollar-sign"></i><span>Tasa de Cambio</span></a>
                </div>

            </nav>
        </div>
    </div>
</div>
