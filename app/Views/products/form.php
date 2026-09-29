<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1>
            <a href="<?= base_url('products') ?>" style="color:var(--text-muted);"><i class="fas fa-arrow-left"></i></a> 
            <span style="margin-left:12px;"><?= esc($pageTitle) ?></span>
        </h1>
    </div>
</div>

<form action="<?= base_url(isset($product) ? 'products/update/'.$product->id : 'products/store') ?>" method="POST" enctype="multipart/form-data">
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px;">
        <!-- Main Form -->
        <div class="card">
            <h3 style="margin-bottom:20px;"><i class="fas fa-info-circle" style="color:var(--info);margin-right:8px;"></i> Basic Information</h3>
            
            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" required value="<?= esc(old('name', $product->name ?? '')) ?>" onkeyup="generateSlug(this.value, 'productSlug')">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Slug *</label>
                    <input type="text" name="slug" id="productSlug" class="form-control" required value="<?= esc(old('slug', $product->slug ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">SKU *</label>
                    <input type="text" name="sku" class="form-control" required value="<?= esc(old('sku', $product->sku ?? '')) ?>" style="text-transform:uppercase;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Barcode</label>
                <div style="display:flex;gap:10px;">
                    <input type="text" name="barcode" id="barcodeField" class="form-control" value="<?= esc(old('barcode', $product->barcode ?? '')) ?>">
                    <button type="button" class="btn btn-ghost" onclick="generateBarcode()"><i class="fas fa-barcode"></i> Generate</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= esc(old('description', $product->description ?? '')) ?></textarea>
            </div>

            <h3 style="margin:30px 0 20px;padding-top:20px;border-top:1px solid var(--border);"><i class="fas fa-money-bill" style="color:var(--success);margin-right:8px;"></i> Pricing & Stock Rules</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Purchase Price</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" class="form-control" value="<?= esc(old('purchase_price', $product->purchase_price ?? '0')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" class="form-control" required value="<?= esc(old('selling_price', $product->selling_price ?? '0')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Tax</label>
                    <select name="tax_id" class="form-control">
                        <option value="">No Tax</option>
                        <?php foreach($taxes as $tax): ?>
                            <option value="<?= $tax->id ?>" <?= old('tax_id', $product->tax_id ?? '') == $tax->id ? 'selected' : '' ?>><?= esc($tax->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Min Stock Level (Alert)</label>
                    <input type="number" min="0" name="min_stock_level" class="form-control" value="<?= esc(old('min_stock_level', $product->min_stock_level ?? '0')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Quantity</label>
                    <input type="number" min="0" name="reorder_qty" class="form-control" value="<?= esc(old('reorder_qty', $product->reorder_qty ?? '0')) ?>">
                </div>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div>
            <div class="card" style="margin-bottom:20px;">
                <h3 style="margin-bottom:20px;"><i class="fas fa-sitemap" style="color:var(--primary-light);margin-right:8px;"></i> Classification</h3>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-control">
                        <option value="">Select Brand</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand->id ?>" <?= old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' ?>><?= esc($brand->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit *</label>
                    <select name="unit_id" class="form-control" required>
                        <option value="">Select Unit</option>
                        <?php foreach($units as $unit): ?>
                            <option value="<?= $unit->id ?>" <?= old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' ?>><?= esc($unit->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;">
                <h3 style="margin-bottom:20px;"><i class="fas fa-image" style="color:var(--warning);margin-right:8px;"></i> Product Image</h3>
                <?php if (isset($product) && $product->image): ?>
                    <div style="margin-bottom:12px;text-align:center;">
                        <img src="<?= base_url($product->image) ?>" alt="Product" style="max-height:120px;border-radius:var(--radius-md);">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="card">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-switch">
                        <input type="checkbox" name="is_active" value="1" <?= old('is_active', $product->is_active ?? '1') == '1' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                        <span style="font-size:0.85rem;font-weight:600;">Product is Active</span>
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-primary btn-lg w-full"><i class="fas fa-save"></i> <?= isset($product) ? 'Update Product' : 'Save Product' ?></button>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function generateSlug(text, targetId) {
        <?php if(!isset($product)): ?>
        document.getElementById(targetId).value = text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        <?php endif; ?>
    }

    function generateBarcode() {
        const timestamp = new Date().getTime().toString().substr(-8);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        document.getElementById('barcodeField').value = timestamp + random;
    }
</script>
<?= $this->endSection() ?>
