<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-file-contract'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo isset($titulo) ? $titulo : 'Contratos'; ?></h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : ''; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <a href="<?php echo base_url('planescredito'); ?>" class="btn btn-outline-secondary">Volver a Planes</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Solicitudes / Préstamos aprobados</h6>
                        <div class="small text-muted">Seleccione un préstamo y genere su contrato</div>
                    </div>

                    <style>
                        /* Match compact table styles used by solicitudes list so dimensions align */
                        #prestamos-table.table-compact td, #prestamos-table.table-compact th{
                            padding: .12rem .28rem !important;
                            vertical-align: middle !important;
                            font-size: .72rem !important;
                            line-height: 1.02 !important;
                            white-space: nowrap !important;
                        }
                        #prestamos-table.table-compact thead th{ font-size: .7rem !important; padding: .18rem .28rem !important; }
                        #prestamos-table.table-compact .btn{ padding: .12rem .28rem !important; font-size: .72rem !important; min-width: auto !important; }
                        /* Truncate Cliente column to prevent layout stretching */
                        #prestamos-table th:nth-child(2), #prestamos-table td:nth-child(2){
                            max-width: 180px; overflow: hidden; text-overflow: ellipsis;
                        }
                        /* Narrow actions column */
                        #prestamos-table th:nth-child(5), #prestamos-table td:nth-child(5){ width: 120px; white-space: nowrap; }
                    </style>
                    <div class="table-responsive">
                        <table id="prestamos-table" class="table table-sm table-striped table-bordered table-compact" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th style="width:200px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5" class="text-center small text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal: seleccionar plantilla y generar contrato -->
            <div class="modal fade" id="contractModal" tabindex="-1" role="dialog" aria-labelledby="contractModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="contractModalLabel">Generar Contrato</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form id="contractForm">
                                <input type="hidden" name="idprestamo" id="c_idprestamo" value="">
                                <div class="form-group">
                                    <label>Plantilla</label>
                                    <select id="c_template" name="template_id" class="form-control"></select>
                                </div>
                                <div class="form-group">
                                    <label>Vista previa</label>
                                    <div id="c_preview" style="border:1px solid #e6e6e6;padding:12px;border-radius:4px;min-height:120px;background:#fff;"></div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" id="c_generate_btn" class="btn btn-primary">Generar Contrato</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function($){
        var base = '<?php echo base_url(); ?>';

        function loadPrestamos(){
            $.get(base + 'contratos/get_prestamos', function(resp){
                try{ var r = JSON.parse(resp); } catch(e){ r = resp; }
                if (!r || !r.status){ $('#prestamos-table tbody').html('<tr><td colspan="5" class="text-center text-danger">Error cargando préstamos</td></tr>'); return; }
                var rows = '';
                    if (Array.isArray(r.prestamos) && r.prestamos.length){
                    r.prestamos.forEach(function(p){
                        var cliente = p.cliente_nombre || p.cliente || '';
                        var monto = p.monto_credito ? ('$' + Number(p.monto_credito).toFixed(2)) : '';
                        var fecha = p.fecha_credito || '';
                        rows += '<tr>' +
                            '<td>' + (p.idprestamo || '') + '</td>' +
                            '<td>' + cliente + '</td>' +
                            '<td>' + monto + '</td>' +
                            '<td>' + fecha + '</td>' +
                            '<td>';
                        // Always show both actions. Enable/disable based on availability.
                        var generateDisabled = (!p.idprestamo);
                        var downloadDisabled = (!p.has_contract);
                        var genBtn = '<button class="btn btn-sm btn-primary mr-1 btn-generate" data-id="'+ (p.idprestamo || '') +'"' + (generateDisabled ? ' disabled title="No hay préstamo asociado"' : '') + '>Generar</button>';
                        var dlClass = 'btn btn-sm btn-success mr-1' + (downloadDisabled ? ' disabled' : '');
                        var dlHref = base + 'contratos/download/' + (p.idprestamo || '');
                        var dlBtn = '<a class="' + dlClass + '" href="' + (downloadDisabled ? 'javascript:void(0);' : dlHref) + '"' + (downloadDisabled ? ' title="Aún no se ha generado el contrato"' : ' target="_blank"') + '>Descargar</a>';
                        var viewBtn = '<a class="btn btn-sm btn-secondary" href="' + (p.idprestamo ? base + 'contratos/view/' + p.idprestamo : 'javascript:void(0);') + '"' + (p.idprestamo ? '' : ' title="No hay vista disponible"') + '>Ver</a>';
                        rows += genBtn + dlBtn + viewBtn;
                        rows += '</td></tr>';
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center small text-muted">No hay préstamos disponibles</td></tr>';
                }
                $('#prestamos-table tbody').html(rows);
            });
        }

        function loadTemplates(selectEl, previewEl){
            $.get(base + 'contratos/get_templates', function(resp){
                try{ var r = JSON.parse(resp); } catch(e){ r = resp; }
                if (!r || !r.status) return;
                var opts = '<option value="">-- Seleccione plantilla --</option>';
                r.templates.forEach(function(t){ opts += '<option value="'+t.id+'">'+t.name+'</option>'; });
                $(selectEl).html(opts);

                $(selectEl).off('change').on('change', function(){
                    var id = $(this).val();
                    if (!id) { $(previewEl).html(''); return; }
                    // fetch preview from server
                    $.get(base + 'contratos/preview', { idprestamo: $('#c_idprestamo').val(), template_id: id }, function(resp){
                        try{ var pr = JSON.parse(resp); } catch(e){ pr = resp; }
                        if (pr && pr.status){ $(previewEl).html(pr.html); }
                        else { $(previewEl).html('<div class="text-danger">Vista previa no disponible</div>'); }
                    });
                });
            });
        }

        $(document).on('click', '.btn-generate', function(){
            var id = $(this).data('id');
            $('#c_idprestamo').val(id);
            $('#c_template').html('<option>...</option>');
            $('#c_preview').html('Cargando plantillas...');
            loadTemplates('#c_template', '#c_preview');
            $('#contractModal').modal('show');
        });

        $('#c_generate_btn').on('click', function(){
            var tpl = $('#c_template').val();
            var tpl_name = $('#c_template option:selected').text();
            if (!tpl) { alert('Seleccione una plantilla antes de generar el contrato.'); return; }
            if (!confirm('Va a generar el contrato con la plantilla: "' + tpl_name + '". ¿Desea continuar?')) return;

            var form = $('#contractForm');
            var data = form.serialize();
            $('#c_generate_btn').prop('disabled', true).text('Generando...');
            $.post(base + 'contratos/generate', data, function(resp){
                try{ var r = JSON.parse(resp); } catch(e){ r = resp; }
                $('#c_generate_btn').prop('disabled', false).text('Generar Contrato');
                if (!r || !r.status){ alert((r && r.message) ? r.message : 'Error generando contrato'); return; }
                $('#contractModal').modal('hide');
                loadPrestamos();
                alert('Contrato generado correctamente (Plantilla: ' + tpl_name + ')');
            });
        });

        $(document).ready(function(){ loadPrestamos(); });
    })(jQuery);
</script>
