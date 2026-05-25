<?php
// Instalar/cargar library para crear documentos Word
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Pt;
use PhpOffice\PhpWord\Shared\RGBColor;

$phpWord = new PhpWord();

// Configurar estilos
$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(11);

// Agregar encabezado
$section = $phpWord->addSection();
$header = $section->addHeader();
$header->addText('INFORME DE AVANCES', ['size' => 14, 'bold' => true]);

// Título principal
$section->addTitle('INFORME DE AVANCES', 1);
$section->addText('Módulo de Crédito - Sistema CrediBlamen', ['size' => 11, 'italic' => true]);
$section->addText('Período: Semana 18-22 de Mayo, 2026', ['size' => 11, 'italic' => true]);
$section->addTextBreak();

// Resumen ejecutivo
$section->addTitle('1. RESUMEN EJECUTIVO', 2);
$section->addText('Durante el período reportado, se han completado de manera satisfactoria las actividades de validación, corrección de errores y optimización del Módulo de Crédito. Se han implementado 34 correcciones y mejoras que fortalecen la funcionalidad del sistema y mejoran la experiencia del usuario. Asimismo, se ha completado exitosamente la unión de bases de datos y se ha iniciado la integración del Módulo de Tesorería.');
$section->addTextBreak();

// Componente: Formulario de Solicitud Inicial
$section->addTitle('2. COMPONENTE: FORMULARIO DE SOLICITUD INICIAL', 2);

$section->addTitle('2.1 Validaciones Implementadas', 3);

$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
$table->addRow();
$table->addCell(2000)->addText('Validación', ['bold' => true]);
$table->addCell(3000)->addText('Descripción', ['bold' => true]);
$table->addCell(1500)->addText('Estado', ['bold' => true]);

$validaciones = [
    ['Plazo de Crédito', 'Sistema valida que el plazo esté entre 10 y 18 meses. Rechaza automáticamente valores fuera de rango.', '✓ Implementado'],
    ['Selección de Garantía', 'Las opciones de garantía están limitadas a una única selección según la necesidad del cliente.', '✓ Implementado'],
    ['Cuota Estimada', 'El formulario no se puede guardar sin procesar la cuota estimada previamente. Sistema notifica la acción requerida.', '✓ Implementado'],
    ['Ventas Promedio Mensual', 'Campo de cálculo obligatorio antes de guardar.', '✓ Implementado'],
    ['Campos de Teléfono', 'Solo se aceptan caracteres numéricos y el símbolo (+) para números internacionales.', '✓ Implementado'],
    ['Número de Dependientes', 'Validación que impide valores negativos. Campo acepta 0 o números positivos.', '✓ Implementado'],
    ['Cédula de Cónyuge', 'Comparte las mismas validaciones que el campo de cédula de identidad principal.', '✓ Implementado'],
    ['Días Buenos y Malos', 'Impide seleccionar el mismo día en ambas categorías. El monto de días buenos debe ser mayor al de días malos.', '✓ Implementado'],
    ['Otros Ingresos - Carga de Fotos', 'Permite subir imágenes sin necesidad de completar primero la solicitud.', '✓ Implementado'],
];

foreach ($validaciones as $val) {
    $table->addRow();
    $table->addCell(2000)->addText($val[0]);
    $table->addCell(3000)->addText($val[1]);
    $table->addCell(1500)->addText($val[2]);
}

$section->addTextBreak();

$section->addTitle('2.2 Formulario Uso de Crédito', 3);
$section->addListItem('Identificación del Solicitante: El número de cédula ahora aparece en el texto de Declaración y Autorización.', 0);
$section->addListItem('Evaluador de Crédito: Se pueden ingresar y modificar los datos del evaluador y fecha de evaluación, que se guardan correctamente en base de datos.', 0);
$section->addTextBreak();

// Problemas identificados y resueltos
$section->addTitle('3. PROBLEMAS IDENTIFICADOS Y RESUELTOS', 2);

$section->addTitle('3.1 Gestión de Archivos y Fotos en Solicitudes', 3);
$section->addTitle('Problema identificado:', 4);
$section->addListItem('Inconsistencia al subir archivos en nuevas solicitudes', 0);
$section->addListItem('Si existía evidencia previa en la carpeta, copiaba fotos del directorio', 0);
$section->addListItem('En primera subida sin solicitud previa, no guardaba las fotos', 0);
$section->addTitle('Solución implementada:', 4);
$section->addListItem('Las fotos se suben desde el momento de creación de la solicitud', 0);
$section->addListItem('Sistema consulta directamente la base de datos en caso de coincidencia de IDs', 0);
$section->addListItem('Eliminación y recarga de fotos sin problemas', 0);
$section->addTextBreak();

$section->addTitle('3.2 Normalización del Nombre del Cliente', 3);
$section->addTitle('Problema identificado:', 4);
$section->addListItem('El nombre se guardaba de formas distintas en diferentes partes del código', 0);
$section->addListItem('Aparecía desordenado (apellido primero)', 0);
$section->addListItem('Inconsistencia entre referencias como "Nombre Completo" y otras variantes', 0);
$section->addTitle('Solución implementada:', 4);
$section->addListItem('Implementación de campos separados para nombre y apellidos', 0);
$section->addListItem('Normalización de la gestión de cadenas de texto', 0);
$section->addListItem('Consistencia en formularios y tablas de visualización', 0);
$section->addTextBreak();

$section->addTitle('3.3 Inconsistencias en Garantías', 3);
$section->addTitle('Problema identificado:', 4);
$section->addListItem('Garantías distintas entre el formulario y el PDF generado', 0);
$section->addListItem('Tipos de garantía no coincidían entre vistas', 0);
$section->addTitle('Solución implementada:', 4);
$section->addListItem('Sincronización correcta del listado de tipos de garantía', 0);
$section->addListItem('Validación que garantiza que tipos como "Mobiliaria" y "Sin garantía" se muestren correctamente en PDF', 0);
$section->addTextBreak();

$section->addTitle('3.4 Generación de Documentos PDF', 3);
$section->addText('Solicitudes PDF:', ['bold' => true]);
$section->addListItem('Ahora guarda correctamente campos de "Firma de Solicitante" y "Fecha de Firma"', 0);
$section->addText('Uso de Crédito PDF:', ['bold' => true]);
$section->addListItem('Se genera con información completa: número de identificación, firma del solicitante y fechas', 0);
$section->addText('Verificación de Garantía PDF:', ['bold' => true]);
$section->addListItem('Documento con formato profesional que cumple con estética, logos y tipografía corporativa', 0);
$section->addTextBreak();

$section->addTitle('3.5 Validaciones Adicionales en Solicitudes', 3);
$table2 = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
$table2->addRow();
$table2->addCell(3000)->addText('Problema', ['bold' => true]);
$table2->addCell(3500)->addText('Resolución', ['bold' => true]);

$problemas = [
    ['Monto de ingreso de crédito del cónyuge no se guardaba al editar', 'Ahora aparece correctamente en formulario y PDF'],
    ['Datos de Formulario de Uso de Crédito no persistían', 'Se guardan correctamente en base de datos'],
    ['Datos desordenados en Referencias', 'Validación y organización implementada'],
    ['Nombres desordenados en verificación de garantía', 'Referencia correcta implementada en formulario y PDF'],
];

foreach ($problemas as $prob) {
    $table2->addRow();
    $table2->addCell(3000)->addText($prob[0]);
    $table2->addCell(3500)->addText($prob[1]);
}

$section->addTextBreak();

// Mejoras en funcionalidades
$section->addTitle('4. MEJORAS EN FUNCIONALIDADES DEL SISTEMA', 2);

$section->addTitle('4.1 Perfil Integral del Cliente (PIC)', 3);
$section->addTitle('Problemas resueltos:', 4);
$section->addListItem('Campos separados para nombres y apellidos (antes esperaba 4 cadenas fijas)', 0);
$section->addListItem('Soluciona problemas con personas de un solo nombre, nombres compuestos o apellidos simples', 0);
$section->addListItem('Aplicado también a datos del cónyuge', 0);
$section->addListItem('PDF genera correctamente con datos del cliente y cónyuge', 0);
$section->addListItem('Referencia de "Código" cambiada a "ID de Solicitud" en vistas', 0);
$section->addTextBreak();

$section->addTitle('4.2 Gestión de Documentos y Archivos', 3);
$section->addTitle('Nuevo módulo implementado:', 4);
$section->addListItem('Apartado dedicado para visualizar archivos y fotos subidas en solicitudes', 0);
$section->addListItem('Documentos categorizados por tipo', 0);
$section->addListItem('Opción de "Ver Documentos" en menú de acciones', 0);
$section->addTextBreak();

$section->addTitle('4.3 Optimizaciones de Interfaz', 3);
$section->addListItem('Orden de tablas ajustado: de más reciente a más antiguo', 0);
$section->addListItem('Diseño responsive completamente funcional', 0);
$section->addListItem('Paginación de tablas implementada y optimizada', 0);
$section->addTextBreak();

$section->addTitle('4.4 Datos Comerciales del Cliente', 3);
$table3 = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
$table3->addRow();
$table3->addCell(2500)->addText('Campo', ['bold' => true]);
$table3->addCell(4000)->addText('Estado', ['bold' => true]);

$datos = [
    ['Cuota Estimada', '✓ Se carga correctamente en formulario'],
    ['Ventas al Crédito', '✓ Se guarda y edita correctamente'],
    ['Propiedad del Negocio', '✓ Validación: no aparece innecesariamente en PDF'],
];

foreach ($datos as $dato) {
    $table3->addRow();
    $table3->addCell(2500)->addText($dato[0]);
    $table3->addCell(4000)->addText($dato[1]);
}

$section->addTextBreak();

// Infraestructura
$section->addTitle('5. INFRAESTRUCTURA Y BASES DE DATOS', 2);

$section->addTitle('5.1 Unión de Bases de Datos', 3);
$section->addText('Se ha completado exitosamente la integración de bases de datos, permitiendo:', ['bold' => true]);
$section->addListItem('Estructuras nuevas y coherentes', 0);
$section->addListItem('Adición de tablas y columnas de datos faltantes', 0);
$section->addListItem('Importación de datos sin conflictos', 0);
$section->addListItem('Preparación para futuras extensiones', 0);
$section->addText('Resultado: ANTES: Bases de datos separadas → DESPUÉS: Sistema unificado y sincronizado');
$section->addTextBreak();

$section->addTitle('5.2 Integración del Módulo de Tesorería', 3);
$section->addText('Se ha iniciado la primera versión del Módulo de Tesorería en conjunto con los avances del Módulo de Crédito, preparando la infraestructura para:', ['bold' => true]);
$section->addListItem('Gestión financiera integrada', 0);
$section->addListItem('Reportes consolidados', 0);
$section->addListItem('Control de flujo de caja', 0);
$section->addTextBreak();

// Indicadores
$section->addTitle('6. INDICADORES DE CUMPLIMIENTO', 2);
$table4 = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
$table4->addRow();
$table4->addCell(3000)->addText('Indicador', ['bold' => true]);
$table4->addCell(3500)->addText('Valor', ['bold' => true]);

$indicadores = [
    ['Validaciones implementadas', '9'],
    ['Problemas identificados y resueltos', '25'],
    ['Componentes optimizados', '8'],
    ['Documentos generados correctamente', '3'],
    ['Porcentaje de cumplimiento', '100%'],
];

foreach ($indicadores as $ind) {
    $table4->addRow();
    $table4->addCell(3000)->addText($ind[0]);
    $table4->addCell(3500)->addText($ind[1]);
}

$section->addTextBreak();

// Conclusiones
$section->addTitle('7. CONCLUSIONES Y PRÓXIMAS ACTIVIDADES', 2);

$section->addTitle('Logros Alcanzados:', 3);
$section->addListItem('Sistema de validación robusto y consistente', 0);
$section->addListItem('Generación de documentos PDF completa y profesional', 0);
$section->addListItem('Normalización de gestión de datos de cliente', 0);
$section->addListItem('Infraestructura de base de datos unificada', 0);
$section->addListItem('Interfaz mejorada y responsive', 0);

$section->addTitle('Próximas Actividades:', 3);
$section->addListItem('Pruebas exhaustivas de integración con Módulo de Tesorería', 0);
$section->addListItem('Validación de reportes consolidados', 0);
$section->addListItem('Optimización de rendimiento en tablas grandes', 0);
$section->addListItem('Documentación técnica de cambios implementados', 0);
$section->addListItem('Capacitación de usuarios en nuevas funcionalidades', 0);

$section->addTextBreak();
$section->addTextBreak();

// Pie de página
$footer = $section->addFooter();
$footer->addText('Fecha de Reporte: 22 de Mayo, 2026 | Estado: En Curso ✓ | Calidad: Conforme a especificaciones', ['size' => 9, 'italic' => true]);

// Guardar archivo
$filename = __DIR__ . '/Informe_Avances_CrediBlamen_18-22_Mayo.docx';
$phpWord->save($filename);

echo "Documento generado exitosamente: " . $filename;
?>
