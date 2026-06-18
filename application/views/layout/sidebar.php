	<div class="nav-item <?php echo ($this->router->fetch_class() == 'analisis_financiero' ? 'active' : ''); ?>">
		<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>6. AnÃ¡lisis Financiero</span></a>
	</div>
<?php $is_home = ($this->router->fetch_class() == 'home'); ?>
<div class="app-sidebar colored <?php echo ($this->router->fetch_class() == 'tesoreria' ? 'sidebar-teso' : ''); ?>">
	<style>
		/* GENERIC SIDEBAR STYLES - Apply to all modules, not just tesorerÃ­a */
		.app-sidebar .navigation-main .nav-item a {
			display: flex !important;
			align-items: center !important;
			gap: 10px !important;
			padding: 12px 18px !important;
			margin-left: 8px !important;
			margin-right: 8px !important;
			overflow: visible !important;
			white-space: normal !important;
			width: auto !important;
			max-width: none !important;
			color: #333333 !important;
		}
		.app-sidebar .navigation-main .nav-item a:hover,
		.app-sidebar .navigation-main .nav-item.active > a {
			color: #ffffff !important;
		}
		.app-sidebar .navigation-main .nav-item a span {
			font-size: 13px !important;
			line-height: 1.4;
			flex: 1 1 auto;
			min-width: auto !important;
			width: auto !important;
			white-space: normal !important;
			overflow: visible !important;
			overflow-wrap: anywhere !important;
			word-break: break-word !important;
			color: inherit !important;
		}
		.app-sidebar .navigation-main .nav-item a:hover span,
		.app-sidebar .navigation-main .nav-item.active > a span {
			color: #ffffff !important;
		}

		.app-sidebar .navigation-main .nav-item.has-sub > a.menu-group-title,
		.app-sidebar .navigation-main .nav-item.has-sub > a.menu-group-title span {
			color: #1f2937 !important;
		}

		/* Generic menu items (used in Consultas and other dropdowns) */
		.app-sidebar .navigation-main .submenu-content {
			max-width: none !important;
			width: auto !important;
			min-width: 250px !important;
		}
		.app-sidebar .navigation-main .nav-item:not(.open) .submenu-content {
			display: none !important;
		}
		.app-sidebar .navigation-main .nav-item.open .submenu-content {
			display: flex !important;
			flex-direction: column !important;
		}
		.app-sidebar .navigation-main .submenu-content .menu-item {
			display: flex !important;
			align-items: center !important;
			justify-content: flex-start;
			gap: 10px;
			background: #ffffff;
			color: #1f2937 !important;
			border: 1px solid #e3e8f2;
			border-radius: 12px;
			padding: 12px 14px;
			margin: 0 0 10px;
			font-size: 13px;
			font-weight: 600;
			line-height: 1.35;
			box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
			min-height: 48px;
			text-decoration: none;
			overflow: visible !important;
			white-space: normal !important;
			word-break: break-word;
			overflow-wrap: break-word;
			max-width: none !important;
			width: auto !important;
			box-sizing: border-box !important;
		}
		.app-sidebar .navigation-main .submenu-content .menu-item span {
			display: block;
			flex: 1 1 auto;
			color: #1f2937 !important;
			font-size: 13px !important;
			line-height: 1.35;
			white-space: normal;
			overflow: visible !important;
			overflow-wrap: anywhere;
			word-break: break-word;
			min-width: 0 !important;
		}
		.app-sidebar .navigation-main .submenu-content .menu-item:hover {
			background: #3a4d63;
			border-color: #4a5d7a;
			color: #ffffff !important;
		}
		.app-sidebar .navigation-main .submenu-content .menu-item:hover span {
			color: #ffffff !important;
		}

		/* Improve sidebar readability for tesorerÃ­a and preserve colored sidebar aesthetic */
		.app-sidebar.sidebar-teso .sidebar-header {
			background-color: #272d36 !important;
		}
		.app-sidebar.sidebar-teso .sidebar-content {
			background-color: #404e67 !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .nav-item a {
			display: flex !important;
			align-items: center !important;
			gap: 10px !important;
			padding: 12px 18px !important;
			margin-left: 8px !important;
			margin-right: 8px !important;
			overflow: visible !important;
			white-space: normal !important;
			color: #ffffff !important;
			width: auto !important;
			max-width: none !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .nav-item a i {
			color: #bcc8d8 !important;
			flex: 0 0 auto !important;
			min-width: 24px !important;
			width: 24px !important;
			text-align: center !important;
			font-size: 18px !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .nav-item a span {
			color: #ffffff !important;
			font-size: 13px !important;
			line-height: 1.4;
			flex: 1 1 auto;
			min-width: auto !important;
			width: auto !important;
			white-space: normal !important;
			overflow: visible !important;
			overflow-wrap: anywhere !important;
			word-break: break-word !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .nav-item a:hover {
			background-color: rgba(255, 255, 255, 0.1) !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .nav-item a:hover span {
			color: #ffffff !important;
		}
		.app-sidebar.sidebar-teso .sidebar-profile .profile-name,
		.app-sidebar.sidebar-teso .sidebar-profile .profile-role,
		.app-sidebar.sidebar-teso .navigation-main .nav-lavel {
			color: #ffffff !important;
		}

		/* TesorerÃ­a: menÃº tipo botones (coordinado con otros mÃ³dulos) */
		.app-sidebar.sidebar-teso .navigation-main .nav-item.has-sub .submenu-content {
			background-color: #4a5872 !important;
			padding: 12px 12px !important;
			max-width: none !important;
			width: auto !important;
			min-width: 250px !important;
			display: flex !important;
			flex-direction: column !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item {
			display: flex !important;
			align-items: center !important;
			justify-content: flex-start;
			gap: 10px;
			background: #ffffff;
			color: #1f2937 !important;
			border: 1px solid #e3e8f2;
			border-radius: 12px;
			padding: 12px 14px;
			margin: 0 0 10px;
			font-size: 13px;
			font-weight: 600;
			line-height: 1.35;
			box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
			min-height: 48px;
			text-decoration: none;
			overflow: visible !important;
			white-space: normal !important;
			word-break: break-word;
			overflow-wrap: break-word;
			max-width: none !important;
			width: auto !important;
			box-sizing: border-box !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item i {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			width: 42px !important;
			height: 42px !important;
			min-width: 42px !important;
			flex: 0 0 42px !important;
			line-height: 42px !important;
			font-size: 18px !important;
			text-align: center !important;
			color: inherit !important;
			background: transparent !important;
			border-radius: 10px !important;
			padding: 0 !important;
			box-sizing: border-box !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item span {
			display: block;
			flex: 1 1 auto;
			color: #1f2937 !important;
			font-size: 13px !important;
			line-height: 1.35;
			white-space: normal;
			overflow: visible !important;
			overflow-wrap: anywhere;
			word-break: break-word;
			min-width: 0 !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item:hover {
			background: #3a4d63;
			border-color: #4a5d7a;
			color: #ffffff !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item:hover span {
			color: #ffffff !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item.active {
			background: linear-gradient(120deg, #2f5d87 0%, #3a6e97 100%);
			border-color: #3a6e97;
			color: #ffffff !important;
			box-shadow: 0 6px 14px rgba(47, 93, 135, .35);
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item.active i,
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item.active span {
			color: #ffffff !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item:last-child {
			margin-bottom: 0;
		}
	</style>
	<div class="sidebar-header">
		<a class="header-brand" href="<?php echo base_url('/'); ?>">
			<div class="logo-img logo-wrap">
				<img src="<?php echo base_url('public/img/logo.jpg'); ?>" class="header-brand-img rounded-circle" alt="logo" width="36" height="36">
			</div>
			<!-- brand text removed to keep a cleaner, logo-only header -->
		</a>
		<?php $user = $this->ion_auth->user()->row(); $perfil = isset($user->perfil) ? $user->perfil : NULL; ?>
		<div class="sidebar-profile-header px-3 py-2" style="flex: 1;">
			<div class="profile-name font-weight-bold" style="font-size: 13px; color: #222;"><?php echo isset($user) ? ($user->first_name ?: ($user->username ?: $user->email)) : 'Usuario'; ?></div>
			<?php
			// Try to show the user's group name if possible (Ion Auth)
			$role_label = 'Usuario';
			try {
				if (isset($this->ion_auth) && method_exists($this->ion_auth, 'get_users_groups')) {
					$g = $this->ion_auth->get_users_groups($user->id)->row();
					if ($g && isset($g->name)) {
						$role_label = $g->name;
					}
				}
			} catch (Exception $e) { /* ignore */ }
			?>
			<div class="profile-role small text-muted" style="font-size: 11px; color: #666;"><?php echo htmlspecialchars($role_label); ?></div>
		</div>
		<!-- nav-toggle removed for full-view layout -->
		<button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
	</div>

<?php 
$perfil = isset($user->perfil) ? $user->perfil : NULL; 
$is_contab = ($this->router->fetch_class() == 'contabilidad');
$is_pld = ($this->router->fetch_class() == 'pld');
$is_teso = ($this->router->fetch_class() == 'tesoreria');
$is_konami = ($this->router->fetch_class() == 'konami');
$is_admin = ($this->router->fetch_class() == 'administracion'); 

// Detect promotor role: either Ion Auth group 'promotor' or legacy perfil == 4
$is_promotor = false;
try {
    if (isset($this->ion_auth) && method_exists($this->ion_auth, 'in_group')) {
        $is_promotor = $this->ion_auth->in_group('promotor') || ($perfil == 4);
    } else {
        $is_promotor = ($perfil == 4);
    }
} catch (Exception $e) {
    $is_promotor = ($perfil == 4);
}
?>

<?php /* When inside the AdministraciÃ³n module we render only the shortcuts (Atajos)
   server-side to avoid flicker and to keep the sidebar focused. */ ?>

<div class="sidebar-content">
		<!-- Perfil movido al sidebar-header -->
<?php if (isset($is_admin) && $is_admin): ?>
		<div class="nav-container">
			<nav id="main-menu-navigation" class="navigation-main">
				<div class="nav-lavel">Atajos</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion'); ?>"><i class="fas fa-cogs"></i><span>Inicio</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'usuarios' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/usuarios'); ?>"><i class="ik ik-users"></i><span>GestiÃ³n de Usuarios</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'roles' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/roles'); ?>"><i class="ik ik-lock"></i><span>Roles y Permisos</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'configuracion' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/configuracion'); ?>"><i class="ik ik-settings"></i><span>ConfiguraciÃ³n General</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'seguridad' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/seguridad'); ?>"><i class="ik ik-shield"></i><span>Seguridad</span></a>
				</div>

					<!-- Moved: Tipos de Productos to the end of the main menu -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'tipos_productos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('tipos_productos'); ?>"><i class="fas fa-tags"></i><span>Tipos de Productos</span></a>
					</div>
				<!-- Tasa de Cambio -->
				<div class="nav-item <?php echo ($this->router->fetch_class() == 'tasacambio' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('tasacambio'); ?>"><i class="fas fa-dollar-sign"></i><span>Tasa de Cambio</span></a>
				</div>			</nav>
		</div>
<?php endif; ?>

		<div class="nav-container">
			<nav id="main-menu-navigation" class="navigation-main">
				<div class="nav-lavel">MENÃš PRINCIPAL</div>
				<?php // Mostrar menÃº de Contabilidad siempre que estemos en ese mÃ³dulo para facilitar navegaciÃ³n ?>
				<?php if (isset($is_contab) && $is_contab) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-calculator"></i><span>Contabilidad</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('contabilidad'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('contabilidad/catalogo'); ?>" class="menu-item">CatÃ¡logo de Cuentas</a>
							<!-- Transacciones label + enlaces (visible dentro de Contabilidad) -->
							<div style="padding:6px 14px 0 14px; color:#444; font-weight:600;">Transacciones</div>
							<a href="<?php echo base_url('contabilidad/diario'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'diario' ? 'active' : ''); ?>">Libro Diario</a>
							<a href="<?php echo base_url('contabilidad/mayor'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'mayor' ? 'active' : ''); ?>">Libro Mayor</a>
							<a href="<?php echo base_url('contabilidad/balanza'); ?>" class="menu-item">Balanza</a>
							<a href="<?php echo base_url('contabilidad/balance'); ?>" class="menu-item">Balance General</a>
							<a href="<?php echo base_url('contabilidad/resultados'); ?>" class="menu-item">Estado de Resultados</a>
							<a href="<?php echo base_url('contabilidad/flujo'); ?>" class="menu-item">Flujo de Efectivo</a>
							<a href="<?php echo base_url('contabilidad/revaluacion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'revaluacion' ? 'active' : ''); ?>">ReevaluaciÃ³n del DÃ³lar</a>
						</div>
					</div>
				<?php endif; ?>
					<?php /* perfil already defined above */ ?>
					<?php
					// Fallback: si la lÃ³gica de roles no imprime elementos, mostrar enlaces esenciales
					$has_role_menu = (
						($perfil === 1) || ($perfil === 2) || (! empty($is_promotor)) || (! empty($is_admin)) || $is_contab || $is_pld || $is_teso || $is_konami
					);
					// MenÃº fallback oculto - se usa el menÃº especÃ­fico por perfil
					if (false && ! $has_role_menu) : ?>
						<div class="nav-item">
							<a href="<?php echo base_url('/'); ?>"><i class="ik ik-home"></i><span>Inicio</span></a>
						</div>
						<div class="nav-item">
							<a href="<?php echo base_url('clientes'); ?>"><i class="ik ik-user-check"></i><span>Clientes</span></a>
						</div>
					<!-- Contratos oculto -->
					<!--
					<div class="nav-item">
						<a href="<?php echo base_url('contratos'); ?>"><i class="fas fa-file-contract"></i><span>Contratos</span></a>
					</div>
					-->
					<?php endif; ?>
				<?php if ($perfil == 1) : ?>
					<?php if (! $is_contab && ! $is_pld && ! $is_teso && ! $is_konami && ! $is_admin) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'home' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="left" title="Inicio" href="<?php echo base_url('/'); ?> "><i class="ik ik-home"></i><span>Inicio</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Clientes" href="<?php echo base_url('clientes'); ?>"><i class="ik ik-user-check"></i><span>Clientes</span></a>
					</div>
					<!-- moved: Rutas (antes Asesores) -->
					<!-- Promotores no muestran enlace a Rutas -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'rechazados' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Clientes Rechazados" href="<?php echo base_url('clientes/rechazados'); ?>"><i class="ik ik-user-minus"></i><span>Clientes Rechazados</span></a>
					</div>

					<!-- Contracts link moved later (after Emision Plan de Pago) -->
					<?php endif; ?>

					<?php if ($is_pld) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-shield-alt"></i><span>PLD / Cumplimiento</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('pld'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('pld/kyc'); ?>" class="menu-item">KYC</a>
							<a href="<?php echo base_url('pld/monitoreo'); ?>" class="menu-item">Monitoreo</a>
							<a href="<?php echo base_url('pld/riesgo'); ?>" class="menu-item">Riesgo</a>
							<a href="<?php echo base_url('pld/alertas'); ?>" class="menu-item">Alertas</a>
							<a href="<?php echo base_url('pld/reportes'); ?>" class="menu-item">Reportes</a>
							<a href="<?php echo base_url('pld/expediente'); ?>" class="menu-item">Expediente</a>
							<a href="<?php echo base_url('pld/bitacora'); ?>" class="menu-item">BitÃ¡cora</a>
						</div>
					</div>
					<?php elseif ($is_teso) : ?>
					<div class="nav-item has-sub <?php echo ((in_array($this->router->fetch_method(), array('bancos','conciliacion','reporte_bancos')) || ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'banco')) ? 'active open' : ''); ?>">
						<a href="#" class="menu-group-title"><i class="fas fa-university"></i><span>Banco</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria/bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'bancos' ? 'active' : ''); ?>"><i class="fas fa-university"></i><span>Banco</span></a>
							<a href="<?php echo base_url('tesoreria/movimientos?modo=banco'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'banco' ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span>Movimientos</span></a>
					<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'conciliacion' ? 'active' : ''); ?>"><i class="fas fa-file-alt"></i><span>Estados de Cuenta</span></a>
							<a href="<?php echo base_url('tesoreria/reporte_bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reporte_bancos' ? 'active' : ''); ?>"><i class="fas fa-chart-bar"></i><span>Reporte de Bancos</span></a>
						</div>
					</div>
					<div class="nav-item has-sub <?php echo (in_array($this->router->fetch_method(), array('cajas','pagos','movimientos','arqueos','cobros','reporte_caja')) ? 'active open' : ''); ?>">
						<a href="#" class="menu-group-title"><i class="fas fa-cash-register"></i><span>Caja</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria/cajas'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cajas' ? 'active' : ''); ?>"><i class="fas fa-cash-register"></i><span>Caja</span></a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'pagos' ? 'active' : ''); ?>"><i class="fas fa-credit-card"></i><span>Pagos de CrÃ©dito</span></a>
					<a href="<?php echo base_url('tesoreria/movimientos?modo=caja'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'caja' ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span>Movimientos</span></a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'arqueos' ? 'active' : ''); ?>"><i class="fas fa-coins"></i><span>Arqueo de CrÃ©ditos</span></a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cobros' ? 'active' : ''); ?>"><i class="fas fa-hand-holding-usd"></i><span>Cobros</span></a>
							<a href="<?php echo base_url('tesoreria/reporte_caja'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reporte_caja' ? 'active' : ''); ?>"><i class="fas fa-chart-bar"></i><span>Reporte de Caja</span></a>
						</div>
					</div>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-wallet"></i><span>TesorerÃ­a</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>"><i class="fas fa-home"></i><span>Inicio</span></a>
							<a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cajas_bancos' ? 'active' : ''); ?>"><i class="fas fa-cogs"></i><span>ConfiguraciÃ³n Cuentas</span></a>
							<a href="<?php echo base_url('tesoreria/flujo'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'flujo' ? 'active' : ''); ?>"><i class="fas fa-chart-line"></i><span>Flujo de Efectivo</span></a>
							<a href="<?php echo base_url('tesoreria/integracion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'integracion' ? 'active' : ''); ?>"><i class="fas fa-link"></i><span>IntegraciÃ³n Bancaria</span></a>
							<a href="<?php echo base_url('tesoreria/reportes'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reportes' ? 'active' : ''); ?>"><i class="fas fa-file-alt"></i><span>ReporterÃ­a</span></a>
							<!-- Seguridad y Roles removed for TesorerÃ­a -->
						</div>
					</div>
					<?php elseif ($is_konami) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-landmark"></i><span>Conami / CONAMI</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('konami'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('konami/informes'); ?>" class="menu-item">InformaciÃ³n Institucional</a>
							<a href="<?php echo base_url('konami/cartera'); ?>" class="menu-item">Seguimiento Cartera</a>
							<a href="<?php echo base_url('konami/pld'); ?>" class="menu-item">Usuarios PLD/FT</a>
							<a href="<?php echo base_url('konami/inusuales'); ?>" class="menu-item">Operaciones Inusuales</a>
							<a href="<?php echo base_url('konami/gobierno'); ?>" class="menu-item">Gobierno Corporativo</a>
							<a href="<?php echo base_url('konami/riesgos'); ?>" class="menu-item">GestiÃ³n de Riesgos</a>
							<a href="<?php echo base_url('konami/financiero'); ?>" class="menu-item">Reporte Financiero</a>
							<a href="<?php echo base_url('konami/limites'); ?>" class="menu-item">LÃ­mites Regulatorios</a>
							<a href="<?php echo base_url('konami/integracion'); ?>" class="menu-item">IntegraciÃ³n Contable</a>
							<a href="<?php echo base_url('konami/auditoria'); ?>" class="menu-item">AuditorÃ­a Interna</a>
						</div>
					</div>
					<?php else: ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes'); ?>"><i class="fas fa-file-signature"></i><span>1. Solicitud Inicial</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'uso_credito' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/uso_credito'); ?>"><i class="fas fa-file-alt"></i><span>2. Uso CrÃ©dito</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'referencias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/referencias'); ?>"><i class="fas fa-user-friends"></i><span>3. Referencias</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'perfil_integral' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('perfil_integral'); ?>"><i class="fas fa-id-card"></i><span>4. PIC</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'garantias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('garantias'); ?>"><i class="fas fa-shield-alt"></i><span>5. GarantÃ­as</span></a>
					</div>

					<!-- Acceso rÃ¡pido: 6. AnÃ¡lisis Financiero -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'analisis_financiero' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>6. AnÃ¡lisis Financiero</span></a>
					</div>

					<!-- FAF submenu (hidden temporarily) -->
					<?php if (false): ?>
					<div class="nav-item has-sub <?php echo ($this->router->fetch_class() == 'solicitudes' && in_array($this->router->fetch_method(), array('faf_asalariado','faf_comerciante')) ? 'active open' : ''); ?>">
						<a href="#"><i class="fas fa-chart-pie"></i><span>6. FAF - Analisis Financiero</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('solicitudes/faf_asalariado'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'faf_asalariado' ? 'active' : ''); ?>">FAF - Asalariado</a>
							<a href="<?php echo base_url('solicitudes/faf_comerciante'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'faf_comerciante' ? 'active' : ''); ?>">FAF - Comerciante</a>
						</div>
					</div>
					<?php endif; ?>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'validacion_aprobacion' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/validacion_aprobacion'); ?>"><i class="fas fa-check-square"></i><span>7. ComitÃ© de Aprobaciones</span></a>
					</div>
					<!-- Nuevo acceso rÃ¡pido: EmisiÃ³n Plan de Pago -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'prestamo' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('prestamo'); ?>"><i class="fas fa-file-invoice-dollar"></i><span>8. EmisiÃ³n Plan de Pago</span></a>
					</div>
					<!-- 9. Contratos (oculto) -->
					<!--
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'contratos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('contratos'); ?>"><i class="fas fa-file-contract"></i><span>9. Contratos</span></a>
					</div>
					-->

					<!-- 10. Planes de CrÃ©dito: accesible desde sidebar debajo de Contratos -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'planescredito' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('planescredito'); ?>"><i class="fas fa-list-alt"></i><span>9. Planes de Pago</span></a>
					</div>
					   <div class="nav-item <?php echo ($this->router->fetch_class() == 'pagos' ? 'active' : ''); ?>">
						   <a href="<?php echo base_url('pagos'); ?>"><i class="fas fa-comment-dollar"></i><span>10. Pagos</span></a>
					   </div>
					   <div class="nav-item <?php echo ($this->router->fetch_class() == 'desembolsos' ? 'active' : ''); ?>">
						   <a href="<?php echo base_url('desembolsos'); ?>"><i class="fas fa-money-check-alt"></i><span>11. Desembolsos Programados</span></a>
					   </div>
					<!-- Planes de Pago hidden from sidebar per request -->
					<!-- Contracts link kept later after Emision Plan de Pago -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'prestamospagados' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('prestamospagados'); ?>"><i class="fas fa-check-circle"></i><span>CrÃ©ditos Pagados</span></a>
					</div>
					<?php endif; ?>

					<?php if (! $is_contab && ! $is_pld && ! $is_teso && ! $is_konami && ! $is_admin) : ?>
					<!-- Caja (hidden per request) -->
					<div class="nav-item has-sub">
						<a href="#"><i class="fas fa-file-pdf"></i><span>Consultas</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">CrÃ©ditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">CrÃ©ditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">CrÃ©ditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">CrÃ©ditos por Asesor,<br> Fecha y Estado</a>
							<a href="<?php echo base_url('reporte/estadocuentacliente'); ?>" class="menu-item">Estado de Cuenta por Cliente</a>
							<a href="<?php echo base_url('reporte/estadocuentafechas'); ?>" class="menu-item">Estado de Cuenta por Fechas</a>
							<a href="<?php echo base_url('reporte/pagosclientesfechas'); ?>" class="menu-item">Pagos por Clientes y Fechas</a>
							<a href="<?php echo base_url('reporte/pagosestado'); ?>" class="menu-item">Pagos por Estado y Fechas</a>
						</div>
					</div>
					<!-- Plan de Pago report link hidden per request -->

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'tipos_productos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('tipos_productos'); ?>"><i class="fas fa-box-open"></i><span>Productos</span></a>
					</div>
					<!-- Series de Recibos: control de series y consecutivos de recibos de abonos -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'series_recibos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('series_recibos'); ?>"><i class="fas fa-receipt"></i><span>Series de Recibos</span></a>
					</div>
					<?php endif; ?>

					<div class="nav-lavel">Administrador</div>
					<?php if (! $is_contab && ! $is_pld && ! $is_teso && ! $is_konami) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'usuarios' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('usuarios'); ?>"><i class="ik ik-users"></i><span>Usuarios</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'feriados' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('feriados'); ?>"><i class="fas fa-calendar-alt"></i><span>Feriados</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'sistema' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('sistema'); ?>"><i class="ik ik-settings"></i><span>Sistema</span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (!empty($is_promotor)) : ?>
					<!-- Promotor: limited menu (solicitudes, uso_credito, referencias, PIC, garantias, clientes, asesores, clientes rechazados) -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes'); ?>"><i class="fas fa-file-signature"></i><span>1.Solicitud Inicial</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'uso_credito' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/uso_credito'); ?>"><i class="fas fa-file-alt"></i><span>2.Uso Credito</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'referencias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/referencias'); ?>"><i class="fas fa-user-friends"></i><span>3. Referencias</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'perfil_integral' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('perfil_integral'); ?>"><i class="fas fa-id-card"></i><span>4. PIC</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'garantias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('garantias'); ?>"><i class="fas fa-shield-alt"></i><span>5. Garantias</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('clientes'); ?>"><i class="ik ik-user-check"></i><span>Clientes</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'asesores' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('asesores'); ?>"><i class="ik ik-users"></i><span>Rutas</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'rechazados' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('clientes/rechazados'); ?>"><i class="ik ik-user-minus"></i><span>Clientes Rechazados</span></a>
					</div>
				<?php endif; ?>
				<?php if ($perfil == 2) : ?>
					<?php if (! $is_contab && ! $is_pld && ! $is_teso) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'home' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="left" title="Inicio" href="<?php echo base_url('/'); ?> "><i class="ik ik-home"></i><span>Inicio</span></a>
					</div>
					<?php endif; ?>
					<!-- Clientes duplicado - oculto -->
					<?php if (false && ! $is_contab && ! $is_pld && ! $is_teso) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Clientes" href="<?php echo base_url('clientes'); ?>"><i class="ik ik-user-check"></i><span>Clientes</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'rechazados' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Clientes Rechazados" href="<?php echo base_url('clientes/rechazados'); ?>"><i class="ik ik-user-minus"></i><span>Clientes Rechazados</span></a>
					</div>
					<?php endif; ?>
					<?php if ($is_konami) : ?>
					<div class="nav-item has-sub <?php echo ((in_array($this->router->fetch_method(), array('bancos','conciliacion','reporte_bancos')) || ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'banco')) ? 'active open' : ''); ?>">
						<a href="#" class="menu-group-title"><i class="fas fa-university"></i><span>Banco</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria/bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'bancos' ? 'active' : ''); ?>"><i class="fas fa-university"></i><span>Banco</span></a>
					<a href="<?php echo base_url('tesoreria/movimientos?modo=banco'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'banco' ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span>Movimientos</span></a>
							<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'conciliacion' ? 'active' : ''); ?>"><i class="fas fa-check-circle"></i><span>ConciliaciÃ³n</span></a>
							<a href="<?php echo base_url('tesoreria/reporte_bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reporte_bancos' ? 'active' : ''); ?>"><i class="fas fa-chart-bar"></i><span>Reporte de Bancos</span></a>
						</div>
					</div>
					<div class="nav-item has-sub <?php echo (in_array($this->router->fetch_method(), array('cajas','pagos','movimientos','arqueos','cobros','reporte_caja')) ? 'active open' : ''); ?>">
						<a href="#" class="menu-group-title"><i class="fas fa-cash-register"></i><span>Caja</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria/cajas'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cajas' ? 'active' : ''); ?>"><i class="fas fa-cash-register"></i><span>Caja</span></a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'pagos' ? 'active' : ''); ?>"><i class="fas fa-credit-card"></i><span>Pagos de CrÃ©dito</span></a>
							<a href="<?php echo base_url('tesoreria/movimientos?modo=caja'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'movimientos' && $this->input->get('modo') === 'caja' ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span>Movimientos</span></a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'arqueos' ? 'active' : ''); ?>"><i class="fas fa-coins"></i><span>Arqueo de CrÃ©ditos</span></a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cobros' ? 'active' : ''); ?>"><i class="fas fa-hand-holding-usd"></i><span>Cobros</span></a>
							<a href="<?php echo base_url('tesoreria/reporte_caja'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reporte_caja' ? 'active' : ''); ?>"><i class="fas fa-chart-bar"></i><span>Reporte de Caja</span></a>
						</div>
					</div>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-wallet"></i><span>TesorerÃ­a</span></a>
						<div class="submenu-content">
					<?php elseif ($is_admin) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-cogs"></i><span>AdministraciÃ³n</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('administracion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('administracion/usuarios'); ?>" class="menu-item">Usuarios</a>
							<a href="<?php echo base_url('administracion/roles'); ?>" class="menu-item">Roles y Permisos</a>
							<a href="<?php echo base_url('administracion/configuracion'); ?>" class="menu-item">ConfiguraciÃ³n General</a>
							<a href="<?php echo base_url('administracion/seguridad'); ?>" class="menu-item">Seguridad</a>
							<a href="<?php echo base_url('administracion/auditoria'); ?>" class="menu-item">AuditorÃ­a</a>
							<a href="<?php echo base_url('administracion/parametros'); ?>" class="menu-item">ParÃ¡metros de MÃ³dulos</a>
							<a href="<?php echo base_url('administracion/catalogos'); ?>" class="menu-item">CatÃ¡logos</a>
							<a href="<?php echo base_url('administracion/integraciones'); ?>" class="menu-item">Integraciones</a>
							<a href="<?php echo base_url('administracion/respaldo'); ?>" class="menu-item">Respaldos</a>
							<a href="<?php echo base_url('administracion/plantillas'); ?>" class="menu-item">Plantillas</a>
						</div>
					</div>
					<?php elseif ($is_konami) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-landmark"></i><span>Conami / CONAMI</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('konami'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('konami/informes'); ?>" class="menu-item">InformaciÃ³n Institucional</a>
							<a href="<?php echo base_url('konami/cartera'); ?>" class="menu-item">Seguimiento Cartera</a>
							<a href="<?php echo base_url('konami/pld'); ?>" class="menu-item">Usuarios PLD/FT</a>
							<a href="<?php echo base_url('konami/inusuales'); ?>" class="menu-item">Operaciones Inusuales</a>
							<a href="<?php echo base_url('konami/gobierno'); ?>" class="menu-item">Gobierno Corporativo</a>
							<a href="<?php echo base_url('konami/riesgos'); ?>" class="menu-item">GestiÃ³n de Riesgos</a>
							<a href="<?php echo base_url('konami/financiero'); ?>" class="menu-item">Reporte Financiero</a>
							<a href="<?php echo base_url('konami/limites'); ?>" class="menu-item">LÃ­mites Regulatorios</a>
							<a href="<?php echo base_url('konami/integracion'); ?>" class="menu-item">IntegraciÃ³n Contable</a>
							<a href="<?php echo base_url('konami/auditoria'); ?>" class="menu-item">AuditorÃ­a Interna</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if (! $is_contab && ! $is_pld && ! $is_teso && ! $is_konami && ! $is_admin) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'caja' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Caja" href="<?php echo base_url('caja'); ?>"><i class="fas fa-box"></i><span>Caja</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'pagos' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Pagos" href="<?php echo base_url('pagos'); ?>"><i class="fas fa-comment-dollar"></i><span>Pagos</span></a>
					</div>
					<?php endif; ?>
					<?php if (! $is_pld && ! $is_teso && ! $is_konami) : ?>
					<div class="nav-item has-sub">
						<a href="#"><i class="fas fa-file-pdf"></i><span>Consultas</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">CrÃ©ditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">CrÃ©ditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">CrÃ©ditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">CrÃ©ditos por Asesor,<br> Fecha y Estado</a>
							<a href="<?php echo base_url('reporte/estadocuentacliente'); ?>" class="menu-item">Estado de Cuenta por Cliente</a>
							<a href="<?php echo base_url('reporte/estadocuentafechas'); ?>" class="menu-item">Estado de Cuenta por Fechas</a>
							<a href="<?php echo base_url('reporte/pagosclientesfechas'); ?>" class="menu-item">Pagos por Clientes y Fechas</a>
							<a href="<?php echo base_url('reporte/pagosestado'); ?>" class="menu-item">Pagos por Estado y Fechas</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if (! $is_pld && ! $is_teso && ! $is_konami) : ?>
					<!-- 'Plan de Pago' (report) hidden per request -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'analisis_financiero' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>9. AnÃ¡lisis Financiero</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'tipos_productos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('tipos_productos'); ?>"><i class="fas fa-box-open"></i><span>Productos</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'tipos_productos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('tipos_productos'); ?>"><i class="fas fa-box-open"></i><span>Productos</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'tipos_productos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('tipos_productos'); ?>"><i class="fas fa-box-open"></i><span>Productos</span></a>
					</div>
					<?php endif; ?>

				<?php endif; ?>
				<?php if ($perfil == 3) : ?>

					<?php if (! $is_contab && ! $is_pld && ! $is_teso) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Clientes" href="<?php echo base_url('clientes'); ?>"><i class="ik ik-user-check"></i><span>Clientes</span></a>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'clientes' && $this->router->fetch_method() == 'rechazados' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Clientes Rechazados" href="<?php echo base_url('clientes/rechazados'); ?>"><i class="ik ik-user-minus"></i><span>Clientes Rechazados</span></a>
					</div>
					<?php endif; ?>
					<?php if ($is_teso) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-wallet"></i><span>TesorerÃ­a</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="menu-item">Bancario</a>
							<a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="menu-item">Documentos</a>
							<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item">Estados de Cuenta Bancarios</a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item">Pagos de Credito</a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item">Cobros</a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item">Arqueos de Credito</a>
							<a href="<?php echo base_url('tesoreria/flujo'); ?>" class="menu-item">Flujo de Efectivo</a>
							<a href="<?php echo base_url('tesoreria/integracion'); ?>" class="menu-item">IntegraciÃ³n Bancaria</a>
							<a href="<?php echo base_url('tesoreria/reportes'); ?>" class="menu-item">ReporterÃ­a</a>
							<!-- Seguridad y Roles removed for TesorerÃ­a -->
						</div>
					</div>
					<?php endif; ?>

					<?php if (! $is_pld && ! $is_teso) : ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'pagos' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Pagos" href="<?php echo base_url('pagos'); ?>"><i class="fas fa-comment-dollar"></i><span>Pagos</span></a>
					</div>
					<div class="nav-item has-sub">
						<a href="#"><i class="fas fa-file-pdf"></i><span>Consultas</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">CrÃ©ditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">CrÃ©ditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">CrÃ©ditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">CrÃ©ditos por Asesor,<br> Fecha y Estado</a>
							<a href="<?php echo base_url('reporte/estadocuentacliente'); ?>" class="menu-item">Estado de Cuenta por Cliente</a>
							<a href="<?php echo base_url('reporte/estadocuentafechas'); ?>" class="menu-item">Estado de Cuenta por Fechas</a>
							<a href="<?php echo base_url('reporte/pagosclientesfechas'); ?>" class="menu-item">Pagos por Clientes y Fechas</a>
							<a href="<?php echo base_url('reporte/pagosestado'); ?>" class="menu-item">Pagos por Estado y Fechas</a>
						</div>
					</div>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'pagos' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
						<a data-toggle="tooltip" data-placement="bottom" title="Administrar Pagos" href="<?php echo base_url('reporte/plandepago'); ?>"><i class="fas fa-comment-dollar"></i><span>Plan de Pago</span></a>
					</div>
					<?php endif; ?>


				<?php endif; ?>


			</nav>
		</div>
	</div>
</div>

<?php if (!empty($is_promotor)) : ?>
	<script>
		// Ensure promotor users don't see or access the 'Rutas' link in any sidebar variation.
		document.addEventListener('DOMContentLoaded', function(){
			try {
				document.querySelectorAll('.navigation-main a').forEach(function(a){
					var href = a.getAttribute('href') || '';
					var txt = (a.textContent || '').trim();
					if (href.indexOf('/asesores') !== -1 || txt === 'Rutas' || txt === ' Rutas') {
						var item = a.closest('.nav-item');
						if (item) item.parentNode.removeChild(item);
					}
				});
			} catch (e) { /* ignore */ }
		});
	</script>
<?php endif; ?>



