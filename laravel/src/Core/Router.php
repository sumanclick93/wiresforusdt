<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $middlewareMap = [];

    public function __construct()
    {
        // Define middleware logic mapping
        $this->middlewareMap = [
            'auth' => function () {
                if (!Session::check()) {
                    Session::flash('error', 'Authentication required.');
                    $this->redirect('/login');
                }
            },
            'gated2fa' => function () {
                $user = Session::user();
                if ($user) {
                    // Check user status
                    if ($user->status === 'pending_review') {
                        Session::logout();
                        Session::flash('error', 'Your account is pending administrator review and approval.');
                        $this->redirect('/login');
                    }

                    if ($user->status === 'suspended') {
                        Session::logout();
                        Session::flash('error', 'Your account has been suspended.');
                        $this->redirect('/login');
                    }

                    // Admin is exempt from standard client 2FA gates
                    if ($user->role === 'admin') {
                        return;
                    }

                    // Clean URI to resolve subdirectories correctly
                    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
                    $scriptDir = dirname($scriptName);
                    if ($scriptDir !== '/' && $scriptDir !== '\\') {
                        if (str_starts_with($currentUri, $scriptDir)) {
                            $currentUri = substr($currentUri, strlen($scriptDir));
                        } else {
                            $parentDir = dirname($scriptDir);
                            if ($parentDir !== '/' && $parentDir !== '\\' && str_starts_with($currentUri, $parentDir)) {
                                $currentUri = substr($currentUri, strlen($parentDir));
                            }
                        }
                    }

                    // If 2FA not enabled, force redirection to setup-2fa (unless skipped for current session)
                    if (!$user->google2fa_enabled) {
                        if (!Session::get('skip_2fa_for_now')) {
                            if ($currentUri !== '/setup-2fa' && $currentUri !== '/logout') {
                                $this->redirect('/setup-2fa');
                            }
                        }
                    } else {
                        // If 2FA enabled, check challenge completed
                        if (!Session::has('totp_authenticated')) {
                            if ($currentUri !== '/login/2fa' && $currentUri !== '/logout' && $currentUri !== '/setup-2fa') {
                                $this->redirect('/login/2fa');
                            }
                        }
                    }
                } else {
                    $this->redirect('/login');
                }
            }
        ];
    }

    /**
     * Add a GET route.
     */
    public function get(string $path, string $action, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $action, $middlewares);
    }

    /**
     * Add a POST route.
     */
    public function post(string $path, string $action, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $action, $middlewares);
    }

    /**
     * Store route mapping.
     */
    protected function addRoute(string $method, string $path, string $action, array $middlewares): void
    {
        // Convert Laravel-style route parameters e.g., /admin/approve/{id} to regex e.g. /admin/approve/([^/]+)
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Dispatch incoming request to registered controller.
     */
    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Dynamically strip base directory prefix if running inside a subdirectory
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/' && $scriptDir !== '\\') {
            if (str_starts_with($path, $scriptDir)) {
                $path = substr($path, strlen($scriptDir));
            } else {
                $parentDir = dirname($scriptDir);
                if ($parentDir !== '/' && $parentDir !== '\\' && str_starts_with($path, $parentDir)) {
                    $path = substr($path, strlen($parentDir));
                }
            }
        }

        // Ensure path starts with a slash
        if (empty($path)) {
            $path = '/';
        } elseif (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // CSRF Verification for all write methods
        if ($method === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Session::verifyCsrfToken($token)) {
                http_response_code(419);
                echo "<h2>419 Page Expired - CSRF Token Mismatch</h2><p><a href='javascript:history.back()'>Go Back</a></p>";
                return;
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // Remove the first full match
                array_shift($matches);

                // Run associated route middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if (isset($this->middlewareMap[$middleware])) {
                        call_user_func($this->middlewareMap[$middleware]);
                    }
                }

                // Resolve Controller and Action
                list($controllerClass, $actionMethod) = explode('@', $route['action']);
                
                // Prepend full namespace
                $controllerClass = "App\\Controllers\\" . $controllerClass;

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $actionMethod)) {
                        call_user_func_array([$controllerInstance, $actionMethod], $matches);
                        return;
                    }
                }
            }
        }

        // Route Not Found
        http_response_code(404);
        $this->render404();
    }

    /**
     * Render a standard secure 404 template.
     */
    protected function render404(): void
    {
        $cssUrl = url('/css/landing.css');
        $homeUrl = url('/');
        $requestUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '');
        $scriptName = htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = htmlspecialchars(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $originalPath = htmlspecialchars($path);
        
        if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }
        if (empty($path)) {
            $path = '/';
        } elseif (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        $routedPath = htmlspecialchars($path);
        $method = htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? '');

        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 Not Found - Wires4</title>
            <link rel='stylesheet' href='{$cssUrl}'>
            <style>
                body { background: #060b0d; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
                h1 { color: #b9ff3a; font-size: 80px; margin: 0; letter-spacing: -2px; }
                p { color: #7f8c8d; margin-bottom: 24px; }
                .btn { background: #b9ff3a; color: #000; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 30px; }
                .debug-box { margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.05); border: 1px dashed #b9ff3a; border-radius: 8px; font-family: monospace; text-align: left; max-width: 600px; font-size: 12px; color: #ccc; }
            </style>
        </head>
        <body>
            <div>
                <h1>404</h1>
                <p>The requested secure resource could not be located.</p>
                <a href='{$homeUrl}' class='btn'>Return Home</a>
                
                <div class='debug-box'>
                    <strong>Routing Debug Info:</strong><br>
                    Request Method: {$method}<br>
                    Request URI: {$requestUri}<br>
                    Original Path: {$originalPath}<br>
                    Script Name: {$scriptName}<br>
                    Script Dir: {$scriptDir}<br>
                    Routed Path: {$routedPath}<br>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Helper to perform redirects.
     */
    protected function redirect(string $url): void
    {
        if (!preg_match('#^https?://#i', $url)) {
            $url = url($url);
        }
        header("Location: $url");
        exit;
    }
}
