<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAdjustmentModel extends Model
{
    protected $table            = 'stock_adjustments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'reference_no', 'warehouse_id', 'type', 
        'status', 'adjustment_date', 'notes', 'created_by', 'approved_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAdjustments($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('stock_adjustments.*, w.name as warehouse, u.name as creator')
            ->join('warehouses w', 'w.id = stock_adjustments.warehouse_id')
            ->join('users u', 'u.id = stock_adjustments.created_by')
            ->where('stock_adjustments.deleted_at', null)
            ->orderBy('stock_adjustments.id', 'DESC');

        if ($id) {
            return $builder->where('stock_adjustments.id', $id)->get()->getRow();
        }

        return $builder->get()->getResult();
    }
}
