<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <!-- <link rel="stylesheet" href="public/plugins/bootstrap/dist/css/bootstrap.min.css"> -->
    <style>
        *{
            font-size:11px !important;
        }
        
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
    <?php
    $forma_pago = '';
    $total_capital = 0;
    $total_interes = 0;
    $total_pagado = 0;
    $total_pendiente = 0;
    if ($prestamo->forma_pago == 0) {
        $forma_pago = 'DIARIO';
    } elseif ($prestamo->forma_pago == 1) {
        $forma_pago = 'SEMANAL';
    } elseif ($prestamo->forma_pago == 2) {
        $forma_pago = 'QUINCENAL';
    } elseif ($prestamo->forma_pago == 3) {
        $forma_pago = 'MENSUAL';
    }
    ?>
    <table>
        <tbody>
        <tr>
            <th colspan="6" style="text-align:center;">PLAN DE PAGO</th>
        </tr>
        <tr>
            <td>CRÉDITO N°</td>
            <td><?php echo $prestamo->id ?></td>
            <td>FECHA CRÉDITO</td>
            <td colspan="3"><?php echo formatoFechaCorta($prestamo->fecha_credito) ?></td>
        </tr>
        <tr>
            <td>CLIENTE:</td>
            <td colspan="2"><?php echo $prestamo->idcliente . ' - ' . $prestamo->apellidos . ', ' . $prestamo->nombres ?></td>
            <td>TELEFONO</td>
            <td colspan="2"><?php echo $prestamo->telefonoC ?></td>
        </tr>
        <tr>
            <td>ASESOR:</td>
            <td colspan="2"><?php echo $prestamo->nombre_asesor ?></td>
            <td>TELEFONO</td>
            <td colspan="2"><?php echo $prestamo->telefonoA ?></td>
        </tr>
        <tr>
            <td>MONTO CRÉDITO:</td>
            <td colspan="5"><?php echo number_format($prestamo->monto_credito, 2) ?></td>
        </tr>
        <tr>
            <td>FORMA DE PAGO:</td>
            <td colspan="5"><?php echo $forma_pago ?></td>
        </tr>
        <tr>
            <td>No. De cuenta Banrural para depositar:</td>
            <td colspan="5">3002578456</td>
        </tr>

        <tr>
            <th colspan="6" style="text-align:center; color:darkgreen;">CRONOGRAMA DE CUOTAS</th>
        </tr>
        
<tr>
    <th>Cuota</th>
    <th>Fecha Pago</th>
    <th>Estado</th>
    <th>Monto Cuota</th>
    <th>Monto Pagado</th>
    <th>Monto Pendiente</th>
</tr>

<?php 
$total_monto_pagado = 0; // Total de Monto Pagado
$total_monto_pendiente = 0; // Total de Monto Pendiente
$exceso_pago = 0; // Exceso de pago acumulado

// Calculamos el total de monto cuota basado en la primera cuota y la cantidad de cuotas
$primer_monto_cuota = isset($cuotas[0]) ? $cuotas[0]->monto_couta : 0;
$total_monto_cuota = $primer_monto_cuota * count($cuotas); // Cálculo basado en la cantidad de cuotas

foreach ($cuotas as $index => $couta): 
    $numero_couta = $couta->numero_couta;
    $fecha_couta = $couta->fecha_couta;
    $fecha_pago = $couta->fecha_pago;
    $monto_pagado = $couta->monto_pagado;
    $monto_couta = $primer_monto_cuota; // Aquí se asegura que se use el monto de la primera cuota siempre
    $monto_pendiente = $couta->monto_pendiente;
    $estado_couta = $couta->estado_couta;
    $fechaAtual = strtotime(date('Y-m-d'));
    $fechaVencimiento = strtotime($fecha_couta);

    // Acumulando los totales
    $total_monto_pagado += $monto_pagado;

    // Si el monto pagado excede el monto de la cuota
    if ($monto_pagado > $monto_couta) {
        // Calculamos el exceso de pago
        $exceso_pago = $monto_pagado - $monto_couta;

        // Monto pendiente se vuelve 0 si se pagó más que la cuota
        $monto_pendiente = 0;

        // Cambiamos el estado a "PAGADO" cuando hay exceso de pago
        $estado = "PAGADO";
    } else {
        // Si el pago no excede la cuota, calculamos el monto pendiente normal
        $monto_pendiente = $monto_couta - $monto_pagado;

        // Determinamos el estado de la cuota basado en la fecha
        if ($monto_pagado > 0) {
            $estado = "PAGADO";  // Si se ha realizado algún pago, el estado será "PAGADO"
        } elseif ($fechaAtual == $fechaVencimiento) {
            $estado = "PAGA HOY";
        } elseif ($fechaAtual > $fechaVencimiento) {
            $estado = "VENCIDO";
        } elseif ($fechaAtual < $fechaVencimiento) {
            $estado = "POR COBRAR";
        }
    }

    // Si no es la última cuota, deducimos el exceso de pago de la siguiente cuota
    if ($index + 1 < count($cuotas) && $exceso_pago > 0) {
        // Restamos el exceso de pago del monto de la siguiente cuota
        $cuotas[$index + 1]->monto_couta -= $exceso_pago;
        $exceso_pago = 0; // Reiniciamos el exceso para la siguiente iteración
    }

    // Acumulamos el monto pendiente
    $total_monto_pendiente += $monto_pendiente;

    // Estilo para el monto cuota si no se ha pagado nada
    $monto_cuota_color = ($monto_pagado == 0) ? 'style="color:darkred; font-weight:bold;"' : '';
    
    // Texto "Sin Pago" si no se ha pagado nada
    $texto_pago = ($monto_pagado == 0) ? "(Sin Pago)" : "";
?>
<tr>
    <td><?php echo $numero_couta ?></td>
    <td><?php echo formatoFechaCorta($fecha_couta); ?></td>
    <td><?php echo $estado ?></td>
    <td <?php echo $monto_cuota_color; ?>><strong><?php echo $texto_pago; ?></strong> <?php echo number_format($monto_couta, 2); ?></td>
    <td style="color:darkgreen;"><?php echo number_format($monto_pagado, 2) ?></td>
    <td><?php echo number_format($monto_pendiente, 2) ?></td>
</tr>
<?php endforeach; ?>

<tr>
    <td colspan="3"><strong>TOTAL</strong></td>
    <td style="color:#767500;"><strong><?php echo number_format($total_monto_cuota, 2) ?></strong></td>
    <td style="color:darkgreen;"><strong><?php echo number_format($total_monto_pagado, 2) ?></strong></td>
    <td style="color:darkred;"><strong><?php echo number_format($total_monto_cuota - $total_monto_pagado, 2) ?></strong></td>
</tr>

        </tbody>
    </table>
    <h5 style="font-size:9px !important">
        (*) ES OBLIGATORIO QUE REALICE SU PAGO ANTES DE LA 05:00PM PARA OBTENER MÁS BENEFICIOS<br>
        <?php echo $empresa->mensaje_ticket ?>
    </h5>
</main>
</body>

</html>
