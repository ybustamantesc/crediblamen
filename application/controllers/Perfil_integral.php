<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil_integral extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Perfil_integral_model');
        $this->load->model('Core_model', 'core_model');
        $this->load->helper(['url','form']);
        $this->load->library(['session']);
    }

    /**
     * Normalize field: convert empty strings to null
     */
    private function nf($v)
    {
        if (!isset($v)) return null;
        if ($v === '') return null;
        if (is_string($v)) {
            $v = trim($v);
            return $v === '' ? null : $v;
        }
        return $v;
    }

    /**
     * Normalize numeric: return null for empty, otherwise numeric value or original
     */
    private function nn($v)
    {
        $val = $this->nf($v);
        if ($val === null) return null;
        if (is_numeric($val)) return $val + 0;
        return $val;
    }

    private function _infer_aprob_status_from_comment($comment)
    {
        $txt = strtolower((string)$comment);
        if (strpos($txt, 'anul') !== false) return 'annulled';
        if (strpos($txt, 'rechaz') !== false) return 'rejected';
        if (strpos($txt, 'aprob') !== false) return 'approved';
        return 'pending';
    }

    public function index()
    {
        // Load all perfiles and let DataTables handle pagination on the client (like Solicitudes)
        $this->db->order_by('id', 'DESC');
        $perfiles = $this->db->get('tb_perfil_integral_cliente')->result();

        $data = [
            'titulo' => 'Perfil Integral del Cliente',
            'subtitulo' => 'Listado de perfiles integrales',
            'icono' => 'fas fa-id-card',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'perfiles' => $perfiles
        ];
        // Build map of solicitudes referenced by perfiles to allow fallback values in the view
        $solicitud_ids = array_filter(array_map(function($r){ return isset($r->solicitud_id)?intval($r->solicitud_id):null; }, $data['perfiles']));
        if (!empty($solicitud_ids)) {
            // use core_model if available to fetch solicitudes
            $this->db->where_in('idsolicitud', $solicitud_ids);
            $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
            $this->db->from('tb_solicitudes');
            $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
            $sols = $this->db->get()->result();
            $sol_map = [];
            foreach ($sols as $s) $sol_map[$s->idsolicitud] = $s;
            $data['solicitudes_map'] = $sol_map;
        } else {
            $data['solicitudes_map'] = [];
        }
        // Annotate each perfil with approval status based on committee approvals for its solicitud
        if (!empty($data['perfiles']) && is_array($data['perfiles'])) {
            foreach ($data['perfiles'] as $p) {
                $p->aprob_status = 'pending';
                $sid = isset($p->solicitud_id) ? intval($p->solicitud_id) : 0;
                if ($sid) {
                    $sol = isset($data['solicitudes_map'][$sid]) ? $data['solicitudes_map'][$sid] : null;
                    $estado_sol = ($sol && isset($sol->estado_aprobacion)) ? strtolower((string)$sol->estado_aprobacion) : '';
                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $p->aprob_status = 'annulled';
                    }

                    $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $sid));
                    if (!empty($aprs) && is_array($aprs)) {
                        usort($aprs, function($a,$b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                        $latest = $aprs[0];
                        $p->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    }

                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $p->aprob_status = 'annulled';
                    }

                    $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $sid));
                    if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                        $p->aprob_status = 'annulled';
                    }
                }
            }
        }
        $this->load->view('layout/header', $data);
        $this->load->view('perfil_integral/index', $data);
        $this->load->view('layout/footer');
    }

    public function download($perfil_id)
    {
        $perfil = $this->Perfil_integral_model->get($perfil_id);
        if (! $perfil) {
            $this->session->set_flashdata('error', 'Perfil no encontrado.');
            redirect('perfil_integral');
        }

        $sol = $this->core_model->get_by_id('tb_solicitudes', ['idsolicitud' => $perfil->solicitud_id]);

        // render pdf view
        $html = $this->load->view('perfil_integral/pdf', ['perfil' => $perfil, 'solicitud' => $sol], true);
        $filename = 'Perfil_'.$perfil->solicitud_id;

        // Prefer the app's Pdf library (uses the project's dompdf in /dompdf)
        try {
            if (file_exists(APPPATH.'libraries/Pdf.php')) {
                $this->load->library('Pdf');
                // createPDF($html, $filename, $download = TRUE, $paper = 'A4', $orientation = 'portrait')
                $this->pdf->createPDF($html, $filename, TRUE, 'A4', 'portrait');
                exit;
            }
        } catch (Exception $e) {
            // continue to fallback below
        }

        // If Pdf library not available, try vendor autoload dompdf (composer)
        if (!class_exists('\Dompdf\Dompdf')) {
            if (file_exists(FCPATH.'vendor/autoload.php')) {
                require_once FCPATH.'vendor/autoload.php';
            }
        }

        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filename.'.pdf', array('Attachment' => 1));
            exit;
        }

        // Final fallback: return styled HTML for download and advise installation
        $this->session->set_flashdata('error', 'Generador de PDF no disponible. El perfil se descargará como HTML. Para habilitar PDF coloque la librería dompdf en la raíz del proyecto o instale via composer.');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'.html"');
        echo $html;
        exit;
    }

    /**
     * Download only the Matriz PDF for a perfil
     */
    public function download_matriz($perfil_id)
    {
        $perfil = $this->Perfil_integral_model->get($perfil_id);
        if (! $perfil) {
            $this->session->set_flashdata('error', 'Perfil no encontrado.');
            redirect('perfil_integral');
        }
        $sol = $this->core_model->get_by_id('tb_solicitudes', ['idsolicitud' => $perfil->solicitud_id]);

        // map stored matriz_answers (JSON) to readable labels using same mapping used in the form
        $answers_raw = is_string($perfil->matriz_answers) ? $perfil->matriz_answers : (@json_encode($perfil->matriz_answers) ?: '');
        $answers_arr = [];
        if ($answers_raw) {
            $dec = @json_decode($answers_raw, true);
            if (is_array($dec)) $answers_arr = $dec;
        }

        $map = $this->_matriz_labels_map();
        $values = $this->_matriz_values_map();
        $groups = $this->_matriz_groups();
        // compute per-group score using selected answer ids and values map
        $group_scores = [];
        foreach ($groups as $key => $ids) {
            $score = null;
            foreach ($ids as $id) {
                if (in_array($id, $answers_arr)) {
                    // value may exist
                    $score = isset($values[$id]) ? $values[$id] : $score;
                    break;
                }
            }
            // default to 0 if not found
            $group_scores[$key] = $score !== null ? $score : 0;
        }
        $answers_labels = [];
        foreach ($answers_arr as $aid) {
            if (isset($map[$aid])) $answers_labels[] = $map[$aid];
            else $answers_labels[] = $aid;
        }

        // prepare logo as data URI for dompdf
        $logo_data = null;
        $logo_path = FCPATH . 'public/img/logo.png';
        if (file_exists($logo_path)) {
            $mtype = mime_content_type($logo_path) ?: 'image/png';
            $contents = @file_get_contents($logo_path);
            if ($contents !== false) $logo_data = 'data:' . $mtype . ';base64,' . base64_encode($contents);
        }

        $data = [
            'perfil' => $perfil,
            'solicitud' => $sol,
            'sol' => $sol,
            'matriz_score' => $perfil->matriz_score ?? null,
            'matriz_answers_labels' => $answers_labels,
            'matriz_answers_ids' => $answers_arr,
            'nivel_riesgo' => $perfil->nivel_riesgo ?? null,
            'group_scores' => $group_scores,
            'logo_data' => $logo_data,
            'fecha_evaluacion' => (!empty($perfil->fecha_perfil) ? $perfil->fecha_perfil : date('Y-m-d H:i:s')),
            // fecha_impresion y usuario para el pie de página
            'fecha_impresion' => date('d/m/Y H:i'),
            'impreso_por' => $this->session->userdata('username') ?: $this->session->userdata('user_name') ?: $this->session->userdata('email') ?: null,
        ];

        // Calculo del DDC basado en el nivel de riesgo (celda B15 en Excel)
        // Excel logic: =IF(B15="BAJO","DDC-S",IF(B15="MEDIO","DDC-S",IF(B15="ALTO","DDC-I")))
        $nr = isset($data['nivel_riesgo']) ? strtoupper(trim($data['nivel_riesgo'])) : null;
        if ($nr === 'BAJO' || $nr === 'MEDIO') {
            $data['ddc_result'] = 'DDC-S';
        } elseif ($nr === 'ALTO') {
            $data['ddc_result'] = 'DDC-I';
        } else {
            $data['ddc_result'] = null;
        }

        $html = $this->load->view('perfil_integral/matriz_pdf', $data, true);
        $filename = 'Matriz_'.$perfil->solicitud_id;

        try {
            if (file_exists(APPPATH.'libraries/Pdf.php')) {
                $this->load->library('Pdf');
                $this->pdf->createPDF($html, $filename, TRUE, 'A4', 'portrait');
                return;
            }
        } catch (Exception $e) {}

        if (!class_exists('\\Dompdf\\Dompdf')) {
            if (file_exists(FCPATH.'vendor/autoload.php')) require_once FCPATH.'vendor/autoload.php';
        }
        if (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filename.'.pdf', array('Attachment' => 1));
            return;
        }

        // fallback: send HTML
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'.html"');
        echo $html;
        return;
    }

    // Internal helper: returns mapping id=>label for matriz questions
    private function _matriz_labels_map()
    {
        $groups = [
            'Tipo de Persona' => [
                'tipo_natural' => 'Natural',
                'tipo_juridica' => 'Jurídica'
            ],
            'Ocupación' => [
                'propietario' => 'Propietario', 'empleado' => 'Empleado', 'negocio_propio' => 'Negocio Propio', 'estudiante_ocup' => 'Estudiante', 'ama_de_casa' => 'Ama de Casa', 'jubilado' => 'Jubilado'
            ],
            'Actividad Económica' => [
                'agricultura_ganaderia'=>'Agricultura/Ganadería','actividades_financieras'=>'Actividades Financieras/Jurídicas','transporte'=>'Transporte','comercio_servicios'=>'Comercio/Servicios','industria_manufactura'=>'Industria/Manufactura','estado'=>'Estado','construccion'=>'Construcción','profesionales'=>'Profesionales','actividades_hogar'=>'Actividades del Hogar/Estudiante','asalariados'=>'Asalariados'
            ],
            'Garantías' => ['garantia_hipotecaria'=>'Garantía Hipotecaria','garantia_inmobiliaria'=>'Garantía Inmobiliaria','sin_garantia'=>'Sin garantía'],
            'Edad' => ['edad_21_39'=>'De 21 a 39 años','edad_40_55'=>'De 40 a 55 años','edad_mayor_56'=>'Mayor a 56 Años'],
            'Condición PEP' => ['pep_si'=>'Si','pep_no'=>'No'],
            '¿Es Frecuente?' => ['frecuente_si'=>'Si','frecuente_no'=>'No','frecuente_recomendado'=>'Recomendado'],
            'Zona geográfica' => [
                'zona_managua'=>'Managua','zona_matagalpa'=>'Matagalpa','zona_chinandega'=>'Chinandega','zona_leon'=>'León','zona_carazo'=>'Carazo','zona_granada'=>'Granada','zona_masaya'=>'Masaya','zona_raccn'=>'RACCN','zona_esteli'=>'Estelí','zona_rivas'=>'Rivas','zona_jinotega'=>'Jinotega','zona_raccs'=>'RACCS','zona_chontales'=>'Chontales','zona_zelaya'=>'Zelaya Central','zona_triangulo_minero'=>'Triángulo Minero','zona_nueva_segovia'=>'Nueva Segovia','zona_boaco'=>'Boaco','zona_madridz'=>'Madriz','zona_rio_san_juan'=>'Río San Juan'
            ],
            'Valor de Transacción' => ['valor_usd_100_500'=>'USD 100 - 500','valor_usd_500_1000'=>'USD 500.01 - 1,000.00','valor_usd_1000_1500'=>'USD 1,000.01 - 1,500.00','valor_usd_1500_2000'=>'USD 1,500.01 - 2,000.00','valor_usd_2000_5000'=>'USD 2,000.01 - 5,000.00','valor_usd_10001_more'=>'mayor a USD 10,001.00']
        ];
        $flat = [];
        foreach ($groups as $g) {
            foreach ($g as $k=>$v) $flat[$k] = $v;
        }
        return $flat;
    }

    // return id => numeric value mapping used in the form
    private function _matriz_values_map()
    {
        return [
            'tipo_natural'=>25,'tipo_juridica'=>100,
            'propietario'=>100,'empleado'=>50,'negocio_propio'=>50,'estudiante_ocup'=>100,'ama_de_casa'=>100,'jubilado'=>100,
            'agricultura_ganaderia'=>100,'actividades_financieras'=>100,'transporte'=>100,'comercio_servicios'=>50,'industria_manufactura'=>50,'estado'=>50,'construccion'=>50,'profesionales'=>50,'actividades_hogar'=>50,'asalariados'=>50,
            'garantia_hipotecaria'=>25,'garantia_inmobiliaria'=>50,'sin_garantia'=>100,
            'edad_21_39'=>25,'edad_40_55'=>50,'edad_mayor_56'=>100,
            'pep_si'=>100,'pep_no'=>50,
            'frecuente_si'=>25,'frecuente_no'=>25,'frecuente_recomendado'=>50,
            'zona_managua'=>50,'zona_matagalpa'=>100,'zona_chinandega'=>100,'zona_leon'=>100,'zona_carazo'=>50,'zona_granada'=>50,'zona_masaya'=>50,'zona_raccn'=>100,'zona_esteli'=>100,'zona_rivas'=>100,'zona_jinotega'=>100,'zona_raccs'=>100,'zona_chontales'=>50,'zona_zelaya'=>100,'zona_triangulo_minero'=>100,'zona_nueva_segovia'=>100,'zona_boaco'=>25,'zona_madridz'=>25,'zona_rio_san_juan'=>25,
            'valor_usd_100_500'=>25,'valor_usd_500_1000'=>50,'valor_usd_1000_1500'=>50,'valor_usd_1500_2000'=>100,'valor_usd_2000_5000'=>100,'valor_usd_10001_more'=>100
        ];
    }

    // groups: map our row keys to arrays of answer ids that belong to that group
    private function _matriz_groups()
    {
        return [
            'tipo_persona' => ['tipo_natural','tipo_juridica'],
            'categoria' => ['propietario','empleado','negocio_propio','estudiante_ocup','ama_de_casa','jubilado'],
            'actividad_economica' => ['agricultura_ganaderia','actividades_financieras','transporte','comercio_servicios','industria_manufactura','estado','construccion','profesionales','actividades_hogar','asalariados'],
            'edad' => ['edad_21_39','edad_40_55','edad_mayor_56'],
            'condicion_pep' => ['pep_si','pep_no'],
            'es_frecuente' => ['frecuente_si','frecuente_no','frecuente_recomendado'],
            'zona_geografica' => ['zona_managua','zona_matagalpa','zona_chinandega','zona_leon','zona_carazo','zona_granada','zona_masaya','zona_raccn','zona_esteli','zona_rivas','zona_jinotega','zona_raccs','zona_chontales','zona_zelaya','zona_triangulo_minero','zona_nueva_segovia','zona_boaco','zona_madridz','zona_rio_san_juan'],
            'valor_transaccion' => ['valor_usd_100_500','valor_usd_500_1000','valor_usd_1000_1500','valor_usd_1500_2000','valor_usd_2000_5000','valor_usd_10001_more'],
            'garantia' => ['garantia_hipotecaria','garantia_inmobiliaria','sin_garantia']
        ];
    }

    public function create($solicitud_id = null)
    {
        if (! $solicitud_id) {
            $this->session->set_flashdata('error', 'Falta el ID de la solicitud.');
            redirect('solicitudes');
        }

        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $solicitud_id));
        if (! $sol) {
            $this->session->set_flashdata('error', 'Solicitud no encontrada.');
            redirect('solicitudes');
        }

        // try to find existing perfil
        $perfil = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
        // If there is no perfil yet, attempt to prefill combined nombres/apellidos
        if (! $perfil) {
            // Prefer combined fields when available
            if (! empty($sol->nombre_completo)) {
                $sol->nombre = trim($sol->nombre_completo);
            } else {
                $names = trim((isset($sol->nombres) ? $sol->nombres : '') . ' ' . (isset($sol->nombre) ? $sol->nombre : ''));
                if ($names !== '') {
                    $sol->nombre = preg_replace('/\s+/', ' ', $names);
                }
            }
            if (! empty($sol->apellidos)) {
                $sol->primer_apellido = trim($sol->apellidos);
            } else {
                $surnames = trim((isset($sol->primer_apellido) ? $sol->primer_apellido : '') . ' ' . (isset($sol->segundo_apellido) ? $sol->segundo_apellido : ''));
                if ($surnames !== '') {
                    $sol->primer_apellido = preg_replace('/\s+/', ' ', $surnames);
                }
            }
            // Also ensure conventional combined keys exist for views that prefer them
            if (!empty($sol->nombre)) {
                $sol->nombres = $sol->nombre;
            }
            if (!empty($sol->primer_apellido)) {
                $sol->apellidos = $sol->primer_apellido;
            }
        }

        // Prefill fecha_perfil from solicitud's fecha_solicitud or fecha_recepcion when not provided
        if (empty($perfil)) {
            if (empty($sol->fecha_perfil)) {
                $srcDate = null;
                if (!empty($sol->fecha_solicitud)) $srcDate = $sol->fecha_solicitud;
                elseif (!empty($sol->fecha_recepcion)) $srcDate = $sol->fecha_recepcion;
                if ($srcDate) {
                    // Normalize to YYYY-MM-DD for <input type="date">
                    $ts = strtotime($srcDate);
                    if ($ts !== false) {
                        $sol->fecha_perfil = date('Y-m-d', $ts);
                    }
                }
            }
        }

        // Additional prefills from solicitud: categoria_empleo, and spouse details
        if (empty($perfil)) {
            // Categoria empleo: if tipo_contrato present -> Empleado, otherwise -> Negocio propio
            if (!isset($sol->categoria_empleo) || $sol->categoria_empleo === null || $sol->categoria_empleo === '') {
                if (!empty($sol->tipo_contrato)) {
                    $sol->categoria_empleo = 'Empleado';
                } else {
                    $sol->categoria_empleo = 'Negocio propio';
                }
            }

            // Spouse name parsing: split solicitud->nombre_conyuge into up to 4 parts
            $sp_full = '';
            if (!empty($sol->nombre_conyuge)) $sp_full = trim($sol->nombre_conyuge);
            if ($sp_full !== '') {
                $parts = preg_split('/\s+/', $sp_full);
                $cnt = count($parts);
                $f = $s2 = $pa = $sa = null;
                if ($cnt >= 4) {
                    $f = $parts[0];
                    $s2 = $parts[1];
                    $pa = $parts[$cnt - 2];
                    $sa = $parts[$cnt - 1];
                } elseif ($cnt === 3) {
                    $f = $parts[0]; $s2 = $parts[1]; $pa = $parts[2];
                } elseif ($cnt === 2) {
                    $f = $parts[0]; $pa = $parts[1];
                } elseif ($cnt === 1) {
                    $f = $parts[0];
                }
                if ($f) $sol->conyuge_primer_nombre = $f;
                if ($s2) $sol->conyuge_segundo_nombre = $s2;
                if ($pa) $sol->conyuge_primer_apellido = $pa;
                if ($sa) $sol->conyuge_segundo_apellido = $sa;
                // provide full spouse name for views: prefer solicitud->nombre_conyuge, otherwise build from parsed parts
                if (!isset($sol->nombre_conyuge) || trim($sol->nombre_conyuge) === '') {
                    $sol->nombre_conyuge = trim(($f ? $f : '') . ($s2 ? ' ' . $s2 : '') . ($pa ? ' ' . $pa : '') . ($sa ? ' ' . $sa : ''));
                }
            }

            // Spouse contact & address: bring from solicitud where available
            if ((!isset($sol->conyuge_direccion) || $sol->conyuge_direccion === null || $sol->conyuge_direccion === '') ) {
                if (isset($sol->direccion_exacta) && $sol->direccion_exacta) $sol->conyuge_direccion = $sol->direccion_exacta;
                elseif (isset($sol->direccion) && $sol->direccion) $sol->conyuge_direccion = $sol->direccion;
            }
            if ((!isset($sol->conyuge_telefono_domicilio) || $sol->conyuge_telefono_domicilio === null || $sol->conyuge_telefono_domicilio === '') && !empty($sol->telefono_conyuge)) {
                $sol->conyuge_telefono_domicilio = $sol->telefono_conyuge;
            }
            if ((!isset($sol->conyuge_celular) || $sol->conyuge_celular === null || $sol->conyuge_celular === '') && !empty($sol->telefono_conyuge)) {
                $sol->conyuge_celular = $sol->telefono_conyuge;
            }
        }

                // re-fetch and log stored value
                $stored = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
                if ($stored) log_message('info', 'Perfil_integral saved - stored categoria_empleo: ' . var_export($stored->categoria_empleo ?? null, true));
                // If debug flag present, return JSON for inspection
                if ($this->input->get('debug') == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => true, 'action' => 'update', 'posted' => $post, 'data' => $data, 'stored' => $stored));
                    exit;
                }
        $data = [
            'titulo' => 'Perfil Integral del Cliente',
            'solicitud' => $sol,
            'perfil' => $perfil
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('perfil_integral/form', $data);
        $this->load->view('layout/footer');
                // re-fetch and log stored value
                $stored = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
                if ($stored) log_message('info', 'Perfil_integral created - stored categoria_empleo: ' . var_export($stored->categoria_empleo ?? null, true));
                if ($this->input->get('debug') == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => true, 'action' => 'insert', 'posted' => $post, 'data' => $data, 'stored' => $stored));
                    exit;
                }
    }

    public function save()
    {
        $post = $this->input->post();
        // Accept `nombre_conyuge` as a single full-name field (no automatic splitting)
        $solicitud_id = isset($post['solicitud_id']) ? intval($post['solicitud_id']) : 0;
        if (! $solicitud_id) {
            $this->session->set_flashdata('error', 'Solicitud inválida.');
            redirect('solicitudes');
        }
        // use controller helper methods to normalize fields

        $data = [
            'solicitud_id' => $solicitud_id,
            'nombre' => $this->nf($post['nombre'] ?? null),
            'segundo_nombre' => $this->nf($post['segundo_nombre'] ?? null),
            'primer_apellido' => $this->nf($post['primer_apellido'] ?? null),
            'segundo_apellido' => $this->nf($post['segundo_apellido'] ?? null),
            'tipo_documento' => $this->nf($post['tipo_documento'] ?? null),
            'numero_documento' => $this->nf($post['numero_documento'] ?? null),
            'fecha_nacimiento' => $this->nf($post['fecha_nacimiento'] ?? null),
            'telefono' => $this->nf($post['telefono'] ?? null),
            'celular' => $this->nf($post['celular'] ?? null),
            'email' => $this->nf($post['email'] ?? null),
            'direccion' => $this->nf($post['direccion'] ?? null),
            'ciudad' => $this->nf($post['ciudad'] ?? null),
            'apartado_postal' => $this->nf($post['apartado_postal'] ?? null),
            'estado_civil' => $this->nf($post['estado_civil'] ?? null),
            'sexo' => $this->nf($post['sexo'] ?? null),
            'n_dependientes' => $this->nn($post['n_dependientes'] ?? null),
            'ocupacion' => $this->nf($post['ocupacion'] ?? null),
            'profesion' => $this->nf($post['profesion'] ?? null),
            'empresa' => $this->nf($post['empresa'] ?? null),
            'ingreso_mensual' => $this->nn($post['ingreso_mensual'] ?? null),
            'antiguedad_laboral' => $this->nf($post['antiguedad_laboral'] ?? null),
            'otros' => $this->nf($post['otros'] ?? null),
            'fecha_perfil' => $this->nf($post['fecha_perfil'] ?? null),
            'nivel_riesgo' => $this->nf($post['nivel_riesgo'] ?? null),
            'tipo_ddc' => $this->nf($post['tipo_ddc'] ?? null),
            'nombre_conocido' => $this->nf($post['nombre_conocido'] ?? null),
            'en_su_propio_pais' => isset($post['en_su_propio_pais']) ? intval($post['en_su_propio_pais']) : 0,
            'es_funcionario_publico' => isset($post['es_funcionario_publico']) ? intval($post['es_funcionario_publico']) : 0,
            'cargo_funcionario' => $this->nf($post['cargo_funcionario'] ?? null),
            'pais_emision_documento' => $this->nf($post['pais_emision_documento'] ?? null),
            'numero_registro' => $this->nf($post['numero_registro'] ?? null),
            'fecha_emision_documento' => $this->nf($post['fecha_emision_documento'] ?? null),
            'fecha_vencimiento_documento' => $this->nf($post['fecha_vencimiento_documento'] ?? null),
            'pais_nacimiento' => $this->nf($post['pais_nacimiento'] ?? null),
            'categoria_empleo' => $this->nf($post['categoria_empleo'] ?? null),
            'categoria_otro' => $this->nf($post['categoria_otro'] ?? null),
            'zona_cobertura' => $this->nf($post['zona_cobertura'] ?? null),
            'sitio_web_centro_trabajo' => $this->nf($post['sitio_web_centro_trabajo'] ?? null),
            'email_centro_trabajo' => $this->nf($post['email_centro_trabajo'] ?? null),
            'fax_centro_trabajo' => $this->nf($post['fax_centro_trabajo'] ?? null),
            'ingreso_mensual_usd' => $this->nn($post['ingreso_mensual_usd'] ?? null),
            'ingreso_mensual_cordobas' => $this->nn($post['ingreso_mensual_cordobas'] ?? null),
            'nombre_conyuge' => $this->nf($post['nombre_conyuge'] ?? null),
            'conyuge_primer_nombre' => $this->nf($post['conyuge_primer_nombre'] ?? null),
            'conyuge_segundo_nombre' => $this->nf($post['conyuge_segundo_nombre'] ?? null),
            'conyuge_primer_apellido' => $this->nf($post['conyuge_primer_apellido'] ?? null),
            'conyuge_segundo_apellido' => $this->nf($post['conyuge_segundo_apellido'] ?? null),
            'conyuge_direccion' => $this->nf($post['conyuge_direccion'] ?? null),
            'conyuge_telefono_domicilio' => $this->nf($post['conyuge_telefono_domicilio'] ?? null),
            'conyuge_celular' => $this->nf($post['conyuge_celular'] ?? null),
            'conyuge_email_personal' => $this->nf($post['conyuge_email_personal'] ?? null),
            'conyuge_profesion' => $this->nf($post['conyuge_profesion'] ?? null),
            'conyuge_ocupacion_actual' => $this->nf($post['conyuge_ocupacion_actual'] ?? null),
            'conyuge_nombre_centro_trabajo' => $this->nf($post['conyuge_nombre_centro_trabajo'] ?? null),
            'conyuge_direccion_centro_trabajo' => $this->nf($post['conyuge_direccion_centro_trabajo'] ?? null),
            'conyuge_email_centro_trabajo' => $this->nf($post['conyuge_email_centro_trabajo'] ?? null),
            'conyuge_sitio_web' => $this->nf($post['conyuge_sitio_web'] ?? null),
            'conyuge_telefono_centro_trabajo' => $this->nf($post['conyuge_telefono_centro_trabajo'] ?? null),
            'conyuge_fax_centro_trabajo' => $this->nf($post['conyuge_fax_centro_trabajo'] ?? null),
            'conyuge_apartado_postal' => $this->nf($post['conyuge_apartado_postal'] ?? null),
            'conyuge_ingreso_usd' => $this->nn($post['conyuge_ingreso_usd'] ?? null),
            'conyuge_ingreso_cordobas' => $this->nn($post['conyuge_ingreso_cordobas'] ?? null),
            'documento_legal_1_pais_emision' => $this->nf($post['documento_legal_1_pais_emision'] ?? null),
            'documento_legal_1_numero' => $this->nf($post['documento_legal_1_numero'] ?? null),
            'documento_legal_1_fecha_emision' => $this->nf($post['documento_legal_1_fecha_emision'] ?? null),
            'documento_legal_1_fecha_vencimiento' => $this->nf($post['documento_legal_1_fecha_vencimiento'] ?? null),
            'documento_legal_2_pais_emision' => $this->nf($post['documento_legal_2_pais_emision'] ?? null),
            'documento_legal_2_numero' => $this->nf($post['documento_legal_2_numero'] ?? null),
            'documento_legal_2_fecha_emision' => $this->nf($post['documento_legal_2_fecha_emision'] ?? null),
            'documento_legal_2_fecha_vencimiento' => $this->nf($post['documento_legal_2_fecha_vencimiento'] ?? null),
            // Map doc1/doc2 municipio values to specific DB columns
            'doc1_municipio_emision_documento' => $this->nf($post['documento_legal_1_departamento_municipio'] ?? null),
            'doc2_municipio_emision_documento' => $this->nf($post['documento_legal_2_departamento_municipio'] ?? null),
            // Origen de fondos: store as JSON array when checkboxes used
            'origen_fondos' => $this->nf(!empty($post['origen_fondos']) ? json_encode($post['origen_fondos']) : null),
            'origen_otros' => $this->nf($post['origen_otros'] ?? null),
            'proposito_relacion' => $this->nf($post['proposito_relacion'] ?? null),
            'actividad_esperada' => $this->nf($post['actividad_esperada'] ?? null),
            'actividad_esperada_json' => $this->nf($post['actividad_esperada_json'] ?? null),
            'actividad_esperada_observaciones' => $this->nf($post['actividad_esperada_observaciones'] ?? null),
            // Documentos doc1/doc2 omitted (hidden)
            // Tipo de relación (checkboxes) stored as JSON
            'tipo_relacion' => $this->nf(!empty($post['tipo_relacion']) ? json_encode($post['tipo_relacion']) : null),
            'tipo_relacion_otro' => $this->nf($post['tipo_relacion_otro'] ?? null),
            // Matriz de evaluación: score numeric and answers JSON
            'matriz_score' => $this->nn($post['matriz_score'] ?? null),
            'matriz_answers' => $this->nf($post['matriz_answers'] ?? null),
        ];

        $exist = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
        // Debug: log incoming categoria_empleo from POST
        $post_cat = isset($post['categoria_empleo']) ? $post['categoria_empleo'] : null;
        log_message('debug', 'Perfil_integral save - POST categoria_empleo: ' . var_export($post_cat, true));
        // Filter $data to table columns to avoid DB errors if migration wasn't applied yet
        try {
            $cols_q = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_perfil_integral_cliente'");
            $cols_arr = [];
            if ($cols_q) {
                foreach ($cols_q->result_array() as $r) $cols_arr[] = $r['COLUMN_NAME'];
            }
            // Log keys before filtering for debugging
            log_message('debug', 'Perfil_integral save - keys before filter: ' . json_encode(array_keys($data)));
            if (!empty($cols_arr)) {
                // If some expected fields are missing from the table, attempt to add them (safe, idempotent)
                $missing = [];
                foreach (array_keys($data) as $k) {
                    if (!in_array($k, $cols_arr)) $missing[] = $k;
                }
                if (!empty($missing)) {
                    foreach ($missing as $col) {
                        // attempt to add as TEXT to keep types permissive; log outcome
                        $col_esc = $this->db->escape_str($col);
                        try {
                            $this->db->query("ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `" . $col_esc . "` TEXT DEFAULT NULL");
                            $dberr = $this->db->error();
                            if (!empty($dberr['code'])) {
                                log_message('error', "Perfil_integral save - failed to add column {$col}: " . json_encode($dberr));
                            } else {
                                log_message('info', "Perfil_integral save - added missing column: {$col}");
                            }
                        } catch (Exception $e) {
                            log_message('error', "Perfil_integral save - failed to add column {$col}: " . $e->getMessage());
                        }
                    }
                    // refresh column list after attempted alters
                    $cols_q = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_perfil_integral_cliente'");
                    $cols_arr = [];
                    if ($cols_q) {
                        foreach ($cols_q->result_array() as $r) $cols_arr[] = $r['COLUMN_NAME'];
                    }
                }
                // Finally filter $data to the (possibly refreshed) table columns
                foreach (array_keys($data) as $k) {
                    if (!in_array($k, $cols_arr)) unset($data[$k]);
                }
            }
            // Log keys after filtering and which columns are present in the table
            log_message('debug', 'Perfil_integral save - keys after filter: ' . json_encode(array_keys($data)));
            log_message('debug', 'Perfil_integral save - table columns: ' . json_encode($cols_arr));
            // Log whether categoria_empleo survived filtering and its value
            if (array_key_exists('categoria_empleo', $data)) {
                log_message('debug', 'Perfil_integral save - data[categoria_empleo] after filter: ' . var_export($data['categoria_empleo'], true));
            } else {
                log_message('warning', 'Perfil_integral save - categoria_empleo not present in $data after filter');
            }
            // Check for expected fields and warn if missing after filter
            $expected = ['segundo_nombre','sexo','n_dependientes','pais_nacimiento','categoria_empleo','proposito_relacion','actividad_esperada'];
            foreach ($expected as $f) {
                if (!array_key_exists($f, $data)) {
                    log_message('info', "Perfil_integral save - expected field missing after filter: {$f}");
                }
            }
        } catch (Exception $e) {
            // If query fails, continue without filtering (migration should be applied)
            log_message('error', 'Perfil_integral save - INFORMATION_SCHEMA query failed: ' . $e->getMessage());
        }
        $stored = null;
        if ($exist) {
            $res = $this->Perfil_integral_model->update($exist->id, $data);
            $dberr = $this->db->error();
            if (!empty($dberr['code'])) {
                log_message('error', 'Perfil_integral update error: ' . json_encode($dberr) . ' | data keys: ' . implode(',', array_keys($data)));
                $this->session->set_flashdata('error', 'Error al actualizar perfil. Revise los logs del servidor.');
            } else {
                $this->session->set_flashdata('success', 'Perfil actualizado.');
            }
            // re-fetch and log stored value
            $stored = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
            if ($stored) log_message('info', 'Perfil_integral saved - stored categoria_empleo: ' . var_export($stored->categoria_empleo ?? null, true));
        } else {
            $newid = $this->Perfil_integral_model->insert($data);
            $dberr = $this->db->error();
            if (!empty($dberr['code'])) {
                log_message('error', 'Perfil_integral insert error: ' . json_encode($dberr) . ' | data keys: ' . implode(',', array_keys($data)));
                $this->session->set_flashdata('error', 'Error al crear perfil. Revise los logs del servidor.');
            } else {
                $this->session->set_flashdata('success', 'Perfil creado.');
            }
            // re-fetch and log stored value
            $stored = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
            if ($stored) log_message('info', 'Perfil_integral created - stored categoria_empleo: ' . var_export($stored->categoria_empleo ?? null, true));
        }

        // If debug flag present, return JSON with posted, prepared data and stored DB row
        $debug_flag = ($this->input->get('debug') == '1') || (isset($post['debug']) && $post['debug'] == '1');
        if ($debug_flag) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => true, 'action' => ($exist ? 'update' : 'insert'), 'posted' => $post, 'data' => $data, 'stored' => $stored));
            exit;
        }

        redirect('perfil_integral/create/'.$solicitud_id);
    }

    /**
     * AJAX endpoint to save only the matriz fields (score, answers, nivel_riesgo)
     */
    public function save_matriz_ajax()
    {
        $post = $this->input->post();
        header('Content-Type: application/json');
        $solicitud_id = isset($post['solicitud_id']) ? intval($post['solicitud_id']) : 0;
        if (! $solicitud_id) {
            echo json_encode(['status' => false, 'message' => 'Solicitud inválida']);
            return;
        }

        $matriz_score = $this->nn($post['matriz_score'] ?? null);
        $matriz_answers = $this->nf($post['matriz_answers'] ?? null);
        $nivel_riesgo = $this->nf($post['nivel_riesgo'] ?? null);

        $exist = $this->Perfil_integral_model->get_by_solicitud($solicitud_id);
        $data = [
            'matriz_score' => $matriz_score,
            'matriz_answers' => $matriz_answers,
            'nivel_riesgo' => $nivel_riesgo
        ];

        // Ensure the table has necessary columns (safe, idempotent)
        try{
            $cols_q = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_perfil_integral_cliente'");
            $cols_arr = [];
            if ($cols_q) {
                foreach ($cols_q->result_array() as $r) $cols_arr[] = $r['COLUMN_NAME'];
            }
            $missing = [];
            foreach (array_keys($data) as $k) {
                if (!in_array($k, $cols_arr)) $missing[] = $k;
            }
            if (!in_array('solicitud_id', $cols_arr)) $missing[] = 'solicitud_id';
            if (!empty($missing)) {
                foreach ($missing as $col) {
                    $col_esc = $this->db->escape_str($col);
                    try{
                        $this->db->query("ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `" . $col_esc . "` TEXT DEFAULT NULL");
                    }catch(Exception $e){
                        log_message('error', 'save_matriz_ajax - failed to add column '.$col.': '.$e->getMessage());
                    }
                }
                // refresh cols list
                $cols_q = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_perfil_integral_cliente'");
                $cols_arr = [];
                if ($cols_q) {
                    foreach ($cols_q->result_array() as $r) $cols_arr[] = $r['COLUMN_NAME'];
                }
            }
            // filter data to present columns to avoid DB errors
            foreach (array_keys($data) as $k) {
                if (!in_array($k, $cols_arr)) unset($data[$k]);
            }
        }catch(Exception $e){
            log_message('error', 'save_matriz_ajax - INFORMATION_SCHEMA query failed: '.$e->getMessage());
        }

        if ($exist) {
            $res = $this->Perfil_integral_model->update($exist->id, $data);
            $dberr = $this->db->error();
            if (!empty($dberr['code'])) {
                echo json_encode(['status' => false, 'message' => 'DB update error: ' . json_encode($dberr)]);
                return;
            }
            echo json_encode(['status' => true, 'message' => 'Matriz actualizada']);
            return;
        } else {
            // create new record with solicitud_id and matriz data
            $data['solicitud_id'] = $solicitud_id;
            $newid = $this->Perfil_integral_model->insert($data);
            $dberr = $this->db->error();
            if (!empty($dberr['code'])) {
                echo json_encode(['status' => false, 'message' => 'DB insert error: ' . json_encode($dberr)]);
                return;
            }
            if ($newid) {
                echo json_encode(['status' => true, 'message' => 'Matriz creada']);
                return;
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al crear perfil']);
                return;
            }
        }
    }

}
