<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleReturnItemModel;
use App\Models\SaleReturnModel;
use App\Models\StockModel;

class InspectionController extends BaseController
{
    public function index()
    {
        $itemModel = new SaleReturnItemModel();
        
        $data = [
            'pageTitle' => 'QC Inspections (Returned Items)',
            'items' => $itemModel->select('sale_return_items.*, sale_returns.return_no, sale_returns.warehouse_id, products.name as product_name, warehouses.name as warehouse_name')
                                 ->join('sale_returns', 'sale_returns.id = sale_return_items.sale_return_id')
                                 ->join('products', 'products.id = sale_return_items.product_id')
                                 ->join('warehouses', 'warehouses.id = sale_returns.warehouse_id')
                                 ->where('inspection_status !=', 'na')
                                 ->orderBy('sale_return_items.id', 'DESC')
                                 ->findAll()
        ];
        return view('inspections/index', $data);
    }

    public function approve($id)
    {
        $itemModel = new SaleReturnItemModel();
        $item = $itemModel->find($id);

        if (!$item || $item->inspection_status != 'pending') {
            return redirect()->back()->with('error', 'Invalid inspection item.');
        }

        $returnModel = new SaleReturnModel();
        $return = $returnModel->find($item->sale_return_id);
        
        $stockModel = new StockModel();

        // Update status
        $itemModel->update($id, ['inspection_status' => 'passed']);

        // Add back to stock since it passed inspection
        $stockModel->updateStock($item->product_id, $return->warehouse_id, $item->quantity);

        return redirect()->back()->with('success', 'Item passed inspection and returned to stock.');
    }

    public function reject($id)
    {
        $itemModel = new SaleReturnItemModel();
        $item = $itemModel->find($id);

        if (!$item || $item->inspection_status != 'pending') {
            return redirect()->back()->with('error', 'Invalid inspection item.');
        }

        // Just update status to failed. Stock remains out of inventory (written off).
        $itemModel->update($id, ['inspection_status' => 'failed']);

        return redirect()->back()->with('success', 'Item failed inspection. Stock written off.');
    }
}
