<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Wires4 — Get Higher Crypto Limits</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="<?= url('/css/landing.css?v=1.1') ?>" />
    </head>
    <body>
        <!-- Header -->
        <header class="site-header">
            <div class="container header-inner">
                <a class="brand" href="<?= url('/') ?>">
                    <img src="<?= url('/images/logo.png') ?>" alt="Wires4" />
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
                        <a class="btn btn-primary" style="padding: 10px 22px; font-size: 13px; border-radius: 30px; color:#060b0d;" href="#request-access">Request Access</a>
                    <?php endif; ?>
                </div>
                <!-- Hamburger Button -->
                <button class="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </header>

        <!-- Mobile Drawer Overlay & Menu -->
        <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="toggleMobileMenu()"></div>
        <div class="mobile-drawer" id="mobileDrawer">
            <div class="mobile-drawer-nav">
                <a href="<?= url('/') ?>" class="active">Home</a>
                <a href="<?= url('/about') ?>">About Us</a>
                <a href="<?= url('/how_it_work') ?>">How It Works</a>
                <?php /* <a href="<?= url('/proof_of_funds') ?>">Tests for sale</a> */ ?>
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
                        <a class="btn btn-primary" style="width: 100%; color:#060b0d;" href="#request-access">Request Access</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container hero-inner">
                <div class="hero-content">
                    <h1>Get Higher Crypto Limits</h1>
                    <p class="sub">For USDT – <span>1% Fee"</span></p>
                    <p class="lead">Our clients safely participate in digital asset markets from custody to trading, staking, and governance — all with proven security architecture.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#request-access">Start Onboarding</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <img src="<?= url('/images/crypto_coins_hero.png') ?>" alt="Futuristic Floating Crypto Coins" />
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-label">Client Volume Processed</div>
                        <div class="stat-value">$51,126,242,057</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Processed Trade Orders</div>
                        <div class="stat-value">$8,532,215</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Global Leaders Section -->
        <section class="leaders">
            <div class="container leaders-inner">
                <div class="leaders-visual">
                    <img src="<?= url('/images/digital_network_globe.png') ?>" alt="Global Digital Network Globe" />
                </div>
                <div class="leaders-content">
                    <h2>The platform trusted by <span class="accent-text">global leaders</span></h2>
                    <p>Our clients safely participate in digital asset markets from custody to trading, staking, and governance — all from within the most advanced and proven security architecture.</p>
                    <a class="trustpilot-badge" href="https://www.trustpilot.com/" target="_blank">
                        REVIEW US ON <i class="fa-solid fa-star"></i> Trustpilot
                    </a>
                </div>
            </div>
        </section>

        <!-- Assets Supported Section -->
        <section class="assets-sec">
            <div class="container">
                <div class="section-header">
                    <h2>Assets supported</h2>
                    <p>Wires4 Digital supports assets that meet our standards of quality and safety. Asset support varies by legal entity, jurisdiction, and service.</p>
                </div>
                
                <div class="features-grid">
                    <!-- Card 1 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                        <h3>Blockchain Development</h3>
                        <p>We build highly secured, robust, and scalable decentralized networks, ledger infrastructures, and cross-chain bridging protocols tailored for your enterprise.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h3>Smart Contract Development</h3>
                        <p>Our smart contract solutions undergo rigorous audits and formal verification to guarantee seamless, autonomous, and zero-defect executions.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        </div>
                        <h3>Tokenization Services</h3>
                        <p>We enable asset fractioning, compliant token offerings, and fractional security representation for real-world assets on public or private chains.</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                            </svg>
                        </div>
                        <h3>Cryptocurrency Solutions</h3>
                        <p>Enjoy massive digital liquidity pools, customized settlement gateways, and dynamic API integrations designed for institutions.</p>
                    </div>

                    <!-- Card 5 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <h3>Security and Compliance</h3>
                        <p>We incorporate bank-grade multi-party computation (MPC) key shards, hardware security modules (HSM), and rigorous KYC/AML integrations.</p>
                    </div>

                    <!-- Card 6 -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                <polyline points="2 17 12 22 22 17"></polyline>
                                <polyline points="2 12 12 17 22 12"></polyline>
                            </svg>
                        </div>
                        <h3>Tokenization Services</h3>
                        <p>Expand your financial horizons by turning intangible resources, rewards networks, or high-value physical goods into tradeable tokens.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Crypto Bank & Carousel Section -->
        <section class="bank-section">
            <div class="container bank-inner" style="grid-template-columns: 1fr; max-width: 600px; margin: 0 auto;">
                <?php /*
                <div class="bank-content">
                    <h2>The only federally chartered <span class="accent-text">crypto bank</span></h2>
                    <p>Wires4 Digital Bank is the only crypto-native bank to hold a charter from the US Office of the Comptroller of the Currency. As an unequivocal qualified custodian, Wires4 provides regulated custody and settlement services.</p>
                    <a class="btn btn-ghost" href="<?= url('/about') ?>">Read More</a>
                </div>
                */ ?>
                
                <!-- Interactive Carousel Box -->
                <div class="carousel-box">
                    <h3>A complete offering</h3>
                    
                    <div class="carousel-content-wrapper">
                        <!-- Slide 1 (Active) -->
                        <div class="carousel-slide active" data-index="0">
                            <div class="carousel-slide-header">
                                <div class="carousel-slide-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <div class="carousel-slide-title">Safeguard assets</div>
                            </div>
                            <p class="carousel-slide-desc">Security is the foundation of everything we do. Our institutional custody solution mitigates the risk of human error, and is backed by biometric authentication, behavioral analytics, and social controls—all without compromising accessibility.</p>
                        </div>

                        <!-- Slide 2 -->
                        <div class="carousel-slide" data-index="1">
                            <div class="carousel-slide-header">
                                <div class="carousel-slide-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    </svg>
                                </div>
                                <div class="carousel-slide-title">Regulated Settlement</div>
                            </div>
                            <p class="carousel-slide-desc">Settle massive blocks of cryptocurrency within seconds with complete peace of mind. Our settlement solutions strictly adhere to the highest state and federal compliance framework regulations.</p>
                        </div>

                        <!-- Slide 3 (Disabled for Reversion)
                        <div class="carousel-slide" data-index="2">
                            <div class="carousel-slide-header">
                                <div class="carousel-slide-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="20" x2="18" y2="10"></line>
                                        <line x1="12" y1="20" x2="12" y2="4"></line>
                                        <line x1="6" y1="20" x2="6" y2="14"></line>
                                    </svg>
                                </div>
                                <div class="carousel-slide-title">Advanced Yield Staking</div>
                            </div>
                            <p class="carousel-slide-desc">Engage in high-performing consensus networks and proof-of-stake algorithms. Earn safe, stable, and audit-proven staking yields directly from institutional-grade validators.</p>
                        </div>
                        -->

                        <!-- Slide 3 -->
                        <div class="carousel-slide" data-index="2">
                            <div class="carousel-slide-header">
                                <div class="carousel-slide-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div class="carousel-slide-title">Same-Day Onboarding</div>
                            </div>
                            <p class="carousel-slide-desc">Get verified and launch your digital asset operations within minutes. Our automated compliance stack eliminates overhead delay so you can capture market opportunities today.</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="carousel-pagination">
                        <button class="pagination-dot active" onclick="switchSlide(0)" aria-label="Slide 1"></button>
                        <button class="pagination-dot" onclick="switchSlide(1)" aria-label="Slide 2"></button>
                        <button class="pagination-dot" onclick="switchSlide(2)" aria-label="Slide 3"></button>
                        <!-- <button class="pagination-dot" onclick="switchSlide(3)" aria-label="Slide 4"></button> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Atlas Settlement Network Section -->
        <section class="atlas">
            <div class="container atlas-inner">
                <div class="atlas-visual">
                    <img src="<?= url('/images/tether_atlas_sphere.png') ?>" alt="USDT Atlas Network Sphere" />
                </div>
                <div class="atlas-content">
                    <h2>Introducing Atlas, Wires4 USDT Digital's new <span class="accent-text">settlement network</span></h2>
                    <p>Buy- and sell-side institutions are joining Atlas to benefit from the highest level of regulatory protections, proven security, and optimized capital efficiency.</p>
                    <a class="btn btn-ghost" href="<?= url('/how_it_work') ?>">Read More</a>
                </div>
            </div>
        </section>

        <!-- Porto Stripe -->
        <section class="container">
            <div class="porto-stripe">
                <div class="porto-inner">
                    <h2>Porto by Wires4 USDT Digital</h2>
                    <p>Discover Porto, an institutional self-custody wallet built with the security and technology Wires4 USDT Digital is best known for. Finally, institutions have full control over with the industry's simplest self-custody setup and flat, same-day onboarding. This is self-custody the way it's meant to be.</p>
                    <div class="porto-actions">
                        <a class="btn btn-dark" href="#request-access">Get Started</a>
                        <a class="btn btn-ghost" style="border-color: rgba(6,11,13,0.3); color: #060b0d;" href="<?= url('/about') ?>">Read More</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Request Access Section -->
        <section class="request-access-section" id="request-access" style="padding: 100px 0; position: relative; background: rgba(6, 11, 13, 0.4);">
            <div class="container">
                <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 24px; padding: 60px 40px; text-align: center; backdrop-filter: blur(20px); max-width: 800px; margin: 0 auto; box-shadow: 0 30px 60px rgba(0,0,0,0.5); position: relative; overflow: hidden;">
                    <div style="position: absolute; right: -50px; top: -50px; font-size: 200px; color: rgba(185,255,58,0.015); pointer-events: none;">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    
                    <span style="color: var(--accent-neon); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 12px;">PERIMETER CONTROL INVITATION ONLY</span>
                    <h2 style="font-size: 38px; font-weight: 800; color: #fff; margin-bottom: 16px; letter-spacing: -1px;">Request Access Validation</h2>
                    <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 32px; font-size: 15px; line-height: 1.6;">
                        Public registrations are currently closed. To qualify for same-day onboarding and access higher crypto limits with a flat 1% fee, submit your verified email address below.
                    </p>

                    <?php if (\App\Core\Session::hasFlash('success')): ?>
                        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.25); color: var(--accent-neon); border-radius: 12px; padding: 20px; max-width: 600px; margin: 0 auto 24px; text-align: left; display: flex; gap: 16px; align-items: flex-start;">
                            <i class="fa-solid fa-circle-check" style="font-size: 20px; margin-top: 2px; color: var(--accent-neon);"></i>
                            <div>
                                <strong style="display:block; margin-bottom:4px; font-size:15px; color:#fff;">Invitation Dispatch Initialized</strong>
                                <?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (\App\Core\Session::hasFlash('error')): ?>
                        <div style="background: rgba(231, 76, 60, 0.08); border: 1px solid rgba(231, 76, 60, 0.25); color: #e74c3c; border-radius: 12px; padding: 20px; max-width: 600px; margin: 0 auto 24px; text-align: left; display: flex; gap: 16px; align-items: flex-start;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 20px; margin-top: 2px; color: #e74c3c;"></i>
                            <div>
                                <strong style="display:block; margin-bottom:4px; font-size:15px; color:#fff;">Validation Interrupted</strong>
                                <p style="margin: 0;"><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('/request-access') ?>" method="POST" style="display: flex; gap: 12px; max-width: 600px; margin: 0 auto; flex-wrap: wrap; justify-content: center;">
                        <?= csrf_field() ?>
                        <input type="email" name="email" placeholder="Enter institution email address..." required style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.08); padding: 16px 24px; border-radius: 50px; color: #fff; font-family: var(--font-sans); font-size: 15px; flex: 1; min-width: 280px; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--accent-neon)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                        <button type="submit" class="btn btn-primary" style="border: none; padding: 16px 36px; border-radius: 50px;">
                            Request Invite <i class="fa-solid fa-chevron-right" style="margin-left: 8px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-sec">
            <div class="container faq-inner">
                <div class="faq-info">
                    <h2>You have Questions</h2>
                    <h3 class="accent-text">We Have Answers</h3>
                    <p>If you haven't found an answer to your question, contact us.</p>
                    <a class="btn btn-primary" href="<?= url('/contact') ?>">Ask a question</a>
                </div>
                
                <div class="faq-accordion">
                    <!-- Item 1 -->
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>How long does a typical OTC transaction take to settle?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">
                                Settling deep-block transactions on Wires4 is designed to be near-instant. For standard domestic and international wires, settlement clears within the same business day, typically taking under 2 hours once verification steps are complete.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Are there any hidden fees for Wires4 services?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">
                                No, absolute transparency is core to Wires4. We operate on a flat fee structure with a simple 1% fee on operations. Every charge, including blockchain gas or network settlement costs, is clearly defined upfront before transaction confirmation.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Is there a minimum transaction requirement?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">
                                Yes, as an institutional-focused desk, our minimum OTC transaction requirement is $10,000 USD (or equivalent). This allows us to dedicate peak engineering and liquidity resources to process large volumes securely.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Is there a maximum transaction requirement?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">
                                There is no set maximum limit. Wires4 is built on highly backed institutional-grade liquidity pools, enabling us to handle multi-million dollar high-volume USDT transfers without experiencing negative slippage or liquidity blockages.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="faq-item">
                        <div class="faq-header" onclick="toggleFaq(this)">
                            <h4>Can I use Wires4 for both buying and selling crypto?</h4>
                            <div class="faq-icon">+</div>
                        </div>
                        <div class="faq-body">
                            <div class="faq-content">
                                Yes, Wires4 provides fully comprehensive, bi-directional buying and selling channels. You can easily trade large wire transfers directly into USDT or cash out your high USDT volumes back into standard fiat bank wires.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
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
                        <p><i class="fa-solid fa-phone" style="margin-right: 8px; color: var(--accent-neon);"></i> (524) 555 0000</p>
                        <p><i class="fa-solid fa-envelope" style="margin-right: 8px; color: var(--accent-neon);"></i> Wires4usdt@gmail.com</p>
                        <div class="social-links">
                            <a class="social-icon" href="#" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                            <a class="social-icon" href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                            <a class="social-icon" href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                            <a class="social-icon" href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="footer-links">
                        <a href="<?= url('/') ?>">Home</a>
                        <a href="<?= url('/about') ?>">About Us</a>
                        <a href="<?= url('/how_it_work') ?>">How It Works</a>
                        <?php /* <a href="<?= url('/proof_of_funds') ?>">Tests for sale</a> */ ?>
                        <a href="<?= url('/contact') ?>">Contact Us</a>
                    </div>
                    <p class="copyright" style="font-size:12px;">Providing secure blockchain custody and exchange pipelines globally.</p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script>
            // Mobile Menu Toggle
            function toggleMobileMenu() {
                const drawer = document.getElementById('mobileDrawer');
                const overlay = document.getElementById('mobileDrawerOverlay');
                const toggleIcon = document.querySelector('.menu-toggle i');
                
                drawer.classList.toggle('open');
                overlay.classList.toggle('open');
                
                if (drawer.classList.contains('open')) {
                    toggleIcon.className = "fa-solid fa-xmark";
                } else {
                    toggleIcon.className = "fa-solid fa-bars";
                }
            }

            // Close Mobile Menu on Drawer Link Click
            document.querySelectorAll('.mobile-drawer-nav a').forEach(link => {
                link.addEventListener('click', () => {
                    const drawer = document.getElementById('mobileDrawer');
                    if (drawer.classList.contains('open')) {
                        toggleMobileMenu();
                    }
                });
            });

            // Carousel State Management
            let currentSlide = 0;
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.pagination-dot');

            function switchSlide(index) {
                if (index < 0 || index >= slides.length) return;
                
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                slides[index].classList.add('active');
                dots[index].classList.add('active');
                currentSlide = index;
            }

            // Auto-rotate Carousel slides
            setInterval(() => {
                let nextSlide = (currentSlide + 1) % slides.length;
                switchSlide(nextSlide);
            }, 6000);

            // FAQ Accordion State Management
            function toggleFaq(headerElement) {
                const item = headerElement.parentElement;
                const body = item.querySelector('.faq-body');
                const isOpen = item.classList.contains('active');
                
                // Close all other FAQ items
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.faq-body').style.maxHeight = null;
                        otherItem.querySelector('.faq-body').style.opacity = null;
                    }
                });

                // Toggle active state on current item
                if (isOpen) {
                    item.classList.remove('active');
                    body.style.maxHeight = null;
                    body.style.opacity = null;
                } else {
                    item.classList.add('active');
                    body.style.maxHeight = body.scrollHeight + "px";
                    body.style.opacity = "1";
                }
            }
            // User Dropdown State Management
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
    </body>
</html>
