<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('expenses/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount (₹)</th>
                        <th>Payment Method</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($expenses)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No expenses recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($expenses as $e): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($e->expense_date)) ?></td>
                            <td><?= esc($e->category) ?></td>
                            <td class="fw-bold text-danger">₹<?= number_format($e->amount, 2) ?></td>
                            <td><?= esc($e->payment_method) ?></td>
                            <td><?= esc($e->notes) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?= base_url('expenses/delete/' . $e->id) ?>', 'this expense')"><i class="fas fa-trash"></i></button>
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
