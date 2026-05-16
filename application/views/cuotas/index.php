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
								<!-- <a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo</a> -->
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
							<h3>Listado Cuotas según estado.</h3>
							<div class="form-group mt-5">
								<div class="row">
									<div class="col-md-4">
										<select name="" id="CboEstado" class="form-control">
											<option value="">SELECCIONAR</option>
											<option value="0">PAGADO</option>
											<option value="1">PENDIENTE</option>
											<option value="2">VENCIDAS</option>
										</select>
									</div>
									<div class="col-md-4">
										<button type="button" id="btnExportarPdf" class="btn btn-danger">Exportar PDF</button>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive-sm">
								<table class="table data-table  table-sm table-hover" id="tablCuotasEstado">
									<thead>
										<tr>
											<!-- <th>#</th> -->
											<th>Cliente</th>
											<th>Asesor</th>
											<th>N° Cuota</th>
											<th>Fecha Cuota</th>
											<th>Fecha Pago</th>
											<th>Monto Pagado</th>
											<th>Monto Cuota</th>
											<th>Monto Pendiente</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
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
