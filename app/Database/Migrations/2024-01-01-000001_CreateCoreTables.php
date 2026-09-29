<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreTables extends Migration
{
    public function up()
    {
        // ==================== ROLES ====================
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'display_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'is_system'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('roles', true);

        // ==================== PERMISSIONS ====================
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'module'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'action'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'display_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('permissions', true);

        // ==================== ROLE_PERMISSIONS ====================
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'role_id'       => ['type' => 'BIGINT', 'unsigned' => true],
            'permission_id' => ['type' => 'BIGINT', 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_id', 'permission_id']);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions', true);

        // ==================== BRANCHES ====================
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'city'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'state'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pincode'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_main'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('branches', true);

        // ==================== USERS ====================
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'branch_id'         => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'role_id'           => ['type' => 'BIGINT', 'unsigned' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'avatar'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'two_factor_secret' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login'        => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('users', true);

        // ==================== COMPANY_SETTINGS ====================
        $this->forge->addField([
            'id'                   => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'company_name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'address'              => ['type' => 'TEXT', 'null' => true],
            'city'                 => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'state'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country'              => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'India'],
            'pincode'              => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'phone'                => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'gst_number'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'pan_number'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'logo'                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'financial_year_start' => ['type' => 'DATE', 'null' => true],
            'financial_year_end'   => ['type' => 'DATE', 'null' => true],
            'currency'             => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'INR'],
            'currency_symbol'      => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => '₹'],
            'date_format'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'd-m-Y'],
            'timezone'             => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Asia/Kolkata'],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('company_settings', true);

        // ==================== SETTINGS ====================
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'group'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'string'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['group', 'key']);
        $this->forge->createTable('settings', true);

        // ==================== AUDIT_LOGS ====================
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'module'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'record_id'  => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'old_data'   => ['type' => 'JSON', 'null' => true],
            'new_data'   => ['type' => 'JSON', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('module');
        $this->forge->addKey('created_at');
        $this->forge->createTable('audit_logs', true);

        // ==================== CI_SESSIONS ====================
        $this->forge->addField([
            'id'         => ['type' => 'VARCHAR', 'constraint' => 128],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'timestamp'  => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'data'       => ['type' => 'TEXT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('ci_sessions', true);
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('company_settings', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('branches', true);
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('permissions', true);
        $this->forge->dropTable('roles', true);
    }
}
