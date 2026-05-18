<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-5 col-md-5">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <!-- <a data-toggle="tooltip" data-placement="top" title="Nuevo Pago Masivo de Créditos" href="<?php echo base_url($this->router->fetch_class() . '/creditos'); ?>" class="btn bg-green text-white float-right mr-1"><i class="fas fa-plus-circle"></i> Pago masivo créditos </a> -->
                                <!-- Pago masivo ocultado por requerimiento -->
                                <a style="display:none;" data-toggle="tooltip" data-placement="top" title="Nuevo Pago Masivo de Cuotas" href="<?php echo base_url($this->router->fetch_class() . '/masivo'); ?>" class="btn bg-blue text-white float-right mr-1"><i class="fas fa-plus-circle"></i> Pago masivo cuotas</a>
                                <a data-toggle="tooltip" data-placement="top" title="Nuevo Pago Unitario de Cuotas" href="<?php echo base_url('pagos/prestamos_core'); ?>" class="btn bg-warning text-white float-right"><i class="fas fa-list-alt"></i> Pago unitario</a>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <?php if ($message = $this->session->flashdata('success')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-success alert-success text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($message = $this->session->flashdata('info')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-info alert-info text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-smile"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($message = $this->session->flashdata('error')) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-danger alert-dagner text-white alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-frown"></i> <?php echo $message; ?></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php
                $provisionales = isset($pagos_provisionales) && is_array($pagos_provisionales) ? $pagos_provisionales : array();
                $timelineRows = array();
                $normalizarNombreCorto = function ($nombre) {
                    $nombre = trim((string)$nombre);
                    if ($nombre === '') return '-';
                    $partes = preg_split('/\s+/', $nombre);
                    if (!$partes || count($partes) === 0) return '-';
                    return $partes[0];
                };

                foreach ($provisionales as $pv) {
                    $estadoRaw = isset($pv->estado) ? strtolower(trim($pv->estado)) : 'pendiente';
                    $fechaBase = !empty($pv->created_at) ? $pv->created_at : (!empty($pv->fecha) ? $pv->fecha . ' 00:00:00' : '');
                    $refDisplay = isset($pv->documento_numero) ? trim((string)$pv->documento_numero) : '';
                    if ($refDisplay !== '' && preg_match('/^[A-Za-z]+$/', $refDisplay) && isset($pv->id) && intval($pv->id) > 0) {
                        $refDisplay = $refDisplay . str_pad(intval($pv->id), 10, '0', STR_PAD_LEFT);
                    }
                    $serieDisplay = $refDisplay !== '' ? $refDisplay : (!empty($pv->serie_codigo) ? $pv->serie_codigo : '-');
                    $timelineRows[] = array(
                        'tipo' => 'provisional',
                        'sort_ts' => !empty($fechaBase) ? strtotime($fechaBase) : 0,
                        'fecha' => !empty($pv->fecha) ? $pv->fecha : '-',
                        'cliente' => isset($pv->beneficiario) ? $pv->beneficiario : '-',
                        'prestamo' => isset($pv->idprestamo) && intval($pv->idprestamo) > 0 ? $pv->idprestamo : '-',
                        'cuota' => isset($pv->idcuota) && intval($pv->idcuota) > 0 ? $pv->idcuota : '-',
                        'monto' => isset($pv->monto) ? floatval($pv->monto) : 0,
                        'moneda' => isset($pv->moneda) && trim((string)$pv->moneda) !== '' ? strtoupper(trim((string)$pv->moneda)) : 'USD',
                        'metodo' => isset($pv->medio_pago) ? ucfirst($pv->medio_pago) : '-',
                        'referencia' => $refDisplay !== '' ? $refDisplay : '-',
                        'serie' => $serieDisplay,
                        'registrado_por' => $normalizarNombreCorto(isset($pv->registrado_por) ? $pv->registrado_por : '-'),
                        'estado' => in_array($estadoRaw, array('registrado','programado','pendiente')) ? 'Pendiente aprobación' : ucfirst($estadoRaw),
                        'estado_class' => in_array($estadoRaw, array('registrado','programado','pendiente')) ? 'badge-warning' : 'badge-secondary',
                        'acciones_html' => '<span class="text-muted small">Pendiente en tesorería</span>'
                    );
                }

                if (!empty($prestamo_pagos)) {
                    foreach ($prestamo_pagos as $pp) {
                        $cliente_display = '';
                        if (!empty($pp->cli_nombres_from_p)) $cliente_display = $pp->cli_nombres_from_p;
                        elseif (!empty($pp->cli_nombres_from_s)) $cliente_display = $pp->cli_nombres_from_s;
                        elseif (!empty($pp->cli_apellidos_from_p)) $cliente_display = $pp->cli_apellidos_from_p;
                        else $cliente_display = $pp->cli_apellidos_from_s;
                        $cliente_display = trim($cliente_display);

                        $accionesAplicado = '<div class="btn-group" role="group">'
                            . '<a target="_blank" href="' . base_url('pagos/prestamo_pdf/' . $pp->id) . '" class="btn btn-sm btn-secondary" title="Imprimir"><i class="ik ik-printer"></i></a>'
                            . '</div>';

                        $timelineRows[] = array(
                            'tipo' => 'aplicado',
                            'sort_ts' => !empty($pp->fecha_pago) ? strtotime($pp->fecha_pago) : 0,
                            'fecha' => !empty($pp->fecha_pago) ? formatoFechaCorta($pp->fecha_pago) : '-',
                            'cliente' => $cliente_display !== '' ? $cliente_display : '-',
                            'prestamo' => $pp->idprestamo,
                            'cuota' => $pp->numero_cuota ? $pp->numero_cuota : ($pp->idcuota ? $pp->idcuota : '-'),
                            'monto' => floatval($pp->monto_pagado),
                            'moneda' => isset($pp->moneda) && trim((string)$pp->moneda) !== '' ? strtoupper(trim((string)$pp->moneda)) : 'USD',
                            'metodo' => $pp->metodo_pago,
                            'referencia' => $pp->referencia,
                            'serie' => !empty($pp->referencia) ? $pp->referencia : (isset($pp->serie_codigo) && $pp->serie_codigo ? $pp->serie_codigo : '-'),
                            'registrado_por' => $normalizarNombreCorto(isset($pp->emitido_por_firstname) && $pp->emitido_por_firstname ? $pp->emitido_por_firstname : '-'),
                            'estado' => 'Aplicado',
                            'estado_class' => 'badge-success',
                            'acciones_html' => $accionesAplicado
                        );
                    }
                }

                usort($timelineRows, function ($a, $b) {
                    if ($a['sort_ts'] === $b['sort_ts']) return 0;
                    return ($a['sort_ts'] < $b['sort_ts']) ? 1 : -1;
                });
            ?>

            <div class="row d-none">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Pagos Registrados</h3>
                        </div>
                        <div class="card-body">
                            <?php

                            $tiempo_en_segundos = time();
                            $fecha_actual = date("d-m-Y h:i:s a", $tiempo_en_segundos);
                            echo "La fecha actual es: $fecha_actual";
                            ?>
                            <div class="table-responsive-sm">
                                <table class="table data-table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Nro Crédito</th>
                                        <th>Nro Cuota</th>
                                        <th>Pagado</th>
                                        <th>Fecha Pago</th>
                                        <th class="nosort text-right pr-25">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $contador = 1; ?>
                                    <?php foreach ($pagos as $pago) : ?>
                                        <tr class="text-center">
                                            <td><?php echo $contador++; ?> </td>
                                            <td><?php echo $pago->apellidos . ', ' . $pago->nombres; ?> </td>
                                            <td><?php echo $pago->idcredito; ?> </td>
                                            <td><?php echo $pago->numero_couta; ?> </td>
                                            <td><?php echo $pago->monto_pago; ?> </td>
                                            <td><?php echo formatoFechaCorta($pago->fechaPago); ?> </td>
                                            <td>
                                                <div class="table-actions text-center">
                                                    <!-- <a target="_blank" href="<?php echo base_url($this->router->fetch_class() . '/pdfmasivo/' . $pago->idpago); ?>"><i class="ik ik-printer f-16 text-dark"></i></a> -->
                                                    <!-- <a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $pago->idpago) ?>" data-toggle="tooltip" data-placement="top" title="Vizualizar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-eye f-16 mr-15 text-success"></i></a> -->
                                                    <a href="javascript:void(0);" data-toggle="modal" data-target="#pago-<?php echo $pago->idpago ?>" data-toggle="tooltip" data-placement="top" title="Imprimir Recibo"><i class="ik ik-printer f-16 text-dark"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="pago-<?php echo $pago->idpago ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalCenterLabel">Seleccionar el Formato</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <a class="btn btn-primary mb-2 text-white btnFormato1" id="btnFormato1" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 1/4 Carta</a>
                                                        <a class="btn btn-primary mb-2 text-white btnFormato2" id="btnFormato2" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 1/3 Carta</a>
                                                        <a class="btn btn-primary mb-2 text-white btnFormato3" id="btnFormato3" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 1/2 Carta</a>
                                                        <a class="btn btn-primary mb-2 text-white btnFormato4" id="btnFormato4" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 1 Carta</a>
                                                        <a class="btn btn-primary mb-2 text-white btnFormato5" id="btnFormato5" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 80mm</a>
                                                        <a class="btn btn-primary mb-2 text-white btnFormato6" id="btnFormato6" idpago="<?php echo $pago->idpago ?>"><i class="fas fa-file-pdf"></i> 57mm</a>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Cancelar" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Historial de Pagos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2 align-items-end">
                                <div class="col-md-4">
                                    <input id="pagos_search" class="form-control" placeholder="Buscar por cliente, préstamo o referencia..." />
                                </div>
                                <div class="col-md-3">
                                    <select id="pagos_filter_status" class="form-control">
                                        <option value="all">Todos</option>
                                        <option value="pendiente">Pendiente aprobación</option>
                                        <option value="aplicado">Aplicado</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-right">
                                    <small class="text-muted">Un solo historial, ordenado del pago más reciente al más antiguo.</small>
                                </div>
                            </div>
                            <div class="table-responsive-sm">
                                <table id="pagos-historial-table" class="table table-sm table-striped table-bordered table-compact">
                                    <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Id Prestamo</th>
                                        <th>Nro Cuota</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Metodo</th>
                                        <th>Serie Recibo</th>
                                        <th>Registrado por</th>
                                        <th>Estado</th>
                                        <th class="nosort text-right pr-25">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($timelineRows)) : ?>
                                        <?php foreach ($timelineRows as $idx => $row) : ?>
                                            <tr class="text-center" data-status="<?php echo strtolower($row['estado']) === 'aplicado' ? 'aplicado' : 'pendiente'; ?>">
                                                <td><?php echo $idx + 1; ?></td>
                                                <td><?php echo html_escape($row['cliente']); ?></td>
                                                <td><?php echo html_escape($row['prestamo']); ?></td>
                                                <td><?php echo html_escape($row['cuota']); ?></td>
                                                <td>
                                                    <?php
                                                        $monedaRow = isset($row['moneda']) ? strtoupper(trim((string)$row['moneda'])) : 'USD';
                                                        $simboloRow = ($monedaRow === 'NIO') ? 'C$' : '$';
                                                    ?>
                                                    <span class="badge badge-light"><?php echo html_escape($monedaRow); ?></span>
                                                    <?php echo $simboloRow . number_format($row['monto'], 2); ?>
                                                </td>
                                                <td><?php echo html_escape($row['fecha']); ?></td>
                                                <td><?php echo html_escape($row['metodo']); ?></td>
                                                <td><?php echo html_escape($row['serie']); ?></td>
                                                <td><?php echo html_escape($row['registrado_por']); ?></td>
                                                <td><span class="badge <?php echo $row['estado_class']; ?>"><?php echo html_escape($row['estado']); ?></span></td>
                                                <td><?php echo $row['acciones_html']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="11" class="text-center">No hay pagos registrados.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Prestamo Pago Modal -->
            <div class="modal fade" id="modalEditPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Pago de Prestamo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_pp_id" />
                            <div class="form-group">
                                <label>Monto Pagado</label>
                                <input class="form-control" id="edit_monto" />
                            </div>
                            <div class="form-group">
                                <label>Fecha Pago</label>
                                <input type="datetime-local" class="form-control" id="edit_fecha" />
                            </div>
                            <div class="form-group">
                                <label>Metodo</label>
                                <input class="form-control" id="edit_metodo" />
                            </div>
                            <div class="form-group">
                                <label>Referencia</label>
                                <input class="form-control" id="edit_referencia" />
                            </div>
                            <div class="form-group">
                                <label>Dato Adicional</label>
                                <input class="form-control" id="edit_dato_adicional" />
                            </div>
                            <div id="editPrestamoAlert" class="alert d-none" role="alert"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveEditPrestamo" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(function(){
                    $('#pagos_search').on('input', function(){
                        var term = ($(this).val() || '').toLowerCase();
                        $('#pagos-historial-table tbody tr').each(function(){
                            var txt = ($(this).text() || '').toLowerCase();
                            $(this).toggle(txt.indexOf(term) !== -1);
                        });
                    });

                    $('#pagos_filter_status').on('change', function(){
                        var val = $(this).val();
                        $('#pagos-historial-table tbody tr').each(function(){
                            var rowStatus = ($(this).data('status') || '').toString();
                            if (val === 'all') {
                                $(this).show();
                            } else {
                                $(this).toggle(rowStatus === val);
                            }
                        });
                    });

                    // Edit button
                    $(document).on('click', '.btn-edit-prestamo', function(){
                        var id = $(this).data('id');
                        $('#editPrestamoAlert').addClass('d-none');
                        $.getJSON(base_url + 'pagos/getPrestamoPagoAjax/' + id, function(resp){
                            if (resp.status) {
                                var d = resp.data;
                                $('#edit_pp_id').val(d.id);
                                $('#edit_monto').val(d.monto_pagado);
                                // convert fecha to datetime-local format
                                var dt = d.fecha_pago ? new Date(d.fecha_pago) : new Date();
                                function pad(n){return (n<10?'0'+n:n)}
                                var local = dt.getFullYear() + '-' + pad(dt.getMonth()+1) + '-' + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + ':' + pad(dt.getMinutes());
                                $('#edit_fecha').val(local);
                                $('#edit_metodo').val(d.metodo_pago);
                                $('#edit_referencia').val(d.referencia);
                                $('#edit_dato_adicional').val(d.dato_adicional);
                                $('#modalEditPrestamo').modal('show');
                            } else alert('No se pudo cargar el pago');
                        });
                    });

                    // Save edit
                    $('#saveEditPrestamo').on('click', function(){
                        var data = {
                            id: $('#edit_pp_id').val(),
                            monto_pagado: $('#edit_monto').val(),
                            fecha_pago: $('#edit_fecha').val(),
                            metodo_pago: $('#edit_metodo').val(),
                            referencia: $('#edit_referencia').val(),
                            dato_adicional: $('#edit_dato_adicional').val()
                        };
                        $.post(base_url + 'pagos/updatePrestamoPago', data, function(resp){
                            try { var j = typeof resp === 'string' ? JSON.parse(resp) : resp; } catch(e){ j = resp; }
                            if (j && j.status) {
                                $('#editPrestamoAlert').removeClass('d-none alert-danger').addClass('alert-success').text(j.message || 'Guardado');
                                setTimeout(function(){ location.reload(); }, 800);
                            } else {
                                $('#editPrestamoAlert').removeClass('d-none alert-success').addClass('alert-danger').text(j.message || 'Error');
                            }
                        });
                    });

                    // Anular
                    $(document).on('click', '.btn-anular-prestamo', function(){
                        if (!confirm('Anular este pago?')) return;
                        var id = $(this).data('id');
                        $.post(base_url + 'pagos/anularPrestamoPago/' + id, {}, function(resp){
                            try { var j = typeof resp === 'string' ? JSON.parse(resp) : resp; } catch(e){ j = resp; }
                            if (j && j.status) { alert('Pago anulado'); location.reload(); }
                            else alert(j.message || 'Error al anular');
                        });
                    });
                });
            </script>

            <style>
                .table-compact td, .table-compact th{
                    padding: .32rem .55rem;
                    vertical-align: middle;
                    font-size: .85rem;
                    line-height: 1.15;
                    white-space: nowrap;
                }
                .table-compact thead th{
                    font-size: .82rem;
                    padding: .38rem .55rem;
                    color: #6c84a0;
                    background: #f8fbff;
                }
                #pagos-historial-table td:nth-child(2){
                    max-width: 220px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                #pagos-historial-table td:last-child .btn,
                #pagos-historial-table td:last-child .btn-group{
                    white-space: nowrap;
                }
                @media (max-width: 767.98px) {
                    .table-compact td, .table-compact th{
                        white-space: normal !important;
                        font-size: .82rem;
                    }
                    .breadcrumb-container .btn{ display:block; width:100%; }
                    .table-responsive-sm{ overflow-x:auto; }
                }
            </style>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>

</div>
