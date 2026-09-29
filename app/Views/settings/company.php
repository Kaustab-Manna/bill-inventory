<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-building" style="color:var(--primary-light);margin-right:8px;"></i> Company Profile</h1>
        <p>Manage your business information, branding, and financial year settings</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast('<?= esc(session()->getFlashdata('success')) ?>','success'));</script>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form action="<?= base_url('settings/company') ?>" method="POST" enctype="multipart/form-data">
        <!-- Business Info -->
        <h3 style="margin-bottom:20px;"><i class="fas fa-info-circle" style="color:var(--primary-light);margin-right:8px;"></i> Business Information</h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Company Name *</label>
                <input type="text" name="company_name" class="form-control" value="<?= esc($company->company_name ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc($company->email ?? '') ?>" placeholder="business@company.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control" value="<?= esc($company->phone ?? '') ?>" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
            </div>
            <div class="form-group"></div>
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= esc($company->address ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="<?= esc($company->city ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control" value="<?= esc($company->state ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control" value="<?= esc($company->country ?? 'India') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control" value="<?= esc($company->pincode ?? '') ?>">
            </div>
        </div>

        <!-- Tax Info -->
        <h3 style="margin:24px 0 20px;padding-top:24px;border-top:1px solid var(--border);">
            <i class="fas fa-receipt" style="color:var(--success);margin-right:8px;"></i> Tax Information
        </h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="<?= esc($company->gst_number ?? '') ?>" placeholder="22AAAAA0000A1Z5" style="text-transform:uppercase;">
            </div>
            <div class="form-group">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan_number" class="form-control" value="<?= esc($company->pan_number ?? '') ?>" placeholder="AAAAA0000A" style="text-transform:uppercase;">
            </div>
        </div>

        <!-- Financial Year & Currency -->
        <h3 style="margin:24px 0 20px;padding-top:24px;border-top:1px solid var(--border);">
            <i class="fas fa-calendar-alt" style="color:var(--warning);margin-right:8px;"></i> Financial Year & Currency
        </h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Financial Year Start</label>
                <input type="date" name="financial_year_start" class="form-control" value="<?= esc($company->financial_year_start ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Financial Year End</label>
                <input type="date" name="financial_year_end" class="form-control" value="<?= esc($company->financial_year_end ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Currency</label>
                <select name="currency" class="form-control">
                    <option value="INR" <?= ($company->currency ?? '') === 'INR' ? 'selected' : '' ?>>INR - Indian Rupee</option>
                    <option value="USD" <?= ($company->currency ?? '') === 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                    <option value="EUR" <?= ($company->currency ?? '') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                    <option value="GBP" <?= ($company->currency ?? '') === 'GBP' ? 'selected' : '' ?>>GBP - British Pound</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control" value="<?= esc($company->currency_symbol ?? '₹') ?>" style="max-width:80px;">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-control">
                    <option value="Asia/Kolkata" <?= ($company->timezone ?? '') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST)</option>
                    <option value="UTC" <?= ($company->timezone ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                    <option value="America/New_York" <?= ($company->timezone ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New York (EST)</option>
                    <option value="Europe/London" <?= ($company->timezone ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT)</option>
                </select>
            </div>
            <div class="form-group"></div>
        </div>

        <!-- Invoice Branding -->
        <h3 style="margin:24px 0 20px;padding-top:24px;border-top:1px solid var(--border);">
            <i class="fas fa-file-invoice" style="color:var(--info);margin-right:8px;"></i> Invoice Branding
        </h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control" value="<?= esc($company->invoice_prefix ?? 'INV-') ?>" placeholder="e.g. INV-">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Invoice Footer Notes</label>
            <textarea name="invoice_footer" class="form-control" rows="2" placeholder="e.g. Thank you for your business!"><?= esc($company->invoice_footer ?? '') ?></textarea>
        </div>


        <!-- Logo -->
        <h3 style="margin:24px 0 20px;padding-top:24px;border-top:1px solid var(--border);">
            <i class="fas fa-image" style="color:var(--accent);margin-right:8px;"></i> Logo & Branding
        </h3>

        <div class="form-group">
            <label class="form-label">Company Logo</label>
            <?php if (!empty($company->logo)): ?>
                <div style="margin-bottom:12px;">
                    <img src="<?= base_url($company->logo) ?>" alt="Logo" style="max-height:60px;border-radius:var(--radius-md);border:1px solid var(--border);padding:8px;background:var(--bg-input);">
                </div>
            <?php endif; ?>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Recommended: 200x60px, PNG or SVG</p>
        </div>

        <!-- Submit -->
        <div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Changes</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
