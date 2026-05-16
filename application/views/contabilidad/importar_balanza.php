<!-- Vista para importar balanza de comprobación -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-file-upload"></i> Importar Balanza de Comprobación</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Instrucciones:</h5>
                        <ol>
                            <li>Prepare un archivo <strong>Excel (.xlsx) o CSV</strong> con las siguientes columnas:
                                <ul>
                                    <li><strong>Código:</strong> Código de cuenta (ej: 11010101201)</li>
                                    <li><strong>Denominación:</strong> Nombre de la cuenta</li>
                                    <li><strong>Saldo Anterior:</strong> Saldo inicial (opcional)</li>
                                    <li><strong>Cargos:</strong> Total de cargos en el período</li>
                                    <li><strong>Abonos:</strong> Total de abonos en el período</li>
                                    <li><strong>Saldo Actual:</strong> Saldo final</li>
                                </ul>
                            </li>
                            <li>Las cuentas se clasificarán automáticamente según su código:
                                <ul>
                                    <li><strong>1XXXX:</strong> Activo</li>
                                    <li><strong>2XXXX:</strong> Pasivo</li>
                                    <li><strong>3XXXX:</strong> Patrimonio</li>
                                    <li><strong>4XXXX:</strong> Ingresos</li>
                                    <li><strong>5XXXX:</strong> Gastos</li>
                                </ul>
                            </li>
                            <li>Se creará automáticamente un <strong>asiento de apertura</strong> con los saldos iniciales</li>
                        </ol>
                    </div>

                    <!-- Paso 1: Subir archivo -->
                    <div id="step1" class="import-step">
                        <h5 class="mb-3">Paso 1: Subir Archivo</h5>
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="balanzaFile">Seleccione el archivo de balanza:</label>
                                <input type="file" class="form-control-file" id="balanzaFile" name="balanzaFile" 
                                       accept=".xlsx,.xls,.csv" required>
                                <small class="form-text text-muted">Formatos aceptados: .xlsx, .xls, .csv</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="periodoMes">Período (Mes):</label>
                                        <select class="form-control" id="periodoMes" name="periodoMes" required>
                                            <option value="01">Enero</option>
                                            <option value="02">Febrero</option>
                                            <option value="03">Marzo</option>
                                            <option value="04">Abril</option>
                                            <option value="05">Mayo</option>
                                            <option value="06">Junio</option>
                                            <option value="07">Julio</option>
                                            <option value="08">Agosto</option>
                                            <option value="09">Septiembre</option>
                                            <option value="10">Octubre</option>
                                            <option value="11">Noviembre</option>
                                            <option value="12">Diciembre</option>
                                        </select>
                                        <small class="form-text text-muted">Mes al que corresponde esta balanza</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="periodoAnio">Año:</label>
                                        <input type="number" class="form-control" id="periodoAnio" name="periodoAnio" 
                                               value="<?php echo date('Y'); ?>" min="2020" max="2030" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="fechaAsiento">Fecha del Asiento:</label>
                                <input type="date" class="form-control" id="fechaAsiento" name="fechaAsiento" 
                                       value="<?php echo date('Y-m-d', strtotime('last day of last month')); ?>" required>
                                <small class="form-text text-muted">Se recomienda último día del mes (para cierre mensual)</small>
                            </div>

                            <div class="form-group">
                                <label for="tipoImportacion">Tipo de Importación:</label>
                                <select class="form-control" id="tipoImportacion" name="tipoImportacion" required>
                                    <option value="apertura">Asiento de Apertura (Primera vez)</option>
                                    <option value="cierre">Cierre Mensual</option>
                                    <option value="ajuste">Ajuste de Saldos</option>
                                </select>
                                <small class="form-text text-muted">Seleccione el tipo de asiento a generar</small>
                            </div>

                            <div class="form-group">
                                <label for="descripcionAsiento">Descripción del Asiento:</label>
                                <input type="text" class="form-control" id="descripcionAsiento" name="descripcionAsiento" 
                                       value="" placeholder="Se generará automáticamente" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload"></i> Subir y Analizar
                            </button>
                        </form>
                    </div>

                    <!-- Paso 2: Vista previa y confirmación -->
                    <div id="step2" class="import-step" style="display:none;">
                        <h5 class="mb-3">Paso 2: Vista Previa de Importación</h5>
                        
                        <div class="alert alert-info">
                            <strong>Período:</strong> <span id="previewPeriodo"></span><br>
                            <strong>Tipo:</strong> <span id="previewTipo"></span>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Revise los datos antes de importar.</strong> 
                            Se crearán/actualizarán <span id="totalCuentas">0</span> cuentas y se generará 1 asiento contable.
                        </div>

                        <!-- Resumen de cuentas por tipo -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6>Resumen por Tipo de Cuenta:</h6>
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Cantidad</th>
                                            <th>Total Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resumenTipos">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabla de vista previa -->
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-striped table-hover" id="previewTable">
                                <thead class="thead-dark" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th class="text-right">Saldo Inicial</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                </tbody>
                            </table>
                        </div>

                        <!-- Verificación de cuadre -->
                        <div class="card mt-3" id="cuadreCard">
                            <div class="card-body">
                                <h6>Verificación del Asiento de Apertura:</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Total Debe (Activo + Gastos):</strong></td>
                                        <td class="text-right" id="totalDebe">0.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Haber (Pasivo + Patrimonio + Ingresos):</strong></td>
                                        <td class="text-right" id="totalHaber">0.00</td>
                                    </tr>
                                    <tr class="font-weight-bold" id="diferencia">
                                        <td>Diferencia:</td>
                                        <td class="text-right" id="diferenciaValor">0.00</td>
                                    </tr>
                                </table>
                                <div id="cuadreAlert"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" onclick="resetImport()">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="btnConfirmarImport">
                                <i class="fas fa-check"></i> Confirmar e Importar
                            </button>
                        </div>
                    </div>

                    <!-- Paso 3: Resultados -->
                    <div id="step3" class="import-step" style="display:none;">
                        <h5 class="mb-3">Paso 3: Resultados de la Importación</h5>
                        <div id="resultadosImport"></div>
                        <div class="mt-3">
                            <a href="<?php echo site_url('contabilidad/catalogo'); ?>" class="btn btn-primary">
                                <i class="fas fa-book"></i> Ver Catálogo de Cuentas
                            </a>
                            <a href="<?php echo site_url('contabilidad/diario'); ?>" class="btn btn-info">
                                <i class="fas fa-book-open"></i> Ver Libro Diario
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="resetImport()">
                                <i class="fas fa-redo"></i> Nueva Importación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .import-step {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .table-sm th, .table-sm td {
        padding: 0.3rem;
        font-size: 0.9rem;
    }
    #previewTable tbody tr:hover {
        background-color: #f0f8ff !important;
    }
</style>

<script>
let previewData = [];

$(document).ready(function() {
    console.log('=== Sistema de Importación de Balanza ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Formulario encontrado:', $('#uploadForm').length);
    
    // Verificar si toastr está disponible
    if (typeof toastr === 'undefined') {
        console.warn('⚠️ Toastr no está cargado, se usarán alerts');
    }
    
    // Auto-generar descripción basada en período y tipo
    function actualizarDescripcion() {
        const mes = $('#periodoMes option:selected').text();
        const anio = $('#periodoAnio').val();
        const tipo = $('#tipoImportacion').val();
        
        let descripcion = '';
        if (tipo === 'apertura') {
            descripcion = `Asiento de Apertura - Saldos Iniciales ${mes} ${anio}`;
        } else if (tipo === 'cierre') {
            descripcion = `Cierre Mensual - ${mes} ${anio}`;
        } else {
            descripcion = `Ajuste de Saldos - ${mes} ${anio}`;
        }
        
        $('#descripcionAsiento').val(descripcion);
        
        // Auto-actualizar fecha del asiento al último día del mes seleccionado
        const mesNum = $('#periodoMes').val();
        const ultimoDia = new Date(anio, mesNum, 0).getDate();
        const fechaSugerida = `${anio}-${mesNum}-${ultimoDia.toString().padStart(2, '0')}`;
        $('#fechaAsiento').val(fechaSugerida);
    }
    
    // Actualizar descripción al cambiar período o tipo
    $('#periodoMes, #periodoAnio, #tipoImportacion').on('change', actualizarDescripcion);
    
    // Setear mes actual
    const mesActual = '<?php echo date("m"); ?>';
    $('#periodoMes').val(mesActual);
    
    // Generar descripción inicial
    actualizarDescripcion();
    
    // Handle form upload
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Formulario enviado');
        
        const fileInput = document.getElementById('balanzaFile');
        if (!fileInput.files.length) {
            alert('Por favor seleccione un archivo');
            if (typeof toastr !== 'undefined') {
                toastr.error('Por favor seleccione un archivo');
            }
            return;
        }

        const formData = new FormData(this);
        
        console.log('Enviando archivo:', fileInput.files[0].name);
        console.log('Período:', $('#periodoMes').val() + '/' + $('#periodoAnio').val());
        
        // Show loading
        if (typeof toastr !== 'undefined') {
            toastr.info('Procesando archivo...', 'Por favor espere');
        } else {
            alert('Procesando archivo...');
        }
        
        // Deshabilitar botón para evitar doble clic
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: '<?php echo base_url("importar_balanza_directo.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta recibida:', response);
                
                btnSubmit.prop('disabled', false).html('<i class="fas fa-upload"></i> Subir y Analizar');
                
                if (response.status === 'success') {
                    previewData = response.data;
                    mostrarVistaPreviaStep2(response.data);
                    $('#step1').hide();
                    $('#step2').show();
                } else {
                    const errorMsg = response.message || 'Error al procesar el archivo';
                    console.error('Error:', errorMsg);
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        alert('ERROR: ' + errorMsg);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', xhr, status, error);
                console.error('Response Text:', xhr.responseText);
                
                btnSubmit.prop('disabled', false).html('<i class="fas fa-upload"></i> Subir y Analizar');
                
                let errorMsg = 'Error al subir el archivo';
                if (xhr.responseText) {
                    try {
                        const err = JSON.parse(xhr.responseText);
                        errorMsg = err.message || errorMsg;
                    } catch(e) {
                        errorMsg = 'Error del servidor: ' + xhr.status;
                    }
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert('ERROR: ' + errorMsg);
                }
            }
        });
    });

    // Confirmar importación
    $('#btnConfirmarImport').on('click', function() {
        if (!previewData || !previewData.cuentas || previewData.cuentas.length === 0) {
            toastr.error('No hay datos para importar');
            return;
        }

        // Verificar cuadre
        const totalDebe = parseFloat($('#totalDebe').text().replace(/,/g, ''));
        const totalHaber = parseFloat($('#totalHaber').text().replace(/,/g, ''));
        const diferencia = Math.abs(totalDebe - totalHaber);

        if (diferencia > 0.01) {
            if (!confirm('ADVERTENCIA: El asiento no cuadra. Diferencia: ' + diferencia.toFixed(2) + '\n\n¿Desea continuar de todos modos?')) {
                return;
            }
        }

        const btnConfirmar = $(this);
        btnConfirmar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importando...');

        $.ajax({
            url: '<?php echo base_url("importar_balanza_confirmar.php"); ?>',
            type: 'POST',
            data: JSON.stringify({
                cuentas: previewData.cuentas,
                asiento: {
                    fecha: $('#fechaAsiento').val(),
                    descripcion: $('#descripcionAsiento').val(),
                    periodo_mes: previewData.periodo.mes,
                    periodo_anio: previewData.periodo.anio,
                    tipo: previewData.periodo.tipo
                },
                periodo: previewData.periodo || {
                    mes: $('#periodoMes').val(),
                    anio: $('#periodoAnio').val()
                }
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    mostrarResultadosStep3(response);
                    $('#step2').hide();
                    $('#step3').show();
                } else {
                    toastr.error(response.message || 'Error al importar');
                    btnConfirmar.prop('disabled', false).html('<i class="fas fa-check"></i> Confirmar e Importar');
                }
            },
            error: function(xhr) {
                toastr.error('Error al importar los datos');
                console.error(xhr);
                btnConfirmar.prop('disabled', false).html('<i class="fas fa-check"></i> Confirmar e Importar');
            }
        });
    });
});

function mostrarVistaPreviaStep2(data) {
    const cuentas = data.cuentas || [];
    const resumen = data.resumen || {};
    
    $('#totalCuentas').text(cuentas.length);
    
    // Mostrar información del período
    const periodo = data.periodo || {};
    $('#previewPeriodo').text(`${periodo.mes_nombre} ${periodo.anio}`);
    $('#previewTipo').text(periodo.tipo_nombre || 'No especificado');
    
    // Resumen por tipos
    let resumenHtml = '';
    ['activo', 'pasivo', 'patrimonio', 'ingreso', 'gasto'].forEach(tipo => {
        if (resumen[tipo]) {
            resumenHtml += `
                <tr>
                    <td class="text-capitalize"><strong>${tipo}</strong></td>
                    <td>${resumen[tipo].cantidad}</td>
                    <td class="text-right">${formatMoney(resumen[tipo].total)}</td>
                </tr>
            `;
        }
    });
    $('#resumenTipos').html(resumenHtml);
    
    // Tabla de cuentas
    let tableHtml = '';
    cuentas.forEach(cuenta => {
        const badgeClass = cuenta.existe ? 'badge-warning' : 'badge-success';
        const estadoText = cuenta.existe ? 'Ya existe (actualizará)' : 'Nueva';
        tableHtml += `
            <tr>
                <td><code>${cuenta.code}</code></td>
                <td>${cuenta.name}</td>
                <td><span class="badge badge-info">${cuenta.type}</span></td>
                <td class="text-right"><strong>${formatMoney(cuenta.saldo_actual)}</strong></td>
                <td><span class="badge ${badgeClass}">${estadoText}</span></td>
            </tr>
        `;
    });
    $('#previewTableBody').html(tableHtml);
    
    // Cuadre
    const totalDebe = data.cuadre.total_debe || 0;
    const totalHaber = data.cuadre.total_haber || 0;
    const diferencia = Math.abs(totalDebe - totalHaber);
    
    $('#totalDebe').text(formatMoney(totalDebe));
    $('#totalHaber').text(formatMoney(totalHaber));
    $('#diferenciaValor').text(formatMoney(diferencia));
    
    if (diferencia < 0.01) {
        $('#diferencia').removeClass('text-danger').addClass('text-success');
        $('#cuadreAlert').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> El asiento cuadra correctamente</div>');
    } else {
        $('#diferencia').removeClass('text-success').addClass('text-danger');
        $('#cuadreAlert').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <strong>ADVERTENCIA:</strong> El asiento NO cuadra. Por favor revise los saldos.</div>');
    }
}

function mostrarResultadosStep3(response) {
    let html = '<div class="alert alert-success"><h5><i class="fas fa-check-circle"></i> Importación Completada</h5></div>';
    
    if (response.periodo) {
        html += `<div class="alert alert-info"><strong>Período importado:</strong> ${response.periodo}</div>`;
    }
    
    html += '<div class="row">';
    html += `<div class="col-md-3"><div class="card bg-light"><div class="card-body text-center">`;
    html += `<h3 class="text-primary">${response.cuentas_creadas || 0}</h3>`;
    html += `<p>Cuentas Creadas</p></div></div></div>`;
    
    html += `<div class="col-md-3"><div class="card bg-light"><div class="card-body text-center">`;
    html += `<h3 class="text-info">${response.cuentas_actualizadas || 0}</h3>`;
    html += `<p>Cuentas Actualizadas</p></div></div></div>`;
    
    html += `<div class="col-md-3"><div class="card bg-light"><div class="card-body text-center">`;
    html += `<h3 class="text-success">${response.asiento_id || 'N/A'}</h3>`;
    html += `<p>ID Asiento Generado</p></div></div></div>`;
    
    html += `<div class="col-md-3"><div class="card bg-light"><div class="card-body text-center">`;
    html += `<h3 class="text-dark">${response.total_cuentas || 0}</h3>`;
    html += `<p>Total Procesadas</p></div></div></div>`;
    html += '</div>';
    
    if (response.errores && response.errores.length > 0) {
        html += '<div class="alert alert-warning mt-3"><h6>Advertencias:</h6><ul>';
        response.errores.forEach(err => {
            html += `<li>${err}</li>`;
        });
        html += '</ul></div>';
    }
    
    html += '<div class="alert alert-light mt-3">';
    html += '<p><strong>Nota importante:</strong> Este asiento se ha generado de forma independiente. ';
    html += 'Puede cargar balanzas de otros meses y cada una generará su propio asiento.</p>';
    html += '</div>';
    
    $('#resultadosImport').html(html);
}

function formatMoney(amount) {
    return new Intl.NumberFormat('es-NI', { 
        style: 'decimal', 
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function resetImport() {
    $('#step1').show();
    $('#step2, #step3').hide();
    $('#uploadForm')[0].reset();
    previewData = [];
}
</script>
