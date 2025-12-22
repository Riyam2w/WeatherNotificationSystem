<?php
// app/core/Controller.php
declare(strict_types=1);

class Controller {
    protected function view(string $path, array $data = []) {
        extract($data);
        require __DIR__ . "/../views/$path.php";
    }
}
