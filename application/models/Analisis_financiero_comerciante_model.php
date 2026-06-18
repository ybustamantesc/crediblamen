<?php
/**
 * Modelo para guardar y obtener análisis financiero de comerciante
 */
class Analisis_financiero_comerciante_model extends CI_Model {
    protected $table = 'tb_analisis_financiero_comerciante';

    public function save($data) {
        // Lista de campos válidos (ajustar según tu tabla)
        $campos_validos = [
            'id','idsolicitud','gasto_local_alquiler','gasto_energia','gasto_agua','gasto_internet','gasto_seguridad','gasto_limpieza','gasto_personal','gasto_personal_basico','gasto_salario_ayudante','gasto_transporte','total_gastos_fijos',
            'cuota_periodica','cuota_periodica_estim','flujo_neto_disponible','total_transporte','total_gastos_vivienda',
            // Campos del formulario simplificado (flujo mensual)
            'ingreso_sueldo_neto','ingreso_comisiones','ingreso_bonificaciones','ingreso_remesas','ingreso_otros','total_ingresos',
            'sueldo','inss','ir','sueldo_neto_calc',
            'gastos_alimentacion','gastos_servicios','gastos_vestuario','gastos_educativos','gastos_transporte','gastos_alquiler',
            'pago_empleado_viatico','entretenimiento','otros_gastos','total_gastos_familiares',
            'cuotas_prestamos','pension_alimenticia','otras_obligaciones','total_otras_obligaciones','total_egresos','flujo_neto_mensual',
            'canasta_basica','cantidad_promedio','monto_por_persona','personas_dependientes','gastos_alimentacion_canasta',
            'transporte_urbano','transporte_individual','transporte_interurbano','recorrido_laboral','vehiculo_particular',
            'alquiler','casa_propia','cobertura_deuda','cobertura_garantia','tc_acumulado','p_entretenimiento',
            'total_deuda_acreditar','porcentaje_deuda_total',
            // Campos Balance General (Activos)
            'efectivo_caja','dinero_banco','total_disponible','cuentas_cobrar',
            'inventario_mercaderia','productos_proceso','productos_terminados','total_inventarios',
            'bienes_muebles','propiedades','otros_activos','total_activos_fijos','total_activos',
            // Campos Balance General (Pasivos)
            'cuentas_pagar_proveedores','cuentas_pagar_credito','pasivo_no_corriente','total_pasivo',
            'total_patrimonio','total_pasivo_patrimonio',
            // Campos Estado de Resultado Mensual
            'ventas_contado','ventas_credito','ventas_totales','costos_venta','margen_bruto','gastos_generales','utilidad_operativa',
            // Campos Flujo de Caja Mensual
            'fcm_ventas_contado','fcm_recuperacion_credito','fcm_compras_contado','fcm_gastos_generales','flujo_negocio',
            'fcm_otros_ingresos','fcm_gastos_consumo','fcm_valor_canasta_basica','fcm_cant_personas_dep','fcm_otros_gastos',
            // Campos Obligaciones Largo Plazo (mapeados a columnas individuales)
            'oblig_largo_plazo1_fecha','oblig_largo_plazo1_cuota','oblig_largo_plazo1_inst','oblig_largo_plazo1_saldo',
            'oblig_largo_plazo2_fecha','oblig_largo_plazo2_cuota','oblig_largo_plazo2_inst','oblig_largo_plazo2_saldo',
            'oblig_largo_plazo3_fecha','oblig_largo_plazo3_cuota','oblig_largo_plazo3_inst','oblig_largo_plazo3_saldo',
            'subtotal_oblig_largo_plazo','subtotal_olp_saldo',
            // Campos Obligaciones Corto Plazo (mapeados a columnas individuales)
            'oblig_corto_plazo1_fecha','oblig_corto_plazo1_cuota','oblig_corto_plazo1_inst','oblig_corto_plazo1_saldo',
            'oblig_corto_plazo2_fecha','oblig_corto_plazo2_cuota','oblig_corto_plazo2_inst','oblig_corto_plazo2_saldo',
            'oblig_corto_plazo3_fecha','oblig_corto_plazo3_cuota','oblig_corto_plazo3_inst','oblig_corto_plazo3_saldo',
            'subtotal_oblig_corto_plazo','subtotal_ocp_saldo',
            // Campos Costos de Operación
            'costo_salario_ayudante','costo_transporte','costo_total_operacion',
            // Campos Indicadores
            'indicador_endeudamiento','capital_trabajo_neto','porcentaje_margen','monto_credito_solicitado','nivel_endeudamiento',
            // Campos Recomendación de Crédito
            'tipo_credito',
            'monto_financiar',
            'plazo_credito',
            'numero_cuotas',
            'num_cuotas',
            'monto_cuota',
            'fecha_pago_cuota',
            'frecuencia_pago',
            'forma_pago',
            'garantia_requerida',
            'fundamentacion_propuesta',
            'tasa_interes',
            'comision_desembolso',
            'comentario',
            'created_at','updated_at'
        ];

        // MAPEO DE ARRAYS A COLUMNAS INDIVIDUALES
        // Mapear olp (obligaciones largo plazo) arrays a columnas individuales
        if (isset($data['olp_fecha']) && is_array($data['olp_fecha'])) {
            for ($i = 0; $i < 3; $i++) {
                $idx = $i + 1; // 1, 2, 3
                $data["oblig_largo_plazo{$idx}_fecha"] = isset($data['olp_fecha'][$i]) ? $data['olp_fecha'][$i] : null;
                $data["oblig_largo_plazo{$idx}_cuota"] = isset($data['olp_cuota'][$i]) ? $this->_to_float($data['olp_cuota'][$i]) : 0;
                $data["oblig_largo_plazo{$idx}_inst"] = isset($data['olp_instituciones'][$i]) ? $data['olp_instituciones'][$i] : null;
                $data["oblig_largo_plazo{$idx}_saldo"] = isset($data['olp_saldo'][$i]) ? $this->_to_float($data['olp_saldo'][$i]) : 0;
            }
            unset($data['olp_fecha'], $data['olp_cuota'], $data['olp_instituciones'], $data['olp_saldo']);
        }

        // Mapear subtotal de largo plazo a la columna histórica y moderna si existe.
        if (isset($data['subtotal_olp_saldo'])) {
            $subtotal = $this->_to_float($data['subtotal_olp_saldo']);
            $data['subtotal_oblig_largo_plazo'] = $subtotal;
            // Mantener también el nombre de campo original para compatibilidad con tablas antiguas.
            $data['subtotal_olp_saldo'] = $subtotal;
        }

        // Mapear ocp (obligaciones corto plazo) arrays a columnas individuales
        if (isset($data['ocp_fecha']) && is_array($data['ocp_fecha'])) {
            for ($i = 0; $i < 3; $i++) {
                $idx = $i + 1; // 1, 2, 3
                $data["oblig_corto_plazo{$idx}_fecha"] = isset($data['ocp_fecha'][$i]) ? $data['ocp_fecha'][$i] : null;
                $data["oblig_corto_plazo{$idx}_cuota"] = isset($data['ocp_cuota'][$i]) ? $this->_to_float($data['ocp_cuota'][$i]) : 0;
                $data["oblig_corto_plazo{$idx}_inst"] = isset($data['ocp_instituciones'][$i]) ? $data['ocp_instituciones'][$i] : null;
                $data["oblig_corto_plazo{$idx}_saldo"] = isset($data['ocp_saldo'][$i]) ? $this->_to_float($data['ocp_saldo'][$i]) : 0;
            }
            unset($data['ocp_fecha'], $data['ocp_cuota'], $data['ocp_instituciones'], $data['ocp_saldo']);
        }

        // Mapear subtotal de corto plazo a la columna histórica y moderna si existe.
        if (isset($data['subtotal_ocp_saldo'])) {
            $subtotal = $this->_to_float($data['subtotal_ocp_saldo']);
            $data['subtotal_oblig_corto_plazo'] = $subtotal;
            // Mantener también el nombre de campo original para compatibilidad con tablas antiguas.
            $data['subtotal_ocp_saldo'] = $subtotal;
        }

        // Mapear campos de Flujo de Caja con nombres cortos a nombres largos
        $flujo_map = [
            'fcm_ventas_contado' => 'flujo_ventas_contado',
            'fcm_recuperacion_credito' => 'flujo_recuperacion_credito',
            'fcm_compras_contado' => 'flujo_compras_contado',
            'fcm_gastos_generales' => 'flujo_gastos_generales',
            'fcm_otros_ingresos' => 'flujo_otros_ingresos_fam',
            'fcm_gastos_consumo' => 'flujo_gastos_consumo_fam',
            'fcm_otros_gastos' => 'flujo_otros_gastos',
        ];
        foreach ($flujo_map as $corto => $largo) {
            if (isset($data[$corto])) {
                $data[$largo] = $data[$corto];
                // Conservar el campo corto si existe en la tabla `fcm_*`,
                // para no dejar valores vacíos cuando el esquema tiene ambas columnas.
            }
        }

        // Mapear disponible_ab
        if (isset($data['total_disponible'])) {
            $data['disponible_ab'] = $data['total_disponible'];
        }

        // Mapear inventarios_abc
        if (isset($data['total_inventarios'])) {
            $data['inventarios_abc'] = $data['total_inventarios'];
        }

        // Mapear cuentas por pagar
        if (isset($data['cuentas_pagar_credito'])) {
            $data['cuentas_pagar_corto_plazo'] = $data['cuentas_pagar_credito'];
        }

        // Mapear gastos fijos a campos individuales si existen
        if (isset($data['gasto_personal'])) {
            $data['gasto_personal_basico'] = $this->_to_float($data['gasto_personal']);
            unset($data['gasto_personal']);
        }

        // Mapear GASTOS FIJOS MENSUALES a COSTOS DE OPERACIÓN DIRECTOS
        if (isset($data['gasto_salario_ayudante'])) {
            $data['costo_salario_ayudante'] = $this->_to_float($data['gasto_salario_ayudante']);
        }
        if (isset($data['gasto_transporte'])) {
            $data['costo_transporte'] = $this->_to_float($data['gasto_transporte']);
        }

        // Calcular y guardar costo total de operación
        $costo_salario = isset($data['costo_salario_ayudante']) ? $this->_to_float($data['costo_salario_ayudante']) : 0;
        $costo_transporte = isset($data['costo_transporte']) ? $this->_to_float($data['costo_transporte']) : 0;
        $data['costo_total_operacion'] = $costo_salario + $costo_transporte;

        // Mapear indicador_endeudamiento
        if (isset($data['indicador_endeudamiento'])) {
            $data['nivel_endeudamiento'] = $this->_to_float($data['indicador_endeudamiento']);
            unset($data['indicador_endeudamiento']);
        }

        $columnas_tabla = $this->db->list_fields($this->table);

        // Compatibilidad: si la tabla tiene `num_cuotas` (columna histórica), aceptar `numero_cuotas` del formulario
        if (in_array('num_cuotas', $columnas_tabla) && isset($data['numero_cuotas'])) {
            $data['num_cuotas'] = $data['numero_cuotas'];
            unset($data['numero_cuotas']);
        }

        // Preferir guardar en columna `cuentas_cobrar` si existe; si no, mapear a `cuentas_por_cobrar` por compatibilidad
        if (isset($data['cuentas_cobrar'])) {
            if (in_array('cuentas_cobrar', $columnas_tabla)) {
                // mantener `cuentas_cobrar` tal cual
            } elseif (in_array('cuentas_por_cobrar', $columnas_tabla)) {
                $data['cuentas_por_cobrar'] = $data['cuentas_cobrar'];
                unset($data['cuentas_cobrar']);
            }
        }

        // Asegurar conversión numérica para cuentas_cobrar si sigue presente
        if (isset($data['cuentas_cobrar'])) {
            $data['cuentas_cobrar'] = $this->_to_float($data['cuentas_cobrar']);
        }
        if (isset($data['cuentas_por_cobrar'])) {
            $data['cuentas_por_cobrar'] = $this->_to_float($data['cuentas_por_cobrar']);
        }

        // Eliminar campo 'tipo' si existe
        if (isset($data['tipo'])) unset($data['tipo']);

        // Compatibilidad entre nombres de columna según entorno.
        if (isset($data['cuota_periodica']) && !isset($data['cuota_periodica_estim'])) {
            $data['cuota_periodica_estim'] = $data['cuota_periodica'];
        }
        
        foreach ($data as $k => $v) {
            // Solo guardar si está en campos válidos Y existe en la tabla
            if (!in_array($k, $campos_validos)) {
                unset($data[$k]);
                continue;
            }
            if (!in_array($k, $columnas_tabla)) {
                unset($data[$k]);
                continue;
            }
            if (is_array($v)) {
                $data[$k] = json_encode($v);
            } elseif ($v === '' || $v === []) {
                $data[$k] = null;
            } elseif (in_array($k, ['cobertura_deuda', 'cobertura_garantia', 'porcentaje_deuda_total']) && is_string($v)) {
                $num = str_replace(array('%', ' ', 'C$'), '', $v);
                $num = str_replace(',', '', $num);
                $data[$k] = is_numeric($num) ? (float)$num : 0;
            }
        }
        if (isset($data['id']) && $data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update($this->table, $data);
            return $data['id'];
        } else {
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    private function _to_float($value) {
        if (is_null($value) || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        $normalized = str_replace(array('C$', '%', ' '), '', (string)$value);
        $normalized = str_replace(',', '', $normalized);
        return is_numeric($normalized) ? (float)$normalized : 0;
    }

    public function get_by_solicitud($idsolicitud) {
        $row = $this->db->get_where($this->table, ['idsolicitud' => $idsolicitud])->row();
        if (!$row) return null;

        $row = (array)$row;

        // Mapeo inverso: convertir columnas individuales de BD a arrays
        // oblig_largo_plazo{1,2,3}_fecha -> olp_fecha[]
        $olp_fecha = [];
        $olp_cuota = [];
        $olp_instituciones = [];
        $olp_saldo = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($row["oblig_largo_plazo{$i}_fecha"])) {
                $olp_fecha[] = $row["oblig_largo_plazo{$i}_fecha"];
            }
            if (isset($row["oblig_largo_plazo{$i}_cuota"])) {
                $olp_cuota[] = $row["oblig_largo_plazo{$i}_cuota"];
            }
            if (isset($row["oblig_largo_plazo{$i}_inst"])) {
                $olp_instituciones[] = $row["oblig_largo_plazo{$i}_inst"];
            }
            if (isset($row["oblig_largo_plazo{$i}_saldo"])) {
                $olp_saldo[] = $row["oblig_largo_plazo{$i}_saldo"];
            }
        }
        if (!empty($olp_fecha) || !empty($olp_cuota) || !empty($olp_instituciones) || !empty($olp_saldo)) {
            $row['olp_fecha'] = $olp_fecha;
            $row['olp_cuota'] = $olp_cuota;
            $row['olp_instituciones'] = $olp_instituciones;
            $row['olp_saldo'] = $olp_saldo;
        }

        // Mapeo inverso para obligaciones corto plazo
        $ocp_fecha = [];
        $ocp_cuota = [];
        $ocp_instituciones = [];
        $ocp_saldo = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($row["oblig_corto_plazo{$i}_fecha"])) {
                $ocp_fecha[] = $row["oblig_corto_plazo{$i}_fecha"];
            }
            if (isset($row["oblig_corto_plazo{$i}_cuota"])) {
                $ocp_cuota[] = $row["oblig_corto_plazo{$i}_cuota"];
            }
            if (isset($row["oblig_corto_plazo{$i}_inst"])) {
                $ocp_instituciones[] = $row["oblig_corto_plazo{$i}_inst"];
            }
            if (isset($row["oblig_corto_plazo{$i}_saldo"])) {
                $ocp_saldo[] = $row["oblig_corto_plazo{$i}_saldo"];
            }
        }
        if (!empty($ocp_fecha) || !empty($ocp_cuota) || !empty($ocp_instituciones) || !empty($ocp_saldo)) {
            $row['ocp_fecha'] = $ocp_fecha;
            $row['ocp_cuota'] = $ocp_cuota;
            $row['ocp_instituciones'] = $ocp_instituciones;
            $row['ocp_saldo'] = $ocp_saldo;
        }

        // Mapeo inverso: COSTOS DE OPERACIÓN DE VUELTA A GASTOS FIJOS
        // Cuando se cargan datos, el formulario espera gasto_salario_ayudante y gasto_transporte
        if (isset($row['costo_salario_ayudante'])) {
            $row['gasto_salario_ayudante'] = $row['costo_salario_ayudante'];
        }
        if (isset($row['costo_transporte'])) {
            $row['gasto_transporte'] = $row['costo_transporte'];
        }

        // Mapeo inverso: gasto_personal_basico -> gasto_personal
        if (isset($row['gasto_personal_basico'])) {
            $row['gasto_personal'] = $row['gasto_personal_basico'];
        }

        // Mapeo inverso: dinero_banco -> dinero_ahorrado si el PDF o el front usan nombres antiguos
        if (isset($row['dinero_banco'])) {
            $row['dinero_ahorrado'] = $row['dinero_banco'];
        }

        // Mapeo inverso: Flujo de Caja Mensual DB -> nombres de formulario fcm_*
        $flujo_inverso = [
            'flujo_ventas_contado' => 'fcm_ventas_contado',
            'flujo_recuperacion_credito' => 'fcm_recuperacion_credito',
            'flujo_compras_contado' => 'fcm_compras_contado',
            'flujo_gastos_generales' => 'fcm_gastos_generales',
            'flujo_otros_ingresos_fam' => 'fcm_otros_ingresos',
            'flujo_gastos_consumo_fam' => 'fcm_gastos_consumo',
            'flujo_otros_gastos' => 'fcm_otros_gastos',
        ];
        foreach ($flujo_inverso as $dbCampo => $formCampo) {
            if (isset($row[$dbCampo])) {
                $currentValue = isset($row[$formCampo]) ? $this->_to_float($row[$formCampo]) : null;
                $dbValue = $this->_to_float($row[$dbCampo]);
                if ($currentValue === null || $currentValue === 0.0) {
                    $row[$formCampo] = $row[$dbCampo];
                }
            }
        }

        // Compatibilidad: si la columna histórica en BD es `num_cuotas`, exponer también `numero_cuotas` para las vistas
        if (!isset($row['numero_cuotas']) && isset($row['num_cuotas'])) {
            $row['numero_cuotas'] = $row['num_cuotas'];
        }

        // Asegurar que el formulario reciba los nombres de campo usados en la vista.
        if (isset($row['subtotal_oblig_largo_plazo']) && !isset($row['subtotal_olp_saldo'])) {
            $row['subtotal_olp_saldo'] = $row['subtotal_oblig_largo_plazo'];
        }
        if (isset($row['subtotal_oblig_corto_plazo']) && !isset($row['subtotal_ocp_saldo'])) {
            $row['subtotal_ocp_saldo'] = $row['subtotal_oblig_corto_plazo'];
        }
        if (isset($row['subtotal_olp_saldo']) && !isset($row['subtotal_oblig_largo_plazo'])) {
            $row['subtotal_oblig_largo_plazo'] = $row['subtotal_olp_saldo'];
        }
        if (isset($row['subtotal_ocp_saldo']) && !isset($row['subtotal_oblig_corto_plazo'])) {
            $row['subtotal_oblig_corto_plazo'] = $row['subtotal_ocp_saldo'];
        }

        return (object)$row;
    }
}
