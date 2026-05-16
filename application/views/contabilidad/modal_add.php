<?php // modal_add.php - Enhanced modern modal ?>
<div id="modalAddEntry" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;overflow-y:auto;padding:20px;">
    <div style="width:98%;max-width:1600px;background:#fff;padding:0;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.3);max-height:90vh;display:flex;flex-direction:column;">
        <!-- Header -->
        <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);padding:24px 32px;border-radius:12px 12px 0 0;color:#fff;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <h2 style="margin:0;font-size:28px;font-weight:600;">Nuevo Asiento</h2>
                    <p style="margin:4px 0 0;opacity:0.9;font-size:14px;">Registro de asientos contables</p>
                </div>
                <button type="button" id="btnCancelModal" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:24px;line-height:1;transition:all 0.3s;">×</button>
            </div>
        </div>
        
        <!-- Body -->
        <div style="padding:32px;overflow-y:auto;flex:1;">
            <form id="formNewEntry">
                <!-- Hidden exchange rate field -->
                <input type="hidden" id="exchangeRate" value="36.50" />
                
                <!-- Tipo de Documento -->
                <div style="display:grid;grid-template-columns:280px;gap:20px;margin-bottom:24px;">
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                            <i class="fas fa-file-invoice" style="margin-right:6px;color:#667eea;"></i>Tipo de Documento <span style="color:#ef4444;">*</span>
                        </label>
                        <select name="document_type" required style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;background:#fff;cursor:pointer;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'">
                            <option value="">-- Seleccione tipo --</option>
                            <option value="CD">CD - Comprobante de Diario</option>
                            <option value="CI">CI - Comprobante de Ingreso</option>
                            <option value="CE">CE - Comprobante de Egreso</option>
                            <option value="CT">CT - Comprobante de Traspaso</option>
                            <option value="CA">CA - Comprobante de Ajuste</option>
                            <option value="CN">CN - Comprobante de Nómina</option>
                            <option value="CAP">CAP - Comprobante de Apertura</option>
                            <option value="CCIER">CCIER - Comprobante de Cierre</option>
                            <option value="CDEP">CDEP - Comprobante de Depreciación</option>
                            <option value="CPROV">CPROV - Comprobante de Provisiones</option>
                        </select>
                    </div>
                </div>
                
                <!-- Fecha y Descripción -->
                <div style="display:grid;grid-template-columns:200px 1fr;gap:20px;margin-bottom:24px;">
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                            <i class="fas fa-calendar-alt" style="margin-right:6px;color:#667eea;"></i>Fecha <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'" />
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#374151;font-size:14px;">
                            <i class="fas fa-align-left" style="margin-right:6px;color:#667eea;"></i>Descripción <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea name="description" placeholder="Descripción del asiento contable..." required rows="3" style="width:100%;padding:12px 14px;border:2px solid #e5e7eb;border-radius:8px;font-size:15px;transition:all 0.3s;resize:vertical;font-family:inherit;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                    </div>
                </div>

                <!-- Líneas de asiento -->
                <div style="margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <h4 style="margin:0;font-size:18px;font-weight:600;color:#1f2937;">
                            <i class="fas fa-list-ul" style="margin-right:8px;color:#667eea;"></i>Movimientos Contables
                        </h4>
                        <button type="button" id="btnAddLine" style="background:#667eea;color:#fff;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.3s;display:flex;align-items:center;gap:8px;" onmouseover="this.style.background='#5568d3'" onmouseout="this.style.background='#667eea'">
                            <i class="fas fa-plus-circle"></i> Agregar Línea
                        </button>
                    </div>

                    <div id="linesWrapper" style="background:#fff;border:2px solid #e5e7eb;border-radius:8px;padding:16px;">
                        <!-- Headers -->
                        <div style="display:grid;grid-template-columns:1.5fr 2.5fr 1fr 1fr 1fr 1fr 50px;gap:12px;padding-bottom:8px;font-weight:600;color:#6b7280;font-size:12px;text-transform:uppercase;">
                            <div>CENTRO DE COSTO</div>
                            <div>CUENTA</div>
                            <div style="text-align:center;">DEBE (NIO)</div>
                            <div style="text-align:center;">DEBE (USD)</div>
                            <div style="text-align:center;">HABER (NIO)</div>
                            <div style="text-align:center;">HABER (USD)</div>
                            <div></div>
                        </div>
                        <div style="padding-bottom:8px;font-weight:600;color:#6b7280;font-size:12px;text-transform:uppercase;padding-left:2px;">
                            <div>DETALLE</div>
                        </div>
                        
                        <!-- Primera línea -->
                        <div class="entry-line" style="margin-bottom:16px;">
                            <div style="display:grid;grid-template-columns:1.5fr 2.5fr 1fr 1fr 1fr 1fr 50px;gap:12px;margin-bottom:8px;align-items:center;">
                                <select name="lines[0][centro_costo_id]" class="line-centro-costo" data-line="0" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'">
                                    <option value="">-- Centro de Costo --</option>
                                    <?php if (!empty($centros_costo)): ?>
                                        <?php foreach ($centros_costo as $cc): ?>
                                            <option value="<?php echo $cc->id; ?>"><?php echo $cc->codigo . ' - ' . $cc->nombre; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div style="height:44px;">
                                    <select name="lines[0][account_id]" class="account-select" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'">
                                        <option value="">-- Buscar por código o nombre --</option>
                                    </select>
                                </div>
                                <input type="number" step="0.01" name="lines[0][debit]" placeholder="0.00" class="line-debit line-debit-mxn" data-line="0" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#e5e7eb'" />
                                <input type="number" step="0.01" name="lines[0][debit_usd]" placeholder="0.00" class="line-debit-usd" data-line="0" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;background:#fef3c7;transition:all 0.3s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'" />
                                <input type="number" step="0.01" name="lines[0][credit]" placeholder="0.00" class="line-credit line-credit-mxn" data-line="0" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'" />
                                <input type="number" step="0.01" name="lines[0][credit_usd]" placeholder="0.00" class="line-credit-usd" data-line="0" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;background:#d1fae5;transition:all 0.3s;" onfocus="this.style.borderColor='#059669'" onblur="this.style.borderColor='#e5e7eb'" />
                                <button type="button" class="btn-remove-line" data-line="0" style="background:#ef4444;color:#fff;border:none;width:44px;height:44px;border-radius:6px;cursor:pointer;font-size:18px;line-height:1;transition:all 0.3s;opacity:0.5;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">×</button>
                            </div>
                            <div>
                                <input name="lines[0][description]" placeholder="Detalle del movimiento..." style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totales -->
                <div style="background:linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);padding:20px;border-radius:8px;margin-bottom:24px;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;">
                        <div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                            <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Debe (NIO)</div>
                            <div style="font-size:24px;font-weight:700;color:#ef4444;" id="totalDebitNIO">C$0.00</div>
                        </div>
                        <div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                            <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Debe (USD)</div>
                            <div style="font-size:24px;font-weight:700;color:#f59e0b;" id="totalDebitUSD">$0.00</div>
                        </div>
                        <div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                            <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Haber (NIO)</div>
                            <div style="font-size:24px;font-weight:700;color:#10b981;" id="totalCreditNIO">C$0.00</div>
                        </div>
                        <div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                            <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Haber (USD)</div>
                            <div style="font-size:24px;font-weight:700;color:#059669;" id="totalCreditUSD">$0.00</div>
                        </div>
                    </div>
                    <div id="entryMessage" style="margin-top:16px;text-align:center;font-weight:600;font-size:16px;padding:12px;border-radius:6px;"></div>
                </div>

                <!-- Botones de acción -->
                <div style="display:flex;justify-content:flex-end;gap:12px;padding-top:20px;border-top:2px solid #e5e7eb;">
                    <button type="button" id="btnCancelModalFooter" style="background:#fff;color:#6b7280;border:2px solid #d1d5db;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:600;font-size:15px;transition:all 0.3s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                        <i class="fas fa-times" style="margin-right:6px;"></i>Cancelar
                    </button>
                    <button type="submit" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:#fff;border:none;padding:12px 32px;border-radius:8px;cursor:pointer;font-weight:600;font-size:15px;transition:all 0.3s;box-shadow:0 4px 6px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 12px rgba(102, 126, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 6px rgba(102, 126, 234, 0.3)'">
                        <i class="fas fa-save" style="margin-right:8px;"></i>Guardar Asiento
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
    // Ensure JS has correct endpoints regardless of rewrite/index.php config
    window.ADD_ENTRY_URL = '<?php echo site_url("contabilidad/add_entry"); ?>';
    window.UPDATE_ENTRY_URL = '<?php echo site_url("contabilidad/update_entry"); ?>';
</script>

<style>

#modalAddEntry input:focus, #modalAddEntry select:focus {
    outline: none;
}
#btnCancelModal:hover {
    background: rgba(255,255,255,0.3) !important;
    transform: scale(1.1);
}

/* Select2 custom styling for modal */
.entry-line > div:first-child {
    height: 44px !important;
    overflow: hidden !important;
}
.select2-container {
    width: 100% !important;
    max-width: 100% !important;
    height: 44px !important;
    display: block !important;
}
.select2-container--default .select2-selection--single {
    border: 2px solid #e5e7eb !important;
    border-radius: 6px !important;
    height: 44px !important;
    padding: 8px 12px !important;
    min-width: 100% !important;
    max-width: 100% !important;
    line-height: 26px !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px !important;
    padding-left: 0 !important;
    padding-right: 30px !important;
    font-size: 13px !important;
    color: #1f2937 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    display: block !important;
    max-width: 100% !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
    right: 8px !important;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #667eea !important;
}
.select2-dropdown {
    border: 2px solid #667eea !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2) !important;
}
.select2-search--dropdown .select2-search__field {
    border: 2px solid #e5e7eb !important;
    border-radius: 6px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
}
.select2-search--dropdown .select2-search__field:focus {
    border-color: #667eea !important;
    outline: none !important;
}
.select2-results__option {
    padding: 8px 12px !important;
    font-size: 14px !important;
}
</style>
