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
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="POST">
								<div class="form-group">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Cliente</label>
												<select class="form-control select2" name="idcliente" id="idcliente" required>
													<option value="">SELECCIONAR</option>
													<?php foreach ($clientes as $cliente) : ?>
														<?php if (isset($simulacion)) : ?>
															<option value="<?php echo $cliente->idcliente; ?>" <?php echo ($cliente->idcliente == $simulacion->idcliente ? 'selected' : '') ?>><?php echo $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
														<?php else : ?>
															<option value="<?php echo $cliente->idcliente; ?>"><?php echo $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
														<?php endif; ?>
													<?php endforeach; ?>
												</select>
												<?php echo form_error('idcliente', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Asesor</label>
												<select class="form-control select2" name="idasesor" id="idasesor" required>
													<option value="">SELECCIONAR</option>
													<?php foreach ($asesores as $asesor) : ?>
														<?php if (isset($simulacion)) : ?>
															<option value="<?php echo $asesor->idasesor; ?>" <?php echo ($asesor->idasesor == $simulacion->idasesor ? 'selected' : '') ?>><?php echo $asesor->nombres; ?></option>
														<?php else : ?>
															<option value="<?php echo $asesor->idasesor; ?>"><?php echo $asesor->nombres; ?></option>
														<?php endif; ?>
													<?php endforeach; ?>
												</select>
												<?php echo form_error('idasesor', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Fecha Crédito</label>
												<?php $fechaActual = date('d/m/Y'); ?>
												<input type="text" class="form-control datetimepicker-input" name="fecha_credito" id="fecha_credito" id="fecha_credito" data-toggle="datetimepicker" data-target="#fecha_credito" required value="<?php echo (isset($simulacion) ? formatoFechaCorta($simulacion->fecha_credito) : $fechaActual); ?>">
												<?php echo form_error('fecha_credito', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Monto Crédito</label>
												<input type="text" class="form-control" name="monto_credito" id="monto_credito" required value="<?php echo (isset($simulacion) ? $simulacion->monto_credito : set_value('monto_credito')); ?>">
												<?php echo form_error('monto_credito', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>(%) Interes</label>
												<input type="text" class="form-control" name="interes_credito" id="interes_credito" required value="<?php echo (isset($simulacion) ? $simulacion->interes_credito : set_value('interes_credito')); ?>">
												<?php echo form_error('interes_credito', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>N° Cuotas</label>
												<input type="number" min="1" class="form-control" name="numero_cuotas" id="numero_cuotas" required value="<?php echo (isset($simulacion) ? $simulacion->numero_cuotas : set_value('numero_cuotas')); ?>">
												<?php echo form_error('numero_cuotas', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Monto Capital</label>
												<input type="text" class="form-control" readonly name="monto_capital" id="monto_capital" required value="<?php echo (isset($simulacion) ? $simulacion->monto_capital : set_value('monto_capital')); ?>">
												<?php echo form_error('monto_capital', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Monto Interes</label>
												<input type="text" class="form-control" readonly name="monto_interes" id="monto_interes" required value="<?php echo (isset($simulacion) ? $simulacion->monto_interes : set_value('monto_interes')); ?>">
												<?php echo form_error('monto_interes', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Monto Cuota</label>
												<input type="text" class="form-control" readonly name="monto_cuota" id="monto_cuota" required value="<?php echo (isset($simulacion) ? $simulacion->monto_cuota : set_value('monto_cuota')); ?>">
												<?php echo form_error('monto_cuota', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Total Interes</label>
												<input type="text" class="form-control" readonly name="total_interes" id="total_interes" required value="<?php echo (isset($simulacion) ? $simulacion->total_interes : set_value('total_interes')); ?>">
												<?php echo form_error('total_interes', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Total a Pagar</label>
												<input type="text" class="form-control" readonly name="total_pagar" id="total_pagar" required value="<?php echo (isset($simulacion) ? $simulacion->total_pagar : set_value('total_pagar')); ?>">
												<?php echo form_error('total_pagar', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Forma de Pago</label>
												<select name="forma_pago" id="forma_pago" class="form-control" required>
													<?php if (isset($simulacion)) : ?>
														<option value="0" <?php echo ($simulacion->forma_pago == 0 ? 'selected' : '') ?>>DIARIO</option>
														<option value="1" <?php echo ($simulacion->forma_pago == 1 ? 'selected' : '') ?>>SEMANAL</option>
														<option value="2" <?php echo ($simulacion->forma_pago == 2 ? 'selected' : '') ?>>QUINCENAL</option>
														<option value="3" <?php echo ($simulacion->forma_pago == 3 ? 'selected' : '') ?>>MENSUAL</option>
													<?php else : ?>
														<option value="0">DIARIO</option>
														<option value="1">SEMANAL</option>
														<option value="2">QUINCENAL</option>
														<option value="3">MENSUAL</option>
													<?php endif; ?>
												</select>
												<?php echo form_error('forma_pago', '<div class="text-danger">', '</div>') ?>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="">&nbsp;</label>
												<button type="button" class="btn bg-danger text-white mr-2 btn-block" id="btnSimular"> <i class="ik ik-align-justify"></i> Simular Cuotas</button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover pr-20 pl-20" id="tblcuotas" style="width: 100%;">
												<thead>
													<th>N° Cuota</th>
													<th>Fecha</th>
													<th>Capital</th>
													<th>Interes</th>
													<th>Monto Cuota</th>
												</thead>
												<tbody>
												</tbody>
											</table>
										</div>
									</div>
									<?php if (isset($simulacion)) : ?>
										<div class="col-md-6">
											<input type="hidden" name="simulacion_id" value="<?php echo ($simulacion->idsimulacion); ?>">
										</div>
									<?php endif; ?>
								</div>
								<button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Guardar</button>
								<a class="btn btn-danger" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
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
