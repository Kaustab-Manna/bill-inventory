<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsTables extends Migration
{
    public function up()
    {
        // Quotations Table
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'quotation_no'   => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'customer_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'warehouse_id'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'discount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'accepted', 'rejected'], 'default' => 'pending'],
            'quotation_date' => ['type' => 'DATE'],
            'expiry_date'    => ['type' => 'DATE', 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_by'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('quotations', true);

        // Quotation Items Table
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'quotation_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'     => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit_price'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('quotation_id', 'quotations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quotation_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('quotation_items', true);
        $this->forge->dropTable('quotations', true);
    }
}
