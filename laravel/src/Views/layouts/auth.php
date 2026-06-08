<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= htmlspecialchars($title ?? 'Secure Portal') ?> — Wires4</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="<?= url('/css/landing.css') ?>" />
        <style>
            /* Custom page-specific overrides for a clean layout */
            .auth-wrapper {
                min-height: calc(100vh - 180px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
                position: relative;
            }
            .auth-card {
                background: var(--panel-bg);
                border: 1px solid var(--panel-border);
                border-radius: 20px;
                backdrop-filter: blur(20px);
                width: 100%;
                max-width: 520px;
                padding: 40px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
                position: relative;
                z-index: 2;
            }
            .auth-card::before {
                content: '';
                position: absolute;
                top: -1px;
                left: -1px;
                right: -1px;
                bottom: -1px;
                border-radius: 20px;
                background: linear-gradient(135deg, rgba(185, 255, 58, 0.2), rgba(185, 255, 58, 0) 60%);
                z-index: -1;
                pointer-events: none;
            }
            .auth-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .auth-header h1 {
                font-family: var(--font-sans);
                font-size: 28px;
                font-weight: 800;
                color: var(--text-primary);
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }
            .auth-header p {
                color: var(--text-muted);
                font-size: 14px;
            }
            .form-group {
                margin-bottom: 24px;
            }
            .form-label {
                display: block;
                font-family: var(--font-sans);
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--text-muted);
                margin-bottom: 8px;
            }
            .form-control {
                width: 100%;
                background: rgba(0, 0, 0, 0.4);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 10px;
                padding: 14px 18px;
                color: var(--text-primary);
                font-family: var(--font-sans);
                font-size: 15px;
                transition: all 0.3s ease;
            }
            .form-control:focus {
                outline: none;
                border-color: var(--accent-neon);
                box-shadow: 0 0 15px rgba(185, 255, 58, 0.15);
                background: rgba(0, 0, 0, 0.6);
            }
            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.25);
            }
            .alert {
                border-radius: 10px;
                padding: 16px;
                margin-bottom: 24px;
                font-size: 14px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }
            .alert-success {
                background: rgba(185, 255, 58, 0.08);
                border: 1px solid rgba(185, 255, 58, 0.25);
                color: var(--accent-neon);
            }
            .alert-danger {
                background: rgba(231, 76, 60, 0.08);
                border: 1px solid rgba(231, 76, 60, 0.25);
                color: #e74c3c;
            }
            /* Simulated Mailbox Drawer UI */
            .sim-mailbox-trigger {
                position: fixed;
                bottom: 24px;
                right: 24px;
                background: #111;
                border: 1px solid var(--accent-neon);
                border-radius: 50px;
                padding: 14px 24px;
                color: var(--accent-neon);
                cursor: pointer;
                font-weight: 700;
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 10px 30px rgba(185, 255, 58, 0.2);
                z-index: 1000;
                transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .sim-mailbox-trigger:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px rgba(185, 255, 58, 0.35);
                background: var(--accent-neon);
                color: #000;
            }
            .sim-mailbox-badge {
                background: #e74c3c;
                color: #fff;
                font-size: 10px;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .sim-drawer {
                position: fixed;
                bottom: 0;
                right: 24px;
                width: 420px;
                height: 560px;
                background: #090e10;
                border: 1px solid rgba(185, 255, 58, 0.2);
                border-bottom: none;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                z-index: 1001;
                transform: translateY(105%);
                transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.8);
                display: flex;
                flex-direction: column;
            }
            .sim-drawer.open {
                transform: translateY(0);
            }
            .sim-drawer-header {
                padding: 16px 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: rgba(185, 255, 58, 0.03);
            }
            .sim-drawer-header h3 {
                font-size: 14px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--accent-neon);
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .sim-drawer-close {
                background: transparent;
                border: none;
                color: var(--text-muted);
                cursor: pointer;
                font-size: 16px;
            }
            .sim-drawer-close:hover {
                color: var(--text-primary);
            }
            .sim-drawer-body {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
            }
            .sim-email-item {
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 8px;
                padding: 12px 14px;
                margin-bottom: 12px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .sim-email-item:hover {
                background: rgba(185, 255, 58, 0.03);
                border-color: rgba(185, 255, 58, 0.2);
            }
            .sim-email-to {
                font-size: 11px;
                color: var(--accent-neon);
                font-weight: 600;
                margin-bottom: 4px;
            }
            .sim-email-subj {
                font-size: 13px;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 4px;
            }
            .sim-email-time {
                font-size: 10px;
                color: var(--text-muted);
                text-align: right;
            }
            .sim-email-details {
                display: none;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed rgba(255, 255, 255, 0.1);
                font-size: 12px;
                color: var(--text-primary);
                line-height: 1.4;
            }
            .sim-email-details a {
                color: var(--accent-neon);
                text-decoration: underline;
            }
            .sim-email-details.open {
                display: block;
            }
            .sim-empty {
                text-align: center;
                color: var(--text-muted);
                padding: 40px 20px;
                font-size: 13px;
            }
            .sim-clear-btn {
                background: rgba(231, 76, 60, 0.15);
                border: 1px solid rgba(231, 76, 60, 0.3);
                color: #e74c3c;
                border-radius: 4px;
                padding: 4px 8px;
                font-size: 10px;
                cursor: pointer;
            }
            .sim-clear-btn:hover {
                background: #e74c3c;
                color: #fff;
            }
        </style>
        <?php if (isset($styles)) echo $styles; ?>
    </head>
    <body>
        <!-- Header -->
        <header class="site-header">
            <div class="container header-inner">
                <a class="brand" href="<?= url('/') ?>">
                    <img src="<?= url('/images/logo.png') ?>" alt="Wires4" style="height: 32px;" />
                </a>
                <nav class="main-nav">
                    <a href="<?= url('/') ?>">Home</a>
                    <a href="<?= url('/about') ?>">About Us</a>
                    <a href="<?= url('/how_it_work') ?>">How It Works</a>
                    <?php /* <a href="<?= url('/proof_of_funds') ?>">Tests for sale</a> */ ?>
                    <a href="<?= url('/contact') ?>">Contact Us</a>
                </nav>
                <div class="header-cta" style="display: flex; align-items: center; gap: 18px;">
                    <?php if (\App\Core\Session::check()): ?>
                        <?php $currUser = \App\Core\Session::user(); ?>
                        <div class="user-dropdown-container" id="userDropdownContainer">
                            <button type="button" class="user-dropdown-trigger" id="userDropdownTrigger">
                                <span class="user-welcome">Welcome, <span class="user-name"><?= htmlspecialchars($currUser->name) ?></span></span>
                                <div class="user-avatar-icon">
                                    <i class="fa-solid fa-circle-user"></i>
                                </div>
                                <i class="fa-solid fa-chevron-down chevron-icon"></i>
                            </button>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <div class="dropdown-header-info">
                                    <div class="dropdown-user-role"><?= htmlspecialchars(ucfirst($currUser->role)) ?> Portal</div>
                                    <div class="dropdown-user-email"><?= htmlspecialchars($currUser->email) ?></div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <?php if ($currUser->role === 'admin'): ?>
                                    <a href="<?= url('/admin/dashboard') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-chart-line"></i> Admin Panel
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('/dashboard') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-table-columns"></i> Dashboard
                                    </a>
                                    <a href="<?= url('/buy-usdt') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-cart-shopping"></i> Buy USDT
                                    </a>
                                    <a href="<?= url('/sell-usdt') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-money-bill-transfer"></i> Sell USDT
                                    </a>
                                    <a href="<?= url('/profile') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-user-gear"></i> Profile Settings
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <form action="<?= url('/logout') ?>" method="POST" class="dropdown-logout-form">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item logout-btn">
                                        <i class="fa-solid fa-right-from-bracket"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" style="color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 700; text-transform: uppercase; transition: color 0.3s ease;" onmouseover="this.style.color='var(--accent-neon)'" onmouseout="this.style.color='var(--text-primary)'">Log in</a>
                        <a class="btn btn-primary" style="padding: 10px 22px; font-size: 13px; border-radius: 30px; color:#060b0d;" href="<?= url('/#request-access') ?>">Request Access</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Main Wrapper -->
        <main class="auth-wrapper">
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer style="padding: 60px 0; border-top: 1px solid rgba(255, 255, 255, 0.03); background: rgba(3, 6, 7, 0.85); text-align: center; font-size: 13px; color: var(--text-muted);">
            <div class="container">
                <img src="<?= url('/images/logo.png') ?>" alt="Wires4" style="height: 28px; margin-bottom: 20px; opacity: 0.7;" />
                <p style="max-width: 650px; margin: 0 auto 20px; line-height: 1.6;">Designed to meet the complex trading needs of institutions. Wires4 Digital provides highly regulated, MPC-secured digital custody and high-limit liquidity settlement infrastructures.</p>
                <div style="font-size: 12px; line-height: 1.8; margin-bottom: 24px; color: rgba(255, 255, 255, 0.4); text-align: center; max-width: 800px; margin: 0 auto 24px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                    <strong>Powered by BURTEREX S.R.O.</strong><br>
                    MSB Registration Number: 31000274751182 | Trade License Number: 19560851 (Czech Republic Registration)<br>
                    Registered Office Address: Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic<br>
                    Registered Office: Veřejný rejstřík a Sbírka listin - Ministry of Justice
                </div>
                <p>&copy; <?= date('Y') ?> Wires4. All rights reserved. Secured under military-grade perimeter architecture.</p>
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const trigger = document.getElementById('userDropdownTrigger');
                const container = document.getElementById('userDropdownContainer');

                if (trigger && container) {
                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        container.classList.toggle('active');
                    });

                    document.addEventListener('click', (e) => {
                        if (!container.contains(e.target)) {
                            container.classList.remove('active');
                        }
                    });
                }
            });
        </script>

        <?php if (isset($scripts)) echo $scripts; ?>
    </body>
</html>
