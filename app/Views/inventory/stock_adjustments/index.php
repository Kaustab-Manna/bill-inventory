<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-sliders-h" style="color:var(--primary-light);margin-right:8px;"></i> Stock Adjustments</h1>
        <p>Manage and track stock additions or subtractions</p>
    </div>
    <a href="<?= base_url('stock-adjustments/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Adjustment
    </a>
</div>

<div class="card">
    <div class="data-table-wrapper">
        <table class="data-table" id="adjustmentsTable">
            <thead>
                <tr>
                    <th>Ref No</th>
                    <th>Date</th>
                    <th>Warehouse</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($adjustments)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                                <h4>No adjustments found</h4>
                                <p>Create your first stock adjustment to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($adjustments as $adj): ?>
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;font-weight:600;"><?= esc($adj->reference_no) ?></td>
                            <td><?= date('d M Y', strtotime($adj->adjustment_date)) ?></td>
                            <td><?= esc($adj->warehouse) ?></td>
                            <td>
                                <?php if ($adj->type === 'addition'): ?>
                                    <span style="color:var(--success);font-weight:600;"><i class="fas fa-arrow-up"></i> Addition</span>
                                <?php else: ?>
                                    <span style="color:var(--danger);font-weight:600;"><i class="fas fa-arrow-down"></i> Subtraction</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($adj->status === 'pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php elseif ($adj->status === 'approved'): ?>
                                    <span class="badge badge-info">Approved</span>
                                <?php elseif ($adj->status === 'completed'): ?>
                                    <span class="badge badge-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($adj->creator) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="<?= base_url('stock-adjustments/view/' . $adj->id) ?>" class="btn-icon view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($adj->status === 'pending'): ?>
                                    <button class="btn-icon delete" title="Delete" onclick="deleteAdjustment(<?= $adj->id ?>)">
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
    async function deleteAdjustment(id) {
        if (!confirm('Are you sure you want to delete this pending adjustment?')) return;
        
        const response = await fetch(`<?= base_url("stock-adjustments/delete") ?>/${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    }
</script>
<?= $this->endSection() ?>
