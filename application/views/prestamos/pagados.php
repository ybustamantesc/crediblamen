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
						<div class="card-body">
							<div class="table-responsive-sm">
								<table class="table data-table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Cliente</th>
											<th>Fecha Crédito</th>
											<th>Monto Crédito</th>
											<th>Interés</th>
											<th>Coutas</th>
											<th>Total Interés</th>
											<th>Total Pagar</th>
											<th>Forma Pago</th>
											<th>Estado</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php $i = 0; ?>
										<?php foreach ($prestamos as $prestamo) : ?>
											<?php $i++; ?>
											<tr>
												<td><?php echo $i; ?> </td>
												<td><?php echo $prestamo->apellidos . ', ' . $prestamo->nombres; ?> </td>
												<td><?php echo $prestamo->fecha_credito; ?> </td>
												<td><?php echo number_format($prestamo->monto_credito, 2); ?> </td>
												<td><?php echo number_format($prestamo->interes_credito, 2) . '%'; ?> </td>
												<td><?php echo $prestamo->numero_coutas; ?> </td>
												<td><?php echo number_format($prestamo->total_interes, 2); ?> </td>
												<td><?php echo number_format($prestamo->total_pagar, 2); ?> </td>
												<td class="text-center">
													<?php if ($prestamo->forma_pago == 0) : ?>
														<span class="badge badge-pill badge-success mb-1">DIARIO</span>
													<?php elseif ($prestamo->forma_pago == 1) : ?>
														<span class="badge badge-pill badge-primary mb-1">SEMANAL</span>
													<?php elseif ($prestamo->forma_pago == 2) : ?>
														<span class="badge badge-pill badge-info mb-1">QUINCENAL</span>
													<?php elseif ($prestamo->forma_pago == 3) : ?>
														<span class="badge badge-pill badge-warning mb-1">MENSUAL</span>
													<?php endif; ?>
												</td>
												<td class="text-center">
													<?php if ($prestamo->estado == 0) : ?>
														<span class="badge  badge-pill  badge-success mb-1"><i class="fas fa-check-circle"></i> PAGADO</span>
													<?php elseif ($prestamo->estado == 2) : ?>
														<span class="badge  badge-pill badge-primary mb-1"><i class="fas fa-sync-alt"></i> EN PROCESO</span>
													<?php elseif ($prestamo->estado == 1) : ?>
														<span class="badge  badge-pill badge-danger mb-1"><i class="fas fa-lock"></i> PENDIENTE</span>
													<?php endif; ?>
												</td>
												<td>
													<div class="table-actions text-center">
														<a target="_blank" href="<?php echo base_url("prestamo" . '/pdf/' . $prestamo->id); ?>" data-toggle="tooltip" data-placement="top" title="Estado de Cuenta"><i class="fas fa-file-pdf text-danger"></i></a>
													</div>
												</td>
											</tr>

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
