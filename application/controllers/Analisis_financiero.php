<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analisis_financiero extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Analisis_financiero_asalariado_model', 'afa_model');
        $this->load->model('Analisis_financiero_comerciante_model', 'afc_model');
        $this->load->model('Core_model', 'core_model');
        $this->load->model('Garantia_model', 'garantia_model');
        $this->load->library('session');
        $this->load->library('ion_auth');
        // if (!$this->ion_auth->logged_in()) {
        //     redirect('login');
        // }
    }

    private function _infer_aprob_status_from_comment($comment)
    {
        $txt = strtolower((string)$comment);
        if (strpos($txt, 'anul') !== false) return 'annulled';
        if (strpos($txt, 'rechaz') !== false) return 'rejected';
        if (strpos($txt, 'aprob') !== false) return 'approved';
        return 'pending';
    }

    // Endpoint para obtener datos guardados de asalariado (AJAX)
    public function get_asalariado($idsolicitud) {
        $analisis = $this->afa_model->get_by_solicitud($idsolicitud);
        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
        $analisis = $analisis ? (array)$analisis : [];
        $solicitud = $solicitud ? (array)$solicitud : [];
        $analisis = $this->_normalizar_campos_obligaciones($analisis);
        // Lista de campos que pueden mapearse desde la solicitud inicial (incluye campos de montos para análisis financiero)
        $campos_map = [
            'apellidos', 'nombres', 'direccion', 'telefono', 'estado_civil', 'fecha_nacimiento', 'numero_dependientes',
            'nombre_empresa', 'direccion_empresa', 'telefono_empresa', 'cargo_puesto', 'ingreso_mensual_neto', 'deducciones',
            'nombre_conyuge', 'dni_conyuge', 'ocupacion_conyuge', 'telefono_conyuge',
            // Campos de montos para análisis financiero
            'ventas_promedio_mensual', 'cuentas_por_cobrar', 'caja_efectivo', 'banco', 'ventas_al_credito',
            'energia_electrica', 'agua_potable', 'internet_telefonia', 'gastos_personales', 'gastos_transporte', 'pago_trabajadores',
        ];
        foreach ($campos_map as $campo) {
            if (!isset($analisis[$campo]) && isset($solicitud[$campo])) {
                $analisis[$campo] = $solicitud[$campo];
            }
        }

        // Calcular Cobertura de Garantía = (Total Garantía $ / Monto Solicitado $) * 100
        $cobertura_garantia = $this->_calcular_cobertura_garantia($idsolicitud, $solicitud);
        $analisis['cobertura_garantia'] = $cobertura_garantia;

        if (!empty($analisis)) {
            echo json_encode(['status' => true, 'data' => $analisis]);
        } else {
            echo json_encode(['status' => false, 'msg' => 'No hay datos']);
        }
    }

    // Endpoint para obtener datos guardados de comerciante (AJAX)
    public function get_comerciante($idsolicitud) {
        $analisis = $this->afc_model->get_by_solicitud($idsolicitud);
        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
        $analisis = $analisis ? (array)$analisis : [];
        $solicitud = $solicitud ? (array)$solicitud : [];
        $analisis = $this->_normalizar_campos_obligaciones($analisis);
        // Lista de campos que pueden mapearse desde la solicitud inicial (incluye campos de montos para análisis financiero)
        $campos_map = [
            'apellidos', 'nombres', 'direccion', 'telefono', 'estado_civil', 'fecha_nacimiento', 'numero_dependientes',
            'nombre_empresa', 'direccion_empresa', 'telefono_empresa', 'cargo_puesto', 'ingreso_mensual_neto', 'deducciones',
            'nombre_conyuge', 'dni_conyuge', 'ocupacion_conyuge', 'telefono_conyuge',
            // Campos de montos para análisis financiero
            'ventas_promedio_mensual', 'cuentas_por_cobrar', 'caja_efectivo', 'banco', 'ventas_al_credito',
            'energia_electrica', 'agua_potable', 'internet_telefonia', 'gastos_personales', 'gastos_transporte', 'pago_trabajadores',
        ];
        foreach ($campos_map as $campo) {
            if (!isset($analisis[$campo]) && isset($solicitud[$campo])) {
                $analisis[$campo] = $solicitud[$campo];
            }
        }

        // Compatibilidad de nombre de campo en BD: cuota_periodica_estim -> cuota_periodica
        if (!isset($analisis['cuota_periodica']) && isset($analisis['cuota_periodica_estim'])) {
            $analisis['cuota_periodica'] = $analisis['cuota_periodica_estim'];
        }

        // Sugerencia: Promedio de cuota estimada mensual (US$) * tipo de cambio.
        $tipo_cambio = 36.6243;
        $cuota_usd = 0;
        if (isset($solicitud['cuota_estimado']) && is_numeric($solicitud['cuota_estimado'])) {
            $cuota_usd = (float)$solicitud['cuota_estimado'];
        } elseif (isset($solicitud['cuota_estim_estimada']) && is_numeric($solicitud['cuota_estim_estimada'])) {
            $cuota_usd = (float)$solicitud['cuota_estim_estimada'];
        }
        $cuota_sugerida_cs = round($cuota_usd * $tipo_cambio, 2);
        $analisis['cuota_periodica_sugerida'] = $cuota_sugerida_cs;

        // Si no hay valor guardado, usar sugerido por defecto.
        if ((!isset($analisis['cuota_periodica']) || $analisis['cuota_periodica'] === '' || $analisis['cuota_periodica'] === null) && $cuota_sugerida_cs > 0) {
            $analisis['cuota_periodica'] = $cuota_sugerida_cs;
        }

        // Calcular Cobertura de Garantía = (Total Garantía $ / Monto Solicitado $) * 100
        $cobertura_garantia = $this->_calcular_cobertura_garantia($idsolicitud, $solicitud);
        $analisis['cobertura_garantia'] = $cobertura_garantia;

        if (!empty($analisis)) {
            echo json_encode(['status' => true, 'data' => $analisis]);
        } else {
            echo json_encode(['status' => false, 'msg' => 'No hay datos']);
        }
    }
    // Descargar PDF de análisis asalariado
    public function descargar_pdf_asalariado($idsolicitud)
    {
        $this->load->model('Analisis_financiero_asalariado_model', 'afa_model');
        $analisis = $this->afa_model->get_by_solicitud($idsolicitud);
        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
        $solicitud = $solicitud ? (array)$solicitud : array();
        // Si no hay datos, mostrar todos los campos vacíos
        if (!$analisis) {
            // Lista de campos de la tabla asalariado
            $campos = [
                'ingreso_sueldo_neto','ingreso_comisiones','ingreso_bonificaciones','ingreso_remesas','ingreso_otros','total_ingresos','sueldo','inss','ir','sueldo_neto_calc','gastos_alimentacion','gastos_servicios','gastos_vestuario','gastos_educativos','gastos_transporte','gastos_alquiler','pago_empleado_viatico','entretenimiento','otros_gastos','total_gastos_familiares','cuotas_prestamos','pension_alimenticia','otras_obligaciones','total_otras_obligaciones','total_egresos','flujo_neto_mensual','cuota_periodica','canasta_basica','cantidad_promedio','monto_por_persona','personas_dependientes','gastos_alimentacion_canasta','transporte_urbano','transporte_individual','transporte_interurbano','recorrido_laboral','vehiculo_particular','total_transporte','alquiler','casa_propia','total_gastos_vivienda','cobertura_deuda','cobertura_garantia','tc_acumulado','p_entretenimiento','created_at','updated_at'
            ];
            $analisis = array();
            foreach ($campos as $c) $analisis[$c] = '';
        } else {
            $analisis = (array)$analisis;
        }

        // Asegurar nombre del cliente en el PDF
        if (empty($analisis['nombres']) && isset($solicitud['nombres'])) {
            $analisis['nombres'] = $solicitud['nombres'];
        }
        if (empty($analisis['apellidos']) && isset($solicitud['apellidos'])) {
            $analisis['apellidos'] = $solicitud['apellidos'];
        }

        // Forzar cobertura de garantía calculada al momento de generar PDF
        // (Total garantía USD / Monto solicitado USD) * 100
        $analisis['cobertura_garantia'] = $this->_calcular_cobertura_garantia($idsolicitud, $solicitud);

        $data = ['analisis' => $analisis];
        $html = $this->load->view('analisis_financiero/pdf_asalariado', $data, true);
        $this->_generar_pdf($html, 'analisis_asalariado_' . $idsolicitud);
    }

    // Descargar PDF de análisis comerciante
    public function descargar_pdf_comerciante($idsolicitud)
    {
        $this->load->model('Analisis_financiero_comerciante_model', 'afc_model');
        $analisis = $this->afc_model->get_by_solicitud($idsolicitud);
        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
        $solicitud = $solicitud ? (array)$solicitud : array();
        if (!$analisis) {
            // Lista de campos de la tabla comerciante (ajustar si hay más campos)
            $campos = [
                'ingreso_sueldo_neto','ingreso_comisiones','ingreso_bonificaciones','ingreso_remesas','ingreso_otros','total_ingresos','sueldo','inss','ir','sueldo_neto_calc','gastos_alimentacion','gastos_servicios','gastos_vestuario','gastos_educativos','gastos_transporte','gastos_alquiler','pago_empleado_viatico','entretenimiento','otros_gastos','total_gastos_familiares','cuotas_prestamos','pension_alimenticia','otras_obligaciones','total_otras_obligaciones','total_egresos','flujo_neto_mensual','cuota_periodica','canasta_basica','cantidad_promedio','monto_por_persona','personas_dependientes','gastos_alimentacion_canasta','transporte_urbano','transporte_individual','transporte_interurbano','recorrido_laboral','vehiculo_particular','alquiler','casa_propia','cobertura_deuda','cobertura_garantia','tc_acumulado','p_entretenimiento','created_at','updated_at'
            ];
            $analisis = array();
            foreach ($campos as $c) $analisis[$c] = '';
        } else {
            $analisis = (array)$analisis;
        }

        if (empty($analisis['nombres']) && isset($solicitud['nombres'])) {
            $analisis['nombres'] = $solicitud['nombres'];
        }
        if (empty($analisis['apellidos']) && isset($solicitud['apellidos'])) {
            $analisis['apellidos'] = $solicitud['apellidos'];
        }
        $data = ['analisis' => $analisis];
        $html = $this->load->view('analisis_financiero/pdf_comerciante', $data, true);
        $this->_generar_pdf($html, 'analisis_comerciante_' . $idsolicitud);
    }

    // Utilidad interna para generar PDF usando Dompdf
    private function _generar_pdf($html, $filename = 'documento')
    {
        if (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
        $dompfPath = FCPATH . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
        if (!file_exists($dompfPath)) {
            show_error('Dompdf no encontrado en ' . $dompfPath . '. Coloca la librería dompdf en la raíz del proyecto.', 500);
            return;
        }
        require_once $dompfPath;
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf = $dompdf->output();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function _normalizar_campos_obligaciones($analisis)
    {
        $grupos = [
            ['olp_fecha', 'olp_cuota', 'olp_instituciones', 'olp_saldo'],
            ['ocp_fecha', 'ocp_cuota', 'ocp_instituciones', 'ocp_saldo'],
            ['asal_olp_fecha', 'asal_olp_cuota', 'asal_olp_instituciones', 'asal_olp_saldo'],
        ];

        foreach ($grupos as $grupo) {
            $filas = [];
            $columnas = [];
            $max = 0;

            foreach ($grupo as $campo) {
                $columnas[$campo] = $this->_decode_json_array(isset($analisis[$campo]) ? $analisis[$campo] : []);
                $max = max($max, count($columnas[$campo]));
            }

            for ($i = 0; $i < $max; $i++) {
                $fila = [];
                $vacia = true;
                foreach ($grupo as $campo) {
                    $valor = isset($columnas[$campo][$i]) ? $columnas[$campo][$i] : '';
                    $fila[$campo] = $valor;
                    if (trim((string)$valor) !== '') {
                        $vacia = false;
                    }
                }

                if (!$vacia) {
                    $filas[] = $fila;
                }
            }

            foreach ($grupo as $campo) {
                $analisis[$campo] = array_column($filas, $campo);
            }
        }

        return $analisis;
    }

    private function _decode_json_array($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [$value];
        }

        return [];
    }

    // Guardar análisis financiero asalariado (AJAX)
    public function guardar_asalariado() {
        $this->load->model('Analisis_financiero_asalariado_model', 'afa_model');
        $data = $this->input->post();
        if (empty($data['idsolicitud'])) {
            echo json_encode(['status' => false, 'msg' => 'Solicitud no especificada']);
            return;
        }
        if (isset($data['tipo'])) {
            unset($data['tipo']);
        }
        foreach ($data as $k => $v) {
            if ($k === 'tipo') {
                continue;
            }
            if (is_string($v)) {
                // Normalizar valores numéricos con formato de texto como "12,5 %" o "C$ 1,234.50"
                $clean = str_replace(array('C$', '%', ' '), '', $v);
                $clean = str_replace(',', '', $clean);
                if (is_numeric($clean)) {
                    $data[$k] = (float)$clean;
                }
            } elseif (is_numeric($v)) {
                $data[$k] = (float)$v;
            }
        }

        // Normalizar cobertura_deuda si viene formateada con porcentaje o texto.
        if (isset($data['cobertura_deuda'])) {
            $data['cobertura_deuda'] = $this->_to_float($data['cobertura_deuda']);
        }

        // Si los totales de transporte o vivienda no se recibieron, recalcularlos desde sus campos base.
        if (!isset($data['total_transporte']) || $data['total_transporte'] === null) {
            $total_transporte = 0;
            $total_transporte += $this->_to_float(isset($data['transporte_urbano']) ? $data['transporte_urbano'] : 0);
            $total_transporte += $this->_to_float(isset($data['transporte_individual']) ? $data['transporte_individual'] : 0);
            $total_transporte += $this->_to_float(isset($data['transporte_interurbano']) ? $data['transporte_interurbano'] : 0);
            $total_transporte += $this->_to_float(isset($data['recorrido_laboral']) ? $data['recorrido_laboral'] : 0);
            $total_transporte += $this->_to_float(isset($data['vehiculo_particular']) ? $data['vehiculo_particular'] : 0);
            $data['total_transporte'] = $total_transporte;
        }

        if (!isset($data['total_gastos_vivienda']) || $data['total_gastos_vivienda'] === null) {
            $data['total_gastos_vivienda'] = $this->_to_float(isset($data['alquiler']) ? $data['alquiler'] : 0)
                + $this->_to_float(isset($data['casa_propia']) ? $data['casa_propia'] : 0);
        }

        // Calcular cobertura_deuda desde flujo neto mensual y cuota periódica cuando hay datos disponibles.
        $flujo_neto = $this->_to_float(isset($data['flujo_neto_mensual']) ? $data['flujo_neto_mensual'] : 0);
        $cuota = $this->_to_float(isset($data['cuota_periodica']) ? $data['cuota_periodica'] : 0);
        if ($cuota > 0) {
            $data['cobertura_deuda'] = round($flujo_neto / $cuota, 4);
        } elseif (!isset($data['cobertura_deuda']) || !is_numeric($data['cobertura_deuda'])) {
            $data['cobertura_deuda'] = 0;
        }

        // Validación de negocio: cuota periódica no puede ser mayor al flujo neto mensual.
        $cuota_periodica = $this->_to_float(isset($data['cuota_periodica']) ? $data['cuota_periodica'] : 0);
        $flujo_neto = $this->_to_float(isset($data['flujo_neto_mensual']) ? $data['flujo_neto_mensual'] : 0);
        if ($cuota_periodica > 0 && $flujo_neto > 0 && $cuota_periodica > $flujo_neto) {
            echo json_encode(['status' => false, 'msg' => 'La Cuota Periódica no puede ser mayor que el Flujo Neto Mensual Disponible.']);
            return;
        }

        $existe = $this->afa_model->get_by_solicitud($data['idsolicitud']);
        if ($existe) {
            $data['id'] = $existe->id;
        }
        $id = $this->afa_model->save($data);
        echo json_encode(['status' => true, 'id' => $id]);
    }

    // Guardar análisis financiero comerciante (AJAX)
    public function guardar_comerciante() {
        $this->load->model('Analisis_financiero_comerciante_model', 'afc_model');
        $data = $this->input->post();
        // Log payload for debugging: temporary
        log_message('debug', 'guardar_comerciante payload: ' . var_export($data, true));
        // Asegurar idsolicitud
        if (empty($data['idsolicitud'])) {
            echo json_encode(['status' => false, 'msg' => 'Solicitud no especificada']);
            return;
        }
        if (isset($data['tipo'])) {
            unset($data['tipo']);
        }
        // Limpiar y mapear campos numéricos
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $clean = str_replace(array('C$', '%', ' '), '', $v);
                $clean = str_replace(',', '', $clean);
                if (is_numeric($clean)) {
                    $data[$k] = (float)$clean;
                }
            } elseif (is_numeric($v)) {
                $data[$k] = (float)$v;
            }
        }

        // Validación de negocio: cuota periódica no puede ser mayor al flujo neto disponible.
        // Fallback a flujo_neto_mensual para evitar bloqueos por diferencias de nombre de campo.
        $cuota_periodica = $this->_to_float(isset($data['cuota_periodica']) ? $data['cuota_periodica'] : 0);
        $flujo_neto = $this->_to_float(isset($data['flujo_neto_disponible']) ? $data['flujo_neto_disponible'] : 0);
        if ($flujo_neto <= 0) {
            $flujo_neto = $this->_to_float(isset($data['flujo_neto_mensual']) ? $data['flujo_neto_mensual'] : 0);
        }
        if ($cuota_periodica > 0 && $flujo_neto > 0 && $cuota_periodica > $flujo_neto) {
            echo json_encode(['status' => false, 'msg' => 'La Cuota Periódica no puede ser mayor que el Flujo Neto Disponible.']);
            return;
        }

        // Compatibilidad con esquema existente en algunas BD.
        if (isset($data['cuota_periodica']) && !isset($data['cuota_periodica_estim'])) {
            $data['cuota_periodica_estim'] = $data['cuota_periodica'];
        }
        // Guardar (insertar o actualizar si ya existe para la solicitud)
        $existe = $this->afc_model->get_by_solicitud($data['idsolicitud']);
        if ($existe) {
            $data['id'] = $existe->id;
        }
        $id = $this->afc_model->save($data);
        echo json_encode(['status' => true, 'id' => $id]);
    }

    // Función auxiliar para calcular Cobertura de Garantía
    // Cobertura de Garantía (%) = (Total Garantía en $ / Monto Solicitado $) * 100
    private function _calcular_cobertura_garantia($idsolicitud, $solicitud = array())
    {
        if (empty($idsolicitud)) {
            return 0;
        }

        // Obtener todas las garantías de la solicitud
        $garantias = $this->garantia_model->get_all_by_solicitud($idsolicitud);
        
        // Calcular total en córdobas: SUM(cantidad * costo)
        $total_garantias_cordobas = 0;
        if (!empty($garantias)) {
            foreach ($garantias as $g) {
                $cantidad = isset($g->cantidad) ? (float)$g->cantidad : 0;
                $costo = isset($g->costo) ? (float)$g->costo : 0;
                $total_garantias_cordobas += ($cantidad * $costo);
            }
        }

        // Convertir a dólares (tasa por defecto 36.5)
        $tasa_cambio = 36.5;
        $total_garantias_dolares = $total_garantias_cordobas / $tasa_cambio;

        // Obtener monto solicitado en dólares
        $monto_solicitado = 0;
        if (!empty($solicitud) && isset($solicitud['monto_solicitado'])) {
            $monto_solicitado = (float)$solicitud['monto_solicitado'];
        }

        // Calcular cobertura: (Total Garantía $ / Monto Solicitado $) * 100
        $cobertura_porcentaje = 0;
        if ($monto_solicitado > 0) {
            $cobertura_porcentaje = round(($total_garantias_dolares / $monto_solicitado) * 100, 2);
        }

        return $cobertura_porcentaje;
    }

    // Normaliza números en texto (ej. "11,205.00", "C$ 200", "44.6 %") a float.
    private function _to_float($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        $normalized = str_replace(array('C$', '%', ' '), '', (string)$value);
        $normalized = str_replace(',', '', $normalized);
        return is_numeric($normalized) ? (float)$normalized : 0;
    }

    // Vista principal: muestra todas las solicitudes iniciales
    public function index()
    {
        // Filtros por estado y fecha
        $estado = $this->input->get('estado');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $this->db->select('tb_solicitudes.*, COALESCE(tb_asesores.nombres, "") as nombre_asesor');
        $this->db->from('tb_solicitudes');
        $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
        $this->db->order_by('tb_solicitudes.idsolicitud', 'DESC');
        $solicitudes = $this->db->get()->result();
        $rows_afa = $this->db->select('idsolicitud')->from('tb_analisis_financiero_asalariado')->get()->result();
        $rows_afc = $this->db->select('idsolicitud')->from('tb_analisis_financiero_comerciante')->get()->result();
        $analisis_completados = array();
        foreach ($rows_afa as $r) {
            $analisis_completados[(int)$r->idsolicitud] = true;
        }
        foreach ($rows_afc as $r) {
            $analisis_completados[(int)$r->idsolicitud] = true;
        }

        $result = array();
        foreach ($solicitudes as $s) {
            $status = isset($analisis_completados[(int)$s->idsolicitud]) ? 'completed' : 'pending';

            $estado_sol = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
            if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                $status = 'annulled';
            } else {
                $latest = $this->db
                    ->where('idsolicitud', $s->idsolicitud)
                    ->order_by('created_at', 'DESC')
                    ->order_by('idaprobacion', 'DESC')
                    ->limit(1)
                    ->get('tb_solicitud_aprobaciones')
                    ->row();
                if ($latest) {
                    $status_aprob = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    if ($status_aprob === 'annulled') {
                        $status = 'annulled';
                    }
                }
            }

            $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $s->idsolicitud));
            if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                $status = 'annulled';
            }

            $s->aprob_status = $status;
            // Filtro por estado
            if ($estado && $estado != 'all' && $status != $estado) continue;
            // Filtro por fecha
            $fecha = isset($s->fecha_solicitud) ? $s->fecha_solicitud : (isset($s->fecha_recepcion) ? $s->fecha_recepcion : '');
            $fecha_solo = $fecha ? date('Y-m-d', strtotime($fecha)) : '';
            if ($start_date && $fecha_solo < $start_date) continue;
            if ($end_date && $fecha_solo > $end_date) continue;
            $result[] = $s;
        }
        $data = array(
            'titulo' => 'Análisis Financiero',
            'subtitulo' => 'Definir tipo de análisis para solicitudes iniciales',
            'icono' => 'fa fa-file-alt',
            'solicitudes' => $result,
            'filtro_estado' => $estado ? $estado : 'all',
            'filtro_start_date' => $start_date,
            'filtro_end_date' => $end_date
        );
        $this->load->view('layout/header', $data);
        $this->load->view('analisis_financiero/index', $data);
        $this->load->view('layout/footer');
    }
}
