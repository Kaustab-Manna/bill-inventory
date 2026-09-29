<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'contact_person' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'address'        => ['type' => 'TEXT', 'null' => true],
            'tax_number'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('vendors', true);
    }

    public function down()
    {
        $this->forge->dropTable('vendors', true);
    }
}
