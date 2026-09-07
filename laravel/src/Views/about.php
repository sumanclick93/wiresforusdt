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

        <div
            style="background: rgba(185, 255, 58, 0.04); border: 1px solid rgba(185, 255, 58, 0.15); border-radius: 12px; padding: 24px; position: relative;">
            <h4 style="color: #fff; font-size: 14px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px;">
                Corporate Licensing & Transparency</h4>
            <div style="font-size: 12px; line-height: 1.7; font-family: var(--font-sans);">
                <strong>Powered by BURTEREX S.R.O.</strong><br>
                MSB Registration Number: <span style="color: var(--accent-neon);">31000274751182</span><br>
                Trade License Number: <span style="color: var(--accent-neon);">19560851</span> (Czech Republic
                Registration)<br>
                Registered Office Address: Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic<br>
                Registered Office: Veřejný rejstřík a Sbírka listin - Ministry of Justice
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px;">
        <a href="<?= url('/#request-access') ?>" class="btn btn-primary"
            style="padding: 12px 30px; border-radius: 30px;">REQUEST OTC ACCESS</a>
    </div>
</div>