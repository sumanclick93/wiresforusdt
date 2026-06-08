<div class="auth-card" style="max-width: 800px; width: 100%;">
    <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED INQUIRY CHANNELS</span>
            <h1 style="font-size: 32px; font-weight: 800; margin: 0; color: #fff;">Contact Wires4</h1>
        </div>
        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon);">
            <i class="fa-solid fa-headset" style="margin-right: 6px;"></i> SUPPORT CHANNELS
        </div>
    </div>

    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-bottom: 30px;">
        <!-- Left Side: Contact Information -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <p style="font-size: 14px; line-height: 1.7; color: var(--text-muted); margin: 0;">
                Reach out to our OTC settlement desks directly through our encrypted social messaging handles or by visiting our headquarters.
            </p>

            <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 10px;">
                <a href="https://wa.me/13475289691" target="_blank" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: #fff; background: rgba(38, 161, 123, 0.1); border: 1px solid rgba(38, 161, 123, 0.25); border-radius: 10px; padding: 14px 18px; transition: all 0.3s;" onmouseover="this.style.background='rgba(38, 161, 123, 0.2)'" onmouseout="this.style.background='rgba(38, 161, 123, 0.1)'">
                    <i class="fa-brands fa-whatsapp" style="font-size: 24px; color: #25d366;"></i>
                    <div>
                        <strong style="display: block; font-size: 13px; text-transform: uppercase; color: var(--text-muted);">WhatsApp Support</strong>
                        <span style="font-size: 14px; font-weight: 700; color: #fff;">+1 (347) 528-9691</span>
                    </div>
                </a>

                <a href="https://t.me/buyusdteasy" target="_blank" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: #fff; background: rgba(52, 152, 219, 0.1); border: 1px solid rgba(52, 152, 219, 0.25); border-radius: 10px; padding: 14px 18px; transition: all 0.3s;" onmouseover="this.style.background='rgba(52, 152, 219, 0.2)'" onmouseout="this.style.background='rgba(52, 152, 219, 0.1)'">
                    <i class="fa-brands fa-telegram" style="font-size: 24px; color: #3498db;"></i>
                    <div>
                        <strong style="display: block; font-size: 13px; text-transform: uppercase; color: var(--text-muted);">Telegram Channel</strong>
                        <span style="font-size: 14px; font-weight: 700; color: #fff;">@buyusdteasy</span>
                    </div>
                </a>
            </div>

            <!-- Business Office details -->
            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 20px; margin-top: 10px;">
                <h3 style="font-size: 14px; font-weight: bold; color: var(--accent-neon); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-map-location-dot"></i> Corporate Headquarters
                </h3>
                <p style="font-size: 13px; color: #fff; margin-bottom: 8px; font-weight: 700;">
                    Burterex s.r.o.
                </p>
                <p style="font-size: 13px; margin: 0; line-height: 1.6;">
                    Cimburkova 916/8,<br>
                    130 00 Praha - Žižkov,<br>
                    Czech Republic
                </p>
            </div>
        </div>

        <!-- Right Side: Contact Form -->
        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 24px;">
            <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 10px;">
                Submit Inquiry Form
            </h3>

            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Inquiry successfully logged! Our OTC department will contact you shortly.'); this.reset();">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="name">Your Name</label>
                    <input type="text" id="name" class="form-control" placeholder="e.g. John Doe" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" class="form-control" placeholder="yourname@institution.com" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="message">Message</label>
                    <textarea id="message" rows="4" class="form-control" placeholder="Describe your liquidity requirements..." required style="resize: none;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 12px; border-radius: 10px;">
                    Submit Inquiry <i class="fa-solid fa-chevron-right" style="margin-left: 6px;"></i>
                </button>
            </form>
        </div>
    </div>
</div>
