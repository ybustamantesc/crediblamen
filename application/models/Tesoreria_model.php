<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tesoreria_model extends CI_Model {
        // Asegurar campo contabilizado en teso_movimientos
        public function ensure_contabilizado_column() {
            if (!$this->db->field_exists('contabilizado', 'teso_movimientos')) {
                $sql = "ALTER TABLE teso_movimientos ADD COLUMN contabilizado TINYINT(1) NOT NULL DEFAULT 0";
                try { $this->db->query($sql); } catch (Exception $e) { return false; }
            }
            return true;
        }
    public function __construct()
    {
        parent::__construct();
    }

    public function save_movimiento($payload)
    {
        $data = array(
            'cuenta_id' => isset($payload['cuenta_id']) ? $payload['cuenta_id'] : NULL,
            'tipo' => isset($payload['tipo']) ? $payload['tipo'] : NULL, // ingreso/egreso/transferencia
            'monto' => isset($payload['monto']) ? $payload['monto'] : 0,
            'moneda' => isset($payload['moneda']) ? $payload['moneda'] : 'PEN',
            'descripcion' => isset($payload['descripcion']) ? $payload['descripcion'] : NULL,
            'fecha' => isset($payload['fecha']) ? $payload['fecha'] : date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('teso_movimientos', $data);
        return $this->db->insert_id();
    }

    public function save_pago($payload)
    {
        $data = array(
            'proveedor_id' => isset($payload['proveedor_id']) ? $payload['proveedor_id'] : NULL,
            'cuenta_id' => isset($payload['cuenta_id']) ? $payload['cuenta_id'] : NULL,
            'monto' => isset($payload['monto']) ? $payload['monto'] : 0,
            'fecha_programada' => isset($payload['fecha_programada']) ? $payload['fecha_programada'] : NULL,
            'estado' => 'programado',
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('teso_pagos', $data);
        return $this->db->insert_id();
    }

    public function save_arqueo($payload)
    {
        $data = array(
            'caja_id' => isset($payload['caja_id']) ? $payload['caja_id'] : NULL,
            'apertura' => isset($payload['apertura']) ? $payload['apertura'] : NULL,
            'cierre' => isset($payload['cierre']) ? $payload['cierre'] : NULL,
            'observaciones' => isset($payload['observaciones']) ? $payload['observaciones'] : NULL,
            'faltantes' => isset($payload['faltantes']) ? $payload['faltantes'] : 0,
            'sobrantes' => isset($payload['sobrantes']) ? $payload['sobrantes'] : 0,
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('teso_arqueos', $data);
        return $this->db->insert_id();
    }

    public function save_flujo($payload)
    {
        // Ensure the teso_flujo table exists (some installations may not have run DB scripts)
        if (!$this->db->table_exists('teso_flujo')) {
            $sql = "CREATE TABLE IF NOT EXISTS teso_flujo (
              id BIGINT AUTO_INCREMENT PRIMARY KEY,
              fecha DATE NOT NULL,
              cuenta_id INT NOT NULL,
              concepto VARCHAR(200) NOT NULL,
              tipo ENUM('ingreso','egreso') NOT NULL,
              proyectado DECIMAL(18,2) NOT NULL DEFAULT 0.00,
              realizado DECIMAL(18,2) NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NULL
            ) ENGINE=InnoDB;";
            try {
                $this->db->query($sql);
            } catch (Exception $e) {
                log_message('error', 'Tesoreria_model->save_flujo: failed to create teso_flujo table: ' . $e->getMessage());
                return false;
            }
        }
        $data = array(
            'fecha' => isset($payload['fecha']) ? $payload['fecha'] : date('Y-m-d'),
            'cuenta_id' => isset($payload['cuenta_id']) ? $payload['cuenta_id'] : 1,
            'concepto' => isset($payload['concepto']) ? $payload['concepto'] : NULL,
            'tipo' => isset($payload['tipo']) ? $payload['tipo'] : 'ingreso',
            'proyectado' => isset($payload['proyectado']) ? $payload['proyectado'] : 0,
            'realizado' => isset($payload['realizado']) ? $payload['realizado'] : NULL,
            'created_at' => date('Y-m-d H:i:s')
        );
        try {
            $this->db->insert('teso_flujo', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Tesoreria_model->save_flujo insert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener pagos (opcionalmente filtrados)
     */
    public function get_pagos($filters = array())
    {
        $this->db->from('teso_pagos');
        if (!empty($filters['date_from'])) $this->db->where('fecha >=', $filters['date_from']);
        if (!empty($filters['date_to'])) $this->db->where('fecha <=', $filters['date_to']);
        if (!empty($filters['estado'])) $this->db->where('estado', $filters['estado']);
        $this->db->order_by('fecha', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Pagos pendientes/provisionales para revisión en Tesorería.
     */
    public function get_pagos_pendientes($filters = array())
    {
        if (!$this->db->table_exists('teso_pagos')) {
            return array();
        }

        $hasIdPrestamo = $this->db->field_exists('idprestamo', 'teso_pagos');
        $hasConcepto = $this->db->field_exists('concepto', 'teso_pagos');
        $hasDescripcion = $this->db->field_exists('descripcion', 'teso_pagos');
        $hasEstado = $this->db->field_exists('estado', 'teso_pagos');
        $hasFecha = $this->db->field_exists('fecha', 'teso_pagos');
        $hasCreatedAt = $this->db->field_exists('created_at', 'teso_pagos');
        $hasIdSerie = $this->db->field_exists('idserie', 'teso_pagos');
        $hasSerieCodigo = $this->db->field_exists('serie_codigo', 'teso_pagos');
        $hasBeneficiario = $this->db->field_exists('beneficiario', 'teso_pagos');
        $hasDocumentoNumero = $this->db->field_exists('documento_numero', 'teso_pagos');
        $hasDocumentoTipo = $this->db->field_exists('documento_tipo', 'teso_pagos');

        $this->db->from('teso_pagos');

        // Enfocar en provisionales de créditos
        if ($hasIdPrestamo) {
            $this->db->where('idprestamo >', 0);
        } elseif ($hasConcepto) {
            $this->db->like('concepto', 'Pago provisional préstamo');
        } elseif ($hasDescripcion) {
            $this->db->like('descripcion', 'Pago provisional préstamo');
        } elseif ($hasDocumentoTipo) {
            $this->db->where('documento_tipo', 'RECIBO');
        }

        if ($hasEstado) {
            $this->db->group_start();
            $this->db->where_in('estado', array('registrado', 'programado', 'pendiente'));
            $this->db->group_end();
        }

        if ($hasFecha) {
            if (!empty($filters['date_from'])) $this->db->where('fecha >=', $filters['date_from']);
            if (!empty($filters['date_to'])) $this->db->where('fecha <=', $filters['date_to']);
            if (empty($filters['date_from']) && empty($filters['date_to']) && !empty($filters['fecha'])) {
                $this->db->where('fecha', $filters['fecha']);
            }
        }

        if (!empty($filters['idserie'])) {
            $idserieFiltro = intval($filters['idserie']);
            if ($idserieFiltro > 0) {
                if ($hasIdSerie) {
                    $this->db->where('idserie', $idserieFiltro);
                } elseif ($hasSerieCodigo && !empty($filters['serie_codigo'])) {
                    $this->db->where('UPPER(serie_codigo)', strtoupper(trim((string)$filters['serie_codigo'])));
                }
            }
        }

        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $hasAnySearchField = false;
            if ($hasBeneficiario || $hasConcepto || $hasDescripcion || $hasDocumentoNumero) {
                $this->db->group_start();
                if ($hasBeneficiario) {
                    $this->db->like('beneficiario', $q);
                    $hasAnySearchField = true;
                }
                if ($hasConcepto) {
                    if ($hasAnySearchField) {
                        $this->db->or_like('concepto', $q);
                    } else {
                        $this->db->like('concepto', $q);
                        $hasAnySearchField = true;
                    }
                }
                if ($hasDescripcion) {
                    if ($hasAnySearchField) {
                        $this->db->or_like('descripcion', $q);
                    } else {
                        $this->db->like('descripcion', $q);
                        $hasAnySearchField = true;
                    }
                }
                if ($hasDocumentoNumero) {
                    if ($hasAnySearchField) {
                        $this->db->or_like('documento_numero', $q);
                    } else {
                        $this->db->like('documento_numero', $q);
                    }
                }
                $this->db->group_end();
            }
        }

        if ($hasCreatedAt) {
            $this->db->order_by('created_at', 'DESC');
        } elseif ($hasFecha) {
            $this->db->order_by('fecha', 'DESC');
        } else {
            $this->db->order_by('id', 'DESC');
        }
        $rows = $this->db->get()->result();

        // En distintos entornos los nombres de columnas cambian; normalizamos para la vista.
        $usuarioIds = array();
        foreach ($rows as $r) {
            $uid = 0;
            if (isset($r->usuario_id) && intval($r->usuario_id) > 0) $uid = intval($r->usuario_id);
            else if (isset($r->idusuario) && intval($r->idusuario) > 0) $uid = intval($r->idusuario);
            else if (isset($r->user_id) && intval($r->user_id) > 0) $uid = intval($r->user_id);
            if ($uid > 0) $usuarioIds[$uid] = $uid;
        }

        $usuariosMap = array();
        if (!empty($usuarioIds) && $this->db->table_exists('users')) {
            $users = $this->db->where_in('id', array_values($usuarioIds))->get('users')->result();
            foreach ($users as $u) {
                $nombre = trim(((isset($u->first_name) ? $u->first_name : '') . ' ' . (isset($u->last_name) ? $u->last_name : '')));
                if ($nombre === '' && isset($u->username)) $nombre = (string)$u->username;
                if ($nombre === '' && isset($u->email)) $nombre = (string)$u->email;
                $usuariosMap[intval($u->id)] = $nombre !== '' ? $nombre : ('Usuario #' . intval($u->id));
            }
        }

        $clientesMap = array();
        $clienteIds = array();
        foreach ($rows as $r) {
            if (isset($r->idcliente) && intval($r->idcliente) > 0) {
                $clienteIds[intval($r->idcliente)] = intval($r->idcliente);
            }
            if (isset($r->proveedor_id) && intval($r->proveedor_id) > 0) {
                $clienteIds[intval($r->proveedor_id)] = intval($r->proveedor_id);
            }
        }
        if (!empty($clienteIds) && $this->db->table_exists('tb_clientes')) {
            $clientes = $this->db->where_in('idcliente', array_values($clienteIds))->get('tb_clientes')->result();
            foreach ($clientes as $c) {
                $nom = trim(((isset($c->apellidos) ? $c->apellidos : '') . ' ' . (isset($c->nombres) ? $c->nombres : '')));
                if ($nom === '' && isset($c->razon_social)) $nom = (string)$c->razon_social;
                $clientesMap[intval($c->idcliente)] = $nom !== '' ? $nom : ('Cliente #' . intval($c->idcliente));
            }
        }

        foreach ($rows as &$r) {
            $clientIdResolved = 0;
            if (isset($r->idcliente) && intval($r->idcliente) > 0) {
                $clientIdResolved = intval($r->idcliente);
            } else if (isset($r->proveedor_id) && intval($r->proveedor_id) > 0) {
                $clientIdResolved = intval($r->proveedor_id);
            }
            if ($clientIdResolved <= 0 && isset($r->idprestamo) && intval($r->idprestamo) > 0 && $this->db->table_exists('tb_prestamos')) {
                if ($this->db->field_exists('idcliente', 'tb_prestamos')) {
                    $pr = $this->db->select('idcliente')->from('tb_prestamos')->where('idprestamo', intval($r->idprestamo))->limit(1)->get()->row();
                    if ($pr && isset($pr->idcliente) && intval($pr->idcliente) > 0) {
                        $clientIdResolved = intval($pr->idcliente);
                    }
                } elseif ($this->db->field_exists('idsolicitud', 'tb_prestamos') && $this->db->table_exists('tb_solicitudes') && $this->db->field_exists('idcliente', 'tb_solicitudes')) {
                    $pr = $this->db->select('s.idcliente')
                        ->from('tb_prestamos pr')
                        ->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left')
                        ->where('pr.idprestamo', intval($r->idprestamo))
                        ->limit(1)
                        ->get()
                        ->row();
                    if ($pr && isset($pr->idcliente) && intval($pr->idcliente) > 0) {
                        $clientIdResolved = intval($pr->idcliente);
                    }
                }
            }

            $resolvedLink = null;
            $currentPrestamo = isset($r->idprestamo) ? intval($r->idprestamo) : 0;
            $currentCuota = isset($r->idcuota) ? intval($r->idcuota) : 0;
            if ($currentPrestamo <= 0 || $currentCuota <= 0 || $clientIdResolved <= 0) {
                $resolvedLink = $this->resolve_provisional_match($r);
                if ($resolvedLink) {
                    if ($currentPrestamo <= 0) $r->idprestamo = $resolvedLink['idprestamo'];
                    if ($currentCuota <= 0) $r->idcuota = $resolvedLink['idcuota'];
                    if ($clientIdResolved <= 0 && !empty($resolvedLink['idcliente'])) {
                        $clientIdResolved = intval($resolvedLink['idcliente']);
                    }
                }
            }

            $beneficiario = '';
            if (isset($r->beneficiario) && trim((string)$r->beneficiario) !== '') $beneficiario = trim((string)$r->beneficiario);
            else if (isset($r->nombre_beneficiario) && trim((string)$r->nombre_beneficiario) !== '') $beneficiario = trim((string)$r->nombre_beneficiario);
            else if (isset($r->beneficiary) && trim((string)$r->beneficiary) !== '') $beneficiario = trim((string)$r->beneficiary);
            else if (isset($r->proveedor_nombre) && trim((string)$r->proveedor_nombre) !== '') $beneficiario = trim((string)$r->proveedor_nombre);
            else if ($clientIdResolved > 0 && isset($clientesMap[$clientIdResolved])) $beneficiario = $clientesMap[$clientIdResolved];

            $concepto = '';
            if (isset($r->concepto) && trim((string)$r->concepto) !== '') $concepto = trim((string)$r->concepto);
            else if (isset($r->descripcion) && trim((string)$r->descripcion) !== '') $concepto = trim((string)$r->descripcion);
            else if (isset($r->detalle) && trim((string)$r->detalle) !== '') $concepto = trim((string)$r->detalle);
            else {
                $tmpPrestamo = isset($r->idprestamo) ? intval($r->idprestamo) : 0;
                $tmpCuota = isset($r->idcuota) ? intval($r->idcuota) : 0;
                if ($tmpPrestamo > 0) {
                    $concepto = 'Pago provisional prestamo #' . $tmpPrestamo . ($tmpCuota > 0 ? (' cuota #' . $tmpCuota) : '');
                }
            }

            $metodo = '';
            if (isset($r->medio_pago) && trim((string)$r->medio_pago) !== '') $metodo = trim((string)$r->medio_pago);
            else if (isset($r->metodo_pago) && trim((string)$r->metodo_pago) !== '') $metodo = trim((string)$r->metodo_pago);
            else if (isset($r->metodo) && trim((string)$r->metodo) !== '') $metodo = trim((string)$r->metodo);
            else if (isset($r->forma_pago) && trim((string)$r->forma_pago) !== '') $metodo = trim((string)$r->forma_pago);

            $moneda = '';
            if (isset($r->moneda) && trim((string)$r->moneda) !== '') $moneda = strtoupper(trim((string)$r->moneda));
            else if (isset($r->currency) && trim((string)$r->currency) !== '') $moneda = strtoupper(trim((string)$r->currency));
            if ($moneda === '') $moneda = 'USD';

            $ref = '';
            if (isset($r->documento_numero) && trim((string)$r->documento_numero) !== '') $ref = trim((string)$r->documento_numero);
            else if (isset($r->numero_documento) && trim((string)$r->numero_documento) !== '') $ref = trim((string)$r->numero_documento);
            else if (isset($r->referencia) && trim((string)$r->referencia) !== '') $ref = trim((string)$r->referencia);
            else if (isset($r->doc_numero) && trim((string)$r->doc_numero) !== '') $ref = trim((string)$r->doc_numero);

            $fechaNorm = '';
            if (isset($r->fecha) && trim((string)$r->fecha) !== '') $fechaNorm = substr((string)$r->fecha, 0, 10);
            else if (isset($r->fecha_pago) && trim((string)$r->fecha_pago) !== '') $fechaNorm = substr((string)$r->fecha_pago, 0, 10);
            else if (isset($r->fecha_programada) && trim((string)$r->fecha_programada) !== '') $fechaNorm = substr((string)$r->fecha_programada, 0, 10);
            else if (isset($r->created_at) && trim((string)$r->created_at) !== '') $fechaNorm = substr((string)$r->created_at, 0, 10);

            $uid = 0;
            if (isset($r->usuario_id) && intval($r->usuario_id) > 0) $uid = intval($r->usuario_id);
            else if (isset($r->idusuario) && intval($r->idusuario) > 0) $uid = intval($r->idusuario);
            else if (isset($r->user_id) && intval($r->user_id) > 0) $uid = intval($r->user_id);

            $registradoPor = '-';
            if ($uid > 0 && isset($usuariosMap[$uid])) {
                $registradoPor = $usuariosMap[$uid];
            } else if (isset($r->creado_por) && trim((string)$r->creado_por) !== '') {
                $registradoPor = trim((string)$r->creado_por);
            }

            $r->beneficiario = $beneficiario !== '' ? $beneficiario : '-';
            $r->concepto = $concepto !== '' ? $concepto : '-';
            $r->medio_pago = $metodo !== '' ? strtolower($metodo) : '-';
            $r->moneda = $moneda;
            $r->documento_numero = $ref !== '' ? $ref : '-';
            $r->fecha = $fechaNorm !== '' ? $fechaNorm : (isset($r->fecha) ? $r->fecha : '-');
            $r->registrado_por = $registradoPor;
        }
        unset($r);

        return $rows;
    }

    public function resolve_provisional_match($row)
    {
        if (!$this->db->table_exists('tb_prestamo_cuotas') || !$this->db->table_exists('tb_prestamos')) {
            return null;
        }

        $hasPrestamoIdcliente = $this->db->field_exists('idcliente', 'tb_prestamos');
        $hasPrestamoIdsolicitud = $this->db->field_exists('idsolicitud', 'tb_prestamos');
        $hasSolicitudes = $this->db->table_exists('tb_solicitudes');
        $hasSolicitudIdcliente = $hasSolicitudes && $this->db->field_exists('idcliente', 'tb_solicitudes');

        $clientId = 0;
        if (isset($row->idcliente) && intval($row->idcliente) > 0) $clientId = intval($row->idcliente);
        else if (isset($row->proveedor_id) && intval($row->proveedor_id) > 0) $clientId = intval($row->proveedor_id);

        $targetAmount = isset($row->monto) ? floatval($row->monto) : 0;

        $select = 'pc.idcuota, pc.idprestamo, pc.numero, pc.cuota, pc.saldo, pc.fecha_vencimiento';
        if ($hasPrestamoIdcliente) {
            $select .= ', pr.idcliente as prestamo_idcliente';
        }
        if ($hasPrestamoIdsolicitud) {
            $select .= ', pr.idsolicitud as prestamo_idsolicitud';
        }
        if ($hasSolicitudIdcliente) {
            $select .= ', s.idcliente as solicitud_idcliente';
        }

        $this->db->select($select);
        $this->db->from('tb_prestamo_cuotas pc');
        $this->db->join('tb_prestamos pr', 'pr.idprestamo = pc.idprestamo', 'inner');
        if ($hasPrestamoIdsolicitud && $hasSolicitudes) {
            $this->db->join('tb_solicitudes s', 's.idsolicitud = pr.idsolicitud', 'left');
        }
        if ($clientId > 0) {
            if ($hasPrestamoIdcliente) {
                $this->db->where('pr.idcliente', $clientId);
            } elseif ($hasSolicitudIdcliente) {
                $this->db->where('s.idcliente', $clientId);
            }
        }
        $this->db->order_by('pc.fecha_vencimiento', 'ASC');
        $this->db->order_by('pc.numero', 'ASC');
        $cuotas = $this->db->get()->result();

        if (empty($cuotas)) {
            return null;
        }

        $candidates = array();
        foreach ($cuotas as $c) {
            $saldo = null;
            if (isset($c->saldo) && $c->saldo !== null) {
                $saldo = floatval($c->saldo);
            } else {
                $this->db->select_sum('monto_pagado');
                $this->db->from('tb_prestamo_pagos');
                $this->db->where('idprestamo', intval($c->idprestamo));
                $this->db->where('idcuota', intval($c->idcuota));
                if ($this->db->field_exists('anulado', 'tb_prestamo_pagos')) {
                    $this->db->where('(anulado = 0 OR anulado IS NULL)', null, false);
                }
                $paidRow = $this->db->get()->row();
                $paid = 0;
                if ($paidRow) {
                    $varsPaid = get_object_vars($paidRow);
                    $firstPaid = reset($varsPaid);
                    $paid = floatval($firstPaid);
                }
                $saldo = floatval(isset($c->cuota) ? $c->cuota : 0) - $paid;
            }

            if ($saldo <= 0) {
                continue;
            }

            $cuotaMonto = isset($c->cuota) ? floatval($c->cuota) : 0;
            $matchesAmount = ($targetAmount > 0) && (abs($saldo - $targetAmount) < 0.02 || abs($cuotaMonto - $targetAmount) < 0.02);
            $cuotaClientId = 0;
            if (isset($c->prestamo_idcliente) && intval($c->prestamo_idcliente) > 0) {
                $cuotaClientId = intval($c->prestamo_idcliente);
            } elseif (isset($c->solicitud_idcliente) && intval($c->solicitud_idcliente) > 0) {
                $cuotaClientId = intval($c->solicitud_idcliente);
            } elseif ($clientId > 0) {
                $cuotaClientId = $clientId;
            }

            if ($matchesAmount) {
                $candidates[] = array(
                    'idprestamo' => intval($c->idprestamo),
                    'idcuota' => intval($c->idcuota),
                    'idcliente' => $cuotaClientId,
                    'numero' => isset($c->numero) ? $c->numero : null,
                    'saldo' => $saldo
                );
            }
        }

        if (count($candidates) === 1) {
            return $candidates[0];
        }

        // En esquemas legacy puede no existir idcliente en el provisional.
        // Si hay varias coincidencias por monto, elegimos de forma determinística
        // la cuota más antigua (ya vienen ordenadas por vencimiento/numero).
        if ($clientId <= 0 && count($candidates) > 1) {
            return $candidates[0];
        }

        if ($clientId > 0 && count($candidates) > 1) {
            return $candidates[0];
        }

        if ($clientId > 0 && empty($candidates)) {
            foreach ($cuotas as $c) {
                $saldoBase = isset($c->saldo) && $c->saldo !== null ? floatval($c->saldo) : floatval(isset($c->cuota) ? $c->cuota : 0);
                if ($saldoBase > 0) {
                    $cuotaClientId = 0;
                    if (isset($c->prestamo_idcliente) && intval($c->prestamo_idcliente) > 0) {
                        $cuotaClientId = intval($c->prestamo_idcliente);
                    } elseif (isset($c->solicitud_idcliente) && intval($c->solicitud_idcliente) > 0) {
                        $cuotaClientId = intval($c->solicitud_idcliente);
                    } elseif ($clientId > 0) {
                        $cuotaClientId = $clientId;
                    }
                    return array(
                        'idprestamo' => intval($c->idprestamo),
                        'idcuota' => intval($c->idcuota),
                        'idcliente' => $cuotaClientId,
                        'numero' => isset($c->numero) ? $c->numero : null,
                        'saldo' => $saldoBase
                    );
                }
            }
        }

        // Último fallback para provisionales sin metadatos: tomar la primera cuota
        // con saldo del conjunto ordenado, solo cuando no hay idcliente.
        if ($clientId <= 0 && empty($candidates)) {
            foreach ($cuotas as $c) {
                $saldoBase = isset($c->saldo) && $c->saldo !== null ? floatval($c->saldo) : floatval(isset($c->cuota) ? $c->cuota : 0);
                if ($saldoBase <= 0) continue;
                $cuotaClientId = 0;
                if (isset($c->prestamo_idcliente) && intval($c->prestamo_idcliente) > 0) {
                    $cuotaClientId = intval($c->prestamo_idcliente);
                } elseif (isset($c->solicitud_idcliente) && intval($c->solicitud_idcliente) > 0) {
                    $cuotaClientId = intval($c->solicitud_idcliente);
                }
                return array(
                    'idprestamo' => intval($c->idprestamo),
                    'idcuota' => intval($c->idcuota),
                    'idcliente' => $cuotaClientId,
                    'numero' => isset($c->numero) ? $c->numero : null,
                    'saldo' => $saldoBase
                );
            }
        }

        return null;
    }

    // Ensure helper table exists to link pagos to journals
    private function ensure_pagos_journal_table()
    {
        if ($this->db->table_exists('teso_pagos_journal')) return;
        $sql = "CREATE TABLE IF NOT EXISTS teso_pagos_journal (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pago_id BIGINT NOT NULL,
            journal_id INT NOT NULL,
            locked TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL,
            UNIQUE KEY uq_pago_journal (pago_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        try { $this->db->query($sql); } catch (Exception $e) { log_message('error','Failed creating teso_pagos_journal: '.$e->getMessage()); }
    }

    // Link a pago with a contabilidad journal
    public function link_pago_journal($pago_id, $journal_id, $locked = 0)
    {
        $this->ensure_pagos_journal_table();
        $exists = $this->db->get_where('teso_pagos_journal', ['pago_id' => intval($pago_id)])->row();
        $data = ['pago_id' => intval($pago_id), 'journal_id' => intval($journal_id), 'locked' => $locked ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')];
        if ($exists) {
            $this->db->where('pago_id', intval($pago_id))->update('teso_pagos_journal', $data);
            return $this->db->affected_rows() !== 0;
        } else {
            $this->db->insert('teso_pagos_journal', $data);
            return $this->db->insert_id() ? true : false;
        }
    }

    public function get_pago_journal($pago_id)
    {
        $this->ensure_pagos_journal_table();
        return $this->db->get_where('teso_pagos_journal', ['pago_id' => intval($pago_id)])->row();
    }

    /**
     * Return flujo rows optionally filtered by date range or tipo
     */
    public function get_flujo($filters = array())
    {
        $this->db->from('teso_flujo');
        if (!empty($filters['date_from'])) {
            $this->db->where('fecha >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('fecha <=', $filters['date_to']);
        }
        if (!empty($filters['tipo'])) {
            $this->db->where('tipo', $filters['tipo']);
        }
        $this->db->order_by('fecha', 'DESC');
        return $this->db->get()->result();
    }
}
