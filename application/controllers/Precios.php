<?php
defined('BASEPATH') or exit('Acción no permitida');
class Precios extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para acceder a este menú.');
			redirect('/');
		}
	}
	public function index()
	{
		$data = array(
			'titulo' => 'Gestión de Precios',
			'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
			'icono' => 'ik ik-dollar-sign',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamiento.js'
			),
			'precios' => $this->core_model->get_all('precios')
		);
		//echo '<pre>';
		//print_r($data['precios']);
		//exit();
		$this->load->view('layout/header', $data);
		$this->load->view('precios/index');
		$this->load->view('layout/footer');
	}
	public function core($precio_id = NULL)
	{
		if (!$precio_id) {
			#Registrar usuario
			$this->form_validation->set_rules('precio_categoria', 'Categoría', 'trim|required|min_length[5]|max_length[50]|is_unique[precios.precio_categoria]');
			$this->form_validation->set_rules('precio_valor_hora', 'Precio Hora', 'trim|required|max_length[50]');
			$this->form_validation->set_rules('precio_valor_mensualidad', 'Precio Menusal', 'trim|required|max_length[50]');
			$this->form_validation->set_rules('precio_numero_vacantes', 'Nro Vacantes', 'trim|required|integer|greater_than[0]');
			if ($this->form_validation->run()) {

				$data = elements(
					array(
						'precio_categoria',
						'precio_valor_hora',
						'precio_valor_mensualidad',
						'precio_numero_vacantes',
						'precio_estado'
					),
					$this->input->post()
				);
				$data = html_escape($data);
				$this->core_model->insert('precios', $data);
				redirect($this->router->fetch_class());
			} else {
				#Error de validacion.
				$data = array(
					'titulo' => 'Registrar Precios',
					'subtitulo' => 'Registrar Precio',
					'icono_view' => 'ik ik-dollar-sign ',
					'scripts' => array(
						'plugins/mask/jquery.mask.min.js',
						'plugins/mask/custom.js',

					),

				);

				$this->load->view('layout/header', $data);
				$this->load->view('precios/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar Usuario
			if (!$this->core_model->get_by_id('precios', array('precio_id' => $precio_id))) {
				$this->session->set_flashdata('error', 'Registro no encontrado.');
				redirect($this->router->fetch_class());
			} else {
				$this->form_validation->set_rules('precio_categoria', 'Categoría', 'trim|required|min_length[5]|max_length[50]|callback_check_categoria');
				$this->form_validation->set_rules('precio_valor_hora', 'Precio Hora', 'trim|required|max_length[50]');
				$this->form_validation->set_rules('precio_valor_mensualidad', 'Precio Menusal', 'trim|required|max_length[50]');
				$this->form_validation->set_rules('precio_numero_vacantes', 'Nro Vacantes', 'trim|required|integer|greater_than[0]');
				if ($this->form_validation->run()) {
					$precio_estado = $this->input->post('precio_estado');
					if ($precio_estado == 0) {
						if ($this->db->table_exists('estacionar')) {
							if ($this->core_model->get_by_id('estacionar', array('estacionar_precio_id' => $precio_id, 'estacionar_estado' => 0))) {
								$this->session->set_flashdata('error', 'Esta Categoría está siendo usuda en Estacionar.');
								redirect($this->router->fetch_class());
							}
						}
					}
					if ($precio_estado == 0) {
						if ($this->db->table_exists('matriculas')) {
							if ($this->core_model->get_by_id('matriculas', array('precioid' => $precio_id, 'estado' => 0))) {
								$this->session->set_flashdata('error', 'Esta Categoría está siendo usuada en Matrículas.');
								redirect($this->router->fetch_class());
							}
						}
					}
					$data = elements(
						array(
							'precio_categoria',
							'precio_valor_hora',
							'precio_valor_mensualidad',
							'precio_numero_vacantes',
							'precio_estado'
						),
						$this->input->post()
					);
					$data = html_escape($data);
					$this->core_model->update('precios', $data, array('precio_id' => $precio_id));
					redirect($this->router->fetch_class());
				} else {
					#Error de validacion.
					$data = array(
						'titulo' => 'Modificar Precios',
						'subtitulo' => 'Mofidicar Información de Precios',
						'icono_view' => 'ik ik-dollar-sign ',
						'scripts' => array(
							'plugins/mask/jquery.mask.min.js',
							'plugins/mask/custom.js',

						),
						'precio' => $this->core_model->get_by_id('precios', array('precio_id' => $precio_id))
					);

					$this->load->view('layout/header', $data);
					$this->load->view('precios/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}

	public function check_categoria($precio_categoria)
	{
		$precio_id = $this->input->post('precio_id');
		if ($this->core_model->get_by_id('precios', array('precio_categoria' => $precio_categoria, 'precio_id!=' => $precio_id))) {
			$this->form_validation->set_message('precio_categoria', 'Esta Categoría ya existe');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function del($precio_id = NULL)
	{
		if (!$precio_id || !$this->core_model->get_by_id('precios', array('precio_id' => $precio_id))) {
			$this->session->set_flashdata('error', 'Precio no encontrado');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('precios', array('precio_id' => $precio_id, 'precio_estado' => 1))) {
			$this->session->set_flashdata('error', 'Precio con Estado Activo no puede se eliminado.');
			redirect($this->router->fetch_class());
		}
		$this->core_model->delete('precios', array('precio_id' => $precio_id));
		redirect($this->router->fetch_class());
	}
}
