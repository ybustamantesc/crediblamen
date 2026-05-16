<?php
// Lista simple de cuentas para verificar importación
$mysqli = new mysqli('localhost', 'root', '', 'minitas');
if ($mysqli->connect_error) {
    die('DB connection error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$res = $mysqli->query("SELECT id, code, name, type, parent_id, created_at FROM tb_account ORDER BY code");
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Cuentas importadas</title>
  <style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:4px}</style>
</head>
<body>
  <h1>Cuentas importadas (<?php echo $res ? $res->num_rows : 0; ?>)</h1>
  <table>
    <thead><tr><th>ID</th><th>Código</th><th>Nombre</th><th>Tipo</th><th>Parent</th><th>Creado</th></tr></thead>
    <tbody>
    <?php if ($res): while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['id']); ?></td>
        <td><?php echo htmlspecialchars($row['code']); ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['type']); ?></td>
        <td><?php echo htmlspecialchars($row['parent_id']); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
      </tr>
    <?php endwhile; endif; ?>
    </tbody>
  </table>
</body>
</html>
