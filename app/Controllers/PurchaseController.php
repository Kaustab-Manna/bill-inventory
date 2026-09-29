<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\PurchaseItemModel;
use App\Models\VendorModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class PurchaseController extends BaseController
{
    protected $db;
    protected $purchaseModel;
    protected $purchaseItemModel;
    protected $stockModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->purchaseModel = new PurchaseModel();
        $this->purchaseItemModel = new PurchaseItemModel();
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Purchase Invoices',
            'purchases' => $this->purchaseModel
                          ->select('purchases.*, vendors.name as vendor_name')
                          ->join('vendors', 'vendors.id = purchases.vendor_id', 'left')
                          ->orderBy('purchases.id', 'DESC')
                          ->findAll()
        ];
        return view('purchases/index', $data);
    }

    public function create()
    {
        $vendorModel = new VendorModel();
        $warehouseModel = new WarehouseModel();
        $productModel = new ProductModel();

        $data = [
            'pageTitle' => 'Create Purchase Invoice',
            'vendors' => $vendorModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll()
        ];
        return view('purchases/create', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['product_id'])) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product.');
        }

        $productModel = new ProductModel();
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = (float)($post['quantity'][$i] ?? 0);
            $price = (float)($post['unit_price'][$i] ?? 0);
            $productId = (int)$post['product_id'][$i];
            $product = $productModel->find($productId);
            $productName = $product ? $product->name : ('Product #' . $productId);

            if ($qty <= 0) {
                return redirect()->back()->withInput()->with('error', "Quantity for '{$productName}' must be greater than zero.");
            }
            if ($price < 0) {
                return redirect()->back()->withInput()->with('error', "Unit price for '{$productName}' cannot be negative.");
            }
        }

        $this->db->transStart();

        $subtotal = max(0, (float)$post['subtotal']);
        $discountPercent = max(0, min(100, (float)($post['discount_percent'] ?? 0)));
        $discount = max(0, (float)($post['discount'] ?? 0));
        $totalAmount = max(0, (float)$post['total_amount']);

        $purchaseData = [
            'invoice_no' => 'PINV-' . strtoupper(uniqid()),
            'vendor_id' => $post['vendor_id'],
            'warehouse_id' => $post['warehouse_id'],
            'subtotal' => $subtotal,
            'tax_amount' => 0, 
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'purchase_date' => $post['purchase_date'] ?: date('Y-m-d H:i:s'),
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        $purchaseId = $this->purchaseModel->insert($purchaseData);

        $batchModel = new \App\Models\BatchModel();

        $items = [];
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = $post['quantity'][$i];
            $price = $post['unit_price'][$i];
            $productId = $post['product_id'][$i];
            $expiryDate = !empty($post['expiry_date'][$i]) ? $post['expiry_date'][$i] : null;
            
            $items[] = [
                'purchase_id' => $purchaseId,
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];

            // Increase stock since we received goods
            $this->stockModel->updateStock($productId, $post['warehouse_id'], $qty);

            // Add to batches if expiry is provided
            if ($expiryDate) {
                $batchModel->insert([
                    'product_id' => $productId,
                    'warehouse_id' => $post['warehouse_id'],
                    'batch_number' => 'B-' . date('Ymd') . '-' . rand(100, 999),
                    'expiry_date' => $expiryDate,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'selling_price' => 0,
                    'is_active' => 1
                ]);
            }
        }

        $this->purchaseItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create Purchase Invoice.');
        }

        return redirect()->to('/purchases')->with('success', 'Purchase Invoice created successfully.');
    }

    public function view($id)
    {
        $purchase = $this->purchaseModel
                   ->select('purchases.*, vendors.name as vendor_name, vendors.email, vendors.phone, vendors.address, warehouses.name as warehouse_name')
                   ->join('vendors', 'vendors.id = purchases.vendor_id', 'left')
                   ->join('warehouses', 'warehouses.id = purchases.warehouse_id', 'left')
                   ->find($id);

        if (!$purchase) {
            return redirect()->to('/purchases')->with('error', 'Purchase Invoice not found');
        }

        $items = $this->purchaseItemModel
                      ->select('purchase_items.*, products.name as product_name')
                      ->join('products', 'products.id = purchase_items.product_id', 'left')
                      ->where('purchase_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Purchase Invoice',
            'purchase' => $purchase,
            'items' => $items
        ];

        return view('purchases/view', $data);
    }

    public function updatePayment($id)
    {
        $purchase = $this->purchaseModel->find($id);
        if (!$purchase) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $addAmount = $this->request->getPost('amount');
        if (!is_numeric($addAmount) || $addAmount <= 0) {
            return redirect()->back()->with('error', 'Invalid amount.');
        }

        $newPaidAmount = $purchase->paid_amount + $addAmount;
        $status = 'unpaid';

        if ($newPaidAmount >= $purchase->total_amount) {
            $newPaidAmount = $purchase->total_amount;
            $status = 'paid';
        } elseif ($newPaidAmount > 0) {
            $status = 'partial';
        }

        $this->purchaseModel->update($id, [
            'paid_amount' => $newPaidAmount,
            'payment_status' => $status
        ]);

        return redirect()->back()->with('success', 'Payment updated successfully.');
    }

    public function delete($id)
    {
        $purchase = $this->purchaseModel->find($id);
        if (!$purchase) {
            return redirect()->to('/purchases')->with('error', 'Invoice not found.');
        }

        $items = $this->purchaseItemModel->where('purchase_id', $id)->findAll();

        $this->db->transStart();

        // Revert stock (deduct the purchased goods)
        foreach ($items as $item) {
            $this->stockModel->updateStock($item->product_id, $purchase->warehouse_id, -$item->quantity);
        }

        $this->purchaseItemModel->where('purchase_id', $id)->delete();
        $this->purchaseModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/purchases')->with('error', 'Failed to delete Purchase Invoice.');
        }

        return redirect()->to('/purchases')->with('success', 'Purchase Invoice deleted successfully.');
    }
}
