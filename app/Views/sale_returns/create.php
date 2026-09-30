<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 fw-bold" style="color: var(--text-primary, #F1F5F9);">
            <i class="fas fa-undo text-danger me-2"></i><?= esc($pageTitle) ?>
        </h4>
        <a href="<?= base_url('sales-returns') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Returns List
        </a>
    </div>
    <div class="card-body p-4">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Invoice Lookup Section (Enhanced Dark Theme High-Contrast) -->
        <div class="card mb-4" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.35);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="card-title fw-bold mb-0" style="color: #818CF8;">
                        <i class="fas fa-file-invoice me-2"></i>Fetch From Sales Invoice
                    </h5>
                    <span class="badge" style="background: rgba(99, 102, 241, 0.25); color: #C7D2FE; border: 1px solid rgba(99, 102, 241, 0.5); font-size: 0.75rem; letter-spacing: 0.5px;">RECOMMENDED</span>
                </div>
                <p class="mb-3" style="color: #CBD5E1; font-size: 0.875rem;">
                    Enter or select the customer's original Invoice No (e.g. <code style="background: rgba(15, 23, 42, 0.85); color: #38BDF8; padding: 2px 7px; border-radius: 4px; border: 1px solid #334155; font-weight: 600;">INV-XXXX</code>) to automatically load customer details, warehouse, sold products, unit prices, and tax rates.
                </p>
                <div class="row align-items-center g-2">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text" style="background: var(--bg-input, #0F172A); border-color: var(--border, #334155); color: #94A3B8;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="invoiceSearchInput" class="form-control" list="recentInvoiceList" placeholder="Type or select Invoice No (e.g. INV-...)" autocomplete="off">
                            <datalist id="recentInvoiceList">
                                <?php if (!empty($recentSales)): ?>
                                    <?php foreach ($recentSales as $rs): ?>
                                        <option value="<?= esc($rs->invoice_no) ?>">
                                            <?= esc($rs->invoice_no) ?> &mdash; <?= esc($rs->customer_name ?? 'Walk-in') ?> (₹<?= number_format($rs->total_amount, 2) ?>) - <?= date('d M Y', strtotime($rs->sale_date)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" id="btnFetchInvoice" onclick="fetchInvoiceData()">
                            <i class="fas fa-download me-1"></i> Fetch Invoice Data
                        </button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger d-none" id="btnClearInvoice" onclick="clearFetchedInvoice()">
                            <i class="fas fa-times me-1"></i> Clear Fetched Invoice
                        </button>
                    </div>
                </div>

                <!-- Banner shown after successful fetch -->
                <div id="invoiceLoadedBanner" class="alert d-none mt-3 mb-0 py-2 d-flex flex-wrap align-items-center justify-content-between" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #6EE7B7;">
                    <div>
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong style="color: #F8FAFC;">Invoice Loaded:</strong> <span id="bannerInvoiceNo" class="badge bg-primary fs-6 me-2"></span>
                        <span class="me-2" style="color: #E2E8F0;">Customer: <strong id="bannerCustomer" style="color: #FFFFFF;"></strong></span>
                        <span class="me-2" style="color: #E2E8F0;">Warehouse: <strong id="bannerWarehouse" style="color: #FFFFFF;"></strong></span>
                        <span style="color: #E2E8F0;">Total Invoiced: <strong id="bannerTotal" style="color: #34D399;"></strong></span>
                    </div>
                    <span class="badge bg-success"><i class="fas fa-link me-1"></i> Invoice Linked</span>
                </div>

                <!-- Error banner -->
                <div id="invoiceErrorBanner" class="alert alert-danger d-none mt-3 mb-0 py-2" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #FCA5A5;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="invoiceErrorMessage" style="color: #FEE2E2;"></span>
                </div>
            </div>
        </div>

        <form action="<?= base_url('sales-returns/store') ?>" method="post" id="returnForm">
            <!-- Hidden Sale ID -->
            <input type="hidden" name="sale_id" id="saleIdInput" value="">

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="color: #E2E8F0;">Return Date *</label>
                    <input type="datetime-local" name="return_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="color: #E2E8F0;">Warehouse (Return To) *</label>
                    <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
                        <option value="">Select Warehouse</option>
                        <?php foreach ($warehouses as $w): ?>
                            <option value="<?= $w->id ?>"><?= esc($w->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="color: #E2E8F0;">Customer</label>
                    <select name="customer_id" id="customerSelect" class="form-select">
                        <option value="">Walk-in Customer</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c->id ?>"><?= esc($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Product Selection / Returned Items -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2" style="border-color: var(--border, #334155) !important;">
                    <h5 class="mb-0 fw-bold" style="color: #818CF8;">
                        <i class="fas fa-box-open me-2"></i>Returned Items
                    </h5>
                    <small style="color: #94A3B8;">Return quantities and taxes are verified upon save</small>
                </div>

                <!-- Optional manual product add -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--bg-input, #0F172A); border-color: var(--border, #334155); color: #818CF8;">
                            <i class="fas fa-plus-circle"></i>
                        </span>
                        <select id="productSearch" class="form-select" onchange="addProduct()">
                            <option value="">Or select / search product to return manually...</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p->id ?>" 
                                        data-name="<?= esc($p->name) ?>" 
                                        data-price="<?= $p->selling_price ?>"
                                        data-sku="<?= esc($p->sku ?? '') ?>"
                                        data-tax-rate="<?= (float)($p->tax_rate ?? 0) ?>"
                                        data-tax-type="<?= esc($p->tax_type ?? 'exclusive') ?>"
                                        data-tax-name="<?= esc($p->tax_name ?? 'GST') ?>">
                                    <?= esc($p->name) ?> <?= !empty($p->sku) ? '('.esc($p->sku).')' : '' ?> - ₹<?= number_format($p->selling_price, 2) ?> <?= ($p->tax_rate > 0) ? '(+'.$p->tax_rate.'% tax)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead style="background: rgba(255, 255, 255, 0.04); border-color: var(--border, #334155);">
                            <tr style="color: #F1F5F9;">
                                <th style="color: #F1F5F9;">Product Details</th>
                                <th width="160" style="color: #F1F5F9;">Condition & QC</th>
                                <th width="100" class="text-center" style="color: #F1F5F9;">Sold Qty</th>
                                <th width="140" style="color: #F1F5F9;">Unit Price (₹)</th>
                                <th width="120" style="color: #F1F5F9;">Return Qty</th>
                                <th width="130" class="text-end" style="color: #F1F5F9;">Subtotal (₹)</th>
                                <th width="150" class="text-end" style="color: #F1F5F9;">GST( inclu. all tax )</th>
                                <th width="140" class="text-end" style="color: #F1F5F9;">Total Refund (₹)</th>
                                <th width="50" class="text-center" style="color: #F1F5F9;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items populated dynamically -->
                        </tbody>
                        <tbody id="emptyTableNotice">
                            <tr>
                                <td colspan="9" class="text-center py-4" style="color: #94A3B8;">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block" style="color: #64748B;"></i>
                                    No items added yet. Fetch an invoice above or select a product manually.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 1px solid var(--border, #334155);">
                                <th colspan="5" class="text-end" style="color: #CBD5E1;">Subtotal:</th>
                                <th colspan="3" class="text-end pe-3">
                                    <input type="number" step="0.01" name="subtotal" id="calcSubtotal" class="form-control-plaintext fw-bold text-end pe-2" style="color: #F1F5F9;" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end fw-semibold" style="color: #818CF8;">GST( inclu. all tax ):</th>
                                <th colspan="3" class="text-end pe-3">
                                    <input type="number" step="0.01" name="tax_amount" id="calcTax" class="form-control-plaintext fw-bold text-end pe-2" style="color: #818CF8;" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                            <tr style="border-top: 2px solid #EF4444; background: rgba(239, 68, 68, 0.08);">
                                <th colspan="5" class="text-end fs-5 text-danger fw-bold">Total Refund Amount:</th>
                                <th colspan="3" class="text-end pe-3">
                                    <input type="number" step="0.01" name="total_amount" id="calcTotal" class="form-control-plaintext fw-bold fs-5 text-end pe-2 text-danger" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="color: #E2E8F0;">Return Reason / Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Provide any relevant details or reason for the customer's return..."></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: var(--border, #334155) !important;">
                <a href="<?= base_url('sales-returns') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-danger btn-lg px-4" id="saveBtn" disabled>
                    <i class="fas fa-undo me-1"></i> Process Sale Return
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Fetch sales invoice data via AJAX
    function fetchInvoiceData() {
        const invoiceInput = document.getElementById('invoiceSearchInput');
        const invoiceNo = invoiceInput.value.trim();
        const btn = document.getElementById('btnFetchInvoice');
        const banner = document.getElementById('invoiceLoadedBanner');
        const errBanner = document.getElementById('invoiceErrorBanner');
        const errMessage = document.getElementById('invoiceErrorMessage');
        const clearBtn = document.getElementById('btnClearInvoice');

        if (!invoiceNo) {
            errMessage.innerText = 'Please enter or select an Invoice Number to fetch.';
            errBanner.classList.remove('d-none');
            banner.classList.add('d-none');
            return;
        }

        const originalBtnHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Fetching...';
        btn.disabled = true;
        errBanner.classList.add('d-none');

        fetch('<?= base_url('sales-returns/fetch-invoice') ?>?invoice_no=' + encodeURIComponent(invoiceNo))
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;

                if (!data.success) {
                    errMessage.innerText = data.message || 'Failed to fetch invoice.';
                    errBanner.classList.remove('d-none');
                    banner.classList.add('d-none');
                    return;
                }

                // Successful fetch: Populate Sale ID
                document.getElementById('saleIdInput').value = data.sale.id;

                // Populate Customer & Warehouse
                if (data.sale.customer_id) {
                    document.getElementById('customerSelect').value = data.sale.customer_id;
                } else {
                    document.getElementById('customerSelect').value = '';
                }

                if (data.sale.warehouse_id) {
                    document.getElementById('warehouseSelect').value = data.sale.warehouse_id;
                }

                // Populate Banner with high visibility text
                document.getElementById('bannerInvoiceNo').innerText = data.sale.invoice_no;
                document.getElementById('bannerCustomer').innerText = data.sale.customer_name;
                document.getElementById('bannerWarehouse').innerText = data.sale.warehouse_name;
                document.getElementById('bannerTotal').innerText = '₹' + parseFloat(data.sale.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                banner.classList.remove('d-none');
                clearBtn.classList.remove('d-none');

                // Clear current table items
                const tbody = document.querySelector('#itemsTable tbody');
                tbody.innerHTML = '';

                // Populate invoice items
                let addedCount = 0;
                data.items.forEach(item => {
                    const returnableQty = parseFloat(item.returnable_quantity) || 0;
                    if (returnableQty <= 0) {
                        return; // item already fully returned
                    }

                    appendItemRow({
                        id: item.product_id,
                        name: item.product_name,
                        sku: item.sku,
                        soldQty: item.sold_quantity,
                        returnableQty: returnableQty,
                        price: item.unit_price,
                        taxRate: item.tax_rate,
                        taxType: item.tax_type,
                        taxName: item.tax_name,
                        defaultQty: returnableQty
                    });
                    addedCount++;
                });

                if (addedCount === 0) {
                    errMessage.innerText = 'All items from this invoice have already been returned in full.';
                    errBanner.classList.remove('d-none');
                }

                calculateTotals();
            })
            .catch(err => {
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;
                errMessage.innerText = 'Error connecting to server. Please try again.';
                errBanner.classList.remove('d-none');
                console.error(err);
            });
    }

    // Clear fetched invoice
    function clearFetchedInvoice() {
        document.getElementById('saleIdInput').value = '';
        document.getElementById('invoiceSearchInput').value = '';
        document.getElementById('invoiceLoadedBanner').classList.add('d-none');
        document.getElementById('btnClearInvoice').classList.add('d-none');
        document.getElementById('invoiceErrorBanner').classList.add('d-none');

        // Reset items
        document.querySelector('#itemsTable tbody').innerHTML = '';
        calculateTotals();
    }

    // Add manual product from dropdown
    function addProduct() {
        const select = document.getElementById('productSearch');
        if (select.value === "") return;

        const option = select.options[select.selectedIndex];
        const id = select.value;
        const name = option.getAttribute('data-name');
        const sku = option.getAttribute('data-sku') || '';
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        const taxRate = parseFloat(option.getAttribute('data-tax-rate')) || 0;
        const taxType = option.getAttribute('data-tax-type') || 'exclusive';
        const taxName = option.getAttribute('data-tax-name') || 'GST';

        // Check if row already exists
        let exists = false;
        document.querySelectorAll('input[name="product_id[]"]').forEach(input => {
            if (input.value == id) {
                exists = true;
                let tr = input.closest('tr');
                let qtyInput = tr.querySelector('input[name="quantity[]"]');
                let cur = parseFloat(qtyInput.value) || 0;
                let maxQty = parseFloat(qtyInput.getAttribute('max'));
                if (!isNaN(maxQty) && cur + 1 > maxQty) {
                    alert(`Maximum returnable quantity for this item is ${maxQty}.`);
                    return;
                }
                qtyInput.value = (cur + 1);
                calculateRow(qtyInput);
            }
        });

        if (!exists) {
            appendItemRow({
                id: id,
                name: name,
                sku: sku,
                soldQty: null,
                returnableQty: null,
                price: price,
                taxRate: taxRate,
                taxType: taxType,
                taxName: taxName,
                defaultQty: 1
            });
        }

        select.value = "";
        calculateTotals();
    }

    // Append row helper with high visibility text
    function appendItemRow(item) {
        const tbody = document.querySelector('#itemsTable tbody');
        const emptyNotice = document.getElementById('emptyTableNotice');
        if (emptyNotice) emptyNotice.style.display = 'none';

        const tr = document.createElement('tr');
        const maxAttr = item.returnableQty ? `max="${item.returnableQty}"` : '';
        const soldDisplay = item.soldQty !== null 
            ? `<span class="badge border" style="background: rgba(255,255,255,0.06); color: #F1F5F9; border-color: var(--border, #334155) !important;">${item.soldQty} <small style="color: #94A3B8;">(Max: ${item.returnableQty})</small></span>` 
            : `<span style="color: #94A3B8;">&mdash;</span>`;

        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="color: #F1F5F9;">${item.name}</div>
                ${item.sku ? `<small style="color: #94A3B8;">SKU: ${item.sku}</small>` : ''}
                <input type="hidden" name="product_id[]" value="${item.id}">
                <input type="hidden" name="tax_rate[]" class="row-tax-rate" value="${item.taxRate}">
                <input type="hidden" name="tax_type[]" class="row-tax-type" value="${item.taxType}">
                <input type="hidden" name="tax_amount[]" class="row-tax-amount-val" value="0">
                <input type="hidden" name="total[]" class="row-total-val" value="0">
            </td>
            <td>
                <select name="item_condition[]" class="form-select form-select-sm">
                    <option value="sealed">Seal Intact (To Stock)</option>
                    <option value="broken_seal">Broken Seal (QC Req.)</option>
                </select>
            </td>
            <td class="text-center">
                ${soldDisplay}
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control form-control-sm text-end row-price" value="${parseFloat(item.price).toFixed(2)}" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
            </td>
            <td>
                <input type="number" step="any" min="0.01" ${maxAttr} name="quantity[]" class="form-control form-control-sm text-center row-qty" value="${item.defaultQty}" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
            </td>
            <td>
                <input type="text" class="form-control-plaintext form-control-sm text-end fw-semibold row-subtotal" style="color: #F1F5F9;" value="0.00" readonly>
            </td>
            <td class="text-end">
                <span class="badge border me-1 row-tax-badge" style="background: rgba(99, 102, 241, 0.15); color: #818CF8; border-color: rgba(99, 102, 241, 0.3) !important;">${item.taxRate > 0 ? item.taxRate + '%' : '0%'}</span>
                <span class="fw-semibold row-tax-text" style="color: #818CF8;">₹0.00</span>
            </td>
            <td>
                <input type="text" class="form-control-plaintext form-control-sm text-end fw-bold text-danger row-total-text" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" title="Remove Item" onclick="removeRow(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        calculateRow(tr.querySelector('.row-qty'));
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateTotals();
    }

    function calculateRow(elem) {
        const tr = elem.closest('tr');
        const priceInput = tr.querySelector('.row-price');
        const qtyInput = tr.querySelector('.row-qty');
        const taxRate = parseFloat(tr.querySelector('.row-tax-rate').value) || 0;
        const taxType = (tr.querySelector('.row-tax-type').value || 'exclusive').toLowerCase();

        let price = Math.max(0, parseFloat(priceInput.value) || 0);
        let qty = parseFloat(qtyInput.value) || 0;

        // Enforce max if linked to invoice
        const maxQty = parseFloat(qtyInput.getAttribute('max'));
        if (!isNaN(maxQty) && qty > maxQty) {
            qty = maxQty;
            qtyInput.value = maxQty;
            alert(`Maximum returnable quantity for this item is ${maxQty}.`);
        }

        if (qty < 0) {
            qty = Math.abs(qty) || 1;
            qtyInput.value = qty;
        }

        const lineSubtotal = qty * price;
        let lineTax = 0;
        let lineTotal = lineSubtotal;

        if (taxRate > 0) {
            if (taxType === 'inclusive') {
                lineTax = lineSubtotal - (lineSubtotal / (1 + (taxRate / 100)));
                lineTotal = lineSubtotal;
            } else {
                lineTax = (lineSubtotal * taxRate) / 100;
                lineTotal = lineSubtotal + lineTax;
            }
        }

        // Update displays and hidden inputs
        tr.querySelector('.row-subtotal').value = lineSubtotal.toFixed(2);
        tr.querySelector('.row-tax-amount-val').value = lineTax.toFixed(2);
        tr.querySelector('.row-tax-text').innerText = '₹' + lineTax.toFixed(2);
        tr.querySelector('.row-total-val').value = lineTotal.toFixed(2);
        tr.querySelector('.row-total-text').value = lineTotal.toFixed(2);

        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let grandTotal = 0;
        let rowCount = 0;

        document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
            const rowSub = parseFloat(tr.querySelector('.row-subtotal')?.value) || 0;
            const rowTax = parseFloat(tr.querySelector('.row-tax-amount-val')?.value) || 0;
            const rowTot = parseFloat(tr.querySelector('.row-total-val')?.value) || 0;

            subtotal += rowSub;
            totalTax += rowTax;
            grandTotal += rowTot;
            rowCount++;
        });

        const emptyNotice = document.getElementById('emptyTableNotice');
        if (emptyNotice) {
            emptyNotice.style.display = rowCount === 0 ? '' : 'none';
        }

        document.getElementById('calcSubtotal').value = subtotal.toFixed(2);
        document.getElementById('calcTax').value = totalTax.toFixed(2);
        document.getElementById('calcTotal').value = grandTotal.toFixed(2);

        document.getElementById('saveBtn').disabled = rowCount === 0 || grandTotal <= 0;
    }

    // Allow pressing Enter in invoiceSearchInput to trigger fetch
    document.getElementById('invoiceSearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchInvoiceData();
        }
    });
</script>
<?= $this->endSection() ?>
