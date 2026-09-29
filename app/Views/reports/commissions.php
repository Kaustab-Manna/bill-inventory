<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-hand-holding-usd text-success"></i> <?= esc($pageTitle) ?></h4>
        <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
    <div class="card-body">
        
        <form method="get" class="row g-3 mb-4 d-print-none bg-light p-3 rounded border">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Generate Report</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Salesperson</th>
                        <th class="text-center">Total Invoices Generated</th>
                        <th class="text-end">Total Revenue Generated</th>
                        <th class="text-end">Total Commission Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($commissions)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No commissions recorded for this period.</td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $grandInvoices = 0;
                        $grandRevenue = 0;
                        $grandCommission = 0;
                        foreach($commissions as $comm): 
                            $grandInvoices += $comm->total_sales;
                            $grandRevenue += $comm->total_revenue;
                            $grandCommission += $comm->total_commission;
                        ?>
                        <tr>
                            <td class="fw-bold text-primary"><i class="fas fa-user-tie"></i> <?= esc($comm->salesperson) ?></td>
                            <td class="text-center"><?= $comm->total_sales ?></td>
                            <td class="text-end">₹<?= number_format($comm->total_revenue, 2) ?></td>
                            <td class="text-end fw-bold text-success">₹<?= number_format($comm->total_commission, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-bold fs-5">
                            <td class="text-end">GRAND TOTAL:</td>
                            <td class="text-center"><?= $grandInvoices ?></td>
                            <td class="text-end text-primary">₹<?= number_format($grandRevenue, 2) ?></td>
                            <td class="text-end text-success">₹<?= number_format($grandCommission, 2) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
