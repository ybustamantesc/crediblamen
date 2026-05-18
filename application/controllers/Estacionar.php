<?php
defined('BASEPATH') or exit('Acción no permitida');
class Estacionar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('estacionar_model');
    }
    public function index()
    {
        $data = array(
            'titulo' => 'Tickets de Estacionamiento Registrados',
            'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
            'icono' => 'fas fa-dollar-sign ',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
                'dist/css/estacionar.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/estacionamiento.js'
            ),
            'estacionados' => $this->estacionar_model->get_all(),
            'numero_vacantes_pequeno' => $this->estacionar_model->get_numero_vacantes('1'),
            'vacantes_ocupadas_pequeno' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 1)),
            'numero_vacantes_medio' => $this->estacionar_model->get_numero_vacantes('2'),
            'vacantes_ocupadas_medio' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 2)),
            'numero_vacantes_grande' => $this->estacionar_model->get_numero_vacantes('3'),
            'vacantes_ocupadas_grande' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 3)),
            'numero_vacantes_moto' => $this->estacionar_model->get_numero_vacantes('4'),
            'vacantes_ocupadas_moto' => $this->core_model->get_all('estacionar', array('estacionar_estado' => 0, 'estacionar_precio_id' => 4)),
        );
        // echo '<pre>';
        // print_r($data["matriculas"]);
        // exit();

        $this->load->view('layout/header', $data);
        $this->load->view('estacionar/index');
        $this->load->view('layout/footer');
    }
    public function core($estacionar_id = NULL)
    {
        if (!$estacionar_id) {
            #Registrar
            $this->form_validation->set_rules('estacionar_precio_id', 'Categoría', 'required');
            $this->form_validation->set_rules('estacionar_numero_vacante', 'Vacante', 'required|integer|greater_than[0]|callback_check_vacante_ocupada|callback_check_rango_vacantes_categoria');
            $this->form_validation->set_rules('estacionar_placa_vehiculo', 'Placa Vehículo', 'required|exact_length[8]|callback_check_placa_status_abierta');
            $this->form_validation->set_rules('estacionar_marca_vehiculo', 'Marca Vehículo', 'required|min_length[2]|max_length[30]');
            $this->form_validation->set_rules('estacionar_modelo_vehiculo', 'Modelo Vehículo', 'required|min_length[2]|max_length[20]');
            if ($this->form_validation->run()) {
                // echo '<pre>';
                // print_r($this->input->post());
                // exit();
                $data = elements(
                    array(
                        'estacionar_valor_hora',
                        'estacionar_numero_vacante',
                        'estacionar_placa_vehiculo',
                        'estacionar_marca_vehiculo',
                        'estacionar_modelo_vehiculo'
                    ),
                    $this->input->post()
                );
                $data = html_escape($data);
                $data["estacionar_precio_id"] = intval(substr($this->input->post('estacionar_precio_id'), 0, 1));
                $data["estacionar_estado"] = 0;
                $this->core_model->insert('estacionar', $data, TRUE);
                $estacionar_id = $this->session->userdata('last_id');
                redirect($this->router->fetch_class() . '/comportamiento/' . $estacionar_id);
            } else {
                #ERROR DE VALIDACION AL INSERTAR
                $data = array(
                    'titulo' => 'Registrar Ticket',
                    'subtitulo' => 'Registrar Ticket.',
                    'icono_view' => 'ik ik-user ',
                    'texto_modal' => '¿Desea guardar el registro?',
                    'styles' => array(
                        'plugins/select2/dist/css/select2.min.css',
                        'dist/css/estacionar.css'
                    ),
                    'scripts' => array(
                        'plugins/mask/jquery.mask.min.js',
                        'plugins/mask/custom.js',
                        'plugins/select2/dist/js/select2.min.js',
                        'js/estacionar/estacionar.js'
                    ),
                    'precios' => $this->core_model->get_all('precios', array('precio_estado' => 1)),
                    'clientes' => $this->core_model->get_all('clientes', array('estado' => 1))
                );

                $this->load->view('layout/header', $data);
                $this->load->view('estacionar/core');
                $this->load->view('layout/footer');
            }
        } else {
            #Editar
            if (!$this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado');
                redirect($this->router->fetch_class());
            } else {
                $estacionar_tiempo_transcurrido = str_replace('.', '', $this->input->post('estacionar_tiempo_transcurrido'));
                if ($estacionar_tiempo_transcurrido > '015') {
                    $this->form_validation->set_rules('estacionar_forma_pago_id', 'Forma de Pago', 'required');
                } else {
                    $this->form_validation->set_rules('estacionar_forma_pago_id', 'Forma de Pago', 'trim');
                }

                if ($this->form_validation->run()) {

                    $data = elements(
                        array(
                            'estacionar_valor_adeudado',
                            'estacionar_forma_pago_id',
                            'estacionar_tiempo_transcurrido'
                        ),
                        $this->input->post()
                    );
                    if ($estacionar_tiempo_transcurrido <= '015') {
                        $data['estacionar_forma_pago_id'] = 3;
                    }
                    $data["estacionar_fecha_salida"] = date('Y-m-d H:i:s');
                    $data["estacionar_estado"] = 1;
                    $data = html_escape($data);
                    $this->core_model->update('estacionar', $data, array('estacionar_id' => $estacionar_id));
                    redirect($this->router->fetch_class() . '/comportamiento/' . $estacionar_id);
                } else {
                    //Error de validacion
                    $data = array(
                        'titulo' => 'Editar Ticket',
                        'subtitulo' => 'Editando Matricula.',
                        'icono_view' => 'ik ik-user ',
                        'texto_modal' => '¿Desea generar este ticket?',
                        'styles' => array(
                            'plugins/select2/dist/css/select2.min.css'
                        ),
                        'scripts' => array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/select2/dist/js/select2.min.js',
                            'plugins/mask/custom.js',
                            'js/estacionar/estacionar.js'

                        ),
                        'precios' => $this->core_model->get_all('precios', array('precio_estado' => 1)),
                        'formapagos' => $this->core_model->get_all('formapago', array('estado' => 1)),
                        'estacionado' => $this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))
                    );
                    $this->load->view('layout/header', $data);
                    $this->load->view('estacionar/core');
                    $this->load->view('layout/footer');
                }
            }
        }
    }

    public function check_existe_matricula($fechaVencimiento)
    {

        /* Recupera o post */
        $clienteid = $this->input->post('matricula_cliente_hidden_id');

        /* Verifica no banco se há mensalidade já cadastrada para o mensalista e coma data passsados no post */
        $mamtricula_user = $this->core_model->get_by_id('matricula', array('clienteid' => $clienteid, 'fechaVencimiento' => $fechaVencimiento));

        if ($mamtricula_user) {

            /* Faz o explode da $mensalidade_data_vencimento do post */
            $fechaVencimento_post = explode('-', $fechaVencimiento);


            /* Faz o explode da $mensalidade_data_vencimento vinda do banco */
            $fechaVencimiento_user = explode('-', $mamtricula_user->fechaVencimiento);



            if ($fechaVencimento_post[0] == $fechaVencimiento_user[0] && $fechaVencimento_post[1] == $fechaVencimiento_user[1]) {
                $this->form_validation->set_message('check_existe_matricula', 'Ya tiene una fecha de pago');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }
    public function check_fecha_valida($fechaVencimiento)
    {

        $fechaAtual = strtotime(date('Y-m-d'));

        $fechaVencimiento = strtotime($fechaVencimiento);

        /* Se a data de vencimento for menor que a data atual */
        if ($fechaAtual > $fechaVencimiento) {
            $this->form_validation->set_message('check_fecha_valida', 'Error fecha de vencimiento.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    public function check_dia_vencimiento($diaVencimiento)
    {

        if ($diaVencimiento) {

            $diaVencimiento = explode('-', $diaVencimiento);

            $diaVencimientoCliente = $this->input->post('diasVencimiento');

            if ($diaVencimiento[2] != $diaVencimientoCliente) {
                $this->form_validation->set_message('check_dia_vencimiento', 'Este campo debe contener el mismo dia que el "Dia vencimiento"');
                return FALSE;
            } else {
                return true;
            }
        } else {
            $this->form_validation->set_message('check_data_com_dia_vencimento', 'Campo obrigatório');
            return FALSE;
        }
    }

    public function check_vacante_ocupada($estacionar_numero_vacante)
    {
        $estacionar_precio_id = intval(substr($this->input->post('estacionar_precio_id'), 0, 1));
        if ($this->core_model->get_by_id('estacionar', array('estacionar_numero_vacante' => $estacionar_numero_vacante, 'estacionar_estado' => 0, 'estacionar_precio_id' => $estacionar_precio_id))) {
            $this->form_validation->set_message('check_vacante_ocupada', 'Esta vacante ya está ocupada para esta categoría.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_rango_vacantes_categoria($numero_vacante)
    {
        $estacionar_precio_id = intval(substr($this->input->post('estacionar_precio_id'), 0, 1));
        if ($estacionar_precio_id) {
            $precios = $this->core_model->get_by_id('precios', array('precio_id' => $estacionar_precio_id));
            if ($precios->precio_numero_vacantes < $numero_vacante) {
                $this->form_validation->set_message('check_rango_vacantes_categoria', 'A vaga deve estar entre 1 e ' . $precios->precio_numero_vacantes);
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $this->form_validation->set_message('check_rango_vacantes_categoria', 'Seleccione una Categoría');
            return FALSE;
        }
    }
    public function check_placa_status_abierta($estacionar_placa_vehiculo)
    {
        $estacionar_placa_vehiculo = strtoupper($estacionar_placa_vehiculo);
        if ($this->core_model->get_by_id('estacionar', array('estacionar_placa_vehiculo' => $estacionar_placa_vehiculo, 'estacionar_estado' => 0))) {
            $this->form_validation->set_message('check_placa_status_abierta', 'Existe un ticket abierto para este placa');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    public function comportamiento($estacionar_id = NULL)
    {
        if (!$this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        } else {
            $data = array(
                'titulo' => '¿Que te gustaría hacer?',
                'subtitulo' => 'Por favor elija una de las opciones a seguir.',
                'icono_view' => 'ik ik-user ',
                'styles' => array(
                    'plugins/select2/dist/css/select2.min.css'
                ),
                'estacionado' => $this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))
            );
            $this->load->view('layout/header', $data);
            $this->load->view('estacionar/comportamiento');
            $this->load->view('layout/footer');
        }
    }
    public function pdf($estacionar_id = NULL)
    {
        if (!$estacionar_id || !$this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        } else {
            $this->load->library('pdf');
            $this->load->model('estacionar_model');
            $empresa = $this->core_model->get_by_id('sistema', array('sistema_id' => 1));
            $ticket = $this->estacionar_model->get_by_id($estacionar_id);
            $file_name = 'Ticket - Placa_' . $ticket->estacionar_placa_vehiculo;
            $html = '<html style="font-size:12px">';
            $html .= '<head>';
            $html .= '<title>' . $empresa->sistema_razon_social . '</title>';
            $html .= '</head>';
            $html .= '<body>';
            $html .= '<h5 align="center">
            ' . $empresa->sistema_razon_social . '<br>
            DIRECCIÓN: ' . $empresa->sistema_direccion . '<br>
            TELÉFONO: ' . $empresa->sistema_telefono . '<br>
            WEB: ' . $empresa->sistema_web . '<br>
            CORREO: ' . $empresa->sistema_email . '<br>
            </h5>';
            $html .= '<hr>';
            $datos_salida = '';
            if ($ticket->estacionar_estado == 1) {
                $datos_salida .= '<strong>Fecha Salida</strong>' . formatoFechaHora($ticket->estacionar_fecha_salida) . '<br>'
                    . ' <strong>Tiempo Transcurrido(hh:mm).</strong>' . $ticket->estacionar_tiempo_transcurrido . '<br>'
                    . ' <strong>Valor Pago.</strong>' . $ticket->estacionar_valor_adeudado . '<br>'
                    . ' <strong>Forma de Pago.</strong>' . $ticket->nombre . '<br>';
            }
            $html .= '<p aligne="right">TICKER N° ' . $ticket->estacionar_id . '</p><br>';
            $html .= '<p>
                        <strong>Placa Vehiculo.</strong>' . $ticket->estacionar_placa_vehiculo . '<br>            
                        <strong>Modelo Vehiculo.</strong>' . $ticket->estacionar_modelo_vehiculo . '<br>
                        <strong>Categoría Vehiculo.</strong>' . $ticket->precio_categoria . '<br>
                        <strong>Número Vacante.</strong>' . $ticket->estacionar_numero_vacante . '<br>
                        <strong>Fecha de Entrada.</strong>' . formatoFechaHora($ticket->estacionar_fecha_entrada) . '<br>'
                . $datos_salida .
                '</p>';
            $html .= '<br>';
            $html .= '<hr>';
            $html .= '<h5 align="center">
            ' . $empresa->sistema_razon_social . '<br>
            ' . $empresa->sistema_mensaje_ticket . '<br>
             ' . date('d/m/Y H:i:s') . '<br>
            </h5>';
            // echo '<pre>';
            // print_r($html);
            // exit();
            $this->pdf->createPDF($html, $file_name, false);
            $html .= '<hr>';
            $html .= '</body>';
            $html .= '</html>';
        }
    }
    public function del($estacionar_id = NULL)
    {
        if (!$estacionar_id || !$this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id, 'estacionar_estado' => 0))) {
            $this->session->set_flashdata('error', 'Este Registro no puede ser eliminado, está en estado pendiente.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('estacionar', array('estacionar_id' => $estacionar_id));
        redirect($this->router->fetch_class());
    }
}
