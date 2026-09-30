<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\CustomerModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class SalesController extends BaseController
{
    protected $db;
    protected $saleModel;
    protected $saleItemModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
    }

    public function index()
    {
        $statusFilter = strtolower(trim($this->request->getGet('status') ?? ''));

        // Base query for invoices
        $builder = $this->saleModel
                        ->select('sales.*, customers.name as customer_name')
                        ->join('customers', 'customers.id = sales.customer_id', 'left')
                        ->orderBy('sales.id', 'DESC');

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            if ($statusFilter === 'pending' || $statusFilter === 'unpaid') {
                $builder->whereIn('sales.status', ['unpaid', 'pending']);
            } elseif ($statusFilter === 'paid') {
                $builder->where('sales.status', 'paid');
            } elseif ($statusFilter === 'partial') {
                $builder->where('sales.status', 'partial');
            }
        }

        $sales = $builder->findAll();

        // Compute live metrics across all sales for the filter cards/tabs
        $allSales = $this->saleModel->select('status, total_amount, paid_amount')->findAll();
        $counts = [
            'all'              => count($allSales),
            'paid'             => 0,
            'partial'          => 0,
            'pending'          => 0,
            'total_revenue'    => 0,
            'total_receivable' => 0
        ];

        foreach ($allSales as $s) {
            $st = strtolower($s->status ?? '');
            $total = (float)($s->total_amount ?? 0);
            $paid = (float)($s->paid_amount ?? 0);
            $due = max(0, $total - $paid);

            $counts['total_revenue'] += $paid;

            if ($st === 'paid') {
                $counts['paid']++;
            } elseif ($st === 'partial') {
                $counts['partial']++;
                $counts['total_receivable'] += $due;
            } else { // unpaid or pending
                $counts['pending']++;
                $counts['total_receivable'] += $due;
            }
        }

        $data = [
            'pageTitle'     => 'Sales Invoices',
            'sales'         => $sales,
            'currentFilter' => $statusFilter ?: 'all',
            'counts'        => $counts
        ];

        return view('sales/index', $data);
    }

    public function create()
    {
        $customerModel  = new CustomerModel();
        $warehouseModel = new WarehouseModel();
        $productModel   = new ProductModel();
        $stockModel     = new StockModel();

        $allStock = $stockModel->findAll();
        $stockMap = [];
        foreach ($allStock as $stk) {
            $avail = max(0, (float)$stk->quantity - (float)($stk->reserved_qty ?? 0));
            $stockMap[$stk->warehouse_id][$stk->product_id] = $avail;
        }

        $products = $productModel
            ->select('products.*, t.name as tax_name, COALESCE(t.rate, 0) as tax_rate, COALESCE(t.type, "exclusive") as tax_type')
            ->join('taxes t', 't.id = products.tax_id', 'left')
            ->where('products.is_active', 1)
            ->findAll();

        $data = [
            'pageTitle'  => 'Create Sale Invoice',
            'customers'  => $customerModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products'   => $products,
            'stockMap'   => json_encode($stockMap)
        ];
        return view('sales/create', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['product_id'])) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product.');
        }

        if (empty($post['warehouse_id'])) {
            return redirect()->back()->withInput()->with('error', 'Warehouse is required.');
        }

        $warehouseId = (int)$post['warehouse_id'];
        $stockModel = new StockModel();
        $productModel = new ProductModel();

        // 1. Strict Stock, Quantity, and Tax Calculation before database operations
        $itemsData = [];
        $calculatedTaxTotal = 0;
        $calculatedSubtotal = 0;

        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = (float)$post['quantity'][$i];
            $price = (float)($post['unit_price'][$i] ?? 0);
            $productId = (int)$post['product_id'][$i];
            
            $product = $productModel->select('products.*, t.rate as tax_rate, t.type as tax_type, t.name as tax_name')
                                    ->join('taxes t', 't.id = products.tax_id', 'left')
                                    ->find($productId);
            $productName = $product ? $product->name : ('Product #' . $productId);

            if ($qty <= 0) {
                return redirect()->back()->withInput()->with('error', "Cannot complete sale: Quantity for '{$productName}' must be greater than zero.");
            }

            if ($price < 0) {
                return redirect()->back()->withInput()->with('error', "Cannot complete sale: Unit price for '{$productName}' cannot be negative.");
            }

            $availableStock = $stockModel->getAvailableStock($productId, $warehouseId);

            if ($availableStock <= 0) {
                return redirect()->back()->withInput()->with('error', "Cannot complete sale: '{$productName}' is OUT OF STOCK in the selected warehouse!");
            }

            if ($qty > $availableStock) {
                return redirect()->back()->withInput()->with('error', "Cannot complete sale: Insufficient stock for '{$productName}'. Available: {$availableStock}, Requested: {$qty}.");
            }

            // Calculate item tax
            $itemSubtotal = $price * $qty;
            $taxRate = (float)($product->tax_rate ?? 0);
            $taxType = strtolower($product->tax_type ?? 'exclusive');

            $itemTax = 0;
            $itemTotal = $itemSubtotal;
            if ($taxRate > 0) {
                if ($taxType === 'inclusive') {
                    $itemTax = $itemSubtotal - ($itemSubtotal / (1 + ($taxRate / 100)));
                    $itemTotal = $itemSubtotal;
                } else {
                    $itemTax = ($itemSubtotal * $taxRate) / 100;
                    $itemTotal = $itemSubtotal + $itemTax;
                }
            }

            $calculatedTaxTotal += $itemTax;
            $calculatedSubtotal += $itemSubtotal;

            $itemsData[] = [
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => round($itemSubtotal, 2),
                'tax_amount' => round($itemTax, 2),
                'total' => round($itemTotal, 2)
            ];
        }

        $this->db->transStart();

        $subtotal = max(0, (float)($post['subtotal'] ?? $calculatedSubtotal));
        if ($subtotal <= 0) {
            $subtotal = $calculatedSubtotal;
        }

        $taxAmount = isset($post['tax_amount']) && (float)$post['tax_amount'] > 0 
            ? (float)$post['tax_amount'] 
            : $calculatedTaxTotal;

        $discountPercent = max(0, min(100, (float)($post['discount_percent'] ?? 0)));
        $discount = max(0, (float)($post['discount'] ?? 0));
        
        $totalAmount = (float)($post['total_amount'] ?? 0);
        if ($totalAmount <= 0) {
            $totalAmount = max(0, $subtotal + $taxAmount - $discount);
        }

        $paidAmount = max(0, (float)($post['paid_amount'] ?? 0));
        $status = ($paidAmount >= $totalAmount && $totalAmount > 0) ? 'paid' : (($paidAmount > 0) ? 'partial' : 'unpaid');

        $saleData = [
            'invoice_no' => 'INV-' . strtoupper(uniqid()),
            'customer_id' => $post['customer_id'] ?: null,
            'warehouse_id' => $warehouseId,
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_percent' => $discountPercent,
            'discount' => round($discount, 2),
            'total_amount' => round($totalAmount, 2),
            'paid_amount' => round($paidAmount, 2),
            'payment_method' => $post['payment_method'] ?? 'cash',
            'status' => $status,
            'sale_date' => $post['sale_date'],
            'created_by' => session()->get('user_id')
        ];

        $saleId = $this->saleModel->insert($saleData);

        $items = [];
        foreach ($itemsData as $item) {
            $item['sale_id'] = $saleId;
            $items[] = $item;

            // Deduct stock safely (prevent negative stock)
            $deducted = $stockModel->updateStock($item['product_id'], $warehouseId, -$item['quantity'], false);
            if (!$deducted) {
                $this->db->transRollback();
                $product = $productModel->find($item['product_id']);
                $pName = $product ? $product->name : ('Product #' . $item['product_id']);
                return redirect()->back()->withInput()->with('error', "Stock deduction failed for '{$pName}'. Insufficient stock.");
            }
        }

        $this->saleItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create sale. Please try again.');
        }

        return redirect()->to('/sales')->with('success', 'Sale Invoice created successfully.');
    }

    public function view($id)
    {
        $sale = $this->saleModel
                     ->select('sales.*, 
                               customers.name as customer_name, 
                               customers.email as customer_email, 
                               customers.phone as customer_phone, 
                               customers.address as customer_address,
                               customers.email,
                               customers.phone,
                               customers.address,
                               warehouses.name as warehouse_name, 
                               users.name as salesperson_name')
                     ->join('customers', 'customers.id = sales.customer_id', 'left')
                     ->join('warehouses', 'warehouses.id = sales.warehouse_id', 'left')
                     ->join('users', 'users.id = sales.salesperson_id', 'left')
                     ->find($id);

        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale not found');
        }

        $items = $this->saleItemModel
                      ->select('sale_items.*, products.name as product_name')
                      ->join('products', 'products.id = sale_items.product_id', 'left')
                      ->where('sale_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Sale Invoice',
            'sale' => $sale,
            'items' => $items
        ];

        return view('sales/view', $data);
    }

    public function delete($id)
    {
        $sale = $this->saleModel->find($id);
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale not found');
        }

        $items = $this->saleItemModel->where('sale_id', $id)->findAll();
        $stockModel = new StockModel();

        $this->db->transStart();

        // Restore stock
        foreach ($items as $item) {
            $stockModel->updateStock($item->product_id, $sale->warehouse_id, $item->quantity);
        }

        $this->saleItemModel->where('sale_id', $id)->delete();
        $this->saleModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/sales')->with('error', 'Failed to delete sale.');
        }

        return redirect()->to('/sales')->with('success', 'Sale Invoice deleted and stock restored successfully.');
    }

    public function updatePayment($id)
    {
        $sale = $this->saleModel->find($id);
        if (!$sale) {
            return redirect()->back()->with('error', 'Sale not found.');
        }

        $paidAmount = $this->request->getPost('paid_amount');
        $status = ($paidAmount >= $sale->total_amount) ? 'paid' : (($paidAmount > 0) ? 'partial' : 'unpaid');

        $this->saleModel->update($id, [
            'paid_amount' => $paidAmount,
            'status' => $status
        ]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function sendInvoice($id)
    {
        $sale = $this->saleModel
                     ->select('sales.*, customers.name as customer_name, customers.email, customers.phone')
                     ->join('customers', 'customers.id = sales.customer_id', 'left')
                     ->find($id);

        if (!$sale) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sale not found.']);
        }

        if (!$sale->customer_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'No customer associated with this invoice (Walk-in). Cannot send.']);
        }

        $type = $this->request->getPost('type'); // email, sms, whatsapp
        $messagingService = new \App\Services\MessagingService();
        
        $invoiceLink = base_url("sales/view/{$id}");
        $message = "Dear {$sale->customer_name}, your invoice {$sale->invoice_no} for Rs. {$sale->total_amount} has been generated. ";

        if ($type == 'email') {
            if (!$sale->email) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer has no email address.']);
            }
            $message .= "\nYou can view it here: " . $invoiceLink;
            $messagingService->sendEmail($sale->email, "Invoice {$sale->invoice_no} from BillInventory", $message);
        } elseif ($type == 'sms') {
            if (!$sale->phone) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer has no phone number.']);
            }
            $message .= "View: " . $invoiceLink;
            $messagingService->sendSMS($sale->phone, $message);
        } elseif ($type == 'whatsapp') {
            if (!$sale->phone) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer has no phone number.']);
            }
            $messagingService->sendWhatsApp($sale->phone, $message, $invoiceLink);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid messaging type.']);
        }

        // Log this action
        $auditModel = new \App\Models\AuditLogModel();
        $auditModel->logAction('update', 'sales_messaging', $id, null, ['sent_via' => $type, 'recipient' => $sale->customer_name]);

        return $this->response->setJSON(['success' => true, 'message' => "Invoice sent successfully via " . ucfirst($type)]);
    }
}
