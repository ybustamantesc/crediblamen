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
            padding: 8px;
            height: 30px;
        }

        th {
            background: #D3D3D3;
            font-weight: bold;
        }

        td {
            background: #fff;
            text-align: center;
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
            <th colspan="10" class="titulo">
                <?php echo $titulo ?>
            </th>
        </tr>
        <tr>
            <th>Orden</th>
            <th>Cliente</th>
            <th>N°Crédito</th>
            <th>F.Crédito</th>
            <th>Monto Crédito</th>
            <th>T.Cuotas</th>
            <th>Interes</th>
            <th>Total Interes</th>
            <th>Total Pagar</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        $total_credito = 0;
        $total_interes = 0;
        $total_pagar = 0;
        ?>
        <?php
        // helper to safely get object properties without raising Notices
        if (!function_exists('_get_prop')) {
            function _get_prop($obj, $prop, $default = '')
            {
                if (is_object($obj) && (property_exists($obj, $prop) || isset($obj->$prop))) {
                    return $obj->$prop;
                }
                return $default;
            }
        }

        foreach ($prestamos as $prestamo) :
            $i++;
            $fp = _get_prop($prestamo, 'forma_pago', null);
            if ($fp === null) {
                $fp = _get_prop($prestamo, 'formaPago', null);
            }
            $forma_pago = '';
            if ($fp === 0 || $fp === '0') {
                $forma_pago = 'DIARIO';
            } elseif ($fp === 1 || $fp === '1') {
                $forma_pago = 'SEMANAL';
            } elseif ($fp === 2 || $fp === '2') {
                $forma_pago = 'QUINCENAL';
            } elseif ($fp === 3 || $fp === '3') {
                $forma_pago = 'MENSUAL';
            } elseif (!empty($fp)) {
                $forma_pago = (string)$fp;
            }

            $st = _get_prop($prestamo, 'estado', null);
            $estado = '';
            if ($st === 0 || $st === '0') {
                $estado = 'PAGADO';
            } elseif ($st === 1 || $st === '1') {
                $estado = 'POR COBRAR';
            } elseif (!empty($st)) {
                $estado = (string)$st;
            }

            $monto_credito_val = floatval(_get_prop($prestamo, 'monto_credito', 0));
            $total_interes_val = floatval(_get_prop($prestamo, 'total_interes', 0));
            $total_pagar_val = floatval(_get_prop($prestamo, 'total_pagar', 0));
            $total_credito += $monto_credito_val;
            $total_interes += $total_interes_val;
            $total_pagar += $total_pagar_val;

            $id_prestamo = _get_prop($prestamo, 'id', _get_prop($prestamo, 'ID', null));
            $pendientes = intval(_get_prop($prestamo, 'CuotasPendientes', _get_prop($prestamo, 'CuotasPendiente', 0)));
            $pagadas = intval(_get_prop($prestamo, 'CuotasPagadas', _get_prop($prestamo, 'CuotasPagada', 0)));

            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo _get_prop($prestamo, 'apellidos', '') . ', ' . _get_prop($prestamo, 'nombres', ''); ?></td>
                <td><?php echo _get_prop($prestamo, 'id', _get_prop($prestamo, 'idcredito', _get_prop($prestamo, 'idprestamo', _get_prop($prestamo, 'ID', _get_prop($prestamo, 'id_credito', _get_prop($prestamo, 'numero', _get_prop($prestamo, 'codigo', ''))))))); ?></td>
                <td><?php echo formatoFechaCorta(_get_prop($prestamo, 'fecha_credito', '')); ?></td>
                <td><?php echo number_format($monto_credito_val, 2); ?></td>
                <td><?php echo _get_prop($prestamo, 'numero_coutas', _get_prop($prestamo, 'numero_cuotases', '')); ?></td>
                <td><?php echo _get_prop($prestamo, 'interes_credito', '0') . '%'; ?></td>
                
                <td><?php echo number_format($total_interes_val, 2); ?></td>
                <td><?php echo number_format($total_pagar_val, 2); ?></td>
                <td><?php echo $estado; ?></td>
                
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4">TOTAL CRÉDITO</td>

            <td><?php echo number_format($total_credito, 2); ?></td>
            <td colspan="2">TOTAL INTERES</td>

            <td><?php echo number_format($total_interes, 2); ?></td>
            <td><?php echo number_format($total_pagar, 2); ?></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</main>
</body>

</html>
