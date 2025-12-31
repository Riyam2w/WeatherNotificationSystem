<?php
declare(strict_types=1);

class LocationController extends Controller
{
    private string $basePath;

    public function __construct()
    {
        $this->basePath = __DIR__ . '/../data/';
    }

    public function index(): void
    {
        $type = $_GET['type'] ?? '';

        switch ($type) {

            case 'countries':
                $this->respondFile('countries.json');
                return;

            case 'states':
                $country = $_GET['country'] ?? '';
                $this->requireParam('country', $country);

                $this->filterJson(
                    'states.json',
                    fn(array $s) => ($s['country_code'] ?? '') === $country
                );
                return;

            case 'cities':
                $country = $_GET['country'] ?? '';
                $state   = $_GET['state'] ?? '';

                $this->requireParam('country', $country);
                $this->requireParam('state', $state);

                $this->filterJson(
                    'cities.json',
                    fn(array $c) =>
                        ($c['country_code'] ?? '') === $country &&
                        ($c['state_code'] ?? '') === $state
                );
                return;

            default:
                $this->json([
                    'success' => false,
                    'errors'  => ['type' => 'Invalid location type']
                ], 400);
        }
    }

    /* -------------------------
       Helpers
    ------------------------- */

    private function respondFile(string $file): void
    {
        $path = $this->basePath . $file;

        if (!file_exists($path)) {
            $this->json([
                'success' => false,
                'errors'  => ['data' => 'Data source missing']
            ], 500);
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            $this->json([
                'success' => false,
                'errors'  => ['data' => 'Invalid data format']
            ], 500);
        }

        $this->json($data);
    }

    private function filterJson(string $file, callable $filter): void
    {
        $path = $this->basePath . $file;

        if (!file_exists($path)) {
            $this->json([
                'success' => false,
                'errors'  => ['data' => 'Data source missing']
            ], 500);
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            $this->json([
                'success' => false,
                'errors'  => ['data' => 'Invalid data format']
            ], 500);
        }

        $this->json(array_values(array_filter($data, $filter)));
    }

    private function requireParam(string $name, string $value): void
    {
        if ($value === '') {
            $this->json([
                'success' => false,
                'errors'  => [$name => ucfirst($name) . ' is required']
            ], 422);
        }
    }
}
