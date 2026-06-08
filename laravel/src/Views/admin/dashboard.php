<?php
// Set specific styles inside the layout
$styles = "
<style>
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
        font-size: 14px;
        text-align: left;
    }
    .admin-table th {
        background: rgba(255, 255, 255, 0.03);
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .admin-table td {
        padding: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
    }
    .admin-table tr:hover {
        background: rgba(255, 255, 255, 0.01);
    }
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-pending {
        background: rgba(243, 156, 18, 0.15);
        color: #f39c12;
        border: 1px solid rgba(243, 156, 18, 0.3);
    }
    .badge-active {
        background: rgba(185, 255, 58, 0.15);
        color: var(--accent-neon);
        border: 1px solid rgba(185, 255, 58, 0.3);
    }
    .badge-suspended {
        background: rgba(231, 76, 60, 0.15);
        color: #e74c3c;
        border: 1px solid rgba(231, 76, 60, 0.3);
    }
    .admin-btn {
        background: none;
        border: 1px solid rgba(255,255,255,0.15);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        color: var(--text-primary);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .admin-btn-approve {
        border-color: var(--accent-neon);
        color: var(--accent-neon);
        background: rgba(185, 255, 58, 0.03);
    }
    .admin-btn-approve:hover {
        background: var(--accent-neon);
        color: #000;
        box-shadow: 0 0 10px rgba(185,255,58,0.25);
    }
    .admin-btn-suspend {
        border-color: #e74c3c;
        color: #e74c3c;
        background: rgba(231, 76, 60, 0.03);
    }
    .admin-btn-suspend:hover {
        background: #e74c3c;
        color: #fff;
        box-shadow: 0 0 10px rgba(231,76,60,0.25);
    }
    .admin-btn-activate {
        border-color: #3498db;
        color: #3498db;
        background: rgba(52, 152, 219, 0.03);
    }
    .admin-btn-activate:hover {
        background: #3498db;
        color: #fff;
    }
    dialog::backdrop {
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
    }
</style>
";
?>

<div class="auth-card" style="max-width: 1050px; width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED ADMINISTRATIVE ROOT AUTHORITY</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Approval Authority Board</h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= url('/admin/dropdowns') ?>" class="admin-btn admin-btn-approve" style="padding: 8px 16px; font-size: 12px; border-radius: 30px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                <i class="fa-solid fa-gears" style="font-size: 10px;"></i> Manage Dropdowns
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-lock" style="font-size: 10px;"></i>
                ADMINISTRATOR ROOT
            </div>
        </div>
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

    <!-- Pending Review Panel -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-user-clock" style="color: #f39c12;"></i> 
            Pending Account Reviews (<?= count($pendingUsers) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 120px;">
            <?php if (empty($pendingUsers)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 40px 20px; font-size: 13px;">
                    No profiles are currently awaiting administrator validation.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Registrant Name</th>
                            <th>Email Address</th>
                            <th>Settlement Address</th>
                            <th>Protocol</th>
                            <th>Submitted At</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingUsers as $pending): ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($pending->name) ?></strong>
                                </td>
                                <td>
                                    <span style="color: var(--text-muted);"><?= htmlspecialchars($pending->email) ?></span>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 12px; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                        <?= htmlspecialchars(substr($pending->wallet_address, 0, 8)) ?>...<?= htmlspecialchars(substr($pending->wallet_address, -8)) ?>
                                    </code>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--accent-neon); font-size: 12px;"><?= htmlspecialchars($pending->network_type) ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($pending->created_at))) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= url('/admin/user/' . $pending->id . '/profile') ?>" class="admin-btn" style="margin-right: 8px; border-color: #8bf0ff; color: #8bf0ff; background: rgba(139, 240, 255, 0.03); text-decoration: none;">
                                        <i class="fa-solid fa-address-card"></i> View Profile
                                    </a>
                                    <form action="<?= url('/admin/approve/' . $pending->id) ?>" method="POST" style="display: inline-block; margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-approve">
                                            <i class="fa-solid fa-circle-check"></i> Approve Registration
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending USDT Purchase Requests Panel -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #f39c12;"></i> 
            Pending USDT Purchase Requests (<?= count($pendingUSDT ?? []) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 120px;">
            <?php if (empty($pendingUSDT)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 40px 20px; font-size: 13px;">
                    No USDT purchase requests are currently awaiting compliance verification.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Registrant Name</th>
                            <th>Requested Wire Amount</th>
                            <th>Deposit Reference</th>
                            <th>Deposit Proof</th>
                            <th>Submitted At</th>
                            <th style="text-align: right;">Action Desk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingUSDT as $req): ?>
                            <?php 
                            // Fetch user name
                            $db = \App\Core\Database::getConnection();
                            $uStmt = $db->prepare("SELECT name FROM users WHERE id = :id LIMIT 1");
                            $uStmt->execute([':id' => $req->user_id]);
                            $uName = $uStmt->fetchColumn() ?: "Unknown User";
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($uName) ?></strong>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--accent-neon); font-size: 14px;"><?= number_format($req->usdt_amount, 2) ?> USDT</span>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 12px; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                        <?= htmlspecialchars($req->deposit_reference_number) ?>
                                    </code>
                                </td>
                                <td>
                                    <a href="<?= url('/uploads/' . $req->proof_of_deposit) ?>" target="_blank" style="color: #8bf0ff; text-decoration: underline; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-file-pdf"></i> View Proof
                                    </a>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->created_at))) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="<?= url('/admin/buy-usdt/approve/' . $req->id) ?>" method="POST" style="display: inline-block; margin: 0; margin-right: 8px;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-approve">
                                            <i class="fa-solid fa-circle-check"></i> Approve & Credit
                                        </button>
                                    </form>
                                    <form action="<?= url('/admin/buy-usdt/reject/' . $req->id) ?>" method="POST" style="display: inline-block; margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-suspend">
                                            <i class="fa-solid fa-circle-xmark"></i> Reject Request
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historical USDT Purchase Requests Panel -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-neon);"></i> 
            USDT Purchase Requests History (<?= count($historyUSDT ?? []) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 100px;">
            <?php if (empty($historyUSDT)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 30px 20px; font-size: 13px;">
                    No processed USDT purchase requests in history.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Registrant Name</th>
                            <th>Wire Amount</th>
                            <th>Deposit Reference</th>
                            <th>Deposit Proof</th>
                            <th>Processed At</th>
                            <th style="text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historyUSDT as $req): ?>
                            <?php 
                            // Fetch user name
                            $db = \App\Core\Database::getConnection();
                            $uStmt = $db->prepare("SELECT name FROM users WHERE id = :id LIMIT 1");
                            $uStmt->execute([':id' => $req->user_id]);
                            $uName = $uStmt->fetchColumn() ?: "Unknown User";
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($uName) ?></strong>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #fff; font-size: 13.5px;"><?= number_format($req->usdt_amount, 2) ?> USDT</span>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 12px; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                        <?= htmlspecialchars($req->deposit_reference_number) ?>
                                    </code>
                                </td>
                                <td>
                                    <a href="<?= url('/uploads/' . $req->proof_of_deposit) ?>" target="_blank" style="color: #8bf0ff; text-decoration: underline; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-file-pdf"></i> View Proof
                                    </a>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->updated_at))) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($req->status === 'approved'): ?>
                                        <span class="badge badge-active">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-suspended">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending USDT Sell Requests Panel -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #f39c12;"></i> 
            Pending USDT Sell & Liquidation Requests (<?= count($pendingSell ?? []) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 120px;">
            <?php if (empty($pendingSell)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 40px 20px; font-size: 13px;">
                    No USDT liquidation requests are currently awaiting compliance verification.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Registrant Name</th>
                            <th>Requested Sell Amount</th>
                            <th>Receiving Wallet Used</th>
                            <th>Target Bank Coordinates</th>
                            <th>Submitted At</th>
                            <th style="text-align: right;">Action Desk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingSell as $req): ?>
                            <?php 
                            // Fetch user details
                            $db = \App\Core\Database::getConnection();
                            $uStmt = $db->prepare("SELECT name, usdt_balance FROM users WHERE id = :id LIMIT 1");
                            $uStmt->execute([':id' => $req->user_id]);
                            $uUser = $uStmt->fetch();
                            $uName = $uUser->name ?? "Unknown User";
                            $uBalance = $uUser->usdt_balance ?? 0.0;
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($uName) ?></strong>
                                    <small style="display: block; color: var(--text-muted); font-size: 11px;">Wallet Bal: <?= number_format($uBalance, 2) ?> USDT</small>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #ff3a3a; font-size: 14px;">-<?= number_format($req->usdt_amount, 2) ?> USDT</span>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 12px; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                        <?= htmlspecialchars(substr($req->platform_wallet_address, 0, 8)) ?>...<?= htmlspecialchars(substr($req->platform_wallet_address, -8)) ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: #fff; line-height: 1.4;">
                                        <strong>Bank Name:</strong> <?= htmlspecialchars($req->bank_name) ?><br>
                                        <strong>Account No:</strong> <?= htmlspecialchars($req->bank_account_number) ?><br>
                                        <strong>Holder:</strong> <?= htmlspecialchars($req->bank_account_holder) ?><br>
                                        <strong>SWIFT:</strong> <?= htmlspecialchars($req->bank_swift) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->created_at))) ?></span>
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <form action="<?= url('/admin/sell-usdt/approve/' . $req->id) ?>" method="POST" style="display: inline-block; margin: 0; margin-right: 8px;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-approve" <?= ($uBalance < $req->usdt_amount) ? 'disabled style="opacity: 0.5; cursor: not-allowed;" title="Insufficient user balance"' : '' ?>>
                                            <i class="fa-solid fa-circle-check"></i> Approve & Deduct
                                        </button>
                                    </form>
                                    <form action="<?= url('/admin/sell-usdt/reject/' . $req->id) ?>" method="POST" style="display: inline-block; margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-suspend">
                                            <i class="fa-solid fa-circle-xmark"></i> Reject Request
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historical USDT Sell Requests Panel -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-neon);"></i> 
            USDT Sell & Liquidation Requests History (<?= count($historySell ?? []) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 100px;">
            <?php if (empty($historySell)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 30px 20px; font-size: 13px;">
                    No processed USDT liquidation requests in history.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Registrant Name</th>
                            <th>Liquidated Amount</th>
                            <th>Receiving Wallet</th>
                            <th>Target Bank Coordinates</th>
                            <th>Processed At</th>
                            <th style="text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historySell as $req): ?>
                            <?php 
                            // Fetch user name
                            $db = \App\Core\Database::getConnection();
                            $uStmt = $db->prepare("SELECT name FROM users WHERE id = :id LIMIT 1");
                            $uStmt->execute([':id' => $req->user_id]);
                            $uName = $uStmt->fetchColumn() ?: "Unknown User";
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($uName) ?></strong>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #fff; font-size: 13.5px;">-<?= number_format($req->usdt_amount, 2) ?> USDT</span>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 12px; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                        <?= htmlspecialchars(substr($req->platform_wallet_address, 0, 8)) ?>...<?= htmlspecialchars(substr($req->platform_wallet_address, -8)) ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                                        <strong>Bank Name:</strong> <?= htmlspecialchars($req->bank_name) ?><br>
                                        <strong>Account No:</strong> <?= htmlspecialchars($req->bank_account_number) ?><br>
                                        <strong>Holder:</strong> <?= htmlspecialchars($req->bank_account_holder) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($req->updated_at))) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($req->status === 'approved'): ?>
                                        <span class="badge badge-active">Approved & Deducted</span>
                                    <?php else: ?>
                                        <span class="badge badge-suspended">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Accounts Panel -->
    <div>
        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-users" style="color: var(--accent-neon);"></i> 
            Active Customers Enrolled (<?= count($activeUsers) ?>)
        </h3>
        
        <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 120px;">
            <?php if (empty($activeUsers)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 40px 20px; font-size: 13px;">
                    No active customers have been approved yet.
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Secure Login ID</th>
                            <th>Email Address</th>
                            <th>Network Protocol</th>
                            <th>Status Badge</th>
                            <th style="text-align: right;">Perimeter Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($activeUsers as $act): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px; position: relative;">
                                        <strong style="color: #fff; font-size: 14px;"><?= htmlspecialchars($act->name) ?></strong>
                                        <div class="download-dropdown" style="position: relative; display: inline-block;">
                                            <button type="button" class="download-trigger-btn" onclick="toggleDownloadDropdown(event, <?= $act->id ?>)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: inline-flex; align-items: center; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-neon)'" onmouseout="this.style.color='var(--text-muted)'">
                                                <i class="fa-solid fa-circle-down" style="font-size: 16px;"></i>
                                            </button>
                                            <div id="download-dropdown-<?= $act->id ?>" class="download-dropdown-menu" style="display: none; position: absolute; left: 0; top: 28px; background: #111a1d; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 100; min-width: 150px; overflow: hidden;">
                                                <a href="<?= url('/admin/user/' . $act->id . '/download?format=pdf') ?>" style="display: block; padding: 10px 16px; color: #fff; font-size: 12px; text-decoration: none; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='none'">
                                                    <i class="fa-solid fa-file-pdf" style="color: #e74c3c; margin-right: 8px;"></i> Download PDF
                                                </a>
                                                <a href="<?= url('/admin/user/' . $act->id . '/download?format=excel') ?>" style="display: block; padding: 10px 16px; color: #fff; font-size: 12px; text-decoration: none; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='none'">
                                                    <i class="fa-solid fa-file-excel" style="color: #2ecc71; margin-right: 8px;"></i> Download Excel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code style="font-family: var(--font-mono); font-size: 13px; color: var(--accent-neon); font-weight: bold;"><?= htmlspecialchars($act->login_id) ?></code>
                                </td>
                                <td>
                                    <span style="color: var(--text-muted);"><?= htmlspecialchars($act->email) ?></span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #fff; font-size: 12px;"><?= htmlspecialchars($act->network_type) ?></span>
                                </td>
                                <td>
                                    <?php if ($act->status === 'active'): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-suspended">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <a href="<?= url('/admin/user/' . $act->id . '/profile') ?>" class="admin-btn" title="Profile & Permissions" style="border-color: #8bf0ff; color: #8bf0ff; background: rgba(139, 240, 255, 0.03); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; position: relative;">
                                        <i class="fa-solid fa-address-card"></i>
                                        <?php if ($act->edit_permission_status === 'requested' || $act->edit_permission_status === 'pending_approval'): ?>
                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent-neon); box-shadow: 0 0 8px var(--accent-neon); position: absolute; top: -3px; right: -3px;"></span>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <!-- SDM Selfie Link button -->
                                    <button type="button" class="admin-btn" title="SDM Selfie Upload Link" onclick="openSdmSelfieModal(<?= $act->id ?>, '<?= htmlspecialchars(addslashes($act->name)) ?>', '<?= htmlspecialchars(addslashes($act->sdm_selfie_link ?? '')) ?>')" style="border-color: var(--accent-neon); color: var(--accent-neon); background: rgba(185, 255, 58, 0.03); padding: 6px 10px;">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>
                                    
                                    <!-- Ask for more documents button -->
                                    <button type="button" class="admin-btn" title="Request Documents" onclick="openRequestDocsModal(<?= $act->id ?>, '<?= htmlspecialchars(addslashes($act->name)) ?>', '<?= htmlspecialchars(addslashes($act->requested_documents ?? '')) ?>')" style="border-color: #f39c12; color: #f39c12; background: rgba(243, 156, 18, 0.03); padding: 6px 10px;">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </button>

                                    <!-- Update Buy USDT Bank Details button -->
                                    <button type="button" class="admin-btn" title="Update Buy USDT Bank Details" onclick="openUpdateBuyBankModal(<?= $act->id ?>, '<?= htmlspecialchars(addslashes($act->name)) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_bank_name ?? '')) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_bank_address ?? '')) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_routing_no ?? '')) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_account_no ?? '')) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_beneficiary ?? '')) ?>', '<?= htmlspecialchars(addslashes($act->buy_usdt_bank_pdf ?? '')) ?>')" style="border-color: #9b59b6; color: #9b59b6; background: rgba(155, 89, 182, 0.03); padding: 6px 10px;">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </button>

                                    <?php if ($act->status === 'active'): ?>
                                        <form action="<?= url('/admin/suspend/' . $act->id) ?>" method="POST" style="display: inline-block; margin: 0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="admin-btn admin-btn-suspend" title="Suspend Node" style="padding: 6px 10px;">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= url('/admin/activate/' . $act->id) ?>" method="POST" style="display: inline-block; margin: 0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="admin-btn admin-btn-activate" title="Reactivate Node" style="padding: 6px 10px;">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- SDM Selfie Modal -->
<dialog id="sdmSelfieModal" style="border: 1px solid rgba(185, 255, 58, 0.3); background: #0c1417; color: #fff; padding: 24px; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.8); outline: none;">
    <h3 style="margin-top: 0; font-size: 18px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-camera" style="color: var(--accent-neon);"></i>
        SDM Selfie Upload Link
    </h3>
    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
        Specify the selfie verification upload link for <strong id="sdmUserName" style="color: #fff;"></strong>. Once submitted, this link will be visible to the user and sent via email.
    </p>
    <form action="" method="POST">
        <?= csrf_field() ?>
        <div style="margin-bottom: 20px;">
            <label for="sdm_selfie_link" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Selfie Upload Link</label>
            <input type="url" id="sdm_selfie_link" name="sdm_selfie_link" required placeholder="https://example.com/upload/..." style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--accent-neon)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="admin-btn" onclick="document.getElementById('sdmSelfieModal').close()" style="border-color: rgba(255,255,255,0.15); color: var(--text-muted); padding: 8px 16px; border-radius: 30px;">Cancel</button>
            <button type="submit" class="admin-btn admin-btn-approve" style="padding: 8px 16px; border-radius: 30px;">Submit & Send</button>
        </div>
    </form>
</dialog>

<!-- Request Documents Modal -->
<dialog id="requestDocsModal" style="border: 1px solid rgba(243, 156, 18, 0.3); background: #0c1417; color: #fff; padding: 24px; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.8); outline: none;">
    <h3 style="margin-top: 0; font-size: 18px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-file-signature" style="color: #f39c12;"></i>
        Request Additional Documents
    </h3>
    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
        Specify the list of required documents for <strong id="docUserName" style="color: #fff;"></strong>. Once submitted, this list will be visible in the user's portal and sent via email.
    </p>
    <form action="" method="POST">
        <?= csrf_field() ?>
        <div style="margin-bottom: 20px;">
            <label for="requested_documents" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">List of Required Documents</label>
            <textarea id="requested_documents" name="requested_documents" required rows="4" placeholder="- Proof of Address (e.g. Utility Bill)&#10;- Source of Wealth statement" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s; resize: vertical; font-family: inherit;" onfocus="this.style.borderColor='#f39c12'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="admin-btn" onclick="document.getElementById('requestDocsModal').close()" style="border-color: rgba(255,255,255,0.15); color: var(--text-muted); padding: 8px 16px; border-radius: 30px;">Cancel</button>
            <button type="submit" class="admin-btn" style="border-color: #f39c12; color: #f39c12; background: rgba(243, 156, 18, 0.03); padding: 8px 16px; border-radius: 30px;" onmouseover="this.style.background='#f39c12'; this.style.color='#000';" onmouseout="this.style.background='rgba(243, 156, 18, 0.03)'; this.style.color='#f39c12';">Submit & Send</button>
        </div>
    </form>
</dialog>

<!-- Update Buy USDT Bank Details Modal -->
<dialog id="updateBuyBankModal" style="border: 1px solid rgba(155, 89, 182, 0.3); background: #0c1417; color: #fff; padding: 24px; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.8); outline: none;">
    <h3 style="margin-top: 0; font-size: 18px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-building-columns" style="color: #9b59b6;"></i>
        Update Buy USDT Bank Details
    </h3>
    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
        Update the secure receiving bank details that <strong id="buyBankUserName" style="color: #fff;"></strong> will see when purchasing USDT, along with an optional wire instructions PDF.
    </p>
    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div style="margin-bottom: 12px;">
            <label for="buy_usdt_bank_name_input" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Receiving Bank Name</label>
            <input type="text" id="buy_usdt_bank_name_input" name="buy_usdt_bank_name" placeholder="e.g. Signature Institutional Trust" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 10px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="buy_usdt_bank_address_input" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Receiving Bank Address</label>
            <input type="text" id="buy_usdt_bank_address_input" name="buy_usdt_bank_address" placeholder="e.g. 40 Wall Street, Floor 28, New York, NY 10005, USA" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 10px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="buy_usdt_routing_no_input" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Routing No ABA</label>
            <input type="text" id="buy_usdt_routing_no_input" name="buy_usdt_routing_no" placeholder="e.g. 026008673" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 10px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="buy_usdt_account_no_input" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Beneficiary Account Number</label>
            <input type="text" id="buy_usdt_account_no_input" name="buy_usdt_account_no" placeholder="e.g. 4567891230" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 10px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="buy_usdt_beneficiary_input" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Beneficiary Name</label>
            <input type="text" id="buy_usdt_beneficiary_input" name="buy_usdt_beneficiary" placeholder="e.g. Wires4 USDT Digital LLC" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 10px; color: #fff; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9b59b6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Wire Instructions PDF</label>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <input type="file" id="buy_usdt_bank_pdf_picker" name="buy_usdt_bank_pdf" accept="application/pdf" style="display: none;" onchange="document.getElementById('pdf-selected-label').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                <button type="button" class="admin-btn" onclick="document.getElementById('buy_usdt_bank_pdf_picker').click()" style="border-color: rgba(255,255,255,0.15); color: #fff; padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; width: fit-content; border-radius: 8px;">
                    <i class="fa-solid fa-file-pdf"></i> Upload PDF
                </button>
                <span id="pdf-selected-label" style="font-size: 11px; color: var(--text-muted);">No file chosen</span>
                <div id="current-pdf-container" style="display: none; font-size: 11px; color: var(--accent-neon); font-weight: bold; margin-top: 4px;">
                    Current PDF: <span id="current-pdf-name"></span>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="admin-btn" onclick="document.getElementById('updateBuyBankModal').close()" style="border-color: rgba(255,255,255,0.15); color: var(--text-muted); padding: 8px 16px; border-radius: 30px;">Cancel</button>
            <button type="submit" class="admin-btn" style="border-color: #9b59b6; color: #9b59b6; background: rgba(155, 89, 182, 0.03); padding: 8px 16px; border-radius: 30px;" onmouseover="this.style.background='#9b59b6'; this.style.color='#000';" onmouseout="this.style.background='rgba(155, 89, 182, 0.03)'; this.style.color='#9b59b6';">Update Details</button>
        </div>
    </form>
</dialog>

<script>
    function toggleDownloadDropdown(event, id) {
        event.stopPropagation();
        
        // Hide all other dropdowns first
        document.querySelectorAll('.download-dropdown-menu').forEach(menu => {
            if (menu.id !== 'download-dropdown-' + id) {
                menu.style.display = 'none';
            }
        });

        const dropdown = document.getElementById('download-dropdown-' + id);
        if (dropdown) {
            dropdown.style.display = (dropdown.style.display === 'none' || dropdown.style.display === '') ? 'block' : 'none';
        }
    }

    // Close dropdowns when clicking outside
    window.addEventListener('click', function() {
        document.querySelectorAll('.download-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    });

    // Modal helpers
    const sdmModal = document.getElementById('sdmSelfieModal');
    const sdmForm = sdmModal.querySelector('form');
    const sdmUserName = document.getElementById('sdmUserName');

    function openSdmSelfieModal(userId, userName, currentLink) {
        sdmForm.action = "<?= url('/admin/user/') ?>" + userId + "/sdm-selfie";
        sdmUserName.textContent = userName;
        document.getElementById('sdm_selfie_link').value = currentLink || '';
        sdmModal.showModal();
    }

    const docModal = document.getElementById('requestDocsModal');
    const docForm = docModal.querySelector('form');
    const docUserName = document.getElementById('docUserName');

    function openRequestDocsModal(userId, userName, currentDocs) {
        docForm.action = "<?= url('/admin/user/') ?>" + userId + "/request-docs";
        docUserName.textContent = userName;
        document.getElementById('requested_documents').value = currentDocs || '';
        docModal.showModal();
    }

    const buyBankModal = document.getElementById('updateBuyBankModal');
    const buyBankForm = buyBankModal.querySelector('form');
    const buyBankUserName = document.getElementById('buyBankUserName');

    function openUpdateBuyBankModal(userId, userName, bankName, bankAddress, routingNo, accountNo, beneficiary, bankPdf) {
        buyBankForm.action = "<?= url('/admin/user/') ?>" + userId + "/buy-bank";
        buyBankUserName.textContent = userName;
        document.getElementById('buy_usdt_bank_name_input').value = bankName || '';
        document.getElementById('buy_usdt_bank_address_input').value = bankAddress || '';
        document.getElementById('buy_usdt_routing_no_input').value = routingNo || '';
        document.getElementById('buy_usdt_account_no_input').value = accountNo || '';
        document.getElementById('buy_usdt_beneficiary_input').value = beneficiary || '';
        document.getElementById('buy_usdt_bank_pdf_picker').value = '';
        document.getElementById('pdf-selected-label').textContent = 'No file chosen';
        
        const currentPdfContainer = document.getElementById('current-pdf-container');
        if (bankPdf) {
            document.getElementById('current-pdf-name').textContent = bankPdf;
            currentPdfContainer.style.display = 'block';
        } else {
            currentPdfContainer.style.display = 'none';
        }
        
        buyBankModal.showModal();
    }
</script>
