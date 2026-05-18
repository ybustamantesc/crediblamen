<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php // Minimal Solicitud Inicial skeleton (layout restored)
// Backup: tools/backup/solicitudes_core_backup_20251215.php
?>

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
if (! function_exists('s_checked')) {
    function s_checked($field){
        $v = s_val($field);
        return ($v === '1' || $v === 1 || $v === true) ? 'checked' : '';
    }
}
if (! function_exists('s_date_fmt')){
    function s_date_fmt($field, $fmt='Y-m-d', $fallback=''){
        $v = s_val($field, $fallback);
        if (!$v) return $fallback;
        $d = date_create($v);
        return $d ? date_format($d, $fmt) : $fallback;
    }
}
?>

<?php
// Obtener información del usuario logueado para autocompletar campos de promotor
$current_user = null;
$current_user_name = '';
$is_current_user_promotor = false;

try {
    if (isset($this->ion_auth) && method_exists($this->ion_auth, 'user')) {
        $current_user = $this->ion_auth->user()->row();
        if ($current_user) {
            // Construir nombre completo del usuario
            $current_user_name = trim(($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
            if (empty($current_user_name)) {
                $current_user_name = $current_user->username ?? $current_user->email ?? '';
            }
            
            // Verificar si es promotor (grupo 'promotor' o perfil == 4)
            if (method_exists($this->ion_auth, 'in_group')) {
                $is_current_user_promotor = $this->ion_auth->in_group('promotor');
            }
            if (!$is_current_user_promotor && isset($current_user->perfil)) {
                $is_current_user_promotor = (intval($current_user->perfil) === 4);
            }
        }
    }
} catch (Exception $e) {
    // Silently fail
}

// Valor por defecto para nombre_promotor
$default_nombre_promotor = '';
if (!empty($current_user_name)) {
    $default_nombre_promotor = $current_user_name;
}
?>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-file-signature'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo isset($titulo) ? $titulo : 'Solicitud Inicial'; ?></h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : ''; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <!-- action buttons -->
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                                <?php if (isset($solicitud) && isset($solicitud->idsolicitud)): ?>
                                    <div class="alert alert-info" style="font-size:13px;">
                                        <strong>Datos guardados (debug):</strong>
                                        &nbsp; ID: <?php echo intval($solicitud->idsolicitud); ?>
                                        &nbsp; | &nbsp; `propuesta_tipos`: <?php echo htmlspecialchars(isset($solicitud->propuesta_tipos) ? $solicitud->propuesta_tipos : ''); ?>
                                        &nbsp; | &nbsp; `tasa_interes`: <?php echo htmlspecialchars(isset($solicitud->tasa_interes) ? $solicitud->tasa_interes : ''); ?>
                                    </div>
                                <?php endif; ?>
                        <form id="solicitud_form" method="post" action="<?php echo current_url(); ?>" enctype="multipart/form-data">
                            <style>
                            .bloqueado-readonly {
                                background: #f5f5f5 !important;
                                color: #888 !important;
                                pointer-events: none !important;
                                border-color: #e0e0e0 !important;
                            }
                            </style>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var aprob = <?php echo json_encode(isset($solicitud->aprob_status) ? $solicitud->aprob_status : 'pending'); ?>;
                                if (aprob === 'approved' || aprob === 'rejected') {
                                    var form = document.getElementById('solicitud_form');
                                    if (form) {
                                        var fields = form.querySelectorAll('input, select, textarea');
                                        fields.forEach(function(f) {
                                            if (!f.hasAttribute('data-allow')) {
                                                if (f.type !== 'hidden') {
                                                    f.disabled = true;
                                                    f.readOnly = true;
                                                    f.classList.add('bloqueado-readonly');
                                                }
                                            }
                                        });
                                        // Disable all buttons except navigation/cancel
                                        var buttons = form.querySelectorAll('button');
                                        buttons.forEach(function(b) {
                                            if (!b.hasAttribute('data-allow')) {
                                                b.disabled = true;
                                                b.classList.add('bloqueado-readonly');
                                            }
                                        });
                                    }
                                }
                            });
                            </script>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var aprob = <?php echo json_encode(isset($solicitud->aprob_status) ? $solicitud->aprob_status : 'pending'); ?>;
                                if (aprob === 'approved' || aprob === 'rejected') {
                                    var form = document.getElementById('solicitud_form');
                                    if (form) {
                                        var fields = form.querySelectorAll('input, select, textarea');
                                        fields.forEach(function(f) {
                                            if (!f.hasAttribute('data-allow')) {
                                                if (f.type !== 'hidden') {
                                                    f.disabled = true;
                                                    f.readOnly = true;
                                                    f.classList.add('bloqueado-readonly');
                                                }
                                            }
                                        });
                                        // Disable all buttons except navigation/cancel
                                        var buttons = form.querySelectorAll('button');
                                        buttons.forEach(function(b) {
                                            if (!b.hasAttribute('data-allow')) {
                                                b.disabled = true;
                                                b.classList.add('bloqueado-readonly');
                                            }
                                        });
                                    }
                                }
                            });
                            </script>
                            <!-- Hidden fields to persist selected producto proposal and product attributes -->
                            <input type="hidden" id="producto_select_hidden" name="producto_select" value="<?php echo htmlspecialchars(isset($solicitud->propuesta_tipos) ? $solicitud->propuesta_tipos : ''); ?>">
                            <input type="hidden" id="producto_tasa" name="producto_tasa" value="<?php echo htmlspecialchars(isset($solicitud->producto_tasa) ? $solicitud->producto_tasa : (isset($solicitud->tasa_interes) ? $solicitud->tasa_interes : '')); ?>">
                            <input type="hidden" id="producto_comision" name="producto_comision" value="<?php echo htmlspecialchars(isset($solicitud->producto_comision) ? $solicitud->producto_comision : (isset($solicitud->comision_desembolso) ? $solicitud->comision_desembolso : '')); ?>">
                            <input type="hidden" id="producto_plazo" name="producto_plazo" value="<?php echo htmlspecialchars(isset($solicitud->producto_plazo) ? $solicitud->producto_plazo : (isset($solicitud->plazo_meses) ? $solicitud->plazo_meses : '')); ?>">
                            <input type="hidden" id="propuesta_tipos" name="propuesta_tipos" value="<?php echo htmlspecialchars(isset($solicitud->propuesta_tipos) ? $solicitud->propuesta_tipos : ''); ?>">

                            <!-- Word-mode header template: visual-only area that sits above the form fields -->
                            <div class="word-mode-template mb-3" style="background:#1f1f1f;color:#fff;padding:18px;border-radius:6px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <div>
                                        <h3 style="margin:0;padding:0;font-weight:700;letter-spacing:1px;">FORMATO DE SOLICITUD INICIAL DE CRÉDITO</h3>
                                        <div style="margin-top:6px;font-size:13px;opacity:0.9">INFORMACIÓN DEL CRÉDITO SOLICITADO</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <label style="color:#fff;margin-right:8px;">Nuevo <input type="checkbox" id="tmpl_nuevo" name="es_nuevo" <?php echo (s_val('es_nuevo') ? 'checked' : ''); ?>></label>
                                        <label style="color:#fff;">Renovación <input type="checkbox" id="tmpl_renovacion" name="es_renovacion" <?php echo (s_val('es_renovacion') ? 'checked' : ''); ?>></label>
                                    </div>
                                </div>

                                <div style="margin-top:12px;background:#fff;color:#000;padding:12px;border-radius:4px;">
                                    <div style="height:48px; border:1px solid #ddd; padding:6px; overflow:auto;">Giro del Negocio: <span id="tmpl_giro_negocio_display"><?php echo htmlspecialchars(s_val('giro_negocio', '')); ?></span></div>
                                </div>

                                <div style="margin-top:10px;">
                                    <style>
                                        /* Responsive stack for Tipo de Crédito / Destino Conami / Destino */
                                        .tmpl-selects{ display:flex; gap:12px; align-items:center; }
                                        .tmpl-selects > div { min-width:220px; }
                                        .tmpl-selects > div.flex1 { flex:1; min-width:0; }
                                        @media (max-width: 767.98px) {
                                            .tmpl-selects { flex-direction: column !important; }
                                            .tmpl-selects > div { min-width: 0 !important; width: 100% !important; }
                                        }
                                    </style>
                                    <div class="tmpl-selects">
                                        <div style="min-width:220px;">
                                            <label style="font-size:13px;color:#ddd;display:block;margin-bottom:4px;">Tipo de Crédito</label>
                                            <select name="tipo_credito" id="tipo_credito" class="form-control">
                                                <option value="">-- Seleccione tipo --</option>
                                                <option value="Créditos Personales" <?php echo (s_val('tipo_credito')=='Créditos Personales'?'selected':''); ?>>Créditos Personales</option>
                                                <option value="Créditos Hipotecarios" <?php echo (s_val('tipo_credito')=='Créditos Hipotecarios'?'selected':''); ?>>Créditos Hipotecarios</option>
                                                <option value="Microcréditos" <?php echo (s_val('tipo_credito')=='Microcréditos'?'selected':''); ?>>Microcréditos</option>
                                            </select>
                                        </div>
                                        <div class="flex1" style="flex:1;">
                                            <label style="font-size:13px;color:#ddd;display:block;margin-bottom:4px;">Destino Conami</label>
                                            <select name="rubro_credito" id="rubro_credito" class="form-control">
                                                <option value="">-- Seleccione rubro --</option>
                                                <?php
                                                $rubros = array('Agricultura','Ganadería','Industria','Comercio','Turismo','Vivienda (Mejora, Ampliación, Remodelación, Otros)','Servicios','Transporte','Personales (Asalariados)','Pesca','Construcción','Hipotecario','Tarjetas de Crédito');
                                                foreach($rubros as $r){
                                                    $sel = (s_val('rubro_credito') == $r) ? 'selected' : '';
                                                    echo '<option value="'.htmlspecialchars($r).'" '.$sel.'>'.htmlspecialchars($r).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div style="min-width:220px;">
                                            <label style="font-size:13px;color:#ddd;display:block;margin-bottom:4px;">Destino</label>
                                            <select name="destino_credito" id="destino_credito" class="form-control">
                                                <option value="">-- Seleccione destino --</option>
                                                <option value="Consumo" <?php echo (s_val('destino_credito')=='Consumo'?'selected':''); ?>>Consumo</option>
                                                <option value="Inversión" <?php echo (s_val('destino_credito')=='Inversión'?'selected':''); ?>>Inversión</option>
                                                <option value="Capital de trabajo" <?php echo (s_val('destino_credito')=='Capital de trabajo'?'selected':''); ?>>Capital de trabajo</option>
                                                <option value="Construcción" <?php echo (s_val('destino_credito')=='Construcción'?'selected':''); ?>>Construcción</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top:10px;font-size:14px;">
                                    <span style="display:inline-block;width:250px;">Monto solicitado: U$ <strong id="tmpl_monto"><?php echo htmlspecialchars(s_val('monto_solicitado', '')); ?></strong></span>
                                    <span style="display:inline-block;width:220px;">Plazo: <strong id="tmpl_plazo"><?php echo htmlspecialchars(s_val('plazo_meses', '')); ?></strong> meses</span>
                                    <span style="display:inline-block;width:200px;">Frecuencia: <strong id="tmpl_frecuencia"><?php echo htmlspecialchars(s_val('frecuencia', '')); ?></strong></span>
                                </div>

                                <div style="margin-top:8px;font-size:14px;">
                                    <span style="display:inline-block;width:300px;">Tasa de interés a cobrar: <strong id="tmpl_tasa"><?php echo htmlspecialchars(s_val('tasa_interes','')); ?></strong> %</span>
                                    <span style="display:inline-block;width:300px;">Promedio de cuota estimada: U$ <strong id="tmpl_cuota"><?php echo htmlspecialchars(s_val('cuota_estimado','')); ?></strong></span>
                                </div>

                                <div style="margin-top:8px;font-size:13px;color:#ddd;">
                                    Garantía ofrecida: <span id="tmpl_garantias_display"><?php
                                        $g = [];
                                        if (s_val('garantia_hipotecaria')) $g[] = 'Hipotecaria';
                                        if (s_val('garantia_prendaria')) $g[] = 'Prendaria';
                                        if (s_val('garantia_fiador')) $g[] = 'Fiador';
                                        if (s_val('garantia_otra')) $g[] = 'Otra';
                                        echo htmlspecialchars(implode(', ', $g));
                                        ?></span>
                                </div>
                                <div style="margin-top:6px;font-size:13px;color:#ddd;">
                                    Es rural: <span id="tmpl_es_rural_display"><?php echo ((string)s_val('es_rural', '0') === '1') ? 'Sí' : 'No'; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <?php if (isset($solicitud) && isset($solicitud->idsolicitud)): ?>
                                    <!-- Hidden field populated from modal on submit -->
                                    <input type="hidden" name="edit_comment" id="edit_comment_hidden" value="">
                                <?php endif; ?>

                                            <div class="col-md-12"><div class="form-group"><label>Giro del Negocio</label><input id="giro_negocio" type="text" class="form-control" name="giro_negocio" value="<?php echo s_val('giro_negocio', set_value('giro_negocio')); ?>" placeholder="Escriba el giro del negocio"></div></div>

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
                                        // Normalize common typo/label: 'Personas' should be 'Personal'
                                        if (!empty($clasificaciones) && isset($clasificaciones['Personas'])){
                                            unset($clasificaciones['Personas']);
                                            $clasificaciones['Personal'] = 'Personal';
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

                                    <div class="col-md-3"><div class="form-group"><label>Plazo (meses)</label>
                                            <input id="plazo_meses" type="number" class="form-control" name="plazo_meses" value="<?php echo s_val('plazo_meses', set_value('plazo_meses')); ?>" min="1">
                                            <div id="plazo_range_info" style="font-size:12px; color:#666; margin-top:6px;">Plazo: <span id="plazo_min">-</span> - <span id="plazo_max">-</span> meses</div>
                                            <div id="plazo_msg" style="font-size:13px; margin-top:6px; display:none;"></div>
                                        </div></div>
                                    <input type="hidden" id="frecuencia" name="frecuencia" value="Mensual">
                                    <div class="col-md-3"><div class="form-group"><label>Tasa de interés a cobrar (%)</label>
                                            <?php
                                            $raw_tasa = s_val('tasa_interes', set_value('tasa_interes'));
                                            $tasa_disp = $raw_tasa;
                                            if (is_numeric($raw_tasa)){
                                                $tv = floatval($raw_tasa);
                                                if ($tv > 0 && $tv <= 1) { $tasa_disp = $tv * 100; }
                                            }
                                            // trim trailing zeros
                                            if (is_numeric($tasa_disp)) {
                                                $tasa_disp = rtrim(rtrim(number_format((float)$tasa_disp, 2, '.', ''), '0'), '.');
                                            }
                                            ?>
                                            <input id="tasa_interes" type="number" step="0.01" class="form-control" name="tasa_interes" value="<?php echo htmlspecialchars($tasa_disp); ?>" readonly>
                                        </div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Porcentaje desembolso (%)</label>
                                            <?php
                                            // Prefer producto_comision (hidden field) if present, otherwise stored comision_desembolso
                                            $raw_com = s_val('producto_comision', s_val('comision_desembolso', set_value('comision_desembolso')));
                                            $com_disp = $raw_com;
                                            if (is_numeric($raw_com)){
                                                $cv = floatval($raw_com);
                                                if ($cv > 0 && $cv <= 1) { $com_disp = $cv * 100; }
                                            }
                                            if (is_numeric($com_disp)) { $com_disp = rtrim(rtrim(number_format((float)$com_disp, 2, '.', ''), '0'), '.'); }
                                            ?>
                                            <input id="comision_desembolso" type="number" step="0.01" class="form-control" name="comision_desembolso" value="<?php echo htmlspecialchars($com_disp); ?>" readonly>
                                        </div></div>
                                    <div class="col-md-4"><div class="form-group">
                                            <label>Promedio de cuota estimada (mensual): U$</label>
                                            <div class="input-group">
                                                <input id="cuota_estimado" type="text" class="form-control" name="cuota_estimado" value="<?php echo s_val('cuota_estimado', set_value('cuota_estimado')); ?>" readonly>
                                                <div class="input-group-append"><button id="btn_procesar_cuota" type="button" class="btn btn-secondary">Procesar cuota</button></div>
                                            </div>
                                            <label style="margin-top:8px;">Promedio de cuota estimada quincenal/catorcenal: U$</label>
                                            <input id="cuota_estimado_quincenal" type="text" class="form-control" name="cuota_estimado_quincenal" readonly>
                                            <div id="plan_pago_preview" style="font-size:13px; color:#333; margin-top:6px;"></div>
                                        </div></div>

                                    <div class="col-md-12 mt-2"><label>Garantía ofrecida:</label>
                                        <div class="d-flex" style="gap:12px;">
                                            <label><input type="checkbox" name="garantia_hipotecaria" value="25" <?php echo s_checked('garantia_hipotecaria'); ?>> Garantía Hipotecaria (25)</label>
                                            <label><input type="checkbox" name="garantia_mobiliaria" value="50" <?php echo s_checked('garantia_mobiliaria'); ?>> Garantía Mobiliaria (50)</label>
                                            <label><input type="checkbox" name="garantia_sin" value="100" <?php echo s_checked('garantia_sin'); ?>> Sin garantía (100)</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <div class="form-group">
                                            <label>¿Es rural?</label>
                                            <select class="form-control" id="es_rural" name="es_rural">
                                                <option value="0" <?php echo ((string)s_val('es_rural', '0') === '0') ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo ((string)s_val('es_rural', '0') === '1') ? 'selected' : ''; ?>>Sí</option>
                                            </select>
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

                                    <!-- Producto fijo (si ya existe en la solicitud) -->
                                    <div id="producto_fijo_panel" class="col-md-3" style="float:right; width:300px; margin-left:12px; margin-top:8px;">
                                        <?php if (!empty($solicitud) && !empty($solicitud->propuesta_tipos)): ?>
                                            <?php
                                                $fixed_tasa = s_val('producto_tasa', s_val('tasa_interes'));
                                                $fixed_com = s_val('producto_comision', s_val('comision_desembolso', set_value('comision_desembolso')));
                                                $fixed_plazo = s_val('producto_plazo', s_val('plazo_meses'));
                                            ?>
                                            <div style="border:1px solid #d6ffd6; padding:10px; border-radius:6px; background:#f7fff7;">
                                                <strong>Producto fijado (guardado)</strong>
                                                <div style="font-size:13px; margin-top:6px;">ID(s): <?php echo htmlspecialchars($solicitud->propuesta_tipos); ?></div>
                                                    <?php
                                                        // format tasa and comision for display: if stored as decimal (0.08) show 8, else show raw
                                                        $display_tasa = '';
                                                        if (isset($fixed_tasa) && $fixed_tasa !== '') {
                                                            if (is_numeric($fixed_tasa)) {
                                                                $tv = floatval($fixed_tasa);
                                                                if ($tv > 0 && $tv <= 1) $tv = $tv * 100;
                                                                $display_tasa = rtrim(rtrim(number_format((float)$tv, 2, '.', ''), '0'), '.');
                                                            } else { $display_tasa = $fixed_tasa; }
                                                        }
                                                        $display_com = '';
                                                        if (isset($fixed_com) && $fixed_com !== '') {
                                                            if (is_numeric($fixed_com)) {
                                                                $cv = floatval($fixed_com);
                                                                if ($cv > 0 && $cv <= 1) $cv = $cv * 100;
                                                                $display_com = rtrim(rtrim(number_format((float)$cv, 2, '.', ''), '0'), '.');
                                                            } else { $display_com = $fixed_com; }
                                                        }
                                                    ?>
                                                    <div style="font-size:13px;">Tasa: <?php echo htmlspecialchars($display_tasa); ?></div>
                                                    <div style="font-size:13px;">% Desembolso: <?php echo htmlspecialchars($display_com); ?></div>
                                                    <div style="font-size:13px;">Plazo: <?php echo htmlspecialchars($fixed_plazo); ?> meses</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- SECTION 1: Datos Personales (placeholder) -->
                                    <div class="col-md-12 mt-1">
                                        <h5>DATOS GENERALES DEL CLIENTE</h5>
                                        <div id="section-1">
                                            <div class="row">
                                                <div class="col-md-3"><div class="form-group"><label>Fecha de solicitud</label><input type="datetime-local" class="form-control" name="fecha_solicitud" value="<?php echo s_date_fmt('fecha_solicitud', 'Y-m-d\TH:i', set_value('fecha_solicitud')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Nombre completo <span class="text-danger">*</span></label><input type="text" required class="form-control" name="nombre_completo" id="nombre_completo_input" style="text-transform:uppercase;" value="<?php echo s_val('nombre_completo', trim(s_val('apellidos','') . ' ' . s_val('nombres','')) ?: set_value('nombre_completo')); ?>"></div></div>
                                                <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    var nombreInput = document.getElementById('nombre_completo_input');
                                                    if(nombreInput) {
                                                        nombreInput.addEventListener('input', function() {
                                                            this.value = this.value.toUpperCase();
                                                        });
                                                    }
                                                    var conyugeInput = document.getElementById('nombre_conyuge_input');
                                                    if(conyugeInput) {
                                                        conyugeInput.addEventListener('input', function() {
                                                            this.value = this.value.toUpperCase();
                                                        });
                                                    }
                                                    var negocioInput = document.getElementById('nombre_negocio_input');
                                                    if(negocioInput) {
                                                        negocioInput.addEventListener('input', function() {
                                                            this.value = this.value.toUpperCase();
                                                        });
                                                    }
                                                });
                                                </script>
                                                <div class="col-md-3"><div class="form-group"><label>Cédula de identidad</label>
                                                    <input id="numero_doc_input" type="text" data-ci-format="0000000000000X" class="form-control" name="numero_doc" value="<?php echo s_val('numero_doc', set_value('numero_doc')); ?>" placeholder="Sin guiones, ejemplo: 0000000000000X">
                                                    <div id="numero_doc_hint" style="display:block;margin-top:6px;color:#a00;font-size:12px;">No uses giones</div>
                                                    <div id="numero_doc_actions" style="display:none;margin-top:6px;">
                                                        <button type="button" id="btn_import_solicitud" class="btn btn-sm btn-outline-secondary">Importar desde última Solicitud</button>
                                                    </div>
                                                </div></div>

                                                <div class="col-md-3"><div class="form-group"><label>Fecha de nacimiento</label><input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo s_date_fmt('fecha_nacimiento', 'Y-m-d', set_value('fecha_nacimiento')); ?>"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Edad</label><input type="number" class="form-control" name="edad" value="<?php echo s_val('edad', set_value('edad')); ?>"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Sexo</label>
                                                    <select class="form-control" name="sexo">
                                                        <option value="">-- Seleccione --</option>
                                                        <option value="F" <?php echo (s_val('sexo', set_value('sexo')) == 'F' ? 'selected' : ''); ?>>F</option>
                                                        <option value="M" <?php echo (s_val('sexo', set_value('sexo')) == 'M' ? 'selected' : ''); ?>>M</option>
                                                    </select>
                                                </div></div>
                                                <div class="col-md-5"><div class="form-group"><label>Estado civil</label>
                                                    <select class="form-control" name="estado_civil">
                                                        <option value="">-- Seleccione --</option>
                                                        <option value="Soltero" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Soltero' ? 'selected' : ''); ?>>Soltero(a)</option>
                                                        <option value="Casado" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Casado' ? 'selected' : ''); ?>>Casado(a)</option>
                                                        <option value="Union libre" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Union libre' ? 'selected' : ''); ?>>Unión libre</option>
                                                        <option value="Divorciado" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Divorciado' ? 'selected' : ''); ?>>Divorciado(a)</option>
                                                        <option value="Viudo" <?php echo (s_val('estado_civil', set_value('estado_civil')) == 'Viudo' ? 'selected' : ''); ?>>Viudo(a)</option>
                                                    </select>
                                                </div></div>

                                                <div class="col-md-6"><div class="form-group"><label>Nombre del cónyuge o pareja</label><input type="text" class="form-control" name="nombre_conyuge" id="nombre_conyuge_input" style="text-transform:uppercase;" value="<?php echo s_val('nombre_conyuge', set_value('nombre_conyuge')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Cédula del cónyuge o pareja</label><input type="text" class="form-control" name="dni_conyuge" value="<?php echo s_val('dni_conyuge', set_value('dni_conyuge')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Ocupación del cónyuge o pareja</label><input type="text" class="form-control" name="ocupacion_conyuge" value="<?php echo s_val('ocupacion_conyuge', set_value('ocupacion_conyuge')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Ingresos del cónyuge</label><input type="number" min="0" step="any" class="form-control" name="ingresos_conyuge" value="<?php echo s_val('ingresos_conyuge', set_value('ingresos_conyuge')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Teléfono del cónyuge o pareja</label><input type="text" class="form-control" name="telefono_conyuge" value="<?php echo s_val('telefono_conyuge', set_value('telefono_conyuge')); ?>"></div></div>
                                                <div class="col-md-3"><div class="form-group"><label>Número de dependientes</label><input type="number" class="form-control" name="numero_dependientes" value="<?php echo s_val('numero_dependientes', set_value('numero_dependientes')); ?>"></div></div>

                                                <div class="col-md-6"><div class="form-group"><label>Teléfono(s) del solicitante</label><input type="text" class="form-control" name="telefono" value="<?php echo s_val('telefono', set_value('telefono')); ?>"></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Dirección exacta de domicilio</label><textarea id="direccion" rows="2" class="form-control" name="direccion"><?php echo s_val('direccion', set_value('direccion')); ?></textarea></div></div>

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
                                    <div id="section-laboral-asalariado" class="row" style="display:none;">
                                        <div class="col-md-12 mt-4">
                                            <h5>INFORMACIÓN LABORAL (CLIENTE ASALARIADO)</h5>
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
                                    </div>

                                    <!-- SECTION 3: INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO) -->
                                    <div id="section-negocio-comerciante" class="w-100 d-flex flex-wrap">
                                    <div class="col-md-12 mt-4">
                                        <h5>INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO)</h5>
                                    </div>
                                    <div class="col-md-6"><div class="form-group"><label>Nombre del negocio</label><input type="text" class="form-control" name="nombre_negocio" value="<?php echo s_val('nombre_negocio', set_value('nombre_negocio')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Actividad económica principal</label><input type="text" class="form-control" name="actividad_economica" value="<?php echo s_val('actividad_economica', set_value('actividad_economica')); ?>"></div></div>
                                    <div class="col-md-12"><div class="form-group">
                                        <label style="display:flex;align-items:center;gap:12px;">Ubicación del negocio
                                            <span style="font-weight:normal;font-size:0.9rem;margin-left:8px;">
                                                <label style="margin:0; font-weight:normal;"><input type="checkbox" id="ubicacion_same" <?php echo (s_val('ubicacion_negocio') && s_val('direccion') && s_val('ubicacion_negocio')==s_val('direccion') ? 'checked' : ''); ?>> Es la misma</label>
                                            </span>
                                        </label>
                                        <input type="text" id="ubicacion_negocio" class="form-control" name="ubicacion_negocio" value="<?php echo s_val('ubicacion_negocio', set_value('ubicacion_negocio')); ?>">
                                    </div></div>

                                    <script>
                                    (function(){
                                        var chk = document.getElementById('ubicacion_same');
                                        var dir = document.getElementById('direccion');
                                        var ubi = document.getElementById('ubicacion_negocio');
                                        function syncFromDir(){ if(dir && ubi){ ubi.value = dir.value; ubi.readOnly = true; } }
                                        if (chk && dir && ubi){
                                            // initial state
                                            if (chk.checked){ syncFromDir(); } else { ubi.readOnly = false; }
                                            chk.addEventListener('change', function(){
                                                if (this.checked){ syncFromDir(); } else { ubi.value = ''; ubi.readOnly = false; }
                                            });
                                            dir.addEventListener('input', function(){ if (chk.checked) ubi.value = dir.value; });
                                        }
                                    })();
                                    </script>
                                    <script>
                                        // Cedula (numero_doc) validation and Nuevo/Renovacion enforcement
                                        (function(){
                                            function buildRegexFromFormat(fmt){
                                                // fmt like '0000000000000X' -> digits = length-1, final letter
                                                if(!fmt || typeof fmt !== 'string') fmt = '0000000000000X';
                                                var digits = fmt.length - 1;
                                                return new RegExp('^\\d{' + digits + '}[A-Za-z]$');
                                            }

                                            function stripHyphens(v){ return (v||'').replace(/[-\s]/g,''); }

                                            document.addEventListener('DOMContentLoaded', function(){
                                                var numEl = document.getElementById('numero_doc_input');
                                                var hint = document.getElementById('numero_doc_hint');
                                                var form = document.getElementById('solicitud_form');
                                                var tmplNuevo = document.getElementById('tmpl_nuevo');
                                                var tmplRen = document.getElementById('tmpl_renovacion');

                                                // make Nuevo/Renovacion behave like radio (only one selectable)
                                                if(tmplNuevo && tmplRen){
                                                    tmplNuevo.addEventListener('change', function(){ if(this.checked) tmplRen.checked = false; });
                                                    tmplRen.addEventListener('change', function(){ if(this.checked) tmplNuevo.checked = false; });
                                                }

                                                if(numEl){
                                                    var fmt = numEl.getAttribute('data-ci-format') || '0000000000000X';
                                                    var re = buildRegexFromFormat(fmt);
                                                    // Show example in hint (preserve the 'No uses giones' message)
                                                    hint.innerText = 'No uses giones. Formato esperado: ' + fmt + ' (ej: ' + fmt + ')';

                                                    numEl.addEventListener('input', function(){
                                                        var cleaned = stripHyphens(this.value);
                                                        if(cleaned !== this.value) this.value = cleaned;
                                                        // live validation style
                                                        if(re.test(this.value)){
                                                            hint.style.color = '#099';
                                                            hint.innerText = 'Formato válido';
                                                        } else {
                                                            hint.style.color = '#a00';
                                                            hint.innerText = 'No uses giones. Formato esperado: ' + fmt + ' (ej: ' + fmt + ')';
                                                        }
                                                    });
                                                }

                                                if(form){
                                                    form.addEventListener('submit', function(e){
                                                        // Validate numero_doc
                                                        if(numEl){
                                                            var fmt = numEl.getAttribute('data-ci-format') || '0000000000000X';
                                                            var re = buildRegexFromFormat(fmt);
                                                            var v = stripHyphens(numEl.value || '');
                                                            if(v === '' || !re.test(v)){
                                                                e.preventDefault();
                                                                hint.style.color = '#a00';
                                                                hint.innerText = 'Cédula inválida o incompleta. Use el formato: ' + fmt + ' (sin guiones)';
                                                                numEl.focus();
                                                                return false;
                                                            }
                                                        }

                                                        // Ensure one of Nuevo / Renovación is selected
                                                        try{
                                                            var n = document.querySelector('input[name="es_nuevo"]');
                                                            var r = document.querySelector('input[name="es_renovacion"]');
                                                            var nchecked = n ? !!n.checked : false;
                                                            var rchecked = r ? !!r.checked : false;
                                                            if(!nchecked && !rchecked){
                                                                e.preventDefault();
                                                                alert('Debe seleccionar al menos uno: Nuevo o Renovación');
                                                                if(n) n.focus();
                                                                return false;
                                                            }
                                                        }catch(err){}
                                                    });
                                                }
                                            });
                                        })();
                                    </script>
                                    <script>
                                    (function(){
                                        // Validator: check if numero_doc exists and offer import
                                        var input = document.getElementById('numero_doc_input');
                                        var hint = document.getElementById('numero_doc_hint');
                                        var actions = document.getElementById('numero_doc_actions');
                                        var btnSol = document.getElementById('btn_import_solicitud');
                                        var lastResponse = null;

                                        function showHint(msg, klass){
                                            hint.style.display = 'block';
                                            hint.className = 'text-' + (klass || 'muted');
                                            hint.innerText = msg;
                                        }
                                        function hideHint(){ hint.style.display = 'none'; hint.innerText = ''; }

                                        function hideActions(){ actions.style.display = 'none'; }
                                        function showActions(){ actions.style.display = 'block'; }

                                        function clearActions(){ lastResponse = null; hideActions(); hideHint(); }

                                        function fetchCliente(doc){
                                            if (!doc || doc.trim()==='') { clearActions(); return; }
                                            var url = '<?php echo base_url('clientes/ajax_find_by_doc'); ?>';
                                            var xhr = new XMLHttpRequest();
                                            xhr.open('GET', url + '?numero_doc=' + encodeURIComponent(doc), true);
                                            xhr.setRequestHeader('Accept','application/json');
                                            xhr.onreadystatechange = function(){
                                                if (xhr.readyState !== 4) return;
                                                if (xhr.status === 200){
                                                    try {
                                                        var res = JSON.parse(xhr.responseText || '{}');
                                                        lastResponse = res;
                                                        if (res && res.success){
                                                            showHint('Cliente existente — datos disponibles para importar', 'success');
                                                            showActions();
                                                        } else if (res && res.last_solicitud){
                                                            showHint('No se encontró cliente, pero existe una solicitud previa: puede importar sus datos', 'info');
                                                            showActions();
                                                        } else {
                                                            showHint('No existe cliente ni solicitud previa con ese documento', 'muted');
                                                            hideActions();
                                                        }
                                                    } catch (e){
                                                        showHint('Respuesta inválida del servidor', 'danger'); hideActions();
                                                    }
                                                } else {
                                                    showHint('Error consultando servidor: ' + xhr.status, 'danger'); hideActions();
                                                }
                                            };
                                            xhr.send();
                                        }

                                        function importDataFromSolicitud(){
                                            if (!lastResponse) return;
                                            var src = lastResponse.last_solicitud || lastResponse.cliente;
                                            if (!src) return;
                                            // Map requested fields
                                            var setIfFound = function(name, value){
                                                var el = document.getElementsByName(name)[0];
                                                if (!el) return;
                                                if (value !== undefined && value !== null && value !== '') {
                                                    el.value = value;
                                                }
                                            };

                                            // Personal
                                            setIfFound('nombre_completo', (src.apellidos ? src.apellidos + ' ' : '') + (src.nombres || ''));
                                            setIfFound('fecha_nacimiento', src.fecha_nacimiento || src.fecha_nac || src.fechaNac || '');
                                            setIfFound('edad', src.edad || '');
                                            // estado_civil (select)
                                            if (src.estado_civil) {
                                                var sc = document.getElementsByName('estado_civil')[0]; if (sc) sc.value = src.estado_civil;
                                            }
                                            setIfFound('nombre_conyuge', src.nombre_conyuge || src.nombre_conyugue || '');
                                            setIfFound('dni_conyuge', src.dni_conyuge || src.cedula_conyuge || '');
                                            setIfFound('ocupacion_conyuge', src.ocupacion_conyuge || '');
                                            setIfFound('telefono_conyuge', src.telefono_conyuge || '');
                                            setIfFound('numero_dependientes', src.numero_dependientes || src.numero_hijos || '');
                                            setIfFound('telefono', src.telefono || src.telefono_movil || '');
                                            setIfFound('direccion', src.direccion || src.direccion_residencia || '');
                                            // condicion_vivienda radio
                                            if (src.condicion_vivienda) {
                                                var radios = document.getElementsByName('condicion_vivienda');
                                                for (var i=0;i<radios.length;i++){ if (radios[i].value == src.condicion_vivienda) radios[i].checked = true; }
                                            }
                                            setIfFound('tiempo_residir_anios', src.tiempo_residir_anios || src.tiempo_residir || '');
                                            setIfFound('tiempo_residir_meses', src.tiempo_residir_meses || '');

                                            // Empleo
                                            setIfFound('nombre_empresa', src.nombre_empresa || src.empleador || '');
                                            setIfFound('direccion_empresa', src.direccion_empresa || '');
                                            setIfFound('telefono_empresa', src.telefono_empresa || '');
                                            setIfFound('cargo_puesto', src.cargo_puesto || src.puesto || '');
                                            setIfFound('tiempo_empleo_anios', src.tiempo_empleo_anios || '');
                                            setIfFound('tiempo_empleo_meses', src.tiempo_empleo_meses || '');
                                            // tipo de contrato -> checkboxes
                                            if (src.tipo_contrato) {
                                                var tc = (''+src.tipo_contrato).split(/[,;|]/).map(function(x){return x.trim();});
                                                ['tipo_contrato_permanente','tipo_contrato_temporal','tipo_contrato_otro'].forEach(function(k){ var el = document.getElementsByName(k)[0]; if (el) el.checked = false; });
                                                if (tc.indexOf('Permanente') !== -1 || tc.indexOf('Permanente') !== -1) { var elp = document.getElementsByName('tipo_contrato_permanente')[0]; if (elp) elp.checked = true; }
                                                if (tc.indexOf('Temporal') !== -1) { var elt = document.getElementsByName('tipo_contrato_temporal')[0]; if (elt) elt.checked = true; }
                                                if (tc.indexOf('Otro') !== -1) { var elo = document.getElementsByName('tipo_contrato_otro')[0]; if (elo) elo.checked = true; }
                                            }
                                            setIfFound('ingreso_mensual_neto', src.ingreso_mensual_neto || src.ingresos || '');
                                            setIfFound('deducciones', src.deducciones || '');

                                            // Negocio
                                            setIfFound('nombre_negocio', src.nombre_negocio || src.nombre_empresa || '');
                                            setIfFound('actividad_economica', src.actividad_economica || src.rubro_credito || '');
                                            setIfFound('telefono_negocio', src.telefono_negocio || '');
                                            setIfFound('tiempo_operacion_anios', src.tiempo_operacion_anios || '');
                                            setIfFound('tiempo_operacion_meses', src.tiempo_operacion_meses || '');

                                            // Ventas
                                            setIfFound('ventas_buenos_amount', src.ventas_buenos_amount || src.ventas_dias_buenos || '');
                                            setIfFound('ventas_malos_amount', src.ventas_malos_amount || src.ventas_dias_malos || '');
                                            setIfFound('ventas_promedio_mensual', src.ventas_promedio_mensual || src.ventas_promedio_mensual || '');

                                            // update hidden apellidos/nombres as fallback
                                            var hidA = document.getElementsByName('apellidos')[0];
                                            var hidN = document.getElementsByName('nombres')[0];
                                            if (hidA && hidN){ hidA.value = src.apellidos || ''; hidN.value = src.nombres || ''; }

                                            showHint('Datos importados desde la última solicitud correctamente', 'success');
                                        }

                                        if (input){
                                            var timer = null;
                                            input.addEventListener('input', function(){
                                                if (timer) clearTimeout(timer);
                                                timer = setTimeout(function(){ fetchCliente(input.value.trim()); }, 600);
                                            });
                                            // also check on blur
                                            input.addEventListener('blur', function(){ fetchCliente(input.value.trim()); });
                                        }
                                        if (btnSol) btnSol.addEventListener('click', function(){ importDataFromSolicitud(); });

                                    })();
                                    </script>
                                    <div class="col-md-4"><div class="form-group"><label>Teléfono del negocio</label><input type="text" class="form-control" name="telefono_negocio" value="<?php echo s_val('telefono_negocio', set_value('telefono_negocio')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo de operación (años)</label><input type="number" class="form-control" name="tiempo_operacion_anios" value="<?php echo s_val('tiempo_operacion_anios', set_value('tiempo_operacion_anios')); ?>"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Tiempo de operación (meses)</label><input type="number" class="form-control" name="tiempo_operacion_meses" value="<?php echo s_val('tiempo_operacion_meses', set_value('tiempo_operacion_meses')); ?>"></div></div>
                                    <!-- Propiedad del local: ocultado por petición (no mostrar ni capturar en el formulario) -->

                                    <!-- Ingresos y Ventas -->
                                    <div class="col-md-12 mt-3"><h6>Ingresos y Ventas</h6></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas en días buenos: C$</label><input type="number" step="0.01" class="form-control" name="ventas_buenos_amount" value="<?php echo s_val('ventas_buenos_amount', set_value('ventas_buenos_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas en días malos: C$</label><input type="number" step="0.01" class="form-control" name="ventas_malos_amount" value="<?php echo s_val('ventas_malos_amount', set_value('ventas_malos_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Ventas promedio mensual: C$</label>
                                            <div class="input-group">
                                                <input id="ventas_promedio_mensual" type="number" step="0.01" class="form-control" name="ventas_promedio_mensual" value="<?php echo s_val('ventas_promedio_mensual', set_value('ventas_promedio_mensual')); ?>">
                                                <div class="input-group-append">
                                                    <button id="btn_calcular_ventas_promedio" type="button" class="btn btn-secondary">Calcular</button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Pulse "Calcular" para estimar a partir de ventas en días buenos/malos. El campo es editable manualmente.</small>
                                        </div></div>

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

                                    <div class="col-md-4"><div class="form-group"><label>Margen comercial (%) Actividad Principal</label><input type="number" step="0.01" class="form-control" name="margen_comercial" value="<?php echo s_val('margen_comercial', set_value('margen_comercial')); ?>"></div></div>

                                    <!-- Otros ingresos (3 bloques) -->
                                    <div class="col-md-12 mt-3"><h6>Otros ingresos</h6></div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 1: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_1_amount" value="<?php echo s_val('otros_ingresos_1_amount', set_value('otros_ingresos_1_amount')); ?>"></div>
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_1_margin" value="<?php echo s_val('otros_ingresos_1_margin', set_value('otros_ingresos_1_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_1_detalle"><?php echo s_val('otros_ingresos_1_detalle', set_value('otros_ingresos_1_detalle')); ?></textarea></div>
                                        <div class="form-group"><label>Fotos - Otros Ingresos 1 (máx 3)</label><input type="file" accept="image/*" id="otros_ingresos_1_input" name="otros_ingresos_1[]" multiple class="form-control-file"><div id="otros_ingresos_1_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 2: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_2_amount" value="<?php echo s_val('otros_ingresos_2_amount', set_value('otros_ingresos_2_amount')); ?>"></div>
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_2_margin" value="<?php echo s_val('otros_ingresos_2_margin', set_value('otros_ingresos_2_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_2_detalle"><?php echo s_val('otros_ingresos_2_detalle', set_value('otros_ingresos_2_detalle')); ?></textarea></div>
                                        <div class="form-group"><label>Fotos - Otros Ingresos 2 (máx 3)</label><input type="file" accept="image/*" id="otros_ingresos_2_input" name="otros_ingresos_2[]" multiple class="form-control-file"><div id="otros_ingresos_2_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group"><label>Otros ingresos 3: C$</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_3_amount" value="<?php echo s_val('otros_ingresos_3_amount', set_value('otros_ingresos_3_amount')); ?>"></
                                        <div class="form-group"><label>Margen comercial (%)</label><input type="number" step="0.01" class="form-control" name="otros_ingresos_3_margin" value="<?php echo s_val('otros_ingresos_3_margin', set_value('otros_ingresos_3_margin')); ?>"></div>
                                        <div class="form-group"><label>Detallar:</label><textarea class="form-control" name="otros_ingresos_3_detalle"><?php echo s_val('otros_ingresos_3_detalle', set_value('otros_ingresos_3_detalle')); ?></textarea></div>
                                        <div class="form-group"><label>Fotos - Otros Ingresos 3 (máx 3)</label><input type="file" accept="image/*" id="otros_ingresos_3_input" name="otros_ingresos_3[]" multiple class="form-control-file"><div id="otros_ingresos_3_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div></div>
                                    </div>

                                    <!-- Estructura financiera y detalle de inventario -->
                                    <div class="col-md-12 mt-3"><h6>Estructura Financiera del Negocio</h6></div>
                                    <div class="col-md-4">
                                        <div class="form-group"><label>Cuentas por cobrar: C$</label><input type="number" step="0.01" class="form-control" name="cuentas_por_cobrar_amount" value="<?php echo s_val('cuentas_por_cobrar_amount', set_value('cuentas_por_cobrar_amount')); ?>"></div>
                                        <div class="form-group"><label>Ventas al Crédito: C$</label><input type="number" step="0.01" class="form-control" name="ventas_al_credito" value="<?php echo s_val('ventas_al_credito', set_value('ventas_al_credito')); ?>"></div>
                                        <div class="form-group">
                                            <label>Foto de evidencia</label>
                                            <input type="file" accept="image/*" class="form-control-file" name="cuentas_por_cobrar_evidencia" id="cuentas_por_cobrar_evidencia">
                                            <?php if (s_val('cuentas_por_cobrar_evidencia')): ?>
                                                <div class="mt-2">
                                                    <img src="<?php echo base_url('uploads/solicitudes/' . s_val('cuentas_por_cobrar_evidencia')); ?>" alt="Evidencia" style="max-width:200px;max-height:150px;border:1px solid #ddd;border-radius:4px;">
                                                    <br><small class="text-muted">Archivo actual: <?php echo s_val('cuentas_por_cobrar_evidencia'); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Caja (efectivo): C$</label><input type="number" step="0.01" class="form-control" name="caja_amount" value="<?php echo s_val('caja_amount', set_value('caja_amount')); ?>"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Banco: C$</label><input type="number" step="0.01" class="form-control" name="banco_amount" value="<?php echo s_val('banco_amount', set_value('banco_amount')); ?>"></div></div>

                                    <div class="col-md-12 mt-3"><h6>Detalle del Inventario</h6></div>

                                    <div class="col-md-12"><div class="form-group"><label>Producto / Detalle</label><textarea class="form-control" rows="4" name="detalle_inventario"><?php echo s_val('detalle_inventario', set_value('detalle_inventario')); ?></textarea></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Monto total del inventario: C$</label><input type="number" step="0.01" class="form-control" name="monto_total_inventario" value="<?php echo s_val('monto_total_inventario', set_value('monto_total_inventario')); ?>"></div></div>
                                    </div>

                                    <!-- SECTION 4: GASTOS FIJOS Y OPERATIVOS -->
                                    <div class="col-md-12 mt-4">
                                        <h5>Gastos Fijos y Operativos</h5>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Pago de alquiler local: C$</label><input type="number" step="0.01" class="form-control" name="pago_alquiler" value="<?php echo s_val('pago_alquiler', set_value('pago_alquiler')); ?>"></div></div>
                                    <div id="field-pago-trabajadores" class="col-md-4"><div class="form-group"><label>Pago de trabajadores: C$</label><input type="number" step="0.01" class="form-control" name="pago_trabajadores" value="<?php echo s_val('pago_trabajadores', set_value('pago_trabajadores')); ?>"></div></div>
                                    <div id="field-numero-empleados" class="col-md-4"><div class="form-group"><label>Número de empleados</label><input type="number" class="form-control" name="numero_empleados" value="<?php echo s_val('numero_empleados', set_value('numero_empleados')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Energía eléctrica: C$</label><input type="number" step="0.01" class="form-control" name="energia_electrica" value="<?php echo s_val('energia_electrica', set_value('energia_electrica')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Agua potable: C$</label><input type="number" step="0.01" class="form-control" name="agua_potable" value="<?php echo s_val('agua_potable', set_value('agua_potable')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Internet / Telefonía: C$</label><input type="number" step="0.01" class="form-control" name="internet_telefonia" value="<?php echo s_val('internet_telefonia', set_value('internet_telefonia')); ?>"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Otros gastos: C$</label><input type="number" step="0.01" class="form-control" name="otros_gastos" value="<?php echo s_val('otros_gastos', set_value('otros_gastos')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Gastos Personales: C$</label><input type="number" step="0.01" class="form-control" name="gastos_personales" value="<?php echo s_val('gastos_personales', set_value('gastos_personales')); ?>"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label>Gastos Transporte: C$</label><input type="number" step="0.01" class="form-control" name="gastos_transporte" value="<?php echo s_val('gastos_transporte', set_value('gastos_transporte')); ?>"></div></div>

                                    <!-- SECTION 4b: Declaración del cliente -->
                                    <div class="col-md-12 mt-4"><h5>Declaración del Cliente</h5></div>
                                    <div class="col-md-12"><div class="form-group"><p>Declaro que la información proporcionada es verídica y autorizo a Credi Blamen S.A. a verificar mis datos en las fuentes necesarias para fines de análisis crediticio y cumplimiento regulatorio.</p></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Acepto verificación</label><br><input type="checkbox" name="declaro_verificacion" value="1" <?php echo s_checked('declaro_verificacion'); ?>></div></div>
                                    <div class="col-md-4"><!-- hidden duplicate: keep a single visible comision input above -->
                                        <input type="hidden" name="comision_desembolso" value="<?php echo htmlspecialchars(s_val('comision_desembolso', set_value('comision_desembolso'))); ?>">
                                    </div>
                                    <!-- <div class="col-md-4"><div class="form-group"><label>Firma del solicitante</label><input type="text" class="form-control" name="firma_solicitante" value="<?php echo s_val('firma_solicitante', set_value('firma_solicitante')); ?>"></div></div> -->
                                    <div class="col-md-3"><div class="form-group"><label>Fecha firma</label><input type="date" class="form-control" name="fecha_firma" value="<?php echo s_date_fmt('fecha_firma', 'Y-m-d', set_value('fecha_firma')); ?>"></div></div>
                                    <div class="col-md-9"><div class="form-group"><label>DDC - Investigación de campo</label><input type="text" class="form-control" name="ddc_investigacion_campo" value="<?php echo s_val('ddc_investigacion_campo', set_value('ddc_investigacion_campo')); ?>"></div></div>

                                    <!-- SECTION 5: USO INTERNO (PROMOTOR / MICROFINANCIERA) -->
                                    <div class="col-md-12 mt-4"><h5>USO INTERNO (PROMOTOR / MICROFINANCIERA)</h5></div>
                                    <!-- <div class="col-md-6"><div class="form-group"><label>Nombre del promotor</label><input type="text" class="form-control" name="nombre_promotor" id="nombre_promotor_input" value="<?php echo s_val('nombre_promotor', !empty(set_value('nombre_promotor')) ? set_value('nombre_promotor') : $default_nombre_promotor); ?>" readonly></div></div> -->
                                    <div class="col-md-6"><div class="form-group"><label>Ruta (Asesor)</label>
                                            <select name="idasesor" id="idasesor_select" class="form-control">
                                                <option value="">-- Seleccione ruta/asesor --</option>
                                                <?php if (!empty($asesores) && is_array($asesores)): foreach($asesores as $a): ?>
                                                    <?php $sel = (s_val('idasesor') == (isset($a->idasesor)?$a->idasesor:(isset($a['idasesor'])?$a['idasesor']:''))) ? 'selected' : ''; ?>
                                                    <option value="<?php echo isset($a->idasesor)?$a->idasesor:(isset($a['idasesor'])?$a['idasesor']:''); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars(isset($a->nombres)?$a->nombres:(isset($a['nombres'])?$a['nombres']:'')); ?></option>
                                                <?php endforeach; endif; ?>
                                            </select>
                                        </div></div>
                                    <div class="col-md-12"><div class="form-group"><label>Fecha de recepción de solicitud</label><input type="date" class="form-control" name="fecha_recepcion_solicitud" value="<?php echo s_date_fmt('fecha_recepcion_solicitud', 'Y-m-d', set_value('fecha_recepcion_solicitud')); ?>"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label>Observaciones del promotor</label><textarea class="form-control" name="observaciones_promotor"><?php echo s_val('observaciones_promotor', set_value('observaciones_promotor')); ?></textarea></div></div>

                                    <div id="additional-sections" class="col-12 mt-3"></div>

                                    <!-- PHOTO GROUPS: Fachada, Inventario, Cedula, Otros ingresos -->
                                    <div class="col-md-12 mt-3"><h5>Fotos del Negocio y Documentos</h5></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fachada (máx 2 fotos)</label>
                                            <input type="file" accept="image/*" id="fachada_input" name="fachada[]" multiple class="form-control-file">
                                            <div id="fachada_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cédula - Frontal (máx 1)</label>
                                            <input type="file" accept="image/*" id="cedula_front_input" name="cedula_front" class="form-control-file">
                                            <div id="cedula_front_preview" style="margin-top:6px;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cédula - Trasera (máx 1)</label>
                                            <input type="file" accept="image/*" id="cedula_back_input" name="cedula_back" class="form-control-file">
                                            <div id="cedula_back_preview" style="margin-top:6px;"></div>
                                        </div>
                                    </div>
                                    <!-- 'Otros ingresos' photo inputs moved into each corresponding block above; grouped inputs removed. -->

                                    <!-- SECCIONES DE DOCUMENTOS EN PARALELO -->
                                    <div class="col-md-12 mt-4"><hr></div>
                                    
                                    <!-- DOCUMENTOS GENERALES -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><h5>Documentos Generales</h5></label>
                                            <small class="form-text text-muted">Documentos Generales (máx 10 archivos)</small>
                                            <input type="file" accept="image/*,application/pdf" id="docs_generales_input" name="docs_generales[]" multiple class="form-control-file">
                                            <small class="form-text text-muted">Puede subir imágenes o PDFs. Máximo 10 archivos.</small>
                                            <div id="docs_generales_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                                        </div>
                                    </div>

                                    <!-- DOCUMENTOS LEGALES VARIADOS -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><h5>Documentos Legales Variados</h5></label>
                                            <small class="form-text text-muted">Documentos Legales Variados (máx 10 archivos)</small>
                                            <input type="file" accept="image/*,application/pdf" id="docs_legales_input" name="docs_legales[]" multiple class="form-control-file">
                                            <small class="form-text text-muted">Puede subir imágenes o PDFs. Máximo 10 archivos.</small>
                                            <div id="docs_legales_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                                        </div>
                                    </div>

                                    <!-- FOTOS ADICIONALES -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><h5>Fotos Adicionales</h5></label>
                                            <small class="form-text text-muted">Fotos Adicionales</small>
                                            <input type="file" accept="image/*" id="fotos_adicionales_input" name="fotos_adicionales[]" multiple class="form-control-file">
                                            <small class="form-text text-muted">Agregue cualquier foto adicional que considere necesaria.</small>
                                            <div id="fotos_adicionales_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><h5>Consentimiento de Filtrado</h5></label>
                                            <small class="form-text text-muted">Suba la foto del consentimiento de filtrado</small>
                                            <input type="file" accept="image/*" id="consentimiento_filtrado_input" name="consentimiento_filtrado[]" class="form-control-file">
                                            <small class="form-text text-muted">Solo una foto por solicitud.</small>
                                            <div id="consentimiento_filtrado_preview" style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;"></div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <div class="form-actions">
                                                <button type="submit" id="btn_guardar_solicitud" class="btn btn-primary">Guardar</button>
                                                <?php if (isset($solicitud) && isset($solicitud->idsolicitud) && $solicitud->idsolicitud): ?>
                                                    <a class="btn btn-outline-secondary" href="<?php echo base_url('solicitudes/download_solicitud_pdf_force/' . intval($solicitud->idsolicitud)); ?>">Descargar PDF</a>
                                                <?php endif; ?>
                                            </div>
                                    </div>
                                </div>
                            </form>
                            <script>
                                (function(){
                                    function formatTasaForDisplay(raw){
                                        if(raw === null || raw === undefined || raw === '') return '';
                                        var n = parseFloat(raw);
                                        if(isNaN(n)) return raw;
                                        if(Math.abs(n) <= 1){ n = n * 100; }
                                        var s = (Math.round(n*100)/100).toFixed(2);
                                        s = s.replace(/\.00$/,'');
                                        s = s.replace(/\.?0$/,'');
                                        return s;
                                    }

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
                                        // also update visible tasa input and recompute cuota
                                        try{ var vf = document.querySelector('input[name="tasa_interes"]'); if(vf) vf.value = formatTasaForDisplay(tasa); }catch(e){}
                                        // set visible comision_desembolso (readonly) so user cannot modify after selecting product
                                        try{ var vfCom = document.getElementById('comision_desembolso') || document.querySelector('input[name="comision_desembolso"]'); if(vfCom){ vfCom.value = com; vfCom.setAttribute('readonly','readonly'); } }catch(e){}
                                        try{ if(typeof computeCuota === 'function') computeCuota(); }catch(e){}
                                    }

                                    var sel = document.getElementById('producto_select');
                                    var classSel = document.getElementById('producto_clasificacion');
                                    var montoInput = document.getElementById('monto_solicitado');

                                    // Photo upload helpers: previews + AJAX upload when solicitud ID exists
                                    try {
                                        var solId = '<?php echo isset($solicitud->idsolicitud) ? intval($solicitud->idsolicitud) : 0; ?>';

                                        function renderPreview(containerId, file, removable){
                                            var url = URL.createObjectURL(file);
                                            var img = document.createElement('img');
                                            img.src = url;
                                            img.style.maxWidth = '120px';
                                            img.style.maxHeight = '90px';
                                            img.style.border = '1px solid #ddd';
                                            img.style.padding = '2px';
                                            img.style.display = 'block';
                                            var wrapper = document.createElement('div');
                                            wrapper.style.display = 'inline-block';
                                            wrapper.style.marginRight = '6px';
                                            wrapper.appendChild(img);
                                            document.getElementById(containerId).appendChild(wrapper);
                                        }

                                        function uploadFiles(files, group, previewId, maxCount){
                                            if (!files || !files.length) return;
                                            if (maxCount && files.length > maxCount) {
                                                alert('Se pueden subir como máximo ' + maxCount + ' archivos para ' + group);
                                                return;
                                            }
                                            // If no solicitud id (creating new), ask user to save first
                                            if (!solId || solId === '0') {
                                                alert('Primero guarde la solicitud; luego podrá subir las fotos para el negocio.');
                                                return;
                                            }
                                            for (var i=0;i<files.length;i++) {
                                                (function(f){
                                                    renderPreview(previewId, f);
                                                    var fd = new FormData();
                                                    fd.append('idsolicitud', solId);
                                                    fd.append('group', group);
                                                    fd.append('photo', f, f.name);
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.open('POST', '<?php echo base_url('solicitudes/upload_solicitud_photo_ajax'); ?>', true);
                                                    xhr.onreadystatechange = function(){
                                                        if (xhr.readyState === 4) {
                                                            try {
                                                                var json = JSON.parse(xhr.responseText);
                                                                if (!json || !json.status) {
                                                                    console.warn('Upload failed', json);
                                                                }
                                                                // refresh list from server so uploaded files are shown with actions
                                                                try { if (typeof loadExistingPhotos === 'function') loadExistingPhotos(); } catch(e){}
                                                            } catch(e){ console.warn('Upload response parse error', e); }
                                                        }
                                                    };
                                                    xhr.send(fd);
                                                })(files[i]);
                                            }
                                        }

                                        // Bind inputs
                                        var fach = document.getElementById('fachada_input'); if (fach) fach.addEventListener('change', function(e){ var files = this.files; if(files.length>2){ alert('Fachada: máximo 2 fotos'); } uploadFiles(files, 'fachada', 'fachada_preview', 2); });
                                        var inv = document.getElementById('inventario_input'); if (inv) inv.addEventListener('change', function(e){ var files = this.files; if(files.length>10){ alert('Inventario: máximo 10 fotos'); } uploadFiles(files, 'inventario', 'inventario_preview', 10); });
                                        var cf = document.getElementById('cedula_front_input'); if (cf) cf.addEventListener('change', function(e){ var f = this.files; if(f.length>1){ alert('Solo una foto frontal de cédula permitida'); } uploadFiles(f, 'cedula_front', 'cedula_front_preview', 1); });
                                        var cb = document.getElementById('cedula_back_input'); if (cb) cb.addEventListener('change', function(e){ var f = this.files; if(f.length>1){ alert('Solo una foto trasera de cédula permitida'); } uploadFiles(f, 'cedula_back', 'cedula_back_preview', 1); });
                                        var oi1 = document.getElementById('otros_ingresos_1_input'); if (oi1) oi1.addEventListener('change', function(){ uploadFiles(this.files, 'otros_ingresos_1', 'otros_ingresos_1_preview', 3); });
                                        var oi2 = document.getElementById('otros_ingresos_2_input'); if (oi2) oi2.addEventListener('change', function(){ uploadFiles(this.files, 'otros_ingresos_2', 'otros_ingresos_2_preview', 3); });
                                        var oi3 = document.getElementById('otros_ingresos_3_input'); if (oi3) oi3.addEventListener('change', function(){ uploadFiles(this.files, 'otros_ingresos_3', 'otros_ingresos_3_preview', 3); });
                                        // Nuevos campos con límite de 10 archivos
                                        var dg = document.getElementById('docs_generales_input'); if (dg) dg.addEventListener('change', function(){ var files = this.files; if(files.length>10){ alert('Documentos Generales: máximo 10 archivos'); return; } uploadFiles(files, 'docs_generales', 'docs_generales_preview', 10); });
                                        var dl = document.getElementById('docs_legales_input'); if (dl) dl.addEventListener('change', function(){ var files = this.files; if(files.length>10){ alert('Documentos Legales Variados: máximo 10 archivos'); return; } uploadFiles(files, 'docs_legales', 'docs_legales_preview', 10); });
                                        var fa = document.getElementById('fotos_adicionales_input'); if (fa) fa.addEventListener('change', function(){ uploadFiles(this.files, 'fotos_adicionales', 'fotos_adicionales_preview'); });
                                    } catch(e) { console.warn('photo helper init error', e); }

                                        // Load existing uploaded photos and render with download/delete actions
                                        function renderServerPhoto(containerId, photo){
                                            try{
                                                var wrapper = document.createElement('div');
                                                wrapper.style.display = 'inline-block'; wrapper.style.marginRight = '6px'; wrapper.style.position = 'relative';
                                                wrapper.className = 'sol-photo-item';
                                                wrapper.setAttribute('data-idphoto', photo.idphoto || '');
                                                // image
                                                var img = document.createElement('img'); img.src = '<?php echo base_url('uploads/'); ?>' + (photo.filename || ''); img.style.maxWidth='120px'; img.style.maxHeight='90px'; img.style.border='1px solid #ddd'; img.style.padding='2px'; img.style.display='block';
                                                wrapper.appendChild(img);
                                                // actions container
                                                var actions = document.createElement('div'); actions.style.textAlign='center'; actions.style.marginTop='4px';
                                                // download link
                                                var a = document.createElement('a'); a.href = '<?php echo base_url('uploads/'); ?>' + (photo.filename || ''); a.target = '_blank'; a.className = 'btn btn-sm btn-outline-secondary'; a.style.fontSize='11px'; a.style.marginRight='4px'; a.textContent = 'Descargar';
                                                actions.appendChild(a);
                                                // delete button
                                                var del = document.createElement('button'); del.type='button'; del.className='btn btn-sm btn-danger'; del.style.fontSize='11px'; del.textContent='Eliminar';
                                                del.addEventListener('click', function(){
                                                    if(!photo.idphoto){ if(!confirm('Eliminar imagen?')) return; }
                                                    var ok = confirm('Eliminar esta imagen?'); if(!ok) return;
                                                    var fd = new FormData(); fd.append('idphoto', photo.idphoto || '');
                                                    fetch('<?php echo base_url('solicitudes/delete_photo_ajax'); ?>', { method:'POST', credentials:'same-origin', body: fd })
                                                        .then(function(r){ return r.json(); }).then(function(j){ if(j && j.status){ try{ loadExistingPhotos(); }catch(e){} } else { alert('No se pudo eliminar'); } }).catch(function(){ alert('Error al eliminar'); });
                                                });
                                                actions.appendChild(del);
                                                wrapper.appendChild(actions);
                                                var container = document.getElementById(containerId);
                                                if(container) container.appendChild(wrapper);
                                            }catch(e){ console.warn('renderServerPhoto error', e); }
                                        }

                                        function loadExistingPhotos(){
                                            if (!solId || solId === '0') return;
                                            var url = '<?php echo base_url('solicitudes/list_photos_ajax'); ?>/' + solId;
                                            fetch(url, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(json){
                                                if(!json || !json.status) return;
                                                // clear all preview containers and repopulate
                                                var groups = {};
                                                // json.photos may be array of rows
                                                if(Array.isArray(json.photos)){
                                                    json.photos.forEach(function(p){ var g = (p.grupo || p.group || 'otros'); if(!groups[g]) groups[g]=[]; groups[g].push(p); });
                                                } else if(typeof json.photos === 'object'){
                                                    // fallback: if already grouped
                                                    groups = json.photos;
                                                }
                                                // list of known preview container ids mapping to group names
                                                var mapping = {
                                                    'fachada': 'fachada_preview',
                                                    'inventario': 'inventario_preview',
                                                    'cedula_front': 'cedula_front_preview',
                                                    'cedula_back': 'cedula_back_preview',
                                                    'otros_ingresos_1': 'otros_ingresos_1_preview',
                                                    'otros_ingresos_2': 'otros_ingresos_2_preview',
                                                    'otros_ingresos_3': 'otros_ingresos_3_preview',
                                                    'docs_generales': 'docs_generales_preview',
                                                    'docs_legales': 'docs_legales_preview',
                                                    'fotos_adicionales': 'fotos_adicionales_preview'
                                                };
                                                // clear
                                                Object.keys(mapping).forEach(function(k){ var el = document.getElementById(mapping[k]); if(el){ el.innerHTML = ''; } });
                                                // render
                                                Object.keys(groups).forEach(function(g){ var arr = groups[g] || []; var contId = mapping[g] || null; arr.forEach(function(p){ if(contId) renderServerPhoto(contId, p); }); });
                                            }).catch(function(e){ console.warn('list_photos error', e); });
                                        }

                                        // call on load
                                        try{ document.addEventListener('DOMContentLoaded', function(){ loadExistingPhotos(); }); }catch(e){}

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
                                    var currentSuggestedProduct = null;
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
                                            // NO limpiar el plazo - mantener el valor del usuario
                                            // try{ var vf3 = document.querySelector('input[name="plazo_meses"]'); if(vf3) vf3.value = ''; }catch(e){}
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
                                            // pre-check if this producto is already in propuesta_tipos hidden field
                                            try{
                                                var prev = document.getElementById('propuesta_tipos');
                                                if(prev && prev.value){
                                                    try{
                                                        var arr = JSON.parse(prev.value);
                                                        if(Array.isArray(arr) && arr.indexOf((p.id||p.ID||p.id_producto||'')) !== -1){ cb.checked = true; }
                                                    }catch(e){}
                                                }
                                            }catch(e){}

                                            cb.addEventListener('change', function(){
                                                if(cb.checked){
                                                    // set hidden product select if exists
                                                    try{ if(sel) sel.value = p.id || p.ID || p.id_producto || ''; }catch(e){}
                                                    try{ var prodSel = document.getElementById('producto_select_hidden'); if(prodSel) prodSel.value = JSON.stringify([ (p.id || p.ID || p.id_producto || '') ]); }catch(e){}
                                                    // fill hidden tasa/comision/plazo
                                                    try{ if(document.getElementById('producto_tasa')) document.getElementById('producto_tasa').value = tasa; }catch(e){}
                                                            try{ if(document.getElementById('producto_comision')) document.getElementById('producto_comision').value = (p.comision_desembolso||com||''); }catch(e){}
                                                    try{ if(document.getElementById('producto_plazo')) document.getElementById('producto_plazo').value = plazo; }catch(e){}
                                                    try{ var propuestas = document.getElementById('propuesta_tipos'); if(propuestas) propuestas.value = JSON.stringify([ (p.id || p.ID || p.id_producto || '') ]); }catch(e){}
                                                    // also fill visible form fields (if present)
                                                            try{ var vf = document.querySelector('input[name="tasa_interes"]'); if(vf){ vf.value = formatTasaForDisplay(tasa); vf.setAttribute('readonly','readonly'); } }catch(e){}
                                                            try{ var vf2 = document.querySelector('input[name="comision_desembolso"]'); if(vf2){ vf2.value = formatTasaForDisplay(p.comision_desembolso||com||''); vf2.setAttribute('readonly','readonly'); } }catch(e){}
                                                    // NO sobrescribir el plazo si el usuario ya lo modificó - solo establecer si está vacío
                                                    try{ var vf3 = document.querySelector('input[name="plazo_meses"]'); if(vf3 && (!vf3.value || vf3.value === '')) vf3.value = plazo; }catch(e){}
                                                }
                                                else {
                                                    // unchecked -> clear hidden fields
                                                    try{ var prodSel = document.getElementById('producto_select_hidden'); if(prodSel) prodSel.value = ''; }catch(e){}
                                                    try{ var propuestas = document.getElementById('propuesta_tipos'); if(propuestas) propuestas.value = ''; }catch(e){}
                                                    try{ if(document.getElementById('producto_tasa')) document.getElementById('producto_tasa').value = ''; }catch(e){}
                                                    try{ if(document.getElementById('producto_comision')) document.getElementById('producto_comision').value = ''; }catch(e){}
                                                    try{ if(document.getElementById('producto_plazo')) document.getElementById('producto_plazo').value = ''; }catch(e){}
                                                    // also clear visible fields if they were set by this checkbox
                                                            try{ var vf = document.querySelector('input[name="tasa_interes"]'); if(vf){ vf.value = ''; vf.removeAttribute('readonly'); } }catch(e){}
                                                            try{ var vf2 = document.querySelector('input[name="comision_desembolso"]'); if(vf2){ vf2.value = ''; vf2.removeAttribute('readonly'); } }catch(e){}
                                                    // NO limpiar el plazo automáticamente - permitir que el usuario lo mantenga
                                                    // try{ var vf3 = document.querySelector('input[name="plazo_meses"]'); if(vf3) vf3.value = ''; }catch(e){}
                                                }
                                            }, false);
                                        }
                                        // set current suggested product for plazo and cuota logic
                                        try{ currentSuggestedProduct = p || null; }catch(e){ currentSuggestedProduct = null; }
                                        try{ updatePlazoUI(); }catch(e){}
                                    }

                                    /* Plazo / cuota helpers */
                                    function safeInt(v){ var n = parseInt(v); return isNaN(n)?null:n; }
                                    function normalizePercentInput(v){ var n = parseFloat((v||'').toString().replace(',','.')); if(isNaN(n)) return null; if(n>0 && n<=1) return n*100; return n; }

                                    function updatePlazoUI(){
                                        var monto = parseFloat((montoInput && montoInput.value) ? montoInput.value : 0) || 0;
                                        var cls = (classSel && classSel.value) ? classSel.value : '';
                                        var p = currentSuggestedProduct || findMatchingRemoteProduct(monto, cls);
                                        var minEl = document.getElementById('plazo_min');
                                        var maxEl = document.getElementById('plazo_max');
                                        var msgEl = document.getElementById('plazo_msg');
                                        var plazoInput = document.getElementById('plazo_meses');
                                        if(!minEl || !maxEl || !msgEl || !plazoInput) return;
                                        if(!p){
                                            minEl.innerText = '-'; maxEl.innerText = '-'; msgEl.style.display='none'; plazoInput.removeAttribute('min'); plazoInput.removeAttribute('max'); return;
                                        }
                                        // extract plazo bounds
                                        var rawMinP = (p.plazo_min !== undefined ? p.plazo_min : (p.plazo_minimo !== undefined ? p.plazo_minimo : null));
                                        var rawMaxP = (p.plazo_max !== undefined ? p.plazo_max : (p.plazo_maximo !== undefined ? p.plazo_maximo : null));
                                        var ra = (p.plazo !== undefined ? p.plazo : null);
                                        var minP = safeInt(rawMinP!==null?rawMinP: (ra!==null?ra:null));
                                        var maxP = safeInt(rawMaxP!==null?rawMaxP: (ra!==null?ra:null));
                                        if(minP === null && maxP === null){ minEl.innerText = '-'; maxEl.innerText='-'; msgEl.style.display='none'; plazoInput.removeAttribute('min'); plazoInput.removeAttribute('max'); return; }
                                        if(minP === null) minP = 1;
                                        if(maxP === null) maxP = minP;
                                        minEl.innerText = minP; maxEl.innerText = maxP;
                                        plazoInput.setAttribute('min', minP);
                                        plazoInput.setAttribute('max', maxP);
                                        // validate monto against product's allowed monto bounds if present
                                        var rawMinMonto = (p.monto_min !== undefined ? p.monto_min : (p.min_monto!==undefined? p.min_monto : (p.min!==undefined? p.min : null)));
                                        var rawMaxMonto = (p.monto_max !== undefined ? p.monto_max : (p.max_monto!==undefined? p.max_monto : (p.max!==undefined? p.max : null)));
                                        var minMonto = safeNum(rawMinMonto); var maxMonto = safeNum(rawMaxMonto);
                                        if(minMonto !== null && monto < minMonto){ msgEl.style.display='block'; msgEl.style.color='red'; msgEl.innerText = 'El monto ingresado es menor que el monto mínimo del producto ('+minMonto+').'; }
                                        else if(maxMonto !== null && monto > maxMonto){ msgEl.style.display='block'; msgEl.style.color='red'; msgEl.innerText = 'El monto ingresado supera el máximo permitido por el producto ('+maxMonto+').'; }
                                        else { msgEl.style.display='none'; msgEl.innerText=''; }
                                    }

                                    function computeCuota(){
                                        var monto = parseFloat((montoInput && montoInput.value) ? montoInput.value : 0) || 0;
                                        var plazo = parseInt((document.getElementById('plazo_meses') && document.getElementById('plazo_meses').value) ? document.getElementById('plazo_meses').value : 0) || 0;
                                        var tasaRaw = (document.getElementById('tasa_interes') && document.getElementById('tasa_interes').value) ? document.getElementById('tasa_interes').value : '';
                                        var cuotaEl = document.getElementById('cuota_estimado');
                                        var preview = document.getElementById('plan_pago_preview');
                                        if(!cuotaEl) return;
                                        if(!plazo || plazo <= 0){ cuotaEl.value=''; if(preview) preview.innerHTML=''; return; }

                                        // normalize input rate to the same representation Prestamo expects
                                        // normalizePercentInput returns a percent-like number (e.g. 6 for 6% or 0.06 -> 6)
                                        var tasaPct = normalizePercentInput(tasaRaw);
                                        if(tasaPct === null || tasaPct === '' || isNaN(tasaPct)) { cuotaEl.value=''; if(preview) preview.innerHTML=''; return; }

                                        // prepare payload for the server preview generator (Prestamo controller)
                                        var freqEl = document.getElementById('frecuencia');
                                        var frecuencia = (freqEl && freqEl.value) ? (''+freqEl.value).toLowerCase() : 'mensual';

                                        var payload = {
                                            monto: monto,
                                            // Prestamo UI sends 'tasa' as the monthly rate display value; here we send the percent value
                                            tasa: tasaPct,
                                            plazo: plazo,
                                            frecuencia: frecuencia,
                                            fecha_inicio: null
                                        };

                                        // call server preview which contains the canonical amortization logic
                                        try{
                                            var url = '<?php echo base_url('prestamo/generate_preview_ajax'); ?>';
                                            // use jQuery if available, else fallback to fetch
                                            if(window.jQuery && jQuery.post){
                                                jQuery.post(url, payload, function(resp){
                                                    if(!resp || !resp.status){ cuotaEl.value=''; if(preview) preview.innerHTML=''; return; }
                                                    // Prefer schedule[0].cuota (which includes commission) when available
                                                    var val = '';
                                                    if(resp.schedule && Array.isArray(resp.schedule) && resp.schedule.length>0){
                                                        val = resp.schedule[0].cuota;
                                                    } else if(typeof resp.payment !== 'undefined'){
                                                        var pay = parseFloat(resp.payment) || 0;
                                                        var comm = parseFloat(resp.commission_per_period) || 0;
                                                        val = pay + comm;
                                                    }
                                                    if(val === '' || val === null || typeof val === 'undefined') {
                                                        cuotaEl.value='';
                                                        var cuotaQuincenalEl = document.getElementById('cuota_estimado_quincenal');
                                                        if(cuotaQuincenalEl) cuotaQuincenalEl.value='';
                                                        if(preview) preview.innerHTML='';
                                                    } else {
                                                        // Siempre mostrar la cuota mensual base
                                                        var cuotaMensual = parseFloat(val);
                                                        cuotaEl.value = (Math.round(cuotaMensual * 100) / 100).toFixed(2);
                                                        // Mostrar la cuota quincenal/catorcenal (mensual/2)
                                                        var cuotaQuincenalEl = document.getElementById('cuota_estimado_quincenal');
                                                        if(cuotaQuincenalEl) cuotaQuincenalEl.value = (Math.round((cuotaMensual/2) * 100) / 100).toFixed(2);
                                                    }
                                                    try{ if(typeof syncAll === 'function') syncAll(); }catch(e){}
                                                }, 'json').fail(function(){ cuotaEl.value=''; if(preview) preview.innerHTML=''; });
                                            } else {
                                                // fetch fallback
                                                fetch(url, { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: Object.keys(payload).map(function(k){ return encodeURIComponent(k)+'='+encodeURIComponent(payload[k]); }).join('&') })
                                                .then(function(r){ return r.json(); }).then(function(resp){
                                                    if(!resp || !resp.status){ cuotaEl.value=''; if(preview) preview.innerHTML=''; return; }
                                                    var val = '';
                                                    if(resp.schedule && Array.isArray(resp.schedule) && resp.schedule.length>0){ val = resp.schedule[0].cuota; }
                                                    else if(typeof resp.payment !== 'undefined') { val = (parseFloat(resp.payment)||0) + (parseFloat(resp.commission_per_period)||0); }
                                                    if(val === '' || val === null || typeof val === 'undefined') { cuotaEl.value=''; if(preview) preview.innerHTML=''; }
                                                    else { cuotaEl.value = (Math.round(parseFloat(val) * 100) / 100).toFixed(2); }
                                                    try{ if(typeof syncAll === 'function') syncAll(); }catch(e){}
                                                }).catch(function(){ cuotaEl.value=''; if(preview) preview.innerHTML=''; });
                                            }
                                        }catch(e){ cuotaEl.value=''; if(preview) preview.innerHTML=''; }
                                    }

                                    // wire events to recompute
                                    try{
                                        if(montoInput) montoInput.addEventListener('input', function(){ updatePlazoUI(); updateSuggestionPanel(); });
                                        var plazoIn = document.getElementById('plazo_meses'); if(plazoIn) plazoIn.addEventListener('input', function(){ updatePlazoUI(); });
                                        var tasaIn = document.getElementById('tasa_interes'); if(tasaIn) tasaIn.addEventListener('input', function(){ /* no auto compute, press Procesar cuota */ });
                                        if(classSel) classSel.addEventListener('change', function(){ updatePlazoUI(); });
                                        // wire Procesar cuota button
                                        try{ var btn = document.getElementById('btn_procesar_cuota'); if(btn) btn.addEventListener('click', function(){ computeCuota(); try{ syncAll(); }catch(e){} }); }catch(e){}
                                    }catch(e){}
                                })();
                            </script>
                            <!-- Edit comment modal (floating) -->
                            <div id="editCommentModal" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
                                <div style="background:#fff; max-width:640px; width:92%; margin:0 auto; padding:18px; border-radius:6px; box-shadow:0 8px 30px rgba(0,0,0,0.3);">
                                    <h5 style="margin-top:0;">Comentario de edición</h5>
                                    <p style="margin:6px 0 12px;color:#555;">Por favor ingrese un comentario breve que explique los cambios (mínimo 3 caracteres).</p>
                                    <textarea id="edit_comment_modal_text" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; font-size:14px;"></textarea>
                                    <div style="text-align:right; margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
                                        <button id="edit_comment_modal_cancel" type="button" class="btn btn-secondary">Cancelar</button>
                                        <button id="edit_comment_modal_ok" type="button" class="btn btn-primary">Enviar y Guardar</button>
                                    </div>
                                </div>
                            </div>

                            <script>
                                (function(){
                                    var form = document.getElementById('solicitud_form');
                                    var isEdit = <?php echo (isset($solicitud) && isset($solicitud->idsolicitud) && $solicitud->idsolicitud) ? 'true' : 'false'; ?>;
                                    if(!form) return;
                                    // If editing, intercept submit to require an edit comment when empty
                                    form.addEventListener('submit', function(ev){
                                        try{
                                            if(!isEdit) return; // creation path doesn't require modal
                                            var hidden = document.getElementById('edit_comment_hidden');
                                            var cur = hidden ? hidden.value.trim() : '';
                                            if(cur && cur.length >= 3) return; // already provided

                                            // prevent default submit and show modal
                                            ev.preventDefault(); ev.stopPropagation();
                                            var modal = document.getElementById('editCommentModal');
                                            var ta = document.getElementById('edit_comment_modal_text');
                                            var btnOk = document.getElementById('edit_comment_modal_ok');
                                            var btnCancel = document.getElementById('edit_comment_modal_cancel');
                                            if(!modal || !ta || !btnOk || !btnCancel) return; 
                                            ta.value = '';
                                            modal.style.display = 'flex';

                                            function closeModal(){ modal.style.display = 'none'; btnOk.removeEventListener('click', onOk); btnCancel.removeEventListener('click', onCancel); }
                                            function onCancel(){ closeModal(); }
                                            function onOk(){
                                                var v = (ta.value || '').trim();
                                                if(v.length < 3){ ta.style.borderColor = 'red'; ta.focus(); return; }
                                                // set hidden input (create if missing)
                                                if(!hidden){ hidden = document.createElement('input'); hidden.type='hidden'; hidden.name='edit_comment'; hidden.id='edit_comment_hidden'; form.appendChild(hidden); }
                                                hidden.value = v;
                                                closeModal();
                                                // submit form programmatically (do not trigger infinite loop)
                                                form.submit();
                                            }
                                            function onCancel(){ closeModal(); }
                                            btnOk.addEventListener('click', onOk);
                                            btnCancel.addEventListener('click', onCancel);
                                            ta.focus();
                                        }catch(e){ console && console.error && console.error(e); }
                                    }, false);
                                })();
                            </script>
                            <script>
                                // Mostrar/Ocultar sección INFORMACIÓN LABORAL según rubro de crédito
                                (function(){
                                    function toggleSeccionLaboral() {
                                        var rubroSelect = document.getElementById('rubro_credito');
                                        var seccionLaboral = document.getElementById('section-laboral-asalariado');
                                        
                                        if (!rubroSelect || !seccionLaboral) return;
                                        
                                        var rubro = rubroSelect.value;
                                        
                                        // Mostrar solo si es "Personales (Asalariados)"
                                        if (rubro === 'Personales (Asalariados)') {
                                            seccionLaboral.style.display = '';
                                            // Animación suave
                                            seccionLaboral.style.opacity = '0';
                                            setTimeout(function() {
                                                seccionLaboral.style.transition = 'opacity 0.3s';
                                                seccionLaboral.style.opacity = '1';
                                            }, 10);
                                        } else {
                                            seccionLaboral.style.display = 'none';
                                        }
                                    }

                                    function toggleSeccionNegocio() {
                                        var rubroSelect = document.getElementById('rubro_credito');
                                        var secNeg = document.getElementById('section-negocio-comerciante');
                                        var fPag = document.getElementById('field-pago-trabajadores');
                                        var fEmp = document.getElementById('field-numero-empleados');

                                        var rubroVal = rubroSelect ? (rubroSelect.value || '') : '';
                                        var rubroNorm = rubroVal
                                            .toLowerCase()
                                            .normalize('NFD')
                                            .replace(/[\u0300-\u036f]/g, '')
                                            .trim();

                                        // Regla solicitada: ocultar cuando Destino Conami sea Personales (Asalariados)
                                        // Se compara de forma flexible para tolerar pequeñas variaciones de texto.
                                        var isAsalariado = (rubroNorm.indexOf('personales') !== -1 && rubroNorm.indexOf('asalariad') !== -1);

                                        if (secNeg) {
                                            if (isAsalariado) secNeg.style.setProperty('display', 'none', 'important');
                                            else secNeg.style.removeProperty('display');
                                        }
                                        if (fPag) {
                                            if (isAsalariado) fPag.style.setProperty('display', 'none', 'important');
                                            else fPag.style.removeProperty('display');
                                        }
                                        if (fEmp) {
                                            if (isAsalariado) fEmp.style.setProperty('display', 'none', 'important');
                                            else fEmp.style.removeProperty('display');
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function(){
                                        var rubroSelect = document.getElementById('rubro_credito');
                                        if (rubroSelect) {
                                            // Verificar estado inicial al cargar
                                            toggleSeccionLaboral();
                                            toggleSeccionNegocio();
                                            
                                            // Escuchar cambios en el select
                                            rubroSelect.addEventListener('change', toggleSeccionLaboral);
                                            rubroSelect.addEventListener('change', toggleSeccionNegocio);
                                        }

                                        // Re-ejecutar por seguridad tras renderizado inicial.
                                        setTimeout(toggleSeccionNegocio, 120);
                                    });
                                })();
                            </script>
                            <script>
                                // Sync template placeholders with real inputs
                                (function(){
                                    function id(v){ return document.getElementById(v); }
                                    function setText(idn, val){ var e = id(idn); if(e) e.textContent = (val===null||val===undefined)?'':val; }
                                    function syncAll(){
                                        setText('tmpl_monto', id('monto_solicitado') ? id('monto_solicitado').value : '');
                                        setText('tmpl_plazo', id('plazo_meses') ? id('plazo_meses').value : '');
                                        setText('tmpl_frecuencia', id('frecuencia') ? id('frecuencia').value : '');
                                        setText('tmpl_tasa', id('tasa_interes') ? id('tasa_interes').value : '');
                                        setText('tmpl_cuota', id('cuota_estimado') ? id('cuota_estimado').value : '');
                                        // giro negocio
                                        var g = id('giro_negocio') ? id('giro_negocio').value : (id('giro_negocio_display')? id('giro_negocio_display').textContent : '');
                                        setText('tmpl_giro_negocio_display', g);
                                        // garantias
                                        var gar = [];
                                        if(document.querySelector('input[name="garantia_hipotecaria"]') && document.querySelector('input[name="garantia_hipotecaria"]').checked) gar.push('Hipotecaria');
                                        if(document.querySelector('input[name="garantia_prendaria"]') && document.querySelector('input[name="garantia_prendaria"]').checked) gar.push('Prendaria');
                                        if(document.querySelector('input[name="garantia_fiador"]') && document.querySelector('input[name="garantia_fiador"]').checked) gar.push('Fiador');
                                        if(document.querySelector('input[name="garantia_otra"]') && document.querySelector('input[name="garantia_otra"]').checked) gar.push('Otra');
                                        setText('tmpl_garantias_display', gar.join(', '));
                                        var er = id('es_rural');
                                        if (er) {
                                            setText('tmpl_es_rural_display', (er.value === '1' ? 'Sí' : 'No'));
                                        }
                                        // Nuevo / Renovacion toggles
                                        try{ if(id('tmpl_nuevo')) id('tmpl_nuevo').checked = !!document.querySelector('input[name="es_nuevo"]') ? document.querySelector('input[name="es_nuevo"]').checked : (id('tmpl_nuevo').checked); }catch(e){}
                                        try{ if(id('tmpl_renovacion')) id('tmpl_renovacion').checked = !!document.querySelector('input[name="es_renovacion"]') ? document.querySelector('input[name="es_renovacion"]').checked : (id('tmpl_renovacion').checked); }catch(e){}
                                    }
                                    document.addEventListener('DOMContentLoaded', function(){ syncAll(); });
                                    ['monto_solicitado','plazo_meses','frecuencia','tasa_interes','cuota_estimado','giro_negocio'].forEach(function(k){ var el = document.getElementById(k); if(el) el.addEventListener('input', syncAll); });
                                    ['garantia_hipotecaria','garantia_prendaria','garantia_fiador','garantia_otra'].forEach(function(n){ var el=document.querySelector('input[name="'+n+'"]'); if(el) el.addEventListener('change', syncAll); });
                                    var ruralSel = document.getElementById('es_rural');
                                    if (ruralSel) ruralSel.addEventListener('change', syncAll);
                                })();
                            </script>
                            <script>
                                // Cálculo automático de edad basado en fecha de nacimiento
                                (function(){
                                    function calculateAge(birthDate) {
                                        if (!birthDate) return '';
                                        var today = new Date();
                                        var birth = new Date(birthDate);
                                        var age = today.getFullYear() - birth.getFullYear();
                                        var monthDiff = today.getMonth() - birth.getMonth();
                                        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                                            age--;
                                        }
                                        return age >= 0 ? age : '';
                                    }

                                    function updateAge() {
                                        var fechaNacInput = document.querySelector('input[name="fecha_nacimiento"]');
                                        var edadInput = document.querySelector('input[name="edad"]');
                                        if (fechaNacInput && edadInput) {
                                            var age = calculateAge(fechaNacInput.value);
                                            if (age !== '') {
                                                edadInput.value = age;
                                                // Visual feedback
                                                edadInput.style.transition = 'background-color 0.35s';
                                                edadInput.style.backgroundColor = '#e6ffda';
                                                setTimeout(function(){ edadInput.style.backgroundColor = ''; }, 800);
                                            }
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function(){
                                        var fechaNacInput = document.querySelector('input[name="fecha_nacimiento"]');
                                        if (fechaNacInput) {
                                            // Calcular edad inicial si hay fecha
                                            if (fechaNacInput.value) {
                                                updateAge();
                                            }
                                            // Actualizar cuando cambie la fecha
                                            fechaNacInput.addEventListener('change', updateAge);
                                            fechaNacInput.addEventListener('input', updateAge);
                                        }
                                    });
                                })();
                            </script>
                                    <script>
                                        // Cálculo de Ventas Promedio Mensual
                                        (function(){
                                            function safeFloat(v){ var n = parseFloat((v||'').toString().replace(',','.')); return isNaN(n)?0:n; }

                                            function countChecked(selector){ var els = document.querySelectorAll(selector); return els ? els.length : 0; }

                                            function computeVentasPromedio(){
                                                var ventasBuenosAmt = safeFloat(document.querySelector('input[name="ventas_buenos_amount"]') ? document.querySelector('input[name="ventas_buenos_amount"]').value : 0);
                                                var ventasMalosAmt = safeFloat(document.querySelector('input[name="ventas_malos_amount"]') ? document.querySelector('input[name="ventas_malos_amount"]').value : 0);
                                                var cntBuenos = document.querySelectorAll('input[name="ventas_buenos_days[]"]:checked').length || 0;
                                                var cntMalos = document.querySelectorAll('input[name="ventas_malos_days[]"]:checked').length || 0;

                                                // Cálculo: (buenos*4*cntBuenos) + (malos*4*cntMalos) + (malos*2)
                                                var totalBuenos = ventasBuenosAmt * cntBuenos * 4;
                                                var totalMalos = ventasMalosAmt * cntMalos * 4;
                                                var totalMalosExtra = ventasMalosAmt * 2;
                                                var resultado = totalBuenos + totalMalos + totalMalosExtra;

                                                // round to 2 decimals
                                                resultado = Math.round(resultado * 100) / 100;

                                                var out = document.getElementById('ventas_promedio_mensual');
                                                if(out){ out.value = resultado; }
                                                // small highlight to indicate update
                                                if(out){ out.style.transition = 'background-color 0.35s'; out.style.backgroundColor = '#e6ffda'; setTimeout(function(){ out.style.backgroundColor = ''; }, 800); }
                                                return resultado;
                                            }

                                            document.addEventListener('DOMContentLoaded', function(){
                                                var btn = document.getElementById('btn_calcular_ventas_promedio');
                                                if(btn) btn.addEventListener('click', function(e){ e.preventDefault(); computeVentasPromedio(); });

                                                // Optional: recalc when relevant inputs change (keeps UX smooth)
                                                var fields = ['input[name="ventas_buenos_amount"]','input[name="ventas_malos_amount"]','input[name="ventas_buenos_days[]"]','input[name="ventas_malos_days[]"]'];
                                                fields.forEach(function(sel){
                                                    document.querySelectorAll(sel).forEach(function(el){
                                                        el.addEventListener('change', function(){ /* do not auto overwrite if user typed manual value? we will not auto-run here */ });
                                                    });
                                                });
                                            });
                                            // expose for console/testing
                                            window.computeVentasPromedio = computeVentasPromedio;
                                        })();
                                    </script>
                                    <script>
                                        // Prefill product selection and lock tasa/comision when editing
                                        (function(){
                                            function tryPrefillProduct(){
                                                try{
                                                    var propuestas = document.getElementById('propuesta_tipos');
                                                    if(!propuestas) return;
                                                    var raw = propuestas.value;
                                                    if(!raw) return;
                                                    var arr = null;
                                                    try{ arr = JSON.parse(raw); }catch(e){ arr = null; }
                                                    if(!Array.isArray(arr) || arr.length === 0) return;
                                                    var pid = arr[0];
                                                                // set hidden producto_select_hidden if present
                                                                try{ var ph = document.getElementById('producto_select_hidden'); if(ph) ph.value = JSON.stringify([pid]); }catch(e){}
                                                                // set visible selector if exists
                                                                try{ var sel = document.getElementById('producto_select'); if(sel){ sel.value = pid; sel.dispatchEvent(new Event('change')); } }catch(e){}
                                                                // mark suggested checkbox if it exists (may be added after remote load)
                                                                setTimeout(function(){ try{ var cb = document.getElementById('producto_sugerido_select_cb'); if(cb) cb.checked = true; }catch(e){} }, 600);
                                                                // additionally read any producto_* hidden values (server-populated) and apply them to visible inputs
                                                                try{
                                                                    var hiddenT = document.getElementById('producto_tasa');
                                                                    var hiddenC = document.getElementById('producto_comision');
                                                                    var hiddenP = document.getElementById('producto_plazo');
                                                                    function localFormat(raw){ if(raw===null||raw===undefined||raw==='') return ''; var n = parseFloat(raw); if(isNaN(n)) return raw; if(Math.abs(n)<=1) n = n*100; var s = (Math.round(n*100)/100).toFixed(2); s = s.replace(/\.00$/,''); s = s.replace(/\.?0$/,''); return s; }
                                                                    if(hiddenT && hiddenT.value){ try{ var tasaEl = document.getElementById('tasa_interes'); if(tasaEl){ tasaEl.value = localFormat(hiddenT.value); tasaEl.setAttribute('readonly','readonly'); } }catch(e){} }
                                                                    if(hiddenC && hiddenC.value){ try{ var comEl = document.getElementById('comision_desembolso'); if(comEl){ comEl.value = localFormat(hiddenC.value); comEl.setAttribute('readonly','readonly'); } }catch(e){} }
                                                                    // NO sobrescribir plazo si la solicitud ya tiene un valor - respetar el valor de la base de datos
                                                                    if(hiddenP && hiddenP.value){ try{ var plazoEl = document.getElementById('plazo_meses'); if(plazoEl && (!plazoEl.value || plazoEl.value === '')) plazoEl.value = hiddenP.value; }catch(e){} }
                                                                }catch(e){}
                                                }catch(e){}
                                            }
                                            document.addEventListener('DOMContentLoaded', function(){ tryPrefillProduct(); });
                                            // also attempt after a short delay in case remote product load replaces DOM
                                            setTimeout(tryPrefillProduct, 1200);
                                        })();
                                    </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

