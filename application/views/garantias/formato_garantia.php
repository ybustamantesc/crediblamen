<?php defined('BASEPATH') OR exit('No direct script access allowed');
$g = isset($garantia) ? $garantia : null;
$solicitud_id = isset($solicitud_id) ? $solicitud_id : '';
$this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-shield-alt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Formato de Garantía</h5>
                                <span>Suba fotos y registre la información de la garantía asociada a la solicitud</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a class="btn btn-secondary" href="<?php echo base_url('solicitudes'); ?>">Volver a Solicitudes</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('message')): ?>
                                <div class="alert alert-success"><?php echo $this->session->flashdata('message'); ?></div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('garantias/save'); ?>
                                <input type="hidden" name="solicitud_id" value="<?php echo html_escape($solicitud_id); ?>">

                                <p class="text-muted">Complete el cuadro siguiente. Agregue filas según necesite.</p>

                                <!-- Indicador de Tasa de Cambio -->
                                <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <i class="fas fa-dollar-sign mr-2"></i>
                                        <strong>Tasa de Cambio:</strong> <span id="tasa_cambio_display">Cargando...</span>
                                    </div>
                                    <a href="<?php echo base_url('tasacambio'); ?>" class="btn btn-sm btn-outline-dark" target="_blank">
                                        <i class="fas fa-edit"></i> Gestionar TC
                                    </a>
                                </div>

                                <div id="garantias-table-wrapper" class="table-responsive d-none d-md-block mb-3">
                                    <table id="garantias-table" class="table table-bordered table-sm" style="border-collapse:collapse;">
                                        <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width:6%">Cant.</th>
                                                    <th>Descripción del Artículo</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Modelo</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Marca / Color</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Nº Serie</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Avalúo C$</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Avalúo US$</th>
                                                    <th class="d-none d-md-table-cell" style="width:12%">Estado</th>
                                                    <th style="width:6%">&nbsp;</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // prepare existing rows
                                                $existing = isset($garantias) && is_array($garantias) ? $garantias : array();
                                                $initial_rows = max(1, count($existing));
                                                for ($row=0;$row<$initial_rows;$row++):
                                                    $eg = isset($existing[$row]) ? $existing[$row] : null;
                                                    $uid = $row; // unique id per rendered row
                                            ?>
                                            <tr data-uid="<?php echo $uid; ?>">
                                                <td class="text-center align-middle">
                                                    <input type="number" name="cantidad[]" min="0" step="1" class="form-control form-control-sm text-center qty" value="<?php echo $eg ? (int)$eg->cantidad : ''; ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="nombre[]" class="form-control form-control-sm name" value="<?php echo $eg ? html_escape($eg->nombre) : ''; ?>" placeholder="Descripción / Nombre de la garantía">
                                                    <div class="mt-1">
                                                        <input type="file" accept="image/*" class="form-control-file foto-input" data-uid="<?php echo $uid; ?>" multiple>
                                                        <small class="text-muted fotos-filename" data-uid="<?php echo $uid; ?>"></small>
                                                        <div class="fotos-list mt-1" data-uid="<?php echo $uid; ?>"></div>
                                                    </div>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input type="text" name="modelo[]" class="form-control form-control-sm" value="<?php echo $eg ? html_escape($eg->modelo) : ''; ?>">
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input type="text" name="marca[]" class="form-control form-control-sm" value="<?php echo $eg ? html_escape($eg->marca) : ''; ?>">
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input type="text" name="n_serie[]" class="form-control form-control-sm" value="<?php echo $eg && !empty($eg->n_serie) ? html_escape($eg->n_serie) : ''; ?>">
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">C$</span>
                                                    </div>
                                                    <input type="number" step="0.01" name="costo[]" class="form-control form-control-sm cost cost-cordobas" value="<?php echo $eg ? html_escape($eg->costo) : ''; ?>" data-uid="<?php echo $uid; ?>">
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">US$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm cost-dolares" readonly data-uid="<?php echo $uid; ?>" value="">
                                                </div>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input type="text" name="tiempo_vida[]" class="form-control form-control-sm" value="<?php echo $eg ? html_escape($eg->tiempo_vida) : ''; ?>">
                                                </td>
                                                <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-danger remove-row">Eliminar</button></td>
                                            </tr>
                                            <?php endfor; ?>

                                            <!-- Fila TOTAL -->
                                            <tr class="total-row">
                                                <td colspan="6" class="text-right font-weight-bold">TOTAL</td>
                                                <td><div class="input-group input-group-sm"><div class="input-group-prepend"><span class="input-group-text">C$</span></div><input type="text" id="garantias_total" name="total" class="form-control form-control-sm" value="0.00" readonly></div></td>
                                                <td><div class="input-group input-group-sm"><div class="input-group-prepend"><span class="input-group-text">US$</span></div><input type="text" id="garantias_total_usd" class="form-control form-control-sm" value="0.00" readonly></div></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile: stacked card view for small screens -->
                                <div id="garantias-cards" class="d-block d-md-none mb-3"></div>

                                <style>
                                    /* Mobile tweaks: make file inputs and badges more usable on phones */
                                    @media (max-width: 767.98px) {
                                        .form-control-file { width:100%; display:block; }
                                        .fotos-filename { display:block; margin-top:6px; }
                                        .fotos-list .badge { font-size: .85rem; }
                                        #garantias-table td, #garantias-table th { font-size: .95rem; }
                                        /* Make remove buttons easier to tap */
                                        #garantias-table .remove-row { padding: .35rem .5rem; }
                                    }
                                </style>

                                <div class="mb-2">
                                    <button type="button" id="add_garantia_row" class="btn btn-sm btn-outline-primary">Agregar fila</button>
                                </div>

                                <!-- Foto única por renglón: el input está en cada fila -->

                                <!-- 'Céd. Del Cliente' ocultado: no es necesario mostrar ni persistir -->

                                <div class="form-group d-flex align-items-center">
                                    <button class="btn btn-primary mr-2">Guardar Formato</button>
                                    <?php if ($g): ?>
                                        <a class="btn btn-secondary mr-2" href="<?php echo base_url('garantias/view/'.$g->id); ?>">Ver</a>
                                        <a class="btn btn-info mr-2" href="<?php echo base_url('garantias/pdf/'.$g->id); ?>" target="_blank">Descargar PDF</a>
                                    <?php endif; ?>
                                    <?php if ($g && isset($g->id)): ?>
                                        <button type="button" class="btn btn-outline-dark" onclick="window.open('<?php echo base_url('garantias/pdf/'.$g->id); ?>','_blank')">Imprimir</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-dark" id="print_save_btn">Imprimir</button>
                                    <?php endif; ?>
                                </div>

                            <?php echo form_close(); ?>

                            <script type="text/javascript">
                                    (function waitForjQuery(){
                                if (window.jQuery) {
                                    (function($){
                                        // Variable global para almacenar la tasa de cambio
                                        var tasaCambio = 36.50; // default fallback

                                        // Cargar tasa de cambio actual
                                        function loadTasaCambio(){
                                            $.ajax({
                                                url: '<?php echo base_url("tasacambio/get_tasa_actual_ajax"); ?>',
                                                type: 'GET',
                                                dataType: 'json',
                                                success: function(resp){
                                                    if(resp && resp.status && resp.tasa){
                                                        tasaCambio = parseFloat(resp.tasa);
                                                        console.log('Tasa de cambio cargada:', tasaCambio);
                                                        $('#tasa_cambio_display').html('C$ ' + tasaCambio.toFixed(4) + ' por US$1');
                                                        // Actualizar conversiones existentes
                                                        recalc();
                                                    }
                                                },
                                                error: function(){
                                                    console.warn('No se pudo cargar la tasa de cambio, usando valor por defecto:', tasaCambio);
                                                    $('#tasa_cambio_display').html('C$ ' + tasaCambio.toFixed(4) + ' por US$1 (valor por defecto)');
                                                }
                                            });
                                        }

                                        // Convertir valor individual de córdobas a dólares
                                        function convertirADolares(uid){
                                            var $row = $('#garantias-table tbody tr[data-uid="'+uid+'"]');
                                            var cordobas = parseFloat($row.find('.cost-cordobas').val()) || 0;
                                            var dolares = tasaCambio > 0 ? (cordobas / tasaCambio) : 0;
                                            $row.find('.cost-dolares[data-uid="'+uid+'"]').val(dolares.toFixed(2));
                                        }

                                        function recalc(){
                                            var sum = 0.0;
                                            var sumUSD = 0.0;
                                            $('#garantias-table tbody tr').has('.qty').each(function(){
                                                var $row = $(this);
                                                var uid = $row.data('uid');
                                                var q = parseFloat($row.find('.qty').val()) || 0;
                                                var c = parseFloat($row.find('.cost-cordobas').val()) || 0;
                                                sum += q * c;
                                                
                                                // Actualizar conversión a dólares
                                                if(uid !== undefined){
                                                    convertirADolares(uid);
                                                    var usd = parseFloat($row.find('.cost-dolares').val()) || 0;
                                                    sumUSD += q * usd;
                                                }
                                            });
                                            $('#garantias_total').val(sum.toFixed(2));
                                            $('#garantias_total_usd').val(sumUSD.toFixed(2));
                                        }

                                        // Evento para recalcular cuando cambia cantidad o costo
                                        $(document).on('input change', '#garantias-table .qty, #garantias-table .cost-cordobas', recalc);
                                        
                                        // Cargar tasa y calcular al inicio
                                        $(function(){ 
                                            loadTasaCambio();
                                            recalc(); 
                                        });

                                        // File pool per row UID to accumulate files when user selects multiple times
                                        var filePool = {}; // keyed by uid
                                        // initialize pools for existing rows
                                        $('#garantias-table tbody tr[data-uid]').each(function(){
                                            var uid = $(this).data('uid');
                                            if (uid !== undefined) filePool[uid] = [];
                                        });

                                        // UID counter for newly added rows
                                        var uidCounter = <?php echo isset($initial_rows) ? $initial_rows : 1; ?>;

                                        function renderFileList(uid){
                                            var list = filePool[uid] || [];
                                            var $c = $('.fotos-list[data-uid="'+uid+'"]');
                                            $c.empty();
                                            if (list.length === 0){
                                                $('.fotos-filename[data-uid="'+uid+'"]') .text('');
                                                return;
                                            }
                                            $('.fotos-filename[data-uid="'+uid+'"]') .text(list.map(function(f){return f.name;}).join(', '));
                                            // show each with remove button
                                            list.forEach(function(f, idx){
                                                var $item = $('<div class="badge badge-light mr-1 mb-1 p-2" style="display:inline-block;"></div>');
                                                var $name = $('<span></span>').text(f.name + ' ');
                                                var $rm = $('<button type="button" class="btn btn-sm btn-danger ml-1">x</button>');
                                                $rm.on('click', function(){
                                                    filePool[uid].splice(idx,1);
                                                    renderFileList(uid);
                                                });
                                                $item.append($name).append($rm);
                                                $c.append($item);
                                            });
                                        }

                                        $(document).on('change', '.foto-input', function(e){
                                            var uid = $(this).data('uid');
                                            var files = this.files || [];
                                            if (!filePool[uid]) filePool[uid] = [];
                                            // append files up to 5 total per row
                                            for (var i=0;i<files.length;i++){
                                                if (filePool[uid].length >= 5) break;
                                                filePool[uid].push(files[i]);
                                            }
                                            if (filePool[uid].length > 5){
                                                alert('Máximo 5 fotos por renglón. Se han tomado las primeras 5.');
                                                filePool[uid] = filePool[uid].slice(0,5);
                                            }
                                            renderFileList(uid);
                                            // clear the input so user can re-open file dialog to add more (we keep pool in memory)
                                            $(this).val('');
                                        });

                                        // Add new row
                                        function createRow(uid, data){
                                            data = data || {};
                                            var $tbody = $('#garantias-table tbody');
                                            var $total = $tbody.find('tr.total-row');
                                            var $tr = $('<tr data-uid="'+uid+'"></tr>');
                                            var $tdQty = $('<td class="text-center align-middle"></td>').append('<input type="number" name="cantidad[]" min="0" step="1" class="form-control form-control-sm text-center qty" value="'+(data.cantidad||'')+'">');
                                            var $tdName = $('<td></td>').append('<input type="text" name="nombre[]" class="form-control form-control-sm name" value="'+(data.nombre||'')+'" placeholder="Descripción / Nombre de la garantía">');
                                            var $fileDiv = $('<div class="mt-1"></div>');
                                            $fileDiv.append('<input type="file" accept="image/*" class="form-control-file foto-input" data-uid="'+uid+'" multiple>');
                                            $fileDiv.append('<small class="text-muted fotos-filename" data-uid="'+uid+'"></small>');
                                            $fileDiv.append('<div class="fotos-list mt-1" data-uid="'+uid+'"></div>');
                                            $tdName.append($fileDiv);
                                            var $tdModel = $('<td></td>').append('<input type="text" name="modelo[]" class="form-control form-control-sm" value="'+(data.modelo||'')+'">');
                                            var $tdMarca = $('<td></td>').append('<input type="text" name="marca[]" class="form-control form-control-sm" value="'+(data.marca||'')+'">');
                                            var $tdSerie = $('<td></td>').append('<input type="text" name="n_serie[]" class="form-control form-control-sm" value="'+(data.n_serie||'')+'">');
                                            
                                            // Campo de avalúo en córdobas
                                            var $tdCosto = $('<td></td>').append(
                                                '<div class="input-group input-group-sm">' +
                                                '<div class="input-group-prepend"><span class="input-group-text">C$</span></div>' +
                                                '<input type="number" step="0.01" name="costo[]" class="form-control form-control-sm cost cost-cordobas" value="'+(data.costo||'')+'" data-uid="'+uid+'">' +
                                                '</div>'
                                            );
                                            
                                            // Campo de avalúo en dólares (solo lectura, se calcula automáticamente)
                                            var $tdCostoUSD = $('<td></td>').append(
                                                '<div class="input-group input-group-sm">' +
                                                '<div class="input-group-prepend"><span class="input-group-text">US$</span></div>' +
                                                '<input type="text" class="form-control form-control-sm cost-dolares" readonly data-uid="'+uid+'" value="">' +
                                                '</div>'
                                            );
                                            
                                            var $tdVida = $('<td></td>').append('<input type="text" name="tiempo_vida[]" class="form-control form-control-sm" value="'+(data.tiempo_vida||'')+'">');
                                            var $tdRm = $('<td class="text-center align-middle"></td>').append('<button type="button" class="btn btn-sm btn-danger remove-row">Eliminar</button>');
                                            $tr.append($tdQty).append($tdName).append($tdModel).append($tdMarca).append($tdSerie).append($tdCosto).append($tdCostoUSD).append($tdVida).append($tdRm);
                                            $total.before($tr);
                                            filePool[uid] = [];
                                            recalc();
                                            if ($(window).width() < 768) createCardForUid(uid);
                                        }

                                        // Add row button
                                        $('#add_garantia_row').on('click', function(){
                                            createRow(uidCounter, {});
                                            uidCounter++;
                                        });

                                        // Remove row (delegated)
                                        $(document).on('click', '.remove-row', function(){
                                            var $tr = $(this).closest('tr');
                                            var uid = $tr.data('uid');
                                            if (confirm('Eliminar esta fila?')) {
                                                if (filePool[uid]) delete filePool[uid];
                                                $tr.remove();
                                                // also remove mobile card if present
                                                $('#garantia-card-'+uid).remove();
                                                recalc();
                                            }
                                        });

                                        // flag to indicate we should open PDF after successful save
                                        var print_after_save = false;

                                        // When user clicks Print on unsaved form, set flag and submit
                                        $(document).on('click', '#print_save_btn', function(){
                                            if (confirm('Guardar el formato y generar PDF ahora?')) {
                                                print_after_save = true;
                                                $('#garantias-table').closest('form').submit();
                                            }
                                        });

                                        // intercept submit and map files according to current row order
                                        $('#garantias-table').closest('form').on('submit', function(ev){
                                            ev.preventDefault();
                                            var $form = $(this);
                                            var action = $form.attr('action') || window.location.href;
                                            var fd = new FormData();

                                            // append normal fields using serializeArray (excludes file inputs)
                                            $.each($form.serializeArray(), function(i, field){
                                                fd.append(field.name, field.value);
                                            });

                                            // Map files to current row order: fotos[0] corresponds to first data row, etc.
                                            var dataRowIndex = 0;
                                            $('#garantias-table tbody tr').not('.total-row').each(function(){
                                                var $tr = $(this);
                                                // skip rows without qty/name (safety)
                                                if ($tr.find('input[name="cantidad[]"]').length === 0) return;
                                                var uid = $tr.data('uid');
                                                var arr = filePool[uid] || [];
                                                for (var i=0;i<arr.length;i++){
                                                    fd.append('fotos['+dataRowIndex+'][]', arr[i], arr[i].name);
                                                }
                                                dataRowIndex++;
                                            });

                                            var $btn = $form.find('button[type=submit]').prop('disabled', true).text('Guardando...');

                                            $.ajax({
                                                url: action,
                                                method: 'POST',
                                                data: fd,
                                                processData: false,
                                                contentType: false,
                                                success: function(resp){
                                                    try {
                                                        var j = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                                                        if (print_after_save) {
                                                            // try to extract id from redirect or use returned id
                                                            var id = null;
                                                            if (j && j.redirect) {
                                                                var m = j.redirect.match(/garantias\/view\/(\d+)/);
                                                                if (m) id = m[1];
                                                            }
                                                            if (!id && j && (j.id || j.garantia_id)) id = j.id || j.garantia_id;
                                                            if (id) {
                                                                window.open('<?php echo base_url('garantias/pdf/'); ?>' + id, '_blank');
                                                                print_after_save = false;
                                                                return;
                                                            }
                                                            // fallback: if redirect provided, try to derive id anyway
                                                            if (j && j.redirect) {
                                                                window.location = j.redirect;
                                                                return;
                                                            }
                                                        } else {
                                                            if (j && j.success && j.redirect) {
                                                                window.location = j.redirect;
                                                                return;
                                                            }
                                                        }
                                                    } catch(e){ }
                                                    window.location.reload();
                                                },
                                                error: function(xhr){
                                                    alert('Error guardando: ' + xhr.statusText);
                                                },
                                                complete: function(){
                                                    $btn.prop('disabled', false).text('Guardar Formato');
                                                }
                                            });
                                        });

                                        // Mobile card sync: move inputs into stacked cards for small screens
                                        function createCardForUid(uid){
                                            if($('#garantia-card-'+uid).length) return;
                                            var $tr = $('#garantias-table tbody tr[data-uid="'+uid+'"]');
                                            if (!$tr.length) return;
                                            var $card = $('<div class="card mb-2" id="garantia-card-'+uid+'"></div>');
                                            var $body = $('<div class="card-body"></div>');
                                            var $cantidad = $tr.find('input[name="cantidad[]"]');
                                            var $nombre = $tr.find('input[name="nombre[]"]');
                                            var $modelo = $tr.find('input[name="modelo[]"]');
                                            var $marca = $tr.find('input[name="marca[]"]');
                                            var $nserie = $tr.find('input[name="n_serie[]"]');
                                            var $costo = $tr.find('input[name="costo[]"]');
                                            var $tiempo = $tr.find('input[name="tiempo_vida[]"]');
                                            var $fotoInput = $tr.find('.foto-input');
                                            var $fotosList = $tr.find('.fotos-list');
                                            var $fotosFilename = $tr.find('.fotos-filename');

                                            var $row = $('<div class="row"></div>');
                                            $row.append($('<div class="col-4 form-group"></div>').append('<label class="d-block">Cant.</label>').append($cantidad));
                                            $row.append($('<div class="col-8 form-group"></div>').append('<label class="d-block">Descripción</label>').append($nombre));
                                            $body.append($row);

                                            var $row2 = $('<div class="row"></div>');
                                            $row2.append($('<div class="col-6 form-group"></div>').append('<label class="d-block">Modelo</label>').append($modelo));
                                            $row2.append($('<div class="col-6 form-group"></div>').append('<label class="d-block">Marca</label>').append($marca));
                                            $body.append($row2);

                                            var $row3 = $('<div class="row"></div>');
                                            $row3.append($('<div class="col-6 form-group"></div>').append('<label class="d-block">Nº Serie</label>').append($nserie));
                                            $row3.append($('<div class="col-6 form-group"></div>').append('<label class="d-block">Avalúo C$</label>').append($costo));
                                            $body.append($row3);

                                            // Mostrar valor en dólares (solo lectura)
                                            var $costoDolares = $tr.find('.cost-dolares[data-uid="'+uid+'"]').clone();
                                            var $row3b = $('<div class="row"></div>');
                                            $row3b.append($('<div class="col-12 form-group"></div>').append('<label class="d-block">Avalúo US$</label>').append($costoDolares));
                                            $body.append($row3b);

                                            $body.append($('<div class="form-group"></div>').append('<label class="d-block">Estado</label>').append($tiempo));

                                            var $photoDiv = $('<div class="form-group"></div>').append('<label class="d-block">Fotos</label>');
                                            $photoDiv.append($fotoInput);
                                            $photoDiv.append($fotosFilename);
                                            $photoDiv.append($fotosList);
                                            $body.append($photoDiv);

                                            var $actions = $('<div class="text-right mt-2"></div>');
                                            var $remove = $('<button type="button" class="btn btn-sm btn-danger remove-row">Eliminar</button>');
                                            $actions.append($remove);
                                            $body.append($actions);

                                            $card.append($body);
                                            $('#garantias-cards').append($card);
                                        }


                                        // DESACTIVADO: No mover inputs a tarjetas en móvil para evitar bloqueos en celulares
                                        // Ahora siempre se muestra la tabla, así los campos funcionan en todos los dispositivos
                                        // Si se quiere volver a activar la vista de tarjetas, restaurar la función buildMobileCards y su llamada

                                        // $(function(){
                                        //     buildMobileCards();
                                        //     var resizeTimer = null;
                                        //     $(window).on('resize', function(){ clearTimeout(resizeTimer); resizeTimer = setTimeout(function(){ buildMobileCards(); }, 200); });
                                        // });

                                    })(jQuery);
                                } else {
                                    setTimeout(waitForjQuery, 50);
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

