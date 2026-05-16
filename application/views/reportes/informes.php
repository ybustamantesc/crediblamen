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
							<div class="small text-muted mt-2">Módulo de informes en construcción. Pronto disponible.</div>
						</div>
					</div>
				</div>
			</div>

			<script>
			(function(){const bar=document.querySelector('.upcoming-progress .progress-bar'); if(!bar) return; let v=0,target=90; const step=function(){ v+=Math.random()*6; if(v>=target)v=target; bar.style.width=Math.round(v)+'%'; if(v<target) setTimeout(step,380+Math.random()*300); else bar.classList.add('pulse'); }; setTimeout(step,300);})();
			</script>

		</div>
	</div>
	<footer class=" footer">
		<div class="w-100 clearfix">
			<span class="text-center text-sm-left d-md:inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
			<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
		</div>
	</footer>
</div>
