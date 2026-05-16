<?php
defined('BASEPATH') or exit('Acción no permitida');

class Feriados extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Core_model', 'core_model');
        $this->load->library('ion_auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Feriados',
            'subtitulo' => 'Administrar días feriados / no laborables',
            'icono' => 'fas fa-calendar-alt',
            'feriados' => $this->core_model->get_all('tb_feriados')
        );

        $this->load->view('layout/header', $data);
        $this->load->view('feriados/index', $data);
        $this->load->view('layout/footer');
    }

    public function get($id = null)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$id) { echo json_encode(array('status' => false, 'message' => 'Falta id')); return; }
        $row = $this->core_model->get_by_id('tb_feriados', array('id' => $id));
        if (!$row) { echo json_encode(array('status' => false, 'message' => 'No encontrado')); return; }
        echo json_encode(array('status' => true, 'feriado' => $row));
    }

    public function add_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $fecha = $this->input->post('fecha');
        $motivo = $this->input->post('motivo');
        if (!$fecha) { echo json_encode(array('status' => false, 'message' => 'Fecha requerida')); return; }
        $row = array('fecha' => $fecha, 'motivo' => $motivo, 'activo' => 1);
        $this->core_model->insert('tb_feriados', $row, TRUE);
        $id = $this->session->userdata('last_id');
        echo json_encode(array('status' => true, 'id' => $id));
    }

    public function edit_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = intval($this->input->post('id'));
        $fecha = $this->input->post('fecha');
        $motivo = $this->input->post('motivo');
        if (!$id || !$fecha) { echo json_encode(array('status' => false, 'message' => 'Parámetros inválidos')); return; }
        $this->core_model->update('tb_feriados', array('fecha' => $fecha, 'motivo' => $motivo), array('id' => $id));
        echo json_encode(array('status' => true));
    }

    public function del_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = intval($this->input->post('id'));
        if (!$id) { echo json_encode(array('status' => false, 'message' => 'Falta id')); return; }
        $this->core_model->delete('tb_feriados', array('id' => $id));
        echo json_encode(array('status' => true));
    }

    // Return list of active feriados (for client-side validation)
    public function list_ajax()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->core_model->get_all('tb_feriados', array('activo' => 1));
        if (!is_array($rows)) $rows = array();
        $out = array();
        foreach ($rows as $r) {
            if (isset($r->fecha) && $r->fecha) $out[] = $r->fecha;
        }
        echo json_encode(array('status' => true, 'feriados' => $out));
    }
}
