<?php
defined('BASEPATH') or exit('Acción no permitida');
class Matriculas_model extends CI_Model
{
    public function get_all()
    {
        $this->db->select([
            'matriculas.*',
            'precios.precio_id',
            'precios.precio_categoria',
            'precios.precio_valor_mensualidad',
            'clientes.id',
            'clientes.nombres',
            'clientes.diasVencimiento'
        ]);
        $this->db->join('precios', 'precio_id=precioid', 'LEFT');
        $this->db->join('clientes', 'id=clienteid', 'LEFT');
        return $this->db->get('matriculas')->result();
    }
}
