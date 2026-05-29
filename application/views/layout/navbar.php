<style>
    .header-top.servicont-navbar {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        border-bottom: 3px solid #667eea;
    }
    
    .servicont-navbar .btn-icon,
    .servicont-navbar .nav-link {
        color: rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
    }
    
    .servicont-navbar .btn-icon:hover,
    .servicont-navbar .nav-link:hover {
        color: #ffffff;
        transform: scale(1.1);
    }
    
    .servicont-navbar .dropdown-toggle {
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .servicont-navbar .dropdown-toggle:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>

<header class="header-top servicont-navbar" header-theme="dark">
	<div class="container-fluid">
		<div class="d-flex justify-content-between">
			<div class="top-menu d-flex align-items-center">
				<button type="button" class="btn-icon mobile-nav-toggle d-lg-none"><span></span></button>

				<button type="button" id="navbar-fullscreen" class="nav-link"><i class="ik ik-maximize"></i></button>
			</div>

			<div class="top-menu d-flex align-items-center">
				<?php if ($this->router->fetch_class() == 'home' && isset($notificaciones)) : ?>
					<?php if ($this->ion_auth->is_admin()) : ?>
						<div class="dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="notiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="blink_me"><i class="ik ik-bell"></i><span class="badge bg-danger"><?php echo $notificaciones ?></span></span></a>
							<div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notiDropdown">
								<h4 class="header">Notificaciones</h4>
								<div class="notifications-wrap">
									<?php if (isset($matriculas_vencidas)) : ?>
										<a href="<?php echo base_url('matriculas') ?>" class="media">
											<span class="d-flex">
												<i class="fas fa-hand-holding-usd bg-danger"></i>
											</span>
											<span class="media-body">
												<span class="heading-font-family media-heading">Existen Pagos Vencidos</span><br>
												<span class="media-content">Administrar Pagos</span>
											</span>
										</a>
									<?php endif; ?>
									<?php if (isset($precios_desactivados)) : ?>
										<a href="<?php echo base_url('precios') ?>" class="media">
											<span class="d-flex">
												<i class="fas fa-dollar-sign bg-danger"></i>
											</span>
											<span class="media-body">
												<span class="heading-font-family media-heading">Existen Precios Desactivados</span><br>
												<span class="media-content">Administrar Precios</span>
											</span>
										</a>
									<?php endif; ?>
									<?php if (isset($formas_desactivados)) : ?>
										<a href="<?php echo base_url('formas') ?>" class="media">
											<span class="d-flex">
												<i class="fas fa-comment-dollar bg-danger"></i>
											</span>
											<span class="media-body">
												<span class="heading-font-family media-heading">Existen Formas de Pago Desactivados</span><br>
												<span class="media-content">Administrar Formas de Pago</span>
											</span>
										</a>
									<?php endif; ?>
									<?php if (isset($usuarios_desactivados)) : ?>
										<a href="<?php echo base_url('usuarios') ?>" class="media">
											<span class="d-flex">
												<i class="fas fa-user-friends bg-danger"></i>
											</span>
											<span class="media-body">
												<span class="heading-font-family media-heading">Existen Usuarios Desactivados</span>
												<span class="media-content">Administrar Usuarios</span>
											</span>
										</a>
									<?php endif; ?>
									<?php if (isset($clientes_desactivados)) : ?>
										<a href="<?php echo base_url('usuarios') ?>" class="media">
											<span class="d-flex">
												<i class="fas fa-users bg-danger"></i>
											</span>
											<span class="media-body">
												<span class="heading-font-family media-heading">Existen Clientes Desactivados</span><br>
												<span class="media-content">Administrar Clientes</span>
											</span>
										</a>
									<?php endif; ?>
								</div>
								<div class="footer">Tiene que verificar.</div>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($this->ion_auth->is_admin()) : ?>
					<!-- <button type="button" class="nav-link ml-10" id="apps_modal_btn" data-toggle="modal" data-target="#appsModal"><i class="ik ik-grid"></i></button> -->
				<?php endif; ?>
				<div class="dropdown">
					<a class="dropdown-toggle user-dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ik ik-user text-white"></i></a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
						<a data-toggle="tooltip" data-placement="left" title="Actualizar Perfil" class="dropdown-item" href="<?php echo base_url('usuarios/core/' . $this->session->userdata('user_id')); ?>"><i class="ik ik-user dropdown-icon"></i> Perfil</a>
						<a data-toggle="tooltip" data-placement="left" title="Salir del Sistema" class="dropdown-item" href="<?php echo base_url('login/logout'); ?>"><i class="ik ik-power dropdown-icon"></i> Cerrar Sesión</a>
					</div>
				</div>

			</div>

		</div>
	</div>
</header>
