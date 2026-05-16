#!/usr/bin/env php
<?php
/**
 * Script de Prueba - Verificar Implementación de Días de Mora
 * 
 * Uso: php test_dias_mora.php
 * 
 * Este script verifica que:
 * 1. Las columnas existe en la base de datos
 * 2. El controlador tiene el método update_dias_mora
 * 3. La vista tiene el JavaScript
 */

// Colores para salida en terminal
$colors = [
    'reset'   => "\033[0m",
    'red'     => "\033[31m",
    'green'   => "\033[32m",
    'yellow'  => "\033[33m",
    'blue'    => "\033[34m",
];

function print_test($name, $status) {
    global $colors;
    $symbol = $status ? "✓" : "✗";
    $color = $status ? $colors['green'] : $colors['red'];
    echo $color . "[$symbol]" . $colors['reset'] . " $name\n";
    return $status;
}

echo "\n" . $colors['blue'] . "=== Verificación de Implementación de Días de Mora ===" . $colors['reset'] . "\n\n";

// Test 1: Verificar archivos
echo $colors['yellow'] . "Verificando archivos...\n" . $colors['reset'];

$files_to_check = [
    'application/controllers/Planescredito.php',
    'application/views/planescredito/estado_cuenta.php',
];

$all_files_exist = true;
foreach ($files_to_check as $file) {
    $full_path = dirname(__FILE__) . '/' . $file;
    $exists = file_exists($full_path);
    print_test("Archivo existe: $file", $exists);
    if (!$exists) $all_files_exist = false;
}

echo "\n";

// Test 2: Verificar contenido del controlador
echo $colors['yellow'] . "Verificando controlador...\n" . $colors['reset'];

$controller_path = dirname(__FILE__) . '/application/controllers/Planescredito.php';
if (file_exists($controller_path)) {
    $controller_content = file_get_contents($controller_path);
    
    $has_method = strpos($controller_content, 'public function update_dias_mora()') !== false;
    print_test("Método 'update_dias_mora()' existe", $has_method);
    
    $has_ajax_handling = strpos($controller_content, "set_content_type('application/json')") !== false;
    print_test("Respuesta JSON configurada", $has_ajax_handling);
    
    $has_calculation = strpos($controller_content, '(0.18 / 360)') !== false;
    print_test("Fórmula de cálculo presente", $has_calculation);
}

echo "\n";

// Test 3: Verificar contenido de la vista
echo $colors['yellow'] . "Verificando vista...\n" . $colors['reset'];

$view_path = dirname(__FILE__) . '/application/views/planescredito/estado_cuenta.php';
if (file_exists($view_path)) {
    $view_content = file_get_contents($view_path);
    
    $has_js_function = strpos($view_content, 'function editDiasMora(') !== false;
    print_test("Función JavaScript 'editDiasMora()' presente", $has_js_function);
    
    $has_dias_mora_col = strpos($view_content, 'Días Mora') !== false;
    print_test("Columna 'Días Mora' presente", $has_dias_mora_col);
    
    $has_monto_mora_col = strpos($view_content, 'Monto Mora') !== false;
    print_test("Columna 'Monto Mora' presente", $has_monto_mora_col);
    
    $has_edit_button = strpos($view_content, 'onclick="editDiasMora') !== false;
    print_test("Botón 'Editar' con onclick presente", $has_edit_button);
    
    $has_ajax = strpos($view_content, '$.ajax') !== false;
    print_test("Petición AJAX configurada", $has_ajax);
}

echo "\n" . $colors['yellow'] . "Verificando columnas de base de datos...\n" . $colors['reset'];

// Intentar conexión a BD
try {
    // Cargar config de CodeIgniter
    $config_path = dirname(__FILE__) . '/application/config/database.php';
    if (file_exists($config_path)) {
        require_once $config_path;
        
        // Intentar conexión
        $db = new mysqli(
            $db['default']['hostname'] ?? 'localhost',
            $db['default']['username'] ?? 'root',
            $db['default']['password'] ?? '',
            $db['default']['database'] ?? ''
        );
        
        if ($db->connect_error) {
            print_test("Conexión a BD", false);
            echo "  Error: " . $db->connect_error . "\n";
        } else {
            print_test("Conexión a BD", true);
            
            // Verificar columnas
            $result = $db->query("DESCRIBE tb_prestamo_cuotas");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            $has_dias_mora_manual = in_array('dias_mora_manual', $columns);
            print_test("Columna 'dias_mora_manual' existe", $has_dias_mora_manual);
            
            $has_monto_mora = in_array('monto_mora', $columns);
            print_test("Columna 'monto_mora' existe", $has_monto_mora);
            
            $db->close();
        }
    } else {
        print_test("Verificación de BD", false);
        echo "  Archivo de config no encontrado\n";
    }
} catch (Exception $e) {
    print_test("Verificación de BD", false);
    echo "  Error: " . $e->getMessage() . "\n";
}

echo "\n" . $colors['blue'] . "=== Verificación Completada ===" . $colors['reset'] . "\n\n";

echo $colors['yellow'] . "Próximos pasos:\n" . $colors['reset'];
echo "1. Navega a: http://localhost/Crediblamen/planescredito/estado_cuenta/{id_prestamo}\n";
echo "2. Verifica que aparecen las columnas 'Días Mora' y 'Monto Mora'\n";
echo "3. Haz clic en el botón 'Editar' de cualquier cuota\n";
echo "4. Ingresa un nuevo valor (ej: 10)\n";
echo "5. Verifica que el 'Monto Mora' se actualiza automáticamente\n";
echo "6. Recarga la página y verifica que los cambios persisten\n\n";
?>
