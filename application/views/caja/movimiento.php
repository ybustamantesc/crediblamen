<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
	<?php $this->load->view('layout/sidebar.php'); ?>
	<div class="main-content">
		<div class="container-fluid">
			<div class="page-header">
				<div class="row align-items-end">
					<div class="col-lg-8">
						<div class="page-header-title">
							<i class="<?php echo $icono; ?> bg-blue"></i>
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
							<h3 class="text-center">Registrar Movimiento de Caja</h3>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="POST" action="<?php echo base_url() . 'caja/registrar'; ?>">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Tipo Movimiento</label>
											<select name="tipo_movimiento" class="form-control custom-select" required>
												<option value="">Seleccionar</option>
												<option value="1">Ingreso</option>
												<option value="0">Gasto</option>
											</select>
											<?php echo form_error('tipo_movimiento', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Motivo</label>
											<input type="text" class="form-control" name="descripcion_movimiento" required value="<?php echo (isset($caja) ? $caja->descripcion_movimiento : set_value('descripcion_movimiento')); ?>">
											<?php echo form_error('descripcion_movimiento', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Monto</label>
											<input type="text" class="form-control" name="monto_movimiento" <?php echo (isset($caja) ? 'required' : ''); ?> value="<?php echo (isset($caja) ? $caja->monto_movimiento : set_value('monto_movimiento')); ?>">
											<?php echo form_error('monto_movimiento', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Forma de Pago</label>
											<select name="forma_pago" class="form-control custom-select" required>
												<option value="">SELECCIONAR</option>
												<option value="EFECTIVO">EFECTIVO</option>
												<option value="CHEQUE">CHEQUE</option>
												<option value="TRANSFERENCIA">TRANSFERENCIA</option>
												<option value="DEPÓSITO">DEPÓSITO</option>
											</select>
											<?php echo form_error('forma_pago', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Tipo Documento</label>
											<input type="text" class="form-control" name="tipo_doc">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>N° Documento</label>
											<input type="text" class="form-control" name="numero_doc">
										</div>
									</div>
								</div>
								<button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Guardar</button>
								<a class="btn btn-danger float-right" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
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
