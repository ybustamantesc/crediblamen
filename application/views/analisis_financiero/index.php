<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fa fa-file-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Análisis Financiero</h5>
                                <span>Definir tipo de análisis para solicitudes iniciales</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Estado</label>
                    <select id="filtro_estado" class="form-control">
                        <option value="all" <?php if($filtro_estado=='all') echo 'selected'; ?>>Todos</option>
                        <option value="pending" <?php if($filtro_estado=='pending') echo 'selected'; ?>>Pendiente</option>
                        <option value="completed" <?php if($filtro_estado=='completed') echo 'selected'; ?>>Completado</option>
                        <option value="annulled" <?php if($filtro_estado=='annulled') echo 'selected'; ?>>Anulado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Desde</label>
                    <input type="date" id="filtro_start_date" class="form-control" value="<?php echo $filtro_start_date; ?>">
                </div>
                <div class="col-md-3">
                    <label>Hasta</label>
                    <input type="date" id="filtro_end_date" class="form-control" value="<?php echo $filtro_end_date; ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="btnFiltrar">Filtrar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Solicitudes Iniciales</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table class="table table-sm table-striped table-bordered table-hover" id="tabla_solicitudes_pendientes">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Destino Conami</th>
                                            <th>Creado por</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($solicitudes)) : foreach ($solicitudes as $s) : ?>
                                            <?php $rowClass = ($s->aprob_status === 'annulled') ? 'table-secondary' : ''; ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td><?php echo $s->idsolicitud; ?></td>
                                                <td><?php echo trim($s->apellidos . ' ' . $s->nombres); ?></td>
                                                <td><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></td>
                                                <td><?php echo isset($s->fecha_solicitud) ? $s->fecha_solicitud : (isset($s->fecha_recepcion) ? $s->fecha_recepcion : ''); ?></td>
                                                <td><?php echo html_escape(isset($s->rubro_credito) ? $s->rubro_credito : ''); ?></td>
                                                <td><?php echo html_escape(!empty($s->nombre_asesor) ? $s->nombre_asesor : (isset($s->nombre_promotor) ? $s->nombre_promotor : '')); ?></td>
                                                <td>
                                                    <?php if($s->aprob_status=='annulled') echo '<span class="badge badge-secondary">Anulado</span>';
                                                    elseif($s->aprob_status=='completed') echo '<span class="badge badge-success">Completado</span>';
                                                    else echo '<span class="badge badge-warning">Pendiente</span>'; ?>
                                                </td>
                                                <td>
                                                    <?php if($s->aprob_status=='annulled'): ?>
                                                        <span class="text-muted">Crédito anulado</span>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-info btn-asalariado" data-id="<?php echo $s->idsolicitud; ?>"><i class="fa fa-user-tie"></i> Comerciante</button>
                                                        <button class="btn btn-sm btn-warning btn-comerciante" data-id="<?php echo $s->idsolicitud; ?>"><i class="fa fa-store"></i> Asalariado</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para definir análisis financiero (campos dinámicos después) -->
<div class="modal fade" id="modalAnalisisFinanciero" tabindex="-1" role="dialog" aria-labelledby="modalAnalisisLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAnalisisLabel">Definir Análisis Financiero</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAnalisisFinanciero">
                    <input type="hidden" name="idsolicitud" id="idsolicitud_modal">
                    <input type="hidden" name="tipo" id="tipo_analisis">
                    <!-- Aquí irán los campos dinámicos según tipo -->
                    <div id="campos_dinamicos"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarAnalisis">Guardar</button>
                <button type="button" class="btn btn-success d-none" id="btnDescargarPDFAsalariado">Descargar PDF Asalariado</button>
                <button type="button" class="btn btn-success d-none" id="btnDescargarPDFComerciante">Descargar PDF Comerciante</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('layout/footer'); ?>
<script>
// Mostrar botón de descarga PDF según tipo
function mostrarBotonDescargaPDF(tipo, idsolicitud) {
    if (tipo === 'asalariado') {
        $('#btnDescargarPDFAsalariado').removeClass('d-none').off('click').on('click', function() {
            window.open(base_url + 'analisis_financiero/descargar_pdf_asalariado/' + idsolicitud, '_blank');
        });
        $('#btnDescargarPDFComerciante').addClass('d-none');
    } else if (tipo === 'comerciante') {
        $('#btnDescargarPDFComerciante').removeClass('d-none').off('click').on('click', function() {
            window.open(base_url + 'analisis_financiero/descargar_pdf_comerciante/' + idsolicitud, '_blank');
        });
        $('#btnDescargarPDFAsalariado').addClass('d-none');
    } else {
        $('#btnDescargarPDFAsalariado').addClass('d-none');
        $('#btnDescargarPDFComerciante').addClass('d-none');
    }
}

// Calcular Total Gastos Fijos Mensuales automáticamente
function calcularTotalGastosFijos() {
    let total = 0;
    $('.suma-gastos-fijos').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total_gastos_fijos').val(total.toFixed(2));
}
// Vincular el cálculo a los eventos de input
$(document).on('input', '.suma-gastos-fijos', calcularTotalGastosFijos);
// Calcular al cargar por si hay valores precargados
$(document).ready(function() {
    calcularTotalGastosFijos();
});

// Abrir modal y setear tipo según botón
$(document).on('click', '.btn-asalariado, .btn-comerciante', function() {
    var id = $(this).data('id');
    var tipo = $(this).hasClass('btn-asalariado') ? 'asalariado' : 'comerciante';
    $('#idsolicitud_modal').val(id);
    $('#tipo_analisis').val(tipo);
    if (tipo === 'asalariado') {
        renderCamposAsalariado();
        // Cargar datos guardados si existen
        $.ajax({
            url: base_url + 'analisis_financiero/get_asalariado/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.status && resp.data) {
                    function setArrayField(fieldName, rawValue) {
                        var arr = rawValue;
                        if (typeof rawValue === 'string') {
                            try {
                                arr = JSON.parse(rawValue);
                            } catch (e) {
                                arr = [rawValue];
                            }
                        }
                        if (!Array.isArray(arr)) {
                            arr = [arr];
                        }
                        var $arrEls = $('[name="' + fieldName + '[]"]');
                        if ($arrEls.length) {
                            $arrEls.each(function(idx) {
                                $(this).val((arr[idx] !== undefined && arr[idx] !== null) ? arr[idx] : '');
                            });
                            return true;
                        }
                        return false;
                    }

                    for (var k in resp.data) {
                        if (resp.data.hasOwnProperty(k)) {
                            if (setArrayField(k, resp.data[k])) {
                                continue;
                            }
                            var $el = $('[name="'+k+'"]');
                            if ($el.length) {
                                // Si es indicador, formatear como porcentaje
                                if (k === 'indicador_endeudamiento' || k === 'cobertura_deuda' || k === 'porcentaje_margen' || k === 'porcentaje_deuda_total') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val((val * 100).toFixed(1) + ' %');
                                    } else {
                                        $el.val('0.0 %');
                                    }
                                } else if (k === 'cobertura_garantia') {
                                    // Cobertura de garantía ya viene en porcentaje, solo agregar %
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val(val.toFixed(2) + ' %');
                                    } else {
                                        $el.val('0.00 %');
                                    }
                                } else {
                                    $el.val(resp.data[k]);
                                }
                            }
                        }
                    }
                }
            }
        });
        $('#modalAnalisisLabel').text('Definir Análisis Financiero');
    } else {
        renderCamposComerciante();
        $('#modalAnalisisLabel').text('Analisis Financiero Asalariado');
        // Cargar datos guardados/sugeridos de comerciante
        $.ajax({
            url: base_url + 'analisis_financiero/get_comerciante/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.status && resp.data) {
                    for (var k in resp.data) {
                        if (resp.data.hasOwnProperty(k)) {
                            var $el = $('[name="'+k+'"]');
                            if ($el.length) {
                                // Si es cobertura de garantía, formatear con %
                                if (k === 'cobertura_garantia') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val(val.toFixed(2) + ' %');
                                    } else {
                                        $el.val('0.00 %');
                                    }
                                } else if (k === 'porcentaje_deuda_total') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val(val.toFixed(2) + ' %');
                                    } else {
                                        $el.val('0.00 %');
                                    }
                                } else {
                                    $el.val(resp.data[k]);
                                }
                            }
                        }
                    }
                    // Recalcular totales automáticos luego de cargar datos
                    $('.suma-ingresos, .suma-gastos-familiares, .suma-otras-obligaciones, .canasta-campo, #personas_dependientes, .transporte-campo').trigger('input');
                    calcularSumas();
                }
            }
        });
    }
    mostrarBotonDescargaPDF(tipo, id);
    $('#modalAnalisisFinanciero').modal('show');
});

// Guardar análisis financiero comerciante
$('#btnGuardarAnalisis').on('click', function() {
    var tipo = $('#tipo_analisis').val();
    var form = $('#formAnalisisFinanciero');
    var toNumber = function(v) {
        if (v === null || v === undefined) return 0;
        v = String(v).replace(/C\$/g, '').replace(/%/g, '').replace(/\s/g, '').replace(/,/g, '');
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    };
    // Limpiar campos de porcentaje antes de serializar
    ['indicador_endeudamiento','porcentaje_margen','porcentaje_deuda_total'].forEach(function(campo) {
        var $el = $('[name="'+campo+'"]');
        if ($el.length) {
            var val = $el.val();
            if (typeof val === 'string' && val.indexOf('%') !== -1) {
                val = val.replace('%','').replace(',','.').replace(/\s/g,'');
                var num = parseFloat(val);
                if (!isNaN(num)) {
                    $el.val((num/100).toFixed(6));
                } else {
                    $el.val('0');
                }
            }
        }
    });
    // Forzar cobertura_deuda a número decimal antes de enviar
    var coberturaVal = $('[name="cobertura_deuda"]').val();
    if (typeof coberturaVal === 'string' && coberturaVal.indexOf('%') !== -1) {
        coberturaVal = coberturaVal.replace('%','').replace(',','.').replace(/\s/g,'');
    }
    var coberturaNum = parseFloat(coberturaVal);
    if (isNaN(coberturaNum)) {
        coberturaNum = 0;
    }
    // Convertir a decimal (ej: 12.5% -> 0.125)
    coberturaNum = coberturaNum / 100;
    $('[name="cobertura_deuda"]').val(coberturaNum.toFixed(6));
    var datos = form.serialize();
    var url = '';
    if (tipo === 'comerciante') {
        url = base_url + 'analisis_financiero/guardar_comerciante';
    } else if (tipo === 'asalariado') {
        url = base_url + 'analisis_financiero/guardar_asalariado';
    } else {
        alert('Tipo de análisis no soportado');
        return;
    }

    if (tipo === 'comerciante') {
        var cuota = toNumber($('#cuota_periodica').val());
        var flujo = toNumber($('#flujo_neto_disponible').val());
        if (cuota > 0 && flujo > 0 && cuota > flujo) {
            alert('La Cuota Periódica no puede ser mayor que el Flujo Neto Disponible.');
            $('#cuota_periodica').focus();
            return;
        }
    }

    if (tipo === 'asalariado') {
        var cuotaAsal = toNumber($('#cuota_periodica').val());
        var flujoAsal = toNumber($('#flujo_neto_mensual').val());
        if (cuotaAsal > 0 && flujoAsal > 0 && cuotaAsal > flujoAsal) {
            alert('La Cuota Periódica no puede ser mayor que el Flujo Neto Mensual Disponible.');
            $('#cuota_periodica').focus();
            return;
        }
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: datos,
        dataType: 'json',
        success: function(resp) {
            if (resp.status) {
                alert('Análisis financiero guardado correctamente');
                $('#modalAnalisisFinanciero').modal('hide');
                // Descargar PDF automáticamente si es asalariado
                if (tipo === 'asalariado') {
                    var idsol = $('#idsolicitud_modal').val();
                    setTimeout(function() {
                        window.open(base_url + 'analisis_financiero/descargar_pdf_asalariado/' + idsol, '_blank');
                    }, 500);
                }
            } else {
                alert('Error al guardar: ' + (resp.msg || ''));
            }
        },
        error: function() {
            alert('Error de comunicación con el servidor');
        }
    });
});

function renderCamposComerciante() {
    let html = `
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>A. Sueldo Neto (restadas las deducciones INSS e IR)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_sueldo_neto" id="ingreso_sueldo_neto" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>B. Comisiones</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_comisiones" id="ingreso_comisiones" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>C. Bonificaciones</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_bonificaciones" id="ingreso_bonificaciones" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>D. Remesas</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_remesas" id="ingreso_remesas" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>E. Otros ingresos</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_otros" id="ingreso_otros" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(1) TOTAL INGRESOS (A+B+C+D+E)</b></label>
                <input type="number" class="form-control" id="total_ingresos" name="total_ingresos" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>F. Gastos en alimentación</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_alimentacion" id="gastos_alimentacion" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>G. Servicios básicos (agua, luz, Internet Fijo)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_servicios" id="gastos_servicios" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>H. Vestuario</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_vestuario" id="gastos_vestuario" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>I. Gastos educativos</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_educativos" id="gastos_educativos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>J. Gastos en transporte/ reparaciones, combustible</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_transporte" id="gastos_transporte" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>K. Gastos en alquiler o arriendo vivienda</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_alquiler" id="gastos_alquiler" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>L. Pago de empleado + viatico</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="pago_empleado_viatico" id="pago_empleado_viatico" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>P. Entretenimiento (incluye gastos derivados del uso celulares e internet)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="entretenimiento" id="entretenimiento" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Q. Otros Gastos (Especifique) pago de trabajador+viatico de transporte</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="otros_gastos" id="otros_gastos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(2) GASTOS FAMILIARES TOTAL (F+G+H+I+J+K+L+P+Q)</b></label>
                <input type="number" class="form-control" id="total_gastos_familiares" name="total_gastos_familiares" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>M. Abono o cuotas de prestamos o deudas con instituciones financieras, casas comerciales o particulares</b></label>
                <input type="number" min="0" step="any" class="form-control suma-otras-obligaciones" name="cuotas_prestamos" id="cuotas_prestamos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>N. Pensión alimenticia o similares</b></label>
                <input type="number" min="0" step="any" class="form-control suma-otras-obligaciones" name="pension_alimenticia" id="pension_alimenticia" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>O. Otros</b></label>
                <input type="number" min="0" step="any" class="form-control suma-otras-obligaciones" name="otras_obligaciones" id="otras_obligaciones" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(3) OTRAS OBLIGACIONES (M+N+O)</b></label>
                <input type="number" class="form-control" id="total_otras_obligaciones" name="total_otras_obligaciones" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(4) TOTAL EGRESOS (2+3)</b></label>
                <input type="number" class="form-control" id="total_egresos" name="total_egresos" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(5) FLUJO NETO MENSUAL DISPONIBLE (1-4)</b></label>
                <input type="number" class="form-control" id="flujo_neto_mensual" name="flujo_neto_mensual" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Valor de la cuota periódica de pago estimada C$</b></label>
                <input type="number" min="0" step="any" class="form-control" name="cuota_periodica" id="cuota_periodica" value="0">
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Canasta básica C$</label>
                <input type="number" min="0" step="any" class="form-control canasta-campo" name="canasta_basica" id="canasta_basica" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad promedio</label>
                <input type="number" min="0" step="1" class="form-control canasta-campo" name="cantidad_promedio" id="cantidad_promedio" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Monto por persona</label>
                <input type="number" min="0" step="any" class="form-control" name="monto_por_persona" id="monto_por_persona" value="0" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad de personas dependientes</label>
                <input type="number" min="0" step="1" class="form-control" name="personas_dependientes" id="personas_dependientes" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Gastos de alimentación</label>
                <input type="number" min="0" step="any" class="form-control" name="gastos_alimentacion_canasta" id="gastos_alimentacion_canasta" value="0" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Transporte urbano colectivo</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_urbano" id="transporte_urbano" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio individual (taxi, caponera)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_individual" id="transporte_individual" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte interurbano</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_interurbano" id="transporte_interurbano" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Recorrido laboral</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="recorrido_laboral" id="recorrido_laboral" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Vehículo particular de uso personal</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="vehiculo_particular" id="vehiculo_particular" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total transporte</b></label>
                <input type="number" class="form-control" name="total_transporte" id="total_transporte" value="0" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Alquiler</label>
                <input type="number" min="0" step="any" class="form-control vivienda-campo" name="alquiler" id="alquiler" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Casa propia</label>
                <input type="number" min="0" step="any" class="form-control vivienda-campo" name="casa_propia" id="casa_propia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total Gastos Vivienda</b></label>
                <input type="number" class="form-control" name="total_gastos_vivienda" id="total_gastos_vivienda" value="0" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>Cobertura de la deuda con capacidad de pago = (Flujo neto disponible / cuota), Máxima porción a comprometer del flujo = 25%</b></label>
                <input type="text" class="form-control" id="cobertura_deuda" name="cobertura_deuda" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Cobertura de garantía (150%)</b></label>
                <input type="text" class="form-control" id="cobertura_garantia" name="cobertura_garantia" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>T/C Acumulado de liquidación</b></label>
                <input type="number" min="0" step="any" class="form-control" name="tc_acumulado" id="tc_acumulado" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>P. Entretenimiento (incluye gastos derivados del uso celulares e internet)</b></label>
                <input type="number" min="0" step="any" class="form-control" name="p_entretenimiento" id="p_entretenimiento" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Total de Deuda a Creditar</b></label>
                <input type="number" min="0" step="any" class="form-control" name="total_deuda_acreditar" id="total_deuda_acreditar" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Nivel de endeudamiento</b></label>
                <input type="text" class="form-control" name="porcentaje_deuda_total" id="porcentaje_deuda_total" readonly>
            </div>
        </div>
    </div>
    `;
    $('#campos_dinamicos').html(html);
    // Calcular total ingresos automáticamente
    function calcularTotalIngresos() {
        let total = 0;
        total += parseFloat($('#ingreso_sueldo_neto').val()) || 0;
        total += parseFloat($('#ingreso_comisiones').val()) || 0;
        total += parseFloat($('#ingreso_bonificaciones').val()) || 0;
        total += parseFloat($('#ingreso_remesas').val()) || 0;
        total += parseFloat($('#ingreso_otros').val()) || 0;
        $('#total_ingresos').val(total.toFixed(2));
        calcularPorcentajeDeudaTotal();
    }
    $('.suma-ingresos').on('input', calcularTotalIngresos);
    calcularTotalIngresos();
    // Calcular total gastos familiares automáticamente
    function calcularTotalGastosFamiliares() {
        let total = 0;
        total += parseFloat($('#gastos_alimentacion').val()) || 0;
        total += parseFloat($('#gastos_servicios').val()) || 0;
        total += parseFloat($('#gastos_vestuario').val()) || 0;
        total += parseFloat($('#gastos_educativos').val()) || 0;
        total += parseFloat($('#gastos_transporte').val()) || 0;
        total += parseFloat($('#gastos_alquiler').val()) || 0;
        total += parseFloat($('#pago_empleado_viatico').val()) || 0;
        total += parseFloat($('#entretenimiento').val()) || 0;
        total += parseFloat($('#otros_gastos').val()) || 0;
        $('#total_gastos_familiares').val(total.toFixed(2));
        calcularTotalEgresos();
    }
    $('.suma-gastos-familiares').on('input', calcularTotalGastosFamiliares);
    calcularTotalGastosFamiliares();
    // Calcular total otras obligaciones automáticamente
    function calcularTotalOtrasObligaciones() {
        let total = 0;
        total += parseFloat($('#cuotas_prestamos').val()) || 0;
        total += parseFloat($('#pension_alimenticia').val()) || 0;
        total += parseFloat($('#otras_obligaciones').val()) || 0;
        $('#total_otras_obligaciones').val(total.toFixed(2));
        calcularPorcentajeDeudaTotal();
        calcularTotalEgresos();
    }
    $('.suma-otras-obligaciones').on('input', calcularTotalOtrasObligaciones);
    calcularTotalOtrasObligaciones();
    // Calcular total egresos automáticamente
    function calcularTotalEgresos() {
        let total_gastos_familiares = parseFloat($('#total_gastos_familiares').val()) || 0;
        let total_otras_obligaciones = parseFloat($('#total_otras_obligaciones').val()) || 0;
        let total = total_gastos_familiares + total_otras_obligaciones;
        $('#total_egresos').val(total.toFixed(2));
        calcularFlujoNetoMensual();
    }
    calcularTotalEgresos();
    // Calcular flujo neto mensual disponible automáticamente
    function calcularFlujoNetoMensual() {
        let total_ingresos = parseFloat($('#total_ingresos').val()) || 0;
        let total_egresos = parseFloat($('#total_egresos').val()) || 0;
        let total = total_ingresos - total_egresos;
        $('#flujo_neto_mensual').val(total.toFixed(2));
        calcularCoberturaDeuda();
    }
    $('.suma-ingresos').on('input', calcularFlujoNetoMensual);
    calcularFlujoNetoMensual();
    // Calcular monto por persona automáticamente
    function calcularMontoPorPersona() {
        let canasta = parseFloat($('#canasta_basica').val()) || 0;
        let cantidad = parseFloat($('#cantidad_promedio').val()) || 0;
        let monto = (cantidad > 0) ? (canasta / cantidad) : 0;
        $('#monto_por_persona').val(monto.toFixed(2));
        calcularGastosAlimentacionCanasta();
    }
    $('.canasta-campo').on('input', calcularMontoPorPersona);
    calcularMontoPorPersona();

    // Gastos de alimentación = Monto por persona * Cantidad de personas dependientes
    function calcularGastosAlimentacionCanasta() {
        let montoPersona = parseFloat($('#monto_por_persona').val()) || 0;
        let dependientes = parseFloat($('#personas_dependientes').val()) || 0;
        let total = montoPersona * dependientes;
        $('#gastos_alimentacion_canasta').val(total.toFixed(2));
    }
    $('#personas_dependientes').on('input', calcularGastosAlimentacionCanasta);
    calcularGastosAlimentacionCanasta();

    // Total transporte = suma de 5 campos de transporte
    function calcularTotalTransporte() {
        let total = 0;
        total += parseFloat($('#transporte_urbano').val()) || 0;
        total += parseFloat($('#transporte_individual').val()) || 0;
        total += parseFloat($('#transporte_interurbano').val()) || 0;
        total += parseFloat($('#recorrido_laboral').val()) || 0;
        total += parseFloat($('#vehiculo_particular').val()) || 0;
        $('#total_transporte').val(total.toFixed(2));
    }
    $('.transporte-campo').on('input', calcularTotalTransporte);
    calcularTotalTransporte();

    // Total gastos vivienda = Alquiler + Casa propia
    function calcularTotalGastosVivienda() {
        let total = 0;
        total += parseFloat($('#alquiler').val()) || 0;
        total += parseFloat($('#casa_propia').val()) || 0;
        $('#total_gastos_vivienda').val(total.toFixed(2));
    }
    $('.vivienda-campo').on('input', calcularTotalGastosVivienda);
    calcularTotalGastosVivienda();

    // Cobertura de deuda = (Flujo Neto Mensual / Cuota Periódica) * 100 en porcentaje
    function calcularCoberturaDeuda() {
        let flujo_neto = parseFloat($('#flujo_neto_mensual').val()) || 0;
        let cuota = parseFloat($('#cuota_periodica').val()) || 0;
        let cobertura = 0;
        if (cuota > 0) {
            cobertura = (flujo_neto / cuota) * 100;
        }
        $('#cobertura_deuda').val(cobertura.toFixed(1) + ' %');
    }
    $('#cuota_periodica').on('input', calcularCoberturaDeuda);
    calcularCoberturaDeuda();

    // Nivel de endeudamiento = ((3) Otras Obligaciones + Total de Deuda a Creditar) / (1) Total Ingresos
    function calcularPorcentajeDeudaTotal() {
        let otrasObligaciones = parseFloat($('#total_otras_obligaciones').val()) || 0;
        let deudaAcreditar = parseFloat($('#total_deuda_acreditar').val()) || 0;
        let totalIngresos = parseFloat($('#total_ingresos').val()) || 0;
        let porcentaje = 0;
        if (totalIngresos > 0) {
            porcentaje = ((otrasObligaciones + deudaAcreditar) / totalIngresos) * 100;
        }
        $('#porcentaje_deuda_total').val(porcentaje.toFixed(2) + ' %');
    }
    $('#total_deuda_acreditar').on('input', calcularPorcentajeDeudaTotal);
    calcularPorcentajeDeudaTotal();

    // Calcular sueldo neto automáticamente
    function calcularSueldoNeto() {
        let sueldo = parseFloat($('#sueldo').val()) || 0;
        let inss = parseFloat($('#inss').val()) || 0;
        let ir = parseFloat($('#ir').val()) || 0;
        let neto = sueldo - inss - ir;
        $('#sueldo_neto_calc').val(neto.toFixed(2));
    }
    $('.sueldo-campo').on('input', calcularSueldoNeto);
    calcularSueldoNeto();
}

function renderCamposAsalariado() {
        // Subtotal de saldos obligaciones largo plazo (asalariado)
        function calcularSubtotalAsalOlp() {
            let total = 0;
            $('.asal-olp-saldo').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#asal_subtotal_olp_saldo').val(total);
        }
        $('.asal-olp-saldo').on('input', calcularSubtotalAsalOlp);
        calcularSubtotalAsalOlp();
    let html = `
    <div class="container-fluid">
        <!-- CANASTA BÁSICA Y DEPENDIENTES oculto temporalmente
        <h5 class='mt-3 mb-2'>CANASTA BÁSICA Y DEPENDIENTES</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Canasta básica C$</label>
                <input type="number" min="0" step="any" class="form-control" name="canasta_basica" id="canasta_basica" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad promedio</label>
                <input type="number" min="0" step="1" class="form-control" name="cantidad_promedio" id="cantidad_promedio" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Monto por persona</label>
                <input type="number" min="0" step="any" class="form-control" name="monto_por_persona" id="monto_por_persona" value="0" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad de personas dependientes</label>
                <input type="number" min="0" step="1" class="form-control" name="personas_dependientes" id="personas_dependientes" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Gastos de alimentación</label>
                <input type="number" min="0" step="any" class="form-control" name="gastos_alimentacion_canasta" id="gastos_alimentacion_canasta" value="0">
            </div>
        </div>
        -->
        <hr/>
        <h5 class='mt-3 mb-2'>DISPONIBLE (A+B)</h5>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label>A. Efectivo o Caja</label>
                <input type="number" min="0" step="any" class="form-control suma-disponible" name="efectivo_caja" id="efectivo_caja" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label>B. Dinero ahorrado o Banco</label>
                <input type="number" min="0" step="any" class="form-control suma-disponible" name="dinero_banco" id="dinero_banco" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Disponible (A+B)</b></label>
                <input type="number" class="form-control" id="total_disponible" name="total_disponible" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label>(3) CUENTAS POR COBRAR</label>
                <input type="number" min="0" step="any" class="form-control" name="cuentas_cobrar" id="cuentas_cobrar" value="0">
            </div>
        </div>
        <h5 class='mt-3 mb-2'>(4) INVENTARIOS</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>A. Inventario de mercadería</label>
                <input type="number" min="0" step="any" class="form-control suma-inventarios" name="inventario_mercaderia" id="inventario_mercaderia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Productos en proceso</label>
                <input type="number" min="0" step="any" class="form-control suma-inventarios" name="productos_proceso" id="productos_proceso" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>C. Productos terminados</label>
                <input type="number" min="0" step="any" class="form-control suma-inventarios" name="productos_terminados" id="productos_terminados" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Inventarios (A+B+C)</b></label>
                <input type="number" class="form-control" id="total_inventarios" name="total_inventarios" readonly>
            </div>
        </div>
        <h5 class='mt-3 mb-2'>(5) TOTAL ACTIVOS FIJOS</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>A. Bienes muebles (equipo, maquinaria, etc.)</label>
                <input type="number" min="0" step="any" class="form-control suma-activos-fijos" name="bienes_muebles" id="bienes_muebles" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Propiedades (casa, finca, etc.)</label>
                <input type="number" min="0" step="any" class="form-control suma-activos-fijos" name="propiedades" id="propiedades" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>C. Otros Activos</label>
                <input type="number" min="0" step="any" class="form-control suma-activos-fijos" name="otros_activos" id="otros_activos" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total Activos Fijos (A+B+C)</b></label>
                <input type="number" class="form-control" id="total_activos_fijos" name="total_activos_fijos" readonly>
            </div>
        </div>
        <h5 class='mt-3 mb-2'>(6) TOTAL ACTIVOS</h5>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>Total Activos (Disponible + Inventarios + Activos Fijos + Cuentas por Cobrar)</b></label>
                <input type="number" class="form-control" id="total_activos" name="total_activos" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-3 mb-2'>PASIVOS CIRCULANTE O CORRIENTE</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>(1) Cuentas por pagar a proveedores</label>
                <input type="number" min="0" step="any" class="form-control suma-cuentas-pagar" name="cuentas_pagar_proveedores" id="cuentas_pagar_proveedores" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Cuentas por pagar crédito corto plazo</label>
                <input type="number" min="0" step="any" class="form-control suma-cuentas-pagar" name="cuentas_pagar_credito" id="cuentas_pagar_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(2) Pasivo no corriente (mayor a 1 año)</label>
                <input type="number" min="0" step="any" class="form-control suma-cuentas-pagar" name="pasivo_no_corriente" id="pasivo_no_corriente" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>(3) Total Pasivo (1+2)</b></label>
                <input type="number" class="form-control" id="total_pasivo" name="total_pasivo" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-3 mb-2'>(4) TOTAL PATRIMONIO</h5>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>Total Patrimonio (Total Activos - Total Pasivo)</b></label>
                <input type="number" class="form-control" id="total_patrimonio" name="total_patrimonio" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Total Pasivo + Patrimonio (3+4)</b></label>
                <input type="number" class="form-control" id="total_pasivo_patrimonio" name="total_pasivo_patrimonio" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>ESTADO DE RESULTADO MENSUAL</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>A. Ventas al contado</label>
                <input type="number" min="0" step="any" class="form-control suma-ventas" name="ventas_contado" id="ventas_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Ventas al crédito</label>
                <input type="number" min="0" step="any" class="form-control suma-ventas" name="ventas_credito" id="ventas_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(1) VENTAS TOTALES (A+B)</label>
                <input type="number" class="form-control" id="ventas_totales" name="ventas_totales" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>(2) COSTOS DE VENTA</label>
                <input type="number" min="0" step="any" class="form-control" name="costos_venta" id="costos_venta" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(3) MARGEN BRUTO (1-2)</label>
                <input type="number" class="form-control" id="margen_bruto" name="margen_bruto" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>(4) GASTOS GENERALES</label>
                <input type="number" min="0" step="any" class="form-control" name="gastos_generales" id="gastos_generales" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(5) UTILIDAD OPERATIVA (3-4)</label>
                <input type="number" class="form-control" id="utilidad_operativa" name="utilidad_operativa" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>FLUJO DE CAJA MENSUAL</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>1. Ventas al contado</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_ventas_contado" id="fcm_ventas_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>2. Recuperación ventas al crédito</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_recuperacion_credito" id="fcm_recuperacion_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>3. Compras al contado</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_compras_contado" id="fcm_compras_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>4. Gastos Generales</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_gastos_generales" id="fcm_gastos_generales" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Flujo del negocio (1+2-3-4)</b></label>
                <input type="number" class="form-control" id="flujo_negocio" name="flujo_negocio" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>5. Otros ingresos de la unidad familiar</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_otros_ingresos" id="fcm_otros_ingresos" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>6. Gastos consumo familiar (costo mínimo de vida en función canasta básica y cantidad de personas que dependen del titular)</label>
                <input type="number" min="0" step="any" class="form-control" name="fcm_gastos_consumo" id="fcm_gastos_consumo" value="0" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Valor Canasta básica</label>
                <input type="number" min="0" step="any" class="form-control" name="fcm_valor_canasta_basica" id="fcm_valor_canasta_basica" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad de personas dep</label>
                <input type="number" min="0" step="1" class="form-control" name="fcm_cant_personas_dep" id="fcm_cant_personas_dep" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>7. Otros gastos (pagos de cuotas y otras transacciones financieras)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_otros_gastos" id="fcm_otros_gastos" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>FLUJO NETO DISPONIBLE (1+2-3-4+5-6-7)</b></label>
                <input type="number" class="form-control" id="flujo_neto_disponible" name="flujo_neto_disponible" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Cuota Periódica (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control" name="cuota_periodica" id="cuota_periodica" value="0">
                <small class="text-muted">Sugerida desde Solicitud Inicial: cuota mensual en US$ x 36.6243.</small>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>GASTOS FIJOS MENSUALES</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Local o casa propia/Alquiler</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_local_alquiler" id="gasto_local_alquiler" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio de energía eléctrica</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_energia" id="gasto_energia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio de agua potable</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_agua" id="gasto_agua" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Internet residencial/Plan postpago/TV por cable/Teléfono</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_internet" id="gasto_internet" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Seguridad</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_seguridad" id="gasto_seguridad" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Limpieza y mantenimiento</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_limpieza" id="gasto_limpieza" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Gastos personales básicos</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_personal" id="gasto_personal" value="0">
            </div>

            <div class="col-md-4 mb-2">
                <label>Salario de Ayudante Empleado</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_salario_ayudante" id="gasto_salario_ayudante" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_transporte" id="gasto_transporte" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total Gastos Fijos Mensuales</b></label>
                <input type="number" class="form-control" id="total_gastos_fijos" name="total_gastos_fijos" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 1</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="olp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="olp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control olp-saldo" name="olp_saldo[]">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-2"></div>
            <div class="col-md-3 mb-2"></div>
            <div class="col-md-3 mb-2 text-right"><label><b>Subtotal Saldo Obligaciones</b></label></div>
            <div class="col-md-3 mb-2">
                <input type="number" class="form-control" id="subtotal_olp_saldo" name="subtotal_olp_saldo" readonly>
            </div>
        </div>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A CORTO PLAZO 1</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="ocp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control ocp-saldo" name="ocp_saldo[]">
            </div>
        </div>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A CORTO PLAZO 2</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="ocp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control ocp-saldo" name="ocp_saldo[]">
            </div>
        </div>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A CORTO PLAZO 3</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="ocp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control ocp-saldo" name="ocp_saldo[]">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-2"></div>
            <div class="col-md-3 mb-2"></div>
            <div class="col-md-3 mb-2 text-right"><label><b>Subtotal Saldo Obligaciones Corto Plazo</b></label></div>
            <div class="col-md-3 mb-2">
                <input type="number" class="form-control" id="subtotal_ocp_saldo" name="subtotal_ocp_saldo" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>COSTOS DE OPERACIÓN DIRECTOS</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Salario ayudante/empleado</label>
                <input type="number" min="0" step="any" class="form-control" name="costo_salario_ayudante" id="costo_salario_ayudante">
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte</label>
                <input type="number" min="0" step="any" class="form-control" name="costo_transporte" id="costo_transporte">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total</b></label>
                <input type="number" min="0" step="any" class="form-control" name="costo_total_operacion" id="costo_total_operacion">
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 2</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="olp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="olp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control" name="olp_saldo[]">
            </div>
        </div>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 3</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="olp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota</label>
                <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="olp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo</label>
                <input type="number" min="0" step="any" class="form-control" name="olp_saldo[]">
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>INDICADORES</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Nivel de Endeudamiento = (Total Pasivo + Monto Crédito Solicitado) / Total Activos</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="indicador_endeudamiento" name="indicador_endeudamiento" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label>Capital de trabajo Neto (Activo Corriente – Pasivo Corriente)</label>
                <input type="number" class="form-control" id="capital_trabajo_neto" name="capital_trabajo_neto" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Cobertura de la deuda capacidad de pago = (Cuota / Flujo neto disponible) Máxima porción a comprometer del flujo = 25%</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="cobertura_deuda" name="cobertura_deuda" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Cobertura de garantía (150%)</b></label>
                <input type="text" class="form-control" id="cobertura_garantia" name="cobertura_garantia" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>% Margen</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="porcentaje_margen" name="porcentaje_margen" placeholder="10 %">
                </div>
            </div>
        </div>
    </div>
    `;
    $('#campos_dinamicos').html(html);
    calcularSumas();
    $('.suma-disponible').on('input', calcularSumas);
    $('.suma-inventarios').on('input', calcularSumas);
    $('.suma-activos-fijos').on('input', calcularSumas);
    $('#cuentas_cobrar').on('input', calcularSumas);
    $('.suma-cuentas-pagar').on('input', calcularSumas);
    $('.suma-ventas').on('input', calcularSumas);
    $('#costos_venta').on('input', calcularSumas);
    $('#gastos_generales').on('input', calcularSumas);
    $('.suma-fcm').on('input', calcularSumas);
    $('#cuota_periodica').on('input', calcularSumas);
    $('#monto_credito_solicitado').on('input', calcularSumas);
    $('#fcm_valor_canasta_basica').on('input', calcularSumas);
    $('#fcm_cant_personas_dep').on('input', calcularSumas);
    $('#flujo_neto_disponible').on('input', calcularSumas);
    $('#cuota_periodica').on('input', validarCuotaVsFlujo);
}

function calcularSumas() {
    // Disponible
    let efectivo = parseFloat($('#efectivo_caja').val()) || 0;
    let banco = parseFloat($('#dinero_banco').val()) || 0;
    let total_disponible = efectivo + banco;
    $('#total_disponible').val(total_disponible);
    // Inventarios
    let inv1 = parseFloat($('#inventario_mercaderia').val()) || 0;
    let inv2 = parseFloat($('#productos_proceso').val()) || 0;
    let inv3 = parseFloat($('#productos_terminados').val()) || 0;
    let total_inventarios = inv1 + inv2 + inv3;
    $('#total_inventarios').val(total_inventarios);

    // GASTOS FIJOS MENSUALES
    let total_gastos_fijos = 0;
    $('.suma-gastos-fijos').each(function() {
        total_gastos_fijos += parseFloat($(this).val()) || 0;
    });
    $('#total_gastos_fijos').val(total_gastos_fijos.toFixed(2));
    // Activos fijos
    let af1 = parseFloat($('#bienes_muebles').val()) || 0;
    let af2 = parseFloat($('#propiedades').val()) || 0;
    let af3 = parseFloat($('#otros_activos').val()) || 0;
    let total_activos_fijos = af1 + af2 + af3;
    $('#total_activos_fijos').val(total_activos_fijos);
    // Total activos
    let cuentas_cobrar = parseFloat($('#cuentas_cobrar').val()) || 0;
    let total_activos = total_disponible + total_inventarios + total_activos_fijos + cuentas_cobrar;
    $('#total_activos').val(total_activos);
    // Pasivos
    let pagar1 = parseFloat($('#cuentas_pagar_proveedores').val()) || 0;
    let pagar2 = parseFloat($('#cuentas_pagar_credito').val()) || 0;
    let pasivo_no_corriente = parseFloat($('#pasivo_no_corriente').val()) || 0;
    let total_pasivo = pagar1 + pagar2 + pasivo_no_corriente;
    $('#total_pasivo').val(total_pasivo);
    // Patrimonio
    let total_patrimonio = total_activos - total_pasivo;
    $('#total_patrimonio').val(total_patrimonio);
    // Pasivo + Patrimonio
    let total_pasivo_patrimonio = total_pasivo + total_patrimonio;
    $('#total_pasivo_patrimonio').val(total_pasivo_patrimonio);

    // INDICADORES
    // Nivel de Endeudamiento = (Total Pasivo + Monto Crédito Solicitado) / Total Activos
    let monto_credito = parseFloat($('#monto_credito_solicitado').val()) || 0;
    let indicador_endeudamiento = total_activos > 0 ? ((total_pasivo + monto_credito) / total_activos) : 0;
    $('#indicador_endeudamiento').val((indicador_endeudamiento * 100).toFixed(1) + ' %');
    // Capital de trabajo Neto (Activo Corriente – Pasivo Corriente)
    let activo_corriente = total_disponible + total_inventarios + cuentas_cobrar;
    let pasivo_corriente = pagar1 + pagar2;
    let capital_trabajo_neto = activo_corriente - pasivo_corriente;
    $('#capital_trabajo_neto').val(capital_trabajo_neto);
    // Cobertura de la deuda capacidad de pago = (Cuota / Flujo neto disponible)
    let cuota = parseFloat($('#cuota_periodica').val()) || 0;
    let cobertura_deuda = null;
    // FLUJO DE CAJA MENSUAL (1+2-3-4+5-6-7)
    let fcm_ventas_contado = parseFloat($('#fcm_ventas_contado').val()) || 0; // 1
    let fcm_recuperacion_credito = parseFloat($('#fcm_recuperacion_credito').val()) || 0; // 2
    let fcm_compras_contado = parseFloat($('#fcm_compras_contado').val()) || 0; // 3
    let fcm_gastos_generales = parseFloat($('#fcm_gastos_generales').val()) || 0; // 4
    let fcm_otros_ingresos = parseFloat($('#fcm_otros_ingresos').val()) || 0; // 5
    // Cálculo automático de Gastos consumo familiar (6)
    let fcm_valor_canasta_basica = parseFloat($('#fcm_valor_canasta_basica').val()) || 0;
    let fcm_cant_personas_dep = parseFloat($('#fcm_cant_personas_dep').val()) || 0;
    let fcm_gastos_consumo = 0;
    if (fcm_valor_canasta_basica > 0 && fcm_cant_personas_dep > 0) {
        fcm_gastos_consumo = (fcm_valor_canasta_basica / 6) * fcm_cant_personas_dep;
        $('#fcm_gastos_consumo').val(fcm_gastos_consumo.toFixed(2));
    } else {
        fcm_gastos_consumo = parseFloat($('#fcm_gastos_consumo').val()) || 0;
    }
    let fcm_otros_gastos = parseFloat($('#fcm_otros_gastos').val()) || 0; // 7

    // Calcular Flujo del negocio (1+2-3-4)
    let flujo_negocio = fcm_ventas_contado + fcm_recuperacion_credito - fcm_compras_contado - fcm_gastos_generales;
    $('#flujo_negocio').val(flujo_negocio.toFixed(2));

    let flujo_neto_disponible = fcm_ventas_contado + fcm_recuperacion_credito - fcm_compras_contado - fcm_gastos_generales + fcm_otros_ingresos - fcm_gastos_consumo - fcm_otros_gastos;
    $('#flujo_neto_disponible').val(flujo_neto_disponible);
    // Siempre mostrar el resultado de la fórmula, aunque sea 0 o negativo
    if (flujo_neto_disponible !== 0) {
        cobertura_deuda = (cuota / flujo_neto_disponible) * 100;
    } else {
        cobertura_deuda = 0;
    }
    $('#cobertura_deuda').val(cobertura_deuda.toFixed(1) + ' %');

    // ESTADO DE RESULTADO MENSUAL
    // Ventas totales
    let ventas_contado = parseFloat($('#ventas_contado').val()) || 0;
    let ventas_credito = parseFloat($('#ventas_credito').val()) || 0;
    let ventas_totales = ventas_contado + ventas_credito;
    $('#ventas_totales').val(ventas_totales);
    // Margen bruto
    let costos_venta = parseFloat($('#costos_venta').val()) || 0;
    let margen_bruto = ventas_totales - costos_venta;
    $('#margen_bruto').val(margen_bruto);
    // Utilidad operativa
    let gastos_generales = parseFloat($('#gastos_generales').val()) || 0;
    let utilidad_operativa = margen_bruto - gastos_generales;
    $('#utilidad_operativa').val(utilidad_operativa);

    validarCuotaVsFlujo();
}

function validarCuotaVsFlujo() {
    var $cuota = $('#cuota_periodica');
    var cuota = parseFloat($cuota.val()) || 0;
    var flujo = parseFloat($('#flujo_neto_disponible').val()) || 0;
    if (cuota > flujo) {
        $cuota.addClass('is-invalid');
    } else {
        $cuota.removeClass('is-invalid');
    }
}

// Helper global para obtener el valor numérico de un input de porcentaje
function getPercentVal(selector) {
    let val = $(selector).val();
    if (typeof val === 'string' && val.indexOf('%') !== -1) {
        val = val.replace('%','').replace(',','.').replace(/\s/g,'');
    }
    let num = parseFloat(val);
    return isNaN(num) ? 0 : num;
}
</script>
