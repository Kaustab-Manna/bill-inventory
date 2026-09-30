<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\WarehouseModel;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\StockModel;

class PosController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $productModel = new ProductModel();
        $customerModel = new CustomerModel();
        $warehouseModel = new WarehouseModel();
        $userModel = new \App\Models\UserModel();

        $warehouses = $warehouseModel->where('is_active', 1)->findAll();
        $selectedWarehouseId = $this->request->getGet('warehouse_id');
        
        $selectedWarehouse = null;
        if ($selectedWarehouseId) {
            $selectedWarehouse = $warehouseModel->find($selectedWarehouseId);
        }
        if (!$selectedWarehouse) {
            $selectedWarehouse = $warehouseModel->where('is_default', 1)->first() ?? ($warehouses[0] ?? null);
        }

        $warehouseId = $selectedWarehouse ? (int)$selectedWarehouse->id : 0;

        $products = $productModel->select('products.*, c.name as category, u.short_name as unit, COALESCE(s.quantity, 0) as stock_qty, COALESCE(s.reserved_qty, 0) as reserved_qty')
                                   ->join('categories c', 'c.id = products.category_id', 'left')
                                   ->join('units u', 'u.id = products.unit_id', 'left')
                                   ->join('stock s', 's.product_id = products.id AND s.warehouse_id = ' . $warehouseId, 'left')
                                   ->where('products.is_active', 1)
                                   ->findAll();

        $data = [
            'pageTitle'    => 'POS terminal',
            'warehouse'    => $selectedWarehouse,
            'warehouses'   => $warehouses,
            'customers'    => $customerModel->where('is_active', 1)->findAll(),
            'salespersons' => \Config\Database::connect()->fieldExists('commission_rate', 'users') 
                                ? $userModel->where('is_active', 1)->where('commission_rate >', 0)->findAll() 
                                : $userModel->where('is_active', 1)->findAll(),
            'products'     => $products
        ];
        return view('pos/index', $data);
    }

    public function searchProducts()
    {
        $query = $this->request->getGet('q');
        $warehouseId = (int)$this->request->getGet('warehouse_id');
        $productModel = new ProductModel();
        
        $builder = $productModel->select('products.*, c.name as category, u.short_name as unit, COALESCE(s.quantity, 0) as stock_qty, COALESCE(s.reserved_qty, 0) as reserved_qty')
                                ->join('categories c', 'c.id = products.category_id', 'left')
                                ->join('units u', 'u.id = products.unit_id', 'left')
                                ->join('stock s', 's.product_id = products.id AND s.warehouse_id = ' . $warehouseId, 'left')
                                ->where('products.is_active', 1);

        if (!empty($query)) {
            $builder->groupStart()
                    ->like('products.name', $query)
                    ->orLike('products.sku', $query)
                    ->orLike('products.barcode', $query)
                    ->groupEnd();
        }

        return $this->response->setJSON($builder->findAll());
    }

    public function checkout()
    {
        $post = $this->request->getPost();
        
        $saleModel = new SaleModel();
        $saleItemModel = new SaleItemModel();
        $stockModel = new StockModel();
        $productModel = new ProductModel();

        if (empty($post['cart'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cart is empty.']);
        }

        if (empty($post['warehouse_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please select a warehouse.']);
        }

        $warehouseId = (int)$post['warehouse_id'];

        $cart = json_decode($post['cart'], true);
        if (!$cart || !is_array($cart)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid cart data.']);
        }

        // Strict Stock Check BEFORE starting transaction
        foreach ($cart as $item) {
            $requestedQty = (float)($item['qty'] ?? 0);
            $productId = (int)$item['id'];
            $product = $productModel->find($productId);
            $productName = $product ? $product->name : ('Product #' . $productId);

            if ($requestedQty <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot complete sale: Quantity for '{$productName}' must be greater than zero."
                ]);
            }

            $availableStock = $stockModel->getAvailableStock($productId, $warehouseId);

            if ($availableStock <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot complete sale: '{$productName}' is OUT OF STOCK in this warehouse."
                ]);
            }

            if ($requestedQty > $availableStock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot complete sale: Insufficient stock for '{$productName}'. Available: {$availableStock}, Requested: {$requestedQty}."
                ]);
            }
        }

        $this->db->transStart();

        $subtotal = max(0, (float)$post['subtotal']);
        $discount = max(0, (float)($post['discount'] ?? 0));
        $totalAmount = max(0, (float)$post['total_amount']);
        $paidAmount = max(0, (float)($post['paid_amount'] ?? 0));

        // Calculate Commission
        $commissionAmount = 0;
        $salespersonId = !empty($post['salesperson_id']) ? $post['salesperson_id'] : null;
        if ($salespersonId) {
            $userModel = new \App\Models\UserModel();
            $salesperson = $userModel->find($salespersonId);
            if ($salesperson && $salesperson->commission_rate > 0) {
                $commissionAmount = ($subtotal * $salesperson->commission_rate) / 100;
            }
        }

        $userId = session()->get('user_id') ?? 1;
        $saleData = [
            'invoice_no'     => 'INV-' . strtoupper(uniqid()),
            'customer_id'    => !empty($post['customer_id']) ? $post['customer_id'] : null,
            'warehouse_id'   => $warehouseId,
            'subtotal'       => $subtotal,
            'tax_amount'     => max(0, (float)($post['tax_amount'] ?? 0)),
            'discount'       => $discount,
            'total_amount'   => $totalAmount,
            'paid_amount'    => $paidAmount,
            'payment_method' => $post['payment_method'] ?? 'cash',
            'status'         => ($paidAmount >= $totalAmount) ? 'paid' : (($paidAmount > 0) ? 'partial' : 'unpaid'),
            'sale_date'      => date('Y-m-d H:i:s'),
            'created_by'     => $userId
        ];

        if ($this->db->fieldExists('salesperson_id', 'sales')) {
            $saleData['salesperson_id'] = $salespersonId;
        }
        if ($this->db->fieldExists('commission_amount', 'sales')) {
            $saleData['commission_amount'] = $commissionAmount;
        }

        $saleId = $saleModel->insert($saleData);

        $items = [];
        foreach ($cart as $item) {
            $qty = (float)$item['qty'];
            $price = (float)$item['price'];
            $productId = (int)$item['id'];

            $items[] = [
                'sale_id' => $saleId,
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];

            // Decrement stock safely (allowNegative = false)
            $deducted = $stockModel->updateStock($productId, $warehouseId, -$qty, false);
            if (!$deducted) {
                $this->db->transRollback();
                $product = $productModel->find($productId);
                $pName = $product ? $product->name : ('Product #' . $productId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Insufficient stock to complete deduction for '{$pName}'."
                ]);
            }
        }

        $saleItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Checkout failed. Please try again.']);
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Sale completed successfully!',
            'sale_id' => $saleId,
            'invoice_no' => $saleData['invoice_no']
        ]);
    }

    public function addCustomer()
    {
        $rules = [
            'name'  => 'required|min_length[3]|max_length[150]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false,
                'message' => implode(' ', $errors),
                'errors'  => $errors
            ]);
        }

        $customerModel = new CustomerModel();
        
        $data = [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address')
        ];

        $id = $customerModel->insert($data);

        if ($id) {
            return $this->response->setJSON(['success' => true, 'customer' => ['id' => $id, 'name' => $data['name']]]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to add customer.']);
    }
}
