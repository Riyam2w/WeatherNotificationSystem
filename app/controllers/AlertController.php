<?php
declare(strict_types=1);

require_once __DIR__ . '/../../classes/Validator.php';
require_once __DIR__ . '/../core/ApiValidator.php';

class AlertController
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* -------------------------------------------------
       CREATE ALERT (API)
    ------------------------------------------------- */
    public function store(): void
    {
        Auth::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error'   => 'Method not allowed'
            ]);
            return;
        }

        $userId    = (int) $_SESSION['user_id'];
        $cityName  = trim($_POST['city_name'] ?? '');
        $lat       = $_POST['lat'] ?? null;
        $lon       = $_POST['lon'] ?? null;
        $condition = trim($_POST['condition'] ?? '');
        $operator  = trim($_POST['operator'] ?? '');
        $threshold = $_POST['threshold'] ?? null;
        $alertName = trim($_POST['alert_name'] ?? '');

        /* -------------------------
           API Validation Layer
        ------------------------- */
        $v = new ApiValidator();

        $v->require('city_name', $cityName)
          ->require('condition', $condition)
          ->require('operator', $operator)
          ->range('threshold', is_numeric($threshold) ? (float)$threshold : null, -100, 100);

        if (!in_array($operator, ['>', '<'], true)) {
            $v->errors()['operator'] = 'Invalid operator.';
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
            /* 1. Get or Insert City */
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

            /* 2. Condition ID */
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

            /* 3. Insert Alert */
            $stmt = $this->conn->prepare(
                "INSERT INTO alerts
                (user_id, alert_name, city_id, condition_id, operator, threshold_value)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $threshold = (float)$threshold;

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
                'error'   => 'Failed to create alert'
            ]);
        }
    }
}
