<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
  <?php $this->load->view('layout/sidebar'); ?>
  <div class="main-content">
    <div class="container-fluid">
      <div class="page-header">
        <div class="row align-items-end">
          <div class="col-lg-8">
            <div class="page-header-title">
              <i class="fas fa-cogs bg-blue"></i>
              <div class="d-inline">
                <h5>Administración</h5>
                <span>Panel de configuración y control</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 mb-3">
          <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h4 class="mb-1">Panel de Administración</h4>
                <p class="text-muted mb-0">Gestiona usuarios, roles, parámetros y ajustes del sistema.</p>
              </div>
              <div class="d-flex">
                <a href="<?php echo site_url('menu'); ?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fa fa-arrow-left mr-1"></i> Volver al Menú</a>
                <a href="<?php echo site_url('administracion/usuarios'); ?>" class="btn btn-primary mr-2">Usuarios</a>
                <a href="<?php echo site_url('administracion/roles'); ?>" class="btn btn-outline-secondary mr-2">Roles</a>
                <a href="<?php echo site_url('administracion/configuracion'); ?>" class="btn btn-outline-secondary">Configuración</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header"><h6>Atajos</h6></div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                <li><a href="<?php echo site_url('administracion/usuarios'); ?>">Gestión de Usuarios</a></li>
                <li><a href="<?php echo site_url('administracion/roles'); ?>">Roles y Permisos</a></li>
                <li><a href="<?php echo site_url('administracion/configuracion'); ?>">Configuración General</a></li>
                <li><a href="<?php echo site_url('administracion/seguridad'); ?>">Seguridad</a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-header"><h6>Información rápida</h6></div>
            <div class="card-body">
              <p class="text-muted">Desde aquí puedes configurar parámetros del sistema, gestionar catálogos e integrar servicios externos.</p>
            </div>
          </div>
        </div>
      </div>

      <div id="modalContainer"></div>

    </div>
  </div>
  <footer class="footer">
    <div class="w-100 clearfix">
      <span class="text-center text-sm-left d-md-inline-block">Copyright © 2026 Crediblamen System v1 All Rights Reserved.</span>
      <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Personalizado <i class="fa fa-heart text-danger"></i> by Serviconta</span>
    </div>
  </footer>
</div>
<?php $this->load->view('layout/footer'); ?>