<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card text-white" style="background: var(--gradient-primary); border: none; box-shadow: var(--shadow-glow);">
            <div class="card-body d-flex justify-content-between align-items-center py-4">
                <div>
                    <h5 class="card-title text-white mb-1" style="font-weight: 700;">Total Accounts Receivable</h5>
                    <p class="mb-0 text-white-50">Total money owed to you across all customer and walk-in sales</p>
                </div>
                <h2 class="text-white mb-0" style="font-weight: 800; font-family: 'JetBrains Mono', monospace;">₹<?= number_format($totalReceivable, 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100" style="background: var(--bg-card); border: 1px solid var(--border);">
            <div class="card-header" style="background: var(--bg-surface); border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--text-primary); font-weight: 600;">Customer Outstanding Balances</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background: var(--bg-surface-hover); color: var(--text-secondary);">
                            <tr>
                                <th>Customer</th>
                                <th class="text-end">Balance Due</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($customerBalances)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No outstanding balances.</td></tr>
                            <?php else: ?>
                                <?php foreach($customerBalances as $cb): ?>
                                <tr>
                                    <td>
                                        <strong style="color: var(--text-primary);"><?= esc($cb['customer']->name) ?></strong><br>
                                        <small class="text-muted"><?= esc($cb['customer']->phone) ?></small>
                                    </td>
                                    <td class="text-end text-danger fw-bold" style="font-family: 'JetBrains Mono', monospace;">₹<?= number_format($cb['outstanding'], 2) ?></td>
                                    <td class="text-end">
                                        <?php if ($cb['customer']->id > 0): ?>
                                            <a href="<?= base_url('customers/view/' . $cb['customer']->id) ?>" class="btn btn-sm btn-outline-primary" title="View Customer Ledger"><i class="fas fa-file-invoice me-1"></i> Ledger</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('sales') ?>" class="btn btn-sm btn-outline-info" title="View All Sales"><i class="fas fa-receipt me-1"></i> Invoices</a>
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

    <div class="col-md-6 mb-4">
        <div class="card h-100" style="background: var(--bg-card); border: 1px solid var(--border);">
            <div class="card-header" style="background: var(--bg-surface); border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--text-primary); font-weight: 600;">Unpaid Sales Invoices</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead style="background: var(--bg-surface-hover); color: var(--text-secondary);">
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th class="text-end">Due Amount</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($unpaidInvoices)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No unpaid invoices.</td></tr>
                            <?php else: ?>
                                <?php foreach($unpaidInvoices as $inv): 
                                    $due = $inv->total_amount - $inv->paid_amount;
                                ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($inv->sale_date)) ?></td>
                                    <td><strong><?= esc($inv->invoice_no) ?></strong></td>
                                    <td><?= esc($inv->customer_name ?: 'Walk-in') ?></td>
                                    <td class="text-end text-danger fw-bold" style="font-family: 'JetBrains Mono', monospace;">₹<?= number_format($due, 2) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('sales/view/' . $inv->id) ?>" class="btn btn-sm btn-info text-white" title="View Invoice"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($unpaidInvoices)): ?>
                        <tfoot>
                            <tr style="background: var(--bg-surface-hover); font-weight: bold;">
                                <td colspan="3" class="text-end" style="color: var(--text-primary);">Total Due:</td>
                                <td class="text-end text-danger" style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem;">₹<?= number_format($totalReceivable, 2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
