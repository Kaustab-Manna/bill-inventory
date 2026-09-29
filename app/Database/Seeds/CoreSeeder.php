<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoreSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('roles')->countAllResults() > 0) {
            echo "Core data already seeded. Skipping.\n";
            return;
        }

        // ==================== ROLES ====================
        $roles = [
            ['name' => 'super_admin',    'display_name' => 'Super Admin',         'description' => 'Full system access',         'is_system' => 1],
            ['name' => 'admin',          'display_name' => 'Admin',               'description' => 'Administrative access',      'is_system' => 1],
            ['name' => 'manager',        'display_name' => 'Manager',             'description' => 'Management access',          'is_system' => 0],
            ['name' => 'sales_exec',     'display_name' => 'Sales Executive',     'description' => 'Sales operations access',    'is_system' => 0],
            ['name' => 'purchase_exec',  'display_name' => 'Purchase Executive',  'description' => 'Purchase operations access', 'is_system' => 0],
            ['name' => 'accountant',     'display_name' => 'Accountant',          'description' => 'Financial access',           'is_system' => 0],
            ['name' => 'warehouse_staff','display_name' => 'Store / Warehouse Staff', 'description' => 'Warehouse operations',   'is_system' => 0],
            ['name' => 'cashier',        'display_name' => 'Cashier',             'description' => 'POS and billing access',     'is_system' => 0],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($roles as &$role) {
            $role['created_at'] = $now;
            $role['updated_at'] = $now;
        }
        $this->db->table('roles')->insertBatch($roles);

        // ==================== PERMISSIONS ====================
        $modules = [
            'dashboard'       => ['view'],
            'users'           => ['view', 'create', 'edit', 'delete'],
            'roles'           => ['view', 'create', 'edit', 'delete'],
            'branches'        => ['view', 'create', 'edit', 'delete'],
            'company'         => ['view', 'edit'],
            'settings'        => ['view', 'edit'],
            'products'        => ['view', 'create', 'edit', 'delete', 'export'],
            'categories'      => ['view', 'create', 'edit', 'delete'],
            'brands'          => ['view', 'create', 'edit', 'delete'],
            'units'           => ['view', 'create', 'edit', 'delete'],
            'barcodes'        => ['view', 'create', 'print'],
            'suppliers'       => ['view', 'create', 'edit', 'delete', 'export'],
            'customers'       => ['view', 'create', 'edit', 'delete', 'export'],
            'salespersons'    => ['view', 'create', 'edit', 'delete'],
            'purchase_orders' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
            'purchases'       => ['view', 'create', 'edit', 'delete', 'export'],
            'purchase_returns'=> ['view', 'create', 'edit', 'delete'],
            'quotations'      => ['view', 'create', 'edit', 'delete', 'export'],
            'sales'           => ['view', 'create', 'edit', 'delete', 'export'],
            'pos'             => ['view', 'create'],
            'sales_returns'   => ['view', 'create', 'edit', 'delete'],
            'stock'           => ['view', 'adjust', 'export'],
            'warehouses'      => ['view', 'create', 'edit', 'delete'],
            'stock_transfers' => ['view', 'create', 'approve'],
            'stock_adjustments' => ['view', 'create', 'approve'],
            'batches'         => ['view', 'create', 'edit'],
            'payments'        => ['view', 'create', 'edit', 'delete'],
            'receivables'     => ['view', 'collect'],
            'payables'        => ['view', 'pay'],
            'expenses'        => ['view', 'create', 'edit', 'delete', 'export'],
            'invoices'        => ['view', 'create', 'print', 'export'],
            'reports'         => ['view', 'export'],
            'notifications'   => ['view', 'manage'],
            'documents'       => ['view', 'upload', 'delete'],
            'audit_logs'      => ['view'],
            'backups'         => ['view', 'create', 'restore'],
        ];

        $permissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'module'       => $module,
                    'action'       => $action,
                    'display_name' => ucfirst($action) . ' ' . ucwords(str_replace('_', ' ', $module)),
                    'created_at'   => $now,
                ];
            }
        }
        $this->db->table('permissions')->insertBatch($permissions);

        // ==================== ASSIGN ALL PERMISSIONS TO SUPER ADMIN ====================
        $superAdminRole = $this->db->table('roles')->where('name', 'super_admin')->get()->getRow();
        $allPermissions = $this->db->table('permissions')->get()->getResult();

        $rolePermissions = [];
        foreach ($allPermissions as $perm) {
            $rolePermissions[] = [
                'role_id'       => $superAdminRole->id,
                'permission_id' => $perm->id,
            ];
        }
        $this->db->table('role_permissions')->insertBatch($rolePermissions);

        // ==================== ADMIN PERMISSIONS (most except system) ====================
        $adminRole = $this->db->table('roles')->where('name', 'admin')->get()->getRow();
        $adminPerms = $this->db->table('permissions')
            ->whereNotIn('module', ['audit_logs', 'backups'])
            ->get()->getResult();

        $adminRolePerms = [];
        foreach ($adminPerms as $perm) {
            $adminRolePerms[] = [
                'role_id'       => $adminRole->id,
                'permission_id' => $perm->id,
            ];
        }
        $this->db->table('role_permissions')->insertBatch($adminRolePerms);

        // ==================== DEFAULT BRANCH ====================
        $this->db->table('branches')->insert([
            'name'       => 'Main Branch',
            'code'       => 'MAIN',
            'address'    => '',
            'is_main'    => 1,
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ==================== DEFAULT SUPER ADMIN USER ====================
        $this->db->table('users')->insert([
            'branch_id'  => 1,
            'role_id'    => $superAdminRole->id,
            'name'       => 'Super Admin',
            'email'      => 'admin@billinventory.com',
            'phone'      => '9999999999',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ==================== DEFAULT COMPANY SETTINGS ====================
        $this->db->table('company_settings')->insert([
            'company_name'         => 'My Business',
            'address'              => '',
            'country'              => 'India',
            'currency'             => 'INR',
            'currency_symbol'      => '₹',
            'date_format'          => 'd-m-Y',
            'timezone'             => 'Asia/Kolkata',
            'financial_year_start' => date('Y') . '-04-01',
            'financial_year_end'   => (date('Y') + 1) . '-03-31',
            'created_at'           => $now,
            'updated_at'           => $now,
        ]);

        // ==================== DEFAULT SETTINGS ====================
        $defaultSettings = [
            ['group' => 'invoice', 'key' => 'prefix',           'value' => 'INV-',    'type' => 'string'],
            ['group' => 'invoice', 'key' => 'next_number',      'value' => '1',        'type' => 'integer'],
            ['group' => 'invoice', 'key' => 'terms',            'value' => 'Thank you for your business!', 'type' => 'string'],
            ['group' => 'invoice', 'key' => 'show_logo',        'value' => '1',        'type' => 'boolean'],
            ['group' => 'tax',     'key' => 'default_tax_type', 'value' => 'exclusive','type' => 'string'],
            ['group' => 'tax',     'key' => 'enable_gst',       'value' => '1',        'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'default_mode',     'value' => 'cash',     'type' => 'string'],
            ['group' => 'payment', 'key' => 'enable_upi',       'value' => '1',        'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'low_stock_alert',  'value' => '1',   'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'expiry_alert',     'value' => '1',   'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'payment_due_alert','value' => '1',   'type' => 'boolean'],
            ['group' => 'backup',  'key' => 'auto_backup',      'value' => '0',        'type' => 'boolean'],
            ['group' => 'backup',  'key' => 'backup_frequency',  'value' => 'weekly',  'type' => 'string'],
        ];

        foreach ($defaultSettings as &$s) {
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }
        $this->db->table('settings')->insertBatch($defaultSettings);
    }
}
