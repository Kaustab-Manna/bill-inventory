<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountPercentToQuotations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('quotations', [
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
        $this->forge->dropColumn('quotations', 'discount_percent');
    }
}
