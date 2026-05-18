<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balanza de Comprobación</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 10px; }
        .company { font-weight: bold; font-size: 16px; }
        .meta { margin-top: 4px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .negative { color: red; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company"><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></div>
        <div class="meta">Balanza de Comprobación</div>
        <div class="meta">Periodo: <?php echo ($start ? $start : '-') . ' — ' . ($end ? $end : '-'); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cuenta</th>
                <th>Saldo Inicial (D)</th>
                <th>Saldo Inicial (H)</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo Final (D)</th>
                <th>Saldo Final (H)</th>
                <th>Balance Final</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['rows'])): foreach ($data['rows'] as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['code']); ?></td>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td class="text-right"><?php echo number_format($r['opening_deudor'],2); ?></td>
                <td class="text-right"><?php echo number_format($r['opening_acreedor'],2); ?></td>
                <td class="text-right"><?php echo number_format($r['debits'],2); ?></td>
                <td class="text-right"><?php echo number_format($r['credits'],2); ?></td>
                <td class="text-right <?php echo $r['closing_deudor'] < 0 ? 'negative' : ''; ?>"><?php echo number_format($r['closing_deudor'],2); ?></td>
                <td class="text-right <?php echo $r['closing_acreedor'] < 0 ? 'negative' : ''; ?>"><?php echo number_format($r['closing_acreedor'],2); ?></td>
                <td class="text-right"><?php echo number_format($r['closing_deudor'] - $r['closing_acreedor'],2); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="9">No hay datos para el rango seleccionado.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">TOTALES</th>
                <th class="text-right"><?php echo number_format($data['totals']['opening_deudor'],2); ?></th>
                <th class="text-right"><?php echo number_format($data['totals']['opening_acreedor'],2); ?></th>
                <th class="text-right"><?php echo number_format($data['totals']['debits'],2); ?></th>
                <th class="text-right"><?php echo number_format($data['totals']['credits'],2); ?></th>
                <th class="text-right"><?php echo number_format($data['totals']['closing_deudor'],2); ?></th>
                    <th class="text-right"><?php echo number_format($data['totals']['closing_acreedor'],2); ?></th>
                    <th class="text-right"><?php echo number_format(($data['totals']['closing_deudor'] - $data['totals']['closing_acreedor']),2); ?></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top:20px;">
        <div style="display:flex; justify-content:space-between; margin-top:24px;">
            <div style="text-align:center; width:33%;">
                <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                <div style="font-weight:700;">Contador General</div>
            </div>
            <div style="text-align:center; width:33%;">
                <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                <div style="font-weight:700;">Gerente Financiero</div>
            </div>
            <div style="text-align:center; width:33%;">
                <div style="border-top:1px solid #000; width:70%; margin:0 auto 6px;"></div>
                <div style="font-weight:700;">Gerente General</div>
            </div>
        </div>
    </div>

    <div style="margin-top:10px; text-align:center;" class="no-print">
        <button onclick="window.print();" class="btn btn-primary">Imprimir</button>
    </div>
    <script>
        // Auto-print when loaded in a print flow
        window.onload = function(){ setTimeout(function(){ window.print(); }, 300); };
    </script>
</body>
</html>
