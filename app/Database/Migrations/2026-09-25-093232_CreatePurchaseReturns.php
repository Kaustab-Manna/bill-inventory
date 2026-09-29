<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseReturns extends Migration
{
    public function up()
    {
        // Purchase Returns Table
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'return_no'        => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'vendor_id'        => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id'     => ['type' => 'BIGINT', 'unsigned' => true],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'return_date'      => ['type' => 'DATETIME'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_by'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchase_returns', true);

        // Purchase Return Items Table
        $this->forge->addField([
            'id'                 => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'purchase_return_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id'         => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'           => ['type' => 'DECIMAL', 'constraint' => '15,3', 'default' => 0],
            'unit_price'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'subtotal'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_return_id', 'purchase_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('purchase_return_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('purchase_return_items', true);
        $this->forge->dropTable('purchase_returns', true);
    }
}
