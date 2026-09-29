<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inventory & Billing Management Software - Manage your business efficiently">
    <title><?= esc($pageTitle ?? 'Dashboard') ?> | MallInventory Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
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
<body>
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core JS -->
    <script src="<?= base_url('js/app.js') ?>?v=<?= time() ?>"></script>

    <!-- Flash messages as toasts -->
    <?php if (session()->getFlashdata('success')): ?>
    <script>showToast('<?= esc(session()->getFlashdata('success')) ?>', 'success');</script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <script>showToast('<?= esc(session()->getFlashdata('error')) ?>', 'error');</script>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
