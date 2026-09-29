<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'entity_type', 
        'entity_id', 
        'title', 
        'filename', 
        'file_type', 
        'file_size', 
        'uploaded_by', 
        'created_at'
    ];

    protected $useTimestamps = false;
}
