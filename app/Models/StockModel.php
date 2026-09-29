<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'warehouse_id', 'quantity', 'reserved_qty'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getStockOverview($warehouseId = null)
    {
        $builder = $this->select('stock.*, products.name as product_name, products.sku, products.min_stock_level, products.image, warehouses.name as warehouse_name, units.short_name as unit_name')
                    ->join('products', 'products.id = stock.product_id', 'left')
                    ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left')
                    ->join('units', 'units.id = products.unit_id', 'left');

        if ($warehouseId) {
            $builder->where('stock.warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }

    public function addStock($productId, $warehouseId, $quantity)
    {
        $stock = $this->where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
        if ($stock) {
            $this->update($stock->id, ['quantity' => $stock->quantity + $quantity]);
        } else {
            $this->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity
            ]);
        }
    }

    public function getAvailableStock($productId, $warehouseId)
    {
        $stock = $this->where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
        if (!$stock) {
            return 0.0;
        }
        $available = (float)$stock->quantity - (float)($stock->reserved_qty ?? 0);
        return max(0.0, $available);
    }

    public function hasSufficientStock($productId, $warehouseId, $requestedQty)
    {
        $available = $this->getAvailableStock($productId, $warehouseId);
        return $available >= (float)$requestedQty;
    }

    public function deductStock($productId, $warehouseId, $quantity)
    {
        $stock = $this->where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
        if ($stock && ($stock->quantity - ($stock->reserved_qty ?? 0)) >= $quantity) {
            $this->update($stock->id, ['quantity' => $stock->quantity - $quantity]);
            return true;
        }
        return false;
    }

    public function updateStock($productId, $warehouseId, $quantityChange, $allowNegative = false)
    {
        if ($quantityChange == 0) return true;

        $stock = $this->where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
        if ($stock) {
            $newQuantity = (float)$stock->quantity + (float)$quantityChange;
            if (!$allowNegative && $newQuantity < 0) {
                return false;
            }
            $this->update($stock->id, ['quantity' => $newQuantity]);
        } else {
            if (!$allowNegative && $quantityChange < 0) {
                return false;
            }
            $this->insert([
                'product_id'   => $productId,
                'warehouse_id' => $warehouseId,
                'quantity'     => $quantityChange
            ]);
        }
        return true;
    }
}
