<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== PUBLIC ROUTES ====================
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/logout', 'AuthController::logout');

// ==================== PROTECTED ROUTES ====================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/dashboard/export', 'DashboardController::export');
    $routes->get('/api/search', 'DashboardController::search');

    // User Management
    $routes->get('/users', 'UserController::index');
    $routes->post('/users', 'UserController::store');
    $routes->get('/users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('/users/update/(:num)', 'UserController::update/$1');
    $routes->post('/users/delete/(:num)', 'UserController::delete/$1');

    // Role Management
    $routes->get('/roles', 'RoleController::index');
    $routes->post('/roles', 'RoleController::store');
    $routes->get('/roles/edit/(:num)', 'RoleController::edit/$1');
    $routes->post('/roles/update/(:num)', 'RoleController::update/$1');
    $routes->post('/roles/delete/(:num)', 'RoleController::delete/$1');

    // Branch Management
    $routes->get('/branches', 'BranchController::index');
    $routes->post('/branches', 'BranchController::store');
    $routes->get('/branches/edit/(:num)', 'BranchController::edit/$1');
    $routes->post('/branches/update/(:num)', 'BranchController::update/$1');
    $routes->post('/branches/delete/(:num)', 'BranchController::delete/$1');

    // Settings
    $routes->get('/settings/company', 'SettingsController::company');
    $routes->post('/settings/company', 'SettingsController::updateCompany');
    $routes->get('/settings/general', 'SettingsController::general');
    $routes->post('/settings/general', 'SettingsController::updateGeneral');

    // ==================== PHASE 2 ROUTES ====================
    // Categories
    $routes->get('/categories', 'CategoryController::index');
    $routes->post('/categories', 'CategoryController::store');
    $routes->get('/categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('/categories/update/(:num)', 'CategoryController::update/$1');
    $routes->post('/categories/delete/(:num)', 'CategoryController::delete/$1');

    // Brands
    $routes->get('/brands', 'BrandController::index');
    $routes->post('/brands', 'BrandController::store');
    $routes->get('/brands/edit/(:num)', 'BrandController::edit/$1');
    $routes->post('/brands/update/(:num)', 'BrandController::update/$1');
    $routes->post('/brands/delete/(:num)', 'BrandController::delete/$1');

    // Units
    $routes->get('/units', 'UnitController::index');
    $routes->post('/units', 'UnitController::store');
    $routes->get('/units/edit/(:num)', 'UnitController::edit/$1');
    $routes->post('/units/update/(:num)', 'UnitController::update/$1');
    $routes->post('/units/delete/(:num)', 'UnitController::delete/$1');

    // Taxes
    $routes->get('/taxes', 'TaxController::index');
    $routes->post('/taxes', 'TaxController::store');
    $routes->get('/taxes/edit/(:num)', 'TaxController::edit/$1');
    $routes->post('/taxes/update/(:num)', 'TaxController::update/$1');
    $routes->post('/taxes/delete/(:num)', 'TaxController::delete/$1');

    // Products
    $routes->get('/products', 'ProductController::index');
    $routes->get('/products/create', 'ProductController::create');
    $routes->post('/products/store', 'ProductController::store');
    $routes->get('/products/edit/(:num)', 'ProductController::edit/$1');
    $routes->post('/products/update/(:num)', 'ProductController::update/$1');
    $routes->post('/products/delete/(:num)', 'ProductController::delete/$1');

    // Warehouses
    $routes->get('/warehouses', 'WarehouseController::index');
    $routes->post('/warehouses', 'WarehouseController::store');
    $routes->get('/warehouses/edit/(:num)', 'WarehouseController::edit/$1');
    $routes->post('/warehouses/update/(:num)', 'WarehouseController::update/$1');
    $routes->post('/warehouses/delete/(:num)', 'WarehouseController::delete/$1');

    // Stock
    $routes->get('/stock', 'StockController::index');
    $routes->get('/stock/export', 'StockController::exportCSV');
    // Stock Transfers
    $routes->get('/stock-transfers', 'StockTransferController::index');
    $routes->get('/stock-transfers/create', 'StockTransferController::create');
    $routes->post('/stock-transfers', 'StockTransferController::store');
    $routes->get('/stock-transfers/view/(:num)', 'StockTransferController::view/$1');
    $routes->post('/stock-transfers/approve/(:num)', 'StockTransferController::approve/$1');
    $routes->post('/stock-transfers/complete/(:num)', 'StockTransferController::complete/$1');
    $routes->post('/stock-transfers/delete/(:num)', 'StockTransferController::delete/$1');

    // Stock Adjustments
    $routes->get('/stock-adjustments', 'StockAdjustmentController::index');
    $routes->get('/stock-adjustments/create', 'StockAdjustmentController::create');
    $routes->post('/stock-adjustments', 'StockAdjustmentController::store');
    $routes->get('/stock-adjustments/view/(:num)', 'StockAdjustmentController::view/$1');
    $routes->post('/stock-adjustments/approve/(:num)', 'StockAdjustmentController::approve/$1');
    $routes->post('/stock-adjustments/complete/(:num)', 'StockAdjustmentController::complete/$1');
    $routes->post('/stock-adjustments/delete/(:num)', 'StockAdjustmentController::delete/$1');

    // Customers
    $routes->get('/customers', 'CustomerController::index');
    $routes->get('/customers/create', 'CustomerController::create');
    $routes->post('/customers/store', 'CustomerController::store');
    $routes->get('/customers/edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('/customers/update/(:num)', 'CustomerController::update/$1');
    $routes->get('/customers/view/(:num)', 'CustomerController::view/$1');

    // Vendors
    $routes->get('/vendors', 'VendorController::index');
    $routes->get('/vendors/create', 'VendorController::create');
    $routes->post('/vendors/store', 'VendorController::store');
    $routes->get('/vendors/edit/(:num)', 'VendorController::edit/$1');
    $routes->post('/vendors/update/(:num)', 'VendorController::update/$1');
    $routes->get('/vendors/view/(:num)', 'VendorController::view/$1');
    $routes->get('/vendors/delete/(:num)', 'VendorController::delete/$1');

    // Quotations
    $routes->get('/quotations', 'QuotationController::index');
    $routes->get('/quotations/create', 'QuotationController::create');
    $routes->post('/quotations/store', 'QuotationController::store');
    $routes->get('/quotations/view/(:num)', 'QuotationController::view/$1');
    $routes->post('/quotations/status/(:num)', 'QuotationController::updateStatus/$1');
    $routes->get('/quotations/delete/(:num)', 'QuotationController::delete/$1');

    // Sales Invoices
    $routes->get('/sales', 'SalesController::index');
    $routes->get('/sales/create', 'SalesController::create');
    $routes->post('/sales/store', 'SalesController::store');
    $routes->get('/sales/view/(:num)', 'SalesController::view/$1');
    $routes->post('/sales/payment/(:num)', 'SalesController::updatePayment/$1');
    $routes->post('/sales/send/(:num)', 'SalesController::sendInvoice/$1');
    $routes->get('/sales/delete/(:num)', 'SalesController::delete/$1');

    // Sales Returns
    $routes->get('/sales-returns', 'SaleReturnController::index');
    $routes->get('/sales-returns/create', 'SaleReturnController::create');
    $routes->post('/sales-returns/store', 'SaleReturnController::store');
    $routes->get('/sales-returns/view/(:num)', 'SaleReturnController::view/$1');
    $routes->get('/sales-returns/delete/(:num)', 'SaleReturnController::delete/$1');

    // QC Inspections
    $routes->get('/inspections', 'InspectionController::index');
    $routes->get('/inspections/approve/(:num)', 'InspectionController::approve/$1');
    $routes->get('/inspections/reject/(:num)', 'InspectionController::reject/$1');

    // Purchase Orders
    $routes->get('/purchase-orders', 'PurchaseOrderController::index');
    $routes->get('/purchase-orders/create', 'PurchaseOrderController::create');
    $routes->post('/purchase-orders/store', 'PurchaseOrderController::store');
    $routes->get('/purchase-orders/view/(:num)', 'PurchaseOrderController::view/$1');
    $routes->post('/purchase-orders/status/(:num)', 'PurchaseOrderController::updateStatus/$1');
    $routes->match(['get', 'post'], '/purchase-orders/convert/(:num)', 'PurchaseOrderController::convertToInvoice/$1');
    $routes->get('/purchase-orders/delete/(:num)', 'PurchaseOrderController::delete/$1');

    // Purchase Invoices
    $routes->get('/purchases', 'PurchaseController::index');
    $routes->get('/purchases/create', 'PurchaseController::create');
    $routes->post('/purchases/store', 'PurchaseController::store');
    $routes->get('/purchases/view/(:num)', 'PurchaseController::view/$1');
    $routes->post('/purchases/payment/(:num)', 'PurchaseController::updatePayment/$1');
    $routes->get('/purchases/delete/(:num)', 'PurchaseController::delete/$1');

    // Purchase Returns
    $routes->get('/purchase-returns', 'PurchaseReturnController::index');
    $routes->get('/purchase-returns/create', 'PurchaseReturnController::create');
    $routes->post('/purchase-returns/store', 'PurchaseReturnController::store');
    $routes->get('/purchase-returns/view/(:num)', 'PurchaseReturnController::view/$1');
    $routes->get('/purchase-returns/delete/(:num)', 'PurchaseReturnController::delete/$1');

    // POS
    $routes->get('/pos', 'PosController::index');
    $routes->get('/pos/search-products', 'PosController::searchProducts');
    $routes->post('/pos/checkout', 'PosController::checkout');
    $routes->post('/pos/add-customer', 'PosController::addCustomer');

    // Payments
    $routes->get('/payments', 'PaymentController::index');
    $routes->get('/payments/create', 'PaymentController::create');
    $routes->post('/payments/store', 'PaymentController::store');
    $routes->post('/payments/delete/(:num)', 'PaymentController::delete/$1');

    // Receivables
    $routes->get('/receivables', 'ReceivableController::index');

    // Payables
    $routes->get('/payables', 'PayableController::index');

    // Expenses
    $routes->get('/expenses', 'ExpenseController::index');
    $routes->get('/expenses/create', 'ExpenseController::create');
    $routes->post('/expenses/store', 'ExpenseController::store');
    $routes->post('/expenses/delete/(:num)', 'ExpenseController::delete/$1');

    // Reports
    $routes->get('/reports', 'ReportController::index');
    $routes->get('/reports/sales', 'ReportController::sales');
    $routes->get('/reports/purchases', 'ReportController::purchases');
    $routes->get('/reports/inventory', 'ReportController::inventory');
    $routes->get('/reports/commissions', 'ReportController::commissions');

    // Notifications & Alerts
    $routes->get('/notifications', 'NotificationController::index');
    $routes->get('/notifications/mark-all-read', 'NotificationController::markAllRead');
    $routes->get('/notifications/mark-read/(:num)', 'NotificationController::markRead/$1');

    // Document Management
    $routes->post('/documents/upload', 'DocumentController::upload');
    $routes->get('/documents/download/(:num)', 'DocumentController::download/$1');
    $routes->post('/documents/delete/(:num)', 'DocumentController::delete/$1');
    $routes->get('/documents/delete/(:num)', 'DocumentController::delete/$1');

    // System Utilities
    $routes->get('/backup/database', 'BackupController::database');
    $routes->get('/audit-logs', 'AuditLogController::index');

    // Mobile Application Ecosystem
    $routes->get('/mobile-app', 'MobileAppController::index');
    $routes->post('/mobile-app/send-push', 'MobileAppController::sendPushAlert');
    $routes->get('/mobile-app/download', 'MobileAppController::downloadApk');
    $routes->post('/mobile-app/generate-key', 'MobileAppController::generateApiKey');
});
