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
                            <h3>Consultar Créditos por Asesor</h3>
                        </div>
                        <div class="card-body">
                            <form class="forms-sample" id="frmConsula">
                                <label for="">Asesor</label>
                                <div class="input-group">
                                    <select name="" id="cboAsesor" class="form-control select2">
                                        <option value="">SELECCIONAR</option>
                                        <option value="0">TODOS</option>
                                        <?php foreach ($asesores as $asesor) : ?>
                                            <option value="<?php echo $asesor->idasesor; ?>"><?php echo $asesor->idasesor . ' - ' . $asesor->nombres; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <!-- <button type="button" class="btn btn-primary" id="btnConsultar"><i class="fas fa-search"></i> Consultar</button> -->
                                        <button type="button" class="btn bg-danger text-white" id="btnPdfAsesor"><i class="fas fa-file-pdf"></i> Exportar a PDF</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive-sm">
                                <table class="table  table-sm table-hover" style="width:100%;" id="tablaCreditosAsesor">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>N° Crédito</th>
                                        <th>Fecha Crédito</th>
                                        <th>Monto Crédito</th>
                                        <th>Interés</th>
                                        <th>Coutas</th>
                                        <th>Total Interés</th>
                                        <th>Total Pagar</th>
                                        <th>Forma Pago</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="w-100 clearfix">
            <span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y'); ?> Servifact v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>
</div>
