<?php
defined('BASEPATH') or exit('Acción no permitida');

class Pagos_model extends CI_Model
{
    public function get_all_pagos()
    {
        $this->db->select([
            'tb_pagos.idpago',
            'tb_pagos.monto_pago',
            'tb_pagos.idcredito',
            'tb_pagos.fecha_pago as fechaPago',
            'tb_creditos.monto_credito',
            'tb_creditos.monto_credito',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'users.*',
            'tb_credito_detalle.numero_couta'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_pagos.idcliente');
        $this->db->join('tb_creditos', 'tb_creditos.id=tb_pagos.idcredito');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.id=tb_pagos.idcuota');

        $this->db->join('users', 'users.id=tb_pagos.idusuario');
        $this->db->order_by('DATE(tb_pagos.fecha_pago)', 'DESC');
        return $this->db->get('tb_pagos')->result();
    }

    public function get_by_pago_id($pago_id = NULL)
    {
        $this->db->select([
            'tb_pagos.idpago',
            'tb_pagos.monto_pago',
            'tb_pagos.idcredito',
            'tb_pagos.fecha_pago as fechaPago',
            'tb_creditos.monto_credito',
            'tb_credito_detalle.*',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'users.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_pagos.idcliente');
        $this->db->join('tb_creditos', 'tb_creditos.id=tb_pagos.idcredito');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.id=tb_pagos.idcuota');
        $this->db->join('users', 'users.id=tb_pagos.idusuario');
        $this->db->where('tb_pagos.idpago', $pago_id);
        return $this->db->get('tb_pagos')->row();
    }

    public function getPagoById($pago_id = NULL)
    {
        $this->db->select([
            'tb_pagos.idpago',
            'tb_pagos.descuento_pago',
            'tb_pagos.monto_pago',
            'tb_pagos.idcredito',
            'tb_pagos.fecha_pago as fechaPago',
            'tb_creditos.monto_credito',
            'tb_creditos.total_saldo',
            'tb_creditos.numero_coutas',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'users.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_pagos.idcliente');
        $this->db->join('tb_creditos', 'tb_creditos.id=tb_pagos.idcredito');
        $this->db->join('users', 'users.id=tb_pagos.idusuario');
        $this->db->where('tb_pagos.idpago', $pago_id);
        return $this->db->get('tb_pagos')->row();
    }

    public function getSumaCuotas($credito_id)
    {
        $this->db->select('SUM(monto_pago) as monto_pago');
        $this->db->where('idcredito', $credito_id);
        return $this->db->get('tb_pagos')->row();
    }

    /**
     * Return payments recorded in tb_prestamo_pagos (loan plan payments)
     */
    public function get_all_prestamo_pagos($filters = array())
    {
        $this->db->select([
            'p.id',
            'p.idprestamo',
            'p.idcuota',
            'p.idcliente',
            'p.monto_pagado',
            'p.fecha_pago',
            'p.metodo_pago',
            'p.referencia',
            'p.dato_adicional',
            'p.idserie',
            'pc.numero as numero_cuota',
            'sr.codigo as serie_codigo',
            'sr.nombre as serie_nombre',
            'u.id as emitido_por_id',
            'u.first_name as emitido_por_firstname',
            'u.last_name as emitido_por_lastname',
            'c.idcliente as cli_id_from_p',
            'c.apellidos as cli_apellidos_from_p',
            'c.nombres as cli_nombres_from_p',
            'sc.apellidos as cli_apellidos_from_s',
            'sc.nombres as cli_nombres_from_s'
        ]);
        $this->db->from('tb_prestamo_pagos p');
        $this->db->join('tb_prestamo_cuotas pc', 'pc.idcuota = p.idcuota', 'left');
        $this->db->join('tb_prestamos pr', 'pr.idprestamo = p.idprestamo', 'left');
        $this->db->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left');
        $this->db->join('tb_clientes c', 'c.idcliente = p.idcliente', 'left');
        $this->db->join('tb_clientes sc', 'sc.numero_doc = s.numero_doc', 'left');
        // join series and users (issuer)
        $this->db->join('tb_series_recibos sr', 'sr.idserie = p.idserie', 'left');
        $this->db->join('users u', 'u.id = p.idusuario', 'left');

        // apply filters
        if (!empty($filters['date_from'])) {
            $this->db->where('p.fecha_pago >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('p.fecha_pago <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $this->db->group_start();
            $this->db->like('c.apellidos', $q);
            $this->db->or_like('c.nombres', $q);
            $this->db->or_like('sc.apellidos', $q);
            $this->db->or_like('sc.nombres', $q);
            $this->db->or_like('p.idprestamo', $q);
            $this->db->group_end();
        }
        // allow explicit referencia filtering
        if (!empty($filters['referencia'])) {
            $this->db->like('p.referencia', $filters['referencia']);
        }
        if (!empty($filters['idserie'])) {
            $this->db->where('p.idserie', $filters['idserie']);
        }

        $this->db->order_by('p.fecha_pago', 'DESC');
        return $this->db->get()->result();
    }

    public function get_prestamo_pago_by_id($id)
    {
        $this->db->select([
            'p.*',
            'pc.numero as numero_cuota',
            'pr.idprestamo',
            's.numero_doc',
            'c.apellidos',
            'c.nombres',
            'sr.codigo as serie_codigo',
            'sr.nombre as serie_nombre',
            'u.id as emitido_por_id',
            'u.first_name as emitido_por_firstname',
            'u.last_name as emitido_por_lastname'
        ]);
        $this->db->from('tb_prestamo_pagos p');
        $this->db->join('tb_prestamo_cuotas pc', 'pc.idcuota = p.idcuota', 'left');
        $this->db->join('tb_prestamos pr', 'pr.idprestamo = p.idprestamo', 'left');
        $this->db->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left');
        $this->db->join('tb_clientes c', 'c.idcliente = p.idcliente', 'left');
        $this->db->join('tb_series_recibos sr', 'sr.idserie = p.idserie', 'left');
        $this->db->join('users u', 'u.id = p.idusuario', 'left');
        $this->db->where('p.id', $id);
        return $this->db->get()->row();
    }

    public function getCuotasPagos($data)
    {
        $this->db->where_in('id', $data);
        return $this->db->get('tb_credito_detalle')->result();
    }

    public function getPagoId($id)
    {
        $this->db->select([
            'tb_pagos.idpago',
            'tb_pagos.monto_pago',
            'tb_pagos.fecha_pago as fechaPago',
            'tb_creditos.monto_credito',
            'tb_creditos.total_pagar',
            'tb_creditos.total_saldo',
            'tb_creditos.id as idcredito',
            'tb_clientes.idcliente',

            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'users.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_pagos.idcliente');
        $this->db->join('tb_creditos', 'tb_creditos.id=tb_pagos.idcredito');
        $this->db->join('users', 'users.id=tb_pagos.idusuario');
        $this->db->where('tb_pagos.idpago', $id);
        return $this->db->get('tb_pagos')->row();
    }

    public function getPagosDetalleId($id)
    {
        $this->db->select([
            'tb_pagos.idpago',
            'tb_pagos.monto_pago as totalPago',
            'tb_pagos.fecha_pago as fechaPago',
            'tb_pagos_detalle.monto_pagado as montoPagado',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_pagos', 'tb_pagos.idpago=tb_pagos_detalle.idpago');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.id=tb_pagos_detalle.idcuota');
        $this->db->where('tb_pagos_detalle.idpago', $id);
        return $this->db->get('tb_pagos_detalle')->result();
    }
    
    
}
