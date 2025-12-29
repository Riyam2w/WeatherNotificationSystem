<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Plan.php';

class PricingController extends Controller
{
    public function index(): void
    {
        global $conn;

        $planModel = new Plan($conn);
        $plans = $planModel->getPlansWithFeatures();

        $this->view('pricing/index', [
            'title' => 'Pricing – WeatherGuard',
            'plans' => $plans
        ]);
    }
}
