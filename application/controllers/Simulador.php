<?php
defined('BASEPATH') or exit('Acción no permitida');
class Simulador extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('login');
		}
		$this->load->model('simulacion_model');
	}
	public function index()
	{
		$data = array(
			'titulo' => 'Gestión de Simulador de Créditos',
			'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
			'icono' => 'fas fa-comments-dollar',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/activaDatatable.js'
			),
			'simulaciones' => $this->simulacion_model->getAll()
		);

		$this->load->view('layout/header', $data);
		$this->load->view('simulador/index');
		$this->load->view('layout/footer');
	}
	public function core($simulacion_id = NULL)
	{
		// if (!$this->ion_auth->is_admin()) {
		// 	$this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
		// 	redirect($this->router->fetch_class());
		// }
		if (!$simulacion_id) {
			#Registrar
			$this->form_validation->set_rules('idcliente', 'Cliente', 'trim|required|greater_than[0]');
			$this->form_validation->set_rules('idasesor', 'Asesor', 'trim|required|greater_than[0]');
			$this->form_validation->set_rules('fecha_credito', 'Fecha Crédito', 'trim|required');
			$this->form_validation->set_rules('monto_credito', 'Monto Crédito', 'trim|required');
			$this->form_validation->set_rules('interes_credito', 'Interes Crédito', 'trim|required');
			$this->form_validation->set_rules('monto_capital', 'Monto Capital', 'trim|required');
			$this->form_validation->set_rules('monto_interes', 'Monto Interes', 'trim|required');
			$this->form_validation->set_rules('monto_cuota', 'Monto Couta', 'trim|required');
			$this->form_validation->set_rules('total_pagar', 'Total Pagar', 'trim|required');
			$this->form_validation->set_rules('forma_pago', 'Forma de Pago', 'trim|required');
			if ($this->form_validation->run()) {
				$data = elements(
					array(
						'idcliente',
						'idasesor',
						'fecha_credito',
						'fecha_simulacion',
						'monto_credito',
						'interes_credito',
						'numero_cuotas',
						'monto_capital',
						'monto_interes',
						'monto_cuota',
						'total_interes',
						'total_pagar',
						'forma_pago',
						'idusuario'
					),
					$this->input->post()
				);
				$date = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_credito'));
				$fechaCredito = $date->format('Y-m-d');
				$data["fecha_credito"] = $fechaCredito;
				$data["fecha_simulacion"] = date('Y-m-d H:i:s');
				$usuario_id = $this->ion_auth->get_user_id();
				$data["idusuario"] = $usuario_id;
				$data = html_escape($data);
				$this->core_model->insert('tb_simulacion', $data);
				$idsimulacion = $this->db->insert_id();

				if ($this->input->post('forma_pago') == '0') {
					$p = 'P1D';
				}
				if ($this->input->post('forma_pago') == '1') {
					$p = 'P7D';
				}
				if ($this->input->post('forma_pago') == '2') {
					$p = 'P14D';
				}
				if ($this->input->post('forma_pago') == '3') {
					$p = 'P1M';
				}
				$periodo = new DatePeriod(
					new DateTime($fechaCredito), // Donde empezamos a contar el periodo
					new DateInterval($p), // Definimos el periodo a 1 día, 1mes
					$this->input->post('numero_cuotas'), // Aplicamos el numero de repeticiones
					DatePeriod::EXCLUDE_START_DATE
				);
				$cuota = 1;
				foreach ($periodo as $date) {
					$items =
					array(
						'idsimulacion' => $idsimulacion,
						'fecha_cuota' => $date->format('Y-m-d'),
						'numero_cuota' => $cuota++,
						'monto_capital' => $this->input->post('monto_capital'),
						'monto_interes' => $this->input->post('monto_interes'),
						'monto_cuota' => $this->input->post('monto_cuota'),
					);
					$this->core_model->insert('tb_detalle_simulacion', $items);
				}
				redirect($this->router->fetch_class() . '/opcion/' . $idsimulacion);
			} else {
				#Error de validacion.
				$data = array(
					'titulo' => 'Nueva Simulación de Crédito',
					'subtitulo' => 'Ingrese los datos solicitados',
					'icono_view' => 'fas fa-comments-dollar ',
					'styles' => array(
						'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
						'plugins/select2/dist/css/select2.min.css'
					),
					'scripts' => array(
						'plugins/select2/dist/js/select2.min.js',
						'plugins/datatables.net/js/jquery.dataTables.min.js',
						'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
						'js/simulador/simulador.js'
					),
					'clientes' => $this->core_model->get_all('tb_clientes'),
					'asesores' => $this->core_model->get_all('tb_asesores')
				);
				$this->load->view('layout/header', $data);
				$this->load->view('simulador/core');
				$this->load->view('layout/footer');
			}
		} else {
			#Editar
			if (!$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))) {
				$this->session->set_flashdata('error', 'Registro no encontrado.');
				redirect($this->router->fetch_class());
			}
			if (!$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id, 'estado' => 1))) {
				$this->session->set_flashdata('error', 'Esta Simulación ya fué utilizada.');
				redirect($this->router->fetch_class());
			} else {
				// echo '<pre>';
				// print_r($this->input->post());
				// exit();
				$this->form_validation->set_rules('idcliente', 'Cliente', 'trim|required|greater_than[0]');
				$this->form_validation->set_rules('idasesor', 'Asesor', 'trim|required|greater_than[0]');
				$this->form_validation->set_rules('fecha_credito', 'Fecha Crédito', 'trim|required');
				$this->form_validation->set_rules('monto_credito', 'Monto Crédito', 'trim|required');
				$this->form_validation->set_rules('interes_credito', 'Interes Crédito', 'trim|required');
				$this->form_validation->set_rules('monto_capital', 'Monto Capital', 'trim|required');
				$this->form_validation->set_rules('monto_interes', 'Monto Interes', 'trim|required');
				$this->form_validation->set_rules('monto_cuota', 'Monto Couta', 'trim|required');
				$this->form_validation->set_rules('total_pagar', 'Total Pagar', 'trim|required');
				$this->form_validation->set_rules('forma_pago', 'Forma de Pago', 'trim|required');

				if ($this->form_validation->run()) {
					// echo '<pre>';
					// print_r($this->input->post());
					// exit();
					$data = elements(
						array(
							'idcliente',
							'idasesor',
							'fecha_credito',
							'monto_credito',
							'interes_credito',
							'numero_cuotas',
							'monto_capital',
							'monto_interes',
							'monto_cuota',
							'total_interes',
							'total_pagar',
							'forma_pago',
							'idusuario'
						),
						$this->input->post()
					);
					$date = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_credito'));
					$fechaCredito = $date->format('Y-m-d');
					$data["fecha_credito"] = $fechaCredito;
					$usuario_id = $this->ion_auth->get_user_id();
					$data["idusuario"] = $usuario_id;
					$data = html_escape($data);
					$this->core_model->update('tb_simulacion', $data, array('idsimulacion' => $simulacion_id));
					$this->core_model->delete('tb_detalle_simulacion', array('idsimulacion' => $simulacion_id));

					if ($this->input->post('forma_pago') == '0') {
						$p = 'P1D';
					}
					if ($this->input->post('forma_pago') == '1') {
						$p = 'P7D';
					}
					if ($this->input->post('forma_pago') == '2') {
						$p = 'P14D';
					}
					if ($this->input->post('forma_pago') == '3') {
						$p = 'P1M';
					}
					$periodo = new DatePeriod(
						new DateTime($fechaCredito), // Donde empezamos a contar el periodo
						new DateInterval($p), // Definimos el periodo a 1 día, 1mes
						$this->input->post('numero_cuotas'), // Aplicamos el numero de repeticiones
						DatePeriod::EXCLUDE_START_DATE
					);
					$cuota = 1;
					foreach ($periodo as $date) {
						$items =
						array(
							'idsimulacion' => $usuario_id,
							'fecha_cuota' => $date->format('Y-m-d'),
							'numero_cuota' => $cuota++,
							'monto_capital' => $this->input->post('monto_capital'),
							'monto_interes' => $this->input->post('monto_interes'),
							'monto_cuota' => $this->input->post('monto_cuota'),
						);
						$this->core_model->insert('tb_detalle_simulacion', $items);
					}
					redirect($this->router->fetch_class() . '/opcion/' . $simulacion_id);
				} else {
					#Error de validacion.
					$data = array(
						'titulo' => 'Modificar Simulación de Crédito',
						'subtitulo' => 'Ingrese los datos solicitados',
						'icono_view' => 'fas fa-comments-dollar ',
						'styles' => array(
							'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'plugins/select2/dist/css/select2.min.css'
						),
						'scripts' => array(
							'plugins/select2/dist/js/select2.min.js',
							'plugins/datatables.net/js/jquery.dataTables.min.js',
							'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'js/simulador/simulador.js'
						),
						'simulacion' => $this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id)),
						'clientes' => $this->core_model->get_all('tb_clientes'),
						'asesores' => $this->core_model->get_all('tb_asesores')
					);
					$this->load->view('layout/header', $data);
					$this->load->view('simulador/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}
	public function detalleSimulador()
	{
		$date = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_credito'));
		$fechaCredito = $date->format('Y-m-d');
		if ($this->input->post('forma_pago') == '0') {
			$p = 'P1D';
		}
		if ($this->input->post('forma_pago') == '1') {
			$p = 'P7D';
		}
		if ($this->input->post('forma_pago') == '2') {
			$p = 'P14D';
		}
		if ($this->input->post('forma_pago') == '3') {
			$p = 'P1M';
		}
		$periodo = new DatePeriod(
			new DateTime($fechaCredito), // Donde empezamos a contar el periodo
			new DateInterval($p), // Definimos el periodo a 1 día, 1mes
			$this->input->post('numero_cuotas'), // Aplicamos el numero de repeticiones
			DatePeriod::EXCLUDE_START_DATE
		);
		$cuota = 1;
		$data = [];
		foreach ($periodo as $date) {
			$items =
			array(
				'fecha_cuota' => $date->format('Y-m-d'),
				'numero_cuotas' => $cuota++,
				'monto_capital' => $this->input->post('monto_capital'),
				'monto_interes' => $this->input->post('monto_interes'),
				'monto_cuota' => $this->input->post('monto_cuota'),
			);
			$data[] = $items;
		}
		$retorna = [
			'data' => $data
		];
		echo json_encode($retorna);
	}
	public function opcion($simulacion_id = NULL)
	{
		if (!$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		} else {
			$data = array(
				'titulo' => '¿Que te gustaría hacer?',
				'subtitulo' => 'Por favor elija una de las opciones a seguir.',
				'icono_view' => 'ik ik-user ',
				'simulacion' => $this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))
			);
			$this->load->view('layout/header', $data);
			$this->load->view('simulador/opcion');
			$this->load->view('layout/footer');
		}
	}
	public function pdf($simulacion_id = NULL){
		if (!$simulacion_id || !$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}
		$this->load->library('pdf');
		$empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
		$cuotas = $this->simulacion_model->getAllById($simulacion_id);
		$simulacion = $this->simulacion_model->getById($simulacion_id);
		$file_name = 'SIMULACIÓN N° ' . $simulacion->idsimulacion;
		$data = array(
			'file_name'=>$file_name,
			'empresa'=>$empresa,
			'simulacion' => $simulacion,
			'cuotas'=>$cuotas,
			'titulo' => $file_name
		);
		$html = $this->load->view('simulador/pdf', $data, TRUE);
		$this->pdf->createPDF($html, $file_name, false, 'A4', 'portrait');
	}
	public function pdf1($simulacion_id = NULL)
	{
		if (!$simulacion_id || !$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		} else {
			$this->load->library('pdf');
			$empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
			$cuotas = $this->simulacion_model->getAllById($simulacion_id);
			$simulacion = $this->simulacion_model->getById($simulacion_id);
			// echo '<pre>';
			// print_r($cuotas);
			// exit();
			$file_name = 'SIMULACIÓN N° ' . $simulacion->idsimulacion;
			$html = '<html style="font-size:12px>"';
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
			$html .= '<hr>';
			$datos_salida = '';
			$forma_pago = '';
			if ($simulacion->forma_pago == 0) {
				$forma_pago = 'DIARIO';
			} elseif ($simulacion->forma_pago == 1) {
				$forma_pago = 'SEMANAL';
			} elseif ($simulacion->forma_pago == 2) {
				$forma_pago = 'QUINCENAL';
			} elseif ($simulacion->forma_pago == 3) {
				$forma_pago = 'MENSUAL';
			}
			if ($simulacion->estado == 1) {
				$datos_salida .= '<strong>Fecha Salida</strong>' . formatoFechaHora($simulacion->fecha_simulacion) . '<br>';
			}
			$html .= '<table class="table table-sm table-bordered">
			<tbody>
			<tr>
			<td>SIMULACIÓN N°</td><td>' . $simulacion->idsimulacion . '</td><td>FECHA SIMULACIÓN</td><td>' . formatoFechaHora($simulacion->fecha_simulacion) . '</td>
			</tr>
			<tr>
			<td>CLIENTE: </td><td colspan="3">' . $simulacion->idcliente . ' - ' . $simulacion->apellidos . ', ' . $simulacion->nombres . '</td>
			</tr>
			<tr>
			<td>ASESOR: </td><td colspan="3">' . $simulacion->nombre_asesor . '</td>
			</tr>
			<tr>
			<td>MONTO CRÉDITO: </td><td>' . number_format($simulacion->monto_credito, 2) . '</td><td>INTERES</td><td>' . number_format($simulacion->total_interes, 2) . '</td>
			</tr>
			<tr>
			<td>FORMA DE PAGO: </td><td colspan="3">' . $forma_pago . '</td>
			</tr>
			</tbody>
			</table>';
			$html .= '<hr>';
			$html .= '<table class="table table-sm table-bordered">
			<thead>
			<tr>
			<th>N° Cuota</th>
			<th>Fecha Cuota</th>
			<th>Capital</th>
			<th>Interes</th>
			<th>Monto Cuota</th>
			</tr>
			</thead>
			<tbody>';
			foreach ($cuotas as $cuota) {
				$numero_cuota = $cuota->numero_cuota;
				$fecha_cuota = $cuota->fecha_cuota;
				$monto_capital = $cuota->monto_capital;
				$monto_interes = $cuota->monto_interes;
				$monto_cuota = $cuota->monto_cuota;

				$html .= '<tr>
				<td>' . $numero_cuota . '</td>
				<td>' . $fecha_cuota . '</td>
				<td>' . $monto_capital . '</td>
				<td>' . $monto_interes . '</td>
				<td>' . $monto_cuota . '</td>
				</tr>';
			}

			$html .= '
			</tbody>
			</table>';
			$html .= '<br><h5 align="center">
			' . $empresa->razon_social . '<br>
			' . $empresa->mensaje_ticket . '<br>
			' . date('d/m/Y H:i:s') . '<br>
			</h5>';
			// echo '<pre>';
			// print_r($html);
			// exit();
			$this->pdf->createPDF($html, $file_name, false, 'A4');
			$html .= '<hr>';
			$html .= '</body>';
			$html .= '</html>';
		}
	}
	public function del($simulacion_id = NULL)
	{
		if (!$this->ion_auth->is_admin()) {
			$this->session->set_flashdata('info', 'No tienes permiso para eliminar prestamos.');
			redirect('/');
		}
		if (!$simulacion_id || !$this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id))) {
			$this->session->set_flashdata('error', 'Registro no encontrado');
			redirect($this->router->fetch_class());
		}
		if ($this->core_model->get_by_id('tb_simulacion', array('idsimulacion' => $simulacion_id, 'estado' => 2))) {
			$this->session->set_flashdata('error', 'El crédito no puese ser eliminado está en proceso de pago.');
			redirect($this->router->fetch_class());
		}
		$this->core_model->delete('tb_detalle_simulacion', array('idsimulacion' => $simulacion_id));
		$this->core_model->delete('tb_simulacion', array('idsimulacion' => $simulacion_id));
		redirect($this->router->fetch_class());
	}
}
