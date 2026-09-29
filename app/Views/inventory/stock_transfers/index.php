<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-exchange-alt" style="color:var(--primary-light);margin-right:8px;"></i> Stock Transfers</h1>
        <p>Manage and track stock transfers between warehouses</p>
    </div>
    <a href="<?= base_url('stock-transfers/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Transfer
    </a>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table" id="transfersTable">
            <thead>
                <tr>
                    <th>Ref No</th>
                    <th>Date</th>
                    <th>From Warehouse</th>
                    <th>To Warehouse</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transfers)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-truck-loading"></i></div>
                                <h4>No transfers found</h4>
                                <p>Create your first stock transfer to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transfers as $transfer): ?>
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;font-weight:600;"><?= esc($transfer->reference_no) ?></td>
                            <td><?= date('d M Y', strtotime($transfer->transfer_date)) ?></td>
                            <td><?= esc($transfer->from_warehouse) ?></td>
                            <td><?= esc($transfer->to_warehouse) ?></td>
                            <td>
                                <?php if ($transfer->status === 'pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php elseif ($transfer->status === 'approved'): ?>
                                    <span class="badge badge-info">Approved</span>
                                <?php elseif ($transfer->status === 'completed'): ?>
                                    <span class="badge badge-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($transfer->creator) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="<?= base_url('stock-transfers/view/' . $transfer->id) ?>" class="btn-icon view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($transfer->status === 'pending'): ?>
                                    <button class="btn-icon delete" title="Delete" onclick="deleteTransfer(<?= $transfer->id ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function deleteTransfer(id) {
        if (!confirm('Are you sure you want to delete this pending transfer?')) return;
        
        const response = await fetch(`<?= base_url("stock-transfers/delete") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }
</script>
<?= $this->endSection() ?>
