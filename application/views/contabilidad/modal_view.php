<?php // modal_view.php - show journal header and lines with modern design ?>
<div id="modalViewEntry" class="modal-view-overlay" onclick="if(event.target===this) document.getElementById('modalContainer').innerHTML=''" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:10000;overflow-y:auto;padding:20px;">
    <div class="modal-view-content" style="width:95%;max-width:1000px;background:#fff;padding:0;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.2);max-height:90vh;display:flex;flex-direction:column;">
        <!-- Header -->
        <div style="background:<?php echo (isset($header->voided) && $header->voided == 1) ? '#dc3545' : '#007bff'; ?>;padding:20px 24px;border-radius:8px 8px 0 0;color:#fff;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <h3 style="margin:0;font-size:22px;font-weight:600;">Asiento N° <?php echo $header->id; ?></h3>
                    <p style="margin:6px 0 0;opacity:0.95;font-size:13px;">
                        <?php echo date('d/m/Y', strtotime($header->date)); ?> • 
                        <?php echo isset($header->entry_type) ? $header->entry_type : 'CD'; ?>
                    </p>
                    <p style="margin:4px 0 0;opacity:0.95;font-size:13px;">
                        <?php echo htmlspecialchars($header->description); ?>
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('modalContainer').innerHTML=''" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:4px;cursor:pointer;font-size:20px;line-height:1;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">&times;</button>
            </div>
        </div>
        
        <?php if (isset($header->voided) && $header->voided == 1): ?>
        <div style="margin:16px 24px 0;padding:12px 16px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:4px;font-weight:500;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-ban" style="font-size:18px;"></i>
            <div>
                <div style="font-size:14px;">Este asiento está <strong>ANULADO</strong></div>
                <div style="font-size:12px;font-weight:400;margin-top:2px;">
                    <?php if(isset($header->voided_at)): ?>
                        Anulado el <?php echo date('d/m/Y H:i', strtotime($header->voided_at)); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Body -->
        <div style="padding:24px;overflow-y:auto;flex:1;">
            <!-- Movimientos -->
            <div style="background:#fff;border:1px solid #dee2e6;border-radius:4px;padding:0;margin-bottom:20px;">
                <!-- Headers -->
                <div style="display:grid;grid-template-columns:2fr 0.8fr 0.8fr 0.8fr 0.8fr 1.2fr 2fr;gap:10px;padding:12px 16px;background:#f8f9fa;border-bottom:2px solid #dee2e6;font-weight:600;color:#495057;font-size:12px;">
                    <div>CUENTA</div>
                    <div style="text-align:right;">DEBE (NIO)</div>
                    <div style="text-align:right;">DEBE (USD)</div>
                    <div style="text-align:right;">HABER (NIO)</div>
                    <div style="text-align:right;">HABER (USD)</div>
                    <div>CENTRO COSTO</div>
                    <div>DETALLE</div>
                </div>
                
                <!-- Líneas -->
                <?php 
                // Get exchange rate
                $exchange_rate = 36.50; // Default
                if (isset($header->date)) {
                    $this->db->where('fecha', $header->date);
                    $this->db->order_by('id', 'DESC');
                    $tasa = $this->db->get('tb_tasa_cambio')->row();
                    if ($tasa && isset($tasa->tasa_cambio)) {
                        $exchange_rate = floatval($tasa->tasa_cambio);
                    }
                }
                foreach ($lines as $ln): 
                    $debit_usd = $ln->debit > 0 ? $ln->debit / $exchange_rate : 0;
                    $credit_usd = $ln->credit > 0 ? $ln->credit / $exchange_rate : 0;
                ?>
                <div style="display:grid;grid-template-columns:2fr 0.8fr 0.8fr 0.8fr 0.8fr 1.2fr 2fr;gap:10px;padding:12px 16px;border-bottom:1px solid #e9ecef;align-items:center;">
                    <div style="font-size:13px;color:#212529;">
                        <strong style="color:#007bff;"><?php echo $ln->code; ?></strong>
                        <span style="color:#6c757d;margin-left:6px;"><?php echo $ln->name; ?></span>
                    </div>
                    <div style="text-align:right;font-weight:600;color:<?php echo $ln->debit > 0 ? '#dc3545' : '#adb5bd'; ?>;font-size:13px;">
                        <?php echo $ln->debit > 0 ? 'C$' . number_format($ln->debit, 2) : '-'; ?>
                    </div>
                    <div style="text-align:right;font-weight:500;color:<?php echo $debit_usd > 0 ? '#dc3545' : '#adb5bd'; ?>;font-size:12px;">
                        <?php echo $debit_usd > 0 ? '$' . number_format($debit_usd, 2) : '-'; ?>
                    </div>
                    <div style="text-align:right;font-weight:600;color:<?php echo $ln->credit > 0 ? '#28a745' : '#adb5bd'; ?>;font-size:13px;">
                        <?php echo $ln->credit > 0 ? 'C$' . number_format($ln->credit, 2) : '-'; ?>
                    </div>
                    <div style="text-align:right;font-weight:500;color:<?php echo $credit_usd > 0 ? '#28a745' : '#adb5bd'; ?>;font-size:12px;">
                        <?php echo $credit_usd > 0 ? '$' . number_format($credit_usd, 2) : '-'; ?>
                    </div>
                    <div style="font-size:12px;color:#495057;">
                        <?php if (isset($ln->centro_costo_codigo) && !empty($ln->centro_costo_codigo)): ?>
                            <span style="background:#e7f5ff;color:#0070C0;padding:4px 8px;border-radius:4px;font-weight:500;">
                                <?php echo $ln->centro_costo_codigo; ?>
                            </span>
                        <?php else: ?>
                            <span style="color:#adb5bd;">-</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:13px;color:#6c757d;">
                        <?php echo htmlspecialchars($ln->line_description); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Totales -->
            <div style="background:#f8f9fa;padding:16px;border-radius:4px;border:1px solid #dee2e6;">
                <?php 
                // Calculate USD totals
                $total_debit_usd = $header->total_debit / $exchange_rate;
                $total_credit_usd = $header->total_credit / $exchange_rate;
                ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px;">
                    <div style="background:#fff;padding:12px;border-radius:4px;border:1px solid #dee2e6;">
                        <div style="color:#6c757d;font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Debe (NIO)</div>
                        <div style="font-size:20px;font-weight:700;color:#dc3545;">C$<?php echo number_format($header->total_debit, 2); ?></div>
                    </div>
                    <div style="background:#fff;padding:12px;border-radius:4px;border:1px solid #dee2e6;">
                        <div style="color:#6c757d;font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Debe (USD)</div>
                        <div style="font-size:20px;font-weight:700;color:#dc3545;">$<?php echo number_format($total_debit_usd, 2); ?></div>
                    </div>
                    <div style="background:#fff;padding:12px;border-radius:4px;border:1px solid #dee2e6;">
                        <div style="color:#6c757d;font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Haber (NIO)</div>
                        <div style="font-size:20px;font-weight:700;color:#28a745;">C$<?php echo number_format($header->total_credit, 2); ?></div>
                    </div>
                    <div style="background:#fff;padding:12px;border-radius:4px;border:1px solid #dee2e6;">
                        <div style="color:#6c757d;font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Total Haber (USD)</div>
                        <div style="font-size:20px;font-weight:700;color:#28a745;">$<?php echo number_format($total_credit_usd, 2); ?></div>
                    </div>
                </div>
                
                <?php 
                $diff = abs($header->total_debit - $header->total_credit);
                $cuadrado = $diff < 0.01;
                ?>
                <div style="margin-top:12px;text-align:center;font-weight:600;font-size:14px;padding:10px;border-radius:4px;background:<?php echo $cuadrado ? '#d4edda' : '#f8d7da'; ?>;color:<?php echo $cuadrado ? '#155724' : '#721c24'; ?>;border:1px solid <?php echo $cuadrado ? '#c3e6cb' : '#f5c6cb'; ?>;">
                    <?php if($cuadrado): ?>
                        ✓ Asiento cuadrado
                    <?php else: ?>
                        ⚠ Diferencia: C$<?php echo number_format($diff, 2); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid #dee2e6;background:#f8f9fa;">
            <button type="button" onclick="document.getElementById('modalContainer').innerHTML=''" class="btn btn-secondary">
                <i class="fas fa-times" style="margin-right:6px;"></i>Cerrar
            </button>
            <button type="button" onclick="window.open('<?php echo site_url('contabilidad/diario_print?id=' . urlencode($header->id)); ?>','_blank')" class="btn btn-primary">
                <i class="fas fa-print" style="margin-right:6px;"></i>Imprimir
            </button>
        </div>
    </div>
</div>

<style>
#modalViewEntry button:hover {
    transform: translateY(-2px);
}
</style>