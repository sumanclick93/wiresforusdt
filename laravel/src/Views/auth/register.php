<?php
// Retrieve active application data from controller variable $application
$app = $application;

// Map backend DB values to frontend values for step/theme sequence compatibility
if ($app) {
    if (($app->account_type ?? '') === 'corporate') {
        $app->account_type = 'Entity';
    } elseif (($app->account_type ?? '') === 'individual') {
        $app->account_type = 'Individual';
    }
}

// Fix: If account_type is empty but there's existing progress, determine the account type to prevent displaying the intro screen
if (empty($app->account_type)) {
    if (!empty($app->company_name) || !empty($app->entity_type) || !empty($app->incorporation_country)) {
        $app->account_type = 'Entity';
    } elseif (!empty($app->first_name) || !empty($app->last_name) || (isset($app->id) && !empty($app->current_step))) {
        $app->account_type = 'Individual';
    }
}

// Helper to safely retrieve dynamic options from database, with hardcoded list as robust fallback
$getDropdownOptions = function(string $key) use ($dropdowns) {
    if (isset($dropdowns[$key]) && !empty($dropdowns[$key])) {
        return $dropdowns[$key];
    }
    
    // Robust fallbacks
    $fallbacks = [
        'countries' => ['United States', 'Canada', 'United Kingdom', 'Germany', 'Czech Republic', 'Singapore', 'Hong Kong', 'Austria', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Denmark', 'Estonia', 'Finland', 'France', 'Greece', 'Hungary', 'Iceland', 'Ireland', 'Italy', 'Latvia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Norway', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'Mexico', 'El Salvador', 'Panama', 'Bahamas', 'Cayman Islands', 'Bermuda', 'BVI', 'Costa Rica', 'Guatemala', 'Belize', 'Brazil', 'Argentina', 'Colombia', 'Chile', 'Peru', 'Uruguay', 'Paraguay', 'Japan', 'South Korea', 'Philippines', 'Thailand', 'Indonesia', 'Malaysia', 'Taiwan', 'Kazakhstan', 'Bahrain', 'Saudi Arabia', 'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Democratic Republic of the Congo', 'Republic of the Congo', 'Djibouti', 'Egypt', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Gabon', 'Gambia', 'Ghana', 'Guinea', 'Guinea-Bissau', 'Ivory Coast', 'Kenya', 'Lesotho', 'Liberia', 'Libya', 'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Mauritius', 'Morocco', 'Mozambique', 'Namibia', 'Niger', 'Nigeria', 'Rwanda', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Somalia', 'South Africa', 'South Sudan', 'Sudan', 'Tanzania', 'Togo', 'Tunisia', 'Uganda', 'Zambia', 'Zimbabwe'],
        'entity_types' => ['Corporation', 'Limited Liability Company', 'Partnership', 'Sole Proprietorship', 'Trust', 'Other'],
        'funding_sources' => ['Retained Earnings', 'Equity Capital', 'Debt financing', 'Investor capital', 'Other'],
        'business_natures' => ['Asset Management', 'Proprietary Trading', 'Venture Capital', 'Crypto Exchange / Brokerage', 'Family Office', 'Other'],
        'occupations' => ['Chief Executive Officer', 'Managing Director', 'Portfolio Manager', 'Compliance Officer', 'Trader', 'Other'],
        'trading_purposes' => ['Speculation', 'Hedging', 'Liquidity Provision', 'Long-term investment', 'Other'],
        'funds_flows' => ['Incoming Bank Wire', 'Outgoing Bank Wire', 'USDT/Digital Settlement', 'Bilateral OTC Clearing', 'Other'],
        'currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'SGD', 'HKD'],
        'network_types' => ['ERC-20', 'TRC-20', 'Solana', 'Arbitrum', 'Optimism'],
        'referral_sources' => ['Google Search', 'Telegram Channels', 'Institutional Broker', 'Word of Mouth', 'Other']
    ];
    
    return $fallbacks[$key] ?? [];
};
?>

<!-- Premium Validation Toast -->
<div id="validation-toast" style="display: none; position: fixed; top: 30px; right: 30px; background: rgba(231, 76, 60, 0.95); color: #fff; padding: 16px 28px; border-radius: 12px; font-family: var(--font-sans); font-size: 14.5px; font-weight: 600; box-shadow: 0 10px 30px rgba(231, 76, 60, 0.35); z-index: 9999; align-items: center; gap: 12px; animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
    <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
    <span>Please complete all required fields with asterisks to advance.</span>
</div>

<div class="stepper-registration-container" style="max-width: 1200px; width: 100%; margin: 40px auto; padding: 0 20px; font-family: var(--font-sans); transition: all 0.3s;">

    <!-- UNIFIED TOP HEADER BANNER (Day/Night toggler & Logout upper right side like SDM website) -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 24px; margin-bottom: 36px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); width: 100%;">
        <!-- Left Side: Logo -->
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="<?= url('/images/logo.png') ?>" alt="Secure Logo" style="height: 36px; display: block;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 22px; font-weight: 800; color: #fff; letter-spacing: 1px; line-height: 1;" class="theme-logo-text">SECURE</span>
                <span style="font-size: 10px; color: var(--text-muted); font-weight: 700; letter-spacing: 1.5px; margin-top: 4px;">DIGITAL MARKETS</span>
            </div>
        </div>
        
        <!-- Right Side: Log Out & Day/Night switcher (matches top right aligned specs) -->
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="<?= url('/logout') ?>" style="display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.25); background: transparent; color: #fff; font-size: 13.5px; font-weight: 600; padding: 8px 22px; border-radius: 20px; text-decoration: none; transition: all 0.3s;" class="theme-logout-btn" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">Log out</a>
            
            <!-- Toggle Switch (Day/Night Theme Simulator) -->
            <div onclick="toggleDayNightTheme()" id="theme-toggle-switch" style="width: 48px; height: 26px; background: rgba(255,255,255,0.15); border-radius: 13px; position: relative; cursor: pointer; display: flex; align-items: center; padding: 0 4px; transition: background 0.3s;" class="theme-toggle-switch">
                <div id="theme-toggle-knob" style="width: 18px; height: 18px; background: #0076ff; border-radius: 50%; position: absolute; right: 4px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #fff; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger" style="background: rgba(231, 76, 60, 0.08); border: 1px solid rgba(231, 76, 60, 0.25); color: #e74c3c; padding: 16px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; gap: 12px; align-items: center;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
            <div><?= htmlspecialchars(\App\Core\Session::getFlash('error')) ?></div>
        </div>
    <?php endif; ?>

    <!-- Welcome / Account Type Selector (Screenshot 1) -->
    <div id="intro-screen" style="display: <?= empty($app->account_type) ? 'grid' : 'none' ?>; grid-template-columns: 1fr 1.2fr; gap: 60px; align-items: flex-start; margin-top: 40px;">
        <!-- Left Sidebar: High-level Steps -->
        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 20px; padding: 40px 30px; backdrop-filter: blur(10px);" class="theme-panel">
            <div style="margin-bottom: 40px;">
                <h2 style="font-size: 28px; font-weight: 800; color: #fff; margin-bottom: 8px;" class="theme-pane-header">Trade with us</h2>
                <p style="color: var(--text-muted); font-size: 15px; margin: 0;">Onboarding is simple</p>
            </div>

            <!-- high-level progress list -->
            <div class="high-level-steps" style="display: flex; flex-direction: column; gap: 32px; position: relative; padding-left: 20px; border-left: 2px solid rgba(255, 255, 255, 0.05);">
                <!-- Step 1 -->
                <div style="position: relative; display: flex; gap: 20px; align-items: flex-start;">
                    <div style="position: absolute; left: -31px; top: 0; width: 22px; height: 22px; border-radius: 50%; background: #060b0d; border: 2px solid var(--accent-neon); display: flex; align-items: center; justify-content: center; font-size: 11px; font-family: var(--font-mono); color: var(--accent-neon); font-weight: 700; box-shadow: 0 0 10px rgba(185, 255, 58, 0.2);">01</div>
                    <div>
                        <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 4px;" class="theme-pane-header">Complete your Account Application</strong>
                        <span style="font-size: 13px; color: var(--accent-neon);">In Progress</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div style="position: relative; display: flex; gap: 20px; align-items: flex-start; opacity: 0.5;">
                    <div style="position: absolute; left: -31px; top: 0; width: 22px; height: 22px; border-radius: 50%; background: #060b0d; border: 2px solid var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 11px; font-family: var(--font-mono); color: var(--text-muted); font-weight: 700;">02</div>
                    <div>
                        <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 4px;" class="theme-pane-header">Await Compliance Approval</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Locked</span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div style="position: relative; display: flex; gap: 20px; align-items: flex-start; opacity: 0.5;">
                    <div style="position: absolute; left: -31px; top: 0; width: 22px; height: 22px; border-radius: 50%; background: #060b0d; border: 2px solid var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 11px; font-family: var(--font-mono); color: var(--text-muted); font-weight: 700;">03</div>
                    <div>
                        <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 4px;" class="theme-pane-header">Complete your SDM Agreement</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Locked</span>
                    </div>
                </div>

                <!-- Step 4 -->
                <div style="position: relative; display: flex; gap: 20px; align-items: flex-start; opacity: 0.5;">
                    <div style="position: absolute; left: -31px; top: 0; width: 22px; height: 22px; border-radius: 50%; background: #060b0d; border: 2px solid var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 11px; font-family: var(--font-mono); color: var(--text-muted); font-weight: 700;">04</div>
                    <div>
                        <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 4px;" class="theme-pane-header">Fund your Account & Begin Trading</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Locked</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Name, Email and Selection -->
        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 20px; padding: 60px; backdrop-filter: blur(10px); min-height: 480px; display: flex; flex-direction: column; justify-content: center;" class="theme-panel">
            <div style="margin-bottom: 36px;">
                <h3 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 12px;" class="theme-pane-header">Create Onboarding Profile</h3>
                <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.6; margin: 0;">Specify your full identity and choose whether you will operate under an Individual or Corporate Entity trading profile.</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Full Name input -->
                <div>
                    <label style="color: #fff; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px;" class="theme-pane-header">Full Name *</label>
                    <input type="text" id="intro-name" value="<?= htmlspecialchars(trim(($app->first_name ?? '') . ' ' . ($app->middle_name ?? '') . ' ' . ($app->last_name ?? ''))) ?>" placeholder="Enter your full name..." style="width: 100%; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.08); padding: 16px 20px; border-radius: 12px; color: #fff; font-size: 15px; font-family: var(--font-sans); outline: none; transition: border-color 0.3s;" class="stepper-input" onfocus="this.style.borderColor='var(--accent-neon)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                </div>

                <!-- Pre-filled Email (readonly) -->
                <div>
                    <label style="color: #fff; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px; opacity: 0.6;" class="theme-pane-header">Email Address *</label>
                    <input type="email" value="<?= htmlspecialchars($email) ?>" readonly style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.04); padding: 16px 20px; border-radius: 12px; color: var(--text-muted); font-size: 15px; cursor: not-allowed;" tabindex="-1">
                </div>

                <!-- Account Type Selector Buttons (Screenshot 1) -->
                <div>
                    <label style="color: #fff; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 12px;" class="theme-pane-header">Profile Selection *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <?php
                        $isEntity = ($app->account_type === 'Entity');
                        $isIndividual = !$isEntity;
                        ?>
                        <!-- Individual Button -->
                        <div id="btn-select-individual" onclick="setIntroAccountType('Individual')" style="background: <?= $isIndividual ? 'rgba(185, 255, 58, 0.04)' : 'rgba(0,0,0,0.5)' ?>; border: 2px solid <?= $isIndividual ? 'var(--accent-neon)' : 'rgba(255,255,255,0.06)' ?>; padding: 18px 24px; border-radius: 12px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.3s;" class="theme-intro-select <?= $isIndividual ? 'active' : '' ?>">
                            <div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid <?= $isIndividual ? 'var(--accent-neon)' : 'rgba(255,255,255,0.2)' ?>; display: flex; align-items: center; justify-content: center;">
                                <div id="radio-individual-dot" style="width: 10px; height: 10px; border-radius: 50%; background: <?= $isIndividual ? 'var(--accent-neon)' : 'transparent' ?>;"></div>
                            </div>
                            <strong style="color: <?= $isIndividual ? '#fff' : 'rgba(255,255,255,0.4)' ?>; font-size: 15px;" class="theme-pane-header">Individual</strong>
                        </div>

                        <!-- Entity Button -->
                        <div id="btn-select-entity" onclick="setIntroAccountType('Entity')" style="background: <?= $isEntity ? 'rgba(185, 255, 58, 0.04)' : 'rgba(0,0,0,0.5)' ?>; border: 2px solid <?= $isEntity ? 'var(--accent-neon)' : 'rgba(255,255,255,0.06)' ?>; padding: 18px 24px; border-radius: 12px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.3s;" class="theme-intro-select <?= $isEntity ? 'active' : '' ?>">
                            <div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid <?= $isEntity ? 'var(--accent-neon)' : 'rgba(255,255,255,0.2)' ?>; display: flex; align-items: center; justify-content: center;">
                                <div id="radio-entity-dot" style="width: 10px; height: 10px; border-radius: 50%; background: <?= $isEntity ? 'var(--accent-neon)' : 'transparent' ?>;"></div>
                            </div>
                            <strong style="color: <?= $isEntity ? '#fff' : 'rgba(255,255,255,0.4)' ?>; font-size: 15px;" class="theme-pane-header">Entity</strong>
                        </div>
                    </div>
                </div>

                <!-- Submit CTA Button -->
                <button type="button" onclick="initializeAccountProgress()" style="background: var(--accent-neon); color: #060b0d; font-family: var(--font-sans); font-size: 15px; font-weight: 700; border: none; padding: 18px; border-radius: 12px; cursor: pointer; transition: all 0.3s; margin-top: 10px; box-shadow: 0 10px 25px rgba(185,255,58,0.15);" onmouseover="this.style.background='var(--accent-neon-hover)'" onmouseout="this.style.background='var(--accent-neon)'">
                    Trade With Us
                </button>
            </div>
        </div>
    </div>


    <!-- Interactive Multi-Step Stepper (Screenshot 2) -->
    <div id="stepper-screen" style="display: <?= !empty($app->account_type) ? 'grid' : 'none' ?>; grid-template-columns: 280px 1fr; gap: 48px; align-items: flex-start; margin-top: 30px;">
        
        <!-- Left Sidebar: Stepper Navigation (Screenshot 2 layout) -->
        <div style="background: #000; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 30px 20px; backdrop-filter: blur(10px); position: sticky; top: 120px;" class="theme-sidebar">
            <!-- Dynamic list of steps (rendered via Javascript) -->
            <div id="sidebar-tabs-container" style="display: flex; flex-direction: column; gap: 8px;">
            </div>
        </div>

        <!-- Right Content Area (Autosaving Form Stack) -->
        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 20px; padding: 40px; backdrop-filter: blur(10px);" class="theme-panel">
            <!-- Stepper Content Top Bar (Header with Save & Verify and Next) -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 24px; margin-bottom: 36px;">
                <h3 id="step-title-header" style="font-size: 26px; font-weight: 800; color: #fff; margin: 0;" class="theme-pane-header">Application Information</h3>
                
                <div style="display: flex; align-items: center; gap: 14px;">
                    <!-- Save and Verify Button -->
                    <button type="button" class="btn-step-save-action" onclick="saveActiveStepData(true)" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                        Save and Verify
                    </button>
                    <!-- Previous Button -->
                    <button type="button" class="btn-step-prev-action" onclick="goToPreviousStep()" style="background: #f5b016; color: #000; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s;">
                        Previous
                    </button>
                    <!-- Next Button -->
                    <button type="button" class="btn-step-next-action" onclick="advanceStep()" style="background: #f5b016; color: #000; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s;">
                        Next
                    </button>
                    <!-- Submit button for final referral screen (matches disabled screenshot style initially) -->
                    <button type="button" class="btn-step-submit-action" onclick="advanceStep()" style="background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 30px; cursor: not-allowed; transition: all 0.3s; display: none;" disabled>
                        Submit
                    </button>
                </div>
            </div>

            <!-- Single multi-step form (submits to final registration on final step) -->
            <form id="multi-step-autosave-form" action="<?= url('/register') ?>" method="POST" onsubmit="return handleFinalRegistrationSubmit(event)" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <!-- ENTITY STEP 1: Company Verification Pane -->
                <div id="pane-company_verification" class="step-pane entity-only-field">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label class="stepper-label">Applicant Legal Entity Type <span style="color: #e74c3c;">*</span></label>
                            <select name="entity_type" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->entity_type) ? 'selected' : '' ?>>Select entity type...</option>
                                <?php foreach ($getDropdownOptions('entity_types') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->entity_type === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Legal Entity Identifier/Natural Person Identifier/Privacy Law Identifier</label>
                            <div class="textarea-container">
                                <textarea name="lei_identifier" class="stepper-input" style="height: 100px; resize: none;" placeholder="Enter legal identifier..." maxlength="2000" oninput="updateCharCounter(this, 'lei-identifier-counter')"><?= htmlspecialchars($app->lei_identifier ?? '') ?></textarea>
                                <div id="lei-identifier-counter" class="char-counter">0/2000</div>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Country of Incorporation <span style="color: #e74c3c;">*</span></label>
                            <select name="incorporation_country" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->incorporation_country) ? 'selected' : '' ?>>Select country...</option>
                                <?php foreach ($getDropdownOptions('countries') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->incorporation_country === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Date of Incorporation <span style="color: #e74c3c;">*</span></label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="date" name="incorporation_date" value="<?= htmlspecialchars($app->incorporation_date ?? '') ?>" class="stepper-input" required>
                                <i class="fa-solid fa-calendar-days" style="position: absolute; right: 20px; color: var(--text-muted); font-size: 16px; pointer-events: none;"></i>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Is the company regulated by any government agency?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="company_regulated" value="Yes" <?= $app->company_regulated === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="company_regulated" value="No" <?= $app->company_regulated === 'No' || empty($app->company_regulated) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Has the applicant or any of its directors ever declared bankruptcy? If so, please explain further.</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="declared_bankruptcy_entity" value="Yes" <?= $app->declared_bankruptcy_entity === 'Yes' ? 'checked' : '' ?> onchange="toggleBankruptcyEntityTextarea(true)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="declared_bankruptcy_entity" value="No" <?= $app->declared_bankruptcy_entity === 'No' || empty($app->declared_bankruptcy_entity) ? 'checked' : '' ?> onchange="toggleBankruptcyEntityTextarea(false)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <div id="bankruptcy-entity-desc-wrapper" style="display: <?= $app->declared_bankruptcy_entity === 'Yes' ? 'block' : 'none' ?>; margin-top: 14px;">
                                <textarea name="declared_bankruptcy_entity_desc" class="stepper-input" style="height: 80px; resize: none;" placeholder="Provide bankruptcy details..."><?= htmlspecialchars($app->declared_bankruptcy_entity_desc ?? '') ?></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Is the applicant or any of its shareholders or directors a politically exposed person ("PEP")? If so, please explain further.</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="pep_status_entity" value="Yes" <?= $app->pep_status_entity === 'Yes' ? 'checked' : '' ?> onchange="togglePepEntityTextarea(true)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="pep_status_entity" value="No" <?= $app->pep_status_entity === 'No' || empty($app->pep_status_entity) ? 'checked' : '' ?> onchange="togglePepEntityTextarea(false)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <div id="pep-entity-desc-wrapper" style="display: <?= $app->pep_status_entity === 'Yes' ? 'block' : 'none' ?>; margin-top: 14px;">
                                <textarea name="pep_status_entity_desc" class="stepper-input" style="height: 80px; resize: none;" placeholder="Provide political details..."><?= htmlspecialchars($app->pep_status_entity_desc ?? '') ?></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Is the company registered in the United States as a financial entity?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="financial_entity_us" value="Yes" <?= $app->financial_entity_us === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="financial_entity_us" value="No" <?= $app->financial_entity_us === 'No' || empty($app->financial_entity_us) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Is the company a swap dealer?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="swap_dealer" value="Yes" <?= $app->swap_dealer === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="swap_dealer" value="No" <?= $app->swap_dealer === 'No' || empty($app->swap_dealer) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENTITY STEP 2: Additional Information Pane -->
                <div id="pane-additional_information" class="step-pane entity-only-field">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">Company Name <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="company_name" value="<?= htmlspecialchars($app->company_name ?? '') ?>" class="stepper-input" placeholder="Enter company name" required>
                            </div>
                            <div>
                                <label class="stepper-label">Company Registered Number <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="company_reg_number" value="<?= htmlspecialchars($app->company_reg_number ?? '') ?>" class="stepper-input" placeholder="Enter registration number" required>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Contact Number <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="contact_number" value="<?= htmlspecialchars($app->contact_number ?? '') ?>" class="stepper-input" placeholder="Enter contact number" required>
                        </div>

                        <div>
                            <label class="stepper-label">Source of Funding <span style="color: #e74c3c;">*</span></label>
                            <select name="source_funding_entity" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->source_funding_entity) ? 'selected' : '' ?>>Select source of funding...</option>
                                <?php foreach ($getDropdownOptions('funding_sources') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->source_funding_entity === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Nature of Business <span style="color: #e74c3c;">*</span></label>
                            <select name="nature_of_business" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->nature_of_business) ? 'selected' : '' ?>>Select nature of business...</option>
                                <?php foreach ($getDropdownOptions('business_natures') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->nature_of_business === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 1px; margin-top: 16px; margin-bottom: -4px; text-transform: uppercase;" class="theme-pane-header">
                            COMPANY REGISTERED ADDRESS
                        </div>

                        <div>
                            <label class="stepper-label">Street Address <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="street_address_entity" value="<?= htmlspecialchars($app->street_address_entity ?? '') ?>" class="stepper-input" placeholder="Enter a location" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">Country <span style="color: #e74c3c;">*</span></label>
                                <select name="country_entity" class="stepper-input" required>
                                    <option value="" disabled <?= empty($app->country_entity) ? 'selected' : '' ?>>Select country...</option>
                                    <?php foreach ($getDropdownOptions('countries') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= $app->country_entity === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="stepper-label">City <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="city_entity" value="<?= htmlspecialchars($app->city_entity ?? '') ?>" class="stepper-input" placeholder="Enter city" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">State/Province <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="state_entity" value="<?= htmlspecialchars($app->state_entity ?? '') ?>" class="stepper-input" placeholder="Enter state/province" required>
                            </div>
                            <div>
                                <label class="stepper-label">Postal Code <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="postal_entity" value="<?= htmlspecialchars($app->postal_entity ?? '') ?>" class="stepper-input" placeholder="Enter postal code" required>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Is the company's operating address different from the registered address provided above?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="operating_address_different" value="Yes" <?= $app->operating_address_different === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="operating_address_different" value="No" <?= $app->operating_address_different === 'No' || empty($app->operating_address_different) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Does your company have a website?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="has_website" value="Yes" <?= $app->has_website === 'Yes' || empty($app->has_website) ? 'checked' : '' ?> onchange="toggleWebsiteInput(true)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="has_website" value="No" <?= $app->has_website === 'No' ? 'checked' : '' ?> onchange="toggleWebsiteInput(false)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div id="website-input-wrapper" style="display: <?= $app->has_website === 'No' ? 'none' : 'block' ?>;">
                            <label class="stepper-label">Please provide your website here <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="website" id="website_input" value="<?= htmlspecialchars($app->website ?? '') ?>" class="stepper-input" placeholder="https://example.com" <?= $app->has_website === 'No' ? '' : 'required' ?>>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div>
                                <label class="stepper-label">LinkedIn</label>
                                <input type="text" name="linkedin_entity" value="<?= htmlspecialchars($app->linkedin_entity ?? '') ?>" class="stepper-input" placeholder="LinkedIn URL">
                            </div>
                            <div>
                                <label class="stepper-label">Instagram</label>
                                <input type="text" name="instagram_entity" value="<?= htmlspecialchars($app->instagram_entity ?? '') ?>" class="stepper-input" placeholder="Instagram handle">
                            </div>
                            <div>
                                <label class="stepper-label">Twitter</label>
                                <input type="text" name="twitter_entity" value="<?= htmlspecialchars($app->twitter_entity ?? '') ?>" class="stepper-input" placeholder="X / Twitter handle">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENTITY STEP 7: Upload Documents Pane (Screenshot 2 style) -->
                <div id="pane-upload_documents" class="step-pane entity-only-field">
                    <div style="display: flex; flex-direction: column; gap: 28px;">
                        
                        <!-- Doc 1: Original Certificate/Articles of Incorporation -->
                        <div>
                            <label class="stepper-label">Original Certificate/Articles of Incorporation <span style="color: #e74c3c;">*</span></label>
                            <div class="upload-area" id="entity_articles_file-upload-area" onclick="triggerFileInput('entity_articles_file_upload')">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Upload an Attachment</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">Original Certificate/Articles of Incorporation</span>
                            </div>
                            <input type="file" name="entity_articles_file_upload" id="entity_articles_file_upload" style="display: none;" onchange="handleEntityFileSelect(this, 'entity_articles_file')">
                            <input type="hidden" name="entity_articles_file" id="entity_articles_file_input" value="<?= htmlspecialchars($app->entity_articles_file ?? '') ?>">
                            
                            <div id="entity_articles_file-file-badge" class="upload-preview" style="display: <?= !empty($app->entity_articles_file) ? 'flex' : 'none' ?>;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #2ecc71;"></i>
                                    <div style="text-align: left;">
                                        <strong style="color: #fff; font-size: 13.5px; display: block;" id="entity_articles_file-filename-label"><?= htmlspecialchars($app->entity_articles_file ?? '') ?></strong>
                                        <span style="color: #2ecc71; font-size: 11px;">Uploaded successfully</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeEntityFile(event, 'entity_articles_file')" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Doc 2: Signed & Dated Shareholders Register -->
                        <div>
                            <label class="stepper-label">Signed & Dated Shareholders Register - Document outlining the shareholders and their ownership percentage <span style="color: #e74c3c;">*</span></label>
                            <div class="upload-area" id="entity_shareholders_file-upload-area" onclick="triggerFileInput('entity_shareholders_file_upload')">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Upload an Attachment</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">Signed & Dated Shareholders Register - Document outlining the shareholders and their ownership percentage</span>
                            </div>
                            <input type="file" name="entity_shareholders_file_upload" id="entity_shareholders_file_upload" style="display: none;" onchange="handleEntityFileSelect(this, 'entity_shareholders_file')">
                            <input type="hidden" name="entity_shareholders_file" id="entity_shareholders_file_input" value="<?= htmlspecialchars($app->entity_shareholders_file ?? '') ?>">
                            
                            <div id="entity_shareholders_file-file-badge" class="upload-preview" style="display: <?= !empty($app->entity_shareholders_file) ? 'flex' : 'none' ?>;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #2ecc71;"></i>
                                    <div style="text-align: left;">
                                        <strong style="color: #fff; font-size: 13.5px; display: block;" id="entity_shareholders_file-filename-label"><?= htmlspecialchars($app->entity_shareholders_file ?? '') ?></strong>
                                        <span style="color: #2ecc71; font-size: 11px;">Uploaded successfully</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeEntityFile(event, 'entity_shareholders_file')" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Doc 3: Company Bank Statement -->
                        <div>
                            <label class="stepper-label">Company Bank Statement (issued within the past 90 days) <span style="color: #e74c3c;">*</span></label>
                            <div class="upload-area" id="entity_bank_statement_file-upload-area" onclick="triggerFileInput('entity_bank_statement_file_upload')">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Upload an Attachment</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">Company Bank Statement (issued within the past 90 days)</span>
                            </div>
                            <input type="file" name="entity_bank_statement_file_upload" id="entity_bank_statement_file_upload" style="display: none;" onchange="handleEntityFileSelect(this, 'entity_bank_statement_file')">
                            <input type="hidden" name="entity_bank_statement_file" id="entity_bank_statement_file_input" value="<?= htmlspecialchars($app->entity_bank_statement_file ?? '') ?>">
                            
                            <div id="entity_bank_statement_file-file-badge" class="upload-preview" style="display: <?= !empty($app->entity_bank_statement_file) ? 'flex' : 'none' ?>;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #2ecc71;"></i>
                                    <div style="text-align: left;">
                                        <strong style="color: #fff; font-size: 13.5px; display: block;" id="entity_bank_statement_file-filename-label"><?= htmlspecialchars($app->entity_bank_statement_file ?? '') ?></strong>
                                        <span style="color: #2ecc71; font-size: 11px;">Uploaded successfully</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeEntityFile(event, 'entity_bank_statement_file')" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Doc 4: Proof of Address -->
                        <div>
                            <label class="stepper-label">Proof of Address (Utility Bill/Bank Statement issued within the past 90 days) <span style="color: #e74c3c;">*</span></label>
                            <div class="upload-area" id="entity_proof_address_file-upload-area" onclick="triggerFileInput('entity_proof_address_file_upload')">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Upload an Attachment</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">Proof of Address (Utility Bill/Bank Statement issued within the past 90 days)</span>
                            </div>
                            <input type="file" name="entity_proof_address_file_upload" id="entity_proof_address_file_upload" style="display: none;" onchange="handleEntityFileSelect(this, 'entity_proof_address_file')">
                            <input type="hidden" name="entity_proof_address_file" id="entity_proof_address_file_input" value="<?= htmlspecialchars($app->entity_proof_address_file ?? '') ?>">
                            
                            <div id="entity_proof_address_file-file-badge" class="upload-preview" style="display: <?= !empty($app->entity_proof_address_file) ? 'flex' : 'none' ?>;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #2ecc71;"></i>
                                    <div style="text-align: left;">
                                        <strong style="color: #fff; font-size: 13.5px; display: block;" id="entity_proof_address_file-filename-label"><?= htmlspecialchars($app->entity_proof_address_file ?? '') ?></strong>
                                        <span style="color: #2ecc71; font-size: 11px;">Uploaded successfully</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeEntityFile(event, 'entity_proof_address_file')" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Doc 5: Board Resolution -->
                        <div>
                            <label class="stepper-label">Board Resolution Authorizing an Account Opening with SDM <span style="color: #e74c3c;">*</span></label>
                            <div class="upload-area" id="entity_board_resolution_file-upload-area" onclick="triggerFileInput('entity_board_resolution_file_upload')">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Upload an Attachment</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">Board Resolution Authorizing an Account Opening with SDM</span>
                            </div>
                            <input type="file" name="entity_board_resolution_file_upload" id="entity_board_resolution_file_upload" style="display: none;" onchange="handleEntityFileSelect(this, 'entity_board_resolution_file')">
                            <input type="hidden" name="entity_board_resolution_file" id="entity_board_resolution_file_input" value="<?= htmlspecialchars($app->entity_board_resolution_file ?? '') ?>">
                            
                            <div id="entity_board_resolution_file-file-badge" class="upload-preview" style="display: <?= !empty($app->entity_board_resolution_file) ? 'flex' : 'none' ?>;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #2ecc71;"></i>
                                    <div style="text-align: left;">
                                        <strong style="color: #fff; font-size: 13.5px; display: block;" id="entity_board_resolution_file-filename-label"><?= htmlspecialchars($app->entity_board_resolution_file ?? '') ?></strong>
                                        <span style="color: #2ecc71; font-size: 11px;">Uploaded successfully</span>
                                    </div>
                                </div>
                                <button type="button" onclick="removeEntityFile(event, 'entity_board_resolution_file')" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ENTITY STEP 8: UBO Verification Pane (Screenshot 3 style) -->
                <div id="pane-ubo_verification" class="step-pane entity-only-field">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        
                        <div style="margin-bottom: 12px;">
                            <h4 style="font-size: 16px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Ultimate Beneficial Owners</h4>
                            <p style="color: var(--text-muted); font-size: 13.5px; margin: 0; line-height: 1.6;">Please fill out the following information for all shareholders owning 10% or more of the entity</p>
                        </div>

                        <!-- UBO rows container -->
                        <div id="ubo-rows-container" style="display: flex; flex-direction: column; gap: 36px;">
                            <!-- Dynamically generated in JS -->
                        </div>

                        <input type="hidden" name="entity_ubos_json" id="entity_ubos_json" value="<?= htmlspecialchars($app->entity_ubos_json ?? '[]') ?>">

                        <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                            <button type="button" onclick="addUboRow()" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-plus"></i> Add Another Ultimate Beneficial Owner
                            </button>
                        </div>

                    </div>
                </div>

                <!-- ENTITY STEP 9: List of Directors and Authorized Users Pane (Screenshot 4 style) -->
                <div id="pane-directors_authorized" class="step-pane entity-only-field">
                    <div style="display: flex; flex-direction: column; gap: 48px;">
                        
                        <!-- SECTION A: DIRECTORS -->
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <div>
                                <h4 style="font-size: 16px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">List of Directors</h4>
                            </div>

                            <div id="director-rows-container" style="display: flex; flex-direction: column; gap: 36px;">
                                <!-- Dynamically generated in JS -->
                            </div>

                            <input type="hidden" name="entity_directors_json" id="entity_directors_json" value="<?= htmlspecialchars($app->entity_directors_json ?? '[]') ?>">

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="button" onclick="addDirectorRow()" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-plus"></i> Add Another Director
                                </button>
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 0;">

                        <!-- SECTION B: AUTHORIZED SIGNATORIES -->
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <div>
                                <h4 style="font-size: 16px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">List of all Authorized Users or Signatories</h4>
                            </div>

                            <div id="signatory-rows-container" style="display: flex; flex-direction: column; gap: 36px;">
                                <!-- Dynamically generated in JS -->
                            </div>

                            <input type="hidden" name="entity_authorized_signatories_json" id="entity_authorized_signatories_json" value="<?= htmlspecialchars($app->entity_authorized_signatories_json ?? '[]') ?>">

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="button" onclick="addSignatoryRow()" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-plus"></i> Add Another Authorized User
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- STEP 1: Application Information Pane -->
                <div id="pane-application_info" class="step-pane active individual-only-field">
                    <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                        <div>
                            <label class="stepper-label">Title/Occupation *</label>
                            <select name="title_occupation" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->title_occupation) ? 'selected' : '' ?>>Select occupation...</option>
                                <?php foreach ($getDropdownOptions('occupations') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->title_occupation === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div>
                                <label class="stepper-label">First Name *</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($app->first_name ?? '') ?>" class="stepper-input" placeholder="First Name" required>
                            </div>
                            <div>
                                <label class="stepper-label">Middle Name(s)</label>
                                <input type="text" name="middle_name" value="<?= htmlspecialchars($app->middle_name ?? '') ?>" class="stepper-input" placeholder="if applicable">
                            </div>
                            <div>
                                <label class="stepper-label">Last Name *</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($app->last_name ?? '') ?>" class="stepper-input" placeholder="Last Name" required>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Date of Birth *</label>
                            <input type="date" name="dob" value="<?= htmlspecialchars($app->dob ?? '') ?>" class="stepper-input" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div>
                                <label class="stepper-label">LinkedIn (Optional)</label>
                                <input type="text" name="linkedin" value="<?= htmlspecialchars($app->linkedin ?? '') ?>" class="stepper-input" placeholder="LinkedIn profile link">
                            </div>
                            <div>
                                <label class="stepper-label">Instagram (Optional)</label>
                                <input type="text" name="instagram" value="<?= htmlspecialchars($app->instagram ?? '') ?>" class="stepper-input" placeholder="Instagram handle">
                            </div>
                            <div>
                                <label class="stepper-label">Twitter/X (Optional)</label>
                                <input type="text" name="twitter" value="<?= htmlspecialchars($app->twitter ?? '') ?>" class="stepper-input" placeholder="X profile link">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Residential Address Pane (Screenshot 1 Layout) -->
                <div id="pane-residential_address" class="step-pane individual-only-field">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label class="stepper-label">Street Address <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="street_address" value="<?= htmlspecialchars($app->street_address ?? '') ?>" class="stepper-input" placeholder="Enter a location" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">Unit</label>
                                <input type="text" name="unit_apartment" value="<?= htmlspecialchars($app->unit_apartment ?? '') ?>" class="stepper-input" placeholder="Unit/Suite">
                            </div>
                            <div>
                                <label class="stepper-label">City <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="city" value="<?= htmlspecialchars($app->city ?? '') ?>" class="stepper-input" placeholder="Enter your city" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">Country <span style="color: #e74c3c;">*</span></label>
                                <select name="country" class="stepper-input" required>
                                    <option value="" disabled <?= empty($app->country) ? 'selected' : '' ?>>Select country...</option>
                                    <?php foreach ($getDropdownOptions('countries') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= $app->country === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="stepper-label">State <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="state_province" value="<?= htmlspecialchars($app->state_province ?? '') ?>" class="stepper-input" placeholder="Enter your state" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <label class="stepper-label">Postal Code <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="postal_zip" value="<?= htmlspecialchars($app->postal_zip ?? '') ?>" class="stepper-input" placeholder="Postal Code" required>
                            </div>
                            <div>
                                <label class="stepper-label">Phone Number <span style="color: #e74c3c;">*</span></label>
                                <input type="text" name="phone_number" value="<?= htmlspecialchars($app->phone_number ?? '') ?>" class="stepper-input" placeholder="Phone Number" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Trading Account Pane (Screenshot 2 Layout) -->
                <div id="pane-trading_account" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label class="stepper-label">Intended Purpose of Trading (select all that apply) <span style="color: #e74c3c;">*</span></label>
                            <select name="trading_purpose" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->trading_purpose) ? 'selected' : '' ?>>Select primary purpose...</option>
                                <?php foreach ($getDropdownOptions('trading_purposes') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->trading_purpose === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Please provide a brief description of the intended purpose of trading.</label>
                            <div class="textarea-container">
                                <textarea name="trading_purpose_desc" class="stepper-input" style="height: 100px; resize: none;" placeholder="Description..." maxlength="2000" oninput="updateCharCounter(this, 'trading-desc-counter')"><?= htmlspecialchars($app->trading_purpose_desc ?? '') ?></textarea>
                                <div id="trading-desc-counter" class="char-counter">0/2000</div>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Expected date for first trade <span style="color: #e74c3c;">*</span></label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="date" name="first_trade_date" value="<?= htmlspecialchars($app->first_trade_date ?? '') ?>" class="stepper-input" style="padding-right: 50px;" required>
                                <i class="fa-solid fa-calendar-days" style="position: absolute; right: 20px; color: var(--text-muted); font-size: 16px; pointer-events: none;"></i>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Please confirm the flow of funds for the intended first trade. <span style="color: #e74c3c;">*</span></label>
                            <select name="flow_of_funds" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->flow_of_funds) ? 'selected' : '' ?>>Select primary flow...</option>
                                <?php foreach ($getDropdownOptions('funds_flows') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->flow_of_funds === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Enter the expected currency and size of the first trade. <span style="color: #e74c3c;">*</span></label>
                            <div class="inline-input-group">
                                <select name="first_trade_currency" class="stepper-input inline-select" required>
                                    <?php foreach ($getDropdownOptions('currencies') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= ($app->first_trade_currency === $val || (empty($app->first_trade_currency) && $val === 'USDT')) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="first_trade_size" value="<?= htmlspecialchars($app->first_trade_size ?? '') ?>" class="stepper-input inline-amount" placeholder="Enter first trade size" required>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Enter the expected currency and monthly volume to be traded. <span style="color: #e74c3c;">*</span></label>
                            <div class="inline-input-group">
                                <select name="monthly_volume_currency" class="stepper-input inline-select" required>
                                    <?php foreach ($getDropdownOptions('currencies') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= ($app->monthly_volume_currency === $val || (empty($app->monthly_volume_currency) && $val === 'USDT')) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="monthly_volume_size" value="<?= htmlspecialchars($app->monthly_volume_size ?? '') ?>" class="stepper-input inline-amount" placeholder="Enter monthly trade volume" required>
                            </div>
                        </div>

                        <div class="individual-only-field">
                            <label class="stepper-label">Source of Funding <span style="color: #e74c3c;">*</span></label>
                            <div class="textarea-container">
                                <textarea name="source_funding" class="stepper-input" style="height: 100px; resize: none;" placeholder="Details regarding source of funds..." maxlength="2000" required oninput="updateCharCounter(this, 'source-funding-counter')"><?= htmlspecialchars($app->source_funding ?? '') ?></textarea>
                                <div id="source-funding-counter" class="char-counter">0/2000</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Financial Information Pane (Screenshot 3 Layout) -->
                <div id="pane-financial_info" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 28px;">
                        <div>
                            <label class="stepper-label">Enter the estimated annual income of the applicant. <span style="color: #e74c3c;">*</span></label>
                            <div class="inline-input-group">
                                <select name="annual_income_currency" class="stepper-input inline-select" required>
                                    <?php foreach ($getDropdownOptions('currencies') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= ($app->annual_income_currency === $val || (empty($app->annual_income_currency) && $val === 'USD')) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="annual_income_amount" value="<?= htmlspecialchars($app->annual_income_amount ?? '') ?>" class="stepper-input inline-amount" placeholder="Annual Income" required>
                            </div>
                        </div>

                        <div>
                            <label class="stepper-label">Enter the value of the liquid assets of the applicant. <span style="color: #e74c3c;">*</span></label>
                            <div class="inline-input-group">
                                <select name="liquid_assets_currency" class="stepper-input inline-select" required>
                                    <?php foreach ($getDropdownOptions('currencies') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= ($app->liquid_assets_currency === $val || (empty($app->liquid_assets_currency) && $val === 'USD')) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="liquid_assets_amount" value="<?= htmlspecialchars($app->liquid_assets_amount ?? '') ?>" class="stepper-input inline-amount" placeholder="Liquid Assets Value" required>
                            </div>
                        </div>

                        <div class="individual-only-field">
                            <label class="stepper-label">Has the applicant ever declared bankruptcy? If so, please explain further.</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="declared_bankruptcy" value="Yes" <?= $app->declared_bankruptcy === 'Yes' ? 'checked' : '' ?> onchange="toggleBankruptcyTextarea(true)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="declared_bankruptcy" value="No" <?= $app->declared_bankruptcy === 'No' || empty($app->declared_bankruptcy) ? 'checked' : '' ?> onchange="toggleBankruptcyTextarea(false)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <div id="bankruptcy-desc-wrapper" style="display: <?= $app->declared_bankruptcy === 'Yes' ? 'block' : 'none' ?>; margin-top: 14px;">
                                <textarea name="declared_bankruptcy_desc" class="stepper-input" style="height: 80px; resize: none;" placeholder="Provide bankruptcy details..."><?= htmlspecialchars($app->declared_bankruptcy_desc ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="individual-only-field">
                            <label class="stepper-label">Is the applicant a politically exposed person ("PEP")? If so, please explain further.</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="pep_status" value="Yes" <?= $app->pep_status === 'Yes' ? 'checked' : '' ?> onchange="togglePepTextarea(true)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="pep_status" value="No" <?= $app->pep_status === 'No' || empty($app->pep_status) ? 'checked' : '' ?> onchange="togglePepTextarea(false)">
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <div id="pep-desc-wrapper" style="display: <?= $app->pep_status === 'Yes' ? 'block' : 'none' ?>; margin-top: 14px;">
                                <textarea name="pep_status_desc" class="stepper-input" style="height: 80px; resize: none;" placeholder="Provide political details..."><?= htmlspecialchars($app->pep_status_desc ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="individual-only-field">
                            <label class="stepper-label">Over the previous four quarters, has the applicant maintained an average of at least 10 considerable-sized transactions per quarter in the relevant market?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="considerable_transactions" value="Yes" <?= $app->considerable_transactions === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="considerable_transactions" value="No" <?= $app->considerable_transactions === 'No' || empty($app->considerable_transactions) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div class="individual-only-field">
                            <label class="stepper-label">Does the applicant have an investment portfolio (Cash and Financial Instruments) in the excess of USD 500,000?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="portfolio_excess" value="Yes" <?= $app->portfolio_excess === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="portfolio_excess" value="No" <?= $app->portfolio_excess === 'No' || empty($app->portfolio_excess) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>

                        <div class="entity-only-field">
                            <label class="stepper-label">Is the company an accredited investor or equivalent in the country of incorporation?</label>
                            <div class="custom-radio-group">
                                <label class="custom-radio-option">
                                    <input type="radio" name="accredited_investor" value="Yes" <?= $app->accredited_investor === 'Yes' ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">Yes</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="accredited_investor" value="No" <?= $app->accredited_investor === 'No' || empty($app->accredited_investor) ? 'checked' : '' ?>>
                                    <div class="radio-circle">
                                        <div class="radio-dot"></div>
                                    </div>
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Banking Information Pane (Screenshot 4 Layout) -->
                <div id="pane-banking_info" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <h4 style="font-size: 16px; font-weight: 700; color: #fff; margin: 0; border-left: 3px solid #0076ff; padding-left: 10px;">Banking Information #1</h4>

                        <div>
                            <label class="stepper-label">Account Currency <span style="color: #e74c3c;">*</span></label>
                            <select name="bank_currency" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->bank_currency) ? 'selected' : '' ?>>Select Currency...</option>
                                <?php foreach ($getDropdownOptions('currencies') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->bank_currency === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="stepper-label">Name on Bank Account <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_account_holder" value="<?= htmlspecialchars($app->bank_account_holder ?? '') ?>" class="stepper-input" placeholder="Enter bank account name" required>
                        </div>

                        <div>
                            <label class="stepper-label">IBAN/Account Number <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_account_number" value="<?= htmlspecialchars($app->bank_account_number ?? '') ?>" class="stepper-input" placeholder="IBAN or Account Number" required>
                        </div>

                        <div>
                            <label class="stepper-label">Routing Code <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_routing_code" value="<?= htmlspecialchars($app->bank_routing_code ?? '') ?>" class="stepper-input" placeholder="Routing Transit Number" required>
                        </div>

                        <div>
                            <label class="stepper-label">Swift/ABA/Sort Code <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_swift" value="<?= htmlspecialchars($app->bank_swift ?? '') ?>" class="stepper-input" placeholder="SWIFT/BIC Code" required>
                        </div>

                        <div>
                            <label class="stepper-label">Beneficiary Address <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_beneficiary_address" value="<?= htmlspecialchars($app->bank_beneficiary_address ?? '') ?>" class="stepper-input" placeholder="Enter a location" required>
                        </div>

                        <div>
                            <label class="stepper-label">Bank Name <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_name" value="<?= htmlspecialchars($app->bank_name ?? '') ?>" class="stepper-input" placeholder="Enter bank institution name" required>
                        </div>

                        <div>
                            <label class="stepper-label">Bank Address <span style="color: #e74c3c;">*</span></label>
                            <input type="text" name="bank_address" value="<?= htmlspecialchars($app->bank_address ?? '') ?>" class="stepper-input" placeholder="Enter a location" required>
                        </div>

                        <div>
                            <label class="stepper-label">Country <span style="color: #e74c3c;">*</span></label>
                            <select name="bank_country" class="stepper-input" required>
                                <option value="" disabled <?= empty($app->bank_country) ? 'selected' : '' ?>>Select Bank Country...</option>
                                <?php foreach ($getDropdownOptions('countries') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->bank_country === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="display: flex; gap: 12px; align-items: flex-start; cursor: pointer;">
                                <input type="checkbox" name="bank_intermediary_enabled" id="bank_intermediary_enabled" value="1" <?= !empty($app->bank_intermediary) ? 'checked' : '' ?> onchange="toggleIntermediaryWrapper(this.checked)" style="width: 20px; height: 20px; accent-color: var(--accent-neon); margin-top: 2px;">
                                <span style="color: rgba(255,255,255,0.8); font-size: 13.5px; line-height: 1.5; font-weight: 500;">If the sending/receiving bank account is located outside of The United States of America, please input intermediary bank details below:</span>
                            </label>
                        </div>

                        <div id="intermediary-fields-wrapper" style="display: <?= !empty($app->bank_intermediary) ? 'block' : 'none' ?>; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 24px; margin-top: 10px;">
                            <label class="stepper-label">Intermediary Bank Details</label>
                            <input type="text" name="bank_intermediary" value="<?= htmlspecialchars($app->bank_intermediary ?? '') ?>" class="stepper-input" placeholder="e.g. Intermediary Bank Name, SWIFT, Account Number">
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 15px;">
                            <button type="button" onclick="handleAddBankNotice()" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 20px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-plus"></i> Add Bank Account
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 6: Proof of Funds Pane (Screenshot 5 Layout) -->
                <div id="pane-proof_funds" class="step-pane individual-only-field">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <input type="hidden" name="proof_funds_file" id="proof_funds_file_input" value="<?= htmlspecialchars($app->proof_funds_file ?? '') ?>">
                        
                        <!-- Premium Dashed Upload Box -->
                        <div class="upload-area" id="proof-upload-area" onclick="triggerFileInput('proof_funds_file_upload')">
                            <i class="fa-regular fa-folder-open" style="font-size: 44px; color: var(--text-muted); margin-bottom: 16px;"></i>
                            <strong style="color: #fff; font-size: 16px; display: block; margin-bottom: 8px;">Upload Attachment(s)</strong>
                            <span style="color: var(--text-muted); font-size: 13px;">Bank statement showing proof of funds. <span style="color: #e74c3c;">*</span></span>
                        </div>
                        
                        <input type="file" name="proof_funds_file_upload" id="proof_funds_file_upload" style="display: none;" onchange="handleProofOfFundsFileSelect(this)" multiple>
                        
                        <!-- File Upload Success State Badges Container -->
                        <div id="proof-files-container" style="display: flex; flex-direction: column; gap: 12px;"></div>
                    </div>
                </div>

                <!-- STEP 7: Wallet Address Pane (Screenshot 1 Layout) -->
                <div id="pane-wallet_address" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <p style="color: var(--text-primary); font-size: 15px; margin: 0; line-height: 1.6;" class="theme-pane-header">Please enter your wallet addresses below.</p>
                        <p style="color: var(--text-muted); font-size: 14.5px; margin: 0; line-height: 1.6;">We will need a wallet address to ultimately approve an application. If you need assistance in setting up a wallet we can provide our advice. <br>You can skip this section if it is not available at the moment.</p>
                        
                        <div id="wallet-rows-container" style="display: flex; flex-direction: column; gap: 16px; margin-top: 10px;">
                            <!-- Wallet Input Combined Row -->
                            <div class="inline-input-group wallet-row" style="display: flex; gap: 12px; width: 100%;">
                                <select name="network_type" class="stepper-input inline-select" style="width: 240px;">
                                    <option value="" disabled <?= empty($app->network_type) ? 'selected' : '' ?>>Select or enter custom asset</option>
                                    <?php foreach ($getDropdownOptions('network_types') as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= $app->network_type === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="wallet_address" value="<?= htmlspecialchars($app->wallet_address ?? '') ?>" class="stepper-input inline-amount" placeholder="Enter wallet address" style="flex-grow: 1;">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                            <button type="button" onclick="handleAddWalletNotice()" style="background: #0076ff; color: #fff; font-family: var(--font-sans); font-size: 13px; font-weight: 700; border: none; padding: 10px 24px; border-radius: 20px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-plus"></i> Add Wallet
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 8: Declaration Pane (Screenshot 2 Layout) -->
                <div id="pane-declaration" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 28px;">
                        <p style="color: var(--text-muted); font-size: 15px; margin: 0; line-height: 1.7;">In submitting and returning the above information/documents, I, the client, fully understand and confirm for and on behalf of myself that:</p>
                        
                        <div style="display: flex; flex-direction: column; gap: 20px; padding-left: 10px;">
                            <p style="color: var(--text-primary); font-size: 14.5px; margin: 0; line-height: 1.6; border-left: 2px solid rgba(255,255,255,0.1); padding-left: 16px;" class="theme-pane-header">We have full power and authority to open the SDM Inc. Individual Account and to enter into the associated terms and conditions for and on behalf of the applicant.</p>
                            
                            <p style="color: var(--text-primary); font-size: 14.5px; margin: 0; line-height: 1.6; border-left: 2px solid rgba(255,255,255,0.1); padding-left: 16px;" class="theme-pane-header">The information provided in this Individual Application is to my/our knowledge true and complete.</p>
                            
                            <p style="color: var(--text-primary); font-size: 14.5px; margin: 0; line-height: 1.6; border-left: 2px solid rgba(255,255,255,0.1); padding-left: 16px;" class="theme-pane-header">We will notify SDM Inc., or where applicable its affiliates, of any changes to the information provided within at least 7 business days.</p>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <label style="display: flex; gap: 14px; align-items: center; cursor: pointer;" onclick="toggleDeclarationCheckbox()">
                                <div id="declaration-check-box" style="width: 22px; height: 22px; border-radius: 6px; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; background: <?= $app->declaration_signed ? 'var(--accent-neon)' : 'transparent' ?>; color: #000; font-size: 12px; transition: all 0.2s;" class="theme-checkbox-outline">
                                    <i class="fa-solid fa-check" style="display: <?= $app->declaration_signed ? 'block' : 'none' ?>;"></i>
                                </div>
                                <input type="hidden" name="declaration_signed" id="declaration_signed_input" value="<?= $app->declaration_signed ? '1' : '0' ?>">
                                <span style="color: #fff; font-size: 15px; font-weight: 600; user-select: none;" class="theme-pane-header">I Agree</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- STEP 9: KYC Verification Pane (Screenshot 3 Sumsub mockup) -->
                <div id="pane-kyc" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- Sumsub Outer Card -->
                        <div style="background: #111618; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 48px 24px; position: relative; min-height: 380px; display: flex; flex-direction: column; align-items: center; justify-content: center;" class="theme-sumsub-panel">
                            
                            <!-- Language Dropdown Top Right -->
                            <div style="position: absolute; right: 24px; top: 24px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 6px 16px; display: flex; align-items: center; gap: 6px; font-size: 13px; color: #fff; cursor: pointer;" class="theme-sumsub-lang" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                <i class="fa-solid fa-globe" style="font-size: 12px; color: var(--text-muted);"></i>
                                <strong style="font-family: var(--font-sans);">En</strong>
                            </div>

                            <!-- Mock Screen 3: Sumsub File Drop -->
                            <div id="sumsub-upload-screen" style="display: flex; flex-direction: column; align-items: center; text-align: center; max-width: 520px; width: 100%;">
                                <h4 style="font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 8px;" class="theme-pane-header">Upload Government ID Scan</h4>
                                <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 24px;">Please select document type and upload a clear scan of your identity card.</p>
                                
                                <div style="width: 100%; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                                    <div id="doc-tab-passport" onclick="selectKycDocType('Passport')" style="background: rgba(185, 255, 58, 0.04); border: 1px solid var(--accent-neon); padding: 12px; border-radius: 10px; cursor: pointer; color: #fff; font-weight: 600;" class="kyc-doc-tab">Passport</div>
                                    <div id="doc-tab-license" onclick="selectKycDocType('Driver License')" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.04); padding: 12px; border-radius: 10px; cursor: pointer; color: rgba(255,255,255,0.5);" class="kyc-doc-tab">License</div>
                                    <div id="doc-tab-id" onclick="selectKycDocType('National Card')" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.04); padding: 12px; border-radius: 10px; cursor: pointer; color: rgba(255,255,255,0.5);" class="kyc-doc-tab">ID Card</div>
                                </div>

                                <input type="hidden" name="kyc_document_type" id="kyc_document_type" value="<?= htmlspecialchars($app->kyc_document_type ?? 'Passport') ?>">
                                <input type="hidden" name="kyc_document_file" id="kyc_document_file_input" value="<?= htmlspecialchars($app->kyc_document_file ?? '') ?>">

                                <!-- Drag-Drop ID Area -->
                                <div class="upload-area" id="kyc-upload-area" onclick="triggerFileInput('kyc_document_file_upload')" style="width: 100%; padding: 36px 20px;">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--text-muted); margin-bottom: 12px;"></i>
                                    <strong style="color: #fff; font-size: 15px; display: block; margin-bottom: 6px;">Select or Drag ID Scan File</strong>
                                    <span style="color: var(--text-muted); font-size: 12px;">Supports JPG, PNG or PDF format</span>
                                </div>
                                <input type="file" name="kyc_document_file_upload" id="kyc_document_file_upload" style="display: none;" onchange="handleKycFileSelect(this)" multiple>

                                <!-- KYC Scans Success Badges Container -->
                                <div id="kyc-files-container" style="display: flex; flex-direction: column; gap: 12px; width: 100%; margin-top: 16px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 10: Referral Pane (Screenshot 4 Layout) -->
                <div id="pane-referral" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label class="stepper-label">How did you hear about us? <span style="color: #e74c3c;">*</span></label>
                            <select name="referral_source" id="referral-select-input" class="stepper-input" required onchange="handleReferralSelectChange(this.value)">
                                <option value="" disabled <?= empty($app->referral_source) ? 'selected' : '' ?>>Select an option...</option>
                                <?php foreach ($getDropdownOptions('referral_sources') as $val): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= $app->referral_source === $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="referral-code-wrapper" style="display: <?= !empty($app->referral_source) ? 'block' : 'none' ?>; margin-top: 10px;">
                            <label class="stepper-label">Promo / Referral Code (Optional)</label>
                            <input type="text" name="referral_code" value="<?= htmlspecialchars($app->referral_code ?? '') ?>" class="stepper-input" placeholder="e.g. VIP-ONBOARD">
                        </div>
                    </div>
                </div>

                <!-- STEP 11: Final Password Setup & Submission -->
                <div id="pane-password_setup" class="step-pane">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div style="background: rgba(185, 255, 58, 0.03); border: 1px solid rgba(185, 255, 58, 0.1); border-radius: 12px; padding: 24px;" class="theme-panel">
                            <strong style="color: var(--accent-neon); display: block; margin-bottom: 8px; font-size: 15px;"><i class="fa-solid fa-lock"></i> Final Security Credential Configuration</strong>
                            <p style="color: var(--text-muted); font-size: 13.5px; margin: 0; line-height: 1.6;">You have completed all application steps. Set a highly secure password below to finalize your credential keys. Once submitted, your profile will enter pending admin review.</p>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label class="stepper-label">Create Login Password *</label>
                                <input type="password" name="password" id="final-password" class="stepper-input" placeholder="Min. 8 characters" required>
                            </div>
                            <div>
                                <label class="stepper-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" id="final-password_confirmation" class="stepper-input" placeholder="Re-enter password" required>
                            </div>
                        </div>

                        <button type="submit" style="background: var(--accent-neon); color: #060b0d; font-family: var(--font-sans); font-size: 15px; font-weight: 700; border: none; padding: 18px; border-radius: 12px; cursor: pointer; transition: all 0.3s; width: 100%; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 10px 25px rgba(185,255,58,0.15);" onmouseover="this.style.background='var(--accent-neon-hover)'" onmouseout="this.style.background='var(--accent-neon)'">
                            Submit Account Profile <i class="fa-solid fa-file-signature"></i>
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Bottom Navigation Bar (Mirroring Screenshot bottom Previous / Next triggers) -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 24px; margin-top: 36px;">
                <button type="button" class="btn-step-prev-action" onclick="goToPreviousStep()" style="background: #f5b016; color: #000; font-family: var(--font-sans); font-size: 14px; font-weight: 700; border: none; padding: 12px 30px; border-radius: 30px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    Previous
                </button>
                <button type="button" class="btn-step-next-action" onclick="advanceStep()" style="background: #f5b016; color: #000; font-family: var(--font-sans); font-size: 14px; font-weight: 700; border: none; padding: 12px 30px; border-radius: 30px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    Next
                </button>
                <!-- Bottom Submit button (matches Referral submit trigger) -->
                <button type="button" class="btn-step-submit-action" onclick="advanceStep()" style="background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); font-family: var(--font-sans); font-size: 14px; font-weight: 700; border: none; padding: 12px 30px; border-radius: 30px; cursor: not-allowed; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: none;" disabled>
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the new stepper tabs list (Screenshot 2 style) */
    .step-tab {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 35px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
    }
    
    /* Active step tab (yellow/gold background pill in Screenshot 2) */
    .step-tab.active {
        background: #f5b016 !important; /* Warm gold/yellow */
        color: #000 !important;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(245, 176, 22, 0.25);
    }
    .step-tab.active .tab-icon {
        color: #000 !important;
    }
    .step-tab.active .tab-label {
        color: #000 !important;
    }

    /* Pending / uncompleted steps with orange exclamation */
    .step-tab.pending {
        background: transparent;
        color: #ff9f43;
    }
    .step-tab.pending .tab-icon {
        color: #ff9f43; /* orange */
    }
    .step-tab.pending .tab-label {
        color: rgba(255, 255, 255, 0.75);
    }
    
    /* Locked / standard steps with grey details */
    .step-tab.locked {
        background: transparent;
        color: #555;
    }
    .step-tab.locked .tab-icon {
        color: #555;
    }
    .step-tab.locked .tab-label {
        color: #555;
    }

    .step-tab.completed {
        background: transparent;
        color: #2ecc71;
    }
    .step-tab.completed .tab-icon {
        color: #2ecc71;
    }
    .step-tab.completed .tab-label {
        color: rgba(255, 255, 255, 0.75);
    }

    .step-tab:hover:not(.active) {
        background: rgba(255, 255, 255, 0.03);
    }

    .tab-icon {
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px;
    }
    .tab-label {
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Stepper inputs formatting (Matches aesthetic of Wires4) */
    .stepper-label {
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
        margin-bottom: 10px;
    }
    .stepper-input {
        width: 100%;
        background: rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 16px 20px;
        border-radius: 12px;
        color: #fff;
        font-size: 15px;
        font-family: var(--font-sans);
        outline: none;
        transition: border-color 0.3s;
    }
    .stepper-input:focus {
        border-color: var(--accent-neon) !important;
    }
    select.stepper-input {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%25238fa0a3%22%20d%3D%22M10.23%203.43L6%207.66%201.77%203.43a.75.75%200%2000-1.06%201.06l4.76%204.77a.75.75%200%20001.06%200l4.76-4.77a.75.75%200%2000-1.06-1.06z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 18px center;
        background-size: 12px;
        padding-right: 40px;
    }
    select.stepper-input option {
        background: #060b0d;
        color: #fff;
    }

    /* Form panels switching animation */
    .step-pane {
        display: none;
    }
    .step-pane.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }

    /* Textarea character counters */
    .textarea-container {
        position: relative;
    }
    .char-counter {
        text-align: right;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    /* Inline currency + size fields */
    .inline-input-group {
        display: flex;
        gap: 12px;
        width: 100%;
    }
    .inline-select {
        width: 120px !important;
        flex-shrink: 0;
    }
    .inline-amount {
        flex-grow: 1;
    }

    /* Premium radio buttons */
    .custom-radio-group {
        display: flex;
        gap: 24px;
        margin-top: 10px;
    }
    .custom-radio-option {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        position: relative;
    }
    .custom-radio-option input[type="radio"] {
        display: none;
    }
    .radio-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #f5b016;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .radio-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: transparent;
        transition: background 0.2s;
    }
    .custom-radio-option input[type="radio"]:checked + .radio-circle .radio-dot {
        background: #f5b016;
    }
    .radio-label {
        color: #fff;
        font-size: 15px;
        font-weight: 600;
    }

    /* Premium attachment drag drop card */
    .upload-area {
        border: 2px dashed rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.01);
        padding: 48px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #f5b016;
        background: rgba(245, 176, 22, 0.03);
        box-shadow: 0 0 20px rgba(245, 176, 22, 0.1);
    }
    .upload-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 12px 20px;
        margin-top: 16px;
    }

    /* Sumsub mock-tabs */
    .kyc-doc-tab {
        text-align: center;
        font-size: 14px;
        transition: all 0.25s;
    }

    /* PREMIUM DAY / NIGHT THEME STYLING CONFIGURATIONS */
    body.light-theme {
        --bg-color: #f3f6f7;
        --bg-gradient: linear-gradient(180deg, #f3f6f7 0%, #e8ecf0 100%);
        --panel-bg: rgba(255, 255, 255, 0.95);
        --panel-border: rgba(0, 107, 255, 0.08);
        --text-primary: #0f1618;
        --text-muted: #5b696c;
        background: #f3f6f7 !important;
    }

    body.light-theme .theme-logo-text {
        color: #0f1618 !important;
    }

    body.light-theme .theme-pane-header {
        color: #0f1618 !important;
    }

    body.light-theme .theme-panel {
        background: #ffffff !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        box-shadow: 0 10px 30px rgba(15, 22, 24, 0.04) !important;
    }

    body.light-theme .theme-sidebar {
        background: #ffffff !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        box-shadow: 0 10px 30px rgba(15, 22, 24, 0.04) !important;
    }

    body.light-theme .theme-logout-btn {
        border-color: rgba(15, 22, 24, 0.2) !important;
        color: #0f1618 !important;
    }

    body.light-theme .stepper-label {
        color: #333 !important;
    }

    body.light-theme .stepper-input {
        background: rgba(255,255,255,0.8) !important;
        border: 1px solid rgba(15,22,24,0.12) !important;
        color: #0f1618 !important;
    }

    body.light-theme .step-tab.pending .tab-label {
        color: #5b696c !important;
    }

    body.light-theme .step-tab.locked {
        color: #a0acae !important;
    }

    body.light-theme .step-tab.locked .tab-icon {
        color: #a0acae !important;
    }

    body.light-theme .step-tab.locked .tab-label {
        color: #a0acae !important;
    }

    body.light-theme .theme-intro-select {
        background: rgba(255,255,255,0.8) !important;
        border-color: rgba(15,22,24,0.1) !important;
    }

    body.light-theme .theme-intro-select.active {
        border-color: var(--accent-neon) !important;
        background: rgba(0, 118, 255, 0.04) !important;
    }

    body.light-theme .theme-sumsub-panel {
        background: #fbfcfd !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }

    body.light-theme .theme-sumsub-lang {
        background: rgba(0,0,0,0.04) !important;
        border-color: rgba(0,0,0,0.08) !important;
        color: #333 !important;
    }

    body.light-theme .theme-checkbox-outline {
        border-color: #333 !important;
    }

    body.light-theme .theme-toggle-switch {
        background: rgba(0,0,0,0.08) !important;
    }

    /* Light Theme Sumsub Steps */
    body.light-theme .theme-sumsub-box {
        background: #f5f7f9 !important;
        border-color: rgba(0,0,0,0.06) !important;
    }
    body.light-theme .theme-sumsub-box > div {
        border-bottom-color: rgba(0,0,0,0.06) !important;
    }
    body.light-theme .theme-sumsub-box .step-number-circle {
        border-color: rgba(0,0,0,0.2) !important;
        background: rgba(0,0,0,0.03) !important;
        color: #333 !important;
    }
    body.light-theme .theme-sumsub-id-box {
        border-color: rgba(0,0,0,0.05) !important;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05) !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    body.entity-theme .individual-only-field {
        display: none !important;
    }
    body.individual-theme .entity-only-field {
        display: none !important;
    }
</style>

<script>
    // State management
    let activeAccountType = "<?= htmlspecialchars($app->account_type ?? '') ?>";
    if (!activeAccountType) {
        activeAccountType = "Individual";
    }
    let activeStepKey = "<?= htmlspecialchars($app->current_step ?? 'application_info') ?>";
    const email = "<?= htmlspecialchars($email) ?>";
    const token = "<?= htmlspecialchars($token) ?>";

    // Helper to dynamically update textarea character counters
    function updateCharCounter(el, counterId) {
        const counter = document.getElementById(counterId);
        if (counter) {
            const current = el.value.length;
            const max = el.getAttribute('maxlength') || 2000;
            counter.innerText = `${current}/${max}`;
        }
    }

    let stepsSequence = [];
    function updateStepsSequence() {
        if (activeAccountType === 'Entity') {
            stepsSequence = [
                'company_verification',
                'additional_information',
                'trading_account',
                'wallet_address',
                'financial_info',
                'banking_info',
                'upload_documents',
                'ubo_verification',
                'directors_authorized',
                'declaration',
                'referral',
                'password_setup'
            ];
        } else {
            stepsSequence = [
                'application_info',
                'residential_address',
                'trading_account',
                'financial_info',
                'banking_info',
                'proof_funds',
                'wallet_address',
                'declaration',
                'kyc',
                'referral',
                'password_setup'
            ];
        }
    }

    const stepTitles = {
        'application_info': 'Application Information',
        'residential_address': 'Residential Address',
        'trading_account': 'Trading Account',
        'financial_info': 'Financial Information',
        'banking_info': 'Banking Information',
        'proof_funds': 'Proof of Funds',
        'wallet_address': 'Wallet Address',
        'declaration': 'Declaration',
        'kyc': 'KYC',
        'referral': 'Referral details',
        'password_setup': 'Secure Password',
        
        // Entity steps:
        'company_verification': 'Company Verification',
        'additional_information': 'Additional Information',
        'upload_documents': 'Upload Documents',
        'ubo_verification': 'UBO Verification',
        'directors_authorized': 'Directors and Authorized Users'
    };

    // Day/Night Theme toggler (Screenshot 1 top right)
    function toggleDayNightTheme() {
        const body = document.body;
        const knob = document.getElementById('theme-toggle-knob');
        const sw = document.getElementById('theme-toggle-switch');
        
        body.classList.toggle('light-theme');
        
        if (body.classList.contains('light-theme')) {
            knob.style.right = 'auto';
            knob.style.left = '4px';
            knob.style.background = '#f5b016'; // gold color knob for light
            sw.style.background = 'rgba(0,0,0,0.08)';
        } else {
            knob.style.left = 'auto';
            knob.style.right = '4px';
            knob.style.background = '#0076ff'; // blue color knob for dark
            sw.style.background = 'rgba(255,255,255,0.15)';
        }
    }

    // Welcome Screen Selector
    function setIntroAccountType(type) {
        activeAccountType = type;
        if (type === 'Entity') {
            document.body.classList.add('entity-theme');
            document.body.classList.remove('individual-theme');
        } else {
            document.body.classList.add('individual-theme');
            document.body.classList.remove('entity-theme');
        }
        const btnInd = document.getElementById('btn-select-individual');
        const btnEnt = document.getElementById('btn-select-entity');
        const dotInd = document.getElementById('radio-individual-dot');
        const dotEnt = document.getElementById('radio-entity-dot');
        
        if (type === 'Individual') {
            btnInd.classList.add('active');
            btnInd.style.borderColor = 'var(--accent-neon)';
            btnInd.style.background = 'rgba(185, 255, 58, 0.04)';
            btnInd.querySelector('strong').style.color = 'var(--text-primary)';
            dotInd.style.background = 'var(--accent-neon)';

            btnEnt.classList.remove('active');
            btnEnt.style.borderColor = 'rgba(255,255,255,0.06)';
            btnEnt.style.background = 'rgba(0,0,0,0.5)';
            btnEnt.querySelector('strong').style.color = 'rgba(255,255,255,0.4)';
            dotEnt.style.background = 'transparent';
        } else {
            btnEnt.classList.add('active');
            btnEnt.style.borderColor = 'var(--accent-neon)';
            btnEnt.style.background = 'rgba(185, 255, 58, 0.04)';
            btnEnt.querySelector('strong').style.color = 'var(--text-primary)';
            dotEnt.style.background = 'var(--accent-neon)';

            btnInd.classList.remove('active');
            btnInd.style.borderColor = 'rgba(255,255,255,0.06)';
            btnInd.style.background = 'rgba(0,0,0,0.5)';
            btnInd.querySelector('strong').style.color = 'rgba(255,255,255,0.4)';
            dotInd.style.background = 'transparent';
        }
    }

    // Submit Welcome Screen to enter Stepper
    async function initializeAccountProgress() {
        const nameVal = document.getElementById('intro-name').value.trim();
        if (!nameVal) {
            alert("Please enter your full name to proceed.");
            return;
        }

        // Split name into first and last name for Step 1 prefill
        const nameParts = nameVal.split(' ');
        const firstName = nameParts[0] || "";
        const lastName = nameParts.slice(1).join(' ') || "";

        // Fill inputs in Step 1
        const inputFirst = document.querySelector('input[name="first_name"]');
        const inputLast = document.querySelector('input[name="last_name"]');
        if (inputFirst) inputFirst.value = firstName;
        if (inputLast) inputLast.value = lastName;

        // Perform AJAX Save via FormData
        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('account_type', activeAccountType);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        const initialStep = activeAccountType === 'Entity' ? 'company_verification' : 'application_info';
        formData.append('current_step', initialStep);
        formData.append('_token', '<?= csrf_token() ?>');

        try {
            const response = await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                // Transition UI
                document.getElementById('intro-screen').style.display = 'none';
                document.getElementById('stepper-screen').style.display = 'grid';
                renderSidebarTabs();
                switchStep(initialStep);
            } else {
                alert("Error: " + (result.error || "Unable to save profile configuration."));
            }
        } catch (e) {
            console.error("Autosave failed", e);
            alert("Unable to reach onboarding server. Please check your internet connection.");
        }
    }

    // Toggle declaration sign
    function toggleDeclarationCheckbox() {
        const checkbox = document.getElementById('declaration-check-box');
        const input = document.getElementById('declaration_signed_input');
        const icon = checkbox.querySelector('i');
        
        if (input.value === '1') {
            input.value = '0';
            checkbox.style.background = 'transparent';
            checkbox.style.borderColor = '#fff';
            icon.style.display = 'none';
        } else {
            input.value = '1';
            checkbox.style.background = 'var(--accent-neon)';
            checkbox.style.borderColor = 'var(--accent-neon)';
            icon.style.display = 'block';
        }
        
        // Auto-save the change immediately on click
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Toggle Bankruptcy fields wrapper
    function toggleBankruptcyTextarea(show) {
        const wrapper = document.getElementById('bankruptcy-desc-wrapper');
        wrapper.style.display = show ? 'block' : 'none';
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Toggle Entity Bankruptcy fields wrapper
    function toggleBankruptcyEntityTextarea(show) {
        const wrapper = document.getElementById('bankruptcy-entity-desc-wrapper');
        if (wrapper) {
            wrapper.style.display = show ? 'block' : 'none';
            const textarea = wrapper.querySelector('textarea');
            if (textarea) textarea.required = show;
        }
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Toggle Entity PEP fields wrapper
    function togglePepEntityTextarea(show) {
        const wrapper = document.getElementById('pep-entity-desc-wrapper');
        if (wrapper) {
            wrapper.style.display = show ? 'block' : 'none';
            const textarea = wrapper.querySelector('textarea');
            if (textarea) textarea.required = show;
        }
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Toggle Entity Website field wrapper
    function toggleWebsiteInput(show) {
        const wrapper = document.getElementById('website-input-wrapper');
        if (wrapper) {
            wrapper.style.display = show ? 'block' : 'none';
            const input = document.getElementById('website_input');
            if (input) input.required = show;
        }
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Render Sidebar Tabs dynamically based on stepsSequence
    function renderSidebarTabs() {
        updateStepsSequence();
        const container = document.getElementById('sidebar-tabs-container');
        if (!container) return;

        // Clear existing tabs
        container.innerHTML = '';

        // Sidebar steps: everything except 'password_setup'
        const sidebarSteps = stepsSequence.filter(step => step !== 'password_setup');

        sidebarSteps.forEach(step => {
            const tabId = `tab-${step}`;
            let label = stepTitles[step];

            const div = document.createElement('div');
            div.id = tabId;
            div.className = 'step-tab locked';
            div.setAttribute('data-step', step);
            div.onclick = () => switchStep(step);

            const spanIcon = document.createElement('span');
            spanIcon.className = 'tab-icon';
            spanIcon.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i>';

            const spanLabel = document.createElement('span');
            spanLabel.className = 'tab-label';
            spanLabel.innerText = label;

            div.appendChild(spanIcon);
            div.appendChild(spanLabel);
            container.appendChild(div);
        });

        // Set active tab
        const activeTab = document.getElementById(`tab-${activeStepKey}`);
        if (activeTab) {
            activeTab.className = "step-tab active";
        }
        updateAllTabIndicators();
    }

    // Toggle PEP fields wrapper
    function togglePepTextarea(show) {
        const wrapper = document.getElementById('pep-desc-wrapper');
        wrapper.style.display = show ? 'block' : 'none';
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Toggle Intermediary fields wrapper
    function toggleIntermediaryWrapper(show) {
        const wrapper = document.getElementById('intermediary-fields-wrapper');
        wrapper.style.display = show ? 'block' : 'none';
        if (!show) {
            const input = document.querySelector('input[name="bank_intermediary"]');
            if (input) input.value = '';
        }
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // Add secondary bank notice
    function handleAddBankNotice() {
        alert("Primary bank account details successfully locked. You will be able to attach secondary payout accounts directly from your secure client dashboard once review is complete.");
    }

    // Add secondary wallet notice (Screenshot 1: + Add Wallet)
    function handleAddWalletNotice() {
        alert("Primary payout settlement wallet successfully linked. Additional custom token settlement channels can be dynamically enabled from your account panel after compliance review is concluded.");
    }

    // Trigger Hidden File Inputs
    function triggerFileInput(id) {
        document.getElementById(id).click();
    }

    // Helper to safely retrieve file list from input values
    function getFileList(inputValue) {
        if (!inputValue) return [];
        try {
            const decoded = JSON.parse(inputValue);
            if (Array.isArray(decoded)) return decoded;
        } catch(e) {}
        return [inputValue];
    }

    // Helper to escape HTML characters
    function escapeHtml(string) {
        return String(string).replace(/[&<>"']/g, function (s) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': '&quot;',
                "'": '&#39;'
            }[s];
        });
    }

    // Render Proof of Funds Badges Container
    function renderProofFundsFiles() {
        const inputVal = document.getElementById('proof_funds_file_input').value;
        const files = getFileList(inputVal);
        const container = document.getElementById('proof-files-container');
        if (!container) return;
        container.innerHTML = '';
        
        files.forEach((filename, index) => {
            const itemHtml = `
                <div class="upload-preview" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-file-invoice-dollar" style="font-size: 24px; color: var(--accent-neon);"></i>
                        <div style="text-align: left;">
                            <a href="<?= url('/uploads/') ?>${encodeURIComponent(filename)}" target="_blank" style="color: #fff; font-size: 14px; display: block; font-weight: 700; text-decoration: underline;">${escapeHtml(filename)}</a>
                            <span style="color: #2ecc71; font-size: 12px; display: flex; align-items: center; gap: 4px;"><i class="fa-solid fa-circle-check"></i> Uploaded Successfully</span>
                        </div>
                    </div>
                    <button type="button" onclick="removeProofFundsFileAtIndex(${index})" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 16px; padding: 4px; transition: color 0.2s;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='#8fa0a3'">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    // Upload Proof of Funds File via AJAX
    async function handleProofOfFundsFileSelect(input) {
        if (input.files.length === 0) return;
        
        const area = document.getElementById('proof-upload-area');
        area.style.opacity = '0.5';
        area.querySelector('strong').innerText = "Uploading documents...";

        try {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const formData = new FormData();
                formData.append('email', email);
                formData.append('token', token);
                formData.append('_token', '<?= csrf_token() ?>');
                formData.append('proof_funds_file_upload', file);
                formData.append('current_step', 'proof_funds');

                const response = await fetch("<?= url('/api/register/save') ?>", {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success' && result.proof_funds_file) {
                    document.getElementById('proof_funds_file_input').value = result.proof_funds_file;
                } else {
                    alert("Upload failed for " + file.name + ": " + (result.error || "Incorrect file format."));
                }
            }
            
            renderProofFundsFiles();
            
            area.style.opacity = '1';
            area.querySelector('strong').innerText = "Upload Attachment(s)";
            updateAllTabIndicators();
        } catch (e) {
            console.error(e);
            alert("Network connection error during file upload.");
            area.style.opacity = '1';
            area.querySelector('strong').innerText = "Upload Attachment(s)";
        }
    }

    // Remove proof funds file at specific index
    async function removeProofFundsFileAtIndex(index) {
        if (!confirm("Are you sure you want to remove this proof of funds statement?")) return;

        const inputVal = document.getElementById('proof_funds_file_input').value;
        const files = getFileList(inputVal);
        files.splice(index, 1);

        const updatedVal = files.length > 0 ? JSON.stringify(files) : '';
        document.getElementById('proof_funds_file_input').value = updatedVal;
        renderProofFundsFiles();

        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('_token', '<?= csrf_token() ?>');
        formData.append('proof_funds_file', updatedVal);

        try {
            await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            updateAllTabIndicators();
        } catch (err) {
            console.error(err);
        }
    }

    function selectKycDocType(type) {
        document.getElementById('kyc_document_type').value = type;
        document.querySelectorAll('.kyc-doc-tab').forEach(tab => {
            tab.style.borderColor = 'rgba(255,255,255,0.04)';
            tab.style.background = 'rgba(0,0,0,0.3)';
            tab.style.color = 'rgba(255,255,255,0.5)';
            tab.style.fontWeight = '400';
        });
        
        let activeId = 'doc-tab-passport';
        if (type === 'Driver License') activeId = 'doc-tab-license';
        if (type === 'National Card') activeId = 'doc-tab-id';
        
        const activeTab = document.getElementById(activeId);
        if (activeTab) {
            activeTab.style.borderColor = 'var(--accent-neon)';
            activeTab.style.background = 'rgba(185, 255, 58, 0.04)';
            activeTab.style.color = '#fff';
            activeTab.style.fontWeight = '600';
        }
        
        saveActiveStepData(false);
    }

    // Render KYC Badges Container
    function renderKycFiles() {
        const inputVal = document.getElementById('kyc_document_file_input').value;
        const files = getFileList(inputVal);
        const container = document.getElementById('kyc-files-container');
        if (!container) return;
        container.innerHTML = '';
        
        files.forEach((filename, index) => {
            const itemHtml = `
                <div class="upload-preview" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-id-card" style="font-size: 20px; color: #e74c3c;"></i>
                        <div style="text-align: left;">
                            <a href="<?= url('/uploads/') ?>${encodeURIComponent(filename)}" target="_blank" style="color: #fff; font-size: 13.5px; display: block; font-weight: 700; text-decoration: underline;">${escapeHtml(filename)}</a>
                            <span style="color: #2ecc71; font-size: 11px;">Scanned successfully</span>
                        </div>
                    </div>
                    <button type="button" onclick="removeKycFileAtIndex(${index})" style="background: transparent; border: none; color: #8fa0a3; cursor: pointer; font-size: 14px; padding: 4px; transition: color 0.2s;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='#8fa0a3'">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    // Upload KYC File via AJAX
    async function handleKycFileSelect(input) {
        if (input.files.length === 0) return;
        
        const area = document.getElementById('kyc-upload-area');
        area.style.opacity = '0.5';
        area.querySelector('strong').innerText = "Uploading ID Scans...";

        try {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const formData = new FormData();
                formData.append('email', email);
                formData.append('token', token);
                formData.append('_token', '<?= csrf_token() ?>');
                formData.append('kyc_document_file_upload', file);
                formData.append('current_step', 'kyc');
                formData.append('kyc_document_type', document.getElementById('kyc_document_type').value);

                const response = await fetch("<?= url('/api/register/save') ?>", {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success' && result.kyc_document_file) {
                    document.getElementById('kyc_document_file_input').value = result.kyc_document_file;
                } else {
                    alert("Upload failed for " + file.name + ": " + (result.error || "File error."));
                }
            }
            
            renderKycFiles();
            
            area.style.opacity = '1';
            area.querySelector('strong').innerText = "Select or Drag ID Scan File";
            updateAllTabIndicators();
        } catch (e) {
            console.error(e);
            alert("Network connection error.");
            area.style.opacity = '1';
            area.querySelector('strong').innerText = "Select or Drag ID Scan File";
        }
    }

    // Remove KYC file at specific index
    async function removeKycFileAtIndex(index) {
        if (!confirm("Are you sure you want to remove this Government ID scan?")) return;

        const inputVal = document.getElementById('kyc_document_file_input').value;
        const files = getFileList(inputVal);
        files.splice(index, 1);

        const updatedVal = files.length > 0 ? JSON.stringify(files) : '';
        document.getElementById('kyc_document_file_input').value = updatedVal;
        renderKycFiles();

        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('_token', '<?= csrf_token() ?>');
        formData.append('kyc_document_file', updatedVal);

        try {
            await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            updateAllTabIndicators();
        } catch (err) {
            console.error(err);
        }
    }

    // Upload Entity Document File via AJAX
    async function handleEntityFileSelect(input, fieldName) {
        if (input.files.length === 0) return;
        
        const file = input.files[0];
        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('_token', '<?= csrf_token() ?>');
        formData.append(fieldName + '_upload', file);
        formData.append('current_step', 'upload_documents');

        // Show uploading preview indicators
        const area = document.getElementById(fieldName + '-upload-area');
        area.style.opacity = '0.5';
        area.querySelector('strong').innerText = "Uploading document...";

        try {
            const response = await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success' && result[fieldName]) {
                // Update State
                document.getElementById(fieldName + '_input').value = result[fieldName];
                document.getElementById(fieldName + '-filename-label').innerText = result[fieldName];
                document.getElementById(fieldName + '-file-badge').style.display = 'flex';
                
                // Reset upload card indicator
                area.style.opacity = '1';
                area.querySelector('strong').innerText = "Upload an Attachment";
                
                // Mark Step as active/completed in side tabs
                updateAllTabIndicators();
            } else {
                alert("Upload failed: " + (result.error || "Incorrect file format."));
                area.style.opacity = '1';
                area.querySelector('strong').innerText = "Upload an Attachment";
            }
        } catch (e) {
            console.error(e);
            alert("Network connection error during file upload.");
            area.style.opacity = '1';
        }
    }

    async function removeEntityFile(e, fieldName) {
        e.stopPropagation();
        if (!confirm("Are you sure you want to remove this document?")) return;

        document.getElementById(fieldName + '_input').value = '';
        document.getElementById(fieldName + '-file-badge').style.display = 'none';

        // Clear in DB
        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('_token', '<?= csrf_token() ?>');
        formData.append(fieldName, '');

        try {
            await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            updateAllTabIndicators();
        } catch (err) {
            console.error(err);
        }
    }

    // Dynamic Lists Helpers
    function getCountryOptionsHtml(selectedVal) {
        const countries = [
            'United States', 'Canada', 'United Kingdom', 'Germany', 'Czech Republic', 'Singapore', 'Hong Kong', 'Austria', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Denmark', 'Estonia', 'Finland', 'France', 'Greece', 'Hungary', 'Iceland', 'Ireland', 'Italy', 'Latvia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Norway', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'Mexico', 'El Salvador', 'Panama', 'Bahamas', 'Cayman Islands', 'Bermuda', 'BVI', 'Costa Rica', 'Guatemala', 'Belize', 'Brazil', 'Argentina', 'Colombia', 'Chile', 'Peru', 'Uruguay', 'Paraguay', 'Japan', 'South Korea', 'Philippines', 'Thailand', 'Indonesia', 'Malaysia', 'Taiwan', 'Kazakhstan', 'Bahrain', 'Saudi Arabia', 'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Democratic Republic of the Congo', 'Republic of the Congo', 'Djibouti', 'Egypt', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Gabon', 'Gambia', 'Ghana', 'Guinea', 'Guinea-Bissau', 'Ivory Coast', 'Kenya', 'Lesotho', 'Liberia', 'Libya', 'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Mauritius', 'Morocco', 'Mozambique', 'Namibia', 'Niger', 'Nigeria', 'Rwanda', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Somalia', 'South Africa', 'South Sudan', 'Sudan', 'Tanzania', 'Togo', 'Tunisia', 'Uganda', 'Zambia', 'Zimbabwe'
        ];
        let html = '<option value="" disabled' + (!selectedVal ? ' selected' : '') + '>Select country...</option>';
        countries.forEach(c => {
            html += `<option value="${c}"${c === selectedVal ? ' selected' : ''}>${c}</option>`;
        });
        return html;
    }

    // UBO ROWS
    function getUboRowHtml(index, data) {
        const uboNum = index + 1;
        const countryOptions = getCountryOptionsHtml(data.country);
        const uboTitleId = `ubo-title-${index}`;
        const uboFirstNameId = `ubo-first-name-${index}`;
        const uboMiddleNameId = `ubo-middle-name-${index}`;
        const uboLastNameId = `ubo-last-name-${index}`;
        const uboDobId = `ubo-dob-${index}`;
        const uboEmailId = `ubo-email-${index}`;
        const uboCountryId = `ubo-country-${index}`;
        const uboPercentageId = `ubo-percentage-${index}`;
        return `
            <div class="ubo-row" data-index="${index}" style="display: flex; flex-direction: column; gap: 20px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #fff; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;" class="theme-pane-header">UBO Verification #${uboNum}</strong>
                    ${index > 0 ? `<button type="button" onclick="removeUboRow(${index})" style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 14px;"><i class="fa-solid fa-trash-can"></i> Remove</button>` : ''}
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div>
                        <label class="stepper-label" for="${uboTitleId}">Title/Occupation</label>
                        <input type="text" id="${uboTitleId}" value="${data.title || ''}" class="stepper-input ubo-title" placeholder="e.g. CEO" oninput="serializeUbos()">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div>
                            <label class="stepper-label" for="${uboFirstNameId}">First Name *</label>
                            <input type="text" id="${uboFirstNameId}" value="${data.first_name || ''}" class="stepper-input ubo-first-name" placeholder="First Name" required oninput="serializeUbos()">
                        </div>
                        <div>
                            <label class="stepper-label" for="${uboMiddleNameId}">Middle Name(s) (if applicable)</label>
                            <input type="text" id="${uboMiddleNameId}" value="${data.middle_name || ''}" class="stepper-input ubo-middle-name" placeholder="Middle Name" oninput="serializeUbos()">
                        </div>
                        <div>
                            <label class="stepper-label" for="${uboLastNameId}">Last Name *</label>
                            <input type="text" id="${uboLastNameId}" value="${data.last_name || ''}" class="stepper-input ubo-last-name" placeholder="Last Name" required oninput="serializeUbos()">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 20px;">
                        <div>
                            <label class="stepper-label" for="${uboDobId}">Date of Birth *</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="date" id="${uboDobId}" value="${data.dob || ''}" class="stepper-input ubo-dob" required oninput="serializeUbos()">
                                <i class="fa-solid fa-calendar-days" style="position: absolute; right: 20px; color: var(--text-muted); font-size: 16px; pointer-events: none;"></i>
                            </div>
                        </div>
                        <div>
                            <label class="stepper-label" for="${uboEmailId}">Email *</label>
                            <input type="email" id="${uboEmailId}" value="${data.email || ''}" class="stepper-input ubo-email" placeholder="email@address.com" required oninput="serializeUbos()">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px;">
                        <div>
                            <label class="stepper-label" for="${uboCountryId}">Country/Region *</label>
                            <select id="${uboCountryId}" class="stepper-input ubo-country" required onchange="serializeUbos()">
                                ${countryOptions}
                            </select>
                        </div>
                        <div>
                            <label class="stepper-label" for="${uboPercentageId}">Ownership Percentage *</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="number" id="${uboPercentageId}" value="${data.percentage || ''}" class="stepper-input ubo-percentage" placeholder="Percentage" min="10" max="100" required oninput="serializeUbos()" style="padding-right: 40px;">
                                <span style="position: absolute; right: 20px; color: var(--text-muted); font-weight: 700; font-size: 14px;">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderUbos() {
        const container = document.getElementById('ubo-rows-container');
        if (!container) return;
        
        let ubos = [];
        try {
            const raw = document.getElementById('entity_ubos_json').value;
            ubos = JSON.parse(raw || '[]');
        } catch (e) {
            ubos = [];
        }
        
        if (ubos.length === 0) {
            ubos.push({ title: '', first_name: '', middle_name: '', last_name: '', dob: '', email: '', country: '', percentage: '' });
        }
        
        container.innerHTML = '';
        ubos.forEach((ubo, index) => {
            container.insertAdjacentHTML('beforeend', getUboRowHtml(index, ubo));
        });
    }

    function addUboRow() {
        let ubos = [];
        try {
            ubos = JSON.parse(document.getElementById('entity_ubos_json').value || '[]');
        } catch (e) {
            ubos = [];
        }
        ubos.push({ title: '', first_name: '', middle_name: '', last_name: '', dob: '', email: '', country: '', percentage: '' });
        document.getElementById('entity_ubos_json').value = JSON.stringify(ubos);
        renderUbos();
        saveActiveStepData(false);
    }

    function removeUboRow(index) {
        let ubos = [];
        try {
            ubos = JSON.parse(document.getElementById('entity_ubos_json').value || '[]');
        } catch (e) {
            ubos = [];
        }
        ubos.splice(index, 1);
        document.getElementById('entity_ubos_json').value = JSON.stringify(ubos);
        renderUbos();
        saveActiveStepData(false);
    }

    function serializeUbos() {
        const rows = document.querySelectorAll('.ubo-row');
        const ubos = [];
        rows.forEach(row => {
            const countryEl = row.querySelector('.ubo-country');
            ubos.push({
                title: row.querySelector('.ubo-title').value,
                first_name: row.querySelector('.ubo-first-name').value,
                middle_name: row.querySelector('.ubo-middle-name').value,
                last_name: row.querySelector('.ubo-last-name').value,
                dob: row.querySelector('.ubo-dob').value,
                email: row.querySelector('.ubo-email').value,
                country: countryEl ? countryEl.value : '',
                percentage: row.querySelector('.ubo-percentage').value
            });
        });
        document.getElementById('entity_ubos_json').value = JSON.stringify(ubos);
        saveActiveStepData(false);
    }

    // DIRECTORS
    function getDirectorRowHtml(index, data) {
        const dirNum = index + 1;
        const countryOptions = getCountryOptionsHtml(data.nationality);
        const dirTitleId = `dir-title-${index}`;
        const dirNameId = `dir-name-${index}`;
        const dirEmailId = `dir-email-${index}`;
        const dirPhoneId = `dir-phone-${index}`;
        const dirNatId = `dir-nationality-${index}`;
        return `
            <div class="director-row" data-index="${index}" style="display: flex; flex-direction: column; gap: 20px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #fff; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;" class="theme-pane-header">Director #${dirNum}</strong>
                    ${index > 0 ? `<button type="button" onclick="removeDirectorRow(${index})" style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 14px;"><i class="fa-solid fa-trash-can"></i> Remove</button>` : ''}
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div>
                        <label class="stepper-label" for="${dirTitleId}">Director's Title/Occupation</label>
                        <input type="text" id="${dirTitleId}" value="${data.title || ''}" class="stepper-input director-title" placeholder="Director's Title/Occupation" oninput="serializeDirectors()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${dirNameId}">Director's Full Legal Name *</label>
                        <input type="text" id="${dirNameId}" value="${data.name || ''}" class="stepper-input director-name" placeholder="Director's Full Legal Name" required oninput="serializeDirectors()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${dirEmailId}">Director's Email Address *</label>
                        <input type="email" id="${dirEmailId}" value="${data.email || ''}" class="stepper-input director-email" placeholder="Director's Email Address" required oninput="serializeDirectors()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${dirPhoneId}">Contact Number *</label>
                        <input type="text" id="${dirPhoneId}" value="${data.phone || ''}" class="stepper-input director-phone" placeholder="Contact Number" required oninput="serializeDirectors()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${dirNatId}">Nationality/Jurisdiction *</label>
                        <select id="${dirNatId}" class="stepper-input director-nationality" required onchange="serializeDirectors()">
                            ${countryOptions}
                        </select>
                    </div>
                </div>
            </div>
        `;
    }

    function renderDirectors() {
        const container = document.getElementById('director-rows-container');
        if (!container) return;
        
        let directors = [];
        try {
            const raw = document.getElementById('entity_directors_json').value;
            directors = JSON.parse(raw || '[]');
        } catch (e) {
            directors = [];
        }
        
        if (directors.length === 0) {
            directors.push({ title: '', name: '', email: '', phone: '', nationality: '' });
        }
        
        container.innerHTML = '';
        directors.forEach((dir, index) => {
            container.insertAdjacentHTML('beforeend', getDirectorRowHtml(index, dir));
        });
    }

    function addDirectorRow() {
        let directors = [];
        try {
            directors = JSON.parse(document.getElementById('entity_directors_json').value || '[]');
        } catch (e) {
            directors = [];
        }
        directors.push({ title: '', name: '', email: '', phone: '', nationality: '' });
        document.getElementById('entity_directors_json').value = JSON.stringify(directors);
        renderDirectors();
        saveActiveStepData(false);
    }

    function removeDirectorRow(index) {
        let directors = [];
        try {
            directors = JSON.parse(document.getElementById('entity_directors_json').value || '[]');
        } catch (e) {
            directors = [];
        }
        directors.splice(index, 1);
        document.getElementById('entity_directors_json').value = JSON.stringify(directors);
        renderDirectors();
        saveActiveStepData(false);
    }

    function serializeDirectors() {
        const rows = document.querySelectorAll('.director-row');
        const directors = [];
        rows.forEach(row => {
            const natEl = row.querySelector('.director-nationality');
            directors.push({
                title: row.querySelector('.director-title').value,
                name: row.querySelector('.director-name').value,
                email: row.querySelector('.director-email').value,
                phone: row.querySelector('.director-phone').value,
                nationality: natEl ? natEl.value : ''
            });
        });
        document.getElementById('entity_directors_json').value = JSON.stringify(directors);
        saveActiveStepData(false);
    }

    // SIGNATORIES
    function getSignatoryRowHtml(index, data) {
        const sigNum = index + 1;
        const sigNameId = `sig-name-${index}`;
        const sigEmailId = `sig-email-${index}`;
        const sigPhoneId = `sig-phone-${index}`;
        return `
            <div class="signatory-row" data-index="${index}" style="display: flex; flex-direction: column; gap: 20px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #fff; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;" class="theme-pane-header">Authorized User/Signatory #${sigNum}</strong>
                    ${index > 0 ? `<button type="button" onclick="removeSignatoryRow(${index})" style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 14px;"><i class="fa-solid fa-trash-can"></i> Remove</button>` : ''}
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div>
                        <label class="stepper-label" for="${sigNameId}">Name *</label>
                        <input type="text" id="${sigNameId}" value="${data.name || ''}" class="stepper-input signatory-name" placeholder="Name" required oninput="serializeSignatories()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${sigEmailId}">Email Address *</label>
                        <input type="email" id="${sigEmailId}" value="${data.email || ''}" class="stepper-input signatory-email" placeholder="Email Address" required oninput="serializeSignatories()">
                    </div>

                    <div>
                        <label class="stepper-label" for="${sigPhoneId}">Contact Number *</label>
                        <input type="text" id="${sigPhoneId}" value="${data.phone || ''}" class="stepper-input signatory-phone" placeholder="Contact Number" required oninput="serializeSignatories()">
                    </div>
                </div>
            </div>
        `;
    }

    function renderSignatories() {
        const container = document.getElementById('signatory-rows-container');
        if (!container) return;
        
        let signatories = [];
        try {
            const raw = document.getElementById('entity_authorized_signatories_json').value;
            signatories = JSON.parse(raw || '[]');
        } catch (e) {
            signatories = [];
        }
        
        if (signatories.length === 0) {
            signatories.push({ name: '', email: '', phone: '' });
        }
        
        container.innerHTML = '';
        signatories.forEach((sig, index) => {
            container.insertAdjacentHTML('beforeend', getSignatoryRowHtml(index, sig));
        });
    }

    function addSignatoryRow() {
        let signatories = [];
        try {
            signatories = JSON.parse(document.getElementById('entity_authorized_signatories_json').value || '[]');
        } catch (e) {
            signatories = [];
        }
        signatories.push({ name: '', email: '', phone: '' });
        document.getElementById('entity_authorized_signatories_json').value = JSON.stringify(signatories);
        renderSignatories();
        saveActiveStepData(false);
    }

    function removeSignatoryRow(index) {
        let signatories = [];
        try {
            signatories = JSON.parse(document.getElementById('entity_authorized_signatories_json').value || '[]');
        } catch (e) {
            signatories = [];
        }
        signatories.splice(index, 1);
        document.getElementById('entity_authorized_signatories_json').value = JSON.stringify(signatories);
        renderSignatories();
        saveActiveStepData(false);
    }

    // Live serialization of authorized signatories
    function serializeSignatories() {
        const rows = document.querySelectorAll('.signatory-row');
        const signatories = [];
        rows.forEach(row => {
            signatories.push({
                name: row.querySelector('.signatory-name').value,
                email: row.querySelector('.signatory-email').value,
                phone: row.querySelector('.signatory-phone').value
            });
        });
        document.getElementById('entity_authorized_signatories_json').value = JSON.stringify(signatories);
        saveActiveStepData(false);
    }

    // Enable/disable submit buttons based on Referral completion (matches Screenshot 4)
    function handleReferralSelectChange(val) {
        const submitButtons = document.querySelectorAll('.btn-step-submit-action');
        const codeWrapper = document.getElementById('referral-code-wrapper');
        
        if (val) {
            submitButtons.forEach(btn => {
                btn.style.background = '#f5b016';
                btn.style.color = '#000';
                btn.style.cursor = 'pointer';
                btn.disabled = false;
            });
            if (codeWrapper) codeWrapper.style.display = 'block';
        } else {
            submitButtons.forEach(btn => {
                btn.style.background = 'rgba(255,255,255,0.08)';
                btn.style.color = 'rgba(255,255,255,0.3)';
                btn.style.cursor = 'not-allowed';
                btn.disabled = true;
            });
            if (codeWrapper) codeWrapper.style.display = 'none';
        }
        saveActiveStepData(false);
        updateAllTabIndicators();
    }

    // General Validation Core logic (Determines if all required fields in step are fully complete)
    function isStepComplete(stepKey) {
        const pane = document.getElementById(`pane-${stepKey}`);
        if (!pane) return false;

        const requiredInputs = pane.querySelectorAll('[required]');
        let allFilled = true;

        requiredInputs.forEach(input => {
            if (activeAccountType === 'Entity' && input.closest('.individual-only-field')) return;
            if (activeAccountType === 'Individual' && input.closest('.entity-only-field')) return;
            const closestHiddenParent = input.closest('[style*="display: none"], [style*="display:none"]');
            if (closestHiddenParent && closestHiddenParent.id !== `pane-${stepKey}`) return;

            if (input.type === 'checkbox') {
                if (!input.checked) allFilled = false;
            } else if (input.type === 'radio') {
                // Radio groups check
                const name = input.name;
                const checkedRadio = pane.querySelector(`input[name="${name}"]:checked`);
                if (!checkedRadio) allFilled = false;
            } else {
                if (input.value.trim() === '') {
                    allFilled = false;
                }
            }
        });

        // Special custom validations:
        if (stepKey === 'proof_funds') {
            const fileVal = document.getElementById('proof_funds_file_input').value;
            if (!fileVal) allFilled = false;
        }
        if (stepKey === 'declaration') {
            const declVal = document.getElementById('declaration_signed_input').value;
            if (declVal !== '1') allFilled = false;
        }
        if (stepKey === 'kyc') {
            const kycVal = document.getElementById('kyc_document_file_input').value;
            if (!kycVal) allFilled = false;
        }
        if (stepKey === 'upload_documents') {
            const f1 = document.getElementById('entity_articles_file_input').value;
            const f2 = document.getElementById('entity_shareholders_file_input').value;
            const f3 = document.getElementById('entity_bank_statement_file_input').value;
            const f4 = document.getElementById('entity_proof_address_file_input').value;
            const f5 = document.getElementById('entity_board_resolution_file_input').value;
            if (!f1 || !f2 || !f3 || !f4 || !f5) allFilled = false;
        }
        if (stepKey === 'ubo_verification') {
            try {
                const raw = document.getElementById('entity_ubos_json').value;
                const ubos = JSON.parse(raw || '[]');
                if (ubos.length === 0) allFilled = false;
            } catch (e) {
                allFilled = false;
            }
        }
        if (stepKey === 'directors_authorized') {
            try {
                const rawDir = document.getElementById('entity_directors_json').value;
                const dirs = JSON.parse(rawDir || '[]');
                const rawSig = document.getElementById('entity_authorized_signatories_json').value;
                const sigs = JSON.parse(rawSig || '[]');
                if (dirs.length === 0 || sigs.length === 0) allFilled = false;
            } catch (e) {
                allFilled = false;
            }
        }

        return allFilled;
    }

    // Dynamic Sidebar completion indicators renderer (matches Step 2 completed tick vs pending orange exclamations specs)
    function updateAllTabIndicators() {
        stepsSequence.forEach(step => {
            const tab = document.getElementById(`tab-${step}`);
            if (tab && !tab.classList.contains('active')) {
                const icon = tab.querySelector('.tab-icon i');
                const complete = isStepComplete(step);
                
                if (complete) {
                    tab.className = "step-tab completed";
                    icon.className = "fa-solid fa-circle-check";
                } else {
                    tab.className = "step-tab pending";
                    icon.className = "fa-solid fa-circle-exclamation";
                }
            }
        });
    }

    // Live validation error flags highlighter
    function validateStepFields(stepKey) {
        const pane = document.getElementById(`pane-${stepKey}`);
        if (!pane) return true;

        const requiredInputs = pane.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (activeAccountType === 'Entity' && input.closest('.individual-only-field')) return;
            if (activeAccountType === 'Individual' && input.closest('.entity-only-field')) return;
            const closestHiddenParent = input.closest('[style*="display: none"], [style*="display:none"]');
            if (closestHiddenParent && closestHiddenParent.id !== `pane-${stepKey}`) return;

            if (input.value.trim() === '') {
                isValid = false;
                input.style.borderColor = '#e74c3c'; // red border
                input.style.boxShadow = '0 0 10px rgba(231, 76, 60, 0.2)';
            } else {
                input.style.borderColor = 'rgba(255,255,255,0.08)';
                input.style.boxShadow = 'none';
            }
        });

        // Special custom validations:
        if (stepKey === 'proof_funds') {
            const fileVal = document.getElementById('proof_funds_file_input').value;
            if (!fileVal) {
                isValid = false;
                document.getElementById('proof-upload-area').style.borderColor = '#e74c3c';
            } else {
                document.getElementById('proof-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
            }
        }
        if (stepKey === 'declaration') {
            const declVal = document.getElementById('declaration_signed_input').value;
            if (declVal !== '1') {
                isValid = false;
                document.getElementById('declaration-check-box').style.borderColor = '#e74c3c';
            } else {
                document.getElementById('declaration-check-box').style.borderColor = '#fff';
            }
        }
        if (stepKey === 'kyc') {
            const kycVal = document.getElementById('kyc_document_file_input').value;
            if (!kycVal) {
                isValid = false;
                document.getElementById('kyc-upload-area').style.borderColor = '#e74c3c';
            } else {
                document.getElementById('kyc-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
            }
        }
        if (stepKey === 'upload_documents') {
            const f1 = document.getElementById('entity_articles_file_input').value;
            const f2 = document.getElementById('entity_shareholders_file_input').value;
            const f3 = document.getElementById('entity_bank_statement_file_input').value;
            const f4 = document.getElementById('entity_proof_address_file_input').value;
            const f5 = document.getElementById('entity_board_resolution_file_input').value;
            if (!f1 || !f2 || !f3 || !f4 || !f5) {
                isValid = false;
                document.getElementById('entity_articles_file-upload-area').style.borderColor = f1 ? 'rgba(255,255,255,0.15)' : '#e74c3c';
                document.getElementById('entity_shareholders_file-upload-area').style.borderColor = f2 ? 'rgba(255,255,255,0.15)' : '#e74c3c';
                document.getElementById('entity_bank_statement_file-upload-area').style.borderColor = f3 ? 'rgba(255,255,255,0.15)' : '#e74c3c';
                document.getElementById('entity_proof_address_file-upload-area').style.borderColor = f4 ? 'rgba(255,255,255,0.15)' : '#e74c3c';
                document.getElementById('entity_board_resolution_file-upload-area').style.borderColor = f5 ? 'rgba(255,255,255,0.15)' : '#e74c3c';
            } else {
                document.getElementById('entity_articles_file-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
                document.getElementById('entity_shareholders_file-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
                document.getElementById('entity_bank_statement_file-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
                document.getElementById('entity_proof_address_file-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
                document.getElementById('entity_board_resolution_file-upload-area').style.borderColor = 'rgba(255,255,255,0.15)';
            }
        }

        return isValid;
    }

    // Show premium validation warning toast
    function showValidationWarningToast() {
        const toast = document.getElementById('validation-toast');
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // Switch step via side tabs (Adjusted dynamic button behaviors per screenshot specifications)
    function switchStep(targetKey) {
        // Save current active step first
        saveActiveStepData(false);

        activeStepKey = targetKey;

        // Toggle active pane
        document.querySelectorAll('.step-pane').forEach(pane => pane.classList.remove('active'));
        const activePane = document.getElementById(`pane-${targetKey}`);
        if (activePane) activePane.classList.add('active');

        // Toggle active navigation tab
        document.querySelectorAll('.step-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        const activeTab = document.getElementById(`tab-${targetKey}`);
        if (activeTab) {
            activeTab.className = "step-tab active";
            activeTab.querySelector('.tab-icon i').className = "fa-solid fa-circle-exclamation";
        }

        // Update all other tab statuses dynamically based on database/field values
        updateAllTabIndicators();

        // Update top header title
        const headerTitle = document.getElementById('step-title-header');
        if (headerTitle) headerTitle.innerText = stepTitles[targetKey] || "Institutional Onboarding";

        // Dynamic buttons display matching screenshots specifications
        const saveVerifyBtns = document.querySelectorAll('.btn-step-save-action');
        const prevBtns = document.querySelectorAll('.btn-step-prev-action');
        const nextBtns = document.querySelectorAll('.btn-step-next-action');
        const submitBtns = document.querySelectorAll('.btn-step-submit-action');

        // 1. Save and Verify button show/hide (Only visible on Step 1-7, and 10)
        if (['declaration', 'kyc', 'password_setup'].includes(targetKey)) {
            saveVerifyBtns.forEach(btn => btn.style.display = 'none');
        } else {
            saveVerifyBtns.forEach(btn => btn.style.display = 'flex');
        }

        // 2. Previous button show/hide (Hidden on first step)
        if (targetKey === stepsSequence[0]) {
            prevBtns.forEach(btn => btn.style.display = 'none');
        } else {
            prevBtns.forEach(btn => btn.style.display = 'flex');
        }

        // 3. Next vs Submit buttons
        if (targetKey === 'referral') {
            nextBtns.forEach(btn => btn.style.display = 'none');
            submitBtns.forEach(btn => btn.style.display = 'flex');
            
            // Handle Submit disabled state check
            const refVal = document.getElementById('referral-select-input').value;
            handleReferralSelectChange(refVal);
        } else if (targetKey === 'password_setup') {
            nextBtns.forEach(btn => btn.style.display = 'none');
            submitBtns.forEach(btn => btn.style.display = 'none');
        } else {
            nextBtns.forEach(btn => btn.style.display = 'flex');
            submitBtns.forEach(btn => btn.style.display = 'none');
        }

        // Auto-save the new step indicator to database
        saveCurrentStepKey(targetKey);
    }

    // Go to previous step
    function goToPreviousStep() {
        const currentIndex = stepsSequence.indexOf(activeStepKey);
        if (currentIndex > 0) {
            switchStep(stepsSequence[currentIndex - 1]);
        }
    }

    // Next Button advance with strict fields validation
    function advanceStep() {
        // Validate current step fields before going next!
        const isValid = validateStepFields(activeStepKey);
        if (!isValid) {
            showValidationWarningToast();
            return;
        }

        const currentIndex = stepsSequence.indexOf(activeStepKey);
        if (currentIndex !== -1 && currentIndex < stepsSequence.length - 1) {
            switchStep(stepsSequence[currentIndex + 1]);
        }
    }

    // AJAX Save progress of active step input fields via FormData
    async function saveActiveStepData(showNotice = false) {
        const pane = document.getElementById(`pane-${activeStepKey}`);
        if (!pane) return;

        // Serialize inputs inside the active step pane using FormData
        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('_token', '<?= csrf_token() ?>');
        formData.append('current_step', activeStepKey);

        const inputs = pane.querySelectorAll('input, select, textarea');
        let filledCount = 0;
        
        inputs.forEach(input => {
            if (input.name) {
                if (input.type === 'file') {
                    // Handled separately by dynamic ajax file upload calls
                } else if (input.type === 'radio') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                        filledCount++;
                    }
                } else if (input.type === 'checkbox') {
                    formData.append(input.name, input.checked ? '1' : '0');
                    if (input.checked) filledCount++;
                } else {
                    formData.append(input.name, input.value);
                    if (input.value !== '') {
                        filledCount++;
                    }
                }
            }
        });

        try {
            const response = await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
            const result = await response.json();
            
            if (showNotice) {
                if (result.status === 'success') {
                    alert("Onboarding progress successfully saved and verified!");
                } else {
                    alert("Verification Warning: " + (result.error || "Save rejected by server."));
                }
            }
        } catch (e) {
            console.error("AJAX autosave error:", e);
        }
    }

    // Save only current active step key state in background
    async function saveCurrentStepKey(stepKey) {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('token', token);
        formData.append('current_step', stepKey);
        formData.append('_token', '<?= csrf_token() ?>');

        try {
            await fetch("<?= url('/api/register/save') ?>", {
                method: "POST",
                body: formData
            });
        } catch (e) {
            console.error("Step key save failed", e);
        }
    }

    // Trigger auto-saving as user typing (with simple debounce)
    let autosaveDebounceTimeout;
    document.getElementById('multi-step-autosave-form').addEventListener('input', () => {
        clearTimeout(autosaveDebounceTimeout);
        autosaveDebounceTimeout = setTimeout(() => {
            saveActiveStepData(false);
            updateAllTabIndicators();
        }, 1000); // Trigger save after 1 second of inactivity
    });

    document.getElementById('multi-step-autosave-form').addEventListener('change', () => {
        saveActiveStepData(false);
        updateAllTabIndicators();
    });

    // Handle final form submit
    function handleFinalRegistrationSubmit(e) {
        const password = document.getElementById('final-password').value;
        const confirm = document.getElementById('final-password_confirmation').value;

        if (!password || !confirm) {
            alert("Please establish a secure registration password.");
            e.preventDefault();
            return false;
        }

        if (password !== confirm) {
            alert("The password confirmation does not match.");
            e.preventDefault();
            return false;
        }

        if (password.length < 8) {
            alert("Your security password must be at least 8 characters long.");
            e.preventDefault();
            return false;
        }

        return true;
    }

    // Drag and Drop File Upload Styling Enhancements
    function setupDragAndDrop(areaId, inputId, uploadFunc) {
        const area = document.getElementById(areaId);
        const input = document.getElementById(inputId);

        if (!area || !input) return;

        ['dragenter', 'dragover'].forEach(eventName => {
            area.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                area.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                area.classList.remove('dragover');
            }, false);
        });

        area.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                input.files = files;
                uploadFunc(input);
            }
        }, false);
    }

    // Trigger initial UI setup if user was already in a step
    window.addEventListener('DOMContentLoaded', () => {
        if (activeAccountType === 'Entity') {
            document.body.classList.add('entity-theme');
            document.body.classList.remove('individual-theme');
            renderUbos();
            renderDirectors();
            renderSignatories();
        } else {
            document.body.classList.add('individual-theme');
            document.body.classList.remove('entity-theme');
        }

        renderSidebarTabs();
        renderProofFundsFiles();
        renderKycFiles();

        if (activeAccountType && activeStepKey) {
            // Check if step exists in current account type steps, otherwise fallback to first step
            if (!stepsSequence.includes(activeStepKey)) {
                activeStepKey = stepsSequence[0];
            }
            switchStep(activeStepKey);
        }

        // Setup Drag & Drops
        setupDragAndDrop('proof-upload-area', 'proof_funds_file_upload', handleProofOfFundsFileSelect);
        setupDragAndDrop('kyc-upload-area', 'kyc_document_file_upload', handleKycFileSelect);

        if (activeAccountType === 'Entity') {
            setupDragAndDrop('entity_articles_file-upload-area', 'entity_articles_file_upload', (input) => handleEntityFileSelect(input, 'entity_articles_file'));
            setupDragAndDrop('entity_shareholders_file-upload-area', 'entity_shareholders_file_upload', (input) => handleEntityFileSelect(input, 'entity_shareholders_file'));
            setupDragAndDrop('entity_bank_statement_file-upload-area', 'entity_bank_statement_file_upload', (input) => handleEntityFileSelect(input, 'entity_bank_statement_file'));
            setupDragAndDrop('entity_proof_address_file-upload-area', 'entity_proof_address_file_upload', (input) => handleEntityFileSelect(input, 'entity_proof_address_file'));
            setupDragAndDrop('entity_board_resolution_file-upload-area', 'entity_board_resolution_file_upload', (input) => handleEntityFileSelect(input, 'entity_board_resolution_file'));
        }

        // Populate initial textarea character counters
        document.querySelectorAll('textarea').forEach(tx => {
            if (tx.name === 'trading_purpose_desc') updateCharCounter(tx, 'trading-desc-counter');
            if (tx.name === 'source_funding') updateCharCounter(tx, 'source-funding-counter');
            if (tx.name === 'lei_identifier') updateCharCounter(tx, 'lei-identifier-counter');
        });
    });
</script>
