<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Konami extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url'));
        $this->load->library(array('session'));
    }

    public function index() {
        $this->load->view('konami/index');
    }

    // 1. Información Institucional (Reportes Regulatorios)
    public function informes() {
        $this->load->model('Prestamos_model');
        $mes = $this->input->get('mes_reporte');
        $data = [];
        if ($mes) {
            $fechaInicio = $mes . '-01';
            $fechaFin = date('Y-m-t', strtotime($fechaInicio));
        } else {
            $fechaInicio = date('Y-m-01');
            $fechaFin = date('Y-m-t');
        }
        // Paginación
        $per_page = 50;
        $page = intval($this->input->get('page')) > 0 ? intval($this->input->get('page')) : 1;
        $offset = ($page - 1) * $per_page;
        $prestamos = $this->Prestamos_model->get_prestamos_con_cuotas_pagadas($fechaInicio, $fechaFin);
        $total_rows = count($prestamos);
        $prestamos_paged = array_slice($prestamos, $offset, $per_page);
        $data['prestamos_prim'] = $prestamos_paged;
        $data['mes_reporte'] = $mes;
        $data['total_rows'] = $total_rows;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $this->load->view('konami/informes', $data);
    }

    public function cheats() {
        $this->load->view('konami/cheats');
    }

    public function history() {
        $this->load->view('konami/history');
    }

    public function about() {
        $this->load->view('konami/about');
    }

    // 2. Seguimiento de Cartera de Crédito
    public function cartera() {
        $this->load->view('konami/cartera');
    }

    // 3. Gestión de Usuarios – PLD/FT
    public function pld() {
        $this->load->view('konami/pld');
    }

    // 4. Control de Operaciones Inusuales
    public function inusuales() {
        $this->load->view('konami/inusuales');
    }

    // 5. Gobierno Corporativo
    public function gobierno() {
        $this->load->view('konami/gobierno');
    }

    // 6. Gestión de Riesgos
    public function riesgos() {
        $this->load->view('konami/riesgos');
    }

    // 7. Reporte Financiero Estandarizado
    public function financiero() {
        $this->load->view('konami/financiero');
    }

    // 8. Monitoreo de Límites Regulatorios
    public function limites() {
        $this->load->view('konami/limites');
    }

    // 9. Integración Contable
    public function integracion() {
        $this->load->view('konami/integracion');
    }

    // 10. Historial y Auditoría Interna
    public function auditoria() {
        $this->load->view('konami/auditoria');
    }
}
