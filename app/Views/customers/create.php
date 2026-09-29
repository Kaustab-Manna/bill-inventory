<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('customers/store') ?>" method="post">
            
            <h5 class="mb-3 border-bottom pb-2">Basic Info</h5>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mobile Number (10 Digits)</label>
                    <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>" pattern="[0-9]{10}" maxlength="10" placeholder="e.g. 9876543210" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    <small class="text-muted">Must be exactly 10 digits</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">GST / Tax Number</label>
                    <input type="text" name="gst_no" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Credit Limit (₹)</label>
                    <input type="number" step="0.01" name="credit_limit" class="form-control" value="0.00">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2">Billing / Shipping Details</h5>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Full Address</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Customer</button>
            <a href="<?= base_url('customers') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
