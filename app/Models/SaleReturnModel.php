<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleReturnModel extends Model
{
    protected $table            = 'sale_returns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'return_no', 'sale_id', 'customer_id', 'warehouse_id', 'subtotal', 
        'tax_amount', 'total_amount', 'status', 'return_date', 'notes', 'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
