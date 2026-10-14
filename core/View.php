<?php
declare(strict_types=1);

namespace Core;

use Exception;

class View {
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string {
        $viewsPath = dirname(__DIR__) . '/views/';
        $templatePath = $viewsPath . ltrim($template, '/') . '.php';

        if (!file_exists($templatePath)) {
            throw new Exception("View template file not found: {$templatePath}");
        }

        // Extract variables to view scope
        extract($data, EXTR_SKIP);

        // Capture template content
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        // If no layout is requested, return inner content
        if ($layout === null) {
            return $content;
        }

        $layoutPath = $viewsPath . ltrim($layout, '/') . '.php';
        if (!file_exists($layoutPath)) {
            throw new Exception("View layout file not found: {$layoutPath}");
        }

        // Layout has access to $content and all $data
        ob_start();
        require $layoutPath;
        return ob_get_clean();
    }
}
