(function(){
  function fetchUsers(){
    fetch(BASE + 'administracion/usuarios_json')
      .then(r=>r.json())
      .then(d=>{
        const tbody = document.querySelector('#tblUsuarios tbody');
        tbody.innerHTML = '';
        (d.data||[]).forEach(row=>{
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${row.id}</td>
            <td>${row.username||''}</td>
            <td>${row.email||''}</td>
            <td>${row.first_name||''}</td>
            <td>${row.last_name||''}</td>
            <td>${row.grupos||''}</td>
            <td><span class="badge ${row.active==1?'badge-success':'badge-secondary'}">${row.active==1?'Sí':'No'}</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary act-edit" data-id="${row.id}" data-row='${JSON.stringify(row)}'>Editar</button>
              <button class="btn btn-sm btn-outline-${row.active==1?'warning':'success'} act-toggle" data-id="${row.id}" data-active="${row.active==1?0:1}">${row.active==1?'Desactivar':'Activar'}</button>
              <button class="btn btn-sm btn-outline-danger act-reset" data-id="${row.id}">Reset Pass</button>
            </td>`;
          tbody.appendChild(tr);
        });
      });
  }

  function serializeForm(){
    const id = document.getElementById('usr_id').value;
    const active = document.getElementById('usr_active').checked ? 1 : 0;
    let groupsRaw = document.getElementById('usr_groups').value.trim();
    let group_ids = [];
    if(groupsRaw){ group_ids = groupsRaw.split(',').map(s=>parseInt(s.trim())).filter(n=>!isNaN(n)); }
    return {
      id: id,
      username: document.getElementById('usr_username').value.trim(),
      email: document.getElementById('usr_email').value.trim(),
      first_name: document.getElementById('usr_first').value.trim(),
      last_name: document.getElementById('usr_last').value.trim(),
      password: document.getElementById('usr_pass').value,
      active: active,
      'group_ids': group_ids
    };
  }

  document.addEventListener('click', function(e){
    if(e.target.id === 'btnNuevo'){
      document.getElementById('frmUsuario').reset();
      document.getElementById('usr_id').value = '';
      document.getElementById('usr_active').checked = true;
      document.getElementById('modalUsuarioTitle').innerText = 'Nuevo Usuario';
      $('#modalUsuario').modal('show');
    }
    if(e.target.id === 'btnRefrescar'){
      fetchUsers();
    }
    if(e.target.id === 'btnGuardar'){
      const payload = serializeForm();
      const form = new FormData();
      if(payload.id) form.append('id', payload.id);
      form.append('username', payload.username);
      form.append('email', payload.email);
      form.append('first_name', payload.first_name);
      form.append('last_name', payload.last_name);
      if(payload.password) form.append('password', payload.password);
      form.append('active', payload.active);
      (payload['group_ids']||[]).forEach(v=> form.append('group_ids[]', v));
      fetch(BASE + 'administracion/usuario_save', { method: 'POST', body: form })
      .then(r=>r.json()).then(res=>{
        if(res.ok){ $('#modalUsuario').modal('hide'); fetchUsers(); }
        else { alert(res.msg||'Error al guardar'); }
      });
    }
    if(e.target.classList.contains('act-edit')){
      const row = JSON.parse(e.target.getAttribute('data-row'));
      document.getElementById('usr_id').value = row.id;
      document.getElementById('usr_username').value = row.username||'';
      document.getElementById('usr_email').value = row.email||'';
      document.getElementById('usr_first').value = row.first_name||'';
      document.getElementById('usr_last').value = row.last_name||'';
      document.getElementById('usr_pass').value = '';
      document.getElementById('usr_active').checked = (row.active==1);
      document.getElementById('usr_groups').value = '';
      document.getElementById('modalUsuarioTitle').innerText = 'Editar Usuario';
      $('#modalUsuario').modal('show');
    }
    if(e.target.classList.contains('act-toggle')){
      const id = e.target.getAttribute('data-id');
      const active = e.target.getAttribute('data-active');
      const form = new FormData();
      form.append('active', active);
      fetch(BASE + 'administracion/usuario_toggle/' + id, { method: 'POST', body: form })
        .then(r=>r.json()).then(_=> fetchUsers());
    }
    if(e.target.classList.contains('act-reset')){
      const id = e.target.getAttribute('data-id');
      document.getElementById('reset_id').value = id;
      document.getElementById('reset_new').value = '';
      $('#modalReset').modal('show');
    }
    if(e.target.id === 'btnDoReset'){
      const id = document.getElementById('reset_id').value;
      const newp = document.getElementById('reset_new').value;
      const form = new FormData();
      form.append('id', id);
      if(newp) form.append('new_password', newp);
      fetch(BASE + 'administracion/usuario_reset_password', { method: 'POST', body: form })
        .then(r=>r.json()).then(res=>{
          $('#modalReset').modal('hide');
          if(res.ok){
            if(res.new_password){
              alert('Nueva contraseña: ' + res.new_password);
            }
            fetchUsers();
          } else {
            alert('No se pudo resetear');
          }
        });
    }
  });

  function init(){ fetchUsers(); }
  if(document.readyState !== 'loading') init();
  else document.addEventListener('DOMContentLoaded', init);
})();
