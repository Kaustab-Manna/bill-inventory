<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        if (!$session->get('is_active')) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Your account has been deactivated.');
        }

        // Super Admin has full unrestricted access
        if ($session->get('role_name') === 'super_admin') {
            return;
        }

        // Check explicit permission if arguments provided (e.g., 'products.view')
        if ($arguments) {
            $permissions = $session->get('permissions') ?? [];
            foreach ($arguments as $permission) {
                if (!isset($permissions[$permission])) {
                    return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that resource.');
                }
            }
            return;
        }

        // Automatic module-level enforcement based on the current URI
        $uri = service('uri');
        $segment = $uri->getSegment(1);

        $moduleMap = [
            'users'             => 'users',
            'roles'             => 'roles',
            'branches'          => 'branches',
            'settings'          => 'settings',
            'categories'        => 'categories',
            'brands'            => 'brands',
            'units'             => 'units',
            'taxes'             => 'taxes',
            'products'          => 'products',
            'warehouses'        => 'warehouses',
            'stock'             => 'stock',
            'stock-transfers'   => 'stock_transfers',
            'stock-adjustments' => 'stock_adjustments',
            'customers'         => 'customers',
            'vendors'           => 'suppliers',
            'quotations'        => 'quotations',
            'sales'             => 'sales',
            'sales-returns'     => 'sales_returns',
            'inspections'       => 'sales_returns',
            'purchase-orders'   => 'purchase_orders',
            'purchases'         => 'purchases',
            'purchase-returns'  => 'purchase_returns',
            'pos'               => 'pos',
            'payments'          => 'payments',
            'receivables'       => 'receivables',
            'payables'          => 'payables',
            'expenses'          => 'expenses',
            'reports'           => 'reports',
            'audit-logs'        => 'audit_logs',
            'backup'            => 'backups',
            'documents'         => 'documents',
        ];

        if ($segment && isset($moduleMap[$segment])) {
            $moduleKey = $moduleMap[$segment];
            if (!has_module_access($moduleKey)) {
                $friendlyName = ucwords(str_replace('_', ' ', $moduleKey));
                if ($request->isAJAX()) {
                    $response = service('response');
                    return $response->setStatusCode(403)->setJSON([
                        'success' => false,
                        'message' => "Permission denied: Your role does not have access to the {$friendlyName} module."
                    ]);
                }
                return redirect()->to('/dashboard')->with('error', "Access restricted: Your role does not have access to the {$friendlyName} module.");
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
