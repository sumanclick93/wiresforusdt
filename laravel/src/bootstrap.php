<?php

// Set timezone
date_default_timezone_set('UTC');

// Error reporting settings based on environment
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Register custom PSR-4 autoloader
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';
    
    // Check if class uses namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get relative class name
    $relativeClass = substr($class, $len);
    
    // Map namespace separator to directory separator and append .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load configuration properties
\App\Core\Config::load(__DIR__ . '/../.env');

// Boot secure session
\App\Core\Session::start();

/**
 * Global helper function to get CSRF token in view scripts.
 */
function csrf_token(): string
{
    return \App\Core\Session::csrfToken();
}

/**
 * Global helper function to output CSRF field in html forms.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/**
 * Global helper function to get old form input value.
 */
function old(string $key, string $default = ''): string
{
    return \App\Core\Session::old($key, $default);
}

function url(string $path): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = dirname($scriptName);
    
    $base = '';
    if ($scriptDir !== '/' && $scriptDir !== '\\') {
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (str_starts_with($requestUri, $scriptDir)) {
            $base = $scriptDir;
        } else {
            $parentDir = dirname($scriptDir);
            if (str_starts_with($requestUri, $parentDir)) {
                $base = ($parentDir === '/' || $parentDir === '\\') ? '' : $parentDir;
            } else {
                $base = $scriptDir; // Fallback
            }
        }
    }
    
    return $base . '/' . ltrim($path, '/');
}

/**
 * Global helper to access session instance.
 */
function session()
{
    return new class {
        public function get($key, $default = null) { return \App\Core\Session::get($key, $default); }
        public function has($key) { return \App\Core\Session::has($key); }
        public function getFlash($key, $default = null) { return \App\Core\Session::getFlash($key, $default); }
        public function hasFlash($key) { return \App\Core\Session::hasFlash($key); }
    };
}

/**
 * Check active authenticated user.
 */
function auth()
{
    return new class {
        public function check() { return \App\Core\Session::check(); }
        public function user() { return \App\Core\Session::user(); }
    };
}
