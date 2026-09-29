<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('quotations/store') ?>" method="post" id="quotationForm">
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Quotation Date *</label>
                    <input type="date" name="quotation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Warehouse</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">Select Warehouse</option>
                        <?php foreach ($warehouses as $w): ?>
                            <option value="<?= $w->id ?>"><?= esc($w->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">Walk-in Customer</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c->id ?>"><?= esc($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="mb-4">
                <h5 class="mb-3 border-bottom pb-2">Items</h5>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <select id="productSearch" class="form-select" onchange="addProduct()">
                        <option value="">Select product to add...</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p->id ?>" data-name="<?= esc($p->name) ?>" data-price="<?= $p->selling_price ?>">
                                <?= esc($p->name) ?> - ₹<?= $p->selling_price ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Product Name</th>
                                <th width="150">Unit Price (₹)</th>
                                <th width="150">Quantity</th>
                                <th width="150">Subtotal (₹)</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th>
                                    <input type="number" step="0.01" name="subtotal" id="calcSubtotal" class="form-control-plaintext fw-bold" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Discount (%):</th>
                                <th>
                                    <div style="position: relative;">
                                        <input type="number" step="0.01" min="0" max="100" name="discount_percent" id="calcDiscountPercent" class="form-control text-end pe-4" value="0" onkeyup="calculateTotals()" onchange="calculateTotals()">
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;">%</span>
                                    </div>
                                    <input type="hidden" name="discount" id="calcDiscount" value="0.00">
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end fs-5">Total Amount:</th>
                                <th>
                                    <input type="number" step="0.01" name="total_amount" id="calcTotal" class="form-control-plaintext fw-bold fs-5 text-primary" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes / Terms & Conditions</label>
                <textarea name="notes" class="form-control" rows="3">Valid for 7 days. Prices are subject to change.</textarea>
            </div>

            <div class="text-end">
                <a href="<?= base_url('quotations') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="saveBtn"><i class="fas fa-save"></i> Save Quotation</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function addProduct() {
        const select = document.getElementById('productSearch');
        if (select.value === "") return;

        const option = select.options[select.selectedIndex];
        const id = select.value;
        const name = option.getAttribute('data-name');
        const price = parseFloat(option.getAttribute('data-price')).toFixed(2);

        // Check if already exists
        let exists = false;
        document.querySelectorAll('input[name="product_id[]"]').forEach(input => {
            if (input.value == id) {
                exists = true;
                let tr = input.closest('tr');
                let qtyInput = tr.querySelector('input[name="quantity[]"]');
                let cur = parseFloat(qtyInput.value) || 0;
                qtyInput.value = Math.max(0, cur) + 1;
                calculateRow(qtyInput);
            }
        });

        if (!exists) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    ${name}
                    <input type="hidden" name="product_id[]" value="${id}">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control" value="${price}" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
                </td>
                <td>
                    <input type="number" step="any" min="0.01" name="quantity[]" class="form-control" value="1" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control-plaintext row-subtotal" value="${price}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                </td>
            `;
            document.querySelector('#itemsTable tbody').appendChild(tr);
        }

        select.value = ""; // reset select
        calculateTotals();
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateTotals();
    }

    function calculateRow(input) {
        const tr = input.closest('tr');
        const price = Math.max(0, parseFloat(tr.querySelector('input[name="unit_price[]"]').value) || 0);
        const qtyInput = tr.querySelector('input[name="quantity[]"]');
        let qty = parseFloat(qtyInput.value);

        if (isNaN(qty) || qty < 0) {
            qty = 0;
            if (qtyInput.value && parseFloat(qtyInput.value) < 0) {
                qtyInput.value = Math.abs(parseFloat(qtyInput.value)) || 1;
                qty = parseFloat(qtyInput.value);
            }
        }
        if (input.type === 'change' && (isNaN(qty) || qty <= 0)) {
            qty = 1;
            qtyInput.value = 1;
        }

        const safeQty = Math.max(0, qty);
        tr.querySelector('.row-subtotal').value = (price * safeQty).toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.row-subtotal').forEach(input => {
            subtotal += Math.max(0, parseFloat(input.value) || 0);
        });

        const discountPercent = Math.max(0, Math.min(100, parseFloat(document.getElementById('calcDiscountPercent').value) || 0));
        const discountAmount = subtotal * (discountPercent / 100);
        
        document.getElementById('calcDiscount').value = discountAmount.toFixed(2);

        const total = Math.max(0, subtotal - discountAmount);

        document.getElementById('calcSubtotal').value = subtotal.toFixed(2);
        document.getElementById('calcTotal').value = total.toFixed(2);
        
        document.getElementById('saveBtn').disabled = subtotal <= 0;
    }
</script>
<?= $this->endSection() ?>
