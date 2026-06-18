<div class="menu-page">
    <div class="container py-5">
        <!-- Styles moved to public/css/branding.css -->

        <div class="menu-hero">
            <div class="menu-hero-brand">
                <img src="<?php echo base_url('Logo/Logo.png'); ?>" alt="Crediblamen" class="menu-logo">
                <div>
                    <span class="hero-eyebrow">Crediblamen</span>
                    <h1>Seleccionar módulo</h1>
                    <p>Ingresa a las secciones principales de CrediBlamen con un diseño moderno, rápido y confiable.</p>
                </div>
            </div>
            <div class="hero-actions">
                <a href="<?php echo site_url('login/logout'); ?>" class="btn-hero btn-hero-logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                <a href="<?php echo site_url('contabilidad'); ?>" class="btn-hero btn-hero-secondary d-none" id="btnIrContabilidad"><i class="fa fa-calculator"></i> Ir a Contabilidad</a>
            </div>
        </div>

        <div class="menu-grid">
        <?php if (isset($permissions) && $permissions['creditos']) : ?>
            <div class="menu-card">
                <a href="<?php echo base_url('home'); ?>" class="menu-btn btn-creditos"><i class="fas fa-money-bill-wave mr-2"></i> Créditos / Promotoria</a>
                <span class="menu-caption">Panel de créditos y promotoria</span>
            </div>
        <?php endif; ?>

        <!-- PLD module temporarily hidden -->

        <?php 
            $hasKonami = false;
            if (isset($permissions)) {
                if (isset($permissions['konami'])) { $hasKonami = (bool)$permissions['konami']; }
                elseif (isset($permissions['komani'])) { $hasKonami = (bool)$permissions['komani']; }
            }
        ?>
        <?php if ($hasKonami) : ?>
            <div class="menu-card">
                <a href="<?php echo base_url('index.php/konami'); ?>" class="menu-btn btn-konami"><i class="fas fa-landmark mr-2"></i> Conami / PLA</a>
                <span class="menu-caption">Módulo Conami / PLA</span>
            </div>
        <?php endif; ?>

        <?php if (isset($permissions) && $permissions['tesoreria']) : ?>
            <div class="menu-card">
                <a href="<?php echo site_url('tesoreria'); ?>" class="menu-btn btn-tesoreria">
                    <span class="menu-btn-icon"><i class="fas fa-wallet"></i></span>
                    <span class="menu-btn-text">Tesorería / Pagos</span>
                </a>
                <span class="menu-caption">Movimientos de caja, conciliaciones y pagos</span>
            </div>
            <div class="menu-card">
                <a href="<?php echo site_url('tesoreria/cobros'); ?>" class="menu-btn btn-cobros">
                    <span class="menu-btn-icon"><i class="fas fa-hand-holding-usd"></i></span>
                    <span class="menu-btn-text">Gestión de Cobros</span>
                </a>
                <span class="menu-caption">Cobros, seguimiento y gestión de pagos</span>
            </div>
        <?php endif; ?>

        <?php if (isset($permissions) && $permissions['contabilidad']) : ?>
            <div class="menu-card">
                <a href="<?php echo site_url('contabilidad'); ?>" class="menu-btn btn-contabilidad">
                    <span class="menu-btn-icon"><i class="fas fa-calculator"></i></span>
                    <span class="menu-btn-text">Contabilidad / Financiera</span>
                </a>
                <span class="menu-caption">Asientos, reportes y gestión financiera</span>
            </div>
        <?php endif; ?>

        <?php 
            $hasAdmin = false;
            if (isset($permissions)) {
                if (isset($permissions['administracion'])) { $hasAdmin = (bool)$permissions['administracion']; }
                elseif (isset($permissions['admin'])) { $hasAdmin = (bool)$permissions['admin']; }
            }
        ?>
        <!-- Administración module temporarily hidden -->
        <!-- Estadísticas accesible para todos -->
        <!-- Estadísticas temporarily hidden -->
    </div>

</div>