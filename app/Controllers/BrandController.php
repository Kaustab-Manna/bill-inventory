<?php

namespace App\Controllers;

use App\Models\BrandModel;
use App\Models\AuditLogModel;

class BrandController extends BaseController
{
    protected $brandModel;
    protected $auditModel;

    public function __construct()
    {
        $this->brandModel = new BrandModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Brands',
            'brands'    => $this->brandModel->findAll()
        ];
        return view('products/brands/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[150]',
            'slug' => 'required|alpha_dash|is_unique[brands.slug]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'slug'      => strtolower($this->request->getPost('slug')),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $id = $this->brandModel->insert($data);
        $this->auditModel->logAction('create', 'brands', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Brand created successfully.']);
    }

    public function edit($id)
    {
        $brand = $this->brandModel->find($id);
        if (!$brand) return $this->response->setJSON(['success' => false, 'message' => 'Brand not found.']);
        return $this->response->setJSON(['success' => true, 'brand' => $brand]);
    }

    public function update($id)
    {
        $brand = $this->brandModel->find($id);
        if (!$brand) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $rules = [
            'name' => 'required|max_length[150]',
            'slug' => "required|alpha_dash|is_unique[brands.slug,id,{$id}]|max_length[150]",
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'slug'      => strtolower($this->request->getPost('slug')),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $this->brandModel->update($id, $data);
        $this->auditModel->logAction('update', 'brands', $id, (array)$brand, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Brand updated successfully.']);
    }

    public function delete($id)
    {
        $brand = $this->brandModel->find($id);
        if (!$brand) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $this->brandModel->delete($id);
        $this->auditModel->logAction('delete', 'brands', $id, (array)$brand);

        return $this->response->setJSON(['success' => true, 'message' => 'Brand deleted successfully.']);
    }
}
