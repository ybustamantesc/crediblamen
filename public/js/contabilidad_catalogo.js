document.addEventListener('DOMContentLoaded', function(){
    try { console.log('contabilidad_catalogo: DOMContentLoaded'); } catch(e){}
    // Guardar referencias
    const btnNew = document.getElementById('btnNewAccount');
    const btnExport = document.getElementById('btnExportExcel');
    const importMayorFile = document.getElementById('importMayorFile');
    const importMayorBtn = document.getElementById('importMayorBtn');
    const filterInput = document.getElementById('filterAccount');
    const filterType = document.getElementById('filterType');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    let modalContainer = document.getElementById('modalContainer');
    // ensure modalContainer exists for dynamic modals and keep reference
    if (!modalContainer) {
        modalContainer = document.createElement('div');
        modalContainer.id = 'modalContainer';
        document.body.appendChild(modalContainer);
    }
    
    let allAccounts = []; // Store all accounts for filtering
    let currentPage = 1;
    const pageSize = 25;

    // Helper: fetch accounts and render a Bootstrap table (with indentation for hierarchy)
    function fetchAccounts(){
        // use the existing JSON endpoint that returns the flat account list
        // (`accounts_tree` did not exist and caused HTML/404 to be returned)
        fetch(base_url+'contabilidad/accounts')
        .then(r=>r.json()).then(json=>{
            allAccounts = json.data || [];
            renderFilteredAccounts();
        }).catch(err=>{
            console.error(err);
            const tbody = document.getElementById('accountsBody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error cargando cuentas</td></tr>';
            } else {
                console.error('accountsBody element not found when handling fetch error');
            }
        });
    }
    
    function renderFilteredAccounts() {
        const searchTerm = filterInput ? filterInput.value.toLowerCase().trim() : '';
        const selectedType = filterType ? filterType.value.toLowerCase() : '';
        
        const tbody = document.getElementById('accountsBody');
        if (!tbody) { console.error('accountsBody element not found'); return; }
        
        // Filter accounts
        let filtered = allAccounts;
        
        // Apply search filter (minimum 3 characters)
        if (searchTerm && searchTerm.length >= 3) {
            filtered = filtered.filter(a => {
                const code = (a.code || '').toLowerCase();
                const name = (a.name || '').toLowerCase();
                return code.includes(searchTerm) || name.includes(searchTerm);
            });
        }
        
        // Apply type filter
        if (selectedType) {
            filtered = filtered.filter(a => (a.type || '').toLowerCase() === selectedType);
        }
        
        if (!filtered.length){
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No se encontraron cuentas</td></tr>';
            return;
        }

        // Build id -> node map and children lists
        const map = {};
        allAccounts.forEach(a=> map[a.id] = Object.assign({}, a, { children: [] }));
        const roots = [];
        allAccounts.forEach(a=>{
            if (a.parent_id && map[a.parent_id]) map[a.parent_id].children.push(map[a.id]);
            else roots.push(map[a.id]);
        });

        // traverse and produce flat ordered list with depth
        const ordered = [];
        function walk(node, depth){
            // Only include if it's in filtered list
            const isInFiltered = filtered.some(f => f.id === node.id);
            if (isInFiltered) {
                ordered.push(Object.assign({}, node, { depth: depth }));
            }
            if (node.children && node.children.length) {
                // sort children by code for consistent order
                node.children.sort((x,y)=> (x.code||'').localeCompare(y.code||''));
                node.children.forEach(c=> walk(c, depth+1));
            }
        }
        roots.sort((x,y)=> (x.code||'').localeCompare(y.code||''));
        roots.forEach(r=> walk(r, 0));

        // Pagination
        const totalRecords = ordered.length;
        const totalPages = Math.ceil(totalRecords / pageSize);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;
        
        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalRecords);
        const paginated = ordered.slice(startIdx, endIdx);
        
        // Update pagination info
        const pagingInfo = document.getElementById('catalogoPagingInfo');
        if (pagingInfo) {
            if (totalRecords === 0) {
                pagingInfo.textContent = 'No hay registros';
            } else {
                pagingInfo.textContent = `Mostrando ${startIdx + 1} - ${endIdx} de ${totalRecords} cuentas`;
            }
        }
        
        // Render pagination buttons
        const pagination = document.getElementById('catalogoPagination');
        if (pagination) {
            pagination.innerHTML = '';
            
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
            nextLi.className = 'page-item' + (currentPage === totalPages || totalPages === 0 ? ' disabled' : '');
            nextLi.innerHTML = '<a class="page-link" href="#" data-page="next">Siguiente</a>';
            pagination.appendChild(nextLi);
            
            // Add click handlers
            pagination.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.getAttribute('data-page');
                    if (page === 'prev' && currentPage > 1) {
                        currentPage--;
                        renderFilteredAccounts();
                    } else if (page === 'next' && currentPage < totalPages) {
                        currentPage++;
                        renderFilteredAccounts();
                    } else if (page !== 'prev' && page !== 'next') {
                        currentPage = parseInt(page);
                        renderFilteredAccounts();
                    }
                });
            });
        }

        function formatAccountType(type) {
            if (!type) return '';
            const value = type.toString().trim().toLowerCase();
            const labels = {
                activo: 'Activo',
                pasivo: 'Pasivo',
                patrimonio: 'Patrimonio',
                ingreso: 'Ingreso',
                gasto: 'Gasto',
                contingente: 'Contingente',
                orden: 'Orden'
            };
            return labels[value] || type.toString().trim();
        }

        // render rows
        tbody.innerHTML = '';
        paginated.forEach(a=>{
            const tr = document.createElement('tr');
            // indentation via padding
            const indent = Math.max(0, a.depth) * 18;
            const codeTd = document.createElement('td'); codeTd.innerHTML = '<strong>'+ (a.code||'') +'</strong>';
            const nameTd = document.createElement('td'); nameTd.innerHTML = '<div style="padding-left:'+indent+'px">'+ (a.name||'') +'</div>';
            const typeTd = document.createElement('td'); typeTd.textContent = formatAccountType(a.type || '');
            typeTd.className = 'text-muted';
            
            // Agrupación (report_bs/report_is) column
            const groupTd = document.createElement('td');
            const typeLower = (a.type || '').toString().toLowerCase();
            let groupText = '';
            if (a.agrupador_estado && a.agrupador_estado.toString().trim() !== '') {
                groupText = a.agrupador_estado;
            } else if (['activo','pasivo','patrimonio'].includes(typeLower)) {
                groupText = a.report_bs || '';
            } else {
                groupText = a.report_is || '';
            }
            groupTd.textContent = groupText;
            groupTd.style.fontWeight = '600'; groupTd.style.color = '#2a5298';

            // Naturaleza column
            const natTd = document.createElement('td');
            if (a.naturaleza) {
                const isDeudora = a.naturaleza === 'deudora';
                natTd.innerHTML = '<span style="background:#64748b;color:#fff;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:600;white-space:nowrap;border:1px solid #475569;">' +
                    (isDeudora ? '<i class="fas fa-arrow-up" style="color:#ef4444;font-size:9px;"></i> Deudora' : '<i class="fas fa-arrow-down" style="color:#10b981;font-size:9px;"></i> Acreedora') +
                    '</span>';
            } else {
                natTd.innerHTML = '<span style="color:#94a3b8;font-size:11px;">Sin definir</span>';
            }
            
            const bal = (typeof a.balance !== 'undefined') ? parseFloat(a.balance) : 0;
            const balTd = document.createElement('td'); balTd.style.textAlign = 'right'; balTd.innerHTML = bal.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
            const actionsTd = document.createElement('td'); actionsTd.style.textAlign = 'center';
            // Single "Acciones" dropdown with clean submenu styling
            actionsTd.innerHTML = ''+
                '<div class="action-dropdown" style="position:relative;display:inline-block;overflow:visible;">'
                + '<button class="btn btn-sm btn-outline-primary btn-actions-toggle" type="button">Acciones</button>'
                + '<div class="action-menu" style="position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid rgba(0,0,0,0.08);padding:6px 0;display:none;min-width:180px;z-index:99999;border-radius:10px;box-shadow:0 14px 30px rgba(0,0,0,0.12);overflow:visible;">' 
                    + '<button type="button" class="action-item btn-action-child" data-id="'+a.id+'" style="width:100%;border:none;background:transparent;padding:10px 16px;text-align:left;color:#0b1220;font-weight:600;cursor:pointer;">Subcuenta</button>'
                    + '<button type="button" class="action-item btn-action-sibling" data-id="'+a.id+'" style="width:100%;border:none;background:transparent;padding:10px 16px;text-align:left;color:#0b1220;font-weight:600;cursor:pointer;">Mismo nivel</button>'
                    + '<button type="button" class="action-item btn-action-edit" data-id="'+a.id+'" style="width:100%;border:none;background:transparent;padding:10px 16px;text-align:left;color:#0b1220;font-weight:600;cursor:pointer;">Editar</button>'
                    + '<button type="button" class="action-item btn-action-delete" data-id="'+a.id+'" style="width:100%;border:none;background:transparent;padding:10px 16px;text-align:left;color:#dc2626;font-weight:600;cursor:pointer;">Eliminar</button>'
                + '</div>'
                + '</div>';
            tr.appendChild(codeTd); tr.appendChild(nameTd); tr.appendChild(typeTd); tr.appendChild(groupTd); tr.appendChild(natTd); tr.appendChild(balTd); tr.appendChild(actionsTd);
            tbody.appendChild(tr);
        });

        attachTableEvents();
    }

    function attachTableEvents(){
        // Use event delegation so handlers work for server-rendered and dynamically
        // inserted rows. Attach once to document body.
        if (!document.body.dataset.accountsDelegation) {
            try { console.log('contabilidad_catalogo: attaching delegated handlers'); } catch(e){}
            document.body.addEventListener('click', function(ev){
                // Close any open action menus when clicking outside
                const openMenus = document.querySelectorAll('.action-menu');
                openMenus.forEach(m => {
                    if (!m.contains(ev.target) && !m.previousElementSibling?.contains(ev.target)) {
                        m.style.display = 'none';
                    }
                });

                const edit = ev.target.closest('.btn-action-edit');
                if (edit) {
                    ev.preventDefault();
                    const id = edit.getAttribute('data-id');
                    fetch(base_url+'contabilidad/modal_account?id='+id).then(r=>r.text()).then(html=>{
                        let mc = document.getElementById('modalContainer') || modalContainer;
                        if (!mc) {
                            mc = document.createElement('div'); mc.id = 'modalContainer'; document.body.appendChild(mc);
                        }
                        mc.innerHTML = html;
                        modalContainer = mc;
                        attachAccountModalEvents();
                    }).catch(e=>{console.error(e); alert('Error al abrir modal');});
                    return;
                }
                const child = ev.target.closest('.btn-action-child');
                if (child) {
                    ev.preventDefault();
                    const id = child.getAttribute('data-id');
                    fetch(base_url+'contabilidad/modal_account?mode=child&parent_id='+encodeURIComponent(id)).then(r=>r.text()).then(html=>{
                        let mc = document.getElementById('modalContainer') || modalContainer;
                        if (!mc) {
                            mc = document.createElement('div'); mc.id = 'modalContainer'; document.body.appendChild(mc);
                        }
                        mc.innerHTML = html;
                        modalContainer = mc;
                        attachAccountModalEvents();
                    }).catch(e=>{console.error(e); alert('Error al abrir modal');});
                    return;
                }
                const sibling = ev.target.closest('.btn-action-sibling');
                if (sibling) {
                    ev.preventDefault();
                    const id = sibling.getAttribute('data-id');
                    fetch(base_url+'contabilidad/modal_account?mode=sibling&parent_id='+encodeURIComponent(id)).then(r=>r.text()).then(html=>{
                        let mc = document.getElementById('modalContainer') || modalContainer;
                        if (!mc) {
                            mc = document.createElement('div'); mc.id = 'modalContainer'; document.body.appendChild(mc);
                        }
                        mc.innerHTML = html;
                        modalContainer = mc;
                        attachAccountModalEvents();
                    }).catch(e=>{console.error(e); alert('Error al abrir modal');});
                    return;
                }
                const del = ev.target.closest('.btn-action-delete');
                if (del) {
                    ev.preventDefault();
                    if (!confirm('Eliminar cuenta?')) return;
                    const id = del.getAttribute('data-id');
                    const fd = new FormData(); fd.append('id', id);
                    fetch(base_url+'contabilidad/account_delete', { method:'POST', body: fd })
                    .then(r=>r.json()).then(resp=>{
                        if (resp.status === 'success') fetchAccounts(); else if (resp.status === 'error' && resp.errors) alert(resp.errors.join('\n')); else alert('Error al eliminar');
                    }).catch(e=>{console.error(e); alert('Error');});
                    return;
                }
            });
            // Toggle action menu when clicking the Acciones button
            document.body.addEventListener('click', function(ev){
                const toggle = ev.target.closest('.btn-actions-toggle');
                if (toggle) {
                    ev.preventDefault();
                    const dropdown = toggle.closest('.action-dropdown');
                    if (!dropdown) return;
                    const menu = dropdown.querySelector('.action-menu');
                    if (!menu) return;
                    // Close other menus
                    document.querySelectorAll('.action-menu').forEach(m=>{ if (m !== menu) m.style.display = 'none'; });
                    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
                }
            });
            document.body.dataset.accountsDelegation = '1';
            // mark JS loaded for diagnostics
            try { document.body.dataset.accountsJs = '1'; } catch(e){}
        }
    }

    // Expose fallback global functions in case delegated handlers do not run
    window.openAccountModal = function(id){
        try { if (!id) return; } catch(e) { return; }
        fetch((typeof base_url === 'undefined' ? '' : base_url) + 'contabilidad/modal_account?id=' + id)
            .then(r => r.text()).then(html => {
                let mc = document.getElementById('modalContainer') || modalContainer;
                if (!mc) { mc = document.createElement('div'); mc.id = 'modalContainer'; document.body.appendChild(mc); }
                mc.innerHTML = html;
                modalContainer = mc;
                attachAccountModalEvents();
            }).catch(e => { console.error(e); alert('Error al abrir modal'); });
    };

    window.deleteAccount = function(id){
        if (!id) return; if (!confirm('Eliminar cuenta?')) return;
        const fd = new FormData(); fd.append('id', id);
        fetch((typeof base_url === 'undefined' ? '' : base_url) + 'contabilidad/account_delete', { method: 'POST', body: fd })
            .then(r => r.json()).then(resp => { if (resp.status === 'success') fetchAccounts(); else if (resp.errors) alert(resp.errors.join('\n')); else alert('Error al eliminar'); })
            .catch(e => { console.error(e); alert('Error'); });
    };

    // Nuevo
    if (btnNew){
        btnNew.addEventListener('click', function(){
            // Show a small chooser to decide creation mode
            let chooser = document.createElement('div');
            chooser.style.position = 'fixed'; chooser.style.left = '0'; chooser.style.top = '0'; chooser.style.width = '100%'; chooser.style.height = '100%'; chooser.style.display = 'flex'; chooser.style.alignItems = 'center'; chooser.style.justifyContent = 'center'; chooser.style.background = 'rgba(3,7,18,0.45)'; chooser.style.zIndex = 99999;
            chooser.innerHTML = '<div style="background:#fff;padding:20px;border-radius:10px;max-width:420px;width:100%;box-shadow:0 10px 30px rgba(2,6,23,0.16);">'
                + '<h4 style="margin:0 0 8px 0">Crear nueva cuenta</h4>'
                + '<div style="margin-bottom:12px;color:#374151;font-size:14px">Seleccione tipo de cuenta a crear:</div>'
                + '<div style="display:flex;gap:8px;margin-bottom:12px">'
                    + '<button id="cnew_root" class="btn btn-sm btn-outline-primary">Cuenta Mayor</button>'
                    + '<button id="cnew_child" class="btn btn-sm btn-outline-primary">Subcuenta</button>'
                    + '<button id="cnew_sibling" class="btn btn-sm btn-outline-primary">Mismo nivel</button>'
                + '</div>'
                + '<div style="text-align:right"><button id="cnew_cancel" class="btn btn-sm">Cancelar</button></div>'
                + '</div>';
            document.body.appendChild(chooser);

            function openModalWithMode(mode){
                // fetch modal with mode param
                fetch(base_url+'contabilidad/modal_account?mode='+encodeURIComponent(mode))
                    .then(r=>r.text()).then(html=>{
                        let mc = document.getElementById('modalContainer') || modalContainer;
                        if (!mc) { mc = document.createElement('div'); mc.id = 'modalContainer'; document.body.appendChild(mc); }
                        mc.innerHTML = html;
                        modalContainer = mc;
                        attachAccountModalEvents();
                        chooser.remove();
                    }).catch(e=>{ console.error(e); alert('Error al abrir modal'); chooser.remove(); });
            }

            chooser.querySelector('#cnew_cancel').addEventListener('click', function(){ chooser.remove(); });
            chooser.querySelector('#cnew_root').addEventListener('click', function(){ openModalWithMode('root'); });
            chooser.querySelector('#cnew_child').addEventListener('click', function(){ openModalWithMode('child'); });
            chooser.querySelector('#cnew_sibling').addEventListener('click', function(){ openModalWithMode('sibling'); });
        });
    }
    
    // Debounce helper
    let searchDebounce;
    function debounceSearch() {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentPage = 1; // Reset to first page on search
            renderFilteredAccounts();
        }, 300);
    }
    
    // Filter event listeners
    if (filterInput) {
        filterInput.addEventListener('input', debounceSearch);
    }
    
    if (filterType) {
        filterType.addEventListener('change', function() {
            currentPage = 1; // Reset to first page on filter change
            renderFilteredAccounts();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (filterInput) filterInput.value = '';
            if (filterType) filterType.value = '';
            currentPage = 1; // Reset to first page
            renderFilteredAccounts();
        });
    }
    
    // Export buttons - Basic and Bimoneda
    const exportBasic = document.getElementById('exportBasic');
    const exportBimoneda = document.getElementById('exportBimoneda');

    // Import (Excel) that marks Cuenta de Mayor based on 'Tipo' column
    if (importMayorBtn) {
        importMayorBtn.addEventListener('click', function(e){
            e.preventDefault();
            if (!importMayorFile || !importMayorFile.files || importMayorFile.files.length === 0) {
                alert('Seleccione un archivo Excel (.xls, .xlsx)');
                return;
            }
            const f = importMayorFile.files[0];
            const fd = new FormData();
            fd.append('file', f);
            importMayorBtn.disabled = true;
            importMayorBtn.textContent = 'Subiendo...';
            fetch(base_url + 'contabilidad/import_mayor_excel', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(resp => {
                    importMayorBtn.disabled = false;
                    importMayorBtn.textContent = 'Subir';
                    if (!resp) { alert('Respuesta inválida del servidor'); return; }
                    if (resp.status && resp.status === 'success') {
                        alert('Import completo. Filas: ' + resp.total_rows + '\nActualizadas: ' + resp.updated_rows + '\nNo encontradas: ' + resp.not_found + '\nErrores: ' + resp.errors);
                        // refresh accounts list
                        fetchAccounts();
                    } else {
                        alert('Error: ' + (resp.message || JSON.stringify(resp)));
                    }
                }).catch(err => {
                    importMayorBtn.disabled = false;
                    importMayorBtn.textContent = 'Subir';
                    console.error(err);
                    alert('Error al subir el archivo');
                });
        });
    }
    
    if (exportBasic) {
        exportBasic.addEventListener('click', function(e){
            e.preventDefault();
            const searchTerm = filterInput ? filterInput.value.trim() : '';
            const selectedType = filterType ? filterType.value : '';
            
            let url = base_url + 'contabilidad/catalogo_export';
            const params = ['report_type=basic'];
            if (searchTerm && searchTerm.length >= 3) params.push('search=' + encodeURIComponent(searchTerm));
            if (selectedType) params.push('type=' + encodeURIComponent(selectedType));
            
            url += '?' + params.join('&');
            window.location.href = url;
        });
    }
    
    if (exportBimoneda) {
        exportBimoneda.addEventListener('click', function(e){
            e.preventDefault();
            const searchTerm = filterInput ? filterInput.value.trim() : '';
            const selectedType = filterType ? filterType.value : '';
            
            let url = base_url + 'contabilidad/catalogo_export';
            const params = ['report_type=bimoneda'];
            if (searchTerm && searchTerm.length >= 3) params.push('search=' + encodeURIComponent(searchTerm));
            if (selectedType) params.push('type=' + encodeURIComponent(selectedType));
            
            url += '?' + params.join('&');
            window.location.href = url;
        });
    }

    // Manejar el modal (llenar select padre y submit)
    function attachAccountModalEvents(){
        const modal = document.getElementById('modalAccount');
        if (!modal) return;
        const btnCancel = document.getElementById('btnCancelAccount');
        const form = document.getElementById('formAccount');
        const createMode = modal.querySelector('select[name="create_mode"]');
        const parentSelect = modal.querySelector('select[name="parent_id"]');
        const codeInput = modal.querySelector('#inputCode');
        const levelInput = modal.querySelector('input[name="level"]');
        const suggestBtn = modal.querySelector('#btnSuggestCode');
        if (btnCancel) btnCancel.addEventListener('click', ()=> {
            const mc = document.getElementById('modalContainer') || modalContainer;
            if (mc) mc.innerHTML = '';
        });

        // cargar cuentas para select padre
        fetch(base_url+'contabilidad/accounts').then(r=>r.json()).then(json=>{
            const list = json.data || [];
            const sel = modal.querySelector('select[name="parent_id"]');
            const selectedParentId = sel ? sel.dataset.selectedParentId || '' : '';
            const currentAccountId = modal.querySelector('input[name="id"]').value;
            if (sel) {
                sel.innerHTML = '<option value="">-- Ninguna --</option>';
                list.forEach(a=>{
                    if (currentAccountId && String(currentAccountId) === String(a.id)) return; // no permitir seleccionar a si misma
                    const opt = document.createElement('option');
                    opt.value = a.id;
                    opt.text = (a.code ? a.code + ' - ' : '') + a.name;
                    if (typeof a.level !== 'undefined') opt.dataset.level = a.level;
                    if (selectedParentId && String(a.id) === String(selectedParentId)) {
                        opt.selected = true;
                    }
                    sel.appendChild(opt);
                });
            }
            updateParentState();
            fetchSuggestedCode();
        });

        function updateParentState() {
            if (!parentSelect || !createMode) return;
            if (createMode.value === 'root') {
                parentSelect.value = '';
                parentSelect.disabled = true;
            } else {
                parentSelect.disabled = false;
            }
            // Update modal title according to create mode
            const titleEl = modal.querySelector('#modalAccountTitle');
            if (titleEl && !form.querySelector('input[name="id"]').value) {
                if (createMode.value === 'sibling') titleEl.textContent = 'Nueva Cuenta (Mismo nivel)';
                else if (createMode.value === 'child') titleEl.textContent = 'Nueva Cuenta (Subcuenta)';
                else if (createMode.value === 'root') titleEl.textContent = 'Nueva Cuenta (Cuenta Mayor)';
            }
        }

        function fetchSuggestedCode() {
            if (!codeInput) return;
            const mode = createMode ? createMode.value : 'child';
            const params = new URLSearchParams();
            if (mode === 'sibling') {
                // For sibling mode, pass the source account id explicitly so backend can find its parent
                const srcId = parentSelect && parentSelect.dataset ? parentSelect.dataset.sourceAccountId : null;
                if (srcId && String(srcId).trim() !== '') params.append('source_id', srcId);
                else if (parentSelect && parentSelect.value) params.append('parent_id', parentSelect.value);
            } else if (mode !== 'root' && parentSelect && parentSelect.value) {
                params.append('parent_id', parentSelect.value);
            }
            if (mode) {
                params.append('mode', mode === 'root' ? 'root' : (mode === 'sibling' ? 'sibling' : 'child'));
            }
            fetch(base_url+'contabilidad/get_next_account_code?' + params.toString())
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && data.suggested_code) {
                        if (!form.querySelector('input[name="id"]').value) {
                            codeInput.value = data.suggested_code;
                        }
                    }
                    if (data && data.suggested_level && levelInput) {
                        if (!form.querySelector('input[name="id"]').value) {
                            levelInput.value = data.suggested_level;
                        }
                    }
                })
                .catch(err => console.error('Error fetching suggested code:', err));
        }

        if (!form) return;
        form.addEventListener('submit', function(e){
            e.preventDefault();
            const fd = new FormData(form);
            fetch(base_url+'contabilidad/account_save', { method:'POST', body: fd })
            .then(r=>r.json()).then(resp=>{
                if (resp.status === 'success'){
                    modalContainer.innerHTML = '';
                    fetchAccounts();
                } else if (resp.status === 'error' && resp.errors) {
                    const errDiv = modal.querySelector('#accountErrors');
                    if (errDiv){ errDiv.style.display = 'block'; errDiv.innerHTML = resp.errors.map(e=>`<div>• ${e}</div>`).join(''); }
                    else alert(resp.errors.join('\n'));
                } else {
                    alert('Error guardando');
                }
            }).catch(err=>{console.error(err); alert('Error');});
        });

        if (suggestBtn) {
            suggestBtn.addEventListener('click', function(e){
                e.preventDefault();
                fetchSuggestedCode();
            });
        }

        if (createMode) {
            createMode.addEventListener('change', function(){
                updateParentState();
                fetchSuggestedCode();
            });
        }

        if (parentSelect) {
            parentSelect.addEventListener('change', function(){
                if (!createMode || createMode.value !== 'root') fetchSuggestedCode();
            });
        }

        const btnCancelFooter = modal.querySelector('#btnCancelAccountFooter');
        if (btnCancelFooter) {
            btnCancelFooter.addEventListener('click', function(){
                const mc = document.getElementById('modalContainer') || modalContainer;
                if (mc) mc.innerHTML = '';
            });
        }
    }

    if (typeof base_url === 'undefined') window.base_url = window.location.origin + '/servicredit/';
    fetchAccounts();
});
