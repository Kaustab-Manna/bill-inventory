<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('payments/store') ?>" method="post">
            
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Type *</label>
                    <select name="type" id="paymentType" class="form-select" onchange="togglePartyFields()" required>
                        <option value="in">Money IN (Received from Customer)</option>
                        <option value="out">Money OUT (Paid to Vendor)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <!-- Customer Field (Visible by default) -->
            <div class="mb-4" id="customerSection">
                <label class="form-label">Select Customer (Leave empty if Walk-in)</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- General / Walk-in Customer --</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c->id ?>"><?= esc($c->name) ?> (<?= esc($c->phone) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Optional: If you want to link this to a specific invoice, type the Sale ID here:</small>
                <input type="number" name="sale_id" class="form-control mt-1" placeholder="Sale Invoice ID (optional)">
            </div>

            <!-- Vendor Field (Hidden by default) -->
            <div class="mb-4" id="vendorSection" style="display: none;">
                <label class="form-label">Select Vendor *</label>
                <select name="vendor_id" id="vendorSelect" class="form-select">
                    <option value="">-- Select Vendor --</option>
                    <?php foreach($vendors as $v): ?>
                        <option value="<?= $v->id ?>"><?= esc($v->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Optional: If paying a specific Purchase Invoice, type ID here:</small>
                <input type="number" name="purchase_id" class="form-control mt-1" placeholder="Purchase Invoice ID (optional)">
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount (₹) *</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control fs-4 fw-bold text-primary" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI / Mobile Payment</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT</option>
                        <option value="Card">Credit / Debit Card</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Reference No. (UTR / Txn ID)</label>
                    <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional remarks"></textarea>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <a href="<?= base_url('payments') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Payment</button>
            </div>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function togglePartyFields() {
    const type = document.getElementById('paymentType').value;
    const customerSec = document.getElementById('customerSection');
    const vendorSec = document.getElementById('vendorSection');
    const vendorSelect = document.getElementById('vendorSelect');

    if (type === 'in') {
        customerSec.style.display = 'block';
        vendorSec.style.display = 'none';
        vendorSelect.removeAttribute('required');
    } else {
        customerSec.style.display = 'none';
        vendorSec.style.display = 'block';
        vendorSelect.setAttribute('required', 'required');
    }
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
