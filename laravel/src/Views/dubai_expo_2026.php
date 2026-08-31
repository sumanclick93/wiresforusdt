<div class="auth-card" style="max-width: 900px; width: 100%;">
    <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">GLOBAL CRYPTO & OTC SUMMIT</span>
            <h1 style="font-size: 32px; font-weight: 800; margin: 0; color: #fff;">Dubai Expo 2026</h1>
        </div>
        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon);">
            <i class="fa-solid fa-calendar-star" style="margin-right: 6px;"></i> OCT 14-18, 2026 | DUBAI, UAE
        </div>
    </div>

    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 24px; background: rgba(185, 255, 58, 0.1); border: 1px solid rgba(185, 255, 58, 0.3); color: var(--accent-neon); padding: 14px 18px; border-radius: 10px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 24px; background: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.3); color: #e74c3c; padding: 14px 18px; border-radius: 10px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Banner Content -->
    <div style="font-size: 15px; line-height: 1.8; color: var(--text-muted); display: flex; flex-direction: column; gap: 24px; margin-bottom: 32px;">
        <p>
            Join <strong style="color: #fff;">Wires4 Digital OTC Desk</strong> at the premier <strong style="color: #fff;">Dubai Crypto & OTC Expo 2026</strong> in Dubai, United Arab Emirates. Connect directly with our institutional trading directors, compliance experts, and high-volume liquidity providers.
        </p>

        <!-- Feature Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-handshake"></i> Private VIP Executive Suite
                </h3>
                <p style="font-size: 13px; margin: 0;">
                    Book 1-on-1 confidential consultations with our senior OTC directors at our VIP suite during Expo week.
                </p>
            </div>

            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-building-columns"></i> Institutional Liquidity & Wires
                </h3>
                <p style="font-size: 13px; margin: 0;">
                    Explore multi-million USD/EUR/GBP fiat wire off-ramp solutions with same-day settlement guarantees.
                </p>
            </div>

            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
                <h3 style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-dice"></i> Gaming & Casino OTC Desk
                </h3>
                <p style="font-size: 13px; margin: 0;">
                    Tailored treasury rebalancing and high-roller payout channels for licensed international casino operators.
                </p>
            </div>
        </div>
    </div>

    <!-- Expo Inquiry Form -->
    <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(185, 255, 58, 0.15); border-radius: 16px; padding: 28px;">
        <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-paper-plane" style="color: var(--accent-neon);"></i>
            Expo Inquiry & VIP Meeting Request
        </h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">
            Submit your details below to schedule a meeting with our leadership team or request exclusive Expo liquidity terms.
        </p>

        <form action="<?= url('/dubai-expo-2026') ?>" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
            <?= csrf_field() ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Your Name <span style="color: #e74c3c;">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Alexander Vance" style="width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Business Email <span style="color: #e74c3c;">*</span></label>
                    <input type="email" name="email" required placeholder="name@company.com" style="width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Phone / WhatsApp <span style="color: #e74c3c;">*</span></label>
                    <input type="text" name="phone_number" required placeholder="+1 (929) 727 5156" style="width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Company / Institution <span style="color: #e74c3c;">*</span></label>
                    <input type="text" name="company" required placeholder="Company Name" style="width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Expected Trade Volume / Liquidity</label>
                <select name="expected_volume" style="width: 100%; padding: 12px 14px; background: #070e11; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box;">
                    <option value="$100K - $500K">$100K - $500K USDT</option>
                    <option value="$500K - $2M" selected>$500K - $2M USDT</option>
                    <option value="$2M - $10M">$2M - $10M USDT</option>
                    <option value="$10M+">$10M+ Institutional Block</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Message / Meeting Preferences</label>
                <textarea name="message" rows="4" placeholder="Describe your liquidity requirements, preferred meeting date during Dubai Expo 2026, or specific questions..." style="width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; box-sizing: border-box; resize: vertical;"></textarea>
            </div>

            <button type="submit" style="background: var(--accent-neon); color: #000; font-weight: 800; padding: 14px 28px; border: none; border-radius: 8px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: all 0.25s ease; margin-top: 10px;">
                <i class="fa-solid fa-calendar-check" style="margin-right: 6px;"></i> Submit Expo Inquiry
            </button>
        </form>
    </div>

    <!-- Official Licensing info -->
    <div style="background: rgba(185, 255, 58, 0.04); border: 1px solid rgba(185, 255, 58, 0.15); border-radius: 12px; padding: 20px; margin-top: 28px;">
        <div style="font-size: 12px; line-height: 1.7; font-family: var(--font-sans); color: var(--text-muted);">
            <strong style="color: #fff;">Powered by BURTEREX S.R.O.</strong><br>
            MSB Registration Number: <span style="color: var(--accent-neon);">31000274751182</span> | Trade License Number: <span style="color: var(--accent-neon);">19560851</span> (Czech Republic Registration)<br>
            Registered Office Address: Cimburkova 916/8, 130 00 Praha - Žižkov, Czech Republic
        </div>
    </div>
</div>
