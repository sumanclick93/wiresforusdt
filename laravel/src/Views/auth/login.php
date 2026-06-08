<div class="auth-card">
    <div class="auth-header">
        <h1>Secure Login</h1>
        <p>Enter your Wires4usdt credentials to access the OTC Desk Portal</p>
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

    <form action="<?= url('/login') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="login_id">Secure Login ID</label>
            <input type="text" name="login_id" id="login_id" class="form-control" placeholder="e.g. W4_1001" required autofocus value="<?= htmlspecialchars(old('login_id')) ?>">
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                <a href="<?= url('/recovery') ?>" style="color: var(--accent-neon); font-size: 11px; text-decoration: none; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Forgot Password?</a>
            </div>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••••••" required>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="form-group" style="display: flex; align-items: center; margin-top: 14px; margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                <input type="checkbox" name="remember" id="remember" style="display: none;" onchange="this.nextElementSibling.style.background = this.checked ? 'var(--accent-neon)' : 'transparent'; this.nextElementSibling.style.borderColor = this.checked ? 'var(--accent-neon)' : 'rgba(255,255,255,0.3)'; this.nextElementSibling.querySelector('i').style.display = this.checked ? 'block' : 'none';">
                <div style="width: 18px; height: 18px; border-radius: 4px; border: 1.5px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: transparent;">
                    <i class="fa-solid fa-check" style="display: none; color: #000; font-size: 10px;"></i>
                </div>
                <span style="color: var(--text-muted); font-size: 13.5px; font-weight: 600;">Remember Me</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border: none; padding: 14px; border-radius: 10px; margin-top: 8px;">
            Authenticate Session <i class="fa-solid fa-shield-halved" style="margin-left: 8px;"></i>
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        <p style="font-size: 13px; color: var(--text-muted);">
            No access credentials? <a href="<?= url('/#request-access') ?>" style="color: var(--accent-neon); text-decoration: none; font-weight: 700;">Submit Access Request</a>
        </p>
    </div>
</div>
