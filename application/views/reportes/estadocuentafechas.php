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

			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header d-block">
							<h3>Consultar Créditos Por Cliente y Fecha de Cuota</h3>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label for="">Cliente</label>
										<select name="" id="cboClienteECF" class="form-control select2">
											<option value="">SELECCIONAR</option>
											<?php foreach ($clientes as $cliente) : ?>
												<option value="<?php echo $cliente->idcliente; ?>"><?php echo $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="">Fecha Cuota</label>
										<input type="date" class="form-control" id="fechaCuota" value="<?php echo date("Y-m-d"); ?>">
									</div>
								</div>
							</div>
							<table class="table data-table table-striped table-bordered table-hover" id="tblEstadoCuentaClienteFechaPago">
								<thead>
									<tr>
										<th class="nosort">#</th>
										<th>Crédito</th>
										<th>Cliente</th>
										<th>Asesor</th>
										<th>Fecha Cuota</th>
										<th>N° Cuota</th>
										<th>Monto Cuota</th>
										<th>Monto Pendiente</th>
										<th>Fecha Pago</th>
										<th>Monto Pago</th>
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
	<footer class="footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> Servifact v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>
</div>
