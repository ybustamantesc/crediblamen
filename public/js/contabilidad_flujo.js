(function(){
    function fmt(n){ return Number(n).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function getParams(){
        var start = document.getElementById('flujoStart').value;
        var end = document.getElementById('flujoEnd').value;
        if(!start || !end) return null; return {start:start,end:end};
    }

    function load(){
        var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') window.base_url = window.location.origin + '/servicredit/';
        var url = base_url + 'contabilidad/flujo_data?start_date=' + encodeURIComponent(params.start) + '&end_date=' + encodeURIComponent(params.end);
        fetch(url).then(function(r){ return r.json(); }).then(function(json){
            if (!json || json.status !== 'success') { console.error('API error', json); alert('Error cargando datos.'); return; }
            render(json.data);
        }).catch(function(err){ console.error(err); alert('Error cargando datos.'); });
    }

    function render(data){
        var tbody = document.querySelector('#flujoTable tbody'); tbody.innerHTML = '';
        if (!data || !data.rows || data.rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay movimientos en el periodo</td></tr>';
            setTotals(data ? data.totals : null);
            return;
        }
        data.rows.forEach(function(r){
            var tr = document.createElement('tr');
            var tdDate = document.createElement('td'); tdDate.textContent = r.date;
            var tdJ = document.createElement('td'); tdJ.textContent = r.journal_id;
            var tdDesc = document.createElement('td'); tdDesc.textContent = r.description;
            var tdCat = document.createElement('td'); tdCat.textContent = r.category;
            var tdAmt = document.createElement('td'); tdAmt.className = 'text-right'; tdAmt.textContent = fmt(r.amount || 0);
            tr.appendChild(tdDate); tr.appendChild(tdJ); tr.appendChild(tdDesc); tr.appendChild(tdCat); tr.appendChild(tdAmt);
            tbody.appendChild(tr);
        });
        setTotals(data.totals);
    }

    function setTotals(t){
        t = t || {};
        document.getElementById('tot_colecciones').textContent = fmt(t.colecciones_creditos || 0);
        document.getElementById('tot_intereses').textContent = fmt(t.intereses_comisiones || 0);
        document.getElementById('tot_desembolsos').textContent = fmt(Math.abs(t.desembolsos_creditos || 0));
        document.getElementById('tot_pagos').textContent = fmt(Math.abs(t.pagos_operativos || 0));
        document.getElementById('tot_financiacion').textContent = fmt(t.financiacion || 0);
        document.getElementById('tot_neto').textContent = fmt(t.neto || 0);
    }

    function exportCsv(){
        var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
        if (typeof base_url === 'undefined') window.base_url = window.location.origin + '/servicredit/';
        window.location = base_url + 'contabilidad/flujo_export?start_date=' + encodeURIComponent(params.start) + '&end_date=' + encodeURIComponent(params.end);
    }

    document.addEventListener('DOMContentLoaded', function(){
        var btn = document.getElementById('flujoRefresh'); if (btn) btn.addEventListener('click', load);
        var bex = document.getElementById('flujoExport'); if (bex) bex.addEventListener('click', exportCsv);
        var bpdf = document.getElementById('flujoPdf'); if (bpdf) bpdf.addEventListener('click', function(){
            var params = getParams(); if(!params) return alert('Seleccione rango de fechas válidas.');
            if (typeof base_url === 'undefined') window.base_url = window.location.origin + '/servicredit/';
            window.open(base_url + 'contabilidad/flujo_pdf?start_date=' + encodeURIComponent(params.start) + '&end_date=' + encodeURIComponent(params.end), '_blank');
        });
    });
})();
