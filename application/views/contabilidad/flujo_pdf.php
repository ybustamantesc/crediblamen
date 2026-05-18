<?php // Reuse same layout as print but allow extra header/footer for PDF ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Flujo de Efectivo</title>
    <style>body{font-family: Arial, Helvetica, sans-serif; font-size:12px;} table{width:100%;border-collapse:collapse;} th,td{padding:6px;border:1px solid #ccc;} .text-right{text-align:right;}</style>
</head>
<body>
    <header style="margin-bottom:10px;">
        <h2><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></h2>
        <h3>Flujo de Efectivo</h3>
        <p>Periodo: <?php echo ($start ? $start : '') . ' - ' . ($end ? $end : ''); ?></p>
        <p>Generado por: <?php echo $exported_by ?: 'Sistema'; ?> el <?php echo $exported_at; ?></p>
    </header>
    <table>
        <thead>
            <tr><th>Fecha</th><th>Asiento</th><th>Descripción</th><th>Categoría</th><th class="text-right">Monto</th></tr>
        </thead>
        <tbody>
            <?php if (empty($data['rows'])): ?>
                <tr><td colspan="5" class="text-center">No hay movimientos</td></tr>
            <?php else: foreach ($data['rows'] as $r): ?>
                <tr>
                    <td><?php echo $r['date']; ?></td>
                    <td><?php echo $r['journal_id']; ?></td>
                    <td><?php echo htmlspecialchars($r['description']); ?></td>
                    <td><?php echo $r['category']; ?></td>
                    <td class="text-right"><?php echo number_format($r['amount'],2,'.',''); ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    <footer style="position:fixed;bottom:0;left:0;right:0;text-align:right;font-size:10px;color:#666;">Hash: {hash}</footer>
</body>
</html>