<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-receipt bg-blue"></i>
                            <div class="d-inline">
                                <h5>Listado de Cobros</h5>
                                <span>Revisa todos los cobros registrados y administra su estado.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="text" id="cobrosSearch" class="form-control" placeholder="Buscar descripción, persona o cuenta" />
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <input type="date" id="cobrosDateFrom" class="form-control" placeholder="Desde" />
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <input type="date" id="cobrosDateTo" class="form-control" placeholder="Hasta" />
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <select id="cobrosFilterEstado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="registrado">Registrado</option>
                                <option value="activo">Activo</option>
                                <option value="anulado">Anulado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="btnRefrescarCobros" class="btn btn-outline-primary btn-block">Refrescar listado</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <!-- Excel export hidden as requested -->
                            <button type="button" id="btnExportCobrosPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Exportar PDF</button>
                        </div>
                        <div class="col-md-6 text-right text-md-right">
                            <small class="text-muted">El reporte se exporta con los filtros aplicados.</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="cobrosTabla">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Persona</th>
                                    <th>Descripción</th>
                                    <th>Cuenta</th>
                                    <th>Serie / Recibo</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="cobrosSinDatos" class="text-muted">Cargando listado de cobros...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Cobro -->
<div class="modal fade" id="modalEditarCobro" tabindex="-1" role="dialog" aria-labelledby="modalEditarCobroLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCobroLabel">Editar Cobro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarCobro">
                    <input type="hidden" id="editarCobroId" />
                    <div class="form-group">
                        <label for="editarCobroBeneficiario">Beneficiario</label>
                        <input type="text" id="editarCobroBeneficiario" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroDescripcion">Descripción</label>
                        <input type="text" id="editarCobroDescripcion" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroMonto">Monto</label>
                        <input type="number" id="editarCobroMonto" class="form-control" step="0.01" required />
                    </div>
                    <div class="form-group">
                        <label for="editarCobroObservaciones">Observaciones</label>
                        <textarea id="editarCobroObservaciones" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCobroEdicion">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Anular Cobro -->
<div class="modal fade" id="modalAnularCobro" tabindex="-1" role="dialog" aria-labelledby="modalAnularCobroLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAnularCobroLabel">Anular Cobro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="anularCobroId" />
                <div class="form-group">
                    <label for="motivoAnulacionCobro">Motivo de anulación</label>
                    <textarea id="motivoAnulacionCobro" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAnularCobro">Anular Cobro</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var siteUrl = '<?php echo site_url('tesoreria'); ?>';

    function formatCurrency(value) {
        var number = parseFloat(value);
        if (isNaN(number)) return '0.00';
        return number.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function cargarCobrosListado() {
        var filtros = {
            q: $('#cobrosSearch').val() || '',
            estado: $('#cobrosFilterEstado').val() || '',
            date_from: $('#cobrosDateFrom').val() || '',
            date_to: $('#cobrosDateTo').val() || ''
        };

        $.ajax({
            url: siteUrl + '/get_cobros_ajax',
            method: 'GET',
            dataType: 'json',
            data: filtros
        }).done(function(resp){
            var tbody = $('#cobrosTabla tbody');
            tbody.empty();
            if (!resp || !resp.status || !Array.isArray(resp.cobros) || resp.cobros.length === 0) {
                $('#cobrosSinDatos').text('No se encontraron cobros con los filtros aplicados.');
                return;
            }

            resp.cobros.forEach(function(c, index) {
                var montoTexto = formatCurrency(c.monto_total) + ' ' + (c.moneda || 'NIO');
                var estadoTexto = c.estado ? c.estado.charAt(0).toUpperCase() + c.estado.slice(1) : 'N/A';
                var acciones = '';
                acciones += '<button type="button" class="btn btn-sm btn-info mr-1 btn-ver-recibo" data-id="' + c.id + '"><i class="fas fa-print"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-success mr-1 btnContabilizarMov" data-id="' + c.id + '"><i class="fas fa-balance-scale"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-warning mr-1 btn-editar-cobro" data-id="' + c.id + '" data-beneficiario="' + escapeHtml(c.beneficiario) + '" data-descripcion="' + escapeHtml(c.descripcion) + '" data-monto="' + parseFloat(c.monto_total) + '" data-observaciones="' + escapeHtml(c.observaciones || '') + '"><i class="fas fa-edit"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-danger btn-eliminar-cobro" data-id="' + c.id + '"><i class="fas fa-trash"></i></button>';
                acciones += '<button type="button" class="btn btn-sm btn-secondary ml-1 btn-anular-cobro" data-id="' + c.id + '"><i class="fas fa-ban"></i></button>';

                tbody.append('<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + (c.fecha_registro ? c.fecha_registro.split(' ')[0] : '') + '</td>' +
                    '<td>' + escapeHtml(c.beneficiario) + '</td>' +
                    '<td>' + escapeHtml(c.descripcion) + '</td>' +
                    '<td>' + (c.cuenta_nombre ? escapeHtml(c.cuenta_nombre + ' (' + (c.cuenta_codigo || '') + ')') : '-') + '</td>' +
                    '<td>' + escapeHtml((c.serie_codigo || '') + ' ' + (c.referencia1 || '')) + '</td>' +
                    '<td>' + montoTexto + '</td>' +
                    '<td>' + escapeHtml(estadoTexto) + '</td>' +
                    '<td>' + acciones + '</td>' +
                '</tr>');
            });

            $('#cobrosSinDatos').text('');
        }).fail(function(){
            $('#cobrosSinDatos').text('Error al cargar el listado de cobros.');
        });
    }

    function abrirModalEditarCobro(id, beneficiario, descripcion, monto, observaciones) {
        $('#editarCobroId').val(id);
        $('#editarCobroBeneficiario').val(beneficiario);
        $('#editarCobroDescripcion').val(descripcion);
        $('#editarCobroMonto').val(parseFloat(monto || 0).toFixed(2));
        $('#editarCobroObservaciones').val(observaciones || '');
        $('#modalEditarCobro').modal('show');
    }

    function guardarEdicionCobro() {
        var id = parseInt($('#editarCobroId').val(), 10);
        var beneficiario = $('#editarCobroBeneficiario').val();
        var descripcion = $('#editarCobroDescripcion').val();
        var monto = parseFloat($('#editarCobroMonto').val() || 0);
        var observaciones = $('#editarCobroObservaciones').val();

        if (!id || beneficiario.trim() === '' || descripcion.trim() === '' || monto <= 0) {
            alert('Complete los campos obligatorios antes de guardar.');
            return;
        }

        $('#btnGuardarCobroEdicion').prop('disabled', true).text('Guardando...');
        $.ajax({
            url: siteUrl + '/update_cobro_ajax',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                beneficiario: beneficiario,
                descripcion: descripcion,
                monto_total: monto,
                observaciones: observaciones
            }
        }).done(function(resp){
            $('#btnGuardarCobroEdicion').prop('disabled', false).text('Guardar Cambios');
            if (resp && resp.status) {
                $('#modalEditarCobro').modal('hide');
                cargarCobrosListado();
                alert('Cobro actualizado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo actualizar el cobro.'));
            }
        }).fail(function(){
            $('#btnGuardarCobroEdicion').prop('disabled', false).text('Guardar Cambios');
            alert('Error de red al actualizar el cobro.');
        });
    }

    function abrirModalAnularCobro(id) {
        $('#anularCobroId').val(id);
        $('#motivoAnulacionCobro').val('');
        $('#modalAnularCobro').modal('show');
    }

    function confirmarAnularCobro() {
        var id = parseInt($('#anularCobroId').val(), 10);
        var motivo = $('#motivoAnulacionCobro').val();
        if (!id || motivo.trim() === '') {
            alert('Ingrese el motivo de anulación.');
            return;
        }

        $('#btnConfirmarAnularCobro').prop('disabled', true).text('Anulando...');
        $.ajax({
            url: siteUrl + '/anular_movimiento_ajax',
            method: 'POST',
            dataType: 'json',
            data: {
                id: id,
                motivo: motivo
            }
        }).done(function(resp){
            $('#btnConfirmarAnularCobro').prop('disabled', false).text('Anular Cobro');
            if (resp && resp.status) {
                $('#modalAnularCobro').modal('hide');
                cargarCobrosListado();
                alert('Cobro anulado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo anular el cobro.'));
            }
        }).fail(function(){
            $('#btnConfirmarAnularCobro').prop('disabled', false).text('Anular Cobro');
            alert('Error de red al anular el cobro.');
        });
    }

    function eliminarCobro(id) {
        if (!confirm('¿Está seguro de eliminar este cobro? Esta acción ajustará el saldo de la cuenta.')) {
            return;
        }
        $.ajax({
            url: siteUrl + '/delete_cobro_ajax',
            method: 'POST',
            dataType: 'json',
            data: { id: id }
        }).done(function(resp){
            if (resp && resp.status) {
                cargarCobrosListado();
                alert('Cobro eliminado correctamente.');
            } else {
                alert('Error: ' + (resp.message || 'No se pudo eliminar el cobro.'));
            }
        }).fail(function(){
            alert('Error de red al eliminar el cobro.');
        });
    }

    $(document).ready(function(){
        cargarCobrosListado();
        $('#cobrosSearch, #cobrosFilterEstado, #cobrosDateFrom, #cobrosDateTo').on('input change', cargarCobrosListado);
        $('#btnRefrescarCobros').on('click', cargarCobrosListado);

        $('#btnExportCobrosExcel').on('click', function(){
            var params = $.param({
                q: $('#cobrosSearch').val() || '',
                estado: $('#cobrosFilterEstado').val() || '',
                date_from: $('#cobrosDateFrom').val() || '',
                date_to: $('#cobrosDateTo').val() || ''
            });
            window.location = siteUrl + '/cobros_export_xlsx?' + params;
        });

        $('#btnExportCobrosPdf').on('click', function(){
            var params = $.param({
                q: $('#cobrosSearch').val() || '',
                estado: $('#cobrosFilterEstado').val() || '',
                date_from: $('#cobrosDateFrom').val() || '',
                date_to: $('#cobrosDateTo').val() || ''
            });
            window.open(siteUrl + '/cobros_export_pdf?' + params, '_blank');
        });

        $(document).on('click', '.btn-editar-cobro', function(){
            abrirModalEditarCobro(
                $(this).data('id'),
                $(this).data('beneficiario'),
                $(this).data('descripcion'),
                $(this).data('monto'),
                $(this).data('observaciones')
            );
        });

        $(document).on('click', '.btn-anular-cobro', function(){
            abrirModalAnularCobro($(this).data('id'));
        });

        $(document).on('click', '.btn-eliminar-cobro', function(){
            eliminarCobro($(this).data('id'));
        });

        $(document).on('click', '.btn-ver-recibo', function(){
            var id = $(this).data('id');
            if (id) {
                window.open(siteUrl + '/recibo_cobro/' + id, '_blank');
            }
        });

        $(document).on('click', '.btnContabilizarMov', function(){
            var id = $(this).data('id');
            window.CONTABILIZAR_MOV_ID = id;
            $.get(siteUrl + '/get_movimiento_ajax', {id: id}, function(resp){
                var j = (typeof resp === 'object') ? resp : JSON.parse(resp);
                if(j && j.status && j.movimiento){
                    var modalContainer = document.getElementById('modalContainer');
                    if (!modalContainer) {
                        modalContainer = document.createElement('div');
                        modalContainer.id = 'modalContainer';
                        document.body.appendChild(modalContainer);
                    }
                    if (j.movimiento.journal_id) {
                        $.get('<?php echo site_url('contabilidad/modal_view'); ?>', {id: j.movimiento.journal_id}, function(html){
                            modalContainer.innerHTML = html;
                        });
                    } else {
                        $.get('<?php echo site_url('contabilidad/modal_add'); ?>', function(html){
                            var $modal = $(html);
                            window.CONTABILIZAR_MOV_ID = id;
                            $modal.find('input[name="date"]').val(j.movimiento.fecha_registro);
                            $modal.find('textarea[name="description"]').val(j.movimiento.descripcion || '');
                            modalContainer.innerHTML = '';
                            modalContainer.appendChild($modal[0]);
                            if (typeof attachModalEvents === 'function') {
                                attachModalEvents();
                            }
                            $modal.find('#btnCancelModal').on('click', function(){ modalContainer.innerHTML = ''; });
                            $modal.on('click', function(e){ if(e.target === this) modalContainer.innerHTML = ''; });
                        });
                    }
                } else {
                    alert('No se pudo obtener el movimiento para contabilizar.');
                }
            });
        });

        $('#btnGuardarCobroEdicion').on('click', guardarEdicionCobro);
        $('#btnConfirmarAnularCobro').on('click', confirmarAnularCobro);
    });
})();
</script>
<script src="<?php echo base_url('public/js/contabilidad_modal_enhanced.js'); ?>?v=<?php echo time(); ?>"></script>
