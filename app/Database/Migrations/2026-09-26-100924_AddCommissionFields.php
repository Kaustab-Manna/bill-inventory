<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionFields extends Migration
{
    public function up()
    {
        // Add commission rate to users table
        $this->forge->addColumn('users', [
            'commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'after' => 'is_active'
            ]
        ]);

        // Add salesperson tracking to sales table
        $this->forge->addColumn('sales', [
            'salesperson_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'customer_id'
            ],
            'commission_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'paid_amount'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'commission_rate');
        $this->forge->dropColumn('sales', 'salesperson_id, commission_amount');
    }
}
