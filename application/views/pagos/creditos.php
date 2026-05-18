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
							<form class="forms-sample" name="form_core" method="POST" action="<?php echo base_url('pagos/pagartodo'); ?>">
								<div class="form-group row">
									<div class="col-md-9">
										<div class="form-group">
											<label>Cliente</label>
											<select class="form-control select2" name="cliente" id="cliente" required <?php echo (isset($pago) && $pago->estado == 1 ? 'disabled' : ''); ?>>
												<option value="">SELECCIONAR</option>
												<?php foreach ($clientes as $cliente) : ?>
													<?php if (isset($pago)) : ?>
														<option value="<?php echo $cliente->idcliente; ?>" <?php echo ($cliente->idcliente == $pago->idcliente ? 'selected' : '') ?>><?php echo $cliente->idcliente . ' - ' . $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
													<?php else : ?>
														<option value="<?php echo $cliente->idcliente; ?>"><?php echo $cliente->idcliente . ' - ' . $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
											<?php echo form_error('cliente', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Total Pagar</label>
											<input type="text" class="form-control" readonly name="total_pagar_creditos" id="total_pagar_creditos" required value="<?php echo (isset($pago) ? $pago->fecha_credito : set_value('fecha_credito')); ?>">
											<?php echo form_error('total_pagar_creditos', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-12">
										<table class="table table-bordered table-hover tablaCreditos" id="tablaCreditos" style="width:100% ;">
											<thead>
												<tr>
													<th><input type="checkbox" id="select-all-creditos"> Todo</th>
													<th>N° Créditos</th>
													<th>Fecha Crédito</th>
													<th>Total Pagar</th>
													<th>Total Saldo</th>
													<th>Forma Pago</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
										<?php echo form_error('credito_id', '<div class="text-danger">', '</div>') ?>
									</div>

									<?php if (isset($pago)) : ?>
										<div class="col-md-6">
											<input type="hidden" name="id" value="<?php echo ($pago->id); ?>">
										</div>
									<?php endif; ?>
								</div>
								<button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Pagar</button>
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
