<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Garantia_model extends CI_Model {
    protected $table = 'tb_garantias';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result();
    }

    /**
     * Return one row per solicitud grouped, with representative fields and aggregates
     */
    public function get_grouped_by_solicitud()
    {
        $sql = "SELECT g.solicitud_id, MIN(g.id) AS id, COUNT(*) AS items_count, SUM(IFNULL(g.cantidad,0)) AS cantidad_total,
                    (SELECT nombre FROM " . $this->table . " g2 WHERE g2.solicitud_id = g.solicitud_id ORDER BY g2.id ASC LIMIT 1) AS nombre,
                    (SELECT marca FROM " . $this->table . " g2 WHERE g2.solicitud_id = g.solicitud_id ORDER BY g2.id ASC LIMIT 1) AS marca,
                    (SELECT modelo FROM " . $this->table . " g2 WHERE g2.solicitud_id = g.solicitud_id ORDER BY g2.id ASC LIMIT 1) AS modelo,
                    MAX(COALESCE(g.created_at, '1970-01-01')) AS last_created
                FROM " . $this->table . " g
                GROUP BY g.solicitud_id
                ORDER BY last_created DESC";
        return $this->db->query($sql)->result();
    }

    public function get_all_by_solicitud($solicitud_id)
    {
        return $this->db->order_by('id','ASC')->get_where($this->table, ['solicitud_id' => $solicitud_id])->result();
    }

    public function delete_by_solicitud($solicitud_id)
    {
        return $this->db->delete($this->table, ['solicitud_id' => $solicitud_id]);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        $err = $this->db->error();
        if (! empty($err) && isset($err['message']) && $err['message'] !== '') {
            log_message('error', '[GARANTIAS] DB INSERT ERROR: ' . print_r($err, true));
        }
        return $this->db->insert_id();
    }

    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_by_solicitud($solicitud_id)
    {
        return $this->db->get_where($this->table, ['solicitud_id' => $solicitud_id])->row();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
}
