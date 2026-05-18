<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estadisticas extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','form']);
        $this->load->library(['session']);
    }

    public function index()
    {
        $data = [
            'titulo' => 'Estadísticas',
            'subtitulo' => 'Indicadores financieros del sistema',
            'icono' => 'fas fa-chart-bar',
            'scripts' => ['js/estadisticas.js']
        ];
        $this->load->view('layout/header', $data);
        $this->load->view('estadisticas/home', $data);
        $this->load->view('layout/footer');
    }

    // Returns JSON with basic indicators (replace with real queries later)
    public function indicators_json()
    {
        // TODO: replace with DB queries to compute real metrics
        $indicators = [
            'revenue' => 125430.75,
            'expenses' => 84210.40,
            'net' => 41220.35,
            'total_loans' => 342,
            'overdue_loans' => 18,
            'today_collections' => 3540.00,
            'month_collections' => 45230.50
        ];
        header('Content-Type: application/json');
        echo json_encode($indicators);
    }

    // Returns detail rows for a given metric
    public function metric_details($metric = '')
    {
        // Example stubbed data
        $details = [];
        switch ($metric) {
            case 'overdue_loans':
                $details = [
                    ['loan_id'=>101,'client'=>'Juan Perez','amount'=>1200.00,'days'=>15],
                    ['loan_id'=>223,'client'=>'Maria Lopez','amount'=>3400.00,'days'=>32]
                ];
                break;
            case 'revenue':
                $details = [
                    ['source'=>'Pagos','amount'=>95000.00],
                    ['source'=>'Intereses','amount'=>30430.75]
                ];
                break;
            default:
                $details = [];
        }
        header('Content-Type: application/json');
        echo json_encode($details);
    }
}
