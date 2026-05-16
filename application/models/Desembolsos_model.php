<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Desembolsos_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function listar_pendientes($start = null, $end = null, $q = null) {
        $this->db->select("p.idprestamo, CONCAT(COALESCE(c.nombres,''), ' ', COALESCE(c.apellidos,'')) as cliente, p.monto_credito as monto, p.fecha_desembolso, p.primer_dia_pago, p.usuario_desembolso, p.fecha_desembolso_real, p.desembolsado, p.estado as estado_prestamo, p.obs_desembolso, p.costos_legales, p.seguros, p.comisiones, p.numero_coutas as plazo, p.interes_credito as tasa, COALESCE(NULLIF(TRIM(CONCAT(COALESCE(u_id.first_name, ''), ' ', COALESCE(u_id.last_name, ''))), ''), NULLIF(TRIM(CONCAT(COALESCE(u_user.first_name, ''), ' ', COALESCE(u_user.last_name, ''))), ''), NULLIF(TRIM(u_id.username), ''), NULLIF(TRIM(u_user.username), ''), p.usuario_desembolso) as usuario_desembolso_nombre", FALSE);
        $this->db->select("EXISTS(SELECT 1 FROM teso_movimientos tm WHERE tm.tipo = 'desembolso_preview' AND tm.estado = 'previsualizacion' AND (tm.referencia2 = CONCAT('p=', p.idprestamo) OR tm.referencia2 LIKE CONCAT('p=', p.idprestamo, '&%'))) as solicitud_pendiente", FALSE);
        $this->db->select("CASE 
            WHEN p.desembolsado = 1 THEN 'procesado'
            WHEN EXISTS(SELECT 1 FROM teso_movimientos tm WHERE tm.tipo = 'desembolso_preview' AND tm.estado = 'previsualizacion' AND (tm.referencia2 = CONCAT('p=', p.idprestamo) OR tm.referencia2 LIKE CONCAT('p=', p.idprestamo, '&%'))) THEN 'pendiente_aprobacion'
            ELSE 'pendiente'
        END as estado", FALSE);
        $this->db->from('tb_prestamos p');
        $this->db->join('tb_solicitudes s', 's.idsolicitud = p.idsolicitud', 'left');
        $this->db->join('tb_clientes c', 'c.idcliente = s.idcliente', 'left');
        $this->db->join('users u_id', 'u_id.id = p.usuario_desembolso', 'left');
        $this->db->join('users u_user', 'u_user.username = p.usuario_desembolso', 'left');
        
        // FILTRO 1: Solo planes NO anulados (estado != 2)
        $this->db->where('p.estado !=', 2);
        
        // FILTRO 2: Solo solicitudes aprobadas (comparar en minúscula para seguridad)
        $this->db->where("LOWER(s.estado_aprobacion) = 'aprobado'");
        
        // Filtros opcionales: rango de fechas y búsqueda
        if($start) $this->db->where('p.fecha_desembolso >=',$start);
        if($end) $this->db->where('p.fecha_desembolso <=',$end);
        if($q) {
            $this->db->group_start();
            $this->db->like('p.idprestamo',$q);
            $this->db->or_like('c.nombres',$q);
            $this->db->or_like('c.apellidos',$q);
            $this->db->group_end();
        }
        $this->db->order_by('p.fecha_desembolso','asc');
        return $this->db->get()->result();
    }
    public function ejecutar_desembolso($idprestamo, $fecha, $primer_dia_pago, $obs) {
        $usuario = $this->session->userdata('username') ?: $this->session->userdata('user_id');
        $this->db->where('idprestamo',$idprestamo);
        return $this->db->update('tb_prestamos', [
            'desembolsado'=>1,
            'fecha_desembolso'=>$fecha,
            'primer_dia_pago'=>$primer_dia_pago,
            'obs_desembolso'=>$obs,
            'usuario_desembolso'=>$usuario,
            'fecha_desembolso_real'=>date('Y-m-d H:i:s')
        ]);
    }
}
