<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-9">
        <div class="card" id="printArea">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                    <div>
                        <h2 class="text-danger mb-1 fw-bold">SALES RETURN</h2>
                        <div class="text-muted fs-5">#<?= esc($return->return_no) ?></div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-1">Inventory System</h4>
                        <div class="text-muted">
                            Warehouse: <?= esc($return->warehouse_name ?? 'N/A') ?>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted mb-2">Customer Info:</div>
                        <h5 class="fw-bold mb-1"><?= esc($return->customer_name ?? 'Walk-in Customer') ?></h5>
                        <?php if (!empty($return->customer_id)): ?>
                            <?php if (!empty($return->address)): ?><div><?= esc($return->address) ?></div><?php endif; ?>
                            <?php if (!empty($return->phone)): ?><div>Phone: <?= esc($return->phone) ?></div><?php endif; ?>
                            <?php if (!empty($return->email)): ?><div>Email: <?= esc($return->email) ?></div><?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 text-end">
                        <div class="mb-2"><span class="text-muted me-2">Date:</span> <?= date('d M Y, h:i A', strtotime($return->return_date)) ?></div>
                        <div class="mb-2">
                            <span class="text-muted me-2">Status:</span> 
                            <span class="badge bg-success fs-6">Completed</span>
                        </div>
                        <?php if (!empty($return->original_invoice_no)): ?>
                            <div>
                                <span class="text-muted me-2">Original Invoice:</span>
                                <a href="<?= base_url('sales/view/' . $return->sale_id) ?>" class="badge bg-primary text-decoration-none fs-6">
                                    <i class="fas fa-file-invoice me-1"></i><?= esc($return->original_invoice_no) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Items -->
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-bordered align-middle">
                        <thead style="background: rgba(255, 255, 255, 0.04); border-color: var(--border, #334155);">
                            <tr style="color: #F1F5F9;">
                                <th class="text-center" width="40" style="color: #F1F5F9;">#</th>
                                <th style="color: #F1F5F9;">Product Returned</th>
                                <th class="text-center" width="160" style="color: #F1F5F9;">Condition & QC</th>
                                <th class="text-end" width="120" style="color: #F1F5F9;">Unit Price (₹)</th>
                                <th class="text-center" width="70" style="color: #F1F5F9;">Qty</th>
                                <th class="text-end" width="120" style="color: #F1F5F9;">Subtotal (₹)</th>
                                <th class="text-end" width="130" style="color: #F1F5F9;">GST( inclu. all tax )</th>
                                <th class="text-end" width="130" style="color: #F1F5F9;">Refund (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td>
                                        <div class="fw-bold" style="color: #F1F5F9;"><?= esc($item->product_name) ?></div>
                                        <?php if (!empty($item->sku)): ?>
                                            <small style="color: #94A3B8;">SKU: <?= esc($item->sku) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($item->item_condition ?? '') === 'broken_seal'): ?>
                                            <?php if (($item->inspection_status ?? '') === 'passed'): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> QC Passed</span>
                                            <?php elseif (($item->inspection_status ?? '') === 'failed'): ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> QC Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> QC Pending</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Seal Intact</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end" style="color: #F1F5F9;"><?= number_format($item->unit_price, 2) ?></td>
                                    <td class="text-center fw-semibold" style="color: #F1F5F9;"><?= $item->quantity + 0 ?></td>
                                    <td class="text-end" style="color: #F1F5F9;"><?= number_format($item->subtotal, 2) ?></td>
                                    <td class="text-end text-primary fw-semibold"><?= number_format($item->tax_amount, 2) ?></td>
                                    <td class="text-end text-danger fw-bold">- <?= number_format($item->total, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-sm-6">
                        <?php if(!empty($return->notes)): ?>
                        <div class="card border-0" style="background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border, #334155) !important;">
                            <div class="card-body p-3">
                                <strong style="color: #F1F5F9;">Return Reason / Notes:</strong>
                                <div class="mt-1" style="color: #CBD5E1;"><?= nl2br(esc($return->notes)) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-sm table-borderless text-end">
                            <tr>
                                <td style="color: #94A3B8;">Subtotal:</td>
                                <td class="fw-semibold" style="color: #F1F5F9;">₹<?= number_format($return->subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <td style="color: #818CF8;">GST( inclu. all tax ):</td>
                                <td class="fw-semibold text-primary">₹<?= number_format($return->tax_amount, 2) ?></td>
                            </tr>
                            <tr class="fs-5 border-top fw-bold text-danger">
                                <td class="pt-3">Total Refund Amount:</td>
                                <td class="pt-3">₹<?= number_format($return->total_amount, 2) ?></td>
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
                <button class="btn btn-primary w-100 mb-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Return Note
                </button>
                <a href="<?= base_url('inspections') ?>" class="btn btn-warning w-100 mb-2">
                    <i class="fas fa-clipboard-check me-1"></i> Go to QC Inspections
                </a>
                <a href="<?= base_url('sales-returns') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-1"></i> Back to Returns List
                </a>
            </div>
        </div>
        
        <div class="card border-danger">
            <div class="card-body">
                <a href="<?= base_url('sales-returns/delete/' . $return->id) ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this return record? The items will be deducted from your warehouse stock.');">
                    <i class="fas fa-trash me-1"></i> Delete Record
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
