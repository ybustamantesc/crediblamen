document.addEventListener('DOMContentLoaded', function(){
    function fmt(v){
        return (typeof v === 'number') ? v.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) : v;
    }

    function loadIndicators(){
        fetch(base_url + 'estadisticas/indicators_json')
            .then(function(r){ return r.json(); })
            .then(function(data){
                document.getElementById('stat-revenue').innerText = '$ ' + fmt(data.revenue || 0);
                document.getElementById('stat-expenses').innerText = '$ ' + fmt(data.expenses || 0);
                document.getElementById('stat-net').innerText = '$ ' + fmt(data.net || 0);
                document.getElementById('stat-loans').innerText = (data.total_loans || 0);
            }).catch(function(err){ console && console.error && console.error(err); });
    }

    // Detail buttons
    document.body.addEventListener('click', function(e){
        var target = e.target;
        if (target && target.dataset && target.dataset.metric){
            e.preventDefault();
            var metric = target.dataset.metric;
            var modal = $('#metricModal');
            $('#metricModalLabel').text('Detalle: ' + metric);
            $('#metricDetails').html('Cargando...');
            modal.modal('show');
            fetch(base_url + 'estadisticas/metric_details/' + metric)
                .then(function(r){ return r.json(); })
                .then(function(rows){
                    if (!rows || rows.length === 0) return $('#metricDetails').html('<p>No hay datos.</p>');
                    var html = '<div class="table-responsive"><table class="table table-sm">';
                    // build headers from first row
                    html += '<thead><tr>';
                    Object.keys(rows[0]).forEach(function(k){ html += '<th>' + k + '</th>'; });
                    html += '</tr></thead><tbody>';
                    rows.forEach(function(r){ html += '<tr>'; Object.keys(r).forEach(function(k){ html += '<td>' + r[k] + '</td>'; }); html += '</tr>'; });
                    html += '</tbody></table></div>';
                    $('#metricDetails').html(html);
                }).catch(function(err){ $('#metricDetails').html('<p>Error cargando datos.</p>'); console && console.error && console.error(err); });
        }
    });

    // global base_url provided by header view; fallback
    if (typeof base_url === 'undefined') window.base_url = function(p){ return (p || ''); };

    loadIndicators();
});
