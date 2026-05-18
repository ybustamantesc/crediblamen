// Enhanced modal functionality for contabilidad with USD conversion and account search
window.attachModalEvents = function() {
    const modal = document.getElementById('modalAddEntry');
    if (!modal) return;
    // Detectar si viene de tesorería (movimiento)
    let movimientoId = null;
    if (window.CONTABILIZAR_MOV_ID) {
        movimientoId = window.CONTABILIZAR_MOV_ID;
    }

    // --- Period closed guard: check on open and when date changes ---
    (function initPeriodGuard(){
        const dateInput = modal.querySelector('input[name="date"]');
        const submitBtn = modal.querySelector('button[type="submit"]');
        const msg = document.getElementById('entryMessage');
        if (!dateInput || !submitBtn) return;

        // helper to mark UI when closed
        function markClosed(text){
            if (msg) { msg.textContent = text || 'Periodo cerrado'; msg.style.color = 'red'; }
            submitBtn.disabled = true;
        }
        function markOpen(){ if (msg) { msg.textContent = ''; } submitBtn.disabled = false; }

        // check single date via is_period_closed
        function checkDate(d){
            fetch(base_url + 'contabilidad/is_period_closed?date=' + encodeURIComponent(d))
            .then(r=>r.json()).then(j=>{
                if (j && j.status === 'success' && j.closed && !j.is_admin) {
                    markClosed('Este periodo está cerrado. Pida a un administrador que lo abra.');
                } else {
                    markOpen();
                }
            }).catch(err=>{
                console.error('Error checking period:', err);
                // don't block saving if check fails
                markOpen();
            });
        }

        // load closed periods and auto-adjust if current month closed
        fetch(base_url + 'contabilidad/cierre_mensual_list')
        .then(r=>r.json()).then(listResp=>{
            const closedSet = new Set();
            if (listResp && listResp.status === 'success' && Array.isArray(listResp.data)) {
                listResp.data.forEach(p=>{
                    const y = String(p.year).padStart(4,'0');
                    const m = String(p.month).padStart(2,'0');
                    closedSet.add(y + '-' + m);
                });
            }

            function ymFromDate(dstr){ return dstr ? dstr.slice(0,7) : null; }
            function nextMonthFirstDay(dstr){
                let d = dstr ? new Date(dstr) : new Date();
                d.setDate(1);
                d.setMonth(d.getMonth() + 1);
                const y = d.getFullYear(); const m = String(d.getMonth()+1).padStart(2,'0');
                return y + '-' + m + '-01';
            }

            let cur = dateInput.value || (new Date()).toISOString().slice(0,10);
            let curYM = ymFromDate(cur);
            if (curYM && closedSet.has(curYM)) {
                let attempts = 0;
                while (attempts < 24 && closedSet.has(curYM)) {
                    cur = nextMonthFirstDay(cur);
                    curYM = ymFromDate(cur);
                    attempts++;
                }
                dateInput.value = cur;
                if (msg) { msg.textContent = 'La fecha fue ajustada a ' + cur + ' porque el mes seleccionado está cerrado.'; msg.style.color = 'orange'; }
            }

            // initial check after potential adjust
            checkDate(dateInput.value || (new Date()).toISOString().slice(0,10));
        }).catch(err=>{
            console.error('Error fetching closed periods', err);
            checkDate(dateInput.value || (new Date()).toISOString().slice(0,10));
        });

        dateInput.addEventListener('change', function(){ checkDate(this.value); });
    })();

    let lineCounter = 1;
    const exchangeRate = document.getElementById('exchangeRate');

    // Load current exchange rate from system (always uses the most recent record)
    function loadExchangeRate() {
        fetch(base_url + 'tasacambio/get_tasa_actual_ajax')
            .then(r => r.json())
            .then(data => {
                if (data.status && data.tasa_compra) {
                    exchangeRate.value = parseFloat(data.tasa_compra).toFixed(4);
                    console.log('Tipo de cambio cargado:', exchangeRate.value);
                } else {
                    // Error case - usar tasa por defecto
                    exchangeRate.value = '36.50';
                    console.warn('No se pudo cargar tasa de cambio, usando 36.50 por defecto');
                }
            })
            .catch(err => {
                console.error('Error loading exchange rate:', err);
                exchangeRate.value = '36.50';
            });
    }

    // Load rate on modal open
    if (exchangeRate) {
        loadExchangeRate();
    }

    // Currency conversion handlers
    function convertUSDtoNIO(usdValue) {
        const rate = parseFloat(exchangeRate.value) || 36.50;
        return usdValue * rate;
    }

    function convertNIOtoUSD(nioValue) {
        const rate = parseFloat(exchangeRate.value) || 36.50;
        return nioValue / rate;
    }

    // Auto-convert when typing in USD fields (Debe USD -> Debe NIO)
    modal.addEventListener('input', function(e) {
        if (e.target.classList.contains('line-debit-usd')) {
            const line = e.target.getAttribute('data-line');
            const usdValue = parseFloat(e.target.value) || 0;
            const nioInput = modal.querySelector(`.line-debit-mxn[data-line="${line}"]`);
            if (nioInput && usdValue > 0) {
                nioInput.value = convertUSDtoNIO(usdValue).toFixed(2);
                // Clear credit fields
                const creditNIO = modal.querySelector(`.line-credit-mxn[data-line="${line}"]`);
                const creditUSD = modal.querySelector(`.line-credit-usd[data-line="${line}"]`);
                if (creditNIO) creditNIO.value = '';
                if (creditUSD) creditUSD.value = '';
            }
            updateTotals();
        }

        // Auto-convert when typing in USD fields (Haber USD -> Haber NIO)
        if (e.target.classList.contains('line-credit-usd')) {
            const line = e.target.getAttribute('data-line');
            const usdValue = parseFloat(e.target.value) || 0;
            const nioInput = modal.querySelector(`.line-credit-mxn[data-line="${line}"]`);
            if (nioInput && usdValue > 0) {
                nioInput.value = convertUSDtoNIO(usdValue).toFixed(2);
                // Clear debit fields
                const debitNIO = modal.querySelector(`.line-debit-mxn[data-line="${line}"]`);
                const debitUSD = modal.querySelector(`.line-debit-usd[data-line="${line}"]`);
                if (debitNIO) debitNIO.value = '';
                if (debitUSD) debitUSD.value = '';
            }
            updateTotals();
        }

        // Auto-convert when typing in NIO fields (Debe NIO -> Debe USD)
        if (e.target.classList.contains('line-debit-mxn')) {
            const line = e.target.getAttribute('data-line');
            const nioValue = parseFloat(e.target.value) || 0;
            const usdInput = modal.querySelector(`.line-debit-usd[data-line="${line}"]`);
            if (usdInput && nioValue > 0 && !e.target.dataset.skipConversion) {
                usdInput.value = convertNIOtoUSD(nioValue).toFixed(2);
            }
            updateTotals();
        }

        // Auto-convert when typing in NIO fields (Haber NIO -> Haber USD)
        if (e.target.classList.contains('line-credit-mxn')) {
            const line = e.target.getAttribute('data-line');
            const nioValue = parseFloat(e.target.value) || 0;
            const usdInput = modal.querySelector(`.line-credit-usd[data-line="${line}"]`);
            if (usdInput && nioValue > 0 && !e.target.dataset.skipConversion) {
                usdInput.value = convertNIOtoUSD(nioValue).toFixed(2);
            }
            updateTotals();
        }

        // Recalculate all when exchange rate changes
        if (e.target.id === 'exchangeRate') {
            const allUSDDebits = modal.querySelectorAll('.line-debit-usd');
            allUSDDebits.forEach(input => {
                const line = input.getAttribute('data-line');
                const usdValue = parseFloat(input.value) || 0;
                if (usdValue > 0) {
                    const nioInput = modal.querySelector(`.line-debit-mxn[data-line="${line}"]`);
                    if (nioInput) {
                        nioInput.dataset.skipConversion = 'true';
                        nioInput.value = convertUSDtoNIO(usdValue).toFixed(2);
                        delete nioInput.dataset.skipConversion;
                    }
                }
            });

            const allUSDCredits = modal.querySelectorAll('.line-credit-usd');
            allUSDCredits.forEach(input => {
                const line = input.getAttribute('data-line');
                const usdValue = parseFloat(input.value) || 0;
                if (usdValue > 0) {
                    const nioInput = modal.querySelector(`.line-credit-mxn[data-line="${line}"]`);
                    if (nioInput) {
                        nioInput.dataset.skipConversion = 'true';
                        nioInput.value = convertUSDtoNIO(usdValue).toFixed(2);
                        delete nioInput.dataset.skipConversion;
                    }
                }
            });
            updateTotals();
        }
    });

    // Calculate totals
    function updateTotals() {
        let totalDebitNIO = 0;
        let totalDebitUSD = 0;
        let totalCreditNIO = 0;
        let totalCreditUSD = 0;

        modal.querySelectorAll('.line-debit-mxn').forEach(inp => {
            totalDebitNIO += parseFloat(inp.value) || 0;
        });

        modal.querySelectorAll('.line-debit-usd').forEach(inp => {
            totalDebitUSD += parseFloat(inp.value) || 0;
        });

        modal.querySelectorAll('.line-credit-mxn').forEach(inp => {
            totalCreditNIO += parseFloat(inp.value) || 0;
        });

        modal.querySelectorAll('.line-credit-usd').forEach(inp => {
            totalCreditUSD += parseFloat(inp.value) || 0;
        });

        document.getElementById('totalDebitNIO').textContent = 'C$' + totalDebitNIO.toFixed(2);
        document.getElementById('totalDebitUSD').textContent = '$' + totalDebitUSD.toFixed(2);
        document.getElementById('totalCreditNIO').textContent = 'C$' + totalCreditNIO.toFixed(2);
        document.getElementById('totalCreditUSD').textContent = '$' + totalCreditUSD.toFixed(2);

        const msg = document.getElementById('entryMessage');
        const diff = Math.abs(totalDebitNIO - totalCreditNIO);
        if (diff < 0.01) {
            msg.textContent = '✓ Asiento cuadrado';
            msg.style.background = '#d1fae5';
            msg.style.color = '#065f46';
            msg.style.border = '2px solid #10b981';
        } else {
            msg.textContent = '⚠ Diferencia: C$' + diff.toFixed(2);
            msg.style.background = '#fee2e2';
            msg.style.color = '#991b1b';
            msg.style.border = '2px solid #ef4444';
        }
    }

    // Function to toggle debe/haber fields (only one side can have values per line)
    function toggleDebitCreditFields(lineElement) {
        const debitNIO = lineElement.querySelector('.line-debit-mxn');
        const debitUSD = lineElement.querySelector('.line-debit-usd');
        const creditNIO = lineElement.querySelector('.line-credit-mxn');
        const creditUSD = lineElement.querySelector('.line-credit-usd');

        if (!debitNIO || !debitUSD || !creditNIO || !creditUSD) return;

        // Check if debe has values
        const hasDebit = (parseFloat(debitNIO.value) || 0) > 0 || (parseFloat(debitUSD.value) || 0) > 0;
        
        // Check if haber has values
        const hasCredit = (parseFloat(creditNIO.value) || 0) > 0 || (parseFloat(creditUSD.value) || 0) > 0;

        if (hasDebit) {
            // Disable credit fields
            creditNIO.disabled = true;
            creditUSD.disabled = true;
            creditNIO.style.opacity = '0.5';
            creditUSD.style.opacity = '0.5';
            creditNIO.style.cursor = 'not-allowed';
            creditUSD.style.cursor = 'not-allowed';
        } else if (hasCredit) {
            // Disable debit fields
            debitNIO.disabled = true;
            debitUSD.disabled = true;
            debitNIO.style.opacity = '0.5';
            debitUSD.style.opacity = '0.5';
            debitNIO.style.cursor = 'not-allowed';
            debitUSD.style.cursor = 'not-allowed';
        } else {
            // Enable all fields
            debitNIO.disabled = false;
            debitUSD.disabled = false;
            creditNIO.disabled = false;
            creditUSD.disabled = false;
            debitNIO.style.opacity = '1';
            debitUSD.style.opacity = '1';
            creditNIO.style.opacity = '1';
            creditUSD.style.opacity = '1';
            debitNIO.style.cursor = '';
            debitUSD.style.cursor = '';
            creditNIO.style.cursor = '';
            creditUSD.style.cursor = '';
        }
    }

    // Attach toggle handlers to all lines
    function attachToggleHandlers(lineElement) {
        const inputs = lineElement.querySelectorAll('.line-debit-mxn, .line-debit-usd, .line-credit-mxn, .line-credit-usd');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                toggleDebitCreditFields(lineElement);
            });
        });
    }

    // Initialize toggle handlers on existing lines
    modal.querySelectorAll('.entry-line').forEach(line => {
        attachToggleHandlers(line);
    });

    // Sync main description with line descriptions in real-time
    const mainDescriptionField = modal.querySelector('textarea[name="description"]');
    if (mainDescriptionField) {
        mainDescriptionField.addEventListener('input', function() {
            const descValue = this.value;
            // Update all line description fields
            modal.querySelectorAll('.entry-line input[name^="lines"][name$="[description]"]').forEach(lineDesc => {
                // Only update if the field is empty or hasn't been manually edited
                if (!lineDesc.dataset.manuallyEdited) {
                    lineDesc.value = descValue;
                }
            });
        });
    }

    // Mark line descriptions as manually edited when user types in them
    modal.addEventListener('input', function(e) {
        if (e.target.matches('.entry-line input[name^="lines"][name$="[description]"]')) {
            e.target.dataset.manuallyEdited = 'true';
        }
    });

    // Add new line
    const btnAddLine = document.getElementById('btnAddLine');
    
    // Expose addLine function globally for edit functionality
    window.addEntryLine = function() {
        const wrapper = document.getElementById('linesWrapper');
        if (!wrapper) return;
        
        const newLine = document.createElement('div');
        newLine.className = 'entry-line';
        newLine.style.cssText = 'margin-bottom:16px;';

        // Get centro_costo options from the first line's select
        let centroCostoOptions = '<option value="">-- Centro de Costo --</option>';
        const firstCentroCosto = document.querySelector('.line-centro-costo');
        if (firstCentroCosto) {
            const options = firstCentroCosto.querySelectorAll('option');
            options.forEach((opt, i) => {
                if (i > 0) { // Skip first placeholder
                    centroCostoOptions += '<option value="' + opt.value + '">' + opt.textContent + '</option>';
                }
            });
        }

        // Get main description value
        const mainDescription = document.querySelector('textarea[name="description"]');
        const descriptionValue = mainDescription ? mainDescription.value : '';

        newLine.innerHTML = `
            <div style="display:grid;grid-template-columns:1.5fr 2.5fr 1fr 1fr 1fr 1fr 50px;gap:12px;margin-bottom:8px;align-items:center;">
                <select name="lines[${lineCounter}][centro_costo_id]" class="line-centro-costo" data-line="${lineCounter}" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'">
                    ${centroCostoOptions}
                </select>
                <div style="height:44px;">
                    <select name="lines[${lineCounter}][account_id]" class="account-select" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb'">
                        <option value="">-- Buscar por código o nombre --</option>
                    </select>
                </div>
                <input type="number" step="0.01" name="lines[${lineCounter}][debit]" placeholder="0.00" class="line-debit line-debit-mxn" data-line="${lineCounter}" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#e5e7eb'" />
                <input type="number" step="0.01" name="lines[${lineCounter}][debit_usd]" placeholder="0.00" class="line-debit-usd" data-line="${lineCounter}" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;background:#fef3c7;transition:all 0.3s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'" />
                <input type="number" step="0.01" name="lines[${lineCounter}][credit]" placeholder="0.00" class="line-credit line-credit-mxn" data-line="${lineCounter}" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'" />
                <input type="number" step="0.01" name="lines[${lineCounter}][credit_usd]" placeholder="0.00" class="line-credit-usd" data-line="${lineCounter}" style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;text-align:right;font-size:13px;background:#d1fae5;transition:all 0.3s;" onfocus="this.style.borderColor='#059669'" onblur="this.style.borderColor='#e5e7eb'" />
                <button type="button" class="btn-remove-line" data-line="${lineCounter}" style="background:#ef4444;color:#fff;border:none;width:44px;height:44px;border-radius:6px;cursor:pointer;font-size:18px;line-height:1;transition:all 0.3s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">×</button>
            </div>
            <div>
                <input name="lines[${lineCounter}][description]" value="${descriptionValue.replace(/"/g, '&quot;')}" placeholder="Detalle del movimiento..." style="width:100%;height:44px;padding:8px 12px;border:2px solid #e5e7eb;border-radius:6px;font-size:13px;transition:all 0.3s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e5e7eb' />
            </div>
        `;

        wrapper.appendChild(newLine);
        lineCounter++;
        initializeSelect2ForLine(newLine);
        attachToggleHandlers(newLine);
    };
    
    if (btnAddLine) {
        btnAddLine.addEventListener('click', window.addEntryLine);
    }

    // Remove line
    modal.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-line')) {
            const line = e.target.closest('.entry-line');
            if (line && modal.querySelectorAll('.entry-line').length > 1) {
                // Destroy Select2 before removing
                const select = line.querySelector('.account-select');
                if (select && typeof $ !== 'undefined' && $.fn.select2) {
                    try {
                        $(select).select2('destroy');
                    } catch(e) {}
                }
                line.remove();
                updateTotals();
            } else {
                alert('Debe haber al menos una línea en el asiento');
            }
        }
    });

    // Initialize Select2 with account search
    function initializeSelect2ForLine(lineElement) {
        if (typeof $ === 'undefined' || !$.fn.select2) {
            console.warn('Select2 not loaded');
            return;
        }
        
        const select = lineElement ? lineElement.querySelector('.account-select') : null;
        if (!select) return;

        // Destroy existing Select2 if any
        try {
            if ($(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }
        } catch(e) {}

        $(select).select2({
            placeholder: '-- Escribe 3 caracteres para buscar --',
            allowClear: true,
            width: '100%',
            dropdownParent: $(modal),
            language: {
                noResults: function() {
                    return "No se encontraron cuentas con ese criterio";
                },
                searching: function() {
                    return '<i class="fas fa-spinner fa-spin"></i> Buscando cuentas...';
                },
                inputTooShort: function(args) {
                    var remainingChars = args.minimum - args.input.length;
                    return 'Escribe ' + remainingChars + ' carácter' + (remainingChars > 1 ? 'es' : '') + ' más para buscar';
                },
                errorLoading: function() {
                    return 'No se pudieron cargar los resultados';
                }
            },
            minimumInputLength: 3,
            ajax: {
                url: base_url + 'contabilidad/search_accounts',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        limit: 100
                    };
                },
                processResults: function (data) {
                    if (!data || !data.data) {
                        console.warn('No data returned from search_accounts');
                        return { results: [] };
                    }
                    console.log('Found accounts:', data.data.length);
                    return {
                        results: data.data.map(function(account) {
                            return {
                                id: account.id,
                                text: (account.code ? account.code + ' - ' : '') + account.name,
                                code: account.code,
                                name: account.name
                            };
                        })
                    };
                },
                cache: true
            },
            templateResult: function(account) {
                if (account.loading) return account.text;
                if (!account.id) return null;
                
                // Highlight code and name separately with better styling
                if (account.code && account.name) {
                    var $result = $('<div style="padding:4px 0;">');
                    $result.append($('<strong style="color:#667eea;font-size:14px;">' + account.code + '</strong>'));
                    $result.append($('<span style="color:#4b5563;margin-left:8px;font-size:13px;">' + account.name + '</span>'));
                    return $result;
                }
                return $('<span>' + account.text + '</span>');
            },
            templateSelection: function(account) {
                if (account.code && account.name) {
                    var text = account.code + ' - ' + account.name;
                    // Truncate if too long
                    if (text.length > 45) {
                        text = text.substring(0, 45) + '...';
                    }
                    return text;
                }
                return account.text || account.id;
            }
        }).on('select2:open', function() {
            // Style the dropdown container
            $('.select2-container--open .select2-dropdown').css({
                'border': '2px solid #667eea',
                'border-radius': '8px',
                'box-shadow': '0 4px 12px rgba(102, 126, 234, 0.2)'
            });
            
            // Focus on the search field when dropdown opens
            setTimeout(function() {
                $('.select2-search--dropdown .select2-search__field').focus();
            }, 100);
        });
    }

    // Close modal
    const closeButtons = [
        document.getElementById('btnCancelModal'),
        document.getElementById('btnCancelModalFooter')
    ];
    closeButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                // Destroy all Select2 instances before removing modal
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    modal.querySelectorAll('.account-select').forEach(select => {
                        try {
                            $(select).select2('destroy');
                        } catch(e) {}
                    });
                }
                
                // Find the modal container and clear it
                const modalContainer = document.getElementById('modalContainer');
                if (modalContainer) {
                    modalContainer.innerHTML = '';
                } else {
                    // Fallback: remove the modal element itself
                    modal.remove();
                }
            });
        }
    });
    
    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            // Destroy all Select2 instances before removing modal
            if (typeof $ !== 'undefined' && $.fn.select2) {
                modal.querySelectorAll('.account-select').forEach(select => {
                    try {
                        $(select).select2('destroy');
                    } catch(e) {}
                });
            }
            
            // Find the modal container and clear it
            const modalContainer = document.getElementById('modalContainer');
            if (modalContainer) {
                modalContainer.innerHTML = '';
            } else {
                // Fallback: remove the modal element itself
                modal.remove();
            }
        }
    });

    // Submit form
    const form = document.getElementById('formNewEntry');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Submit iniciado');
            
            // Validate balanced entry
            let totalDebit = 0;
            let totalCredit = 0;
            modal.querySelectorAll('.line-debit-mxn').forEach(inp => {
                totalDebit += parseFloat(inp.value) || 0;
            });
            modal.querySelectorAll('.line-credit-mxn').forEach(inp => {
                totalCredit += parseFloat(inp.value) || 0;
            });

            console.log('Total Debe:', totalDebit, 'Total Haber:', totalCredit);

            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                alert('El asiento no está cuadrado. La diferencia es: C$' + Math.abs(totalDebit - totalCredit).toFixed(2));
                return;
            }

            // Validar que haya al menos 2 líneas con datos
            let linesWithData = 0;
            modal.querySelectorAll('.entry-line').forEach(line => {
                const accountId = line.querySelector('.account-select')?.value;
                const debit = parseFloat(line.querySelector('.line-debit-mxn')?.value) || 0;
                const credit = parseFloat(line.querySelector('.line-credit-mxn')?.value) || 0;
                if (accountId && (debit > 0 || credit > 0)) {
                    linesWithData++;
                }
            });

            console.log('Líneas con datos:', linesWithData);

            if (linesWithData < 2) {
                alert('Debe agregar al menos 2 movimientos contables con cuenta y monto');
                return;
            }

            const formData = new FormData(form);
                        // Si es tesorería, agregar source_type/source_id
                        if (movimientoId) {
                            formData.append('source_type', 'teso_movimiento');
                            formData.append('source_id', movimientoId);
                        }
            
            // Debug: mostrar datos del formulario
            console.log('Datos del formulario:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            const endpoint = window.ADD_ENTRY_URL || (base_url + 'contabilidad/add_entry');
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(r => {
                console.log('Response status:', r.status);
                return r.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        alert('Asiento guardado correctamente');
                        modal.remove();
                        // Recargar la página para ver el nuevo asiento
                        location.reload();
                    } else {
                        alert('Error al guardar: ' + (data.message || 'Error desconocido'));
                        console.error('Error data:', data);
                    }
                } catch(parseError) {
                    console.error('Error parsing JSON:', parseError);
                    console.error('Response text:', text);
                    alert('Error al procesar respuesta del servidor. Revisa la consola para más detalles.');
                }
            })
            .catch(err => {
                console.error('Error de red:', err);
                alert('Error de conexión al guardar el asiento. Revisa la consola.');
            });
        });
    }

    // Initialize Select2 for all existing account selects on modal load
    modal.querySelectorAll('.entry-line').forEach(line => {
        initializeSelect2ForLine(line);
    });

    // Initial totals calculation
    updateTotals();
};
