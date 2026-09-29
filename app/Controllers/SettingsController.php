<?php

namespace App\Controllers;

use App\Models\CompanySettingModel;
use App\Models\SettingModel;
use App\Models\AuditLogModel;

class SettingsController extends BaseController
{
    protected $companyModel;
    protected $settingModel;
    protected $auditModel;

    public function __construct()
    {
        $this->companyModel = new CompanySettingModel();
        $this->settingModel = new SettingModel();
        $this->auditModel   = new AuditLogModel();
    }

    /**
     * Company profile page
     */
    public function company()
    {
        $data = [
            'pageTitle' => 'Company Settings',
            'company'   => $this->companyModel->getSettings(),
        ];
        return view('settings/company', $data);
    }

    /**
     * Update company settings
     */
    public function updateCompany()
    {
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[150]',
            'email'        => 'permit_empty|valid_email',
            'phone'        => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $old = (array)$this->companyModel->getSettings();

        $companyData = [
            'company_name'         => $this->request->getPost('company_name'),
            'address'              => $this->request->getPost('address'),
            'city'                 => $this->request->getPost('city'),
            'state'                => $this->request->getPost('state'),
            'country'              => $this->request->getPost('country'),
            'pincode'              => $this->request->getPost('pincode'),
            'phone'                => $this->request->getPost('phone'),
            'email'                => $this->request->getPost('email'),
            'gst_number'           => $this->request->getPost('gst_number'),
            'pan_number'           => $this->request->getPost('pan_number'),
            'financial_year_start' => $this->request->getPost('financial_year_start'),
            'financial_year_end'   => $this->request->getPost('financial_year_end'),
            'currency'             => $this->request->getPost('currency'),
            'currency_symbol'      => $this->request->getPost('currency_symbol'),
            'timezone'             => $this->request->getPost('timezone'),
            'invoice_prefix'       => $this->request->getPost('invoice_prefix'),
            'invoice_footer'       => $this->request->getPost('invoice_footer'),
        ];

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = 'company_logo_' . time() . '.' . $logo->getExtension();
            $logo->move(FCPATH . 'uploads/logos', $newName);
            $companyData['logo'] = 'uploads/logos/' . $newName;
        }

        $this->companyModel->update(1, $companyData);
        $this->auditModel->logAction('update', 'company', 1, $old, $companyData);

        return redirect()->to('/settings/company')->with('success', 'Company settings updated successfully.');
    }

    /**
     * General settings page
     */
    public function general()
    {
        $data = [
            'pageTitle'        => 'General Settings',
            'invoiceSettings'  => $this->settingModel->getGroup('invoice'),
            'taxSettings'      => $this->settingModel->getGroup('tax'),
            'paymentSettings'  => $this->settingModel->getGroup('payment'),
            'notifSettings'    => $this->settingModel->getGroup('notification'),
            'backupSettings'   => $this->settingModel->getGroup('backup'),
        ];
        return view('settings/general', $data);
    }

    /**
     * Update general settings
     */
    public function updateGeneral()
    {
        $settings = $this->request->getPost('settings');
        if (is_array($settings)) {
            foreach ($settings as $group => $items) {
                foreach ($items as $key => $value) {
                    $this->settingModel->setSetting($group, $key, $value);
                }
            }
        }

        $this->auditModel->logAction('update', 'settings', null, null, $settings);

        return redirect()->to('/settings/general')->with('success', 'Settings updated successfully.');
    }
}
