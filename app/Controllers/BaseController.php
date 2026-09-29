<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $session = \Config\Services::session();

        // Check and ensure database tables at most once per session to maximize speed
        if (!$session->get('schema_checked_v3')) {
            try {
                $db = \Config\Database::connect();
                if (
                    !$db->fieldExists('commission_rate', 'users') ||
                    !$db->tableExists('purchases') ||
                    !$db->tableExists('payments') ||
                    !$db->fieldExists('customer_id', 'payments') ||
                    !$db->fieldExists('vendor_id', 'payments') ||
                    !$db->tableExists('expenses') ||
                    !$db->fieldExists('payment_method', 'expenses')
                ) {
                    $this->ensureSchema($db);
                }
                $session->set('schema_checked_v3', true);
            } catch (\Throwable $me) {
                log_message('error', 'Auto-migration notice: ' . $me->getMessage());
            }
        }
        
        if ($session->get('isLoggedIn')) {
            $this->generateSystemAlerts($session);
            
            // Fetch unread notifications for the bell icon globally
            $notificationModel = new \App\Models\NotificationModel();
            $unreadCount = $notificationModel->getUnreadCount();
            $latestNotifications = $notificationModel->getLatestUnread(5);
            
            \Config\Services::renderer()->setVar('globalUnreadCount', $unreadCount);
            \Config\Services::renderer()->setVar('globalNotifications', $latestNotifications);
        }
    }

    protected function generateSystemAlerts($session)
    {
        // Only run this check once per session/day to save resources
        $lastChecked = $session->get('alerts_checked_date');
        $today = date('Y-m-d');
        
        if ($lastChecked === $today) {
            return;
        }

        $notificationModel = new \App\Models\NotificationModel();
        
        // 1. Check Low Stock
        $stockModel = new \App\Models\StockModel();
        $db = \Config\Database::connect();
        
        // Find products with stock <= 10
        $lowStocks = $db->table('stock')
            ->select('products.name, products.sku, stock.quantity')
            ->join('products', 'products.id = stock.product_id', 'left')
            ->where('stock.quantity <=', 10)
            ->get()->getResult();

        foreach ($lowStocks as $ls) {
            // Avoid duplicate active alerts for the same SKU
            $exists = $notificationModel->where('type', 'low_stock')
                                        ->like('message', $ls->sku)
                                        ->where('is_read', 0)
                                        ->first();
            if (!$exists) {
                $notificationModel->insert([
                    'type' => 'low_stock',
                    'title' => 'Low Stock Alert',
                    'message' => "Product {$ls->name} (SKU: {$ls->sku}) is running low. Current quantity: {$ls->quantity}.",
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $session->set('alerts_checked_date', $today);
    }

    protected function ensureSchema($db)
    {
        // 1. Try running migrations first
        try {
            $runner = \Config\Services::migrations();
            $runner->setNamespace(null);
            $runner->latest();
        } catch (\Throwable $e) {
            log_message('error', 'Migration runner note: ' . $e->getMessage());
        }

        // 2. Direct Fallback: Guarantee critical tables exist via raw SQL
        if (!$db->tableExists('vendors')) {
            $db->query("CREATE TABLE IF NOT EXISTS `vendors` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `contact_person` VARCHAR(100) NULL,
                `email` VARCHAR(150) NULL,
                `phone` VARCHAR(20) NULL,
                `address` TEXT NULL,
                `tax_number` VARCHAR(50) NULL,
                `is_active` TINYINT(1) DEFAULT 1,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchase_orders')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchase_orders` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `po_no` VARCHAR(50) UNIQUE NOT NULL,
                `vendor_id` BIGINT UNSIGNED NOT NULL,
                `warehouse_id` BIGINT UNSIGNED NOT NULL,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `discount_percent` DECIMAL(5,2) DEFAULT 0,
                `discount` DECIMAL(15,2) DEFAULT 0,
                `total_amount` DECIMAL(15,2) DEFAULT 0,
                `status` ENUM('draft', 'sent', 'approved', 'completed', 'cancelled') DEFAULT 'draft',
                `expected_date` DATE NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchase_order_items')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchase_order_items` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `po_id` BIGINT UNSIGNED NOT NULL,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `quantity` DECIMAL(15,3) DEFAULT 0,
                `unit_price` DECIMAL(15,2) DEFAULT 0,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total` DECIMAL(15,2) DEFAULT 0,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchases')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchases` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `invoice_no` VARCHAR(50) UNIQUE NOT NULL,
                `vendor_id` BIGINT UNSIGNED NOT NULL,
                `warehouse_id` BIGINT UNSIGNED NOT NULL,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `discount_percent` DECIMAL(5,2) DEFAULT 0,
                `discount` DECIMAL(15,2) DEFAULT 0,
                `total_amount` DECIMAL(15,2) DEFAULT 0,
                `paid_amount` DECIMAL(15,2) DEFAULT 0,
                `payment_status` ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
                `purchase_date` DATETIME NOT NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchase_items')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchase_items` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `purchase_id` BIGINT UNSIGNED NOT NULL,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `quantity` DECIMAL(15,3) DEFAULT 0,
                `unit_price` DECIMAL(15,2) DEFAULT 0,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total` DECIMAL(15,2) DEFAULT 0,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchase_returns')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchase_returns` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `return_no` VARCHAR(50) UNIQUE NOT NULL,
                `vendor_id` BIGINT UNSIGNED NOT NULL,
                `warehouse_id` BIGINT UNSIGNED NOT NULL,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total_amount` DECIMAL(15,2) DEFAULT 0,
                `return_date` DATETIME NOT NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('purchase_return_items')) {
            $db->query("CREATE TABLE IF NOT EXISTS `purchase_return_items` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `purchase_return_id` BIGINT UNSIGNED NOT NULL,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `quantity` DECIMAL(15,3) DEFAULT 0,
                `unit_price` DECIMAL(15,2) DEFAULT 0,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total` DECIMAL(15,2) DEFAULT 0,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('payments')) {
            $db->query("CREATE TABLE IF NOT EXISTS `payments` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `payment_no` VARCHAR(50) UNIQUE NOT NULL,
                `payment_date` DATE NOT NULL,
                `type` ENUM('in', 'out') DEFAULT 'in',
                `customer_id` BIGINT UNSIGNED NULL,
                `vendor_id` BIGINT UNSIGNED NULL,
                `sale_id` BIGINT UNSIGNED NULL,
                `purchase_id` BIGINT UNSIGNED NULL,
                `amount` DECIMAL(15,2) DEFAULT 0,
                `payment_method` VARCHAR(50) DEFAULT 'Cash',
                `reference_no` VARCHAR(100) NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('expenses')) {
            $db->query("CREATE TABLE IF NOT EXISTS `expenses` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `expense_no` VARCHAR(50) NULL,
                `expense_date` DATE NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `amount` DECIMAL(15,2) DEFAULT 0,
                `payment_method` VARCHAR(50) DEFAULT 'Cash',
                `branch_id` BIGINT UNSIGNED NULL,
                `reference_no` VARCHAR(100) NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('notifications')) {
            $db->query("CREATE TABLE IF NOT EXISTS `notifications` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` BIGINT UNSIGNED NULL,
                `type` VARCHAR(50) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `link` VARCHAR(255) NULL,
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('documents')) {
            $db->query("CREATE TABLE IF NOT EXISTS `documents` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `entity_type` VARCHAR(50) NOT NULL,
                `entity_id` BIGINT UNSIGNED NOT NULL,
                `file_name` VARCHAR(255) NOT NULL,
                `file_path` VARCHAR(255) NOT NULL,
                `file_type` VARCHAR(100) NULL,
                `file_size` BIGINT UNSIGNED NULL,
                `uploaded_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('sale_returns')) {
            $db->query("CREATE TABLE IF NOT EXISTS `sale_returns` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `return_no` VARCHAR(50) UNIQUE NOT NULL,
                `sale_id` BIGINT UNSIGNED NULL,
                `customer_id` BIGINT UNSIGNED NULL,
                `warehouse_id` BIGINT UNSIGNED NULL,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total_amount` DECIMAL(15,2) DEFAULT 0,
                `status` ENUM('pending', 'completed') DEFAULT 'completed',
                `return_date` DATE NOT NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                `deleted_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('sale_return_items')) {
            $db->query("CREATE TABLE IF NOT EXISTS `sale_return_items` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `sale_return_id` BIGINT UNSIGNED NOT NULL,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `quantity` DECIMAL(15,3) DEFAULT 0,
                `unit_price` DECIMAL(15,2) DEFAULT 0,
                `subtotal` DECIMAL(15,2) DEFAULT 0,
                `tax_amount` DECIMAL(15,2) DEFAULT 0,
                `total` DECIMAL(15,2) DEFAULT 0,
                `item_condition` ENUM('sealed', 'broken_seal') DEFAULT 'sealed',
                `inspection_status` ENUM('na', 'pending', 'passed', 'failed') DEFAULT 'na',
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        // 3. Ensure all extended columns exist across existing tables
        if ($db->tableExists('users') && !$db->fieldExists('commission_rate', 'users')) {
            $db->query("ALTER TABLE `users` ADD `commission_rate` DECIMAL(5,2) DEFAULT 0.00 AFTER `is_active`");
        }

        if ($db->tableExists('sales')) {
            if (!$db->fieldExists('salesperson_id', 'sales')) {
                $db->query("ALTER TABLE `sales` ADD `salesperson_id` INT UNSIGNED NULL AFTER `customer_id`");
            }
            if (!$db->fieldExists('commission_amount', 'sales')) {
                $db->query("ALTER TABLE `sales` ADD `commission_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `paid_amount`");
            }
            if (!$db->fieldExists('discount_percent', 'sales')) {
                $db->query("ALTER TABLE `sales` ADD `discount_percent` DECIMAL(5,2) DEFAULT 0.00 AFTER `tax_amount`");
            }
        }

        if ($db->tableExists('quotations') && !$db->fieldExists('discount_percent', 'quotations')) {
            $db->query("ALTER TABLE `quotations` ADD `discount_percent` DECIMAL(5,2) DEFAULT 0.00 AFTER `tax_amount`");
        }

        if ($db->tableExists('customers')) {
            if (!$db->fieldExists('gst_no', 'customers')) {
                $db->query("ALTER TABLE `customers` ADD `gst_no` VARCHAR(50) NULL");
            }
            if (!$db->fieldExists('city', 'customers')) {
                $db->query("ALTER TABLE `customers` ADD `city` VARCHAR(100) NULL");
            }
            if (!$db->fieldExists('state', 'customers')) {
                $db->query("ALTER TABLE `customers` ADD `state` VARCHAR(100) NULL");
            }
            if (!$db->fieldExists('pincode', 'customers')) {
                $db->query("ALTER TABLE `customers` ADD `pincode` VARCHAR(20) NULL");
            }
            if (!$db->fieldExists('credit_limit', 'customers')) {
                $db->query("ALTER TABLE `customers` ADD `credit_limit` DECIMAL(15,2) DEFAULT 0.00");
            }
        }

        if ($db->tableExists('company_settings')) {
            if (!$db->fieldExists('invoice_prefix', 'company_settings')) {
                $db->query("ALTER TABLE `company_settings` ADD `invoice_prefix` VARCHAR(20) NULL DEFAULT 'INV-'");
            }
            if (!$db->fieldExists('invoice_footer', 'company_settings')) {
                $db->query("ALTER TABLE `company_settings` ADD `invoice_footer` TEXT NULL");
            }
        }

        // Payments table columns
        if ($db->tableExists('payments')) {
            if (!$db->fieldExists('customer_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `customer_id` BIGINT UNSIGNED NULL AFTER `payment_date`");
            }
            if (!$db->fieldExists('vendor_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `vendor_id` BIGINT UNSIGNED NULL AFTER `customer_id`");
            }
            if (!$db->fieldExists('sale_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `sale_id` BIGINT UNSIGNED NULL AFTER `vendor_id`");
            }
            if (!$db->fieldExists('purchase_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `purchase_id` BIGINT UNSIGNED NULL AFTER `sale_id`");
            }
            if (!$db->fieldExists('type', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `type` ENUM('in', 'out') DEFAULT 'in' AFTER `payment_date`");
            }
            if (!$db->fieldExists('payment_method', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `payment_method` VARCHAR(50) DEFAULT 'Cash' AFTER `amount`");
            }
            if (!$db->fieldExists('payment_date', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `payment_date` DATE NOT NULL AFTER `payment_no`");
            }
            if ($db->fieldExists('party_type', 'payments') && $db->fieldExists('party_id', 'payments')) {
                try {
                    $db->query("UPDATE `payments` SET `customer_id` = `party_id`, `type` = 'in' WHERE `customer_id` IS NULL AND `party_type` = 'customer'");
                    $db->query("UPDATE `payments` SET `vendor_id` = `party_id`, `type` = 'out' WHERE `vendor_id` IS NULL AND `party_type` = 'vendor'");
                } catch (\Throwable $e) {}
            }
        }

        // Expenses table columns
        if ($db->tableExists('expenses')) {
            if (!$db->fieldExists('payment_method', 'expenses')) {
                if ($db->fieldExists('paid_by', 'expenses')) {
                    $db->query("ALTER TABLE `expenses` CHANGE `paid_by` `payment_method` VARCHAR(50) DEFAULT 'Cash'");
                } else {
                    $db->query("ALTER TABLE `expenses` ADD `payment_method` VARCHAR(50) DEFAULT 'Cash' AFTER `amount`");
                }
            }
            if ($db->fieldExists('expense_no', 'expenses')) {
                try {
                    $db->query("ALTER TABLE `expenses` MODIFY `expense_no` VARCHAR(50) NULL");
                } catch (\Throwable $e) {}
            }
        }

        // Try seeding if needed
        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\CoreSeeder');
            $seeder->call('App\Database\Seeds\Phase2Seeder');
        } catch (\Throwable $se) {
            // Already seeded
        }
    }
}
