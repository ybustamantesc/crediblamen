(function(){
  function $(sel){ return document.querySelector(sel); }
  function formatMoney(v){ return Number(v).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); }

  function render(data){
    var tbody = document.querySelector('#tbl_balance tbody');
    tbody.innerHTML = '';
    if (!data || !data.groups) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay datos</td></tr>';
      return;
    }
    // ACTIVO
    if (data.groups.activo && data.groups.activo.length){
      var trh = document.createElement('tr'); trh.innerHTML = '<td colspan="4" style="font-weight:bold;">ACTIVO</td>'; tbody.appendChild(trh);
      data.groups.activo.forEach(function(r){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>Activo</td><td>'+ (r.code||'') +'</td><td>'+ (r.name||'') +'</td><td class="text-right">'+ formatMoney(r.display) +'</td>';
        tbody.appendChild(tr);
      });
    }
    // PASIVO
    if (data.groups.pasivo && data.groups.pasivo.length){
      var trh2 = document.createElement('tr'); trh2.innerHTML = '<td colspan="4" style="font-weight:bold;">PASIVO</td>'; tbody.appendChild(trh2);
      data.groups.pasivo.forEach(function(r){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>Pasivo</td><td>'+ (r.code||'') +'</td><td>'+ (r.name||'') +'</td><td class="text-right">'+ formatMoney(Math.abs(r.display)) +'</td>';
        tbody.appendChild(tr);
      });
    }
    // PATRIMONIO
    if (data.groups.patrimonio && data.groups.patrimonio.length){
      var trh3 = document.createElement('tr'); trh3.innerHTML = '<td colspan="4" style="font-weight:bold;">PATRIMONIO</td>'; tbody.appendChild(trh3);
      data.groups.patrimonio.forEach(function(r){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>Patrimonio</td><td>'+ (r.code||'') +'</td><td>'+ (r.name||'') +'</td><td class="text-right">'+ formatMoney(Math.abs(r.display)) +'</td>';
        tbody.appendChild(tr);
      });
    }

    // totals
    document.getElementById('total_activo').textContent = formatMoney(data.totals.activo || 0);
    document.getElementById('total_pasivo').textContent = formatMoney(data.totals.pasivo || 0);
    document.getElementById('total_patrimonio').textContent = formatMoney(data.totals.patrimonio || 0);
    document.getElementById('total_pasivo_patrimonio').textContent = formatMoney(data.totals.pasivo_patrimonio || 0);
  }

  function fetchAndRender(){
    var as_of = document.getElementById('balance_as_of').value;
    var url = base_url + 'contabilidad/balance_data' + (as_of ? '?as_of='+encodeURIComponent(as_of) : '');
    fetch(url).then(function(resp){ return resp.json(); }).then(function(json){
      if (json && json.status === 'success') render(json.data);
      else { console.error('balance_data error', json); }
    }).catch(function(err){ console.error('fetch error', err); });
  }

  document.addEventListener('DOMContentLoaded', function(){
    // base_url is expected to be available globally by the app; otherwise derive
    if (typeof base_url === 'undefined') {
      window.base_url = window.location.origin + '/servicredit/';
    }

    var btn = document.getElementById('btn_balance_refresh');
    if (btn) btn.addEventListener('click', function(e){ fetchAndRender(); });

    var btnCsv = document.getElementById('btn_balance_csv');
    if (btnCsv) btnCsv.addEventListener('click', function(){
      var as_of = document.getElementById('balance_as_of').value;
      var href = base_url + 'contabilidad/balance_export' + (as_of ? '?as_of='+encodeURIComponent(as_of) : '');
      window.location = href;
    });

    var btnPrint = document.getElementById('btn_balance_print');
    if (btnPrint) btnPrint.addEventListener('click', function(){
      var as_of = document.getElementById('balance_as_of').value;
      var href = base_url + 'contabilidad/balance_print' + (as_of ? '?as_of='+encodeURIComponent(as_of) : '');
      window.open(href, '_blank');
    });

    var btnPdf = document.getElementById('btn_balance_pdf');
    if (btnPdf) btnPdf.addEventListener('click', function(){
      var as_of = document.getElementById('balance_as_of').value;
      var href = base_url + 'contabilidad/balance_pdf' + (as_of ? '?as_of='+encodeURIComponent(as_of) : '');
      // open in new tab to let browser handle download
      window.open(href, '_blank');
    });

    // initial load
    fetchAndRender();
  });
})();
