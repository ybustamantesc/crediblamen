<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>

    <!-- <div class="main-content">
        <div class="container-fluid">
            <h1>Usuarios</h1>
        </div>
    </div> -->
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
                                <li class="breadcrumb-item">
                                    <a href="<?php echo base_url('/'); ?>" title="Inicio"><i class="ik ik-home"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $titulo; ?></li>
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
                            <a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn btn-primary float-right"><i class="fas fa-plus-circle"></i> Nuevo</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-md">
                                <table class="table data-table table-sm pl-20 pr-20">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Categoria</th>
                                            <th>Valor Hora</th>
                                            <th>Placa</th>
                                            <th>Forma de Pago</th>
                                            <th>Estado</th>
                                            <th class="nosort text-right pr-25">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($estacionados as $estacionado) : ?>
                                            <tr>
                                                <td><?php echo $estacionado->estacionar_id; ?> </td>
                                                <td><?php echo $estacionado->precio_categoria; ?> </td>
                                                <td><?php echo $estacionado->precio_valor_hora; ?> </td>
                                                <td><?php echo $estacionado->estacionar_placa_vehiculo; ?> </td>
                                                <td><?php echo ($estacionado->estacionar_estado == 1 ? $estacionado->nombre : 'Abierto') ?> </td>
                                                <td><?php echo ($estacionado->estacionar_estado == 1 ? '<span class="badge badge-success mb-1"> Pagada</span> ' : '<span class="badge badge-warning mb-1"> En abierta</span>'); ?> </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a target="_blank" href="<?php echo base_url($this->router->fetch_class() . '/pdf/' . $estacionado->estacionar_id); ?>"><i class="ik ik-printer f-16 text-dark"></i></a>
                                                        <a href="<?php echo base_url($this->router->fetch_class() . '/core/' . $estacionado->estacionar_id) ?>" data-toggle="tooltip" data-placement="top" title="<?php echo ($estacionado->estacionar_estado == 1 ? 'Visualizar' : 'Cerrar'); ?>  <?php echo $this->router->fetch_class(); ?>"><i class="<?php echo ($estacionado->estacionar_estado == 1 ? 'ik ik-eye f-16 mr-15 text-info' : 'ik ik-edit f-16 mr-15 text-success'); ?>"></i></a>
                                                        <a href="" data-toggle="modal" data-target="#estacionado-<?php echo $estacionado->estacionar_id ?>" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"><i class="ik ik-trash-2 f-16 text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="estacionado-<?php echo $estacionado->estacionar_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="exampleModalCenterLabel"><i class="fas fa-exclamation-triangle"></i> ¿Quieres eliminar el registro?</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Si desea eliminar el registro click en <strong>Sí, eliminar.</strong></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" data-toggle="tooltip" data-placement="top" title="Cancelar" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                            <a href="<?php echo base_url($this->router->fetch_class() . '/del/' . $estacionado->estacionar_id) ?>" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar <?php echo $this->router->fetch_class(); ?>"> Sí, eliminar</a>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block text-center">
                            SITUACIÓN DE VACANTES
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-6">
                                    <p class="text-center text-uppercase">VEHÍCULO PEQUEÑO <?php echo ($numero_vacantes_pequeno->precio_estado == 0 ? '<span class="text-danger font-weight-bold"><i class="fas fa-ban"></i> Desactivada</span>' : ''); ?> </p>
                                    <div class="widget social-widget">
                                        <div class="widget-body text-center">
                                            <div class="content">
                                                <i class="fas fa-car fa-3x text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-inline mt-15 text-center">
                                                <?php
                                                $ocupadas = array();
                                                $placas = array();
                                                foreach ($vacantes_ocupadas_pequeno as $vacante) {
                                                    $ocupadas[] = $vacante->estacionar_numero_vacante;
                                                    $placas[$vacante->estacionar_numero_vacante] = $vacante->estacionar_placa_vehiculo;
                                                }
                                                ?>
                                                <?php for ($i = 1; $i <= $numero_vacantes_pequeno->vacantes; $i++) : ?>
                                                    <li class="list-inline-item">
                                                        <?php if (in_array($i, $ocupadas)) : ?>
                                                            <div class="widget social-widget vaga bg-warning">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="PLACA: <?php echo $placas[$i]; ?>">
                                                                        <i class="fas fa-car"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="widget social-widget vaga <?php echo ($numero_vacantes_pequeno->precio_estado == 0 ? ' bg-danger' : ' bg-success'); ?>">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="<?php echo ($numero_vacantes_pequeno->precio_estado == 0 ? 'Desactivado' : 'Disponible'); ?>">
                                                                        <div class="number"><?php echo ($numero_vacantes_pequeno->precio_estado == 0 ? '<i class="fas fa-ban"></i>' : $i); ?> </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-4 col-6">
                                    <p class="text-center text-uppercase">VEHÍCULO MEDIO <?php echo ($numero_vacantes_medio->precio_estado == 0 ? '<span class="text-danger font-weight-bold"><i class="fas fa-ban"></i> Desactivada</span>' : ''); ?> </p>
                                    <div class="widget social-widget">
                                        <div class="widget-body text-center">
                                            <div class="content">
                                                <i class="fas fa-truck-pickup fa-3x text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-inline mt-15 text-center">
                                                <?php
                                                $ocupadas = array();
                                                $placas = array();
                                                foreach ($vacantes_ocupadas_medio as $vacante) {
                                                    $ocupadas[] = $vacante->estacionar_numero_vacante;
                                                    $placas[$vacante->estacionar_numero_vacante] = $vacante->estacionar_placa_vehiculo;
                                                }
                                                ?>
                                                <?php for ($i = 1; $i <= $numero_vacantes_medio->vacantes; $i++) : ?>
                                                    <li class="list-inline-item">
                                                        <?php if (in_array($i, $ocupadas)) : ?>
                                                            <div class="widget social-widget vaga <?php echo ($numero_vacantes_medio->precio_estado == 0 ? ' bg-danger' : ' bg-success'); ?>">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="PLACA: <?php echo $placas[$i]; ?>">
                                                                        <i class="fas fa-truck-pickup"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="widget social-widget vaga <?php echo ($numero_vacantes_medio->precio_estado == 0 ? ' bg-danger' : ' bg-success'); ?> ">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="<?php echo ($numero_vacantes_medio->precio_estado == 0 ? 'Desactivado' : 'Disponible'); ?>">
                                                                        <div class="number"><?php echo ($numero_vacantes_medio->precio_estado == 0 ? '<i class="fas fa-ban"></i>' : $i); ?> </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-4 col-6">
                                    <p class="text-center text-uppercase">VEHÍCULO GRANDE <?php echo ($numero_vacantes_grande->precio_estado == 0 ? '<span class="text-danger font-weight-bold"><i class="fas fa-ban"></i> Desactivada</span>' : ''); ?> </p>
                                    <div class="widget social-widget">
                                        <div class="widget-body text-center">
                                            <div class="content">
                                                <i class="fas fa-truck-moving fa-3x text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-inline mt-15 text-center">
                                                <?php
                                                $ocupadas = array();
                                                $placas = array();
                                                foreach ($vacantes_ocupadas_grande as $vacante) {
                                                    $ocupadas[] = $vacante->estacionar_numero_vacante;
                                                    $placas[$vacante->estacionar_numero_vacante] = $vacante->estacionar_placa_vehiculo;
                                                }
                                                ?>
                                                <?php for ($i = 1; $i <= $numero_vacantes_grande->vacantes; $i++) : ?>
                                                    <li class="list-inline-item">
                                                        <?php if (in_array($i, $ocupadas)) : ?>
                                                            <div class="widget social-widget vaga bg-warning">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="PLACA: <?php echo $placas[$i]; ?>">
                                                                        <i class="fas fa-truck-moving"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="widget social-widget vaga bg-success">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="<?php echo ($numero_vacantes_grande->precio_estado == 0 ? 'Desactivado' : 'Disponible'); ?>">
                                                                        <div class="number"><?php echo ($numero_vacantes_grande->precio_estado == 0 ? '<i class="fas fa-ban"></i>' : $i); ?> </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-4 col-6">
                                    <p class="text-center text-uppercase">VEHÍCULO MOTO <?php echo ($numero_vacantes_moto->precio_estado == 0 ? '<span class="text-danger font-weight-bold"><i class="fas fa-ban"></i> Desactivada</span>' : ''); ?> </p>
                                    <div class="widget social-widget">
                                        <div class="widget-body text-center">
                                            <div class="content">
                                                <i class="fas fa-motorcycle fa-3x text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-inline mt-15 text-center">
                                                <?php
                                                $ocupadas = array();
                                                $placas = array();
                                                foreach ($vacantes_ocupadas_moto as $vacante) {
                                                    $ocupadas[] = $vacante->estacionar_numero_vacante;
                                                    $placas[$vacante->estacionar_numero_vacante] = $vacante->estacionar_placa_vehiculo;
                                                }
                                                ?>
                                                <?php for ($i = 1; $i <= $numero_vacantes_moto->vacantes; $i++) : ?>
                                                    <li class="list-inline-item">
                                                        <?php if (in_array($i, $ocupadas)) : ?>
                                                            <div class="widget social-widget vaga bg-warning">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="PLACA: <?php echo $placas[$i]; ?>">
                                                                        <i class="fas fa-motorcycle"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="widget social-widget vaga bg-success">
                                                                <div class="widget-body">
                                                                    <div class="content" data-toggle="tooltip" data-placement="top" title="<?php echo ($numero_vacantes_moto->precio_estado == 0 ? 'Desactivado' : 'Disponible'); ?>">
                                                                        <div class="number"><?php echo ($numero_vacantes_moto->precio_estado == 0 ? '<i class="fas fa-ban"></i>' : $i); ?> </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> ServiPrest v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>

</div>