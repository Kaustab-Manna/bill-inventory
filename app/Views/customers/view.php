<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Customer Profile Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="fas fa-user-circle"></i> Customer Profile</h5>
            </div>
            <div class="card-body">
                <h4 class="fw-bold mb-3"><?= esc($customer->name) ?></h4>
                
                <div class="mb-3">
                    <div class="text-muted small">Contact Information</div>
                    <div><i class="fas fa-phone text-secondary"></i> <?= esc($customer->phone ?: 'N/A') ?></div>
                    <div><i class="fas fa-envelope text-secondary"></i> <?= esc($customer->email ?: 'N/A') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Billing Details</div>
                    <div><?= esc($customer->address ?: 'N/A') ?></div>
                    <div><?= esc($customer->city ?: '') ?> <?= esc($customer->state ?: '') ?> <?= esc($customer->pincode ?: '') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">GST / Tax Details</div>
                    <div class="fw-bold"><?= esc($customer->gst_no ?: 'N/A') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Credit Limit</div>
                    <div class="fs-5 text-primary">₹<?= number_format($customer->credit_limit, 2) ?></div>
                </div>

                <div class="mb-3 border-top pt-3">
                    <div class="text-muted small">Outstanding Receivables</div>
                    <?php if ($outstanding > 0): ?>
                        <h3 class="text-danger">₹<?= number_format($outstanding, 2) ?></h3>
                        <?php if ($customer->credit_limit > 0 && $outstanding > $customer->credit_limit): ?>
                            <div class="alert alert-danger p-2 mt-2">
                                <i class="fas fa-exclamation-triangle"></i> Credit limit exceeded!
                            </div>
                        <?php endif; ?>
                    <?php elseif ($outstanding < 0): ?>
                        <h3 class="text-success">₹<?= number_format(abs($outstanding), 2) ?> (Advance)</h3>
                    <?php else: ?>
                        <h3 class="text-success">₹0.00</h3>
                    <?php endif; ?>
                </div>

                <a href="<?= base_url('customers/edit/' . $customer->id) ?>" class="btn btn-outline-primary w-100">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-8">
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Customer Ledger</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">Sales History</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab"><i class="fas fa-folder-open"></i> Documents</button>
            </li>
        </ul>
        
        <div class="tab-content border-start border-end border-bottom bg-white p-3 mb-4" id="myTabContent">
            
            <!-- LEDGER TAB -->
            <div class="tab-pane fade show active" id="ledger" role="tabpanel">
                <h5 class="mb-3">Account Ledger</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Ref No</th>
                                <th class="text-end">Debit (Owes)</th>
                                <th class="text-end">Credit (Paid)</th>
                                <th class="text-end">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($ledger)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No transactions found.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($ledger as $entry): ?>
                                <tr>
                                    <td><?= date('d-m-Y H:i', strtotime($entry['date'])) ?></td>
                                    <td>
                                        <?php if($entry['type'] == 'Invoice'): ?>
                                            <span class="badge bg-primary">Invoice</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Return</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($entry['ref_no']) ?></td>
                                    <td class="text-end text-danger"><?= $entry['debit'] > 0 ? '₹'.number_format($entry['debit'], 2) : '-' ?></td>
                                    <td class="text-end text-success"><?= $entry['credit'] > 0 ? '₹'.number_format($entry['credit'], 2) : '-' ?></td>
                                    <td class="text-end fw-bold">
                                        <?php if ($entry['balance'] > 0): ?>
                                            <span class="text-danger">₹<?= number_format($entry['balance'], 2) ?></span>
                                        <?php elseif ($entry['balance'] < 0): ?>
                                            <span class="text-success">₹<?= number_format(abs($entry['balance']), 2) ?> (Cr)</span>
                                        <?php else: ?>
                                            ₹0.00
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($ledger)): ?>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="text-end">Final Balance:</td>
                                <td class="text-end">
                                    <?php if ($outstanding > 0): ?>
                                        <span class="text-danger">₹<?= number_format($outstanding, 2) ?> Due</span>
                                    <?php else: ?>
                                        <span class="text-success">₹<?= number_format(abs($outstanding), 2) ?> Cr</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- SALES HISTORY TAB -->
            <div class="tab-pane fade" id="sales" role="tabpanel">
                <h5 class="mb-3">Sales Invoices</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sales)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No sales history found.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($sale->sale_date)) ?></td>
                                    <td><?= esc($sale->invoice_no) ?></td>
                                    <td>₹<?= number_format($sale->total_amount, 2) ?></td>
                                    <td>₹<?= number_format($sale->paid_amount, 2) ?></td>
                                    <td>
                                        <?php if ($sale->status == 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($sale->status == 'partial'): ?>
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('sales/view/' . $sale->id) ?>" class="btn btn-sm btn-info" title="View Invoice"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DOCUMENTS TAB -->
            <div class="tab-pane fade" id="docs" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Document Vault</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#uploadForm" id="toggleUploadBtn">
                        <i class="fas fa-upload me-1"></i> Upload File
                    </button>
                </div>

                <div class="collapse mb-4" id="uploadForm">
                    <div class="card card-body" style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-md);">
                        <form action="<?= base_url('documents/upload') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="entity_type" value="customer">
                            <input type="hidden" name="entity_id" value="<?= $customer->id ?>">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Document Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. GST Certificate / KYC Document">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">File (Max 5MB)</label>
                                    <input type="file" name="document" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,image/*">
                                    <small class="text-muted d-block mt-1">Allowed formats: PDF, Word, Excel, CSV, Text, Images (JPG, PNG)</small>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-1"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: var(--bg-surface-hover); color: var(--text-secondary);">
                            <tr>
                                <th>Title</th>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Uploaded On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($documents)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">No documents attached yet.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($documents as $doc): ?>
                                <tr>
                                    <td class="fw-bold"><?= esc($doc->title) ?></td>
                                    <td>
                                        <i class="far fa-file-alt text-primary me-1"></i>
                                        <?= esc($doc->filename) ?>
                                    </td>
                                    <td><?= round($doc->file_size / 1024, 2) ?> KB</td>
                                    <td><?= date('d M Y, H:i', strtotime($doc->created_at)) ?></td>
                                    <td>
                                        <a href="<?= base_url('documents/download/' . $doc->id) ?>" class="btn btn-sm btn-info text-white" title="Download Document"><i class="fas fa-download"></i></a>
                                        <form action="<?= base_url('documents/delete/' . $doc->id) ?>" method="post" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?');" title="Delete Document"><i class="fas fa-trash"></i></button>
                                        </form>
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

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all buttons
            tabButtons.forEach(b => {
                b.classList.remove('active');
            });
            // Add active to clicked
            this.classList.add('active');

            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Show target pane
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if(targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });

    // Auto open docs tab if url hash is #docs
    if (window.location.hash === '#docs') {
        const docsTab = document.getElementById('docs-tab');
        if (docsTab) docsTab.click();
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
