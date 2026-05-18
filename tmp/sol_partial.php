<?php
defined('BASEPATH') or exit('AcciÃ³n no permitida');
class Solicitudes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Cargar librerÃ­as y modelos necesarios
            // Cargar librerÃ­as y modelos correctamente
            $this->load->model('Core_model', 'core_model');
            $this->load->library('form_validation');
            $this->load->library('session');
            $this->load->library('pdf');
        // input y output son parte de CI_Controller, no requieren carga explÃ­cita
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
    }


    public function index()
    {
        $data = array(
            'titulo' => 'Solicitud Inicial de CrÃ©dito',
            'subtitulo' => 'Registrar solicitudes iniciales de crÃ©dito',
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
            'solicitudes' => $this->core_model->get_all('tb_solicitudes')
        );

        // Determine approval status for each solicitud: pending|approved|rejected
        foreach ($data['solicitudes'] as $s) {
            $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $s->idsolicitud));
            if (empty($aprs)) {
                $s->aprob_status = 'pending';
            } else {
                // sort by created_at desc and take latest
                usort($aprs, function($a, $b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                $latest = $aprs[0];
                $txt = isset($latest->comment) ? strtolower($latest->comment) : '';
                if (strpos($txt, 'rechaz') !== false) {
                    $s->aprob_status = 'rejected';
                } elseif (strpos($txt, 'aprob') !== false) {
                    $s->aprob_status = 'approved';
                } else {
                    $s->aprob_status = 'pending';
                }
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('solicitudes/index', $data);
        $this->load->view('layout/footer');
    }


    public function core($cliente_id = NULL)
    {
        // Word-mode: when active, only accept and validate a whitelist of fields
        $use_word_template = true;
        $word_allowed_fields = array(
            'apellidos','nombres','numero_doc','tipo_documento','fecha_nacimiento','edad','estado_civil','telefono','direccion',
            'giro_negocio','monto_solicitado','plazo_meses','frecuencia','tasa_interes','cuota_estim_estimada','garantia','comision_desembolso',
            'ventas_promedio_diarios','ventas_promedio_mensual','ventas_dias_buenos','ventas_dias_malos','ventas_dias_buenos_mask','ventas_dias_malos_mask','margen_comercial',
            'detalle_inventario','nombre_negocio','actividad_economica','ubicacion_negocio','numero_empleados','cuentas_por_cobrar_amount','caja_amount','banco_amount',
            'pago_alquiler','pago_trabajadores','energia','agua','internet','gastos_fijos','gastos_operativos','otros_gastos','otros_ingresos_detalle',
            'promotor','fecha_recepcion','observaciones','datos_personales','datos_conyuge','propuesta_tipos','edit_comment'
        );
        // Expose to views via data when rendering the form
        $view_word_mode = $use_word_template;

        if (!$cliente_id) {
            // If UI provides a single `nombre_completo` field, split it into apellidos/nombres
            if ($this->input->post('nombre_completo')) {
                $nc = trim($this->input->post('nombre_completo'));
                if ($nc !== '') {
                    // simple heuristic: last word -> apellidos, rest -> nombres
                    $parts = preg_split('/\s+/', $nc);
                    if (count($parts) == 1) {
                        $_POST['nombres'] = $parts[0];
                        $_POST['apellidos'] = '';
                    } else {
                        $last = array_pop($parts);
                        $_POST['apellidos'] = $last;
                        $_POST['nombres'] = implode(' ', $parts);
                    }
                }
            }
            // Registrar
            // Apply validation rules depending on Word-mode. In Word-mode we apply
            // a minimal set of required fields so hidden inputs do not cause errors.
            if ($use_word_template) {
                // Minimal rules required by Word template
                $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
                $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
                $this->form_validation->set_rules('monto_solicitado', 'Monto Solicitado', 'trim|required');
                $this->form_validation->set_rules('plazo_meses', 'Plazo', 'trim|required');
                $this->form_validation->set_rules('frecuencia', 'Frecuencia', 'trim|required');
                $this->form_validation->set_rules('edit_comment', 'Comentario', 'trim|required|min_length[3]');
            } else {
                // Legacy/full validation when not using Word template
                $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
                $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
                $this->form_validation->set_rules('direccion', 'DirecciÃ³n', 'trim|required|min_length[5]|max_length[100]');
                $this->form_validation->set_rules('telefono', 'TelÃ©fono', 'trim|required|min_length[4]|max_length[30]');
                $this->form_validation->set_rules('tipo_documento', 'Tipo Documento', 'trim|required');
                $this->form_validation->set_rules('numero_doc', 'Nro Documento', 'trim|required');
                // Comentario obligatorio (histÃ³rico)
                $this->form_validation->set_rules('edit_comment', 'Comentario', 'trim|required|min_length[3]');
            }

            if ($this->form_validation->run()) {
                if ($use_word_template) {
                    // Accept only whitelisted fields when Word-mode is active
                    $data = elements($word_allowed_fields, $this->input->post());
                } else {
                    // Legacy: accept entire POST
                    $data = $this->input->post();
                }
            }

                // Normalize inputs: convert empty strings to NULL for numeric/date fields,
                // normalize booleans/radios and format numeric values to avoid MySQL strict errors.
                $numeric_fields = array(
                    'monto_solicitado', 'plazo_meses', 'tasa_interes', 'cuota_estim_estimada',
                    'ingreso_promedio_alto', 'ingreso_promedio_bajo', 'ventas_promedio_diarios', 'ventas_promedio_mensual', 'margen_comercial',
                    'cuentas_por_cobrar_amount', 'caja_amount', 'banco_amount', 'pago_alquiler', 'pago_trabajadores', 'energia', 'agua', 'internet', 'gastos_fijos',
                    'negocio_antiguedad', 'edad', 'salario_conyuge', 'numero_dependientes', 'tiempo_residir_anios', 'tiempo_residir_meses',
                    'tiempo_empleo_anios', 'tiempo_empleo_meses', 'ingreso_mensual_neto', 'deducciones',
                    'tiempo_operacion_anios', 'tiempo_operacion_meses', 'ventas_dias_buenos', 'ventas_dias_malos', 'numero_empleados', 'comision_desembolso'
                    , 'ventas_dias_buenos_mask', 'ventas_dias_malos_mask'
                );
                // 'estado' puede venir como '' desde el formulario; tratarlo como numÃ©rico (NULL cuando vacÃ­o)
                $numeric_fields[] = 'estado';

                $boolean_fields = array(
                    'es_nuevo', 'es_renovacion', 'negocio_propio', 'matricula_permiso', 'cedula_vigente', 'otros_ingresos', 'ahorros', 'recibo_servicios', 'investigacion_vecinos', 'propiedad_negocio'
                );

                $date_fields = array('fecha_nacimiento', 'fecha_solicitud', 'fecha_recepcion');

                // Helper to normalize numeric-looking strings (remove thousands commas)
                foreach ($numeric_fields as $f) {
                    if (array_key_exists($f, $data)) {
                        $val = $data[$f];
                        if (is_string($val)) {
                            $clean = str_replace(',', '', $val);
                        } else {
                            $clean = $val;
                        }
                        if ($clean === '' || $clean === null) {
                            $data[$f] = null;
                        } else {
                            // Keep decimals as string if they contain non-numeric chars; try numeric cast
                            if (is_numeric($clean)) {
                                // preserve integer vs decimal
                                if (strpos((string) $clean, '.') !== false) {
                                    $data[$f] = (float) $clean;
                                } else {
                                    $data[$f] = (int) $clean;
                                }
                            } else {
                                // Not numeric: set to NULL to avoid DB type errors (simpler than changing schema)
                                $data[$f] = null;
                            }
                        }
                    }
                }

                // Normalize boolean / checkbox values
                foreach ($boolean_fields as $bf) {
                    if (array_key_exists($bf, $data)) {
                        $v = $data[$bf];
                        if ($v === '' || $v === null) {
                            $data[$bf] = null;
                        } elseif (is_numeric($v)) {
                            $data[$bf] = (int) $v ? 1 : 0;
                        } else {
                            // common checkbox value is 'on' or a non-empty string
                            $data[$bf] = (!empty($v)) ? 1 : null;
                        }
                    }
                }

                // Normalize dates: empty -> NULL; attempt to parse and format
                foreach ($date_fields as $dt) {
                    if (array_key_exists($dt, $data)) {
                        $v = trim((string) $data[$dt]);
                        if ($v === '' || $v === '0000-00-00' || $v === null) {
                            $data[$dt] = null;
                        } else {
                            $ts = strtotime($v);
                            if ($ts === false) {
                                $data[$dt] = null;
                            } else {
                                // fecha_nacimiento -> DATE, others keep DATETIME if time present
                                if ($dt === 'fecha_nacimiento') {
                                    $data[$dt] = date('Y-m-d', $ts);
                                } else {
                                    // Prefer Y-m-d H:i:s, but if no time component, set as date
                                    if (preg_match('/T|:/', $v)) {
                                        $data[$dt] = date('Y-m-d H:i:s', $ts);
                                    } else {
                                        $data[$dt] = date('Y-m-d', $ts);
                                    }
                                }
                            }
                        }
                    }
                }
                // Convert posted weekday checkbox arrays into bitmask fields
                // ventas_buenos_days[] -> ventas_dias_buenos_mask
                $vb = $this->input->post('ventas_buenos_days');
                $mask_b = 0;
                if (is_array($vb)) {
                    foreach ($vb as $ix) {
                        $i = (int) $ix;
                        if ($i >= 0 && $i <= 6) $mask_b |= (1 << $i);
                    }
                }
                $data['ventas_dias_buenos_mask'] = ($mask_b > 0 ? $mask_b : null);

                $vm = $this->input->post('ventas_malos_days');
                $mask_m = 0;
                if (is_array($vm)) {
                    foreach ($vm as $ix) {
                        $i = (int) $ix;
                        if ($i >= 0 && $i <= 6) $mask_m |= (1 << $i);
                    }
                }
                $data['ventas_dias_malos_mask'] = ($mask_m > 0 ? $mask_m : null);

                // Backwards compatibility: the DB still has a NOT NULL `tipo_doc` column
                // Map the new `tipo_documento` string to the legacy numeric `tipo_doc` values
                if (array_key_exists('tipo_documento', $data)) {
                    $td = $data['tipo_documento'];
                    // legacy mapping: 0=Cedula,1=RUC,2=Pasaporte,3=Otro
                    if ($td === 'Cedula') {
                        $data['tipo_doc'] = 0;
                    } elseif ($td === 'Pasaporte') {
                        $data['tipo_doc'] = 2;
                    } else {
                        // unknown -> fallback to 3 (Otro)
                        $data['tipo_doc'] = 3;
                    }
                } elseif (!array_key_exists('tipo_doc', $data) || $data['tipo_doc'] === null) {
                    // ensure key exists to avoid strict-mode errors
                    $data['tipo_doc'] = null;
                }

                // insert and store last id in session via core_model
                // handle propuesta_tipos (array from form) -> store as JSON string in column 'propuesta_tipos'
                $propuesta = $this->input->post('propuesta_tipos');
                if (is_array($propuesta)) {
                    $data['propuesta_tipos'] = json_encode(array_values($propuesta));
                } else {
                    $data['propuesta_tipos'] = null;
                }

                $this->core_model->insert('tb_solicitudes', $data, TRUE);
                $last_id = $this->session->userdata('last_id');
                if ($last_id) {
                    // Guardar comentario histÃ³rico enviado en el formulario (no va al PDF)
                    $comment_text = trim($this->input->post('edit_comment'));
                    if ($comment_text !== '') {
                        $u = $this->ion_auth->user()->row();
                        $comment_data = array(
                            'idsolicitud' => $last_id,
                            'user_id' => ($u ? $u->id : 0),
                            'username' => ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema'),
                            'action' => 'create',
                            'comment' => $comment_text
                        );
                        $this->core_model->insert('tb_solicitudes_comments', $comment_data, TRUE);
                    }
                    // Auto-create cliente record if not exists (when creating a nueva solicitud)
                    // Use numero_doc as unique key to avoid duplicates
                    try {
                        $numero_doc = isset($data['numero_doc']) ? trim($data['numero_doc']) : '';
                        if (!empty($numero_doc)) {
                            $existing_client = $this->core_model->get_by_id('tb_clientes', array('numero_doc' => $numero_doc));
                            if (!$existing_client) {
                                $client_row = array(
                                    'apellidos' => isset($data['apellidos']) ? $data['apellidos'] : '',
                                    'nombres' => isset($data['nombres']) ? $data['nombres'] : '',
                                    'direccion' => isset($data['direccion']) ? $data['direccion'] : '',
                                    'telefono' => isset($data['telefono']) ? $data['telefono'] : '',
                                    // prefer legacy tipo_doc if set, otherwise map from tipo_documento
                                    'tipo_doc' => (isset($data['tipo_doc']) ? $data['tipo_doc'] : (isset($data['tipo_documento']) && $data['tipo_documento'] === 'Cedula' ? 0 : (isset($data['tipo_documento']) && $data['tipo_documento'] === 'Pasaporte' ? 2 : 3))),
                                    'numero_doc' => $numero_doc,
                                    'estado' => 1
                                );
                                $this->core_model->insert('tb_clientes', $client_row, TRUE);
                                $client_last = $this->session->userdata('last_id');
                                if ($client_last) {
                                    // Add a historic comment linking the created client
                                    $u = $this->ion_auth->user()->row();
                                    $comment_data = array(
                                        'idsolicitud' => $last_id,
                                        'user_id' => ($u ? $u->id : 0),
                                        'username' => ($u ? (trim($u->first_name . ' ' . $u->last_name) ?: $u->username) : 'Sistema'),
                                        'action' => 'auto_create_cliente',
                                        'comment' => 'Cliente creado automÃ¡ticamente (idcliente: ' . $client_last . ') al registrar la solicitud.'
                                    );
                                    $this->core_model->insert('tb_solicitudes_comments', $comment_data, TRUE);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // swallow to avoid breaking solicitud creation; log for debugging
                        log_message('error', 'Error auto-creating cliente for solicitud ' . $last_id . ': ' . $e->getMessage());
                    }

                    // Auto-create two referencia rows (placeholder) so UI can load two forms
                    try {
                        $existing_refs = $this->core_model->get_by_id_all('tb_solicitud_referencias', array('idsolicitud' => $last_id));
                        if (!is_array($existing_refs) || count($existing_refs) < 2) {
                            for ($ri = 1; $ri <= 2; $ri++) {
                                $ref_row = array(
                                    'idsolicitud' => $last_id,
                                    'referencia_num' => $ri
                                );
                                $this->core_model->insert('tb_solicitud_referencias', $ref_row, TRUE);
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'Error auto-creating referencias for solicitud ' . $last_id . ': ' . $e->getMessage());
                    }
                    // Generate PDF HTML (reuse same view as pdf()) and save to uploads
                    $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $last_id));
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

                    $pdf_data = array(
                        'solicitud' => $solicitud,
                        'generated_by' => $generated_by,
                        'generated_at' => $generated_at,
                        'document_title' => 'Solicitud Inicial de CrÃ©dito',
                        'aprobaciones' => $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $last_id))
                    );

                    $html = $this->load->view('solicitudes/pdf', $pdf_data, TRUE);

                    // ensure uploads directory
                    $dir = FCPATH . 'uploads/solicitudes/';
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    $filename = 'Solicitud_Inicial_de_Credito_' . $last_id . '.pdf';
                    $fullpath = $dir . $filename;

                    // Use Pdf library to save file
                    $saved = $this->pdf->savePDF($html, $fullpath);
                    if ($saved) {
                        $download_url = base_url('uploads/solicitudes/' . $filename);
                        $this->session->set_flashdata('pdf_download', $download_url);
                    } else {
                        $this->session->set_flashdata('error', 'No se pudo generar el PDF, pero el registro fue guardado.');
                    }

                    // After creating solicitud, redirect to completar Formato de GarantÃ­a
                    redirect('garantias/create/' . $last_id);
                } else {
                    redirect($this->router->fetch_class());
                }
            } else {
                $data = array(
                    'titulo' => 'Registrar Solicitud Inicial',
                    'subtitulo' => 'Ingrese los datos de la solicitud y cliente.',
                    'icono_view' => 'ik ik-user ',
                    'scripts' => array(
                        'js/utils/utils.js'
                    )
                    , 'aprobaciones' => array()
                );
                // expose word-mode to the view so it can hide non-whitelisted fields
                $data['word_allowed_fields'] = $word_allowed_fields;
                $data['view_word_mode'] = $view_word_mode;
                $this->load->view('layout/header', $data);
                $this->load->view('solicitudes/core', $data);
                $this->load->view('layout/footer');
            }
