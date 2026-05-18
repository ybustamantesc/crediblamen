<?php
/**
 * Modelo para guardar y obtener análisis financiero de comerciante
 */
class Analisis_financiero_comerciante_model extends CI_Model {
    protected $table = 'tb_analisis_financiero_comerciante';

    public function save($data) {
        // Lista de campos válidos (ajustar según tu tabla)
        $campos_validos = [
            'id','idsolicitud','gasto_local_alquiler','gasto_energia','gasto_agua','gasto_internet','gasto_seguridad','gasto_limpieza','gasto_personal','gasto_salario_ayudante','gasto_transporte','total_gastos_fijos',
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
            'created_at','updated_at'
        ];
        $columnas_tabla = $this->db->list_fields($this->table);
        // Eliminar campo 'tipo' si existe
        if (isset($data['tipo'])) unset($data['tipo']);

        // Compatibilidad entre nombres de columna según entorno.
        if (isset($data['cuota_periodica']) && !isset($data['cuota_periodica_estim'])) {
            $data['cuota_periodica_estim'] = $data['cuota_periodica'];
        }
        foreach ($data as $k => $v) {
            if (!in_array($k, $campos_validos) || !in_array($k, $columnas_tabla)) {
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

    public function get_by_solicitud($idsolicitud) {
        return $this->db->get_where($this->table, ['idsolicitud' => $idsolicitud])->row();
    }
}
