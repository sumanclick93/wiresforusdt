<div class="auth-card">
    <div class="auth-header">
        <h1 style="color: var(--accent-neon);"><i class="fa-solid fa-key"></i> Setup Multi-Factor 2FA</h1>
        <p>Your account is protected by strict military-grade perimeter policies. Please link your authenticator application to continue.</p>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin: 24px 0;">
        <div style="background: #fff; display: inline-block; padding: 16px; border-radius: 12px; box-shadow: 0 10px 30px rgba(185,255,58,0.1); border: 2px solid var(--accent-neon);">
            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="Scan QR Code" style="display: block; width: 200px; height: 200px;" />
        </div>
        <p style="font-size: 12px; color: var(--text-muted); margin-top: 12px;">
            Scan this QR code using Google Authenticator, Authy, or any standard TOTP app.
        </p>
    </div>

    <div style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 16px; margin-bottom: 24px; text-align: center;">
        <span class="form-label" style="margin-bottom: 4px;">Manual Security Key</span>
        <code style="font-family: var(--font-mono); font-size: 16px; font-weight: bold; color: var(--accent-neon); letter-spacing: 2px;">
            <?= chunk_split($secret, 4, ' ') ?>
        </code>
        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 6px;">
            If you cannot scan the QR code, manually input this secret key. Keep this key safe.
        </span>
    </div>

    <form action="<?= url('/setup-2fa') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="code">6-Digit Verification Code</label>
            <input type="text" name="code" id="code" class="form-control" placeholder="000 000" pattern="[0-9]*" inputmode="numeric" maxlength="6" style="text-align: center; font-size: 24px; letter-spacing: 6px; font-family: var(--font-mono);" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 14px; border-radius: 10px; margin-top: 8px;">
            Activate 2FA Guard <i class="fa-solid fa-lock" style="margin-left: 8px;"></i>
        </button>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?= url('/setup-2fa/skip') ?>" style="color: var(--text-muted); font-size: 13.5px; text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='var(--accent-neon)'" onmouseout="this.style.color='var(--text-muted)'">
                Configure Later / Skip for now <i class="fa-solid fa-chevron-right" style="font-size: 11px; margin-left: 4px;"></i>
            </a>
        </div>
    </form>
</div>
