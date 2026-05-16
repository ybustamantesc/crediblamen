<?php
defined('BASEPATH') or exit('Acción no permitida');

class Planescredito extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Cargar base de datos y modelos necesarios
        $this->load->database();
        $this->load->model('core_model');
        // Allow unauthenticated access only for PDF download requests (download=1)
        $is_download_request = (isset($_GET['download']) && (string)$_GET['download'] === '1') || (php_sapi_name() === 'cli' && isset($GLOBALS['argv']) && in_array('--download', $GLOBALS['argv']));
        if (!$this->ion_auth->logged_in() && !$is_download_request) {
            redirect('login');
        }
    }

    public function index()
    {
        // Auto-import if missing or incomplete (idempotent and safe)
        $this->auto_import_if_needed();

        // Optional date range filter from GET params (YYYY-mm-dd)
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        // --- Paginación ---
        $per_page = 25;
        $page = max(1, intval($this->input->get('page')));
        $offset = ($page - 1) * $per_page;
        $total_rows = 0;
        // Traer nombre del creador (JOIN con users) y calcular cuota actual
        $this->db->select('tb_prestamos.*, users.username as creado_por');
        $this->db->from('tb_prestamos');
        $this->db->join('users', 'users.id = tb_prestamos.idusuario', 'left');
        if ($start_date) {
            $this->db->where('fecha_credito >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('fecha_credito <=', $end_date);
        }
        $total_rows = $this->db->count_all_results('', false);
        $this->db->limit($per_page, $offset);
        $prestamos = $this->db->get()->result();

        // Optional text filter (search by solycitud number or0cliente nombre)
        $q = trim((string)$this->input->get('q'));

        // Enrich each prestamo with normalized fields used by the view
        if (is_array($prestamos)) {
            foreach ($prestamos as &$p) {
                // Normalize monto
                if (isset($p->monto_credito) && $p->monto_credito !== '') {
                    $p->monto = $p->monto_credito;
                } elseif (!isset($p->monto)) {
                    $p->monto = 0;
                }
                // Normalize desembolsado
                // Calcular cuota actual (cuotas pagadas + 1)
                $pagadas = 0;
                $this->db->select('COUNT(*) as pagadas');
                $this->db->from('tb_prestamo_pagos');
                $this->db->where('idprestamo', $p->idprestamo);
                $pagos = $this->db->get()->row();
                if ($pagos && isset($pagos->pagadas)) {
                    $pagadas = intval($pagos->pagadas);
                }
                $p->cuota_actual = $pagadas + 1;
                if (isset($p->cuotas) && $p->cuota_actual > $p->cuotas) {
                    $p->cuota_actual = $p->cuotas; // No pasar del total
                }
                if (isset($p->monto_desembolsado) && $p->monto_desembolsado !== '') {
                    $p->desembolsado = $p->monto_desembolsado;
                } elseif (!isset($p->desembolsado)) {
                    $p->desembolsado = 0;
                }
                // Normalize tasa / interes
                if (!isset($p->tasa) && isset($p->interes_credito)) {
                    $p->tasa = $p->interes_credito;
                }
                // Normalize cuotas
                if (!isset($p->cuotas) && isset($p->numero_coutas)) {
                    $p->cuotas = $p->numero_coutas;
                }
                // Normalize fecha: usar fecha_desembolso_real y primer_dia_pago si existen
                if (isset($p->fecha_desembolso_real) && $p->fecha_desembolso_real) {
                    $p->fecha = date('Y-m-d', strtotime($p->fecha_desembolso_real));
                } elseif (isset($p->fecha_desembolso) && $p->fecha_desembolso) {
                    $p->fecha = $p->fecha_desembolso;
                } elseif (isset($p->fecha_credito)) {
                    $p->fecha = $p->fecha_credito;
                }
                // Agregar campo para la fecha del primer pago si existe
                if (isset($p->primer_dia_pago) && $p->primer_dia_pago) {
                    $p->fecha_primer_pago = $p->primer_dia_pago;
                } else {
                    $p->fecha_primer_pago = $p->fecha;
                }

                // Ensure cliente_nombre exists by checking solicitud then cliente
                $p->cliente_nombre = '';
                if (isset($p->idsolicitud) && $p->idsolicitud) {
                    $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $p->idsolicitud));
                    if (!$sol) {
                        $sol = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $p->idsolicitud));
                    }
                    if ($sol) {
                        $p->cliente_nombre = trim((isset($sol->apellidos) ? $sol->apellidos : '') . ' ' . (isset($sol->nombres) ? $sol->nombres : ''));
                    }
                }
                if (empty($p->cliente_nombre) && isset($p->idcliente) && $p->idcliente) {
                    $cli = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $p->idcliente));
                    if ($cli) {
                        $p->cliente_nombre = trim((isset($cli->apellidos) ? $cli->apellidos : '') . ' ' . (isset($cli->nombres) ? $cli->nombres : ''));
                    }
                }
                // As a last resort, try other fields that might contain the name
                if (empty($p->cliente_nombre)) {
                    if (isset($p->cliente) && $p->cliente) $p->cliente_nombre = $p->cliente;
                    elseif (isset($p->nombre_cliente) && $p->nombre_cliente) $p->cliente_nombre = $p->nombre_cliente;
                }

                $estado_info = $this->_calcular_estado_credito($p->idprestamo);
                $p->estado_credito = $estado_info['estado_credito'];
            }
            unset($p);
        }

        // If q provided, filter prestamos in PHP by solicitud number or cliente name (case-insensitive)
        if ($q !== '') {
            $filtered = array();
            $q_low = mb_strtolower($q);
            foreach ($prestamos as $p) {
                $match = false;
                // check solicitud/id match (exact or contains)
                if (isset($p->idsolicitud) && $p->idsolicitud !== '') {
                    if (strpos((string)$p->idsolicitud, $q) !== false) $match = true;
                }
                // check idprestamo
                if (!$match && isset($p->idprestamo) && $p->idprestamo !== '') {
                    if (strpos((string)$p->idprestamo, $q) !== false) $match = true;
                }
                // check cliente_nombre
                if (!$match) {
                    $name = isset($p->cliente_nombre) ? mb_strtolower($p->cliente_nombre) : '';
                    if ($name !== '' && mb_strpos($name, $q_low) !== false) $match = true;
                }
                if ($match) $filtered[] = $p;
            }
            $prestamos = $filtered;
        }

        $data = array(
            'titulo' => 'Planes de Pago',
            'subtitulo' => 'Listado de créditos generados y planes de pago',
            'icono' => 'fas fa-list-alt',
            'prestamos' => $prestamos,
            'filter_start_date' => $start_date,
            'filter_end_date' => $end_date,
            'total_rows' => $total_rows,
            'per_page' => $per_page,
            'current_page' => $page
        );
        $data['q'] = $q;
        $this->load->view('layout/header', $data);
        $this->load->view('planescredito/index', $data);
        $this->load->view('layout/footer');
    }

    private function auto_import_if_needed()
    {
        $sqlFile = FCPATH . 'sql/import_carga_credito.sql';
        if (!is_file($sqlFile)) {
            return;
        }

        $csvFile = $this->extract_csv_from_import($sqlFile);
        if (!$csvFile) {
            return;
        }

        $importNeeded = false;

        // Check if import_log exists
        $logTable = $this->db->query("SHOW TABLES LIKE 'import_log'");
        if (!$logTable || $logTable->num_rows() === 0) {
            $importNeeded = true;
        } else {
            $row = $this->db->query("SELECT stg_prestamos, imported_prestamos FROM import_log WHERE csv_file = ? ORDER BY id DESC LIMIT 1", array($csvFile))->row();
            if (!$row) {
                $importNeeded = true;
            } else {
                $stg = (int)$row->stg_prestamos;
                $imp = (int)$row->imported_prestamos;
                if ($stg <= 0 || $imp < $stg) {
                    $importNeeded = true;
                }
            }
        }

        // If staging exists, verify current counts against staging ids
        $stgTable = $this->db->query("SHOW TABLES LIKE 'stg_carga_credito'");
        if ($stgTable && $stgTable->num_rows() > 0) {
            $stgCount = (int)$this->db->query("SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) AS c FROM stg_carga_credito WHERE TRIM(num_prestamo_raw) REGEXP '^[0-9]+$'")->row()->c;
            if ($stgCount > 0) {
                $impCount = (int)$this->db->query("SELECT COUNT(*) AS c FROM tb_prestamos WHERE idprestamo IN (SELECT DISTINCT CAST(NULLIF(TRIM(num_prestamo_raw), '') AS UNSIGNED) FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL AND TRIM(num_prestamo_raw) REGEXP '^[0-9]+$')")->row()->c;
                if ($impCount < $stgCount) {
                    $importNeeded = true;
                }
            }
        }

        if ($importNeeded) {
            $lockFile = APPPATH . 'cache/import_lock';
            $lockTtlSeconds = 120; // 2 minutes

            if (is_file($lockFile)) {
                $lockAge = time() - (int)@filemtime($lockFile);
                if ($lockAge >= 0 && $lockAge < $lockTtlSeconds) {
                    return; // avoid concurrent/looped imports
                }
                @unlink($lockFile);
            }

            @file_put_contents($lockFile, date('c'));

            // Run import in background to avoid blocking the page
            $importScript = FCPATH . 'temp/import_run.php';
            if (is_file($importScript)) {
                if (stripos(PHP_OS, 'WIN') === 0) {
                    $cmd = 'start /B "" ' . escapeshellarg(PHP_BINARY) . ' -f ' . escapeshellarg($importScript) . ' > NUL 2>&1';
                    @popen($cmd, 'r');
                } else {
                    $cmd = escapeshellarg(PHP_BINARY) . ' -f ' . escapeshellarg($importScript) . ' > /dev/null 2>&1 &';
                    @exec($cmd);
                }
            } else {
                $this->run_import_sql($sqlFile);
                @unlink($lockFile);
            }
        }
    }

    private function extract_csv_from_import($sqlFile)
    {
        $sql = @file_get_contents($sqlFile);
        if ($sql === false || $sql === '') {
            return null;
        }

        if (preg_match("/SET\s+@csv_file\s*=\s*'([^']+)'/i", $sql, $m)) {
            return basename($m[1]);
        }

        if (preg_match("/LOAD\s+DATA\s+LOCAL\s+INFILE\s+'([^']+)'/i", $sql, $m)) {
            return basename($m[1]);
        }

        return null;
    }

    private function run_import_sql($sqlFile)
    {
        set_time_limit(0);
        @ini_set('mysqli.allow_local_infile', '1');
        $host = isset($this->db->hostname) ? $this->db->hostname : 'localhost';
        $user = isset($this->db->username) ? $this->db->username : 'root';
        $pass = isset($this->db->password) ? $this->db->password : '';
        $db   = isset($this->db->database) ? $this->db->database : '';

        $mysqli = mysqli_init();
        if ($mysqli === false) {
            log_message('error', 'Auto-import DB init failed');
            return;
        }

        $mysqli->options(MYSQLI_OPT_LOCAL_INFILE, true);
        if (!@$mysqli->real_connect($host, $user, $pass, $db)) {
            log_message('error', 'Auto-import DB connect error: ' . $mysqli->connect_error);
            return;
        }

        $sql = @file_get_contents($sqlFile);
        if ($sql === false || $sql === '') {
            log_message('error', 'Auto-import SQL file empty: ' . $sqlFile);
            $mysqli->close();
            return;
        }

        if (!$mysqli->multi_query($sql)) {
            log_message('error', 'Auto-import SQL error: ' . $mysqli->error);
        } else {
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
                if ($mysqli->errno) {
                    log_message('error', 'Auto-import SQL error: ' . $mysqli->error);
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        }

        $mysqli->close();
    }

    // AJAX: return prestamo + cuotas
    public function get($id = null)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$id) { echo json_encode(array('status' => false, 'message' => 'Falta id')); return; }

        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $id));
        if (!$prestamo) { echo json_encode(array('status' => false, 'message' => 'No encontrado')); return; }

        // Enrich prestamo with cobrador and cliente-like fields (so front-end/modal shows route name)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        // Determine cobrador: prefer solicitud.promotor/cobrador, then prestamo fields
        $prestamo->cobrador = '';
        if ($solicitud) {
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            // resolve numeric promotor id to name
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : '');
                }
            }
        }
        // fallback to prestamo fields
        if (empty($prestamo->cobrador)) {
            if (isset($prestamo->promotor) && $prestamo->promotor) $prestamo->cobrador = $prestamo->promotor;
            elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) $prestamo->cobrador = $prestamo->nombre_promotor;
            elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) $prestamo->cobrador = $prestamo->nombre_asesor;
        }
        // If cobrador is numeric ID, try to resolve in tb_asesores
        if (!empty($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
            $aid = intval($prestamo->cobrador);
            if ($aid > 0) {
                $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                if ($ases2) $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
            }
        }

        $cuotas = $this->core_model->get_by_id_all('tb_prestamo_cuotas', array('idprestamo' => $id));
        if (!is_array($cuotas)) $cuotas = array();

        echo json_encode(array('status' => true, 'prestamo' => $prestamo, 'cuotas' => $cuotas));
    }

    // AJAX: return unique clients that have a plan/credito (idcliente when available)
    public function clients()
    {
        if (!$this->input->is_ajax_request()) show_404();

        // Build a query joining prestamos, solicitudes and creditos to find clients
        $this->db->select('tb_clientes.idcliente, tb_clientes.apellidos, tb_clientes.nombres, tb_solicitudes.numero_doc');
        $this->db->from('tb_clientes');
        $this->db->join('tb_creditos', 'tb_creditos.idcliente = tb_clientes.idcliente', 'left');
        $this->db->join('tb_solicitudes', 'tb_solicitudes.numero_doc = tb_clientes.numero_doc', 'left');
        $this->db->join('tb_prestamos', 'tb_prestamos.idsolicitud = tb_solicitudes.idsolicitud', 'left');
        $this->db->where('(tb_creditos.id IS NOT NULL OR tb_prestamos.idprestamo IS NOT NULL)');
        $this->db->group_by('tb_clientes.idcliente');
        $rows = $this->db->get()->result();

        $clients = array();
        if ($rows) {
            foreach ($rows as $r) {
                $name = '';
                if (!empty($r->idcliente)) {
                    $name = $r->idcliente . ' - ' . trim((isset($r->apellidos) ? $r->apellidos : '') . ' ' . (isset($r->nombres) ? $r->nombres : ''));
                    $clients[] = array('id' => $r->idcliente, 'nombre' => $name, 'numero_doc' => isset($r->numero_doc) ? $r->numero_doc : '');
                }
            }
        }

        // Additionally, some prestamos might only be linked by solicitud.numero_doc without a client record
        // Fetch those documentos and include them as entries (id null, use numero_doc as key)
        $this->db->select('tb_solicitudes.numero_doc');
        $this->db->from('tb_prestamos');
        $this->db->join('tb_solicitudes', 'tb_solicitudes.idsolicitud = tb_prestamos.idsolicitud', 'left');
        $this->db->where('tb_prestamos.estado !=', 0);
        $this->db->group_by('tb_solicitudes.numero_doc');
        $docs = $this->db->get()->result();
        if ($docs) {
            foreach ($docs as $d) {
                if (empty($d->numero_doc)) continue;
                // skip if already present in $clients (matched by numero_doc)
                $found = false;
                foreach ($clients as $c) {
                    if (!empty($c['numero_doc']) && $c['numero_doc'] === $d->numero_doc) { $found = true; break; }
                }
                if (!$found) {
                    $clients[] = array('id' => null, 'nombre' => $d->numero_doc, 'numero_doc' => $d->numero_doc);
                }
            }
        }

        echo json_encode(array('status' => true, 'clients' => $clients));
    }

    // Generate PDF for a prestamo (uses the pdf library)
    public function pdf($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }

        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $id));
        if (!$prestamo) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }

        // Enrich prestamo with related info (solicitud / cliente / producto / cobrador)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : $prestamo->cobrador);
                }
            }
        } else {
            if (isset($prestamo->idcliente) && $prestamo->idcliente) {
                $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
                if ($cliente) {
                    $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                    $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
                }
            }
            if (empty($prestamo->cobrador)) {
                if (isset($prestamo->promotor) && $prestamo->promotor) $prestamo->cobrador = $prestamo->promotor;
                elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) $prestamo->cobrador = $prestamo->nombre_promotor;
                elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) $prestamo->cobrador = $prestamo->nombre_asesor;
            }
            if (!empty($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
                $aid = intval($prestamo->cobrador);
                if ($aid > 0) {
                    $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases2) $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
                }
            }
        }

        // Enrich prestamo with related info (solicitud / cliente / producto / cobrador)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : $prestamo->cobrador);
                }
            }
        } else {
            if (isset($prestamo->idcliente) && $prestamo->idcliente) {
                $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
                if ($cliente) {
                    $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                    $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
                }
            }
            if (empty($prestamo->cobrador)) {
                if (isset($prestamo->promotor) && $prestamo->promotor) $prestamo->cobrador = $prestamo->promotor;
                elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) $prestamo->cobrador = $prestamo->nombre_promotor;
                elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) $prestamo->cobrador = $prestamo->nombre_asesor;
            }
            if (!empty($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
                $aid = intval($prestamo->cobrador);
                if ($aid > 0) {
                    $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases2) $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
                }
            }
        }

        // Additional fallbacks: sometimes prestamo stores client/name fields directly
        if (empty($prestamo->cliente_nombre)) {
            if (!empty($prestamo->cliente)) {
                $prestamo->cliente_nombre = $prestamo->cliente;
            } elseif (!empty($prestamo->nombre_cliente)) {
                $prestamo->cliente_nombre = $prestamo->nombre_cliente;
            } elseif (!empty($prestamo->apellidos) || !empty($prestamo->nombres)) {
                $prestamo->cliente_nombre = trim((isset($prestamo->apellidos)?$prestamo->apellidos:'') . ' ' . (isset($prestamo->nombres)?$prestamo->nombres:''));
            }
        }
        if (empty($prestamo->doc_identidad)) {
            if (!empty($prestamo->numero_doc)) $prestamo->doc_identidad = $prestamo->numero_doc;
            elseif (!empty($prestamo->doc_identidad)) $prestamo->doc_identidad = $prestamo->doc_identidad;
        }
        if (empty($prestamo->producto_nombre)) {
            if (!empty($prestamo->producto)) $prestamo->producto_nombre = $prestamo->producto;
            elseif (!empty($prestamo->producto_nombre)) $prestamo->producto_nombre = $prestamo->producto_nombre;
        }
        // Enrich prestamo with related info (cliente/solicitud/producto/cobrador)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : $prestamo->cobrador);
                }
            }
        } else {
            if (isset($prestamo->idcliente) && $prestamo->idcliente) {
                $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
                if ($cliente) {
                    $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                    $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
                }
            }
            if (empty($prestamo->cobrador)) {
                if (isset($prestamo->promotor) && $prestamo->promotor) $prestamo->cobrador = $prestamo->promotor;
                elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) $prestamo->cobrador = $prestamo->nombre_promotor;
                elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) $prestamo->cobrador = $prestamo->nombre_asesor;
            }
            if (!empty($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
                $aid = intval($prestamo->cobrador);
                if ($aid > 0) {
                    $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases2) $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
                }
            }
        }

        // Enrich prestamo object with related info (solicitud / cliente / cobrador / producto)
        // Prefer data from solicitud if available, fall back to cliente table
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                // fallback singular table name
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            // common doc fields
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            // product/cobrador fields might have different names across schemas
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            // Try to fetch product name from propuestas -> tipos_productos when producto_nombre not present
            if (empty($prestamo->producto_nombre)) {
                $prop = $this->core_model->get_by_id('tb_solicitud_propuestas', array('idsolicitud' => $solicitud->idsolicitud));
                if ($prop && isset($prop->idtipo_producto) && $prop->idtipo_producto) {
                    $tipo = $this->core_model->get_by_id('tb_tipo_productos', array('id' => $prop->idtipo_producto));
                    if ($tipo && isset($tipo->nombre)) {
                        $prestamo->producto_nombre = $tipo->nombre;
                    }
                }
            }
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            // If the solicitud provided an ID instead of a name, try to resolve it to a human-friendly name
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) {
                        $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : '');
                    }
                }
            }
        } else {
            // fallback to cliente table if prestamo stores idcliente
            if (isset($prestamo->idcliente) && $prestamo->idcliente) {
                $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
                if ($cliente) {
                    $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                    $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
                }
            }
            // try to populate cobrador from prestamo itself if present (support multiple possible field names)
            if (empty($prestamo->cobrador)) {
                if (isset($prestamo->promotor) && $prestamo->promotor) {
                    $prestamo->cobrador = $prestamo->promotor;
                } elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) {
                    $prestamo->cobrador = $prestamo->nombre_promotor;
                } elseif (isset($prestamo->cobrador) && $prestamo->cobrador) {
                    // already set
                } elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) {
                    $prestamo->cobrador = $prestamo->nombre_asesor;
                }

                // If prestamo contains an ID (numeric promotor), try to resolve to name in tb_asesores
                if (isset($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
                    $aid = intval($prestamo->cobrador);
                    if ($aid > 0) {
                        $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                        if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                        if ($ases2) {
                            $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
                        }
                    }
                }
            }
        }

        $this->load->library('pdf');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $cuotas = $this->core_model->get_by_id_all('tb_prestamo_cuotas', array('idprestamo' => $id));
        if (!is_array($cuotas)) $cuotas = array();

        $prestamo->es_anulado = false;
        if (isset($prestamo->estado) && intval($prestamo->estado) === 2) {
            $prestamo->es_anulado = true;
        }

        // Compute count and sum of cuotas for display in the PDF
        $count_cuotas = count($cuotas);
        $sum_cuotas = 0.0;
        foreach ($cuotas as $cq) {
            $sum_cuotas += isset($cq->cuota) ? floatval($cq->cuota) : 0;
        }

        $file_name = 'PLAN_DE_PAGO_N_' . $id;
        $data = array(
            'file_name' => $file_name,
            'empresa' => $empresa,
            'prestamo' => $prestamo,
            'cuotas' => $cuotas,
            'count_cuotas' => $count_cuotas,
            'sum_cuotas' => $sum_cuotas,
            'titulo' => 'Plan de Pago N° ' . $id
        );

        // Compute TCA/TCM server-side using explicit Excel-like rule requested by the user:
        // amounts: first element = - Saldo Inicial (monto), then each cuota (campo 'cuota')
        // dates: first element = fecha de desembolso (fecha_credito), then each cuota date
        try {
            $amounts = array(); $dates = array();

            // Determine the saldo inicial / monto inicial (preference order)
            $principal = null;
            $principal_fields = array('monto', 'monto_credito', 'monto_desembolsado', 'monto_total', 'monto_prestamo');
            foreach ($principal_fields as $f) {
                if (isset($prestamo->{$f}) && $prestamo->{$f} !== '') { $principal = floatval($prestamo->{$f}); break; }
            }
            // If still null, use 'saldo_inicial' field if present
            if (($principal === null || $principal == 0) && isset($prestamo->saldo_inicial)) {
                $principal = floatval($prestamo->saldo_inicial);
            }

            // Use fecha_credito as desembolso; if missing, try first cuota date
            $disb_date = isset($prestamo->fecha_credito) && $prestamo->fecha_credito ? $prestamo->fecha_credito : null;
            // Collect cuota rows and ensure they're ordered by date ascending
            $cuotas_rows = is_array($cuotas) ? $cuotas : array();
            usort($cuotas_rows, function($a, $b){
                $da = (string)$this->_find_first_field_value($a, array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
                $db = (string)$this->_find_first_field_value($b, array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
                $ta = strtotime($da) ?: 0; $tb = strtotime($db) ?: 0;
                return $ta - $tb;
            });

            if (!$disb_date && count($cuotas_rows) > 0) {
                $disb_date = $this->_find_first_field_value($cuotas_rows[0], array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
            }
            if (!$disb_date) $disb_date = date('Y-m-d');

            // Build arrays exactly: first negative principal, then each cuota (campo 'cuota')
            $amounts[] = -1.0 * floatval($principal !== null ? $principal : 0);
            $dates[] = $disb_date;
            foreach ($cuotas_rows as $c) {
                $amt = $this->_find_first_field_value($c, array('cuota','monto','importe','valor','amount','payment'));
                $d = $this->_find_first_field_value($c, array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
                $amounts[] = floatval($amt !== null ? $amt : 0);
                $dates[] = $d !== null ? $d : $disb_date;
            }

            $x = null;
            if (count($amounts) > 1) {
                // use initial guess 0 as requested
                $x = $this->_xirr_calc($amounts, $dates, 0);
            }
            if (is_numeric($x) && is_finite($x) && $x > -0.999999) {
                $data['tca'] = $x;
                $data['tcm'] = pow(1 + $x, 1/12.0) - 1;
            } else {
                $data['tca'] = null;
                $data['tcm'] = null;
            }
            $data['xirr_flows'] = array('amounts' => $amounts, 'dates' => $dates);
        } catch (Exception $e) {
            $data['tca'] = null; $data['tcm'] = null; $data['xirr_flows'] = array();
        }

        $html = $this->load->view('planescredito/pdf', $data, TRUE);
        // allow ?download=1 to force file download
        // Allow forcing download when called from CLI (useful for debugging)
        $download = ($this->input->get('download') && intval($this->input->get('download')) === 1) ? true : false;
        if (php_sapi_name() === 'cli') {
            $cli_args = isset($GLOBALS['argv']) ? $GLOBALS['argv'] : array();
            foreach ($cli_args as $arg) {
                if (is_string($arg) && (strpos($arg, 'download=1') !== false || $arg === '--download')) {
                    $download = true; break;
                }
            }
        }
        // Increment printed count (create column if missing)
        try {
            if (!$this->db->field_exists('pdf_printed_count', 'tb_prestamos')) {
                $this->db->query('ALTER TABLE `tb_prestamos` ADD COLUMN `pdf_printed_count` INT DEFAULT 0');
            }
            $this->db->set('pdf_printed_count', 'COALESCE(pdf_printed_count,0) + 1', FALSE);
            $this->db->where('idprestamo', $id);
            $this->db->update('tb_prestamos');
        } catch (Exception $e) {
            // ignore DB schema/permission errors; printing should continue regardless
            log_message('error', 'Could not increment pdf_printed_count for prestamo ' . $id . ': ' . $e->getMessage());
        }
        // Use Letter portrait so the PDF prints on a single Carta (Letter) page in vertical
        $this->pdf->createPDF($html, $file_name, $download, 'letter', 'portrait');
    }

    // Estado de Cuenta: muestra el plan con los pagos aplicados (saldo por cuota)
    public function estado_cuenta($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $id));
        if (!$prestamo) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }

        // Enrich prestamo with related info (solicitud / cliente / producto / cobrador)
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
            if (empty($prestamo->cobrador) && isset($solicitud->promotor) && is_numeric($solicitud->promotor)) {
                $aid = intval($solicitud->promotor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) $prestamo->cobrador = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : $prestamo->cobrador);
                }
            }
        } else {
            if (isset($prestamo->idcliente) && $prestamo->idcliente) {
                $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
                if ($cliente) {
                    $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                    $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
                }
            }
            if (empty($prestamo->cobrador)) {
                if (isset($prestamo->promotor) && $prestamo->promotor) $prestamo->cobrador = $prestamo->promotor;
                elseif (isset($prestamo->nombre_promotor) && $prestamo->nombre_promotor) $prestamo->cobrador = $prestamo->nombre_promotor;
                elseif (isset($prestamo->nombre_asesor) && $prestamo->nombre_asesor) $prestamo->cobrador = $prestamo->nombre_asesor;
            }
            if (!empty($prestamo->cobrador) && is_numeric($prestamo->cobrador)) {
                $aid = intval($prestamo->cobrador);
                if ($aid > 0) {
                    $ases2 = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases2) $ases2 = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases2) $prestamo->cobrador = isset($ases2->nombres) ? $ases2->nombres : (isset($ases2->nombre) ? $ases2->nombre : $prestamo->cobrador);
                }
            }
        }

        // Additional fallbacks: sometimes prestamo stores client/name fields directly
        if (empty($prestamo->cliente_nombre)) {
            if (!empty($prestamo->cliente)) {
                $prestamo->cliente_nombre = $prestamo->cliente;
            } elseif (!empty($prestamo->nombre_cliente)) {
                $prestamo->cliente_nombre = $prestamo->nombre_cliente;
            } elseif (!empty($prestamo->apellidos) || !empty($prestamo->nombres)) {
                $prestamo->cliente_nombre = trim((isset($prestamo->apellidos)?$prestamo->apellidos:'') . ' ' . (isset($prestamo->nombres)?$prestamo->nombres:''));
            }
        }
        if (empty($prestamo->doc_identidad)) {
            if (!empty($prestamo->numero_doc)) $prestamo->doc_identidad = $prestamo->numero_doc;
            elseif (!empty($prestamo->doc_identidad)) $prestamo->doc_identidad = $prestamo->doc_identidad;
        }
        if (empty($prestamo->producto_nombre)) {
            if (!empty($prestamo->producto)) $prestamo->producto_nombre = $prestamo->producto;
            elseif (!empty($prestamo->producto_nombre)) $prestamo->producto_nombre = $prestamo->producto_nombre;
        }

        $estado_info = $this->_calcular_estado_credito($id);
        $prestamo->estado_credito = $estado_info['estado_credito'];
        $prestamo->dias_mora_credito = $estado_info['dias_mora'];

        // fetch cuotas
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $id);
        $this->db->order_by('numero', 'ASC');
        $cuotas = $this->db->get()->result();
        if (!is_array($cuotas)) $cuotas = array();

        // Tasa moratoria del plan de pagos: prioriza campo en cuotas si existe, fallback 18%
        $tasa_moratoria_plan = 18.00;
        if (!empty($cuotas)) {
            $primera_cuota = $cuotas[0];
            $campos_tasa_mora = array('tasa_moratoria', 'interes_moratorio', 'mora_pct', 'tasa_mora');
            foreach ($campos_tasa_mora as $campo_tasa_mora) {
                if (isset($primera_cuota->{$campo_tasa_mora}) && $primera_cuota->{$campo_tasa_mora} !== '') {
                    $valor = floatval($primera_cuota->{$campo_tasa_mora});
                    $tasa_moratoria_plan = ($valor > 1) ? $valor : ($valor * 100);
                    break;
                }
            }
        }
        $factor_mora_diaria = ($tasa_moratoria_plan / 100) / 360;

        $total_pending = 0.0;
        $tasa_moratoria_plan = 18.00;
        if (!empty($cuotas)) {
            $primera_cuota = $cuotas[0];
            $campos_tasa_mora = array('tasa_moratoria', 'interes_moratorio', 'mora_pct', 'tasa_mora');
            foreach ($campos_tasa_mora as $campo_tasa_mora) {
                if (isset($primera_cuota->{$campo_tasa_mora}) && $primera_cuota->{$campo_tasa_mora} !== '') {
                    $valor = floatval($primera_cuota->{$campo_tasa_mora});
                    $tasa_moratoria_plan = ($valor > 1) ? $valor : ($valor * 100);
                    break;
                }
            }
        }
        $factor_mora_diaria = ($tasa_moratoria_plan / 100) / 360;

        $rows = array();
        $today = new DateTime('now');
        
        foreach ($cuotas as $c) {
            $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
            $cuota_val = isset($c->cuota) ? floatval($c->cuota) : 0;
            $principal = isset($c->principal) ? floatval($c->principal) : 0;
            
            // fetch payments rows for this cuota with series and user info
            $payments = array();
            $paid = 0.0;
            if (!empty($c_id)) {
                $this->db->select(['p.monto_pagado', 'p.fecha_pago', 'p.referencia', 'p.idserie', 'p.metodo_pago', 'u.first_name', 'u.last_name', 'sr.codigo as serie_codigo']);
                $this->db->from('tb_prestamo_pagos p');
                $this->db->join('users u', 'u.id = p.idusuario', 'left');
                $this->db->join('tb_series_recibos sr', 'sr.idserie = p.idserie', 'left');
                $this->db->where('p.idprestamo', $id);
                $this->db->where('p.idcuota', $c_id);
                $this->db->order_by('p.fecha_pago', 'ASC');
                $payRows = $this->db->get()->result();
                if (is_array($payRows) && count($payRows)) {
                    foreach ($payRows as $pr) {
                        $amt = isset($pr->monto_pagado) ? floatval($pr->monto_pagado) : 0.0;
                        $paid += $amt;
                        $serie_codigo = isset($pr->serie_codigo) ? trim((string)$pr->serie_codigo) : '';
                        $referencia_pago = isset($pr->referencia) ? trim((string)$pr->referencia) : '';
                        if ($serie_codigo === '' && $referencia_pago !== '') {
                            if (preg_match('/^([A-Za-z]+)/', $referencia_pago, $mSerieRef)) {
                                $serie_codigo = strtoupper($mSerieRef[1]);
                            } else {
                                $serie_codigo = $referencia_pago;
                            }
                        }
                        $payments[] = array(
                            'monto' => $amt,
                            'fecha_pago' => isset($pr->fecha_pago) ? $pr->fecha_pago : '',
                            'referencia' => $referencia_pago,
                            'serie_codigo' => $serie_codigo,
                            'metodo_pago' => isset($pr->metodo_pago) ? $pr->metodo_pago : '',
                            'emitido_por' => trim((isset($pr->first_name) ? $pr->first_name : '') . ' ' . (isset($pr->last_name) ? $pr->last_name : ''))
                        );
                    }
                }
            }
            $remaining = $cuota_val - $paid;
            if ($remaining < 0) $remaining = 0.0;
            $total_pending += $remaining;
            
            // Calcular días de mora - SOLO usar valor manual, no auto-calcular
            $dias_mora = 0;
            
            // Si hay una edición manual, usarla; sino, dejar en 0 (no auto-calcular)
            if (!empty($c->dias_mora_manual)) {
                $dias_mora = intval($c->dias_mora_manual);
            }
            // Ya NO auto-calculamos basado en fechas - solo valores manuales
            
            // Calcular monto de mora con tasa moratoria del plan de pagos
            $monto_mora = 0.0;
            if (!empty($c->monto_mora)) {
                // Si hay un monto editado manualmente, usarlo
                $monto_mora = floatval($c->monto_mora);
            } elseif ($dias_mora > 0 && $principal > 0) {
                $monto_mora = round($principal * $factor_mora_diaria * $dias_mora, 2);
            }
            
            $estado_pago = $this->_clasificar_estado_pago($dias_mora, $remaining <= 0);

            $fecha_venc_base = isset($c->fecha_vencimiento) && !empty($c->fecha_vencimiento)
                ? substr((string)$c->fecha_vencimiento, 0, 10)
                : (isset($c->fecha) && !empty($c->fecha) ? substr((string)$c->fecha, 0, 10) : '');
            $dias_transcurridos = 0;
            if ($fecha_venc_base !== '') {
                try {
                    $fecha_venc_dt = new DateTime($fecha_venc_base);
                    if ($paid > 0 && !empty($payments)) {
                        $fecha_pago_cuota = isset($payments[0]['fecha_pago']) ? substr((string)$payments[0]['fecha_pago'], 0, 10) : '';
                        if ($fecha_pago_cuota !== '') {
                            $fecha_pago_dt = new DateTime($fecha_pago_cuota);
                            $dias_transcurridos = ($fecha_pago_dt >= $fecha_venc_dt)
                                ? intval($fecha_venc_dt->diff($fecha_pago_dt)->days)
                                : 0;
                        }
                    } else {
                        $dias_transcurridos = ($today >= $fecha_venc_dt)
                            ? intval($fecha_venc_dt->diff($today)->days)
                            : 0;
                    }
                } catch (Exception $e) {
                    $dias_transcurridos = 0;
                }
            }

            $modulo = '0.00';
            if ($paid > 0) {
                $modulo = 'Caja';
                foreach ($payments as $pago_metodo) {
                    $metodo_pago = strtolower(trim((string)(isset($pago_metodo['metodo_pago']) ? $pago_metodo['metodo_pago'] : '')));
                    if ($metodo_pago !== '' && strpos($metodo_pago, 'credit') !== false) {
                        $modulo = 'Credito';
                        break;
                    }
                }
            }

            $rows[] = array(
                'numero' => isset($c->numero) ? $c->numero : null,
                'fecha' => isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : (isset($c->fecha) ? $c->fecha : ''),
                'cuota' => $cuota_val,
                'principal' => $principal,
                'interes' => isset($c->interes) ? floatval($c->interes) : 0,
                'comision' => isset($c->comision) ? floatval($c->comision) : 0,
                'pagado' => $paid,
                'saldo' => $remaining,
                'dias_mora' => $dias_mora,
                'monto_mora' => $monto_mora,
                'estado_pago' => $estado_pago,
                'idcuota' => $c_id,
                'payments' => $payments,
                'dias_transcurridos' => $dias_transcurridos,
                'asiento_contable' => '-',
                'modulo' => $modulo,
                'tipo_cambio' => 36.6243,
                'seguro' => '0.00',
                'dispensa' => '0.00'
            );
        }


        // Buscar nombre del usuario creador
        $creador_nombre = '';
        if (isset($prestamo->idusuario)) {
            $user = $this->db->select('username')->from('users')->where('id', $prestamo->idusuario)->get()->row();
            if ($user && isset($user->username)) {
                $creador_nombre = $user->username;
            }
        }

        $normalizar_porcentaje = function($valor) {
            if ($valor === null || $valor === '') {
                return null;
            }
            $numero = floatval($valor);
            if ($numero <= 0) {
                return $numero;
            }
            return ($numero > 1) ? $numero : ($numero * 100);
        };

        $fecha_ultima_cuota = '-';
        if (!empty($rows)) {
            $ultima_cuota = end($rows);
            $fecha_ultima_cuota = !empty($ultima_cuota['fecha']) ? $ultima_cuota['fecha'] : '-';
            reset($rows);
        }

        $cuotas_en_mora = 0;
        foreach ($rows as $row_estado) {
            if (intval(isset($row_estado['dias_mora']) ? $row_estado['dias_mora'] : 0) > 0 && floatval(isset($row_estado['saldo']) ? $row_estado['saldo'] : 0) > 0) {
                $cuotas_en_mora++;
            }
        }

        $sector_economico = '';
        if ($solicitud) {
            if (!empty($solicitud->actividad_economica)) $sector_economico = $solicitud->actividad_economica;
            elseif (!empty($solicitud->giro_negocio)) $sector_economico = $solicitud->giro_negocio;
            elseif (!empty($solicitud->rubro_credito)) $sector_economico = $solicitud->rubro_credito;
            elseif (!empty($solicitud->id_sector_economico)) $sector_economico = $solicitud->id_sector_economico;
            elseif (!empty($solicitud->id_sector_economico2)) $sector_economico = $solicitud->id_sector_economico2;
        }
        if ($sector_economico === '') {
            if (!empty($prestamo->id_sector_economico)) $sector_economico = $prestamo->id_sector_economico;
            elseif (!empty($prestamo->id_sector_economico2)) $sector_economico = $prestamo->id_sector_economico2;
        }

        $tipo_producto = '';
        if ($solicitud) {
            if (!empty($solicitud->tipo_credito)) $tipo_producto = $solicitud->tipo_credito;
            elseif (!empty($solicitud->producto_nombre)) $tipo_producto = $solicitud->producto_nombre;
            elseif (!empty($solicitud->producto)) $tipo_producto = $solicitud->producto;
        }
        if ($tipo_producto === '' && !empty($prestamo->producto_nombre)) {
            $tipo_producto = $prestamo->producto_nombre;
        }

        $frecuencia = '';
        if (!empty($prestamo->frecuencia_pago)) $frecuencia = $prestamo->frecuencia_pago;
        elseif (!empty($prestamo->periosidad_pagos)) $frecuencia = $prestamo->periosidad_pagos;
        elseif ($solicitud && !empty($solicitud->frecuencia)) $frecuencia = $solicitud->frecuencia;

        $plazo_tiempo = '';
        if (!empty($prestamo->numero_coutas)) $plazo_tiempo = $prestamo->numero_coutas . ' cuotas';
        elseif ($solicitud && !empty($solicitud->plazo_meses)) $plazo_tiempo = $solicitud->plazo_meses . ' meses';

        $interes_moratorio = $tasa_moratoria_plan;

        $comision_desembolso = null;
        if (isset($prestamo->comision_desembolso) && $prestamo->comision_desembolso !== '') {
            $comision_desembolso = $normalizar_porcentaje($prestamo->comision_desembolso);
        } elseif ($solicitud && isset($solicitud->comision_desembolso) && $solicitud->comision_desembolso !== '') {
            $comision_desembolso = $normalizar_porcentaje($solicitud->comision_desembolso);
        }

        $tca = null;
        try {
            $amounts = array();
            $dates = array();
            $principal_tca = null;
            $principal_fields = array('monto', 'monto_credito', 'monto_desembolsado', 'monto_total', 'monto_prestamo');
            foreach ($principal_fields as $campo_principal) {
                if (isset($prestamo->{$campo_principal}) && $prestamo->{$campo_principal} !== '') {
                    $principal_tca = floatval($prestamo->{$campo_principal});
                    break;
                }
            }
            if (($principal_tca === null || $principal_tca == 0) && isset($prestamo->saldo_inicial)) {
                $principal_tca = floatval($prestamo->saldo_inicial);
            }
            $fecha_desembolso_tca = !empty($prestamo->fecha_credito) ? $prestamo->fecha_credito : (!empty($prestamo->fecha_desembolso) ? $prestamo->fecha_desembolso : date('Y-m-d'));
            $amounts[] = -1.0 * floatval($principal_tca !== null ? $principal_tca : 0);
            $dates[] = $fecha_desembolso_tca;
            foreach ($rows as $fila_tca) {
                $amounts[] = floatval(isset($fila_tca['cuota']) ? $fila_tca['cuota'] : 0);
                $dates[] = !empty($fila_tca['fecha']) ? $fila_tca['fecha'] : $fecha_desembolso_tca;
            }
            if (count($amounts) > 1) {
                $x = $this->_xirr_calc($amounts, $dates, 0);
                if (is_numeric($x) && is_finite($x) && $x > -0.999999) {
                    $tca = $x;
                }
            }
        } catch (Exception $e) {
            $tca = null;
        }

        $data['resumen_tecnico'] = array(
            'sector_economico' => $sector_economico !== '' ? $sector_economico : '-',
            'tasa_moratoria' => $interes_moratorio,
            'tca' => $tca,
            'tipo_producto' => $tipo_producto !== '' ? $tipo_producto : '-',
            'tipo_cuota' => 'Nivelada',
            'plazo_tiempo' => $plazo_tiempo !== '' ? $plazo_tiempo : '-',
            'tipo_frecuencia' => $frecuencia !== '' ? $frecuencia : '-',
            'comision' => $comision_desembolso,
            'fecha_vencimiento' => $fecha_ultima_cuota,
            'cuotas_en_mora' => $cuotas_en_mora,
            'metodologia' => 'Individual',
            'moneda' => 'USD',
            'codigo_cliente' => isset($prestamo->idcliente) && $prestamo->idcliente ? $prestamo->idcliente : (($solicitud && isset($solicitud->idcliente)) ? $solicitud->idcliente : '-')
        );

        $data = array(
            'titulo' => 'Estado de Cuenta',
            'prestamo' => $prestamo,
            'rows' => $rows,
            'total_pending' => round($total_pending, 2),
            'creador_nombre' => $creador_nombre,
            'resumen_tecnico' => $data['resumen_tecnico']
        );

        $download = ($this->input->get('download') && intval($this->input->get('download')) === 1) ? true : false;
        if ($download) {
            $this->load->library('pdf');
            // Use a clean PDF-specific view (no navbar/sidebar/footer) for consistent printing
            $html = $this->load->view('planescredito/estado_cuenta_pdf', $data, TRUE);
            // write debug HTML so it's easy to inspect what will be rendered to PDF
            try {
                $tmpDir = FCPATH . 'temp';
                if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);
                @file_put_contents($tmpDir . DIRECTORY_SEPARATOR . 'estado_cuenta_debug.html', $html);
            } catch (Exception $e) { /* ignore write errors */ }
            // Also save a PDF copy to disk for direct inspection (debug)
            try {
                $pdf_debug_path = $tmpDir . DIRECTORY_SEPARATOR . 'estado_cuenta_debug.pdf';
                // use savePDF to write file to disk
                $this->pdf->savePDF($html, $pdf_debug_path, 'A4', 'portrait');
            } catch (Exception $e) { /* ignore save errors */ }
            $file_name = 'ESTADO_CUENTA_N_' . $id;
            // stream inline to browser (do not force download) so target="_blank" opens the PDF
            $this->pdf->createPDF($html, $file_name, false, 'A4', 'portrait');
            return;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('planescredito/estado_cuenta', $data);
        $this->load->view('layout/footer');
    }

    // Simple PDF report for Estado de Cuenta (minimal styling, reliable render)
    public function estado_cuenta_simple_pdf($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $id));
        if (!$prestamo) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }

        // build rows (cuotas + payments)
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $id);
        $this->db->order_by('numero', 'ASC');
        $cuotas = $this->db->get()->result();
        if (!is_array($cuotas)) $cuotas = array();

        // Tasa moratoria y base para cálculo de días transcurridos (evita variables indefinidas)
        $tasa_moratoria_plan = 18.00;
        if (!empty($cuotas)) {
            $primera_cuota = $cuotas[0];
            $campos_tasa_mora = array('tasa_moratoria', 'interes_moratorio', 'mora_pct', 'tasa_mora');
            foreach ($campos_tasa_mora as $campo_tasa_mora) {
                if (isset($primera_cuota->{$campo_tasa_mora}) && $primera_cuota->{$campo_tasa_mora} !== '') {
                    $valor = floatval($primera_cuota->{$campo_tasa_mora});
                    $tasa_moratoria_plan = ($valor > 1) ? $valor : ($valor * 100);
                    break;
                }
            }
        }
        $factor_mora_diaria = ($tasa_moratoria_plan / 100) / 360;

        $rows = array();
        $today = new DateTime('now');
        foreach ($cuotas as $c) {
            $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
            $cuota_val = isset($c->cuota) ? floatval($c->cuota) : 0;
            $principal = isset($c->principal) ? floatval($c->principal) : 0;
            $payments = array();
            $paid = 0.0;
            if (!empty($c_id)) {
                $this->db->select(['p.monto_pagado', 'p.fecha_pago', 'p.referencia', 'p.idserie', 'p.metodo_pago', 'u.first_name', 'u.last_name', 'sr.codigo as serie_codigo']);
                $this->db->from('tb_prestamo_pagos p');
                $this->db->join('users u', 'u.id = p.idusuario', 'left');
                $this->db->join('tb_series_recibos sr', 'sr.idserie = p.idserie', 'left');
                $this->db->where('p.idprestamo', $id);
                $this->db->where('p.idcuota', $c_id);
                $this->db->order_by('p.fecha_pago', 'ASC');
                $payRows = $this->db->get()->result();
                if (is_array($payRows) && count($payRows)) {
                    foreach ($payRows as $pr) {
                        $amt = isset($pr->monto_pagado) ? floatval($pr->monto_pagado) : 0.0;
                        $paid += $amt;
                        $serie_codigo = isset($pr->serie_codigo) ? trim((string)$pr->serie_codigo) : '';
                        $referencia_pago = isset($pr->referencia) ? trim((string)$pr->referencia) : '';
                        if ($serie_codigo === '' && $referencia_pago !== '') {
                            if (preg_match('/^([A-Za-z]+)/', $referencia_pago, $mSerieRef)) {
                                $serie_codigo = strtoupper($mSerieRef[1]);
                            } else {
                                $serie_codigo = $referencia_pago;
                            }
                        }
                        $payments[] = array(
                            'monto' => $amt,
                            'fecha_pago' => isset($pr->fecha_pago) ? $pr->fecha_pago : '',
                            'referencia' => $referencia_pago,
                            'serie_codigo' => $serie_codigo,
                            'metodo_pago' => isset($pr->metodo_pago) ? $pr->metodo_pago : '',
                            'emitido_por' => trim((isset($pr->first_name) ? $pr->first_name : '') . ' ' . (isset($pr->last_name) ? $pr->last_name : ''))
                        );
                    }
                }
            }
            $remaining = $cuota_val - $paid;
            if ($remaining < 0) $remaining = 0.0;

            $dias_mora = 0;
            if (!empty($c->dias_mora_manual)) {
                $dias_mora = intval($c->dias_mora_manual);
            }

            $monto_mora = 0.0;
            if ($c->monto_mora !== null && $c->monto_mora !== '') {
                $monto_mora = floatval($c->monto_mora);
            } elseif ($dias_mora > 0 && $principal > 0) {
                $monto_mora = round($principal * $factor_mora_diaria * $dias_mora, 2);
            }

            $estado_pago = $this->_clasificar_estado_pago($dias_mora, $remaining <= 0);

            $fecha_venc_base = isset($c->fecha_vencimiento) && !empty($c->fecha_vencimiento)
                ? substr((string)$c->fecha_vencimiento, 0, 10)
                : (isset($c->fecha) && !empty($c->fecha) ? substr((string)$c->fecha, 0, 10) : '');
            $dias_transcurridos = 0;
            if ($fecha_venc_base !== '') {
                try {
                    $fecha_venc_dt = new DateTime($fecha_venc_base);
                    if ($paid > 0 && !empty($payments)) {
                        $fecha_pago_cuota = isset($payments[0]['fecha_pago']) ? substr((string)$payments[0]['fecha_pago'], 0, 10) : '';
                        if ($fecha_pago_cuota !== '') {
                            $fecha_pago_dt = new DateTime($fecha_pago_cuota);
                            $dias_transcurridos = ($fecha_pago_dt >= $fecha_venc_dt)
                                ? intval($fecha_venc_dt->diff($fecha_pago_dt)->days)
                                : 0;
                        }
                    } else {
                        $dias_transcurridos = ($today >= $fecha_venc_dt)
                            ? intval($fecha_venc_dt->diff($today)->days)
                            : 0;
                    }
                } catch (Exception $e) {
                    $dias_transcurridos = 0;
                }
            }

            $modulo = '-';
            if ($paid > 0) {
                $modulo = 'Caja';
                foreach ($payments as $pago_metodo) {
                    $metodo_pago = strtolower(trim((string)(isset($pago_metodo['metodo_pago']) ? $pago_metodo['metodo_pago'] : '')));
                    if ($metodo_pago !== '' && strpos($metodo_pago, 'credit') !== false) {
                        $modulo = 'Credito';
                        break;
                    }
                }
            }

            $rows[] = array(
                'numero' => isset($c->numero) ? $c->numero : null,
                'fecha' => isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : (isset($c->fecha) ? $c->fecha : ''),
                'cuota' => $cuota_val,
                'principal' => $principal,
                'interes' => isset($c->interes) ? floatval($c->interes) : 0,
                'comision' => isset($c->comision) ? floatval($c->comision) : 0,
                'pagado' => $paid,
                'saldo' => $remaining,
                'dias_mora' => $dias_mora,
                'monto_mora' => $monto_mora,
                'estado_pago' => $estado_pago,
                'payments' => $payments,
                'dias_transcurridos' => $dias_transcurridos,
                'asiento_contable' => '-',
                'modulo' => $modulo,
                'tipo_cambio' => 36.6243,
                'seguro' => '0.00',
                'dispensa' => '0.00'
            );
        }

        // Enriquecer datos de encabezado para que coincida con la vista de estado de cuenta
        $solicitud = null;
        if (isset($prestamo->idsolicitud) && $prestamo->idsolicitud) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $prestamo->idsolicitud));
            if (!$solicitud) {
                $solicitud = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $prestamo->idsolicitud));
            }
        }

        if ($solicitud) {
            $prestamo->cliente_nombre = trim((isset($solicitud->apellidos) ? $solicitud->apellidos : '') . ' ' . (isset($solicitud->nombres) ? $solicitud->nombres : ''));
            $prestamo->doc_identidad = isset($solicitud->numero_doc) ? $solicitud->numero_doc : (isset($solicitud->doc_identidad) ? $solicitud->doc_identidad : '');
            $prestamo->producto_nombre = isset($solicitud->producto_nombre) ? $solicitud->producto_nombre : (isset($solicitud->producto) ? $solicitud->producto : '');
            $prestamo->cobrador = isset($solicitud->promotor) ? $solicitud->promotor : (isset($solicitud->cobrador) ? $solicitud->cobrador : '');
        } elseif (isset($prestamo->idcliente) && $prestamo->idcliente) {
            $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $prestamo->idcliente));
            if ($cliente) {
                $prestamo->cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
                $prestamo->doc_identidad = isset($cliente->numero_doc) ? $cliente->numero_doc : (isset($cliente->doc_identidad) ? $cliente->doc_identidad : '');
            }
        }

        if (empty($prestamo->cliente_nombre)) {
            if (!empty($prestamo->cliente)) {
                $prestamo->cliente_nombre = $prestamo->cliente;
            } elseif (!empty($prestamo->nombre_cliente)) {
                $prestamo->cliente_nombre = $prestamo->nombre_cliente;
            }
        }

        $estado_info = $this->_calcular_estado_credito($id);
        $prestamo->estado_credito = $estado_info['estado_credito'];

        $creador_nombre = '';
        if (isset($prestamo->idusuario) && $prestamo->idusuario) {
            $user = $this->core_model->get_by_id('users', array('id' => $prestamo->idusuario));
            if ($user) {
                $creador_nombre = isset($user->username) ? $user->username : trim((isset($user->first_name) ? $user->first_name : '') . ' ' . (isset($user->last_name) ? $user->last_name : ''));
            }
        }

        $normalizar_porcentaje = function($valor) {
            if ($valor === null || $valor === '') {
                return null;
            }
            $numero = floatval($valor);
            if ($numero <= 0) {
                return $numero;
            }
            return ($numero > 1) ? $numero : ($numero * 100);
        };

        $fecha_ultima_cuota = '-';
        if (!empty($rows)) {
            $ultima_cuota = end($rows);
            $fecha_ultima_cuota = !empty($ultima_cuota['fecha']) ? $ultima_cuota['fecha'] : '-';
            reset($rows);
        }

        $cuotas_en_mora = 0;
        foreach ($rows as $row_estado) {
            if (intval(isset($row_estado['dias_mora']) ? $row_estado['dias_mora'] : 0) > 0 && floatval(isset($row_estado['saldo']) ? $row_estado['saldo'] : 0) > 0) {
                $cuotas_en_mora++;
            }
        }

        $sector_economico = '';
        if ($solicitud) {
            if (!empty($solicitud->actividad_economica)) $sector_economico = $solicitud->actividad_economica;
            elseif (!empty($solicitud->giro_negocio)) $sector_economico = $solicitud->giro_negocio;
            elseif (!empty($solicitud->rubro_credito)) $sector_economico = $solicitud->rubro_credito;
            elseif (!empty($solicitud->id_sector_economico)) $sector_economico = $solicitud->id_sector_economico;
            elseif (!empty($solicitud->id_sector_economico2)) $sector_economico = $solicitud->id_sector_economico2;
        }

        $tipo_producto = '';
        if ($solicitud) {
            if (!empty($solicitud->tipo_credito)) $tipo_producto = $solicitud->tipo_credito;
            elseif (!empty($solicitud->producto_nombre)) $tipo_producto = $solicitud->producto_nombre;
            elseif (!empty($solicitud->producto)) $tipo_producto = $solicitud->producto;
        }
        if ($tipo_producto === '' && !empty($prestamo->producto_nombre)) {
            $tipo_producto = $prestamo->producto_nombre;
        }

        $frecuencia = '';
        if (!empty($prestamo->frecuencia_pago)) $frecuencia = $prestamo->frecuencia_pago;
        elseif (!empty($prestamo->periosidad_pagos)) $frecuencia = $prestamo->periosidad_pagos;
        elseif ($solicitud && !empty($solicitud->frecuencia)) $frecuencia = $solicitud->frecuencia;

        $plazo_tiempo = '';
        if (!empty($prestamo->numero_coutas)) $plazo_tiempo = $prestamo->numero_coutas . ' cuotas';
        elseif ($solicitud && !empty($solicitud->plazo_meses)) $plazo_tiempo = $solicitud->plazo_meses . ' meses';

        $comision_desembolso = null;
        if (isset($prestamo->comision_desembolso) && $prestamo->comision_desembolso !== '') {
            $comision_desembolso = $normalizar_porcentaje($prestamo->comision_desembolso);
        } elseif ($solicitud && isset($solicitud->comision_desembolso) && $solicitud->comision_desembolso !== '') {
            $comision_desembolso = $normalizar_porcentaje($solicitud->comision_desembolso);
        }

        $tca = null;
        try {
            $amounts = array();
            $dates = array();
            $principal_tca = isset($prestamo->monto_credito) ? floatval($prestamo->monto_credito) : 0;
            $fecha_desembolso_tca = !empty($prestamo->fecha_credito) ? $prestamo->fecha_credito : (!empty($prestamo->fecha_desembolso) ? $prestamo->fecha_desembolso : date('Y-m-d'));
            $amounts[] = -1.0 * $principal_tca;
            $dates[] = $fecha_desembolso_tca;
            foreach ($rows as $fila_tca) {
                $amounts[] = floatval(isset($fila_tca['cuota']) ? $fila_tca['cuota'] : 0);
                $dates[] = !empty($fila_tca['fecha']) ? $fila_tca['fecha'] : $fecha_desembolso_tca;
            }
            if (count($amounts) > 1) {
                $x = $this->_xirr_calc($amounts, $dates, 0);
                if (is_numeric($x) && is_finite($x) && $x > -0.999999) {
                    $tca = $x;
                }
            }
        } catch (Exception $e) {
            $tca = null;
        }

        $resumen_tecnico = array(
            'sector_economico' => $sector_economico !== '' ? $sector_economico : '-',
            'tasa_moratoria' => $tasa_moratoria_plan,
            'tca' => $tca,
            'tipo_producto' => $tipo_producto !== '' ? $tipo_producto : '-',
            'tipo_cuota' => 'Nivelada',
            'plazo_tiempo' => $plazo_tiempo !== '' ? $plazo_tiempo : '-',
            'tipo_frecuencia' => $frecuencia !== '' ? $frecuencia : '-',
            'comision' => $comision_desembolso,
            'fecha_vencimiento' => $fecha_ultima_cuota,
            'cuotas_en_mora' => $cuotas_en_mora,
            'metodologia' => 'Individual',
            'moneda' => 'USD',
            'codigo_cliente' => isset($prestamo->idcliente) && $prestamo->idcliente ? $prestamo->idcliente : (($solicitud && isset($solicitud->idcliente)) ? $solicitud->idcliente : '-')
        );

        // Renderizar vista PDF completa (réplica formal de estado_cuenta)
        $data = array(
            'prestamo' => $prestamo,
            'rows' => $rows,
            'creador_nombre' => $creador_nombre,
            'resumen_tecnico' => $resumen_tecnico,
        );

        $html = $this->load->view('planescredito/estado_cuenta_simple_pdf', $data, true);

        $this->load->library('pdf');
        $file_name = 'ESTADO_CUENTA_N_' . $id;
        $this->pdf->createPDF($html, $file_name, false, 'A4', 'landscape');
    }

    // Debug route: returns JSON with cashflows and computed TCA/TCM for given prestamo id
    public function debug_xirr($id = null)
    {
        if (!$id) { echo json_encode(array('status' => false, 'message' => 'Falta id')); return; }
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $id));
        if (!$prestamo) { echo json_encode(array('status' => false, 'message' => 'No encontrado')); return; }
        $cuotas = $this->core_model->get_by_id_all('tb_prestamo_cuotas', array('idprestamo' => $id));
        if (!is_array($cuotas)) $cuotas = array();
        $amounts = array(); $dates = array();
        if (isset($prestamo->monto_credito)) {
            $amounts[] = -1.0 * floatval($prestamo->monto_credito);
            $disb_date = isset($prestamo->fecha_credito) ? $prestamo->fecha_credito : null;
            if (!$disb_date && isset($cuotas[0])) {
                $disb_date = $this->_find_first_field_value($cuotas[0], array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
            }
            if (!$disb_date) $disb_date = date('Y-m-d');
            $dates[] = $disb_date;
            foreach ($cuotas as $c) {
                $amount = $this->_find_first_field_value($c, array('cuota','monto','importe','valor','amount'));
                $date = $this->_find_first_field_value($c, array('fecha_vencimiento','fecha_pago','fecha','vencimiento','date'));
                $amounts[] = floatval($amount !== null ? $amount : 0);
                $dates[] = $date !== null ? $date : $disb_date;
            }
        }
        $result = array('status' => true, 'amounts' => $amounts, 'dates' => $dates, 'tca' => null, 'tcm' => null);
        if (count($amounts) > 1) {
            $x = $this->_xirr_calc($amounts, $dates, 0.1);
            if (is_numeric($x) && is_finite($x) && $x > -0.999999) {
                $result['tca'] = $x;
                $result['tcm'] = pow(1 + $x, 1/12.0) - 1;
            } else {
                $result['error'] = 'xirr_failed';
                $result['raw_x'] = $x;
            }
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // Private helper: XIRR calculation (tries Newton, falls back to bisection). Accepts Excel serial dates.
    // Método público AJAX para actualizar días de mora
    public function update_dias_mora()
    {
        // Verificar que el usuario está autenticado
        if (!$this->ion_auth->logged_in()) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No autenticado'
                ]));
        }

        // Obtener parámetros de la petición
        $idcuota = intval($this->input->post('idcuota'));
        $nuevos_dias = intval($this->input->post('nuevos_dias'));

        // Validar entrada
        if ($idcuota <= 0 || $nuevos_dias < 0) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Parámetros inválidos'
                ]));
        }

        try {
            // Obtener información de la cuota
            $this->db->select('idcuota, principal, dias_mora_manual, monto_mora');
            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idcuota', $idcuota);
            $cuota = $this->db->get()->row();

            if (!$cuota) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Cuota no encontrada'
                    ]));
            }

            // Calcular nuevo monto mora
            $principal = floatval($cuota->principal);
            $nuevo_monto_mora = $principal * (0.18 / 360) * $nuevos_dias;
            $nuevo_monto_mora = round($nuevo_monto_mora, 2);

            // Actualizar la base de datos
            $update_data = [
                'dias_mora_manual' => $nuevos_dias,
                'monto_mora' => $nuevo_monto_mora
            ];

            $this->db->where('idcuota', $idcuota);
            $success = $this->db->update('tb_prestamo_cuotas', $update_data);

            if ($success) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'Actualizado correctamente',
                        'nuevo_monto_mora' => $nuevo_monto_mora
                    ]));
            } else {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar la base de datos'
                    ]));
            }
        } catch (Exception $e) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]));
        }
    }

    private function _xirr_calc($amounts, $dates, $guess = 0.1)
    {
        $fmt = function($d){
            // Excel serial date -> timestamp
            if (is_numeric($d)) {
                return (int) round(($d - 25569) * 86400.0);
            }
            // If date looks like dd/mm/YYYY or d/m/YYYY, parse explicitly as day-first
            if (is_string($d) && preg_match('#^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$#', trim($d))) {
                $clean = trim($d);
                // try d/m/Y
                $dt = DateTime::createFromFormat('d/m/Y', $clean);
                if ($dt && $dt->format('d/m/Y') === preg_replace('#[^\d\/]#','/', $clean)) return $dt->getTimestamp();
                // try d-m-Y
                $dt = DateTime::createFromFormat('d-m-Y', $clean);
                if ($dt) return $dt->getTimestamp();
                // try m/d/Y as fallback
                $dt = DateTime::createFromFormat('m/d/Y', $clean);
                if ($dt) return $dt->getTimestamp();
            }
            // Try ISO or other parseable formats
            try { return (new DateTime($d))->getTimestamp(); } catch (Exception $e) { return strtotime($d); }
        };
        $ts = array_map($fmt, $dates);
        $days = array_map(function($t) use ($ts){ return ($t - $ts[0]) / 86400.0; }, $ts);

        $f = function($r) use ($amounts, $days) {
            $res = 0.0; $year = 365.0;
            foreach ($amounts as $i => $a) {
                $den = pow(1 + $r, $days[$i] / $year);
                if (!is_finite($den) || $den == 0) return NAN;
                $res += $a / $den;
            }
            return $res;
        };

        $df = function($r) use ($amounts, $days) {
            $res = 0.0; $year = 365.0;
            foreach ($amounts as $i => $a) {
                $t = $days[$i] / $year;
                $den = pow(1 + $r, $t + 1);
                if (!is_finite($den) || $den == 0) return NAN;
                $res += -$a * $t / $den;
            }
            return $res;
        };

        // Try multiple initial guesses with Newton-Raphson (helps convergence like Excel)
        $possible_guesses = array($guess, 0.0, 0.01, 0.1, 0.2, -0.1, 0.5, 1.0);
        foreach ($possible_guesses as $g) {
            $rate = $g;
            for ($i=0; $i<200; $i++) {
                $fv = $f($rate);
                $dfv = $df($rate);
                if (!is_finite($fv) || !is_finite($dfv)) break;
                if (abs($dfv) < 1e-14) break;
                $new = $rate - $fv / $dfv;
                if (!is_finite($new)) break;
                if (abs($new - $rate) < 1e-12) { $rate = $new; break; }
                $rate = $new;
            }
            if (is_finite($rate) && $rate > -0.999999) return $rate;
        }

        // If Newton failed for all guesses, continue to fallback bisection below

        // Fallback bisection
        $min = -0.999999; $max = 10.0;
        $fa = $f($min); $fb = $f($max);
        if (!is_finite($fa) || !is_finite($fb)) return NAN;
        if ($fa * $fb > 0) {
            $found = false; $step = 0.1;
            for ($a = -0.9999; $a < 50; $a += $step) {
                $b = $a + $step;
                $fa = $f($a); $fb = $f($b);
                if (!is_finite($fa) || !is_finite($fb)) continue;
                if ($fa * $fb <= 0) { $min = $a; $max = $b; $found = true; break; }
            }
            if (!$found) return NAN;
        }
        $a = $min; $b = $max; $fa = $f($a); $fb = $f($b);
        if (!is_finite($fa) || !is_finite($fb) || $fa * $fb > 0) return NAN;
        for ($i=0; $i<200; $i++) {
            $m = ($a + $b) / 2.0; $fm = $f($m);
            if (!is_finite($fm)) break;
            if (abs($fm) < 1e-12) return $m;
            if ($fa * $fm < 0) { $b = $m; $fb = $fm; } else { $a = $m; $fa = $fm; }
            if (abs($b - $a) < 1e-12) return ($a + $b) / 2.0;
        }
        // Log failure with flows to help debugging
        if (function_exists('log_message')) {
            $flows = array('amounts' => $amounts, 'dates' => $dates);
            log_message('error', 'XIRR failed to converge. Flows: ' . json_encode($flows));
        }
        return NAN;
    }

    private function _clasificar_estado_pago($dias_mora, $esta_pagada = false)
    {
        $dias_mora = intval($dias_mora);

        if ($dias_mora <= 0) {
            return $esta_pagada ? 'AL DÍA' : 'VIGENTE';
        }

        if ($dias_mora <= 15) {
            return 'MORA TEMPRANA';
        }
        if ($dias_mora <= 30) {
            return 'MORA';
        }
        if ($dias_mora <= 60) {
            return 'MORA MEDIA';
        }
        if ($dias_mora <= 90) {
            return 'MORA ALTA';
        }
        if ($dias_mora <= 120) {
            return 'CARTERA EN RIESGO';
        }
        if ($dias_mora <= 180) {
            return 'CARTERA DUDOSA';
        }
        if ($dias_mora <= 240) {
            return 'CARTERA CRÍTICA';
        }
        if ($dias_mora <= 360) {
            return 'CARTERA IRRECUPERABLE';
        }

        return 'CASTIGADO';
    }

    private function _calcular_estado_credito($idprestamo)
    {
        $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
        if ($prestamo && isset($prestamo->estado) && intval($prestamo->estado) === 2) {
            return array(
                'dias_mora' => 0,
                'estado_credito' => 'ANULADO'
            );
        }

        $this->db->select('fecha_vencimiento, dias_mora_manual');
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $idprestamo);
        $this->db->where('(saldo IS NULL OR saldo > 0)');
        $this->db->order_by('fecha_vencimiento', 'ASC');
        $cuota_vencida = $this->db->get()->row();

        $dias_mora = 0;
        if ($cuota_vencida) {
            if ($cuota_vencida->dias_mora_manual !== null && $cuota_vencida->dias_mora_manual !== '') {
                $dias_mora = intval($cuota_vencida->dias_mora_manual);
            } elseif (!empty($cuota_vencida->fecha_vencimiento)) {
                $fecha_actual = date('Y-m-d');
                $dias_mora = (strtotime($fecha_actual) - strtotime($cuota_vencida->fecha_vencimiento)) / 86400;
                if ($dias_mora < 0) {
                    $dias_mora = 0;
                }
                $dias_mora = intval($dias_mora);
            }
        }

        return array(
            'dias_mora' => $dias_mora,
            'estado_credito' => $this->_clasificar_estado_pago($dias_mora, false)
        );
    }

    // Find the first non-empty field value from an object using a list of candidate property names
    private function _find_first_field_value($obj, $candidates = array())
    {
        if (!is_object($obj) && !is_array($obj)) return null;
        foreach ($candidates as $c) {
            if (is_object($obj) && isset($obj->{$c}) && $obj->{$c} !== '') return $obj->{$c};
            if (is_array($obj) && array_key_exists($c, $obj) && $obj[$c] !== '') return $obj[$c];
        }
        // try a case-insensitive search
        foreach ($candidates as $c) {
            foreach ($obj as $k => $v) {
                if (strtolower($k) === strtolower($c) && $v !== '') return $v;
            }
        }
        return null;
    }
}
