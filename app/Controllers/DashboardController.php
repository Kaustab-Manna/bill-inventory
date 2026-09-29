<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanySettingModel;

class DashboardController extends BaseController
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $companyModel = new CompanySettingModel();
        
        $saleModel = new \App\Models\SaleModel();
        $purchaseModel = new \App\Models\PurchaseModel();
        $productModel = new \App\Models\ProductModel();
        $customerModel = new \App\Models\CustomerModel();
        $vendorModel = new \App\Models\VendorModel();

        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Today's Sales
        $todaySalesRow = $saleModel->selectSum('total_amount')->where('DATE(sale_date)', $today)->where('status !=', 'cancelled')->first();
        $todaySales = $todaySalesRow && $todaySalesRow->total_amount ? $todaySalesRow->total_amount : 0;

        // Today's Purchases
        $todayPurchasesRow = $purchaseModel->selectSum('total_amount')->where('DATE(purchase_date)', $today)->first();
        $todayPurchases = $todayPurchasesRow && $todayPurchasesRow->total_amount ? $todayPurchasesRow->total_amount : 0;

        // This Month's Sales
        $monthSalesRow = $saleModel->selectSum('total_amount')->where('MONTH(sale_date)', $currentMonth)->where('YEAR(sale_date)', $currentYear)->where('status !=', 'cancelled')->first();
        $monthSales = $monthSalesRow && $monthSalesRow->total_amount ? $monthSalesRow->total_amount : 0;

        // This Month's Purchases
        $monthPurchasesRow = $purchaseModel->selectSum('total_amount')->where('MONTH(purchase_date)', $currentMonth)->where('YEAR(purchase_date)', $currentYear)->first();
        $monthPurchases = $monthPurchasesRow && $monthPurchasesRow->total_amount ? $monthPurchasesRow->total_amount : 0;

        // Pending Receivables & Payables
        $salesTotals = $saleModel->selectSum('total_amount')->selectSum('paid_amount')->where('status !=', 'cancelled')->first();
        $pendingReceivables = $salesTotals ? (($salesTotals->total_amount ?? 0) - ($salesTotals->paid_amount ?? 0)) : 0;

        $purchasesTotals = $purchaseModel->selectSum('total_amount')->selectSum('paid_amount')->first();
        $pendingPayables = $purchasesTotals ? (($purchasesTotals->total_amount ?? 0) - ($purchasesTotals->paid_amount ?? 0)) : 0;

        // Stock Calculation
        $stockQuery = $db->query("
            SELECT p.id, p.min_stock_level, IFNULL(s.quantity, 0) as total_qty
            FROM products p
            LEFT JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.is_active = 1
        ")->getResult();

        $lowStockCount = 0;
        $outOfStockCount = 0;
        foreach ($stockQuery as $item) {
            $qty = (float)$item->total_qty;
            $minLevel = (float)($item->min_stock_level ?? 0);
            
            if ($qty <= 0) {
                $outOfStockCount++;
            } elseif ($minLevel > 0 && $qty <= $minLevel) {
                $lowStockCount++;
            }
        }

        // Graph Data (Year)
        $yearlySales = array_fill(1, 12, 0);
        $yearlyPurchases = array_fill(1, 12, 0);
        
        $salesByMonth = $db->query("SELECT MONTH(sale_date) as m, SUM(total_amount) as total FROM sales WHERE YEAR(sale_date) = ? AND status != 'cancelled' GROUP BY MONTH(sale_date)", [$currentYear])->getResult();
        foreach ($salesByMonth as $row) $yearlySales[(int)$row->m] = (float)$row->total;
        
        $purchasesByMonth = $db->query("SELECT MONTH(purchase_date) as m, SUM(total_amount) as total FROM purchases WHERE YEAR(purchase_date) = ? GROUP BY MONTH(purchase_date)", [$currentYear])->getResult();
        foreach ($purchasesByMonth as $row) $yearlyPurchases[(int)$row->m] = (float)$row->total;

        // Graph Data (Week & Month - Days)
        $monthLabels = []; $chartMonthSales = []; $chartMonthPurchases = [];
        $weekLabels = []; $weekSales = []; $weekPurchases = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartMonthSales[$date] = 0; 
            $chartMonthPurchases[$date] = 0;
            $monthLabels[] = date('M d', strtotime($date));
            if ($i < 7) {
                $weekSales[$date] = 0; 
                $weekPurchases[$date] = 0;
                $weekLabels[] = date('D', strtotime($date));
            }
        }
        
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        
        $salesByDay = $db->query("SELECT DATE(sale_date) as d, SUM(total_amount) as total FROM sales WHERE sale_date >= ? AND status != 'cancelled' GROUP BY DATE(sale_date)", [$thirtyDaysAgo])->getResult();
        foreach ($salesByDay as $row) {
            if (isset($chartMonthSales[$row->d])) $chartMonthSales[$row->d] = (float)$row->total;
            if (isset($weekSales[$row->d])) $weekSales[$row->d] = (float)$row->total;
        }

        $purchasesByDay = $db->query("SELECT DATE(purchase_date) as d, SUM(total_amount) as total FROM purchases WHERE purchase_date >= ? GROUP BY DATE(purchase_date)", [$thirtyDaysAgo])->getResult();
        foreach ($purchasesByDay as $row) {
            if (isset($chartMonthPurchases[$row->d])) $chartMonthPurchases[$row->d] = (float)$row->total;
            if (isset($weekPurchases[$row->d])) $weekPurchases[$row->d] = (float)$row->total;
        }
        
        $chartData = [
            'year' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'sales' => array_values($yearlySales),
                'purchases' => array_values($yearlyPurchases)
            ],
            'month' => [
                'labels' => $monthLabels,
                'sales' => array_values($chartMonthSales),
                'purchases' => array_values($chartMonthPurchases)
            ],
            'week' => [
                'labels' => $weekLabels,
                'sales' => array_values($weekSales),
                'purchases' => array_values($weekPurchases)
            ]
        ];

        // KPI Data
        $data = [
            'pageTitle'   => 'Dashboard',
            'company'     => $companyModel->getSettings(),
            'totalUsers'  => $userModel->countActive(),
            'totalSales'  => $saleModel->where('status !=', 'cancelled')->countAllResults(),
            'totalPurchases' => $purchaseModel->countAllResults(),
            'totalProducts'  => $productModel->countAllResults(),
            'totalCustomers' => $customerModel->countAllResults(),
            'totalSuppliers' => $vendorModel->countAllResults(),
            'todaySales'     => $todaySales,
            'todayPurchases' => $todayPurchases,
            'monthSales'     => $monthSales,
            'monthPurchases' => $monthPurchases,
            'lowStockCount'  => $lowStockCount,
            'outOfStockCount'=> $outOfStockCount,
            'pendingReceivables' => $pendingReceivables,
            'pendingPayables'    => $pendingPayables,
            'recentActivity'     => (new \App\Models\AuditLogModel())->getRecent(5),
            'topProducts'    => [],
            'chartData'      => json_encode($chartData),
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Export Dashboard Summary as CSV
     */
    public function export()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $companyModel = new CompanySettingModel();
        
        $saleModel = new \App\Models\SaleModel();
        $purchaseModel = new \App\Models\PurchaseModel();
        $productModel = new \App\Models\ProductModel();
        $customerModel = new \App\Models\CustomerModel();
        $vendorModel = new \App\Models\VendorModel();
        $auditModel = new \App\Models\AuditLogModel();

        $company = $companyModel->getSettings();
        $currency = $company->currency_symbol ?? '₹';

        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Today's Sales & Purchases
        $todaySalesRow = $saleModel->selectSum('total_amount')->where('DATE(sale_date)', $today)->where('status !=', 'cancelled')->first();
        $todaySales = $todaySalesRow && $todaySalesRow->total_amount ? (float)$todaySalesRow->total_amount : 0;

        $todayPurchasesRow = $purchaseModel->selectSum('total_amount')->where('DATE(purchase_date)', $today)->first();
        $todayPurchases = $todayPurchasesRow && $todayPurchasesRow->total_amount ? (float)$todayPurchasesRow->total_amount : 0;

        // Month's Sales & Purchases
        $monthSalesRow = $saleModel->selectSum('total_amount')->where('MONTH(sale_date)', $currentMonth)->where('YEAR(sale_date)', $currentYear)->where('status !=', 'cancelled')->first();
        $monthSales = $monthSalesRow && $monthSalesRow->total_amount ? (float)$monthSalesRow->total_amount : 0;

        $monthPurchasesRow = $purchaseModel->selectSum('total_amount')->where('MONTH(purchase_date)', $currentMonth)->where('YEAR(purchase_date)', $currentYear)->first();
        $monthPurchases = $monthPurchasesRow && $monthPurchasesRow->total_amount ? (float)$monthPurchasesRow->total_amount : 0;

        // Receivables & Payables
        $salesTotals = $saleModel->selectSum('total_amount')->selectSum('paid_amount')->where('status !=', 'cancelled')->first();
        $pendingReceivables = $salesTotals ? (($salesTotals->total_amount ?? 0) - ($salesTotals->paid_amount ?? 0)) : 0;

        $purchasesTotals = $purchaseModel->selectSum('total_amount')->selectSum('paid_amount')->first();
        $pendingPayables = $purchasesTotals ? (($purchasesTotals->total_amount ?? 0) - ($purchasesTotals->paid_amount ?? 0)) : 0;

        // Stock Counts
        $stockQuery = $db->query("
            SELECT p.id, p.min_stock_level, IFNULL(s.quantity, 0) as total_qty
            FROM products p
            LEFT JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.is_active = 1
        ")->getResult();

        $lowStockCount = 0;
        $outOfStockCount = 0;
        foreach ($stockQuery as $item) {
            $qty = (float)$item->total_qty;
            $minLevel = (float)($item->min_stock_level ?? 0);
            if ($qty <= 0) {
                $outOfStockCount++;
            } elseif ($minLevel > 0 && $qty <= $minLevel) {
                $lowStockCount++;
            }
        }

        $filename = 'dashboard_summary_' . date('Y-m-d_His') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Type: text/csv; charset=UTF-8");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header section
        fputcsv($output, [$company->name ?? 'BillInventory Pro', 'Dashboard Executive Summary']);
        fputcsv($output, ['Generated Date', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Currency', $currency]);
        fputcsv($output, []);

        // Key Performance Indicators
        fputcsv($output, ['=== KEY PERFORMANCE INDICATORS ===']);
        fputcsv($output, ['Metric', 'Value', 'Notes']);
        fputcsv($output, ["Today's Sales", number_format($todaySales, 2), "Active sales for {$today}"]);
        fputcsv($output, ["Today's Purchases", number_format($todayPurchases, 2), "Purchases on {$today}"]);
        fputcsv($output, ["This Month's Sales", number_format($monthSales, 2), "Month to date"]);
        fputcsv($output, ["This Month's Purchases", number_format($monthPurchases, 2), "Month to date"]);
        fputcsv($output, ["Pending Receivables", number_format($pendingReceivables, 2), "Unpaid customer balance"]);
        fputcsv($output, ["Pending Payables", number_format($pendingPayables, 2), "Unpaid supplier balance"]);
        fputcsv($output, ["Low Stock Items", $lowStockCount, "Items below threshold"]);
        fputcsv($output, ["Out of Stock Items", $outOfStockCount, "Items with 0 quantity"]);
        fputcsv($output, ["Total Products Catalog", $productModel->countAllResults(), "Total registered products"]);
        fputcsv($output, ["Total Customers", $customerModel->countAllResults(), "Total registered customers"]);
        fputcsv($output, ["Total Suppliers", $vendorModel->countAllResults(), "Total registered suppliers"]);
        fputcsv($output, ["Active Users", $userModel->countActive(), "Active system accounts"]);
        fputcsv($output, []);

        // Last 7 Days Daily Breakdown
        fputcsv($output, ['=== LAST 7 DAYS PERFORMANCE ===']);
        fputcsv($output, ['Date', 'Day', 'Sales Amount', 'Purchases Amount']);
        for ($i = 6; $i >= 0; $i--) {
            $dayDate = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('D', strtotime($dayDate));
            $daySale = $db->query("SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = ? AND status != 'cancelled'", [$dayDate])->getRow();
            $dayPurchase = $db->query("SELECT SUM(total_amount) as total FROM purchases WHERE DATE(purchase_date) = ?", [$dayDate])->getRow();

            fputcsv($output, [
                $dayDate,
                $dayName,
                number_format($daySale && $daySale->total ? (float)$daySale->total : 0, 2),
                number_format($dayPurchase && $dayPurchase->total ? (float)$dayPurchase->total : 0, 2)
            ]);
        }
        fputcsv($output, []);

        // Recent Activities
        fputcsv($output, ['=== RECENT AUDIT ACTIVITIES ===']);
        fputcsv($output, ['Action', 'Module', 'User', 'Date / Time']);
        $recent = $auditModel->getRecent(20);
        foreach ($recent as $act) {
            fputcsv($output, [
                strtoupper($act->action ?? ''),
                ucfirst($act->module ?? ''),
                $act->user_name ?? 'System',
                $act->created_at ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Live Global Search across pages, products, customers, vendors, and invoices
     */
    public function search()
    {
        $query = trim($this->request->getGet('q') ?? '');
        if (strlen($query) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $results = [];

        // 1. System Navigation Pages
        $pages = [
            ['title' => 'Dashboard', 'url' => base_url('dashboard'), 'category' => 'Navigation', 'icon' => 'fas fa-th-large'],
            ['title' => 'POS Billing Terminal', 'url' => base_url('pos'), 'category' => 'Navigation', 'icon' => 'fas fa-cash-register'],
            ['title' => 'Products List', 'url' => base_url('products'), 'category' => 'Navigation', 'icon' => 'fas fa-boxes-stacked'],
            ['title' => 'Add New Product', 'url' => base_url('products/create'), 'category' => 'Navigation', 'icon' => 'fas fa-plus'],
            ['title' => 'Warehouses', 'url' => base_url('warehouses'), 'category' => 'Navigation', 'icon' => 'fas fa-warehouse'],
            ['title' => 'Stock Overview', 'url' => base_url('stock'), 'category' => 'Navigation', 'icon' => 'fas fa-layer-group'],
            ['title' => 'Stock Transfers', 'url' => base_url('stock-transfers'), 'category' => 'Navigation', 'icon' => 'fas fa-truck-ramp-box'],
            ['title' => 'Stock Adjustments', 'url' => base_url('stock-adjustments'), 'category' => 'Navigation', 'icon' => 'fas fa-sliders'],
            ['title' => 'Sales Invoices', 'url' => base_url('sales'), 'category' => 'Navigation', 'icon' => 'fas fa-file-invoice-dollar'],
            ['title' => 'Create Sale Invoice', 'url' => base_url('sales/create'), 'category' => 'Navigation', 'icon' => 'fas fa-file-circle-plus'],
            ['title' => 'Quotations', 'url' => base_url('quotations'), 'category' => 'Navigation', 'icon' => 'fas fa-file-lines'],
            ['title' => 'Purchase Invoices', 'url' => base_url('purchases'), 'category' => 'Navigation', 'icon' => 'fas fa-cart-shopping'],
            ['title' => 'Create Purchase', 'url' => base_url('purchases/create'), 'category' => 'Navigation', 'icon' => 'fas fa-cart-plus'],
            ['title' => 'Purchase Orders', 'url' => base_url('purchase-orders'), 'category' => 'Navigation', 'icon' => 'fas fa-clipboard-list'],
            ['title' => 'Customers', 'url' => base_url('customers'), 'category' => 'Navigation', 'icon' => 'fas fa-users'],
            ['title' => 'Vendors & Suppliers', 'url' => base_url('vendors'), 'category' => 'Navigation', 'icon' => 'fas fa-handshake'],
            ['title' => 'Payments', 'url' => base_url('payments'), 'category' => 'Navigation', 'icon' => 'fas fa-coins'],
            ['title' => 'Reports & Analytics', 'url' => base_url('reports'), 'category' => 'Navigation', 'icon' => 'fas fa-chart-line'],
            ['title' => 'Users Management', 'url' => base_url('users'), 'category' => 'Navigation', 'icon' => 'fas fa-user-gear'],
            ['title' => 'Roles & Permissions', 'url' => base_url('roles'), 'category' => 'Navigation', 'icon' => 'fas fa-shield-halved'],
            ['title' => 'Branches', 'url' => base_url('branches'), 'category' => 'Navigation', 'icon' => 'fas fa-building'],
            ['title' => 'Audit Trail Logs', 'url' => base_url('audit-logs'), 'category' => 'Navigation', 'icon' => 'fas fa-clock-rotate-left'],
            ['title' => 'Company Settings', 'url' => base_url('settings/company'), 'category' => 'Navigation', 'icon' => 'fas fa-gear'],
        ];

        foreach ($pages as $p) {
            if (stripos($p['title'], $query) !== false) {
                $results[] = [
                    'title'    => $p['title'],
                    'subtitle' => 'System Module',
                    'url'      => $p['url'],
                    'category' => 'Navigation',
                    'icon'     => $p['icon']
                ];
            }
        }

        // 2. Products
        $productModel = new \App\Models\ProductModel();
        $products = $productModel->select('id, name, sku, selling_price')
                                 ->groupStart()
                                     ->like('name', $query)
                                     ->orLike('sku', $query)
                                     ->orLike('barcode', $query)
                                 ->groupEnd()
                                 ->where('deleted_at', null)
                                 ->limit(5)
                                 ->findAll();

        foreach ($products as $pr) {
            $results[] = [
                'title'    => $pr->name,
                'subtitle' => 'SKU: ' . $pr->sku . ' • ₹' . number_format($pr->selling_price, 2),
                'url'      => base_url('products?q=' . urlencode($pr->name)),
                'category' => 'Products',
                'icon'     => 'fas fa-box'
            ];
        }

        // 3. Customers
        $customerModel = new \App\Models\CustomerModel();
        $customers = $customerModel->select('id, name, phone, email')
                                   ->groupStart()
                                       ->like('name', $query)
                                       ->orLike('phone', $query)
                                       ->orLike('email', $query)
                                   ->groupEnd()
                                   ->limit(4)
                                   ->findAll();

        foreach ($customers as $c) {
            $results[] = [
                'title'    => $c->name,
                'subtitle' => ($c->phone ?: $c->email ?: 'Customer'),
                'url'      => base_url('customers/view/' . $c->id),
                'category' => 'Customers',
                'icon'     => 'fas fa-user'
            ];
        }

        // 4. Vendors
        $vendorModel = new \App\Models\VendorModel();
        $vendors = $vendorModel->select('id, name, phone, email')
                               ->groupStart()
                                   ->like('name', $query)
                                   ->orLike('phone', $query)
                                   ->orLike('email', $query)
                               ->groupEnd()
                               ->limit(4)
                               ->findAll();

        foreach ($vendors as $v) {
            $results[] = [
                'title'    => $v->name,
                'subtitle' => ($v->phone ?: $v->email ?: 'Supplier'),
                'url'      => base_url('vendors/view/' . $v->id),
                'category' => 'Vendors',
                'icon'     => 'fas fa-truck'
            ];
        }

        // 5. Sales Invoices
        $saleModel = new \App\Models\SaleModel();
        $sales = $saleModel->select('id, invoice_no, total_amount, sale_date')
                           ->like('invoice_no', $query)
                           ->limit(4)
                           ->findAll();

        foreach ($sales as $s) {
            $results[] = [
                'title'    => $s->invoice_no,
                'subtitle' => '₹' . number_format($s->total_amount, 2) . ' • ' . date('d M Y', strtotime($s->sale_date)),
                'url'      => base_url('sales/view/' . $s->id),
                'category' => 'Sales',
                'icon'     => 'fas fa-file-invoice'
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }
}
