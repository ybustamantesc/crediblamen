document.addEventListener('DOMContentLoaded', function(){
  function byId(id){return document.getElementById(id);} 
  function showAlert(msg, type){ alert(msg); }

  async function fetchClosed(){
    try{
      const res = await fetch(window.CIERRE_LIST_URL);
      const txt = await res.text();
      let json = null; try{ json = JSON.parse(txt); }catch(e){ console.error('Non-json',txt); showAlert('Error de servidor: '+txt,'danger'); return; }
      if(json.status !== 'success'){ showAlert(json.message || 'Error','danger'); return; }
      const tbody = document.querySelector('#closedPeriodsTable tbody'); tbody.innerHTML = '';
      json.data.forEach(function(r){
        const tr = document.createElement('tr');
        tr.innerHTML = '<td>'+r.year+'</td><td>'+r.month+'</td><td>'+(r.closed_by||'')+'</td><td>'+ (r.closed_at||'') +'</td><td>'+ (r.notes||'') +'</td><td><button class="btn btn-sm btn-outline-danger btn-unlock">Desbloquear</button></td>';
        tr.querySelector('.btn-unlock').addEventListener('click', function(){ unlockPeriod(r.year, r.month); });
        tbody.appendChild(tr);
      });
    }catch(err){ console.error(err); showAlert('Error al listar periodos: '+err.message); }
  }

  async function closePeriod(){
    const year = byId('cierreYear').value; const month = byId('cierreMonth').value; const notes = byId('cierreNotes').value;
    if(!year || !month){ showAlert('Año o mes inválido'); return; }
    try{
      const fd = new FormData(); fd.append('year', year); fd.append('month', month); fd.append('notes', notes);
      const res = await fetch(window.CIERRE_CLOSE_URL, {method:'POST', body:fd});
      const txt = await res.text(); let json=null; try{json=JSON.parse(txt);}catch(e){ showAlert('Error servidor: '+txt); return; }
      if(json.status==='success'){ showAlert('Periodo cerrado'); fetchClosed(); } else { showAlert(json.message || 'Error'); }
    }catch(err){ showAlert('Error: '+err.message); }
  }

  async function unlockPeriod(year, month){
    if(!confirm('Desbloquear periodo '+month+'/'+year+' ?')) return;
    try{
      const fd = new FormData(); fd.append('year', year); fd.append('month', month);
      const res = await fetch(window.CIERRE_OPEN_URL, {method:'POST', body:fd});
      const txt = await res.text(); let json=null; try{json=JSON.parse(txt);}catch(e){ showAlert('Error servidor: '+txt); return; }
      if(json.status==='success'){ showAlert('Periodo desbloqueado'); fetchClosed(); } else { showAlert(json.message || 'Error'); }
    }catch(err){ showAlert('Error: '+err.message); }
  }

  byId('btnClosePeriod').addEventListener('click', closePeriod);
  byId('btnRefreshPeriods').addEventListener('click', fetchClosed);
  fetchClosed();
});
