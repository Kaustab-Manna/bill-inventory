<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockTransferModel;
use App\Models\StockTransferItemModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class StockTransferController extends BaseController
{
    protected $transferModel;
    protected $transferItemModel;
    protected $warehouseModel;
    protected $db;

    public function __construct()
    {
        $this->transferModel = new StockTransferModel();
        $this->transferItemModel = new StockTransferItemModel();
        $this->warehouseModel = new WarehouseModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Stock Transfers',
            'transfers' => $this->transferModel->getTransfers()
        ];
        return view('inventory/stock_transfers/index', $data);
    }

    public function create()
    {
        $productModel = new ProductModel();
        $data = [
            'pageTitle' => 'Create Stock Transfer',
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll(),
            'reference_no' => 'TR-' . time()
        ];
        return view('inventory/stock_transfers/create', $data);
    }

    public function store()
    {
        $rules = [
            'from_warehouse_id' => 'required',
            'to_warehouse_id' => 'required|differs[from_warehouse_id]',
            'transfer_date' => 'required',
            'product_id.*' => 'required',
            'quantity.*' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => $this->validator->getErrors()]);
        }

        $this->db->transStart();

        $transferData = [
            'reference_no' => $this->request->getPost('reference_no'),
            'from_warehouse_id' => $this->request->getPost('from_warehouse_id'),
            'to_warehouse_id' => $this->request->getPost('to_warehouse_id'),
            'status' => 'pending',
            'transfer_date' => $this->request->getPost('transfer_date'),
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id')
        ];

        $transferId = $this->transferModel->insert($transferData);

        $productIds = $this->request->getPost('product_id');
        $quantities = $this->request->getPost('quantity');

        $items = [];
        for ($i = 0; $i < count($productIds); $i++) {
            $items[] = [
                'stock_transfer_id' => $transferId,
                'product_id' => $productIds[$i],
                'quantity' => $quantities[$i]
            ];
        }

        $this->transferItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create transfer']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Stock transfer created successfully']);
    }

    public function view($id)
    {
        $transfer = $this->transferModel->getTransfers($id);
        if (!$transfer) {
            return redirect()->to('/stock-transfers')->with('error', 'Transfer not found');
        }

        $data = [
            'pageTitle' => 'View Stock Transfer',
            'transfer' => $transfer,
            'items' => $this->transferItemModel->getItemsByTransferId($id)
        ];
        return view('inventory/stock_transfers/view', $data);
    }

    public function approve($id)
    {
        $transfer = $this->transferModel->find($id);
        if (!$transfer || $transfer->status !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid transfer status for approval']);
        }

        $this->transferModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Transfer approved successfully']);
    }

    public function complete($id)
    {
        $transfer = $this->transferModel->find($id);
        if (!$transfer || $transfer->status !== 'approved') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid transfer status for completion']);
        }

        $items = $this->transferItemModel->where('stock_transfer_id', $id)->findAll();
        $stockModel = new StockModel();

        // Verify stock in source warehouse first
        foreach ($items as $item) {
            $available = $stockModel->getAvailableStock($item->product_id, $transfer->from_warehouse_id);
            if ((float)$item->quantity > $available) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot complete transfer: Insufficient stock in source warehouse for product ID #' . $item->product_id
                ]);
            }
        }

        $this->db->transStart();

        foreach ($items as $item) {
            // Remove from source warehouse safely
            $deducted = $stockModel->updateStock($item->product_id, $transfer->from_warehouse_id, -$item->quantity, false);
            if (!$deducted) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Stock deduction failed for product ID #' . $item->product_id
                ]);
            }
            // Add to destination warehouse
            $stockModel->updateStock($item->product_id, $transfer->to_warehouse_id, $item->quantity);
        }

        $this->transferModel->update($id, [
            'status' => 'completed'
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to complete transfer']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Transfer completed successfully']);
    }

    public function delete($id)
    {
        $transfer = $this->transferModel->find($id);
        
        if (!$transfer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transfer not found']);
        }
        
        // Only allow deleting pending transfers
        if ($transfer->status !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only pending transfers can be deleted']);
        }

        $this->db->transStart();
        
        // Delete items (soft delete isn't on for items, but it cascades if we delete the parent, but parent uses soft delete so we should manually delete items or leave them orphaned but unreachable)
        // Since transfer uses soft delete, let's just delete the transfer
        $this->transferModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete transfer']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Transfer deleted successfully']);
    }
}
