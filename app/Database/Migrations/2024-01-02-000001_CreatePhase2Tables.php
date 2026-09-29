<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase2Tables extends Migration
{
    public function up()
    {
        // 1. categories
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'parent_id'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('categories', true);

        // 2. brands
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'logo'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('brands', true);

        // 3. units
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'short_name'  => ['type' => 'VARCHAR', 'constraint' => 10],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('units', true);

        // 4. unit_conversions
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'from_unit_id'      => ['type' => 'BIGINT', 'unsigned' => true],
            'to_unit_id'        => ['type' => 'BIGINT', 'unsigned' => true],
            'conversion_factor' => ['type' => 'DECIMAL', 'constraint' => '15,6'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('from_unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('to_unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('unit_conversions', true);

        // 5. taxes
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'rate'        => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'type'        => ['type' => 'ENUM', 'constraint' => ['inclusive', 'exclusive'], 'default' => 'exclusive'],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('taxes', true);

        // 6. products
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'category_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'brand_id'        => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'unit_id'         => ['type' => 'BIGINT', 'unsigned' => true],
            'tax_id'          => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'            => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'sku'             => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'barcode'         => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true, 'null' => true],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'purchase_price'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'selling_price'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'min_stock_level' => ['type' => 'INT', 'default' => 0],
            'reorder_qty'     => ['type' => 'INT', 'default' => 0],
            'image'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'hsn_code'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'is_active'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'custom_fields'   => ['type' => 'JSON', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('brand_id', 'brands', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('tax_id', 'taxes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('products', true);

        // 7. product_images
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_images', true);

        // 8. warehouses
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'branch_id'  => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'manager_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('manager_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('warehouses', true);

        // 9. stock
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'     => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'reserved_qty' => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'warehouse_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock', true);

        // 10. batches
        $this->forge->addField([
            'id'                 => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'         => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id'       => ['type' => 'BIGINT', 'unsigned' => true],
            'batch_number'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'manufacturing_date' => ['type' => 'DATE', 'null' => true],
            'expiry_date'        => ['type' => 'DATE', 'null' => true],
            'quantity'           => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'purchase_price'     => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'selling_price'      => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'is_active'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('batches', true);

        // 11. stock_movements
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'     => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'batch_id'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'type'           => ['type' => 'ENUM', 'constraint' => ['in', 'out', 'transfer', 'adjustment']],
            'reference_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'reference_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'       => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'before_qty'     => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'after_qty'      => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_by'     => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('batch_id', 'batches', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('stock_movements', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements', true);
        $this->forge->dropTable('batches', true);
        $this->forge->dropTable('stock', true);
        $this->forge->dropTable('warehouses', true);
        $this->forge->dropTable('product_images', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('taxes', true);
        $this->forge->dropTable('unit_conversions', true);
        $this->forge->dropTable('units', true);
        $this->forge->dropTable('brands', true);
        $this->forge->dropTable('categories', true);
    }
}
