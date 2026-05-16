<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <style>
        body {
            margin: 0px;
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
            padding: 5px;
            height: 20px;
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
        <th colspan="7" class="titulo">
            <?php echo $titulo; ?>
        </th>
    </tr>
    <tr>
        <th colspan="7">
            INGRESOS
        </th>
    </tr>
    <tr>
        <th>ITEM</th>
        <th>FECHA</th>
        <th>MOTIVO</th>
        <th>MONTO</th>
        <th>FORMA DE PAGO</th>
        <th>TIPO DOC</th>
        <th>NRO DOC</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0;
    $total_ingresos = 0;
    ?>
    <?php foreach ($ingresos as $reg) : ?>
        <?php
        $i++;
        $total_ingresos = $total_ingresos + $reg->monto_movimiento;
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo formatoFechaHora($reg->fecha_movimiento); ?></td>
            <td><?php echo $reg->descripcion_movimiento; ?></td>
            <td><?php echo number_format($reg->monto_movimiento, 2); ?></td>
            <td><?php echo $reg->forma_pago; ?></td>
            <td><?php echo $reg->tipo_doc; ?></td>
            <td><?php echo $reg->numero_doc; ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3">TOTAL INGRESOS</td>
        <td><?php echo number_format($total_ingresos, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th colspan="7">
            GASTOS
        </th>
    </tr>
    <tr>
        <th>ITEM</th>
        <th>FECHA</th>
        <th>MOTIVO</th>
        <th>MONTO</th>
        <th>FORMA DE PAGO</th>
        <th>TIPO DOC</th>
        <th>NRO DOC</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0;
    $total_gastos = 0;
    //$monto_apertura = 0;
    ?>
    <?php foreach ($gastos as $reg) : ?>
        <?php
        $i++;
        $total_gastos = $total_gastos + $reg->monto_movimiento;
        //$monto_apertura = $reg->monto_apertura;
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo formatoFechaHora($reg->fecha_movimiento); ?></td>
            <td><?php echo $reg->descripcion_movimiento; ?></td>
            <td><?php echo number_format($reg->monto_movimiento, 2); ?></td>
            <td><?php echo $reg->forma_pago; ?></td>
            <td><?php echo $reg->tipo_doc; ?></td>
            <td><?php echo $reg->numero_doc; ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3">TOTAL GASTOS</td>
        <td><?php echo number_format($total_gastos, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="7">RESUMEN</td>
    </tr>
    <tr>
        <td colspan="3">TOTAL INGRESOS</td>
        <td><?php echo number_format($total_ingresos, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3">TOTAL GASTOS</td>
        <td><?php echo number_format($total_gastos, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td colspan="3">MONTO DE APERTURA</td>
        <td><?php echo number_format($caja->monto_apertura, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3">
            <b>
                <h3>SALDO EFECTIVO+MONTO DE APERTURA</h3>
            </b>
        </td>
        <td><?php echo number_format($total_ingresos - $total_gastos + $caja->monto_apertura, 2); ?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>
</body>

</html>
