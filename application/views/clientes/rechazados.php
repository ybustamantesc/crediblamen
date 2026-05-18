<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="<?php echo $icono; ?> bg-red"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <nav class="breadcrumb-container" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <!-- No new-button for rejected list -->
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Clientes Rechazados</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter_rechazo_status">Filtro estado</label>
                                    <select id="filter_rechazo_status" class="form-control">
                                        <option value="all">Todos</option>
                                        <option value="rechazados">Rechazados</option>
                                        <option value="restaurados">Restaurados</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Unified responsive card grid for all sizes + search -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <input id="rechazados-search" class="form-control form-control-sm" placeholder="Buscar cliente (nombre, doc, teléfono)...">
                                </div>
                                <div class="col-12 col-md-3 ml-auto text-right">
                                    <small class="text-muted">Mostrar por estado</small>
                                </div>
                            </div>

                            <div class="row" id="rechazados-list">
                                <?php if (!empty($clientes)) : ?>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <?php $status = (isset($cliente->restaurado_en) && !empty($cliente->restaurado_en)) ? 'restaurado' : 'rechazado'; ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-2 cliente-card" data-search="<?php echo strtolower(htmlspecialchars($cliente->apellidos . ' ' . $cliente->nombres . ' ' . $cliente->numero_doc . ' ' . $cliente->telefono)); ?>" data-status="<?php echo $status; ?>">
                                            <div class="card h-100">
                                                <div class="card-body p-2">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($cliente->apellidos . ', ' . $cliente->nombres); ?></h6>
                                                    <p class="small text-muted mb-1"><?php echo htmlspecialchars($cliente->numero_doc); ?> — <?php echo htmlspecialchars($cliente->telefono); ?></p>
                                                    <p class="mb-1"><?php echo ($status == 'restaurado') ? '<span class="badge badge-info">RESTAURADO</span>' : '<span class="badge badge-danger">RECHAZADO</span>'; ?></p>
                                                    <div class="d-flex flex-wrap">
                                                        <a href="#" data-toggle="modal" data-target="#verMotivo-<?php echo isset($cliente->idcliente) ? $cliente->idcliente : (isset($cliente->id) ? $cliente->id : ''); ?>" class="btn btn-sm btn-primary mr-1 mb-1">Ver motivo</a>
                                                        <a href="#" data-toggle="modal" data-target="#restaurar-<?php echo isset($cliente->idcliente) ? $cliente->idcliente : (isset($cliente->id) ? $cliente->id : ''); ?>" class="btn btn-sm btn-success mr-1 mb-1">Restaurar</a>
                                                        <a href="<?php echo base_url('clientes/download_rechazo/' . (isset($cliente->id) ? $cliente->id : (isset($cliente->idcliente) ? $cliente->idcliente : ''))); ?>" class="btn btn-sm btn-dark mr-1 mb-1">TXT</a>
                                                        <a href="<?php echo base_url('clientes/download_rechazo_pdf/' . (isset($cliente->id) ? $cliente->id : (isset($cliente->idcliente) ? $cliente->idcliente : ''))); ?>" class="btn btn-sm btn-outline-primary mb-1">PDF</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">No hay registros en la lista de Clientes Rechazados.</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <script>
                            (function(){
                                var $cards = Array.prototype.slice.call(document.querySelectorAll('#rechazados-list .cliente-card'));
                                var $search = document.getElementById('rechazados-search');
                                var $filter = document.getElementById('filter_rechazo_status');

                                function applyFilters(){
                                    var q = $search.value.trim().toLowerCase();
                                    var filter = $filter ? $filter.value : 'all';
                                    $cards.forEach(function(card){
                                        var txt = card.getAttribute('data-search') || '';
                                        var status = card.getAttribute('data-status') || '';
                                        var matchQ = !q || txt.indexOf(q) !== -1;
                                        var matchStatus = (filter === 'all') || (filter === 'restaurados' && status === 'restaurado') || (filter === 'rechazados' && status === 'rechazado');
                                        card.style.display = (matchQ && matchStatus) ? '' : 'none';
                                    });
                                }

                                if($search) $search.addEventListener('input', applyFilters);
                                if($filter) $filter.addEventListener('change', applyFilters);
                                // initial
                                applyFilters();
                            })();
                            </script>
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
