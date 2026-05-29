<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Seleccionar Cuentas</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div style="max-height:420px;overflow:auto;">
        <div style="margin-bottom:8px;">
            <input id="auxSearch" type="search" class="form-control form-control-sm" placeholder="Buscar cuenta (código o nombre)..." />
        </div>
        <form id="formAuxAccounts">
            <div id="auxAccountsList">
            <?php foreach ($accounts as $a): ?>
                <div style="padding:4px 0;border-bottom:1px solid #eee;display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="accounts[]" value="<?php echo intval($a->id); ?>" id="acct_<?php echo intval($a->id); ?>" />
                    <label for="acct_<?php echo intval($a->id); ?>"><?php echo htmlspecialchars($a->code . ' - ' . $a->name); ?></label>
                </div>
            <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button id="auxSelectAll" class="btn btn-sm btn-link">Seleccionar todo</button>
    <button id="auxDeselectAll" class="btn btn-sm btn-link">Deseleccionar todo</button>
    <button id="auxApply" class="btn btn-sm btn-primary">Aplicar</button>
    <button class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
