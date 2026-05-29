<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-id-card bg-blue"></i>
                            <div class="d-inline">
                                <h5>Perfil Integral del Cliente</h5>
                                <span>Complete o revise los datos integrales del cliente</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a class="btn btn-secondary" href="<?php echo base_url('solicitudes'); ?>">Volver a Solicitudes</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>

                    <?php
                    // small helper to prefer perfil value, then solicitud, then empty
                    function pv($perfil, $sol, $keys, $default = ''){
                        if (!is_array($keys)) $keys = array($keys);
                        foreach ($keys as $k){
                            if (!empty($perfil) && isset($perfil->$k) && $perfil->$k !== null) return $perfil->$k;
                            if (!empty($sol) && isset($sol->$k) && $sol->$k !== null) return $sol->$k;
                        }
                        return $default;
                    }
                    function pv_full_names($perfil, $sol, $default = ''){
                        $names = '';
                        if (!empty($perfil)) {
                            if (!empty($perfil->nombres)) return $perfil->nombres;
                            if (!empty($perfil->nombre)) {
                                $names = trim($perfil->nombre . ' ' . ($perfil->segundo_nombre ?? ''));
                                if ($names !== '') return $names;
                            }
                        }
                        if (!empty($sol)) {
                            if (!empty($sol->nombres)) return $sol->nombres;
                            if (!empty($sol->nombre)) {
                                $names = trim($sol->nombre . ' ' . ($sol->segundo_nombre ?? ''));
                                if ($names !== '') return $names;
                            }
                        }
                        return $default;
                    }
                    function pv_full_surnames($perfil, $sol, $default = ''){
                        $surnames = '';
                        if (!empty($perfil)) {
                            if (!empty($perfil->apellidos)) return $perfil->apellidos;
                            if (!empty($perfil->primer_apellido)) {
                                $surnames = trim($perfil->primer_apellido . ' ' . ($perfil->segundo_apellido ?? ''));
                                if ($surnames !== '') return $surnames;
                            }
                        }
                        if (!empty($sol)) {
                            if (!empty($sol->apellidos)) return $sol->apellidos;
                            if (!empty($sol->primer_apellido)) {
                                $surnames = trim($sol->primer_apellido . ' ' . ($sol->segundo_apellido ?? ''));
                                if ($surnames !== '') return $surnames;
                            }
                        }
                        return $default;
                    }
                    // Compute prefills from solicitud (when perfil is empty) and perform simple conversions
                    $pref_sexo = pv($perfil, $solicitud, ['sexo']);
                    $pref_n_dependientes = pv($perfil, $solicitud, ['n_dependientes','numero_dependientes','numero_de_dependientes']);
                    $pref_telefono = pv($perfil, $solicitud, ['telefono','telefono_contacto','telefono_solicitante','celular']);
                    $pref_celular = pv($perfil, $solicitud, ['celular','telefono','telefono_contacto']);
                    $pref_fecha_nacimiento = pv($perfil, $solicitud, ['fecha_nacimiento']);
                    $pref_direccion = pv($perfil, $solicitud, ['direccion','direccion_exacta','direccion_domicilio']);
                    $pref_ocupacion = pv($perfil, $solicitud, ['ocupacion','cargo_puesto']);
                    $pref_empresa = pv($perfil, $solicitud, ['empresa','nombre_empresa']);
                    
                    // Determinar categoría de empleo
                    $categoria_empleo = pv($perfil, $solicitud, ['categoria_empleo']);
                    
                    // Calcular ingreso mensual según categoría
                    $pref_ingreso_cordobas = null;
                    $pref_ingreso_total = null;
                    
                    // Obtener otros ingresos adicionales
                    $otros_ingresos_1 = pv($perfil, $solicitud, ['otros_ingresos_1_amount']);
                    $otros_ingresos_2 = pv($perfil, $solicitud, ['otros_ingresos_2_amount']);
                    $otros_ingresos_3 = pv($perfil, $solicitud, ['otros_ingresos_3_amount']);
                    $otros_ingresos_1_num = is_numeric($otros_ingresos_1) ? floatval($otros_ingresos_1) : 0;
                    $otros_ingresos_2_num = is_numeric($otros_ingresos_2) ? floatval($otros_ingresos_2) : 0;
                    $otros_ingresos_3_num = is_numeric($otros_ingresos_3) ? floatval($otros_ingresos_3) : 0;
                    $total_otros_ingresos = $otros_ingresos_1_num + $otros_ingresos_2_num + $otros_ingresos_3_num;
                    
                    // Determinar el ingreso base según si es asalariado o comerciante
                    $ingreso_mensual_neto = pv($perfil, $solicitud, ['ingreso_mensual_neto']);
                    $ventas_promedio = pv($perfil, $solicitud, ['ventas_promedio_mensual']);
                    
                    // Si tiene ingreso_mensual_neto (es asalariado): usar ese valor
                    if (!empty($ingreso_mensual_neto) && is_numeric($ingreso_mensual_neto) && floatval($ingreso_mensual_neto) > 0) {
                        $pref_ingreso_cordobas = floatval($ingreso_mensual_neto);
                    }
                    // Si no es asalariado: usar ventas_promedio_mensual
                    elseif (!empty($ventas_promedio) && is_numeric($ventas_promedio) && floatval($ventas_promedio) > 0) {
                        $pref_ingreso_cordobas = floatval($ventas_promedio);
                    }
                    // Fallback: intentar con otros campos
                    else {
                        // Si es Negocio propio: sumar ventas_dias_buenos + ventas_dias_malos
                        if ($categoria_empleo === 'Negocio propio') {
                            $ventas_buenos = pv($perfil, $solicitud, ['ventas_dias_buenos', 'ventas_buenos_amount']);
                            $ventas_malos = pv($perfil, $solicitud, ['ventas_dias_malos', 'ventas_malos_amount']);
                            $ventas_buenos_num = is_numeric($ventas_buenos) ? floatval($ventas_buenos) : 0;
                            $ventas_malos_num = is_numeric($ventas_malos) ? floatval($ventas_malos) : 0;
                            if ($ventas_buenos_num > 0 || $ventas_malos_num > 0) {
                                $pref_ingreso_cordobas = $ventas_buenos_num + $ventas_malos_num;
                            }
                        }
                        
                        // Último fallback: campos genéricos
                        if (empty($pref_ingreso_cordobas)) {
                            $pref_ingreso_cordobas = pv($perfil, $solicitud, ['ingreso_mensual_cordobas','ingreso_mensual','ingreso_mensual_cordoba']);
                        }
                    }
                    
                    // Calcular Ingreso Mensual Total: ingreso base + otros ingresos adicionales
                    $ingreso_base_num = is_numeric($pref_ingreso_cordobas) ? floatval($pref_ingreso_cordobas) : 0;
                    $pref_ingreso_total = $ingreso_base_num + $total_otros_ingresos;
                    
                    // Calcular USD: conversión desde córdobas
                    $pref_ingreso_usd = null;
                    if (is_numeric($pref_ingreso_cordobas) && floatval($pref_ingreso_cordobas) != 0) {
                        $pref_ingreso_usd = round(floatval($pref_ingreso_cordobas) / 36.64, 2);
                    } else {
                        $pref_ingreso_usd = pv($perfil, $solicitud, ['ingreso_mensual_usd']);
                    }
                    echo form_open('perfil_integral/save'); ?>
                    <input type="hidden" name="solicitud_id" value="<?php echo html_escape($solicitud->idsolicitud); ?>">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nombres</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo html_escape(pv_full_names($perfil, $solicitud)); ?>" placeholder="Ej: María Kamila"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Apellidos</label>
                            <input type="text" name="primer_apellido" class="form-control" value="<?php echo html_escape(pv_full_surnames($perfil, $solicitud)); ?>" placeholder="Ej: Hernández Morales"> 
                        </div>
                    </div>

                    <!-- Matriz de Evaluación Modal -->
                    <div id="matrizModal" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
                        <div style="background:#fff; max-width:760px; width:94%; margin:0 auto; padding:18px; border-radius:6px; box-shadow:0 8px 30px rgba(0,0,0,0.3);">
                            <h5 style="margin-top:0;">Matriz de Evaluación</h5>
                            <p style="color:#555;">Marque las casillas correspondientes. Cada casilla suma la puntuación indicada.</p>
                            <div id="matriz_questions_container" style="max-height:420px; overflow:auto; border:1px solid #eee; padding:12px; border-radius:4px; background:#fafafa;">
                                <em id="matriz_empty_hint">Aún no hay preguntas definidas.</em>
                                <!-- Questions will be injected here by JS -->
                            </div>
                            <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:8px;">
                                <button type="button" id="matrizCancel" class="btn btn-secondary">Cancelar</button>
                                <button type="button" id="matrizSave" class="btn btn-primary">Guardar Matriz</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function(){
                            var modal = document.getElementById('matrizModal');
                            var btnOpen = document.getElementById('btnOpenMatriz');
                            var btnCancel = document.getElementById('matrizCancel');
                            var btnSave = document.getElementById('matrizSave');
                            var container = document.getElementById('matriz_questions_container');
                            // lazy resolver to avoid referencing elements before they exist in DOM
                            var getEl = function(id){ try{ return document.getElementById(id); }catch(e){ return null; } };

                            // Function to update Tipo de DDC based on Nivel de Riesgo
                            // Logic: BAJO or MEDIO -> DDC-S, ALTO -> DDC-I
                            function updateTipoDDC(nivelRiesgo){
                                try{
                                    var tipoDdcInput = document.querySelector('input[name="tipo_ddc"]');
                                    if(!tipoDdcInput) return;
                                    
                                    var nivel = (nivelRiesgo || '').toUpperCase();
                                    if(nivel === 'BAJO' || nivel === 'MEDIO'){
                                        tipoDdcInput.value = 'DDC-S';
                                    } else if(nivel === 'ALTO'){
                                        tipoDdcInput.value = 'DDC-I';
                                    }
                                    // For other values like 'Simplificada' or empty, leave unchanged
                                }catch(e){
                                    console.error('Error updating tipo_ddc:', e);
                                }
                            }

                            // Example: function to populate questions. Supports either flat array of items or grouped questions:
                            // flat: [{id:'q1',text:'...',value:10}, ...]
                            // grouped: [{ group:'Tipo', items:[{id:'q1',text:'..',value:10}, ...] }, ...]
                            window.setMatrizQuestions = function(questions){
                                container.innerHTML = '';
                                if(!Array.isArray(questions) || questions.length === 0){
                                    container.innerHTML = '<em id="matriz_empty_hint">Aún no hay preguntas definidas.</em>';
                                    return;
                                }

                                var html = ['<div style="display:flex;flex-direction:column;gap:12px;">'];

                                // detect grouped structure
                                var isGrouped = questions.length>0 && typeof questions[0].group !== 'undefined' && Array.isArray(questions[0].items);

                                if(isGrouped){
                                    questions.forEach(function(g){
                                        html.push('<div style="border-bottom:1px solid #eee;padding-bottom:8px;">');
                                        html.push('<div style="font-weight:600;margin-bottom:6px;">'+(g.group||'')+'</div>');
                                        html.push('<div style="display:flex;flex-direction:column;gap:6px;">');
                                        (g.items||[]).forEach(function(q){
                                            var val = (typeof q.value !== 'undefined' && !isNaN(parseFloat(q.value))) ? parseFloat(q.value) : 1;
                                            var grp = (g.group||'').replace(/"/g, '&quot;');
                                            html.push('<label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" data-group="'+grp+'" data-qid="'+(q.id||'')+'" data-qval="'+val+'"> <span>'+ (q.text || '') + ' <small style="color:#666;margin-left:8px;">('+val+' pts)</small></span></label>');
                                        });
                                        html.push('</div>');
                                        html.push('</div>');
                                    });
                                } else {
                                    html.push('<div style="display:flex;flex-direction:column;gap:8px;">');
                                    questions.forEach(function(q){
                                        var val = (typeof q.value !== 'undefined' && !isNaN(parseFloat(q.value))) ? parseFloat(q.value) : 1;
                                        html.push('<label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" data-qid="'+(q.id||'')+'" data-qval="'+val+'"> <span>'+ (q.text || '') + ' <small style="color:#666;margin-left:8px;">('+val+' pts)</small></span></label>');
                                    });
                                    html.push('</div>');
                                }

                                html.push('</div>');
                                container.innerHTML = html.join('');
                            };

                            function computeMatrizScore(overrideNivelRiesgo = true){
                                var checks = container.querySelectorAll('input[type="checkbox"]');
                                var total = 0;
                                var answers = [];
                                checks.forEach(function(c){
                                    var id = c.getAttribute('data-qid') || '';
                                    var val = parseFloat(c.getAttribute('data-qval') || '1') || 0;
                                    if(c.checked){ total += val; answers.push(id); }
                                });
                                var sd = getEl('matriz_score_display'); if(sd) sd.value = Number(total.toFixed(2));
                                var sh = getEl('matriz_score'); if(sh) sh.value = total;
                                var ah = getEl('matriz_answers'); if(ah) ah.value = JSON.stringify(answers);

                                // Compute risk level following Excel formula provided by the user:
                                // =IF(C8=100;"ALTO";IF(C13<=350;"BAJO";IF(C13<=450;"MEDIO";IF(C13<=800;"ALTO";0))))
                                // Map: C8 -> value of 'tipo_juridica' checkbox (if checked)
                                var tipoJuridicaChecked = false;
                                var tipoJElem = container.querySelector('input[data-qid="tipo_juridica"]');
                                if(tipoJElem && tipoJElem.checked) tipoJuridicaChecked = true;

                                var risk = '0';
                                if(tipoJuridicaChecked){
                                    risk = 'Alto';
                                } else if(total <= 350){
                                    risk = 'Bajo';
                                } else if(total <= 450){
                                    risk = 'Medio';
                                } else if(total <= 800){
                                    risk = 'Alto';
                                } else {
                                    risk = '0';
                                }

                                // Set the Nivel de Riesgo select if present
                                try{
                                    var nivelSel = document.querySelector('select[name="nivel_riesgo"]');
                                    if(nivelSel && overrideNivelRiesgo){
                                        // match option values used in the form (Alto/Medio/Bajo/Simplificada)
                                        if(risk === 'Alto') nivelSel.value = 'Alto';
                                        else if(risk === 'Medio') nivelSel.value = 'Medio';
                                        else if(risk === 'Bajo') nivelSel.value = 'Bajo';
                                        // else leave unchanged
                                    }
                                    
                                    // Update Tipo de DDC según nivel de riesgo
                                    updateTipoDDC(nivelSel ? nivelSel.value : '');
                                }catch(e){}

                                return { total: total, answers: answers, risk: risk };
                            }

                            // initial default questions (from user's attachments) - grouped
                            try{
                                var defaultMatrizQuestions = [
                                    {
                                        group: 'Tipo de Persona',
                                        items: [
                                            { id: 'tipo_natural', text: 'Natural', value: 25 },
                                            { id: 'tipo_juridica', text: 'Jurídica', value: 100 }
                                        ]
                                    },
                                    {
                                        group: 'Ocupación',
                                        items: [
                                            { id: 'propietario', text: 'Propietario', value: 100 },
                                            { id: 'empleado', text: 'Empleado', value: 50 },
                                            { id: 'negocio_propio', text: 'Negocio Propio', value: 50 },
                                            { id: 'estudiante_ocup', text: 'Estudiante', value: 100 },
                                            { id: 'ama_de_casa', text: 'Ama de Casa', value: 100 },
                                            { id: 'jubilado', text: 'Jubilado', value: 100 }
                                        ]
                                    },
                                    {
                                        group: 'Actividad Económica',
                                        items: [
                                            { id: 'agricultura_ganaderia', text: 'Agricultura/Ganadería', value: 100 },
                                            { id: 'actividades_financieras', text: 'Actividades Financieras/Jurídicas', value: 100 },
                                            { id: 'transporte', text: 'Transporte', value: 100 },
                                            { id: 'comercio_servicios', text: 'Comercio/Servicios', value: 50 },
                                            { id: 'industria_manufactura', text: 'Industria/Manufactura', value: 50 },
                                            { id: 'estado', text: 'Estado', value: 50 },
                                            { id: 'construccion', text: 'Construcción', value: 50 },
                                            { id: 'profesionales', text: 'Profesionales', value: 50 },
                                            { id: 'actividades_hogar', text: 'Actividades del Hogar/Estudiante', value: 50 },
                                            { id: 'asalariados', text: 'Asalariados', value: 50 }
                                        ]
                                    },
                                    {
                                        group: 'Garantías',
                                        items: [
                                            { id: 'garantia_hipotecaria', text: 'Garantía Hipotecaria', value: 100 },
                                            { id: 'garantia_prendaria', text: 'Garantía Prendaria', value: 25 },
                                            { id: 'garantia_fidusiaria', text: 'Garantía Fidusiaria', value: 50 }
                                        ]
                                    }
                                    ,
                                    {
                                        group: 'Edad',
                                        items: [
                                            { id: 'edad_21_39', text: 'De 21 a 39 años', value: 25 },
                                            { id: 'edad_40_55', text: 'De 40 a 55 años', value: 50 },
                                            { id: 'edad_mayor_56', text: 'Mayor a 56 Años', value: 100 }
                                        ]
                                    },
                                    {
                                        group: 'Condición PEP',
                                        items: [
                                            { id: 'pep_si', text: 'Si', value: 100 },
                                            { id: 'pep_no', text: 'No', value: 50 }
                                        ]
                                    },
                                    {
                                        group: '¿Es Frecuente?',
                                        items: [
                                            { id: 'frecuente_si', text: 'Si', value: 25 },
                                            { id: 'frecuente_no', text: 'No', value: 25 },
                                            { id: 'frecuente_recomendado', text: 'Recomendado', value: 50 }
                                        ]
                                    }
                                    ,
                                    {
                                        group: 'Zona geográfica',
                                        items: [
                                            { id: 'zona_managua', text: 'Managua', value: 50 },
                                            { id: 'zona_matagalpa', text: 'Matagalpa', value: 100 },
                                            { id: 'zona_chinandega', text: 'Chinandega', value: 100 },
                                            { id: 'zona_leon', text: 'León', value: 100 },
                                            { id: 'zona_carazo', text: 'Carazo', value: 50 },
                                            { id: 'zona_granada', text: 'Granada', value: 50 },
                                            { id: 'zona_masaya', text: 'Masaya', value: 50 },
                                            { id: 'zona_raccn', text: 'RACCN', value: 100 },
                                            { id: 'zona_esteli', text: 'Estelí', value: 100 },
                                            { id: 'zona_rivas', text: 'Rivas', value: 100 },
                                            { id: 'zona_jinotega', text: 'Jinotega', value: 100 },
                                            { id: 'zona_raccs', text: 'RACCS', value: 100 },
                                            { id: 'zona_chontales', text: 'Chontales', value: 50 },
                                            { id: 'zona_zelaya', text: 'Zelaya Central', value: 100 },
                                            { id: 'zona_triangulo_minero', text: 'Triángulo Minero', value: 100 },
                                            { id: 'zona_nueva_segovia', text: 'Nueva Segovia', value: 100 },
                                            { id: 'zona_boaco', text: 'Boaco', value: 25 },
                                            { id: 'zona_madridz', text: 'Madriz', value: 25 },
                                            { id: 'zona_rio_san_juan', text: 'Río San Juan', value: 25 }
                                        ]
                                    },
                                    {
                                        group: 'Valor de Transacción',
                                        items: [
                                            { id: 'valor_usd_100_500', text: 'USD 100 - 500', value: 25 },
                                            { id: 'valor_usd_500_1000', text: 'USD 500.01 - 1,000.00', value: 50 },
                                            { id: 'valor_usd_1000_1500', text: 'USD 1,000.01 - 1,500.00', value: 50 },
                                            { id: 'valor_usd_1500_2000', text: 'USD 1,500.01 - 2,000.00', value: 100 },
                                            { id: 'valor_usd_2000_5000', text: 'USD 2,000.01 - 5,000.00', value: 100 },
                                            { id: 'valor_usd_10001_more', text: 'mayor a USD 10,001.00', value: 100 }
                                        ]
                                    }
                                ];
                                if(container) setMatrizQuestions(defaultMatrizQuestions);
                                // Hide visible point labels to keep scores private
                                try{ var style = document.createElement('style'); style.innerHTML = '#matriz_questions_container small{ display:none !important; }'; document.head.appendChild(style); }catch(e){}
                                // compute initial score/risk without overriding a saved Nivel de Riesgo
                                try{ computeMatrizScore(false); }catch(e){ console.warn('computeMatrizScore initial call failed (might be before inputs exist)'); }
                            }catch(e){}

                            // open modal: bind after DOM is ready (btnOpen is rendered after this script)
                            function bindOpenButton(){
                                var b = document.getElementById('btnOpenMatriz');
                                if(b){ b.addEventListener('click', function(){ modal.style.display = 'flex'; }); }
                                console.log('Matriz: bindOpenButton attached?', !!b);
                            }
                            if(document.readyState === 'loading'){
                                document.addEventListener('DOMContentLoaded', bindOpenButton);
                            } else { bindOpenButton(); }

                            // Bind listener for manual change of Nivel de Riesgo to update Tipo de DDC
                            function bindNivelRiesgoListener(){
                                var nivelSel = document.querySelector('select[name="nivel_riesgo"]');
                                if(nivelSel){
                                    nivelSel.addEventListener('change', function(){
                                        updateTipoDDC(this.value);
                                    });
                                    // Also update on page load with current value
                                    updateTipoDDC(nivelSel.value);
                                }
                            }
                            if(document.readyState === 'loading'){
                                document.addEventListener('DOMContentLoaded', bindNivelRiesgoListener);
                            } else { bindNivelRiesgoListener(); }

                            // Bind cancel/save after DOM ready to ensure elements exist
                            function bindModalControls(){
                                var bc = document.getElementById('matrizCancel');
                                var bs = document.getElementById('matrizSave');
                                if(bc){ bc.addEventListener('click', function(){ modal.style.display='none'; }); }
                                if(bs){ bs.addEventListener('click', function(){
                                    var res = computeMatrizScore();
                                    // require at least one checked
                                    if(!res.answers || res.answers.length === 0){ alert('Debe seleccionar al menos una opción en la Matriz.'); return; }
                                    // prepare ajax payload from current form values
                                    try{
                                        var form = document.querySelector('form');
                                        // Use the full form FormData so any CSRF token is included
                                        var fd = new FormData(form);
                                        // ensure hidden fields are up to date/override (use lazy resolver)
                                        var sh2 = getEl('matriz_score'); if(sh2) fd.set('matriz_score', sh2.value || '');
                                        var ah2 = getEl('matriz_answers'); if(ah2) fd.set('matriz_answers', ah2.value || '');
                                        fd.set('nivel_riesgo', (document.querySelector('select[name="nivel_riesgo"]')||{value:''}).value);

                                        // UI feedback
                                        bs.disabled = true; var prevText = bs.innerText; bs.innerText = 'Guardando...';

                                        // send AJAX POST to controller endpoint
                                        var endpoint = '<?php echo base_url('perfil_integral/save_matriz_ajax'); ?>';
                                        console.log('Matriz: posting to', endpoint, 'payload keys:', Array.from(fd.keys()));
                                        fetch(endpoint, {
                                            method: 'POST',
                                            body: fd,
                                            credentials: 'same-origin',
                                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                        }).then(function(resp){
                                            return resp.text().then(function(text){
                                                var parsed = null;
                                                try{ parsed = JSON.parse(text); }catch(e){ parsed = null; }
                                                if(!resp.ok){
                                                    var errtxt = text || resp.statusText || ('HTTP '+resp.status);
                                                    throw new Error('HTTP '+resp.status+' - '+errtxt);
                                                }
                                                if(parsed === null){
                                                    throw new Error('Invalid JSON response: ' + (text||'<empty>'));
                                                }
                                                return parsed;
                                            });
                                        }).then(function(json){
                                            if(json && json.status){
                                                modal.style.display='none';
                                                window.location.reload();
                                            } else {
                                                var msg = (json && json.message) ? json.message : 'error';
                                                alert('Error guardando Matriz: ' + msg);
                                                bs.disabled = false; bs.innerText = prevText;
                                            }
                                        }).catch(function(err){
                                            console.error('Matriz save error', err);
                                            var msg = err && err.message ? err.message : 'Error en conexión al guardar Matriz';
                                            alert('Error guardando Matriz: ' + msg);
                                            bs.disabled = false; bs.innerText = prevText;
                                        });
                                    }catch(e){
                                        console.error(e); alert('Error interno al preparar guardado');
                                    }
                                }); }
                            }
                            if(document.readyState === 'loading'){
                                document.addEventListener('DOMContentLoaded', bindModalControls);
                            } else { bindModalControls(); }

                            // compute live when checking and enforce single selection per group
                            container.addEventListener('change', function(e){
                                if(e.target && e.target.type==='checkbox'){
                                    try{
                                        if(e.target.checked){
                                            var g = e.target.getAttribute('data-group');
                                            if(g){
                                                var others = container.querySelectorAll('input[type="checkbox"][data-group="'+g+'"]');
                                                others.forEach(function(o){ if(o !== e.target) o.checked = false; });
                                            }
                                        }
                                    }catch(err){}
                                    computeMatrizScore();
                                }
                            });

                            // when form loads, reflect existing saved values
                            document.addEventListener('DOMContentLoaded', function(){
                                try{
                                    var ah = getEl('matriz_answers'); var raw = (ah && ah.value) ? ah.value : '';
                                    if(raw){
                                        var arr = JSON.parse(raw);
                                        // if questions are already present, pre-check (best-effort)
                                        var chks = container.querySelectorAll('input[type="checkbox"]');
                                        if(chks && chks.length>0){ chks.forEach(function(c){ if(arr.indexOf(c.getAttribute('data-qid'))!==-1) c.checked = true; }); computeMatrizScore(false); }
                                    }
                                }catch(e){}
                            });

                            // enforce required matrix on submit
                            try{
                                var form = document.querySelector('form');
                                if(form){ form.addEventListener('submit', function(ev){
                                    var ah2 = getEl('matriz_answers'); var v = (ah2 && ah2.value) ? ah2.value : '';
                                    try{ var parsed = JSON.parse(v||'[]'); if(!Array.isArray(parsed) || parsed.length===0){ ev.preventDefault(); ev.stopPropagation(); alert('La Matriz de Evaluación es obligatoria. Abra la Matriz y seleccione al menos una opción.'); return false; } }catch(e){ ev.preventDefault(); ev.stopPropagation(); alert('La Matriz de Evaluación es obligatoria. Abra la Matriz y seleccione al menos una opción.'); return false; }
                                }); }
                            }catch(e){}

                        })();
                    </script>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Fecha del Perfil</label>
                            <input type="date" name="fecha_perfil" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['fecha_perfil'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Nivel de Riesgo</label>
                            <?php $nivel_riesgo_value = strtolower(trim(pv($perfil,$solicitud,['nivel_riesgo']))); ?>
                            <select name="nivel_riesgo" class="form-control">
                                <option value="">--</option>
                                <option value="Alto" <?php echo ($nivel_riesgo_value === 'alto') ? 'selected' : ''; ?>>Alto</option>
                                <option value="Medio" <?php echo ($nivel_riesgo_value === 'medio') ? 'selected' : ''; ?>>Medio</option>
                                <option value="Bajo" <?php echo ($nivel_riesgo_value === 'bajo') ? 'selected' : ''; ?>>Bajo</option>
                                <option value="Simplificada" <?php echo ($nivel_riesgo_value === 'simplificada') ? 'selected' : ''; ?>>Simplificada</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Evaluación Matriz</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <button type="button" id="btnOpenMatriz" class="btn btn-outline-primary">Abrir Matriz</button>
                                <input type="text" id="matriz_score_display" class="form-control" readonly placeholder="Puntaje" style="max-width:120px;">
                            </div>
                            <input type="hidden" name="matriz_score" id="matriz_score" value="<?php echo isset($perfil->matriz_score)?html_escape($perfil->matriz_score):''; ?>">
                            <input type="hidden" name="matriz_answers" id="matriz_answers" value="<?php echo isset($perfil->matriz_answers)?html_escape($perfil->matriz_answers):''; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tipo de DDC según nivel de riesgo</label>
                            <input type="text" name="tipo_ddc" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['tipo_ddc'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nombre con el que se le conoce</label>
                            <input type="text" name="nombre_conocido" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['nombre_conocido','nombre_conocido'])); ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Estado Civil</label>
                            <input type="text" name="estado_civil" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['estado_civil'])); ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sexo</label>
                            <select name="sexo" class="form-control">
                                <option value="" <?php echo ($pref_sexo==='' || $pref_sexo===null)?'selected':''; ?>>--</option>
                                <option value="F" <?php echo ($pref_sexo==='F' || $pref_sexo==='Mujer' || strtolower($pref_sexo)==='f')?'selected':''; ?>>F</option>
                                <option value="M" <?php echo ($pref_sexo==='M' || $pref_sexo==='Varón' || $pref_sexo==='Hombre' || strtolower($pref_sexo)==='m')?'selected':''; ?>>M</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>N° Dependientes</label>
                            <input type="number" name="n_dependientes" class="form-control" value="<?php echo html_escape($pref_n_dependientes); ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label>País de nacimiento</label>
                            <input type="text" name="pais_nacimiento" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['pais_nacimiento','pais_nac'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Tipo Documento</label>
                            <select name="tipo_documento" class="form-control">
                                <option value="">-- Seleccione --</option>
                                <option value="Cedula Identidad" <?php echo (pv($perfil, $solicitud, ['tipo_documento']) == 'Cedula Identidad' ? 'selected' : ''); ?>>Cédula Identidad</option>
                                <option value="Cedula RUC" <?php echo (pv($perfil, $solicitud, ['tipo_documento']) == 'Cedula RUC' ? 'selected' : ''); ?>>Cédula RUC</option>
                                <option value="Pasaporte" <?php echo (pv($perfil, $solicitud, ['tipo_documento']) == 'Pasaporte' ? 'selected' : ''); ?>>Pasaporte</option>
                                <option value="Cedula Residente" <?php echo (pv($perfil, $solicitud, ['tipo_documento']) == 'Cedula Residente' ? 'selected' : ''); ?>>Cédula Residente</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>N° Documento</label>
                            <input type="text" name="numero_documento" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['numero_documento','numero_doc','documento'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>N° de registro de Cedula</label>
                            <input type="text" name="numero_registro" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['numero_registro'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>País emisión</label>
                            <input type="text" name="pais_emision_documento" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['pais_emision_documento'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Fecha emisión</label>
                            <input type="date" name="fecha_emision_documento" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['fecha_emision_documento'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Fecha de vencimiento</label>
                            <input type="date" name="fecha_vencimiento_documento" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['fecha_vencimiento_documento'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Fecha Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo html_escape($pref_fecha_nacimiento ?: ''); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?php echo html_escape($pref_telefono ?: ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>¿En su propio país?</label>
                            <select name="en_su_propio_pais" class="form-control">
                                <option value="0" <?php echo (pv($perfil,$solicitud,['en_su_propio_pais'])==0)?'selected':''; ?>>No</option>
                                <option value="1" <?php echo (pv($perfil,$solicitud,['en_su_propio_pais'])==1)?'selected':''; ?>>Sí</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>¿Se desempeñó como funcionario público de alta jerarquía?</label>
                            <select name="es_funcionario_publico" class="form-control">
                                <option value="0" <?php echo (pv($perfil,$solicitud,['es_funcionario_publico'])==0)?'selected':''; ?>>No</option>
                                <option value="1" <?php echo (pv($perfil,$solicitud,['es_funcionario_publico'])==1)?'selected':''; ?>>Sí</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>En caso afirmativo, indicar el cargo</label>
                            <input type="text" name="cargo_funcionario" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['cargo_funcionario'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Celular</label>
                            <input type="text" name="celular" class="form-control" value="<?php echo html_escape($pref_celular ?: ''); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['email'])); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['direccion'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" value="<?php echo html_escape(pv($perfil, $solicitud, ['ciudad'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Categoria (empleo)</label>
                            <select name="categoria_empleo" class="form-control">
                                <option value="">--</option>
                                <option value="Empleado" <?php echo (pv($perfil,$solicitud,['categoria_empleo'])=='Empleado')?'selected':''; ?>>Empleado</option>
                                <option value="Negocio propio" <?php echo (pv($perfil,$solicitud,['categoria_empleo'])=='Negocio propio')?'selected':''; ?>>Negocio propio</option>
                                <option value="Estudiante" <?php echo (pv($perfil,$solicitud,['categoria_empleo'])=='Estudiante')?'selected':''; ?>>Estudiante</option>
                                <option value="Ama de casa" <?php echo (pv($perfil,$solicitud,['categoria_empleo'])=='Ama de casa')?'selected':''; ?>>Ama de casa</option>
                                <option value="Jubilado" <?php echo (pv($perfil,$solicitud,['categoria_empleo'])=='Jubilado')?'selected':''; ?>>Jubilado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ocupación</label>
                            <input type="text" name="ocupacion" class="form-control" value="<?php echo html_escape($pref_ocupacion ?: ''); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Empresa</label>
                            <input type="text" name="empresa" class="form-control" value="<?php echo html_escape($pref_empresa ?: ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Especifique otra categoría</label>
                            <input type="text" name="categoria_otro" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['categoria_otro'])); ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Zona de cobertura</label>
                            <select name="zona_cobertura" class="form-control">
                                <option value="">--</option>
                                <option value="Internacional" <?php echo (pv($perfil,$solicitud,['zona_cobertura'])=='Internacional')?'selected':''; ?>>Internacional</option>
                                <option value="Regional" <?php echo (pv($perfil,$solicitud,['zona_cobertura'])=='Regional')?'selected':''; ?>>Regional</option>
                                <option value="Nacional" <?php echo (pv($perfil,$solicitud,['zona_cobertura'])=='Nacional')?'selected':''; ?>>Nacional</option>
                                <option value="Local" <?php echo (pv($perfil,$solicitud,['zona_cobertura'])=='Local')?'selected':''; ?>>Local</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Sitio web centro de trabajo</label>
                            <input type="text" name="sitio_web_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['sitio_web_centro_trabajo'])); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Ingreso Mensual (USD)</label>
                            <input type="number" step="0.01" name="ingreso_mensual_usd" class="form-control" value="<?php echo html_escape($pref_ingreso_usd); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ingreso Mensual (C$)</label>
                            <input type="number" step="0.01" name="ingreso_mensual_cordobas" class="form-control" value="<?php echo html_escape($pref_ingreso_cordobas); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ingreso Mensual (total)</label>
                            <input type="number" step="0.01" name="ingreso_mensual" class="form-control" value="<?php echo html_escape($pref_ingreso_total); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Antigüedad laboral</label>
                            <input type="text" name="antiguedad_laboral" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['antiguedad_laboral','tiempo_empleo_anios'])); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Observaciones / Otros</label>
                            <textarea name="otros" class="form-control"><?php echo html_escape(pv($perfil,$solicitud,['otros','observaciones_promotor','observaciones'])); ?></textarea>
                        </div>
                    </div>

                    <hr />
                    <h5>Datos del cónyuge / unión de hecho</h5>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Nombre completo del cónyuge / unión de hecho</label>
                            <input type="text" name="nombre_conyuge" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['nombre_conyuge']) ?: trim(pv($perfil,$solicitud,['conyuge_primer_nombre']) . ' ' . pv($perfil,$solicitud,['conyuge_segundo_nombre']) . ' ' . pv($perfil,$solicitud,['conyuge_primer_apellido']) . ' ' . pv($perfil,$solicitud,['conyuge_segundo_apellido']))); ?>" placeholder="Ej: Jose Mario Lopez Fernandez">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Dirección del domicilio</label>
                            <input type="text" name="conyuge_direccion" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_direccion'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>N° teléfono del domicilio</label>
                            <input type="text" name="conyuge_telefono_domicilio" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_telefono_domicilio'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>N° de celular</label>
                            <input type="text" name="conyuge_celular" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_celular'])); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Correo electrónico personal</label>
                            <input type="email" name="conyuge_email_personal" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_email_personal'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Profesión</label>
                            <input type="text" name="conyuge_profesion" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_profesion'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ocupación actual</label>
                            <input type="text" name="conyuge_ocupacion_actual" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_ocupacion_actual'])); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nombre del centro de trabajo</label>
                            <input type="text" name="conyuge_nombre_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_nombre_centro_trabajo'])); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Dirección centro de trabajo</label>
                            <input type="text" name="conyuge_direccion_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_direccion_centro_trabajo'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Email centro de trabajo</label>
                            <input type="email" name="conyuge_email_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_email_centro_trabajo'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Sitio web</label>
                            <input type="text" name="conyuge_sitio_web" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_sitio_web'])); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Teléfono centro trabajo</label>
                            <input type="text" name="conyuge_telefono_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_telefono_centro_trabajo'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Fax centro trabajo</label>
                            <input type="text" name="conyuge_fax_centro_trabajo" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_fax_centro_trabajo'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Apartado postal</label>
                            <input type="text" name="conyuge_apartado_postal" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_apartado_postal'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ingreso mensual (USD)</label>
                            <input type="number" step="0.01" name="conyuge_ingreso_usd" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_ingreso_usd'])); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Ingreso mensual (C$) cónyuge</label>
                            <input type="number" step="0.01" name="conyuge_ingreso_cordobas" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['conyuge_ingreso_cordobas'])); ?>">
                        </div>
                    </div>

                    <hr />
                    <h5>Documentos legales (actividad económica)</h5>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Doc1 - N°</label>
                            <input type="text" name="documento_legal_1_numero" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_1_numero'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc1 - País emisión</label>
                            <input type="text" name="documento_legal_1_pais_emision" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_1_pais_emision'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc1 - Departamento y Municipio</label>
                            <input type="text" name="documento_legal_1_departamento_municipio" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_1_departamento_municipio'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc1 - Fecha emisión</label>
                            <input type="date" name="documento_legal_1_fecha_emision" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_1_fecha_emision'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc1 - Fecha venc.</label>
                            <input type="date" name="documento_legal_1_fecha_vencimiento" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_1_fecha_vencimiento'])); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Doc2 - N°</label>
                            <input type="text" name="documento_legal_2_numero" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_2_numero'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc2 - País emisión</label>
                            <input type="text" name="documento_legal_2_pais_emision" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_2_pais_emision'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc2 - Fecha emisión</label>
                            <input type="date" name="documento_legal_2_fecha_emision" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_2_fecha_emision'])); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Doc2 - Fecha venc.</label>
                            <input type="date" name="documento_legal_2_fecha_vencimiento" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['documento_legal_2_fecha_vencimiento'])); ?>">
                        </div>
                    </div>

                    <!-- Propósito y Actividad esperada se muestran más abajo, después de Origen de fondos -->

                    <!-- Documentos doc1/doc2 hidden per user preference -->

                    <hr />
                    <h5>Información acerca de la relación de negocios con Crediblamen S.A</h5>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <div class="form-check">
                                <?php $tr = isset($perfil->tipo_relacion)?(is_string($perfil->tipo_relacion)?$perfil->tipo_relacion:json_encode($perfil->tipo_relacion)):''; $tr_arr = [];
                                      if (!empty($tr)) { $tdec = @json_decode($tr,true); if (is_array($tdec)) $tr_arr = $tdec; }
                                ?>
                                <label class="form-check-label">Tipo de relación de negocios con Crediblamen S.A:</label>
                                <div>
                                    <?php $opts = ['Compra y venta de bienes inmobiliarios','Administración de dinero, valores u otros activos','Otorgamiento de microcréditos a personas naturales y jurídicas','Organización de contribuciones para la creación, operación o administración','Creación, operación o administración de personas jurídicas u otras','Otro'];
                                    foreach ($opts as $o) : ?>
                                        <label class="checkbox-inline mr-3"><input type="checkbox" name="tipo_relacion[]" value="<?php echo html_escape($o); ?>" <?php echo in_array($o,$tr_arr)?'checked':''; ?>> <?php echo htmlspecialchars($o); ?></label>
                                    <?php endforeach; ?>
                                </div>
                                <div style="margin-top:8px;">
                                    <label>Otro (indique):</label>
                                    <input type="text" name="tipo_relacion_otro" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['tipo_relacion_otro'])); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mover Origen de fondos aquí (debajo de Información acerca de la relación de negocios) -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Origen de fondos</label>
                            <?php $of = isset($perfil->origen_fondos)?(is_string($perfil->origen_fondos)?$perfil->origen_fondos:json_encode($perfil->origen_fondos)):''; $of_arr = []; if (!empty($of)) { $try = @json_decode($of,true); if (is_array($try)) $of_arr = $try; else { $try2 = @unserialize($of); if (is_array($try2)) $of_arr = $try2; else $of_arr = array_filter(array_map('trim', explode(',', $of))); } }
                                  $of_opts = ['Préstamo','Venta de activos','Ahorro','Transferencia de fondos','salarios','Negocios','Remesas','Herencias','Donación','Dividendos'];
                            ?>
                            <div>
                                <?php foreach ($of_opts as $o): ?>
                                    <label class="checkbox-inline mr-3"><input type="checkbox" name="origen_fondos[]" value="<?php echo html_escape($o); ?>" <?php echo in_array($o,$of_arr)?'checked':''; ?>> <?php echo htmlspecialchars($o); ?></label>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top:6px;"><label>Otros (explicar):</label><input type="text" name="origen_otros" class="form-control" value="<?php echo html_escape(pv($perfil,$solicitud,['origen_otros'])); ?>"></div>
                        </div>
                    </div>

                    <!-- Mover Propósito y Actividad esperada debajo de Origen de fondos -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Propósito de la relación</label>
                            <textarea name="proposito_relacion" class="form-control"><?php echo html_escape(pv($perfil,$solicitud,['proposito_relacion'])); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
<parameter name="label">Actividad esperada</label>
                            <?php
                                // Prepare JS-friendly JSON for existing rows
                                $raw_act = pv($perfil,$solicitud,['actividad_esperada_json','actividad_esperada']);
                                $act_rows = [];
                                if (!empty($raw_act)) {
                                    $dec = @json_decode($raw_act, true);
                                    if (is_array($dec)) $act_rows = $dec;
                                    else {
                                        $try = @unserialize($raw_act);
                                        if (is_array($try)) $act_rows = $try;
                                    }
                                }
                                
                                // Si no hay filas, calcular la primera fila automáticamente desde la solicitud
                                if (empty($act_rows) && !empty($solicitud)) {
                                    $plazo_meses = pv($perfil, $solicitud, ['plazo_meses']);
                                    $frecuencia = pv($perfil, $solicitud, ['frecuencia']);
                                    $cuota_estimada = pv($perfil, $solicitud, ['cuota_estim_estimada', 'cuota_estimada']);
                                    
                                    // Calcular número de transacciones: plazo_meses * frecuencia
                                    $numero_transacciones = '';
                                    if (is_numeric($plazo_meses) && !empty($frecuencia)) {
                                        $multiplicador = 1; // mensual por defecto
                                        if (strtolower($frecuencia) === 'quincenal') {
                                            $multiplicador = 2;
                                        } elseif (strtolower($frecuencia) === 'catorcenal') {
                                            $multiplicador = 2;
                                        } elseif (strtolower($frecuencia) === 'semanal') {
                                            $multiplicador = 4;
                                        }
                                        $numero_transacciones = intval($plazo_meses) * $multiplicador;
                                    }
                                    
                                    // Agregar primera fila con datos calculados
                                    if ($numero_transacciones !== '' || $cuota_estimada !== '' || $plazo_meses !== '') {
                                        $act_rows[] = [
                                            'numero_transacciones' => (string)$numero_transacciones,
                                            'monto_promedio' => $cuota_estimada ?: '',
                                            'periodo' => $plazo_meses ? $plazo_meses . ' meses' : ''
                                        ];
                                    }
                                }
                                
                                // Ensure each row has keys: numero_transacciones, monto_promedio, periodo
                                foreach ($act_rows as &$r) {
                                    if (!isset($r['numero_transacciones'])) $r['numero_transacciones'] = '';
                                    if (!isset($r['monto_promedio'])) $r['monto_promedio'] = '';
                                    if (!isset($r['periodo'])) $r['periodo'] = '';
                                }
                                unset($r);
                            ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="actividad_esperada_table">
                                    <thead>
                                        <tr>
                                            <th style="width:35%;">Número de transacciones</th>
                                            <th style="width:35%;">Monto promedio</th>
                                            <th style="width:25%;">Periodo</th>
                                            <th style="width:5%;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mb-2">
                                <button type="button" id="add_actividad_row" class="btn btn-sm btn-outline-primary">Agregar fila</button>
                            </div>
                            <!-- Hidden field that will contain JSON representation for controller -->
                            <input type="hidden" name="actividad_esperada_json" id="actividad_esperada_json" value="<?php echo html_escape(json_encode($act_rows)); ?>">
                            <small class="form-text text-muted">Agrega filas y completa los valores. Se guardarán como tabla.</small>

                            <div class="form-group mt-2">
                                <label>Observaciones sobre la actividad esperada</label>
                                <textarea name="actividad_esperada_observaciones" class="form-control" maxlength="5000"><?php echo html_escape(pv($perfil,$solicitud,['actividad_esperada_observaciones','actividad_observaciones'])); ?></textarea>
                                <small class="form-text text-muted">Puede dejar hasta 5000 caracteres para detallar observaciones adicionales.</small>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function(){
                            var table = document.getElementById('actividad_esperada_table').getElementsByTagName('tbody')[0];
                            var hidden = document.getElementById('actividad_esperada_json');
                            var existing = [];
                            try { existing = JSON.parse(hidden.value || '[]'); } catch(e) { existing = []; }

                            function createRow(rowData) {
                                var tr = document.createElement('tr');
                                var td1 = document.createElement('td');
                                var inp1 = document.createElement('input'); inp1.type='text'; inp1.className='form-control form-control-sm'; inp1.name='actividad_numero[]'; inp1.value = rowData.numero_transacciones || '';
                                td1.appendChild(inp1);

                                var td2 = document.createElement('td');
                                var inp2 = document.createElement('input'); inp2.type='text'; inp2.className='form-control form-control-sm'; inp2.name='actividad_monto[]'; inp2.value = rowData.monto_promedio || '';
                                td2.appendChild(inp2);

                                var td3 = document.createElement('td');
                                var inp3 = document.createElement('input'); inp3.type='text'; inp3.className='form-control form-control-sm'; inp3.name='actividad_periodo[]'; inp3.value = rowData.periodo || '';
                                td3.appendChild(inp3);

                                var td4 = document.createElement('td');
                                var btn = document.createElement('button'); btn.type='button'; btn.className='btn btn-sm btn-danger remove-actividad'; btn.innerText='Eliminar';
                                td4.appendChild(btn);

                                tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
                                table.appendChild(tr);
                            }

                            // populate existing rows
                            if (existing.length) {
                                for (var i=0;i<existing.length;i++) createRow(existing[i]);
                            } else {
                                // add one empty row by default
                                createRow({});
                            }

                            document.getElementById('add_actividad_row').addEventListener('click', function(){ createRow({}); });

                            // delegate remove
                            table.addEventListener('click', function(e){
                                if (e.target && e.target.classList.contains('remove-actividad')) {
                                    var tr = e.target.closest('tr'); if (tr) tr.parentNode.removeChild(tr);
                                }
                            });

                            // before submit, serialize rows into hidden input
                            var form = document.querySelector('form');
                            if (form) {
                                form.addEventListener('submit', function(){
                                    var out = [];
                                    var rows = table.querySelectorAll('tr');
                                    rows.forEach(function(r){
                                        var n = r.querySelector('input[name="actividad_numero[]"]');
                                        var m = r.querySelector('input[name="actividad_monto[]"]');
                                        var p = r.querySelector('input[name="actividad_periodo[]"]');
                                        if (n || m || p) {
                                            out.push({
                                                numero_transacciones: n ? n.value.trim() : '',
                                                monto_promedio: m ? m.value.trim() : '',
                                                periodo: p ? p.value.trim() : ''
                                            });
                                        }
                                    });
                                    hidden.value = JSON.stringify(out);
                                });
                            }
                        })();
                    </script>

                    <div class="form-group">
                        <button type="submit" id="btnGuardarPerfil" class="btn btn-primary">Guardar Perfil</button>
                        <a class="btn btn-secondary" href="<?php echo base_url('perfil_integral'); ?>">Volver al listado</a>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>

        </div>
    </div>
</div>
