<?php
defined('BASEPATH') or exit('Acción no permitida');
class Clientes extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
	}

	public function download_rechazo($id = NULL)
	{
		// Permisos: cualquier usuario autenticado puede descargar (ajustar si se necesita)
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}

		if (!$id) {
			show_404();
		}

		// Try to find row in tb_clientes_rechazados by id
		$row = NULL;
		if ($this->db->table_exists('tb_clientes_rechazados')) {
			$row = $this->core_model->get_by_id('tb_clientes_rechazados', array('id' => $id));
			// If not found, try to treat $id as idcliente_original
			if (!$row) {
				$row = $this->db->order_by('rechazado_en', 'DESC')->get_where('tb_clientes_rechazados', array('idcliente_original' => $id))->row();
			}
			// If still not found and there is a client with this id, try by numero_doc
			if (!$row) {
				$main = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $id));
				if ($main && !empty($main->numero_doc)) {
					$row = $this->db->order_by('rechazado_en', 'DESC')->get_where('tb_clientes_rechazados', array('numero_doc' => $main->numero_doc))->row();
				}
			}
		}

		if (!$row) {
			$this->session->set_flashdata('error', 'Registro de rechazo no encontrado para descargar.');
			redirect($this->router->fetch_class() . '/rechazados');
		}

		// Build content (plain text) including rejection and restoration details
		$content = "Rechazo / Restauración - Cliente\n";
		$content .= "ID rechazo: " . (isset($row->id) ? $row->id : '') . "\n";
		$content .= "ID cliente original: " . (isset($row->idcliente_original) ? $row->idcliente_original : '') . "\n";
		$content .= "Nombre: " . (isset($row->apellidos) ? $row->apellidos : '') . ", " . (isset($row->nombres) ? $row->nombres : '') . "\n";
		$content .= "Documento: " . (isset($row->numero_doc) ? $row->numero_doc : '') . "\n";
		$content .= "Telefono: " . (isset($row->telefono) ? $row->telefono : '') . "\n\n";
		$content .= "Motivo de rechazo:\n" . (isset($row->rechazo_motivo) ? $row->rechazo_motivo : '') . "\n\n";
		if (!empty($row->restaurado_en) || !empty($row->restaurado_comentario)) {
			$content .= "Restauracion:\n";
			$content .= "Comentario: " . (isset($row->restaurado_comentario) ? $row->restaurado_comentario : '') . "\n";
			$content .= "Restaurado por: " . (isset($row->restaurado_por) ? $row->restaurado_por : '') . "\n";
			$content .= "Restaurado en: " . (isset($row->restaurado_en) ? $row->restaurado_en : '') . "\n";
		}

		$filename = 'rechazo_' . (isset($row->id) ? $row->id : time()) . '.txt';

		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo $content;
		exit;
	}

	public function download_rechazo_pdf($id = NULL)
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
		if (!$id) {
			show_404();
		}
		$row = NULL;
		if ($this->db->table_exists('tb_clientes_rechazados')) {
			$row = $this->core_model->get_by_id('tb_clientes_rechazados', array('id' => $id));
			if (!$row) {
				$row = $this->db->order_by('rechazado_en', 'DESC')->get_where('tb_clientes_rechazados', array('idcliente_original' => $id))->row();
			}
			if (!$row) {
				$main = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $id));
				if ($main && !empty($main->numero_doc)) {
					$row = $this->db->order_by('rechazado_en', 'DESC')->get_where('tb_clientes_rechazados', array('numero_doc' => $main->numero_doc))->row();
				}
			}
		}
		if (!$row) {
			$this->session->set_flashdata('error', 'Registro de rechazo no encontrado para descargar.');
			redirect($this->router->fetch_class() . '/rechazados');
		}

		// Try to get restaurado_por nombre
		$restaurado_por_nombre = '';
		if (!empty($row->restaurado_por)) {
			$user_row = $this->db->get_where('users', array('id' => $row->restaurado_por))->row();
			if ($user_row) {
				$restaurado_por_nombre = isset($user_row->first_name) ? trim($user_row->first_name . ' ' . (isset($user_row->last_name) ? $user_row->last_name : '')) : (isset($user_row->username) ? $user_row->username : 'Usuario ' . $row->restaurado_por);
			}
		}

		// Build simple HTML for PDF
		$html = '<h2>Detalle de Rechazo / Restauración</h2>';
		$html .= '<p><strong>ID Rechazo:</strong> ' . (isset($row->id) ? $row->id : '') . '</p>';
		$html .= '<p><strong>ID Cliente original:</strong> ' . (isset($row->idcliente_original) ? $row->idcliente_original : '') . '</p>';
		$html .= '<p><strong>Nombre:</strong> ' . (isset($row->apellidos) ? $row->apellidos : '') . ', ' . (isset($row->nombres) ? $row->nombres : '') . '</p>';
		$html .= '<p><strong>Documento:</strong> ' . (isset($row->numero_doc) ? $row->numero_doc : '') . '</p>';
		$html .= '<hr/>';
		$html .= '<h4>Motivo de rechazo</h4>';
		$html .= '<p>' . nl2br(htmlspecialchars(isset($row->rechazo_motivo) ? $row->rechazo_motivo : '')) . '</p>';
		if (!empty($row->restaurado_en) || !empty($row->restaurado_comentario)) {
			$html .= '<hr/>';
			$html .= '<h4>Datos de restauración</h4>';
			$html .= '<p><strong>Comentario:</strong><br/>' . nl2br(htmlspecialchars(isset($row->restaurado_comentario) ? $row->restaurado_comentario : '')) . '</p>';
			$html .= '<p><strong>Restaurado por:</strong> ' . htmlspecialchars($restaurado_por_nombre) . '</p>';
			$html .= '<p><strong>Restaurado en:</strong> ' . (isset($row->restaurado_en) ? $row->restaurado_en : '') . '</p>';
		}

		// Load Dompdf
		if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
			require_once FCPATH . 'dompdf/autoload.inc.php';
			$dompdf = new \Dompdf\Dompdf();
			$dompdf->loadHtml('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . $html);
			$dompdf->setPaper('A4', 'portrait');
			$dompdf->render();
			$filename = 'rechazo_' . (isset($row->id) ? $row->id : time()) . '.pdf';
			$dompdf->stream($filename, array('Attachment' => 1));
			exit;
		} else {
			$this->session->set_flashdata('error', 'Generador de PDF no disponible (dompdf no encontrado).');
			redirect($this->router->fetch_class() . '/rechazados');
		}
	}
	public function index()
	{
		// Verificar permisos: Promotor (perfil 4) no debe ver la lista de clientes
		$user = $this->ion_auth->user()->row();
		if (isset($user->perfil) && $user->perfil == 4) {
			$this->session->set_flashdata('info', 'No tienes acceso a la gestión de clientes.');
			redirect('solicitudes');
		}
		
		$data = array(
			'titulo' => 'Gestión de Clientes',
			'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
			'icono' => 'fas fa-users ',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
				'plugins/select2/dist/css/select2.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/select2/dist/js/select2.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'clientes' => $this->core_model->get_all('tb_clientes')
		);

		$this->load->view('layout/header', $data);
		$this->load->view('clientes/index');
		$this->load->view('layout/footer');
	}

	public function rechazados()
	{
		// Show the list of rejected applicants. Exclude rows that were already restaurado (restaurado_en IS NOT NULL)
		$clientes = array();
		if ($this->db->table_exists('tb_clientes_rechazados')) {
			// Fetch all rejected rows; the view will mark restored ones visually.
			$this->db->order_by('rechazado_en', 'DESC');
			$clientes = $this->db->get('tb_clientes_rechazados')->result();
			// Resolve restaurado_por user name for display (if column exists)
			if (!empty($clientes)) {
				foreach ($clientes as &$c) {
					$c->restaurado_por_nombre = null;
					if (!empty($c->restaurado_por)) {
						$user_row = $this->db->get_where('users', array('id' => $c->restaurado_por))->row();
						if ($user_row) {
							if (isset($user_row->first_name) || isset($user_row->last_name)) {
								$c->restaurado_por_nombre = trim((isset($user_row->first_name) ? $user_row->first_name : '') . ' ' . (isset($user_row->last_name) ? $user_row->last_name : ''));
							} elseif (isset($user_row->username)) {
								$c->restaurado_por_nombre = $user_row->username;
							} else {
								$c->restaurado_por_nombre = 'Usuario ' . $c->restaurado_por;
							}
						}
					}
				}
				unset($c);
			}
		}
		$data = array(
			'titulo' => 'Clientes Rechazados',
			'subtitulo' => 'Solicitudes y clientes no aprobados',
			'icono' => 'fas fa-user-times ',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
				'plugins/select2/dist/css/select2.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/select2/dist/js/select2.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'clientes' => $clientes
		);

		$this->load->view('layout/header', $data);
		$this->load->view('clientes/rechazados');
		$this->load->view('layout/footer');
	}

	/**
	 * AJAX: Buscar cliente por número de documento y devolver JSON
	 * GET/POST param: numero_doc
	 */
	public function ajax_find_by_doc($numero_doc = NULL)
	{
		if (!$this->ion_auth->logged_in()) {
			show_404();
		}
		$doc = $numero_doc ? $numero_doc : $this->input->get_post('numero_doc');
		$this->output->set_content_type('application/json');
		if (!$doc) {
			echo json_encode(array('success' => false, 'message' => 'numero_doc requerido'));
			return;
		}
		$doc = trim($doc);
		$client = $this->core_model->get_by_id('tb_clientes', array('numero_doc' => $doc));
		// also fetch the most recent solicitud that matches this documento (if any)
		$last_solicitud = null;
		if ($this->db->table_exists('tb_solicitudes')) {
			$last_solicitud = $this->db->order_by('fecha_solicitud', 'DESC')->get_where('tb_solicitudes', array('numero_doc' => $doc), 1)->row();
			if (!$last_solicitud) {
				// fallback: try other possible column names
				$possible = array('numero_documento','documento','cedula','identificacion');
				foreach ($possible as $col) {
					if ($this->db->field_exists($col, 'tb_solicitudes')) {
						$last_solicitud = $this->db->order_by('fecha_solicitud', 'DESC')->get_where('tb_solicitudes', array($col => $doc), 1)->row();
						if ($last_solicitud) break;
					}
				}
			}
		}
		// detect if this documento is associated with any existing credit records
		$has_credit = false;
		if ($this->db->table_exists('tb_creditos')) {
			if ($client && isset($client->idcliente) && $client->idcliente) {
				$credit = $this->db->limit(1)->get_where('tb_creditos', array('idcliente' => $client->idcliente))->row();
				if ($credit) $has_credit = true;
			}
			if (!$has_credit) {
				// try to find credit rows by numero_doc if table stores that value
				if ($this->db->field_exists('numero_doc', 'tb_creditos')) {
					$credit2 = $this->db->limit(1)->get_where('tb_creditos', array('numero_doc' => $doc))->row();
					if ($credit2) $has_credit = true;
				}
			}
		}

		echo json_encode(array('success' => ($client ? true : false), 'cliente' => $client, 'last_solicitud' => $last_solicitud, 'has_credit' => $has_credit));
	}
	public function core($cliente_id = NULL)
	{
		if (!$cliente_id) {
			#Registrar

			$this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
			$this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|min_length[5]|max_length[100]');
			$this->form_validation->set_rules('telefono', 'Teléfono', 'trim|required|min_length[4]|max_length[30]');
			$this->form_validation->set_rules('tipo_doc', 'Tipo Documento', 'trim|required');
			$this->form_validation->set_rules('numero_doc', 'Nro Documento', 'trim|required');
			if ($this->form_validation->run()) {
				$data = elements(
					array(
						'apellidos', 'nombres', 'direccion', 'telefono', 'tipo_doc', 'numero_doc', 'estado', 'comentarios',
						'fecha_nacimiento','edad','estado_civil','nombre_conyuge','dni_conyuge','ocupacion_conyuge','telefono_conyuge','numero_dependientes','condicion_vivienda','tiempo_residir_anios','tiempo_residir_meses',
						'nombre_empresa','direccion_empresa','telefono_empresa','cargo_puesto','tiempo_empleo_anios','tiempo_empleo_meses','tipo_contrato','ingreso_mensual_neto','deducciones',
						'nombre_negocio','actividad_economica','telefono_negocio','tiempo_operacion_anios','tiempo_operacion_meses','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual'
					),
					$this->input->post()
				);
				// Normalize data: convert empty strings to NULL, normalize dates and numeric fields
				foreach ($data as $k => $v) {
					if ($v === '') { $data[$k] = null; }
				}
				// fecha_nacimiento to Y-m-d or NULL
				if (isset($data['fecha_nacimiento']) && $data['fecha_nacimiento'] !== null) {
					$fd = trim((string)$data['fecha_nacimiento']);
					if ($fd === '' || $fd === '0000-00-00') { $data['fecha_nacimiento'] = null; }
					else { $ts = strtotime($fd); $data['fecha_nacimiento'] = ($ts === false ? null : date('Y-m-d', $ts)); }
				}
				// integer fields
				$intFields = array('edad','numero_dependientes','tiempo_residir_anios','tiempo_residir_meses','tiempo_empleo_anios','tiempo_empleo_meses','tiempo_operacion_anios','tiempo_operacion_meses');
				foreach ($intFields as $if) {
					if (isset($data[$if])) {
						$val = $data[$if];
						if ($val === null || $val === '' || !is_numeric($val)) { $data[$if] = null; } else { $data[$if] = (int)$val; }
					}
				}
				// decimal fields
				$floatFields = array('ingreso_mensual_neto','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual');
				foreach ($floatFields as $ff) {
					if (isset($data[$ff])) {
						$val = $data[$ff];
						if ($val === null || $val === '' || !is_numeric(str_replace(',','.',(string)$val))) { $data[$ff] = null; } else { $data[$ff] = floatval(str_replace(',','.',(string)$val)); }
					}
				}
				// tipo_doc as integer (default 0)
				if (isset($data['tipo_doc'])) {
					if ($data['tipo_doc'] === null || $data['tipo_doc'] === '') { $data['tipo_doc'] = 0; }
					else { $data['tipo_doc'] = is_numeric($data['tipo_doc']) ? (int)$data['tipo_doc'] : 0; }
				}
				$data = html_escape($data);
				$this->core_model->insert('tb_clientes', $data);
				// Redirigir a solicitudes después de guardar
				redirect('solicitudes');
			} else {
				//exit('error de validacion');
				$data = array(
					'titulo' => 'Registrar Cliente',
					'subtitulo' => 'Ingrese los datos del nuevo Cliente.',
					'icono_view' => 'ik ik-user ',
					'scripts' => array(
						'js/utils/utils.js'
					)
				);
				$this->load->view('layout/header', $data);
				$this->load->view('clientes/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar 
			// Verificar permisos: Promotor (perfil 4) no puede editar
			$user = $this->ion_auth->user()->row();
			if (isset($user->perfil) && $user->perfil == 4) {
				$this->session->set_flashdata('info', 'No tienes permiso para editar clientes.');
				redirect($this->router->fetch_class());
			}
			if (!$this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id))) {
				$this->session->set_flashdata('error', 'Registro no encontrado');
				redirect($this->router->fetch_class());
			} else {

				$this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|min_length[3]|max_length[50]');
				$this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|min_length[3]|max_length[40]');
				$this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|min_length[5]|max_length[100]');
				$this->form_validation->set_rules('telefono', 'Teléfono', 'trim|required|min_length[4]|max_length[30]');
				$this->form_validation->set_rules('tipo_doc', 'Tipo Documento', 'trim|required');
				$this->form_validation->set_rules('numero_doc', 'Nro Documento', 'trim|required');
				if ($this->form_validation->run()) {

					$cliente_estado = $this->input->post('estado');
					if ($cliente_estado == 0) {
						if ($this->db->table_exists('tb_creditos')) {
							if ($this->core_model->get_by_id('tb_creditos', array('idcliente' => $cliente_id))) {
								$this->session->set_flashdata('error', 'Este Cliente no puede ser desactivado tiene relacion con Créditos.');
								redirect($this->router->fetch_class());
							}
						}
					}
					$data = elements(
						array(
							'apellidos','nombres','direccion','telefono','tipo_doc','numero_doc','estado','comentarios',
							'fecha_nacimiento','edad','estado_civil','nombre_conyuge','dni_conyuge','ocupacion_conyuge','telefono_conyuge','numero_dependientes','condicion_vivienda','tiempo_residir_anios','tiempo_residir_meses',
							'nombre_empresa','direccion_empresa','telefono_empresa','cargo_puesto','tiempo_empleo_anios','tiempo_empleo_meses','tipo_contrato','ingreso_mensual_neto','deducciones',
							'nombre_negocio','actividad_economica','telefono_negocio','tiempo_operacion_anios','tiempo_operacion_meses','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual'
						),
						$this->input->post()
					);

					// Normalize data before update: convert empty strings to NULL, normalize dates and numeric fields
					foreach ($data as $k => $v) {
						if ($v === '') { $data[$k] = null; }
					}
					// fecha_nacimiento to Y-m-d or NULL
					if (isset($data['fecha_nacimiento']) && $data['fecha_nacimiento'] !== null) {
						$fd = trim((string)$data['fecha_nacimiento']);
						if ($fd === '' || $fd === '0000-00-00') { $data['fecha_nacimiento'] = null; }
						else { $ts = strtotime($fd); $data['fecha_nacimiento'] = ($ts === false ? null : date('Y-m-d', $ts)); }
					}
					// integer fields
					$intFields = array('edad','numero_dependientes','tiempo_residir_anios','tiempo_residir_meses','tiempo_empleo_anios','tiempo_empleo_meses','tiempo_operacion_anios','tiempo_operacion_meses');
					foreach ($intFields as $if) {
						if (isset($data[$if])) {
							$val = $data[$if];
							if ($val === null || $val === '' || !is_numeric($val)) { $data[$if] = null; } else { $data[$if] = (int)$val; }
						}
					}
					// decimal fields
					$floatFields = array('ingreso_mensual_neto','ventas_buenos_amount','ventas_malos_amount','ventas_promedio_mensual');
					foreach ($floatFields as $ff) {
						if (isset($data[$ff])) {
							$val = $data[$ff];
							if ($val === null || $val === '' || !is_numeric(str_replace(',','.',(string)$val))) { $data[$ff] = null; } else { $data[$ff] = floatval(str_replace(',','.',(string)$val)); }
						}
					}
					// tipo_doc as integer (default 0)
					if (isset($data['tipo_doc'])) {
						if ($data['tipo_doc'] === null || $data['tipo_doc'] === '') { $data['tipo_doc'] = 0; }
						else { $data['tipo_doc'] = is_numeric($data['tipo_doc']) ? (int)$data['tipo_doc'] : 0; }
					}
					$data = html_escape($data);
					$this->core_model->update('tb_clientes', $data, array('idcliente' => $cliente_id));
				// Redirigir a solicitudes después de guardar
				redirect('solicitudes');
				} else {
					//Error de validacion
					$data = array(
						'titulo' => 'Editar Cliente',
						'subtitulo' => 'Realice los cambios que desee del Cliente.',
						'icono_view' => 'ik ik-user ',
						'scripts' => array(
							'js/utils/utils.js'
						),
						'cliente' => $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id))
					);
					$this->load->view('layout/header', $data);
					$this->load->view('clientes/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}
	public function del($cliente_id = NULL)
	{
		// Allow admins but NOT Promotor (perfil 4)
		$user = $this->ion_auth->user()->row();
		if (!$this->ion_auth->is_admin() || (isset($user->perfil) && $user->perfil == 4)) {
			$this->session->set_flashdata('info', 'No tienes permiso para eliminar clientes.');
			redirect('/');
		}
		if (!$cliente_id || !$this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id, 'estado' => 1))) {
			$this->session->set_flashdata('error', 'Cliente con Estado Activo no puede se eliminado.');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('tb_creditos', array('idcliente' => $cliente_id))) {
			$this->session->set_flashdata('error', 'El cliente no puede ser eliminado, tiene créditos');
			redirect($this->router->fetch_class());
		}
		$this->core_model->delete('tb_clientes', array('idcliente' => $cliente_id));
		redirect($this->router->fetch_class());
	}

	public function mark_rejected($cliente_id = NULL)
	{
		// Allow Admins and Supervisors (perfil == 2), but NOT Promotor (perfil == 4)
		$user = $this->ion_auth->user()->row();
		$can_reject = ($this->ion_auth->is_admin() || (isset($user->perfil) && $user->perfil == 2));
		// Explicitly deny Promotor even if they are admin
		if ((isset($user->perfil) && $user->perfil == 4) || ! $can_reject) {
			$this->session->set_flashdata('info', 'No tienes permiso para marcar clientes como rechazados.');
			redirect('/');
		}

		if (!$cliente_id || !$this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}

		// Only accept POST requests with motivo
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			$this->session->set_flashdata('error', 'Solicitud inválida.');
			redirect($this->router->fetch_class());
		}

		$cliente = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id));
		$motivo = $this->input->post('rechazo_motivo', TRUE);
		$motivo = $motivo ? html_escape($motivo) : NULL;

		// Ensure rejected table exists; if not, create it inline
		if (! $this->db->table_exists('tb_clientes_rechazados')) {
			$sql = "CREATE TABLE IF NOT EXISTS `tb_clientes_rechazados` (
			  `id` INT NOT NULL AUTO_INCREMENT,
			  `idcliente_original` INT DEFAULT NULL,
			  `apellidos` VARCHAR(50) NOT NULL,
			  `nombres` VARCHAR(80) NOT NULL,
			  `direccion` VARCHAR(200) DEFAULT NULL,
			  `telefono` VARCHAR(30) DEFAULT NULL,
			  `tipo_doc` TINYINT(2) DEFAULT NULL,
			  `numero_doc` VARCHAR(60) DEFAULT NULL,
			  `comentarios` TEXT,
			  `rechazo_motivo` VARCHAR(255) DEFAULT NULL,
			  `rechazado_por` INT DEFAULT NULL,
			  `rechazado_en` DATETIME DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
			$this->db->query($sql);
		}

		// Insert snapshot into rejected table
		$insert_user_id = isset($user->id) ? $user->id : NULL;
		// Ensure idcliente_original column exists (for older installs that have the table without this column)
		if ($this->db->table_exists('tb_clientes_rechazados') && ! $this->db->field_exists('idcliente_original', 'tb_clientes_rechazados')) {
			$this->db->query("ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `idcliente_original` INT DEFAULT NULL AFTER `id`");
		}
		$insert = array(
			'idcliente_original' => $cliente_id,
			'apellidos' => $cliente->apellidos,
			'nombres' => $cliente->nombres,
			'direccion' => $cliente->direccion,
			'telefono' => $cliente->telefono,
			'tipo_doc' => $cliente->tipo_doc,
			'numero_doc' => $cliente->numero_doc,
			'comentarios' => isset($cliente->comentarios) ? $cliente->comentarios : NULL,
			'rechazo_motivo' => $motivo,
			'rechazado_por' => $insert_user_id,
			'rechazado_en' => date('Y-m-d H:i:s')
		);
		$this->core_model->insert('tb_clientes_rechazados', $insert);

		// Add 'rechazado' column to tb_clientes if missing, then mark
		if (! $this->db->field_exists('rechazado', 'tb_clientes')) {
			$this->db->query("ALTER TABLE `tb_clientes` ADD COLUMN `rechazado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `estado`");
		}
		// Also add a 'rechazo_motivo' column to tb_clientes so main list can show it
		if (! $this->db->field_exists('rechazo_motivo', 'tb_clientes')) {
			$this->db->query("ALTER TABLE `tb_clientes` ADD COLUMN `rechazo_motivo` VARCHAR(255) DEFAULT NULL AFTER `rechazado`");
		}
		$this->core_model->update('tb_clientes', array('rechazado' => 1, 'estado' => 0, 'rechazo_motivo' => $motivo), array('idcliente' => $cliente_id));

		$this->session->set_flashdata('success', 'Cliente marcado como rechazado y movido a la lista de rechazados.');
		redirect($this->router->fetch_class());
	}

	public function restore_rejected($cliente_id = NULL)
	{
		// Allow Admins and Supervisors
		$user = $this->ion_auth->user()->row();
		$can_restore = ($this->ion_auth->is_admin() || (isset($user->perfil) && $user->perfil == 2));
		if (! $can_restore) {
			$this->session->set_flashdata('info', 'No tienes permiso para restaurar clientes.');
			redirect('/');
		}

		if (!$cliente_id) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			$this->session->set_flashdata('error', 'Solicitud inválida.');
			redirect($this->router->fetch_class());
		}

		$comentario = $this->input->post('restauracion_comentario', TRUE);
		$comentario = $comentario ? html_escape($comentario) : NULL;

		// Determine whether $cliente_id is an idcliente (tb_clientes) or an id in tb_clientes_rechazados
		$target_cliente_id = NULL;
		$rejected_row = NULL;

		// If it exists in tb_clientes, use it directly
		if ($this->core_model->get_by_id('tb_clientes', array('idcliente' => $cliente_id))) {
			$target_cliente_id = $cliente_id;
		} else {
			// Check if it's an id in tb_clientes_rechazados
			$rejected_row = $this->core_model->get_by_id('tb_clientes_rechazados', array('id' => $cliente_id));
			if ($rejected_row) {
				// Prefer idcliente_original if available
				if (!empty($rejected_row->idcliente_original)) {
					$target_cliente_id = $rejected_row->idcliente_original;
				} else {
					// Fallback: try to find the client by numero_doc
					if (!empty($rejected_row->numero_doc)) {
						$client_match = $this->core_model->get_by_id('tb_clientes', array('numero_doc' => $rejected_row->numero_doc));
						if ($client_match) {
							$target_cliente_id = $client_match->idcliente;
						}
					}
				}
			}
		}

		if (empty($target_cliente_id)) {
			$this->session->set_flashdata('error', 'Registro no encontrado para restaurar.');
			redirect($this->router->fetch_class() . '/rechazados');
		}

		// Update main client: unset rechazado, clear motivo and reactivate
		$update_main = array('rechazado' => 0, 'estado' => 1);
		if ($this->db->field_exists('rechazo_motivo', 'tb_clientes')) {
			$update_main['rechazo_motivo'] = NULL;
		}
		$this->core_model->update('tb_clientes', $update_main, array('idcliente' => $target_cliente_id));

		// Ensure restoration columns exist in tb_clientes_rechazados
		if ($this->db->table_exists('tb_clientes_rechazados')) {
			if (! $this->db->field_exists('restaurado_comentario', 'tb_clientes_rechazados')) {
				$this->db->query("ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_comentario` VARCHAR(255) DEFAULT NULL AFTER `rechazo_motivo`");
			}
			if (! $this->db->field_exists('restaurado_por', 'tb_clientes_rechazados')) {
				$this->db->query("ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_por` INT DEFAULT NULL AFTER `restaurado_comentario`");
			}
			if (! $this->db->field_exists('restaurado_en', 'tb_clientes_rechazados')) {
				$this->db->query("ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_en` DATETIME DEFAULT NULL AFTER `restaurado_por`");
			}

			// Prefer matching by idcliente_original when available, otherwise fallback to numero_doc
			$last = NULL;
			if ($this->db->field_exists('idcliente_original', 'tb_clientes_rechazados')) {
				$this->db->order_by('rechazado_en', 'DESC');
				$this->db->limit(1);
				$last = $this->db->get_where('tb_clientes_rechazados', array('idcliente_original' => $target_cliente_id))->row();
			}
			if (!$last) {
				$main = $this->core_model->get_by_id('tb_clientes', array('idcliente' => $target_cliente_id));
				if (!empty($main->numero_doc)) {
					$this->db->order_by('rechazado_en', 'DESC');
					$this->db->limit(1);
					$last = $this->db->get_where('tb_clientes_rechazados', array('numero_doc' => $main->numero_doc))->row();
				}
			}
			if ($last) {
				$this->core_model->update('tb_clientes_rechazados', array(
					'restaurado_comentario' => $comentario,
					'restaurado_por' => isset($user->id) ? $user->id : NULL,
					'restaurado_en' => date('Y-m-d H:i:s')
				), array('id' => $last->id));
			}
		}

		$this->session->set_flashdata('success', 'Cliente restaurado correctamente. Se registró un comentario de restauración.');
		redirect($this->router->fetch_class() . '/rechazados');
	}

	// AJAX: Buscar clientes con créditos activos (no pagados/cancelados) para select2
	public function buscar_ajax() {
		$q = $this->input->get('q');
		// Buscar solo clientes que tengan créditos activos (usando tb_solicitudes y tb_prestamos)
		$this->db->select('c.idcliente as id, CONCAT(c.apellidos, ", ", c.nombres, " (", c.numero_doc, ")") as text');
		$this->db->from('tb_clientes c');
		$this->db->join('tb_solicitudes s', 's.idcliente = c.idcliente');
		$this->db->join('tb_prestamos p', 'p.idsolicitud = s.idsolicitud');
		$this->db->where('p.estado !=', 'cancelado');
		if ($q) {
			$this->db->group_start();
			$this->db->like('c.nombres', $q);
			$this->db->or_like('c.apellidos', $q);
			$this->db->or_like('c.numero_doc', $q);
			$this->db->group_end();
		}
		$this->db->group_by('c.idcliente');
		$this->db->limit(20);
		$res = $this->db->get()->result_array();
		header('Content-Type: application/json');
		echo json_encode($res);
	}
}
