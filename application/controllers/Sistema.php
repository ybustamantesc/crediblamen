<?php
defined('BASEPATH') or exit('Acción no permitida');
class Sistema extends CI_Controller
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
		$this->form_validation->set_rules('razon_social', 'Razón Social', 'trim|required|min_length[5]|max_length[145]');
		$this->form_validation->set_rules('email', 'Correo', 'trim|required|valid_email|max_length[100]');
		$this->form_validation->set_rules('web', 'Sitio Web', 'trim|valid_url|max_length[200]');
		$this->form_validation->set_rules('direccion', 'Sitio Web', 'trim|max_length[200]');
		$this->form_validation->set_rules('telefonos', 'Correo Electrónico', 'trim|max_length[45]');
		$this->form_validation->set_rules('mensaje_ticket', 'Mensaje', 'trim|max_length[200]');
		$this->form_validation->set_rules('idmoneda', 'Moneda', 'trim|required');
		if ($this->form_validation->run()) {
			//ACTUALIZAR DATOS
			$data = elements(
				array(
					'razon_social',
					'email',
					'web',
					'direccion',
					'telefonos',
					'mensaje_ticket',
					'idmoneda',
					'logotipo'
				),
				$this->input->post()
			);
			$data = html_escape($data);
			$imagen_actual=$this->input->post("logotipo_ant");
			if (isset($_FILES['logotipo']) && !empty($_FILES['logotipo']['name'])) {
				$img_ant=$this->core_model->get_by_id("tb_sistema",array("id"=>1));
				$fileInfo = pathinfo($_FILES['logotipo']['name']);
				$img_name = random_string('numeric', 10).'.'.$fileInfo['extension'];
				move_uploaded_file($_FILES['logotipo']['tmp_name'], 'public/img/sistema/'.$img_name);
				unlink('public/img/sistema/'.$img_ant->logotipo);
				$data['logotipo'] = $img_name;
			}else{
				$data['logotipo'] = $imagen_actual;
			}
			$this->core_model->update('tb_sistema', $data, array('id' => 1));
			redirect($this->router->fetch_class());
		} else {
			//ERROR DE VALIDACION
			$data = array(
				'titulo' => 'Modificar Información del Sistema',
				'subtitulo' => 'Ingrese lo datos solicitados.',
				'icono_view' => 'ik ik-settings ',
				'sistema' => $this->core_model->get_by_id('tb_sistema', array('id' => 1)),
				'monedas' => $this->core_model->get_all('tb_monedas')

			);

			$this->load->view('layout/header', $data);
			$this->load->view('sistema/index');
			$this->load->view('layout/footer');
		}
	}
}
