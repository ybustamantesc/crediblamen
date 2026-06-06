<?php
defined('BASEPATH') or exit('Acción no permitida');
class Solicitudes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Cargar librerías y modelos necesarios
            // Cargar librerías y modelos correctamente
            $this->load->model('Core_model', 'core_model');
            $this->load->library('form_validation');
            $this->load->library('session');
            $this->load->library('pdf');
        // input y output son parte de CI_Controller, no requieren carga explícita
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
    }

    /**
     * Ensure the $sol object has alias properties expected by views/PDF templates.
     * This normalizes different column names across environments into consistent names.
     */
    private function _expose_pdf_aliases(&$sol)
    {
        if (!$sol) return;
        try {
            // Sales amounts
            if (!isset($sol->ventas_en_dias_buenos)) {
                if (isset($sol->ventas_dias_buenos)) $sol->ventas_en_dias_buenos = $sol->ventas_dias_buenos;
                elseif (isset($sol->ventas_buenos_amount)) $sol->ventas_en_dias_buenos = $sol->ventas_buenos_amount;
                else $sol->ventas_en_dias_buenos = 0;
            }
            if (!isset($sol->ventas_en_dias_malos)) {
                if (isset($sol->ventas_dias_malos)) $sol->ventas_en_dias_malos = $sol->ventas_dias_malos;
                elseif (isset($sol->ventas_malos_amount)) $sol->ventas_en_dias_malos = $sol->ventas_malos_amount;
                else $sol->ventas_en_dias_malos = 0;
            }

            // Otros ingresos
            if (!isset($sol->otros_ingresos_1_monto) && isset($sol->otros_ingresos_1_amount)) $sol->otros_ingresos_1_monto = $sol->otros_ingresos_1_amount;
            if (!isset($sol->otros_ingresos_1_margen) && isset($sol->otros_ingresos_1_margin)) $sol->otros_ingresos_1_margen = $sol->otros_ingresos_1_margin;
            if (!isset($sol->otros_ingresos_2_monto) && isset($sol->otros_ingresos_2_amount)) $sol->otros_ingresos_2_monto = $sol->otros_ingresos_2_amount;
            if (!isset($sol->otros_ingresos_2_margen) && isset($sol->otros_ingresos_2_margin)) $sol->otros_ingresos_2_margen = $sol->otros_ingresos_2_margin;
            if (!isset($sol->otros_ingresos_3_monto) && isset($sol->otros_ingresos_3_amount)) $sol->otros_ingresos_3_monto = $sol->otros_ingresos_3_amount;
            if (!isset($sol->otros_ingresos_3_margen) && isset($sol->otros_ingresos_3_margin)) $sol->otros_ingresos_3_margen = $sol->otros_ingresos_3_margin;

            // Financial structure
            if (!isset($sol->cuentas_por_cobrar) && isset($sol->cuentas_por_cobrar_amount)) $sol->cuentas_por_cobrar = $sol->cuentas_por_cobrar_amount;
            if (!isset($sol->caja_efectivo) && isset($sol->caja_amount)) $sol->caja_efectivo = $sol->caja_amount;
            if (!isset($sol->saldo_banco) && isset($sol->banco_amount)) $sol->saldo_banco = $sol->banco_amount;

            // Gastos
            if (!isset($sol->gasto_alquiler) && isset($sol->pago_alquiler)) $sol->gasto_alquiler = $sol->pago_alquiler;
            if (!isset($sol->gasto_trabajadores) && isset($sol->pago_trabajadores)) $sol->gasto_trabajadores = $sol->pago_trabajadores;
            if (!isset($sol->gasto_energia)) {
                if (isset($sol->energia_electrica)) $sol->gasto_energia = $sol->energia_electrica;
                elseif (isset($sol->energia)) $sol->gasto_energia = $sol->energia;
                else $sol->gasto_energia = 0;
            }
            if (!isset($sol->gasto_agua)) {
                if (isset($sol->agua_potable)) $sol->gasto_agua = $sol->agua_potable;
                elseif (isset($sol->agua)) $sol->gasto_agua = $sol->agua;
                else $sol->gasto_agua = 0;
            }
            if (!isset($sol->gasto_internet)) {
                if (isset($sol->internet_telefonia)) $sol->gasto_internet = $sol->internet_telefonia;
                elseif (isset($sol->internet)) $sol->gasto_internet = $sol->internet;
                else $sol->gasto_internet = 0;
            }
        } catch (Exception $e) { /* ignore */ }
    }

    private function _garantia_has($values, $needle)
    {
        foreach ($values as $value) {
            if (stripos($value, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Expose boolean flags for garantia and tipo_contrato and prepare product lines for PDF
     */
    private function _expose_pdf_flags_and_products(&$sol, $propuestas = array())
    {
        if (!$sol) return;
        try {
            // Parse garantia string into flags
            if (isset($sol->garantia) && is_string($sol->garantia) && trim($sol->garantia) !== '') {
                $g = array_map('trim', explode(',', $sol->garantia));
                $sol->garantia_hipotecaria = ($this->_garantia_has($g, 'Hipotecaria') ? 1 : 0);
                $sol->garantia_mobiliaria = ($this->_garantia_has($g, 'Mobiliaria') ? 1 : 0);
                $sol->garantia_sin = ($this->_garantia_has($g, 'Sin') ? 1 : 0);
                $sol->garantia_prendaria = ($this->_garantia_has($g, 'Prendaria') ? 1 : 0);
                $sol->garantia_fiador = ($this->_garantia_has($g, 'Fiador') ? 1 : 0);
                $sol->garantia_otra = ($this->_garantia_has($g, 'Otra') ? 1 : 0);
            } else {
                $sol->garantia_hipotecaria = isset($sol->garantia_hipotecaria) ? (int)$sol->garantia_hipotecaria : 0;
                $sol->garantia_mobiliaria = isset($sol->garantia_mobiliaria) ? (int)$sol->garantia_mobiliaria : 0;
                $sol->garantia_sin = isset($sol->garantia_sin) ? (int)$sol->garantia_sin : 0;
                $sol->garantia_prendaria = isset($sol->garantia_prendaria) ? (int)$sol->garantia_prendaria : 0;
                $sol->garantia_fiador = isset($sol->garantia_fiador) ? (int)$sol->garantia_fiador : 0;
                $sol->garantia_otra = isset($sol->garantia_otra) ? (int)$sol->garantia_otra : 0;
            }

            // Parse tipo_contrato string into flags
            if (isset($sol->tipo_contrato) && is_string($sol->tipo_contrato) && trim($sol->tipo_contrato) !== '') {
                $tc = array_map('trim', explode(',', $sol->tipo_contrato));
                $sol->tipo_contrato_permanente = (in_array('Permanente', $tc) ? 1 : 0);
                $sol->tipo_contrato_temporal = (in_array('Temporal', $tc) ? 1 : 0);
                $sol->tipo_contrato_otro = (in_array('Otro', $tc) ? 1 : 0);
            } else {
                $sol->tipo_contrato_permanente = isset($sol->tipo_contrato_permanente) ? (int)$sol->tipo_contrato_permanente : 0;
                $sol->tipo_contrato_temporal = isset($sol->tipo_contrato_temporal) ? (int)$sol->tipo_contrato_temporal : 0;
                $sol->tipo_contrato_otro = isset($sol->tipo_contrato_otro) ? (int)$sol->tipo_contrato_otro : 0;
            }

            // Prepare product lines array for the PDF
            $sol->producto_lines = array();
            if (!empty($propuestas) && is_array($propuestas)) {
                foreach ($propuestas as $p) {
                    $parts = array();
                    if (isset($p->nombre)) $parts[] = $p->nombre;
                    if (isset($p->clasificacion)) $parts[] = '(' . $p->clasificacion . ')';
                    // tasa/comision/plazo if present
                    $meta = array();
                    if (isset($p->tasa_mensual)) $meta[] = 'Tasa: ' . rtrim(rtrim(number_format((float)$p->tasa_mensual * ( ($p->tasa_mensual>1)?1:100), 2, '.', ''), '0'), '.');
                    elseif (isset($p->tasa)) $meta[] = 'Tasa: ' . $p->tasa;
                    if (isset($p->comision_desembolso)) $meta[] = 'Comisión: ' . rtrim(rtrim(number_format((float)$p->comision_desembolso * ( ($p->comision_desembolso>1)?1:100), 2, '.', ''), '0'), '.');
                    if (isset($p->plazo_max)) $meta[] = 'Plazo: ' . $p->plazo_max;
                    if (!empty($meta)) $parts[] = '[' . implode(' | ', $meta) . ']';
                    $line = implode(' ', $parts);
                    $sol->producto_lines[] = $line;
                }
            }
        } catch (Exception $e) { /* ignore */ }
    }


    public function index()
    {
        // support optional date filters via GET: start_date, end_date (YYYY-MM-DD)
        $start_date = trim((string)$this->input->get('start_date'));
        $end_date = trim((string)$this->input->get('end_date'));

        $data = array(
            'titulo' => 'Solicitud Inicial de Crédito',
            'subtitulo' => 'Registrar solicitudes iniciales de crédito',
            'icono' => 'fas fa-file-signature',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
                'plugins/select2/dist/css/select2.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/select2/dist/js/select2.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'solicitudes' => array(),
            'filter_start_date' => $start_date,
            'filter_end_date' => $end_date
        );

        // Determine date field to use for ordering (prefer recepción, then solicitud)
        $date_field = 'fecha_recepcion';
        if (! $this->db->field_exists($date_field, 'tb_solicitudes')) {
            $date_field = $this->db->field_exists('fecha_solicitud', 'tb_solicitudes') ? 'fecha_solicitud' : 'idsolicitud';
        }

        // Fetch solicitudes, optionally filtered by date range if provided
        $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres,""), "") as nombre_asesor');
        $this->db->from('tb_solicitudes');
        $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
        if ($start_date) $this->db->where($date_field . ' >=', $start_date);
        if ($end_date) $this->db->where($date_field . ' <=', $end_date);
        // Default ordering: newest first by record id
        $this->db->order_by('idsolicitud', 'DESC');
        $data['solicitudes'] = $this->db->get()->result();

        // Determine approval status for each solicitud: pending|approved|rejected|annulled
        foreach ($data['solicitudes'] as $s) {
            $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $s->idsolicitud));
            if (empty($aprs)) {
                $estado = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                $s->aprob_status = (($estado === 'anulado' || $estado === 'annulled') ? 'annulled' : 'pending');
            } else {
                // sort by created_at desc and take latest
                usort($aprs, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                $latest = $aprs[0];
                $s->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
            }

            $estado = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
            if ($estado === 'anulado' || $estado === 'annulled') {
                $s->aprob_status = 'annulled';
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('solicitudes/index', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Callback validator for numero_doc (Cédula)
     * Ensures pattern: 13 digits followed by a letter (example: 0000000000000X)
     */
    public function _validate_numero_doc($str)
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', (string)$str);
        if ($clean === '') {
            $this->form_validation->set_message('_validate_numero_doc', 'El campo %s es requerido.');
            return false;
        }
        // default pattern: 13 digits + 1 letter
        if (!preg_match('/^\d{13}[A-Za-z]$/', $clean)) {
            $this->form_validation->set_message('_validate_numero_doc', 'Cédula inválida o incompleta. Use 13 dígitos y una letra final (sin guiones).');
            return false;
        }
        return true;
    }

    private function _infer_aprob_status_from_comment($comment)
    {
        $txt = strtolower((string)$comment);
        if (strpos($txt, 'anul') !== false) {
            return 'annulled';
        }
        if (strpos($txt, 'rechaz') !== false) {
            return 'rejected';
        }
        if (strpos($txt, 'aprob') !== false) {
            return 'approved';
        }
        return 'pending';
    }

    private function _is_solicitud_annulled($idsolicitud)
    {
        $idsolicitud = (int)$idsolicitud;
        if ($idsolicitud <= 0) return false;

        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
        if ($sol && isset($sol->estado_aprobacion)) {
            $estado = strtolower((string)$sol->estado_aprobacion);
            if ($estado === 'anulado' || $estado === 'annulled') {
                return true;
            }
        }

        $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $idsolicitud));
        if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
            return true;
        }

        return false;
    }


    public function core($cliente_id = NULL)
    {
        if ($cliente_id && $this->_is_solicitud_annulled($cliente_id)) {
            $this->session->set_flashdata('error', 'La solicitud está anulada y no puede editarse.');
            redirect('solicitudes');
            return;
        }
        // Simplified, robust implementation of core() with Word-mode support.
        $use_word_template = true;
        $word_allowed_fields = array(
            'apellidos','nombres','nombre_completo','numero_doc','tipo_documento','fecha_nacimiento','fecha_solicitud','edad','estado_civil','sexo','telefono','direccion',
            'tipo_credito','rubro_credito','destino_credito',
            'giro_negocio','monto_solicitado','plazo_meses','frecuencia','tasa_interes','cuota_estim_estimada','garantia','es_rural','comision_desembolso',
            // note: product helper fields are written into existing fields (eg. propuesta_tipos)
            // do NOT attempt to persist product_* columns unless DB has them
            'ventas_promedio_diarios','ventas_promedio_mensual','ventas_dias_buenos','ventas_dias_malos','ventas_dias_buenos_mask','ventas_dias_malos_mask','ventas_al_credito','margen_comercial',
            'detalle_inventario','nombre_negocio','actividad_economica','ubicacion_negocio','numero_empleados','cuentas_por_cobrar_amount','cuentas_por_cobrar_evidencia','caja_amount','banco_amount','monto_total_inventario',
            'pago_alquiler','pago_trabajadores','energia','agua','internet','gastos_fijos','gastos_operativos','otros_gastos','gastos_personales','gastos_transporte','otros_ingresos_detalle',
            // fields matching view names (ensure posted keys pass whitelist)
            'energia_electrica','agua_potable','internet_telefonia','declaro_verificacion','firma_solicitante','fecha_firma','ddc_investigacion_campo','nombre_promotor','fecha_recepcion_solicitud',
            // business / shop fields (Section 3)
            'telefono_negocio','tiempo_operacion_anios','tiempo_operacion_meses','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual','ventas_buenos_days','ventas_malos_days','margen_comercial',
            'otros_ingresos_1_amount','otros_ingresos_1_margin','otros_ingresos_1_detalle','otros_ingresos_2_amount','otros_ingresos_2_margin','otros_ingresos_2_detalle','otros_ingresos_3_amount','otros_ingresos_3_margin','otros_ingresos_3_detalle',
            // employment / employer fields (Section 2)
            'nombre_empresa','direccion_empresa','telefono_empresa','cargo_puesto','tiempo_empleo_anios','tiempo_empleo_meses','tipo_contrato','ingreso_mensual_neto','deducciones',
            // personal / household fields (section 1)
            'nombre_conyuge','dni_conyuge','ocupacion_conyuge','ingresos_conyuge','telefono_conyuge','numero_dependientes','tiempo_residir_anios','tiempo_residir_meses','condicion_vivienda',
            'promotor','fecha_recepcion','observaciones','observaciones_promotor','datos_personales','datos_conyuge','propuesta_tipos','es_nuevo','es_renovacion','edit_comment'
        );
        $view_word_mode = $use_word_template;

        // Helper closures used below
        $normalize_numeric = function (&$data, $fields) {
            foreach ($fields as $f) {
                if (!array_key_exists($f, $data)) continue;
                $val = $data[$f];
                if (is_string($val)) $val = str_replace(',', '', $val);
                if ($val === '' || $val === null) {
                    $data[$f] = null;
                } elseif (is_numeric($val)) {
                    $data[$f] = (strpos((string)$val, '.') !== false) ? (float)$val : (int)$val;
                } else {
                    $data[$f] = null;
                }
            }
        };

        $normalize_dates = function (&$data, $fields) {
            foreach ($fields as $dt) {
                if (!array_key_exists($dt, $data)) continue;
                $v = trim((string)$data[$dt]);
                if ($v === '' || $v === '0000-00-00' || $v === null) {
                    $data[$dt] = null;
                    continue;
                }
                $ts = strtotime($v);
                if ($ts === false) {
                    $data[$dt] = null;
                } else {
                    $data[$dt] = ($dt === 'fecha_nacimiento') ? date('Y-m-d', $ts) : (preg_match('/T|:/', $v) ? date('Y-m-d H:i:s', $ts) : date('Y-m-d', $ts));
                }
            }
        };

        // POST/create path
        if (!$cliente_id) {
            // support single nombre_completo field by splitting into nombres/apellidos
            if ($this->input->post('nombre_completo')) {
                $nc = trim($this->input->post('nombre_completo'));
                if ($nc !== '') {
                    $parts = preg_split('/\s+/', $nc);
                    if (count($parts) === 1) {
                        $_POST['nombres'] = $parts[0];
                        $_POST['apellidos'] = '';
                    } else {
                        $last = array_pop($parts);
                        $_POST['apellidos'] = $last;
                        $_POST['nombres'] = implode(' ', $parts);
                    }
                }
            }

            // Validation (minimal in Word-mode)
            if ($use_word_template) {
                $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
                $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
                $this->form_validation->set_rules('monto_solicitado', 'Monto Solicitado', 'trim|required');
                $this->form_validation->set_rules('plazo_meses', 'Plazo', 'trim|required');
                $this->form_validation->set_rules('frecuencia', 'Frecuencia', 'trim|required');
                // Validate numero_doc format: 13 digits followed by a letter (configurable in view)
                $this->form_validation->set_rules('numero_doc', 'Cédula de identidad', 'trim|required|callback__validate_numero_doc');
            } else {
                $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
                $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
                $this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|min_length[5]|max_length[100]');
                $this->form_validation->set_rules('numero_doc', 'Cédula de identidad', 'trim|required|callback__validate_numero_doc');
            }

            if ($this->form_validation->run()) {
                // Accept only whitelist in Word-mode
                $data = ($use_word_template) ? elements($word_allowed_fields, $this->input->post()) : $this->input->post();
                // Si no viene nombre_promotor, usar el usuario logueado
                if (empty($data['nombre_promotor'])) {
                    if ($this->ion_auth->logged_in()) {
                        $u = $this->ion_auth->user()->row();
                        if ($u) {
                            $nombre = trim($u->first_name . ' ' . $u->last_name);
                            $data['nombre_promotor'] = $nombre !== '' ? $nombre : $u->username;
                        }
                    }
                }
                // Preserve the cuentas_por_cobrar_amount value for save
                if ($this->input->post('cuentas_por_cobrar_amount') !== null) {
                    $raw = trim((string)$this->input->post('cuentas_por_cobrar_amount'));
                    $data['cuentas_por_cobrar_amount'] = ($raw === '' ? null : $raw);
                    if (!$this->db->field_exists('cuentas_por_cobrar_amount', 'tb_solicitudes') && $this->db->field_exists('cuentas_por_cobrar', 'tb_solicitudes')) {
                        $data['cuentas_por_cobrar'] = $data['cuentas_por_cobrar_amount'];
                    }
                }
                // Guardar idasesor (Ruta/Asesor) si viene del formulario
                if ($this->input->post('idasesor') !== null) {
                    $raw_idasesor = trim($this->input->post('idasesor'));
                    if ($raw_idasesor !== '') {
                        $data['idasesor'] = is_numeric($raw_idasesor) ? (int)$raw_idasesor : null;
                    }
                }

                // Map visible form names to DB column names and normalize posted checkbox groups
                // cuota_estimado (form) -> cuota_estim_estimada (DB)
                if ($this->input->post('cuota_estimado') !== null) {
                    $raw = trim($this->input->post('cuota_estimado'));
                    if ($raw !== '') {
                        $data['cuota_estim_estimada'] = is_numeric($raw) ? floatval(str_replace(',', '', $raw)) : $raw;
                    }
                }
                // cuota_estimado_quincenal (form) -> cuota_estim_estimada_quincenal (DB)
                if ($this->input->post('cuota_estimado_quincenal') !== null) {
                    $rawq = trim($this->input->post('cuota_estimado_quincenal'));
                    if ($rawq !== '') {
                        $data['cuota_estim_estimada_quincenal'] = is_numeric($rawq) ? floatval(str_replace(',', '', $rawq)) : $rawq;
                    }
                }

                // Ensure comision_desembolso is stored as decimal (0.07) when user enters 7
                if (array_key_exists('comision_desembolso', $data)) {
                    $raw = trim((string)$data['comision_desembolso']);
                    if ($raw !== '' && is_numeric($raw)) {
                        $val = floatval(str_replace(',', '.', $raw));
                        if ($val > 1) { $data['comision_desembolso'] = $val / 100.0; }
                        else { $data['comision_desembolso'] = $val; }
                    }
                }

                // Merge garantia_* checkboxes into single `garantia` column (comma-separated)
                $gar_list = array();
                if ($this->input->post('garantia_hipotecaria')) $gar_list[] = 'Hipotecaria';
                if ($this->input->post('garantia_mobiliaria')) $gar_list[] = 'Mobiliaria';
                if ($this->input->post('garantia_sin')) $gar_list[] = 'Sin garantía';
                if ($this->input->post('garantia_prendaria')) $gar_list[] = 'Prendaria';
                if ($this->input->post('garantia_fiador')) $gar_list[] = 'Fiador';
                if ($this->input->post('garantia_otra')) $gar_list[] = 'Otra';
                if (count($gar_list) > 0) {
                    $data['garantia'] = implode(', ', $gar_list);
                } else {
                    // keep existing textual garantia if provided, otherwise null
                    $data['garantia'] = (isset($data['garantia']) && trim($data['garantia']) !== '') ? $data['garantia'] : null;
                }

                // Merge tipo_contrato checkboxes into single `tipo_contrato` column
                $tc = array();
                if ($this->input->post('tipo_contrato_permanente')) $tc[] = 'Permanente';
                if ($this->input->post('tipo_contrato_temporal')) $tc[] = 'Temporal';
                if ($this->input->post('tipo_contrato_otro')) $tc[] = 'Otro';
                if (count($tc) > 0) {
                    $data['tipo_contrato'] = implode(', ', $tc);
                } else {
                    $data['tipo_contrato'] = (isset($data['tipo_contrato']) && trim($data['tipo_contrato']) !== '') ? $data['tipo_contrato'] : null;
                }

                // If product hidden fields were submitted, map them to the stored fields
                if ($this->input->post('producto_tasa') !== null && trim((string)$this->input->post('producto_tasa')) !== '') {
                    $pt = trim((string)$this->input->post('producto_tasa'));
                    if (is_numeric($pt)) $data['tasa_interes'] = floatval(str_replace(',', '.', $pt));
                }
                if ($this->input->post('producto_comision') !== null && trim((string)$this->input->post('producto_comision')) !== '') {
                    $pc = trim((string)$this->input->post('producto_comision'));
                    if (is_numeric($pc)) $data['comision_desembolso'] = floatval(str_replace(',', '.', $pc));
                }
                // NO sobrescribir plazo_meses con producto_plazo si el usuario ya ingresó un plazo
                // Solo usar producto_plazo si plazo_meses está vacío
                if ($this->input->post('producto_plazo') !== null && trim((string)$this->input->post('producto_plazo')) !== '') {
                    $pp = trim((string)$this->input->post('producto_plazo'));
                    // Solo sobrescribir si plazo_meses NO fue enviado o está vacío
                    $user_plazo = $this->input->post('plazo_meses');
                    if (is_numeric($pp) && ($user_plazo === null || trim((string)$user_plazo) === '')) {
                        $data['plazo_meses'] = intval($pp);
                    }
                }

                // Normalize header checkboxes from HTML (checkbox sends 'on' when checked)
                if (array_key_exists('es_nuevo', $data)) {
                    $v = $data['es_nuevo'];
                    $data['es_nuevo'] = (($v === 'on' || $v === '1' || $v === 1 || $v === true) ? 1 : 0);
                }
                if (array_key_exists('es_renovacion', $data)) {
                    $v = $data['es_renovacion'];
                    $data['es_renovacion'] = (($v === 'on' || $v === '1' || $v === 1 || $v === true) ? 1 : 0);
                }
                if (array_key_exists('es_rural', $data)) {
                    $v = strtolower(trim((string)$data['es_rural']));
                    $data['es_rural'] = (($v === '1' || $v === 'si' || $v === 'sí' || $v === 'on' || $v === 'true') ? 1 : 0);
                }

                // Map ventas_buenos_amount / ventas_malos_amount (form) -> ventas_dias_buenos / ventas_dias_malos (DB)
                if ($this->input->post('ventas_buenos_amount') !== null) {
                    $vb = trim((string)$this->input->post('ventas_buenos_amount'));
                    if ($vb === '') { $data['ventas_dias_buenos'] = null; }
                    elseif (is_numeric($vb)) { $data['ventas_dias_buenos'] = floatval(str_replace(',', '.', $vb)); }
                }
                if ($this->input->post('ventas_malos_amount') !== null) {
                    $vm = trim((string)$this->input->post('ventas_malos_amount'));
                    if ($vm === '') { $data['ventas_dias_malos'] = null; }
                    elseif (is_numeric($vm)) { $data['ventas_dias_malos'] = floatval(str_replace(',', '.', $vm)); }
                }

                // normalize
                $numeric_fields = array(
                    'monto_solicitado', 'plazo_meses', 'tasa_interes', 'cuota_estim_estimada', 'comision_desembolso',
                    'ventas_promedio_diarios', 'ventas_promedio_mensual', 'ventas_dias_buenos', 'ventas_dias_malos', 'ventas_buenos_amount', 'ventas_malos_amount', 'ventas_promedio_mensual', 'ventas_al_credito', 'margen_comercial',
                    'ventas_dias_buenos_mask', 'ventas_dias_malos_mask', 'margen_comercial', 'numero_empleados', 'numero_dependientes', 'edad',
                    'tiempo_residir_anios','tiempo_residir_meses', 'tiempo_empleo_anios','tiempo_empleo_meses','ingreso_mensual_neto','ingresos_conyuge', 'tiempo_operacion_anios','tiempo_operacion_meses',
                    'cuentas_por_cobrar_amount', 'caja_amount', 'banco_amount', 'monto_total_inventario', 'pago_alquiler', 'pago_trabajadores',
                        'energia', 'agua', 'internet', 'gastos_fijos', 'gastos_operativos', 'gastos_personales', 'gastos_transporte',
                        // accept numeric inputs coming from the view names as well
                        'energia_electrica','agua_potable','internet_telefonia'
                );
                // Also normalize optional "otros_ingresos_*" fields which are decimals
                $otros_ing = array(
                    'otros_ingresos_1_amount','otros_ingresos_1_margin',
                    'otros_ingresos_2_amount','otros_ingresos_2_margin',
                    'otros_ingresos_3_amount','otros_ingresos_3_margin'
                );
                $numeric_fields = array_merge($numeric_fields, $otros_ing);
                $normalize_numeric($data, $numeric_fields);
                $normalize_dates($data, array('fecha_nacimiento', 'fecha_recepcion', 'fecha_firma', 'fecha_recepcion_solicitud', 'fecha_solicitud'));

                // sales day masks
                $mask_b = 0;
                $vb = $this->input->post('ventas_buenos_days');
                if (is_array($vb)) foreach ($vb as $ix) { $i = (int)$ix; if ($i >= 0 && $i <= 6) $mask_b |= (1 << $i); }
                $data['ventas_dias_buenos_mask'] = ($mask_b > 0 ? $mask_b : null);
                $mask_m = 0;
                $vm = $this->input->post('ventas_malos_days');
                if (is_array($vm)) foreach ($vm as $ix) { $i = (int)$ix; if ($i >= 0 && $i <= 6) $mask_m |= (1 << $i); }
                $data['ventas_dias_malos_mask'] = ($mask_m > 0 ? $mask_m : null);

                // map tipo_documento -> tipo_doc (legacy)
                if (array_key_exists('tipo_documento', $data)) {
                    $td = $data['tipo_documento'];
                    $data['tipo_doc'] = ($td === 'Cedula') ? 0 : (($td === 'Pasaporte') ? 2 : 3);
                } else {
                    $data['tipo_doc'] = isset($data['tipo_doc']) ? $data['tipo_doc'] : null;
                }

                // debug log: inspect product-related and commission fields before insert
                try { log_message('debug', '[SOLICITUDS][CREATE] producto_tasa=' . var_export($this->input->post('producto_tasa'), true) . ' producto_comision=' . var_export($this->input->post('producto_comision'), true) . ' producto_plazo=' . var_export($this->input->post('producto_plazo'), true) . ' mapped_comision=' . var_export(isset($data['comision_desembolso']) ? $data['comision_desembolso'] : null, true)); } catch (Exception $e) {}

                // Remove any keys that are not actual columns in tb_solicitudes to avoid SQL errors
                foreach (array_keys($data) as $k) {
                    if (!$this->db->field_exists($k, 'tb_solicitudes')) {
                        unset($data[$k]);
                    }
                }

                // Coerce non-numeric values to NULL for integer columns in the DB (prevents INSERT errors)
                $cols = $this->db->field_data('tb_solicitudes');
                $colTypes = array();
                foreach ($cols as $c) { $colTypes[$c->name] = strtolower($c->type); }
                foreach ($data as $k => $v) {
                    if (!isset($colTypes[$k])) continue;
                    $t = $colTypes[$k];
                    if (strpos($t, 'int') !== false || strpos($t, 'tinyint') !== false || strpos($t, 'smallint') !== false || strpos($t, 'mediumint') !== false) {
                        if ($v === '' || $v === null) { $data[$k] = null; }
                        elseif (!is_numeric($v)) { $data[$k] = null; }
                        else { $data[$k] = (int)$v; }
                    }
                }

                // Ensure any remaining empty string values are converted to NULL to avoid strict SQL errors
                foreach ($data as $k => $v) {
                    if ($v === '') { $data[$k] = null; }
                }

                // (intentionally left blank) Keep NULL values for empty fields to avoid altering business semantics.
                $insert_id = $this->core_model->insert('tb_solicitudes', $data, TRUE);
                if ($insert_id) {
                    // Create or update cliente record from solicitud data
                    $client_id = null;
                    try {
                        $cliente_data = array();
                        // prefer sanitized $data values (these are filtered to DB columns)
                        $cliente_data['apellidos'] = isset($data['apellidos']) ? $data['apellidos'] : $this->input->post('apellidos');
                        $cliente_data['nombres'] = isset($data['nombres']) ? $data['nombres'] : $this->input->post('nombres');
                        $cliente_data['numero_doc'] = isset($data['numero_doc']) ? $data['numero_doc'] : trim((string)$this->input->post('numero_doc'));
                        // tipo_doc: try sanitized data, then posted tipo_documento, otherwise default to 0 (Cedula)
                        if (isset($data['tipo_doc'])) {
                            $cliente_data['tipo_doc'] = $data['tipo_doc'];
                        } else {
                            $td = $this->input->post('tipo_documento');
                            if ($td !== null) {
                                $cliente_data['tipo_doc'] = ($td === 'Cedula' ? 0 : ($td === 'Pasaporte' ? 2 : 3));
                            } else {
                                $cliente_data['tipo_doc'] = 0;
                            }
                        }
                        $cliente_data['fecha_nacimiento'] = isset($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : $this->input->post('fecha_nacimiento');
                        $cliente_data['edad'] = isset($data['edad']) ? $data['edad'] : $this->input->post('edad');
                        $cliente_data['estado_civil'] = isset($data['estado_civil']) ? $data['estado_civil'] : $this->input->post('estado_civil');
                        $cliente_data['nombre_conyuge'] = isset($data['nombre_conyuge']) ? $data['nombre_conyuge'] : $this->input->post('nombre_conyuge');
                        $cliente_data['dni_conyuge'] = isset($data['dni_conyuge']) ? $data['dni_conyuge'] : $this->input->post('dni_conyuge');
                        $cliente_data['ocupacion_conyuge'] = isset($data['ocupacion_conyuge']) ? $data['ocupacion_conyuge'] : $this->input->post('ocupacion_conyuge');
                        $cliente_data['telefono_conyuge'] = isset($data['telefono_conyuge']) ? $data['telefono_conyuge'] : $this->input->post('telefono_conyuge');
                        $cliente_data['numero_dependientes'] = isset($data['numero_dependientes']) ? $data['numero_dependientes'] : $this->input->post('numero_dependientes');
                        $cliente_data['telefono'] = isset($data['telefono']) ? $data['telefono'] : $this->input->post('telefono');
                        $cliente_data['direccion'] = isset($data['direccion']) ? $data['direccion'] : $this->input->post('direccion');
                        $cliente_data['condicion_vivienda'] = isset($data['condicion_vivienda']) ? $data['condicion_vivienda'] : $this->input->post('condicion_vivienda');
                        $cliente_data['tiempo_residir_anios'] = isset($data['tiempo_residir_anios']) ? $data['tiempo_residir_anios'] : $this->input->post('tiempo_residir_anios');
                        $cliente_data['tiempo_residir_meses'] = isset($data['tiempo_residir_meses']) ? $data['tiempo_residir_meses'] : $this->input->post('tiempo_residir_meses');

                        // Empleo / empresa
                        $cliente_data['nombre_empresa'] = isset($data['nombre_empresa']) ? $data['nombre_empresa'] : $this->input->post('nombre_empresa');
                        $cliente_data['direccion_empresa'] = isset($data['direccion_empresa']) ? $data['direccion_empresa'] : $this->input->post('direccion_empresa');
                        $cliente_data['telefono_empresa'] = isset($data['telefono_empresa']) ? $data['telefono_empresa'] : $this->input->post('telefono_empresa');
                        $cliente_data['cargo_puesto'] = isset($data['cargo_puesto']) ? $data['cargo_puesto'] : $this->input->post('cargo_puesto');
                        $cliente_data['tiempo_empleo_anios'] = isset($data['tiempo_empleo_anios']) ? $data['tiempo_empleo_anios'] : $this->input->post('tiempo_empleo_anios');
                        $cliente_data['tiempo_empleo_meses'] = isset($data['tiempo_empleo_meses']) ? $data['tiempo_empleo_meses'] : $this->input->post('tiempo_empleo_meses');
                        $cliente_data['tipo_contrato'] = isset($data['tipo_contrato']) ? $data['tipo_contrato'] : (isset($data['tipo_contrato_permanente']) || $this->input->post('tipo_contrato_permanente') ? 'Permanente' : ($this->input->post('tipo_contrato_temporal') ? 'Temporal' : null));
                        $cliente_data['ingreso_mensual_neto'] = isset($data['ingreso_mensual_neto']) ? $data['ingreso_mensual_neto'] : $this->input->post('ingreso_mensual_neto');
                        $cliente_data['deducciones'] = isset($data['deducciones']) ? $data['deducciones'] : $this->input->post('deducciones');

                        // Negocio / comercio
                        $cliente_data['nombre_negocio'] = isset($data['nombre_negocio']) ? $data['nombre_negocio'] : $this->input->post('nombre_negocio');
                        $cliente_data['actividad_economica'] = isset($data['actividad_economica']) ? $data['actividad_economica'] : $this->input->post('actividad_economica');
                        $cliente_data['telefono_negocio'] = isset($data['telefono_negocio']) ? $data['telefono_negocio'] : $this->input->post('telefono_negocio');
                        $cliente_data['tiempo_operacion_anios'] = isset($data['tiempo_operacion_anios']) ? $data['tiempo_operacion_anios'] : $this->input->post('tiempo_operacion_anios');
                        $cliente_data['tiempo_operacion_meses'] = isset($data['tiempo_operacion_meses']) ? $data['tiempo_operacion_meses'] : $this->input->post('tiempo_operacion_meses');

                        // Ventas
                        $cliente_data['ventas_buenos_amount'] = isset($data['ventas_buenos_amount']) ? $data['ventas_buenos_amount'] : $this->input->post('ventas_buenos_amount');
                        $cliente_data['ventas_malos_amount'] = isset($data['ventas_malos_amount']) ? $data['ventas_malos_amount'] : $this->input->post('ventas_malos_amount');
                        $cliente_data['ventas_promedio_mensual'] = isset($data['ventas_promedio_mensual']) ? $data['ventas_promedio_mensual'] : $this->input->post('ventas_promedio_mensual');

                        if (!empty($cliente_data['numero_doc'])) {
                            // Normalize cliente_data values: convert empty strings to NULL, normalize dates and numeric types
                            foreach ($cliente_data as $k => $v) {
                                if ($v === '') {
                                    $cliente_data[$k] = null;
                                }
                            }

                            // fecha_nacimiento: normalize to Y-m-d or NULL
                            if (isset($cliente_data['fecha_nacimiento']) && $cliente_data['fecha_nacimiento'] !== null) {
                                $fd = trim((string)$cliente_data['fecha_nacimiento']);
                                if ($fd === '' || $fd === '0000-00-00') {
                                    $cliente_data['fecha_nacimiento'] = null;
                                } else {
                                    $ts = strtotime($fd);
                                    if ($ts === false) {
                                        $cliente_data['fecha_nacimiento'] = null;
                                    } else {
                                        $cliente_data['fecha_nacimiento'] = date('Y-m-d', $ts);
                                    }
                                }
                            }

                            // integer fields
                            $intFields = array('edad','numero_dependientes','tiempo_residir_anios','tiempo_residir_meses','tiempo_empleo_anios','tiempo_empleo_meses','tiempo_operacion_anios','tiempo_operacion_meses');
                            foreach ($intFields as $if) {
                                if (isset($cliente_data[$if])) {
                                    $val = $cliente_data[$if];
                                    if ($val === null || $val === '' || !is_numeric($val)) {
                                        $cliente_data[$if] = null;
                                    } else {
                                        $cliente_data[$if] = (int)$val;
                                    }
                                }
                            }

                            // decimal fields
                            $floatFields = array('ingreso_mensual_neto','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual');
                            foreach ($floatFields as $ff) {
                                if (isset($cliente_data[$ff])) {
                                    $val = $cliente_data[$ff];
                                    if ($val === null || $val === '' || !is_numeric(str_replace(',','.',(string)$val))) {
                                        $cliente_data[$ff] = null;
                                    } else {
                                        $cliente_data[$ff] = floatval(str_replace(',','.',(string)$val));
                                    }
                                }
                            }

                            // ensure tipo_doc is an int when present
                            if (isset($cliente_data['tipo_doc'])) {
                                if ($cliente_data['tipo_doc'] === null || $cliente_data['tipo_doc'] === '') {
                                    $cliente_data['tipo_doc'] = 0;
                                } else {
                                    $cliente_data['tipo_doc'] = is_numeric($cliente_data['tipo_doc']) ? (int)$cliente_data['tipo_doc'] : 0;
                                }
                            }
                            $existing = $this->core_model->get_by_id('tb_clientes', array('numero_doc' => $cliente_data['numero_doc']));
                            if ($existing) {
                                // Update only non-empty fields so we don't wipe existing data with empty POSTs
                                $upd = array();
                                $fields = array(
                                    'apellidos','nombres','tipo_doc','fecha_nacimiento','edad','estado_civil','nombre_conyuge','dni_conyuge','ocupacion_conyuge','telefono_conyuge','numero_dependientes',
                                    'telefono','direccion','condicion_vivienda','tiempo_residir_anios','tiempo_residir_meses',
                                    'nombre_empresa','direccion_empresa','telefono_empresa','cargo_puesto','tiempo_empleo_anios','tiempo_empleo_meses','tipo_contrato','ingreso_mensual_neto','deducciones',
                                    'nombre_negocio','actividad_economica','telefono_negocio','tiempo_operacion_anios','tiempo_operacion_meses',
                                    'ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual'
                                );
                                foreach ($fields as $f) {
                                    if (isset($cliente_data[$f]) && $cliente_data[$f] !== null && $cliente_data[$f] !== '') {
                                        $upd[$f] = $cliente_data[$f];
                                    }
                                }
                                if (!empty($upd)) {
                                    $this->core_model->update('tb_clientes', $upd, array('idcliente' => $existing->idcliente));
                                }
                                $client_id = $existing->idcliente;
                            } else {
                                // Insert new cliente record
                                $ins = array();
                                $fieldsIns = array(
                                    'apellidos','nombres','tipo_doc','fecha_nacimiento','edad','estado_civil','nombre_conyuge','dni_conyuge','ocupacion_conyuge','telefono_conyuge','numero_dependientes',
                                    'telefono','direccion','condicion_vivienda','tiempo_residir_anios','tiempo_residir_meses',
                                    'nombre_empresa','direccion_empresa','telefono_empresa','cargo_puesto','tiempo_empleo_anios','tiempo_empleo_meses','tipo_contrato','ingreso_mensual_neto','deducciones',
                                    'nombre_negocio','actividad_economica','telefono_negocio','tiempo_operacion_anios','tiempo_operacion_meses',
                                    'ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual','numero_doc'
                                );
                                foreach ($fieldsIns as $f) {
                                    if (isset($cliente_data[$f])) $ins[$f] = $cliente_data[$f];
                                }
                                if (!empty($ins)) {
                                    $client_id = $this->core_model->insert('tb_clientes', $ins, TRUE);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Non-fatal: log and continue
                        log_message('error','[SOLICITUDS] Error creando/actualizando cliente: ' . $e->getMessage());
                    }

                    // Process any uploaded files that were submitted together with the create form
                    try {
                        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
                        $max_bytes = 5 * 1024 * 1024; // 5MB
                        $allow_pdf_groups = array('docs_generales', 'docs_legales');
                        
                        // Handle single file upload for cuentas_por_cobrar_evidencia
                        if (isset($_FILES['cuentas_por_cobrar_evidencia']) && $_FILES['cuentas_por_cobrar_evidencia']['error'] === UPLOAD_ERR_OK) {
                            $file = $_FILES['cuentas_por_cobrar_evidencia'];
                            if ($file['size'] <= $max_bytes && in_array($file['type'], $allowed_types)) {
                                $origName = isset($file['name']) ? basename($file['name']) : 'evidencia';
                                $safeName = preg_replace('/[^A-Za-z0-9\.\_\- ]+/', '_', $origName);
                                $safeName = mb_substr($safeName, 0, 200);
                                $destDir = FCPATH . 'uploads/solicitudes/' . intval($insert_id) . '/evidencia/';
                                if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                                $filename = $safeName;
                                if (is_file($destDir . $filename)) {
                                    $filename = time() . '_' . $filename;
                                }
                                $target = $destDir . $filename;
                                if (move_uploaded_file($file['tmp_name'], $target)) {
                                    // Update the solicitud record with the filename
                                    $this->core_model->update('tb_solicitudes', 
                                        array('cuentas_por_cobrar_evidencia' => $filename), 
                                        array('idsolicitud' => $insert_id)
                                    );
                                    // Also register in tb_solicitud_photos for gallery view
                                    $relPath = 'solicitudes/' . $insert_id . '/evidencia/' . $filename;
                                    try {
                                        $ins = array(
                                            'idsolicitud' => $insert_id,
                                            'filename' => $relPath,
                                            'grupo' => 'evidencia',
                                            'created_at' => date('Y-m-d H:i:s')
                                        );
                                        // avoid duplicate entries for same file
                                        $exists = null;
                                        try { $exists = $this->core_model->get_by_id('tb_solicitud_photos', array('idsolicitud' => $insert_id, 'filename' => $relPath)); } catch (Exception $_) { $exists = null; }
                                        if (!$exists) {
                                            $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
                                        }
                                    } catch (Exception $e) {
                                        // ignore DB insert errors
                                    }
                                }
                            }
                        }
                        
                        $groups = array(
                            // single file fields (treated as single-element arrays)
                            'cedula_front' => 'cedula_front',
                            'cedula_back' => 'cedula_back',
                            'consentimiento_filtrado' => 'consentimiento_filtrado',
                            // multi-file fields
                            'fachada' => 'fachada',
                            'inventario' => 'inventario',
                            'otros_ingresos_1' => 'otros_ingresos_1',
                            'otros_ingresos_2' => 'otros_ingresos_2',
                            'otros_ingresos_3' => 'otros_ingresos_3',
                            'docs_generales' => 'docs_generales',
                            'docs_legales' => 'docs_legales',
                            'fotos_adicionales' => 'fotos_adicionales'
                        );
                        foreach ($groups as $field => $group_name) {
                            if (!isset($_FILES[$field])) continue;
                            // normalize to array of files
                            $files = array();
                            if (is_array($_FILES[$field]['name'])) {
                                // multiple
                                $cnt = count($_FILES[$field]['name']);
                                for ($i = 0; $i < $cnt; $i++) {
                                    if ($_FILES[$field]['error'][$i] !== UPLOAD_ERR_OK) continue;
                                    $files[] = array(
                                        'tmp_name' => $_FILES[$field]['tmp_name'][$i],
                                        'name' => $_FILES[$field]['name'][$i],
                                        'type' => $_FILES[$field]['type'][$i],
                                        'size' => $_FILES[$field]['size'][$i]
                                    );
                                }
                            } else {
                                // single
                                if ($_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                                    $files[] = $_FILES[$field];
                                }
                            }

                            foreach ($files as $f) {
                                if (!isset($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) continue;
                                if ($f['size'] > $max_bytes) continue;
                                $type_ok = in_array($f['type'], $allowed_types);
                                if (!$type_ok && in_array($group_name, $allow_pdf_groups) && $f['type'] === 'application/pdf') {
                                    $type_ok = true;
                                }
                                if (!$type_ok) continue;
                                $origName = isset($f['name']) ? basename($f['name']) : 'upload';
                                $safeName = preg_replace('/[^A-Za-z0-9\.\_\- ]+/', '_', $origName);
                                $safeName = mb_substr($safeName, 0, 200);
                                $destDir = FCPATH . 'uploads/solicitudes/' . intval($insert_id) . '/' . $group_name . '/';
                                if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                                $name = $safeName;
                                if (is_file($destDir . $name)) {
                                    $name = time() . '_' . $name;
                                }
                                $target = $destDir . $name;
                                if (move_uploaded_file($f['tmp_name'], $target)) {
                                    $relPath = 'solicitudes/' . $insert_id . '/' . $group_name . '/' . $name;
                                    try {
                                        $ins = array(
                                            'idsolicitud' => $insert_id,
                                            'filename' => $relPath,
                                            'grupo' => $group_name,
                                            'created_at' => date('Y-m-d H:i:s')
                                        );
                                        $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
                                    } catch (Exception $e) {
                                        // ignore DB insert errors (table may not exist) but file stays on disk
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // non-fatal - continue
                    }

                    $this->session->set_flashdata('success', 'Solicitud guardada correctamente');
                    // If a cliente was created/updated, persist idcliente on the solicitud and redirect to its edit form so fields are visible
                    if (!empty($client_id)) {
                        // save idcliente on the solicitud row for easy reference
                        try {
                            $this->core_model->update('tb_solicitudes', array('idcliente' => $client_id), array('idsolicitud' => $insert_id));
                        } catch (Exception $e) { log_message('error','[SOLICITUDS] No se pudo actualizar idcliente en tb_solicitudes: ' . $e->getMessage()); }
                        redirect('clientes/core/' . $client_id);
                    } else {
                        // Fallback: continue to guarantees creation
                        redirect('garantias/create/' . $insert_id);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Error al guardar solicitud');
                    redirect($this->router->fetch_class());
                }
            }

            // Show create form
            $data = array(
                'titulo' => 'Registrar Solicitud Inicial',
                'subtitulo' => 'Ingrese los datos de la solicitud y cliente.',
                'icono_view' => 'ik ik-user ',
                'scripts' => array('js/utils/utils.js'),
                'aprobaciones' => array(),
                'asesores' => $this->core_model->get_all('tb_asesores')
            );
            $data['word_allowed_fields'] = $word_allowed_fields;
            $data['view_word_mode'] = $view_word_mode;
            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/core', $data);
            $this->load->view('layout/footer');
            return;
        }

        // Edit path
        // ensure solicitud exists
        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $cliente_id));
        if (!$sol) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
            return;
        }

        // If garantia is stored as a comma-separated string, expose boolean-like properties
        try {
            if (isset($sol->garantia) && is_string($sol->garantia) && trim($sol->garantia) !== '') {
                $g = array_map('trim', explode(',', $sol->garantia));
                $sol->garantia_hipotecaria = ($this->_garantia_has($g, 'Hipotecaria') ? 1 : 0);
                $sol->garantia_mobiliaria = ($this->_garantia_has($g, 'Mobiliaria') ? 1 : 0);
                $sol->garantia_sin = ($this->_garantia_has($g, 'Sin') ? 1 : 0);
                $sol->garantia_prendaria = ($this->_garantia_has($g, 'Prendaria') ? 1 : 0);
                $sol->garantia_fiador = ($this->_garantia_has($g, 'Fiador') ? 1 : 0);
                $sol->garantia_otra = ($this->_garantia_has($g, 'Otra') ? 1 : 0);
            } else {
                $sol->garantia_hipotecaria = isset($sol->garantia_hipotecaria) ? $sol->garantia_hipotecaria : 0;
                $sol->garantia_mobiliaria = isset($sol->garantia_mobiliaria) ? $sol->garantia_mobiliaria : 0;
                $sol->garantia_sin = isset($sol->garantia_sin) ? $sol->garantia_sin : 0;
                $sol->garantia_prendaria = isset($sol->garantia_prendaria) ? $sol->garantia_prendaria : 0;
                $sol->garantia_fiador = isset($sol->garantia_fiador) ? $sol->garantia_fiador : 0;
                $sol->garantia_otra = isset($sol->garantia_otra) ? $sol->garantia_otra : 0;
            }
            // parse tipo_contrato into checkbox flags for the view
            if (isset($sol->tipo_contrato) && is_string($sol->tipo_contrato) && trim($sol->tipo_contrato) !== '') {
                $tc = array_map('trim', explode(',', $sol->tipo_contrato));
                $sol->tipo_contrato_permanente = (in_array('Permanente', $tc) ? 1 : 0);
                $sol->tipo_contrato_temporal = (in_array('Temporal', $tc) ? 1 : 0);
                $sol->tipo_contrato_otro = (in_array('Otro', $tc) ? 1 : 0);
            } else {
                $sol->tipo_contrato_permanente = isset($sol->tipo_contrato_permanente) ? $sol->tipo_contrato_permanente : 0;
                $sol->tipo_contrato_temporal = isset($sol->tipo_contrato_temporal) ? $sol->tipo_contrato_temporal : 0;
                $sol->tipo_contrato_otro = isset($sol->tipo_contrato_otro) ? $sol->tipo_contrato_otro : 0;
            }
        } catch (Exception $e) {
            // ignore parsing errors
        }

        // Provide convenient fallback properties used by the view
        try {
            // cuota_estimado (form) expects this name; DB stores cuota_estim_estimada
            // Always sync from DB column to form field name, even if empty
            if (isset($sol->cuota_estim_estimada)) {
                $sol->cuota_estimado = $sol->cuota_estim_estimada;
            }
            // cuota_estimado_quincenal (form) expects this name; DB stores cuota_estim_estimada_quincenal
            if (isset($sol->cuota_estim_estimada_quincenal)) {
                $sol->cuota_estimado_quincenal = $sol->cuota_estim_estimada_quincenal;
            }
            // Additional fallbacks for view field names vs DB columns
            try {
                // ventas_buenos_amount / ventas_malos_amount are form names; DB stores ventas_dias_buenos / ventas_dias_malos
                if (!isset($sol->ventas_buenos_amount) && isset($sol->ventas_dias_buenos)) {
                    $sol->ventas_buenos_amount = $sol->ventas_dias_buenos;
                }
                if (!isset($sol->ventas_malos_amount) && isset($sol->ventas_dias_malos)) {
                    $sol->ventas_malos_amount = $sol->ventas_dias_malos;
                }
                // ventas_promedio_mensual exists as-is
                if (!isset($sol->ventas_promedio_mensual) && isset($sol->ventas_promedio_mensual)) {
                    $sol->ventas_promedio_mensual = $sol->ventas_promedio_mensual;
                }
                // propiedad_negocio should be textual; ensure view gets the stored value if present
                if (!isset($sol->propiedad_negocio) && isset($sol->propiedad_negocio)) {
                    $sol->propiedad_negocio = $sol->propiedad_negocio;
                }
                // Other optional 'otros_ingresos_*' fields: if DB has these columns, copy them, otherwise leave empty
                $maybe_cols = array(
                    'otros_ingresos_1_amount','otros_ingresos_1_margin','otros_ingresos_1_detalle',
                    'otros_ingresos_2_amount','otros_ingresos_2_margin','otros_ingresos_2_detalle',
                    'otros_ingresos_3_amount','otros_ingresos_3_margin','otros_ingresos_3_detalle'
                );
                foreach ($maybe_cols as $mc) {
                    if (!isset($sol->{$mc}) && $this->db->field_exists($mc, 'tb_solicitudes')) {
                        $row = $this->db->get_where('tb_solicitudes', array('idsolicitud' => $sol->idsolicitud))->row();
                        if ($row && isset($row->{$mc})) $sol->{$mc} = $row->{$mc};
                    }
                }
                // Expose view-named fields from DB (or fallback to generic energia/agua/internet)
                if (!isset($sol->energia_electrica)) {
                    if ($this->db->field_exists('energia_electrica', 'tb_solicitudes') && isset($sol->energia_electrica)) {
                        $sol->energia_electrica = $sol->energia_electrica;
                    } elseif (isset($sol->energia)) {
                        $sol->energia_electrica = $sol->energia;
                    }
                }
                if (!isset($sol->agua_potable)) {
                    if ($this->db->field_exists('agua_potable', 'tb_solicitudes') && isset($sol->agua_potable)) {
                        $sol->agua_potable = $sol->agua_potable;
                    } elseif (isset($sol->agua)) {
                        $sol->agua_potable = $sol->agua;
                    }
                }
                if (!isset($sol->internet_telefonia)) {
                    if ($this->db->field_exists('internet_telefonia', 'tb_solicitudes') && isset($sol->internet_telefonia)) {
                        $sol->internet_telefonia = $sol->internet_telefonia;
                    } elseif (isset($sol->internet)) {
                        $sol->internet_telefonia = $sol->internet;
                    }
                }
                // Other declaration/signature/promotor fields
                $other_view_cols = array('declaro_verificacion','firma_solicitante','fecha_firma','ddc_investigacion_campo','nombre_promotor','fecha_recepcion_solicitud','observaciones_promotor','rubro_credito','destino_credito','tipo_credito');
                foreach ($other_view_cols as $vc) {
                    if (!isset($sol->{$vc}) && $this->db->field_exists($vc, 'tb_solicitudes')) {
                        $row = $this->db->get_where('tb_solicitudes', array('idsolicitud' => $sol->idsolicitud))->row();
                        if ($row && isset($row->{$vc})) $sol->{$vc} = $row->{$vc};
                    }
                }
                // Ensure es_nuevo / es_renovacion flags are available on the $sol object as ints (0/1)
                try {
                    if ($this->db->field_exists('es_nuevo', 'tb_solicitudes')) {
                        if (!isset($sol->es_nuevo)) {
                            $row = $this->db->get_where('tb_solicitudes', array('idsolicitud' => $sol->idsolicitud))->row();
                            $sol->es_nuevo = ($row && isset($row->es_nuevo)) ? ((int)$row->es_nuevo) : 0;
                        } else {
                            $sol->es_nuevo = (int)$sol->es_nuevo;
                        }
                    } else {
                        // if column doesn't exist, ensure property at least present for view (unchecked)
                        if (!isset($sol->es_nuevo)) $sol->es_nuevo = 0;
                    }
                    if ($this->db->field_exists('es_renovacion', 'tb_solicitudes')) {
                        if (!isset($sol->es_renovacion)) {
                            $row = $this->db->get_where('tb_solicitudes', array('idsolicitud' => $sol->idsolicitud))->row();
                            $sol->es_renovacion = ($row && isset($row->es_renovacion)) ? ((int)$row->es_renovacion) : 0;
                        } else {
                            $sol->es_renovacion = (int)$sol->es_renovacion;
                        }
                    } else {
                        if (!isset($sol->es_renovacion)) $sol->es_renovacion = 0;
                    }
                } catch (Exception $e) { /* ignore DB/read errors */ }
            } catch (Exception $e) { }
            // populate producto_* view fields from stored columns if not present
            if (!isset($sol->producto_tasa) && isset($sol->tasa_interes)) { $sol->producto_tasa = $sol->tasa_interes; }
            if (!isset($sol->producto_comision) && isset($sol->comision_desembolso)) { $sol->producto_comision = $sol->comision_desembolso; }
            if (!isset($sol->producto_plazo) && isset($sol->plazo_meses)) { $sol->producto_plazo = $sol->plazo_meses; }
            // Fallback to evidence photo record when the solicitud row has no direct evidence filename
            if (empty($sol->cuentas_por_cobrar_evidencia) && isset($sol->idsolicitud)) {
                try {
                    $evidence_photo = $this->core_model->get_by_id('tb_solicitud_photos', array(
                        'idsolicitud' => $sol->idsolicitud,
                        'grupo' => 'evidencia'
                    ));
                    if ($evidence_photo && !empty($evidence_photo->filename)) {
                        $sol->cuentas_por_cobrar_evidencia = basename($evidence_photo->filename);
                    }
                } catch (Exception $e) {
                    // ignore DB lookup failures
                }
            }
            // if product proposal exists, try to read product classification to preselect the classification dropdown
            if ((!isset($sol->clasificacion) || $sol->clasificacion === null || $sol->clasificacion === '') && !empty($sol->propuesta_tipos)) {
                try {
                    $ids = json_decode($sol->propuesta_tipos, true);
                    if (is_array($ids) && count($ids) > 0) {
                        $pid = $ids[0];
                        $row = $this->db->get_where('tb_tipo_productos', array('id' => $pid))->row();
                        if ($row) {
                            if (isset($row->clasificacion)) {
                                $sol->clasificacion = $row->clasificacion;
                            }
                            // populate producto_* view fields from the product row when available
                            if (!isset($sol->producto_tasa) || $sol->producto_tasa === '') {
                                if (isset($row->tasa_mensual)) { $sol->producto_tasa = $row->tasa_mensual; }
                                elseif (isset($row->tasa) ) { $sol->producto_tasa = $row->tasa; }
                            }
                            if (!isset($sol->producto_comision) || $sol->producto_comision === '') {
                                if (isset($row->comision_desembolso)) { $sol->producto_comision = $row->comision_desembolso; }
                                elseif (isset($row->porcentaje) ) { $sol->producto_comision = $row->porcentaje; }
                            }
                            if (!isset($sol->producto_plazo) || $sol->producto_plazo === '') {
                                if (isset($row->plazo_max)) { $sol->producto_plazo = $row->plazo_max; }
                                elseif (isset($row->plazo)) { $sol->producto_plazo = $row->plazo; }
                            }
                        }
                    }
                } catch (Exception $e) { }
            // MAP COMMON ALIASES USED BY PDF TEMPLATE
            try {
                // Sales amounts used by PDF: ventas_en_dias_buenos / ventas_en_dias_malos
                if (!isset($sol->ventas_en_dias_buenos)) {
                    if (isset($sol->ventas_dias_buenos)) $sol->ventas_en_dias_buenos = $sol->ventas_dias_buenos;
                    elseif (isset($sol->ventas_buenos_amount)) $sol->ventas_en_dias_buenos = $sol->ventas_buenos_amount;
                    else $sol->ventas_en_dias_buenos = 0;
                }
                if (!isset($sol->ventas_en_dias_malos)) {
                    if (isset($sol->ventas_dias_malos)) $sol->ventas_en_dias_malos = $sol->ventas_dias_malos;
                    elseif (isset($sol->ventas_malos_amount)) $sol->ventas_en_dias_malos = $sol->ventas_malos_amount;
                    else $sol->ventas_en_dias_malos = 0;
                }

                // Otros ingresos: PDF expects *_monto and *_margen names
                if (!isset($sol->otros_ingresos_1_monto) && isset($sol->otros_ingresos_1_amount)) $sol->otros_ingresos_1_monto = $sol->otros_ingresos_1_amount;
                if (!isset($sol->otros_ingresos_1_margen) && isset($sol->otros_ingresos_1_margin)) $sol->otros_ingresos_1_margen = $sol->otros_ingresos_1_margin;
                if (!isset($sol->otros_ingresos_2_monto) && isset($sol->otros_ingresos_2_amount)) $sol->otros_ingresos_2_monto = $sol->otros_ingresos_2_amount;
                if (!isset($sol->otros_ingresos_2_margen) && isset($sol->otros_ingresos_2_margin)) $sol->otros_ingresos_2_margen = $sol->otros_ingresos_2_margin;
                if (!isset($sol->otros_ingresos_3_monto) && isset($sol->otros_ingresos_3_amount)) $sol->otros_ingresos_3_monto = $sol->otros_ingresos_3_amount;
                if (!isset($sol->otros_ingresos_3_margen) && isset($sol->otros_ingresos_3_margin)) $sol->otros_ingresos_3_margen = $sol->otros_ingresos_3_margin;

                // Financial structure aliases
                if (!isset($sol->cuentas_por_cobrar) && isset($sol->cuentas_por_cobrar_amount)) $sol->cuentas_por_cobrar = $sol->cuentas_por_cobrar_amount;
                if (!isset($sol->caja_efectivo) && isset($sol->caja_amount)) $sol->caja_efectivo = $sol->caja_amount;
                if (!isset($sol->saldo_banco) && isset($sol->banco_amount)) $sol->saldo_banco = $sol->banco_amount;

                // Gasto aliases used in PDF
                if (!isset($sol->gasto_alquiler) && isset($sol->pago_alquiler)) $sol->gasto_alquiler = $sol->pago_alquiler;
                if (!isset($sol->gasto_trabajadores) && isset($sol->pago_trabajadores)) $sol->gasto_trabajadores = $sol->pago_trabajadores;
                if (!isset($sol->gasto_energia)) {
                    if (isset($sol->energia_electrica)) $sol->gasto_energia = $sol->energia_electrica;
                    elseif (isset($sol->energia)) $sol->gasto_energia = $sol->energia;
                    else $sol->gasto_energia = 0;
                }
                if (!isset($sol->gasto_agua)) {
                    if (isset($sol->agua_potable)) $sol->gasto_agua = $sol->agua_potable;
                    elseif (isset($sol->agua)) $sol->gasto_agua = $sol->agua;
                    else $sol->gasto_agua = 0;
                }
                if (!isset($sol->gasto_internet)) {
                    if (isset($sol->internet_telefonia)) $sol->gasto_internet = $sol->internet_telefonia;
                    elseif (isset($sol->internet)) $sol->gasto_internet = $sol->internet;
                    else $sol->gasto_internet = 0;
                }

                // Inventory / ventas promedio alias
                if (!isset($sol->ventas_promedio_mensual) && isset($sol->ventas_promedio_mensual)) $sol->ventas_promedio_mensual = $sol->ventas_promedio_mensual;
            } catch (Exception $e) { /* ignore */ }

            // Block download if solicitud is rejected
            try {
                if (isset($status_label) && strtolower($status_label) === 'rechazado') {
                    $this->session->set_flashdata('error', 'No se puede descargar aprobaciones: la solicitud está rechazada.');
                    redirect($this->router->fetch_class() . '/validacion_aprobacion');
                    return;
                }
            } catch (Exception $e) { /* ignore */ }
            }
        } catch (Exception $e) {
            // ignore
        }

        // minimal validation for edit in Word-mode
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
        // Require an edit comment when updating a solicitud so changes are auditable
        $this->form_validation->set_rules('edit_comment', 'Comentario de edición', 'trim|required|min_length[3]');

        if ($this->form_validation->run()) {
            $data = elements($word_allowed_fields, $this->input->post());
            // Preserve the cuentas_por_cobrar_amount value for save
            if ($this->input->post('cuentas_por_cobrar_amount') !== null) {
                $raw = trim((string)$this->input->post('cuentas_por_cobrar_amount'));
                $data['cuentas_por_cobrar_amount'] = ($raw === '' ? null : $raw);
                if (!$this->db->field_exists('cuentas_por_cobrar_amount', 'tb_solicitudes') && $this->db->field_exists('cuentas_por_cobrar', 'tb_solicitudes')) {
                    $data['cuentas_por_cobrar'] = $data['cuentas_por_cobrar_amount'];
                }
            }
            // Guardar idasesor (Ruta/Asesor) si viene del formulario (modo edición)
            if ($this->input->post('idasesor') !== null) {
                $raw_idasesor = trim($this->input->post('idasesor'));
                if ($raw_idasesor !== '') {
                    $data['idasesor'] = is_numeric($raw_idasesor) ? (int)$raw_idasesor : null;
                }
            }
                    // Exponer nombre del asesor para PDF/vista si existe idasesor
                    if (isset($sol->idasesor) && $sol->idasesor) {
                        $asesor_row = $this->db->get_where('tb_asesores', array('idasesor' => $sol->idasesor))->row();
                        if ($asesor_row && isset($asesor_row->nombres)) {
                            $sol->nombre_asesor = $asesor_row->nombres;
                        } else {
                            $sol->nombre_asesor = $sol->idasesor;
                        }
                    } else {
                        $sol->nombre_asesor = '';
                    }
            // Debug: log incoming POST (selected fields) and filtered $data for troubleshooting
            try { log_message('debug', '[SOLICITUDS][UPDATE][INCOMING_POST] ' . preg_replace('/\s+/', ' ', var_export(array_intersect_key($this->input->post(), array_flip($word_allowed_fields)), true))); } catch (Exception $e) {}

            // Map cuota_estimado (form) -> cuota_estim_estimada (DB)
            if ($this->input->post('cuota_estimado') !== null) {
                $raw = trim($this->input->post('cuota_estimado'));
                if ($raw !== '') {
                    $data['cuota_estim_estimada'] = is_numeric($raw) ? floatval(str_replace(',', '', $raw)) : $raw;
                }
            }
            // Map cuota_estimado_quincenal (form) -> cuota_estim_estimada_quincenal (DB)
            if ($this->input->post('cuota_estimado_quincenal') !== null) {
                $rawq = trim($this->input->post('cuota_estimado_quincenal'));
                if ($rawq !== '') {
                    $data['cuota_estim_estimada_quincenal'] = is_numeric($rawq) ? floatval(str_replace(',', '', $rawq)) : $rawq;
                }
            }

            // ensure comision_desembolso saved as decimal if user entered percent
            if (isset($data['comision_desembolso']) && $data['comision_desembolso'] !== '' && is_numeric($data['comision_desembolso'])) {
                $cv = floatval(str_replace(',', '.', $data['comision_desembolso']));
                if ($cv > 1) { $data['comision_desembolso'] = $cv / 100.0; }
                else { $data['comision_desembolso'] = $cv; }
            }

            // Merge garantia_* checkbox inputs into single `garantia` string
            $gar_list = array();
            if ($this->input->post('garantia_hipotecaria')) $gar_list[] = 'Hipotecaria';
            if ($this->input->post('garantia_mobiliaria')) $gar_list[] = 'Mobiliaria';
            if ($this->input->post('garantia_sin')) $gar_list[] = 'Sin garantía';
            if ($this->input->post('garantia_prendaria')) $gar_list[] = 'Prendaria';
            if ($this->input->post('garantia_fiador')) $gar_list[] = 'Fiador';
            if ($this->input->post('garantia_otra')) $gar_list[] = 'Otra';
            if (count($gar_list) > 0) {
                $data['garantia'] = implode(', ', $gar_list);
            } else {
                $data['garantia'] = (isset($data['garantia']) && trim($data['garantia']) !== '') ? $data['garantia'] : null;
            }
            // Merge tipo_contrato checkboxes into single `tipo_contrato` column
            $tc = array();
            if ($this->input->post('tipo_contrato_permanente')) $tc[] = 'Permanente';
            if ($this->input->post('tipo_contrato_temporal')) $tc[] = 'Temporal';
            if ($this->input->post('tipo_contrato_otro')) $tc[] = 'Otro';
            if (count($tc) > 0) {
                $data['tipo_contrato'] = implode(', ', $tc);
            } else {
                $data['tipo_contrato'] = (isset($data['tipo_contrato']) && trim($data['tipo_contrato']) !== '') ? $data['tipo_contrato'] : null;
            }
            // Map product hidden fields to stored columns when editing
            if ($this->input->post('producto_tasa') !== null && trim((string)$this->input->post('producto_tasa')) !== '') {
                $pt = trim((string)$this->input->post('producto_tasa'));
                if (is_numeric($pt)) $data['tasa_interes'] = floatval(str_replace(',', '.', $pt));
            }
            if ($this->input->post('producto_comision') !== null && trim((string)$this->input->post('producto_comision')) !== '') {
                $pc = trim((string)$this->input->post('producto_comision'));
                if (is_numeric($pc)) $data['comision_desembolso'] = floatval(str_replace(',', '.', $pc));
            }
            // NO sobrescribir plazo_meses con producto_plazo si el usuario ya ingresó un plazo
            // Solo usar producto_plazo si plazo_meses está vacío
            if ($this->input->post('producto_plazo') !== null && trim((string)$this->input->post('producto_plazo')) !== '') {
                $pp = trim((string)$this->input->post('producto_plazo'));
                // Solo sobrescribir si plazo_meses NO fue enviado o está vacío
                $user_plazo = $this->input->post('plazo_meses');
                if (is_numeric($pp) && ($user_plazo === null || trim((string)$user_plazo) === '')) {
                    $data['plazo_meses'] = intval($pp);
                }
            }
            // Normalize header checkboxes from HTML (checkbox sends 'on' when checked)
            if (array_key_exists('es_nuevo', $data)) {
                $v = $data['es_nuevo'];
                $data['es_nuevo'] = (($v === 'on' || $v === '1' || $v === 1 || $v === true) ? 1 : 0);
            }
            if (array_key_exists('es_renovacion', $data)) {
                $v = $data['es_renovacion'];
                $data['es_renovacion'] = (($v === 'on' || $v === '1' || $v === 1 || $v === true) ? 1 : 0);
            }
            if (array_key_exists('es_rural', $data)) {
                $v = strtolower(trim((string)$data['es_rural']));
                $data['es_rural'] = (($v === '1' || $v === 'si' || $v === 'sí' || $v === 'on' || $v === 'true') ? 1 : 0);
            }
            // Map ventas_buenos_amount / ventas_malos_amount (form) -> ventas_dias_buenos / ventas_dias_malos (DB)
            if ($this->input->post('ventas_buenos_amount') !== null) {
                $vb = trim((string)$this->input->post('ventas_buenos_amount'));
                if ($vb === '') { $data['ventas_dias_buenos'] = null; }
                elseif (is_numeric($vb)) { $data['ventas_dias_buenos'] = floatval(str_replace(',', '.', $vb)); }
            }
            if ($this->input->post('ventas_malos_amount') !== null) {
                $vm = trim((string)$this->input->post('ventas_malos_amount'));
                if ($vm === '') { $data['ventas_dias_malos'] = null; }
                elseif (is_numeric($vm)) { $data['ventas_dias_malos'] = floatval(str_replace(',', '.', $vm)); }
            }
            $normalize_numeric($data, array(
                'monto_solicitado', 'plazo_meses', 'comision_desembolso', 'ventas_dias_buenos', 'ventas_dias_malos', 'ventas_al_credito',
                'ventas_dias_buenos_mask', 'ventas_dias_malos_mask', 'numero_empleados', 'numero_dependientes', 'edad', 'tiempo_residir_anios','tiempo_residir_meses', 'tiempo_empleo_anios','tiempo_empleo_meses','ingreso_mensual_neto', 'tiempo_operacion_anios','tiempo_operacion_meses','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual','margen_comercial','otros_ingresos_1_amount','otros_ingresos_1_margin','otros_ingresos_2_amount','otros_ingresos_2_margin','otros_ingresos_3_amount','otros_ingresos_3_margin', 'cuentas_por_cobrar_amount', 'caja_amount',
                'banco_amount', 'monto_total_inventario', 'pago_alquiler', 'pago_trabajadores', 'energia', 'agua', 'internet', 'gastos_fijos', 'gastos_operativos'
            ));
            $normalize_dates($data, array('fecha_nacimiento', 'fecha_recepcion', 'fecha_firma', 'fecha_recepcion_solicitud', 'fecha_solicitud'));

            // masks
            $mask_b = 0; $vb = $this->input->post('ventas_buenos_days'); if (is_array($vb)) foreach ($vb as $ix) { $i = (int)$ix; if ($i >= 0 && $i <= 6) $mask_b |= (1 << $i); }
            $data['ventas_dias_buenos_mask'] = ($mask_b > 0 ? $mask_b : null);
            $mask_m = 0; $vm = $this->input->post('ventas_malos_days'); if (is_array($vm)) foreach ($vm as $ix) { $i = (int)$ix; if ($i >= 0 && $i <= 6) $mask_m |= (1 << $i); }
            $data['ventas_dias_malos_mask'] = ($mask_m > 0 ? $mask_m : null);

            // Before update: remove any keys that are not actual columns in tb_solicitudes to avoid SQL errors
            foreach (array_keys($data) as $k) {
                if (!$this->db->field_exists($k, 'tb_solicitudes')) {
                    unset($data[$k]);
                }
            }
            try { log_message('debug', '[SOLICITUDS][UPDATE][FINAL_KEYS] ' . implode(',', array_keys($data))); } catch (Exception $e) {}

            // Coerce non-numeric values to NULL for integer columns in the DB (prevents UPDATE errors)
            $cols = $this->db->field_data('tb_solicitudes');
            $colTypes = array();
            foreach ($cols as $c) { $colTypes[$c->name] = strtolower($c->type); }
            foreach ($data as $k => $v) {
                if (!isset($colTypes[$k])) continue;
                $t = $colTypes[$k];
                if (strpos($t, 'int') !== false || strpos($t, 'tinyint') !== false || strpos($t, 'smallint') !== false || strpos($t, 'mediumint') !== false) {
                    if ($v === '' || $v === null) { $data[$k] = null; }
                    elseif (!is_numeric($v)) { $data[$k] = null; }
                    else { $data[$k] = (int)$v; }
                }
            }

            // Convert any remaining empty string values to NULL before update/insert to avoid SQL strict-mode errors
            foreach ($data as $k => $v) {
                if ($v === '') { $data[$k] = null; }
            }

            // update
            // debug log: inspect product-related and commission fields before update
            try { log_message('debug', '[SOLICITUDS][UPDATE] producto_tasa=' . var_export($this->input->post('producto_tasa'), true) . ' producto_comision=' . var_export($this->input->post('producto_comision'), true) . ' producto_plazo=' . var_export($this->input->post('producto_plazo'), true) . ' mapped_comision=' . var_export(isset($data['comision_desembolso']) ? $data['comision_desembolso'] : null, true)); } catch (Exception $e) {}

            $ok = $this->core_model->update('tb_solicitudes', $data, array('idsolicitud' => $cliente_id));
            // store debug keys for quick verification (flash only)
            $saved_keys = is_array($data) ? array_keys($data) : array();
            $this->session->set_flashdata('debug_saved_keys', implode(',', $saved_keys));
            $this->session->set_flashdata('last_saved_id', $cliente_id);
            // Save the edit comment into tb_solicitudes_comments for audit (if provided)
            try {
                $ec = trim((string)$this->input->post('edit_comment'));
                if ($ec !== '') {
                    $u = $this->ion_auth->user()->row();
                    $comment_data = array(
                        'idsolicitud' => $cliente_id,
                        'user_id' => ($u ? $u->id : 0),
                        'username' => ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema'),
                        'action' => 'edit',
                        'comment' => $ec
                    );
                    $this->core_model->insert('tb_solicitudes_comments', $comment_data, TRUE);
                }
            } catch (Exception $e) { /* ignore comment save errors */ }

            // Process any requested photo/document deletions only when the form is saved
            try {
                $deletePayload = trim((string)$this->input->post('photos_to_delete'));
                if ($deletePayload !== '') {
                    $deletedItems = json_decode($deletePayload, true);
                    if (!is_array($deletedItems)) {
                        $deletedItems = array();
                    }
                    if (!empty($deletedItems)) {
                        $u = $this->ion_auth->user()->row();
                        $username = 'Sistema';
                        if ($u) {
                            $username = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->username ?? 'Usuario');
                        }
                        foreach ($deletedItems as $item) {
                            $idphoto = null;
                            $filename = '';
                            if (is_array($item)) {
                                if (isset($item['idphoto']) && $item['idphoto'] !== '') {
                                    $idphoto = intval($item['idphoto']);
                                }
                                if (isset($item['filename'])) {
                                    $filename = trim((string)$item['filename']);
                                }
                            } elseif (is_string($item) || is_numeric($item)) {
                                $filename = trim((string)$item);
                            }
                            if (!$idphoto && $filename === '') {
                                continue;
                            }

                            $photoRow = null;
                            if ($idphoto) {
                                try { $photoRow = $this->core_model->get_by_id('tb_solicitud_photos', array('idphoto' => $idphoto)); } catch (Exception $e) { $photoRow = null; }
                            }
                            if (!$photoRow && $filename !== '') {
                                $safeFilename = trim(str_replace('\\', '/', $filename), '/');
                                if (strpos($safeFilename, 'solicitudes/') !== 0) {
                                    $safeFilename = ltrim($safeFilename, '/');
                                }
                                if ($safeFilename !== '') {
                                    try { $photoRow = $this->core_model->get_by_id('tb_solicitud_photos', array('filename' => $safeFilename)); } catch (Exception $e) { $photoRow = null; }
                                    if (!$photoRow) {
                                        $photoRow = (object) array('filename' => $safeFilename, 'idsolicitud' => $cliente_id);
                                    }
                                }
                            }
                            if (!$photoRow) {
                                continue;
                            }

                            $deleteFilename = trim((string)($photoRow->filename ?? $filename));
                            if ($deleteFilename !== '') {
                                $filePath = FCPATH . 'uploads/' . ltrim($deleteFilename, '/');
                                if (is_file($filePath)) {
                                    @unlink($filePath);
                                }
                            }

                            if (!empty($photoRow->idphoto)) {
                                try { $this->core_model->delete('tb_solicitud_photos', array('idphoto' => $photoRow->idphoto)); } catch (Exception $e) { }
                            } elseif ($deleteFilename !== '') {
                                try { $this->core_model->delete('tb_solicitud_photos', array('filename' => $deleteFilename)); } catch (Exception $e) { }
                            }

                            $basename = pathinfo($deleteFilename, PATHINFO_BASENAME);
                            if ($basename !== '') {
                                $historyComment = 'El archivo/foto de nombre \'' . $basename . '\' fue eliminado por \'' . $username . '\'';
                                try {
                                    $this->core_model->insert('tb_solicitudes_comments', array(
                                        'idsolicitud' => $cliente_id,
                                        'user_id' => ($u ? $u->id : 0),
                                        'username' => $username,
                                        'action' => 'delete_file',
                                        'comment' => $historyComment
                                    ), TRUE);
                                } catch (Exception $e) { }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', '[SOLICITUDS] Error processing photo deletions: ' . $e->getMessage());
            }

            if ($ok) {
                $this->session->set_flashdata('success', 'Solicitud actualizada');
                
                // Process any uploaded files after successful update
                try {
                    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
                    $max_bytes = 5 * 1024 * 1024; // 5MB
                    
                    // Handle photo upload for cuentas_por_cobrar_evidencia in edit mode
                    if (isset($_FILES['cuentas_por_cobrar_evidencia']) && $_FILES['cuentas_por_cobrar_evidencia']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['cuentas_por_cobrar_evidencia'];
                        if ($file['size'] <= $max_bytes && in_array($file['type'], $allowed_types)) {
                            $origName = isset($file['name']) ? basename($file['name']) : 'evidencia';
                            $safeName = preg_replace('/[^A-Za-z0-9\.\_\- ]+/', '_', $origName);
                            $safeName = mb_substr($safeName, 0, 200);
                            $destDir = FCPATH . 'uploads/solicitudes/' . intval($cliente_id) . '/evidencia/';
                            if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                            $filename = $safeName;
                            if (is_file($destDir . $filename)) {
                                $filename = time() . '_' . $filename;
                            }
                            $target = $destDir . $filename;
                            if (move_uploaded_file($file['tmp_name'], $target)) {
                                // Update the solicitud record with the new filename
                                $this->core_model->update('tb_solicitudes', 
                                    array('cuentas_por_cobrar_evidencia' => $filename), 
                                    array('idsolicitud' => $cliente_id)
                                );
                                // Also register in tb_solicitud_photos
                                $relPath = 'solicitudes/' . $cliente_id . '/evidencia/' . $filename;
                                try {
                                    $ins = array(
                                        'idsolicitud' => $cliente_id,
                                        'filename' => $relPath,
                                        'grupo' => 'evidencia',
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                    // avoid duplicate entries for same file
                                    $exists = null;
                                    try { $exists = $this->core_model->get_by_id('tb_solicitud_photos', array('idsolicitud' => $cliente_id, 'filename' => $relPath)); } catch (Exception $_) { $exists = null; }
                                    if (!$exists) {
                                        $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
                                    }
                                } catch (Exception $e) {
                                    // ignore DB insert errors
                                }
                            }
                        }
                    }
                    
                    // Handle other multi-file uploads
                    $groups = array(
                        'cedula_front' => 'cedula_front',
                        'cedula_back' => 'cedula_back',
                        'fachada' => 'fachada',
                        'inventario' => 'inventario',
                        'otros_ingresos_1' => 'otros_ingresos_1',
                        'otros_ingresos_2' => 'otros_ingresos_2',
                        'otros_ingresos_3' => 'otros_ingresos_3'
                    );
                    foreach ($groups as $field => $group_name) {
                        if (!isset($_FILES[$field])) continue;
                        // normalize to array of files
                        $files = array();
                        if (is_array($_FILES[$field]['name'])) {
                            // multiple
                            $cnt = count($_FILES[$field]['name']);
                            for ($i = 0; $i < $cnt; $i++) {
                                if ($_FILES[$field]['error'][$i] !== UPLOAD_ERR_OK) continue;
                                $files[] = array(
                                    'tmp_name' => $_FILES[$field]['tmp_name'][$i],
                                    'name' => $_FILES[$field]['name'][$i],
                                    'type' => $_FILES[$field]['type'][$i],
                                    'size' => $_FILES[$field]['size'][$i]
                                );
                            }
                        } else {
                            // single
                            if ($_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                                $files[] = $_FILES[$field];
                            }
                        }

                        foreach ($files as $f) {
                            if (!isset($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) continue;
                            if ($f['size'] > $max_bytes) continue;
                            if (!in_array($f['type'], $allowed_types)) continue;
                            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                            $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                            $name = time() . '_' . substr(md5(uniqid('', true)), 0, 8) . ($safeExt ? '.' . $safeExt : '');
                            $destDir = FCPATH . 'uploads/solicitudes/' . intval($cliente_id) . '/' . $group_name . '/';
                            if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                            $target = $destDir . $name;
                            if (move_uploaded_file($f['tmp_name'], $target)) {
                                $relPath = 'solicitudes/' . $cliente_id . '/' . $group_name . '/' . $name;
                                try {
                                    $ins = array(
                                        'idsolicitud' => $cliente_id,
                                        'filename' => $relPath,
                                        'grupo' => $group_name,
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                    $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
                                } catch (Exception $e) {
                                    // ignore DB insert errors
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    // non-fatal - continue
                }
            }
            redirect($this->router->fetch_class());
        }

        // Show edit form prefilled
        $data = array(
            'titulo' => 'Editar Solicitud / Cliente',
            'subtitulo' => 'Realice los cambios que desee.',
            'icono_view' => 'ik ik-user ',
            'scripts' => array('js/utils/utils.js'),
            'solicitud' => $sol,
            'asesores' => $this->core_model->get_all('tb_asesores'),
            'aprobaciones' => $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $cliente_id))
        );
        // expose word-mode and view mode
        $data['word_allowed_fields'] = $word_allowed_fields;
        $data['view_word_mode'] = $view_word_mode;
        $this->load->view('layout/header', $data);
        $this->load->view('solicitudes/core', $data);
        $this->load->view('layout/footer');
        return;
    }

        /**
         * AJAX endpoint para agregar un comentario independiente (ej. desde cancelar)
         * POST: idsolicitud, comment, action
         */
        public function add_comment_ajax()
        {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $idsolicitud = $this->input->post('idsolicitud');
            $comment = trim($this->input->post('comment'));
            $action = $this->input->post('action') ? $this->input->post('action') : 'note';

            if (!$idsolicitud || $comment === '') {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
                return;
            }

            if (!$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
                return;
            }

            $u = $this->ion_auth->user()->row();
            $comment_data = array(
                'idsolicitud' => $idsolicitud,
                'user_id' => ($u ? $u->id : 0),
                'username' => ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema'),
                'action' => $action,
                'comment' => $comment
            );

            $this->core_model->insert('tb_solicitudes_comments', $comment_data, TRUE);
            $last_id = $this->session->userdata('last_id');
            $saved = false;
            if ($last_id) {
                $saved = true;
            }

            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => $saved, 'message' => ($saved ? 'Comentario guardado' : 'Error al guardar'))));
        }

        /**
         * AJAX endpoint para agregar una nota colaborativa (desde listado)
         * POST: idsolicitud, note
         */
        public function add_note_ajax()
        {
                // Allow both AJAX and non-AJAX POSTs (some clients remove X-Requested-With)
                $idsolicitud = $this->input->post('idsolicitud');
                $note = trim($this->input->post('comment') ? $this->input->post('comment') : $this->input->post('note'));

                if (!$idsolicitud || $note === '') {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
                    return;
                }

                $sol_actual = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
                if (!$sol_actual) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
                    return;
                }

                if ($this->_is_solicitud_annulled($idsolicitud)) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
                    return;
                }

                $u = $this->ion_auth->user()->row();
                $note_data = array(
                    'idsolicitud' => $idsolicitud,
                    'user_id' => ($u ? $u->id : 0),
                    'username' => ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema'),
                    'note' => $note
                );

                // Insert and return the inserted note for immediate UI rendering
                $this->core_model->insert('tb_solicitudes_notes', $note_data, TRUE);
                $last_id = $this->session->userdata('last_id');
                if ($last_id) {
                    // fetch the inserted note
                    $inserted = $this->db->get_where('tb_solicitudes_notes', array('idnote' => $last_id))->row();
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'message' => 'Nota guardada', 'note' => $inserted)));
                    return;
                }

                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Error al guardar')));
        }

            /**
             * AJAX endpoint: devuelve comentarios de una solicitud en JSON
             * GET: /solicitudes/get_comments_ajax/{id}
             */
            public function get_comments_ajax($id = NULL)
            {
                if (!$this->input->is_ajax_request()) {
                    show_404();
                }

                if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
                    return;
                }

                $comments = $this->core_model->get_by_id_all('tb_solicitudes_comments', array('idsolicitud' => $id));
                // Ensure consistent ordering (newest first)
                if (is_array($comments)) {
                    usort($comments, function($a, $b){
                        $ta = isset($a->created_at) ? strtotime($a->created_at) : 0;
                        $tb = isset($b->created_at) ? strtotime($b->created_at) : 0;
                        return $tb - $ta;
                    });
                }

                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'comments' => $comments)));
            }

        /**
         * Endpoint: devuelve notas colaborativas de una solicitud en JSON
         * GET: /solicitudes/get_notes_ajax/{id}
         * NOTE: Accepts both AJAX and normal GET requests (some environments strip X-Requested-With)
         */
        public function get_notes_ajax($id = NULL)
        {
            // allow GET requests even if X-Requested-With header is missing

            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada', 'notes' => array())));
                return;
            }

            $notes = $this->core_model->get_by_id_all('tb_solicitudes_notes', array('idsolicitud' => $id));
            if (!is_array($notes)) {
                $notes = array();
            } else {
                usort($notes, function($a, $b){
                    $ta = isset($a->created_at) ? strtotime($a->created_at) : 0;
                    $tb = isset($b->created_at) ? strtotime($b->created_at) : 0;
                    return $tb - $ta;
                });
            }

            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'notes' => $notes)));
        }

        /**
         * Mostrar comentarios históricos de una solicitud
         */
        public function comments($id = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado');
                redirect($this->router->fetch_class());
            }

            $data = array(
                'titulo' => 'Comentarios Solicitud #' . $id,
                'subtitulo' => 'Histórico de comentarios y acciones',
                'solicitud' => $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id)),
                'comments' => $this->core_model->get_by_id_all('tb_solicitudes_comments', array('idsolicitud' => $id))
            );

            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/comments', $data);
            $this->load->view('layout/footer');
        }

        /**
         * Vista: Formato de Uso de Crédito
         */
        public function uso_credito()
        {
            $data = array(
                'titulo' => 'Formato de Uso de Crédito',
                'subtitulo' => 'Registro del uso del crédito por solicitud',
                'icono' => 'fas fa-file-alt',
                'styles' => array(
                    'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
                ),
                'scripts' => array(
                    'plugins/datatables.net/js/jquery.dataTables.min.js',
                    'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                    'plugins/datatables.net/js/activaDatatable.js'
                ),
                'solicitudes' => array()
            );

            // Mismos datos visibles que en el listado principal: incluir asesor (creado por/ruta).
            $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
            $this->db->from('tb_solicitudes');
            $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
            // Default newest-first ordering by record id
            $this->db->order_by('idsolicitud', 'DESC');
            $data['solicitudes'] = $this->db->get()->result();

            // Annotate each solicitud with approval status (pending|approved|rejected|annulled)
            if (!empty($data['solicitudes']) && is_array($data['solicitudes'])) {
                foreach ($data['solicitudes'] as $s) {
                    $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $s->idsolicitud));
                    if (empty($aprs)) {
                        $estado = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                        $s->aprob_status = (($estado === 'anulado' || $estado === 'annulled') ? 'annulled' : 'pending');
                    } else {
                        // order by created_at desc and take latest
                        usort($aprs, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                        $latest = $aprs[0];
                        $s->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    }

                    $estado_sol = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $s->aprob_status = 'annulled';
                    }

                    $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $s->idsolicitud));
                    if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                        $s->aprob_status = 'annulled';
                    }
                }
            }

            // Normalize identification and fecha_solicitud fields so the view can rely on them
            if (!empty($data['solicitudes']) && is_array($data['solicitudes'])) {
                foreach ($data['solicitudes'] as $s) {
                    // identification: prefer explicit names used across environments
                    $s->numero_doc = isset($s->numero_doc) && $s->numero_doc ? $s->numero_doc : (
                        (isset($s->numero_documento) && $s->numero_documento) ? $s->numero_documento : (
                        (isset($s->cedula) && $s->cedula) ? $s->cedula : (
                        (isset($s->identificacion) && $s->identificacion) ? $s->identificacion : null)));
                    // fecha_solicitud: prefer primary field then fallbacks
                    $s->fecha_solicitud = isset($s->fecha_solicitud) && $s->fecha_solicitud ? $s->fecha_solicitud : (
                        (isset($s->fecha_recepcion) && $s->fecha_recepcion) ? $s->fecha_recepcion : (
                        (isset($s->fecha_recepcion_solicitud) && $s->fecha_recepcion_solicitud) ? $s->fecha_recepcion_solicitud : null));
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/uso_credito', $data);
            $this->load->view('layout/footer');
        }

        /**
         * FAF - Asalariado view
         */
        public function faf_asalariado()
        {
            $data = array(
                'titulo' => 'FAF - Asalariado',
                'subtitulo' => 'Formato de Análisis Financiero (Asalariado)',
                'icono' => 'fas fa-briefcase',
                'solicitudes' => $this->core_model->get_all('tb_solicitudes')
            );

            // Annotate each solicitud with FAF existence flags to enforce mutual exclusivity in the UI
            if (!empty($data['solicitudes']) && is_array($data['solicitudes'])) {
                foreach ($data['solicitudes'] as $s) {
                    $s->faf_asalariado = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $s->idsolicitud, 'tipo' => 'asalariado')) ? 1 : 0;
                    $s->faf_comerciante = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $s->idsolicitud, 'tipo' => 'comerciante')) ? 1 : 0;
                }
            }
            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/faf_asalariado', $data);
            $this->load->view('layout/footer');
        }

        /**
         * FAF - Comerciante view
         */
        public function faf_comerciante()
        {
            $data = array(
                'titulo' => 'FAF - Comerciante',
                'subtitulo' => 'Formato de Análisis Financiero (Comerciante)',
                'icono' => 'fas fa-store',
                'solicitudes' => $this->core_model->get_all('tb_solicitudes')
            );

            // Annotate each solicitud with FAF existence flags to enforce mutual exclusivity in the UI
            if (!empty($data['solicitudes']) && is_array($data['solicitudes'])) {
                foreach ($data['solicitudes'] as $s) {
                    $s->faf_asalariado = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $s->idsolicitud, 'tipo' => 'asalariado')) ? 1 : 0;
                    $s->faf_comerciante = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $s->idsolicitud, 'tipo' => 'comerciante')) ? 1 : 0;
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/faf_comerciante', $data);
            $this->load->view('layout/footer');
        }

        /**
         * AJAX: obtener FAF para una solicitud y tipo
         * GET: /solicitudes/get_faf_ajax/{id}/{tipo}
         */
        public function get_faf_ajax($id = NULL, $tipo = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada', 'faf' => null)));
                return;
            }
            $tipo = strtolower($tipo ?: $this->input->get_post('tipo'));
            if (!in_array($tipo, array('asalariado', 'comerciante'))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Tipo inválido', 'faf' => null)));
                return;
            }

            $faf = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $id, 'tipo' => $tipo));
            $other_tipo = ($tipo === 'asalariado') ? 'comerciante' : 'asalariado';
            $other = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $id, 'tipo' => $other_tipo));

            // Also include the solicitud data so the modal can prefill fields
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));

            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'faf' => $faf, 'other' => $other, 'solicitud' => $solicitud)));
        }

        /**
         * AJAX: guardar FAF (insert o update)
         * POST: idsolicitud, tipo, ... campos del formato
         */
        public function save_faf_ajax()
        {
            $idsolicitud = $this->input->post('idsolicitud');
            $tipo = strtolower($this->input->post('tipo'));

            if (!$idsolicitud || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
                return;
            }
            if (!in_array($tipo, array('asalariado', 'comerciante'))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Tipo inválido')));
                return;
            }

            if ($this->_is_solicitud_annulled($idsolicitud)) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
                return;
            }

            // If the other tipo already has data, block saving here
            $other_tipo = ($tipo === 'asalariado') ? 'comerciante' : 'asalariado';
            $other = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $idsolicitud, 'tipo' => $other_tipo));
            if ($other && !empty(trim((string)$other->data))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'El FAF ya fue completado en la vista ' . ucfirst($other_tipo) . '.')));
                return;
            }

                // Build flexible data payload: support two modes:
                // 1) front-end sends a single 'data' JSON string (preferred)
                // 2) front-end posts individual fields directly
                $payload = array();
                $posted = $this->input->post();
                if (isset($posted['data'])) {
                    $raw = $posted['data'];
                    // If it's an array (rare), use it direct; if string, try decode
                    if (is_array($raw)) {
                        $payload = $raw;
                    } else {
                        $decoded = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $payload = $decoded;
                        } else {
                            // fallback: store raw string under key 'raw'
                            $payload = array('raw' => $raw);
                        }
                    }
                } else {
                    $payload = $posted;
                    unset($payload['idsolicitud']);
                    unset($payload['tipo']);
                }

                // Normalize numeric fields in payload (remove commas, convert to float)
                $numeric_keys = array('monto_solicitado', 'plazo', 'plazo_meses', 'ingreso_mensual', 'ingreso_mensual_neto', 'otros_ingresos', 'gastos_personales', 'ingresos_netos', 'capacidad_pago');
                foreach ($numeric_keys as $nk) {
                    if (array_key_exists($nk, $payload)) {
                        $v = $payload[$nk];
                        if ($v === '' || $v === null) {
                            $payload[$nk] = null;
                        } else {
                            // remove thousands separators and spaces
                            if (is_string($v)) $v = str_replace(',', '', trim($v));
                            if (is_numeric($v)) {
                                // keep as numeric (float)
                                $payload[$nk] = (strpos((string)$v, '.') !== false) ? (float)$v : (int)$v;
                            } else {
                                // try to parse float
                                $clean = floatval(preg_replace('/[^0-9.\-]/', '', (string)$v));
                                $payload[$nk] = ($clean == 0 && (string)$v !== '0' && (string)$v !== '0.0') ? null : $clean;
                            }
                        }
                    }
                }

                // Prepare DB row
                $row = array(
                    'idsolicitud' => $idsolicitud,
                    'tipo' => $tipo,
                    'data' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $existing = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $idsolicitud, 'tipo' => $tipo));
                if ($existing) {
                    $this->core_model->update('tb_solicitud_faf', $row, array('idfaf' => $existing->idfaf));
                } else {
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $this->core_model->insert('tb_solicitud_faf', $row, TRUE);
                }

                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'message' => 'Guardado')));
        }

            /**
             * Descargar PDF del FAF para una solicitud y tipo
             * GET: /solicitudes/download_faf/{id}/{tipo}
             */
            public function download_faf($idsolicitud = NULL, $tipo = NULL)
            {
                if (!$idsolicitud || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                    $this->session->set_flashdata('error', 'Solicitud no encontrada');
                    redirect('solicitudes');
                }
                $tipo = strtolower($tipo ?: 'asalariado');
                if (!in_array($tipo, array('asalariado', 'comerciante'))) {
                    $this->session->set_flashdata('error', 'Tipo inválido');
                    redirect('solicitudes');
                }

                $faf = $this->core_model->get_by_id('tb_solicitud_faf', array('idsolicitud' => $idsolicitud, 'tipo' => $tipo));
                if (!$faf) {
                    $this->session->set_flashdata('error', 'FAF no encontrado para esta solicitud');
                    redirect('solicitudes');
                }

                $data = json_decode($faf->data, true);
                if (!is_array($data)) $data = array('raw' => $faf->data);

                $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));

                $pdf_data = array(
                    'solicitud' => $solicitud,
                    'faf' => $data,
                    'tipo' => $tipo,
                    'generated_at' => date('d/m/Y H:i')
                );

                $html = $this->load->view('solicitudes/faf_pdf', $pdf_data, TRUE);
                $filename = 'FAF_' . strtoupper($tipo) . '_SOL' . $idsolicitud . '.pdf';
                // Stream PDF to browser
                $this->pdf->createPDF($html, $filename, TRUE);
            }

        /**
         * Vista: Formato de Verificación de Referencias
         */
        public function referencias()
        {
            $data = array(
                'titulo' => 'Formato de Verificación de Referencias - Solicitud de Crédito',
                'subtitulo' => 'Registro de referencias personales (2 por solicitud)',
                'icono' => 'fas fa-user-friends',
                'styles' => array(
                    'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
                ),
                'scripts' => array(
                    'plugins/datatables.net/js/jquery.dataTables.min.js',
                    'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                    'plugins/datatables.net/js/activaDatatable.js'
                ),
                'solicitudes' => array()
            );

            // Incluir datos de asesor/ruta para mostrar "Creado por" en el listado.
            $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
            $this->db->from('tb_solicitudes');
            $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
            // Default newest-first ordering by record id
            $this->db->order_by('idsolicitud', 'DESC');
            $data['solicitudes'] = $this->db->get()->result();

            // Annotate each solicitud with approval status (pending|approved|rejected|annulled)
            if (!empty($data['solicitudes']) && is_array($data['solicitudes'])) {
                foreach ($data['solicitudes'] as $s) {
                    $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $s->idsolicitud));
                    if (empty($aprs)) {
                        $estado = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                        $s->aprob_status = (($estado === 'anulado' || $estado === 'annulled') ? 'annulled' : 'pending');
                    } else {
                        usort($aprs, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                        $latest = $aprs[0];
                        $s->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    }

                    $estado_sol = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $s->aprob_status = 'annulled';
                    }

                    $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $s->idsolicitud));
                    if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                        $s->aprob_status = 'annulled';
                    }
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/referencias', $data);
            $this->load->view('layout/footer');
        }

        /**
         * AJAX: obtener datos del formato de uso para una solicitud
         */
        public function get_uso_ajax($id = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada', 'uso' => null)));
                return;
            }

            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $id));

            // Load the full solicitud row and ensure commonly-needed keys exist
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
            // Make a copy we can augment safely
            $solOut = $solicitud;

            // Add/normalize expected fields with sensible fallbacks
            $solOut->destino_credito = isset($solicitud->destino_credito) ? $solicitud->destino_credito : (isset($solicitud->destino) ? $solicitud->destino : null);
            $solOut->rubro_credito = isset($solicitud->rubro_credito) ? $solicitud->rubro_credito : (isset($solicitud->destino_conami) ? $solicitud->destino_conami : (isset($solicitud->rubro) ? $solicitud->rubro : null));
            $solOut->firma_solicitante = isset($solicitud->firma_solicitante) ? $solicitud->firma_solicitante : (isset($solicitud->firma) ? $solicitud->firma : null);
            $solOut->fecha_firma = isset($solicitud->fecha_firma) ? $solicitud->fecha_firma : (isset($solicitud->fecha_firma_solicitud) ? $solicitud->fecha_firma_solicitud : null);
            $solOut->nombre_promotor = isset($solicitud->nombre_promotor) ? $solicitud->nombre_promotor : (isset($solicitud->promotor) ? $solicitud->promotor : null);
            $solOut->observaciones_promotor = isset($solicitud->observaciones_promotor) ? $solicitud->observaciones_promotor : (isset($solicitud->observaciones) ? $solicitud->observaciones : null);
            $solOut->ddc_investigacion_campo = isset($solicitud->ddc_investigacion_campo) ? $solicitud->ddc_investigacion_campo : (isset($solicitud->ddc_investigacion) ? $solicitud->ddc_investigacion : null);
            $solOut->es_nuevo = isset($solicitud->es_nuevo) ? (int)$solicitud->es_nuevo : 0;
            $solOut->es_renovacion = isset($solicitud->es_renovacion) ? (int)$solicitud->es_renovacion : 0;
            $solOut->evaluador_credito = null;
            if ($uso && isset($uso->evaluador_credito) && trim((string)$uso->evaluador_credito) !== '') {
                $solOut->evaluador_credito = $uso->evaluador_credito;
            } elseif (isset($solicitud->evaluador_credito) && trim((string)$solicitud->evaluador_credito) !== '') {
                $solOut->evaluador_credito = $solicitud->evaluador_credito;
            }

            // Helper normalized fields (explicit names for the front-end)
            $solOut->nombre_completo = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            // identification: include common alternative column names (numero_doc, numero_documento, cedula, identificacion)
            $solOut->numero_identificacion =
                (isset($solicitud->numero_doc) && $solicitud->numero_doc) ? $solicitud->numero_doc : (
                (isset($solicitud->numero_documento) && $solicitud->numero_documento) ? $solicitud->numero_documento : (
                (isset($solicitud->cedula) && $solicitud->cedula) ? $solicitud->cedula : (
                (isset($solicitud->identificacion) && $solicitud->identificacion) ? $solicitud->identificacion : null)));
            $solOut->telefono_contacto = isset($solicitud->telefono) ? $solicitud->telefono : (isset($solicitud->celular) ? $solicitud->celular : (isset($solicitud->telefono_contacto) ? $solicitud->telefono_contacto : null));
            $solOut->correo_electronico = isset($solicitud->email) ? $solicitud->email : (isset($solicitud->correo_electronico) ? $solicitud->correo_electronico : null);
            $solOut->fecha_solicitud = isset($solicitud->fecha_solicitud) && $solicitud->fecha_solicitud ? $solicitud->fecha_solicitud : (isset($solicitud->fecha_recepcion) && $solicitud->fecha_recepcion ? $solicitud->fecha_recepcion : (isset($solicitud->fecha_recepcion_solicitud) && $solicitud->fecha_recepcion_solicitud ? $solicitud->fecha_recepcion_solicitud : null));
            
            // Normalizar campo de plazo
            $solOut->plazo_solicitado = isset($solicitud->plazo_meses) ? $solicitud->plazo_meses : (isset($solicitud->plazo) ? $solicitud->plazo : null);
            
            // Agregar fecha_evaluacion como fallback desde uso si existe
            $solOut->fecha_evaluacion = null;
            if ($uso && isset($uso->fecha_evaluacion)) {
                $solOut->fecha_evaluacion = $uso->fecha_evaluacion;
            }

            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'uso' => $uso, 'solicitud' => $solOut)));
        }

        /**
         * AJAX: Obtener referencias (máx 2) para una solicitud
         */
        // [Removed older duplicate: get_referencias_ajax]

        /**
         * AJAX: guardar formato de uso (insert o update)
         */
        public function save_uso_ajax()
        {
            header('Content-Type: application/json; charset=utf-8');
            
            // allow both AJAX and normal POST
            $idsolicitud = $this->input->post('idsolicitud');
            if (!$idsolicitud || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                echo json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada'));
                return;
            }

            if ($this->_is_solicitud_annulled($idsolicitud)) {
                echo json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.'));
                return;
            }
            
            // Verificar que la tabla existe
            if (!$this->db->table_exists('tb_solicitud_uso_credito')) {
                echo json_encode(['status' => false, 'message' => 'La tabla tb_solicitud_uso_credito no existe. Ejecute el script SQL correspondiente.']);
                return;
            }

            $row = array(
                'descripcion' => $this->input->post('descripcion'),
                'fuente_ingreso' => $this->input->post('fuente_ingreso'),
                'monto_estimado_mes' => $this->input->post('monto_estimado_mes'),
                'monto_solicitado' => $this->input->post('monto_solicitado') ?: NULL,
                'plazo_solicitado' => $this->input->post('plazo_solicitado') ?: NULL,
                'destino_prestamo' => $this->input->post('destino_prestamo') ?: NULL,
                'destino_detalle' => $this->input->post('destino_detalle') ?: NULL,
                'declaracion_nombre' => $this->input->post('declaracion_nombre'),
                'declaracion_firma' => $this->input->post('declaracion_firma'),
                'declaracion_fecha' => $this->input->post('declaracion_fecha') ?: NULL,
                'evaluador_credito' => $this->input->post('evaluador_credito'),
                'fecha_evaluacion' => $this->input->post('fecha_evaluacion') ?: NULL
            );

            try {
                $existing = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $idsolicitud));
                if ($existing) {
                    // update
                    $result = $this->core_model->update('tb_solicitud_uso_credito', $row, array('iduso' => $existing->iduso));
                    if ($result === false) {
                        $db_error = $this->db->error();
                        echo json_encode(array('status' => FALSE, 'message' => 'Error al actualizar: ' . json_encode($db_error)));
                        return;
                    }
                } else {
                    $row['idsolicitud'] = $idsolicitud;
                    $result = $this->core_model->insert('tb_solicitud_uso_credito', $row, TRUE);
                    if ($result === false) {
                        $db_error = $this->db->error();
                        echo json_encode(array('status' => FALSE, 'message' => 'Error al insertar: ' . json_encode($db_error)));
                        return;
                    }
                }
                
                echo json_encode(array('status' => TRUE, 'message' => 'Uso de crédito guardado correctamente'));
            } catch (Exception $e) {
                echo json_encode(array('status' => FALSE, 'message' => 'Excepción: ' . $e->getMessage()));
            }
        }

        /**
         * AJAX: Guardar referencias (dos) para una solicitud
         * POST: idsolicitud, campos prefijados por referencia _1 y _2
         */
        // [Removed older duplicate: save_referencias_ajax]

        /**
         * AJAX: Eliminar foto de referencia
         * POST: idfoto
         */
        // [Removed older duplicate: delete_referencia_foto_ajax]

        /**
         * Descargar PDF con las dos referencias y sus fotos (si existen)
         */
        public function download_referencias($id = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado');
                redirect($this->router->fetch_class());
            }

            $refs = $this->core_model->get_by_id_all('tb_solicitud_referencias', array('idsolicitud' => $id));
            if (!is_array($refs)) $refs = array();

            // attach image sources if files exist (prefer file:// for dompdf stability)
            foreach ($refs as &$r) {
                $r->photo_front = null; $r->photo_back = null;
                $r->photo_front_src = null; $r->photo_back_src = null;
                $q = $this->db->get_where('tb_solicitud_referencias_fotos', array('idsolicitud' => $id, 'referencia_num' => $r->referencia_num));
                if ($q && $q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $abs = FCPATH . $row->filename;
                        if (file_exists($abs)) {
                            $abs_pdf = str_replace('\\', '/', $abs);
                            $file_src = preg_match('/^[A-Za-z]:\//', $abs_pdf) ? ('file:///' . $abs_pdf) : ('file://' . $abs_pdf);
                            $m = mime_content_type($abs);
                            $data = base64_encode(file_get_contents($abs));
                            $datauri = 'data:' . $m . ';base64,' . $data;
                            $tipo = strtolower(trim((string)($row->tipo ?? '')));
                            if ($tipo === 'front' || $tipo === 'frontal') {
                                $r->photo_front = $datauri;
                                $r->photo_front_src = $file_src;
                            }
                            if ($tipo === 'back' || $tipo === 'trasera' || $tipo === 'trasero') {
                                $r->photo_back = $datauri;
                                $r->photo_back_src = $file_src;
                            }
                        }
                    }
                }
            }

            $this->load->library('pdf');
            $data = array('referencias' => $refs, 'solicitud' => $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id)), 'generated_at' => date('d/m/Y H:i'));
            $html = $this->load->view('solicitudes/referencias_pdf', $data, TRUE);
            $filename = 'Referencias_solicitud_' . $id;
            $this->pdf->createPDF($html, $filename, TRUE);
        }

        /**
         * Descargar formato de Uso de Crédito en PDF para una solicitud
         * URL: /solicitudes/download_uso/{id}
         */
        public function download_uso($id = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado');
                redirect($this->router->fetch_class());
            }

            $this->load->library('pdf');

            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $id));
            // Validación en servidor: permitir descarga si hay datos en uso o en la solicitud inicial
            $hasData = false;
            if ($uso) {
                $fieldsToCheck = array(
                    'descripcion', 'fuente_ingreso', 'monto_estimado_mes', 'declaracion_nombre',
                    'declaracion_firma', 'declaracion_fecha', 'evaluador_credito', 'fecha_evaluacion', 'monto_solicitado', 'plazo_solicitado'
                );
                foreach ($fieldsToCheck as $f) {
                    if (isset($uso->$f) && $uso->$f !== null && trim((string) $uso->$f) !== '') {
                        if ($uso->$f === '0000-00-00') continue;
                        $hasData = true; break;
                    }
                }
            }

            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
            if (!$hasData && $solicitud) {
                // Check key fields on solicitud as fallback
                $solFields = array('monto_solicitado','plazo_meses','ingreso_mensual_neto','monto_solicitado','plazo_meses');
                foreach ($solFields as $sf) {
                    if (isset($solicitud->$sf) && $solicitud->$sf !== null && trim((string)$solicitud->$sf) !== '') { $hasData = true; break; }
                }
            }

            if (!$hasData) {
                $this->session->set_flashdata('info', 'Formato vacío: complete al menos un campo antes de descargar.');
                redirect($this->router->fetch_class() . '/uso_credito');
            }

            // If uso is missing but solicitud has data, build a temporary uso object from solicitud fields
            if (!$uso && $solicitud) {
                $uso = new stdClass();
                $uso->descripcion = isset($solicitud->detalle_inventario) ? $solicitud->detalle_inventario : '';
                $uso->fuente_ingreso = isset($solicitud->giro_negocio) ? $solicitud->giro_negocio : (isset($solicitud->actividad_economica) ? $solicitud->actividad_economica : '');
                $uso->monto_estimado_mes = isset($solicitud->ingreso_mensual_neto) ? $solicitud->ingreso_mensual_neto : (isset($solicitud->ventas_promedio_mensual) ? $solicitud->ventas_promedio_mensual : null);
                $uso->monto_solicitado = isset($solicitud->monto_solicitado) ? $solicitud->monto_solicitado : null;
                $uso->plazo_solicitado = isset($solicitud->plazo_meses) ? $solicitud->plazo_meses : (isset($solicitud->plazo) ? $solicitud->plazo : null);
            }

            // Normalize identification and contact fields so the PDF view receives a consistent property
            if ($solicitud) {
                $solicitud->numero_identificacion =
                    (isset($solicitud->numero_doc) && $solicitud->numero_doc) ? $solicitud->numero_doc : (
                    (isset($solicitud->numero_documento) && $solicitud->numero_documento) ? $solicitud->numero_documento : (
                    (isset($solicitud->cedula) && $solicitud->cedula) ? $solicitud->cedula : (
                    (isset($solicitud->identificacion) && $solicitud->identificacion) ? $solicitud->identificacion : null)));
                if ((!isset($solicitud->cedula) || !$solicitud->cedula) && isset($solicitud->numero_identificacion) && $solicitud->numero_identificacion) {
                    $solicitud->cedula = $solicitud->numero_identificacion;
                }
                if ((!isset($solicitud->telefono) || !$solicitud->telefono) && isset($solicitud->telefono_contacto) && $solicitud->telefono_contacto) {
                    $solicitud->telefono = $solicitud->telefono_contacto;
                }
                if ((!isset($solicitud->email) || !$solicitud->email) && isset($solicitud->correo_electronico) && $solicitud->correo_electronico) {
                    $solicitud->email = $solicitud->correo_electronico;
                }
                if ((!isset($solicitud->fecha_solicitud) || !$solicitud->fecha_solicitud) && isset($solicitud->fecha_recepcion) && $solicitud->fecha_recepcion) {
                    $solicitud->fecha_solicitud = $solicitud->fecha_recepcion;
                }
            }

            $data = array(
                'uso' => $uso,
                'solicitud' => $solicitud,
                'generated_at' => date('d/m/Y H:i')
            );

            $html = $this->load->view('solicitudes/uso_credito_pdf', $data, TRUE);
            $filename = 'UsoCredito_solicitud_' . $id;
            $this->pdf->createPDF($html, $filename, TRUE);
        }

        /**
         * Validación de Aprobacion: vista que muestra solicitudes para validar aprobaciones
         */
        public function validacion_aprobacion()
        {
            // load all solicitudes (reuse index behavior)
            $data = array(
                'titulo' => 'Validación de Aprobación',
                'subtitulo' => 'Revisión y registro de aprobaciones',
                'icono' => 'fas fa-check-square',
                'solicitudes' => $this->core_model->get_all('tb_solicitudes')
            );

            // annotate each solicitud with approval status for filtering (pending|approved|rejected|annulled)
            foreach ($data['solicitudes'] as $s) {
                $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $s->idsolicitud));
                $s->aprobado_por = '';
                $s->aprobado_usuario = '';
                if (empty($aprs)) {
                    $estado = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                    $s->aprob_status = (($estado === 'anulado' || $estado === 'annulled') ? 'annulled' : 'pending');
                } else {
                    usort($aprs, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                    $latest = $aprs[0];

                    // Mostrar en la tabla quién aprobó y la vía (Comite/Junta) según el último registro.
                    $s->aprobado_por = isset($latest->aprobado_por) ? (string)$latest->aprobado_por : '';
                    if (!empty($latest->username)) {
                        $s->aprobado_usuario = (string)$latest->username;
                    } elseif (!empty($latest->user_id)) {
                        $u = $this->core_model->get_by_id('users', array('id' => (int)$latest->user_id));
                        if ($u) {
                            $nombre = trim(((isset($u->first_name) ? $u->first_name : '') . ' ' . (isset($u->last_name) ? $u->last_name : '')));
                            if ($nombre !== '') {
                                $s->aprobado_usuario = $nombre;
                            } elseif (!empty($u->username)) {
                                $s->aprobado_usuario = (string)$u->username;
                            } elseif (!empty($u->email)) {
                                $s->aprobado_usuario = (string)$u->email;
                            }
                        }
                    }

                    $s->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                }

                $estado_sol = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                    $s->aprob_status = 'annulled';
                }

                // annotate whether a loan/plan already exists for this solicitud
                try {
                    $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $s->idsolicitud));
                    $s->has_plan = ($plan ? 1 : 0);
                    if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                        $s->aprob_status = 'annulled';
                    }
                } catch (Exception $e) {
                    $s->has_plan = 0;
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('solicitudes/validacion_aprobacion', $data);
            $this->load->view('layout/footer');
        }

        /**
         * Documento resumen (PDF) de aprobaciones/rechazos por rango de fechas.
         * Filtros GET:
         *  - fecha_inicio (YYYY-MM-DD)
         *  - fecha_fin (YYYY-MM-DD)
         *  - estado (all|approved|rejected|pending|annulled)
         */
        public function reporte_resumen_aprobaciones()
        {
            $fecha_inicio = trim((string)$this->input->get('fecha_inicio'));
            $fecha_fin = trim((string)$this->input->get('fecha_fin'));
            $estado = strtolower(trim((string)$this->input->get('estado')));
            if (!in_array($estado, array('all', 'approved', 'rejected', 'pending', 'annulled'), true)) {
                $estado = 'all';
            }

            $solicitudes = $this->db
                ->select('s.*, COALESCE(a.nombres, "") as nombre_asesor', false)
                ->from('tb_solicitudes s')
                ->join('tb_asesores a', 'a.idasesor = s.idasesor', 'left')
                ->order_by('s.idsolicitud', 'DESC')
                ->get()
                ->result();

            $rows = array();

            foreach ($solicitudes as $s) {
                $latest = $this->db
                    ->where('idsolicitud', $s->idsolicitud)
                    ->order_by('created_at', 'DESC')
                    ->order_by('idaprobacion', 'DESC')
                    ->limit(1)
                    ->get('tb_solicitud_aprobaciones')
                    ->row();

                $aprob_status = 'pending';
                $aprob_status_label = 'Pendiente';
                $fecha_decision = '';
                $via_aprobacion = '';
                $aprobado_usuario = '';

                if ($latest) {
                    $aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    if ($aprob_status === 'rejected') {
                        $aprob_status_label = 'Rechazado';
                    } elseif ($aprob_status === 'approved') {
                        $aprob_status_label = 'Aprobado';
                    } elseif ($aprob_status === 'annulled') {
                        $aprob_status_label = 'Anulado';
                    }

                    $fecha_decision = isset($latest->created_at) ? (string)$latest->created_at : '';
                    $via_aprobacion = isset($latest->aprobado_por) ? (string)$latest->aprobado_por : '';

                    if (!empty($latest->username)) {
                        $aprobado_usuario = (string)$latest->username;
                    } elseif (!empty($latest->user_id)) {
                        $u = $this->core_model->get_by_id('users', array('id' => (int)$latest->user_id));
                        if ($u) {
                            $nombre = trim(((isset($u->first_name) ? $u->first_name : '') . ' ' . (isset($u->last_name) ? $u->last_name : '')));
                            if ($nombre !== '') {
                                $aprobado_usuario = $nombre;
                            } elseif (!empty($u->username)) {
                                $aprobado_usuario = (string)$u->username;
                            } elseif (!empty($u->email)) {
                                $aprobado_usuario = (string)$u->email;
                            }
                        }
                    }
                }

                $estado_sol = isset($s->estado_aprobacion) ? strtolower((string)$s->estado_aprobacion) : '';
                if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                    $aprob_status = 'annulled';
                    $aprob_status_label = 'Anulado';
                }

                if ($estado !== 'all' && $aprob_status !== $estado) {
                    continue;
                }

                $fecha_ref = '';
                if ($fecha_decision !== '') {
                    $fecha_ref = substr($fecha_decision, 0, 10);
                } elseif (!empty($s->fecha_recepcion)) {
                    $fecha_ref = substr((string)$s->fecha_recepcion, 0, 10);
                } elseif (!empty($s->fecha_solicitud)) {
                    $fecha_ref = substr((string)$s->fecha_solicitud, 0, 10);
                }

                if ($fecha_inicio !== '' && $fecha_ref !== '' && $fecha_ref < $fecha_inicio) {
                    continue;
                }
                if ($fecha_fin !== '' && $fecha_ref !== '' && $fecha_ref > $fecha_fin) {
                    continue;
                }

                $tasa_val = null;
                if (isset($s->tasa_interes) && $s->tasa_interes !== '' && $s->tasa_interes !== null) {
                    $tasa_val = $s->tasa_interes;
                } elseif (isset($s->producto_tasa) && $s->producto_tasa !== '' && $s->producto_tasa !== null) {
                    $tasa_val = $s->producto_tasa;
                }

                $tasa_label = '';
                if ($tasa_val !== null && $tasa_val !== '') {
                    if (is_numeric($tasa_val)) {
                        $tasa_num = (float)$tasa_val;
                        if ($tasa_num > 0 && $tasa_num <= 1) {
                            $tasa_num *= 100;
                        }
                        $tasa_label = number_format($tasa_num, 2) . '%';
                    } else {
                        $tasa_label = (string)$tasa_val;
                    }
                }

                $plazo = '';
                if (isset($s->plazo_meses) && $s->plazo_meses !== '' && $s->plazo_meses !== null) {
                    $plazo = $s->plazo_meses;
                } elseif (isset($s->plazo) && $s->plazo !== '' && $s->plazo !== null) {
                    $plazo = $s->plazo;
                }

                $rows[] = (object) array(
                    'idsolicitud' => $s->idsolicitud,
                    'cliente' => trim((isset($s->apellidos) ? $s->apellidos : '') . ' ' . (isset($s->nombres) ? $s->nombres : '')),
                    'fecha_decision' => $fecha_decision,
                    'estado' => $aprob_status_label,
                    'via_aprobacion' => $via_aprobacion,
                    'aprobado_usuario' => $aprobado_usuario,
                    'monto_solicitado' => isset($s->monto_solicitado) ? $s->monto_solicitado : null,
                    'tasa' => $tasa_label,
                    'plazo' => $plazo,
                    'cuota_estimada' => isset($s->cuota_estim_estimada) ? $s->cuota_estim_estimada : null,
                    'destino_conami' => isset($s->destino_conami) ? $s->destino_conami : '',
                    'creado_por' => isset($s->nombre_asesor) ? $s->nombre_asesor : ''
                );
            }

            $data = array(
                'titulo' => 'Resumen de Creditos Aprobados/Rechazados',
                'rows' => $rows,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => $estado,
                'generado_en' => date('d/m/Y H:i')
            );

            $html = $this->load->view('solicitudes/reporte_resumen_aprobaciones', $data, TRUE);
            $filename = 'Resumen_Aprobaciones_' . date('Ymd_His');
            $this->pdf->createPDF($html, $filename, TRUE, 'A4', 'landscape');
        }

        /**
         * AJAX: obtener referencias para una solicitud
         */
        public function get_referencias_ajax($idsolicitud = null)
        {
            header('Content-Type: application/json; charset=utf-8');
            $idsolicitud = intval($idsolicitud ?: $this->input->get('idsolicitud'));
            if (! $idsolicitud) { echo json_encode(['status' => false, 'message' => 'Falta id de solicitud']); return; }
            $refs = $this->core_model->get_by_id_all('tb_solicitud_referencias', ['idsolicitud' => $idsolicitud]);
            if (!is_array($refs)) $refs = [];
            $out = [];
            foreach ($refs as $r) {
                $row = [
                    'idreferencia' => isset($r->idreferencia) ? $r->idreferencia : null,
                    'referencia_num' => isset($r->referencia_num) ? $r->referencia_num : null,
                    'nombre' => isset($r->nombre) ? $r->nombre : null,
                    'cedula' => isset($r->cedula) ? $r->cedula : null,
                    'direccion' => isset($r->direccion) ? $r->direccion : null,
                    'telefono' => isset($r->telefono) ? $r->telefono : null,
                    'tipo_referencia' => isset($r->tipo_referencia) ? $r->tipo_referencia : null,
                    'tipo_personal_relacion' => isset($r->tipo_personal_relacion) ? $r->tipo_personal_relacion : null,
                    'desde_conoce_cliente' => isset($r->desde_conoce_cliente) ? $r->desde_conoce_cliente : null,
                    'relacion_economica' => isset($r->relacion_economica) ? $r->relacion_economica : null,
                    'opinion' => isset($r->opinion) ? $r->opinion : null,
                    'comentarios' => isset($r->comentarios) ? $r->comentarios : null,
                    'photos' => new stdClass()
                ];
                // collect photos if table exists
                if ($this->db->table_exists('tb_solicitud_referencias_fotos')) {
                    $photos = $this->db->get_where('tb_solicitud_referencias_fotos', ['idsolicitud' => $idsolicitud, 'referencia_num' => $r->referencia_num])->result();
                    $pobj = new stdClass();
                    foreach ($photos as $p) {
                        $t = isset($p->tipo) ? $p->tipo : 'front';
                        $entry = ['id' => isset($p->idfoto)?$p->idfoto:null, 'url' => base_url(trim($p->filename, '/'))];
                        if ($t === 'front') $pobj->front = (object)$entry; else $pobj->back = (object)$entry;
                    }
                    $row['photos'] = $pobj;
                }
                $out[] = $row;
            }
            echo json_encode(['status' => true, 'referencias' => $out]);
        }

        /**
         * AJAX: guardar referencias (upsert) y fotos
         */
        public function save_referencias_ajax()
        {
            header('Content-Type: application/json; charset=utf-8');
            
            if (!$this->input->is_ajax_request() && empty($_POST) && empty($_FILES)) {
                echo json_encode(['status' => false, 'message' => 'Petición inválida']); 
                return;
            }
            
            $idsolicitud = intval($this->input->post('idsolicitud'));
            if (! $idsolicitud) { 
                echo json_encode(['status' => false, 'message' => 'Falta idsolicitud']); 
                return; 
            }

            if ($this->_is_solicitud_annulled($idsolicitud)) {
                echo json_encode(['status' => false, 'message' => 'La solicitud está anulada y no puede editarse.']);
                return;
            }
            
            // Verificar que la tabla existe
            if (!$this->db->table_exists('tb_solicitud_referencias')) {
                echo json_encode(['status' => false, 'message' => 'La tabla tb_solicitud_referencias no existe. Ejecute el script SQL correspondiente.']); 
                return;
            }

            $saved_any = false;
            $errors = [];
            
            for ($i = 1; $i <= 2; $i++) {
                $nombre = $this->input->post('nombre_'.$i);
                $cedula = $this->input->post('cedula_'.$i);
                $direccion = $this->input->post('direccion_'.$i);
                $telefono = $this->input->post('telefono_'.$i);
                $tipo = $this->input->post('tipo_'.$i);
                $tipo_personal_rel = $this->input->post('tipo_personal_relacion_'.$i);
                $desde = $this->input->post('desde_'.$i);
                $rel_econ = $this->input->post('relacion_'.$i);
                $opinion = $this->input->post('opinion_'.$i);
                $comentarios = $this->input->post('comentarios_'.$i);

                // normalize relacion_economica to nullable tinyint
                if ($rel_econ === '' || $rel_econ === null) $rel_econ = null; else $rel_econ = intval($rel_econ) ? 1 : 0;

                $row = [
                    'idsolicitud' => $idsolicitud,
                    'referencia_num' => $i,
                    'nombre' => $nombre,
                    'cedula' => $cedula,
                    'direccion' => $direccion,
                    'telefono' => $telefono,
                    'tipo_referencia' => $tipo,
                    'tipo_personal_relacion' => $tipo_personal_rel,
                    'desde_conoce_cliente' => $desde,
                    'relacion_economica' => $rel_econ,
                    'opinion' => $opinion,
                    'comentarios' => $comentarios
                ];

                // check existing
                $existing = $this->core_model->get_by_id('tb_solicitud_referencias', ['idsolicitud' => $idsolicitud, 'referencia_num' => $i]);
                if ($existing) {
                    // update
                    try {
                        $result = $this->core_model->update('tb_solicitud_referencias', $row, ['idreferencia' => $existing->idreferencia]);
                        if ($result) {
                            $saved_any = true;
                            $ref_id = $existing->idreferencia;
                        } else {
                            $errors[] = "Error al actualizar referencia $i";
                        }
                    } catch (Exception $e) {
                        $errors[] = "Excepción al actualizar referencia $i: " . $e->getMessage();
                    }
                } else {
                    try {
                        $ref_id = $this->core_model->insert('tb_solicitud_referencias', $row, TRUE);
                        if ($ref_id) {
                            $saved_any = true;
                        } else {
                            // Intentar obtener el último ID de la sesión como fallback
                            $ref_id = $this->session->userdata('last_id');
                            if ($ref_id) {
                                $saved_any = true;
                            } else {
                                $db_error = $this->db->error();
                                $errors[] = "Error al insertar referencia $i - DB Error: " . json_encode($db_error);
                            }
                        }
                    } catch (Exception $e) {
                        $errors[] = "Excepción al insertar referencia $i: " . $e->getMessage();
                    }
                }

                // handle photos for this referencia
                if (! empty($_FILES)) {
                    $upload_base = FCPATH . 'uploads/solicitudes/solicitud_' . $idsolicitud . '/referencias/referencia_' . $i . '/';
                    if (! is_dir($upload_base)) @mkdir($upload_base, 0755, true);
                    // front
                    $field_front = 'cedula_front_' . $i;
                    if (isset($_FILES[$field_front]) && $_FILES[$field_front]['error'] === UPLOAD_ERR_OK) {
                        $tmp = $_FILES[$field_front];
                        $ext = pathinfo($tmp['name'], PATHINFO_EXTENSION);
                        $basename = time() . '_' . bin2hex(random_bytes(4));
                        $fname = $basename . '_front.' . $ext;
                        $dest = $upload_base . $fname;
                        if (move_uploaded_file($tmp['tmp_name'], $dest)) {
                            if ($this->db->table_exists('tb_solicitud_referencias_fotos')) {
                                $insert = ['idsolicitud' => $idsolicitud, 'idreferencia' => $ref_id, 'referencia_num' => $i, 'tipo' => 'front', 'filename' => 'uploads/solicitudes/solicitud_' . $idsolicitud . '/referencias/referencia_' . $i . '/' . $fname];
                                try { $this->db->insert('tb_solicitud_referencias_fotos', $insert); } catch (Exception $e) {}
                            }
                        }
                    }
                    // back
                    $field_back = 'cedula_back_' . $i;
                    if (isset($_FILES[$field_back]) && $_FILES[$field_back]['error'] === UPLOAD_ERR_OK) {
                        $tmp = $_FILES[$field_back];
                        $ext = pathinfo($tmp['name'], PATHINFO_EXTENSION);
                        $basename = time() . '_' . bin2hex(random_bytes(4));
                        $fname = $basename . '_back.' . $ext;
                        $dest = $upload_base . $fname;
                        if (move_uploaded_file($tmp['tmp_name'], $dest)) {
                            if ($this->db->table_exists('tb_solicitud_referencias_fotos')) {
                                $insert = ['idsolicitud' => $idsolicitud, 'idreferencia' => $ref_id, 'referencia_num' => $i, 'tipo' => 'back', 'filename' => 'uploads/solicitudes/solicitud_' . $idsolicitud . '/referencias/referencia_' . $i . '/' . $fname];
                                try { $this->db->insert('tb_solicitud_referencias_fotos', $insert); } catch (Exception $e) {}
                            }
                        }
                    }
                }
            }

            if ($saved_any) {
                echo json_encode(['status' => true, 'message' => 'Referencias guardadas correctamente']);
            } else {
                $error_msg = 'No se pudieron guardar las referencias';
                if (!empty($errors)) {
                    $error_msg .= ': ' . implode('; ', $errors);
                }
                echo json_encode(['status' => false, 'message' => $error_msg, 'errors' => $errors]);
            }
        }

        /**
         * AJAX: eliminar foto de referencia por idfoto
         */
        public function delete_referencia_foto_ajax()
        {
            header('Content-Type: application/json; charset=utf-8');
            $idfoto = intval($this->input->post('idfoto'));
            if (! $idfoto) { echo json_encode(['status' => false, 'message' => 'Falta idfoto']); return; }
            $row = $this->db->get_where('tb_solicitud_referencias_fotos', ['idfoto' => $idfoto])->row();
            if (! $row) { echo json_encode(['status' => false, 'message' => 'Foto no encontrada']); return; }
            $filepath = FCPATH . ltrim($row->filename, '/');
            try { $this->db->where('idfoto', $idfoto)->delete('tb_solicitud_referencias_fotos'); } catch (Exception $e) { echo json_encode(['status' => false, 'message' => 'Error al eliminar']); return; }
            if (file_exists($filepath)) @unlink($filepath);
            echo json_encode(['status' => true]);
        }

        /**
         * Endpoint: devuelve las aprobaciones registradas para una solicitud
         * GET: /solicitudes/get_aprobaciones_ajax/{id}
         */
        public function get_aprobaciones_ajax($id = NULL)
        {
            // allow non-AJAX GETs
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada', 'aprobaciones' => array(), 'propuestas' => array())));
                return;
            }

            $aprobaciones = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $id));
            if (!is_array($aprobaciones)) {
                $aprobaciones = array();
            } else {
                usort($aprobaciones, function($a, $b){
                    $ta = isset($a->created_at) ? strtotime($a->created_at) : 0;
                    $tb = isset($b->created_at) ? strtotime($b->created_at) : 0;
                    return $tb - $ta;
                });
            }

            // Also fetch propuesta_tipos saved on the solicitud (JSON array stored as TEXT)
            $propuestas = array();
            try {
                $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
                if ($sol && !empty($sol->propuesta_tipos)) {
                    $ids = json_decode($sol->propuesta_tipos, true);
                    if (is_array($ids) && count($ids) > 0) {
                        // Fetch matching tipo_productos rows - table uses `id` as PK
                        $this->db->where_in('id', $ids);
                        $rows = $this->db->get('tb_tipo_productos')->result();
                        if (is_array($rows)) {
                            // preserve the order of ids as stored
                            $map = array();
                            foreach ($rows as $r) { $map[$r->id] = $r; }
                            $ordered = array();
                            foreach ($ids as $iid) { if (isset($map[$iid])) $ordered[] = $map[$iid]; }
                            $propuestas = $ordered;
                            // annotate each propuesta with the original plazo (plazo_meses) from the solicitud
                            // so the approval modal displays the requested plazo instead of the product's default
                            try {
                                $plazo_from_solicitud = isset($sol->plazo_meses) ? $sol->plazo_meses : null;
                                $monto_from_solicitud = isset($sol->monto_solicitado) ? $sol->monto_solicitado : null;
                                $tasa_from_solicitud = isset($sol->producto_tasa) && $sol->producto_tasa !== '' ? $sol->producto_tasa : (isset($sol->tasa_interes) ? $sol->tasa_interes : null);
                                $com_from_solicitud = isset($sol->producto_comision) && $sol->producto_comision !== '' ? $sol->producto_comision : (isset($sol->comision_desembolso) ? $sol->comision_desembolso : null);
                                
                                foreach ($propuestas as &$pp) {
                                    // Siempre usar el plazo de la solicitud original, no el del producto
                                    if ($plazo_from_solicitud !== null) {
                                        $pp->plazo_solicitado = $plazo_from_solicitud;
                                    }
                                    // Asignar otros valores de la solicitud si están disponibles
                                    if ($monto_from_solicitud !== null) {
                                        $pp->monto_solicitado = $monto_from_solicitud;
                                    }
                                    if ($tasa_from_solicitud !== null) {
                                        $pp->tasa_mensual = $tasa_from_solicitud;
                                    }
                                    if ($com_from_solicitud !== null) {
                                        $pp->comision_desembolso = $com_from_solicitud;
                                    }
                                }
                                unset($pp);
                            } catch (Exception $e) {
                                // ignore annotation errors
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore and return empty propuestas on error
                $propuestas = array();
            }

            // Fetch garantias for this solicitud
            $garantias_info = array();
            $total_garantias = 0;
            try {
                if ($this->db->table_exists('tb_garantias')) {
                    $garantias = $this->db->order_by('id','DESC')->get_where('tb_garantias', array('solicitud_id' => $id))->result();
                    if (is_array($garantias) && count($garantias) > 0) {
                        foreach ($garantias as $g) {
                            $garantias_info[] = array(
                                'nombre' => isset($g->nombre) ? $g->nombre : '',
                                'cantidad' => isset($g->cantidad) ? $g->cantidad : 1,
                                'marca' => isset($g->marca) ? $g->marca : '',
                                'modelo' => isset($g->modelo) ? $g->modelo : '',
                                'costo' => isset($g->costo) ? floatval($g->costo) : 0
                            );
                            $total_garantias += (isset($g->costo) ? floatval($g->costo) : 0) * (isset($g->cantidad) ? intval($g->cantidad) : 1);
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore errors and return empty garantias
            }

            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'aprobaciones' => $aprobaciones, 'propuestas' => $propuestas, 'garantias' => $garantias_info, 'total_garantias' => $total_garantias)));
        }

        /**
         * Endpoint: guarda aprobaciones (puede recibir un rol+comentario o los 3 campos en bulk)
         * POST: idsolicitud, role, comment
         * OR: idsolicitud, comite_interno, comite_externo, gerencia_administrativa
         */
        public function add_aprobacion_ajax()
        {
            header('Content-Type: application/json; charset=utf-8');
            
            if (!$this->input->is_ajax_request()) {
                // allow POST from regular forms too, but prefer AJAX
            }

            $idsolicitud = $this->input->post('idsolicitud');
            if (!$idsolicitud || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                echo json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada'));
                return;
            }
            
            // Verificar que la tabla existe
            if (!$this->db->table_exists('tb_solicitud_aprobaciones')) {
                echo json_encode(['status' => false, 'message' => 'La tabla tb_solicitud_aprobaciones no existe. Ejecute el script SQL correspondiente.']);
                return;
            }

            $u = $this->ion_auth->user()->row();
            $user_id = ($u ? $u->id : 0);
            $username = ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema');

            $saved_any = false;
            $errors = [];

            // single role mode
            $role = $this->input->post('role');
            $comment = trim($this->input->post('comment'));
            if ($role && $comment !== '') {
                $row = array(
                    'idsolicitud' => $idsolicitud,
                    'role' => $role,
                    'user_id' => $user_id,
                    'username' => $username,
                    'comment' => $comment,
                    'aprobado_por' => $this->input->post('aprobado_por') ?: null
                );
                try {
                    $result = $this->core_model->insert('tb_solicitud_aprobaciones', $row, TRUE);
                    if ($result) {
                        $saved_any = true;
                    } else {
                        $last = $this->session->userdata('last_id');
                        if ($last) {
                            $saved_any = true;
                        } else {
                            $db_error = $this->db->error();
                            $errors[] = "Error al insertar aprobación - DB Error: " . json_encode($db_error);
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = "Excepción al insertar aprobación: " . $e->getMessage();
                }
            } else {
                // bulk mode: three fields
                $fields = array(
                    'Comite Interno' => trim($this->input->post('comite_interno')),
                    'Comite Externo' => trim($this->input->post('comite_externo')),
                    'Gerencia Administrativa' => trim($this->input->post('gerencia_administrativa'))
                );
                foreach ($fields as $r => $c) {
                    if ($c !== '') {
                        $row = array(
                            'idsolicitud' => $idsolicitud,
                            'role' => $r,
                            'user_id' => $user_id,
                            'username' => $username,
                            'comment' => $c
                        );
                        try {
                            $result = $this->core_model->insert('tb_solicitud_aprobaciones', $row, TRUE);
                            if ($result) {
                                $saved_any = true;
                            } else {
                                $last = $this->session->userdata('last_id');
                                if ($last) {
                                    $saved_any = true;
                                } else {
                                    $db_error = $this->db->error();
                                    $errors[] = "Error al insertar $r - DB Error: " . json_encode($db_error);
                                }
                            }
                        } catch (Exception $e) {
                            $errors[] = "Excepción al insertar $r: " . $e->getMessage();
                        }
                    }
                }
            }

            if ($saved_any) {
                echo json_encode(array('status' => true, 'message' => 'Aprobaciones guardadas correctamente'));
            } else {
                $error_msg = 'No se pudo guardar ninguna aprobación';
                if (!empty($errors)) {
                    $error_msg .= ': ' . implode('; ', $errors);
                }
                echo json_encode(array('status' => false, 'message' => $error_msg, 'errors' => $errors));
            }
        }

        /**
         * AJAX: submit a single validation decision (approve/reject) with optional photo
         * Expects POST: idsolicitud, decision (approve|reject), comment, optional file field 'photo'
         */
        public function submit_validacion_ajax()
        {
            try {
                // allow non-AJAX POSTs too
                $idsolicitud = $this->input->post('idsolicitud');
                $decision = $this->input->post('decision');
                $comment = trim($this->input->post('comment'));

                if (!$idsolicitud || !$decision || ($comment === '')) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
                    return;
                }

                if (!$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
                    return;
                }

                // Check if table exists and has AUTO_INCREMENT
                $table_info = $this->db->query("SHOW CREATE TABLE tb_solicitud_aprobaciones")->row_array();
                if (!$table_info || !preg_match('/AUTO_INCREMENT/i', $table_info['Create Table'])) {
                    log_message('error', 'Table tb_solicitud_aprobaciones missing AUTO_INCREMENT on idaprobacion');
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => FALSE, 
                        'message' => 'Error de configuración: tabla sin AUTO_INCREMENT'
                    )));
                    return;
                }

                // prevent multiple validation decisions by same role: allow multiple historic entries but UI considers first non-empty
                $u = $this->ion_auth->user()->row();
                $user_id = ($u ? $u->id : 0);
                $username = ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema');

                // optional photo handling: if provided, save to uploads/solicitudes/{id}/ and record in fotos table
                $photo_path = null;
                if (!empty($_FILES) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['photo'];
                    $allowed = array('image/jpeg', 'image/jpg', 'image/png');
                    $maxBytes = 5 * 1024 * 1024;
                    if ($file['size'] <= $maxBytes && in_array($file['type'], $allowed)) {
                        $upload_dir = FCPATH . 'uploads/solicitudes/' . intval($idsolicitud) . '/';
                        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $basename = time() . '_' . bin2hex(random_bytes(6));
                        $filename = $basename . '.' . $ext;
                        $dest = $upload_dir . $filename;
                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            // save to fotos table
                            $row = array(
                                'idsolicitud' => $idsolicitud,
                                'filename' => 'solicitudes/' . intval($idsolicitud) . '/' . $filename,
                                'mime' => $file['type'],
                                'size' => (int)$file['size']
                            );
                            $this->core_model->insert('tb_solicitud_photos', $row, TRUE);
                            $photo_path = $row['filename'];
                        }
                    }
                }

                // prevent multiple validation decisions (role 'Validación')
                $existing_validacion = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $idsolicitud, 'role' => 'Validación'));
                if (!empty($existing_validacion)) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Ya existe una validación para esta solicitud.')));
                    return;
                }

                // build approval record
                $role = 'Validación';
                $full_comment = '[' . ($decision === 'approve' ? 'Aprobado' : 'Rechazado') . '] ' . $comment;
                if ($photo_path) {
                    $full_comment .= ' [foto:' . $photo_path . ']';
                }

                $ins = array(
                    'idsolicitud' => $idsolicitud,
                    'role' => $role,
                    'user_id' => $user_id,
                    'username' => $username,
                    'comment' => $full_comment,
                    'aprobado_por' => $this->input->post('aprobado_por') ?: null,
                    // store any propuesta overrides provided by the approver (JSON string)
                    'propuesta_overrides' => $this->input->post('propuesta_overrides') ? $this->input->post('propuesta_overrides') : null
                );

                $this->core_model->insert('tb_solicitud_aprobaciones', $ins, TRUE);
                $last = $this->session->userdata('last_id');

                // Log insert result for debugging
                if (!$last || $last <= 0) {
                    log_message('error', 'Insert to tb_solicitud_aprobaciones failed or returned invalid ID: ' . var_export($last, true));
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => FALSE, 
                        'message' => 'Error al insertar la validación en la base de datos'
                    )));
                    return;
                }

                // Update solicitud estado_aprobacion field so other lists can reflect status
                if ($last) {
                    if ($decision === 'approve') {
                        $this->db->where('idsolicitud', $idsolicitud)->update('tb_solicitudes', array('estado_aprobacion' => 'aprobado'));
                    } else {
                        $this->db->where('idsolicitud', $idsolicitud)->update('tb_solicitudes', array('estado_aprobacion' => 'rechazado'));
                    }

                    // Persist propuestas for the solicitud so next step (Plan de Pago) can use them.
                    // Only persist when the decision was APPROVE.
                    if ($decision === 'approve') {
                        try {
                            log_message('debug', 'submit_validacion_ajax: Starting to persist propuestas for solicitud ' . $idsolicitud);
                            
                            // remove existing propuestas for this solicitud
                            $this->core_model->delete('tb_solicitud_propuestas', array('idsolicitud' => $idsolicitud));

                            // get selected product ids from solicitud.propuesta_tipos
                            $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
                            $selected_ids = array();
                            if ($sol && !empty($sol->propuesta_tipos)) {
                                $selected_ids = json_decode($sol->propuesta_tipos, true);
                                if (!is_array($selected_ids)) $selected_ids = array();
                            }
                            
                            log_message('debug', 'submit_validacion_ajax: selected_ids = ' . json_encode($selected_ids));

                            // overrides sent with approval (optional)
                            $overrides = array();
                            $raw_over = $this->input->post('propuesta_overrides');
                            log_message('debug', 'submit_validacion_ajax: raw propuesta_overrides = ' . var_export($raw_over, true));
                            
                            if ($raw_over) {
                                $decoded = json_decode($raw_over, true);
                                if (is_array($decoded)) {
                                    foreach ($decoded as $o) {
                                        if (isset($o['id'])) {
                                            // normalize keys: allow 'comision' or 'comision_desembolso'
                                            $overrides[$o['id']] = $o;
                                        }
                                    }
                                }
                            }
                            
                            log_message('debug', 'submit_validacion_ajax: processed overrides = ' . json_encode($overrides));

                            if (!empty($selected_ids)) {
                                log_message('debug', 'submit_validacion_ajax: Fetching products for ' . count($selected_ids) . ' IDs');
                                
                                // fetch product rows for selected ids
                                $this->db->where_in('id', $selected_ids);
                                $prod_rows = $this->db->get('tb_tipo_productos')->result();
                                $map = array();
                                foreach ($prod_rows as $pr) { $map[$pr->id] = $pr; }
                                
                                log_message('debug', 'submit_validacion_ajax: Found ' . count($prod_rows) . ' products');

                                foreach ($selected_ids as $pid) {
                                    if (!isset($map[$pid])) continue;
                                    $prod = $map[$pid];
                                    log_message('debug', 'submit_validacion_ajax: Processing product ID ' . $pid);
                                    
                                    // if override exists use it; otherwise use product defaults
                                    $monto = null;
                                    $tasa = null;
                                    if (isset($overrides[$pid])) {
                                        $o = $overrides[$pid];
                                        $monto = (array_key_exists('monto', $o) && ($o['monto'] !== null && $o['monto'] !== '')) ? $o['monto'] : $monto;
                                        $tasa = (array_key_exists('tasa', $o) && ($o['tasa'] !== null && $o['tasa'] !== '')) ? $o['tasa'] : $tasa;
                                        $plazo_override = (array_key_exists('plazo', $o) && ($o['plazo'] !== null && $o['plazo'] !== '')) ? $o['plazo'] : null;
                                        $com_override = (array_key_exists('comision', $o) && ($o['comision'] !== null && $o['comision'] !== '')) ? $o['comision'] : (array_key_exists('comision_desembolso', $o) ? $o['comision_desembolso'] : null);
                                    } else {
                                        $plazo_override = null;
                                        $com_override = null;
                                    }
                                    if ($monto === null) {
                                        // prefer monto_min if present, else monto_max, else null
                                        if (isset($prod->monto_min) && $prod->monto_min !== null && $prod->monto_min !== '') $monto = $prod->monto_min;
                                        elseif (isset($prod->monto_max) && $prod->monto_max !== null && $prod->monto_max !== '') $monto = $prod->monto_max;
                                    }
                                    if ($tasa === null) {
                                        if (isset($prod->tasa_mensual) && $prod->tasa_mensual !== null && $prod->tasa_mensual !== '') $tasa = $prod->tasa_mensual;
                                    }
                                    // if overrides for plazo/comision exist, use them; otherwise product defaults
                                    $row = array(
                                        'idsolicitud' => $idsolicitud,
                                        'idtipo_producto' => $pid,
                                        'monto' => $monto,
                                        'tasa' => $tasa,
                                        'comision_desembolso' => ($com_override !== null ? $com_override : (isset($prod->comision_desembolso) ? $prod->comision_desembolso : null)),
                                        'plazo_min' => (isset($prod->plazo_min) ? $prod->plazo_min : null),
                                        'plazo_max' => ($plazo_override !== null ? $plazo_override : (isset($prod->plazo_max) ? $prod->plazo_max : null))
                                    );
                                    
                                    log_message('debug', 'submit_validacion_ajax: Inserting propuesta: ' . json_encode($row));
                                    $this->core_model->insert('tb_solicitud_propuestas', $row);
                                    $inserted_id = $this->session->userdata('last_id');
                                    log_message('debug', 'submit_validacion_ajax: Inserted propuesta with ID: ' . var_export($inserted_id, true));

                                    // If overrides had comments for this producto, persist history rows
                                    if (isset($overrides[$pid]) && is_array($overrides[$pid]) && isset($overrides[$pid]['comments']) && is_array($overrides[$pid]['comments'])) {
                                        $oc = $overrides[$pid]['comments'];
                                        // fields map: key in comments => human field name
                                        $fields_map = array('monto' => 'monto', 'tasa' => 'tasa', 'plazo' => 'plazo_max', 'comision' => 'comision_desembolso');
                                        foreach ($fields_map as $ck => $dbfield) {
                                            if (array_key_exists($ck, $oc) && trim($oc[$ck]) !== '') {
                                                $oldv = null;
                                                if ($ck === 'monto') {
                                                    $oldv = (isset($prod->monto_min) && $prod->monto_min !== null ? $prod->monto_min : (isset($prod->monto_max) ? $prod->monto_max : null));
                                                } elseif ($ck === 'tasa') { $oldv = isset($prod->tasa_mensual) ? $prod->tasa_mensual : null; }
                                                elseif ($ck === 'plazo') { $oldv = isset($prod->plazo_max) ? $prod->plazo_max : null; }
                                                elseif ($ck === 'comision') { $oldv = isset($prod->comision_desembolso) ? $prod->comision_desembolso : null; }

                                                $historyRow = array(
                                                    'idsolicitud' => $idsolicitud,
                                                    'idtipo_producto' => $pid,
                                                    'field_name' => $ck,
                                                    'old_value' => ($oldv !== null ? (string)$oldv : null),
                                                    'new_value' => (isset($overrides[$pid][$ck]) ? (string)$overrides[$pid][$ck] : null),
                                                    'comment' => $oc[$ck],
                                                    'user_id' => $user_id,
                                                    'username' => $username
                                                );
                                                $this->core_model->insert('tb_solicitud_propuestas_history', $historyRow);
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            log_message('error', 'Error persisting solicitud propuestas for id ' . $idsolicitud . ': ' . $e->getMessage());
                        }
                    }
                }

                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => TRUE, 
                    'message' => 'Decisión registrada exitosamente',
                    'idaprobacion' => $last
                )));
            } catch (Exception $e) {
                log_message('error', 'Error in submit_validacion_ajax: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => FALSE, 
                    'message' => 'Error al guardar la decisión: ' . $e->getMessage()
                )));
            }
        }

        /**
         * AJAX: anular crédito aprobado.
         * Reglas: comentario obligatorio, sin pagos y sin desembolso ejecutado.
         */
        public function anular_credito_ajax()
        {
            $this->output->set_content_type('application/json');

            try {
                $idsolicitud = intval($this->input->post('idsolicitud'));
                $comment = trim((string)$this->input->post('comment'));

                if ($idsolicitud <= 0) {
                    $this->output->set_output(json_encode(array('status' => false, 'message' => 'Parámetros incompletos.')));
                    return;
                }

                if ($comment === '' || strlen($comment) < 3) {
                    $this->output->set_output(json_encode(array('status' => false, 'message' => 'El comentario de anulación es obligatorio (mínimo 3 caracteres).')));
                    return;
                }

                $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud));
                if (!$sol) {
                    $this->output->set_output(json_encode(array('status' => false, 'message' => 'Solicitud no encontrada.')));
                    return;
                }

                if (isset($sol->estado_aprobacion) && strtolower((string)$sol->estado_aprobacion) === 'anulado') {
                    $this->output->set_output(json_encode(array('status' => false, 'message' => 'La solicitud ya está anulada.')));
                    return;
                }

                $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $idsolicitud));
                if ($prestamo) {
                    $ya_desembolsado = (
                        (isset($prestamo->desembolsado) && intval($prestamo->desembolsado) === 1) ||
                        (!empty($prestamo->fecha_desembolso_real))
                    );

                    if ($ya_desembolsado) {
                        $this->output->set_output(json_encode(array('status' => false, 'message' => 'No se puede anular porque el crédito ya fue desembolsado.')));
                        return;
                    }

                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $prestamo->idprestamo);
                    if ($this->db->field_exists('anulado', 'tb_prestamo_pagos')) {
                        $this->db->where('(anulado IS NULL OR anulado = 0)', null, false);
                    }
                    $pagos_count = (int)$this->db->count_all_results();
                    if ($pagos_count > 0) {
                        $this->output->set_output(json_encode(array('status' => false, 'message' => 'No se puede anular porque ya registra pagos.')));
                        return;
                    }
                }

                $u = $this->ion_auth->user()->row();
                $user_id = ($u ? (int)$u->id : 0);
                $username = ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema');

                $this->db->trans_begin();

                if ($this->db->field_exists('estado_aprobacion', 'tb_solicitudes')) {
                    $this->core_model->update('tb_solicitudes', array('estado_aprobacion' => 'anulado'), array('idsolicitud' => $idsolicitud));
                }

                if ($prestamo) {
                    $upd_prestamo = array();
                    if ($this->db->field_exists('estado', 'tb_prestamos')) {
                        $upd_prestamo['estado'] = 2; // 2 = anulado
                    }
                    if ($this->db->field_exists('obs_desembolso', 'tb_prestamos')) {
                        $upd_prestamo['obs_desembolso'] = 'ANULADO: ' . $comment;
                    }
                    if (!empty($upd_prestamo)) {
                        $this->core_model->update('tb_prestamos', $upd_prestamo, array('idprestamo' => $prestamo->idprestamo));
                    }
                }

                if ($this->db->table_exists('tb_solicitud_aprobaciones')) {
                    $ins = array(
                        'idsolicitud' => $idsolicitud,
                        'role' => 'Anulación',
                        'user_id' => $user_id,
                        'username' => $username,
                        'comment' => '[Anulado] ' . $comment,
                        'aprobado_por' => 'Anulación'
                    );
                    $this->core_model->insert('tb_solicitud_aprobaciones', $ins, TRUE);
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->output->set_output(json_encode(array('status' => false, 'message' => 'No se pudo completar la anulación.')));
                    return;
                }

                $this->db->trans_commit();
                $this->output->set_output(json_encode(array('status' => true, 'message' => 'Crédito anulado correctamente.')));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(array('status' => false, 'message' => 'Error al anular: ' . $e->getMessage())));
            }
        }

        /**
         * Generar/descargar PDF de la Solicitud Inicial
         * URL: /solicitudes/download_solicitud_pdf/{id}
         */
        public function download_solicitud_pdf($id = NULL)
        {
            if (!$id) { show_404(); }
            $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
            $this->db->from('tb_solicitudes');
            $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
            $this->db->where('tb_solicitudes.idsolicitud', (int)$id);
            $sol = $this->db->get()->row();
            if (!$sol) { $this->session->set_flashdata('error', 'Solicitud no encontrada'); redirect('solicitudes'); }

            // cargar propuestas persistidas (si existen)
            $propuestas = array();
            if (!empty($sol->propuesta_tipos)) {
                $ids = json_decode($sol->propuesta_tipos, true);
                if (is_array($ids) && count($ids)) {
                    $this->db->where_in('id', $ids);
                    $propuestas = $this->db->get('tb_tipo_productos')->result();
                }
            }


            // Exponer nombre del asesor/ruta para el PDF
            if (isset($sol->idasesor) && $sol->idasesor) {
                $asesor_row = $this->db->get_where('tb_asesores', array('idasesor' => $sol->idasesor))->row();
                if ($asesor_row && isset($asesor_row->nombres)) {
                    $sol->asesor = $asesor_row->nombres;
                } else {
                    $sol->asesor = $sol->idasesor;
                }
            } elseif (isset($sol->ruta)) {
                $sol->asesor = $sol->ruta;
            } else {
                $sol->asesor = '';
            }
            // Alias para la vista PDF (no sobrescribir si ya vino del JOIN)
            if (empty($sol->nombre_asesor)) {
                $sol->nombre_asesor = $sol->asesor;
            }

            $data = array('solicitud' => $sol, 'propuestas' => $propuestas);
            // ensure aliases and flags the PDF expects are present
            $this->_expose_pdf_aliases($sol);
            $this->_expose_pdf_flags_and_products($sol, $propuestas);
            $data['solicitud'] = $sol;
            $html = $this->load->view('solicitudes/print_solicitud_pdf', $data, TRUE);

            if (isset($this->pdf) && method_exists($this->pdf, 'load_html')) {
                $this->pdf->load_html($html);
                $this->pdf->render();
                $filename = 'Solicitud_' . $sol->idsolicitud . '.pdf';
                // Forzar descarga del PDF en lugar de mostrar inline
                $this->pdf->stream($filename, array('Attachment' => 1));
                return;
            }

            // fallback: show a small helper page with a 'Descargar PDF' button that forces generation
            $forceUrl = base_url('solicitudes/download_solicitud_pdf_force/' . intval($sol->idsolicitud));
            $viewUrl = base_url('solicitudes/download_solicitud_pdf/' . intval($sol->idsolicitud));
            $msg = '<!doctype html><html><head><meta charset="utf-8"><title>Solicitud '.intval($sol->idsolicitud).'</title></head><body style="font-family:Arial,Helvetica,sans-serif; padding:18px;">';
            $msg .= '<h2>Solicitud Inicial: #' . intval($sol->idsolicitud) . '</h2>';
            $msg .= '<p>No se pudo generar el PDF automáticamente en este request. Use el botón para forzar la descarga usando el generador interno.</p>';
            $msg .= '<p><a href="' . $forceUrl . '" class="btn" style="display:inline-block;padding:10px 14px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;">Descargar PDF</a></p>';
            $msg .= '<hr/><p>Si desea ver la versión HTML: <a href="' . $viewUrl . '" target="_blank">Abrir versión HTML</a></p>';
            $msg .= '</body></html>';
            echo $msg;
            return;
        }

        /**
         * Forzar generación y descarga del PDF usando la librería interna Pdf (dompdf)
         * URL: /solicitudes/download_solicitud_pdf_force/{id}
         */
        public function download_solicitud_pdf_force($id = NULL)
        {
            if (!$id) { show_404(); }
            $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
            $this->db->from('tb_solicitudes');
            $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
            $this->db->where('tb_solicitudes.idsolicitud', (int)$id);
            $sol = $this->db->get()->row();
            if (!$sol) { $this->session->set_flashdata('error', 'Solicitud no encontrada'); redirect('solicitudes'); }

            // cargar propuestas persistidas (si existen)
            $propuestas = array();
            if (!empty($sol->propuesta_tipos)) {
                $ids = json_decode($sol->propuesta_tipos, true);
                if (is_array($ids) && count($ids)) {
                    $this->db->where_in('id', $ids);
                    $propuestas = $this->db->get('tb_tipo_productos')->result();
                }
            }


            // Exponer nombre del asesor/ruta para el PDF (igual que en download_solicitud_pdf)
            if (isset($sol->idasesor) && $sol->idasesor) {
                $asesor_row = $this->db->get_where('tb_asesores', array('idasesor' => $sol->idasesor))->row();
                if ($asesor_row && isset($asesor_row->nombres)) {
                    $sol->asesor = $asesor_row->nombres;
                } else {
                    $sol->asesor = $sol->idasesor;
                }
            } elseif (isset($sol->ruta)) {
                $sol->asesor = $sol->ruta;
            } else {
                $sol->asesor = '';
            }
            if (empty($sol->nombre_asesor)) {
                $sol->nombre_asesor = $sol->asesor;
            }

            $data = array('solicitud' => $sol, 'propuestas' => $propuestas);
            // ensure aliases and flags the PDF expects are present
            $this->_expose_pdf_aliases($sol);
            $this->_expose_pdf_flags_and_products($sol, $propuestas);
            $data['solicitud'] = $sol;
            $html = $this->load->view('solicitudes/print_solicitud_pdf', $data, TRUE);

            // Try to use the app's Pdf library (application/libraries/Pdf.php)
            try {
                $this->load->library('pdf');
                if (isset($this->pdf) && method_exists($this->pdf, 'createPDF')) {
                    $filename = 'Solicitud_' . intval($sol->idsolicitud);
                    // createPDF will stream and exit
                    $this->pdf->createPDF($html, $filename, TRUE, 'A4', 'portrait');
                    return;
                }
            } catch (Exception $e) {
                // ignore and fallthrough to error handling
            }

            // If we reach here, pdf generation failed — inform the user and redirect
            $this->session->set_flashdata('error', 'No se pudo generar el PDF (librería no disponible).');
            redirect('solicitudes');
        }

        /**
         * Descargar aprobaciones en PDF para una solicitud
         * URL: /solicitudes/download_aprobaciones/{id}
         */
        public function download_aprobaciones($id = NULL)
        {
            if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado');
                redirect($this->router->fetch_class());
            }

            $aprobaciones = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $id));
            if (empty($aprobaciones)) {
                $this->session->set_flashdata('info', 'Pendiente de aprobación: no hay aprobaciones para descargar.');
                redirect($this->router->fetch_class() . '/validacion_aprobacion');
            }

            // determine overall status label & color based on latest aprobacion comment
            $status_label = 'Aprobaciones';
            $status_color = '#0b3d91';
            try {
                if (is_array($aprobaciones) && count($aprobaciones) > 0) {
                    usort($aprobaciones, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                    $latest = $aprobaciones[0];
                    $txt = isset($latest->comment) ? strtolower($latest->comment) : '';
                    if (strpos($txt, 'rechaz') !== false) {
                        $status_label = 'Rechazado';
                        $status_color = '#c82333';
                    } elseif (strpos($txt, 'aprob') !== false) {
                        $status_label = 'Aprobado';
                        $status_color = '#28a745';
                    } else {
                        $status_label = 'Pendiente';
                        $status_color = '#0b3d91';
                    }
                }
            } catch (Exception $e) { /* ignore */ }

            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));

            $data = array(
                'solicitud' => $solicitud,
                'aprobaciones' => $aprobaciones,
                'generated_at' => date('d/m/Y H:i')
            );

            // include generated_by (current user) for footer
            try {
                $u = $this->ion_auth->user()->row();
                $generated_by = '';
                if ($u) {
                    if (!empty($u->first_name) || !empty($u->last_name)) {
                        $generated_by = trim($u->first_name . ' ' . $u->last_name);
                    } else {
                        $generated_by = isset($u->username) ? $u->username : '';
                    }
                }
            } catch (Exception $e) { $generated_by = ''; }
            $data['generated_by'] = $generated_by;

            // include persisted propuestas (if any) so PDF contains conditions: monto, plazo, tasa, comision
            try {
                $propuestas = $this->core_model->get_by_id_all('tb_solicitud_propuestas', array('idsolicitud' => $id));
                if (!is_array($propuestas)) $propuestas = array();
            } catch (Exception $e) {
                $propuestas = array();
            }
            $data['propuestas'] = $propuestas;

            // Prepare requested vs approved summary
            $requested = array(
                'monto' => isset($solicitud->monto_solicitado) ? (float)$solicitud->monto_solicitado : null,
                'plazo' => isset($solicitud->plazo_meses) ? (int)$solicitud->plazo_meses : null,
                'tasa' => (isset($solicitud->tasa_interes) && $solicitud->tasa_interes !== '') ? $solicitud->tasa_interes : (isset($solicitud->producto_tasa) ? $solicitud->producto_tasa : null),
                'comision' => (isset($solicitud->comision_desembolso) && $solicitud->comision_desembolso !== '') ? $solicitud->comision_desembolso : null
            );
            $approved_total = 0.0;
            foreach ($propuestas as $pp) {
                if (isset($pp->monto) && is_numeric($pp->monto)) {
                    $approved_total += (float)$pp->monto;
                }
            }
            $data['requested'] = $requested;
            $data['approved_total'] = $approved_total;

            $html = $this->load->view('solicitudes/aprobaciones_pdf', $data, TRUE);
            $filename = 'Aprobaciones_Solicitud_' . $id;
            // render using pdf library if available
            if (isset($this->pdf) && method_exists($this->pdf, 'load_html')) {
                $this->pdf->load_html($html);
                $this->pdf->render();
                $this->pdf->stream($filename . '.pdf', array('Attachment' => 0));
                return;
            }
            $this->pdf->createPDF($html, $filename, TRUE);
        }
    /**
     * Generate and stream PDF for a solicitud
     * URL: /solicitudes/pdf/{id}
     */
    public function pdf($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado para generar PDF');
            redirect($this->router->fetch_class());
        }

        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));

        // Obtener nombre del asesor/ruta
        if (isset($solicitud->idasesor) && $solicitud->idasesor) {
            $asesor_row = $this->db->get_where('tb_asesores', array('idasesor' => $solicitud->idasesor))->row();
            if ($asesor_row && isset($asesor_row->nombres)) {
                $solicitud->nombre_asesor = $asesor_row->nombres;
            } else {
                $solicitud->nombre_asesor = $solicitud->idasesor;
            }
        } else {
            $solicitud->nombre_asesor = '';
        }

        // prepare generated metadata (user and timestamp)
        $user = $this->ion_auth->user()->row();
        $generated_by = '';
        if ($user) {
            if (!empty($user->first_name) || !empty($user->last_name)) {
                $generated_by = trim($user->first_name . ' ' . $user->last_name);
            } else {
                $generated_by = isset($user->username) ? $user->username : '';
            }
        }
        $generated_at = date('d/m/Y H:i');

        $data = array(
            'solicitud' => $solicitud,
            'generated_by' => $generated_by,
            'generated_at' => $generated_at,
            'document_title' => 'Solicitud Inicial de Crédito'
        );

        // include approvals for the PDF
        $data['aprobaciones'] = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $id));

        // render view to HTML
        $html = $this->load->view('solicitudes/pdf', $data, TRUE);

        // load pdf library and stream
        $this->load->library('pdf');
        $filename = 'Solicitud_Inicial_de_Credito_' . $id;
        $this->pdf->createPDF($html, $filename, TRUE);
    }

    /**
     * Emitir plan de pago: redirect to plan page or show message
     */
    public function emitir_plan($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class() . '/validacion_aprobacion');
            return;
        }

        // check if there are propuestas persisted
        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
        if ($sol && isset($sol->estado_aprobacion) && strtolower((string)$sol->estado_aprobacion) === 'anulado') {
            $this->session->set_flashdata('error', 'No puede emitir plan de pago: la solicitud está anulada.');
            redirect($this->router->fetch_class() . '/validacion_aprobacion');
            return;
        }

        // check if there are propuestas persisted
        $props = $this->core_model->get_by_id_all('tb_solicitud_propuestas', array('idsolicitud' => $id));
        if (empty($props)) {
            $this->session->set_flashdata('info', 'No hay propuestas persistidas para esta solicitud. Genere las propuestas desde Validación primero.');
            redirect($this->router->fetch_class() . '/validacion_aprobacion');
            return;
        }

        // Redirect to plan generation page (stub) - implement plan UI separately
        redirect($this->router->fetch_class() . '/plan_pago/' . $id);
    }

    /**
     * AJAX endpoint to save an approval for a solicitud
     * Expects POST: idsolicitud, role, comment
     */
    public function approve_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

    $idsolicitud = $this->input->post('idsolicitud');
    $role = $this->input->post('role');
    $comment = $this->input->post('comment');

        if (!$idsolicitud || !$role) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
            return;
        }

        // verify solicitud exists
    if (!$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
            return;
        }

        // No autenticación, usar valores por defecto
        $user_id = 0;
        $username = 'Invitado';

        // prevent duplicate approvals for the same role on the same solicitud
    $existing = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $idsolicitud, 'role' => $role));
        if (!empty($existing)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Este rol ya ha aprobado la solicitud')));
            return;
        }

        $data = array(
            'idsolicitud' => $idsolicitud,
            'role' => $role,
            'user_id' => $user_id,
            'username' => $username,
            'comment' => $comment
        );

        // insert approval
    $this->core_model->insert('tb_solicitud_aprobaciones', $data, TRUE);
        $last_id = $this->session->userdata('last_id');
        $approval = NULL;
        if ($last_id) {
            $approval = $this->core_model->get_by_id('tb_solicitud_aprobaciones', array('idaprobacion' => $last_id));
        }

        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'message' => 'Aprobación registrada', 'approval' => $approval)));
    }

    /**
     * AJAX: upload photo for a solicitud (supports multi-part POST)
     * POST: idsolicitud, file upload field `photo` (single file per request)
     */
    public function upload_solicitud_photo_ajax()
    {
        // Allow both AJAX and normal POST (clients may not set X-Requested-With)
        $idsolicitud = $this->input->post('idsolicitud');
        if (!$idsolicitud || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $idsolicitud))) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
            return;
        }

        if ($this->_is_solicitud_annulled($idsolicitud)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
            return;
        }

        if (empty($_FILES) || !isset($_FILES['photo'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Archivo no recibido')));
            return;
        }

        // group-aware limits (optional group param)
        $group = trim((string)$this->input->post('group')) ?: null;
        $existing = $this->core_model->get_by_id_all('tb_solicitud_photos', array('idsolicitud' => $idsolicitud));
        $count = is_array($existing) ? count($existing) : 0;
        // default overall limit (if no group): 50
        if (!$group) {
            if ($count >= 50) { $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Límite de fotos alcanzado.'))); return; }
        } else {
            // compute existing in this group by filename path (we store group as subfolder)
            $groupCount = 0;
            if (is_array($existing)) {
                foreach ($existing as $ph) {
                    if (isset($ph->filename) && strpos($ph->filename, '/' . $group . '/') !== false) $groupCount++;
                }
            }
            $limits = array('fachada' => 2, 'inventario' => 10, 'cedula_front' => 1, 'cedula_back' => 1, 'otros_ingresos_1' => 3, 'otros_ingresos_2' => 3, 'otros_ingresos_3' => 3, 'docs_generales' => 10, 'docs_legales' => 10, 'fotos_adicionales' => 50);
            $max = isset($limits[$group]) ? $limits[$group] : 20;
            if ($groupCount >= $max) { $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Ya existen ' . $groupCount . ' fotos en ' . $group . '. Elimine alguna antes de subir.'))); return; }
        }

        $file = $_FILES['photo'];
        // basic validation
        // Allow PDFs for docs_generales and docs_legales groups
        $allowed = array('image/jpeg', 'image/jpg', 'image/png');
        if ($group && in_array($group, array('docs_generales', 'docs_legales', 'consentimiento_filtrado'))) {
            $allowed[] = 'application/pdf';
        }
        $maxBytes = 5 * 1024 * 1024; // 5MB
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Error en la subida: ' . $file['error'])));
            return;
        }
        if ($file['size'] > $maxBytes) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'El archivo excede el tamaño máximo permitido (5MB).')));
            return;
        }
        if (!in_array($file['type'], $allowed)) {
            $msg = ($group && in_array($group, array('docs_generales', 'docs_legales'))) ? 'Tipo de archivo no permitido. Solo JPG/PNG/PDF.' : 'Tipo de archivo no permitido. Solo JPG/PNG.';
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => $msg)));
            return;
        }

        // Unificar ruta de guardado para docs_generales, docs_legales, consentimiento_filtrado y fotos_adicionales
        $base_dir = FCPATH . 'uploads/solicitudes/' . intval($idsolicitud) . '/';
        $group_no_subfolder = array('docs_generales', 'docs_legales', 'consentimiento_filtrado', 'fotos_adicionales');
        if ($group && in_array($group, $group_no_subfolder)) {
            $upload_dir = $base_dir;
            $subpath = 'solicitudes/' . intval($idsolicitud) . '/';
        } else if ($group) {
            $group_safe = preg_replace('/[^a-z0-9_\-]/i', '_', $group);
            $upload_dir = $base_dir . $group_safe . '/';
            $subpath = 'solicitudes/' . intval($idsolicitud) . '/' . $group_safe . '/';
        } else {
            $upload_dir = $base_dir;
            $subpath = 'solicitudes/' . intval($idsolicitud) . '/';
        }
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true);
        }

        // Preserve original filename when saving; sanitize to avoid bad chars
        $origName = isset($file['name']) ? basename($file['name']) : 'upload';
        $safeName = preg_replace('/[^A-Za-z0-9\.\_\- ]+/', '_', $origName);
        $safeName = mb_substr($safeName, 0, 200);
        // Avoid overwriting existing files: if name exists, prefix with timestamp
        $targetName = $safeName;
        if (is_file($upload_dir . $targetName)) {
            $targetName = time() . '_' . $targetName;
        }
        $dest = $upload_dir . $targetName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'No se pudo guardar el archivo.')));
            return;
        }

        // save metadata using the preserved filename
        $row = array(
            'idsolicitud' => $idsolicitud,
            'filename' => $subpath . $targetName,
            'grupo' => $group,
            'mime' => $file['type'],
            'size' => (int)$file['size']
        );
        try { $this->core_model->insert('tb_solicitud_photos', $row, TRUE); } catch (Exception $e) { log_message('error','[SOLICITUDES] insert tb_solicitud_photos error: '.$e->getMessage()); }
        $last = $this->session->userdata('last_id');

        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => (bool)$last, 'message' => ($last ? 'Subida correcta' : 'Error al registrar archivo'), 'file' => ($last ? $row : null))));
    }

    /**
     * AJAX: list photos for a solicitud
     * GET: /solicitudes/list_photos_ajax/{id}
     */
    public function list_photos_ajax($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada', 'photos' => array())));
            return;
        }
        try { $photos = $this->core_model->get_by_id_all('tb_solicitud_photos', array('idsolicitud' => $id)); } catch (Exception $e) { $photos = array(); }
        if (!is_array($photos)) $photos = array();
        $unique = array();
        $seen = array();
        foreach ($photos as $p) {
            if (!empty($p->filename)) {
                $filename = ltrim(str_replace('\\', '/', $p->filename), '/');
                if (isset($seen[$filename])) continue;
                $seen[$filename] = true;
            }
            if (empty($p->grupo) && !empty($p->filename)) {
                $parts = explode('/', str_replace('\\', '/', $p->filename));
                $group = 'otros';
                if (isset($parts[2])) {
                    $group = preg_replace('/[^a-z0-9_\-]/i', '_', $parts[2]);
                }
                $p->grupo = $group;
            }
            $unique[] = $p;
        }
        $photos = $unique;

        // NOTE: only return photos from the database metadata table.
        // Do not scan the uploads folder for loose files.
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'photos' => $photos)));
    }

    /**
     * Web: show gallery page for photos of a solicitud
     */
    public function photos($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->session->set_flashdata('error', 'Solicitud no encontrada');
            redirect('solicitudes');
            return;
        }
        $photos = array();
        try { $photos = $this->core_model->get_by_id_all('tb_solicitud_photos', array('idsolicitud' => $id)); } catch (Exception $e) { $photos = array(); }
        $unique = array();
        $seen = array();
        $hidden_groups = array('docs_generales', 'docs_legales', 'consentimiento_filtrado', 'fotos_adicionales');
        foreach ($photos as $p) {
            if (!empty($p->filename)) {
                $filename = ltrim(str_replace('\\', '/', $p->filename), '/');
                if (isset($seen[$filename])) continue;
                $seen[$filename] = true;
            }
            if (empty($p->grupo) && !empty($p->filename)) {
                $parts = explode('/', str_replace('\\', '/', $p->filename));
                $group = 'otros';
                if (isset($parts[2])) {
                    $group = preg_replace('/[^a-z0-9_\-]/i', '_', $parts[2]);
                }
                $p->grupo = $group;
            }
            $lowerFilename = isset($filename) ? strtolower($filename) : '';
            if (in_array($p->grupo, $hidden_groups, true)
                || strpos($lowerFilename, '/docs_generales/') !== false
                || strpos($lowerFilename, '/docs_legales/') !== false
                || strpos($lowerFilename, '/consentimiento_filtrado/') !== false
                || strpos($lowerFilename, '/fotos_adicionales/') !== false
            ) {
                continue;
            }
            $unique[] = $p;
        }
        $photos = $unique;

        // NOTE: only show photos present in the database metadata table.
        // Disk fallback is disabled so files that exist on disk without a DB row
        // are not added to the gallery.

        $data = array(
            'titulo' => 'Fotos de Solicitud',
            'subtitulo' => 'Galería de imágenes para la solicitud #' . intval($id),
            'icono' => 'fas fa-image',
            'photos' => $photos,
            'idsolicitud' => intval($id)
        );
        $this->load->view('layout/header', $data);
        $this->load->view('solicitudes/photos', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Web: show documents page for a solicitud
     */
    public function documents($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->session->set_flashdata('error', 'Solicitud no encontrada');
            redirect('solicitudes');
            return;
        }

        $files = array();
        try { $files = $this->core_model->get_by_id_all('tb_solicitud_photos', array('idsolicitud' => $id)); } catch (Exception $e) { $files = array(); }
        if (!is_array($files)) $files = array();

        $groups = array(
            'docs_generales' => array(),
            'docs_legales' => array(),
            'fotos_adicionales' => array(),
            'consentimiento_filtrado' => array()
        );
        foreach ($files as $file) {
            $group = isset($file->grupo) && trim($file->grupo) !== '' ? strtolower(preg_replace('/[^a-z0-9_]/i', '_', $file->grupo)) : 'otros';
            if (array_key_exists($group, $groups)) {
                $groups[$group][] = $file;
            }
        }

        $data = array(
            'titulo' => 'Documentos de Solicitud',
            'subtitulo' => 'Carga y consulta de documentos para la solicitud #' . intval($id),
            'icono' => 'fas fa-folder-open',
            'documents' => $groups,
            'idsolicitud' => intval($id)
        );
        $this->load->view('layout/header', $data);
        $this->load->view('solicitudes/documents', $data);
        $this->load->view('layout/footer');
    }

    /**
     * AJAX: upload single photo for a solicitud
     * POST: idsolicitud, group, file 'photo'
     */
    public function upload_photo_ajax()
    {
        $ids = intval($this->input->post('idsolicitud'));
        $group = $this->input->post('group');
        if (!$ids || !$group || empty($_FILES['photo'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
            return;
        }
        // ensure solicitud exists
        if (!$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $ids))) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
            return;
        }
        if ($this->_is_solicitud_annulled($ids)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
            return;
        }
        $file = $_FILES['photo'];
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Archivo no subido')));
            return;
        }
        // Preserve original filename when saving; sanitize and avoid collisions
        $origName = isset($file['name']) ? basename($file['name']) : 'upload';
        $safeName = preg_replace('/[^A-Za-z0-9\.\_\- ]+/', '_', $origName);
        $safeName = mb_substr($safeName, 0, 200);
        $destDir = FCPATH . 'uploads/solicitudes/' . $ids . '/' . $group . '/';
        if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
        $target = $destDir . $safeName;
        if (is_file($target)) { // avoid overwrite
            $target = $destDir . time() . '_' . $safeName;
        }
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'No se pudo mover el archivo')));
            return;
        }
        // prepare DB record (store relative path under uploads/)
        $relPath = 'solicitudes/' . $ids . '/' . $group . '/' . basename($target);
        try {
            $ins = array(
                'idsolicitud' => $ids,
                'filename' => $relPath,
                'grupo' => $group,
                'created_at' => date('Y-m-d H:i:s')
            );
            // avoid duplicate entries for same file
            $idphoto = null;
            try {
                $exists = $this->core_model->get_by_id('tb_solicitud_photos', array('idsolicitud' => $ids, 'filename' => $relPath));
            } catch (Exception $_) { $exists = null; }
            if (!$exists) {
                $idphoto = $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
            } else {
                $idphoto = isset($exists->idphoto) ? $exists->idphoto : null;
            }
        } catch (Exception $e) {
            // DB insert may fail if table missing; still return success for file saved
            $idphoto = null;
            log_message('error', '[SOLICITUDES] upload_photo_ajax DB insert error: ' . $e->getMessage());
        }
        $url = base_url('uploads/' . $relPath);
        $resp = array('status' => TRUE, 'file' => $safeName, 'url' => $url, 'idphoto' => $idphoto, 'filename' => $relPath, 'mime' => isset($file['type']) ? $file['type'] : 'application/octet-stream');
        $this->output->set_content_type('application/json')->set_output(json_encode($resp));
    }

    /**
     * AJAX: delete photo
     * POST: idphoto
     */
    public function delete_photo_ajax()
    {
        $idphoto = $this->input->post('idphoto');
        $filename = $this->input->post('filename');
        // allow deletion by idphoto or by filename (useful when files exist on disk but no DB row)
        if (!$idphoto && !$filename) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'idphoto o filename requerido')));
            return;
        }

        $photo = null;
        if ($idphoto) {
            $photo = $this->core_model->get_by_id('tb_solicitud_photos', array('idphoto' => $idphoto));
        }
        if (!$photo && $filename) {
            // sanitize: accept only paths under 'solicitudes/' to avoid deleting unrelated files
            $safe = trim(str_replace('\\', '/', $filename), '/');
            if (strpos($safe, 'solicitudes/') !== 0) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Filename inválido')));
                return;
            }
            // try to find DB row by filename
            try { $photo = $this->core_model->get_by_id('tb_solicitud_photos', array('filename' => $safe)); } catch (Exception $e) { $photo = null; }
            // set a pseudo-photo object for file deletion even if DB row not found
            if (!$photo) {
                $photo = (object) array('filename' => $safe);
            }
        }

        if (!$photo) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Archivo no encontrado')));
            return;
        }

        $idsolicitud = 0;
        if (isset($photo->idsolicitud)) {
            $idsolicitud = (int)$photo->idsolicitud;
        } elseif (isset($photo->filename) && preg_match('#^solicitudes/(\d+)/#', ltrim((string)$photo->filename, '/'), $m)) {
            $idsolicitud = (int)$m[1];
        }
        if ($idsolicitud > 0 && $this->_is_solicitud_annulled($idsolicitud)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
            return;
        }

        $filepath = FCPATH . 'uploads/' . ltrim($photo->filename, '/');
        // delete file if exists
        if (is_file($filepath)) {
            @unlink($filepath);
        }
        // delete DB record if it exists with idphoto
        if ($idphoto) {
            $this->core_model->delete('tb_solicitud_photos', array('idphoto' => $idphoto));
        } else {
            // try to delete by filename if a DB row exists
            try { $this->core_model->delete('tb_solicitud_photos', array('filename' => $photo->filename)); } catch (Exception $e) { }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'message' => 'Eliminado')));
    }

    /**
     * AJAX: update group/type of a photo
     * POST: idphoto OR filename, group
     */
    public function update_photo_group_ajax()
    {
        $idphoto = $this->input->post('idphoto');
        $filename = $this->input->post('filename');
        $group = $this->input->post('group');
        if (!$group || (!$idphoto && !$filename)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Parámetros incompletos')));
            return;
        }
        try {
            if ($idphoto) {
                $photo = $this->core_model->get_by_id('tb_solicitud_photos', array('idphoto' => $idphoto));
                if ($photo && isset($photo->idsolicitud) && $this->_is_solicitud_annulled((int)$photo->idsolicitud)) {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
                    return;
                }
                $ok = $this->core_model->update('tb_solicitud_photos', array('grupo' => $group), array('idphoto' => $idphoto));
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => (bool)$ok)));
                return;
            }
            // by filename: sanitize
            $safe = trim(str_replace('\\','/', $filename), '/');
            if (strpos($safe, 'solicitudes/') !== 0) { $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Filename inválido'))); return; }
            if (preg_match('#^solicitudes/(\d+)/#', $safe, $m) && $this->_is_solicitud_annulled((int)$m[1])) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
                return;
            }
            // try update existing row
            $row = $this->core_model->get_by_id('tb_solicitud_photos', array('filename' => $safe));
            if ($row) {
                $ok = $this->core_model->update('tb_solicitud_photos', array('grupo' => $group), array('filename' => $safe));
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => (bool)$ok)));
                return;
            }
            // insert a metadata row if table exists
            try {
                $parts = explode('/', $safe);
                $idsolicitud = isset($parts[1]) ? intval($parts[1]) : 0;
                $ins = array('idsolicitud' => $idsolicitud, 'filename' => $safe, 'grupo' => $group, 'created_at' => date('Y-m-d H:i:s'));
                $this->core_model->insert('tb_solicitud_photos', $ins, TRUE);
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE)));
                return;
            } catch (Exception $e) {
                // insertion failed
                $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'No se pudo guardar metadata')));
                return;
            }
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Error')));
            return;
        }
    }

    /**
     * AJAX: Auto-classify photos for a solicitud using heuristics
     * POST: (none) - URL: /solicitudes/auto_classify_photos_ajax/{id}
     */
    public function auto_classify_photos_ajax($id = NULL)
    {
        if (!$id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id))) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada')));
            return;
        }
        if ($this->_is_solicitud_annulled($id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'La solicitud está anulada y no puede editarse.')));
            return;
        }
        // build photos list (DB + disk)
        $photos = array();
        try { $photos = $this->core_model->get_by_id_all('tb_solicitud_photos', array('idsolicitud' => $id)); } catch (Exception $e) { $photos = array(); }
        if (!is_array($photos)) $photos = array();
        // map seen
        $seen = array(); foreach ($photos as $p) if (isset($p->filename)) $seen[] = ltrim(str_replace('\\','/',$p->filename), '/');
        $upload_dir = FCPATH . 'uploads/solicitudes/' . intval($id) . '/';
        if (is_dir($upload_dir)) {
            try {
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $file) {
                    if (! $file->isFile()) continue;
                    $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                    if (! in_array($ext, array('jpg','jpeg','png','gif','pdf'))) continue;
                    $abs = $file->getPathname();
                    $rel = str_replace('\\','/', substr($abs, strlen(FCPATH . 'uploads/')));
                    if (!in_array($rel, $seen)) {
                        $photos[] = (object) array('idphoto' => null, 'filename' => $rel, 'grupo' => 'otros', 'created_at' => date('Y-m-d H:i:s', $file->getMTime()));
                    }
                }
            } catch (Exception $e) { }
        }

        // heuristics function
        $classify = function($rel) {
            $lower = strtolower($rel);
            // prioritize folder names
            if (strpos($lower, '/cedula') !== false || strpos($lower, 'cedula') !== false || strpos($lower, 'dni') !== false) {
                if (strpos($lower, 'back') !== false || strpos($lower, 'tras') !== false || strpos($lower, 'rear') !== false) return 'cedula_back';
                return 'cedula_front';
            }
            if (strpos($lower, '/fachada') !== false || strpos($lower, 'fachada') !== false || strpos($lower, 'frontis') !== false) return 'fachada';
            if (strpos($lower, '/inventario') !== false || strpos($lower, 'inventario') !== false) return 'inventario';
            if (strpos($lower, 'otros_ingresos_1') !== false) return 'otros_ingresos_1';
            if (strpos($lower, 'otros_ingresos_2') !== false) return 'otros_ingresos_2';
            if (strpos($lower, 'otros_ingresos_3') !== false) return 'otros_ingresos_3';
            // fallback: try to use dimensions to detect document (tall) vs photo (wide)
            $abs = FCPATH . 'uploads/' . $rel;
            if (is_file($abs)) {
                $s = @getimagesize($abs);
                if ($s && isset($s[0]) && isset($s[1])) {
                    $w = $s[0]; $h = $s[1];
                    if ($h > $w * 1.2) return 'cedula_front';
                    if ($w > $h * 1.2) return 'inventario';
                }
            }
            return 'otros';
        };

        $changes = 0;
        foreach ($photos as $p) {
            $rel = isset($p->filename) ? ltrim(str_replace('\\','/',$p->filename), '/') : null;
            if (!$rel) continue;
            $newgroup = $classify($rel);
            // update or insert
            if (isset($p->idphoto) && $p->idphoto) {
                try { $this->core_model->update('tb_solicitud_photos', array('grupo' => $newgroup), array('idphoto' => $p->idphoto)); $changes++; } catch (Exception $e) {}
            } else {
                try { list($idsPart) = array_slice(explode('/', $rel), 1, 1); $ids = intval($idsPart); $ins = array('idsolicitud' => $ids, 'filename' => $rel, 'grupo' => $newgroup, 'created_at' => date('Y-m-d H:i:s')); $this->core_model->insert('tb_solicitud_photos', $ins, TRUE); $changes++; } catch (Exception $e) {}
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'changed' => $changes)));
    }

    public function del($cliente_id = NULL)
    {
        // Allow admins and Promotor group to delete solicitudes
        if (!($this->ion_auth->is_admin() || (method_exists($this->ion_auth,'in_group') && $this->ion_auth->in_group('Promotor')))) {
            $this->session->set_flashdata('info', 'No tienes permiso para eliminar solicitudes.');
            redirect('/');
        }
    if (!$cliente_id || !$this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $cliente_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
    if ($this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $cliente_id, 'estado' => 1))) {
            $this->session->set_flashdata('error', 'Solicitud con Estado Activo no puede ser eliminado.');
            redirect($this->router->fetch_class());
        }
        // If you want to enforce relations with other tables, add checks here
    $this->core_model->delete('tb_solicitudes', array('idsolicitud' => $cliente_id));
        redirect($this->router->fetch_class());
    }
}
