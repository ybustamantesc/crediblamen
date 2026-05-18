<?php
defined('BASEPATH') or exit('Acción no permitida');
class Cuotas extends CI_Controller
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
		$data = array(
			'titulo' => 'Estado de Cuotas',
			'subtitulo' => 'Consultar cuotas por estado.',
			'icono' => 'fas fa-users ',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
				'plugins/select2/dist/css/select2.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/select2/dist/js/select2.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				// 'plugins/datatables.net/js/activaDatatable.js',
				'js/reportes/utils.js'
			)
		);
		$this->load->view('layout/header', $data);
		$this->load->view('cuotas/index');
		$this->load->view('layout/footer');
	}
	public function pdfEstado($estado)
	{
		//$estado = $this->input->post('estado');
		if (empty($estado)) {
			$datosCuota = $this->prestamos_model->getAllCoutas();
		}
		if ($estado == 0) {
			$datosCuota = $this->prestamos_model->getCoutasPagadas();
		}
		if ($estado == 1) {
			$datosCuota = $this->prestamos_model->getCoutasPendientes();
		}
		if ($estado == 2) {
			$datosCuota = $this->prestamos_model->getCoutasVencidas();
		}
		$this->load->library('pdf');
		$file_name = "";
		if (empty($estado)) {
			$file_name = "ESTADO DE TODOS LOS CLIENTES";
		} else {
			$file_name = "ESTADO DE CLIENTES DEL - " . $estado;
		}
		$data = array(
			'estadoscuotas' => $datosCuota,
			'titulo' => $file_name
		);
		$html = $this->load->view('cuotas/export_pdf_estado', $data, TRUE);
		$this->pdf->createPDF($html, $file_name, false, 'A4', 'landscape');
	}
	public function getCuotasEstado()
	{
		$estado = $this->input->post('estado');
		if (empty($estado)) {
			$datosCuota = $this->prestamos_model->getAllCoutas();
		}
		if ($estado == 0) {
			$datosCuota = $this->prestamos_model->getCoutasPagadas();
		}
		if ($estado == 1) {
			$datosCuota = $this->prestamos_model->getCoutasPendientes();
		}
		if ($estado == 2) {
			$datosCuota = $this->prestamos_model->getCoutasVencidas();
		}
		$data = [];
		foreach ($datosCuota as $couta) {
			$fechaAtual = strtotime(date('Y-m-d'));
			$fechaVencimiento = strtotime($couta->fecha_couta);
			$estado = '';

			if ($couta->estado_couta == 1 or $couta->estado_couta == 2) {
				if ($fechaAtual == $fechaVencimiento) {
					$estado = '<span class="badge  badge-primary mb-1"><i class="fas fa-calendar-check"></i> PAGA HOY</span>';
				}
				if ($fechaAtual > $fechaVencimiento) {
					$estado = '<span class="badge  badge-danger mb-1"><i class="fas fa-exclamation-triangle"></i> VENCIÓ</span>';
				}
				if ($fechaAtual < $fechaVencimiento) {
					$estado = '<span class="badge  badge-warning mb-1"><i class="fas fa-sync-alt"></i> PENDIENTE</span>';
				}
			} else {
				$estado = '<span class="badge  badge-success mb-1"><i class="fas fa-check-circle"></i> CANCELADO</span>';
			}

			$data[] = [
				'cliente' => $couta->apellidos . ', ' . $couta->nombres,
				'asesor' => $couta->nombre_asesor,
				'numero_couta' => $couta->numero_couta,
				'fecha_couta' => $couta->fecha_couta,
				'fecha_pago' => $couta->fecha_pago,
				'monto_pagado' => $couta->monto_pagado,
				'monto_couta' => $couta->monto_couta,
				'monto_pendiente' => $couta->monto_pendiente,
				'estado' => $estado
			];
		}
		$retorna = [
			'data' => $data,
		];
		echo json_encode($retorna);
	}
}
