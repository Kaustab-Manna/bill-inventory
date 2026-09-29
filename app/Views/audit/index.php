<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 text-white"><i class="fas fa-shield-alt text-warning"></i> <?= esc($pageTitle) ?></h4>
        <button class="btn btn-sm btn-light" onclick="exportToPDF()"><i class="fas fa-file-pdf text-danger"></i> Export PDF</button>
    </div>
    <div class="card-body" id="auditLogContent">
        
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <strong>Security Notice:</strong> This audit trail logs all critical actions taken by users across the system. It cannot be deleted or modified.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover data-table align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="15%">Timestamp</th>
                        <th width="15%">User</th>
                        <th width="10%">Module</th>
                        <th width="10%">Action</th>
                        <th width="50%">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No audit logs found.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td>
                                <?php 
                                    $time = strtotime($log->created_at);
                                    if (!$time || date('Y', $time) == '1970'): 
                                ?>
                                    <span class="text-muted fst-italic">Unknown Date</span>
                                <?php else: ?>
                                    <span class="fw-bold"><?= date('d-m-Y', $time) ?></span><br>
                                    <small class="text-muted"><?= date('h:i:s A', $time) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><i class="fas fa-user-circle"></i> <?= esc($log->user_name ?: 'System') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= esc(strtoupper($log->module)) ?></span>
                            </td>
                            <td>
                                <?php 
                                    $actionClass = 'bg-info';
                                    if(strtolower($log->action) == 'delete') $actionClass = 'bg-danger';
                                    if(strtolower($log->action) == 'create' || strtolower($log->action) == 'insert') $actionClass = 'bg-success';
                                    if(strtolower($log->action) == 'update') $actionClass = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $actionClass ?>"><?= esc(strtoupper($log->action)) ?></span>
                            </td>
                            <td>
                                <?php 
                                    $summary = "";
                                    $actionStr = strtolower($log->action);
                                    $moduleStr = strtolower($log->module);

                                    if ($actionStr == 'login') {
                                        $summary = "User securely authenticated and logged into the system.";
                                    } elseif ($actionStr == 'logout') {
                                        $summary = "User ended session and logged out.";
                                    } elseif ($actionStr == 'create' || $actionStr == 'insert') {
                                        $summary = "Created a new entry in {$moduleStr}.";
                                        if ($log->new_data) {
                                            $newData = json_decode($log->new_data, true);
                                            if (is_array($newData)) {
                                                $nameField = $newData['name'] ?? $newData['title'] ?? $newData['invoice_no'] ?? $newData['reference_no'] ?? null;
                                                if ($nameField) {
                                                    $summary = "Created {$moduleStr}: <strong>" . esc($nameField) . "</strong>.";
                                                }
                                            }
                                        }
                                    } elseif ($actionStr == 'delete') {
                                        $summary = "Deleted an existing record from {$moduleStr}.";
                                    } elseif ($actionStr == 'update') {
                                        $summary = "Updated record in {$moduleStr}.";
                                        if ($log->old_data && $log->new_data) {
                                            $oldData = json_decode($log->old_data, true);
                                            $newData = json_decode($log->new_data, true);
                                            $changes = [];
                                            if (is_array($oldData) && is_array($newData)) {
                                                foreach ($newData as $key => $val) {
                                                    if (isset($oldData[$key]) && $oldData[$key] != $val && $key != 'updated_at') {
                                                        $changes[] = "<span class='badge bg-light text-dark border'>" . esc(ucfirst(str_replace('_', ' ', $key))) . "</span>";
                                                    }
                                                }
                                            }
                                            if (count($changes) > 0) {
                                                $summary = "Updated " . count($changes) . " fields in {$moduleStr}: " . implode(' ', $changes);
                                            }
                                        }
                                    } else {
                                        $summary = "Performed {$actionStr} operation on {$moduleStr}.";
                                    }
                                ?>
                                
                                <div style="font-size: 0.9rem;">
                                    <?= $summary ?>
                                </div>
                                
                                <?php if($log->record_id): ?>
                                    <div class="mt-1" style="font-size: 0.8rem; color: var(--text-muted);">
                                        <strong>Record ID:</strong> <?= esc($log->record_id) ?>
                                    </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function exportToPDF() {
        const element = document.getElementById('auditLogContent');
        const opt = {
            margin:       0.5,
            filename:     'Audit_Logs_' + new Date().toISOString().split('T')[0] + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
        };

        // Hide "View Data Changes" buttons during export to clean up the PDF
        const buttons = element.querySelectorAll('button[data-bs-toggle="collapse"]');
        buttons.forEach(btn => btn.style.display = 'none');

        html2pdf().set(opt).from(element).save().then(() => {
            // Restore buttons after PDF is generated
            buttons.forEach(btn => btn.style.display = 'inline-block');
        });
    }
</script>
<?= $this->endSection() ?>
