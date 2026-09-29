<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['user_id', 'action', 'module', 'record_id', 'old_data', 'new_data', 'ip_address', 'user_agent', 'created_at'];
    protected $useTimestamps    = false;

    /**
     * Log an audit event
     */
    public function logAction(string $action, string $module, ?int $recordId = null, $oldData = null, $newData = null): void
    {
        $request = \Config\Services::request();
        $session = session();

        $this->insert([
            'user_id'    => $session->get('user_id'),
            'action'     => $action,
            'module'     => $module,
            'record_id'  => $recordId,
            'old_data'   => $oldData ? json_encode($oldData) : null,
            'new_data'   => $newData ? json_encode($newData) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent logs with user names
     */
    public function getRecent(int $limit = 50)
    {
        return $this->select('audit_logs.*, users.name as user_name')
                    ->join('users', 'users.id = audit_logs.user_id', 'left')
                    ->orderBy('audit_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by module
     */
    public function getByModule(string $module, int $limit = 50)
    {
        return $this->select('audit_logs.*, users.name as user_name')
                    ->join('users', 'users.id = audit_logs.user_id', 'left')
                    ->where('audit_logs.module', $module)
                    ->orderBy('audit_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
