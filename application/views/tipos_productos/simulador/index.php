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
					<div class="col-lg-4">
						<nav class="breadcrumb-container" aria-label="breadcrumb">
							<ol class="breadcrumb">
								<a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo</a>
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
			<?php if ($message = $this->session->flashdata('info')) : ?>
				<div class="row">
					<div class="col-md-12">
						<div class="alert bg-info alert-info text-white alert-dismissible fade show" role="alert">
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
						<div class="card-header d-block">
							<h3>Simulación de Créditos Registrados</h3>
						</div>
						<div class="card-body">
							<div class="table-responsive-sm">
								<table class="table data-table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Fecha</th>
											<th>Cliente</th>
											<th>Asesor</th>
											<th>Monto Crédito</th>
											<th>Forma Pago</th>
											<th>Estado</th>
											<th class="text-right pr-25 nosort">Acciones</th>
										</tr>
									</thead>
									<tbody>

										<?php foreach ($simulaciones as $row) : ?>
											<tr>
												<td><?php echo $row->idsimulacion; ?></td>
												<td><?php echo formatoFechaCorta($row->fecha_simulacion); ?> </td>
												<td><?php echo $row->apellidos . ', ' . $row->nombres; ?> </td>
												<td><?php echo $row->nombre_asesor; ?> </td>
												<td><?php echo number_format($row->monto_credito, 2); ?> </td>
												<td class="text-center">
													<?php if ($row->forma_pago == 0) : ?>
														<span class="badge badge-pill badge-success mb-1"><i class="fas fa-calendar-day"></i> DIARIO</span>
													<?php elseif ($row->forma_pago == 1) : ?>
														<span class="badge badge-pill badge-primary mb-1"><i class="fas fa-calendar-minus"></i> SEMANAL</span>
													<?php elseif ($row->forma_pago == 2) : ?>
														<span class="badge badge-pill badge-info mb-1"><i class="far fa-calendar-alt"></i> QUINCENAL</span>
													<?php elseif ($row->forma_pago == 3) : ?>
														<span class="badge badge-pill badge-warning mb-1"><i class="fas fa-calendar-alt"></i> MENSUAL</span>
													<?php endif; ?>
												</td>
												<td class="text-center">
													<?php if ($row->estado == 1) : ?>
														<span class="badge  badge-pill  badge-danger mb-1"><i class="fas fa-sync-alt"></i> PENDIENTE</span>
													<?php elseif ($row->estado == 0) : ?>
														<span class="badge  badge-pill badge-primary mb-1"><i class="fas fa-check-circle"></i> PROCESADO</span>
													<?php endif; ?>
												</td>
												<td>
													<div class="table-actions">
														<a target="_blank" href="<?php echo base_url($this->router->fetch_class() . '/pdf/' . $row->idsimulacion); ?>" data-toggle="tooltip" data-placement="top" title="Visualizar Cuotas"><i class="fas fa-file-pdf text-danger"></i></a>

														<a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $row->idsimulacion) ?>" data-toggle="tooltip" data-placement="top" title="Editar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-edit f-16 text-success"></i></a>
														<a href="" data-toggle="modal" data-target="#simulacion-<?php echo $row->idsimulacion ?>" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-trash-2 f-16 text-danger"></i></a>

													</div>
												</td>
											</tr>
											<div class="modal fade" id="simulacion-<?php echo $row->idsimulacion ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
												<div class="modal-dialog modal-dialog-centered" role="document">
													<div class="modal-content">
														<div class="modal-header bg-danger text-white">
															<h5 class="modal-title" id="exampleModalCenterLabel"><i class="fas fa-exclamation-triangle"></i> ¿Quieres eliminar el registro?</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														</div>
														<div class="modal-body">
															<p>Si desea eliminar el registro click en <strong>Sí, eliminar.</strong></p>
														</div>
														<div class="modal-footer">
															<button type="button" data-toggle="tooltip" data-placement="top" title="Cancelar" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
															<a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $row->idsimulacion) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
														</div>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
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
