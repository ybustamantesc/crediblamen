// ...existing code...

<!-- Script para resaltar asiento relacionado desde Tesorería -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        var id = localStorage.getItem('highlightDiarioAsiento');
        if(id){
            var row = document.querySelector('.entry-row[data-id="'+id+'"]');
            if(row){
                row.style.background = '#fffbe6';
                row.style.boxShadow = '0 0 0 2px #facc15';
                row.scrollIntoView({behavior:'smooth',block:'center'});
            }
            localStorage.removeItem('highlightDiarioAsiento');
        }
    } catch(e) { /* Silenciar errores JS */ }
});
</script>
<style>
    .servicont-diario-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .servicont-diario-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .servicont-diario-btn {
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(42, 82, 152, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .servicont-diario-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(42, 82, 152, 0.4);
        color: #ffffff;
    }
    
    .servicont-diario-btn i {
        font-size: 14px;
    }

    .diario-wrapper {
        overflow-x: auto;
        width: 100%;
    }

    .table-diary {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
        min-width: 100%;
    }

    .table-diary th,
    .table-diary td {
        word-break: break-word;
        white-space: normal;
        vertical-align: middle;
    }

    .servicont-catalogo-card.servicont-diario-card,
    .servicont-catalogo-card.servicont-diario-card .card-body {
        overflow: visible !important;
    }

    .table-diary th:first-child,
    .table-diary td:first-child {
        white-space: nowrap;
        width: 50px;
    }

    .table-diary th.col-actions,
    .table-diary td.col-actions {
        white-space: nowrap;
        width: 130px;
        max-width: 130px;
    }

    .diario-wrapper,
    .table-diary,
    .table-diary th,
    .table-diary td {
        overflow: visible !important;
    }

    .diario-wrapper .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .diario-wrapper .table-responsive table {
        overflow: visible !important;
    }

    @media (max-width: 980px) {
        .table-diary {
            min-width: 920px;
        }
    }

    .actions-menu {
        position: relative;
        display: inline-block;
        text-align: center;
        z-index: 9999;
        overflow: visible !important;
        width: 100%;
    }

    .actions-menu summary {
        list-style: none;
        cursor: pointer;
        background: #475569;
        color: #fff;
        border: none;
        padding: 8px 10px;
        border-radius: 8px;
        font-weight: 600;
        outline: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        position: relative;
        z-index: 9999;
        white-space: nowrap;
        min-width: 90px;
        max-width: 120px;
        text-align: center;
        line-height: 1.2;
    }

    .actions-menu summary::-webkit-details-marker {
        display: none;
    }

    .actions-menu[open] summary {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .actions-list {
        display: flex;
        position: fixed;
        right: auto;
        bottom: auto;
        top: 0;
        left: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        min-width: 220px;
        max-width: none;
        overflow: visible;
        z-index: 9998;
        padding: 8px;
        white-space: normal;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-start;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        transition: none;
    }

    .actions-menu[open] .actions-list {
        display: flex;
    }

    .actions-list button {
        min-width: 100px;
        flex: 1 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        color: #1f2937;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
    }

    .actions-list button:hover {
        background: #f3f4f6;
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-diario-header">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-book-open" style="font-size: 24px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h1 class="servicont-header-title">Libro Diario</h1>
                        <p class="servicont-header-subtitle">Registro de asientos contables del sistema</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="servicont-catalogo-card servicont-diario-card">
                        <div class="card-body" style="position:relative; padding: 30px; overflow: visible;">
                            <!-- Barra de herramientas -->
                            <div class="servicont-toolbar">
                                <button id="btnNewAsiento" class="servicont-diario-btn">
                                    <i class="fas fa-plus"></i> Nuevo Asiento
                                </button>
                                
                                <button id="btnMassPost" class="servicont-diario-btn" style="display:none;">
                                    <i class="fas fa-check-double"></i> Mayorizar Seleccionados (<span id="selectedCount">0</span>)
                                </button>
                                
                                <button id="btnExportPDF" class="servicont-diario-btn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button id="btnExportCSV" class="servicont-diario-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <i class="fas fa-file-csv"></i> Exportar CSV
                                </button>
                                
                                <!-- Filtro por tipo de documento -->
                                <select id="filterDocType" class="form-control" style="min-width:200px;">
                                    <option value="">📋 Todos los tipos</option>
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
                                
                                <!-- Filtro por centro de costo (eliminado según nueva lógica) -->
                                
                                <!-- Buscador por ID -->
                                <div style="position:relative;flex:1;min-width:250px;max-width:400px;">
                                    <input type="text" id="searchAsientoId" class="form-control" placeholder="🔍 Buscar por ID o descripción..." />
                                </div>
                                
                                <button id="btnClearFilters" class="btn" style="background:#475569;color:white;border:none;font-weight:500;">
                                    <i class="fas fa-redo" style="color:#64748b;"></i> Limpiar
                                </button>
                            </div>
                            
                            <div id="diarioContent">
                                <?php if (isset($entries) && is_array($entries) && count($entries)): ?>
                                    <div class="diario-wrapper">
                                        <div class="table-responsive">
                                            <table class="table-diary">
                                                <thead>
                                                <tr style="background:linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;width:50px;">
                                                        <input type="checkbox" id="selectAll" style="width:18px;height:18px;cursor:pointer;" />
                                                    </th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Estado</th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Código</th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Tipo</th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Centro Costo</th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Fecha</th>
                                                    <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;">Descripción / Montos</th>
                                                    <th class="col-actions" style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;text-align:center;white-space:nowrap;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($entries as $d): 
                                                $entry_type = isset($d->entry_type) && $d->entry_type ? $d->entry_type : 'CD';
                                                // Usar color neutro único para todos los tipos
                                                $color = '#475569';
                                                $is_voided = isset($d->voided) && $d->voided == 1;
                                                $is_posted = isset($d->posted) && $d->posted == 1;
                                                $centro_costo_ids = isset($d->centro_costo_ids) ? $d->centro_costo_ids : '';
                                                $centro_costo_nombres = isset($d->centro_costo_nombres) && $d->centro_costo_nombres ? $d->centro_costo_nombres : '-';
                                            ?>
                                                <tr class="entry-row" data-id="<?php echo $d->id; ?>" data-type="<?php echo $entry_type; ?>" data-centro="<?php echo $centro_costo_ids; ?>" data-description="<?php echo strtolower(htmlspecialchars($d->description)); ?>" data-date="<?php echo date('Y-m-d', strtotime($d->date)); ?>" data-posted="<?php echo $is_posted ? '1' : '0'; ?>" data-voided="<?php echo $is_voided ? '1' : '0'; ?>" style="border-bottom:1px solid #f3f4f6;transition:all 0.2s;<?php if($is_voided) echo 'opacity:0.5;'; ?>" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                                                    <td style="padding:14px 12px;text-align:center;">
                                                        <?php if(!$is_voided && !$is_posted): ?>
                                                            <input type="checkbox" class="entry-checkbox" data-id="<?php echo $d->id; ?>" style="width:18px;height:18px;cursor:pointer;" />
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="padding:14px 12px;text-align:center;">
                                                        <?php if($is_voided): ?>
                                                            <span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;">
                                                                <i class="fas fa-ban" style="color:#ef4444;"></i> ANULADO
                                                            </span>
                                                        <?php elseif($is_posted): ?>
                                                            <span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;">
                                                                <i class="fas fa-check-double" style="color:#10b981;"></i> MAYORIZADO
                                                            </span>
                                                        <?php else: ?>
                                                            <span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;animation:pulse 2s infinite;">
                                                                <i class="fas fa-clock" style="color:#f59e0b;"></i> PENDIENTE
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="padding:14px 12px;font-weight:700;color:<?php echo $color; ?>;font-size:15px;">
                                                        <?php echo $entry_type . '-' . $d->id; ?>
                                                    </td>
                                                    <td style="padding:14px 12px;">
                                                        <span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;border:2px solid #475569;">
                                                            <i class="fas fa-file-alt" style="color:#94a3b8;font-size:9px;margin-right:4px;"></i><?php echo $entry_type; ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding:14px 12px;color:#6b7280;font-size:13px;">
                                                        <span style="display:inline-block;background:#f3f4f6;color:#4b5563;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;">
                                                            <?php echo $centro_costo_nombres; ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding:14px 12px;color:#6b7280;font-size:14px;">
                                                        <?php echo date('d/m/Y', strtotime($d->date)); ?>
                                                    </td>
                                                    <td style="padding:14px 12px;">
                                                        <div style="font-weight:500;color:#1f2937;font-size:14px;margin-bottom:6px;overflow-wrap:break-word;word-break:break-word;white-space:normal;">
                                                            <?php echo htmlspecialchars($d->description); ?>
                                                            <?php if($is_voided): ?>
                                                                <span style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;margin-left:8px;">ANULADO</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div style="display:flex;gap:16px;font-size:13px;">
                                                            <span style="color:#ef4444;font-weight:600;">
                                                                <i class="fas fa-arrow-up" style="font-size:10px;margin-right:4px;"></i>Debe: C$<?php echo number_format($d->total_debit,2); ?>
                                                            </span>
                                                            <span style="color:#10b981;font-weight:600;">
                                                                <i class="fas fa-arrow-down" style="font-size:10px;margin-right:4px;"></i>Haber: C$<?php echo number_format($d->total_credit,2); ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="col-actions" style="padding:14px 12px;text-align:center;white-space:nowrap;position:relative;z-index:1;">
                                                        <details class="actions-menu">
                                                            <summary>Acciones</summary>
                                                            <div class="actions-list">
                                                                <button type="button" class="cc-btn cc-btn-view" data-id="<?php echo $d->id; ?>">
                                                                    <i class="fas fa-eye" style="margin-right:8px;color:#3b82f6;"></i> Ver
                                                                </button>
                                                                <?php if(!$is_voided && !$is_posted): ?>
                                                                    <button type="button" class="cc-btn cc-btn-edit" data-id="<?php echo $d->id; ?>">
                                                                        <i class="fas fa-edit" style="margin-right:8px;color:#f59e0b;"></i> Editar
                                                                    </button>
                                                                    <button type="button" class="cc-btn cc-btn-void" data-id="<?php echo $d->id; ?>">
                                                                        <i class="fas fa-ban" style="margin-right:8px;color:#ef4444;"></i> Anular
                                                                    </button>
                                                                    <button type="button" class="cc-btn cc-btn-post" data-id="<?php echo $d->id; ?>">
                                                                        <i class="fas fa-check-double" style="margin-right:8px;color:#10b981;"></i> Mayorizar
                                                                    </button>
                                                                <?php elseif($is_posted && !$is_voided): ?>
                                                                    <button type="button" class="cc-btn cc-btn-unpost" data-id="<?php echo $d->id; ?>">
                                                                        <i class="fas fa-unlock" style="margin-right:8px;color:#06b6d4;"></i> Desmayorizar
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </details>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                    
                                    <div id="noResultsMessage" style="display:none;padding:40px;text-align:center;color:#6b7280;">
                                        <i class="fas fa-search" style="font-size:48px;color:#d1d5db;margin-bottom:16px;"></i>
                                        <div style="font-size:18px;font-weight:600;margin-bottom:8px;">No se encontraron asientos</div>
                                        <div style="font-size:14px;">Intenta con otros criterios de búsqueda</div>
                                    </div>
                                <?php else: ?>
                                    <div style="padding:40px;text-align:center;color:#6b7280;background:#f9fafb;border-radius:8px;">
                                        <i class="fas fa-book-open" style="font-size:48px;color:#d1d5db;margin-bottom:16px;"></i>
                                        <div style="font-size:18px;font-weight:600;margin-bottom:8px;">No hay asientos registrados</div>
                                        <div style="font-size:14px;">Haz clic en "Nuevo Asiento" para crear el primero</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div id="modalContainer"></div>
                            
                            <!-- Hidden fields for PDF export -->
                            <input type="hidden" id="empresa_razon_social" value="<?php echo isset($empresa->razon_social) ? htmlspecialchars($empresa->razon_social) : 'Empresa'; ?>">
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

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function refreshActionsMenu(details) {
        var menu = details.querySelector('.actions-list');
        var summary = details.querySelector('summary');
        if (!menu || !summary) return;

        if (!details.open) {
            menu.style.visibility = 'hidden';
            menu.style.opacity = '0';
            menu.style.pointerEvents = 'none';
            return;
        }

        var rect = summary.getBoundingClientRect();
        var top = rect.top - menu.offsetHeight - 8;
        var left = rect.right - menu.offsetWidth;
        left = Math.min(Math.max(8, left), window.innerWidth - menu.offsetWidth - 8);
        top = Math.max(8, top);

        menu.style.position = 'fixed';
        menu.style.zIndex = '21000';
        menu.style.top = top + 'px';
        menu.style.left = left + 'px';
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
        menu.style.visibility = 'visible';
        menu.style.opacity = '1';
        menu.style.pointerEvents = 'auto';
    }

    var allDetails = Array.from(document.querySelectorAll('details.actions-menu'));
    document.body.addEventListener('toggle', function(event) {
        var details = event.target.closest('details.actions-menu');
        if (!details) return;
        setTimeout(function() {
            refreshActionsMenu(details);
        }, 0);
    }, true);

    window.addEventListener('scroll', function() {
        allDetails.filter(function(details) { return details.open; }).forEach(function(details) {
            setTimeout(function() {
                refreshActionsMenu(details);
            }, 0);
        });
    });

    window.addEventListener('resize', function() {
        allDetails.filter(function(details) { return details.open; }).forEach(function(details) {
            setTimeout(function() {
                refreshActionsMenu(details);
            }, 0);
        });
    });
});
</script>

<!-- Scripts are loaded via the footer using $scripts from the controller to avoid duplicates -->
