	<div class="nav-item <?php echo ($this->router->fetch_class() == 'analisis_financiero' ? 'active' : ''); ?>">
		<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>6. Análisis Financiero</span></a>
	</div>
<?php $is_home = ($this->router->fetch_class() == 'home'); ?>
<div class="app-sidebar colored <?php echo ($this->router->fetch_class() == 'tesoreria' ? 'sidebar-teso' : ''); ?>">
	<style>
		/* Improve sidebar readability: darker text and slightly smaller items */
		.app-sidebar .navigation-main .nav-item a span {
			color: #222 !important;
			font-size: 13px !important;
		}
		.app-sidebar .navigation-main .nav-item a i {
			color: #333 !important;
		}
		.app-sidebar .sidebar-profile .profile-name {
			font-size: 14px;
		}
		.app-sidebar .nav-lavel {
			font-size: 12px;
			color: #444;
		}

		/* Tesorería: menú tipo botones (coordinado con otros módulos) */
		.app-sidebar.sidebar-teso .navigation-main .nav-lavel,
		.app-sidebar.sidebar-teso .sidebar-profile .profile-name,
		.app-sidebar.sidebar-teso .sidebar-profile .profile-role,
		.app-sidebar.sidebar-teso .navigation-main .nav-item.has-sub.active.open > a span,
		.app-sidebar.sidebar-teso .navigation-main .nav-item.has-sub.active.open > a i {
			color: #ffffff !important;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content {
			padding: 8px;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item {
			display: flex;
			align-items: flex-start;
			justify-content: flex-start;
			gap: 12px;
			background: #ffffff;
			color: #1f2937 !important;
			border: 1px solid #e3e8f2;
			border-radius: 8px;
			padding: 11px 12px;
			margin: 0 0 10px;
			font-size: 13px;
			font-weight: 600;
			line-height: 1.25;
			box-shadow: 0 2px 8px rgba(15, 23, 42, .08);
			min-height: 42px;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item i {
			width: 16px;
			min-width: 16px;
			flex: 0 0 16px;
			text-align: center;
			color: #4b5563 !important;
			font-size: 13px;
		}
		.app-sidebar.sidebar-teso .navigation-main .submenu-content .menu-item span {
			display: block;
			flex: 1 1 auto;
			color: inherit !important;
			font-size: 13px !important;
			line-height: 1.35;
			white-space: normal;
			overflow-wrap: anywhere;
			word-break: break-word;
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
		<button type="button" class="nav-toggle"><i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i></button>
		<button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
	</div>

	<?php $user = $this->ion_auth->user()->row(); $perfil = isset($user->perfil) ? $user->perfil : NULL; 
	$is_contab = ($this->router->fetch_class() == 'contabilidad');
	$is_pld = ($this->router->fetch_class() == 'pld');
	$is_teso = ($this->router->fetch_class() == 'tesoreria');
	$is_konami = ($this->router->fetch_class() == 'konami');
	$is_admin = ($this->router->fetch_class() == 'administracion'); ?>
<?php
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

<?php /* When inside the Administración module we render only the shortcuts (Atajos)
   server-side to avoid flicker and to keep the sidebar focused. */ ?>

<div class="sidebar-content">
	<div class="sidebar-profile px-3 py-2 d-flex align-items-center">
		<div class="profile-img mr-2">
			<img src="<?php echo base_url('public/img/logo.jpg'); ?>" alt="avatar" class="rounded-circle" width="44" height="44">
		</div>
		<div class="profile-info">
			<div class="profile-name font-weight-bold"><?php echo isset($user) ? ($user->first_name ?: ($user->username ?: $user->email)) : 'Usuario'; ?></div>
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
			<div class="profile-role small text-muted"><?php echo htmlspecialchars($role_label); ?></div>
		</div>
	</div>

<?php if (isset($is_admin) && $is_admin): ?>
		<div class="nav-container">
			<nav id="main-menu-navigation" class="navigation-main">
				<div class="nav-lavel">Atajos</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'index' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion'); ?>"><i class="fas fa-cogs"></i><span>Inicio</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'usuarios' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/usuarios'); ?>"><i class="ik ik-users"></i><span>Gestión de Usuarios</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'roles' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/roles'); ?>"><i class="ik ik-lock"></i><span>Roles y Permisos</span></a>
				</div>

				<div class="nav-item <?php echo ($this->router->fetch_class() == 'administracion' && $this->router->fetch_method() == 'configuracion' ? 'active' : ''); ?>">
					<a href="<?php echo base_url('administracion/configuracion'); ?>"><i class="ik ik-settings"></i><span>Configuración General</span></a>
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
				<div class="nav-lavel">MENÚ PRINCIPAL</div>
				<?php // Mostrar menú de Contabilidad siempre que estemos en ese módulo para facilitar navegación ?>
				<?php if (isset($is_contab) && $is_contab) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-calculator"></i><span>Contabilidad</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('contabilidad'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('contabilidad/catalogo'); ?>" class="menu-item">Catálogo de Cuentas</a>
							<!-- Transacciones label + enlaces (visible dentro de Contabilidad) -->
							<div style="padding:6px 14px 0 14px; color:#444; font-weight:600;">Transacciones</div>
							<a href="<?php echo base_url('contabilidad/diario'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'diario' ? 'active' : ''); ?>">Libro Diario</a>
							<a href="<?php echo base_url('contabilidad/mayor'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'mayor' ? 'active' : ''); ?>">Libro Mayor</a>
							<a href="<?php echo base_url('contabilidad/balanza'); ?>" class="menu-item">Balanza</a>
							<a href="<?php echo base_url('contabilidad/balance'); ?>" class="menu-item">Balance General</a>
							<a href="<?php echo base_url('contabilidad/resultados'); ?>" class="menu-item">Estado de Resultados</a>
							<a href="<?php echo base_url('contabilidad/flujo'); ?>" class="menu-item">Flujo de Efectivo</a>
							<a href="<?php echo base_url('contabilidad/revaluacion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'revaluacion' ? 'active' : ''); ?>">Reevaluación del Dólar</a>
						</div>
					</div>
				<?php endif; ?>
					<?php /* perfil already defined above */ ?>
					<?php
					// Fallback: si la lógica de roles no imprime elementos, mostrar enlaces esenciales
					$has_role_menu = (
						($perfil === 1) || ($perfil === 2) || (! empty($is_promotor)) || (! empty($is_admin)) || $is_contab || $is_pld || $is_teso || $is_konami
					);
					// Menú fallback oculto - se usa el menú específico por perfil
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
							<a href="<?php echo base_url('pld/bitacora'); ?>" class="menu-item">Bitácora</a>
						</div>
					</div>
					<?php elseif ($is_teso) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-wallet"></i><span>Tesorería</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>"><i class="fas fa-home"></i><span>Inicio</span></a>
							<a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cajas_bancos' ? 'active' : ''); ?>"><i class="fas fa-university"></i><span>Bancario</span></a>
							<a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'movimientos' ? 'active' : ''); ?>"><i class="fas fa-exchange-alt"></i><span>Documentos</span></a>
							<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'conciliacion' ? 'active' : ''); ?>"><i class="fas fa-check-circle"></i><span>Conciliación</span></a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'pagos' ? 'active' : ''); ?>"><i class="fas fa-credit-card"></i><span>Pagos de Credito</span></a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'cobros' ? 'active' : ''); ?>"><i class="fas fa-hand-holding-usd"></i><span></span></a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'arqueos' ? 'active' : ''); ?>"><i class="fas fa-cash-register"></i><span>Arqueos de Credito</span></a>
							<a href="<?php echo base_url('tesoreria/flujo'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'flujo' ? 'active' : ''); ?>"><i class="fas fa-chart-line"></i><span>Flujo de Efectivo</span></a>
							<a href="<?php echo base_url('tesoreria/integracion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'integracion' ? 'active' : ''); ?>"><i class="fas fa-link"></i><span>Integración Bancaria</span></a>
							<a href="<?php echo base_url('tesoreria/reportes'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'reportes' ? 'active' : ''); ?>"><i class="fas fa-file-alt"></i><span>Reportería</span></a>
							<a href="<?php echo base_url('tesoreria/seguridad'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'seguridad' ? 'active' : ''); ?>"><i class="fas fa-user-shield"></i><span>Seguridad y Roles</span></a>
						</div>
					</div>
					<?php elseif ($is_konami) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-landmark"></i><span>Conami / CONAMI</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('konami'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('konami/informes'); ?>" class="menu-item">Información Institucional</a>
							<a href="<?php echo base_url('konami/cartera'); ?>" class="menu-item">Seguimiento Cartera</a>
							<a href="<?php echo base_url('konami/pld'); ?>" class="menu-item">Usuarios PLD/FT</a>
							<a href="<?php echo base_url('konami/inusuales'); ?>" class="menu-item">Operaciones Inusuales</a>
							<a href="<?php echo base_url('konami/gobierno'); ?>" class="menu-item">Gobierno Corporativo</a>
							<a href="<?php echo base_url('konami/riesgos'); ?>" class="menu-item">Gestión de Riesgos</a>
							<a href="<?php echo base_url('konami/financiero'); ?>" class="menu-item">Reporte Financiero</a>
							<a href="<?php echo base_url('konami/limites'); ?>" class="menu-item">Límites Regulatorios</a>
							<a href="<?php echo base_url('konami/integracion'); ?>" class="menu-item">Integración Contable</a>
							<a href="<?php echo base_url('konami/auditoria'); ?>" class="menu-item">Auditoría Interna</a>
						</div>
					</div>
					<?php else: ?>
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes'); ?>"><i class="fas fa-file-signature"></i><span>1. Solicitud Inicial</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'uso_credito' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/uso_credito'); ?>"><i class="fas fa-file-alt"></i><span>2. Uso Crédito</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'solicitudes' && $this->router->fetch_method() == 'referencias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('solicitudes/referencias'); ?>"><i class="fas fa-user-friends"></i><span>3. Referencias</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'perfil_integral' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('perfil_integral'); ?>"><i class="fas fa-id-card"></i><span>4. PIC</span></a>
					</div>

					<div class="nav-item <?php echo ($this->router->fetch_class() == 'garantias' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('garantias'); ?>"><i class="fas fa-shield-alt"></i><span>5. Garantías</span></a>
					</div>

					<!-- Acceso rápido: 6. Análisis Financiero -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'analisis_financiero' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>6. Análisis Financiero</span></a>
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
						<a href="<?php echo base_url('solicitudes/validacion_aprobacion'); ?>"><i class="fas fa-check-square"></i><span>7. Comite de Aprobaciones</span></a>
					</div>
					<!-- Nuevo acceso rápido: Emisión Plan de Pago -->
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'prestamo' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('prestamo'); ?>"><i class="fas fa-file-invoice-dollar"></i><span>8. Emision Plan de Pago</span></a>
					</div>
					<!-- 9. Contratos (oculto) -->
					<!--
					<div class="nav-item <?php echo ($this->router->fetch_class() == 'contratos' ? 'active' : ''); ?>">
						<a href="<?php echo base_url('contratos'); ?>"><i class="fas fa-file-contract"></i><span>9. Contratos</span></a>
					</div>
					-->

					<!-- 10. Planes de Crédito: accesible desde sidebar debajo de Contratos -->
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
						<a href="<?php echo base_url('prestamospagados'); ?>"><i class="fas fa-check-circle"></i><span>Créditos Pagados</span></a>
					</div>
					<?php endif; ?>

					<?php if (! $is_contab && ! $is_pld && ! $is_teso && ! $is_konami && ! $is_admin) : ?>
					<!-- Caja (hidden per request) -->
					<div class="nav-item has-sub">
						<a href="#"><i class="fas fa-file-pdf"></i><span>Consultas</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">Créditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">Créditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">Créditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">Créditos por Asesor,<br> Fecha y Estado</a>
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
						<a href="<?php echo base_url('feriados'); ?>"><i class="fas fa-calendar-day"></i><span>Feriados</span></a>
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
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-wallet"></i><span>Tesorería</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="menu-item">Bancario</a>
							<a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="menu-item">Documentos</a>
							<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item">Conciliación</a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item">Pagos de Credito</a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item"></a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item">Arqueos de Credito</a>
							<a href="<?php echo base_url('tesoreria/flujo'); ?>" class="menu-item">Flujo de Efectivo</a>
							<a href="<?php echo base_url('tesoreria/integracion'); ?>" class="menu-item">Integración Bancaria</a>
							<a href="<?php echo base_url('tesoreria/reportes'); ?>" class="menu-item">Reportería</a>
							<a href="<?php echo base_url('tesoreria/seguridad'); ?>" class="menu-item">Seguridad y Roles</a>
						</div>
					</div>
					<?php elseif ($is_admin) : ?>
					<div class="nav-item has-sub active open">
						<a href="#"><i class="fas fa-cogs"></i><span>Administración</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('administracion'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('administracion/usuarios'); ?>" class="menu-item">Usuarios</a>
							<a href="<?php echo base_url('administracion/roles'); ?>" class="menu-item">Roles y Permisos</a>
							<a href="<?php echo base_url('administracion/configuracion'); ?>" class="menu-item">Configuración General</a>
							<a href="<?php echo base_url('administracion/seguridad'); ?>" class="menu-item">Seguridad</a>
							<a href="<?php echo base_url('administracion/auditoria'); ?>" class="menu-item">Auditoría</a>
							<a href="<?php echo base_url('administracion/parametros'); ?>" class="menu-item">Parámetros de Módulos</a>
							<a href="<?php echo base_url('administracion/catalogos'); ?>" class="menu-item">Catálogos</a>
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
							<a href="<?php echo base_url('konami/informes'); ?>" class="menu-item">Información Institucional</a>
							<a href="<?php echo base_url('konami/cartera'); ?>" class="menu-item">Seguimiento Cartera</a>
							<a href="<?php echo base_url('konami/pld'); ?>" class="menu-item">Usuarios PLD/FT</a>
							<a href="<?php echo base_url('konami/inusuales'); ?>" class="menu-item">Operaciones Inusuales</a>
							<a href="<?php echo base_url('konami/gobierno'); ?>" class="menu-item">Gobierno Corporativo</a>
							<a href="<?php echo base_url('konami/riesgos'); ?>" class="menu-item">Gestión de Riesgos</a>
							<a href="<?php echo base_url('konami/financiero'); ?>" class="menu-item">Reporte Financiero</a>
							<a href="<?php echo base_url('konami/limites'); ?>" class="menu-item">Límites Regulatorios</a>
							<a href="<?php echo base_url('konami/integracion'); ?>" class="menu-item">Integración Contable</a>
							<a href="<?php echo base_url('konami/auditoria'); ?>" class="menu-item">Auditoría Interna</a>
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
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">Créditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">Créditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">Créditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">Créditos por Asesor,<br> Fecha y Estado</a>
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
						<a href="<?php echo base_url('analisis_financiero'); ?>"><i class="fa fa-file-alt"></i><span>9. Análisis Financiero</span></a>
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
						<a href="#"><i class="fas fa-wallet"></i><span>Tesorería</span></a>
						<div class="submenu-content">
							<a href="<?php echo base_url('tesoreria'); ?>" class="menu-item <?php echo ($this->router->fetch_method() == 'index' ? 'active' : ''); ?>">Inicio</a>
							<a href="<?php echo base_url('tesoreria/cajas_bancos'); ?>" class="menu-item">Bancario</a>
							<a href="<?php echo base_url('tesoreria/movimientos'); ?>" class="menu-item">Documentos</a>
							<a href="<?php echo base_url('tesoreria/conciliacion'); ?>" class="menu-item">Conciliación</a>
							<a href="<?php echo base_url('tesoreria/pagos'); ?>" class="menu-item">Pagos de Credito</a>
							<a href="<?php echo base_url('tesoreria/cobros'); ?>" class="menu-item"></a>
							<a href="<?php echo base_url('tesoreria/arqueos'); ?>" class="menu-item">Arqueos de Credito</a>
							<a href="<?php echo base_url('tesoreria/flujo'); ?>" class="menu-item">Flujo de Efectivo</a>
							<a href="<?php echo base_url('tesoreria/integracion'); ?>" class="menu-item">Integración Bancaria</a>
							<a href="<?php echo base_url('tesoreria/reportes'); ?>" class="menu-item">Reportería</a>
							<a href="<?php echo base_url('tesoreria/seguridad'); ?>" class="menu-item">Seguridad y Roles</a>
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
							<a href="<?php echo base_url('reporte'); ?>" class="menu-item">Créditos por Fechas</a>
							<a href="<?php echo base_url('reporte/creditosasesor'); ?>" class="menu-item">Créditos por Asesor</a>
							<a href="<?php echo base_url('reporte/creditoscliente'); ?>" class="menu-item">Créditos por Cliente</a>
							<a href="<?php echo base_url('reporte/creditosasesorfechasestado'); ?>" class="menu-item">Créditos por Asesor,<br> Fecha y Estado</a>
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
