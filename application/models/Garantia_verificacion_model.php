<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Garantia_verificacion_model extends CI_Model {

    protected $table = 'tb_garantias_verificaciones';

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_by_garantia($garantia_id)
    {
        $this->db->from($this->table);
        $this->db->where('garantia_id', $garantia_id);
        if ($this->db->field_exists('created_at', $this->table)) {
            $this->db->order_by('created_at', 'DESC');
        }
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_solicitud($solicitud_id)
    {
        $this->db->from($this->table);
        $this->db->where('solicitud_id', $solicitud_id);
        if ($this->db->field_exists('created_at', $this->table)) {
            $this->db->order_by('created_at', 'DESC');
        }
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }
}
