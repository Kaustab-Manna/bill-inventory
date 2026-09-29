<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterDocumentsFileTypeLength extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('documents', [
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('documents', [
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
    }
}
