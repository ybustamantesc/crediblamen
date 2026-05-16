<?php
defined('BASEPATH') or exit('Acción no permitida');
class Formas extends CI_Controller
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
            'titulo' => 'Gestión de Formas de Pago',
            'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
            'icono' => 'ik ik-credit-card',
            'formas' => $this->core_model->get_all('formapago')
        );
        //echo '<pre>';
        //print_r($data['precios']);
        //exit();
        $this->load->view('layout/header', $data);
        $this->load->view('formas/index');
        $this->load->view('layout/footer');
    }
    public function core($forma_id = NULL)
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
            redirect($this->router->fetch_class());
        }
        if (!$forma_id) {
            #Registrar 
            $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|min_length[5]|max_length[30]|is_unique[formapago.nombre]');
            if ($this->form_validation->run()) {
                $data = elements(
                    array(
                        'nombre',
                        'estado'
                    ),
                    $this->input->post()
                );
                $data = html_escape($data);
                $this->core_model->insert('formapago', $data);
                redirect($this->router->fetch_class());
            } else {
                #Error de validacion.
                $data = array(
                    'titulo' => 'Registrar Formas de Pago',
                    'subtitulo' => 'Registrar Formas de Pago',
                    'icono_view' => 'ik ik-credit-card ',
                    'scripts' => array(
                        'plugins/mask/jquery.mask.min.js',
                        'plugins/mask/custom.js',
                    ),
                );
                $this->load->view('layout/header', $data);
                $this->load->view('formas/core');
                $this->load->view('layout/footer');
            }
        } else {
            #Editar Usuario
            if (!$this->core_model->get_by_id('formapago', array('id' => $forma_id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado.');
                redirect($this->router->fetch_class());
            } else {

                $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|min_length[5]|max_length[30]|callback_check_forma');
                if ($this->form_validation->run()) {

                    $data = elements(
                        array(
                            'nombre',
                            'estado'
                        ),
                        $this->input->post()
                    );
                    $data = html_escape($data);
                    $this->core_model->update('formapago', $data, array('id' => $forma_id));
                    redirect($this->router->fetch_class());
                } else {
                    #Error de validacion.
                    $data = array(
                        'titulo' => 'Modificar Forma de Pago',
                        'subtitulo' => 'Mofidicar Información de Forma de Pago',
                        'icono_view' => 'ik ik-credit-card ',
                        'scripts' => array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/mask/custom.js',

                        ),
                        'forma' => $this->core_model->get_by_id('formapago', array('id' => $forma_id))
                    );

                    $this->load->view('layout/header', $data);
                    $this->load->view('formas/core');
                    $this->load->view('layout/footer');
                }
            }
        }
    }

    public function check_forma($nombre)
    {
        $id = $this->input->post('id');
        if ($this->core_model->get_by_id('formapago', array('nombre' => $nombre, 'id!=' => $id))) {
            $this->form_validation->set_message('check_forma', 'Esta Forma de Pago ya existe');
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
        if (!$id || !$this->core_model->get_by_id('formapago', array('id' => $id))) {
            $this->session->set_flashdata('error', 'Precio no encontrado');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('formapago', array('id' => $id, 'estado' => 1))) {
            $this->session->set_flashdata('error', 'Forma de Pago con Estado Activo no puede se eliminado.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('formapago', array('id' => $id));
        redirect($this->router->fetch_class());
    }
}
