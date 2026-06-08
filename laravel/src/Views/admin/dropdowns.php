<div class="auth-card" style="max-width: 1100px; width: 100%; padding: 40px;">
    <!-- View Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED ADMINISTRATIVE ROOT CONFIGURATION</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Dropdown Specification Desk</h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= url('/admin/dashboard') ?>" class="btn btn-ghost" style="padding: 8px 16px; font-size: 12px; border-radius: 30px; display: flex; align-items: center; gap: 6px; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Admin Panel
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-gears" style="font-size: 10px;"></i>
                SYSTEM ENGINE
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

    <!-- Two Column Layout: Tabs on left, CRUD Workspace on right -->
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 32px; align-items: flex-start;">
        
        <!-- Left Tab Panel -->
        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); padding: 20px; border-radius: 16px;">
            <span style="font-size: 10px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 12px;">SELECT SELECT GROUP</span>
            
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($groupLabels as $key => $label): ?>
                    <?php $isActive = ($activeKey === $key); ?>
                    <a href="<?= url('/admin/dropdowns?key=' . urlencode($key)) ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 700; text-decoration: none; border: 1px solid <?= $isActive ? 'rgba(185, 255, 58, 0.2)' : 'transparent' ?>; background: <?= $isActive ? 'rgba(185, 255, 58, 0.04)' : 'transparent' ?>; color: <?= $isActive ? 'var(--accent-neon)' : 'var(--text-muted)' ?>; transition: all 0.2s ease;" class="dropdown-tab-item">
                        <span><?= htmlspecialchars($label) ?></span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 10px; opacity: <?= $isActive ? '1' : '0.3' ?>;"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right CRUD Workspace -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Option Addition Form -->
            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); padding: 24px; border-radius: 16px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0 0 16px 0; text-transform: uppercase;">
                    <i class="fa-solid fa-circle-plus" style="color: var(--accent-neon); margin-right: 8px;"></i>
                    Add Option to <?= htmlspecialchars($groupLabels[$activeKey] ?? $activeKey) ?>
                </h3>

                <form action="<?= url('/admin/dropdowns/add') ?>" method="POST" style="margin: 0; display: flex; gap: 16px; align-items: flex-end;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="dropdown_key" value="<?= htmlspecialchars($activeKey) ?>">

                    <div style="flex: 1;">
                        <label class="form-label" style="margin-bottom: 8px;">Option Label / Value</label>
                        <input type="text" name="option_value" class="form-control" placeholder="e.g. Custom Option Value" required style="padding: 12px 16px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 12px 28px; border-radius: 10px; font-size: 13px; font-weight: bold; border: none; color: #000; box-shadow: 0 0 15px rgba(185, 255, 58, 0.15); display: flex; align-items: center; gap: 8px; height: 47px;">
                        <i class="fa-solid fa-plus"></i> Add Option
                    </button>
                </form>
            </div>

            <!-- Existing List Panel -->
            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); padding: 24px; border-radius: 16px;">
                <h3 style="font-size: 15px; font-weight: bold; color: #fff; margin: 0 0 16px 0; text-transform: uppercase; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 12px;">
                    <i class="fa-solid fa-list-check" style="color: var(--accent-neon); margin-right: 8px;"></i>
                    Active Database Options (<?= count($options) ?>)
                </h3>

                <div style="overflow-x: auto;">
                    <?php if (empty($options)): ?>
                        <div style="text-align: center; color: var(--text-muted); padding: 40px 10px; font-size: 13px;">
                            No options currently configured for this dropdown group.
                        </div>
                    <?php else: ?>
                        <table class="admin-table" style="margin-top: 0;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                    <th style="padding: 12px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; font-weight: bold;">Option Value</th>
                                    <th style="padding: 12px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; font-weight: bold;">System ID</th>
                                    <th style="padding: 12px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; font-weight: bold; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($options as $opt): ?>
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                                        <td style="padding: 12px; font-weight: bold; color: #fff;">
                                            <?= htmlspecialchars($opt->option_value) ?>
                                        </td>
                                        <td style="padding: 12px; color: var(--text-muted); font-family: monospace; font-size: 12.5px;">
                                            #<?= $opt->id ?>
                                        </td>
                                        <td style="padding: 12px; text-align: right;">
                                            <form action="<?= url('/admin/dropdowns/delete/' . $opt->id) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this option?')" style="display: inline-block; margin: 0;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="admin-btn admin-btn-suspend" style="padding: 6px 12px; border-radius: 6px;">
                                                    <i class="fa-solid fa-trash-can"></i> Delete
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

        </div>

    </div>
</div>

<style>
    .dropdown-tab-item:hover {
        background: rgba(255, 255, 255, 0.02) !important;
        color: #fff !important;
    }
</style>
