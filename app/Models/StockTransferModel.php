<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTransferModel extends Model
{
    protected $table            = 'stock_transfers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'reference_no', 'from_warehouse_id', 'to_warehouse_id', 
        'status', 'transfer_date', 'notes', 'created_by', 'approved_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getTransfers($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('stock_transfers.*, fw.name as from_warehouse, tw.name as to_warehouse, u.name as creator')
            ->join('warehouses fw', 'fw.id = stock_transfers.from_warehouse_id')
            ->join('warehouses tw', 'tw.id = stock_transfers.to_warehouse_id')
            ->join('users u', 'u.id = stock_transfers.created_by')
            ->where('stock_transfers.deleted_at', null)
            ->orderBy('stock_transfers.id', 'DESC');

        if ($id) {
            return $builder->where('stock_transfers.id', $id)->get()->getRow();
        }

        return $builder->get()->getResult();
    }
}
