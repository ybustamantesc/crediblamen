<?php
defined('BASEPATH') or exit('Acción no permitida');
class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
		$this->load->model('prestamos_model');
	}

	public function index()
	{
		// Nombre para el saludo
		$user = $this->ion_auth->user()->row();
		$display_name = '';
		if (!empty($user)) {
			if (!empty($user->first_name)) {
				$display_name = $user->first_name;
			} elseif (!empty($user->username)) {
				$display_name = $user->username;
			} else {
				$display_name = $user->email ?? 'Usuario';
			}
		}

		$data = array(
			'titulo' => 'Inicio',
			'subtitulo' => 'Bienvenido.',
			'display_name' => $display_name,
			'icono' => 'ik ik-home ',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			//'estacionados' => $this->estacionar_model->get_all(),
			// 'numero_vacantes_pequeno' => $this->estacionar_model->get_numero_vacantes('1'),
			// 'vacantes_ocupadas_pequeno' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 1)),
			// 'numero_vacantes_medio' => $this->estacionar_model->get_numero_vacantes('2'),
			// 'vacantes_ocupadas_medio' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 2)),
			// 'numero_vacantes_grande' => $this->estacionar_model->get_numero_vacantes('3'),
			// 'vacantes_ocupadas_grande' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 3)),
			// 'numero_vacantes_moto' => $this->estacionar_model->get_numero_vacantes('4'),
			// 'vacantes_ocupadas_moto' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 4)),

			// 'total_vacantes' => $this->home_model->getTotalVacantes(),
			'total_asesores' => $this->home_model->countAll('tb_asesores'),
			'total_clientes' => $this->home_model->countAll('tb_clientes'),
			'total_prestamos' => $this->home_model->countAll('tb_prestamos'),
			'total_pagos' => $this->home_model->countAll('tb_prestamo_pagos'),
			'coutas_vencidas' => $this->prestamos_model->getCoutasVencidas(),
			'coutas_pagan_hoy' => $this->prestamos_model->getCoutasPaganHoy(),
			// 'total_matriculas_pagadas' => $this->home_model->countAll('matriculas', array('estado' => 1)),
			// 'total_matriculas_pendientes' => $this->home_model->countAll('matriculas', array('estado' => 0)),

			// 'total_avulsos' => $this->home_model->getTotalAvulsos(),
			// 'total_avulsos_pagadas' => $this->home_model->countAll('estacionar', array('estacionar_estado' => 1)),
			// 'total_avulsos_pendientes' => $this->home_model->countAll('estacionar', array('estacionar_estado' => 0)),

			// 'total_clientes' => $this->home_model->countAll('clientes'),
			// 'total_clientes_activos' => $this->home_model->countAll('clientes', array('estado' => 1)),
			// 'total_clientes_inactivos' => $this->home_model->countAll('clientes', array('estado' => 0)),
			//NOTIFICACIONES


		);
		// $notificaciones = 0;
		// if ($this->home_model->getMatriculasVencidas()) {
		//     $data['matriculas_vencidas'] = TRUE;
		//     $notificaciones++;
		// }
		// if ($this->core_model->get_by_id('precios', array('precio_estado' => 0))) {
		//     $data['precios_desactivados'] = TRUE;
		//     $notificaciones++;
		// }
		// if ($this->core_model->get_by_id('formapago', array('estado' => 0))) {
		//     $data['formas_desactivados'] = TRUE;
		//     $notificaciones++;
		// }
		// if ($this->core_model->get_by_id('users', array('active' => 0))) {
		//     $data['usuarios_desactivados'] = TRUE;
		//     $notificaciones++;
		// }
		// if ($this->core_model->get_by_id('clientes', array('estado' => 0))) {
		//     $data['clientes_desactivados'] = TRUE;
		//     $notificaciones++;
		// }
		// if ($notificaciones > 0) {
		//     $data['notificaciones'] = $notificaciones;
		// }
		// echo '<pre>';
		// print_r($data["matriculas"]);
		// exit();

		$this->load->view('layout/header', $data);

		// Preparar datos para las gráficas: últimos 12 meses
		$labels_keys = array();
		$labels_display = array();
		for ($i = 11; $i >= 0; $i--) {
			$key = date('Y-m', strtotime("-{$i} months"));
			$labels_keys[] = $key;
			// Mostrar formato corto en español, ej. 'Mar 2025'
			$labels_display[] = date('M Y', strtotime("-{$i} months"));
		}

		// Créditos por mes (tb_prestamos.fecha_credito)
		$this->db->select("DATE_FORMAT(fecha_credito, '%Y-%m') as ym, COUNT(*) as cnt", false);
		$this->db->where('fecha_credito >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$credit_rows = $this->db->get('tb_prestamos')->result();
		$credit_map = array();
		foreach ($credit_rows as $r) {
			$credit_map[$r->ym] = (int) $r->cnt;
		}
		$credit_data = array();
		foreach ($labels_keys as $lab) {
			$credit_data[] = isset($credit_map[$lab]) ? $credit_map[$lab] : 0;
		}

		// Pagos por mes (tb_prestamo_pagos.fecha_pago)
		$this->db->select("DATE_FORMAT(fecha_pago, '%Y-%m') as ym, SUM(monto_pagado) as total", false);
		$this->db->where('fecha_pago >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$pay_rows = $this->db->get('tb_prestamo_pagos')->result();
		$pay_map = array();
		foreach ($pay_rows as $r) {
			$pay_map[$r->ym] = (float) $r->total;
		}
		$pay_data = array();
		foreach ($labels_keys as $lab) {
			$pay_data[] = isset($pay_map[$lab]) ? $pay_map[$lab] : 0;
		}

		// Distribución por estado de crédito
		$this->db->select('estado, COUNT(*) as cnt');
		$this->db->group_by('estado');
		$status_rows = $this->db->get('tb_prestamos')->result();
		// Mapear estados a etiquetas legibles (asumimos mapeo básico; ajustar si tienes valores personalizados)
		$estado_map = array(
			'0' => 'Pagado',
			'1' => 'Activo',
			'2' => 'Anulado'
		);
		$status_labels = array();
		$status_data = array();
		foreach ($status_rows as $r) {
			$k = (string) $r->estado;
			$label = isset($estado_map[$k]) ? $estado_map[$k] : 'Estado ' . $k;
			$status_labels[] = $label;
			$status_data[] = (int) $r->cnt;
		}

		$data['chart_months'] = $labels_keys; // claves YYYY-MM para mapear datos
		$data['chart_months_display'] = $labels_display; // etiquetas legibles
		$data['chart_credits'] = $credit_data;
		$data['chart_payments'] = $pay_data;
		$data['chart_status_labels'] = $status_labels;
		$data['chart_status_data'] = $status_data;

		$this->load->view('home/index', $data);
		$this->load->view('layout/footer');
	}

	/**
	 * Devuelve JSON con los datos necesarios para las gráficas (útil para AJAX)
	 * URL: /home/charts
	 */
	public function charts()
	{
		// seguridad básica: solo usuarios logueados
		if (!$this->ion_auth->logged_in()) {
			return $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(['error' => 'Unauthorized']));
		}

		// Preparar etiquetas y claves (últimos 12 meses)
		$labels_keys = array();
		$labels_display = array();
		for ($i = 11; $i >= 0; $i--) {
			$key = date('Y-m', strtotime("-{$i} months"));
			$labels_keys[] = $key;
			$labels_display[] = date('M Y', strtotime("-{$i} months"));
		}

		// Créditos por mes
		$this->db->select("DATE_FORMAT(fecha_credito, '%Y-%m') as ym, COUNT(*) as cnt", false);
		$this->db->where('fecha_credito >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$credit_rows = $this->db->get('tb_prestamos')->result();
		$credit_map = array();
		foreach ($credit_rows as $r) {
			$credit_map[$r->ym] = (int) $r->cnt;
		}
		$credit_data = array();
		foreach ($labels_keys as $lab) {
			$credit_data[] = isset($credit_map[$lab]) ? $credit_map[$lab] : 0;
		}

		// Pagos por mes
		$this->db->select("DATE_FORMAT(fecha_pago, '%Y-%m') as ym, SUM(monto_pagado) as total", false);
		$this->db->where('fecha_pago >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$pay_rows = $this->db->get('tb_prestamo_pagos')->result();
		$pay_map = array();
		foreach ($pay_rows as $r) {
			$pay_map[$r->ym] = (float) $r->total;
		}
		$pay_data = array();
		foreach ($labels_keys as $lab) {
			$pay_data[] = isset($pay_map[$lab]) ? $pay_map[$lab] : 0;
		}

		// Distribución por estado
		$this->db->select('estado, COUNT(*) as cnt');
		$this->db->group_by('estado');
		$status_rows = $this->db->get('tb_prestamos')->result();
		$estado_map = array('0' => 'Pagado', '1' => 'Activo', '2' => 'Anulado');
		$status_labels = array();
		$status_data = array();
		foreach ($status_rows as $r) {
			$k = (string) $r->estado;
			$label = isset($estado_map[$k]) ? $estado_map[$k] : 'Estado ' . $k;
			$status_labels[] = $label;
			$status_data[] = (int) $r->cnt;
		}

		$out = array(
			'months' => $labels_display,
			'months_keys' => $labels_keys,
			'credits' => $credit_data,
			'payments' => $pay_data,
			'status_labels' => $status_labels,
			'status_data' => $status_data
		);

		return $this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	/**
	 * Datos de créditos por mes (JSON)
	 * URL: /home/credits_chart
	 */
	public function credits_chart()
	{
		if (!$this->ion_auth->logged_in()) {
			return $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(['error' => 'Unauthorized']));
		}

		$labels_display = array();
		$labels_keys = array();
		for ($i = 11; $i >= 0; $i--) {
			$key = date('Y-m', strtotime("-{$i} months"));
			$labels_keys[] = $key;
			$labels_display[] = date('M Y', strtotime("-{$i} months"));
		}

		$this->db->select("DATE_FORMAT(fecha_credito, '%Y-%m') as ym, COUNT(*) as cnt", false);
		$this->db->where('fecha_credito >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$credit_rows = $this->db->get('tb_prestamos')->result();
		$credit_map = array();
		foreach ($credit_rows as $r) {
			$credit_map[$r->ym] = (int) $r->cnt;
		}
		$credit_data = array();
		foreach ($labels_keys as $lab) {
			$credit_data[] = isset($credit_map[$lab]) ? $credit_map[$lab] : 0;
		}

		$out = array('months' => $labels_display, 'months_keys' => $labels_keys, 'credits' => $credit_data);
		return $this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	/**
	 * Datos de pagos por mes (JSON)
	 * URL: /home/payments_chart
	 */
	public function payments_chart()
	{
		if (!$this->ion_auth->logged_in()) {
			return $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(['error' => 'Unauthorized']));
		}
		$labels_display = array();
		$labels_keys = array();
		for ($i = 11; $i >= 0; $i--) {
			$key = date('Y-m', strtotime("-{$i} months"));
			$labels_keys[] = $key;
			$labels_display[] = date('M Y', strtotime("-{$i} months"));
		}

		$this->db->select("DATE_FORMAT(fecha_pago, '%Y-%m') as ym, SUM(monto_pagado) as total", false);
		$this->db->where('fecha_pago >=', date('Y-m-01', strtotime('-11 months')));
		$this->db->group_by('ym');
		$this->db->order_by('ym', 'ASC');
		$pay_rows = $this->db->get('tb_prestamo_pagos')->result();
		$pay_map = array();
		foreach ($pay_rows as $r) {
			$pay_map[$r->ym] = (float) $r->total;
		}
		$pay_data = array();
		foreach ($labels_keys as $lab) {
			$pay_data[] = isset($pay_map[$lab]) ? $pay_map[$lab] : 0;
		}

		$out = array('months' => $labels_display, 'months_keys' => $labels_keys, 'payments' => $pay_data);
		return $this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	/**
	 * Distribución por estado (JSON)
	 * URL: /home/status_chart
	 */
	public function status_chart()
	{
		if (!$this->ion_auth->logged_in()) {
			return $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(['error' => 'Unauthorized']));
		}

		$this->db->select('estado, COUNT(*) as cnt');
		$this->db->group_by('estado');
		$status_rows = $this->db->get('tb_prestamos')->result();
		$estado_map = array('0' => 'Pagado', '1' => 'Activo', '2' => 'Anulado');
		$status_labels = array();
		$status_data = array();
		foreach ($status_rows as $r) {
			$k = (string) $r->estado;
			$label = isset($estado_map[$k]) ? $estado_map[$k] : 'Estado ' . $k;
			$status_labels[] = $label;
			$status_data[] = (int) $r->cnt;
		}

		$out = array('status_labels' => $status_labels, 'status_data' => $status_data);
		return $this->output->set_content_type('application/json')->set_output(json_encode($out));
	}

	/**
	 * Endpoint KPI Stats - Retorna datos REALES del sistema
	 * URL: /home/kpi_stats
	 * Calcula: Ingresos, Egresos, Balance, Clientes Activos, Crecimiento Mensual
	 */
	public function kpi_stats()
	{
		if (!$this->ion_auth->logged_in()) {
			return $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(['error' => 'Unauthorized']));
		}

		// ========== 1. TOTAL DE INGRESOS (Últimas 30 días) ==========
		$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
		$fecha_fin = date('Y-m-d');
		
		$this->db->select('COALESCE(SUM(monto_pagado), 0) as total', false);
		$this->db->where('fecha_pago >=', $fecha_inicio);
		$this->db->where('fecha_pago <=', $fecha_fin);
		$ingresos_query = $this->db->get('tb_prestamo_pagos');
		$ingresos = $ingresos_query->row()->total;

		// ========== 2. TOTAL DE EGRESOS (Últimas 30 días) ==========
		// Egresos = Desembolsos de préstamos
		$this->db->select('COALESCE(SUM(monto_desembolsado), 0) as total', false);
		$this->db->where('desembolsado', 1);
		$this->db->where('fecha_desembolso_real >=', $fecha_inicio);
		$this->db->where('fecha_desembolso_real <=', $fecha_fin);
		$egresos_query = $this->db->get('tb_prestamos');
		$egresos = $egresos_query->row()->total;

		// ========== 3. BALANCE ACTUAL ==========
		$balance = $ingresos - $egresos;

		// ========== 4. CLIENTES ACTIVOS ==========
		$this->db->select('COUNT(*) as total', false);
		$this->db->where('estado', 1);
		$this->db->where('rechazado', 0);
		$clientes_query = $this->db->get('tb_clientes');
		$clientes_activos = (int)$clientes_query->row()->total;

		// ========== 5. CRECIMIENTO MENSUAL (Mes Actual vs Mes Anterior) ==========
		// Mes actual: pagos
		$mes_actual_inicio = date('Y-m-01');
		$mes_actual_fin = date('Y-m-t');
		
		$this->db->select('COALESCE(SUM(monto_pagado), 0) as total', false);
		$this->db->where('fecha_pago >=', $mes_actual_inicio);
		$this->db->where('fecha_pago <=', $mes_actual_fin);
		$ingresos_mes_actual = $this->db->get('tb_prestamo_pagos')->row()->total;

		// Mes anterior
		$mes_anterior_inicio = date('Y-m-01', strtotime('-1 month'));
		$mes_anterior_fin = date('Y-m-t', strtotime('-1 month'));
		
		$this->db->select('COALESCE(SUM(monto_pagado), 0) as total', false);
		$this->db->where('fecha_pago >=', $mes_anterior_inicio);
		$this->db->where('fecha_pago <=', $mes_anterior_fin);
		$ingresos_mes_anterior = $this->db->get('tb_prestamo_pagos')->row()->total;

		// Calcular crecimiento porcentual
		$crecimiento = 0;
		if ($ingresos_mes_anterior > 0) {
			$crecimiento = (($ingresos_mes_actual - $ingresos_mes_anterior) / $ingresos_mes_anterior) * 100;
		}

		// Compilar respuesta
		$output = array(
			'ingresos' => (float)$ingresos,
			'ingresos_tendencia' => ($ingresos > 0) ? 'up' : 'neutral',
			'egresos' => (float)$egresos,
			'egresos_tendencia' => ($egresos > 0) ? 'down' : 'neutral',
			'balance' => (float)$balance,
			'balance_tendencia' => ($balance > 0) ? 'up' : 'down',
			'clientes_activos' => (int)$clientes_activos,
			'clientes_tendencia' => 'up',
			'crecimiento' => round($crecimiento, 1),
			'crecimiento_tendencia' => ($crecimiento > 0) ? 'up' : 'down',
			'periodo' => 'Últimas 30 días',
			'fecha_actualizado' => date('Y-m-d H:i:s')
		);

		return $this->output->set_content_type('application/json')->set_output(json_encode($output));
	}
}
