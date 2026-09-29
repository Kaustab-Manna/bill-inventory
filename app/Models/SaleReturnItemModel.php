<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleReturnItemModel extends Model
{
    protected $table            = 'sale_return_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_return_id', 'product_id', 'item_condition', 'inspection_status', 'quantity', 'unit_price', 'subtotal', 'tax_amount', 'total'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
