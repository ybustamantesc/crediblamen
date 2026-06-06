(function() {
    'use strict';

    function getBaseUrl() {
        if (typeof window.base_url !== 'undefined' && window.base_url) {
            return window.base_url;
        }
        var pathParts = window.location.pathname.split('/').filter(function(part) {
            return part.length > 0;
        });
        if (pathParts.length > 0) {
            return window.location.origin + '/' + pathParts[0] + '/';
        }
        return window.location.origin + '/';
    }

    var balanceContent = null;
    var resultadosContent = null;
    var saveStatusNode = null;
    var savedStructure = null;
    var balanceLoaded = false;
    var resultadosLoaded = false;

    var balanceGroups = {
        activo: 'ACTIVOS',
        pasivo: 'PASIVOS',
        patrimonio: 'PATRIMONIO'
    };

    var resultadosGroups = {
        ingreso: 'INGRESOS FINANCIEROS',
        gasto: 'GASTOS FINANCIEROS',
        orden: 'CUENTAS DE ORDEN DE LA IMF'
    };

    var availableAccountsByType = {};

    function loadBalanceAccounts() {
        balanceContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando balance...</div>';

        var url = getBaseUrl() + 'contabilidad/desagregacion_cuentas_balance';
        console.debug('desagregacion_cuentas balance URL:', url);
        fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'success') {
                    availableAccountsByType = Object.assign(availableAccountsByType, data.data.groups || {});
                    renderBalanceGroups(data.data);
                } else {
                    balanceContent.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(function(e) {
                console.error('Error loading balance accounts:', e);
                balanceContent.innerHTML = '<div class="alert alert-danger">Error de comunicación: ' + escapeHtml(e.message || String(e)) + '</div>';
            });
    }

    function loadResultadosAccounts() {
        resultadosContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando resultados...</div>';

        var url = getBaseUrl() + 'contabilidad/desagregacion_cuentas_resultados';
        console.debug('desagregacion_cuentas resultados URL:', url);
        fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'success') {
                    availableAccountsByType = Object.assign(availableAccountsByType, data.data.groups || {});
                    renderResultadosGroups(data.data);
                } else {
                    resultadosContent.innerHTML = '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(function(e) {
                console.error('Error loading resultados accounts:', e);
                resultadosContent.innerHTML = '<div class="alert alert-danger">Error de comunicación: ' + escapeHtml(e.message || String(e)) + '</div>';
            });
    }

    function loadSavedStructure() {
        fetch(getBaseUrl() + 'contabilidad/desagregacion_cuentas_saved')
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'success' && data.data) {
                    savedStructure = data.data;
                }
                applySavedStructure();
            })
            .catch(function(e) {
                console.warn('No se pudo cargar configuración guardada:', e);
            });
    }

    function applySavedStructure() {
        if (!savedStructure) {
            return;
        }

        if (balanceLoaded) {
            populateSavedRowsForPanel(balanceGroups);
        }
        if (resultadosLoaded) {
            populateSavedRowsForPanel(resultadosGroups);
        }
    }

    function populateSavedRowsForPanel(groupMap) {
        Object.keys(groupMap).forEach(function(typeKey) {
            var rowsContainer = document.querySelector('.type-rows[data-type-key="' + escapeHtml(typeKey) + '"]');
            if (!rowsContainer) {
                return;
            }

            var existingRows = rowsContainer.querySelectorAll('.type-row-card');
            if (existingRows.length > 0) {
                return;
            }

            var savedRows = (savedStructure.groups && savedStructure.groups[typeKey]) || [];
            if (!savedRows.length) {
                return;
            }

            savedRows.forEach(function(rowData) {
                addTypeRow(typeKey, rowData);
            });
        });
    }

    function renderBalanceGroups(data) {
        var html = '';
        var groups = data.groups || {};

        Object.keys(balanceGroups).forEach(function(key) {
            html += buildTypeSection(balanceGroups[key], key, groups[key] || []);
        });

        balanceContent.innerHTML = html;
        balanceLoaded = true;
        applySavedStructure();
    }

    function renderResultadosGroups(data) {
        var html = '';
        var groups = data.groups || {};

        Object.keys(resultadosGroups).forEach(function(key) {
            html += buildTypeSection(resultadosGroups[key], key, groups[key] || []);
        });

        resultadosContent.innerHTML = html;
        resultadosLoaded = true;
        applySavedStructure();
    }

    function buildTypeSection(title, typeKey, accounts) {
        var section = '<div class="type-card">';
        section += '<div class="type-card-header">';
        section += '<span>' + escapeHtml(title) + '</span>';
        section += '<button type="button" class="btn btn-sm btn-primary btn-add-row" data-type-key="' + escapeHtml(typeKey) + '">Agregar fila</button>';
        section += '</div>';
        section += '<div class="type-card-body">';
        section += '<div class="type-rows" data-type-key="' + escapeHtml(typeKey) + '">';
        section += '<div class="rows-empty">No hay filas creadas. Usa "Agregar fila" para crear una tabla nueva.</div>';
        section += '</div>';
        section += '</div>';
        section += '</div>';
        return section;
    }

    function addTypeRow(typeKey, rowData) {
        var rowsContainer = document.querySelector('.type-rows[data-type-key="' + escapeHtml(typeKey) + '"]');
        if (!rowsContainer) {
            return;
        }
        var rowId = 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
        var rowCard = document.createElement('div');
        rowCard.className = 'type-row-card';
        rowCard.setAttribute('data-row-id', rowId);
        if (rowData && rowData.id) {
            rowCard.setAttribute('data-db-id', rowData.id);
        }

        var titleValue = rowData && rowData.title ? rowData.title : 'Nueva fila';
        rowCard.innerHTML = '<div class="type-row-header">' +
            '<div class="flex-fill">' +
            '<div class="input-group">' +
            '<input type="text" class="form-control form-control-sm row-title" value="' + escapeHtml(titleValue) + '" placeholder="Nombre de fila">' +
            '<div class="input-group-append">' +
            '<button type="button" class="btn btn-sm btn-outline-secondary btn-move-row-up" title="Mover arriba"><i class="fas fa-arrow-up"></i></button>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary btn-move-row-down" title="Mover abajo"><i class="fas fa-arrow-down"></i></button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<span class="row-total text-muted font-weight-bold ml-3">Total: 0.00</span>' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Eliminar fila</button>' +
            '</div>' +
            '<div class="type-row-body">' +
            '<div class="form-group">' +
            '<label>Buscar cuentas para esta fila</label>' +
            '<input type="search" class="form-control form-control-sm search-accounts-input" placeholder="Buscar por código o nombre">' +
            '<div class="accounts-search-results list-group mt-2"><div class="text-muted p-2">Escribe para buscar por código o nombre.</div></div>' +
            '</div>' +
            '<div class="accounts-table-wrapper"><div class="text-muted">No hay cuentas seleccionadas.</div></div>' +
            '</div>';

        rowsContainer.appendChild(rowCard);
        if (rowData && Array.isArray(rowData.accounts) && rowData.accounts.length) {
            renderSelectedAccounts(rowCard, rowData.accounts);
        }
        updateRowsEmpty(rowsContainer);
    }

    function removeTypeRow(button) {
        var rowCard = button.closest('.type-row-card');
        if (!rowCard) {
            return;
        }
        var rowsContainer = rowCard.closest('.type-rows');
        rowCard.remove();
        if (rowsContainer) {
            updateRowsEmpty(rowsContainer);
        }
    }

    function updateRowsEmpty(rowsContainer) {
        var emptyMessage = rowsContainer.querySelector('.rows-empty');
        if (!emptyMessage) {
            return;
        }
        var rowCount = rowsContainer.querySelectorAll('.type-row-card').length;
        emptyMessage.style.display = rowCount === 0 ? 'block' : 'none';
    }

    function renderSelectedAccounts(rowCard, selectedAccounts) {
        var wrapper = rowCard.querySelector('.accounts-table-wrapper');
        if (!wrapper) {
            return;
        }
        if (!selectedAccounts.length) {
            wrapper.innerHTML = '<div class="text-muted">No hay cuentas seleccionadas.</div>';
            updateRowTotal(rowCard);
            return;
        }

        var html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
        html += '<thead class="thead-light"><tr>';
        html += '<th style="width:25%;">N# de Cuenta</th>';
        html += '<th style="width:55%;">Nombre</th>';
        html += '<th style="width:15%;">Monto</th>';
        html += '<th style="width:5%;"></th>';
        html += '</tr></thead><tbody>';

        selectedAccounts.forEach(function(account) {
            var amountValue = typeof account.amount !== 'undefined' && account.amount !== null ? account.amount : '0.00';
            html += '<tr class="row-account-row" data-account-id="' + escapeHtml(account.id) + '">';
            html += '<td>' + escapeHtml(account.code) + '</td>';
            html += '<td>' + escapeHtml(account.name) + '</td>';
            html += '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm row-account-amount" data-account-id="' + escapeHtml(account.id) + '" value="' + escapeHtml(amountValue) + '"></td>';
            html += '<td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-account">×</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        wrapper.innerHTML = html;
        updateRowTotal(rowCard);
    }

    function getSelectedAccountIds(rowCard) {
        var selected = [];
        var rows = rowCard.querySelectorAll('.row-account-row');
        rows.forEach(function(row) {
            var id = row.getAttribute('data-account-id');
            if (id) {
                selected.push(id);
            }
        });
        return selected;
    }

    function getAllSelectedAccountIds() {
        var selected = [];
        document.querySelectorAll('.row-account-row').forEach(function(row) {
            var id = row.getAttribute('data-account-id');
            if (id && selected.indexOf(id) === -1) {
                selected.push(id);
            }
        });
        return selected;
    }

    function searchAccounts(rowCard, query) {
        var resultsNode = rowCard.querySelector('.accounts-search-results');
        if (!resultsNode) {
            return;
        }

        var typeKey = rowCard.closest('.type-rows').getAttribute('data-type-key');
        var accounts = availableAccountsByType[typeKey] || [];
        var normalized = String(query || '').trim().toLowerCase();
        var selectedIds = getAllSelectedAccountIds();

        if (!normalized) {
            resultsNode.innerHTML = '<div class="text-muted p-2">Escribe para buscar por código o nombre.</div>';
            return;
        }

        var matches = accounts.filter(function(account) {
            var code = String(account.code || '').toLowerCase();
            var name = String(account.name || '').toLowerCase();
            return (code.indexOf(normalized) !== -1 || name.indexOf(normalized) !== -1) && selectedIds.indexOf(String(account.id)) === -1;
        });

        if (!matches.length) {
            resultsNode.innerHTML = '<div class="text-muted p-2">No se encontraron cuentas coincidentes.</div>';
            return;
        }

        var html = '';
        matches.forEach(function(account) {
            html += '<button type="button" class="list-group-item list-group-item-action add-account-result" data-account-id="' + escapeHtml(account.id) + '">';
            html += '[' + escapeHtml(account.code) + '] ' + escapeHtml(account.name);
            html += '</button>';
        });
        resultsNode.innerHTML = html;
    }

    function addSelectedAccount(rowCard, accountId) {
        var typeKey = rowCard.closest('.type-rows').getAttribute('data-type-key');
        var accounts = availableAccountsByType[typeKey] || [];

        var allSelectedIds = getAllSelectedAccountIds();
        if (allSelectedIds.indexOf(String(accountId)) !== -1) {
            setRowMessage(rowCard, 'Esta cuenta ya está seleccionada en otra fila.');
            return;
        }

        var account = accounts.find(function(item) {
            return String(item.id) === String(accountId);
        });
        if (!account) {
            return;
        }

        var selectedAccounts = getSelectedAccountsInRow(rowCard);
        selectedAccounts.push({
            id: account.id,
            code: account.code,
            name: account.name,
            amount: '0.00'
        });
        renderSelectedAccounts(rowCard, selectedAccounts);
    }

    function getSelectedAccountsInRow(rowCard) {
        var selectedAccounts = [];
        rowCard.querySelectorAll('.row-account-row').forEach(function(row) {
            var id = row.getAttribute('data-account-id');
            if (!id) {
                return;
            }
            var codeCell = row.querySelector('td:nth-child(1)');
            var nameCell = row.querySelector('td:nth-child(2)');
            var amountInput = row.querySelector('.row-account-amount');
            selectedAccounts.push({
                id: id,
                code: codeCell ? codeCell.textContent.trim() : '',
                name: nameCell ? nameCell.textContent.trim() : '',
                amount: amountInput ? amountInput.value.trim() : '0.00'
            });
        });
        return selectedAccounts;
    }

    function formatCurrency(value) {
        var number = parseFloat(String(value).replace(',', '.'));
        if (isNaN(number)) {
            return '0.00';
        }
        return number.toFixed(2);
    }

    function computeRowTotal(rowCard) {
        var total = 0;
        rowCard.querySelectorAll('.row-account-amount').forEach(function(input) {
            var value = parseFloat(String(input.value).replace(',', '.'));
            if (!isNaN(value)) {
                total += value;
            }
        });
        return total;
    }

    function updateRowTotal(rowCard) {
        if (!rowCard) {
            return;
        }
        var total = computeRowTotal(rowCard);
        var totalNode = rowCard.querySelector('.row-total');
        if (totalNode) {
            totalNode.textContent = 'Total: ' + formatCurrency(total);
        }
    }

    function setRowMessage(rowCard, message) {
        var resultsNode = rowCard.querySelector('.accounts-search-results');
        if (!resultsNode) {
            return;
        }
        resultsNode.innerHTML = '<div class="text-danger p-2">' + escapeHtml(message) + '</div>';
    }

    function setSaveStatus(message, isError) {
        if (!saveStatusNode) {
            saveStatusNode = document.getElementById('saveStatus');
        }
        if (!saveStatusNode) {
            return;
        }
        saveStatusNode.textContent = message;
        saveStatusNode.className = isError ? 'ml-3 text-danger' : 'ml-3 text-success';
    }

    function getKnownGroupKeys() {
        var keys = [];
        Object.keys(balanceGroups).forEach(function(key) {
            if (keys.indexOf(key) === -1) {
                keys.push(key);
            }
        });
        Object.keys(resultadosGroups).forEach(function(key) {
            if (keys.indexOf(key) === -1) {
                keys.push(key);
            }
        });
        return keys;
    }

    function gatherSavePayload() {
        var groups = {};
        document.querySelectorAll('.type-rows').forEach(function(container) {
            var typeKey = container.getAttribute('data-type-key');
            if (!typeKey) {
                return;
            }
            groups[typeKey] = [];
            container.querySelectorAll('.type-row-card').forEach(function(rowCard) {
                var title = rowCard.querySelector('.row-title');
                var rowData = {
                    title: title ? title.value.trim() : 'Fila sin nombre',
                    accounts: []
                };
                rowCard.querySelectorAll('.row-account-row').forEach(function(accountRow) {
                    var accountId = accountRow.getAttribute('data-account-id');
                    if (!accountId) {
                        return;
                    }
                    var code = accountRow.querySelector('td:nth-child(1)') ? accountRow.querySelector('td:nth-child(1)').textContent.trim() : '';
                    var name = accountRow.querySelector('td:nth-child(2)') ? accountRow.querySelector('td:nth-child(2)').textContent.trim() : '';
                    var amountInput = accountRow.querySelector('.row-account-amount');
                    rowData.accounts.push({
                        id: accountId,
                        code: code,
                        name: name,
                        amount: amountInput ? amountInput.value.trim() : '0.00'
                    });
                });
                if (rowCard.dataset.dbId) {
                    rowData.id = rowCard.dataset.dbId;
                }
                groups[typeKey].push(rowData);
            });
        });

        getKnownGroupKeys().forEach(function(typeKey) {
            if (!groups.hasOwnProperty(typeKey)) {
                groups[typeKey] = (savedStructure && savedStructure.groups && Array.isArray(savedStructure.groups[typeKey])) ? savedStructure.groups[typeKey] : [];
            }
        });

        return { groups: groups };
    }

    function saveConfiguration() {
        var payload = gatherSavePayload();
        setSaveStatus('Guardando...', false);
        var url = getBaseUrl() + 'contabilidad/desagregacion_cuentas_save';
        console.debug('desagregacion_cuentas save URL:', url);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'success') {
                    setSaveStatus('Configuración guardada.', false);
                } else {
                    setSaveStatus('Error guardando: ' + (data.message || 'Desconocido'), true);
                }
            })
            .catch(function(e) {
                console.error('Error saving configuration:', e);
                setSaveStatus('Error guardando: ' + (e.message || String(e)), true);
            });
    }

    document.addEventListener('click', function(event) {
        var addButton = event.target.closest('.btn-add-row');
        if (addButton) {
            var typeKey = addButton.getAttribute('data-type-key');
            addTypeRow(typeKey);
            return;
        }

        var removeButton = event.target.closest('.btn-remove-row');
        if (removeButton) {
            removeTypeRow(removeButton);
            return;
        }

        var addAccountButton = event.target.closest('.add-account-result');
        if (addAccountButton) {
            var rowCard = addAccountButton.closest('.type-row-card');
            var accountId = addAccountButton.getAttribute('data-account-id');
            addSelectedAccount(rowCard, accountId);
            var searchInput = rowCard.querySelector('.search-accounts-input');
            if (searchInput) {
                searchInput.value = '';
            }
            var resultsNode = rowCard.querySelector('.accounts-search-results');
            if (resultsNode) {
                resultsNode.innerHTML = '<div class="text-muted p-2">Escribe para buscar por código o nombre.</div>';
            }
            return;
        }

        var removeAccountButton = event.target.closest('.btn-remove-account');
        if (removeAccountButton) {
            var accountRow = removeAccountButton.closest('.row-account-row');
            if (accountRow) {
                var rowCard = accountRow.closest('.type-row-card');
                accountRow.remove();
                var selectedIds = getSelectedAccountIds(rowCard);
                if (!selectedIds.length) {
                    renderSelectedAccounts(rowCard, []);
                } else {
                    updateRowTotal(rowCard);
                }
            }
            return;
        }

        var moveUpButton = event.target.closest('.btn-move-row-up');
        if (moveUpButton) {
            var rowCard = moveUpButton.closest('.type-row-card');
            if (rowCard && rowCard.previousElementSibling) {
                rowCard.parentNode.insertBefore(rowCard, rowCard.previousElementSibling);
            }
            return;
        }

        var moveDownButton = event.target.closest('.btn-move-row-down');
        if (moveDownButton) {
            var rowCard = moveDownButton.closest('.type-row-card');
            if (rowCard && rowCard.nextElementSibling) {
                rowCard.parentNode.insertBefore(rowCard.nextElementSibling, rowCard);
            }
            return;
        }

        var saveButton = event.target.closest('#saveDesagregacion');
        if (saveButton) {
            saveConfiguration();
            return;
        }
    });

    document.addEventListener('input', function(event) {
        var searchInput = event.target.closest('.search-accounts-input');
        if (searchInput) {
            var rowCard = searchInput.closest('.type-row-card');
            searchAccounts(rowCard, searchInput.value);
            return;
        }

        var amountInput = event.target.closest('.row-account-amount');
        if (!amountInput) {
            return;
        }
        var rowCard = amountInput.closest('.type-row-card');
        updateRowTotal(rowCard);
    });

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    document.addEventListener('DOMContentLoaded', function() {
        balanceContent = document.getElementById('balance_content');
        resultadosContent = document.getElementById('resultados_content');
        saveStatusNode = document.getElementById('saveStatus');
        loadBalanceAccounts();
        loadSavedStructure();
    });

    var resultadosTab = document.getElementById('resultados-tab');
    if (resultadosTab) {
        resultadosTab.addEventListener('click', function() {
            if (!resultadosContent.innerHTML.trim() || resultadosContent.innerHTML.indexOf('Cargando') !== -1) {
                loadResultadosAccounts();
            }
        });
    }
})();
