document.addEventListener('DOMContentLoaded', function(){
    var selectedAccounts = [];

    var btnSelect = document.getElementById('btnSelectAccounts');
    var btnRun = document.getElementById('btnRunAux');
    var btnExport = document.getElementById('btnExportAux');
    var auxCount = document.getElementById('auxCount');

    btnSelect.addEventListener('click', function(){
        // open modal via AJAX
        // avoid duplicates: remove existing modal if present
        var existing = document.getElementById('auxiliaresModal');
        if (existing) {
            try { if (window.jQuery && window.jQuery(existing).modal) window.jQuery(existing).modal('hide'); } catch(e){}
            existing.parentNode.removeChild(existing);
        }
        var modalDiv = document.createElement('div');
        modalDiv.id = 'auxiliaresModal';
        modalDiv.className = 'modal fade';
        modalDiv.innerHTML = '<div class="modal-dialog modal-lg"><div class="modal-content">Cargando...</div></div>';
        document.body.appendChild(modalDiv);
        // build URL using base_url if available to avoid 404 in subfolders
        var base = (typeof base_url !== 'undefined') ? base_url : '/';
        var url = base.replace(/\/$/, '') + '/contabilidad/modal_auxiliares';
        fetch(url).then(function(r){ return r.text(); }).then(function(html){
            modalDiv.querySelector('.modal-content').innerHTML = html;
            // use Bootstrap modal if available
            if (window.jQuery && window.jQuery(modalDiv).modal) {
                window.jQuery(modalDiv).modal({backdrop:'static'});
            }
            // pre-check previously selected
            selectedAccounts.forEach(function(id){ var cb = modalDiv.querySelector('input[value="'+id+'"]'); if (cb) cb.checked = true; });

            // initialize modal controls from parent script (ensures handlers run after innerHTML insertion)
            var selAll = modalDiv.querySelector('#auxSelectAll');
            if (selAll) {
                selAll.addEventListener('click', function(e){
                    e.preventDefault();
                    modalDiv.querySelectorAll('#formAuxAccounts input[type=checkbox]').forEach(function(cb){ cb.checked = true; });
                });
            }
            var applyBtn = modalDiv.querySelector('#auxApply');
            if (applyBtn) {
                applyBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    var ids = Array.from(modalDiv.querySelectorAll('#formAuxAccounts input[name="accounts[]"]:checked')).map(function(i){ return i.value; });
                    document.dispatchEvent(new CustomEvent('aux:accounts:selected', {detail: ids}));
                    if (window.jQuery && window.jQuery(modalDiv).modal) {
                        try { window.jQuery(modalDiv).modal('hide'); } catch(err){}
                    }
                    setTimeout(function(){ var el = document.getElementById('auxiliaresModal'); if (el && el.parentNode) el.parentNode.removeChild(el); }, 350);
                });
            }

            // attach a single document-level listener (once) to receive the selection
            if (!document._aux_listener_attached) {
                document.addEventListener('aux:accounts:selected', function(e){
                    var ids = (e && e.detail) ? e.detail : [];
                    selectedAccounts = Array.isArray(ids) ? ids : [];
                    auxCount.textContent = selectedAccounts.length;
                    var el = document.getElementById('auxiliaresModal'); if (el && el.parentNode) el.parentNode.removeChild(el);
                });
                document._aux_listener_attached = true;
            }

            // initialize live search on the auxSearch input (debounced)
            var searchInput = modalDiv.querySelector('#auxSearch');
            if (searchInput) {
                var doSearch = debounce(function(e){
                    var q = String(searchInput.value || '').trim();
                    var base = (typeof base_url !== 'undefined') ? base_url : '/';
                    var sUrl = base.replace(/\/$/, '') + '/contabilidad/search_accounts?q=' + encodeURIComponent(q) + '&limit=200';
                    fetch(sUrl).then(function(r){ return r.json(); }).then(function(json){
                        var list = modalDiv.querySelector('#auxAccountsList');
                        if (!list) return;
                        list.innerHTML = '';
                        var rows = (json && json.data) ? json.data : [];
                        rows.forEach(function(a){
                            var div = document.createElement('div');
                            div.style.padding = '4px 0';
                            div.style.borderBottom = '1px solid #eee';
                            div.style.display = 'flex';
                            div.style.alignItems = 'center';
                            div.style.gap = '8px';
                            var input = document.createElement('input');
                            input.type = 'checkbox';
                            input.name = 'accounts[]';
                            input.value = a.id;
                            input.id = 'acct_' + a.id;
                            if (selectedAccounts.indexOf(String(a.id)) !== -1 || selectedAccounts.indexOf(a.id) !== -1) input.checked = true;
                            var label = document.createElement('label');
                            label.htmlFor = input.id;
                            label.textContent = (a.code ? (a.code + ' - ') : '') + (a.name || '');
                            div.appendChild(input); div.appendChild(label);
                            list.appendChild(div);
                        });
                    }).catch(function(err){ console.error('search_accounts error', err); });
                }, 250);
                searchInput.addEventListener('input', doSearch);
            }
        }).catch(function(err){
            console.error('Error loading modal:', err, url);
            alert('No se pudo cargar la lista de cuentas (404). URL: ' + url);
        });
    });

    // helper: debounce
    function debounce(fn, wait){
        var t;
        return function(){
            var args = arguments;
            clearTimeout(t);
            t = setTimeout(function(){ fn.apply(null, args); }, wait);
        };
    }

    // (selection handled by the single listener attached when modal is loaded)

    btnRun.addEventListener('click', function(){
        var start = document.getElementById('auxStart').value;
        var end = document.getElementById('auxEnd').value;
        var payload = new FormData();
        payload.append('start', start);
        payload.append('end', end);
        if (selectedAccounts.length === 0) {
            payload.append('all', '1');
        } else {
            selectedAccounts.forEach(function(id){ payload.append('accounts[]', id); });
        }

        var base2 = (typeof base_url !== 'undefined') ? base_url : '/';
        var dataUrl = base2.replace(/\/$/, '') + '/contabilidad/auxiliares_data';
        fetch(dataUrl, {method:'POST', body: payload}).then(function(r){ return r.json(); }).then(function(json){
            renderAux(json);
        }).catch(function(err){ console.error(err); alert('Error al obtener datos'); });
    });

    btnExport.addEventListener('click', function(){
        var start = document.getElementById('auxStart').value;
        var end = document.getElementById('auxEnd').value;
        var currency = (document.getElementById('auxCurrency') ? document.getElementById('auxCurrency').value : 'local');
        var base3 = (typeof base_url !== 'undefined') ? base_url : '/';
        var url = base3.replace(/\/$/, '') + '/contabilidad/auxiliares_export?start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(end);
        url += '&currency=' + encodeURIComponent(currency);
        if (selectedAccounts.length) {
            selectedAccounts.forEach(function(id){ url += '&accounts[]=' + encodeURIComponent(id); });
        } else {
            url += '&all=1';
        }
        window.location = url;
    });

    // Export PDF
    var btnExportPdf = document.getElementById('btnExportPdf');
    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', function(){
            var start = document.getElementById('auxStart').value;
            var end = document.getElementById('auxEnd').value;
            var base = (typeof base_url !== 'undefined') ? base_url : '/';
            var currency = (document.getElementById('auxCurrency') ? document.getElementById('auxCurrency').value : 'local');
            var url = base.replace(/\/$/, '') + '/contabilidad/auxiliares_export?format=pdf&start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(end) + '&currency=' + encodeURIComponent(currency);
            if (selectedAccounts.length) {
                selectedAccounts.forEach(function(id){ url += '&accounts[]=' + encodeURIComponent(id); });
            } else {
                url += '&all=1';
            }
            // open in new tab
            window.open(url, '_blank');
        });
    }

    // Export XLSX
    var btnExportXlsx = document.getElementById('btnExportXlsx');
    if (btnExportXlsx) {
        btnExportXlsx.addEventListener('click', function(){
            var start = document.getElementById('auxStart').value;
            var end = document.getElementById('auxEnd').value;
            var base = (typeof base_url !== 'undefined') ? base_url : '/';
            var currency = (document.getElementById('auxCurrency') ? document.getElementById('auxCurrency').value : 'local');
            var url = base.replace(/\/$/, '') + '/contabilidad/auxiliares_export?format=xlsx&start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(end) + '&currency=' + encodeURIComponent(currency);
            if (selectedAccounts.length) {
                selectedAccounts.forEach(function(id){ url += '&accounts[]=' + encodeURIComponent(id); });
            } else {
                url += '&all=1';
            }
            window.location = url;
        });
    }

    function renderAux(payload){
        var target = document.getElementById('auxResult');
        if (!payload || !payload.data) { target.innerHTML = '<div class="alert alert-warning">Sin datos</div>'; return; }
        var html = '';
        payload.data.forEach(function(acc){
            html += '<div style="border:1px solid #ddd;margin-bottom:12px;padding:8px;">';
            html += '<div style="font-weight:700;margin-bottom:6px;">' + (acc.code || '') + ' - ' + (acc.name || '') + '</div>';
                html += '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
                html += '<thead><tr style="background:#f7f7f7;"><th style="padding:6px;border:1px solid #ddd;">fecha</th><th style="padding:6px;border:1px solid #ddd;">Tipo Documento</th><th style="padding:6px;border:1px solid #ddd;">No Documento</th><th style="padding:6px;border:1px solid #ddd;">Centro costo</th><th style="padding:6px;border:1px solid #ddd;">Descripcion</th><th style="padding:6px;border:1px solid #ddd;text-align:right;">Debito</th><th style="padding:6px;border:1px solid #ddd;text-align:right;">Credito</th><th style="padding:6px;border:1px solid #ddd;text-align:right;">Balance Final</th></tr></thead>';
            html += '<tbody>';
            // opening
                html += '<tr><td colspan="7" style="font-style:italic;padding:6px;border:1px solid #eee;">Saldo anterior</td><td style="text-align:right;padding:6px;border:1px solid #eee;">' + formatNumber(acc.opening) + '</td></tr>';
            acc.lines.forEach(function(l){
                html += '<tr>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.date || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.doc_type || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.document_no || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.centro_costo || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.descripcion || '') + '</td>';
                        html += '<td style="padding:6px;border:1px solid #eee;text-align:right;">' + (l.debit ? formatNumber(l.debit) : '-') + '</td>';
                        html += '<td style="padding:6px;border:1px solid #eee;text-align:right;">' + (l.credit ? formatNumber(l.credit) : '-') + '</td>';
                        html += '<td style="padding:6px;border:1px solid #eee;text-align:right;">' + formatNumber(l.balance) + '</td>';
                html += '</tr>';
            });
                html += '<tr style="font-weight:700;"><td colspan="7" style="text-align:right;padding:6px;border-top:2px solid #000;">Balance Final</td><td style="text-align:right;padding:6px;border-top:2px solid #000;">' + formatNumber(acc.final_balance) + '</td></tr>';
            html += '</tbody></table></div>';
        });
        target.innerHTML = html;
    }

    function formatNumber(n){
        if (!n && n !== 0) return '0,00';
        return Number(n).toLocaleString('es-ES', {minimumFractionDigits:2, maximumFractionDigits:2});
    }
});
