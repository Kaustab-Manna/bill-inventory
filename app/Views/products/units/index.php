<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-weight" style="color:var(--success);margin-right:8px;"></i> Units</h1>
        <p>Manage product measurement units (e.g., Kg, Pcs, Box)</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Unit
    </button>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Unit Name</th>
                    <th>Short Name</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($units as $unit): ?>
                <tr>
                    <td style="font-weight:600;"><?= esc($unit->name) ?></td>
                    <td style="font-family:'JetBrains Mono',monospace;"><?= esc($unit->short_name) ?></td>
                    <td>
                        <?php if ($unit->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-icon edit" onclick="editUnit(<?= $unit->id ?>)"><i class="fas fa-pen"></i></button>
                            <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('units/delete/' . $unit->id) ?>', '<?= esc($unit->name) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="unitModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Unit</h3>
            <button class="modal-close" onclick="closeModal('unitModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="unitForm">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Kilogram" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Short Name *</label>
                    <input type="text" name="short_name" class="form-control" placeholder="e.g., kg" required>
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
            <button class="btn btn-ghost" onclick="closeModal('unitModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveUnit()"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingId = null;

    function openCreateModal() {
        editingId = null;
        document.getElementById('modalTitle').innerText = 'Add Unit';
        resetForm('unitForm');
        document.querySelector('#unitForm [name="is_active"]').checked = true;
        openModal('unitModal');
    }

    async function editUnit(id) {
        const result = await ajaxGet(`<?= base_url('units/edit') ?>/${id}`);
        if (result.success) {
            editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Unit';
            setFormValues('unitForm', result.unit);
            document.querySelector('#unitForm [name="is_active"]').checked = result.unit.is_active == 1;
            openModal('unitModal');
        }
    }

    async function saveUnit() {
        const form = getFormData('unitForm');
        if (!document.querySelector('#unitForm [name="is_active"]').checked) form.set('is_active', '0');
        const url = editingId ? `<?= base_url('units/update') ?>/${editingId}` : '<?= base_url('units') ?>';
        await ajaxPost(url, form, { closeModal: 'unitModal', reload: true });
    }
</script>
<?= $this->endSection() ?>
