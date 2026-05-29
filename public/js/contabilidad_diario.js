document.addEventListener('DOMContentLoaded', function(){
    if (typeof base_url === 'undefined') {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const root = parts.length ? ('/' + parts[0] + '/') : '/';
        window.base_url = window.location.origin + root;
    }
    
    const entriesContainer = document.getElementById('diarioContent');
    const modalContainer = document.getElementById('modalContainer');
    
    document.addEventListener('click', function(event) {
        const clickedActionsMenu = event.target.closest('.actions-menu');
        const openMenus = document.querySelectorAll('.actions-menu[open]');
        openMenus.forEach(menu => {
            if (menu !== clickedActionsMenu) {
                menu.removeAttribute('open');
            }
        });
    });
    
    let currentPage = 1;
    const pageSize = 25;
    let allEntries = []; // Store all entries for client-side pagination
    let filteredEntries = []; // Store currently filtered rows

    // Client-side filtering with pagination
    function applyFiltersAndPagination() {
        const rows = document.querySelectorAll('.entry-row');
        allEntries = Array.from(rows);
        
        const filterDocType = document.getElementById('filterDocType');
        const searchInput = document.getElementById('searchAsientoId');
        const filterStart = document.getElementById('filterStart');
        const filterEnd = document.getElementById('filterEnd');
        const noResults = document.getElementById('noResultsMessage');
        
        const selectedType = filterDocType ? filterDocType.value : '';
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const startDate = filterStart ? filterStart.value : '';
        const endDate = filterEnd ? filterEnd.value : '';
        
        // Filter entries
        let filtered = allEntries.filter(row => {
            const rowType = row.getAttribute('data-type') || '';
            const rowCentros = row.getAttribute('data-centro') || ''; // Can contain multiple IDs: "1,2,3"
            const rowDesc = row.getAttribute('data-description') || '';
            const rowId = row.getAttribute('data-id') || '';
            const rowDate = row.getAttribute('data-date') || '';
            
            const typeMatch = !selectedType || rowType === selectedType;
            
            // Centro de costo filtering removed — always match
            let centroMatch = true;
            
            const searchMatch = !searchTerm || rowDesc.includes(searchTerm) || rowId.includes(searchTerm);
            const startMatch = !startDate || (rowDate && rowDate >= startDate);
            const endMatch = !endDate || (rowDate && rowDate <= endDate);
            
            return typeMatch && centroMatch && searchMatch && startMatch && endMatch;
        });
        
        filteredEntries = filtered;
        const filteredIds = new Set(filteredEntries.map(row => row.getAttribute('data-id')));
        const totalRecords = filtered.length;
        const totalPages = Math.ceil(totalRecords / pageSize);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;
        
        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalRecords);
        
        // Hide all rows first
        allEntries.forEach(row => {
            row.style.display = 'none';
            const checkbox = row.querySelector('.entry-checkbox');
            if (checkbox && !filteredIds.has(row.getAttribute('data-id'))) {
                checkbox.checked = false;
            }
        });
        
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
            
            if (totalPages === 0) {
                updateMassPostButton();
                return;
            }
            
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
        updateMassPostButton();
    }
    
    // Initialize pagination on page load
    setTimeout(applyFiltersAndPagination, 100);
    
    // === BULK SELECTION FUNCTIONALITY ===
    const selectAllCheckbox = document.getElementById('selectAll');
    const btnMassPost = document.getElementById('btnMassPost');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    function updateMassPostButton() {
        const filteredCheckboxes = filteredEntries
            .map(row => row.querySelector('.entry-checkbox'))
            .filter(cb => cb instanceof HTMLInputElement);
        const checkedBoxes = filteredCheckboxes.filter(cb => cb.checked);
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
            const allChecked = filteredCheckboxes.length > 0 && filteredCheckboxes.every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }
    }
    
    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = filteredEntries
                .map(row => row.querySelector('.entry-checkbox'))
                .filter(cb => cb instanceof HTMLInputElement);
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
            const filterStart = document.getElementById('filterStart');
            const filterEnd = document.getElementById('filterEnd');
            if (filterStart) filterStart.value = '';
            if (filterEnd) filterEnd.value = '';

            document.querySelectorAll('.entry-checkbox:checked').forEach(checkbox => {
                checkbox.checked = false;
            });

            currentPage = 1;
            applyFiltersAndPagination();
            updateMassPostButton();
        });
    }

    // Export to PDF functionality
    const btnExportPDF = document.getElementById('btnExportPDF');
    if (btnExportPDF) {
        btnExportPDF.addEventListener('click', function(e) {
            e.preventDefault();

            const checkedBoxes = Array.from(document.querySelectorAll('.entry-checkbox:checked'));
            if (checkedBoxes.length === 0) {
                alert('Seleccione al menos un asiento para exportar');
                return;
            }

            const entryIds = [...new Set(checkedBoxes.map(cb => cb.getAttribute('data-id')))].filter(Boolean);
            if (entryIds.length === 0) {
                alert('No hay asientos seleccionados para exportar');
                return;
            }

            const filterDocType = document.getElementById('filterDocType');
            const searchInput = document.getElementById('searchAsientoId');
            const selectedType = filterDocType ? filterDocType.value : '';
            const searchTerm = searchInput ? searchInput.value.trim() : '';
            
            const payload = {
                entry_ids: entryIds,
                filter_type: selectedType,
                search_term: searchTerm
            };

            btnExportPDF.disabled = true;
            const originalText = btnExportPDF.innerHTML;
            btnExportPDF.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';

            fetch(base_url + 'contabilidad/export_selected_entries_pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text || 'Error al generar el PDF');
                    });
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const now = new Date();
                a.download = 'libro_diario_' + now.toISOString().slice(0,10) + '.pdf';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                    a.remove();
                }, 1000);
            })
            .catch(err => {
                console.error('Error:', err);
                alert(err.message || 'Error al generar el PDF');
            })
            .finally(() => {
                btnExportPDF.disabled = false;
                btnExportPDF.innerHTML = originalText;
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
                return;
            }
            // backdrop click: the modal wrapper has id 'modalViewEntry'
            if (e.target && e.target.id === 'modalViewEntry'){
                modalContainer.innerHTML = '';
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
        `;
        entriesContainer.parentNode.insertBefore(bar, entriesContainer);
        // Button click is handled after filters initialize; do not reload the full table HTML.
        document.getElementById('btnFilter').addEventListener('click', function(e){
            e.preventDefault();
            currentPage = 1;
            applyFiltersAndPagination();
        });

        const filterStart = document.getElementById('filterStart');
        const filterEnd = document.getElementById('filterEnd');
        if (filterStart) {
            filterStart.addEventListener('change', function() {
                currentPage = 1;
                applyFiltersAndPagination();
            });
        }
        if (filterEnd) {
            filterEnd.addEventListener('change', function() {
                currentPage = 1;
                applyFiltersAndPagination();
            });
        }
        
        const btnExportCSV = document.getElementById('btnExportCSV');
        if (btnExportCSV) {
            btnExportCSV.addEventListener('click', function(e) {
                e.preventDefault();

                const checkedBoxes = Array.from(document.querySelectorAll('.entry-checkbox:checked'));
                if (checkedBoxes.length === 0) {
                    alert('Seleccione al menos un asiento para exportar');
                    return;
                }

                const entryIds = [...new Set(checkedBoxes.map(cb => cb.getAttribute('data-id')))].filter(Boolean);
                if (entryIds.length === 0) {
                    alert('No hay asientos seleccionados para exportar');
                    return;
                }

                btnExportCSV.disabled = true;
                const originalText = btnExportCSV.innerHTML;
                btnExportCSV.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando CSV...';

                fetch(base_url + 'contabilidad/export_selected_entries_csv', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ entry_ids: entryIds })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'Error al generar el CSV');
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    const now = new Date();
                    a.download = 'libro_diario_' + now.toISOString().slice(0,10) + '.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(() => {
                        window.URL.revokeObjectURL(url);
                        a.remove();
                    }, 1000);
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert(err.message || 'Error al generar el CSV');
                })
                .finally(() => {
                    btnExportCSV.disabled = false;
                    btnExportCSV.innerHTML = originalText;
                });
            });
        }
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
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;width:50px;"><input type="checkbox" id="selectAll" style="width:18px;height:18px;cursor:pointer;" /></th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Estado</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Código</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Tipo</th>
                        <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;border-bottom:2px solid #d1d5db;white-space:nowrap;">Centro Costo</th>
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
            const centroName = (d.centro_costo_nombres && d.centro_costo_nombres.trim()) ? d.centro_costo_nombres : '-';
            const safeDesc = (d.description || '').toString().replace(/</g,'&lt;').replace(/>/g,'&gt;');
            const dateFormatted = d.date ? new Date(d.date).toLocaleDateString('es-NI', {day:'2-digit', month:'2-digit', year:'numeric'}) : '';
            
            html += `<tr class="entry-row" data-id="${d.id}" data-type="${entryType}" data-centro="${d.centro_costo_ids || ''}" data-description="${safeDesc.toLowerCase()}" data-posted="${isPosted ? '1' : '0'}" data-voided="${isVoided ? '1' : '0'}" style="border-bottom:1px solid #f3f4f6;transition:all 0.2s;${isVoided ? 'opacity:0.5;' : ''}" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                <td style="padding:14px 12px;text-align:center;">
                    ${(!isVoided && !isPosted) ? `<input type="checkbox" class="entry-checkbox" data-id="${d.id}" style="width:18px;height:18px;cursor:pointer;" />` : ''}
                </td>
                <td style="padding:14px 12px;text-align:center;">
                    ${isVoided ? `<span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;"><i class="fas fa-ban" style="color:#ef4444;"></i> ANULADO</span>` : isPosted ? `<span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;"><i class="fas fa-check-double" style="color:#10b981;"></i> MAYORIZADO</span>` : `<span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;white-space:nowrap;border:2px solid #475569;animation:pulse 2s infinite;"><i class="fas fa-clock" style="color:#f59e0b;"></i> PENDIENTE</span>`}
                </td>
                <td style="padding:14px 12px;font-weight:700;color:${color};font-size:15px;">
                    ${entryType}-${d.id}
                </td>
                <td style="padding:14px 12px;">
                    <span style="background:#64748b;color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;border:2px solid #475569;">
                        ${entryType}
                    </span>
                </td>
                <td style="padding:14px 12px;color:#6b7280;font-size:13px;white-space:nowrap;">
                    <span style="display:inline-block;background:#f3f4f6;color:#4b5563;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;">
                        ${centroName}
                    </span>
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
                <td style="padding:14px 12px;text-align:center;white-space:nowrap;position:relative;z-index:1;">
                    <button class="cc-btn cc-btn-view btn btn-sm" data-id="${d.id}" style="position:relative;z-index:1;background:#475569;color:white;border:none;font-weight:500;">
                        <i class="fas fa-eye" style="color:#3b82f6;"></i> Ver
                    </button>
                    ${(!isVoided && !isPosted) ? `
                        <button class="cc-btn cc-btn-edit btn btn-sm" data-id="${d.id}" style="position:relative;z-index:1;background:#475569;color:white;border:none;font-weight:500;">
                            <i class="fas fa-edit" style="color:#f59e0b;"></i> Editar
                        </button>
                        <button class="cc-btn cc-btn-void btn btn-sm" data-id="${d.id}" style="position:relative;z-index:1;background:#475569;color:white;border:none;font-weight:500;">
                            <i class="fas fa-ban" style="color:#ef4444;"></i> Anular
                        </button>
                        <button class="cc-btn cc-btn-post btn btn-sm" data-id="${d.id}" style="position:relative;z-index:1;background:#475569;color:white;border:none;font-weight:500;">
                            <i class="fas fa-check-double" style="color:#10b981;"></i> Mayorizar
                        </button>
                    ` : ''}
                    ${(isPosted && !isVoided) ? `
                        <button class="cc-btn cc-btn-unpost btn btn-sm" data-id="${d.id}" style="position:relative;z-index:1;background:#475569;color:white;border:none;font-weight:500;">
                            <i class="fas fa-unlock" style="color:#06b6d4;"></i> Desmayorizar
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
                            
                            const docTypeSelect = form.querySelector('select[name="document_type"], select[name="entry_type"]');
                            const dateInput = form.querySelector('input[name="date"]');
                            const descInput = form.querySelector('textarea[name="description"], input[name="description"]');

                            // Helper to set value and trigger change/input events (supports Select2)
                            function setSelectValue(selectEl, val) {
                                if (!selectEl) return;
                                try {
                                    if (typeof $ !== 'undefined' && $.fn.select2 && $(selectEl).data('select2')) {
                                        $(selectEl).val(val).trigger('change');
                                    } else {
                                        selectEl.value = val;
                                        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } catch (e) { try { selectEl.value = val; } catch(_){} }
                            }

                            const documentTypeValue = data.header.document_type || data.header.entry_type || '';
                            if (docTypeSelect && documentTypeValue) {
                                setSelectValue(docTypeSelect, documentTypeValue);
                            }
                            if (dateInput && data.header.date) {
                                dateInput.value = data.header.date.split(' ')[0];
                                dateInput.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            if (descInput && data.header.description) {
                                descInput.value = data.header.description;
                                descInput.dispatchEvent(new Event('input', { bubbles: true }));
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
                            
                            // Preserve centro_costo options from the template before removing lines
                            const firstCentro = wrapper.querySelector('.line-centro-costo');
                            if (firstCentro) {
                                window.centroCostoOptionsHtml = firstCentro.innerHTML || '';
                            }

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
                                            // Establecer centro de costo si viene en la línea
                                            const centroSelect = currentLine.querySelector('.line-centro-costo');
                                            if (centroSelect) {
                                                const centroId = ln.centro_costo_id || ln.centro_costo_id === 0 ? ln.centro_costo_id : (ln.centro_costo_id || '');
                                                // If options missing but we preserved template, fill them
                                                if (centroSelect.options.length <= 1 && window.centroCostoOptionsHtml) {
                                                    centroSelect.innerHTML = window.centroCostoOptionsHtml;
                                                }
                                                if (centroId) {
                                                    // Try to set value; if option not present, add it
                                                    try {
                                                        centroSelect.value = centroId;
                                                        centroSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                                    } catch(e) {
                                                        // create option using available centro info
                                                        const opt = document.createElement('option');
                                                        opt.value = centroId;
                                                        const txt = (ln.centro_costo_codigo ? ln.centro_costo_codigo + ' - ' : '') + (ln.centro_costo_nombre || '');
                                                        opt.text = txt || centroId;
                                                        opt.selected = true;
                                                        centroSelect.appendChild(opt);
                                                        centroSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                                    }
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
        const filterStart = document.getElementById('filterStart');
        const filterEnd = document.getElementById('filterEnd');
        const btnFilter = document.getElementById('btnFilter');
        const btnClear = document.getElementById('btnClearFilters');

        if (!filterDocType || !searchInput || !btnClear || !filterStart || !filterEnd || !btnFilter) {
            console.warn('Filter elements not found');
            return;
        }

        // Remove existing listeners (if any)
        const newFilterDocType = filterDocType.cloneNode(true);
        filterDocType.parentNode.replaceChild(newFilterDocType, filterDocType);
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        const newFilterStart = filterStart.cloneNode(true);
        filterStart.parentNode.replaceChild(newFilterStart, filterStart);
        const newFilterEnd = filterEnd.cloneNode(true);
        filterEnd.parentNode.replaceChild(newFilterEnd, filterEnd);
        const newBtnFilter = btnFilter.cloneNode(true);
        btnFilter.parentNode.replaceChild(newBtnFilter, btnFilter);
        const newBtnClear = btnClear.cloneNode(true);
        btnClear.parentNode.replaceChild(newBtnClear, btnClear);

        function applyFilters(){
            const selectedType = newFilterDocType.value;
            const searchTerm = newSearchInput.value.trim().toLowerCase();
            const startDate = newFilterStart.value;
            const endDate = newFilterEnd.value;
            
            const rows = document.querySelectorAll('.entry-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const rowType = row.getAttribute('data-type') || '';
                const rowId = row.getAttribute('data-id') || '';
                const rowDesc = row.getAttribute('data-description') || '';
                const rowDate = row.getAttribute('data-date') || '';
                
                let typeMatch = !selectedType || selectedType === '' || rowType === selectedType;
                let searchMatch = !searchTerm || rowId.includes(searchTerm) || rowDesc.includes(searchTerm);
                let startMatch = !startDate || (rowDate && rowDate >= startDate);
                let endMatch = !endDate || (rowDate && rowDate <= endDate);
                
                if (typeMatch && searchMatch && startMatch && endMatch) {
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
        newFilterStart.addEventListener('change', applyFilters);
        newFilterEnd.addEventListener('change', applyFilters);
        newBtnFilter.addEventListener('click', function(e){
            e.preventDefault();
            applyFilters();
        });
        newBtnClear.addEventListener('click', function(e){
            e.preventDefault();
            newFilterDocType.value = '';
            newSearchInput.value = '';
            newFilterStart.value = '';
            newFilterEnd.value = '';
            applyFilters();
        });
    }
    
    // Date filtering is now handled through applyFiltersAndPagination.
    let filtersInitialized = false;
    
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
