<?php
declare(strict_types=1);

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

    public function store(): void
    {
        $this->ensurePost();

        $input = $_POST ?: json_decode(file_get_contents('php://input'), true) ?? [];

        $userId        = (int)($_SESSION['user_id'] ?? 0);
        $cityName      = trim($input['city_name'] ?? '');
        $lat           = $input['lat'] ?? null;
        $lon           = $input['lon'] ?? null;
        $conditionType = trim($input['condition_type'] ?? '');
        $operator      = trim($input['operator'] ?? '');
        $threshold     = $input['threshold'] ?? null;
        $unit          = trim($input['unit'] ?? '');

        /* ---------------- Plan check ---------------- */
        if (!Plan::canCreateAlert($this->conn, $userId)) {
            $this->json([
                'success' => false,
                'errors'  => ['plan' => 'Alert limit reached. Please upgrade your plan.']
            ], 403);
        }

        /* ---------------- Validation ---------------- */
        $v = new ApiValidator();
        $v->require('city_name', $cityName)
          ->require('condition_type', $conditionType)
          ->require('operator', $operator)
          ->require('unit', $unit);

        if (!in_array($operator, ['>', '<'], true)) {
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
        }

        /* ---------------- DB Transaction ---------------- */
        $this->conn->begin_transaction();

        try {
            /* Resolve city_id */
            $stmt = $this->conn->prepare(
                "SELECT id FROM cities WHERE name=? AND lat=? AND lon=? LIMIT 1"
            );
            $stmt->bind_param('sdd', $cityName, $lat, $lon);
            $stmt->execute();

            if ($row = $stmt->get_result()->fetch_assoc()) {
                $cityId = (int)$row['id'];
            } else {
                $stmt = $this->conn->prepare(
                    "INSERT INTO cities (name, lat, lon) VALUES (?, ?, ?)"
                );
                $stmt->bind_param('sdd', $cityName, $lat, $lon);
                $stmt->execute();
                $cityId = $stmt->insert_id;
            }

            /* Insert alert */
            $stmt = $this->conn->prepare(
                "INSERT INTO alerts
                 (user_id, city_id, condition_type, operator, threshold_value, unit, status)
                 VALUES (?, ?, ?, ?, ?, ?, 'active')"
            );

            $thresholdF = (float)$threshold;

            $stmt->bind_param(
                'iissds',
                $userId,
                $cityId,
                $conditionType,
                $operator,
                $thresholdF,
                $unit
            );

            $stmt->execute();
            $this->conn->commit();

            $this->json([
                'success' => true,
                'message' => 'Alert created successfully'
            ]);

        } catch (Throwable $e) {
            $this->conn->rollback();

            $this->json([
                'success' => false,
                'errors'  => ['server' => 'Failed to create alert']
            ], 500);
        }
    }
}
