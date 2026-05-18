<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
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
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Planes de Pago Generados</h3>
                        </div>
                        <div class="card-body">
                            <form method="get" class="form-inline mb-3">
                                <label class="mr-2">Fecha desde:</label>
                                <input type="date" name="start_date" class="form-control form-control-sm mr-3" value="<?php echo isset($filter_start_date) ? $filter_start_date : ''; ?>">
                                <label class="mr-2">Buscar:</label>
                                <input type="text" id="planes_search" name="q" class="form-control form-control-sm mr-3" placeholder="Nombre o # solicitud" value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
                                <label class="mr-2">Fecha hasta:</label>
                                <input type="date" name="end_date" class="form-control form-control-sm mr-3" value="<?php echo isset($filter_end_date) ? $filter_end_date : ''; ?>">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>
                                <a href="<?php echo site_url('planescredito'); ?>" class="btn btn-sm btn-secondary">Limpiar</a>
                            </form>

                            <div class="table-responsive-sm">
                                <?php
                                // --- Controles de paginación ---
                                $total_rows = isset($total_rows) ? $total_rows : 0;
                                $per_page = isset($per_page) ? $per_page : 25;
                                $current_page = isset($current_page) ? $current_page : 1;
                                $total_pages = $per_page > 0 ? ceil($total_rows / $per_page) : 1;
                                $base_url = site_url('planescredito');
                                $query_params = $_GET;
                                unset($query_params['page']);
                                $query_str = http_build_query($query_params);
                                function page_url($page, $base_url, $query_str) {
                                    $url = $base_url;
                                    if ($query_str !== '') {
                                        $url .= '?' . $query_str . '&page=' . $page;
                                    } else {
                                        $url .= '?page=' . $page;
                                    }
                                    return $url;
                                }
                                if ($total_pages > 1) {
                                    echo '<nav aria-label="Paginación de créditos"><ul class="pagination pagination-sm">';
                                    // Prev
                                    $prev_page = max(1, $current_page - 1);
                                    echo '<li class="page-item'.($current_page==1?' disabled':'').'"><a class="page-link" href="'.page_url($prev_page, $base_url, $query_str).'">&laquo;</a></li>';
                                    // Page numbers (show max 7)
                                    $start = max(1, $current_page - 3);
                                    $end = min($total_pages, $current_page + 3);
                                    if ($start > 1) { echo '<li class="page-item"><a class="page-link" href="'.page_url(1, $base_url, $query_str).'">1</a></li>'; if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; }
                                    for ($i = $start; $i <= $end; $i++) {
                                        echo '<li class="page-item'.($i==$current_page?' active':'').'"><a class="page-link" href="'.page_url($i, $base_url, $query_str).'">'.$i.'</a></li>';
                                    }
                                    if ($end < $total_pages) { if ($end < $total_pages-1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; echo '<li class="page-item"><a class="page-link" href="'.page_url($total_pages, $base_url, $query_str).'">'.$total_pages.'</a></li>'; }
                                    // Next
                                    $next_page = min($total_pages, $current_page + 1);
                                    echo '<li class="page-item'.($current_page==$total_pages?' disabled':'').'"><a class="page-link" href="'.page_url($next_page, $base_url, $query_str).'">&raquo;</a></li>';
                                    echo '</ul></nav>';
                                }
                                ?>
                                <style>
                                    /* Match compact table styles used by solicitudes list so dimensions align */
                                    .table-compact td, .table-compact th{ padding: .12rem .28rem !important; vertical-align: middle !important; font-size: .72rem !important; line-height: 1.02 !important; white-space: nowrap !important; }
                                    .table-compact thead th{ font-size: .7rem !important; padding: .18rem .28rem !important; }
                                    .table-compact .btn{ padding: .12rem .28rem !important; font-size: .72rem !important; min-width: auto !important; }
                                    .col-small { width: 70px; }
                                    .col-xsmall { width: 50px; }
                                    .col-medium { width: 110px; }
                                    /* Truncate Cliente column to prevent layout stretching */
                                    table.table-compact th:nth-child(3), table.table-compact td:nth-child(3){ max-width: 180px; overflow: hidden; text-overflow: ellipsis; }
                                    /* Narrow actions column */
                                    table.table-compact th:last-child, table.table-compact td:last-child { width: 120px; white-space: nowrap; }
                                    /* Hide Desemb. column (5th column) */
                                    table.table-compact th:nth-child(5), table.table-compact td:nth-child(5) { display: none; }
                                    .estado-badge { display:inline-block; min-width:108px; padding:.18rem .5rem; border-radius:999px; font-size:.68rem; font-weight:700; text-align:center; }
                                    .estado-vigente { background:#e7f6ea; color:#17643a; }
                                    .estado-al-dia { background:#e6f4ff; color:#0b5cad; }
                                    .estado-mora-temprana { background:#fff4d6; color:#8a5a00; }
                                    .estado-mora { background:#ffe6bf; color:#9a4d00; }
                                    .estado-mora-media { background:#ffd9b3; color:#994400; }
                                    .estado-mora-alta { background:#ffc9c9; color:#9f1d1d; }
                                    .estado-riesgo { background:#f7c6d9; color:#8f1655; }
                                    .estado-dudosa { background:#e4cdfc; color:#5a2f91; }
                                    .estado-critica { background:#d9d0ff; color:#442f8f; }
                                    .estado-irrecuperable { background:#d6d6d6; color:#444; }
                                    .estado-castigado { background:#2f2f2f; color:#fff; }
                                    .estado-anulado { background:#7b7b7b; color:#fff; }
                                </style>

                                <?php
                                $estado_class_map = array(
                                    'VIGENTE' => 'estado-vigente',
                                    'AL DÍA' => 'estado-al-dia',
                                    'MORA TEMPRANA' => 'estado-mora-temprana',
                                    'MORA' => 'estado-mora',
                                    'MORA MEDIA' => 'estado-mora-media',
                                    'MORA ALTA' => 'estado-mora-alta',
                                    'CARTERA EN RIESGO' => 'estado-riesgo',
                                    'CARTERA DUDOSA' => 'estado-dudosa',
                                    'CARTERA CRÍTICA' => 'estado-critica',
                                    'CARTERA IRRECUPERABLE' => 'estado-irrecuperable',
                                    'CASTIGADO' => 'estado-castigado',
                                    'ANULADO' => 'estado-anulado'
                                );
                                ?>

                                    <table class="table table-sm data-table table-striped table-bordered table-hover table-compact">
                                        <thead>
                                            <tr>
                                                <th class="col-xsmall">#</th>
                                                <th class="col-small">Solicitud</th>
                                                <th>Cliente</th>
                                                <th class="col-medium">Monto</th>
                                                <th class="col-medium">Desemb.</th>
                                                <th class="col-xsmall">Int%</th>
                                                <th class="col-xsmall">Ctas</th>
                                                <th class="col-medium">Frecuencia</th>
                                                <th class="col-medium">Fecha de Crédito</th>
                                                <th>Creador</th>
                                                <th>Asesor/Ruta</th>
                                                <th>Estado</th>
                                                <th>Cuota actual</th>
                                                <th class="col-xsmall"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($prestamos as $p): ?>
                                            <tr>
                                                <?php
                                                    // Safe fallbacks to avoid undefined property notices
                                                    $cliente_name = '';
                                                    if (isset($p->cliente_nombre) && $p->cliente_nombre !== '') {
                                                        $cliente_name = $p->cliente_nombre;
                                                    } elseif (isset($p->cliente) && $p->cliente !== '') {
                                                        $cliente_name = $p->cliente;
                                                    } elseif (isset($p->nombre_cliente) && $p->nombre_cliente !== '') {
                                                        $cliente_name = $p->nombre_cliente;
                                                    } else {
                                                        // try composing from name parts if present
                                                        $parts = array();
                                                        if (!empty($p->primer_nombre)) $parts[] = $p->primer_nombre;
                                                        if (!empty($p->segundo_nombre)) $parts[] = $p->segundo_nombre;
                                                        if (!empty($p->primer_apellido)) $parts[] = $p->primer_apellido;
                                                        if (!empty($p->segundo_apellido)) $parts[] = $p->segundo_apellido;
                                                        $cliente_name = implode(' ', $parts);
                                                    }

                                                    $monto_val = isset($p->monto) ? $p->monto : (isset($p->monto_credito) ? $p->monto_credito : 0);
                                                    $desembolsado_val = isset($p->desembolsado) ? $p->desembolsado : (isset($p->monto_desembolsado) ? $p->monto_desembolsado : 0);
                                                    $tasa_val = null;
                                                    if (isset($p->tasa)) {
                                                        $tasa_val = $p->tasa;
                                                    } elseif (isset($p->interes_credito)) {
                                                        // some places store as fraction (0.05) or percent (5)
                                                        $tasa_val = $p->interes_credito;
                                                    } elseif (isset($p->interes_credito) && is_numeric($p->interes_credito)) {
                                                        $tasa_val = $p->interes_credito;
                                                    }
                                                    $cuotas_val = isset($p->cuotas) ? $p->cuotas : (isset($p->numero_coutas) ? $p->numero_coutas : '');
                                                    $fecha_val = isset($p->fecha) ? $p->fecha : (isset($p->fecha_credito) ? $p->fecha_credito : '');
                                                ?>
                                                <td><?php echo isset($p->idprestamo) ? $p->idprestamo : ''; ?></td>
                                                <td><?php echo isset($p->idsolicitud) ? $p->idsolicitud : ''; ?></td>
                                                <td><?php echo htmlspecialchars($cliente_name); ?></td>
                                                <td><?php echo '$' . number_format(floatval($monto_val),2); ?></td>
                                                <td><?php echo '$' . number_format(floatval($desembolsado_val),2); ?></td>
                                                <td><?php 
                                                    // Mostrar tasa como porcentaje
                                                    if (is_numeric($tasa_val)) {
                                                        // Si es menor a 1, asume que es fracción y multiplica por 100
                                                        $tasa_pct = (floatval($tasa_val) < 1 ? floatval($tasa_val) * 100 : floatval($tasa_val));
                                                        echo rtrim(rtrim(number_format($tasa_pct,2), '0'), '.') . '%';
                                                    } else {
                                                        echo isset($tasa_val) ? $tasa_val : '';
                                                    }
                                                ?></td>
                                                <td><?php echo $cuotas_val; ?></td>
                                                <td><?php 
                                                    $forma_pago_val = isset($p->forma_pago) ? intval($p->forma_pago) : 3;
                                                    $frecuencias = array(0 => 'Diario', 1 => 'Semanal', 2 => 'Quincenal', 3 => 'Mensual');
                                                    echo isset($frecuencias[$forma_pago_val]) ? $frecuencias[$forma_pago_val] : 'Mensual';
                                                ?></td>
                                                <td><?php echo $fecha_val; ?></td>
                                                <td><?php echo isset($p->creado_por) ? htmlspecialchars($p->creado_por) : ''; ?></td>
                                                <td><?php echo isset($p->nombre_asesor) ? htmlspecialchars($p->nombre_asesor) : (isset($p->ruta) ? htmlspecialchars($p->ruta) : ''); ?></td>
                                                <?php $estado_credito = isset($p->estado_credito) ? $p->estado_credito : 'VIGENTE'; ?>
                                                <td>
                                                    <span class="estado-badge <?php echo isset($estado_class_map[$estado_credito]) ? $estado_class_map[$estado_credito] : 'estado-vigente'; ?>">
                                                        <?php echo htmlspecialchars($estado_credito); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo isset($p->cuota_actual) ? $p->cuota_actual : ''; ?></td>
                                                <td>
                                                    <?php
                                                        $printed = isset($p->pdf_printed_count) ? intval($p->pdf_printed_count) : 0;
                                                        if ($printed > 0) {
                                                            $btn_class = 'btn btn-sm btn-warning';
                                                            $btn_label = 'Reimprimir';
                                                        } else {
                                                            $btn_class = 'btn btn-sm btn-primary';
                                                            $btn_label = 'Imprimir';
                                                        }
                                                    ?>
                                                    <a href="<?php echo site_url('planescredito/pdf/'.$p->idprestamo.'?download=1'); ?>" class="<?php echo $btn_class; ?>" target="_blank"><?php echo $btn_label; ?></a>
                                                    <a href="<?php echo site_url('planescredito/estado_cuenta/'.$p->idprestamo); ?>" class="btn btn-sm btn-info ml-1" target="_blank">Estado de cuenta</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
        </div>
    </footer>
</div>


<!-- Modal for viewing plan details -->
<div class="modal fade" id="viewPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle Plan de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="planDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($){
        $(document).on('click', '.btn-view-plan', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('#planDetails').html('<p>Cargando...</p>');
            $('#viewPlanModal').modal('show');
            $.ajax({
                url: '<?php echo base_url('planescredito/get/'); ?>' + id,
                method: 'GET',
                dataType: 'json'
            }).done(function(resp){
                if (!resp.status) { $('#planDetails').html('<div class="alert alert-danger">'+resp.message+'</div>'); return; }
                var html = '';
                html += '<p><strong>Crédito:</strong> ' + (resp.prestamo.idprestamo || '') + '</p>';
                html += '<p><strong>Solicitud:</strong> ' + (resp.prestamo.idsolicitud || '') + '</p>';
                html += '<p><strong>Monto:</strong> ' + (parseFloat(resp.prestamo.monto_credito).toFixed(2)) + '</p>';
                html += '<hr>';
                html += '<table class="table table-sm table-bordered"><thead><tr><th>#</th><th>Fecha</th><th>Capital</th><th>Interés</th><th>Cuota</th><th>Saldo</th></tr></thead><tbody>';
                if (Array.isArray(resp.cuotas) && resp.cuotas.length) {
                    resp.cuotas.forEach(function(c){
                        html += '<tr>';
                        html += '<td>' + c.numero + '</td>';
                        html += '<td>' + c.fecha_vencimiento + '</td>';
                        html += '<td>' + parseFloat(c.principal).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(c.interes).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(c.cuota).toFixed(2) + '</td>';
                        html += '<td>' + parseFloat(c.saldo).toFixed(2) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="6">No hay cuotas</td></tr>';
                }
                html += '</tbody></table>';
                $('#planDetails').html(html);
            }).fail(function(){
                $('#planDetails').html('<div class="alert alert-danger">Error al cargar</div>');
            });
        });
    })(jQuery);
</script>

<script>
    (function($){
        // Client-side search: filter rows by solicitud number or client name
        function applyPlanesFilter(){
            var q = ($('#planes_search').val() || '').toString().toLowerCase().trim();
            if(q === ''){ jQuery('table.data-table tbody tr').show(); return; }
            jQuery('table.data-table tbody tr').each(function(){
                var $tr = jQuery(this);
                var solicitud = ($tr.find('td').eq(1).text() || '').toString().toLowerCase();
                var cliente = ($tr.find('td').eq(2).text() || '').toString().toLowerCase();
                if(solicitud.indexOf(q) !== -1 || cliente.indexOf(q) !== -1){
                    $tr.show();
                } else {
                    $tr.hide();
                }
            });
        }

        jQuery(function(){
            jQuery(document).on('input', '#planes_search', function(){ applyPlanesFilter(); });
            // apply initial filter if q provided server-side
            applyPlanesFilter();
        });
    })(jQuery);
</script>

<!-- Preview Modal -->
<div class="modal fade" id="contratoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Previsualización de Contrato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="contratoPreviewModalBody" style="max-height:70vh; overflow:auto;">
                <p>Cargando...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal: Select Contract Template -->
<div class="modal fade" id="contratoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Generar Contrato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="contratoModalBody">
                    <p>Cargando plantillas...</p>
                </div>
            </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="btnPreviewContrato">Editar / Previsualizar</button>
                <button type="button" class="btn btn-primary" id="btnGenerateContrato">Generar Contrato</button>
            </div>
        </div>
    </div>
</div>

<!-- Editor Modal (TinyMCE) -->
<div class="modal fade" id="contratoEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Editar Contrato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <textarea id="contratoEditor" name="contratoEditor" style="min-height:400px;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnSaveContrato">Guardar</button>
                <button type="button" class="btn btn-primary" id="btnSaveAndPdf">Guardar y Generar PDF</button>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE (CDN) - use application/config/config.php $config['tinymce_key'] to set your Tiny Cloud API key -->
<?php
    // Prefer a self-hosted TinyMCE file if available, otherwise use Tiny Cloud CDN with configured key
    $local_tinymce_path = FCPATH . 'public/js/tinymce/tinymce.min.js';
    $local_tinymce_url = base_url('public/js/tinymce/tinymce.min.js');
    $tinymce_key = $this->config->item('tinymce_key') ? $this->config->item('tinymce_key') : 'no-api-key';
    if (file_exists($local_tinymce_path)) {
        echo '<script src="' . $local_tinymce_url . '" referrerpolicy="origin"></script>';
    } else {
        // if ($tinymce_key === 'no-api-key') {
        //     echo '<div class="container mt-2"><div class="alert alert-warning">';
        //     echo '<strong>TinyMCE API key missing:</strong> editors will be read-only.';
        //     echo ' Add your Tiny Cloud API key to <code>application/config/config.php</code> as <code>$config[\'tinymce_key\'] = \"YOUR_API_KEY\";</code> or place a self-hosted TinyMCE build at <code>public/js/tinymce/tinymce.min.js</code> to avoid CDN API key requirements.';
        //     echo ' See <a href="https://www.tiny.cloud/docs/tinymce/latest/invalid-api-key/" target="_blank">Tiny Cloud docs</a> for details.';
        //     echo '</div></div>';
        // }
        echo '<script src="https://cdn.tiny.cloud/1/' . $tinymce_key . '/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>';
    }
?>

<script>
    (function($){
        var currentPrestamoId = null;

        $(document).on('click', '.btn-crear-contrato', function(e){
            e.preventDefault();
            currentPrestamoId = $(this).data('id');
            $('#contratoModalBody').html('<p>Cargando plantillas...</p>');
            $('#contratoModal').modal('show');
            // fetch available templates from server (Contratos controller)
            $.get('<?php echo base_url('contratos/list_templates_from_folder'); ?>', function(resp){
                if (!resp || !resp.status) { $('#contratoModalBody').html('<div class="alert alert-danger">No se pudieron listar plantillas</div>'); return; }
                var html = '<div class="list-group">';
                if (resp.templates && resp.templates.length) {
                    resp.templates.forEach(function(t, idx){
                        html += '<label class="list-group-item">';
                        html += '<input type="radio" name="contrato_template" value="'+encodeURIComponent(t.file)+'" ' + (idx===0? 'checked':'') + '> ' + $('<div>').text(t.name).html();
                        html += '<span class="text-muted small ml-2">(' + t.size + ' bytes)</span>';
                        html += '</label>';
                    });
                } else {
                    html += '<div class="alert alert-info">No hay plantillas en la carpeta <code>Contratos/</code></div>';
                }
                html += '</div>';
                $('#contratoModalBody').html(html);
            }, 'json').fail(function(){
                $('#contratoModalBody').html('<div class="alert alert-danger">Error al solicitar plantillas</div>');
            });
        });

        $('#btnGenerateContrato').on('click', function(e){
            e.preventDefault();
            var filename = $('input[name=contrato_template]:checked').val();
            if (!filename || !currentPrestamoId) { alert('Seleccione una plantilla'); return; }
            // filename is url encoded; send via POST
            var data = { idprestamo: currentPrestamoId, filename: decodeURIComponent(filename) };
            $('#btnGenerateContrato').prop('disabled', true).text('Generando...');
            $.post('<?php echo base_url('contratos/generate_from_file'); ?>', data, function(resp){
                $('#btnGenerateContrato').prop('disabled', false).text('Generar Contrato');
                if (!resp || !resp.status) { alert('Error: ' + (resp && resp.message ? resp.message : 'No se pudo generar')); return; }
                // success: open generated contract view
                $('#contratoModal').modal('hide');
                if (resp.url) {
                    window.open(resp.url, '_blank');
                } else {
                    alert('Contrato generado');
                }
                // Optionally refresh contratos list or change UI; leave to user to refresh page
            }, 'json').fail(function(xhr, status, err){
                $('#btnGenerateContrato').prop('disabled', false).text('Generar Contrato');
                console.error('Generate failed', status, err, xhr.responseText);
                var message = xhr && xhr.responseText ? xhr.responseText : 'Error en servidor al generar contrato';
                alert('Error en servidor al generar contrato:\n' + message);
            });
        });

        // Edit / Preview button handler -> open TinyMCE editor with filled HTML
        $('#btnPreviewContrato').on('click', function(e){
            e.preventDefault();
            var filename = $('input[name=contrato_template]:checked').val();
            if (!filename || !currentPrestamoId) { alert('Seleccione una plantilla'); return; }
            var data = { idprestamo: currentPrestamoId, filename: decodeURIComponent(filename) };
            $('#btnPreviewContrato').prop('disabled', true).text('Cargando...');
            $.post('<?php echo base_url('contratos/preview_from_file'); ?>', data, function(resp){
                $('#btnPreviewContrato').prop('disabled', false).text('Editar / Previsualizar');
                if (!resp || !resp.status) { alert('Error: ' + (resp && resp.message ? resp.message : 'No se pudo previsualizar')); return; }
                // initialize TinyMCE if not yet
                if (!tinymce.get('contratoEditor')) {
                    tinymce.init({ selector:'#contratoEditor', height:600, menubar:true, plugins: 'link lists table paste table code', toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | link | code' });
                    // wait a bit then set content
                    setTimeout(function(){ tinymce.get('contratoEditor').setContent(resp.html); $('#contratoModal').modal('hide'); $('#contratoEditModal').modal('show'); }, 300);
                } else {
                    tinymce.get('contratoEditor').setContent(resp.html);
                    $('#contratoModal').modal('hide');
                    $('#contratoEditModal').modal('show');
                }
            }, 'json').fail(function(xhr, status, err){
                $('#btnPreviewContrato').prop('disabled', false).text('Editar / Previsualizar');
                console.error('Preview failed', status, err, xhr.responseText);
                var message = xhr && xhr.responseText ? xhr.responseText : 'Error en servidor al previsualizar';
                alert('Error en servidor al previsualizar:\n' + message);
            });
        });

        // Save edited content
        $('#btnSaveContrato').on('click', function(e){
            e.preventDefault();
            var content = tinymce.get('contratoEditor').getContent();
            if (!content || !currentPrestamoId) { alert('Contenido vacío o préstamo no seleccionado'); return; }
            var payload = { idprestamo: currentPrestamoId, html: content };
            $('#btnSaveContrato').prop('disabled', true).text('Guardando...');
            $.post('<?php echo base_url('contratos/save_edited'); ?>', payload, function(resp){
                $('#btnSaveContrato').prop('disabled', false).text('Guardar');
                if (!resp || !resp.status) { alert('Error: ' + (resp && resp.message ? resp.message : 'No se pudo guardar')); return; }
                alert('Contrato guardado correctamente');
            }, 'json').fail(function(xhr, status, err){
                $('#btnSaveContrato').prop('disabled', false).text('Guardar');
                console.error('Save failed', status, err, xhr.responseText);
                var message = xhr && xhr.responseText ? xhr.responseText : 'Error en servidor al guardar contrato';
                alert('Error en servidor al guardar contrato:\n' + message);
            });
        });

        // Save and generate PDF
        $('#btnSaveAndPdf').on('click', function(e){
            e.preventDefault();
            var content = tinymce.get('contratoEditor').getContent();
            if (!content || !currentPrestamoId) { alert('Contenido vacío o préstamo no seleccionado'); return; }
            var payload = { idprestamo: currentPrestamoId, html: content };
            $('#btnSaveAndPdf').prop('disabled', true).text('Generando PDF...');
            $.post('<?php echo base_url('contratos/save_edited'); ?>', payload, function(resp){
                $('#btnSaveAndPdf').prop('disabled', false).text('Guardar y Generar PDF');
                if (!resp || !resp.status) { alert('Error: ' + (resp && resp.message ? resp.message : 'No se pudo guardar')); return; }
                if (resp.pdf_url) {
                    window.open(resp.pdf_url, '_blank');
                    $('#contratoEditModal').modal('hide');
                } else if (resp.view_url) {
                    window.open(resp.view_url, '_blank');
                    $('#contratoEditModal').modal('hide');
                } else {
                    alert('Contrato guardado, pero no hay URL para PDF');
                }
            }, 'json').fail(function(xhr, status, err){
                $('#btnSaveAndPdf').prop('disabled', false).text('Guardar y Generar PDF');
                console.error('SaveAndPdf failed', status, err, xhr.responseText);
                var message = xhr && xhr.responseText ? xhr.responseText : 'Error en servidor al guardar/generar PDF';
                alert('Error en servidor al guardar/generar PDF:\n' + message);
            });
        });

    })(jQuery);
</script>
