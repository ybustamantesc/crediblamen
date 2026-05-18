<?php
defined('BASEPATH') or exit('Acción no permitida');
class Home_model extends CI_Model
{
	public function getTotalBancos()
	{
		$this->db->select_sum('precio_numero_vacantes');
		$this->db->where('precio_estado', 1);
		return $this->db->get('precios')->row();
	}
	public function getTotalPagos()
	{
		$this->db->select('FORMAT(SUM(valor),2) as valor');
		return $this->db->get('matriculas')->row();
	}
	public function getTotalAvulsos()
	{
		$this->db->select('FORMAT(SUM(estacionar_valor_adeudado),2) as totalAvulsos');
		return $this->db->get('estacionar')->row();
	}
	public function countAll($table = NULL, $condition = NULL)
	{
		$this->db->from($table);
		if ($condition) {
			$this->db->where($condition);
		}
		return $this->db->count_all_results();
	}
	public function getMatriculasVencidas()
	{
		$this->db->where('fechaVencimiento<', date('Y-m-d'));
		$this->db->where('estado', 0);
		return $this->db->get('matriculas')->result();
	}
}
