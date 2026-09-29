<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table            = 'branches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['name', 'code', 'address', 'city', 'state', 'pincode', 'phone', 'email', 'is_main', 'is_active'];
    protected $useTimestamps    = true;

    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('is_main', 'DESC')->findAll();
    }

    public function getAllWithUserCount()
    {
        return $this->select('branches.*, COUNT(users.id) as user_count')
                    ->join('users', 'users.branch_id = branches.id AND users.deleted_at IS NULL', 'left')
                    ->groupBy('branches.id')
                    ->orderBy('branches.is_main', 'DESC')
                    ->findAll();
    }
}
