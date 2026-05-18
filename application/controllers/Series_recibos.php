<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Series_recibos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('core_model');
        $this->load->library('ion_auth');
        $this->load->helper(['url', 'form']);
    }

    // Mostrar la vista principal
    public function index()
    {
        // permiso: requiere usuario logueado
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
            return;
        }

        $data['series'] = $this->core_model->get_all('tb_series_recibos') ?: [];
        // Page metadata used by the view (icon, title, subtitle)
        $data['icono'] = 'fas fa-receipt';
        $data['titulo'] = 'Series de Recibos';
        $data['subtitulo'] = 'Control de series y consecutivos de recibos de abonos';

        // Load header (includes css/js), then the view and footer to match other controllers
        $data['styles'] = array(
            'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
        );
        $data['scripts'] = array(
            'plugins/datatables.net/js/jquery.dataTables.min.js',
            'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
            'plugins/datatables.net/js/activaDatatable.js'
        );

        $this->load->view('layout/header', $data);
        $this->load->view('series_recibos/index', $data);
        $this->load->view('layout/footer');
    }

    // Devuelve JSON con todas las series
    public function list()
    {
        if (!$this->ion_auth->logged_in()) {
            http_response_code(403);
            echo json_encode([]);
            return;
        }
        $rows = $this->core_model->get_all('tb_series_recibos') ?: [];
        header('Content-Type: application/json');
        echo json_encode(array_values($rows));
    }

    // Devuelve JSON de una serie
    public function get($id = null)
    {
        if (!$this->ion_auth->logged_in()) {
            http_response_code(403);
            echo json_encode(null);
            return;
        }
        $id = (int)$id;
        $row = $this->core_model->get_by_id('tb_series_recibos', ['idserie' => $id]);
        header('Content-Type: application/json');
        echo json_encode($row);
    }

    // Guardar (insert / update) vía POST
    public function save()
    {
        if (!$this->ion_auth->logged_in()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo', 'Código', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('consecutivo', 'Consecutivo', 'required|integer');
        $this->form_validation->set_rules('estado', 'Estado', 'required|in_list[0,1]');

        if ($this->form_validation->run() === false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $idserie = $this->input->post('idserie') ? (int)$this->input->post('idserie') : null;
        $data = [
            'codigo' => $this->input->post('codigo'),
            'nombre' => $this->input->post('nombre'),
            'consecutivo' => (int)$this->input->post('consecutivo'),
            'estado' => (int)$this->input->post('estado')
        ];

        if ($idserie) {
            $data['updated_on'] = time();
            $ok = $this->core_model->update('tb_series_recibos', $data, ['idserie' => $idserie]);
            header('Content-Type: application/json');
            if ($ok) {
                echo json_encode(['success' => true, 'message' => 'Serie actualizada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
            }
        } else {
            $data['created_on'] = time();
            $last = $this->core_model->insert('tb_series_recibos', $data, true);
            header('Content-Type: application/json');
            if ($last) {
                echo json_encode(['success' => true, 'message' => 'Serie creada', 'idserie' => $last]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear serie']);
            }
        }
    }
}
