<?php
/**
 * Script para verificar los campos de la tabla tb_analisis_financiero_comerciante
 * Ejecuta desde el navegador: http://localhost/Crediblamen/check_table_fields.php
 */

// Conexión a la base de datos
$config = array(
    'host' => 'localhost',
    'user' => 'root', // Ajusta según tu configuración
    'password' => '', // Ajusta según tu configuración
    'database' => 'crediblamensis' // Ajusta según tu base de datos
);

$conn = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener información de la tabla
$table_name = 'tb_analisis_financiero_comerciante';
$query = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table_name'";

$result = $conn->query($query);

echo "<h2>Campos de la tabla: $table_name</h2>";
echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>Campo</th>";
echo "<th>Tipo</th>";
echo "<th>Nulo</th>";
echo "<th>Clave</th>";
echo "<th>Valor por defecto</th>";
echo "</tr>";

$campos = array();
while ($row = $result->fetch_assoc()) {
    $campos[] = $row['COLUMN_NAME'];
    echo "<tr>";
    echo "<td><code>" . $row['COLUMN_NAME'] . "</code></td>";
    echo "<td>" . $row['COLUMN_TYPE'] . "</td>";
    echo "<td>" . ($row['IS_NULLABLE'] === 'YES' ? 'Sí' : 'No') . "</td>";
    echo "<td>" . ($row['COLUMN_KEY'] ?: '-') . "</td>";
    echo "<td>" . ($row['COLUMN_DEFAULT'] ?: '-') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr/>";
echo "<h3>Campos detectados:</h3>";
echo "<pre>";
echo implode("\n", $campos);
echo "</pre>";

// Campos que se usan en el código del Asalariado
$campos_esperados = [
    'id', 'idsolicitud', 'ingreso_sueldo_neto', 'ingreso_comisiones', 'ingreso_bonificaciones', 'ingreso_remesas', 
    'ingreso_otros', 'total_ingresos', 'sueldo', 'inss', 'ir', 'sueldo_neto_calc', 'gastos_alimentacion', 
    'gastos_servicios', 'gastos_vestuario', 'gastos_educativos', 'gastos_transporte', 'gastos_alquiler', 
    'pago_empleado_viatico', 'entretenimiento', 'otros_gastos', 'total_gastos_familiares', 'cuotas_prestamos', 
    'pension_alimenticia', 'otras_obligaciones', 'total_otras_obligaciones', 'total_egresos', 'flujo_neto_mensual', 
    'cuota_periodica', 'canasta_basica', 'cantidad_promedio', 'monto_por_persona', 'personas_dependientes', 
    'gastos_alimentacion_canasta', 'transporte_urbano', 'transporte_individual', 'transporte_interurbano', 
    'recorrido_laboral', 'vehiculo_particular', 'total_transporte', 'alquiler', 'casa_propia', 'total_gastos_vivienda',
    'cobertura_deuda', 'cobertura_garantia', 'tc_acumulado', 'p_entretenimiento', 'created_at', 'updated_at'
];

echo "<h3>Campos que FALTAN en la tabla:</h3>";
$campos_faltantes = array_diff($campos_esperados, $campos);
if (!empty($campos_faltantes)) {
    echo "<pre style='background-color: #fff3cd; padding: 10px;'>";
    echo implode("\n", $campos_faltantes);
    echo "</pre>";
    
    echo "<h4>Script ALTER TABLE para crear los campos faltantes:</h4>";
    echo "<pre style='background-color: #f8f9fa; padding: 10px; border: 1px solid #dee2e6;'>";
    foreach ($campos_faltantes as $campo) {
        echo "ALTER TABLE `$table_name` ADD COLUMN `$campo` DECIMAL(14,2) NULL;\n";
    }
    echo "</pre>";
} else {
    echo "<p style='color: green;'><strong>✓ Todos los campos existen en la tabla</strong></p>";
}

$conn->close();
?>
