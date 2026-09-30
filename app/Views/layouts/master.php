<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inventory System - Inventory & Billing Management Software">
    <title><?= esc($pageTitle ?? 'Dashboard') ?> | Inventory System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="alternate icon" type="image/png" href="<?= base_url('favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('favicon.png') ?>">

    <!-- High-Performance DNS & CDN Preconnect -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Non-Blocking Google Fonts (Inter) -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>

    <!-- FontAwesome 6 (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Bootstrap 5.3 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Core App CSS (Optimized) -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>?v=2.6">

    <script>
        window.APP_BASE_URL = "<?= rtrim(base_url(), '/') ?>";
        (function() {
            const savedTheme = localStorage.getItem('appTheme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (savedTheme === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>
    <?= $this->renderSection('styles') ?>
</head>
<body data-instant>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            <?= $this->include('layouts/header') ?>

            <!-- Content Area -->
            <div class="content-area">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS Bundle (CDN) with defer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Core JS with defer -->
    <script src="<?= base_url('js/app.js') ?>?v=2.6" defer></script>

    <!-- instant.page (CDN) - Preloads pages on hover/touch for instant transitions -->
    <script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.min.js" type="module" defer></script>

    <!-- Flash messages as toasts -->
    <?php if (session()->getFlashdata('success')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('<?= esc(session()->getFlashdata('success')) ?>', 'success'));</script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('<?= esc(session()->getFlashdata('error')) ?>', 'error'));</script>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
