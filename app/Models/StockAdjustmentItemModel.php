<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAdjustmentItemModel extends Model
{
    protected $table            = 'stock_adjustment_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'stock_adjustment_id', 'product_id', 'batch_id', 'quantity'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getItemsByAdjustmentId($adjustmentId)
    {
        return $this->db->table($this->table)
            ->select('stock_adjustment_items.*, p.name as product_name, p.sku, u.short_name as unit, b.batch_number')
            ->join('products p', 'p.id = stock_adjustment_items.product_id')
            ->join('units u', 'u.id = p.unit_id')
            ->join('batches b', 'b.id = stock_adjustment_items.batch_id', 'left')
            ->where('stock_adjustment_id', $adjustmentId)
            ->get()->getResult();
    }
}
