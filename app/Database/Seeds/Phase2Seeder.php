<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase2Seeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('categories')->countAllResults() > 0) {
            echo "Phase 2 data already seeded. Skipping.\n";
            return;
        }

        // 1. Seed Permissions
        $permissions = [
            // Products
            ['module' => 'products', 'action' => 'view', 'display_name' => 'View Products'],
            ['module' => 'products', 'action' => 'create', 'display_name' => 'Create Products'],
            ['module' => 'products', 'action' => 'edit', 'display_name' => 'Edit Products'],
            ['module' => 'products', 'action' => 'delete', 'display_name' => 'Delete Products'],
            
            // Categories
            ['module' => 'categories', 'action' => 'view', 'display_name' => 'View Categories'],
            ['module' => 'categories', 'action' => 'manage', 'display_name' => 'Manage Categories'],
            
            // Brands
            ['module' => 'brands', 'action' => 'view', 'display_name' => 'View Brands'],
            ['module' => 'brands', 'action' => 'manage', 'display_name' => 'Manage Brands'],
            
            // Units
            ['module' => 'units', 'action' => 'view', 'display_name' => 'View Units'],
            ['module' => 'units', 'action' => 'manage', 'display_name' => 'Manage Units'],
            
            // Taxes
            ['module' => 'taxes', 'action' => 'view', 'display_name' => 'View Taxes'],
            ['module' => 'taxes', 'action' => 'manage', 'display_name' => 'Manage Taxes'],
            
            // Warehouses
            ['module' => 'warehouses', 'action' => 'view', 'display_name' => 'View Warehouses'],
            ['module' => 'warehouses', 'action' => 'manage', 'display_name' => 'Manage Warehouses'],
            
            // Stock
            ['module' => 'stock', 'action' => 'view', 'display_name' => 'View Stock'],
            ['module' => 'stock', 'action' => 'manage', 'display_name' => 'Manage Stock'],
        ];

        foreach ($permissions as $perm) {
            $this->db->table('permissions')->ignore(true)->insert($perm);
        }

        // Assign all new permissions to Super Admin role
        $superAdmin = $this->db->table('roles')->where('name', 'super_admin')->get()->getRow();
        if ($superAdmin) {
            $allPerms = $this->db->table('permissions')->get()->getResultArray();
            $rolePerms = [];
            foreach ($allPerms as $perm) {
                $rolePerms[] = [
                    'role_id'       => $superAdmin->id,
                    'permission_id' => $perm['id'],
                ];
            }
            $this->db->table('role_permissions')->ignore(true)->insertBatch($rolePerms);
        }

        // 2. Default Taxes
        $taxes = [
            ['name' => 'GST 0%', 'rate' => 0.00, 'type' => 'exclusive'],
            ['name' => 'GST 5%', 'rate' => 5.00, 'type' => 'exclusive'],
            ['name' => 'GST 12%', 'rate' => 12.00, 'type' => 'exclusive'],
            ['name' => 'GST 18%', 'rate' => 18.00, 'type' => 'exclusive'],
            ['name' => 'GST 28%', 'rate' => 28.00, 'type' => 'exclusive'],
        ];
        $this->db->table('taxes')->ignore(true)->insertBatch($taxes);

        // 3. Default Units
        $units = [
            ['name' => 'Pieces', 'short_name' => 'Pcs'],
            ['name' => 'Kilograms', 'short_name' => 'Kg'],
            ['name' => 'Liters', 'short_name' => 'Ltr'],
            ['name' => 'Boxes', 'short_name' => 'Box'],
            ['name' => 'Meters', 'short_name' => 'Mtr'],
        ];
        $this->db->table('units')->ignore(true)->insertBatch($units);

        // 4. Default Warehouse
        $mainBranch = $this->db->table('branches')->where('is_main', 1)->get()->getRow();
        $this->db->table('warehouses')->ignore(true)->insert([
            'branch_id' => $mainBranch ? $mainBranch->id : null,
            'name'      => 'Main Warehouse',
            'code'      => 'WH-MAIN',
            'is_default'=> 1,
            'is_active' => 1,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);
        
        // 5. Basic Categories & Brands (Optional but helpful for testing)
        $this->db->table('categories')->ignore(true)->insertBatch([
            ['name' => 'General', 'slug' => 'general', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Electronics', 'slug' => 'electronics', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Groceries', 'slug' => 'groceries', 'created_at' => date('Y-m-d H:i:s')],
        ]);

        $this->db->table('brands')->ignore(true)->insertBatch([
            ['name' => 'Generic', 'slug' => 'generic', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Sony', 'slug' => 'sony', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Samsung', 'slug' => 'samsung', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }
}
