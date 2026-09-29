<?php

namespace App\Controllers;

use App\Models\TaxModel;
use App\Models\AuditLogModel;

class TaxController extends BaseController
{
    protected $taxModel;
    protected $auditModel;

    public function __construct()
    {
        $this->taxModel   = new TaxModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Taxes',
            'taxes'     => $this->taxModel->findAll()
        ];
        return view('products/taxes/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[100]',
            'rate' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'rate'      => $this->request->getPost('rate'),
            'type'      => $this->request->getPost('type') ?? 'exclusive',
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $id = $this->taxModel->insert($data);
        $this->auditModel->logAction('create', 'taxes', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Tax created successfully.']);
    }

    public function edit($id)
    {
        $tax = $this->taxModel->find($id);
        if (!$tax) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        return $this->response->setJSON(['success' => true, 'tax' => $tax]);
    }

    public function update($id)
    {
        $tax = $this->taxModel->find($id);
        if (!$tax) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $rules = [
            'name' => 'required|max_length[100]',
            'rate' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'rate'      => $this->request->getPost('rate'),
            'type'      => $this->request->getPost('type') ?? 'exclusive',
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $this->taxModel->update($id, $data);
        $this->auditModel->logAction('update', 'taxes', $id, (array)$tax, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Tax updated successfully.']);
    }

    public function delete($id)
    {
        $tax = $this->taxModel->find($id);
        if (!$tax) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $this->taxModel->delete($id);
        $this->auditModel->logAction('delete', 'taxes', $id, (array)$tax);

        return $this->response->setJSON(['success' => true, 'message' => 'Tax deleted successfully.']);
    }
}
