<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\VendorModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;

class PurchaseOrderController extends BaseController
{
    protected $db;
    protected $poModel;
    protected $poItemModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->poModel = new PurchaseOrderModel();
        $this->poItemModel = new PurchaseOrderItemModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Purchase Orders',
            'pos' => $this->poModel
                          ->select('purchase_orders.*, vendors.name as vendor_name')
                          ->join('vendors', 'vendors.id = purchase_orders.vendor_id', 'left')
                          ->orderBy('purchase_orders.id', 'DESC')
                          ->findAll()
        ];
        return view('purchase_orders/index', $data);
    }

    public function create()
    {
        $vendorModel = new VendorModel();
        $warehouseModel = new WarehouseModel();
        $productModel = new ProductModel();

        $data = [
            'pageTitle' => 'Create Purchase Order',
            'vendors' => $vendorModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll()
        ];
        return view('purchase_orders/create', $data);
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

        $poData = [
            'po_no' => 'PO-' . strtoupper(uniqid()),
            'vendor_id' => $post['vendor_id'],
            'warehouse_id' => $post['warehouse_id'],
            'subtotal' => $subtotal,
            'tax_amount' => 0, 
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'expected_date' => $post['expected_date'] ?: null,
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        $poId = $this->poModel->insert($poData);

        $items = [];
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = $post['quantity'][$i];
            $price = $post['unit_price'][$i];
            
            $items[] = [
                'po_id' => $poId,
                'product_id' => $post['product_id'][$i],
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];
        }

        $this->poItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create Purchase Order.');
        }

        return redirect()->to('/purchase-orders')->with('success', 'Purchase Order created successfully.');
    }

    public function view($id)
    {
        $po = $this->poModel
                   ->select('purchase_orders.*, vendors.name as vendor_name, vendors.email, vendors.phone, vendors.address, warehouses.name as warehouse_name')
                   ->join('vendors', 'vendors.id = purchase_orders.vendor_id', 'left')
                   ->join('warehouses', 'warehouses.id = purchase_orders.warehouse_id', 'left')
                   ->find($id);

        if (!$po) {
            return redirect()->to('/purchase-orders')->with('error', 'Purchase Order not found');
        }

        $items = $this->poItemModel
                      ->select('purchase_order_items.*, products.name as product_name')
                      ->join('products', 'products.id = purchase_order_items.product_id', 'left')
                      ->where('po_id', $id)
                      ->findAll();

        // Check if an invoice was already generated for this PO
        $purchaseModel = new \App\Models\PurchaseModel();
        $linkedInvoice = $purchaseModel
            ->like('notes', $po->po_no)
            ->first();

        $data = [
            'pageTitle'     => 'View Purchase Order',
            'po'            => $po,
            'items'         => $items,
            'linkedInvoice' => $linkedInvoice
        ];

        return view('purchase_orders/view', $data);
    }

    public function updateStatus($id)
    {
        $status = strtolower(trim($this->request->getPost('status') ?? ''));

        // If admin approves the Purchase Order, automatically convert it to Purchase Invoice
        if ($status === 'approved') {
            return $this->convertToInvoice($id);
        }

        $this->poModel->update($id, ['status' => $status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function convertToInvoice($id)
    {
        $po = $this->poModel->find($id);
        if (!$po) {
            return redirect()->to('/purchase-orders')->with('error', 'Purchase Order not found.');
        }

        $purchaseModel = new \App\Models\PurchaseModel();

        // Check if an invoice was already created for this PO
        $existingInvoice = $purchaseModel
            ->like('notes', $po->po_no)
            ->first();

        if ($existingInvoice) {
            if ($po->status !== 'approved') {
                $this->poModel->update($id, ['status' => 'approved']);
            }
            return redirect()->to('/purchases/view/' . $existingInvoice->id)
                             ->with('info', "Purchase Order #{$po->po_no} is already converted to Purchase Invoice #{$existingInvoice->invoice_no}.");
        }

        $this->db->transStart();

        $invoiceNo = 'PINV-' . strtoupper(uniqid());
        $purchaseItemModel = new \App\Models\PurchaseItemModel();
        $stockModel = new \App\Models\StockModel();
        $auditModel = new \App\Models\AuditLogModel();

        $purchaseData = [
            'invoice_no'       => $invoiceNo,
            'vendor_id'        => $po->vendor_id,
            'warehouse_id'     => $po->warehouse_id,
            'subtotal'         => $po->subtotal,
            'tax_amount'       => $po->tax_amount ?? 0,
            'discount_percent' => $po->discount_percent ?? 0,
            'discount'         => $po->discount ?? 0,
            'total_amount'     => $po->total_amount,
            'paid_amount'      => 0,
            'payment_status'   => 'unpaid',
            'purchase_date'    => date('Y-m-d H:i:s'),
            'notes'            => "Generated automatically from approved Purchase Order #" . $po->po_no . (!empty($po->notes) ? (" | " . $po->notes) : ""),
            'created_by'       => session()->get('user_id')
        ];

        $purchaseId = $purchaseModel->insert($purchaseData);

        // Fetch PO items
        $poItems = $this->poItemModel->where('po_id', $id)->findAll();
        $items = [];
        foreach ($poItems as $item) {
            $items[] = [
                'purchase_id' => $purchaseId,
                'product_id'  => $item->product_id,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'subtotal'    => $item->subtotal,
                'tax_amount'  => $item->tax_amount ?? 0,
                'total'       => $item->total
            ];

            // Increase stock since the approved order is now received as a purchase invoice
            $stockModel->updateStock($item->product_id, $po->warehouse_id, $item->quantity);
        }

        if (!empty($items)) {
            $purchaseItemModel->insertBatch($items);
        }

        // Update PO status to approved and record conversion in notes
        $this->poModel->update($id, [
            'status' => 'approved',
            'notes'  => trim(($po->notes ?? '') . " [Converted to Purchase Invoice {$invoiceNo}]")
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to convert Purchase Order to Purchase Invoice.');
        }

        $auditModel->logAction('convert_to_invoice', 'purchase_orders', $id, (array)$po, [
            'invoice_id' => $purchaseId,
            'invoice_no' => $invoiceNo
        ]);

        return redirect()->to('/purchases/view/' . $purchaseId)
                         ->with('success', "Purchase Order #{$po->po_no} approved and automatically converted into Purchase Invoice #{$invoiceNo}! Stock has been added to warehouse.");
    }

    public function delete($id)
    {
        $this->poModel->delete($id);
        return redirect()->to('/purchase-orders')->with('success', 'Purchase Order deleted successfully.');
    }
}
