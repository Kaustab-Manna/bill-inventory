<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-layer-group" style="color:var(--primary);margin-right:8px;"></i> Stock Overview</h1>
        <p>Real-time inventory levels across all warehouses</p>
    </div>
    <div class="d-flex gap-8">
        <form action="<?= base_url('stock') ?>" method="get" class="mb-0">
            <select name="warehouse_id" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="">All Warehouses</option>
                <?php foreach($warehouses as $wh): ?>
                    <option value="<?= $wh->id ?>" <?= (isset($selectedWarehouse) && $selectedWarehouse == $wh->id) ? 'selected' : '' ?>>
                        <?= esc($wh->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?= base_url('stock/export') . (isset($selectedWarehouse) && $selectedWarehouse ? '?warehouse_id=' . $selectedWarehouse : '') ?>" class="btn btn-ghost"><i class="fas fa-download"></i> Export</a>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-search">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="Search by product name or SKU..." id="stockSearch">
    </div>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table" id="stockTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Warehouse</th>
                    <th>In Stock</th>
                    <th>Reserved</th>
                    <th>Available</th>
                    <th>Expiry</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stock as $item): ?>
                <?php 
                    $available = (float)$item->quantity - (float)$item->reserved_qty;
                    $isOutOfStock = $available <= 0;
                    $isLowStock = !$isOutOfStock && ($available <= $item->min_stock_level);
                    $displayAvailable = max(0, $available);
                    $displayQuantity = max(0, (float)$item->quantity);
                ?>
                <tr>
                    <td>
                        <div class="d-flex items-center gap-12">
                            <?php if ($item->image): ?>
                                <img src="<?= base_url($item->image) ?>" alt="Product" style="width:32px;height:32px;border-radius:4px;object-fit:cover;">
                            <?php else: ?>
                                <div class="kpi-icon info" style="width:32px;height:32px;border-radius:4px;font-size:0.8rem;"><i class="fas fa-box"></i></div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight:600;color:var(--text-primary);"><?= esc($item->product_name) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">SKU: <?= esc($item->sku) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= esc($item->warehouse_name) ?></td>
                    <td style="font-weight:600;"><?= number_format($displayQuantity, 2) ?> <?= esc($item->unit_name) ?></td>
                    <td style="color:var(--text-muted);"><?= number_format($item->reserved_qty, 2) ?> <?= esc($item->unit_name) ?></td>
                    <td>
                        <span style="font-weight:700;font-size:1.1rem;color:<?= $isOutOfStock ? 'var(--danger)' : ($isLowStock ? 'var(--warning)' : 'var(--success)') ?>;">
                            <?= number_format($displayAvailable, 2) ?> <?= esc($item->unit_name) ?>
                        </span>
                    </td>
                    <td>
                        <?php
                            $today = new DateTime();
                            $thirtyDays = (new DateTime())->modify('+30 days');
                            $hasExpired = false;
                            $hasExpiringSoon = false;
                            
                            $productBatches = [];
                            if (isset($batches)) {
                                foreach ($batches as $b) {
                                    if ($b->product_id == $item->product_id && $b->warehouse_id == $item->warehouse_id) {
                                        $expDate = new DateTime($b->expiry_date);
                                        if ($expDate < $today) {
                                            $hasExpired = true;
                                        } elseif ($expDate <= $thirtyDays) {
                                            $hasExpiringSoon = true;
                                        }
                                        $productBatches[] = $b;
                                    }
                                }
                            }
                        ?>
                        
                        <?php if ($hasExpired): ?>
                            <span class="badge badge-danger" title="Contains expired batches!">Expired</span>
                        <?php elseif ($hasExpiringSoon): ?>
                            <span class="badge badge-warning text-dark" title="Contains batches expiring within 30 days">Expiring Soon</span>
                        <?php elseif (!empty($productBatches)): ?>
                            <span class="badge badge-info" title="Expiry dates tracked">Tracked</span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($isOutOfStock): ?>
                            <span class="badge badge-danger">Out of Stock</span>
                        <?php elseif ($isLowStock): ?>
                            <span class="badge badge-warning">Low Stock</span>
                        <?php else: ?>
                            <span class="badge badge-success">In Stock</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($stock)): ?>
                <tr><td colspan="6" class="text-center">No stock records found. Try adding products and doing a stock adjustment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    setupTableSearch('stockSearch', 'stockTable');
</script>
<?= $this->endSection() ?>
