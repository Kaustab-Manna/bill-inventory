<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-warehouse" style="color:var(--accent);margin-right:8px;"></i> Warehouses</h1>
        <p>Manage multiple storage locations and stock points</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Warehouse
    </button>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Warehouse Name</th>
                    <th>Code</th>
                    <th>Branch</th>
                    <th>Manager</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($warehouses as $wh): ?>
                <tr>
                    <td>
                        <div style="font-weight:600;"><?= esc($wh->name) ?></div>
                        <?php if ($wh->is_default): ?>
                            <span class="badge badge-primary" style="font-size:0.65rem;margin-top:4px;">Default Warehouse</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= esc($wh->code) ?></td>
                    <td><?= esc($wh->branch_name ?? 'Unassigned') ?></td>
                    <td><?= esc($wh->manager_name ?? '-') ?></td>
                    <td>
                        <?php if ($wh->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-icon edit" onclick="editWarehouse(<?= $wh->id ?>)"><i class="fas fa-pen"></i></button>
                            <?php if (!$wh->is_default): ?>
                                <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('warehouses/delete/' . $wh->id) ?>', '<?= esc($wh->name) ?>')"><i class="fas fa-trash"></i></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="whModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Warehouse</h3>
            <button class="modal-close" onclick="closeModal('whModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="whForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Warehouse Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" class="form-control" required style="text-transform:uppercase;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Branch Assignment</label>
                        <select name="branch_id" class="form-control">
                            <option value="">No Branch</option>
                            <?php foreach($branches as $branch): ?>
                                <option value="<?= $branch->id ?>"><?= esc($branch->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Manager</label>
                        <select name="manager_id" class="form-control">
                            <option value="">Select Manager</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user->id ?>"><?= esc($user->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="1"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-switch">
                            <input type="checkbox" name="is_default" value="1">
                            <span class="slider"></span>
                            <span style="font-size:0.85rem;">Set as Default Warehouse</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-switch">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="slider"></span>
                            <span style="font-size:0.85rem;">Active</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('whModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveWarehouse()"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingId = null;

    function openCreateModal() {
        editingId = null;
        document.getElementById('modalTitle').innerText = 'Add Warehouse';
        resetForm('whForm');
        document.querySelector('#whForm [name="is_active"]').checked = true;
        openModal('whModal');
    }

    async function editWarehouse(id) {
        const result = await ajaxGet(`<?= base_url('warehouses/edit') ?>/${id}`);
        if (result.success) {
            editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Warehouse';
            setFormValues('whForm', result.warehouse);
            document.querySelector('#whForm [name="is_active"]').checked = result.warehouse.is_active == 1;
            document.querySelector('#whForm [name="is_default"]').checked = result.warehouse.is_default == 1;
            openModal('whModal');
        }
    }

    async function saveWarehouse() {
        const form = getFormData('whForm');
        if (!document.querySelector('#whForm [name="is_active"]').checked) form.set('is_active', '0');
        if (!document.querySelector('#whForm [name="is_default"]').checked) form.set('is_default', '0');
        const url = editingId ? `<?= base_url('warehouses/update') ?>/${editingId}` : '<?= base_url('warehouses') ?>';
        await ajaxPost(url, form, { closeModal: 'whModal', reload: true });
    }
</script>
<?= $this->endSection() ?>
