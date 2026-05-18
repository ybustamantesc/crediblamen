<?php $this->load->view('layout/header'); ?>
<?php $this->load->view('layout/navbar'); ?>
<?php $this->load->view('layout/sidebar'); ?>
<div class="page-wrapper">
  <div class="page-content fade-in">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Gestión de Usuarios</span>
            <div>
              <button class="btn btn-primary btn-sm" id="btnNuevo"><i class="fa fa-plus"></i> Nuevo</button>
              <button class="btn btn-secondary btn-sm" id="btnRefrescar"><i class="fa fa-refresh"></i> Refrescar</button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="tblUsuarios">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Grupos</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUsuarioTitle">Nuevo Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="frmUsuario">
          <input type="hidden" name="id" id="usr_id" />
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" class="form-control" name="username" id="usr_username" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" id="usr_email" required />
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nombre</label>
              <input type="text" class="form-control" name="first_name" id="usr_first" />
            </div>
            <div class="form-group col-md-6">
              <label>Apellido</label>
              <input type="text" class="form-control" name="last_name" id="usr_last" />
            </div>
          </div>
          <div class="form-group">
            <label>Contraseña <small class="text-muted">(requerida al crear)</small></label>
            <input type="password" class="form-control" name="password" id="usr_pass" />
          </div>
          <div class="form-group">
            <label>Grupos (IDs separados por coma)</label>
            <input type="text" class="form-control" name="group_ids[]" id="usr_groups" placeholder="1,2,3" />
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="usr_active" checked />
            <label class="form-check-label" for="usr_active">Activo</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
      </div>
    </div>
  </div>
  </div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalReset" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Restablecer contraseña</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="reset_id" />
        <div class="form-group">
          <label>Nueva contraseña (opcional; si se deja vacío se genera)</label>
          <input type="text" id="reset_new" class="form-control" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btnDoReset">Restablecer</button>
      </div>
    </div>
  </div>
</div>

<script>
  const BASE = '<?php echo site_url(); ?>';
</script>
<script src="<?php echo base_url('public/js/administracion_usuarios.js'); ?>"></script>
<?php $this->load->view('layout/footer'); ?>