document.addEventListener('DOMContentLoaded', function() {
    if (typeof base_url === 'undefined') window.base_url = window.location.origin + '/servicredit/';
    
    const content = document.getElementById('centrosCostoContent');
    const modalContainer = document.getElementById('modalContainer');
    const btnNew = document.getElementById('btnNewCentroCosto');
    const searchInput = document.getElementById('searchCentroCosto');
    
    let allCentros = [];
    
    // Load centros de costo
    function loadCentros() {
        content.innerHTML = '<div style="text-align:center;padding:40px;color:#6b7280;"><i class="fas fa-spinner fa-spin fa-2x"></i><div style="margin-top:12px;">Cargando...</div></div>';
        
        fetch(base_url + 'contabilidad/centros_costo_list')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    allCentros = data.data || [];
                    renderCentros(allCentros);
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(err => {
                console.error('Error loading centros:', err);
                content.innerHTML = '<div class="alert alert-danger">Error de comunicación</div>';
            });
    }
    
    // Render centros table
    function renderCentros(centros) {
        if (!centros || centros.length === 0) {
            content.innerHTML = `
                <div style="padding:40px;text-align:center;color:#6b7280;background:#f9fafb;border-radius:8px;">
                    <i class="fas fa-building" style="font-size:48px;color:#d1d5db;margin-bottom:16px;"></i>
                    <div style="font-size:18px;font-weight:600;margin-bottom:8px;">No hay centros de costo registrados</div>
                    <div style="font-size:14px;">Haz clic en "Nuevo Centro de Costo" para crear el primero</div>
                </div>
            `;
            return;
        }
        
        let html = `
            <div style="overflow-x:auto;">
                <table class="table table-hover" style="min-width:600px;">
                    <thead style="background:linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                        <tr>
                            <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;">Código</th>
                            <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;">Nombre</th>
                            <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;">Descripción</th>
                            <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;text-align:center;">Estado</th>
                            <th style="padding:16px 12px;font-weight:600;color:#374151;font-size:13px;text-transform:uppercase;text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        centros.forEach(c => {
            const isActive = parseInt(c.activo) === 1;
            html += `
                <tr data-id="${c.id}" data-codigo="${(c.codigo || '').toLowerCase()}" data-nombre="${(c.nombre || '').toLowerCase()}" style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:14px 12px;font-weight:700;color:#667eea;font-size:15px;">${escapeHtml(c.codigo)}</td>
                    <td style="padding:14px 12px;font-weight:600;color:#1f2937;">${escapeHtml(c.nombre)}</td>
                    <td style="padding:14px 12px;color:#6b7280;font-size:14px;">${escapeHtml(c.descripcion || '-')}</td>
                    <td style="padding:14px 12px;text-align:center;">
                        ${isActive 
                            ? '<span style="background:#d1fae5;color:#059669;padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;">ACTIVO</span>'
                            : '<span style="background:#fee2e2;color:#dc2626;padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;">INACTIVO</span>'
                        }
                    </td>
                    <td style="padding:14px 12px;text-align:center;white-space:nowrap;">
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${c.id}" style="margin-right:4px;">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${c.id}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
        
        // Attach events
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                openEditModal(this.getAttribute('data-id'));
            });
        });
        
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteCentro(this.getAttribute('data-id'));
            });
        });
    }
    
    // Search filter
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            if (!term) {
                renderCentros(allCentros);
                return;
            }
            
            const filtered = allCentros.filter(c => {
                return (c.codigo && c.codigo.toLowerCase().includes(term)) ||
                       (c.nombre && c.nombre.toLowerCase().includes(term));
            });
            
            renderCentros(filtered);
        });
    }
    
    // Open new modal
    if (btnNew) {
        btnNew.addEventListener('click', function() {
            openNewModal();
        });
    }
    
    function openNewModal() {
        fetch(base_url + 'contabilidad/centros_costo_modal')
            .then(r => r.text())
            .then(html => {
                modalContainer.innerHTML = html;
                attachModalEvents();
            })
            .catch(err => {
                console.error('Error loading modal:', err);
                alert('Error al abrir el modal');
            });
    }
    
    function openEditModal(id) {
        const centro = allCentros.find(c => c.id == id);
        if (!centro) {
            alert('Centro de costo no encontrado');
            return;
        }
        
        fetch(base_url + 'contabilidad/centros_costo_modal')
            .then(r => r.text())
            .then(html => {
                modalContainer.innerHTML = html;
                
                // Fill form
                document.getElementById('modalTitle').textContent = 'Editar Centro de Costo';
                document.getElementById('centro_id').value = centro.id;
                document.getElementById('centro_codigo').value = centro.codigo;
                document.getElementById('centro_nombre').value = centro.nombre;
                document.getElementById('centro_descripcion').value = centro.descripcion || '';
                document.getElementById('centro_activo').checked = parseInt(centro.activo) === 1;
                
                attachModalEvents();
            })
            .catch(err => {
                console.error('Error loading modal:', err);
                alert('Error al abrir el modal');
            });
    }
    
    function attachModalEvents() {
        const modal = document.getElementById('modalCentroCosto');
        if (!modal) return;
        
        const form = document.getElementById('formCentroCosto');
        const btnCancel = document.getElementById('btnCancelModal');
        const btnCancelFooter = document.getElementById('btnCancelModalFooter');
        
        // Cancel buttons
        [btnCancel, btnCancelFooter].forEach(btn => {
            if (btn) {
                btn.addEventListener('click', function() {
                    modalContainer.innerHTML = '';
                });
            }
        });
        
        // Backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modalContainer.innerHTML = '';
            }
        });
        
        // Form submit
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                saveCentro();
            });
        }
    }
    
    function saveCentro() {
        const form = document.getElementById('formCentroCosto');
        const formData = new FormData(form);
        
        // Convert to URLSearchParams
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            params.append(key, value);
        }
        
        fetch(base_url + 'contabilidad/centros_costo_save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message || 'Centro de costo guardado correctamente');
                modalContainer.innerHTML = '';
                loadCentros();
            } else {
                alert('Error: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(err => {
            console.error('Error saving:', err);
            alert('Error de comunicación al guardar');
        });
    }
    
    function deleteCentro(id) {
        if (!confirm('¿Está seguro de eliminar este centro de costo?\n\nNota: No se puede eliminar si está siendo usado en asientos contables.')) {
            return;
        }
        
        fetch(base_url + 'contabilidad/centros_costo_delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message || 'Centro de costo eliminado correctamente');
                loadCentros();
            } else {
                alert('Error: ' + (data.message || 'No se puede eliminar. Puede estar en uso.'));
            }
        })
        .catch(err => {
            console.error('Error deleting:', err);
            alert('Error de comunicación al eliminar');
        });
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initial load
    loadCentros();
});
