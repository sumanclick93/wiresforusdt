<?php

namespace App\Helpers;

use App\Core\Config;

class MailHelper
{
    /**
     * Send an email using SMTP credentials from .env.
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        $host = Config::get('MAIL_HOST', 'localhost');
        $port = (int) Config::get('MAIL_PORT', 25);
        $username = Config::get('MAIL_USERNAME', '');
        $password = Config::get('MAIL_PASSWORD', '');
        $encryption = strtolower(Config::get('MAIL_ENCRYPTION', ''));
        $fromEmail = Config::get('MAIL_FROM_ADDRESS', 'support@wiresforusdt.com');
        $fromName = Config::get('MAIL_FROM_NAME', 'Wires4');

        // Determine protocol
        $protocol = '';
        if ($encryption === 'ssl') {
            $protocol = 'ssl://';
        }

        // Create context to bypass SSL certificate validation failures (common in local/hosting environments)
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        // Open socket connection with timeout
        $socket = @stream_socket_client(
            $protocol . $host . ':' . $port,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            error_log("SMTP Connection failed: $errstr ($errno)");
            return false;
        }

        // Read greeting
        self::readResponse($socket);

        // Extract host without port for EHLO greeting
        $ehloHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (($pos = strpos($ehloHost, ':')) !== false) {
            $ehloHost = substr($ehloHost, 0, $pos);
        }

        // HELO/EHLO greeting
        self::sendCommand($socket, "EHLO " . $ehloHost);
        self::readResponse($socket);

        // STARTTLS if encryption is tls (port 587)
        if ($encryption === 'tls') {
            self::sendCommand($socket, "STARTTLS");
            $response = self::readResponse($socket);
            if (str_starts_with($response, '220') || str_contains($response, '220')) {
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    error_log("SMTP STARTTLS handshake failed");
                    fclose($socket);
                    return false;
                }
                // Send EHLO again after STARTTLS
                self::sendCommand($socket, "EHLO " . $ehloHost);
                self::readResponse($socket);
            }
        }

        // Authentication
        if (!empty($username) && !empty($password)) {
            self::sendCommand($socket, "AUTH LOGIN");
            self::readResponse($socket);

            self::sendCommand($socket, base64_encode($username));
            self::readResponse($socket);

            self::sendCommand($socket, base64_encode($password));
            $authResponse = self::readResponse($socket);
            if (!str_contains($authResponse, '235')) {
                error_log("SMTP Authentication failed: " . $authResponse);
                fclose($socket);
                return false;
            }
        }

        // MAIL FROM
        self::sendCommand($socket, "MAIL FROM:<$fromEmail>");
        self::readResponse($socket);

        // RCPT TO
        self::sendCommand($socket, "RCPT TO:<$to>");
        self::readResponse($socket);

        // DATA
        self::sendCommand($socket, "DATA");
        self::readResponse($socket);

        // Headers and Body
        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>",
            "To: $to",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Date: " . date('r'),
            "Message-ID: <" . uniqid('', true) . "@" . $host . ">",
        ];

        // Normalize line endings in the body to CRLF (\r\n) for RFC-compliant SMTP transport
        $body = str_replace(["\r\n", "\r", "\n"], "\r\n", $body);

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        self::sendCommand($socket, $message);
        $dataResponse = self::readResponse($socket);

        // QUIT
        self::sendCommand($socket, "QUIT");
        fclose($socket);

        return str_contains($dataResponse, '250') || str_contains($dataResponse, '200');
    }

    private static function sendCommand($socket, string $command): void
    {
        fwrite($socket, $command . "\r\n");
    }

    private static function readResponse($socket): string
    {
        $response = '';
        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return trim($response);
    }
}
