<div class="auth-card" style="max-width: 800px; width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED STAKING PORTAL</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Client Dashboard</h1>
        </div>
        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle" style="font-size: 8px; animation: slow-rotate 4s linear infinite;"></i>
            SECURE ACTIVE SESSION
        </div>
    </div>

    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Selfie Verification Request Alert -->
    <?php if (!empty($user->sdm_selfie_link)): ?>
        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(185, 255, 58, 0.05);">
            <div style="font-size: 20px; color: var(--accent-neon); display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: rgba(185, 255, 58, 0.1); flex-shrink: 0; margin-top: 2px;">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div>
                <h4 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0 0 6px 0;">Selfie Verification Required</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 14px 0; line-height: 1.5;">To ensure institutional compliance and account security, a verification selfie is required. Please click the button below to upload your verification selfie.</p>
                <a href="<?= htmlspecialchars($user->sdm_selfie_link) ?>" target="_blank" class="btn btn-primary" style="padding: 10px 20px; font-size: 12px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #000; font-weight: 800; background: var(--accent-neon); box-shadow: 0 0 15px rgba(185, 255, 58, 0.15);">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Upload Verification Selfie
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Document Request Alert -->
    <?php if (!empty($user->requested_documents) && ($user->edit_permission_status ?? 'none') === 'allowed'): ?>
        <div style="background: rgba(243, 156, 18, 0.08); border: 1px solid rgba(243, 156, 18, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.05);">
            <div style="font-size: 20px; color: #f39c12; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: rgba(243, 156, 18, 0.1); flex-shrink: 0; margin-top: 2px;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div style="flex-grow: 1;">
                <h4 style="font-size: 15px; font-weight: 800; color: #fff; margin: 0 0 6px 0;">Additional Documents Requested</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 12px 0; line-height: 1.5;">Our compliance department requires additional documentation to finalize your profile specifications. Please submit the following documents:</p>
                <div style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(243, 156, 18, 0.15); padding: 14px; border-radius: 8px; color: #fff; font-family: inherit; font-size: 13px; white-space: pre-wrap; margin-bottom: 14px; font-weight: 500; line-height: 1.5;"><?= htmlspecialchars($user->requested_documents) ?></div>
                <p style="font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.4;">
                    You can upload files securely under <a href="<?= url('/profile?tab=doc-spec') ?>" style="color: var(--accent-neon); text-decoration: underline; font-weight: 600;">Profile & Permissions</a> or submit them via your registered email inbox.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Account Details Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Left details panel -->
        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 14px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 16px; font-weight: 700; letter-spacing: 0.5px;">
                <i class="fa-solid fa-user-gear" style="color: var(--accent-neon); margin-right: 8px;"></i> Profile Specifications
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Account Holder</span>
                    <span style="font-size: 13px; font-weight: 700; color: #fff;"><?= htmlspecialchars($user->name) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Secure Login ID</span>
                    <span style="font-size: 13px; font-weight: 700; font-family: var(--font-mono); color: var(--accent-neon);"><?= htmlspecialchars($user->login_id) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Email Identity</span>
                    <span style="font-size: 13px; font-weight: 700; color: #fff;"><?= htmlspecialchars($user->email) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 4px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Security Standard</span>
                    <span style="font-size: 13px; font-weight: 700; color: var(--accent-neon);">
                        <i class="fa-solid fa-circle-check"></i> TOTP 2FA Activated
                    </span>
                </div>
            </div>
        </div>

        <!-- Right details panel -->
        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 14px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 16px; font-weight: 700; letter-spacing: 0.5px;">
                <i class="fa-solid fa-wallet" style="color: var(--accent-neon); margin-right: 8px;"></i> Settlement Node
            </h3>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Asset Type</span>
                    <span style="font-size: 13px; font-weight: 700; color: #fff;"><i class="fa-solid fa-coins" style="color: #26a17b;"></i> USDT</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Network Standard</span>
                    <span style="font-size: 13px; font-weight: 700; color: var(--accent-neon);"><?= htmlspecialchars($user->network_type) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <span style="font-size: 13px; color: var(--text-muted);">USDT Wallet Balance</span>
                    <span style="font-size: 13px; font-weight: 700; color: var(--accent-neon);"><?= number_format($user->usdt_balance ?? 0.0, 2) ?> USDT</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <span style="font-size: 13px; color: var(--text-muted);">Settlement Wallet Address</span>
                    <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono); color: #fff; background: rgba(0,0,0,0.5); padding: 8px 12px; border-radius: 6px; word-break: break-all; border: 1px solid rgba(255,255,255,0.03);">
                        <?= htmlspecialchars($user->wallet_address) ?>
                    </span>
                </div>
                <div style="margin-top: 15px; border-top: 1px dashed rgba(255,255,255,0.08); padding-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?= url('/buy-usdt') ?>" class="btn btn-primary" style="width: 100%; justify-content: center; display: inline-flex; align-items: center; gap: 8px; border-radius: 30px; font-weight: 800; font-size: 13px; color: #000; text-decoration: none; padding: 12px; box-shadow: 0 0 15px rgba(185, 255, 58, 0.2);">
                        <i class="fa-solid fa-cart-shopping"></i> BUY USDT NOW
                    </a>
                    <a href="<?= url('/sell-usdt') ?>" class="btn btn-ghost" style="width: 100%; justify-content: center; display: inline-flex; align-items: center; gap: 8px; border-radius: 30px; font-weight: 800; font-size: 13px; text-decoration: none; padding: 12px; border: 1px solid rgba(185, 255, 58, 0.3); color: var(--accent-neon); background: rgba(185, 255, 58, 0.03); transition: all 0.3s ease; box-shadow: 0 0 10px rgba(185, 255, 58, 0.05);">
                        <i class="fa-solid fa-money-bill-transfer"></i> SELL USDT NOW
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active limits matrix (Stunning interactive cards) -->
    <div style="background: linear-gradient(135deg, rgba(185, 255, 58, 0.05), rgba(0, 0, 0, 0.4)); border: 1px solid rgba(185, 255, 58, 0.15); border-radius: 12px; padding: 24px; margin-bottom: 30px; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; bottom: -20px; font-size: 120px; color: rgba(185,255,58,0.02); pointer-events: none; transform: rotate(-15deg);">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">
                <i class="fa-solid fa-chart-line" style="color: var(--accent-neon); margin-right: 8px;"></i> Limit Capacity Allocations
            </h3>
            <span style="font-size: 11px; font-weight: bold; background: var(--accent-neon); color: #000; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;">
                1% Rate Applied
            </span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div style="background: rgba(0,0,0,0.4); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.03);">
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 6px;">Daily Limits</span>
                <strong style="font-size: 20px; color: #fff;">$500,000</strong>
            </div>
            <div style="background: rgba(0,0,0,0.4); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.03);">
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 6px;">Monthly Volume</span>
                <strong style="font-size: 20px; color: #fff;">$15,000,000</strong>
            </div>
            <div style="background: rgba(0,0,0,0.4); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.03);">
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 6px;">Security Class</span>
                <strong style="font-size: 20px; color: var(--accent-neon);">Tier 1 Plus</strong>
            </div>
        </div>
    </div>

    <!-- USDT Purchase Requests History -->
    <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 24px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-neon);"></i> 
            OTC Purchase Request History (<?= count($requests ?? []) ?>)
        </h3>

        <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 8px; min-height: 80px;">
            <?php if (empty($requests)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 30px 10px; font-size: 13px;">
                    No USDT purchase requests have been submitted yet.
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Amount</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Deposit Reference</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Receiving Bank</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Submitted At</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); height: 48px;">
                                <td style="padding: 12px; font-weight: bold; color: #fff;">
                                    <?= number_format($req->usdt_amount, 2) ?> USDT
                                </td>
                                <td style="padding: 12px;">
                                    <code style="font-family: var(--font-mono); color: var(--accent-neon); background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($req->deposit_reference_number) ?></code>
                                </td>
                                <td style="padding: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars($req->receiving_bank_name) ?>
                                </td>
                                <td style="padding: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->created_at))) ?>
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <?php if ($req->status === 'pending'): ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(243, 156, 18, 0.12); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.25);">Pending</span>
                                    <?php elseif ($req->status === 'approved'): ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(185, 255, 58, 0.12); color: var(--accent-neon); border: 1px solid rgba(185, 255, 58, 0.25);">Approved</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(231, 76, 60, 0.12); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.25);">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- USDT Sell Requests History -->
    <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 24px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-neon);"></i> 
            OTC Liquidation History (<?= count($sellRequests ?? []) ?>)
        </h3>

        <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 8px; min-height: 80px;">
            <?php if (empty($sellRequests)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 30px 10px; font-size: 13px;">
                    No USDT liquidation requests have been submitted yet.
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Amount</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Receiving Wallet</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Target Bank</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold;">Submitted At</th>
                            <th style="padding: 12px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: bold; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sellRequests as $req): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); height: 48px;">
                                <td style="padding: 12px; font-weight: bold; color: #fff;">
                                    <?= number_format($req->usdt_amount, 2) ?> USDT
                                </td>
                                <td style="padding: 12px;">
                                    <code style="font-family: var(--font-mono); color: var(--accent-neon); background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;"><?= substr($req->platform_wallet_address, 0, 8) ?>...<?= substr($req->platform_wallet_address, -6) ?></code>
                                </td>
                                <td style="padding: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars($req->bank_name) ?> (<?= htmlspecialchars($req->bank_account_number) ?>)
                                </td>
                                <td style="padding: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->created_at))) ?>
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <?php if ($req->status === 'pending'): ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(243, 156, 18, 0.12); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.25);">Pending</span>
                                    <?php elseif ($req->status === 'approved'): ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(185, 255, 58, 0.12); color: var(--accent-neon); border: 1px solid rgba(185, 255, 58, 0.25);">Approved & Deducted</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: rgba(231, 76, 60, 0.12); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.25);">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contact & actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; flex-wrap: wrap; gap: 16px;">
        <span style="font-size: 13px; color: var(--text-muted);">
            Need customized limit increments? Reach out to support.
        </span>
        <a href="mailto:limits@wiresforusdt.com" class="btn btn-ghost" style="padding: 10px 20px; font-size: 12px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-headset"></i> Request Limit Increments
        </a>
    </div>
</div>
