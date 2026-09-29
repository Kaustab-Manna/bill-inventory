<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'brand_id', 'unit_id', 'tax_id',
        'name', 'slug', 'sku', 'barcode', 'description',
        'purchase_price', 'selling_price', 'min_stock_level', 'reorder_qty',
        'image', 'hsn_code', 'is_active', 'custom_fields'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAllWithDetails()
    {
        return $this->select('products.*, categories.name as category_name, brands.name as brand_name, units.short_name as unit_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->join('brands', 'brands.id = products.brand_id', 'left')
                    ->join('units', 'units.id = products.unit_id', 'left')
                    ->orderBy('products.id', 'DESC')
                    ->findAll();
    }
}
