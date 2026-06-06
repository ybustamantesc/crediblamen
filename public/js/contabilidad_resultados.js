/* contabilidad_resultados.js
   Fetch and render Estado de Resultados, wire exports
*/
(function(){
    function fmt(n){
        return Number(n).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
    }

    function getParams(){
        // new: single month selector (YYYY-MM) -> compute start/end
        var m = document.getElementById('resMonth').value;
        var ac = document.getElementById('resAcumulado') ? document.getElementById('resAcumulado').checked : false;
        var currency = document.getElementById('resCurrency') ? document.getElementById('resCurrency').value : 'local';
        if(!m) return null;
        var parts = m.split('-');
        if(parts.length !== 2) return null;
        var y = parts[0], mm = parts[1];
        var start = y + '-' + mm + '-01';
        // compute last day of month
        var last = new Date(y, parseInt(mm), 0).getDate();
        var end = y + '-' + mm + '-' + (last < 10 ? '0' + last : last);
        return {start: start, end: end, acumulado: ac, year: y, month: mm, currency: currency};
    }

    function load(){
        var params = getParams();
        if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') {
            window.base_url = window.location.origin + '/servicredit/';
        }
        var urlMonth = base_url + 'contabilidad/resultados_data?start_date='+encodeURIComponent(params.start)+'&end_date='+encodeURIComponent(params.end)+'&currency='+encodeURIComponent(params.currency);

        if (params.acumulado) {
            // fetch both month and acumulado (year start -> month end) in parallel
            var startAc = params.year + '-01-01';
            var urlAcum = base_url + 'contabilidad/resultados_data?start_date='+encodeURIComponent(startAc)+'&end_date='+encodeURIComponent(params.end)+'&currency='+encodeURIComponent(params.currency);
            Promise.all([fetch(urlMonth).then(function(r){return r.json();}), fetch(urlAcum).then(function(r){return r.json();})])
            .then(function(results){
                var m = results[0], a = results[1];
                if (!m || !a) { console.error('Empty response(s)'); alert('Error cargando datos.'); return; }
                if ((m.status && m.status !== 'success') || (a.status && a.status !== 'success')) { console.error('API error', m, a); alert('Error cargando datos.'); return; }
                render({current: m.data, acumulado: a.data});
            }).catch(function(err){ console.error(err); alert('Error cargando datos.'); });
            return;
        }

        // default: single month
        fetch(urlMonth).then(function(res){ return res.json(); }).then(function(json){
            if (!json) { console.error('Empty response'); alert('Error cargando datos.'); return; }
            if (json.status && json.status !== 'success') { console.error('API error', json); alert('Error cargando datos.'); return; }
            render(json.data);
        }).catch(function(err){
            console.error(err); alert('Error cargando datos.');
        });
    }

    function render(data){
        var body = document.getElementById('resReportBody');
        if (!body) return; // nothing to render
        body.innerHTML = '';
        // set year from month selector and show if acumulado
        var mval = document.getElementById('resMonth') ? document.getElementById('resMonth').value : '';
        var acChecked = document.getElementById('resAcumulado') ? document.getElementById('resAcumulado').checked : false;
        var currency = document.getElementById('resCurrency') ? document.getElementById('resCurrency').value : 'local';
        var currencyLabel = (currency === 'usd') ? 'Dólares' : 'Córdobas';
        var year = '';
        if (mval) { var p = mval.split('-'); if(p.length===2) year = p[0]; }
        var ry = document.getElementById('reportYear'); if(ry) ry.textContent = (acChecked && year ? ('Acumulado ' + year + ' - ' + currencyLabel) : (year ? (year + ' - ' + currencyLabel) : currencyLabel));

        // If server returned the structured estado de resultados, render by grupos
        // Support combined rendering when `data` contains `{ current:..., acumulado:... }`
        var isCombined = data && data.current && data.acumulado;
        if (isCombined) {
            // render with two amount columns (mes / acumulado)
            var cur = data.current, ac = data.acumulado;

            function renderDualSection(title, itemsCur, itemsAcum, totalCur, totalAcum) {
                var t = document.createElement('div'); t.className = 'r-row r-section';
                t.innerHTML = '<div class="desc">' + title + '</div><div class="amt">Mes</div><div class="amt">Acumulado</div>';
                body.appendChild(t);

                // build a map by name for acumulado for quick lookup
                var mapA = {};
                if (itemsAcum && itemsAcum.length) itemsAcum.forEach(function(it){ mapA[(it.nombre||'').toString()] = it; });

                if (itemsCur && itemsCur.length) {
                    itemsCur.forEach(function(it){
                        var name = it.nombre || '';
                        var amtCur = Number(it.monto || 0);
                        var aIt = mapA[name];
                        var amtA = aIt ? Number(aIt.monto || 0) : 0;

                        var rr = document.createElement('div'); rr.className = 'r-row';
                        var left = document.createElement('div'); left.className = 'desc'; left.textContent = name;
                        var rightCur = document.createElement('div'); rightCur.className = 'amt'; rightCur.textContent = Number(amtCur).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
                        var rightAc = document.createElement('div'); rightAc.className = 'amt'; rightAc.textContent = Number(amtA).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});

                        rr.appendChild(left); rr.appendChild(rightCur); rr.appendChild(rightAc); body.appendChild(rr);
                    });
                }

                // totals row
                var tr = document.createElement('div'); tr.className = 'r-row r-total';
                tr.innerHTML = '<div class="desc">' + title + ' - Total</div><div class="amt">' + Number(totalCur||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div><div class="amt">' + Number(totalAcum||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div>';
                body.appendChild(tr);
                var gap = document.createElement('div'); gap.style.height = '4px'; body.appendChild(gap);
            }

            // render each logical section if present in current
            if (cur.ingresos_financieros) renderDualSection('Ingresos financieros por:', cur.ingresos_financieros, ac.ingresos_financieros, cur.total_ingresos_financieros, ac.total_ingresos_financieros);
            if (cur.gastos_financieros) renderDualSection('Gastos financieros por:', cur.gastos_financieros, ac.gastos_financieros, cur.total_gastos_financieros, ac.total_gastos_financieros);

            // Margen financiero bruto
            var mfb = document.createElement('div'); mfb.className = 'r-row r-total'; mfb.innerHTML = '<div class="desc">Margen Financiero Bruto</div><div class="amt">' + Number(cur.margen_financiero_bruto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div><div class="amt">' + Number(ac.margen_financiero_bruto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div>';
            body.appendChild(mfb);

            // Provisiones
            if (cur.provisiones) renderDualSection('Gasto por provisión e incobrabilidad de la cartera de créditos directa', cur.provisiones, ac.provisiones, cur.total_provisiones, ac.total_provisiones);

            // Margen financiero neto
            var mfn = document.createElement('div'); mfn.className = 'r-row r-total'; mfn.innerHTML = '<div class="desc">Margen Financiero Neto</div><div class="amt">' + Number(cur.margen_financiero_neto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div><div class="amt">' + Number(ac.margen_financiero_neto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div>';
            body.appendChild(mfn);

            // Operativos
            if (cur.ingresos_operativos || cur.gastos_operativos) renderDualSection('Operativos', cur.ingresos_operativos||[], ac.ingresos_operativos||[], cur.total_ingresos_operativos, ac.total_ingresos_operativos);
            if (cur.gastos_operativos) renderDualSection('Gastos operativos diversos', cur.gastos_operativos, ac.gastos_operativos, cur.total_gastos_operativos, ac.total_gastos_operativos);

            var rob = document.createElement('div'); rob.className = 'r-row r-total'; rob.innerHTML = '<div class="desc">Resultado operativo bruto</div><div class="amt">' + Number(cur.resultado_operativo_bruto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div><div class="amt">' + Number(ac.resultado_operativo_bruto||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '</div>';
            body.appendChild(rob);

            // Totals for footer: compute month and acumulado totals
            var tot_ing_cur = (cur.total_ingresos_financieros || 0) + (cur.total_ingresos_operativos || 0);
            var tot_gas_cur = (cur.total_gastos_financieros || 0) + (cur.total_provisiones || 0) + (cur.total_gastos_operativos || 0) + (cur.total_gastos_administracion || 0) + (cur.total_impuesto || 0);

            var tot_ing_ac = (ac.total_ingresos_financieros || 0) + (ac.total_ingresos_operativos || 0);
            var tot_gas_ac = (ac.total_gastos_financieros || 0) + (ac.total_provisiones || 0) + (ac.total_gastos_operativos || 0) + (ac.total_gastos_administracion || 0) + (ac.total_impuesto || 0);

            document.getElementById('tot_ingresos').textContent = Number(tot_ing_cur).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '  /  ' + Number(tot_ing_ac).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
            document.getElementById('tot_gastos').textContent = Number(tot_gas_cur).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '  /  ' + Number(tot_gas_ac).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
            document.getElementById('res_operativo').textContent = Number(tot_ing_cur - tot_gas_cur).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + '  /  ' + Number(tot_ing_ac - tot_gas_ac).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
            return;
        }

        if (data && data.ingresos_financieros) {
            // helper to render a section title and items
            function renderSection(title, items, boldNames) {
                var t = document.createElement('div'); t.className = 'r-row r-section';
                t.innerHTML = '<div class="desc">' + title + '</div><div class="amt"></div>';
                body.appendChild(t);
                if (items && items.length) {
                    items.forEach(function(it){
                        var rr = document.createElement('div'); rr.className = 'r-row';
                        var left = document.createElement('div'); left.className = 'desc'; left.textContent = it.nombre || '';
                        var right = document.createElement('div'); right.className = 'amt'; right.textContent = fmt(it.monto || 0);
                        if (boldNames && (it.is_total || /total/i.test(it.nombre))) { rr.className = 'r-row r-total'; left.style.fontWeight = '700'; }
                        rr.appendChild(left); rr.appendChild(right); body.appendChild(rr);
                    });
                }
                // small gap
                var gap = document.createElement('div'); gap.style.height = '4px'; body.appendChild(gap);
            }

            renderSection('Ingresos financieros por:', data.ingresos_financieros, false);
            var tr = document.createElement('div'); tr.className = 'r-row r-total'; tr.innerHTML = '<div class="desc">Total Ingresos Financieros</div><div class="amt">' + fmt(data.total_ingresos_financieros || 0) + '</div>';
            body.appendChild(tr);

            renderSection('Gastos financieros por:', data.gastos_financieros, false);
            var tg = document.createElement('div'); tg.className = 'r-row r-total'; tg.innerHTML = '<div class="desc">Total Gastos Financieros</div><div class="amt">' + fmt(data.total_gastos_financieros || 0) + '</div>';
            body.appendChild(tg);

            // Margen financiero bruto
            var mfb = document.createElement('div'); mfb.className = 'r-row r-total'; mfb.innerHTML = '<div class="desc">Margen Financiero Bruto</div><div class="amt">' + fmt(data.margen_financiero_bruto || 0) + '</div>';
            body.appendChild(mfb);

            // Provisiones
            renderSection('Gasto por provisión e incobrabilidad de la cartera de créditos directa', data.provisiones, false);
            var prov = document.createElement('div'); prov.className = 'r-row r-total'; prov.innerHTML = '<div class="desc">Total Provisiones</div><div class="amt">' + fmt(data.total_provisiones || 0) + '</div>';
            body.appendChild(prov);

            // Margen financiero neto
            var mfn = document.createElement('div'); mfn.className = 'r-row r-total'; mfn.innerHTML = '<div class="desc">Margen Financiero Neto</div><div class="amt">' + fmt(data.margen_financiero_neto || 0) + '</div>';
            body.appendChild(mfn);

            // Operativos
            renderSection('Ingresos operativos diversos', data.ingresos_operativos, false);
            renderSection('Gastos operativos diversos', data.gastos_operativos, false);

            var rob = document.createElement('div'); rob.className = 'r-row r-total'; rob.innerHTML = '<div class="desc">Resultado operativo bruto</div><div class="amt">' + fmt(data.resultado_operativo_bruto || 0) + '</div>';
            body.appendChild(rob);

            if (data.participacion_asociadas && data.participacion_asociadas.length) renderSection('Participación en resultados de asociadas', data.participacion_asociadas, false);

            renderSection('Gastos de administración', data.gastos_administracion, false);
            var rat = document.createElement('div'); rat.className = 'r-row r-total'; rat.innerHTML = '<div class="desc">Resultado antes del impuesto a la renta</div><div class="amt">' + fmt(data.resultado_antes_impuesto || 0) + '</div>';
            body.appendChild(rat);

            if (data.impuesto_renta && data.impuesto_renta.length) renderSection('Impuesto a la renta', data.impuesto_renta, false);

            var re = document.createElement('div'); re.className = 'r-row r-total'; re.innerHTML = '<div class="desc">Resultado del ejercicio</div><div class="amt">' + fmt(data.resultado_ejercicio || 0) + '</div>';
            body.appendChild(re);

            // Compute summary totals for the footer
            var tot_ing = (data.total_ingresos_financieros || 0) + (data.total_ingresos_operativos || 0);
            var tot_gas = (data.total_gastos_financieros || 0) + (data.total_provisiones || 0) + (data.total_gastos_operativos || 0) + (data.total_gastos_administracion || 0) + (data.total_impuesto || 0);
            document.getElementById('tot_ingresos').textContent = fmt(tot_ing);
            document.getElementById('tot_gastos').textContent = fmt(tot_gas);
            document.getElementById('res_operativo').textContent = fmt((tot_ing - tot_gas));
            return;
        }

        // Fallback: if old flat rows format present
        if(!data || !data.rows || data.rows.length === 0) {
            body.innerHTML = '<div class="text-muted">No hay datos</div>';
            document.getElementById('tot_ingresos').textContent = fmt(0);
            document.getElementById('tot_gastos').textContent = fmt(0);
            document.getElementById('res_operativo').textContent = fmt(0);
            return;
        }

        var prevSection = '';
        data.rows.forEach(function(r){
            // Insert section header when it changes
            if (r.section && r.section !== prevSection) {
                var sec = document.createElement('div');
                sec.className = 'r-row r-section';
                sec.innerHTML = '<div class="desc">'+(r.section||'')+'</div><div class="amt"></div>';
                body.appendChild(sec);
                prevSection = r.section;
            }

            var rr = document.createElement('div'); rr.className = 'r-row';
            var left = document.createElement('div'); left.className = 'desc'; left.textContent = (r.name || '');
            var right = document.createElement('div'); right.className = 'amt'; right.textContent = fmt(r.display || 0);

            // heuristics for totals: explicit flag or name contains 'total'
            if (r.is_total || (r.name && /total/i.test(r.name))) {
                rr.className = 'r-row r-total';
                left.style.fontWeight = '700';
            }

            rr.appendChild(left); rr.appendChild(right); body.appendChild(rr);
        });

        // compute totals (prefer controller-provided totals)
        var tot_ing = 0, tot_gas = 0;
        if (data.totals) {
            tot_ing = typeof data.totals.total_ingresos !== 'undefined' ? data.totals.total_ingresos : 0;
            tot_gas = typeof data.totals.total_gastos !== 'undefined' ? data.totals.total_gastos : 0;
        } else {
            data.rows.forEach(function(r){ if (r.type === 'ingreso') tot_ing += Number(r.display || 0); if (r.type === 'gasto') tot_gas += Number(r.display || 0); });
        }
        document.getElementById('tot_ingresos').textContent = fmt(tot_ing);
        document.getElementById('tot_gastos').textContent = fmt(tot_gas);
        document.getElementById('res_operativo').textContent = fmt(tot_ing - tot_gas);
    }

    function exportCsv(){
        var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') { window.base_url = window.location.origin + '/servicredit/'; }
        var q = '?start_date='+encodeURIComponent(params.start)+'&end_date='+encodeURIComponent(params.end);
        if (params.acumulado) q += '&acumulado=1';
        q += '&currency='+encodeURIComponent(params.currency);
        window.location = base_url + 'contabilidad/resultados_export' + q;
    }

    function exportExcel(){
        var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') { window.base_url = window.location.origin + '/servicredit/'; }
        var q = '?start_date='+encodeURIComponent(params.start)+'&end_date='+encodeURIComponent(params.end);
        if (params.acumulado) q += '&acumulado=1';
        q += '&currency='+encodeURIComponent(params.currency);
        // Use location to trigger download of generated XLSX
        window.location = base_url + 'contabilidad/resultados_pdf' + q;
    }

    function exportPdfReal(){
        var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') { window.base_url = window.location.origin + '/servicredit/'; }
        var q = '?start_date='+encodeURIComponent(params.start)+'&end_date='+encodeURIComponent(params.end);
        if (params.acumulado) q += '&acumulado=1';
        q += '&currency='+encodeURIComponent(params.currency);
        window.open(base_url + 'contabilidad/resultados_pdf_real' + q, '_blank');
    }

    document.addEventListener('DOMContentLoaded', function(){
        var btn = document.getElementById('resRefresh');
        if(btn) btn.addEventListener('click', load);
        var bexcel = document.getElementById('resExcel'); if(bexcel) bexcel.addEventListener('click', exportExcel);
        var bpdfreal = document.getElementById('resPdfReal'); if(bpdfreal) bpdfreal.addEventListener('click', exportPdfReal);
        // set default month to current
        var msel = document.getElementById('resMonth');
        if(msel && !msel.value){
            var d = new Date();
            var ym = d.getFullYear() + '-' + ("0" + (d.getMonth()+1)).slice(-2);
            msel.value = ym;
        }
        // Auto-load for default month
        if(msel && msel.value) load();
    });

})();
