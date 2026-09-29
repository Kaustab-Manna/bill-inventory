<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QuotationModel;
use App\Models\QuotationItemModel;
use App\Models\CustomerModel;
use App\Models\WarehouseModel;
use App\Models\ProductModel;

class QuotationController extends BaseController
{
    protected $db;
    protected $quotationModel;
    protected $quotationItemModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->quotationModel = new QuotationModel();
        $this->quotationItemModel = new QuotationItemModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Quotations',
            'quotations' => $this->quotationModel
                                 ->select('quotations.*, customers.name as customer_name')
                                 ->join('customers', 'customers.id = quotations.customer_id', 'left')
                                 ->orderBy('quotations.id', 'DESC')
                                 ->findAll()
        ];
        return view('quotations/index', $data);
    }

    public function create()
    {
        $customerModel = new CustomerModel();
        $warehouseModel = new WarehouseModel();
        $productModel = new ProductModel();

        $data = [
            'pageTitle' => 'Create Quotation',
            'customers' => $customerModel->where('is_active', 1)->findAll(),
            'warehouses' => $warehouseModel->where('is_active', 1)->findAll(),
            'products' => $productModel->where('is_active', 1)->findAll()
        ];
        return view('quotations/create', $data);
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

        $quotationData = [
            'quotation_no' => 'QT-' . strtoupper(uniqid()),
            'customer_id' => $post['customer_id'] ?: null,
            'warehouse_id' => $post['warehouse_id'] ?: null,
            'subtotal' => $subtotal,
            'tax_amount' => 0, // simplified
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'quotation_date' => $post['quotation_date'],
            'expiry_date' => $post['expiry_date'] ?: null,
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        $quotationId = $this->quotationModel->insert($quotationData);

        $items = [];
        for ($i = 0; $i < count($post['product_id']); $i++) {
            $qty = $post['quantity'][$i];
            $price = $post['unit_price'][$i];
            $items[] = [
                'quotation_id' => $quotationId,
                'product_id' => $post['product_id'][$i],
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $qty * $price,
                'tax_amount' => 0,
                'total' => $qty * $price
            ];
        }

        $this->quotationItemModel->insertBatch($items);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create quotation.');
        }

        return redirect()->to('/quotations')->with('success', 'Quotation created successfully.');
    }

    public function view($id)
    {
        $quotation = $this->quotationModel
                          ->select('quotations.*, customers.name as customer_name, customers.email, customers.phone, customers.address, warehouses.name as warehouse_name')
                          ->join('customers', 'customers.id = quotations.customer_id', 'left')
                          ->join('warehouses', 'warehouses.id = quotations.warehouse_id', 'left')
                          ->find($id);

        if (!$quotation) {
            return redirect()->to('/quotations')->with('error', 'Quotation not found');
        }

        $items = $this->quotationItemModel
                      ->select('quotation_items.*, products.name as product_name')
                      ->join('products', 'products.id = quotation_items.product_id', 'left')
                      ->where('quotation_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Quotation',
            'quotation' => $quotation,
            'items' => $items
        ];

        return view('quotations/view', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $this->quotationModel->update($id, ['status' => $status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function delete($id)
    {
        $this->quotationModel->delete($id);
        return redirect()->to('/quotations')->with('success', 'Quotation deleted successfully.');
    }
}
