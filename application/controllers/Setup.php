<?php defined('BASEPATH') OR exit('Acción no permitida');

class Setup extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!isset($this->ion_auth) || !$this->ion_auth->logged_in()) {
            redirect('login');
        }
    }

    /**
     * Create the 'Promotor' group if it does not exist.
     * Accessible only to admins.
     */
    public function create_promotor()
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('error', 'Acceso denegado');
            redirect('/');
        }

        $group_name = 'Promotor';
        // Check existing groups (by name)
        $groups = $this->ion_auth->groups()->result();
        foreach ($groups as $g) {
            if (strcasecmp($g->name, $group_name) === 0) {
                $this->session->set_flashdata('info', "Grupo '{$group_name}' ya existe.");
                redirect('usuarios');
            }
        }

        // Create group via Ion Auth if available, otherwise try direct DB insert into `groups` table
        if (method_exists($this->ion_auth, 'create_group')) {
            $res = $this->ion_auth->create_group($group_name, 'Promotor - Acceso a procesos comerciales');
            if ($res) {
                $this->session->set_flashdata('success', "Grupo '{$group_name}' creado correctamente.");
            } else {
                $this->session->set_flashdata('error', "No se pudo crear el grupo '{$group_name}'.");
            }
        } else {
            // Fallback: attempt to insert directly into the groups table
            try {
                $table = 'groups';
                if ($this->db->table_exists($table)) {
                    $exists = $this->db->get_where($table, array('name' => $group_name))->row();
                    if ($exists) {
                        $this->session->set_flashdata('info', "Grupo '{$group_name}' ya existe.");
                    } else {
                        $ins = $this->db->insert($table, array('name' => $group_name, 'description' => 'Promotor - Acceso a procesos comerciales'));
                        if ($ins) $this->session->set_flashdata('success', "Grupo '{$group_name}' creado correctamente (via DB fallback).");
                        else $this->session->set_flashdata('error', "No se pudo crear el grupo '{$group_name}' via DB.");
                    }
                } else {
                    $this->session->set_flashdata('error', 'Tabla de grupos no encontrada en la base de datos (groups).');
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Error al crear grupo via DB: ' . $e->getMessage());
            }
        }
        redirect('usuarios');
    }
}
