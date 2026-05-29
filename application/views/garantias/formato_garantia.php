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

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Cliente:</strong> <?php echo htmlspecialchars(isset($cliente_nombre) ? $cliente_nombre : ''); ?>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <strong>Código de solicitud:</strong> <?php echo htmlspecialchars(isset($codigo_solicitud) ? $codigo_solicitud : ''); ?>
                                    </div>
                                </div>

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
                                                    <th class="d-none d-md-table-cell" style="width:10%">Marca / Color</th>
                                                    <th class="d-none d-md-table-cell" style="width:8%">Nº Serie</th>
                                                    <th class="d-none d-md-table-cell" style="width:14%">Avalúo C$</th>
                                                    <th class="d-none d-md-table-cell" style="width:14%">Avalúo US$</th>
                                                    <th class="d-none d-md-table-cell" style="width:10%">Estado</th>
                                                    <th style="width:6%">&nbsp;</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // prepare existing rows - only render if there are existing garantias
                                                $existing = isset($garantias) && is_array($garantias) ? $garantias : array();
                                                $initial_rows = count($existing); // Do not add empty row if no existing guarantees
                                                for ($row=0;$row<$initial_rows;$row++):
                                                    $eg = isset($existing[$row]) ? $existing[$row] : null;
                                                    $uid = $row; // unique id per rendered row
                                            ?>
                                            <tr data-uid="<?php echo $uid; ?>" data-garantia-id="<?php echo $eg && isset($eg->id) ? $eg->id : ''; ?>">
                                                <input type="hidden" name="garantia_id[]" value="<?php echo $eg && isset($eg->id) ? $eg->id : ''; ?>">
                                                <td class="text-center align-middle">
                                                    <input type="number" name="cantidad[]" min="0" step="1" class="form-control form-control-sm text-center qty" value="<?php echo $eg ? (int)$eg->cantidad : ''; ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="nombre[]" class="form-control form-control-sm name" value="<?php echo $eg ? html_escape($eg->nombre) : ''; ?>" placeholder="Descripción / Nombre de la garantía">
                                                    <div class="mt-1">
                                                        <input type="file" accept="image/*" class="form-control-file foto-input" data-uid="<?php echo $uid; ?>" multiple>
                                                        <small class="text-muted fotos-filename" data-uid="<?php echo $uid; ?>"></small>
                                                        <div class="fotos-list mt-1" data-uid="<?php echo $uid; ?>"></div>
                                                        <?php if ($eg && isset($eg->id)): ?>
                                                            <?php
                                                                $existingPhotos = array();
                                                                if (isset($photos_map) && isset($photos_map[$eg->id]) && is_array($photos_map[$eg->id]) && ! empty($photos_map[$eg->id])) {
                                                                    $existingPhotos = $photos_map[$eg->id];
                                                                } else {
                                                                    for ($pi = 1; $pi <= 5; $pi++) {
                                                                        $col = 'foto' . $pi;
                                                                        if (! empty($eg->$col)) {
                                                                            $existingPhotos[] = $eg->$col;
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                            <?php if (! empty($existingPhotos)): ?>
                                                                <div class="existing-fotos-list mt-2" data-uid="<?php echo $uid; ?>">
                                                                    <div class="text-muted small mb-1">Fotos guardadas:</div>
                                                                    <div class="d-flex flex-wrap" style="gap:.4rem;">
                                                                        <?php foreach ($existingPhotos as $photo_idx => $photo):
                                                                            $rel = trim($photo);
                                                                            if ($rel === '') continue;
                                                                            if (strpos($rel, 'data:') === 0) {
                                                                                $src = $rel;
                                                                            } elseif (preg_match('#^https?://#i', $rel)) {
                                                                                $src = $rel;
                                                                            } else {
                                                                                $src = base_url(ltrim($rel, '/\\'));
                                                                            }
                                                        ?>
                                                                            <div class="photo-wrapper position-relative" data-uid="<?php echo $uid; ?>" data-photo-idx="<?php echo $photo_idx; ?>" data-photo="<?php echo htmlspecialchars($rel); ?>">
                                                                                <a href="<?php echo htmlspecialchars($src); ?>" target="_blank" class="d-inline-block existing-photo" style="width:68px; height:68px; overflow:hidden; border:1px solid #dee2e6; border-radius:4px; display:flex; align-items:center; justify-content:center;">
                                                                                    <img src="<?php echo htmlspecialchars($src); ?>" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                                                                                </a>
                                                                                <button type="button" class="btn btn-xs btn-danger delete-photo-btn" style="position:absolute; top:-8px; right:-8px; width:24px; height:24px; padding:0; border-radius:50%; font-size:12px; line-height:1; display:flex; align-items:center; justify-content:center;" title="Eliminar foto">×</button>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
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
                                <!-- Mobile totals: visible only on small screens -->
                                <div id="garantias-mobile-total" class="d-block d-md-none mb-3">
                                    <div class="card">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div class="font-weight-bold">TOTAL</div>
                                            <div style="min-width:260px; display:flex; gap:.75rem; align-items:center;">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend"><span class="input-group-text">C$</span></div>
                                                    <input type="text" id="garantias_total_mobile" class="form-control form-control-sm" value="0.00" readonly>
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend"><span class="input-group-text">US$</span></div>
                                                    <input type="text" id="garantias_total_usd_mobile" class="form-control form-control-sm" value="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <style>
                                    /* Mobile tweaks: make file inputs and badges more usable on phones */
                                    @media (max-width: 767.98px) {
                                        .form-control-file { width:100%; display:block; }
                                        .fotos-filename { display:block; margin-top:6px; }
                                        .fotos-list .badge { font-size: .85rem; }
                                        #garantias-table td, #garantias-table th { font-size: .95rem; }
                                        /* Make remove buttons easier to tap */
                                        #garantias-table .remove-row { padding: .35rem .5rem; }
                                        
                                        /* Mobile card styles for garantias */
                                        #garantias-cards .card {
                                            border: 1px solid #dfe7f4;
                                            border-radius: 4px;
                                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                                        }
                                        #garantias-cards .card-body {
                                            padding: 1rem;
                                        }
                                        #garantias-cards .form-group {
                                            margin-bottom: 1rem;
                                        }
                                        #garantias-cards label {
                                            font-weight: 600;
                                            font-size: 0.9rem;
                                            margin-bottom: 0.4rem;
                                            color: #333;
                                        }
                                        #garantias-cards .form-control,
                                        #garantias-cards .form-control-file {
                                            font-size: 1rem;
                                            padding: 0.6rem 0.75rem;
                                        }
                                        #garantias-cards .input-group-text {
                                            font-size: 0.9rem;
                                        }
                                        #garantias-cards .row {
                                            margin-right: -0.5rem;
                                            margin-left: -0.5rem;
                                        }
                                        #garantias-cards .col-6,
                                        #garantias-cards .col-12 {
                                            padding-right: 0.5rem;
                                            padding-left: 0.5rem;
                                        }
                                        #garantias-cards .text-right {
                                            padding-top: 0.5rem;
                                            border-top: 1px solid #e0e0e0;
                                        }
                                        #garantias-cards .btn {
                                            font-size: 0.9rem;
                                            padding: 0.5rem 1rem;
                                        }
                                        /* Existing photos styles in mobile cards */
                                        #garantias-cards .existing-fotos-list {
                                            margin-bottom: 1rem;
                                            padding-bottom: 1rem;
                                            border-bottom: 1px solid #e9ecef;
                                        }
                                        #garantias-cards .existing-fotos-list .text-muted {
                                            font-size: 0.85rem;
                                            margin-bottom: 0.5rem;
                                        }
                                        #garantias-cards .existing-fotos-list .d-flex {
                                            gap: 0.5rem;
                                        }
                                        #garantias-cards .existing-fotos-list a {
                                            width: 70px;
                                            height: 70px;
                                            border-radius: 4px;
                                            border: 1px solid #dfe7f4;
                                            display: inline-block;
                                            overflow: hidden;
                                        }
                                        #garantias-cards .existing-fotos-list img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: cover;
                                        }
                                    }
                                    
                                    /* Styles for photo deletion */
                                    .photo-wrapper {
                                        position: relative;
                                        transition: opacity 0.2s ease;
                                    }
                                    
                                    .photo-wrapper.marked-for-deletion {
                                        opacity: 0.5;
                                    }
                                    
                                    .photo-wrapper.marked-for-deletion .existing-photo {
                                        filter: grayscale(100%);
                                        opacity: 0.6;
                                    }
                                    
                                    .photo-wrapper.marked-for-deletion::after {
                                        content: '';
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        right: 0;
                                        bottom: 0;
                                        border: 2px solid #dc3545;
                                        border-radius: 4px;
                                        background: repeating-linear-gradient(
                                            45deg,
                                            rgba(220, 53, 69, 0.1),
                                            rgba(220, 53, 69, 0.1) 10px,
                                            transparent 10px,
                                            transparent 20px
                                        );
                                    }
                                    
                                    .delete-photo-btn {
                                        background-color: #dc3545;
                                        border: none;
                                        color: white;
                                        font-weight: bold;
                                        cursor: pointer;
                                        transition: all 0.2s ease;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                    }
                                    
                                    .delete-photo-btn:hover {
                                        background-color: #c82333;
                                        transform: scale(1.1);
                                        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
                                    }
                                    
                                    .photo-wrapper.marked-for-deletion .delete-photo-btn {
                                        background-color: #28a745;
                                    }
                                    
                                    .photo-wrapper.marked-for-deletion .delete-photo-btn:hover {
                                        background-color: #218838;
                                    }

                                    /* Ensure the mobile totals card is hidden on desktop */
                                    @media (min-width: 768px) {
                                        #garantias-mobile-total { display: none !important; }
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
                                            // Update mobile totals if present
                                            if ($('#garantias_total_mobile').length) {
                                                $('#garantias_total_mobile').val(sum.toFixed(2));
                                            }
                                            if ($('#garantias_total_usd_mobile').length) {
                                                $('#garantias_total_usd_mobile').val(sumUSD.toFixed(2));
                                            }
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
                                        var uidCounter = <?php echo isset($initial_rows) ? $initial_rows : 0; ?>;

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
                                            var $tdQty = $('<td class="text-center align-middle"></td>').append('<input type="hidden" name="garantia_id[]" value="">').append('<input type="number" name="cantidad[]" min="0" step="1" class="form-control form-control-sm text-center qty" value="'+(data.cantidad||'')+'">');
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

                                        // Track photos marked for deletion: {uid: {photo: data-photo}}
                                        var photosToDelete = {};

                                        // Handle delete photo button clicks
                                        $(document).on('click', '.delete-photo-btn', function(e){
                                            e.preventDefault();
                                            e.stopPropagation();
                                            var $wrapper = $(this).closest('.photo-wrapper');
                                            var uid = $wrapper.data('uid');
                                            var photoData = $wrapper.data('photo');
                                            
                                            if (!photosToDelete[uid]) {
                                                photosToDelete[uid] = {};
                                            }
                                            
                                            // Toggle deletion state
                                            if (photosToDelete[uid][photoData]) {
                                                delete photosToDelete[uid][photoData];
                                                $wrapper.removeClass('marked-for-deletion');
                                            } else {
                                                photosToDelete[uid][photoData] = true;
                                                $wrapper.addClass('marked-for-deletion');
                                            }
                                        });
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

                                            // Add photos marked for deletion
                                            var deleteIndex = 0;
                                            for (var uid in photosToDelete) {
                                                for (var photo in photosToDelete[uid]) {
                                                    fd.append('fotos_eliminar[' + deleteIndex + ']', photo);
                                                    deleteIndex++;
                                                }
                                            }

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

                                        // Mobile card sync: create visual card layout for small screens without moving inputs
                                        function createCardForUid(uid){
                                            if($('#garantia-card-'+uid).length) return;
                                            var $tr = $('#garantias-table tbody tr[data-uid="'+uid+'"]');
                                            if (!$tr.length) return;
                                            var $card = $('<div class="card mb-2" id="garantia-card-'+uid+'"></div>');
                                            var $body = $('<div class="card-body"></div>');
                                            
                                            // Get references to actual inputs
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
                                            var $existingFotos = $tr.find('.existing-fotos-list');

                                            // Row 1: Cantidad y Descripción
                                            var $row = $('<div class="row"></div>');
                                            $row.append($('<div class="col-4 form-group"></div>')
                                                .append('<label class="d-block">Cant.</label>')
                                                .append($cantidad.clone().attr('id', 'mobile_cant_'+uid)));
                                            $row.append($('<div class="col-8 form-group"></div>')
                                                .append('<label class="d-block">Descripción</label>')
                                                .append($nombre.clone().attr('id', 'mobile_nombre_'+uid)));
                                            $body.append($row);

                                            // Row 2: Modelo y Marca
                                            var $row2 = $('<div class="row"></div>');
                                            $row2.append($('<div class="col-6 form-group"></div>')
                                                .append('<label class="d-block">Modelo</label>')
                                                .append($modelo.clone().attr('id', 'mobile_modelo_'+uid)));
                                            $row2.append($('<div class="col-6 form-group"></div>')
                                                .append('<label class="d-block">Marca</label>')
                                                .append($marca.clone().attr('id', 'mobile_marca_'+uid)));
                                            $body.append($row2);

                                            // Row 3: Nº Serie y Avalúo C$
                                            var $row3 = $('<div class="row"></div>');
                                            $row3.append($('<div class="col-6 form-group"></div>')
                                                .append('<label class="d-block">Nº Serie</label>')
                                                .append($nserie.clone().attr('id', 'mobile_serie_'+uid)));
                                            $row3.append($('<div class="col-6 form-group"></div>')
                                                .append('<label class="d-block">Avalúo C$</label>')
                                                .append($costo.clone().attr('id', 'mobile_costo_'+uid)));
                                            $body.append($row3);

                                            // Row 3b: Avalúo US$
                                            var $costoDolares = $tr.find('.cost-dolares[data-uid="'+uid+'"]').clone();
                                            var $row3b = $('<div class="row"></div>');
                                            $row3b.append($('<div class="col-12 form-group"></div>')
                                                .append('<label class="d-block">Avalúo US$</label>')
                                                .append($costoDolares));
                                            $body.append($row3b);

                                            // Row 4: Estado/Tiempo de vida
                                            $body.append($('<div class="form-group"></div>')
                                                .append('<label class="d-block">Estado</label>')
                                                .append($tiempo.clone().attr('id', 'mobile_tiempo_'+uid)));

                                            // Row 5: Fotos (incluyendo fotos existentes)
                                            var $photoDiv = $('<div class="form-group"></div>')
                                                .append('<label class="d-block">Fotos</label>');
                                            
                                            // Si existen fotos guardadas, mostrarlas primero
                                            if ($existingFotos.length > 0) {
                                                $photoDiv.append($existingFotos.clone().attr('id', 'mobile_existing_'+uid));
                                            }
                                            
                                            $photoDiv.append($fotoInput.clone().attr('id', 'mobile_fotos_'+uid))
                                                .append($fotosFilename.clone().attr('id', 'mobile_filename_'+uid))
                                                .append($fotosList.clone().attr('id', 'mobile_fotoslist_'+uid));
                                            $body.append($photoDiv);

                                            // Actions
                                            var $actions = $('<div class="text-right mt-3"></div>');
                                            var $remove = $('<button type="button" class="btn btn-sm btn-danger remove-row">Eliminar</button>');
                                            $actions.append($remove);
                                            $body.append($actions);

                                            $card.append($body);
                                            $('#garantias-cards').append($card);
                                            
                                            // Sync mobile inputs with table inputs
                                            syncMobileCardToTable(uid);
                                        }
                                        
                                        // Function to keep mobile card inputs in sync with table inputs
                                        function syncMobileCardToTable(uid){
                                            var $tr = $('#garantias-table tbody tr[data-uid="'+uid+'"]');
                                            var mobileInputs = {
                                                cantidad: $('#mobile_cant_'+uid),
                                                nombre: $('#mobile_nombre_'+uid),
                                                modelo: $('#mobile_modelo_'+uid),
                                                marca: $('#mobile_marca_'+uid),
                                                serie: $('#mobile_serie_'+uid),
                                                costo: $('#mobile_costo_'+uid),
                                                tiempo: $('#mobile_tiempo_'+uid)
                                            };
                                            
                                            // Update mobile when table changes
                                            $tr.find('input[name="cantidad[]"]').on('change', function(){
                                                mobileInputs.cantidad.val($(this).val());
                                            });
                                            $tr.find('input[name="nombre[]"]').on('change', function(){
                                                mobileInputs.nombre.val($(this).val());
                                            });
                                            $tr.find('input[name="modelo[]"]').on('change', function(){
                                                mobileInputs.modelo.val($(this).val());
                                            });
                                            $tr.find('input[name="marca[]"]').on('change', function(){
                                                mobileInputs.marca.val($(this).val());
                                            });
                                            $tr.find('input[name="n_serie[]"]').on('change', function(){
                                                mobileInputs.serie.val($(this).val());
                                            });
                                            $tr.find('input[name="costo[]"]').on('change', function(){
                                                mobileInputs.costo.val($(this).val());
                                            });
                                            $tr.find('input[name="tiempo_vida[]"]').on('change', function(){
                                                mobileInputs.tiempo.val($(this).val());
                                            });
                                            
                                            // Update table when mobile changes
                                            mobileInputs.cantidad.on('change', function(){
                                                $tr.find('input[name="cantidad[]"]').val($(this).val()).trigger('change');
                                            });
                                            mobileInputs.nombre.on('change', function(){
                                                $tr.find('input[name="nombre[]"]').val($(this).val());
                                            });
                                            mobileInputs.modelo.on('change', function(){
                                                $tr.find('input[name="modelo[]"]').val($(this).val());
                                            });
                                            mobileInputs.marca.on('change', function(){
                                                $tr.find('input[name="marca[]"]').val($(this).val());
                                            });
                                            mobileInputs.serie.on('change', function(){
                                                $tr.find('input[name="n_serie[]"]').val($(this).val());
                                            });
                                            mobileInputs.costo.on('change', function(){
                                                $tr.find('input[name="costo[]"]').val($(this).val()).trigger('change');
                                            });
                                            mobileInputs.tiempo.on('change', function(){
                                                $tr.find('input[name="tiempo_vida[]"]').val($(this).val());
                                            });
                                        }


                                        // Build mobile cards for all existing rows on load and on resize
                                        function buildMobileCards(){
                                            $('#garantias-table tbody tr').not('.total-row').each(function(){
                                                var uid = $(this).data('uid');
                                                if (uid !== undefined && $(window).width() < 768) {
                                                    createCardForUid(uid);
                                                }
                                            });
                                        }

                                        $(function(){
                                            // Build cards on initial load if on mobile
                                            if ($(window).width() < 768) {
                                                buildMobileCards();
                                            }
                                            var resizeTimer = null;
                                            $(window).on('resize', function(){ 
                                                clearTimeout(resizeTimer); 
                                                resizeTimer = setTimeout(function(){ 
                                                    if ($(window).width() < 768) {
                                                        buildMobileCards();
                                                    } else {
                                                        // Clear cards if resizing to desktop
                                                        $('#garantias-cards').empty();
                                                    }
                                                }, 200); 
                                            });
                                        });

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

