<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pld extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pld_model');
        $this->load->helper(array('url','form'));
        $this->load->library('session');
    }

    public function index()
    {
        $data = array(
            'titulo' => 'PLD / Cumplimiento',
            'subtitulo' => 'Inicio - Prevención de Lavado de Activos',
            'icono' => 'fas fa-shield-alt',
            'scripts' => array('js/pld_home.js')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('pld/home', $data);
        $this->load->view('layout/footer');
    }

    public function kyc()
    {
        $data = array('titulo' => 'KYC - Identificación', 'icono' => 'fas fa-id-card');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/kyc', $data);
        $this->load->view('layout/footer');
    }

    public function monitoreo()
    {
        $data = array('titulo' => 'Monitoreo de Transacciones', 'icono' => 'fas fa-search-dollar');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/monitoreo', $data);
        $this->load->view('layout/footer');
    }

    public function riesgo()
    {
        $data = array('titulo' => 'Evaluación de Riesgos', 'icono' => 'fas fa-chart-pie');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/riesgo', $data);
        $this->load->view('layout/footer');
    }

    public function alertas()
    {
        $data = array('titulo' => 'Gestión de Alertas', 'icono' => 'fas fa-exclamation-triangle');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/alertas', $data);
        $this->load->view('layout/footer');
    }

    public function reportes()
    {
        $data = array('titulo' => 'Reportes Regulatorios', 'icono' => 'fas fa-file-alt');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/reportes', $data);
        $this->load->view('layout/footer');
    }

    public function expediente()
    {
        $data = array('titulo' => 'Expediente de Cliente', 'icono' => 'fas fa-folder-open');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/expediente', $data);
        $this->load->view('layout/footer');
    }

    public function bitacora()
    {
        $data = array('titulo' => 'Bitácora de Cumplimiento', 'icono' => 'fas fa-clipboard-list');
        $this->load->view('layout/header', $data);
        $this->load->view('pld/bitacora', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: modal for new KYC or quick action
    public function modal_kyc()
    {
        $this->load->view('pld/modal_kyc');
    }

    // AJAX placeholder: save KYC
    public function save_kyc()
    {
        $this->load->library('form_validation');
        $this->load->helper('security');

        $this->form_validation->set_rules('client_id', 'Cliente', 'required|integer');
        $this->form_validation->set_rules('document_type', 'Tipo de documento', 'trim|required');
        $this->form_validation->set_rules('document_number', 'Número de documento', 'trim|required');
        $this->form_validation->set_rules('first_name', 'Nombres', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Apellidos', 'trim|required');
        $this->form_validation->set_rules('birth_date', 'Fecha de nacimiento', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');

        header('Content-Type: application/json');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $post = $this->input->post(NULL, TRUE);

        $data = array(
            'client_id' => intval($post['client_id']),
            'document_type' => $post['document_type'],
            'document_number' => $post['document_number'],
            'first_name' => $post['first_name'],
            'last_name' => $post['last_name'],
            'birth_date' => (!empty($post['birth_date']) ? $post['birth_date'] : NULL),
            'address' => isset($post['address']) ? $post['address'] : NULL,
            'phone' => isset($post['phone']) ? $post['phone'] : NULL,
            'email' => isset($post['email']) ? $post['email'] : NULL,
            'notes' => isset($post['notes']) ? $post['notes'] : NULL,
            'documents' => NULL,
            'created_by' => $this->ion_auth->get_user_id()
        );

        // Handle file uploads (single or multiple input name="documents[]")
        $savedFiles = array();
        if (!empty($_FILES) && isset($_FILES['documents'])) {
            $files = $_FILES['documents'];
            $count = is_array($files['name']) ? count($files['name']) : 1;

            for ($i = 0; $i < $count; $i++) {
                if (is_array($files['name'])) {
                    if (empty($files['name'][$i])) continue;
                    $_FILES['file_tmp'] = array(
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    );
                } else {
                    if (empty($files['name'])) break;
                    $_FILES['file_tmp'] = $files;
                }

                $config['upload_path'] = FCPATH . 'uploads/pld/';
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }
                $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config['max_size'] = 5120; // 5MB
                $config['file_name'] = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['file_tmp']['name']);
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('file_tmp')) {
                    $uploadData = $this->upload->data();
                    $savedFiles[] = array(
                        'orig_name' => $uploadData['client_name'],
                        'file_name' => $uploadData['file_name'],
                        'file_path' => 'uploads/pld/' . $uploadData['file_name'],
                        'size' => $uploadData['file_size']
                    );
                } else {
                    // log upload error, but continue
                    log_message('error', 'PLD upload error: ' . $this->upload->display_errors('', ''));
                }
            }
        }

        if (!empty($savedFiles)) {
            $data['documents'] = json_encode($savedFiles);
        }

        $insertId = $this->Pld_model->save_kyc($data);

        if ($insertId) {
            echo json_encode(['status' => 'success', 'id' => $insertId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar KYC']);
        }
    }
}
