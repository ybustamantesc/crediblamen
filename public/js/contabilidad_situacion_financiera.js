(function() {
    'use strict';

    var base_url = window.base_url || 'http://localhost/Servicredit/';
    
    // Elements
    var mesSelect = document.getElementById('mes_select');
    var anioSelect = document.getElementById('anio_select');
    var btnRefreshMensual = document.getElementById('btn_refresh_mensual');
    var btnRefreshAnual = document.getElementById('btn_refresh_anual');
    var btnExportMensual = document.getElementById('btn_export_mensual');
    var btnExportAnual = document.getElementById('btn_export_anual');
    var btnPrintMensual = document.getElementById('btn_print_mensual');
    var btnPrintAnual = document.getElementById('btn_print_anual');
    var mensualContent = document.getElementById('mensual_content');
    var anualContent = document.getElementById('anual_content');

    // Load mensual data
    function loadMensual() {
        var mes = mesSelect.value;
        if (!mes) {
            mensualContent.innerHTML = '<div class="alert alert-warning">Seleccione un mes</div>';
            return;
        }

        mensualContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando...</div>';

        fetch(base_url + 'contabilidad/situacion_financiera_mensual?mes=' + encodeURIComponent(mes))
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    renderMensual(data.data, data.fecha);
                } else {
                    mensualContent.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(e => {
                console.error('Error loading mensual:', e);
                mensualContent.innerHTML = '<div class="alert alert-danger">Error de comunicación</div>';
            });
    }

    // Render mensual table
    function renderMensual(data, fecha) {
        var html = '<div class="mb-3"><h6>Al ' + formatDate(fecha) + '</h6></div>';
        html += '<table class="table table-sm table-bordered">';
        html += '<thead class="thead-dark">';
        html += '<tr><th style="width:70%">Cuenta</th><th class="text-right">Monto</th></tr>';
        html += '</thead>';
        html += '<tbody>';

        // ACTIVO - grouped by Estado de Situación Financiera
        html += '<tr class="table-primary"><td colspan="2"><strong>ACTIVO</strong></td></tr>';
        if (data.activo && data.activo.length > 0) {
            data.activo.forEach(function(group) {
                html += '<tr class="table-secondary"><td>' + escapeHtml(group.label) + '</td>';
                html += '<td class="text-right">' + formatNumber(group.total) + '</td></tr>';
            });
        }
        html += '<tr class="font-weight-bold bg-light">';
        html += '<td>Total Activo</td>';
        html += '<td class="text-right">' + formatNumber(data.total_activo) + '</td>';
        html += '</tr>';

        html += '<tr><td colspan="2">&nbsp;</td></tr>';

        // PASIVO Y PATRIMONIO
        html += '<tr class="table-primary"><td colspan="2"><strong>PASIVO Y PATRIMONIO</strong></td></tr>';
        
        // PASIVO
        html += '<tr class="table-secondary"><td colspan="2"><strong>PASIVO</strong></td></tr>';
        if (data.pasivo && data.pasivo.length > 0) {
            data.pasivo.forEach(function(group) {
                html += '<tr><td>' + escapeHtml(group.label) + '</td>';
                html += '<td class="text-right">' + formatNumber(group.total) + '</td></tr>';
            });
        }
        html += '<tr class="font-weight-bold bg-light">';
        html += '<td>Total Pasivo</td>';
        html += '<td class="text-right">' + formatNumber(data.total_pasivo) + '</td>';
        html += '</tr>';

        // PATRIMONIO
        html += '<tr class="table-secondary"><td colspan="2"><strong>PATRIMONIO</strong></td></tr>';
        if (data.patrimonio && data.patrimonio.length > 0) {
            data.patrimonio.forEach(function(group) {
                html += '<tr><td>' + escapeHtml(group.label) + '</td>';
                html += '<td class="text-right">' + formatNumber(group.total) + '</td></tr>';
            });
        }
        html += '<tr class="font-weight-bold bg-light">';
        html += '<td>Total Patrimonio</td>';
        html += '<td class="text-right">' + formatNumber(data.total_patrimonio) + '</td>';
        html += '</tr>';

        // TOTAL PASIVO Y PATRIMONIO
        html += '<tr class="font-weight-bold table-info">';
        html += '<td>Total Pasivo y Patrimonio</td>';
        html += '<td class="text-right">' + formatNumber(data.total_pasivo + data.total_patrimonio) + '</td>';
        html += '</tr>';

        html += '</tbody>';
        html += '</table>';

        mensualContent.innerHTML = html;
    }

    // Load anual data
    function loadAnual() {
        var anio = anioSelect.value;
        if (!anio) {
            anualContent.innerHTML = '<div class="alert alert-warning">Seleccione un año</div>';
            return;
        }

        anualContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando...</div>';

        fetch(base_url + 'contabilidad/situacion_financiera_anual?anio=' + encodeURIComponent(anio))
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    renderAnual(data.data);
                } else {
                    anualContent.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(e => {
                console.error('Error loading anual:', e);
                anualContent.innerHTML = '<div class="alert alert-danger">Error de comunicación</div>';
            });
    }

    // Render anual table
    function renderAnual(data) {
        var meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        var html = '<div class="mb-3"><h6>Año ' + data.anio + '</h6></div>';
        html += '<div style="overflow-x:auto;">';
        html += '<table class="table table-sm table-bordered" style="min-width:1200px;">';
        html += '<thead class="thead-dark">';
        html += '<tr><th style="min-width:200px;">Cuenta</th>';
        
        meses.forEach(function(mes) {
            html += '<th class="text-right" style="min-width:90px; background-color:#007bff; color:#fff;">' + mes + '</th>';
        });
        
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        if (data.cuentas && data.cuentas.length > 0) {
            data.cuentas.forEach(function(cuenta) {
                html += '<tr>';
                html += '<td>' + escapeHtml(cuenta.nombre) + '</td>';
                
                for (var m = 1; m <= 12; m++) {
                    var valor = cuenta.meses[m] || 0;
                    html += '<td class="text-right">' + formatNumber(valor) + '</td>';
                }
                
                html += '</tr>';
            });
        } else {
            html += '<tr><td colspan="13" class="text-center text-muted">No hay datos disponibles</td></tr>';
        }

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        anualContent.innerHTML = html;
    }

    // Export functions
    function exportMensual() {
        var mes = mesSelect.value;
        if (!mes) {
            alert('Seleccione un mes');
            return;
        }
        window.location = base_url + 'contabilidad/situacion_financiera_export_mensual?mes=' + encodeURIComponent(mes);
    }

    function exportAnual() {
        var anio = anioSelect.value;
        if (!anio) {
            alert('Seleccione un año');
            return;
        }
        window.location = base_url + 'contabilidad/situacion_financiera_export_anual?anio=' + encodeURIComponent(anio);
    }

    // Print functions
    function printMensual() {
        var mes = mesSelect.value;
        if (!mes) {
            alert('Seleccione un mes');
            return;
        }
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title> </title>');
        printWindow.document.write('<style>@page{size:letter;margin:0.75in 0.5in 0.75in 0.5in;}body{font-family:Arial,sans-serif;font-size:7pt;margin:0;padding:0;}h2{font-size:10pt;margin:8px 0 4px 0;text-align:center;}h6{font-size:8pt;margin:4px 0;text-align:center;}table{width:100%;border-collapse:collapse;font-size:7pt;margin-bottom:20px;}th{padding:2px 4px;text-align:left;}td{padding:2px 4px;text-align:left;border-bottom:1px dotted #666;}td.last{border-bottom:1px dotted #666;} .text-right{text-align:right;}.font-weight-bold{font-weight:bold;} .table-primary td{background-color:#dfe6f0;font-weight:bold;} .table-secondary td{background-color:#e9ecef;font-weight:bold;} .table-info td{background-color:#d1ecf1;font-weight:bold;} .bg-light{background-color:#f8f9fa;} .firmas-container{margin-top:40px;width:100%;text-align:center;page-break-inside:avoid;} .firmas-row{display:table;width:100%;} .firma{display:inline-block;width:45%;text-align:center;} .firma-linea{border-top:1px solid #000;width:200px;margin:0 auto 5px auto;} .firma-texto{font-size:8pt;font-weight:bold;} button{display:block;margin:20px auto;} @media print{button{display:none;}}</style>');
        printWindow.document.write('</head><body onload="window.print();">');
        printWindow.document.write('<h2>ESTADO DE SITUACIÓN FINANCIERA</h2>');
        printWindow.document.write(mensualContent.innerHTML);
        printWindow.document.write('<div class="firmas-container">');
        printWindow.document.write('<div class="firma"><div class="firma-linea"></div><div class="firma-texto">Contador General</div></div>');
        printWindow.document.write('<div style="width:10%;display:inline-block;"></div>');
        printWindow.document.write('<div class="firma"><div class="firma-linea"></div><div class="firma-texto">Gerente General</div></div>');
        printWindow.document.write('</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
    }

    function printAnual() {
        var anio = anioSelect.value;
        if (!anio) {
            alert('Seleccione un año');
            return;
        }
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Estado de Situación Financiera - Consolidado Anual</title>');
        printWindow.document.write('<style>@page{size:letter landscape;margin:0.4in;}body{font-family:Arial,sans-serif;font-size:6pt;margin:0;}h2{font-size:9pt;margin:6px 0;text-align:center;}h6{font-size:7pt;margin:3px 0;text-align:center;}table{width:100%;border-collapse:collapse;font-size:6pt;}th{padding:1px 2px;text-align:left;}td{padding:1px 2px;text-align:left;border-bottom:1px dotted #666;} .text-right{text-align:right;}.font-weight-bold{font-weight:bold;}.table-primary td{background-color:#dfe6f0;font-weight:bold;}.table-secondary td{background-color:#e9ecef;font-weight:bold;}.table-info td{background-color:#d1ecf1;font-weight:bold;}@media print{button{display:none;}}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>ESTADO DE SITUACIÓN FINANCIERA - CONSOLIDADO ANUAL</h2>');
        printWindow.document.write(anualContent.innerHTML);
        printWindow.document.write('<br><button onclick="window.print();">Imprimir</button>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        setTimeout(function() { printWindow.print(); }, 500);
    }

    // Utility functions
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Event listeners
    if (btnRefreshMensual) btnRefreshMensual.addEventListener('click', loadMensual);
    if (btnRefreshAnual) btnRefreshAnual.addEventListener('click', loadAnual);
    if (btnExportMensual) btnExportMensual.addEventListener('click', exportMensual);
    if (btnExportAnual) btnExportAnual.addEventListener('click', exportAnual);
    if (btnPrintMensual) btnPrintMensual.addEventListener('click', printMensual);
    if (btnPrintAnual) btnPrintAnual.addEventListener('click', printAnual);

    // Auto-load mensual on page load
    if (mesSelect && mesSelect.value) {
        loadMensual();
    }

})();
