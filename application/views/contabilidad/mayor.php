<style>
    .servicont-mayor-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    .servicont-mayor-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
</style>

<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap servicont-main-wrapper">
    <?php $this->load->view('contabilidad/sidebar_contabilidad'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="servicont-mayor-header">
                <div class="d-flex align-items-center">
                    <div class="servicont-header-icon" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-file-text" style="font-size: 24px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h1 class="servicont-catalogo-title">Libro Mayor</h1>
                        <p class="servicont-catalogo-subtitle" style="color: #ffffff !important;">Reporte de saldos y movimientos por cuenta</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="servicont-catalogo-card">
                        <div class="card-body" style="padding: 30px;">
                            <div id="mayorControls" class="mb-4">
                                <div class="form-row mb-3">
                                    <div class="col-md-4">
                                        <label style="font-weight: 600; color: #2a5298; margin-bottom: 8px;">Cuenta</label>
                                        <select id="mayorAccount" class="form-control servicont-input"></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label style="font-weight: 600; color: #2a5298; margin-bottom: 8px;">Desde</label>
                                        <input type="date" id="mayorStart" class="form-control servicont-input" />
                                    </div>
                                    <div class="col-md-3">
                                        <label style="font-weight: 600; color: #2a5298; margin-bottom: 8px;">Hasta</label>
                                        <input type="date" id="mayorEnd" class="form-control servicont-input" />
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button id="mayorRefresh" class="servicont-btn-primary btn-block">Actualizar</button>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <button id="mayorExport" class="servicont-btn-secondary btn-block">Exportar página</button>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="mayorExportAll" class="servicont-btn-secondary btn-block">Imprimir Todas las cuentas</button>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="mayorPrint" class="servicont-btn-secondary btn-block">Imprimir</button>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><label class="input-group-text" style="background: #2a5298; color: white; border: none;">Mostrar</label></div>
                                            <select id="mayorPerPage" class="form-control servicont-input">
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="250">250</option>
                                                <option value="500" selected>500</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button id="mayorPrev" class="servicont-btn-secondary">«</button>
                                                <button id="mayorNext" class="servicont-btn-secondary">»</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="mayorContent">
                                <div class="table-responsive">
                                    <table id="mayorTable" class="table servicont-table">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Asiento</th>
                                                <th>Descripción</th>
                                                <th class="text-right">Debe</th>
                                                <th class="text-right">Haber</th>
                                                <th class="text-right">Saldo (Deudor)</th>
                                                <th class="text-right">Saldo (Acreedor)</th>
                                                <th>Acciones</th>
                                            </div>
                                        </thead>
                                        <style>
                                            /* If server provided entries (posted journals), hide the account-based controls and show a simple posted list */
                                            <?php if (!empty($entries)): ?>
                                                #mayorControls { display: none !important; }
                                            <?php endif; ?>
                                        </style>

                                        <div id="mayorContent">
                                            <?php if (!empty($entries)): ?>
                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label style="font-weight:600;color:#2a5298;">Desde</label>
                                                        <input type="date" id="mayorFilterStart" class="form-control servicont-input" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label style="font-weight:600;color:#2a5298;">Hasta</label>
                                                        <input type="date" id="mayorFilterEnd" class="form-control servicont-input" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label style="font-weight:600;color:#2a5298;">Asiento</label>
                                                        <input type="text" id="mayorSearchAsiento" class="form-control servicont-input" placeholder="N° asiento" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label style="font-weight:600;color:#2a5298;">Descripción</label>
                                                        <input type="text" id="mayorSearchDesc" class="form-control servicont-input" placeholder="Texto en descripción" />
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <button id="mayorFilterBtn" class="servicont-btn-primary" style="margin-right:10px;"><i class="fas fa-filter" style="margin-right:6px"></i>Filtrar</button>
                                                        <button id="mayorFilterReset" class="servicont-btn-secondary" style="margin-right:10px;"><i class="fas fa-eraser" style="margin-right:6px"></i>Reset</button>
                                                        <button id="mayorExportPosted" class="servicont-btn-secondary"><i class="fas fa-file-excel" style="margin-right:6px"></i>Exportar a Excel</button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="mayorTablePosted" class="table servicont-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Fecha</th>
                                                                <th>Asiento</th>
                                                                <th>Descripción</th>
                                                                <th class="text-right">Debe</th>
                                                                <th class="text-right">Haber</th>
                                                                <th class="text-center">Estado</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($entries as $e): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($e->date))); ?></td>
                                                                    <td><?php echo intval($e->id); ?></td>
                                                                    <td><?php echo htmlspecialchars($e->description ?? ''); ?></td>
                                                                    <td class="text-right"><?php echo number_format(floatval($e->total_debit ?? 0),2,',','.'); ?></td>
                                                                    <td class="text-right"><?php echo number_format(floatval($e->total_credit ?? 0),2,',','.'); ?></td>
                                                                    <td class="text-center"><?php echo (isset($e->posted) && intval($e->posted) === 1) ? 'Mayorizado' : 'Pendiente'; ?></td>
                                                                    <td style="white-space:nowrap;">
                                                                        <!-- 'Ver' ocultado por solicitud; sólo mostrar Imprimir -->
                                                                        <button type="button" class="btn btn-sm btn-outline-secondary mayor-print-journal" data-id="<?php echo intval($e->id); ?>">Imprimir</button>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <script>
                                                    (function(){
                                                        // Client-side filtering for posted table by date (dd/mm/YYYY in table)
                                                        function parseDMY(dmy) {
                                                            if (!dmy) return null;
                                                            var parts = dmy.split('/'); if (parts.length !== 3) return null;
                                                            return new Date(parts[2], parseInt(parts[1],10)-1, parts[0]);
                                                        }

                                                        var filterBtn = document.getElementById('mayorFilterBtn');
                                                        var resetBtn = document.getElementById('mayorFilterReset');
                                                        var startInput = document.getElementById('mayorFilterStart');
                                                        var endInput = document.getElementById('mayorFilterEnd');
                                                        var searchAsiento = document.getElementById('mayorSearchAsiento');
                                                        var searchDesc = document.getElementById('mayorSearchDesc');
                                                        var table = document.getElementById('mayorTablePosted');
                                                        if (filterBtn && table) {
                                                            filterBtn.addEventListener('click', function(){
                                                                var s = startInput.value ? new Date(startInput.value) : null;
                                                                var e = endInput.value ? new Date(endInput.value) : null;
                                                                // normalize end to end of day
                                                                if (e) e.setHours(23,59,59,999);
                                                                var tbody = table.tBodies[0];
                                                                Array.from(tbody.rows).forEach(function(r){
                                                                    var dateCell = r.cells[0] ? r.cells[0].innerText.trim() : '';
                                                                    var rowDate = parseDMY(dateCell);
                                                                    var show = true;
                                                                    if (s && rowDate && rowDate < s) show = false;
                                                                    if (e && rowDate && rowDate > e) show = false;
                                                                    // Asiento filter (column 1)
                                                                    if (show && searchAsiento && searchAsiento.value.trim() !== '') {
                                                                        var asientoCell = r.cells[1] ? r.cells[1].innerText.trim() : '';
                                                                        var q = searchAsiento.value.trim();
                                                                        if (asientoCell.indexOf(q) === -1) show = false;
                                                                    }
                                                                    // Descripción filter (column 2)
                                                                    if (show && searchDesc && searchDesc.value.trim() !== '') {
                                                                        var descCell = r.cells[2] ? r.cells[2].innerText.trim().toLowerCase() : '';
                                                                        var qd = searchDesc.value.trim().toLowerCase();
                                                                        if (descCell.indexOf(qd) === -1) show = false;
                                                                    }
                                                                    r.style.display = show ? '' : 'none';
                                                                });
                                                            });
                                                        }
                                                        if (resetBtn && table) {
                                                            resetBtn.addEventListener('click', function(){
                                                                startInput.value = '';
                                                                endInput.value = '';
                                                                if (searchAsiento) searchAsiento.value = '';
                                                                if (searchDesc) searchDesc.value = '';
                                                                var tbody = table.tBodies[0];
                                                                Array.from(tbody.rows).forEach(function(r){ r.style.display = ''; });
                                                            });
                                                        }

                                                        // Export the currently filtered posted rows to Excel
                                                        var exportPostedBtn = document.getElementById('mayorExportPosted');
                                                        if (exportPostedBtn) {
                                                            exportPostedBtn.addEventListener('click', function(ev){
                                                                ev.preventDefault();
                                                                var qs = '?';
                                                                if (startInput.value) qs += 'start_date=' + encodeURIComponent(startInput.value) + '&';
                                                                if (endInput.value) qs += 'end_date=' + encodeURIComponent(endInput.value) + '&';
                                                                if (searchAsiento && searchAsiento.value.trim() !== '') qs += 'asiento=' + encodeURIComponent(searchAsiento.value.trim()) + '&';
                                                                if (searchDesc && searchDesc.value.trim() !== '') qs += 'description=' + encodeURIComponent(searchDesc.value.trim()) + '&';
                                                                // remove trailing & or ?
                                                                qs = qs.replace(/[&?]+$/, '');
                                                                window.location = base_url + 'contabilidad/mayor_export_posted' + qs;
                                                            });
                                                        }

                                                        // Ensure print buttons in server-rendered rows still work (delegated)
                                                        document.addEventListener('click', function(ev){
                                                            var printBtn = ev.target.closest && ev.target.closest('.mayor-print-journal');
                                                            if (printBtn) {
                                                                ev.preventDefault();
                                                                var pid = printBtn.getAttribute('data-id') || printBtn.dataset.id;
                                                                if (!pid) return;
                                                                window.open(base_url + 'contabilidad/diario_print?id=' + encodeURIComponent(pid), '_blank');
                                                            }
                                                        });
                                                    })();
                                                </script>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table id="mayorTable" class="table servicont-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Fecha</th>
                                                                <th>Asiento</th>
                                                                <th>Descripción</th>
                                                                <th class="text-right">Debe</th>
                                                                <th class="text-right">Haber</th>
                                                                <th class="text-right">Saldo (Deudor)</th>
                                                                <th class="text-right">Saldo (Acreedor)</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                            <div id="mayorFooter" class="mt-2 small text-muted"></div>
    </footer>
</div>
