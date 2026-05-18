<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Flujo de Efectivo</title>
    <style>body{font-family: Arial, Helvetica, sans-serif; font-size:12px;} table{width:100%;border-collapse:collapse;} th,td{padding:6px;border:1px solid #ccc;} .text-right{text-align:right;}</style>
</head>
<body>
    <h3><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?> - Flujo de Efectivo</h3>
    <p>Periodo: <?php echo ($start ? $start : '') . ' - ' . ($end ? $end : ''); ?></p>
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
        <tfoot>
            <tr><th colspan="4">Total Colecciones (Créditos)</th><th class="text-right"><?php echo number_format($data['totals']['colecciones_creditos'] ?? 0,2,'.',''); ?></th></tr>
            <tr><th colspan="4">Total Intereses / Comisiones</th><th class="text-right"><?php echo number_format($data['totals']['intereses_comisiones'] ?? 0,2,'.',''); ?></th></tr>
            <tr><th colspan="4">Total Desembolsos</th><th class="text-right"><?php echo number_format($data['totals']['desembolsos_creditos'] ?? 0,2,'.',''); ?></th></tr>
            <tr><th colspan="4">Total Pagos Operativos</th><th class="text-right"><?php echo number_format($data['totals']['pagos_operativos'] ?? 0,2,'.',''); ?></th></tr>
            <tr><th colspan="4">Total Financiación</th><th class="text-right"><?php echo number_format($data['totals']['financiacion'] ?? 0,2,'.',''); ?></th></tr>
            <tr class="font-weight-bold"><th colspan="4">Flujo Neto</th><th class="text-right"><?php echo number_format($data['totals']['neto'] ?? 0,2,'.',''); ?></th></tr>
        </tfoot>
    </table>
    <div style="margin-top:20px;font-size:10px;color:#666;">Hash: {hash}</div>
</body>
</html>