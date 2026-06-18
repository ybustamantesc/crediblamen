<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo isset($icono) ? $icono : 'fas fa-id-card'; ?> bg-blue"></i>
                            <div class="d-inline">
                                <h5><?php echo isset($titulo) ? $titulo : 'Perfil Integral del Cliente'; ?></h5>
                                <span><?php echo isset($subtitulo) ? $subtitulo : 'Listado de perfiles'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <a href="<?php echo base_url('perfil_integral/create'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo Perfil</a>
                    </div>
                </div>
            </div>

<script>
    (function(){
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) return;
        function applyPerfilFilters(){
            var q = document.getElementById('perfil_search').value.toLowerCase().trim();
            var status = document.getElementById('perfil_filter_status').value;
            var rows = document.querySelectorAll('#perfil-table tbody tr');
            rows.forEach(function(r){
                var code = (r.querySelector('td:nth-child(3)') || {textContent:''}).textContent.toLowerCase();
                var name = (r.querySelector('td:nth-child(4)') || {textContent:''}).textContent.toLowerCase();
                var rowStatus = r.getAttribute('data-status') || 'pending';
                var matchQ = q === '' || code.indexOf(q) !== -1 || name.indexOf(q) !== -1;
                var matchStatus = status === 'all' || status === rowStatus;
                if (matchQ && matchStatus) r.style.display = '';
                else r.style.display = 'none';
            });
            // also apply to mobile cards
            var cards = document.querySelectorAll('.perfil-card');
            cards.forEach(function(c){
                var code = (c.querySelector('.perfil-code') || {textContent:''}).textContent.toLowerCase();
                var name = (c.querySelector('.perfil-name') || {textContent:''}).textContent.toLowerCase();
                var cardStatus = c.getAttribute('data-status') || 'pending';
                var matchQ = q === '' || code.indexOf(q) !== -1 || name.indexOf(q) !== -1;
                var matchStatus = status === 'all' || status === cardStatus;
                if (matchQ && matchStatus) c.style.display = '';
                else c.style.display = 'none';
            });
        }
        var s = document.getElementById('perfil_search');
        var f = document.getElementById('perfil_filter_status');
        if (s) s.addEventListener('input', applyPerfilFilters);
        if (f) f.addEventListener('change', applyPerfilFilters);
    })();
</script>
            <script>
                (function waitForPerfilDataTable(){
                    if(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable){
                        (function($){
                            try{
                                var table = $('#perfil-table').DataTable({
                                    "bSort": false,
                                    "responsive": true,
                                    "autoWidth": false,
                                    "pageLength": 10,
                                    "lengthMenu": [[10,25,50,100],[10,25,50,100]]
                                });
                                var wrapper = $(table.table().container());
                                wrapper.find('.dataTables_filter').hide();
                                $('#perfil_search').off('input.dt').on('input', function(){ table.search(this.value).draw(); });
                                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
                                    if (!settings || !settings.nTable || settings.nTable.id !== 'perfil-table') return true;
                                    var status = $('#perfil_filter_status').val();
                                    if (!status || status === 'all') return true;
                                    var row = table.row(dataIndex).node();
                                    return ($(row).data('status') || 'pending') === status;
                                });
                                $('#perfil_filter_status').off('change.dt').on('change', function(){ table.draw(); });
                            }catch(e){ console.warn('Perfil integral DataTable not ready', e); }
                        })(jQuery);
                    } else { setTimeout(waitForPerfilDataTable, 100); }
                })();
            </script>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input id="perfil_search" class="form-control" placeholder="Buscar por código o cliente..." />
                        </div>
                        <div class="col-md-3">
                            <select id="perfil_filter_status" class="form-control">
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
                    <style>
                        #perfil-table{ table-layout: auto; width:100%; max-width: 100%; margin: 0; box-sizing: border-box; }
                        #perfil-table{ max-width:100%; box-sizing:border-box; display: table; }
                        #perfil-table th:first-child, #perfil-table td:first-child{
                            width:auto; min-width:40px; max-width:80px; text-align:center;
                            padding-left:.4rem; padding-right:.4rem;
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                        }
                        #perfil-table th:last-child, #perfil-table td:last-child{
                            width: 110px; min-width:90px; white-space: nowrap;
                            vertical-align: middle; text-align:center;
                        }
                        /* Match compact table styles used by solicitudes/uso_credito */
                        #perfil-table.table-compact td, #perfil-table.table-compact th{
                            padding: .12rem .28rem;
                            vertical-align: middle;
                            font-size: .72rem;
                            line-height: 1.02;
                            white-space: normal;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        #perfil-table.table-compact thead th{ font-size: .7rem; padding: .18rem .28rem; }
                        #perfil-table.table-compact .btn{ padding: .12rem .28rem; font-size: .72rem; min-width: auto; }
                        /* Keep columns readable while allowing wrapping */
                        #perfil-table th:nth-child(2), #perfil-table td:nth-child(2){
                            max-width: 80px;
                            overflow-wrap: break-word;
                        }
                        #perfil-table th:nth-child(3), #perfil-table td:nth-child(3){
                            max-width: 90px;
                            overflow-wrap: break-word;
                        }
                        #perfil-table th:nth-child(4), #perfil-table td:nth-child(4){
                            max-width: 200px;
                            overflow-wrap: break-word;
                            word-break: normal;
                        }
                        #perfil-table th:nth-child(5), #perfil-table td:nth-child(5){
                            max-width: 150px;
                            overflow-wrap: break-word;
                        }
                        #perfil-table th:nth-child(6), #perfil-table td:nth-child(6){
                            max-width: 120px;
                            overflow-wrap: break-word;
                        }
                        #perfil-table th:nth-child(7), #perfil-table td:nth-child(7){
                            max-width: 130px;
                            overflow-wrap: break-word;
                        }
                        #perfil-table th:nth-child(8), #perfil-table td:nth-child(8){
                            max-width: 120px;
                            overflow-wrap: break-word;
                        }
                        /* Action buttons group */
                        .perfil-actions{
                            display: flex;
                            flex-wrap: nowrap;
                            justify-content: center;
                            gap: .08rem;
                            align-items: center;
                        }
                        .perfil-actions .btn{
                            padding: .12rem .28rem !important;
                            font-size: .72rem !important;
                            line-height: 1.02 !important;
                            min-width: auto !important;
                            white-space: nowrap !important;
                            flex: 1;
                        }
                        .perfil-actions .btn:last-child{ margin-right:0 !important; }
                        .table-responsive { overflow-x: auto; }
                    </style>
                    <style>
                        /* Ensure only one of table or cards is visible (avoid conflicts from other CSS) */
                        #perfil-table-wrap { display: block; }
                        #perfil-cards-wrap { display: none; }
                        @media (max-width: 767.98px) {
                            /* Keep table visible in mobile and hide cards so DataTables pagination works consistently */
                            #perfil-table-wrap { display: block !important; }
                            #perfil-cards-wrap { display: none !important; }
                            /* Hide #, Solicitud and Destino Conami columns to save horizontal space */
                            #perfil-table th:first-child, #perfil-table td:first-child,
                            #perfil-table th:nth-child(2), #perfil-table td:nth-child(2),
                            #perfil-table th:nth-child(7), #perfil-table td:nth-child(7) { display: none; }
                            #perfil-table { table-layout: fixed; }
                            #perfil-table th:last-child, #perfil-table td:last-child {
                                width: auto !important;
                                min-width: 0 !important;
                                white-space: normal !important;
                            }
                            #perfil-table td:last-child {
                                padding: .08rem .15rem !important;
                            }
                            #perfil-table td:last-child .btn {
                                padding: .10rem .14rem !important;
                                font-size: .68rem !important;
                                min-width: 0 !important;
                                line-height: 1 !important;
                                width: 100% !important;
                                white-space: normal !important;
                                overflow-wrap: break-word !important;
                                word-break: break-word !important;
                            }
                            .perfil-actions {
                                gap: .08rem !important;
                                flex-direction: column !important;
                                align-items: stretch !important;
                            }
                            .perfil-actions .btn {
                                display: block !important;
                                width: 100% !important;
                                text-align: center !important;
                            }
                        }
                        @media (min-width: 768px) {
                            #perfil-cards-wrap { display: none !important; }
                        }
                        .perfil-card .btn {
                            margin-right: .35rem;
                            margin-bottom: .28rem;
                        }
                        .perfil-card .btn:last-child {
                            margin-right: 0;
                        }
                        .perfil-card .btn + .btn {
                            margin-left: 0;
                        }
                    </style>
                    <div id="perfil-table-wrap" class="table-responsive d-block">
                        <table id="perfil-table" class="table table-sm table-striped table-bordered table-compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Solicitud</th>
                                    <th>ID de Solicitud</th>
                                    <th>Nombre</th>
                                    <th class="d-none d-md-table-cell">Tel/Cel</th>
                                    <th class="d-none d-md-table-cell">Nivel Riesgo</th>
                                    <th>Destino Conami</th>
                                    <th>Creado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($perfiles)) : 
                                    if (!function_exists('pref')){
                                        function pref($p, $s, $keys, $default = ''){
                                            if (!is_array($keys)) $keys = [$keys];
                                            foreach ($keys as $k){
                                                if (!empty($p) && isset($p->$k) && $p->$k !== null && $p->$k !== '') return $p->$k;
                                                if (!empty($s) && isset($s->$k) && $s->$k !== null && $s->$k !== '') return $s->$k;
                                            }
                                            return $default;
                                        }
                                    }
                                    foreach ($perfiles as $p) :
                                        $sol = isset($solicitudes_map[$p->solicitud_id]) ? $solicitudes_map[$p->solicitud_id] : null;
                                        $status = isset($p->aprob_status) ? $p->aprob_status : 'pending';
                                        $rowClass = '';
                                        if ($status === 'approved') $rowClass = 'table-success';
                                        elseif ($status === 'rejected') $rowClass = 'table-danger';
                                        elseif ($status === 'annulled') $rowClass = 'table-secondary';
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>" data-id="<?php echo $p->solicitud_id; ?>" data-status="<?php echo $status; ?>">
                                        <td><?php echo $p->id; ?></td>
                                        <td><?php echo $p->solicitud_id; ?></td>
                                        <td><?php echo 'SOL-' . str_pad($p->solicitud_id, 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo html_escape(pref($p,$sol, ['nombre','nombres']).' '.pref($p,$sol,['primer_apellido','apellidos'])); ?></td>
                                        <td class="d-none d-md-table-cell"><?php echo html_escape(pref($p,$sol,['telefono']).' / '.pref($p,$sol,['celular'])); ?></td>
                                        <td class="d-none d-md-table-cell"><?php echo html_escape(pref($p,$sol,['nivel_riesgo'])); ?></td>
                                        <td><?php echo html_escape(pref($p,$sol,['rubro_credito'])); ?></td>
                                        <td><?php echo html_escape(pref($p,$sol,['nombre_asesor','nombre_promotor'])); ?></td>
                                        <td>
                                            <div class="perfil-actions">
                                                <a class="btn btn-sm btn-info" href="<?php echo base_url('perfil_integral/create/'.$p->solicitud_id); ?>">Editar</a>
                                                <a class="btn btn-sm btn-secondary" href="<?php echo base_url('perfil_integral/download/'.$p->id); ?>" target="_blank">Descargar</a>
                                                <a class="btn btn-sm btn-primary" href="<?php echo base_url('perfil_integral/download_matriz/'.$p->id); ?>" target="_blank">Matriz</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="9" class="text-center">No hay perfiles registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div id="perfil-cards-wrap" class="d-block d-md-none">
                        <div class="row">
                            <?php if (!empty($perfiles)) : foreach ($perfiles as $p) :
                                $sol = isset($solicitudes_map[$p->solicitud_id]) ? $solicitudes_map[$p->solicitud_id] : null;
                                $status = isset($p->aprob_status) ? $p->aprob_status : 'pending';
                                $cardClass = '';
                                if ($status === 'approved') $cardClass = 'border-success';
                                elseif ($status === 'rejected') $cardClass = 'border-danger';
                                elseif ($status === 'annulled') $cardClass = 'border-secondary';
                            ?>
                                <div class="col-12">
                                    <div class="card mb-2 perfil-card <?php echo $cardClass; ?>" data-id="<?php echo $p->solicitud_id; ?>" data-status="<?php echo $status; ?>">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="perfil-name font-weight-bold"><?php echo html_escape(pref($p,$sol, ['nombre','nombres']).' '.pref($p,$sol,['primer_apellido','apellidos'])); ?></div>
                                                    <div class="perfil-code text-muted small"><?php echo 'SOL-' . str_pad($p->solicitud_id, 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                                <div class="text-right">
                                                    <a href="<?php echo base_url('perfil_integral/create/'.$p->solicitud_id); ?>" class="btn btn-sm btn-info">Editar</a>
                                                    <a href="<?php echo base_url('perfil_integral/download/'.$p->id); ?>" class="btn btn-sm btn-secondary" target="_blank">Descargar</a>
                                                    <a href="<?php echo base_url('perfil_integral/download_matriz/'.$p->id); ?>" class="btn btn-sm btn-primary" target="_blank">Matriz PDF</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="col-12"><div class="text-center text-muted">No hay perfiles registrados.</div></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    </div>

                    <!-- Pagination handled client-side by DataTables (same logic as Solicitudes) -->
                </div>
            </div>

        </div>
    </div>
</div>
