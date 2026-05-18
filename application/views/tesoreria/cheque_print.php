<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cheque #<?php echo htmlspecialchars((string)($mov->numero_cheque ?: $mov->id), ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 16px; color: #222; background: #fff; }
        .toolbar { max-width: 760px; margin: 0 auto 8px auto; text-align: right; }
        .btn { background: #1e88e5; color: #fff; border: 0; padding: 8px 14px; border-radius: 4px; cursor: pointer; }
        .cheque-wrap { width: 740px; margin: 0 auto; border: 1px solid #9b9b9b; background: #f4f4f4; }
        .cheque { margin: 10px; border: 2px solid #7f7f7f; padding: 6px 8px; min-height: 150px; background: #efefef; }
        .linea { border-bottom: 1px solid #6f6f6f; display: inline-block; min-height: 16px; vertical-align: bottom; }
        .fila { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; font-size: 11px; }
        .izq { width: 54%; }
        .der { width: 42%; text-align: left; }
        .banco { font-style: italic; font-weight: 700; font-size: 10px; }
        .campo-sm { width: 95px; }
        .campo-md { width: 220px; }
        .campo-lg { width: 480px; }
        .monto-box { display: inline-block; min-width: 70px; padding: 1px 6px; background: #84c341; color: #fff; font-weight: 700; text-align: right; }
        .firma-line { width: 120px; }
        .logo { text-align: right; font-weight: 700; color: #198754; font-size: 24px; line-height: 1; }
        .logo small { display: block; color: #198754; font-size: 12px; letter-spacing: 1px; }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
            .cheque-wrap { border: 0; }
        }
    </style>
</head>
<body>
    <?php
        if (!function_exists('cheque_numero_a_letras')) {
            function cheque_numero_a_letras($numero) {
                $numero = (int)$numero;

                $unidades = array('', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve');
                $especiales = array(
                    10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
                    16 => 'dieciseis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve',
                    20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidos', 23 => 'veintitres', 24 => 'veinticuatro',
                    25 => 'veinticinco', 26 => 'veintiseis', 27 => 'veintisiete', 28 => 'veintiocho', 29 => 'veintinueve'
                );
                $decenas = array('', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa');
                $centenas = array('', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos');

                $convertir_grupo = function ($n) use ($unidades, $especiales, $decenas, $centenas) {
                    $n = (int)$n;
                    if ($n === 0) {
                        return '';
                    }
                    if ($n === 100) {
                        return 'cien';
                    }

                    $texto = '';
                    $c = (int)floor($n / 100);
                    $resto = $n % 100;

                    if ($c > 0) {
                        $texto .= $centenas[$c];
                        if ($resto > 0) {
                            $texto .= ' ';
                        }
                    }

                    if ($resto > 0) {
                        if ($resto < 10) {
                            $texto .= $unidades[$resto];
                        } elseif ($resto < 30) {
                            $texto .= $especiales[$resto];
                        } else {
                            $d = (int)floor($resto / 10);
                            $u = $resto % 10;
                            $texto .= $decenas[$d];
                            if ($u > 0) {
                                $texto .= ' y ' . $unidades[$u];
                            }
                        }
                    }

                    return trim($texto);
                };

                if ($numero === 0) {
                    return 'cero';
                }

                $partes = array();
                $millones = (int)floor($numero / 1000000);
                $miles = (int)floor(($numero % 1000000) / 1000);
                $cientos = $numero % 1000;

                if ($millones > 0) {
                    if ($millones === 1) {
                        $partes[] = 'un millon';
                    } else {
                        $partes[] = $convertir_grupo($millones) . ' millones';
                    }
                }

                if ($miles > 0) {
                    if ($miles === 1) {
                        $partes[] = 'mil';
                    } else {
                        $partes[] = $convertir_grupo($miles) . ' mil';
                    }
                }

                if ($cientos > 0) {
                    $partes[] = $convertir_grupo($cientos);
                }

                return trim(implode(' ', $partes));
            }
        }

        if (!function_exists('cheque_monto_en_letras')) {
            function cheque_monto_en_letras($monto) {
                $monto = (float)$monto;
                $entero = (int)floor(abs($monto));
                $centavos = (int)round((abs($monto) - $entero) * 100);
                if ($centavos === 100) {
                    $entero += 1;
                    $centavos = 0;
                }

                $literal = cheque_numero_a_letras($entero);
                $moneda = ($entero === 1) ? 'dolar' : 'dolares';

                return strtoupper(trim($literal . ' ' . $moneda . ' con ' . str_pad((string)$centavos, 2, '0', STR_PAD_LEFT) . '/100'));
            }
        }

        $fecha = !empty($mov->fecha_aplicacion) ? $mov->fecha_aplicacion : $mov->fecha_registro;
        $monto = number_format((float)$mov->monto_total, 2, '.', ',');
        $beneficiario = trim((string)($mov->beneficiario ?: '-'));
        $numCheque = trim((string)($mov->numero_cheque ?: $mov->id));
        $cantidadLetra = cheque_monto_en_letras($mov->monto_total);
        $lugar = 'Managua';
        $preview = isset($_GET['preview']) && $_GET['preview'] == '1';
    ?>

    <div class="toolbar">
        <button class="btn" onclick="window.print()">Imprimir</button>
    </div>

    <div class="cheque-wrap">
        <div class="cheque">
            <div class="fila">
                <div class="izq banco">BANCO LAFISE</div>
                <div class="der">
                    <div>Lugar: <span class="linea campo-sm"><?php echo htmlspecialchars($lugar, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <div>Fecha: <span class="linea campo-sm"><?php echo htmlspecialchars((string)$fecha, ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
            </div>

            <div class="fila">
                <div class="izq">Cheque #: <span class="linea campo-sm"><?php echo htmlspecialchars($numCheque, ENT_QUOTES, 'UTF-8'); ?></span></div>
                <div class="der"></div>
            </div>

            <div class="fila" style="margin-top:8px;">
                <div class="izq">Páguese a: <span class="linea campo-md"><?php echo htmlspecialchars($beneficiario, ENT_QUOTES, 'UTF-8'); ?></span></div>
                <div class="der" style="text-align:right;">$ <span class="monto-box"><?php echo htmlspecialchars($monto, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>

            <div class="fila" style="margin-top:8px;">
                <div class="izq" style="width:100%;">Cantidad en letras <span class="linea campo-lg"><?php echo htmlspecialchars($cantidadLetra, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>

            <div class="fila" style="margin-top:14px;">
                <div class="izq">FIRMA: <span class="linea firma-line"></span></div>
                <div class="der logo">
                    Banco
                    <small>LAFISE</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($preview): ?>
    <script>
        document.querySelector('.toolbar').style.display = 'none';
    </script>
    <?php endif; ?>
</body>
</html>
