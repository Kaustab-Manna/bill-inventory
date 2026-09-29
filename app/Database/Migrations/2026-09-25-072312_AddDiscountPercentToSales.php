<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountPercentToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'discount_percent' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'after' => 'tax_amount'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sales', 'discount_percent');
    }
}
