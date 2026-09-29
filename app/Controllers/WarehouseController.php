<?php

namespace App\Controllers;

use App\Models\WarehouseModel;
use App\Models\BranchModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class WarehouseController extends BaseController
{
    protected $warehouseModel;
    protected $auditModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->auditModel     = new AuditLogModel();
    }

    public function index()
    {
        $branchModel = new BranchModel();
        $userModel   = new UserModel();

        $data = [
            'pageTitle'  => 'Warehouses',
            'warehouses' => $this->warehouseModel->getAllWithDetails(),
            'branches'   => $branchModel->where('is_active', 1)->findAll(),
            'users'      => $userModel->where('is_active', 1)->findAll(),
        ];
        return view('inventory/warehouses/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|max_length[150]',
            'code'  => 'required|alpha_dash|is_unique[warehouses.code]|max_length[20]',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'code'       => strtoupper($this->request->getPost('code')),
            'branch_id'  => $this->request->getPost('branch_id') ?: null,
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'address'    => $this->request->getPost('address'),
            'phone'      => $this->request->getPost('phone'),
            'is_default' => $this->request->getPost('is_default') ?? 0,
            'is_active'  => $this->request->getPost('is_active') ?? 1,
        ];

        // Ensure only one default warehouse
        if ($data['is_default']) {
            $this->warehouseModel->where('is_default', 1)->set(['is_default' => 0])->update();
        }

        $id = $this->warehouseModel->insert($data);
        $this->auditModel->logAction('create', 'warehouses', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Warehouse created successfully.']);
    }

    public function edit($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        return $this->response->setJSON(['success' => true, 'warehouse' => $warehouse]);
    }

    public function update($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $rules = [
            'name'  => 'required|max_length[150]',
            'code'  => "required|alpha_dash|is_unique[warehouses.code,id,{$id}]|max_length[20]",
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ];

        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'code'       => strtoupper($this->request->getPost('code')),
            'branch_id'  => $this->request->getPost('branch_id') ?: null,
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'address'    => $this->request->getPost('address'),
            'phone'      => $this->request->getPost('phone'),
            'is_default' => $this->request->getPost('is_default') ?? 0,
            'is_active'  => $this->request->getPost('is_active') ?? 1,
        ];

        if ($data['is_default']) {
            $this->warehouseModel->where('id !=', $id)->where('is_default', 1)->set(['is_default' => 0])->update();
        }

        $this->warehouseModel->update($id, $data);
        $this->auditModel->logAction('update', 'warehouses', $id, (array)$warehouse, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Warehouse updated successfully.']);
    }

    public function delete($id)
    {
        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        
        if ($warehouse->is_default) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete the default warehouse.']);
        }

        $this->warehouseModel->delete($id);
        $this->auditModel->logAction('delete', 'warehouses', $id, (array)$warehouse);

        return $this->response->setJSON(['success' => true, 'message' => 'Warehouse deleted successfully.']);
    }
}
