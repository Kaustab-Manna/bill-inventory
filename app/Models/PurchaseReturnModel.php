<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseReturnModel extends Model
{
    protected $table            = 'purchase_returns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'return_no', 'vendor_id', 'warehouse_id', 'subtotal', 'tax_amount',
        'total_amount', 'return_date', 'notes', 'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
