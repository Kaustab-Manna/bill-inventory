<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-file-invoice" style="color:var(--primary-light);margin-right:8px;"></i> Transfer Details: <?= esc($transfer->reference_no) ?></h1>
        <p>Created on <?= date('d M Y', strtotime($transfer->created_at)) ?> by <?= esc($transfer->creator) ?></p>
    </div>
    <div>
        <a href="<?= base_url('stock-transfers') ?>" class="btn btn-ghost" style="margin-right:8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <?php if ($transfer->status === 'pending'): ?>
            <button class="btn btn-primary" onclick="approveTransfer(<?= $transfer->id ?>)">
                <i class="fas fa-check"></i> Approve Transfer
            </button>
        <?php elseif ($transfer->status === 'approved'): ?>
            <button class="btn btn-success" onclick="completeTransfer(<?= $transfer->id ?>)">
                <i class="fas fa-check-double"></i> Complete Transfer
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; padding: 20px;">
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Status</div>
            <div>
                <?php if ($transfer->status === 'pending'): ?>
                    <span class="badge badge-warning">Pending</span>
                <?php elseif ($transfer->status === 'approved'): ?>
                    <span class="badge badge-info">Approved</span>
                <?php elseif ($transfer->status === 'completed'): ?>
                    <span class="badge badge-success">Completed</span>
                <?php else: ?>
                    <span class="badge badge-danger">Rejected</span>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Transfer Date</div>
            <div style="font-weight:600;"><?= date('d M Y', strtotime($transfer->transfer_date)) ?></div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">From Warehouse</div>
            <div style="font-weight:600;"><?= esc($transfer->from_warehouse) ?></div>
        </div>
        <div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">To Warehouse</div>
            <div style="font-weight:600;"><?= esc($transfer->to_warehouse) ?></div>
        </div>
    </div>
    
    <?php if (!empty($transfer->notes)): ?>
    <div style="padding: 0 20px 20px;">
        <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:4px;">Notes</div>
        <div style="padding:12px; background:var(--bg-lighter); border-radius:6px; border:1px solid var(--border-color);">
            <?= nl2br(esc($transfer->notes)) ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3 style="padding: 20px 20px 0; margin-top:0;">Transferred Products</h3>
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
    async function approveTransfer(id) {
        if (!confirm('Are you sure you want to approve this transfer?')) return;
        
        const response = await fetch(`<?= base_url("stock-transfers/approve") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }

    async function completeTransfer(id) {
        if (!confirm('Are you sure you want to complete this transfer? This will move the stock between warehouses.')) return;
        
        const response = await fetch(`<?= base_url("stock-transfers/complete") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }
</script>
<?= $this->endSection() ?>
