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
        return $this->db->get_where($this->table, array('garantia_id' => $garantia_id))->result();
    }

    public function get_by_solicitud($solicitud_id)
    {
        return $this->db->get_where($this->table, array('solicitud_id' => $solicitud_id))->result();
    }
}
