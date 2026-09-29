<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-key" style="color:var(--warning);margin-right:8px;"></i> Roles & Permissions</h1>
        <p>Manage user roles and configure module-level access permissions</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateRoleModal()">
        <i class="fas fa-plus"></i> Add Role
    </button>
</div>

<!-- Roles Grid -->
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:20px;">
    <?php foreach ($roles as $role): ?>
        <div class="card stagger-item" style="position:relative;">
            <?php if ($role->is_system): ?>
                <div style="position:absolute;top:12px;right:12px;">
                    <span class="badge badge-warning"><i class="fas fa-lock" style="margin-right:4px;"></i> System</span>
                </div>
            <?php endif; ?>
            <div class="d-flex items-center gap-12 mb-16">
                <div class="kpi-icon primary" style="width:42px;height:42px;">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div>
                    <h3 style="font-size:1rem;"><?= esc($role->display_name) ?></h3>
                    <span style="font-size:0.75rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;"><?= esc($role->name) ?></span>
                </div>
            </div>
            <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:12px;"><?= esc($role->description ?? 'No description') ?></p>
            <div class="mb-16">
                <?php if ($role->is_system && $role->name === 'super_admin'): ?>
                    <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary-light); font-weight: 600; padding: 4px 8px; border-radius: var(--radius-sm); font-size: 0.75rem;">
                        <i class="fas fa-crown me-1"></i> Full Access (All 37 Modules)
                    </span>
                <?php else: ?>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); font-weight: 600; padding: 4px 8px; border-radius: var(--radius-sm); font-size: 0.75rem;">
                        <i class="fas fa-shield-alt me-1"></i> <?= $role->module_count ?? 0 ?> Modules (<?= $role->perm_count ?? 0 ?> Actions)
                    </span>
                <?php endif; ?>
            </div>
            <div class="d-flex items-center justify-between">
                <span style="font-size:0.8rem;color:var(--text-muted);">
                    <i class="fas fa-users" style="margin-right:4px;"></i> <?= $role->user_count ?? 0 ?> user(s)
                </span>
                <div class="action-btns">
                    <button class="btn-icon edit" title="Edit Role & Permissions" onclick="editRole(<?= $role->id ?>)">
                        <i class="fas fa-pen"></i>
                    </button>
                    <?php if (!$role->is_system): ?>
                        <button class="btn-icon delete" title="Delete" onclick="confirmDelete('<?= base_url('roles/delete/' . $role->id) ?>', '<?= esc($role->display_name) ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Role Modal -->
<div class="modal-overlay" id="roleModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="roleModalTitle"><i class="fas fa-user-tag" style="color:var(--primary-light);margin-right:8px;"></i> Add New Role</h3>
            <button class="modal-close" onclick="closeModal('roleModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role Name (System Key) *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., inventory_manager" required id="roleName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name *</label>
                        <input type="text" name="display_name" class="form-control" placeholder="e.g., Inventory Manager" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Brief role description" rows="2"></textarea>
                </div>

                <!-- Permissions Section with Module-Level Access Control -->
                <div style="margin-top:20px;">
                    <div class="d-flex justify-content-between align-items-center mb-8">
                        <div>
                            <label class="form-label mb-0" style="font-weight:600; font-size:0.95rem;">
                                <i class="fas fa-layer-group text-primary me-1"></i> Module Access & Action Permissions
                            </label>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Select which modules this role can access, or fine-tune individual actions</div>
                        </div>
                        <span class="badge" id="totalPermsCountBadge" style="background:var(--primary); color:#fff; font-size:0.75rem; padding:4px 8px;">0 Selected</span>
                    </div>

                    <!-- Search and Toolbar -->
                    <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <div style="position:relative; flex:1; min-width:200px;">
                            <i class="fas fa-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.8rem;"></i>
                            <input type="text" id="permModuleSearch" class="form-control form-control-sm" placeholder="Filter modules (e.g. sales, products, users)..." style="padding-left:30px;" oninput="filterPermModules(this.value)">
                        </div>
                        <div class="d-flex gap-8">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="selectAllPermissions()" title="Select All Permissions Across All Modules">
                                <i class="fas fa-check-double text-success"></i> Select All
                            </button>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="deselectAllPermissions()" title="Deselect All">
                                <i class="fas fa-times text-danger"></i> Deselect All
                            </button>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="toggleAllModuleAccordions(true)" title="Expand Details">
                                <i class="fas fa-angles-down"></i> Expand
                            </button>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="toggleAllModuleAccordions(false)" title="Collapse Details">
                                <i class="fas fa-angles-up"></i> Collapse
                            </button>
                        </div>
                    </div>

                    <!-- Modules List Container -->
                    <div id="permissionsContainer" style="max-height:360px; overflow-y:auto; border:1px solid var(--border); border-radius:var(--radius-md); padding:10px; background:var(--bg-card);">
                        <?php
                        $groupedPermissions = [];
                        foreach ($permissions as $perm) {
                            $groupedPermissions[$perm['module']][] = $perm;
                        }
                        ?>
                        <?php foreach ($groupedPermissions as $module => $perms): ?>
                            <div class="module-perm-block mb-8" id="mod_block_<?= $module ?>" style="border:1px solid var(--border); border-radius:var(--radius-md); background:var(--bg-input); transition:all 0.2s;">
                                <!-- Module Header with Master Toggle -->
                                <div class="d-flex justify-content-between align-items-center" style="padding:9px 12px; background:rgba(255,255,255,0.02); border-bottom:1px solid var(--border); border-radius:var(--radius-md) var(--radius-md) 0 0;">
                                    <label class="form-check d-flex align-items-center gap-8 mb-0" style="cursor:pointer; font-weight:600; font-size:0.875rem; user-select:none;">
                                        <input type="checkbox" class="module-master-checkbox" id="mod_master_<?= $module ?>" data-module="<?= $module ?>" onchange="toggleModulePermissions('<?= $module ?>', this.checked)">
                                        <span style="color:var(--text-primary); text-transform:capitalize;">
                                            <i class="fas fa-folder text-primary me-2"></i>
                                            <?= ucwords(str_replace('_', ' ', $module)) ?>
                                        </span>
                                    </label>
                                    <div class="d-flex align-items-center gap-8">
                                        <span class="badge module-perm-count" id="mod_badge_<?= $module ?>" style="background:var(--bg-surface); border:1px solid var(--border); color:var(--text-muted); font-size:0.75rem; padding:2px 8px; font-family:'JetBrains Mono',monospace;">
                                            0/<?= count($perms) ?>
                                        </span>
                                        <button type="button" class="btn btn-ghost btn-xs p-1" onclick="toggleModuleDetails('<?= $module ?>')" title="Toggle Action Details">
                                            <i class="fas fa-chevron-down" id="mod_arrow_<?= $module ?>" style="font-size:0.75rem; transition:transform 0.2s;"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Individual Action Checkboxes -->
                                <div class="module-actions-container" id="mod_actions_<?= $module ?>" style="display:flex; flex-wrap:wrap; gap:8px; padding:10px 14px;">
                                    <?php foreach ($perms as $perm): ?>
                                        <label class="form-check d-flex align-items-center gap-6 mb-0" style="min-width:130px; cursor:pointer;">
                                            <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" class="perm-checkbox perm-child-<?= $module ?>" data-module="<?= $module ?>" onchange="updateModuleMasterState('<?= $module ?>')">
                                            <span style="font-size:0.8rem; color:var(--text-secondary); text-transform:capitalize;"><?= esc($perm['action']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div id="noModulesMatch" style="display:none; text-align:center; padding:24px; color:var(--text-muted);">
                            <i class="fas fa-search me-2"></i> No matching modules found.
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('roleModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveRole()"><i class="fas fa-save"></i> Save Role</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingRoleId = null;

    function openCreateRoleModal() {
        editingRoleId = null;
        document.getElementById('roleModalTitle').innerHTML = '<i class="fas fa-user-tag" style="color:var(--primary-light);margin-right:8px;"></i> Add New Role';
        document.getElementById('roleName').readOnly = false;
        resetForm('roleForm');
        document.getElementById('permModuleSearch').value = '';
        filterPermModules('');
        deselectAllPermissions();
        openModal('roleModal');
    }

    async function editRole(id) {
        const result = await ajaxGet(`<?= base_url('roles/edit') ?>/${id}`);
        if (result.success) {
            editingRoleId = id;
            document.getElementById('roleModalTitle').innerHTML = '<i class="fas fa-user-edit" style="color:var(--info);margin-right:8px;"></i> Edit Role';
            setFormValues('roleForm', result.role);

            if (result.role.is_system) {
                document.getElementById('roleName').readOnly = true;
            } else {
                document.getElementById('roleName').readOnly = false;
            }

            document.getElementById('permModuleSearch').value = '';
            filterPermModules('');

            // Set permissions
            deselectAllPermissions();
            result.permissions.forEach(permId => {
                const cb = document.querySelector(`.perm-checkbox[value="${permId}"]`);
                if (cb) cb.checked = true;
            });

            syncAllModules();
            openModal('roleModal');
        }
    }

    async function saveRole() {
        const form = getFormData('roleForm');
        const url = editingRoleId
            ? `<?= base_url('roles/update') ?>/${editingRoleId}`
            : '<?= base_url('roles') ?>';
        await ajaxPost(url, form, { closeModal: 'roleModal', reload: true });
    }

    function toggleModulePermissions(module, isChecked) {
        document.querySelectorAll(`.perm-child-${module}`).forEach(cb => {
            cb.checked = isChecked;
        });
        updateModuleMasterState(module);
    }

    function updateModuleMasterState(module) {
        const children = document.querySelectorAll(`.perm-child-${module}`);
        const master = document.getElementById(`mod_master_${module}`);
        const badge = document.getElementById(`mod_badge_${module}`);
        const block = document.getElementById(`mod_block_${module}`);
        
        const total = children.length;
        let checked = 0;
        children.forEach(cb => { if (cb.checked) checked++; });

        if (master) {
            if (checked === total && total > 0) {
                master.checked = true;
                master.indeterminate = false;
            } else if (checked > 0) {
                master.checked = false;
                master.indeterminate = true;
            } else {
                master.checked = false;
                master.indeterminate = false;
            }
        }

        if (badge) {
            badge.textContent = `${checked}/${total}`;
            if (checked === total && total > 0) {
                badge.style.background = 'rgba(16, 185, 129, 0.2)';
                badge.style.color = 'var(--success)';
                badge.style.borderColor = 'rgba(16, 185, 129, 0.4)';
            } else if (checked > 0) {
                badge.style.background = 'rgba(99, 102, 241, 0.2)';
                badge.style.color = 'var(--primary-light)';
                badge.style.borderColor = 'rgba(99, 102, 241, 0.4)';
            } else {
                badge.style.background = 'var(--bg-surface)';
                badge.style.color = 'var(--text-muted)';
                badge.style.borderColor = 'var(--border)';
            }
        }

        if (block) {
            if (checked > 0) {
                block.style.borderColor = 'rgba(99, 102, 241, 0.4)';
            } else {
                block.style.borderColor = 'var(--border)';
            }
        }

        updateTotalBadge();
    }

    function syncAllModules() {
        document.querySelectorAll('.module-master-checkbox').forEach(m => {
            const mod = m.getAttribute('data-module');
            if (mod) updateModuleMasterState(mod);
        });
        updateTotalBadge();
    }

    function updateTotalBadge() {
        const totalChecked = document.querySelectorAll('.perm-checkbox:checked').length;
        const totalBadge = document.getElementById('totalPermsCountBadge');
        if (totalBadge) {
            totalBadge.textContent = `${totalChecked} Selected`;
        }
    }

    function selectAllPermissions() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
        syncAllModules();
    }

    function deselectAllPermissions() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
        syncAllModules();
    }

    function toggleModuleDetails(module) {
        const container = document.getElementById(`mod_actions_${module}`);
        const arrow = document.getElementById(`mod_arrow_${module}`);
        if (!container) return;

        if (container.style.display === 'none') {
            container.style.display = 'flex';
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        } else {
            container.style.display = 'none';
            if (arrow) arrow.style.transform = 'rotate(-90deg)';
        }
    }

    function toggleAllModuleAccordions(expand) {
        document.querySelectorAll('.module-actions-container').forEach(c => {
            c.style.display = expand ? 'flex' : 'none';
        });
        document.querySelectorAll('.module-perm-block i.fa-chevron-down').forEach(arr => {
            arr.style.transform = expand ? 'rotate(0deg)' : 'rotate(-90deg)';
        });
    }

    function filterPermModules(query) {
        const q = (query || '').toLowerCase().trim();
        let visibleCount = 0;
        document.querySelectorAll('.module-perm-block').forEach(block => {
            const mod = block.id.replace('mod_block_', '');
            const text = block.textContent.toLowerCase();
            if (!q || text.includes(q) || mod.includes(q)) {
                block.style.display = '';
                visibleCount++;
            } else {
                block.style.display = 'none';
            }
        });
        const noMatch = document.getElementById('noModulesMatch');
        if (noMatch) noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
    }
</script>
<?= $this->endSection() ?>
