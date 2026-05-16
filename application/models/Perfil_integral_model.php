<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil_integral_model extends CI_Model {

    protected $table = 'tb_perfil_integral_cliente';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_solicitud($solicitud_id)
    {
        // Try common column names for solicitud reference
        $candidates = array('solicitud_id','idsolicitud','id_solicitud');
        foreach ($candidates as $col) {
            // check that column exists in table to avoid MySQL 1054 errors
            if (! $this->db->field_exists($col, $this->table)) continue;
            $q = $this->db->get_where($this->table, array($col => $solicitud_id));
            if ($q && $q->num_rows() > 0) return $q->row();
        }
        return null;
    }

    public function get($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        // Detect primary key column for this table
        $pk = 'id';
        try {
            $q = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '".$this->db->escape_str($this->table)."' AND COLUMN_KEY = 'PRI'");
            if ($q && $q->num_rows() > 0) {
                $row = $q->row_array();
                if (!empty($row['COLUMN_NAME'])) $pk = $row['COLUMN_NAME'];
            }
        } catch (Exception $e) {
            // fallback to 'id'
        }
        return $this->db->update($this->table, $data, array($pk => $id));
    }
}
