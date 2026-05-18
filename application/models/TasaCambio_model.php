<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TasaCambio_model extends CI_Model {

    protected $table = 'tb_tasa_cambio';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtiene todas las tasas de cambio ordenadas por fecha descendente
     */
    public function get_all()
    {
        return $this->db->order_by('fecha', 'DESC')->get($this->table)->result();
    }

    /**
     * Obtiene una tasa de cambio por ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Obtiene la tasa de cambio para una fecha específica
     */
    public function get_by_fecha($fecha)
    {
        return $this->db->where('fecha', $fecha)->get($this->table)->row();
    }

    /**
     * Obtiene el registro completo más reciente (última fecha)
     */
    public function get_ultimo_registro()
    {
        return $this->db->order_by('fecha', 'DESC')->order_by('id', 'DESC')->limit(1)->get($this->table)->row();
    }

    /**
     * Obtiene la tasa de cambio más reciente (última fecha disponible)
     * Por defecto devuelve la tasa de compra
     */
    public function get_tasa_actual($tipo = 'compra')
    {
        $row = $this->get_ultimo_registro();
        if (!$row) return 36.50; // default fallback
        
        if ($tipo === 'venta') {
            return floatval($row->tasa_venta ?: $row->tasa_cambio); // fallback a compra si no hay venta
        }
        return floatval($row->tasa_cambio);
    }

    /**
     * Obtiene la tasa de cambio vigente para una fecha dada
     * (toma la tasa más reciente anterior o igual a la fecha)
     */
    public function get_tasa_vigente($fecha = null, $tipo = 'compra')
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        $row = $this->db->where('fecha <=', $fecha)
                        ->order_by('fecha', 'DESC')
                        ->limit(1)
                        ->get($this->table)
                        ->row();
        if (!$row) return 36.50; // default fallback
        
        if ($tipo === 'venta') {
            return floatval($row->tasa_venta ?: $row->tasa_cambio);
        }
        return floatval($row->tasa_cambio);
    }

    /**
     * Inserta una nueva tasa de cambio
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Actualiza una tasa de cambio existente
     */
    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Elimina una tasa de cambio
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
