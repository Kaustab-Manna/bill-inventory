<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrders extends Migration
{
    public function up()
    {
        // Purchase Orders Table
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'po_no'            => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'vendor_id'        => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id'     => ['type' => 'BIGINT', 'unsigned' => true],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'discount_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'discount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft', 'sent', 'approved', 'completed', 'cancelled'], 'default' => 'draft'],
            'expected_date'    => ['type' => 'DATE', 'null' => true],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_by'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchase_orders', true);

        // Purchase Order Items Table
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'po_id'            => ['type' => 'BIGINT', 'unsigned' => true],
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
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchase_order_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order_items', true);
        $this->forge->dropTable('purchase_orders', true);
    }
}
