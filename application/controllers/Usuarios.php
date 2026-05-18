<?php
defined('BASEPATH') or exit('Acción no permitida');
class Usuarios extends CI_Controller
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
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para acceder a este menú.');
			redirect('/');
		}
		$data = array(
			'titulo' => 'Gestión de Usuarios',
			'subtitulo' => 'Listado de todos los usuarios registrados.',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamiento.js'
			),
			'usuarios' => $this->ion_auth->users()->result()
		);
		$this->load->view('layout/header', $data);
		$this->load->view('usuarios/index');
		$this->load->view('layout/footer');
	}
	public function core($usuario_id = NULL)
	{
		if (!$usuario_id) {
			if (!$this->ion_auth->is_admin()) {
				$this->session->set_flashdata('info', 'No tienes permiso para acceder a este menú.');
				redirect('/');
			}
			#Registrar usuario
			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required|min_length[4]|max_length[20]');
			$this->form_validation->set_rules('last_name', 'Sobrenombre', 'trim|required|min_length[5]|max_length[20]');
			$this->form_validation->set_rules('username', 'Usuario', 'trim|required|min_length[4]|max_length[30]|is_unique[users.username]');
			$this->form_validation->set_rules('email', 'Correo', 'trim|valid_email|required|min_length[5]|max_length[200]|is_unique[users.email]');

			if ($this->form_validation->run()) {

				$username = html_escape($this->input->post('first_name'));
				$password = html_escape($this->input->post('password'));
				$email = html_escape($this->input->post('email'));
				$additional_data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'active' => $this->input->post('active'),
					'perfil' => $this->input->post('perfil'),
					'idserie_recibo' => $this->input->post('idserie_recibo') ? intval($this->input->post('idserie_recibo')) : null
				);
				$group = array($this->input->post('perfil')); // Sets user to admin.
				$additional_data = html_escape($additional_data);
				if ($this->ion_auth->register($username, $password, $email, $additional_data, $group)) {
					$this->session->set_flashdata('success', 'Datos guardados');
				} else {
					$this->session->set_flashdata('error', 'No se pudo guardar los datos');
				}
				redirect($this->router->fetch_class());
			} else {
				//exit('error de validacion');
				$data = array(
					'titulo' => 'Registrar Usuario',
					'subtitulo' => 'Nuevo Usuario.',
					'icono_view' => 'ik ik-user ',
					// provide series list for select
					'series_recibos' => $this->core_model->get_all('tb_series_recibos')
				);
				// provide available groups to the view so admins can assign roles (including Promotor)
				if (method_exists($this->ion_auth, 'groups')) {
					$data['grupos'] = $this->ion_auth->groups()->result();
				} else {
					$data['grupos'] = array();
				}

				$this->load->view('layout/header', $data);
				$this->load->view('usuarios/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar Usuario
			if (!$this->ion_auth->user($usuario_id)->row()) {
				exit('Usuarios no existe');
			} else {
				if ($this->session->userdata('user_id') != $usuario_id && !$this->ion_auth->is_admin()) {
					$this->session->set_flashdata('error', 'No puedes alterar un usuario diferente al tuyo');
					redirect('/');
				}
				// Allow administrators to edit other administrators.
				// Previously this blocked editing any admin; remove that restriction so admins can manage admins.
				// Keep existing checks that non-admins cannot edit other users (handled above).

				$perfil_actual = $this->ion_auth->get_users_groups($usuario_id)->row();
				$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required|min_length[5]|max_length[20]');
				$this->form_validation->set_rules('last_name', 'Sobrenombre', 'trim|required|min_length[5]|max_length[20]');
				$this->form_validation->set_rules('username', 'Usuario', 'trim|required|min_length[5]|max_length[30]|callback_username_check');
				$this->form_validation->set_rules('email', 'Correo', 'trim|valid_email|required|min_length[5]|max_length[200]|callback_email_check');
				$this->form_validation->set_rules('password', 'Contraseña', 'trim|min_length[8]');
				$this->form_validation->set_rules('confirmacion', 'Confirmación', 'trim|matches[password]');

				if ($this->form_validation->run()) {
					$data = elements(
						array(
							'first_name',
							'last_name',
							'username',
							'email',
							'password',
							'idserie_recibo',
							'active'
						),
						$this->input->post()
					);
					if (!$this->ion_auth->is_admin()) {
						unset($data['active']);
					}
					$password = $this->input->post('password');
					if (!$password) {
						unset($data['password']);
					}
					$data = html_escape($data);
					if ($this->ion_auth->update($usuario_id, $data)) {
						$perfil_post = $this->input->post('perfil');
						if ($perfil_post) {
							if ($perfil_actual->id != $perfil_post) {
								$this->ion_auth->remove_from_group($perfil_actual->id, $usuario_id);
								$this->ion_auth->add_to_group($perfil_post, $usuario_id);
							}
						}
						$this->session->set_flashdata('success', 'Datos actualizados');
					} else {
						$this->session->set_flashdata('error', 'Errir al actualizar');
					}
					if (!$this->ion_auth->is_admin()) {
						redirect('/');
					} else {
						redirect($this->router->fetch_class());
					}
				} else {
					//Error de validacion
					$data = array(
						'titulo' => 'Editar Usuario',
						'subtitulo' => 'Editando Usuario.',
						'icono_view' => 'ik ik-user ',
						'usuario' => $this->ion_auth->user($usuario_id)->row(),
						'perfil_usuario' => $this->ion_auth->get_users_groups($usuario_id)->row()
					);
					// provide series list for select
					$data['series_recibos'] = $this->core_model->get_all('tb_series_recibos');
					// provide available groups to the view so admins can assign roles (including Promotor)
					if (method_exists($this->ion_auth, 'groups')) {
						$data['grupos'] = $this->ion_auth->groups()->result();
					} else {
						$data['grupos'] = array();
					}

					$this->load->view('layout/header', $data);
					$this->load->view('usuarios/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}
	public function username_check($username)
	{
		$usuario_id = $this->input->post('usuario_id');
		if (empty($usuario_id)) {
			// Fallback to URL segment (core/{id}) if POST field missing
			$usuario_id = $this->uri->segment(3);
			if (empty($usuario_id)) {
				$usuario_id = NULL;
			}
		}
		$username = trim($username);
		$this->db->from('users')->where('username', $username);
		if (!empty($usuario_id)) {
			$this->db->where('id !=', (int) $usuario_id);
		}
		$query = $this->db->get();
		log_message('debug', 'username_check: username=' . $username . ' usuario_id=' . var_export($usuario_id, true) . ' rows=' . $query->num_rows());
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check', 'El usuario ya existe');
			return FALSE;
		}
		return TRUE;
	}
	public function email_check($email)
	{
		$usuario_id = $this->input->post('usuario_id');
		if (empty($usuario_id)) {
			// Fallback to URL segment (core/{id}) if POST field missing
			$usuario_id = $this->uri->segment(3);
			if (empty($usuario_id)) {
				$usuario_id = NULL;
			}
		}
		$email = trim($email);
		$this->db->from('users')->where('email', $email);
		if (!empty($usuario_id)) {
			$this->db->where('id !=', (int) $usuario_id);
		}
		$query = $this->db->get();
		log_message('debug', 'email_check: email=' . $email . ' usuario_id=' . var_export($usuario_id, true) . ' rows=' . $query->num_rows());
		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('email_check', 'El correo ya existe');
			return FALSE;
		}
		return TRUE;
	}
	public function del($usuario_id = NULL)
	{
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('error', 'No tienes permiso para acceder a este menú.');
			redirect('/');
		}
		if (!$usuario_id || !$this->core_model->get_by_id('users', array('id' => $usuario_id))) {
			$this->session->set_flashdata('error', 'Usuario no encontrado');
			redirect($this->router->fetch_class());
		} else {
			if ($this->ion_auth->is_admin($usuario_id)) {
				// Prevent deleting yourself
				if ($this->session->userdata('user_id') == $usuario_id) {
					$this->session->set_flashdata('error', 'No puedes eliminar tu propia cuenta.');
					redirect($this->router->fetch_class());
				}
				// Otherwise allow admins to delete other admin accounts
			}
			if ($this->ion_auth->delete_user($usuario_id)) {
				$this->session->set_flashdata('success', 'Registro eliminado correctamente.');
				redirect($this->router->fetch_class());
			} else {
				$this->session->set_flashdata('error', 'No fue posible eliminar el registro.');
				redirect($this->router->fetch_class());
			}
		}
	}
}
