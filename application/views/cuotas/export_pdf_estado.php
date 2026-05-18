<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $titulo; ?></title>

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
			text-align: center;
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
				<th colspan="9" class="titulo">
					<?php echo $titulo; ?>
				</th>
			</tr>
			<tr>
				<th>Cliente</th>
				<th>Asesor</th>
				<th>N° Cuota</th>
				<th>Fecha Cuota</th>
				<th>Fecha Pago</th>
				<th>Monto Pagado</th>
				<th>Monto Cuota</th>
				<th>Monto Pendiente</th>
				<th>Estado</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($estadoscuotas as $cuota) : ?>
				<?php
				$fechaAtual = strtotime(date('Y-m-d'));
				$fechaVencimiento = strtotime($cuota->fecha_couta);
				$estado = '';

				if ($cuota->estado_couta == 1 or $cuota->estado_couta == 2) {
					if ($fechaAtual == $fechaVencimiento) {
						$estado = '<span class="badge  badge-primary mb-1"><i class="fas fa-calendar-check"></i> PAGA HOY</span>';
					}
					if ($fechaAtual > $fechaVencimiento) {
						$estado = '<span class="badge  badge-danger mb-1"><i class="fas fa-exclamation-triangle"></i> VENCIÓ</span>';
					}
					if ($fechaAtual < $fechaVencimiento) {
						$estado = '<span class="badge  badge-warning mb-1"><i class="fas fa-sync-alt"></i> PENDIENTE</span>';
					}
				} else {
					$estado = '<span class="badge  badge-success mb-1"><i class="fas fa-check-circle"></i> CANCELADO</span>';
				}

				?>
				<tr>
					<td><?php echo $cuota->apellidos . ', ' . $cuota->nombres; ?></td>
					<td><?php echo $cuota->nombre_asesor; ?></td>
					<td><?php echo $cuota->numero_couta; ?></td>
					<td><?php echo $cuota->fecha_couta; ?></td>
					<td><?php echo $cuota->fecha_pago; ?></td>
					<td><?php echo $cuota->monto_pagado; ?></td>
					<td><?php echo $cuota->monto_couta; ?></td>
					<td><?php echo $cuota->monto_pendiente; ?></td>
					<td><?php echo $estado; ?></td>
				</tr>
			<?php endforeach; ?>


		</tbody>
	</table>
</body>

</html>
