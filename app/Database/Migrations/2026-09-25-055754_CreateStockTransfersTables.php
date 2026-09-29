<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockTransfersTables extends Migration
{
    public function up()
    {
        // 1. stock_transfers
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'reference_no'      => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'from_warehouse_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'to_warehouse_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'completed', 'rejected'], 'default' => 'pending'],
            'transfer_date'     => ['type' => 'DATE'],
            'notes'             => ['type' => 'TEXT', 'null' => true],
            'created_by'        => ['type' => 'BIGINT', 'unsigned' => true],
            'approved_by'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('from_warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('to_warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('stock_transfers', true);

        // 2. stock_transfer_items
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'stock_transfer_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'        => ['type' => 'BIGINT', 'unsigned' => true],
            'batch_id'          => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'quantity'          => ['type' => 'DECIMAL', 'constraint' => '15,3'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('stock_transfer_id', 'stock_transfers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('batch_id', 'batches', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('stock_transfer_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_transfer_items', true);
        $this->forge->dropTable('stock_transfers', true);
    }
}
