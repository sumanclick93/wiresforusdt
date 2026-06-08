<?php

namespace App\Controllers;

use App\Core\Session;
use App\Models\User;
use App\Helpers\CsvGenerator;
use App\Helpers\PdfGenerator;
use ZipArchive;

class AdminController extends Controller
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

        // Send real email using SMTP MailHelper
        \App\Helpers\MailHelper::send($email, $subject, $body);
    }

    /**
     * Download customer details and files in a ZIP archive.
     */
    public function downloadCustomerData(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        // Fetch application details
        $db = \App\Core\Database::getConnection();
        $stmtApp = $db->prepare("SELECT * FROM user_applications WHERE email = :email LIMIT 1");
        $stmtApp->execute([':email' => $user->email]);
        $application = $stmtApp->fetch();

        $format = strtolower($_GET['format'] ?? 'pdf');

        // Create sanitized name for filenames
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', strtolower($user->name)));
        if (empty($sanitizedName)) {
            $sanitizedName = 'customer_' . $user->id;
        }

        // Generate report content
        if ($format === 'excel' || $format === 'csv') {
            $reportContent = CsvGenerator::generate($user, $application);
            $reportFilename = $sanitizedName . '_details.csv';
        } else {
            $reportContent = PdfGenerator::generate($user, $application);
            $reportFilename = $sanitizedName . '_details.pdf';
        }

        // Serve file directly to download
        header('Pragma: no-cache');
        header('Expires: 0');
        if ($format === 'excel' || $format === 'csv') {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $reportFilename . '"');
        } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $reportFilename . '"');
        }
        header('Content-Length: ' . strlen($reportContent));
        echo $reportContent;
        exit;
    }

    /**
     * Admin Dashboard: List pending users and overall stats.
     */
    public function dashboard(): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $pendingUsers = User::allPending();
        $activeUsers = User::allActiveUsers();
        
        // Fetch USDT purchase requests
        $pendingUSDT = \App\Models\USDTRequest::allPending();
        $historyUSDT = \App\Models\USDTRequest::allHistory();

        // Fetch USDT sell requests
        $pendingSell = \App\Models\USDTSellRequest::allPending();
        $historySell = \App\Models\USDTSellRequest::allHistory();

        $this->render('admin/dashboard', [
            'pendingUsers' => $pendingUsers,
            'activeUsers' => $activeUsers,
            'pendingUSDT' => $pendingUSDT,
            'historyUSDT' => $historyUSDT,
            'pendingSell' => $pendingSell,
            'historySell' => $historySell
        ], 'Administrator Board');
    }

    /**
     * Approve USDT Purchase Request and credit user wallet.
     */
    public function approveUSDTRequest(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $request = \App\Models\USDTRequest::find((int)$id);
        if (!$request) {
            http_response_code(404);
            die("USDT Request not found.");
        }

        if ($request->status !== 'pending') {
            Session::flash('error', 'This purchase request is already processed.');
            $this->back();
            return;
        }

        $user = User::find($request->user_id);
        if (!$user) {
            Session::flash('error', 'The user associated with this request could not be found.');
            $this->back();
            return;
        }

        // Process request
        $request->status = 'approved';
        $request->save();

        // Credit user wallet
        $user->usdt_balance += $request->usdt_amount;
        $user->save();

        // Dispatch simulated email notification
        $subject = "USDT Purchase Request Approved - Wallet Credited";
        $body = "
            <h2>Your USDT Purchase Request is Approved!</h2>
            <p>Dear {$user->name},</p>
            <p>Our compliance desk has verified your wire deposit for reference number <strong>{$request->deposit_reference_number}</strong>.</p>
            <p>Your whitelisted settlement wallet has been successfully credited with the purchased amount:</p>
            <div style='background: #111; border: 1px solid #333; padding: 15px; border-radius: 4px; font-family: monospace; color: #fff; margin: 15px 0;'>
                <strong>Credited Amount:</strong> <span style='color: #b9ff3a; font-size: 18px; font-weight: bold;'>+" . number_format($request->usdt_amount, 2) . " USDT</span><br>
                <strong>New Active Balance:</strong> " . number_format($user->usdt_balance, 2) . " USDT<br>
                <strong>Target Wallet Address:</strong> <span style='font-size: 11px;'>{$user->wallet_address}</span>
            </div>
            <p>You can check your updated balance and request history from your secure client dashboard.</p>
            <p>Thank you for choosing Wires4.</p>
        ";

        $this->simulateEmail($user->email, $subject, $body);

        Session::flash('success', "USDT request for " . number_format($request->usdt_amount, 2) . " USDT has been approved and credited to {$user->name}'s wallet.");
        $this->back();
    }

    /**
     * Reject USDT Purchase Request.
     */
    public function rejectUSDTRequest(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $request = \App\Models\USDTRequest::find((int)$id);
        if (!$request) {
            http_response_code(404);
            die("USDT Request not found.");
        }

        if ($request->status !== 'pending') {
            Session::flash('error', 'This purchase request is already processed.');
            $this->back();
            return;
        }

        // Process request
        $request->status = 'rejected';
        $request->save();

        $user = User::find($request->user_id);
        if ($user) {
            // Dispatch simulated email notification
            $subject = "USDT Purchase Request Rejected";
            $body = "
                <h2>USDT Purchase Request Status Update</h2>
                <p>Dear {$user->name},</p>
                <p>Your USDT purchase request for reference number <strong>{$request->deposit_reference_number}</strong> (amounting to " . number_format($request->usdt_amount, 2) . " USDT) was unfortunately rejected by our compliance department.</p>
                <p>Please ensure that all wire transfer details exactly match your registered bank specifications, and that a clear, valid proof of deposit image/PDF is provided.</p>
                <p>If you believe this was an error, please contact Wires4 institutional support at support@wiresforusdt.com or via Telegram.</p>
            ";
            $this->simulateEmail($user->email, $subject, $body);
        }

        Session::flash('success', "USDT request for " . number_format($request->usdt_amount, 2) . " USDT has been rejected.");
        $this->back();
    }

    /**
     * Approve User Account.
     */
    public function approveUser(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        if ($user->status !== 'pending_review') {
            Session::flash('error', 'This user is not pending review.');
            $this->back();
            return;
        }

        // Generate unique Login ID: e.g. W4_1000 + user's database auto-increment ID
        $loginId = 'W4_' . (1000 + $user->id);
        
        // Update user - DO NOT overwrite the password they set during registration
        $user->login_id = $loginId;
        $user->status = 'active';
        $user->save();

        // Dispatch simulated email
        $subject = "Secured Account Approved - Access Credentials Inside";
        
        // Determine protocol and server name dynamically
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $loginUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/login";
        
        $body = "
            <h2>Congratulations! Your Wires4 Account is Approved</h2>
            <p>Our security department has validated your registration. Below are your secure credentials. Please log in immediately and set up your Multi-Factor Authentication.</p>
            <div style='background: #111; border: 1px solid #333; padding: 15px; border-radius: 4px; font-family: monospace; color: #fff; margin: 15px 0;'>
                <strong>Secure Login Portal:</strong> <a href='{$loginUrl}' style='color: #b9ff3a;'>{$loginUrl}</a><br>
                <strong>Your Login ID:</strong> <span style='color: #b9ff3a; font-size: 16px; font-weight: bold;'>{$loginId}</span><br>
                <strong>Password:</strong> Use the secure password you established during your registration/enrollment.
            </div>
            <p style='color: #f39c12; font-weight: bold;'>Security Notice: You will be forced to activate Time-Based One-Time Password (TOTP) 2FA using your authenticator app upon initial sign-in.</p>
            <p>Do not share these credentials with anyone. Wires4 administrators will never ask for your password.</p>
        ";

        $this->simulateEmail($user->email, $subject, $body);

        Session::flash('success', "Account for {$user->name} was successfully approved. Credentials sent to simulated mailbox.");
        $this->back();
    }

    /**
     * Suspend User Account.
     */
    public function suspendUser(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }
        
        if ($user->role === 'admin') {
            Session::flash('error', 'Cannot suspend admin accounts.');
            $this->back();
            return;
        }

        $user->status = 'suspended';
        $user->save();

        Session::flash('success', "Account for {$user->name} has been suspended.");
        $this->back();
    }

    /**
     * Activate Suspended User Account.
     */
    public function activateUser(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }
        
        if ($user->status !== 'suspended') {
            Session::flash('error', 'This user is not suspended.');
            $this->back();
            return;
        }

        $user->status = 'active';
        $user->save();

        Session::flash('success', "Account for {$user->name} has been reactivated.");
        $this->back();
    }

    public function viewUserProfile(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
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

        // Decode any pending profile updates
        $pendingUpdates = [];
        if ($user->pending_profile_update) {
            $pendingUpdates = json_decode($user->pending_profile_update, true) ?: [];
        }

        $this->render('admin/user_profile', [
            'user' => $user,
            'application' => $application,
            'pendingUpdates' => $pendingUpdates
        ], "Customer Specifications — {$user->name}");
    }

    /**
     * Authorize user to edit their profile details.
     */
    public function allowEditPermission(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $user->edit_permission_status = 'allowed';
        $user->save();

        Session::flash('success', "Edit permission successfully granted to {$user->name}.");
        $this->redirect("/admin/user/{$user->id}/profile");
    }

    /**
     * Deny user's request to edit their profile details.
     */
    public function denyEditPermission(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $user->edit_permission_status = 'none';
        $user->save();

        Session::flash('success', "Edit permission request from {$user->name} has been denied.");
        $this->redirect("/admin/user/{$user->id}/profile");
    }

    /**
     * Approve pending profile updates and merge them into the live application record.
     */
    public function approveProfileUpdates(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        if (!$user->pending_profile_update) {
            Session::flash('error', "No pending updates found for this profile.");
            $this->redirect("/admin/user/{$user->id}/profile");
            return;
        }

        $updates = json_decode($user->pending_profile_update, true);
        if (empty($updates)) {
            Session::flash('error', "Invalid updates payload.");
            $this->redirect("/admin/user/{$user->id}/profile");
            return;
        }

        // Apply changes to user_applications
        $db = \App\Core\Database::getConnection();
        $setClauses = [];
        $params = [];
        foreach ($updates as $column => $value) {
            $setClauses[] = "`$column` = :$column";
            $params[":$column"] = $value;
        }
        $params[':email'] = $user->email;

        $sql = "UPDATE user_applications SET " . implode(', ', $setClauses) . " WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Also synchronize main user properties if they changed
        $first_name = $updates['first_name'] ?? null;
        $last_name = $updates['last_name'] ?? null;
        if ($first_name || $last_name) {
            $stmtApp = $db->prepare("SELECT first_name, last_name FROM user_applications WHERE email = :email LIMIT 1");
            $stmtApp->execute([':email' => $user->email]);
            $application = $stmtApp->fetch();
            $user->name = trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? ''));
            if (empty($user->name)) {
                $user->name = 'Trader';
            }
        }

        if (isset($updates['wallet_address'])) {
            $user->wallet_address = $updates['wallet_address'];
        }
        if (isset($updates['network_type'])) {
            $user->network_type = $updates['network_type'];
        }

        // Reset permission state and clear the buffer
        $user->edit_permission_status = 'none';
        $user->pending_profile_update = null;
        $user->requested_documents = null;
        $user->save();

        Session::flash('success', "Profile updates for {$user->name} have been approved and integrated successfully.");
        $this->redirect("/admin/user/{$user->id}/profile");
    }

    /**
     * Reject pending profile updates.
     */
    public function rejectProfileUpdates(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $user->edit_permission_status = 'none';
        $user->pending_profile_update = null;
        $user->save();

        Session::flash('success', "Profile updates for {$user->name} have been rejected.");
        $this->redirect("/admin/user/{$user->id}/profile");
    }

    /**
     * Approve USDT Sell Request, deduct user balance, and dispatch a simulated email.
     */
    public function approveSellRequest(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $request = \App\Models\USDTSellRequest::find((int)$id);
        if (!$request) {
            http_response_code(404);
            die("USDT Sell Request not found.");
        }

        if ($request->status !== 'pending') {
            Session::flash('error', 'This sell request is already processed.');
            $this->back();
            return;
        }

        $user = User::find($request->user_id);
        if (!$user) {
            Session::flash('error', 'The user associated with this request could not be found.');
            $this->back();
            return;
        }

        // Check if user still has enough balance
        if ($user->usdt_balance < $request->usdt_amount) {
            Session::flash('error', "User has insufficient USDT balance (" . number_format($user->usdt_balance, 2) . " USDT) to approve this request of " . number_format($request->usdt_amount, 2) . " USDT.");
            $this->back();
            return;
        }

        // Process request
        $request->status = 'approved';
        $request->save();

        // Deduct from user wallet
        $user->usdt_balance -= $request->usdt_amount;
        $user->save();

        // Dispatch simulated email notification
        $subject = "USDT Sell Request Approved - Fiat Transferred & Wallet Deducted";
        $body = "
            <h2>Your USDT Sell Request is Approved!</h2>
            <p>Dear {$user->name},</p>
            <p>Our compliance desk has verified your USDT transfer to Wires4 settlement address (<strong>{$request->platform_wallet_address}</strong>).</p>
            <p>Accordingly, the sold amount of USDT has been deducted from your wallet and a USD fiat wire has been initiated to your designated bank account:</p>
            <div style='background: #111; border: 1px solid #333; padding: 15px; border-radius: 4px; font-family: monospace; color: #fff; margin: 15px 0;'>
                <strong>Sold Amount:</strong> <span style='color: #ff3a3a; font-size: 18px; font-weight: bold;'>-" . number_format($request->usdt_amount, 2) . " USDT</span><br>
                <strong>Remaining Wallet Balance:</strong> " . number_format($user->usdt_balance, 2) . " USDT<br>
                <strong>Receiving Bank Name:</strong> {$request->bank_name}<br>
                <strong>Receiving Bank Account:</strong> {$request->bank_account_number}<br>
                <strong>Account Holder Name:</strong> {$request->bank_account_holder}
            </div>
            <p>Please allow 1-3 business days for institutional wire clearing. You can track this transfer via your client dashboard.</p>
            <p>Thank you for choosing Wires4.</p>
        ";

        $this->simulateEmail($user->email, $subject, $body);

        Session::flash('success', "USDT sell request for " . number_format($request->usdt_amount, 2) . " USDT has been approved and deducted from {$user->name}'s wallet.");
        $this->back();
    }

    /**
     * Reject USDT Sell Request.
     */
    public function rejectSellRequest(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $request = \App\Models\USDTSellRequest::find((int)$id);
        if (!$request) {
            http_response_code(404);
            die("USDT Sell Request not found.");
        }

        if ($request->status !== 'pending') {
            Session::flash('error', 'This sell request is already processed.');
            $this->back();
            return;
        }

        // Process request
        $request->status = 'rejected';
        $request->save();

        $user = User::find($request->user_id);
        if ($user) {
            // Dispatch simulated email notification
            $subject = "USDT Sell Request Rejected";
            $body = "
                <h2>USDT Sell Request Rejection</h2>
                <p>Dear {$user->name},</p>
                <p>Your USDT sell request for " . number_format($request->usdt_amount, 2) . " USDT has been rejected by our compliance department.</p>
                <p>No funds were deducted from your secure settlement wallet. Please verify that your registered receiving bank coordinates are correct and active.</p>
                <p>If you believe this was in error, please contact your account officer or support@wiresforusdt.com.</p>
            ";
            $this->simulateEmail($user->email, $subject, $body);
        }

        Session::flash('success', "USDT sell request for " . number_format($request->usdt_amount, 2) . " USDT has been rejected.");
        $this->back();
    }

    /**
     * Show the Dropdown option CRUD management board.
     */
    public function showDropdownOptions(): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $activeKey = $_GET['key'] ?? 'countries';
        
        // Fetch all options under the active key
        $options = \App\Models\DropdownOption::findByKey($activeKey);

        // Defined group mappings for labels
        $groupLabels = [
            'countries' => 'Countries List',
            'entity_types' => 'Entity Types',
            'funding_sources' => 'Source of Funding',
            'business_natures' => 'Nature of Business',
            'occupations' => 'Title/Occupation',
            'trading_purposes' => 'Trading Purposes',
            'funds_flows' => 'Flow of Funds',
            'currencies' => 'Currency List',
            'network_types' => 'Network Protocols',
            'referral_sources' => 'Referral Sources'
        ];

        $this->render('admin/dropdowns', [
            'currentUser' => $currentUser,
            'activeKey' => $activeKey,
            'options' => $options,
            'groupLabels' => $groupLabels
        ], 'Manage Onboarding Dropdowns');
    }

    /**
     * Add a new option to a dropdown group.
     */
    public function addDropdownOption(): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $key = trim($_POST['dropdown_key'] ?? '');
        $value = trim($_POST['option_value'] ?? '');

        if (empty($key) || empty($value)) {
            Session::flash('error', 'All fields are required.');
            $this->back();
            return;
        }

        $option = new \App\Models\DropdownOption();
        $option->dropdown_key = $key;
        $option->option_value = $value;

        if ($option->save()) {
            Session::flash('success', "Option '{$value}' successfully added to group.");
            $this->redirect("/admin/dropdowns?key=" . urlencode($key));
        } else {
            Session::flash('error', 'An error occurred while saving the option.');
            $this->back();
        }
    }

    /**
     * Delete a dropdown option.
     */
    public function deleteDropdownOption(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $option = \App\Models\DropdownOption::find((int)$id);
        if (!$option) {
            http_response_code(404);
            die("Option not found.");
        }

        $key = $option->dropdown_key;
        if ($option->delete()) {
            Session::flash('success', "Option '{$option->option_value}' has been deleted.");
            $this->redirect("/admin/dropdowns?key=" . urlencode($key));
        } else {
            Session::flash('error', 'An error occurred while deleting the option.');
            $this->back();
        }
    }

    /**
     * Submit SDM Selfie upload link and email it to the user.
     */
    public function submitSdmSelfie(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $link = trim($_POST['sdm_selfie_link'] ?? '');
        if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
            Session::flash('error', 'Please enter a valid selfie verification upload URL.');
            $this->back();
            return;
        }

        $user->sdm_selfie_link = $link;
        if ($user->save()) {
            // Send email
            $subject = "Selfie Verification Required - Wires4 Compliance";
            $body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <h2 style='color: #060b0d;'>Selfie Verification Required</h2>
                    <p>Dear {$user->name},</p>
                    <p>To comply with security and regulatory standards, our compliance department requires a selfie verification.</p>
                    <p>Please click the secure link below to upload your verification selfie:</p>
                    <div style='background: #111; border: 1px solid #333; padding: 15px; border-radius: 4px; font-family: monospace; color: #fff; margin: 15px 0;'>
                        <strong>Secure Upload Link:</strong> <a href='{$link}' style='color: #b9ff3a; font-weight: bold;'>{$link}</a>
                    </div>
                    <p>This verification link is also visible on your secure client dashboard portal.</p>
                    <p>Thank you for your cooperation,</p>
                    <p><strong>Wires4 Compliance Desk</strong></p>
                </div>
            ";
            $this->simulateEmail($user->email, $subject, $body);

            Session::flash('success', "Selfie verification link successfully updated and sent to {$user->name}.");
        } else {
            Session::flash('error', "Failed to update selfie verification link.");
        }
        $this->back();
    }

    /**
     * Request additional documents and email the list to the user.
     */
    public function requestDocuments(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $docs = trim($_POST['requested_documents'] ?? '');
        if (empty($docs)) {
            Session::flash('error', 'Please specify the list of required documents.');
            $this->back();
            return;
        }

        $user->requested_documents = $docs;
        $user->edit_permission_status = 'allowed';
        if ($user->save()) {
            // Send email
            $subject = "Additional Documentation Required - Wires4 Compliance";
            $body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <h2 style='color: #060b0d;'>Additional Documentation Required</h2>
                    <p>Dear {$user->name},</p>
                    <p>Our compliance department has requested additional documentation to finalize your profile specifications.</p>
                    <p>Please send or upload the following documents:</p>
                    <div style='background: #fdf6ec; border-left: 4px solid #f39c12; padding: 15px; border-radius: 4px; margin: 15px 0; color: #664d03; font-weight: bold;'>
                        <pre style='margin: 0; font-family: inherit; white-space: pre-wrap;'>{$docs}</pre>
                    </div>
                    <p>You can upload these documents inside your secure client dashboard under <strong>Profile & Permissions</strong>, or send them as a response to this email support inbox.</p>
                    <p>Thank you for your cooperation,</p>
                    <p><strong>Wires4 Compliance Desk</strong></p>
                </div>
            ";
            $this->simulateEmail($user->email, $subject, $body);

            Session::flash('success', "Document request successfully updated and sent to {$user->name}.");
        } else {
            Session::flash('error', "Failed to update document request.");
        }
        $this->back();
    }

    /**
     * Update Buy USDT Bank wire instructions details and optional PDF document.
     */
    public function updateBuyBankDetails(string $id): void
    {
        $currentUser = Session::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            http_response_code(403);
            die("Unauthorized access.");
        }

        $user = User::find((int)$id);
        if (!$user) {
            http_response_code(404);
            die("User not found.");
        }

        $bankName = trim($_POST['buy_usdt_bank_name'] ?? '');
        $bankAddress = trim($_POST['buy_usdt_bank_address'] ?? '');
        $routingNo = trim($_POST['buy_usdt_routing_no'] ?? '');
        $accountNo = trim($_POST['buy_usdt_account_no'] ?? '');
        $beneficiary = trim($_POST['buy_usdt_beneficiary'] ?? '');

        $pdfFilename = $user->buy_usdt_bank_pdf;
        if (!empty($_FILES['buy_usdt_bank_pdf']) && $_FILES['buy_usdt_bank_pdf']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['buy_usdt_bank_pdf'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                Session::flash('error', 'Only PDF files are allowed for wire instructions.');
                $this->back();
                return;
            }
            $pdfFilename = 'buy_bank_pdf_' . uniqid() . '.pdf';
            $uploadDir = __DIR__ . '/../../storage/app/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            move_uploaded_file($file['tmp_name'], $uploadDir . $pdfFilename);
        }

        $user->buy_usdt_bank_name = !empty($bankName) ? $bankName : null;
        $user->buy_usdt_bank_address = !empty($bankAddress) ? $bankAddress : null;
        $user->buy_usdt_routing_no = !empty($routingNo) ? $routingNo : null;
        $user->buy_usdt_account_no = !empty($accountNo) ? $accountNo : null;
        $user->buy_usdt_beneficiary = !empty($beneficiary) ? $beneficiary : null;
        $user->buy_usdt_bank_pdf = $pdfFilename;

        if ($user->save()) {
            Session::flash('success', "Buy USDT bank wire details successfully updated for {$user->name}.");
        } else {
            Session::flash('error', "Failed to update bank wire details.");
        }
        $this->back();
    }
}
