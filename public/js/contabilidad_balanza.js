(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var monthInput = document.getElementById('balanzaMonth');
        var refresh = document.getElementById('balanzaRefresh');
        var tbody = document.querySelector('#balanzaTable tbody');
        var footer = document.getElementById('balanzaFooter');
        var exportBtn = document.getElementById('balanzaExport');
        var exportAllBtn = document.getElementById('balanzaExportAll');
        var currencySel = document.getElementById('balanzaCurrency');

        function format(n){ return parseFloat(n||0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

        function render(data){
            tbody.innerHTML = '';
            if (!data || !data.rows) { tbody.innerHTML = '<tr><td colspan="6">Sin datos</td></tr>'; return; }
            // filter out internal adjustment account 9999 (should also be excluded server-side)
            var rows = data.rows.filter(function(rr){ return (rr.code||'').toString().trim() !== '9999'; });
            if (!rows || rows.length === 0) { tbody.innerHTML = '<tr><td colspan="6">Sin datos</td></tr>'; return; }
            rows.forEach(function(r){
                var tr = document.createElement('tr');
                var saldo_anterior = parseFloat(r.opening_deudor || 0) - parseFloat(r.opening_acreedor || 0);
                var cargos = parseFloat(r.debits || 0);
                var abonos = parseFloat(r.credits || 0);
                var saldo_actual = saldo_anterior + cargos - abonos;
                tr.innerHTML = '<td>' + (r.code||'') + '</td>' +
                               '<td>' + (r.name||'') + '</td>' +
                               '<td class="text-right">' + format(saldo_anterior) + '</td>' +
                               '<td class="text-right">' + format(cargos) + '</td>' +
                               '<td class="text-right">' + format(abonos) + '</td>' +
                               '<td class="text-right">' + format(saldo_actual) + '</td>';
                tbody.appendChild(tr);
                // highlight negative numbers defensively (if any cell contains negative value)
                try {
                    var cells = tr.querySelectorAll('td.text-right');
                    for (var i=0;i<cells.length;i++){
                        var txt = cells[i].innerText.replace(/,/g,'');
                        var val = parseFloat(txt);
                        if (!isNaN(val) && val < 0) cells[i].classList.add('negative');
                    }
                } catch(e){ /* ignore */ }
            });
            // compute totals client-side from filtered rows to ensure 9999 excluded
            var tot_saldo_anterior = 0, tot_cargos = 0, tot_abonos = 0, tot_saldo_actual = 0;
            var group = document.getElementById('balanzaGroup');
            var isDetalleView = group && group.value === 'detalle';
            var mayorParentIds = new Set();
            rows.forEach(function(rr){
                if (rr.parent_id !== null && rr.parent_id !== undefined && rr.parent_id !== '') {
                    mayorParentIds.add(String(rr.parent_id));
                }
            });
            rows.forEach(function(rr){
                var isMayorRow = rr.is_mayor === 1 || rr.is_mayor === '1' || rr.is_mayor === true || mayorParentIds.has(String(rr.id));
                if (isDetalleView && isMayorRow) {
                    return;
                }
                var saldo_anterior = parseFloat(rr.opening_deudor || 0) - parseFloat(rr.opening_acreedor || 0);
                var cargos = parseFloat(rr.debits || 0);
                var abonos = parseFloat(rr.credits || 0);
                var saldo_actual = saldo_anterior + cargos - abonos;
                tot_saldo_anterior += saldo_anterior;
                tot_cargos += cargos;
                tot_abonos += abonos;
                tot_saldo_actual += saldo_actual;
            });
            document.getElementById('tot_saldo_anterior').innerText = format(tot_saldo_anterior);
            document.getElementById('tot_cargos').innerText = format(tot_cargos);
            document.getElementById('tot_abonos').innerText = format(tot_abonos);
            document.getElementById('tot_saldo_actual').innerText = format(tot_saldo_actual);
            footer.innerText = 'Cuentas: ' + rows.length;
        }

        function monthRange(m){
            // m expected in format YYYY-MM
            if (!m) return null;
            var parts = m.split('-');
            if (parts.length !== 2) return null;
            var y = parseInt(parts[0],10); var mm = parseInt(parts[1],10);
            var start = y + '-' + (parts[1]) + '-01';
            var lastDay = new Date(y, mm, 0).getDate();
            var end = y + '-' + (parts[1]) + '-' + (lastDay < 10 ? '0'+lastDay : lastDay);
            return { start: start, end: end };
        }

        function load(){
            var qs = '?';
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) { qs += 'start_date=' + encodeURIComponent(mr.start) + '&end_date=' + encodeURIComponent(mr.end) + '&'; }
            // always include zero-movement accounts
            qs += 'include_zero=1&';
            // currency selection
            try { if (currencySel && currencySel.value) qs += 'currency=' + encodeURIComponent(currencySel.value) + '&'; } catch(e) {}
            // grouping / scope: if 'mayor' selected request only_mayor filter
            try {
                var group = document.getElementById('balanzaGroup');
                if (group && group.value === 'mayor') {
                    qs += 'only_mayor=1&';
                }
            } catch(e) { /* ignore */ }
            fetch(base_url + 'contabilidad/balanza_data' + qs)
            .then(function(r){ return r.json(); })
            .then(function(resp){ if (resp.status === 'success') render(resp.data); else { tbody.innerHTML = '<tr><td colspan="9">Error al obtener datos</td></tr>'; console.error(resp); } })
            .catch(function(e){ tbody.innerHTML = '<tr><td colspan="9">Error de comunicación</td></tr>'; console.error(e); });
        }

        refresh.addEventListener('click', function(ev){ ev.preventDefault(); load(); });
        // Request Excel export (XLSX) with formatting
        if (exportBtn) exportBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var params = [];
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) { params.push('start_date=' + encodeURIComponent(mr.start)); params.push('end_date=' + encodeURIComponent(mr.end)); }
            // always include zero-movement accounts
            params.push('include_zero=1');
            // currency selection
            try { if (currencySel && currencySel.value) params.push('currency=' + encodeURIComponent(currencySel.value)); } catch(e) {}
            // grouping options
            try {
                var g = document.getElementById('balanzaGroup');
                if (g && g.value === 'mayor') { params.push('only_mayor=1'); }
            } catch(e) {}
            params.push('format=excel');
            var qs = params.length ? ('?' + params.join('&')) : '';
            window.location = base_url + 'contabilidad/balanza_export' + qs;
        });
        if (exportAllBtn) exportAllBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var params = [];
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) { params.push('start_date=' + encodeURIComponent(mr.start)); params.push('end_date=' + encodeURIComponent(mr.end)); }
            // always include zero-movement accounts
            params.push('include_zero=1');
            // currency selection
            try { if (currencySel && currencySel.value) params.push('currency=' + encodeURIComponent(currencySel.value)); } catch(e) {}
            // grouping options
            try {
                var g2 = document.getElementById('balanzaGroup');
                if (g2 && g2.value === 'mayor') { params.push('only_mayor=1'); }
            } catch(e) {}
            params.push('format=excel');
            var qs = params.length ? ('?' + params.join('&')) : '';
            window.location = base_url + 'contabilidad/balanza_export' + qs;
        });
        var pdfBtn = document.getElementById('balanzaPdf');
        if (pdfBtn) pdfBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var qs = '?';
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) qs += 'start_date=' + encodeURIComponent(mr.start) + '&end_date=' + encodeURIComponent(mr.end) + '&';
            qs += 'include_zero=1&';
            try { if (currencySel && currencySel.value) qs += 'currency=' + encodeURIComponent(currencySel.value) + '&'; } catch(e) {}
            try { var g = document.getElementById('balanzaGroup'); if (g && g.value === 'mayor') qs += 'group=mayor&'; } catch(e) {}
            window.open(base_url + 'contabilidad/balanza_pdf' + qs, '_blank');
        });
        var pdfBgBtn = document.getElementById('balanzaPdfBg');
        if (pdfBgBtn) pdfBgBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var qs = '?';
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) qs += 'start_date=' + encodeURIComponent(mr.start) + '&end_date=' + encodeURIComponent(mr.end) + '&';
            qs += 'include_zero=1&';
            // currency selection
            try { if (currencySel && currencySel.value) qs += 'currency=' + encodeURIComponent(currencySel.value) + '&'; } catch(e) {}
            try { var g3 = document.getElementById('balanzaGroup'); if (g3 && g3.value === 'mayor') qs += 'only_mayor=1&'; } catch(e) {}
            // start DB-backed job
            fetch(base_url + 'contabilidad/balanza_pdf_job' + qs, { method: 'GET' })
            .then(function(r){ return r.json(); })
            .then(function(resp){
                if (resp.status === 'accepted') {
                    var statusUrl = resp.status_url;
                    var check = function(){
                        fetch(statusUrl).then(function(r){ return r.json(); }).then(function(s){
                            if (s.status === 'done') {
                                window.open(s.download_url, '_blank');
                                if (s.file_hash) console.info('Report hash:', s.file_hash);
                            }
                            else if (s.status === 'error') { alert('Error generando PDF: ' + (s.error || 'ver logs')); }
                            else { setTimeout(check, 2000); }
                        }).catch(function(e){ console.error(e); setTimeout(check, 3000); });
                    };
                    check();
                } else {
                    alert('No se pudo iniciar la generación en background.');
                }
            }).catch(function(e){ console.error(e); alert('Error iniciando job'); });
        });

        function updateSignaturePreview(role, url) {
            var preview = document.getElementById('firmaPreview_' + role);
            if (!preview) return;
            if (url) {
                preview.innerHTML = '<div style="display:flex; align-items:center; gap:10px;"><img src="' + url + '" style="max-height:60px; max-width:100%; border:1px solid #dee2e6; padding:2px;" /><span class="text-success">Cargada</span></div>';
            } else {
                preview.innerHTML = 'No hay firma cargada.';
            }
        }

        function handleUpload(role) {
            var fileInput = document.getElementById('firmaFile_' + role);
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) { alert('Seleccione un archivo'); return; }
            var f = fileInput.files[0];
            var fd = new FormData();
            fd.append('firma', f);
            fd.append('role', role);
            fetch(base_url + 'contabilidad/upload_signature', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(j){
                if (j.status === 'success') {
                    updateSignaturePreview(role, j.path);
                    fileInput.value = '';
                } else {
                    alert('Error: ' + (j.error || 'upload failed'));
                }
            }).catch(function(e){ console.error(e); alert('Error subiendo firma'); });
        }

        function handleDelete(role) {
            var labels = {
                'contador': 'Contador General',
                'financiero': 'Gerente Financiero',
                'gerente': 'Gerente General'
            };
            if (!confirm('¿Eliminar la firma de ' + labels[role] + '?')) return;
            var body = 'role=' + encodeURIComponent(role);
            fetch(base_url + 'contabilidad/delete_signature', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body
            })
            .then(function(r){ return r.json(); })
            .then(function(j){
                if (j.status === 'success') {
                    updateSignaturePreview(role, '');
                } else {
                    alert('Error: ' + (j.error || 'no se pudo eliminar')); 
                }
            }).catch(function(e){ console.error(e); alert('Error eliminando firma'); });
        }

        ['contador', 'financiero', 'gerente'].forEach(function(role){
            var uploadBtnRole = document.getElementById('uploadFirma_' + role);
            if (uploadBtnRole) {
                uploadBtnRole.addEventListener('click', function(ev){ ev.preventDefault(); handleUpload(role); });
            }
            var deleteBtnRole = document.getElementById('deleteFirma_' + role);
            if (deleteBtnRole) {
                deleteBtnRole.addEventListener('click', function(ev){ ev.preventDefault(); handleDelete(role); });
            }
        });

        // set default month (current)
        if (monthInput && !monthInput.value) {
            var d = new Date();
            var ym = d.getFullYear() + '-' + (('0'+(d.getMonth()+1)).slice(-2));
            monthInput.value = ym;
        }
        // initial load
        load();
    });
})();
