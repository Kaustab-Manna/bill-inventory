<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'branch_id', 'role_id', 'name', 'email', 'phone',
        'password', 'avatar', 'two_factor_secret',
        'is_active', 'last_login', 'commission_rate'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'     => 'required|min_length[2]|max_length[150]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'role_id'  => 'required|integer',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
    ];

    /**
     * Get user with role info
     */
    public function getUserWithRole(int $id)
    {
        return $this->select('users.*, roles.name as role_name, roles.display_name as role_display_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->find($id);
    }

    /**
     * Get all users with roles and branch info
     */
    public function getAllWithRelations()
    {
        return $this->select('users.*, roles.display_name as role_display_name, branches.name as branch_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->join('branches', 'branches.id = users.branch_id', 'left')
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $email, string $password)
    {
        $user = $this->where('email', $email)->where('is_active', 1)->first();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    /**
     * Get user permissions as array
     */
    public function getPermissions(int $userId): array
    {
        $db = \Config\Database::connect();
        $result = $db->table('permissions p')
            ->select('p.module, p.action')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->join('users u', 'u.role_id = rp.role_id')
            ->where('u.id', $userId)
            ->get()
            ->getResultArray();

        $permissions = [];
        foreach ($result as $row) {
            $permissions[$row['module'] . '.' . $row['action']] = true;
        }
        return $permissions;
    }

    /**
     * Count active users
     */
    public function countActive(): int
    {
        return $this->where('is_active', 1)->countAllResults();
    }
}
