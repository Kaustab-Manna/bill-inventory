<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 fw-bold"><i class="fas fa-undo text-danger me-2"></i><?= esc($pageTitle) ?></h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('inspections') ?>" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-clipboard-check me-1"></i> QC Inspections
            </a>
            <a href="<?= base_url('sales-returns/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Sale Return
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="140">Date</th>
                        <th width="160">Return No</th>
                        <th width="160">Original Invoice</th>
                        <th>Customer</th>
                        <th width="130" class="text-end">Tax Refunded</th>
                        <th width="150" class="text-end">Total Refund</th>
                        <th width="110" class="text-center">Status</th>
                        <th width="100" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($returns)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                No sales returns found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returns as $return): ?>
                            <tr>
                                <td><?= date('d-m-Y H:i', strtotime($return->return_date)) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($return->return_no) ?></span></td>
                                <td>
                                    <?php if (!empty($return->original_invoice_no)): ?>
                                        <a href="<?= base_url('sales/view/' . $return->sale_id) ?>" class="badge bg-primary text-decoration-none">
                                            <i class="fas fa-file-invoice me-1"></i><?= esc($return->original_invoice_no) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Direct Return</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($return->customer_name ?? 'Walk-in') ?></td>
                                <td class="text-end text-primary fw-semibold">₹<?= number_format($return->tax_amount, 2) ?></td>
                                <td class="text-end text-danger fw-bold">- ₹<?= number_format($return->total_amount, 2) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success">Completed</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('sales-returns/view/' . $return->id) ?>" class="btn btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="<?= base_url('sales-returns/delete/' . $return->id) ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this return? This will re-deduct the returned stock quantities.');"><i class="fas fa-trash"></i></a>
                                    </div>
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
