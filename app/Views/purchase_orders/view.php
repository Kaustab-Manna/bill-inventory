<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-end align-items-center flex-wrap" style="gap: 10px; margin-bottom: 1.5rem;">
    <a href="<?= base_url('purchase-orders') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>

    <?php if (!empty($linkedInvoice)): ?>
        <a href="<?= base_url('purchases/view/' . $linkedInvoice->id) ?>" class="btn btn-outline-success">
            <i class="fas fa-file-invoice me-1"></i> View Purchase Invoice
        </a>
    <?php elseif ($po->status !== 'approved' && $po->status !== 'cancelled'): ?>
        <a href="<?= base_url('purchase-orders/convert/' . $po->id) ?>" class="btn btn-success" onclick="return confirm('Approve Purchase Order #<?= esc($po->po_no) ?> and convert it into a Purchase Invoice? Stock will be updated.');">
            <i class="fas fa-check-circle me-1"></i> Approve & Convert to Invoice
        </a>
    <?php endif; ?>

    <form action="<?= base_url('purchase-orders/status/' . $po->id) ?>" method="post" class="mb-0">
        <?php
            $selectClass = 'form-select';
            if ($po->status == 'approved') $selectClass .= ' bg-success text-white border-success';
            elseif ($po->status == 'draft') $selectClass .= ' bg-warning text-dark border-warning';
            elseif ($po->status == 'sent') $selectClass .= ' bg-info text-dark border-info';
            elseif ($po->status == 'cancelled') $selectClass .= ' bg-danger text-white border-danger';
        ?>
        <select name="status" class="<?= $selectClass ?>" onchange="if(this.value === 'approved') { if(confirm('Approving this Purchase Order will automatically convert it into a Purchase Invoice and update stock. Proceed?')) { this.form.submit(); } else { return false; } } else { this.form.submit(); }" style="min-width: 150px;">
            <option value="draft" <?= $po->status == 'draft' ? 'selected' : '' ?> class="bg-white text-dark">Draft</option>
            <option value="sent" <?= $po->status == 'sent' ? 'selected' : '' ?> class="bg-white text-dark">Sent</option>
            <option value="approved" <?= $po->status == 'approved' ? 'selected' : '' ?> class="bg-white text-dark">Approved</option>
            <option value="cancelled" <?= $po->status == 'cancelled' ? 'selected' : '' ?> class="bg-white text-dark">Cancelled</option>
        </select>
    </form>
    
    <button class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i> Print PO
    </button>
</div>

<?php if (!empty($linkedInvoice)): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <strong>Approved &amp; Converted to Purchase Invoice:</strong>
            <span>This order was approved and generated Purchase Invoice <code class="fw-bold"><?= esc($linkedInvoice->invoice_no) ?></code>. Warehouse inventory has been restocked.</span>
        </div>
        <a href="<?= base_url('purchases/view/' . $linkedInvoice->id) ?>" class="btn btn-sm btn-success">
            <i class="fas fa-eye me-1"></i> View Purchase Invoice
        </a>
    </div>
<?php elseif ($po->status !== 'approved' && $po->status !== 'cancelled'): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            <strong>Approval Workflow:</strong> When an Admin approves this Purchase Order, it will automatically convert into a <strong>Purchase Invoice</strong> and add the items to warehouse stock.
        </div>
        <a href="<?= base_url('purchase-orders/convert/' . $po->id) ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve Purchase Order #<?= esc($po->po_no) ?> and convert it into a Purchase Invoice?');">
            <i class="fas fa-check-circle me-1"></i> Approve &amp; Convert Now
        </a>
    </div>
<?php endif; ?>

<div class="row" style="margin-top: 1rem;">
    <div class="col-12">
        <div class="card" id="printArea">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-5 pb-3 border-bottom">
                    <div>
                        <h2 class="text-primary mb-1 fw-bold">PURCHASE ORDER</h2>
                        <div class="fs-5">#<?= esc($po->po_no) ?></div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-1">Inventory System</h4>
                        <div>
                            Deliver To Warehouse: <?= esc($po->warehouse_name ?? 'N/A') ?>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <div class="mb-2">Vendor / Supplier:</div>
                        <h5 class="fw-bold mb-1"><?= esc($po->vendor_name) ?></h5>
                        <div><?= esc($po->address) ?></div>
                        <div>Phone: <?= esc($po->phone) ?></div>
                        <div>Email: <?= esc($po->email) ?></div>
                    </div>
                    <div class="col-sm-6 text-end">
                        <div class="mb-2"><span class="me-2">Order Date:</span> <?= date('d M Y', strtotime($po->created_at)) ?></div>
                        <div class="mb-2"><span class="me-2">Expected By:</span> <?= $po->expected_date ? date('d M Y', strtotime($po->expected_date)) : 'N/A' ?></div>
                        <div>
                            <span class="me-2">Status:</span> 
                            <?php if ($po->status == 'approved'): ?>
                                <span class="badge bg-success fs-6">Approved</span>
                            <?php elseif ($po->status == 'draft'): ?>
                                <span class="badge bg-warning text-dark fs-6">Draft</span>
                            <?php elseif ($po->status == 'sent'): ?>
                                <span class="badge bg-info fs-6">Sent</span>
                            <?php elseif ($po->status == 'cancelled'): ?>
                                <span class="badge bg-danger fs-6">Cancelled</span>
                            <?php else: ?>
                                <span class="badge bg-primary fs-6">Completed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="table-responsive mb-5">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
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
                        <?php if(!empty($po->notes)): ?>
                        <div class="mb-3">
                            <strong>Notes / Terms:</strong> <br>
                            <?= nl2br(esc($po->notes)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless text-end">
                            <tr>
                                <td>Subtotal:</td>
                                <td width="150">₹<?= number_format($po->subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Discount (<?= $po->discount_percent + 0 ?>%):</td>
                                <td>₹<?= number_format($po->discount, 2) ?></td>
                            </tr>
                            <tr class="fs-5 border-top fw-bold text-primary">
                                <td class="pt-3">Total Amount:</td>
                                <td class="pt-3">₹<?= number_format($po->total_amount, 2) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
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
