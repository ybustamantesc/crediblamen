<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
	<?php $this->load->view('layout/sidebar.php'); ?>
	<div class="main-content">
		<div class="container-fluid">
			<div class="page-header">
				<div class="row align-items-end">
					<div class="col-lg-8">
						<div class="page-header-title">
							<i class="<?php echo $icono_view; ?> bg-blue"></i>
							<div class="d-inline">
								<h5> <?php echo $titulo; ?> </h5>
								<span><?php echo $subtitulo; ?></span>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<nav class="breadcrumb-container" aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item">
									<a href="<?php echo base_url('/'); ?>" data-toggle="tooltip" data-placement="top" title="Inicio"><i class="ik ik-home"></i></a>
								</li>
								<li class="breadcrumb-item">
									<a href="<?php echo $this->router->fetch_class(); ?>" data-toggle="tooltip" data-placement="top" title="Listar <?php echo $this->router->fetch_class(); ?>">Listar <?php echo $this->router->fetch_class(); ?></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo $titulo; ?></li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<h3><?php echo $titulo; ?></h3>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="POST">
								<div class="form-group row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Nombre</label>
											<input type="text" class="form-control" name="first_name" value="<?php echo (isset($usuario) ? $usuario->first_name : set_value('first_name')); ?> ">
											<?php echo form_error('first_name', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Sobrenombre</label>
											<input type="text" class="form-control" name="last_name" value="<?php echo (isset($usuario) ? $usuario->last_name : set_value('last_name')); ?> ">
											<?php echo form_error('last_name', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Usuario</label>
											<input type="text" class="form-control" name="username" value="<?php echo (isset($usuario) ? $usuario->username : set_value('username')); ?> ">
											<?php echo form_error('username', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Correo (Login)</label>
											<input type="text" class="form-control" name="email" value="<?php echo (isset($usuario) ? $usuario->email : set_value('email')); ?> ">
											<?php echo form_error('email', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Contraseña</label>
											<input type="password" class="form-control" name="password" value="">
											<?php echo form_error('password', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Confirmación</label>
											<input type="password" class="form-control" name="confirmacion" value="">
											<?php echo form_error('confirmacion', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<?php if ($this->ion_auth->is_admin()) : ?>
										<div class="col-md-6">
											<div class="form-group">
												<label>Perfil de acceso</label>
												<select name="perfil" class="custom-select">
													<?php
													// Render available groups provided by controller
													if (!empty($grupos) && is_array($grupos)) {
														foreach ($grupos as $g) {
															$sel = '';
															if (isset($perfil_usuario) && is_object($perfil_usuario) && isset($perfil_usuario->id) && $perfil_usuario->id == $g->id) $sel = 'selected';
															echo '<option value="' . htmlspecialchars($g->id) . '" ' . $sel . '>' . htmlspecialchars($g->name) . '</option>';
														}
													} else {
														// Fallback to legacy options
														// Compute perfil id safely
														$perfil_id = (isset($perfil_usuario) && is_object($perfil_usuario) && isset($perfil_usuario->id)) ? $perfil_usuario->id : null;
														if ($usuario) :
														?>
															<option value="1" <?php echo ($perfil_id == 1 ? 'selected' : ''); ?>>Super Adminstrador</option>
															<option value="2" <?php echo ($perfil_id == 2 ? 'selected' : ''); ?>>Administrador</option>
															<option value="3" <?php echo ($perfil_id == 3 ? 'selected' : ''); ?>>Asesor</option>
															<option value="4" <?php echo ($perfil_id == 4 ? 'selected' : ''); ?>>Promotor</option>
														<?php else : ?>
															<option value="1">Super Adminstrador</option>
															<option value="2">Administrador</option>
															<option value="3">Asesor</option>
															<option value="4">Promotor</option>
														<?php
														endif;
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Serie de Recibos</label>
												<select name="idserie_recibo" class="custom-select">
													<option value="">-- Ninguna --</option>
													<?php if (!empty($series_recibos) && is_array($series_recibos)): foreach ($series_recibos as $sr): ?>
														<?php $sel = (isset($usuario) && isset($usuario->idserie_recibo) && $usuario->idserie_recibo == $sr->idserie) ? 'selected' : ''; ?>
														<option value="<?php echo $sr->idserie; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($sr->codigo . ' - ' . $sr->nombre); ?></option>
													<?php endforeach; endif; ?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Activo</label>
												<select name="active" class="custom-select">
													<?php if (isset($usuario)) : ?>
														<option value="0" <?php echo ($usuario->active == 0 ? 'selected' : ''); ?>>No</option>
														<option value="1" <?php echo ($usuario->active == 1 ? 'selected' : ''); ?>>SÍ</option>
													<?php else : ?>
														<option value="0">No</option>
														<option value="1">SÍ</option>
													<?php endif; ?>
												</select>
											</div>
										</div>
									<?php endif; ?>
									<?php if (isset($usuario)) : ?>
										<div class="col-md-6">
											<input type="hidden" name="usuario_id" value="<?php echo ($usuario->id); ?>">
										</div>
									<?php endif; ?>
								</div>

								<button type="submit" class="btn btn-primary mr-2">Guardar</button>
								<a class="btn btn-info" href="<?php echo base_url($this->router->fetch_class()); ?>">Volver</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer class="footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>

</div>
