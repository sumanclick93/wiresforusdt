<div class="auth-card" style="max-width: 800px; width: 100%;">
    <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px; margin-bottom: 24px;">
        <span style="color: var(--accent-neon); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">OPERATIONAL SCHEMATIC</span>
        <h1 style="font-size: 32px; font-weight: 800; margin: 0; color: #fff;">How Wires4USDT Works</h1>
    </div>

    <div style="font-size: 15px; line-height: 1.8; color: var(--text-muted); display: flex; flex-direction: column; gap: 24px;">
        <p>
            Wires4 operates a secure, high-limit digital liquidity gateway based on a **Verification-First Secure Onboarding Pipeline**. Below is our operational structure:
        </p>

        <!-- Steps Timeline Grid -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Step 1 -->
            <div style="display: flex; gap: 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; align-items: flex-start;">
                <div style="background: var(--accent-neon); color: #000; font-weight: bold; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; box-shadow: 0 0 10px rgba(185,255,58,0.3);">
                    1
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: bold; color: #fff; margin-bottom: 6px;">Perimeter Validation Request</h3>
                    <p style="font-size: 13px; margin: 0;">
                        Submit your institutional email through our landing page. The Wires4 Core PHP backend generates a highly unique, temporary cryptographic invitation token.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div style="display: flex; gap: 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; align-items: flex-start;">
                <div style="background: var(--accent-neon); color: #000; font-weight: bold; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; box-shadow: 0 0 10px rgba(185,255,58,0.3);">
                    2
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: bold; color: #fff; margin-bottom: 6px;">Profile & Settlement Nodes Setup</h3>
                    <p style="font-size: 13px; margin: 0;">
                        Access the gated enrollment form using the secure invite token in your inbox. Specify your full KYC details, verified settlement cryptocurrency wallet address, and network protocols (ERC-20, TRC-20, Arbitrum).
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div style="display: flex; gap: 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; align-items: flex-start;">
                <div style="background: var(--accent-neon); color: #000; font-weight: bold; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; box-shadow: 0 0 10px rgba(185,255,58,0.3);">
                    3
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: bold; color: #fff; margin-bottom: 6px;">Administrator Board Approval</h3>
                    <p style="font-size: 13px; margin: 0;">
                        Our compliance board reviews your application. Upon validation, the admin panel generates a unique alphanumeric Client ID (e.g. W4_1001) and sends a secure temporary password.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div style="display: flex; gap: 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; align-items: flex-start;">
                <div style="background: var(--accent-neon); color: #000; font-weight: bold; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; box-shadow: 0 0 10px rgba(185,255,58,0.3);">
                    4
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: bold; color: #fff; margin-bottom: 6px;">MFA Key Binding & Staking Access</h3>
                    <p style="font-size: 13px; margin: 0;">
                        Upon initial sign-in, the system prompts Deposit instructions and all non-bypassable Time-Based One-Time Password (TOTP) matrix. Link your authenticator application to activate Tier 1 Plus high-limit settlement.
                    </p>
                </div>
            </div>
        </div>

        <p>
            Once onboarding is fully verified, clients can initiate settlement and block deposits directly from custody to validators. Every block transaction settles with a flat **1% fee**, fully managed by our MSB licensed perimeter infrastructure.
        </p>
    </div>

    <div style="margin-top: 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px;">
        <a href="<?= url('/#request-access') ?>" class="btn btn-primary" style="padding: 12px 30px; border-radius: 30px;">Initialize Onboarding</a>
    </div>
</div>
