<!doctype html>
<html lang="en">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18380805225"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'AW-18380805225');
        </script>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Retail Exchange vs. OTC Desk — WiresforUSDT</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="<?= url('/css/landing.css?v=1.3') ?>" />
    </head>
    <body>
        <header class="site-header">
            <div class="container header-inner">
                <a class="brand" href="<?= url('/') ?>">
                    <img src="<?= url('/images/logo.png') ?>" alt="Wires4" />
                </a>
                <nav class="main-nav">
                    <a href="<?= url('/') ?>">Home</a>
                    <a href="<?= url('/about') ?>">About Us</a>
                    <a href="<?= url('/how_it_work') ?>">How It Works</a>
                    <a href="<?= url('/otc-usdt') ?>">OTC USDT</a>
                    <a href="<?= url('/retail-exchange-vs-otc-desk') ?>" class="active">Retail vs OTC</a>
                    <a href="<?= url('/otc-for-casino') ?>">OTC for Casino</a>
                    <a href="<?= url('/dubai-expo-2026') ?>">Dubai Expo2026</a>
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
                                    <a href="<?= url('/admin/dashboard') ?>" class="dropdown-item"><i class="fa-solid fa-chart-line"></i> Admin Panel</a>
                                <?php else: ?>
                                    <a href="<?= url('/dashboard') ?>" class="dropdown-item"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
                                    <a href="<?= url('/profile') ?>" class="dropdown-item"><i class="fa-solid fa-user-gear"></i> Profile Settings</a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <form action="<?= url('/logout') ?>" method="POST" class="dropdown-logout-form">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" style="color: var(--text-primary); text-decoration: none; font-size: 14px; font-weight: 700; text-transform: uppercase;">Log in</a>
                        <a class="btn btn-primary" style="padding: 10px 22px; font-size: 13px; border-radius: 30px; color:#060b0d;" href="#otc-inquiry">Get an OTC Quote</a>
                    <?php endif; ?>
                </div>
                <button class="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </header>

        <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="toggleMobileMenu()"></div>
        <div class="mobile-drawer" id="mobileDrawer">
            <div class="mobile-drawer-nav">
                <a href="<?= url('/') ?>">Home</a>
                <a href="<?= url('/about') ?>">About Us</a>
                <a href="<?= url('/how_it_work') ?>">How It Works</a>
                <a href="<?= url('/otc-usdt') ?>">OTC USDT</a>
                <a href="<?= url('/retail-exchange-vs-otc-desk') ?>" class="active">Retail vs OTC</a>
                <a href="<?= url('/otc-for-casino') ?>">OTC for Casino</a>
                <a href="<?= url('/dubai-expo-2026') ?>">Dubai Expo2026</a>
                <a href="<?= url('/contact') ?>">Contact Us</a>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
                    <?php if (\App\Core\Session::check()): ?>
                        <?php $currUser = \App\Core\Session::user(); ?>
                        <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="<?= url($currUser->role === 'admin' ? '/admin/dashboard' : '/dashboard') ?>">
                            <?= $currUser->role === 'admin' ? 'Admin Panel' : 'Dashboard' ?>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-ghost" style="width: 100%;" href="<?= url('/login') ?>">Log in</a>
                        <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="#otc-inquiry">Get an OTC Quote</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
            $selectedAmount = old('transaction_amount');
            $selectedType = old('customer_type');
            $amounts = [
                '$5K-$10K' => 'Get Quote',
                '$10K-$25K' => 'Get Quote',
                '$25K-$50K' => 'Get Quote',
                '$50K-$100K' => 'Get Quote',
                '$100K-$250K' => 'Get Quote',
                '$250K-$1M' => 'Get Quote',
                '$1M+' => 'Contact OTC Desk',
            ];
        ?>

        <section class="hero">
            <div class="container hero-inner">
                <div class="hero-content">
                    <span class="otc-kicker">Retail Exchange vs. OTC Desk</span>
                    <h1>Need More Crypto Transaction Capacity?</h1>
                    <p class="sub">When retail exchange limits don't fit, <span>OTC may be better</span></p>
                    <p class="lead">When retail exchange limits don't fit the size of your transaction, OTC may be the better option. Purchase $5,000+ in USDT through a dedicated OTC process using bank wire, direct support, and compliance-first onboarding.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#otc-inquiry">Get an OTC Quote</a>
                    </div>
                    <div class="otc-pills">
                        <span class="otc-pill">$5K+ Transactions</span>
                        <span class="otc-pill">Bank Wire</span>
                        <span class="otc-pill">USDT</span>
                        <span class="otc-pill">KYC/AML Required</span>
                    </div>
                </div>
                <div class="hero-visual">
                    <img src="<?= url('/images/crypto_coins_hero.png') ?>" alt="Retail exchange vs OTC desk" />
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">Side-by-side comparison</span>
                <h2>Retail Exchange vs. OTC Desk</h2>
                <div class="otc-compare">
                    <div class="otc-compare-head">
                        <div></div>
                        <div>Retail Exchange</div>
                        <div>WiresforUSDT OTC</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Best for</div>
                        <div>Everyday crypto transactions</div>
                        <div>Larger USDT transactions</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Transaction size</div>
                        <div>Account-dependent limits</div>
                        <div>$5K+ qualified transactions</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Funding</div>
                        <div>Platform-dependent</div>
                        <div>Bank wire</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Support</div>
                        <div>Often automated</div>
                        <div>Direct human support</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Pricing</div>
                        <div>Platform-dependent</div>
                        <div>Quote before execution</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">Verification</div>
                        <div>Platform-specific</div>
                        <div>KYC/AML required</div>
                    </div>
                    <div class="otc-compare-row">
                        <div class="otc-compare-label">USDT delivery</div>
                        <div>Account-dependent</div>
                        <div>Approved wallet settlement</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt">
            <div class="container">
                <span class="otc-kicker">Keep your retail account</span>
                <h2>Already using Coinbase, Robinhood or another retail platform?</h2>
                <p class="lead">You don't necessarily need to replace it. When your transaction requirements are larger than what is practical through your existing retail setup, WiresforUSDT provides a separate OTC transaction process.</p>
                <div class="hero-actions" style="margin-top: 28px;">
                    <a class="btn btn-primary" href="#otc-inquiry">See if you qualify</a>
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">OTC process</span>
                <h2>Need to Buy $25K, $50K, $100K or More in USDT?</h2>
                <p class="lead">Instead of trying to change or work around another platform's restrictions, use a service designed for larger qualified transactions.</p>
                <div class="otc-steps cols-5">
                    <div class="otc-step">
                        <div class="otc-step-num">1</div>
                        <h3>Tell Us Your Amount</h3>
                        <p>Select how much you're looking to transact.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">2</div>
                        <h3>Complete KYC/AML</h3>
                        <p>Complete the required identity and transaction verification.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">3</div>
                        <h3>Receive Your Quote</h3>
                        <p>Review the transaction rate, applicable fees, and terms before proceeding.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">4</div>
                        <h3>Wire USD</h3>
                        <p>Fund your approved transaction through bank wire.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">5</div>
                        <h3>Receive USDT</h3>
                        <p>Following verification of funds, USDT is delivered to your approved wallet according to the agreed transaction terms.</p>
                    </div>
                </div>
                <div class="hero-actions" style="margin-top: 32px;">
                    <a class="btn btn-primary" href="#otc-inquiry">Start my transaction</a>
                </div>
            </div>
        </section>

        <section class="otc-section alt">
            <div class="container">
                <span class="otc-kicker">Transaction size</span>
                <h2>How Much Are You Looking to Transact?</h2>
                <div class="otc-amount-grid">
                    <?php foreach ($amounts as $amount => $action): ?>
                        <button type="button" class="otc-amount-card<?= $selectedAmount === $amount ? ' active' : '' ?>" data-amount="<?= htmlspecialchars($amount) ?>" onclick="selectOtcAmount(this)">
                            <?= htmlspecialchars($amount) ?>
                            <span class="otc-amount-action"><?= htmlspecialchars($action) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">Why Use WiresforUSDT OTC?</span>
                <h2>Built for qualified larger USDT purchases</h2>
                <div class="otc-why-grid">
                    <div class="otc-why-card">
                        <i class="fa-solid fa-layer-group"></i>
                        <h3>Larger Transactions</h3>
                        <p>Built for customers whose transaction requirements may be better suited to OTC than a consumer trading application.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-building-columns"></i>
                        <h3>Bank-Wire Funding</h3>
                        <p>Fund approved USDT purchases using traditional banking rails.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-user-check"></i>
                        <h3>Human Support</h3>
                        <p>Communicate directly with the OTC team throughout onboarding and settlement.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <h3>Upfront Quote</h3>
                        <p>Review your transaction pricing and applicable fees before execution.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h3>Compliance-First</h3>
                        <p>KYC/AML and transaction review are required before settlement.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt">
            <div class="container">
                <span class="otc-kicker">Keep retail for everyday use</span>
                <h2>Retail Limits Don't Have to Limit Your Transaction Size.</h2>
                <p class="lead">Your retail exchange can remain useful for everyday crypto activity. For a larger transaction, use a separate OTC solution designed for qualified customers.</p>
                <h3 style="margin-top: 36px; font-size: 28px; font-weight: 800;">Ready to Buy USDT?</h3>
                <div class="otc-dual-cta">
                    <a class="btn btn-primary" href="#otc-inquiry">Get my OTC quote</a>
                    <span class="or-text">or</span>
                    <a class="btn btn-ghost" href="<?= url('/contact') ?>">Speak with the OTC desk</a>
                </div>
                <div class="otc-pills">
                    <span class="otc-pill">$5K+</span>
                    <span class="otc-pill">Bank Wire</span>
                    <span class="otc-pill">USDT</span>
                    <span class="otc-pill">Direct Support</span>
                </div>
            </div>
        </section>

        <section class="faq-sec">
            <div class="container faq-inner">
                <div class="faq-info">
                    <h2>Frequently Asked Questions</h2>
                    <h3 class="accent-text">Retail vs OTC</h3>
                    <p>If you haven't found an answer, request a quote below.</p>
                    <a class="btn btn-primary" href="#otc-inquiry">Get an OTC Quote</a>
                </div>
                <div class="faq-accordion">
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Does WiresforUSDT increase my Coinbase or Robinhood limit?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">No. WiresforUSDT operates independently and does not change or circumvent another platform's limits.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Do I need to transfer funds from Coinbase or Robinhood?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">No. An eligible WiresforUSDT transaction is conducted separately according to the funding instructions provided for the transaction.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Can I purchase $100,000+ in USDT?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Potentially. Larger transactions are reviewed individually based on eligibility, compliance requirements, liquidity, jurisdiction, and other transaction considerations.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Is KYC required?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Yes. KYC/AML and transaction verification are required.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>How much does WiresforUSDT charge?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Pricing and applicable fees are disclosed as part of your transaction quote before execution.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt" id="otc-inquiry">
            <div class="container">
                <div class="otc-form-card">
                    <span class="otc-kicker" style="text-align:center;">Get an OTC quote</span>
                    <h2 style="text-align:center; margin-left:auto; margin-right:auto;">Ready to Buy USDT?</h2>
                    <p class="lead" style="text-align:center; margin: 0 auto 28px;">Transactions are subject to eligibility, jurisdiction, compliance review, applicable law, liquidity, and transaction approval.</p>

                    <?php if (\App\Core\Session::hasFlash('success')): ?>
                        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.25); color: var(--accent-neon); border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; gap: 16px; align-items: flex-start;">
                            <i class="fa-solid fa-circle-check" style="font-size: 20px; margin-top: 2px;"></i>
                            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (\App\Core\Session::hasFlash('error')): ?>
                        <div style="background: rgba(231, 76, 60, 0.08); border: 1px solid rgba(231, 76, 60, 0.25); color: #e74c3c; border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; gap: 16px; align-items: flex-start;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 20px; margin-top: 2px;"></i>
                            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('/retail-exchange-vs-otc-desk') ?>" method="POST" onsubmit="return validateOtcInquiry(event)">
                        <?= csrf_field() ?>
                        <input type="hidden" name="inquiry_source" value="retail-vs-otc">
                        <div class="otc-form-grid">
                            <div>
                                <label class="otc-label" for="transaction_amount">Transaction Amount</label>
                                <select id="transaction_amount" name="transaction_amount" class="otc-select" required>
                                    <option value="">Select amount</option>
                                    <?php foreach ($amounts as $amount => $action): ?>
                                        <option value="<?= htmlspecialchars($amount) ?>"<?= $selectedAmount === $amount ? ' selected' : '' ?>><?= htmlspecialchars($amount) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <span class="otc-label">Individual / Business</span>
                                <input type="hidden" name="customer_type" id="customer_type" value="<?= htmlspecialchars($selectedType) ?>">
                                <div class="otc-type-toggle">
                                    <button type="button" class="otc-type-btn<?= $selectedType === 'Individual' ? ' active' : '' ?>" onclick="selectCustomerType('Individual', this)">Individual</button>
                                    <button type="button" class="otc-type-btn<?= $selectedType === 'Business' ? ' active' : '' ?>" onclick="selectCustomerType('Business', this)">Business</button>
                                </div>
                            </div>
                            <div>
                                <label class="otc-label" for="contact">Email / Phone</label>
                                <input type="text" id="contact" name="contact" class="otc-input" value="<?= htmlspecialchars(old('contact')) ?>" placeholder="email@company.com or +1..." required>
                            </div>
                            <div>
                                <label class="otc-label" for="state">State</label>
                                <input type="text" id="state" name="state" class="otc-input" value="<?= htmlspecialchars(old('state')) ?>" placeholder="Enter state" required>
                            </div>
                            <div class="full" style="margin-top: 8px;">
                                <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 16px;">
                                    Get my OTC quote <i class="fa-solid fa-chevron-right" style="margin-left: 8px;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <div class="otc-form-card otc-legal">
                    <p><strong style="color:#fff;">Powered by BURTEREX S.R.O.</strong></p>
                    <p>MSB Registration Number: 31000274751182</p>
                    <p>Trade License Number: 19560851 — Czech Republic</p>
                    <p>Registered Office: Cimburkova 916/8, 130 00 Praha – Žižkov, Czech Republic</p>
                    <p>Digital asset transactions involve risk. Services are subject to eligibility, jurisdiction, compliance review, applicable law, liquidity, and transaction approval. WiresforUSDT is independent and is not affiliated with, endorsed by, or sponsored by Coinbase or Robinhood. Third-party trademarks belong to their respective owners.</p>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <div class="container">
                <div class="footer-top">
                    <div class="footer-brand-side">
                        <a class="brand" href="<?= url('/') ?>" style="margin-bottom: 24px;">
                            <img src="<?= url('/images/logo.png') ?>" alt="Wires4" />
                        </a>
                        <p>Designed to meet the complex trading needs of institutions. Wires4 Digital provides highly regulated, MPC-secured digital custody and high-limit liquidity settlement infrastructures.</p>
                        <p class="copyright">&copy; 2026 Wires4 USDT. All Rights Reserved.</p>
                    </div>
                    <div class="footer-contact-side">
                        <h4>Contact Us</h4>
                        <p><i class="fa-solid fa-phone" style="margin-right: 8px; color: var(--accent-neon);"></i> +1 (929) 727 5156</p>
                        <p><i class="fa-solid fa-envelope" style="margin-right: 8px; color: var(--accent-neon);"></i> Wires4usdt@gmail.com</p>
                        <div class="social-links">
                            <a class="social-icon" href="https://t.me/wiresforusdt" target="_blank" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                            <a class="social-icon" href="https://wa.me/19297275156" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="footer-links">
                        <a href="<?= url('/') ?>">Home</a>
                        <a href="<?= url('/about') ?>">About Us</a>
                        <a href="<?= url('/how_it_work') ?>">How It Works</a>
                        <a href="<?= url('/otc-usdt') ?>">OTC USDT</a>
                        <a href="<?= url('/retail-exchange-vs-otc-desk') ?>">Retail vs OTC</a>
                        <a href="<?= url('/otc-for-casino') ?>">OTC for Casino</a>
                        <a href="<?= url('/dubai-expo-2026') ?>">Dubai Expo2026</a>
                        <a href="<?= url('/contact') ?>">Contact Us</a>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            function toggleMobileMenu() {
                const drawer = document.getElementById('mobileDrawer');
                const overlay = document.getElementById('mobileDrawerOverlay');
                const toggleIcon = document.querySelector('.menu-toggle i');
                drawer.classList.toggle('open');
                overlay.classList.toggle('open');
                toggleIcon.className = drawer.classList.contains('open') ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            }

            document.querySelectorAll('.mobile-drawer-nav a').forEach(link => {
                link.addEventListener('click', () => {
                    const drawer = document.getElementById('mobileDrawer');
                    if (drawer.classList.contains('open')) toggleMobileMenu();
                });
            });

            function selectOtcAmount(button) {
                const amount = button.getAttribute('data-amount');
                document.querySelectorAll('.otc-amount-card').forEach(card => card.classList.remove('active'));
                button.classList.add('active');
                const select = document.getElementById('transaction_amount');
                if (select) select.value = amount;
                document.getElementById('otc-inquiry').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function selectCustomerType(type, button) {
                document.getElementById('customer_type').value = type;
                document.querySelectorAll('.otc-type-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            }

            function validateOtcInquiry(event) {
                if (!document.getElementById('customer_type').value) {
                    event.preventDefault();
                    alert('Please select Individual or Business.');
                    return false;
                }
                return true;
            }

            function toggleFaq(headerElement) {
                const item = headerElement.parentElement;
                const body = item.querySelector('.faq-body');
                const isOpen = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.faq-body').style.maxHeight = null;
                        otherItem.querySelector('.faq-body').style.opacity = null;
                    }
                });
                if (isOpen) {
                    item.classList.remove('active');
                    body.style.maxHeight = null;
                    body.style.opacity = null;
                } else {
                    item.classList.add('active');
                    body.style.maxHeight = body.scrollHeight + 'px';
                    body.style.opacity = '1';
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const trigger = document.getElementById('userDropdownTrigger');
                const container = document.getElementById('userDropdownContainer');
                if (trigger && container) {
                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        container.classList.toggle('active');
                    });
                    document.addEventListener('click', (e) => {
                        if (!container.contains(e.target)) container.classList.remove('active');
                    });
                }
                const amountSelect = document.getElementById('transaction_amount');
                if (amountSelect) {
                    amountSelect.addEventListener('change', () => {
                        document.querySelectorAll('.otc-amount-card').forEach(card => {
                            card.classList.toggle('active', card.getAttribute('data-amount') === amountSelect.value);
                        });
                    });
                }
            });
        </script>
    </body>
</html>
