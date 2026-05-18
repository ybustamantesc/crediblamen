<?php
defined('BASEPATH') or exit('Acción no permitida');
class Creditos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('prestamos_model');
        $this->load->model('home_model');
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Créditos',
            'subtitulo' => 'Panel principal de Créditos',
            'icono' => 'ik ik-folder'
        );

        $this->load->view('layout/header', $data);
        $this->load->view('creditos/index');
        $this->load->view('layout/footer');
    }

    public function estadisticas()
    {
        $data = array(
            'titulo' => 'Estadísticas - Créditos',
            'subtitulo' => 'Indicadores y cuotas',
            'icono' => 'ik ik-bar-chart',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'total_asesores' => $this->home_model->countAll('tb_asesores'),
            'total_clientes' => $this->home_model->countAll('tb_clientes'),
            'total_prestamos' => $this->home_model->countAll('tb_creditos'),
            'total_pagos' => $this->home_model->countAll('tb_pagos'),
            'coutas_vencidas' => $this->prestamos_model->getCoutasVencidas(),
            'coutas_pagan_hoy' => $this->prestamos_model->getCoutasPaganHoy(),
        );

        $this->load->view('layout/header', $data);
        $this->load->view('creditos/estadisticas', $data);
        $this->load->view('layout/footer');
    }
}
