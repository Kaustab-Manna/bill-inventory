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

        // Ensure all database tables exist automatically on any deployment
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('purchases')) {
                $runner = \Config\Services::migrations();
                $runner->setNamespace(null);
                $runner->latest();

                $seeder = \Config\Database::seeder();
                try {
                    $seeder->call('App\Database\Seeds\CoreSeeder');
                    $seeder->call('App\Database\Seeds\Phase2Seeder');
                } catch (\Throwable $se) {
                    // Ignore if already seeded
                }
            }
        } catch (\Throwable $me) {
            log_message('error', 'Auto-migration notice: ' . $me->getMessage());
        }

        $session = \Config\Services::session();
        
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
}
