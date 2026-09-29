<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-boxes"></i> <?= esc($pageTitle) ?></h4>
        <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
    <div class="card-body">
        
        <!-- Summary Cards -->
        <div class="row mb-5 mt-3">
            <div class="col-md-4">
                <div class="card bg-info text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Total Inventory Cost (Purchase Value)</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalValuation, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Estimated Sales Value</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalSellingValuation, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Potential Gross Profit</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalSellingValuation - $totalValuation, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!empty($lowStockItems)): ?>
        <h5 class="text-danger border-bottom pb-2 mb-3"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h5>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-danger table-striped table-hover">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Warehouse</th>
                        <th class="text-center">Current Quantity</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lowStockItems as $lsi): ?>
                    <tr>
                        <td class="fw-bold"><?= esc($lsi->sku) ?></td>
                        <td><?= esc($lsi->product_name) ?></td>
                        <td><?= esc($lsi->warehouse_name ?: 'Main Store') ?></td>
                        <td class="text-center fw-bold fs-5">
                            <?= $lsi->quantity + 0 ?>
                        </td>
                        <td class="text-center">
                            <?php if($lsi->quantity <= 0): ?>
                                <span class="badge bg-dark fs-6">Out of Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Low Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-list"></i> Full Inventory Status</h5>
        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Warehouse</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Selling Price</th>
                        <th class="text-end">Total Valuation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($inventory)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No inventory found.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($inventory as $item): ?>
                        <tr>
                            <td class="text-muted font-monospace"><?= esc($item->sku) ?></td>
                            <td class="fw-bold text-primary"><?= esc($item->product_name) ?></td>
                            <td><?= esc($item->warehouse_name ?: 'Main Store') ?></td>
                            <td class="text-center fw-bold <?= $item->quantity <= 10 ? 'text-danger' : 'text-success' ?>">
                                <?= $item->quantity + 0 ?>
                            </td>
                            <td class="text-end">₹<?= number_format($item->purchase_price, 2) ?></td>
                            <td class="text-end">₹<?= number_format($item->selling_price, 2) ?></td>
                            <td class="text-end fw-bold">₹<?= number_format($item->quantity * $item->purchase_price, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .d-print-none { display: none !important; }
}
</style>
<?= $this->endSection() ?>
