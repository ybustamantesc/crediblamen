<?php
defined('BASEPATH') or exit('Acción no permitida');

class Prestamos_model extends CI_Model
{
    // Devuelve todos los préstamos con la cantidad de cuotas pagadas en el rango de fechas
    public function get_prestamos_con_cuotas_pagadas($fechaInicio, $fechaFin)
    {
        $fechaFinSql = $this->db->escape($fechaFin);
        // Obtener paginación desde GET si existe
        $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 50;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $per_page;
        $sql = "SELECT pr.idprestamo, pr.numero_coutas, pr.idsolicitud, pr.monto_credito, pr.fecha_credito,
                        pr.idprestamo as id_credito,
                        DATE_FORMAT(pr.fecha_desembolso, '%Y%m%d') as fecha_otorgamiento,
                        (
                            SELECT DATE_FORMAT(MAX(cu.fecha_vencimiento), '%Y%m%d')
                            FROM tb_prestamo_cuotas cu
                            WHERE cu.idprestamo = pr.idprestamo
                        ) as fecha_vencimiento_credito,
                        COALESCE(MAX(pc.numero), 0) as cuotas_pagadas,
                        (
                            SELECT COUNT(*)
                            FROM tb_prestamo_cuotas c
                            WHERE c.idprestamo = pr.idprestamo
                                AND c.fecha_vencimiento <= $fechaFinSql
                                AND c.numero > COALESCE((
                                    SELECT MAX(pc2.numero)
                                    FROM tb_prestamo_pagos pp2
                                    LEFT JOIN tb_prestamo_cuotas pc2 ON pc2.idcuota = pp2.idcuota
                                    WHERE pp2.idprestamo = pr.idprestamo AND pp2.fecha_pago <= $fechaFinSql
                                ), 0)
                        ) as cuotas_vencidas,
                        (
                            CASE 
                                WHEN (
                                    SELECT COUNT(*)
                                    FROM tb_prestamo_cuotas c2
                                    WHERE c2.idprestamo = pr.idprestamo
                                        AND c2.fecha_vencimiento <= $fechaFinSql
                                        AND (c2.saldo IS NULL OR c2.saldo > 0)
                                ) = 0 THEN 0
                                ELSE (
                                    SELECT IFNULL(MAX(DATEDIFF($fechaFinSql, c.fecha_vencimiento)), 0)
                                    FROM tb_prestamo_cuotas c
                                    WHERE c.idprestamo = pr.idprestamo
                                        AND c.fecha_vencimiento <= $fechaFinSql
                                        AND (c.saldo IS NULL OR c.saldo > 0)
                                )
                            END
                        ) as dias_mora_interes
            FROM tb_prestamos pr
            LEFT JOIN tb_prestamo_pagos pp ON pp.idprestamo = pr.idprestamo AND pp.fecha_pago <= $fechaFinSql
            LEFT JOIN tb_prestamo_cuotas pc ON pc.idcuota = pp.idcuota
            GROUP BY pr.idprestamo, pr.numero_coutas, pr.idsolicitud, pr.monto_credito, pr.fecha_credito
            ORDER BY pr.fecha_credito DESC
            LIMIT $per_page OFFSET $offset
        ";
        $query = $this->db->query($sql);
        return $query->result();
    }
    public function get_all()
    {
        // Return unified list of credits from both tb_creditos and tb_prestamos
        $sql = "SELECT c.id as id, c.idcliente as idcliente, NULL as idsolicitud, c.monto_credito, NULL as monto_desembolsado, NULL as interes_credito, NULL as comision_desembolso, NULL as numero_coutas, c.forma_pago, c.fecha_credito, c.estado, NULL as created_at, c.idasesor, NULL as promotor, a.nombres as nombre_asesor, cli.apellidos, cli.nombres as cliente_nombres, cli.telefono as telefonoC, 'credito' as fuente FROM tb_creditos c LEFT JOIN tb_asesores a ON a.idasesor = c.idasesor LEFT JOIN tb_clientes cli ON cli.idcliente = c.idcliente WHERE c.estado != 0 "
            . " UNION ALL "
            . "SELECT pr.idprestamo as id, NULL as idcliente, pr.idsolicitud as idsolicitud, pr.monto_credito, pr.monto_desembolsado, pr.interes_credito, pr.comision_desembolso, pr.numero_coutas, pr.forma_pago, pr.fecha_credito, pr.estado, pr.created_at, pr.idasesor, pr.promotor, a2.nombres as nombre_asesor, s.apellidos, s.nombres as cliente_nombres, s.telefono as telefonoC, 'prestamo' as fuente FROM tb_prestamos pr LEFT JOIN tb_asesores a2 ON a2.idasesor = pr.idasesor LEFT JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud WHERE pr.estado != 0 "
            . " ORDER BY fecha_credito DESC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getAllPrestamosPagados()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->where('tb_creditos.estado', 0);
        $this->db->order_by('fecha_credito', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function get_by_id($prestamo_id = NULL)
    {
        $this->db->select([
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_creditos.id', $prestamo_id);
        return $this->db->get('tb_creditos')->row();
    }

    public function getCoutasCanceladas()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_credito_detalle.estado_couta', 0);
        return $this->db->get('tb_creditos')->result();
    }

    public function getCoutasCanceladasFechas($fechaInicio = NULL, $fechaFin = NUll)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_credito_detalle.estado_couta', 0);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        return $this->db->get('tb_creditos')->result();
    }

    public function getCoutasPaganHoy()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta=', date('Y-m-d'));
        return $this->db->get('tb_creditos')->result();
    }

    public function getCoutasVencidas()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta<', date('Y-m-d'));
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getCoutasVencidasFechas()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta<', date('Y-m-d'));
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }


    public function getPagosCliente($idcliente = NULL, $fechaInicio = NULL, $fechaFin = NUll)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->where('tb_creditos.idcliente', $idcliente);
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getPagosClienteAll($fechaInicio = NULL, $fechaFin = NUll)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getPagosEstado($estado = NULL, $fechaInicio = NULL, $fechaFin = NUll)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', $estado);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->where('tb_credito_detalle.estado_couta', $estado);
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getPagosEstadoAll($fechaInicio = NULL, $fechaFin = NUll)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        //$this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->order_by('DATE(tb_credito_detalle.fecha_couta)', 'ASC');
        return $this->db->get('tb_creditos')->result();
    }


    public function getCoutasPendientes()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>', date('Y-m-d'));
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getCuotasPendientesFechas($fechaInicio, $fechaFin)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getCuotasPaganHoyFechas($fechaInicio, $fechaFin)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_asesores.nombres as nombre_asesor',
            'tb_asesores.telefono as telefonoA',
            'tb_clientes.telefono as telefonoC',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 1);
        $this->db->where('tb_credito_detalle.fecha_couta>=', $fechaInicio);
        $this->db->where('tb_credito_detalle.fecha_couta<=', $fechaFin);
        $this->db->order_by('fecha_couta', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getByDates($fechaInicio = NULL, $fechaFin = NULL, $idcliente = NULL, $idasesor = NULL)
    {
                $sql = "SELECT
                                                c.id as id,
                                                c.monto_credito as monto_credito,
                                                c.fecha_credito as fecha_credito,
                                                c.estado as estado,
                                                c.idasesor as idasesor,
                                                c.numero_coutas as numero_coutas,
                                                c.interes_credito as interes_credito,
                                                c.total_interes as total_interes,
                                                c.total_pagar as total_pagar,
                                                NULL as comision_desembolso,
                                                COALESCE((SELECT SUM(p.monto_pago) FROM tb_pagos p WHERE p.idcredito = c.id),0) as total_pagado,
                                                cl.idcliente as idcliente,
                                                cl.apellidos as apellidos,
                                                cl.nombres as nombres,
                                                cl.telefono as telefonoC,
                                                a.nombres as nombre_asesor,
                                                ROUND(c.monto_credito,2) as monto_dolares,
                                                ROUND(c.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc WHERE tc.fecha <= c.fecha_credito ORDER BY tc.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
                                                ROUND(c.interes_credito * 100,0) as interes_porcent,
                                                -- cuotas pagadas / pendientes basadas en pagos (tb_pagos + tb_pagos_detalle)
                                                COALESCE((
                                                    (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                    +
                                                    (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                ),0) as CuotasPagadas,
                                                GREATEST(c.numero_coutas - COALESCE((
                                                    (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                    +
                                                    (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                ),0),0) as CuotasPendientes,
                                                CASE
                                                    WHEN (
                                                        COALESCE((
                                                            (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                            +
                                                            (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                        ),0)
                                                    ) >= c.numero_coutas THEN 'PAGADO'
                                                    WHEN EXISTS(SELECT 1 FROM tb_credito_detalle cd2 WHERE cd2.idcredito = c.id AND cd2.fecha_couta < CURDATE() AND cd2.estado_couta = 1) THEN 'EN MORA'
                                                    ELSE 'VIGENTE'
                                                END as estado_calculado
                                 FROM tb_creditos c
                                 JOIN tb_clientes cl ON cl.idcliente = c.idcliente
                                 LEFT JOIN tb_asesores a ON a.idasesor = c.idasesor
                                 WHERE c.fecha_credito >= ?
                                     AND c.fecha_credito <= ?
                                     AND c.idcliente = ?
                                     AND c.idasesor = ?

                                 UNION ALL

                                 SELECT
                                                pr.idprestamo as id,
                                                pr.monto_credito as monto_credito,
                                                COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) as fecha_credito,
                                                pr.estado as estado,
                                                pr.idasesor as idasesor,
                                                pr.numero_coutas as numero_coutas,
                                                pr.interes_credito as interes_credito,
                                                COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) as total_interes,
                                                ROUND(pr.monto_credito + COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) + (pr.monto_credito * COALESCE(pr.comision_desembolso,0) / 100),2) as total_pagar,
                                                pr.comision_desembolso as comision_desembolso,
                                                COALESCE((SELECT SUM(pp.monto_pagado) FROM tb_prestamo_pagos pp WHERE pp.idprestamo = pr.idprestamo),0) as total_pagado,
                                                cl2.idcliente as idcliente,
                                                cl2.apellidos as apellidos,
                                                cl2.nombres as nombres,
                                                cl2.telefono as telefonoC,
                                                COALESCE(a2.nombres, s.nombre_promotor, s.promotor) as nombre_asesor,
                                                ROUND(pr.monto_credito,2) as monto_dolares,
                                                ROUND(pr.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc2 WHERE tc2.fecha <= COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) ORDER BY tc2.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
                                                ROUND(pr.interes_credito * 100,0) as interes_porcent,
                                                -- cuotas pagadas / pendientes basadas en pagos de prestamo (tb_prestamo_pagos)
                                                (SELECT COUNT(DISTINCT pp.idcuota) FROM tb_prestamo_pagos pp WHERE pp.idprestamo = pr.idprestamo AND pp.idcuota IS NOT NULL) as CuotasPagadas,
                                                GREATEST(pr.numero_coutas - (SELECT COUNT(DISTINCT pp2.idcuota) FROM tb_prestamo_pagos pp2 WHERE pp2.idprestamo = pr.idprestamo AND pp2.idcuota IS NOT NULL),0) as CuotasPendientes,
                                                CASE
                                                    WHEN (SELECT COUNT(*) FROM tb_prestamo_cuotas pc2 WHERE pc2.idprestamo = pr.idprestamo AND (pc2.saldo IS NULL OR pc2.saldo <= 0)) >= pr.numero_coutas THEN 'PAGADO'
                                                    WHEN EXISTS(SELECT 1 FROM tb_prestamo_cuotas pc3 WHERE pc3.idprestamo = pr.idprestamo AND pc3.fecha_vencimiento < CURDATE() AND (pc3.saldo IS NULL OR pc3.saldo > 0)) THEN 'EN MORA'
                                                    ELSE 'VIGENTE'
                                                END as estado_calculado
                                 FROM tb_prestamos pr
                                 JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                                 JOIN tb_clientes cl2 ON cl2.idcliente = s.idcliente
                                 LEFT JOIN tb_asesores a2 ON a2.idasesor = pr.idasesor
                                 WHERE (
                                             (pr.fecha_credito >= ? AND pr.fecha_credito <= ?)
                                             OR (DATE(pr.created_at) >= ? AND DATE(pr.created_at) <= ?)
                                             )
                                         AND cl2.idcliente = ?
                                     AND pr.idasesor = ?
                                 ORDER BY fecha_credito DESC";

                $params = [$fechaInicio, $fechaFin, $idcliente, $idasesor, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $idcliente, $idasesor];
                return $this->db->query($sql, $params)->result();
    }

    public function getByDatesNoAsesor($fechaInicio = NULL, $fechaFin = NULL, $idcliente = NULL)
    {
                $sql = "SELECT
                                                c.id as id,
                                                c.monto_credito as monto_credito,
                                                c.fecha_credito as fecha_credito,
                                                c.estado as estado,
                                                c.idasesor as idasesor,
                                                c.numero_coutas as numero_coutas,
                                                c.interes_credito as interes_credito,
                                                c.total_interes as total_interes,
                                                c.total_pagar as total_pagar,
                                                NULL as comision_desembolso,
                                                COALESCE((SELECT SUM(p.monto_pago) FROM tb_pagos p WHERE p.idcredito = c.id),0) as total_pagado,
                                                cl.idcliente as idcliente,
                                                cl.apellidos as apellidos,
                                                cl.nombres as nombres,
                                                cl.telefono as telefonoC,
                                                a.nombres as nombre_asesor,
                                                ROUND(c.monto_credito,2) as monto_dolares,
                                                ROUND(c.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc WHERE tc.fecha <= c.fecha_credito ORDER BY tc.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
                                                ROUND(c.interes_credito * 100,0) as interes_porcent,
                                                -- cuotas pagadas / pendientes basadas en pagos (tb_pagos + tb_pagos_detalle)
                                                COALESCE((
                                                    (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                    +
                                                    (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                ),0) as CuotasPagadas,
                                                GREATEST(c.numero_coutas - COALESCE((
                                                    (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                    +
                                                    (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                ),0),0) as CuotasPendientes,
                                                CASE
                                                    WHEN (
                                                        COALESCE((
                                                            (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                                                            +
                                                            (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                                                        ),0)
                                                    ) >= c.numero_coutas THEN 'PAGADO'
                                                    WHEN EXISTS(SELECT 1 FROM tb_credito_detalle cd2 WHERE cd2.idcredito = c.id AND cd2.fecha_couta < CURDATE() AND cd2.estado_couta = 1) THEN 'EN MORA'
                                                    ELSE 'VIGENTE'
                                                END as estado_calculado
                                 FROM tb_creditos c
                                 JOIN tb_clientes cl ON cl.idcliente = c.idcliente
                                 LEFT JOIN tb_asesores a ON a.idasesor = c.idasesor
                                 WHERE c.fecha_credito >= ?
                                     AND c.fecha_credito <= ?
                                     AND c.idcliente = ?

                                 UNION ALL

                                 SELECT
                                                pr.idprestamo as id,
                                                pr.monto_credito as monto_credito,
                                                COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) as fecha_credito,
                                                pr.estado as estado,
                                                pr.idasesor as idasesor,
                                                pr.numero_coutas as numero_coutas,
                                                pr.interes_credito as interes_credito,
                                                COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) as total_interes,
                                                ROUND(pr.monto_credito + COALESCE((SELECT SUM(pc.interes) FROM tb_prestamo_cuotas pc WHERE pc.idprestamo = pr.idprestamo),0) + (pr.monto_credito * COALESCE(pr.comision_desembolso,0) / 100),2) as total_pagar,
                                                pr.comision_desembolso as comision_desembolso,
                                                COALESCE((SELECT SUM(pp.monto_pagado) FROM tb_prestamo_pagos pp WHERE pp.idprestamo = pr.idprestamo),0) as total_pagado,
                                                cl2.idcliente as idcliente,
                                                cl2.apellidos as apellidos,
                                                cl2.nombres as nombres,
                                                cl2.telefono as telefonoC,
                                                COALESCE(a2.nombres, s.nombre_promotor, s.promotor) as nombre_asesor,
                                                ROUND(pr.monto_credito,2) as monto_dolares,
                                                ROUND(pr.monto_credito * COALESCE((SELECT tasa_cambio FROM tb_tasa_cambio tc2 WHERE tc2.fecha <= COALESCE(pr.primer_dia_pago, pr.fecha_desembolso, pr.fecha_credito) ORDER BY tc2.fecha DESC LIMIT 1),(SELECT tasa_cambio FROM tb_tasa_cambio ORDER BY fecha DESC LIMIT 1)),2) as monto_cordobas,
                                                ROUND(pr.interes_credito * 100,0) as interes_porcent,
                                                -- cuotas pagadas / pendientes basadas en pagos de prestamo (tb_prestamo_pagos)
                                                (SELECT COUNT(DISTINCT pp3.idcuota) FROM tb_prestamo_pagos pp3 WHERE pp3.idprestamo = pr.idprestamo AND pp3.idcuota IS NOT NULL) as CuotasPagadas,
                                                GREATEST(pr.numero_coutas - (SELECT COUNT(DISTINCT pp4.idcuota) FROM tb_prestamo_pagos pp4 WHERE pp4.idprestamo = pr.idprestamo AND pp4.idcuota IS NOT NULL),0) as CuotasPendientes,
                                                CASE
                                                    WHEN (SELECT COUNT(*) FROM tb_prestamo_cuotas pc2 WHERE pc2.idprestamo = pr.idprestamo AND (pc2.saldo IS NULL OR pc2.saldo <= 0)) >= pr.numero_coutas THEN 'PAGADO'
                                                    WHEN EXISTS(SELECT 1 FROM tb_prestamo_cuotas pc3 WHERE pc3.idprestamo = pr.idprestamo AND pc3.fecha_vencimiento < CURDATE() AND (pc3.saldo IS NULL OR pc3.saldo > 0)) THEN 'EN MORA'
                                                    ELSE 'VIGENTE'
                                                END as estado_calculado
                                 FROM tb_prestamos pr
                                 JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                                 JOIN tb_clientes cl2 ON cl2.idcliente = s.idcliente
                                 LEFT JOIN tb_asesores a2 ON a2.idasesor = pr.idasesor
                                 WHERE pr.fecha_credito >= ?
                                     AND pr.fecha_credito <= ?
                                     AND cl2.idcliente = ?
                                 ORDER BY fecha_credito DESC";

                $params = [$fechaInicio, $fechaFin, $idcliente, $fechaInicio, $fechaFin, $idcliente];
                return $this->db->query($sql, $params)->result();
    }

    public function getByDatesAsesorEstado($fechaInicio = NULL, $fechaFin = NULl, $idasesor = NULL, $estado = NULL)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.telefono',
            'tb_clientes.nombres',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_creditos.fecha_credito>=', $fechaInicio);
        $this->db->where('tb_creditos.fecha_credito<=', $fechaFin);
        $this->db->where('tb_creditos.idasesor', $idasesor);
        $this->db->where('tb_creditos.estado', $estado);
        return $this->db->get('tb_creditos')->result();
    }

    public function getByDatesEstado($fechaInicio = NULL, $fechaFin = NULl, $idasesor = NULL)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.telefono',
            'tb_clientes.nombres',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_creditos.fecha_credito>=', $fechaInicio);
        $this->db->where('tb_creditos.fecha_credito<=', $fechaFin);
        $this->db->where('tb_creditos.estado', $estado);
        return $this->db->get('tb_creditos')->result();
    }

    public function getByDatesAsesorTodos($fechaInicio = NULL, $fechaFin = NULl, $idasesor = NULL)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.telefono',
            'tb_clientes.nombres',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_creditos.fecha_credito>=', $fechaInicio);
        $this->db->where('tb_creditos.fecha_credito<=', $fechaFin);
        $this->db->where('tb_creditos.idasesor', $idasesor);
        return $this->db->get('tb_creditos')->result();
    }

    public function getByDatesTodos($fechaInicio = NULL, $fechaFin = NULl)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.telefono',
            'tb_clientes.nombres',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_creditos.fecha_credito>=', $fechaInicio);
        $this->db->where('tb_creditos.fecha_credito<=', $fechaFin);

        return $this->db->get('tb_creditos')->result();
    }

    public function getContarCuotasPendientes($idcredito)
    {
        $sql = "SELECT GREATEST(c.numero_coutas - COALESCE((
                    (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = c.id AND p.idcuota IS NOT NULL)
                    +
                    (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = c.id AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = c.id))
                ),0),0) as CuotasPendientes
                FROM tb_creditos c
                WHERE c.id = ?";
        $query = $this->db->query($sql, [$idcredito]);
        return $query->row();
    }

    public function getContarCuotasPagadas($idcredito)
    {
        $sql = "SELECT COALESCE((
                        (SELECT COUNT(DISTINCT p.idcuota) FROM tb_pagos p WHERE p.idcredito = ? AND p.idcuota IS NOT NULL)
                        +
                        (SELECT COUNT(DISTINCT pd.idcuota) FROM tb_pagos_detalle pd JOIN tb_pagos p2 ON p2.idpago = pd.idpago WHERE p2.idcredito = ? AND pd.idcuota IS NOT NULL AND pd.idcuota NOT IN (SELECT p3.idcuota FROM tb_pagos p3 WHERE p3.idcredito = ?))
                    ),0) as CuotasPagadas";
        $query = $this->db->query($sql, [$idcredito, $idcredito, $idcredito]);
        return $query->row();
    }

    public function getCreditoByCliente($cliente_id = NULL)
    {
        $sql = "SELECT c.id as id,
                cli.idcliente,
                cli.apellidos,
                cli.nombres,
                c.monto_credito,
                c.interes_credito,
                c.numero_coutas,
                NULL as total_interes,
                NULL as total_pagar,
                c.fecha_credito,
                c.estado,
                'credito' as fuente
            FROM tb_creditos c
            JOIN tb_clientes cli ON cli.idcliente = c.idcliente
            WHERE c.idcliente = ?
                AND c.estado != 0

                UNION ALL

                SELECT pr.idprestamo as id,
                        s.idcliente,
                        s.apellidos,
                        s.nombres,
                        pr.monto_credito,
                        pr.interes_credito,
                        pr.numero_coutas,
                        NULL as total_interes,
                        NULL as total_pagar,
                        pr.fecha_credito,
                        pr.estado,
                        'prestamo' as fuente
                FROM tb_prestamos pr
                JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                WHERE s.idcliente = ?
                    AND pr.estado != 0

                ORDER BY fecha_credito DESC";

        $params = [$cliente_id, $cliente_id];
        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    public function getPagosAsesor($idasesor = NULL, $fechaInicio = NULL, $fechaFin = NULl)
    {
                $sql = "SELECT p.idprestamo as idcredito,
                                                p.id as idpago,
                                                DATE_FORMAT(p.fecha_pago,'%Y-%m-%d') as fecha_pago,
                                                p.monto_pagado as monto_pago,
                                                COALESCE(p.descuento_pago,0) as descuento_pago,
                                                p.monto_pagado as monto_pago_2,
                                                p.monto_pagado as monto_pago_3,
                                                c.idcliente,
                                                c.apellidos,
                                                c.nombres,
                                                a.nombres as nombre_asesor
                                 FROM tb_prestamo_pagos p
                                 JOIN tb_prestamos pr ON pr.idprestamo = p.idprestamo
                                 JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                                 JOIN tb_clientes c ON c.idcliente = s.idcliente
                                 LEFT JOIN tb_asesores a ON a.idasesor = pr.idasesor
                                 WHERE pr.idasesor = ?
                                     AND DATE(p.fecha_pago) >= ?
                                     AND DATE(p.fecha_pago) <= ?
                 
                                 UNION ALL

                                 SELECT tb_pagos.idcredito,
                                                tb_pagos.idpago,
                                                DATE_FORMAT(tb_pagos.fecha_pago,'%Y-%m-%d') as fecha_pago,
                                                tb_pagos.monto_pago,
                                                tb_pagos.descuento_pago,
                                                tb_pagos.monto_pago,
                                                tb_pagos.monto_pago,
                                                tb_clientes.idcliente,
                                                tb_clientes.apellidos,
                                                tb_clientes.nombres,
                                                tb_asesores.nombres as nombre_asesor
                                 FROM tb_pagos
                                 JOIN tb_creditos ON tb_creditos.id=tb_pagos.idcredito
                                 JOIN tb_clientes ON tb_clientes.idcliente=tb_creditos.idcliente
                                 JOIN tb_asesores ON tb_asesores.idasesor=tb_creditos.idasesor
                                 WHERE tb_creditos.idasesor = ?
                                     AND DATE(tb_pagos.fecha_pago) >= ?
                                     AND DATE(tb_pagos.fecha_pago) <= ?
                                 ORDER BY fecha_pago DESC";

                $params = [$idasesor, $fechaInicio, $fechaFin, $idasesor, $fechaInicio, $fechaFin];
                return $this->db->query($sql, $params)->result();
    }

    public function getPagosAllAsesor($fechaInicio = NULL, $fechaFin = NULl)
    {
                $sql = "SELECT p.idprestamo as idcredito,
                                                p.id as idpago,
                                                p.fecha_pago,
                                                p.monto_pagado as monto_pago,
                                                COALESCE(p.descuento_pago,0) as descuento_pago,
                                                p.monto_pagado as monto_pago_2,
                                                p.monto_pagado as monto_pago_3,
                                                c.idcliente,
                                                c.apellidos,
                                                c.nombres,
                                                a.nombres as nombre_asesor
                                 FROM tb_prestamo_pagos p
                                 JOIN tb_prestamos pr ON pr.idprestamo = p.idprestamo
                                 JOIN tb_solicitudes s ON s.idsolicitud = pr.idsolicitud
                                 JOIN tb_clientes c ON c.idcliente = s.idcliente
                                 LEFT JOIN tb_asesores a ON a.idasesor = pr.idasesor
                                 WHERE DATE(p.fecha_pago) >= ?
                                     AND DATE(p.fecha_pago) <= ?

                                 UNION ALL

                                 SELECT tb_pagos.idcredito,
                                                tb_pagos.idpago,
                                                tb_pagos.fecha_pago,
                                                tb_pagos.monto_pago,
                                                tb_pagos.descuento_pago,
                                                tb_pagos.monto_pago,
                                                tb_pagos.monto_pago,
                                                tb_clientes.idcliente,
                                                tb_clientes.apellidos,
                                                tb_clientes.nombres,
                                                tb_asesores.nombres as nombre_asesor
                                 FROM tb_pagos
                                 JOIN tb_creditos ON tb_creditos.id=tb_pagos.idcredito
                                 JOIN tb_clientes ON tb_clientes.idcliente=tb_creditos.idcliente
                                 JOIN tb_asesores ON tb_asesores.idasesor=tb_creditos.idasesor
                                 WHERE DATE(tb_pagos.fecha_pago) >= ?
                                     AND DATE(tb_pagos.fecha_pago) <= ?
                                 ORDER BY fecha_pago DESC";

                $params = [$fechaInicio, $fechaFin, $fechaInicio, $fechaFin];
                return $this->db->query($sql, $params)->result();
    }

    public function getCreditoByAsesor($idasesor = NULL)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_asesores.nombres as nombre_asesor',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->where('tb_creditos.idasesor', $idasesor);
        $this->db->where('tb_creditos.estado!=', 0);
        return $this->db->get('tb_creditos')->result();
    }

    public function getCreditosByAll()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_asesores.nombres as nombre_asesor',
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');

        $this->db->where('tb_creditos.estado!=', 0);
        return $this->db->get('tb_creditos')->result();
    }

    public function get_all_by_id($prestamo_id = NULL)
    {
        $this->db->select([
            'tb_credito_detalle.*'
        ]);
        $this->db->where('tb_credito_detalle.idcredito', $prestamo_id);
        return $this->db->get('tb_credito_detalle')->result();
    }

    public function getCoutasPagadas()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_credito_detalle.estado_couta', 0);
        $this->db->order_by('fecha_pago', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getAllCoutas()
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->order_by('fecha_pago', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getAllCuotasCliente($id)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_creditos.idcliente', $id);
        $this->db->where('tb_creditos.estado<>', 0);
        $this->db->order_by('fecha_pago', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }

    public function getAllCuotasClienteFechaPago($idcliente, $fechaPago)
    {
        $this->db->select([
            'tb_creditos.*',
            'tb_asesores.nombres as nombre_asesor',
            'tb_clientes.idcliente',
            'tb_clientes.apellidos',
            'tb_clientes.nombres',
            'tb_credito_detalle.*'
        ]);
        $this->db->join('tb_clientes', 'tb_clientes.idcliente=tb_creditos.idcliente');
        $this->db->join('tb_asesores', 'tb_asesores.idasesor=tb_creditos.idasesor');
        $this->db->join('tb_credito_detalle', 'tb_credito_detalle.idcredito=tb_creditos.id');
        $this->db->where('tb_creditos.idcliente', $idcliente);
        $this->db->where('tb_credito_detalle.fecha_couta', $fechaPago);
        $this->db->where('tb_creditos.estado<>', 0);
        $this->db->order_by('fecha_pago', 'DESC');
        return $this->db->get('tb_creditos')->result();
    }
}
