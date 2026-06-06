<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid mt-3">

            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <a class="btn bg-blue text-white float-right" href="<?php echo base_url('solicitudes'); ?>">Volver a Solicitudes</a>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <h4>Crear Crédito desde Aprobación</h4>
                            <p class="text-muted">Selecciona una solicitud con propuestas aprobadas y genera el calendario.</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <button id="btnLoadPropuestasTop" class="btn btn-outline-primary mt-2" style="display:none">Recargar propuestas</button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Solicitud</label>
                            <select id="idsolicitud" class="form-control">
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($solicitudes as $s): ?>
                                    <option value="<?php echo $s->idsolicitud; ?>">#<?php echo $s->idsolicitud; ?> - <?php echo htmlspecialchars(isset($s->nombre_solicitante) ? $s->nombre_solicitante : (isset($s->nombres) ? $s->nombres : 'Sin nombre')); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6 align-self-end text-right">
                            <button id="btnLoadPropuestas" class="btn btn-primary">Cargar Aprobaciones</button>
                        </div>
                    </div>

                    <hr />
                    <h5>Aprobaciones persistidas</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-compact" id="propuestas_table">
                            <thead>
                                <tr><th>ID</th><th>Monto</th><th>Tasa</th><th>Plazo</th><th>Comisión</th><th>Acción</th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <hr />
                    <h5>Detalle de Solicitud</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Interés mensual (%)</label>
                                    <input id="interes_mensual" class="form-control" readonly />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Interés corriente anual (%)</label>
                                    <input id="interes_corriente_anual" class="form-control" readonly />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Interés moratorio (%)</label>
                                    <input id="interes_moratorio" class="form-control" readonly />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Cobrador (Asesor)</label>
                                    <select id="cobrador" class="form-control"><option value="">-- Seleccione --</option></select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Promotor</label>
                                    <input id="promotor" class="form-control" value="<?php echo isset($nombre_usuario) ? htmlspecialchars($nombre_usuario) : ''; ?>" />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Tipo de cuota</label>
                                    <select id="tipo_cuota" class="form-control"><option value="nivelada">Nivelada</option></select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Fecha desembolso</label>
                                    <input id="fecha_desembolso" type="date" class="form-control" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>1er día de pago</label>
                                    <input id="primer_dia_pago" type="date" class="form-control" />
                                    <small class="form-text text-muted">A partir de esta fecha comenzará a correrse el calendario de las cuotas.</small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Monto</label>
                                    <input id="saldo_inicial" class="form-control" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />
                    <h5>Generar Plan de Pago</h5>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Monto</label>
                            <input id="monto" class="form-control bg-light" readonly tabindex="-1" />
                        </div>
                        <div class="form-group col-md-2">
                            <label>Tasa mensual (%)</label>
                            <input id="tasa" class="form-control bg-light" readonly tabindex="-1" />
                        </div>
                        <div class="form-group col-md-2">
                            <label>Plazo (meses)</label>
                            <input id="plazo" type="number" class="form-control" placeholder="Ej: 10" />
                            <small id="plazo_info" class="form-text text-muted" style="display:none;"></small>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Frecuencia</label>
                            <select id="frecuencia" class="form-control">
                                <option value="diario">Diario</option>
                                <option value="semanal">Semanal</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="catorcenal">Catorcenal (cada 14 días)</option>
                                <option value="mensual">Mensual</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2" id="dia_container" style="display:none;">
                            <label>Día (mensual)</label>
                            <input id="dia_pago" type="number" min="1" max="31" class="form-control" placeholder="3" />
                        </div>
                        <div class="form-group col-md-2" id="dia_semana_container" style="display:none;">
                            <label>Día de la semana</label>
                            <select id="dia_semana" class="form-control">
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miércoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sábado</option>
                                <option value="0">Domingo</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="dias_quincena_container" style="display:none;">
                            <label>Días (quincenal)</label>
                            <div class="form-row">
                                <div class="col"><input id="dia1" type="number" min="1" max="31" class="form-control" placeholder="1" /></div>
                                <div class="col"><input id="dia2" type="number" min="1" max="31" class="form-control" placeholder="16" /></div>
                            </div>
                        </div>
                        <div class="form-group col-md-2" id="dia_catorcenal_container" style="display:none;">
                            <label>Primer día de pago</label>
                            <input id="primer_pago_catorcenal" type="date" class="form-control" />
                        </div>
                        <div class="form-group col-md-2">
                            <label>Comisión desembolso (%)</label>
                            <input id="comision" class="form-control bg-light" readonly tabindex="-1" />
                        </div>
                        <div class="form-group col-md-1 align-self-end">
                            <button id="btnPreview" class="btn btn-info">Generar</button>
                        </div>
                    </div>

                                    <div id="preview_container" style="display:none">
                                        <h6>Resumen</h6>
                                        <p>Cuota estimada (sin comisión): <strong id="preview_payment"></strong></p>
                                        <p>Comisión por cuota: <strong id="preview_commission"></strong></p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped" id="schedule_table">
                                                <thead><tr><th>#</th><th>Fecha</th><th>N° días</th><th>Cuota</th><th>Principal</th><th>Interés</th><th>Comisión</th><th>Saldo</th></tr></thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div class="row justify-content-end">
                                            <div class="col-md-6">
                                                <table class="table table-bordered table-sm mb-2" id="totals_table" style="display:none">
                                                    <tbody>
                                                        <tr><th>Total cuotas</th><td id="total_cuotas"></td></tr>
                                                        <tr><th>Total principal</th><td id="total_principal"></td></tr>
                                                        <tr><th>Total interés</th><td id="total_interes"></td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button id="btnSaveCredit" class="btn btn-success">Guardar crédito</button>
                                        </div>
                                    </div>

                    <style>
                        .table-compact td, .table-compact th{ padding: .35rem .6rem; vertical-align: middle; font-size: .9rem; }
                    </style>

                    <script>
                    $(function(){
                        // fetch active feriados for client-side validation
                        var feriadosDates = [];
                        $.getJSON('<?php echo base_url('feriados/list_ajax'); ?>').done(function(resp){
                            if (resp && resp.status && Array.isArray(resp.feriados)) feriadosDates = resp.feriados;
                        }).fail(function(){ feriadosDates = []; });

                        function formatYMD(d){
                            var y = d.getFullYear(); var m = (d.getMonth()+1); var dd = d.getDate();
                            return y + '-' + (m<10?('0'+m):m) + '-' + (dd<10?('0'+dd):dd);
                        }

                        function isFeriado(ymd){
                            return feriadosDates.indexOf(ymd) !== -1;
                        }

                        function candidateDateForDay(baseYmd, day){
                            if (!baseYmd || !day) return null;
                            var parts = baseYmd.split('-'); if (parts.length<3) return null;
                            var y = parseInt(parts[0],10); var m = parseInt(parts[1],10);
                            // build date in same month
                            var maxDay = new Date(y, m, 0).getDate();
                            var dd = Math.min(parseInt(day,10), maxDay);
                            var d = new Date(y, m-1, dd);
                            return formatYMD(d);
                        }

                        var currentPlazoMin = null;
                        var currentPlazoMax = null;

                        function validateChosenDays(){
                            var base = $('#primer_dia_pago').val() || $('#fecha_desembolso').val() || (new Date()).toISOString().slice(0,10);
                            var f = $('#frecuencia').val();
                            if (f === 'mensual'){
                                var dia = $('#dia_pago').val();
                                if (!dia || dia === '') {
                                    return {ok:false, message: 'Por favor ingrese el día de pago mensual (1-31)'};
                                }
                                var diaNum = parseInt(dia);
                                if (isNaN(diaNum) || diaNum < 1 || diaNum > 31) {
                                    return {ok:false, message: 'El día de pago debe estar entre 1 y 31'};
                                }
                                var cand = candidateDateForDay(base, diaNum);
                                if (cand && isFeriado(cand)) return {ok:false, message: 'El día seleccionado ('+diaNum+') coincide con un feriado ('+cand+'). Elija otro día.'};
                            } else if (f === 'quincenal'){
                                var d1 = $('#dia1').val(); var d2 = $('#dia2').val();
                                if (!d1 || d1 === '') {
                                    return {ok:false, message: 'Por favor ingrese el primer día de pago quincenal'};
                                }
                                if (!d2 || d2 === '') {
                                    return {ok:false, message: 'Por favor ingrese el segundo día de pago quincenal'};
                                }
                                if (d1){ var c1 = candidateDateForDay(base, d1); if (c1 && isFeriado(c1)) return {ok:false, message: 'El día 1 seleccionado ('+d1+') coincide con un feriado ('+c1+').'}; }
                                if (d2){ var c2 = candidateDateForDay(base, d2); if (c2 && isFeriado(c2)) return {ok:false, message: 'El día 2 seleccionado ('+d2+') coincide con un feriado ('+c2+').'}; }
                            } else if (f === 'semanal'){
                                var diaSem = $('#dia_semana').val();
                                if (!diaSem && diaSem !== '0') {
                                    return {ok:false, message: 'Por favor seleccione el día de la semana para pagos semanales'};
                                }
                            } else if (f === 'catorcenal'){
                                var primerPago = $('#primer_pago_catorcenal').val();
                                if (!primerPago || primerPago === '') {
                                    return {ok:false, message: 'Por favor seleccione el primer día de pago para el plan catorcenal'};
                                }
                            }
                            return {ok:true};
                        }

                        function validatePlazo(){
                            var plazo = parseInt($('#plazo').val());
                            if (isNaN(plazo) || plazo < 1) return {ok:false, message: 'Ingrese un plazo válido'};
                            if (currentPlazoMin !== null && plazo < currentPlazoMin) {
                                return {ok:false, message: 'El plazo debe ser mínimo ' + currentPlazoMin + ' meses'};
                            }
                            if (currentPlazoMax !== null && plazo > currentPlazoMax) {
                                return {ok:false, message: 'El plazo debe ser máximo ' + currentPlazoMax + ' meses'};
                            }
                            return {ok:true};
                        }

                        // NO mostrar alertas flotantes - solo prevenir acción
                        $('#dia_pago, #dia1, #dia2, #primer_dia_pago, #fecha_desembolso, #frecuencia').on('change', function(){
                            // Silently validate, don't show alerts
                        });
                        // If `idsolicitud` is provided in querystring, preselect and load proposals
                        try{
                            var params = new URLSearchParams(window.location.search);
                            var preloadId = params.get('idsolicitud') || params.get('id');
                            if(preloadId){
                                // set value and trigger load
                                $('#idsolicitud').val(preloadId);
                                // show optional top reload button if hidden
                                $('#btnLoadPropuestasTop').show();
                                // trigger click to load propuestas and populate fields
                                setTimeout(function(){
                                    $('#btnLoadPropuestas').trigger('click');
                                }, 200);
                            }
                        }catch(e){ console && console.warn && console.warn('preload id error', e); }
                        // Llenar asesores al cargar la página
                        function fillAsesoresSelect(selectedId) {
                            $.getJSON('/Crediblamen/asesores/list_json', function(asesoresResp) {
                                var asesores = Array.isArray(asesoresResp) ? asesoresResp : (asesoresResp.asesores || []);
                                var $c = $('#cobrador');
                                $c.html('<option value="">-- Seleccione --</option>');
                                $.each(asesores, function(i,a){
                                    var id = a.idasesor || a.id || a.id_asesor || a.idAsesor || a.idasesor;
                                    var name = a.nombres || a.nombre || a.nombre_asesor || a.nombres;
                                    $c.append('<option value="'+id+'">'+(name||'Asesor '+id)+'</option>');
                                });
                                if (selectedId) $c.val(selectedId);
                            });
                        }

                        // Llenar asesores al cargar la página
                        fillAsesoresSelect();

                        function populateDetalleFromResponse(resp) {
                            if (!resp || !resp.status) return;
                            var sol = resp.solicitud || {};
                            var propuestas = resp.propuestas || [];
                            // Si la solicitud tiene asesor, seleccionarlo
                            if (sol.idasesor) fillAsesoresSelect(sol.idasesor);

                            var tasa = sol.tasa_interes || sol.tasa || null;
                            if (!tasa && propuestas.length>0) {
                                tasa = propuestas[0].tasa || propuestas[0].tasa_mensual || propuestas[0].interes;
                            }
                            if (tasa) {
                                var displayT = parseFloat(tasa);
                                if (!isNaN(displayT) && displayT <= 1) displayT = displayT * 100.0;
                                displayT = Number(displayT.toFixed(4));
                                $('#interes_mensual').val(displayT);
                                $('#tasa').val(displayT);
                            }

                            var saldo = sol.monto_solicitado || sol.monto || sol.monto_aprobado || null;
                            if (!saldo && propuestas.length>0) saldo = propuestas[0].monto || propuestas[0].monto_aprobado;
                            if (saldo) $('#saldo_inicial').val(saldo);

                            if (sol.promotor) $('#promotor').val(sol.promotor);
                            if (sol.fecha_desembolso) $('#fecha_desembolso').val(sol.fecha_desembolso);
                            if (sol.primer_dia_pago) $('#primer_dia_pago').val(sol.primer_dia_pago);

                            computeRatesFromInteres();
                        }

                        function computeRatesFromInteres(){
                            var im = parseFloat($('#interes_mensual').val());
                            if (isNaN(im)) { $('#interes_corriente_anual').val(''); $('#interes_moratorio').val(''); return; }
                            var anual = im * 12;
                            var moratorio = anual / 4.0;
                            $('#interes_corriente_anual').val(Number(anual.toFixed(2)));
                            $('#interes_moratorio').val(Number(moratorio.toFixed(2)));
                        }

                        $('#idsolicitud').on('change', function(){
                            var id = $(this).val();
                            if (!id) return;
                            $.getJSON('<?php echo base_url('prestamo/get_solicitud_ajax'); ?>/' + id, function(resp){
                                if (!resp || !resp.status) { alert('Error cargando solicitud'); return; }
                                populateDetalleFromResponse(resp);
                            });
                        });

                        $('#interes_mensual').on('input', function(){ computeRatesFromInteres(); });

                        $('#btnLoadPropuestas').on('click', function(e){
                            e.preventDefault();
                            var id = $('#idsolicitud').val();
                            if (!id) return alert('Seleccione una solicitud');
                            $('#propuestas_table tbody').html('<tr><td colspan="6">Cargando...</td></tr>');
                            $.getJSON('<?php echo base_url('prestamo/get_propuestas_ajax'); ?>/' + id, function(resp){
                                if (!resp.status) { alert('Error al cargar'); return; }
                                    var rows = '';
                                    if (!resp.propuestas || resp.propuestas.length === 0) rows = '<tr><td colspan="6">No hay propuestas</td></tr>';
                                    else {
                                        // Obtener plazo_min y plazo_max de la primera propuesta
                                        var firstProp = resp.propuestas[0];
                                        currentPlazoMin = firstProp.plazo_min || null;
                                        currentPlazoMax = firstProp.plazo_max || null;
                                        
                                        // Mostrar información de plazo
                                        if (currentPlazoMin || currentPlazoMax) {
                                            var infoText = 'Rango permitido: ';
                                            if (currentPlazoMin && currentPlazoMax) {
                                                infoText += currentPlazoMin + ' - ' + currentPlazoMax + ' meses';
                                            } else if (currentPlazoMin) {
                                                infoText += 'mínimo ' + currentPlazoMin + ' meses';
                                            } else if (currentPlazoMax) {
                                                infoText += 'máximo ' + currentPlazoMax + ' meses';
                                            }
                                            $('#plazo_info').text(infoText).show();
                                            // Actualizar atributos min/max del input
                                            if (currentPlazoMin) $('#plazo').attr('min', currentPlazoMin);
                                            if (currentPlazoMax) $('#plazo').attr('max', currentPlazoMax);
                                        }
                                        
                                        $.each(resp.propuestas, function(i,p){
                                            var rawT = (typeof p.tasa !== 'undefined' ? parseFloat(p.tasa) : null);
                                            var rawC = (typeof p.comision_desembolso !== 'undefined' ? parseFloat(p.comision_desembolso) : null);
                                            var displayT = (rawT === null || isNaN(rawT)) ? '' : (rawT <= 1 ? rawT * 100.0 : rawT);
                                            var displayC = (rawC === null || isNaN(rawC)) ? '' : (rawC <= 1 ? rawC * 100.0 : rawC);
                                            if (displayT !== '') displayT = Number(displayT.toFixed(4));
                                            if (displayC !== '') displayC = Number(displayC.toFixed(4));
                                            rows += '<tr>'+
                                                '<td>'+p.idpropuesta+'</td>'+
                                                '<td>'+p.monto+'</td>'+
                                                '<td>'+displayT+'</td>'+
                                                '<td>'+p.plazo+'</td>'+
                                                '<td>'+displayC+'</td>'+
                                                '<td><button class="btn btn-sm btn-primary use-prop" data-monto="'+p.monto+'" data-tasa="'+displayT+'" data-plazo="'+p.plazo+'" data-comision="'+displayC+'">Usar</button></td>'+
                                                '</tr>';
                                        });
                                    }
                                    $('#propuestas_table tbody').html(rows);
                            });
                        });

                        $(document).on('click', '.use-prop', function(){
                            var btn = $(this);
                            var montoVal = btn.data('monto');
                            var tasaVal = btn.data('tasa');
                            var comVal = btn.data('comision');
                            $('#monto').val(montoVal);
                            $('#saldo_inicial').val(montoVal);
                            $('#tasa').val(tasaVal);
                            $('#interes_mensual').val(tasaVal);
                            $('#plazo').val(btn.data('plazo'));
                            $('#comision').val(comVal);
                            computeRatesFromInteres();
                        });

                                        $('#btnPreview').on('click', function(e){
                            e.preventDefault();
                                    var chk = validateChosenDays(); if (!chk.ok) { alert(chk.message); return; }
                                    var pchk = validatePlazo(); if (!pchk.ok) { alert(pchk.message); return; }
                            var payload = {
                                monto: $('#monto').val(),
                                        tasa: $('#tasa').val(),
                                        plazo: $('#plazo').val(),
                                        frecuencia: $('#frecuencia').val(),
                                        fecha_inicio: $('#primer_pago_catorcenal').val() || $('#primer_dia_pago').val() || $('#fecha_desembolso').val() || null,
                                        fecha_desembolso: $('#fecha_desembolso').val() || null,
                                        dia: $('#dia_pago').val() || null,
                                        dia_semana: $('#dia_semana').val() || null,
                                        dia1: $('#dia1').val() || null,
                                        dia2: $('#dia2').val() || null
                            };
                            $('#preview_container').hide();
                            $('#schedule_table tbody').html('');
                                    $.post('<?php echo base_url('prestamo/generate_preview_ajax'); ?>', payload, function(resp){
                                if (!resp || !resp.status) { alert('Error generando preview'); return; }
                                $('#preview_payment').text(resp.payment);
                                        var commAmt = resp.commission_per_period || 0;
                                        var commPct = resp.commission_percent ? (resp.commission_percent * 100) : null;
                                        var commText = Number(parseFloat(commAmt).toFixed(2));
                                        if (commPct !== null) commText += ' (' + Number(parseFloat(commPct).toFixed(2)) + '%)';
                                        $('#preview_commission').text(commText);
                                var rows = '';
                                        // Calcular subtotales
                                        var totalCuotas = 0, totalPrincipal = 0, totalInteres = 0;
                                        $.each(resp.schedule, function(i,r){
                                            var dias = r.dias !== undefined ? r.dias : 0;
                                            var cuota = parseFloat(r.cuota)||0;
                                            var principal = parseFloat(r.principal)||0;
                                            var interes = parseFloat(r.interes)||0;
                                            totalCuotas += cuota;
                                            totalPrincipal += principal;
                                            totalInteres += interes;
                                            rows += '<tr><td>'+r.numero+'</td><td>'+r.fecha+'</td><td>'+dias+'</td><td>'+r.cuota+'</td><td>'+r.principal+'</td><td>'+r.interes+'</td><td>'+r.comision+'</td><td>'+r.saldo+'</td></tr>';
                                        });
                                        $('#schedule_table tbody').html(rows);
                                        // Mostrar subtotales
                                        $('#total_cuotas').text(totalCuotas.toFixed(2));
                                        $('#total_principal').text(totalPrincipal.toFixed(2));
                                        $('#total_interes').text(totalInteres.toFixed(2));
                                        $('#totals_table').show();
                                        $('#preview_container').show();
                            }, 'json');
                        });

                        $('#monto').on('input', function(){
                            var v = $(this).val();
                            $('#saldo_inicial').val(v);
                        });

                        // frequency change: show/hide day inputs
                        function updateDayInputs(){
                            var f = $('#frecuencia').val();
                            // Ocultar todos primero
                            $('#dia_container').hide();
                            $('#dia_semana_container').hide();
                            $('#dias_quincena_container').hide();
                            $('#dia_catorcenal_container').hide();
                            
                            if (f === 'mensual') {
                                $('#dia_container').show();
                            } else if (f === 'semanal') {
                                $('#dia_semana_container').show();
                            } else if (f === 'quincenal') {
                                $('#dias_quincena_container').show();
                            } else if (f === 'catorcenal') {
                                $('#dia_catorcenal_container').show();
                                // Prellenar con primer_dia_pago si existe
                                var primerDia = $('#primer_dia_pago').val();
                                if (primerDia && !$('#primer_pago_catorcenal').val()) {
                                    $('#primer_pago_catorcenal').val(primerDia);
                                }
                            }
                        }
                        $('#frecuencia').on('change', updateDayInputs);
                        // init visibility
                        updateDayInputs();

                        $('#btnSaveCredit').on('click', function(e){
                            e.preventDefault();
                            var chk = validateChosenDays(); if (!chk.ok) { alert(chk.message); return; }
                            var pchk = validatePlazo(); if (!pchk.ok) { alert(pchk.message); return; }
                            var idsol = $('#idsolicitud').val();
                            if (!idsol) return alert('Seleccione solicitud');
                            var payload = {
                                idsolicitud: idsol,
                                monto: $('#monto').val(),
                                tasa: $('#tasa').val(),
                                plazo: $('#plazo').val(),
                                frecuencia: $('#frecuencia').val(),
                                comision: $('#comision').val(),
                                fecha_credito: $('#primer_pago_catorcenal').val() || $('#primer_dia_pago').val() || $('#fecha_desembolso').val() || (new Date()).toISOString().slice(0,10),
                                fecha_desembolso: $('#fecha_desembolso').val() || null,
                                primer_dia_pago: $('#primer_dia_pago').val() || null,
                                dia: $('#dia_pago').val() || null,
                                dia_semana: $('#dia_semana').val() || null,
                                dia1: $('#dia1').val() || null,
                                dia2: $('#dia2').val() || null
                            };
                            $(this).prop('disabled', true).text('Guardando...');
                            $.post('<?php echo base_url('prestamo/save_credit_ajax'); ?>', payload, function(resp){
                                if (!resp || !resp.status) { alert('Error al guardar: '+(resp && resp.message)); $('#btnSaveCredit').prop('disabled', false).text('Guardar crédito'); return; }
                                alert('Crédito creado. ID: '+resp.idprestamo);
                                // Descargar automáticamente el PDF del plan de crédito
                                window.open('<?php echo base_url('prestamo/pdf/'); ?>' + resp.idprestamo, '_blank');
                                location.reload();
                            }, 'json');
                        });
                    });
                    </script>

                </div>
            </div>

        </div>
    </div>
</div>
                                        idsolicitud: idsol,
