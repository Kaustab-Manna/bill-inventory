<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleReturnsTables extends Migration
{
    public function up()
    {
        // Sale Returns Table
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'return_no'      => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'sale_id'        => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'customer_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'warehouse_id'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'completed'], 'default' => 'completed'],
            'return_date'    => ['type' => 'DATE'],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_by'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('sale_returns', true);

        // Sale Return Items Table
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'sale_return_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'     => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'       => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit_price'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_return_id', 'sale_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_return_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('sale_return_items', true);
        $this->forge->dropTable('sale_returns', true);
    }
}
