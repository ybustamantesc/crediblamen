<?php
defined('BASEPATH') or exit('Acción no permitida');

class Prestamo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Core_model', 'core_model');
        $this->load->library('form_validation');
        $this->load->library('ion_auth');
        $this->load->model('prestamos_model');
        $this->load->model('Tesoreria_model');
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
    }

    public function index()
    {
        // Obtener nombre del usuario logueado
        $user = $this->ion_auth->user()->row();
        $nombre_usuario = '';
        if ($user) {
            $nombre_usuario = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (empty($nombre_usuario)) {
                $nombre_usuario = $user->username ?? '';
            }
        }
        
        // load solicitudes approved but exclude those that already have a generated plan (tb_prestamos)
        $approved = $this->core_model->get_all('tb_solicitudes', array('estado_aprobacion' => 'aprobado'));
        $solicitudes = array();
        if (is_array($approved)) {
            foreach ($approved as $s) {
                $has = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $s->idsolicitud));
                if (!$has) $solicitudes[] = $s;
            }
        }

        $data = array(
            'titulo' => 'Crear Crédito desde Aprobación',
            'subtitulo' => 'Generar crédito y calendario a partir de propuestas aprobadas (pendientes)',
            'icono' => 'fas fa-coins',
            'nombre_usuario' => $nombre_usuario,
            'solicitudes' => $solicitudes
        );

        $this->load->view('layout/header', $data);
        $this->load->view('prestamo/core', $data);
        $this->load->view('layout/footer');
    }

    // Return persisted propuestas for a solicitud
    public function get_propuestas_ajax($id = null)
    {
        // Allow both AJAX and direct calls for debugging
        // if (!$this->input->is_ajax_request()) show_404();
        if (!$id) {
            echo json_encode(array('status' => false, 'message' => 'Falta id')); return;
        }
        
        $rows = $this->core_model->get_by_id_all('tb_solicitud_propuestas', array('idsolicitud' => $id));
        if (!is_array($rows)) $rows = array();
        
        log_message('debug', 'get_propuestas_ajax for solicitud ' . $id . ': found ' . count($rows) . ' propuestas');

        // try to get the solicitud's requested plazo (standardized months)
        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
        // only return propuestas if solicitud was approved
        if (!$sol) {
            log_message('debug', 'get_propuestas_ajax: solicitud ' . $id . ' not found');
            echo json_encode(array('status' => true, 'propuestas' => array()));
            return;
        }
        
        if (isset($sol->estado_aprobacion) && $sol->estado_aprobacion !== 'aprobado') {
            log_message('debug', 'get_propuestas_ajax: solicitud ' . $id . ' not approved (estado: ' . $sol->estado_aprobacion . ')');
            echo json_encode(array('status' => true, 'propuestas' => array()));
            return;
        }
        
        $plazo_solicitado = null;
        if ($sol && isset($sol->plazo_meses)) {
            $plazo_solicitado = intval($sol->plazo_meses);
        }

        // annotate each propuesta with a `plazo` property used by the UI
        foreach ($rows as &$r) {
            // if controller returns objects, set property; handle arrays too
            if (is_object($r)) {
                if ($plazo_solicitado) {
                    $r->plazo = $plazo_solicitado;
                } else {
                    // fallback to plazo_max or plazo_min if present
                    if (isset($r->plazo_max) && $r->plazo_max) $r->plazo = $r->plazo_max;
                    elseif (isset($r->plazo_min) && $r->plazo_min) $r->plazo = $r->plazo_min;
                    else $r->plazo = null;
                }
            } else if (is_array($r)) {
                if ($plazo_solicitado) {
                    $r['plazo'] = $plazo_solicitado;
                } else {
                    if (!empty($r['plazo_max'])) $r['plazo'] = $r['plazo_max'];
                    elseif (!empty($r['plazo_min'])) $r['plazo'] = $r['plazo_min'];
                    else $r['plazo'] = null;
                }
            }
        }
        
        log_message('debug', 'get_propuestas_ajax returning ' . count($rows) . ' propuestas with plazo added');

        echo json_encode(array('status' => true, 'propuestas' => $rows));
    }

    // Return solicitud details, propuestas and asesores for the UI
    public function get_solicitud_ajax($id = null)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$id) { echo json_encode(array('status' => false, 'message' => 'Falta id')); return; }

        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $id));
        if (!$sol) { echo json_encode(array('status' => false, 'message' => 'Solicitud no encontrada')); return; }

        $propuestas = $this->core_model->get_by_id_all('tb_solicitud_propuestas', array('idsolicitud' => $id));
        if (!is_array($propuestas)) $propuestas = array();

        // load asesores for cobrador dropdown
        $asesores = $this->core_model->get_all('tb_asesores');
        if (!is_array($asesores)) $asesores = array();

        echo json_encode(array('status' => true, 'solicitud' => $sol, 'propuestas' => $propuestas, 'asesores' => $asesores));
    }

    // Generate preview amortization schedule
    public function generate_preview_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $monto = floatval($this->input->post('monto'));
        $tasa = floatval($this->input->post('tasa'));
        $plazo = intval($this->input->post('plazo'));
        $frecuencia = $this->input->post('frecuencia') ?: 'mensual'; // 'mensual' or 'quincenal'
        $start_date = $this->input->post('fecha_inicio') ?: date('Y-m-d');
        $posted_comision = $this->input->post('comision');
        if ($posted_comision === null || $posted_comision === '') {
            $posted_comision = $this->input->post('comision_desembolso');
        }

        if ($monto <= 0 || $plazo <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Parámetros inválidos')); return;
        }

        // normalize tasa: if >1 assume percent
        if ($tasa > 1) $tasa = $tasa / 100.0;

        // normalize posted commission if provided
        $comision_percent = null;
        if ($posted_comision !== null && $posted_comision !== '') {
            $comision_percent = floatval($posted_comision);
            if ($comision_percent > 1) {
                $comision_percent = $comision_percent / 100.0;
            }
            if ($comision_percent <= 0) {
                $comision_percent = null;
            }
        }
        if ($comision_percent === null) {
            $comision_percent = ($monto > 5000) ? 0.05 : 0.07;
        }

        // periods and rate per period based on frequency
        $frecuencia_lower = strtolower($frecuencia);
        if ($frecuencia_lower === 'diario') {
            $periods = $plazo * 30;
            $r = $tasa / 30.0;
        } elseif ($frecuencia_lower === 'semanal') {
            $periods = $plazo * 4;
            $r = $tasa / 4.0;
        } elseif ($frecuencia_lower === 'quincenal' || $frecuencia_lower === 'catorcenal') {
            $periods = $plazo * 2;
            $r = $tasa / 2.0;
        } else {
            $periods = $plazo;
            $r = $tasa;
        }

        // amortization (French method): payment = P * r / (1 - (1+r)^-n)
        $payment = 0.0;
        if ($r > 0) {
            $payment = $monto * $r / (1 - pow(1 + $r, -$periods));
        } else {
            $payment = $monto / $periods;
        }

        // Business rule: commission percent depends on loan amount
        // If monto > 5000 -> 5% ; otherwise (<=5000) -> 7%
        // Only apply business rule when no commission was provided by caller
        if ($comision_percent === null) {
            $comision_percent = ($monto > 5000) ? 0.05 : 0.07;
        }
        $commission_total = $monto * $comision_percent;
        $commission_per_period = ($periods > 0) ? ($commission_total / $periods) : 0;

        // Sumar comisión por periodo a la cuota (como en Excel)
        $payment_excel = $payment + $commission_per_period;

        // prepare day parameters
        $dia = $this->input->post('dia') ? intval($this->input->post('dia')) : null;
        $dia_semana = $this->input->post('dia_semana') !== null ? intval($this->input->post('dia_semana')) : null;
        $dia1 = $this->input->post('dia1') ? intval($this->input->post('dia1')) : null;
        $dia2 = $this->input->post('dia2') ? intval($this->input->post('dia2')) : null;

        // load active holidays to avoid scheduling on those dates
        $feriados_rows = $this->core_model->get_all('tb_feriados', array('activo' => 1));
        $feriados = array();
        if (is_array($feriados_rows)) {
            foreach ($feriados_rows as $fr) {
                if (isset($fr->fecha) && $fr->fecha) $feriados[] = $fr->fecha;
            }
        }

        // build due dates list based on frequency and provided day(s)
        $due_dates = array();
        if ($frecuencia === 'mensual') {
            $d = ($dia && $dia >= 1 && $dia <= 31) ? $dia : intval((new DateTime($start_date))->format('d'));
            $cur = new DateTime($start_date);
            $year = intval($cur->format('Y'));
            $month = intval($cur->format('m'));
            $makeDate = function($y, $m, $day) {
                $max = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));
                $dd = min($day, $max);
                return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $dd));
            };
            $candidate = $makeDate($year, $month, $d);
            if ($candidate < new DateTime($start_date)) {
                $candidate->modify('+1 month');
                $y = intval($candidate->format('Y'));
                $m = intval($candidate->format('m'));
                $candidate = $makeDate($y, $m, $d);
            }
            for ($i = 1; $i <= $periods; $i++) {
                // if candidate falls on Sunday or a holiday, shift forward until valid
                $pushDate = clone $candidate;
                while (in_array($pushDate->format('Y-m-d'), $feriados) || $pushDate->format('w') == 0) {
                    $pushDate->modify('+1 day');
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $candidate->modify('+1 month');
                $y = intval($candidate->format('Y'));
                $m = intval($candidate->format('m'));
                $candidate = $makeDate($y, $m, $d);
            }
        } elseif ($frecuencia === 'quincenal') {
            $d1 = ($dia1 && $dia1 >= 1 && $dia1 <= 31) ? $dia1 : 1;
            $d2 = ($dia2 && $dia2 >= 1 && $dia2 <= 31) ? $dia2 : 16;
            $cur = new DateTime($start_date);
            $year = intval($cur->format('Y'));
            $month = intval($cur->format('m'));
            $makeDate = function($y, $m, $day) {
                $max = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));
                $dd = min($day, $max);
                return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $dd));
            };
            $count = 0;
            $mY = $year; $mM = $month;
            while ($count < $periods) {
                $cand1 = $makeDate($mY, $mM, $d1);
                $cand2 = $makeDate($mY, $mM, $d2);
                $list = array();
                if ($cand1 <= $cand2) { $list = array($cand1, $cand2); } else { $list = array($cand2, $cand1); }
                foreach ($list as $cdate) {
                    // shift Sundays or holidays forward until valid
                    $cpush = clone $cdate;
                    while (in_array($cpush->format('Y-m-d'), $feriados) || $cpush->format('w') == 0) {
                        $cpush->modify('+1 day');
                    }
                    if ($cpush >= new DateTime($start_date)) {
                        $due_dates[] = $cpush->format('Y-m-d');
                        $count++;
                        if ($count >= $periods) break 2;
                    }
                }
                $mM++;
                if ($mM > 12) { $mM = 1; $mY++; }
            }
        } elseif ($frecuencia === 'catorcenal') {
            // start from provided start_date (primer dia de pago) and add 14 days per period
            $candidate = new DateTime($start_date);
            for ($i = 1; $i <= $periods; $i++) {
                $pushDate = clone $candidate;
                while (in_array($pushDate->format('Y-m-d'), $feriados) || $pushDate->format('w') == 0) {
                    $pushDate->modify('+1 day');
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $candidate->modify('+14 days');
            }
        } elseif ($frecuencia_lower === 'semanal') {
            // Weekly: generate dates on the specific day of the week
            $target_day = ($dia_semana !== null) ? $dia_semana : null; // 0=Sunday, 1=Monday, ..., 6=Saturday
            $candidate = new DateTime($start_date);
            
            // If target_day specified, find the next occurrence of that day
            if ($target_day !== null) {
                $current_day = intval($candidate->format('w')); // 0=Sunday, 1=Monday, etc.
                if ($current_day != $target_day) {
                    $days_ahead = ($target_day - $current_day + 7) % 7;
                    if ($days_ahead == 0) $days_ahead = 7;
                    $candidate->modify('+' . $days_ahead . ' days');
                }
            }
            
            for ($i = 1; $i <= $periods; $i++) {
                $pushDate = clone $candidate;
                // Check if falls on holiday or Sunday, skip forward
                while (in_array($pushDate->format('Y-m-d'), $feriados) || $pushDate->format('w') == 0) {
                    $pushDate->modify('+1 day');
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $candidate->modify('+7 days');
            }
        } elseif ($frecuencia_lower === 'diario') {
            // Daily: start from start_date and add 1 day per period (skip weekends and holidays)
            $candidate = new DateTime($start_date);
            $count = 0;
            while ($count < $periods) {
                $candidate->modify('+1 day');
                $pushDate = clone $candidate;
                // Skip Sundays and Saturdays and holidays
                if ($pushDate->format('w') == 0 || $pushDate->format('w') == 6 || in_array($pushDate->format('Y-m-d'), $feriados)) {
                    continue;
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $count++;
            }
        } else {
            // fallback: quincenal-like behavior
            $d1 = ($dia1 && $dia1 >= 1 && $dia1 <= 31) ? $dia1 : 1;
            $d2 = ($dia2 && $dia2 >= 1 && $dia2 <= 31) ? $dia2 : 16;
            $cur = new DateTime($start_date);
            $year = intval($cur->format('Y'));
            $month = intval($cur->format('m'));
            $makeDate = function($y, $m, $day) {
                $max = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));
                $dd = min($day, $max);
                return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $dd));
            };
            $count = 0; $mY = $year; $mM = $month;
            while ($count < $periods) {
                $cand1 = $makeDate($mY, $mM, $d1);
                $cand2 = $makeDate($mY, $mM, $d2);
                $list = array();
                if ($cand1 <= $cand2) { $list = array($cand1, $cand2); } else { $list = array($cand2, $cand1); }
                foreach ($list as $cdate) {
                    $cpush = clone $cdate;
                    while (in_array($cpush->format('Y-m-d'), $feriados) || $cpush->format('w') == 0) { $cpush->modify('+1 day'); }
                    if ($cpush >= new DateTime($start_date)) {
                        $due_dates[] = $cpush->format('Y-m-d');
                        $count++;
                        if ($count >= $periods) break 2;
                    }
                }
                $mM++; if ($mM > 12) { $mM = 1; $mY++; }
            }
        }

        // Build schedule with per-period rounding and adjust last period to absorb rounding residuals
        $schedule = array();
        $balance = $monto;
        $sum_principal_rounded = 0.0;
        $sum_commission_rounded = 0.0;
        $commission_total = $monto * $comision_percent;
        $commission_per_period = ($periods > 0) ? ($commission_total / $periods) : 0;
        
        // Obtener fecha de desembolso para calcular días de la primera cuota
        $fecha_desembolso = $this->input->post('fecha_desembolso') ?: $start_date;
        
        // Calcular días base según frecuencia
        $dias_base = 30; // Por defecto mensual
        if ($frecuencia_lower === 'diario') {
            $dias_base = 1;
        } elseif ($frecuencia_lower === 'semanal') {
            $dias_base = 7;
        } elseif ($frecuencia_lower === 'quincenal') {
            $dias_base = 15;
        } elseif ($frecuencia_lower === 'catorcenal') {
            $dias_base = 14;
        }
        
        for ($idx = 0; $idx < count($due_dates); $idx++) {
            $i = $idx + 1;
            $date_str = $due_dates[$idx];

            // Calcular días reales entre fecha anterior y fecha actual
            $dias_reales = 0;
            if ($i === 1) {
                // Primera cuota: calcular días desde fecha de desembolso hasta primera fecha de pago
                $fecha_inicio = new DateTime($fecha_desembolso);
                $fecha_fin = new DateTime($date_str);
                $dias_reales = max(0, $fecha_inicio->diff($fecha_fin)->days);
            } else {
                // Cuotas subsecuentes: calcular días desde cuota anterior
                $fecha_anterior = new DateTime($due_dates[$idx - 1]);
                $fecha_actual = new DateTime($date_str);
                $dias_reales = max(0, $fecha_anterior->diff($fecha_actual)->days);
            }

            // Ajustar el interés proporcionalmente a los días reales
            if ($i === 1 && $dias_reales > 0) {
                // Primera cuota: ajustar interés según días reales
                $interest_unrounded = $balance * $r * ($dias_reales / $dias_base);
            } else {
                $interest_unrounded = $balance * $r;
            }

            $principal_unrounded = $payment - $interest_unrounded;
            if ($i === $periods) {
                // last period: principal is remaining balance (unrounded)
                $principal_unrounded = $balance;
            }

            // round interest and principal for display/storage
            $interest_rounded = round($interest_unrounded, 2);
            $principal_rounded = round($principal_unrounded, 2);

            // commission rounding: distribute evenly and put remainder in last period
            if ($i < $periods) {
                $commission_rounded = round($commission_per_period, 2);
                $sum_commission_rounded += $commission_rounded;
            } else {
                $commission_rounded = round($commission_total - $sum_commission_rounded, 2);
            }

            // update sums and balance using rounded principal
            $sum_principal_rounded += $principal_rounded;
            $balance = $balance - $principal_rounded;

            // cuota total is sum of rounded components
            $cuota_total = round($principal_rounded + $interest_rounded + $commission_rounded, 2);

            $schedule[] = array(
                'numero' => $i,
                'fecha' => $date_str,
                'dias' => $dias_reales,
                'cuota' => $cuota_total,
                'principal' => $principal_rounded,
                'interes' => $interest_rounded,
                'comision' => $commission_rounded,
                'saldo' => round(max(0, $balance), 2)
            );
        }

        echo json_encode(array('status' => true, 'payment' => round($payment,2), 'commission_percent' => $comision_percent, 'commission_per_period' => round($commission_per_period,2), 'schedule' => $schedule));
    }

    // Save the credit and cuotas
    public function save_credit_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $idsolicitud = intval($this->input->post('idsolicitud'));
        $monto = floatval($this->input->post('monto'));
        $tasa = floatval($this->input->post('tasa'));
        $plazo = intval($this->input->post('plazo'));
        $frecuencia = $this->input->post('frecuencia') ?: 'mensual';
        $comision = floatval($this->input->post('comision'));
        $fecha_credito = $this->input->post('fecha_credito') ?: date('Y-m-d');
        $fecha_desembolso = $this->input->post('fecha_desembolso') ?: $this->input->post('fecha_credito') ?: date('Y-m-d');
        $primer_dia_pago = $this->input->post('primer_dia_pago') ?: null;

        if (!$idsolicitud || $monto <= 0 || $plazo <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Parámetros inválidos')); return;
        }

        // Business rule: only one plan/credit per solicitud allowed
        $existing = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $idsolicitud));
        if ($existing) {
            echo json_encode(array('status' => false, 'message' => 'Ya existe un plan de pago para esta solicitud')); return;
        }
        // compute desembolso using business rule commission percent
        // If monto > 5000 -> 5% ; otherwise (<=5000) -> 7%
        $comision_percent = ($monto > 5000) ? 0.05 : 0.07;
        $monto_desembolsado = $monto * (1 - $comision_percent);

        // normalize tasa
        if ($tasa > 1) $tasa = $tasa / 100.0;

        // determine periods and r based on frequency
        $frecuencia_lower = strtolower($frecuencia);
        if ($frecuencia_lower === 'diario') {
            // Daily: plazo is in months, convert to days (30 days per month)
            $periods = $plazo * 30;
            $r = $tasa / 30.0;
        } elseif ($frecuencia_lower === 'semanal') {
            // Weekly: plazo is in months, convert to weeks (4 weeks per month)
            $periods = $plazo * 4;
            $r = $tasa / 4.0;
        } elseif ($frecuencia_lower === 'quincenal' || $frecuencia_lower === 'catorcenal') {
            // Bi-weekly: plazo is in months, 2 periods per month
            $periods = $plazo * 2;
            $r = $tasa / 2.0;
        } else {
            // Monthly (default)
            $periods = $plazo;
            $r = $tasa;
        }

        // calculate payment
        if ($r > 0) {
            $payment = $monto * $r / (1 - pow(1 + $r, -$periods));
        } else {
            $payment = $monto / $periods;
        }

        // insert into tb_prestamos
        // Map frecuencia to forma_pago: 0=Diario, 1=Semanal, 2=Quincenal, 3=Mensual
        $forma_pago_map = array(
            'diario' => 0,
            'semanal' => 1,
            'quincenal' => 2,
            'catorcenal' => 2,
            'mensual' => 3
        );
        $forma_pago_value = isset($forma_pago_map[strtolower($frecuencia)]) ? $forma_pago_map[strtolower($frecuencia)] : 3;
        
        // Obtener el usuario logueado para guardar el creador
        $user = $this->ion_auth->user()->row();
        $idusuario = ($user && isset($user->id)) ? $user->id : null;
        $prestamo = array(
            'idsolicitud' => $idsolicitud,
            'monto_credito' => $monto,
            'monto_desembolsado' => $monto_desembolsado,
            'interes_credito' => $tasa,
            'comision_desembolso' => $comision_percent,
            'numero_coutas' => $periods,
            'forma_pago' => $forma_pago_value,
            'fecha_credito' => $fecha_credito,
            'fecha_desembolso' => $fecha_desembolso,
            'primer_dia_pago' => $primer_dia_pago,
            'estado' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'idusuario' => $idusuario
        );

        // Persist cobrador/promotor if provided by the form
        $posted_cobrador = $this->input->post('cobrador'); // may be id or name
        $posted_promotor = $this->input->post('promotor'); // free text
        if ($posted_cobrador) {
            if (is_numeric($posted_cobrador)) {
                $prestamo['idasesor'] = intval($posted_cobrador);
                $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => intval($posted_cobrador)));
                if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => intval($posted_cobrador)));
                if ($ases) {
                    $prestamo['promotor'] = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : '');
                } else {
                    // fallback: store the raw value as promotor
                    $prestamo['promotor'] = $posted_cobrador;
                }
            } else {
                $prestamo['promotor'] = $posted_cobrador;
            }
        }
        // If the separate promotor input exists and promotor not set, use it
        if (!empty($posted_promotor) && empty($prestamo['promotor'])) {
            $prestamo['promotor'] = $posted_promotor;
        }

        $this->core_model->insert('tb_prestamos', $prestamo, TRUE);
        $idprestamo = $this->session->userdata('last_id');
        if (!$idprestamo) {
            echo json_encode(array('status' => false, 'message' => 'Error al crear préstamo')); return;
        }

        // generate schedule and insert cuotas using provided day(s)
        // commission_per_period: distribute total commission across periods
        $commission_total = $monto * $comision_percent;
        $commission_per_period = ($periods > 0) ? ($commission_total / $periods) : 0;

        $dia = $this->input->post('dia') ? intval($this->input->post('dia')) : null;
        $dia1 = $this->input->post('dia1') ? intval($this->input->post('dia1')) : null;
        $dia2 = $this->input->post('dia2') ? intval($this->input->post('dia2')) : null;

        // load active holidays to avoid scheduling on those dates (same as preview)
        $feriados_rows = $this->core_model->get_all('tb_feriados', array('activo' => 1));
        $feriados = array();
        if (is_array($feriados_rows)) {
            foreach ($feriados_rows as $fr) {
                if (isset($fr->fecha) && $fr->fecha) $feriados[] = $fr->fecha;
            }
        }

        // build due_dates same as preview logic
        $due_dates = array();
        if ($frecuencia === 'mensual') {
            $d = ($dia && $dia >= 1 && $dia <= 31) ? $dia : intval((new DateTime($fecha_credito))->format('d'));
            $cur = new DateTime($fecha_credito);
            $year = intval($cur->format('Y'));
            $month = intval($cur->format('m'));
            $makeDate = function($y, $m, $day) {
                $max = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));
                $dd = min($day, $max);
                return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $dd));
            };
            $candidate = $makeDate($year, $month, $d);
            if ($candidate < new DateTime($fecha_credito)) {
                $candidate->modify('+1 month');
                $y = intval($candidate->format('Y'));
                $m = intval($candidate->format('m'));
                $candidate = $makeDate($y, $m, $d);
            }
            for ($i = 1; $i <= $periods; $i++) {
                // shift Sunday or holiday forward until valid
                $pushDate = clone $candidate;
                while (in_array($pushDate->format('Y-m-d'), $feriados) || $pushDate->format('w') == 0) {
                    $pushDate->modify('+1 day');
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $candidate->modify('+1 month');
                $y = intval($candidate->format('Y'));
                $m = intval($candidate->format('m'));
                $candidate = $makeDate($y, $m, $d);
            }
        } elseif ($frecuencia === 'quincenal') {
            $d1 = ($dia1 && $dia1 >= 1 && $dia1 <= 31) ? $dia1 : 1;
            $d2 = ($dia2 && $dia2 >= 1 && $dia2 <= 31) ? $dia2 : 16;
            $cur = new DateTime($fecha_credito);
            $year = intval($cur->format('Y'));
            $month = intval($cur->format('m'));
            $makeDate = function($y, $m, $day) {
                $max = (int)date('t', strtotime(sprintf('%04d-%02d-01', $y, $m)));
                $dd = min($day, $max);
                return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $dd));
            };
            $count = 0; $mY = $year; $mM = $month;
            while ($count < $periods) {
                $cand1 = $makeDate($mY, $mM, $d1);
                $cand2 = $makeDate($mY, $mM, $d2);
                $list = array();
                if ($cand1 <= $cand2) { $list = array($cand1, $cand2); } else { $list = array($cand2, $cand1); }
                foreach ($list as $cdate) {
                    // shift Sundays or holidays forward until valid
                    $cpush = clone $cdate;
                    while (in_array($cpush->format('Y-m-d'), $feriados) || $cpush->format('w') == 0) {
                        $cpush->modify('+1 day');
                    }
                    if ($cpush >= new DateTime($fecha_credito)) {
                        $due_dates[] = $cpush->format('Y-m-d');
                        $count++;
                        if ($count >= $periods) break 2;
                    }
                }
                $mM++; if ($mM > 12) { $mM = 1; $mY++; }
            }
        } elseif ($frecuencia === 'catorcenal') {
            // start from provided fecha_credito and add 14 days per period
            $candidate = new DateTime($fecha_credito);
            for ($i = 1; $i <= $periods; $i++) {
                $pushDate = clone $candidate;
                while (in_array($pushDate->format('Y-m-d'), $feriados) || $pushDate->format('w') == 0) {
                    $pushDate->modify('+1 day');
                }
                $due_dates[] = $pushDate->format('Y-m-d');
                $candidate->modify('+14 days');
            }
        }

        // persist cuotas with per-period rounding and distribute rounding remainder to last period
        $balance = $monto;
        $sum_principal_rounded = 0.0;
        $sum_commission_rounded = 0.0;
        $commission_total = $monto * $comision_percent;
        $commission_per_period = ($periods > 0) ? ($commission_total / $periods) : 0;

        // Calcular días base según frecuencia para cuotas normales
        $dias_base = 30; // Por defecto mensual
        if (strtolower($frecuencia) === 'diario') {
            $dias_base = 1;
        } elseif (strtolower($frecuencia) === 'semanal') {
            $dias_base = 7;
        } elseif ($frecuencia === 'quincenal') {
            $dias_base = 15;
        } elseif ($frecuencia === 'catorcenal') {
            $dias_base = 14;
        }

        for ($idx = 0; $idx < count($due_dates); $idx++) {
            $i = $idx + 1;
            $date_str = $due_dates[$idx];

            // Calcular días reales entre fecha anterior y fecha actual
            $dias_reales = 0;
            if ($i === 1) {
                // Primera cuota: calcular días desde fecha_credito hasta primera fecha de pago
                $fecha_inicio = new DateTime($fecha_credito);
                $fecha_fin = new DateTime($date_str);
                $dias_reales = max(0, $fecha_inicio->diff($fecha_fin)->days);
            } else {
                // Cuotas subsecuentes: calcular días desde cuota anterior
                $fecha_anterior = new DateTime($due_dates[$idx - 1]);
                $fecha_actual = new DateTime($date_str);
                $dias_reales = max(0, $fecha_anterior->diff($fecha_actual)->days);
            }

            // Ajustar el interés proporcionalmente a los días reales
            if ($i === 1 && $dias_reales > 0) {
                // Primera cuota: ajustar interés según días reales
                $interest_unrounded = $balance * $r * ($dias_reales / $dias_base);
            } else {
                $interest_unrounded = $balance * $r;
            }

            $principal_unrounded = $payment - $interest_unrounded;
            if ($i === $periods) {
                $principal_unrounded = $balance;
            }

            $interest_rounded = round($interest_unrounded, 2);
            $principal_rounded = round($principal_unrounded, 2);

            if ($i < $periods) {
                $commission_rounded = round($commission_per_period, 2);
                $sum_commission_rounded += $commission_rounded;
            } else {
                $commission_rounded = round($commission_total - $sum_commission_rounded, 2);
            }

            $sum_principal_rounded += $principal_rounded;
            $balance = $balance - $principal_rounded;

            $cuota_total = round($principal_rounded + $interest_rounded + $commission_rounded, 2);

            $row = array(
                'idprestamo' => $idprestamo,
                'numero' => $i,
                'fecha_vencimiento' => $date_str,
                'dias' => $dias_reales,
                'principal' => $principal_rounded,
                'interes' => $interest_rounded,
                'comision' => $commission_rounded,
                'cuota' => $cuota_total,
                'saldo' => round(max(0, $balance),2),
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->core_model->insert('tb_prestamo_cuotas', $row);
        }

        // return success with created id
        // Attempt to auto-create a contract record so the Contratos list reflects emitted plans
        $contract_created = false;
        try {
            if ($this->db->table_exists('tb_contratos')) {
                $existing = $this->core_model->get_by_id('tb_contratos', array('idprestamo' => $idprestamo));
                if (!$existing) {
                    $user = $this->ion_auth->user()->row();
                    $contract_record = array(
                        'idprestamo' => $idprestamo,
                        'template_id' => 0,
                        'contenido' => '',
                        'created_by' => isset($user->id) ? $user->id : null,
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    $ok = $this->core_model->insert('tb_contratos', $contract_record);
                    if ($ok) $contract_created = true;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Auto-create contrato failed for prestamo ' . $idprestamo . ': ' . $e->getMessage());
        }

        // Create tesoreria flujo entry for desembolso if fecha_desembolso provided
        try {
            $fd = $this->input->post('fecha_desembolso');
            if ($fd && $monto_desembolsado > 0) {
                // choose an existing tesoreria account if present
                $acc = $this->db->get('teso_accounts')->row();
                $cuenta_id = $acc ? $acc->id : 1;
                $concept = 'Desembolso préstamo #' . $idprestamo . ' (Solicitud ' . $idsolicitud . ')';
                $this->Tesoreria_model->save_flujo(array(
                    'fecha' => $fd,
                    'cuenta_id' => $cuenta_id,
                    'concepto' => $concept,
                    'tipo' => 'egreso',
                    'proyectado' => $monto_desembolsado,
                    'realizado' => NULL
                ));
            }
        } catch (Exception $e) {
            log_message('error', 'save_credit_ajax: tesoreria flujo insert failed: ' . $e->getMessage());
        }

        echo json_encode(array('status' => true, 'idprestamo' => $idprestamo, 'contract_created' => $contract_created));
        return;
    }

    // Maintenance: backfill promotor/cobrador for existing prestamos
    public function backfill_cobrador()
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            show_error('Acceso no autorizado', 403);
        }

        $this->load->database();
        $rows = $this->db->from('tb_prestamos')->where('(promotor IS NULL OR promotor = "")')->get()->result();
        $processed = 0; $updated = 0; $errors = 0;
        foreach ($rows as $p) {
            $processed++;
            $new = null;
            if (!empty($p->idsolicitud)) {
                $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $p->idsolicitud));
                if (!$sol) $sol = $this->core_model->get_by_id('tb_solicitud', array('idsolicitud' => $p->idsolicitud));
                if ($sol) {
                    if (!empty($sol->promotor)) $new = $sol->promotor;
                    elseif (!empty($sol->cobrador)) $new = $sol->cobrador;
                    elseif (!empty($sol->nombre_promotor)) $new = $sol->nombre_promotor;
                }
            }
            if (empty($new)) {
                if (!empty($p->nombre_promotor)) $new = $p->nombre_promotor;
                elseif (!empty($p->nombre_asesor)) $new = $p->nombre_asesor;
                elseif (!empty($p->promotor)) $new = $p->promotor;
            }
            // If still empty but idasesor exists, resolve advisor name
            if (empty($new) && !empty($p->idasesor)) {
                $aid = intval($p->idasesor);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) {
                        $new = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : null);
                    }
                }
            }
            if (empty($new)) continue;
            $update = array('promotor' => $new);
            if (is_numeric($new)) {
                $aid = intval($new);
                if ($aid > 0) {
                    $ases = $this->core_model->get_by_id('tb_asesores', array('idasesor' => $aid));
                    if (!$ases) $ases = $this->core_model->get_by_id('tb_asesores', array('id' => $aid));
                    if ($ases) {
                        $update['idasesor'] = $aid;
                        $update['promotor'] = isset($ases->nombres) ? $ases->nombres : (isset($ases->nombre) ? $ases->nombre : $new);
                    }
                }
            }
            try {
                $this->core_model->update('tb_prestamos', $update, array('idprestamo' => $p->idprestamo));
                $updated++;
            } catch (Exception $e) {
                $errors++;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(array('status' => true, 'processed' => $processed, 'updated' => $updated, 'errors' => $errors));
        return;
    }

    public function check_limite_credito($monto_credito)
    {
        $cliente_id = $this->input->post('idcliente');
        if ($this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id, 'limite_credito<' => $monto_credito))) {
            $this->form_validation->set_message('check_limite_credito', 'Limite Crédito');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function getLimiteCredito()
    {
        $cliente_id = $this->input->post('cliente_id');
        $data = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
        echo json_encode($data);
    }

    public function opcion($credito_id = NULL)
    {
        if (!$this->core_model->get_by_id('tb_creditos', array('id' => $credito_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        } else {
            $data = array(
                'titulo' => '¿Que te gustaría hacer?',
                'subtitulo' => 'Por favor elija una de las opciones a seguir.',
                'icono_view' => 'ik ik-user ',
                'credito' => $this->core_model->get_by_id('tb_creditos', array('id' => $credito_id))
            );
            $this->load->view('layout/header', $data);
            $this->load->view('prestamos/opcion');
            $this->load->view('layout/footer');
        }
    }

    public function pdf($prestamo_id = NULL)
    {
        if (!$prestamo_id || !$this->core_model->get_by_id('tb_creditos', array('id' => $prestamo_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        $this->load->library('pdf');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $coutas = $this->prestamos_model->get_all_by_id($prestamo_id);
        $prestamo = $this->prestamos_model->get_by_id($prestamo_id);
        $file_name = 'CRÉDITO N° ' . $prestamo->idcredito;
        $data = array(
            'file_name' => $file_name,
            'empresa' => $empresa,
            'prestamo' => $prestamo,
            'cuotas' => $coutas,
            'titulo' => $file_name
        );
        $html = $this->load->view('prestamos/pdf', $data, TRUE);
        $this->pdf->createPDF($html, $file_name, false, 'A4', 'landscape');
    }

    public function pdfestadocuenta($prestamo_id = NULL)
    {
        if (!$prestamo_id || !$this->core_model->get_by_id('tb_creditos', array('id' => $prestamo_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        } else {
            $this->load->library('pdf');
            $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
            $coutas = $this->prestamos_model->get_all_by_id($prestamo_id);
            $prestamo = $this->prestamos_model->get_by_id($prestamo_id);
            $file_name = 'PRESTAMO N° ' . $prestamo->id;
            $html = '<html style="font-size:10px>"';
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
			CORREO: ' . $empresa->email . '<br>
			</h5>';
            $html .= "<h3 class='text-center'>ESTADO DE CUENTA</h3>";
            $html .= '<hr>';
            $datos_salida = '';
            $forma_pago = '';
            $total_capital = 0;
            $total_interes = 0;
            $total_pagado = 0;
            $total_pendiente = 0;
            if ($prestamo->forma_pago == 0) {
                $forma_pago = 'DIARIO';
            } elseif ($prestamo->forma_pago == 1) {
                $forma_pago = 'SEMANAL';
            } elseif ($prestamo->forma_pago == 2) {
                $forma_pago = 'QUINCENAL';
            } elseif ($prestamo->forma_pago == 3) {
                $forma_pago = 'MENSUAL';
            }
            if ($prestamo->estado == 1) {
                $datos_salida .= '<strong>Fecha Salida</strong>' . formatoFechaHora($prestamo->fecha_credito) . '<br>';
            }

            $html .= '<table class="table table-sm table-bordered">
			<tbody>
			<tr>
			<td>CRÉDITO N°</td><td>' . $prestamo->id . '</td><td>FECHA CRÉDITO</td><td>' . formatoFechaHora($prestamo->fecha_credito) . '</td>
			</tr>
			<tr>
			<td>CLIENTE: </td><td colspan="3">' . $prestamo->idcliente . ' - ' . $prestamo->apellidos . ', ' . $prestamo->nombres . '</td>
			</tr>
			<tr>
			<td>ASESOR: </td><td colspan="3">' . $prestamo->nombre_asesor . '</td>
			</tr>
			<tr>
			<td>MONTO CRÉDITO: </td><td>' . number_format($prestamo->monto_credito, 2) . '</td><td></td><td></td>
			</tr>
			<tr>
			<td>FORMA DE PAGO: </td><td colspan="3">' . $forma_pago . '</td>
			</tr>
			<tr>
			<td>No. De cuenta Banrural para depositar:: </td><td colspan="3">3002578456</td>
			</tr>
			</tbody>
			</table>';
            $html .= '<hr>';
            $html .= '<table class="table table-sm table-bordered">
			<thead>
			<tr>
			<th>Cuota</th>
			<th>Fecha Cuota</th>
			<th>Capital</th>
			<th>Interes</th>
			<th>Mora</th>
			<th>Monto Pagado</th>
			<th>F.Realizó Pago</th>
			<th>Monto Cuota</th>
			<th>Saldo</th>
			<th>Estado</th>
			</tr>
			</thead>
			<tbody>';
            foreach ($coutas as $couta) {
                $numero_couta = $couta->numero_couta;
                $fecha_couta = $couta->fecha_couta;
                $fecha_pago = $couta->fecha_pago;
                $monto_pagado = $couta->monto_pagado;
                $monto_couta = $couta->monto_couta;
                $monto_pendiente = $couta->monto_pendiente;
                $estado_couta = $couta->estado_couta;
                $fechaAtual = strtotime(date('Y-m-d'));
                $fechaVencimiento = strtotime($fecha_couta);
                $monto_capital = $couta->monto_capital;
                $monto_interes = $couta->monto_interes;
                $total_capital = $total_capital + $couta->monto_capital;
                $total_interes = $total_interes + $couta->monto_interes;
                $total_pagado = $total_pagado + $couta->monto_pagado;
                $total_pendiente = $total_pendiente + $couta->monto_pendiente;
                $estado = '';
                if ($estado_couta == 1) {
                    if ($fechaAtual == $fechaVencimiento) {
                        $estado = "PAGA HOY";
                    }
                    if ($fechaAtual > $fechaVencimiento) {
                        $estado = "VENCIÓ";
                    }
                    if ($fechaAtual < $fechaVencimiento) {
                        $estado = "PENDIENTE";
                    }
                } else {
                    $estado = "CANCELADO";
                }
                $html .= '<tr>
				<td>' . $numero_couta . '</td>
				<td>' . $fecha_couta . '</td>
				<td>' . $monto_capital . '</td>
				<td>' . $monto_interes . '</td>
				<td>00.00</td>
				<td>' . $monto_pagado . '</td>
				<td>' . ($fecha_pago == "" ? '' : formatoFechaHora($fecha_pago)) . '</td>
				<td>' . $monto_couta . '</td>
				<td>' . $monto_pendiente . '</td>
				<td>' . $estado . '</td>
				</tr>';
            }
            $html .= '
			<tr>
			<td></td>
			<td></td>
			<td>' . number_format($total_capital, 2) . '</td>
			<td>' . number_format($total_interes, 2) . '</td>
			<td>00.00</td>
			<td>' . number_format($total_pagado, 2) . '</td>
			<td></td>
			<td></td>
			<td>' . number_format($total_pendiente, 2) . '</td>
			<td></td>
			</tr>
			';

            $html .= '
			</tbody>
			</table>';
            $html .= '<br><h5 align="center">
			<br>ES OBLIGATORIO QUE REALICE SU PAGO ANTES DE LA 05:00PM PARA OBTENER MÁS BENEFICIOS<br>
			' . $empresa->mensaje_ticket . '<br>
			' . $empresa->razon_social . '<br>
			' . date('d/m/Y H:i:s') . '<br>
			</h5>';

            $this->pdf->createPDF($html, $file_name, false, 'A4', 'landscape');
            $html .= '<hr>';
            $html .= '</body>';
            $html .= '</html>';
        }
    }

    public function del($prestamo_id = NULL)
    {
        // if (!$this->ion_auth->is_admin()) {
        // 	$this->session->set_flashdata('info', 'No tienes permiso para eliminar prestamos.');
        // 	redirect('/');
        // }
        if (!$prestamo_id || !$this->core_model->get_by_id('tb_creditos', array('id' => $prestamo_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('tb_creditos', array('id' => $prestamo_id, 'estado' => 2))) {
            $this->session->set_flashdata('error', 'El crédito no puese ser eliminado está en proceso de pago.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('tb_credito_detalle', array('idcredito' => $prestamo_id));
        $this->core_model->delete('tb_creditos', array('id' => $prestamo_id));
        redirect($this->router->fetch_class());
    }
}
