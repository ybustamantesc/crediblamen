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
            <form id="formFiltros" method="GET" action="<?php echo base_url('analisis_financiero'); ?>">
                <div class="row mb-3">
                    <div class="col-md-3">
                            <label>Estado</label>
                        <select id="filtro_estado" name="estado" class="form-control">
                            <option value="all" <?php if($filtro_estado=='all') echo 'selected'; ?>>Todos</option>
                            <option value="pending" <?php if($filtro_estado=='pending') echo 'selected'; ?>>Pendiente</option>
                            <option value="completed" <?php if($filtro_estado=='completed') echo 'selected'; ?>>Completado</option>
                            <option value="annulled" <?php if($filtro_estado=='annulled') echo 'selected'; ?>>Anulado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                            <label>Desde</label>
                        <input type="date" id="filtro_start_date" name="start_date" class="form-control" value="<?php echo $filtro_start_date; ?>">
                    </div>
                    <div class="col-md-3">
                            <label>Hasta</label>
                        <input type="date" id="filtro_end_date" name="end_date" class="form-control" value="<?php echo $filtro_end_date; ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">Filtrar</button>
                    </div>
                </div>
            </form>
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
                                                <td><?php echo trim($s->nombres . ' ' . $s->apellidos); ?></td>
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
                                                        <div class="btn-actions">
                                                            <button class="btn btn-sm btn-info btn-comerciante" data-id="<?php echo $s->idsolicitud; ?>" data-tipo="comerciante" data-solicitud="<?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?>" data-cliente="<?php echo htmlspecialchars(trim($s->nombres . ' ' . $s->apellidos), ENT_QUOTES); ?>"><i class="fa fa-user-tie"></i> Comerciante</button>
                                                            <button class="btn btn-sm btn-warning btn-asalariado" data-id="<?php echo $s->idsolicitud; ?>" data-tipo="asalariado" data-solicitud="<?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?>" data-cliente="<?php echo htmlspecialchars(trim($s->nombres . ' ' . $s->apellidos), ENT_QUOTES); ?>"><i class="fa fa-store"></i> Asalariado</button>
                                                        </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalAnalisisLabel">Definir Análisis Financiero</h5>
                    <p class="mb-0 text-muted" id="modalAnalisisSubtitle" style="font-size:0.95rem;"></p>
                </div>
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
                <button type="button" class="btn btn-success d-none" id="btnDescargarPDFAsalariado" data-pdf-url="">Descargar PDF Asalariado</button>
                <button type="button" class="btn btn-success d-none" id="btnDescargarPDFComerciante" data-pdf-url="">Descargar PDF Comerciante</button>
            </div>
        </div>
    </div>
</div>
<style>
    #modalAnalisisFinanciero .modal-dialog {
        max-width: 860px;
        width: 100%;
        margin: 1.75rem auto;
    }
    #modalAnalisisFinanciero .modal-content {
        border-radius: 0.65rem;
    }
    #modalAnalisisFinanciero .modal-header,
    #modalAnalisisFinanciero .modal-footer {
        padding: 1rem 1.5rem;
    }
    #modalAnalisisFinanciero .modal-body {
        padding: 1.5rem;
        max-height: calc(100vh - 180px);
        overflow-y: auto;
    }
    #modalAnalisisFinanciero .modal-body .row > [class*="col-"] {
        min-width: 0;
    }
    #tabla_solicitudes_pendientes td .btn-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: .35rem;
        white-space: nowrap;
        align-items: center;
    }
    #tabla_solicitudes_pendientes th:nth-child(5),
    #tabla_solicitudes_pendientes td:nth-child(5) {
        width: 14%;
        max-width: 140px;
        white-space: normal;
    }
    #tabla_solicitudes_pendientes th:nth-child(8),
    #tabla_solicitudes_pendientes td:nth-child(8) {
        width: 20%;
        min-width: 170px;
    }
</style>
<?php $this->load->view('layout/footer'); ?>
<script>
// Mostrar botón de descarga PDF según tipo
function mostrarBotonDescargaPDF(tipo, idsolicitud) {
    var pdfAsalariadoUrl = base_url + 'analisis_financiero/descargar_pdf_asalariado/' + idsolicitud;
    var pdfComercianteUrl = base_url + 'analisis_financiero/descargar_pdf_comerciante/' + idsolicitud;

    if (tipo === 'asalariado') {
        $('#btnDescargarPDFAsalariado')
            .data('pdf-url', pdfAsalariadoUrl)
            .removeClass('d-none');
        $('#btnDescargarPDFComerciante')
            .data('pdf-url', '')
            .addClass('d-none');
    } else if (tipo === 'comerciante') {
        $('#btnDescargarPDFComerciante')
            .data('pdf-url', pdfComercianteUrl)
            .removeClass('d-none');
        $('#btnDescargarPDFAsalariado')
            .data('pdf-url', '')
            .addClass('d-none');
    } else {
        $('#btnDescargarPDFAsalariado').data('pdf-url', '').addClass('d-none');
        $('#btnDescargarPDFComerciante').data('pdf-url', '').addClass('d-none');
    }
}

$(document).on('click', '#btnDescargarPDFAsalariado, #btnDescargarPDFComerciante', function() {
    var url = $(this).data('pdf-url');
    if (url) {
        window.open(url, '_blank');
    }
});

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
// Sincronizar COSTOS DE OPERACIÓN cuando cambian GASTOS FIJOS
$(document).on('input', '#gasto_salario_ayudante, #gasto_transporte', calcularSumas);
// Calcular al cargar por si hay valores precargados
$(document).ready(function() {
    calcularTotalGastosFijos();
});

// Abrir modal y setear tipo según botón
$(document).on('click', '.btn-asalariado, .btn-comerciante', function() {
    var id = $(this).data('id');
    var tipo = $(this).data('tipo') || ($(this).hasClass('btn-asalariado') ? 'asalariado' : 'comerciante');
    var codigoSolicitud = $(this).data('solicitud') || '';
    var clienteNombre = $(this).data('cliente') || '';
    $('#idsolicitud_modal').val(id);
    $('#tipo_analisis').val(tipo);
    $('#modalAnalisisSubtitle').text('Solicitud: ' + codigoSolicitud + ' | Cliente: ' + clienteNombre);
    if (tipo === 'asalariado') {
        renderCamposAsalariado();
        $('#modalAnalisisLabel').text('Análisis Financiero Asalariado');
        // Cargar datos guardados si existen
        $.ajax({
            url: base_url + 'analisis_financiero/get_asalariado/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.status && resp.data) {
                    // Agregar indicador de datos cargados desde solicitud al subtítulo
                    var subtitulo = 'Solicitud: ' + codigoSolicitud + ' | Cliente: ' + clienteNombre;
                    if (resp.data.datos_cargados_desde_solicitud) {
                        subtitulo += ' <span style="color:#28a745; font-weight: bold;">✓ Datos cargados desde Solicitud Inicial</span>';
                    }
                    $('#modalAnalisisSubtitle').html(subtitulo);
                    
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

                    // Inicializar acumulador para obligaciones del asalariado
                    var sum_asal_olp_saldo = 0;
                    
                    for (var k in resp.data) {
                        if (resp.data.hasOwnProperty(k) && k !== 'datos_cargados_desde_solicitud') {
                            // Si es un array de saldos OLP, sumar mientras se popula
                            if (k === 'olp_saldo' && Array.isArray(resp.data[k])) {
                                resp.data[k].forEach(function(val) {
                                    sum_asal_olp_saldo += parseFloat(val) || 0;
                                });
                            }
                            
                            if (setArrayField(k, resp.data[k])) {
                                continue;
                            }
                            var $el = $('[name="'+k+'"]');
                            if ($el.length) {
                                // Si es indicador, formatear como porcentaje
                                if (k === 'indicador_endeudamiento' || k === 'porcentaje_margen' || k === 'porcentaje_deuda_total') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val((val * 100).toFixed(1) + ' %');
                                    } else {
                                        $el.val('0.0 %');
                                    }
                                } else if (k === 'cobertura_deuda') {
                                    var val = resp.data[k];
                                    if (typeof val === 'string') {
                                        val = val.replace('%', '').replace(',', '.').trim();
                                    }
                                    val = parseFloat(val);
                                    if (!isNaN(val)) {
                                        if (val > 0 && val <= 1) {
                                            val = val * 100;
                                        }
                                        $el.val(val.toFixed(2) + ' %');
                                    } else {
                                        $el.val('0.00 %');
                                    }
                                } else if (k === 'cobertura_garantia') {
                                    // Cobertura de garantía ya viene en porcentaje, solo agregar %
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        $el.val(val.toFixed(2) + ' %');
                                    } else {
                                        $el.val('0.00 %');
                                    }
                                } else if (k === 'tasa_interes' || k === 'comision_desembolso') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        if (val > 0 && val <= 1) {
                                            val = val * 100;
                                        }
                                        var formatted = val.toFixed(2).replace(/\.00$/, '');
                                        $el.val(formatted);
                                    } else {
                                        $el.val('0');
                                    }
                                } else {
                                    $el.val(resp.data[k]);
                                }
                            }
                            // Actualizar label asociado si existe (mostrar valor cargado)
                            // Excluir 'comentario' para que no se muestre en el label
                            if (k !== 'comentario') {
                                var $label = $('#label_' + k);
                                if ($label.length) {
                                    var valToShow = resp.data[k];
                                    if (k === 'numero_cuotas') {
                                        var plazoValue = parseFloat($('#plazo_credito').val()) || 0;
                                        valToShow = plazoValue > 0 ? Math.round(plazoValue * 2) : 0;
                                    } else {
                                        var $inputForLabel = $('[name="' + k + '"]');
                                        if ($inputForLabel.length) {
                                            valToShow = $inputForLabel.val();
                                        }
                                    }
                                    var orig = $label.data('original') || $label.text();
                                    $label.html(orig + ': ' + (valToShow !== undefined && valToShow !== null ? '<strong>' + valToShow + '</strong>' : ''));
                                }
                            }
                        }
                    }
                    // Disparar cálculo automático por input en los campos relevantes
                    $('#ingreso_sueldo_neto, #ingreso_comisiones, #ingreso_bonificaciones, #ingreso_remesas, #ingreso_otros, #gastos_servicios, #gastos_vestuario, #gastos_educativos, #gastos_transporte, #gastos_alquiler, #pago_empleado_viatico, #entretenimiento, #otros_gastos, #gasto_personal, #cuotas_prestamos, #pension_alimenticia, #otras_obligaciones, #transporte_urbano, #transporte_individual, #transporte_interurbano, #recorrido_laboral, #vehiculo_particular, #alquiler, #casa_propia, #personas_dependientes, #plazo_credito, #total_deuda_acreditar').trigger('input');
                    var frecuenciaActual = ($('#frecuencia_pago').val() || '').toLowerCase();
                    if (frecuenciaActual !== 'quincenal' && frecuenciaActual !== 'catorcenal') {
                        frecuenciaActual = 'quincenal';
                        $('#frecuencia_pago').val(frecuenciaActual);
                    }
                    actualizarNumeroCuotasPorFrecuencia();
                    // Asignar el subtotal calculado durante el loop
                    $('#asal_subtotal_olp_saldo').val(sum_asal_olp_saldo.toFixed(2));
                }
            }
        });
    } else {
        renderCamposComerciante();
        $('#modalAnalisisLabel').text('Análisis Financiero Comerciante');
        $('#modalAnalisisSubtitle').text('Solicitud: ' + codigoSolicitud + ' | Cliente: ' + clienteNombre);
        // Cargar datos guardados si existen
        $.ajax({
            url: base_url + 'analisis_financiero/get_comerciante/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.status && resp.data) {
                    // Reusar helper para campos que vienen como arrays (olp_*, ocp_*, etc.)
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

                    // Inicializar acumuladores para obligaciones
                    var sum_olp_saldo = 0;
                    var sum_ocp_saldo = 0;
                    
                    for (var k in resp.data) {
                        if (resp.data.hasOwnProperty(k)) {
                            // Si es un array de saldos, sumar mientras se popula
                            if (k === 'olp_saldo' && Array.isArray(resp.data[k])) {
                                resp.data[k].forEach(function(val) {
                                    sum_olp_saldo += parseFloat(val) || 0;
                                });
                            }
                            if (k === 'ocp_saldo' && Array.isArray(resp.data[k])) {
                                resp.data[k].forEach(function(val) {
                                    sum_ocp_saldo += parseFloat(val) || 0;
                                });
                            }
                            
                            if (setArrayField(k, resp.data[k])) {
                                continue;
                            }
                            var $el = $('[name="'+k+'"]');
                            if ($el.length) {
                                // Si es cobertura de deuda o cobertura de garantía, formatear con %
                                if (k === 'cobertura_deuda' || k === 'cobertura_garantia') {
                                    var val = resp.data[k];
                                    if (typeof val === 'string') {
                                        val = val.replace('%', '').replace(',', '.').trim();
                                    }
                                    val = parseFloat(val);
                                    if (!isNaN(val)) {
                                        if (val > 0 && val <= 1) {
                                            val = val * 100;
                                        }
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
                                } else if (k === 'tasa_interes' || k === 'comision_desembolso') {
                                    var val = parseFloat(resp.data[k]);
                                    if (!isNaN(val)) {
                                        if (val > 0 && val <= 1) {
                                            val = val * 100;
                                        }
                                        var formatted = val.toFixed(2).replace(/\.00$/, '');
                                        $el.val(formatted);
                                    } else {
                                        $el.val('0');
                                    }
                                } else {
                                    $el.val(resp.data[k]);
                                }
                            }
                            // Actualizar label asociado si existe (mostrar valor cargado)
                            // Excluir 'comentario' para que no se muestre en el label
                            if (k !== 'comentario') {
                                var $label = $('#label_' + k);
                                if ($label.length) {
                                    var valToShow = resp.data[k];
                                    if (k === 'numero_cuotas') {
                                        var plazoValue = parseFloat($('#plazo_credito').val()) || 0;
                                        valToShow = plazoValue > 0 ? Math.round(plazoValue * 2) : 0;
                                    } else {
                                        var $inputForLabel = $('[name="' + k + '"]');
                                        if ($inputForLabel.length) {
                                            valToShow = $inputForLabel.val();
                                        }
                                    }
                                    var orig = $label.data('original') || $label.text();
                                    $label.html(orig + ': ' + (valToShow !== undefined && valToShow !== null ? '<strong>' + valToShow + '</strong>' : ''));
                                }
                            }
                        }
                    }

                    function syncComercianteLoadedFields() {
                        var ventasContado = $('#ventas_contado').val();
                        var fcmVentasContado = $('#fcm_ventas_contado').val();
                        if (ventasContado !== '' && (fcmVentasContado === '' || fcmVentasContado === '0')) {
                            $('#fcm_ventas_contado').val(ventasContado);
                        }
                        if (fcmVentasContado !== '' && (ventasContado === '' || ventasContado === '0')) {
                            $('#ventas_contado').val(fcmVentasContado);
                        }

                        var ventasCredito = $('#ventas_credito').val();
                        var fcmVentasCredito = $('#fcm_recuperacion_credito').val();
                        if (ventasCredito !== '' && (fcmVentasCredito === '' || fcmVentasCredito === '0')) {
                            $('#fcm_recuperacion_credito').val(ventasCredito);
                        }
                        if (fcmVentasCredito !== '' && (ventasCredito === '' || ventasCredito === '0')) {
                            $('#ventas_credito').val(fcmVentasCredito);
                        }

                        var gastosGenerales = $('#gastos_generales').val();
                        var fcmGastosGenerales = $('#fcm_gastos_generales').val();
                        if (gastosGenerales !== '' && (fcmGastosGenerales === '' || fcmGastosGenerales === '0')) {
                            $('#fcm_gastos_generales').val(gastosGenerales);
                        }
                        if (fcmGastosGenerales !== '' && (gastosGenerales === '' || gastosGenerales === '0')) {
                            $('#gastos_generales').val(fcmGastosGenerales);
                        }
                    }

                    syncComercianteLoadedFields();
                    var frecuenciaActual = ($('#frecuencia_pago').val() || '').toLowerCase();
                    if (frecuenciaActual !== 'quincenal' && frecuenciaActual !== 'catorcenal') {
                        frecuenciaActual = 'quincenal';
                        $('#frecuencia_pago').val(frecuenciaActual);
                    }
                    // Disparar cálculo de cuotas con la nueva fórmula (plazo * 2)
                    $('#plazo_credito').trigger('input');
                    actualizarNumeroCuotasPorFrecuencia();
                    calcularSumas();
                    // Asignar los subtotales calculados durante el loop
                    $('#subtotal_olp_saldo').val(sum_olp_saldo.toFixed(2));
                    $('#subtotal_ocp_saldo').val(sum_ocp_saldo.toFixed(2));
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
    ['indicador_endeudamiento','porcentaje_margen','porcentaje_deuda_total','cobertura_garantia'].forEach(function(campo) {
        var $el = $('[name="'+campo+'"]');
        if ($el.length) {
            var val = $el.val();
            if (typeof val === 'string' && val.indexOf('%') !== -1) {
                val = val.replace('%','').replace(',','.').replace(/\s/g,'');
                var num = parseFloat(val);
                if (!isNaN(num)) {
                    if (campo === 'indicador_endeudamiento' || campo === 'porcentaje_margen' || campo === 'porcentaje_deuda_total') {
                        $el.val((num/100).toFixed(6));
                    } else {
                        $el.val(num.toFixed(6));
                    }
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
    // Asegurar que COSTOS DE OPERACIÓN estén sincronizados antes de guardar
    calcularSumas();
    normalizePercentageForSubmit('#tasa_interes');
    normalizePercentageForSubmit('#comision_desembolso');
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
                var idsol = $('#idsolicitud_modal').val();
                // Descargar PDF automáticamente según el tipo
                if (tipo === 'asalariado') {
                    setTimeout(function() {
                        window.open(base_url + 'analisis_financiero/descargar_pdf_asalariado/' + idsol, '_blank');
                    }, 500);
                } else if (tipo === 'comerciante') {
                    setTimeout(function() {
                        window.open(base_url + 'analisis_financiero/descargar_pdf_comerciante/' + idsol, '_blank');
                    }, 500);
                }
                // Refrescar la página después de guardar
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert('Error al guardar: ' + (resp.msg || ''));
            }
        },
        error: function() {
            alert('Error de comunicación con el servidor');
        }
    });
});

function renderCamposAsalariado() {
    let html = `
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label><b>Sueldo Bruto (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control sueldo-campo" name="sueldo" id="sueldo" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>INSS (7%)</b></label>
                <input type="number" class="form-control" name="inss" id="inss" readonly value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>IR</b></label>
                <input type="number" class="form-control" name="ir" id="ir" readonly value="0">
            </div>
        </div>
        <hr/>
        <h6 class='mt-3 mb-2 text-muted'>Canasta Básica</h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Canasta básica C$</label>
                <input type="number" min="0" step="any" class="form-control canasta-campo" name="canasta_basica" id="canasta_basica" value="21249.74">
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad promedio</label>
                <input type="number" min="0" step="1" class="form-control canasta-campo" name="cantidad_promedio" id="cantidad_promedio" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Monto por persona</label>
                <input type="number" min="0" step="any" class="form-control" name="monto_por_persona" id="monto_por_persona" value="0.00" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad de personas dependientes</label>
                <input type="number" min="0" step="1" class="form-control canasta-campo" name="personas_dependientes" id="personas_dependientes" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Gastos de alimentación</label>
                <input type="number" min="0" step="any" class="form-control" name="gastos_alimentacion_canasta" id="gastos_alimentacion_canasta" value="0" readonly>
            </div>
        </div>
        <hr/>
        <h6 class='mt-3 mb-2 text-muted'>Transporte</h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Transporte urbano colectivo (C$)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_urbano" id="transporte_urbano" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio individual (taxi, caponera) (C$)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_individual" id="transporte_individual" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte interurbano (C$)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="transporte_interurbano" id="transporte_interurbano" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Recorrido laboral (C$)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="recorrido_laboral" id="recorrido_laboral" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Vehículo particular de uso personal (C$)</label>
                <input type="number" min="0" step="any" class="form-control transporte-campo" name="vehiculo_particular" id="vehiculo_particular" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total transporte</b></label>
                <input type="number" class="form-control" name="total_transporte" id="total_transporte" value="0" readonly>
            </div>
        </div>
        <hr/>
        <h6 class='mt-3 mb-2 text-muted'>Vivienda</h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Alquiler (C$)</label>
                <input type="number" min="0" step="any" class="form-control vivienda-campo" name="alquiler" id="alquiler" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Casa propia (C$)</label>
                <input type="number" min="0" step="any" class="form-control vivienda-campo" name="casa_propia" id="casa_propia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total Gastos Vivienda</b></label>
                <input type="number" class="form-control" name="total_gastos_vivienda" id="total_gastos_vivienda" value="0" readonly>
            </div>
        </div>
        <h5 class="mt-4 mb-2">FLUJO MENSUAL</h5>
        <h6 class="mb-3">INGRESOS</h6>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>A. Sueldo Neto (restadas las deducciones INSS e IR)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_sueldo_neto" id="ingreso_sueldo_neto" readonly value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>B. Comisiones (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_comisiones" id="ingreso_comisiones" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>C. Bonificaciones (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_bonificaciones" id="ingreso_bonificaciones" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>D. Remesas (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_remesas" id="ingreso_remesas" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>E. Otros ingresos (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-ingresos" name="ingreso_otros" id="ingreso_otros" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(1) TOTAL INGRESOS (A+B+C+D+E)</b></label>
                <input type="number" class="form-control" id="total_ingresos" name="total_ingresos" readonly>
            </div>
        </div>
        <h6 class="mt-3 mb-3">GASTOS</h6>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>F. Gastos en alimentación</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_alimentacion" id="gastos_alimentacion" value="0" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>G. Servicios básicos (agua, luz, Internet Fijo) (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_servicios" id="gastos_servicios" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>H. Vestuario (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_vestuario" id="gastos_vestuario" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>I. Gastos educativos (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_educativos" id="gastos_educativos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>J. Gastos en transporte/ reparaciones, combustible (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_transporte" id="gastos_transporte" value="0" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>K. Gastos en alquiler o arriendo vivienda (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gastos_alquiler" id="gastos_alquiler" value="0" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>L. Pago empleado/viático (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="pago_empleado_viatico" id="pago_empleado_viatico" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>P. Entretenimiento (incluye gastos derivados del uso celulares e internet) (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="entretenimiento" id="entretenimiento" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Q. Otros Gastos (Especifique) pago de trabajador+viático de transporte (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="otros_gastos" id="otros_gastos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>R. Gastos Personales (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-familiares" name="gasto_personal" id="gasto_personal" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>(2) GASTOS FAMILIARES TOTAL (F+G+H+I+J+K+L+P+Q+R)</b></label>
                <input type="number" class="form-control" id="total_gastos_familiares" name="total_gastos_familiares" readonly>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>M. Abono o cuotas de préstamos o deudas con instituciones financieras, casas comerciales o particulares (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-otras-obligaciones" name="cuotas_prestamos" id="cuotas_prestamos" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>N. Pensión alimenticia o similares (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control suma-otras-obligaciones" name="pension_alimenticia" id="pension_alimenticia" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label><b>O. Otros (C$)</b></label>
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
                <small class="text-muted">Sugerida desde Solicitud Inicial: cuota mensual en US$ x 36.6243.</small>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label><b>Cobertura de la deuda con capacidad de pago = (Cuota / Flujo neto disponible), Máxima porción a comprometer del flujo = 25%</b></label>
                <input type="text" class="form-control" id="cobertura_deuda" name="cobertura_deuda" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>Cobertura de garantía (150%)</b></label>
                <input type="text" class="form-control" id="cobertura_garantia" name="cobertura_garantia" readonly>
            </div>
            <div class="col-md-6 mb-2">
                <label><b>T/C Acumulado de liquidación (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control" name="tc_acumulado" id="tc_acumulado" value="0">
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-3'><b>RECOMENDACIÓN DE CRÉDITO</b></h5>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label id="label_tipo_credito" data-original="Tipo de Crédito">Tipo de Crédito</label>
                <input type="text" class="form-control" name="tipo_credito" id="tipo_credito" placeholder="Ej: Crédito Personal, Crédito Productivo">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_monto_financiar" data-original="Monto a financiar (US$)">Monto a financiar (US$)</label>
                <div class="input-group">
                    <input type="number" min="0" step="any" class="form-control" name="monto_financiar" id="monto_financiar" value="0">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary" id="btn_procesar_cuota_analisis">Procesar cuota</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="tasa_interes">Interés (%)</label>
                <input type="text" class="form-control" name="tasa_interes" id="tasa_interes" value="0" placeholder="Ej: 16">
            </div>
            <div class="col-md-6 mb-2">
                <label for="comision_desembolso">Desembolso (%)</label>
                <input type="text" class="form-control" name="comision_desembolso" id="comision_desembolso" value="0" placeholder="Ej: 7">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label id="label_plazo_credito" data-original="Plazo del crédito (en meses)">Plazo del crédito (en meses)</label>
                <input type="number" min="0" step="1" class="form-control" name="plazo_credito" id="plazo_credito" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_numero_cuotas" data-original="No. de cuotas">No. de cuotas</label>
                <input type="number" min="0" step="1" class="form-control" name="numero_cuotas" id="numero_cuotas" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_monto_cuota" data-original="Monto de cada cuota (US$)">Monto de cada cuota (US$)</label>
                <input type="number" min="0" step="any" class="form-control" name="monto_cuota" id="monto_cuota" value="0">
                <input type="hidden" name="cuota_estim_estimada" id="cuota_estim_estimada" value="">
                <input type="hidden" name="cuota_estim_estimada_quincenal" id="cuota_estim_estimada_quincenal" value="">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_fecha_pago_cuota" data-original="Fecha de pago de cada cuota">Fecha de pago de cada cuota</label>
                <input type="text" class="form-control" name="fecha_pago_cuota" id="fecha_pago_cuota" placeholder="Ej: 15 de cada mes">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_frecuencia_pago" data-original="Frecuencia de pago de cada cuota">Frecuencia de pago de cada cuota</label>
                <select class="form-control" name="frecuencia_pago" id="frecuencia_pago">
                    <option value="quincenal" selected>Quincenal</option>
                    <option value="catorcenal">Catorcenal</option>
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_forma_pago" data-original="Forma de pago">Forma de pago</label>
                <input type="text" class="form-control" name="forma_pago" id="forma_pago">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_garantia_requerida" data-original="Garantía requerida">Garantía requerida</label>
                <input type="text" class="form-control" name="garantia_requerida" id="garantia_requerida" placeholder="Detalle de la garantía o garantías requeridas">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_fundamentacion_propuesta" data-original="Fundamentación de la propuesta">Fundamentación de la propuesta</label>
                <input type="text" class="form-control" name="fundamentacion_propuesta" id="fundamentacion_propuesta" placeholder="Justificación y análisis de la recomendación de crédito">
            </div>
            <div class="col-md-12 mb-2">
                <label id="label_comentario" data-original="Comentario">Comentario</label>
                <textarea class="form-control" name="comentario" id="comentario" rows="4" placeholder="Agregar comentarios adicionales sobre el análisis financiero"></textarea>
            </div>
        </div>
    </div>
    `;
    $('#campos_dinamicos').html(html);
    function actualizarNumeroCuotasPorFrecuencia() {
        var frecuencia = ($('#frecuencia_pago').val() || 'quincenal').toLowerCase();
        $('#frecuencia_pago').val(frecuencia);
        var plazo = parseFloat($('#plazo_credito').val()) || 0;

        // Mantener compatibilidad con la lógica previa: si 'quincenal' o 'catorcenal'
        // usamos el mismo cálculo base (plazo*2) para `numero_cuotas`.
        var cuotas = 0;
        if (plazo > 0) {
            if (frecuencia === 'quincenal' || frecuencia === 'catorcenal') {
                cuotas = plazo * 2;
            } else {
                cuotas = plazo;
            }
        }
        $('#numero_cuotas').val(cuotas > 0 ? Math.round(cuotas) : 0);
        // Nota: no se modifica `monto_cuota` aquí — el monto se mantiene
        // independientemente de la frecuencia, tal como pidió el usuario.
    }
    $('#frecuencia_pago').on('change', actualizarNumeroCuotasPorFrecuencia);
    $('#plazo_credito').on('input', actualizarNumeroCuotasPorFrecuencia);
    actualizarNumeroCuotasPorFrecuencia();
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
        calcularFlujoNetoMensual();
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
        total += parseFloat($('#gasto_personal').val()) || 0;
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
        var $ctx = $('#campos_dinamicos');
        if (!$ctx.length) { $ctx = $(document); }
        let total_ingresos = parseFloat($ctx.find('#total_ingresos').val()) || 0;
        let total_egresos = parseFloat($ctx.find('#total_egresos').val()) || 0;
        let total = total_ingresos - total_egresos;
        $ctx.find('#flujo_neto_mensual').val(total.toFixed(2));
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
        sincronizarGastosAlimentacion();
    }
    $('#personas_dependientes').on('input', calcularGastosAlimentacionCanasta);
    calcularGastosAlimentacionCanasta();

    // Sincronizar Gastos de alimentación (CANASTA BÁSICA) hacia F. Gastos en alimentación (GASTOS)
    function sincronizarGastosAlimentacion() {
        let valor = parseFloat($('#gastos_alimentacion_canasta').val()) || 0;
        $('#gastos_alimentacion').val(valor.toFixed(2));
        calcularTotalGastosFamiliares();
    }

    // Total transporte = suma de 5 campos de transporte
    function calcularTotalTransporte() {
        let total = 0;
        total += parseFloat($('#transporte_urbano').val()) || 0;
        total += parseFloat($('#transporte_individual').val()) || 0;
        total += parseFloat($('#transporte_interurbano').val()) || 0;
        total += parseFloat($('#recorrido_laboral').val()) || 0;
        total += parseFloat($('#vehiculo_particular').val()) || 0;
        $('#total_transporte').val(total.toFixed(2));
        // Also populate the gastos field used in GASTOS section and update totals
        $('#gastos_transporte').val(total.toFixed(2));
        calcularTotalGastosFamiliares();
    }
    $('.transporte-campo').on('input', calcularTotalTransporte);
    calcularTotalTransporte();

    // Total gastos vivienda = Alquiler + Casa propia
    function calcularTotalGastosVivienda() {
        let total = 0;
        total += parseFloat($('#alquiler').val()) || 0;
        total += parseFloat($('#casa_propia').val()) || 0;
        $('#total_gastos_vivienda').val(total.toFixed(2));
        // Sync into GASTOS (K) field and update totals
        $('#gastos_alquiler').val(total.toFixed(2));
        calcularTotalGastosFamiliares();
    }
    $('.vivienda-campo').on('input', calcularTotalGastosVivienda);
    calcularTotalGastosVivienda();

    // Cobertura de deuda = (Cuota Periódica / Flujo Neto) * 100 en porcentaje.
    // Usa flujo neto mensual o flujo neto disponible según el formulario visible.
    function calcularCoberturaDeuda() {
        var $ctx = $('#campos_dinamicos');
        if (!$ctx.length) { $ctx = $(document); }
        let flujo_neto = 0;
        let $fnMensual = $ctx.find('#flujo_neto_mensual');
        let $fnDisponible = $ctx.find('#flujo_neto_disponible');
        if ($fnMensual.length && $.trim($fnMensual.val()) !== '') {
            flujo_neto = parseFloat($fnMensual.val()) || 0;
        }
        if (flujo_neto === 0 && $fnDisponible.length && $.trim($fnDisponible.val()) !== '') {
            flujo_neto = parseFloat($fnDisponible.val()) || 0;
        }
        let cuota = parseFloat($ctx.find('#cuota_periodica').val()) || 0;
        let cobertura = 0;
        if (flujo_neto > 0) {
            cobertura = (cuota / flujo_neto) * 100;
        }
        let coberturaDisplay = cobertura.toFixed(2) + ' %';
        $ctx.find('#cobertura_deuda').val(coberturaDisplay);
        return cobertura;
    }
    $('#cuota_periodica').on('input', calcularCoberturaDeuda);
    $('.suma-ingresos, .suma-fcm, .suma-gastos-familiares, .suma-otras-obligaciones').on('input', calcularCoberturaDeuda);
    $('#fcm_gastos_consumo').on('input', calcularCoberturaDeuda);
    calcularCoberturaDeuda();

    // Exponer funciones que se usan desde el botón de cuota fuera del scope local
    window.calcularCoberturaDeuda = calcularCoberturaDeuda;
    window.calcularFlujoNetoMensual = calcularFlujoNetoMensual;
    window.actualizarNumeroCuotasPorFrecuencia = actualizarNumeroCuotasPorFrecuencia;

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

    function calcularIR(sueldo) {
        if (sueldo <= 0) {
            return 0;
        }
        let inss = sueldo * 0.07;
        let ingresoNetoMensual = sueldo - inss;
        if (ingresoNetoMensual <= 0) {
            return 0;
        }
        let ingresoAnual = ingresoNetoMensual * 12;
        let irAnual = 0;
        if (ingresoAnual <= 100000) {
            irAnual = 0;
        } else if (ingresoAnual <= 200000) {
            irAnual = (ingresoAnual - 100000) * 0.15;
        } else if (ingresoAnual <= 350000) {
            irAnual = 15000 + (ingresoAnual - 200000) * 0.20;
        } else if (ingresoAnual <= 500000) {
            irAnual = 45000 + (ingresoAnual - 350000) * 0.25;
        } else {
            irAnual = 82500 + (ingresoAnual - 500000) * 0.30;
        }
        return irAnual / 12;
    }

    // Calcular sueldo neto automáticamente
    function calcularSueldoNeto() {
        let sueldo = parseFloat($('#sueldo').val()) || 0;
        let inss = parseFloat((sueldo * 0.07).toFixed(2)) || 0;
        let ir = parseFloat(calcularIR(sueldo).toFixed(2)) || 0;
        let neto = sueldo - inss - ir;
        $('#inss').val(inss.toFixed(2));
        $('#ir').val(ir.toFixed(2));
        $('#ingreso_sueldo_neto').val(neto.toFixed(2));
        calcularTotalIngresos();
        calcularSumas();
        calcularFlujoNetoMensual();
    }
    $('.sueldo-campo').on('input', calcularSueldoNeto);
    calcularSueldoNeto();
}

function renderCamposComerciante() {
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
                <label>A. Efectivo o Caja (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-disponible" name="efectivo_caja" id="efectivo_caja" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label>B. Dinero ahorrado o Banco (C$)</label>
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
                <label>(3) CUENTAS POR COBRAR (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="cuentas_cobrar" id="cuentas_cobrar" value="0">
            </div>
        </div>
        <h5 class='mt-3 mb-2'>(4) INVENTARIOS</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>A. Inventario de mercadería (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-inventarios" name="inventario_mercaderia" id="inventario_mercaderia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Productos en proceso (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-inventarios" name="productos_proceso" id="productos_proceso" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>C. Productos terminados (C$)</label>
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
                <label>A. Bienes muebles (equipo, maquinaria, etc.) (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-activos-fijos" name="bienes_muebles" id="bienes_muebles" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Propiedades (casa, finca, etc.) (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-activos-fijos" name="propiedades" id="propiedades" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>C. Otros Activos (C$)</label>
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
                <label>(1) Cuentas por pagar a proveedores (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-cuentas-pagar" name="cuentas_pagar_proveedores" id="cuentas_pagar_proveedores" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Cuentas por pagar crédito corto plazo (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-cuentas-pagar" name="cuentas_pagar_credito" id="cuentas_pagar_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(2) Pasivo no corriente (mayor a 1 año) (C$)</label>
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
                <label>A. Ventas al contado (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-ventas" name="ventas_contado" id="ventas_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>B. Ventas al crédito (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-ventas" name="ventas_credito" id="ventas_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(1) VENTAS TOTALES (A+B)</label>
                <input type="number" class="form-control" id="ventas_totales" name="ventas_totales" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>(2) COSTOS DE VENTA (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="costos_venta" id="costos_venta" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>(3) MARGEN BRUTO (1-2)</label>
                <input type="number" class="form-control" id="margen_bruto" name="margen_bruto" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>(4) GASTOS GENERALES (C$)</label>
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
                <label>1. Ventas al contado (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_ventas_contado" id="fcm_ventas_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>2. Recuperación ventas al crédito (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_recuperacion_credito" id="fcm_recuperacion_credito" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>3. Compras al contado (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_compras_contado" id="fcm_compras_contado" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>4. Gastos Generales (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_gastos_generales" id="fcm_gastos_generales" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Flujo del negocio (1+2-3-4)</b></label>
                <input type="number" class="form-control" id="flujo_negocio" name="flujo_negocio" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>5. Otros ingresos de la unidad familiar (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-fcm" name="fcm_otros_ingresos" id="fcm_otros_ingresos" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>6. Gastos consumo familiar (costo mínimo de vida en función canasta básica y cantidad de personas que dependen del titular)</label>
                <input type="number" min="0" step="any" class="form-control" name="fcm_gastos_consumo" id="fcm_gastos_consumo" value="0" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Valor Canasta básica (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="fcm_valor_canasta_basica" id="fcm_valor_canasta_basica" value="21249.74">
            </div>
            <div class="col-md-4 mb-2">
                <label>Cantidad de personas dep</label>
                <input type="number" min="0" step="1" class="form-control" name="fcm_cant_personas_dep" id="fcm_cant_personas_dep" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>7. Otros gastos (pagos de cuotas y otras transacciones financieras) (C$)</label>
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
                <label>Local o casa propia/Alquiler (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_local_alquiler" id="gasto_local_alquiler" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio de energía eléctrica (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_energia" id="gasto_energia" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Servicio de agua potable (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_agua" id="gasto_agua" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Internet residencial/Plan postpago/TV por cable/Teléfono (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_internet" id="gasto_internet" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Seguridad (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_seguridad" id="gasto_seguridad" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Limpieza y mantenimiento (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_limpieza" id="gasto_limpieza" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Gastos personales básicos (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_personal" id="gasto_personal" value="0">
            </div>

            <div class="col-md-4 mb-2">
                <label>Salario de Ayudante Empleado (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_salario_ayudante" id="gasto_salario_ayudante" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte (C$)</label>
                <input type="number" min="0" step="any" class="form-control suma-gastos-fijos" name="gasto_transporte" id="gasto_transporte" value="0">
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total Gastos Fijos Mensuales</b></label>
                <input type="number" class="form-control" id="total_gastos_fijos" name="total_gastos_fijos" readonly>
            </div>
        </div>
        <hr/>
        <div class="largo-plazo-section">
            <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 1</h5>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Fecha</label>
                    <input type="date" class="form-control" name="olp_fecha[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Cuota (C$)</label>
                    <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Instituciones</label>
                    <input type="text" class="form-control" name="olp_instituciones[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Saldo (C$)</label>
                    <input type="number" min="0" step="any" class="form-control olp-saldo" name="olp_saldo[]">
                </div>
            </div>
            <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 2</h5>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Fecha</label>
                    <input type="date" class="form-control" name="olp_fecha[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Cuota (C$)</label>
                    <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Instituciones</label>
                    <input type="text" class="form-control" name="olp_instituciones[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Saldo (C$)</label>
                    <input type="number" min="0" step="any" class="form-control olp-saldo" name="olp_saldo[]">
                </div>
            </div>
            <h5 class='mt-4 mb-2'>OBLIGACIONES A LARGO PLAZO 3</h5>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Fecha</label>
                    <input type="date" class="form-control" name="olp_fecha[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Cuota (C$)</label>
                    <input type="number" min="0" step="any" class="form-control" name="olp_cuota[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Instituciones</label>
                    <input type="text" class="form-control" name="olp_instituciones[]">
                </div>
                <div class="col-md-3 mb-2">
                    <label>Saldo (C$)</label>
                    <input type="number" min="0" step="any" class="form-control olp-saldo" name="olp_saldo[]">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-2"></div>
                <div class="col-md-3 mb-2"></div>
                <div class="col-md-3 mb-2 text-right"><label><b>Subtotal Saldo Obligaciones Largo Plazo</b></label></div>
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" id="subtotal_olp_saldo" name="subtotal_olp_saldo" readonly>
                </div>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>OBLIGACIONES A CORTO PLAZO 1</h5>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label>Fecha</label>
                <input type="date" class="form-control" name="ocp_fecha[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Cuota (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo (C$)</label>
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
                <label>Cuota (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo (C$)</label>
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
                <label>Cuota (C$)</label>
                <input type="number" min="0" step="any" class="form-control" name="ocp_cuota[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Instituciones</label>
                <input type="text" class="form-control" name="ocp_instituciones[]">
            </div>
            <div class="col-md-3 mb-2">
                <label>Saldo (C$)</label>
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
                <input type="number" min="0" step="any" class="form-control" name="costo_salario_ayudante" id="costo_salario_ayudante" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label>Transporte</label>
                <input type="number" min="0" step="any" class="form-control" name="costo_transporte" id="costo_transporte" readonly>
            </div>
            <div class="col-md-4 mb-2">
                <label><b>Total</b></label>
                <input type="number" min="0" step="any" class="form-control" name="costo_total_operacion" id="costo_total_operacion" readonly>
            </div>
        </div>
        <hr/>
        <h5 class='mt-4 mb-2'>INDICADORES</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label><b>Monto Crédito Solicitado (C$)</b></label>
                <input type="number" min="0" step="any" class="form-control" id="monto_credito_solicitado" name="monto_credito_solicitado" value="0">
            </div>
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
        <hr/>
        <h5 class='mt-4 mb-3'><b>RECOMENDACIÓN DE CRÉDITO</b></h5>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label id="label_tipo_credito" data-original="Tipo de Crédito">Tipo de Crédito</label>
                <input type="text" class="form-control" name="tipo_credito" id="tipo_credito" placeholder="Ej: Crédito Personal, Crédito Productivo">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_monto_financiar" data-original="Monto a financiar (US$)">Monto a financiar (US$)</label>
                <div class="input-group">
                    <input type="number" min="0" step="any" class="form-control" name="monto_financiar" id="monto_financiar" value="0">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary" id="btn_procesar_cuota_analisis">Procesar cuota</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="tasa_interes">Interés (%)</label>
                <input type="text" class="form-control" name="tasa_interes" id="tasa_interes" value="0" placeholder="Ej: 16">
            </div>
            <div class="col-md-6 mb-2">
                <label for="comision_desembolso">Desembolso (%)</label>
                <input type="text" class="form-control" name="comision_desembolso" id="comision_desembolso" value="0" placeholder="Ej: 7">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label id="label_plazo_credito" data-original="Plazo del crédito (en meses)">Plazo del crédito (en meses)</label>
                <input type="number" min="0" step="1" class="form-control" name="plazo_credito" id="plazo_credito" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_numero_cuotas" data-original="No. de cuotas">No. de cuotas</label>
                <input type="number" min="0" step="1" class="form-control" name="numero_cuotas" id="numero_cuotas" value="0">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_monto_cuota" data-original="Monto de cada cuota (US$)">Monto de cada cuota (US$)</label>
                <input type="number" min="0" step="any" class="form-control" name="monto_cuota" id="monto_cuota" value="0">
                <input type="hidden" name="cuota_estim_estimada" id="cuota_estim_estimada" value="">
                <input type="hidden" name="cuota_estim_estimada_quincenal" id="cuota_estim_estimada_quincenal" value="">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_fecha_pago_cuota" data-original="Fecha de pago de cada cuota">Fecha de pago de cada cuota</label>
                <input type="text" class="form-control" name="fecha_pago_cuota" id="fecha_pago_cuota" placeholder="Ej: 15 de cada mes">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_frecuencia_pago" data-original="Frecuencia de pago de cada cuota">Frecuencia de pago de cada cuota</label>
                <select class="form-control" name="frecuencia_pago" id="frecuencia_pago">
                    <option value="quincenal" selected>Quincenal</option>
                    <option value="catorcenal">Catorcenal</option>
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_forma_pago" data-original="Forma de pago">Forma de pago</label>
                <input type="text" class="form-control" name="forma_pago" id="forma_pago">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_garantia_requerida" data-original="Garantía requerida">Garantía requerida</label>
                <input type="text" class="form-control" name="garantia_requerida" id="garantia_requerida" placeholder="Detalle de la garantía o garantías requeridas">
            </div>
            <div class="col-md-6 mb-2">
                <label id="label_fundamentacion_propuesta" data-original="Fundamentación de la propuesta">Fundamentación de la propuesta</label>
                <input type="text" class="form-control" name="fundamentacion_propuesta" id="fundamentacion_propuesta" placeholder="Justificación y análisis de la recomendación de crédito">
            </div>
            <div class="col-md-12 mb-2">
                <label id="label_comentario" data-original="Comentario">Comentario</label>
                <textarea class="form-control" name="comentario" id="comentario" rows="4" placeholder="Agregar comentarios adicionales sobre el análisis financiero"></textarea>
            </div>
        </div>
    </div>
    `;
    $('#campos_dinamicos').html(html);
    function actualizarNumeroCuotasPorFrecuencia() {
        var frecuencia = ($('#frecuencia_pago').val() || 'quincenal').toLowerCase();
        $('#frecuencia_pago').val(frecuencia);
        var plazo = parseFloat($('#plazo_credito').val()) || 0;

        // Mantener compatibilidad con la lógica previa: si 'quincenal' o 'catorcenal'
        // usamos el mismo cálculo base (plazo*2) para `numero_cuotas`.
        var cuotas = 0;
        if (plazo > 0) {
            if (frecuencia === 'quincenal' || frecuencia === 'catorcenal') {
                cuotas = plazo * 2;
            } else {
                cuotas = plazo;
            }
        }
        $('#numero_cuotas').val(cuotas > 0 ? Math.round(cuotas) : 0);

        // Nota: no se modifica `monto_cuota` aquí — el monto se mantiene
        // independientemente de la frecuencia, tal como pidió el usuario.
    }
    $('#frecuencia_pago').on('change', actualizarNumeroCuotasPorFrecuencia);
    $('#plazo_credito').on('input', actualizarNumeroCuotasPorFrecuencia);
    actualizarNumeroCuotasPorFrecuencia();
    calcularSumas();
    function calcularSubtotalOlp() {
        let total = 0;
        $('.olp-saldo').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#subtotal_olp_saldo').val(total.toFixed(2));
    }
    function calcularSubtotalOcp() {
        let total = 0;
        $('.ocp-saldo').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#subtotal_ocp_saldo').val(total.toFixed(2));
    }
    $('.olp-saldo').on('input', function() {
        calcularSubtotalOlp();
        calcularSumas();
    });
    $('.ocp-saldo').on('input', function() {
        calcularSubtotalOcp();
        calcularSumas();
    });
    calcularSubtotalOlp();
    calcularSubtotalOcp();
    $('.suma-disponible').on('input', calcularSumas);
    $('.suma-inventarios').on('input', calcularSumas);
    $('.suma-activos-fijos').on('input', calcularSumas);
    $('#cuentas_cobrar').on('input', calcularSumas);
    $('.suma-cuentas-pagar').on('input', calcularSumas);
    $('.suma-ventas').on('input', calcularSumas);
    var comercianteSyncing = false;
    function syncComercianteField(source, target) {
        if (comercianteSyncing) {
            return;
        }
        comercianteSyncing = true;
        $(target).val($(source).val());
        comercianteSyncing = false;
    }
    $('#ventas_contado').on('input', function() {
        syncComercianteField(this, '#fcm_ventas_contado');
        calcularSumas();
    });
    $('#fcm_ventas_contado').on('input', function() {
        syncComercianteField(this, '#ventas_contado');
        calcularSumas();
    });
    $('#ventas_credito').on('input', function() {
        syncComercianteField(this, '#fcm_recuperacion_credito');
        calcularSumas();
    });
    $('#fcm_recuperacion_credito').on('input', function() {
        syncComercianteField(this, '#ventas_credito');
        calcularSumas();
    });
    $('#costos_venta').on('input', calcularSumas);
    $('#gastos_generales').on('input', function() {
        syncComercianteField(this, '#fcm_gastos_generales');
        calcularSumas();
    });
    $('#fcm_gastos_generales').on('input', function() {
        syncComercianteField(this, '#gastos_generales');
        calcularSumas();
    });
    $('.suma-fcm').on('input', calcularSumas);
    $('#cuota_periodica').on('input', calcularSumas);
    $('#monto_credito_solicitado').on('input', calcularSumas);
    $('#fcm_valor_canasta_basica').on('input', calcularSumas);
    $('#fcm_cant_personas_dep').on('input', calcularSumas);
    $('#flujo_neto_disponible').on('input', calcularSumas);
    $('#cuota_periodica').on('input', validarCuotaVsFlujo);
}

function calcularSumas() {
    var $ctx = $('#campos_dinamicos');
    if (!$ctx.length) { $ctx = $(document); }
    // Sincronizar COSTOS DE OPERACIÓN DIRECTOS desde GASTOS FIJOS MENSUALES
    let salario_ayudante = parseFloat($ctx.find('#gasto_salario_ayudante').val()) || 0;
    let transporte = parseFloat($ctx.find('#gasto_transporte').val()) || 0;
    $ctx.find('#costo_salario_ayudante').val(salario_ayudante.toFixed(2));
    $ctx.find('#costo_transporte').val(transporte.toFixed(2));
    let total_costos_operacion = salario_ayudante + transporte;
    $ctx.find('#costo_total_operacion').val(total_costos_operacion.toFixed(2));

    // Disponible
    let efectivo = parseFloat($ctx.find('#efectivo_caja').val()) || 0;
    let banco = parseFloat($ctx.find('#dinero_banco').val()) || 0;
    let total_disponible = efectivo + banco;
    $ctx.find('#total_disponible').val(total_disponible);
    // Inventarios
    let inv1 = parseFloat($ctx.find('#inventario_mercaderia').val()) || 0;
    let inv2 = parseFloat($ctx.find('#productos_proceso').val()) || 0;
    let inv3 = parseFloat($ctx.find('#productos_terminados').val()) || 0;
    let total_inventarios = inv1 + inv2 + inv3;
    $ctx.find('#total_inventarios').val(total_inventarios);

    // GASTOS FIJOS MENSUALES
    let total_gastos_fijos = 0;
    $ctx.find('.suma-gastos-fijos').each(function() {
        total_gastos_fijos += parseFloat($(this).val()) || 0;
    });
    $ctx.find('#total_gastos_fijos').val(total_gastos_fijos.toFixed(2));
    // Activos fijos
    let af1 = parseFloat($ctx.find('#bienes_muebles').val()) || 0;
    let af2 = parseFloat($ctx.find('#propiedades').val()) || 0;
    let af3 = parseFloat($ctx.find('#otros_activos').val()) || 0;
    let total_activos_fijos = af1 + af2 + af3;
    $ctx.find('#total_activos_fijos').val(total_activos_fijos);
    // Total activos
    let cuentas_cobrar = parseFloat($ctx.find('#cuentas_cobrar').val()) || 0;
    let total_activos = total_disponible + total_inventarios + total_activos_fijos + cuentas_cobrar;
    $ctx.find('#total_activos').val(total_activos);
    // Pasivos
    let pagar1 = parseFloat($ctx.find('#cuentas_pagar_proveedores').val()) || 0;
    let pagar2 = parseFloat($ctx.find('#cuentas_pagar_credito').val()) || 0;
    let pasivo_no_corriente = parseFloat($ctx.find('#pasivo_no_corriente').val()) || 0;
    let total_pasivo = pagar1 + pagar2 + pasivo_no_corriente;
    $ctx.find('#total_pasivo').val(total_pasivo);
    // Patrimonio
    let total_patrimonio = total_activos - total_pasivo;
    $ctx.find('#total_patrimonio').val(total_patrimonio);
    // Pasivo + Patrimonio
    let total_pasivo_patrimonio = total_pasivo + total_patrimonio;
    $ctx.find('#total_pasivo_patrimonio').val(total_pasivo_patrimonio);

    // INDICADORES
    // Nivel de Endeudamiento = (Total Pasivo + Monto Crédito Solicitado) / Total Activos
    let monto_credito = parseFloat($ctx.find('#monto_credito_solicitado').val()) || 0;
    let indicador_endeudamiento = total_activos > 0 ? ((total_pasivo + monto_credito) / total_activos) : 0;
    $ctx.find('#indicador_endeudamiento').val((indicador_endeudamiento * 100).toFixed(1) + ' %');
    // Capital de trabajo Neto (Activo Corriente – Pasivo Corriente)
    let activo_corriente = total_disponible + total_inventarios + cuentas_cobrar;
    let pasivo_corriente = pagar1 + pagar2;
    let capital_trabajo_neto = activo_corriente - pasivo_corriente;
    $ctx.find('#capital_trabajo_neto').val(capital_trabajo_neto);
    // Cobertura de la deuda capacidad de pago = (Cuota / Flujo neto disponible)
    let cuota = parseFloat($ctx.find('#cuota_periodica').val()) || 0;
    let total_olp = 0;
    $ctx.find('.olp-saldo').each(function() {
        total_olp += parseFloat($(this).val()) || 0;
    });
    $ctx.find('#subtotal_olp_saldo').val(total_olp.toFixed(2));
    let total_ocp = 0;
    $ctx.find('.ocp-saldo').each(function() {
        total_ocp += parseFloat($(this).val()) || 0;
    });
    $ctx.find('#subtotal_ocp_saldo').val(total_ocp.toFixed(2));
    let cobertura_deuda = null;
    // FLUJO DE CAJA MENSUAL (1+2-3-4+5-6-7)
    let fcm_ventas_contado = parseFloat($ctx.find('#fcm_ventas_contado').val()) || 0; // 1
    let fcm_recuperacion_credito = parseFloat($ctx.find('#fcm_recuperacion_credito').val()) || 0; // 2
    let fcm_compras_contado = parseFloat($ctx.find('#fcm_compras_contado').val()) || 0; // 3
    let fcm_gastos_generales = parseFloat($ctx.find('#fcm_gastos_generales').val()) || 0; // 4
    let fcm_otros_ingresos = parseFloat($ctx.find('#fcm_otros_ingresos').val()) || 0; // 5
    // Cálculo automático de Gastos consumo familiar (6)
    let fcm_valor_canasta_basica = parseFloat($ctx.find('#fcm_valor_canasta_basica').val()) || 0;
    let fcm_cant_personas_dep = parseFloat($ctx.find('#fcm_cant_personas_dep').val()) || 0;
    let fcm_gastos_consumo = 0;
    if (fcm_valor_canasta_basica > 0 && fcm_cant_personas_dep > 0) {
        fcm_gastos_consumo = (fcm_valor_canasta_basica / 6) * fcm_cant_personas_dep;
        $ctx.find('#fcm_gastos_consumo').val(fcm_gastos_consumo.toFixed(2));
    } else {
        fcm_gastos_consumo = parseFloat($ctx.find('#fcm_gastos_consumo').val()) || 0;
    }
    let fcm_otros_gastos = parseFloat($ctx.find('#fcm_otros_gastos').val()) || 0; // 7

    // Sincronizar valores entre ESTADO DE RESULTADO MENSUAL y FLUJO DE CAJA MENSUAL
    let ventas_contado_val = parseFloat($ctx.find('#ventas_contado').val()) || 0;
    let fcm_ventas_contado_val = parseFloat($ctx.find('#fcm_ventas_contado').val()) || 0;
    let ventas_contado_sync = ventas_contado_val || fcm_ventas_contado_val;

    let ventas_credito_val = parseFloat($ctx.find('#ventas_credito').val()) || 0;
    let fcm_recuperacion_credito_val = parseFloat($ctx.find('#fcm_recuperacion_credito').val()) || 0;
    let ventas_credito_sync = ventas_credito_val || fcm_recuperacion_credito_val;

    let gastos_generales_val = parseFloat($ctx.find('#gastos_generales').val()) || 0;
    let fcm_gastos_generales_val = parseFloat($ctx.find('#fcm_gastos_generales').val()) || 0;
    let gastos_generales_sync = gastos_generales_val || fcm_gastos_generales_val;

    // No formatear durante la edición: preservamos el valor ingresado.
    fcm_ventas_contado = ventas_contado_sync;
    fcm_recuperacion_credito = ventas_credito_sync;
    fcm_gastos_generales = gastos_generales_sync;

    // Calcular Flujo del negocio (1+2-3-4)
    let flujo_negocio = fcm_ventas_contado + fcm_recuperacion_credito - fcm_compras_contado - fcm_gastos_generales;
    $ctx.find('#flujo_negocio').val(flujo_negocio.toFixed(2));

    let flujo_neto_disponible = fcm_ventas_contado + fcm_recuperacion_credito - fcm_compras_contado - fcm_gastos_generales + fcm_otros_ingresos - fcm_gastos_consumo - fcm_otros_gastos;
    $ctx.find('#flujo_neto_disponible').val(flujo_neto_disponible);
    // Siempre mostrar el resultado de la fórmula, aunque sea 0 o negativo
    if (flujo_neto_disponible !== 0) {
        cobertura_deuda = (cuota / flujo_neto_disponible) * 100;
    } else {
        cobertura_deuda = 0;
    }
    $ctx.find('#cobertura_deuda').val(cobertura_deuda.toFixed(1) + ' %');

    // ESTADO DE RESULTADO MENSUAL
    // Ventas totales
    let ventas_contado = parseFloat($ctx.find('#ventas_contado').val()) || 0;
    let ventas_credito = parseFloat($ctx.find('#ventas_credito').val()) || 0;
    let ventas_totales = ventas_contado + ventas_credito;
    $ctx.find('#ventas_totales').val(ventas_totales);
    // Margen bruto
    let costos_venta = parseFloat($ctx.find('#costos_venta').val()) || 0;
    let margen_bruto = ventas_totales - costos_venta;
    $ctx.find('#margen_bruto').val(margen_bruto);
    // Utilidad operativa
    let gastos_generales = parseFloat($ctx.find('#gastos_generales').val()) || 0;
    let utilidad_operativa = margen_bruto - gastos_generales;
    $ctx.find('#utilidad_operativa').val(utilidad_operativa);

    validarCuotaVsFlujo();
}

function validarCuotaVsFlujo() {
    var $ctx = $('#campos_dinamicos');
    if (!$ctx.length) { $ctx = $(document); }
    var $cuota = $ctx.find('#cuota_periodica');
    var cuota = parseFloat($cuota.val()) || 0;
    var flujo = parseFloat($ctx.find('#flujo_neto_disponible').val());
    if (isNaN(flujo) || flujo === 0) {
        flujo = parseFloat($ctx.find('#flujo_neto_mensual').val()) || 0;
    } else {
        flujo = flujo || 0;
    }
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

function formatPercentageDisplay(selector) {
    var $el = $(selector);
    if (!$el.length) return;
    var val = $el.val();
    if (typeof val === 'string') {
        val = val.replace('%', '').replace(',', '.').trim();
    }
    var num = parseFloat(val);
    if (isNaN(num)) {
        $el.val('0');
        return;
    }
    if (Math.abs(num) <= 1) {
        num = num * 100;
    }
    var formatted = num.toFixed(2).replace(/\.00$/, '');
    $el.val(formatted);
}

function normalizePercentageForSubmit(selector) {
    var $el = $(selector);
    if (!$el.length) return;
    var val = $el.val();
    if (typeof val === 'string') {
        val = val.replace('%', '').replace(',', '.').trim();
    }
    var num = parseFloat(val);
    if (isNaN(num)) {
        $el.val('0');
        return;
    }
    if (Math.abs(num) > 1) {
        num = num / 100;
    }
    $el.val(num);
}

function processAnalisisFinancieroCuota() {
    var monto = parseFloat($('#monto_financiar').val()) || 0;
    var plazo = parseInt($('#plazo_credito').val()) || 0;
    var tasaRaw = getPercentVal('#tasa_interes');
    var tasa = tasaRaw || 0;
    if (tasa > 0 && tasa <= 1) {
        tasa = tasa * 100;
    }
    var comisionRaw = getPercentVal('#comision_desembolso');
    var comision = comisionRaw || 0;
    if (comision > 0 && comision <= 1) {
        comision = comision * 100;
    }
    var frecuencia = ($('#frecuencia_pago').val() || 'quincenal').toLowerCase();
    // Para la vista de cuota, siempre solicitamos al backend la cuota MENSUAL
    // y luego adaptamos la visualización: si la frecuencia seleccionada
    // es quincenal/catorcenal mostramos cuota_mensual/2.
    var previewFrecuencia = 'mensual';

    if (monto <= 0 || plazo <= 0) {
        alert('Ingrese monto a financiar y plazo del crédito antes de procesar cuota.');
        return;
    }

    // El endpoint espera tasa y comisión en porcentaje y frecuencia quincenal/catorcenal/mensual
    var payload = {
        monto: monto,
        tasa: tasa,
        comision: comision,
        comision_desembolso: comision,
        plazo: plazo,
        frecuencia: previewFrecuencia,
        fecha_inicio: null
    };

    $.post(base_url + 'prestamo/generate_preview_ajax', payload, function(resp) {
        if (!resp || !resp.status) {
            alert('No se pudo calcular la cuota. Verifique los datos.');
            return;
        }
        var cuotaMensual = 0;
        if (resp.schedule && Array.isArray(resp.schedule) && resp.schedule.length > 0) {
            cuotaMensual = parseFloat(resp.schedule[0].cuota) || 0;
        } else if (typeof resp.payment !== 'undefined') {
            var pago = parseFloat(resp.payment) || 0;
            var comi = parseFloat(resp.commission_per_period) || 0;
            cuotaMensual = pago + comi;
        }
        var cuota = cuotaMensual;
        // Si la frecuencia seleccionada es quincenal/catorcenal, mostramos la
        // cuota como la cuota mensual dividida entre 2.
        if (frecuencia === 'quincenal' || frecuencia === 'catorcenal') {
            cuota = cuotaMensual / 2.0;
        }
        if (cuota <= 0) {
            alert('No se recibió un valor válido de cuota.');
            return;
        }
        // Registrar monto de cuota y usar plazo ingresado
        var displayVal = (Math.round(cuota * 100) / 100).toFixed(2);
        $('#campos_dinamicos').find('#monto_cuota').val(displayVal);
        updateCuotaPeriodicaFromMontoCuota();
        $('#plazo_credito').val(plazo);
        actualizarNumeroCuotasPorFrecuencia();
        if (typeof calcularFlujoNetoMensual === 'function') {
            calcularFlujoNetoMensual();
        }
        calcularCoberturaDeuda();
        actualizarLabelCampo('monto_cuota');
        actualizarLabelCampo('plazo_credito');
        actualizarLabelCampo('numero_cuotas');

        // Guardar valores ocultos para referencia si se desea
        $('#cuota_estim_estimada').val((Math.round(cuotaMensual * 100) / 100).toFixed(2));
        $('#cuota_estim_estimada_quincenal').val((Math.round((cuotaMensual/2.0) * 100) / 100).toFixed(2));
    }, 'json').fail(function() {
        alert('Error en la comunicación al calcular la cuota.');
    });
}

$(document).on('click', '#btn_procesar_cuota_analisis', function() {
    processAnalisisFinancieroCuota();
});

$(document).on('input', '#campos_dinamicos #monto_cuota', function() {
    updateCuotaPeriodicaFromMontoCuota();
});

// Round numeric inputs with fractional step to 2 decimals on blur
$(document).on('blur', 'input[step="any"]', function() {
    var $el = $(this);
    var val = $el.val();
    if (val === null || val === '') return;
    var num = parseFloat(val.toString().replace(',', '.'));
    if (isNaN(num)) return;
    $el.val(num.toFixed(2));
});

function updateCuotaPeriodicaFromMontoCuota() {
    var $ctx = $('#campos_dinamicos');
    if (!$ctx.length) { $ctx = $(document); }
    var displayVal = $ctx.find('#monto_cuota').val();
    if (displayVal === null || displayVal === '') return;
    var montoCuota = parseFloat(displayVal.toString().replace(',', '.')) || 0;
    if (montoCuota <= 0) return;

    // Según el comentario en la UI, la cuota periódica en C$ es cuota mensual USD x 36.6243.
    // Si `monto_cuota` muestra cuota quincenal/catorcenal, la conversión usa la cuota mensual base.
    var frecuencia = ($ctx.find('#frecuencia_pago').val() || 'quincenal').toLowerCase();
    var cuotaMensual = montoCuota;
    if (frecuencia === 'quincenal' || frecuencia === 'catorcenal') {
        cuotaMensual = montoCuota * 2.0;
    }

    var tasaCambio = 36.6243;
    var cuotaCordobas = cuotaMensual * tasaCambio;
    $ctx.find('#cuota_periodica').val((Math.round(cuotaCordobas * 100) / 100).toFixed(2));
    if ($ctx.find('#flujo_neto_mensual').length) {
        if (typeof calcularFlujoNetoMensual === 'function') {
            calcularFlujoNetoMensual();
        }
        calcularCoberturaDeuda();
    } else {
        calcularSumas();
    }
    validarCuotaVsFlujo();
}
</script>

