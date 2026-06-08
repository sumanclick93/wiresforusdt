<?php
$networkType = $user->network_type ?? 'ERC-20';
$isTrc = (strpos(strtoupper($networkType), 'TRC') !== false);
$platformWallet = $isTrc ? "TYsnK4xX6F8hK42yS6G7P9J2K1L5M4N3O5" : "0x71C7656EC7ab88b098defB751B7401B5f6d8976F";

// Determine bank details from application onboarding coordinates if present
$appBankName = $application->bank_name ?? '';
$appBankAccountNo = $application->bank_account_number ?? '';
$appBankAccountHolder = $application->bank_account_holder ?? '';
$appBankSwift = $application->bank_swift ?? '';

$hasBankDetails = (!empty($appBankName) && !empty($appBankAccountNo) && !empty($appBankAccountHolder) && !empty($appBankSwift));
?>

<div class="auth-card" style="max-width: 1000px; width: 100%; padding: 40px;">
    <!-- Form Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED OTC SETTLEMENT desk</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Sell USDT for USD Wire</h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= url('/dashboard') ?>" class="btn btn-ghost" style="padding: 8px 16px; font-size: 12px; border-radius: 30px; display: flex; align-items: center; gap: 6px; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-wallet" style="font-size: 10px;"></i>
                Active Balance: <?= number_format($user->usdt_balance ?? 0.00, 2) ?> USDT
            </div>
        </div>
    </div>

    <!-- Error/Success Feedbacks -->
    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 30px;">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 30px;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Guidelines Banner -->
    <div style="background: rgba(185, 255, 58, 0.02); border: 1px solid rgba(185, 255, 58, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
        <h4 style="color: #fff; margin: 0 0 8px 0; font-size: 14.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fa-solid fa-circle-info" style="color: var(--accent-neon); margin-right: 6px;"></i> USDT Liquidation Process
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; margin: 0; line-height: 1.6;">
            To liquidate your USDT and receive bank wire fiat:
            First, transfer your USDT to Wires4's secure institutional wallet shown on the right panel. Once transferred, enter the exact USDT amount to liquidate below and submit your request. Upon compliance confirmation of your USDT transfer, our settlement desk will authorize a USD fiat wire to your registered banking coordinates and deduct the USDT from your secure wallet balance.
        </p>
    </div>

    <!-- Warning banner if bank details are missing -->
    <?php if (!$hasBankDetails): ?>
        <div class="alert alert-danger" style="margin-bottom: 30px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 20px;"></i>
            <div>
                <strong>Incomplete Banking Specifications:</strong> Your registered bank wire details are missing or incomplete. 
                Please update your secure bank coordinates in your <a href="<?= url('/profile') ?>" style="color: #ffb8b8; text-decoration: underline; font-weight: bold;">User Profile</a> before submitting liquidation requests.
            </div>
        </div>
    <?php endif; ?>

    <!-- Sell Form -->
    <form action="<?= url('/sell-usdt') ?>" method="POST" id="sell-usdt-form" style="margin: 0;">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px;">
            
            <!-- Left Panel: Form Input & Pre-filled Bank Specs -->
            <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.03); padding: 24px; border-radius: 16px;">
                
                <div style="border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px; margin-bottom: 8px;">
                    <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0; text-transform: uppercase;">
                        <i class="fa-solid fa-money-bill-transfer" style="color: var(--accent-neon); margin-right: 8px;"></i> Liquidation & Settlement Details
                    </h3>
                </div>

                <!-- USDT Amount to Sell -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">USDT Amount to Sell</label>
                    <div style="position: relative;">
                        <input type="number" name="usdt_amount" step="0.000001" min="0.000001" max="<?= htmlspecialchars($user->usdt_balance ?? 0.00) ?>" class="form-control" placeholder="0.00" required style="padding-right: 60px;">
                        <span style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-weight: bold; font-size: 12.5px; color: var(--accent-neon);">USDT</span>
                    </div>
                    <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                        Your active balance: <strong style="color: #fff;"><?= number_format($user->usdt_balance ?? 0.00, 2) ?> USDT</strong>.
                    </small>
                </div>

                <div style="border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px; margin-top: 10px; margin-bottom: 8px;">
                    <h3 style="font-size: 14px; font-weight: bold; color: #fff; margin: 0; text-transform: uppercase;">
                        <i class="fa-solid fa-building-columns" style="color: var(--accent-neon); margin-right: 8px;"></i> Receiving Bank Wire Destination
                    </h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Bank Name -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control read-only-field" value="<?= htmlspecialchars($appBankName) ?>" placeholder="Not provided" required <?= $hasBankDetails ? 'readonly' : '' ?>>
                    </div>

                    <!-- Bank Account Number -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Bank Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control read-only-field" value="<?= htmlspecialchars($appBankAccountNo) ?>" placeholder="Not provided" required <?= $hasBankDetails ? 'readonly' : '' ?>>
                    </div>

                    <!-- Account Holder Name -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" name="bank_account_holder" class="form-control read-only-field" value="<?= htmlspecialchars($appBankAccountHolder) ?>" placeholder="Not provided" required <?= $hasBankDetails ? 'readonly' : '' ?>>
                    </div>

                    <!-- Swift Code / BIC -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">SWIFT Code / BIC / routing</label>
                        <input type="text" name="bank_swift" class="form-control read-only-field" value="<?= htmlspecialchars($appBankSwift) ?>" placeholder="Not provided" required <?= $hasBankDetails ? 'readonly' : '' ?>>
                    </div>
                </div>

            </div>

            <!-- Right Panel: Copyable Wallet Address -->
            <div style="display: flex; flex-direction: column; gap: 20px; background: rgba(255, 255, 255, 0.01); border: 1px solid rgba(255,255,255,0.03); padding: 24px; border-radius: 16px;">
                
                <div style="margin-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0; text-transform: uppercase;">
                        <i class="fa-solid fa-circle-down" style="color: var(--accent-neon); margin-right: 8px;"></i> Wires4 Receiving Node
                    </h3>
                </div>

                <!-- Platform Receiving standard -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.05); padding: 12px 16px; border-radius: 10px;">
                    <span style="font-size: 12.5px; color: var(--text-muted); font-weight: bold;">Network Standard:</span>
                    <span style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 800; color: var(--accent-neon);">
                        <?= htmlspecialchars($networkType) ?>
                    </span>
                </div>

                <!-- Copyable Wallet Address -->
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Platform Settlement Wallet Address</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="platform_wallet_addr" class="form-control read-only-field" value="<?= htmlspecialchars($platformWallet) ?>" readonly style="padding-right: 80px; font-family: monospace; font-size: 12px;">
                        <button type="button" class="copy-btn" onclick="copyToClipboard('platform_wallet_addr', this)">Copy</button>
                    </div>
                </div>

                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 16px; margin-top: 10px;">
                    <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Important Instructions</span>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: var(--text-muted); line-height: 1.6;">
                        <li>Ensure you transfer using the correct <strong><?= htmlspecialchars($networkType) ?></strong> network.</li>
                        <li>Verify your receiving bank coordinates on the left exactly match your target wire account.</li>
                        <li>USDT balances will only be deducted upon administrative approval and wire authorization.</li>
                    </ul>
                </div>

            </div>

        </div>

        <!-- Submit Panel -->
        <div style="display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px; flex-wrap: wrap; gap: 16px;">
            <span style="font-size: 12px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-shield-halved" style="color: var(--accent-neon);"></i> Military-grade perimeter security sync is active.
            </span>
            <button type="submit" class="btn btn-primary" <?= !$hasBankDetails ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?> style="padding: 14px 36px; border-radius: 30px; font-size: 13.5px; font-weight: bold; border: none; color: #000; box-shadow: 0 0 20px rgba(185, 255, 58, 0.25);">
                <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Submit Sell Request
            </button>
        </div>
    </form>
</div>

<style>
    .read-only-field {
        background: rgba(0, 0, 0, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        color: rgba(255, 255, 255, 0.8) !important;
        cursor: text !important;
    }
    .copy-btn {
        position: absolute;
        right: 4px;
        top: 4px;
        bottom: 4px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 0 16px;
        font-family: var(--font-sans);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .copy-btn:hover {
        background: var(--accent-neon);
        color: #000;
        border-color: var(--accent-neon);
    }
</style>

<script>
    /**
     * Copy value to clipboard with visual feedback micro-interaction.
     */
    function copyToClipboard(elementId, button) {
        const input = document.getElementById(elementId);
        if (!input) return;

        navigator.clipboard.writeText(input.value).then(() => {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-check"></i> COPIED';
            button.style.borderColor = 'var(--accent-neon)';
            button.style.color = '#000';
            button.style.background = 'var(--accent-neon)';

            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                button.style.color = 'var(--text-primary)';
                button.style.background = 'rgba(255, 255, 255, 0.02)';
            }, 1500);
        }).catch(err => {
            console.error('Failed to copy to clipboard: ', err);
        });
    }
</script>
