<?php
// Configurable Team Member Image URLs:
// You can use local paths (e.g. url('/images/yanic.jpg')) 
// or paste full web URLs (e.g. 'https://yourdomain.com/path/to/image.jpg')
$yanicPhotoUrl = url('/images/Yanick.jpeg');
$bdLeadPhotoUrl = url('/images/Nabin_Jiaswal.jpeg');
?>

<div class="auth-card" style="max-width: 800px; width: 100%;">
    <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px;">
        <span
            style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">INSTITUTIONAL
            GRADE ORIGINS</span>
        <h1 style="font-size: 32px; font-weight: 800; margin: 0; color: #fff;">About Wires4</h1>
    </div>

    <div
        style="font-size: 15px; line-height: 1.8; color: var(--text-muted); display: flex; flex-direction: column; gap: 24px;">
        <p>
            Designed to meet the complex and high-volume trading needs of buy- and sell-side institutions, <strong
                style="color: #fff;">Wires4</strong> provides a highly regulated, MPC-secured digital custody and
            high-limit OTC liquidity settlement infrastructure.
        </p>

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 5px 0;">
            <div
                style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
                <h3
                    style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-building-columns"></i> OTC Desk Network
                </h3>
                <p style="font-size: 13px;">
                    Wires4Usdt is an crypto OTC desk with connections dating back over 15 year working with multiple
                    providers to tackle various industry types and as an unequivocal qualified custodian, we provide
                    fully-backed, institutional exchange channels globally.
                </p>
            </div>
            <div
                style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
                <h3
                    style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-shield-halved"></i> Military Grade Perimeter
                </h3>
                <p style="font-size: 13px;">
                    All assets under our custody are secured with bank-grade multi-party computation (MPC) key shards,
                    hardware security modules (HSM), and rigorous KYC/AML compliance matrices.
                </p>
            </div>
        </div>

        <!-- Leadership & Key Team Section -->
        <div>
            <h3
                style="font-size: 15px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users" style="color: var(--accent-neon);"></i> Key Leadership
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">

                <!-- Founder Card -->
                <div
                    style="background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; position: relative; overflow: hidden;">
                    <div
                        style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--accent-neon), transparent);">
                    </div>
                    <div
                        style="width: 110px; height: 110px; border-radius: 50%; border: 2px solid var(--accent-neon); margin-bottom: 16px; overflow: hidden; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(185, 255, 58, 0.15); position: relative;">
                        <img src="<?= htmlspecialchars($yanicPhotoUrl) ?>" alt=""
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.style.display='none'; document.getElementById('yanic-fallback').style.display='flex';" />
                        <div id="yanic-fallback"
                            style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-user-tie" style="font-size: 50px; color: var(--accent-neon);"></i>
                        </div>
                    </div>
                    <h4 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0 0 4px 0;">YANNICK ALEGE</h4>
                    <span
                        style="font-size: 12px; font-weight: 700; color: var(--accent-neon); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 12px;">Founder
                        &amp; CEO</span>
                    <!-- <p style="font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.6;">
                        Pioneering institutional OTC settlement channels and digital asset liquidity structures with over a decade of execution expertise.
                    </p> -->
                </div>

                <!-- Business Development Lead Card -->
                <div
                    style="background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; position: relative; overflow: hidden;">
                    <div
                        style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--accent-neon), transparent);">
                    </div>
                    <div
                        style="width: 110px; height: 110px; border-radius: 50%; border: 2px solid var(--accent-neon); margin-bottom: 16px; overflow: hidden; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(185, 255, 58, 0.15); position: relative;">
                        <img src="<?= htmlspecialchars($bdLeadPhotoUrl) ?>" alt=""
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.style.display='none'; document.getElementById('bd-lead-fallback').style.display='flex';" />
                        <div id="bd-lead-fallback"
                            style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-user-gear" style="font-size: 50px; color: var(--accent-neon);"></i>
                        </div>
                    </div>
                    <h4 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0 0 4px 0;">Nabin J.</h4>
                    <span
                        style="font-size: 12px; font-weight: 700; color: var(--accent-neon); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 12px;">Head
                        of Global Partnerships</span>
                    <!-- <p style="font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.6;">
                        Driving institutional client onboarding, strategic exchange liquidity partnerships, and high-volume OTC wire settlement relations globally.
                    </p> -->
                </div>

            </div>
        </div>

        <p>
            Through our new **Atlas Settlement Network**, our clients can execute massive block orders of USDT with a
            flat **1% fee** and near-instant settlement speed. This allows institutional desks to manage large block
            trades without experiencing liquidity slippage or structural delays.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 10px;">
            <!-- United States FinCEN MSB Card -->
            <div style="padding: 18px; background: rgba(18, 18, 22, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; font-size: 12px; line-height: 1.6; color: #d4d4d8; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 10px;">
                    <div>
                        <span style="font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent-neon); display: block; margin-bottom: 2px;">UNITED STATES &bull; FINCEN</span>
                        <strong style="font-size: 16px; color: #ffffff; display: block; line-height: 1.2;">Money Services Business</strong>
                        <span style="font-size: 13px; color: #a1a1aa;">Dynamic Capital Tech LLC</span>
                    </div>
                    <span style="border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 2px 8px; font-size: 11px; font-weight: 700; color: #e4e4e7;">US</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr; gap: 8px; font-size: 12px;">
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">REGISTRATION NO.</strong> <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: #fff;">31000308345717</span></div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">ISSUER</strong> Financial Crimes Enforcement Network (FinCEN)</div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">ENTITY</strong> Delaware limited liability company &bull; EIN 33-1626372</div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">REGISTERED OFFICE</strong> 8 The Green, Suite A, Dover, DE 19901, USA</div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">GOVERNING LAW</strong> State of Delaware, USA &bull; Bank Secrecy Act</div>
                    <div>Powered by <a href="https://butterex.com" target="_blank" rel="noopener noreferrer" style="color: var(--accent-neon); text-decoration: none; font-weight: 600;">Butterex.com</a></div>
                </div>
            </div>

            <!-- Czech Republic Corporate Licensing Card -->
            <div style="padding: 18px; background: rgba(18, 18, 22, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; font-size: 12px; line-height: 1.6; color: #d4d4d8; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 10px;">
                    <div>
                        <span style="font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent-neon); display: block; margin-bottom: 2px;">CZECH REPUBLIC &bull; EUROPE</span>
                        <strong style="font-size: 16px; color: #ffffff; display: block; line-height: 1.2;">Money Services Business</strong>
                        <span style="font-size: 13px; color: #a1a1aa;">BURTEREX S.R.O.</span>
                    </div>
                    <span style="border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 4px; padding: 2px 8px; font-size: 11px; font-weight: 700; color: #e4e4e7;">CZ</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr; gap: 8px; font-size: 12px;">
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">MSB REGISTRATION NO.</strong> <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: #fff;">31000274751182</span></div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">TRADE LICENSE NO.</strong> <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: #fff;">19560851</span> (Czech Republic Registration)</div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">REGISTERED OFFICE ADDRESS</strong> Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic</div>
                    <div><strong style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">REGISTERED OFFICE AUTHORITY</strong> Veřejný rejstřík a Sbírka listin - Ministry of Justice</div>
                    <div>Powered by <a href="https://butterex.com" target="_blank" rel="noopener noreferrer" style="color: var(--accent-neon); text-decoration: none; font-weight: 600;">Butterex.com</a></div>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px;">
        <a href="<?= url('/#request-access') ?>" class="btn btn-primary"
            style="padding: 12px 30px; border-radius: 30px;">REQUEST OTC ACCESS</a>
    </div>
</div>