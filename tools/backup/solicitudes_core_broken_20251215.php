<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php // Minimal Solicitud Inicial skeleton (layout restored)
// Backup: tools/backup/solicitudes_core_backup_20251215.php
?>

<?php $this->load->view('layout/navbar'); ?>
<?php
// Expose the extracted `$solicitud` variable to helper functions via $GLOBALS
// so s_val(), s_checked(), etc. can access it when called inside function scope.
$GLOBALS['solicitud'] = isset($solicitud) ? $solicitud : (isset($GLOBALS['solicitud']) ? $GLOBALS['solicitud'] : null);

if (! function_exists('s_val')) {
    function s_val($field, $fallback = '') {
        $solicitud = isset($GLOBALS['solicitud']) ? $GLOBALS['solicitud'] : (isset($solicitud) ? $solicitud : null);
        if (isset($solicitud) && isset($solicitud->$field) && $solicitud->$field !== null) {
            return $solicitud->$field;
        }
        return $fallback;
    }
}
?>
                                    <div class="col-md-3"><div class="form-group"><label>Monto solicitado: U$</label><input id="monto_solicitado" type="number" step="0.01" class="form-control" name="monto_solicitado" value="<?php echo s_val('monto_solicitado', set_value('monto_solicitado')); ?>"></div></div>

                                    <!-- Visible classification dropdown (populated from $productos clasificacion values) -->
                                    <div class="col-md-3"><div class="form-group"><label>Tipo de producto</label>
                                        <?php
                                        $clasificaciones = array();
                                        if (!empty($productos) && is_array($productos)){
                                            foreach ($productos as $p){
                                                $c = isset($p->clasificacion) ? $p->clasificacion : (isset($p['clasificacion']) ? $p['clasificacion'] : '');
                                                if ($c !== '') $clasificaciones[$c] = $c;
                                            }
                                        }
                                        ?>
                                        <select id="producto_clasificacion" name="clasificacion" class="form-control">
                                            <option value="">-- Seleccione tipo --</option>
                                            <?php if (!empty($clasificaciones)): foreach ($clasificaciones as $c): ?>
                                                <option value="<?php echo htmlspecialchars($c); ?>" <?php echo (s_val('clasificacion')==$c ? 'selected' : ''); ?>><?php echo htmlspecialchars($c); ?></option>
                                            <?php endforeach; else: ?>
                                                <option value="Negocios" <?php echo (s_val('clasificacion')=='Negocios' ? 'selected' : ''); ?>>Negocios</option>
                                                <option value="Personal" <?php echo (s_val('clasificacion')=='Personal' ? 'selected' : ''); ?>>Personal</option>
                                                <option value="Hipotecario" <?php echo (s_val('clasificacion')=='Hipotecario' ? 'selected' : ''); ?>>Hipotecario / Vivienda</option>
                                                <option value="Vehiculo" <?php echo (s_val('clasificacion')=='Vehiculo' ? 'selected' : ''); ?>>Vehículo usado</option>
                                            <?php endif; ?>
                                        </select>
                                    </div></div>

                                    <div class="col-md-3"><div class="form-group"><label>Plazo (meses)</label><input type="number" class="form-control" name="plazo_meses" value="<?php echo s_val('plazo_meses', set_value('plazo_meses')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Frecuencia</label><input type="text" class="form-control" name="frecuencia" value="<?php echo s_val('frecuencia', set_value('frecuencia')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Tasa de interés a cobrar (%)</label><input type="number" step="0.01" class="form-control" name="tasa_interes" value="<?php echo s_val('tasa_interes', set_value('tasa_interes')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Promedio de cuota estimada: U$</label><input type="number" step="0.01" class="form-control" name="cuota_estimado" value="<?php echo s_val('cuota_estimado', set_value('cuota_estimado')); ?>"></div></div>

                                    <div class="col-md-12 mt-2"><label>Garantía ofrecida:</label>
                                        <div class="d-flex" style="gap:12px;">
                                            <label><input type="checkbox" name="garantia_hipotecaria" value="1" <?php echo s_checked('garantia_hipotecaria'); ?>> Hipotecaria</label>
                                            <label><input type="checkbox" name="garantia_prendaria" value="1" <?php echo s_checked('garantia_prendaria'); ?>> Prendaria</label>
                                            <label><input type="checkbox" name="garantia_fiador" value="1" <?php echo s_checked('garantia_fiador'); ?>> Fiador</label>
                                            <label><input type="checkbox" name="garantia_otra" value="1" <?php echo s_checked('garantia_otra'); ?>> Otra</label>
                                        </div>
                                    </div>

                                    <!-- Product selector removed: handled by suggestion panel -->

                                    <!-- Suggestion panel: will be populated via AJAX from tipos_productos -->
                                    <div id="producto_sugerido_panel" class="col-md-3" style="float:right; width:300px; margin-left:12px;">
                                        <div style="border:1px solid #e6e6e6; padding:12px; border-radius:6px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.03);">
                                            <strong>Producto sugerido</strong>
                                            <div id="producto_sugerido_body" style="margin-top:8px; font-size:13px; color:#333;">
                                                <em>Cargando productos...</em>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 1: Datos Personales (placeholder) -->
                                    <div class="col-md-12 mt-1">
                                        <h5>1. DATOS GENERALES DEL CLIENTE</h5>
                                        <div id="section-1">
                                            <div class="row">
                                                <div class="col-md-3"><div class="form-group"><label>Fecha de solicitud</label><input type="datetime-local" class="form-control" name="fecha_solicitud" value="<?php echo s_date_fmt('fecha_solicitud', 'Y-m-d\TH:i', set_value('fecha_solicitud')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Nombre completo <span class="text-danger">*</span></label><input type="text" required class="form-control" name="nombre_completo" value="<?php echo s_val('nombre_completo', trim(s_val('apellidos','') . ' ' . s_val('nombres','')) ?: set_value('nombre_completo')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Cédula de identidad</label><input type="text" class="form-control" name="numero_doc" value="<?php echo s_val('numero_doc', set_value('numero_doc')); ?>"></div></div>

                                                <div class="col-md-3"><div class="form-group"><label>Fecha de nacimiento</label><input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo s_date_fmt('fecha_nacimiento', 'Y-m-d', set_value('fecha_nacimiento')); ?>"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Edad</label><input type="number" class="form-control" name="edad" value="<?php echo s_val('edad', set_value('edad')); ?>"></div></div>
                                                <div class="col-md-7"><div class="form-group"><label>Estado civil</label>
                                                    <select class="form-control" name="estado_civil">
                                                        <option value="">-- Seleccione --</option>
                                                        <option value="Soltero" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Soltero' ? 'selected' : ''); ?>>Soltero(a)</option>
                                                        <option value="Casado" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Casado' ? 'selected' : ''); ?>>Casado(a)</option>
                                                        <option value="Union libre" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Union libre' ? 'selected' : ''); ?>>Unión libre</option>
                                                        <option value="Divorciado" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Divorciado' ? 'selected' : ''); ?>>Divorciado(a)</option>
                                                        <option value="Viudo" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Viudo' ? 'selected' : ''); ?>>Viudo(a)</option>
                                                    </select>
                                                </div></div>

                                                <div class="col-md-6"><div class="form-group"><label>Nombre del cónyuge o pareja</label><input type="text" class="form-control" name="nombre_conyuge" value="<?php echo s_val('nombre_conyuge', set_value('nombre_conyuge')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Cédula del cónyuge o pareja</label><input type="text" class="form-control" name="dni_conyuge" value="<?php echo s_val('dni_conyuge', set_value('dni_conyuge')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Ocupación del cónyuge o pareja</label><input type="text" class="form-control" name="ocupacion_conyuge" value="<?php echo s_val('ocupacion_conyuge', set_value('ocupacion_conyuge')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Teléfono del cónyuge o pareja</label><input type="text" class="form-control" name="telefono_conyuge" value="<?php echo s_val('telefono_conyuge', set_value('telefono_conyuge')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Número de dependientes</label><input type="number" class="form-control" name="numero_dependientes" value="<?php echo s_val('numero_dependientes', set_value('numero_dependientes')); ?>"></div></div>

                                                <div class="col-md-6"><div class="form-group"><label>Teléfono(s) del solicitante</label><input type="text" class="form-control" name="telefono" value="<?php echo s_val('telefono', set_value('telefono')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Dirección exacta de domicilio</label><textarea rows="2" class="form-control" name="direccion"><?php echo s_val('direccion', set_value('direccion')); ?></textarea></div></div>

                                                <div class="col-md-3"><div class="form-group"><label>Tiempo de residir (años)</label><input type="number" class="form-control" name="tiempo_residir_anios" value="<?php echo s_val('tiempo_residir_anios', set_value('tiempo_residir_anios')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Tiempo de residir (meses)</label><input type="number" class="form-control" name="tiempo_residir_meses" value="<?php echo s_val('tiempo_residir_meses', set_value('tiempo_residir_meses')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Condición vivienda</label><div>
                                                    <label class="mr-2"><input type="radio" name="condicion_vivienda" value="Propia" <?php echo (s_val('condicion_vivienda') == 'Propia' ? 'checked' : ''); ?>> Propia</label>
                                                    <label class="mr-2"><input type="radio" name="condicion_vivienda" value="Familiar" <?php echo (s_val('condicion_vivienda') == 'Familiar' ? 'checked' : ''); ?>> Familiar</label>
                                                    <label class="mr-2"><input type="radio" name="condicion_vivienda" value="Alquilada" <?php echo (s_val('condicion_vivienda') == 'Alquilada' ? 'checked' : ''); ?>> Alquilada</label>
                                                    <label><input type="radio" name="condicion_vivienda" value="Otra" <?php echo (s_val('condicion_vivienda') == 'Otra' ? 'checked' : ''); ?>> Otra</label>
                                                </div></div></div>

                                                <!-- Keep legacy apellidos/nombres hidden for controller compatibility -->
                                                <input type="hidden" name="apellidos" value="<?php echo s_val('apellidos', set_value('apellidos')); ?>">
                                                <input type="hidden" name="nombres" value="<?php echo s_val('nombres', set_value('nombres')); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Placeholder area for additional sections (kept hidden until used) -->
                                    <!-- SECTION 2: INFORMACIÓN LABORAL (CLIENTE ASALARIADO) -->
                                    <div class="col-md-12 mt-4">
                                        <h5>2. INFORMACIÓN LABORAL (CLIENTE ASALARIADO)</h5>
                                    </div>
                                    <div class="col-md-6"><div class="form-group"><label>Nombre de la empresa</label><input type="text" class="form-control" name="nombre_empresa" value="<?php echo s_val('nombre_empresa', set_value('nombre_empresa')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Dirección de la empresa</label><input type="text" class="form-control" name="direccion_empresa" value="<?php echo s_val('direccion_empresa', set_value('direccion_empresa')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Teléfono de la empresa</label><input type="text" class="form-control" name="telefono_empresa" value="<?php echo s_val('telefono_empresa', set_value('telefono_empresa')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Cargo / Puesto</label><input type="text" class="form-control" name="cargo_puesto" value="<?php echo s_val('cargo_puesto', set_value('cargo_puesto')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo en el empleo (años)</label><input type="number" class="form-control" name="tiempo_empleo_anios" value="<?php echo s_val('tiempo_empleo_anios', set_value('tiempo_empleo_anios')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo en el empleo (meses)</label><input type="number" class="form-control" name="tiempo_empleo_meses" value="<?php echo s_val('tiempo_empleo_meses', set_value('tiempo_empleo_meses')); ?>"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label>Tipo de contrato</label>
                                        <div>
                                            <label class="mr-2"><input type="checkbox" name="tipo_contrato_permanente" value="1" <?php echo s_checked('tipo_contrato_permanente'); ?>> Permanente</label>
                                            <label class="mr-2"><input type="checkbox" name="tipo_contrato_temporal" value="1" <?php echo s_checked('tipo_contrato_temporal'); ?>> Temporal</label>
                                            <label class="mr-2"><input type="checkbox" name="tipo_contrato_otro" value="1" <?php echo s_checked('tipo_contrato_otro'); ?>> Otro</label>
                                        </div>
                                    </div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ingreso mensual neto (C$)</label><input type="number" step="0.01" class="form-control" name="ingreso_mensual_neto" value="<?php echo s_val('ingreso_mensual_neto', set_value('ingreso_mensual_neto')); ?>"></div></div>
                                    <div class="col-md-8"><div class="form-group"><label>Deducciones (INSS, IR)</label><input type="text" class="form-control" name="deducciones" value="<?php echo s_val('deducciones', set_value('deducciones')); ?>"></div></div>

                                    <!-- SECTION 3: INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO) -->
                                    <div class="col-md-12 mt-4">
                                        <h5>3. INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO)</h5>
                                    </div>
                                    <div class="col-md-6"><div class="form-group"><label>Nombre del negocio</label><input type="text" class="form-control" name="nombre_negocio" value="<?php echo s_val('nombre_negocio', set_value('nombre_negocio')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Actividad económica principal</label><input type="text" class="form-control" name="actividad_economica" value="<?php echo s_val('actividad_economica', set_value('actividad_economica')); ?>"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label>Ubicación del negocio</label><input type="text" class="form-control" name="ubicacion_negocio" value="<?php echo s_val('ubicacion_negocio', set_value('ubicacion_negocio')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Teléfono del negocio</label><input type="text" class="form-control" name="telefono_negocio" value="<?php echo s_val('telefono_negocio', set_value('telefono_negocio')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo de operación (años)</label><input type="number" class="form-control" name="tiempo_operacion_anios" value="<?php echo s_val('tiempo_operacion_anios', set_value('tiempo_operacion_anios')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo de operación (meses)</label><input type="number" class="form-control" name="tiempo_operacion_meses" value="<?php echo s_val('tiempo_operacion_meses', set_value('tiempo_operacion_meses')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Propiedad del local</label><input type="text" class="form-control" name="propiedad_negocio" value="<?php echo s_val('propiedad_negocio', set_value('propiedad_negocio')); ?>"></div></div>

                                    <!-- Ingresos y Ventas -->
                                    <div class="col-md-12 mt-3"><h6>Ingresos y Ventas</h6></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas en días buenos: C$</label><input type="number" step="0.01" class="form-control" name="ventas_buenos_amount" value="<?php echo s_val('ventas_buenos_amount', set_value('ventas_buenos_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas en días malos: C$</label><input type="number" step="0.01" class="form-control" name="ventas_malos_amount" value="<?php echo s_val('ventas_malos_amount', set_value('ventas_malos_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas promedio mensual: C$</label><input type="number" step="0.01" class="form-control" name="ventas_promedio_mensual" value="<?php echo s_val('ventas_promedio_mensual', set_value('ventas_promedio_mensual')); ?>"></div></div>

                                    <div class="col-md-12"><div class="form-group"><label>Días de ventas (buenos)</label>
                                        <div class="d-flex" style="gap:8px;">
                                            <?php $mask_buenos = (int) s_val('ventas_dias_buenos_mask', 0); $labels = array('L','Ma','Mi','J','V','S','D'); for ($i=0;$i<7;$i++): $is = (($mask_buenos & (1<<$i)) !== 0) ? 'checked' : ''; ?>
                                                <label style="min-width:40px;"><input type="checkbox" name="ventas_buenos_days[]" value="<?php echo $i; ?>" <?php echo $is; ?>> <?php echo $labels[$i]; ?></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div></div>

                                    <div class="col-md-12"><div class="form-group"><label>Días de ventas (malos)</label>
                                        <div class="d-flex" style="gap:8px;">
                                            <?php $mask_malos = (int) s_val('ventas_dias_malos_mask', 0); for ($i=0;$i<7;$i++): $is2 = (($mask_malos & (1<<$i)) !== 0) ? 'checked' : ''; ?>
                                                <label style="min-width:40px;"><input type="checkbox" name="ventas_malos_days[]" value="<?php echo $i; ?>" <?php echo $is2; ?>> <?php echo $labels[$i]; ?></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div></div>

                                    <div class="col-md-4"><div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="margen_comercial" value="<?php echo s_val('margen_comercial', set_value('margen_comercial')); ?>"></div></div>

                                    <!-- Otros ingresos (3 bloques) -->
                                    <div class="col-md-12 mt-3"><h6>Otros ingresos</h6></div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 1: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_1_amount" value="<?php echo s_val('otros_ingresos_1_amount', set_value('otros_ingresos_1_amount')); ?>"></div>
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_1_margin" value="<?php echo s_val('otros_ingresos_1_margin', set_value('otros_ingresos_1_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_1_detalle"><?php echo s_val('otros_ingresos_1_detalle', set_value('otros_ingresos_1_detalle')); ?></textarea></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 2: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_2_amount" value="<?php echo s_val('otros_ingresos_2_amount', set_value('otros_ingresos_2_amount')); ?>"></div>
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_2_margin" value="<?php echo s_val('otros_ingresos_2_margin', set_value('otros_ingresos_2_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_2_detalle"><?php echo s_val('otros_ingresos_2_detalle', set_value('otros_ingresos_2_detalle')); ?></textarea></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 3: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_3_amount" value="<?php echo s_val('otros_ingresos_3_amount', set_value('otros_ingresos_3_amount')); ?>"></div>
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_3_margin" value="<?php echo s_val('otros_ingresos_3_margin', set_value('otros_ingresos_3_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_3_detalle"><?php echo s_val('otros_ingresos_3_detalle', set_value('otros_ingresos_3_detalle')); ?></textarea></div>
                                    </div>

                                    <!-- Estructura financiera y detalle de inventario -->
                                    <div class="col-md-12 mt-3"><h6>Estructura Financiera del Negocio</h6></div>
                                    <div class="col-md-4"><div class="form-group"><label>Cuentas por cobrar: C$</label><input type="number" step="0.01" class="form-control" name="cuentas_por_cobrar_amount" value="<?php echo s_val('cuentas_por_cobrar_amount', set_value('cuentas_por_cobrar_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Caja (efectivo): C$</label><input type="number" step="0.01" class="form-control" name="caja_amount" value="<?php echo s_val('caja_amount', set_value('caja_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Banco: C$</label><input type="number" step="0.01" class="form-control" name="banco_amount" value="<?php echo s_val('banco_amount', set_value('banco_amount')); ?>"></div></div>

                                    <div class="col-md-12 mt-3"><h6>Detalle del Inventario</h6></div>
                                    <div class="col-md-12"><div class="form-group"><label>Producto / Detalle</label><textarea class="form-control" rows="4" name="detalle_inventario"><?php echo s_val('detalle_inventario', set_value('detalle_inventario')); ?></textarea></div></div>

                                    <!-- SECTION 4: GASTOS FIJOS Y OPERATIVOS -->
                                    <div class="col-md-12 mt-4">
                                        <h5>Gastos Fijos y Operativos</h5>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Pago de alquiler local: C$</label><input type="number" step="0.01" class="form-control" name="pago_alquiler" value="<?php echo s_val('pago_alquiler', set_value('pago_alquiler')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Pago de trabajadores: C$</label><input type="number" step="0.01" class="form-control" name="pago_trabajadores" value="<?php echo s_val('pago_trabajadores', set_value('pago_trabajadores')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Número de empleados</label><input type="number" class="form-control" name="numero_empleados" value="<?php echo s_val('numero_empleados', set_value('numero_empleados')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Energía eléctrica: C$</label><input type="number" step="0.01" class="form-control" name="energia_electrica" value="<?php echo s_val('energia_electrica', set_value('energia_electrica')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Agua potable: C$</label><input type="number" step="0.01" class="form-control" name="agua_potable" value="<?php echo s_val('agua_potable', set_value('agua_potable')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Internet / Telefonía: C$</label><input type="number" step="0.01" class="form-control" name="internet_telefonia" value="<?php echo s_val('internet_telefonia', set_value('internet_telefonia')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Otros gastos: C$</label><input type="number" step="0.01" class="form-control" name="otros_gastos" value="<?php echo s_val('otros_gastos', set_value('otros_gastos')); ?>"></div></div>

                                    <!-- SECTION 4b: Declaración del cliente -->
                                    <div class="col-md-12 mt-4"><h5>Declaración del Cliente</h5></div>
                                    <div class="col-md-12"><div class="form-group"><p>Declaro que la información proporcionada es verídica y autorizo a Credi Blamen S.A. a verificar mis datos en las fuentes necesarias para fines de análisis crediticio y cumplimiento regulatorio.</p></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Acepto verificación</label><br><input type="checkbox" name="declaro_verificacion" value="1" <?php echo s_checked('declaro_verificacion'); ?>></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Comisión por desembolso (%)</label><input type="number" step="0.01" class="form-control" name="comision_desembolso" value="<?php echo s_val('comision_desembolso', set_value('comision_desembolso')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Firma del solicitante</label><input type="text" class="form-control" name="firma_solicitante" value="<?php echo s_val('firma_solicitante', set_value('firma_solicitante')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Fecha firma</label><input type="date" class="form-control" name="fecha_firma" value="<?php echo s_date_fmt('fecha_firma', 'Y-m-d', set_value('fecha_firma')); ?>"></div></div>
                                    <div class="col-md-9"><div class="form-group"><label>DDC - Investigación de campo</label><input type="text" class="form-control" name="ddc_investigacion_campo" value="<?php echo s_val('ddc_investigacion_campo', set_value('ddc_investigacion_campo')); ?>"></div></div>

                                    <!-- SECTION 5: USO INTERNO (PROMOTOR / MICROFINANCIERA) -->
                                    <div class="col-md-12 mt-4"><h5>5. USO INTERNO (PROMOTOR / MICROFINANCIERA)</h5></div>
                                    <div class="col-md-6"><div class="form-group"><label>Nombre del promotor</label><input type="text" class="form-control" name="nombre_promotor" value="<?php echo s_val('nombre_promotor', set_value('nombre_promotor')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Fecha de recepción de solicitud</label><input type="date" class="form-control" name="fecha_recepcion_solicitud" value="<?php echo s_date_fmt('fecha_recepcion_solicitud', 'Y-m-d', set_value('fecha_recepcion_solicitud')); ?>"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label>Observaciones del promotor</label><textarea class="form-control" name="observaciones_promotor"><?php echo s_val('observaciones_promotor', set_value('observaciones_promotor')); ?></textarea></div></div>

                                    <div id="additional-sections" class="col-12 mt-3"></div>

                                    <div class="col-12 mt-3">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <script>
                                (function(){
                                    function setProductValues(opt){
                                        if(!opt) return;
                                        var tasa = opt.getAttribute('data-tasa') || '';
                                        var com = opt.getAttribute('data-comision') || '';
                                        var plazo = opt.getAttribute('data-plazo') || '';
                                        var $tasa = document.getElementById('producto_tasa');
                                        var $com = document.getElementById('producto_comision');
                                        var $plazo = document.getElementById('producto_plazo');
                                        if($tasa) $tasa.value = tasa;
                                        if($com) $com.value = com;
                                        if($plazo) $plazo.value = plazo;
                                    }

                                    var sel = document.getElementById('producto_select');
                                    var classSel = document.getElementById('producto_clasificacion');
                                    var montoInput = document.getElementById('monto_solicitado');

                                    function autoSelectProduct(){
                                        if(!sel) return;
                                        var monto = parseFloat((montoInput && montoInput.value) ? montoInput.value : 0) || 0;
                                        var cls = (classSel && classSel.value) ? classSel.value : '';
                                        var options = sel.options;
                                        var found = null;
                                        for(var i=0;i<options.length;i++){
                                            var o = options[i];
                                            if(!o.value) continue; // skip placeholder
                                            var optCls = o.getAttribute('data-clasificacion') || '';
                                            var min = parseFloat(o.getAttribute('data-min-monto')) || NaN;
                                            var max = parseFloat(o.getAttribute('data-max-monto')) || NaN;

                                            // classification check: if cls selected then optCls must match (if optCls set)
                                            if(cls){
                                                if(optCls && optCls !== cls) continue;
                                            }

                                            // amount check: if min/max provided, ensure monto within bounds
                                            if(!isNaN(min) && !isNaN(max)){
                                                if(monto < min || monto > max) continue;
                                            } else if(!isNaN(min)){
                                                if(monto < min) continue;
                                            } else if(!isNaN(max)){
                                                if(monto > max) continue;
                                            }

                                            // if we reach here, option is acceptable
                                            found = o;
                                            break;
                                        }
                                        if(found){
                                            sel.value = found.value;
                                            setProductValues(found);
                                        }
                                    }

                                    if(sel){
                                        sel.addEventListener('change', function(e){
                                            var opt = sel.options[sel.selectedIndex];
                                            setProductValues(opt);
                                        }, false);
                                        // set defaults from selected option on load
                                        setProductValues(sel.options[sel.selectedIndex]);
                                    }

                                    if(montoInput){
                                        montoInput.addEventListener('input', function(){
                                            autoSelectProduct();
                                            try{ loadProductosAll(); }catch(e){}
                                        }, false);
                                    }

                                    if(classSel){
                                        classSel.addEventListener('change', function(){
                                            autoSelectProduct();
                                            try{ loadProductosAll(); }catch(e){}
                                            updateSuggestionPanel();
                                        }, false);
                                    }

                                    // Try auto-select once on load if user hasn't chosen a product yet
                                    document.addEventListener('DOMContentLoaded', function(){
                                        try{ if(sel && !sel.value) autoSelectProduct(); }catch(e){}
                                        try{ loadProductosAll(); }catch(e){}
                                        try{ loadAllTipos(); }catch(e){}
                                    });

                                    /* Remote productos loader and suggestion UI */
                                    var productosRemote = [];
                                    var sugeridoBody = document.getElementById('producto_sugerido_body');
                                    var apiUrl = '<?php echo base_url("tipos_productos/match_ajax"); ?>';

                                    function safeNum(v){ var n = parseFloat(v); return isNaN(n)?null:n; }

                                    function loadRemoteProductos(monto, clasificacion){
                                        if(!apiUrl) return;
                                        var url = apiUrl + '?monto=' + encodeURIComponent(monto || '') + '&clasificacion=' + encodeURIComponent(clasificacion || '');
                                        fetch(url, { credentials: 'same-origin' })
                                            .then(function(r){ if(!r.ok) throw new Error('http'); return r.json(); })
                                            .then(function(data){
                                                productosRemote = Array.isArray(data) ? data : (Array.isArray(data.tipos) ? data.tipos : []);
                                                populateClassificationOptionsFromRemote();
                                                updateSuggestionPanel();
                                            }).catch(function(){
                                                if(sugeridoBody) sugeridoBody.innerHTML = '<em>No se pudo cargar la lista de productos.</em>';
                                            });
                                    }

                                    // load full list of tipos to populate the classification dropdown reliably
                                    function loadAllTipos(){
                                        var url2 = '<?php echo base_url("tipos_productos/list_ajax"); ?>';
                                        fetch(url2, { credentials: 'same-origin' })
                                            .then(function(r){ if(!r.ok) throw new Error('http'); return r.json(); })
                                            .then(function(data){
                                                var arr = Array.isArray(data) ? data : (Array.isArray(data.tipos) ? data.tipos : []);
                                                if(!arr || !arr.length) return;
                                                var distinct = {};
                                                arr.forEach(function(p){ var c = p.clasificacion || p.tipo || p.category || ''; if(c) distinct[c]=c; });
                                                var keys = Object.keys(distinct);
                                                if(!keys.length) return;
                                                // rebuild classification select
                                                if(classSel){
                                                    classSel.innerHTML = '<option value="">-- Seleccione tipo --</option>';
                                                    keys.forEach(function(k){
                                                        var opt = document.createElement('option'); opt.value = k; opt.text = k;
                                                        if('<?php echo addslashes(s_val('clasificacion')); ?>' === k) opt.selected = true;
                                                        classSel.appendChild(opt);
                                                    });
                                                }
                                            }).catch(function(){
                                                // ignore - keep existing options
                                            });
                                    }

                                    // load full productos list (all tipos) and cache locally for client-side matching
                                    function loadProductosAll(){
                                        var url = '<?php echo base_url("tipos_productos/list_ajax"); ?>';
                                        fetch(url, { credentials: 'same-origin' })
                                            .then(function(r){ if(!r.ok) throw new Error('http'); return r.json(); })
                                            .then(function(data){
                                                var arr = Array.isArray(data) ? data : (Array.isArray(data.tipos) ? data.tipos : []);
                                                productosRemote = arr || [];
                                                // ensure classification options updated
                                                populateClassificationOptionsFromRemote();
                                                // re-evaluate suggestion panel
                                                updateSuggestionPanel();
                                                // optionally auto-select
                                                autoSelectProduct();
                                            }).catch(function(){
                                                if(sugeridoBody) sugeridoBody.innerHTML = '<em>No se pudo cargar la lista de productos.</em>';
                                            });
                                    }

                                    function populateClassificationOptionsFromRemote(){
                                        // keep existing behavior but don't overwrite if empty; prefer full list loader
                                        if(!classSel) return;
                                        if(!productosRemote || !productosRemote.length) return;
                                        var distinct = {};
                                        productosRemote.forEach(function(p){
                                            var c = p.clasificacion || p.tipo || p.category || p.clasificacion_producto || '';
                                            if(c) distinct[c] = c;
                                        });
                                        var keys = Object.keys(distinct);
                                        if(!keys.length) return;
                                        // append any missing options without wiping custom selection
                                        keys.forEach(function(k){
                                            var exists = false;
                                            for(var i=0;i<classSel.options.length;i++){
                                                if(classSel.options[i].value === k) { exists = true; break; }
                                            }
                                            if(!exists){
                                                var opt = document.createElement('option'); opt.value = k; opt.text = k;
                                                if('<?php echo addslashes(s_val('clasificacion')); ?>' === k) opt.selected = true;
                                                classSel.appendChild(opt);
                                            }
                                        });
                                    }

                                    function findMatchingRemoteProduct(monto, cls){
                                        if(!productosRemote || !productosRemote.length) return null;
                                        var matches = [];
                                        for(var i=0;i<productosRemote.length;i++){
                                            var p = productosRemote[i];
                                            var optCls = p.clasificacion || p.tipo || p.category || p.clasificacion_producto || '';
                                            if(cls && optCls && optCls !== cls) continue;

                                            // try many possible field names for min/max
                                            var rawMin = (p.monto_min !== undefined ? p.monto_min : (p.min_monto !== undefined ? p.min_monto : (p.min !== undefined ? p.min : (p.minimo !== undefined ? p.minimo : (p.minAmount !== undefined ? p.minAmount : null)))));
                                            var rawMax = (p.monto_max !== undefined ? p.monto_max : (p.max_monto !== undefined ? p.max_monto : (p.max !== undefined ? p.max : (p.maximo !== undefined ? p.maximo : (p.maxAmount !== undefined ? p.maxAmount : null)))));
                                            var min = safeNum(rawMin);
                                            var max = safeNum(rawMax);

                                            // If neither bound exists, skip: we require at least one explicit bound
                                            if(min === null && max === null) continue;

                                            // Check inclusive bounds
                                            if(min !== null && max !== null){ if(monto < min || monto > max) continue; }
                                            else if(min !== null){ if(monto < min) continue; }
                                            else if(max !== null){ if(monto > max) continue; }

                                            // compute a score to prefer tighter ranges: smaller width preferred
                                            var width = null;
                                            if(min !== null && max !== null){ width = (max - min); }
                                            else if(min !== null){ width = Infinity; }
                                            else if(max !== null){ width = Infinity; }

                                            matches.push({ prod: p, min: min, max: max, width: width });
                                        }

                                        if(!matches.length) return null;

                                        // sort: prefer entries with finite width (both bounds), then smaller width, then larger min
                                        matches.sort(function(a,b){
                                            var aw = isFinite(a.width) ? 0 : 1;
                                            var bw = isFinite(b.width) ? 0 : 1;
                                            if(aw !== bw) return aw - bw;
                                            if(a.width !== b.width) return (a.width || 0) - (b.width || 0);
                                            // prefer larger min (more specific to the monto)
                                            var amin = (a.min !== null ? a.min : -Infinity);
                                            var bmin = (b.min !== null ? b.min : -Infinity);
                                            return bmin - amin;
                                        });

                                        return matches[0].prod;
                                    }

                                    function updateSuggestionPanel(){
                                        if(!sugeridoBody) return;
                                        var monto = parseFloat((montoInput && montoInput.value) ? montoInput.value : 0) || 0;
                                        var cls = (classSel && classSel.value) ? classSel.value : '';
                                        var p = findMatchingRemoteProduct(monto, cls);
                                        if(!p){
                                            sugeridoBody.innerHTML = '<div><em>No hay producto disponible para este monto y clasificación.</em></div>';
                                            // clear any previously filled tasa/comision/plazo in visible fields
                                            try{ var vf = document.querySelector('input[name="tasa_interes"]'); if(vf) vf.value = ''; }catch(e){}
                                            try{ var vf2 = document.querySelector('input[name="comision_desembolso"]'); if(vf2) vf2.value = ''; }catch(e){}
                                            try{ var vf3 = document.querySelector('input[name="plazo_meses"]'); if(vf3) vf3.value = ''; }catch(e){}
                                            // also clear hidden product placeholders if present
                                            try{ if(document.getElementById('producto_tasa')) document.getElementById('producto_tasa').value = ''; }catch(e){}
                                            try{ if(document.getElementById('producto_comision')) document.getElementById('producto_comision').value = ''; }catch(e){}
                                            try{ if(document.getElementById('producto_plazo')) document.getElementById('producto_plazo').value = ''; }catch(e){}
                                            return;
                                        }
                                        var nombre = p.nombre || p.nombre_producto || p.title || p.name || 'Producto';
                                        var min = p.min_monto || p.min || p.minimo || '';
                                        var max = p.max_monto || p.max || p.maximo || '';
                                        var tasa = p.tasa_interes || p.tasa || p.interes || p.tasa_mensual || '';
                                        var com = p.comision_desembolso || p.comision || p.comision_desc || '';
                                        var plazo = p.plazo_max || p.plazo || p.plazo_maximo || '';

                                        var html = '<div style="font-weight:600; margin-bottom:6px;">' + nombre + '</div>';
                                        html += '<div style="font-size:13px; color:#666; margin-bottom:6px;">Clasificación: ' + (p.clasificacion||'') + '</div>';
                                        html += '<div style="font-size:13px; color:#333; margin-bottom:8px;">Monto aplicable: ' + (min!==''?min:'-') + ' - ' + (max!==''?max:'-') + '</div>';
                                        html += '<div style="font-size:13px; color:#333;">Tasa interés: ' + (tasa!==''?tasa+'%':'-') + '</div>';
                                        html += '<div style="font-size:13px; color:#333;">Porcentaje desembolso: ' + (p.porcentaje_desembolso || com || '-') + '</div>';
                                        html += '<div style="margin-top:8px;"><label><input type="checkbox" id="producto_sugerido_select_cb"> Seleccionar este producto</label></div>';
                                        sugeridoBody.innerHTML = html;

                                        // wire checkbox
                                        var cb = document.getElementById('producto_sugerido_select_cb');
                                        if(cb){
                                            cb.addEventListener('change', function(){
                                                if(cb.checked){
                                                    // set hidden product select if exists
                                                    try{ if(sel) sel.value = p.id || p.ID || p.id_producto || '';}catch(e){}
                                                    // fill hidden tasa/comision/plazo
                                                    try{ if(document.getElementById('producto_tasa')) document.getElementById('producto_tasa').value = tasa; }catch(e){}
                                                    try{ if(document.getElementById('producto_comision')) document.getElementById('producto_comision').value = (p.comision_desembolso||com||''); }catch(e){}
                                                    try{ if(document.getElementById('producto_plazo')) document.getElementById('producto_plazo').value = plazo; }catch(e){}
                                                    // also fill visible form fields (if present)
                                                    try{ var vf = document.querySelector('input[name="tasa_interes"]'); if(vf) vf.value = tasa; }catch(e){}
                                                    try{ var vf2 = document.querySelector('input[name="comision_desembolso"]'); if(vf2) vf2.value = (p.comision_desembolso||com||''); }catch(e){}
                                                    try{ var vf3 = document.querySelector('input[name="plazo_meses"]'); if(vf3) vf3.value = plazo; }catch(e){}
                                                }
                                            }, false);
                                        }
                                    }
                                })();
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

