<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table            = 'warehouses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id', 'name', 'code', 'address', 'phone', 'manager_id', 'is_default', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllWithDetails()
    {
        return $this->select('warehouses.*, branches.name as branch_name, users.name as manager_name')
                    ->join('branches', 'branches.id = warehouses.branch_id', 'left')
                    ->join('users', 'users.id = warehouses.manager_id', 'left')
                    ->findAll();
    }
}
