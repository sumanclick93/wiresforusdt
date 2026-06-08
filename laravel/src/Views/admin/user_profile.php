<div class="auth-card" style="max-width: 1050px; width: 100%;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">COMPLIANCE VERIFICATION DESK</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">Profile Specs — <?= htmlspecialchars($user->name) ?></h1>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= url('/admin/dashboard') ?>" class="btn btn-ghost" style="padding: 8px 16px; font-size: 12px; border-radius: 30px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Return Board
            </a>
            <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-lock" style="font-size: 10px;"></i>
                ADMIN CONTROLS
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <i class="fa-solid fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 24px;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Account Status Action Bar -->
    <?php if ($user->status === 'pending_review'): ?>
        <div style="background: rgba(243, 156, 18, 0.05); border: 1px solid rgba(243, 156, 18, 0.25); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Account Registration Pending Review</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">This client's account registration is currently pending compliance review. Validate their specifications and documents below before approving.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form action="<?= url('/admin/approve/' . $user->id) ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 12.5px; border: none; border-radius: 30px; color: #000; box-shadow: 0 0 15px rgba(185, 255, 58, 0.25); font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-circle-check"></i> Approve Registration
                    </button>
                </form>
                <form action="<?= url('/admin/suspend/' . $user->id) ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-suspend" style="padding: 10px 20px; font-size: 12.5px; border-radius: 30px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-ban"></i> Suspend/Reject User
                    </button>
                </form>
            </div>
        </div>
    <?php elseif ($user->status === 'suspended'): ?>
        <div style="background: rgba(231, 76, 60, 0.05); border: 1px solid rgba(231, 76, 60, 0.25); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Account Suspended</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">This client's account is currently suspended. Payouts and logins are disabled.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form action="<?= url('/admin/activate/' . $user->id) ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 12.5px; border: none; border-radius: 30px; color: #000; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-circle-check"></i> Reactivate Account
                    </button>
                </form>
            </div>
        </div>
    <?php elseif ($user->status === 'active'): ?>
        <div style="background: rgba(46, 204, 113, 0.05); border: 1px solid rgba(46, 204, 113, 0.25); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Account Status: Active</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">This client's account is active and verified. All liquidity services are available.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form action="<?= url('/admin/suspend/' . $user->id) ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-suspend" style="padding: 10px 20px; font-size: 12.5px; border-radius: 30px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-ban"></i> Suspend Account
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Bars based on edit_permission_status -->
    <?php 
    $status = $user->edit_permission_status ?? 'none';
    if ($status === 'requested'): 
    ?>
        <div style="background: rgba(243, 156, 18, 0.05); border: 1px solid rgba(243, 156, 18, 0.25); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Profile Edit Requested</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">This client has requested permission to modify their onboarding details. Granting permission will unlock their inputs.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form action="<?= url('/admin/user/' . $user->id . '/allow-edit') ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 12.5px; border: none; border-radius: 30px; color: #000;">
                        <i class="fa-solid fa-circle-check"></i> Authorize Edit Access
                    </button>
                </form>
                <form action="<?= url('/admin/user/' . $user->id . '/deny-edit') ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-suspend" style="padding: 10px 20px; font-size: 12.5px; border-radius: 30px;">
                        <i class="fa-solid fa-circle-xmark"></i> Deny Request
                    </button>
                </form>
            </div>
        </div>
    <?php elseif ($status === 'pending_approval'): ?>
        <div style="background: rgba(52, 152, 219, 0.05); border: 1px solid rgba(52, 152, 219, 0.25); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Onboarding Profile Updates Pending Approval</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Review the highlighted pending changes below. Approving will merge these modifications into their live profile.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form action="<?= url('/admin/user/' . $user->id . '/approve-updates') ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 12.5px; border: none; border-radius: 30px; color: #000; box-shadow: 0 0 15px rgba(185, 255, 58, 0.25);">
                        <i class="fa-solid fa-circle-check"></i> Approve Profile Updates
                    </button>
                </form>
                <form action="<?= url('/admin/user/' . $user->id . '/reject-updates') ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-suspend" style="padding: 10px 20px; font-size: 12.5px; border-radius: 30px;">
                        <i class="fa-solid fa-ban"></i> Reject Updates
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Display grid comparison of pending updates if present -->
    <?php if ($status === 'pending_approval' && !empty($pendingUpdates)): ?>
        <div style="background: rgba(185, 255, 58, 0.02); border: 1px solid rgba(185, 255, 58, 0.15); border-radius: 12px; padding: 24px; margin-bottom: 30px;">
            <h3 style="font-size: 15px; font-weight: bold; color: var(--accent-neon); margin: 0 0 16px 0; text-transform: uppercase;">
                <i class="fa-solid fa-magnifying-glass-chart"></i> Pending Modifications Diff Matrix
            </h3>
            
            <div style="overflow-x: auto; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px;">
                <table class="admin-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Specification Field</th>
                            <th>Original Value</th>
                            <th style="color: var(--accent-neon);">Proposed Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $anyChanges = false;
                        foreach ($pendingUpdates as $field => $proposedValue):
                            $originalValue = $application->$field ?? '';
                            if (trim($proposedValue) !== trim($originalValue)):
                                $anyChanges = true;
                                $fieldLabel = ucwords(str_replace('_', ' ', $field));
                        ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 13.5px;"><?= $fieldLabel ?></strong>
                                    <code style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-muted); display: block;"><?= $field ?></code>
                                </td>
                                <td>
                                    <?php if (str_ends_with($field, '_file')): ?>
                                        <!-- Render as file list -->
                                        <?php 
                                        $origFiles = [];
                                        if (!empty($originalValue)) {
                                            $decoded = json_decode($originalValue, true);
                                            $origFiles = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$originalValue];
                                        }
                                        if (empty($origFiles)):
                                            echo '<span style="color: #e74c3c; text-decoration: line-through; font-size: 13.5px;">Empty</span>';
                                        else:
                                            foreach ($origFiles as $f):
                                                if (empty($f)) continue;
                                        ?>
                                            <a href="<?= url('/uploads/' . urlencode($f)) ?>" target="_blank" style="color: #e74c3c; text-decoration: line-through; font-size: 12.5px; display: block; font-family: var(--font-mono); font-weight: bold;"><?= htmlspecialchars($f) ?></a>
                                        <?php 
                                            endforeach;
                                        endif; 
                                        ?>
                                    <?php else: ?>
                                        <span style="color: #e74c3c; text-decoration: line-through; font-size: 13.5px;"><?= htmlspecialchars($originalValue ?: 'Empty') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (str_ends_with($field, '_file')): ?>
                                        <!-- Render as file list -->
                                        <?php 
                                        $propFiles = [];
                                        if (!empty($proposedValue)) {
                                            $decoded = json_decode($proposedValue, true);
                                            $propFiles = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$proposedValue];
                                        }
                                        if (empty($propFiles)):
                                            echo '<span style="color: var(--accent-neon); font-size: 13.5px;">Empty</span>';
                                        else:
                                            foreach ($propFiles as $f):
                                                if (empty($f)) continue;
                                        ?>
                                            <a href="<?= url('/uploads/' . urlencode($f)) ?>" target="_blank" style="color: var(--accent-neon); font-weight: bold; font-size: 12.5px; display: block; font-family: var(--font-mono); text-decoration: underline;"><?= htmlspecialchars($f) ?></a>
                                        <?php 
                                            endforeach;
                                        endif; 
                                        ?>
                                    <?php else: ?>
                                        <span style="color: var(--accent-neon); font-weight: bold; font-size: 13.5px; background: rgba(185, 255, 58, 0.08); padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(185, 255, 58, 0.2);">
                                            <?= htmlspecialchars($proposedValue ?: 'Empty') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$anyChanges):
                        ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                    No changes detected between original values and proposed values.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- User onboarding data summary inside tab layout -->
    <div style="display: grid; grid-template-columns: 240px 1fr; gap: 32px; align-items: start; flex-wrap: wrap;">
        
        <!-- Left Sidebar Navigation Tabs -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <button type="button" class="tab-btn active" onclick="switchProfileTab(event, 'personal-spec')">
                <i class="fa-solid fa-address-card"></i> 
                <?= ($application && $application->account_type === 'corporate') ? 'Corporate Details' : 'Personal Details' ?>
            </button>
            <button type="button" class="tab-btn" onclick="switchProfileTab(event, 'trading-spec')">
                <i class="fa-solid fa-chart-line"></i> Financial Specs
            </button>
            <button type="button" class="tab-btn" onclick="switchProfileTab(event, 'banking-spec')">
                <i class="fa-solid fa-building-columns"></i> Banking Details
            </button>
            <button type="button" class="tab-btn" onclick="switchProfileTab(event, 'risk-spec')">
                <i class="fa-solid fa-shield-halved"></i> Risk Declarations
            </button>
            <button type="button" class="tab-btn" onclick="switchProfileTab(event, 'node-spec')">
                <i class="fa-solid fa-wallet"></i> Settlement Node
            </button>
            <button type="button" class="tab-btn" onclick="switchProfileTab(event, 'doc-spec')">
                <i class="fa-solid fa-folder-open"></i> Uploaded Files
            </button>
        </div>

        <!-- Right Content Panels -->
        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.04); border-radius: 16px; padding: 30px; min-height: 400px;">
            
            <?php 
            // In admin view, fields are ALWAYS read-only
            $inputClass = "form-control read-only-field";
            ?>

            <!-- Tab 1: Personal or Corporate details -->
            <div id="personal-spec" class="tab-panel active-panel">
                <?php if ($application && $application->account_type === 'corporate'): ?>
                    <!-- Corporate specifications -->
                    <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                        Corporate Profile Details
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_name ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Legal Entity Identifier (LEI)</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->lei_identifier ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Entity Type</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->entity_type ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Registration Number</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_reg_number ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country of Incorporation</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->incorporation_country ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Incorporation</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->incorporation_date ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company Regulated Status</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_regulated ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nature of Business</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->nature_of_business ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Corporate Contact Phone</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->contact_number ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Business Website</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->website ?? '') ?>" disabled>
                        </div>
                    </div>
                    <h3 style="font-size: 15px; color: #fff; margin: 24px 0 16px 0; font-weight: 700; text-transform: uppercase;">Corporate Address Specs</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Corporate Street Address</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->street_address_entity ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->city_entity ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State / Region</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->state_entity ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->postal_entity ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->country_entity ?? '') ?>" disabled>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Individual specifications -->
                    <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                        Personal Profile Details
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_name ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->middle_name ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->last_name ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Occupation / Title</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->title_occupation ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->dob ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->phone_number ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country of Residence</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->country ?? '') ?>" disabled>
                        </div>
                    </div>
                    <h3 style="font-size: 15px; color: #fff; margin: 24px 0 16px 0; font-weight: 700; text-transform: uppercase;">Residential Address Details</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->street_address ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit / Apartment</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->unit_apartment ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->city ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State / Province</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->state_province ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal / ZIP Code</label>
                            <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->postal_zip ?? '') ?>" disabled>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 2: Trading & Financials -->
            <div id="trading-spec" class="tab-panel">
                <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    Financial & Trading Specifications
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Trading Purpose</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->trading_purpose ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intended Onboarding Date</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_date ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Flow of Funds</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->flow_of_funds ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">First Trade Currency</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_currency ?? 'USDT') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">First Trade Estimate Size</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_size ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monthly Target Volume</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->monthly_volume_size ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Annual Income Capacity</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->annual_income_amount ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Liquid Assets Size</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->liquid_assets_amount ?? '') ?>" disabled>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Source of Funding Specs</label>
                        <textarea rows="3" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->source_funding ?? '') ?></textarea>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Trading Strategy Description</label>
                        <textarea rows="3" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->trading_purpose_desc ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Banking Specs -->
            <div id="banking-spec" class="tab-panel">
                <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    Beneficiary Banking Details
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_name ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Holder</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_account_holder ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Number / IBAN</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_account_number ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Routing / Sort Code</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_routing_code ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SWIFT / BIC Code</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_swift ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Currency</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_currency ?? 'USD') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bank Country</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_country ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intermediary Bank (if any)</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_intermediary ?? '') ?>" disabled>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Beneficiary Physical Address</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_beneficiary_address ?? '') ?>" disabled>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Bank Branch Address</label>
                        <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_address ?? '') ?>" disabled>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Risk Declarations -->
            <div id="risk-spec" class="tab-panel">
                <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    Risk & Regulatory Declarations
                </h3>
                
                <?php if ($application && $application->account_type === 'corporate'): ?>
                    <!-- Corporate Risk Declarations -->
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Has the entity ever declared bankruptcy?</span>
                                <input type="text" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->declared_bankruptcy_entity ?? 'No') ?>" disabled>
                            </div>
                            <label class="form-label">Bankruptcy Description</label>
                            <textarea rows="2" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->declared_bankruptcy_entity_desc ?? '') ?></textarea>
                        </div>

                        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Are any Directors PEPs?</span>
                                <input type="text" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->pep_status_entity ?? 'No') ?>" disabled>
                            </div>
                            <label class="form-label">PEP Status Details</label>
                            <textarea rows="2" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->pep_status_entity_desc ?? '') ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">US Financial Entity Status</label>
                                <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->financial_entity_us ?? '') ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Registered Swap Dealer</label>
                                <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->swap_dealer ?? '') ?>" disabled>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Individual Risk Declarations -->
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Have you ever declared bankruptcy?</span>
                                <input type="text" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->declared_bankruptcy ?? 'No') ?>" disabled>
                            </div>
                            <label class="form-label">Bankruptcy Description</label>
                            <textarea rows="2" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->declared_bankruptcy_desc ?? '') ?></textarea>
                        </div>

                        <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Are you a Politically Exposed Person (PEP)?</span>
                                <input type="text" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->pep_status ?? 'No') ?>" disabled>
                            </div>
                            <label class="form-label">PEP Status Details</label>
                            <textarea rows="2" class="<?= $inputClass ?>" style="resize: none;" disabled><?= htmlspecialchars($application->pep_status_desc ?? '') ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Accredited Investor Status</label>
                                <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->accredited_investor ?? 'No') ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Significant Volume Transactions</label>
                                <input type="text" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->considerable_transactions ?? 'No') ?>" disabled>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 5: Settlement Node Specs -->
            <div id="node-spec" class="tab-panel">
                <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    Settlement Wallet Specifications
                </h3>
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <p style="font-size: 13.5px; color: var(--text-muted); margin: 0; line-height: 1.6;">
                        This is the whitelisted cryptographic payout address for this client.
                    </p>
                    
                    <div class="form-group">
                        <label class="form-label">Whitelisted USDT Settlement Address</label>
                        <input type="text" class="<?= $inputClass ?>" style="font-family: var(--font-mono); font-size: 15px; color: var(--accent-neon); background: rgba(0,0,0,0.6);" value="<?= htmlspecialchars($application->wallet_address ?? '') ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Blockchain Network Standard</label>
                        <input type="text" class="<?= $inputClass ?>" style="font-weight: bold; width: 240px;" value="<?= htmlspecialchars($application->network_type ?? 'ERC-20') ?>" disabled>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Uploaded Files -->
            <div id="doc-spec" class="tab-panel">
                <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                    Uploaded Compliance Documents
                </h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    
                    <?php 
                    $parseFiles = function($val) {
                        if (empty($val)) return [];
                        $decoded = json_decode($val, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            return $decoded;
                        }
                        return [$val];
                    };
                    ?>
                    
                    <!-- Individual Documents -->
                    <?php if ($application && $application->kyc_document_file): ?>
                        <?php foreach ($parseFiles($application->kyc_document_file) as $file): ?>
                            <div class="document-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: #e74c3c;"></i>
                                    <div>
                                        <strong style="color: #fff; font-size: 13.5px; display: block;">Government Identification KYC Document</strong>
                                        <a href="<?= url('/uploads/' . urlencode($file)) ?>" target="_blank" style="font-size: 12px; color: #8bf0ff; font-family: var(--font-mono); text-decoration: underline; font-weight: bold;"><?= htmlspecialchars($file) ?></a>
                                    </div>
                                </div>
                                <span class="badge badge-active"><i class="fa-solid fa-circle-check"></i> VERIFIEDSnap</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($application && $application->proof_funds_file): ?>
                        <?php foreach ($parseFiles($application->proof_funds_file) as $file): ?>
                            <div class="document-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-file-contract" style="font-size: 24px; color: var(--accent-neon);"></i>
                                    <div>
                                        <strong style="color: #fff; font-size: 13.5px; display: block;">Cryptographic Proof of Capital Reserves</strong>
                                        <a href="<?= url('/uploads/' . urlencode($file)) ?>" target="_blank" style="font-size: 12px; color: #8bf0ff; font-family: var(--font-mono); text-decoration: underline; font-weight: bold;"><?= htmlspecialchars($file) ?></a>
                                    </div>
                                </div>
                                <span class="badge badge-active"><i class="fa-solid fa-circle-check"></i> VERIFIEDSnap</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Corporate Onboarding Uploads -->
                    <?php if ($application && $application->account_type === 'corporate'): ?>
                        <?php 
                        $corpDocs = [
                            'entity_articles_file' => 'Articles of Incorporation',
                            'entity_shareholders_file' => 'Shareholder Registry & Structure',
                            'entity_bank_statement_file' => 'Corporate Bank Statement Snapshot',
                            'entity_proof_address_file' => 'Operating Business Address Verification',
                            'entity_board_resolution_file' => 'Authorized Corporate Board Resolution'
                        ];
                        foreach ($corpDocs as $colName => $docLabel):
                            if (!empty($application->$colName)):
                        ?>
                            <div class="document-item">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-file-invoice" style="font-size: 24px; color: #8bf0ff;"></i>
                                    <div>
                                        <strong style="color: #fff; font-size: 13.5px; display: block;"><?= $docLabel ?></strong>
                                        <a href="<?= url('/uploads/' . urlencode($application->$colName)) ?>" target="_blank" style="font-size: 12px; color: #8bf0ff; font-family: var(--font-mono); text-decoration: underline; font-weight: bold;"><?= htmlspecialchars($application->$colName) ?></a>
                                    </div>
                                </div>
                                <span class="badge badge-active"><i class="fa-solid fa-circle-check"></i> VERIFIEDSnap</span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Tab controls */
    .tab-btn {
        width: 100%;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        padding: 14px 18px;
        color: var(--text-muted);
        font-family: var(--font-sans);
        font-size: 13.5px;
        font-weight: 700;
        text-align: left;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.15);
    }
    .tab-btn.active {
        background: rgba(185, 255, 58, 0.08);
        border-color: var(--accent-neon);
        color: var(--accent-neon);
        box-shadow: 0 4px 15px rgba(185, 255, 58, 0.08);
    }
    
    .tab-panel {
        display: none;
    }
    .tab-panel.active-panel {
        display: block;
        animation: fadeInPanel 0.4s ease-out;
    }
    @keyframes fadeInPanel {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .read-only-field {
        background: rgba(0, 0, 0, 0.2) !important;
        border-color: rgba(255, 255, 255, 0.03) !important;
        color: rgba(255, 255, 255, 0.6) !important;
        cursor: not-allowed;
    }

    .document-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255, 255, 255, 0.04);
        padding: 16px 20px;
        border-radius: 10px;
        flex-wrap: wrap;
        gap: 16px;
    }
</style>

<script>
    /**
     * Tab switching mechanism.
     */
    function switchProfileTab(event, tabId) {
        event.preventDefault();
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active-panel'));
        
        // Add active to current button and show targeted panel
        event.currentTarget.classList.add('active');
        const activePanel = document.getElementById(tabId);
        if (activePanel) {
            activePanel.classList.add('active-panel');
        }
    }
</script>
