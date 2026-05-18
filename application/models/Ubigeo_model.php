<?php
defined('BASEPATH') or exit('Acción no permitida');
class Ubigeo_model extends CI_Model
{
    public function getDepartamentos()
    {
        $this->db->select([
            'id',
            'nombre'
        ]);
        return $this->db->get('tb_departamentos')->result();
    }
    public function getProvincias($departamento_id = NULL)
    {
        $this->db->select([
            'id',
            'nombre',
            'iddepartamento'
        ]);
        $this->db->where('iddepartamento', $departamento_id);
        return $this->db->get('tb_provincias')->result();
    }
    function get_category($category_id)
    {
        $query = $this->db->get_where('tb_provincias', array('iddepartamento' => '01'));
        return $query;
    }
    public function getDistritos($provincia_id = NULL)
    {
        $this->db->select([
            'id',
            'nombre',
            'idprovincia'
        ]);
        $this->db->where('idprovincia', $provincia_id);
        return $this->db->get('tb_distritos')->result();
    }
}
