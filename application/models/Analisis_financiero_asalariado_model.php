<?php
/**
 * Modelo para guardar y obtener análisis financiero de asalariado
 */
class Analisis_financiero_asalariado_model extends CI_Model {
    protected $table = 'tb_analisis_financiero_asalariado';

    public function save($data) {
        // Campos válidos según la tabla asalariado y PDF
        $campos_validos = [
            'id','idsolicitud','ingreso_sueldo_neto','ingreso_comisiones','ingreso_bonificaciones','ingreso_remesas','ingreso_otros','total_ingresos','sueldo','inss','ir','sueldo_neto_calc','gastos_alimentacion','gastos_servicios','gastos_vestuario','gastos_educativos','gastos_transporte','gastos_alquiler','pago_empleado_viatico','entretenimiento','otros_gastos','total_gastos_familiares','cuotas_prestamos','pension_alimenticia','otras_obligaciones','total_otras_obligaciones','total_egresos','flujo_neto_mensual','cuota_periodica','canasta_basica','cantidad_promedio','monto_por_persona','personas_dependientes','gastos_alimentacion_canasta','transporte_urbano','transporte_individual','transporte_interurbano','recorrido_laboral','vehiculo_particular','alquiler','casa_propia','cobertura_deuda','cobertura_garantia','tc_acumulado','p_entretenimiento','total_deuda_acreditar','porcentaje_deuda_total','created_at','updated_at',
            // Campos adicionales para PDF
            'efectivo_caja','dinero_banco','total_disponible','cuentas_cobrar','inventario_mercaderia','productos_proceso','productos_terminados','total_inventarios','bienes_muebles','propiedades','otros_activos','total_activos_fijos','total_activos','cuentas_pagar_proveedores','cuentas_pagar_credito','pasivo_no_corriente','total_pasivo','total_patrimonio','total_pasivo_patrimonio','ventas_contado','ventas_credito','ventas_totales','costos_venta','margen_bruto','gastos_generales','utilidad_operativa','fcm_ventas_contado','fcm_recuperacion_credito','fcm_compras_contado','fcm_gastos_generales','flujo_negocio','fcm_otros_ingresos','fcm_gastos_consumo','fcm_otros_gastos','flujo_neto_disponible','gasto_local_alquiler','gasto_energia','gasto_agua','gasto_internet','gasto_seguridad','gasto_limpieza','gasto_personal','total_gastos_fijos','olp_fecha','olp_cuota','olp_instituciones','olp_saldo','subtotal_olp_saldo','ocp_fecha','ocp_cuota','ocp_instituciones','ocp_saldo','subtotal_ocp_saldo','costo_salario_ayudante','costo_transporte','costo_total_operacion','asal_olp_fecha','asal_olp_cuota','asal_olp_instituciones','asal_olp_saldo','asal_subtotal_olp_saldo','indicador_endeudamiento','capital_trabajo_neto',
            // Campo nuevo
            'porcentaje_margen',
            'fcm_valor_canasta_basica',
            'fcm_cant_personas_dep'
        ];
        // Eliminar campo 'tipo' si existe
        if (isset($data['tipo'])) unset($data['tipo']);
        // Convertir arrays a JSON string y vacíos/no numéricos a null para campos numéricos
        foreach ($data as $k => $v) {
            if (!in_array($k, $campos_validos)) {
                unset($data[$k]);
                continue;
            }
            if (is_array($v)) {
                $data[$k] = json_encode($v);
            } elseif ($v === '' || $v === [] || (in_array($k, ['nivel_endeudamiento','capital_trabajo_neto','porcentaje_margen']) && !is_numeric($v))) {
                $data[$k] = null;
            } elseif (in_array($k, ['cobertura_deuda', 'cobertura_garantia', 'porcentaje_deuda_total']) && is_string($v)) {
                $num = str_replace(array('%', ' ', 'C$'), '', $v);
                $num = str_replace(',', '', $num);
                $data[$k] = is_numeric($num) ? (float)$num : 0;
            }
        }
        // Calcular cobertura_deuda si no es numérico o es 0
        if (
            (!isset($data['cobertura_deuda']) || !is_numeric($data['cobertura_deuda']) || $data['cobertura_deuda'] === 0)
            && isset($data['cuota_periodica']) && isset($data['flujo_neto_disponible'])
            && is_numeric($data['cuota_periodica']) && is_numeric($data['flujo_neto_disponible']) && $data['flujo_neto_disponible'] > 0
        ) {
            $data['cobertura_deuda'] = round(($data['cuota_periodica'] / $data['flujo_neto_disponible']) * 100, 2);
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

    public function get_by_solicitud($idsolicitud) {
        return $this->db->get_where($this->table, ['idsolicitud' => $idsolicitud])->row();
    }
}
