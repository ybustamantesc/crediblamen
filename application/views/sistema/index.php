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
								<li class="breadcrumb-item active" aria-current="page"><?php echo $titulo; ?></li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
			<?php if ($message = $this->session->flashdata('success')) : ?>
				<div class="row">
					<div class="col-md-12">
						<div class="alert bg-success alert-success text-white alert-dismissible fade show" role="alert">
							<strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<i class="ik ik-x"></i>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($message = $this->session->flashdata('error')) : ?>
				<div class="row">
					<div class="col-md-12">
						<div class="alert bg-danger alert-dagner text-white alert-dismissible fade show" role="alert">
							<strong><i class="fas fa-frown"></i> <?php echo $message; ?></strong>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<i class="ik ik-x"></i>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<?php echo (isset($sistema) ? '<i class="ik ik-calendar ik-2x"></i> Fecha de la última actualización: ' . formatoFechaHora($sistema->fechaActualizacion) : ''); ?>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_index" method="POST" enctype="multipart/form-data">
								<div class="form-group row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Razón Social</label>
											<input type="text" class="form-control" name="razon_social" value="<?php echo (isset($sistema) ? $sistema->razon_social : set_value('razon_social')); ?>">
											<?php echo form_error('razon_social', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Correo Electrónico</label>
											<input type="text" class="form-control" name="email" value="<?php echo (isset($sistema) ? $sistema->email : set_value('email')); ?>">
											<?php echo form_error('email', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Sitio Web</label>
											<input type="text" class="form-control" name="web" value="<?php echo (isset($sistema) ? $sistema->web : set_value('web')); ?>">
											<?php echo form_error('web', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Teléfonos</label>
											<input class="form-control" name="telefonos" value="<?php echo (isset($sistema) ? $sistema->telefonos : set_value('telefonos')); ?>">
											<?php echo form_error('telefonos', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label>Dirección</label>
											<input type="text" class="form-control" name="direccion" value="<?php echo (isset($sistema) ? $sistema->direccion : set_value('direccion')); ?>">
											<?php echo form_error('direccion', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label>Moneda</label>
											<select class="form-control select2" name="idmoneda" required>
												<option value="">Seleccionar</option>
												<?php foreach ($monedas as $moneda) : ?>
													<?php if (isset($sistema)) : ?>
														<option value="<?php echo $moneda->id; ?>" <?php echo ($moneda->id == $sistema->idmoneda ? 'selected' : '') ?>><?php echo $moneda->simbolo . ' - ' . $moneda->nombre; ?></option>
													<?php else : ?>
														<option value="<?php echo $moneda->id; ?>"><?php echo $moneda->simbolo . ' - ' . $moneda->nombre;; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<?php echo form_error('idmoneda', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label>Mensaje</label>
											<textarea type="text" class="form-control" name="mensaje_ticket"><?php echo (isset($sistema) ? $sistema->mensaje_ticket : set_value('mensaje_ticket')); ?></textarea>
											<?php echo form_error('mensaje_ticket', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<label>Logotipo</label>
										<input type="file" class="form-control"  name="logotipo" id="logotipo"  value="<?php echo (isset($sistema) ? $sistema->logotipo : set_value('logotipo')); ?>">
										<input type="hidden" name="logotipo_ant" value="<?php echo (isset($sistema) ? $sistema->logotipo : ''); ?>">
										<?php echo form_error('logotipo', '<div class="text-danger">', '</div>'); ?>
									</div>
									<div class="col-md-4">
										<?php if (isset($sistema)) : ?>
											<img  id="imgPreview" src="<?php echo base_url()."public/img/sistema/".$sistema->logotipo;?>" alt="" width="150px">
										<?php else: ?>
											<img id="imgPreview" width="150px" src="<?php echo base_url()."public/img/sistema/default.png";?>">
										<?php endif; ?>
									</div>

								</div>

								<button type="submit" class="btn btn-primary mr-2">Guardar</button>
								<a class="btn btn-info" href="<?php echo base_url('/home'); ?>">Volver</a>
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