<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-cog" style="color:var(--text-muted);margin-right:8px;"></i> General Settings</h1>
        <p>Configure invoice, tax, payment, notification, and backup preferences</p>
    </div>
</div>

<form action="<?= base_url('settings/general') ?>" method="POST">
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap:20px;">

        <!-- Invoice Settings -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice" style="color:var(--primary-light);margin-right:8px;"></i> Invoice Settings</h3>
            </div>
            <div class="form-group">
                <label class="form-label">Invoice Prefix</label>
                <input type="text" name="settings[invoice][prefix]" class="form-control" value="<?= esc($invoiceSettings['prefix'] ?? 'INV-') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Next Invoice Number</label>
                <input type="number" name="settings[invoice][next_number]" class="form-control" value="<?= esc($invoiceSettings['next_number'] ?? 1) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Invoice Terms</label>
                <textarea name="settings[invoice][terms]" class="form-control" rows="2"><?= esc($invoiceSettings['terms'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[invoice][show_logo]" value="1" <?= ($invoiceSettings['show_logo'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Show logo on invoices</span>
                </label>
            </div>
        </div>

        <!-- Tax Settings -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-percent" style="color:var(--success);margin-right:8px;"></i> Tax Settings</h3>
            </div>
            <div class="form-group">
                <label class="form-label">Default Tax Type</label>
                <select name="settings[tax][default_tax_type]" class="form-control">
                    <option value="exclusive" <?= ($taxSettings['default_tax_type'] ?? '') === 'exclusive' ? 'selected' : '' ?>>Exclusive (Tax added on top)</option>
                    <option value="inclusive" <?= ($taxSettings['default_tax_type'] ?? '') === 'inclusive' ? 'selected' : '' ?>>Inclusive (Tax included in price)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[tax][enable_gst]" value="1" <?= ($taxSettings['enable_gst'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Enable GST</span>
                </label>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-credit-card" style="color:var(--accent);margin-right:8px;"></i> Payment Settings</h3>
            </div>
            <div class="form-group">
                <label class="form-label">Default Payment Mode</label>
                <select name="settings[payment][default_mode]" class="form-control">
                    <option value="cash" <?= ($paymentSettings['default_mode'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="bank_transfer" <?= ($paymentSettings['default_mode'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    <option value="upi" <?= ($paymentSettings['default_mode'] ?? '') === 'upi' ? 'selected' : '' ?>>UPI</option>
                    <option value="card" <?= ($paymentSettings['default_mode'] ?? '') === 'card' ? 'selected' : '' ?>>Card</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[payment][enable_upi]" value="1" <?= ($paymentSettings['enable_upi'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Enable UPI Payments</span>
                </label>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bell" style="color:var(--warning);margin-right:8px;"></i> Notification Settings</h3>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[notification][low_stock_alert]" value="1" <?= ($notifSettings['low_stock_alert'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Low Stock Alerts</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[notification][expiry_alert]" value="1" <?= ($notifSettings['expiry_alert'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Expiry Alerts</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[notification][payment_due_alert]" value="1" <?= ($notifSettings['payment_due_alert'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Payment Due Alerts</span>
                </label>
            </div>
        </div>

        <!-- Backup Settings -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-database" style="color:var(--danger);margin-right:8px;"></i> Backup Settings</h3>
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox" name="settings[backup][auto_backup]" value="1" <?= ($backupSettings['auto_backup'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                    <span>Auto Backup</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">Backup Frequency</label>
                <select name="settings[backup][backup_frequency]" class="form-control">
                    <option value="daily" <?= ($backupSettings['backup_frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="weekly" <?= ($backupSettings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                    <option value="monthly" <?= ($backupSettings['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>
            </div>
            <div class="form-group border-top pt-3 mt-3">
                <label class="form-label d-block text-muted mb-2">Manual Backup</label>
                <a href="<?= base_url('backup/database') ?>" class="btn btn-outline-danger w-100">
                    <i class="fas fa-download"></i> Generate SQL Backup Now
                </a>
            </div>
        </div>
    </div>

    <div style="margin-top:24px;display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save All Settings</button>
    </div>
</form>

<?= $this->endSection() ?>
