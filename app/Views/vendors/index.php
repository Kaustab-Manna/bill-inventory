<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('vendors/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Vendor
        </a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="vendorsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Email / Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= $vendor->id ?></td>
                            <td><?= esc($vendor->name) ?></td>
                            <td><?= esc($vendor->contact_person ?? 'N/A') ?></td>
                            <td>
                                <?= esc($vendor->email) ?><br>
                                <small class="text-muted"><?= esc($vendor->phone) ?></small>
                            </td>
                            <td>
                                <?php if ($vendor->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('vendors/view/' . $vendor->id) ?>" class="btn btn-sm btn-primary" title="View Ledger"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('vendors/edit/' . $vendor->id) ?>" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="<?= base_url('vendors/delete/' . $vendor->id) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this vendor?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
