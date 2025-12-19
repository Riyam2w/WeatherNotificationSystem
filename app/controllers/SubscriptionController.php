<?php 
declare(strict_types=1);

require_once __DIR__ .'/../models/Subscription.php';

class SubscriptionController {
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
    public function store(): void {
        header('Content-Type: application/json');

        $email = trim($_POST['email'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $type = $_POST['condition_type'] ?? '';
        $operator = $_POST['condition_operator'] ?? '';
        $value = $_POST['condition_value'] ?? '';

        if ($email === '') {
        echo json_encode([
        'success' => false,
        'message' => 'Email is required.'
        ]);
           exit;
        }

        if (!preg_match('/^[a-zA-Z0-9]+@gmail\.com$/', $email)) {
        echo json_encode([
        'success' => false,
        'message' => 'Only valid Gmail addresses are allowed.'
        ]);
           exit;
        }

        if ($city === '' || !is_numeric($value)) {
        echo json_encode([
        'success' => false,
        'message' => 'Invalid city.'
       ]);
       exit;
        }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if($row = $res->fetch_assoc()) {
            $userId = (int)$row['id'];
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO users (email, created_at) VALUES (?, NOW())"
            );
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $userId = $stmt->insert_id;      
          }

          $subscription = new Subscription($this->conn);

          $saved = $subscription->create([
            'user_id' => $userId,
            'city' => $city,
            'condition_type' => $type,
            'condition_operator' => $operator,
            'condition_value' => (float)$value
            
          ]);

          echo json_encode([
            'success' => $saved,
            'message' => $saved ? 'Alert activated successfully' : 'Failed to save'
          ]);

          exit;
    }
}

?>