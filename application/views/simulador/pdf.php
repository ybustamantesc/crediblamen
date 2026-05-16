<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $titulo; ?></title>
	<!-- <link rel="stylesheet" href="public/plugins/bootstrap/dist/css/bootstrap.min.css"> -->
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
		<?php
		$forma_pago = '';
		if ($simulacion->forma_pago == 0) {
			$forma_pago = 'DIARIO';
		} elseif ($simulacion->forma_pago == 1) {
			$forma_pago = 'SEMANAL';
		} elseif ($simulacion->forma_pago == 2) {
			$forma_pago = 'QUINCENAL';
		} elseif ($simulacion->forma_pago == 3) {
			$forma_pago = 'MENSUAL';
		}
		?>
		<table>
			<tbody>
				<tr>
					<td colspan="5">SIMULADOR DE CRÉDITO</td>
				</tr>
				<tr>
					<td>SIMULACIÓN N°</td>
					<td><?php echo $simulacion->idsimulacion ?></td>
					<td>FECHA SIMULACIÓN</td>
					<td colspan="2"><?php echo formatoFechaHora($simulacion->fecha_simulacion) ?></td>
				</tr>
				<tr>
					<td>CLIENTE: </td>
					<td colspan="4"><?php echo $simulacion->idcliente . ' - ' . $simulacion->apellidos . ', ' . $simulacion->nombres ?></td>
				</tr>
				<tr>
					<td>ASESOR: </td>
					<td colspan="4"><?php echo $simulacion->nombre_asesor ?></td>
				</tr>
				<tr>
					<td>MONTO CRÉDITO: </td>
					<td><?php echo number_format($simulacion->monto_credito, 2) ?></td>
					<td>INTERES</td>
					<td colspan="2"><?php echo number_format($simulacion->total_interes, 2) ?></td>
				</tr>
				<tr>
					<td>FORMA DE PAGO: </td>
					<td colspan="4"><?php echo $forma_pago ?></td>
				</tr>
				<tr>
					<td colspan="5">DETALLE DE CUOTAS</td>
				</tr>
				<tr>
					<th>N° Cuota</th>
					<th>Fecha Cuota</th>
					<th>Capital</th>
					<th>Interes</th>
					<th>Monto Cuota</th>
				</tr>
				<?php $total_capital=0;$total_interes=0;?>
				<?php foreach ($cuotas as $cuota): ?>
					<?php
					$total_capital=$total_capital+$cuota->monto_capital;
					$total_interes=$total_interes+$cuota->monto_interes;
					$numero_cuota = $cuota->numero_cuota;
					$fecha_cuota = $cuota->fecha_cuota;
					$monto_capital = $cuota->monto_capital;
					$monto_interes = $cuota->monto_interes;
					$monto_cuota = $cuota->monto_cuota;
					?>
					<tr>
						<td><?php echo $numero_cuota ?></td>
						<td><?php echo $fecha_cuota ?></td>
						<td><?php echo number_format($monto_capital,2) ?></td>
						<td><?php echo number_format($monto_interes,2) ?></td>
						<td><?php echo number_format($monto_cuota,2) ?></td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td></td>
					<td></td>
					<td><?php echo number_format($total_capital, 2) ?></td>
					<td><?php echo number_format($total_interes, 2) ?></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</main>
</body>

</html>
