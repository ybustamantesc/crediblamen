<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $titulo; ?></title>
	<!-- <link rel="stylesheet" href="public/plugins/bootstrap/dist/css/bootstrap.min.css"> -->
	<style>
		body {
			margin: 3px;
		}

		html {
			font-size: 12px/1;
			font-family: 'CyberCJK', sans-serif;
			overflow: auto;
		}

		table {
			color: #333;
			font-family: Arial, Arial, sans-serif;
			width: 100%;
			border-collapse: collapse;
		}

		td,
		th {
			border: 1px solid #666;
			padding: 8px;
			height: 30px;
		}

		th {
			background: #D3D3D3;
			font-weight: bold;
		}

		td {
			background: #FAFAFA;
			text-align: center;
		}



		.titulo {
			text-align: center;
			font-size: 12px;
		}
	</style>
</head>

<body>
	<table>
		<thead>
			<tr>
				<th colspan="11" class="titulo">
					<?php echo $titulo; ?>
				</th>
			</tr>
			<tr>
				<th>Orden</th>
				<th>Cliente</th>
				<th>Asesor</th>
				<th>F.Crédito</th>
				<th>Monto Crédito</th>
				<th>Interes</th>
				<th>Cuotas</th>
				<th>Total Interes</th>
				<th>Total Pagar</th>
				<th>Forma de Pago</th>
				<th>Estado</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($prestamos as $prestamo) : ?>
				<?php
				$forma_pago = '';
				if ($prestamo->forma_pago == 0) {
					$forma_pago = 'DIARIO';
				} elseif ($prestamo->forma_pago == 1) {
					$forma_pago = 'SEMANAL';
				} elseif ($prestamo->forma_pago == 2) {
					$forma_pago = 'QUINCENAL';
				} elseif ($prestamo->forma_pago == 3) {
					$forma_pago = 'MENSUAL';
				}
				$estado = '';
				if ($prestamo->estado == 0) {
					$estado = 'CANCELADO';
				} elseif ($prestamo->estado == 1) {
					$estado = 'PENDIENTE';
				} elseif ($prestamo->estado == 2) {
					$estado = 'PAGANDO';
				}

				?>
				<tr>
					<td><?php echo $prestamo->id; ?></td>
					<td><?php echo $prestamo->apellidos . ', ' . $prestamo->nombres; ?></td>
					<td><?php echo $prestamo->nombre_asesor; ?></td>
					<td><?php echo $prestamo->fecha_credito; ?></td>
					<td><?php echo $prestamo->monto_credito; ?></td>
					<td><?php echo $prestamo->interes_credito . '%'; ?></td>
					<td><?php echo $prestamo->numero_coutas; ?></td>
					<td><?php echo $prestamo->total_interes; ?></td>
					<td><?php echo $prestamo->total_pagar; ?></td>
					<td><?php echo $estado; ?></td>
					<td><?php echo $forma_pago; ?></td>
				</tr>
			<?php endforeach; ?>


		</tbody>
	</table>
</body>

</html>
