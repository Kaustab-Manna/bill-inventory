<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>
<style>
    /* ============================================================
       SALES INVOICES & PAYMENT STATUS FILTERING SUITE
       ============================================================ */

    .sales-page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* 1. Filter KPI Tabs */
    .status-filter-deck {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
    }

    .filter-tab-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-decoration: none;
        color: var(--text-primary);
        position: relative;
        overflow: hidden;
    }
    .filter-tab-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        color: var(--text-primary);
    }
    .filter-tab-card.active {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(15, 23, 42, 0.6));
        box-shadow: 0 0 0 2px var(--primary), 0 8px 24px rgba(99, 102, 241, 0.2);
    }

    .filter-tab-card.tab-paid.active {
        border-color: #10b981;
        box-shadow: 0 0 0 2px #10b981, 0 8px 24px rgba(16, 185, 129, 0.2);
    }
    .filter-tab-card.tab-partial.active {
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px #f59e0b, 0 8px 24px rgba(245, 158, 11, 0.2);
    }
    .filter-tab-card.tab-pending.active {
        border-color: #ef4444;
        box-shadow: 0 0 0 2px #ef4444, 0 8px 24px rgba(239, 68, 68, 0.2);
    }

    .filter-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .filter-tab-info {
        flex: 1;
    }
    .filter-tab-count {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--text-primary);
    }
    .filter-tab-label {
        font-size: 0.82rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* 2. Filter & Search Toolbar */
    .sales-toolbar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 280px;
    }

    .search-box-wrapper {
        position: relative;
        flex: 1;
        max-width: 420px;
    }
    .search-box-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.88rem;
    }
    .search-box-wrapper input {
        padding-left: 38px;
        background: var(--bg-input);
        border: 1px solid var(--border);
        color: var(--text-primary);
        border-radius: 10px;
    }
    .search-box-wrapper input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .status-select-wrapper select {
        background: var(--bg-input);
        border: 1px solid var(--border);
        color: var(--text-primary);
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 16px;
        cursor: pointer;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* 3. Sales Table Presentation */
    .sales-table-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .badge-status-paid {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 5px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-status-partial {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 5px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-status-pending {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 5px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Empty state */
    .empty-filter-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--text-muted);
    }
    .empty-filter-state i {
        font-size: 3rem;
        margin-bottom: 14px;
        color: var(--border-light);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="sales-page-wrapper">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary);">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Sales Invoices
            </h1>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                Track client billing, filter by payment status (Paid, Partial, Pending), and record collections.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('pos') ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cash-register me-1"></i> POS Billing
            </a>
            <a href="<?= base_url('sales/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Sale Invoice
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Interactive Payment Status Filter KPI Deck -->
    <div class="status-filter-deck">
        <!-- ALL TAB -->
        <div class="filter-tab-card <?= ($currentFilter === 'all' || empty($currentFilter)) ? 'active' : '' ?>" 
             onclick="applyStatusFilter('all')" id="tab-all" title="View all invoices">
            <div class="filter-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="filter-tab-info">
                <div class="filter-tab-count"><?= number_format($counts['all']) ?></div>
                <div class="filter-tab-label">All Invoices</div>
            </div>
        </div>

        <!-- PAID TAB -->
        <div class="filter-tab-card tab-paid <?= ($currentFilter === 'paid') ? 'active' : '' ?>" 
             onclick="applyStatusFilter('paid')" id="tab-paid" title="Filter Paid Invoices">
            <div class="filter-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="filter-tab-info">
                <div class="filter-tab-count" style="color: #10b981;"><?= number_format($counts['paid']) ?></div>
                <div class="filter-tab-label">Paid Full</div>
            </div>
        </div>

        <!-- PARTIAL TAB -->
        <div class="filter-tab-card tab-partial <?= ($currentFilter === 'partial') ? 'active' : '' ?>" 
             onclick="applyStatusFilter('partial')" id="tab-partial" title="Filter Partial Invoices">
            <div class="filter-icon-box" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="filter-tab-info">
                <div class="filter-tab-count" style="color: #f59e0b;"><?= number_format($counts['partial']) ?></div>
                <div class="filter-tab-label">Partial Paid</div>
            </div>
        </div>

        <!-- PENDING / UNPAID TAB -->
        <div class="filter-tab-card tab-pending <?= ($currentFilter === 'pending' || $currentFilter === 'unpaid') ? 'active' : '' ?>" 
             onclick="applyStatusFilter('pending')" id="tab-pending" title="Filter Pending & Unpaid Invoices">
            <div class="filter-icon-box" style="background: rgba(239, 68, 68, 0.12); color: #f87171;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="filter-tab-info">
                <div class="filter-tab-count" style="color: #ef4444;"><?= number_format($counts['pending']) ?></div>
                <div class="filter-tab-label">Pending / Unpaid</div>
            </div>
        </div>
    </div>

    <!-- 2. Filter & Search Toolbar -->
    <div class="sales-toolbar">
        <div class="toolbar-left">
            <div class="search-box-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="salesSearchInput" class="form-control form-control-sm" 
                       placeholder="Search by invoice no, customer name..." 
                       oninput="filterSalesTable()">
            </div>

            <div class="status-select-wrapper">
                <select id="statusDropdownFilter" class="form-select form-select-sm" onchange="applyStatusFilter(this.value)">
                    <option value="all" <?= ($currentFilter === 'all') ? 'selected' : '' ?>>Show: All Statuses</option>
                    <option value="paid" <?= ($currentFilter === 'paid') ? 'selected' : '' ?>>🟢 Paid Only</option>
                    <option value="partial" <?= ($currentFilter === 'partial') ? 'selected' : '' ?>>🟡 Partial Only</option>
                    <option value="pending" <?= ($currentFilter === 'pending' || $currentFilter === 'unpaid') ? 'selected' : '' ?>>🔴 Pending Only</option>
                </select>
            </div>
        </div>

        <div class="toolbar-right">
            <span class="badge bg-secondary" style="font-size:0.8rem; padding: 6px 12px;" id="visibleCounterBadge">
                Showing <strong id="visibleCount"><?= count($sales) ?></strong> of <?= $counts['all'] ?>
            </span>
            <button type="button" class="btn btn-sm btn-ghost" onclick="resetAllFilters()" title="Reset All Filters">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>

    <!-- 3. Sales Invoices Table Card -->
    <div class="sales-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="salesTable">
                <thead style="background:var(--bg-body); border-bottom:1px solid var(--border);">
                    <tr>
                        <th style="padding:14px 18px;">Date & Time</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-end">Balance Due</th>
                        <th class="text-center">Payment Status</th>
                        <th class="text-center" style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">
                    <?php foreach ($sales as $sale): ?>
                        <?php 
                            $statusNormalized = strtolower(trim($sale->status ?? ''));
                            if ($statusNormalized === 'unpaid') $statusNormalized = 'pending';

                            $totalVal = (float)($sale->total_amount ?? 0);
                            $paidVal  = (float)($sale->paid_amount ?? 0);
                            $dueVal   = max(0, $totalVal - $paidVal);
                        ?>
                        <tr class="sales-row" 
                            data-status="<?= esc($statusNormalized) ?>"
                            data-invoice="<?= esc(strtolower($sale->invoice_no ?? '')) ?>"
                            data-customer="<?= esc(strtolower($sale->customer_name ?? 'walk-in customer')) ?>"
                            data-date="<?= esc($sale->sale_date) ?>">
                            
                            <td style="padding:14px 18px;">
                                <div style="font-weight:600; color:var(--text-primary);">
                                    <?= date('d M Y', strtotime($sale->sale_date)) ?>
                                </div>
                                <small class="text-muted"><?= date('h:i A', strtotime($sale->sale_date)) ?></small>
                            </td>

                            <td>
                                <a href="<?= base_url('sales/view/' . $sale->id) ?>" class="text-decoration-none fw-bold" style="color:var(--primary);">
                                    <span class="badge bg-secondary" style="font-family:'JetBrains Mono',monospace; letter-spacing:0.5px;">
                                        <?= esc($sale->invoice_no) ?>
                                    </span>
                                </a>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:28px; height:28px; border-radius:50%; background:var(--bg-body); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.75rem; color:var(--text-muted);">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color:var(--text-primary); font-size:0.9rem;">
                                            <?= esc($sale->customer_name ?? 'Walk-in Customer') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-end fw-bold" style="color:var(--text-primary);">
                                ₹<?= number_format($totalVal, 2) ?>
                            </td>

                            <td class="text-end text-success fw-bold">
                                ₹<?= number_format($paidVal, 2) ?>
                            </td>

                            <td class="text-end">
                                <?php if ($dueVal <= 0.01): ?>
                                    <span class="text-success fw-bold">₹0.00</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold">₹<?= number_format($dueVal, 2) ?></span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php if ($statusNormalized === 'paid'): ?>
                                    <span class="badge-status-paid">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                <?php elseif ($statusNormalized === 'partial'): ?>
                                    <span class="badge-status-partial">
                                        <i class="fas fa-clock"></i> Partial
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status-pending">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= base_url('sales/view/' . $sale->id) ?>" class="btn btn-sm btn-ghost text-info" title="View Full Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($statusNormalized !== 'paid'): ?>
                                    <button type="button" class="btn btn-sm btn-ghost text-success" 
                                            onclick="openQuickPayModal(<?= $sale->id ?>, '<?= esc($sale->invoice_no) ?>', <?= $totalVal ?>, <?= $paidVal ?>)" 
                                            title="Record Payment">
                                        <i class="fas fa-coins"></i>
                                    </button>
                                    <?php endif; ?>

                                    <a href="<?= base_url('sales/delete/' . $sale->id) ?>" class="btn btn-sm btn-ghost text-danger" title="Delete Invoice" 
                                       onclick="return confirm('Are you sure you want to delete invoice <?= esc($sale->invoice_no) ?>? This will restore stock.');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Empty Filter Message -->
            <div class="empty-filter-state d-none" id="emptyFilterBox">
                <i class="fas fa-filter-circle-xmark"></i>
                <h5 class="fw-bold" style="color:var(--text-primary);">No Invoices Found</h5>
                <p style="font-size:0.88rem; max-width:400px; margin:0 auto 16px auto;">
                    No sales invoices match the current filter criteria.
                </p>
                <button type="button" class="btn btn-sm btn-primary" onclick="resetAllFilters()">
                    <i class="fas fa-rotate-left me-1"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Quick Pay Modal -->
<div class="modal fade" id="quickPayModal" tabindex="-1" aria-labelledby="quickPayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background:var(--bg-modal); border:1px solid var(--border); border-radius:var(--radius-lg);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h6 class="modal-title fw-bold" id="quickPayModalLabel">
                    <i class="fas fa-coins text-success me-2"></i> Update Payment
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickPayForm" method="post" action="">
                <div class="modal-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Invoice:</small>
                        <strong id="qpInvoiceNo" style="font-family:'JetBrains Mono',monospace;"></strong>
                    </div>
                    <div class="mb-3 d-flex justify-content-between p-2 rounded" style="background:var(--bg-body); border:1px solid var(--border);">
                        <div>
                            <small class="text-muted d-block">Total</small>
                            <span class="fw-bold" id="qpTotalAmt">₹0.00</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Balance Due</small>
                            <span class="fw-bold text-danger" id="qpDueAmt">₹0.00</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Total Paid Amount (₹) *</label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="qpPaidInput" class="form-control" required>
                        <small class="text-muted">Enter full amount to mark as Paid.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let activeFilter = '<?= esc($currentFilter ?: "all") ?>';

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize with default or query param filter
        applyStatusFilter(activeFilter, false);
    });

    // Apply Filter Function (handles tabs + dropdown + live client filtering)
    function applyStatusFilter(status, updateUrl = true) {
        activeFilter = status.toLowerCase();

        // 1. Update Card Active States
        document.querySelectorAll('.filter-tab-card').forEach(c => c.classList.remove('active'));
        const tabEl = document.getElementById(`tab-${activeFilter}`);
        if (tabEl) {
            tabEl.classList.add('active');
        }

        // 2. Synchronize Dropdown
        const dropdown = document.getElementById('statusDropdownFilter');
        if (dropdown) {
            dropdown.value = activeFilter;
        }

        // 3. Update Browser URL (without page reload)
        if (updateUrl && window.history.pushState) {
            const newUrl = (activeFilter === 'all') 
                ? '<?= base_url("sales") ?>' 
                : '<?= base_url("sales") ?>?status=' + encodeURIComponent(activeFilter);
            window.history.pushState({ path: newUrl }, '', newUrl);
        }

        // 4. Run Table Filter
        filterSalesTable();
    }

    // Filter Table Rows by Status & Search Query
    function filterSalesTable() {
        const query = (document.getElementById('salesSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.sales-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
            const invoice = (row.getAttribute('data-invoice') || '');
            const customer = (row.getAttribute('data-customer') || '');
            const rowText = row.innerText.toLowerCase();

            // Match Status
            let matchesStatus = false;
            if (activeFilter === 'all') {
                matchesStatus = true;
            } else if (activeFilter === 'paid') {
                matchesStatus = (rowStatus === 'paid');
            } else if (activeFilter === 'partial') {
                matchesStatus = (rowStatus === 'partial');
            } else if (activeFilter === 'pending' || activeFilter === 'unpaid') {
                matchesStatus = (rowStatus === 'pending' || rowStatus === 'unpaid');
            }

            // Match Query
            let matchesQuery = true;
            if (query.length > 0) {
                matchesQuery = invoice.includes(query) || customer.includes(query) || rowText.includes(query);
            }

            if (matchesStatus && matchesQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counter & empty state
        document.getElementById('visibleCount').innerText = visibleCount;
        const emptyBox = document.getElementById('emptyFilterBox');
        if (visibleCount === 0) {
            emptyBox.classList.remove('d-none');
        } else {
            emptyBox.classList.add('d-none');
        }
    }

    // Reset All Filters
    function resetAllFilters() {
        document.getElementById('salesSearchInput').value = '';
        applyStatusFilter('all', true);
    }

    // Open Quick Pay Modal
    function openQuickPayModal(saleId, invoiceNo, total, paid) {
        const form = document.getElementById('quickPayForm');
        form.action = '<?= base_url("sales/payment") ?>/' + saleId;

        document.getElementById('qpInvoiceNo').innerText = invoiceNo;
        document.getElementById('qpTotalAmt').innerText = '₹' + parseFloat(total).toLocaleString('en-IN', {minimumFractionDigits: 2});
        
        const due = Math.max(0, total - paid);
        document.getElementById('qpDueAmt').innerText = '₹' + parseFloat(due).toLocaleString('en-IN', {minimumFractionDigits: 2});
        
        const paidInput = document.getElementById('qpPaidInput');
        paidInput.value = parseFloat(total); // default to full payment for convenience
        paidInput.max = total;

        const modal = new bootstrap.Modal(document.getElementById('quickPayModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
