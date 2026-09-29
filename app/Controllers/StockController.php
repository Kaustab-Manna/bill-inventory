<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\WarehouseModel;

class StockController extends BaseController
{
    protected $stockModel;

    public function __construct()
    {
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        $warehouseModel = new WarehouseModel();
        $batchModel = new \App\Models\BatchModel();
        
        $warehouseId = $this->request->getGet('warehouse_id');
        
        $data = [
            'pageTitle'         => 'Stock Overview',
            'stock'             => $this->stockModel->getStockOverview($warehouseId),
            'warehouses'        => $warehouseModel->where('is_active', 1)->findAll(),
            'selectedWarehouse' => $warehouseId,
            'batches'           => $batchModel->where('quantity >', 0)
                                       ->where('expiry_date IS NOT NULL')
                                       ->orderBy('expiry_date', 'ASC')
                                       ->findAll()
        ];
        return view('inventory/stock/index', $data);
    }

    public function exportCSV()
    {
        $warehouseId = $this->request->getGet('warehouse_id');
        $stock = $this->stockModel->getStockOverview($warehouseId);

        $filename = 'stock_overview_' . date('Ymd_His') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; "); 

        $file = fopen('php://output', 'w');

        $header = array("Product", "SKU", "Warehouse", "In Stock", "Reserved", "Available"); 
        fputcsv($file, $header);

        foreach ($stock as $item) {
            $available = $item->quantity - $item->reserved_qty;
            $line = array(
                $item->product_name,
                $item->sku,
                $item->warehouse_name,
                $item->quantity,
                $item->reserved_qty,
                $available
            );
            fputcsv($file, $line); 
        }

        fclose($file); 
        exit;
    }
}
