<?php
defined('BASEPATH') or exit('Acción no permitida');
class Core_model extends CI_Model
{
    public function get_all($table = NULL, $condition = NULL)
    {
        if ($table && $this->db->table_exists($table)) {
            if (is_array($condition)) {
                $this->db->where($condition);
            }
            return $this->db->get($table)->result();
        } else {
            return FALSE;
        }
    }
    public function get_by_id($table = NULL, $condition = NULL)
    {
        if ($table && $this->db->table_exists($table) && is_array($condition)) {
            $this->db->where($condition);
            $this->db->limit(1);
            return $this->db->get($table)->row();
        } else {
            return FALSE;
        }
    }
       public function get_by_id_all($table = NULL, $condition = NULL)
    {
        if ($table && $this->db->table_exists($table) && is_array($condition)) {
            $this->db->where($condition);
            //$this->db->limit(1);
            return $this->db->get($table)->result();
        } else {
            return FALSE;
        }
    }
    public function insert($table = NULL, $data = NULL, $get_last_id = NULL)
    {
        if ($table && $this->db->table_exists($table) && is_array($data)) {
            $this->db->insert($table, $data);
            #Almcenamos el ultimo id
            if ($get_last_id) {
                $last = $this->db->insert_id();
                $this->session->set_userdata('last_id', $last);
            }
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('success', 'Registro Guardado Correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al Guardar el Registro.');
            }
            // Return the insert id when requested, otherwise boolean success
            if ($get_last_id) {
                return isset($last) ? $last : false;
            }
            return ($this->db->affected_rows() > 0);
        } else {
            return false;
        }
    }
    
    public function update($table = NULL, $data = NULL, $condition = NULL)
    {
        if ($table && $this->db->table_exists($table) && is_array($data) && is_array($condition)) {
            if ($this->db->update($table, $data, $condition)) {
                $this->session->set_flashdata('success', 'Registro Actualizado Correctamente.');
                return true;
            } else {
                $this->session->set_flashdata('error', 'Error al Actualizar el Registro');
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function delete($table = NULL, $condition = NULL)
    {
        if ($table && $this->db->table_exists($table) && is_array($condition)) {
            if ($this->db->delete($table, $condition)) {
                $this->session->set_flashdata('success', 'Registro Eliminado Correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al Eliminar el Registro.');
            }
        } else {
            return false;
        }
    }
    
}
