<?php
// Script para inspeccionar la estructura de la tabla tb_analisis_financiero_comerciante

// Configurar conexión (ajusta según tu config)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'crediblamen'; // Reemplaza con tu BD

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

echo "<h2>Columnas en tb_analisis_financiero_comerciante</h2>";

$sql = "DESCRIBE tb_analisis_financiero_comerciante";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

echo "<h2>Campos que se envían desde el formulario comerciante</h2>";
echo "<pre>";
$campos_formulario = [
    'efectivo_caja', 'dinero_banco', 'total_disponible', 'cuentas_cobrar',
    'inventario_mercaderia', 'productos_proceso', 'productos_terminados', 'total_inventarios',
    'bienes_muebles', 'propiedades', 'otros_activos', 'total_activos_fijos', 'total_activos',
    'cuentas_pagar_proveedores', 'cuentas_pagar_credito', 'pasivo_no_corriente', 'total_pasivo',
    'total_patrimonio', 'total_pasivo_patrimonio',
    'ventas_contado', 'ventas_credito', 'ventas_totales', 'costos_venta', 'margen_bruto', 'gastos_generales', 'utilidad_operativa',
    'fcm_ventas_contado', 'fcm_recuperacion_credito', 'fcm_compras_contado', 'fcm_gastos_generales', 'flujo_negocio',
    'fcm_otros_ingresos', 'fcm_gastos_consumo', 'fcm_valor_canasta_basica', 'fcm_cant_personas_dep', 'fcm_otros_gastos',
    'olp_fecha', 'olp_cuota', 'olp_instituciones', 'olp_saldo', 'subtotal_olp_saldo',
    'ocp_fecha', 'ocp_cuota', 'ocp_instituciones', 'ocp_saldo', 'subtotal_ocp_saldo',
    'costo_salario_ayudante', 'costo_transporte', 'costo_total_operacion',
    'indicador_endeudamiento', 'capital_trabajo_neto', 'porcentaje_margen', 'monto_credito_solicitado'
];
print_r($campos_formulario);
echo "</pre>";

echo "<h2>Campos FALTANTES en la tabla</h2>";
echo "<pre>";
$sql_columns = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'tb_analisis_financiero_comerciante' AND TABLE_SCHEMA = '{$db_name}'";
$result_cols = $conn->query($sql_columns);
$columnas_existentes = [];
while ($row = $result_cols->fetch_assoc()) {
    $columnas_existentes[] = $row['COLUMN_NAME'];
}

$faltantes = array_diff($campos_formulario, $columnas_existentes);
if (empty($faltantes)) {
    echo "✓ Todos los campos existen en la tabla.";
} else {
    echo "Los siguientes campos NO existen en la tabla:\n";
    print_r($faltantes);
}
echo "</pre>";

$conn->close();
?>
