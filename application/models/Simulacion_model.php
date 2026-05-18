<?php
defined('BASEPATH') or exit('Acción no permitida');
class Simulacion_model extends CI_Model
{
	public function getAll()
	{
		$this->db->select([
			'tb_simulacion.*',
			'tb_clientes.idcliente',
			'tb_clientes.apellidos',
			'tb_clientes.nombres',
			'tb_asesores.nombres as nombre_asesor'
		]);
		$this->db->join('tb_clientes', 'tb_simulacion.idcliente=tb_clientes.idcliente');
		$this->db->join('tb_asesores', 'tb_simulacion.idasesor=tb_asesores.idasesor');
		return $this->db->get('tb_simulacion')->result();
	}
	public function getById($id = NULL)
	{
		$this->db->select([
			'tb_simulacion.*',
			'tb_clientes.idcliente',
			'tb_clientes.apellidos',
			'tb_clientes.nombres',
			'tb_asesores.nombres as nombre_asesor'
		]);
		$this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_simulacion.idcliente');
		$this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_simulacion.idasesor');
		$this->db->where('tb_simulacion.idsimulacion', $id);
		return $this->db->get('tb_simulacion')->row();
	}
	public function getAllById($id = NULL)
	{
		$this->db->select([
			'tb_detalle_simulacion.*'
		]);
		$this->db->where('tb_detalle_simulacion.idsimulacion', $id);
		return $this->db->get('tb_detalle_simulacion')->result();
	}
}
