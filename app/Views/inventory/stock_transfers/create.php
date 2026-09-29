<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-truck-loading" style="color:var(--primary-light);margin-right:8px;"></i> Create Stock Transfer</h1>
        <p>Transfer products between warehouses</p>
    </div>
    <a href="<?= base_url('stock-transfers') ?>" class="btn btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <form id="transferForm">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Reference No *</label>
                <input type="text" name="reference_no" class="form-control" value="<?= esc($reference_no) ?>" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Transfer Date *</label>
                <input type="date" name="transfer_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">From Warehouse *</label>
                <select name="from_warehouse_id" class="form-control" required>
                    <option value="">Select Source Warehouse</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= $w->id ?>"><?= esc($w->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">To Warehouse *</label>
                <select name="to_warehouse_id" class="form-control" required>
                    <option value="">Select Destination Warehouse</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= $w->id ?>"><?= esc($w->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h4 style="margin: 24px 0 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Products to Transfer</h4>
        
        <table class="data-table" id="productsTable" style="margin-bottom: 16px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="width:150px;">Quantity</th>
                    <th style="width:80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="product_id[]" class="form-control" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p->id ?>"><?= esc($p->name) ?> (<?= esc($p->sku) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control" min="1" step="1" onkeydown="if(event.key==='-'||event.key==='e'||event.key==='E'||event.key==='.') event.preventDefault();" required>
                    </td>
                    <td>
                        <button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-ghost" onclick="addProductRow()" style="margin-bottom: 24px;">
            <i class="fas fa-plus"></i> Add Another Product
        </button>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any additional notes here..."></textarea>
        </div>

        <div class="form-group" style="text-align: right; margin-top: 32px;">
            <button type="button" class="btn btn-primary" onclick="saveTransfer()">
                <i class="fas fa-save"></i> Save Transfer
            </button>
        </div>
    </form>
</div>

<!-- Template for new rows -->
<template id="productRowTemplate">
    <tr>
        <td>
            <select name="product_id[]" class="form-control" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p->id ?>"><?= esc($p->name) ?> (<?= esc($p->sku) ?>)</option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="quantity[]" class="form-control" min="1" step="1" onkeydown="if(event.key==='-'||event.key==='e'||event.key==='E'||event.key==='.') event.preventDefault();" required>
        </td>
        <td>
            <button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function addProductRow() {
        const tbody = document.querySelector('#productsTable tbody');
        const template = document.getElementById('productRowTemplate');
        tbody.appendChild(template.content.cloneNode(true));
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        const tbody = row.closest('tbody');
        if (tbody.children.length > 1) {
            row.remove();
        } else {
            alert('You must have at least one product in the transfer.');
        }
    }

    async function saveTransfer() {
        const form = document.getElementById('transferForm');
        
        // Basic HTML5 validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        
        if (formData.get('from_warehouse_id') === formData.get('to_warehouse_id')) {
            alert('Source and destination warehouses cannot be the same.');
            return;
        }

        const response = await fetch('<?= base_url("stock-transfers") ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            window.location.href = '<?= base_url("stock-transfers") ?>';
        } else {
            let msg = 'Failed to create transfer.\n';
            if (typeof result.message === 'object') {
                for (const key in result.message) {
                    msg += `- ${result.message[key]}\n`;
                }
            } else {
                msg += result.message;
            }
            alert(msg);
        }
    }
</script>
<?= $this->endSection() ?>
