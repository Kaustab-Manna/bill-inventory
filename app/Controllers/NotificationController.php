<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'All Notifications',
            'notifications' => $this->notificationModel->orderBy('created_at', 'DESC')->findAll(100) // Show last 100
        ];
        
        return view('notifications/index', $data);
    }

    public function markAllRead()
    {
        $this->notificationModel->where('is_read', 0)->set(['is_read' => 1])->update();
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    public function markRead($id)
    {
        $this->notificationModel->update($id, ['is_read' => 1]);
        return redirect()->back();
    }
}
