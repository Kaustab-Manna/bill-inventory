<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Files\File;

class BackupController extends BaseController
{
    public function database()
    {
        // Must be Super Admin
        if (session()->get('role_id') != 1) {
            return redirect()->back()->with('error', 'Unauthorized. Only Super Admin can backup database.');
        }

        $db = \Config\Database::connect();
        
        $tables = $db->listTables();
        $sql = "-- MallInventory Pro Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $query = $db->query("SHOW CREATE TABLE `$table`");
            $row = $query->getRowArray();
            $sql .= $row['Create Table'] . ";\n\n";

            $query = $db->query("SELECT * FROM `$table`");
            $result = $query->getResultArray();

            foreach ($result as $row) {
                $sql .= "INSERT INTO `$table` VALUES(";
                $values = [];
                foreach ($row as $value) {
                    if (is_null($value)) {
                        $values[] = "NULL";
                    } else {
                        $values[] = $db->escape($value);
                    }
                }
                $sql .= implode(', ', $values) . ");\n";
            }
            $sql .= "\n\n";
        }

        $filename = 'backup_mallinventory_' . date('Y-m-d_H-i-s') . '.sql';
        
        return $this->response->download($filename, $sql);
    }
}
