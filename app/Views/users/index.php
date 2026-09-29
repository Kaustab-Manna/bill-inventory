<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-1 d-flex align-items-center gap-2" style="font-size:1.5rem; font-weight:700; color:var(--text-primary);">
            <i class="fas fa-user-shield" style="color:var(--primary);"></i> User Management
        </h2>
        <p class="mb-0" style="color:var(--text-muted); font-size:0.875rem;">Manage system users, assigned roles, branch permissions, and access</p>
    </div>
    <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2" onclick="openCreateUserModal()" style="border-radius:var(--radius-md); font-weight:600;">
        <i class="fas fa-user-plus"></i> Add New User
    </button>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm" style="background:var(--bg-card); border:1px solid var(--border) !important; border-radius:var(--radius-xl); overflow:hidden;">
    
    <!-- Filter Toolbar -->
    <div class="p-3 border-bottom" style="background:rgba(99, 102, 241, 0.02); border-color:var(--border) !important;">
        <div class="row g-2 align-items-center">
            <div class="col-lg-5 col-md-12">
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background:var(--bg-input); border-color:var(--border); color:var(--text-muted);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Search by name, email, or mobile..." id="userSearch" onkeyup="filterUsers()" style="background:var(--bg-input); border-color:var(--border); color:var(--text-primary);">
                </div>
            </div>
            <div class="col-lg-3 col-md-5 col-sm-6">
                <select class="form-select" id="roleFilter" onchange="filterUsers()" style="background:var(--bg-input); border-color:var(--border); color:var(--text-primary);">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role->display_name) ?>"><?= esc($role->display_name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-5 col-sm-6">
                <select class="form-select" id="statusFilter" onchange="filterUsers()" style="background:var(--bg-input); border-color:var(--border); color:var(--text-primary);">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-12 text-end">
                <button class="btn w-100" onclick="resetFilters()" title="Reset Filters" style="background:var(--bg-surface); border:1px solid var(--border); color:var(--text-muted);">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="usersTable" style="color:var(--text-primary); border-color:var(--border);">
            <thead style="background:rgba(99, 102, 241, 0.06); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted);">
                <tr>
                    <th class="ps-4" style="width:50px;">#</th>
                    <th>User</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th class="text-center">Commission</th>
                    <th class="text-center">Status</th>
                    <th>Last Login</th>
                    <th class="text-end pe-4" style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <h5>No users found</h5>
                                <p class="small">Click "Add New User" above to create an account.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr class="user-row" data-name="<?= strtolower(esc($user->name)) ?>" data-email="<?= strtolower(esc($user->email)) ?>" data-phone="<?= esc($user->phone ?? '') ?>" data-role="<?= strtolower(esc($user->role_display_name)) ?>" data-status="<?= $user->is_active ? 'active' : 'inactive' ?>">
                            <td class="ps-4" style="color:var(--text-muted); font-size:0.85rem; font-weight:600;"><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width:38px; height:38px; font-size:0.85rem; background:linear-gradient(135deg, var(--primary), #8B5CF6); flex-shrink:0;">
                                        <?= strtoupper(substr($user->name, 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color:var(--text-primary); font-size:0.9rem;"><?= esc($user->name) ?></div>
                                        <?php if (!empty($user->phone)): ?>
                                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                                <i class="fas fa-phone-alt me-1" style="font-size:0.65rem;"></i> <?= esc($user->phone) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:0.85rem; color:var(--text-secondary);"><?= esc($user->email) ?></span>
                            </td>
                            <td>
                                <?php 
                                    $roleBadge = 'bg-secondary';
                                    $roleLower = strtolower($user->role_name ?? '');
                                    if (str_contains($roleLower, 'admin')) {
                                        $roleBadge = 'bg-primary';
                                    } elseif (str_contains($roleLower, 'manager')) {
                                        $roleBadge = 'bg-info text-white';
                                    } elseif (str_contains($roleLower, 'sales')) {
                                        $roleBadge = 'bg-success';
                                    }
                                ?>
                                <span class="badge <?= $roleBadge ?> px-2 py-1" style="font-size:0.75rem; font-weight:600; letter-spacing:0.02em;">
                                    <?= esc($user->role_display_name) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size:0.85rem; color:var(--text-secondary);">
                                    <i class="fas fa-store me-1" style="color:var(--text-muted); font-size:0.8rem;"></i>
                                    <?= esc($user->branch_name ?? 'All Branches') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($user->commission_rate > 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem; font-weight:600;">
                                        <?= number_format($user->commission_rate, 2) ?>%
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:0.85rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($user->is_active): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;">
                                        <i class="fas fa-circle me-1" style="font-size:0.45rem;"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size:0.75rem;">
                                        <i class="fas fa-circle me-1" style="font-size:0.45rem;"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:0.8rem; color:var(--text-muted);">
                                    <?php if ($user->last_login): ?>
                                        <i class="far fa-clock me-1" style="font-size:0.75rem;"></i> <?= date('d M Y H:i', strtotime($user->last_login)) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" title="Edit User" onclick="editUser(<?= $user->id ?>)" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:var(--radius-sm);">
                                        <i class="fas fa-pen" style="font-size:0.75rem;"></i>
                                    </button>
                                    <?php if ($user->id != session()->get('user_id')): ?>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete User" onclick="confirmDelete('<?= base_url('users/delete/' . $user->id) ?>', '<?= esc($user->name) ?>')" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:var(--radius-sm);">
                                            <i class="fas fa-trash" style="font-size:0.75rem;"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create / Edit User Modal -->
<div class="modal-overlay" id="userModal">
    <div class="modal shadow-lg" style="max-width: 640px; border-radius: var(--radius-xl); background: var(--bg-modal); border: 1px solid var(--border);">
        <div class="modal-header border-bottom p-4" style="border-color: var(--border) !important;">
            <h4 class="modal-title mb-0 d-flex align-items-center gap-2" id="userModalTitle" style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">
                <i class="fas fa-user-plus text-primary"></i> Add New User
            </h4>
            <button type="button" class="modal-close" onclick="closeModal('userModal')" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body p-4">
            <form id="userForm">
                <input type="hidden" name="id" id="userId">
                <input type="hidden" name="user_id" id="userIdField">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" class="form-control" placeholder="user@example.com" required style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Phone Number
                        </label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" id="passwordLabel" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" id="userPassword" style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role_id" class="form-select" required style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->id ?>"><?= esc($role->display_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Assigned Branch
                        </label>
                        <select name="branch_id" class="form-select" style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                            <option value="">All Branches</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch->id ?>"><?= esc($branch->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Commission Rate (%)
                        </label>
                        <input type="number" step="0.01" name="commission_rate" class="form-control" placeholder="0.00" value="0.00" style="background: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                        <small style="color:var(--text-muted); font-size:0.75rem; display:block; margin-top:4px;">Applies to sales made by this user</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Account Status
                        </label>
                        <div class="p-2 px-3 border rounded d-flex align-items-center justify-content-between" style="background:var(--bg-input); border-color:var(--border) !important; height:42px;">
                            <span style="font-size:0.875rem; color:var(--text-primary);">Active Status</span>
                            <label class="form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer border-top p-3 px-4 d-flex justify-content-end gap-2" style="border-color: var(--border) !important;">
            <button type="button" class="btn btn-outline-secondary px-3" onclick="closeModal('userModal')" style="border-color:var(--border); color:var(--text-muted);">
                Cancel
            </button>
            <button type="button" class="btn btn-primary px-4" onclick="saveUser()" id="saveUserBtn">
                <i class="fas fa-save me-1"></i> Save User
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let editingUserId = null;

    function openCreateUserModal() {
        editingUserId = null;
        document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-plus text-primary"></i> Add New User';
        document.getElementById('passwordLabel').innerHTML = 'Password <span class="text-danger">*</span>';
        document.getElementById('userPassword').required = true;
        resetForm('userForm');
        document.querySelector('#userForm [name="is_active"]').checked = true;
        document.querySelector('#userForm [name="commission_rate"]').value = '0.00';
        openModal('userModal');
    }

    async function editUser(id) {
        const result = await ajaxGet(`<?= base_url('users/edit') ?>/${id}`);
        if (result.success) {
            editingUserId = id;
            document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-edit text-info"></i> Edit User';
            document.getElementById('passwordLabel').innerHTML = 'Password <small class="text-muted fw-normal">(leave blank to keep)</small>';
            document.getElementById('userPassword').required = false;

            setFormValues('userForm', result.user);
            document.querySelector('#userForm [name="is_active"]').checked = result.user.is_active == 1;
            document.getElementById('userPassword').value = '';

            openModal('userModal');
        }
    }

    async function saveUser() {
        const form = getFormData('userForm');
        // Handle checkbox - if unchecked, set to 0
        if (!document.querySelector('#userForm [name="is_active"]').checked) {
            form.set('is_active', '0');
        }

        const url = editingUserId
            ? `<?= base_url('users/update') ?>/${editingUserId}`
            : '<?= base_url('users') ?>';

        await ajaxPost(url, form, { closeModal: 'userModal', reload: true });
    }

    function filterUsers() {
        const searchVal = document.getElementById('userSearch').value.toLowerCase().trim();
        const roleVal = document.getElementById('roleFilter').value.toLowerCase().trim();
        const statusVal = document.getElementById('statusFilter').value.toLowerCase().trim();

        const rows = document.querySelectorAll('#usersTable tbody tr.user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';
            const phone = row.getAttribute('data-phone') || '';
            const role = row.getAttribute('data-role') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal || name.includes(searchVal) || email.includes(searchVal) || phone.includes(searchVal);
            const matchesRole = !roleVal || role.includes(roleVal);
            const matchesStatus = !statusVal || status === statusVal;

            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        document.getElementById('userSearch').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        filterUsers();
    }
</script>
<?= $this->endSection() ?>
