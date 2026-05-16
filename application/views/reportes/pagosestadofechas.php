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
                                <!-- <a data-toggle="tooltip" data-placement="right" title="Nuevo <?php $this->router->fetch_class(); ?>" href="<?php echo base_url($this->router->fetch_class() . '/core/'); ?>" class="btn bg-blue text-white float-right"><i class="fas fa-plus-circle"></i> Nuevo</a> -->
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Filtros de búsqueda por fecha de cuota</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fechaInicio">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fechFin">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Estado</label>
                                        <select name="" id="cboPagosEstados" class="form-control">
                                            <option value="">SELECCIONAR</option>
                                            <option value="0">TODOS</option>
                                            <option value="1">POR COBRAR</option>
                                            <option value="2">PAGAN HOY</option>
                                            <option value="3">VENCIDO</option>
                                            <option value="4">PAGADOS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary mb-2" id="btnConsultarPagosEstado"><i class="fas fa-search"></i> Consultar</button>
                                        <button type="button" class="btn bg-danger text-white mb-2 ml-2" id="btnPdfPagosEstados"><i class="fas fa-file-pdf"></i> Exportar a PDF</button>
                                        <button type="button" class="btn bg-success text-white mb-2 ml-2" id="btnLimpiar"><i class="fas fa-backspace"></i> Nueva consulta</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table data-table table-sm table-striped table-bordered table-hover" id="tablaPagosEstado" style="width: 100%">
                                <thead>
                                <tr class="text-center">
                                    <th class="nosort">#</th>
                                    <th>Crédito</th>
                                    <th>Cliente</th>
                                    <th>Asesor</th>
                                    <th>Fecha Cuota</th>
                                    <th>N° Cuota</th>
                                    <th>Monto Cuota</th>
                                    <th>Fecha Pago</th>
                                    <th>Monto Pendiente</th>
                                    <th>Monto Pago</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
