<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card text-white" style="background: var(--gradient-danger); border: none; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.25);">
            <div class="card-body d-flex justify-content-between align-items-center py-4">
                <div>
                    <h5 class="card-title text-white mb-1" style="font-weight: 700;">Total Accounts Payable</h5>
                    <p class="mb-0 text-white-50">Total money you owe across all vendor/supplier purchases</p>
                </div>
                <h2 class="text-white mb-0" style="font-weight: 800; font-family: 'JetBrains Mono', monospace;">₹<?= number_format($totalPayable, 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100" style="background: var(--bg-card); border: 1px solid var(--border);">
            <div class="card-header" style="background: var(--bg-surface); border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--text-primary); font-weight: 600;">Vendor Outstanding Balances</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background: var(--bg-surface-hover); color: var(--text-secondary);">
                            <tr>
                                <th>Vendor</th>
                                <th class="text-end">You Owe</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($vendorBalances)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No outstanding vendor balances.</td></tr>
                            <?php else: ?>
                                <?php foreach($vendorBalances as $vb): ?>
                                <tr>
                                    <td>
                                        <strong style="color: var(--text-primary);"><?= esc($vb['vendor']->name) ?></strong><br>
                                        <small class="text-muted"><?= esc($vb['vendor']->phone) ?></small>
                                    </td>
                                    <td class="text-end text-danger fw-bold" style="font-family: 'JetBrains Mono', monospace;">₹<?= number_format($vb['outstanding'], 2) ?></td>
                                    <td class="text-end">
                                        <?php if ($vb['vendor']->id > 0): ?>
                                            <a href="<?= base_url('vendors/view/' . $vb['vendor']->id) ?>" class="btn btn-sm btn-outline-primary" title="View Vendor Ledger"><i class="fas fa-truck me-1"></i> Ledger</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('purchases') ?>" class="btn btn-sm btn-outline-info" title="View Purchases"><i class="fas fa-cart-shopping me-1"></i> Purchases</a>
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
                <h5 class="card-title mb-0" style="color: var(--text-primary); font-weight: 600;">Unpaid Purchase Invoices</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead style="background: var(--bg-surface-hover); color: var(--text-secondary);">
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Vendor</th>
                                <th class="text-end">Due Amount</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($unpaidPurchases)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No unpaid purchase invoices.</td></tr>
                            <?php else: ?>
                                <?php foreach($unpaidPurchases as $inv): 
                                    $due = $inv->total_amount - $inv->paid_amount;
                                ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($inv->purchase_date)) ?></td>
                                    <td><strong><?= esc($inv->invoice_no ?? ('PUR-' . $inv->id)) ?></strong></td>
                                    <td><?= esc($inv->vendor_name ?: 'Direct Supplier') ?></td>
                                    <td class="text-end text-danger fw-bold" style="font-family: 'JetBrains Mono', monospace;">₹<?= number_format($due, 2) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('purchases/view/' . $inv->id) ?>" class="btn btn-sm btn-info text-white" title="View Purchase"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($unpaidPurchases)): ?>
                        <tfoot>
                            <tr style="background: var(--bg-surface-hover); font-weight: bold;">
                                <td colspan="3" class="text-end" style="color: var(--text-primary);">Total Due:</td>
                                <td class="text-end text-danger" style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem;">₹<?= number_format($totalPayable, 2) ?></td>
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
