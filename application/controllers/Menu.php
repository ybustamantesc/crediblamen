<?php
defined('BASEPATH') or exit('Acción no permitida');
class Menu extends CI_Controller
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
            'titulo' => 'Menu de Acceso',
            'subtitulo' => 'Seleccione el módulo al que desea acceder',
            'icono' => 'ik ik-grid'
        );

        // Permisos por rol: por defecto solo 'creditos' visible; administradores ven todo
        $is_admin = false;
        if (isset($this->ion_auth) && method_exists($this->ion_auth, 'is_admin')) {
            $is_admin = $this->ion_auth->is_admin();
        }

        // Only administrators or users in group 'Promotor' may access creditos
        $can_creditos = $is_admin || (isset($this->ion_auth) && method_exists($this->ion_auth, 'in_group') && $this->ion_auth->in_group('Promotor'));
        $data['permissions'] = array(
            'creditos' => $can_creditos,
            'pld' => $is_admin,
            'komani' => $is_admin,
            'tesoreria' => $is_admin,
            'contabilidad' => $is_admin,
            'administracion' => $is_admin,
        );

        $this->load->view('layout/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('layout/footer');
    }
}
