<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'display_name', 'description', 'is_system'];
    protected $useTimestamps    = true;

    /**
     * Get all roles with user count
     */
    public function getAllWithUserCount()
    {
        return $this->select('roles.*, COUNT(DISTINCT users.id) as user_count, COUNT(DISTINCT rp.permission_id) as perm_count, COUNT(DISTINCT p.module) as module_count')
                    ->join('users', 'users.role_id = roles.id AND users.deleted_at IS NULL', 'left')
                    ->join('role_permissions rp', 'rp.role_id = roles.id', 'left')
                    ->join('permissions p', 'p.id = rp.permission_id', 'left')
                    ->groupBy('roles.id')
                    ->orderBy('roles.id', 'ASC')
                    ->findAll();
    }

    /**
     * Get permissions for a role
     */
    public function getPermissions(int $roleId): array
    {
        $db = \Config\Database::connect();
        return $db->table('permissions p')
            ->select('p.*')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->get()
            ->getResultArray();
    }

    /**
     * Sync role permissions
     */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $db = \Config\Database::connect();
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        if (!empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $permId) {
                $data[] = ['role_id' => $roleId, 'permission_id' => $permId];
            }
            $db->table('role_permissions')->insertBatch($data);
        }
    }
}
