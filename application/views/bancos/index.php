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
							<h3>Cajas Registradas</h3>
						</div>
						<div class="card-body">
							<div class="table-responsive-sm">
								<table class="table data-table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Banco</th>
											<th>Estado</th>
											<th class="nosort text-right pr-25">Acciones</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($bancos as $banco) : ?>
											<tr>
												<td><?php echo $banco->id; ?> </td>
												<td><?php echo $banco->nombre; ?> </td>
												<td><?php echo ($banco->estado == 1 ? '<span class="badge  badge-success mb-1"><i class="fas fa-check-circle"></i> ACTIVO</span>' : '<span class="badge badge-warning mb-1"><i class="fas fa-lock"></i> DESACTIVADO</span>'); ?> </td>
												<td>
													<div class="table-actions">
														<a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $banco->id) ?>" data-toggle="tooltip" data-placement="top" title="Editar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-edit f-16 mr-15 text-success"></i></a>
														<a href="" data-toggle="modal" data-target="#banco-<?php echo $banco->id ?>" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-trash-2 f-16 text-danger"></i></a>
													</div>
												</td>
											</tr>
											<div class="modal fade" id="banco-<?php echo $banco->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
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
															<a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $banco->id) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
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
			<span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>

</div>
