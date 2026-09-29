<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Purchase Invoice
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
                        <th>Invoice No</th>
                        <th>Vendor</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><?= date('d-m-Y H:i', strtotime($purchase->purchase_date)) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($purchase->invoice_no) ?></span></td>
                            <td><?= esc($purchase->vendor_name) ?></td>
                            <td>₹<?= number_format($purchase->total_amount, 2) ?></td>
                            <td class="text-success">₹<?= number_format($purchase->paid_amount, 2) ?></td>
                            <td>
                                <?php if ($purchase->payment_status == 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif ($purchase->payment_status == 'partial'): ?>
                                    <span class="badge bg-warning text-dark">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('purchases/view/' . $purchase->id) ?>" class="btn btn-sm btn-info" title="View & Pay"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('purchases/delete/' . $purchase->id) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this Purchase Invoice? The received stock will be deducted back from your inventory.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
