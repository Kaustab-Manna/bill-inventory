<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<style>
    /* POS Container to fill available space in master layout */
    .pos-wrapper {
        display: flex;
        gap: 20px;
        height: calc(100vh - 140px); /* Adjust based on master header height */
        margin: -20px; /* Offset master padding if any */
        padding: 20px;
        background: var(--bg-body);
    }

    .search-bar {
        position: relative;
        margin-bottom: 20px;
    }
    .search-bar i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }
    .search-bar input {
        padding-left: 40px;
        height: 46px;
        font-size: 1rem;
    }

    .pos-products-section {
        flex: 1;
        overflow-y: auto;
        padding-right: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding-bottom: 40px;
    }
    .product-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        border-color: var(--primary);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    .product-card.out-of-stock {
        opacity: 0.6;
        border-color: rgba(239, 68, 68, 0.35);
        cursor: not-allowed;
    }
    .product-card.out-of-stock:hover {
        transform: none;
        box-shadow: none;
        border-color: rgba(239, 68, 68, 0.6);
    }
    .product-name {
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--text-main);
    }
    .product-sku {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-family: monospace;
        margin-bottom: 12px;
    }
    .product-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
        margin-top: auto;
    }

    /* Cart Styles */
    .pos-cart-section {
        width: 380px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .cart-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 10px 20px;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed var(--border-color);
    }
    .cart-item-info {
        flex: 1;
    }
    .cart-item-title {
        font-weight: 600;
        font-size: 0.95rem;
    }
    .cart-item-price {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .qty-btn:hover { background: #e2e8f0; }
    
    .cart-summary {
        background: var(--bg-body);
        padding: 20px;
        border-top: 1px solid var(--border-color);
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95rem;
        color: var(--text-muted);
    }
    .summary-row.total {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }
    
    .checkout-btn {
        width: 100%;
        height: 54px;
        font-size: 1.1rem;
        margin-top: 15px;
    }

    /* Modal Fixes */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.7);
        align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal.show { display: flex !important; }
    .modal-content {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        width: 100%; max-width: 500px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        color: var(--text-primary);
    }
    .modal-header { padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 20px; }
    .modal-footer { padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px; }
    .close-modal { cursor: pointer; font-size: 1.5rem; color: var(--text-muted); }
    .close-modal:hover { color: var(--danger); }
</style>

<div class="pos-wrapper">
    
    <!-- Left Section: Products -->
    <div class="pos-products-section">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search products by name, SKU, or barcode..." onkeyup="filterProducts()">
        </div>

        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $p): ?>
                <?php 
                    $avail = max(0, (float)($p->stock_qty ?? 0) - (float)($p->reserved_qty ?? 0));
                    $isOOS = ($avail <= 0);
                ?>
                <div class="product-card <?= $isOOS ? 'out-of-stock' : '' ?>" 
                     data-name="<?= strtolower(esc($p->name)) ?>" 
                     data-sku="<?= strtolower(esc($p->sku)) ?>" 
                     data-stock="<?= $avail ?>"
                     onclick="addToCart(<?= $p->id ?>, '<?= esc(addslashes($p->name)) ?>', <?= $p->selling_price ?>, <?= $avail ?>)">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px; gap:8px;">
                        <div class="product-name" style="margin:0;"><?= esc($p->name) ?></div>
                        <?php if($isOOS): ?>
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4); font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">OUT OF STOCK</span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.72rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; white-space: nowrap;"><i class="fas fa-boxes"></i> <?= $avail ?> <?= esc($p->unit ?? 'pcs') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-sku"><?= esc($p->sku) ?></div>
                    <div class="product-price">₹<?= number_format($p->selling_price, 2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Section: Cart -->
    <div class="pos-cart-section">
        <div class="cart-header">
            <div style="display:flex; gap:10px; margin-bottom: 12px;">
                <select id="warehouse_id" class="form-control" style="flex:1;" onchange="window.location.href='<?= base_url('pos') ?>?warehouse_id=' + this.value">
                    <?php if (!empty($warehouses)): ?>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh->id ?>" <?= ($warehouse && $warehouse->id == $wh->id) ? 'selected' : '' ?>>
                                Warehouse: <?= esc($wh->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php elseif($warehouse): ?>
                        <option value="<?= $warehouse->id ?>"><?= esc($warehouse->name) ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div style="display:flex; gap:10px; margin-bottom: 12px;">
                <select id="salesperson_id" class="form-control" style="flex:1;">
                    <option value="">No Salesperson (Self)</option>
                    <?php foreach ($salespersons as $sp): ?>
                        <option value="<?= $sp->id ?>"><?= esc($sp->name) ?> (<?= $sp->commission_rate ?>%)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <select id="customer_id" class="form-control" style="flex:1;">
                    <option value="">Walk-in Customer</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c->id ?>"><?= esc($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline" style="padding:0 14px;" onclick="openModal('customerModal')" title="Add Customer">
                    <i class="fas fa-user-plus"></i>
                </button>
            </div>
        </div>

        <div class="cart-items" id="cartItems">
            <!-- Cart items will be rendered here by JS -->
            <div style="text-align:center; color:var(--text-muted); margin-top: 50px;">
                <i class="fas fa-shopping-cart" style="font-size:3rem; opacity:0.2; margin-bottom:15px;"></i>
                <p>Cart is empty</p>
            </div>
        </div>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="summarySubtotal">₹0.00</span>
            </div>
            <div class="summary-row">
                <span>Tax (0%)</span>
                <span id="summaryTax">₹0.00</span>
            </div>
            <div class="summary-row">
                <span>Discount</span>
                <input type="number" id="discountInput" class="form-control" style="width:80px; height:28px; padding:4px; text-align:right;" value="0" min="0" onchange="renderCart()">
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="summaryTotal">₹0.00</span>
            </div>

            <button class="btn btn-success checkout-btn" onclick="openModal('checkoutModal')" id="checkoutBtn" disabled>
                <i class="fas fa-credit-card"></i> Pay Now
            </button>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="customerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Customer</h3>
            <span class="close-modal" onclick="closeModal('customerModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form id="customerForm">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label" style="display:block; margin-bottom:5px; font-size:0.9rem;">Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label" style="display:block; margin-bottom:5px; font-size:0.9rem;">Phone</label>
                    <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label" style="display:block; margin-bottom:5px; font-size:0.9rem;">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('customerModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Complete Payment</h3>
            <span class="close-modal" onclick="closeModal('checkoutModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div style="text-align:center; margin-bottom: 20px;">
                <div style="font-size:0.9rem; color:var(--text-muted);">Amount Due</div>
                <div style="font-size:2.5rem; font-weight:700; color:var(--primary);" id="checkoutTotalDisplay">₹0.00</div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label class="form-label" style="display:block; margin-bottom:5px; font-size:0.9rem;">Payment Method</label>
                <select id="paymentMethod" class="form-control">
                    <option value="cash">Cash</option>
                    <option value="card">Credit Card</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label class="form-label" style="display:block; margin-bottom:5px; font-size:0.9rem;">Amount Paid</label>
                <input type="number" id="amountPaid" class="form-control" style="font-size:1.2rem; font-weight:bold;" step="0.01" min="0">
            </div>
            
            <div id="changeContainer" style="display:none; padding:15px; background:var(--bg-body); border-radius:8px; margin-top:10px;">
                <div style="display:flex; justify-content:space-between; font-weight:600;">
                    <span>Change Due:</span>
                    <span id="changeAmount" style="color:var(--success);">₹0.00</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('checkoutModal')">Cancel</button>
            <button class="btn btn-success" onclick="processCheckout()" id="confirmCheckoutBtn">Confirm Sale</button>
        </div>
    </div>
</div>

<!-- UPI QR Code Modal -->
<div id="upiModal" class="modal">
    <div class="modal-content" style="max-width: 350px; text-align: center;">
        <div class="modal-header">
            <h3>Scan to Pay</h3>
            <span class="close-modal" onclick="closeModal('upiModal'); openModal('checkoutModal');">&times;</span>
        </div>
        <div class="modal-body">
            <h2 id="upiAmountDisplay" style="color:var(--primary); margin-bottom:15px; font-weight:700;">₹0.00</h2>
            <img id="upiQrImage" src="" alt="UPI QR Code" style="width: 220px; height: 220px; border-radius: 12px; margin: 0 auto; display: block; border: 1px solid var(--border-color); padding: 10px; background: white;">
            <p style="margin-top: 15px; font-size: 0.9rem; color: var(--text-muted);">Scan this QR code with any UPI app (GPay, PhonePe, Paytm) to complete the payment.</p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button class="btn btn-success" style="width: 100%; height: 50px; font-size: 1.1rem;" onclick="submitCheckoutToServer()" id="upiConfirmBtn">Payment Received</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    let cart = [];
    
    function filterProducts() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const sku = card.getAttribute('data-sku');
            if (name.includes(query) || sku.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price, stock) {
        if (stock <= 0) {
            alert('Cannot add to cart: "' + name + '" is OUT OF STOCK!');
            return;
        }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty + 1 > stock) {
                alert('Cannot add more! Only ' + stock + ' available in stock for "' + name + '".');
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, stock: stock });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(item => item.id === id);
        if (item) {
            if (delta > 0 && item.qty + delta > item.stock) {
                alert('Maximum stock limit reached! Only ' + item.stock + ' available in stock.');
                return;
            }
            item.qty += delta;
            if (item.qty <= 0) {
                removeFromCart(id);
                return;
            }
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div style="text-align:center; color:var(--text-muted); margin-top: 50px;">
                    <i class="fas fa-shopping-cart" style="font-size:3rem; opacity:0.2; margin-bottom:15px;"></i>
                    <p>Cart is empty</p>
                </div>
            `;
            document.getElementById('summarySubtotal').innerText = '₹0.00';
            document.getElementById('summaryTotal').innerText = '₹0.00';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let subtotal = 0;

        cart.forEach(item => {
            const itemTotal = item.qty * item.price;
            subtotal += itemTotal;
            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">₹${item.price.toFixed(2)} <span style="font-size:0.75rem; color:var(--text-muted);">(Stock: ${item.stock})</span></div>
                    </div>
                    <div class="cart-item-actions">
                        <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                        <span style="width:30px; text-align:center; font-weight:600;">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        <button class="qty-btn" style="color:var(--danger); border-color:var(--danger); margin-left: 8px;" onclick="removeFromCart(${item.id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        
        const discount = Math.max(0, parseFloat(document.getElementById('discountInput').value) || 0);
        const total = Math.max(0, subtotal - discount);

        document.getElementById('summarySubtotal').innerText = '₹' + subtotal.toFixed(2);
        document.getElementById('summaryTotal').innerText = '₹' + total.toFixed(2);
        
        document.getElementById('checkoutTotalDisplay').innerText = '₹' + total.toFixed(2);
        document.getElementById('amountPaid').value = total.toFixed(2);
        
        checkoutBtn.disabled = false;
        
        // Scroll cart to bottom
        container.scrollTop = container.scrollHeight;
    }

    document.getElementById('amountPaid').addEventListener('input', function() {
        const totalStr = document.getElementById('checkoutTotalDisplay').innerText.replace('₹', '');
        const total = parseFloat(totalStr);
        const paid = parseFloat(this.value) || 0;
        
        const changeContainer = document.getElementById('changeContainer');
        const changeAmount = document.getElementById('changeAmount');
        
        if (paid > total) {
            changeContainer.style.display = 'block';
            changeAmount.innerText = '₹' + (paid - total).toFixed(2);
        } else {
            changeContainer.style.display = 'none';
        }
    });

    function processCheckout() {
        const method = document.getElementById('paymentMethod').value;
        const totalStr = document.getElementById('summaryTotal').innerText.replace('₹', '');
        
        if (method === 'upi') {
            // Hide checkout modal temporarily
            closeModal('checkoutModal');
            
            // Set Amount Display
            document.getElementById('upiAmountDisplay').innerText = '₹' + parseFloat(totalStr).toFixed(2);
            
            // Generate QR Code using a generic merchant UPI string
            // In a real app, replace pa=merchant@upi with the actual business UPI ID
            const upiUrl = encodeURIComponent(`upi://pay?pa=merchant@upi&pn=BillInventory&am=${totalStr}&cu=INR`);
            document.getElementById('upiQrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${upiUrl}`;
            
            // Open UPI Modal
            openModal('upiModal');
        } else {
            submitCheckoutToServer();
        }
    }

    async function submitCheckoutToServer() {
        const method = document.getElementById('paymentMethod').value;
        const btn = method === 'upi' ? document.getElementById('upiConfirmBtn') : document.getElementById('confirmCheckoutBtn');
        
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const subtotalStr = document.getElementById('summarySubtotal').innerText.replace('₹', '');
        const totalStr = document.getElementById('summaryTotal').innerText.replace('₹', '');
        
        const formData = new FormData();
        formData.append('cart', JSON.stringify(cart));
        formData.append('customer_id', document.getElementById('customer_id').value);
        formData.append('salesperson_id', document.getElementById('salesperson_id').value);
        formData.append('warehouse_id', document.getElementById('warehouse_id').value);
        formData.append('subtotal', subtotalStr);
        formData.append('tax_amount', 0);
        formData.append('discount', Math.max(0, parseFloat(document.getElementById('discountInput').value) || 0));
        formData.append('total_amount', totalStr);
        formData.append('paid_amount', Math.max(0, parseFloat(document.getElementById('amountPaid').value) || 0));
        formData.append('payment_method', method);

        try {
            const response = await fetch('<?= base_url("pos/checkout") ?>', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                // Open the receipt/bill in a new tab
                window.open('<?= base_url("sales/view/") ?>' + result.sale_id, '_blank');
                
                // Reset POS
                cart = [];
                document.getElementById('discountInput').value = 0;
                document.getElementById('amountPaid').value = '';
                document.getElementById('customer_id').value = "";
                renderCart();
                closeModal('checkoutModal');
                closeModal('upiModal');
            } else {
                alert(result.message);
            }
        } catch (e) {
            alert('An error occurred during checkout.');
        }

        btn.disabled = false;
        btn.innerHTML = originalText;
    }

    async function saveCustomer() {
        const form = document.getElementById('customerForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const response = await fetch('<?= base_url("pos/add-customer") ?>', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            const select = document.getElementById('customer_id');
            const option = document.createElement('option');
            option.value = result.customer.id;
            option.text = result.customer.name;
            select.add(option);
            select.value = result.customer.id; // Auto-select
            
            closeModal('customerModal');
            form.reset();
        } else {
            alert(result.message);
        }
    }
</script>
<?= $this->endSection() ?>
