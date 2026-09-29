<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-chart-line"></i> <?= esc($pageTitle) ?></h4>
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
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Generate Report</button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Total Sales</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalSales, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Amount Collected</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalPaid, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow-sm border-0">
                    <div class="card-body py-4">
                        <h6 class="text-uppercase mb-2 text-white">Amount Due</h6>
                        <h3 class="mb-0 text-white">₹<?= number_format($totalSales - $totalPaid, 2) ?></h3>
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
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($sales)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No sales found for this date range.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($sales as $s): ?>
                        <tr>
                            <td><?= date('d-m-Y H:i', strtotime($s->sale_date)) ?></td>
                            <td class="fw-bold text-primary"><?= esc($s->invoice_no) ?></td>
                            <td><?= esc($s->customer_name ?: 'Walk-in Customer') ?></td>
                            <td class="text-end">₹<?= number_format($s->subtotal, 2) ?></td>
                            <td class="text-end text-danger">-₹<?= number_format($s->discount, 2) ?></td>
                            <td class="text-end fw-bold">₹<?= number_format($s->total_amount, 2) ?></td>
                            <td class="text-end text-success fw-bold">₹<?= number_format($s->paid_amount, 2) ?></td>
                            <td class="text-center">
                                <?php if($s->status == 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif($s->status == 'partial'): ?>
                                    <span class="badge bg-warning text-dark">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if(!empty($sales)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end text-uppercase">Grand Totals:</td>
                        <td class="text-end text-primary fs-5">₹<?= number_format($totalSales, 2) ?></td>
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
