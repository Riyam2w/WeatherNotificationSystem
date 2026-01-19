<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';
require_once dirname(__DIR__, 2) . '/classes/Validator.php';
require_once __DIR__ . '/../core/ApiValidator.php';
require_once __DIR__ . '/../models/Plan.php';

class AlertController extends Controller
{
    private mysqli $conn;
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
    public function index(): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(403); 
            echo "Unauthorized";
            return;
        }
        $sql = "SELECT a.*, c.name as city_name, ac.label as condition_label 
                FROM alerts a
                JOIN cities c ON a.city_id = c.id
                JOIN alert_conditions ac ON a.condition_id = ac.id
                WHERE a.user_id = ?
                ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $alerts = [];
        while ($row = $result->fetch_assoc()) {
            $alerts[] = $row;
        }
        $this->view('dashboard/pages/alerts', ['alerts' => $alerts]);
    }
    public function store(): void
    {
        $this->ensurePost();      
        $input = $_POST ?: json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json([
                'success' => false,
                'errors'  => ['auth' => 'Unauthorized']
            ], 401);
            return;
        }   
        $cityName      = trim($input['city_name'] ?? '');
        $lat           = $input['lat'] ?? null;
        $lon           = $input['lon'] ?? null;
        $conditionId   = (int)($input['condition_id'] ?? '0');
        $operator      = trim($input['operator'] ?? '');
        $threshold     = $input['threshold'] ?? null;
        $unit          = trim($input['unit'] ?? '');
        /* ---------------- Plan check ---------------- */
        require_once __DIR__ . '/../models/Subscription.php';
        $subModel = new Subscription($this->conn);
        $features = $subModel->getFeaturesForUser($userId);
        /* ---------------- Check max_alerts ---------------- */
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM alerts WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $alertCount = $stmt->get_result()->fetch_assoc()['total'];
        if (isset($features['max_alerts']) && $alertCount >= $features['max_alerts']) {
            $this->json([
                'success' => false,
                'message' => "You've reached the maximum alert limit for the Free plan ({$features['max_alerts']} alerts). Please upgrade your plan to create more alerts!",
                'errors'  => ['plan' => 'Max alerts reached']
            ], 403);
            return;
        }
        /* ---------------- Validation ---------------- */
        $v = new ApiValidator();
        $v->require('city_name', $cityName)
          ->require('condition_id', $conditionId)
          ->require('operator', $operator)
          ->require('unit', $unit);

        if (!in_array($operator, ['>', '<', '>=', '<=', '='], true)) {
            $v->addError('operator', 'Invalid operator.');
        }
        if ($threshold === null || !is_numeric($threshold)) {
            $v->addError('threshold', 'Threshold must be numeric.');
        }
        if ($lat === null || !Validator::range((float)$lat, -90, 90)) {
            $v->addError('lat', 'Invalid latitude.');
        }
        if ($lon === null || !Validator::range((float)$lon, -180, 180)) {
            $v->addError('lon', 'Invalid longitude.');
        }
        if ($v->fails()) {
            $this->json([
                'success' => false,
                'errors'  => $v->errors()
            ], 422);
            return;
        }
        $lat = round((float)$lat, 6);
        $lon = round((float)$lon, 6);   
        $threshold = (float)$threshold;
        /* ---------------- DB Transaction ---------------- */
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare(
                "SELECT id FROM cities WHERE name=? AND lat=? AND lon=? LIMIT 1"
            );
            $stmt->bind_param('sdd', $cityName, $lat, $lon);
            $stmt->execute();
            if ($row = $stmt->get_result()->fetch_assoc()) {
                $cityId = (int)$row['id'];
            } else {
                $stmt = $this->conn->prepare(
                    "INSERT INTO cities (name, lat, lon, country, country_code) VALUES (?, ?, ?, ?, ?)"
                );
                $defaultCountry = 'Unknown';
                $defaultCode = 'XX';
                $stmt->bind_param('sddss', $cityName, $lat, $lon, $defaultCountry, $defaultCode);
                $stmt->execute();
                $cityId = $stmt->insert_id;
            }
            if (isset($features['cities_limit']) && $features['cities_limit'] !== 'unlimited') {
                $stmt = $this->conn->prepare("SELECT 1 FROM alerts WHERE user_id = ? AND city_id = ? LIMIT 1");
                $stmt->bind_param("ii", $userId, $cityId);
                $stmt->execute();
                $isMonitoringCity = $stmt->get_result()->fetch_assoc();
                if (!$isMonitoringCity) {
                    $stmt = $this->conn->prepare("SELECT COUNT(DISTINCT city_id) as total FROM alerts WHERE user_id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $cityCount = $stmt->get_result()->fetch_assoc()['total'];

                    if ($cityCount >= (int)$features['cities_limit']) {
                        $this->conn->rollback();
                        $this->json([
                            'success' => false,
                            'message' => "The Free plan only allows monitoring " . ($features['cities_limit'] == 1 ? "a single city" : "up to {$features['cities_limit']} cities") . ". Upgrade to a paid plan for unlimited locations!",
                            'errors'  => ['plan' => 'City limit reached']
                        ], 403);
                        return;
                    }
                }
            }

            $stmt = $this->conn->prepare(
                "SELECT id FROM alert_conditions WHERE id=? LIMIT 1"
            );
            $stmt->bind_param('i', $conditionId);
            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) {
                throw new RuntimeException('Invalid condition ID.');
            }
            $conditionId = (int)$row['id']; 
            /* Insert alert */
            $stmt = $this->conn->prepare(
                "INSERT INTO alerts
               (user_id, city_id, condition_id, `operator`, threshold_value, unit, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())"
            );

            $stmt->bind_param(
                'iiisds',
                $userId,
                $cityId,
                $conditionId,
                $operator,
                $threshold,
                $unit
            );

            $stmt->execute();

            /* Record user association with city */
            $stmt = $this->conn->prepare("INSERT IGNORE INTO user_cities (user_id, city_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $userId, $cityId);
            $stmt->execute();

            $this->conn->commit();

            $this->json([
                'success' => true,
                'message' => 'Alert created successfully'
            ], 201);

        } catch (Throwable $e) {
            $this->conn->rollback();

            $this->json([
                'success' => false,
                'errors'  => ['server' => $e->getMessage()]
            ], 500);
        }
    }


    public function delete($id): void
    {
        $id = (int)$id;
        $this->ensurePost(); // or allow DELETE method? usually forms use POST with methodOverride but here we might use AJAX.
        // Let's assume standard POST for now or handle DELETE method check
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        require_once __DIR__ . '/../models/Alert.php';
        $alertModel = new Alert($this->conn);
        
        if ($alertModel->delete($id, $userId)) {
             $this->json(['success' => true, 'message' => 'Alert deleted successfully']);
        } else {
             $this->json(['success' => false, 'error' => 'Failed to delete alert'], 500);
        }
    }
}
