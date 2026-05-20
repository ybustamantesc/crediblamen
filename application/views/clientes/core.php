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
							<?php echo (isset($cliente) ? '<i class="ik ik-calendar ik-2x"></i> Fecha de la última actualización: ' . formatoFechaHora($cliente->fechaActualizacion) : 'Registrar Nuevo Cliente'); ?>
						</div>
						<div class="card-body">
							<form class="forms-sample" name="form_core" method="POST">
								<?php if (isset($cliente)) : ?>
									<input type="hidden" readonly class="form-control" name="cliente_id" value="<?php echo ($cliente->idcliente); ?>">
								<?php else : ?>
									<input type="hidden" readonly name="cliente_id" value="" class="form-control">
								<?php endif; ?>

								<div class="d-flex justify-content-end mb-3">
									<button type="submit" class="btn btn-success mr-2 d-md-inline-block"><i class="fas fa-check"></i> Guardar</button>
									<a class="btn btn-info d-md-inline-block" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
								</div>

								<div class="row">                                    
									<div class="col-md-6">
										<div class="form-group">
											<label>Nombres</label>
											<input type="text" class="form-control" required name="nombres" value="<?php echo (isset($cliente) ? $cliente->nombres : set_value('nombres')); ?>">
											<?php echo form_error('nombres', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Apellidos</label>
											<input type="text" class="form-control" required name="apellidos" value="<?php echo (isset($cliente) ? $cliente->apellidos : set_value('apellidos')); ?>">
											<?php echo form_error('apellidos', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label>Dirección</label>
											<input type="text" class="form-control" required name="direccion" value="<?php echo (isset($cliente) ? $cliente->direccion : set_value('direccion')); ?>">
											<?php echo form_error('direccion', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Teléfono</label>
											<input type="text" class="form-control" required name="telefono" value="<?php echo (isset($cliente) ? $cliente->telefono : set_value('telefono')); ?>">
											<?php echo form_error('telefono', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Tipo Documento</label>
											<select name="tipo_doc" id="" class="form-control custom-select">
												<?php
												// Default to Cedula (0) when cliente has no tipo_doc set
												$tipo_sel = (isset($cliente) && isset($cliente->tipo_doc) && $cliente->tipo_doc !== '' && $cliente->tipo_doc !== null) ? $cliente->tipo_doc : 0;
												?>
												<option value="0" <?php echo ($tipo_sel == 0 ? 'selected' : ''); ?>>Cedula de Identidad</option>
												<option value="1" <?php echo ($tipo_sel == 1 ? 'selected' : ''); ?>>Numero RUC</option>
												<option value="2" <?php echo ($tipo_sel == 2 ? 'selected' : ''); ?>>Pasaporte</option>
												<option value="3" <?php echo ($tipo_sel == 3 ? 'selected' : ''); ?>>Otro</option>
											</select>
											<?php echo form_error('tipo_doc', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Numero Documento</label>
											<input type="text" class="form-control" name="numero_doc" required value="<?php echo (isset($cliente) ? $cliente->numero_doc : set_value('numero_doc')); ?>">
											<?php echo form_error('numero_doc', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Estado</label>
											<select name="estado" class="form-control" required>
												<?php if (isset($cliente)) : ?>
													<option value="1" <?php echo ($cliente->estado == 1 ? 'selected' : '') ?>>ACTIVO</option>
													<option value="0" <?php echo ($cliente->estado == 0 ? 'selected' : '') ?>>INACTIVO</option>
												<?php else : ?>
													<option value="1">ACTIVO</option>
													<option value="0">INACTIVO</option>
												<?php endif; ?>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label>Comentarios.</label>
											<textarea type="text" class="form-control" name="comentarios"><?php echo (isset($cliente) ? $cliente->comentarios : set_value('comentarios')); ?></textarea>
											<?php echo form_error('comentarios', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<!-- Personal adicionales -->
									<div class="col-md-3">
										<div class="form-group">
											<label>Fecha Nacimiento</label>
											<input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo (isset($cliente) ? $cliente->fecha_nacimiento : set_value('fecha_nacimiento')); ?>">
											<?php echo form_error('fecha_nacimiento', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-1">
										<div class="form-group">
											<label>Edad</label>
											<input type="number" class="form-control" name="edad" value="<?php echo (isset($cliente) ? $cliente->edad : set_value('edad')); ?>">
											<?php echo form_error('edad', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Estado Civil</label>
											<input type="text" class="form-control" name="estado_civil" value="<?php echo (isset($cliente) ? $cliente->estado_civil : set_value('estado_civil')); ?>">
											<?php echo form_error('estado_civil', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<!-- Cónyuge -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Nombre Cónyuge</label>
											<input type="text" class="form-control" name="nombre_conyuge" value="<?php echo (isset($cliente) ? $cliente->nombre_conyuge : set_value('nombre_conyuge')); ?>">
											<?php echo form_error('nombre_conyuge', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Cédula Cónyuge</label>
											<input type="text" class="form-control" name="dni_conyuge" value="<?php echo (isset($cliente) ? $cliente->dni_conyuge : set_value('dni_conyuge')); ?>">
											<?php echo form_error('dni_conyuge', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-5">
										<div class="form-group">
											<label>Ocupación Cónyuge</label>
											<input type="text" class="form-control" name="ocupacion_conyuge" value="<?php echo (isset($cliente) ? $cliente->ocupacion_conyuge : set_value('ocupacion_conyuge')); ?>">
											<?php echo form_error('ocupacion_conyuge', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Teléfono Cónyuge</label>
											<input type="text" class="form-control" name="telefono_conyuge" value="<?php echo (isset($cliente) ? $cliente->telefono_conyuge : set_value('telefono_conyuge')); ?>">
											<?php echo form_error('telefono_conyuge', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Dependientes</label>
											<input type="number" class="form-control" name="numero_dependientes" value="<?php echo (isset($cliente) ? $cliente->numero_dependientes : set_value('numero_dependientes')); ?>">
											<?php echo form_error('numero_dependientes', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<!-- Vivienda -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Condición Vivienda</label>
											<input type="text" class="form-control" name="condicion_vivienda" value="<?php echo (isset($cliente) ? $cliente->condicion_vivienda : set_value('condicion_vivienda')); ?>">
											<?php echo form_error('condicion_vivienda', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Años Residencia</label>
											<input type="number" class="form-control" name="tiempo_residir_anios" value="<?php echo (isset($cliente) ? $cliente->tiempo_residir_anios : set_value('tiempo_residir_anios')); ?>">
											<?php echo form_error('tiempo_residir_anios', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Meses Residencia</label>
											<input type="number" class="form-control" name="tiempo_residir_meses" value="<?php echo (isset($cliente) ? $cliente->tiempo_residir_meses : set_value('tiempo_residir_meses')); ?>">
											<?php echo form_error('tiempo_residir_meses', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<!-- Empleo / Empresa -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Nombre Empresa</label>
											<input type="text" class="form-control" name="nombre_empresa" value="<?php echo (isset($cliente) ? $cliente->nombre_empresa : set_value('nombre_empresa')); ?>">
											<?php echo form_error('nombre_empresa', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Dirección Empresa</label>
											<input type="text" class="form-control" name="direccion_empresa" value="<?php echo (isset($cliente) ? $cliente->direccion_empresa : set_value('direccion_empresa')); ?>">
											<?php echo form_error('direccion_empresa', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Teléfono Empresa</label>
											<input type="text" class="form-control" name="telefono_empresa" value="<?php echo (isset($cliente) ? $cliente->telefono_empresa : set_value('telefono_empresa')); ?>">
											<?php echo form_error('telefono_empresa', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Cargo / Puesto</label>
											<input type="text" class="form-control" name="cargo_puesto" value="<?php echo (isset($cliente) ? $cliente->cargo_puesto : set_value('cargo_puesto')); ?>">
											<?php echo form_error('cargo_puesto', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Años Empleo</label>
											<input type="number" class="form-control" name="tiempo_empleo_anios" value="<?php echo (isset($cliente) ? $cliente->tiempo_empleo_anios : set_value('tiempo_empleo_anios')); ?>">
											<?php echo form_error('tiempo_empleo_anios', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Meses Empleo</label>
											<input type="number" class="form-control" name="tiempo_empleo_meses" value="<?php echo (isset($cliente) ? $cliente->tiempo_empleo_meses : set_value('tiempo_empleo_meses')); ?>">
											<?php echo form_error('tiempo_empleo_meses', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Tipo Contrato</label>
											<input type="text" class="form-control" name="tipo_contrato" value="<?php echo (isset($cliente) ? $cliente->tipo_contrato : set_value('tipo_contrato')); ?>">
											<?php echo form_error('tipo_contrato', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Ingreso Mensual Neto</label>
											<input type="text" class="form-control" name="ingreso_mensual_neto" value="<?php echo (isset($cliente) ? $cliente->ingreso_mensual_neto : set_value('ingreso_mensual_neto')); ?>">
											<?php echo form_error('ingreso_mensual_neto', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Deducciones</label>
											<input type="text" class="form-control" name="deducciones" value="<?php echo (isset($cliente) ? $cliente->deducciones : set_value('deducciones')); ?>">
											<?php echo form_error('deducciones', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

									<!-- Negocio / Ventas -->
									<div class="col-12 col-md-4">
										<div class="form-group">
											<label>Nombre Negocio</label>
											<input type="text" class="form-control" name="nombre_negocio" value="<?php echo (isset($cliente) ? $cliente->nombre_negocio : set_value('nombre_negocio')); ?>">
											<?php echo form_error('nombre_negocio', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-4">
										<div class="form-group">
											<label>Actividad Económica</label>
											<input type="text" class="form-control" name="actividad_economica" value="<?php echo (isset($cliente) ? $cliente->actividad_economica : set_value('actividad_economica')); ?>">
											<?php echo form_error('actividad_economica', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-3">
										<div class="form-group">
											<label>Teléfono Negocio</label>
											<input type="text" class="form-control" name="telefono_negocio" value="<?php echo (isset($cliente) ? $cliente->telefono_negocio : set_value('telefono_negocio')); ?>">
											<?php echo form_error('telefono_negocio', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-2">
										<div class="form-group">
											<label>Años Operación</label>
											<input type="number" class="form-control" name="tiempo_operacion_anios" value="<?php echo (isset($cliente) ? $cliente->tiempo_operacion_anios : set_value('tiempo_operacion_anios')); ?>">
											<?php echo form_error('tiempo_operacion_anios', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-2">
										<div class="form-group">
											<label>Meses Operación</label>
											<input type="number" class="form-control" name="tiempo_operacion_meses" value="<?php echo (isset($cliente) ? $cliente->tiempo_operacion_meses : set_value('tiempo_operacion_meses')); ?>">
											<?php echo form_error('tiempo_operacion_meses', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-3">
										<div class="form-group">
											<label>Ventas en días buenos</label>
											<input type="text" class="form-control" name="ventas_buenos_amount" value="<?php echo (isset($cliente) ? $cliente->ventas_buenos_amount : set_value('ventas_buenos_amount')); ?>">
											<?php echo form_error('ventas_buenos_amount', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-3">
										<div class="form-group">
											<label>Ventas en días malos</label>
											<input type="text" class="form-control" name="ventas_malos_amount" value="<?php echo (isset($cliente) ? $cliente->ventas_malos_amount : set_value('ventas_malos_amount')); ?>">
											<?php echo form_error('ventas_malos_amount', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>
									<div class="col-12 col-md-3">
										<div class="form-group">
											<label>Ventas promedio mensual</label>
											<input type="text" class="form-control" name="ventas_promedio_mensual" value="<?php echo (isset($cliente) ? $cliente->ventas_promedio_mensual : set_value('ventas_promedio_mensual')); ?>">
											<?php echo form_error('ventas_promedio_mensual', '<div class="text-danger">', '</div>') ?>
										</div>
									</div>

								</div>
							</form>
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
