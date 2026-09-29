<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInvoiceSettingsToCompany extends Migration
{
    public function up()
    {
        $this->forge->addColumn('company_settings', [
            'invoice_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'INV-'
            ],
            'invoice_footer' => [
                'type' => 'TEXT',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('company_settings', 'invoice_prefix');
        $this->forge->dropColumn('company_settings', 'invoice_footer');
    }
}
