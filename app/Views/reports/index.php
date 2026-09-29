<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line" style="color:var(--primary);margin-right:8px;"></i> <?= esc($pageTitle) ?></h1>
        <p>Comprehensive overview of your business performance</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
</div>

<div class="row">
    <!-- Key Metrics -->
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid var(--success) !important;">
            <div class="card-body">
                <div class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.8rem; letter-spacing: 0.5px;">Total Sales</div>
                <h3 class="mb-0 text-success fw-bolder">₹<?= number_format($totalSales, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid var(--danger) !important;">
            <div class="card-body">
                <div class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.8rem; letter-spacing: 0.5px;">Total Purchases</div>
                <h3 class="mb-0 text-danger fw-bolder">₹<?= number_format($totalPurchases, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid var(--warning) !important;">
            <div class="card-body">
                <div class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.8rem; letter-spacing: 0.5px;">Total Expenses</div>
                <h3 class="mb-0 text-warning text-dark fw-bolder">₹<?= number_format($totalExpenses, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid var(--primary) !important;">
            <div class="card-body">
                <div class="text-muted mb-3 text-uppercase fw-bold" style="font-size:0.8rem; letter-spacing: 0.5px;">Est. Net Profit</div>
                <h3 class="mb-0 text-primary fw-bolder">₹<?= number_format($netProfit, 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Stock Valuation -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="card-title fw-bold">Inventory Assets</h5>
            </div>
            <div class="card-body text-center">
                <div style="width: 120px; height: 120px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-boxes fa-3x text-primary"></i>
                </div>
                <h4 class="fw-bold">₹<?= number_format($stockValuation, 2) ?></h4>
                <p class="text-muted">Total Current Stock Value (at Cost Price)</p>
                <a href="<?= base_url('stock') ?>" class="btn btn-outline-primary btn-sm">View Stock Overview</a>
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="card-title fw-bold">Recent Sales Activity</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recentSales)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No sales yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($recentSales as $sale): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($sale->sale_date)) ?></td>
                                    <td>
                                        <a href="<?= base_url('sales/view/'.$sale->id) ?>" class="text-decoration-none fw-bold"><?= esc($sale->invoice_no) ?></a>
                                    </td>
                                    <td><?= esc($sale->customer_name ?: 'Walk-in Customer') ?></td>
                                    <td class="fw-bold">₹<?= number_format($sale->total_amount, 2) ?></td>
                                    <td>
                                        <?php if ($sale->status == 'paid'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Paid</span>
                                        <?php elseif ($sale->status == 'partial'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Partial</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3 border-bottom pb-2">Advanced Reports</h4>
    </div>
    
    <!-- Sales Report -->
    <div class="col-md-4 mb-3">
        <a href="<?= base_url('reports/sales') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm custom-card-hover" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 p-3 bg-primary bg-opacity-10 rounded-circle text-primary">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-1">Sales Report</h5>
                        <p class="text-muted small mb-0">Detailed date-range analytics</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Purchases Report -->
    <div class="col-md-4 mb-3">
        <a href="<?= base_url('reports/purchases') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm custom-card-hover" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 p-3 bg-danger bg-opacity-10 rounded-circle text-danger">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-1">Purchases Report</h5>
                        <p class="text-muted small mb-0">Supplier & tax summaries</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Inventory Report -->
    <div class="col-md-3 mb-3">
        <a href="<?= base_url('reports/inventory') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm custom-card-hover" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 p-3 bg-info bg-opacity-10 rounded-circle text-info">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-1">Inventory</h5>
                        <p class="text-muted small mb-0">Stock worth & alerts</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Commissions Report -->
    <div class="col-md-3 mb-3">
        <a href="<?= base_url('reports/commissions') ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm custom-card-hover" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 p-3 bg-success bg-opacity-10 rounded-circle text-success">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title fw-bold text-dark mb-1">Commissions</h5>
                        <p class="text-muted small mb-0">Salesperson earnings</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.custom-card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
@media print {
    .sidebar, .navbar, .btn, .page-header button { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>
<?= $this->endSection() ?>
