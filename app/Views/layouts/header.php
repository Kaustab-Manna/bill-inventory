<header class="header">
    <div class="header-left">
        <div class="page-breadcrumb" style="display:flex; align-items:center; gap:10px;">
            <a href="<?= base_url('dashboard') ?>" title="Go to Dashboard" class="breadcrumb-home-link" style="color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; font-size:1.05rem; transition: all 0.2s;" onmouseover="this.style.color='var(--primary)'; this.style.transform='scale(1.15)';" onmouseout="this.style.color='var(--text-muted)'; this.style.transform='scale(1)';">
                <i class="fas fa-home"></i>
            </a>
            <span style="color:var(--border-light); font-size:0.85rem;">/</span>
            <span style="font-weight:600; color:var(--text-primary); font-size:0.95rem;"><?= esc($pageTitle ?? 'Dashboard') ?></span>
        </div>
    </div>

    <div class="header-right">
        <div class="header-search position-relative">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search modules, products, invoices..." id="globalSearch" autocomplete="off">
            <div id="searchResultsDropdown" class="search-results-dropdown" style="display: none;"></div>
        </div>

        <!-- Night / Light Mode Toggle -->
        <button class="header-btn" id="themeToggleBtn" title="Switch Theme (Light/Night)" onclick="toggleTheme()">
            <i class="fas fa-sun" id="themeIcon"></i>
        </button>

        <div class="dropdown">
            <button class="header-btn" title="Notifications" onclick="toggleDropdown('notifDropdown')">
                <i class="fas fa-bell"></i>
                <?php if(isset($globalUnreadCount) && $globalUnreadCount > 0): ?>
                    <span class="badge bg-danger rounded-circle position-absolute" style="top:-5px; right:-5px; font-size: 0.6rem; padding: 3px 5px;"><?= $globalUnreadCount ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu p-0" id="notifDropdown" style="width: 320px; right: 0; left: auto; max-height: 400px; overflow-y: auto;">
                <div class="bg-primary text-white fw-bold p-3 border-bottom d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    <a href="<?= base_url('notifications/mark-all-read') ?>" class="text-white small text-decoration-none"><i class="fas fa-check-double"></i> Mark all read</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php if(!empty($globalNotifications)): ?>
                        <?php foreach($globalNotifications as $n): ?>
                            <a href="<?= base_url('notifications') ?>" class="list-group-item list-group-item-action p-3 border-bottom <?= $n->is_read ? 'bg-light' : 'bg-dark bg-opacity-25' ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold <?= $n->type == 'low_stock' ? 'text-warning' : 'text-primary' ?>">
                                        <?php if($n->type == 'low_stock'): ?>
                                            <i class="fas fa-exclamation-triangle"></i>
                                        <?php else: ?>
                                            <i class="fas fa-info-circle"></i>
                                        <?php endif; ?>
                                        <?= esc($n->title) ?>
                                    </h6>
                                    <small class="text-muted" style="font-size:0.7rem;"><?= date('d M H:i', strtotime($n->created_at)) ?></small>
                                </div>
                                <p class="mb-0 small text-light"><?= esc($n->message) ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p class="mb-0">No new notifications</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-2 border-top text-center bg-dark">
                    <a href="<?= base_url('notifications') ?>" class="text-decoration-none small fw-bold text-primary">View All Notifications</a>
                </div>
            </div>
        </div>

        <button class="header-btn" title="Fullscreen" onclick="toggleFullscreen()">
            <i class="fas fa-expand"></i>
        </button>

        <div class="dropdown">
            <div class="user-dropdown" onclick="toggleDropdown('userDropdown')">
                <div class="user-avatar">
                    <?= strtoupper(substr(session()->get('user_name') ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= esc(session()->get('user_name') ?? 'Admin') ?></div>
                    <div class="user-role"><?= esc(session()->get('role_display_name') ?? 'Super Admin') ?></div>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.65rem; color:var(--text-muted);"></i>
            </div>
            <div class="dropdown-menu" id="userDropdown">
                <a href="<?= base_url('settings/company') ?>" class="dropdown-item">
                    <i class="fas fa-building"></i> Company Profile
                </a>
                <a href="<?= base_url('settings/general') ?>" class="dropdown-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('logout') ?>" class="dropdown-item" style="color:var(--danger);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>
