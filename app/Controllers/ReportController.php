<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\PurchaseModel;
use App\Models\ExpenseModel;
use App\Models\StockModel;
use App\Models\ProductModel;

class ReportController extends BaseController
{
    public function index()
    {
        $saleModel = new SaleModel();
        $purchaseModel = new PurchaseModel();
        $expenseModel = new ExpenseModel();
        $stockModel = new StockModel();
        $productModel = new ProductModel();

        // 1. Total Sales (Overall)
        $sales = $saleModel->findAll();
        $totalSales = 0;
        foreach($sales as $s) { $totalSales += $s->total_amount; }

        // 2. Total Purchases
        $purchases = $purchaseModel->findAll();
        $totalPurchases = 0;
        foreach($purchases as $p) { $totalPurchases += $p->total_amount; }

        // 3. Total Expenses
        $expenses = $expenseModel->findAll();
        $totalExpenses = 0;
        foreach($expenses as $e) { $totalExpenses += $e->amount; }

        // 4. Stock Valuation (Cost Value)
        $allStock = $stockModel->findAll();
        $products = $productModel->findAll();
        
        $productCostMap = [];
        foreach($products as $p) {
            $productCostMap[$p->id] = $p->purchase_price;
        }

        $stockValuation = 0;
        foreach($allStock as $st) {
            $cost = $productCostMap[$st->product_id] ?? 0;
            $stockValuation += ($st->quantity * $cost);
        }

        // 5. Estimated Profit
        // Profit = Sales - Purchases - Expenses + Stock Valuation (Assuming unsold stock holds value)
        $netProfit = $totalSales - $totalPurchases - $totalExpenses + $stockValuation;

        $data = [
            'pageTitle' => 'Reports & Analytics',
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalExpenses' => $totalExpenses,
            'stockValuation' => $stockValuation,
            'netProfit' => $netProfit,
            
            // Top 5 selling products (could be complex, mock or query)
            // Let's do simple Recent Sales for now
            'recentSales' => $saleModel
                            ->select('sales.*, customers.name as customer_name')
                            ->join('customers', 'customers.id = sales.customer_id', 'left')
                            ->orderBy('sales.sale_date', 'DESC')
                            ->limit(5)
                            ->findAll()
        ];

        return view('reports/index', $data);
    }

    public function sales()
    {
        $saleModel = new SaleModel();
        
        // Date range filtering
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        $sales = $saleModel->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('DATE(sales.sale_date) >=', $startDate)
            ->where('DATE(sales.sale_date) <=', $endDate)
            ->orderBy('sales.sale_date', 'DESC')
            ->findAll();

        $totalSales = 0;
        $totalPaid = 0;
        foreach($sales as $s) {
            $totalSales += $s->total_amount;
            $totalPaid += $s->paid_amount;
        }

        $data = [
            'pageTitle' => 'Sales Report',
            'sales' => $sales,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'totalSales' => $totalSales,
            'totalPaid' => $totalPaid
        ];
        return view('reports/sales', $data);
    }

    public function purchases()
    {
        $purchaseModel = new PurchaseModel();
        
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        $purchases = $purchaseModel->select('purchases.*, vendors.name as supplier_name')
            ->join('vendors', 'vendors.id = purchases.vendor_id', 'left')
            ->where('DATE(purchases.purchase_date) >=', $startDate)
            ->where('DATE(purchases.purchase_date) <=', $endDate)
            ->orderBy('purchases.purchase_date', 'DESC')
            ->findAll();

        $totalPurchases = 0;
        $totalPaid = 0;
        foreach($purchases as $p) {
            $totalPurchases += $p->total_amount;
            $totalPaid += $p->paid_amount;
        }

        $data = [
            'pageTitle' => 'Purchase Report',
            'purchases' => $purchases,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'totalPurchases' => $totalPurchases,
            'totalPaid' => $totalPaid
        ];
        return view('reports/purchases', $data);
    }

    public function inventory()
    {
        $stockModel = new StockModel();
        
        // Detailed stock valuation and low stock alerts
        $inventory = $stockModel->select('stock.*, products.name as product_name, products.sku, products.purchase_price, products.selling_price, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stock.product_id', 'left')
            ->join('warehouses', 'warehouses.id = stock.warehouse_id', 'left')
            ->orderBy('products.name', 'ASC')
            ->findAll();

        $totalValuation = 0;
        $totalSellingValuation = 0;
        $lowStockItems = [];

        foreach($inventory as $item) {
            $totalValuation += ($item->quantity * $item->purchase_price);
            $totalSellingValuation += ($item->quantity * $item->selling_price);
            
            // Assume min stock is 10 for demonstration since min_stock_level isn't directly on stock table if not joined
            // If product has min_stock_level, we would join it. We'll use a static threshold of 10 or 0 for OOS
            if ($item->quantity <= 10) {
                $lowStockItems[] = $item;
            }
        }

        $data = [
            'pageTitle' => 'Inventory & Valuation Report',
            'inventory' => $inventory,
            'totalValuation' => $totalValuation,
            'totalSellingValuation' => $totalSellingValuation,
            'lowStockItems' => $lowStockItems
        ];
        return view('reports/inventory', $data);
    }

    public function commissions()
    {
        $db = \Config\Database::connect();
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        $builder = $db->table('sales s')
            ->select('u.name as salesperson, COUNT(s.id) as total_sales, SUM(s.total_amount) as total_revenue, SUM(s.commission_amount) as total_commission')
            ->join('users u', 'u.id = s.salesperson_id')
            ->where('s.sale_date >=', $startDate . ' 00:00:00')
            ->where('s.sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('s.salesperson_id')
            ->orderBy('total_commission', 'DESC');

        $data = [
            'pageTitle' => 'Salesperson Commissions Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'commissions' => $builder->get()->getResult()
        ];

        return view('reports/commissions', $data);
    }
}
