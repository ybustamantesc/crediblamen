<?php
defined('BASEPATH') or exit('Acción no permitida');
class Monedas extends CI_Controller
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
            'titulo' => 'Gestión de Monedas',
            'subtitulo' => 'Registrar, Modificar, Eliminar, Buscar.',
            'icono' => 'fas fa-hand-holding-usd',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'monedas' => $this->core_model->get_all('tb_monedas')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('monedas/index');
        $this->load->view('layout/footer');
    }
    public function core($moneda_id = NULL)
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
            redirect($this->router->fetch_class());
        }
        if (!$moneda_id) {
            #Registrar 
            $this->form_validation->set_rules('nombre', 'Nombre Moneda', 'trim|required|min_length[3]|max_length[20]|is_unique[tb_monedas.nombre]');
            $this->form_validation->set_rules('simbolo', 'Símbolo Moneda', 'trim|required|min_length[3]|max_length[6]');
            if ($this->form_validation->run()) {
                $data = elements(
                    array(
                        'nombre',
                        'simbolo',
                        'estado'
                    ),
                    $this->input->post()
                );
                $data = html_escape($data);
                $this->core_model->insert('tb_monedas', $data);
                redirect($this->router->fetch_class());
            } else {
                #Error de validacion.
                $data = array(
                    'titulo' => 'Registrar Nuevo Moneda',
                    'subtitulo' => 'Ingrese los datos solicitados',
                    'icono_view' => 'fas fa-hand-holding-usd '
                );
                $this->load->view('layout/header', $data);
                $this->load->view('monedas/core');
                $this->load->view('layout/footer');
            }
        } else {
            #Editar 
            if (!$this->core_model->get_by_id('tb_monedas', array('id' => $moneda_id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado.');
                redirect($this->router->fetch_class());
            } else {
                $this->form_validation->set_rules('nombre', 'Nombre Moneda', 'trim|required|min_length[3]|max_length[20]|callback_check_moneda');
                if ($this->form_validation->run()) {
                    $data = elements(
                        array(
                            'nombre',
                            'simbolo',
                            'estado'
                        ),
                        $this->input->post()
                    );
                    $data = html_escape($data);
                    $this->core_model->update('tb_monedas', $data, array('id' => $moneda_id));
                    redirect($this->router->fetch_class());
                } else {
                    #Error de validacion.
                    $data = array(
                        'titulo' => 'Modificar Moneda',
                        'subtitulo' => 'Ingrese los datos solicitados',
                        'icono_view' => 'fas fa-hand-holding-usd ',
                        'moneda' => $this->core_model->get_by_id('tb_monedas', array('id' => $moneda_id))
                    );
                    $this->load->view('layout/header', $data);
                    $this->load->view('monedas/core');
                    $this->load->view('layout/footer');
                }
            }
        }
    }

    public function check_moneda($nombre)
    {
        $id = $this->input->post('id');
        if ($this->core_model->get_by_id('tb_monedas', array('nombre' => $nombre, 'id!=' => $id))) {
            $this->form_validation->set_message('check_banco', 'Este Nombre de Moneda ya existe');
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
        if (!$id || !$this->core_model->get_by_id('tb_monedas', array('id' => $id))) {
            $this->session->set_flashdata('error', 'Moneda no encontrada.');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('tb_monedas', array('id' => $id, 'estado' => 1))) {
            $this->session->set_flashdata('error', 'Esta Moneda tiene como Estado Activo no puede se eliminada.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('tb_monedas', array('id' => $id));
        redirect($this->router->fetch_class());
    }
}
