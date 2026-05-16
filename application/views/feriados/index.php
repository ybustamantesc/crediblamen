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
                        <button id="btnNewFeriado" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo Feriado</button>
                    </div>
                </div>
            </div>

            <div class="card">
                    <div class="card-body">
                    <div class="table-responsive">
                        <style>
                            .table-compact td, .table-compact th{ padding: .12rem .28rem; vertical-align: middle; font-size: .72rem; }
                            .table-compact thead th{ font-size: .68rem; padding: .12rem .28rem; }
                            .table-compact .btn{ padding: .10rem .22rem; font-size: .68rem; }
                            /* truncate motivo to avoid stretching and keep actions narrow */
                            #feriados-table td:nth-child(3), #feriados-table th:nth-child(3){ max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                            #feriados-table td:last-child, #feriados-table th:last-child{ width: 120px; text-align: center; }
                        </style>
                        <table id="feriados-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Motivo</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feriados as $f): ?>
                                    <tr data-id="<?php echo $f->id; ?>">
                                        <td><?php echo $f->id; ?></td>
                                        <td><?php echo isset($f->fecha) ? $f->fecha : ''; ?></td>
                                        <td><?php echo isset($f->motivo) ? $f->motivo : ''; ?></td>
                                        <td><?php echo ($f->activo==1? 'Sí' : 'No'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-edit" data-id="<?php echo $f->id; ?>">Editar</button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $f->id; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="feriadoModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Feriado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="feriado_id" value="" />
                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="date" id="feriado_fecha" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Motivo</label>
                                <input type="text" id="feriado_motivo" class="form-control" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" id="feriado_save" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
        </div>
    </footer>
</div>

<script>
jQuery(function($){
    $('#btnNewFeriado').on('click', function(){
        $('#feriado_id').val('');
        $('#feriado_fecha').val('');
        $('#feriado_motivo').val('');
        $('#feriadoModal').modal('show');
    });

    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        if (!id) return;
        $.getJSON('<?php echo base_url('feriados/get/'); ?>' + id).done(function(resp){
            if (!resp || !resp.status) { alert((resp && resp.message)?resp.message:'Error'); return; }
            var f = resp.feriado;
            $('#feriado_id').val(f.id);
            $('#feriado_fecha').val(f.fecha);
            $('#feriado_motivo').val(f.motivo);
            $('#feriadoModal').modal('show');
        }).fail(function(){ alert('Error en la petición'); });
    });

    $(document).on('click', '.btn-delete', function(){
        if (!confirm('Confirmar eliminación')) return;
        var id = $(this).data('id');
        $.post('<?php echo base_url('feriados/del_ajax'); ?>', { id: id }).done(function(resp){
            try{ var j = (typeof resp === 'object')?resp:JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if (j && j.status) location.reload(); else alert((j && j.message)?j.message:'Error');
        }).fail(function(){ alert('Error en la petición'); });
    });

    $('#feriado_save').on('click', function(){
        var id = $('#feriado_id').val();
        var fecha = $('#feriado_fecha').val();
        var motivo = $('#feriado_motivo').val();
        if (!fecha) { alert('Ingrese una fecha'); return; }
        var url = id ? '<?php echo base_url('feriados/edit_ajax'); ?>' : '<?php echo base_url('feriados/add_ajax'); ?>';
        var data = { fecha: fecha, motivo: motivo };
        if (id) data.id = id;
        $.post(url, data).done(function(resp){
            try{ var j = (typeof resp === 'object')?resp:JSON.parse(resp); }catch(e){ alert('Respuesta inválida'); return; }
            if (j && j.status) location.reload(); else alert((j && j.message)?j.message:'Error');
        }).fail(function(){ alert('Error en la petición'); });
    });
});
</script>
