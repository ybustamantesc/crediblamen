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

            <div class="card">
                <div class="card-body">
                    <style>
                        #uso-table{ table-layout: auto; width:100%; max-width: 100%; margin: 0; box-sizing: border-box; }
                        #uso-table{ max-width:100%; box-sizing:border-box; display: table; }
                        #uso-table th:first-child, #uso-table td:first-child{
                            width:auto; min-width:40px; max-width:80px; text-align:center;
                            padding-left:.4rem; padding-right:.4rem;
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                        }
                        #uso-table th:last-child, #uso-table td:last-child{
                            width: 90px; min-width:70px; white-space: nowrap;
                            vertical-align: middle; text-align:center;
                        }
                        /* Match compact table styles used by solicitudes list so dimensions align */
                        #uso-table.table-compact td, #uso-table.table-compact th{
                            padding: .12rem .28rem;
                            vertical-align: middle;
                            font-size: .72rem;
                            line-height: 1.02;
                            white-space: normal;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        #uso-table.table-compact thead th{ font-size: .7rem; padding: .18rem .28rem; }
                        #uso-table.table-compact .btn{ padding: .12rem .28rem; font-size: .72rem; min-width: auto; }
                        /* Keep Cliente column from stretching too far while allowing wrapping */
                        #uso-table th:nth-child(2), #uso-table td:nth-child(2){
                            max-width: 180px;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        /* Match action column width */
                        #uso-table th:last-child, #uso-table td:last-child{ width: 120px; white-space: nowrap; }
                        /* Reduce search/filter input height similarly */
                        #uso_search, #uso_filter_status { height: calc(1.1em + 0.28rem); padding: 0.14rem 0.36rem; }
                        .table-responsive { overflow-x: auto; }
                    </style>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="uso_search" class="form-control" placeholder="Buscar por código o cliente..." />
                        </div>
                        <div class="col-md-3">
                            <select id="uso_filter_status" class="form-control">
                                <option value="all">Todos</option>
                                <option value="pending">Pendiente</option>
                                <option value="approved">Aprobado</option>
                                <option value="rejected">Rechazado</option>
                                <option value="annulled">Anulado</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-right">
                            <small class="text-muted">Filtrar por estado o buscar por cliente/código.</small>
                        </div>
                    </div>
                    <div id="uso-table-wrapper" class="table-responsive d-none d-md-block">
                        <table id="uso-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Código</th>
                                    <th>Destino Conami</th>
                                    <th>Creado por</th>
                                    <th class="d-none">Fecha de Solicitud</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($solicitudes)): foreach($solicitudes as $s): ?>
                                    <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                    <?php $rowClass = ''; if(isset($s->aprob_status)){ if($s->aprob_status === 'approved') $rowClass = 'table-success'; elseif($s->aprob_status === 'rejected') $rowClass = 'table-danger'; elseif($s->aprob_status === 'annulled') $rowClass = 'table-secondary'; }
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>" data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <td><?php echo $s->idsolicitud; ?></td>
                                        <td>
                                            <?php
                                                // Prefer explicit name fields; show full name of cliente
                                                $name = trim((isset($s->nombres)?$s->nombres:'') . ' ' . (isset($s->apellidos)?$s->apellidos:''));
                                                if(!$name){
                                                    // fallback to identification if name missing
                                                    $name = isset($s->numero_doc) && $s->numero_doc ? $s->numero_doc : (isset($s->numero_documento) && $s->numero_documento ? $s->numero_documento : (isset($s->cedula) && $s->cedula ? $s->cedula : (isset($s->identificacion) && $s->identificacion ? $s->identificacion : '')));
                                                }
                                                echo htmlspecialchars($name);
                                            ?>
                                            <?php if ($status === 'annulled'): ?>
                                                <span class="badge badge-secondary ml-1">Anulado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($s->rubro_credito ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($s->nombre_asesor ?? ''); ?></td>
                                        <td class="d-none"><?php echo (!empty($s->fecha_solicitud) ? $s->fecha_solicitud : (!empty($s->fecha_recepcion) ? $s->fecha_recepcion : '')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary btn-uso" data-id="<?php echo $s->idsolicitud; ?>">Formato Uso Crédito</button>
                                                <button class="btn btn-sm btn-outline-secondary btn-download-uso" data-id="<?php echo $s->idsolicitud; ?>" title="Descargar formato"><i class="fa fa-download"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="7" class="text-center">No hay solicitudes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile card list -->
                    <div id="uso-cards-wrapper" class="d-block d-md-none">
                        <div class="row">
                            <?php if(!empty($solicitudes)): foreach($solicitudes as $s): ?>
                                <?php $status = isset($s->aprob_status) ? $s->aprob_status : 'pending'; ?>
                                <?php $rowClass = ''; if(isset($s->aprob_status)){ if($s->aprob_status === 'approved') $rowClass = 'border-success'; elseif($s->aprob_status === 'rejected') $rowClass = 'border-danger'; elseif($s->aprob_status === 'annulled') $rowClass = 'border-secondary'; }
                                ?>
                                <div class="col-12">
                                    <div class="card mb-2 <?php echo $rowClass; ?>" data-id="<?php echo $s->idsolicitud; ?>" data-status="<?php echo $status; ?>">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="font-weight-bold"><?php echo htmlspecialchars(trim((isset($s->nombres)?$s->nombres:'') . ' ' . (isset($s->apellidos)?$s->apellidos:''))); ?></div>
                                                    <div class="text-muted small"><?php echo 'SOL-' . str_pad($s->idsolicitud, 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-primary btn-uso" data-id="<?php echo $s->idsolicitud; ?>">Uso</button>
                                                        <button class="btn btn-sm btn-outline-secondary btn-download-uso" data-id="<?php echo $s->idsolicitud; ?>" title="Descargar"><i class="fa fa-download"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="col-12"><div class="text-center text-muted">No hay solicitudes.</div></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <script>
                        (function waitForUsoCreditoDataTable(){
                            if(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable){
                                (function($){
                                    try{
                                        var table = $('#uso-table').DataTable({
                                            "bSort": false,
                                            "responsive": true,
                                            "autoWidth": false,
                                            "pageLength": 10,
                                            "lengthMenu": [[10,25,50,100],[10,25,50,100]]
                                        });
                                        var wrapper = $(table.table().container());
                                        wrapper.find('.dataTables_filter').hide();
                                        $('#uso_search').off('input.dt').on('input', function(){ table.search(this.value).draw(); });
                                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
                                            if (!settings || !settings.nTable || settings.nTable.id !== 'uso-table') return true;
                                            var status = $('#uso_filter_status').val();
                                            if (!status || status === 'all') return true;
                                            var row = table.row(dataIndex).node();
                                            return ($(row).data('status') || 'pending') === status;
                                        });
                                        $('#uso_filter_status').off('change.dt').on('change', function(){ table.draw(); });
                                    }catch(e){ console.error('Uso credito DataTable error', e); }
                                })(jQuery);
                            } else { setTimeout(waitForUsoCreditoDataTable, 100); }
                        })();
                    </script>

                    <style>
                        /* Force responsive sections to be mutually exclusive in case global CSS overrides exist */
                        @media (max-width: 767.98px) {
                            #uso-table-wrapper { display: none !important; }
                            #uso-cards-wrapper { display: block !important; }
                        }
                        @media (min-width: 768px) {
                            #uso-table-wrapper { display: block !important; }
                            #uso-cards-wrapper { display: none !important; }
                        }
                    </style>
                </div>
            </div>

            <?php $this->load->view('solicitudes/_uso_credito_modal'); ?>

            <footer class="footer">
                <div class="w-100 clearfix">
                    <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
                </div>
            </footer>
        </div>
    </div>
</div>
