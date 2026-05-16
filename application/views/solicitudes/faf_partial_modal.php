<!-- FAF modal partial used by FAF views -->
<div id="fafModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FAF - Análisis Financiero (Asalariado / Comerciante)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="fafForm">
                    <input type="hidden" name="idsolicitud" id="faf_idsolicitud" value="" />
                    <input type="hidden" name="tipo" id="faf_tipo" value="" />

                    <div class="form-group">
                        <h6>Datos de la solicitud</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Cliente</label>
                                <input type="text" class="form-control" id="faf_cliente" readonly />
                            </div>
                            <div class="col-md-3">
                                <label>Documento</label>
                                <input type="text" class="form-control" id="faf_documento" readonly />
                            </div>
                            <div class="col-md-3">
                                <label>Código</label>
                                <input type="text" class="form-control" id="faf_codigo" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h6>Datos solicitados</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Monto solicitado</label>
                                <input type="text" class="form-control" id="faf_monto_solicitado" readonly />
                            </div>
                            <div class="col-md-4">
                                <label>Plazo (meses)</label>
                                <input type="text" class="form-control" id="faf_plazo" readonly />
                            </div>
                            <div class="col-md-4">
                                <label>Tipo crédito</label>
                                <input type="text" class="form-control" id="faf_tipo_credito" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h6>Datos laborales</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Empresa / Centro de trabajo</label>
                                <input type="text" class="form-control" id="faf_empresa" />
                            </div>
                            <div class="col-md-3">
                                <label>Cargo / Puesto</label>
                                <input type="text" class="form-control" id="faf_cargo" />
                            </div>
                            <div class="col-md-3">
                                <label>Teléfono trabajo</label>
                                <input type="text" class="form-control" id="faf_tel_trabajo" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h6>Campos del Análisis Financiero (para PDF)</h6>
                        <div class="row">
                            <div class="col-md-4"><label>Efectivo o Caja</label><input type="number" step="0.01" class="form-control" id="faf_efectivo_caja" name="efectivo_caja" /></div>
                            <div class="col-md-4"><label>Dinero ahorrado o Banco</label><input type="number" step="0.01" class="form-control" id="faf_dinero_banco" name="dinero_banco" /></div>
                            <div class="col-md-4"><label>Total Disponible</label><input type="number" step="0.01" class="form-control" id="faf_total_disponible" name="total_disponible" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Cuentas por Cobrar</label><input type="number" step="0.01" class="form-control" id="faf_cuentas_cobrar" name="cuentas_cobrar" /></div>
                            <div class="col-md-4"><label>Inventario Mercadería</label><input type="number" step="0.01" class="form-control" id="faf_inventario_mercaderia" name="inventario_mercaderia" /></div>
                            <div class="col-md-4"><label>Productos en Proceso</label><input type="number" step="0.01" class="form-control" id="faf_productos_proceso" name="productos_proceso" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Productos Terminados</label><input type="number" step="0.01" class="form-control" id="faf_productos_terminados" name="productos_terminados" /></div>
                            <div class="col-md-4"><label>Total Inventarios</label><input type="number" step="0.01" class="form-control" id="faf_total_inventarios" name="total_inventarios" /></div>
                            <div class="col-md-4"><label>Bienes Muebles</label><input type="number" step="0.01" class="form-control" id="faf_bienes_muebles" name="bienes_muebles" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Propiedades</label><input type="number" step="0.01" class="form-control" id="faf_propiedades" name="propiedades" /></div>
                            <div class="col-md-4"><label>Otros Activos</label><input type="number" step="0.01" class="form-control" id="faf_otros_activos" name="otros_activos" /></div>
                            <div class="col-md-4"><label>Total Activos Fijos</label><input type="number" step="0.01" class="form-control" id="faf_total_activos_fijos" name="total_activos_fijos" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Total Activos</label><input type="number" step="0.01" class="form-control" id="faf_total_activos" name="total_activos" /></div>
                            <div class="col-md-4"><label>Cuentas por Pagar Proveedores</label><input type="number" step="0.01" class="form-control" id="faf_cuentas_pagar_proveedores" name="cuentas_pagar_proveedores" /></div>
                            <div class="col-md-4"><label>Cuentas por Pagar Crédito</label><input type="number" step="0.01" class="form-control" id="faf_cuentas_pagar_credito" name="cuentas_pagar_credito" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Pasivo No Corriente</label><input type="number" step="0.01" class="form-control" id="faf_pasivo_no_corriente" name="pasivo_no_corriente" /></div>
                            <div class="col-md-4"><label>Total Pasivo</label><input type="number" step="0.01" class="form-control" id="faf_total_pasivo" name="total_pasivo" /></div>
                            <div class="col-md-4"><label>Total Patrimonio</label><input type="number" step="0.01" class="form-control" id="faf_total_patrimonio" name="total_patrimonio" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Total Pasivo + Patrimonio</label><input type="number" step="0.01" class="form-control" id="faf_total_pasivo_patrimonio" name="total_pasivo_patrimonio" /></div>
                            <div class="col-md-4"><label>Ventas Contado</label><input type="number" step="0.01" class="form-control" id="faf_ventas_contado" name="ventas_contado" /></div>
                            <div class="col-md-4"><label>Ventas Crédito</label><input type="number" step="0.01" class="form-control" id="faf_ventas_credito" name="ventas_credito" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Ventas Totales</label><input type="number" step="0.01" class="form-control" id="faf_ventas_totales" name="ventas_totales" /></div>
                            <div class="col-md-4"><label>Costos Venta</label><input type="number" step="0.01" class="form-control" id="faf_costos_venta" name="costos_venta" /></div>
                            <div class="col-md-4"><label>Margen Bruto</label><input type="number" step="0.01" class="form-control" id="faf_margen_bruto" name="margen_bruto" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Gastos Generales</label><input type="number" step="0.01" class="form-control" id="faf_gastos_generales" name="gastos_generales" /></div>
                            <div class="col-md-4"><label>Utilidad Operativa</label><input type="number" step="0.01" class="form-control" id="faf_utilidad_operativa" name="utilidad_operativa" /></div>
                            <div class="col-md-4"><label>FCM Ventas Contado</label><input type="number" step="0.01" class="form-control" id="faf_fcm_ventas_contado" name="fcm_ventas_contado" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>FCM Recuperación Crédito</label><input type="number" step="0.01" class="form-control" id="faf_fcm_recuperacion_credito" name="fcm_recuperacion_credito" /></div>
                            <div class="col-md-4"><label>FCM Compras Contado</label><input type="number" step="0.01" class="form-control" id="faf_fcm_compras_contado" name="fcm_compras_contado" /></div>
                            <div class="col-md-4"><label>FCM Gastos Generales</label><input type="number" step="0.01" class="form-control" id="faf_fcm_gastos_generales" name="fcm_gastos_generales" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Flujo Negocio</label><input type="number" step="0.01" class="form-control" id="faf_flujo_negocio" name="flujo_negocio" /></div>
                            <div class="col-md-4"><label>FCM Otros Ingresos</label><input type="number" step="0.01" class="form-control" id="faf_fcm_otros_ingresos" name="fcm_otros_ingresos" /></div>
                            <div class="col-md-4"><label>FCM Gastos Consumo</label><input type="number" step="0.01" class="form-control" id="faf_fcm_gastos_consumo" name="fcm_gastos_consumo" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>FCM Otros Gastos</label><input type="number" step="0.01" class="form-control" id="faf_fcm_otros_gastos" name="fcm_otros_gastos" /></div>
                            <div class="col-md-4"><label>Flujo Neto Disponible</label><input type="number" step="0.01" class="form-control" id="faf_flujo_neto_disponible" name="flujo_neto_disponible" /></div>
                            <div class="col-md-4"><label>Gasto Local/Alquiler</label><input type="number" step="0.01" class="form-control" id="faf_gasto_local_alquiler" name="gasto_local_alquiler" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Gasto Energía</label><input type="number" step="0.01" class="form-control" id="faf_gasto_energia" name="gasto_energia" /></div>
                            <div class="col-md-4"><label>Gasto Agua</label><input type="number" step="0.01" class="form-control" id="faf_gasto_agua" name="gasto_agua" /></div>
                            <div class="col-md-4"><label>Gasto Internet</label><input type="number" step="0.01" class="form-control" id="faf_gasto_internet" name="gasto_internet" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Gasto Seguridad</label><input type="number" step="0.01" class="form-control" id="faf_gasto_seguridad" name="gasto_seguridad" /></div>
                            <div class="col-md-4"><label>Gasto Limpieza</label><input type="number" step="0.01" class="form-control" id="faf_gasto_limpieza" name="gasto_limpieza" /></div>
                            <div class="col-md-4"><label>Gasto Personal</label><input type="number" step="0.01" class="form-control" id="faf_gasto_personal" name="gasto_personal" /></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label>Total Gastos Fijos</label><input type="number" step="0.01" class="form-control" id="faf_total_gastos_fijos" name="total_gastos_fijos" /></div>
                            <div class="col-md-4"><label>Otras variables PDF...</label><input type="text" class="form-control" id="faf_otro_pdf" name="otro_pdf" placeholder="Agrega más campos según PDF..." /></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h6>Resultados / Cálculos</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Ingresos netos (ingreso + otros)</label>
                                <input type="number" step="0.01" class="form-control" id="faf_ingresos_netos" readonly />
                            </div>
                            <div class="col-md-4">
                                <label>Capacidad de pago (netos - gastos)</label>
                                <input type="number" step="0.01" class="form-control" id="faf_capacidad_pago" readonly />
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="btnCalcularFaf" type="button" class="btn btn-info">Calcular</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea id="faf_observaciones" class="form-control" rows="3"></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button id="btnSaveFaf" type="button" class="btn btn-primary">Guardar FAF</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openFafModal(id, tipo) {
        $('#fafModal').data('idsolicitud', id).data('tipo', tipo);
        $('#faf_idsolicitud').val(id);
        $('#faf_tipo').val(tipo);
        $('#fafModal').modal('show');

        // reset form while loading
        $('#fafForm')[0].reset();
        $('#faf_ingresos_netos').val('');
        $('#faf_capacidad_pago').val('');

        $.getJSON('<?php echo base_url('solicitudes/get_faf_ajax/'); ?>' + id + '/' + tipo, function(json){
            if (!json.status) return alert('Error al cargar datos FAF');

            var solicitud = json.solicitud || {};
            var saved = {};
            if (json.faf && json.faf.data) {
                try { saved = JSON.parse(json.faf.data); } catch(e){ saved = {}; }
            }

            // Prefill from saved FAF first, fallback to solicitud fields
            $('#faf_cliente').val( (saved.cliente || (solicitud.nombres? (solicitud.nombres + ' ' + (solicitud.apellidos||'')) : (solicitud.nombre_completo || ''))) );
            $('#faf_documento').val( saved.documento || solicitud.numero_documento || solicitud.numero_doc || '');
            var codigo = solicitud.idsolicitud ? ('SOL-' + String(solicitud.idsolicitud).padStart(4,'0')) : '';
            $('#faf_codigo').val(saved.codigo || codigo);

            $('#faf_monto_solicitado').val( saved.monto_solicitado || solicitud.monto_solicitado || solicitud.monto || '');
            $('#faf_plazo').val( saved.plazo || solicitud.plazo_meses || solicitud.plazo || '');
            $('#faf_tipo_credito').val( saved.tipo_credito || solicitud.tipo_credito || '');

            $('#faf_empresa').val( saved.empresa || solicitud.nombre_empresa || solicitud.centro_trabajo || '');
            $('#faf_cargo').val( saved.cargo || solicitud.cargo_puesto || '');
            $('#faf_tel_trabajo').val( saved.telefono_trabajo || solicitud.telefono_trabajo || '');

            $('#faf_ingreso_mensual').val( saved.ingreso_mensual || solicitud.ingreso_mensual_neto || solicitud.ingreso_neto || '');
            $('#faf_otros_ingresos').val( saved.otros_ingresos || '');
            $('#faf_gastos_personales').val( saved.gastos_personales || '');
            $('#faf_observaciones').val( saved.observaciones || '');

            if (saved.ingresos_netos) $('#faf_ingresos_netos').val(saved.ingresos_netos);
            if (saved.capacidad_pago) $('#faf_capacidad_pago').val(saved.capacidad_pago);

            // If the other FAF type exists and has data, disable saving (mutual exclusivity)
            if (json.other && json.other.data) {
                $('#btnSaveFaf').prop('disabled', true).text('Bloqueado: otro FAF completado');
            } else {
                $('#btnSaveFaf').prop('disabled', false).text('Guardar FAF');
            }
        });
    }

    // Simple calculation placeholder — update later with exact formulas
    $('#btnCalcularFaf').on('click', function(){
        var ing = parseFloat($('#faf_ingreso_mensual').val()) || 0;
        var otros = parseFloat($('#faf_otros_ingresos').val()) || 0;
        var gastos = parseFloat($('#faf_gastos_personales').val()) || 0;
        var netos = ing + otros;
        var capacidad = netos - gastos;
        $('#faf_ingresos_netos').val(netos.toFixed(2));
        $('#faf_capacidad_pago').val(capacidad.toFixed(2));

        // Calcular Flujo del negocio (1+2-3-4)
        var fcm_ventas_contado = parseFloat($('#faf_fcm_ventas_contado').val()) || 0;
        var fcm_recuperacion_credito = parseFloat($('#faf_fcm_recuperacion_credito').val()) || 0;
        var fcm_compras_contado = parseFloat($('#faf_fcm_compras_contado').val()) || 0;
        var fcm_gastos_generales = parseFloat($('#faf_fcm_gastos_generales').val()) || 0;
        var flujo_negocio = fcm_ventas_contado + fcm_recuperacion_credito - fcm_compras_contado - fcm_gastos_generales;
        $('#faf_flujo_negocio').val(flujo_negocio.toFixed(2));
    });

    $('#btnSaveFaf').on('click', function(){
        var id = $('#faf_idsolicitud').val();
        var tipo = $('#faf_tipo').val();

        // Recoger todos los campos del análisis financiero
        var payloadObj = {
            cliente: $('#faf_cliente').val(),
            documento: $('#faf_documento').val(),
            codigo: $('#faf_codigo').val(),
            monto_solicitado: $('#faf_monto_solicitado').val(),
            plazo: $('#faf_plazo').val(),
            tipo_credito: $('#faf_tipo_credito').val(),
            empresa: $('#faf_empresa').val(),
            cargo: $('#faf_cargo').val(),
            telefono_trabajo: $('#faf_tel_trabajo').val(),
            efectivo_caja: $('#faf_efectivo_caja').val(),
            dinero_banco: $('#faf_dinero_banco').val(),
            total_disponible: $('#faf_total_disponible').val(),
            cuentas_cobrar: $('#faf_cuentas_cobrar').val(),
            inventario_mercaderia: $('#faf_inventario_mercaderia').val(),
            productos_proceso: $('#faf_productos_proceso').val(),
            productos_terminados: $('#faf_productos_terminados').val(),
            total_inventarios: $('#faf_total_inventarios').val(),
            bienes_muebles: $('#faf_bienes_muebles').val(),
            propiedades: $('#faf_propiedades').val(),
            otros_activos: $('#faf_otros_activos').val(),
            total_activos_fijos: $('#faf_total_activos_fijos').val(),
            total_activos: $('#faf_total_activos').val(),
            cuentas_pagar_proveedores: $('#faf_cuentas_pagar_proveedores').val(),
            cuentas_pagar_credito: $('#faf_cuentas_pagar_credito').val(),
            pasivo_no_corriente: $('#faf_pasivo_no_corriente').val(),
            total_pasivo: $('#faf_total_pasivo').val(),
            total_patrimonio: $('#faf_total_patrimonio').val(),
            total_pasivo_patrimonio: $('#faf_total_pasivo_patrimonio').val(),
            ventas_contado: $('#faf_ventas_contado').val(),
            ventas_credito: $('#faf_ventas_credito').val(),
            ventas_totales: $('#faf_ventas_totales').val(),
            costos_venta: $('#faf_costos_venta').val(),
            margen_bruto: $('#faf_margen_bruto').val(),
            gastos_generales: $('#faf_gastos_generales').val(),
            utilidad_operativa: $('#faf_utilidad_operativa').val(),
            fcm_ventas_contado: $('#faf_fcm_ventas_contado').val(),
            fcm_recuperacion_credito: $('#faf_fcm_recuperacion_credito').val(),
            fcm_compras_contado: $('#faf_fcm_compras_contado').val(),
            fcm_gastos_generales: $('#faf_fcm_gastos_generales').val(),
            flujo_negocio: $('#faf_flujo_negocio').val(),
            fcm_otros_ingresos: $('#faf_fcm_otros_ingresos').val(),
            fcm_gastos_consumo: $('#faf_fcm_gastos_consumo').val(),
            fcm_otros_gastos: $('#faf_fcm_otros_gastos').val(),
            flujo_neto_disponible: $('#faf_flujo_neto_disponible').val(),
            gasto_local_alquiler: $('#faf_gasto_local_alquiler').val(),
            gasto_energia: $('#faf_gasto_energia').val(),
            gasto_agua: $('#faf_gasto_agua').val(),
            gasto_internet: $('#faf_gasto_internet').val(),
            gasto_seguridad: $('#faf_gasto_seguridad').val(),
            gasto_limpieza: $('#faf_gasto_limpieza').val(),
            gasto_personal: $('#faf_gasto_personal').val(),
            total_gastos_fijos: $('#faf_total_gastos_fijos').val(),
            // Puedes agregar más campos aquí según el PDF
            ingresos_netos: $('#faf_ingresos_netos').val(),
            capacidad_pago: $('#faf_capacidad_pago').val(),
            observaciones: $('#faf_observaciones').val()
        };

        var form = new FormData();
        form.append('idsolicitud', id);
        form.append('tipo', tipo);
        form.append('data', JSON.stringify(payloadObj));

        $.ajax({
            url: '<?php echo base_url('solicitudes/save_faf_ajax'); ?>',
            method: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function(resp){
                if (resp.status) {
                    alert('FAF guardado correctamente');
                    $('#fafModal').modal('hide');
                    // optionally mark row or refresh page
                } else {
                    alert('Error: ' + (resp.msg || 'No se pudo guardar'));
                }
            },
            error: function(){ alert('Error de red al guardar FAF'); }
        });
    });
</script>

<script>
(function(){
    function renderFields(data){
        var html = '<div class="form-group"><label>Datos FAF (JSON)</label><textarea class="form-control" id="faf_json" rows="8">'+(data && data.data ? data.data : '')+'</textarea></div>';
        return html;
    }

    document.addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.btn-open-faf');
        if(btn){
            e.preventDefault();
            var id = btn.getAttribute('data-id');
            var tipo = btn.getAttribute('data-tipo') || 'asalariado';
            document.getElementById('faf_ids').textContent = id;
            document.getElementById('faf_idhidden').value = id;
            document.getElementById('faf_tipo').value = tipo;
            document.getElementById('faf_fields').innerHTML = 'Cargando...';
            jQuery('#fafModal').modal('show');
            fetch('<?php echo base_url('solicitudes/get_faf_ajax/'); ?>'+id+'/'+tipo, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(json){
                if(json && json.status){
                    if(json.other && json.other.data && json.other.data.trim() !== ''){
                        document.getElementById('faf_fields').innerHTML = '<div class="alert alert-info">Este registro ya tiene el FAF completado en la vista "'+(tipo==='asalariado'? 'Comerciante' : 'Asalariado')+'". No se puede editar aquí.</div>' + renderFields(json.faf);
                        document.getElementById('faf_save').disabled = true;
                    } else {
                        document.getElementById('faf_fields').innerHTML = renderFields(json.faf);
                        document.getElementById('faf_save').disabled = false;
                    }
                } else {
                    document.getElementById('faf_fields').innerHTML = '<div class="text-danger">No se pudo cargar el FAF.</div>';
                }
            }).catch(function(){ document.getElementById('faf_fields').innerHTML = '<div class="text-danger">Error de red.</div>'; });
        }

        if(e.target && (e.target.id === 'faf_save' || e.target.closest && e.target.closest('#faf_save'))){
            e.preventDefault();
            var id = document.getElementById('faf_idhidden').value;
            var tipo = document.getElementById('faf_tipo').value;
            var payload = new URLSearchParams();
            payload.append('idsolicitud', id);
            payload.append('tipo', tipo);
            payload.append('data', document.getElementById('faf_json').value || '');
            var btn = document.getElementById('faf_save');
            btn.disabled = true; btn.textContent = 'Guardando...';
            fetch('<?php echo base_url('solicitudes/save_faf_ajax'); ?>', { method: 'POST', body: payload, credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(json){
                if(json && json.status){
                    jQuery('#fafModal').modal('hide');
                    $('.alert').remove();
                    $('.container-fluid').prepend('<div class="alert alert-success">FAF guardado.</div>');
                } else {
                    document.getElementById('faf_error').style.display = 'block';
                    document.getElementById('faf_error').textContent = (json && json.message) ? json.message : 'Error al guardar';
                }
            }).catch(function(){ document.getElementById('faf_error').style.display = 'block'; document.getElementById('faf_error').textContent = 'Error de red'; })
            .finally(function(){ btn.disabled = false; btn.textContent = 'Guardar FAF'; });
        }
    }, false);
})();
</script>
