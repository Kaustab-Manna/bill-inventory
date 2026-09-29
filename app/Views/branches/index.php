<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-building" style="color:var(--accent);margin-right:8px;"></i> Branch Management</h1>
        <p>Manage your business branches and store locations</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateBranchModal()">
        <i class="fas fa-plus"></i> Add Branch
    </button>
</div>

<!-- Branch Cards -->
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:20px;">
    <?php foreach ($branches as $branch): ?>
        <div class="card stagger-item">
            <div class="d-flex items-center justify-between mb-16">
                <div class="d-flex items-center gap-12">
                    <div class="kpi-icon <?= $branch->is_main ? 'primary' : 'accent' ?>" style="width:42px;height:42px;">
                        <i class="fas fa-<?= $branch->is_main ? 'crown' : 'store' ?>"></i>
                    </div>
                    <div>
                        <h3 style="font-size:1rem;"><?= esc($branch->name) ?></h3>
                        <span class="font-mono" style="font-size:0.75rem;color:var(--text-muted);"><?= esc($branch->code) ?></span>
                    </div>
                </div>
                <?php if ($branch->is_main): ?>
                    <span class="badge badge-primary">Main</span>
                <?php endif; ?>
            </div>

            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:12px;">
                <?php if ($branch->address): ?>
                    <div class="mb-8"><i class="fas fa-map-marker-alt" style="width:16px;margin-right:6px;"></i><?= esc($branch->address) ?></div>
                <?php endif; ?>
                <?php if ($branch->city || $branch->state): ?>
                    <div class="mb-8"><i class="fas fa-city" style="width:16px;margin-right:6px;"></i><?= esc(implode(', ', array_filter([$branch->city, $branch->state, $branch->pincode]))) ?></div>
                <?php endif; ?>
                <?php if ($branch->phone): ?>
                    <div class="mb-8"><i class="fas fa-phone" style="width:16px;margin-right:6px;"></i><?= esc($branch->phone) ?></div>
                <?php endif; ?>
                <?php if ($branch->email): ?>
                    <div><i class="fas fa-envelope" style="width:16px;margin-right:6px;"></i><?= esc($branch->email) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex items-center justify-between" style="padding-top:12px;border-top:1px solid var(--border);">
                <div class="d-flex items-center gap-12">
                    <span style="font-size:0.8rem;color:var(--text-muted);"><i class="fas fa-users"></i> <?= $branch->user_count ?? 0 ?> users</span>
                    <?php if ($branch->is_active): ?>
                        <span class="badge badge-success">Active</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                    <?php endif; ?>
                </div>
                <div class="action-btns">
                    <button class="btn-icon edit" onclick="editBranch(<?= $branch->id ?>)"><i class="fas fa-pen"></i></button>
                    <?php if (!$branch->is_main): ?>
                        <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('branches/delete/' . $branch->id) ?>', '<?= esc($branch->name) ?>')"><i class="fas fa-trash"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Branch Modal -->
<div class="modal-overlay" id="branchModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="branchModalTitle"><i class="fas fa-store" style="color:var(--accent);margin-right:8px;"></i> Add Branch</h3>
            <button class="modal-close" onclick="closeModal('branchModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="branchForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Branch Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Branch name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Branch Code *</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g., BR01" required style="text-transform:uppercase;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" placeholder="Full address" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" placeholder="State">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" placeholder="Pincode">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="branch@company.com">
                    </div>
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
            <button class="btn btn-danger" id="modalDeleteBtn" style="display: none; margin-right: auto;" onclick="deleteBranchFromModal()"><i class="fas fa-trash"></i> Delete</button>
            <button class="btn btn-ghost" onclick="closeModal('branchModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveBranch()"><i class="fas fa-save"></i> Save Branch</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingBranchId = null;

    function openCreateBranchModal() {
        editingBranchId = null;
        document.getElementById('branchModalTitle').innerHTML = '<i class="fas fa-store" style="color:var(--accent);margin-right:8px;"></i> Add Branch';
        resetForm('branchForm');
        document.querySelector('#branchForm [name="is_active"]').checked = true;
        document.getElementById('modalDeleteBtn').style.display = 'none';
        openModal('branchModal');
    }

    async function editBranch(id) {
        const result = await ajaxGet(`<?= base_url('branches/edit') ?>/${id}`);
        if (result.success) {
            editingBranchId = id;
            document.getElementById('branchModalTitle').innerHTML = '<i class="fas fa-pen" style="color:var(--info);margin-right:8px;"></i> Edit Branch';
            setFormValues('branchForm', result.branch);
            document.querySelector('#branchForm [name="is_active"]').checked = result.branch.is_active == 1;
            
            const deleteBtn = document.getElementById('modalDeleteBtn');
            if (result.branch.is_main == 1) {
                deleteBtn.style.display = 'none';
            } else {
                deleteBtn.style.display = 'inline-flex';
                // Store branch info in dataset for the delete button
                deleteBtn.dataset.id = id;
                deleteBtn.dataset.name = result.branch.name;
            }
            
            openModal('branchModal');
        }
    }

    async function saveBranch() {
        const form = getFormData('branchForm');
        if (!document.querySelector('#branchForm [name="is_active"]').checked) {
            form.set('is_active', '0');
        }
        const url = editingBranchId
            ? `<?= base_url('branches/update') ?>/${editingBranchId}`
            : '<?= base_url('branches') ?>';
        await ajaxPost(url, form, { closeModal: 'branchModal', reload: true });
    }

    function deleteBranchFromModal() {
        const deleteBtn = document.getElementById('modalDeleteBtn');
        const id = deleteBtn.dataset.id;
        const name = deleteBtn.dataset.name;
        if (id) {
            confirmDelete(`<?= base_url('branches/delete') ?>/${id}`, name);
        }
    }
</script>
<?= $this->endSection() ?>
