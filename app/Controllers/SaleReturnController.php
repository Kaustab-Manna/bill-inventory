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
                              ->select('sale_returns.*, customers.name as customer_name, sales.invoice_no as original_invoice_no')
                              ->join('customers', 'customers.id = sale_returns.customer_id', 'left')
                              ->join('sales', 'sales.id = sale_returns.sale_id', 'left')
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

        // Products with tax details
        $products = $productModel
            ->select('products.*, taxes.rate as tax_rate, taxes.type as tax_type, taxes.name as tax_name')
            ->join('taxes', 'taxes.id = products.tax_id', 'left')
            ->where('products.is_active', 1)
            ->findAll();

        // Recent sales for quick invoice autocomplete/suggestion
        $recentSales = $this->db->table('sales')
            ->select('sales.id, sales.invoice_no, sales.total_amount, sales.sale_date, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.deleted_at', null)
            ->orderBy('sales.id', 'DESC')
            ->limit(30)
            ->get()
            ->getResult();

        $data = [
            'pageTitle'    => 'Create Sale Return',
            'customers'    => $customerModel->where('is_active', 1)->findAll(),
            'warehouses'   => $warehouseModel->where('is_active', 1)->findAll(),
            'products'     => $products,
            'recentSales'  => $recentSales
        ];
        return view('sale_returns/create', $data);
    }

    public function fetchInvoice()
    {
        $invoiceNo = trim($this->request->getGet('invoice_no') ?? '');
        if (empty($invoiceNo)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please provide an invoice number.'
            ]);
        }

        $sale = $this->db->table('sales')
            ->select('sales.*, customers.name as customer_name, customers.phone as customer_phone, customers.email as customer_email, warehouses.name as warehouse_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->join('warehouses', 'warehouses.id = sales.warehouse_id', 'left')
            ->where('sales.invoice_no', $invoiceNo)
            ->where('sales.deleted_at', null)
            ->get()
            ->getRow();

        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Invoice '{$invoiceNo}' not found. Please verify the invoice number."
            ]);
        }

        $items = $this->db->table('sale_items')
            ->select('sale_items.*, products.name as product_name, products.sku, products.barcode, taxes.rate as tax_rate, taxes.type as tax_type, taxes.name as tax_name')
            ->join('products', 'products.id = sale_items.product_id', 'left')
            ->join('taxes', 'taxes.id = products.tax_id', 'left')
            ->where('sale_items.sale_id', $sale->id)
            ->get()
            ->getResult();

        if (empty($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "No items found for invoice '{$invoiceNo}'."
            ]);
        }

        $processedItems = [];
        foreach ($items as $item) {
            // Check quantity already returned in previous completed returns for this sale
            $alreadyReturned = $this->db->table('sale_return_items')
                ->join('sale_returns', 'sale_returns.id = sale_return_items.sale_return_id')
                ->where('sale_returns.sale_id', $sale->id)
                ->where('sale_return_items.product_id', $item->product_id)
                ->where('sale_returns.deleted_at', null)
                ->selectSum('sale_return_items.quantity', 'total_returned')
                ->get()
                ->getRow()->total_returned ?? 0;

            $soldQty = (float)$item->quantity;
            $alreadyReturnedQty = (float)$alreadyReturned;
            $returnableQty = max(0, $soldQty - $alreadyReturnedQty);

            // Determine tax rate and type
            $taxRate = (float)($item->tax_rate ?? 0);
            $taxType = strtolower($item->tax_type ?? 'exclusive');

            // Fallback: If product tax rate isn't in tax table, compute effective tax from recorded item tax
            if ($taxRate <= 0 && (float)$item->tax_amount > 0 && (float)$item->subtotal > 0) {
                $taxRate = round(((float)$item->tax_amount / (float)$item->subtotal) * 100, 2);
            }

            $unitPrice = (float)$item->unit_price;

            $processedItems[] = [
                'product_id'          => (int)$item->product_id,
                'product_name'        => $item->product_name ?? ('Product #' . $item->product_id),
                'sku'                 => $item->sku ?? '',
                'barcode'             => $item->barcode ?? '',
                'sold_quantity'       => $soldQty,
                'already_returned'    => $alreadyReturnedQty,
                'returnable_quantity' => $returnableQty,
                'unit_price'          => $unitPrice,
                'tax_rate'            => $taxRate,
                'tax_type'            => $taxType,
                'tax_name'            => $item->tax_name ?? ($taxRate > 0 ? "GST {$taxRate}%" : 'None')
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'sale' => [
                'id'             => (int)$sale->id,
                'invoice_no'     => $sale->invoice_no,
                'customer_id'    => $sale->customer_id ? (int)$sale->customer_id : '',
                'customer_name'  => $sale->customer_name ?? 'Walk-in Customer',
                'customer_phone' => $sale->customer_phone ?? '',
                'customer_email' => $sale->customer_email ?? '',
                'warehouse_id'   => (int)$sale->warehouse_id,
                'warehouse_name' => $sale->warehouse_name ?? 'Main Warehouse',
                'sale_date'      => $sale->sale_date,
                'subtotal'       => (float)$sale->subtotal,
                'tax_amount'     => (float)$sale->tax_amount,
                'total_amount'   => (float)$sale->total_amount
            ],
            'items' => $processedItems
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['product_id']) || !is_array($post['product_id'])) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one product to return.');
        }

        if (empty($post['warehouse_id'])) {
            return redirect()->back()->withInput()->with('error', 'Warehouse is required.');
        }

        $saleId = !empty($post['sale_id']) ? (int)$post['sale_id'] : null;
        $productModel = new ProductModel();

        $calculatedSubtotal = 0;
        $calculatedTax = 0;
        $calculatedTotal = 0;
        $validItems = [];

        for ($i = 0; $i < count($post['product_id']); $i++) {
            $productId = (int)$post['product_id'][$i];
            $qty = (float)($post['quantity'][$i] ?? 0);
            $price = (float)($post['unit_price'][$i] ?? 0);
            $condition = $post['item_condition'][$i] ?? 'sealed';
            $taxRate = max(0, (float)($post['tax_rate'][$i] ?? 0));
            $taxType = strtolower($post['tax_type'][$i] ?? 'exclusive');

            $product = $productModel->find($productId);
            $productName = $product ? $product->name : ('Product #' . $productId);

            if ($qty <= 0) {
                return redirect()->back()->withInput()->with('error', "Return quantity for '{$productName}' must be greater than zero.");
            }
            if ($price < 0) {
                return redirect()->back()->withInput()->with('error', "Unit price for '{$productName}' cannot be negative.");
            }

            // If linked to original sale invoice, validate against returnable quantity
            if ($saleId) {
                $soldItem = $this->db->table('sale_items')
                    ->where('sale_id', $saleId)
                    ->where('product_id', $productId)
                    ->get()
                    ->getRow();

                if ($soldItem) {
                    $alreadyReturned = $this->db->table('sale_return_items')
                        ->join('sale_returns', 'sale_returns.id = sale_return_items.sale_return_id')
                        ->where('sale_returns.sale_id', $saleId)
                        ->where('sale_return_items.product_id', $productId)
                        ->where('sale_returns.deleted_at', null)
                        ->selectSum('sale_return_items.quantity', 'total_returned')
                        ->get()
                        ->getRow()->total_returned ?? 0;

                    $maxReturnable = max(0, (float)$soldItem->quantity - (float)$alreadyReturned);
                    if ($qty > $maxReturnable + 0.0001) {
                        return redirect()->back()->withInput()->with('error', "Return quantity ({$qty}) for '{$productName}' exceeds returnable quantity ({$maxReturnable}) on this invoice.");
                    }
                }
            }

            // Precise tax and line total calculations
            $lineSubtotal = round($qty * $price, 2);
            $lineTax = 0;
            if ($taxRate > 0) {
                if ($taxType === 'inclusive') {
                    $lineTax = round($lineSubtotal - ($lineSubtotal / (1 + ($taxRate / 100))), 2);
                    $lineTotal = $lineSubtotal;
                } else {
                    $lineTax = round(($lineSubtotal * $taxRate) / 100, 2);
                    $lineTotal = round($lineSubtotal + $lineTax, 2);
                }
            } else {
                $lineTotal = $lineSubtotal;
            }

            $calculatedSubtotal += $lineSubtotal;
            $calculatedTax += $lineTax;
            $calculatedTotal += $lineTotal;

            $inspectionStatus = ($condition == 'broken_seal') ? 'pending' : 'na';

            $validItems[] = [
                'product_id'        => $productId,
                'item_condition'    => $condition,
                'inspection_status' => $inspectionStatus,
                'quantity'          => $qty,
                'unit_price'        => $price,
                'subtotal'          => $lineSubtotal,
                'tax_amount'        => $lineTax,
                'total'             => $lineTotal
            ];
        }

        if (empty($validItems)) {
            return redirect()->back()->withInput()->with('error', 'No valid items to return.');
        }

        $this->db->transStart();
        $stockModel = new StockModel();

        $returnData = [
            'return_no'    => 'RET-' . strtoupper(uniqid()),
            'sale_id'      => $saleId,
            'customer_id'  => !empty($post['customer_id']) ? (int)$post['customer_id'] : null,
            'warehouse_id' => (int)$post['warehouse_id'],
            'subtotal'     => round($calculatedSubtotal, 2),
            'tax_amount'   => round($calculatedTax, 2),
            'total_amount' => round($calculatedTotal, 2),
            'status'       => 'completed',
            'return_date'  => $post['return_date'] ?? date('Y-m-d H:i:s'),
            'notes'        => $post['notes'] ?? '',
            'created_by'   => session()->get('user_id')
        ];

        $returnId = $this->saleReturnModel->insert($returnData);

        foreach ($validItems as &$item) {
            $item['sale_return_id'] = $returnId;

            // Increase stock ONLY if seal is intact (otherwise it waits for inspection)
            if ($item['item_condition'] == 'sealed') {
                $stockModel->updateStock($item['product_id'], (int)$post['warehouse_id'], $item['quantity']);
            }
        }

        $this->saleReturnItemModel->insertBatch($validItems);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to process return. Please try again.');
        }

        return redirect()->to('/sales-returns/view/' . $returnId)->with('success', 'Sale Return processed successfully with tax inclusion.');
    }

    public function view($id)
    {
        $return = $this->saleReturnModel
                       ->select('sale_returns.*, customers.name as customer_name, customers.email, customers.phone, customers.address, warehouses.name as warehouse_name, sales.invoice_no as original_invoice_no')
                       ->join('customers', 'customers.id = sale_returns.customer_id', 'left')
                       ->join('warehouses', 'warehouses.id = sale_returns.warehouse_id', 'left')
                       ->join('sales', 'sales.id = sale_returns.sale_id', 'left')
                       ->find($id);

        if (!$return) {
            return redirect()->to('/sales-returns')->with('error', 'Return not found');
        }

        $items = $this->saleReturnItemModel
                      ->select('sale_return_items.*, products.name as product_name, products.sku, products.barcode')
                      ->join('products', 'products.id = sale_return_items.product_id', 'left')
                      ->where('sale_return_id', $id)
                      ->findAll();

        $data = [
            'pageTitle' => 'View Sale Return',
            'return'    => $return,
            'items'     => $items
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
