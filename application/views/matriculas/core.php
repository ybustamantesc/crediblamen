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
			<?php echo date('Y-m-d'); ?>
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<?php echo (isset($matricula) ? '<i class="ik ik-calendar ik-2x"></i> Fecha de la última actualización: ' . formatoFechaHora($matricula->actualizado) : ''); ?>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="post">
								<div class="row mb-3">
									<div class="col-md-8 mb-3">
										<label for="">Cliente</label>
										<select class="form-control clientes select2" name="clienteid" <?php echo (isset($matricula) ? 'disabled' : ''); ?>>
											<option value="">Seleccionar</option>
											<?php foreach ($clientes as $cliente) : ?>
												<?php if (isset($matricula)) : ?>
													<option value="<?php echo $cliente->id . ' ' . $cliente->diasVencimiento ?>" <?php echo ($cliente->id == $matricula->clienteid ? 'selected' : '') ?>><?php echo $cliente->nombres; ?></option>
												<?php else : ?>
													<option value="<?php echo $cliente->id . ' ' . $cliente->diasVencimiento ?>"><?php echo $cliente->nombres; ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
										<?php echo form_error('mensalidade_mensalista_id', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Días de Vencimento</label>
										<input type="text" class="form-control diasVencimento" name="diasVencimento" value="<?php echo (isset($matricula) ? $cliente->diasVencimiento : set_value('diasVencimiento')) ?>" readonly>
										<?php echo form_error('diasVencimento', '<div class="text-danger">', '</div>') ?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8 mb-3">
										<label for="">Categoría</label>
										<select class="form-control precios select2" name="precioid" <?php echo (isset($matricula) && $matricula->estado == 1 ? 'disabled' : ''); ?>>
											<option value="">Seleccionar</option>
											<?php foreach ($precios as $precio) : ?>
												<?php if (isset($matricula)) : ?>
													<option value="<?php echo $precio->precio_id . ' ' . $precio->precio_valor_mensualidad ?>" <?php echo ($precio->precio_id == $matricula->precioid ? 'selected' : '') ?>><?php echo $precio->precio_categoria ?></option>
												<?php else : ?>
													<option value="<?php echo $precio->precio_id . ' ' . $precio->precio_valor_mensualidad ?>"><?php echo $precio->precio_categoria ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
										<?php echo form_error('precioid', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Costo Mensual</label>
										<input type="text" class="form-control precio_valor_mensualidad" name="valor" value="<?php echo (isset($matricula->valor) ? $matricula->valor : '0,00') ?>" readonly="">
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-4 mb-3">
										<label for="">Fecha de Vencimiento</label>
										<input type="date" class="form-control" name="fechaVencimiento" value="<?php echo (isset($matricula) ? $matricula->fechaVencimiento : set_value('fechaVencimiento')) ?>" <?php echo (isset($matricula) ? 'disabled' : ''); ?>>
										<?php echo form_error('fechaVencimiento', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Situación</label>
										<select class="form-control" name="estado" <?php echo (isset($matricula) && $matricula->estado == 1 ? 'disabled' : ''); ?>>
											<?php if (isset($matricula)) : ?>
												<option value="0" <?php echo ($matricula->estado == 0 ? 'selected' : '') ?>>Pendente</option>
												<option value="1" <?php echo ($matricula->estado == 1 ? 'selected' : '') ?>>Paga</option>
											<?php else : ?>
												<option value="0">Pendente</option>
												<option value="1">Paga</option>
											<?php endif; ?>
										</select>
									</div>
									<?php if (isset($matricula) && $matricula->estado == 1) : ?>
										<div class="col-md-4 mb-3">
											<label for="">Fecha de Pago</label>
											<input type="text" class="form-control" value="<?php echo formatoFechaHora($matricula->fechaPago); ?>" readonly>
										</div>
									<?php endif; ?>
								</div>
								<?php if (isset($matricula)) : ?>
									<input type="hidden" name="matriculaid" value="<?php echo $matricula->matriculaid ?>" />
								<?php endif; ?>
								<input type="hidden" class="matricula_cliente_id" name="matricula_cliente_hidden_id" value="" />
								<input type="hidden" class="matricula_precio_id" name="matricula_precio_hidden_id" value="" />
								<?php if (isset($matricula) && $matricula->estado == 1) : ?>
									<button type="submit" class="btn btn-success mr-2" disabled="">Encerrada</button>
								<?php else : ?>
									<a title="Guardar Matrícula" href="javascript:void(0)" class="btn btn btn-primary mr-2" data-toggle="modal" data-target="#matricula">Salvar</i></a>
								<?php endif; ?>
								<a href="<?php echo base_url($this->router->fetch_class()); ?>" class="btn btn-light">Volver</a>
								<div class="modal fade" id="matricula" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="demoModalLabel"><i class="ik ik-alert-octagon text-danger"></i> Confirmar</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body">
												<span class="text-dark font-weight-bold"><?php echo $texto_modal; ?></span></br>
												<p></p>
												Click en <span class="text-primary font-weight-bold">"Sí"</span> para guardar.
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-success" data-dismiss="modal">No</button>
												<button type="submit" class="btn btn-primary mr-2" value="">Sí</button>
											</div>
										</div>
									</div>
								</div>
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
