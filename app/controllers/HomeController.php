<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Plan.php';

class HomeController extends Controller
{
    public function index(): void
    {
        global $conn;

        $planModel = new Plan($conn);
        $plans = $planModel->getPlansWithFeatures();

        $this->view('home/index', [
            'title' => 'WeatherNotify – Automated Weather Alerts',
            'plans' => $plans
        ]);
    }

    public function features(): void
    {
        $this->view('home/features', [
            'title' => 'Features | WeatherNotify'
        ]);
    }
}
