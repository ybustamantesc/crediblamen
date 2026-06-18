<?php
/**
 * Modelo para guardar y obtener análisis financiero de asalariado
 */
class Analisis_financiero_asalariado_model extends CI_Model {
    protected $table = 'tb_analisis_financiero_asalariado';

    public function save($data) {
        // Campos válidos según la tabla asalariado y PDF
        $campos_validos = [
            'id','idsolicitud','ingreso_sueldo_neto','ingreso_comisiones','ingreso_bonificaciones','ingreso_remesas','ingreso_otros','total_ingresos','sueldo','inss','ir','sueldo_neto_calc','gastos_alimentacion','gastos_servicios','gastos_vestuario','gastos_educativos','gastos_transporte','gastos_alquiler','pago_empleado_viatico','entretenimiento','otros_gastos','gasto_personal','total_gastos_familiares','cuotas_prestamos','pension_alimenticia','otras_obligaciones','total_otras_obligaciones','total_egresos','flujo_neto_mensual','cuota_periodica','canasta_basica','cantidad_promedio','monto_por_persona','personas_dependientes','gastos_alimentacion_canasta','transporte_urbano','transporte_individual','transporte_interurbano','recorrido_laboral','vehiculo_particular','total_transporte','alquiler','casa_propia','total_gastos_vivienda','cobertura_deuda','cobertura_garantia','tc_acumulado','p_entretenimiento','total_deuda_acreditar','porcentaje_deuda_total','created_at','updated_at',
            // Campos adicionales para PDF
            'efectivo_caja','dinero_banco','total_disponible','cuentas_cobrar','inventario_mercaderia','productos_proceso','productos_terminados','total_inventarios','bienes_muebles','propiedades','otros_activos','total_activos_fijos','total_activos','cuentas_pagar_proveedores','cuentas_pagar_credito','pasivo_no_corriente','total_pasivo','total_patrimonio','total_pasivo_patrimonio','ventas_contado','ventas_credito','ventas_totales','costos_venta','margen_bruto','gastos_generales','utilidad_operativa','fcm_ventas_contado','fcm_recuperacion_credito','fcm_compras_contado','fcm_gastos_generales','flujo_negocio','fcm_otros_ingresos','fcm_gastos_consumo','fcm_otros_gastos','flujo_neto_disponible','gasto_local_alquiler','gasto_energia','gasto_agua','gasto_internet','gasto_seguridad','gasto_limpieza','total_gastos_fijos','tasa_interes','comision_desembolso','olp_fecha','olp_cuota','olp_instituciones','olp_saldo','subtotal_olp_saldo','ocp_fecha','ocp_cuota','ocp_instituciones','ocp_saldo','subtotal_ocp_saldo','costo_salario_ayudante','costo_transporte','costo_total_operacion','asal_olp_fecha','asal_olp_cuota','asal_olp_instituciones','asal_olp_saldo','asal_subtotal_olp_saldo','indicador_endeudamiento','capital_trabajo_neto',
            // Campos nuevos
            'porcentaje_margen',
            'fcm_valor_canasta_basica',
            'fcm_cant_personas_dep',
            'tipo_credito',
            'monto_financiar',
            'plazo_credito',
            'num_cuotas',
            'monto_cuota',
            'fecha_pago',
            'frecuencia_pago',
            'forma_pago',
            'garantia',
            'fundamentacion',
            'comentario'
        ];
        // Eliminar campo 'tipo' si existe
        if (isset($data['tipo'])) unset($data['tipo']);

        // Compatibilidad de nombres entre formulario y BD:
        // formulario => columna BD
        $campo_alias = [
            'numero_cuotas' => 'num_cuotas',
            'fecha_pago_cuota' => 'fecha_pago',
            'garantia_requerida' => 'garantia',
            'fundamentacion_propuesta' => 'fundamentacion'
        ];
        foreach ($campo_alias as $form => $db) {
            if (isset($data[$form]) && !isset($data[$db])) {
                $data[$db] = $data[$form];
                unset($data[$form]);
            }
        }

        $column_info = $this->db->field_data($this->table);
        $column_names = array_map(function ($field) {
            return $field->name;
        }, $column_info);
        $column_types = [];
        foreach ($column_info as $field) {
            $column_types[$field->name] = strtolower($field->type);
        }

        // Convertir arrays a JSON string y vacíos a null.
        // Para campos numéricos, normalizar el valor.
        foreach ($data as $k => $v) {
            if (!in_array($k, $campos_validos) || !in_array($k, $column_names)) {
                unset($data[$k]);
                continue;
            }
            if (is_array($v)) {
                $data[$k] = json_encode($v);
                continue;
            }
            if ($v === '' || $v === []) {
                $data[$k] = null;
                continue;
            }

            $column_type = isset($column_types[$k]) ? $column_types[$k] : '';
            if (strpos($column_type, 'int') !== false || strpos($column_type, 'decimal') !== false || strpos($column_type, 'double') !== false || strpos($column_type, 'float') !== false) {
                if (is_string($v)) {
                    $clean = str_replace(array('%', ' ', 'C$'), '', $v);
                    $clean = str_replace(',', '', $clean);
                    $data[$k] = is_numeric($clean) ? (float)$clean : null;
                } elseif (is_numeric($v)) {
                    $data[$k] = (float)$v;
                } else {
                    $data[$k] = null;
                }
            } else {
                if (is_string($v)) {
                    $data[$k] = trim($v);
                }
            }
        }
        // Calcular cobertura_deuda si falta o viene inválida, usando los datos disponibles.
        $flujo_neto_mensual = isset($data['flujo_neto_mensual']) ? $this->_to_float($data['flujo_neto_mensual']) : 0;
        $cuota_periodica = isset($data['cuota_periodica']) ? $this->_to_float($data['cuota_periodica']) : 0;

        if ($cuota_periodica > 0 && (
            !isset($data['cobertura_deuda']) || !is_numeric($data['cobertura_deuda']) || $this->_to_float($data['cobertura_deuda']) === 0
        )) {
            if ($flujo_neto_mensual > 0) {
                $data['cobertura_deuda'] = round($cuota_periodica / $flujo_neto_mensual, 4);
            } else {
                $data['cobertura_deuda'] = 0;
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

    // Normaliza números en texto (ej. "11,205.00", "C$ 200", "44.6 %") a float.
    private function _to_float($value)
    {
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

        // Compatibilidad: si la columna en BD es `num_cuotas`, exponer
        // también `numero_cuotas` para que el controlador/vista sigan
        // funcionando sin cambios.
        if (!isset($row['numero_cuotas']) && isset($row['num_cuotas'])) {
            $row['numero_cuotas'] = $row['num_cuotas'];
        }
        if (!isset($row['fecha_pago_cuota']) && isset($row['fecha_pago'])) {
            $row['fecha_pago_cuota'] = $row['fecha_pago'];
        }
        if (!isset($row['garantia_requerida']) && isset($row['garantia'])) {
            $row['garantia_requerida'] = $row['garantia'];
        }
        if (!isset($row['fundamentacion_propuesta']) && isset($row['fundamentacion'])) {
            $row['fundamentacion_propuesta'] = $row['fundamentacion'];
        }

        // Mapeo inverso: convertir columnas individuales de BD a arrays (si existen)
        // Primero intenta con nombre asal_olp_* (asalariado)
        $asal_olp_fecha = [];
        $asal_olp_cuota = [];
        $asal_olp_instituciones = [];
        $asal_olp_saldo = [];
        $tiene_asal_olp = false;
        for ($i = 1; $i <= 3; $i++) {
            if (isset($row["asal_olp_fecha_$i"])) {
                $asal_olp_fecha[] = $row["asal_olp_fecha_$i"];
                $tiene_asal_olp = true;
            }
            if (isset($row["asal_olp_cuota_$i"])) {
                $asal_olp_cuota[] = $row["asal_olp_cuota_$i"];
            }
            if (isset($row["asal_olp_instituciones_$i"])) {
                $asal_olp_instituciones[] = $row["asal_olp_instituciones_$i"];
            }
            if (isset($row["asal_olp_saldo_$i"])) {
                $asal_olp_saldo[] = $row["asal_olp_saldo_$i"];
            }
        }
        if ($tiene_asal_olp) {
            $row['asal_olp_fecha'] = $asal_olp_fecha;
            $row['asal_olp_cuota'] = $asal_olp_cuota;
            $row['asal_olp_instituciones'] = $asal_olp_instituciones;
            $row['asal_olp_saldo'] = $asal_olp_saldo;
        }

        return (object)$row;
    }
}
