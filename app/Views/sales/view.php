<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-9">
        <div class="card" id="printArea">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-5 pb-3 border-bottom">
                    <div>
                        <h2 class="text-primary mb-1 fw-bold">RETAIL INVOICE</h2>
                        <div class="text-muted fs-5">#<?= esc($sale->invoice_no) ?></div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-1">BillInventory</h4>
                        <div class="text-muted">
                            Warehouse: <?= esc($sale->warehouse_name ?? 'N/A') ?>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <div class="text-muted mb-2">Billed To:</div>
                        <h5 class="fw-bold mb-1"><?= esc($sale->customer_name ?? 'Walk-in Customer') ?></h5>
                        <?php if (!empty($sale->customer_id)): ?>
                            <?php $addr = $sale->customer_address ?? $sale->address ?? null; ?>
                            <?php if (!empty($addr)): ?>
                                <div><?= esc($addr) ?></div>
                            <?php endif; ?>
                            <?php $ph = $sale->customer_phone ?? $sale->phone ?? null; ?>
                            <?php if (!empty($ph)): ?>
                                <div>Phone: <?= esc($ph) ?></div>
                            <?php endif; ?>
                            <?php $em = $sale->customer_email ?? $sale->email ?? null; ?>
                            <?php if (!empty($em)): ?>
                                <div>Email: <?= esc($em) ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 text-end">
                        <div class="mb-2"><span class="text-muted me-2">Date:</span> <?= date('d M Y, h:i A', strtotime($sale->sale_date)) ?></div>
                        <div>
                            <span class="text-muted me-2">Status:</span> 
                            <?php if ($sale->status == 'paid'): ?>
                                <span class="badge bg-success fs-6">Paid</span>
                            <?php elseif ($sale->status == 'partial'): ?>
                                <span class="badge bg-warning text-dark fs-6">Partial</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Unpaid</span>
                            <?php endif; ?>
                        </div>
                        <?php if(isset($sale->salesperson_name) && $sale->salesperson_name): ?>
                        <div class="mt-2 text-primary">
                            <span class="text-muted me-2">Sales Executive:</span> 
                            <i class="fas fa-user-tie"></i> <?= esc($sale->salesperson_name) ?>
                        </div>
                        <?php endif; ?>
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
                        <div class="mb-3">
                            <strong>Payment Method:</strong> <?= ucfirst($sale->payment_method ?? 'cash') ?>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless text-end">
                            <tr>
                                <td>Subtotal:</td>
                                <td width="150">₹<?= number_format($sale->subtotal ?? 0, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Discount (<?= ($sale->discount_percent ?? 0) + 0 ?>%):</td>
                                <td>₹<?= number_format($sale->discount ?? 0, 2) ?></td>
                            </tr>
                            <tr class="fs-5 border-top fw-bold text-primary">
                                <td class="pt-3">Total Amount:</td>
                                <td class="pt-3">₹<?= number_format($sale->total_amount, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Amount Paid:</td>
                                <td class="text-success">₹<?= number_format($sale->paid_amount, 2) ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Amount Due:</td>
                                <td class="text-danger">₹<?= number_format(max(0, $sale->total_amount - $sale->paid_amount), 2) ?></td>
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
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="<?= base_url('sales') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Update Payment</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('sales/payment/' . $sale->id) ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label text-muted">Amount Paid (₹)</label>
                        <input type="number" step="0.01" min="0" name="paid_amount" class="form-control" value="<?= $sale->paid_amount ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Update</button>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Share Invoice</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-outline-success w-100 text-start" onclick="sendInvoice('whatsapp')">
                        <i class="fab fa-whatsapp me-2"></i> Send via WhatsApp
                    </button>
                    <button class="btn btn-outline-info w-100 text-start" onclick="sendInvoice('sms')">
                        <i class="fas fa-sms me-2"></i> Send via SMS
                    </button>
                    <button class="btn btn-outline-primary w-100 text-start" onclick="sendInvoice('email')">
                        <i class="fas fa-envelope me-2"></i> Send via Email
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-body">
                <a href="<?= base_url('sales/delete/' . $sale->id) ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this invoice? This will restore the stock quantities.');">
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
<script>
    async function sendInvoice(type) {
        if (!confirm(`Are you sure you want to send this invoice via ${type.toUpperCase()}?`)) return;

        const formData = new FormData();
        formData.append('type', type);

        try {
            const res = await fetch(`<?= base_url('sales/send/' . $sale->id) ?>`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.success) {
                alert(data.message);
            } else {
                alert('Failed: ' + data.message);
            }
        } catch (e) {
            alert('An error occurred while sending the invoice.');
        }
    }
</script>
<?= $this->endSection() ?>
