<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>
<style>
    /* ============================================================
       BILLINVENTORY MOBILE POS & FLEET STUDIO — BESPOKE UI
       ============================================================ */

    .studio-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
        padding-bottom: 40px;
    }

    /* 1. Header Command Ribbon */
    .studio-header {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
        border: 1px solid rgba(99, 102, 241, 0.25);
        border-radius: var(--radius-xl);
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35);
    }
    .studio-header::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    .studio-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .studio-brand-box {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .studio-brand-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
        flex-shrink: 0;
    }

    .studio-title-area h1 {
        font-size: 1.85rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .studio-badge-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 9999px;
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .studio-desc {
        color: #94a3b8;
        font-size: 0.92rem;
        margin: 0;
        max-width: 620px;
        line-height: 1.5;
    }

    .studio-header-meta {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .studio-meta-item {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.82rem;
        color: #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .live-beacon-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: beacon-pulse 2s infinite;
    }
    @keyframes beacon-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* 2. Studio Navigation Pill Bar */
    .studio-nav-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 8px 12px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .studio-nav-pills {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .studio-nav-pill {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .studio-nav-pill:hover {
        background: var(--bg-surface-hover);
        color: var(--text-primary);
    }
    .studio-nav-pill.active {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
    }

    .studio-quick-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* 3. Section 1: Smartphone Showcase & APK Distribution */
    .showcase-layout-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
        align-items: stretch;
    }
    @media (max-width: 960px) {
        .showcase-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Ultra-Realistic Handheld Smartphone Mockup */
    .phone-stage-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 28px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .smartphone-device {
        width: 250px;
        background: #090d16;
        border: 4px solid #334155;
        border-radius: 36px;
        padding: 10px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5), inset 0 0 10px rgba(0, 0, 0, 0.8);
        position: relative;
        overflow: hidden;
    }

    /* Dynamic Island / Notch */
    .smartphone-speaker {
        width: 60px;
        height: 6px;
        background: #1e293b;
        border-radius: 9999px;
        margin: 4px auto 10px auto;
    }

    /* Screen Container */
    .smartphone-screen {
        background: #0f172a;
        border-radius: 26px;
        padding: 12px 10px;
        min-height: 440px;
        display: flex;
        flex-direction: column;
        color: #f8fafc;
        font-size: 0.78rem;
        position: relative;
        overflow: hidden;
    }

    /* Smartphone Status Bar */
    .phone-status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 10px;
        padding: 0 4px;
    }

    /* App Header in Phone */
    .phone-app-header {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 8px 10px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Barcode Scan Laser Animation Area */
    .scan-reticle-box {
        background: #020617;
        border: 1px dashed #475569;
        border-radius: 12px;
        height: 105px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .scan-laser-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: #ef4444;
        box-shadow: 0 0 10px #ef4444, 0 0 20px #ef4444;
        animation: laser-sweep 2.2s infinite ease-in-out;
    }
    @keyframes laser-sweep {
        0% { top: 10%; opacity: 0.2; }
        50% { top: 85%; opacity: 1; }
        100% { top: 10%; opacity: 0.2; }
    }
    .barcode-svg-sim {
        width: 130px;
        opacity: 0.7;
    }

    /* Phone Cart Summary */
    .phone-cart-list {
        background: rgba(30, 41, 59, 0.6);
        border-radius: 10px;
        padding: 8px;
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .phone-cart-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.72rem;
    }

    .phone-checkout-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 8px;
        border-radius: 8px;
        text-align: center;
        border: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        margin-top: auto;
    }

    .phone-hardware-dock {
        font-size: 0.64rem;
        color: #34d399;
        text-align: center;
        margin-top: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    /* Distribution Console (Right Side) */
    .dist-console-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .dist-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 22px;
        gap: 16px;
    }

    .dist-main-box {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 24px;
        align-items: center;
        background: var(--bg-body);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 22px;
        margin-bottom: 24px;
    }
    @media (max-width: 600px) {
        .dist-main-box {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }

    .qr-frame-box {
        width: 145px;
        height: 145px;
        background: #ffffff;
        border-radius: 16px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
        position: relative;
        margin: 0 auto;
    }

    .dist-specs-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .dist-spec-tag {
        font-size: 0.76rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-apk-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .btn-apk-hero:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 28px rgba(16, 185, 129, 0.55);
        color: #ffffff;
    }

    /* Enterprise Hardware Compatibility Ribbons */
    .hardware-chips-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .hw-chip {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-primary);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: border-color 0.2s;
    }
    .hw-chip:hover {
        border-color: var(--primary);
    }

    /* 4. Telemetry Deck (Distinct horizontal gauge) */
    .telemetry-strip-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
    }

    .telemetry-box {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, border-color 0.2s;
    }
    .telemetry-box:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
    }

    .telemetry-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .telemetry-val {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }
    .telemetry-lbl {
        font-size: 0.82rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
    }

    /* 5. Section 2: Real-time FCM Push Broadcast Studio */
    .broadcast-studio-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 28px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
    }

    .broadcast-split-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        gap: 26px;
        align-items: flex-start;
    }
    @media (max-width: 900px) {
        .broadcast-split-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Notification Simulator Preview */
    .notification-sim-stage {
        background: var(--bg-body);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        position: sticky;
        top: 20px;
    }

    .notification-lock-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        color: #f8fafc;
    }

    .sim-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .sim-app-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }
    .sim-app-logo {
        width: 22px;
        height: 22px;
        background: #6366f1;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #ffffff;
    }

    .sim-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #ffffff;
        margin-bottom: 4px;
    }
    .sim-body {
        font-size: 0.85rem;
        color: #cbd5e1;
        line-height: 1.45;
        margin-bottom: 12px;
    }

    .sim-actions {
        display: flex;
        gap: 8px;
    }
    .sim-action-chip {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #cbd5e1;
    }

    /* 6. Section 3: REST API & Developer Terminal */
    .api-vault-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 28px;
    }

    .terminal-console {
        background: #090d16;
        border: 1px solid #1e293b;
        border-radius: 16px;
        padding: 20px;
        font-family: 'JetBrains Mono', monospace;
        color: #e2e8f0;
    }

    .terminal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid #1e293b;
        margin-bottom: 16px;
    }

    .terminal-dots {
        display: flex;
        gap: 6px;
    }
    .t-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .t-dot-red { background: #ef4444; }
    .t-dot-yellow { background: #f59e0b; }
    .t-dot-green { background: #10b981; }

    .terminal-code-block {
        background: #020617;
        border: 1px solid #1e293b;
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 0.82rem;
        line-height: 1.6;
        color: #38bdf8;
        overflow-x: auto;
    }

    .endpoint-tab-group {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .endpoint-tab {
        background: #1e293b;
        border: 1px solid #334155;
        color: #94a3b8;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .endpoint-tab.active, .endpoint-tab:hover {
        background: #334155;
        color: #38bdf8;
        border-color: #38bdf8;
    }

    /* 7. Connected Fleet Table */
    .fleet-roster-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .device-avatar-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* 8. Enterprise Support Bar (Replaces floating WhatsApp) */
    .support-enterprise-banner {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: var(--radius-lg);
        padding: 22px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .btn-whatsapp-concierge {
        background: #25d366;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 10px 22px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
        transition: all 0.2s ease;
    }
    .btn-whatsapp-concierge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
        color: #ffffff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="studio-wrapper">

    <!-- 1. Executive Header Ribbon -->
    <div class="studio-header">
        <div class="studio-header-content">
            <div class="studio-brand-box">
                <div class="studio-brand-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="studio-title-area">
                    <h1>
                        Mobile POS & Fleet Hub
                        <span class="studio-badge-pill">Enterprise v5.0.2</span>
                    </h1>
                    <p class="studio-desc">
                        Direct APK distribution, real-time Zebra/Honeywell barcode terminal provisioning, ESC/POS Bluetooth thermal printing, and Google FCM push broadcast engine.
                    </p>
                </div>
            </div>

            <div class="studio-header-meta">
                <div class="studio-meta-item">
                    <span class="live-beacon-dot"></span>
                    <span><strong>Gateway:</strong> Operational (42ms)</span>
                </div>
                <div class="studio-meta-item">
                    <i class="fas fa-shield-alt text-primary"></i>
                    <span><strong>Security:</strong> JWT Bearer TLS 1.3</span>
                </div>
                <div class="studio-meta-item">
                    <i class="fas fa-database text-info"></i>
                    <span><strong>Engine:</strong> SQLite Offline Cache</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Studio Navigation Pill Bar -->
    <div class="studio-nav-bar">
        <div class="studio-nav-pills">
            <a href="#section-distribution" class="studio-nav-pill active" onclick="setActiveTab(this)">
                <i class="fas fa-download"></i> App Deployment & QR
            </a>
            <a href="#section-broadcaster" class="studio-nav-pill" onclick="setActiveTab(this)">
                <i class="fas fa-paper-plane"></i> FCM Push Broadcaster
            </a>
            <a href="#section-api" class="studio-nav-pill" onclick="setActiveTab(this)">
                <i class="fas fa-terminal"></i> REST API & Security
            </a>
            <a href="#section-fleet" class="studio-nav-pill" onclick="setActiveTab(this)">
                <i class="fas fa-network-wired"></i> Handheld Fleet (5 Active)
            </a>
        </div>

        <div class="studio-quick-actions">
            <a href="<?= base_url('mobile-app/download') ?>" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-download me-1"></i> Quick APK
            </a>
            <button type="button" class="btn btn-sm btn-primary" onclick="scrollToSection('section-broadcaster')">
                <i class="fas fa-bolt me-1"></i> Send Push Alert
            </button>
        </div>
    </div>

    <!-- 3. Telemetry Overview Deck -->
    <div class="telemetry-strip-grid">
        <div class="telemetry-box">
            <div class="telemetry-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
                <i class="fas fa-mobile-screen-button"></i>
            </div>
            <div>
                <div class="telemetry-val"><?= esc($activeDevices) ?></div>
                <div class="telemetry-lbl">Enrolled Mobile Terminals</div>
            </div>
        </div>

        <div class="telemetry-box">
            <div class="telemetry-icon" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="fas fa-wifi"></i>
            </div>
            <div>
                <div class="telemetry-val">98.4%</div>
                <div class="telemetry-lbl">Active Telemetry Sync</div>
            </div>
        </div>

        <div class="telemetry-box">
            <div class="telemetry-icon" style="background: rgba(6, 182, 212, 0.12); color: #22d3ee;">
                <i class="fas fa-print"></i>
            </div>
            <div>
                <div class="telemetry-val">ESC/POS</div>
                <div class="telemetry-lbl">Bluetooth 58/80mm Thermal</div>
            </div>
        </div>

        <div class="telemetry-box">
            <div class="telemetry-icon" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24;">
                <i class="fas fa-barcode"></i>
            </div>
            <div>
                <div class="telemetry-val">GS1 Laser</div>
                <div class="telemetry-lbl">Zebra & Honeywell Hardware</div>
            </div>
        </div>
    </div>

    <!-- 4. Section 1: Showcase & Distribution Center -->
    <div id="section-distribution" class="showcase-layout-grid">
        
        <!-- Left: Realistic Mobile POS Smartphone Mockup -->
        <div class="phone-stage-card">
            <div class="smartphone-device">
                <div class="smartphone-speaker"></div>
                <div class="smartphone-screen">
                    <!-- Status Bar -->
                    <div class="phone-status-bar">
                        <span>09:41</span>
                        <div>
                            <i class="fas fa-signal me-1"></i>
                            <i class="fas fa-wifi me-1"></i>
                            <i class="fas fa-battery-full text-success"></i>
                        </div>
                    </div>

                    <!-- App Title -->
                    <div class="phone-app-header">
                        <div style="font-weight:700; color:#ffffff; font-size:0.8rem;">
                            <i class="fas fa-boxes-stacked text-primary me-1"></i> BillInventory Go
                        </div>
                        <span class="badge bg-success" style="font-size:0.6rem;">Online</span>
                    </div>

                    <!-- Barcode Viewfinder with animated red laser line -->
                    <div class="scan-reticle-box">
                        <div class="scan-laser-line"></div>
                        <svg class="barcode-svg-sim" viewBox="0 0 100 30" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0" y="0" width="3" height="30" fill="#ffffff"/>
                            <rect x="6" y="0" width="2" height="30" fill="#ffffff"/>
                            <rect x="11" y="0" width="4" height="30" fill="#ffffff"/>
                            <rect x="18" y="0" width="2" height="30" fill="#ffffff"/>
                            <rect x="23" y="0" width="5" height="30" fill="#ffffff"/>
                            <rect x="31" y="0" width="3" height="30" fill="#ffffff"/>
                            <rect x="37" y="0" width="2" height="30" fill="#ffffff"/>
                            <rect x="42" y="0" width="4" height="30" fill="#ffffff"/>
                            <rect x="49" y="0" width="2" height="30" fill="#ffffff"/>
                            <rect x="54" y="0" width="5" height="30" fill="#ffffff"/>
                            <rect x="62" y="0" width="3" height="30" fill="#ffffff"/>
                            <rect x="68" y="0" width="2" height="30" fill="#ffffff"/>
                            <rect x="73" y="0" width="4" height="30" fill="#ffffff"/>
                            <rect x="80" y="0" width="3" height="30" fill="#ffffff"/>
                            <rect x="86" y="0" width="5" height="30" fill="#ffffff"/>
                            <rect x="94" y="0" width="2" height="30" fill="#ffffff"/>
                        </svg>
                        <span style="font-size:0.65rem; color:#94a3b8; margin-top:4px;">Laser Scan Active: GS1-128</span>
                    </div>

                    <!-- Scanned Cart Items -->
                    <div class="phone-cart-list">
                        <div class="phone-cart-item">
                            <span style="color:#ffffff;">Logitech G304 Mouse</span>
                            <span style="font-weight:700; color:#38bdf8;">₹3,998</span>
                        </div>
                        <div class="phone-cart-item">
                            <span style="color:#ffffff;">Sandisk 128GB USB 3.2</span>
                            <span style="font-weight:700; color:#38bdf8;">₹850</span>
                        </div>
                        <div style="border-top:1px dashed #334155; margin-top:4px; padding-top:4px; display:flex; justify-content:space-between; font-weight:700;">
                            <span>Subtotal (2 Items)</span>
                            <span style="color:#10b981;">₹4,848.00</span>
                        </div>
                    </div>

                    <!-- Touch Checkout -->
                    <button type="button" class="phone-checkout-btn">
                        <i class="fas fa-check-circle me-1"></i> Complete POS Checkout
                    </button>

                    <div class="phone-hardware-dock">
                        <i class="fas fa-print"></i> Paired: Rongta RPP02N (80mm)
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Distribution & Fleet Deployment Console -->
        <div class="dist-console-card">
            <div>
                <div class="dist-header-row">
                    <div>
                        <h2 style="font-size:1.35rem; font-weight:800; color:var(--text-primary); margin:0 0 6px 0;">
                            Enterprise Mobile Deployment & Installation
                        </h2>
                        <p style="color:var(--text-secondary); font-size:0.9rem; margin:0;">
                            Provision dedicated field sales phones, rugged warehouse scanners, and checkout counter tablets with instant APK sideloading.
                        </p>
                    </div>
                    <span class="badge bg-primary" style="font-size:0.75rem; padding:6px 12px;">
                        Build 2026.09.28
                    </span>
                </div>

                <!-- Main QR & Download Panel -->
                <div class="dist-main-box">
                    <div class="qr-frame-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=<?= urlencode(base_url('mobile-app/download')) ?>" 
                             alt="Scan QR for Mobile App" 
                             style="width: 125px; height: 125px; display: block; border-radius: 8px;">
                    </div>

                    <div>
                        <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-primary); margin-bottom:8px;">
                            Scan QR Code with Handheld Camera
                        </h3>
                        
                        <div class="dist-specs-tags">
                            <span class="dist-spec-tag"><i class="fab fa-android text-success"></i> Android 10 to 15+</span>
                            <span class="dist-spec-tag"><i class="fas fa-box text-info"></i> Package: 24.8 MB</span>
                            <span class="dist-spec-tag"><i class="fas fa-shield-halved text-warning"></i> Signed Release APK</span>
                            <span class="dist-spec-tag"><i class="fas fa-fingerprint text-primary"></i> SHA-256 Validated</span>
                        </div>

                        <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                            <a href="<?= base_url('mobile-app/download') ?>" class="btn-apk-hero" id="heroDownloadApkBtn">
                                <i class="fas fa-download"></i> Download Production APK (24.8 MB)
                            </a>
                            <button type="button" class="btn btn-ghost" onclick="copyDownloadUrl()" title="Copy APK Download Link">
                                <i class="fas fa-link me-1"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hardware Compatibility & Profiles -->
            <div>
                <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                    Certified Hardware & MDM Integration:
                </div>
                <div class="hardware-chips-row">
                    <div class="hw-chip">
                        <i class="fas fa-barcode text-primary"></i>
                        <span>Zebra TC21 / TC26 / TC52</span>
                    </div>
                    <div class="hw-chip">
                        <i class="fas fa-scanner-gun text-info"></i>
                        <span>Honeywell ScanPal EDA51</span>
                    </div>
                    <div class="hw-chip">
                        <i class="fas fa-tablet-screen-button text-success"></i>
                        <span>Samsung Tab Active Series</span>
                    </div>
                    <div class="hw-chip">
                        <i class="fas fa-cash-register text-warning"></i>
                        <span>Sunmi POS Android Handhelds</span>
                    </div>
                    <div class="hw-chip">
                        <i class="fas fa-print text-danger"></i>
                        <span>Bluetooth ESC/POS 58/80mm</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 5. Section 2: Live Push Notification Broadcaster Studio -->
    <div id="section-broadcaster" class="broadcast-studio-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
            <div>
                <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:0 0 4px 0;">
                    <i class="fas fa-paper-plane text-primary me-2"></i> Real-Time FCM Push Dispatch Studio
                </h2>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin:0;">
                    Broadcast flash price revisions, inventory arrivals, urgent customer dispatch alerts, and stock requisitions directly to mobile device lock-screens.
                </p>
            </div>
            <span class="badge bg-success" style="padding:6px 12px; font-size:0.82rem;">
                <span class="live-beacon-dot me-1"></span> Google FCM Gateway Connected
            </span>
        </div>

        <div class="broadcast-split-grid">
            <!-- Left: Compose Form -->
            <form id="studioPushForm" onsubmit="handlePushDispatch(event)">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Target Device Fleet</label>
                    <select name="target" class="form-select" id="broadcastTarget">
                        <option value="all">Broadcast to All Connected Handhelds (1,120+ Active)</option>
                        <option value="field_sales">Field Sales Representatives Only</option>
                        <option value="delivery_dispatch">Dispatch & Delivery Fleet Drivers</option>
                        <option value="warehouse_scan">Warehouse Scan Terminal Guns</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Broadcast Subject / Title *</label>
                    <input type="text" name="title" id="broadcastTitle" class="form-control" 
                           placeholder="e.g. Flash Stock Alert: New HP Laptops in Stock" 
                           value="New Stock Alert: HP Pavilion Available" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Push Notification Message Body *</label>
                    <textarea name="message" id="broadcastBody" class="form-control" rows="3" 
                              placeholder="Write clear notification instructions..." required>50 units of HP Pavilion Gaming received in Kolkata Central Warehouse. Ready for sales dispatch.</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Alert Urgency & Sound Level</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="priority" id="urgentHigh" value="high" checked>
                            <label class="form-check-label fw-bold" for="urgentHigh">
                                <span class="badge bg-danger">High Priority (Audible Chime & Heads-up Banner)</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="priority" id="urgentNormal" value="normal">
                            <label class="form-check-label fw-bold" for="urgentNormal">
                                <span class="badge bg-secondary">Normal Notification</span>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="broadcastSubmitBtn" style="padding:10px 24px;">
                    <i class="fas fa-paper-plane me-2"></i> Dispatch Instant Push Broadcast
                </button>
            </form>

            <!-- Right: Interactive Android Lock-Screen Notification Simulator -->
            <div class="notification-sim-stage">
                <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                    <i class="fab fa-android text-success me-1"></i> Live Mobile Notification Simulator
                </div>

                <div class="notification-lock-card">
                    <div class="sim-top-bar">
                        <div class="sim-app-badge">
                            <div class="sim-app-logo">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <span>BillInventory Pro</span>
                        </div>
                        <span>Just now</span>
                    </div>

                    <div class="sim-title" id="simTitleText">
                        New Stock Alert: HP Pavilion Available
                    </div>
                    <div class="sim-body" id="simBodyText">
                        50 units of HP Pavilion Gaming received in Kolkata Central Warehouse. Ready for sales dispatch.
                    </div>

                    <div class="sim-actions">
                        <div class="sim-action-chip">
                            <i class="fas fa-eye me-1"></i> View Inventory
                        </div>
                        <div class="sim-action-chip">
                            <i class="fas fa-check me-1"></i> Acknowledge
                        </div>
                    </div>
                </div>

                <div style="margin-top:14px; display:flex; align-items:center; gap:8px; font-size:0.78rem; color:var(--text-muted);">
                    <i class="fas fa-volume-high text-info"></i>
                    <span>Push delivers with audible device ringtone on field terminals</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Section 3: REST API Gateway & Security Console -->
    <div id="section-api" class="api-vault-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div>
                <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:0 0 4px 0;">
                    <i class="fas fa-terminal text-primary me-2"></i> Mobile REST API Gateway & Developer Console
                </h2>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin:0;">
                    Secure JSON microservice powering mobile authentication, catalog barcode queries, offline order sync, and FCM device tokens.
                </p>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateApiKey()">
                    <i class="fas fa-key me-1"></i> Generate API Bearer Key
                </button>
            </div>
        </div>

        <div class="terminal-console">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <div class="t-dot t-dot-red"></div>
                    <div class="t-dot t-dot-yellow"></div>
                    <div class="t-dot t-dot-green"></div>
                </div>
                <div style="font-size:0.75rem; color:#64748b;">
                    gateway.billinventory.internal/v1 • HMAC-SHA256
                </div>
            </div>

            <div class="endpoint-tab-group">
                <button type="button" class="endpoint-tab active" onclick="switchEndpoint('auth', this)">
                    POST /api/v1/auth/login
                </button>
                <button type="button" class="endpoint-tab" onclick="switchEndpoint('products', this)">
                    GET /api/v1/products/scan/{barcode}
                </button>
                <button type="button" class="endpoint-tab" onclick="switchEndpoint('orders', this)">
                    POST /api/v1/pos/sync-order
                </button>
                <button type="button" class="endpoint-tab" onclick="switchEndpoint('fcm', this)">
                    POST /api/v1/fcm/register
                </button>
            </div>

            <div class="terminal-code-block" id="terminalSnippet">
// 1. Mobile Terminal Handshake & Authentication
curl -X POST "<?= esc($apiEndpoint) ?>/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"device_uuid": "ZBR-TC26-8802", "secret_key": "biv_live_9f83...61"}'

// Response: HTTP/2 200 OK
{
  "status": "success",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "terminal_role": "warehouse_scanner",
  "offline_sync_allowed": true
}
            </div>
        </div>
    </div>

    <!-- 7. Section 4: Connected Hardware Fleet Table -->
    <div id="section-fleet" class="fleet-roster-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div>
                <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:0 0 4px 0;">
                    <i class="fas fa-network-wired text-primary me-2"></i> Connected Handheld Fleet Roster
                </h2>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin:0;">
                    Live telemetry, battery levels, assigned staff members, and real-time connectivity status.
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="badge bg-success" style="padding:6px 12px; font-size:0.8rem;">
                    <span class="live-beacon-dot me-1"></span> 4 Online • 1 Idle
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Terminal Model & Hardware</th>
                        <th>Device UUID</th>
                        <th>Battery</th>
                        <th>Assigned Staff / Hub</th>
                        <th>OS Version</th>
                        <th>Last Ping</th>
                        <th>FCM Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($devices as $dev): ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="device-avatar-icon" style="background:var(--bg-body); border:1px solid var(--border); color:var(--primary);">
                                    <?php if (strpos($dev['device_name'], 'Zebra') !== false || strpos($dev['device_name'], 'Honeywell') !== false): ?>
                                        <i class="fas fa-barcode"></i>
                                    <?php elseif (strpos($dev['device_name'], 'Tab') !== false || strpos($dev['device_name'], 'Pad') !== false): ?>
                                        <i class="fas fa-tablet-screen-button"></i>
                                    <?php else: ?>
                                        <i class="fas fa-mobile-screen"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color:var(--text-primary);"><?= esc($dev['device_name']) ?></div>
                                    <small class="text-muted"><?= esc($dev['device_type'] ?? 'Handheld') ?></small>
                                </div>
                            </div>
                        </td>
                        <td><code><?= esc($dev['device_id']) ?></code></td>
                        <td>
                            <span class="badge bg-secondary" style="font-size:0.78rem;">
                                <i class="fas fa-battery-half text-success me-1"></i> <?= esc($dev['battery'] ?? '85%') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-dark" style="border:1px solid var(--border); font-size:0.82rem; font-weight:500;">
                                <?= esc($dev['assigned_to']) ?>
                            </span>
                        </td>
                        <td><small class="text-secondary"><?= esc($dev['os_version']) ?></small></td>
                        <td><small class="text-muted"><?= esc($dev['last_active']) ?></small></td>
                        <td>
                            <?php if ($dev['fcm_status'] === 'Online'): ?>
                                <span class="badge bg-success">
                                    <span class="live-beacon-dot me-1"></span> Online
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Standby
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 8. Enterprise Deployment Support Banner (Clean & professional, not floating) -->
    <div class="support-enterprise-banner">
        <div>
            <h3 style="font-size:1.15rem; font-weight:700; color:#ffffff; margin:0 0 4px 0;">
                <i class="fas fa-headset text-success me-2"></i> Need Dedicated Handheld Deployment Support?
            </h3>
            <p style="color:#94a3b8; font-size:0.88rem; margin:0;">
                Our mobile systems engineering team provides custom Zebra StageNow barcode provisioning barcodes and MDM profiles.
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <a href="https://wa.me/?text=Hello%20BillInventory%20Support%20Team,%20I%20need%20assistance%20deploying%20the%20mobile%20APK" 
               target="_blank" class="btn-whatsapp-concierge">
                <i class="fab fa-whatsapp" style="font-size:1.2rem;"></i> WhatsApp Mobile Support
            </a>
            <button type="button" class="btn btn-outline-light btn-sm" onclick="showMdmInfo()">
                <i class="fas fa-file-code me-1"></i> MDM StageNow XML
            </button>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Live simulator mirroring
    document.getElementById('broadcastTitle').addEventListener('input', function() {
        document.getElementById('simTitleText').innerText = this.value || 'Notification Title';
    });
    document.getElementById('broadcastBody').addEventListener('input', function() {
        document.getElementById('simBodyText').innerText = this.value || 'Notification message...';
    });

    // Handle AJAX Push Broadcast
    async function handlePushDispatch(e) {
        e.preventDefault();
        const btn = document.getElementById('broadcastSubmitBtn');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Broadcasting to Fleet...';

        const formData = new FormData(document.getElementById('studioPushForm'));

        try {
            const response = await fetch('<?= base_url("mobile-app/send-push") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success', 5000);
            } else {
                showToast(data.message || 'Dispatch failed.', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Network error while dispatching broadcast.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    }

    // Copy APK Download Link
    function copyDownloadUrl() {
        const link = "<?= base_url('mobile-app/download') ?>";
        navigator.clipboard.writeText(link).then(() => {
            showToast('Direct APK download link copied to clipboard!', 'info');
        }).catch(() => {
            showToast('APK URL: ' + link, 'info');
        });
    }

    // Generate New API Secret
    async function generateApiKey() {
        if (!confirm('Generate a new Bearer API Secret for mobile handhelds?')) return;
        try {
            const res = await fetch('<?= base_url("mobile-app/generate-key") ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                alert("New Mobile Bearer Secret Generated:\n\n" + data.apiKey + "\n\nStore this key in your mobile config or terminal MDM.");
                showToast('API key generated successfully.', 'success');
            }
        } catch (e) {
            showToast('Failed to generate API Key.', 'error');
        }
    }

    // Switch endpoints in terminal
    const endpointSnippets = {
        auth: `// 1. Mobile Terminal Handshake & Authentication
curl -X POST "<?= esc($apiEndpoint) ?>/auth/login" \\
  -H "Content-Type: application/json" \\
  -d '{"device_uuid": "ZBR-TC26-8802", "secret_key": "biv_live_9f83...61"}'

// Response: HTTP/2 200 OK
{
  "status": "success",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "terminal_role": "warehouse_scanner",
  "offline_sync_allowed": true
}`,
        products: `// 2. Barcode Query & Real-Time Stock Availability
curl -X GET "<?= esc($apiEndpoint) ?>/products/scan/8901030704081" \\
  -H "Authorization: Bearer biv_live_9f83...61"

// Response: HTTP/2 200 OK
{
  "id": 142,
  "sku": "PRD-HP-G304",
  "name": "Logitech G304 Wireless Mouse",
  "unit_price": 1999.00,
  "warehouse_stock": 48
}`,
        orders: `// 3. Instant Field Sales Order & POS Sync
curl -X POST "<?= esc($apiEndpoint) ?>/pos/sync-order" \\
  -H "Authorization: Bearer biv_live_9f83...61" \\
  -H "Content-Type: application/json" \\
  -d '{"terminal_id": "POS-04", "total": 4848.00, "payment_method": "cash"}'

// Response: HTTP/2 201 Created
{
  "invoice_number": "INV-2026-00918",
  "status": "confirmed",
  "pdf_receipt_url": "<?= base_url('invoices/download/918') ?>"
}`,
        fcm: `// 4. Register Terminal Device Push Token
curl -X POST "<?= esc($apiEndpoint) ?>/fcm/register" \\
  -H "Authorization: Bearer biv_live_9f83...61" \\
  -d '{"device_uuid": "ZBR-TC26-8802", "fcm_token": "fXy9_10Kx..."}'

// Response: HTTP/2 200 OK
{
  "registered": true,
  "fcm_status": "Active"
}`
    };

    function switchEndpoint(key, el) {
        document.querySelectorAll('.endpoint-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('terminalSnippet').innerText = endpointSnippets[key] || '';
    }

    // Scroll helper
    function scrollToSection(id) {
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function setActiveTab(el) {
        document.querySelectorAll('.studio-nav-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
    }

    function showMdmInfo() {
        alert("Zebra StageNow & Android Enterprise MDM Profile:\n\n" +
              "• Package Name: com.billinventory.pro.handheld\n" +
              "• Enrollment Type: Dedicated Device (COSU - Corporate-Owned Single-Use)\n" +
              "• Kiosk Mode: Enabled for POS Terminal\n" +
              "• StageNow Barcode: Generated via Zebra Mobility DNA.\n\n" +
              "Contact your system administrator for barcode printing.");
    }
</script>
<?= $this->endSection() ?>
