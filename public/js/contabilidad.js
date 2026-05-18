document.addEventListener('DOMContentLoaded', function(){
    // minimal page-level script: fetch listing and open modal
    const btnNew = document.getElementById('btnNewEntry');
    const modalContainer = document.getElementById('modalContainer');
    const entriesBody = document.getElementById('entriesBody');

    // Base url helper — fallback to current app root if not defined by server
    if (typeof base_url === 'undefined') {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const root = parts.length ? ('/' + parts[0] + '/') : '/';
        window.base_url = window.location.origin + root;
    }

    function fetchEntries(){
        if (!entriesBody) return;
        fetch(base_url + 'contabilidad/list_entries')
        .then(r => r.json())
        .then(json => {
            if (!json || !Array.isArray(json.data)) {
                entriesBody.innerHTML = '<tr><td colspan="5" style="padding:12px;text-align:center;color:#888;">No hay registros</td></tr>';
                return;
            }
            const rows = json.data.map(e => `
                <tr class="entry-row" data-id="${e.id}" data-date="${e.date}" data-description="${(e.description||'').toString().replace(/"/g,'&quot;')}">
                    <td style="padding:8px">${e.id}</td>
                    <td style="padding:8px">${e.date}</td>
                    <td style="padding:8px">${e.description}</td>
                    <td style="padding:8px;text-align:right">${e.total_debit}</td>
                    <td style="padding:8px;text-align:right">${e.total_credit}</td>
                </tr>`).join('');
            entriesBody.innerHTML = rows || '<tr><td colspan="5" style="padding:12px;text-align:center;color:#888;">No hay registros</td></tr>';
        }).catch(err => {
            console.error('Error fetching entries', err);
            entriesBody.innerHTML = '<tr><td colspan="5" style="padding:12px;text-align:center;color:#888;">Error</td></tr>';
        });
    }

    if (btnNew) {
        btnNew.addEventListener('click', function(){
            fetch(base_url + 'contabilidad/modal_add')
            .then(r => r.text())
            .then(html => {
                if (!modalContainer) return;
                modalContainer.innerHTML = html;
                // initialize modal behavior provided by contabilidad_modal_enhanced.js
                if (typeof attachModalEvents === 'function') {
                    try { attachModalEvents(); } catch(e) { console.error('attachModalEvents error', e); }
                }
            }).catch(err => {
                console.error('Error loading modal:', err);
                alert('Error al abrir el modal');
            });
        });
    }

    // expose refresh for other scripts
    window.refreshContabilidadEntries = fetchEntries;

    if (entriesBody) fetchEntries();
});
