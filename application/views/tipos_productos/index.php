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
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <button id="btnNewTipo" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo Tipo</button>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                        <div class="table-responsive">
                        <style>
                            .table-compact td, .table-compact th{ padding: .12rem .28rem; vertical-align: middle; font-size: .72rem; }
                            .table-compact thead th{ font-size: .68rem; padding: .12rem .28rem; }
                            .table-compact .btn{ padding: .10rem .24rem; font-size: .68rem; }
                            /* truncate long cells and keep actions column narrow */
                            .table-compact td.tipo-nombre, .table-compact td.tipo-monto, .table-compact td.tipo-clasificacion{ max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                            .table-compact td:last-child{ width: 120px; text-align: center; }
                        </style>
                        <table id="tipos-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Porcentaje (%)</th>
                                    <th>Clasificación</th>
                                    <th>Monto (min - max)</th>
                                    <th>Moneda</th>
                                    <th>Tasa mensual (%)</th>
                                    <th>Comisión desembolso (%)</th>
                                    <th>Plazo (min - max meses)</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tipos = (isset($tipos) && is_array($tipos)) ? $tipos : (is_object($tipos) ? (array)$tipos : array()); ?>
                                <?php foreach ($tipos as $t): ?>
                                    <?php if (!is_object($t)) { $t = (object)$t; } ?>
                                    <tr data-id="<?php echo $t->id; ?>">
                                        <td><?php echo $t->id; ?></td>
                                        <td class="tipo-nombre"><?php echo $t->nombre; ?></td>
                                        <td class="tipo-porc"><?php echo (isset($t->porcentaje)?(float)$t->porcentaje * 100:''); ?></td>
                                        <td class="tipo-clasificacion"><?php echo (isset($t->clasificacion)?$t->clasificacion:''); ?></td>
                                        <td class="tipo-monto"><?php echo (isset($t->monto_min)?number_format($t->monto_min,2,'.',''):'') . ' - ' . (isset($t->monto_max)?number_format($t->monto_max,2,'.',''):''); ?></td>
                                        <td class="tipo-moneda"><?php echo (isset($t->moneda) && $t->moneda === 'NIO' ? 'Córdobas (NIO)' : 'Dólares (USD)'); ?></td>
                                        <td class="tipo-tasa"><?php echo (isset($t->tasa_mensual)?(float)$t->tasa_mensual * 100:''); ?></td>
                                        <td class="tipo-comision"><?php echo (isset($t->comision_desembolso)?(float)$t->comision_desembolso * 100:''); ?></td>
                                        <td class="tipo-plazo"><?php echo (isset($t->plazo_min)?$t->plazo_min:'') . ' - ' . (isset($t->plazo_max)?$t->plazo_max:''); ?></td>
                                        <td><?php echo ($t->estado==1? '<span class="badge badge-success">ACTIVO</span>':'<span class="badge badge-warning">INACTIVO</span>'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-edit" data-id="<?php echo $t->id; ?>">Editar</button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $t->id; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="tipoModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tipo de Producto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="tipo_id" value="" />
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" id="tipo_nombre" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Moneda</label>
                                <select id="tipo_moneda" class="form-control">
                                    <option value="USD">Dólares (USD)</option>
                                    <option value="NIO">Córdobas (NIO)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Clasificación</label>
                                <select id="tipo_clasificacion" class="form-control">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Negocios">Negocios</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Viviendo o Hipotecario">Viviendo o Hipotecario</option>
                                    <option value="Vehiculos Usados">Vehiculos Usados</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Porcentaje (ej: 2.5 = 2.5%)</label>
                                    <input type="number" step="0.01" id="tipo_porcentaje" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label id="label_monto_min">Monto mínimo (USD)</label>
                                    <input type="number" step="0.01" id="tipo_monto_min" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label id="label_monto_max">Monto máximo (USD)</label>
                                    <input type="number" step="0.01" id="tipo_monto_max" class="form-control" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Tasa mensual (ej: 1.5 = 1.5%)</label>
                                    <input type="number" step="0.01" id="tipo_tasa_mensual" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Comisión desembolso (ej: 1.0 = 1%)</label>
                                    <input type="number" step="0.01" id="tipo_comision_desembolso" class="form-control" />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Plazo min (meses)</label>
                                    <input type="number" step="1" id="tipo_plazo_min" class="form-control" />
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Plazo max (meses)</label>
                                    <input type="number" step="1" id="tipo_plazo_max" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" id="tipo_save" class="btn btn-primary">Guardar</button>
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
    </div>
</div>

<script>
    jQuery(function($){
        $('#btnNewTipo').on('click', function(){
            $('#tipo_id').val('');
            $('#tipo_nombre').val('');
            $('#tipo_porcentaje').val('');
            $('#tipo_clasificacion').val('');
            $('#tipo_moneda').val('USD');
            $('#tipo_monto_min').val('');
            $('#tipo_monto_max').val('');
            $('#label_monto_min').text('Monto mínimo (USD)');
            $('#label_monto_max').text('Monto máximo (USD)');
            $('#tipoModal').modal('show');
        });

        $(document).on('click', '.btn-edit', function(){
            var id = $(this).data('id');
            if (!id) return;
            $.getJSON('<?php echo base_url('tipos_productos/get_ajax/'); ?>' + id)
            .done(function(resp){
                if (!resp || !resp.status){ alert((resp && resp.message) ? resp.message : 'Error al obtener registro'); return; }
                var t = resp.tipo;
                $('#tipo_id').val(t.id);
                $('#tipo_nombre').val(t.nombre || '');
                $('#tipo_clasificacion').val(t.clasificacion || '');
                $('#tipo_porcentaje').val((t.porcentaje !== null && t.porcentaje !== undefined) ? (parseFloat(t.porcentaje) * 100) : '');
                $('#tipo_monto_min').val(t.monto_min || '');
                $('#tipo_monto_max').val(t.monto_max || '');
                $('#tipo_tasa_mensual').val((t.tasa_mensual !== null && t.tasa_mensual !== undefined) ? (parseFloat(t.tasa_mensual) * 100) : '');
                $('#tipo_comision_desembolso').val((t.comision_desembolso !== null && t.comision_desembolso !== undefined) ? (parseFloat(t.comision_desembolso) * 100) : '');
                $('#tipo_plazo_min').val(t.plazo_min || '');
                $('#tipo_plazo_max').val(t.plazo_max || '');
                if (t.moneda && t.moneda === 'NIO') {
                    $('#tipo_moneda').val('NIO');
                    $('#label_monto_min').text('Monto mínimo (NIO)');
                    $('#label_monto_max').text('Monto máximo (NIO)');
                } else {
                    $('#tipo_moneda').val('USD');
                    $('#label_monto_min').text('Monto mínimo (USD)');
                    $('#label_monto_max').text('Monto máximo (USD)');
                }
                $('#tipoModal').modal('show');
            }).fail(function(){ alert('Error en la petición'); });
                // Cambiar etiquetas de monto según moneda seleccionada
                $('#tipo_moneda').on('change', function(){
                    var moneda = $(this).val();
                    if (moneda === 'NIO') {
                        $('#label_monto_min').text('Monto mínimo (NIO)');
                        $('#label_monto_max').text('Monto máximo (NIO)');
                    } else {
                        $('#label_monto_min').text('Monto mínimo (USD)');
                        $('#label_monto_max').text('Monto máximo (USD)');
                    }
                });
        });

        // Delete via AJAX
        $(document).on('click', '.btn-delete', function(){
            var id = $(this).data('id');
            if (!id) return;
            if (!confirm('Confirmar eliminación')) return;
            $.post('<?php echo base_url('tipos_productos/del_ajax'); ?>', { id: id })
            .done(function(resp){
                try{ var j = (typeof resp === 'object') ? resp : JSON.parse(resp); } catch(e){ alert('Respuesta inválida'); return; }
                if (j && j.status){
                    // remove row or reload
                    $('tr[data-id="'+id+'"]').fadeOut(200, function(){ $(this).remove(); });
                } else {
                    alert((j && j.message) ? j.message : 'Error al eliminar');
                }
            }).fail(function(){ alert('Error en la petición'); });
        });

        $('#tipo_save').on('click', function(){
            var id = $('#tipo_id').val();
            var nombre = $('#tipo_nombre').val().trim();
            var clasificacion = $('#tipo_clasificacion').val();
            var porc = parseFloat($('#tipo_porcentaje').val()) || 0;
            var monto_min = $('#tipo_monto_min').val();
            var monto_max = $('#tipo_monto_max').val();
            var tasa = parseFloat($('#tipo_tasa_mensual').val()) || 0;
            var com = parseFloat($('#tipo_comision_desembolso').val()) || 0;
            var plazo_min = $('#tipo_plazo_min').val();
            var plazo_max = $('#tipo_plazo_max').val();
            if(nombre.length < 2){ alert('Ingrese un nombre válido'); return; }
            var url = id ? '<?php echo base_url('tipos_productos/edit_ajax'); ?>' : '<?php echo base_url('tipos_productos/add_ajax'); ?>';
            var data = { nombre: nombre, porcentaje: porc/100, clasificacion: clasificacion, monto_min: monto_min, monto_max: monto_max, tasa_mensual: tasa/100, comision_desembolso: com/100, plazo_min: plazo_min, plazo_max: plazo_max };
            if(id) data.id = id;
            $.post(url, data).done(function(resp){
                try{ var j = (typeof resp === 'object') ? resp : JSON.parse(resp); } catch(e){ alert('Respuesta inválida'); return; }
                if(j && j.status){
                    // simple: reload page to show changes
                    location.reload();
                } else {
                    alert((j && j.message) ? j.message : 'Error al guardar');
                }
            }).fail(function(){ alert('Error en la petición'); });
        });
    });
</script>
