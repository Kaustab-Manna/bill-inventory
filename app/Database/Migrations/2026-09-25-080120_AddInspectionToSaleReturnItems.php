<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInspectionToSaleReturnItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sale_return_items', [
            'item_condition' => [
                'type' => 'ENUM',
                'constraint' => ['sealed', 'broken_seal'],
                'default' => 'sealed',
                'after' => 'product_id'
            ],
            'inspection_status' => [
                'type' => 'ENUM',
                'constraint' => ['na', 'pending', 'passed', 'failed'],
                'default' => 'na',
                'after' => 'item_condition'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_return_items', 'item_condition');
        $this->forge->dropColumn('sale_return_items', 'inspection_status');
    }
}
