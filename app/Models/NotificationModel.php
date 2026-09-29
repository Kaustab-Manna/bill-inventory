<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['type', 'title', 'message', 'is_read', 'created_at'];

    // Dates
    protected $useTimestamps = false; // We manage created_at manually if needed or set it true, but let's set true and adapt
    
    public function getUnreadCount()
    {
        return $this->where('is_read', 0)->countAllResults();
    }
    
    public function getLatestUnread($limit = 5)
    {
        return $this->where('is_read', 0)->orderBy('created_at', 'DESC')->findAll($limit);
    }
}
