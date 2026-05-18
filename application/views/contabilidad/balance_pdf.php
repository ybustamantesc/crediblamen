<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Balance General - <?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; font-size:11px; }
    th, td { border: 1px solid #ddd; padding: 6px; }
    th { background: #f5f5f5; }
    .text-right { text-align: right; }
    .title { font-size: 16px; font-weight: bold; }
    .meta { font-size: 10px; color: #666; }
    .signature { margin-top: 40px; }
    .small { font-size: 9px; color: #444; }
  </style>
</head>
<body>
  <div class="header">
    <div class="title"><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></div>
    <div class="meta"><?php echo isset($empresa->direccion) ? $empresa->direccion : ''; ?></div>
    <div style="margin-top:6px;">Balance General al <?php echo isset($as_of) && $as_of ? $as_of : date('Y-m-d'); ?></div>
    <div class="small">Generado por: <?php echo isset($exported_by) ? $exported_by : 'Sistema'; ?> - <?php echo isset($exported_at) ? $exported_at : date('Y-m-d H:i:s'); ?></div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Grupo</th>
        <th>Código</th>
        <th>Cuenta</th>
        <th class="text-right">Saldo</th>
        <th class="text-right">Comparativo (Año Ant.)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (isset($data['groups']['activo'])): ?>
        <tr><td colspan="5" style="font-weight:bold;">ACTIVO</td></tr>
        <?php foreach ($data['groups']['activo'] as $r): ?>
          <tr>
            <td>Activo</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?><?php echo (isset($r['current']) && $r['current']) ? ' (Corriente)' : ''; ?></td>
            <td class="text-right"><?php echo number_format($r['display'],2,'.',','); ?></td>
            <td class="text-right"><?php echo number_format(isset($r['compare_display']) ? $r['compare_display'] : 0,2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Activo</td><td class="text-right"><?php echo number_format($data['totals']['activo'],2,'.',','); ?></td><td></td></tr>
      <?php endif; ?>

      <?php if (isset($data['groups']['pasivo'])): ?>
        <tr><td colspan="5" style="font-weight:bold;">PASIVO</td></tr>
        <?php foreach ($data['groups']['pasivo'] as $r): ?>
          <tr>
            <td>Pasivo</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?><?php echo (isset($r['current']) && $r['current']) ? ' (Corriente)' : ''; ?></td>
            <td class="text-right"><?php echo number_format(abs($r['display']),2,'.',','); ?></td>
            <td class="text-right"><?php echo number_format(isset($r['compare_display']) ? $r['compare_display'] : 0,2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Pasivo</td><td class="text-right"><?php echo number_format($data['totals']['pasivo'],2,'.',','); ?></td><td></td></tr>
      <?php endif; ?>

      <?php if (isset($data['groups']['patrimonio'])): ?>
        <tr><td colspan="5" style="font-weight:bold;">PATRIMONIO</td></tr>
        <?php foreach ($data['groups']['patrimonio'] as $r): ?>
          <tr>
            <td>Patrimonio</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?></td>
            <td class="text-right"><?php echo number_format(abs($r['display']),2,'.',','); ?></td>
            <td class="text-right"><?php echo number_format(isset($r['compare_display']) ? $r['compare_display'] : 0,2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Patrimonio</td><td class="text-right"><?php echo number_format($data['totals']['patrimonio'],2,'.',','); ?></td><td></td></tr>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="3" style="font-weight:bold;">TOTAL ACTIVO</td><td class="text-right"><?php echo number_format($data['totals']['activo'],2,'.',','); ?></td><td></td></tr>
      <tr><td colspan="3" style="font-weight:bold;">TOTAL PASIVO + PATRIMONIO</td><td class="text-right"><?php echo number_format($data['totals']['pasivo_patrimonio'],2,'.',','); ?></td><td></td></tr>
    </tfoot>

    <div class="signature">
      <div>Firma: ____________________________</div>
      <div class="small">Hash del documento: {hash}</div>
    </div>

  <script type="text/php">
    if (isset($pdf)) {
      $font = $fontMetrics->getFont('DejaVuSans');
      $pdf->page_text(520, 15, "Página {PAGE_NUM} / {PAGE_COUNT}", $font, 9, array(0,0,0));
    }
  </script>
</body>
</html>
