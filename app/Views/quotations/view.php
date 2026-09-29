<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-9">
        <div class="card" id="printArea">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-5 pb-3 border-bottom">
                    <div>
                        <h2 class="text-primary mb-1 fw-bold">QUOTATION</h2>
                        <div class="text-muted fs-5">#<?= esc($quotation->quotation_no) ?></div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-1">BillInventory</h4>
                        <div class="text-muted">
                            Warehouse: <?= esc($quotation->warehouse_name ?? 'N/A') ?>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <div class="text-muted mb-2">Quote To:</div>
                        <h5 class="fw-bold mb-1"><?= esc($quotation->customer_name ?? 'Walk-in Customer') ?></h5>
                        <?php if (!empty($quotation->customer_id)): ?>
                            <?php if (!empty($quotation->address)): ?><div><?= esc($quotation->address) ?></div><?php endif; ?>
                            <?php if (!empty($quotation->phone)): ?><div>Phone: <?= esc($quotation->phone) ?></div><?php endif; ?>
                            <?php if (!empty($quotation->email)): ?><div>Email: <?= esc($quotation->email) ?></div><?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 text-end">
                        <div class="mb-2"><span class="text-muted me-2">Date:</span> <?= date('d M Y', strtotime($quotation->quotation_date)) ?></div>
                        <div class="mb-2">
                            <span class="text-muted me-2">Expiry Date:</span> 
                            <?= $quotation->expiry_date ? date('d M Y', strtotime($quotation->expiry_date)) : 'N/A' ?>
                        </div>
                        <div>
                            <span class="text-muted me-2">Status:</span> 
                            <?php if ($quotation->status == 'pending'): ?>
                                <span class="badge bg-warning text-dark fs-6">Pending</span>
                            <?php elseif ($quotation->status == 'accepted'): ?>
                                <span class="badge bg-success fs-6">Accepted</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Rejected</span>
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
                                <th class="text-end" width="150">Unit Price (₹)</th>
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
                        <?php if ($quotation->notes): ?>
                            <div class="text-muted mb-2"><strong>Notes / Terms:</strong></div>
                            <p class="text-muted" style="white-space: pre-line;"><?= esc($quotation->notes) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless text-end">
                            <tr>
                                <td>Subtotal:</td>
                                <td width="150">₹<?= number_format($quotation->subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Discount (<?= $quotation->discount_percent + 0 ?>%):</td>
                                <td>₹<?= number_format($quotation->discount, 2) ?></td>
                            </tr>
                            <tr class="fs-5 border-top fw-bold text-primary">
                                <td class="pt-3">Total Amount:</td>
                                <td class="pt-3">₹<?= number_format($quotation->total_amount, 2) ?></td>
                            </tr>
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
                    <i class="fas fa-print"></i> Print Quotation
                </button>
                <a href="<?= base_url('quotations') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Update Status</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('quotations/status/' . $quotation->id) ?>" method="post">
                    <?php
                        $selectClass = 'form-select mb-3';
                        if ($quotation->status == 'accepted') $selectClass .= ' bg-success text-white';
                        elseif ($quotation->status == 'pending') $selectClass .= ' bg-warning text-dark';
                        elseif ($quotation->status == 'rejected') $selectClass .= ' bg-danger text-white';
                    ?>
                    <select name="status" class="<?= $selectClass ?>">
                        <option value="pending" <?= $quotation->status == 'pending' ? 'selected' : '' ?> class="bg-white text-dark">Pending</option>
                        <option value="accepted" <?= $quotation->status == 'accepted' ? 'selected' : '' ?> class="bg-white text-dark">Accepted</option>
                        <option value="rejected" <?= $quotation->status == 'rejected' ? 'selected' : '' ?> class="bg-white text-dark">Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-success w-100">Update</button>
                </form>
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
