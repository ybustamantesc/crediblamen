<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Administracion extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url'));
        $this->load->library(array('session'));
        $this->load->database();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) { redirect('login'); }
    }

    public function index() {
        $this->load->view('administracion/home');
    }

    public function usuarios() { $this->load->view('administracion/usuarios'); }

    // Listado JSON de usuarios para AJAX/DataTables
    public function usuarios_json() {
        $query = $this->db->query("SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.active,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') as grupos
            FROM users u
            LEFT JOIN users_groups ug ON ug.user_id = u.id
            LEFT JOIN groups g ON g.id = ug.group_id
            GROUP BY u.id
            ORDER BY u.id DESC");
        $rows = $query->result_array();
        $this->output->set_content_type('application/json')->set_output(json_encode(['data' => $rows]));
    }

    // Crear / Editar usuario
    public function usuario_save() {
        $id = $this->input->post('id');
        $username = trim($this->input->post('username'));
        $email = trim($this->input->post('email'));
        $first_name = trim($this->input->post('first_name'));
        $last_name = trim($this->input->post('last_name'));
        $password = $this->input->post('password');
        $active = (int)$this->input->post('active');
        $group_ids = $this->input->post('group_ids'); // array

        if (!$username || !$email || (!$id && !$password)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'msg' => 'Datos incompletos']));
        }

        $this->db->trans_begin();
        try {
            if ($id) {
                $data = [
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'active' => $active,
                ];
                if ($password) {
                    $data['password'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $this->db->where('id', (int)$id)->update('users', $data);

                if (is_array($group_ids)) {
                    $this->db->where('user_id', (int)$id)->delete('users_groups');
                    foreach ($group_ids as $gid) {
                        $this->db->insert('users_groups', ['user_id' => (int)$id, 'group_id' => (int)$gid]);
                    }
                }
            } else {
                $data = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'active' => $active ? 1 : 1,
                    'created_on' => time(),
                ];
                $this->db->insert('users', $data);
                $new_id = $this->db->insert_id();
                if (is_array($group_ids)) {
                    foreach ($group_ids as $gid) {
                        $this->db->insert('users_groups', ['user_id' => (int)$new_id, 'group_id' => (int)$gid]);
                    }
                }
            }

            if ($this->db->trans_status() === false) { throw new Exception('DB error'); }
            $this->db->trans_commit();
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => true]));
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'msg' => 'Error al guardar']));
        }
    }

    public function usuario_toggle($id) {
        $active = (int)$this->input->post('active');
        $this->db->where('id', (int)$id)->update('users', ['active' => $active ? 1 : 0]);
        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true]));
    }

    public function usuario_reset_password() {
        $id = (int)$this->input->post('id');
        $new = $this->input->post('new_password');
        if (!$new) {
            // generar una contraseña de 10 chars
            $new = substr(bin2hex(random_bytes(8)), 0, 10);
        }
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $this->db->where('id', $id)->update('users', ['password' => $hash]);
        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'new_password' => $new]));
    }
    public function roles() { $this->load->view('administracion/roles'); }
    public function configuracion() { $this->load->view('administracion/configuracion'); }
    public function seguridad() { $this->load->view('administracion/seguridad'); }
    public function auditoria() { $this->load->view('administracion/auditoria'); }
        public function parametros() { $this->load->view('administracion/parametros'); }
        public function catalogos() { $this->load->view('administracion/catalogos'); }
        public function integraciones() { $this->load->view('administracion/integraciones'); }
        public function respaldos() { $this->load->view('administracion/respaldo'); }
        public function plantillas() { $this->load->view('administracion/plantillas'); }
}
