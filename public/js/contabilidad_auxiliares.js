document.addEventListener('DOMContentLoaded', function(){
    var selectedAccounts = [];

    var btnSelect = document.getElementById('btnSelectAccounts');
    var btnRun = document.getElementById('btnRunAux');
    var btnExport = document.getElementById('btnExportAux');
    var auxCount = document.getElementById('auxCount');
    var auxCurrency = document.getElementById('auxCurrency');

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
            var deselect = modalDiv.querySelector('#auxDeselectAll');
            if (deselect) {
                deselect.addEventListener('click', function(e){
                    e.preventDefault();
                    modalDiv.querySelectorAll('#formAuxAccounts input[type=checkbox]').forEach(function(cb){ cb.checked = false; });
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

    // normalize date inputs to ISO YYYY-MM-DD
    function normalizeDate(d){
        if (!d) return '';
        if (typeof d !== 'string') d = String(d);
        d = d.trim();
        if (!d) return '';
        // dd/mm/yyyy -> yyyy-mm-dd
        if (d.indexOf('/') !== -1) {
            var p = d.split('/');
            if (p.length === 3) {
                var dd = p[0].padStart(2,'0');
                var mm = p[1].padStart(2,'0');
                var yyyy = p[2];
                if (yyyy.length === 2) yyyy = '20' + yyyy;
                return yyyy + '-' + mm + '-' + dd;
            }
        }
        // already ISO
        if (/^\d{4}-\d{2}-\d{2}$/.test(d)) return d;
        // try Date parse fallback
        var dt = new Date(d);
        if (!isNaN(dt.getTime())){
            var y = dt.getFullYear();
            var m = String(dt.getMonth()+1).padStart(2,'0');
            var day = String(dt.getDate()).padStart(2,'0');
            return y + '-' + m + '-' + day;
        }
        return d;
    }

    // (selection handled by the single listener attached when modal is loaded)

    btnRun.addEventListener('click', function(){
        var rawStart = document.getElementById('auxStart').value;
        var rawEnd = document.getElementById('auxEnd').value;
        var start = normalizeDate(rawStart);
        var end = normalizeDate(rawEnd);
        var currency = auxCurrency ? auxCurrency.value : 'local';
        var payload = new FormData();
        if (start) payload.append('start', start);
        if (end) payload.append('end', end);
        payload.append('currency', currency);
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

    var btnClearFilters = document.getElementById('btnClearFilters');
    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', function(){
            selectedAccounts = [];
            auxCount.textContent = '0';
            var startInput = document.getElementById('auxStart');
            var endInput = document.getElementById('auxEnd');
            if (startInput) startInput.value = '';
            if (endInput) endInput.value = '';
            var target = document.getElementById('auxResult');
            if (target) target.innerHTML = '';
        });
    }

    btnExport.addEventListener('click', function(){
        var rawStart = document.getElementById('auxStart').value;
        var rawEnd = document.getElementById('auxEnd').value;
        var start = normalizeDate(rawStart);
        var end = normalizeDate(rawEnd);
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
            var rawStart = document.getElementById('auxStart').value;
            var rawEnd = document.getElementById('auxEnd').value;
            var start = normalizeDate(rawStart);
            var end = normalizeDate(rawEnd);
            var base = (typeof base_url !== 'undefined') ? base_url : '/';
            var currency = (document.getElementById('auxCurrency') ? document.getElementById('auxCurrency').value : 'local');
            var url = base.replace(/\/$/, '') + '/contabilidad/auxiliares_export?format=pdf&start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(end) + '&currency=' + encodeURIComponent(currency);
            if (selectedAccounts.length) {
                selectedAccounts.forEach(function(id){ url += '&accounts[]=' + encodeURIComponent(id); });
            } else {
                url += '&all=1';
            }
            // trigger download
            window.location = url;
        });
    }

    // Export XLSX
    var btnExportXlsx = document.getElementById('btnExportXlsx');
    if (btnExportXlsx) {
        btnExportXlsx.addEventListener('click', function(){
            var rawStart = document.getElementById('auxStart').value;
            var rawEnd = document.getElementById('auxEnd').value;
            var start = normalizeDate(rawStart);
            var end = normalizeDate(rawEnd);
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
        var currency = payload.currency === 'usd' ? 'usd' : 'local';
        var currencyLabel = payload.currency_label ? ' ' + payload.currency_label : '';
        var currencyPrefix = currency === 'usd' ? '$' : 'C$';
        var html = '';
        payload.data.forEach(function(acc){
            // Calculate totals for debit and credit
            var totalDebit = 0;
            var totalCredit = 0;
            acc.lines.forEach(function(l){
                totalDebit += (l.debit ? parseFloat(l.debit) : 0);
                totalCredit += (l.credit ? parseFloat(l.credit) : 0);
            });
            
            html += '<div style="border:1px solid #ddd;margin-bottom:12px;padding:8px;overflow-x:auto;">';
            html += '<div style="font-weight:700;margin-bottom:6px;">' + (acc.code || '') + ' - ' + (acc.name || '') + '</div>';
                html += '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
                html += '<thead><tr style="background:#f7f7f7;"><th style="padding:6px;border:1px solid #ddd;">Fecha</th><th style="padding:6px;border:1px solid #ddd;">Tipo Documento</th><th style="padding:6px;border:1px solid #ddd;">No Documento</th><th style="padding:6px;border:1px solid #ddd;">Centro Costo</th><th style="padding:6px;border:1px solid #ddd;">Descripción</th><th style="padding:6px;border:1px solid #ddd;text-align:right;">Débito' + currencyLabel + '</th><th style="padding:6px;border:1px solid #ddd;text-align:right;">Crédito' + currencyLabel + '</th></tr></thead>';
            html += '<tbody>';
            // opening
                html += '<tr><td colspan="6" style="font-style:italic;padding:6px;border:1px solid #eee;">Saldo anterior</td><td style="text-align:right;padding:6px;border:1px solid #eee;">' + formatCurrency(acc.opening, currencyPrefix) + '</td></tr>';
            acc.lines.forEach(function(l){
                html += '<tr>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.date || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.doc_type || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.document_no || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.centro_costo || '') + '</td>';
                    html += '<td style="padding:6px;border:1px solid #eee;">' + (l.descripcion || '') + '</td>';
                        html += '<td style="padding:6px;border:1px solid #eee;text-align:right;">' + (l.debit ? formatCurrency(l.debit, currencyPrefix) : '-') + '</td>';
                        html += '<td style="padding:6px;border:1px solid #eee;text-align:right;">' + (l.credit ? formatCurrency(l.credit, currencyPrefix) : '-') + '</td>';
                html += '</tr>';
            });
                html += '<tr style="font-weight:700;background:#f0f0f0;"><td colspan="5" style="text-align:right;padding:6px;border-top:2px solid #000;border-bottom:2px solid #000;">Subtotal</td><td style="text-align:right;padding:6px;border-top:2px solid #000;border-bottom:2px solid #000;">' + formatCurrency(totalDebit, currencyPrefix) + '</td><td style="text-align:right;padding:6px;border-top:2px solid #000;border-bottom:2px solid #000;">' + formatCurrency(totalCredit, currencyPrefix) + '</td></tr>';
                html += '<tr style="font-weight:700;"><td colspan="6" style="text-align:right;padding:6px;border-top:2px solid #000;">Balance de Cuenta</td><td style="text-align:right;padding:6px;border-top:2px solid #000;">' + formatCurrency(acc.final_balance, currencyPrefix) + '</td></tr>';
            html += '</tbody></table>';
            html += '</div>';
        });
        html += '</div>';
        target.innerHTML = html;
    }

    function formatNumber(n){
        var num = Number(n);
        if (Number.isNaN(num)) { return '0.00'; }
        var sign = num < 0 ? '-' : '';
        num = Math.abs(num);
        var parts = num.toFixed(2).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return sign + parts[0] + '.' + parts[1];
    }

    function formatCurrency(n, prefix){
        return prefix + formatNumber(n);
    }
});
