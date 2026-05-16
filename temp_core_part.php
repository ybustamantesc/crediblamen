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
