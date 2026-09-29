<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('customers/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Customer
        </a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Credit Limit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td>
                                <strong><?= esc($c->name) ?></strong><br>
                                <small class="text-muted"><?= esc($c->email) ?></small>
                            </td>
                            <td><?= esc($c->phone) ?></td>
                            <td><?= esc($c->city) ?></td>
                            <td>₹<?= number_format($c->credit_limit, 2) ?></td>
                            <td>
                                <?php if ($c->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('customers/view/' . $c->id) ?>" class="btn btn-sm btn-info" title="View Profile & Ledger"><i class="fas fa-user-circle"></i> Profile</a>
                                <a href="<?= base_url('customers/edit/' . $c->id) ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
