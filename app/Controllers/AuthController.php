<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $auditModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Show login page
     */
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    /**
     * Process login
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->verifyPassword($email, $password);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        // Load user permissions
        $permissions = $this->userModel->getPermissions($user->id);

        // Get role info
        $userWithRole = $this->userModel->getUserWithRole($user->id);

        // Set session data
        $sessionData = [
            'user_id'          => $user->id,
            'user_name'        => $user->name,
            'user_email'       => $user->email,
            'user_avatar'      => $user->avatar,
            'role_id'          => $user->role_id,
            'role_name'        => $userWithRole->role_name,
            'role_display_name'=> $userWithRole->role_display_name,
            'branch_id'        => $user->branch_id,
            'is_active'        => $user->is_active,
            'permissions'      => $permissions,
            'logged_in'        => true,
        ];

        session()->set($sessionData);

        // Update last login
        $this->userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);

        // Audit log
        $this->auditModel->logAction('login', 'auth', $user->id);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auditModel->logAction('logout', 'auth', session()->get('user_id'));
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}
