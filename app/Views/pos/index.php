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

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    .modal-overlay.active, .modal-overlay.show {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: all !important;
    }
    .modal-overlay .modal {
        background: var(--bg-card, #1e293b);
        border: 1px solid var(--border-color, #334155);
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        transform: scale(0.96);
        transition: transform 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .modal-overlay.active .modal, .modal-overlay.show .modal {
        transform: scale(1) !important;
    }
    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color, #334155);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main, #f8fafc);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted, #94a3b8);
        font-size: 1.25rem;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .modal-close:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color, #334155);
        background: var(--bg-body, #0f172a);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
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
                     data-id="<?= $p->id ?>"
                     data-title="<?= esc($p->name) ?>"
                     data-name="<?= strtolower(esc($p->name)) ?>" 
                     data-sku="<?= strtolower(esc($p->sku ?? '')) ?>" 
                     data-price="<?= (float)$p->selling_price ?>"
                     data-tax-rate="<?= (float)($p->tax_rate ?? 0) ?>"
                     data-tax-type="<?= esc($p->tax_type ?? 'exclusive') ?>"
                     data-tax-name="<?= esc($p->tax_name ?? '') ?>"
                     data-stock="<?= $avail ?>"
                     onclick="addToCartFromElement(this)">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px; gap:8px;">
                        <div class="product-name" style="margin:0;"><?= esc($p->name) ?></div>
                        <?php if($isOOS): ?>
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4); font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">OUT OF STOCK</span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.72rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; white-space: nowrap;"><i class="fas fa-boxes"></i> <?= $avail ?> <?= esc($p->unit ?? 'pcs') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-sku"><?= esc($p->sku ?? '') ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-top:auto;">
                        <div class="product-price">₹<?= number_format($p->selling_price, 2) ?></div>
                        <?php if(!empty($p->tax_rate) && (float)$p->tax_rate > 0): ?>
                            <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; font-size: 0.7rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                                +<?= $p->tax_rate + 0 ?>% <?= esc($p->tax_name ?? 'GST') ?>
                            </span>
                        <?php endif; ?>
                    </div>
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
                <button type="button" class="btn btn-outline" style="padding:0 14px;" onclick="openModal('customerModal')" title="Add Customer">
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
                <span id="summaryTaxLabel">GST( inclu. all tax )</span>
                <span id="summaryTax">₹0.00</span>
            </div>
            <div class="summary-row">
                <span>Discount</span>
                <input type="number" id="discountInput" class="form-control" style="width:80px; height:28px; padding:4px; text-align:right;" value="0" min="0" oninput="renderCart()" onchange="renderCart()">
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="summaryTotal">₹0.00</span>
            </div>

            <button type="button" class="btn btn-success checkout-btn" onclick="openCheckoutModal()" id="checkoutBtn">
                <i class="fas fa-credit-card"></i> Pay Now
            </button>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal-overlay" id="customerModal">
    <div class="modal" style="max-width: 480px;">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus" style="color:var(--primary);"></i> Add New Customer</h3>
            <button type="button" class="modal-close" onclick="closeModal('customerModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="customerForm">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">Customer Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter customer name" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Optional email address">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('customerModal')">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveCustomer()">
                <i class="fas fa-save"></i> Save Customer
            </button>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal-overlay" id="checkoutModal">
    <div class="modal" style="max-width: 520px;">
        <div class="modal-header">
            <h3><i class="fas fa-cash-register" style="color:var(--primary);"></i> Complete Payment</h3>
            <button type="button" class="modal-close" onclick="closeModal('checkoutModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="text-align:center; margin-bottom: 20px; padding: 18px; background: var(--bg-body); border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size:0.9rem; color:var(--text-muted); font-weight: 500;">Amount Due</div>
                <div style="font-size:2.6rem; font-weight:800; color:var(--primary); line-height: 1.2;" id="checkoutTotalDisplay">₹0.00</div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">Payment Method</label>
                <select id="paymentMethod" class="form-control" style="height: 44px; font-size: 1rem;">
                    <option value="cash">💵 Cash</option>
                    <option value="card">💳 Credit / Debit Card</option>
                    <option value="upi">📱 UPI / QR Code</option>
                    <option value="bank_transfer">🏦 Bank Transfer</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">Amount Paid</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%); font-weight:700; color:var(--text-muted); font-size:1.1rem;">₹</span>
                    <input type="number" id="amountPaid" class="form-control" style="padding-left:32px; font-size:1.25rem; font-weight:700; height:46px;" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            
            <div id="changeContainer" style="display:none; padding:15px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.35); border-radius:10px; margin-top:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-weight:700;">
                    <span style="color:var(--text-main);">Change to Return:</span>
                    <span id="changeAmount" style="color:var(--success); font-size: 1.35rem;">₹0.00</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('checkoutModal')">Cancel</button>
            <button type="button" class="btn btn-success" onclick="processCheckout()" id="confirmCheckoutBtn" style="min-width: 140px; font-weight: 600; height: 42px;">
                <i class="fas fa-check-circle"></i> Confirm Sale
            </button>
        </div>
    </div>
</div>

<!-- UPI QR Code Modal -->
<div class="modal-overlay" id="upiModal">
    <div class="modal" style="max-width: 380px; text-align: center;">
        <div class="modal-header">
            <h3 style="width:100%; text-align:center;"><i class="fas fa-qrcode" style="color:var(--primary);"></i> Scan to Pay</h3>
            <button type="button" class="modal-close" onclick="closeModal('upiModal'); openModal('checkoutModal');"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="font-size:0.9rem; color:var(--text-muted); margin-bottom:4px;">Amount Payable</div>
            <h2 id="upiAmountDisplay" style="color:var(--primary); margin-bottom:16px; font-weight:800; font-size:2.2rem;">₹0.00</h2>
            <div style="background:white; display:inline-block; padding:12px; border-radius:16px; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                <img id="upiQrImage" src="" alt="UPI QR Code" style="width: 220px; height: 220px; display: block; border-radius: 8px;">
            </div>
            <p style="margin-top: 16px; font-size: 0.85rem; color: var(--text-muted); line-height: 1.4;">Scan this QR code with any UPI app (Google Pay, PhonePe, Paytm) to complete the payment.</p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-success" style="width: 100%; height: 48px; font-size: 1.05rem; font-weight: 600;" onclick="submitCheckoutToServer()" id="upiConfirmBtn">
                <i class="fas fa-check-circle"></i> Payment Received
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('active');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    let cart = [];

    function filterProducts() {
        const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const sku = (card.getAttribute('data-sku') || '').toLowerCase();
            if (name.includes(query) || sku.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCartFromElement(el) {
        const id = parseInt(el.getAttribute('data-id'), 10);
        const name = el.getAttribute('data-title') || el.getAttribute('data-name');
        const price = parseFloat(el.getAttribute('data-price')) || 0;
        const stock = parseFloat(el.getAttribute('data-stock')) || 0;
        const taxRate = parseFloat(el.getAttribute('data-tax-rate')) || 0;
        const taxType = el.getAttribute('data-tax-type') || 'exclusive';
        const taxName = el.getAttribute('data-tax-name') || '';
        addToCart(id, name, price, stock, taxRate, taxType, taxName);
    }

    function addToCart(id, name, price, stock, taxRate = 0, taxType = 'exclusive', taxName = '') {
        if (stock <= 0) {
            notifyMessage('Cannot add to cart: "' + name + '" is OUT OF STOCK!', 'warning');
            return;
        }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty + 1 > stock) {
                notifyMessage('Cannot add more! Only ' + stock + ' available in stock for "' + name + '".', 'warning');
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, stock: stock, tax_rate: taxRate, tax_type: taxType, tax_name: taxName });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(item => item.id === id);
        if (item) {
            if (delta > 0 && item.qty + delta > item.stock) {
                notifyMessage('Maximum stock limit reached! Only ' + item.stock + ' available in stock.', 'warning');
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

    function getCartTotals() {
        let subtotal = 0;
        let totalTax = 0;

        cart.forEach(item => {
            const lineNet = item.qty * item.price;
            subtotal += lineNet;
            
            const taxRate = parseFloat(item.tax_rate) || 0;
            const taxType = (item.tax_type || 'exclusive').toLowerCase();
            
            if (taxRate > 0) {
                if (taxType === 'inclusive') {
                    totalTax += lineNet - (lineNet / (1 + (taxRate / 100)));
                } else {
                    totalTax += (lineNet * taxRate) / 100;
                }
            }
        });

        const discountInput = document.getElementById('discountInput');
        const discount = Math.max(0, parseFloat(discountInput ? discountInput.value : 0) || 0);
        const total = Math.max(0, (subtotal + totalTax) - discount);
        return { subtotal, totalTax, discount, total };
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const { subtotal, totalTax, total } = getCartTotals();
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div style="text-align:center; color:var(--text-muted); margin-top: 50px;">
                    <i class="fas fa-shopping-cart" style="font-size:3rem; opacity:0.25; margin-bottom:15px;"></i>
                    <p style="font-weight:500;">Cart is empty</p>
                    <small style="opacity:0.7;">Click on any product to start a sale</small>
                </div>
            `;
            document.getElementById('summarySubtotal').innerText = '₹0.00';
            document.getElementById('summaryTax').innerText = '₹0.00';
            const taxLabel = document.getElementById('summaryTaxLabel');
            if (taxLabel) taxLabel.innerText = 'GST( inclu. all tax )';
            document.getElementById('summaryTotal').innerText = '₹0.00';
            document.getElementById('checkoutTotalDisplay').innerText = '₹0.00';
            if (checkoutBtn) {
                checkoutBtn.style.opacity = '0.65';
            }
            return;
        }

        let html = '';
        cart.forEach(item => {
            const itemTotal = item.qty * item.price;
            const taxRate = parseFloat(item.tax_rate) || 0;
            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${escapeHtml(item.name)}</div>
                        <div class="cart-item-price">
                            ₹${item.price.toFixed(2)} 
                            ${taxRate > 0 ? `<span style="font-size:0.75rem; color:#818cf8; font-weight:600;">(+${taxRate}% ${escapeHtml(item.tax_name || 'GST')})</span>` : ''}
                            <span style="font-size:0.75rem; color:var(--text-muted);">(Stock: ${item.stock})</span>
                        </div>
                    </div>
                    <div class="cart-item-actions">
                        <button type="button" class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                        <span style="width:30px; text-align:center; font-weight:600;">${item.qty}</span>
                        <button type="button" class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        <button type="button" class="qty-btn" style="color:var(--danger, #ef4444); border-color:rgba(239,68,68,0.3); margin-left: 8px;" onclick="removeFromCart(${item.id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        
        document.getElementById('summarySubtotal').innerText = '₹' + subtotal.toFixed(2);
        document.getElementById('summaryTax').innerText = '₹' + totalTax.toFixed(2);
        const taxLabel = document.getElementById('summaryTaxLabel');
        if (taxLabel) {
            taxLabel.innerText = 'GST( inclu. all tax )';
        }
        document.getElementById('summaryTotal').innerText = '₹' + total.toFixed(2);
        document.getElementById('checkoutTotalDisplay').innerText = '₹' + total.toFixed(2);
        
        const amountPaidInput = document.getElementById('amountPaid');
        if (amountPaidInput && (!amountPaidInput.value || parseFloat(amountPaidInput.value) <= 0 || parseFloat(amountPaidInput.dataset.autoFilled) === 1)) {
            amountPaidInput.value = total.toFixed(2);
            amountPaidInput.dataset.autoFilled = "1";
        }
        
        if (checkoutBtn) {
            checkoutBtn.style.opacity = '1';
        }
        
        updateChangeDue();
    }

    function updateChangeDue() {
        const { total } = getCartTotals();
        const paidInput = document.getElementById('amountPaid');
        const paid = parseFloat(paidInput ? paidInput.value : 0) || 0;
        
        const changeContainer = document.getElementById('changeContainer');
        const changeAmount = document.getElementById('changeAmount');
        
        if (paid > total) {
            changeContainer.style.display = 'block';
            changeAmount.innerText = '₹' + (paid - total).toFixed(2);
        } else {
            changeContainer.style.display = 'none';
        }
    }

    const amountPaidEl = document.getElementById('amountPaid');
    if (amountPaidEl) {
        amountPaidEl.addEventListener('input', function() {
            this.dataset.autoFilled = "0";
            updateChangeDue();
        });
    }

    function openCheckoutModal() {
        if (!cart || cart.length === 0) {
            notifyMessage('Your cart is empty! Please click on products to add them to your order first.', 'warning');
            return;
        }

        const whSelect = document.getElementById('warehouse_id');
        if (!whSelect || !whSelect.value) {
            notifyMessage('Please select a warehouse before proceeding.', 'warning');
            return;
        }

        renderCart();
        openModal('checkoutModal');
    }

    function processCheckout() {
        const method = document.getElementById('paymentMethod').value;
        const { total } = getCartTotals();
        
        if (method === 'upi') {
            closeModal('checkoutModal');
            document.getElementById('upiAmountDisplay').innerText = '₹' + total.toFixed(2);
            
            const upiUrl = encodeURIComponent(`upi://pay?pa=merchant@upi&pn=BillInventory&am=${total.toFixed(2)}&cu=INR`);
            document.getElementById('upiQrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${upiUrl}`;
            
            openModal('upiModal');
        } else {
            submitCheckoutToServer();
        }
    }

    async function submitCheckoutToServer() {
        if (!cart || cart.length === 0) {
            notifyMessage('Cart is empty. Add products first!', 'warning');
            return;
        }

        const method = document.getElementById('paymentMethod').value;
        const btn = method === 'upi' ? document.getElementById('upiConfirmBtn') : document.getElementById('confirmCheckoutBtn');
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const { subtotal, totalTax, discount, total } = getCartTotals();
        const paidAmountInput = document.getElementById('amountPaid');
        const paidAmount = parseFloat(paidAmountInput ? paidAmountInput.value : 0) || total;
        
        const customerSelect = document.getElementById('customer_id');
        const salespersonSelect = document.getElementById('salesperson_id');
        const warehouseSelect = document.getElementById('warehouse_id');

        const formData = new FormData();
        formData.append('cart', JSON.stringify(cart));
        formData.append('customer_id', customerSelect ? customerSelect.value : '');
        formData.append('salesperson_id', salespersonSelect ? salespersonSelect.value : '');
        formData.append('warehouse_id', warehouseSelect ? warehouseSelect.value : '');
        formData.append('subtotal', subtotal.toFixed(2));
        formData.append('tax_amount', totalTax.toFixed(2));
        formData.append('discount', discount.toFixed(2));
        formData.append('total_amount', total.toFixed(2));
        formData.append('paid_amount', paidAmount.toFixed(2));
        formData.append('payment_method', method);

        try {
            const response = await fetch('<?= base_url("pos/checkout") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            
            if (result.success) {
                notifyMessage('Sale completed successfully! Invoice #' + (result.invoice_no || result.sale_id), 'success');
                
                // Open the receipt in a new tab if possible
                const receiptUrl = '<?= base_url("sales/view/") ?>' + result.sale_id;
                const win = window.open(receiptUrl, '_blank');
                if (!win) {
                    notifyMessage('Sale created! <a href="' + receiptUrl + '" target="_blank" style="color:#60a5fa; text-decoration:underline; font-weight:600;">Click here to view receipt</a>', 'info', 8000);
                }
                
                // Reset Cart & Inputs
                cart = [];
                const discountInput = document.getElementById('discountInput');
                if (discountInput) discountInput.value = 0;
                if (paidAmountInput) {
                    paidAmountInput.value = '';
                    delete paidAmountInput.dataset.autoFilled;
                }
                if (customerSelect) customerSelect.value = "";
                
                renderCart();
                closeModal('checkoutModal');
                closeModal('upiModal');
            } else {
                notifyMessage(result.message || 'Checkout failed. Please check inputs and stock.', 'error');
            }
        } catch (e) {
            console.error('Checkout error:', e);
            notifyMessage('A network or server error occurred during checkout. Please try again.', 'error');
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
        try {
            const response = await fetch('<?= base_url("pos/add-customer") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (result.success) {
                notifyMessage('Customer added successfully!', 'success');
                const select = document.getElementById('customer_id');
                if (select) {
                    const option = document.createElement('option');
                    option.value = result.customer.id;
                    option.text = result.customer.name;
                    select.add(option);
                    select.value = result.customer.id;
                }
                closeModal('customerModal');
                form.reset();
            } else {
                notifyMessage(result.message || 'Failed to add customer.', 'error');
            }
        } catch (err) {
            console.error('Save customer error:', err);
            notifyMessage('Could not save customer. Please try again.', 'error');
        }
    }

    function notifyMessage(message, type = 'info', duration = 4000) {
        if (typeof showToast === 'function') {
            showToast(message, type, duration);
        } else {
            alert(message);
        }
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }
</script>
<?= $this->endSection() ?>
