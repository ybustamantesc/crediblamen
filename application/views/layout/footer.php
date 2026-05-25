</div>
<div class="modal fade apps-modal" id="appsModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="ik ik-x-circle"></i></button>
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="quick-search">
				<div class="container">
					<div class="row">
						<div class="col-md-4 ml-auto mr-auto">
							<div class="input-wrap">
								<input type="text" id="quick-search" class="form-control" placeholder="Buscar..." />
								<i class="ik ik-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body d-flex align-items-center">
				<div class="container">
					<div class="apps-wrap">
						<div class="app-item">
							<a href="<?php echo base_url('/') ?>"><i class="ik ik-home"></i><span>Inicio</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('estacionar') ?>"><i class="fas fa-parking"></i><span>Estacionar</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('clientes') ?>"><i class="ik ik-users"></i><span>Clientes</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('matriculas') ?>"><i class="ik ik-shopping-cart"></i><span>Matrículas</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('usuarios') ?>"><i class="ik ik-briefcase"></i><span>Usuarios</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('sistema') ?>"><i class="ik ik-server"></i><span>Sistema</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('formas') ?>"><i class="ik ik-clipboard"></i><span>Formas de Pago</span></a>
						</div>
						<div class="app-item">
							<a href="<?php echo base_url('precios') ?>"><i class="ik ik-message-square"></i><span>Precios</span></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	base_url = '<?= base_url(); ?>'
</script>
<style>
	/* Ensure page layout keeps footer at bottom and allows dropdowns to overflow */
	html, body { height: 100%; }
	.page-wrap { display: flex; flex-direction: column; min-height: 100vh; }
	.main-content { flex: 1 0 auto; }
	footer.footer { flex-shrink: 0; margin-top: 12px; z-index: 1000; position: relative; }
	/* Ensure dropdowns show above footer and other elements */
	.dropdown-menu { z-index: 10550 !important; }
</style>
<script src="<?php echo base_url('public/src/js/vendor/modernizr-2.8.3.min.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/popper.js/dist/umd/popper.min.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/screenfull/dist/screenfull.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/moment/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'); ?>"></script>

<!-- Select2 for searchable dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="<?php echo base_url('public/dist/js/theme.min.js'); ?>"></script>

<!-- <link rel="stylesheet" href="<?php echo base_url('public/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css'); ?>"> -->
<!-- <script src="<?php echo base_url('public/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js'); ?>"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.2.0/jquery.bootstrap-touchspin.min.js" integrity="sha512-VzUh7hLMvCqgvfBmkd2OINf5/pHDbWGqxS+RFaL/fsgA+rT94LxTFnjlFkm0oKM5BXWbc9EjBQAuARqzGKLbcA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.2.0/jquery.bootstrap-touchspin.css" integrity="sha512-M+RT/z+GO2INvbXyfkn7l5qN+g09mr0+JQ++nxLUfqAufrp/v5GIQ1k4IMn0BIHgxZK2Ss+YA+kHK4wJUKJK0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php if (isset($scripts)) : ?>
	<?php foreach ($scripts as $script) :
		$script_file = FCPATH . 'public/' . $script;
		$ver = (file_exists($script_file)) ? filemtime($script_file) : time();
	?>
		<script src="<?php echo base_url('public/' . $script) . '?v=' . $ver; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>
<script>
	$("#logotipo").change(function () {
		readImage(this);
	});
	function readImage (input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#imgPreview').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
</script>

<script>
	// Only force navigation for server-side pagination links on small screens.
	// This avoids interfering with client-side DataTables pagination.
	document.addEventListener('click', function(ev){
		try{
			if (!ev || !ev.target) return;
			// Only on small viewports (mobile)
			if (window.innerWidth > 767) return;
			var a = ev.target.closest && ev.target.closest('a');
			if (!a) return;
			var pag = a.closest && a.closest('.pagination');
			if (!pag) return;
			var href = a.getAttribute('href') || '';
			// Only act on links that explicitly include a page query (server-side pagination)
			if (href.indexOf('page=') === -1) return;
			if (href.indexOf('javascript:') === 0) return;
			// allow modifier keys and middle-click to keep native behavior
			if (ev.ctrlKey || ev.metaKey || ev.shiftKey || ev.button === 1) return;
			ev.preventDefault(); ev.stopImmediatePropagation();
			// Normalize relative query like '?page=2' to full path
			var url = href;
			if (href.charAt(0) === '?') {
				url = window.location.pathname + href;
			} else if (!/^https?:\/\//i.test(href) && href.charAt(0) !== '/') {
				// relative path without leading slash
				url = window.location.pathname.replace(/\/$/, '') + '/' + href;
			}
			window.location.href = url;
		}catch(e){ /* swallow errors */ }
	}, true);
</script>

</body>

</html>
