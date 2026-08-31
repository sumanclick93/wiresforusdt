<?php
// Set specific styles inside the layout
$styles = "
<style>
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
        font-size: 13.5px;
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
        background: rgba(255, 255, 255, 0.015);
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pending {
        background: rgba(243, 156, 18, 0.15);
        color: #f39c12;
        border: 1px solid rgba(243, 156, 18, 0.3);
    }
    .badge-contacted {
        background: rgba(52, 152, 219, 0.15);
        color: #3498db;
        border: 1px solid rgba(52, 152, 219, 0.3);
    }
    .badge-closed {
        background: rgba(185, 255, 58, 0.15);
        color: var(--accent-neon);
        border: 1px solid rgba(185, 255, 58, 0.3);
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
    }
    .admin-btn-danger {
        border-color: #e74c3c;
        color: #e74c3c;
        background: rgba(231, 76, 60, 0.03);
    }
    .admin-btn-danger:hover {
        background: #e74c3c;
        color: #fff;
    }
</style>
";
?>

<div class="auth-card" style="max-width: 1150px; width: 100%;">
    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">EXPO DELEGATION MANAGEMENT</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Enquiry for Expo — Dubai 2026</h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="<?= url('/admin/dashboard') ?>" class="admin-btn" style="padding: 8px 16px; font-size: 12px; border-radius: 30px;">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <a href="<?= url('/admin/dropdowns') ?>" class="admin-btn admin-btn-approve" style="padding: 8px 16px; font-size: 12px; border-radius: 30px;">
                <i class="fa-solid fa-gears"></i> Manage Dropdowns
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon);">
                <i class="fa-solid fa-calendar-star" style="margin-right: 4px;"></i> DUBAI 2026
            </div>
        </div>
    </div>

    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Table of Submissions -->
    <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-height: 200px;">
        <?php if (empty($inquiries)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 60px 20px; font-size: 14px;">
                <i class="fa-solid fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px; color: rgba(255,255,255,0.2);"></i>
                No Dubai Expo 2026 inquiries received yet.
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Attendee / Company</th>
                        <th>Contact Channels</th>
                        <th>Expected Volume</th>
                        <th>Message / Notes</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $item): ?>
                        <tr>
                            <td><strong style="color: var(--text-muted);">#<?= htmlspecialchars($item->id) ?></strong></td>
                            <td>
                                <strong style="color: #fff; font-size: 14px; display: block;"><?= htmlspecialchars($item->name) ?></strong>
                                <span style="font-size: 12px; color: var(--accent-neon);"><i class="fa-solid fa-building" style="margin-right: 4px;"></i><?= htmlspecialchars($item->company) ?></span>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: #fff; margin-bottom: 2px;">
                                    <i class="fa-solid fa-envelope" style="color: var(--accent-neon); margin-right: 6px;"></i><?= htmlspecialchars($item->email) ?>
                                </div>
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    <i class="fa-solid fa-phone" style="color: #25d366; margin-right: 6px;"></i><?= htmlspecialchars($item->phone_number) ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #fff; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; font-size: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                    <?= htmlspecialchars($item->expected_volume ?: 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 240px; font-size: 12px; color: var(--text-muted); line-height: 1.4; word-break: break-word;">
                                    <?= !empty($item->message) ? nl2br(htmlspecialchars($item->message)) : '<em style="opacity:0.5;">No message provided</em>' ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $statusClass = 'badge-pending';
                                    if ($item->status === 'contacted') $statusClass = 'badge-contacted';
                                    if ($item->status === 'closed') $statusClass = 'badge-closed';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= htmlspecialchars(ucfirst($item->status)) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">
                                    <?= htmlspecialchars(date('M d, Y H:i', strtotime($item->created_at))) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    <!-- Status Update Form -->
                                    <form action="<?= url('/admin/expo-inquiries/update-status/' . $item->id) ?>" method="POST" style="display: inline-flex; gap: 4px; margin: 0;">
                                        <?= csrf_field() ?>
                                        <select name="status" style="background: #070e11; color: #fff; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 4px 6px; font-size: 11px;">
                                            <option value="pending" <?= $item->status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="contacted" <?= $item->status === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                            <option value="closed" <?= $item->status === 'closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>
                                        <button type="submit" class="admin-btn admin-btn-approve" title="Save Status">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Form -->
                                    <form action="<?= url('/admin/expo-inquiries/delete/' . $item->id) ?>" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this Expo Inquiry?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn admin-btn-danger" title="Delete Inquiry">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
