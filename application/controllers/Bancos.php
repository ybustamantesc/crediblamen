<?php
defined('BASEPATH') or exit('Acción no permitida');
class Bancos extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
	}
	public function index()
	{
		$data = array(
			'titulo' => 'Gestión de Bancos',
			'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
			'icono' => 'fas fa-university',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'bancos' => $this->core_model->get_all('tb_bancos')
		);
		$this->load->view('layout/header', $data);
		$this->load->view('bancos/index');
		$this->load->view('layout/footer');
	}
	public function core($banco_id = NULL)
	{
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
			redirect($this->router->fetch_class());
		}
		if (!$banco_id) {
			#Registrar 
			$this->form_validation->set_rules('nombre', 'Nombre Banco', 'trim|required|min_length[4]|max_length[30]|is_unique[tb_bancos.nombre]');
			if ($this->form_validation->run()) {
				$data = elements(
					array(
						'nombre',
						'estado'
					),
					$this->input->post()
				);
				$data = html_escape($data);
				$this->core_model->insert('tb_bancos', $data);
				redirect($this->router->fetch_class());
			} else {
				#Error de validacion.
				$data = array(
					'titulo' => 'Registrar Nuevo Banco',
					'subtitulo' => 'Ingrese los datos solicitados',
					'icono_view' => 'fas fa-university ',
					'scripts' => array(
						'plugins/mask/jquery.mask.min.js'
					),
				);
				$this->load->view('layout/header', $data);
				$this->load->view('bancos/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar
			if (!$this->core_model->get_by_id('tb_bancos', array('id' => $banco_id))) {
				$this->session->set_flashdata('error', 'Registro no encontrado.');
				redirect($this->router->fetch_class());
			} else {
				$this->form_validation->set_rules('nombre', 'Nombre Banco', 'trim|required|min_length[4]|max_length[30]|callback_check_banco');
				if ($this->form_validation->run()) {
					$data = elements(
						array(
							'nombre',
							'estado'
						),
						$this->input->post()
					);
					$data = html_escape($data);
					$this->core_model->update('tb_bancos', $data, array('id' => $banco_id));
					redirect($this->router->fetch_class());
				} else {
					#Error de validacion.
					$data = array(
						'titulo' => 'Modificar Banco',
						'subtitulo' => 'Ingrese los datos solicitados',
						'icono_view' => 'fas fa-university ',
						'scripts' => array(
							'plugins/mask/jquery.mask.min.js'
						),
						'banco' => $this->core_model->get_by_id('tb_bancos', array('id' => $banco_id))
					);
					$this->load->view('layout/header', $data);
					$this->load->view('bancos/core');
					$this->load->view('layout/footer');
				}
			}
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
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para eliminar.');
			redirect($this->router->fetch_class());
		}
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
}
