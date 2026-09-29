<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - BillInventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #090d16;
            --card-bg: rgba(18, 24, 38, 0.9);
            --border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --primary: #3b82f6;
            --success: #10b981;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b 0%, var(--bg) 60%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .setup-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(16px);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-error { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        p { color: var(--text-secondary); font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        .log-box {
            background: #040711;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 24px;
            max-height: 200px;
            overflow-y: auto;
        }
        .log-item { margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px; }
        .log-item:last-child { margin-bottom: 0; }
        .credentials-box {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.25);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 24px;
        }
        .credentials-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #60a5fa; margin-bottom: 10px; }
        .cred-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px; }
        .cred-row:last-child { margin-bottom: 0; }
        .cred-label { color: var(--text-secondary); }
        .cred-val { font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #fff; background: rgba(0,0,0,0.3); padding: 2px 8px; border-radius: 6px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }
        .btn:hover { opacity: 0.95; transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="setup-card">
        <?php if ($status === 'success'): ?>
            <div class="badge badge-success">✓ Ready to Use</div>
            <h1>Database Initialized</h1>
            <p>Your cloud database has been successfully migrated and seeded with default administrative roles and data.</p>
            
            <div class="log-box">
                <?php foreach ($logs as $log): ?>
                    <div class="log-item"><span>➜</span> <span><?= esc($log) ?></span></div>
                <?php endforeach; ?>
            </div>

            <div class="credentials-box">
                <div class="credentials-title">Admin Login Credentials</div>
                <div class="cred-row">
                    <span class="cred-label">Email:</span>
                    <span class="cred-val"><?= esc($email) ?></span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password:</span>
                    <span class="cred-val"><?= esc($password) ?></span>
                </div>
            </div>

            <a href="<?= base_url('/login') ?>" class="btn">Go to Login Page →</a>
        <?php else: ?>
            <div class="badge badge-error">✕ Setup Error</div>
            <h1>Connection Issue</h1>
            <p>Could not connect or apply database migrations. Please verify your Render environment variables.</p>
            
            <div class="log-box" style="color: #f87171;">
                <div class="log-item"><span>Error:</span> <span><?= esc($error) ?></span></div>
                <?php foreach ($logs as $log): ?>
                    <div class="log-item"><span>➜</span> <span><?= esc($log) ?></span></div>
                <?php endforeach; ?>
            </div>

            <a href="<?= base_url('/setup-database') ?>" class="btn" style="background: #374151;">Retry Setup ↺</a>
        <?php endif; ?>
    </div>
</body>
</html>
