<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanySettingModel extends Model
{
    protected $table            = 'company_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'company_name', 'address', 'city', 'state', 'country', 'pincode',
        'phone', 'email', 'website', 'gst_number', 'pan_number', 'logo',
        'financial_year_start', 'financial_year_end', 'currency', 'currency_symbol',
        'date_format', 'timezone', 'invoice_prefix', 'invoice_footer'
    ];
    protected $useTimestamps = true;

    /**
     * Get company settings (singleton - always ID 1)
     */
    public function getSettings()
    {
        return $this->find(1);
    }
}
