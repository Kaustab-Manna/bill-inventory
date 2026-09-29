<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-tags" style="color:var(--primary-light);margin-right:8px;"></i> Categories</h1>
        <p>Manage product categories and subcategories</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Category
    </button>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent Category</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flatCategories as $cat): ?>
                <tr>
                    <td style="font-weight:600;"><?= esc($cat->name) ?></td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= esc($cat->slug) ?></td>
                    <td>
                        <?php 
                        $parent = array_filter($flatCategories, fn($c) => $c->id == $cat->parent_id);
                        echo !empty($parent) ? esc(reset($parent)->name) : '-';
                        ?>
                    </td>
                    <td>
                        <?php if ($cat->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-icon edit" onclick="editCategory(<?= $cat->id ?>)"><i class="fas fa-pen"></i></button>
                            <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('categories/delete/' . $cat->id) ?>', '<?= esc($cat->name) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($flatCategories)): ?>
                <tr><td colspan="5" class="text-center">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Category</h3>
            <button class="modal-close" onclick="closeModal('categoryModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="categoryForm">
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required onkeyup="generateSlug(this.value, 'catSlug')">
                </div>
                <div class="form-group">
                    <label class="form-label">Slug *</label>
                    <input type="text" name="slug" id="catSlug" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parent Category</label>
                    <select name="parent_id" class="form-control">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($flatCategories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= esc($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
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
            <button class="btn btn-ghost" onclick="closeModal('categoryModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveCategory()"><i class="fas fa-save"></i> Save</button>
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
        document.getElementById('modalTitle').innerText = 'Add Category';
        resetForm('categoryForm');
        document.querySelector('#categoryForm [name="is_active"]').checked = true;
        openModal('categoryModal');
    }

    async function editCategory(id) {
        const result = await ajaxGet(`<?= base_url('categories/edit') ?>/${id}`);
        if (result.success) {
            editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Category';
            setFormValues('categoryForm', result.category);
            document.querySelector('#categoryForm [name="is_active"]').checked = result.category.is_active == 1;
            openModal('categoryModal');
        }
    }

    async function saveCategory() {
        const form = getFormData('categoryForm');
        if (!document.querySelector('#categoryForm [name="is_active"]').checked) form.set('is_active', '0');
        const url = editingId ? `<?= base_url('categories/update') ?>/${editingId}` : '<?= base_url('categories') ?>';
        await ajaxPost(url, form, { closeModal: 'categoryModal', reload: true });
    }
</script>
<?= $this->endSection() ?>
