<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
	<!-- Branding styles moved to public/css/branding.css -->
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

			<!-- Bienvenida y gráficas -->
			<div class="row">
				<div class="col-12 mb-3">
					<div class="card">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
									<h4 class="mb-1">Bienvenido, <?php echo isset($display_name) ? $display_name : ''; ?></h4>
									<div class="d-flex align-items-center">
										<a href="<?php echo site_url('menu'); ?>" class="btn btn-sm btn-outline-secondary mr-2" id="btnVolverMenu"><i class="fa fa-arrow-left mr-1"></i> Volver al Menú</a>
										<button id="refreshCharts" class="btn btn-sm btn-outline-primary mr-2">Refrescar gráficas</button>
										<div class="form-check form-switch mr-3">
											<input class="form-check-input" type="checkbox" id="autoRefreshToggle">
											<label class="form-check-label small text-muted" for="autoRefreshToggle">Auto-refresh (5m)</label>
										</div>
										<div class="small text-muted" id="lastRefresh">Última actualización: --</div>
									</div>
							</div>
							<p class="text-muted mt-2">Resumen rápido del sistema. Aquí tienes las estadísticas principales de los últimos 12 meses.</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Toolbar para acciones rápidas sobre las gráficas -->
			<div class="row mb-3">
				<div class="col-12">
					<div class="card">
						<div class="card-body chart-toolbar">
							<div class="d-flex w-100 align-items-center justify-content-between mb-2">
								<div class="toolbar-title"><h6 class="mb-0">Reportes Rápidos</h6></div>
								<div class="small text-muted">Acciones rápidas — atajos para revisar datos relevantes</div>
							</div>
							<div class="d-flex align-items-center justify-content-between w-100">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<a href="<?php echo site_url('reportes/pagos_hoy'); ?>" class="btn btn-sm chart-action-btn ajax-report-btn" data-report-title="Pagos de Hoy"><i class="fas fa-dollar-sign chart-action-icon"></i> Pagos de Hoy</a>
								<a href="<?php echo site_url('reportes/vencidos'); ?>" class="btn btn-sm chart-action-btn ajax-report-btn" data-report-title="Vencidos"><i class="fas fa-exclamation-triangle chart-action-icon"></i> Vencidos</a>
								<a href="<?php echo site_url('reportes/estadisticas'); ?>" class="btn btn-sm chart-action-btn ajax-report-btn" data-report-title="Estadísticas"><i class="fas fa-chart-line chart-action-icon"></i> Estadísticas</a>
								<a href="<?php echo site_url('reportes/informes'); ?>" class="btn btn-sm chart-action-btn ajax-report-btn" data-report-title="Informes"><i class="fas fa-file-alt chart-action-icon"></i> Informes</a>
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- TARJETAS DE ESTADÍSTICAS CLAVE (KPIs) -->
			<div class="row mb-4">
				<!-- 1. Total de Ingresos -->
				<div class="col-lg-2-4 col-md-6 col-sm-12 mb-3">
					<div class="stat-card stat-card-ingresos">
						<div class="stat-card-header">
							<i class="fas fa-arrow-up stat-card-icon"></i>
							<span class="stat-card-trend trend-up">↑ --</span>
						</div>
						<div class="stat-card-body">
							<p class="stat-card-label">Total de Ingresos</p>
							<h3 class="stat-card-value">--</h3>
							<small class="stat-card-subtitle">Últimas 30 días</small>
						</div>
					</div>
				</div>

				<!-- 2. Total de Egresos -->
				<div class="col-lg-2-4 col-md-6 col-sm-12 mb-3">
					<div class="stat-card stat-card-egresos">
						<div class="stat-card-header">
							<i class="fas fa-arrow-down stat-card-icon"></i>
							<span class="stat-card-trend trend-down">↓ --</span>
						</div>
						<div class="stat-card-body">
							<p class="stat-card-label">Total de Egresos</p>
							<h3 class="stat-card-value">--</h3>
							<small class="stat-card-subtitle">Últimas 30 días</small>
						</div>
					</div>
				</div>

				<!-- 3. Balance Actual -->
				<div class="col-lg-2-4 col-md-6 col-sm-12 mb-3">
					<div class="stat-card stat-card-balance">
						<div class="stat-card-header">
							<i class="fas fa-wallet stat-card-icon"></i>
							<span class="stat-card-trend trend-neutral">→ --</span>
						</div>
						<div class="stat-card-body">
							<p class="stat-card-label">Balance Actual</p>
							<h3 class="stat-card-value">--</h3>
							<small class="stat-card-subtitle">Neto disponible</small>
						</div>
					</div>
				</div>

				<!-- 4. Clientes Activos -->
				<div class="col-lg-2-4 col-md-6 col-sm-12 mb-3">
					<div class="stat-card stat-card-clientes">
						<div class="stat-card-header">
							<i class="fas fa-users stat-card-icon"></i>
							<span class="stat-card-trend trend-up">↑ --</span>
						</div>
						<div class="stat-card-body">
							<p class="stat-card-label">Clientes Activos</p>
							<h3 class="stat-card-value">--</h3>
							<small class="stat-card-subtitle">En el mes actual</small>
						</div>
					</div>
				</div>

				<!-- 5. Crecimiento Mensual -->
				<div class="col-lg-2-4 col-md-6 col-sm-12 mb-3">
					<div class="stat-card stat-card-crecimiento">
						<div class="stat-card-header">
							<i class="fas fa-chart-line stat-card-icon"></i>
							<span class="stat-card-trend trend-up">↑ --</span>
						</div>
						<div class="stat-card-body">
							<p class="stat-card-label">Crecimiento Mensual</p>
							<h3 class="stat-card-value">--</h3>
							<small class="stat-card-subtitle">vs mes anterior</small>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-12 mb-3">
					<div class="card chart-card">
						<div class="card-header"><h6>Créditos - Últimos 12 meses (cantidad)</h6></div>
						<div class="card-body chart-canvas-wrap">
							<canvas id="creditsChart" height="120"></canvas>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-12 mb-3">
					<div class="card chart-card">
						<div class="card-header"><h6>Créditos por Estado</h6></div>
						<div class="card-body d-flex justify-content-center align-items-center chart-canvas-wrap">
							<canvas id="statusChart" width="250" height="250"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card chart-card">
						<div class="card-header"><h6>Pagos - Monto mensual (últimos 12 meses)</h6></div>
						<div class="card-body chart-canvas-wrap">
							<canvas id="paymentsChart" height="80"></canvas>
						</div>
					</div>
				</div>
			</div>

			<script>
			document.addEventListener('DOMContentLoaded', function () {
				const endpointCredits = '<?php echo base_url('home/credits_chart'); ?>';
				const endpointPayments = '<?php echo base_url('home/payments_chart'); ?>';
				const endpointStatus = '<?php echo base_url('home/status_chart'); ?>';
				let charts = {};

				// Apply Chart.js polished defaults for a futuristic, corporate look
				if (typeof Chart !== 'undefined') {
					Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, system-ui, -apple-system, 'Helvetica Neue', Arial";
					Chart.defaults.color = 'rgba(7,48,72,0.92)';
					Chart.defaults.plugins.legend.labels.color = 'rgba(7,48,72,0.78)';
					Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(7,48,72,0.95)';
					Chart.defaults.plugins.tooltip.titleColor = '#ffffff';
					Chart.defaults.plugins.tooltip.bodyColor = '#ffffff';
					Chart.defaults.animation.easing = 'easeOutQuart';
					Chart.defaults.elements.point.radius = 3;
					Chart.defaults.elements.point.hoverRadius = 6;
				}

				function renderCharts(data){
					const months = data.months || [];
					const credits = data.credits || [];
					const payments = data.payments || [];
					const statusLabels = data.status_labels || [];
					const statusData = data.status_data || [];

					// Destroy existing charts to avoid duplicates
					if (charts.credits) charts.credits.destroy();
					if (charts.status) charts.status.destroy();
					if (charts.payments) charts.payments.destroy();

					// Credits bar chart (gradient bars, rounded)
					const ctxCredits = document.getElementById('creditsChart').getContext('2d');
					const gradBar = ctxCredits.createLinearGradient(0,0,0,300);
					gradBar.addColorStop(0, 'rgba(0,163,137,0.96)');
					gradBar.addColorStop(1, 'rgba(7,48,72,0.12)');

					charts.credits = new Chart(ctxCredits, {
						type: 'bar',
						data: {
							labels: months,
							datasets: [{
								label: 'Créditos',
								data: credits,
								backgroundColor: gradBar,
								borderColor: 'rgba(7,48,72,0.12)',
								borderRadius: 8,
								barPercentage: 0.62
							}]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							scales: {
								x: { grid: { display: false }, ticks: { color: 'rgba(7,48,72,0.7)' } },
								y: { beginAtZero: true, ticks: { color: 'rgba(7,48,72,0.7)' }, grid: { color: 'rgba(7,48,72,0.04)' } }
							},
							plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(ctx){ return ctx.dataset.label + ': ' + (ctx.raw || 0); } } } }
						}
					});

					// Status doughnut chart (clean segments with white separators)
					const ctxStatus = document.getElementById('statusChart').getContext('2d');
					charts.status = new Chart(ctxStatus, {
						type: 'doughnut',
						data: {
							labels: statusLabels,
							datasets: [{
								data: statusData,
								backgroundColor: ['#ffd34a', '#00a389', '#073048', '#6c8ef3', '#f36c6c'],
								borderColor: '#ffffff',
								borderWidth: 2
							}]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							cutout: '55%',
							plugins: { legend: { position: 'bottom', labels: { color: 'rgba(7,48,72,0.8)' } } }
						}
					});

					// Payments line chart (smooth line with soft gradient fill)
					const ctxPayments = document.getElementById('paymentsChart').getContext('2d');
					const gradFill = ctxPayments.createLinearGradient(0,0,0,200);
					gradFill.addColorStop(0, 'rgba(7,48,72,0.16)');
					gradFill.addColorStop(1, 'rgba(0,163,137,0.03)');

					charts.payments = new Chart(ctxPayments, {
						type: 'line',
						data: {
							labels: months,
							datasets: [{
								label: 'Monto Pagado',
								data: payments,
								borderColor: '#073048',
								backgroundColor: gradFill,
								tension: 0.35,
								pointBackgroundColor: '#ffffff',
								pointBorderColor: '#073048',
								pointRadius: 4,
								fill: true
							}]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							scales: { y: { beginAtZero: true, ticks: { callback: function(value){ try{ return new Intl.NumberFormat('es-PE',{style:'currency',currency:'PEN'}).format(value); }catch(e){ return value; } }, color: 'rgba(7,48,72,0.7)' }, grid: { color: 'rgba(7,48,72,0.04)' } }, x: { ticks: { color: 'rgba(7,48,72,0.7)' }, grid: { display: false } } },
							plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(context){ let v = context.raw || 0; try{ return new Intl.NumberFormat('es-PE',{style:'currency',currency:'PEN'}).format(v); }catch(e){ return v; } } }, backgroundColor: 'rgba(7,48,72,0.95)', titleColor: '#fff', bodyColor: '#fff' } }
						}
					});
				}

				async function fetchAndRender(){
					try{
						const [cResp, pResp, sResp] = await Promise.all([
							fetch(endpointCredits, { credentials: 'same-origin' }),
							fetch(endpointPayments, { credentials: 'same-origin' }),
							fetch(endpointStatus, { credentials: 'same-origin' })
						]);

						if (!cResp.ok || !pResp.ok || !sResp.ok) throw new Error('Error al obtener datos');

						const [cJson, pJson, sJson] = await Promise.all([cResp.json(), pResp.json(), sResp.json()]);

						// Combinar resultados (months se toma de credits o payments)
						const combined = {
							months: cJson.months || pJson.months || [],
							months_keys: cJson.months_keys || pJson.months_keys || [],
							credits: cJson.credits || [],
							payments: pJson.payments || [],
							status_labels: sJson.status_labels || [],
							status_data: sJson.status_data || []
						};

						renderCharts(combined);
						updateLastRefreshLabel();
					}catch(err){
						console.error(err);
						// mostrar mensaje pequeño
						alert('No se pudieron cargar las gráficas. Revisa la consola.');
					}
				}

				// Inicializar
				fetchAndRender();

				// Botón refrescar
				document.getElementById('refreshCharts').addEventListener('click', function(){ fetchAndRender(); });

				// Auto-refresh (5 minutes)
				let autoRefreshIntervalId = null;
				const AUTO_REFRESH_MS = 5 * 60 * 1000;

				function updateLastRefreshLabel() {
					const el = document.getElementById('lastRefresh');
					if (!el) return;
					const now = new Date();
					const formatted = now.toLocaleString();
					el.textContent = 'Última actualización: ' + formatted;
				}

				function startAutoRefresh() {
					if (autoRefreshIntervalId) return;
					autoRefreshIntervalId = setInterval(function() {
						fetchAndRender().then(() => updateLastRefreshLabel());
					}, AUTO_REFRESH_MS);
				}

				function stopAutoRefresh() {
					if (!autoRefreshIntervalId) return;
					clearInterval(autoRefreshIntervalId);
					autoRefreshIntervalId = null;
				}

				// Toggle control
				document.getElementById('autoRefreshToggle').addEventListener('change', function(e){
					if (e.target.checked) {
						startAutoRefresh();
						updateLastRefreshLabel();
					} else {
						stopAutoRefresh();
					}
				});

				// If you want auto-refresh enabled by default, uncomment next line
				// document.getElementById('autoRefreshToggle').checked = true; startAutoRefresh();
			});
			</script>

			<script>
			// AJAX loader for report buttons - fetches the report page and extracts the main container
			document.addEventListener('DOMContentLoaded', function(){
				const buttons = document.querySelectorAll('.ajax-report-btn');
				if (!buttons.length) return;
				buttons.forEach(btn => {
					btn.addEventListener('click', function(e){
						e.preventDefault();
						const url = btn.getAttribute('href');
						const title = btn.getAttribute('data-report-title') || 'Reporte';
						const modalLabel = document.getElementById('reportModalLabel');
						const modalBody = document.getElementById('reportModalBody');

						if (modalLabel) modalLabel.textContent = title;
						if (modalBody) modalBody.innerHTML = '<div class="text-center py-4">Cargando...</div>';

						fetch(url, { credentials: 'same-origin' })
						.then(r => { if (!r.ok) throw new Error('Error cargando reporte'); return r.text(); })
						.then(html => {
							// Parse and extract the main content area
							const parser = new DOMParser();
							const doc = parser.parseFromString(html, 'text/html');
							let content = doc.querySelector('.main-content .container-fluid');
							if (!content) content = doc.querySelector('.container-fluid');
							if (content && modalBody) {
								modalBody.innerHTML = content.innerHTML;
							} else if (modalBody) {
								modalBody.innerHTML = '<div class="text-center text-danger py-4">No se pudo extraer el contenido del reporte.</div>';
							}
							// show modal (Bootstrap 4/jQuery)
							if (window.jQuery && typeof window.jQuery('#reportModal').modal === 'function') {
								window.jQuery('#reportModal').modal('show');
							} else {
								// fallback: open in new window
								window.open(url, '_blank');
							}
						})
						.catch(err => {
							if (modalBody) modalBody.innerHTML = '<div class="text-center text-danger py-4">Error cargando el reporte.</div>';
							console.error(err);
						});
					});
				});
			});
			</script>
		</div>
	</div>

	<!-- Modal para cargar reportes vía AJAX -->
	<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="reportModalLabel">Reporte</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body" id="reportModalBody">
	        <div class="text-center py-4">Cargando...</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Dashboard Statistics Script -->
	<script src="<?php echo base_url('public/js/dashboard-stats.js'); ?>"></script>

	<footer class=" footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>
</div>
