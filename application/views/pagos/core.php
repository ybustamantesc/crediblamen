<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono_view; ?> bg-blue"></i>
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
                        <div class="card-body">
                            <form class="forms-sample" name="form_core" method="POST">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <select class="form-control select2" name="idcliente" id="idcliente" required <?php echo(isset($pago) && $pago->estado == 1 ? 'disabled' : ''); ?>>
                                                <option value="">SELECCIONAR</option>
                                            </select>
                                            <?php echo form_error('idcliente', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nro Crédito</label>
                                            <select class="form-control select2" name="idcredito" id="idcredito" required>
                                                <option value="">SELECCIONAR</option>
                                            </select>
                                            <?php echo form_error('idcredito', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Botón para visualizar el crédito -->
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="d-block negrita">PREVISUALIZAR CRÉDITO (OPCIONAL PARA VER DETALLES)</label>
                                            <button type="button" class="btn btn-primary" id="preview_credito">FORMATO PDF</button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Monto Crédito</label>
                                            <input style="text-align:center;" type="text" class="form-control" readonly name="monto_credito" id="monto_credito" required value="<?php echo(isset($pago) ? $pago->monto_credito : set_value('monto_credito')); ?>">
                                            <?php echo form_error('monto_credito', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Crédito</label>
                                            <input style="text-align:center;" type="text" class="form-control" readonly name="fecha_credito" id="fecha_credito" required value="<?php echo(isset($pago) ? $pago->fecha_credito : set_value('fecha_credito')); ?>">
                                            <?php echo form_error('fecha_credito', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Cuotas</label>
                                            <select class="form-control select2" name="idcuota" id="idcuota" required value="<?php echo(isset($pago) ? $pago->numero_couta : set_value('numero_couta')); ?>">
                                                <option value="">SELECCIONAR</option>
                                            </select>
                                            <?php echo form_error('idcuota', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Monto Cuota</label>
                                            <input type="text" class="form-control" readonly name="monto_couta" id="monto_couta" required value="<?php echo(isset($pago) ? $pago->monto_couta : set_value('monto_couta')); ?>">
                                            <?php echo form_error('monto_couta', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fecha Cuota</label>
                                            <input type="text" class="form-control" readonly name="fecha_couta" id="fecha_couta" required value="<?php echo(isset($pago) ? $pago->fecha_couta : set_value('fecha_couta')); ?>">
                                            <?php echo form_error('fecha_couta', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Monto Pagado</label>
                                            <input type="text" class="form-control" name="monto_pago" id="monto_pago" required value="<?php echo(isset($pago) ? $pago->monto_pago : set_value('monto_pago')); ?>">
                                            <?php echo form_error('monto_pago', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Monto Pendiente</label>
                                            <input type="text" class="form-control" readonly name="monto_pendiente" id="monto_pendiente" required value="<?php echo(isset($pago) ? $pago->monto_pendiente : set_value('monto_pendiente')); ?>">
                                            <?php echo form_error('monto_pendiente', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Fecha Pago</label>
                                            <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" required>
                                           
                                            <?php echo form_error('fecha_pago', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Mora</label>
                                            <input type="text" class="form-control" name="mora" id="mora">
                                            <?php echo form_error('mora', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Forma de Pago</label>
                                            <select name="forma_pago" class="form-control custom-select" required>
                                                <option value="">SELECCIONAR</option>
                                                <option value="EFECTIVO">EFECTIVO</option>
                                                <option value="CHEQUE">CHEQUE</option>
                                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                <option value="DEPÓSITO">DEPÓSITO</option>
                                            </select>
                                            <?php echo form_error('forma_pago', '<div class="text-danger">', '</div>') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tipo Documento</label>
                                            <input type="text" class="form-control" name="tipo_doc">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>N° Documento</label>
                                            <input type="text" class="form-control" name="numero_doc">
                                        </div>
                                    </div>

                                    <?php if (isset($pago)) : ?>
                                        <div class="col-md-6">
                                            <input type="hidden" name="id" value="<?php echo($pago->id); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn bg-success text-white mr-2"><i class="fas fa-check"></i> Pagar</button>
                                <a class="btn btn-danger" href="<?php echo base_url($this->router->fetch_class()); ?>"><i class="fas fa-arrow-circle-left"></i> Volver</a>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>

</div>

<script>
    document.getElementById('preview_credito').addEventListener('click', function() {
        // Obtener el valor del número de crédito seleccionado
        var idCredito = document.getElementById('idcredito').value;
        
        if (idCredito) {
            // Construir la URL para redirigir al PDF
            var url = 'https://avielprest.ncdv.tech/prestamo/pdf/' + idCredito;
            
            // Obtener el tamaño de la ventana del navegador
            var width = window.innerWidth < 768 ? window.innerWidth * 0.9 : 800;  // Ajustamos el tamaño si es móvil
            var height = window.innerHeight < 768 ? window.innerHeight * 0.8 : 600; // Ajustamos la altura en móvil
            var left = (window.innerWidth / 2) - (width / 2);
            var top = (window.innerHeight / 2) - (height / 2);
            
            // Abrir el enlace en un popup centrado
            window.open(url, '_blank', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',scrollbars=yes');
        } else {
            alert('Por favor, seleccione un número de crédito');
        }
    });
</script>
<script>
    // Load only clients that have plans (from Planescredito::clients)
    (function(){
        $(document).ready(function(){
            $.getJSON(base_url + 'planescredito/clients', function(resp){
                if (!resp || !resp.status || !resp.clients) return;
                var $sel = $('#idcliente');
                $sel.empty().append('<option value="">SELECCIONAR</option>');
                resp.clients.forEach(function(c){
                    var val = c.id !== null && c.id !== undefined ? c.id : ('DOC:' + c.numero_doc);
                    var text = c.nombre;
                    $sel.append($('<option>').attr('value', val).text(text));
                });
                try { if ($sel.data('select2')) $sel.select2('destroy'); } catch(e){}
                $sel.select2({ width: '100%' });
            }).fail(function(){ console.warn('No se pudieron cargar clientes con plan'); });
        });
    })();
</script>