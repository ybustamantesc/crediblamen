document.addEventListener('DOMContentLoaded', function(){
    if (typeof base_url === 'undefined') {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const root = parts.length ? ('/' + parts[0] + '/') : '/';
        window.base_url = window.location.origin + root;
    }
    
    const entriesContainer = document.getElementById('diarioContent');
    const modalContainer = document.getElementById('modalContainer');
    
    let currentPage = 1;
    const pageSize = 25;
    let allEntries = []; // Store all entries for client-side pagination

    // Client-side filtering with pagination
    function applyFiltersAndPagination() {
        const rows = document.querySelectorAll('.entry-row');
        allEntries = Array.from(rows);
        
        const filterDocType = document.getElementById('filterDocType');
        const searchInput = document.getElementById('searchAsientoId');
        const noResults = document.getElementById('noResultsMessage');
        
        const selectedType = filterDocType ? filterDocType.value : '';
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        
        // Filter entries
        let filtered = allEntries.filter(row => {
            const rowType = row.getAttribute('data-type') || '';
            const rowCentros = row.getAttribute('data-centro') || ''; // Can contain multiple IDs: "1,2,3"
            const rowDesc = row.getAttribute('data-description') || '';
            const rowId = row.getAttribute('data-id') || '';
            
            const typeMatch = !selectedType || rowType === selectedType;
            
            // Centro de costo filtering removed — always match
            let centroMatch = true;
            
            const searchMatch = !searchTerm || rowDesc.includes(searchTerm) || rowId.includes(searchTerm);
            
            return typeMatch && centroMatch && searchMatch;
        });
        
        const totalRecords = filtered.length;
        const totalPages = Math.ceil(totalRecords / pageSize);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;
        
        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalRecords);
        
        // Hide all rows first
        allEntries.forEach(row => row.style.display = 'none');
        
        // Show only paginated filtered rows
        for (let i = startIdx; i < endIdx; i++) {
            if (filtered[i]) filtered[i].style.display = '';
        }
        
        // Update pagination info
        const pagingInfo = document.getElementById('diarioPagingInfo');
        if (pagingInfo) {
            if (totalRecords === 0) {
                pagingInfo.textContent = 'No hay registros';
                if (noResults) noResults.style.display = 'block';
            } else {
                pagingInfo.textContent = `Mostrando ${startIdx + 1} - ${endIdx} de ${totalRecords} asientos`;
                if (noResults) noResults.style.display = 'none';
            }
        }
        
        // Render pagination buttons
        const pagination = document.getElementById('diarioPagination');
        if (pagination) {
            pagination.innerHTML = '';
            
            if (totalPages === 0) return;
            
            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
            prevLi.innerHTML = '<a class="page-link" href="#" data-page="prev">Anterior</a>';
            pagination.appendChild(prevLi);
            
            // Page numbers
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = 'page-item' + (i === currentPage ? ' active' : '');
                li.innerHTML = '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
                pagination.appendChild(li);
            }
            
            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
            nextLi.innerHTML = '<a class="page-link" href="#" data-page="next">Siguiente</a>';
            pagination.appendChild(nextLi);
            
            // Add click handlers
            pagination.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.getAttribute('data-page');
                    if (page === 'prev' && currentPage > 1) {
                        currentPage--;
                        applyFiltersAndPagination();
                    } else if (page === 'next' && currentPage < totalPages) {
                        currentPage++;
                        applyFiltersAndPagination();
                    } else if (page !== 'prev' && page !== 'next') {
                        currentPage = parseInt(page);
                        applyFiltersAndPagination();
                    }
                });
            });
        }
    }
    
    // Initialize pagination on page load
    setTimeout(applyFiltersAndPagination, 100);
    
    // === BULK SELECTION FUNCTIONALITY ===
    const selectAllCheckbox = document.getElementById('selectAll');
    const btnMassPost = document.getElementById('btnMassPost');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    function updateMassPostButton() {
        const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        
        if (btnMassPost) {
            if (count > 0) {
                btnMassPost.style.display = 'flex';
            } else {
                btnMassPost.style.display = 'none';
            }
        }
        
        // Update selectAll checkbox state
        if (selectAllCheckbox) {
            const allCheckboxes = document.querySelectorAll('.entry-checkbox');
            const allChecked = allCheckboxes.length > 0 && allCheckboxes.length === count;
            selectAllCheckbox.checked = allChecked;
        }
    }
    
    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.entry-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateMassPostButton();
        });
    }
    
    // Individual checkbox change handler (delegated)
    if (entriesContainer) {
        entriesContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('entry-checkbox')) {
                updateMassPostButton();
            }
        });
    }
    
    // Mass Post button click handler
    if (btnMassPost) {
        btnMassPost.addEventListener('click', function(e) {
            e.preventDefault();
            
            const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Seleccione al menos un asiento para mayorizar');
                return;
            }
            
            const entryIds = Array.from(checkedBoxes).map(cb => cb.getAttribute('data-id'));
            
            if (!confirm(`¿Está seguro de mayorizar ${entryIds.length} asiento(s)?`)) {
                return;
            }
            
            // Show loading state
            btnMassPost.disabled = true;
            const originalText = btnMassPost.innerHTML;
            btnMassPost.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mayorizando...';
            
            // Call mass post endpoint
            fetch(base_url + 'contabilidad/mass_post', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ entry_ids: entryIds })
            })
            .then(response => response.json())
            .then(data => {
                btnMassPost.disabled = false;
                btnMassPost.innerHTML = originalText;
                
                if (data.success) {
                    alert(`Mayorización completada:\n✓ ${data.posted} asiento(s) mayorizados exitosamente\n✗ ${data.failed} asiento(s) fallidos`);
                    
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Error al mayorizar: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnMassPost.disabled = false;
                btnMassPost.innerHTML = originalText;
                alert('Error de conexión al mayorizar asientos');
            });
        });
    }
    // === END BULK SELECTION ===
    
    // Attach filter change handlers
    const filterDocType = document.getElementById('filterDocType');
    const searchInput = document.getElementById('searchAsientoId');
    const clearFiltersBtn = document.getElementById('btnClearFilters');
    
    if (filterDocType) {
        filterDocType.addEventListener('change', function() {
            currentPage = 1;
            applyFiltersAndPagination();
        });
    }
    
    // Centro de costo filter disabled
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                applyFiltersAndPagination();
            }, 300);
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (filterDocType) filterDocType.value = '';
            if (searchInput) searchInput.value = '';
            currentPage = 1;
            applyFiltersAndPagination();
        });
    }

    // Export to PDF functionality
    const btnExportPDF = document.getElementById('btnExportPDF');
    if (btnExportPDF) {
        btnExportPDF.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get visible filtered entries
            const visibleRows = Array.from(document.querySelectorAll('.entry-row')).filter(row => {
                return row.style.display !== 'none';
            });
            
            if (visibleRows.length === 0) {
                alert('No hay asientos para exportar');
                return;
            }
            
            // Get company info
            const empresaName = document.getElementById('empresa_razon_social') ? document.getElementById('empresa_razon_social').value : 'Empresa';
            
            // Get filter info
            const filterDocType = document.getElementById('filterDocType');
            const searchInput = document.getElementById('searchAsientoId');
            const selectedType = filterDocType ? filterDocType.value : '';
            const searchTerm = searchInput ? searchInput.value : '';
            
            let filterInfo = '';
            if (selectedType || searchTerm) {
                filterInfo = '<div style="margin-bottom:15px;padding:10px;background:#f0f9ff;border-left:4px solid #0ea5e9;border-radius:4px;font-size:9pt;">';
                filterInfo += '<strong style="color:#0369a1;">Filtros aplicados:</strong> ';
                const filters = [];
                if (selectedType) filters.push('Tipo: ' + selectedType);
                if (searchTerm) filters.push('Búsqueda: "' + searchTerm + '"');
                filterInfo += filters.join(' | ');
                filterInfo += '</div>';
            }
            
            // Collect all entry IDs to fetch details
            const entryIds = visibleRows.map(row => row.getAttribute('data-id'));
            
            // Fetch detailed lines for all entries
            fetch(base_url + 'contabilidad/get_entries_details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ entry_ids: entryIds })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.lines) {
                    alert('Error al obtener detalles de los asientos');
                    return;
                }
                
                // Build HTML
                const w = window.open('', '_blank');
                let html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Libro Diario - ${empresaName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Calibri, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 18mm 14mm;
            color: #1a1a1a;
            font-size: 10pt;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1F4E78;
        }
        .company-name {
            font-size: 18pt;
            font-weight: 700;
            color: #1F4E78;
        }
        .print-date {
            text-align: right;
            font-size: 9pt;
            color: #666;
        }
        .report-title {
            font-size: 20pt;
            font-weight: 700;
            color: #1F4E78;
            margin: 20px 0 15px 0;
            text-align: center;
        }
        .account-section { margin-bottom: 40px; page-break-inside: avoid; }
        .account-info { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #1F4E78; }
        .account-name { font-size: 14pt; font-weight: 600; color: #1F4E78; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        thead { background: linear-gradient(135deg, #1F4E78 0%, #2d5f8d 100%); color: white; }
        th { padding: 12px 8px; text-align: left; font-weight: 600; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
        th.text-right { text-align: right; }
        tbody tr { border-bottom: 1px solid #e0e0e0; }
        tbody tr:nth-child(even) { background-color: #f8f9fa; }
        tbody tr:hover { background-color: #e3f2fd; }
        td { padding: 10px 8px; font-size: 9pt; }
        td.text-right { text-align: right; font-family: 'Courier New', monospace; font-weight: 500; }
        td.opening-balance { font-weight: 700; background-color: #fff3cd; }
        .sig-block { display:flex; gap:30px; margin-top:28px; }
        .sig { flex:1; text-align:center; }
        .sig-line { border-top:1px solid #222; margin:0 20px 6px 20px; height:2px; }
        .sig-label { font-size:10pt; color:#333; margin-top:6px; }
        @media print { body { padding: 10mm; } .account-section { page-break-inside: avoid; } @page { margin: 10mm; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">${empresaName}</div>
        <div class="print-date">
            ${new Date().toLocaleDateString('es-NI', {year: 'numeric', month: 'long', day: 'numeric'})}
        </div>
    </div>
    
    <div class="report-title">LIBRO DIARIO</div>
    ${filterInfo}
    
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tipo Cuenta</th>
                <th style="width: 10%;">Fecha</th>
                <th class="text-center" style="width: 10%;">Asiento ID</th>
                <th style="width: 35%;">Descripción</th>
                <th class="text-right" style="width: 13%;">Débito</th>
                <th class="text-right" style="width: 13%;">Crédito</th>
                <th class="text-right" style="width: 7%;">Saldo</th>
            </tr>
        </thead>
        <tbody>`;
                
                let runningBalance = 0;
                
                // Process each line
                data.lines.forEach(line => {
                    const debit = parseFloat(line.debit) || 0;
                    const credit = parseFloat(line.credit) || 0;
                    runningBalance += debit - credit;
                    
                    const accountType = line.account_type || 'activo';
                    const accountCode = line.account_code || '';
                    const accountName = line.account_name || '';
                    const description = line.description || '';
                    const entryType = line.entry_type || 'CD';
                    const entryId = line.entry_id || '';
                    const date = line.date || '';
                    
                    html += `
            <tr>
                <td>${accountType}</td>
                <td>${date}</td>
                <td class="text-center"><span class="account-code">${entryType}-${entryId}</span></td>
                <td>${accountCode ? accountCode + ' - ' : ''}${accountName}<br><small style="color:#6b7280;">${description}</small></td>
                <td class="text-right">${debit > 0 ? debit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</td>
                <td class="text-right">${credit > 0 ? credit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</td>
                <td class="text-right">${runningBalance.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            </tr>`;
                });
                
                html += `
        </tbody>
    </table>
</table>

    <div class="sig-block">
        <div class="sig">
            <div class="sig-line"></div>
            <div class="sig-label">Contador General</div>
        </div>
        <div class="sig">
            <div class="sig-line"></div>
            <div class="sig-label">Gerente General</div>
        </div>
        <div class="sig">
            <div class="sig-line"></div>
            <div class="sig-label">Administrador</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>
</body>
</html>`;
                
                w.document.open();
                w.document.write(html);
                w.document.close();
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Error al generar el PDF');
            });
        });
    }

    // Handler for btnNewAsiento that exists in static HTML
    const btnNew = document.getElementById('btnNewAsiento');
    if (btnNew) {
        btnNew.addEventListener('click', function(){
            fetch(base_url+'contabilidad/modal_add').then(r=>r.text()).then(html=>{
                modalContainer.innerHTML = html;
                if (typeof attachModalEvents === 'function') attachModalEvents();
            }).catch(err=>{ console.error('Error loading modal:', err); alert('Error al abrir el modal'); });
        });
    }

    // Delegated handler for modal actions inserted via innerHTML (scripts inside innerHTML don't execute)
    if (modalContainer) {
        modalContainer.addEventListener('click', function(e){
            var btnClose = e.target.closest ? e.target.closest('#btnCloseView') : null;
            if (btnClose) {
                modalContainer.innerHTML = '';
                try{ if (typeof fetchEntries === 'function') fetchEntries(); } catch(err){}
                return;
            }
            // backdrop click: the modal wrapper has id 'modalViewEntry'
            if (e.target && e.target.id === 'modalViewEntry'){
                modalContainer.innerHTML = '';
                try{ if (typeof fetchEntries === 'function') fetchEntries(); } catch(err){}
                return;
            }
        });
    }

    // Inject high-specificity CSS to protect action buttons from being hidden by other styles/scripts
    (function protectActionButtons(){
        try{
            const css = `
                #diarioContent .cc-btn { position: relative !important; z-index: 99999 !important; display: inline-block !important; opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; }
                #diarioContent .diario-wrapper, #diarioContent .table-diary { overflow: visible !important; }
                #diarioContent .col-actions, #diarioContent td { overflow: visible !important; }
            `;
            const s = document.createElement('style'); s.type='text/css'; s.appendChild(document.createTextNode(css));
            (document.head || document.getElementsByTagName('head')[0]).appendChild(s);
        }catch(e){ console.warn('contabilidad_diario: could not inject protection CSS', e); }
    })();

    // Additional styles for voided rows and disabled actions
    (function addVoidedStyles(){
        try{
            const css = `
                .diario-row-voided { background:#fff7f7 !important; color:#555; }
                .diario-row-voided td { opacity:0.85; }
                .diario-row-voided .badge-danger { background:#d9534f; color:#fff; padding:6px 8px; border-radius:4px; }
                .diario-row-voided .cc-btn-void, .diario-row-voided .cc-btn-edit { display:none !important; }
            `;
            const s = document.createElement('style'); s.type='text/css'; s.appendChild(document.createTextNode(css));
            (document.head || document.getElementsByTagName('head')[0]).appendChild(s);
        }catch(e){ }
    })();

    function renderFilterBar(){
        if (document.getElementById('cont-diario-filter')) return;
        const bar = document.createElement('div');
        bar.id = 'cont-diario-filter';
        bar.style.display = 'flex'; bar.style.gap = '8px'; bar.style.marginBottom = '8px';
        bar.innerHTML = `
            <div><label>Desde</label><input id="filterStart" type="date" style="padding:6px" /></div>
            <div><label>Hasta</label><input id="filterEnd" type="date" style="padding:6px" /></div>
            <div style="align-self:end"><button id="btnFilter" class="btn">Filtrar</button></div>
            <div style="align-self:end;margin-left:auto"><button id="btnExport" class="btn btn-outline-secondary">Exportar CSV</button></div>
        `;
        entriesContainer.parentNode.insertBefore(bar, entriesContainer);
        document.getElementById('btnFilter').addEventListener('click', fetchEntries);
        document.getElementById('btnExport').addEventListener('click', function(){
            const s = document.getElementById('filterStart').value;
            const e = document.getElementById('filterEnd').value;
            let url = base_url + 'contabilidad/export_csv';
            if (s || e) url += '?'+ new URLSearchParams({start_date: s, end_date: e}).toString();
            window.location = url;
        });
    }

    function buildTableHtml(data){
        if (!data || !data.length) {
            return `<div style="padding:40px;text-align:center;color:#6b7280;background:#f9fafb;border-radius:8px;">
                <i class="fas fa-book-open" style="font-size:48px;color:#d1d5db;margin-bottom:16px;"></i>
                <div style="font-size:18px;font-weight:600;margin-bottom:8px;">No hay asientos registrados</div>
                <div style="font-size:14px;">Haz clic en "Nuevo Asiento" para crear el primero</div>
            </div>`;
        }
        
        const typeColors = {
            'CD': '#667eea', 'CI': '#10b981', 'CE': '#ef4444', 'CT': '#f59e0b',
            'CA': '#8b5cf6', 'CN': '#06b6d4', 'CAP': '#14b8a6', 'CCIER': '#ec4899',
            'CDEP': '#f97316', 'CPROV': '#6366f1'
        };
        
        let html = `<div class="diario-wrapper" style="overflow:auto;position:relative;">
            <table class="table-diary" style="width:100%;border-collapse:separate;border-spacing:0;min-width:760px;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background:linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Código</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Tipo</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Fecha</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;">Descripción / Montos</th>
                        <th class="col-actions" style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;text-align:center;white-space:nowrap;">Acciones</th>
                    </tr>
                </thead>
                <tbody>`;
        
        data.forEach(d => {
            const entryType = d.entry_type || 'CD';
            const color = typeColors[entryType] || '#6b7280';
            const isVoided = d.voided && parseInt(d.voided) === 1;
            const isPosted = d.posted && parseInt(d.posted) === 1;
            const safeDesc = (d.description || '').toString().replace(/</g,'&lt;').replace(/>/g,'&gt;');
            const dateFormatted = d.date ? new Date(d.date).toLocaleDateString('es-NI', {day:'2-digit', month:'2-digit', year:'numeric'}) : '';
            
            html += `<tr class="entry-row" data-id="${d.id}" data-type="${entryType}" data-description="${safeDesc.toLowerCase()}" data-posted="${isPosted ? '1' : '0'}" data-voided="${isVoided ? '1' : '0'}" style="border-bottom:1px solid #f3f4f6;transition:all 0.2s;${isVoided ? 'opacity:0.5;' : ''}" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                <td style="padding:14px 12px;font-weight:700;color:${color};font-size:15px;">
                    ${entryType}-${d.id}
                    ${isPosted ? '<i class="fas fa-lock" style="color:#10b981;font-size:11px;margin-left:6px;" title="Mayorizado"></i>' : ''}
                </td>
                <td style="padding:14px 12px;">
                    <span style="background:${color};color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;">
                        ${entryType}
                    </span>
                    ${isPosted ? '<span style="background:#10b981;color:#fff;padding:4px 8px;border-radius:12px;font-size:10px;font-weight:600;margin-left:4px;white-space:nowrap;">✓ MAYOR</span>' : ''}
                </td>
                <td style="padding:14px 12px;color:#6b7280;font-size:14px;white-space:nowrap;">
                    ${dateFormatted}
                </td>
                <td style="padding:14px 12px;">
                    <div style="font-weight:500;color:#1f2937;font-size:14px;margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        ${safeDesc}
                        ${isVoided ? '<span style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;margin-left:8px;">ANULADO</span>' : ''}
                    </div>
                    <div style="display:flex;gap:16px;font-size:13px;">
                        <span style="color:#ef4444;font-weight:600;">
                            <i class="fas fa-arrow-up" style="font-size:10px;margin-right:4px;"></i>Debe: C$${parseFloat(d.total_debit).toFixed(2)}
                        </span>
                        <span style="color:#10b981;font-weight:600;">
                            <i class="fas fa-arrow-down" style="font-size:10px;margin-right:4px;"></i>Haber: C$${parseFloat(d.total_credit).toFixed(2)}
                        </span>
                    </div>
                </td>
                <td style="padding:14px 12px;text-align:center;white-space:nowrap;">
                    <button class="cc-btn cc-btn-view btn btn-sm btn-primary" data-id="${d.id}">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    ${(!isVoided && !isPosted) ? `
                        <button class="cc-btn cc-btn-edit btn btn-sm btn-warning" data-id="${d.id}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="cc-btn cc-btn-void btn btn-sm btn-danger" data-id="${d.id}">
                            <i class="fas fa-ban"></i> Anular
                        </button>
                        <button class="cc-btn cc-btn-post btn btn-sm btn-success" data-id="${d.id}">
                            <i class="fas fa-check-double"></i> Mayorizar
                        </button>
                    ` : ''}
                    ${(isPosted && !isVoided) ? `
                        <button class="cc-btn cc-btn-unpost btn btn-sm btn-info" data-id="${d.id}">
                            <i class="fas fa-unlock"></i> Desmayorizar
                        </button>
                    ` : ''}
                </td>
            </tr>`;
        });
        
        html += `</tbody></table></div>
        <div id="noResultsMessage" style="display:none;padding:40px;text-align:center;color:#6b7280;">
            <i class="fas fa-search" style="font-size:48px;color:#d1d5db;margin-bottom:16px;"></i>
            <div style="font-size:18px;font-weight:600;margin-bottom:8px;">No se encontraron asientos</div>
            <div style="font-size:14px;">Intenta con otros criterios de búsqueda</div>
        </div>`;
        
        return html;
    }

    function fetchEntries(){
        const s = document.getElementById('filterStart') ? document.getElementById('filterStart').value : '';
        const e = document.getElementById('filterEnd') ? document.getElementById('filterEnd').value : '';
        let url = base_url+'contabilidad/list_entries';
        if (s || e) url += '?' + new URLSearchParams({start_date: s, end_date: e}).toString();
        entriesContainer.innerHTML = '<div style="padding:12px;color:#666;text-align:center;">Cargando...</div>';
        fetch(url).then(r=>r.json()).then(json=>{
            const data = json.data || [];
            entriesContainer.innerHTML = buildTableHtml(data);
        }).catch(err=>{
            console.error('contabilidad: fetchEntries error', err);
            entriesContainer.innerHTML = '<div style="padding:12px;color:#a00;text-align:center;">Error cargando</div>';
        });
    }

    // single delegated handler for buttons inside diarioContent
    entriesContainer.addEventListener('click', function(evt){
        const btn = evt.target.closest('button');
        if (!btn) return;
        // Nuevo Asiento
        if (btn.id === 'btnNewAsiento'){
            fetch(base_url+'contabilidad/modal_add').then(r=>r.text()).then(html=>{
                modalContainer.innerHTML = html;
                if (typeof attachModalEvents === 'function') attachModalEvents();
            });
            return;
        }
        // View
        if (btn.classList.contains('cc-btn-view')){
            const id = btn.getAttribute('data-id');
            fetch(base_url+'contabilidad/modal_view?id='+encodeURIComponent(id)).then(r=>r.text()).then(html=>{ 
                modalContainer.innerHTML = html;
            });
            return;
        }
        // Void (Anular)
        if (btn.classList.contains('cc-btn-void')){
            const id = btn.getAttribute('data-id');
            const row = btn.closest('.entry-row');
            const isPosted = row && row.getAttribute('data-posted') === '1';
            
            if (isPosted) {
                alert('No se puede anular un asiento mayorizado. Primero debe desmayorizarlo.');
                return;
            }
            
            if (!confirm('¿Anular asiento #'+id+'? Esta acción marcará el asiento como anulado.')) return;
            fetch(base_url+'contabilidad/reverse_entry', { method:'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
            .then(r=> r.text().then(text => ({ ok: r.ok, status: r.status, text })) )
            .then(obj => {
                let parsed = null;
                try { parsed = JSON.parse(obj.text); } catch(e) {
                    console.error('reverse_entry: server returned non-JSON (status '+obj.status+')', obj.text);
                    alert('Error del servidor al anular (revisa la consola)');
                    return;
                }
                if (parsed && parsed.status === 'success') { 
                    alert('Asiento anulado exitosamente'); 
                    window.location.reload();
                } else {
                    console.error('reverse_entry: server error response', parsed);
                    alert('Error al anular: ' + (parsed.error || (parsed.errors && parsed.errors.join('\n')) || 'respuesta desconocida'));
                }
            }).catch(err=>{ console.error('reverse_entry fetch failed', err); alert('Error de red'); });
            return;
        }
        
        // Post (Mayorizar)
        if (btn.classList.contains('cc-btn-post')){
            const id = btn.getAttribute('data-id');
            if (!confirm('¿Mayorizar asiento #'+id+'?\n\nUna vez mayorizado, el asiento quedará bloqueado y no podrá ser editado o anulado hasta que sea desmayorizado.')) return;
            
            fetch(base_url+'contabilidad/post_entry', { method:'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
            .then(r=>r.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Asiento mayorizado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error al mayorizar: ' + (data.error || 'Error desconocido'));
                }
            }).catch(err=>{ console.error('post_entry error', err); alert('Error de red'); });
            return;
        }
        
        // Unpost (Desmayorizar)
        if (btn.classList.contains('cc-btn-unpost')){
            const id = btn.getAttribute('data-id');
            if (!confirm('¿Desmayorizar asiento #'+id+'?\n\nEsto permitirá editar o anular el asiento nuevamente.')) return;

            // Pedir contraseña de administrador antes de desmayorizar
            const adminPass = prompt('Ingrese la contraseña de administrador para confirmar:');
            if (adminPass === null) return; // usuario canceló

            const body = 'id='+encodeURIComponent(id)+'&admin_pass='+encodeURIComponent(adminPass);

            fetch(base_url+'contabilidad/unpost_entry', { method:'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: body })
            .then(r=>r.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Asiento desmayorizado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error al desmayorizar: ' + (data.error || 'Error desconocido'));
                }
            }).catch(err=>{ console.error('unpost_entry error', err); alert('Error de red'); });
            return;
        }
        // Edit
        if (btn.classList.contains('cc-btn-edit')){
            const id = btn.getAttribute('data-id');
            const row = btn.closest('.entry-row');
            const isPosted = row && row.getAttribute('data-posted') === '1';
            
            if (isPosted) {
                alert('No se puede editar un asiento mayorizado. Primero debe desmayorizarlo.');
                return;
            }
            
            // Cargar el modal de agregar
            fetch(base_url+'contabilidad/modal_add').then(r=>r.text()).then(html=>{
                modalContainer.innerHTML = html;
                
                // Esperar a que el modal se cargue y attachModalEvents se ejecute
                setTimeout(function(){
                    if (typeof attachModalEvents === 'function') {
                        attachModalEvents();
                    }
                    
                    // Cargar los datos del asiento
                    fetch(base_url+'contabilidad/journal_detail?id='+encodeURIComponent(id))
                        .then(r=>r.json())
                        .then(jresp=>{
                            if (jresp.status !== 'success') { 
                                alert('No se pudo cargar el asiento'); 
                                return; 
                            }
                            
                            const data = jresp.data;
                            
                            // Cambiar título del modal
                            const modalTitle = document.querySelector('#modalAddEntry h2');
                            if (modalTitle) {
                                modalTitle.textContent = 'Editar Asiento #' + id;
                            }
                            
                            // Llenar campos básicos
                            const form = document.getElementById('formNewEntry');
                            if (!form) return;
                            
                            const docTypeSelect = form.querySelector('select[name="document_type"]');
                            const dateInput = form.querySelector('input[name="date"]');
                            const descInput = form.querySelector('input[name="description"]');
                            
                            if (docTypeSelect && data.header.entry_type) {
                                docTypeSelect.value = data.header.entry_type;
                            }
                            if (dateInput && data.header.date) {
                                dateInput.value = data.header.date.split(' ')[0];
                            }
                            if (descInput && data.header.description) {
                                descInput.value = data.header.description;
                            }
                            
                            // Agregar campo oculto con el ID para actualización
                            const hiddenId = document.createElement('input');
                            hiddenId.type = 'hidden';
                            hiddenId.name = 'id';
                            hiddenId.value = id;
                            form.appendChild(hiddenId);
                            
                            // Limpiar líneas existentes y agregar las del asiento
                            const wrapper = document.getElementById('linesWrapper');
                            if (!wrapper) return;
                            
                            // Remover todas las líneas excepto los headers
                            const existingLines = wrapper.querySelectorAll('.entry-line');
                            existingLines.forEach(line => line.remove());
                            
                            // Función recursiva para agregar líneas secuencialmente
                            function addLineSequentially(lines, index) {
                                if (index >= lines.length) return;
                                
                                const ln = lines[index];
                                
                                // Agregar nueva línea
                                if (typeof window.addEntryLine === 'function') {
                                    window.addEntryLine();
                                }
                                
                                // Esperar y procesar la línea recién agregada
                                setTimeout(() => {
                                    const allLines = wrapper.querySelectorAll('.entry-line');
                                    const currentLine = allLines[allLines.length - 1];
                                    
                                    if (currentLine) {
                                        // Buscar el select de cuenta y establecer valor
                                        const accountSelect = currentLine.querySelector('.account-select');
                                        if (accountSelect) {
                                            // Si es Select2, crear la opción y seleccionarla
                                            if (typeof $.fn.select2 !== 'undefined' && $(accountSelect).data('select2')) {
                                                const option = new Option(ln.code + ' - ' + ln.name, ln.account_id, true, true);
                                                $(accountSelect).append(option).trigger('change');
                                            } else {
                                                // Fallback: agregar option normal
                                                const option = document.createElement('option');
                                                option.value = ln.account_id;
                                                option.text = ln.code + ' - ' + ln.name;
                                                option.selected = true;
                                                accountSelect.appendChild(option);
                                            }
                                        }
                                        
                                        // Establecer montos (solo NIO, USD se calculará automáticamente)
                                        const debitInput = currentLine.querySelector('.line-debit-mxn');
                                        const creditInput = currentLine.querySelector('.line-credit-mxn');
                                        const detailInput = currentLine.querySelector('input[name*="[description]"]');
                                        
                                        if (debitInput && ln.debit > 0) {
                                            debitInput.value = parseFloat(ln.debit).toFixed(2);
                                            debitInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        }
                                        if (creditInput && ln.credit > 0) {
                                            creditInput.value = parseFloat(ln.credit).toFixed(2);
                                            creditInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        }
                                        if (detailInput && ln.line_description) {
                                            detailInput.value = ln.line_description;
                                        }
                                    }
                                    
                                    // Procesar la siguiente línea
                                    addLineSequentially(lines, index + 1);
                                }, 200);
                            }
                            
                            // Iniciar la carga secuencial de líneas
                            addLineSequentially(data.lines, 0);
                            
                        })
                        .catch(err=>{ 
                            console.error('Error cargando asiento:', err); 
                            alert('Error al cargar los datos del asiento'); 
                        });
                }, 300);
            }).catch(err=>{ 
                console.error('Error cargando modal:', err); 
                alert('Error al abrir el modal de edición'); 
            });
            return;
        }
    });

    // expose for external refresh
    window.refreshContabilidadEntries = fetchEntries;

    // Filter and search functionality - Must be called after table loads
    function initFilters(){
        const filterDocType = document.getElementById('filterDocType');
        const searchInput = document.getElementById('searchAsientoId');
        const btnClear = document.getElementById('btnClearFilters');
        const noResults = document.getElementById('noResultsMessage');

        if (!filterDocType || !searchInput || !btnClear) {
            console.warn('Filter elements not found');
            return;
        }

        // Remove existing listeners (if any)
        const newFilterDocType = filterDocType.cloneNode(true);
        filterDocType.parentNode.replaceChild(newFilterDocType, filterDocType);
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        const newBtnClear = btnClear.cloneNode(true);
        btnClear.parentNode.replaceChild(newBtnClear, btnClear);

        function applyFilters(){
            const selectedType = newFilterDocType.value;
            const searchTerm = newSearchInput.value.trim().toLowerCase();
            
            const rows = document.querySelectorAll('.entry-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const rowType = row.getAttribute('data-type') || '';
                const rowId = row.getAttribute('data-id') || '';
                const rowDesc = row.getAttribute('data-description') || '';
                
                let typeMatch = !selectedType || selectedType === '' || rowType === selectedType;
                let searchMatch = !searchTerm || rowId.includes(searchTerm) || rowDesc.includes(searchTerm);
                
                if (typeMatch && searchMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const noResultsEl = document.getElementById('noResultsMessage');
            if (noResultsEl) {
                noResultsEl.style.display = (visibleCount === 0 && rows.length > 0) ? 'block' : 'none';
            }
        }

        newFilterDocType.addEventListener('change', applyFilters);
        newSearchInput.addEventListener('input', applyFilters);
        newBtnClear.addEventListener('click', function(){
            newFilterDocType.value = '';
            newSearchInput.value = '';
            applyFilters();
        });
    }
    
    // Initialize filters on page load
    let filtersInitialized = false;
    setTimeout(function() {
        if (!filtersInitialized) {
            initFilters();
            filtersInitialized = true;
        }
    }, 500);
    
    // Reinitialize only when table content actually changes (via AJAX)
    if (window.MutationObserver && entriesContainer) {
        let timeoutId = null;
        const observer = new MutationObserver(function(mutations) {
            // Debounce: only reinit once after all mutations settle
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                const hasTable = entriesContainer.querySelector('.entry-row');
                if (hasTable && filtersInitialized) {
                    // Table was rebuilt, reinitialize filters
                    initFilters();
                }
            }, 200);
        });
        
        observer.observe(entriesContainer, { childList: true });
    }

    // Diagnostic observer disabled - only enable for debugging button visibility issues
    // Uncomment the following code if you need to debug button visibility:
    /*
    (function attachDiagnosticObserver(){
        const log = (...args) => console.warn('[cont-diario-diagn]', ...args);
        const target = entriesContainer;
        if (!target) return;
        const mo = new MutationObserver((mutations)=>{
            for (const m of mutations){
                if (m.type === 'childList'){
                    m.removedNodes.forEach(n=>{
                        if (n.nodeType===1){
                            const btns = n.querySelectorAll && n.querySelectorAll('.cc-btn');
                            if (btns && btns.length) log('NODES REMOVED CONTAINING ACTION BUTTONS', btns.length, n);
                        }
                    });
                    m.addedNodes.forEach(n=>{
                        if (n.nodeType===1){
                            const btn = n.querySelector && n.querySelector('.cc-btn');
                            if (btn) log('NODES ADDED: action button present');
                        }
                    });
                }
            }
        });
        mo.observe(target, { childList: true, subtree: true });
    })();
    */

    // init - NOTA: No llamamos fetchEntries() porque la página ya carga con datos de PHP
    // Solo se usará fetchEntries() si el usuario aplica filtros de fecha
    renderFilterBar();
});
