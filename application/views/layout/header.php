<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Sistema | <?php echo (isset($titulo) ? $titulo : 'AVIELPREST - SISTEMA CONTROL CREDITOS') ?> </title>
	<meta name="description" content="Sistema Administrac��n de Prestamos o Cr��ditos">
	<meta name="keywords" content="Prestamos, Creditos, Cuotas diarias, semanales, quincenales y mensuales, sistema cooperativas">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('public/src/img/favicon.png');?>">
	<link rel="shortcut icon" href="<?php echo base_url('public/src/img/favicon.png');?>">

	<link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo base_url('public/plugins/bootstrap/dist/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('public/plugins/fontawesome-free/css/all.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('public/plugins/icon-kit/dist/css/iconkit.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('public/plugins/ionicons/dist/css/ionicons.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('public/plugins/perfect-scrollbar/css/perfect-scrollbar.css'); ?>">

	<link rel="stylesheet" href="<?php echo base_url('public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">
	
	<!-- Select2 for searchable dropdowns -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.20/dist/sweetalert2.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.20/dist/sweetalert2.all.min.js"></script>
	<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css"> -->
		<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet">
		<?php if (isset($styles)) : ?>
			<?php foreach ($styles as $style) : ?>
				<link rel="stylesheet" href="<?php echo base_url('public/' . $style); ?>">
			<?php endforeach; ?>
		<?php endif; ?>

		<link rel="stylesheet" href="<?php echo base_url('public/dist/css/theme.min.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('public/css/branding.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('public/css/dashboard-stats.css'); ?>">

		<!-- Chart.js (global) -->
		<!-- Load jQuery early so page scripts can use it (local fallback used in footer too) -->
		<script src="<?php echo base_url('public/src/js/vendor/jquery-3.3.1.min.js'); ?>"></script>
		<script>
		if (typeof jQuery === 'undefined') {
			// If for some reason local file didn't load, try CDN as fallback
			document.write('\x3Cscript src="https://code.jquery.com/jquery-3.6.0.min.js">\x3C/script>');
		}
		</script>
		<!-- Chart.js (global) -->
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	</head>

	<body>
		<div class="wrapper">
