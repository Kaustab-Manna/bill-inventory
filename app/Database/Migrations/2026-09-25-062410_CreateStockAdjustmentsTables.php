<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockAdjustmentsTables extends Migration
{
    public function up()
    {
        // 1. stock_adjustments
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'reference_no'    => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'warehouse_id'    => ['type' => 'BIGINT', 'unsigned' => true],
            'type'            => ['type' => 'ENUM', 'constraint' => ['addition', 'subtraction']],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'completed', 'rejected'], 'default' => 'pending'],
            'adjustment_date' => ['type' => 'DATE'],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_by'      => ['type' => 'BIGINT', 'unsigned' => true],
            'approved_by'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('stock_adjustments', true);

        // 2. stock_adjustment_items
        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'stock_adjustment_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'          => ['type' => 'BIGINT', 'unsigned' => true],
            'batch_id'            => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'quantity'            => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('stock_adjustment_id', 'stock_adjustments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('batch_id', 'batches', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('stock_adjustment_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_adjustment_items', true);
        $this->forge->dropTable('stock_adjustments', true);
    }
}
