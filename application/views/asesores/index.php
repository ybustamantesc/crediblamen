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
								<h3>Asesores Registrados</h3>
							</div>
							<div class="card-body">
								<div class="row mb-3 align-items-center">
									<div class="col-12 col-md-6">
										<h5 class="mb-0">Asesores Registrados</h5>
									</div>
									<div class="col-12 col-md-6 text-right">
										<div class="input-group" style="max-width:360px;margin-left:auto;">
											<input id="asesores-search" type="search" class="form-control form-control-sm" placeholder="Buscar asesor...">
											<div class="input-group-append">
												<button id="asesores-clear" class="btn btn-sm btn-outline-secondary" type="button">Limpiar</button>
											</div>
										</div>
									</div>
								</div>

								<style>
									.asesor-card .card{ padding:.5rem; }
									.asesor-card h6{ font-size: .9rem; margin-bottom:.25rem }
									.asesor-card p{ margin-bottom:.15rem; font-size:.82rem }
								</style>

								<div class="row" id="asesores-list">
									<?php foreach ($asesores as $asesor) : ?>
										<div class="col-6 col-sm-4 col-md-3 col-lg-2 asesor-card" data-search="<?php echo strtolower(htmlspecialchars($asesor->nombres . ' ' . $asesor->direccion . ' ' . $asesor->telefono)); ?>" data-name="<?php echo strtolower(htmlspecialchars($asesor->nombres)); ?>">
											<div class="card mb-2">
												<div class="card-body p-2">
													<h6 class="mb-1"><?php echo htmlspecialchars($asesor->nombres); ?></h6>
													<p class="text-muted small mb-1"><?php echo htmlspecialchars($asesor->direccion); ?></p>
													<p class="mb-1 small"><strong>Tel:</strong> <?php echo htmlspecialchars($asesor->telefono); ?></p>
													<div class="d-flex justify-content-between align-items-center">
														<div><?php echo ($asesor->estado == 1 ? '<span class="badge badge-success">ACTIVO</span>' : '<span class="badge badge-warning">INACTIVO</span>'); ?></div>
														<div>
															<a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $asesor->idasesor) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
															<a href="#" data-toggle="modal" data-target="#cliente-<?php echo $asesor->idasesor ?>" class="btn btn-sm btn-danger">Eliminar</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="text-center mt-3">
									<button id="asesores-show-more" class="btn btn-sm btn-outline-primary">Mostrar más</button>
								</div>

								<!-- Modals (keep them outside grid for accessibility) -->
								<?php foreach ($asesores as $asesor) : ?>
								<div class="modal fade" id="cliente-<?php echo $asesor->idasesor ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
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
												<a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $asesor->idasesor) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
											</div>
										</div>
									</div>
								</div>
								<?php endforeach; ?>

								<script>
								(function(){
									var perPage = 25;
									var $cards = Array.prototype.slice.call(document.querySelectorAll('#asesores-list .asesor-card'));
									var total = $cards.length;
									var shown = 0;
									var $btn = document.getElementById('asesores-show-more');

									function showNext(){
										var next = Math.min(shown + perPage, total);
										for(var i=shown;i<next;i++){
											$cards[i].style.display = '';
										}
										shown = next;
										if(shown >= total) $btn.style.display = 'none';
									}

									function resetPagination(){
										// hide all then show first page
										$cards.forEach(function(c){ c.style.display = 'none'; });
										shown = 0;
										if(total > 0){ $btn.style.display = ''; showNext(); } else { $btn.style.display = 'none'; }
									}

									// initial hide all
									$cards.forEach(function(c){ c.style.display = 'none'; });
									resetPagination();

									$btn.addEventListener('click', function(){ showNext(); });

									// search
									var $search = document.getElementById('asesores-search');
									var $clear = document.getElementById('asesores-clear');

									function doSearch(){
										var q = $search.value.trim().toLowerCase();
										if(!q){
											// clear search -> restore pagination
											resetPagination();
											return;
										}
										// show only matching cards
										$cards.forEach(function(c){
											var txt = (c.getAttribute('data-search')||'');
											if(txt.indexOf(q) !== -1){ c.style.display = ''; } else { c.style.display = 'none'; }
										});
										// hide show more while searching
										$btn.style.display = 'none';
									}

									$search.addEventListener('input', doSearch);
									$clear.addEventListener('click', function(){ $search.value=''; resetPagination(); });
								})();
								</script>
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
