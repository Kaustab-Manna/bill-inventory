<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('purchase-returns/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Purchase Return
        </a>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Return No</th>
                        <th>Vendor</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $return): ?>
                        <tr>
                            <td><?= date('d-m-Y H:i', strtotime($return->return_date)) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($return->return_no) ?></span></td>
                            <td><?= esc($return->vendor_name) ?></td>
                            <td class="text-danger fw-bold">- ₹<?= number_format($return->total_amount, 2) ?></td>
                            <td>
                                <span class="badge bg-success">Returned</span>
                            </td>
                            <td>
                                <a href="<?= base_url('purchase-returns/view/' . $return->id) ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('purchase-returns/delete/' . $return->id) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this return? The items will be added BACK to your warehouse stock.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
