<?php $empresa = isset($empresa) ? $empresa : null; $h = $journal['header']; $lines = $journal['lines']; 
$totalDebit = 0; $totalCredit = 0;
foreach ($lines as $l) { $totalDebit += $l->debit; $totalCredit += $l->credit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Asiento Contable #<?php echo $h->id; ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:12px; color:#333; background:#fff; }

@media print {
    @page { 
        size: letter;
        margin: 1.5cm 1cm 1cm 1cm;
    }
    body { padding:0; }
}

@media screen {
    body { padding:30px; max-width:900px; margin:0 auto; }
}

.print-header { 
    display:flex; 
    align-items:center; 
    margin-bottom:20px; 
    padding-bottom:15px; 
    border-bottom:3px solid #002060; 
}

.logo { 
    width:70px; 
    height:70px; 
    margin-right:20px; 
    object-fit:contain; 
}

.header-info { flex:1; }

.empresa-name { 
    font-size:16px; 
    font-weight:700; 
    color:#002060; 
    margin-bottom:4px; 
    line-height:1.2;
}

.empresa-detail { 
    font-size:10px; 
    color:#666; 
    line-height:1.4;
}

.header-right { 
    text-align:right; 
    padding-left:20px;
}

.asiento-number { 
    font-size:18px; 
    font-weight:700; 
    color:#002060; 
    margin-bottom:3px; 
}

.asiento-date { 
    font-size:11px; 
    color:#666; 
}

.section-title { 
    font-size:14px; 
    font-weight:600; 
    color:#002060; 
    margin:15px 0 10px 0; 
    padding-bottom:5px; 
    border-bottom:2px solid #e5e7eb; 
}

.info-box { 
    background:#f8f9fa; 
    padding:10px 12px; 
    border-radius:4px; 
    margin-bottom:15px; 
    border-left:4px solid #002060; 
    font-size:11px;
    line-height:1.6;
}

.info-label { 
    font-weight:600; 
    color:#002060; 
    margin-right:8px; 
}

.table { 
    width:100%; 
    border-collapse:collapse; 
    margin-top:10px; 
}

.table thead { 
    background:#002060; 
    color:#fff; 
}

.table th { 
    padding:8px 6px; 
    text-align:left; 
    font-weight:600; 
    font-size:10px; 
    text-transform:uppercase; 
    border:1px solid #002060; 
}

.table td { 
    padding:6px; 
    border:1px solid #ddd; 
    font-size:10px; 
    vertical-align:top;
}

.table tbody tr:nth-child(even) { 
    background:#f8f9fa; 
}

.text-right { text-align:right; }

.total-row { 
    background:#e9ecef !important; 
    font-weight:700; 
    border-top:2px solid #002060 !important;
}

.total-row td { 
    font-size:11px; 
    color:#002060; 
    padding:8px 6px;
}

.account-code { 
    color:#002060; 
    font-weight:600; 
}

.amount-positive { 
    color:#059669; 
    font-weight:600; 
}

.page-break { page-break-after: always; }

@media print {
    .no-print { display:none !important; }
}
</style>
</head>
<body>
<div class="print-header">
    <?php if ($empresa && !empty($empresa->logotipo)) : ?>
        <img class="logo" src="<?php echo base_url('uploads/'.$empresa->logotipo); ?>" alt="Logo" />
    <?php endif; ?>
    <div class="header-info">
        <div class="empresa-name"><?php echo $empresa ? htmlspecialchars($empresa->razon_social) : 'Empresa'; ?></div>
        <?php if ($empresa && !empty($empresa->direccion)): ?>
            <div class="empresa-detail"><?php echo htmlspecialchars($empresa->direccion); ?></div>
        <?php endif; ?>
        <?php if ($empresa && !empty($empresa->telefonos)): ?>
            <div class="empresa-detail">Tel: <?php echo htmlspecialchars($empresa->telefonos); ?></div>
        <?php endif; ?>
    </div>
    <div class="header-right">
        <div class="asiento-number">Asiento #<?php echo $h->id; ?></div>
        <div class="asiento-date"><?php echo date('d/m/Y', strtotime($h->date)); ?></div>
    </div>
</div>

<div class="content">
    <div class="section-title">Detalle del Asiento Contable</div>
    
    <div class="info-box">
        <div><span class="info-label">Descripción:</span><?php echo htmlspecialchars($h->description); ?></div>
        <div style="margin-top:5px"><span class="info-label">Fecha:</span><?php echo date('d/m/Y', strtotime($h->date)); ?></div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width:15%">Código</th>
                <th style="width:32%">Nombre de Cuenta</th>
                <th style="width:15%" class="text-right">Debe</th>
                <th style="width:15%" class="text-right">Haber</th>
                <th style="width:23%">Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lines as $l): ?>
                <tr>
                    <td><span class="account-code"><?php echo htmlspecialchars($l->code); ?></span></td>
                    <td><?php echo htmlspecialchars($l->name); ?></td>
                    <td class="text-right">
                        <?php if ($l->debit > 0): ?>
                            <span class="amount-positive"><?php echo number_format($l->debit, 2, '.', ','); ?></span>
                        <?php else: ?>
                            <span style="color:#ccc">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if ($l->credit > 0): ?>
                            <span class="amount-positive"><?php echo number_format($l->credit, 2, '.', ','); ?></span>
                        <?php else: ?>
                            <span style="color:#ccc">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($l->line_description); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>TOTALES:</strong></td>
                <td class="text-right"><strong><?php echo number_format($totalDebit, 2, '.', ','); ?></strong></td>
                <td class="text-right"><strong><?php echo number_format($totalCredit, 2, '.', ','); ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top:30px; text-align:center; font-size:9px; color:#999;" class="no-print">
        Documento generado el <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</div>

<script>
    window.onload = function(){ setTimeout(function(){ window.print(); }, 400); };
</script>
</body>
</html>