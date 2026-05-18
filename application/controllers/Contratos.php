<?php
defined('BASEPATH') or exit('Acción no permitida');

class Contratos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Core_model', 'core_model');
        $this->load->model('Prestamos_model', 'prestamos_model');
        $this->load->library('ion_auth');
        if (!$this->ion_auth->logged_in()) {
            // If request is AJAX, return a JSON error instead of redirecting to login page
            if (isset($this->input) && $this->input->is_ajax_request()) {
                // Use plain JSON here because _json may depend on other state
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('status' => false, 'message' => 'Sesión expirada. Inicie sesión nuevamente.'));
                exit;
            }
            redirect('login');
        }
    }

    // Build a map of replacement tokens for a given prestamo
    private function _build_contract_replacements($prestamo)
    {
        $repl = array();
        if (!is_object($prestamo)) return $repl;

        // Try to load solicitud and uso
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
        }
        $uso = null;
        if ($solicitud && isset($solicitud->idsolicitud)) {
            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $solicitud->idsolicitud));
        }

        // Basic client / loan fields
        $cliente_nombre = isset($prestamo->cliente_nombre) ? $prestamo->cliente_nombre : (isset($prestamo->apellidos) ? trim($prestamo->apellidos . ' ' . $prestamo->nombres) : (isset($solicitud->apellidos) ? trim($solicitud->apellidos . ' ' . $solicitud->nombres) : ''));
        $cliente_numero = isset($prestamo->idcliente) ? $prestamo->idcliente : (isset($prestamo->cliente_numero) ? $prestamo->cliente_numero : '');
        $doc_identidad = isset($prestamo->doc_identidad) ? $prestamo->doc_identidad : (isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($prestamo->documento) ? $prestamo->documento : ''));
        $direccion = isset($prestamo->direccion) ? $prestamo->direccion : (isset($solicitud->direccion) ? $solicitud->direccion : '');

        // amounts and terms
        $monto_val = '';
        foreach (array('monto_credito','monto','monto_aprobado','monto_desembolsado') as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $monto_val = $prestamo->{$f}; break; } }
        if ($monto_val === '' && isset($uso->monto_solicitado)) $monto_val = $uso->monto_solicitado;
        if ($monto_val === '' && isset($solicitud->monto_solicitado)) $monto_val = $solicitud->monto_solicitado;
        $monto_fmt = $monto_val !== '' ? ('$' . number_format(floatval($monto_val),2)) : '';

        $plazo_val = '';
        foreach (array('numero_coutas','numero_cuotas','nro_cuotas','plazo_meses','plazo') as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $plazo_val = $prestamo->{$f}; break; } }
        if ($plazo_val === '' && isset($uso->plazo_solicitado)) $plazo_val = $uso->plazo_solicitado;
        if ($plazo_val === '' && isset($solicitud->plazo_meses)) $plazo_val = $solicitud->plazo_meses;

        $tasa_val = null;
        if (isset($prestamo->tasa)) $tasa_val = $prestamo->tasa;
        elseif (isset($prestamo->interes_credito)) $tasa_val = $prestamo->interes_credito;

        // cuotas schedule HTML
        $cuotas = $this->core_model->get_by_id_all('tb_prestamo_cuotas', array('idprestamo' => isset($prestamo->idprestamo) ? $prestamo->idprestamo : (isset($prestamo->id) ? $prestamo->id : 0)));
        if (!is_array($cuotas)) $cuotas = array();
        $cuotas_html = '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $cuotas_html .= '<thead><tr><th style="border:1px solid #ddd;padding:6px">#</th><th style="border:1px solid #ddd;padding:6px">Fecha</th><th style="border:1px solid #ddd;padding:6px">Cuota</th><th style="border:1px solid #ddd;padding:6px">Capital</th><th style="border:1px solid #ddd;padding:6px">Interés</th><th style="border:1px solid #ddd;padding:6px">Saldo</th></tr></thead><tbody>';
        foreach ($cuotas as $c) {
            $num = isset($c->numero) ? $c->numero : '';
            $fecha = isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : (isset($c->fecha) ? $c->fecha : '');
            $cuota = isset($c->cuota) ? '$' . number_format($c->cuota,2) : '';
            $principal = isset($c->principal) ? '$' . number_format($c->principal,2) : '';
            $interes = isset($c->interes) ? '$' . number_format($c->interes,2) : '';
            $saldo = isset($c->saldo) ? '$' . number_format($c->saldo,2) : '';
            $cuotas_html .= '<tr><td style="border:1px solid #ddd;padding:6px">'.$num.'</td><td style="border:1px solid #ddd;padding:6px">'.$fecha.'</td><td style="border:1px solid #ddd;padding:6px">'.$cuota.'</td><td style="border:1px solid #ddd;padding:6px">'.$principal.'</td><td style="border:1px solid #ddd;padding:6px">'.$interes.'</td><td style="border:1px solid #ddd;padding:6px">'.$saldo.'</td></tr>';
        }
        $cuotas_html .= '</tbody></table>';

        // garantias by solicitud
        $garantias_html = '';
        if ($solicitud && isset($solicitud->idsolicitud)) {
            $grows = $this->core_model->get_by_id_all('tb_garantias', array('idsolicitud' => $solicitud->idsolicitud));
            if (is_array($grows) && count($grows) > 0) {
                $garantias_html .= '<table style="width:100%;border-collapse:collapse;font-size:12px">';
                $garantias_html .= '<thead><tr><th style="border:1px solid #ddd;padding:6px">Tipo</th><th style="border:1px solid #ddd;padding:6px">Descripción</th><th style="border:1px solid #ddd;padding:6px">Avaluo</th><th style="border:1px solid #ddd;padding:6px">Estado</th></tr></thead><tbody>';
                foreach ($grows as $g) {
                    $tipo = isset($g->tipo) ? $g->tipo : (isset($g->descripcion) ? $g->descripcion : '');
                    $desc = isset($g->descripcion) ? $g->descripcion : '';
                    $avaluo = isset($g->avaluo) ? '$' . number_format(floatval($g->avaluo),2) : '';
                    $estado = isset($g->estado) ? $g->estado : '';
                    $garantias_html .= '<tr><td style="border:1px solid #ddd;padding:6px">'.$tipo.'</td><td style="border:1px solid #ddd;padding:6px">'.$desc.'</td><td style="border:1px solid #ddd;padding:6px">'.$avaluo.'</td><td style="border:1px solid #ddd;padding:6px">'.$estado.'</td></tr>';
                }
                $garantias_html .= '</tbody></table>';
            }
        }

        // perfil integral if present
        $perfil = null;
        if ($solicitud && isset($solicitud->idsolicitud)) {
            if ($this->db->table_exists('tb_perfil_integral')) {
                $perfil = $this->core_model->get_by_id('tb_perfil_integral', array('idsolicitud' => $solicitud->idsolicitud));
            }
        }

        // system/company info
        $sys = $this->core_model->get_all('tb_sistema');
        $empresa = (is_array($sys) && count($sys) > 0) ? $sys[0] : null;

        // fill replacements (lowercase keys)
        $repl['{{generated_at}}'] = date('d/m/Y');
        $repl['{{idprestamo}}'] = isset($prestamo->idprestamo) ? $prestamo->idprestamo : (isset($prestamo->id) ? $prestamo->id : '');
        $repl['{{idsolicitud}}'] = isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : (isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : '');
        $repl['{{cliente_nombre}}'] = $cliente_nombre;
        $repl['{{cliente_nombres}}'] = isset($solicitud->nombres) ? $solicitud->nombres : (isset($prestamo->nombres) ? $prestamo->nombres : '');
        $repl['{{cliente_apellidos}}'] = isset($solicitud->apellidos) ? $solicitud->apellidos : (isset($prestamo->apellidos) ? $prestamo->apellidos : '');
        $repl['{{cliente_numero}}'] = $cliente_numero;
        $repl['{{doc_identidad}}'] = $doc_identidad;
        $repl['{{deudor_fullname}}'] = $cliente_nombre;
        $repl['{{deudor_doc}}'] = $doc_identidad;
        $repl['{{deudor_direccion}}'] = $direccion;
        $repl['{{monto_credito}}'] = $monto_fmt;
        $repl['{{monto}}'] = $monto_fmt;
        $repl['{{monto_raw}}'] = $monto_val;
        $repl['{{plazo_meses}}'] = $plazo_val;
        $repl['{{numero_cuotas}}'] = $plazo_val;
        $repl['{{interes}}'] = is_numeric($tasa_val) ? (floatval($tasa_val) * (floatval($tasa_val) > 1 ? 1 : 1)) : (isset($tasa_val) ? $tasa_val : '');
        $repl['{{interes_percent}}'] = is_numeric($tasa_val) ? (floatval($tasa_val) > 1 ? $tasa_val . '%' : rtrim(rtrim(number_format(floatval($tasa_val)*100,2),'0'),'.') . '%') : (isset($tasa_val) ? $tasa_val : '');
        $repl['{{cuotas_table}}'] = $cuotas_html;
        $repl['{{garantias_table}}'] = $garantias_html;

        // perfil fields (best-effort)
        if ($perfil) {
            foreach ($perfil as $k => $v) {
                if (is_scalar($v)) {
                    $repl['{{perfil_'.$k.'}}'] = $v;
                }
            }
        }

        // company/system replacements
        if ($empresa) {
            $repl['{{empresa_razon_social}}'] = isset($empresa->razon_social) ? $empresa->razon_social : '';
            $repl['{{empresa_comercial}}'] = isset($empresa->nombre_comercial) ? $empresa->nombre_comercial : (isset($empresa->empresa_comercial) ? $empresa->empresa_comercial : '');
            $repl['{{acreedor_fullname}}'] = isset($empresa->representante) ? $empresa->representante : '';
            $repl['{{acreedor_doc}}'] = isset($empresa->representante_doc) ? $empresa->representante_doc : '';
        }

        // build variants (uppercase and percent-wrapped)
        $variants = array();
        foreach ($repl as $k => $v) {
            $u = strtoupper(trim($k,"{}"));
            $variants['{{'.$u.'}}'] = $v;
            $variants['%'.$u.'%'] = $v;
        }
        // merge back, prefer specific keys already set
        foreach ($variants as $k=>$v) if (!isset($repl[$k])) $repl[$k] = $v;

        return $repl;
    }

    // Internal helper: ensure JSON responses even if PHP emitted warnings/notices
    private function _json($data)
    {
        // capture any prior output (warnings/notices) and log it
        if (function_exists('ob_get_length') && ob_get_length() !== false) {
            $buf = @ob_get_clean();
            if ($buf !== null && trim($buf) !== '') {
                log_message('error', 'Contratos controller stray output: ' . substr($buf, 0, 4000));
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // View generated contract HTML in browser
    public function view($idprestamo = null)
    {
        if (!$idprestamo) show_404();
        if (!$this->db->table_exists('tb_contratos')) show_404();
        $contract = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
        if (!$contract) show_404();
        // Simple wrapper to display stored HTML
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Contrato ' . htmlspecialchars($idprestamo) . '</title></head><body style="font-family:DejaVu Sans, Arial, Helvetica, sans-serif; padding:12px;">';
        echo $contract->contenido;
        echo '</body></html>';
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Contratos',
            'subtitulo' => 'Generar y administrar contratos por préstamo',
            'icono' => 'fas fa-file-contract'
        );

        $this->load->view('layout/header', $data);
        $this->load->view('contratos/index', $data);
        $this->load->view('layout/footer');
    }

    // Return prestamos (AJAX) - filtered to likely approved ones if available
    public function get_prestamos()
    {
        if (!$this->input->is_ajax_request()) show_404();
        // Prefer to list solicitudes iniciales que fueron aprobadas.
        $results = array();
        if ($this->db->table_exists('tb_solicitudes')) {
            // support multiple possible approval fields/values used across the app
            // prefer spanish 'estado_aprobacion'='aprobado', but accept 'aprob_status' too
            $this->db->from('tb_solicitudes');
            $this->db->group_start();
            $this->db->where("estado_aprobacion", 'aprobado');
            $this->db->or_where("aprob_status", 'approved');
            $this->db->or_where("aprob_status", 'aprobado');
            $this->db->group_end();
            $sols = $this->db->get()->result();
            if (is_array($sols)) {
                foreach ($sols as $s) {
                    $item = new stdClass();
                    $item->idsolicitud = isset($s->idsolicitud) ? $s->idsolicitud : null;
                    // try to find linked prestamo
                    $p = null;
                    if ($this->db->table_exists('tb_prestamos')) {
                        $p = $this->db->from('tb_prestamos')->where('idsolicitud', $item->idsolicitud)->limit(1)->get()->row();
                    }
                    $item->idprestamo = $p ? (isset($p->idprestamo) ? $p->idprestamo : (isset($p->id) ? $p->id : null)) : null;
                    // client name and monto/fecha fields use solicitud by default, fallback to prestamo
                    $item->cliente_nombre = trim((isset($s->apellidos) ? $s->apellidos : '') . ' ' . (isset($s->nombres) ? $s->nombres : ''));
                    $item->monto_credito = (isset($p->monto_credito) && $p->monto_credito !== '') ? $p->monto_credito : (isset($s->monto_solicitado) ? $s->monto_solicitado : '');
                    $item->fecha_credito = isset($p->fecha_credito) && $p->fecha_credito ? $p->fecha_credito : (isset($s->fecha_solicitud) ? $s->fecha_solicitud : '');

                    // has_contract: check tb_contratos by idprestamo if present, else false
                    $has_contract = false;
                    if ($this->db->table_exists('tb_contratos') && $item->idprestamo) {
                        $row = $this->db->select('id')->from('tb_contratos')->where('idprestamo', $item->idprestamo)->limit(1)->get()->row();
                        $has_contract = $row ? true : false;
                    }
                    $item->has_contract = $has_contract;

                    $results[] = $item;
                }
            }
        }

        echo json_encode(array('status' => true, 'prestamos' => $results));
    }

    // Return available contract templates (AJAX)
    public function get_templates()
    {
        if (!$this->input->is_ajax_request()) show_404();

        // Template definitions; 'file' points to a template in views/contratos/templates/
        $templates = array(
            array('id' => 1, 'name' => 'Contrato de Mutuo con Fiador', 'file' => ''),
            array('id' => 2, 'name' => 'Contrato de Mutuo Prestamos Empleado', 'file' => ''),
            array('id' => 3, 'name' => 'Contrato de Mutuo sin Fiador', 'file' => ''),
            array('id' => 4, 'name' => 'CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR', 'file' => 'contrato_privado_mutuo_comision_amortizada_sin_fiador.html'),
            array('id' => 5, 'name' => 'Contrato Privado MutuoComision Amortizado', 'file' => '')
        );

        echo json_encode(array('status' => true, 'templates' => $templates));
    }

    // AJAX preview: render template filled with data for a given prestamo
    public function preview()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $idprestamo = $this->input->get('idprestamo');
        $template_id = $this->input->get('template_id');
        if (!$idprestamo || !$template_id) { echo json_encode(array('status'=>false,'message'=>'Faltan parámetros')); return; }

        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if (!$prestamo) {
            // fallback: try tb_creditos via Prestamos_model (joins client and detail)
            if (method_exists($this->prestamos_model, 'get_by_id')) {
                $p = $this->prestamos_model->get_by_id($idprestamo);
                if ($p) {
                    // normalize to expected fields
                    $p->idprestamo = isset($p->id) ? $p->id : (isset($p->idprestamo) ? $p->idprestamo : $idprestamo);
                    // map some common fields
                    if (!isset($p->cliente_nombre) && isset($p->apellidos)) {
                        $p->cliente_nombre = trim($p->apellidos . ' ' . $p->nombres);
                    }
                    $prestamo = $p;
                }
            }
        }
        if (!$prestamo) { echo json_encode(array('status'=>false,'message'=>'Préstamo no encontrado')); return; }

        // Try to load related solicitud and uso_credito
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
        }

        $uso = null;
        if ($solicitud && isset($solicitud->idsolicitud)) {
            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $solicitud->idsolicitud));
        }

        // Map template id -> filename
        $map = array(
            4 => 'contrato_privado_mutuo_comision_amortizada_sin_fiador.html'
        );

        if (!isset($map[intval($template_id)]) || !$map[intval($template_id)]) {
            echo json_encode(array('status'=>false,'message'=>'Plantilla no disponible (aún)')); return;
        }

        $file = 'application/views/contratos/templates/' . $map[intval($template_id)];
        if (!file_exists($file)) { echo json_encode(array('status'=>false,'message'=>'Archivo de plantilla no encontrado')); return; }

        $content = file_get_contents($file);

        // Prepare replacement map using prestamo, solicitud and uso
        $repl = array();
        $repl['{{generated_at}}'] = date('d/m/Y');
        $repl['{{idprestamo}}'] = isset($prestamo->idprestamo) ? $prestamo->idprestamo : '';
        $repl['{{idsolicitud}}'] = isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : (isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : '');
        $repl['{{cliente_nombre}}'] = isset($prestamo->cliente_nombre) ? $prestamo->cliente_nombre : (isset($prestamo->apellidos) ? trim($prestamo->apellidos . ' ' . $prestamo->nombres) : (isset($solicitud->apellidos) ? trim($solicitud->apellidos . ' ' . $solicitud->nombres) : ''));
        $repl['{{cliente_numero}}'] = isset($prestamo->idcliente) ? $prestamo->idcliente : (isset($prestamo->cliente_numero) ? $prestamo->cliente_numero : '');
        $repl['{{doc_identidad}}'] = isset($prestamo->doc_identidad) ? $prestamo->doc_identidad : (isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($prestamo->documento) ? $prestamo->documento : ''));
        // deudor placeholders matching template
        $repl['{{deudor_fullname}}'] = $repl['{{cliente_nombre}}'];
        $repl['{{deudor_doc}}'] = $repl['{{doc_identidad}}'];
        $repl['{{deudor_direccion}}'] = isset($prestamo->direccion) ? $prestamo->direccion : (isset($solicitud->direccion) ? $solicitud->direccion : '');
        // Prefer values from approved loan ($prestamo). Try common field names, then fallback to solicitud/uso.
        $monto_fields = array('monto_credito','monto','monto_aprobado','monto_desembolsado');
        $plazo_fields = array('numero_coutas','numero_cuotas','nro_cuotas','plazo_meses','plazo');
        $monto_val = '';
        foreach ($monto_fields as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $monto_val = $prestamo->{$f}; break; } }
        if ($monto_val === '' && isset($uso->monto_solicitado)) { $monto_val = $uso->monto_solicitado; }
        if ($monto_val === '' && isset($solicitud->monto_solicitado)) { $monto_val = $solicitud->monto_solicitado; }
        $repl['{{monto_credito}}'] = $monto_val !== '' ? ('$' . number_format(floatval($monto_val),2)) : '';

        $plazo_val = '';
        foreach ($plazo_fields as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $plazo_val = $prestamo->{$f}; break; } }
        if ($plazo_val === '' && isset($uso->plazo_solicitado)) { $plazo_val = $uso->plazo_solicitado; }
        if ($plazo_val === '' && isset($solicitud->plazo_meses)) { $plazo_val = $solicitud->plazo_meses; }
        $repl['{{plazo_meses}}'] = $plazo_val !== '' ? $plazo_val : '';
        $repl['{{fecha_solicitud}}'] = isset($solicitud->fecha_solicitud) ? $solicitud->fecha_solicitud : '';
        $repl['{{empresa_razon_social}}'] = '';
        $repl['{{direccion}}'] = isset($solicitud->direccion) ? $solicitud->direccion : '';

        // Try to load system/company info (tb_sistema) if available
        $sys = $this->core_model->get_all('tb_sistema');
        if (is_array($sys) && count($sys) > 0) {
            $s0 = $sys[0];
            $repl['{{empresa_razon_social}}'] = isset($s0->razon_social) ? $s0->razon_social : (isset($s0->empresa_razon_social) ? $s0->empresa_razon_social : '');
            $repl['{{empresa_comercial}}'] = isset($s0->nombre_comercial) ? $s0->nombre_comercial : (isset($s0->empresa_comercial) ? $s0->empresa_comercial : $repl['{{empresa_razon_social}}']);
            $repl['{{acreedor_fullname}}'] = isset($s0->representante) ? $s0->representante : (isset($s0->contacto) ? $s0->contacto : $repl['{{empresa_comercial}}']);
            $repl['{{acreedor_doc}}'] = isset($s0->representante_doc) ? $s0->representante_doc : (isset($s0->documento_representante) ? $s0->documento_representante : '');
        } else {
            $repl['{{empresa_comercial}}'] = isset($repl['{{empresa_razon_social}}']) && $repl['{{empresa_razon_social}}'] ? $repl['{{empresa_razon_social}}'] : 'CREDIBLAMEN';
            $repl['{{acreedor_fullname}}'] = $repl['{{empresa_comercial}}'];
            $repl['{{acreedor_doc}}'] = '';
        }

        // From uso_credito (if exists)
        $repl['{{monto_solicitado}}'] = isset($uso->monto_solicitado) ? ('$' . number_format($uso->monto_solicitado,2)) : '';
        $repl['{{plazo_solicitado}}'] = isset($uso->plazo_solicitado) ? $uso->plazo_solicitado : '';
        $repl['{{destino_credito}}'] = isset($uso->destino_prestamo) ? $uso->destino_prestamo : (isset($solicitud->rubro_credito) ? $solicitud->rubro_credito : '');
        $repl['{{destino_detalle}}'] = isset($uso->destino_detalle) ? $uso->destino_detalle : '';
        $repl['{{descripcion}}'] = isset($uso->descripcion) ? $uso->descripcion : '';

        // simple replacement
        $filled = strtr($content, $repl);

        echo json_encode(array('status'=>true,'html'=>$filled));
    }

    // Generate a contract record (AJAX)
    public function generate()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $idprestamo = $this->input->post('idprestamo');
        $template_id = $this->input->post('template_id');

        if (!$idprestamo || !$template_id) {
            echo json_encode(array('status' => false, 'message' => 'Faltan parámetros')); return;
        }

        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if (!$prestamo) {
            if (method_exists($this->prestamos_model, 'get_by_id')) {
                $p = $this->prestamos_model->get_by_id($idprestamo);
                if ($p) {
                    $p->idprestamo = isset($p->id) ? $p->id : (isset($p->idprestamo) ? $p->idprestamo : $idprestamo);
                    if (!isset($p->cliente_nombre) && isset($p->apellidos)) {
                        $p->cliente_nombre = trim($p->apellidos . ' ' . $p->nombres);
                    }
                    $prestamo = $p;
                }
            }
        }
        if (!$prestamo) {
            echo json_encode(array('status' => false, 'message' => 'Préstamo no encontrado')); return;
        }

        // Load cuotas to compute schedule details
        $cuotas = $this->core_model->get_by_id_all('tb_prestamo_cuotas', array('idprestamo' => $idprestamo));
        if (!is_array($cuotas)) $cuotas = array();
        $numero_cuotas = count($cuotas);
        $sum_cuotas = 0.0;
        $primer_venc = '';
        $ultima_venc = '';
        $ultima_cuota_amount = '';
        foreach ($cuotas as $i => $c) {
            $sum_cuotas += isset($c->cuota) ? floatval($c->cuota) : 0;
            if ($i === 0) { $primer_venc = isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : ''; }
            $ultima_venc = isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : $ultima_venc;
            $ultima_cuota_amount = isset($c->cuota) ? '$' . number_format($c->cuota,2) : $ultima_cuota_amount;
        }
        $monto_cuota_example = $numero_cuotas ? ('$' . number_format(($sum_cuotas / max(1,$numero_cuotas)),2)) : '';

        // If a template file exists for this template id, load and fill it
        $map = array(
            4 => 'contrato_privado_mutuo_comision_amortizada_sin_fiador.html'
        );

        $html = '';
        if (isset($map[intval($template_id)]) && $map[intval($template_id)]) {
            $file = 'application/views/contratos/templates/' . $map[intval($template_id)];
            if (file_exists($file)) {
                $content = file_get_contents($file);
                // prepare replacements similar to preview()
                $solicitud = null;
                if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
                    $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
                    if (!$solicitud) $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
                }
                $uso = null;
                if ($solicitud && isset($solicitud->idsolicitud)) {
                    $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $solicitud->idsolicitud));
                }

                $repl = array();
                $repl['{{generated_at}}'] = date('d/m/Y');
                $repl['{{idprestamo}}'] = isset($prestamo->idprestamo) ? $prestamo->idprestamo : '';
                $repl['{{idsolicitud}}'] = isset($prestamo->idsolicitud) ? $prestamo->idsolicitud : (isset($solicitud->idsolicitud) ? $solicitud->idsolicitud : '');
                $repl['{{cliente_nombre}}'] = isset($prestamo->cliente_nombre) ? $prestamo->cliente_nombre : (isset($prestamo->apellidos) ? trim($prestamo->apellidos . ' ' . $prestamo->nombres) : (isset($solicitud->apellidos) ? trim($solicitud->apellidos . ' ' . $solicitud->nombres) : ''));
                $repl['{{cliente_numero}}'] = isset($prestamo->idcliente) ? $prestamo->idcliente : (isset($prestamo->cliente_numero) ? $prestamo->cliente_numero : '');
                $repl['{{doc_identidad}}'] = isset($prestamo->doc_identidad) ? $prestamo->doc_identidad : (isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($prestamo->documento) ? $prestamo->documento : ''));
                // deudor placeholders matching template
                $repl['{{deudor_fullname}}'] = $repl['{{cliente_nombre}}'];
                $repl['{{deudor_doc}}'] = $repl['{{doc_identidad}}'];
                $repl['{{deudor_direccion}}'] = isset($prestamo->direccion) ? $prestamo->direccion : (isset($solicitud->direccion) ? $solicitud->direccion : '');
                // Prefer approved loan fields for amount and term
                $monto_val = '';
                foreach (array('monto_credito','monto','monto_aprobado','monto_desembolsado') as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $monto_val = $prestamo->{$f}; break; } }
                if ($monto_val === '' && isset($uso->monto_solicitado)) { $monto_val = $uso->monto_solicitado; }
                if ($monto_val === '' && isset($solicitud->monto_solicitado)) { $monto_val = $solicitud->monto_solicitado; }
                $repl['{{monto_credito}}'] = $monto_val !== '' ? ('$' . number_format(floatval($monto_val),2)) : '';

                $plazo_val = '';
                foreach (array('numero_coutas','numero_cuotas','nro_cuotas','plazo_meses','plazo') as $f) { if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $plazo_val = $prestamo->{$f}; break; } }
                if ($plazo_val === '' && isset($uso->plazo_solicitado)) { $plazo_val = $uso->plazo_solicitado; }
                if ($plazo_val === '' && isset($solicitud->plazo_meses)) { $plazo_val = $solicitud->plazo_meses; }
                $repl['{{plazo_meses}}'] = $plazo_val !== '' ? $plazo_val : '';
                $repl['{{fecha_solicitud}}'] = isset($solicitud->fecha_solicitud) ? $solicitud->fecha_solicitud : '';
                $repl['{{empresa_razon_social}}'] = '';
                $repl['{{direccion}}'] = isset($solicitud->direccion) ? $solicitud->direccion : '';
                // Try to load system/company info (tb_sistema) if available
                $sys = $this->core_model->get_all('tb_sistema');
                if (is_array($sys) && count($sys) > 0) {
                    $s0 = $sys[0];
                    $repl['{{empresa_razon_social}}'] = isset($s0->razon_social) ? $s0->razon_social : (isset($s0->empresa_razon_social) ? $s0->empresa_razon_social : '');
                    $repl['{{empresa_comercial}}'] = isset($s0->nombre_comercial) ? $s0->nombre_comercial : (isset($s0->empresa_comercial) ? $s0->empresa_comercial : $repl['{{empresa_razon_social}}']);
                    $repl['{{acreedor_fullname}}'] = isset($s0->representante) ? $s0->representante : (isset($s0->contacto) ? $s0->contacto : $repl['{{empresa_comercial}}']);
                    $repl['{{acreedor_doc}}'] = isset($s0->representante_doc) ? $s0->representante_doc : (isset($s0->documento_representante) ? $s0->documento_representante : '');
                } else {
                    $repl['{{empresa_comercial}}'] = isset($repl['{{empresa_razon_social}}']) && $repl['{{empresa_razon_social}}'] ? $repl['{{empresa_razon_social}}'] : 'CREDIBLAMEN';
                    $repl['{{acreedor_fullname}}'] = $repl['{{empresa_comercial}}'];
                    $repl['{{acreedor_doc}}'] = '';
                }
                $repl['{{monto_solicitado}}'] = isset($uso->monto_solicitado) ? ('$' . number_format($uso->monto_solicitado,2)) : '';
                $repl['{{plazo_solicitado}}'] = isset($uso->plazo_solicitado) ? $uso->plazo_solicitado : '';
                $repl['{{destino_credito}}'] = isset($uso->destino_prestamo) ? $uso->destino_prestamo : (isset($solicitud->rubro_credito) ? $solicitud->rubro_credito : '');
                $repl['{{destino_detalle}}'] = isset($uso->destino_detalle) ? $uso->destino_detalle : '';
                $repl['{{descripcion}}'] = isset($uso->descripcion) ? $uso->descripcion : '';

                // schedule replacements
                $repl['{{numero_cuotas}}'] = $numero_cuotas;
                $repl['{{numero_cuotas_principales}}'] = $numero_cuotas; // same by default
                $repl['{{monto_cuota}}'] = $monto_cuota_example;
                $repl['{{monto_cuota_ejemplo}}'] = $monto_cuota_example;
                $repl['{{ultima_cuota_amount}}'] = $ultima_cuota_amount;
                $repl['{{primer_vencimiento}}'] = $primer_venc;
                $repl['{{fecha_vencimiento}}'] = $ultima_venc;


                $html = strtr($content, $repl);
            }
        }

        // fallback if no html produced
        if (!$html) {
            $html = '<div style="font-family:DejaVu Sans, Arial, Helvetica, sans-serif;">';
            $html .= '<h2>Contrato (Plantilla #' . intval($template_id) . ')</h2>';
            $html .= '<p>Contrato generado para el préstamo N° ' . intval($idprestamo) . '.</p>';
            $html .= '<p>Cliente: ' . (isset($prestamo->cliente_nombre) ? html_escape($prestamo->cliente_nombre) : '') . '</p>';
            $html .= '</div>';
        }

        $user = $this->ion_auth->user()->row();

        $record = array(
            'idprestamo' => $idprestamo,
            'template_id' => $template_id,
            'contenido' => $html,
            'created_by' => isset($user->id) ? $user->id : null,
            'created_at' => date('Y-m-d H:i:s')
        );

        // Attempt to insert into tb_contratos (migration should create this table)
        // Ensure only one contract per prestamo: if exists, refuse generation
        $ok = false;
        if ($this->db->table_exists('tb_contratos')) {
            $existing = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
            if ($existing) {
                echo json_encode(array('status' => false, 'message' => 'Contrato ya generado para este préstamo')); return;
            }
            $ok = $this->core_model->insert('tb_contratos', $record);
        } else {
            // Try to insert anyway; core_model->insert will fail if table missing
            $ok = $this->core_model->insert('tb_contratos', $record);
        }

        if ($ok) {
            echo json_encode(array('status' => true, 'message' => 'Contrato generado', 'contract' => $record));
        } else {
            echo json_encode(array('status' => false, 'message' => 'No se pudo guardar el contrato (verifique tabla tb_contratos)'));
        }
    }

    // AJAX: list available template files from the Contratos folder
    public function list_templates_from_folder()
    {
        if (!$this->input->is_ajax_request()) {
            // Log details to help debug why the client call isn't considered AJAX
            $hdr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '(none)';
            log_message('error', 'list_templates_from_folder called without AJAX header. X-Requested-With=' . $hdr . ' URI=' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''));
            // Return JSON so client-side receives a parseable response instead of HTML 404/login page
            $this->_json(array('status' => false, 'message' => 'Petición no válida (no AJAX) o sesión expirada'));
        }
        $folder = FCPATH . 'Contratos';
        $allowed = array('html','htm','docx','txt');
        $templates = array();
        if (is_dir($folder)) {
            $files = scandir($folder);
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                $full = $folder . DIRECTORY_SEPARATOR . $f;
                if (!is_file($full)) continue;
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) continue;
                $templates[] = array('name' => $f, 'file' => $f, 'size' => filesize($full));
            }
        }
        echo json_encode(array('status' => true, 'templates' => $templates));
    }

    // AJAX: generate a contract from a file in the Contratos folder and persist to tb_contratos
    public function generate_from_file()
    {
        if (!$this->input->is_ajax_request()) show_404();
        ob_start();
        $idprestamo = $this->input->post('idprestamo');
        $filename = $this->input->post('filename');
        if (!$idprestamo || !$filename) { $this->_json(array('status'=>false,'message'=>'Faltan parámetros')); }

        // sanitize filename (prevent path traversal)
        $basename = basename($filename);
        $folder = FCPATH . 'Contratos' . DIRECTORY_SEPARATOR;
        $fullpath = $folder . $basename;
        if (!file_exists($fullpath) || !is_readable($fullpath)) { $this->_json(array('status'=>false,'message'=>'Archivo no encontrado')); }

        $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        $content = '';
        try {
            if (in_array($ext, array('html','htm','txt'))) {
                $content = file_get_contents($fullpath);
            } elseif ($ext === 'docx') {
                if (!class_exists('ZipArchive')) {
                    // ZipArchive not available: give explicit error to help debugging
                    $this->_json(array('status'=>false,'message'=>'PHP ZipArchive no disponible. Habilite la extensión zip para procesar archivos .docx'));
                }
                if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($fullpath) === true) {
                    $idx = $zip->locateName('word/document.xml', ZIPARCHIVE::FL_NOCASE);
                    if ($idx !== false) {
                        $xml = $zip->getFromIndex($idx);
                        // basic extraction: remove xml tags but keep text nodes
                        // replace <w:t>text</w:t> with text
                        $xml = preg_replace('/<w:t[^>]*>/', '', $xml);
                        $xml = str_replace(array('</w:t>','</w:p>','<w:p>','<w:tab/>'), array('','\n','\n','\t'), $xml);
                        $content = strip_tags($xml);
                        // wrap in minimal HTML
                        $content = '<div style="font-family:DejaVu Sans, Arial, Helvetica, sans-serif;">' . nl2br(htmlspecialchars($content)) . '</div>';
                    }
                    $zip->close();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error reading contract file ' . $fullpath . ': ' . $e->getMessage());
        }

        if (!$content) { $this->_json(array('status'=>false,'message'=>'No se pudo leer/convertir el archivo')); }

        // Load prestamo and related data (reuse mapping logic from generate())
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if (!$prestamo) {
            if (method_exists($this->prestamos_model, 'get_by_id')) {
                $p = $this->prestamos_model->get_by_id($idprestamo);
                if ($p) { $p->idprestamo = isset($p->id) ? $p->id : (isset($p->idprestamo) ? $p->idprestamo : $idprestamo); $prestamo = $p; }
            }
        }
        if (!$prestamo) { $this->_json(array('status'=>false,'message'=>'Préstamo no encontrado')); }

        // try to load solicitud and uso (same code as generate/preview)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
        }
        $uso = null;
        if ($solicitud && isset($solicitud->idsolicitud)) {
            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $solicitud->idsolicitud));
        }

        // Build a comprehensive replacement map using helper
        $repl = $this->_build_contract_replacements($prestamo);
        $filled = strtr($content, $repl);

        // persist into tb_contratos avoiding duplicates
        try {
            if ($this->db->table_exists('tb_contratos')) {
                $existing = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
                if ($existing) { $this->_json(array('status'=>false,'message'=>'Contrato ya generado para este préstamo')); }
                $user = $this->ion_auth->user()->row();
                $rec = array('idprestamo' => $idprestamo, 'template_id' => 0, 'contenido' => $filled, 'created_by' => isset($user->id) ? $user->id : null, 'created_at' => date('Y-m-d H:i:s'));
                $ok = $this->core_model->insert('tb_contratos', $rec);
                    if ($ok) {
                    $url = base_url('contratos/view/' . $idprestamo);
                    $this->_json(array('status'=>true,'message'=>'Contrato generado','url'=>$url));
                }
            }
        } catch (Exception $e) {
            log_message('error','generate_from_file failed: '.$e->getMessage());
        }

        $this->_json(array('status'=>false,'message'=>'No se pudo guardar el contrato (verifique tabla tb_contratos)'));
    }

    // AJAX: preview a template file filled with prestamo data, without persisting
    public function preview_from_file()
    {
        if (!$this->input->is_ajax_request()) show_404();
        ob_start();
        $idprestamo = $this->input->post('idprestamo');
        $filename = $this->input->post('filename');
        if (!$idprestamo || !$filename) { $this->_json(array('status'=>false,'message'=>'Faltan parámetros')); }

        $basename = basename($filename);
        $folder = FCPATH . 'Contratos' . DIRECTORY_SEPARATOR;
        $fullpath = $folder . $basename;
        if (!file_exists($fullpath) || !is_readable($fullpath)) { $this->_json(array('status'=>false,'message'=>'Archivo no encontrado')); }

        $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        $content = '';
        try {
            if (in_array($ext, array('html','htm','txt'))) {
                $content = file_get_contents($fullpath);
            } elseif ($ext === 'docx') {
                if (!class_exists('ZipArchive')) {
                    log_message('error', 'preview_from_file: ZipArchive not available on server');
                    $this->_json(array('status'=>false,'message'=>'PHP ZipArchive no disponible. Habilite la extensión zip para procesar archivos .docx'));
                }
                $zip = new ZipArchive();
                if ($zip->open($fullpath) === true) {
                    $idx = $zip->locateName('word/document.xml', ZIPARCHIVE::FL_NOCASE);
                    if ($idx !== false) {
                        $xml = $zip->getFromIndex($idx);
                        $xml = preg_replace('/<w:t[^>]*>/', '', $xml);
                        $xml = str_replace(array('</w:t>','</w:p>','<w:p>','<w:tab/>'), array('','\n','\n','\t'), $xml);
                        $content = strip_tags($xml);
                        $content = '<div style="font-family:DejaVu Sans, Arial, Helvetica, sans-serif;">' . nl2br(htmlspecialchars($content)) . '</div>';
                    }
                    $zip->close();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error reading contract file for preview ' . $fullpath . ': ' . $e->getMessage());
        }

        if (!$content) { $this->_json(array('status'=>false,'message'=>'No se pudo leer/convertir el archivo')); }

        // load prestamo and related solicitud/uso
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if (!$prestamo) {
            if (method_exists($this->prestamos_model, 'get_by_id')) {
                $p = $this->prestamos_model->get_by_id($idprestamo);
                if ($p) { $p->idprestamo = isset($p->id) ? $p->id : (isset($p->idprestamo) ? $p->idprestamo : $idprestamo); $prestamo = $p; }
            }
        }
        if (!$prestamo) { $this->_json(array('status'=>false,'message'=>'Préstamo no encontrado')); }

        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
        }
        $uso = null;
        if ($solicitud && isset($solicitud->idsolicitud)) {
            $uso = $this->core_model->get_by_id('tb_solicitud_uso_credito', array('idsolicitud' => $solicitud->idsolicitud));
        }

        // Use centralized replacement builder
        $repl = $this->_build_contract_replacements($prestamo);
        $filled = strtr($content, $repl);

        $this->_json(array('status'=>true,'html'=>$filled));
    }

    // Download a generated contract (by idprestamo)
    public function download($idprestamo = null)
    {
        if (!$idprestamo) show_404();
        if (!$this->db->table_exists('tb_contratos')) show_404();
        $contract = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
        if (!$contract) show_404();
        $this->load->library('pdf');
        $file_name = 'CONTRATO_P_' . $idprestamo;
        // contract content is stored as HTML in 'contenido'
        $html = isset($contract->contenido) ? $contract->contenido : '<p>Contrato</p>';
        // stream as download
        $this->pdf->createPDF($html, $file_name, TRUE, 'letter', 'portrait');
    }

    // Save edited HTML content from editor (AJAX)
    public function save_edited()
    {
        if (!$this->input->is_ajax_request()) show_404();
        ob_start();
        $idprestamo = $this->input->post('idprestamo');
        $html = $this->input->post('html');
        if (!$idprestamo || $html === null) { $this->_json(array('status'=>false,'message'=>'Faltan parámetros')); }

        // Ensure we have prestamo
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if (!$prestamo) {
            if (method_exists($this->prestamos_model, 'get_by_id')) {
                $p = $this->prestamos_model->get_by_id($idprestamo);
                if ($p) { $p->idprestamo = isset($p->id) ? $p->id : (isset($p->idprestamo) ? $p->idprestamo : $idprestamo); $prestamo = $p; }
            }
        }
        if (!$prestamo) { $this->_json(array('status'=>false,'message'=>'Préstamo no encontrado')); }

        // persist into tb_contratos (insert or update)
        try {
            $user = $this->ion_auth->user()->row();
            $record = array('idprestamo' => $idprestamo, 'template_id' => 0, 'contenido' => $html, 'created_by' => isset($user->id) ? $user->id : null, 'created_at' => date('Y-m-d H:i:s'));
            if ($this->db->table_exists('tb_contratos')) {
                $existing = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
                if ($existing && isset($existing->idcontrato)) {
                    // update
                    $ok = $this->core_model->update('tb_contratos', array('contenido' => $html, 'created_at' => date('Y-m-d H:i:s')), array('idcontrato' => $existing->idcontrato));
                    $idcontrato = $existing->idcontrato;
                } else {
                    $idcontrato = $this->core_model->insert('tb_contratos', $record, true);
                    $ok = $idcontrato ? true : false;
                }
                if ($ok) {
                    $pdf_url = base_url('contratos/download/' . $idprestamo);
                    $view_url = base_url('contratos/view/' . $idprestamo);
                    $this->_json(array('status'=>true,'message'=>'Contrato guardado','idcontrato'=>$idcontrato,'pdf_url'=>$pdf_url,'view_url'=>$view_url));
                }
            }
        } catch (Exception $e) {
            log_message('error','save_edited failed: '.$e->getMessage());
        }

        $this->_json(array('status'=>false,'message'=>'No se pudo guardar el contrato (verifique tabla tb_contratos)'));
    }
}
