<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?= esc(session()->get('user_name')) ?>! Here's your business overview.</p>
    </div>
    <div class="d-flex gap-8 align-items-center" style="position:relative;">
        <!-- Export Dropdown -->
        <div class="dropdown">
            <button type="button" class="btn btn-ghost btn-sm" id="btnDashboardExport" onclick="toggleDropdown('dashboardExportDropdown'); event.stopPropagation();" title="Export Dashboard Data">
                <i class="fas fa-download"></i> Export <i class="fas fa-chevron-down ms-1" style="font-size:0.65rem; opacity:0.7;"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" id="dashboardExportDropdown" style="min-width: 240px;">
                <a href="<?= base_url('dashboard/export') ?>" class="dropdown-item">
                    <i class="fas fa-file-csv text-success" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">Export as CSV</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">KPIs, 7-day stats & activity log</div>
                    </div>
                </a>
                <button type="button" class="dropdown-item w-100 text-start border-0 bg-transparent" onclick="exportDashboardPDF()">
                    <i class="fas fa-file-pdf text-danger" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">Export as PDF</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Download clean PDF report</div>
                    </div>
                </button>
                <button type="button" class="dropdown-item w-100 text-start border-0 bg-transparent" onclick="window.print()">
                    <i class="fas fa-print text-primary" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">Print Overview</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Browser print preview</div>
                    </div>
                </button>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('reports/sales') ?>" class="dropdown-item">
                    <i class="fas fa-chart-line text-info" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">Detailed Sales Report</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">View in-depth sales analytics</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Sale Split Action -->
        <div class="dropdown d-inline-flex" style="position:relative;">
            <div class="d-inline-flex" style="box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25); border-radius: var(--radius-md); overflow: hidden;">
                <a href="<?= base_url('pos') ?>" class="btn btn-primary btn-sm" id="btnQuickSale" style="border-top-right-radius: 0; border-bottom-right-radius: 0; box-shadow: none;" title="Open POS Billing Terminal">
                    <i class="fas fa-bolt"></i> Quick Sale
                </a>
                <button type="button" class="btn btn-primary btn-sm px-2" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 1px solid rgba(255,255,255,0.25); box-shadow: none;" onclick="toggleDropdown('quickSaleDropdown'); event.stopPropagation();" title="More Sale Options">
                    <i class="fas fa-chevron-down" style="font-size: 0.65rem;"></i>
                </button>
            </div>
            <div class="dropdown-menu dropdown-menu-end" id="quickSaleDropdown" style="min-width: 230px;">
                <a href="<?= base_url('pos') ?>" class="dropdown-item">
                    <i class="fas fa-cash-register text-primary" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">POS Terminal</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Fast barcode & touch checkout</div>
                    </div>
                </a>
                <a href="<?= base_url('sales/create') ?>" class="dropdown-item">
                    <i class="fas fa-file-invoice-dollar text-success" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">New Sales Invoice</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Standard B2B / tax invoice</div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('quotations/create') ?>" class="dropdown-item">
                    <i class="fas fa-file-lines text-warning" style="width:16px;"></i>
                    <div>
                        <div style="font-weight:600;">New Quotation</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Create price estimate</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards Row 1 -->
<div class="kpi-grid">
    <div class="kpi-card primary stagger-item">
        <div class="kpi-icon primary"><i class="fas fa-chart-line"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Today's Sales</div>
            <div class="kpi-value"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($todaySales, 2) ?></div>
            <div class="kpi-change up"><i class="fas fa-arrow-up"></i> Ready to track</div>
        </div>
    </div>

    <div class="kpi-card success stagger-item">
        <div class="kpi-icon success"><i class="fas fa-shopping-bag"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Today's Purchases</div>
            <div class="kpi-value"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($todayPurchases, 2) ?></div>
            <div class="kpi-change up"><i class="fas fa-arrow-up"></i> Ready to track</div>
        </div>
    </div>

    <div class="kpi-card warning stagger-item">
        <div class="kpi-icon warning"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Low Stock Items</div>
            <div class="kpi-value"><?= $lowStockCount ?></div>
            <div class="kpi-change">Action required</div>
        </div>
    </div>

    <div class="kpi-card danger stagger-item">
        <div class="kpi-icon danger"><i class="fas fa-times-circle"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Out of Stock</div>
            <div class="kpi-value"><?= $outOfStockCount ?></div>
            <div class="kpi-change text-danger">Immediate action needed</div>
        </div>
    </div>

    <div class="kpi-card danger stagger-item">
        <div class="kpi-icon danger"><i class="fas fa-clock"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Pending Receivables</div>
            <div class="kpi-value"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($pendingReceivables, 2) ?></div>
            <div class="kpi-change">To be collected</div>
        </div>
    </div>
</div>

<!-- KPI Cards Row 2 -->
<div class="kpi-grid">
    <div class="kpi-card info stagger-item">
        <div class="kpi-icon info"><i class="fas fa-box"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Total Products</div>
            <div class="kpi-value"><?= number_format($totalProducts) ?></div>
        </div>
    </div>

    <div class="kpi-card accent stagger-item">
        <div class="kpi-icon accent"><i class="fas fa-users"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Customers</div>
            <div class="kpi-value"><?= number_format($totalCustomers) ?></div>
        </div>
    </div>

    <div class="kpi-card primary stagger-item">
        <div class="kpi-icon primary"><i class="fas fa-handshake"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Suppliers</div>
            <div class="kpi-value"><?= number_format($totalSuppliers) ?></div>
        </div>
    </div>

    <div class="kpi-card success stagger-item">
        <div class="kpi-icon success"><i class="fas fa-user-shield"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Active Users</div>
            <div class="kpi-value"><?= number_format($totalUsers) ?></div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-bottom:24px;">
    <!-- Sales Chart -->
    <div class="card stagger-item">
        <div class="card-header">
            <h3><i class="fas fa-chart-area" style="color:var(--primary-light);margin-right:8px;"></i> Sales & Purchase Overview</h3>
            <div class="d-flex gap-8">
                <button class="btn btn-ghost btn-sm active-tab" onclick="setChartPeriod('week')">Week</button>
                <button class="btn btn-ghost btn-sm" onclick="setChartPeriod('month')">Month</button>
                <button class="btn btn-ghost btn-sm" onclick="setChartPeriod('year')">Year</button>
            </div>
        </div>
        <div style="height: 300px; width: 100%; padding: 0 20px 20px 20px;">
            <canvas id="salesPurchaseChart"></canvas>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="card stagger-item">
        <div class="card-header">
            <h3><i class="fas fa-bolt" style="color:var(--warning);margin-right:8px;"></i> Quick Stats</h3>
        </div>
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:var(--bg-input); border-radius:var(--radius-md);">
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">This Month Sales</div>
                    <div style="font-size:1.1rem; font-weight:700; font-family:'JetBrains Mono',monospace;"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($monthSales, 2) ?></div>
                </div>
                <div class="kpi-icon primary" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-arrow-trend-up"></i></div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:var(--bg-input); border-radius:var(--radius-md);">
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">This Month Purchases</div>
                    <div style="font-size:1.1rem; font-weight:700; font-family:'JetBrains Mono',monospace;"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($monthPurchases, 2) ?></div>
                </div>
                <div class="kpi-icon success" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-truck"></i></div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:var(--bg-input); border-radius:var(--radius-md);">
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">Pending Payables</div>
                    <div style="font-size:1.1rem; font-weight:700; font-family:'JetBrains Mono',monospace;"><?= esc($company->currency_symbol ?? '₹') ?><?= number_format($pendingPayables, 2) ?></div>
                </div>
                <div class="kpi-icon danger" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:var(--bg-input); border-radius:var(--radius-md);">
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">Active Users</div>
                    <div style="font-size:1.1rem; font-weight:700; font-family:'JetBrains Mono',monospace;"><?= $totalUsers ?></div>
                </div>
                <div class="kpi-icon info" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card stagger-item">
    <div class="card-header">
        <h3><i class="fas fa-clock-rotate-left" style="color:var(--accent);margin-right:8px;"></i> Recent Activity</h3>
        <a href="<?= base_url('audit-logs') ?>" class="btn btn-ghost btn-sm">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <?php if(!empty($recentActivity)): ?>
        <div class="list-group list-group-flush">
            <?php foreach($recentActivity as $activity): ?>
                <?php 
                    $mod = strtolower(trim($activity->module));
                    $moduleMap = [
                        'auth'            => 'audit-logs',
                        'warehouse'       => 'warehouses',
                        'warehouses'      => 'warehouses',
                        'stock_transfer'  => 'stock-transfers',
                        'stock_transfers' => 'stock-transfers',
                        'stock_adjustment'=> 'stock-adjustments',
                        'sale'            => 'sales',
                        'purchase'        => 'purchases',
                    ];
                    $targetPath = $moduleMap[$mod] ?? str_replace('_', '-', $mod);
                    $link = base_url($targetPath);
                ?>
                <a href="<?= $link ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="padding: 16px; border-bottom: 1px solid var(--border); background: transparent; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.background='var(--bg-input)'; this.style.paddingLeft='20px';" onmouseout="this.style.background='transparent'; this.style.paddingLeft='16px';">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <div class="kpi-icon <?= $activity->action == 'create' ? 'success' : ($activity->action == 'delete' ? 'danger' : 'info') ?>" style="width:40px; height:40px; font-size:1rem; flex-shrink: 0;">
                            <?php if($activity->action == 'create'): ?><i class="fas fa-plus"></i>
                            <?php elseif($activity->action == 'delete'): ?><i class="fas fa-trash"></i>
                            <?php elseif($activity->action == 'login'): ?><i class="fas fa-sign-in-alt"></i>
                            <?php else: ?><i class="fas fa-pencil-alt"></i><?php endif; ?>
                        </div>
                        <div>
                            <h5 style="margin:0; font-size:0.95rem; font-weight:600;">
                                <?= ucfirst($activity->action) ?> <?= rtrim(ucfirst($activity->module), 's') ?>
                            </h5>
                            <small style="color:var(--text-muted);">by <?= esc($activity->user_name ?? 'System') ?></small>
                        </div>
                    </div>
                    <?php
                        $time = strtotime($activity->created_at);
                        if (!$time || date('Y', $time) == '1970') {
                            $timeStr = "Unknown Date";
                        } else {
                            $timeStr = date('M d, h:i A', $time);
                        }
                    ?>
                    <span style="font-size:0.8rem; color:var(--text-muted);"><i class="fas fa-clock"></i> <?= $timeStr ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-list-check"></i></div>
            <h4>No recent activity yet</h4>
            <p>Recent sales, purchases, and other transactions will appear here.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartDataRaw = <?= $chartData ?>;
    let salesPurchaseChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('salesPurchaseChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Create gradient for Sales
        const salesGradient = ctx.createLinearGradient(0, 0, 0, 300);
        salesGradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
        salesGradient.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

        // Create gradient for Purchases
        const purchaseGradient = ctx.createLinearGradient(0, 0, 0, 300);
        purchaseGradient.addColorStop(0, 'rgba(6, 182, 212, 0.5)');
        purchaseGradient.addColorStop(1, 'rgba(6, 182, 212, 0.05)');

        salesPurchaseChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartDataRaw.week.labels,
                datasets: [
                    {
                        label: 'Sales',
                        data: chartDataRaw.week.sales,
                        borderColor: '#6366f1',
                        backgroundColor: salesGradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Purchases',
                        data: chartDataRaw.week.purchases,
                        borderColor: '#06b6d4',
                        backgroundColor: purchaseGradient,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        borderColor: 'rgba(51, 65, 85, 0.5)',
                        borderWidth: 1,
                        displayColors: true,
                        usePointStyle: true,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(51, 65, 85, 0.1)',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#94a3b8',
                            padding: 10,
                            callback: function(value) {
                                if (value >= 1000) {
                                    return '₹' + (value / 1000).toFixed(1) + 'k';
                                }
                                return '₹' + value;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });

    function setChartPeriod(period) {
        if (event && event.currentTarget) {
            document.querySelectorAll('.card-header .btn-ghost').forEach(el => el.classList.remove('active-tab'));
            event.currentTarget.classList.add('active-tab');
        }

        if (salesPurchaseChart && chartDataRaw[period]) {
            const data = chartDataRaw[period];
            salesPurchaseChart.data.labels = data.labels;
            salesPurchaseChart.data.datasets[0].data = data.sales;
            salesPurchaseChart.data.datasets[1].data = data.purchases;
            salesPurchaseChart.update();
        }
    }

    function exportDashboardPDF() {
        if (typeof showToast === 'function') {
            showToast('Preparing PDF export...', 'info', 2000);
        }
        
        // Close open dropdowns
        document.querySelectorAll('.dropdown-menu.show').forEach(d => d.classList.remove('show'));

        const content = document.querySelector('.content-area') || document.body;
        const pageHeaderActions = document.querySelector('.page-header .d-flex');
        if (pageHeaderActions) pageHeaderActions.style.visibility = 'hidden';

        const opt = {
            margin:       [0.3, 0.3, 0.3, 0.3],
            filename:     'Dashboard_Report_' + new Date().toISOString().split('T')[0] + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        if (typeof html2pdf !== 'undefined') {
            html2pdf().set(opt).from(content).save().then(() => {
                if (pageHeaderActions) pageHeaderActions.style.visibility = 'visible';
                if (typeof showToast === 'function') {
                    showToast('Dashboard PDF downloaded successfully!', 'success', 3000);
                }
            }).catch(err => {
                if (pageHeaderActions) pageHeaderActions.style.visibility = 'visible';
                console.error('PDF generation error:', err);
                window.print();
            });
        } else {
            if (pageHeaderActions) pageHeaderActions.style.visibility = 'visible';
            window.print();
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
@media print {
    .sidebar, .header, .page-header .d-flex, .toast-container {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .content-area {
        padding: 10px !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .card, .kpi-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
</style>
<?= $this->endSection() ?>
