<?php
defined('BASEPATH') or exit('Acción no permitida');
class Estacionar_model extends CI_Model
{
    public function get_all()
    {
        $this->db->select([
            'estacionar.*',
            'precios.precio_id',
            'precios.precio_categoria',
            'precios.precio_valor_hora',
            'formapago.id',
            'formapago.nombre'
        ]);
        $this->db->join('precios', 'precio_id=estacionar_precio_id', 'LEFT');
        $this->db->join('formapago', 'id=estacionar_forma_pago_id', 'LEFT');
        return $this->db->get('estacionar')->result();
    }
    public function get_by_id($estacionar_id = NULL)
    {
        $this->db->select([
            'estacionar.*',
            'precios.precio_id',
            'precios.precio_categoria',
            'precios.precio_valor_hora',
            'formapago.id',
            'formapago.nombre'
        ]);
        $this->db->join('precios', 'precio_id=estacionar_precio_id', 'LEFT');
        $this->db->join('formapago', 'id=estacionar_forma_pago_id', 'LEFT');
        $this->db->where('estacionar_id', $estacionar_id);
        return $this->db->get('estacionar')->row();
    }
    public function get_numero_vacantes($precio_id = NULL)
    {
        $this->db->select('precio_estado');
        $this->db->select('precio_numero_vacantes as vacantes');
        $this->db->where('precio_id', $precio_id);
        return $this->db->get('precios')->row();
    }
}
