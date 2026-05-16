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
						<div class="card-header">
							<h3>Consultar Créditos</h3>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="">Fecha Inicio</label>
										<input type="date" class="form-control" id="fechaInicio">
									</div>
                                </div>
                                <div class="col-md-3">
									<div class="form-group">
										<label for="">Fecha Fin</label>
										<input type="date" class="form-control" id="fechFin">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Asesor</label>
										<select class="form-control select2" id="pagosAsesor" required>
											<option value="">SELECCIONAR</option>
											<option value="0">TODOS</option>
											<?php foreach ($asesores as $asesor) : ?>
												<option value="<?php echo $asesor->idasesor; ?>"><?php echo $asesor->nombres; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="text-center">
								<button type="button" class="btn btn-primary mb-2" id="btnConsultarAsesorPagos"><i class="fas fa-search"></i> Consultar</button>
								<button type="button" class="btn bg-danger text-white mb-2 ml-2" id="btnPdfPagosAsesor"><i class="fas fa-file-pdf"></i> Exportar a PDF</button>
								<button type="button" class="btn bg-success text-white mb-2 ml-2" id="btnLimpiar"><i class="fas fa-backspace"></i> Nueva consulta</button>
							</div>
							<table class="table  table-hover" id="tablaPagosAsesor">
								<thead>
									<tr>
										<th>#</th>
										<th>Cliente</th>
										<th>Asesor</th>
										<th>Fecha Pago</th>
										<th>Monto Pago</th>
										<th>Descuento</th>
									</tr>
								</thead>
							</table>
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
