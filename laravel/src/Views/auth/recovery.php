<div class="auth-card">
    <div class="auth-header">
        <h1>Account Recovery</h1>
        <p>Enter your verified email address to recover your secure login credentials</p>
    </div>

    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= url('/recovery') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="email">Verified Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="yourname@domain.com" required autofocus value="<?= htmlspecialchars(old('email')) ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 14px; border-radius: 10px; margin-top: 8px;">
            Request Recovery Link <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        <p style="font-size: 13px; color: var(--text-muted);">
            Remember your credentials? <a href="<?= url('/login') ?>" style="color: var(--accent-neon); text-decoration: none; font-weight: 700;">Back to Login</a>
        </p>
    </div>
</div>
