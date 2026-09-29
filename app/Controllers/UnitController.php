<?php

namespace App\Controllers;

use App\Models\UnitModel;
use App\Models\AuditLogModel;

class UnitController extends BaseController
{
    protected $unitModel;
    protected $auditModel;

    public function __construct()
    {
        $this->unitModel  = new UnitModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Units of Measurement',
            'units'     => $this->unitModel->findAll()
        ];
        return view('products/units/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'       => 'required|max_length[50]',
            'short_name' => 'required|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'short_name' => $this->request->getPost('short_name'),
            'is_active'  => $this->request->getPost('is_active') ?? 1,
        ];

        $id = $this->unitModel->insert($data);
        $this->auditModel->logAction('create', 'units', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Unit created successfully.']);
    }

    public function edit($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        return $this->response->setJSON(['success' => true, 'unit' => $unit]);
    }

    public function update($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $rules = [
            'name'       => 'required|max_length[50]',
            'short_name' => 'required|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'short_name' => $this->request->getPost('short_name'),
            'is_active'  => $this->request->getPost('is_active') ?? 1,
        ];

        $this->unitModel->update($id, $data);
        $this->auditModel->logAction('update', 'units', $id, (array)$unit, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Unit updated successfully.']);
    }

    public function delete($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $this->unitModel->delete($id);
        $this->auditModel->logAction('delete', 'units', $id, (array)$unit);

        return $this->response->setJSON(['success' => true, 'message' => 'Unit deleted successfully.']);
    }
}
