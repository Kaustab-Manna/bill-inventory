<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchases extends Migration
{
    public function up()
    {
        // Purchases Table
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_no'       => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'vendor_id'        => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id'     => ['type' => 'BIGINT', 'unsigned' => true],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'discount_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'discount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'paid_amount'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_status'   => ['type' => 'ENUM', 'constraint' => ['unpaid', 'partial', 'paid'], 'default' => 'unpaid'],
            'purchase_date'    => ['type' => 'DATETIME'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_by'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchases', true);

        // Purchase Items Table
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'purchase_id'      => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'       => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'         => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit_price'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'            => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_id', 'purchases', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchase_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_items', true);
        $this->forge->dropTable('purchases', true);
    }
}
