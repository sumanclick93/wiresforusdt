<?php

namespace App\Core;

use App\Models\User;

class Session
{
    /**
     * Start the PHP session securely if not already started.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            
            // Set secure cookie if HTTPS is enabled
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            session_start();
        }

        // Initialize flash storage if missing
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        // Shift old inputs
        if (isset($_SESSION['_old_new'])) {
            $_SESSION['_old'] = $_SESSION['_old_new'];
            unset($_SESSION['_old_new']);
        } else {
            unset($_SESSION['_old']);
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session has key.
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Forget a session key.
     */
    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Flash a success or error message for the next request.
     */
    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Check if a flash key exists.
     */
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Get a flash value and clear it immediately.
     */
    public static function getFlash(string $key, $default = null)
    {
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
        return $default;
    }

    /**
     * Flash input values for validation errors.
     */
    public static function flashInput(array $input): void
    {
        $_SESSION['_old_new'] = $input;
    }

    /**
     * Get old input value.
     */
    public static function old(string $key, string $default = ''): string
    {
        return $_SESSION['_old'][$key] ?? $default;
    }

    /**
     * Authenticate a user.
     */
    public static function login(User $user): void
    {
        self::set('user_id', $user->id);
    }

    /**
     * Clear authentication state and destroy session.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Get currently logged-in user model.
     */
    public static function user(): ?User
    {
        $userId = self::get('user_id');
        if ($userId) {
            return User::find($userId);
        }
        return null;
    }

    /**
     * Check if user is logged in.
     */
    public static function check(): bool
    {
        return self::has('user_id');
    }

    /**
     * Generate or fetch CSRF validation token.
     */
    public static function csrfToken(): string
    {
        $token = self::get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            self::set('csrf_token', $token);
        }
        return $token;
    }

    /**
     * Verify the POSTed CSRF token against session token.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        $sessionToken = self::get('csrf_token');
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }
}
