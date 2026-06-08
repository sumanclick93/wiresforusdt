<?php
$isCorporate = ($application && $application->account_type === 'corporate');
$profileLabel = $isCorporate ? "Corporate Account Profile" : "Individual Account Profile";

// Configure bank details
$hasCustomBankDetails = !empty($user->buy_usdt_bank_name);

$bankName = $hasCustomBankDetails ? $user->buy_usdt_bank_name : "Awaiting Admin Setup...";
$bankAddress = $hasCustomBankDetails ? $user->buy_usdt_bank_address : "Receiving address will be configured by administrator.";
$routingNo = $hasCustomBankDetails ? $user->buy_usdt_routing_no : "—";
$accountNo = $hasCustomBankDetails ? $user->buy_usdt_account_no : "—";
$beneficiary = $hasCustomBankDetails ? $user->buy_usdt_beneficiary : "—";
$bankPdf = $hasCustomBankDetails ? ($user->buy_usdt_bank_pdf ?? null) : null;
?>

<div class="auth-card" style="max-width: 1000px; width: 100%; padding: 40px;">
    <!-- Form Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED OTC SETTLEMENT desk</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Buy USDT via Bank Wire</h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= url('/dashboard') ?>" class="btn btn-ghost" style="padding: 8px 16px; font-size: 12px; border-radius: 30px; display: flex; align-items: center; gap: 6px; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-shield-halved" style="font-size: 10px;"></i>
                <?= htmlspecialchars($profileLabel) ?>
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

    <!-- Main Grid: Info text + Bank wire guidelines -->
    <div style="background: rgba(185, 255, 58, 0.02); border: 1px solid rgba(185, 255, 58, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
        <h4 style="color: #fff; margin: 0 0 8px 0; font-size: 14.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
            <i class="fa-solid fa-circle-info" style="color: var(--accent-neon); margin-right: 6px;"></i> Wire Instruction Guidelines
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; margin: 0; line-height: 1.6;">
            To complete your purchase of USDT, please initiate a standard wire transfer from your registered bank account using the secure details provided below. Once the wire transfer has been dispatched, input the Deposit Reference Number, enter the exact USDT Amount, upload your proof of deposit, and submit the request. Our compliance desk will credit your digital node balance immediately upon funds clearance.
        </p>
    </div>

    <!-- Wire Submission Form -->
    <form action="<?= url('/buy-usdt') ?>" method="POST" enctype="multipart/form-data" id="buy-usdt-form" style="margin: 0;">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px;">
            
            <!-- Read-only Bank Wiring Coordinates -->
            <div style="grid-column: span 2; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.03); padding: 24px; border-radius: 16px;">
                
                <div style="grid-column: span 2; margin-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0; text-transform: uppercase;">
                        <i class="fa-solid fa-building-columns" style="color: var(--accent-neon); margin-right: 8px;"></i> Receiving Wire Coordinates
                    </h3>
                </div>

                <!-- Receiving Bank Name -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Receiving Bank Name</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="bank_name" class="form-control read-only-field" value="<?= htmlspecialchars($bankName) ?>" readonly style="padding-right: 80px;">
                        <?php if ($hasCustomBankDetails): ?>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('bank_name', this)">Copy</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Receiving Bank Address -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Receiving Bank Address</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="bank_address" class="form-control read-only-field" value="<?= htmlspecialchars($bankAddress) ?>" readonly style="padding-right: 80px;">
                        <?php if ($hasCustomBankDetails): ?>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('bank_address', this)">Copy</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Routing No ABA -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Routing No ABA</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="routing_no" class="form-control read-only-field" value="<?= htmlspecialchars($routingNo) ?>" readonly style="padding-right: 80px;">
                        <?php if ($hasCustomBankDetails): ?>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('routing_no', this)">Copy</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Beneficiary Account Number -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Beneficiary Account Number</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="account_no" class="form-control read-only-field" value="<?= htmlspecialchars($accountNo) ?>" readonly style="padding-right: 80px;">
                        <?php if ($hasCustomBankDetails): ?>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('account_no', this)">Copy</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Beneficiary Name -->
                <div class="form-group" style="grid-column: span 2; margin-bottom: 0;">
                    <label class="form-label">Beneficiary Name</label>
                    <div style="position: relative; display: flex;">
                        <input type="text" id="beneficiary_name" class="form-control read-only-field" value="<?= htmlspecialchars($beneficiary) ?>" readonly style="padding-right: 80px;">
                        <?php if ($hasCustomBankDetails): ?>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('beneficiary_name', this)">Copy</button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($bankPdf): ?>
                    <!-- Wire Instructions PDF Download -->
                    <div style="grid-column: span 2; margin-top: 15px; padding-top: 15px; border-top: 1px dashed rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 13px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-file-pdf" style="color: #e74c3c; font-size: 16px;"></i> Deposit Instruction
                        </span>
                        <a href="<?= url('/uploads/' . urlencode($bankPdf)) ?>" target="_blank" class="btn btn-ghost" style="padding: 6px 14px; font-size: 11px; border-radius: 20px; border-color: rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-weight: bold; color: var(--accent-neon);">
                            <i class="fa-solid fa-download"></i> Download PDF
                        </a>
                    </div>
                <?php endif; ?>

            </div>

            <!-- User Form Submissions (Right Panel) -->
            <div style="display: flex; flex-direction: column; gap: 20px; background: rgba(255, 255, 255, 0.01); border: 1px solid rgba(255,255,255,0.03); padding: 24px; border-radius: 16px;">
                
                <div style="margin-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0; text-transform: uppercase;">
                        <i class="fa-solid fa-file-invoice-dollar" style="color: var(--accent-neon); margin-right: 8px;"></i> Deposit Verification
                    </h3>
                </div>

                <!-- Deposit Reference Number -->
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Deposit Reference Number</label>
                    <input type="text" name="deposit_reference_number" class="form-control" placeholder="e.g. FT192739281" required <?= !$hasCustomBankDetails ? 'disabled style="cursor: not-allowed; opacity: 0.5;"' : '' ?>>
                </div>

                <!-- USDT Amount to Purchase -->
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">USDT Amount To Purchase</label>
                    <div style="position: relative;">
                        <input type="number" name="usdt_amount" step="0.000001" min="0.000001" class="form-control" placeholder="0.00" required style="padding-right: 60px; <?= !$hasCustomBankDetails ? 'cursor: not-allowed; opacity: 0.5;' : '' ?>" <?= !$hasCustomBankDetails ? 'disabled' : '' ?>>
                        <span style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-weight: bold; font-size: 12.5px; color: var(--accent-neon); <?= !$hasCustomBankDetails ? 'opacity: 0.5;' : '' ?>">USDT</span>
                    </div>
                </div>

                <!-- Upload Proof of Deposit -->
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Upload Proof of Deposit</label>
                    <div class="upload-area" id="proof-upload-area" onclick="<?= $hasCustomBankDetails ? 'triggerProofFileInput()' : '' ?>" style="cursor: <?= $hasCustomBankDetails ? 'pointer' : 'not-allowed' ?>; padding: 24px 16px; border: 1px dashed rgba(255,255,255,0.1); border-radius: 10px; background: rgba(0,0,0,0.3); text-align: center; transition: all 0.3s ease; <?= !$hasCustomBankDetails ? 'opacity: 0.5;' : '' ?>">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: var(--accent-neon); margin-bottom: 8px; display: block; <?= !$hasCustomBankDetails ? 'color: var(--text-muted);' : '' ?>"></i>
                        <span id="upload-label" style="font-size: 12px; font-weight: 700; color: #fff;">Click to upload proof</span>
                        <span style="font-size: 10px; color: var(--text-muted); display: block; margin-top: 4px;">JPG, PNG, or PDF up to 10MB</span>
                        <input type="file" name="proof_of_deposit" id="proof_of_deposit" style="display: none;" onchange="handleProofFileSelect(this)" required <?= !$hasCustomBankDetails ? 'disabled' : '' ?>>
                    </div>
                </div>

            </div>

        </div>

        <!-- Submit Panel -->
        <div style="display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px; flex-wrap: wrap; gap: 16px;">
            <span style="font-size: 12px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-shield-halved" style="color: var(--accent-neon);"></i> Military-grade perimeter security sync is active.
            </span>
            <button type="submit" class="btn btn-primary" <?= !$hasCustomBankDetails ? 'disabled style="padding: 14px 36px; border-radius: 30px; font-size: 13.5px; font-weight: bold; border: none; color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.05); cursor: not-allowed; box-shadow: none;"' : 'style="padding: 14px 36px; border-radius: 30px; font-size: 13.5px; font-weight: bold; border: none; color: #000; box-shadow: 0 0 20px rgba(185, 255, 58, 0.25);"' ?>>
                <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Submit Purchase Request
            </button>
        </div>
    </form>
</div>

<style>
    /* Styling adjustments specifically for Buy USDT screen */
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
    .upload-area:hover {
        border-color: var(--accent-neon) !important;
        background: rgba(185, 255, 58, 0.02) !important;
        box-shadow: 0 0 15px rgba(185, 255, 58, 0.05);
    }
</style>

<script>
    /**
     * Copy value to clipboard with nice visual feedback micro-interaction.
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

    /**
     * Trigger the file dialog.
     */
    function triggerProofFileInput() {
        document.getElementById('proof_of_deposit').click();
    }

    /**
     * Handle the file selection and update visual label.
     */
    function handleProofFileSelect(input) {
        const uploadArea = document.getElementById('proof-upload-area');
        const label = document.getElementById('upload-label');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            label.innerText = file.name;
            uploadArea.style.borderColor = 'var(--accent-neon)';
            uploadArea.style.background = 'rgba(185, 255, 58, 0.04)';
        } else {
            label.innerText = "Click to upload proof";
            uploadArea.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            uploadArea.style.background = 'rgba(0, 0, 0, 0.3)';
        }
    }
</script>
