<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Payment.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    private mysqli $conn;
    private array $config;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        $this->config = require __DIR__ . '/../config/app_config.php';

        // Defensive configuration validation
        if (
            empty($this->config['stripe']) ||
            empty($this->config['stripe']['secret_key']) ||
            empty($this->config['stripe']['publishable_key'])
        ) {
            throw new RuntimeException('Stripe configuration missing or invalid');
        }
    }

    /* ---------------- Checkout Page ---------------- */
    public function checkout(string $slug): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            header('Location: /login');
            exit;
        }

        $planModel = new Plan($this->conn);
        $selectedPlan = null;

        foreach ($planModel->getAllActive() as $plan) {
            if ($plan['slug'] === $slug) {
                $selectedPlan = $plan;
                break;
            }
        }

        if (!$selectedPlan) {
            header('Location: /pricing');
            exit;
        }

        $cycle = $_GET['cycle'] ?? 'monthly';

        $this->view('payment/checkout', [
            'title' => 'Checkout – WeatherGuard',
            'plan'  => $selectedPlan,
            'cycle' => $cycle,
            'stripe_publishable_key' => $this->config['stripe']['publishable_key']
        ], 'payment');
    }

    /* ---------------- Create Stripe Session ---------------- */
    public function process(): void
    {
        $this->ensurePost();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $planId = (int)($_POST['plan_id'] ?? 0);
        $cycle  = $_POST['billing_cycle'] ?? 'monthly';

        $planModel = new Plan($this->conn);
        $plan = null;

        foreach ($planModel->getAllActive() as $p) {
            if ((int)$p['id'] === $planId) {
                $plan = $p;
                break;
            }
        }

        if (!$plan) {
            $this->json(['success' => false, 'error' => 'Invalid plan'], 400);
            return;
        }

        $amount = ($cycle === 'yearly')
            ? (float)$plan['yearly_price']
            : (float)$plan['monthly_price'];

        Stripe::setApiKey($this->config['stripe']['secret_key']);

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https'
            : 'http';

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => $this->config['stripe']['currency'] ?? 'inr',
                        'product_data' => [
                            'name' => 'WeatherGuard ' . $plan['name'] . ' Plan',
                        ],
                        'unit_amount' => (int) round($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'client_reference_id' => (string)$userId,
                'success_url' => $scheme . '://' . $_SERVER['HTTP_HOST']
                    . '/payment/success?session_id={CHECKOUT_SESSION_ID}&plan_id='
                    . $planId . '&cycle=' . $cycle,
                'cancel_url' => $scheme . '://' . $_SERVER['HTTP_HOST'] . '/pricing',
            ]);

            $this->json([
                'success' => true,
                'id' => $session->id,
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Stripe error: ' . $e->getMessage()
            ], 500);
        }
    }

    /* ---------------- Payment Success ---------------- */
    public function success(): void
    {
        $userId    = (int)($_SESSION['user_id'] ?? 0);
        $sessionId = $_GET['session_id'] ?? '';
        $planId    = (int)($_GET['plan_id'] ?? 0);
        $cycle     = $_GET['cycle'] ?? 'monthly';

        if (!$userId || !$sessionId || !$planId) {
            header('Location: /dashboard');
            exit;
        }

        Stripe::setApiKey($this->config['stripe']['secret_key']);

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                throw new RuntimeException('Payment not verified');
            }

            // Prevent duplicate processing
            $stmt = $this->conn->prepare(
                "SELECT id FROM payments WHERE transaction_id = ?"
            );
            $stmt->bind_param("s", $sessionId);
            $stmt->execute();

            if ($stmt->get_result()->fetch_assoc()) {
                header('Location: /dashboard');
                exit;
            }

            // Fetch plan
            $planModel = new Plan($this->conn);
            $plan = null;
            foreach ($planModel->getAllActive() as $p) {
                if ((int)$p['id'] === $planId) {
                    $plan = $p;
                    break;
                }
            }

            if (!$plan) {
                throw new RuntimeException('Plan not found');
            }

            $amount = ($cycle === 'yearly')
                ? (float)$plan['yearly_price']
                : (float)$plan['monthly_price'];

            $validFrom = date('Y-m-d');
            $validTill = ($cycle === 'yearly')
                ? date('Y-m-d', strtotime('+1 year'))
                : date('Y-m-d', strtotime('+1 month'));

            $paymentModel = new Payment($this->conn);
            $this->conn->begin_transaction();

            $paymentModel->createPayment([
                'user_id' => $userId,
                'plan_id' => $planId,
                'amount' => $amount,
                'payment_gateway' => 'Stripe',
                'transaction_id' => $sessionId,
                'status' => 'success',
                'valid_from' => $validFrom,
                'valid_till' => $validTill,
                'billing_cycle' => $cycle,
            ]);

            $paymentModel->createSubscription([
                'user_id' => $userId,
                'plan_id' => $planId,
                'city' => 'Primary',
                'valid_from' => $validFrom,
                'valid_till' => $validTill,
                'billing_cycle' => $cycle,
            ]);

            $paymentModel->updateUserPlan($userId, $planId);
            $this->conn->commit();

            $_SESSION['flash_message'] =
                'Welcome to ' . $plan['name'] . '! Your plan is now active.';

            header('Location: /dashboard');

        } catch (Exception $e) {
            if ($this->conn->errno) {
                $this->conn->rollback();
            }
            die('Payment verification failed: ' . $e->getMessage());
        }
    }
}
