<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_productos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('core_model');
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Tipo de Productos',
            'subtitulo' => 'Administrar tipos de productos y porcentajes por crédito',
            'icono' => 'fas fa-boxes',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'tipos' => $this->core_model->get_all('tb_tipo_productos')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('tipos_productos/index');
        $this->load->view('layout/footer');
    }

    // AJAX: crear nuevo tipo
    public function add_ajax()
    {
        if (!$this->ion_auth->is_admin()) {
            echo json_encode(['status'=>false,'message'=>'Sin permisos']); return;
        }
        $nombre = $this->input->post('nombre');
        $porc = $this->input->post('porcentaje');
        $clasificacion = $this->input->post('clasificacion');
        // additional fields
        $monto_min = $this->input->post('monto_min');
        $monto_max = $this->input->post('monto_max');
        $tasa_mensual = $this->input->post('tasa_mensual');
        $comision_desembolso = $this->input->post('comision_desembolso');
        $plazo_min = $this->input->post('plazo_min');
        $plazo_max = $this->input->post('plazo_max');
        if (!$nombre) { echo json_encode(['status'=>false,'message'=>'Nombre requerido']); return; }
        $data = [
            'nombre' => $nombre,
            'porcentaje' => floatval($porc),
            'monto_min' => ($monto_min !== null && $monto_min !== '' ? floatval($monto_min) : null),
            'monto_max' => ($monto_max !== null && $monto_max !== '' ? floatval($monto_max) : null),
            'tasa_mensual' => ($tasa_mensual !== null && $tasa_mensual !== '' ? floatval($tasa_mensual) : null),
            'comision_desembolso' => ($comision_desembolso !== null && $comision_desembolso !== '' ? floatval($comision_desembolso) : null),
            'clasificacion' => ($clasificacion !== null && $clasificacion !== '' ? $clasificacion : null),
            'plazo_min' => ($plazo_min !== null && $plazo_min !== '' ? intval($plazo_min) : null),
            'plazo_max' => ($plazo_max !== null && $plazo_max !== '' ? intval($plazo_max) : null)
        ];
        $this->core_model->insert('tb_tipo_productos', $data);
        $id = $this->db->insert_id();
        $row = $this->core_model->get_by_id('tb_tipo_productos', ['id'=>$id]);
        echo json_encode(['status'=>true,'tipo'=>$row]);
    }

    // AJAX: actualizar
    public function edit_ajax()
    {
        if (!$this->ion_auth->is_admin()) { echo json_encode(['status'=>false,'message'=>'Sin permisos']); return; }
        $id = intval($this->input->post('id'));
        if (!$id) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
        $nombre = $this->input->post('nombre');
        $porc = $this->input->post('porcentaje');
        $clasificacion = $this->input->post('clasificacion');
        $monto_min = $this->input->post('monto_min');
        $monto_max = $this->input->post('monto_max');
        $tasa_mensual = $this->input->post('tasa_mensual');
        $comision_desembolso = $this->input->post('comision_desembolso');
        $plazo_min = $this->input->post('plazo_min');
        $plazo_max = $this->input->post('plazo_max');
        $data = [
            'nombre'=>$nombre,
            'porcentaje'=>floatval($porc),
            'monto_min' => ($monto_min !== null && $monto_min !== '' ? floatval($monto_min) : null),
            'monto_max' => ($monto_max !== null && $monto_max !== '' ? floatval($monto_max) : null),
            'tasa_mensual' => ($tasa_mensual !== null && $tasa_mensual !== '' ? floatval($tasa_mensual) : null),
            'comision_desembolso' => ($comision_desembolso !== null && $comision_desembolso !== '' ? floatval($comision_desembolso) : null),
            'clasificacion' => ($clasificacion !== null && $clasificacion !== '' ? $clasificacion : null),
            'plazo_min' => ($plazo_min !== null && $plazo_min !== '' ? intval($plazo_min) : null),
            'plazo_max' => ($plazo_max !== null && $plazo_max !== '' ? intval($plazo_max) : null)
        ];
        $this->core_model->update('tb_tipo_productos', $data, ['id'=>$id]);
        $row = $this->core_model->get_by_id('tb_tipo_productos', ['id'=>$id]);
        echo json_encode(['status'=>true,'tipo'=>$row]);
    }

    public function del($id = NULL)
    {
        if (!$this->ion_auth->is_admin()) { $this->session->set_flashdata('info','Sin permisos'); redirect($this->router->fetch_class()); }
        if (!$id || !$this->core_model->get_by_id('tb_tipo_productos', ['id'=>$id])) { $this->session->set_flashdata('error','Registro no encontrado'); redirect($this->router->fetch_class()); }
        $this->core_model->delete('tb_tipo_productos', ['id'=>$id]);
        redirect($this->router->fetch_class());
    }

    // AJAX: obtener un tipo por id
    public function get_ajax($id = NULL)
    {
        if (!$id) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
        $row = $this->core_model->get_by_id('tb_tipo_productos', ['id'=>intval($id)]);
        if (!$row) { echo json_encode(['status'=>false,'message'=>'No encontrado']); return; }
        echo json_encode(['status'=>true,'tipo'=>$row]);
    }

    // AJAX: eliminar tipo
    public function del_ajax()
    {
        if (!$this->input->is_ajax_request()) { /* allow AJAX only */ }
        if (!$this->ion_auth->is_admin()) { echo json_encode(['status'=>false,'message'=>'Sin permisos']); return; }
        $id = intval($this->input->post('id'));
        if (!$id) { echo json_encode(['status'=>false,'message'=>'ID requerido']); return; }
        $exists = $this->core_model->get_by_id('tb_tipo_productos', ['id'=>$id]);
        if (!$exists) { echo json_encode(['status'=>false,'message'=>'Registro no encontrado']); return; }
        $this->core_model->delete('tb_tipo_productos', ['id'=>$id]);
        echo json_encode(['status'=>true,'message'=>'Eliminado']);
    }

    // AJAX: obtener tipos que aplican según monto, plazo y clasificación
    public function match_ajax()
    {
        // allow both GET and POST
        $monto = $this->input->get_post('monto');
        $porcentaje = $this->input->get_post('porcentaje'); // legacy: percent (e.g. '6' for 6%)
        $negocio = $this->input->get_post('negocio');
        $clasificacion = $this->input->get_post('clasificacion');

        // base query
        $this->db->select('*')->from('tb_tipo_productos');

        // classification filter: prefer explicit clasificacion param, otherwise fallback to negocio flag
        if ($clasificacion && trim($clasificacion) !== '') {
            $this->db->where('clasificacion', $clasificacion);
        } else {
            if ($negocio && intval($negocio) === 1) {
                $this->db->where('clasificacion', 'Negocios');
            } else {
                $this->db->where_in('clasificacion', array('Personas', 'Viviendo o Hipotecario', 'Vehiculos Usados'));
            }
        }

        // monto filter: between monto_min and monto_max (null treated as open)
        if ($monto !== null && $monto !== '') {
            $m = floatval($monto);
            $this->db->where("( (monto_min IS NULL OR monto_min <= {$m}) AND (monto_max IS NULL OR monto_max >= {$m}) )", null, false);
        }

        // porcentaje filter: legacy support
        if ($porcentaje !== null && $porcentaje !== '') {
            $p = floatval($porcentaje) / 100.0;
            // allow small tolerance
            $tol = 0.00001;
            $minp = $p - $tol;
            $maxp = $p + $tol;
            $this->db->where('porcentaje >=', $minp);
            $this->db->where('porcentaje <=', $maxp);
        }

        $query = $this->db->get();
        $rows = $query->result();
        echo json_encode(array('status'=>true, 'tipos'=>$rows));
    }

    // AJAX: list all tipos (lightweight JSON) for client-side population
    public function list_ajax()
    {
        $rows = $this->core_model->get_all('tb_tipo_productos');
        echo json_encode(array('status'=>true, 'tipos'=>$rows));
    }
}
