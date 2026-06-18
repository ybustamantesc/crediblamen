<?php // modal_account.php - crear/editar cuenta ?>
<?php
// Fallback: ensure ER/BS lines are available even if controller didn't pass them
if (!isset($er_lines) || !is_array($er_lines) || !isset($bs_lines) || !is_array($bs_lines)) {
    // attempt to load config directly
    if (isset($this) && method_exists($this, 'config')) {
        try {
            // load without sections so item('report_lines') returns the array
            $this->config->load('report_lines', FALSE, TRUE);
            $rl = $this->config->item('report_lines');
            if (!$rl) {
                // fallback: try loading with default args
                $this->config->load('report_lines');
                $rl = $this->config->item('report_lines');
            }
            if (is_array($rl)) {
                if (!isset($er_lines) || !is_array($er_lines)) $er_lines = isset($rl['er']) ? $rl['er'] : (isset($rl['report_lines']['er']) ? $rl['report_lines']['er'] : []);
                if (!isset($bs_lines) || !is_array($bs_lines)) $bs_lines = isset($rl['bs']) ? $rl['bs'] : (isset($rl['report_lines']['bs']) ? $rl['report_lines']['bs'] : []);
            }
        } catch (Exception $e) {
            // ignore; view will show empty selects
        }
    }
}
?>
<?php $selectedType = isset($account->type) ? trim(strtolower($account->type)) : ''; ?>
<div id="modalAccount" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(3,7,18,0.6);display:flex;align-items:center;justify-content:center;z-index:99999;padding:20px;">
    <div style="width:100%;max-width:720px;background:#ffffff;padding:0;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,0.12);overflow:hidden;color:#0b1220;border:1px solid rgba(2,6,23,0.04);">
        <div style="background:linear-gradient(135deg,#f0f9ff 0%,#fbf7ff 100%);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(2,6,23,0.04);">
            <div>
                <?php
                    $isEdit = isset($account) && $account;
                    $modeTitle = '';
                    if (!$isEdit) {
                        $cmode = isset($create_mode) ? $create_mode : (isset($account) && isset($account->parent_id) && $account->parent_id ? 'child' : 'root');
                        if ($cmode === 'sibling') $modeTitle = ' (Mismo nivel)';
                        elseif ($cmode === 'child') $modeTitle = ' (Subcuenta)';
                        elseif ($cmode === 'root') $modeTitle = ' (Cuenta Mayor)';
                    }
                ?>
                <h3 id="modalAccountTitle" style="margin:0;font-size:20px;font-weight:700;color:#0b1220;"><?php echo $isEdit ? 'Editar Cuenta' : 'Nueva Cuenta' . $modeTitle; ?></h3>
                <div style="color:rgba(11,18,32,0.7);font-size:13px;margin-top:4px;">Detalles de la cuenta — diseño moderno</div>
            </div>
            <button id="btnCancelAccount" style="background:#ffffff;border:1px solid rgba(2,6,23,0.06);color:#0b1220;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:700;">Cerrar</button>
        </div>
        <div style="padding:22px;background:#ffffff;">
            <div id="accountErrors" style="color:#9b1c1c;margin-bottom:10px;display:none;background:rgba(249, 115, 22, 0.04);padding:8px;border-radius:8px;font-size:13px;"></div>
            <form id="formAccount">
                <input type="hidden" name="id" value="<?php echo isset($account) ? $account->id : ''; ?>" />

                <div style="display:grid;grid-template-columns:150px 1fr;gap:16px;margin-bottom:14px;align-items:center;">
                    <div>
                        <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Código</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" name="code" id="inputCode" value="<?php echo isset($account) ? $account->code : ''; ?>" style="flex:1;padding:12px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#ffffff;color:#0b1220;font-size:14px;" placeholder="Auto-generado" />
                            <button type="button" id="btnSuggestCode" title="Generar código automático" style="padding:10px 14px;border-radius:8px;background:#667eea;color:#fff;border:none;cursor:pointer;font-weight:600;font-size:12px;">Auto</button>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Nombre</label>
                        <input type="text" name="name" value="<?php echo isset($account) ? $account->name : ''; ?>" style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#ffffff;color:#0b1220;font-size:14px;" />
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Tipo</label>
                        <select name="type" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;">
                            <option value="activo" <?php echo ($selectedType === 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="pasivo" <?php echo ($selectedType === 'pasivo') ? 'selected' : ''; ?>>Pasivo</option>
                            <option value="patrimonio" <?php echo ($selectedType === 'patrimonio') ? 'selected' : ''; ?>>Patrimonio</option>
                            <option value="ingreso" <?php echo ($selectedType === 'ingreso') ? 'selected' : ''; ?>>Ingreso</option>
                            <option value="gasto" <?php echo ($selectedType === 'gasto') ? 'selected' : ''; ?>>Gasto</option>
                            <option value="contingente" <?php echo ($selectedType === 'contingente') ? 'selected' : ''; ?>>Contingente</option>
                            <option value="orden" <?php echo ($selectedType === 'orden') ? 'selected' : ''; ?>>Orden</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.8);font-weight:600;margin-bottom:6px;font-size:13px;">Naturaleza</label>
                        <select name="naturaleza" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;">
                            <option value="">-- Seleccionar --</option>
                            <option value="deudora" <?php echo (isset($account) && $account->naturaleza=='deudora') ? 'selected' : ''; ?>>Deudora</option>
                            <option value="acreedora" <?php echo (isset($account) && $account->naturaleza=='acreedora') ? 'selected' : ''; ?>>Acreedora</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.8);font-weight:600;margin-bottom:6px;font-size:13px;">Nivel</label>
                        <input type="number" name="level" min="1" step="1" value="<?php echo isset($account) && isset($account->level) ? intval($account->level) : 1; ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;" />
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;align-items:center;">
                    <div>
                        <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Estado de Resultado</label>
                        <select name="report_is" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;">
                            <option value="">-- Seleccionar --</option>
                            <?php if (isset($er_lines) && is_array($er_lines)): ?>
                                <?php foreach ($er_lines as $key): $val = trim($key); ?>
                                    <option value="<?php echo htmlspecialchars($val); ?>" <?php echo (isset($account) && isset($account->report_is) && $account->report_is === $val) ? 'selected' : ''; ?>><?php echo htmlspecialchars($val); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Estado de Situación Financiera</label>
                        <select name="report_bs" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;">
                            <option value="">-- Seleccionar --</option>
                            <?php if (isset($bs_lines) && is_array($bs_lines)): ?>
                                <?php foreach ($bs_lines as $key): $val = trim($key); ?>
                                    <option value="<?php echo htmlspecialchars($val); ?>" <?php echo (isset($account) && isset($account->report_bs) && $account->report_bs === $val) ? 'selected' : ''; ?>><?php echo htmlspecialchars($val); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                                <div>
                                    <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Crear</label>
                                    <?php $cmode = isset($create_mode) ? $create_mode : (isset($account) && isset($account->parent_id) && $account->parent_id ? 'child' : 'root'); ?>
                                    <select name="create_mode" id="createMode" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;">
                                        <option value="child" <?php echo ($cmode === 'child') ? 'selected' : ''; ?>>Subcuenta</option>
                                        <option value="sibling" <?php echo ($cmode === 'sibling') ? 'selected' : ''; ?>>Mismo nivel (hermana)</option>
                                        <option value="root" <?php echo ($cmode === 'root') ? 'selected' : ''; ?>>Cuenta Mayor</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block;color:#143a63;font-weight:700;margin-bottom:6px;font-size:13px;">Cuentas de Mayor</label>
                                    <?php $selected_parent = isset($selected_parent_id) ? $selected_parent_id : (isset($account->parent_id) ? intval($account->parent_id) : ''); ?>
                                    <select name="parent_id" id="parentSelect" data-selected-parent-id="<?php echo $selected_parent; ?>" data-source-account-id="<?php echo isset($source_account) ? intval($source_account->id) : ''; ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(2,6,23,0.06);background:#fff;color:#0b1220;font-size:14px;"><option value="">-- Ninguna --</option></select>
                                    <?php if (isset($source_account) && $source_account): ?>
                                        <div style="margin-top:8px;color:#475569;font-size:12px;">
                                            <?php if (isset($create_mode) && $create_mode === 'sibling'): ?>
                                                Creando cuenta a mismo nivel de: <strong><?php echo htmlspecialchars($source_account->code . ' - ' . $source_account->name); ?></strong>
                                            <?php elseif (isset($create_mode) && $create_mode === 'child'): ?>
                                                Creando subcuenta de: <strong><?php echo htmlspecialchars($source_account->code . ' - ' . $source_account->name); ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="display:flex;justify-content:flex-end;gap:10px;">
                                <button type="button" id="btnCancelAccountFooter" class="btn" style="background:#fff;border:1px solid rgba(2,6,23,0.06);color:#0b1220;padding:10px 18px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;line-height:1.2;">Cancelar</button>
                                <button type="submit" class="btn" style="background:#1e40af;color:#fff;padding:10px 20px;border-radius:10px;border:none;font-weight:700;display:inline-flex;align-items:center;justify-content:center;line-height:1.2;">Guardar</button>
                            </div>
                        </form>
        </div>
    </div>
</div>

<style>
    #modalAccount input:focus, #modalAccount select:focus { outline: none; box-shadow: 0 6px 20px rgba(99,102,241,0.08); border-color: #667eea; }
    #modalAccount label { color: #000 !important; font-weight:700; }
    #modalAccount .btn { cursor: pointer; display:inline-flex; align-items:center; justify-content:center; line-height:1.2; }
    #modalAccount .btn:hover { transform: translateY(-2px); }
</style>
