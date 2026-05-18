<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TasaCambio extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('TasaCambio_model');
        $this->load->model('Core_model', 'core_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    /**
     * Lista todas las tasas de cambio
     */
    public function index()
    {
        $data = array(
            'titulo' => 'Tasa de Cambio',
            'subtitulo' => 'Gestión de tasas de cambio por fecha',
            'icono' => 'fas fa-dollar-sign',
            'tasas' => $this->TasaCambio_model->get_all()
        );

        $this->load->view('layout/header', $data);
        $this->load->view('tasa_cambio/index', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Formulario para crear/editar tasa de cambio
     */
    public function core($id = null)
    {
        $tasa = null;
        if ($id) {
            $tasa = $this->TasaCambio_model->get_by_id($id);
            if (!$tasa) {
                $this->session->set_flashdata('error', 'Tasa de cambio no encontrada.');
                redirect('tasacambio');
            }
        }

        $data = array(
            'titulo' => $id ? 'Editar Tasa de Cambio' : 'Nueva Tasa de Cambio',
            'subtitulo' => 'Complete los datos de la tasa de cambio',
            'icono' => 'fas fa-dollar-sign',
            'tasa' => $tasa
        );

        $this->load->view('layout/header', $data);
        $this->load->view('tasa_cambio/core', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Guardar tasa de cambio (crear o actualizar)
     */
    public function save()
    {
        $id = $this->input->post('id');
        $fecha = $this->input->post('fecha');
        $tasa_cambio = $this->input->post('tasa_cambio');
        $tasa_venta = $this->input->post('tasa_venta');

        // Validaciones
        if (empty($fecha) || empty($tasa_cambio) || empty($tasa_venta)) {
            $this->session->set_flashdata('error', 'Todos los campos son obligatorios.');
            redirect($id ? 'tasacambio/core/' . $id : 'tasacambio/core');
            return;
        }

        if (!is_numeric($tasa_cambio) || floatval($tasa_cambio) <= 0) {
            $this->session->set_flashdata('error', 'La tasa de cambio COMPRA debe ser un número mayor a cero.');
            redirect($id ? 'tasacambio/core/' . $id : 'tasacambio/core');
            return;
        }

        if (!is_numeric($tasa_venta) || floatval($tasa_venta) <= 0) {
            $this->session->set_flashdata('error', 'La tasa de cambio VENTA debe ser un número mayor a cero.');
            redirect($id ? 'tasacambio/core/' . $id : 'tasacambio/core');
            return;
        }

        // Validación lógica: la tasa de venta debe ser mayor o igual a la de compra
        if (floatval($tasa_venta) < floatval($tasa_cambio)) {
            $this->session->set_flashdata('error', 'La tasa de VENTA debe ser mayor o igual a la tasa de COMPRA.');
            redirect($id ? 'tasacambio/core/' . $id : 'tasacambio/core');
            return;
        }

        $data = array(
            'fecha' => $fecha,
            'tasa_cambio' => floatval($tasa_cambio),
            'tasa_venta' => floatval($tasa_venta)
        );

        try {
            if ($id) {
                // Actualizar
                $this->TasaCambio_model->update($id, $data);
                $this->session->set_flashdata('message', 'Tasa de cambio actualizada correctamente.');
            } else {
                // Verificar si ya existe una tasa para esta fecha
                $existing = $this->TasaCambio_model->get_by_fecha($fecha);
                if ($existing) {
                    $this->session->set_flashdata('error', 'Ya existe una tasa de cambio para esta fecha. Edítela en lugar de crear una nueva.');
                    redirect('tasacambio/core');
                    return;
                }
                // Insertar
                $this->TasaCambio_model->insert($data);
                $this->session->set_flashdata('message', 'Tasa de cambio creada correctamente.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error al guardar: ' . $e->getMessage());
        }

        redirect('tasacambio');
    }

    /**
     * Eliminar tasa de cambio
     */
    public function delete($id)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID no proporcionado.');
            redirect('tasacambio');
            return;
        }

        try {
            $this->TasaCambio_model->delete($id);
            $this->session->set_flashdata('message', 'Tasa de cambio eliminada correctamente.');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error al eliminar: ' . $e->getMessage());
        }

        redirect('tasacambio');
    }

    /**
     * AJAX: Obtener tasa de cambio actual (más reciente)
     */
    public function get_tasa_actual_ajax()
    {
        header('Content-Type: application/json');
        $ultimo_registro = $this->TasaCambio_model->get_ultimo_registro();
        
        if ($ultimo_registro) {
            echo json_encode(array(
                'status' => true, 
                'tasa' => floatval($ultimo_registro->tasa_cambio), // mantener compatibilidad
                'tasa_compra' => floatval($ultimo_registro->tasa_cambio),
                'tasa_venta' => floatval($ultimo_registro->tasa_venta ?: $ultimo_registro->tasa_cambio),
                'fecha' => $ultimo_registro->fecha,
                'id' => $ultimo_registro->id
            ));
        } else {
            echo json_encode(array(
                'status' => false,
                'tasa' => 36.50,
                'tasa_compra' => 36.50,
                'tasa_venta' => 36.50,
                'message' => 'No hay tasas registradas'
            ));
        }
    }

    /**
     * AJAX: Obtener tasa de cambio vigente para una fecha
     */
    public function get_tasa_vigente_ajax()
    {
        header('Content-Type: application/json');
        $fecha = $this->input->get('fecha');
        $tasa_compra = $this->TasaCambio_model->get_tasa_vigente($fecha, 'compra');
        $tasa_venta = $this->TasaCambio_model->get_tasa_vigente($fecha, 'venta');
        echo json_encode(array(
            'status' => true, 
            'tasa' => $tasa_compra, // mantener compatibilidad
            'tasa_compra' => $tasa_compra,
            'tasa_venta' => $tasa_venta,
            'fecha' => $fecha
        ));
    }
}
