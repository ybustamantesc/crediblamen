$(document).ready(function () {
    console.log('reportes/utils.js loaded');
    $('.select2').select2();
    $("#tablaCreditos").DataTable();
    //listar('');
    listarCuotasEstado();
    listarCreditoClientes();
    listarPlanPagoCliente("");
    // If inputs are native date fields, set value as YYYY-MM-DD; otherwise init datetimepicker
    if ($('#fechaInicio').length && $('#fechaInicio').attr('type') === 'date') {
        try {
            if (!$('#fechaInicio').val()) {
                $('#fechaInicio').val(new Date().toISOString().slice(0, 10));
            }
        } catch (e) {
            console.log('set fechaInicio failed', e);
        }
    } else {
        $('#fechaInicio').datetimepicker({
            defaultDate: new Date(),
            format: 'DD/MM/YYYY'
        });
    }
    //$('.select2').select2();
    if ($('#fechFin').length && $('#fechFin').attr('type') === 'date') {
        try {
            if (!$('#fechFin').val()) {
                $('#fechFin').val(new Date().toISOString().slice(0, 10));
            }
        } catch (e) {
            console.log('set fechFin failed', e);
        }
    } else {
        $('#fechFin').datetimepicker({
            defaultDate: new Date(),
            format: 'DD/MM/YYYY'
        });
    }

    $('#btnConsultar').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let idcliente = $("#cliente").val();
        let idasesor = $("#asesor").val();
        if (idcliente == '') {
            alert("Seleccionar un cliente");
            return false;
        }
        if (idasesor == '') {
            alert("Seleccionar un asesor");
            return false;
        }
        if (fechaInicio == '' || fechaFin == '') {
            alert('Seleccionar las fechas');
            return false;
        } else {
            listar(fechaInicio, fechaFin, idcliente, idasesor);
        }
    });

    $('#btnConsultarAsesorEstado').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let estado = $("#estado").val();
        let idasesor = $("#asesorestado").val();
        if (fechaInicio == '' || fechaFin == '' || idasesor == '' || estado == '') {
            alert('Seleccionar los criterios de búsqueda');
            return false;
        } else {

            listarCreditosAsesorEstado(fechaInicio, fechaFin, idasesor, estado);
        }
    });
    $('#btnPdfAsesorEstado').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let estado = $("#estado").val();
        let idasesor = $("#asesorestado").val();
        if (fechaInicio == '' || fechaFin == '' || idasesor == '' || estado == '') {
            alert('Seleccionar los criterios de búsqueda');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfCreditosAsesorEstado/' + fechaInicio + '/' + fechaFin + '/' + idasesor + '/' + estado);
        }
    });
    $('#cboClientePlanPago').on('change', function () {
        let id = $(this).val();
        listarPlanPagoCliente(id);
    });
    $('#cboClienteEstadoCuenta').on('change', function () {
        let id = $(this).val();
        listarEstadoCuentaCliente(id);
    });
    $('#fechaCuota').on('change', function () {
        let idCliente = $("#cboClienteECF").val();
        let fechaCuota = $(this).val();
        listarEstadoCuentaClienteFecha(idCliente, fechaCuota);
    });

    $('#pagosAsesor').on('change', function () {
        let idasesor = $(this).val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        listarPagosAsesorFechas(idasesor, fechaInicio, fechaFin);
    });
    $('#cboCliente').on('change', function () {
        let id = $(this).val();
        listarCreditoClientes(id);
    });
    $('#cboAsesor').on('change', function () {
        let id = $(this).val();
        listarCreditosAsesor(id);
    });
    $('#CboEstado').on('change', function () {
        let id = $(this).val();
        listarCuotasEstado(id);
    });
    $('#btnPdf').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let idcliente = $("#cliente").val();
        let idasesor = $("#asesor").val();
        if (fechaInicio == '' || fechaFin == '') {
            alert('Seleccionar las fechas');
            return false;
        } else {
            // ensure dates are YYYY-MM-DD
            function _toYMD(d) {
                if (!d) return d;
                if (d.indexOf('/') !== -1) {
                    let parts = d.split('/');
                    if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
                if (d.indexOf('-') !== -1) {
                    let parts = d.split('-');
                    if (parts[0].length === 4) return d;
                    if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
                return d;
            }
            let fi = _toYMD(fechaInicio);
            let ff = _toYMD(fechaFin);
            window.open(base_url + 'reporte/pdffechas/' + fi + '/' + ff + '/' + idcliente + '/' + idasesor);
        }
    });
    // Open debug JSON in a new tab for quick inspection
    $('#btnDebugJson').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let idcliente = $("#cliente").val();
        let idasesor = $("#asesor").val();
        function _toYMD(d) {
            if (!d) return d;
            if (d.indexOf('/') !== -1) {
                let parts = d.split('/');
                if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            if (d.indexOf('-') !== -1) {
                let parts = d.split('-');
                if (parts[0].length === 4) return d;
                if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            return d;
        }
        let fi = _toYMD(fechaInicio);
        let ff = _toYMD(fechaFin);
        let url = base_url + 'debug_getCreditoFechas_json.php?fechaInicio=' + encodeURIComponent(fi) + '&fechaFin=' + encodeURIComponent(ff) + '&idcliente=' + encodeURIComponent(idcliente) + '&idasesor=' + encodeURIComponent(idasesor) + '&debug=1';
        console.log('Opening debug URL:', url);
        window.open(url, '_blank');
    });
    // Excel export
    $('#btnExportExcel').on('click', function () {
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        let idcliente = $("#cliente").val();
        let idasesor = $("#asesor").val();
        function _toYMD(d) {
            if (!d) return d;
            if (d.indexOf('/') !== -1) {
                let parts = d.split('/');
                if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            if (d.indexOf('-') !== -1) {
                let parts = d.split('-');
                if (parts[0].length === 4) return d;
                if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            return d;
        }
        let fi = _toYMD(fechaInicio);
        let ff = _toYMD(fechaFin);
        let url = base_url + 'reporte/excelfechas/' + encodeURIComponent(fi) + '/' + encodeURIComponent(ff) + '/' + encodeURIComponent(idcliente) + '/' + encodeURIComponent(idasesor);
        console.log('Opening Excel URL:', url);
        window.open(url, '_blank');
    });
    $('#btnExportarPdf').on('click', function () {
        let estado = $("#CboEstado").val();
        if (estado == '') {
            alert('Seleccione un estado');
        } else {
            window.open(base_url + 'cuotas/pdfEstado/' + estado);
        }
    });
    $('#btnPdfCliente').on('click', function () {
        let id = $("#cboCliente").val();
        if (id == '') {
            alert('Seleccionar el cliente');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfCreditosCliente/' + id);
        }
    });
    $('#btnPdfAsesor').on('click', function () {
        let id = $("#cboAsesor").val();
        if (id == '') {
            alert('Seleccionar el Asesor');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfCreditosAsesor/' + id);
        }
    });
    $('#btnConsultarAsesorPagos').on('click', function () {
        let idasesor = $("#pagosAsesor").val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        listarPagosAsesorFechas(idasesor, fechaInicio, fechaFin);
    });
    $('#btnConsultarPagosCliente').on('click', function () {
        let idCliente = $("#cboClientePago").val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        listarPagosClienteFechas(idCliente, fechaInicio, fechaFin);
    });
    $('#btnConsultarPagosEstado').on('click', function () {
        let estado = $("#cboPagosEstados").val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        console.log(fechaFin)
        listarPagosEstadoFechas(estado, fechaInicio, fechaFin);
    });
    $('#btnPdfPagosAsesor').on('click', function () {
        let idasesor = $("#pagosAsesor").val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        if (idasesor == '') {
            alert('Seleccionar el Asesor');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfPagosAsesor/' + idasesor + '/' + fechaInicio + '/' + fechaFin);
        }
    });
    $('#btnPdfPagosCliente').on('click', function () {
        let idcliente = $("#cboClientePago").val();

        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        if (idcliente == '') {
            alert('Seleccionar el Cliente');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfClientePagos/' + idcliente + '/' + fechaInicio + '/' + fechaFin);
        }
    });
    $('#btnPdfPagosEstados').on('click', function () {
        let estado = $("#cboPagosEstados").val();
        let fechaInicio = $("#fechaInicio").val();
        let fechaFin = $("#fechFin").val();
        if (estado == '') {
            alert('Seleccionar el Estado');
            return false;
        } else {
            window.open(base_url + 'reporte/pdfPagosEstado/' + estado + '/' + fechaInicio + '/' + fechaFin);
        }
    });

    // Auto-consultar si ya hay cliente y fechas al cargar la página
    try {
        let autoCliente = $('#cliente').val();
        let autoFechaInicio = $('#fechaInicio').val();
        let autoFechaFin = $('#fechFin').val();
        let autoAsesor = $('#asesor').val();
        if (autoCliente && autoFechaInicio && autoFechaFin) {
            // convertir si viene en formato DD/MM/YYYY a YYYY-MM-DD
            function convertToYMD(d) {
                if (!d) return d;
                if (d.indexOf('/') !== -1) {
                    let parts = d.split('/');
                    if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
                if (d.indexOf('-') !== -1) {
                    // ya puede venir como YYYY-MM-DD
                    let parts = d.split('-');
                    if (parts[0].length === 4) return d;
                    // si viene como DD-MM-YYYY
                    if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
                return d;
            }

            let fi = convertToYMD(autoFechaInicio);
            let ff = convertToYMD(autoFechaFin);
            listar(fi, ff, autoCliente, autoAsesor);
        }
    } catch (e) {
        console.log('Auto consultar falló', e);
    }


});

function listarPagosEstadoFechas(estado, fechaInicio, fechaFin) {
    $('#tablaPagosEstado').DataTable({
        ajax: {
            url: base_url + "reporte/getPagosEstadoFechas",
            data: {fechaInicio: fechaInicio, fechaFin: fechaFin, estado: estado},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "idcredito"
            },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fecha_cuota"
            },
            {
                data: "numero_cuota"
            },
            {
                data: "monto_cuota"
            },
            {
                data: "fecha_pago"
            },
            {
                data: "monto_pendiente"
            },
            {
                data: "monto_pagado"
            },
            {
                data: "estado"
            }
        ],
        "bSort": false,
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarPagosClienteFechas(idCliente, fechaInicio, fechaFin) {
    $('#tablaPagosClientes').DataTable({
        ajax: {
            url: base_url + "reporte/getPagosCliente",
            data: {fechaInicio: fechaInicio, fechaFin: fechaFin, idcliente: idCliente},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "idcredito"
            },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fecha_cuota"
            },
            {
                data: "numero_cuota"
            },
            {
                data: "monto_cuota"
            },
            {
                data: "monto_pagado"
            },

            {
                data: "monto_pendiente"
            },
            {
                data: "fecha_pago"
            },
            {
                data: "estado"
            }
        ],
        "bSort": false,
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarPagosAsesorFechas(idAsesor, fechaInicio, fechaFin) {
    $('#tablaPagosAsesor').DataTable({
        ajax: {
            url: base_url + "reporte/pagosasesor",
            data: {fechaInicio: fechaInicio, fechaFin: fechaFin, idasesor: idAsesor},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fecha_pago"
            },
            {
                data: "monto_pago"
            },
            {
                data: "descuento_pago"
            }
        ],
        "bSort": false,
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listar(fechaInicio, fechaFin, idCliente, idAsesor) {
    // convert dates to YYYY-MM-DD if needed
    function _toYMD(d) {
        if (!d) return d;
        if (d.indexOf('/') !== -1) {
            let parts = d.split('/');
            if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        if (d.indexOf('-') !== -1) {
            // if already YYYY-MM-DD, keep
            let parts = d.split('-');
            if (parts[0].length === 4) return d;
            if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return d;
    }

    let fi = _toYMD(fechaInicio);
    let ff = _toYMD(fechaFin);

    // Use debug public endpoint (avoids auth redirect) and show Saldo (totalPagar - totalPagado)
    function initTableWithData(dataArray) {
        // Ensure table header shows exact titles and hide any extra columns
        var desiredTitles = ['Orden','Nombre del Cliente','No Credito','Fecha de Credito','Monto de Credito','Porcentaje Interes','No Cuotas','Monto Interes','Total a Pagar','Estado','Monto Pagado','Saldo Total'];
        var $ths = $('#tablaCreditos thead th');
        // If the table has a thead, adjust its th texts and hide extras. If it's too short, append missing ths.
        if (!$ths || !$ths.length) {
            $('#tablaCreditos thead').html('<tr></tr>');
            $ths = $('#tablaCreditos thead th');
        }
        if ($ths.length < desiredTitles.length) {
            var need = desiredTitles.length - $ths.length;
            for (var a=0;a<need;a++) $('#tablaCreditos thead tr').append('<th></th>');
            $ths = $('#tablaCreditos thead th');
        }
        $ths.each(function(idx){
            if (idx < desiredTitles.length) {
                $(this).text(desiredTitles[idx]);
                $(this).show();
            } else {
                $(this).text('');
                $(this).hide();
            }
        });

        // Build columns array matching existing header count; hide extra columns
        var nTh = ($ths && $ths.length) ? Math.max($ths.length, desiredTitles.length) : desiredTitles.length;
        var cols = [];
        for (var i=0;i<nTh;i++){
            if (i === 0) cols.push({data: 'id', title: desiredTitles[0]});
            else if (i === 1) cols.push({data: 'nombreCliente', title: desiredTitles[1]});
            else if (i === 2) cols.push({data: 'idCredito', title: desiredTitles[2]});
            else if (i === 3) cols.push({data: 'fechaCredito', title: desiredTitles[3]});
            else if (i === 4) cols.push({data: 'montoCredito', title: desiredTitles[4]});
            else if (i === 5) cols.push({data: 'interes', title: desiredTitles[5]});
            else if (i === 6) cols.push({data: 'coutas', title: desiredTitles[6]});
            else if (i === 7) cols.push({data: 'totalInteres', title: desiredTitles[7]});
            else if (i === 8) cols.push({data: 'totalPagar', title: desiredTitles[8]});
            else if (i === 9) cols.push({data: 'estado_credito', title: desiredTitles[9]});
            else if (i === 10) cols.push({data: 'montoPagado', title: desiredTitles[10]});
            else if (i === 11) cols.push({data: 'saldoTotal', title: desiredTitles[11]});
            else cols.push({data: null, visible: false});
        }

        $('#tablaCreditos').DataTable({
            data: dataArray,
            columns: cols,
            columnDefs: [
                { targets: 9, render: function(data, type, row) {
                        // If server returned HTML badge, use it
                        if (row.estado && row.estado.indexOf('<') !== -1) return row.estado;
                        var st = row.estado_credito || row.estado || row.estado_final || '';
                        if (!st) return '';
                        // Map state to badge class
                        var cls = 'badge badge-secondary';
                        var s = st.toString().toUpperCase();
                        if (s.indexOf('VIGENTE') === 0) cls = 'badge badge-info';
                        if (s.indexOf('VIGENTE EN MORA') === 0) cls = 'badge badge-warning';
                        if (s.indexOf('EN MORA') === 0) cls = 'badge badge-danger';
                        if (s.indexOf('VENCIDO') === 0) cls = 'badge badge-dark';
                        if (s.indexOf('INCOBRABLE') === 0) cls = 'badge badge-danger';
                        if (s.indexOf('SANEADO') === 0) cls = 'badge badge-success';
                        return '<span class="' + cls + ' mr-2 mb-1">' + st + '</span>';
                    }
                },
                { targets: '_all', className: 'dt-body-left' }
            ],
            bSort: false,
            bDestroy: true,
            deferRender: true,
            processing: true,
            autoWidth: false,
            language: {processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'},
            responsive: true,
            pagingType: $(window).width() < 768 ? 'simple' : 'simple_numbers'
        });
    }

    // Directly call the debug public endpoint to avoid auth/session redirects
    $.getJSON(base_url + 'debug_getCreditoFechas_json.php', {fechaInicio: fi, fechaFin: ff, idcliente: idCliente, idasesor: idAsesor}, function (resp) {
        var rows = [];
        if (!resp) rows = [];
        else if (resp.data) rows = resp.data;
        else rows = resp;
        // Ensure each row has saldoTotal; if not, compute fallback from totalPagar - montoPagado
        for (var r=0;r<rows.length;r++) {
            var row = rows[r];
            if (!('saldoTotal' in row)) {
                var tp = parseFloat((row.totalPagar || row.total_pagar || 0)) || 0;
                var mp = parseFloat((row.montoPagado || row.totalPagado || row.total_pagado || 0)) || 0;
                row.saldoTotal = tp - mp;
                // format as string with 2 decimals
                row.saldoTotal = row.saldoTotal.toFixed(2);
            }
        }
        initTableWithData(rows);
    }).fail(function () {
        console.error('Debug endpoint failed');
        initTableWithData([]);
    });
}

function listarCreditoClientes(clienteid) {
    $('#tablaCreditosCliente').DataTable({
        ajax: {
            url: base_url + "reporte/getCreditosCliente",
            data: {clienteid: clienteid},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "cliente"
            },
            {
                data: "idCredito"
            },
            {
                data: "fechaCredito"
            },
            {
                data: "montoCredito"
            },
            {
                data: "interes"
            },
            {
                data: "comisionDesembolso"
            },
            {
                data: "coutas"
            },
            {
                data: "totalInteres"
            },
            {
                data: "totalPagar"
            },
            {
                data: "totalPagado"
            },
            {
                data: "formaPago"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarPlanPagoCliente(clienteid) {
    $('#tablaPlanPago').DataTable({
        ajax: {
            url: base_url + "reporte/getPlanPago",
            data: {idcliente: clienteid},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fechaCredito"
            },
            {
                data: "montoCredito"
            },
            {
                data: "interes"
            },
            {
                data: "comisionDesembolso"
            },
            {
                data: "cuotas"
            },
            {
                data: "totalInteres"
            },
            {
                data: "totalPagar"
            },
            {
                data: "totalPagado"
            },
            {
                data: "formaPago"
            },
            {
                data: "button"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarCreditosAsesor(idasesor) {
    $('#tablaCreditosAsesor').DataTable({
        ajax: {
            url: base_url + "reporte/getCreditosAsesor",
            data: {idasesor: idasesor},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "cliente"
            },
            {
                data: "idCredito"
            },
            {
                data: "fechaCredito"
            },
            {
                data: "montoCredito"
            },
            {
                data: "interes"
            },
            {
                data: "comisionDesembolso"
            },
            {
                data: "coutas"
            },
            {
                data: "totalInteres"
            },
            {
                data: "totalPagar"
            },
            {
                data: "totalPagado"
            },
            {
                data: "formaPago"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarCuotasEstado(estado) {
    $('#tablCuotasEstado').DataTable({
        ajax: {
            url: base_url + "cuotas/getCuotasEstado",
            data: {estado: estado},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "cliente"
        },
            {
                data: "asesor"
            },
            {
                data: "numero_couta"
            },
            {
                data: "fecha_couta"
            },
            {
                data: "fecha_pago"
            },
            {
                data: "monto_pagado"
            },
            {
                data: "monto_couta"
            },
            {
                data: "monto_pendiente"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarEstadoCuentaCliente(id) {
    $('#tablaEstadoCuentaCliente').DataTable({
        ajax: {
            url: base_url + "reporte/getEstadoCuentaCliente",
            data: {clienteid: id},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "idcredito"
            },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fechaCuota"
            },
            {
                data: "numerCuota"
            },
            {
                data: "montoCuota"
            },
            {
                data: "montoPendiente"
            },
            {
                data: "fechaPago"
            },
            {
                data: "montoPago"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarEstadoCuentaClienteFecha(id, fecha) {
    $('#tblEstadoCuentaClienteFechaPago').DataTable({
        ajax: {
            url: base_url + "reporte/getEstadoCuentaClienteFecha",
            data: {clienteid: id, fecha: fecha},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "idcredito"
            },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fechaCuota"
            },
            {
                data: "numerCuota"
            },
            {
                data: "montoCuota"
            },
            {
                data: "montoPendiente"
            },
            {
                data: "fechaPago"
            },
            {
                data: "montoPago"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarCreditosAsesorEstado(fechaInicio, fechaFin, idAsesor, estado) {
    $('#tablaCreditosAsesorEstado').DataTable({
        ajax: {
            url: base_url + "reporte/getCreditosFechasAsesorEstado",
            data: {fechaInicio: fechaInicio, fechaFin: fechaFin, idasesor: idAsesor, estado: estado},
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [
            {
                data: "id"
            },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "idCredito"
            },
            {
                data: "fechaCredito"
            },
            {
                data: "montoCredito"
            },
            {
                data: "interes"
            },
            {
                data: "coutas"
            },
            {
                data: "totalInteres"
            },
            {
                data: "totalPagar"
            },
            {
                data: "formaPago"
            },
            {
                data: "estado"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}

function listarPlanPago() {
    $('#tablaPlanPago').DataTable({
        ajax: {
            url: base_url + "reporte/getPlanPago",
            type: 'POST',
            async: true,
            dataType: 'json',
            error: function (e) {
                console.log(e);
            }
        },
        columns: [{
            data: "id"
        },
            {
                data: "cliente"
            },
            {
                data: "asesor"
            },
            {
                data: "fechaCredito"
            },
            {
                data: "montoCredito"
            },
            {
                data: "interes"
            },
            {
                data: "cuotas"
            },
            {
                data: "totalInteres"
            },
            {
                data: "totalPagar"
            },
            {
                data: "formaPago"
            },
            {
                data: "button"
            }
        ],
        "bDestroy": true,
        "deferRender": true,
        "processing": true,
        "autoWidth": false,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        },
        "responsive": true,
        "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers",
    });
}