<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseReturnModel;
use App\Models\PurchaseReturnItemModel;
use App\Models\VendorModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class PurchaseReturnController extends BaseController
{
    protected $db;
    protected $returnModel;
    protected $returnItemModel;
    protected $stockModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->returnModel = new PurchaseReturnModel();
        $this->returnItemModel = new PurchaseReturnItemModel();
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Purchase Returns',
            'returns' => $this->returnModel
                          ->select('purchase_returns.*, vendors.name as vendor_name')
                          ->join('vendors', 'vendors.id = purchase_returns.vendor_id', 'left')
                          ->orderBy('purchase_returns.id', 'DESC')
                          ->findAll()
        ];
        return view('purchase_returns/index', $data);
    }

    public function create()
    {
        $vendorModel = new VendorModel();
        $warehouseModel = new WarehouseModel();
        $productModel = new ProductModel();

        $data = [
            'pageTitle' => 'Create Purchase Return',
            'vendors' => $vendorModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll()
        ];
        return view('purchase_returns/create', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['product_id'])) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product.');
        }

        // Validate stock in warehouse
        $productModel = new ProductModel();
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = (float)$post['quantity'][$i];
            $price = (float)($post['unit_price'][$i] ?? 0);
            $productId = (int)$post['product_id'][$i];
            $product = $productModel->find($productId);
            $productName = $product ? $product->name : ('Product #' . $productId);

            if ($qty <= 0) {
                return redirect()->back()->withInput()->with('error', "Return quantity for '{$productName}' must be greater than zero.");
            }
            if ($price < 0) {
                return redirect()->back()->withInput()->with('error', "Unit price for '{$productName}' cannot be negative.");
            }

            $available = $this->stockModel->getAvailableStock($productId, $post['warehouse_id']);
            if ($qty > $available) {
                return redirect()->back()->withInput()->with('error', "Cannot return more than available stock for '{$productName}' in warehouse. Available: {$available}, Requested: {$qty}.");
            }
        }

        $this->db->transStart();

        $subtotal = max(0, (float)$post['subtotal']);
        $totalAmount = max(0, (float)$post['total_amount']);

        $returnData = [
            'return_no' => 'PRET-' . strtoupper(uniqid()),
            'vendor_id' => $post['vendor_id'],
            'warehouse_id' => $post['warehouse_id'],
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'return_date' => $post['return_date'] ?: date('Y-m-d H:i:s'),
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        $returnId = $this->returnModel->insert($returnData);

        $items = [];
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = $post['quantity'][$i];
            $price = $post['unit_price'][$i];
            $productId = $post['product_id'][$i];
            
            $items[] = [
                'purchase_return_id' => $returnId,
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];

            // Deduct stock because we are returning to the vendor
            $this->stockModel->updateStock($productId, $post['warehouse_id'], -$qty);
        }

        $this->returnItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to process Purchase Return.');
        }

        return redirect()->to('/purchase-returns')->with('success', 'Purchase Return processed successfully.');
    }

    public function view($id)
    {
        $return = $this->returnModel
                   ->select('purchase_returns.*, vendors.name as vendor_name, vendors.email, vendors.phone, vendors.address, warehouses.name as warehouse_name')
                   ->join('vendors', 'vendors.id = purchase_returns.vendor_id', 'left')
                   ->join('warehouses', 'warehouses.id = purchase_returns.warehouse_id', 'left')
                   ->find($id);

        if (!$return) {
            return redirect()->to('/purchase-returns')->with('error', 'Purchase Return not found');
        }

        $items = $this->returnItemModel
                      ->select('purchase_return_items.*, products.name as product_name')
                      ->join('products', 'products.id = purchase_return_items.product_id', 'left')
                      ->where('purchase_return_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Purchase Return',
            'return' => $return,
            'items' => $items
        ];

        return view('purchase_returns/view', $data);
    }

    public function delete($id)
    {
        $return = $this->returnModel->find($id);
        if (!$return) {
            return redirect()->to('/purchase-returns')->with('error', 'Return not found.');
        }

        $items = $this->returnItemModel->where('purchase_return_id', $id)->findAll();

        $this->db->transStart();

        // Revert stock (add the returned goods back to the warehouse)
        foreach ($items as $item) {
            $this->stockModel->updateStock($item->product_id, $return->warehouse_id, $item->quantity);
        }

        $this->returnItemModel->where('purchase_return_id', $id)->delete();
        $this->returnModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/purchase-returns')->with('error', 'Failed to delete Purchase Return.');
        }

        return redirect()->to('/purchase-returns')->with('success', 'Purchase Return deleted successfully.');
    }
}
