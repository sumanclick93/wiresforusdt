<div class="auth-card" style="max-width: 1050px; width: 100%;">
    <!-- Profile Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">SECURED SPECIFICATION SHEET</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #fff;">My Profile</h1>
        </div>
        <div style="background: rgba(185, 255, 58, 0.08); border: 1px solid rgba(185, 255, 58, 0.2); padding: 8px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: var(--accent-neon); display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 10px;"></i>
            <?= htmlspecialchars($user->role === 'admin' ? 'ADMIN ROOT' : 'VERIFIED ACCOUNT') ?>
        </div>
    </div>

    <!-- Feedback messages -->
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

    <!-- State banner logic -->
    <?php 
    $status = $user->edit_permission_status ?? 'none';
    ?>

    <!-- Document Request Active Info Banner -->
    <?php if (!empty($user->requested_documents) && $status === 'allowed'): ?>
        <div style="background: rgba(243, 156, 18, 0.05); border: 1px solid rgba(243, 156, 18, 0.25); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(243, 156, 18, 0.1); display: flex; align-items: center; justify-content: center; color: #f39c12; flex-shrink: 0;">
                <i class="fa-solid fa-file-signature" style="font-size: 16px;"></i>
            </div>
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 4px 0;">Additional Documents Required</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 10px 0; line-height: 1.4;">Compliance department has requested additional documents. You can upload them directly under the <strong>Uploaded Files</strong> tab.</p>
                <div style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(243, 156, 18, 0.15); padding: 10px 14px; border-radius: 6px; color: #fff; font-size: 12.5px; white-space: pre-wrap; font-weight: 500; font-family: inherit; max-width: 600px;"><?= htmlspecialchars($user->requested_documents) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($status === 'none'): ?>
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #fff;">
                    <i class="fa-solid fa-lock" style="font-size: 16px;"></i>
                </div>
                <div>
                    <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Specifications Gated / Read-Only</h4>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">To update your verified onboarding details, request edit authorization from administrators.</p>
                </div>
            </div>
            <form action="<?= url('/profile/request-edit') ?>" method="POST" style="margin: 0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-ghost" style="padding: 10px 20px; font-size: 12.5px; border-radius: 30px; border-color: var(--accent-neon); color: var(--accent-neon); display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-key"></i> Request Edit Permission
                </button>
            </form>
        </div>
    <?php elseif ($status === 'requested'): ?>
        <div style="background: rgba(243, 156, 18, 0.05); border: 1px solid rgba(243, 156, 18, 0.25); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; margin-bottom: 30px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(243, 156, 18, 0.1); display: flex; align-items: center; justify-content: center; color: #f39c12;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 16px;"></i>
            </div>
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Edit Authorization Requested</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Your request is pending review by compliance administrators. The fields will unlock once authorized.</p>
            </div>
        </div>
    <?php elseif ($status === 'allowed' && empty($user->requested_documents)): ?>
        <div style="background: rgba(185, 255, 58, 0.05); border: 1px solid rgba(185, 255, 58, 0.25); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; margin-bottom: 30px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(185, 255, 58, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-neon);">
                <i class="fa-solid fa-unlock-keyhole" style="font-size: 16px;"></i>
            </div>
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Edit Authorization Active</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Input fields are now unlocked. Modify details and submit; updates will queue for administrator approval.</p>
            </div>
        </div>
    <?php elseif ($status === 'pending_approval'): ?>
        <div style="background: rgba(52, 152, 219, 0.05); border: 1px solid rgba(52, 152, 219, 0.25); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; margin-bottom: 30px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(52, 152, 219, 0.1); display: flex; align-items: center; justify-content: center; color: #3498db;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 16px;"></i>
            </div>
            <div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #fff; margin: 0 0 2px 0;">Updates Pending Compliance Approval</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 0;">You have submitted changes. These will be reviewed by compliance administrators before reflecting in your profile.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Profile Specs Form + Tab Sidebar Layout -->
    <form id="profile-update-form" action="<?= url('/profile/update') ?>" method="POST" style="margin: 0;">
        <?= csrf_field() ?>
        
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
            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.04); border-radius: 16px; padding: 30px; min-height: 400px; position: relative;">
                
                <?php 
                $disabledAttr = ($status === 'allowed' && empty($user->requested_documents)) ? '' : 'disabled'; 
                $inputClass = "form-control " . (($status === 'allowed' && empty($user->requested_documents)) ? '' : 'read-only-field');
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
                                <input type="text" name="company_name" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_name ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Legal Entity Identifier (LEI)</label>
                                <input type="text" name="lei_identifier" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->lei_identifier ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Entity Type</label>
                                <input type="text" name="entity_type" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->entity_type ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Registration Number</label>
                                <input type="text" name="company_reg_number" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_reg_number ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Country of Incorporation</label>
                                <input type="text" name="incorporation_country" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->incorporation_country ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Incorporation</label>
                                <input type="text" name="incorporation_date" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->incorporation_date ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Company Regulated Status</label>
                                <input type="text" name="company_regulated" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->company_regulated ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nature of Business</label>
                                <input type="text" name="nature_of_business" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->nature_of_business ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Corporate Contact Phone</label>
                                <input type="text" name="contact_number" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->contact_number ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Business Website</label>
                                <input type="text" name="website" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->website ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                        </div>
                        <h3 style="font-size: 15px; color: #fff; margin: 24px 0 16px 0; font-weight: 700; text-transform: uppercase;">Corporate Address Specs</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">Corporate Street Address</label>
                                <input type="text" name="street_address_entity" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->street_address_entity ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city_entity" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->city_entity ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">State / Region</label>
                                <input type="text" name="state_entity" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->state_entity ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_entity" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->postal_entity ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <input type="text" name="country_entity" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->country_entity ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Individual specifications -->
                        <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                            Personal Profile details
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_name ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->middle_name ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->last_name ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Occupation / Title</label>
                                <input type="text" name="title_occupation" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->title_occupation ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="text" name="dob" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->dob ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->phone_number ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Country of Residence</label>
                                <input type="text" name="country" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->country ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                        </div>
                        <h3 style="font-size: 15px; color: #fff; margin: 24px 0 16px 0; font-weight: 700; text-transform: uppercase;">Residential Address details</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">Street Address</label>
                                <input type="text" name="street_address" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->street_address ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Unit / Apartment</label>
                                <input type="text" name="unit_apartment" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->unit_apartment ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->city ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">State / Province</label>
                                <input type="text" name="state_province" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->state_province ?? '') ?>" <?= $disabledAttr ?>>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal / ZIP Code</label>
                                <input type="text" name="postal_zip" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->postal_zip ?? '') ?>" <?= $disabledAttr ?>>
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
                            <input type="text" name="trading_purpose" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->trading_purpose ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Intended Onboarding Date</label>
                            <input type="text" name="first_trade_date" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_date ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Flow of Funds</label>
                            <input type="text" name="flow_of_funds" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->flow_of_funds ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">First Trade Currency</label>
                            <input type="text" name="first_trade_currency" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_currency ?? 'USDT') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">First Trade Estimate Size</label>
                            <input type="text" name="first_trade_size" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->first_trade_size ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Monthly Target volume</label>
                            <input type="text" name="monthly_volume_size" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->monthly_volume_size ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Annual Income Capacity</label>
                            <input type="text" name="annual_income_amount" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->annual_income_amount ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Liquid Assets size</label>
                            <input type="text" name="liquid_assets_amount" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->liquid_assets_amount ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Source of Funding specs</label>
                            <textarea name="source_funding" rows="3" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->source_funding ?? '') ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Trading Strategy Description</label>
                            <textarea name="trading_purpose_desc" rows="3" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->trading_purpose_desc ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Banking specs -->
                <div id="banking-spec" class="tab-panel">
                    <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                        Beneficiary Banking details
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_name ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Holder</label>
                            <input type="text" name="bank_account_holder" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_account_holder ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Number / IBAN</label>
                            <input type="text" name="bank_account_number" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_account_number ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Routing / Sort Code</label>
                            <input type="text" name="bank_routing_code" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_routing_code ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SWIFT / BIC Code</label>
                            <input type="text" name="bank_swift" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_swift ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Currency</label>
                            <input type="text" name="bank_currency" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_currency ?? 'USD') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bank Country</label>
                            <input type="text" name="bank_country" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_country ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Intermediary Bank (if any)</label>
                            <input type="text" name="bank_intermediary" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_intermediary ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Beneficiary Physical Address</label>
                            <input type="text" name="bank_beneficiary_address" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_beneficiary_address ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Bank Branch Address</label>
                            <input type="text" name="bank_address" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->bank_address ?? '') ?>" <?= $disabledAttr ?>>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Risk Declarations -->
                <div id="risk-spec" class="tab-panel">
                    <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                        Risk & Regulatory Declarations
                    </h3>
                    
                    <?php if ($application && $application->account_type === 'corporate'): ?>
                        <!-- Corporate Risk declarations -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Has the entity ever declared bankruptcy?</span>
                                    <input type="text" name="declared_bankruptcy_entity" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->declared_bankruptcy_entity ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                                <label class="form-label">Bankruptcy Description</label>
                                <textarea name="declared_bankruptcy_entity_desc" rows="2" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->declared_bankruptcy_entity_desc ?? '') ?></textarea>
                            </div>

                            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Are any Directors classified as PEPs?</span>
                                    <input type="text" name="pep_status_entity" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->pep_status_entity ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                                <label class="form-label">PEP Status Details</label>
                                <textarea name="pep_status_entity_desc" rows="2" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->pep_status_entity_desc ?? '') ?></textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">US Financial Entity Status</label>
                                    <input type="text" name="financial_entity_us" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->financial_entity_us ?? '') ?>" <?= $disabledAttr ?>>
                                </div>
                                <label class="form-group">
                                    <span class="form-label">Registered Swap Dealer</span>
                                    <input type="text" name="swap_dealer" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->swap_dealer ?? '') ?>" <?= $disabledAttr ?>>
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Individual Risk declarations -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Have you ever declared bankruptcy?</span>
                                    <input type="text" name="declared_bankruptcy" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->declared_bankruptcy ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                                <label class="form-label">Bankruptcy Description</label>
                                <textarea name="declared_bankruptcy_desc" rows="2" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->declared_bankruptcy_desc ?? '') ?></textarea>
                            </div>

                            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: 16px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: #fff;">Are you a Politically Exposed Person (PEP)?</span>
                                    <input type="text" name="pep_status" class="<?= $inputClass ?>" style="width: 100px; text-align: center;" value="<?= htmlspecialchars($application->pep_status ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                                <label class="form-label">PEP Status Details</label>
                                <textarea name="pep_status_desc" rows="2" class="<?= $inputClass ?>" style="resize: none;" <?= $disabledAttr ?>><?= htmlspecialchars($application->pep_status_desc ?? '') ?></textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Accredited Investor Status</label>
                                    <input type="text" name="accredited_investor" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->accredited_investor ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Significant Volume Transactions</label>
                                    <input type="text" name="considerable_transactions" class="<?= $inputClass ?>" value="<?= htmlspecialchars($application->considerable_transactions ?? 'No') ?>" <?= $disabledAttr ?>>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab 5: Settlement Node specs -->
                <div id="node-spec" class="tab-panel">
                    <h3 style="font-size: 16px; color: #fff; margin-bottom: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px dashed rgba(255,255,255,0.08); padding-bottom: 10px;">
                        Settlement Wallet Specifications
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <p style="font-size: 13.5px; color: var(--text-muted); margin: 0; line-height: 1.6;">
                            This is your whitelisted cryptographic payout address. All high-limit liquidity operations executed via the Atlas Settlement Network are settled directly to this node.
                        </p>
                        
                        <div class="form-group">
                            <label class="form-label">Whitelisted USDT Settlement Address</label>
                            <input type="text" name="wallet_address" class="<?= $inputClass ?>" style="font-family: var(--font-mono); font-size: 15px; color: var(--accent-neon); background: rgba(0,0,0,0.6);" value="<?= htmlspecialchars($application->wallet_address ?? '') ?>" <?= $disabledAttr ?>>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Blockchain Network Standard</label>
                            <input type="text" name="network_type" class="<?= $inputClass ?>" style="font-weight: bold; width: 240px;" value="<?= htmlspecialchars($application->network_type ?? 'ERC-20') ?>" <?= $disabledAttr ?>>
                        </div>
                    </div>
                </div>

                <!-- Tab 6: Uploaded files -->
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
                        
                        <?php if ($status === 'allowed'): ?>
                            <!-- Dynamic Upload Interface -->
                            <div style="display: flex; flex-direction: column; gap: 24px;">
                                
                                <?php if ($application && $application->account_type === 'corporate'): ?>
                                    <!-- Corporate Uploads -->
                                    <?php 
                                    $corpDocs = [
                                        'entity_articles_file' => ['label' => 'Articles of Incorporation', 'key' => 'entity_articles_file'],
                                        'entity_shareholders_file' => ['label' => 'Shareholder Registry & Structure', 'key' => 'entity_shareholders_file'],
                                        'entity_bank_statement_file' => ['label' => 'Corporate Bank Statement snapshot', 'key' => 'entity_bank_statement_file'],
                                        'entity_proof_address_file' => ['label' => 'Operating Business Address Verification', 'key' => 'entity_proof_address_file'],
                                        'entity_board_resolution_file' => ['label' => 'Authorized Corporate Board Resolution', 'key' => 'entity_board_resolution_file']
                                    ];
                                    foreach ($corpDocs as $field => $info):
                                        $val = $application->$field ?? '';
                                    ?>
                                        <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.04); padding: 20px; border-radius: 12px;">
                                            <label class="form-label" style="font-size: 13.5px; font-weight: 700; color: #fff; display: block; margin-bottom: 12px;"><?= $info['label'] ?></label>
                                            
                                            <input type="hidden" name="<?= $field ?>" id="<?= $field ?>_input" value="<?= htmlspecialchars($val) ?>">
                                            
                                            <div id="<?= $field ?>-preview-container" style="display: flex; flex-direction: column; gap: 10px;"></div>
                                            
                                            <input type="file" id="<?= $field ?>_picker" style="display: none;" onchange="uploadProfileFile(this, '<?= $field ?>', '<?= $info['key'] ?>')">
                                            <button type="button" class="btn btn-ghost" onclick="document.getElementById('<?= $field ?>_picker').click()" style="padding: 8px 16px; font-size: 12px; border-radius: 20px; border-color: rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 8px; margin-top: 10px;">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Upload New File
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                <?php else: ?>
                                    <!-- Individual Uploads -->
                                    <!-- KYC Document -->
                                    <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.04); padding: 20px; border-radius: 12px;">
                                        <label class="form-label" style="font-size: 13.5px; font-weight: 700; color: #fff; display: block; margin-bottom: 12px;">Government Identification KYC Document</label>
                                        <input type="hidden" name="kyc_document_file" id="kyc_document_file_input" value="<?= htmlspecialchars($application->kyc_document_file ?? '[]') ?>">
                                        <div id="kyc_document_file-preview-container" style="display: flex; flex-direction: column; gap: 10px;"></div>
                                        <input type="file" id="kyc_document_file_picker" style="display: none;" onchange="uploadProfileFile(this, 'kyc_document_file', 'kyc_document')" multiple>
                                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('kyc_document_file_picker').click()" style="padding: 8px 16px; font-size: 12px; border-radius: 20px; border-color: rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 8px; margin-top: 10px;">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Scans
                                        </button>
                                    </div>
                                    
                                    <!-- Proof of Funds -->
                                    <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.04); padding: 20px; border-radius: 12px;">
                                        <label class="form-label" style="font-size: 13.5px; font-weight: 700; color: #fff; display: block; margin-bottom: 12px;">Cryptographic Proof of Capital Reserves</label>
                                        <input type="hidden" name="proof_funds_file" id="proof_funds_file_input" value="<?= htmlspecialchars($application->proof_funds_file ?? '[]') ?>">
                                        <div id="proof_funds_file-preview-container" style="display: flex; flex-direction: column; gap: 10px;"></div>
                                        <input type="file" id="proof_funds_file_picker" style="display: none;" onchange="uploadProfileFile(this, 'proof_funds_file', 'proof_funds')" multiple>
                                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('proof_funds_file_picker').click()" style="padding: 8px 16px; font-size: 12px; border-radius: 20px; border-color: rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 8px; margin-top: 10px;">
                                            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Documents
                                        </button>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        <?php else: ?>
                            <!-- Read-only View -->
                            <!-- Individual documents -->
                            <?php if ($application && $application->kyc_document_file): ?>
                                <?php foreach ($parseFiles($application->kyc_document_file) as $file): ?>
                                    <div class="document-item" style="margin-bottom: 8px;">
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
                                    <div class="document-item" style="margin-bottom: 8px;">
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
                                    'entity_bank_statement_file' => 'Corporate Bank Statement snapshot',
                                    'entity_proof_address_file' => 'Operating Business Address Verification',
                                    'entity_board_resolution_file' => 'Authorized Corporate Board Resolution'
                                ];
                                foreach ($corpDocs as $colName => $docLabel):
                                    if (!empty($application->$colName)):
                                ?>
                                    <div class="document-item" style="margin-bottom: 8px;">
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

                            <?php if (empty($application->kyc_document_file) && empty($application->proof_funds_file) && empty($application->entity_articles_file)): ?>
                                <div style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                                    <i class="fa-solid fa-box-open" style="font-size: 32px; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                                    No verified document uploads are attached to this specification profile.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Submit actions row if in allowed status -->
                <?php if ($status === 'allowed'): ?>
                    <div style="margin-top: 30px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: flex-end; gap: 16px;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 28px; border: none; border-radius: 30px; font-size: 13px; font-weight: bold; color: #000;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Submit Profile Updates
                        </button>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </form>
</div>

<style>
    /* Premium Profile View styles */
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
        if (event) event.preventDefault();
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active-panel'));
        
        // Add active to current button and show targeted panel
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        } else {
            // Find tab button by tabId string
            const tabBtn = document.querySelector(`button[onclick*="'${tabId}'"]`);
            if (tabBtn) {
                tabBtn.classList.add('active');
            }
        }
        
        const activePanel = document.getElementById(tabId);
        if (activePanel) {
            activePanel.classList.add('active-panel');
        }
    }

    // Handle initial tab selection via URL query parameter
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            switchProfileTab(null, tab);
        }
    });

    // Helper to decode files list
    function getProfileFileList(val) {
        if (!val) return [];
        try {
            const decoded = JSON.parse(val);
            if (Array.isArray(decoded)) return decoded;
        } catch (e) {}
        return [val];
    }

    // Render current uploaded files preview
    function renderProfileFiles(fieldId, isMultiple) {
        const input = document.getElementById(fieldId + '_input');
        if (!input) return;
        const val = input.value;
        const files = isMultiple ? getProfileFileList(val) : (val ? [val] : []);
        const container = document.getElementById(fieldId + '-preview-container');
        if (!container) return;
        
        container.innerHTML = '';
        if (files.length === 0) {
            container.innerHTML = '<span style="color: var(--text-muted); font-size: 12.5px; font-style: italic;">No file selected</span>';
            return;
        }

        files.forEach((filename, index) => {
            const isPdf = filename.toLowerCase().endsWith('.pdf');
            const iconClass = isPdf ? 'fa-solid fa-file-pdf' : 'fa-solid fa-file-image';
            const iconColor = isPdf ? '#e74c3c' : 'var(--accent-neon)';
            
            const html = `
                <div class="document-item" style="margin-bottom: 8px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="${iconClass}" style="font-size: 20px; color: ${iconColor};"></i>
                        <div>
                            <a href="<?= url('/uploads/') ?>${encodeURIComponent(filename)}" target="_blank" style="font-size: 12.5px; color: #8bf0ff; font-family: var(--font-mono); text-decoration: underline; font-weight: bold;">${filename}</a>
                        </div>
                    </div>
                    <button type="button" onclick="removeProfileFile('${fieldId}', ${index}, ${isMultiple})" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; transition: color 0.2s;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='#8fa0a3'">
                        <i class="fa-solid fa-trash-can"></i> Delete
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    // Upload profile file using AJAX
    async function uploadProfileFile(input, fieldId, fileKey) {
        if (input.files.length === 0) return;
        
        const isMultiple = (fieldId === 'kyc_document_file' || fieldId === 'proof_funds_file');
        const container = document.getElementById(fieldId + '-preview-container');
        container.innerHTML = '<span style="color: var(--accent-neon); font-size: 12.5px; font-style: italic;"><i class="fa-solid fa-spinner fa-spin"></i> Uploading document...</span>';

        try {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const formData = new FormData();
                formData.append('file', file);
                formData.append('file_key', fileKey);
                formData.append('_token', '<?= csrf_token() ?>');

                const response = await fetch("<?= url('/api/profile/upload-file') ?>", {
                    method: "POST",
                    body: formData
                });
                
                if (!response.ok) {
                    const errRes = await response.json();
                    throw new Error(errRes.error || 'Upload failed');
                }
                
                const result = await response.json();
                if (result.status === 'success' && result.filename) {
                    const inputEl = document.getElementById(fieldId + '_input');
                    if (isMultiple) {
                        const currentFiles = getProfileFileList(inputEl.value);
                        currentFiles.push(result.filename);
                        inputEl.value = JSON.stringify(currentFiles);
                    } else {
                        inputEl.value = result.filename;
                    }
                }
            }
        } catch (e) {
            alert("Upload failed: " + e.message);
        } finally {
            input.value = ''; // Clear selection
            renderProfileFiles(fieldId, isMultiple);
        }
    }

    // Remove profile file selection
    function removeProfileFile(fieldId, index, isMultiple) {
        if (!confirm("Are you sure you want to remove this document?")) return;
        const inputEl = document.getElementById(fieldId + '_input');
        if (isMultiple) {
            const currentFiles = getProfileFileList(inputEl.value);
            currentFiles.splice(index, 1);
            inputEl.value = currentFiles.length > 0 ? JSON.stringify(currentFiles) : '[]';
        } else {
            inputEl.value = '';
        }
        renderProfileFiles(fieldId, isMultiple);
    }

    // Initialize UI on load
    document.addEventListener("DOMContentLoaded", function() {
        <?php if ($status === 'allowed'): ?>
            <?php if ($application && $application->account_type === 'corporate'): ?>
                renderProfileFiles('entity_articles_file', false);
                renderProfileFiles('entity_shareholders_file', false);
                renderProfileFiles('entity_bank_statement_file', false);
                renderProfileFiles('entity_proof_address_file', false);
                renderProfileFiles('entity_board_resolution_file', false);
            <?php else: ?>
                renderProfileFiles('kyc_document_file', true);
                renderProfileFiles('proof_funds_file', true);
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
