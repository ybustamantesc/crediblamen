<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Balance General - <?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 10px; }
    .meta { margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background: #eee; }
    .text-right { text-align: right; }
    .title { font-size: 16px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <div class="title"><?php echo isset($empresa->nombre) ? $empresa->nombre : 'Empresa'; ?></div>
    <div><?php echo isset($empresa->direccion) ? $empresa->direccion : ''; ?></div>
    <div style="margin-top:6px;">Balance General al <?php echo isset($as_of) && $as_of ? $as_of : date('Y-m-d'); ?></div>
  </div>

  <table>
    <thead>
      <tr><th>Grupo</th><th>Código</th><th>Cuenta</th><th class="text-right">Saldo</th></tr>
    </thead>
    <tbody>
      <?php if (isset($data['groups']['activo'])): ?>
        <tr><td colspan="4" style="font-weight:bold;">ACTIVO</td></tr>
        <?php foreach ($data['groups']['activo'] as $r): ?>
          <tr>
            <td>Activo</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?></td>
            <td class="text-right"><?php echo number_format($r['display'],2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Activo</td><td class="text-right"><?php echo number_format($data['totals']['activo'],2,'.',','); ?></td></tr>
      <?php endif; ?>

      <?php if (isset($data['groups']['pasivo'])): ?>
        <tr><td colspan="4" style="font-weight:bold;">PASIVO</td></tr>
        <?php foreach ($data['groups']['pasivo'] as $r): ?>
          <tr>
            <td>Pasivo</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?></td>
            <td class="text-right"><?php echo number_format(abs($r['display']),2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Pasivo</td><td class="text-right"><?php echo number_format($data['totals']['pasivo'],2,'.',','); ?></td></tr>
      <?php endif; ?>

      <?php if (isset($data['groups']['patrimonio'])): ?>
        <tr><td colspan="4" style="font-weight:bold;">PATRIMONIO</td></tr>
        <?php foreach ($data['groups']['patrimonio'] as $r): ?>
          <tr>
            <td>Patrimonio</td>
            <td><?php echo $r['code']; ?></td>
            <td><?php echo $r['name']; ?></td>
            <td class="text-right"><?php echo number_format(abs($r['display']),2,'.',','); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" style="font-weight:bold;">Total Patrimonio</td><td class="text-right"><?php echo number_format($data['totals']['patrimonio'],2,'.',','); ?></td></tr>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="3" style="font-weight:bold;">TOTAL ACTIVO</td><td class="text-right"><?php echo number_format($data['totals']['activo'],2,'.',','); ?></td></tr>
      <tr><td colspan="3" style="font-weight:bold;">TOTAL PASIVO + PATRIMONIO</td><td class="text-right"><?php echo number_format($data['totals']['pasivo_patrimonio'],2,'.',','); ?></td></tr>
    </tfoot>
  </table>

  <script>
    window.onload = function(){ window.print(); };
  </script>
</body>
</html>
