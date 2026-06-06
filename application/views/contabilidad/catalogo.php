<style>
    .servicont-catalogo-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .servicont-catalogo-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .servicont-catalogo-title {
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    .servicont-catalogo-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 15px;
        margin: 5px 0 0 0;
        position: relative;
        z-index: 1;
    }
    
    .servicont-catalogo-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    
    .servicont-btn-primary {
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(42, 82, 152, 0.3);
    }
    
    .servicont-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(42, 82, 152, 0.4);
        color: #ffffff;
    }
    
    .servicont-btn-secondary {
        background: #ffffff;
        color: #2a5298;
        border: 2px solid #2a5298;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .servicont-btn-secondary:hover {
        background: #2a5298;
        color: #ffffff;
    }
    
    .servicont-btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }
    
    .servicont-btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
        color: #ffffff;
    }
    
    .servicont-input {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
        min-width: 0;
    }
    
    .servicont-input:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
    }

    .servicont-input.select-full {
        padding-right: 2.8rem;
        width: 100%;
        min-width: 0;
        min-height: 44px;
        height: 44px;
        line-height: 1.4;
    }

    .servicont-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .servicont-toolbar .servicont-btn-primary,
    .servicont-toolbar .servicont-btn-secondary,
    .servicont-toolbar .servicont-btn-success,
    .servicont-toolbar .btn-group {
        margin: 0;
        flex: 0 1 auto;
    }

    .servicont-toolbar .btn-group {
        display: inline-flex;
    }

    .servicont-toolbar .servicont-btn-primary,
    .servicont-toolbar .servicont-btn-secondary,
    .servicont-toolbar .servicont-btn-success {
        min-width: 140px;
    }

    @media (max-width: 768px) {
        .servicont-toolbar {
            justify-content: flex-start;
        }
        .servicont-toolbar .servicont-btn-primary,
        .servicont-toolbar .servicont-btn-secondary,
        .servicont-toolbar .servicont-btn-success,
        .servicont-toolbar .btn-group {
            flex: 1 1 45%;
            min-width: 140px;
        }
        .servicont-toolbar .btn-group {
            width: auto;
        }
    }
    
    .servicont-table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .servicont-table thead {
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
    }
    
    .servicont-table thead th {
        color: #ffffff;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
    }
    
    .servicont-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .servicont-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-catalogo-header">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-book" style="font-size: 24px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h1 class="servicont-header-title">Catálogo de Cuentas</h1>
                        <p class="servicont-header-subtitle">Gestión del Plan de Cuentas Contable</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="servicont-catalogo-card">
                        <div class="card-body" style="padding: 30px;">
            <div class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="servicont-toolbar">
                            <button id="btnNewAccount" class="servicont-btn-primary"><i class="fas fa-plus mr-2"></i>Nueva Cuenta</button>
                            <button id="btnRefreshAccounts" class="servicont-btn-secondary"><i class="fas fa-sync-alt mr-2"></i>Actualizar</button>
                            <div class="btn-group" role="group">
                                <button id="btnExportExcel" type="button" class="servicont-btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" id="exportBasic"><i class="fas fa-file-alt mr-2"></i>Catálogo Básico</a>
                                    <a class="dropdown-item" href="#" id="exportBimoneda"><i class="fas fa-dollar-sign mr-2"></i>Balance Bimoneda</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <input id="filterAccount" class="form-control servicont-input" placeholder="🔍 Buscar por código o nombre..." />
                    </div>
                    <div class="col-md-4">
                        <select id="filterType" class="form-control servicont-input select-full">
                            <option value="">📋 Todos los tipos</option>
                            <option value="activo">Activo</option>
                            <option value="pasivo">Pasivo</option>
                            <option value="patrimonio">Patrimonio</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="gasto">Gasto</option>
                            <option value="contingente">Contingente</option>
                            <option value="orden">Orden</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="clearFilters" class="servicont-btn-secondary btn-block"><i class="fas fa-redo mr-2"></i>Limpiar</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="accountsTable" class="table servicont-table" style="font-size:15px; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width:120px">Código</th>
                            <th>Nombre</th>
                            <th style="width:120px">Tipo</th>
                            <th style="width:180px">Agrupación (Estado SF)</th>
                            <th style="width:100px">Naturaleza</th>
                            <th style="width:140px;text-align:right">Saldo</th>
                            <th style="width:140px;text-align:center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="accountsBody">
                        <?php if (isset($accounts) && is_array($accounts) && count($accounts)): ?>
                            <?php foreach ($accounts as $a): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($a->code); ?></strong></td>
                                    <td><?php echo htmlspecialchars($a->name); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars(ucfirst(strtolower(trim($a->type ?? '')))); ?></td>
                                    <td style="font-weight:600;color:#2a5298">
                                        <?php
                                            $tipo = isset($a->type) ? strtolower(trim($a->type)) : '';
                                            $grp = '';
                                            if (isset($a->agrupador_estado) && trim($a->agrupador_estado) !== '') {
                                                $grp = $a->agrupador_estado;
                                            } elseif (in_array($tipo, ['activo','pasivo','patrimonio'])) {
                                                if (isset($a->report_bs) && trim($a->report_bs) !== '') {
                                                    $grp = $a->report_bs;
                                                }
                                            } else {
                                                if (isset($a->report_is) && trim($a->report_is) !== '') {
                                                    $grp = $a->report_is;
                                                }
                                            }
                                            if ($grp !== '') {
                                                echo htmlspecialchars($grp);
                                            } else {
                                                echo '<span style="color:#94a3b8;font-size:11px;">Sin definir</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if(isset($a->naturaleza) && $a->naturaleza): ?>
                                            <span style="background:#64748b;color:#fff;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:600;white-space:nowrap;border:1px solid #475569;">
                                                <?php if($a->naturaleza == 'deudora'): ?>
                                                    <i class="fas fa-arrow-up" style="color:#ef4444;font-size:9px;"></i> Deudora
                                                <?php else: ?>
                                                    <i class="fas fa-arrow-down" style="color:#10b981;font-size:9px;"></i> Acreedora
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:#94a3b8;font-size:11px;">Sin definir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:right"><?php echo number_format(floatval(isset($a->balance) ? $a->balance : 0), 2, '.', ''); ?></td>
                                    <td style="text-align:center">
                                        <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?php echo intval($a->id); ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo intval($a->id); ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Cargando...</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div id="catalogoPagingInfo" class="text-muted"></div>
                </div>
                <div class="col-md-6">
                    <nav>
                        <ul class="pagination justify-content-end mb-0" id="catalogoPagination">
                        </ul>
                    </nav>
                </div>
            </div>

            <div id="modalContainer"></div>
                        </div>
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
<?php
    // Append filemtime as cache-buster so browsers load the latest patched JS
    $js_file = FCPATH . 'public/js/contabilidad_catalogo.js';
    $ver = (file_exists($js_file)) ? filemtime($js_file) : time();
?>
<!-- Script is loaded via controller 'scripts' array and footer include (cache-busted there) -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        var s = document.querySelector('.app-sidebar');
        var w = document.querySelector('.wrapper');
        if (s && s.classList.contains('hide-sidebar')) s.classList.remove('hide-sidebar');
        if (w) { w.classList.remove('nav-collapsed'); w.classList.remove('menu-collapsed'); }
    } catch (e) {
        console && console.error && console.error('sidebar-hotfix-error', e);
    }
});
</script>
