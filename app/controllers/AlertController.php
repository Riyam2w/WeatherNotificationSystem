<?php
declare(strict_types=1);

require_once __DIR__ . '/../../classes/Validator.php';
require_once __DIR__ . '/../core/ApiValidator.php';
require_once __DIR__ . '/../models/Plan.php';

class AlertController
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function store(): void
    {
        Auth::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            return;
        }

        /* -------------------------
           Parse JSON or Form Data
        ------------------------- */
        $input = $_POST;
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }

        $userId    = (int) $_SESSION['user_id'];
        $cityName  = trim($input['city_name'] ?? '');
        $lat       = $input['lat'] ?? null;
        $lon       = $input['lon'] ?? null;
        $condition = trim($input['condition'] ?? '');
        $operator  = trim($input['operator'] ?? '');
        $threshold = $input['threshold'] ?? null;
        $alertName = trim($input['alert_name'] ?? '');

        /* -------------------------
           Plan Enforcement
        ------------------------- */
        if (!Plan::canCreateAlert($this->conn, $userId)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Alert limit reached. Please upgrade your plan.'
            ]);
            return;
        }

        /* -------------------------
           Validation
        ------------------------- */
        $v = new ApiValidator();

        $v->require('city_name', $cityName)
          ->require('condition', $condition)
          ->require('operator', $operator);

        if (!in_array($operator, ['>', '<', '='], true)) {
            $v->errors()['operator'] = 'Invalid operator.';
        }

        if ($threshold !== null && !is_numeric($threshold)) {
            $v->errors()['threshold'] = 'Threshold must be numeric.';
        }

        if (!Validator::range((float)$lat, -90, 90)) {
            $v->errors()['lat'] = 'Invalid latitude.';
        }

        if (!Validator::range((float)$lon, -180, 180)) {
            $v->errors()['lon'] = 'Invalid longitude.';
        }

        if ($v->fails()) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $v->errors()
            ]);
            return;
        }

        if ($alertName === '') {
            $alertName = "{$cityName} - {$condition} {$operator} {$threshold}";
        }

        /* -------------------------
           Database Transaction
        ------------------------- */
        $this->conn->begin_transaction();

        try {
            // City
            $stmt = $this->conn->prepare(
                "SELECT id FROM cities WHERE name = ? LIMIT 1"
            );
            $stmt->bind_param("s", $cityName);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $cityId = (int) $res->fetch_assoc()['id'];
            } else {
                $stmt = $this->conn->prepare(
                    "INSERT INTO cities (name, lat, lon) VALUES (?, ?, ?)"
                );
                $lat = (float)$lat;
                $lon = (float)$lon;
                $stmt->bind_param("sdd", $cityName, $lat, $lon);
                $stmt->execute();
                $cityId = $stmt->insert_id;
            }

            // Condition
            $stmt = $this->conn->prepare(
                "SELECT id FROM alert_conditions WHERE code = ? LIMIT 1"
            );
            $stmt->bind_param("s", $condition);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows === 0) {
                throw new RuntimeException('Invalid alert condition');
            }

            $conditionId = (int) $res->fetch_assoc()['id'];

            // Alert
            $stmt = $this->conn->prepare(
                "INSERT INTO alerts
                (user_id, alert_name, city_id, condition_id, operator, threshold_value)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $threshold = $threshold !== null ? (float)$threshold : 0.0;

            $stmt->bind_param(
                "isiisd",
                $userId,
                $alertName,
                $cityId,
                $conditionId,
                $operator,
                $threshold
            );

            $stmt->execute();
            $this->conn->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Alert created successfully'
            ]);

        } catch (Throwable $e) {
            $this->conn->rollback();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create alert'
            ]);
        }
    }
}
