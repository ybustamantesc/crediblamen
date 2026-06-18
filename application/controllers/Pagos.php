<?php
defined('BASEPATH') or exit('Acción no permitida');
require_once('./dompdf/autoload.inc.php');
require_once APPPATH . 'third_party/fpdf/fpdf.php';


use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

class Pagos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('pagos_model');
        $this->load->model('prestamos_model');
        $this->load->model('Tesoreria_model');
    }

    // Return clients that have active credits (so only clients with an emitted plan/credito appear)
    private function _get_clients_with_credit()
    {
        $this->db->select('tb_clientes.*');
        $this->db->from('tb_clientes');
        $this->db->join('tb_creditos', 'tb_creditos.idcliente = tb_clientes.idcliente', 'left');
        $this->db->join('tb_solicitudes', 'tb_solicitudes.numero_doc = tb_clientes.numero_doc', 'left');
        $this->db->join('tb_prestamos', 'tb_prestamos.idsolicitud = tb_solicitudes.idsolicitud', 'left');
        $this->db->where('(tb_creditos.id IS NOT NULL OR tb_prestamos.idprestamo IS NOT NULL)');
        $this->db->group_by('tb_clientes.idcliente');
        return $this->db->get()->result();
    }

    public function index()
    {
        // collect filters from GET
        $filters = array();
        $filters['date_from'] = $this->input->get('date_from');
        $filters['date_to'] = $this->input->get('date_to');
        $filters['q'] = $this->input->get('q');
        $filters['idserie'] = $this->input->get('idserie');
        $filters['referencia'] = $this->input->get('referencia');

        $data = array(
            'titulo' => 'Gestión de Pagos',
            'subtitulo' => 'Listar, Buscar.',
            'icono' => 'fas fa-comment-dollar ',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js',
                'js/caja/validarCaja.js',
                'js/pagos/formatos.js'
            ),
            'pagos' => $this->pagos_model->get_all_pagos(),
            'prestamo_pagos' => $this->pagos_model->get_all_prestamo_pagos($filters),
            'series_recibos' => $this->core_model->get_all('tb_series_recibos'),
            'pagos_provisionales' => $this->Tesoreria_model->get_pagos_pendientes(array())
        );
        $this->load->view('layout/header', $data);
        $this->load->view('pagos/index');
        $this->load->view('layout/footer');
    }

    public function core($pago_id = NULL)
    {
        if (!$pago_id) {
            #Registrar
            $this->form_validation->set_rules('idcliente', 'Cliente', 'trim|required');
            $this->form_validation->set_rules('idcredito', 'Crédito', 'trim|required');
            $this->form_validation->set_rules('idcuota', 'Crédito', 'trim|required');
            $this->form_validation->set_rules('monto_pago', 'Monto Pago', 'trim|required');
            // Prepare data for the view so variables used in the view are defined
            $data = array(
                'titulo' => 'Registrar Nuevo Pago',
                'subtitulo' => 'Ingrese los datos solicitados',
                'icono_view' => 'fas fa-comment-dollar ',
                'styles' => array(
                    'plugins/select2/dist/css/select2.min.css'
                ),
                'scripts' => array(
                    'plugins/select2/dist/js/select2.min.js',
                    'plugins/jqueryNumber/jquerynumber.min.js',
                    'js/pagos/utils.js'
                ),
                'clientes' => $this->_get_clients_with_credit()
            );

            $this->load->view('layout/header', $data);
            $this->load->view('pagos/core');
            $this->load->view('layout/footer');
        }
    }

    public function getCreditosCliente()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                redirect($this->router->fetch_class());
            }
            $cliente_id = $this->input->post('cliente_id');
            $output = '<option value="" selected>SELECCIONAR</option>';
            if ($cliente_id) {
                $cliente = null;
                // Support DOC:numero_doc values coming from client selector (when no client record exists)
                if (is_string($cliente_id) && strpos($cliente_id, 'DOC:') === 0) {
                    $numero_doc = substr($cliente_id, 4);
                    $cliente = (object) array('numero_doc' => $numero_doc);
                } else {
                    // If numeric, try fetch by id; otherwise attempt to fetch, and if not found treat as numero_doc
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
                    // prestamos (plans) - use direct SQL to match solicitudes linked to the cliente
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
                            $sql = "SELECT p.* FROM tb_prestamos p JOIN tb_solicitudes s ON s.idsolicitud = p.idsolicitud WHERE s.numero_doc = ?";
                            $prestamos = $this->db->query($sql, array($numero_doc))->result();
                        } elseif (ctype_digit((string)$cliente_id)) {
                            $sql = "SELECT p.* FROM tb_prestamos p JOIN tb_solicitudes s ON s.idsolicitud = p.idsolicitud WHERE s.idcliente = ?";
                            $prestamos = $this->db->query($sql, array($cliente_id))->result();
                        } else {
                            $prestamos = array();
                        }
                        log_message('debug', 'getCreditosCliente prestamos_found=' . count($prestamos) . ' sql=' . $this->db->last_query());
                    } catch (Exception $e) {
                        log_message('error', 'getCreditosCliente SQL error: ' . $e->getMessage());
                        $prestamos = array();
                    }
                    $prestamo_ids = array();
                    if ($prestamos) {
                        foreach ($prestamos as $p) {
                            $output .= '<option value="P-' . $p->idprestamo . '">PLAN-' . $p->idprestamo . '</option>';
                            $prestamo_ids[] = isset($p->idprestamo) ? $p->idprestamo : (isset($p->id) ? $p->id : null);
                        }
                    }
                    // legacy tb_creditos
                    $creditos = $this->core_model->get_all('tb_creditos', array('idcliente' => $cliente_id, 'estado!=' => 0));
                    if ($creditos) {
                        foreach ($creditos as $row) {
                            $output .= '<option value="' . $row->id . '">' . $row->id . '</option>';
                        }
                    }
                }
            }
            // Log for debugging: cliente and output length
            log_message('debug', 'getCreditosCliente cliente_id=' . print_r($cliente_id, true) . ' output_len=' . strlen($output));
            $resp = array('status' => true, 'html' => $output);
            // In non-production, include debug details to aid troubleshooting
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                $resp['debug'] = array('cliente' => isset($cliente) ? $cliente : null, 'numero_doc' => isset($numero_doc) ? $numero_doc : null, 'prestamo_ids' => isset($prestamo_ids) ? $prestamo_ids : array());
            }
            echo json_encode($resp);
        } catch (Throwable $e) {
            log_message('error', 'getCreditosCliente error: ' . $e->getMessage());
            $resp = array('status' => false, 'error' => $e->getMessage());
            echo json_encode($resp);
        } catch (Exception $e) {
            log_message('error', 'getCreditosCliente exception: ' . $e->getMessage());
            $resp = array('status' => false, 'error' => $e->getMessage());
            echo json_encode($resp);
        }
    }

    public function pagartodo()
    {
        $this->form_validation->set_rules('cliente', 'Cliente', 'trim|required');
        $this->form_validation->set_rules('total_pagar_creditos', 'Total', 'trim|required|greater_than[0]');
        if ($this->form_validation->run()) {
            $data['idcliente'] = $this->input->post('cliente');
            $data['idcredito'] = $this->input->post('credito_id');
            $data['total_saldo'] = $this->input->post('total_pagar_creditos');

            foreach ($data['idcredito'] as $credito) {
                $datosCredito = $this->core_model->get_by_id('tb_creditos', array('id' => $credito));
                $dataPago = array(
                    'idcliente' => $data['idcliente'],
                    'idcredito' => $credito,
                    'monto_pago' => $datosCredito->total_saldo,
                    'idusuario' => $this->ion_auth->get_user_id()
                );
                $this->core_model->insert('tb_pagos', $dataPago);
                $idpago = $this->db->insert_id();

                $datosCoutas = $this->core_model->get_by_id_all('tb_credito_detalle', array('idcredito' => $credito, 'estado_couta!=' => 0));

                foreach ($datosCoutas as $cuota) {
                    $this->core_model->insert('tb_pagos_detalle', array('idpago' => $idpago, 'idcuota' => $cuota->id, 'monto_pagado' => $cuota->monto_pendiente));

                    $dataCuota = array(
                        'fecha_pago' => date("Y-m-d H:i:s"),
                        'monto_pagado' => $cuota->monto_pendiente,
                        'monto_pendiente' => '0',
                        'estado_couta' => '0'
                    );
                    $this->core_model->update('tb_credito_detalle', $dataCuota, array('id' => $cuota->id));
                    // Apply accounting mapping per cuota (bulk)
                    $this->load->model('Contabilidad_model');
                    $creator = $this->ion_auth->get_user_id();
                    $capital = floatval($cuota->monto_capital);
                    $interes = floatval($cuota->monto_interes);
                    if ($capital > 0) {
                        $ok = $this->Contabilidad_model->apply_accounting_rule('loan_payment_principal', ['amount' => $capital, 'date' => date("Y-m-d"), 'description' => 'Pago capital cuota ' . $cuota->id . ' pago #' . $idpago, 'created_by' => $creator, 'source_type' => 'pago', 'source_id' => $idpago]);
                        if (!$ok) log_message('error', 'Contabilidad: fallo al aplicar regla loan_payment_principal para pago bulk ' . $idpago);
                    }
                    if ($interes > 0) {
                        $ok2 = $this->Contabilidad_model->apply_accounting_rule('loan_payment_interest', ['amount' => $interes, 'date' => date("Y-m-d"), 'description' => 'Pago interes cuota ' . $cuota->id . ' pago #' . $idpago, 'created_by' => $creator, 'source_type' => 'pago', 'source_id' => $idpago]);
                        if (!$ok2) log_message('error', 'Contabilidad: fallo al aplicar regla loan_payment_interest para pago bulk ' . $idpago);
                    }
                }
                $this->core_model->update('tb_creditos', array('estado' => 0, 'total_saldo' => 0), array('id' => $credito));

                $fechaHoraActual = date("Y-m-d H:i:s");
                $fechActual = date("Y-m-d");
                // No registrar en tb_caja_movimiento: la tesorería maneja los movimientos.
            }

            redirect($this->router->fetch_class());
        } else {
            $data = array(
                'titulo' => 'Registrar Pago Masivo de Créditos',
                'subtitulo' => 'Ingrese los datos solicitados',
                'icono_view' => 'fas fa-comment-dollar ',
                'styles' => array(
                    'plugins/select2/dist/css/select2.min.css',
                    'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
                ),
                'scripts' => array(
                    'plugins/datatables.net/js/jquery.dataTables.min.js',
                    'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                    'plugins/select2/dist/js/select2.min.js',
                    'plugins/jqueryNumber/jquerynumber.min.js',
                    'plugins/datatables.net/js/activaDatatable.js',
                    'js/pagos/creditos.js'
                ),
                'clientes' => $this->core_model->get_all('tb_clientes')
            );
            $this->load->view('layout/header', $data);
            $this->load->view('pagos/creditos');
            $this->load->view('layout/footer');
        }
    }

    public function getCreditoId()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $credito_id = $this->input->post('credito_id');
        $data = $this->core_model->get_by_id('tb_creditos', array('id' => $credito_id));
        echo json_encode($data);
    }

    public function getCuotaId()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $cuota_id = $this->input->post('cuota_id');
        $data = $this->core_model->get_by_id('tb_credito_detalle', array('id' => $cuota_id));
        echo json_encode($data);
    }

    // AJAX: return next pending cuota for a prestamo (from tb_prestamo_cuotas)
    public function getPrestamoNextCuota()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $idprestamo = $this->input->post('idprestamo');
        $resp = array('status' => false, 'html' => '<option value="">SELECCIONAR</option>');
        if (!$idprestamo) { echo json_encode($resp); return; }

        // More robust: fetch all cuotas and compute remaining per cuota using payments; also compute total pending
        $this->db->from('tb_prestamo_cuotas');
        $this->db->where('idprestamo', $idprestamo);
        $this->db->order_by('numero', 'ASC');
        $cuotas = $this->db->get()->result();
        log_message('debug', 'getPrestamoNextCuota idprestamo=' . print_r($idprestamo, true) . ' cuotas_found=' . count($cuotas));
        $chosen = null;
        $total_pending = 0;
        if (!empty($cuotas)) {
            foreach ($cuotas as $c) {
                $c_id = isset($c->idcuota) ? $c->idcuota : (isset($c->id) ? $c->id : null);
                $cuota_val = isset($c->cuota) ? floatval($c->cuota) : 0;
                $saldo_col = isset($c->saldo) ? floatval($c->saldo) : null;

                // compute paid amount from payments table
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
                // compute remaining as cuota - paid
                $remaining = $cuota_val - $paid;
                if ($remaining < 0) $remaining = 0;
                // accumulate total pending across cuotas
                $total_pending += $remaining;
                if ($remaining > 0) {
                    $chosen = array('raw' => $c, 'id' => $c_id, 'numero' => isset($c->numero) ? $c->numero : null, 'cuota' => $cuota_val, 'saldo' => $remaining, 'fecha_vencimiento' => isset($c->fecha_vencimiento) ? $c->fecha_vencimiento : '');
                    // if saldo column exists but differs, update it for future queries
                    if (!is_null($saldo_col) && abs(floatval($saldo_col) - $remaining) > 0.01 && !empty($c_id) && $this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                        $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $remaining), array('idcuota' => $c_id));
                    } elseif (is_null($saldo_col) && !empty($c_id) && $this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                        $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $remaining), array('idcuota' => $c_id));
                    }
                    break;
                }
            }
        }

        // after loop, if chosen exists include total_pending in response
        log_message('debug', 'getPrestamoNextCuota chosen=' . print_r($chosen, true) . ' total_pending=' . $total_pending);
        if ($chosen) {
            $cuota_id = $chosen['id'] ? $chosen['id'] : ('N-' . $chosen['numero']);
            $html = '<option value="' . $cuota_id . '">CUOTA ' . ($chosen['numero'] ? $chosen['numero'] : $cuota_id) . '</option>';
            $estado_info = $this->_calcular_estado_cuota($chosen, $idprestamo, $total_pending);
            $principal_cuota = (isset($chosen['raw']->principal) && $chosen['raw']->principal !== null) ? floatval($chosen['raw']->principal) : 0.0;
            $interes_corriente = (isset($chosen['raw']->interes) && $chosen['raw']->interes !== null) ? floatval($chosen['raw']->interes) : 0.0;
            $interes_moratorio = (isset($chosen['raw']->monto_mora) && $chosen['raw']->monto_mora !== null) ? floatval($chosen['raw']->monto_mora) : 0.0;

            $cuotas_atrasadas = 0;
            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idprestamo', $idprestamo);
            $this->db->where('fecha_vencimiento <', date('Y-m-d'));
            if ($this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                $this->db->where('(saldo IS NULL OR saldo > 0)', null, false);
            }
            $cuotas_atrasadas = intval($this->db->count_all_results());

            $resp = array(
                'status' => true,
                'html' => $html,
                'cuota' => array(
                    'idcuota' => $cuota_id,
                    'numero' => $chosen['numero'],
                    'cuota' => $chosen['cuota'],
                    'saldo' => $chosen['saldo'],
                    'fecha_vencimiento' => $chosen['fecha_vencimiento'],
                    'principal' => $principal_cuota,
                    'interes_corriente' => $interes_corriente,
                    'interes_moratorio' => $interes_moratorio,
                    'cuotas_atrasadas' => $cuotas_atrasadas,
                    'estado' => $estado_info['estado_cuota'],
                    'dias_atraso' => $estado_info['dias_atraso'],
                    'mora' => $estado_info['mora'],
                    'total_pagar' => $estado_info['total_pagar'],
                    'estado_cliente' => $estado_info['estado_cliente'],
                    'estado_credito' => $estado_info['estado_credito']
                ),
                'total_pending' => $total_pending,
                'estado_cliente' => $estado_info['estado_cliente'],
                'estado_credito' => $estado_info['estado_credito']
            );
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                $resp['debug'] = array('chosen' => $chosen, 'total_pending' => $total_pending);
            }
        }
        echo json_encode($resp);
    }

    private function _calcular_estado_cuota($chosen, $idprestamo, $total_pending)
    {
        $fecha_vencimiento = isset($chosen['fecha_vencimiento']) ? substr((string)$chosen['fecha_vencimiento'], 0, 10) : '';
        $dias_atraso = 0;
        $estado_cuota = 'Fecha Vigente';
        $estado_cliente = 'Vigente';

        if (!empty($fecha_vencimiento)) {
            $due = strtotime($fecha_vencimiento);
            $today = strtotime(date('Y-m-d'));
            if ($today > $due) {
                $dias_atraso = intval(($today - $due) / 86400);
                if ($dias_atraso <= 15) {
                    $estado_cuota = 'Mora de 1 a 15 días';
                    $estado_cliente = 'Mora temprana';
                } elseif ($dias_atraso <= 30) {
                    $estado_cuota = 'Mora de 16 a 30 días';
                    $estado_cliente = 'Mora';
                } elseif ($dias_atraso <= 60) {
                    $estado_cuota = 'Mora de 31 a 60 días';
                    $estado_cliente = 'Mora media';
                } elseif ($dias_atraso <= 90) {
                    $estado_cuota = 'Mora de 61 a 90 días';
                    $estado_cliente = 'Mora alta';
                } elseif ($dias_atraso <= 120) {
                    $estado_cuota = 'Mora de 91 a 120 días';
                    $estado_cliente = 'Cartera en riesgo';
                } elseif ($dias_atraso <= 180) {
                    $estado_cuota = 'Mora de 121 a 180 días';
                    $estado_cliente = 'Cartera dudosa';
                } elseif ($dias_atraso <= 240) {
                    $estado_cuota = 'Mora de 181 a 240 días';
                    $estado_cliente = 'Cartera crítica';
                } elseif ($dias_atraso <= 360) {
                    $estado_cuota = 'Mora de 241 a 360 días';
                    $estado_cliente = 'Cartera irrecuperable';
                } else {
                    $estado_cuota = 'Mora mayor a 361 días';
                    $estado_cliente = 'Cartera castigada';
                }
            }
        }

        $cuota_base = isset($chosen['cuota']) ? floatval($chosen['cuota']) : 0;
        $saldo = isset($chosen['saldo']) ? floatval($chosen['saldo']) : 0;
        $mora = 0.0;
        if ($dias_atraso > 0) {
            $tasa_mora_diaria = 0.0005;
            $prestamo = $this->core_model->get_by_id('tb_prestamos', array('idprestamo' => $idprestamo));
            if ($prestamo && isset($prestamo->tasa_moratoria) && floatval($prestamo->tasa_moratoria) > 0) {
                $tasa_mora_diaria = floatval($prestamo->tasa_moratoria) / 360;
            }
            $mora = round($saldo * $tasa_mora_diaria * $dias_atraso, 2);
        }

        return array(
            'dias_atraso' => $dias_atraso,
            'estado_cuota' => $estado_cuota,
            'estado_cliente' => $estado_cliente,
            'estado_credito' => $estado_cliente,
            'mora' => $mora,
            'total_pagar' => round($saldo + $mora, 2),
            'cuota_base' => $cuota_base,
            'saldo' => $saldo
        );
    }

    // AJAX: return total saldo for a prestamo
    public function getPrestamoSaldo()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $idprestamo = $this->input->post('idprestamo');
        $resp = array('status' => false, 'total_saldo' => 0);
        if (!$idprestamo) { echo json_encode($resp); return; }
        // compute total remaining across cuotas from cuota - SUM(pagos)
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

    // New: view to register prestamos payments
    public function prestamos_core()
    {
        $data = array(
            'titulo' => 'Registrar Pago Provisional',
            'subtitulo' => 'Seleccione cliente y préstamo para enviar a tesorería',
            'icono_view' => 'fas fa-comment-dollar ',
            'styles' => array(
                'plugins/select2/dist/css/select2.min.css'
            ),
            'scripts' => array(
                'plugins/select2/dist/js/select2.min.js',
                'plugins/jqueryNumber/jquerynumber.min.js',
                'js/pagos/prestamos.js'
            )
        );

        $this->load->view('layout/header', $data);
        $this->load->view('pagos/core_prestamos');
        $this->load->view('layout/footer');
    }

    // AJAX: registrar pago provisional pendiente en tesorería (no aplica a cuota todavía)
    public function savePrestamoPagoProvisional()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }

        $cliente_id = $this->input->post('cliente_id');
        $idcredito = $this->input->post('idcredito');
        $idcuota = $this->input->post('idcuota');
        $monto = floatval($this->input->post('monto'));
        $metodo = trim((string)$this->input->post('metodo'));
        $moneda = strtoupper(trim((string)$this->input->post('moneda')));
        $tc_compra = floatval($this->input->post('tc_compra'));
        $tc_venta = floatval($this->input->post('tc_venta'));
        $monto_usd = floatval($this->input->post('monto_usd'));
        $monto_nio = floatval($this->input->post('monto_nio'));
        $referencia = trim((string)$this->input->post('referencia'));
        $dato_adicional = trim((string)$this->input->post('dato_adicional'));
        $fecha_pago = $this->input->post('fecha_pago');

        $idserie_sel = null;
        if ($referencia !== '' && ctype_digit((string)$referencia)) {
            $idserie_sel = intval($referencia);
        }

        $idprestamo = null;
        if (is_string($idcredito) && strpos($idcredito, 'P-') === 0) {
            $idprestamo = intval(substr($idcredito, 2));
        } elseif (ctype_digit((string)$idcredito)) {
            $idprestamo = intval($idcredito);
        }

        if (!$idprestamo || !$idcuota || $monto <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Datos incompletos para pago provisional'));
            return;
        }

        if (empty($idserie_sel)) {
            echo json_encode(array('status' => false, 'message' => 'Debe seleccionar la serie de recibo'));
            return;
        }

        if ($moneda === '') $moneda = 'USD';
        if ($metodo === '') $metodo = 'efectivo';

        if ($metodo === 'transferencia' && $moneda !== 'USD') {
            echo json_encode(array('status' => false, 'message' => 'Transferencia solo se permite en USD'));
            return;
        }

        // Asegurar esquema mínimo para poder guardar y mostrar datos completos en Tesorería.
        $this->_ensure_teso_pagos_columns_for_provisional();

        $fecha_base = date('Y-m-d');
        if (!empty($fecha_pago)) {
            $ts = strtotime($fecha_pago);
            if ($ts !== false) {
                $fecha_base = date('Y-m-d', $ts);
            }
        }

        // Buscar datos de cliente y cuota para describir el pendiente
        $cliente_nombre = 'Cliente #' . $cliente_id;
        $cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
        if ($cliente) {
            $cliente_nombre = trim((isset($cliente->apellidos) ? $cliente->apellidos : '') . ' ' . (isset($cliente->nombres) ? $cliente->nombres : ''));
            if ($cliente_nombre === '') {
                $cliente_nombre = isset($cliente->razon_social) ? $cliente->razon_social : $cliente_nombre;
            }
        }

        $cuota_num = $idcuota;
        $dias_mora = 0;
        $monto_mora = 0;
        $cuota = $this->core_model->get_by_id('tb_prestamo_cuotas', array('idcuota' => $idcuota));
        if ($cuota) {
            if (isset($cuota->numero) && $cuota->numero !== null) $cuota_num = $cuota->numero;
            if (!empty($cuota->dias_mora_manual)) $dias_mora = intval($cuota->dias_mora_manual);
            if (!empty($cuota->monto_mora)) $monto_mora = floatval($cuota->monto_mora);
        }

        // Seleccionar cuenta de tesorería por defecto
        $cuenta_id = 1;
        if ($this->db->table_exists('teso_accounts')) {
            $acc = $this->db->where('estado', 1)->order_by('id', 'ASC')->limit(1)->get('teso_accounts')->row();
            if ($acc && isset($acc->id)) $cuenta_id = intval($acc->id);
        }

        $medio_pago = strtolower($metodo);
        if (!in_array($medio_pago, array('transferencia', 'cheque', 'efectivo', 'tarjeta'))) {
            $medio_pago = 'efectivo';
        }

        $serie_codigo = '';
        $serie_consecutivo_asignado = null;
        if (!empty($idserie_sel) && $this->db->table_exists('tb_series_recibos')) {
            // Reservar consecutivo desde el registro provisional para mostrar referencia completa desde el inicio.
            $srForUpdate = $this->db->select('idserie, codigo, nombre, consecutivo')
                ->from('tb_series_recibos')
                ->where('idserie', $idserie_sel)
                ->limit(1)
                ->get()
                ->row();
            if ($srForUpdate && !empty($srForUpdate->codigo)) {
                $serie_codigo = trim((string)$srForUpdate->codigo);
                $current = isset($srForUpdate->consecutivo) ? intval($srForUpdate->consecutivo) : 0;
                $serie_consecutivo_asignado = $current + 1;
                $this->db->where('idserie', $idserie_sel);
                $this->db->update('tb_series_recibos', array(
                    'consecutivo' => $serie_consecutivo_asignado,
                    'ultimo_emitido' => $serie_consecutivo_asignado,
                    'updated_on' => time()
                ));
            }
        }

        $referencia_visible = $referencia;
        if ($serie_codigo !== '') {
            if (!is_null($serie_consecutivo_asignado) && $serie_consecutivo_asignado > 0) {
                $referencia_visible = $serie_codigo . str_pad($serie_consecutivo_asignado, 10, '0', STR_PAD_LEFT);
            } else {
                $referencia_visible = $serie_codigo;
            }
        }

        $estado_pendiente = 'registrado'; // En teso_pagos se interpreta como pendiente de ejecución/aplicación
        $concepto = 'Pago provisional préstamo #' . $idprestamo . ' cuota #' . $cuota_num;
        if ($dias_mora > 0 || $monto_mora > 0) {
            $concepto .= ' | Mora: ' . $dias_mora . ' días / $' . number_format($monto_mora, 2);
        }

        // Insert tolerante a variaciones de esquema en teso_pagos
        $tableFields = array();
        if ($this->db->table_exists('teso_pagos')) {
            $tableFields = $this->db->list_fields('teso_pagos');
        }
        $fieldMap = array_flip($tableFields);
        $hasField = function ($name) use ($fieldMap) {
            return isset($fieldMap[$name]);
        };
        $putFirstExisting = function (&$target, $candidates, $value) use ($hasField) {
            foreach ($candidates as $candidate) {
                if ($hasField($candidate)) {
                    $target[$candidate] = $value;
                    return true;
                }
            }
            return false;
        };

        $dataPago = array();
        $putFirstExisting($dataPago, array('fecha', 'fecha_pago', 'fecha_registro'), $fecha_base);
        $putFirstExisting($dataPago, array('cuenta_id', 'idcuenta', 'account_id'), $cuenta_id);
        $putFirstExisting($dataPago, array('beneficiario', 'nombre_beneficiario', 'beneficiary'), $cliente_nombre);
        $putFirstExisting($dataPago, array('concepto', 'descripcion', 'detalle'), $concepto);
        $putFirstExisting($dataPago, array('moneda', 'currency'), $moneda);
        $putFirstExisting($dataPago, array('monto', 'total', 'importe'), $monto);
        $putFirstExisting($dataPago, array('medio_pago', 'metodo_pago', 'metodo', 'forma_pago'), $medio_pago);
        $putFirstExisting($dataPago, array('documento_tipo', 'tipo_documento', 'doc_tipo'), 'RECIBO');
        $putFirstExisting($dataPago, array('documento_numero', 'numero_documento', 'referencia', 'doc_numero'), $referencia_visible);
        $putFirstExisting($dataPago, array('usuario_id', 'idusuario', 'user_id'), $this->ion_auth->get_user_id());
        $putFirstExisting($dataPago, array('estado', 'status'), $estado_pendiente);
        $putFirstExisting($dataPago, array('created_at', 'fecha_creacion', 'createdon'), date('Y-m-d H:i:s'));

        // Completar campos opcionales si existen
        if ($hasField('idprestamo')) $dataPago['idprestamo'] = $idprestamo;
        if ($hasField('idcuota')) $dataPago['idcuota'] = $idcuota;
        if ($hasField('idcliente')) $dataPago['idcliente'] = $cliente_id;
        if ($hasField('idserie') && !empty($idserie_sel)) $dataPago['idserie'] = $idserie_sel;
        if ($hasField('serie_codigo') && $serie_codigo !== '') $dataPago['serie_codigo'] = $serie_codigo;
        if ($hasField('tc_compra') && $tc_compra > 0) $dataPago['tc_compra'] = $tc_compra;
        if ($hasField('tc_venta') && $tc_venta > 0) $dataPago['tc_venta'] = $tc_venta;
        if ($hasField('monto_usd')) $dataPago['monto_usd'] = round(max(0, $monto_usd), 2);
        if ($hasField('monto_nio')) $dataPago['monto_nio'] = round(max(0, $monto_nio), 2);
        if ($hasField('monto_total_usd')) $dataPago['monto_total_usd'] = round(max(0, $monto), 2);
        if ($hasField('proveedor_id') && !$hasField('idcliente')) $dataPago['proveedor_id'] = $cliente_id;
        if ($hasField('dato_adicional')) $dataPago['dato_adicional'] = $dato_adicional;
        if ($hasField('fecha_programada')) $dataPago['fecha_programada'] = $fecha_base;

        // Si existen restricciones de enum distintas, probar fallback de estado
        $db_debug_backup = $this->db->db_debug;
        $this->db->db_debug = false;
        $ok = $this->db->insert('teso_pagos', $dataPago);
        if (!$ok) {
            $fallback = $dataPago;
            if ($hasField('estado')) {
                $fallback['estado'] = 'programado';
            } elseif ($hasField('status')) {
                $fallback['status'] = 'programado';
            }
            $ok = $this->db->insert('teso_pagos', $fallback);
            if (!$ok) {
                $err = $this->db->error();
                $this->db->db_debug = $db_debug_backup;
                echo json_encode(array('status' => false, 'message' => 'No se pudo registrar pago provisional: ' . (isset($err['message']) ? $err['message'] : 'error DB')));
                return;
            }
        }
        $id = $this->db->insert_id();
        $this->db->db_debug = $db_debug_backup;

        echo json_encode(array(
            'status' => true,
            'message' => 'Pago provisional enviado a Tesorería como pendiente',
            'id' => $id,
              'redirect' => base_url('pagos?date_from=&date_to=&q=&idserie=')
        ));
    }

    // Backfill de columnas para ambientes con esquema viejo de teso_pagos.
    private function _ensure_teso_pagos_columns_for_provisional()
    {
        if (!$this->db->table_exists('teso_pagos')) {
            return;
        }

        $defs = array(
            'beneficiario' => "ALTER TABLE teso_pagos ADD COLUMN beneficiario VARCHAR(191) NULL",
            'concepto' => "ALTER TABLE teso_pagos ADD COLUMN concepto VARCHAR(255) NULL",
            'medio_pago' => "ALTER TABLE teso_pagos ADD COLUMN medio_pago VARCHAR(50) NULL",
            'moneda' => "ALTER TABLE teso_pagos ADD COLUMN moneda VARCHAR(10) NULL",
            'documento_tipo' => "ALTER TABLE teso_pagos ADD COLUMN documento_tipo VARCHAR(50) NULL",
            'documento_numero' => "ALTER TABLE teso_pagos ADD COLUMN documento_numero VARCHAR(120) NULL",
            'usuario_id' => "ALTER TABLE teso_pagos ADD COLUMN usuario_id INT NULL",
            'idprestamo' => "ALTER TABLE teso_pagos ADD COLUMN idprestamo INT NULL",
            'idcuota' => "ALTER TABLE teso_pagos ADD COLUMN idcuota INT NULL",
            'idcliente' => "ALTER TABLE teso_pagos ADD COLUMN idcliente INT NULL",
            'idserie' => "ALTER TABLE teso_pagos ADD COLUMN idserie INT NULL",
            'serie_codigo' => "ALTER TABLE teso_pagos ADD COLUMN serie_codigo VARCHAR(20) NULL",
            'tc_compra' => "ALTER TABLE teso_pagos ADD COLUMN tc_compra DECIMAL(10,4) NULL",
            'tc_venta' => "ALTER TABLE teso_pagos ADD COLUMN tc_venta DECIMAL(10,4) NULL",
            'monto_usd' => "ALTER TABLE teso_pagos ADD COLUMN monto_usd DECIMAL(18,2) NULL",
            'monto_nio' => "ALTER TABLE teso_pagos ADD COLUMN monto_nio DECIMAL(18,2) NULL",
            'monto_total_usd' => "ALTER TABLE teso_pagos ADD COLUMN monto_total_usd DECIMAL(18,2) NULL",
            'dato_adicional' => "ALTER TABLE teso_pagos ADD COLUMN dato_adicional VARCHAR(255) NULL",
            'updated_at' => "ALTER TABLE teso_pagos ADD COLUMN updated_at DATETIME NULL",
            'fecha_aprobacion' => "ALTER TABLE teso_pagos ADD COLUMN fecha_aprobacion DATETIME NULL",
            'aprobado_por' => "ALTER TABLE teso_pagos ADD COLUMN aprobado_por INT NULL",
            'motivo_rechazo' => "ALTER TABLE teso_pagos ADD COLUMN motivo_rechazo VARCHAR(255) NULL"
        );

        foreach ($defs as $field => $sql) {
            if (!$this->db->field_exists($field, 'teso_pagos')) {
                try {
                    $this->db->query($sql);
                } catch (Exception $e) {
                    log_message('error', 'No se pudo crear columna ' . $field . ' en teso_pagos: ' . $e->getMessage());
                }
            }
        }
    }

    // Return latest tasa de cambio (JSON)
    public function get_latest_tasa()
    {
        if (!$this->input->is_ajax_request()) {
            // allow direct access too but return JSON
        }
        // support optional `tipo` GET param to prefer 'venta' or 'compra'
        $tipo = $this->input->get('tipo');
        $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
        if ($tasaRow) {
            $tasa = null;
            if ($tipo === 'venta') {
                if (!empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                    $tasa = floatval($tasaRow->tasa_venta);
                } elseif (!empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                    $tasa = floatval($tasaRow->tasa_cambio);
                }
            } else {
                if (!empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                    $tasa = floatval($tasaRow->tasa_cambio);
                } elseif (!empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                    $tasa = floatval($tasaRow->tasa_venta);
                }
            }
            echo json_encode(array('status' => true, 'tasa' => $tasa, 'row' => $tasaRow, 'tipo' => $tipo));
            return;
        }
        echo json_encode(array('status' => false, 'message' => 'No tasa disponible'));
    }

    // Print a prestamo pago as a simple receipt (FPDF)
    public function prestamo_pdf($id = NULL)
    {
        if (empty($id)) { show_error('Pago no especificado'); }
        $pdf = new FPDF('P', 'mm', array(80, 160));
        $pdf->SetMargins(5, 6, 5);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $p = $this->pagos_model->get_prestamo_pago_by_id($id);
        if (!$p) show_error('Pago no encontrado');
        $pdf->AddPage();
        $imagen_rel = 'public/img/sistema/' . $empresa->logotipo;
        $imagen_path = FCPATH . $imagen_rel;
        if (!empty($empresa->logotipo) && file_exists($imagen_path)) {
            $ext = strtolower(pathinfo($imagen_path, PATHINFO_EXTENSION));
            if ($ext === 'png' && function_exists('imagecreatefrompng') && function_exists('imagejpeg')) {
                // convert PNG to JPG temporarily because this FPDF version expects JPEG
                $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'logo_' . uniqid() . '.jpg';
                $img = @imagecreatefrompng($imagen_path);
                if ($img !== false) {
                    imagejpeg($img, $tmp, 90);
                    imagedestroy($img);
                    $pdf->Image($tmp, 20, 4, 40, 20, 'JPG');
                    @unlink($tmp);
                } else {
                    // fallback: try to pass filesystem path and let FPDF handle it
                    $pdf->Image($imagen_path, 20, 4, 40, 20);
                }
            } else {
                // let FPDF infer type from extension or accept jpg
                $pdf->Image($imagen_path, 20, 4, 40, 20);
            }
        }
        // Header: Company name + receipt title
        $pdf->setY(28);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode($empresa->slogan ?? 'RECIBO DE PAGO'), 0, 1, 'C');
        $pdf->Ln(2);

        // draw a subtle separator
        $pdf->SetDrawColor(230,230,230);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(4);

        // Details left / right aligned
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'RECIBO Nro:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        // If referencia (formatted series number) exists prefer showing it as recibo nro
        $recibo_display = !empty($p->referencia) ? $p->referencia : $p->id;
        $pdf->Cell(0, 6, $recibo_display, 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Cliente:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $cliente_name = trim(($p->apellidos ?? '') . ' ' . ($p->nombres ?? ''));
        $pdf->Cell(0, 6, utf8_decode($cliente_name), 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Prestamo:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, $p->idprestamo, 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Cuota:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, ($p->numero_cuota ? $p->numero_cuota : $p->idcuota), 0, 1, 'R');

        // Serie de recibo (si existe) - mostrar el consecutivo formateado si está disponible
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Serie:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $serie_label = (!empty($p->serie_codigo) ? $p->serie_codigo : (!empty($p->serie_nombre) ? $p->serie_nombre : ''));
        // preferir referencia formateada (ej. A001) almacenada en p.referencia
        $serie_display = !empty($p->referencia) ? $p->referencia : $serie_label;
        $pdf->Cell(0, 6, $serie_display, 0, 1, 'R');

        // Emitido por (usuario)
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Emitido por:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $emitido_por = !empty($p->emitido_por_firstname) ? $p->emitido_por_firstname : (!empty($p->emitido_por_lastname) ? $p->emitido_por_lastname : '');
        $pdf->Cell(0, 6, $emitido_por, 0, 1, 'R');

        $pdf->Ln(2);
        // Determine if payment was made in local currency or USD
        $paid_usd = floatval($p->monto_pagado);
        $paid_moneda = isset($p->moneda) ? strtoupper($p->moneda) : null;
        $monto_original = isset($p->monto_original) ? floatval($p->monto_original) : null;

        // get latest tasa for conversions (prefer compra)
        $tasa = null;
        $tasaRow = $this->db->order_by('fecha', 'DESC')->limit(1)->get('tb_tasa_cambio')->row();
        if ($tasaRow) {
            if (!empty($tasaRow->tasa_cambio) && floatval($tasaRow->tasa_cambio) > 0) {
                $tasa = floatval($tasaRow->tasa_cambio);
            } elseif (!empty($tasaRow->tasa_venta) && floatval($tasaRow->tasa_venta) > 0) {
                $tasa = floatval($tasaRow->tasa_venta);
            }
        }

        if ($paid_moneda === 'NIO' || ($monto_original > 0 && $paid_moneda === null)) {
            // Payment was in Córdobas: show original in C$, then equivalent USD
            $local_amount = $monto_original > 0 ? $monto_original : ($tasa ? $paid_usd * $tasa : null);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 8, 'Monto (C$):', 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, ($local_amount !== null ? 'C$ ' . number_format($local_amount, 2) : 'C$ 0.00'), 0, 1, 'R');

            if (!is_null($paid_usd)) {
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(40, 6, 'Equiv. USD:', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 10);
                $ttext = !is_null($tasa) ? ' (TC: ' . number_format($tasa, 4) . ')' : '';
                $pdf->Cell(0, 6, '$ ' . number_format($paid_usd, 2) . $ttext, 0, 1, 'R');
            }
        } else {
            // Default: show USD amount and equivalence in C$ if tasa available
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 8, 'Monto (USD):', 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, '$ ' . number_format($paid_usd, 2), 0, 1, 'R');

            if (!is_null($tasa) && $tasa > 0) {
                $equivalente = $paid_usd * $tasa;
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(40, 6, 'Equiv. C$:', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, 'C$ ' . number_format($equivalente, 2) . '  (TC: ' . number_format($tasa, 4) . ')', 0, 1, 'R');
            }
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Fecha:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, formatoFechaHora($p->fecha_pago), 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 6, 'Metodo:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, utf8_decode($p->metodo_pago ?: $p->metodo), 0, 1, 'R');

        /* referencia intentionally omitted from receipt */

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 6, utf8_decode($empresa->mensaje_ticket), 0, 1, 'C');
        $pdf->Output('RECIBO-PRESTAMO-' . $p->id . '.pdf', 'I');
    }

    // AJAX: return single prestamo pago data
    public function getPrestamoPagoAjax($id = NULL)
    {
        if (!$this->input->is_ajax_request()) { redirect($this->router->fetch_class()); }
        $id = intval($id);
        $row = $this->pagos_model->get_prestamo_pago_by_id($id);
        if ($row) echo json_encode(array('status' => true, 'data' => $row));
        else echo json_encode(array('status' => false, 'message' => 'No encontrado'));
    }

    // AJAX: update prestamo pago
    public function updatePrestamoPago()
    {
        if (!$this->input->is_ajax_request()) { redirect($this->router->fetch_class()); }
        $id = intval($this->input->post('id'));
        $row = $this->pagos_model->get_prestamo_pago_by_id($id);
        if (!$row) { echo json_encode(array('status' => false, 'message' => 'Pago no encontrado')); return; }
        $data = array(
            'monto_pagado' => floatval($this->input->post('monto_pagado')),
            'fecha_pago' => $this->input->post('fecha_pago') ? date('Y-m-d H:i:s', strtotime($this->input->post('fecha_pago'))) : $row->fecha_pago,
            'metodo_pago' => $this->input->post('metodo_pago'),
            'referencia' => $this->input->post('referencia'),
            'dato_adicional' => $this->input->post('dato_adicional')
        );
        // update row
        $this->core_model->update('tb_prestamo_pagos', $data, array('id' => $id));
        echo json_encode(array('status' => true, 'message' => 'Pago actualizado'));
    }

    // AJAX: anular (soft) prestamo pago and restore cuota saldo
    public function anularPrestamoPago($id = NULL)
    {
        if (!$this->input->is_ajax_request()) { redirect($this->router->fetch_class()); }
        $id = intval($id);
        $row = $this->pagos_model->get_prestamo_pago_by_id($id);
        if (!$row) { echo json_encode(array('status' => false, 'message' => 'Pago no encontrado')); return; }
        if (!empty($row->anulado)) { echo json_encode(array('status' => false, 'message' => 'Pago ya anulado')); return; }
        // restore cuota saldo if cuota exists
        if (!empty($row->idcuota) && $this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
            // add back monto_pagado
            $this->db->set('saldo', 'saldo + ' . floatval($row->monto_pagado), FALSE);
            $this->db->where('idcuota', $row->idcuota);
            $this->db->update('tb_prestamo_cuotas');
        }
        $user = $this->ion_auth->get_user_id();
        $this->core_model->update('tb_prestamo_pagos', array('anulado' => 1, 'anulado_por' => $user, 'anulado_at' => date('Y-m-d H:i:s')), array('id' => $id));
        echo json_encode(array('status' => true, 'message' => 'Pago anulado'));
    }

    // AJAX: save a prestamo payment into tb_prestamo_pagos and update cuota saldo
    public function savePrestamoPago()
    {
        if (!$this->input->is_ajax_request()) {
            redirect($this->router->fetch_class());
        }
        $cliente_id = $this->input->post('cliente_id');
        $idcredito = $this->input->post('idcredito');
        $idcuota = $this->input->post('idcuota');
        // capture posted amount before any conversion
        $posted_monto_original = floatval($this->input->post('monto'));
        $monto = $posted_monto_original;
        $metodo = $this->input->post('metodo');
        $moneda = $this->input->post('moneda');
        $referencia = $this->input->post('referencia');

        // normalize prestamo id (expecting 'P-123' in credit select)
        $idprestamo = null;
        if (is_string($idcredito) && strpos($idcredito, 'P-') === 0) {
            $idprestamo = intval(substr($idcredito, 2));
        }

        if (!$idprestamo || !$idcuota || $monto <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Datos incompletos'));
            return;
        }

        try {
            $user = $this->ion_auth->get_user_id();
            // normalize posted referencia into an integer serie id or null
            $post_referencia = $this->input->post('referencia');
            $idserie_val = null;
            if ($post_referencia !== null && $post_referencia !== '') {
                $idserie_val = intval($post_referencia);
            }

            // If a series was selected, reserve its next consecutivo atomically and
            // build a formatted reference like CODE + zero-padded number (A001).
            $assigned_referencia = null;
            $assigned_referencia_formatted = null;
            if (!is_null($idserie_val)) {
                try {
                    $this->db->trans_start();
                    // lock the row for update to avoid race conditions
                    $sr = $this->db->query('SELECT * FROM tb_series_recibos WHERE idserie = ? FOR UPDATE', array($idserie_val))->row();
                    if ($sr) {
                        $current = isset($sr->consecutivo) ? intval($sr->consecutivo) : 0;
                        $next = $current + 1;
                        // update consecutivo and ultimo_emitido
                        $this->db->where('idserie', $idserie_val);
                        $this->db->update('tb_series_recibos', array('consecutivo' => $next, 'ultimo_emitido' => $next, 'updated_on' => time()));
                        $assigned_referencia = $next;
                        $code = isset($sr->codigo) ? $sr->codigo : '';
                        $assigned_referencia_formatted = $code . str_pad($next, 3, '0', STR_PAD_LEFT);
                    }
                    $this->db->trans_complete();
                } catch (Exception $e) {
                    // fallback: leave assigned_referencia null
                    $assigned_referencia = null;
                    $assigned_referencia_formatted = null;
                }
            }
            // normalize posted fecha_pago (from datetime-local) into MySQL datetime or use now
            $postedFecha = $this->input->post('fecha_pago');
            $fecha_pago_val = date('Y-m-d H:i:s');
            if (!empty($postedFecha)) {
                $ts = strtotime($postedFecha);
                if ($ts !== false) {
                    $fecha_pago_val = date('Y-m-d H:i:s', $ts);
                }
            }
            // If payment posted in local currency (NIO / C$), convert to USD using latest tasa
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
                    // monto was given in Córdobas; convert to USD for storage and allocation
                    $monto_usd = floatval($monto) / floatval($tasa);
                    $monto = round($monto_usd, 2);
                }
            }

            // Allocate payment across cuotas: if monto > cuota.saldo, apply remainder to next cuota(s).
            $remaining = $monto;
            $insert_ids = array();

            // get all cuotas ordered by numero asc; we'll compute remaining per cuota from payments
            $this->db->from('tb_prestamo_cuotas');
            $this->db->where('idprestamo', $idprestamo);
            $this->db->order_by('numero', 'ASC');
            $cuotas = $this->db->get()->result();

            // try to rotate the list so the provided cuota comes first (if it exists)
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

                // compute total already paid for this cuota
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
                    // store assigned formatted receipt (e.g. A001) if series was selected, otherwise store raw referencia
                    'referencia' => ($assigned_referencia_formatted !== null ? $assigned_referencia_formatted : $referencia),
                    'idusuario' => $user,
                    'fecha_pago' => $fecha_pago_val,
                    'idserie' => $idserie_val,
                    'dato_adicional' => $this->input->post('dato_adicional')
                );
                // if table has a moneda column, persist the original posted moneda
                if ($this->db->field_exists('moneda', 'tb_prestamo_pagos')) {
                    $dataPago['moneda'] = !empty($moneda) ? strtoupper($moneda) : 'USD';
                }
                // if table has monto_original column, persist the posted amount before conversion
                if ($this->db->field_exists('monto_original', 'tb_prestamo_pagos')) {
                    $dataPago['monto_original'] = $posted_monto_original;
                }
                // perform DB insert with db_debug disabled to capture DB errors instead of letting CI trigger a fatal 500
                $db_debug_backup = $this->db->db_debug;
                $this->db->db_debug = FALSE;
                $insert_ok = $this->db->insert('tb_prestamo_pagos', $dataPago);
                if (!$insert_ok) {
                    $dberr = $this->db->error();
                    $this->db->db_debug = $db_debug_backup;
                    log_message('error', 'savePrestamoPago DB insert failed: ' . print_r($dberr, true) . ' payload: ' . json_encode($dataPago));
                    echo json_encode(array('status' => false, 'message' => 'DB insert error: ' . (isset($dberr['message']) ? $dberr['message'] : 'unknown')));
                    return;
                }
                $insert_id_new = $this->db->insert_id();
                $this->db->db_debug = $db_debug_backup;
                $insert_ids[] = $insert_id_new;

                // update this cuota saldo (synchronize stored saldo column)
                $new_saldo = $c_saldo - $aplicar;
                if ($new_saldo < 0) $new_saldo = 0;
                if ($this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                    $this->core_model->update('tb_prestamo_cuotas', array('saldo' => $new_saldo), array('idcuota' => $c_id));
                }

                // Insert into tesoreria flujo so payments appear in Flujo de Efectivo
                try {
                    $acc = $this->db->get('teso_accounts')->row();
                    $cuenta_id = $acc ? $acc->id : 1;
                    $fecha_flujo = date('Y-m-d', strtotime($fecha_pago_val));
                    $concept = 'Pago préstamo ' . $idprestamo . ' cuota ' . $c_id;
                    $flujo_id = $this->Tesoreria_model->save_flujo(array(
                        'fecha' => $fecha_flujo,
                        'cuenta_id' => $cuenta_id,
                        'concepto' => $concept,
                        'tipo' => 'ingreso',
                        'proyectado' => 0,
                        'realizado' => $aplicar
                    ));
                    if (!$flujo_id) {
                        log_message('error', 'savePrestamoPago: Tesoreria_model->save_flujo returned false for prestamo ' . $idprestamo . ' cuota ' . $c_id . ' amount ' . $aplicar);
                    }
                } catch (Throwable $e) {
                    log_message('error', 'savePrestamoPago: tesoreria flujo insert failed: ' . $e->getMessage());
                } catch (Exception $e) {
                    log_message('error', 'savePrestamoPago: tesoreria flujo insert failed: ' . $e->getMessage());
                }

                $remaining -= $aplicar;
            }

            if ($remaining > 0) {
                // leftover amount that couldn't be allocated (no more cuotas). store as unallocated reference in pagos table (last insert)
                log_message('warning', 'savePrestamoPago: remaining unallocated amount ' . $remaining . ' for prestamo ' . $idprestamo);
            }

            // Recalculate and update total saldo in tb_prestamos (for convenience / filtering)
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
                // update tb_prestamos.total_saldo if column exists
                if ($this->db->field_exists('total_saldo', 'tb_prestamos')) {
                    $this->core_model->update('tb_prestamos', array('total_saldo' => $totalSaldo), array('idprestamo' => $idprestamo));
                }
            } catch (Exception $e) {
                log_message('error', 'savePrestamoPago: failed updating total_saldo: ' . $e->getMessage());
            }

            // determine a representative insert id (last inserted payment) if any
            $insert_id = null;
            if (!empty($insert_ids) && is_array($insert_ids)) {
                $insert_id = end($insert_ids);
            }
            echo json_encode(array('status' => true, 'message' => 'Pago registrado', 'id' => $insert_id, 'ids' => $insert_ids));
        } catch (Throwable $e) {
            log_message('error', 'savePrestamoPago error: ' . $e->getMessage());
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        } catch (Exception $e) {
            log_message('error', 'savePrestamoPago exception: ' . $e->getMessage());
            echo json_encode(array('status' => false, 'message' => $e->getMessage()));
        }
    }

    public function check_banco($nombre)
    {
        $id = $this->input->post('id');
        if ($this->core_model->get_by_id('tb_bancos', array('nombre' => $nombre, 'id!=' => $id))) {
            $this->form_validation->set_message('check_banco', 'Este Nombre de Banco ya existe');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function del($id = NULL)
    {
        // if (!$this->ion_auth->is_admin()) {
        // 	$this->session->set_flashdata('info', 'No tienes permiso para eliminar.');
        // 	redirect($this->router->fetch_class());
        // }
        if (!$id || !$this->core_model->get_by_id('tb_bancos', array('id' => $id))) {
            $this->session->set_flashdata('error', 'Banco no encontrado.');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('tb_bancos', array('id' => $id, 'estado' => 1))) {
            $this->session->set_flashdata('error', 'Este Banco tiene como Estado Activo no puede se eliminado.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('tb_bancos', array('id' => $id));
        redirect($this->router->fetch_class());
    }

    public function pdf($pago_id = NULL)
    {
        //$pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', array(80, 350));
        $pdf->SetMargins(5, 10, 5);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;
        $cuotasPagadas = $this->prestamos_model->getContarCuotasPagadas($datos_pago->idcredito);
        $cuotasPendientes = $this->prestamos_model->getContarCuotasPendientes($datos_pago->idcredito);
        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 20, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'FECHA PAGO: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ' . $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');
        $pdf->Cell(0, 4, 'Nro CUOTAS: ' . $datos_pago->numero_coutas, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO SALDO: ' . number_format($datos_pago->total_saldo, 2), 0, 1, '');
        $pdf->Cell(0, 4, 'CUOTAS PAGADAS: ' . $cuotasPagadas->CuotasPagadas, 0, 1, '');
        $pdf->Cell(0, 4, 'CUOTAS PENDIENTES: ' . $cuotasPendientes->CuotasPendientes, 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(70, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(40, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(70, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(70, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfformato1($pago_id = NULL)
    {
        $pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', array(140, 107.5));
        $pdf->SetMargins(5, 10, 5);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;

        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 30, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'Fecha: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ' . $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(0, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(0, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');

        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfformato2($pago_id = NULL)
    {
        $pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', array(90, 215));
        $pdf->SetMargins(5, 10, 5);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;

        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 25, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'Fecha: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ' . $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(0, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(0, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfformato3($pago_id = NULL)
    {
        $pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', array(215, 140));
        $pdf->SetMargins(5, 10, 5);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;

        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 50, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'Fecha: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ' . $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(0, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(0, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfformato4($pago_id = NULL)
    {
        $pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(10, 10, 10);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;

        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 85, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'Fecha: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ' . $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(0, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(0, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfformato6($pago_id = NULL)
    {
        $pdf = new FPDF();
        $pdf = new FPDF('P', 'mm', array(57, 140));
        $pdf->SetMargins(5, 10, 5);
        //$pdf->SetAutoPageBreak(false, 0);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $datos_pago = $this->pagos_model->getPagoById($pago_id);
        $total_pagado = 0;

        $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
        $pdf->AddPage();
        $imagen = base_url() . "public/img/sistema/" . $empresa->logotipo;
        $pdf->Image($imagen, 10, 4, 40, 20, 'jpg');
        $pdf->setY(25);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode($empresa->razon_social), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9.2);

        $pdf->Cell(0, 4, $empresa->telefonos, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->direccion, 0, 1, 'C');
        $pdf->Cell(0, 4, $empresa->web, 0, 1, 'C');

        $pdf->Cell(0, 4, $empresa->email, 0, 1, 'C');
        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 4, 'RECIBO DE PAGO', 0, 1, 'C');
        $pdf->Cell(0, 4, $datos_pago->idpago, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Cell(0, 4, 'Fecha: ' . formatoFechaHora($datos_pago->fechaPago), 0, 1, '');
        $pdf->Cell(0, 4, 'CLIENTE: ', 0, 1, '');
        $pdf->Cell(0, 4, $datos_pago->apellidos . ', ' . $datos_pago->nombres, 0, 1, '');
        $pdf->Cell(0, 4, 'Nro.CREDITO: ' . $datos_pago->idcredito, 0, 1, '');
        $pdf->Cell(0, 4, 'MONTO CREDITO: ' . number_format($datos_pago->monto_credito, 2), 0, 1, '');

        $pdf->Ln(1);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 10, 'Nro.CUOTA', 0);
        $pdf->Cell(0, 10, 'MONTO PAGADO', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(0, 0, '', 'T');
        $pdf->Ln(1);
        foreach ($detalle as $d) {
            $total_pagado = $total_pagado + $d->montoPagado;
            $pdf->SetFont('Arial', '', 7);
            $pdf->MultiCell(30, 4, $d->numero_couta, 0, 'L');
            $pdf->Cell(0, -5, number_format($d->montoPagado, 2), 0, 0, 'R');
            $pdf->Ln(3);
        }
        $pdf->Cell(0, 15, '', 'T');
        $pdf->Cell(0, 5, 'SUBTOTAL: ' . number_format($total_pagado, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'DESCUENTO: ' . number_format($datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'TOTAL A PAGAR: ' . number_format($total_pagado - $datos_pago->descuento_pago, 2), 0, 1, 'R');
        $pdf->Cell(0, 5, 'LE ATENDIO: ' . strtoupper($datos_pago->first_name), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode($empresa->mensaje_ticket), 0, 0, 'C');
        $pdf->Output("RECIBO-" . $datos_pago->idpago . ".pdf", 'I');
    }

    public function pdfmasivo($pago_id = NULL)
    {
        if (!$pago_id || !$this->core_model->get_by_id('tb_pagos', array('idpago' => $pago_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        } else {
            $this->load->library('pdf');
            $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
            $datos_pago = $this->pagos_model->getPagoId($pago_id);
            $detalle = $this->pagos_model->getPagosDetalleId($pago_id);
            $file_name = 'PAGO N°' . $pago_id;
            $html = '<html style="font-size:12px>"';
            $html .= '<head>';
            $html .= '<title>' . $empresa->razon_social . '</title>';
            $html .= '<link rel="stylesheet" href="public/plugins/bootstrap/dist/css/bootstrap.min.css">';
            $html .= '</head>';
            $html .= '<body>';
            $html .= '<h5 align="center">
			' . $empresa->razon_social . '<br>
			DIRECCIÓN: ' . $empresa->direccion . '<br>
			TELÉFONO: ' . $empresa->telefonos . '<br>
			WEB: ' . $empresa->web . '<br>
			CORREO: ' . $empresa->email . '
			</h5>';
            $html .= '<hr>';
            $html .= '<span>CRÉDITO N°' . $datos_pago->idcredito . ' </span><br>';
            $html .= '<span>CLIENTE:' . $datos_pago->apellidos . ', ' . $datos_pago->nombres . ' </span><br>';
            $html .= '<span>MONTO CRÉDITO: ' . number_format($datos_pago->total_pagar, 2) . ' </span><br>';
            $html .= '<span>SALDO CRÉDITO: ' . number_format($datos_pago->total_saldo, 2) . ' </span><br>';
            $html .= '<span>FECHA PAGO: ' . formatoFechaHora($datos_pago->fechaPago) . ' </span>';
            $html .= '<hr>';
            $html .= '<h4 class="text-center">RECIBO DE PAGO N° ' . $pago_id . '</h4>';
            $html .= '<table class="table table-sm">
			<thead>
			<tr>
			<th>CUOTA</th>
			<th style="text-align:right">MONTO</th>
			</tr>
			</thead>
			<tbody>';
            foreach ($detalle as $d) {
                $html .= '<tr>
				<td>' . $d->numero_couta . '</td>
				<td style="text-align:right">' . number_format($d->montoPagado, 2) . '</td>
				</tr>';
            }
            $html .= '
			<tr>
			<td>TOTAL</td>
			<td style="text-align:right">' . number_format($datos_pago->monto_pago, 2) . '</td>
			
			</tr>
			
			</tbody>
			</table>';
            $html .= '<span class="mt-1">LE ATENDIÓ:' . strtoupper($datos_pago->first_name) . '</span>';
            $html .= '<br><br><h5 align="center">
			' . $empresa->razon_social . '<br>
			' . $empresa->mensaje_ticket . '<br>
			' . date('d/m/Y H:i:s a') . '<br>
			</h5>';
            $this->pdf->createPDF($html, $file_name, false, 'A5');
            $html .= '<hr>';
            $html .= '</body>';
            $html .= '</html>';
        }
    }
}
