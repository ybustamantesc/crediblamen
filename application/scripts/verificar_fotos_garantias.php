<?php
// Script de verificación de fotos de garantías para una solicitud
// Uso: Coloca este archivo en application/scripts y ejecútalo desde el navegador o CLI

$solicitud_id = isset($_GET['solicitud_id']) ? intval($_GET['solicitud_id']) : 1;

// Cargar entorno CodeIgniter
require_once(dirname(__DIR__).'/config/database.php');
require_once(dirname(__DIR__).'/core/CodeIgniter.php');

$CI =& get_instance();
$CI->load->database();

// 1. Verificar registros en la tabla tb_garantias_fotos
$fotos = $CI->db->where('solicitud_id', $solicitud_id)->get('tb_garantias_fotos')->result();
echo "<h2>Fotos en la base de datos para solicitud #$solicitud_id</h2>";
if (empty($fotos)) {
    echo "<p style='color:red;'>No hay registros en tb_garantias_fotos para esta solicitud.</p>";
} else {
    echo "<ul>";
    foreach ($fotos as $f) {
        echo "<li>Garantía ID: {$f->garantia_id} - Archivo: {$f->filename} ";
        $ruta = FCPATH . ltrim($f->filename, '/\\');
        if (file_exists($ruta)) {
            echo "<span style='color:green;'>[OK: Archivo existe]</span>";
        } else {
            echo "<span style='color:red;'>[ERROR: Archivo NO existe]</span>";
        }
        echo "</li>";
    }
    echo "</ul>";
}

// 2. Verificar archivos en la carpeta
$carpeta = FCPATH . 'uploads/garantias/solicitud_' . $solicitud_id . '/';
echo "<h2>Archivos en la carpeta uploads/garantias/solicitud_$solicitud_id/</h2>";
if (!is_dir($carpeta)) {
    echo "<p style='color:red;'>La carpeta no existe.</p>";
} else {
    $archivos = scandir($carpeta);
    $archivos = array_diff($archivos, array('.', '..'));
    if (empty($archivos)) {
        echo "<p style='color:red;'>No hay archivos en la carpeta.</p>";
    } else {
        echo "<ul>";
        foreach ($archivos as $a) {
            echo "<li>$a</li>";
        }
        echo "</ul>";
    }
}
