<?php $this->load->view('layout/navbar'); ?> <div class="page-wrap"> <?php $this->load->view('layout/sidebar.php'); ?> <div class="main-content">
		<div class="container-fluid">
			<div class="page-header">
				<div class="row align-items-end">
					<div class="col-lg-8">
						<div class="page-header-title">
							<i class="<?php echo $icono_view; ?> bg-blue"></i>
							<div class="d-inline">
								<h5> <?php echo $titulo; ?> </h5>
								<span><?php echo $subtitulo; ?></span>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<nav class="breadcrumb-container" aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item">
									<a href="<?php echo base_url('/'); ?>" data-toggle="tooltip" data-placement="top" title="Inicio"><i class="ik ik-home"></i></a>
								</li>
								<li class="breadcrumb-item">
									<a href="<?php echo $this->router->fetch_class(); ?>" data-toggle="tooltip" data-placement="top" title="Listar <?php echo $this->router->fetch_class(); ?>">Listar <?php echo $this->router->fetch_class(); ?></a>
								</li>
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
			<div class="row">
				<!-- imprestion, goal, impect start -->
				<div class="col-xl-4 col-md-12">
					<div class="card comp-card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="mb-25">IMPRESIÓN DE CONTRATO</h6>
									<a target="_blank" class="btn bg-blue text-white" href="<?php echo base_url($this->router->fetch_class() . '/pdf/' . $credito->id); ?>">Imprimir</a>
								</div>
								<div class="col-auto">
									<i class="fas fa-print bg-blue"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-md-6">
					<div class="card comp-card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="mb-25">LISTAR CRÉDITOS</h6>
									<a class="btn bg-green text-white" href="<?php echo base_url($this->router->fetch_class()); ?>">Listar</a>
								</div>
								<div class="col-auto">
									<i class="fas fa-list-ol bg-green"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-md-6">
					<div class="card comp-card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col">
									<h6 class="mb-25">NUEVO CRÉDITO</h6>
									<a class="btn bg-yellow text-white" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>">Nuevo</a>
								</div>
								<div class="col-auto">
									<i class="fas fa-plus bg-yellow"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- imprestion, goal, impect end -->
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
