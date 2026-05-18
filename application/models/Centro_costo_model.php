<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Centro_costo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtener todos los centros de costo activos
     */
    public function get_all_active()
    {
        $this->db->where('activo', 1);
        $this->db->order_by('codigo', 'ASC');
        return $this->db->get('tb_centro_costo')->result();
    }

    /**
     * Obtener todos los centros de costo (activos e inactivos)
     */
    public function get_all()
    {
        $this->db->order_by('codigo', 'ASC');
        return $this->db->get('tb_centro_costo')->result();
    }

    /**
     * Obtener un centro de costo por ID
     */
    public function get_by_id($id)
    {
        return $this->db->get_where('tb_centro_costo', ['id' => $id])->row();
    }

    /**
     * Obtener un centro de costo por código
     */
    public function get_by_codigo($codigo)
    {
        return $this->db->get_where('tb_centro_costo', ['codigo' => $codigo])->row();
    }

    /**
     * Crear nuevo centro de costo
     */
    public function create($data)
    {
        return $this->db->insert('tb_centro_costo', $data);
    }

    /**
     * Actualizar centro de costo
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tb_centro_costo', $data);
    }

    /**
     * Eliminar centro de costo
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tb_centro_costo');
    }

    /**
     * Verificar si un centro de costo está en uso
     */
    public function is_in_use($id)
    {
        $this->db->where('centro_costo_id', $id);
        return $this->db->get('tb_journal')->num_rows() > 0;
    }
}
