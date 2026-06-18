<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibo de Cobro #<?php echo htmlspecialchars((string)($mov->referencia1 ?: $mov->id), ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; color: #222; }
        .page { width: 100%; max-width: 900px; margin: 0 auto; padding: 20px; }
        .toolbar { text-align: right; margin-bottom: 12px; }
        .btn-print { background: #1e88e5; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .recibo { border: 1px solid #333; padding: 20px; }
        .header { display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 18px; }
        .company { max-width: 65%; }
        .company .name { font-size: 24px; font-weight: bold; margin: 0; }
        .company .doc { margin: 2px 0 8px; font-size: 14px; }
        .company .info { font-size: 12px; line-height: 1.4; }
        .receipt-box { min-width: 200px; border: 2px solid #222; padding: 12px; text-align: left; }
        .receipt-box .label { font-size: 12px; color: #333; margin-bottom: 6px; display: block; }
        .receipt-box .value { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .receipt-box .small { font-size: 12px; color: #555; }
        .section { margin-bottom: 16px; }
        .section .label { font-size: 12px; color: #555; display: block; margin-bottom: 6px; }
        .section .value { font-size: 14px; font-weight: 700; }
        .section-text { font-size: 14px; line-height: 1.6; white-space: pre-wrap; }
        .line { border-top: 1px solid #333; margin: 14px 0; }
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 10px; }
        .info-grid .box { border: 1px solid #ccc; padding: 12px; }
        .info-grid .box .label { margin-bottom: 6px; }
        .info-grid .box .value { font-size: 14px; font-weight: 700; }
        .total-row { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px; }
        .total-row .monto { font-size: 32px; font-weight: 800; color: #000; }
        .monto-letras { font-size: 13px; font-weight: 700; padding: 12px; background: #f9f9f9; border: 1px solid #ccc; }
        .firma-row { display: flex; justify-content: space-between; gap: 16px; margin-top: 36px; }
        .firma { flex: 1; text-align: center; padding-top: 18px; border-top: 1px solid #333; font-size: 12px; color: #555; }
        @media print { .toolbar { display: none; } .page { padding: 0; } .recibo { border: none; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <button class="btn-print" onclick="window.print()">Imprimir</button>
        </div>

        <?php
            function monto_en_letras($numero) {
                $numero = (float)$numero;
                $entero = (int)floor(abs($numero));
                $centavos = (int)round((abs($numero) - $entero) * 100);
                if ($centavos === 100) { $entero += 1; $centavos = 0; }

                $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
                $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
                $especiales = [10=>'diez',11=>'once',12=>'doce',13=>'trece',14=>'catorce',15=>'quince',16=>'dieciseis',17=>'diecisiete',18=>'dieciocho',19=>'diecinueve'];
                $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

                $convertir = function($num) use ($unidades, $decenas, $especiales, $centenas) {
                    $num = (int)$num;
                    if ($num === 0) return '';
                    if ($num === 100) return 'cien';
                    $texto = '';
                    $c = intval($num / 100);
                    $resto = $num % 100;
                    if ($c > 0) { $texto .= $centenas[$c]; if ($resto > 0) $texto .= ' '; }
                    if ($resto > 0) {
                        if ($resto < 10) {
                            $texto .= $unidades[$resto];
                        } elseif ($resto < 20) {
                            $texto .= $especiales[$resto];
                        } else {
                            $d = intval($resto / 10);
                            $u = $resto % 10;
                            $texto .= $decenas[$d];
                            if ($u > 0) $texto .= ' y ' . $unidades[$u];
                        }
                    }
                    return trim($texto);
                };

                if ($entero === 0) {
                    $texto = 'cero';
                } else {
                    $partes = [];
                    $millones = intval($entero / 1000000);
                    $mil = intval(($entero % 1000000) / 1000);
                    $resto = $entero % 1000;
                    if ($millones > 0) {
                        $partes[] = ($millones === 1 ? 'un millon' : $convertir($millones) . ' millones');
                    }
                    if ($mil > 0) {
                        $partes[] = ($mil === 1 ? 'mil' : $convertir($mil) . ' mil');
                    }
                    if ($resto > 0) {
                        $partes[] = $convertir($resto);
                    }
                    $texto = trim(implode(' ', $partes));
                }

                $moneda = 'CÓRDOBAS';
                $centavosTexto = str_pad((string)$centavos, 2, '0', STR_PAD_LEFT);
                return strtoupper($texto . ' ' . $moneda . ' CON ' . $centavosTexto . '/100');
            }

            $fecha = !empty($mov->fecha_aplicacion) ? $mov->fecha_aplicacion : $mov->fecha_registro;
            $monto = number_format((float)$mov->monto_total, 2, '.', ',');
            $montoLetra = monto_en_letras($mov->monto_total);
            $beneficiario = trim((string)($mov->beneficiario ?: '-'));
            $documento = trim((string)($mov->referencia1 ?: $mov->id));
            $serieCodigo = trim((string)($mov->serie_codigo ?: ''));
            $serieDescripcion = trim((string)($mov->serie_descripcion ?: ''));
            $cuentaNombre = trim((string)($mov->cuenta_nombre ?: ''));
            $cuentaCodigo = trim((string)($mov->cuenta_codigo ?: ''));
            $tipo = strtoupper(trim((string)$mov->tipo_transferencia ?: ($mov->tipo ?: '')));
        ?>

        <div class="recibo">
            <div class="header">
                <div class="company">
                    <p class="name">CREDI BLAMEN SOCIEDAD ANONIMA</p>
                    <p class="doc">RECIBO OFICIAL</p>
                    <p class="info">Tel: 2258-4498 | Managua, Nicaragua</p>
                </div>
                <div class="receipt-box">
                    <span class="label">Recibo N°</span>
                    <span class="value"><?php echo htmlspecialchars($documento, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="label">Serie</span>
                    <span class="value"><?php echo htmlspecialchars($serieCodigo, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="small"><?php echo htmlspecialchars($serieDescripcion, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <div class="info-grid">
                <div class="box">
                    <span class="label">Fecha</span>
                    <span class="value"><?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="box">
                    <span class="label">Tipo de Pago</span>
                    <span class="value"><?php echo htmlspecialchars($tipo ?: 'EFECTIVO', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="box">
                    <span class="label">Cuenta Destino</span>
                    <span class="value"><?php echo htmlspecialchars(trim($cuentaNombre . ' ' . $cuentaCodigo), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="box">
                    <span class="label">Moneda</span>
                    <span class="value"><?php echo htmlspecialchars(strtoupper(trim((string)$mov->moneda ?: 'NIO')), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <div class="section">
                <span class="label">Recibí de</span>
                <div class="section-text"><?php echo htmlspecialchars($beneficiario, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="section">
                <span class="label">Por concepto de</span>
                <div class="section-text"><?php echo nl2br(htmlspecialchars((string)$mov->descripcion, ENT_QUOTES, 'UTF-8')); ?></div>
            </div>

            <div class="line"></div>

            <div class="total-row">
                <div class="monto">C$ <?php echo htmlspecialchars($monto, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="monto-letras"><?php echo htmlspecialchars($montoLetra, ENT_QUOTES, 'UTF-8'); ?></div>

            <div class="firma-row">
                <div class="firma">Firma del Receptor</div>
                <div class="firma">Firma del Emisor</div>
            </div>
        </div>
    </div>
</body>
</html>
