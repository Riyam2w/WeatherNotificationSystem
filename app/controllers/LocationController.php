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

    /**
     * Store/Fetch a city from API
     */
    public function store(): void
    {
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        // Get raw POST data for JSON body or standard form data
        // Assuming standard form data based on curl example plan
        $cityName = $_POST['city_name'] ?? '';

        if (empty($cityName)) {
            // Support JSON body as fallback
            $input = json_decode(file_get_contents('php://input'), true);
            $cityName = $input['city_name'] ?? '';
        }

        if (empty($cityName)) {
            $this->json(['success' => false, 'error' => 'City name is required'], 422);
            return;
        }

        require_once __DIR__ . '/../services/CityService.php';
        
        try {
            $service = new CityService();
            $cityData = $service->fetchCityFromApi($cityName);

            if (!$cityData) {
                $this->json(['success' => false, 'error' => 'City not found on external API'], 404);
                return;
            }

            $cityId = $service->saveCity($cityData);

            // Record user association with city if logged in
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if ($userId > 0) {
                $db = new Database();
                $stmt = $db->conn->prepare("INSERT IGNORE INTO user_cities (user_id, city_id) VALUES (?, ?)");
                $stmt->bind_param('ii', $userId, $cityId);
                $stmt->execute();
            }

            $this->json([
                'success' => true,
                'message' => 'City fetched and saved successfully',
                'data' => [
                    'id' => $cityId,
                    'name' => $cityData['name'],
                    'country' => $cityData['country'],
                    'lat' => $cityData['lat'],
                    'lon' => $cityData['lon']
                ]
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
