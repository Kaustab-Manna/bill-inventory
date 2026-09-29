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

        <form action="<?= base_url('sales-returns/store') ?>" method="post" id="returnForm">
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Return Date *</label>
                    <input type="datetime-local" name="return_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Warehouse (Return To) *</label>
                    <select name="warehouse_id" class="form-select" required>
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
                <h5 class="mb-3 border-bottom pb-2">Returned Items</h5>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <select id="productSearch" class="form-select" onchange="addProduct()">
                        <option value="">Select product being returned...</option>
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
                                <th width="150">Condition</th>
                                <th width="150">Unit Price (₹)</th>
                                <th width="150">Quantity Returned</th>
                                <th width="150">Subtotal (₹)</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th>
                                    <input type="number" step="0.01" name="subtotal" id="calcSubtotal" class="form-control-plaintext fw-bold" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end fs-5 text-danger">Total Refund Amount:</th>
                                <th>
                                    <input type="number" step="0.01" name="total_amount" id="calcTotal" class="form-control-plaintext fw-bold fs-5 text-danger" value="0.00" readonly>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Return Notes / Reason</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Explain the reason for return..."></textarea>
            </div>

            <div class="text-end">
                <a href="<?= base_url('sales-returns') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger" id="saveBtn" disabled><i class="fas fa-undo"></i> Process Return</button>
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
                    <select name="item_condition[]" class="form-select">
                        <option value="sealed">Seal Intact</option>
                        <option value="broken_seal">Seal Broken</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control" value="${price}" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
                </td>
                <td>
                    <input type="number" step="any" min="0.01" name="quantity[]" class="form-control" value="1" onkeyup="calculateRow(this)" onchange="calculateRow(this)" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control-plaintext row-subtotal text-danger" value="${price}" readonly>
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

        document.getElementById('calcSubtotal').value = subtotal.toFixed(2);
        document.getElementById('calcTotal').value = subtotal.toFixed(2);
        
        document.getElementById('saveBtn').disabled = subtotal <= 0;
    }
</script>
<?= $this->endSection() ?>
