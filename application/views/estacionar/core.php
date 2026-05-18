<?php $this->load->view('layout/navbar'); ?> <div class="page-wrap"> <?php $this->load->view('layout/sidebar.php'); ?> <div class="main-content">
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
						<div class="card-header"> <?php echo (isset($estacionado) ? '<i class="ik ik-calendar ik-2x"></i> Fecha de la última actualización: ' . formatoFechaHora($estacionado->estacionar_fecha_actualizacion) : ''); ?> </div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="post">
								<div class="row mb-3">
									<div class="col-md-4 mb-3">
										<label for="">Categoría</label>
										<select class="form-control precios" name="estacionar_precio_id" <?php echo (isset($estacionado) ? 'disabled' : '') ?>>
											<option value="">Seleccionar</option>
											<?php foreach ($precios as $precio) : ?>
												<?php if (isset($estacionado)) : ?>
													<option value="<?php echo $precio->precio_id ?>" <?php echo ($precio->precio_id == $estacionado->estacionar_precio_id ? 'selected' : '') ?>><?php echo $precio->precio_categoria ?></option>
												<?php else : ?>
													<option value="<?php echo $precio->precio_id ?><?php echo $precio->precio_valor_hora ?>"><?php echo $precio->precio_categoria ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select> <?php echo form_error('estacionar_precio_id', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Valor Hora</label>
										<input type="text" class="form-control estacionar_valor_hora" name="estacionar_valor_hora" value="<?php echo (isset($estacionado->estacionar_valor_hora) ? $estacionado->estacionar_valor_hora : '0,00') ?>" readonly="">
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Número Vacante</label>
										<input type="number" class="form-control" name="estacionar_numero_vacante" value="<?php echo (isset($estacionado) ? $estacionado->estacionar_numero_vacante : set_value('estacionar_numero_vacante')) ?>" <?php echo (isset($estacionado) ? 'readonly' : '') ?>> <?php echo form_error('estacionar_numero_vacante', '<div class="text-danger">', '</div>') ?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-4 mb-3">
										<label for="">Placa Vehículo</label>
										<input type="text" class="form-control placa" name="estacionar_placa_vehiculo" value="<?php echo (isset($estacionado) ? $estacionado->estacionar_placa_vehiculo : set_value('estacionar_placa_vehiculo')) ?>" <?php echo (isset($estacionado) ? 'readonly' : '') ?>> <?php echo form_error('estacionar_placa_vehiculo', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Marca vehículo</label>
										<input type="text" class="form-control" name="estacionar_marca_vehiculo" value="<?php echo (isset($estacionado) ? $estacionado->estacionar_marca_vehiculo : set_value('estacionar_marca_vehiculo')) ?>" <?php echo (isset($estacionado) ? 'readonly' : '') ?>> <?php echo form_error('estacionar_marca_veiculo', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col-md-4 mb-3">
										<label for="">Modelo vehículo</label>
										<input type="text" class="form-control" name="estacionar_modelo_vehiculo" value="<?php echo (isset($estacionado) ? $estacionado->estacionar_modelo_vehiculo : set_value('estacionar_modelo_vehiculo')) ?>" <?php echo (isset($estacionado) ? 'readonly' : '') ?>> <?php echo form_error('estacionar_modelo_vehiculo', '<div class="text-danger">', '</div>') ?>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col mb-3">
										<label for="">Fechad de Ingreso</label>
										<input type="text" class="form-control" name="estacionar_fecha_entrada" value="<?php echo (isset($estacionado) ? formatoFechaHora($estacionado->estacionar_fecha_entrada) : formatoFechaHora(date('y-m-d H:i:s'))) ?>" readonly="">
									</div>
									<div class="col mb-3">
										<label for="">Fecha de Salida</label> <?php if (isset($estacionado) && $estacionado->estacionar_estado == 1) : ?> <input type="text" class="form-control" name="estacionar_fecha_salida" value="<?php echo (isset($estacionado) ? formatoFechaHora($estacionado->estacionar_fecha_salida) : formatoFechaHora(date('y-m-d H:i:s'))) ?>" readonly=""> <?php else : ?> <input type="text" class="form-control" name="estacionar_fecha_salida" value="<?php echo formatoFechaHora(date('y-m-d H:i:s')) . '&nbsp;|&nbsp;Em aberto' ?>" readonly=""> <?php endif; ?> <?php echo form_error('estacionar_fecha_salida', '<div class="text-danger">', '</div>') ?>
									</div>
									<div class="col mb-3">
										<label for="">Tiempo transcurrido (horas e minutos)</label> <?php
																									$data_entrada = new DateTime(isset($estacionado) ? $estacionado->estacionar_fecha_entrada : date('Y-m-d H:i:s'));
																									$data_saida   = new DateTime(date('Y-m-d H:i:s'));
																									$diff         = $data_saida->diff($data_entrada);
																									$hours        = $diff->h;
																									$hours += ($diff->days * 24);
																									$tempo_decorrido = $hours . '.' . $diff->i; //Concatena as horas com os minutos
																									if (isset($estacionado)) {
																										$valor_devido = intval($estacionado->estacionar_valor_hora) * $tempo_decorrido;
																									} else {
																										$valor_devido = '0,00';
																									}
																									if (str_replace('.', '', $tempo_decorrido) <= '015') {
																										$valor_devido = '0,00';
																									}
																									?> <input type="text" class="form-control" name="estacionar_tiempo_transcurrido" value="<?php echo (isset($estacionado) && $estacionado->estacionar_estado == 1 ? ($estacionado->estacionar_tiempo_transcurrido) : $tempo_decorrido) ?>" readonly>
									</div>
								</div> <?php if (isset($estacionado)) : ?> <div class="row mb-3">
										<div class="col-md-6 mb-3">
											<label for="">Valor aduedado</label>
											<input type="text" class="form-control" name="estacionar_valor_adeudado" value="<?php echo (isset($estacionado) && $estacionado->estacionar_estado == 1 ? $estacionado->estacionar_valor_adeudado : $valor_devido) ?>" readonly="">
										</div>
										<div class="col-md-6 mb-3">
											<label for="">Forma de Pago</label>
											<select class="form-control" name="estacionar_forma_pago_id" <?php echo (isset($estacionado) && $estacionado->estacionar_estado == 1 ? 'disabled' : '') ?>>
												<option value="">Seleccionar</option> <?php foreach ($formapagos as $forma) : ?> <?php if ($estacionado) : ?> <option value="<?php echo $forma->id; ?> " <?php echo ($forma->id == $estacionado->estacionar_forma_pago_id ? 'selected' : '') ?> "><?php echo $forma->nombre; ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('estacionar_forma_pago_id', '<div class="text-danger">', '</div>') ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div>
                                <?php if (isset($estacionado)) : ?>
                                    <input value=" <?php echo $estacionado->estacionar_id; ?>" name="estacionar_id" type="hidden">
														<?php endif; ?>
														<?php if (isset($estacionado) && $estacionado->estacionar_estado == 1) : ?>
															<button type="submit" class="btn btn-success mr-2 disabled" value="" disabled>Guardar</button>
														<?php else : ?> <a title="Cadastrar ordem de estacionamento" href="javascript:void(0)" class="btn btn btn-primary mr-2" data-toggle="modal" data-target="#modalRegistrar">Guardar</i></a>
														<?php endif; ?>
														<a href="<?php echo base_url($this->router->fetch_class()); ?>" class="btn btn-light">Cancelar</a>
										</div>
										<div class="modal fade" id="modalRegistrar" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="demoModalLabel"><i class="ik ik-alert-octagon text-danger"></i>&nbsp;&nbsp;Confirmação de dados!</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													</div>
													<div class="modal-body">
														<span class="text-dark font-weight-bold"><?php echo $texto_modal; ?></span></br>
														<p></p> Clique em <span class="text-primary font-weight-bold">"Sim"</span> para prosseguir.
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
