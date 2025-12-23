<?php
declare(strict_types=1);

class HomeController
{
    public function index(): void
    {
        // If logged in → dashboard
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        // Data for homepage (later from DB)
        $features = [
            [
                'title' => 'Instant SMS Alerts',
                'desc'  => 'Receive notifications within seconds of severe warnings.'
            ],
            [
                'title' => 'Hyper-Local Radar',
                'desc'  => 'Exact street-level weather precision.'
            ],
            [
                'title' => '24/7 Monitoring',
                'desc'  => 'Automated systems never sleep.'
            ],
        ];

        require __DIR__ . '/../views/home.php';
    }
}
