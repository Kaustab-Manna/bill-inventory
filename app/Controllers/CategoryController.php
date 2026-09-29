<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\AuditLogModel;

class CategoryController extends BaseController
{
    protected $categoryModel;
    protected $auditModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle'  => 'Categories',
            'categories' => $this->categoryModel->getCategoriesTree(),
            'flatCategories' => $this->categoryModel->findAll()
        ];
        return view('products/categories/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[150]',
            'slug' => 'required|alpha_dash|is_unique[categories.slug]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => strtolower($this->request->getPost('slug')),
            'parent_id'   => $this->request->getPost('parent_id') ?: null,
            'description' => $this->request->getPost('description'),
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
            'is_active'   => $this->request->getPost('is_active') ?? 1,
        ];

        $id = $this->categoryModel->insert($data);
        $this->auditModel->logAction('create', 'categories', $id, null, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Category created successfully.']);
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) return $this->response->setJSON(['success' => false, 'message' => 'Category not found.']);
        return $this->response->setJSON(['success' => true, 'category' => $category]);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $rules = [
            'name' => 'required|max_length[150]',
            'slug' => "required|alpha_dash|is_unique[categories.slug,id,{$id}]|max_length[150]",
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => strtolower($this->request->getPost('slug')),
            'parent_id'   => $this->request->getPost('parent_id') ?: null,
            'description' => $this->request->getPost('description'),
            'sort_order'  => $this->request->getPost('sort_order') ?? 0,
            'is_active'   => $this->request->getPost('is_active') ?? 1,
        ];

        $this->categoryModel->update($id, $data);
        $this->auditModel->logAction('update', 'categories', $id, (array)$category, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Category updated successfully.']);
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $this->categoryModel->delete($id);
        $this->auditModel->logAction('delete', 'categories', $id, (array)$category);

        return $this->response->setJSON(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}
