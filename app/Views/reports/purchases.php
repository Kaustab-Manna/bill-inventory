<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-shopping-cart"></i> <?= esc($pageTitle) ?></h4>
        <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
    <div class="card-body">
        
        <!-- Filter Form -->
        <form method="get" class="row g-3 mb-4 d-print-none bg-light p-3 rounded border">
            <div class="col-md-4">
                <label class="form-label fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc($start_date) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc($end_date) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-filter"></i> Generate Report</button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Total Purchases</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalPurchases, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Amount Paid</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalPaid, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Outstanding Payable</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalPurchases - $totalPaid, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Ref No</th>
                        <th>Supplier</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($purchases)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No purchases found for this date range.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($purchases as $p): ?>
                        <tr>
                            <td><?= date('d-m-Y H:i', strtotime($p->purchase_date)) ?></td>
                            <td class="fw-bold text-primary"><?= esc($p->invoice_no ?? $p->reference_no ?? ('PUR-' . $p->id)) ?></td>
                            <td><?= esc($p->supplier_name ?: 'Unknown') ?></td>
                            <td class="text-end">₹<?= number_format($p->subtotal, 2) ?></td>
                            <td class="text-end text-danger">₹<?= number_format($p->tax_amount, 2) ?></td>
                            <td class="text-end fw-bold">₹<?= number_format($p->total_amount, 2) ?></td>
                            <td class="text-end text-success fw-bold">₹<?= number_format($p->paid_amount, 2) ?></td>
                            <td class="text-center">
                                <?php if(($p->payment_status ?? '') == 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif(($p->payment_status ?? '') == 'partial'): ?>
                                    <span class="badge bg-warning text-dark">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if(!empty($purchases)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end text-uppercase">Grand Totals:</td>
                        <td class="text-end text-primary fs-5">₹<?= number_format($totalPurchases, 2) ?></td>
                        <td class="text-end text-success fs-5">₹<?= number_format($totalPaid, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>

    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .d-print-none { display: none !important; }
}
</style>
<?= $this->endSection() ?>
