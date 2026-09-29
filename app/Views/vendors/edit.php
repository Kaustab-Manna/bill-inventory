<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
    </div>
    <div class="card-body">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= base_url('vendors/update/' . $vendor->id) ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= old('name', $vendor->name) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" value="<?= old('contact_person', $vendor->contact_person) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email', $vendor->email) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="<?= old('phone', $vendor->phone) ?>" placeholder="e.g. 9876543210" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tax / GST Number</label>
                    <input type="text" name="tax_number" class="form-control" value="<?= old('tax_number', $vendor->tax_number) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= old('is_active', $vendor->is_active) == '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= old('is_active', $vendor->is_active) == '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= old('address', $vendor->address) ?></textarea>
                </div>
            </div>
            
            <div class="text-end">
                <a href="<?= base_url('vendors') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Vendor</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
