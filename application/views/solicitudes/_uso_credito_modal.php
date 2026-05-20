<!-- Shared Modal: Formato Uso de Crédito (used by listado and standalone page) -->
<div class="modal fade" id="usoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Formato de Uso de Crédito - Solicitud <span id="uso_ids"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-12 mb-2"><strong>INFORMACIÓN GENERAL DEL SOLICITANTE</strong></div>
                    <div class="col-md-6 form-group">
                        <label>Nombre Completo</label>
                        <input id="uso_nombre_completo" class="form-control" readonly />
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Número de Identificación</label>
                        <input id="uso_identificacion" class="form-control" readonly />
                    </div>
                    <!-- Teléfono de Contacto (oculto por requerimiento) -->
                    <div class="col-md-4 form-group" style="display:none;">
                        <label>Teléfono de Contacto</label>
                        <input id="uso_telefono" class="form-control" readonly />
                    </div>
                    <!-- Correo Electrónico (oculto por requerimiento) -->
                    <div class="col-md-4 form-group" style="display:none;">
                        <label>Correo Electrónico</label>
                        <input id="uso_email" class="form-control" readonly />
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Fecha de Solicitud</label>
                        <input id="uso_fecha_solicitud" class="form-control" readonly />
                    </div>
                </div>

                <!-- Additional solicitud fields (prefilled) -->
                <!-- Hidden solicitud fields: values are preserved but not shown in the UI -->
                <input type="hidden" id="uso_destino_credito" />
                <input type="hidden" id="uso_rubro_conami" />
                <input type="hidden" id="uso_firma_solicitante" />
                <input type="hidden" id="uso_fecha_firma" />
                <input type="hidden" id="uso_nombre_promotor" />
                <input type="hidden" id="uso_observaciones_promotor" />
                <input type="hidden" id="uso_ddc_investigacion" />
                <input type="hidden" id="uso_es_nuevo" />
                <input type="hidden" id="uso_es_renovacion" />

                <hr/>

                <div class="form-row">
                    <div class="col-12 mb-2"><strong>DETALLE DEL USO DEL CRÉDITO SOLICITADO</strong></div>
                    <div class="col-md-6 form-group">
                        <label>1. Monto Solicitado</label>
                        <input id="uso_monto_solicitado" name="monto_solicitado" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label>2. Plazo Solicitado (en meses)</label>
                        <input id="uso_plazo_solicitado" name="plazo_solicitado" class="form-control" />
                    </div>
                </div>

                <hr/>

                <div class="form-row">
                    <div class="col-12 mb-2"><strong>3. Destino del Préstamo</strong></div>
                    <div class="col-12 form-group">
                        <label>Destino</label>
                        <input id="uso_destino_simple" class="form-control" readonly />
                    </div>
                    <div class="col-12 form-group">
                        <label>Detalle del uso</label>
                        <textarea id="uso_destino_detalle" name="destino_detalle" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <hr/>

                <div class="form-row">
                    <div class="col-12 mb-2"><strong>DESCRIPCIÓN DETALLADA DEL USO DEL CRÉDITO</strong></div>
                    <div class="col-12 form-group">
                        <label>Descripción del uso del dinero</label>
                        <textarea id="uso_descripcion" name="descripcion" class="form-control" rows="4" placeholder="Por favor, proporcione una descripción detallada del plan para utilizar el crédito solicitado."></textarea>
                    </div>
                    <div class="col-12 mb-2"><strong>PLAN DE PAGOS (si aplica)</strong><small class="d-block text-muted">En caso de que el uso del dinero implique un flujo de ingresos futuro, por favor describa cómo planea generar los recursos para hacer frente al pago del préstamo.</small></div>
                    <div class="col-md-8 form-group">
                        <label>Fuente de ingreso para el pago</label>
                        <input id="uso_fuente_ingreso" name="fuente_ingreso" class="form-control" />
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Monto estimado de ingresos mensuales</label>
                        <input id="uso_monto_estimado" name="monto_estimado_mes" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label>DECLARACIÓN Y AUTORIZACIÓN</label>
                    <p id="uso_declaracion_text" class="small text-justify" style="background:#f8f9fa;padding:10px;border:1px solid #eee;border-radius:4px;">Por favor espere: cargando información del solicitante...</p>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Firma del Solicitante (texto)</label>
                        <input id="uso_declaracion_firma" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Fecha</label>
                        <input id="uso_declaracion_fecha" type="date" class="form-control" />
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label>Uso interno - Evaluador de Crédito</label>
                    <input id="uso_evaluador_credito" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Fecha de Evaluación</label>
                    <input id="uso_fecha_evaluacion" type="date" class="form-control" />
                </div>
                <div id="uso_error" class="text-danger" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="uso_save" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        // Helper: disables all editable fields and hides save button if readonly
        function setUsoReadonly(isReadonly){
            var ids = [
                'uso_monto_solicitado','uso_plazo_solicitado','uso_destino_detalle','uso_descripcion','uso_fuente_ingreso','uso_monto_estimado',
                'uso_declaracion_nombre','uso_declaracion_firma','uso_declaracion_fecha','uso_evaluador_credito','uso_fecha_evaluacion'
            ];
            ids.forEach(function(id){
                var el = document.getElementById(id);
                if(el){
                    if(el.tagName==='INPUT'||el.tagName==='TEXTAREA'){
                        el.readOnly = isReadonly;
                        el.disabled = isReadonly;
                    }
                }
            });
            var btn = document.getElementById('uso_save');
            if(btn){ btn.style.display = isReadonly ? 'none' : ''; }
        }
        var selId = null;
        function renderUso(data){
            if(!data) return;
            var aprob = (data && data.solicitud && data.solicitud.aprob_status) ? data.solicitud.aprob_status : 'pending';
            var isReadonly = (aprob === 'approved' || aprob === 'rejected');
            setUsoReadonly(isReadonly);
            if(data.solicitud){
                var s = data.solicitud;
                // Prefer normalized helper fields when present
                document.getElementById('uso_nombre_completo').value = ((s.nombres || '') + ' ' + (s.apellidos || ''));
                document.getElementById('uso_identificacion').value = s.numero_identificacion || s.cedula || s.identificacion || '';
                console.log("La cédula es: " + s.numero_identificacion + ", o es " + s.cedula + ", o es " + s.identificacion);
                document.getElementById('uso_telefono').value = s.telefono_contacto || s.telefono || s.celular || s.telefono_contacto || '';
                document.getElementById('uso_email').value = s.correo_electronico || s.email || '';
                document.getElementById('uso_fecha_solicitud').value = s.fecha_solicitud || s.fecha_recepcion || '';
                // additional solicitud fields
                if(document.getElementById('uso_destino_credito')) document.getElementById('uso_destino_credito').value = s.destino_credito || '';
                if(document.getElementById('uso_rubro_conami')) document.getElementById('uso_rubro_conami').value = s.rubro_credito || s.destino_conami || '';
                if(document.getElementById('uso_firma_solicitante')) document.getElementById('uso_firma_solicitante').value = s.firma_solicitante || s.firma || '';
                if(document.getElementById('uso_fecha_firma')) document.getElementById('uso_fecha_firma').value = s.fecha_firma || s.fecha_firma_solicitud || '';
                if(document.getElementById('uso_nombre_promotor')) document.getElementById('uso_nombre_promotor').value = s.nombre_promotor || '';
                if(document.getElementById('uso_observaciones_promotor')) document.getElementById('uso_observaciones_promotor').value = s.observaciones_promotor || s.observaciones || '';
                if(document.getElementById('uso_ddc_investigacion')) document.getElementById('uso_ddc_investigacion').value = s.ddc_investigacion_campo || '';
                // flags
                if(document.getElementById('uso_flag_nuevo')){
                    if(s.es_nuevo == 1 || s.es_nuevo === true || s.es_nuevo === '1') { document.getElementById('uso_flag_nuevo').style.display = 'inline-block'; } else { document.getElementById('uso_flag_nuevo').style.display = 'none'; }
                }
                if(document.getElementById('uso_flag_renovacion')){
                    if(s.es_renovacion == 1 || s.es_renovacion === true || s.es_renovacion === '1') { document.getElementById('uso_flag_renovacion').style.display = 'inline-block'; } else { document.getElementById('uso_flag_renovacion').style.display = 'none'; }
                }
                var fullName = ((s.nombres || '') + ' ' + (s.apellidos || '')).trim();
                var idnum = s.numero_identificacion || s.cedula || s.identificacion || '';
                console.log("El nombre completo es: " + fullName);
                console.log("La cédula es: " + idnum);
                // Build a prefix and wrap the remaining paragraph so line lengths (by character count)
                // approximate the prefix length (to match the PDF visual layout requested).
                var prefix = 'Yo, ' + (fullName || '________________________') + ' con Número de Identificación ' + (idnum || '________________') + ', declaro bajo juramento que la información';
                var rest = ' proporcionada en este formato es verídica y completa. Entiendo que la microfinanciera podrá verificar la veracidad de esta información y utilizarla para la evaluación de mi solicitud de crédito. Asimismo, autorizo a la microfinanciera a utilizar los datos proporcionados para fines de análisis y evaluación crediticia.';
                function wrapByChars(text, width){
                    if(!text || !width) return text;
                    var words = text.split(/\s+/);
                    var lines = [];
                    var line = '';
                    for(var i=0;i<words.length;i++){
                        var w = words[i];
                        if((line + ' ' + w).trim().length > width){
                            if(line.trim().length>0) lines.push(line.trim());
                            line = w;
                        } else {
                            line = (line + ' ' + w).trim();
                        }
                    }
                    if(line.trim().length>0) lines.push(line.trim());
                    return lines.join('<br/>');
                }
                var wrappedRest = wrapByChars(rest.trim(), prefix.length);
                var declText = prefix + '<br/>' + wrappedRest;
                var dtEl = document.getElementById('uso_declaracion_text'); if(dtEl) dtEl.innerHTML = declText;
                if((!document.getElementById('uso_declaracion_firma').value || document.getElementById('uso_declaracion_firma').value==='') && fullName) {
                    document.getElementById('uso_declaracion_firma').value = fullName;
                }
            }
            var u = data.uso || {};
            var solic = data.solicitud || {};
            document.getElementById('uso_monto_solicitado').value = u.monto_solicitado || solic.monto_solicitado || solic.cuota_estim_estimada || '';
            document.getElementById('uso_plazo_solicitado').value = u.plazo_solicitado || solic.plazo_solicitado || solic.plazo_meses || solic.plazo || '';
            document.getElementById('uso_descripcion').value = u.descripcion || solic.detalle_inventario || '';
            document.getElementById('uso_fuente_ingreso').value = u.fuente_ingreso || solic.giro_negocio || solic.actividad_economica || '';
            document.getElementById('uso_monto_estimado').value = u.monto_estimado_mes || solic.ingreso_mensual_neto || solic.ventas_promedio_mensual || '';
            // Populate the simple Destino field: prefer uso.destino_prestamo, otherwise use solicitud fields
            var destVal = '';
            if (u.destino_prestamo) destVal = u.destino_prestamo;
            else if (solic.destino_credito) destVal = solic.destino_credito;
            else if (solic.rubro_credito) destVal = solic.rubro_credito;
            else if (solic.destino_conami) destVal = solic.destino_conami;
            else destVal = '';
            if (document.getElementById('uso_destino_simple')) document.getElementById('uso_destino_simple').value = destVal;
            // If there's a solicitud-provided detalle, and u.destino_detalle is empty, prefill it
            if ((!u.destino_detalle || u.destino_detalle === '') && destVal && document.getElementById('uso_destino_detalle')) {
                var detalleVal = destVal;
                document.getElementById('uso_destino_detalle').value = detalleVal;
            }
            document.getElementById('uso_destino_detalle').value = u.destino_detalle || '';
            document.getElementById('uso_declaracion_nombre').value = u.declaracion_nombre || '';
            document.getElementById('uso_declaracion_firma').value = u.declaracion_firma || '';
            if(u.declaracion_fecha){ document.getElementById('uso_declaracion_fecha').value = u.declaracion_fecha; } else { document.getElementById('uso_declaracion_fecha').value = ''; }
            document.getElementById('uso_evaluador_credito').value = u.evaluador_credito || '';
            if(u.fecha_evaluacion){ document.getElementById('uso_fecha_evaluacion').value = u.fecha_evaluacion; } else { document.getElementById('uso_fecha_evaluacion').value = ''; }
        }

        function loadUso(id){
            var url = '<?php echo base_url($this->router->fetch_class() . '/get_uso_ajax/'); ?>' + id;
            document.getElementById('uso_error').style.display='none';
            document.getElementById('uso_error').textContent='';
            document.getElementById('uso_descripcion').value = 'Cargando...';
            fetch(url, { credentials: 'same-origin' })
                .then(function(r){ return r.json(); })
                .then(function(json){
                    // Pass the whole JSON to renderUso so it can read both `uso` and `solicitud` keys
                    if(json && json.status){ renderUso(json); } else { renderUso(null); }
                })
                .catch(function(){ renderUso(null); });
        }

        document.addEventListener('click', function(e){
            var btn = e.target.closest && e.target.closest('.btn-uso');
            if(btn){
                e.preventDefault();
                selId = btn.getAttribute('data-id') || btn.dataset.id;
                document.getElementById('uso_ids').textContent = selId;
                renderUso(null);
                if (typeof jQuery !== 'undefined' && jQuery && jQuery('#usoModal').modal) {
                    jQuery('#usoModal').modal('show');
                }
                loadUso(selId);
            }

            var dlBtn = e.target.closest && e.target.closest('.btn-download-uso');
            if(dlBtn){
                e.preventDefault();
                var id = dlBtn.getAttribute('data-id') || dlBtn.dataset.id;
                if(!id){ alert('ID de solicitud no encontrado.'); return; }
                var url = '<?php echo base_url($this->router->fetch_class() . '/get_uso_ajax/'); ?>' + id;
                fetch(url, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(json){
                    if(json && json.status && json.uso){
                        var uso = json.uso; var hasAny = false;
                        ['descripcion','fuente_ingreso','monto_estimado_mes','declaracion_nombre','declaracion_firma','declaracion_fecha','evaluador_credito','fecha_evaluacion'].forEach(function(f){ if(uso[f] !== null && uso[f] !== '' && uso[f] !== '0') { hasAny = true; } });
                        if(hasAny){ var dl = '<?php echo base_url($this->router->fetch_class() . '/download_uso/'); ?>' + id; window.open(dl, '_blank'); }
                        else { alert('Pendiente de llenado: complete al menos un campo del formato antes de descargar.'); }
                    } else { alert('Pendiente de llenado: complete al menos un campo del formato antes de descargar.'); }
                }).catch(function(){ alert('Error comprobando el formato. Revisa la red.'); });
                return;
            }

            if(e.target && (e.target.id === 'uso_save' || e.target.closest && e.target.closest('#uso_save'))){
                // Block save if readonly
                var aprob = (window.lastUsoAprobStatus !== undefined) ? window.lastUsoAprobStatus : null;
                if(aprob === 'approved' || aprob === 'rejected'){
                    e.preventDefault();
                    alert('No se puede modificar el formato porque la solicitud ya está aprobada o rechazada.');
                    return;
                }
                e.preventDefault();
                var payload = new URLSearchParams();
                payload.append('idsolicitud', selId);
                payload.append('descripcion', document.getElementById('uso_descripcion') ? document.getElementById('uso_descripcion').value || '' : '');
                payload.append('fuente_ingreso', document.getElementById('uso_fuente_ingreso') ? document.getElementById('uso_fuente_ingreso').value || '' : '');
                payload.append('monto_estimado_mes', document.getElementById('uso_monto_estimado') ? document.getElementById('uso_monto_estimado').value || '' : '');
                payload.append('monto_solicitado', document.getElementById('uso_monto_solicitado') ? document.getElementById('uso_monto_solicitado').value || '' : '');
                payload.append('plazo_solicitado', document.getElementById('uso_plazo_solicitado') ? document.getElementById('uso_plazo_solicitado').value || '' : '');
                var selDest = '';
                if(document.getElementById('uso_destino_simple')) selDest = document.getElementById('uso_destino_simple').value || '';
                payload.append('destino_prestamo', selDest);
                payload.append('destino_detalle', document.getElementById('uso_destino_detalle') ? document.getElementById('uso_destino_detalle').value || '' : '');
                payload.append('declaracion_nombre', document.getElementById('uso_declaracion_nombre') ? document.getElementById('uso_declaracion_nombre').value || '' : '');
                payload.append('declaracion_firma', document.getElementById('uso_declaracion_firma') ? document.getElementById('uso_declaracion_firma').value || '' : '');
                payload.append('declaracion_fecha', document.getElementById('uso_declaracion_fecha') ? document.getElementById('uso_declaracion_fecha').value || '' : '');
                payload.append('evaluador_credito', document.getElementById('uso_evaluador_credito') ? document.getElementById('uso_evaluador_credito').value || '' : '');
                payload.append('fecha_evaluacion', document.getElementById('uso_fecha_evaluacion') ? document.getElementById('uso_fecha_evaluacion').value || '' : '');
                var btnSave = document.getElementById('uso_save');
                btnSave.disabled = true; btnSave.textContent = 'Guardando...';
                fetch('<?php echo base_url($this->router->fetch_class() . '/save_uso_ajax'); ?>', { method: 'POST', body: payload, credentials: 'same-origin' })
                .then(function(r){ return r.json(); }).then(function(json){ if(json && json.status){ jQuery('#usoModal').modal('hide'); } else { document.getElementById('uso_error').style.display='block'; document.getElementById('uso_error').textContent = (json && json.message) ? json.message : 'Error al guardar.'; } })
                .catch(function(){ document.getElementById('uso_error').style.display='block'; document.getElementById('uso_error').textContent = 'Error al guardar.'; })
                .finally(function(){ btnSave.disabled = false; btnSave.textContent = 'Guardar'; });
            }
        }, false);

        try { var _params = new URLSearchParams(window.location.search); var _qid = _params.get('idsolicitud') || _params.get('id'); if (_qid) { selId = _qid; document.getElementById('uso_ids').textContent = selId; renderUso(null); if (typeof jQuery !== 'undefined' && jQuery && jQuery('#usoModal').modal) { jQuery('#usoModal').modal('show'); } loadUso(selId); } } catch (err) { console.warn('uso_credito: auto-open error', err); }

    })();
</script>
