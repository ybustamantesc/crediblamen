<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Desembolsos extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        // Obtener todas las cuentas activas (igual que en movimientos)
        $cuentas = $this->db->order_by('name','asc')->get_where('teso_accounts', "estado = 1")->result();
        $data = [
            'cuentas' => $cuentas,
            'titulo' => 'Desembolsos Programados'
        ];
        $this->load->view('desembolsos/index', $data);
    }
    public function list_ajax() {
        $this->load->model('Desembolsos_model');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $q = $this->input->get('q');
        try {
            $data = $this->Desembolsos_model->listar_pendientes($start, $end, $q);
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch(Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage(), 'query' => $this->db->last_query()]);
        }
    }
    public function ejecutar_ajax() {
        $this->load->model('Desembolsos_model');
        $idprestamo = $this->input->post('idprestamo');
        $fecha = $this->input->post('fecha_desembolso');
        $primer_dia_pago = $this->input->post('primer_dia_pago');
        $obs = $this->input->post('observaciones');
        $ok = $this->Desembolsos_model->ejecutar_desembolso($idprestamo, $fecha, $primer_dia_pago, $obs);
        header('Content-Type: application/json');
        echo json_encode(['status'=>$ok]);
    }
    public function detalle_ajax() {
        $idprestamo = $this->input->get('idprestamo');
        // Seleccionar campos relevantes, incluyendo tipo_credito
        $this->db->select('p.idprestamo, p.monto_credito as monto, p.numero_coutas as plazo, p.interes_credito as tasa, p.comision_desembolso as comision, s.nombres, s.apellidos, s.tipo_credito, s.idcliente');
        $this->db->from('tb_prestamos p');
        $this->db->join('tb_solicitudes s', 's.idsolicitud = p.idsolicitud', 'left');
        $this->db->where('p.idprestamo', $idprestamo);
        $row = $this->db->get()->row();
        $resp = array();
        try {
            if($row) {
                $saldo_renovacion = 0;
                if (!empty($row->idcliente)) {
                    $saldo_q = $this->db->query(
                        "SELECT COALESCE(SUM(IFNULL(pc.saldo, IFNULL(pc.principal,0) + IFNULL(pc.interes,0))),0) AS saldo_renovacion
                         FROM tb_prestamo_cuotas pc
                         JOIN tb_prestamos p2 ON p2.idprestamo = pc.idprestamo
                         JOIN tb_solicitudes s2 ON s2.idsolicitud = p2.idsolicitud
                         WHERE s2.idcliente = ?
                           AND p2.idprestamo <> ?
                           AND IFNULL(p2.estado,0) <> 2
                           AND (pc.saldo IS NULL OR pc.saldo > 0)",
                        array($row->idcliente, $idprestamo)
                    )->row();
                    $saldo_renovacion = $saldo_q ? floatval($saldo_q->saldo_renovacion) : 0;
                }

                $resp = array(
                    'idprestamo' => $row->idprestamo,
                    'monto' => $row->monto,
                    'plazo' => isset($row->plazo) && $row->plazo ? $row->plazo : '',
                    'tasa' => isset($row->tasa) && $row->tasa ? $row->tasa : '',
                    'comision' => isset($row->comision) ? $row->comision : '',
                    'producto' => isset($row->tipo_credito) ? $row->tipo_credito : '',
                    'cliente' => trim($row->nombres.' '.$row->apellidos),
                    'saldo_renovacion' => $saldo_renovacion
                );
            }
        } catch(Exception $e) {
            $resp = array('error' => $e->getMessage());
        }
        header('Content-Type: application/json');
        echo json_encode($resp);
    }

    /**
     * Ejecutar desembolso con costos adicionales y crear movimiento de cheque
     * POST: idprestamo, fecha_desembolso, primer_dia_pago, cuenta_bancaria_id, 
     *       monto_credito, total_desembolso, costos_legales, seguros, comisiones, observaciones
     */
    public function ejecutar_desembolso_con_cheque_ajax() {
        header('Content-Type: application/json');
        
        try {
            $this->load->model('Desembolsos_model');
            
            $idprestamo = $this->input->post('idprestamo');
            $fecha_desembolso = $this->input->post('fecha_desembolso');
            $primer_dia_pago = $this->input->post('primer_dia_pago');
            $cuenta_bancaria_id = $this->input->post('cuenta_bancaria_id');
            $monto_credito = floatval($this->input->post('monto_credito'));
            $total_desembolso = floatval($this->input->post('total_desembolso'));
            $costos_legales = floatval($this->input->post('costos_legales'));
            $seguros = floatval($this->input->post('seguros'));
            $comisiones = floatval($this->input->post('comisiones'));
            $comentario_costos_legales = trim((string)$this->input->post('comentario_costos_legales'));
            $comentario_seguros = trim((string)$this->input->post('comentario_seguros'));
            $comentario_comisiones = trim((string)$this->input->post('comentario_comisiones'));
            $confirmado_costos_legales = (string)$this->input->post('confirmado_costos_legales');
            $confirmado_seguros = (string)$this->input->post('confirmado_seguros');
            $confirmado_comisiones = (string)$this->input->post('confirmado_comisiones');
            $saldo_renovacion = floatval($this->input->post('saldo_renovacion'));
            $renov_principal = floatval($this->input->post('renov_principal'));
            $renov_interes_corriente = floatval($this->input->post('renov_interes_corriente'));
            $renov_interes_mora = floatval($this->input->post('renov_interes_mora'));
            $monto_renovacion = floatval($this->input->post('monto_renovacion'));
            $comentario_renovacion = trim((string)$this->input->post('comentario_renovacion'));
            $observaciones = $this->input->post('observaciones');
            $total_costos = $costos_legales + $seguros + $comisiones;
            $total_renovacion = $monto_renovacion;
            if ($total_renovacion <= 0) {
                $total_renovacion = $renov_principal + $renov_interes_corriente + $renov_interes_mora;
            }
            $total_desembolso_calculado = $monto_credito - $total_costos - $total_renovacion;
            
            // Validaciones
            if(!$idprestamo || !$cuenta_bancaria_id || $total_desembolso <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
                return;
            }

            if ($total_costos < 0 || $total_desembolso_calculado < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Los costos no son válidos para el monto del crédito']);
                return;
            }

            if ($total_renovacion < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Los montos de renovación no son válidos']);
                return;
            }

            if ($saldo_renovacion > 0 && $total_renovacion > $saldo_renovacion) {
                echo json_encode(['status' => 'error', 'message' => 'La renovación no puede exceder el saldo del cliente']);
                return;
            }

            if ($costos_legales > 0 && ($confirmado_costos_legales !== '1' || $comentario_costos_legales === '')) {
                echo json_encode(['status' => 'error', 'message' => 'Debe guardar Costos Legales con comentario obligatorio']);
                return;
            }

            if ($seguros > 0 && ($confirmado_seguros !== '1' || $comentario_seguros === '')) {
                echo json_encode(['status' => 'error', 'message' => 'Debe guardar Seguros con comentario obligatorio']);
                return;
            }

            if ($comisiones > 0 && ($confirmado_comisiones !== '1' || $comentario_comisiones === '')) {
                echo json_encode(['status' => 'error', 'message' => 'Debe guardar Comisiones con comentario obligatorio']);
                return;
            }

            $total_desembolso = $total_desembolso_calculado;
            $obs_final = trim((string)$observaciones);
            if ($comentario_costos_legales !== '') {
                $obs_final .= ($obs_final !== '' ? "\n" : '') . 'Comentario costos legales: ' . $comentario_costos_legales;
            }
            if ($comentario_seguros !== '') {
                $obs_final .= ($obs_final !== '' ? "\n" : '') . 'Comentario seguros: ' . $comentario_seguros;
            }
            if ($comentario_comisiones !== '') {
                $obs_final .= ($obs_final !== '' ? "\n" : '') . 'Comentario comisiones: ' . $comentario_comisiones;
            }
            if ($comentario_renovacion !== '') {
                $obs_final .= ($obs_final !== '' ? "\n" : '') . 'Comentario renovación: ' . $comentario_renovacion;
                $obs_final .= "\n" . 'Renovación aplicada - Principal: ' . number_format($renov_principal, 2, '.', '')
                    . ', Interés Corriente: ' . number_format($renov_interes_corriente, 2, '.', '')
                    . ', Interés en Mora: ' . number_format($renov_interes_mora, 2, '.', '');
            }
            
            // 2. Obtener datos de la cuenta bancaria
            $cuenta = $this->db->get_where('teso_accounts', ['id' => $cuenta_bancaria_id])->row();
            if(!$cuenta) {
                throw new Exception('Cuenta bancaria no encontrada');
            }
            
            // 3. Obtener datos del préstamo y solicitud para completar datos del cheque
            $prestamo = $this->db->select("p.idprestamo, p.numero_coutas, p.monto_credito, p.interes_credito, p.comision_desembolso, s.idsolicitud, COALESCE(NULLIF(TRIM(CONCAT(COALESCE(c.nombres, ''), ' ', COALESCE(c.apellidos, ''))), ''), NULLIF(TRIM(CONCAT(COALESCE(s.nombres, ''), ' ', COALESCE(s.apellidos, ''))), '')) AS cliente", false)
                ->from('tb_prestamos p')
                ->join('tb_solicitudes s', 's.idsolicitud = p.idsolicitud', 'left')
                ->join('tb_clientes c', 'c.idcliente = s.idcliente', 'left')
                ->where('p.idprestamo', $idprestamo)
                ->get()->row();

            $cliente_nombre = ($prestamo && !empty($prestamo->cliente)) ? trim($prestamo->cliente) : 'Prestatario';
            $plazo = ($prestamo && !empty($prestamo->numero_coutas)) ? intval($prestamo->numero_coutas) : 0;
            $monto_credito_info = ($prestamo && isset($prestamo->monto_credito)) ? floatval($prestamo->monto_credito) : $monto_credito;
            $tasa_raw = ($prestamo && isset($prestamo->interes_credito)) ? floatval($prestamo->interes_credito) : 0;
            $comision_raw = ($prestamo && isset($prestamo->comision_desembolso)) ? floatval($prestamo->comision_desembolso) : 0;
            // Normalizar porcentajes para cubrir datos legacy (0.12) y porcentaje directo (12)
            $tasa_pct = ($tasa_raw > 1) ? $tasa_raw : ($tasa_raw * 100);
            $comision_pct = ($comision_raw > 1) ? $comision_raw : ($comision_raw * 100);

            // Monto de cuota: tomar la primera cuota del plan generado
            $cuota_row = $this->db->select('cuota')
                ->from('tb_prestamo_cuotas')
                ->where('idprestamo', $idprestamo)
                ->order_by('numero', 'ASC')
                ->limit(1)
                ->get()->row();
            $monto_cuota = ($cuota_row && isset($cuota_row->cuota)) ? floatval($cuota_row->cuota) : 0;

            // No. cheque cronológico por cuenta bancaria
            $row_num = $this->db->query(
                'SELECT MAX(CAST(numero_cheque AS UNSIGNED)) AS max_cheque FROM teso_movimientos WHERE cuenta_id = ? AND forma_pago = ? AND numero_cheque IS NOT NULL AND numero_cheque <> ""',
                array($cuenta_bancaria_id, 'CHEQUE')
            )->row();
            $numero_cheque = ($row_num && !empty($row_num->max_cheque)) ? (intval($row_num->max_cheque) + 1) : 1;

            $concepto = 'Cliente: ' . $cliente_nombre
                . ' | Monto crédito: ' . number_format($monto_credito_info, 2, '.', '')
                . ' | Tasa: ' . number_format($tasa_pct, 2, '.', '') . '%'
                . ' | Comisión: ' . number_format($comision_pct, 2, '.', '') . '%'
                . ' | Plazo: ' . $plazo;

            $descripcion = 'Solicitud de desembolso';
            if (!empty($referencia1)) {
                $descripcion .= ' (' . $referencia1 . ')';
            }

            $costos_aplicados = [];
            if ($costos_legales > 0) {
                $costos_aplicados[] = 'costos legales';
            }
            if ($seguros > 0) {
                $costos_aplicados[] = 'seguros';
            }
            if ($comisiones > 0) {
                $costos_aplicados[] = 'comisiones';
            }

            $referencia1 = '';
            if (!empty($costos_aplicados)) {
                $referencia1 = 'Incluye: ' . implode(', ', $costos_aplicados);
            }

            $preview_meta = http_build_query(array(
                'p' => $idprestamo,
                'fd' => $fecha_desembolso,
                'pp' => $primer_dia_pago,
                'cl' => number_format($costos_legales, 2, '.', ''),
                'sg' => number_format($seguros, 2, '.', ''),
                'cm' => number_format($comisiones, 2, '.', ''),
                'rn' => number_format($total_renovacion, 2, '.', ''),
                'rp' => number_format($renov_principal, 2, '.', ''),
                'rc' => number_format($renov_interes_corriente, 2, '.', ''),
                'rm' => number_format($renov_interes_mora, 2, '.', '')
            ));

            $preview_estado = 'previsualizacion';
            $preview_tipo = 'desembolso_preview';

            $existing_preview = $this->db->select('id')->from('teso_movimientos')
                ->where('tipo_movimiento', 'cheque')
                ->where('estado', 'previsualizacion')
                ->where('tipo', $preview_tipo)
                ->like('referencia2', 'p=' . $idprestamo, 'after')
                ->limit(1)
                ->get()->row();
            
            // 4. Crear movimiento de cheque en tesoreria
            $movimiento_data = [
                'tipo_movimiento' => 'cheque',
                'forma_pago' => 'CHEQUE',
                'concepto' => $concepto,
                'descripcion' => $descripcion,
                'fecha_registro' => $fecha_desembolso,
                'fecha_aplicacion' => $fecha_desembolso,
                'monto_total' => $total_desembolso,
                'iva_total' => 0,
                'cuenta_id' => $cuenta_bancaria_id,
                'tipo_transferencia' => 'cargo',
                'departamento' => null,
                'centro_costos' => null,
                'proyecto' => null,
                'beneficiario' => $cliente_nombre,
                'referencia1' => $referencia1,
                'referencia2' => $preview_meta,
                'numero_cheque' => (string)$numero_cheque,
                'estado' => $preview_estado,
                'tipo' => $preview_tipo,
                'creado_por' => $this->session->userdata('username') ?: $this->session->userdata('user_id'),
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            
            // Insertar o actualizar la solicitud de desembolso en tesorería
            if ($existing_preview && isset($existing_preview->id)) {
                $this->db->where('id', intval($existing_preview->id));
                if (!$this->db->update('teso_movimientos', $movimiento_data)) {
                    throw new Exception('Error actualizando solicitud de desembolso');
                }
            } else {
                if(!$this->db->insert('teso_movimientos', $movimiento_data)) {
                    throw new Exception('Error creando solicitud de desembolso');
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Solicitud de desembolso creada. Finalízala desde Tesorería.',
                'idprestamo' => $idprestamo,
                'monto_desembolsado' => $total_desembolso
            ]);
            
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
