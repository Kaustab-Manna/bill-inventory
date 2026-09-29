<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosTables extends Migration
{
    public function up()
    {
        // 1. customers
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers', true);

        // 2. sales
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_no'     => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'customer_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'warehouse_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'discount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'paid_amount'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'cash'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['paid', 'partial', 'unpaid'], 'default' => 'paid'],
            'sale_date'      => ['type' => 'DATETIME'],
            'created_by'     => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('sales', true);

        // 3. sale_items
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'sale_id'    => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'   => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'unit_price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'subtotal'   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'      => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('sale_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('sale_items', true);
        $this->forge->dropTable('sales', true);
        $this->forge->dropTable('customers', true);
    }
}
