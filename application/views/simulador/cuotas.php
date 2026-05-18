<table class="table table-hover pr-20 pl-20">
	<thead>
		<th>N° Cuota</th>
		<th>Fecha</th>
		<th>Monto Cuota</th>
		<th>Capital</th>
		<th>Interes</th>
		<th>Total</th>
	</thead>
	<?php var_dump($cuotas); ?>
	<?php foreach ($cuotas as $cuota) : ?>
		<tr>
			<td><?php echo $cuota->numero_cuota; ?></td>
			<td><?php echo $cuota->fecha_cuota; ?></td>
			<td><?php echo $cuota->monto_cuota; ?></td>
			<td><?php echo $cuota->monto_cuota; ?></td>
			<td><?php echo $cuota->monto_cuota; ?></td>
			<td><?php echo $cuota->monto_cuota; ?></td>
		</tr>
	<?php endforeach; ?>
</table>
