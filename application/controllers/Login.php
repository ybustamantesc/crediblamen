<?php
defined('BASEPATH') or exit('Acción no permitida');
class Login extends CI_Controller
{
    public function index()
    {
        $data = array(
            'titulo' => 'Control de Acceso',
            'sistema' => $this->core_model->get_by_id('tb_sistema', array('id' => 1))
        );
        $this->load->view('layout/header', $data);
        $this->load->view('login/index', $data);
        $this->load->view('layout/footer', $data);
    }
    public function auth()
    {
        $identity = html_escape($this->input->post('email'));
        $password = html_escape($this->input->post('password'));
    $remember = $this->input->post('remember') ? TRUE : FALSE; // remember the user if checked

        if ($this->ion_auth->login($identity, $password, $remember)) {
            $usuario = $this->core_model->get_by_id('users', array('email' => $identity));
            $this->session->set_flashdata('success', 'Bienvenid(a) al Sistema: ' . $usuario->first_name);
            // If user is Promotor (Ion Auth group 'promotor' or legacy perfil == 4)
            $is_promotor = false;
            try {
                if (isset($this->ion_auth) && method_exists($this->ion_auth, 'in_group')) {
                    $is_promotor = $this->ion_auth->in_group('promotor');
                }
            } catch (Exception $e) { /* ignore */ }
            if (!$is_promotor && isset($usuario->perfil) && intval($usuario->perfil) === 4) {
                $is_promotor = true;
            }

            if ($is_promotor) {
                // Promotor users go directly to solicitudes
                redirect('solicitudes');
            }

            // Default: go to home
            redirect('home');
        } else {
            $this->session->set_flashdata('error', 'Verifique su Usuario o Contraseña.');
            redirect($this->router->fetch_class());
        }
    }
    public function logout()
    {
        $this->ion_auth->logout();
        redirect($this->router->fetch_class());
    }
}
