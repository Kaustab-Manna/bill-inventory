<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color:var(--text-primary);">
            <i class="fas fa-clipboard-check text-warning me-2"></i> QC Inspections (Returned Items)
        </h1>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Inspect unsealed / broken-seal returned items before approving them back into warehouse inventory or writing them off.
        </p>
    </div>
    <div>
        <a href="<?= base_url('sales-returns') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-undo me-1"></i> View Sales Returns
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Return No</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Target Warehouse</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center">No items pending inspection.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= esc($item->return_no) ?></span></td>
                            <td><?= esc($item->product_name) ?> <br><small class="text-danger">(Seal Broken)</small></td>
                            <td><?= $item->quantity + 0 ?></td>
                            <td><?= esc($item->warehouse_name) ?></td>
                            <td>
                                <?php if ($item->inspection_status == 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending Investigation</span>
                                <?php elseif ($item->inspection_status == 'passed'): ?>
                                    <span class="badge bg-success">Passed (In Stock)</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failed (Written Off)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->inspection_status == 'pending'): ?>
                                    <a href="<?= base_url('inspections/approve/' . $item->id) ?>" class="btn btn-sm btn-success" title="Approve & Send to Stock" onclick="return confirm('Approve this item? It will be added back to the warehouse stock.')"><i class="fas fa-check"></i> Approve (Stock)</a>
                                    <a href="<?= base_url('inspections/reject/' . $item->id) ?>" class="btn btn-sm btn-danger" title="Reject & Write Off" onclick="return confirm('Reject this item? It will NOT be added to stock and will be written off.')"><i class="fas fa-times"></i> Reject (Write Off)</a>
                                <?php else: ?>
                                    <span class="text-muted">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
