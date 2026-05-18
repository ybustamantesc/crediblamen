<?php
defined('BASEPATH') or exit('Acción no permitida');
class Asesores extends CI_Controller
{
	/**
	 * Devuelve la lista de asesores activos en formato JSON para AJAX
	 */
	public function list_json()
	{
		// Solo asesores activos
		$asesores = $this->core_model->get_all('tb_asesores', array('estado' => 1));
		header('Content-Type: application/json');
		echo json_encode(['status' => true, 'asesores' => $asesores]);
		exit;
	}
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}

		// Deny access to this controller for users in 'promotor' group or with legacy perfil == 4
		$is_promotor = false;
		try {
			if (isset($this->ion_auth) && method_exists($this->ion_auth, 'in_group')) {
				$is_promotor = $this->ion_auth->in_group('promotor');
			}
		} catch (Exception $e) { /* ignore */ }
		// fallback: check session/profile value
		$usuario = $this->ion_auth->user()->row();
		if (!$is_promotor && isset($usuario->perfil) && intval($usuario->perfil) === 4) {
			$is_promotor = true;
		}
		if ($is_promotor) {
			// Redirect promotor users away from this controller
			$this->session->set_flashdata('error', 'Acceso denegado: no tienes permisos para acceder a Asesores.');
			redirect('home');
		}
	}
	public function index()
	{
		$data = array(
			'titulo' => 'Gestión de Asesores',
			'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
			'icono' => 'ik ik-users ',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'asesores' => $this->core_model->get_all('tb_asesores')
		);

		$this->load->view('layout/header', $data);
		$this->load->view('asesores/index');
		$this->load->view('layout/footer');
	}
	public function core($asedor_id = NULL)
	{
		if (!$asedor_id) {
			#Registrar

			$this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[50]');
			$this->form_validation->set_rules('telefono', 'Teléfono', 'trim|required|min_length[4]|max_length[30]');
			$this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|min_length[5]|max_length[45]');
			if ($this->form_validation->run()) {
				$data = elements(
					array(
						'nombres',
						'telefono',
						'direccion'
					),
					$this->input->post()
				);
				$fechaActual = date('Y-m-d H:i:s');
				$data["fechaRegistro"] = $fechaActual;
				$data = html_escape($data);
				$this->core_model->insert('tb_asesores', $data);
				redirect($this->router->fetch_class());
			} else {
				//exit('error de validacion');
				$data = array(
					'titulo' => 'Registrar Asesor',
					'subtitulo' => 'Ingrese los datos del nuevo Asesor.',
					'icono_view' => 'ik ik-user ',
					'scripts' => array(
						'js/utils/utils.js'
					)
				);
				$this->load->view('layout/header', $data);
				$this->load->view('asesores/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar 
			if (!$this->core_model->get_by_id('tb_asesores', array('idasesor' => $asedor_id))) {
				$this->session->set_flashdata('error', 'Registro no encontrado');
				redirect($this->router->fetch_class());
			} else {
				$this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[50]');
				$this->form_validation->set_rules('telefono', 'Teléfono', 'trim|required|min_length[4]|max_length[30]');
				$this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|min_length[5]|max_length[45]');

				if ($this->form_validation->run()) {
					$asesor_estado = $this->input->post('estado');
					if ($asesor_estado == 0) {
						if ($this->db->table_exists('tb_creditos')) {
							if ($this->core_model->get_by_id('tb_creditos', array('idasesor' => $asedor_id))) {
								$this->session->set_flashdata('error', 'Este Asesor no puede ser desactivado tiene relacion con Créditos.');
								redirect($this->router->fetch_class());
							}
						}
					}
					$data = elements(
						array(
							'nombres',
							'telefono',
							'direccion',
							'estado'
						),
						$this->input->post()
					);
					// echo '<pre>';
					// print_r($data);
					// exit();
					$data = html_escape($data);
					$this->core_model->update('tb_asesores', $data, array('idasesor' => $asedor_id));
					redirect($this->router->fetch_class());
				} else {
					//Error de validacion
					$data = array(
						'titulo' => 'Editar Asesor',
						'subtitulo' => 'Realice los cambios que desee del Asesor.',
						'icono_view' => 'ik ik-user ',
						'asesor' => $this->core_model->get_by_id('tb_asesores', array('idasesor' => $asedor_id))
					);
					$this->load->view('layout/header', $data);
					$this->load->view('asesores/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}
	public function del($asedor_id = NULL)
	{
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para eliminar clientes.');
			redirect('/');
		}
		if (!$asedor_id || !$this->core_model->get_by_id('tb_asesores', array('idasesor' => $asedor_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('tb_asesores', array('idasesor' => $asedor_id, 'estado' => 1))) {
			$this->session->set_flashdata('error', 'El Asesor con Estado Activo no puede se eliminado.');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('tb_creditos', array('idasesor' => $asedor_id))) {
			$this->session->set_flashdata('error', 'El Asesor no puede ser eliminado, tiene créditos');
			redirect($this->router->fetch_class());
		}
		$this->core_model->delete('tb_asesores', array('idasesor' => $asedor_id));
		redirect($this->router->fetch_class());
	}
}
