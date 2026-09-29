<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\UnitModel;
use App\Models\TaxModel;
use App\Models\AuditLogModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $auditModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->auditModel   = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Products',
            'products'  => $this->productModel->getAllWithDetails()
        ];
        return view('products/index', $data);
    }

    public function create()
    {
        $categoryModel = new CategoryModel();
        $brandModel    = new BrandModel();
        $unitModel     = new UnitModel();
        $taxModel      = new TaxModel();

        $data = [
            'pageTitle'  => 'Create Product',
            'categories' => $categoryModel->where('is_active', 1)->findAll(),
            'brands'     => $brandModel->where('is_active', 1)->findAll(),
            'units'      => $unitModel->where('is_active', 1)->findAll(),
            'taxes'      => $taxModel->where('is_active', 1)->findAll(),
        ];
        return view('products/form', $data);
    }

    public function store()
    {
        $rules = [
            'name'    => 'required|max_length[255]',
            'slug'    => 'required|alpha_dash|is_unique[products.slug]|max_length[255]',
            'sku'     => 'required|is_unique[products.sku]|max_length[50]',
            'unit_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'            => $this->request->getPost('name'),
            'slug'            => strtolower($this->request->getPost('slug')),
            'sku'             => strtoupper($this->request->getPost('sku')),
            'barcode'         => $this->request->getPost('barcode') ?: null,
            'category_id'     => $this->request->getPost('category_id') ?: null,
            'brand_id'        => $this->request->getPost('brand_id') ?: null,
            'unit_id'         => $this->request->getPost('unit_id'),
            'tax_id'          => $this->request->getPost('tax_id') ?: null,
            'description'     => $this->request->getPost('description'),
            'purchase_price'  => max(0, (float)($this->request->getPost('purchase_price') ?: 0)),
            'selling_price'   => max(0, (float)($this->request->getPost('selling_price') ?: 0)),
            'min_stock_level' => max(0, (float)($this->request->getPost('min_stock_level') ?: 0)),
            'reorder_qty'     => max(0, (float)($this->request->getPost('reorder_qty') ?: 0)),
            'hsn_code'        => $this->request->getPost('hsn_code'),
            'is_active'       => $this->request->getPost('is_active') ?? 1,
        ];

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = 'product_' . time() . '.' . $image->getExtension();
            $image->move(FCPATH . 'uploads/products', $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $id = $this->productModel->insert($data);
        $this->auditModel->logAction('create', 'products', $id, null, $data);

        return redirect()->to('/products')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) return redirect()->to('/products')->with('error', 'Product not found.');

        $categoryModel = new CategoryModel();
        $brandModel    = new BrandModel();
        $unitModel     = new UnitModel();
        $taxModel      = new TaxModel();

        $data = [
            'pageTitle'  => 'Edit Product',
            'product'    => $product,
            'categories' => $categoryModel->where('is_active', 1)->findAll(),
            'brands'     => $brandModel->where('is_active', 1)->findAll(),
            'units'      => $unitModel->where('is_active', 1)->findAll(),
            'taxes'      => $taxModel->where('is_active', 1)->findAll(),
        ];
        return view('products/form', $data);
    }

    public function update($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) return redirect()->to('/products')->with('error', 'Product not found.');

        $rules = [
            'name'    => 'required|max_length[255]',
            'slug'    => "required|alpha_dash|is_unique[products.slug,id,{$id}]|max_length[255]",
            'sku'     => "required|is_unique[products.sku,id,{$id}]|max_length[50]",
            'unit_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'            => $this->request->getPost('name'),
            'slug'            => strtolower($this->request->getPost('slug')),
            'sku'             => strtoupper($this->request->getPost('sku')),
            'barcode'         => $this->request->getPost('barcode') ?: null,
            'category_id'     => $this->request->getPost('category_id') ?: null,
            'brand_id'        => $this->request->getPost('brand_id') ?: null,
            'unit_id'         => $this->request->getPost('unit_id'),
            'tax_id'          => $this->request->getPost('tax_id') ?: null,
            'description'     => $this->request->getPost('description'),
            'purchase_price'  => max(0, (float)($this->request->getPost('purchase_price') ?: 0)),
            'selling_price'   => max(0, (float)($this->request->getPost('selling_price') ?: 0)),
            'min_stock_level' => max(0, (float)($this->request->getPost('min_stock_level') ?: 0)),
            'reorder_qty'     => max(0, (float)($this->request->getPost('reorder_qty') ?: 0)),
            'hsn_code'        => $this->request->getPost('hsn_code'),
            'is_active'       => $this->request->getPost('is_active') ?? 1,
        ];

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = 'product_' . time() . '.' . $image->getExtension();
            $image->move(FCPATH . 'uploads/products', $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->update($id, $data);
        $this->auditModel->logAction('update', 'products', $id, (array)$product, $data);

        return redirect()->to('/products')->with('success', 'Product updated successfully.');
    }

    public function delete($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $this->productModel->delete($id);
        $this->auditModel->logAction('delete', 'products', $id, (array)$product);

        return $this->response->setJSON(['success' => true, 'message' => 'Product deleted successfully.']);
    }
}
