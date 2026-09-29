<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Supplier Profile Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="fas fa-truck"></i> Supplier Profile</h5>
            </div>
            <div class="card-body">
                <h4 class="fw-bold mb-3"><?= esc($vendor->name) ?></h4>
                
                <div class="mb-3">
                    <div class="text-muted small">Contact Person</div>
                    <div class="fw-bold"><?= esc($vendor->contact_person ?: 'N/A') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Contact Information</div>
                    <div><i class="fas fa-phone text-secondary"></i> <?= esc($vendor->phone ?: 'N/A') ?></div>
                    <div><i class="fas fa-envelope text-secondary"></i> <?= esc($vendor->email ?: 'N/A') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Billing Address</div>
                    <div><?= nl2br(esc($vendor->address ?: 'N/A')) ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">GST / Tax Details</div>
                    <div class="fw-bold"><?= esc($vendor->tax_number ?: 'N/A') ?></div>
                </div>

                <div class="mb-3 border-top pt-3">
                    <div class="text-muted small">Outstanding Payables</div>
                    <?php if ($outstanding > 0): ?>
                        <h3 class="text-danger">₹<?= number_format($outstanding, 2) ?> (Due)</h3>
                    <?php elseif ($outstanding < 0): ?>
                        <h3 class="text-success">₹<?= number_format(abs($outstanding), 2) ?> (Advance)</h3>
                    <?php else: ?>
                        <h3 class="text-success">₹0.00</h3>
                    <?php endif; ?>
                </div>

                <a href="<?= base_url('vendors/edit/' . $vendor->id) ?>" class="btn btn-outline-primary w-100">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-8">
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Supplier Ledger</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab">Purchase History</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab"><i class="fas fa-folder-open me-1"></i> Documents (<?= count($documents) ?>)</button>
            </li>
        </ul>
        
        <div class="tab-content border-start border-end border-bottom p-3 mb-4" id="myTabContent" style="background: var(--bg-card); border-color: var(--border) !important; color: var(--text-primary); border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
            
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
                                <th class="text-end">Credit (Billed)</th>
                                <th class="text-end">Debit (Paid/Return)</th>
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
                                        <?php if($entry['type'] == 'Purchase'): ?>
                                            <span class="badge bg-primary">Purchase</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Return</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($entry['ref_no']) ?></td>
                                    <td class="text-end text-danger"><?= $entry['credit'] > 0 ? '₹'.number_format($entry['credit'], 2) : '-' ?></td>
                                    <td class="text-end text-success"><?= $entry['debit'] > 0 ? '₹'.number_format($entry['debit'], 2) : '-' ?></td>
                                    <td class="text-end fw-bold">
                                        <?php if ($entry['balance'] > 0): ?>
                                            <span class="text-danger">₹<?= number_format($entry['balance'], 2) ?> Cr</span>
                                        <?php elseif ($entry['balance'] < 0): ?>
                                            <span class="text-success">₹<?= number_format(abs($entry['balance']), 2) ?> Dr</span>
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
                                        <span class="text-success">₹<?= number_format(abs($outstanding), 2) ?> Advance</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- PURCHASES HISTORY TAB -->
            <div class="tab-pane fade" id="purchases" role="tabpanel">
                <h5 class="mb-3">Purchase Invoices</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Ref No</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($purchases)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No purchase history found.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($purchase->purchase_date)) ?></td>
                                    <td><?= esc($purchase->invoice_no ?? $purchase->reference_no ?? ('PUR-' . $purchase->id)) ?></td>
                                    <td>₹<?= number_format($purchase->total_amount, 2) ?></td>
                                    <td>₹<?= number_format($purchase->paid_amount, 2) ?></td>
                                    <td>
                                        <?php 
                                            $st = $purchase->payment_status ?? $purchase->status ?? 'unpaid';
                                            if ($st == 'paid' || $st == 'received'): 
                                        ?>
                                            <span class="badge bg-success"><?= ucfirst($st) ?></span>
                                        <?php elseif ($st == 'partial'): ?>
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?= ucfirst($st) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('purchases/view/' . $purchase->id) ?>" class="btn btn-sm btn-info" title="View Purchase"><i class="fas fa-eye"></i></a>
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
                            <input type="hidden" name="entity_type" value="vendor">
                            <input type="hidden" name="entity_id" value="<?= $vendor->id ?>">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Document Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Vendor Agreement / Tax Form">
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
