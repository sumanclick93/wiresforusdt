<?php

namespace App\Core;

class Config
{
    private static $loaded = false;
    private static $vars = [];

    /**
     * Load environment variables from .env file.
     *
     * @param string $path Path to .env file
     * @return void
     */
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip comments
                if (str_starts_with($line, '#') || str_starts_with($line, ';')) {
                    continue;
                }

                // Parse key=value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove surrounding quotes
                    if (preg_match('/^"([^"]*)"$/', $value, $matches) || preg_match('/^\'([^\']*)\'$/', $value, $matches)) {
                        $value = $matches[1];
                    }

                    // Handle boolean-like values
                    $lowerVal = strtolower($value);
                    if ($lowerVal === 'true') {
                        $value = true;
                    } elseif ($lowerVal === 'false') {
                        $value = false;
                    } elseif ($lowerVal === 'null') {
                        $value = null;
                    }

                    self::$vars[$key] = $value;
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable value or fallback to default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load(__DIR__ . '/../../.env');
        }
        return self::$vars[$key] ?? $default;
    }
}
