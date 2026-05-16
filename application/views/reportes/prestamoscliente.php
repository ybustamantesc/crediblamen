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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Consultar Créditos por Cliente</h3>
                        </div>
                        <div class="card-body">
                            <form class="forms-sample" id="frmConsula">
                                <label for="">CLIENTE</label>
                                <div class="input-group">
                                    <select name="" id="cboCliente" class="form-control select2">
                                        <option value="">SELECCIONAR</option>
                                        <?php foreach ($clientes as $cliente) : ?>
                                            <option value="<?php echo $cliente->idcliente; ?>"><?php echo $cliente->idcliente . ' - ' . $cliente->apellidos . ', ' . $cliente->nombres; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <!-- <button type="button" class="btn btn-primary" id="btnConsultar"><i class="fas fa-search"></i> Consultar</button> -->
                                        <button type="button" class="btn bg-danger text-white" id="btnPdfCliente"><i class="fas fa-file-pdf"></i> Exportar a PDF</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive-sm">
                                <table class="table  table-sm table-hover" style="width:100%;" id="tablaCreditosCliente">
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
            <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
            <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by <a href="http://lavalite.org/" class="text-dark" target="_blank">Serviconta</a></span>
        </div>
    </footer>
</div>
