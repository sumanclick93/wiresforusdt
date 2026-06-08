<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Session;
use App\Helpers\TOTPHelper;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Helper to write to simulated mailbox JSON file.
     */
    protected function simulateEmail(string $email, string $subject, string $body): void
    {
        $filePath = __DIR__ . '/../../storage/app/simulated_emails.json';
        
        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $emails = [];
        if (file_exists($filePath)) {
            $emails = json_decode(file_get_contents($filePath), true) ?: [];
        }
        
        array_unshift($emails, [
            'id' => uniqid(),
            'to' => $email,
            'subject' => $subject,
            'body' => $body,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $emails = array_slice($emails, 0, 15);
        file_put_contents($filePath, json_encode($emails, JSON_PRETTY_PRINT));
    }

    /**
     * Get Simulated Emails JSON for drawer.
     */
    public function getSimulatedEmails(): void
    {
        header('Content-Type: application/json');
        $filePath = __DIR__ . '/../../storage/app/simulated_emails.json';
        if (file_exists($filePath)) {
            echo file_get_contents($filePath);
        } else {
            echo json_encode([]);
        }
    }

    /**
     * Clear Simulated Emails.
     */
    public function clearSimulatedEmails(): void
    {
        header('Content-Type: application/json');
        $filePath = __DIR__ . '/../../storage/app/simulated_emails.json';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        echo json_encode(['status' => 'cleared']);
    }

    /**
     * Public Welcome Route.
     */
    public function welcome(): void
    {
        $this->render('welcome');
    }

    /**
     * Public About Route.
     */
    public function about(): void
    {
        $this->render('about', [], 'About Our Custody & Liquidity Infrastructure');
    }

    /**
     * Public How It Works Route.
     */
    public function howItWork(): void
    {
        $this->render('how_it_work', [], 'Secure Block Settlement Pipeline');
    }

    /**
     * Public Proof of Funds Route.
     */
    public function proofOfFunds(): void
    {
        $this->render('proof_of_funds', [], 'Tests for sale');
    }

    /**
     * Public Contact Route.
     */
    public function contact(): void
    {
        $this->render('contact', [], 'Contact Wires4 Support');
    }

    /**
     * Request Access (Email Submission).
     */
    public function requestAccess(): void
    {
        $email = trim($_POST['email'] ?? '');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid institution email address.');
            $this->redirect('/#request-access');
            return;
        }

        // Check if user already exists
        if (User::findByEmail($email) !== null) {
            Session::flash('error', 'This email address is already associated with an account.');
            $this->redirect('/#request-access');
            return;
        }

        // Generate invitation token
        $rawToken = bin2hex(random_bytes(20));
        $hashedToken = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Store token in pending_registrations
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO pending_registrations (email, token, expires_at) 
            VALUES (:email, :token, :expires_at)
        ");
        $stmt->execute([
            ':email' => $email,
            ':token' => $hashedToken,
            ':expires_at' => $expiresAt
        ]);

        // Send simulated email invitation
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $registrationUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/register?token=" . $rawToken;
        $logoUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/images/logo.png";

        $subject = "Exclusive Invitation - Complete Your Wires4 Registration";
        $body = "
        <div style='background-color: #f6f8fa; padding: 40px 20px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; min-height: 100%;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e1e4e8; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <!-- Header with logo -->
                <div style='background-color: #060b0d; padding: 30px; text-align: center;'>
                    <img src='{$logoUrl}' alt='Wires4 Logo' style='height: 35px; max-width: 200px; display: inline-block; vertical-align: middle;' />
                </div>
                
                <!-- Body content -->
                <div style='padding: 40px 30px; text-align: center;'>
                    <h1 style='color: #060b0d; font-size: 24px; font-weight: 700; margin: 0 0 16px 0;'>Welcome To Wires4</h1>
                    <p style='color: #444444; font-size: 16px; line-height: 24px; margin: 0 0 24px 0;'>
                        Your request to access Wires4 has been pre-validated. Thank you for applying.
                    </p>
                    <p style='color: #666666; font-size: 14px; line-height: 20px; margin: 0 0 30px 0;'>
                        Please complete your institutional registration within 24 hours to secure your access.
                    </p>
                    
                    <!-- Button -->
                    <div style='margin: 30px 0;'>
                        <a href='{$registrationUrl}' target='_blank' rel='noopener noreferrer' style='background-color: #b9ff3a; color: #060b0d; padding: 16px 36px; text-decoration: none; border-radius: 30px; font-weight: 700; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px rgba(185, 255, 58, 0.15); transition: background-color 0.2s;'>Click Here To Begin Onboarding</a>
                    </div>
                    
                    <!-- Link Fallback -->
                    <p style='color: #888888; font-size: 12px; margin: 30px 0 10px 0;'>
                        If you cannot click the button, copy and paste this link into your browser:
                    </p>
                    <p style='margin: 0; word-break: break-all;'>
                        <a href='{$registrationUrl}' target='_blank' rel='noopener noreferrer' style='color: #0070f3; font-size: 13px; text-decoration: underline;'>{$registrationUrl}</a>
                    </p>
                    <p style='color: #aa0000; font-size: 12px; font-weight: 600; margin: 25px 0 0 0; font-style: italic;'>
                        This link is single-use and expires in 24 hours.
                    </p>
                </div>
            </div>
            
            <!-- Footer Disclaimer -->
            <div style='max-width: 600px; margin: 20px auto 0 auto; padding: 0 10px; text-align: left;'>
                <p style='color: #888888; font-size: 11px; line-height: 16px; margin: 0; text-align: justify;'>
                    This email and any attachments contain private, confidential, and proprietary information,<br>
                    including client-related data, intended solely for the use of the individual or entity to whom they are addressed.<br>
                    If you have received this email in error, please immediately notify the sender and delete this email and all copies from your system.<br>
                    Any unauthorized review, use, disclosure, or distribution of this email's contents is strictly prohibited.<br>
                    For details on how Wires4 handles personal and confidential information, please refer to our Privacy Policy:<br>
                    <a href='{$protocol}://{$_SERVER['HTTP_HOST']}/privacy-policy' style='color: #888888; text-decoration: underline;'>{$protocol}://{$_SERVER['HTTP_HOST']}/privacy-policy</a>
                </p>
            </div>
        </div>
        ";

        // Save to local simulated mailbox
        $this->simulateEmail($email, $subject, $body);

        // Send real email using SMTP MailHelper
        $sent = \App\Helpers\MailHelper::send($email, $subject, $body);

        if ($sent) {
            Session::flash('success', 'Your request has been successfully submitted! A secure registration link has been dispatched to your email inbox.');
        } else {
            Session::flash('error', 'Unable to dispatch enrollment email. Please contact support.');
        }
        $this->redirect('/#request-access');
    }

    /**
     * Show Registration Form.
     */
    public function showRegistrationForm(): void
    {
        $rawToken = $_GET['token'] ?? '';
        if (!$rawToken) {
            http_response_code(404);
            die("Invalid or missing registration token.");
        }

        $hashedToken = hash('sha256', $rawToken);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM pending_registrations 
            WHERE token = :token AND expires_at > :now 
            LIMIT 1
        ");
        $stmt->execute([
            ':token' => $hashedToken,
            ':now' => date('Y-m-d H:i:s')
        ]);
        $pending = $stmt->fetch();

        if (!$pending) {
            http_response_code(404);
            die("This registration link has expired or is invalid.");
        }

        // Fetch application progress
        $stmtApp = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmtApp->execute([':email' => $pending->email]);
        $application = $stmtApp->fetch();

        if (!$application) {
            // Create an empty dummy object to avoid warnings in view
            $application = (object) [
                'account_type' => '',
                'current_step' => 'application_info',
                'title_occupation' => '',
                'first_name' => '',
                'middle_name' => '',
                'last_name' => '',
                'dob' => '',
                'country' => '',
                'linkedin' => '',
                'instagram' => '',
                'twitter' => '',
                'street_address' => '',
                'unit_apartment' => '',
                'city' => '',
                'state_province' => '',
                'postal_zip' => '',
                'phone_number' => '',
                'trading_purpose' => '',
                'trading_purpose_desc' => '',
                'first_trade_date' => '',
                'flow_of_funds' => '',
                'first_trade_currency' => '',
                'first_trade_size' => '',
                'monthly_volume_currency' => '',
                'monthly_volume_size' => '',
                'source_funding' => '',
                'annual_income_currency' => '',
                'annual_income_amount' => '',
                'liquid_assets_currency' => '',
                'liquid_assets_amount' => '',
                'declared_bankruptcy' => '',
                'declared_bankruptcy_desc' => '',
                'pep_status' => '',
                'pep_status_desc' => '',
                'considerable_transactions' => '',
                'portfolio_excess' => '',
                'bank_currency' => '',
                'bank_account_holder' => '',
                'bank_account_number' => '',
                'bank_routing_code' => '',
                'bank_swift' => '',
                'bank_beneficiary_address' => '',
                'bank_name' => '',
                'bank_address' => '',
                'bank_country' => '',
                'bank_intermediary' => '',
                'proof_funds_type' => '',
                'proof_funds_description' => '',
                'proof_funds_file' => '',
                'wallet_address' => '',
                'network_type' => '',
                'declaration_signed' => 0,
                'kyc_document_type' => '',
                'kyc_document_file' => '',
                'referral_source' => '',
                'referral_code' => '',
                'entity_type' => '',
                'lei_identifier' => '',
                'incorporation_country' => '',
                'incorporation_date' => '',
                'company_regulated' => '',
                'declared_bankruptcy_entity' => '',
                'declared_bankruptcy_entity_desc' => '',
                'pep_status_entity' => '',
                'pep_status_entity_desc' => '',
                'financial_entity_us' => '',
                'swap_dealer' => '',
                'company_name' => '',
                'company_reg_number' => '',
                'contact_number' => '',
                'source_funding_entity' => '',
                'nature_of_business' => '',
                'street_address_entity' => '',
                'country_entity' => '',
                'city_entity' => '',
                'state_entity' => '',
                'postal_entity' => '',
                'operating_address_different' => '',
                'has_website' => '',
                'website' => '',
                'linkedin_entity' => '',
                'instagram_entity' => '',
                'twitter_entity' => '',
                'accredited_investor' => ''
            ];
        }

        // Fetch all dropdown options grouped by key
        $dbOptions = \App\Models\DropdownOption::all();
        $dropdowns = [];
        foreach ($dbOptions as $opt) {
            $dropdowns[$opt->dropdown_key][] = $opt->option_value;
        }

        $this->render('auth/register', [
            'email' => $pending->email,
            'token' => $rawToken,
            'application' => $application,
            'dropdowns' => $dropdowns
        ], 'Institutional Onboarding');
    }

    /**
     * AJAX Endpoint to save registration progress.
     */
    public function saveApplicationProgress(): void
    {
        header('Content-Type: application/json');
        
        $email = trim($_POST['email'] ?? '');
        $token = $_POST['token'] ?? '';
        
        if (isset($_POST['account_type'])) {
            if (strcasecmp($_POST['account_type'], 'Entity') === 0 || strcasecmp($_POST['account_type'], 'corporate') === 0) {
                $_POST['account_type'] = 'corporate';
            } else {
                $_POST['account_type'] = 'individual';
            }
        }
        
        if (!$email || !$token) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing email or validation token.']);
            return;
        }

        // Validate token
        $hashedToken = hash('sha256', $token);
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM pending_registrations 
            WHERE token = :token AND email = :email AND expires_at > :now 
            LIMIT 1
        ");
        $stmt->execute([
            ':token' => $hashedToken,
            ':email' => $email,
            ':now' => date('Y-m-d H:i:s')
        ]);
        $pending = $stmt->fetch();
        if (!$pending) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid or expired registration token.']);
            return;
        }

        // Verify email not already registered
        if (User::findByEmail($email) !== null) {
            http_response_code(400);
            echo json_encode(['error' => 'This email address is already registered.']);
            return;
        }

        // Check if there is already an application record
        $stmtApp = $db->prepare("SELECT id, proof_funds_file, kyc_document_file FROM user_applications WHERE email = :email LIMIT 1");
        $stmtApp->execute([':email' => $email]);
        $exists = $stmtApp->fetch();

        // Handle proof of funds file upload
        if (!empty($_FILES['proof_funds_file_upload'])) {
            $file = $_FILES['proof_funds_file_upload'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = 'proof_of_funds_' . uniqid() . '.' . $ext;
                $uploadDir = __DIR__ . '/../../storage/app/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    $existingVal = $exists ? ($exists->proof_funds_file ?? '') : '';
                    $fileList = [];
                    if (!empty($existingVal)) {
                        $decoded = json_decode($existingVal, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $fileList = $decoded;
                        } else {
                            $fileList = [$existingVal];
                        }
                    }
                    $fileList[] = $newName;
                    $_POST['proof_funds_file'] = json_encode($fileList);
                }
            }
        }

        // Handle KYC file upload
        if (!empty($_FILES['kyc_document_file_upload'])) {
            $file = $_FILES['kyc_document_file_upload'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = 'kyc_document_' . uniqid() . '.' . $ext;
                $uploadDir = __DIR__ . '/../../storage/app/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    $existingVal = $exists ? ($exists->kyc_document_file ?? '') : '';
                    $fileList = [];
                    if (!empty($existingVal)) {
                        $decoded = json_decode($existingVal, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $fileList = $decoded;
                        } else {
                            $fileList = [$existingVal];
                        }
                    }
                    $fileList[] = $newName;
                    $_POST['kyc_document_file'] = json_encode($fileList);
                }
            }
        }

        $entityFiles = [
            'entity_articles_file',
            'entity_shareholders_file',
            'entity_bank_statement_file',
            'entity_proof_address_file',
            'entity_board_resolution_file'
        ];
        foreach ($entityFiles as $fileKey) {
            $uploadKey = $fileKey . '_upload';
            if (!empty($_FILES[$uploadKey])) {
                $file = $_FILES[$uploadKey];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newName = $fileKey . '_' . uniqid() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../storage/app/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                        $_POST[$fileKey] = $newName;
                    }
                }
            }
        }

        // Field definitions for progression saves
        $fields = [
            'account_type', 'current_step', 'title_occupation', 'first_name', 'middle_name',
            'last_name', 'dob', 'country', 'linkedin', 'instagram', 'twitter',
            'street_address', 'unit_apartment', 'city', 'state_province', 'postal_zip',
            'phone_number', 'trading_purpose', 'trading_purpose_desc', 'first_trade_date',
            'flow_of_funds', 'first_trade_currency', 'first_trade_size', 'monthly_volume_currency',
            'monthly_volume_size', 'source_funding', 'annual_income_currency', 'annual_income_amount',
            'liquid_assets_currency', 'liquid_assets_amount', 'declared_bankruptcy', 'declared_bankruptcy_desc',
            'pep_status', 'pep_status_desc', 'considerable_transactions', 'portfolio_excess',
            'bank_currency', 'bank_account_holder', 'bank_account_number', 'bank_routing_code',
            'bank_swift', 'bank_beneficiary_address', 'bank_name', 'bank_address',
            'bank_country', 'bank_intermediary', 'proof_funds_type', 'proof_funds_description',
            'proof_funds_file', 'wallet_address', 'network_type', 'declaration_signed',
            'kyc_document_type', 'kyc_document_file', 'referral_source', 'referral_code',
            'entity_type', 'lei_identifier', 'incorporation_country', 'incorporation_date',
            'company_regulated', 'declared_bankruptcy_entity', 'declared_bankruptcy_entity_desc',
            'pep_status_entity', 'pep_status_entity_desc', 'financial_entity_us', 'swap_dealer',
            'company_name', 'company_reg_number', 'contact_number', 'source_funding_entity',
            'nature_of_business', 'street_address_entity', 'country_entity', 'city_entity',
            'state_entity', 'postal_entity', 'operating_address_different', 'has_website',
            'website', 'linkedin_entity', 'instagram_entity', 'twitter_entity', 'accredited_investor',
            'entity_articles_file', 'entity_shareholders_file', 'entity_bank_statement_file',
            'entity_proof_address_file', 'entity_board_resolution_file', 'entity_ubos_json',
            'entity_directors_json', 'entity_authorized_signatories_json'
        ];

        $updateData = [];
        $updateSQL = [];
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $val = $_POST[$field];
                if ($field === 'declaration_signed') {
                    $val = (int)$val;
                }
                $updateData[":$field"] = $val;
                $updateSQL[] = "$field = :$field";
            }
        }

        if (empty($updateSQL)) {
            echo json_encode(['status' => 'ignored', 'message' => 'No fields to update.']);
            return;
        }

        $updateData[':email'] = $email;

        if ($exists) {
            // Update
            $sql = "UPDATE user_applications SET " . implode(', ', $updateSQL) . ", updated_at = CURRENT_TIMESTAMP WHERE email = :email";
            $stmtSave = $db->prepare($sql);
            $stmtSave->execute($updateData);
        } else {
            // Insert
            $insertFields = array_keys($updateData);
            $insertColumns = array_map(fn($f) => ltrim($f, ':'), $insertFields);
            $sql = "INSERT INTO user_applications (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertFields) . ")";
            $stmtSave = $db->prepare($sql);
            $stmtSave->execute($updateData);
        }

        // Return current saved data so frontend can sync state
        $resData = ['status' => 'success', 'message' => 'Progress saved successfully.'];
        if (isset($_POST['proof_funds_file'])) {
            $val = $_POST['proof_funds_file'];
            $fileList = [];
            if (!empty($val)) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $f) {
                        $fileList[] = basename($f);
                    }
                } else {
                    $fileList[] = basename($val);
                }
            }
            $resData['proof_funds_file'] = json_encode($fileList);
        }
        if (isset($_POST['kyc_document_file'])) {
            $val = $_POST['kyc_document_file'];
            $fileList = [];
            if (!empty($val)) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $f) {
                        $fileList[] = basename($f);
                    }
                } else {
                    $fileList[] = basename($val);
                }
            }
            $resData['kyc_document_file'] = json_encode($fileList);
        }
        foreach ($entityFiles as $fileKey) {
            if (isset($_POST[$fileKey])) {
                $resData[$fileKey] = basename($_POST[$fileKey]);
            }
        }

        printf('%s', json_encode($resData));
    }

    /**
     * Process Profile Registration.
     */
    public function registerProfile(): void
    {
        $rawToken = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConf = $_POST['password_confirmation'] ?? '';

        // Validation checks
        if (!$rawToken || !$password || !$passwordConf) {
            Session::flash('error', 'All fields are required to secure your account credentials.');
            $this->back();
            return;
        }

        if ($password !== $passwordConf) {
            Session::flash('error', 'The password confirmation does not match.');
            $this->back();
            return;
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Your password must be at least 8 characters long.');
            $this->back();
            return;
        }

        $hashedToken = hash('sha256', $rawToken);
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM pending_registrations 
            WHERE token = :token AND expires_at > :now 
            LIMIT 1
        ");
        $stmt->execute([
            ':token' => $hashedToken,
            ':now' => date('Y-m-d H:i:s')
        ]);
        $pending = $stmt->fetch();

        if (!$pending) {
            Session::flash('error', 'Your registration token has expired or is invalid.');
            $this->redirect('/');
            return;
        }

        // Fetch application details
        $stmtApp = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmtApp->execute([':email' => $pending->email]);
        $application = $stmtApp->fetch();

        if (!$application) {
            Session::flash('error', 'Application details are missing. Please complete all steps.');
            $this->back();
            return;
        }

        // Verify email not already registered
        if (User::findByEmail($pending->email) !== null) {
            // Delete pending registration
            $del = $db->prepare("DELETE FROM pending_registrations WHERE token = :token");
            $del->execute([':token' => $hashedToken]);

            Session::flash('error', 'This email address is already registered.');
            $this->redirect('/');
            return;
        }

        // Create new inactive user profile
        $user = new User();
        $user->name = trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? ''));
        if (empty($user->name)) {
            $user->name = 'Trader';
        }
        $user->email = $pending->email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->wallet_address = $application->wallet_address ?? '';
        $user->network_type = $application->network_type ?? 'ERC-20';
        $user->status = 'pending_review';
        $user->role = 'user';
        $user->save();

        // Remove token so it cannot be re-used
        $del = $db->prepare("DELETE FROM pending_registrations WHERE token = :token");
        $del->execute([':token' => $hashedToken]);

        Session::flash('success', 'Your enrollment has been successfully submitted! Your account is currently under pending review. Our administrators will verify your details and deliver your Login ID shortly.');
        $this->redirect('/login');
    }

    /**
     * Show Login Portal.
     */
    public function showLoginForm(): void
    {
        if (Session::check()) {
            $this->redirect('/dashboard');
            return;
        }
        $this->render('auth/login', [], 'Secure Gateway');
    }

    /**
     * Process User Authentication.
     */
    public function login(): void
    {
        $loginId = trim($_POST['login_id'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$loginId || !$password) {
            Session::flash('error', 'Please enter your Secure Login ID and Password.');
            Session::flashInput($_POST);
            $this->back();
            return;
        }

        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            $user = User::findByEmail($loginId);
        } else {
            $user = User::findByLoginId($loginId);
        }

        if (!$user || !password_verify($password, $user->password)) {
            Session::flash('error', 'Invalid Login ID or Password.');
            Session::flashInput($_POST);
            $this->back();
            return;
        }

        if ($user->status === 'pending_review') {
            Session::flash('error', 'Your account is still pending administrator review.');
            $this->back();
            return;
        }

        if ($user->status === 'suspended') {
            Session::flash('error', 'Your account has been suspended. Please contact support.');
            $this->back();
            return;
        }

        // If 2FA enabled, intercept session and challenge
        if ($user->google2fa_enabled) {
            Session::set('totp_pending_user_id', $user->id);
            if (isset($_POST['remember'])) {
                Session::set('remember_me_pending', true);
            }
            $this->redirect('/login/2fa');
            return;
        }

        // Direct Login for users
        Session::login($user);

        if (isset($_POST['remember'])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                session_id(),
                time() + (30 * 24 * 60 * 60), // 30 days
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Enforce 2FA Setup if not set up previously
        if (!$user->google2fa_enabled) {
            $this->redirect('/setup-2fa');
            return;
        }

        $this->redirect('/dashboard');
    }

    /**
     * Show 2FA Verification Screen.
     */
    public function showLogin2FA(): void
    {
        if (!Session::has('totp_pending_user_id')) {
            $this->redirect('/login');
            return;
        }
        $this->render('auth/login_2fa', [], '2FA Verification');
    }

    /**
     * Verify OTP Code.
     */
    public function verify2FA(): void
    {
        $code = trim($_POST['code'] ?? '');
        $userId = Session::get('totp_pending_user_id');

        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        if (strlen($code) !== 6 || !is_numeric($code)) {
            Session::flash('error', 'Please enter a valid 6-digit authenticator code.');
            $this->back();
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        if (TOTPHelper::verifyCode($user->google2fa_secret, $code)) {
            // Secure full login
            Session::login($user);
            Session::forget('totp_pending_user_id');
            Session::set('totp_authenticated', true);

            if (Session::get('remember_me_pending')) {
                Session::forget('remember_me_pending');
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    session_id(),
                    time() + (30 * 24 * 60 * 60), // 30 days
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            if ($user->role === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/dashboard');
            }
            return;
        }

        Session::flash('error', 'The provided authenticator code is incorrect.');
        $this->back();
    }

    /**
     * Show 2FA Activations Form.
     */
    public function showSetup2FA(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        if ($user->google2fa_enabled) {
            $this->redirect('/dashboard');
            return;
        }

        // Fetch or create temporary secret key
        $secret = Session::get('totp_temp_secret');
        if (!$secret) {
            $secret = TOTPHelper::generateSecretKey();
            Session::set('totp_temp_secret', $secret);
        }

        $qrCodeUrl = TOTPHelper::getQRCodeUrl($user->email, $secret);

        $this->render('auth/setup_2fa', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl
        ], 'Multi-Factor Activation');
    }

    /**
     * Save 2FA Activation Profile.
     */
    public function saveSetup2FA(): void
    {
        $code = trim($_POST['code'] ?? '');
        $user = Session::user();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $secret = Session::get('totp_temp_secret');
        if (!$secret) {
            Session::flash('error', 'Session expired. Please try again.');
            $this->redirect('/setup-2fa');
            return;
        }

        if (strlen($code) !== 6 || !is_numeric($code)) {
            Session::flash('error', 'Please enter a valid 6-digit verification code.');
            $this->back();
            return;
        }

        if (TOTPHelper::verifyCode($secret, $code)) {
            $user->google2fa_secret = $secret;
            $user->google2fa_enabled = true;
            $user->save();

            Session::forget('totp_temp_secret');
            Session::set('totp_authenticated', true);

            Session::flash('success', 'TOTP Multi-Factor Authentication successfully activated!');
            $this->redirect('/dashboard');
            return;
        }

        Session::flash('error', 'Invalid validation code. Please scan the QR code again and enter the current code from your authenticator app.');
        $this->back();
    }

    /**
     * Terminate Session.
     */
    public function logout(): void
    {
        Session::logout();
        $this->redirect('/');
    }

    /**
     * Client User Area.
     */
    public function dashboard(): void
    {
        $user = Session::user();
        if ($user->role === 'admin') {
            $this->redirect('/admin/dashboard');
            return;
        }
        
        // Fetch user's purchase requests and sell requests
        $requests = \App\Models\USDTRequest::findByUserId($user->id);
        $sellRequests = \App\Models\USDTSellRequest::findByUserId($user->id);
        
        $this->render('dashboard', [
            'user' => $user,
            'requests' => $requests,
            'sellRequests' => $sellRequests
        ], 'Staking & Settlement Panel');
    }

    /**
     * Show the Buy USDT Purchase form.
     */
    public function showBuyUSDTForm(): void
    {
        $user = Session::user();
        
        // Fetch user application onboarding record to determine account_type
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $user->email]);
        $application = $stmt->fetch();
        
        $this->render('auth/buy_usdt', [
            'user' => $user,
            'application' => $application
        ], 'Buy USDT — Wires4');
    }

    /**
     * Handle the submit action of the USDT purchase request.
     */
    public function submitBuyUSDTRequest(): void
    {
        $user = Session::user();
        
        $depositReference = trim($_POST['deposit_reference_number'] ?? '');
        $usdtAmount = (float)($_POST['usdt_amount'] ?? 0.0);
        
        // Validation
        if (empty($depositReference)) {
            Session::flash('error', 'Please enter your Deposit Reference Number.');
            $this->back();
            return;
        }
        
        if ($usdtAmount <= 0.0) {
            Session::flash('error', 'Please enter a valid USDT Amount to purchase.');
            $this->back();
            return;
        }
        
        if (empty($_FILES['proof_of_deposit']) || $_FILES['proof_of_deposit']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please upload a valid Proof of Deposit document.');
            $this->back();
            return;
        }
        
        // Handle file upload
        $file = $_FILES['proof_of_deposit'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($ext, $allowedExtensions)) {
            Session::flash('error', 'Invalid file type. Only JPG, PNG, and PDF are allowed.');
            $this->back();
            return;
        }
        
        $newName = 'proof_of_deposit_' . uniqid() . '.' . $ext;
        $uploadDir = __DIR__ . '/../../storage/app/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
            Session::flash('error', 'Failed to upload proof of deposit. Please try again.');
            $this->back();
            return;
        }
        
        // Fetch user application to get account type and corresponding Wires4 receiving bank details
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $user->email]);
        $application = $stmt->fetch();
        
        $isCorporate = ($application && $application->account_type === 'corporate');
        
        // Configure bank details
        $bankName = $isCorporate ? "Signature Institutional Trust" : "Silvergate Vault Bank";
        $bankAddress = $isCorporate ? "40 Wall Street, Floor 28, New York, NY 10005, USA" : "8501 Park Ave, New York, NY 10022, USA";
        $routingNo = $isCorporate ? "026008673" : "021000021";
        $accountNo = $isCorporate ? "4567891230" : "9876543210";
        $beneficiary = $isCorporate ? "Wires4 USDT Digital LLC" : "Wires4 Digital Individual Trust";
        
        // Create Request
        $request = new \App\Models\USDTRequest();
        $request->user_id = $user->id;
        $request->receiving_bank_name = $bankName;
        $request->receiving_bank_address = $bankAddress;
        $request->routing_no_aba = $routingNo;
        $request->beneficiary_account_number = $accountNo;
        $request->beneficiary_name = $bankName; // Note: using beneficiary or bankName? The mockup has Beneficiary Name. Let's map accurately.
        $request->beneficiary_name = $beneficiary;
        $request->deposit_reference_number = $depositReference;
        $request->usdt_amount = $usdtAmount;
        $request->proof_of_deposit = $newName;
        $request->status = 'pending';
        
        if ($request->save()) {
            Session::flash('success', 'Your USDT purchase request has been submitted successfully! Our compliance desk will verify the wire transfer and credit your wallet shortly.');
            $this->redirect('/dashboard');
        } else {
            Session::flash('error', 'An error occurred while saving your purchase request.');
            $this->back();
        }
    }

    /**
     * Securely serve compliance or deposit proof uploads.
     */
    public function serveUpload(string $filename): void
    {
        $user = Session::user();
        if (!$user) {
            http_response_code(403);
            die("Unauthorized access.");
        }

        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        
        $filePath = __DIR__ . '/../../storage/app/uploads/' . $filename;
        $fallbackPath = __DIR__ . '/../../old/public_html/wiresforusdt_revamp/laravel/storage/app/uploads/' . $filename;

        $targetPath = null;
        if (file_exists($filePath)) {
            $targetPath = $filePath;
        } elseif (file_exists($fallbackPath)) {
            $targetPath = $fallbackPath;
        }

        if ($targetPath === null) {
            $dir = dirname($filePath);
            $dirExists = is_dir($dir) ? 'Yes' : 'No';
            $filesInDir = 'N/A (Directory does not exist)';
            if (is_dir($dir)) {
                $scan = scandir($dir);
                $filesInDir = json_encode(array_slice($scan, 0, 50)); // Show up to 50 files
            }
            
            // Check other common directories
            $altPaths = [
                'public_uploads' => __DIR__ . '/../../public/uploads/',
                'root_uploads' => __DIR__ . '/../../uploads/',
                'app_storage_uploads' => __DIR__ . '/../../storage/app/uploads/',
                'old_storage_uploads' => __DIR__ . '/../../old/public_html/wiresforusdt_revamp/laravel/storage/app/uploads/'
            ];
            $altInfo = [];
            foreach ($altPaths as $key => $p) {
                $altInfo[$key] = [
                    'path' => $p,
                    'exists' => is_dir($p) ? 'Yes' : 'No',
                    'writable' => is_writable($p) ? 'Yes' : 'No'
                ];
            }

            http_response_code(404);
            echo "<pre>File not found.\n\n";
            echo "Diagnostics:\n";
            echo "Checked File Path: " . $filePath . "\n";
            echo "Checked Fallback Path: " . $fallbackPath . "\n";
            echo "Upload Directory Exists: " . $dirExists . "\n";
            echo "Upload Directory Writable: " . (is_writable($dir) ? 'Yes' : 'No') . "\n";
            echo "Files in Upload Directory: " . $filesInDir . "\n\n";
            echo "Alternative Directories:\n";
            print_r($altInfo);
            echo "</pre>";
            die();
        }

        // Determine content type
        $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg'
        ];
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

        header("Content-Type: $contentType");
        header("Content-Length: " . filesize($targetPath));
        readfile($targetPath);
        exit;
    }

    /**
     * AJAX Endpoint to upload documents for authenticated profile editing.
     */
    public function uploadProfileFile(): void
    {
        header('Content-Type: application/json');
        $user = Session::user();
        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized access.']);
            return;
        }

        if ($user->edit_permission_status !== 'allowed' && empty($user->requested_documents)) {
            http_response_code(403);
            echo json_encode(['error' => 'Profile editing is not authorized at this time.']);
            return;
        }

        if (empty($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file was uploaded.']);
            return;
        }

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'File upload error code: ' . $file['error']]);
            return;
        }

        $fileKey = $_POST['file_key'] ?? 'document';
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($ext, $allowedExtensions)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file standard. Only JPG, PNG, and PDF formats are supported.']);
            return;
        }

        $newName = $fileKey . '_' . uniqid() . '.' . $ext;
        $uploadDir = __DIR__ . '/../../storage/app/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
            echo json_encode(['status' => 'success', 'filename' => $newName]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save uploaded file.']);
        }
        exit;
    }

    /**
     * Show the Sell USDT form.
     */
    public function showSellUSDTForm(): void
    {
        $user = Session::user();
        
        // Fetch user application onboarding record to pre-fill bank details and network
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $user->email]);
        $application = $stmt->fetch();
        
        $this->render('auth/sell_usdt', [
            'user' => $user,
            'application' => $application
        ], 'Sell USDT — Wires4');
    }

    /**
     * Process USDT Sell request.
     */
    public function submitSellUSDTRequest(): void
    {
        $user = Session::user();
        
        $usdtAmount = (float)($_POST['usdt_amount'] ?? 0.0);
        $bankName = trim($_POST['bank_name'] ?? '');
        $bankAccountNo = trim($_POST['bank_account_number'] ?? '');
        $bankAccountHolder = trim($_POST['bank_account_holder'] ?? '');
        $bankSwift = trim($_POST['bank_swift'] ?? '');
        
        // Validation
        if ($usdtAmount <= 0.0) {
            Session::flash('error', 'Please enter a valid USDT Amount to sell.');
            $this->back();
            return;
        }
        
        // Deduct validation - check wallet balance!
        // We reload user from DB to get fresh usdt_balance
        $freshUser = User::find($user->id);
        if ($usdtAmount > $freshUser->usdt_balance) {
            Session::flash('error', 'Insufficient USDT balance in your wallet. Active Balance: ' . number_format($freshUser->usdt_balance, 2) . ' USDT');
            $this->back();
            return;
        }
        
        if (empty($bankName) || empty($bankAccountNo) || empty($bankAccountHolder) || empty($bankSwift)) {
            Session::flash('error', 'Please fill in all receiving bank details.');
            $this->back();
            return;
        }
        
        // Configure Wires4 platform receiving wallet address based on network type
        $networkType = $user->network_type ?? 'ERC-20';
        $isTrc = (strpos(strtoupper($networkType), 'TRC') !== false);
        $platformWallet = $isTrc ? "TYsnK4xX6F8hK42yS6G7P9J2K1L5M4N3O5" : "0x71C7656EC7ab88b098defB751B7401B5f6d8976F";
        
        // Save Request
        $request = new \App\Models\USDTSellRequest();
        $request->user_id = $user->id;
        $request->usdt_amount = $usdtAmount;
        $request->platform_wallet_address = $platformWallet;
        $request->bank_name = $bankName;
        $request->bank_account_number = $bankAccountNo;
        $request->bank_account_holder = $bankAccountHolder;
        $request->bank_swift = $bankSwift;
        $request->status = 'pending';
        
        if ($request->save()) {
            Session::flash('success', 'Your USDT sell request has been submitted successfully! Once approved, Wires4 will wire USD fiat to your receiving bank account and deduct the USDT from your wallet.');
            $this->redirect('/dashboard');
        } else {
            Session::flash('error', 'An error occurred while saving your sell request.');
            $this->back();
        }
    }

    /**
     * Password Recovery.
     */
    public function showRecoveryForm(): void
    {
        $this->render('auth/recovery', [], 'Account Recovery');
    }

    /**
     * Process Self-Service Recovery link.
     */
    public function sendRecoveryLink(): void
    {
        $email = trim($_POST['email'] ?? '');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->back();
            return;
        }

        $user = User::findByEmail($email);
        $genericMsg = 'If a matching active account was found, a secure password recovery link was dispatched to your inbox. Please check the simulated mailbox.';

        if (!$user || $user->status !== 'active') {
            Session::flash('success', $genericMsg);
            $this->back();
            return;
        }

        $rawToken = bin2hex(random_bytes(20));
        $hashedToken = hash('sha256', $rawToken);

        $db = Database::getConnection();
        
        // Remove prior tokens
        $del = $db->prepare("DELETE FROM password_reset_tokens WHERE email = :email");
        $del->execute([':email' => $email]);

        // Insert new reset token
        $ins = $db->prepare("
            INSERT INTO password_reset_tokens (email, token, created_at) 
            VALUES (:email, :token, :created_at)
        ");
        $ins->execute([
            ':email' => $email,
            ':token' => $hashedToken,
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        // Send simulated recovery email
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $recoveryUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/recovery/reset?token=" . $rawToken . "&email=" . urlencode($email);
        $logoUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/images/logo.png";

        $subject = "Secured Account Recovery Request";
        $body = "
        <div style='background-color: #f6f8fa; padding: 40px 20px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; min-height: 100%;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e1e4e8; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <!-- Header with logo -->
                <div style='background-color: #060b0d; padding: 30px; text-align: center;'>
                    <img src='{$logoUrl}' alt='Wires4 Logo' style='height: 35px; max-width: 200px; display: inline-block; vertical-align: middle;' />
                </div>
                
                <!-- Body content -->
                <div style='padding: 40px 30px; text-align: center;'>
                    <h1 style='color: #060b0d; font-size: 24px; font-weight: 700; margin: 0 0 16px 0;'>Account Recovery</h1>
                    <p style='color: #444444; font-size: 16px; line-height: 24px; margin: 0 0 24px 0;'>
                        A request was received to reset your password. Use the secure link below to proceed.
                    </p>
                    <p style='color: #666666; font-size: 14px; line-height: 20px; margin: 0 0 30px 0;'>
                        This link is only valid for 15 minutes.
                    </p>
                    
                    <!-- Button -->
                    <div style='margin: 30px 0;'>
                        <a href='{$recoveryUrl}' target='_blank' rel='noopener noreferrer' style='background-color: #b9ff3a; color: #060b0d; padding: 16px 36px; text-decoration: none; border-radius: 30px; font-weight: 700; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px rgba(185, 255, 58, 0.15); transition: background-color 0.2s;'>Reset Secure Password</a>
                    </div>
                    
                    <!-- Link Fallback -->
                    <p style='color: #888888; font-size: 12px; margin: 30px 0 10px 0;'>
                        If you cannot click the button, copy and paste this link into your browser:
                    </p>
                    <p style='margin: 0; word-break: break-all;'>
                        <a href='{$recoveryUrl}' target='_blank' rel='noopener noreferrer' style='color: #0070f3; font-size: 13px; text-decoration: underline;'>{$recoveryUrl}</a>
                    </p>
                    <p style='color: #888888; font-size: 12px; margin: 25px 0 0 0; font-style: italic;'>
                        If you did not make this request, please secure your credentials immediately.
                    </p>
                </div>
            </div>
            
            <!-- Footer Disclaimer -->
            <div style='max-width: 600px; margin: 20px auto 0 auto; padding: 0 10px; text-align: left;'>
                <p style='color: #888888; font-size: 11px; line-height: 16px; margin: 0; text-align: justify;'>
                    This email and any attachments contain private, confidential, and proprietary information, including client-related data, intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error, please immediately notify the sender and delete this email and all copies from your system. Any unauthorized review, use, disclosure, or distribution of this email's contents is strictly prohibited. For details on how Wires4 handles personal and confidential information, please refer to our Privacy Policy: <a href='{$protocol}://{$_SERVER['HTTP_HOST']}/privacy-policy' style='color: #888888; text-decoration: underline;'>{$protocol}://{$_SERVER['HTTP_HOST']}/privacy-policy</a>
                </p>
            </div>
        </div>
        ";

        // Save to local simulated mailbox
        $this->simulateEmail($email, $subject, $body);

        // Send real email using SMTP MailHelper
        $sent = \App\Helpers\MailHelper::send($email, $subject, $body);

        if ($sent) {
            Session::flash('success', 'If a matching active account was found, a secure password recovery link has been dispatched to your email inbox.');
        } else {
            Session::flash('error', 'Unable to dispatch recovery email. Please contact support.');
        }
        $this->back();
    }

    /**
     * Show Password Reset Inputs.
     */
    public function showResetForm(): void
    {
        $rawToken = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if (!$rawToken || !$email) {
            http_response_code(404);
            die("Invalid password reset request.");
        }

        $hashedToken = hash('sha256', $rawToken);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $tokenRecord = $stmt->fetch();

        if (!$tokenRecord || !hash_equals($tokenRecord->token, $hashedToken)) {
            http_response_code(404);
            die("This recovery link has expired or is invalid.");
        }

        // Validate 15 minute limit
        $createdAt = strtotime($tokenRecord->created_at);
        if (time() - $createdAt > 900) { // 15 mins = 900s
            http_response_code(404);
            die("This recovery link has expired or is invalid.");
        }

        $this->render('auth/recovery_reset', [
            'email' => $email,
            'token' => $rawToken
        ], 'Reset Password');
    }

    /**
     * Process Password Reset.
     */
    public function resetPassword(): void
    {
        $email = trim($_POST['email'] ?? '');
        $rawToken = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConf = $_POST['password_confirmation'] ?? '';

        if (!$email || !$rawToken || !$password) {
            Session::flash('error', 'All fields are required to update your security credential.');
            $this->back();
            return;
        }

        if ($password !== $passwordConf) {
            Session::flash('error', 'Password confirmation does not match.');
            $this->back();
            return;
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters long.');
            $this->back();
            return;
        }

        $hashedToken = hash('sha256', $rawToken);
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $tokenRecord = $stmt->fetch();

        if (!$tokenRecord || !hash_equals($tokenRecord->token, $hashedToken)) {
            Session::flash('error', 'Your password reset token has expired or is invalid.');
            $this->redirect('/recovery');
            return;
        }

        $createdAt = strtotime($tokenRecord->created_at);
        if (time() - $createdAt > 900) {
            Session::flash('error', 'Your password reset token has expired or is invalid.');
            $this->redirect('/recovery');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            Session::flash('error', 'User not found.');
            $this->redirect('/recovery');
            return;
        }

        // Apply new password
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->save();

        // Delete token
        $del = $db->prepare("DELETE FROM password_reset_tokens WHERE email = :email");
        $del->execute([':email' => $email]);

        Session::flash('success', 'Your password has been reset successfully! You can now log in using your Login ID.');
        $this->redirect('/login');
    }

    /**
     * Skip 2FA activation for the current session.
     */
    public function skip2FA(): void
    {
        Session::set('skip_2fa_for_now', true);
        
        $user = Session::user();
        if ($user && $user->role === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/dashboard');
        }
    }

    /**
     * Show User Profile details.
     */
    public function showProfile(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $user->email]);
        $application = $stmt->fetch();

        if (!$application) {
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0] ?? 'Trader';
            $lastName = $nameParts[1] ?? '';
            
            $ins = $db->prepare("
                INSERT INTO user_applications (email, first_name, last_name, wallet_address, network_type, account_type) 
                VALUES (:email, :first_name, :last_name, :wallet_address, :network_type, 'individual')
            ");
            $ins->execute([
                ':email' => $user->email,
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':wallet_address' => $user->wallet_address,
                ':network_type' => $user->network_type ?: 'ERC-20'
            ]);

            // Re-fetch
            $stmt->execute([':email' => $user->email]);
            $application = $stmt->fetch();
        }

        $this->render('auth/profile', [
            'user' => $user,
            'application' => $application
        ], 'Profile Specifications');
    }

    /**
     * Request Permission to Edit Profile.
     */
    public function requestProfileEdit(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $user->edit_permission_status = 'requested';
        $user->save();

        Session::flash('success', 'Profile edit permission successfully requested. Awaiting administrator authorization.');
        $this->redirect('/profile');
    }

    /**
     * Submit Profile Updates.
     */
    public function submitProfileUpdate(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        if ($user->edit_permission_status !== 'allowed') {
            Session::flash('error', 'You do not have authorization to edit your profile.');
            $this->redirect('/profile');
            return;
        }

        // Capture all stepper fields
        $fields = [
            // Individual profile fields
            'title_occupation', 'first_name', 'middle_name', 'last_name', 'dob', 'country',
            'phone_number', 'street_address', 'unit_apartment', 'city', 'state_province',
            'postal_zip', 'linkedin', 'instagram', 'twitter',
            // Financial details
            'trading_purpose', 'trading_purpose_desc', 'first_trade_date', 'flow_of_funds',
            'first_trade_currency', 'first_trade_size', 'monthly_volume_currency', 'monthly_volume_size',
            'source_funding', 'annual_income_currency', 'annual_income_amount', 'liquid_assets_currency', 'liquid_assets_amount',
            // Banking details
            'bank_currency', 'bank_account_holder', 'bank_account_number', 'bank_routing_code',
            'bank_swift', 'bank_beneficiary_address', 'bank_name', 'bank_address', 'bank_country', 'bank_intermediary',
            // Risk declarations
            'declared_bankruptcy', 'declared_bankruptcy_desc', 'pep_status', 'pep_status_desc',
            'considerable_transactions', 'portfolio_excess', 'accredited_investor',
            // Corporate specifications
            'company_name', 'entity_type', 'company_reg_number', 'incorporation_country', 'incorporation_date',
            'lei_identifier', 'contact_number', 'nature_of_business', 'company_regulated',
            'street_address_entity', 'country_entity', 'city_entity', 'state_entity', 'postal_entity',
            'operating_address_different', 'has_website', 'website', 'linkedin_entity', 'instagram_entity', 'twitter_entity',
            'declared_bankruptcy_entity', 'declared_bankruptcy_entity_desc', 'pep_status_entity', 'pep_status_entity_desc',
            'financial_entity_us', 'swap_dealer',
            // Wallet node details
            'wallet_address', 'network_type',
            // File upload fields
            'kyc_document_file', 'proof_funds_file',
            'entity_articles_file', 'entity_shareholders_file', 'entity_bank_statement_file',
            'entity_proof_address_file', 'entity_board_resolution_file'
        ];

        $isDocRequestOnly = !empty($user->requested_documents);

        $pendingData = [];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($isDocRequestOnly && !str_ends_with($field, '_file')) {
                    continue;
                }
                $pendingData[$field] = $_POST[$field];
            }
        }

        $user->pending_profile_update = json_encode($pendingData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $user->edit_permission_status = 'pending_approval';
        $user->save();

        Session::flash('success', 'Profile updates successfully submitted. Your modifications are currently queued for administrator approval.');
        $this->redirect('/profile');
    }
}
