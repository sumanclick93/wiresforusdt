<?php
header('Content-Type: text/plain; charset=UTF-8');

echo "=== SERVER DIAGNOSTICS & LOGS ===\n\n";

require_once __DIR__ . '/../src/bootstrap.php';

// Interactive Verbose SMTP Test
if (!empty($_GET['locate_uploads'])) {
    echo "=== LOCATING UPLOADED FILES ON SERVER ===\n\n";
    $searchFilename = 'kyc_document_6a1d3a01eca45.png';
    $searchRoot = realpath(__DIR__ . '/../');
    echo "Searching for '$searchFilename' in '$searchRoot'...\n";
    
    function findFile($dir, $filename) {
        $found = [];
        if (!is_dir($dir)) return $found;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $found = array_merge($found, findFile($path, $filename));
            } else if ($item === $filename) {
                $found[] = $path;
            }
        }
        return $found;
    }
    
    $results = findFile($searchRoot, $searchFilename);
    if (empty($results)) {
        echo "File '$searchFilename' NOT found in '$searchRoot'.\n";
    } else {
        echo "FOUND file at paths:\n";
        foreach ($results as $r) {
            echo " - $r\n";
        }
    }

    // Try to create the uploads directory and check permissions
    $storageApp = $searchRoot . '/storage/app';
    $uploadsPath = $storageApp . '/uploads';
    echo "\n=== STORAGE APP DIRECTORY DIAGNOSTICS ===\n";
    echo "storage/app path: $storageApp\n";
    echo "storage/app exists: " . (is_dir($storageApp) ? 'Yes' : 'No') . "\n";
    echo "storage/app writable: " . (is_writable($storageApp) ? 'Yes' : 'No') . "\n";
    echo "storage/app permissions: " . substr(sprintf('%o', fileperms($storageApp)), -4) . "\n";
    
    echo "\nTrying to create storage/app/uploads...\n";
    if (!is_dir($uploadsPath)) {
        $created = mkdir($uploadsPath, 0755, true);
        echo "mkdir result: " . ($created ? 'SUCCESS' : 'FAILED') . "\n";
    } else {
        echo "storage/app/uploads already exists.\n";
    }
    
    echo "storage/app/uploads exists: " . (is_dir($uploadsPath) ? 'Yes' : 'No') . "\n";
    echo "storage/app/uploads writable: " . (is_writable($uploadsPath) ? 'Yes' : 'No') . "\n";
    if (is_dir($uploadsPath)) {
        echo "storage/app/uploads permissions: " . substr(sprintf('%o', fileperms($uploadsPath)), -4) . "\n";
    }
    
    echo "\nListing contents of $storageApp:\n";
    if (is_dir($storageApp)) {
        $items = scandir($storageApp);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $p = $storageApp . '/' . $item;
            $type = is_dir($p) ? 'DIR' : 'FILE';
            $perms = substr(sprintf('%o', fileperms($p)), -4);
            echo " - $item [$type] (perms: $perms)\n";
        }
    }
    
    echo "\n=== END OF LOCATOR ===\n\n";
    exit;
}

if (!empty($_GET['test_email'])) {
    $to = $_GET['test_email'];
    echo "=== RUNNING VERBOSE SMTP TEST TO: $to ===\n\n";
    
    $host = \App\Core\Config::get('MAIL_HOST', 'localhost');
    $port = (int) \App\Core\Config::get('MAIL_PORT', 25);
    $username = \App\Core\Config::get('MAIL_USERNAME', '');
    $password = \App\Core\Config::get('MAIL_PASSWORD', '');
    $encryption = strtolower(\App\Core\Config::get('MAIL_ENCRYPTION', ''));
    $fromEmail = \App\Core\Config::get('MAIL_FROM_ADDRESS', 'support@wiresforusdt.com');
    $fromName = \App\Core\Config::get('MAIL_FROM_NAME', 'Wires4');

    echo "SMTP Configuration:\n";
    echo " - Host: $host\n";
    echo " - Port: $port\n";
    echo " - Username: $username\n";
    echo " - From Email: $fromEmail\n";
    echo " - Encryption: $encryption\n\n";

    $protocol = ($encryption === 'ssl') ? 'ssl://' : '';
    
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    echo "Connecting to socket: " . $protocol . $host . ':' . $port . " ...\n";
    $socket = @stream_socket_client(
        $protocol . $host . ':' . $port,
        $errno,
        $errstr,
        15,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$socket) {
        echo "FAILED: Connection could not be established. Error: $errstr ($errno)\n";
        exit;
    }
    echo "CONNECTED successfully!\n\n";

    function readSMTP($socket) {
        $response = '';
        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            echo "< " . trim($line) . "\n";
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return trim($response);
    }

    function sendSMTP($socket, $cmd) {
        echo "> " . $cmd . "\n";
        fwrite($socket, $cmd . "\r\n");
    }

    readSMTP($socket);

    $ehloHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (($pos = strpos($ehloHost, ':')) !== false) {
        $ehloHost = substr($ehloHost, 0, $pos);
    }

    sendSMTP($socket, "EHLO " . $ehloHost);
    readSMTP($socket);

    if ($encryption === 'tls') {
        sendSMTP($socket, "STARTTLS");
        $response = readSMTP($socket);
        if (str_contains($response, '220')) {
            echo "Enabling TLS encryption...\n";
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                echo "FAILED: TLS handshake failed.\n";
                fclose($socket);
                exit;
            }
            echo "TLS handshake succeeded!\n";
            sendSMTP($socket, "EHLO " . $ehloHost);
            readSMTP($socket);
        }
    }

    if (!empty($username) && !empty($password)) {
        sendSMTP($socket, "AUTH LOGIN");
        readSMTP($socket);

        sendSMTP($socket, base64_encode($username));
        readSMTP($socket);

        sendSMTP($socket, base64_encode($password));
        $authResponse = readSMTP($socket);
        if (!str_contains($authResponse, '235')) {
            echo "FAILED: Authentication failed.\n";
            fclose($socket);
            exit;
        }
        echo "AUTHENTICATION successful!\n\n";
    }

    sendSMTP($socket, "MAIL FROM:<$fromEmail>");
    readSMTP($socket);

    sendSMTP($socket, "RCPT TO:<$to>");
    readSMTP($socket);

    sendSMTP($socket, "DATA");
    readSMTP($socket);

    $headers = [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>",
        "To: $to",
        "Subject: =?UTF-8?B?" . base64_encode("SMTP Verbose Connection Test") . "?=",
        "Date: " . date('r'),
        "Message-ID: <" . uniqid('', true) . "@" . $host . ">",
    ];

    $body = "<h2>SMTP Verbose connection test successful!</h2><p>This email confirms SMTP configuration is operational.</p>";
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    
    sendSMTP($socket, $message);
    $dataResponse = readSMTP($socket);

    sendSMTP($socket, "QUIT");
    fclose($socket);

    if (str_contains($dataResponse, '250') || str_contains($dataResponse, '200')) {
        echo "\n[SUCCESS] Test email accepted by SMTP server successfully!\n";
    } else {
        echo "\n[FAILED] SMTP server rejected the message body: $dataResponse\n";
    }
    
    echo "\n========================================\n\n";
}

// 1. Show database connection status
try {
    $db = \App\Core\Database::getConnection();
    echo "Database: Connected successfully.\n";
    
    // Fetch last 3 pending registrations
    $stmt = $db->query("SELECT email, created_at FROM pending_registrations ORDER BY id DESC LIMIT 3");
    $registrations = $stmt->fetchAll();
    echo "Recent registration requests database status:\n";
    if (empty($registrations)) {
        echo " - No registrations found in database.\n";
    } else {
        foreach ($registrations as $reg) {
            echo " - Email: {$reg->email} | Time: {$reg->created_at}\n";
        }
    }

    // Fetch last 3 users
    $stmtUsers = $db->query("SELECT email, role, status, created_at FROM users ORDER BY id DESC LIMIT 3");
    $users = $stmtUsers->fetchAll();
    echo "\nRecent users in database:\n";
    if (empty($users)) {
        echo " - No users found.\n";
    } else {
        foreach ($users as $u) {
            echo " - Email: {$u->email} | Status: {$u->status} | Role: {$u->role} | Time: {$u->created_at}\n";
        }
    }
} catch (\Exception $e) {
    echo "Database: Connection failed - " . $e->getMessage() . "\n";
}

echo "\n----------------------------------------\n";
echo "=== APPLICATION LOGS (storage/logs/laravel.log) ===\n\n";

$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last_lines = array_slice($lines, -40);
    echo implode("", $last_lines);
} else {
    echo "No laravel.log found at $logFile\n";
}

echo "\n----------------------------------------\n";
echo "=== SIMULATED EMAILS (storage/app/simulated_emails.json) ===\n\n";

$emailsFile = __DIR__ . '/../storage/app/simulated_emails.json';
if (file_exists($emailsFile)) {
    echo file_get_contents($emailsFile);
} else {
    echo "No simulated_emails.json found at $emailsFile\n";
}

echo "\n----------------------------------------\n";
echo "=== PHP SYSTEM ERROR LOG ===\n\n";

$systemLog = ini_get('error_log');
if ($systemLog && file_exists($systemLog)) {
    $lines = file($systemLog);
    $last_lines = array_slice($lines, -40);
    echo implode("", $last_lines);
} else {
    // Check for common cPanel local error logs in public/ and root/
    $localLog = __DIR__ . '/error_log';
    $parentLog = __DIR__ . '/../error_log';
    if (file_exists($localLog)) {
        echo "Found error log in public/ :\n";
        $lines = file($localLog);
        $last_lines = array_slice($lines, -40);
        echo implode("", $last_lines);
    } elseif (file_exists($parentLog)) {
        echo "Found error log in root/ :\n";
        $lines = file($parentLog);
        $last_lines = array_slice($lines, -40);
        echo implode("", $last_lines);
    } else {
        echo "No system php error log found at '$systemLog', '$localLog' or '$parentLog'\n";
    }
}
