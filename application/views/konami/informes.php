<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="page-header">
                                    <div class="row align-items-end">
                                        <div class="col-lg-12">
                                            <div class="page-header-title">
                                                <i class="fas fa-file-upload bg-blue"></i>
                                                <div class="d-inline">
                                                    <h5>Reporte PRIM</h5>
                                                    <span>Reportes Regulatorios (CONAMI)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form class="form-inline" method="get" action="">
                                <label for="mes_reporte" class="mr-2 font-weight-bold">Mes del reporte:</label>
                                <input type="month" id="mes_reporte" name="mes_reporte" class="form-control mr-2" style="min-width:180px;" value="<?php echo isset($_GET['mes_reporte']) ? htmlspecialchars($_GET['mes_reporte']) : date('Y-m'); ?>">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </form>
                        </div>
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-file-upload bg-blue"></i>
                            <div class="d-inline">
                                <h5>Reporte PRIM</h5>
                                <span>Reportes Regulatorios (CONAMI)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">Generación de archivos en formatos Excel/XML/CSV para carga regulatoria.</p>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-sm table-striped" id="tabla-prim">
                            <thead class="thead-light">
                                <tr>
                                    <th>Can.Cuotas</th>
                                    <th>Cant.Prorrogas</th>
                                    <th>comision_acumulada_por_cobrar</th>
                                    <th>cuotas_vencidas</th>
                                    <th>dias_mora_interes</th>
                                    <th>fecha_otorgamiento</th>
                                    <th>fecha_vencimiento_credito</th>
                                    <th>id_clasificacion_credito</th>
                                    <th>id_credito</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($prestamos_prim)) : ?>
                                    <?php foreach ($prestamos_prim as $row) : ?>
                                        <tr>
                                            <td><?php echo (int)$row->cuotas_pagadas; ?></td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td><?php echo isset($row->cuotas_vencidas) ? (int)$row->cuotas_vencidas : 0; ?></td>
                                            <td><?php echo isset($row->dias_mora_interes) ? (int)$row->dias_mora_interes : 0; ?></td>
                                            <td><?php echo isset($row->id_credito) ? $row->id_credito : ''; ?></td>
                                            <td><?php echo isset($row->fecha_otorgamiento) ? $row->fecha_otorgamiento : ''; ?></td>
                                            <td><?php echo isset($row->id_clasificacion_credito) ? $row->id_clasificacion_credito : ''; ?></td>
                                            <td><?php echo isset($row->id_credito) ? $row->id_credito : ''; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="24" class="text-center">No hay datos para el mes seleccionado.</td></tr>
                                <?php endif; ?>
                        </tbody>
                        </table>
                        <?php
                            // Paginación
                            $total_pages = isset($total_rows) && isset($per_page) ? ceil($total_rows / $per_page) : 1;
                            $current_page = isset($current_page) ? $current_page : 1;
                            $base_url = $_SERVER['PHP_SELF'];
                            $query_params = $_GET;
                            unset($query_params['page']);
                            $query_str = http_build_query($query_params);
                            if ($total_pages > 1) {
                                echo '<nav aria-label="Paginación"><ul class="pagination pagination-sm">';
                                $prev_page = max(1, $current_page - 1);
                                echo '<li class="page-item'.($current_page==1?' disabled':'').'"><a class="page-link" href="'.$base_url.($query_str ? '?'.$query_str.'&page='.$prev_page : '?page='.$prev_page).'">&laquo;</a></li>';
                                $start = max(1, $current_page - 3);
                                $end = min($total_pages, $current_page + 3);
                                if ($start > 1) { echo '<li class="page-item"><a class="page-link" href="'.$base_url.($query_str ? '?'.$query_str.'&page=1' : '?page=1').'">1</a></li>'; if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; }
                                for ($i = $start; $i <= $end; $i++) {
                                    echo '<li class="page-item'.($i==$current_page?' active':'').'"><a class="page-link" href="'.$base_url.($query_str ? '?'.$query_str.'&page='.$i : '?page='.$i).'">'.$i.'</a></li>';
                                }
                                if ($end < $total_pages) { if ($end < $total_pages-1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; echo '<li class="page-item"><a class="page-link" href="'.$base_url.($query_str ? '?'.$query_str.'&page='.$total_pages : '?page='.$total_pages).'">'.$total_pages.'</a></li>'; }
                                $next_page = min($total_pages, $current_page + 1);
                                echo '<li class="page-item'.($current_page==$total_pages?' disabled':'').'"><a class="page-link" href="'.$base_url.($query_str ? '?'.$query_str.'&page='.$next_page : '?page='.$next_page).'">&raquo;</a></li>';
                                echo '</ul></nav>';
                            }
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
        </div>
    </footer>
</div>
<?php $this->load->view('layout/footer'); ?>