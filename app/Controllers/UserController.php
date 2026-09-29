<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\BranchModel;
use App\Models\AuditLogModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $branchModel;
    protected $auditModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->roleModel   = new RoleModel();
        $this->branchModel = new BranchModel();
        $this->auditModel  = new AuditLogModel();
    }

    /**
     * List all users
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'User Management',
            'users'     => $this->userModel->getAllWithRelations(),
            'roles'     => $this->roleModel->findAll(),
            'branches'  => $this->branchModel->getActive(),
        ];
        return view('users/index', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[2]|max_length[150]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'permit_empty|regex_match[/^[0-9]{10}$/]',
            'password' => 'required|min_length[6]',
            'role_id'  => 'required|integer',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $userData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'   => $this->request->getPost('role_id'),
            'branch_id' => $this->request->getPost('branch_id') ?: null,
            'is_active' => $this->request->getPost('is_active') ?? 1,
            'commission_rate' => $this->request->getPost('commission_rate') ?: 0.00,
        ];

        $id = $this->userModel->insert($userData);
        $this->auditModel->logAction('create', 'users', $id, null, $userData);

        return $this->response->setJSON(['success' => true, 'message' => 'User created successfully.']);
    }

    /**
     * Get user data for editing
     */
    public function edit($id)
    {
        $user = $this->userModel->getUserWithRole($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }
        return $this->response->setJSON(['success' => true, 'user' => $user]);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        $rules = [
            'name'    => 'required|min_length[2]|max_length[150]',
            'email'   => "required|valid_email|is_unique[users.email,id,{$id}]",
            'phone'   => 'permit_empty|regex_match[/^[0-9]{10}$/]',
            'role_id' => 'required|integer',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $oldData = (array)$user;
        $userData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'role_id'   => $this->request->getPost('role_id'),
            'branch_id' => $this->request->getPost('branch_id') ?: null,
            'is_active' => $this->request->getPost('is_active') ?? 1,
            'commission_rate' => $this->request->getPost('commission_rate') ?: 0.00,
        ];

        // Update password only if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $userData);
        $this->auditModel->logAction('update', 'users', $id, $oldData, $userData);

        return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully.']);
    }

    /**
     * Delete user (soft delete)
     */
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot delete your own account.']);
        }

        $this->userModel->delete($id);
        $this->auditModel->logAction('delete', 'users', $id, (array)$user);

        return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
