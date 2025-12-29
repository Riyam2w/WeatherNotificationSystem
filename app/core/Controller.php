<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'main'
    ): void {
        extract($data, EXTR_SKIP);

        $viewFile   = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$view}");
        }

        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout not found: {$layout}.php");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
