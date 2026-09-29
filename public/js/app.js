/* ============================================================
   BillInventory Pro — Core Application JavaScript
   ============================================================ */

// ==================== SIDEBAR ====================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    sidebar.classList.toggle('collapsed');
    main.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

function toggleMenuGroup(el) {
    const group = el.closest('.menu-group');
    group.classList.toggle('open');
}

// ==================== THEME MANAGEMENT ====================
function initTheme() {
    const savedTheme = localStorage.getItem('appTheme') || 'dark';
    setTheme(savedTheme, false);
}

function setTheme(theme, save = true) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.documentElement.setAttribute('data-theme', theme);
    if (theme === 'light') {
        document.documentElement.classList.add('light-mode');
    } else {
        document.documentElement.classList.remove('light-mode');
    }

    const themeIcon = document.getElementById('themeIcon');
    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeIcon) {
        if (theme === 'light') {
            themeIcon.className = 'fas fa-moon';
            if (themeBtn) themeBtn.title = 'Switch to Night Mode';
        } else {
            themeIcon.className = 'fas fa-sun';
            if (themeBtn) themeBtn.title = 'Switch to Light Mode';
        }
    }

    if (save) {
        localStorage.setItem('appTheme', theme);
    }
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || localStorage.getItem('appTheme') || 'dark';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme, true);
    showToast(newTheme === 'light' ? 'Switched to Light Mode' : 'Switched to Night Mode', 'info', 2000);
}

// ==================== GLOBAL NAVBAR SEARCH ====================
function initGlobalSearch() {
    const searchInput = document.getElementById('globalSearch');
    const dropdown = document.getElementById('searchResultsDropdown');
    if (!searchInput || !dropdown) return;

    let debounceTimer = null;
    let selectedIndex = -1;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            dropdown.innerHTML = '';
            dropdown.style.display = 'none';
            selectedIndex = -1;
            return;
        }

        dropdown.style.display = 'block';
        dropdown.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

        debounceTimer = setTimeout(async () => {
            try {
                const baseUrl = window.APP_BASE_URL || '';
                const response = await fetch(`${baseUrl}/api/search?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                renderSearchResults(data.results || [], query);
            } catch (err) {
                console.error('Search error:', err);
                dropdown.innerHTML = '<div class="search-empty"><i class="fas fa-exclamation-triangle"></i> Error searching</div>';
            }
        }, 200);
    });

    searchInput.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('.search-result-item');
        if (!items.length || dropdown.style.display === 'none') return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateActiveItem(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                items[selectedIndex].click();
            } else if (items.length > 0) {
                items[0].click();
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
            searchInput.blur();
        }
    });

    function updateActiveItem(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    function renderSearchResults(results, query) {
        selectedIndex = -1;
        if (!results || results.length === 0) {
            dropdown.innerHTML = `<div class="search-empty"><i class="fas fa-search"></i> No results found for "<strong>${escapeHtml(query)}</strong>"</div>`;
            return;
        }

        const groups = {};
        results.forEach(r => {
            const cat = r.category || 'Other';
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push(r);
        });

        let html = '';
        const categoryOrder = ['Navigation', 'Products', 'Customers', 'Vendors', 'Sales'];
        
        categoryOrder.forEach(cat => {
            if (groups[cat] && groups[cat].length) {
                html += `<div class="search-category-header"><i class="fas fa-layer-group"></i> ${cat}</div>`;
                groups[cat].forEach(item => {
                    html += `
                        <a href="${item.url}" class="search-result-item">
                            <div class="search-result-icon">
                                <i class="${item.icon || 'fas fa-chevron-right'}"></i>
                            </div>
                            <div class="search-result-body">
                                <div class="search-result-title">${escapeHtml(item.title)}</div>
                                <div class="search-result-subtitle">${escapeHtml(item.subtitle || '')}</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size:0.75rem; opacity:0.5;"></i>
                        </a>
                    `;
                });
            }
        });

        // Other categories
        Object.keys(groups).forEach(cat => {
            if (!categoryOrder.includes(cat) && groups[cat].length) {
                html += `<div class="search-category-header"><i class="fas fa-folder"></i> ${cat}</div>`;
                groups[cat].forEach(item => {
                    html += `
                        <a href="${item.url}" class="search-result-item">
                            <div class="search-result-icon"><i class="${item.icon || 'fas fa-chevron-right'}"></i></div>
                            <div class="search-result-body">
                                <div class="search-result-title">${escapeHtml(item.title)}</div>
                                <div class="search-result-subtitle">${escapeHtml(item.subtitle || '')}</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size:0.75rem; opacity:0.5;"></i>
                        </a>
                    `;
                });
            }
        });

        dropdown.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Dismiss on click outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.header-search')) {
            dropdown.style.display = 'none';
        }
    });

    // Reopen when clicking search input if text exists
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2 && dropdown.innerHTML.trim() !== '') {
            dropdown.style.display = 'block';
        }
    });
}

// Restore sidebar state & initialize features
document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initGlobalSearch();

    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('sidebar')?.classList.add('collapsed');
        document.getElementById('mainContent')?.classList.add('sidebar-collapsed');
    }

    // Mobile sidebar
    if (window.innerWidth <= 1024) {
        const sidebar = document.getElementById('sidebar');
        document.getElementById('sidebarToggle')?.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });

        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }
});

// ==================== COLLAPSE TOGGLE HANDLER ====================
document.addEventListener('click', function (e) {
    const toggleBtn = e.target.closest('[data-bs-toggle="collapse"]');
    if (!toggleBtn) return;
    const targetSelector = toggleBtn.getAttribute('data-bs-target') || toggleBtn.getAttribute('href');
    if (!targetSelector) return;
    const targetEl = document.querySelector(targetSelector);
    if (!targetEl) return;

    e.preventDefault();
    if (targetEl.classList.contains('show') || targetEl.style.display === 'block') {
        targetEl.classList.remove('show');
        targetEl.style.display = 'none';
    } else {
        targetEl.classList.add('show');
        targetEl.style.display = 'block';
    }
});

// ==================== DROPDOWN ====================
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const wasOpen = menu.classList.contains('show');

    // Close all dropdowns
    document.querySelectorAll('.dropdown-menu.show').forEach(d => d.classList.remove('show'));

    if (!wasOpen) {
        menu.classList.add('show');
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(d => d.classList.remove('show'));
    }
});

// ==================== TOAST NOTIFICATIONS ====================
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-icon"><i class="fas ${icons[type] || icons.info}"></i></span>
        <span class="toast-message">${message}</span>
        <button class="toast-dismiss" onclick="dismissToast(this)"><i class="fas fa-times"></i></button>
    `;

    container.appendChild(toast);

    setTimeout(() => dismissToast(toast.querySelector('.toast-dismiss')), duration);
}

function dismissToast(btn) {
    const toast = btn.closest ? btn.closest('.toast') : btn;
    if (!toast) return;
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
}

// ==================== MODAL ====================
function openModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal on overlay click
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay') && e.target.classList.contains('active')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Close modal on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});

// ==================== FULLSCREEN ====================
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// ==================== AJAX HELPERS ====================
async function ajaxPost(url, data, options = {}) {
    try {
        const formData = data instanceof FormData ? data : new FormData();
        if (!(data instanceof FormData)) {
            Object.keys(data).forEach(key => formData.append(key, data[key]));
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message || 'Operation successful!', 'success');
            if (options.onSuccess) options.onSuccess(result);
            if (options.reload) setTimeout(() => location.reload(), 800);
            if (options.closeModal) closeModal(options.closeModal);
        } else {
            if (result.errors) {
                Object.values(result.errors).forEach(err => showToast(err, 'error'));
            } else {
                showToast(result.message || 'Something went wrong.', 'error');
            }
            if (options.onError) options.onError(result);
        }

        return result;
    } catch (error) {
        showToast('Network error. Please try again.', 'error');
        console.error('AJAX Error:', error);
        return { success: false };
    }
}

async function ajaxGet(url) {
    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        return await response.json();
    } catch (error) {
        showToast('Network error. Please try again.', 'error');
        console.error('AJAX Error:', error);
        return { success: false };
    }
}

// ==================== FORM HELPERS ====================
function getFormData(formId) {
    return new FormData(document.getElementById(formId));
}

function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) form.reset();
}

function setFormValues(formId, data) {
    const form = document.getElementById(formId);
    if (!form) return;
    Object.keys(data).forEach(key => {
        const field = form.querySelector(`[name="${key}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = !!data[key];
            } else {
                field.value = data[key] ?? '';
            }
        }
    });
}

// ==================== CONFIRM DELETE ====================
function confirmDelete(url, name = 'this item') {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        ajaxPost(url, {}, { reload: true });
    }
}

// ==================== TABLE SEARCH ====================
function setupTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

// ==================== NUMBER FORMATTING ====================
function formatCurrency(amount, symbol = '₹') {
    return symbol + parseFloat(amount || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('en-IN');
}

// ==================== GLOBAL NON-NEGATIVE & QUANTITY SANITIZATION ====================
(function() {
    function isNonNegativeField(input) {
        if (!input || input.tagName !== 'INPUT') return false;
        const name = (input.name || '').toLowerCase();
        const id = (input.id || '').toLowerCase();
        
        if (input.dataset && input.dataset.noNegative === 'true') return true;
        
        // Quantity, price, stock, discount, rate fields
        const targets = [
            'quantity[]', 'quantity', 'qty', 'qty[]',
            'unit_price[]', 'unit_price', 'price',
            'purchase_price', 'selling_price',
            'min_stock_level', 'reorder_qty',
            'paid_amount', 'amount', 'credit_limit',
            'discount_percent', 'discount', 'rate'
        ];
        if (targets.includes(name) || targets.includes(id)) return true;

        // Any input[type="number"] that has min >= 0
        if (input.type === 'number' && input.hasAttribute('min')) {
            const min = parseFloat(input.getAttribute('min'));
            if (!isNaN(min) && min >= 0) return true;
        }

        return false;
    }

    // 1. Prevent typing minus sign or exponential 'e'/'E' in non-negative / quantity inputs
    document.addEventListener('keydown', function(e) {
        if (!isNonNegativeField(e.target)) return;

        if (e.key === '-' || e.key === 'Minus' || e.code === 'Minus' || e.code === 'NumpadSubtract' || e.key === 'e' || e.key === 'E') {
            e.preventDefault();
        }
    }, true);

    // 2. Prevent pasting or entering negative values
    document.addEventListener('input', function(e) {
        const input = e.target;
        if (!isNonNegativeField(input)) return;

        if (typeof input.value === 'string' && input.value.includes('-')) {
            input.value = input.value.replace(/-/g, '');
        }

        const val = parseFloat(input.value);
        if (!isNaN(val) && val < 0) {
            input.value = Math.abs(val);
        }
    }, true);

    // 3. On blur, clamp to minimum if specified and enforce positive value for quantities
    document.addEventListener('blur', function(e) {
        const input = e.target;
        if (!isNonNegativeField(input)) return;

        const name = (input.name || '').toLowerCase();
        const isQty = name.includes('quantity') || name.includes('qty');

        if (input.value !== '') {
            let val = parseFloat(input.value);
            if (isNaN(val) || (isQty && val <= 0)) {
                const min = parseFloat(input.getAttribute('min')) || (isQty ? 1 : 0);
                input.value = min;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            } else if (input.hasAttribute('min')) {
                const min = parseFloat(input.getAttribute('min'));
                if (!isNaN(min) && val < min) {
                    input.value = min;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    }, true);
})();

