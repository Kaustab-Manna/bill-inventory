<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInspectionToSaleReturnItems extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('sale_return_items');
        if (!in_array('item_condition', $fields)) {
            $this->forge->addColumn('sale_return_items', [
                'item_condition' => [
                    'type' => 'ENUM',
                    'constraint' => ['sealed', 'broken_seal'],
                    'default' => 'sealed',
                    'after' => 'product_id'
                ]
            ]);
        }
        if (!in_array('inspection_status', $fields)) {
            $this->forge->addColumn('sale_return_items', [
                'inspection_status' => [
                    'type' => 'ENUM',
                    'constraint' => ['na', 'pending', 'passed', 'failed'],
                    'default' => 'na',
                    'after' => 'item_condition'
                ]
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('sale_return_items', 'item_condition');
        $this->forge->dropColumn('sale_return_items', 'inspection_status');
    }
}
