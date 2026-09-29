<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-file-alt" style="color:var(--primary-light);margin-right:8px;"></i> Adjustment Details: <?= esc($adjustment->reference_no) ?></h1>
        <p>Created on <?= date('d M Y', strtotime($adjustment->created_at)) ?> by <?= esc($adjustment->creator) ?></p>
    </div>
    <div>
        <a href="<?= base_url('stock-adjustments') ?>" class="btn btn-ghost" style="margin-right:8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <?php if ($adjustment->status === 'pending'): ?>
            <button class="btn btn-primary" onclick="approveAdjustment(<?= $adjustment->id ?>)">
                <i class="fas fa-check"></i> Approve
            </button>
        <?php elseif ($adjustment->status === 'approved'): ?>
            <button class="btn btn-success" onclick="completeAdjustment(<?= $adjustment->id ?>)">
                <i class="fas fa-check-double"></i> Complete
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; padding: 20px;">
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Status</div>
            <div>
                <?php if ($adjustment->status === 'pending'): ?>
                    <span class="badge badge-warning">Pending</span>
                <?php elseif ($adjustment->status === 'approved'): ?>
                    <span class="badge badge-info">Approved</span>
                <?php elseif ($adjustment->status === 'completed'): ?>
                    <span class="badge badge-success">Completed</span>
                <?php else: ?>
                    <span class="badge badge-danger">Rejected</span>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Adjustment Date</div>
            <div style="font-weight:600;"><?= date('d M Y', strtotime($adjustment->adjustment_date)) ?></div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Warehouse</div>
            <div style="font-weight:600;"><?= esc($adjustment->warehouse) ?></div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Type</div>
            <div style="font-weight:600;">
                <?php if ($adjustment->type === 'addition'): ?>
                    <span style="color:var(--success);"><i class="fas fa-arrow-up"></i> Addition</span>
                <?php else: ?>
                    <span style="color:var(--danger);"><i class="fas fa-arrow-down"></i> Subtraction</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (!empty($adjustment->notes)): ?>
    <div style="padding: 0 20px 20px;">
        <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Reason / Notes</div>
        <div style="padding:12px; background:var(--bg-lighter); border-radius:6px; border:1px solid var(--border-color);">
            <?= nl2br(esc($adjustment->notes)) ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3 style="padding: 20px 20px 0; margin-top:0;">Adjusted Products</h3>
    <div class="data-table-wrapper" style="margin-top:16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Batch</th>
                    <th style="text-align:right;">Quantity</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td style="color:var(--text-muted);"><?= $index + 1 ?></td>
                        <td style="font-weight:500;"><?= esc($item->product_name) ?></td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;"><?= esc($item->sku) ?></td>
                        <td><?= $item->batch_number ? esc($item->batch_number) : '<span style="color:var(--text-muted);font-style:italic;">None</span>' ?></td>
                        <td style="text-align:right;font-weight:600;"><?= number_format($item->quantity, 2) ?></td>
                        <td><?= esc($item->unit) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function approveAdjustment(id) {
        if (!confirm('Are you sure you want to approve this adjustment?')) return;
        
        const response = await fetch(`<?= base_url("stock-adjustments/approve") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }

    async function completeAdjustment(id) {
        if (!confirm('Are you sure you want to complete this adjustment? This will permanently modify the stock quantity.')) return;
        
        const response = await fetch(`<?= base_url("stock-adjustments/complete") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }
</script>
<?= $this->endSection() ?>
