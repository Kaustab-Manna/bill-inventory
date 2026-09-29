<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCustomers extends Migration
{
    public function up()
    {
        $fields = [
            'gst_no'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'email'],
            'city'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'address'],
            'state'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'city'],
            'pincode'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'state'],
            'credit_limit' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0, 'after' => 'pincode'],
        ];
        $this->forge->addColumn('customers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('customers', ['gst_no', 'city', 'state', 'pincode', 'credit_limit']);
    }
}
