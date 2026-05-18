<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Modal: Crear / Editar Serie -->
<div class="modal fade" id="modalSerie" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSerieTitle">Nueva Serie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formSerie">
          <input type="hidden" name="idserie" id="idserie" value="">
          <div class="form-group">
            <label for="codigo">Código</label>
            <input type="text" class="form-control" id="codigo" name="codigo" maxlength="10" required>
            <small class="form-text text-muted">Ejemplo: A, B, C</small>
          </div>
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label for="consecutivo">Consecutivo (próximo)</label>
            <input type="number" class="form-control" id="consecutivo" name="consecutivo" value="0" required>
          </div>
          <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" class="form-control">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btnSaveSerie" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
