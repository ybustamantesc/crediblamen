<?php
defined('BASEPATH') or exit('Acción no permitida');
class Reportes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        // load any models if needed later
        $this->load->model('home_model');
    }

    public function pagos_hoy()
    {
        $data = array(
            'titulo' => 'Pagos de Hoy',
            'subtitulo' => 'Listado de pagos registrados en el día',
            'icono' => 'fas fa-dollar-sign',
        );

        $this->load->view('layout/header', $data);
        $this->load->view('reportes/pagos_hoy', $data);
        $this->load->view('layout/footer');
    }

    public function vencidos()
    {
        $data = array(
            'titulo' => 'Vencidos',
            'subtitulo' => 'Pagos/ cuotas vencidas',
            'icono' => 'fas fa-exclamation-triangle',
        );

        $this->load->view('layout/header', $data);
        $this->load->view('reportes/vencidos', $data);
        $this->load->view('layout/footer');
    }

    public function estadisticas()
    {
        $data = array(
            'titulo' => 'Estadísticas',
            'subtitulo' => 'Indicadores y métricas',
            'icono' => 'fas fa-chart-line',
        );

        $this->load->view('layout/header', $data);
        $this->load->view('reportes/estadisticas', $data);
        $this->load->view('layout/footer');
    }

    public function informes()
    {
        $data = array(
            'titulo' => 'Informes',
            'subtitulo' => 'Generar y descargar reportes',
            'icono' => 'fas fa-file-alt',
        );

        $this->load->view('layout/header', $data);
        $this->load->view('reportes/informes', $data);
        $this->load->view('layout/footer');
    }
}
