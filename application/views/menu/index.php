<div class="container py-5">
    <!-- Styles moved to public/css/branding.css -->

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Seleccionar Módulo</h2>
            <p class="text-muted">Elija a cuál sección desea ingresar</p>
        </div>
            <div>
            <a href="<?php echo site_url('contabilidad'); ?>" class="btn btn-sm btn-outline-primary mr-2 d-none" id="btnIrContabilidad"><i class="fa fa-calculator mr-1"></i> Ir a Contabilidad</a>
            <a href="<?php echo site_url('login/logout'); ?>" class="btn btn-sm btn-outline-danger" id="btnCerrarSesion"><i class="fa fa-sign-out-alt mr-1"></i> Cerrar sesión</a>
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
                <a href="<?php echo site_url('tesoreria'); ?>" class="menu-btn btn-tesoreria"><i class="fas fa-wallet mr-2"></i> Tesorería / Pagos</a>
                <span class="menu-caption">Movimientos de caja, conciliaciones y pagos</span>
            </div>
        <?php endif; ?>

        <?php if (isset($permissions) && $permissions['contabilidad']) : ?>
            <div class="menu-card">
                <a href="<?php echo site_url('contabilidad'); ?>" class="menu-btn btn-contabilidad"><i class="fas fa-calculator mr-2"></i> Contabilidad / Financiera</a>
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