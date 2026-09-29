<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-box" style="color:var(--info);margin-right:8px;"></i> Products</h1>
        <p>Manage inventory items and products</p>
    </div>
    <a href="<?= base_url('products/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

<div class="toolbar">
    <div class="toolbar-search">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="Search products..." id="productSearch">
    </div>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table" id="productsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock Alert</th>
                    <th>Status</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td>
                        <div class="d-flex items-center gap-12">
                            <?php if ($prod->image): ?>
                                <img src="<?= base_url($prod->image) ?>" alt="Product" style="width:40px;height:40px;border-radius:4px;object-fit:cover;">
                            <?php else: ?>
                                <div class="kpi-icon info" style="width:40px;height:40px;border-radius:4px;"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight:600;color:var(--text-primary);"><?= esc($prod->name) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">SKU: <?= esc($prod->sku) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= esc($prod->category_name ?? '-') ?></td>
                    <td><?= esc($prod->brand_name ?? '-') ?></td>
                    <td>
                        <div style="font-weight:600;"><?= number_format($prod->selling_price, 2) ?></div>
                        <div style="font-size:0.7rem;color:var(--text-muted);">Pur: <?= number_format($prod->purchase_price, 2) ?></div>
                    </td>
                    <td><span class="badge badge-warning">Min: <?= $prod->min_stock_level ?> <?= esc($prod->unit_name) ?></span></td>
                    <td>
                        <?php if ($prod->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="<?= base_url('products/edit/' . $prod->id) ?>" class="btn-icon edit"><i class="fas fa-pen"></i></a>
                            <button class="btn-icon delete" onclick="confirmDelete('<?= base_url('products/delete/' . $prod->id) ?>', '<?= esc($prod->name) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($products)): ?>
                <tr><td colspan="7" class="text-center">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    setupTableSearch('productSearch', 'productsTable');
</script>
<?= $this->endSection() ?>
