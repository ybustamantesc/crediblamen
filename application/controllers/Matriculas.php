<?php
defined('BASEPATH') or exit('Acción no permitida');
class Matriculas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('matriculas_model');
    }
    public function index()
    {
        $data = array(
            'titulo' => 'Gestión de Matriculas',
            'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
            'icono' => 'fas fa-dollar-sign ',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/estacionamiento.js'
            ),
            'matriculas' => $this->matriculas_model->get_all()
        );
        // echo '<pre>';
        // print_r($data["matriculas"]);
        // exit();

        $this->load->view('layout/header', $data);
        $this->load->view('matriculas/index');
        $this->load->view('layout/footer');
    }
    public function core($matricula_id = NULL)
    {
        if (!$matricula_id) {
            #Registrar
            $this->form_validation->set_rules('clienteid', 'Cliente', 'required');
            $this->form_validation->set_rules('precioid', 'Categoría', 'required');
            $this->form_validation->set_rules('fechaVencimiento', 'Fecha de Vencimiento', 'required', 'callback_check_existe_matricula|callback_fecha_valida|callback_check_dia_vencimiento');
            if ($this->form_validation->run()) {
                // echo '<pre>';
                // print_r($this->input->post());
                // exit();
                $data = elements(
                    array(
                        'clienteid',
                        'precioid',
                        'valor',
                        'diasVencimiento',
                        'fechaVencimiento',
                        'estado'
                    ),
                    $this->input->post()
                );
                $data = html_escape($data);
                $data["clienteid"] = $this->input->post('matricula_cliente_hidden_id');
                $data["precioid"] = $this->input->post('matricula_precio_hidden_id');
                $this->core_model->insert('matriculas', $data);
                redirect($this->router->fetch_class());
            } else {
                #ERROR DE VALIDACION AL INSERTAR
                $data = array(
                    'titulo' => 'Registrar Matricula',
                    'subtitulo' => 'Registrar Matricula.',
                    'icono_view' => 'ik ik-user ',
                    'texto_modal' => '¿Sus datos son correctos?<br></br>Despues de guardar sólo será posible cambiar la Categoría y es Estado del vehículo.',
                    'styles' => array(
                        'plugins/select2/dist/css/select2.min.css'
                    ),
                    'scripts' => array(
                        'plugins/mask/jquery.mask.min.js',
                        'plugins/mask/custom.js',
                        'plugins/select2/dist/js/select2.min.js',
                        'js/matriculas/matricula.js'
                    ),
                    'precios' => $this->core_model->get_all('precios', array('precio_estado' => 1)),
                    'clientes' => $this->core_model->get_all('clientes', array('estado' => 1))
                );

                $this->load->view('layout/header', $data);
                $this->load->view('matriculas/core');
                $this->load->view('layout/footer');
            }
        } else {
            #Editar
            if (!$this->core_model->get_by_id('matriculas', array('matriculaid' => $matricula_id))) {
                $this->session->set_flashdata('error', 'Matrícula no encontrado');
                redirect($this->router->fetch_class());
            } else {
                $this->form_validation->set_rules('precioid', 'Categoría', 'required');
                if ($this->form_validation->run()) {
                    $cliente_estado = $this->input->post('estado');
                    if ($cliente_estado == 0) {
                        if ($this->db->table_exists('mensalidades')) {
                            if ($this->core_model->get_by_id('mensalidades', array('mensalidade_id' => $matricula_id, 'status' => 0))) {
                                $this->session->set_flashdata('error', 'Este Cliente con pagos pendientes no puede ser desactivado.');
                                redirect($this->router->fetch_class());
                            }
                        }
                    }
                    $data = elements(
                        array(
                            'precioid',
                            'valor',
                            'diasVencimiento',
                            'estado'
                        ),
                        $this->input->post()
                    );
                    $data["clienteid"] = $this->input->post('matricula_cliente_hidden_id');
                    $data["precioid"] = $this->input->post('matricula_precio_hidden_id');
                    if ($data["estado"] == 1) {
                        $data["fechaPago"] = date('Y-m-d H:i:s');
                    }
                    $data = html_escape($data);
                    $this->core_model->update('matriculas', $data, array('matriculaid' => $matricula_id));
                    redirect($this->router->fetch_class());
                } else {
                    //Error de validacion
                    $data = array(
                        'titulo' => 'Editar Matricula',
                        'subtitulo' => 'Editando Matricula.',
                        'icono_view' => 'ik ik-user ',
                        'texto_modal' => '¿Sus datos son correctos?<br></br>Despues de guardar sólo será posible cambiar la Categoría y es Estado del vehículo.',
                        'styles' => array(
                            'plugins/select2/dist/css/select2.min.css'
                        ),
                        'scripts' => array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/mask/custom.js',
                            'plugins/select2/dist/js/select2.min.js',
                            'js/matriculas/matricula.js'
                        ),
                        'precios' => $this->core_model->get_all('precios', array('precio_estado' => 1)),
                        'clientes' => $this->core_model->get_all('clientes', array('estado' => 1)),
                        'matricula' => $this->core_model->get_by_id('matriculas', array('matriculaid' => $matricula_id))
                    );
                    $this->load->view('layout/header', $data);
                    $this->load->view('matriculas/core');
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
    public function del($matricula_id = NULL)
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para eliminar matriculas.');
            redirect('/');
        }
        if (!$matricula_id || !$this->core_model->get_by_id('matriculas', array('matriculaid' => $matricula_id))) {
            $this->session->set_flashdata('error', 'Registro no encontrado');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('matriculas', array('matriculaid' => $matricula_id, 'estado' => 0))) {
            $this->session->set_flashdata('error', 'Matricula con Estado Activo no puede se eliminado.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('matriculas', array('matriculaid' => $matricula_id));
        redirect($this->router->fetch_class());
    }
}
