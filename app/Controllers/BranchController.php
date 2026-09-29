<?php

namespace App\Controllers;

use App\Models\BranchModel;
use App\Models\AuditLogModel;

class BranchController extends BaseController
{
    protected $branchModel;
    protected $auditModel;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
        $this->auditModel  = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Branch Management',
            'branches'  => $this->branchModel->getAllWithUserCount(),
        ];
        return view('branches/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|max_length[150]',
            'code'  => 'required|alpha_dash|is_unique[branches.code]|max_length[20]',
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
            'name'      => $this->request->getPost('name'),
            'code'      => strtoupper($this->request->getPost('code')),
            'address'   => $this->request->getPost('address'),
            'city'      => $this->request->getPost('city'),
            'state'     => $this->request->getPost('state'),
            'pincode'   => $this->request->getPost('pincode'),
            'phone'     => $this->request->getPost('phone'),
            'email'     => $this->request->getPost('email'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $id = $this->branchModel->insert($data);
        $this->auditModel->logAction('create', 'branches', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Branch created successfully.']);
    }

    public function edit($id)
    {
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Branch not found.']);
        }
        return $this->response->setJSON(['success' => true, 'branch' => $branch]);
    }

    public function update($id)
    {
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Branch not found.']);
        }

        $rules = [
            'name'  => 'required|max_length[150]',
            'code'  => "required|alpha_dash|is_unique[branches.code,id,{$id}]|max_length[20]",
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
            'name'      => $this->request->getPost('name'),
            'code'      => strtoupper($this->request->getPost('code')),
            'address'   => $this->request->getPost('address'),
            'city'      => $this->request->getPost('city'),
            'state'     => $this->request->getPost('state'),
            'pincode'   => $this->request->getPost('pincode'),
            'phone'     => $this->request->getPost('phone'),
            'email'     => $this->request->getPost('email'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $this->branchModel->update($id, $data);
        $this->auditModel->logAction('update', 'branches', $id, (array)$branch, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Branch updated successfully.']);
    }

    public function delete($id)
    {
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Branch not found.']);
        }

        if ($branch->is_main) {
            return $this->response->setJSON(['success' => false, 'message' => 'Main branch cannot be deleted.']);
        }

        $this->branchModel->delete($id);
        $this->auditModel->logAction('delete', 'branches', $id, (array)$branch);

        return $this->response->setJSON(['success' => true, 'message' => 'Branch deleted successfully.']);
    }
}
