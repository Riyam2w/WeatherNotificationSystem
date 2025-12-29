<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/ApiValidator.php';

class LocationController
{
    private string $basePath;

    public function __construct()
    {
        $this->basePath = __DIR__ . '/../data/';
    }

    public function index(): void
    {
        header('Content-Type: application/json');

        $type = $_GET['type'] ?? '';

        switch ($type) {

            case 'countries':
                $this->respondFile('countries.json');
                break;

            case 'states':
                $country = $_GET['country'] ?? '';
                $this->requireParam('country', $country);
                $this->filterJson(
                    'states.json',
                    fn($s) => $s['country_code'] === $country
                );
                break;

            case 'cities':
                $country = $_GET['country'] ?? '';
                $state   = $_GET['state'] ?? '';

                $this->requireParam('country', $country);
                $this->requireParam('state', $state);

                $this->filterJson(
                    'cities.json',
                    fn($c) =>
                        $c['country_code'] === $country &&
                        $c['state_code'] === $state
                );
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid location type']);
        }
    }

    /* -------------------------
       Helpers
    ------------------------- */

    private function respondFile(string $file): void
    {
        $path = $this->basePath . $file;

        if (!file_exists($path)) {
            http_response_code(500);
            echo json_encode(['error' => 'Data source missing']);
            return;
        }

        echo file_get_contents($path);
    }

    private function filterJson(string $file, callable $filter): void
    {
        $path = $this->basePath . $file;

        if (!file_exists($path)) {
            http_response_code(500);
            echo json_encode(['error' => 'Data source missing']);
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        echo json_encode(array_values(array_filter($data, $filter)));
    }

    private function requireParam(string $name, string $value): void
    {
        if ($value === '' || strlen($value) > 10) {
            http_response_code(422);
            echo json_encode([
                'error' => ucfirst($name) . ' is required'
            ]);
            exit;
        }
    }
}
