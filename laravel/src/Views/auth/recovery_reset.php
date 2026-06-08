<div class="auth-card">
    <div class="auth-header">
        <h1 style="color: var(--accent-neon);"><i class="fa-solid fa-lock-open"></i> Reset Password</h1>
        <p>Establish new highly secure credentials for your Wires4 account</p>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= url('/recovery/reset') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

        <div class="form-group">
            <label class="form-label" for="email_display">Email Address</label>
            <input type="email" id="email_display" class="form-control" value="<?= htmlspecialchars($email) ?>" disabled style="opacity: 0.6; background: rgba(0,0,0,0.6); cursor: not-allowed; border-color: rgba(185, 255, 58, 0.2);">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Create New Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 characters" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter password" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 14px; border-radius: 10px; margin-top: 8px;">
            Establish New Password <i class="fa-solid fa-shield-halved" style="margin-left: 8px;"></i>
        </button>
    </form>
</div>
