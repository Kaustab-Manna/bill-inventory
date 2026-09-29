<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockAdjustmentModel;
use App\Models\StockAdjustmentItemModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class StockAdjustmentController extends BaseController
{
    protected $adjustmentModel;
    protected $adjustmentItemModel;
    protected $warehouseModel;
    protected $db;

    public function __construct()
    {
        $this->adjustmentModel = new StockAdjustmentModel();
        $this->adjustmentItemModel = new StockAdjustmentItemModel();
        $this->warehouseModel = new WarehouseModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Stock Adjustments',
            'adjustments' => $this->adjustmentModel->getAdjustments()
        ];
        return view('inventory/stock_adjustments/index', $data);
    }

    public function create()
    {
        $productModel = new ProductModel();
        $data = [
            'pageTitle' => 'Create Stock Adjustment',
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll(),
            'reference_no' => 'ADJ-' . time()
        ];
        return view('inventory/stock_adjustments/create', $data);
    }

    public function store()
    {
        $rules = [
            'warehouse_id' => 'required',
            'type' => 'required|in_list[addition,subtraction]',
            'adjustment_date' => 'required',
            'product_id.*' => 'required',
            'quantity.*' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => $this->validator->getErrors()]);
        }

        $this->db->transStart();

        $adjData = [
            'reference_no' => $this->request->getPost('reference_no'),
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'type' => $this->request->getPost('type'),
            'status' => 'pending',
            'adjustment_date' => $this->request->getPost('adjustment_date'),
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id')
        ];

        $adjId = $this->adjustmentModel->insert($adjData);

        $productIds = $this->request->getPost('product_id');
        $quantities = $this->request->getPost('quantity');

        $items = [];
        for ($i = 0; $i < count($productIds); $i++) {
            $items[] = [
                'stock_adjustment_id' => $adjId,
                'product_id' => $productIds[$i],
                'quantity' => $quantities[$i]
            ];
        }

        $this->adjustmentItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create adjustment']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Stock adjustment created successfully']);
    }

    public function view($id)
    {
        $adjustment = $this->adjustmentModel->getAdjustments($id);
        if (!$adjustment) {
            return redirect()->to('/stock-adjustments')->with('error', 'Adjustment not found');
        }

        $data = [
            'pageTitle' => 'View Stock Adjustment',
            'adjustment' => $adjustment,
            'items' => $this->adjustmentItemModel->getItemsByAdjustmentId($id)
        ];
        return view('inventory/stock_adjustments/view', $data);
    }

    public function approve($id)
    {
        $adjustment = $this->adjustmentModel->find($id);
        if (!$adjustment || $adjustment->status !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status for approval']);
        }

        $this->adjustmentModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Adjustment approved successfully']);
    }

    public function complete($id)
    {
        $adjustment = $this->adjustmentModel->find($id);
        if (!$adjustment || $adjustment->status !== 'approved') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status for completion']);
        }

        $items = $this->adjustmentItemModel->where('stock_adjustment_id', $id)->findAll();
        $stockModel = new StockModel();

        $this->db->transStart();

        foreach ($items as $item) {
            if ($adjustment->type === 'addition') {
                $stockModel->updateStock($item->product_id, $adjustment->warehouse_id, $item->quantity);
            } else { // subtraction
                $stockModel->updateStock($item->product_id, $adjustment->warehouse_id, -$item->quantity);
            }
        }

        $this->adjustmentModel->update($id, [
            'status' => 'completed'
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to complete adjustment']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Adjustment completed successfully']);
    }

    public function delete($id)
    {
        $adjustment = $this->adjustmentModel->find($id);
        
        if (!$adjustment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Adjustment not found']);
        }
        
        if ($adjustment->status !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only pending adjustments can be deleted']);
        }

        $this->db->transStart();
        $this->adjustmentModel->delete($id);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete adjustment']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Adjustment deleted successfully']);
    }
}
