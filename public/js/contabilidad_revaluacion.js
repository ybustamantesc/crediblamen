document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('revalForm');
  const previewBtn = document.getElementById('previewBtn');
  const executeBtn = document.getElementById('executeBtn');
  const status = document.getElementById('revalStatus');
  const result = document.getElementById('revalResult');

  function postReval(execute) {
    status.innerText = 'Procesando...';
    const data = new FormData(form);
    data.append('execute', execute ? '1' : '0');

    const url = window.REVAL_EXECUTE_URL || (window.location.origin + '/contabilidad/revaluacion_execute');
    fetch(url, {
      method: 'POST',
      body: data
    }).then(r => {
      if (!r.ok) {
        return r.text().then(txt => { throw new Error('HTTP ' + r.status + ': ' + txt); });
      }
      return r.text().then(txt => {
        try {
          return JSON.parse(txt);
        } catch (e) {
          throw new Error('Respuesta no JSON: ' + txt);
        }
      });
    }).then(j => {
      if (!j) { status.innerText = 'Respuesta inválida'; return; }
      if (j.status === 'error') {
        status.innerText = 'Error: ' + j.message;
      } else {
        status.innerText = j.message || 'OK';
        if (j.run_id) {
          const link = document.createElement('a');
          link.href = (window.location.origin + '/contabilidad/revaluacion_run/' + j.run_id);
          link.innerText = 'Ver run #' + j.run_id;
          link.className = 'ms-3';
          status.appendChild(link);
        }
        result.innerHTML = '<pre>' + JSON.stringify(j, null, 2) + '</pre>';
      }
    }).catch(err => {
      status.innerText = 'Error en petición: ' + err.message;
      result.innerHTML = '<pre>' + (err.stack || err.message) + '</pre>';
    });
  }

  previewBtn.addEventListener('click', function () {
    postReval(false);
  });

  executeBtn.addEventListener('click', function () {
    if (!confirm('Confirmar ejecución: creará asientos de ajuste y marcará el run como ejecutado.')) return;
    postReval(true);
  });
});
