<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($titulo) ? $titulo : 'Cobros'; ?></title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .company { font-weight:700; font-size:16px; }
        .meta { text-align:right; font-size:11px; color:#555; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { padding:8px 10px; border-bottom:1px solid #e6e6e6; }
        thead th { background:#f5f7fa; text-align:left; color:#333; font-weight:700; }
        tbody tr:nth-child(even){ background:#fbfcfd; }
        .text-right { text-align:right; }
        .small { font-size:11px; color:#666; }
        .footer { margin-top:18px; font-size:11px; color:#444; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company"><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></div>
            <div class="small"><?php echo isset($titulo) ? $titulo : 'Listado de Cobros'; ?></div>
        </div>
        <div class="meta">
            <div>Periodo: <?php echo ($date_from ? $date_from : '---') . ' — ' . ($date_to ? $date_to : '---'); ?></div>
            <div>Generado: <?php echo date('Y-m-d H:i'); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th style="width:90px">Fecha</th>
                <th>Persona</th>
                <th>Descripción</th>
                <th>Cuenta</th>
                <th style="width:130px">Serie / Recibo</th>
                <th style="width:110px" class="text-right">Monto</th>
                <th style="width:60px">Moneda</th>
                <th style="width:90px">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cobros) && is_array($cobros)): ?>
                <?php foreach ($cobros as $index => $cobro): ?>
                    <tr>
                        <td><?php echo ($index + 1); ?></td>
                        <td><?php echo isset($cobro['fecha_registro']) ? substr($cobro['fecha_registro'], 0, 10) : ''; ?></td>
                        <td><?php echo htmlspecialchars($cobro['beneficiario'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($cobro['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(trim(($cobro['cuenta_nombre'] ?? '') . ' (' . ($cobro['cuenta_codigo'] ?? '') . ')'), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(trim(($cobro['serie_codigo'] ?? '') . ' ' . ($cobro['referencia1'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-right"><?php echo number_format(floatval($cobro['monto_total'] ?? 0), 2, '.', ','); ?></td>
                        <td><?php echo htmlspecialchars($cobro['moneda'] ?? 'NIO', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($cobro['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="small">No se encontraron cobros para el rango seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="small">Documento generado por el sistema. <?php echo date('Y'); ?></div>
    </div>
</body>
</html>
