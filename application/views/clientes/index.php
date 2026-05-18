<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
	<?php $this->load->view('layout/sidebar.php'); ?>
	<div class="main-content">
		<div class="container-fluid">
			<div class="page-header">
				<div class="row align-items-end">
					<div class="col-lg-8">
						<div class="page-header-title">
							<i class="<?php echo isset($icono) ? $icono : 'fa fa-users'; ?> bg-blue"></i>
							<div class="d-inline">
								<h5> <?php echo isset($titulo) ? $titulo : 'Clientes'; ?> </h5>
								<span><?php echo isset($subtitulo) ? $subtitulo : ''; ?></span>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<nav class="breadcrumb-container" aria-label="breadcrumb">
							<ol class="breadcrumb">
								<a data-toggle="tooltip" data-placement="right" title="Nuevo cliente" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo</a>
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
							<h3>Clientes</h3>
						</div>
						<div class="card-body">
							<div class="row mb-3 align-items-center">
								<div class="col-12 col-md-6">
									<div class="input-group input-group-sm">
										<input id="clientes-search" type="search" class="form-control" placeholder="Buscar clientes por nombre, documento o teléfono">
										<div class="input-group-append">
											<span class="input-group-text" id="clientes-count"><?php echo count($clientes); ?></span>
										</div>
									</div>
									<small class="form-text text-muted">Resultados: <span id="clientes-count-text"><?php echo count($clientes); ?></span></small>
								</div>
								<div class="col-12 col-md-6 text-md-right">
									<!-- actions if needed -->
								</div>
							</div>

							<div class="row" id="clientes-cards">
								<?php foreach ($clientes as $cliente) : ?>
									<?php $search_text = htmlspecialchars(strtolower(trim((isset($cliente->apellidos)?$cliente->apellidos:'') . ' ' . (isset($cliente->nombres)?$cliente->nombres:'') . ' ' . (isset($cliente->numero_doc)?$cliente->numero_doc:'') . ' ' . (isset($cliente->telefono)?$cliente->telefono:''))));
										$name_text = htmlspecialchars(strtolower(trim((isset($cliente->apellidos)?$cliente->apellidos:'') . ' ' . (isset($cliente->nombres)?$cliente->nombres:'')))); ?>
									<div class="col-12 col-md-6 col-lg-4 mb-3 cliente-card" data-search="<?php echo $search_text; ?>" data-name="<?php echo $name_text; ?>">
										<div class="card h-100">
											<div class="card-body">
												<h6 class="mb-1 small font-weight-bold"><?php echo htmlspecialchars($cliente->apellidos . ', ' . $cliente->nombres); ?></h6>
												<p class="mb-1 small text-muted"><?php echo htmlspecialchars($cliente->numero_doc); ?> — <?php echo htmlspecialchars($cliente->telefono); ?></p>
												<p class="mb-1"><?php if (isset($cliente->rechazado) && $cliente->rechazado == 1) echo '<span class="badge badge-danger">RECHAZADO</span>'; else echo ($cliente->estado == 1 ? '<span class="badge badge-success">ACTIVO</span>' : '<span class="badge badge-warning">INACTIVO</span>'); ?></p>
											</div>
											<div class="card-footer text-right">
												<?php if (isset($cliente->rechazado) && $cliente->rechazado == 1) : ?>
													<a href="#" data-toggle="modal" data-target="#verMotivoMain-<?php echo $cliente->idcliente; ?>" class="btn btn-sm btn-info">Ver motivo</a>
													<a href="<?php echo base_url('clientes/download_rechazo/' . $cliente->idcliente); ?>" class="btn btn-sm btn-dark">TXT</a>
													<a href="<?php echo base_url('clientes/download_rechazo_pdf/' . $cliente->idcliente); ?>" class="btn btn-sm btn-primary">PDF</a>
												<?php else: ?>
												<?php 
													$user = $this->ion_auth->user()->row();
													$es_promotor = (isset($user->perfil) && $user->perfil == 4);
												?>
												<?php if (!$es_promotor): ?>
													<a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $cliente->idcliente) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
													<a href="#" data-toggle="modal" data-target="#rechazar-<?php echo $cliente->idcliente ?>" class="btn btn-sm btn-warning">Rechazar</a>
													<a href="#" data-toggle="modal" data-target="#cliente-<?php echo $cliente->idcliente ?>" class="btn btn-sm btn-danger">Eliminar</a>
												<?php else: ?>
													<span class="text-muted small">Sin permisos de edición</span>
												<?php endif; ?>
												<?php endif; ?>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>

							<div id="clientes-noresults" class="alert alert-warning" style="display:none;">No se encontraron clientes.</div>

							<!-- Modals for each cliente -->
							<?php foreach ($clientes as $cliente) : ?>
								<div class="modal fade" id="rechazar-<?php echo $cliente->idcliente ?>" tabindex="-1" role="dialog" aria-labelledby="rechazarLabel-<?php echo $cliente->idcliente ?>" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header bg-warning text-dark">
												<h5 class="modal-title" id="rechazarLabel-<?php echo $cliente->idcliente ?>"><i class="fas fa-exclamation-circle"></i> Marcar cliente como rechazado</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body">
												<p>¿Confirma que desea marcar este cliente como <strong>Rechazado</strong>? Esta acción moverá una copia a la lista de rechazados y desactivará el cliente.</p>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
												<form method="post" action="<?php echo base_url($this->router->fetch_class() . '/mark_rejected/' . $cliente->idcliente) ?>" style="display:inline;">
													<?php if (function_exists('csrf_token') || (isset($this->security) && method_exists($this->security,'get_csrf_token_name'))): ?>
														<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
													<?php endif; ?>
													<div class="form-group">
														<label for="rechazo_motivo_<?php echo $cliente->idcliente; ?>">Motivo de rechazo</label>
														<textarea id="rechazo_motivo_<?php echo $cliente->idcliente; ?>" name="rechazo_motivo" class="form-control" rows="3" required></textarea>
													</div>
													<button type="submit" class="btn btn-warning">Sí, marcar como rechazado</button>
												</form>
											</div>
										</div>
									</div>
								</div>

								<div class="modal fade" id="verMotivoMain-<?php echo $cliente->idcliente; ?>" tabindex="-1" role="dialog" aria-labelledby="verMotivoMainLabel-<?php echo $cliente->idcliente; ?>" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header bg-info text-white">
												<h5 class="modal-title" id="verMotivoMainLabel-<?php echo $cliente->idcliente; ?>">Motivo de rechazo</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body">
												<p><?php echo isset($cliente->rechazo_motivo) ? nl2br(html_escape($cliente->rechazo_motivo)) : '<em>Sin motivo registrado</em>'; ?></p>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
											</div>
										</div>
									</div>
								</div>

								<div class="modal fade" id="cliente-<?php echo $cliente->idcliente ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
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
												<a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $cliente->idcliente) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>

							<style>
								.fuzzy-suggest{ box-shadow: 0 0 0.4rem rgba(255,193,7,0.35); }
							</style>
							<script>
								// Exact-first search; if none found, show fuzzy suggestions using Levenshtein distance
								document.addEventListener('DOMContentLoaded', function(){
									var input = document.getElementById('clientes-search');
									var cards = Array.prototype.slice.call(document.querySelectorAll('.cliente-card'));
									var countText = document.getElementById('clientes-count-text');
									var noEl = document.getElementById('clientes-noresults');
									function updateCount(n){ if(countText) countText.textContent = n; }

									function levenshtein(a,b){
										if(a===b) return 0;
										if(a.length===0) return b.length;
										if(b.length===0) return a.length;
										var matrix = [];
										var i,j;
										for(i=0;i<=b.length;i++){ matrix[i]=[i]; }
										for(j=0;j<=a.length;j++){ matrix[0][j]=j; }
										for(i=1;i<=b.length;i++){
											for(j=1;j<=a.length;j++){
												var cost = (a[j-1] === b[i-1]) ? 0 : 1;
												matrix[i][j] = Math.min(
													matrix[i-1][j] + 1,      // deletion
													matrix[i][j-1] + 1,      // insertion
													matrix[i-1][j-1] + cost  // substitution
												);
												// transposition
												if(i>1 && j>1 && a[j-1] === b[i-2] && a[j-2] === b[i-1]){
													matrix[i][j] = Math.min(matrix[i][j], matrix[i-2][j-2] + cost);
												}
											}
										}
										return matrix[b.length][a.length];
									}

									if(!input) return;
									input.addEventListener('input', function(){
										var q = input.value.trim().toLowerCase();
										if(q === ''){
											cards.forEach(function(card){ card.style.display=''; card.classList.remove('fuzzy-suggest'); });
											updateCount(cards.length);
											if(noEl) noEl.style.display='none';
											return;
										}

										// Exact substring matches first
										var exact = [];
										cards.forEach(function(card){
											var txt = card.getAttribute('data-search') || '';
											if(txt.indexOf(q) !== -1) exact.push(card);
										});

										if(exact.length > 0){
											// show only exact matches
											cards.forEach(function(card){
												if(exact.indexOf(card) !== -1){ card.style.display=''; card.classList.remove('fuzzy-suggest'); }
												else{ card.style.display='none'; card.classList.remove('fuzzy-suggest'); }
											});
											updateCount(exact.length);
											if(noEl) noEl.style.display = exact.length ? 'none' : '';
											return;
										}

										// No exact results -> fuzzy suggestions based on name (data-name)
										var suggestions = [];
										cards.forEach(function(card){
											var name = (card.getAttribute('data-name') || '').toLowerCase();
											var searchfield = (card.getAttribute('data-search') || '').toLowerCase();
											// compute distance against name and against full search field
											var d1 = name ? levenshtein(q, name) : Infinity;
											var d2 = searchfield ? levenshtein(q, searchfield) : Infinity;
											var dist = Math.min(d1, d2);
											suggestions.push({card: card, dist: dist});
										});
										suggestions.sort(function(a,b){ return a.dist - b.dist; });

										// Determine threshold: accept small absolute distances or relative small (<=40%)
										var results = [];
										for(var i=0;i<suggestions.length && results.length<12;i++){
											var s = suggestions[i];
											var len = Math.max((s.card.getAttribute('data-name')||'').length, q.length, 1);
											var rel = s.dist / len;
											if(s.dist <= 2 || rel <= 0.4) results.push(s.card);
										}

										// Show suggestions (or none)
										cards.forEach(function(card){ card.style.display='none'; card.classList.remove('fuzzy-suggest'); });
										results.forEach(function(card){ card.style.display=''; card.classList.add('fuzzy-suggest'); });
										updateCount(results.length);
										if(noEl) noEl.style.display = results.length ? 'none' : '';
									});
								});
							</script>

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
												</div>
												<div id="clientes-noresults" class="alert alert-warning" style="display:none;">No se encontraron clientes.</div>

												<script>
												document.addEventListener('DOMContentLoaded', function(){
													var input = document.getElementById('clientes-search');
													var cards = Array.prototype.slice.call(document.querySelectorAll('.cliente-card'));
													var countText = document.getElementById('clientes-count-text');
													var noEl = document.getElementById('clientes-noresults');
													function updateCount(n){ if(countText) countText.textContent = n; }
													if(!input) return;
													input.addEventListener('input', function(){
														var q = input.value.trim().toLowerCase();
														var visible = 0;
														cards.forEach(function(card){
															var txt = card.getAttribute('data-search') || '';
															if(q === '' || txt.indexOf(q) !== -1){
																card.style.display = '';
															visible++;
															} else {
															card.style.display = 'none';
															}
														});
														updateCount(visible);
														if(noEl) noEl.style.display = visible ? 'none' : '';
													});
												});
												</script>
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
