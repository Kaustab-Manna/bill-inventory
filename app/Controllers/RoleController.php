<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\AuditLogModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $auditModel;

    public function __construct()
    {
        $this->roleModel  = new RoleModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * List all roles
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $data = [
            'pageTitle'   => 'Role Management',
            'roles'       => $this->roleModel->getAllWithUserCount(),
            'permissions' => $db->table('permissions')->orderBy('module', 'ASC')->orderBy('action', 'ASC')->get()->getResultArray(),
        ];
        return view('roles/index', $data);
    }

    /**
     * Store new role
     */
    public function store()
    {
        $rules = [
            'name'         => 'required|alpha_dash|is_unique[roles.name]|max_length[100]',
            'display_name' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $roleData = [
            'name'         => $this->request->getPost('name'),
            'display_name' => $this->request->getPost('display_name'),
            'description'  => $this->request->getPost('description'),
            'is_system'    => 0,
        ];

        $id = $this->roleModel->insert($roleData);

        // Assign permissions
        $permissions = $this->request->getPost('permissions') ?? [];
        if (!empty($permissions)) {
            $this->roleModel->syncPermissions($id, $permissions);
        }

        $this->auditModel->logAction('create', 'roles', $id, null, $roleData);

        return $this->response->setJSON(['success' => true, 'message' => 'Role created successfully.']);
    }

    /**
     * Get role data and permissions
     */
    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found.']);
        }

        $permissions = $this->roleModel->getPermissions($id);
        $permissionIds = array_column($permissions, 'id');

        return $this->response->setJSON([
            'success'     => true,
            'role'        => $role,
            'permissions' => $permissionIds,
        ]);
    }

    /**
     * Update role
     */
    public function update($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found.']);
        }

        if ($role->is_system && $this->request->getPost('name') && $role->name !== $this->request->getPost('name')) {
            return $this->response->setJSON(['success' => false, 'message' => 'System role name cannot be changed.']);
        }

        $rules = [
            'display_name' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $oldData = (array)$role;
        $roleData = [
            'display_name' => $this->request->getPost('display_name'),
            'description'  => $this->request->getPost('description'),
        ];

        if (!$role->is_system && $this->request->getPost('name')) {
            $roleData['name'] = $this->request->getPost('name');
        }

        $this->roleModel->update($id, $roleData);

        // Sync permissions
        $permissions = $this->request->getPost('permissions') ?? [];
        $this->roleModel->syncPermissions($id, (array)$permissions);

        // Refresh current user session permissions if updating their assigned role
        if (session()->get('role_id') == $id) {
            $userModel = new \App\Models\UserModel();
            session()->set('permissions', $userModel->getPermissions(session()->get('user_id')));
        }

        $this->auditModel->logAction('update', 'roles', $id, $oldData, $roleData);

        return $this->response->setJSON(['success' => true, 'message' => 'Role updated successfully.']);
    }

    /**
     * Delete role
     */
    public function delete($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found.']);
        }

        if ($role->is_system) {
            return $this->response->setJSON(['success' => false, 'message' => 'System roles cannot be deleted.']);
        }

        // Check if any users use this role
        $db = \Config\Database::connect();
        $userCount = $db->table('users')->where('role_id', $id)->where('deleted_at IS NULL')->countAllResults();
        if ($userCount > 0) {
            return $this->response->setJSON(['success' => false, 'message' => "Cannot delete: {$userCount} user(s) are assigned to this role."]);
        }

        // Delete permissions mapping first
        $db->table('role_permissions')->where('role_id', $id)->delete();
        $this->roleModel->delete($id);
        $this->auditModel->logAction('delete', 'roles', $id, (array)$role);

        return $this->response->setJSON(['success' => true, 'message' => 'Role deleted successfully.']);
    }
}
