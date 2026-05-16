<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen de Creditos Aprobados/Rechazados</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #222; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { margin: 0 0 4px 0; font-size: 16px; }
        .meta { margin-bottom: 10px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #bbb; padding: 4px; vertical-align: top; }
        th { background: #f0f0f0; text-align: center; }
        .right { text-align: right; }
        .center { text-align: center; }
        .signatures { width: 100%; margin-top: 32px; table-layout: fixed; }
        .signatures td { border: none; text-align: center; padding: 18px 8px 0 8px; }
        .line { border-top: 1px solid #222; margin: 0 auto 6px auto; width: 90%; }
        .small { font-size: 9px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo htmlspecialchars($titulo); ?></h2>
        <div class="small">Generado: <?php echo htmlspecialchars($generado_en); ?></div>
    </div>

    <div class="meta">
        <strong>Filtro fecha inicio:</strong> <?php echo $fecha_inicio !== '' ? htmlspecialchars($fecha_inicio) : 'Sin filtro'; ?>
        &nbsp; | &nbsp;
        <strong>Filtro fecha fin:</strong> <?php echo $fecha_fin !== '' ? htmlspecialchars($fecha_fin) : 'Sin filtro'; ?>
        &nbsp; | &nbsp;
        <strong>Estado:</strong>
        <?php
            if ($estado === 'approved') echo 'Aprobado';
            elseif ($estado === 'rejected') echo 'Rechazado';
            elseif ($estado === 'pending') echo 'Pendiente';
            else echo 'Todos';
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha Decision</th>
                <th>Solicitud</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Via Aprobacion</th>
                <th>Aprobado por</th>
                <th>Monto</th>
                <th>Tasa Interes</th>
                <th>Plazo</th>
                <th>Cuota Estimada</th>
                <th>Destino Conami</th>
                <th>Creado por</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $i => $r): ?>
                    <tr>
                        <td class="center"><?php echo ($i + 1); ?></td>
                        <td class="center"><?php echo !empty($r->fecha_decision) ? htmlspecialchars(substr($r->fecha_decision, 0, 16)) : ''; ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->idsolicitud); ?></td>
                        <td><?php echo htmlspecialchars((string)$r->cliente); ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->estado); ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->via_aprobacion); ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->aprobado_usuario); ?></td>
                        <td class="right"><?php echo ($r->monto_solicitado !== null && $r->monto_solicitado !== '') ? number_format((float)$r->monto_solicitado, 2) : ''; ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->tasa); ?></td>
                        <td class="center"><?php echo htmlspecialchars((string)$r->plazo); ?></td>
                        <td class="right"><?php echo ($r->cuota_estimada !== null && $r->cuota_estimada !== '') ? number_format((float)$r->cuota_estimada, 2) : ''; ?></td>
                        <td><?php echo htmlspecialchars((string)$r->destino_conami); ?></td>
                        <td><?php echo htmlspecialchars((string)$r->creado_por); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="center">No hay registros para este filtro.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="line"></div>
                <strong>Firma 1</strong><br>
                <span class="small">Elaborado por</span>
            </td>
            <td>
                <div class="line"></div>
                <strong>Firma 2</strong><br>
                <span class="small">Revisado por</span>
            </td>
            <td>
                <div class="line"></div>
                <strong>Firma 3</strong><br>
                <span class="small">Aprobado por</span>
            </td>
            <td>
                <div class="line"></div>
                <strong>Firma 4</strong><br>
                <span class="small">Autorizado por</span>
            </td>
        </tr>
    </table>
</body>
</html>
