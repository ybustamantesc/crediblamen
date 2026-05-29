<div id="modalCentroCosto" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;overflow-y:auto;padding:20px;">
    <div style="width:95%;max-width:600px;background:#fff;padding:0;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.3);">
        <!-- Header -->
        <div style="background:linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);padding:24px 32px;border-radius:12px 12px 0 0;color:#fff;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <h2 style="margin:0;font-size:24px;font-weight:600;" id="modalTitle">Nuevo Centro de Costo</h2>
                    <p style="margin:4px 0 0;opacity:0.9;font-size:14px;">Complete la información del centro de costo</p>
                </div>
                <button type="button" id="btnCancelModal" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:24px;line-height:1;transition:all 0.3s;">×</button>
            </div>
        </div>
        
        <!-- Body -->
        <div style="padding:32px;">
            <form id="formCentroCosto">
                <input type="hidden" name="id" id="centro_id" />
                
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                        <i class="fas fa-hashtag" style="margin-right:6px;color:#0b3d91;"></i>Código <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" name="codigo" id="centro_codigo" required maxlength="10" placeholder="Ej: 006" style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;" onfocus="this.style.borderColor='#0b3d91'" onblur="this.style.borderColor='#e5e7eb'" />
                </div>
                
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                        <i class="fas fa-tag" style="margin-right:6px;color:#0b3d91;"></i>Nombre <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" name="nombre" id="centro_nombre" required maxlength="100" placeholder="Ej: Recursos Humanos" style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;" onfocus="this.style.borderColor='#0b3d91'" onblur="this.style.borderColor='#e5e7eb'" />
                </div>
                
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                        <i class="fas fa-align-left" style="margin-right:6px;color:#0b3d91;"></i>Descripción
                    </label>
                    <textarea name="descripcion" id="centro_descripcion" rows="3" maxlength="500" placeholder="Descripción opcional del centro de costo..." style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;resize:vertical;" onfocus="this.style.borderColor='#0b3d91'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                </div>
                
                <div style="margin-bottom:24px;">
                    <label style="display:flex;align-items:center;cursor:pointer;">
                        <input type="checkbox" name="activo" id="centro_activo" checked value="1" style="width:20px;height:20px;margin-right:8px;" />
                        <span style="font-weight:600;color:#374151;font-size:14px;">
                            <i class="fas fa-check-circle" style="margin-right:6px;color:#10b981;"></i>Activo
                        </span>
                    </label>
                </div>
                
                <!-- Footer -->
                <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:20px;border-top:1px solid #e5e7eb;">
                    <button type="button" id="btnCancelModalFooter" style="background:#f3f4f6;color:#374151;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.3s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                        Cancelar
                    </button>
                    <button type="submit" style="background:#0b3d91;color:#fff;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.3s;" onmouseover="this.style.background='#082863'" onmouseout="this.style.background='#0b3d91'">
                        <i class="fas fa-save" style="margin-right:6px;"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#modalCentroCosto input:focus, #modalCentroCosto select:focus, #modalCentroCosto textarea:focus {
    outline: none;
}
</style>
