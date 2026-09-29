<?php
$currentUrl = current_url();
$uri = service('uri');
$segment1 = $uri->getSegment(1);
$segment2 = $uri->getSegment(2);
?>
<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">M</div>
        <span class="brand-text">MallInventory</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- MAIN -->
        <div class="menu-label">Main</div>
        <a href="<?= base_url('dashboard') ?>" class="menu-item <?= $segment1 === 'dashboard' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-th-large"></i></span>
            <span class="menu-text">Dashboard</span>
        </a>

        <!-- INVENTORY -->
        <?php 
        $hasProducts = has_module_access('products') || has_module_access('categories') || has_module_access('brands') || has_module_access('units') || has_module_access('taxes');
        $hasStock = has_module_access('stock') || has_module_access('warehouses') || has_module_access('stock_transfers') || has_module_access('stock_adjustments');
        ?>
        <?php if ($hasProducts || $hasStock): ?>
        <div class="menu-label">Inventory</div>
        <?php if ($hasProducts): ?>
        <div class="menu-group <?= in_array($segment1, ['products', 'categories', 'brands', 'units', 'taxes', 'barcodes']) ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-box"></i></span>
                <span class="menu-text">Products</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <?php if (has_module_access('products')): ?>
                <a href="<?= base_url('products') ?>" class="menu-item <?= $segment1 === 'products' && !$segment2 ? 'active' : '' ?>">
                    <span class="menu-text">All Products</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('categories')): ?>
                <a href="<?= base_url('categories') ?>" class="menu-item <?= $segment1 === 'categories' ? 'active' : '' ?>">
                    <span class="menu-text">Categories</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('brands')): ?>
                <a href="<?= base_url('brands') ?>" class="menu-item <?= $segment1 === 'brands' ? 'active' : '' ?>">
                    <span class="menu-text">Brands</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('units')): ?>
                <a href="<?= base_url('units') ?>" class="menu-item <?= $segment1 === 'units' ? 'active' : '' ?>">
                    <span class="menu-text">Units</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('taxes')): ?>
                <a href="<?= base_url('taxes') ?>" class="menu-item <?= $segment1 === 'taxes' ? 'active' : '' ?>">
                    <span class="menu-text">Taxes</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($hasStock): ?>
        <div class="menu-group <?= in_array($segment1, ['stock', 'warehouses', 'stock-transfers', 'stock-adjustments', 'batches']) ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-warehouse"></i></span>
                <span class="menu-text">Inventory</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <?php if (has_module_access('stock')): ?>
                <a href="<?= base_url('stock') ?>" class="menu-item <?= $segment1 === 'stock' ? 'active' : '' ?>">
                    <span class="menu-text">Stock Overview</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('warehouses')): ?>
                <a href="<?= base_url('warehouses') ?>" class="menu-item <?= $segment1 === 'warehouses' ? 'active' : '' ?>">
                    <span class="menu-text">Warehouses</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('stock_transfers')): ?>
                <a href="<?= base_url('stock-transfers') ?>" class="menu-item <?= $segment1 === 'stock-transfers' ? 'active' : '' ?>">
                    <span class="menu-text">Stock Transfers</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('stock_adjustments')): ?>
                <a href="<?= base_url('stock-adjustments') ?>" class="menu-item <?= $segment1 === 'stock-adjustments' ? 'active' : '' ?>">
                    <span class="menu-text">Adjustments</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- SALES -->
        <?php
        $hasPos = has_module_access('pos');
        $hasSalesGroup = has_module_access('sales') || has_module_access('quotations') || has_module_access('sales_returns');
        ?>
        <?php if ($hasPos || $hasSalesGroup): ?>
        <div class="menu-label">Sales</div>
        <?php if ($hasPos): ?>
        <a href="<?= base_url('pos') ?>" class="menu-item <?= $segment1 === 'pos' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-cash-register"></i></span>
            <span class="menu-text">POS Billing</span>
            <span class="menu-badge">New</span>
        </a>
        <?php endif; ?>
        <?php if ($hasSalesGroup): ?>
        <div class="menu-group <?= in_array($segment1, ['quotations', 'sales', 'sales-returns', 'inspections']) ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-shopping-cart"></i></span>
                <span class="menu-text">Sales</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <?php if (has_module_access('quotations')): ?>
                <a href="<?= base_url('quotations') ?>" class="menu-item <?= $segment1 === 'quotations' ? 'active' : '' ?>">
                    <span class="menu-text">Quotations</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('sales')): ?>
                <a href="<?= base_url('sales') ?>" class="menu-item <?= $segment1 === 'sales' ? 'active' : '' ?>">
                    <span class="menu-text">Sales Invoices</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('sales_returns')): ?>
                <a href="<?= base_url('sales-returns') ?>" class="menu-item <?= $segment1 === 'sales-returns' ? 'active' : '' ?>">
                    <span class="menu-text">Sales Returns</span>
                </a>
                <a href="<?= base_url('inspections') ?>" class="menu-item <?= $segment1 === 'inspections' ? 'active' : '' ?>">
                    <span class="menu-text">QC Inspections</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- PURCHASE -->
        <?php if (has_module_access('purchases') || has_module_access('purchase_orders') || has_module_access('purchase_returns')): ?>
        <div class="menu-label">Purchase</div>
        <div class="menu-group <?= in_array($segment1, ['purchase-orders', 'purchases', 'purchase-returns']) ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-truck"></i></span>
                <span class="menu-text">Purchases</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <?php if (has_module_access('purchase_orders')): ?>
                <a href="<?= base_url('purchase-orders') ?>" class="menu-item <?= $segment1 === 'purchase-orders' ? 'active' : '' ?>">
                    <span class="menu-text">Purchase Orders</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('purchases')): ?>
                <a href="<?= base_url('purchases') ?>" class="menu-item <?= $segment1 === 'purchases' ? 'active' : '' ?>">
                    <span class="menu-text">Purchase Invoices</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('purchase_returns')): ?>
                <a href="<?= base_url('purchase-returns') ?>" class="menu-item <?= $segment1 === 'purchase-returns' ? 'active' : '' ?>">
                    <span class="menu-text">Purchase Returns</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- PARTIES -->
        <?php if (has_module_access('customers') || has_module_access('suppliers')): ?>
        <div class="menu-label">Parties</div>
        <?php if (has_module_access('customers')): ?>
        <a href="<?= base_url('customers') ?>" class="menu-item <?= $segment1 === 'customers' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-users"></i></span>
            <span class="menu-text">Customers</span>
        </a>
        <?php endif; ?>
        <?php if (has_module_access('suppliers')): ?>
        <a href="<?= base_url('vendors') ?>" class="menu-item <?= $segment1 === 'vendors' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-handshake"></i></span>
            <span class="menu-text">Vendors (Suppliers)</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- FINANCE -->
        <?php if (has_module_access('payments') || has_module_access('receivables') || has_module_access('payables') || has_module_access('expenses')): ?>
        <div class="menu-label">Finance</div>
        <div class="menu-group <?= in_array($segment1, ['payments', 'receivables', 'payables', 'expenses', 'ledger']) ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-coins"></i></span>
                <span class="menu-text">Finance</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <?php if (has_module_access('payments')): ?>
                <a href="<?= base_url('payments') ?>" class="menu-item <?= $segment1 === 'payments' ? 'active' : '' ?>">
                    <span class="menu-text">Payments</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('receivables')): ?>
                <a href="<?= base_url('receivables') ?>" class="menu-item <?= $segment1 === 'receivables' ? 'active' : '' ?>">
                    <span class="menu-text">Receivables</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('payables')): ?>
                <a href="<?= base_url('payables') ?>" class="menu-item <?= $segment1 === 'payables' ? 'active' : '' ?>">
                    <span class="menu-text">Payables</span>
                </a>
                <?php endif; ?>
                <?php if (has_module_access('expenses')): ?>
                <a href="<?= base_url('expenses') ?>" class="menu-item <?= $segment1 === 'expenses' ? 'active' : '' ?>">
                    <span class="menu-text">Expenses</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- REPORTS -->
        <?php if (has_module_access('reports')): ?>
        <div class="menu-label">Reports</div>
        <a href="<?= base_url('reports') ?>" class="menu-item <?= $segment1 === 'reports' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-chart-bar"></i></span>
            <span class="menu-text">Reports & Analytics</span>
        </a>
        <?php endif; ?>

        <!-- ADMINISTRATION -->
        <?php if (has_module_access('users') || has_module_access('roles') || has_module_access('branches') || has_module_access('audit_logs')): ?>
        <div class="menu-label">Administration</div>
        <?php if (has_module_access('users')): ?>
        <a href="<?= base_url('users') ?>" class="menu-item <?= $segment1 === 'users' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-user-shield"></i></span>
            <span class="menu-text">Users</span>
        </a>
        <?php endif; ?>
        <?php if (has_module_access('roles')): ?>
        <a href="<?= base_url('roles') ?>" class="menu-item <?= $segment1 === 'roles' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-key"></i></span>
            <span class="menu-text">Roles & Permissions</span>
        </a>
        <?php endif; ?>
        <?php if (has_module_access('branches')): ?>
        <a href="<?= base_url('branches') ?>" class="menu-item <?= $segment1 === 'branches' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-building"></i></span>
            <span class="menu-text">Branches</span>
        </a>
        <?php endif; ?>
        <?php if (has_module_access('audit_logs')): ?>
        <a href="<?= base_url('audit-logs') ?>" class="menu-item <?= $segment1 === 'audit-logs' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-shield-alt text-warning"></i></span>
            <span class="menu-text">Audit Logs</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <a href="<?= base_url('mobile-app') ?>" class="menu-item <?= $segment1 === 'mobile-app' ? 'active' : '' ?>">
            <span class="menu-icon"><i class="fas fa-mobile-alt text-info"></i></span>
            <span class="menu-text">Mobile POS & Hub</span>
            <span class="badge bg-primary" style="font-size: 0.65rem; padding: 2px 6px; margin-left: auto;">v5.0</span>
        </a>

        <?php if (has_module_access('settings') || has_module_access('company')): ?>
        <div class="menu-group <?= $segment1 === 'settings' ? 'open' : '' ?>">
            <div class="menu-item" onclick="toggleMenuGroup(this)">
                <span class="menu-icon"><i class="fas fa-cog"></i></span>
                <span class="menu-text">Settings</span>
                <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <div class="menu-submenu">
                <a href="<?= base_url('settings/company') ?>" class="menu-item <?= $segment2 === 'company' ? 'active' : '' ?>">
                    <span class="menu-text">Company Profile</span>
                </a>
                <a href="<?= base_url('settings/general') ?>" class="menu-item <?= $segment2 === 'general' ? 'active' : '' ?>">
                    <span class="menu-text">General Settings</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="<?= base_url('logout') ?>" class="menu-item" style="margin:0; color: var(--danger);">
            <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
            <span class="menu-text">Logout</span>
        </a>
    </div>
</aside>
