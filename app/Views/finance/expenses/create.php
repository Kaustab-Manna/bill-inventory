<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('expenses/store') ?>" method="post">
            
            <div class="mb-3">
                <label class="form-label">Expense Date *</label>
                <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category *</label>
                <input type="text" name="category" class="form-control" placeholder="e.g. Electricity, Office Supplies, Salary" required list="categoryList">
                <datalist id="categoryList">
                    <option value="Electricity Bill">
                    <option value="Water Bill">
                    <option value="Internet / Phone">
                    <option value="Office Rent">
                    <option value="Employee Salary">
                    <option value="Tea / Snacks">
                    <option value="Stationery">
                    <option value="Maintenance / Repair">
                    <option value="Transportation">
                </datalist>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount (₹) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control fw-bold text-danger fs-4" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="Cash">Cash</option>
                    <option value="UPI">UPI / Mobile Payment</option>
                    <option value="Bank Transfer">Bank Transfer / NEFT</option>
                    <option value="Card">Credit / Debit Card</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Reference No / Bill No</label>
                <input type="text" name="reference_no" class="form-control" placeholder="Optional">
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Optional remarks..."></textarea>
            </div>

            <div class="text-end border-top pt-3">
                <a href="<?= base_url('expenses') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Expense</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
