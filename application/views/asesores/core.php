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
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<?php echo (isset($asesor) ? '<i class="ik ik-calendar ik-2x"></i> Modificar Asesor': 'Registrar Asesor'); ?>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="POST">
								<div class="form-group row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Nombres</label>
											<input type="text" class="form-control" required name="nombres" value="<?php echo (isset($asesor) ? $asesor->nombres : set_value('nombres')); ?>">
											<?php echo form_error('nombres', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<div class="col-md-8">
										<div class="form-group">
											<label>Dirección</label>
											<input type="text" class="form-control" required name="direccion" value="<?php echo (isset($asesor) ? $asesor->direccion : set_value('direccion')); ?>">
											<?php echo form_error('direccion', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Teléfono</label>
											<input type="text" class="form-control" required name="telefono" value="<?php echo (isset($asesor) ? $asesor->telefono : set_value('telefono')); ?>">
											<?php echo form_error('telefono', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group">
											<label>Estado</label>
											<select name="estado" class="form-control" required>
												<?php if (isset($asesor)) : ?>
													<option value="1" <?php echo ($asesor->estado == 1 ? 'selected' : '') ?>>ACTIVO</option>
													<option value="0" <?php echo ($asesor->estado == 0 ? 'selected' : '') ?>>INACTIVO</option>
												<?php else : ?>
													<option value="1">ACTIVO</option>
													<option value="0">INACTIVO</option>
												<?php endif; ?>
											</select>
										</div>
									</div>

									<?php if (isset($asesor)) : ?>
										<div class="col-md-6">
											<input type="hidden" name="idasesor" value="<?php echo ($asesor->idasesor); ?>">
										</div>
									<?php endif; ?>
								</div>
								<button type="submit" class="btn btn-success mr-2"><i class="fas fa-check"></i> Guardar</button>
								<a class="btn btn-info" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer class="footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>
</div>
