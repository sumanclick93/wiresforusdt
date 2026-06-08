<div class="auth-card">
    <div class="auth-header">
        <h1 style="color: var(--accent-neon);"><i class="fa-solid fa-shield-halved"></i> 2FA Guard Intercept</h1>
        <p>Your session is protected by Multi-Factor Authorization. Enter the 6-digit verification code from your authenticator app.</p>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= url('/login/2fa') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="code">Security Verification Token</label>
            <input type="text" name="code" id="code" class="form-control" placeholder="000 000" pattern="[0-9]*" inputmode="numeric" maxlength="6" style="text-align: center; font-size: 24px; letter-spacing: 6px; font-family: var(--font-mono);" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 14px; border-radius: 10px; margin-top: 8px;">
            Verify Identity <i class="fa-solid fa-key" style="margin-left: 8px;"></i>
        </button>
    </form>
</div>
