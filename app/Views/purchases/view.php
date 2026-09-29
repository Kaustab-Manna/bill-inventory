<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-9">
        <div class="card" id="printArea">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-5 pb-3 border-bottom">
                    <div>
                        <h2 class="text-primary mb-1 fw-bold">PURCHASE INVOICE</h2>
                        <div class="text-muted fs-5">#<?= esc($purchase->invoice_no) ?></div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-1">BillInventory</h4>
                        <div class="text-muted">
                            Warehouse Delivered To: <?= esc($purchase->warehouse_name ?? 'N/A') ?>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <div class="text-muted mb-2">Vendor / Supplier:</div>
                        <h5 class="fw-bold mb-1"><?= esc($purchase->vendor_name) ?></h5>
                        <div><?= esc($purchase->address) ?></div>
                        <div>Phone: <?= esc($purchase->phone) ?></div>
                        <div>Email: <?= esc($purchase->email) ?></div>
                    </div>
                    <div class="col-sm-6 text-end">
                        <div class="mb-2"><span class="text-muted me-2">Purchase Date:</span> <?= date('d M Y, h:i A', strtotime($purchase->purchase_date)) ?></div>
                        <div>
                            <span class="text-muted me-2">Payment Status:</span> 
                            <?php if ($purchase->payment_status == 'paid'): ?>
                                <span class="badge bg-success fs-6">Paid</span>
                            <?php elseif ($purchase->payment_status == 'partial'): ?>
                                <span class="badge bg-warning text-dark fs-6">Partial</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Unpaid</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="table-responsive mb-5">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="50">#</th>
                                <th>Description</th>
                                <th class="text-end" width="150">Unit Cost (₹)</th>
                                <th class="text-center" width="100">Qty</th>
                                <th class="text-end" width="150">Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td><?= esc($item->product_name) ?></td>
                                    <td class="text-end"><?= number_format($item->unit_price, 2) ?></td>
                                    <td class="text-center"><?= $item->quantity + 0 ?></td>
                                    <td class="text-end"><?= number_format($item->total, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-sm-7">
                        <?php if(!empty($purchase->notes)): ?>
                        <div class="mb-3">
                            <strong>Notes / Reference:</strong> <br>
                            <?= nl2br(esc($purchase->notes)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless text-end">
                            <tr>
                                <td>Subtotal:</td>
                                <td width="150">₹<?= number_format($purchase->subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Discount (<?= $purchase->discount_percent + 0 ?>%):</td>
                                <td>₹<?= number_format($purchase->discount, 2) ?></td>
                            </tr>
                            <tr class="fs-5 border-top fw-bold text-primary">
                                <td class="pt-3">Total Amount:</td>
                                <td class="pt-3">₹<?= number_format($purchase->total_amount, 2) ?></td>
                            </tr>
                            <tr class="fs-6 text-success border-top">
                                <td class="pt-2">Paid Amount:</td>
                                <td class="pt-2">₹<?= number_format($purchase->paid_amount, 2) ?></td>
                            </tr>
                            <?php if($purchase->total_amount - $purchase->paid_amount > 0): ?>
                            <tr class="fs-5 text-danger border-top">
                                <td class="pt-2">Balance Due:</td>
                                <td class="pt-2">₹<?= number_format($purchase->total_amount - $purchase->paid_amount, 2) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-body">
                <button class="btn btn-primary w-100 mb-3" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="<?= base_url('purchases') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <?php if($purchase->total_amount - $purchase->paid_amount > 0): ?>
        <div class="card mb-3 border-success">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0 text-white">Record Payment</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('purchases/payment/' . $purchase->id) ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Payment Amount (₹)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" max="<?= $purchase->total_amount - $purchase->paid_amount ?>" value="<?= $purchase->total_amount - $purchase->paid_amount ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Add Payment</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card border-danger">
            <div class="card-body">
                <a href="<?= base_url('purchases/delete/' . $purchase->id) ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this invoice? The stock quantities will be permanently deducted.');">
                    <i class="fas fa-trash"></i> Delete Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .card { border: none; box-shadow: none; }
}
</style>
<?= $this->endSection() ?>
