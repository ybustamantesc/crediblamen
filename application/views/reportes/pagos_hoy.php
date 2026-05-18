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

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body text-center py-5">
							<h2 class="upcoming-title">Próximamente Disponible</h2>
							<p class="upcoming-sub">Carga de Datos en Proceso</p>
							<div class="progress upcoming-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
								<div class="progress-bar" style="width:0%"></div>
							</div>
							<div class="small text-muted mt-2">Este contenido se actualizará automáticamente cuando finalice la carga.</div>
						</div>
					</div>
				</div>
			</div>

			<script>
			// Animate the progress bar to simulate loading
			(function(){
				const bar = document.querySelector('.upcoming-progress .progress-bar');
				if (!bar) return;
				let value = 0;
				const target = 88; // target percent to simulate
				const step = function(){
					value += Math.random() * 6; // random increment
					if (value >= target) value = target;
					bar.style.width = Math.round(value) + '%';
					if (value < target) setTimeout(step, 450 + Math.random()*400);
					else {
						// small pulsing to indicate ongoing process
						bar.classList.add('pulse');
					}
				};
				setTimeout(step, 400);
			})();
			</script>

		</div>
	</div>
	<footer class=" footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>
</div>
