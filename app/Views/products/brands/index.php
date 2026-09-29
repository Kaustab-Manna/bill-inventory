<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-copyright" style="color:var(--accent);margin-right:8px;"></i> Brands</h1>
        <p>Manage product brands and manufacturers</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Brand
    </button>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Brand Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                <tr>
                    <td style="font-weight:600;"><?= esc($brand->name) ?></td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= esc($brand->slug) ?></td>
                    <td>
                        <?php if ($brand->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-icon edit" onclick="editBrand(<?= $brand->id ?>)"><i class="fas fa-pen"></i></button>
                            <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('brands/delete/' . $brand->id) ?>', '<?= esc($brand->name) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($brands)): ?>
                <tr><td colspan="4" class="text-center">No brands found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="brandModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Brand</h3>
            <button class="modal-close" onclick="closeModal('brandModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="brandForm">
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required onkeyup="generateSlug(this.value, 'brandSlug')">
                </div>
                <div class="form-group">
                    <label class="form-label">Slug *</label>
                    <input type="text" name="slug" id="brandSlug" class="form-control" required>
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
            <button class="btn btn-ghost" onclick="closeModal('brandModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveBrand()"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingId = null;

    function generateSlug(text, targetId) {
        if (!editingId) {
            document.getElementById(targetId).value = text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    }

    function openCreateModal() {
        editingId = null;
        document.getElementById('modalTitle').innerText = 'Add Brand';
        resetForm('brandForm');
        document.querySelector('#brandForm [name="is_active"]').checked = true;
        openModal('brandModal');
    }

    async function editBrand(id) {
        const result = await ajaxGet(`<?= base_url('brands/edit') ?>/${id}`);
        if (result.success) {
            editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Brand';
            setFormValues('brandForm', result.brand);
            document.querySelector('#brandForm [name="is_active"]').checked = result.brand.is_active == 1;
            openModal('brandModal');
        }
    }

    async function saveBrand() {
        const form = getFormData('brandForm');
        if (!document.querySelector('#brandForm [name="is_active"]').checked) form.set('is_active', '0');
        const url = editingId ? `<?= base_url('brands/update') ?>/${editingId}` : '<?= base_url('brands') ?>';
        await ajaxPost(url, form, { closeModal: 'brandModal', reload: true });
    }
</script>
<?= $this->endSection() ?>
