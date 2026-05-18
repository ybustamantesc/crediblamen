<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('./dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Tesoreria extends CI_Controller {
    // AJAX: Obtener el siguiente número de cheque para una cuenta bancaria
    public function get_ultimo_numero_cheque_ajax() {
        $cuenta_id = $this->input->get('cuenta_id');
        if (!$cuenta_id) {
            header('Content-Type: application/json');
            echo json_encode(['next_numero' => 1]);
            return;
        }
        $this->load->database();
        // Buscar el máximo número de cheque registrado para esa cuenta
        $row = $this->db->query(
            'SELECT MAX(CAST(numero_cheque AS UNSIGNED)) AS max_cheque FROM teso_movimientos WHERE cuenta_id = ? AND forma_pago = ? AND numero_cheque IS NOT NULL AND numero_cheque <> ""',
            array($cuenta_id, 'CHEQUE')
        )->row();
        $next = 1;
        if ($row && $row->max_cheque) {
            $next = intval($row->max_cheque) + 1;
        }
        header('Content-Type: application/json');
        echo json_encode(['next_numero' => $next]);
    }
    /** @var CI_DB_query_builder */
    public $db;
    /** @var CI_Input */
    public $input;
    public function __construct() {
        parent::__construct();
        $this->load->model('Core_model', '', true);
        $this->load->model('Tesoreria_model', '', true);
        $this->load->library('ion_auth');
        $this->load->library('session');
        // router es global en CI, no se carga como propiedad
    }

    private function ensure_teso_pagos_recepcion_columns()
    {
        if (!$this->db->table_exists('teso_pagos')) {
            return;
        }
        $defs = array(
            'idprestamo' => "ALTER TABLE teso_pagos ADD COLUMN idprestamo INT NULL",
            'idcuota' => "ALTER TABLE teso_pagos ADD COLUMN idcuota INT NULL",
            'idcliente' => "ALTER TABLE teso_pagos ADD COLUMN idcliente INT NULL",
            'beneficiario' => "ALTER TABLE teso_pagos ADD COLUMN beneficiario VARCHAR(255) NULL",
            'concepto' => "ALTER TABLE teso_pagos ADD COLUMN concepto VARCHAR(255) NULL",
            'medio_pago' => "ALTER TABLE teso_pagos ADD COLUMN medio_pago VARCHAR(50) NULL",
            'documento_numero' => "ALTER TABLE teso_pagos ADD COLUMN documento_numero VARCHAR(100) NULL",
            'moneda' => "ALTER TABLE teso_pagos ADD COLUMN moneda VARCHAR(10) NULL",
            'idusuario' => "ALTER TABLE teso_pagos ADD COLUMN idusuario INT NULL",
            'idserie' => "ALTER TABLE teso_pagos ADD COLUMN idserie INT NULL",
            'serie_codigo' => "ALTER TABLE teso_pagos ADD COLUMN serie_codigo VARCHAR(20) NULL",
            'tc_compra' => "ALTER TABLE teso_pagos ADD COLUMN tc_compra DECIMAL(10,4) NULL",
            'tc_venta' => "ALTER TABLE teso_pagos ADD COLUMN tc_venta DECIMAL(10,4) NULL",
            'tc_aplicada' => "ALTER TABLE teso_pagos ADD COLUMN tc_aplicada DECIMAL(10,4) NULL",
            'monto_usd_aplicado' => "ALTER TABLE teso_pagos ADD COLUMN monto_usd_aplicado DECIMAL(18,2) NULL",
            'monto_usd' => "ALTER TABLE teso_pagos ADD COLUMN monto_usd DECIMAL(18,2) NULL",
            'monto_nio' => "ALTER TABLE teso_pagos ADD COLUMN monto_nio DECIMAL(18,2) NULL",
            'monto_total_usd' => "ALTER TABLE teso_pagos ADD COLUMN monto_total_usd DECIMAL(18,2) NULL",
            'dato_adicional' => "ALTER TABLE teso_pagos ADD COLUMN dato_adicional TEXT NULL",
            'monto_recibido' => "ALTER TABLE teso_pagos ADD COLUMN monto_recibido DECIMAL(18,2) NULL",
            'fecha_recepcion' => "ALTER TABLE teso_pagos ADD COLUMN fecha_recepcion DATE NULL",
            'recibo_revisado' => "ALTER TABLE teso_pagos ADD COLUMN recibo_revisado TINYINT(1) NOT NULL DEFAULT 0",
            'recepcion_validada' => "ALTER TABLE teso_pagos ADD COLUMN recepcion_validada TINYINT(1) NOT NULL DEFAULT 0",
            'recepcion_guardada_at' => "ALTER TABLE teso_pagos ADD COLUMN recepcion_guardada_at DATETIME NULL"
        );
        foreach ($defs as $field => $sql) {
            if (!$this->db->field_exists($field, 'teso_pagos')) {
                try {
                    $this->db->query($sql);
                } catch (Exception $e) {
                    log_message('error', 'ensure_teso_pagos_recepcion_columns: ' . $field . ' -> ' . $e->getMessage());
                }
            }
        }
    }

    // AJAX: guardar revisión de recibos (por fila o por serie)
    public function guardar_revision_recibos_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_teso_pagos_recepcion_columns();
        if (!$this->db->table_exists('teso_pagos')) {
            echo json_encode(array('status' => false, 'message' => 'Tabla de pagos no disponible'));
            return;
        }

        $revisado = intval($this->input->post('revisado')) === 1 ? 1 : 0;
        $rawItems = $this->input->post('items');
        $ids = array();

        if (is_string($rawItems) && trim($rawItems) !== '') {
            $arr = json_decode($rawItems, true);
            if (is_array($arr)) {
                foreach ($arr as $it) {
                    $id = is_array($it) ? intval(isset($it['id']) ? $it['id'] : 0) : intval($it);
                    if ($id > 0) $ids[] = $id;
                }
            }
        } elseif (is_array($rawItems)) {
            foreach ($rawItems as $it) {
                $id = is_array($it) ? intval(isset($it['id']) ? $it['id'] : 0) : intval($it);
                if ($id > 0) $ids[] = $id;
            }
        }

        $singleId = intval($this->input->post('pago_id'));
        if ($singleId > 0) $ids[] = $singleId;

        $ids = array_values(array_unique(array_filter($ids, function ($v) { return intval($v) > 0; })));
        if (empty($ids)) {
            echo json_encode(array('status' => false, 'message' => 'No se enviaron pagos para actualizar'));
            return;
        }

        $up = array('recibo_revisado' => $revisado);
        if ($this->db->field_exists('updated_at', 'teso_pagos')) {
            $up['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where_in('id', $ids);
        $ok = $this->db->update('teso_pagos', $up);
        if (!$ok) {
            $err = $this->db->error();
            echo json_encode(array('status' => false, 'message' => isset($err['message']) ? $err['message'] : 'No se pudo guardar revisión'));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Revisión actualizada',
            'updated' => count($ids),
            'revisado' => $revisado
        ));
    }

    private function ensure_tb_prestamo_pagos_distribution_columns()
    {
        if (!$this->db->table_exists('tb_prestamo_pagos')) {
            return;
        }
        $defs = array(
            'monto_principal_pagado' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_principal_pagado DECIMAL(18,2) NULL",
            'monto_interes_corriente_pagado' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_interes_corriente_pagado DECIMAL(18,2) NULL",
            'monto_interes_mora_pagado' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_interes_mora_pagado DECIMAL(18,2) NULL",
            'monto_interes_pagado' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_interes_pagado DECIMAL(18,2) NULL",
            'monto_usd_recibido' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_usd_recibido DECIMAL(18,2) NULL",
            'monto_nio_recibido' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN monto_nio_recibido DECIMAL(18,2) NULL",
            'tc_venta_aplicada' => "ALTER TABLE tb_prestamo_pagos ADD COLUMN tc_venta_aplicada DECIMAL(10,4) NULL"
        );
        foreach ($defs as $field => $sql) {
            if (!$this->db->field_exists($field, 'tb_prestamo_pagos')) {
                try {
                    $this->db->query($sql);
                } catch (Exception $e) {
                    log_message('error', 'ensure_tb_prestamo_pagos_distribution_columns: ' . $field . ' -> ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * AJAX: Obtener cuentas bancarias activas
     * Usado para selectores de desembolsos
     */
    public function get_cuentas_banco_ajax() {
        $this->load->database();
        $cuentas = $this->db->where('type', 'banco')
            ->where('estado', 1)
            ->order_by('name', 'asc')
            ->get('teso_accounts')
            ->result();
        header('Content-Type: application/json');
        echo json_encode($cuentas);
    }


    // AJAX: Obtener beneficiarios
    public function get_beneficiarios_ajax() {
        $this->load->database();
        $beneficiarios = $this->db->order_by('descripcion','asc')->get('beneficiarios')->result();
        header('Content-Type: application/json');
        echo json_encode(['status'=>true, 'beneficiarios'=>$beneficiarios]);
    }

    // AJAX: Guardar nuevo beneficiario
    public function save_beneficiario_ajax() {
        $this->load->database();
        $p = $this->input->post(NULL, TRUE);
        $data = [
            'clave' => isset($p['clave']) ? $p['clave'] : '',
            'descripcion' => isset($p['descripcion']) ? $p['descripcion'] : '',
            'rfc' => isset($p['rfc']) ? $p['rfc'] : '',
            'cuenta' => isset($p['cuenta']) ? $p['cuenta'] : '',
            'clabe' => isset($p['clabe']) ? $p['clabe'] : ''
        ];
        $this->db->insert('beneficiarios', $data);
        $id = $this->db->insert_id();
        header('Content-Type: application/json');
        echo json_encode(['status'=>true, 'id'=>$id]);
    }

    // AJAX: Obtener conceptos bancarios
    public function get_conceptos_ajax() {
        $this->load->database();
        $conceptos = $this->db->order_by('descripcion','asc')->get('conceptos_bancarios')->result();
        header('Content-Type: application/json');
        echo json_encode(['status'=>true, 'conceptos'=>$conceptos]);
    }

    // AJAX: Guardar nuevo concepto bancario
    public function save_concepto_ajax() {
        $this->load->database();
        $p = $this->input->post(NULL, TRUE);
        $data = [
            'clave' => isset($p['clave']) ? $p['clave'] : '',
            'descripcion' => isset($p['descripcion']) ? $p['descripcion'] : '',
            'tipo' => isset($p['tipo']) ? $p['tipo'] : 'CARGO',
            'concepto_sae' => isset($p['concepto_sae']) ? $p['concepto_sae'] : null
        ];
        $this->db->insert('conceptos_bancarios', $data);
        $id = $this->db->insert_id();
        header('Content-Type: application/json');
        echo json_encode(['status'=>true, 'id'=>$id]);
                }
            // AJAX: Guardar movimiento bancario
            public function save_movimiento_ajax() {
        $this->load->database();
        $p = $this->input->post(NULL, TRUE);
        // Validar cuenta_id
        if (!isset($p['cuenta_id']) || !is_numeric($p['cuenta_id']) || intval($p['cuenta_id']) <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status'=>false, 'message'=>'El campo cuenta_id es obligatorio y debe ser un número válido.']);
            return;
        }
        $cuenta_id = intval($p['cuenta_id']);
        $forma_pago = isset($p['forma_pago']) ? $p['forma_pago'] : null;
        $numero_cheque = isset($p['numero_cheque']) ? $p['numero_cheque'] : null;
        // Si es cheque, usar el número recibido (autoincremental por AJAX)
        if (strtoupper($forma_pago) === 'CHEQUE') {
            if (!$numero_cheque) {
                // fallback: buscar el máximo en la tabla
                $row = $this->db->query(
                    'SELECT MAX(CAST(numero_cheque AS UNSIGNED)) AS max_cheque FROM teso_movimientos WHERE cuenta_id = ? AND forma_pago = ? AND numero_cheque IS NOT NULL AND numero_cheque <> ""',
                    array($cuenta_id, 'CHEQUE')
                )->row();
                $numero_cheque = ($row && $row->max_cheque) ? (intval($row->max_cheque) + 1) : 1;
            }
        }
        $data = [
            'tipo_movimiento' => ($forma_pago && strtoupper($forma_pago) === 'CHEQUE') ? 'cheque' : 'transferencia',
            'concepto' => isset($p['concepto']) ? $p['concepto'] : null,
            'forma_pago' => $forma_pago,
            'fecha_registro' => isset($p['fecha_registro']) ? $p['fecha_registro'] : null,
            'fecha_aplicacion' => isset($p['fecha_aplicacion']) ? $p['fecha_aplicacion'] : null,
            'primer_dia_pago' => isset($p['primer_dia_pago']) ? $p['primer_dia_pago'] : null,
            'beneficiario' => isset($p['beneficiario']) ? $p['beneficiario'] : null,
            'referencia1' => isset($p['referencia1']) ? $p['referencia1'] : null,
            'referencia2' => isset($p['referencia2']) ? $p['referencia2'] : null,
            'monto_total' => isset($p['monto_total']) ? $p['monto_total'] : 0,
            'iva_total' => isset($p['iva_total']) ? $p['iva_total'] : 0,
            'departamento' => isset($p['departamento']) ? $p['departamento'] : null,
            'centro_costos' => isset($p['centro_costos']) ? $p['centro_costos'] : null,
            'proyecto' => isset($p['proyecto']) ? $p['proyecto'] : null,
            'descripcion' => isset($p['descripcion']) ? $p['descripcion'] : null,
            'cuenta_id' => $cuenta_id,
            'tipo_transferencia' => isset($p['tipo_transferencia']) ? $p['tipo_transferencia'] : null,
            'numero_cheque' => $numero_cheque,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('teso_movimientos', $data);
        $id = $this->db->insert_id();
        if ($id) {
            // Si es cheque, incrementar el consecutivo en la cuenta
            if (strtoupper((string)$forma_pago) === 'CHEQUE') {
                $this->db->set('sig_cheque', 'sig_cheque+1', false);
                $this->db->where('id', $cuenta_id);
                $this->db->update('teso_accounts');
            }
            header('Content-Type: application/json');
            echo json_encode(['status'=>true, 'id'=>$id]);
        } else {
            $error = $this->db->error();
            header('Content-Type: application/json');
            echo json_encode(['status'=>false, 'message'=>'Error MySQL: '.$error['message']]);
        }
            }
        // AJAX: Obtener movimientos filtrados
        public function get_movimientos_ajax() {
            $cuenta_id = $this->input->get('cuenta_id');
            $this->db->from('teso_movimientos');
            if ($cuenta_id) $this->db->where('cuenta_id', $cuenta_id);
            $this->db->order_by('fecha_registro', 'desc');
            $movs = $this->db->get()->result_array();

            // Resolver nombres de usuario para evitar mostrar IDs en la columna Ejecutado por.
            $userIds = array();
            foreach ($movs as $mTmp) {
                $raw = isset($mTmp['creado_por']) ? trim((string)$mTmp['creado_por']) : '';
                if ($raw === '') continue;
                $uid = 0;
                if (ctype_digit($raw)) {
                    $uid = intval($raw);
                } elseif (preg_match('/^user[_\-\s]*(\d+)$/i', $raw, $um)) {
                    $uid = intval($um[1]);
                }
                if ($uid > 0) $userIds[$uid] = $uid;
            }

            $usersMap = array();
            if (!empty($userIds) && $this->db->table_exists('users')) {
                $users = $this->db->where_in('id', array_values($userIds))->get('users')->result();
                foreach ($users as $u) {
                    $nombre = trim(((isset($u->first_name) ? $u->first_name : '') . ' ' . (isset($u->last_name) ? $u->last_name : '')));
                    if ($nombre === '' && isset($u->username) && trim((string)$u->username) !== '') {
                        $nombre = trim((string)$u->username);
                    }
                    if ($nombre === '' && isset($u->email) && trim((string)$u->email) !== '') {
                        $nombre = trim((string)$u->email);
                    }
                    $usersMap[intval($u->id)] = $nombre !== '' ? $nombre : ('Usuario #' . intval($u->id));
                }
            }

            // Obtener todas las cuentas activas para mapear nombre y código
            $cuentas = $this->db->order_by('name','asc')->get_where('teso_accounts', "estado = 1")->result_array();
            $cuentas_map = array();
            foreach($cuentas as $c){
                $cuentas_map[$c['id']] = $c['name'].' ('.$c['code'].')';
            }
            // Agregar nombre de cuenta y más campos a cada movimiento
            foreach($movs as &$m){
                $m['cuenta_nombre'] = isset($cuentas_map[$m['cuenta_id']]) ? $cuentas_map[$m['cuenta_id']] : $m['cuenta_id'];
                $m['tiene_asiento'] = isset($m['contabilizado']) && $m['contabilizado'] == 1 ? 1 : 0;
                $rawEjecutor = isset($m['creado_por']) ? trim((string)$m['creado_por']) : '';
                $uidEjecutor = 0;
                if ($rawEjecutor !== '') {
                    if (ctype_digit($rawEjecutor)) {
                        $uidEjecutor = intval($rawEjecutor);
                    } elseif (preg_match('/^user[_\-\s]*(\d+)$/i', $rawEjecutor, $um2)) {
                        $uidEjecutor = intval($um2[1]);
                    }
                }
                if ($uidEjecutor > 0 && isset($usersMap[$uidEjecutor])) {
                    $m['ejecutado_por'] = $usersMap[$uidEjecutor];
                } else {
                    $m['ejecutado_por'] = $rawEjecutor !== '' ? $rawEjecutor : '-';
                }

                $referencia2 = isset($m['referencia2']) ? (string)$m['referencia2'] : '';
                $es_desembolso = (isset($m['tipo']) && (string)$m['tipo'] === 'desembolso_preview') || strpos($referencia2, 'p=') === 0;
                if ($es_desembolso) {
                    $meta = array();
                    parse_str($referencia2, $meta);
                    $idprestamo = isset($meta['p']) ? intval($meta['p']) : 0;
                    if ($idprestamo > 0) {
                        $prestamo = $this->db->select('usuario_desembolso')
                            ->from('tb_prestamos')
                            ->where('idprestamo', $idprestamo)
                            ->limit(1)
                            ->get()
                            ->row();
                        if ($prestamo && !empty($prestamo->usuario_desembolso)) {
                            $m['ejecutado_por'] = (string)$prestamo->usuario_desembolso;
                        }
                    }
                }
            }
            header('Content-Type: application/json');
            echo json_encode(['status'=>true, 'movimientos'=>$movs]);
        }

        // AJAX: Obtener siguiente número de cheque
        public function get_sig_cheque_ajax() {
            $cuenta_id = $this->input->get('cuenta_id');
            if (!$cuenta_id) {
                echo json_encode(['status'=>false, 'message'=>'Cuenta no especificada']);
                return;
            }
            $cuenta = $this->db->get_where('teso_accounts', ['id' => $cuenta_id])->row();
            if ($cuenta && isset($cuenta->sig_cheque)) {
                echo json_encode(['status'=>true, 'sig_cheque'=>$cuenta->sig_cheque]);
            } else {
                echo json_encode(['status'=>false, 'message'=>'Cuenta no encontrada']);
            }
        }

        // AJAX: Obtener movimiento por ID
        public function get_movimiento_ajax() {
            $id = $this->input->get('id');
            if (!$id) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
            $mov = $this->db->get_where('teso_movimientos', ['id' => $id])->row();
            if ($mov) {
                echo json_encode(['status'=>true,'movimiento'=>$mov]);
            } else {
                echo json_encode(['status'=>false,'message'=>'No encontrado']);
            }
        }

            // AJAX: Finalizar una solicitud de desembolso en estado previsualización
            public function finalizar_desembolso_preview_ajax() {
                header('Content-Type: application/json');
                $id = intval($this->input->post('id'));
                if ($id <= 0) {
                    echo json_encode(['status' => false, 'message' => 'ID requerido']);
                    return;
                }

                $mov = $this->db->get_where('teso_movimientos', ['id' => $id])->row();
                if (!$mov) {
                    echo json_encode(['status' => false, 'message' => 'Solicitud no encontrada']);
                    return;
                }

                if (strtolower((string)$mov->estado) !== 'previsualizacion') {
                    echo json_encode(['status' => false, 'message' => 'La solicitud ya fue finalizada o no está pendiente']);
                    return;
                }

                parse_str((string)$mov->referencia2, $meta);
                $idprestamo = isset($meta['p']) ? intval($meta['p']) : 0;
                if ($idprestamo <= 0) {
                    echo json_encode(['status' => false, 'message' => 'No se pudo determinar el préstamo asociado']);
                    return;
                }

                $fecha_desembolso = isset($meta['fd']) ? $meta['fd'] : $mov->fecha_registro;
                $primer_dia_pago = isset($meta['pp']) ? $meta['pp'] : null;
                $costos_legales = isset($meta['cl']) ? floatval($meta['cl']) : 0;
                $seguros = isset($meta['sg']) ? floatval($meta['sg']) : 0;
                $comisiones = isset($meta['cm']) ? floatval($meta['cm']) : 0;

                $usuario = $this->session->userdata('username') ?: $this->session->userdata('user_id');
                $obs_desembolso = trim((string)($mov->descripcion ?: $mov->concepto));

                $this->db->trans_start();

                $this->db->where('idprestamo', $idprestamo);
                $this->db->update('tb_prestamos', [
                    'desembolsado' => 1,
                    'fecha_desembolso' => $fecha_desembolso,
                    'fecha_desembolso_real' => date('Y-m-d H:i:s'),
                    'primer_dia_pago' => $primer_dia_pago,
                    'usuario_desembolso' => $usuario,
                    'obs_desembolso' => $obs_desembolso,
                    'costos_legales' => $costos_legales,
                    'seguros' => $seguros,
                    'comisiones' => $comisiones
                ]);

                $this->db->where('id', $id);
                $this->db->update('teso_movimientos', [
                    'estado' => 'activo',
                    'fecha_aplicacion' => $fecha_desembolso
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status() === false) {
                    echo json_encode(['status' => false, 'message' => 'No se pudo finalizar el desembolso']);
                    return;
                }

                echo json_encode(['status' => true, 'message' => 'Desembolso ejecutado correctamente']);
            }

        // Vista imprimible simple de cheque
        public function imprimir_cheque($id = null) {
            if (!$id || !is_numeric($id)) {
                show_error('ID de cheque inválido');
            }

            $mov = $this->db
                ->select('m.*, a.name as cuenta_nombre, a.code as cuenta_codigo')
                ->from('teso_movimientos m')
                ->join('teso_accounts a', 'a.id = m.cuenta_id', 'left')
                ->where('m.id', intval($id))
                ->limit(1)
                ->get()
                ->row();

            if (!$mov) {
                show_error('Cheque no encontrado');
            }

            $isCheque = (strtoupper((string)$mov->forma_pago) === 'CHEQUE') || (strtolower((string)$mov->tipo_movimiento) === 'cheque');
            if (!$isCheque) {
                show_error('El movimiento indicado no es un cheque');
            }

            $data = array('mov' => $mov);
            $this->load->view('tesoreria/cheque_print', $data);
        }
        // AJAX: Actualizar movimiento
        public function update_movimiento_ajax() {
            $this->load->database();
            $p = $this->input->post(NULL, TRUE);
            if (!isset($p['id']) || intval($p['id'])<=0) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
            $data = [
                'cuenta_id' => isset($p['cuenta_id']) ? $p['cuenta_id'] : null,
                'clave_concepto' => isset($p['clave_concepto']) ? $p['clave_concepto'] : null,
                'forma_pago' => isset($p['forma_pago']) ? $p['forma_pago'] : null,
                'fecha' => isset($p['fecha']) ? $p['fecha'] : null,
                'fecha_aplicacion' => isset($p['fecha_aplicacion']) ? $p['fecha_aplicacion'] : null,
                'a_nombre_de' => isset($p['a_nombre_de']) ? $p['a_nombre_de'] : null,
                'referencia1' => isset($p['referencia1']) ? $p['referencia1'] : null,
                'referencia2' => isset($p['referencia2']) ? $p['referencia2'] : null,
                'monto' => isset($p['monto']) ? $p['monto'] : (isset($p['monto_total']) ? $p['monto_total'] : null),
                'iva_total' => isset($p['iva_total']) ? $p['iva_total'] : null,
                'cuenta_destino' => isset($p['cuenta_destino']) ? $p['cuenta_destino'] : null,
                'banco_destino' => isset($p['banco_destino']) ? $p['banco_destino'] : null,
                'numero_cheque' => isset($p['numero_cheque']) ? $p['numero_cheque'] : null,
                'cheque_a' => isset($p['cheque_a']) ? $p['cheque_a'] : null,
                'descripcion' => isset($p['descripcion']) ? $p['descripcion'] : null,
                'estado' => isset($p['estado']) ? $p['estado'] : null
            ];
            $this->db->where('id', intval($p['id']))->update('teso_movimientos', $data);
            echo json_encode(['status'=>true]);
        }
        // AJAX: Anular movimiento
        public function anular_movimiento_ajax() {
            $id = $this->input->post('id');
            $motivo = $this->input->post('motivo');
            if (!$id) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
            if (!$motivo) { echo json_encode(['status'=>false,'message'=>'Motivo requerido']); return; }
            $mov = $this->db->get_where('teso_movimientos', ['id' => intval($id)])->row();
            if (!$mov) {
                echo json_encode(['status'=>false,'message'=>'Movimiento no encontrado']);
                return;
            }

            $meta = array();
            parse_str((string)$mov->referencia2, $meta);
            $idprestamo = isset($meta['p']) ? intval($meta['p']) : 0;
            $es_desembolso = (string)$mov->tipo === 'desembolso_preview'
                || strpos((string)$mov->referencia2, 'p=') === 0;

            $this->db->trans_start();
            $data = [
                'estado' => 'anulado',
                'motivo_anulacion' => $motivo,
                'fecha_anulacion' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', intval($id))->update('teso_movimientos', $data);

            if ($es_desembolso && $idprestamo > 0) {
                $this->db->where('idprestamo', $idprestamo)->update('tb_prestamos', [
                    'desembolsado' => 0,
                    'fecha_desembolso' => null,
                    'fecha_desembolso_real' => null,
                    'primer_dia_pago' => null,
                    'usuario_desembolso' => null,
                    'obs_desembolso' => null,
                    'costos_legales' => 0,
                    'seguros' => 0,
                    'comisiones' => 0
                ]);
            }

            // Si está contabilizado, anular el asiento contable relacionado
            $q = $this->db->select('id')->from('tb_journal')
                ->where('source_type', 'teso_movimiento')
                ->where('source_id', intval($id))
                ->limit(1)->get();
            $asiento_id = null;
            if ($q->num_rows() > 0) {
                $asiento_id = $q->row()->id;
                $user_id = $this->session->userdata('user_id');
                $this->db->where('id', $asiento_id)->update('tb_journal', [
                    'voided' => 1,
                    'voided_by' => $user_id,
                    'voided_at' => date('Y-m-d H:i:s'),
                    'void_reason' => $motivo
                ]);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                echo json_encode(['status'=>false,'msg'=>'No se pudo anular el movimiento']);
                return;
            }
            echo json_encode(['status'=>true, 'asiento_id'=>$asiento_id]);
        }

    public function index()
    {
        $data = [
            'titulo' => 'Tesorería',
            'subtitulo' => 'Inicio - Gestión de Tesorería',
            'icono' => 'fas fa-wallet',
            'scripts' => ['js/tesoreria_home.js']
        ];
        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/home', $data);
        $this->load->view('layout/footer');
    }

    // Admin-only helper to run the local SQL that creates missing tesoreria tables.
    // Usage: GET /tesoreria/setup_db (must be admin)
    public function setup_db()
    {
        if (!$this->ion_auth->is_admin()) {
            show_error('Permisos insuficientes');
        }
        $sqlFile = APPPATH . '../sql/create_tb_tesoreria.sql';
        if (!file_exists($sqlFile)) {
            echo "SQL file not found: $sqlFile"; return;
        }
        $sql = file_get_contents($sqlFile);
        // split by semicolon safely (basic) and execute statements
        $stmts = array_filter(array_map('trim', explode(';', $sql)));
        $results = array('executed' => 0, 'errors' => array());
        foreach ($stmts as $s) {
            if ($s === '') continue;
            try {
                $this->db->query($s);
                $results['executed']++;
            } catch (Exception $e) {
                $results['errors'][] = $e->getMessage();
            }
        }
        header('Content-Type: application/json');
        echo json_encode($results);
    }

    public function cajas_bancos() { $this->_page('Cajas y Bancos', 'tesoreria/cajas_bancos'); }
    public function movimientos() {
        // Permitir filtrar por cuenta
        $this->load->database();
        $cuenta_id = $this->input->get('cuenta_id');
        // Cargar solo cuentas activas (estado=1)
        $cuentas = $this->db->order_by('name','asc')->get_where('teso_accounts', "estado = 1")->result();
        $data = [
            'titulo' => 'Movimientos',
            'icono' => 'fas fa-wallet',
            'cuenta_id' => $cuenta_id,
            'cuentas' => $cuentas
        ];
        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/movimientos', $data);
        $this->load->view('layout/footer');
    }
    public function conciliacion() {
        // Permitir filtrar por cuenta
        $cuenta_id = $this->input->get('cuenta_id');
        $data = ['titulo' => 'Conciliación Bancaria', 'icono' => 'fas fa-wallet', 'cuenta_id' => $cuenta_id];
        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/conciliacion', $data);
        $this->load->view('layout/footer');
    }
    public function pagos()
    {
        $this->ensure_teso_pagos_recepcion_columns();
        $fecha = $this->input->get('fecha');
        $fechaInicio = trim((string)$this->input->get('fecha_inicio'));
        $fechaFin = trim((string)$this->input->get('fecha_fin'));
        $idserie = intval($this->input->get('idserie'));
        $q = trim((string)$this->input->get('q'));
        if ($fechaInicio === '' && $fechaFin === '') {
            if (!empty($fecha)) {
                $fechaInicio = $fecha;
                $fechaFin = $fecha;
            } else {
                $fechaInicio = date('Y-m-d');
                $fechaFin = date('Y-m-d');
            }
        } elseif ($fechaInicio !== '' && $fechaFin === '') {
            $fechaFin = $fechaInicio;
        } elseif ($fechaInicio === '' && $fechaFin !== '') {
            $fechaInicio = $fechaFin;
        }

        $filters = array(
            'fecha' => $fecha,
            'date_from' => $fechaInicio,
            'date_to' => $fechaFin,
            'idserie' => $idserie,
            'q' => $q
        );

        $tasaCompra = null;
        $tasaVenta = null;
        if ($this->db->table_exists('tb_tasa_cambio')) {
            $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
            if ($tasaRow) {
                if (isset($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                    $tasaCompra = floatval($tasaRow->tasa_cambio);
                }
                if (isset($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                    $tasaVenta = floatval($tasaRow->tasa_venta);
                }
                if (($tasaCompra === null || $tasaCompra <= 0) && $tasaVenta !== null && $tasaVenta > 0) {
                    $tasaCompra = $tasaVenta;
                }
                if (($tasaVenta === null || $tasaVenta <= 0) && $tasaCompra !== null && $tasaCompra > 0) {
                    $tasaVenta = $tasaCompra;
                }
            }
        }

        $data = [
            'titulo' => 'Gestión de Pagos',
            'icono' => 'fas fa-wallet',
            'filtro_fecha' => $fecha,
            'filtro_fecha_inicio' => $fechaInicio,
            'filtro_fecha_fin' => $fechaFin,
            'filtro_idserie' => $idserie,
            'filtro_q' => $q,
            'tasa_compra' => $tasaCompra,
            'tasa_venta' => $tasaVenta,
            'series_recibos' => $this->db->table_exists('tb_series_recibos') ? $this->db->order_by('codigo', 'ASC')->get('tb_series_recibos')->result() : array(),
            'pagos_pendientes' => $this->Tesoreria_model->get_pagos_pendientes($filters)
        ];
        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/pagos', $data);
        $this->load->view('layout/footer');
    }

    private function _get_next_cierre_numero()
    {
        if (!$this->db->table_exists('tb_cierres_caja')) {
            return 1;
        }
        $result = $this->db->select_max('consecutivo')->get('tb_cierres_caja')->row();
        $max = (isset($result->consecutivo) && $result->consecutivo) ? intval($result->consecutivo) : 0;
        return $max + 1;
    }

    private function _create_cierre_caja($pagos_ids, $total_monto = 0)
    {
        if (!$this->db->table_exists('tb_cierres_caja') || !is_array($pagos_ids)) {
            return null;
        }

        $usuario_id = null;
        try {
            if (method_exists($this->ion_auth, 'get_user_id')) {
                $tmpUserId = intval($this->ion_auth->get_user_id());
                $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
            }
            if ($usuario_id === null) {
                $usuarioRow = $this->ion_auth->user()->row();
                if ($usuarioRow && isset($usuarioRow->id)) {
                    $tmpUserId = intval($usuarioRow->id);
                    $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
                }
            }
        } catch (Exception $e) {
            $usuario_id = null;
        }

        $consecutivo = $this->_get_next_cierre_numero();
        
        $cierre_data = array(
            'consecutivo' => $consecutivo,
            'fecha_cierre' => date('Y-m-d H:i:s'),
            'idusuario' => $usuario_id,
            'monto_total' => floatval($total_monto),
            'cantidad_pagos' => count($pagos_ids),
            'estado' => 'cerrado',
            'observaciones' => 'Cierre de caja #' . $consecutivo . ' generado automáticamente'
        );

        $this->db->insert('tb_cierres_caja', $cierre_data);
        $cierre_id = $this->db->insert_id();

        // Vincular todos los pagos al cierre
        if ($this->db->field_exists('idcierre_caja', 'teso_pagos') && !empty($pagos_ids)) {
            $this->db->where_in('id', $pagos_ids);
            $this->db->update('teso_pagos', array('idcierre_caja' => $cierre_id));
        }

        return $cierre_id;
    }

    private function _resolver_nombre_usuario_cierre($usuario_id)
    {
        $uid = intval($usuario_id);
        if ($uid <= 0) {
            return 'N/A';
        }

        if ($this->db->table_exists('users') && $this->db->field_exists('id', 'users')) {
            $row = $this->db
                ->select('id, first_name, last_name, username, email')
                ->where('id', $uid)
                ->limit(1)
                ->get('users')
                ->row();
            if ($row) {
                $nombre = trim((isset($row->first_name) ? (string)$row->first_name : '') . ' ' . (isset($row->last_name) ? (string)$row->last_name : ''));
                if ($nombre === '' && isset($row->username) && trim((string)$row->username) !== '') {
                    $nombre = trim((string)$row->username);
                }
                if ($nombre === '' && isset($row->email) && trim((string)$row->email) !== '') {
                    $nombre = trim((string)$row->email);
                }
                if ($nombre !== '') {
                    return $nombre;
                }
            }
        }

        if ($this->db->table_exists('tb_usuarios')) {
            $pk = $this->db->field_exists('idusuario', 'tb_usuarios') ? 'idusuario' : ($this->db->field_exists('id', 'tb_usuarios') ? 'id' : null);
            if ($pk !== null) {
                $row = $this->db->where($pk, $uid)->limit(1)->get('tb_usuarios')->row();
                if ($row) {
                    $nombre = '';
                    if (isset($row->nombres) || isset($row->apellidos)) {
                        $nombre = trim((isset($row->nombres) ? (string)$row->nombres : '') . ' ' . (isset($row->apellidos) ? (string)$row->apellidos : ''));
                    } elseif (isset($row->nombre)) {
                        $nombre = trim((string)$row->nombre);
                    } elseif (isset($row->usuario)) {
                        $nombre = trim((string)$row->usuario);
                    }
                    if ($nombre !== '') {
                        return $nombre;
                    }
                }
            }
        }

        return 'Usuario #' . $uid;
    }

    private function ensure_cierre_arqueo_tables()
    {
        if (!$this->db->table_exists('tb_cierre_arqueos')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS tb_cierre_arqueos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idcierre_caja INT NOT NULL,
                monto_cierre_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                monto_cierre_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                total_billetaje_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                total_billetaje_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                diferencia_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                diferencia_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                comentario_diferencia TEXT NULL,
                idbanco INT NULL,
                estado_deposito VARCHAR(20) NOT NULL DEFAULT 'pendiente',
                monto_depositado_total DECIMAL(18,2) NULL,
                referencia_minuta VARCHAR(120) NULL,
                fecha_deposito DATE NULL,
                idusuario INT NULL,
                deposito_movimiento_id INT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NULL,
                UNIQUE KEY uq_cierre_arqueo_idcierre (idcierre_caja)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$this->db->table_exists('tb_cierre_arqueo_detalle')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS tb_cierre_arqueo_detalle (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idarqueo INT NOT NULL,
                moneda VARCHAR(10) NOT NULL,
                denominacion DECIMAL(10,2) NOT NULL,
                cantidad INT NOT NULL DEFAULT 0,
                monto DECIMAL(18,2) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                KEY idx_arqueo (idarqueo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$this->db->table_exists('tb_cierre_arqueos_series')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS tb_cierre_arqueos_series (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idcierre_caja INT NOT NULL,
                serie_codigo VARCHAR(20) NOT NULL,
                monto_cierre_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                monto_cierre_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                total_billetaje_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                total_billetaje_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                diferencia_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                diferencia_nio DECIMAL(18,2) NOT NULL DEFAULT 0,
                comentario_diferencia TEXT NULL,
                edit_autorizado_por VARCHAR(150) NULL,
                edit_comentario TEXT NULL,
                edit_count INT NOT NULL DEFAULT 0,
                idusuario INT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NULL,
                UNIQUE KEY uq_cierre_serie (idcierre_caja, serie_codigo),
                KEY idx_cierre_serie (idcierre_caja, serie_codigo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if ($this->db->table_exists('tb_cierre_arqueos_series')) {
            if (!$this->db->field_exists('edit_autorizado_por', 'tb_cierre_arqueos_series')) {
                $this->db->query("ALTER TABLE tb_cierre_arqueos_series ADD COLUMN edit_autorizado_por VARCHAR(150) NULL");
            }
            if (!$this->db->field_exists('edit_comentario', 'tb_cierre_arqueos_series')) {
                $this->db->query("ALTER TABLE tb_cierre_arqueos_series ADD COLUMN edit_comentario TEXT NULL");
            }
            if (!$this->db->field_exists('edit_count', 'tb_cierre_arqueos_series')) {
                $this->db->query("ALTER TABLE tb_cierre_arqueos_series ADD COLUMN edit_count INT NOT NULL DEFAULT 0");
            }
        }

        if (!$this->db->table_exists('tb_cierre_arqueo_serie_detalle')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS tb_cierre_arqueo_serie_detalle (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idarqueo_serie INT NOT NULL,
                moneda VARCHAR(10) NOT NULL,
                denominacion DECIMAL(10,2) NOT NULL,
                cantidad INT NOT NULL DEFAULT 0,
                monto DECIMAL(18,2) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                KEY idx_arqueo_serie (idarqueo_serie)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$this->db->table_exists('tb_cierre_depositos_pendientes')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS tb_cierre_depositos_pendientes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idcierre_caja INT NOT NULL,
                moneda_origen VARCHAR(10) NOT NULL,
                monto_arqueo DECIMAL(18,2) NOT NULL DEFAULT 0,
                estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
                moneda_destino VARCHAR(10) NULL,
                tc_tipo_aplicado VARCHAR(20) NULL,
                tasa_cambio DECIMAL(18,6) NULL,
                monto_depositado DECIMAL(18,2) NULL,
                monto_integrado DECIMAL(18,2) NULL,
                idcuenta_banco INT NULL,
                referencia_minuta VARCHAR(120) NULL,
                fecha_deposito DATE NULL,
                movimiento_id INT NULL,
                enviado_por INT NULL,
                integrado_por INT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NULL,
                UNIQUE KEY uq_cierre_deposito_moneda (idcierre_caja, moneda_origen),
                KEY idx_estado (estado),
                KEY idx_cierre (idcierre_caja)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if ($this->db->table_exists('tb_cierre_depositos_pendientes')) {
            if (!$this->db->field_exists('tc_tipo_aplicado', 'tb_cierre_depositos_pendientes')) {
                $this->db->query("ALTER TABLE tb_cierre_depositos_pendientes ADD COLUMN tc_tipo_aplicado VARCHAR(20) NULL");
            }
        }
    }

    private function _get_cierre_depositos_pendientes($filters = array())
    {
        $this->ensure_cierre_arqueo_tables();
        if (!$this->db->table_exists('tb_cierre_depositos_pendientes')) {
            return array();
        }

        $this->db->select('d.*, c.consecutivo AS cierre_consecutivo, a.name AS cuenta_nombre, a.bank_name AS banco_nombre, a.currency AS cuenta_moneda');
        $this->db->from('tb_cierre_depositos_pendientes d');
        $this->db->join('tb_cierres_caja c', 'c.id = d.idcierre_caja', 'left');
        if ($this->db->table_exists('teso_accounts')) {
            $this->db->join('teso_accounts a', 'a.id = d.idcuenta_banco', 'left');
        }

        if (isset($filters['cierre_id']) && intval($filters['cierre_id']) > 0) {
            $this->db->where('d.idcierre_caja', intval($filters['cierre_id']));
        }
        if (isset($filters['estado']) && $filters['estado'] !== '') {
            if (is_array($filters['estado'])) {
                $this->db->where_in('d.estado', $filters['estado']);
            } else {
                $this->db->where('d.estado', $filters['estado']);
            }
        }

        $this->db->order_by('d.created_at', 'DESC');
        $this->db->order_by('d.id', 'DESC');
        return $this->db->get()->result();
    }

    public function enviar_cierre_a_depositar_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_cierre_arqueo_tables();
        $cierre_id = intval($this->input->post('cierre_id'));
        if ($cierre_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Cierre inválido'));
            return;
        }

        $arqueo = $this->_get_cierre_arqueo($cierre_id);
        if (!$arqueo) {
            echo json_encode(array('status' => false, 'message' => 'Debe guardar primero el arqueo general'));
            return;
        }

        $seriesPendientes = $this->_get_series_pendientes_arqueo($cierre_id);
        if (!empty($seriesPendientes)) {
            echo json_encode(array('status' => false, 'message' => 'Faltan arqueos por serie: ' . implode(', ', $seriesPendientes)));
            return;
        }

        $usuario_id = null;
        try {
            if (method_exists($this->ion_auth, 'get_user_id')) {
                $tmpUserId = intval($this->ion_auth->get_user_id());
                $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
            }
        } catch (Exception $e) {
            $usuario_id = null;
        }

        $docs = array(
            'USD' => isset($arqueo['total_billetaje_usd']) ? floatval($arqueo['total_billetaje_usd']) : 0,
            'NIO' => isset($arqueo['total_billetaje_nio']) ? floatval($arqueo['total_billetaje_nio']) : 0,
        );

        $creados = 0;
        $omitidos = 0;
        $this->db->trans_begin();
        try {
            foreach ($docs as $moneda => $monto) {
                if ($monto <= 0) {
                    continue;
                }

                $existente = $this->db
                    ->where('idcierre_caja', $cierre_id)
                    ->where('moneda_origen', $moneda)
                    ->get('tb_cierre_depositos_pendientes')
                    ->row();
                if ($existente) {
                    $omitidos++;
                    continue;
                }

                $this->db->insert('tb_cierre_depositos_pendientes', array(
                    'idcierre_caja' => $cierre_id,
                    'moneda_origen' => $moneda,
                    'monto_arqueo' => round($monto, 2),
                    'estado' => 'pendiente',
                    'enviado_por' => $usuario_id,
                    'created_at' => date('Y-m-d H:i:s')
                ));
                $creados++;
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('No se pudieron generar los depósitos pendientes');
            }
            $this->db->trans_commit();

            echo json_encode(array(
                'status' => true,
                'message' => $creados > 0
                    ? 'Depósitos pendientes enviados a Integración Bancaria: ' . $creados
                    : 'Los depósitos de este cierre ya estaban enviados a Integración Bancaria',
                'depositos' => $this->_get_cierre_depositos_pendientes(array('cierre_id' => $cierre_id))
            ));
            return;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
            return;
        }
    }

    public function integrar_deposito_bancario_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_cierre_arqueo_tables();
        $deposito_id = intval($this->input->post('deposito_id'));
        $idbanco = intval($this->input->post('idcuenta_banco'));
        $monedaDestino = $this->_normalizar_moneda_cierre($this->input->post('moneda_destino'));
        $montoDepositado = floatval($this->input->post('monto_depositado'));
        $tasaCambio = floatval($this->input->post('tasa_cambio'));
        $referenciaMinuta = trim((string)$this->input->post('referencia_minuta'));
        $fechaDeposito = trim((string)$this->input->post('fecha_deposito'));

        if ($deposito_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Depósito inválido'));
            return;
        }
        if ($idbanco <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Debe seleccionar una cuenta bancaria'));
            return;
        }
        if ($montoDepositado <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Debe indicar el monto depositado'));
            return;
        }
        if ($referenciaMinuta === '' || $fechaDeposito === '') {
            echo json_encode(array('status' => false, 'message' => 'Debe indicar minuta y fecha de depósito'));
            return;
        }

        $deposito = $this->db->get_where('tb_cierre_depositos_pendientes', array('id' => $deposito_id))->row();
        if (!$deposito) {
            echo json_encode(array('status' => false, 'message' => 'No se encontró el depósito pendiente'));
            return;
        }
        if (strtolower(trim((string)$deposito->estado)) === 'integrado') {
            echo json_encode(array('status' => false, 'message' => 'Este depósito ya fue integrado anteriormente'));
            return;
        }

        $cuenta = $this->db->get_where('teso_accounts', array('id' => $idbanco))->row();
        if (!$cuenta) {
            echo json_encode(array('status' => false, 'message' => 'La cuenta bancaria seleccionada no existe'));
            return;
        }

        $monedaCuenta = $this->_normalizar_moneda_cierre(isset($cuenta->currency) ? $cuenta->currency : $monedaDestino);
        if ($monedaDestino === '') {
            $monedaDestino = $monedaCuenta;
        }

        $montoIntegrado = $montoDepositado;
        $tcTipoAplicado = null;
        $monedaOrigen = $this->_normalizar_moneda_cierre(isset($deposito->moneda_origen) ? $deposito->moneda_origen : 'USD');
        if ($monedaOrigen === $monedaDestino) {
            $tcTipoAplicado = 'misma_moneda';
            $tasaCambio = null;
        } elseif ($monedaOrigen === 'NIO' && $monedaDestino === 'USD') {
            // Regla operativa: cuando el origen es NIO y destino USD se usa TC Venta.
            if ($tasaCambio <= 0) {
                echo json_encode(array('status' => false, 'message' => 'Debe indicar TC Venta para convertir de NIO a USD'));
                return;
            }
            $tcTipoAplicado = 'venta';
            $montoIntegrado = round($montoDepositado / $tasaCambio, 2);
        } elseif ($monedaOrigen === 'USD' && $monedaDestino === 'NIO') {
            // Regla operativa: cuando el origen es USD y destino NIO se usa TC Compra.
            if ($tasaCambio <= 0) {
                echo json_encode(array('status' => false, 'message' => 'Debe indicar TC Compra para convertir de USD a NIO'));
                return;
            }
            $tcTipoAplicado = 'compra';
            $montoIntegrado = round($montoDepositado * $tasaCambio, 2);
        } else {
            echo json_encode(array('status' => false, 'message' => 'Combinación de monedas no soportada para integración'));
            return;
        }

        $usuario_id = null;
        try {
            if (method_exists($this->ion_auth, 'get_user_id')) {
                $tmpUserId = intval($this->ion_auth->get_user_id());
                $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
            }
        } catch (Exception $e) {
            $usuario_id = null;
        }
        $usuarioTxt = $this->session->userdata('username');
        if (!$usuarioTxt) {
            $usuarioTxt = $usuario_id ? ('user_' . $usuario_id) : 'sistema';
        }

        $this->db->trans_begin();
        try {
            $mov = array(
                'tipo_movimiento' => 'transferencia',
                'concepto' => 'Deposito bancario pendiente cierre #' . intval($deposito->idcierre_caja) . ' ' . $monedaOrigen,
                'forma_pago' => 'DEPOSITO',
                'fecha_registro' => $fechaDeposito,
                'fecha_aplicacion' => $fechaDeposito,
                'beneficiario' => 'TESORERIA',
                'referencia1' => $referenciaMinuta,
                'referencia2' => 'deposito_pendiente_id=' . $deposito_id,
                'monto_total' => $montoIntegrado,
                'iva_total' => 0,
                'descripcion' => 'Integracion bancaria deposito arqueo cierre #' . intval($deposito->idcierre_caja) . ' ' . $monedaOrigen . ' a ' . $monedaDestino,
                'cuenta_id' => $idbanco,
                'tipo_transferencia' => 'abono',
                'estado' => 'activo',
                'creado_por' => $usuarioTxt,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('teso_movimientos', $mov);
            $movId = intval($this->db->insert_id());

            if ($this->db->field_exists('total_abonos', 'teso_accounts')) {
                $this->db->set('total_abonos', 'COALESCE(total_abonos,0)+' . $this->db->escape($montoIntegrado), false);
            }
            if ($this->db->field_exists('saldo_actual', 'teso_accounts')) {
                $this->db->set('saldo_actual', 'COALESCE(saldo_actual,0)+' . $this->db->escape($montoIntegrado), false);
            }
            $this->db->where('id', $idbanco)->update('teso_accounts');

            $this->db->where('id', $deposito_id)->update('tb_cierre_depositos_pendientes', array(
                'estado' => 'integrado',
                'moneda_destino' => $monedaDestino,
                'tc_tipo_aplicado' => $tcTipoAplicado,
                'tasa_cambio' => $tasaCambio > 0 ? $tasaCambio : null,
                'monto_depositado' => $montoDepositado,
                'monto_integrado' => $montoIntegrado,
                'idcuenta_banco' => $idbanco,
                'referencia_minuta' => $referenciaMinuta,
                'fecha_deposito' => $fechaDeposito,
                'movimiento_id' => $movId,
                'integrado_por' => $usuario_id,
                'updated_at' => date('Y-m-d H:i:s')
            ));

            if ($this->db->trans_status() === false) {
                throw new Exception('No se pudo integrar el depósito bancario');
            }
            $this->db->trans_commit();

            echo json_encode(array('status' => true, 'message' => 'Depósito integrado al banco correctamente'));
            return;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
            return;
        }
    }

    private function _normalizar_moneda_cierre($raw)
    {
        $m = strtoupper(trim((string)$raw));
        if (in_array($m, array('NIO', 'NIO$', 'CS', 'C$', 'CRC', 'CORDOBA', 'CORDOBAS'))) {
            return 'NIO';
        }
        return 'USD';
    }

    private function _get_cierre_totales_moneda($cierre_id)
    {
        $totals = array('USD' => 0.0, 'NIO' => 0.0);
        if (!$this->db->table_exists('teso_pagos')) {
            return $totals;
        }

        $rows = $this->db->where('idcierre_caja', intval($cierre_id))->get('teso_pagos')->result();
        foreach ($rows as $r) {
            $estado = isset($r->estado) ? strtolower(trim((string)$r->estado)) : '';
            if ($estado === 'anulado') {
                continue;
            }
            $monto = isset($r->monto_recibido) && $r->monto_recibido !== null ? floatval($r->monto_recibido) : floatval(isset($r->monto) ? $r->monto : 0);
            $mon = $this->_normalizar_moneda_cierre(isset($r->moneda) ? $r->moneda : 'USD');
            $totals[$mon] += $monto;
        }
        return $totals;
    }

    private function _get_cierre_arqueo($cierre_id)
    {
        $this->ensure_cierre_arqueo_tables();
        $row = $this->db->get_where('tb_cierre_arqueos', array('idcierre_caja' => intval($cierre_id)))->row_array();
        if (!$row) {
            return null;
        }
        $detalles = $this->db->where('idarqueo', intval($row['id']))->order_by('moneda, denominacion', 'ASC')->get('tb_cierre_arqueo_detalle')->result_array();
        $row['detalles'] = $detalles;
        return $row;
    }

    private function _detectar_serie_pago($pago)
    {
        $serie = '';
        if (isset($pago->serie_codigo) && trim((string)$pago->serie_codigo) !== '') {
            $serie = strtoupper(trim((string)$pago->serie_codigo));
        }
        if ($serie === '' && isset($pago->documento_numero)) {
            $ref = trim((string)$pago->documento_numero);
            if ($ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $m)) {
                $serie = strtoupper($m[1]);
            }
        }
        return $serie !== '' ? $serie : 'SIN_SERIE';
    }

    private function _get_cierre_totales_moneda_serie($cierre_id, $serie_codigo)
    {
        $totals = array('USD' => 0.0, 'NIO' => 0.0);
        if (!$this->db->table_exists('teso_pagos')) {
            return $totals;
        }

        $rows = $this->db->where('idcierre_caja', intval($cierre_id))->get('teso_pagos')->result();
        $serieFiltro = strtoupper(trim((string)$serie_codigo));
        foreach ($rows as $r) {
            $serieRow = $this->_detectar_serie_pago($r);
            if ($serieRow !== $serieFiltro) {
                continue;
            }

            $estado = isset($r->estado) ? strtolower(trim((string)$r->estado)) : '';
            if ($estado === 'anulado') {
                continue;
            }
            $monto = isset($r->monto_recibido) && $r->monto_recibido !== null ? floatval($r->monto_recibido) : floatval(isset($r->monto) ? $r->monto : 0);
            $mon = $this->_normalizar_moneda_cierre(isset($r->moneda) ? $r->moneda : 'USD');
            $totals[$mon] += $monto;
        }
        return $totals;
    }

    private function _get_cierre_arqueos_series($cierre_id)
    {
        $this->ensure_cierre_arqueo_tables();
        if (!$this->db->table_exists('tb_cierre_arqueos_series')) {
            return array();
        }

        $rows = $this->db
            ->where('idcierre_caja', intval($cierre_id))
            ->order_by('serie_codigo', 'ASC')
            ->get('tb_cierre_arqueos_series')
            ->result_array();

        if (empty($rows)) {
            return array();
        }

        $map = array();
        foreach ($rows as $r) {
            $idArqSerie = isset($r['id']) ? intval($r['id']) : 0;
            $det = array();
            if ($idArqSerie > 0) {
                $det = $this->db
                    ->where('idarqueo_serie', $idArqSerie)
                    ->order_by('moneda, denominacion', 'ASC')
                    ->get('tb_cierre_arqueo_serie_detalle')
                    ->result_array();
            }
            $r['detalles'] = $det;
            $serieKey = isset($r['serie_codigo']) ? strtoupper(trim((string)$r['serie_codigo'])) : '';
            if ($serieKey !== '') {
                $map[$serieKey] = $r;
            }
        }
        return $map;
    }

    private function _get_series_requeridas_cierre($cierre_id)
    {
        $out = array();
        if (!$this->db->table_exists('teso_pagos')) {
            return $out;
        }

        $rows = $this->db->where('idcierre_caja', intval($cierre_id))->get('teso_pagos')->result();
        foreach ($rows as $r) {
            $estado = isset($r->estado) ? strtolower(trim((string)$r->estado)) : '';
            if ($estado === 'anulado') {
                continue;
            }
            $serie = $this->_detectar_serie_pago($r);
            $out[$serie] = true;
        }
        $series = array_keys($out);
        sort($series);
        return $series;
    }

    private function _get_series_pendientes_arqueo($cierre_id)
    {
        $requeridas = $this->_get_series_requeridas_cierre($cierre_id);
        if (empty($requeridas)) {
            return array();
        }
        $guardadasMap = $this->_get_cierre_arqueos_series($cierre_id);
        $pendientes = array();
        foreach ($requeridas as $s) {
            if (!isset($guardadasMap[$s])) {
                $pendientes[] = $s;
            }
        }
        return $pendientes;
    }

    public function save_cierre_arqueo_serie_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_cierre_arqueo_tables();
        $cierre_id = intval($this->input->post('cierre_id'));
        $serie = strtoupper(trim((string)$this->input->post('serie_codigo')));
        if ($cierre_id <= 0 || $serie === '') {
            echo json_encode(array('status' => false, 'message' => 'Datos de cierre o serie inválidos'));
            return;
        }

        $cierre = $this->db->get_where('tb_cierres_caja', array('id' => $cierre_id))->row();
        if (!$cierre) {
            echo json_encode(array('status' => false, 'message' => 'No se encontró el cierre'));
            return;
        }

        $decodeItems = function ($raw) {
            if (is_string($raw)) {
                $arr = json_decode($raw, true);
                return is_array($arr) ? $arr : array();
            }
            return is_array($raw) ? $raw : array();
        };

        $billetajeUSD = $decodeItems($this->input->post('billetaje_usd'));
        $billetajeNIO = $decodeItems($this->input->post('billetaje_nio'));
        $comentario = trim((string)$this->input->post('comentario_diferencia'));
        $editAutorizadoPor = trim((string)$this->input->post('edit_autorizado_por'));
        $editComentario = trim((string)$this->input->post('edit_comentario'));

        $sumBilletaje = function ($rows, $moneda) {
            $items = array();
            $total = 0.0;
            if (!is_array($rows)) {
                return array($items, $total);
            }
            foreach ($rows as $r) {
                $den = isset($r['denominacion']) ? floatval($r['denominacion']) : 0;
                $cant = isset($r['cantidad']) ? intval($r['cantidad']) : 0;
                if ($den <= 0 || $cant < 0) {
                    continue;
                }
                if ($cant === 0) {
                    continue;
                }
                $monto = $den * $cant;
                $items[] = array(
                    'moneda' => $moneda,
                    'denominacion' => $den,
                    'cantidad' => $cant,
                    'monto' => $monto
                );
                $total += $monto;
            }
            return array($items, $total);
        };

        list($itemsUSD, $totalBilletajeUSD) = $sumBilletaje($billetajeUSD, 'USD');
        list($itemsNIO, $totalBilletajeNIO) = $sumBilletaje($billetajeNIO, 'NIO');
        $allItems = array_merge($itemsUSD, $itemsNIO);

        $totCierreSerie = $this->_get_cierre_totales_moneda_serie($cierre_id, $serie);
        $difUSD = round($totalBilletajeUSD - floatval($totCierreSerie['USD']), 2);
        $difNIO = round($totalBilletajeNIO - floatval($totCierreSerie['NIO']), 2);

        if (($difUSD != 0.0 || $difNIO != 0.0) && $comentario === '') {
            echo json_encode(array('status' => false, 'message' => 'Debe ingresar comentario cuando hay faltante o excedente en arqueo de serie'));
            return;
        }

        $usuario_id = null;
        try {
            if (method_exists($this->ion_auth, 'get_user_id')) {
                $tmpUserId = intval($this->ion_auth->get_user_id());
                $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
            }
        } catch (Exception $e) {
            $usuario_id = null;
        }

        $existing = $this->db
            ->where('idcierre_caja', $cierre_id)
            ->where('serie_codigo', $serie)
            ->get('tb_cierre_arqueos_series')
            ->row_array();

        $isEditing = ($existing && isset($existing['id']));
        if ($isEditing && ($editAutorizadoPor === '' || $editComentario === '')) {
            echo json_encode(array('status' => false, 'message' => 'Para editar un arqueo de serie ya guardado debe indicar quién autoriza y comentario obligatorio'));
            return;
        }

        $this->db->trans_begin();
        try {
            $dataArqueoSerie = array(
                'idcierre_caja' => $cierre_id,
                'serie_codigo' => $serie,
                'monto_cierre_usd' => floatval($totCierreSerie['USD']),
                'monto_cierre_nio' => floatval($totCierreSerie['NIO']),
                'total_billetaje_usd' => $totalBilletajeUSD,
                'total_billetaje_nio' => $totalBilletajeNIO,
                'diferencia_usd' => $difUSD,
                'diferencia_nio' => $difNIO,
                'comentario_diferencia' => $comentario !== '' ? $comentario : null,
                'edit_autorizado_por' => $isEditing ? $editAutorizadoPor : null,
                'edit_comentario' => $isEditing ? $editComentario : null,
                'idusuario' => $usuario_id,
                'updated_at' => date('Y-m-d H:i:s')
            );

            if ($existing && isset($existing['id'])) {
                $idarqueoSerie = intval($existing['id']);
                $dataArqueoSerie['edit_count'] = intval(isset($existing['edit_count']) ? $existing['edit_count'] : 0) + 1;
                $this->db->where('id', $idarqueoSerie)->update('tb_cierre_arqueos_series', $dataArqueoSerie);
            } else {
                $dataArqueoSerie['edit_count'] = 0;
                $dataArqueoSerie['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_cierre_arqueos_series', $dataArqueoSerie);
                $idarqueoSerie = intval($this->db->insert_id());
            }

            $this->db->where('idarqueo_serie', $idarqueoSerie)->delete('tb_cierre_arqueo_serie_detalle');
            foreach ($allItems as $it) {
                $ins = $it;
                $ins['idarqueo_serie'] = $idarqueoSerie;
                $ins['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_cierre_arqueo_serie_detalle', $ins);
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('No se pudo guardar el arqueo por serie');
            }
            $this->db->trans_commit();

            echo json_encode(array(
                'status' => true,
                'message' => 'Arqueo de serie ' . $serie . ' guardado correctamente',
                'serie_codigo' => $serie,
                'arqueos_series' => $this->_get_cierre_arqueos_series($cierre_id)
            ));
            return;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
            return;
        }
    }

    public function save_cierre_arqueo_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_cierre_arqueo_tables();
        $cierre_id = intval($this->input->post('cierre_id'));
        if ($cierre_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Cierre inválido'));
            return;
        }

        $cierre = $this->db->get_where('tb_cierres_caja', array('id' => $cierre_id))->row();
        if (!$cierre) {
            echo json_encode(array('status' => false, 'message' => 'No se encontró el cierre'));
            return;
        }

        $seriesPendientes = $this->_get_series_pendientes_arqueo($cierre_id);
        if (!empty($seriesPendientes)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Debe completar todos los arqueos por serie antes del arqueo general. Pendientes: ' . implode(', ', $seriesPendientes)
            ));
            return;
        }

        $decodeItems = function ($raw) {
            if (is_string($raw)) {
                $arr = json_decode($raw, true);
                return is_array($arr) ? $arr : array();
            }
            return is_array($raw) ? $raw : array();
        };

        $billetajeUSD = $decodeItems($this->input->post('billetaje_usd'));
        $billetajeNIO = $decodeItems($this->input->post('billetaje_nio'));
        $comentario = trim((string)$this->input->post('comentario_diferencia'));

        $sumBilletaje = function ($rows, $moneda) {
            $items = array();
            $total = 0.0;
            if (!is_array($rows)) {
                return array($items, $total);
            }
            foreach ($rows as $r) {
                $den = isset($r['denominacion']) ? floatval($r['denominacion']) : 0;
                $cant = isset($r['cantidad']) ? intval($r['cantidad']) : 0;
                if ($den <= 0 || $cant < 0) {
                    continue;
                }
                if ($cant === 0) {
                    continue;
                }
                $monto = $den * $cant;
                $items[] = array(
                    'moneda' => $moneda,
                    'denominacion' => $den,
                    'cantidad' => $cant,
                    'monto' => $monto
                );
                $total += $monto;
            }
            return array($items, $total);
        };

        list($itemsUSD, $totalBilletajeUSD) = $sumBilletaje($billetajeUSD, 'USD');
        list($itemsNIO, $totalBilletajeNIO) = $sumBilletaje($billetajeNIO, 'NIO');
        $allItems = array_merge($itemsUSD, $itemsNIO);

        $totCierre = $this->_get_cierre_totales_moneda($cierre_id);
        $difUSD = round($totalBilletajeUSD - floatval($totCierre['USD']), 2);
        $difNIO = round($totalBilletajeNIO - floatval($totCierre['NIO']), 2);

        if (($difUSD != 0.0 || $difNIO != 0.0) && $comentario === '') {
            echo json_encode(array('status' => false, 'message' => 'Debe ingresar comentario cuando hay faltante o excedente en arqueo'));
            return;
        }

        $existing = $this->_get_cierre_arqueo($cierre_id);

        $usuario_id = null;
        try {
            if (method_exists($this->ion_auth, 'get_user_id')) {
                $tmpUserId = intval($this->ion_auth->get_user_id());
                $usuario_id = $tmpUserId > 0 ? $tmpUserId : null;
            }
        } catch (Exception $e) {
            $usuario_id = null;
        }

        $this->db->trans_begin();
        try {
            $dataArqueo = array(
                'idcierre_caja' => $cierre_id,
                'monto_cierre_usd' => floatval($totCierre['USD']),
                'monto_cierre_nio' => floatval($totCierre['NIO']),
                'total_billetaje_usd' => $totalBilletajeUSD,
                'total_billetaje_nio' => $totalBilletajeNIO,
                'diferencia_usd' => $difUSD,
                'diferencia_nio' => $difNIO,
                'comentario_diferencia' => $comentario !== '' ? $comentario : null,
                'idbanco' => null,
                'estado_deposito' => 'pendiente',
                'monto_depositado_total' => null,
                'referencia_minuta' => null,
                'fecha_deposito' => null,
                'deposito_movimiento_id' => null,
                'idusuario' => $usuario_id,
                'updated_at' => date('Y-m-d H:i:s')
            );

            if ($existing && isset($existing['id'])) {
                $idarqueo = intval($existing['id']);
                $this->db->where('id', $idarqueo)->update('tb_cierre_arqueos', $dataArqueo);
            } else {
                $dataArqueo['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_cierre_arqueos', $dataArqueo);
                $idarqueo = intval($this->db->insert_id());
            }

            $this->db->where('idarqueo', $idarqueo)->delete('tb_cierre_arqueo_detalle');
            foreach ($allItems as $it) {
                $ins = $it;
                $ins['idarqueo'] = $idarqueo;
                $ins['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_cierre_arqueo_detalle', $ins);
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('No se pudo guardar el arqueo');
            }
            $this->db->trans_commit();

            echo json_encode(array(
                'status' => true,
                'message' => 'Arqueo guardado. Ahora puede enviarlo a depositar desde Integración Bancaria',
                'arqueo' => $this->_get_cierre_arqueo($cierre_id)
            ));
            return;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
            return;
        }
    }

    public function guardar_recepcion_pagos_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $this->ensure_teso_pagos_recepcion_columns();
        $raw = $this->input->post('items');
        if (is_string($raw)) {
            $items = json_decode($raw, true);
        } else {
            $items = $raw;
        }

        if (!is_array($items) || empty($items)) {
            echo json_encode(array('status' => false, 'message' => 'No hay datos para guardar'));
            return;
        }

        $okCount = 0;
        $alreadyCount = 0;
        $errors = array();
        $pagos_procesados = array();
        $monto_total = 0;
        $tasa_compra = floatval($this->input->post('tasa_compra'));
        $tasa_venta = floatval($this->input->post('tasa_venta'));

        foreach ($items as $it) {
            $id = isset($it['id']) ? intval($it['id']) : 0;
            $monto_recibido = isset($it['monto_recibido']) ? floatval($it['monto_recibido']) : 0;
            $monto_usd_recibido = isset($it['monto_usd_recibido']) ? floatval($it['monto_usd_recibido']) : null;
            $monto_nio_recibido = isset($it['monto_nio_recibido']) ? floatval($it['monto_nio_recibido']) : null;
            $fecha_recepcion = isset($it['fecha_recepcion']) ? trim((string)$it['fecha_recepcion']) : '';
            if ($id <= 0) continue;
            if ($monto_recibido <= 0 || empty($fecha_recepcion)) {
                continue;
            }

            $result = $this->_aplicar_pago_provisional(
                $id,
                $monto_recibido,
                $fecha_recepcion,
                $tasa_venta,
                $tasa_compra,
                $monto_usd_recibido,
                $monto_nio_recibido
            );
            if (!empty($result['status'])) {
                if (!empty($result['already_processed'])) {
                    $alreadyCount++;
                } else {
                    $okCount++;
                    $pagos_procesados[] = $id;
                    $monto_total += $monto_recibido;
                }
            } else {
                $errors[] = 'Pago #' . $id . ': ' . (isset($result['message']) ? $result['message'] : 'No se pudo procesar');
            }
        }

        $cierre_id = null;
        if ($okCount > 0) {
            $cierre_id = $this->_create_cierre_caja($pagos_procesados, $monto_total);
        }

        $statusFinal = ($okCount > 0 || $alreadyCount > 0);
        $messageFinal = 'No se pudo procesar ningún pago';
        if ($okCount > 0) {
            $messageFinal = 'Pagos procesados: ' . $okCount . ' - Cierre #' . ($cierre_id ? $cierre_id : '') . ' generado';
            if ($alreadyCount > 0) {
                $messageFinal .= ' | Ya procesados omitidos: ' . $alreadyCount;
            }
        } elseif ($alreadyCount > 0) {
            $messageFinal = 'No había pagos nuevos por aplicar. Ya procesados omitidos: ' . $alreadyCount;
        }

        echo json_encode(array(
            'status' => $statusFinal,
            'message' => $messageFinal,
            'updated' => $okCount,
            'already_processed' => $alreadyCount,
            'cierre_id' => $cierre_id,
            'errors' => $errors
        ));
    }

    private function _get_arqueos_pagos_data($fecha, $modo = '', $q = '')
    {
        $this->ensure_teso_pagos_recepcion_columns();

        $this->db->from('teso_pagos');
        if ($this->db->field_exists('fecha_recepcion', 'teso_pagos')) {
            $this->db->where('fecha_recepcion', $fecha);
        } elseif ($this->db->field_exists('fecha', 'teso_pagos')) {
            $this->db->where('fecha', $fecha);
        }
        $this->db->group_start();
        $this->db->where('estado', 'aplicado_pendiente_arqueo');
        $this->db->or_where('estado', 'anulado');
        $this->db->group_end();
        $this->db->order_by('id', 'ASC');
        $rows = $this->db->get()->result();

        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $rows = array_values(array_filter($rows, function ($row) use ($qLower) {
                $hay = array(
                    isset($row->beneficiario) ? mb_strtolower((string)$row->beneficiario) : '',
                    isset($row->concepto) ? mb_strtolower((string)$row->concepto) : '',
                    isset($row->documento_numero) ? mb_strtolower((string)$row->documento_numero) : '',
                    isset($row->estado) ? mb_strtolower((string)$row->estado) : ''
                );
                foreach ($hay as $txt) {
                    if ($txt !== '' && strpos($txt, $qLower) !== false) return true;
                }
                return false;
            }));
        }

        $totales = array(
            'aplicado' => 0,
            'anulado' => 0,
            'general' => 0,
            'aplicados_count' => 0,
            'anulados_count' => 0
        );
        $grupos = array();
        foreach ($rows as $row) {
            $montoRow = isset($row->monto) ? floatval($row->monto) : 0;
            $totales['general'] += $montoRow;
            $estadoRow = isset($row->estado) ? strtolower(trim($row->estado)) : '';
            if ($estadoRow === 'aplicado_pendiente_arqueo') {
                $totales['aplicado'] += $montoRow;
                $totales['aplicados_count']++;
            } elseif ($estadoRow === 'anulado') {
                $totales['anulado'] += $montoRow;
                $totales['anulados_count']++;
            }

            $ref = isset($row->documento_numero) ? trim((string)$row->documento_numero) : '';
            $serie = !empty($row->serie_codigo) ? strtoupper(trim((string)$row->serie_codigo)) : 'SIN SERIE';
            if ($serie === 'SIN SERIE' && $ref !== '' && preg_match('/^([A-Za-z]+)/', $ref, $m)) {
                $serie = strtoupper($m[1]);
            }
            if (!isset($grupos[$serie])) $grupos[$serie] = array();
            $grupos[$serie][] = $row;
        }

        ksort($grupos);

        return array(
            'fecha' => $fecha,
            'modo' => $modo,
            'filtro_q' => $q,
            'rows' => $rows,
            'grupos' => $grupos,
            'totales_reporte' => $totales
        );
    }

    // AJAX: rechazar un pago provisional pendiente
    public function rechazar_pago_provisional_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $pago_id = intval($this->input->post('pago_id'));
        $motivo = trim((string)$this->input->post('motivo'));
        if ($pago_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Pago no válido'));
            return;
        }
        if ($motivo === '') {
            echo json_encode(array('status' => false, 'message' => 'El comentario es obligatorio para anular el pago'));
            return;
        }

        $pago = $this->db->get_where('teso_pagos', array('id' => $pago_id))->row();
        if (!$pago) {
            echo json_encode(array('status' => false, 'message' => 'Pago provisional no encontrado'));
            return;
        }

        $estado = isset($pago->estado) ? strtolower(trim($pago->estado)) : '';
        if (!in_array($estado, array('registrado', 'programado', 'pendiente'))) {
            echo json_encode(array('status' => false, 'message' => 'Solo se pueden anular pagos pendientes'));
            return;
        }

        $up = array('estado' => 'anulado');
        if ($this->db->field_exists('updated_at', 'teso_pagos')) {
            $up['updated_at'] = date('Y-m-d H:i:s');
        }
        if ($this->db->field_exists('motivo_rechazo', 'teso_pagos')) {
            $up['motivo_rechazo'] = $motivo;
        }
        if ($this->db->field_exists('dato_adicional', 'teso_pagos') && !empty($motivo)) {
            $prev = isset($pago->dato_adicional) ? (string)$pago->dato_adicional : '';
            $up['dato_adicional'] = trim($prev . ' | Rechazado: ' . $motivo);
        }

        $this->db->where('id', $pago_id);
        $ok = $this->db->update('teso_pagos', $up);
        if (!$ok) {
            $err = $this->db->error();
                echo json_encode(array('status' => false, 'message' => isset($err['message']) ? $err['message'] : 'No se pudo anular'));
            return;
        }

        echo json_encode(array('status' => true, 'message' => 'Pago provisional anulado'));
    }

    // AJAX: modificar datos base del provisional antes de aprobar
    public function modificar_pago_provisional_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $pago_id = intval($this->input->post('pago_id'));
        $monto = floatval($this->input->post('monto'));
        $referencia = trim((string)$this->input->post('referencia'));
        $metodo = strtolower(trim((string)$this->input->post('metodo')));

        if ($pago_id <= 0 || $monto <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Datos inválidos para modificar'));
            return;
        }

        $pago = $this->db->get_where('teso_pagos', array('id' => $pago_id))->row();
        if (!$pago) {
            echo json_encode(array('status' => false, 'message' => 'Pago provisional no encontrado'));
            return;
        }

        $estado = isset($pago->estado) ? strtolower(trim($pago->estado)) : '';
        if (!in_array($estado, array('registrado', 'programado', 'pendiente'))) {
            echo json_encode(array('status' => false, 'message' => 'Solo se pueden modificar pagos pendientes'));
            return;
        }

        if (!in_array($metodo, array('transferencia', 'cheque', 'efectivo', 'tarjeta'))) {
            $metodo = isset($pago->medio_pago) ? $pago->medio_pago : 'efectivo';
        }

        $up = array(
            'monto' => $monto,
            'medio_pago' => $metodo,
            'documento_numero' => $referencia
        );
        if ($this->db->field_exists('updated_at', 'teso_pagos')) {
            $up['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $pago_id);
        $ok = $this->db->update('teso_pagos', $up);
        if (!$ok) {
            $err = $this->db->error();
            echo json_encode(array('status' => false, 'message' => isset($err['message']) ? $err['message'] : 'No se pudo modificar'));
            return;
        }

        echo json_encode(array('status' => true, 'message' => 'Pago provisional actualizado'));
    }

    private function _aplicar_pago_provisional($pago_id, $monto_recibido_post, $fecha_recepcion_post, $tasa_venta_input = 0, $tasa_compra_input = 0, $monto_usd_recibido_input = null, $monto_nio_recibido_input = null)
    {
        $this->ensure_teso_pagos_recepcion_columns();
        $this->ensure_tb_prestamo_pagos_distribution_columns();

        $pago = $this->db->get_where('teso_pagos', array('id' => intval($pago_id)))->row();
        if (!$pago) {
            return array('status' => false, 'message' => 'Pago provisional no encontrado');
        }

        $estado = isset($pago->estado) ? strtolower(trim($pago->estado)) : '';
        if (!in_array($estado, array('registrado', 'programado', 'pendiente'))) {
            if (in_array($estado, array('aplicado_pendiente_arqueo', 'aplicado', 'cerrado'))) {
                return array('status' => true, 'already_processed' => true, 'message' => 'Pago ya estaba procesado');
            }
            return array('status' => false, 'message' => 'Este pago no está en estado pendiente para aplicar');
        }

        $monto_recibido_post = floatval($monto_recibido_post);
        $fecha_recepcion_post = trim((string)$fecha_recepcion_post);
        if ($monto_recibido_post <= 0 || $fecha_recepcion_post === '') {
            return array('status' => false, 'message' => 'Debe registrar monto recibido y fecha de recepción antes de aprobar');
        }

        $idprestamo = null;
        if ($this->db->field_exists('idprestamo', 'teso_pagos') && isset($pago->idprestamo) && intval($pago->idprestamo) > 0) {
            $idprestamo = intval($pago->idprestamo);
        } elseif (!empty($pago->concepto) && preg_match('/prestamo\s*#\s*(\d+)/i', $pago->concepto, $m)) {
            $idprestamo = intval($m[1]);
        }

        $idcuota = null;
        if ($this->db->field_exists('idcuota', 'teso_pagos') && isset($pago->idcuota) && intval($pago->idcuota) > 0) {
            $idcuota = intval($pago->idcuota);
        } elseif (!empty($pago->concepto) && preg_match('/cuota\s*#\s*(\d+)/i', $pago->concepto, $m2)) {
            $idcuota = intval($m2[1]);
        }

        if (!$idprestamo || !$idcuota) {
            $resolved = $this->Tesoreria_model->resolve_provisional_match($pago);
            if ($resolved) {
                if (!$idprestamo && !empty($resolved['idprestamo'])) $idprestamo = intval($resolved['idprestamo']);
                if (!$idcuota && !empty($resolved['idcuota'])) $idcuota = intval($resolved['idcuota']);

                $backfill = array();
                if ($this->db->field_exists('idprestamo', 'teso_pagos') && !empty($resolved['idprestamo'])) $backfill['idprestamo'] = intval($resolved['idprestamo']);
                if ($this->db->field_exists('idcuota', 'teso_pagos') && !empty($resolved['idcuota'])) $backfill['idcuota'] = intval($resolved['idcuota']);
                if ($this->db->field_exists('idcliente', 'teso_pagos') && !empty($resolved['idcliente'])) $backfill['idcliente'] = intval($resolved['idcliente']);
                if ($this->db->field_exists('concepto', 'teso_pagos') && empty($pago->concepto)) {
                    $backfill['concepto'] = 'Pago provisional prestamo #' . intval($resolved['idprestamo']) . ' cuota #' . intval($resolved['idcuota']);
                }
                if (!empty($backfill)) {
                    $this->db->where('id', intval($pago_id));
                    $this->db->update('teso_pagos', $backfill);
                    foreach ($backfill as $k => $v) {
                        $pago->$k = $v;
                    }
                }
            }
        }

        if (!$idprestamo || !$idcuota) {
            return array('status' => false, 'message' => 'El provisional no tiene préstamo/cuota para aplicar');
        }

        $cliente_id = null;
        if ($this->db->field_exists('idcliente', 'teso_pagos') && isset($pago->idcliente) && intval($pago->idcliente) > 0) {
            $cliente_id = intval($pago->idcliente);
        } else {
            if ($this->db->field_exists('idcliente', 'tb_prestamos')) {
                $prestamo = $this->db->select('idcliente')->get_where('tb_prestamos', array('idprestamo' => $idprestamo))->row();
                if ($prestamo && isset($prestamo->idcliente)) {
                    $cliente_id = intval($prestamo->idcliente);
                }
            }
            if (!$cliente_id && $this->db->field_exists('idsolicitud', 'tb_prestamos') && $this->db->table_exists('tb_solicitudes') && $this->db->field_exists('idcliente', 'tb_solicitudes')) {
                $prestamo = $this->db->select('s.idcliente')
                    ->from('tb_prestamos pr')
                    ->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left')
                    ->where('pr.idprestamo', $idprestamo)
                    ->limit(1)
                    ->get()
                    ->row();
                if ($prestamo && isset($prestamo->idcliente)) {
                    $cliente_id = intval($prestamo->idcliente);
                }
            }
        }

        $posted_monto_original = isset($pago->monto) ? floatval($pago->monto) : 0;
        $monto = $posted_monto_original;
        $posted_monto_usd = (isset($pago->monto_usd) && $pago->monto_usd !== null) ? floatval($pago->monto_usd) : 0.0;
        $posted_monto_nio = (isset($pago->monto_nio) && $pago->monto_nio !== null) ? floatval($pago->monto_nio) : 0.0;
        if ($monto_usd_recibido_input !== null || $monto_nio_recibido_input !== null) {
            $posted_monto_usd = max(0, floatval($monto_usd_recibido_input));
            $posted_monto_nio = max(0, floatval($monto_nio_recibido_input));
        }
        if ($monto <= 0) {
            return array('status' => false, 'message' => 'El monto del pago provisional es inválido');
        }

        if (abs($monto - $monto_recibido_post) >= 0.01) {
            return array('status' => false, 'message' => 'El monto recibido no cuadra con el monto del pago. No se puede aprobar.');
        }

        $metodo = isset($pago->medio_pago) ? strtolower(trim($pago->medio_pago)) : 'efectivo';
        $moneda = isset($pago->moneda) ? strtoupper(trim($pago->moneda)) : 'USD';
        if ($metodo === 'transferencia' && $moneda !== 'USD') {
            return array('status' => false, 'message' => 'Transferencias en NIO no permitidas. Debe registrarse en USD.');
        }
        $referencia = isset($pago->documento_numero) ? $pago->documento_numero : null;
        $dato_adicional = ($this->db->field_exists('dato_adicional', 'teso_pagos') && isset($pago->dato_adicional)) ? $pago->dato_adicional : null;
        $fecha_pago_val = !empty($pago->fecha) ? (date('Y-m-d H:i:s', strtotime($pago->fecha)) ?: date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        $idserie_val = null;
        if ($this->db->field_exists('idserie', 'teso_pagos') && isset($pago->idserie) && intval($pago->idserie) > 0) {
            $idserie_val = intval($pago->idserie);
        }
        if ($referencia !== null && $referencia !== '' && ctype_digit((string)$referencia)) {
            $idserie_val = intval($referencia);
        }
        $assigned_referencia_formatted = null;

        $this->db->trans_begin();
        try {
            if (!is_null($idserie_val)) {
                $sr = $this->db->query('SELECT * FROM tb_series_recibos WHERE idserie = ? FOR UPDATE', array($idserie_val))->row();
                if ($sr) {
                    $code = isset($sr->codigo) ? $sr->codigo : '';
                    $refRaw = trim((string)$referencia);
                    if ($refRaw !== '' && $code !== '' && preg_match('/^' . preg_quote($code, '/') . '\\d{10}$/i', $refRaw)) {
                        // Ya trae referencia final reservada desde registro provisional.
                        $assigned_referencia_formatted = $refRaw;
                    } else {
                        $current = isset($sr->consecutivo) ? intval($sr->consecutivo) : 0;
                        $next = $current + 1;
                        $this->db->where('idserie', $idserie_val);
                        $this->db->update('tb_series_recibos', array('consecutivo' => $next, 'ultimo_emitido' => $next, 'updated_on' => time()));
                        $assigned_referencia_formatted = $code . str_pad($next, 10, '0', STR_PAD_LEFT);
                    }
                }
            }

            $tasaCompraUsada = floatval($tasa_compra_input);
            $tasaVentaUsada = floatval($tasa_venta_input);
            if ($tasaCompraUsada <= 0 || $tasaVentaUsada <= 0) {
                $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
                if ($tasaRow) {
                    if ($tasaCompraUsada <= 0 && !empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                        $tasaCompraUsada = floatval($tasaRow->tasa_cambio);
                    }
                    if ($tasaVentaUsada <= 0 && !empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                        $tasaVentaUsada = floatval($tasaRow->tasa_venta);
                    }
                    if ($tasaCompraUsada <= 0 && !empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                        $tasaCompraUsada = floatval($tasaRow->tasa_venta);
                    }
                    if ($tasaVentaUsada <= 0 && !empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                        $tasaVentaUsada = floatval($tasaRow->tasa_cambio);
                    }
                }
            }

            $monto_usd_aplicado = $monto;
            if ($posted_monto_usd > 0 || $posted_monto_nio > 0) {
                if ($posted_monto_nio > 0 && $tasaVentaUsada <= 0) {
                    throw new Exception('Tipo de cambio venta inválido para convertir el componente NIO a USD');
                }
                $monto_usd_aplicado = round(floatval($posted_monto_usd) + (($posted_monto_nio > 0 && $tasaVentaUsada > 0) ? (floatval($posted_monto_nio) / floatval($tasaVentaUsada)) : 0), 2);
            } elseif (!empty($moneda) && strtoupper($moneda) === 'NIO') {
                if ($tasaVentaUsada <= 0) {
                    throw new Exception('Tipo de cambio venta inválido para convertir pago en C$ a USD');
                }
                $monto_usd_aplicado = round(floatval($monto) / floatval($tasaVentaUsada), 2);
                $posted_monto_nio = floatval($monto);
            } else {
                $posted_monto_usd = floatval($monto);
            }
            $monto = $monto_usd_aplicado;

            $remaining = $monto;
            $insert_ids = array();

            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idprestamo', $idprestamo);
            $this->db->order_by('numero', 'ASC');
            $cuotas = $this->db->get()->result();

            if (!empty($cuotas) && !empty($idcuota)) {
                $startIndex = null;
                foreach ($cuotas as $idx => $c) {
                    $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                    if ($c_id == $idcuota || (isset($c->numero) && $c->numero == $idcuota)) { $startIndex = $idx; break; }
                }
                if (!is_null($startIndex) && $startIndex > 0) {
                    $cuotas = array_merge(array_slice($cuotas, $startIndex), array_slice($cuotas, 0, $startIndex));
                }
            }

            $user_id = $this->ion_auth->get_user_id();
            foreach ($cuotas as $c) {
                if ($remaining <= 0) break;
                $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                $c_val = isset($c->cuota) ? floatval($c->cuota) : 0;

                $paid = 0;
                if (!empty($c_id)) {
                    $this->db->select_sum('monto_pagado');
                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $idprestamo);
                    $this->db->where('idcuota', $c_id);
                    $paidRow = $this->db->get()->row();
                    if ($paidRow) {
                        $varsPaid = get_object_vars($paidRow);
                        $firstPaid = reset($varsPaid);
                        $paid = floatval($firstPaid);
                    }
                }

                $c_saldo = max(0, $c_val - $paid);
                if ($c_saldo <= 0) continue;
                $aplicar = min($c_saldo, $remaining);

                $principal_cuota = isset($c->principal) ? floatval($c->principal) : 0.0;
                $interes_corriente_cuota = isset($c->interes) ? floatval($c->interes) : 0.0;
                $interes_mora_cuota = isset($c->monto_mora) ? floatval($c->monto_mora) : 0.0;

                $pagado_principal_prev = 0.0;
                $pagado_interes_corr_prev = 0.0;
                $pagado_interes_mora_prev = 0.0;
                if ($this->db->field_exists('monto_principal_pagado', 'tb_prestamo_pagos') || $this->db->field_exists('monto_interes_corriente_pagado', 'tb_prestamo_pagos') || $this->db->field_exists('monto_interes_mora_pagado', 'tb_prestamo_pagos')) {
                    $this->db->select('COALESCE(SUM(monto_principal_pagado),0) AS pag_pri, COALESCE(SUM(monto_interes_corriente_pagado),0) AS pag_int_corr, COALESCE(SUM(monto_interes_mora_pagado),0) AS pag_int_mora', false);
                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $idprestamo);
                    $this->db->where('idcuota', $c_id);
                    if ($this->db->field_exists('anulado', 'tb_prestamo_pagos')) {
                        $this->db->where('(anulado = 0 OR anulado IS NULL)', null, false);
                    }
                    $rowDist = $this->db->get()->row();
                    if ($rowDist) {
                        $pagado_principal_prev = floatval(isset($rowDist->pag_pri) ? $rowDist->pag_pri : 0);
                        $pagado_interes_corr_prev = floatval(isset($rowDist->pag_int_corr) ? $rowDist->pag_int_corr : 0);
                        $pagado_interes_mora_prev = floatval(isset($rowDist->pag_int_mora) ? $rowDist->pag_int_mora : 0);
                    }
                }

                $resta_interes_mora = max(0, $interes_mora_cuota - $pagado_interes_mora_prev);
                $resta_interes_corr = max(0, $interes_corriente_cuota - $pagado_interes_corr_prev);
                $resta_principal = max(0, $principal_cuota - $pagado_principal_prev);
                if (($resta_interes_mora + $resta_interes_corr + $resta_principal) <= 0) {
                    $resta_principal = $c_saldo;
                }

                // Prioridad de distribución: interés moratorio -> interés corriente -> principal
                $monto_interes_mora_pagado = min($resta_interes_mora, $aplicar);
                $resto_despues_mora = $aplicar - $monto_interes_mora_pagado;
                $monto_interes_corriente_pagado = min($resta_interes_corr, $resto_despues_mora);
                $resto_despues_interes = $resto_despues_mora - $monto_interes_corriente_pagado;
                $monto_principal_pagado = min($resta_principal, $resto_despues_interes);
                $monto_interes_total_pagado = $monto_interes_mora_pagado + $monto_interes_corriente_pagado;

                $dataPago = array(
                    'idprestamo' => $idprestamo,
                    'idcuota' => $c_id,
                    'idcliente' => $cliente_id,
                    'monto_pagado' => $aplicar,
                    'metodo_pago' => $metodo,
                    'referencia' => ($assigned_referencia_formatted !== null ? $assigned_referencia_formatted : $referencia),
                    'idusuario' => $user_id,
                    'fecha_pago' => $fecha_pago_val,
                    'idserie' => $idserie_val,
                    'dato_adicional' => $dato_adicional
                );
                if ($this->db->field_exists('moneda', 'tb_prestamo_pagos')) {
                    $dataPago['moneda'] = !empty($moneda) ? strtoupper($moneda) : 'USD';
                }
                if ($this->db->field_exists('monto_original', 'tb_prestamo_pagos')) {
                    $dataPago['monto_original'] = $posted_monto_original;
                }
                if ($this->db->field_exists('monto_principal_pagado', 'tb_prestamo_pagos')) {
                    $dataPago['monto_principal_pagado'] = round($monto_principal_pagado, 2);
                }
                if ($this->db->field_exists('monto_interes_corriente_pagado', 'tb_prestamo_pagos')) {
                    $dataPago['monto_interes_corriente_pagado'] = round($monto_interes_corriente_pagado, 2);
                }
                if ($this->db->field_exists('monto_interes_mora_pagado', 'tb_prestamo_pagos')) {
                    $dataPago['monto_interes_mora_pagado'] = round($monto_interes_mora_pagado, 2);
                }
                if ($this->db->field_exists('monto_interes_pagado', 'tb_prestamo_pagos')) {
                    $dataPago['monto_interes_pagado'] = round($monto_interes_total_pagado, 2);
                }
                if ($this->db->field_exists('monto_usd_recibido', 'tb_prestamo_pagos')) {
                    $dataPago['monto_usd_recibido'] = round(max(0, $posted_monto_usd), 2);
                }
                if ($this->db->field_exists('monto_nio_recibido', 'tb_prestamo_pagos')) {
                    $dataPago['monto_nio_recibido'] = round(max(0, $posted_monto_nio), 2);
                }
                if ($this->db->field_exists('tc_venta_aplicada', 'tb_prestamo_pagos') && $tasaVentaUsada > 0) {
                    $dataPago['tc_venta_aplicada'] = $tasaVentaUsada;
                }

                $okInsert = $this->db->insert('tb_prestamo_pagos', $dataPago);
                if (!$okInsert) {
                    $err = $this->db->error();
                    throw new Exception('Error insertando pago aplicado: ' . (isset($err['message']) ? $err['message'] : 'desconocido'));
                }
                $insert_ids[] = $this->db->insert_id();

                if ($this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                    $new_saldo = $c_saldo - $aplicar;
                    if ($new_saldo < 0) $new_saldo = 0;
                    $this->Core_model->update('tb_prestamo_cuotas', array('saldo' => $new_saldo), array('idcuota' => $c_id));
                }

                $cuenta_id_flujo = isset($pago->cuenta_id) ? intval($pago->cuenta_id) : 1;
                $this->Tesoreria_model->save_flujo(array(
                    'fecha' => date('Y-m-d', strtotime($fecha_pago_val)),
                    'cuenta_id' => $cuenta_id_flujo,
                    'concepto' => 'Aplicación pago provisional #' . $pago_id . ' préstamo ' . $idprestamo . ' cuota ' . $c_id,
                    'tipo' => 'ingreso',
                    'proyectado' => 0,
                    'realizado' => $aplicar
                ));

                $remaining -= $aplicar;
            }

            if (empty($insert_ids)) {
                throw new Exception('No se pudo aplicar el pago: no hay cuotas con saldo disponible.');
            }

            $this->db->select_sum('saldo');
            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idprestamo', $idprestamo);
            $rowSum = $this->db->get()->row();
            $totalSaldo = 0;
            if ($rowSum) {
                $vars = get_object_vars($rowSum);
                $first = reset($vars);
                $totalSaldo = floatval($first);
            }
            if ($this->db->field_exists('total_saldo', 'tb_prestamos')) {
                $this->Core_model->update('tb_prestamos', array('total_saldo' => $totalSaldo), array('idprestamo' => $idprestamo));
            }

            $up = array('estado' => 'aplicado_pendiente_arqueo');
            if ($this->db->field_exists('updated_at', 'teso_pagos')) $up['updated_at'] = date('Y-m-d H:i:s');
            if ($this->db->field_exists('fecha_aprobacion', 'teso_pagos')) $up['fecha_aprobacion'] = date('Y-m-d H:i:s');
            if ($this->db->field_exists('aprobado_por', 'teso_pagos')) $up['aprobado_por'] = $user_id;
            if ($this->db->field_exists('monto_recibido', 'teso_pagos')) $up['monto_recibido'] = $monto_recibido_post;
            if ($this->db->field_exists('fecha_recepcion', 'teso_pagos')) $up['fecha_recepcion'] = $fecha_recepcion_post;
            if ($this->db->field_exists('tc_compra', 'teso_pagos') && $tasaCompraUsada > 0) $up['tc_compra'] = $tasaCompraUsada;
            if ($this->db->field_exists('tc_venta', 'teso_pagos') && $tasaVentaUsada > 0) $up['tc_venta'] = $tasaVentaUsada;
            if ($this->db->field_exists('tc_aplicada', 'teso_pagos') && strtoupper($moneda) === 'NIO' && $tasaVentaUsada > 0) $up['tc_aplicada'] = $tasaVentaUsada;
            if ($this->db->field_exists('monto_usd_aplicado', 'teso_pagos')) $up['monto_usd_aplicado'] = $monto_usd_aplicado;
            if ($this->db->field_exists('recepcion_validada', 'teso_pagos')) $up['recepcion_validada'] = 1;
            $this->db->where('id', $pago_id);
            $this->db->update('teso_pagos', $up);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => false, 'message' => 'No se pudo aprobar el pago provisional');
            }

            $this->db->trans_commit();
            return array('status' => true, 'message' => 'Pago aplicado y marcado como pendiente de arqueo', 'ids' => $insert_ids);
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', '_aplicar_pago_provisional throwable: ' . $e->getMessage());
            return array('status' => false, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', '_aplicar_pago_provisional exception: ' . $e->getMessage());
            return array('status' => false, 'message' => $e->getMessage());
        }
    }

    // AJAX: aprobar pago provisional y aplicarlo al préstamo/cuotas
    public function aprobar_pago_provisional_ajax()
    {
        header('Content-Type: application/json');
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => false, 'message' => 'Solicitud inválida'));
            return;
        }

        $pago_id = intval($this->input->post('pago_id'));
        if ($pago_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Pago no válido'));
            return;
        }
        $monto_recibido_post = floatval($this->input->post('monto_recibido'));
        $fecha_recepcion_post = trim((string)$this->input->post('fecha_recepcion'));
        if ($monto_recibido_post <= 0 || $fecha_recepcion_post === '') {
            echo json_encode(array('status' => false, 'message' => 'Debe registrar monto recibido y fecha de recepción antes de aprobar'));
            return;
        }

        $result = $this->_aplicar_pago_provisional($pago_id, $monto_recibido_post, $fecha_recepcion_post);
        echo json_encode($result);
    }

    public function reporte_pagos_recepcion()
    {
        $fecha = $this->input->get('fecha');
        if (empty($fecha)) $fecha = date('Y-m-d');
        $modo = strtolower(trim((string)$this->input->get('modo')));
        $q = trim((string)$this->input->get('q'));
        $data = $this->_get_arqueos_pagos_data($fecha, $modo, $q);
        $data['titulo'] = ($modo === 'arqueo' ? 'Arqueo de Pagos' : 'Reporte de Pagos');

        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/arqueos', $data);
        $this->load->view('layout/footer');
    }
    public function cobros() { $this->_page('Gestión de Cobros', 'tesoreria/cobros'); }
    public function arqueos()
    {
        if (!$this->db->table_exists('tb_cierres_caja')) {
            // Si no existe tabla de cierres, usar el modo antiguo
            $fecha = $this->input->get('fecha');
            if (empty($fecha)) $fecha = date('Y-m-d');
            $modo = strtolower(trim((string)$this->input->get('modo')));
            $q = trim((string)$this->input->get('q'));
            $data = $this->_get_arqueos_pagos_data($fecha, $modo, $q);
            $data['titulo'] = 'Arqueos de Pagos de Crédito';
            $this->load->view('layout/header', $data);
            $this->load->view('tesoreria/arqueos', $data);
            $this->load->view('layout/footer');
            return;
        }

        // Modo nuevo: listar cierres de caja
        $cierres = $this->db->order_by('id', 'DESC')->get('tb_cierres_caja')->result();
        
        $data = array(
            'titulo' => 'Arqueos de Pagos de Crédito',
            'icono' => 'fas fa-box',
            'cierres' => $cierres
        );

        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/arqueos_cierres', $data);
        $this->load->view('layout/footer');
    }

    public function cierres_detalle()
    {
        $cierre_id = $this->input->get('cierre_id');
        if (!$cierre_id) {
            show_error('Cierre no especificado');
            return;
        }

        $cierre = $this->db->get_where('tb_cierres_caja', array('id' => intval($cierre_id)))->row();
        if (!$cierre) {
            show_error('Cierre no encontrado');
            return;
        }

        $cierre->usuario_ejecutor = $this->_resolver_nombre_usuario_cierre(isset($cierre->idusuario) ? $cierre->idusuario : null);

        $this->ensure_teso_pagos_recepcion_columns();
        $this->ensure_cierre_arqueo_tables();
        $pagos = $this->db->where('idcierre_caja', intval($cierre_id))->order_by('id', 'ASC')->get('teso_pagos')->result();
        $cuentas_banco = array();
        if ($this->db->table_exists('teso_accounts')) {
            $cuentas_banco = $this->db
                ->where('type', 'banco')
                ->where('estado', 1)
                ->order_by('name', 'ASC')
                ->get('teso_accounts')
                ->result();
        }

        $data = array(
            'titulo' => 'Detalle del Cierre #' . $cierre->consecutivo,
            'cierre' => $cierre,
            'pagos' => $pagos,
            'cuentas_banco' => $cuentas_banco,
            'arqueo_existente' => $this->_get_cierre_arqueo(intval($cierre_id)),
            'arqueos_series_existentes' => $this->_get_cierre_arqueos_series(intval($cierre_id)),
            'series_requeridas_arqueo' => $this->_get_series_requeridas_cierre(intval($cierre_id)),
            'series_pendientes_arqueo' => $this->_get_series_pendientes_arqueo(intval($cierre_id)),
            'depositos_pendientes_cierre' => $this->_get_cierre_depositos_pendientes(array('cierre_id' => intval($cierre_id)))
        );

        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/cierres_detalle', $data);
        $this->load->view('layout/footer');
    }

    public function arqueos_pdf()
    {
        $fecha = $this->input->get('fecha');
        if (empty($fecha)) $fecha = date('Y-m-d');
        $modo = strtolower(trim((string)$this->input->get('modo')));
        $q = trim((string)$this->input->get('q'));

        $data = $this->_get_arqueos_pagos_data($fecha, $modo, $q);
        $data['titulo'] = 'Cierre Diario de Arqueos de Pagos';

        $html = $this->load->view('tesoreria/arqueos_pdf', $data, TRUE);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'arqueos_pagos_' . preg_replace('/[^0-9\-]/', '', $fecha) . '.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=' . $filename);
        echo $dompdf->output();
    }

    public function cierres_pdf()
    {
        $cierre_id = $this->input->get('cierre_id');
        if (!$cierre_id) {
            show_error('Cierre no especificado');
            return;
        }

        $cierre = $this->db->get_where('tb_cierres_caja', array('id' => intval($cierre_id)))->row();
        if (!$cierre) {
            show_error('Cierre no encontrado');
            return;
        }

        $cierre->usuario_ejecutor = $this->_resolver_nombre_usuario_cierre(isset($cierre->idusuario) ? $cierre->idusuario : null);

        $this->ensure_teso_pagos_recepcion_columns();
        $pagos = $this->db->where('idcierre_caja', intval($cierre_id))->order_by('id', 'ASC')->get('teso_pagos')->result();

        $data = array(
            'titulo' => 'Reporte de Cierre #' . $cierre->consecutivo,
            'cierre' => $cierre,
            'pagos' => $pagos
        );

        $html = $this->load->view('tesoreria/cierres_pdf', $data, TRUE);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $filename = 'cierre_caja_' . str_pad($cierre->consecutivo, 3, '0', STR_PAD_LEFT) . '.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=' . $filename);
        echo $dompdf->output();
    }

    public function flujo() { $this->_page('Flujo de Efectivo', 'tesoreria/flujo'); }
    public function integracion()
    {
        $filters = array();
        $filters['date_from'] = $this->input->get('date_from');
        $filters['date_to'] = $this->input->get('date_to');
        $filters['tipo'] = $this->input->get('tipo');

        $tasaCompra = null;
        $tasaVenta = null;
        if ($this->db->table_exists('tb_tasa_cambio')) {
            $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
            if ($tasaRow) {
                if (isset($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                    $tasaCompra = floatval($tasaRow->tasa_cambio);
                }
                if (isset($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                    $tasaVenta = floatval($tasaRow->tasa_venta);
                }
                if (($tasaCompra === null || $tasaCompra <= 0) && $tasaVenta !== null && $tasaVenta > 0) {
                    $tasaCompra = $tasaVenta;
                }
                if (($tasaVenta === null || $tasaVenta <= 0) && $tasaCompra !== null && $tasaCompra > 0) {
                    $tasaVenta = $tasaCompra;
                }
            }
        }

        $data = array(
            'titulo' => 'Integración Bancaria',
            'flujo' => $this->Tesoreria_model->get_flujo($filters),
            'pagos' => $this->Tesoreria_model->get_pagos([]),
            'depositos_pendientes' => $this->_get_cierre_depositos_pendientes(array('estado' => array('pendiente', 'integrado'))),
            'tasa_compra' => $tasaCompra,
            'tasa_venta' => $tasaVenta,
            'cuentas_banco' => $this->db->table_exists('teso_accounts')
                ? $this->db->where('type', 'banco')->where('estado', 1)->order_by('name', 'ASC')->get('teso_accounts')->result()
                : array()
        );
        $this->load->view('layout/header', $data);
        $this->load->view('tesoreria/integracion', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: link a pago con un asiento contable (se crea/actualiza registro en helper table)
    public function link_pago_journal()
    {
        header('Content-Type: application/json');
        $p = $this->input->post(NULL, TRUE);
        $pago_id = isset($p['pago_id']) ? intval($p['pago_id']) : 0;
        $journal_id = isset($p['journal_id']) ? intval($p['journal_id']) : 0;
        $locked = isset($p['locked']) ? intval($p['locked']) : 0;
        if (!$pago_id || !$journal_id) { echo json_encode(['status'=>false,'message'=>'pago_id y journal_id requeridos']); return; }
        $ok = $this->Tesoreria_model->link_pago_journal($pago_id, $journal_id, $locked);
        echo json_encode(['status'=>$ok ? true : false]);
    }

    // AJAX: toggle lock state for a linked pago
    public function toggle_pago_lock()
    {
        header('Content-Type: application/json');
        $p = $this->input->post(NULL, TRUE);
        $pago_id = isset($p['pago_id']) ? intval($p['pago_id']) : 0;
        $lock = isset($p['lock']) ? intval($p['lock']) : 0;
        if (!$pago_id) { echo json_encode(['status'=>false,'message'=>'pago_id requerido']); return; }
        $link = $this->Tesoreria_model->get_pago_journal($pago_id);
        if (!$link) { echo json_encode(['status'=>false,'message'=>'Pago no vinculado a asiento']); return; }
        $ok = $this->Tesoreria_model->link_pago_journal($pago_id, $link->journal_id, $lock);
        echo json_encode(['status'=>$ok ? true : false]);
    }
    public function reportes() { $this->_page('Reportería', 'tesoreria/reportes'); }
    public function seguridad() { $this->_page('Seguridad y Roles', 'tesoreria/seguridad'); }

    private function _page($titulo, $view)
    {
        $data = ['titulo' => $titulo, 'icono' => 'fas fa-wallet'];
        $this->load->view('layout/header', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/footer');
    }

    // Modales
    public function modal_movimiento() { $this->load->view('tesoreria/modal_movimiento'); }
    public function modal_pago() { $this->load->view('tesoreria/modal_pago'); }
    public function modal_arqueo() { $this->load->view('tesoreria/modal_arqueo'); }

    // Cajas y Bancos endpoints
    public function get_cuentas_ajax()
    {
        $q = $this->db->order_by('id','asc')->get('teso_accounts');
        header('Content-Type: application/json');
        echo json_encode(['status'=>true,'cuentas'=>$q->result()]);
    }

    // Devuelve cuentas con saldo calculado (compatibilidad con teso_cuentas o teso_accounts)
    public function get_cuentas_with_saldo_ajax()
    {
        header('Content-Type: application/json');
        // Forzar el uso de teso_accounts para evitar conflictos si existen ambas tablas
        $accounts = array();
        if ($this->db->table_exists('teso_accounts')) {
            $accounts = $this->db->order_by('id','asc')->get('teso_accounts')->result();
        }
        // Si no existe teso_accounts, intenta con teso_cuentas (opcional)
        else if ($this->db->table_exists('teso_cuentas')) {
            $accounts = $this->db->order_by('id','asc')->get('teso_cuentas')->result();
        }
        // compute saldo per account
        $has_mov = $this->db->table_exists('teso_movimientos');
        $result = array();

        foreach ($accounts as $a) {
            $acct = (array)$a;
            $saldo = 0.00;

            // Siempre calcular saldo por movimientos (abono/cargo)
            if ($has_mov) {
                try {
                    $this->db->select("SUM(CASE WHEN tipo_transferencia='abono' THEN monto_total WHEN tipo_transferencia='cargo' THEN -monto_total ELSE 0 END) as s", false);
                    $this->db->from('teso_movimientos');
                    $this->db->where('cuenta_id', intval($a->id));
                    $this->db->where('estado', 'activo');
                    $row = $this->db->get()->row();
                    if ($row) {
                        $vars = get_object_vars($row);
                        $first = reset($vars);
                        $saldo = floatval($first);
                    }
                } catch (Exception $e) {
                    $saldo = 0.00;
                }
            } else {
                // last-resort: look for common balance-like fields on legacy accounts
                if (isset($acct['saldo']) && $acct['saldo'] !== null) $saldo = floatval($acct['saldo']);
                else if (isset($acct['balance']) && $acct['balance'] !== null) $saldo = floatval($acct['balance']);
                else if (isset($acct['saldo_inicial']) && $acct['saldo_inicial'] !== null) $saldo = floatval($acct['saldo_inicial']);
            }

            $acct['saldo'] = number_format($saldo, 2, '.', '');
            // Normalize field names so views can work with either schema (teso_cuentas or teso_accounts)
            $normalized = array(
                'id' => isset($acct['id']) ? $acct['id'] : (isset($acct['ID']) ? $acct['ID'] : null),
                'code' => isset($acct['code']) ? $acct['code'] : (isset($acct['codigo']) ? $acct['codigo'] : null),
                'name' => isset($acct['name']) ? $acct['name'] : (isset($acct['nombre']) ? $acct['nombre'] : null),
                'type' => isset($acct['type']) ? $acct['type'] : (isset($acct['tipo']) ? $acct['tipo'] : null),
                'bank_name' => isset($acct['bank_name']) ? $acct['bank_name'] : (isset($acct['banco']) ? $acct['banco'] : null),
                'account_number' => isset($acct['account_number']) ? $acct['account_number'] : (isset($acct['account']) ? $acct['account'] : (isset($acct['clabe']) ? $acct['clabe'] : null)),
                'saldo' => $acct['saldo'],
                'currency' => isset($acct['currency']) ? $acct['currency'] : (isset($acct['moneda']) ? $acct['moneda'] : null),
                'currency_symbol' => isset($acct['currency_symbol']) ? $acct['currency_symbol'] : null,
                'estado' => isset($acct['estado']) ? $acct['estado'] : (isset($acct['activo']) ? $acct['activo'] : 1)
            );
            $result[] = $normalized;
        }
        echo json_encode(['status'=>true,'cuentas'=>$result, 'using' => 'teso_accounts']);
    }

    public function save_cuenta_ajax()
    {
        if (!$this->ion_auth->is_admin()) { echo json_encode(['status'=>false,'message'=>'Sin permisos']); return; }
        $p = $this->input->post(NULL, TRUE);
        $data = [
            'name' => isset($p['name']) ? $p['name'] : NULL,
            'type' => isset($p['type']) ? $p['type'] : NULL,
            'bank_name' => isset($p['bank_name']) ? $p['bank_name'] : NULL,
            'account_number' => isset($p['account_number']) ? $p['account_number'] : NULL,
            'currency' => isset($p['currency']) ? $p['currency'] : NULL,
            'currency_symbol' => isset($p['currency_symbol']) ? $p['currency_symbol'] : NULL,
            'estado' => isset($p['estado']) ? intval($p['estado']) : 1,
            'sig_cheque' => isset($p['sig_cheque']) ? intval($p['sig_cheque']) : null,
            'formato' => (isset($p['formato']) && $p['formato'] !== '' ? $p['formato'] : NULL),
        ];
        if (isset($p['id']) && intval($p['id'])>0) {
            // No permitir actualizar code ni saldo_inicial
            unset($data['code']);
            unset($data['saldo_inicial']);
            $this->db->where('id', intval($p['id']))->update('teso_accounts', $data);
            $id = intval($p['id']);
        } else {
            // Generar clave automática (consecutivo)
            $max = $this->db->select_max('id')->get('teso_accounts')->row();
            $next_id = isset($max->id) ? intval($max->id) + 1 : 1;
            $data['code'] = str_pad($next_id, 4, '0', STR_PAD_LEFT); // ejemplo: 0001, 0002...
            // Inicializar campos de montos
            $data['saldo_inicial'] = isset($p['saldo_inicial']) && is_numeric($p['saldo_inicial']) ? floatval($p['saldo_inicial']) : 0;
            $data['total_cargos'] = 0;
            $data['total_abonos'] = 0;
            $data['saldo_actual'] = 0;
            $data['saldo_conciliado'] = 0;
            $data['cargos_transito'] = 0;
            $data['abonos_transito'] = 0;
            $data['montos_transito'] = 0;
            $data['saldos_sin_transito'] = 0;
            try {
                $this->db->insert('teso_accounts', $data);
                $id = $this->db->insert_id();
                if (!$id) {
                    $err = $this->db->error();
                    throw new Exception(isset($err['message']) ? $err['message'] : 'Unknown DB error');
                }
            } catch (Exception $e) {
                // Try to repair common schema issue: id column not AUTO_INCREMENT
                try {
                    $this->db->query("ALTER TABLE `teso_accounts` MODIFY `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY");
                    // retry insert
                    $this->db->insert('teso_accounts', $data);
                    $id = $this->db->insert_id();
                } catch (Exception $e2) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>false,'message'=>'DB error: '.$e->getMessage().' / '.$e2->getMessage()]);
                    return;
                }
            }
        }
        $row = $this->db->get_where('teso_accounts',['id'=>$id])->row();
        header('Content-Type: application/json');
        echo json_encode(['status'=>true,'cuenta'=>$row]);
    }

    public function del_cuenta($id=NULL)
    {
        if (!$this->ion_auth->is_admin()) { $this->session->set_flashdata('info','Sin permisos'); redirect('tesoreria/cajas_bancos'); }
        if (!$id) { $this->session->set_flashdata('error','ID requerido'); redirect('tesoreria/cajas_bancos'); }
        $has_mov_table = $this->db->table_exists('teso_movimientos');
        $movs = 0;
        if ($has_mov_table) {
            $movs = $this->db->where('cuenta_id', intval($id))->count_all_results('teso_movimientos');
        }
        // Verificar si la cuenta tiene saldo distinto de cero
        $cuenta = $this->db->get_where('teso_accounts', ['id'=>intval($id)])->row();
        $saldo = 0;
        if ($cuenta) {
            if (isset($cuenta->saldo)) {
                $saldo = floatval($cuenta->saldo);
            } else if ($has_mov_table) {
                // Calcular saldo si no existe el campo
                $saldo = 0;
                $this->db->select("SUM(CASE WHEN tipo='ingreso' THEN monto WHEN tipo='egreso' THEN -monto ELSE 0 END) as s", false);
                $this->db->from('teso_movimientos');
                $this->db->where('cuenta_id', intval($id));
                $row = $this->db->get()->row();
                if ($row) {
                    $vars = get_object_vars($row);
                    $first = reset($vars);
                    $saldo = floatval($first);
                }
            }
        }
        if ($movs > 0) {
            // Si hay movimientos, primero borra los movimientos y luego la cuenta
            if ($has_mov_table) {
                $this->db->where('cuenta_id', intval($id))->delete('teso_movimientos');
            }
        }
        // Solo permite borrar la cuenta si no tiene saldo activo
        if (abs($saldo) > 0.0001) {
            $this->session->set_flashdata('error','No se puede eliminar la cuenta: tiene saldo activo.');
            redirect('tesoreria/cajas_bancos');
        }
        $this->db->where('id', intval($id))->delete('teso_accounts');
        redirect('tesoreria/cajas_bancos');
    }

    // AJAX endpoints (scaffold)
    public function save_movimiento()
    {
        $payload = $this->input->post(NULL, TRUE);
        $id = $this->Tesoreria_model->save_movimiento($payload);
        header('Content-Type: application/json');
        echo json_encode($id ? array('status'=>'success','id'=>$id) : array('status'=>'error'));
    }

    public function save_pago()
    {
        $payload = $this->input->post(NULL, TRUE);
        $id = $this->Tesoreria_model->save_pago($payload);
        header('Content-Type: application/json');
        echo json_encode($id ? array('status'=>'success','id'=>$id) : array('status'=>'error'));
    }

    public function save_arqueo()
    {
        $payload = $this->input->post(NULL, TRUE);
        $id = $this->Tesoreria_model->save_arqueo($payload);
        header('Content-Type: application/json');
        echo json_encode($id ? array('status'=>'success','id'=>$id) : array('status'=>'error'));
    }

    // AJAX: Obtener créditos vigentes de un cliente (igual que pagos/prestamos_core)
    public function get_creditos_cliente() {
        $cliente_id = $this->input->post('cliente_id');
        if (!$cliente_id) {
            echo json_encode(['status'=>false, 'msg'=>'ID de cliente requerido']);
            return;
        }
        $this->load->model('Prestamos_model');
        $creditos = array();
        foreach ($this->Prestamos_model->get_all() as $c) {
            if ((string)$c->idcliente === (string)$cliente_id && $c->estado != 0) {
                $creditos[] = array(
                    'id' => $c->id,
                    'fuente' => $c->fuente,
                    'codigo' => isset($c->codigo) ? $c->codigo : (isset($c->id) ? $c->id : ''),
                    'monto' => isset($c->monto_credito) ? $c->monto_credito : '',
                    'fecha' => isset($c->fecha_credito) ? $c->fecha_credito : '',
                    'estado' => $c->estado
                );
            }
        }
        echo json_encode(['status'=>true, 'creditos'=>$creditos]);
    }

    // AJAX: Obtener cuotas pendientes de un crédito
    public function get_cuotas_pendientes() {
        $idprestamo = $this->input->post('idprestamo');
        if (!$idprestamo) {
            echo json_encode(['status'=>false, 'msg'=>'ID de crédito requerido']);
            return;
        }
        // Suponiendo tabla tb_cuotas con campos: idcuota, idprestamo, numero, fecha_vencimiento, monto, monto_pagado
        $this->db->select('idcuota, numero, fecha_vencimiento, monto, monto_pagado, (monto-IFNULL(monto_pagado,0)) as monto_pendiente');
        $this->db->from('tb_cuotas');
        $this->db->where('idprestamo', $idprestamo);
        $this->db->where('(monto-IFNULL(monto_pagado,0)) >', 0);
        $this->db->order_by('numero', 'asc');
        $cuotas = $this->db->get()->result_array();
        echo json_encode(['status'=>true, 'cuotas'=>$cuotas]);
    }

    // AJAX: Registrar pago de cliente a cuota
    public function registrar_pago_cliente() {
        $idcliente = $this->input->post('idcliente');
        $idprestamo = $this->input->post('idcredito');
        $idcuota = $this->input->post('idcuota');
        $monto_pago = $this->input->post('monto_pago');
        $fecha_pago = $this->input->post('fecha_pago');
        $forma_pago = $this->input->post('forma_pago');
        if (!$idcliente || !$idprestamo || !$idcuota || !$monto_pago || !$fecha_pago || !$forma_pago) {
            echo json_encode(['status'=>false, 'msg'=>'Datos incompletos']);
            return;
        }
        // Insertar pago en tabla tb_pagos (ajusta el nombre si es diferente)
        $data = [
            'idcliente' => $idcliente,
            'idprestamo' => $idprestamo,
            'idcuota' => $idcuota,
            'monto' => $monto_pago,
            'fecha_pago' => $fecha_pago,
            'forma_pago' => $forma_pago,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tb_pagos', $data);
        // Actualizar monto_pagado en la cuota
        $this->db->set('monto_pagado', 'IFNULL(monto_pagado,0)+'.floatval($monto_pago), false);
        $this->db->where('idcuota', $idcuota);
        $this->db->update('tb_cuotas');
        echo json_encode(['status'=>true]);
    }

    // AJAX: Listar clientes con créditos activos (igual que Planescredito::clients)
    public function clientes_con_creditos() {
        $this->load->model('Prestamos_model');
        $clientes = array();
        $creditos = $this->Prestamos_model->get_all();
        foreach ($creditos as $c) {
            if (!empty($c->idcliente)) {
                $clientes[$c->idcliente] = array(
                    'id' => $c->idcliente,
                    'nombre' => (isset($c->apellidos) ? $c->apellidos : '') . ', ' . (isset($c->cliente_nombres) ? $c->cliente_nombres : ''),
                    'numero_doc' => isset($c->numero_doc) ? $c->numero_doc : ''
                );
            }
        }
        $clientes = array_values($clientes);
        echo json_encode(['status'=>true, 'clients'=>$clientes]);
    }

    // --- Métodos robustos copiados/adaptados de Pagos.php para integración exacta ---

    public function getCreditosCliente() {
        try {
            if (!$this->input->is_ajax_request()) {
                redirect($this->router->fetch_class());
            }
            $cliente_id = $this->input->post('cliente_id');
            $output = '<option value="" selected>SELECCIONAR</option>';
            if ($cliente_id) {
                $cliente = null;
                if (is_string($cliente_id) && strpos($cliente_id, 'DOC:') === 0) {
                    $numero_doc = substr($cliente_id, 4);
                    $cliente = (object) array('numero_doc' => $numero_doc);
                } else {
                    if (ctype_digit((string)$cliente_id)) {
                        $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
                    } else {
                        $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
                        if (!$cliente) {
                            $cliente = (object) array('numero_doc' => $cliente_id);
                        }
                    }
                }
                if ($cliente) {
                    $prestamos = array();
                    $numero_doc = '';
                    if (!empty($cliente->numero_doc)) {
                        $numero_doc = $cliente->numero_doc;
                    } else if (ctype_digit((string)$cliente_id)) {
                        $c = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
                        if ($c && !empty($c->numero_doc)) $numero_doc = $c->numero_doc;
                    }
                    try {
                        if (!empty($numero_doc)) {
                            $sql = "SELECT p.* FROM tb_prestamos p JOIN tb_solicitudes s ON s.idsolicitud = p.idsolicitud WHERE s.numero_doc = ? AND p.total_saldo > 0";
                            $prestamos = $this->db->query($sql, array($numero_doc))->result();
                        } elseif (ctype_digit((string)$cliente_id)) {
                            $sql = "SELECT p.* FROM tb_prestamos p JOIN tb_solicitudes s ON s.idsolicitud = p.idsolicitud WHERE s.idcliente = ? AND p.total_saldo > 0";
                            $prestamos = $this->db->query($sql, array($cliente_id))->result();
                        } else {
                            $prestamos = array();
                        }
                    } catch (Exception $e) {
                        $prestamos = array();
                    }
                    if ($prestamos) {
                        foreach ($prestamos as $p) {
                            $output .= '<option value="P-' . $p->idprestamo . '">PLAN-' . $p->idprestamo . '</option>';
                        }
                    }
                }
            }
            $resp = array('status' => true, 'html' => $output);
            echo json_encode($resp);
        } catch (Throwable $e) {
            $resp = array('status' => false, 'error' => $e->getMessage());
            echo json_encode($resp);
        } catch (Exception $e) {
            $resp = array('status' => false, 'error' => $e->getMessage());
            echo json_encode($resp);
        }
    }

    public function getPrestamoNextCuota() {
        error_log('DEBUG getPrestamoNextCuota INICIO');
        if (!$this->input->is_ajax_request()) {
            error_log('DEBUG getPrestamoNextCuota NO AJAX');
            redirect($this->router->fetch_class());
        }
        $idprestamo = $this->input->post('idprestamo');
        error_log('DEBUG getPrestamoNextCuota idprestamo=' . print_r($idprestamo, true));
        $resp = array('status' => false, 'html' => '<option value="">SELECCIONAR</option>');
        if (!$idprestamo) { error_log('DEBUG getPrestamoNextCuota SIN idprestamo'); echo json_encode($resp); return; }
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $idprestamo);
        $this->db->order_by('numero', 'ASC');
        $cuotas = $this->db->get()->result();
        $chosen = null;
        $total_pending = 0;
        if (!empty($cuotas)) {
            foreach ($cuotas as $c) {
                $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                $cuota_val = isset($c->cuota) ? floatval($c->cuota) : 0;
                $saldo_col = isset($c->saldo) ? floatval($c->saldo) : null;
                $paid = 0;
                if (!empty($c_id)) {
                    $this->db->select_sum('monto_pagado');
                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $idprestamo);
                    $this->db->where('idcuota', $c_id);
                    $paidRow = $this->db->get()->row();
                    if ($paidRow) {
                        $varsPaid = get_object_vars($paidRow);
                        $firstPaid = reset($varsPaid);
                        $paid = floatval($firstPaid);
                    }
                }
                $remaining = $cuota_val - $paid;
                if ($remaining < 0) $remaining = 0;
                $total_pending += $remaining;
                if ($remaining > 0) {
                    $chosen = array('raw' => $c, 'id' => $c_id, 'numero' => isset($c->numero) ? $c->numero : null, 'cuota' => $cuota_val, 'saldo' => $remaining, 'fecha_vencimiento' => isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : '');
                    if (!is_null($saldo_col) && abs(floatval($saldo_col) - $remaining) > 0.01 && !empty($c_id) && $this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                        $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $remaining), array('idcuota' => $c_id));
                    } elseif (is_null($saldo_col) && !empty($c_id) && $this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                        $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $remaining), array('idcuota' => $c_id));
                    }
                    break;
                }
            }
        }
        if ($chosen) {
            $cuota_id = $chosen['id'] ? $chosen['id'] : ('N-' . $chosen['numero']);
            $html = '<option value="' . $cuota_id . '">CUOTA ' . ($chosen['numero'] ? $chosen['numero'] : $cuota_id) . '</option>';
            $status = 'Fecha Vigente';
            $dias_atraso = 0;
            if (!empty($chosen['fecha_vencimiento'])) {
                $due = strtotime(substr($chosen['fecha_vencimiento'], 0, 10));
                $today = strtotime(date('Y-m-d'));
                if ($today > $due) {
                    $dias_atraso = intval(($today - $due) / 86400);
                    if ($dias_atraso <= 30) {
                        $status = 'Riesgo normal / mora temprana';
                    } elseif ($dias_atraso <= 60) {
                        $status = 'Riesgo potencial';
                    } elseif ($dias_atraso <= 90) {
                        $status = 'Riesgo real';
                    } elseif ($dias_atraso <= 180) {
                        $status = 'Duda de recuperación';
                    } else {
                        $status = 'Irrecuperable / cartera gravemente vencida';
                    }
                } else {
                    $status = 'Fecha Vigente';
                }
            }
            $resp = array('status' => true, 'html' => $html, 'cuota' => array('idcuota' => $cuota_id, 'cuota' => $chosen['cuota'], 'saldo' => $chosen['saldo'], 'fecha_vencimiento' => $chosen['fecha_vencimiento'], 'estado' => $status, 'dias_atraso' => $dias_atraso), 'total_pending' => $total_pending);
        }
        error_log('DEBUG getPrestamoNextCuota RESP=' . print_r($resp, true));
        header('Content-Type: application/json');
        echo json_encode($resp);
    }

    public function getPrestamoSaldo() {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $idprestamo = $this->input->post('idprestamo');
        $resp = array('status' => false, 'total_saldo' => 0);
        if (!$idprestamo) { echo json_encode($resp); return; }
        $total = 0;
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $idprestamo);
        $cuotas = $this->db->get()->result();
        if (!empty($cuotas)) {
            foreach ($cuotas as $c) {
                $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                $c_val = isset($c->cuota) ? floatval($c->cuota) : 0;
                $paid = 0;
                if (!empty($c_id)) {
                    $this->db->select_sum('monto_pagado');
                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $idprestamo);
                    $this->db->where('idcuota', $c_id);
                    $paidRow = $this->db->get()->row();
                    if ($paidRow) {
                        $varsPaid = get_object_vars($paidRow);
                        $firstPaid = reset($varsPaid);
                        $paid = floatval($firstPaid);
                    }
                }
                $remaining = $c_val - $paid;
                if ($remaining < 0) $remaining = 0;
                $total += $remaining;
            }
        }
        $resp = array('status' => true, 'total_saldo' => $total);
        echo json_encode($resp);
    }

    public function savePrestamoPago() {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $cliente_id = $this->input->post('cliente_id');
        $idcredito = $this->input->post('idcredito');
        $idcuota = $this->input->post('idcuota');
        $posted_monto_original = floatval($this->input->post('monto'));
        $monto = $posted_monto_original;
        $metodo = $this->input->post('metodo');
        $moneda = $this->input->post('moneda');
        $referencia = $this->input->post('referencia');
        $idprestamo = null;
        if (is_string($idcredito) && strpos($idcredito, 'P-') === 0) {
            $idprestamo = intval(substr($idcredito, 2));
        }
        if (!$idprestamo || !$idcuota || $monto <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Datos incompletos'));
            return;
        }
        try {
            $user = $this->ion_auth->get_user_id ? $this->ion_auth->get_user_id() : null;
            $post_referencia = $this->input->post('referencia');
            $idserie_val = null;
            if ($post_referencia !== null && $post_referencia !== '') {
                $idserie_val = intval($post_referencia);
            }
            $assigned_referencia = null;
            $assigned_referencia_formatted = null;
            if (!is_null($idserie_val)) {
                try {
                    $this->db->trans_start();
                    $sr = $this->db->query('SELECT * FROM tb_series_recibos WHERE idserie = ? FOR UPDATE', array($idserie_val))->row();
                    if ($sr) {
                        $current = isset($sr->consecutivo) ? intval($sr->consecutivo) : 0;
                        $next = $current + 1;
                        $this->db->where('idserie', $idserie_val);
                        $this->db->update('tb_series_recibos', array('consecutivo' => $next, 'ultimo_emitido' => $next, 'updated_on' => time()));
                        $assigned_referencia = $next;
                        $code = isset($sr->codigo) ? $sr->codigo : '';
                        $assigned_referencia_formatted = $code . str_pad($next, 3, '0', STR_PAD_LEFT);
                    }
                    $this->db->trans_complete();
                } catch (Exception $e) {
                    $assigned_referencia = null;
                    $assigned_referencia_formatted = null;
                }
            }
            $postedFecha = $this->input->post('fecha_pago');
            $fecha_pago_val = date('Y-m-d H:i:s');
            if (!empty($postedFecha)) {
                $ts = strtotime($postedFecha);
                if ($ts !== false) {
                    $fecha_pago_val = date('Y-m-d H:i:s', $ts);
                }
            }
            if (!empty($moneda) && strtoupper($moneda) === 'NIO') {
                $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
                $tasa = null;
                if ($tasaRow) {
                    if (!empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                        $tasa = floatval($tasaRow->tasa_cambio);
                    } elseif (!empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                        $tasa = floatval($tasaRow->tasa_venta);
                    }
                }
                if (!is_null($tasa) && $tasa > 0) {
                    $monto_usd = floatval($monto) / floatval($tasa);
                    $monto = round($monto_usd, 2);
                }
            }
            $remaining = $monto;
            $insert_ids = array();
            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idprestamo', $idprestamo);
            $this->db->order_by('numero', 'ASC');
            $cuotas = $this->db->get()->result();
            if (!empty($cuotas) && !empty($idcuota)) {
                $startIndex = null;
                foreach ($cuotas as $idx => $c) {
                    $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                    if ($c_id == $idcuota || $c->numero == $idcuota) { $startIndex = $idx; break; }
                }
                if (!is_null($startIndex) && $startIndex > 0) {
                    $cuotas = array_merge(array_slice($cuotas, $startIndex), array_slice($cuotas, 0, $startIndex));
                }
            }
            foreach ($cuotas as $c) {
                if ($remaining <= 0) break;
                $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                $c_val = isset($c->cuota) ? floatval($c->cuota) : 0;
                $paid = 0;
                if (!empty($c_id)) {
                    $this->db->select_sum('monto_pagado');
                    $this->db->from('tb_prestamo_pagos');
                    $this->db->where('idprestamo', $idprestamo);
                    $this->db->where('idcuota', $c_id);
                    $paidRow = $this->db->get()->row();
                    if ($paidRow) {
                        $varsPaid = get_object_vars($paidRow);
                        $firstPaid = reset($varsPaid);
                        $paid = floatval($firstPaid);
                    }
                }
                $c_saldo = max(0, $c_val - $paid);
                if ($c_saldo <= 0) continue;
                $aplicar = min($c_saldo, $remaining);
                $dataPago = array(
                    'idprestamo' => $idprestamo,
                    'idcuota' => $c_id,
                    'idcliente' => $cliente_id,
                    'monto_pagado' => $aplicar,
                    'metodo_pago' => $metodo,
                    'referencia' => ($assigned_referencia_formatted !== null ? $assigned_referencia_formatted : $referencia),
                    'idusuario' => $user,
                    'fecha_pago' => $fecha_pago_val,
                    'idserie' => $idserie_val,
                    'dato_adicional' => $this->input->post('dato_adicional')
                );
                if ($this->db->field_exists('moneda', 'tb_prestamo_pagos')) {
                    $dataPago['moneda'] = !empty($moneda) ? strtoupper($moneda) : 'USD';
                }
                if ($this->db->field_exists('monto_original', 'tb_prestamo_pagos')) {
                    $dataPago['monto_original'] = $posted_monto_original;
                }
                $db_debug_backup = $this->db->db_debug;
                $this->db->db_debug = FALSE;
                $insert_ok = $this->db->insert('tb_prestamo_pagos', $dataPago);
                if (!$insert_ok) {
                    $dberr = $this->db->error();
                    $this->db->db_debug = $db_debug_backup;
                    echo json_encode(array('status' => false, 'message' => 'DB insert error: ' . (isset($dberr['message']) ? $dberr['message'] : 'unknown')));
                    return;
                }
                $insert_id_new = $this->db->insert_id();
                $this->db->db_debug = $db_debug_backup;
                $insert_ids[] = $insert_id_new;
                $new_saldo = $c_saldo - $aplicar;
                if ($new_saldo < 0) $new_saldo = 0;
                if ($this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                    $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $new_saldo), array('idcuota' => $c_id));
                }
                $remaining -= $aplicar;
            }
            try {
                $this->db->select_sum('saldo');
                $this->db->from('tb_prestamo_cuotas');
                $this->db->where('idprestamo', $idprestamo);
                $rowSum = $this->db->get()->row();
                $totalSaldo = 0;
                if ($rowSum) {
                    $vars = get_object_vars($rowSum);
                    $first = reset($vars);
                    $totalSaldo = floatval($first);
                }
                if ($this->db->field_exists('total_saldo', 'tb_prestamos')) {
                    $this->core_model->update('tb_prestamos', array('total_saldo' => $totalSaldo), array('idprestamo' => $idprestamo));
                }
            } catch (Exception $e) {}
            $insert_id = null;
            if (!empty($insert_ids) && is_array($insert_ids)) {
                $insert_id = end($insert_ids);
            }
            echo json_encode(array('status' => true, 'message' => 'Pago registrado', 'id' => $insert_id, 'ids' => $insert_ids));
        } catch (Throwable $e) {
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        } catch (Exception $e) {
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        }
    }
}
