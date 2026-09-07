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
        <title>Need to Make a Larger USDT Purchase? — WiresforUSDT</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="<?= url('/css/landing.css?v=1.2') ?>" />
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
                    <a href="<?= url('/otc-usdt') ?>" class="active">OTC USDT</a>
                    <a href="<?= url('/retail-exchange-vs-otc-desk') ?>">Retail vs OTC</a>
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
                                    <a href="<?= url('/admin/dashboard') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-chart-line"></i> Admin Panel
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('/dashboard') ?>" class="dropdown-item">
                                        <i class="fa-solid fa-table-columns"></i> Dashboard
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
                        <a class="btn btn-primary" style="padding: 10px 22px; font-size: 13px; border-radius: 30px; color:#060b0d;" href="#otc-inquiry">Start Inquiry</a>
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
                <a href="<?= url('/otc-usdt') ?>" class="active">OTC USDT</a>
                <a href="<?= url('/retail-exchange-vs-otc-desk') ?>">Retail vs OTC</a>
                <a href="<?= url('/otc-for-casino') ?>">OTC for Casino</a>
                <a href="<?= url('/dubai-expo-2026') ?>">Dubai Expo2026</a>
                <a href="<?= url('/contact') ?>">Contact Us</a>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
                    <?php if (\App\Core\Session::check()): ?>
                        <?php $currUser = \App\Core\Session::user(); ?>
                        <?php if ($currUser->role === 'admin'): ?>
                            <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="<?= url('/admin/dashboard') ?>">Admin Panel</a>
                        <?php else: ?>
                            <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="<?= url('/dashboard') ?>">Dashboard</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="btn btn-ghost" style="width: 100%;" href="<?= url('/login') ?>">Log in</a>
                        <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="#otc-inquiry">Start Inquiry</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
            $selectedAmount = old('transaction_amount');
            $selectedType = old('customer_type');
            $amounts = ['$5K-$25K', '$25K-$50K', '$50K-$100K', '$100K-$250K', '$250K-$1M', '$1M+'];
        ?>

        <section class="hero">
            <div class="container hero-inner">
                <div class="hero-content">
                    <span class="otc-kicker">WIRESFORUSDT</span>
                    <h1>Need to Make a Larger USDT Purchase?</h1>
                    <p class="sub">OTC USDT for Qualified Customers Using <span>Bank Wires</span></p>
                    <p class="lead">If retail crypto platforms no longer fit the size of your transaction, WiresforUSDT provides a direct OTC process for larger USDT purchases, subject to eligibility and compliance review.</p>
                    <div class="otc-pills">
                        <span class="otc-pill"><i class="fa-solid fa-dollar-sign"></i> $5,000+ Transactions</span>
                        <span class="otc-pill"><i class="fa-solid fa-building-columns"></i> Bank Wire Funding</span>
                        <span class="otc-pill"><i class="fa-solid fa-coins"></i> USDT Settlement</span>
                        <span class="otc-pill"><i class="fa-solid fa-headset"></i> Human Support</span>
                        <span class="otc-pill"><i class="fa-solid fa-shield-halved"></i> KYC / AML Required</span>
                    </div>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#otc-inquiry">Start OTC Inquiry</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <img src="<?= url('/images/crypto_coins_hero.png') ?>" alt="USDT OTC settlement" />
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">OTC Process</span>
                <h2>Built for Larger Transactions</h2>
                <p class="lead">Retail platforms work well for everyday crypto purchases. For larger transactions, customers may prefer an OTC process designed around higher transaction values, direct support, and bank-wire settlement.</p>
                <div class="otc-steps">
                    <div class="otc-step">
                        <div class="otc-step-num">1</div>
                        <h3>Complete verification</h3>
                        <p>Submit customer and transaction information.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">2</div>
                        <h3>Confirm your transaction</h3>
                        <p>Receive the applicable rate, fee, settlement instructions, and supported network details.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">3</div>
                        <h3>Send your bank wire</h3>
                        <p>Fund the approved transaction from your verified bank account.</p>
                    </div>
                    <div class="otc-step">
                        <div class="otc-step-num">4</div>
                        <h3>Receive USDT</h3>
                        <p>After funds are confirmed and required compliance checks are complete, USDT is sent to the approved wallet.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt">
            <div class="container">
                <span class="otc-kicker">Qualified Customers</span>
                <h2>Buying $50K, $100K or More?</h2>
                <p class="lead">WiresforUSDT is designed for qualified customers seeking larger USDT transactions through an assisted OTC process.</p>
                <div class="otc-use-grid">
                    <div class="otc-use-card"><i class="fa-solid fa-arrow-up-right-dots"></i> Larger USDT purchases</div>
                    <div class="otc-use-card"><i class="fa-solid fa-briefcase"></i> Business treasury transactions</div>
                    <div class="otc-use-card"><i class="fa-solid fa-globe"></i> International supplier payments</div>
                    <div class="otc-use-card"><i class="fa-solid fa-ship"></i> Import/export transactions</div>
                    <div class="otc-use-card"><i class="fa-solid fa-right-left"></i> OTC alternative to retail platforms</div>
                    <div class="otc-use-card"><i class="fa-solid fa-chart-line"></i> Higher-value transaction support</div>
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">Why WiresforUSDT?</span>
                <h2>Designed around bank-wire OTC settlement</h2>
                <div class="otc-why-grid">
                    <div class="otc-why-card">
                        <i class="fa-solid fa-layer-group"></i>
                        <h3>Larger Transaction Focus</h3>
                        <p>Designed for customers whose transaction requirements extend beyond typical retail purchases.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-building-columns"></i>
                        <h3>Bank Wire Settlement</h3>
                        <p>Fund approved transactions through traditional banking rails.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-user-check"></i>
                        <h3>Human Support</h3>
                        <p>Communicate with a representative throughout the transaction.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <h3>Transparent Pricing</h3>
                        <p>Applicable transaction rate and fees are disclosed before execution.</p>
                    </div>
                    <div class="otc-why-card">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h3>Compliance-First Onboarding</h3>
                        <p>Customer verification and transaction screening are required before settlement.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt">
            <div class="container">
                <span class="otc-kicker">Transaction Size</span>
                <h2>How Much Are You Looking to Purchase?</h2>
                <div class="otc-amount-grid">
                    <?php foreach ($amounts as $amount): ?>
                        <button type="button" class="otc-amount-card<?= $selectedAmount === $amount ? ' active' : '' ?>" data-amount="<?= htmlspecialchars($amount) ?>" onclick="selectOtcAmount(this)">
                            <?= htmlspecialchars($amount) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="otc-section">
            <div class="container">
                <span class="otc-kicker">Retail vs OTC</span>
                <h2>Already Using a Retail Crypto Platform?</h2>
                <p class="lead">You do not necessarily need to replace it. Retail exchanges and OTC services solve different problems. If your current platform works for smaller transactions but you need a solution designed for larger purchases, WiresforUSDT may be an appropriate alternative.</p>
                <p class="otc-note">No affiliation with Coinbase, Robinhood, or other third-party exchanges is implied.</p>
            </div>
        </section>

        <section class="faq-sec">
            <div class="container faq-inner">
                <div class="faq-info">
                    <h2>Frequently Asked Questions</h2>
                    <h3 class="accent-text">OTC USDT Desk</h3>
                    <p>If you haven't found an answer to your question, start an inquiry below.</p>
                    <a class="btn btn-primary" href="#otc-inquiry">Start Inquiry</a>
                </div>
                <div class="faq-accordion">
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>What is the minimum transaction?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Transactions generally start at $5,000, subject to eligibility and compliance review.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>How do I fund my purchase?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Approved transactions are funded through bank wire from a verified account.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>What cryptocurrency do you provide?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">The service focuses on USDT, with supported blockchain networks confirmed before execution.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Do I need to complete KYC?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Yes. Identity/business verification and applicable AML checks are required.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>How much does the service cost?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Typical transaction pricing is 1%-3%, depending on transaction size, market conditions, settlement requirements, and other factors. Exact pricing is disclosed before you transact.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>How fast is settlement?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">Settlement timing depends on receipt and confirmation of funds, compliance clearance, network conditions, and transaction requirements. Final timing is confirmed for each transaction.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="otc-section alt" id="otc-inquiry">
            <div class="container">
                <div class="otc-form-card">
                    <span class="otc-kicker" style="text-align:center;">No Obligation Inquiry</span>
                    <h2 style="text-align:center; margin-left:auto; margin-right:auto;">Ready to Make a Larger USDT Transaction?</h2>
                    <p class="lead" style="text-align:center; margin: 0 auto 28px;">No obligation. Transactions are subject to verification, compliance review, applicable laws, availability, and final approval.</p>

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

                    <form action="<?= url('/otc-usdt') ?>" method="POST" onsubmit="return validateOtcInquiry(event)">
                        <?= csrf_field() ?>
                        <input type="hidden" name="inquiry_source" value="otc-usdt">
                        <div class="otc-form-grid">
                            <div>
                                <label class="otc-label" for="transaction_amount">Transaction Amount</label>
                                <select id="transaction_amount" name="transaction_amount" class="otc-select" required>
                                    <option value="">Select amount</option>
                                    <?php foreach ($amounts as $amount): ?>
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
                                    Submit Inquiry <i class="fa-solid fa-chevron-right" style="margin-left: 8px;"></i>
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
                    <h3 style="color:#fff; margin-bottom: 12px;">Required Business &amp; Compliance Information</h3>
                    <p><strong style="color:#fff;">WiresforUSDT.com</strong></p>
                    <p>Operated by: <strong style="color:#fff;">BURTEREX S.R.O.</strong></p>
                    <p>Business Address: Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic</p>
                    <p>Licensing / Registration Information: MSB Registration Number 31000274751182 | Trade License Number 19560851 (Czech Republic Registration)</p>
                    <p>Transaction fees generally range from 1%-3%. Final pricing is disclosed before execution.</p>
                    <p>WiresforUSDT is not affiliated with Coinbase, Robinhood, or any other third-party cryptocurrency exchange referenced in advertising or informational materials.</p>
                    <p>Cryptocurrency transactions involve risk. Customers should independently evaluate whether cryptocurrency transactions are appropriate for their circumstances.</p>
                    <div class="otc-legal-links">
                        <a href="<?= url('/contact') ?>">Privacy Policy</a>
                        <a href="<?= url('/contact') ?>">Terms of Service</a>
                        <a href="<?= url('/contact') ?>">AML/KYC Policy</a>
                        <a href="<?= url('/contact') ?>">Contact</a>
                    </div>
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
                        <div style="margin-top: 20px; font-size: 12px; line-height: 1.6; color: var(--text-muted); text-align: left; border-left: 2px solid var(--accent-neon); padding-left: 14px; margin-bottom: 20px;">
                            <strong>Powered by BURTEREX S.R.O.</strong><br>
                            MSB Registration Number: 31000274751182<br>
                            Trade License Number: 19560851 (Czech Republic Registration)<br>
                            Registered Office Address: Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic<br>
                            Register Office: Veřejný rejstřík a Sbírka listin - Ministry of Justice
                        </div>
                        <p class="copyright">&copy; 2026 Wires4 USDT. All Rights Reserved.</p>
                    </div>
                    <div class="footer-contact-side">
                        <h4>Contact Us</h4>
                        <p><i class="fa-solid fa-phone" style="margin-right: 8px; color: var(--accent-neon);"></i> +1 (929) 727 5156</p>
                        <p><i class="fa-solid fa-envelope" style="margin-right: 8px; color: var(--accent-neon);"></i> admin@wiresforusdt.com</p>
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
                    <p class="copyright" style="font-size:12px;">Privacy Policy | Terms of Service | AML/KYC Policy | Contact</p>
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
