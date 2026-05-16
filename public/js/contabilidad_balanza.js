(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var monthInput = document.getElementById('balanzaMonth');
        var refresh = document.getElementById('balanzaRefresh');
        var tbody = document.querySelector('#balanzaTable tbody');
        var footer = document.getElementById('balanzaFooter');
        var exportBtn = document.getElementById('balanzaExport');
        var exportAllBtn = document.getElementById('balanzaExportAll');

        function format(n){ return parseFloat(n||0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

        function render(data){
            tbody.innerHTML = '';
            if (!data || !data.rows) { tbody.innerHTML = '<tr><td colspan="9">Sin datos</td></tr>'; return; }
            // filter out internal adjustment account 9999 (should also be excluded server-side)
            var rows = data.rows.filter(function(rr){ return (rr.code||'').toString().trim() !== '9999'; });
            if (!rows || rows.length === 0) { tbody.innerHTML = '<tr><td colspan="9">Sin datos</td></tr>'; return; }
            rows.forEach(function(r){
                var tr = document.createElement('tr');
                var closing_deudor = parseFloat(r.closing_deudor || 0);
                var closing_acreedor = parseFloat(r.closing_acreedor || 0);
                var balance_final = closing_deudor - closing_acreedor;
                tr.innerHTML = '<td>' + (r.code||'') + '</td>' +
                               '<td>' + (r.name||'') + '</td>' +
                               '<td class="text-right">' + format(r.opening_deudor || 0) + '</td>' +
                               '<td class="text-right">' + format(r.opening_acreedor || 0) + '</td>' +
                               '<td class="text-right">' + format(r.debits || 0) + '</td>' +
                               '<td class="text-right">' + format(r.credits || 0) + '</td>' +
                               '<td class="text-right">' + format(closing_deudor) + '</td>' +
                               '<td class="text-right">' + format(closing_acreedor) + '</td>' +
                               '<td class="text-right">' + format(balance_final) + '</td>';
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
            var tot_open_deudor = 0, tot_open_acreedor = 0, tot_debits = 0, tot_credits = 0, tot_close_deudor = 0, tot_close_acreedor = 0;
            rows.forEach(function(rr){
                tot_open_deudor += parseFloat(rr.opening_deudor || 0);
                tot_open_acreedor += parseFloat(rr.opening_acreedor || 0);
                tot_debits += parseFloat(rr.debits || 0);
                tot_credits += parseFloat(rr.credits || 0);
                tot_close_deudor += parseFloat(rr.closing_deudor || 0);
                tot_close_acreedor += parseFloat(rr.closing_acreedor || 0);
            });
            document.getElementById('tot_open_deudor').innerText = format(tot_open_deudor);
            document.getElementById('tot_open_acreedor').innerText = format(tot_open_acreedor);
            document.getElementById('tot_debits').innerText = format(tot_debits);
            document.getElementById('tot_credits').innerText = format(tot_credits);
            document.getElementById('tot_close_deudor').innerText = format(tot_close_deudor);
            document.getElementById('tot_close_acreedor').innerText = format(tot_close_acreedor);
            document.getElementById('tot_balance_final').innerText = format(tot_close_deudor - tot_close_acreedor);
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
        if (pdfBtn) pdfBtn.addEventListener('click', function(ev){ ev.preventDefault(); var qs = '?'; var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null); if (mr) qs += 'start_date=' + encodeURIComponent(mr.start) + '&end_date=' + encodeURIComponent(mr.end) + '&'; qs += 'include_zero=1&'; window.open(base_url + 'contabilidad/balanza_pdf' + qs, '_blank'); });
        var pdfBgBtn = document.getElementById('balanzaPdfBg');
        if (pdfBgBtn) pdfBgBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var qs = '?';
            var mr = monthRange(monthInput && monthInput.value ? monthInput.value : null);
            if (mr) qs += 'start_date=' + encodeURIComponent(mr.start) + '&end_date=' + encodeURIComponent(mr.end) + '&';
            qs += 'include_zero=1&';
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

        // Upload signature handler
        var uploadBtn = document.getElementById('uploadFirma');
        if (uploadBtn) uploadBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var fileInput = document.getElementById('firmaFile');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) { alert('Seleccione un archivo'); return; }
            var f = fileInput.files[0];
            var fd = new FormData(); fd.append('firma', f);
            fetch(base_url + 'contabilidad/upload_signature', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); }).then(function(j){
                if (j.status === 'success') {
                    document.getElementById('firmaPreview').innerHTML = '<img src="' + j.path + '" style="max-height:40px;" /> Subido';
                } else {
                    alert('Error: ' + (j.error || 'upload failed'));
                }
            }).catch(function(e){ console.error(e); alert('Error subiendo firma'); });
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
