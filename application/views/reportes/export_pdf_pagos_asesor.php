<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $titulo; ?></title>
	<style>
		@page {
			margin: 0cm 1cm 1cm;
		}
		body {
			margin-top: 3cm;
			margin-bottom: 2cm;
		}
		header {
			position: fixed;
			top: 0cm;
			height: 2.5cm;
			text-align: center;
		}
		footer {
			border-top: 1px solid black;
			position: fixed;
			bottom: 0cm;
			left: 0cm;
			right: 0cm;
			height: 0.7cm;
			text-align: center;
		}
		html {
			font-size: 10px/1;
			font-family: 'CyberCJK', sans-serif;
			overflow: auto;
		}
		table {
			color: #000;
			font-family: Arial, Arial, sans-serif;
			width: 100%;
			border-collapse: collapse;
		}

		td,
		th {
			border: 1px solid #666;
			padding: 5px;
			height: 13px;
		}

		th {
			background: #D3D3D3;
			font-weight: bold;
		}

		td {
			background: #fff;
			text-align: center;
		}
		.fondo{
			background-color: #D3D3D3;
		}
		.titulo {
			text-align: center;
			font-size: 12px;
		}
		.logo{
			width: 200px;
			height: 100px;
		}
		.logo img {
			width: 100%;
			height: 100%;
			padding: 4px;
		}
		.centrar{
			text-align:center;
		}
		main{
			margin-top: 10px;
		}
	</style>
</head>

<body>
	<header>
		<table style="border-collapse:collapse;border: hidden;">
			<tr>
				<td width="50px" style="border: hidden">
					<?php
					$imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
					$imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagen));
					?>
					<div class="logo">
						<img src="<?php echo $imagenBase64; ?>" width="50px">
					</div>
				</td>
				<td style="border: hidden">
					<div class="titulo">
						<h4><?php echo $empresa->razon_social; ?><br>
							<?php echo $empresa->telefonos; ?><br>
							<?php echo $empresa->email; ?><br>
							<?php echo $empresa->web; ?>
						</h4>
					</div>
				</td>
				<td style="border: hidden">
				</td>
			</tr>
		</table>
	</header>
	<footer>
		<p><?php echo $empresa->razon_social; ?><br><?php echo $empresa->direccion; ?><br><?php echo $empresa->web; ?></p>
	</footer>
<main>
		<table>
		<thead>
			<tr>
				<th colspan="6" class="titulo">
					<?php echo $titulo ?>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>Cliente</th>
				<th>Asesor</th>
				<th>Fecha Pago</th>
				<th>Monto Pago</th>
				<th>Descuento</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0;
			$total_pago= 0;
			$total_descuento = 0;
			// $total_pagar = 0; ?>
			<?php foreach ($prestamos as $prestamo) : ?>
				<?php
				$i++;
				$total_pago = $total_pago + $prestamo->monto_pago;
				$total_descuento= $total_descuento + $prestamo->descuento_pago;
				// $total_pagar = $total_pagar + $prestamo->total_pagar;
				// $forma_pago = '';

				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $prestamo->apellidos . ', ' . $prestamo->nombres; ?></td>
					<td><?php echo $prestamo->nombre_asesor; ?></td>
					<td><?php echo $prestamo->fecha_pago; ?></td>
					<td><?php echo number_format($prestamo->monto_pago, 2); ?></td>
					<td><?php echo number_format($prestamo->descuento_pago, 2); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="4">TOTAL PAGO</td>
				<td><?php echo number_format($total_pago, 2); ?> </td>
				<td> <?php echo number_format($total_descuento, 2); ?> </td>

			</tr>
		</tbody>
	</table>
</main>
</body>

</html>
