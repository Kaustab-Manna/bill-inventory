<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogController extends BaseController
{
    public function index()
    {
        // Only Super Admin can view audit logs
        if (session()->get('role_id') != 1) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $auditLogModel = new AuditLogModel();
        
        $data = [
            'pageTitle' => 'Audit Trail Logs',
            'logs' => $auditLogModel->getRecent(300) // Fetch latest 300 logs
        ];

        return view('audit/index', $data);
    }
}
