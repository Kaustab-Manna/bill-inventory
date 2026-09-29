<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title"><?= esc($pageTitle) ?></h4>
        <a href="<?= base_url('purchase-orders/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Purchase Order
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
                        <th>PO No</th>
                        <th>Vendor</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pos as $po): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($po->created_at)) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($po->po_no) ?></span></td>
                            <td><?= esc($po->vendor_name) ?></td>
                            <td>₹<?= number_format($po->total_amount, 2) ?></td>
                            <td>
                                <?php if ($po->status == 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($po->status == 'draft'): ?>
                                    <span class="badge bg-warning text-dark">Draft</span>
                                <?php elseif ($po->status == 'sent'): ?>
                                    <span class="badge bg-info">Sent</span>
                                <?php elseif ($po->status == 'cancelled'): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('purchase-orders/view/' . $po->id) ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <?php if ($po->status !== 'approved' && $po->status !== 'cancelled'): ?>
                                    <a href="<?= base_url('purchase-orders/convert/' . $po->id) ?>" class="btn btn-sm btn-success" title="Approve & Convert to Purchase Invoice" onclick="return confirm('Approve Purchase Order #<?= esc($po->po_no) ?> and convert it into a Purchase Invoice?');"><i class="fas fa-check-circle"></i></a>
                                <?php endif; ?>
                                <?php if ($po->status == 'draft' || $po->status == 'cancelled'): ?>
                                    <a href="<?= base_url('purchase-orders/delete/' . $po->id) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this PO?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
