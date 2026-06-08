<?php

namespace App\Controllers;

abstract class Controller
{
    /**
     * Render view wrapped in layout or standalone.
     */
    protected function render(string $view, array $data = [], string $title = 'Secure Portal'): void
    {
        // Extract data keys to variable names
        extract($data);

        // Start output buffering
        ob_start();
        
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View file $view not found at $viewFile");
        }

        $content = ob_get_clean();

        // Check if view is welcome (standalone) or needs layout
        if ($view === 'welcome') {
            echo $content;
        } else {
            $layoutFile = __DIR__ . '/../Views/layouts/auth.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content; // Fallback
            }
        }
    }

    /**
     * Redirect helper.
     */
    protected function redirect(string $url): void
    {
        if (!preg_match('#^https?://#i', $url)) {
            $url = url($url);
        }
        header("Location: $url");
        exit;
    }

    /**
     * Redirect back to previous page.
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
}
