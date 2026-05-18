<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-calculator bg-blue"></i>
                            <div class="d-inline">
                                <h5> Contabilidad </h5>
                                <span>Módulo de asientos y reportes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 style="margin-bottom:6px;">Contabilidad</h2>
                            <p style="color:#666;margin-top:0;margin-bottom:18px;">Módulo de contabilidad - asientos, libros y reportes básicos</p>
                            <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <button id="btnNewEntry" class="btn btn-primary">Nuevo Asiento</button>
                                </div>
                                <div>
                                    <input id="search" placeholder="Buscar..." />
                                </div>
                            </div>
                            <div>
                                <table id="tblEntries" style="width:100%;border-collapse:collapse;font-size:12px;">
                                    <thead>
                                        <tr>
                                            <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;width:80px;">ID</th>
                                            <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Fecha</th>
                                            <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Descripción</th>
                                            <th style="border-bottom:1px solid #ddd;padding:8px;text-align:right;width:120px;">Debe</th>
                                            <th style="border-bottom:1px solid #ddd;padding:8px;text-align:right;width:120px;">Haber</th>
                                        </tr>
                                    </thead>
                                    <tbody id="entriesBody">
                                        <tr><td colspan="5" style="padding:12px;text-align:center;color:#888;">Cargando...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- modal container -->
                            <div id="modalContainer"></div>
                        </div>
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
<script src="<?php echo base_url('public/js/contabilidad.js'); ?>"></script>
