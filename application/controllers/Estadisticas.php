<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estadisticas extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','form']);
        $this->load->library(['session']);
        $this->load->database();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Estadísticas',
            'subtitulo' => 'Indicadores financieros del sistema',
            'icono' => 'fas fa-chart-bar',
            'scripts' => ['js/estadisticas.js']
        ];
        $this->load->view('layout/header', $data);
        $this->load->view('estadisticas/home', $data);
        $this->load->view('layout/footer');
    }

    private function _get_movimientos_amount_field()
    {
        if (!$this->db->table_exists('teso_movimientos')) {
            return null;
        }
        if ($this->db->field_exists('monto_total', 'teso_movimientos')) {
            return 'monto_total';
        }
        if ($this->db->field_exists('monto', 'teso_movimientos')) {
            return 'monto';
        }
        return null;
    }

    private function _get_payments_amount_field()
    {
        if (!$this->db->table_exists('tb_prestamo_pagos')) {
            return null;
        }
        if ($this->db->field_exists('monto_pagado', 'tb_prestamo_pagos')) {
            return 'monto_pagado';
        }
        if ($this->db->field_exists('monto', 'tb_prestamo_pagos')) {
            return 'monto';
        }
        return null;
    }

    private function _count_active_loans()
    {
        $total = 0;

        if ($this->db->table_exists('tb_prestamos') && $this->db->field_exists('estado', 'tb_prestamos')) {
            $this->db->from('tb_prestamos');
            $this->db->where('estado !=', 0);
            $total += intval($this->db->count_all_results());
        }

        if ($this->db->table_exists('tb_creditos') && $this->db->field_exists('estado', 'tb_creditos')) {
            $this->db->from('tb_creditos');
            $this->db->where('estado !=', 0);
            $total += intval($this->db->count_all_results());
        }

        return $total;
    }

    private function _count_overdue_loans()
    {
        $today = date('Y-m-d');
        $overdue = 0;

        if ($this->db->table_exists('tb_prestamo_cuotas')) {
            $hasSaldo = $this->db->field_exists('saldo', 'tb_prestamo_cuotas');
            $hasCuota = $this->db->field_exists('cuota', 'tb_prestamo_cuotas');
            $hasFechaVenc = $this->db->field_exists('fecha_vencimiento', 'tb_prestamo_cuotas');
            if ($hasFechaVenc && ($hasSaldo || $hasCuota)) {
                $this->db->select('COUNT(DISTINCT pc.idprestamo) AS cnt', false);
                $this->db->from('tb_prestamo_cuotas pc');
                $this->db->where('pc.fecha_vencimiento <', $today);
                if ($hasSaldo) {
                    $this->db->where('IFNULL(pc.saldo, 0) >', 0);
                } else {
                    $this->db->where('IFNULL(pc.cuota, 0) >', 0);
                }
                if ($this->db->table_exists('tb_prestamos') && $this->db->field_exists('estado', 'tb_prestamos')) {
                    $this->db->join('tb_prestamos pr', 'pr.idprestamo = pc.idprestamo');
                    $this->db->where('pr.estado !=', 0);
                }
                $row = $this->db->get()->row();
                if ($row) {
                    $overdue = intval($row->cnt);
                }
            }
        }

        if ($overdue === 0 && $this->db->table_exists('tb_credito_detalle')) {
            $this->db->select('COUNT(DISTINCT idcredito) AS cnt', false);
            $this->db->from('tb_credito_detalle');
            if ($this->db->field_exists('fecha_couta', 'tb_credito_detalle')) {
                $this->db->where('fecha_couta <', $today);
            }
            if ($this->db->field_exists('estado_couta', 'tb_credito_detalle')) {
                $this->db->where('estado_couta', 1);
            }
            $row = $this->db->get()->row();
            if ($row) {
                $overdue = intval($row->cnt);
            }
        }

        return $overdue;
    }

    public function indicators_json()
    {
        $revenue = 0.0;
        $expenses = 0.0;
        $today_collections = 0.0;
        $month_collections = 0.0;

        $amountField = $this->_get_movimientos_amount_field();
        if ($amountField !== null) {
            $this->db->select("COALESCE(SUM(CASE WHEN tipo_transferencia = 'abono' THEN {$amountField} ELSE 0 END), 0) AS revenue", false);
            $this->db->select("COALESCE(SUM(CASE WHEN tipo_transferencia = 'cargo' THEN {$amountField} ELSE 0 END), 0) AS expenses", false);
            $this->db->from('teso_movimientos');
            if ($this->db->field_exists('estado', 'teso_movimientos')) {
                $this->db->where('estado', 'activo');
            }
            $row = $this->db->get()->row();
            if ($row) {
                $revenue = floatval($row->revenue);
                $expenses = floatval($row->expenses);
            }
        }

        $paymentField = $this->_get_payments_amount_field();
        if ($paymentField !== null) {
            $today = date('Y-m-d');
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');

            $this->db->select("COALESCE(SUM({$paymentField}), 0) AS total", false);
            $this->db->from('tb_prestamo_pagos');
            if ($this->db->field_exists('fecha_pago', 'tb_prestamo_pagos')) {
                $this->db->where('fecha_pago', $today);
            }
            $row = $this->db->get()->row();
            $today_collections = $row ? floatval($row->total) : 0.0;

            $this->db->select("COALESCE(SUM({$paymentField}), 0) AS total", false);
            $this->db->from('tb_prestamo_pagos');
            if ($this->db->field_exists('fecha_pago', 'tb_prestamo_pagos')) {
                $this->db->where('fecha_pago >=', $monthStart);
                $this->db->where('fecha_pago <=', $monthEnd);
            }
            $row = $this->db->get()->row();
            $month_collections = $row ? floatval($row->total) : 0.0;
        }

        $indicators = [
            'revenue' => round($revenue, 2),
            'expenses' => round($expenses, 2),
            'net' => round($revenue - $expenses, 2),
            'total_loans' => $this->_count_active_loans(),
            'overdue_loans' => $this->_count_overdue_loans(),
            'today_collections' => round($today_collections, 2),
            'month_collections' => round($month_collections, 2)
        ];

        header('Content-Type: application/json');
        echo json_encode($indicators);
    }

    public function metric_details($metric = '')
    {
        $details = [];
        switch ($metric) {
            case 'overdue_loans':
                if ($this->db->table_exists('tb_prestamo_cuotas')) {
                    $hasSolicitudes = $this->db->table_exists('tb_solicitudes');
                    $select = [
                        'pc.idprestamo AS loan_id',
                        ($hasSolicitudes ? "CONCAT(IFNULL(s.apellidos,''), ' ', IFNULL(s.nombres,'')) AS client" : "'' AS client"),
                        "COALESCE(pc.saldo, pc.cuota, 0) AS amount_due",
                        "DATEDIFF(CURDATE(), pc.fecha_vencimiento) AS days_overdue"
                    ];
                    $this->db->select($select, false);
                    $this->db->from('tb_prestamo_cuotas pc');
                    $this->db->join('tb_prestamos pr', 'pr.idprestamo = pc.idprestamo', 'left');
                    if ($hasSolicitudes) {
                        $this->db->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left');
                    }
                    $this->db->where('pc.fecha_vencimiento <', date('Y-m-d'));
                    if ($this->db->field_exists('saldo', 'tb_prestamo_cuotas')) {
                        $this->db->where('IFNULL(pc.saldo, 0) >', 0);
                    } elseif ($this->db->field_exists('cuota', 'tb_prestamo_cuotas')) {
                        $this->db->where('IFNULL(pc.cuota, 0) >', 0);
                    }
                    if ($this->db->field_exists('estado', 'tb_prestamos')) {
                        $this->db->where('(pr.estado != 0 OR pr.estado IS NULL)', null, false);
                    }
                    $this->db->order_by('pc.fecha_vencimiento', 'ASC');
                    $this->db->limit(50);
                    $details = $this->db->get()->result_array();
                } elseif ($this->db->table_exists('tb_credito_detalle')) {
                    $this->db->select([
                        'idcredito AS loan_id',
                        "CONCAT(IFNULL(c.apellidos,''), ' ', IFNULL(c.nombres,'')) AS client",
                        'monto_cuota AS amount_due',
                        "DATEDIFF(CURDATE(), fecha_couta) AS days_overdue"
                    ], false);
                    $this->db->from('tb_credito_detalle cd');
                    if ($this->db->table_exists('tb_creditos')) {
                        $this->db->join('tb_creditos cr', 'cr.id = cd.idcredito', 'left');
                    }
                    if ($this->db->table_exists('tb_clientes')) {
                        $this->db->join('tb_clientes c', 'c.idcliente = cr.idcliente', 'left');
                    }
                    $this->db->where('fecha_couta <', date('Y-m-d'));
                    if ($this->db->field_exists('estado_couta', 'tb_credito_detalle')) {
                        $this->db->where('estado_couta', 1);
                    }
                    $this->db->order_by('fecha_couta', 'ASC');
                    $this->db->limit(50);
                    $details = $this->db->get()->result_array();
                }
                break;
            case 'revenue':
                if ($this->db->table_exists('teso_movimientos')) {
                    $amountField = $this->_get_movimientos_amount_field();
                    if ($amountField !== null) {
                        $this->db->select([
                            "IFNULL(concepto, 'Ingresos') AS source",
                            "SUM(CASE WHEN tipo_transferencia = 'abono' THEN {$amountField} ELSE 0 END) AS amount"
                        ], false);
                        $this->db->from('teso_movimientos');
                        $this->db->where('tipo_transferencia', 'abono');
                        if ($this->db->field_exists('estado', 'teso_movimientos')) {
                            $this->db->where('estado', 'activo');
                        }
                        $this->db->group_by('source');
                        $details = $this->db->get()->result_array();
                    }
                }
                break;
            default:
                $details = [];
        }

        header('Content-Type: application/json');
        echo json_encode($details);
    }
}
