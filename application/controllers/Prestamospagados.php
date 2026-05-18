<?php
defined('BASEPATH') or exit('Acción no permitida');
class Prestamospagados extends CI_Controller
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
			'titulo' => 'Prestamos Pagados',
			'subtitulo' => 'Lista de Prestamos Pagados',
			'icono' => 'fas fa-check-circle',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'prestamos' => $this->prestamos_model->getAllPrestamosPagados()
		);

		$this->load->view('layout/header', $data);
		$this->load->view('prestamos/pagados');
		$this->load->view('layout/footer');
	}
}
