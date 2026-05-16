<?php
defined('BASEPATH') or exit('Acción no permitida');
class Caja_model extends CI_Model
{
	public function validar_caja()
	{
		$fecha = date('Y-m-d');
		$this->db->select(['tb_caja.*']);
		$this->db->where('DATE(fecha_apertura)', $fecha);
		$this->db->where('estado', 1);
		$data = $this->db->get('tb_caja')->result();
		return $data;
	}
	public function getTotalCajaCierre()
	{
		$fecha = date('Y-m-d');
		$this->db->select([
			'SUM(monto_movimiento) as total_cierre'
		]);
		$this->db->where('DATE(fecha_apertura)', $fecha);
		$this->db->where('estado', 1);
		$data = $this->db->get('tb_caja')->result();
		return $data;
	}
	public function getTotalCajaFecha($idcaja)
	{
		$fecha = date('Y-m-d');
		$this->db->select([
			'tb_caja.*'
		]);
		$this->db->where('DATE(fecha_apertura)', $fecha);
		$this->db->where('estado', 1);
		$data = $this->db->get('tb_caja')->result();
		return $data;
	}
	public function getDatosCaja()
	{
		$fecha = date('Y-m-d');
		$this->db->select([
			'COUNT(*) as cajas_abiertas',
			'fecha_apertura',
			'fecha_cierre',
			'estado',
			'monto_apertura'
		]);
		$this->db->where('estado', 1);
		$data = $this->db->get('tb_caja')->row();
		return $data;
	}
}
