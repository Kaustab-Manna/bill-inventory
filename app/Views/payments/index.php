<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('payments/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Record Payment
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment No</th>
                        <th>Type</th>
                        <th>Party</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($payments)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No payments recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($payments as $p): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($p->payment_date)) ?></td>
                            <td><?= esc($p->payment_no) ?></td>
                            <td>
                                <?php if($p->type == 'in'): ?>
                                    <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Money In</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Money Out</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($p->type == 'in'): ?>
                                    Customer: <strong><?= esc($p->customer_name ?: 'Walk-in') ?></strong>
                                <?php else: ?>
                                    Vendor: <strong><?= esc($p->vendor_name ?: 'Unknown') ?></strong>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold fs-5 <?= $p->type == 'in' ? 'text-success' : 'text-danger' ?>">
                                <?= $p->type == 'in' ? '+' : '-' ?>₹<?= number_format($p->amount, 2) ?>
                            </td>
                            <td><?= esc($p->payment_method) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?= base_url('payments/delete/' . $p->id) ?>', 'Payment <?= $p->payment_no ?>')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
