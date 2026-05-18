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

        .fondo {
            background-color: #D3D3D3;
        }

        .titulo {
            text-align: center;
            font-size: 12px;
        }

        .logo {
            width: 200px;
            height: 100px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            padding: 4px;
        }

        .centrar {
            text-align: center;
        }

        main {
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
            <th colspan="14" class="titulo">
                <?php echo $titulo ?>
            </th>
        </tr>

        <tr>
            <th class="nosort">#</th>
            <th>Crédito</th>
            <th>Cliente</th>
            <th>Telf.Cliente</th>
            <th>Asesor</th>
            <th>Fecha Cuota</th>
            <th>N° Cuota</th>
            <th>Cuotas Pagadas</th>
            <th>Cuotas Pendientes</th>
            <th>Monto Cuota</th>
            <th>Monto Pendiente</th>
            <th>Fecha Pago</th>
            <th>Monto Pago</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 0;
        $total_pago = 0;
        $total_pendiente = 0;
        $total_monto_cuota = 0;
        ?>

        <?php foreach ($prestamos as $prestamo) : ?>
            <?php
            $i++;
            $total_pago = $total_pago + $prestamo->monto_pagado;
            $total_pendiente = $total_pendiente + $prestamo->monto_pendiente;
            $total_monto_cuota = $total_monto_cuota + $prestamo->monto_couta;
            $fecha_cuota = $prestamo->fecha_couta;
            $estado_cuota = $prestamo->estado_couta;
            if ($prestamo->monto_pendiente == 0) {
                $pendientes = 0;
            } else {
                $cuotasPendientes = $this->prestamos_model->getContarCuotasPendientes($prestamo->idcredito);
                $pendientes = $cuotasPendientes->CuotasPendientes;
            }
            $cuotasPagadas = $this->prestamos_model->getContarCuotasPagadas($prestamo->idcredito);
            $pagadas = $cuotasPagadas->CuotasPagadas;

            $fechaAtual = strtotime(date('Y-m-d'));
            $fechaVencimiento = strtotime($fecha_cuota);
            $estado = '';
            if ($estado_cuota == 1) {
                if ($fechaAtual == $fechaVencimiento) {
                    $estado = '<span class="badge  badge-primary mb-1"><i class="fas fa-info-circle"></i> PAGA HOY</span>';
                }
                if ($fechaAtual > $fechaVencimiento) {
                    $estado = '<span class="badge  badge-danger mb-1"><i class="fas fa-exclamation-triangle"></i> VENCIDO</span>';
                }
                if ($fechaAtual < $fechaVencimiento) {
                    $estado = '<span class="badge  badge-info mb-1"><i class="fas fa-info-circle"></i> POR COBRAR</span>';
                }
            } else {
                $estado = '<span class="badge  badge-success mb-1"><i class="fas fa-check"></i> PAGADO</span>';
            }

            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $prestamo->id; ?></td>
                <td><?php echo $prestamo->apellidos . ', ' . $prestamo->nombres; ?></td>
                <td><?php echo $prestamo->telefonoC; ?></td>
                <td><?php echo $prestamo->nombre_asesor; ?></td>
                <td><?php echo $prestamo->fecha_couta; ?></td>
                <td><?php echo $prestamo->numero_couta; ?></td>
                <td><?php echo $pagadas; ?></td>
                <td><?php echo $pendientes; ?></td>
                <td><?php echo $prestamo->monto_couta; ?></td>
                <td><?php echo $prestamo->monto_pendiente; ?></td>
                <td><?php echo $prestamo->fecha_pago == "" ? "" : formatoFechaCorta($prestamo->fecha_pago); ?></td>
                <td><?php echo $prestamo->monto_pagado; ?></td>
                <td><?php echo $estado; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="6">TOTAL PAGO</td>

            <td></td>
            <td></td>
            <td></td>
            <td><?php echo number_format($total_monto_cuota, 2); ?></td>
            <td><?php echo number_format($total_pendiente, 2); ?></td>
            <td></td>
            <td><?php echo number_format($total_pago, 2); ?></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</main>
</body>

</html>
