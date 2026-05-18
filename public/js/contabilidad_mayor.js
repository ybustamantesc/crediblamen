(function(){
    // contabilidad_mayor.js - simple client for Libro Mayor
    document.addEventListener('DOMContentLoaded', function(){
        var accountSelect = document.getElementById('mayorAccount');
        var startInput = document.getElementById('mayorStart');
        var endInput = document.getElementById('mayorEnd');
        var refresh = document.getElementById('mayorRefresh');
        var tbody = document.querySelector('#mayorTable tbody');
        var perPageSelect = document.getElementById('mayorPerPage');
        var prevBtn = document.getElementById('mayorPrev');
        var nextBtn = document.getElementById('mayorNext');
        var currentPage = 1;

        function fetchAccounts(){
            fetch(base_url + 'contabilidad/accounts')
            .then(function(r){ return r.json(); })
            .then(function(data){
                accountSelect.innerHTML = '';
                var opt = document.createElement('option'); opt.value=''; opt.text = '-- Seleccione cuenta --'; accountSelect.appendChild(opt);
                data.data.forEach(function(a){
                    // ocultar cuenta de ajustes 9999 para que no pueda seleccionarse en Libro Mayor
                    if (a.code && a.code.toString().trim() === '9999') return;
                    var o = document.createElement('option'); o.value = a.id; o.text = a.code + ' - ' + a.name; accountSelect.appendChild(o);
                });
            }).catch(function(e){ console.error('mayor: accounts', e); });
        }

        function renderLedger(result){
            tbody.innerHTML = '';
            if (!result || !result.entries) return;
            // show opening balance row if any
            var opening = parseFloat(result.opening_balance || 0);
            if (opening !== 0) {
                var tr0 = document.createElement('tr');
                tr0.innerHTML = '<td></td><td></td><td><strong>Saldo inicial</strong></td><td></td><td></td><td class="text-right">' + opening.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td><td></td>';
                tbody.appendChild(tr0);
            }
            result.entries.forEach(function(e){
                var tr = document.createElement('tr');
                var debit = parseFloat(e.debit || 0);
                var credit = parseFloat(e.credit || 0);
                var rb = (e.running_balance !== undefined) ? parseFloat(e.running_balance) : 0;
                var rb_abs = (e.running_abs !== undefined) ? parseFloat(e.running_abs) : Math.abs(rb);
                var side = (e.side !== undefined) ? e.side : (rb >= 0 ? 'Deudor' : 'Acreedor');
                var rbClass = '';
                if (rb < 0) rbClass = 'text-danger';
                var deudorCell = (side === 'Deudor') ? rb_abs.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00';
                var acreedorCell = (side === 'Acreedor') ? rb_abs.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00';
                tr.innerHTML = '<td>' + (e.date || '') + '</td>' +
                               '<td><button class="btn btn-link btn-sm mayor-open-journal" data-id="' + e.journal_id + '">' + e.journal_id + '</button></td>' +
                                '<td>' + (e.description || '') + '</td>' +
                                '<td class="text-right">' + debit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>' +
                                '<td class="text-right">' + credit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>' +
                                '<td class="text-right ' + rbClass + '">' + deudorCell + '</td>' +
                                '<td class="text-right ' + rbClass + '">' + acreedorCell + '</td>';
                tbody.appendChild(tr);
            });
            // update footer with paging info if present
            var footer = document.getElementById('mayorFooter');
            if (footer) {
                var total = result.total || 0; var page = result.page || 1; var per = result.per_page || 0;
                var start = ((page-1)*per) + 1; var end = Math.min(total, page*per);
                if (total === 0) footer.innerText = 'Sin movimientos.'; else footer.innerText = 'Mostrando ' + start + ' - ' + end + ' de ' + total + ' registros. Página ' + page + '.';
            }

            // delegate click to open modal_view
            var modalContainer = document.getElementById('modalContainer');
                tbody.querySelectorAll('.mayor-open-journal').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var id = this.getAttribute('data-id');
                    if (!id) return;
                    fetch(base_url + 'contabilidad/modal_view?id=' + encodeURIComponent(id))
                    .then(function(r){ return r.text(); })
                    .then(function(html){ if (modalContainer) modalContainer.innerHTML = html; })
                    .catch(function(e){ console.error('open journal modal', e); });
                });
            });
                // add per-row export and print buttons
                tbody.querySelectorAll('tr').forEach(function(row){
                    var jidBtn = row.querySelector('.mayor-open-journal');
                    if (!jidBtn) return;
                    var id = jidBtn.getAttribute('data-id');
                    // create export btn
                    var exportBtn = document.createElement('button'); exportBtn.className = 'btn btn-sm btn-outline-secondary ml-1 mayor-export-journal'; exportBtn.textContent = 'CSV';
                    var printBtnRow = document.createElement('button'); printBtnRow.className = 'btn btn-sm btn-outline-secondary ml-1 mayor-print-journal'; printBtnRow.textContent = 'Imprimir';
                    var td = document.createElement('td'); td.style.whiteSpace = 'nowrap'; td.appendChild(exportBtn); td.appendChild(printBtnRow);
                    row.appendChild(td);

                    exportBtn.addEventListener('click', function(){ window.location = base_url + 'contabilidad/journal_export?id=' + encodeURIComponent(id); });
                    printBtnRow.addEventListener('click', function(){ window.open(base_url + 'contabilidad/journal_print?id=' + encodeURIComponent(id),'_blank'); });
                });
        }

        function loadLedger(){
            var aid = accountSelect.value;
            if (!aid) { tbody.innerHTML = '<tr><td colspan="6">Seleccione una cuenta.</td></tr>'; return; }
            var qs = '?account_id=' + encodeURIComponent(aid);
            if (startInput.value) qs += '&start_date=' + encodeURIComponent(startInput.value);
            if (endInput.value) qs += '&end_date=' + encodeURIComponent(endInput.value);
            var per_page = perPageSelect ? parseInt(perPageSelect.value) : 500;
            qs += '&per_page=' + encodeURIComponent(per_page);
            qs += '&page=' + encodeURIComponent(currentPage);
            fetch(base_url + 'contabilidad/mayor_data' + qs)
            .then(function(r){ return r.json(); })
            .then(function(resp){ if (resp.status === 'success') renderLedger(resp.data); else { tbody.innerHTML = '<tr><td colspan="6">Error al obtener datos.</td></tr>'; console.error('mayor_data error', resp); } })
            .catch(function(e){ tbody.innerHTML = '<tr><td colspan="6">Error de comunicación.</td></tr>'; console.error('mayor_data', e); });
        }

        // events
        refresh.addEventListener('click', function(ev){ ev.preventDefault(); loadLedger(); });
        var exportBtn = document.getElementById('mayorExport');
        var exportAllBtn = document.getElementById('mayorExportAll');
        var printBtn = document.getElementById('mayorPrint');

        exportBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            var aid = accountSelect.value;
            if (!aid) { alert('Seleccione una cuenta.'); return; }
            var qs = '?account_id=' + encodeURIComponent(aid);
            if (startInput.value) qs += '&start_date=' + encodeURIComponent(startInput.value);
            if (endInput.value) qs += '&end_date=' + encodeURIComponent(endInput.value);
            // include pagination params in export as well
            var per_page = perPageSelect ? parseInt(perPageSelect.value) : 500;
            qs += '&per_page=' + per_page + '&page=' + currentPage;
            // trigger download
            window.location = base_url + 'contabilidad/mayor_export' + qs;
        });

        if (exportAllBtn) {
            exportAllBtn.addEventListener('click', function(ev){
                ev.preventDefault();
                
                // Get company info
                var empresaName = document.getElementById('empresa_razon_social') ? document.getElementById('empresa_razon_social').value : 'Empresa';
                
                // Get date filters
                var startDate = startInput.value;
                var endDate = endInput.value;
                
                // Fetch all accounts with their ledgers
                var qs = '?all=1';
                if (startDate) qs += '&start_date=' + encodeURIComponent(startDate);
                if (endDate) qs += '&end_date=' + encodeURIComponent(endDate);
                
                fetch(base_url + 'contabilidad/mayor_export_all_pdf' + qs)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success || !data.accounts) {
                            alert('Error al obtener datos');
                            return;
                        }
                        
                        // Build professional HTML with all accounts
                        var w = window.open('', '_blank');
                        var dateRange = '';
                        if (startDate || endDate) {
                            dateRange = '<div style="text-align:center;margin-bottom:15px;color:#666;font-size:10pt;">Período: ' + (startDate || 'Inicio') + ' al ' + (endDate || 'Hoy') + '</div>';
                        }
                        
                        var html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Libro Mayor - Todas las Cuentas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 20mm 15mm;
            color: #1a1a1a;
            font-size: 10pt;
        }

        // Delegate clicks for server-rendered or dynamic "Ver" / "Imprimir" buttons
        document.addEventListener('click', function(ev){
            try {
                var openBtn = ev.target.closest && ev.target.closest('.mayor-open-journal');
                if (openBtn) {
                    ev.preventDefault();
                    var id = openBtn.getAttribute('data-id') || openBtn.dataset.id;
                    if (!id) return;
                    var modalContainer = document.getElementById('modalContainer');
                    if (!modalContainer) {
                        modalContainer = document.createElement('div');
                        modalContainer.id = 'modalContainer';
                        document.body.appendChild(modalContainer);
                    }
                    fetch(base_url + 'contabilidad/modal_view?id=' + encodeURIComponent(id))
                        .then(function(r){ return r.text(); })
                        .then(function(html){ modalContainer.innerHTML = html; })
                        .catch(function(e){ console.error('open journal modal', e); });
                    return;
                }

                var printBtn = ev.target.closest && ev.target.closest('.mayor-print-journal');
                if (printBtn) {
                    ev.preventDefault();
                    var pid = printBtn.getAttribute('data-id') || printBtn.dataset.id;
                    if (!pid) return;
                    window.open(base_url + 'contabilidad/diario_print?id=' + encodeURIComponent(pid), '_blank');
                    return;
                }
            } catch (err) { console.error(err); }
        });
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
        .account-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .account-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #1F4E78;
        }
        .account-name {
            font-size: 14pt;
            font-weight: 600;
            color: #1F4E78;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        thead {
            background: linear-gradient(135deg, #1F4E78 0%, #2d5f8d 100%);
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th.text-right { text-align: right; }
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e3f2fd;
        }
        td {
            padding: 10px 8px;
            font-size: 9pt;
        }
        td.text-right {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        td.opening-balance {
            font-weight: 700;
            background-color: #fff3cd;
        }
        @media print {
            body { padding: 10mm; }
            .account-section { page-break-inside: avoid; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">${empresaName}</div>
        <div class="print-date">
            ${new Date().toLocaleDateString('es-NI', {year: 'numeric', month: 'long', day: 'numeric'})}
        </div>
    </div>
    
    <div class="report-title">LIBRO MAYOR - TODAS LAS CUENTAS</div>
    ${dateRange}
`;
                        
                        // Process each account
                        data.accounts.forEach(function(account) {
                            html += `
    <div class="account-section">
        <div class="account-info">
            <div class="account-name">${account.code} - ${account.name}</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Fecha</th>
                    <th style="width: 10%;">Asiento</th>
                    <th style="width: 38%;">Descripción</th>
                    <th class="text-right" style="width: 14%;">Debe</th>
                    <th class="text-right" style="width: 14%;">Haber</th>
                    <th class="text-right" style="width: 14%;">Saldo Deudor</th>
                    <th class="text-right" style="width: 14%;">Saldo Acreedor</th>
                </tr>
            </thead>
            <tbody>
`;
                            
                            // Add opening balance if exists
                            if (account.opening_balance && account.opening_balance !== 0) {
                                html += `
                <tr>
                    <td></td>
                    <td></td>
                    <td class="opening-balance"><strong>Saldo inicial</strong></td>
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right opening-balance">${parseFloat(account.opening_balance).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="text-right">0.00</td>
                </tr>
`;
                            }
                            
                            // Add entries
                            account.entries.forEach(function(entry) {
                                var debit = parseFloat(entry.debit) || 0;
                                var credit = parseFloat(entry.credit) || 0;
                                var rb = parseFloat(entry.running_balance) || 0;
                                var side = entry.side || (rb >= 0 ? 'Deudor' : 'Acreedor');
                                var rbAbs = Math.abs(rb);
                                var deudor = side === 'Deudor' ? rbAbs.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00';
                                var acreedor = side === 'Acreedor' ? rbAbs.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00';
                                
                                html += `
                <tr>
                    <td>${entry.date}</td>
                    <td>${entry.journal_id}</td>
                    <td>${entry.description}</td>
                    <td class="text-right">${debit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="text-right">${credit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="text-right">${deudor}</td>
                    <td class="text-right">${acreedor}</td>
                </tr>
`;
                            });
                            
                            html += `
            </tbody>
        </table>
    </div>
`;
                        });
                        
                        html += `
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
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
                        alert('Error al generar el reporte');
                    });
            });
        }

        printBtn.addEventListener('click', function(ev){
            ev.preventDefault();
            
            // Get data
            var title = document.querySelector('.page-header-title h5');
            var empresaName = document.getElementById('empresa_razon_social') ? document.getElementById('empresa_razon_social').value : '';
            
            // Get selected account info
            var selectedAccount = accountSelect.options[accountSelect.selectedIndex];
            var accountName = selectedAccount ? selectedAccount.text : '';
            
            // Get date range
            var dateRange = '';
            if (startInput.value || endInput.value) {
                dateRange = 'Período: ' + (startInput.value || 'Inicio') + ' al ' + (endInput.value || 'Hoy');
            }
            
            // Calculate totals from visible rows
            var totalDebit = 0;
            var totalCredit = 0;
            tbody.querySelectorAll('tr').forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length >= 5) {
                    var debitText = cells[3].textContent.replace(/[^0-9.-]/g, '');
                    var creditText = cells[4].textContent.replace(/[^0-9.-]/g, '');
                    totalDebit += parseFloat(debitText) || 0;
                    totalCredit += parseFloat(creditText) || 0;
                }
            });
            
            // Build professional HTML
            var w = window.open('', '_blank');
            var html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Libro Mayor - ${accountName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 20mm 15mm;
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
            margin: 20px 0 10px 0;
            text-align: center;
        }
        .account-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #1F4E78;
        }
        .account-name {
            font-size: 14pt;
            font-weight: 600;
            color: #1F4E78;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 10pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        thead {
            background: linear-gradient(135deg, #1F4E78 0%, #2d5f8d 100%);
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th.text-right { text-align: right; }
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e3f2fd;
        }
        td {
            padding: 10px 8px;
            font-size: 9pt;
        }
        td.text-right {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        td.opening-balance {
            font-weight: 700;
            background-color: #fff3cd;
        }
        .totals-section {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border: 2px solid #1F4E78;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11pt;
        }
        .totals-row.final {
            font-weight: 700;
            font-size: 12pt;
            color: #1F4E78;
            border-top: 2px solid #1F4E78;
            padding-top: 12px;
            margin-top: 8px;
        }
        .totals-label {
            font-weight: 600;
        }
        .totals-value {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        @media print {
            body { padding: 10mm; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">${empresaName || 'Empresa'}</div>
        <div class="print-date">${new Date().toLocaleDateString('es-NI', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
    </div>
    
    <div class="report-title">LIBRO MAYOR</div>
    
    <div class="account-info">
        <div class="account-name">${accountName}</div>
        ${dateRange ? '<div class="date-range">' + dateRange + '</div>' : ''}
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 10%;">Asiento</th>
                <th style="width: 38%;">Descripción</th>
                <th class="text-right" style="width: 14%;">Debe</th>
                <th class="text-right" style="width: 14%;">Haber</th>
                <th class="text-right" style="width: 14%;">Saldo Deudor</th>
                <th class="text-right" style="width: 14%;">Saldo Acreedor</th>
            </tr>
        </thead>
        <tbody>`;
            
            // Add table rows (skip last column with buttons)
            tbody.querySelectorAll('tr').forEach(function(row) {
                var isOpeningBalance = row.querySelector('strong') && row.querySelector('strong').textContent.includes('Saldo inicial');
                var rowClass = isOpeningBalance ? ' class="opening-balance"' : '';
                html += '<tr' + rowClass + '>';
                var cells = row.querySelectorAll('td');
                // Only include first 7 columns (skip the Acciones column)
                for (var i = 0; i < Math.min(7, cells.length); i++) {
                    var cell = cells[i];
                    // Handle asiento column with button
                    if (i === 1) {
                        var btn = cell.querySelector('button');
                        if (btn) {
                            html += '<td>' + btn.textContent + '</td>';
                        } else {
                            html += '<td>' + cell.textContent + '</td>';
                        }
                    } else {
                        html += '<td' + (cell.className ? ' class="' + cell.className + '"' : '') + '>' + cell.textContent + '</td>';
                    }
                }
                html += '</tr>';
            });
            
            html += `
        </tbody>
    </table>
    
    <div class="totals-section">
        <div class="totals-row">
            <span class="totals-label">Total Debe:</span>
            <span class="totals-value">C$ ${totalDebit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Total Haber:</span>
            <span class="totals-value">C$ ${totalCredit.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
        </div>
        <div class="totals-row final">
            <span class="totals-label">Diferencia:</span>
            <span class="totals-value">C$ ${Math.abs(totalDebit - totalCredit).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>`;
            
            w.document.open();
            w.document.write(html);
            w.document.close();
        });

        // pagination handlers
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function(){ currentPage = 1; loadLedger(); });
        }
        if (prevBtn) prevBtn.addEventListener('click', function(e){ e.preventDefault(); if (currentPage > 1) { currentPage--; loadLedger(); } });
        if (nextBtn) nextBtn.addEventListener('click', function(e){ e.preventDefault(); currentPage++; loadLedger(); });

        // load accounts on start
        fetchAccounts();

    });
})();
