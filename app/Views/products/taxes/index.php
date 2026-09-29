<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-percent" style="color:var(--danger);margin-right:8px;"></i> Taxes</h1>
        <p>Manage tax rates and types</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Tax
    </button>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tax Name</th>
                    <th>Rate (%)</th>
                    <th>Calculation Type</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taxes as $tax): ?>
                <tr>
                    <td style="font-weight:600;"><?= esc($tax->name) ?></td>
                    <td style="font-family:'JetBrains Mono',monospace;color:var(--danger);font-weight:600;"><?= number_format($tax->rate, 2) ?>%</td>
                    <td><?= ucfirst($tax->type) ?></td>
                    <td>
                        <?php if ($tax->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-icon edit" onclick="editTax(<?= $tax->id ?>)"><i class="fas fa-pen"></i></button>
                            <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('taxes/delete/' . $tax->id) ?>', '<?= esc($tax->name) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="taxModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Tax</h3>
            <button class="modal-close" onclick="closeModal('taxModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="taxForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tax Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., GST 18%" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rate (%) *</label>
                        <input type="number" step="0.01" name="rate" class="form-control" placeholder="18.00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Calculation Type</label>
                    <select name="type" class="form-control">
                        <option value="exclusive">Exclusive (Added to price)</option>
                        <option value="inclusive">Inclusive (Included in price)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-switch">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span class="slider"></span>
                        <span style="font-size:0.85rem;">Active</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('taxModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveTax()"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingId = null;

    function openCreateModal() {
        editingId = null;
        document.getElementById('modalTitle').innerText = 'Add Tax';
        resetForm('taxForm');
        document.querySelector('#taxForm [name="is_active"]').checked = true;
        openModal('taxModal');
    }

    async function editTax(id) {
        const result = await ajaxGet(`<?= base_url('taxes/edit') ?>/${id}`);
        if (result.success) {
            editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Tax';
            setFormValues('taxForm', result.tax);
            document.querySelector('#taxForm [name="is_active"]').checked = result.tax.is_active == 1;
            openModal('taxModal');
        }
    }

    async function saveTax() {
        const form = getFormData('taxForm');
        if (!document.querySelector('#taxForm [name="is_active"]').checked) form.set('is_active', '0');
        const url = editingId ? `<?= base_url('taxes/update') ?>/${editingId}` : '<?= base_url('taxes') ?>';
        await ajaxPost(url, form, { closeModal: 'taxModal', reload: true });
    }
</script>
<?= $this->endSection() ?>
