<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to BillInventory Pro - Inventory & Billing Management Software">
    <title>Login | BillInventory Pro</title>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"></noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <style>
        .auth-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }
        .auth-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .auth-feature i {
            color: var(--primary-light);
            font-size: 0.7rem;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            margin-top: 8px;
        }
        .form-group-icon {
            position: relative;
        }
        .form-group-icon .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .form-group-icon .form-control {
            padding-left: 40px;
        }
        .form-group-icon .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.85rem;
            transition: color 150ms ease;
        }
        .form-group-icon .toggle-password:hover {
            color: var(--text-primary);
        }
        .alert-error {
            background: var(--danger-bg);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card animate-fade-in-up">
            <!-- Logo -->
            <div class="auth-logo">
                <div class="logo-icon"><i class="fas fa-boxes-stacked"></i></div>
                <h2>BillInventory Pro</h2>
                <p>Sign in to manage your business</p>
            </div>

            <!-- Error Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:var(--success-bg);color:var(--success);padding:12px 16px;border-radius:var(--radius-md);font-size:0.85rem;margin-bottom:20px;border:1px solid rgba(16,185,129,0.2);display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-check-circle"></i>
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <div><?= esc($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="form-group-icon">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="admin@billinventory.com"
                               value="<?= old('email') ?>" required autofocus id="loginEmail">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="form-group-icon">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password"
                               required id="loginPassword">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex items-center justify-between mb-16">
                    <label class="form-check">
                        <input type="checkbox" name="remember">
                        <span style="font-size:0.85rem; color:var(--text-muted);">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <!-- Features -->
            <div class="auth-features">
                <div class="auth-feature"><i class="fas fa-check"></i> Multi-Branch Support</div>
                <div class="auth-feature"><i class="fas fa-check"></i> Role Based Access</div>
                <div class="auth-feature"><i class="fas fa-check"></i> Real-time Inventory</div>
                <div class="auth-feature"><i class="fas fa-check"></i> POS & Billing</div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('loginPassword');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<span class="loader" style="width:18px;height:18px;border-width:2px;"></span> Signing in...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
