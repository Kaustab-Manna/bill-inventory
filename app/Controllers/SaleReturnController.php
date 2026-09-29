<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleReturnModel;
use App\Models\SaleReturnItemModel;
use App\Models\CustomerModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;
use App\Models\StockModel;

class SaleReturnController extends BaseController
{
    protected $db;
    protected $saleReturnModel;
    protected $saleReturnItemModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->saleReturnModel = new SaleReturnModel();
        $this->saleReturnItemModel = new SaleReturnItemModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Sales Returns',
            'returns' => $this->saleReturnModel
                              ->select('sale_returns.*, customers.name as customer_name')
                              ->join('customers', 'customers.id = sale_returns.customer_id', 'left')
                              ->orderBy('sale_returns.id', 'DESC')
                              ->findAll()
        ];
        return view('sale_returns/index', $data);
    }

    public function create()
    {
        $customerModel = new CustomerModel();
        $warehouseModel = new WarehouseModel();
        $productModel = new ProductModel();

        $data = [
            'pageTitle' => 'Create Sale Return',
            'customers' => $customerModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll()
        ];
        return view('sale_returns/create', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['product_id'])) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to return.');
        }

        if (empty($post['warehouse_id'])) {
            return redirect()->back()->withInput()->with('error', 'Warehouse is required.');
        }

        $productModel = new ProductModel();
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = (float)($post['quantity'][$i] ?? 0);
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
        }

        $this->db->transStart();
        $stockModel = new StockModel();

        $subtotal = max(0, (float)$post['subtotal']);
        $totalAmount = max(0, (float)$post['total_amount']);

        $returnData = [
            'return_no' => 'RET-' . strtoupper(uniqid()),
            'customer_id' => $post['customer_id'] ?: null,
            'warehouse_id' => $post['warehouse_id'],
            'subtotal' => $subtotal,
            'tax_amount' => 0, 
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'return_date' => $post['return_date'],
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        $returnId = $this->saleReturnModel->insert($returnData);

        $items = [];
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = $post['quantity'][$i];
            $price = $post['unit_price'][$i];
            $productId = $post['product_id'][$i];
            $condition = $post['item_condition'][$i] ?? 'sealed';
            
            $inspectionStatus = ($condition == 'broken_seal') ? 'pending' : 'na';
            
            $items[] = [
                'sale_return_id' => $returnId,
                'product_id' => $productId,
                'item_condition' => $condition,
                'inspection_status' => $inspectionStatus,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];

            // Increase stock ONLY if seal is intact (otherwise it waits for inspection)
            if ($condition == 'sealed') {
                $stockModel->updateStock($productId, $post['warehouse_id'], $qty);
            }
        }

        $this->saleReturnItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to process return. Please try again.');
        }

        return redirect()->to('/sales-returns')->with('success', 'Sale Return processed successfully.');
    }

    public function view($id)
    {
        $return = $this->saleReturnModel
                       ->select('sale_returns.*, customers.name as customer_name, customers.email, customers.phone, customers.address, warehouses.name as warehouse_name')
                       ->join('customers', 'customers.id = sale_returns.customer_id', 'left')
                       ->join('warehouses', 'warehouses.id = sale_returns.warehouse_id', 'left')
                       ->find($id);

        if (!$return) {
            return redirect()->to('/sales-returns')->with('error', 'Return not found');
        }

        $items = $this->saleReturnItemModel
                      ->select('sale_return_items.*, products.name as product_name')
                      ->join('products', 'products.id = sale_return_items.product_id', 'left')
                      ->where('sale_return_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Sale Return',
            'return' => $return,
            'items' => $items
        ];

        return view('sale_returns/view', $data);
    }

    public function delete($id)
    {
        $return = $this->saleReturnModel->find($id);
        if (!$return) {
            return redirect()->to('/sales-returns')->with('error', 'Return not found');
        }

        $items = $this->saleReturnItemModel->where('sale_return_id', $id)->findAll();
        $stockModel = new StockModel();

        $this->db->transStart();

        // Revert stock (deduct the items that were previously added back)
        foreach ($items as $item) {
            if ($item->item_condition == 'sealed' || $item->inspection_status == 'passed') {
                $stockModel->updateStock($item->product_id, $return->warehouse_id, -$item->quantity);
            }
        }

        $this->saleReturnItemModel->where('sale_return_id', $id)->delete();
        $this->saleReturnModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/sales-returns')->with('error', 'Failed to delete return.');
        }

        return redirect()->to('/sales-returns')->with('success', 'Sale Return deleted and stock reverted successfully.');
    }
}
